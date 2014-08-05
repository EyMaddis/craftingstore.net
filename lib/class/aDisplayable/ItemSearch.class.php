<?php
defined('_MCSHOP') or die("Security block!");
class ItemSearch extends aDisplayable
{
	public function prepareDisplay()
	{
		#region Gruppen auslesen
		$gruppen = null;
		$tree = $_SESSION['Index']->nstree->getSubTree(1);
		if($tree)
		{
			foreach($tree as $row)
			{
				$gruppen[] = array('Id' => $row->Id, 'Label' => $row->Label, 'Level' => $row->Level+1);
			}
		}
		$_SESSION['Index']->assign('ITEMSEARCH_GROUPLIST', $gruppen);
		#endregion
		$_SESSION['Index']->assign_say('ITEMSEARCH_TITLE');
		$_SESSION['Index']->assign_say('ITEMSEARCH_WORDS');
		$_SESSION['Index']->assign_say('ITEMSEARCH_SEARCH_IN');
		$_SESSION['Index']->assign_say('ITEMSEARCH_SEARCH_EVERYWHERE');
		$_SESSION['Index']->assign_say('ITEMBOX_NO_SUBGROUPS');
		$_SESSION['Index']->assign_say('ITEMSEARCH_FIND');
	}
}
?>