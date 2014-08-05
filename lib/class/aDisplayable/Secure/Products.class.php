<?php
defined('_MCSHOP') or die("Security block!");

class Products extends aDisplayable
{
	private $pagination;
	function __construct(){
		$this->pagination = new Pagination();
	}

	public function prepareDisplay()	{
		#region falls Suche
		if($_POST['search'])
			$search = $_POST['search'];
		if($_GET['search'])
			$search = urldecode($_GET['search']);
		if($search){
			$searchQuery = '';
			$parts = explode(' ', $search);
			foreach($parts as $part)
			{
				$searchQuery .= ' AND p.Label LIKE \'%'.mysql_real_escape_string($part).'%\'';
			}
			$searchPagination = '&search='.urlencode($search);
		}
		#end

		#region Seitenzahlen
		$this->pagination->prepare($_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_products AS p WHERE (p.ShopId='{$_SESSION['Index']->adminShop->getId()}'$searchQuery) ORDER BY p.Enabled DESC, p.Label ASC"), '?show=Products'.$searchPagination, $first, $number);
		#end

		#region Das Query zum ermitteln der aktuellen Produkte
		$query = "SELECT p.Id, p.Image, p.Label, p.Description, pg.Label AS GroupLabel, pg.Id AS GroupId, p.Points, p.Enabled FROM mc_products AS p LEFT JOIN mc_productGroups AS pg ON pg.Id=p.GroupId AND pg.ShopId='{$_SESSION['Index']->adminShop->getId()}' WHERE (p.ShopId='{$_SESSION['Index']->adminShop->getId()}'$searchQuery) ORDER BY p.Enabled DESC, p.Label ASC LIMIT $first,$number ";
		$queryShort = "SELECT Id,ShopId,Enabled,GroupId FROM mc_products WHERE (ShopId='{$_SESSION['Index']->adminShop->getId()}'$searchQuery) ORDER BY Enabled DESC, Label ASC LIMIT $first,$number";
		#end

		#region Itemstatus aktualisieren
		if(is_array($_POST['enabled']) && $_POST['submit'])
		{
			startTransaction("
				mc_products WRITE,
				mc_products AS p WRITE,
				mc_productGroups WRITE,
				mc_productGroups AS g WRITE,
				mc_ProductsInProduct WRITE,
				mc_ProductsInProduct AS iset WRITE");
			#region Werte übernehmen
			foreach($_SESSION['Index']->db->iterate($queryShort) as $row)
			{
				#region Produkt deaktivieren
				if($row->Enabled && !in_array($row->Id, $_POST['enabled']))
				{
					$_SESSION['Index']->db->query("UPDATE mc_products SET Enabled='0' WHERE Id='{$row->Id}' AND ShopId='{$_SESSION['Index']->adminShop->getId()}' LIMIT 1");
				}
				#end
				#region Produkt aktivieren
				elseif(!$row->Enabled && in_array($row->Id, $_POST['enabled']))
				{
					if($_SESSION['Index']->db->fetchOne("SELECT g.Id FROM mc_products AS p LEFT JOIN mc_productGroups AS g ON g.Id=p.GroupId WHERE p.Id='{$row->Id}' AND p.ShopId='{$_SESSION['Index']->adminShop->getId()}' AND g.ShopId='{$_SESSION['Index']->adminShop->getId()}' LIMIT 1"))
					{
						$_SESSION['Index']->db->query("UPDATE mc_products SET Enabled='1' WHERE Id='{$row->Id}' AND ShopId='{$_SESSION['Index']->adminShop->getId()}' LIMIT 1");
					}
					else
					{
						$ADM_PRODUCTS_SOME_NOT_ACTIVATED = true;
					}
				}
				#end
			}
			#end
			commit();
		}
		#end

		#region Items anzeigen
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_TITLE');
		$_SESSION['Index']->assign_say('ADM_PRODUCT_ADD_TITLE');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_IMAGE');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_LABEL');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_DESCRIPTION');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_POINTS');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_ENABLED');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_GROUP');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_EDIT');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_SUBMIT');

		#region Suche
		$_SESSION['Index']->assign('LAST_SEARCH', $search);
		$_SESSION['Index']->assign('ADM_SEARCH_URL', $searchPagination);
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_SEARCH_SUBMIT');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_SEARCH_INFO');
		#end

		if($ADM_PRODUCTS_SOME_NOT_ACTIVATED) $_SESSION['Index']->assign_say('ADM_PRODUCTS_SOME_NOT_ACTIVATED');

		$noGroup = null;
		$ADM_PRODUCTS_LIST = null;
		$showEnable = false;
		$showDisable = false;

		foreach($_SESSION['Index']->db->iterate($query) as $row){
			#region Wenn das Item in der Root-Gruppe steht, muss ein anderes Label ermittelt werden
			if($row->GroupId == 1)
			{
				$row->GroupLabel = $_SESSION['Index']->say('ADM_ITEM_GROUPS_TOP_NODE');
			}
			#end

			#region Falls gar kein GroupLabel existiert, das Item aber aktivert ist, wird der Sondertext ausgegeben
			//Sollte eigentlich nicht mehr auftreten
			$NoGroup = ($row->GroupLabel == null && $row->Enabled);
			if($NoGroup && $noGroup === null)
			{
				$noGroup = $_SESSION['Index']->say('ADM_PRODUCTS_NO_GROUP');
				$noGroupInfo = $_SESSION['Index']->say('ADM_PRODUCTS_NO_GROUP_INFO');
			}
			#end

			$enabled = ($_POST['enable_all_items'] ? true : ($_POST['disable_all_items'] ? false : $row->Enabled));
			$ADM_PRODUCTS_LIST[] = array(
				'Id' => $row->Id,
				'Image' => Item::getImagePath($row->Image, true),
				'Label' => $row->Label,
				'Description' => $row->Description,
				'GroupLabel' => ($NoGroup ? $noGroup : $row->GroupLabel),
				'NoGroupInfo' => ($NoGroup ? $noGroupInfo : null),
				'Points' => $row->Points,
				'Enabled' => $enabled);

			if($enabled)
				$showDisable = true;
			if(!$enabled)
				$showEnable = true;
		}

		$_SESSION['Index']->assign_direct('ADM_PRODUCTS_SHOW_ENABLE_ALL', $showEnable);
		$_SESSION['Index']->assign_direct('ADM_PRODUCTS_SHOW_DISABLE_ALL', $showDisable);

		$_SESSION['Index']->assign_say('ADM_PRODUCTS_ENABLE_ALL');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_ENABLE_ALL_SHORT');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_DISABLE_ALL');
		$_SESSION['Index']->assign_say('ADM_PRODUCTS_DISABLE_ALL_SHORT');
		#region (Nicht-)Leere Liste anzeigen
		if($ADM_PRODUCTS_LIST == null)
		{
			$_SESSION['Index']->assign_say('ADM_PRODUCTS_LIST_EMPTY');
		}
		else
		{
			$_SESSION['Index']->assign('ADM_PRODUCTS_LIST', $ADM_PRODUCTS_LIST);
		}
		#end
		#end
	}
}
?>