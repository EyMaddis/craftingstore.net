<?php
defined('_MCSHOP') or die("Security block!");

class PendingTransfers extends aDisplayable
{
	// false = offline
	private $lastPlayerOnlineStatus = false;
	private $lastServerOnlineStatus = 0;

	public function __construct(){
		$this->lastServerOnlineStatus = $_SESSION['Index']->shop->getShopInfo()->ServerOnline;
		//$this->lastPlayerOnlineStatus = $_SESSION['Index']->user->getPlayerOnlineStatus();
	}

	private function doRefresh(){
		$this->lastServerOnlineStatus = $_SESSION['Index']->shop->getShopInfo("ServerOnline");
	}
	public static function transferProduct($transferId){
		if(!$_SESSION['Index']->user->isLoggedIn())
			return 'NO_USER';

		if(!$_SESSION['Index']->shop->getShopInfo("ServerOnline")){
			$_SESSION['Index']->assign_say('PENDING_TRANSFER_INFO', 'PENDING_ITEM_TRANSFER_ERROR');
			return 'SERVER_OFFLINE';
		}

		$result = $_SESSION['Index']->shop->transfer($_SESSION['Index']->user->getLoginId(), $transferId);
		if($result == 'TRANSFERED'){
			$_SESSION['Index']->assign_say('PENDING_TRANSFER_INFO', 'PENDING_ITEM_TRANSFERED');
		}
		elseif($result == 'ITEM_DISABLED'){
			$_SESSION['Index']->assign_say('PENDING_TRANSFER_INFO', 'PENDING_ITEM_DISABLED');
		}
		else{
			$_SESSION['Index']->assign_say('PENDING_TRANSFER_INFO', 'PENDING_ITEM_TRANSFER_ERROR');
		}
		return $result;
	}

	#region public function prepareDisplay()
	public function prepareDisplay()
	{
		if(!$_SESSION['Index']->user->isLoggedIn())
			return;

		#region doFullRefresh()
		if($_GET['fullRefresh'] || $_GET['doTransfer']){
			$this->doRefresh();
		}
		#end

		#region transferProduct($ProductId)
		if($_GET['doTransfer']){
			PendingTransfers::transferProduct($_GET['doTransfer']);
		}
		#end

		#region gedöns anzeigen
		$_SESSION['Index']->assign_say('PENDING_TITLE');
		$pendingItems = array();
		foreach($_SESSION['Index']->db->iterate("
SELECT i.Id, p.Label, p.Image, i.Amount
FROM mc_inventory AS i

LEFT JOIN mc_products AS p
ON p.Id=i.ProductId AND p.ShopId='{$_SESSION['Index']->shop->getId()}'

WHERE i.ShopId='{$_SESSION['Index']->shop->getId()}' AND i.GamerId='{$_SESSION['Index']->user->getLoginId()}'
AND i.TransferTime is NULL AND Locked=0
ORDER BY i.Id DESC") as $row){
			$pendingItems[] = array(
					'Id' => $row->Id,
					'Label' => $row->Label,
					'Amount' => $row->Amount,
					'Image' => Item::getImagePath($row->Image, true),
					'TransferInfo' => ($this->lastServerOnlineStatus ? 0 : 1)
				);
		}
		/*foreach($_SESSION['Index']->db->iterate("
SELECT t.Id, i.Label, i.Image, t.CallNumber,
(
	SELECT NeedsPlayerOnline FROM mc_transferCommands as c
	WHERE t.Id=c.TransferId AND NeedsPlayerOnline='1' AND TYPE='BY_USER'
	LIMIT 1
) AS NeedsPlayerOnline
FROM mc_transfers AS t

LEFT JOIN mc_products AS i
ON i.Id=t.ProductId AND i.ShopId='{$_SESSION['Index']->shop->getId()}'

WHERE t.ShopId='{$_SESSION['Index']->shop->getId()}' AND t.UserId='{$_SESSION['Index']->user->getLoginId()}'
AND (t.ExecutionTime is NULL OR t.FailCount>0) AND t.DisabledUntil<'".time()."'
ORDER BY t.Id DESC") as $row){
			$pendingItems[] = array(
					'Id' => $row->Id,
					'Label' => $row->Label,
					'Amount' => $row->CallNumber,
					'Image' => Item::getImagePath($row->Image, true),
					'TransferInfo' => ($this->lastServerOnlineStatus ? ($this->lastPlayerOnlineStatus || !$row->NeedsPlayerOnline ? 0 : 1) : 2)
				);
		}*/

		if(count($pendingItems) == 0){
			$_SESSION['Index']->assign_say('PENDING_NO_ITEMS');
		}
		else{
			$_SESSION['Index']->assign_say('PENDING_DO_TRANSFER');
			$_SESSION['Index']->assign_say('PENDING_NEEDS_PLAYER_ONLINE');
			$_SESSION['Index']->assign_say('PENDING_NEEDS_SERVER_ONLINE');
			$_SESSION['Index']->assign('PENDING_ITEMS', $pendingItems);
			$_SESSION['Index']->assign('PENDING_TRANSFER_URL', '?show=PendingTransfers&doTransfer=');
		}
		$_SESSION['Index']->assign_say('PENDING_REFRESH');
		$_SESSION['Index']->assign('PENDING_REFRESH_URL', '?show=PendingTransfers&fullRefresh=1');
		$_SESSION['Index']->assign_say('TRANSFER_HISTORY_TITLE');
		$_SESSION['Index']->assign('TRANSFER_HISTORY_URL','?show={TransferHistory}');
		#end
	}
	#end
}

?>