<?php
defined('_MCSHOP') or die("Security block!");

class Captcha extends aDisplayable
{
	public function prepareDisplay()
	{
		$_SESSION['Index']->assign_direct('CAPTCHA_KEY',CAPTCHA_PUBLIC_KEY);
	}

	public static function isValid($post = true){
		if(!isset($_POST['recaptcha_response_field']))
			return true;

		require_once('./lib/recaptcha/recaptchalib.php');

		if($post)
			$resp = recaptcha_check_answer(CAPTCHA_PRIVATE_KEY,$_SERVER['REMOTE_ADDR'],$_POST['recaptcha_challenge_field'],$_POST['recaptcha_response_field']);
		else
			$resp = recaptcha_check_answer(CAPTCHA_PRIVATE_KEY,$_SERVER['REMOTE_ADDR'],$_GET['recaptcha_challenge_field'],$_GET['recaptcha_response_field']);
		if($resp->is_valid)
			return true;
		return false;
	}
}

?>