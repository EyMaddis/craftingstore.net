<?php

class Toppanel extends aDisplayable
{
	public function prepareDisplay()
	{
		$_SESSION['Index']->assign_say('LOGIN_SERVER_OWNERS');
		$_SESSION['Index']->assign_say('LOGIN_SERVER_OWNERS_DESCRIPTION1');
		$_SESSION['Index']->assign_say('LOGIN_SERVER_OWNERS_DESCRIPTION2');
		$_SESSION['Index']->assign_say('LOGIN_WELCOME_HEADER');
		$_SESSION['Index']->assign_say('LOGIN_REGISTER_DESCRIPTION');
		$_SESSION['Index']->assign_say('LOGIN_WELCOME_DESCRIPTION',array($_SESSION['Index']->shop->getShopInfo()->Label));
		$_SESSION['Index']->assign_say('LOGIN_REGISTER_NOW');
		$_SESSION['Index']->assign('LOGIN_WELCOME_LINK',BASE_DOMAIN);

		if($_SESSION['Index']->user->isLoggedIn())
		{
			$_SESSION['Index']->assign('TOP_LOGGEDIN', true);
			$_SESSION['Index']->assign('NICKNAME', $_SESSION['Index']->user->getName());
			$_SESSION['Index']->assign_say('TOP_GREETS');
			$_SESSION['Index']->assign_say('TOP_CLOSE');
			$_SESSION['Index']->assign_say('TOP_OPEN','TOP_OPEN_PROFILE');

			$_SESSION['Index']->assign_say('LOGIN_PROFILE');
			$_SESSION['Index']->assign_say('CONTROL_CENTER_HEADER');
			$_SESSION['Index']->assign_say('LOGIN_WELCOME_HEADER');
			$_SESSION['Index']->assign_say('LOGIN_PROFILE_LABEL');
			$_SESSION['Index']->assign_say('LOGIN_LOGOFF_LABEL');
			$_SESSION['Index']->assign_say('LOGIN_WELCOME', array($_SESSION['Index']->user->getName()));

			$_SESSION['Index']->assign_say('PROFILE_URL', '?shop=secure&red='.urlencode('?show=Profile&shop='.$_SESSION['Index']->shop->getId()));
			$_SESSION['Index']->assign_say('LOGOUT_URL', '?shop=secure&red='.urlencode('?show=Login&logoff='.$_SESSION['Index']->user->getLoginId().'&shop='.$_SESSION['Index']->shop->getId()));
		}
		else
		{
			$_SESSION['Index']->assign('TOP_LOGGEDIN', false);

			$_SESSION['Index']->assign_say('LOGIN_REGISTER_NOW');
			$_SESSION['Index']->assign_say('LOGIN_LOGIN_HEADER');
			$_SESSION['Index']->assign_say('LOGIN_LOGIN_DESCRIPTION');
			$_SESSION['Index']->assign_say('LOGIN_LOGIN_NOW');

			$_SESSION['Index']->assign_say('TOP_GREETS');
			$_SESSION['Index']->assign_say('TOP_OPEN');
		}
		$_SESSION['Index']->assign_say('TOP_CLOSE');
	}
}

?>