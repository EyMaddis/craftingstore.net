<?php
defined('_MCSHOP') or die("Security block!");
class Account extends aDisplayable{
	public function prepareDisplay(){
		if(!$_SESSION['Index']->user->isLoggedIn())
			return;

		$_SESSION['Index']->assign_say('ACCOUNT_TITLE');
		$_SESSION['Index']->assign_say('ACCOUNT_CURRENT');
		$_SESSION['Index']->assign('ACCOUNT_CURRENT_POINTS', User::getNormalPoints($_SESSION['Index']->user->getLoginId()));
		$_SESSION['Index']->assign('ACCOUNT_CURRENT_BONUSPOINTS', User::getBonusPoints($_SESSION['Index']->user->getLoginId(), $_SESSION['Index']->shop->getId()));
		$_SESSION['Index']->assign_say('ACCOUNT_PAY_MORE');
		$_SESSION['Index']->assign_say('ACCOUNT_BONUSPOINTS');
		$_SESSION['Index']->assign('ACCOUNT_REDIRECT','?shop=secure&red='.urlencode('?show=Buypoints&shop='.$_SESSION['Index']->shop->getId()));
	}
}
?>