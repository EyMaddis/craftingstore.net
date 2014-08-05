<?php
defined('_MCSHOP') or die("Security block!");

class Itembox extends aContentBox
{
	#region public function prepareDisplay()
	public function prepareDisplay()
	{
		#region Inhalt aktualisieren
		$this->setProperties($_POST);
		#end

		#region Variablen Zuweisen
		$_SESSION['Index']->assign_direct('ITEMBOX_GROUP_ID', $this->groupId);
		$_SESSION['Index']->assign_direct('ITEMBOX_ITEM_NAVIGATION_DISABLED', $this->HideItemNavigation);

		$_SESSION['Index']->assign('ITEMBOX_CONTENT_ITEM_ARRAY', $this->itemList);
		
		$_SESSION['Index']->assign_say('ITEMBOX_TO_CART');
		$_SESSION['Index']->assign_say('ITEMBOX_TO_CART_NOW');

		$_SESSION['Index']->assign_say('ITEMBOX_BUY_COUNT');

		$_SESSION['Index']->assign_say('ITEMBOX_ITEM_DETAILS_LABEL');
		$_SESSION['Index']->assign_say('ITEMBOX_ITEM_DETAILS_BUTTON');

		$groupPath = null;
		foreach($_SESSION['Index']->nstree->getPath($this->groupId) as $row)
		{
			if($groupPath === null)
				$groupPath = array(array('Label' => $_SESSION['Index']->say('ITEMBOX_ROOT_NODE', false), 'BoxUrl' => '?show={Itembox}&groupId='.$row->Id));
			else
				$groupPath[] = array('Label' => $row->Label, 'BoxUrl' => '?show={Itembox}&groupId='.$row->Id);
		}
		#region letzte Gruppe löschen und ohne Link anzeigen
		if(count($groupPath) > 1){
			$_SESSION['Index']->assign_direct('ITEMBOX_CURRENT_GROUP', $groupPath[count($groupPath)-1]['Label']);
			unset($groupPath[count($groupPath)-1]);
		}
		#end
		else{
			$groupPath = null;
		}
		$_SESSION['Index']->assign('ITEMBOX_GROUP_PATH', $groupPath);
		
		$selected = ' selected="selected"';

		$_SESSION['Index']->assign_say('ITEMBOX_SORT_ALPHABET');
		$_SESSION['Index']->assign_say('ITEMBOX_SORT_PRICE');
		$_SESSION['Index']->assign_say('ITEMBOX_SORT_POPULARITY');
		$_SESSION['Index']->assign_direct('ITEMBOX_SORT_ALPHABET_SELECT', ($this->sortField == "name" ? $selected : ""));
		$_SESSION['Index']->assign_direct('ITEMBOX_SORT_PRICE_SELECT', ($this->sortField == "price" ? $selected : ""));
		$_SESSION['Index']->assign_direct('ITEMBOX_SORT_POPULARITY_SELECT', ($this->sortField == "popular" ? $selected : ""));

		$_SESSION['Index']->assign_say('SORT_ASCENDING');
		$_SESSION['Index']->assign_say('SORT_DESCENDING');
		$_SESSION['Index']->assign_direct('ITEMBOX_SORT_ASCENDING_SELECT', ($this->sortDirection == "asc" ? $selected : ""));
		$_SESSION['Index']->assign_direct('ITEMBOX_SORT_DESCENDING', ($this->sortDirection == "desc" ? $selected : ""));

		$_SESSION['Index']->assign_say('ITEMBOX_LIMIT_LABEL');
		$_SESSION['Index']->assign_direct('ITEMBOX_ITEMS_PER_PAGE', $this->itemsPerPage);


		$_SESSION['Index']->assign_say('ITEMBOX_NO_SUBGROUPS');
		$_SESSION['Index']->assign_direct('ITEMBOX_NO_SUBGROUPS_SELECT', ($this->noSubgroups ? ' checked="checked"' : ""));

		$_SESSION['Index']->assign_direct('ITEMBOX_TOTAL_PAGES', $this->totalPages);
		$_SESSION['Index']->assign_direct('ITEMBOX_CURRENT_PAGE', $this->currentPage);

		$_SESSION['Index']->assign_say('ITEMBOX_PAGE_LABEL');
		$_SESSION['Index']->assign_say('ITEMBOX_UPDATE');
		$_SESSION['Index']->assign_say('ITEMBOX_NO_ITEMS_FOUND');
		#end
	}
	#end

	#region definierbare Eigenschaften
	#Eigenschaften, die die Items definieren
	private $groupId = 1;
	private $currentPage = 0;
	private $sortField = "name";
	private $sortDirection = "asc";
	private $itemsPerPage = 9;
	private $noSubgroups = false;
	#Wenn gerade eine Suche stattfindet, wird hier die Sucheingabe gespeichert (bereits escaped)
	private $search = null;
	#end
	#region readonly-Eigenschaften
	#Enthält die Liste der zuletzt angegebenen Gruppe inklusive aller Untergruppen
	private $groupList = null;
	#Enthält die Liste der zuletzt ausgelesenen Items
	private $itemList = null;
	#Enthält die aktuelle Seitenanzahl
	private $totalPages = 0;
	#end

	#region Übernimmt ein assoziatives Array mit den neuen Werten für die privaten Eigenschaften
	public function setProperties($array)
	{
		#region Setzt die private Eigenschaft groupId
		if(isNumber($array['groupId']))
		{
			$this->groupId = $array['groupId'];
		}
		else if(isNumber($_GET['groupId']))#kann auch per GET übergeben werden, dann wird die Suche allerdings gelöscht
		{
			$this->groupId = $_GET['groupId'];
			$this->search = null;
		}
		#end

		if($array)
		{
			#region Es findet eine Suche statt
			if(array_key_exists('search', $array))
			{
				$this->search = mysql_real_escape_string($array['search']);

				if(isNumber($array['searchgroup'], true)) $this->groupId = $array['searchgroup'];
				else $this->groupId = null;
				#Standardwerte für eine Suche
				$this->currentPage = 0;
				$this->sortField = "popular";
				$this->sortDirection = 'desc';
			}
			#end
		
			if(array_key_exists('currentPage', $array) && isNumber($array['currentPage'], true))
			{
				$this->currentPage = $array['currentPage'];
			}
			if((array_key_exists('sortField', $array)) && (($array['sortField'] == "name") || ($array['sortField'] == "price") || ($array['sortField'] == "popular")))
			{
				$this->sortField = $array['sortField'];
			}
			if((array_key_exists('sortDirection', $array)) && (($array['sortDirection'] == "asc") || ($array['sortDirection'] == "desc")))
			{
				$this->sortDirection = $array['sortDirection'];
			}
			if((array_key_exists('itemsPerPage', $array)) && (($array['itemsPerPage'] == "-1") || (isNumber($array['itemsPerPage']) && ($array['itemsPerPage'] >= 3))))
			{
				$this->itemsPerPage = $array['itemsPerPage'];
			}
			if($array['noSubgroups'])
			{
				$this->noSubgroups = true;
			}
			else
			{
				$this->noSubgroups = false;
			}
		}

		#region Aktualisiert die gesamte Itemliste
		$this->refreshGroupList();
		$this->refreshItemlist();
		#end
	}
	#end
	#region Gibt das Datenbankfeld zurück, dass dem aktuellen Sortierkriterium entspricht
	private function getsortFieldSql()
	{
		if($this->sortField == "price")
		{
			return "i.Points";
		}
		elseif($this->sortField == "popular")
		{
			return "i.BuyCounter";
		}
		else
		{
			return "i.Label";
		}
	}
	#end

	#region Aktualisiert die private Eigenschaft groupList anhand der aktuellen groupId
	private function refreshGroupList()
	{
		if(!$this->groupId)
		{
			$this->groupList = array();
			return; #Abbrechen, falls mit der Suche die Gruppenbeschränkung deaktiviert wurde
		}

		$gruppen = array($this->groupId);
		if(!$this->noSubgroups)
		{
			//Zuerst die Untergruppen auslesen
			$tree = $_SESSION['Index']->nstree->getSubTree($this->groupId);
			if($tree)
			{
				foreach($tree as $row)
				{
					$gruppen[] = $row->Id;
				}
			}
		}
		$this->groupList = $gruppen;
	}
	#end
	#region Ermittelt alle Items und Itemsets, die den aktuell definierten Eigenschaften entsprechen
	private function refreshItemlist()
	{
		#region Im Falle einer Suche, wird der Suchstring zerlegt und die Bedingung daraus gebaut
		$searchQuery = '';
		if($this->search)
		{
			$split = explode(' ', $this->search);
			foreach($split as $part)
			{
				$searchQuery .= ' AND i.Label LIKE \'%'.$part.'%\'';
			}
		}
		#end

		#region Alle Gruppen zu einem WHERE-Statement für ein MySQL-Statement zusammenbauen
		$whereGroups = '';
		foreach($this->groupList as $value)
		{
			if($whereGroups)
				$whereGroups .= ',';
			$whereGroups .= '\''.$value.'\'';
		}
		if($whereGroups)
		{
			$whereGroups = ' AND i.GroupId IN ('.$whereGroups.')';
		}
		#end

		#region Anzahl der Items ermitteln
		$itemCount = $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_products As i WHERE i.Enabled='1' AND i.ShopId='{$_SESSION['Index']->shop->getId()}'$whereGroups$searchQuery");

		$this->totalPages = ceil($itemCount/$this->itemsPerPage);
		if(($this->currentPage >= $this->totalPages) && ($this->totalPages > 0))
		{
			$this->currentPage = $this->totalPages - 1;
		}
		#end

		#region Wenn mehr als 0 Items vorhanden sind, dann diese ermitteln und zurückgeben
		if($itemCount > 0)
		{
			$this->itemList = array();
			//Jetzt die Items, die direkt in dieser Gruppe sind, auslesen
			if($this->itemsPerPage != -1)
			{
				$Limit = " Limit ".($this->currentPage * $this->itemsPerPage).", ".$this->itemsPerPage;
			}
			foreach($_SESSION['Index']->db->iterate("SELECT i.*, g.Label AS GroupLabel FROM mc_products AS i LEFT JOIN mc_productGroups AS g ON i.GroupId=g.Id WHERE i.Enabled='1' AND i.ShopId='{$_SESSION['Index']->shop->getId()}' AND g.ShopId='{$_SESSION['Index']->shop->getId()}'$whereGroups$searchQuery ORDER BY ".$this->getsortFieldSql()." ".$this->sortDirection.$Limit) as $row)
			{
				$this->itemList[] = array(
					'Id' => $row->Id,
					'Label' => $row->Label,
					'GroupLabel' => ($row->GroupId == 1 ? $_SESSION['Index']->say('ADM_ITEM_GROUPS_TOP_NODE') : $row->GroupLabel),
					'GroupLink' => '?show={Itembox}&groupId='.$row->GroupId,
					'ToCartUrl' => '?show=Cart&add='.$row->Id,
					'DetailsUrl' => '?show=Itemdetails&id='.$row->Id.($row->HasSetItems ? '&h=400' : ''),
					'Popularity' => $row->BuyCounter,
					'Points' => $row->Points,
					'Image' => Item::getImagePath($row->Image),
					'Pricetag' => 0);
			}
			return;
		}
		#end
		#region Es wurden keine Items gefunden: Liste auf null setzen
		$this->itemList = null;
		#end
	}
	#end
}
?>