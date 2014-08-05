<?php
defined('_MCSHOP') or die("Security block!");

class Header extends aDisplayable{
	public function prepareDisplay(){
		//get Current shop ID
		$currShop = $_SESSION['Index']->shop->getShopInfo()->Id;
		
		#region load logo
		$sql = "SELECT ShopLogo, ShopLogoWidth, ShopLogoHeight FROM mc_shops WHERE Id = '".$currShop."'";
		$logo = $_SESSION['Index']->shop->getShopInfo()->ShopLogo;
		$subdomain = $_SESSION['Index']->shop->getShopInfo()->Subdomain;
		if ($logo == "")
		{
			$logo = "logo.png";
			$width = 428;
			$height = 100;
			$shop_image_dir = "./images/";
		}
		else
		{
			$width = $_SESSION['Index']->shop->getShopInfo()->ShopLogoWidth;
			if($width > 0)
				$width = ' width="'.$width.'px"';
			else
				$width = ' width="auto"';
			
			$height = $_SESSION['Index']->shop->getShopInfo()->ShopLogoHeight;
			if($height > 0)
				$height = ' height="'.$height.'px"';
			else
				$height = ' height="auto"';
		$shop_image_dir = "./images/shops/".$subdomain."/";
		}
		#endregion

		$_SESSION['Index']->assign('SHOP_LOGO', $shop_image_dir.$logo);
		$_SESSION['Index']->assign_direct('SHOP_LOGO_HEIGHT', $width);
		$_SESSION['Index']->assign_direct('SHOP_LOGO_WIDTH', $height);
		
		#region Loading Links
		$this->links = array();
		$sql = "SELECT Name, Link, Target FROM mc_topmenu WHERE ShopId='$currShop' ORDER BY Position";
		foreach($_SESSION['Index']->db->iterate($sql) as $row){
			$this->links[] = array('name' => $row->Name,
				'link' => $row->Link,
				'target' => $row->Target);
		}
		$_SESSION['Index']->assign('TOPMENU_LINKS', $this->links);
		#endregion
	}
}
?>