<?php

/**
 * cronjob.php
 *
 * Rasmus Epha, 2013-02-24
 *
 * Überträgt Produkte aus der Warteschlange an die Server, die noch nicht übertragen wurden
 */


function logInfo($str){
	echo date('Y-m-d H:i:s').' '.$str."\n";
}
function writeLog($buffer){
	$f = fopen(__DIR__.'/cron-'.date('Y-m-d').'.log', 'a');
	fputs($f, $buffer);
	fclose($f);
}

error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
//error_reporting(E_ALL);

define("_MCSHOP", true);
require_once(dirname(__FILE__).'/../lib/lib.include.php');

#region db connecten
$_SESSION['Index']->db = MySqlDatabase::getInstance();

if(!$_SESSION['Index']->db->isConnected())
{
	try
	{
		$_SESSION['Index']->db->connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
		$_SESSION['Index']->db->query("SET NAMES 'utf8'");
	}
	catch (Exception $e)
	{
		die($e->getMessage());
	}
}
#end
$time = time();
if(count($_SERVER['argv']) > 1){
	ob_start("writeLog");

	if(!isNumber($_SERVER['argv'][1]))
		die();
	$ShopId = $_SERVER['argv'][1];
	$uuid = $_SERVER['argv'][2];

	logInfo("$uuid Starting with Shop $ShopId:");

	$server = new JSONquery($ShopId);
	$noPlayer = true;
	$playerlist = null;

	foreach($_SESSION['Index']->db->iterate("SELECT UserId FROM mc_transferCommands WHERE ShopId='$ShopId' AND Type='BY_CRON' AND IgnoreCommand<>1 AND Locked='0'
			AND ScheduledExecutionTime is not null
			AND (
				(ExecutionTime is null AND ScheduledExecutionTime<=$time)
				OR (ExecutionTime is not null AND ScheduledExecutionTime<=$time-getFailDelay(FailCount))
			) GROUP BY UserId") as $row){
		if($result = $server->processPendingProducts($row->UserId, $playerlist)){
			logInfo("User {$row->UserId}: $result");
			logInfo("$uuid Process cancelled");
			break;
		}
		else{
			logInfo("User {$row->UserId}: FINISHED");
		}
		$noPlayer = false;
	}
	if($noPlayer){
		logInfo("Nothing to do.");
	}
	$_SESSION['Index']->db->update("UPDATE mc_shops SET CronLock='0' WHERE Id='$ShopId' LIMIT 1");

	logInfo("Finished");
	ob_end_flush();
}
else{
	$uuid = $_SESSION['Index']->db->getUUID();
	writeLog("Starting Cronjob with UUID $uuid:\n");

	$anz = $_SESSION['Index']->db->update("UPDATE mc_shops AS s SET s.CronLock='$uuid' WHERE s.CronLock='0' AND s.ServerOnline='1'
		AND (SELECT t.UserId FROM mc_transferCommands AS t
			WHERE t.ShopId=s.Id AND t.Type='BY_CRON'
			AND t.IgnoreCommand<>1 AND Locked='0'

			AND ScheduledExecutionTime is not null
			AND (
				(ExecutionTime is null AND ScheduledExecutionTime<=$time)
				OR (ExecutionTime is not null AND ScheduledExecutionTime<=$time-getFailDelay(FailCount))
			)
			LIMIT 1) is not null");

	if($anz == 1){
		writeLog("$uuid-Found 1 Shop with pending transfers\n");
	}
	else{
		writeLog("$uuid-Found $anz Shops with pending transfers\n");
	}

	foreach($_SESSION['Index']->db->iterate("SELECT Id FROM mc_shops WHERE CronLock='$uuid'") as $row)
	{
		writeLog("$uuid-Starting Cronjob-Process for Shop {$row->Id}\n");
		shell_exec("nohup php ".__FILE__." {$row->Id} $uuid >> /dev/null &");
	}
}
?>