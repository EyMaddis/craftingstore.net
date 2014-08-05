<?php
/************************
**
** File:		itemdetails.php
**	Author: 	Mathis Neumann
**	Date:		12/01/2012
**	Desc:		show detailed information about an item or itemset
**
*************************/

class Itemdetails extends aDisplayable
{
	public function prepareDisplay()
	{
		if($ItemInfo = Item::getItemInfoById($_GET['id'], $ConnectedItems))
		{
			$ItemId = $_GET['id'];

			if($ConnectedItems){
				$_SESSION['Index']->assign('ITEMDETAILS_INFO_LIST', $ConnectedItems);
				$_SESSION['Index']->assign_say('ITEMDETAILS_LIST_DESCRIPTION', 'ITEMDETAILS_LIST_ITEMS_IN_SET');
			}

			$_SESSION['Index']->assign('ITEMDETAILS_LABEL', $ItemInfo->Label);
			$_SESSION['Index']->assign('ITEMDETAILS_DESCRIPTION', $ItemInfo->Description);

			$_SESSION['Index']->assign_say('ITEMDETAILS_POINTS_LABEL');
			$_SESSION['Index']->assign('ITEMDETAILS_POINTS', $ItemInfo->Points);

			$_SESSION['Index']->assign_say('ITEMDETAILS_BUY_COUNTER_LABEL', array($ItemInfo->BuyCounter));

			$_SESSION['Index']->assign_say('ITEMDETAILS_BUY_NOW');
			$_SESSION['Index']->assign_say('ITEMDETAILS_BUY_BUTTON');

			$_SESSION['Index']->assign('ITEMDETAILS_ITEM_ID', $ItemId);
			$_SESSION['Index']->assign('ITEMDETAILS_IMAGE', $_SESSION['Index']->relImgPath.$Image = ($ItemInfo->Image == null ? $_SESSION['Index']->default_img : $ItemInfo->Image));
		}
		else
		{
			$_SESSION['Index']->assign('ITEMDETAILS_ERROR', true);
			$_SESSION['Index']->assign_say('ITEMDETAILS_ERROR_INVALID_ITEM');
		}
	}
}
?>