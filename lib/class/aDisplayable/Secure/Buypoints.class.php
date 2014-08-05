<?php

/************************
**
** 	File:	 	Buypoints.class.php
**	Author: 	Malte Peers
**	Date:		06/03/2012
**	Desc:		class to connect to Paypal and handle IPN-Requests
**
*************************/

defined('_MCSHOP') or die("Security block!");

class Buypoints extends aDisplayable
{
	public function prepareDisplay(){
#region Wenn der User nicht eingeloggt/validiert ist, zum Login weiterleiten
if(!$_SESSION['Index']->user->isValidated() || !$_SESSION['Index']->user->isLoggedIn())
	setLocation('?show=Login&shop='.$_GET['shop']);
#end
		if($_GET['kaesekuchen'] != "Würstchen")
		{
			setLocation('/beta-restrictions', BASE_DOMAIN, false);
		}


		#region Paketauswahl anzeigen
		$seller=PAYPAL_SELLEREMAIL; // our Paypal-Account
		$notifyURL=PAYPAL_RETURN_IPN; // IPN Listener


		$packages = array();
		$comma = $_SESSION['Index']->say('COMMA');
		foreach($_SESSION['Index']->db->iterate("SELECT Money, Name, img FROM mc_moneypackages ORDER BY Money ASC LIMIT 5") as $row){
			$packages[] = array('id' => count($packages),
				'money' => $row->Money,
				'money_display' => $row->Money.$comma.'-',
				'price' => $row->Money*POINTS_PER_EURO,
				'name' => htmlspecialchars($row->Name),
				'image' => $row->img);
		}
		$_SESSION['Index']->assign('BUYPOINTS_PACKAGES', $packages);
		$_SESSION['Index']->assign('BUYPOINTS_SELLER', $seller);
		$ShopId = (isNumber($_GET['shop']) ? $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_shops WHERE Id='{$_GET['shop']}'") : 0);
		if(isset($_GET['fromProfile'])){
			if($ShopId)
				$_SESSION['Index']->assign('BUYPOINTS_RETURNURL', '?show=Profile&shop='.$ShopId);
			else
				$_SESSION['Index']->assign('BUYPOINTS_RETURNURL', '?show=Profile');
		}
		else{
			if($ShopId)
				$_SESSION['Index']->assign('BUYPOINTS_RETURNURL', '?red&shop='.$ShopId);
			else
				$_SESSION['Index']->assign('BUYPOINTS_RETURNURL', '?show=Profile');
		}
		$_SESSION['Index']->assign('BUYPOINTS_NOTIFYURL', $notifyURL);
		$_SESSION['Index']->assign('BUYPOINTS_FORM_ACTION', PAYPAL_FORM_ACTION);
		$_SESSION['Index']->assign('GAMER_ID', $_SESSION['Index']->user->getLoginID());
		$_SESSION['Index']->assign_say('BUYPOINTS_INFO');
		$_SESSION['Index']->assign_say('BUYPOINTS_TITLE');
		$_SESSION['Index']->assign_say('BACK');
		$_SESSION['Index']->assign_say('CURRENCY_SYMBOL');
		$_SESSION['Index']->assign_say('SECURE_BUYPOINTS_TITLE');
		$_SESSION['Index']->assign_say('BUYPOINTS_FORM_SUBMIT');
		$_SESSION['Index']->assign_say('BUYPOINTS_FORM_SUBMIT_WAIT');
	}
}

?>