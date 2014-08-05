<?php
defined('_MCSHOP') or die("Security block!");

class Footer extends aDisplayable
{
	public function prepareDisplay()
	{
		$_SESSION['Index']->assign('FOOTER_VERSION', $_SESSION['Index']->getVersion());

		foreach($_SESSION['Index']->db->Iterate("SELECT Id,Language,Image FROM mc_languages WHERE Id<>'{$_SESSION['Index']->lang->getLangId()}' ORDER BY Language") as $row)
		{
			$lang[] = array(
				'Id' => $row->Id,
				'Language' => $row->Language,
				'Image' => $row->Image);
		}
		$_SESSION['Index']->assign('FOOTER_LANG', $lang);
		$_SESSION['Index']->assign('POWERED_BY', POWERED_BY);
		$_SESSION['Index']->assign('COMPANY', COMPANY);
		$_SESSION['Index']->assign('IMPRINT_URL', IMPRINT_URL);
		$_SESSION['Index']->assign('TOS_URL', TOS_URL);
		$_SESSION['Index']->assign('PROJECTNAME', PROJECTNAME);
	}
}

?>