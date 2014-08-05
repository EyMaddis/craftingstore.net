<?php
defined('_MCSHOP') or die("Security block!");
class ForgotPassword extends aDisplayable{
	public function prepareDisplay(){
		function setResetToken($userId){
			$token = random_string_by_length(64);
			$_SESSION['Index']->db->query("UPDATE mc_gamer SET ResetPasswordToken='$token' WHERE Id='{$userId}' LIMIT 1");
			return $token;
		}
		function checkToken($userId,$token){
			return ($token == $_SESSION['Index']->db->fetchOne("SELECT ResetPasswordToken FROM mc_gamer WHERE Id='{$userId}'"));
		}
		function resetPassword($userId, $userEmail, $token){
			$newPw = random_string_by_length(14);
			$_SESSION['Index']->db->query("UPDATE mc_gamer SET Password='".mysql_real_escape_string(bcrypt_encode($userEmail, $newPw))."', ResetPasswordToken = NULL WHERE Id='{$userId}' AND ResetPasswordToken='".mysql_real_escape_string($token)."' LIMIT 1");
			return $newPw;
		}
		function checkUser($userId){
			if(isNumber($userId))
			{
				$user = $_SESSION['Index']->db->fetchOneRow("SELECT Id, Nickname, Email, ResetPasswordToken FROM mc_gamer WHERE Id='$userId' LIMIT 1");
				if($user->Id == $userId)
					return $user;
			}
			return false;
		}

		$_SESSION['Index']->assign_say('FORGOTPW_TITLE');
		$_SESSION['Index']->assign_direct('SHOP_ID',$_GET['shop']);
		$_SESSION['Index']->assign_say('FORGOTPW_BACK');

		if($_GET['u'] && $_GET['t']){
			$user = checkUser($_GET['u']);
			if($user !== false && checkToken($user->Id, $_GET['t'])){
				$newPw = resetPassword($user->Id, $user->Email, $_GET['t']);
				if(mail(
					//To (ist weiter unten im Header bereits angegeben)
						null,//$user->Email,
					//Subject
						$_SESSION['Index']->say('FORGOTPW_PWMAIL_SUBJECT'),
					//Message
						$_SESSION['Index']->say('FORGOTPW_PWMAIL_MESSAGE', array($user->Nickname, $newPw, SECURE_URL.'?show=Login&shop='.$_GET['shop']), false),
					//Header
						"MIME-Version: 1.0\n"
						."To: \"{$user->Nickname}\" <{$user->Email}>\n"
						."From: \"".BASE_DOMAIN."\" <noreply@".BASE_DOMAIN.">\n"
						."Content-type: text/html; charset=utf-8\n"
						)){
					$_SESSION['Index']->assign_say('FORGOTPW_MAIL_SEND','FORGOTPW_RESET_DONE');
				}
				else{
					$_SESSION['Index']->assign_say('FORGOTPW_MAIL_ERROR','FORGOTPW_INVALID_MAIL');
				}
			}
			else{
				$_SESSION['Index']->assign_say('FORGOTPW_MAIL_ERROR','FORGOTPW_RESET_INVALID');
			}
			return;
		}

		if (isset($_POST['recaptcha_response_field']) && !Captcha::isValid()){
			$_SESSION['Index']->assign_say('REGISTER_CAPTCHA_INVALID');
			$error = 1;
		}	
		if($_POST['text'] && !$error){
			$user = $_SESSION['Index']->db->fetchOneRow("SELECT Id, Nickname, Email FROM mc_gamer WHERE Nickname='".mysql_real_escape_string($_POST['text'])."' OR Email='".mysql_real_escape_string($_POST['text'])."' LIMIT 1");
			if($user->Id){
				$token = setResetToken($user->Id);
				if(mail(
				//To (ist weiter unten im Header bereits angegeben)
					null,//$user->Email,
				//Subject
					$_SESSION['Index']->say('FORGOTPW_MAIL_SUBJECT'),
				//Message
					$_SESSION['Index']->say('FORGOTPW_MAIL_MESSAGE', array($user->Nickname, SECURE_URL.'?show=ForgotPassword&shop='.$_GET['shop'].'&u='.$user->Id.'&t='.$token), false),
				//Header
					"MIME-Version: 1.0\n"
					."To: {$user->Nickname} <{$user->Email}>\n"
					."From: \"".BASE_DOMAIN."\" <noreply@".BASE_DOMAIN.">\n"
					."Content-type: text/html; charset=utf-8\n"
					)){
					$_SESSION['Index']->assign_say('FORGOTPW_MAIL_SEND');
					return;
				}
				else{
					$_SESSION['Index']->assign_say('FORGOTPW_ERROR','FORGOTPW_ERROR_MAIL_NOT_SEND');
				}
			}
			else{
				//Fehlerbehandlung mit Zählen und sperren etc...
				$_SESSION['Index']->assign_say('FORGOTPW_ERROR','FORGOTPW_ERROR_NOT_FOUND');
			}
		}

		$_SESSION['Index']->assign_say('REGISTER_CAPTCHA');
		$_SESSION['Index']->assign_say('FORGOTPW_USER_MAIL');
		$_SESSION['Index']->assign_say('FORGOTPW_INFO');
		$_SESSION['Index']->assign_say('FORGOTPW_SEND');
		$_SESSION['Index']->assign_say('FORGOTPW_CANCEL');
	}
}
?>