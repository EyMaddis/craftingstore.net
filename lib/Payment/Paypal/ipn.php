<?php

/************************
**
** 	File:	 	ipn.php
**	Author: 	Malte Peers, Rasmus Epha
**	Date:		12/28/2012
**	Desc:		the IPN Listener
**
*************************/
function clear_error($file){
	fclose(fopen ($file, 'w'));
}
$error_file = dirname(__FILE__).'/ipn_errors.log';
#clear_error($error_file);
// logging errors
ini_set('log_errors', true);
ini_set('error_log', $error_file);



define("_DEBUG", true);
define("_DEBUG_LOG", 'ipn.log');
function debug($text){
	if(_DEBUG){
		$fh = fopen(_DEBUG_LOG, 'a');
		fwrite($fh, date('Y-m-d H:i:s').': '.$text."\n");
		fclose($fh);
	}
}

ob_start('debug');
echo "Starting IPN-Request from {$_SERVER['SERVER_ADDR']}\n";
if($_SERVER['SERVER_ADDR'] != '178.63.74.19')
{
	echo "Ungültige Server-IP. Per Mail melden!\n";
	mail($failemail, 'Invalid IPN-Server', $_SERVER['SERVER_ADDR']);
	ob_flush();
	die();
}
ob_flush();

define("_MCSHOP", true);

require_once('../../lib.include.php');
ini_set('display_errors',1);
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));


if(!is_object($_SESSION['Index']))
{
echo "Creating new Index-Object";
	$_SESSION['Index'] = new Index();
ob_flush();
}


echo "Connection to Database: ";
$_SESSION['Index']->connectDatabase();
ob_flush();

echo "Creating Listener:";
// IPN ListenerFile from Paypal
include('ipnlistener.php');
$listener = new IpnListener();
ob_flush();

// we're playing in the sandbox
$listener->use_sandbox = true;


// a few variables from the global config
$sellerEmail = PAYPAL_SELLEREMAIL;
$failemail = PAYPAL_NOTICEEMAIL;


// try to process the IPN POST
echo "Post-Request:\n";
print_r($_POST);
ob_flush();
try
{
	echo "Processing Request...\n";
	$listener->requirePostMethod();
	$verified = $listener->processIpn();
}
catch(Exception $e)
{
	echo "Error processing the Request:\n".$e->getMessage();
	ob_flush();
	exit();
}
ob_flush();

if ($verified)
{
	//error_log(print_r($_POST, true));
	$error = false;

	/* 1. Make sure the payment status is "Completed"  ............................UNCOMMENT IF NO SANDBOX MODE
	if ($_POST['payment_status'] != 'Completed') {
		echo "Processing cancelled. payment_status is not Completed\n";
		// simply ignore any IPN that is not completed
		exit(0);
	}*/

	// seller-Email-Check
	if ($_POST['receiver_email'] != $sellerEmail)
	{
		echo "receiver_email stimmt nicht mit der geforderten überein: ".$_POST['receiver_email']."\n";
		$error = true;
	}

	// We want to get Euros
	if ($_POST['mc_currency'] != 'EUR')
	{
		echo "Die übergebene Währung stimmt nicht mit der geforderten überein: ".$_POST['mc_currency']."\n";
		$error = true;
	}

	echo "Lock Tables\n";
	$_SESSION['Index']->db->query("LOCK TABLE mc_orders WRITE");

	echo "Check dublikated Entry in Database with TxnId ".$_POST['txn_id']."\n";
	$TxnId = mysql_real_escape_string($_POST['txn_id']);
	$r = $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_orders WHERE TxnId='$TxnId'");

	if ($r > 0){
		echo "TxnId wurde bereits bearbeitet.\n";
		$error = true;
	}

	if ($error)
	{
		echo "Error-Mail wird versendet\n";
		$listenerError = $listener->getTextReport();
		mail($failemail, 'IPN Fraud Warning', "IPN failed fraud checks:\n\n".ob_get_contents()."\n\n".$listenerError);
	}
	else
	{
		// complete->add to database
		$payer_email = mysql_real_escape_string($_POST['payer_email']);
		$Amount = mysql_real_escape_string($_POST['mc_gross']);
		$PaypalTime = mysql_real_escape_string(strftime('%Y-%m-%d %H:%M:%S', strtotime($_POST['payment_date'])));
		$custom = explode(".", $_POST['custom']);
		$UserId = mysql_real_escape_string($custom[0]);


		$sql = "INSERT INTO mc_orders (TxnId,UserId,PaypalTime,Amount) VALUES ('$TxnId', '$UserId', '$PaypalTime', '$Amount')";

		$_SESSION['Index']->db->query($sql);

		$_SESSION['Index']->db->query("UNLOCK TABLES");


		// tell me about the successful buy
		//mail($failemail, 'VALID IPN', $listener->getTextReport());
		//Charge balance. If false, log!

		User::Inpayment($UserId, $Amount);

		$to = filter_var($_POST['payer_email'], FILTER_SANITIZE_EMAIL);
		$subject = "Einkauf Minecraftshop.net";
		mail(
			null,# =$to, steht aber im header drin. Sonst gäbe es zwei Mails
			"Es wurde gerade eingekauft...",
			"Spieler ... kaufte für $Amount € Punkte.",
			"MIME-Version: 1.0\n"
				."To: {$to} <{$to}>\n"
				."From: Minecraftshop.net <noreply@".BASE_DOMAIN.">\n"
				."Content-type: text/html; charset=utf-8\n");
	}

}
else
{
	// manually investigate the invalid IPN
	$listenerError = $listener->getTextReport();
	echo "Sending mail 'cause invalid IPN: $listenerError\n";
	mail($failemail, 'Invalid IPN', $listenerError);
}

ob_flush();
?>
