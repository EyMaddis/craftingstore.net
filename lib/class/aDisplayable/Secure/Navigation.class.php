<?php
defined('_MCSHOP') or die("Security block!");

class Navigation extends aDisplayable
{
#region Menüs
	private static $allowedShowValues = array(
			'RegisteredPlayers',
			'Statistics',
			'Products',
			'ItemGroups',
			'Content',
			'ServerSettings');
	private static $adminNavigation = array(
			array('target' => 'RegisteredPlayers',
				'img' => 'icon_user.png',
				'label' => 'ADM_HEADER_NAVIGATION_REGISTEREDPLAYERS',
				'checked' => array('RegisteredPlayers')),
			array('target' => 'Statistics',
				'img' => 'icon_statistics.png',
				'label' => 'ADM_HEADER_NAVIGATION_STATISTICS',
				'checked' => array('Statistics')),
			array('target' => 'Products',
				'img' => 'icon_pricetag.png',
				'label' => 'ADM_HEADER_NAVIGATION_PRODUCTS',
				'checked' => array('Products','ProductEdit')),
			array('target' => 'ItemGroups',
				'img' => 'icon_pricetag_group.png',
				'label' => 'ADM_HEADER_NAVIGATION_ITEMGROUPS',
				'checked' => array('ItemGroups')),
			array('target' => 'Content',
				'img' => 'icon_texts.png',
				'label' => 'ADM_HEADER_NAVIGATION_CONTENT',
				'checked' => array('Content')),
			array('target' => 'ServerSettings',
				'img' => 'icon_db.png',
				'label' => 'ADM_HEADER_NAVIGATION_SERVERSETTINGS',
				'checked' => array('ServerSettings')),
		);
#end
	public function __construct(){
	}

	public function prepareDisplay(){
		#region falls die Session abgelaufen ist, auf die Hauptseite umleiten
		if(!isset($_SESSION['Index']->adminShop))
			setLocation('?show=LoginServer');
		#end

		$_SESSION['Index']->assign('AdminNavigationPrepared', true);
		$_SESSION['Index']->assign_say('ADM_STRUCTURE_SWITCHSHOP');
		$_SESSION['Index']->assign_say('CANCEL');

		$this->switchShop();
		$this->prepareShopSelection();
		$this->prepareShopLogo();

		$_SESSION['Index']->assign('NAVIGATION_SHOP_URL', 'http://'.$_SESSION['Index']->adminShop->getShopInfo()->Subdomain.'.'.BASE_DOMAIN);
		$_SESSION['Index']->assign_say('NAVIGATION_SHOP_TITLE');

		$_SESSION['Index']->assign('ADM_HEADER_NAVIGATION_ACTIVE', $_GET['show']);

		$_SESSION['Index']->assign_say('ADM_PROFILE_TITLE');

		$this->prepareInfobox();
		$this->prepareMenu();
		$this->prepareAccount();
		$this->prepareFooter();
	}

	private function prepareInfobox(){
		$_SESSION['Index']->assign_say('LOGIN_LOGOFF_LABEL');

		if(!$_SESSION['Index']->adminShop->getShopInfo('ServerOnline')){
			$_SESSION['Index']->assign_say('SHOP_OFFLINE_ERROR');
			$_SESSION['Index']->assign_say('SHOP_OFFLINE_ERROR_INFO');
		}
	}

	private function switchShop(){
		if(isNumber($_POST['NAVIGATION_CHANGE_SERVER']))
		{
			foreach($_SESSION['Index']->db->iterate("SELECT Id FROM mc_shops WHERE CustomersId='{$_SESSION['CustomerId']}'") as $row)
			{
				if($_POST['NAVIGATION_CHANGE_SERVER'] == $row->Id)
				{
					$_SESSION['Index']->adminShop = new Shop($row->Id);
					// Aktuelle Shopauswahl in der Datenbank speichern
					$_SESSION['Index']->db->query("UPDATE mc_customers SET LastShopId='{$row->Id}' WHERE Id='{$_SESSION['CustomerId']}' LIMIT 1");
					break;
				}
			}
		}
	}

	private function prepareShopSelection(){
		$Shops = array();
		foreach($_SESSION['Index']->db->iterate("SELECT Id, Label, Subdomain, Domain FROM mc_shops WHERE CustomersId='{$_SESSION['CustomerId']}'") as $row)
		{
			$Shops[] = array(
				'Id' => $row->Id,
				'Label' => $row->Label,
				'Subdomain' => ($row->Domain ? $row->Domain : $row->Subdomain.'.'.BASE_DOMAIN),
				'Selected' => ($_SESSION['Index']->adminShop->getId() == $row->Id)
			);
		}
		$_SESSION['Index']->assign('STRUCTURE_SHOP_LIST', $Shops);

		if(count($Shops) > 1)
		{
			if(in_array($_GET['show'], Navigation::$allowedShowValues))
			{
				$_SESSION['ShopChangeTarget'] = $_GET['show'];
			}

			$_SESSION['Index']->assign_direct('STRUCTURE_MULTIPLE_SHOPS_TARGET_PAGE', $_SESSION['ShopChangeTarget']);
			$_SESSION['Index']->assign_direct('STRUCTURE_MULTIPLE_SHOPS', true);
		}
	}

	private function prepareShopLogo(){
		$logo = $_SESSION['Index']->adminShop->getShopInfo()->ShopLogo;
		$subdomain = $_SESSION['Index']->adminShop->getShopInfo()->Subdomain;
	
		if (empty($logo))
		{
			$shop_image = "./images/logo.png";
		}
		else
		{
			$shop_image = "./images/shops/".$subdomain."/".$logo;
		}
		$_SESSION['Index']->assign_direct('ADM_STRUCTURE_LOGO', $shop_image);
	}

	private function prepareMenu(){
		$menuList = array();
		foreach(Navigation::$adminNavigation as $menuItem)
		{
			$i = count($menuList);
			$menuList[$i] = $menuItem; //Kopieren
			$menuList[$i]['checked'] = in_array($_GET['show'], $menuItem['checked']); //Checked-Wert setzen
			$menuList[$i]['label'] = $_SESSION['Index']->lang->say($menuItem['label']); //Label aus Sprache ermitteln
		}
		$_SESSION['Index']->assign('ADM_NAVIGATION',$menuList);
	}

	private function prepareAccount(){
		$_SESSION['Index']->assign_say('ADM_ACCOUNT_LABEL');
		$_SESSION['Index']->assign('ADM_ACCOUNT', str_replace('.', $_SESSION['Index']->say('COMMA'), sprintf('%01.2f', $_SESSION['Index']->db->fetchOne("SELECT Current FROM mc_customeraccounts WHERE CustomersId='{$_SESSION['CustomerId']}' ORDER BY Time DESC LIMIT 1")/100)));
	}

	private function prepareFooter(){
		$_SESSION['Index']->assign_say('IMPRINT');
		$_SESSION['Index']->assign_say('TERMS_AND_CONDITIONS');
		$_SESSION['Index']->assign_say('COMPANY',COMPANY);
		$_SESSION['Index']->assign_say('PROJECTNAME',PROJECTNAME);
		$_SESSION['Index']->assign_say('FOOTER_VERSION',VERSION);
	}
	
}
?>