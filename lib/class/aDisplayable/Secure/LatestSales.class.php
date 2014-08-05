<?php
defined('_MCSHOP') or die("Security block!");

class LatestSales extends aDisplayable
{
	private $pagination;
	function __construct()
	{
		$this->pagination = new Pagination();
	}

	public function prepareDisplay(){
		$_SESSION['Index']->assign_direct('NAVIGATION', true);
		$_SESSION['Index']->assign_say('ADM_LATEST_TITLE');
		$_SESSION['Index']->assign_say('ADM_LATEST_HEADER_PRODUCT');
		$_SESSION['Index']->assign_say('ADM_LATEST_HEADER_PLAYER');
		$_SESSION['Index']->assign_say('ADM_LATEST_HEADER_AMOUNT');
		$_SESSION['Index']->assign_say('ADM_LATEST_HEADER_DATE');
		$_SESSION['Index']->assign_say('ADM_LATEST_HEADER_STATUS');
		$_SESSION['Index']->assign_say('ADM_LATEST_HEADER_REVENUE');

		$this->pagination->prepare($_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_gameraccounts AS ga INNER JOIN mc_inventory AS i ON ga.InventoryId=i.Id WHERE Action IS NOT NULL AND ga.ShopId='{$_SESSION['Index']->adminShop->getId()}'"), '?show=Statistics&c=LatestSales', $first, $number);

		foreach($_SESSION['Index']->db->iterate("
SELECT
	g.Nickname,
	p.Label AS Item,
	p.Image AS Image,
	ga.Revenue,
	ga.Action,
	i.Amount,
	i.TransferTime,
	IFNULL(TransferTime, ga.Time) AS RowTime

FROM mc_gameraccounts AS ga

INNER JOIN mc_inventory AS i
ON ga.InventoryId=i.Id

INNER JOIN mc_gamer AS g
ON ga.GamerId=g.Id

INNER JOIN mc_products AS p
ON i.ProductId=p.Id

WHERE Action is not null AND ga.ShopId='{$_SESSION['Index']->adminShop->getId()}' AND p.ShopId='{$_SESSION['Index']->adminShop->getId()}'
ORDER BY RowTime DESC, g.Nickname ASC, p.Label ASC
LIMIT $first,$number") as $row)
		{
			switch($row->Action){
				case 'DEFAULT':
					$info = $_SESSION['Index']->lang->say('ADM_LS_INFO_NEW_USER_LONG');
					$status = $_SESSION['Index']->lang->say('ADM_LS_INFO_NEW_USER');
					$class = 'successful';
					break;
				case 'INPAYMENT':
					$info = $_SESSION['Index']->lang->say('ADM_LS_INFO_BOUGHT_POINTS_LONG');
					$status = $_SESSION['Index']->lang->say('ADM_LS_INFO_BOUGHT_POINTS');
					$class = 'successful';
					break;
				case 'BOUGHT_ITEM':
				case 'BOUGHT_ITEM_WITH_BONUS':
					if(!$row->TransferTime){
						$info = $_SESSION['Index']->lang->say('ADM_LS_INFO_BOUGHT_PRODUCT_LONG');
						$status = $_SESSION['Index']->lang->say('ADM_LS_INFO_BOUGHT_PRODUCT');
						$class = 'notice';
					}
					else{
						$info = $_SESSION['Index']->lang->say('ADM_LS_INFO_PRODUCT_TRANSFERD_LONG');
						$status = $_SESSION['Index']->lang->say('ADM_LS_INFO_PRODUCT_TRANSFERD');
						$class = 'successful';
					}
					break;
				default:
					$info = '';
					$status = '';
					$class = '';
			}
			if(date('dmY') == date('dmY', $row->RowTime)){
				$date = 'X Heute, '.date('H:i', $row->RowTime);
			}
			else{
				$date = date('d.m.Y H:i', $row->RowTime);
			}

			$list[] = array('Name' => $row->Nickname,
				'Item' => $row->Item,
				'Image' => Item::getImagePath($row->Image, true),
				'Amount' => $row->Amount,
				'Date' => $date,
				'Status' => $status,
				'Info' => $info,
				'class' => $class,
				'Difference' => CurrencyFormatted($row->Revenue/100).' '.CURRENCY_SHORT
			);
		}
		if(!$list)
			$_SESSION['Index']->assign_say('ADM_LATEST_NO_LIST');
		else
			$_SESSION['Index']->assign('ADM_LATEST_LIST', $list);
	}
}
?>