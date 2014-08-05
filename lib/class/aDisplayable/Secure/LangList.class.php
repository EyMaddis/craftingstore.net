<?php
defined('_MCSHOP') or die("Security block!");

class LangList extends aDisplayable
{
	public function prepareDisplay()
	{
		foreach($_SESSION['Index']->db->Iterate("SELECT Id,Language,Image FROM mc_languages WHERE Id<>'{$_SESSION['Index']->lang->getLangId()}' ORDER BY Language") as $row)
		{
			$lang[] = array(
				'Id' => $row->Id,
				'Language' => $row->Language,
				'Image' => $row->Image);
		}
		$_SESSION['Index']->assign('LANGS', $lang);
		$params = '?';
		foreach($_GET as $key => $value)
		{
			if($key == 'setLang')
				continue;
			$params .= $key.'='.$value;
			$params .= '&';
		}
		$_SESSION['Index']->assign('LANG_PRE_URL', $params);
	}
}

?>