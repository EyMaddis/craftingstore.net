<?php
defined('_MCSHOP') or die("Security block!");

class LMenu extends aDisplayable
{
	private $groupId = 0;
	private $openGroupId = 0;//$_GET['group']
	private $currentMenu;

	public function refreshMenu()
	{
		$this->currentMenu = $_SESSION['Index']->nstree->getTree();
	}

	public function prepareDisplay()
	{
		if(!$currentMenu)
		{
			$this->refreshMenu();
		}
		$_SESSION['Index']->assign_direct('LMENU_CONTENT', $this->currentMenu);
	}
}

?>