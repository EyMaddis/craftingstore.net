<?php

class ProductListUpdater{
	private $selectedProductsArray;
	private $selectedProductsAmountArray;
	private $deleteProductButton;
	private $ProductId;

	public $addProductId = '';
	public $addProductAmount = '';

	public $CanSave = true;
	public $hasProducts = null;
	public $selectedProductsList = array();
	public $availableProductsList = array();

	public function __construct($ShopId, $ProductId){
		$this->ShopId = $ShopId;
		$this->ProductId = $ProductId;
	}

	public function load(){
		$this->getFullProductList();
		
		foreach($_SESSION['Index']->db->iterate("SELECT ProductId,Amount FROM mc_ProductsInProduct WHERE ParentProductId='{$this->ProductId}' AND ShopId='{$this->ShopId}'") as $row){
			$this->selectedProductsArray[$row->ProductId] = $row->ProductId;
			$this->selectedProductsAmountArray[$row->ProductId] = $row->Amount;
		}
		$this->processSelectedProducts();
		$this->refreshVars();
	}
	public function update($selectedProductsArray, $selectedProductsAmountArray, $addProductId, $addProductAmount, $deleteProductButton){
		$this->getFullProductList();

		$this->selectedProductsArray = $selectedProductsArray;
		$this->selectedProductsAmountArray = $selectedProductsAmountArray;
		$this->deleteProductButton = $deleteProductButton;

		$this->processSelectedProducts();
		$this->addProduct($addProductId, $addProductAmount);

		if($deleteProductButton) $this->CanSave = false;
	}
	public function save($newProductId = 0){
		if($newProductId > 0){
			$ProductId = $newProductId;
		}
		else{
			$ProductId = $this->ProductId;
			$_SESSION['Index']->db->query("DELETE FROM mc_ProductsInProduct WHERE ParentProductId='$ProductId' AND ShopId='{$this->ShopId}'");
		}

		$includedProducts = "";
		foreach($this->selectedProductsList as $value){
			if($includedProducts)
				$includedProducts .= ',';
			$includedProducts .= "('$ProductId','{$value['Id']}','{$this->ShopId}','{$value['Amount']}')";
		}
		if($includedProducts){
			$_SESSION['Index']->db->query("INSERT INTO mc_ProductsInProduct (ParentProductId,ProductId,ShopId,Amount) VALUES ".$includedProducts);
		}
	}

	private function getFullProductList(){
		foreach($_SESSION['Index']->db->iterate("SELECT Id, Label FROM mc_products WHERE Id<>'{$this->ProductId}' AND ShopId='{$this->ShopId}' ORDER BY Label ASC") as $row){
			$this->availableProductsList[$row->Id] = $row->Label;
		}
	}

	private function processSelectedProducts(){
		//Vorhandene Produkte übernehmen
		if(!is_array($this->selectedProductsArray)){
			return;
		}

		foreach($this->selectedProductsArray as $key => $value){
			//nur Items, die in der List drin sind und nicht gelöscht werden sollen, werden übernommen
			if(isset($this->availableProductsList[$key]) && !isset($this->deleteProductButton[$key])){
				// Rekursiv überprüfen, ob das Produkt das hinzuzufügende Produkt nicht bereits enthält
				if(!$this->ProductId || !ProductListUpdater::productInProduct($key, $this->ShopId, $this->ProductId)){
					$this->selectedProductsList[] = array(
						'Id' => $key,
						'Label' => $this->availableProductsList[$key],
						'Amount' => $this->selectedProductsAmountArray[$key]
					);
				}
				unset($this->availableProductsList[$key]);
			}
		}
	}

	private function addProduct($addProductId, $addProductAmount){
		if(isset($this->availableProductsList[$addProductId]) && isNumber($addProductAmount)){
			// Rekursiv überprüfen, ob das Produkt das hinzuzufügende Produkt nicht bereits enthält
			if(!$this->ProductId || !ProductListUpdater::productInProduct($this->ShopId, $addProductId, $this->ProductId)){
				$this->selectedProductsList[] = array(
					'Id' => $addProductId,
					'Label' => $this->availableProductsList[$addProductId],
					'Amount' => $addProductAmount
				);
			}
			unset($this->availableProductsList[$addProductId]);
		}
		else{
			$this->addProductId = $addProductId;
			$this->addProductAmount = $addProductAmount;
		}
	}

	private function refreshVars(){
		$this->hasProducts = count($this->selectedProductsList) > 0;
		if(count($this->availableProductsList) == 0) $this->availableProductsList = null;
	}

	private static function productInProduct($ShopId, $ProductId, $parentProduct){
		foreach($_SESSION['Index']->db->iterate("SELECT ProductId FROM mc_ProductsInProduct WHERE ParentProductId='$ProductId' AND (ShopId='$ShopId' OR ShopId='0')") as $row){
			if($row->ProductId == $parentProduct || ProductListUpdater::productInProduct($ShopId, $row->ProductId, $parentProduct))
				return true;
		}
		return false;
	}
}
?>