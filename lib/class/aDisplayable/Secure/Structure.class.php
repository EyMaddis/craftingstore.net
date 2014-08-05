<?php
defined('_MCSHOP') or die("Security block!");

class Structure extends aDisplayable
{
	public function prepareDisplay()
	{
		switch($_GET['show']){
			case 'Profile':
				$_SESSION['Index']->assign_say('MAIN_TITLE','MAIN_TITLE_PROFILE');
				break;
			case 'Buypoints':
				$_SESSION['Index']->assign_say('MAIN_TITLE','MAIN_TITLE_BUYPOINTS');
				break;
			default:
				$_SESSION['Index']->assign_say('MAIN_TITLE','MAIN_TITLE_ADMINISTRATION');
		}
		$_SESSION['Index']->assign_say('SHOP_TITLE');
		$_SESSION['Index']->assign_direct('AdminStructurePrepared', true);
	}
}
?>