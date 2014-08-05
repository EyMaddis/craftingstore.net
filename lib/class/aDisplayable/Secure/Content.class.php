<?php
defined('_MCSHOP') or die("Security block!");

class Content extends aDisplayable
{
	private $menu = array(
		array('c' => 'ShopConfig','LANG' => 'ADM_CONTENT_SHOP_CONFIG_TITLE'),
		array('c' => 'Topmenu','LANG' => 'ADM_CONTENT_TOPNAV_TITLE'),
		array('c' => 'CustomCSS','LANG' => 'ADM_CONTENT_CUSTOMCSS_TITLE')
	);
	public function prepareDisplay()
	{
		if($_GET['c']) $c = $_GET['c'];
		else $c = 'ShopConfig';
		if(!class_exists($c)) $c = $this->menu[0]['c'];

		$list = array();
		foreach($this->menu as $row){
			$list[] = array('c' => $row['c'], 'Title' => $_SESSION['Index']->lang->say($row['LANG']));
		}

		$_SESSION['Index']->assign('ADM_CONTENT_DEFAULT_URL','?show=Content&c=');
		$_SESSION['Index']->assign_direct('ADM_CONTENT_MENU_LIST',$list);
		$_SESSION['Index']->assign('ADM_CONTENT_NAVIGATION_ACTIVE',$c);
		$_SESSION['Index']->assign_direct('CONTENT',$c.'.tpl');

		$_SESSION['Index']->assign_say('ADM_CONTENT_SELECT_STAT');
	}
}
?>