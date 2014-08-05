<?php
defined('_MCSHOP') or die("Security block!");

class Statistics extends aDisplayable
{
	public function prepareDisplay()
	{
		#$statistiken = array(array('LatestSales','ADM_LATEST_TITLE'));

		if($_GET['c']) $c = $_GET['c'];
		else $c = 'LatestSales';
		if(!class_exists($c)) setLocation('404');

		$_SESSION['Index']->assign_direct('ADM_STATS_NAVIGATION_ACTIVE',$c);
		$_SESSION['Index']->assign_direct('CONTENT',$c.'.tpl');

		$_SESSION['Index']->assign_say('ADM_STATS_SELECT_STAT');
		$_SESSION['Index']->assign_say('ADM_LATEST_TITLE');
	}
}
?>