<?php
defined('_MCSHOP') or die("Security block!");

class Message extends aDisplayable
{
	public function prepareDisplay()
	{
		$_SESSION['Index']->assign_say('MESSAGE_TITLE');
		$_SESSION['Index']->assign_say('MESSAGE_BACK');
		$_SESSION['Index']->assign('MESSAGE_BACK_URL',$_GET['back']);

		if(isset($_POST['recaptcha_response_field'])){
			if(1||Captcha::isValid()){
				$subject = trim($_POST['subject']);
				if(strlen($subject) < 10){
					$_SESSION['Index']->assign_say('MESSAGE_SUBJECT_ERROR');
					$error = true;
				}

				$content = trim($_POST['content']);
				if(strlen($content) < 30){
					$_SESSION['Index']->assign_say('MESSAGE_CONTENT_ERROR');
					$error = true;
				}
			}
			else{
				$_SESSION['Index']->assign_say('MESSAGE_CAPTCHA_ERROR');
				$error = true;
			}
		}
		if($_POST['recaptcha_response_field'] && !$error){
			$_SESSION['Index']->db->query(
			"INSERT INTO mc_messages (mail,subject,message,`from`) VALUES ('".
			mysql_real_escape_string($mail)."','".
			mysql_real_escape_string($subject)."','".
			mysql_real_escape_string(str_replace("\n","<br />",str_replace("\n\n","\n",str_replace("\r","\n",$content))))."','".
			mysql_real_escape_string($_GET['back'])."')");
			mail(null,'Problembenachrichtigung: '.htmlspecialchars($subject),
			'<html><head><title>'.htmlspecialchars($subject).'</title></head>
			<body>Benutzer: '.$_POST['mail'].'<br />Seite: '.htmlspecialchars($_GET['back']).'<br /><br />Nachricht:<br />'.htmlspecialchars($content).'</body></html>',
					//Header
						"MIME-Version: 1.0\n"
						."To: <post@mathis-neumann.de>,<rasmus-epha@gmx.de>\n"
						."From: \"Craftingstore Problemmeldung\" <noreply@".BASE_DOMAIN.">\n"
						."Content-type: text/html; charset=utf-8\n");
			$_SESSION['Index']->assign_say('MESSAGE_SUCCESS');
		}
		else{
			$_SESSION['Index']->assign('MESSAGE_SUBMIT_URL','?show=Message&back='.urlencode($_GET['back']));
			
			$_SESSION['Index']->assign('MESSAGE_SUBJECT_VALUE',$_POST['subject']);
			$_SESSION['Index']->assign('MESSAGE_MAIL_VALUE',$_POST['mail']);
			$_SESSION['Index']->assign('MESSAGE_CONTENT_VALUE',$_POST['content']);

			$_SESSION['Index']->assign_say('MESSAGE_DESCRIPTION');
			$_SESSION['Index']->assign_say('MESSAGE_SUBJECT');
			$_SESSION['Index']->assign_say('MESSAGE_MAIL');
			$_SESSION['Index']->assign_say('MESSAGE_CONTENT');
			$_SESSION['Index']->assign_say('MESSAGE_CAPTCHA');
			$_SESSION['Index']->assign_say('MESSAGE_SUBMIT');
		}
	}
}

?>