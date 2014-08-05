<?php
/************************
**
** File:	 	lib.include.php
**	Author: 	Mathis Neumann, Rasmus Epha
**	Date:		2011/02/09
**	Desc:		include of required libraries
**
*************************/

defined('_MCSHOP') or die("Security block!");
//define('SMARTY_DIR', dirname (__FILE__).'/smarty/');


#region all require_once
$dir = dirname(__FILE__).'/';

if($_SERVER['HTTPS'])
{
	$protocol = "https";
}
else
{
	$protocol = "http";
}


#region alle Klassen im Klassenverzeichnis einbinden
require_once($dir.'../config/config.include.php');
define('MAIN_URL', $protocol."://".DOMAIN.'/');
require_once($dir.'general.functions.php');


#region Piwik Tracking API init
require_once ($dir."class/PiwikTracker.php");
PiwikTracker::$URL = 'http://stats.'.BASE_DOMAIN.'/';
#endregion

require_once($dir.'class/Index.class.php');


require_once($dir.'class/aDisplayable/aDisplayable.class.php');
require_once($dir.'class/aDisplayable/aContentBox.class.php');
require_once($dir.'class/BBCode.class.php');

$sub = getCurrentSubdomain();


#region Normaler Shop-Aufruf
if($sub != 'secure')
{
	require_once($dir.'class/aDisplayable/Account.class.php');
	require_once($dir.'class/aDisplayable/Content.class.php');
	require_once($dir.'class/aDisplayable/Cart.class.php');
	require_once($dir.'class/aDisplayable/Footer.class.php');
	require_once($dir.'class/aDisplayable/Header.class.php');
#require_once($dir.'class/aDisplayable/Inventory.class.php');
	require_once($dir.'class/aDisplayable/Itembox.class.php');
	require_once($dir.'class/aDisplayable/Itemdetails.class.php');
	require_once($dir.'class/aDisplayable/ItemSearch.class.php');
	require_once($dir.'class/aDisplayable/LMenu.class.php');
	require_once($dir.'class/aDisplayable/Main.class.php');
	require_once($dir.'class/aDisplayable/PendingTransfers.class.php');
	require_once($dir.'class/aDisplayable/Right.class.php');
	require_once($dir.'class/aDisplayable/Textbox.class.php');
	require_once($dir.'class/aDisplayable/Toppanel.class.php');
	require_once($dir.'class/aDisplayable/TransferHistory.class.php');
}
#endregion
#region Login/Admin-Bereich
if($sub == 'secure')
{
	require_once($dir.'class/aDisplayable/Secure/AgbFooter.class.php');
#Normaler User
	require_once($dir.'class/aDisplayable/Secure/Buypoints.class.php');
	require_once($dir.'class/aDisplayable/Secure/Captcha.class.php');
	require_once($dir.'class/aDisplayable/Secure/ForgotPassword.class.php');
	require_once($dir.'class/aDisplayable/Secure/ForgotPasswordServer.class.php');
	require_once($dir.'class/aDisplayable/Secure/LangList.class.php');
	require_once($dir.'class/aDisplayable/Secure/Login.class.php');
	require_once($dir.'class/aDisplayable/Secure/Message.class.php');
	require_once($dir.'class/aDisplayable/Secure/Profile.class.php');
	require_once($dir.'class/aDisplayable/Secure/Register.class.php');
	require_once($dir.'class/aDisplayable/Secure/Structure.class.php');

#Admin User
	require_once($dir.'class/aDisplayable/Secure/Account.class.php');
	require_once($dir.'class/aDisplayable/Secure/Admin.class.php');
	require_once($dir.'class/aDisplayable/Secure/Content.class.php');	
	require_once($dir.'class/aDisplayable/Secure/CreateShop.class.php');	
	require_once($dir.'class/aDisplayable/Secure/EmailPlayer.class.php');
	require_once($dir.'class/aDisplayable/Secure/ExclusiveShopPoints.class.php');
	require_once($dir.'class/aDisplayable/Secure/GamerStats.class.php');
	require_once($dir.'class/aDisplayable/Secure/ItemGroups.class.php');
	require_once($dir.'class/aDisplayable/Secure/ItemEdit.class.php');
	require_once($dir.'class/aDisplayable/Secure/ItemStats.class.php');
	require_once($dir.'class/aDisplayable/Secure/LatestSales.class.php');
	require_once($dir.'class/aDisplayable/Secure/LoginServer.class.php');
	require_once($dir.'class/aDisplayable/Secure/Navigation.class.php');
	require_once($dir.'class/aDisplayable/Secure/Pagination.class.php');
	require_once($dir.'class/aDisplayable/Secure/ProductEdit.class.php');
	require_once($dir.'class/aDisplayable/Secure/Products.class.php');
	require_once($dir.'class/aDisplayable/Secure/ProfileServer.class.php');
	require_once($dir.'class/aDisplayable/Secure/RegisteredPlayers.class.php');
	require_once($dir.'class/aDisplayable/Secure/RegisterAdmin.class.php');
	require_once($dir.'class/aDisplayable/Secure/RequestPayout.class.php');
	require_once($dir.'class/aDisplayable/Secure/ServerSettings.class.php');	
	require_once($dir.'class/aDisplayable/Secure/ShopConfig.class.php');	
	require_once($dir.'class/aDisplayable/Secure/Statistics.class.php');
	require_once($dir.'class/aDisplayable/Secure/Structure.class.php');
	require_once($dir.'class/aDisplayable/Secure/Topmenu.class.php');
	require_once($dir.'class/aDisplayable/CustomCSS.class.php');

#allgemeine Klassen
	require_once($dir.'class/LoginWaitTimes.class.php');
	require_once($dir.'class/FileUpload.class.php');
}
#endregion

#region allgemeine Klassen
require_once($dir.'class/is_email.php');
require_once($dir.'class/DisplayControl.class.php');
require_once($dir.'class/Item.class.php');
require_once($dir.'class/Lang.class.php');
require_once($dir.'class/mysqldatabase.class.php');
require_once($dir.'class/mysqlresultset.class.php');
require_once($dir.'class/Shop.class.php');
require_once($dir.'class/User.class.php');
#endregion

require_once($dir.'class/Smarty/Smarty.class.php');
require_once($dir.'class/NestedSet.class.php');
require_once($dir.'jsonapi/JSONquery.php');

#endregion

#endregion
#region starting session and output

ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
// session_set_cookie_params (0, '/', '.'.BASE_DOMAIN);

session_start();
// session_regenerate_id(true);
#region Protection against session fixation: change the session id after a fixed time
if (!isset($_SESSION['CREATED'])) {
	$_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > SESSION_ID_TIMEOUT) {
	// session started more than 30 minates ago
	$_SESSION['CREATED'] = time();  // update creation time
}
#end
#region Protection against session fixation: check if the ip is always the same for one session
if(!isset($_SESSION['CREATION_IP']))
	$_SESSION['CREATION_IP'] = $_SERVER['REMOTE_ADDR'];
#end
#region Session Timeout
if ((isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT))# Session Timeout
|| ($_SESSION['CREATION_IP'] != $_SERVER['REMOTE_ADDR'])) #Session fixation ip check
{
	session_destroy(); // destroy session data in storage
	session_unset(); // unset $_SESSION variable for the runtime
	session_start();
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
#endregion
	
header("Content-Type: text/html; charset=utf-8");
#endregion

/*
echo 'gc_probability: '.ini_get('session.gc_probability').'<br>';
echo 'gc_divisor: '.ini_get('session.gc_divisor').'<br>';
echo 'gc_maxlifetime: '.ini_get('session.gc_maxlifetime').'<br>';
echo 'save_path: '.ini_get('session.save_path').'<br>';
*/

#region Gesperrte Domains
$_SESSION['ALLOWED_SUBDOMAINS'] = array('secure');
$_SESSION['BLOCKED_SUBDOMAINS'] = array(
	'42',
	'admin',
	'blog',
	'board',
	'bugs',
	'chat',
	'demo',
	'dev',
	'dick',
	'docs',
	'forum',
	'garfield',
	'git',
	'help',
	'hitler',
	'info',
	'login',
	'mail',
	'malte',
	'mathis',
	'minecraft',
	'minecraftshop',
	'my',
	'nazi',
	'noreply',
	'penis',
	'rasmus',
	'register',
	'secure',
	'server',
	'shop',
	'shops',
	'smtp',
	'stats',
	'status',
	'support',
	'support',
	'vagina',
	'wiki',
	'wwww','www','ww','w',
	'yerrak'
);
#endregion
?>