<?php
header('Location: http://craftingstore.net/it-was-a-pleasure-craftingstore-is-shutting-down/');
die();
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));

try{
/************************
**
** File:	 	index.php
**	Author: 	Mathis Neumann & Rasmus Epha
**	Date:		06/05/2012
**	Desc:		index for shopsystem
**
*************************/
#region Fehlerbehandlung
if(isset($_GET['error']))
switch($_GET['error']){
	case 404:
		$host = ($_SERVER['HTTPS'] ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'];
		#header('Location: '.$host.'/404');
		/*echo <<<html
Error 404 - Die Angeforderte Seite existiert nicht.<br />
<a href="$host">weiter</a>
html;*/
		die();
		break;
	default:
		break;
}
#end

#region php-Fehlerausgabe
#error_reporting(E_ALL);
#ini_set("display_errors", 0);
#end

#$start = microtime();

define("_MCSHOP", true);
require_once(dirname(__FILE__).'/lib/lib.include.php');

#region DDoS
define('E429','<html><body><h1>HTTP 429 - <a href="http://httpstatusdogs.com/429-too-many-requests" target="_blank">Too Many Requests</a></h1>Please wait ten seconds.</html>');
if($_SESSION['blocked'] > time())
{
	#$_SESSION['blocked'] = time() + 10;
	header("HTTP/1.1 429 Too Many Requests");
	die(E429);
}
elseif($_SESSION['blocked'])
{
	$_SESSION['blocked'] = null;
	$_SESSION['lastAccess'] = null;
}

$_SESSION['lastAccess'][] = time();
if(count($_SESSION['lastAccess']) > 15)
{
	//array_shift($_SESSION['lastAccess']);
	if(array_shift($_SESSION['lastAccess']) + 10 > time()){
		//*
		$_SESSION['blocked'] = time() + 10;
		header("HTTP/1.1 429 Too Many Requests");
		die(E429);
		/*/
		usleep(5*1000000);//Hier noch einen exponentiellen Faktor einbauen, sodass bei mehrfacher überschreitung zwischendrin länger gewartet werden muss
		//*/
	}
}
#end

$tStart = microtime(1);
if(!is_object($_SESSION['Index'])){
	$_SESSION['Index'] = new Index();
}

$_SESSION['Index']->connectDatabase();

$_SESSION['Index']->CheckVersion();


#region User hat sich eingeloggt, es wird ein Token übergeben
if($_GET['tok'] && isNumber($_GET['uid'])){
	if($_GET['tok'] == $_SESSION['Index']->db->fetchOne("SELECT Token FROM mc_gamer WHERE Id='{$_GET['uid']}' LIMIT 1")){
		$_SESSION['Index']->db->query("UPDATE mc_gamer SET Token='' WHERE Id='{$_GET['uid']}' LIMIT 1");

		$subdomain = getCurrentSubdomain(); 
		if($subdomain == 'secure'){
			if($_SESSION['Index']->db->fetchOne("SELECT Validated FROM mc_gamer WHERE Id='{$_GET['uid']}' LIMIT 1")){
				$_SESSION['Index']->user = new User($_GET['uid']);
			}
		}
		else{
			$subdomain = mysql_real_escape_string($subdomain);
			$domain = mysql_real_escape_string($_SERVER['HTTP_HOST']);

			//Prüft, ob der Spieler diesen Shop betreten darf
			if($_SESSION['Index']->db->fetchOne("SELECT g.validated FROM mc_gamer AS g
INNER JOIN mc_permittedshops AS p ON g.Id=p.GamerId
INNER JOIN mc_shops AS s ON s.Id = p.ShopId
where g.Id='{$_GET['uid']}' AND (Domain='$domain' OR Subdomain='$subdomain') LIMIT 1")){
				$_SESSION['Index']->user = new User($_GET['uid']);
			}
		}
	}
	$params = '';
	foreach($_GET as $key => $value){
		if($key != 'tok' && $key != 'uid'){
			if($params){
				$params .= '&'.$key.'='.$value;
			}
			else{
				$params = '?'.$key.'='.$value;
			}
		}
	}
	setLocation($params, null, ($subdomain == 'secure'));
}
#end
#region Redirect durchführen
if(isset($_GET['red'])){
	$shop = $_GET['shop'];
	$useSSL = $shop == 'secure';
	$red = $_GET['red'];

	if($_SESSION['Index']->user->isLoggedIn()){
		$tok = $_SESSION['Index']->user->createToken();
		$uid = $_SESSION['Index']->user->getLoginId();

		if(isNumber($shop) && ($domain = $_SESSION['Index']->db->fetchOneRow("SELECT Subdomain,Domain FROM mc_shops WHERE Id='$shop' LIMIT 1"))){
			setLocation(($red ? urldecode($red)."&tok=$tok&uid=$uid" : "?tok=$tok&uid=$uid"), ($domain->Domain ? $domain->Domain : $domain->Subdomain.'.'.BASE_DOMAIN), $useSSL);
		}
		elseif($shop){
			setLocation(($red ? urldecode($red)."&tok=$tok&uid=$uid" : "?tok=$tok&uid=$uid"), $shop.'.'.BASE_DOMAIN, $useSSL);
		}
	}
	if(isNumber($shop) && ($domain = $_SESSION['Index']->db->fetchOneRow("SELECT Subdomain,Domain FROM mc_shops WHERE Id='$shop' LIMIT 1"))){
		setLocation('',($domain->Domain ? $domain->Domain : $domain->Subdomain.'.'.BASE_DOMAIN), $useSSL);
	}
	elseif($_GET['shop']){
		setLocation($red, $shop.'.'.BASE_DOMAIN, $useSSL);
	}
}
#end

#region PIWIK TRACKING
// API:  http://piwik.org/docs/tracking-api/
if(!$_SESSION['Piwik']){
	$_SESSION['Piwik'] = new PiwikTracker($idSite = 1);
	# You can manually set the visitor details (resolution, time, plugins, etc.) 
	# See all other ->set* functions available in the PiwikTracker.php file
	$_SESSION['Piwik']->setTokenAuth("7f46822d9ea839d9dfc7a90345ecf575"); // minecraftshop.net USER
	$_SESSION['Piwik']->setResolution(1600, 1400); //fixed
}
$_SESSION['Piwik']->setIp($_SERVER['REMOTE_ADDR']);
$_SESSION['Piwik']->setUserAgent($_SERVER['HTTP_USER_AGENT']);
$_SESSION['Piwik']->setUrlReferrer($_SERVER['HTTP_REFERER']);
# Sends Tracker request via http

# You can also track Goal conversions
$_SESSION['Piwik']->setCustomVariable(1, "subdomain", getCurrentSubdomain($_SERVER['HTTP_HOST']), "visit");
$_SESSION['Piwik']->setCustomVariable(2, "loggedin", $_SESSION['Index']->user->getName(), "visit");

$_SESSION['Piwik']->doTrackPageView($_GET['show']); // ?
#end

if (is_object($_SESSION['Index']->shop)){
	$_SESSION['Index']->shop->updateShopInfo(); // reload cache
}

$_SESSION['Index']->Display($_GET['show']);

doSleep($tStart, 1);
}
catch(Exception $e){
	echo $e;
}
?>