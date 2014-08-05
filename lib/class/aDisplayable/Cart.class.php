<?php

class Cart extends aDisplayable
{
	public function __construct(){
		$_SESSION['UserCart'] = array();
	}

	public function prepareDisplay(){
		$_SESSION['Index']->assign_say('CART_TITLE');
		if(isset($_GET['popup'])){
			$this->preparePopup();
		}
		else{
			$this->prepareSidebar();
		}
	}
	private function updateCart(){
		$removeFromCart = array();
		#region Warenkorb aktualisieren
		foreach($_SESSION['UserCart'] as $Id => $value){
			if(isset($_POST['amount'][$Id]) && !$value['OnlyOnce']){
				if(isNumber($_POST['amount'][$Id])){
					$_SESSION['UserCart'][$Id]['Points'] = $_POST['amount'][$Id] * $value['Points'] / $value['Amount'];
					$_SESSION['UserCart'][$Id]['Amount'] = $_POST['amount'][$Id];
				}
				elseif(!$_POST['amount'][$Id]){
					$removeFromCart[] = $Id;
				}
			}
		}
		if($_GET['remove']){
			unset($_SESSION['UserCart'][$_GET['remove']]);
		}
		foreach($removeFromCart as $value){
			unset($_SESSION['UserCart'][$value]);
		}
		#end
	}

	private function prepareCartContent($noChange){
		$displayCart = array();
		#region Ausgabe erzeugen
		$summe = 0;
		foreach($_SESSION['UserCart'] as $Id => $value){
			$displayCart[] = array(
					'Id' => $Id,
					'Label' => htmlspecialchars($value['Label']),
					'Amount' => $value['Amount'],
					'Image' => Item::getImagePath($value['Image'],1),
					'Points' => $value['Points'],
					'OnlyOnce' => $value['OnlyOnce']
				);
			$summe += $value['Points'];
		}
		#end

		if(count($displayCart)){
			$_SESSION['Index']->assign('NO_CHANGE', $noChange);
			$_SESSION['Index']->assign('CART_DATA', $displayCart);
			$_SESSION['Index']->assign_say('CART_UPDATE');
			$_SESSION['Index']->assign_say('CART_CHECKOUT');
		}
		else{
			$_SESSION['Index']->assign_say('CART_EMPTY');
		}
		$_SESSION['Index']->assign_say('BUYITEM_PRODUCTS_NAME_lABEL');
		$_SESSION['Index']->assign_say('BUYITEM_TOTAL_COSTS_LABEL');
		$_SESSION['Index']->assign('BUYITEM_TOTAL_COSTS', $summe);
	}

	private function prepareCart(){
		$this->updateCart();
		$this->prepareCartContent(false);

		if($_SESSION['Index']->user->isLoggedIn()){
			$_SESSION['Index']->assign_say('ACCOUNT_BONUSPOINTS');
			$_SESSION['Index']->assign_say('ACCOUNT_CURRENT');
			$_SESSION['Index']->assign('ACCOUNT_CURRENT_BONUSPOINTS', $_SESSION['Index']->user->bonusPoints());
			$_SESSION['Index']->assign('ACCOUNT_CURRENT_POINTS', $_SESSION['Index']->user->normalPoints());
		}
		// $_SESSION['Index']->assign('BUYITEM_AGREEMENT', $_SESSION['Index']->shop->getShopInfo()->BuyAgreement);
		// $_SESSION['Index']->assign('BUYITEM_AGREEMENT_ERROR', $agreement_error);

		$_SESSION['Index']->assign_say('BUYITEM_AGREEMENT_ACCEPT');
		$_SESSION['Index']->assign_say('BUYITEM_AGREEMENT_LABEL');
	
		$_SESSION['Index']->assign_say('AMOUNT');
	}
	private function prepareBuy(){
		if(!count($_SESSION['UserCart'])){
			$_SESSION['Index']->assign_say('CART_BOUGHT','CART_EMPTY');
			return 'NO_PRODUCTS';
		}
		elseif($_SESSION['Index']->user->isLoggedIn()){
			if($_POST['agree']){
				$_SESSION['Index']->assign_direct('CONFIRMED',true);
				$buyProducts = array();
				$buyAmounts = array();
				$buyCosts = array();
				foreach($_SESSION['UserCart'] as $id => $product){
					$buyProducts[] = $id;
					$buyAmounts[] = $product['Amount'];
					$buyCosts[] = $product['Points'];
				}
				if(is_array($buyResult = $_SESSION['Index']->user->BuyProduct($buyProducts, $buyAmounts, $buyCosts))){
					if(!$_SESSION['Index']->shop->getShopInfo("ServerOnline")){
						$_SESSION['Index']->assign_say('CART_BOUGHT', 'CART_BOUGHT_TRANSFER_ERRORS');
						return 'SERVER_OFFLINE';
					}
					$server = new JSONquery($_SESSION['Index']->shop->getId());
					$transferError = false;
					foreach($buyResult as $transferId){
						$result = $server->transferProduct($_SESSION['Index']->user->getLoginId(), $transferId);
						if($result == "CONNECTION_ERROR" || $result == "JSON_ERROR"){
							$transferError = true;
							break;
						}
						elseif($result == 'ITEM_DISABLED'){
							$transferError = true;
						}
					}
					if($transferError){
						$_SESSION['Index']->assign_say('CART_BOUGHT','CART_BOUGHT_TRANSFER_ERRORS');
						$_SESSION['Index']->shop->transferError();
					}
					else{
						$_SESSION['Index']->assign_say('CART_BOUGHT','CART_BOUGHT_AND_TRANSFERED');
						$_SESSION['Index']->shop->resetTransferError();
					}
				}
				else{
					$_SESSION['Index']->assign_direct('CART_ERROR',true);
					if($buyResult = 'NOT_ENOUGH_MONEY') $_SESSION['Index']->assign_say('CART_BOUGHT','CART_NOT_ENOUGH_MONEY');
					elseif($buyResult = 'INVALID_PRODUCT_ID') $_SESSION['Index']->assign_say('CART_BOUGHT','CART_INVALID_PRODUCT');
					elseif($buyResult = 'PRODUCT_DISABLED') $_SESSION['Index']->assign_say('CART_BOUGHT','CART_PRODUCT_DISABLED');
					else $_SESSION['Index']->assign_say('CART_BOUGHT','CART_ERROR');
				}
			}
			else{
				$_SESSION['Index']->assign_say('CHECKOUT_BACK_TO_CART');
				$_SESSION['Index']->assign_say('CART_BUY');
				$this->prepareCartContent(true);

				$agreement = $_SESSION['Index']->shop->getShopInfo()->BuyAgreement;
				if($agreement){
					$_SESSION['Index']->assign('BUYITEM_AGREEMENT', $agreement);
					$_SESSION['Index']->assign_say('BUYITEM_AGREEMENT_LABEL');
					$_SESSION['Index']->assign_say('BUYITEM_AGREEMENT_ACCEPT');
					if(isset($_GET['confirm'])){
						$_SESSION['Index']->assign_direct('BUYITEM_AGREEMENT_ERROR',true);
					}
				}
			}
		}
		else{
			$_SESSION['Index']->assign_say('CART_NEED_LOGIN');
			$_SESSION['Index']->assign_say('CART_LOGIN_NOW');
		}
	}
	private function prepareAfterAdd(){
		if($_SESSION['LastAddedProductError'] == 'PRODUCT_DISABLED'){
			$_SESSION['Index']->assign_say('CART_AFTER_ADD','CART_AFTER_ADD_DISABLED',array($_SESSION['LastAddedProduct']));
		}
		elseif($_SESSION['LastAddedProductError'] == 'PRODUCT_INVALID'){
			$_SESSION['Index']->assign_say('CART_AFTER_ADD','CART_AFTER_ADD_INVALID');
		}
		else{
			$_SESSION['Index']->assign_say('CART_AFTER_ADD',array($_SESSION['LastAddedProduct']));
		}
		$_SESSION['LastAddedProductError'] = '';
		$_SESSION['LastAddedProduct'] = '';
	}
	private function preparePopup(){
		$_SESSION['Index']->assign_direct('POPUP',true);
		if(isset($_GET['buy'])){
			$this->prepareBuy();
		}
		elseif(isset($_GET['afterAdd'])){
			$this->prepareAfterAdd();
		}
		else{
			$this->prepareCart();
		}
	}
	private function prepareSidebar(){
		if($_GET['add']){
			Cart::addToCart($_GET['add'], 1);
		}

		$displayCart = array();
		$summe = 0;
		foreach($_SESSION['UserCart'] as $value){
			$displayCart[] = array(
					'Label' => htmlspecialchars($value['Label']),
					'Amount' => $value['Amount'],
					'Image' => Item::getImagePath($value['Image'],1),
					'Points' => $value['Points']
				);
			$summe += $value['Points'];
		}
		if(!count($displayCart)) $_SESSION['Index']->assign_say('CART_EMPTY');
		else $_SESSION['Index']->assign_direct('CART_DATA', $displayCart);
		$_SESSION['Index']->assign_say('CART_SUM_LABEL');
		$_SESSION['Index']->assign_direct('CART_SUM',$summe);
		$_SESSION['Index']->assign_say('CART_SHOW_FULL');
		$_SESSION['Index']->assign_say('CART_SHOW_BUY');
	}

	public static function addToCart($ProdId, $Amount){
		$_SESSION['LastAddedProduct'] = '';
		$_SESSION['LastAddedProductError'] = '';
		if(isNumber($ProdId) && isNumber($Amount) && ($prodInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Label, Image, Points, Enabled, DisableDuringCooldown FROM mc_products WHERE Id='$ProdId' AND ShopId='{$_SESSION['Index']->shop->getId()}'")) && $prodInfo->Enabled){
			$_SESSION['LastAddedProduct'] = $prodInfo->Label;
			if($prodInfo->DisableDuringCooldown && ($_SESSION['UserCart'][$ProdId] || Item::CheckProductDisabled($ProdId, $_SESSION['Index']->user->getLoginId(), $_SESSION['Index']->shop->getId()))){
				$_SESSION['LastAddedProductError'] = 'PRODUCT_DISABLED';
				return 'PRODUCT_DISABLED';
			}
			if($_SESSION['UserCart'][$ProdId]){
				$_SESSION['UserCart'][$ProdId]['Amount'] += $Amount;
				$_SESSION['UserCart'][$ProdId]['Points'] = $prodInfo->Points * $_SESSION['UserCart'][$ProdId]['Amount'];
			}
			else{
				if($prodInfo->DisableDuringCooldown) $Amount = 1;
				$_SESSION['UserCart'][$ProdId] = array(
					'Label' => $prodInfo->Label,
					'Amount' => $Amount,
					'Image' => $prodInfo->Image,
					'Points' => $Amount * $prodInfo->Points,
					'OnlyOnce' => $prodInfo->DisableDuringCooldown
				);
			}
			return 'PRODUCT_ADDED';
		}
		$_SESSION['LastAddedProductError'] = 'PRODUCT_INVALID';
		return 'PRODUCT_INVALID';
	}
}

?>