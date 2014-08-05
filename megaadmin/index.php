<?php
define("_MCSHOP", true);

$menu = array(
	0,
	array('Start','start.php'),
	array('Konto','kontoauszuege.php'),
	array('Auszahlungen','beantragteAuszahlungen.php'),
	array('Shop-<br />verwaltung','shopVerwaltung.php'),
	array('Kunden-<br />verwaltung','kundenVerwaltung.php'),
	array('Spieler-<br />verwaltung','spielerVerwaltung.php'),
	array('Beta','betakeys.php'),
	array('Übersetzung','translator.php')
);


require_once('../config/config.include.php');
require_once('../lib/general.functions.php');

require_once($dir.'../lib/class/mysqldatabase.class.php');
require_once($dir.'../lib/class/mysqlresultset.class.php');

session_start();


$db = MySqlDatabase::getInstance();
if(!$db->isConnected())
{
	try
	{
		$db->connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
		$db->query("SET NAMES 'utf8'");
	}
	catch (Exception $e)
	{
		die($e->getMessage());
	}
}

if(!$_SESSION['LoginDone'])
{   // changed for github
	if(sha1($_POST['user']) == 'XXXXXXXXXXXXXXXXXXXXXXXXX' && sha1($_POST['pw']) == 'XXXXXXXXXXXXXXXXXXXXXXXXX')
	{
		$_SESSION['LoginDone'] = 1;
		header("Location: /megaadmin/?p={$_GET['p']}");
		die();
		$output = -2;
	}
	else
	{
		$output = -1;
	}
}
else
{
	define("_LOGIN", true);
	if(isNumber($_GET['p']))
	{
		$output = $_GET['p'];
	}
	else
	{
		$output = 1;
	}
}

echo '<?xml version="1.0" encoding="utf-8"?>';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
	<meta http-equiv="cache-control" content="no-cache" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<title>Mega-Admin Menü</title>
	<style type="text/css">
	body > div{
		margin: 0 auto;
		width: 80%;
	}
	table.menu
	{
		margin: 10px auto 20px;
		background-color:#ddd;
		border-spacing: 10px;
		border-radius: 11px;
	}
	table.menu a {
		color: #000;
		text-decoration: none;
	}
	table.menu a:hover {
		text-decoration: underline;
	}
	table.menu td {
		border-radius: 7px;
		width: 100px;
		height: 80px;
		text-align: center;
		vertical-align: middle;
		background-color: #fff;
		padding: 5px;
		border: 2px solid #999;
	}
	table.menu td.active {
		border-color: #fff;
		background-color: #aaa;
	}

	table.lang{
		border-spacing:0;
	}
	table.lang tr:nth-child(2n) > td{
		border-top: 1px solid #000;
	}
	table.lang td{
		padding:4px 4px;
	}
	table.lang tr:nth-child(2n) > *{
		background-color:#eee;
	}
	table.lang tr:nth-child(2n+1) > *{
		background-color:#ddd;
	}
	table.lang tr.empty:nth-child(2n) > *{
		background-color:#FF5A5A;
	}
	table.lang tr.empty:nth-child(2n+1) > *{
		background-color:#FF7878;
	}
	table.lang input,
	table.lang textarea
	{
		border:1px solid #ccc;
		margin:3;
		padding:2;
		width:100%;
	}
	table.lang input:focus,
	table.lang textarea
	{
		border:1px solid #666;
	}
	</style>
</head>
<body>
<div>
<?php
if($output >= 0)
{
	//Menü anzeigen
	echo '<table class="menu"><tr>';
	foreach($menu as $key => $value)
	{
		if(is_array($value))
		echo '<td'.($key==$output?' class="active"':'').'><a href="?p='.$key.'">'.$value[0].'</a></td>';
	}
	echo '</tr></table>';
}

switch($output)
{
	case -1: //Login ist erforderlich
?>
<form action="" method="post">
	Benutzername: <input type="text" name="user" value="" /><br />
	Passwort: <input type="password" name="pw" value="" /><br />
	<input type="submit" value="einloggen" />
</form>
<?php
		break;
	case -2: //Login war erfolgreich, Link zum weiterleiten
		echo <<<html
Du wurdest erfolgreich eingeloggt.<br />
<a href="/megaadmin/?p={$_GET['p']}">weiter</a>
html;
		break;
	default:
		require_once($menu[$output][1]);
		break;
}
?>
</div>
</body>
</html><?php
#$db->disconnect();??
?>
