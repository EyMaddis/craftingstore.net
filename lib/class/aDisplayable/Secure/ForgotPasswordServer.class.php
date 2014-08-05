<?php
defined('_MCSHOP') or die("Security block!");
class ForgotPasswordServer extends aDisplayable{
	public function prepareDisplay()
	{
		function setResetToken($userId){
			$token = random_string_by_length(64);
			$_SESSION['Index']->db->query("UPDATE mc_customers SET ResetPasswordToken='$token' WHERE Id='{$userId}' LIMIT 1");
			return $token;
		}
		function checkToken($userId,$token){
			return ($token == $_SESSION['Index']->db->fetchOne("SELECT ResetPasswordToken FROM mc_customers WHERE Id='{$userId}'"));
		}
		function resetPassword($userId, $userEmail, $token){
			$newPw = random_string_by_length(14);
			$_SESSION['Index']->db->query("UPDATE mc_customers SET Password='".mysql_real_escape_string(bcrypt_encode($userEmail, $newPw))."', ResetPasswordToken = NULL WHERE Id='{$userId}' AND ResetPasswordToken='".mysql_real_escape_string($token)."' LIMIT 1");
			
			return $newPw;
		}
		function checkUser($userId){
			if(isNumber($userId))
			{
				$user = $_SESSION['Index']->db->fetchOneRow("SELECT Id, Email, ResetPasswordToken, FirstName, SurName FROM mc_customers WHERE Id='$userId' LIMIT 1");
				if($user->Id == $userId)
					return $user;
			}
			return false;
		}

		$step = 1;

		if($_GET['u'] && $_GET['t']){
			$step = 3;
			$user = checkUser($_GET['u']);
			if($user !== false && checkToken($user->Id, $_GET['t'])){
				$newPw = resetPassword($user->Id, $user->Email, $_GET['t']);
				mail(
				//To (ist weiter unten im Header bereits angegeben)
					null,//$user->Email,
				//Subject
					$_SESSION['Index']->say('FORGOTPW_PWMAIL_SUBJECT'),
				//Message
					$_SESSION['Index']->say('FORGOTPW_SERV_PWMAIL_MESSAGE', array($user->FirstName." ".$user->SurName, $newPw, SECURE_URL.'?show=LoginServer'), false),
				//Header
					"MIME-Version: 1.0\n"
					."To: {$user->FirstName} {$user->SurName} <{$user->Email}>\n"
					."From: Minecraftshop.net <noreply@".BASE_DOMAIN.">\n"
					."Content-type: text/html; charset=utf-8\n"
					);
			}
			else{
				$error = true;
			}
		}
		elseif($_POST['email']){
			$user = $_SESSION['Index']->db->fetchOneRow("SELECT Id, Email, FirstName, SurName FROM mc_customers WHERE Email='".mysql_real_escape_string($_POST['email'])."' LIMIT 1");
			if($user->Id)
			{
				$token = setResetToken($user->Id);
				mail(
				//To (ist weiter unten im Header bereits angegeben)
					null,//$user->Email,
				//Subject
					$_SESSION['Index']->say('FORGOTPW_MAIL_SUBJECT'),
				//Message
					$_SESSION['Index']->say('FORGOTPW_SERV_MAIL_MESSAGE', array($user->FirstName." ".$user->SurName, SECURE_URL.'?show=ForgotPasswordServer&u='.$user->Id.'&t='.$token), false),
				//Header
					"MIME-Version: 1.0\n"
					."To: {$user->FirstName} {$user->SurName} <{$user->Email}>\n"
					."From: Minecraftshop.net <noreply@".BASE_DOMAIN.">\n"
					."Content-type: text/html; charset=utf-8\n"
					);
				$step = 2;
			}
			else
			{
				//Fehlerbehandlung mit Zählen und sperren etc...
				$error = 1;
			}
		}
		elseif($_POST){
			$error = 2;
		}

		#region Ausgabe
		if($step == 1 || $error){
			$_SESSION['Index']->assign_say('FORGOTPW_SERV_INFO');
			$_SESSION['Index']->assign_say('FORGOTPW_EMAIL');
			$_SESSION['Index']->assign_say('FORGOTPW_SEND');
			$_SESSION['Index']->assign_say('FORGOTPW_CANCEL');

			if($step == 3 && $error){
				$_SESSION['Index']->assign_say('FORGOTPW_SERV_ERROR','FORGOTPW_RESET_INVALID');
				$step = 1;
			}
		}
		elseif($step == 2)
		{
			$_SESSION['Index']->assign_say('FORGOTPW_MAIL_SEND');
			$_SESSION['Index']->assign_say('FORGOTPW_BACK');
		}
		else
		{
			$_SESSION['Index']->assign_say('FORGOTPW_RESET_DONE');
			$_SESSION['Index']->assign_say('FORGOTPW_BACK');
		}
		#end
		$_SESSION['Index']->assign_direct('STEP',$step);
		$_SESSION['Index']->assign_say('FORGOTPW_SERV_TITLE');
	}
}
?>