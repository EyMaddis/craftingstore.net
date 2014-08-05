<?php

class ItemListUpdater{
	private $ShopId;
	private $ProductId;
	private $selectedItemsArray = array();
	private $selectedItemsAmountArray = array();
	private $deleteItemButton;
	private $editItemButton;

	public $availableItems = array();
	public $selectedItems = array();
	public $hasItems = 0;
	public $addItemId = '';
	public $addItemAmount = '';
	public $editItemId = null;
	public $CanSave = true;


	public function __construct($ShopId, $ProductId){
		$this->ShopId = $ShopId;
		$this->ProductId = $ProductId;
	}

	public function load(){
		$this->getFullItemList();
		if($this->ProductId > 0){
		foreach($_SESSION['Index']->db->iterate("SELECT ItemId,Amount FROM mc_ItemsInProduct WHERE ProductId='{$this->ProductId}' AND ShopId='{$this->ShopId}'") as $row){
				$this->selectedItemsArray[$row->ItemId] = $row->ItemId;
				$this->selectedItemsAmountArray[$row->ItemId] = $row->Amount;
			}
		}

		$this->updateContent();
	}
	public function update($selectedItemsArray, $selectedItemsAmountArray, $addItemId, $addItemAmount, $deleteItemButton, $editItemButton /*, $itemEditCancelButton, $itemEditApplyButton */){
		$this->getFullItemList();
		$this->selectedItemsArray = $selectedItemsArray;
		$this->selectedItemsAmountArray = $selectedItemsAmountArray;
		$this->deleteItemButton = $deleteItemButton;
		$this->editItemButton = $editItemButton;
		$this->addItem($addItemId, $addItemAmount);

		$this->updateContent();

		// Wenn einer der Buttons gedrückt wurde, darf nicht gespeichert werden
		if($deleteItemButton) $this->CanSave = false;
	}

	public function save($newProductId = 0){
		if($newProductId > 0){
			$ProductId = $newProductId;
		}
		else{
			$ProductId = $this->ProductId;
			$_SESSION['Index']->db->query("DELETE FROM mc_ItemsInProduct WHERE ProductId='$ProductId' AND ShopId='{$this->ShopId}'");
		}

		$includedItems = "";
		foreach($this->selectedItems as $value){
			if($includedItems) $includedItems .= ',';
			$includedItems .= "('{$this->ShopId}','$ProductId','{$value['Id']}','{$value['Amount']}')";
		}
		if($includedItems){
			$_SESSION['Index']->db->query("INSERT INTO mc_ItemsInProduct (ShopId,ProductId,ItemId,Amount) VALUES ".$includedItems);
		}
	}

	private function updateContent(){
		$this->processSelectedItems();
		$this->refreshVars();
	}
	private function getFullItemList(){
		foreach($_SESSION['Index']->db->iterate("SELECT Id, Name FROM mc_items WHERE (ShopId='{$this->ShopId}' OR (ShopId='0' AND Id NOT IN (SELECT Id FROM mc_items WHERE ShopId='{$this->ShopId}'))) ORDER BY ShopId DESC, Name ASC") as $row){
		// foreach($_SESSION['Index']->db->iterate("SELECT Id, Name FROM mc_items WHERE ShopId='{$this->ShopId}' ORDER BY Name ASC") as $row){
			$this->availableItems[$row->Id] = $row->Name;
			if(isset($this->itemEditButton[$key])){
				$this->itemEditId = $key;
			}
		}
	}
	private function processSelectedItems(){
		if(!is_array($this->selectedItemsArray))
			return;

		// Übergebene Items in anderes Array verschieben
		foreach($this->selectedItemsArray as $key => $value){
			// nur Items, die in der Liste drin sind und nicht gelöscht werden sollen, werden übernommen
			if(!isset($this->deleteItemButton[$key]) && isset($this->availableItems[$key]) && isNumber($this->selectedItemsAmountArray[$key])){
				$this->selectedItems[] = array(
					'Id' => $key,
					'Name' => $this->availableItems[$key],
					'Amount' => $this->selectedItemsAmountArray[$key]#,'Edit' => (!isset($_POST['itemEditCancelButton'][$key]) && (isset($_POST['itemEditButton'][$key]) || isset($_POST['itemEditName'][$key])) ? array('ItemId' => $key) : null)
				);
				unset($this->availableItems[$key]);

				if(isset($this->editItemButton[$key])){
					$this->editItemId = $key;
				}
			}
		}
	}

	private function addItem($addItemId, $addItemAmount){
		if(isset($this->availableItems[$addItemId]) && isNumber($addItemAmount)){
			ItemListUpdater::cloneItemIfNeccessary($this->ShopId, $addItemId);
			$this->selectedItems[] = array(
				'Id' => $addItemId,
				'Name' => $this->availableItems[$addItemId],
				'Amount' => $addItemAmount
			);
			unset($this->availableItems[$addItemId]);
		}
		else{
			$this->addItemId = $addItemId;
			$this->addItemAmount = $addItemAmount;
		}
	}
	private function refreshVars(){
		$this->hasItems = count($this->selectedItems) > 0;
		if(count($this->availableItems) == 0)
			$this->availableItems = null;
	}

	private static function cloneItemIfNeccessary($ShopId, $ItemId){
		$_SESSION['Index']->db->query("INSERT IGNORE INTO mc_items (Id,ShopId,Name,Ingame,MineId,Damage,Lore,Image)
		SELECT Id,'$ShopId',Name,Ingame,MineId,Damage,Lore,Image
		FROM mc_items WHERE Id='$ItemId' AND ShopId='0' LIMIT 1");
	}
}

?>