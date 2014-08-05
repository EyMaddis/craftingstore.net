<?php
defined('_MCSHOP') or die("Security block!");
class RegisterAdmin extends aDisplayable
{
	public static $wait_times = array(0, 0, 0, 5, 10, 15, 600);
	public static $verificationTime = 86400; //24 Stunden

	public function prepareDisplay(){
		$_SESSION['Index']->assign_say('REGISTER_SERVER_TITLE');

		if(isset($_GET['resendMail']))
			$this->resendMail();
		elseif($_GET['u'] || $_GET['c'])
			$this->verifyCode();
		else
			$this->registrationProcess();
	}

	private function resendMail(){
		if (isset($_POST['recaptcha_response_field']) && !Captcha::isValid()){
			$_SESSION['Index']->assign_say('REGISTER_CAPTCHA_INVALID');
			$error = 1;
		}
		if(!$error && isset($_POST['mail'])){
			$UserInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Id, FirstName, SurName, Email, Password, Validated FROM mc_customers WHERE Email='".mysql_real_escape_string($_POST['mail'])."' LIMIT 1");
			if(!$UserInfo){
				$_SESSION['Index']->assign_say('RESEND_MAIL_MAIL_WRONG');
			}
			elseif($UserInfo->Validated){
				$_SESSION['Index']->assign_say('VERIFICATION_ALREADY_DONE');
				$_SESSION['Index']->assign_say('VERIFICATION_CONFIRMED_TO_LOGIN');
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
					$_SESSION['Index']->db->update("UPDATE mc_customers SET token='".mysql_real_escape_string($token)."' WHERE Id='{$UserInfo->Id}' LIMIT 1");
					$validationKey = RegisterAdmin::generateValidationKey($UserInfo, $token);
					RegisterAdmin::sendValidationMail($UserInfo,$validationKey);

					$_SESSION['Index']->assign_say('REGISTER_RESEND_DONE');
					$_SESSION['Index']->assign_say('RESEND_MAIL_BACK');
					$_SESSION['Index']->assign('RESEND_MAIL_BACK_URL','?show=LoginServer');
					return;
				}
				#end
			}
		}

		$_SESSION['Index']->assign_say('REGISTER_PASSWORD');
		$_SESSION['Index']->assign_say('REGISTER_CAPTCHA');
		$_SESSION['Index']->assign_say('REGISTER_RESEND');
		$_SESSION['Index']->assign('REGISTER_RESEND_URL','?show=RegisterAdmin&resendMail');

		$_SESSION['Index']->assign_say('RESEND_MAIL');
		$_SESSION['Index']->assign_say('RESEND_MAIL_DO');
		$_SESSION['Index']->assign_say('RESEND_MAIL_CANCEL');
		$_SESSION['Index']->assign('RESEND_MAIL_CANCEL_URL','?show=LoginServer');
	}

	private function verifyCode(){
		if(!isNumber($_GET['u'])){
			$_SESSION['Index']->assign_say('VERIFICATION_ERROR','VERIFY_ERROR_INVALID_DATA');
			$_SESSION['Index']->assign_say('VERIFICATION_ERROR_TO_RESEND');
		}
		else{
			$UserInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Id,Password,Email,FirstName,SurName,Token,Validated FROM mc_customers WHERE Id='{$_GET['u']}' LIMIT 1");
			if($UserInfo->Validated){
				$_SESSION['Index']->assign_say('VERIFICATION_ALREADY_DONE');
				$_SESSION['Index']->assign_say('VERIFICATION_CONFIRMED_TO_LOGIN');
				$_SESSION['Index']->assign_direct('SHOP_ID', $_GET['shop']);
			}
			elseif($_GET['c'] == RegisterAdmin::generateValidationKey($UserInfo)){
				$_SESSION['Index']->db->update("UPDATE mc_customers SET Validated='1',Token='' WHERE Id='{$UserInfo->Id}' LIMIT 1");
				$_SESSION['Index']->db->insert("INSERT INTO mc_customeraccounts (Time,Current,Difference,CustomersId) VALUES ('".time()."','0','0','{$UserInfo->Id}')");

				$_SESSION['Index']->assign_say('VERIFICATION_CONFIRMED');
				$_SESSION['Index']->assign_say('VERIFICATION_CONFIRMED_TO_LOGIN');
			}
			else{
				$_SESSION['Index']->assign_say('VERIFICATION_ERROR','VERIFY_ERROR_INVALID_DATA');
				$_SESSION['Index']->assign_say('VERIFICATION_ERROR_TO_RESEND');
			}
		}
	}
	private function registrationProcess(){
		if(count($_POST)){
			$firstname = $_POST['firstname'];
			$surname = $_POST['surname'];
			$minecraftname = $_POST['minecraftname'];
			$mail_accept = $_POST['mail_accept'];
			$mail = $_POST['mail'];
			$mail2 = $_POST['mail2'];
			$pw = $_POST['pw'];
			$pw2 = $_POST['pw2'];

			if($_POST['mail'] != $_POST['mail2']){ //Mails ungleich
				$error = 1;
				$_SESSION['Index']->assign_say('REGISTER_SERVER_MAIL_ERROR', 'REGISTER_SERVER_MAILS_NOT_EQUAL');
			}
			if($_POST['pw'] != $_POST['pw2']){ // Passwörter ungleich
				$error = 1;
				$_SESSION['Index']->assign_say('REGISTER_SERVER_PW_ERROR', 'REGISTER_SERVER_PWS_NOT_EQUAL');
			}
			if(!Captcha::isValid()){ //Captcha prüfen
				$error = 1;
				$_SESSION['Index']->assign_say('REGISTER_SERVER_CAPTCHA_INVALID');
			}
			if (!isset($_POST['legal_accepted'])){
				$_SESSION['Index']->assign_say('REGISTER_ACCEPT_LEGAL_ERROR');
				$error = 1;
			}
			if(!$error){
				$newUserId = RegisterAdmin::tryCreateUser($firstname, $surname, $mail, $mail_accept, $pw, $minecraftname, $Error);
				if($newUserId){
					$UserInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Id, FirstName, SurName, Email, RegTime, Password FROM mc_customers WHERE Id='$newUserId' LIMIT 1");

					$token = random_string_by_length(15);
					$_SESSION['Index']->db->update("UPDATE mc_customers SET token='".mysql_real_escape_string($token)."' WHERE Id='{$UserInfo->Id}' LIMIT 1");

					$validationKey = RegisterAdmin::generateValidationKey($UserInfo, $token);
					RegisterAdmin::sendValidationMail($UserInfo, $validationKey);

					$_SESSION['Index']->assign_say('REGISTER_SERVER_SUCCESSFUL_1');
					$_SESSION['Index']->assign_say('REGISTER_SERVER_SUCCESSFUL_2',array(secondsToTimeString(RegisterAdmin::$verificationTime)));
				}
				#region Es sind Fehler beim Erstellen des Users aufgetreten
				else{
					// Username überprüfen
					if($Error['Username'])
						$_SESSION['Index']->assign_say('REGISTER_SERVER_NAME_ERROR', 'REGISTER_SERVER_NAME_INVALID');
					// MinecraftName überprüfen
					if($Error['MinecraftName'])
						$_SESSION['Index']->assign_say('REGISTER_SERVER_MINECRAFTNAME_ERROR', 'REGISTER_SERVER_MINECRAFTNAME_INVALID');
					// E-Mail überprüfen
					switch($Error['Email']){
						case 1:
							$_SESSION['Index']->assign_say('REGISTER_SERVER_MAIL_ERROR', 'REGISTER_SERVER_MAIL_INVALID');
							$_SESSION['Index']->assign_direct('REGISTER_SERVER_MAIL_ERROR_ACCEPT',1);
							break;
						case 2:
							$_SESSION['Index']->assign_say('REGISTER_SERVER_MAIL_ERROR', 'REGISTER_SERVER_MAIL_IN_USE');
							break;
						default:
					}
					// Passwort überprüfen
					if($Error['Password'])
						$_SESSION['Index']->assign_say('REGISTER_SERVER_PW_ERROR', 'REGISTER_SERVER_PW_INVALID');
				}
				#end
			}
		}
		
		

		if(!$newUserId){
			$_SESSION['Index']->assign('REGISTER_SERVER_FIRSTNAME', $firstname);
			$_SESSION['Index']->assign('REGISTER_SERVER_SURNAME', $surname);
			$_SESSION['Index']->assign('REGISTER_SERVER_MINECRAFTNAME_VALUE', $minecraftname);
			$_SESSION['Index']->assign('REGISTER_SERVER_MAIL_VALUE', $mail);
			$_SESSION['Index']->assign('REGISTER_SERVER_MAIL2_VALUE', $mail2);
			$_SESSION['Index']->assign_say('REGISTER_SERVER_FIRST_AND_SUR_NAME');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_MINECRAFTNAME');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_MAIL');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_MAIL_REPEAT');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_PASSWORD');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_PASSWORD_REPEAT');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_LEGAL');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_LEGAL_DESCRIPTION',array(LEGAL_URL));
			$_SESSION['Index']->assign_say('REGISTER_SERVER_CANCEL');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_REGISTER');
			$_SESSION['Index']->assign_say('REGISTER_SERVER_CAPTCHA');
			$_SESSION['Index']->assign('REGISTER_SERVER_CANCEL_URL','?show=LoginServer');
		}
	}


	#region tryCreateUser($FirstName, $SurName, $Email, $EmailAccept, $Password, $MinecraftName, &$Error)
	# Versucht einen neuen Benutzer zu erstellen
	# Bei einem Fehler wird false zurückgegeben und $Error enthält ein Array mit genauen Fehlerbeschreibungen
	# Bei Erfolg wird der Wert von $_SESSION['Index']->db->insert(); zurückgegeben
	private static function tryCreateUser($FirstName, $SurName, &$Email, $EmailAccept, $Password, $MinecraftName, &$Error){
		$Error = null;
		#region Sind alle Werte Ok?
		// Username gültig
		if(!RegisterAdmin::validUsername($FirstName, $SurName))
			$Error['Username'] = 1;
		// E-Mail gültig
		if(!$Email || (!is_email($Email) && ($EmailAccept != $Email)))
			$Error['Email'] = 1;
		// E-Mail in Verwendung
		elseif(RegisterAdmin::emailInUse($Email))
			$Error['Email'] = 2;
		// MinecraftName gültig
		if(!RegisterAdmin::validMinecraftname($MinecraftName))
			$Error['MinecraftName'] = 1;
		// Passwort gültig
		if(!RegisterAdmin::validPassword($Password))
			$Error['Password'] = 1;
		#end
		#region Alles OK, es darf versucht werden, den Benutzer anzulegen
		if($Error == null){
			//hash-Wert erzeugen
			$Password = bcrypt_encode($Email, $Password);
			//Werte in die Datenbank eintragen
			//Den User erstellen
			$insertId = $_SESSION['Index']->db->insert("INSERT INTO mc_customers (FirstName, SurName, Email, Password, RegTime, MinecraftName) VALUES ('".mysql_real_escape_string($FirstName)."','".mysql_real_escape_string($SurName)."','".mysql_real_escape_string($Email)."','".mysql_real_escape_string($Password)."','".time()."','".mysql_real_escape_string($MinecraftName)."')");
			if($insertId) return $insertId;

			$Error['db'] = 1;
			// RegisterAdmin::sendValidationMail($insertId);
		}
		#end
		return false;
	}
	#end
	#region validPassword($pw)
	private static function validPassword($pw)
	{
		// mindestens 8 Zeichen lang
		if(strlen($pw) < 8) return false;
		//mindestens ein Buchstabe und eine Zahl
		if(!preg_match('/[a-zA-Z]/', $pw) || !preg_match('/\d/', $pw)) return false;
		return true;
	}
	#end
	#region validMinecraftname($name)
	private static function validMinecraftname($name)
	{
		if(strlen($name) == 0) return false;
		return true;
	}
	#end
	#region validUsername($name)
	private static function validUsername($FirstName, $SurName)
	{
		if((strlen($FirstName) == 0) || (strlen($SurName) == 0)) return false;
		//@-Zeichen ist nicht erlaubt
		if((strpos($FirstName, '@') !== false) || (strpos($SurName, '@') !== false)) return false;
		return true;
	}
	#end
	#region emailInUse($email)
	private static function emailInUse($Email)
	{
		return $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_customers WHERE email='".mysql_real_escape_string($Email)."' LIMIT 1");
	}
	#end
	#region sendValidationMail($ShopId, $UserId)
	private static function sendValidationMail($UserInfo,$validationKey)
	{
		$name = htmlspecialchars($UserInfo->FirstName.' '.$UserInfo->SurName);
		$url = SECURE_URL.'?show=RegisterAdmin&amp;u='.$UserInfo->Id.'&amp;c='.$validationKey;
		mail(
			//To (ist weiter unten im Header bereits angegeben)
				null,//$userInfo->Email,
			//Subject
				$_SESSION['Index']->say('REGISTER_SERVER_MAIL_SUBJECT'),
			//Message
				$_SESSION['Index']->say('REGISTER_SERVER_MAIL_MESSAGE', array($name,$url), false),
			//Header
				"MIME-Version: 1.0\n"
				."To: \"$name\" <{$UserInfo->Email}>\n"
				."From: \"".BASE_DOMAIN."\" <noreply@".BASE_DOMAIN.">\n"
				."Return-Path: noreply@".BASE_DOMAIN."\n"
				."Reply-To: noreply@".BASE_DOMAIN."\n"
				."Bcc: support@craftingstore.net\n"
				."Content-type: text/html; charset=utf-8\n"
				);
	}
	function generateValidationKey($UserInfo, $token = null){
		return sha1($UserInfo->Id.$UserInfo->Password.$UserInfo->Email.($token == null?$UserInfo->Token:$token).$UserInfo->FirstName.$UserInfo->SurName);
	}
	#end

}
?>