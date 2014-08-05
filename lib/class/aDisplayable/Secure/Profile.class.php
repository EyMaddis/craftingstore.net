<?php
/************************
**
** File:	 	Profile.class.php
**	Author: 	Rasmus Epha
**	Date:		08.01.2012
**	Desc:		Mit diesem Modul kann der Nutzer seine persönlichen Daten einsehen
**
*************************/
defined('_MCSHOP') or die("Security block!");


class Profile extends aDisplayable
{
	public function prepareDisplay()
	{
#region Wenn der User nicht eingeloggt/validiert ist, zum Login weiterleiten
if(!$_SESSION['Index']->user->isValidated() || !$_SESSION['Index']->user->isLoggedIn())
	setLocation('?show=Login&shop='.$_GET['shop']);
#end
#region Profil Base-Url festlegen (entweder mit ShopId oder ohne)
if(isNumber($_GET['shop'])){
	$shop = $_GET['shop'];
	$_SESSION['Index']->assign('PROFILE_BASE_URL','?show=Profile&shop='.$shop);
}
else{
	$_SESSION['Index']->assign('PROFILE_BASE_URL','?show=Profile');
}
#end

$userInfo = $_SESSION['Index']->db->fetchOneRow("SELECT MinecraftName,Email,Nickname,NewEmail FROM mc_gamer WHERE Id='{$_SESSION['Index']->user->getLoginId()}' LIMIT 1");
$default = true;
#region Änderung der E-Mail-Adresse abbrechen
if(isset($_GET['editmail_cancel']))
	$_SESSION['Index']->db->query("UPDATE mc_gamer SET NewEmail=null WHERE Id='{$_SESSION['Index']->user->getLoginId()}' LIMIT 1");
#end
#region E-Mail ändern
if(isset($_GET['editmail'])){
	$newMail = $_SESSION['Index']->db->fetchOne("SELECT NewEmail FROM mc_gamer WHERE Id='{$_SESSION['Index']->user->getLoginId()}' LIMIT 1");

	if($_GET['k'] && $newMail){
		$default = false;
		$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_SAVE');
		$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_COMMIT');
		$_SESSION['Index']->assign_say('PROFILE_PASSWORD');
		$_SESSION['Index']->assign('PROFILE_EDITMAIL_KEY',$_GET['k']);

		if(isset($_POST['password'])){
			$changeResult = $_SESSION['Index']->user->checkChangeMailValidation($_POST['password'],$_GET['k']);
			if($changeResult === -1){
				setLocation('?show=Profile&shop='.$_GET['shop']);
			}
			elseif($changeResult === -2){
				$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_ERROR','PROFILE_PASSWORD_WRONG');
			}
			elseif($changeResult === -3){
				$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_ERROR','PROFILE_EDITMAIL_KEY_WRONG');
			}
			else{
				$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_DONE');
			}
		}
	}
	elseif(!$newMail){
		$default = false;
		$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_SAVE');
		$_SESSION['Index']->assign_say('PROFILE_EDITMAIL');
		$_SESSION['Index']->assign_say('PROFILE_MAIL');
		$_SESSION['Index']->assign_say('PROFILE_MAIL_REPEAT');

		$mail = $_POST['mail'];
		$escapedmail = mysql_real_escape_string($mail);

		//E-Mail-Adressen identisch?
		if($_POST['mail'] != $_POST['mailrep']){
			$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_ERROR','PROFILE_EDITMAIL_NOT_EQUAL');
		}
		//Mail-Adresse nicht anders als die alte
		elseif($_POST['mail'] == $userInfo->Email){
			$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_ERROR','PROFILE_EDITMAIL_CURRENT');
		}
		//E-Mail-Adresse gültig
		elseif($_POST['mail'] != $_POST['lastmail'] && !is_email($mail)){
			$_SESSION['Index']->assign('PROFILE_LASTMAIL', $_POST['mail']);
			$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_ERROR','PROFILE_EDITMAIL_INVALID');
		}
		//E-Mail-Adresse bereits verwendet
		elseif($_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_gamer WHERE Id<>'{$_SESSION['Index']->user->getLoginId()}' AND (Email='$escapedmail' OR NewEmail='$escapedmail') LIMIT 1")){
			$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_ERROR','PROFILE_EDITMAIL_IN_USE');
		}
		//jetzt können wir Hand anlegen
		elseif($mail){
			$_SESSION['Index']->db->query("UPDATE mc_gamer SET NewEmail='$escapedmail' WHERE Id='{$_SESSION['Index']->user->getLoginId()}'");
			$_SESSION['Index']->user->sendChangeMailValidation($mail,$shop);
			$_SESSION['Index']->assign_say('PROFILE_EDITMAIL_SEND');
		}
		//else: nur normale Ausgabe
	}
	else{
		$default = false;
		$cancelChangeEmail = 'editmail';
	}
}
#end
#region User aus einem Shop löschen
elseif(isNumber($_GET['deleteshop'])){
	$default = false;
	$ShopLabel = $_SESSION['Index']->db->fetchOne("SELECT s.Label FROM mc_permittedshops AS p INNER JOIN mc_shops AS s ON p.ShopId=s.Id WHERE p.GamerId='{$_SESSION['Index']->user->getLoginId()}' AND p.ShopId='{$_GET['deleteshop']}'");
	if($ShopLabel){
		$_SESSION['Index']->assign_direct('PROFILE_DO_DELETESHOP',1);
		if(isset($_GET['commit'])){
			if($_SESSION['Index']->db->delete("DELETE FROM mc_permittedshops WHERE GamerId='{$_SESSION['Index']->user->getLoginId()}' AND ShopId='{$_GET['deleteshop']}' LIMIT 1"))
				$_SESSION['Index']->assign_say('PROFILE_DELETESHOP_CONFIRMATION');
			else
				$_SESSION['Index']->assign_say('PROFILE_DELETESHOP_CONFIRMATION_ERROR');
		}
		else{
			$_SESSION['Index']->assign('PROFILE_DELETESHOP_ID',$_GET['deleteshop']);
			$_SESSION['Index']->assign_say('PROFILE_DELETESHOP_LABEL',array($ShopLabel));
			$_SESSION['Index']->assign_say('PROFILE_DELETESHOP_CONFIRM');
			$_SESSION['Index']->assign_say('PROFILE_DELETESHOP_CANCEL');
		}
	}
}
#end
#region Passwort soll geändert werden
elseif(isset($_GET['editpw'])){
	#region E-Mail-Validierung abbrechen
	if($userInfo->NewEmail){
		$default = false;
		$cancelChangeEmail = 'editpw';
	}
	#end
	#region Passwort abfragen
	if($default){
		if($_POST){
			if($_POST['newpw'] != $_POST['pwconfirm']){
				$_SESSION['Index']->assign_say('PROFILE_EDIT_PW_ERROR','PROFILE_PASSWORDS_NOT_EQUAL');
			}
			elseif(!User::validPassword($_POST['newpw'])){
				$_SESSION['Index']->assign_say('PROFILE_EDIT_PW_ERROR','PROFILE_INVALID_PASSWORD');
			}
			elseif(!User::isPassword($_SESSION['Index']->user->getLoginId(), $_POST['oldpw'])){
				$_SESSION['Index']->assign_say('PROFILE_EDIT_PW_ERROR','PROFILE_PASSWORD_WRONG');
			}
			else{
				$ChangeError = User::changePassword($_SESSION['Index']->user->getLoginId(), $_POST['oldpw'], $_POST['newpw']);
				$_SESSION['Index']->assign_direct('PROFILE_CHANGE_PW_DONE', true);
				$_SESSION['Index']->assign_say('PROFILE_PASSWORD_CHANGED');
				$EditPwDone = true;
			}
		}

		if(!$EditPwDone){
			$_SESSION['Index']->assign_direct('PROFILE_CHANGE_PW',true);
			$_SESSION['Index']->assign_say('PROFILE_CURRENT_PASSWORD');
			$_SESSION['Index']->assign_say('PROFILE_NEW_PASSWORD');
			$_SESSION['Index']->assign_say('PROFILE_NEW_PASSWORD_CONFIRM');
			$_SESSION['Index']->assign_say('PROFILE_EDIT_SAVE');
			$_SESSION['Index']->assign_say('PROFILE_CURRENT_PASSWORD');
		}
	}
	#end
}
#end

if($cancelChangeEmail){
	if($cancelChangeEmail == 'editpw')
		$_SESSION['Index']->assign_say('PROFILE_CANCEL_CHANGE_EMAIL','PROFILE_CHANGE_PW_NOT_POSSIBLE');
	elseif($cancelChangeEmail == 'editmail')
		$_SESSION['Index']->assign_say('PROFILE_CANCEL_CHANGE_EMAIL','PROFILE_CHANGE_MAIL_NOT_POSSIBLE');
	$_SESSION['Index']->assign_say('PROFILE_CANCEL_EMAIL_EDIT');
	$_SESSION['Index']->assign_say('PROFILE_CONTINUE_EMAIL_EDIT');
	$_SESSION['Index']->assign('PROFILE_CANCEL_CHANGE_MAIL_TARGET',$cancelChangeEmail);
}

if($default){
	if($shop){
		$_SESSION['Index']->assign('PROFILE_BACK_URL', "?shop=$shop&red");
		$_SESSION['Index']->assign_say('PROFILE_BACK_TO_SHOP');
		$_SESSION['Index']->assign('PROFILE_BUYPOINTS_URL',"?show=Buypoints&shop=$shop&fromProfile");
	}else{
		$_SESSION['Index']->assign('PROFILE_LOGOFF_URL', '?show=Login&logoff='.$_SESSION['Index']->user->getLoginId());
		$_SESSION['Index']->assign_say('PROFILE_LOGOFF');
		$_SESSION['Index']->assign('PROFILE_BUYPOINTS_URL','?show=Buypoints');
	}

	$_SESSION['Index']->assign_say('PROFILE_PASSWORD');
	$_SESSION['Index']->assign_say('PROFILE_EDIT_PASSWORD');
	$_SESSION['Index']->assign_say('PROFILE_EDIT_PASSWORD_LINK');
	$_SESSION['Index']->assign_say('PROFILE_CHANGE_LINK');
	$_SESSION['Index']->assign_say('PROFILE_NICKNAME');
	$_SESSION['Index']->assign_say('PROFILE_EDIT');
	$_SESSION['Index']->assign_say('PROFILE_MAIL');
	$_SESSION['Index']->assign_say('PROFILE_MAIL_CHANGE');
	$_SESSION['Index']->assign_say('PROFILE_MINECRAFTNAME');
	$_SESSION['Index']->assign_say('PROFILE_CURRENT');
	$_SESSION['Index']->assign_say('PROFILE_CURRENT_DESCRIPTION',array(User::getNormalPoints($_SESSION['Index']->user->getLoginId())));
	$_SESSION['Index']->assign_say('PROFILE_INCREASE_ACCOUNT');
	$_SESSION['Index']->assign_say('PROFILE_INCREASE_ACCOUNT_DESCRIPTION');

	$_SESSION['Index']->assign_say('PROFILE_ACTIVATED_SHOPS');
	$shops = null;
	foreach($_SESSION['Index']->db->iterate("
SELECT s.Id,s.Label,s.Subdomain,s.Domain
FROM mc_permittedshops AS p
LEFT JOIN mc_shops AS s ON s.Id=p.ShopId
WHERE p.GamerId='{$_SESSION['Index']->user->getLoginId()}'") as $row)
	{
		$shops[] = array('Id' => $row->Id, 'Label' => $row->Label, 'Url' => ($row->Domain ? $row->Domain : $row->Subdomain.'.'.BASE_DOMAIN));
	}
	if(!$shops){
		$_SESSION['Index']->assign_say('PROFILE_NO_ACTIVATED_SHOPS');
		$_SESSION['Index']->assign_say('PROFILE_NO_ACTIVATED_SHOPS_MORE_INFO');
	}
	else
		$_SESSION['Index']->assign('PROFILE_ACTIVATED_SHOPS_VALUE',$shops);
	$_SESSION['Index']->assign('PROFILE_MAIL_VALUE',$userInfo->Email);
	$_SESSION['Index']->assign('PROFILE_NEW_MAIL_VALUE',$userInfo->NewEmail);
	$_SESSION['Index']->assign('PROFILE_NICKNAME_VALUE', $userInfo->Nickname);
	$_SESSION['Index']->assign('PROFILE_MINECRAFTNAME_VALUE',$userInfo->MinecraftName);
}


$_SESSION['Index']->assign_say('PROFILE_BACK');
$_SESSION['Index']->assign_say('PROFILE_TITLE');
	}
}

?>