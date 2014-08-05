<?php
defined('_MCSHOP') or die("Security block!");

class TransferHistory extends aDisplayable
{
	private $page = 1;
	private $productsPerPage = 10;
	private $pagesAround = 4;
	#region public function prepareDisplay()
	public function prepareDisplay(){
		//$productAmount = $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_transfers AS t WHERE t.ShopId='{$_SESSION['Index']->shop->getId()}' AND t.UserId='{$_SESSION['Index']->user->getLoginId()}'");
		$productAmount = $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_inventory AS i WHERE i.ShopId='{$_SESSION['Index']->shop->getId()}' AND i.GamerId='{$_SESSION['Index']->user->getLoginId()}'");

		$_SESSION['Index']->assign('TH_BACK_TO_ITEMS_URL','?show={Itembox}');
		$_SESSION['Index']->assign_say('TH_BACK_TO_ITEMS_LABEL');
		if(!$productAmount){
			$_SESSION['Index']->assign_say('TH_NO_PRODUCTS');
		}
		else{
			if($productAmount > $this->productsPerPage){
				#region Seitenanzahl berechnen
				$maxPage = ceil($productAmount/$this->productsPerPage);
				#end
				#region Übergebene Seitenzahl übernehmen
				if(isNumber($_GET['page'])){
					$this->page = $_GET['page'];
				}
				if($this->page > $maxPage) $this->page = $maxPage;
				if($this->page < 1) $this->page = 1;
				#end

				#region Start- und End-Seite berechnen
				$startPage = $this->page - $this->pagesAround;
				$endPage = $this->page + $this->pagesAround;
				if($startPage < 1) $startPage = 1;
				if($endPage > $maxPage) $endPage = $maxPage;
				$_SESSION['Index']->assign_direct('START_PAGE', $startPage);
				$_SESSION['Index']->assign_direct('END_PAGE', $endPage);
				#end
				#region Nach Links- und Nach Rechts-Seiten berechnen
				$previousPage = $this->page - 1;
				$nextPage = $this->page + 1;
				$firstPage = 1;
				$lastPage = $maxPage;
				if($previousPage < 1) $previousPage = 0;
				if($nextPage > $maxPage) $nextPage = 0;
				$_SESSION['Index']->assign_direct('PREVIOUS_PAGE', $previousPage);
				$_SESSION['Index']->assign_direct('NEXT_PAGE', $nextPage);
				$_SESSION['Index']->assign_direct('FIRST_PAGE', $firstPage);
				$_SESSION['Index']->assign_direct('LAST_PAGE', $lastPage);
				#end

				$_SESSION['Index']->assign_direct('CURRENT_PAGE', $this->page);
				$_SESSION['Index']->assign('PAGE_URL', '?show={TransferHistory}&page=');

				$limit = ' LIMIT '.($this->productsPerPage*($this->page-1)).','.$this->productsPerPage;
			}
			else{
				$limit = '';
			}
			#region Produktinformationen aus der DB lesen
			$historyItems = array();
			foreach($_SESSION['Index']->db->iterate("
	SELECT p.Label, p.Image, i.Amount, i.TransferTime, i.DisabledUntil
	FROM mc_inventory AS i

	LEFT JOIN mc_products AS p
	ON p.Id=i.ProductId AND p.ShopId='{$_SESSION['Index']->shop->getId()}'

	WHERE i.ShopId='{$_SESSION['Index']->shop->getId()}' AND i.GamerId='{$_SESSION['Index']->user->getLoginId()}'
	ORDER BY i.TransferTime IS NULL DESC, TransferTime DESC$limit") as $row){
				if($row->TransferTime){
					if($row->DisabledUntil > time()){
						$TransferInfo = $_SESSION['Index']->lang->say('TH_TRANSFER_DISABLED', array(date('d.m.Y', $row->DisabledUntil),date('H:i', $row->DisabledUntil)));
						$TransferImage = 'transmit_blue.png';
					}
					else{
						$TransferInfo = $_SESSION['Index']->lang->say('TH_TRANSFER_FINISHED');
						$TransferImage = 'accept.png';
					}
				}
				else{
						$TransferInfo = $_SESSION['Index']->lang->say('TH_TRANSFER_PENDING');
					$TransferImage = 'transmit.png';
				}
				$historyItems[] = array(
							'Label' => $row->Label,
							'Image' => Item::getImagePath($row->Image, true),
							'Amount' => $row->Amount,
							'TransferTime' => ($row->TransferTime ? date('d.m.Y H:i:s', $row->TransferTime) : null),
							'TransferImage' => $TransferImage,
							'TransferInfo' => $TransferInfo
						);
			}
			/*foreach($_SESSION['Index']->db->iterate("
	SELECT t.Id, i.Label, i.Image, t.CallNumber, t.ExecutionTime, t.FailCount, t.DisabledUntil,
	(
		SELECT NeedsPlayerOnline FROM mc_transferCommands as c
		WHERE t.Id=c.TransferId AND c.ShopId='{$_SESSION['Index']->shop->getId()}'
		AND NeedsPlayerOnline='1' AND TYPE='BY_USER'
		LIMIT 1
	) AS NeedsPlayerOnline
	FROM mc_transfers AS t

	LEFT JOIN mc_products AS i
	ON i.Id=t.ProductId AND i.ShopId='{$_SESSION['Index']->shop->getId()}'

	WHERE t.ShopId='{$_SESSION['Index']->shop->getId()}' AND t.UserId='{$_SESSION['Index']->user->getLoginId()}'
	ORDER BY ExecutionTime IS NULL DESC, FailCount = 0 ASC, ExecutionTime DESC$limit") as $row){
				if($row->FailCount > 0){
					$TransferInfo = $_SESSION['Index']->lang->say('TH_TRANSFER_ERROR', array($row->FailCount));
					$TransferImage = 'transmit_error.png';
				}
				elseif($row->ExecutionTime){
					if($row->DisabledUntil > time()){
						$TransferInfo = $_SESSION['Index']->lang->say('TH_TRANSFER_DISABLED', array(date('d.m.Y', $row->DisabledUntil),date('H:i', $row->DisabledUntil)));
						$TransferImage = 'transmit_blue.png';
					}
					else{
						$TransferInfo = $_SESSION['Index']->lang->say('TH_TRANSFER_FINISHED');
						$TransferImage = 'accept.png';
					}
				}
				else{
						$TransferInfo = $_SESSION['Index']->lang->say('TH_TRANSFER_PENDING');
					$TransferImage = 'transmit.png';
				}
				$historyItems[] = array(
							'Id' => $row->Id,
							'Label' => $row->Label,
							'Image' => Item::getImagePath($row->Image, true),
							'Amount' => $row->CallNumber,
							'ExecutionTime' => ($row->ExecutionTime ? date('d.m.Y H:i:s', $row->ExecutionTime) : null),
							'TransferImage' => $TransferImage,
							'TransferInfo' => $TransferInfo
						);
			}*/
			$_SESSION['Index']->assign('HISTORY_ITEMS', $historyItems);
			#end
		}
	}
	#end
}

?>