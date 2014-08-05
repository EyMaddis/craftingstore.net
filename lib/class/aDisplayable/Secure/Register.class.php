<?php
defined('_MCSHOP') or die("Security block!");
class Register extends aDisplayable{
	public function prepareDisplay(){
		$_SESSION['Index']->assign_say('REGISTER_TITLE');
		if(isset($_GET['notCompleted']))
			$this->notCompleted();
		elseif(isset($_GET['resendMail']))
			$this->resendMail();
		elseif($_GET['u'] || $_GET['c'])
			$this->verifyCode();
		else
			$this->registrationProcess();
	}


	private function notCompleted(){
		$_SESSION['Index']->assign_say('REGISTER_NOT_COMPLETED');
		$_SESSION['Index']->assign_say('REGISTER_NOT_COMPLETED_TO_RESEND');
		$_SESSION['Index']->assign('REGISTER_NOT_COMPLETED_TO_RESEND_URL','?show=Register&resendMail&shop='.$_GET['shop']);
	}
	private function resendMail(){
		if (isset($_POST['recaptcha_response_field']) && !Captcha::isValid()){
			$_SESSION['Index']->assign_say('REGISTER_CAPTCHA_INVALID');
			$error = 1;
		}
		if(!$error && isset($_POST['mail'])){
			$UserInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Id, Nickname, Email, Password, Validated FROM mc_gamer WHERE Email='".mysql_real_escape_string($_POST['mail'])."' LIMIT 1");
			if(!$UserInfo){
				$_SESSION['Index']->assign_say('RESEND_MAIL_MAIL_WRONG');
			}
			elseif($UserInfo->Validated){
				$_SESSION['Index']->assign_say('VERIFICATION_ALREADY_DONE');
				$_SESSION['Index']->assign_say('VERIFICATION_CONFIRMED_TO_LOGIN');
				$_SESSION['Index']->assign_direct('SHOP_ID', $_GET['shop']);
				return;
			}
			else{
				#region Passwort ist ungültig
				if(!bcrypt_check($UserInfo->Email, $_POST['pw'], $UserInfo->Password)){
					$_SESSION['Index']->assign_say('RESEND_MAIL_PW_WRONG');
				}
				#end
				#region Email als Bestätigung senden
				else{
					$token = random_string_by_length(15);
					$_SESSION['Index']->db->update("UPDATE mc_gamer SET token='".mysql_real_escape_string($token)."' WHERE Id='{$UserInfo->Id}' LIMIT 1");
					$validationKey = Register::generateValidationKey($UserInfo, $token);
					if(isNumber($_GET['shop']) && ($_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_shops WHERE Id='{$_GET['shop']}'"))){
						Register::sendValidationMail($UserInfo,$validationKey,$_GET['shop']);
					}
					else{
						Register::sendValidationMail($UserInfo,$validationKey);
					}
					
					$_SESSION['Index']->assign_say('REGISTER_RESEND_DONE');
					$_SESSION['Index']->assign_say('RESEND_MAIL_BACK');
					$_SESSION['Index']->assign('RESEND_MAIL_BACK_URL','?show=Login&shop='.$_GET['shop']);
					return;
				}
				#end
			}
		}

		$_SESSION['Index']->assign_say('REGISTER_PASSWORD');
		$_SESSION['Index']->assign_say('REGISTER_CAPTCHA');
		$_SESSION['Index']->assign_say('REGISTER_RESEND');
		$_SESSION['Index']->assign('REGISTER_RESEND_URL','?show=Register&resendMail&shop='.$_GET['shop']);

		$_SESSION['Index']->assign_say('RESEND_MAIL');
		$_SESSION['Index']->assign_say('RESEND_MAIL_DO');
		$_SESSION['Index']->assign_say('RESEND_MAIL_CANCEL');
		$_SESSION['Index']->assign('RESEND_MAIL_CANCEL_URL','?show=Login&shop='.$_GET['shop']);
	}
	private function verifyCode(){
		if(!isNumber($_GET['u'])){
			$_SESSION['Index']->assign_say('VERIFICATION_ERROR','VERIFY_ERROR_INVALID_DATA');
			$_SESSION['Index']->assign_say('VERIFICATION_ERROR_TO_RESEND');
		}
		else{
			$UserInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Id,Password,Email,Nickname,Token,Validated FROM mc_gamer WHERE Id='{$_GET['u']}' LIMIT 1");
			if($UserInfo->Validated){
				$_SESSION['Index']->assign_say('VERIFICATION_ALREADY_DONE');
				$_SESSION['Index']->assign_say('VERIFICATION_CONFIRMED_TO_LOGIN');
				$_SESSION['Index']->assign_direct('SHOP_ID', $_GET['shop']);
			}
			elseif($_GET['c'] == Register::generateValidationKey($UserInfo)){
				//Token löschen und Spieler als Validated kennzeichnen
				$_SESSION['Index']->db->update("UPDATE mc_gamer SET Validated='1',Token='' WHERE Id='{$UserInfo->Id}' LIMIT 1");
				//Dem User ein leeres Konto erstellen, damit GetCurrentBalance(...) auch bei einem neu angelegten Spieler einen Kontostand zurückgegeben kann
				$_SESSION['Index']->db->insert("INSERT INTO mc_gameraccounts (Time,GamerId,Action) VALUES ('".time()."','{$UserInfo->Id}','DEFAULT')");

				//Wenn die Shop-Id vorhanden und gültig ist, den User für den Shop freischalten
				if(User::addPermittedShop($_GET['shop'], $UserInfo->Id)){
					$_SESSION['Index']->assign_direct('SHOP_ID', $_GET['shop']);
				}
				$_SESSION['Index']->assign_say('VERIFICATION_CONFIRMED');
				$_SESSION['Index']->assign_say('VERIFICATION_CONFIRMED_TO_LOGIN');
			}
			else{
				$_SESSION['Index']->assign_say('VERIFICATION_ERROR','VERIFY_ERROR_INVALID_DATA');
				$_SESSION['Index']->assign_say('VERIFICATION_ERROR_TO_RESEND');
			}
		}
	}
	private static function cleanOldUsers(){
		$time = time()-172800;
		$_SESSION['Index']->db->query("DELETE FROM mc_permittedshops WHERE GamerId IN (
		SELECT Id FROM mc_gamer WHERE RegTime<'$time' AND Validated='0')");
		$_SESSION['Index']->db->query("DELETE FROM mc_gamer WHERE RegTime<'$time' AND Validated='0'");
	}
	private function registrationProcess(){
		#region Neuen Benutzer anlegen/Registrierung
		if(count($_POST)){
			$nickname = $_POST['nickname'];
			$minecraftname = $_POST['minecraftname'];
			$mail_accept = $_POST['mail_accept'];
			$mail = $_POST['mail'];
			$mail2 = $_POST['mail2'];
			$pw = $_POST['pw'];
			$pw2 = $_POST['pw2'];
			if($_POST['mail'] != $_POST['mail2']){ //Mails ungleich
				$error = 1;
				$_SESSION['Index']->assign_say('REGISTER_MAIL_ERROR_2', 'REGISTER_MAILS_NOT_EQUAL');
			}
			if($_POST['pw'] != $_POST['pw2']){ //Passwörter ungleich
				$error = 1;
				$_SESSION['Index']->assign_say('REGISTER_PWS_NOT_EQUAL');
			}
			if (isset($_POST['recaptcha_response_field']) && !Captcha::isValid()){
				$_SESSION['Index']->assign_say('REGISTER_CAPTCHA_INVALID');
				$error = 1;
			}
			#region Versuchen, den User zu erstellen
			if(!$error){ //nur versuchen, wenn bisher kein Fehler auftrat
				//alte User bereinigen
				Register::cleanOldUsers();
				$newUserId = User::tryCreateUser($nickname, $mail, $mail_accept, $pw, $minecraftname, $Error);

				#region Bei erfolgreicher Registrierung entsprechende Ausgabe
				if($newUserId){
					#region Den User zum Shop hinzufügen, falls dieser gültig ist
					$UserInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Id, Nickname, Email, Password FROM mc_gamer WHERE Id='$newUserId' LIMIT 1");
					$token = random_string_by_length(15);
					$_SESSION['Index']->db->update("UPDATE mc_gamer SET token='".mysql_real_escape_string($token)."' WHERE Id='{$UserInfo->Id}' LIMIT 1");
					$validationKey = Register::generateValidationKey($UserInfo, $token);
					if(isNumber($_GET['shop']) && ($_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_shops WHERE Id='{$_GET['shop']}'"))){
						Register::sendValidationMail($UserInfo,$validationKey,$_GET['shop']);
					}
					else{
						Register::sendValidationMail($UserInfo,$validationKey);
					}
					#end
					$_SESSION['Index']->assign_say('REGISTER_SUCCESSFUL_1');
					$_SESSION['Index']->assign_say('REGISTER_SUCCESSFUL_2',array(secondsToTimeString(User::$verificationTime)));
				}
				#end
				#region Ansonsten Fehlerausgabe
				else{
					#region Nickname überprüfen
					switch($Error['Nickname']){
						case 1:
							$_SESSION['Index']->assign_say('REGISTER_NAME_ERROR', 'REGISTER_NAME_INVALID');
							break;
						case 2:
							$_SESSION['Index']->assign_say('REGISTER_NAME_ERROR', 'REGISTER_NAME_IN_USE');
							break;
						default:
					}
					#end
					#region MinecraftName überprüfen
					if($Error['MinecraftName']){
							$_SESSION['Index']->assign_say('REGISTER_MINECRAFTNAME_ERROR', 'REGISTER_MINECRAFTNAME_INVALID');
					}
					#end
					#region E-Mail überprüfen
					switch($Error['Email']){
						case 1:
								$_SESSION['Index']->assign_say('REGISTER_MAIL_ERROR_1', 'REGISTER_MAIL_INVALID');
								$_SESSION['Index']->assign_direct('REGISTER_MAIL_ERROR_ACCEPT',1);
							break;
						case 2:
								$_SESSION['Index']->assign_say('REGISTER_MAIL_ERROR_1', 'REGISTER_MAIL_IN_USE');
							break;
						default:
					}
					#end
					#region Passwort überprüfen
					if($Error['Password']){
						$_SESSION['Index']->assign_say('REGISTER_PW_INVALID');
					}
					#end
				}
				#end
			}
			#end
		}
		#end
		#region Ausgabe für die Registrierung
		if(!$newUserId){
			$_SESSION['Index']->assign('REGISTER_NICKNAME_VALUE', $nickname);
			$_SESSION['Index']->assign('REGISTER_MINECRAFTNAME_VALUE', $minecraftname);
			$_SESSION['Index']->assign('REGISTER_MAIL_VALUE', $mail);
			$_SESSION['Index']->assign('REGISTER_MAIL_REPEAT_VALUE', $mail2);
			$_SESSION['Index']->assign_say('REGISTER_NICKNAME');
			$_SESSION['Index']->assign_say('REGISTER_MINECRAFTNAME');
			$_SESSION['Index']->assign_say('REGISTER_MAIL');
			$_SESSION['Index']->assign_say('REGISTER_MAIL_REPEAT');
			$_SESSION['Index']->assign_say('REGISTER_PASSWORD');
			$_SESSION['Index']->assign_say('REGISTER_PASSWORD_REPEAT');
			$_SESSION['Index']->assign_say('REGISTER_CANCEL');
			$_SESSION['Index']->assign_say('REGISTER_DO');
			$_SESSION['Index']->assign_say('REGISTER_CAPTCHA');
			$_SESSION['Index']->assign_direct('SHOP_ID', $_GET['shop']);
		}
		#end
	}

	private static function generateValidationKey($UserInfo, $token = null){
		return sha1($UserInfo->Id.$UserInfo->Password.$UserInfo->Email.($token == null?$UserInfo->Token:$token).$UserInfo->Nickname);
	}
	private static function sendValidationMail($UserInfo, $validationKey, $ShopId=0){
		return mail(
		//To (ist weiter unten im Header bereits angegeben)
			null,
		//Subject
			$_SESSION['Index']->say('REGISTER_MAIL_SUBJECT', array(BASE_DOMAIN), false),
		//Message
			$_SESSION['Index']->say('REGISTER_MAIL_MESSAGE', array($UserInfo->Nickname, SECURE_URL.'?show=Register'.($ShopId>0?'&amp;shop='.$ShopId:'').'&amp;u='.$UserInfo->Id.'&amp;c='.$validationKey), false),
		//Header
			"MIME-Version: 1.0\n"
			."To: \"{$UserInfo->Nickname}\" <{$UserInfo->Email}>\n"
			."From: \"".BASE_DOMAIN."\" <noreply@".BASE_DOMAIN.">\n"
			."Return-Path: noreply@".BASE_DOMAIN."\n"
			."Reply-To: noreply@".BASE_DOMAIN."\n"
			."Bcc: support@craftingstore.net\n"
			."Content-type: text/html; charset=utf-8\n"
		);
	}
}
?>