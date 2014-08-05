<?php

function logAndDie($result){
	$fp = fopen('./serverapi-log/'.date('Y-m-d').'.log','a+');
	fputs($fp, date('Y-m-d h:i:s').' '.$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REQUEST_URI']."\n	Result: ".$result."\n");
	fclose($fp);
	die($result);
}

error_reporting(1);

define("_MCSHOP", true);
require_once(dirname(__FILE__).'/config/config.include.php');
require_once(dirname(__FILE__).'/lib/lib.include.php');
require_once(dirname(__FILE__).'/lib/general.functions.php');


$subdomain = $_GET['subdomain'];
$mode = $_GET['mode'];
$username = $_GET['user'];
$points = $_GET['points'];

// every get parameter set?
if (!isset($subdomain)) logAndDie("ERROR: missing subdomain");
if (empty($_GET['key'])) logAndDie("ERROR: missing key");
if (empty($mode)) logAndDie("ERROR: missing mode");
if ($mode == 'addPointsToUser'){
	if(empty($username)) logAndDie("ERROR: missing user");
	if(!isNumber($points)) logAndDie("ERROR: invalid points");
}

$currentVersion = "0.4.2";
$currentVersionDownload = "http://craftingstore.net/download";

#region Datenbankverbindung aufbauen
if(!is_object($_SESSION['Index'])){
	$_SESSION['Index'] = new Index();
}

$db = MySqlDatabase::getInstance();

try{
	$db->connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
	$db->query("SET NAMES 'utf8'");
}
catch (Exception $e){
	logAndDie($e->getMessage());
}
#end

$shop = $db->fetchOneRow("SELECT Id,ServerPassword from mc_shops WHERE Subdomain='".mysql_real_escape_string($subdomain)."'");
if($shop == null) logAndDie("ERROR: unknown subdomain");

$keyString = "";
foreach($_GET as $key => $value){
	if($key != 'key')
		$keyString .= $value;
}
if($_GET['key'] != hash("sha256", $keyString.$shop->ServerPassword)){
	logAndDie("ERROR: invalid key");
}

switch($mode){
    case 'onlinePing': 
        onlinePing($shop->Id, $db);
        break;
    case 'offlinePing': 
        offlinePing($shop->Id, $db);
        break;
    case 'getVersion':
        getVersion($currentVersion, $currentVersionDownload);
		  break;
    case 'addPointsToUser':
        addPointsToUser($shop->Id, $username, $points, $db);
		  break;
    default:
        logAndDie("ERROR: unknown method");
}

function onlinePing($ShopId, $db) {
    $db->update("UPDATE mc_shops SET ServerOnline='".SERVER_MAX_TRANSFER_FAILURES."' WHERE Id='$ShopId' LIMIT 1");
	 logAndDie('true');
}
function offlinePing($ShopId, $db){
    $db->update("UPDATE mc_shops SET ServerOnline='0' WHERE Id='$ShopId' LIMIT 1");
	 logAndDie('true');
}
function getVersion($currentVersion, $currentVersionDownload){
    logAndDie($currentVersion.",".$currentVersionDownload);
}
function addPointsToUser($ShopId, $username, $addPoints, $db){
#echo "SELECT Id, (SELECT ShopId FROM mc_permittedshops WHERE Id=GamerId AND ShopId='$ShopId' LIMIT 1) AS ShopId FROM mc_gamer WHERE MinecraftName='".mysql_real_escape_string($username)."' LIMIT 1";
	$userInfo = $db->fetchOneRow("SELECT Id, (SELECT ShopId FROM mc_permittedshops WHERE Id=GamerId AND ShopId='$ShopId' LIMIT 1) AS ShopId FROM mc_gamer WHERE MinecraftName='".mysql_real_escape_string($username)."' LIMIT 1");
	if(!isNumber($userInfo->Id)) logAndDie("ERROR: invalid username");
	if(!isNumber($userInfo->ShopId)) logAndDie("ERROR: user is not validated in the shop");

	try{
		startTransaction("mc_permittedshops WRITE, mc_gameraccounts WRITE, mc_gameraccounts AS ga WRITE", $db);
		$db->query("UPDATE mc_permittedshops SET BonusPoints=BonusPoints+$addPoints WHERE GamerId='{$userInfo->Id}' AND ShopId='$ShopId'");
		$db->insert("INSERT INTO mc_gameraccounts (Time,GamerId,Current,Difference,BonusDifference,Action,ShopId) SELECT '".time()."','{$userInfo->Id}',ga.Current,'0','$addPoints','RECEIVED_BONUS_FROM_INGAME','$ShopId' FROM mc_gameraccounts AS ga WHERE ga.GamerId='{$userInfo->Id}' ORDER BY ga.time DESC LIMIT 1");
		commit($db);
		logAndDie("true");
	}
	catch(Exception $e){
		rollback($db);
		logAndDie("ERROR: ".$e);
	}
}

?>