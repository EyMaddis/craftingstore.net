<?php
define("_MCSHOP", true);
require_once(dirname(__FILE__).'/lib/lib.include.php');
if (is_array($_SESSION['CreateShop']) && $_GET['tmpconfig'] == $_SESSION['CreateShop']['tmpconfig']){
	error_reporting(0);
	$basefile = file_get_contents('./templates/Secure/baseConfigs/jsonapi-config.yml', true);
	$password = $_SESSION['CreateShop']["apipassword"];
	$salt = $_SESSION['CreateShop']["apisalt"];
	$servername = $_SESSION['CreateShop']["servername"];
	$username = $_SESSION['CreateShop']["apiuser"];
	
	$file = str_replace("{PASSWORD}", $password, $basefile);
	$file = str_replace("{SALT}", $salt, $file);
	$file = str_replace("{SERVERNAME}", $servername, $file);
	$file = str_replace("{USERNAME}", $username, $file);
	
	header("Cache-Control: no-cache private");
	header("Content-Disposition: attachment; filename=\"config.yml\"");
	header("Content-Type: application/force-download");
	header("Content-Length: " . strlen($file));
	echo $file;
}
elseif(!isset($_GET['tmpconfig'])) {
	header("Location: http://".BASE_DOMAIN);
}
else {
	echo "Could not generate a config!<br /> It might took too long, try to go back and generate the config once more.";
	echo "<br>";
	//var_dump($_SESSION);
}
?>
