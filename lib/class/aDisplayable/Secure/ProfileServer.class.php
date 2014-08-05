<?php
defined('_MCSHOP') or die("Security block!");

class ProfileServer extends aDisplayable
{
	public function prepareDisplay()
	{
		if(count($_POST)){
			if($_POST['passwordfirst'] || $_POST['passwordsecond']){
				if ($_POST['passwordfirst'] == $_POST['passwordsecond']){
					$Pw = $_POST['passwordfirst'];
					if(User::validPassword($Pw)){
						$Email = $_SESSION['Index']->db->fetchOne("SELECT Email FROM mc_customers WHERE Id='{$_SESSION['CustomerId']}'");
						$crypt = bcrypt_encode($Email, $Pw);
						$_SESSION['Index']->db->query("UPDATE mc_customers SET Password='".mysql_real_escape_string($crypt)."' WHERE Id='{$_SESSION['CustomerId']}'");
					}
					else {
						//error: Password not safe enough
					}
				}
				else {
					//error: Passwords not identical
				}
			}
			$fehler = 0;
			if($_POST['paypal'] && (!is_email($_POST['paypal']) && ($_POST['paypal_accept'] != $_POST['paypal']))){
				$fehler = 1;
				$_SESSION['Index']->assign_say('ADM_PROFILE_PAYPAL_ERROR');
			}
			if($fehler)
			{
				$_SESSION['Index']->assign_direct('ADM_PROFILE_ERROR',1);
			}
			else
			{
				$_SESSION['Index']->db->query("UPDATE mc_customers SET PaypalMail='".mysql_real_escape_string($_POST['paypal'])."' WHERE Id='{$_SESSION['CustomerId']}'");
			}
		}
		$_SESSION['Index']->assign_say('ADM_PROFILE_TITLE');
		$_SESSION['Index']->assign_say('ADM_PROFILE_SUBMIT');
		$_SESSION['Index']->assign_say('ADM_PROFILE_PAYPAL_INFO');
		$_SESSION['Index']->assign_say('ADM_PROFILE_NAME');
		$_SESSION['Index']->assign_say('ADM_PROFILE_MAIL');
		$_SESSION['Index']->assign_say('ADM_PROFILE_PAYPAL');
		$_SESSION['Index']->assign_say('ADM_PROFILE_MINECRAFTNAME');
		$_SESSION['Index']->assign_say('ADM_PROFILE_PW');
		$_SESSION['Index']->assign_say('ADM_PROFILE_PWCHANGE');
		$_SESSION['Index']->assign_say('ADM_PROFILE_PW_INFO');

		$customerInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Email,FirstName,SurName,PaypalMail,MinecraftName FROM mc_customers WHERE Id='{$_SESSION['CustomerId']}'");
		$_SESSION['Index']->assign('ADM_PROFILE_NAME_VALUE',$customerInfo->FirstName.' '.$customerInfo->SurName);
		$_SESSION['Index']->assign('ADM_PROFILE_MAIL_VALUE',$customerInfo->Email);
		$_SESSION['Index']->assign('ADM_PROFILE_PAYPAL_VALUE',isset($_POST['paypal'])?$_POST['paypal']:$customerInfo->PaypalMail);
		$_SESSION['Index']->assign('ADM_PROFILE_MINECRAFT_NAME_VALUE',$customerInfo->MinecraftName);
	}
}
?>