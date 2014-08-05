<?php
defined('_MCSHOP') or die("Security block!");

class ItemStats extends aDisplayable
{
	private $pagination;
	function __construct()
	{
		$this->pagination = new Pagination();
	}

	public function prepareDisplay()
	{
		$this->pagination->prepare(
			$_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_products WHERE (ShopId='{$_SESSION['Index']->adminShop->getId()}')"),
			'?show=Statistics&c=ItemStats', $first, $number);

		$items = array();
		foreach($_SESSION['Index']->db->iterate("SELECT p.Label,p.Image,p.Points,p.BuyCounter,p.Revenue,ig.Label AS GroupLabel FROM mc_products AS p INNER JOIN mc_productGroups AS ig ON ig.Id=p.GroupId AND ig.ShopId='{$_SESSION['Index']->adminShop->getId()}' WHERE p.ShopId='{$_SESSION['Index']->adminShop->getId()}' ORDER BY p.Revenue DESC, p.BuyCounter DESC, p.Label ASC LIMIT $first,$number") as $row){
			$items[] = array(
				'Label' => $row->Label,
				'Image' => Item::getImagePath($row->Image, true),
				'Points' => $row->Points,
				'BuyCounter' => $row->BuyCounter,
				'Revenue' => CurrencyFormatted($row->Revenue/100).' '.CURRENCY_SHORT,
				'GroupLabel' => $row->GroupLabel
			);
		}
		$_SESSION['Index']->assign('ADM_ITEM_LIST', $items);
		$_SESSION['Index']->assign_say('ADM_ITEMSTATS_TITLE');
		$_SESSION['Index']->assign_say('ADM_ITEMSTATS_ITEM');
		$_SESSION['Index']->assign_say('ADM_ITEMSTATS_GROUP');
		$_SESSION['Index']->assign_say('ADM_ITEMSTATS_POINTS');
		$_SESSION['Index']->assign_say('ADM_ITEMSTATS_BUYCOUNT');
		$_SESSION['Index']->assign_say('ADM_ITEMSTATS_REVENUE');
	}
}
?>