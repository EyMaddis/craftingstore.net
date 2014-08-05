<?php
/************************
**
** File:	 	Item.class.php
**	Author: 	Mathis Neumann
**	Date:		12/01/2012
**	Desc:		class to fetch information about the an item or itemset
**
*************************/

defined('_MCSHOP') or die("Security block!");

// includes every information of an item or itemset based on an id in the database
class Item
{
	private $itemID;
	private $shopID;

	private $itemInfo = null;
	public function getItemInfo(){
		// Gets all information from row with itemID - returns an array with all field
		if($this->itemInfo == null) // check if updateItemInfo() was used before
		{
			$this->updateItemInfo();
		}
		return $this->itemInfo;
	}

	#region public static function getItemInfoById($ItemId, &$ConnectedItems)
	//$ItemId ist die Id des Items, zu dem Informationen ermittelt werden sollen
	//wenn $ConnectedProducts gesetzt ist, wird über diesen Wert bei Itemsets zurückgegeben, welche Items sich in dem Set befinden, bei normalen Items wird angegeben, in welchen Sets sie sich befinden
	public static function getItemInfoById($ItemId, &$ConnectedProducts){
		#region fehlerhafte ItemId
		$ConnectedProducts = null;
		if(!isNumber($ItemId)){
			return null;
		}
		#end

		#region Informationen zum Item ermitteln
		$ShopId = $_SESSION['Index']->shop->getId();
		if(!$itemInfo = $_SESSION['Index']->db->fetchOneRow("SELECT * FROM mc_products WHERE Id='$ItemId' AND ShopId='$ShopId'")){
			return null;
		}
		#end
		#region Informationen zu verknüpften Produkten ermitteln
		$ConnectedProducts = array();
		foreach($_SESSION['Index']->db->iterate("SELECT p.Label, pip.Amount, p.Image FROM mc_ProductsInProduct AS pip LEFT JOIN mc_products AS p ON p.Id=pip.ProductId AND p.ShopId='$ShopId' WHERE pip.ParentProductId='$ItemId' AND pip.ShopId='$ShopId'") as $row)
		{
			$ConnectedProducts[] = array('Name' => $row->Label, 'Amount' => $row->Amount, 'Image' => Item::getImagePath($row->Image));
		}
		#end
		#region Informationen zu verknüpften Items ermitteln
		foreach($_SESSION['Index']->db->iterate("SELECT i.Name, iip.Amount, i.Image FROM mc_ItemsInProduct AS iip LEFT JOIN mc_items AS i ON i.Id=iip.ItemId AND i.ShopId='$ShopId' WHERE iip.ProductId='$ItemId' AND iip.ShopId='$ShopId'") as $row)
		{
			$ConnectedProducts[] = array('Name' => $row->Name, 'Amount' => $row->Amount, 'Image' => Item::getImagePath($row->Image));
		}
		#end
		return $itemInfo;
	}
	#end

	public function __construct(){
	}

	public function setItemID($itemID){
		// Sets the itemID which is necessery for the following methods
		if (is_numeric($sID) && is_natural($sID))
		{
			$this->itemID = $itemID;
			return true;
		}
		else
		{
			setError("ItemID is not an integer!", __FILE__,__LINE__);
			return false;
		}
		
	}
	public function getItemID(){
		// return the Item ID
		if(!isset($this->itemID))
		{
			setError("No itemID found!", __FILE__, __LINE__);
			return false;
		}
		else
		{
			return $this->itemID;
		}
	}

	public function updateItemInfo(){
		// The item information are saved in the session to reduce traffic
		// just needed if getItemInfo was not parsed before.
		if(!isset($this->itemID))
		{
			setError("No item selected!", __FILE__, __LINE__);
		}
		else
		{
			$this->itemInfo = $_SESSION['Index']->db->fetchOneRow("SELECT * FROM mc_products WHERE Id='{$this->itemID}'");
		}
	}

	public static function CreateItem(){
	}

	//Rechnet den Cooldown in Sekunden um
	public static function cooldownToSeconds($Cooldown,$Interval){
		switch($Interval)
		{
			case 'i'://Minute
				return $Cooldown*60;
			case 'h'://Stunde
				return $Cooldown*3600;
			case 'd'://Tag
				return $Cooldown*86400;
			case 'w'://Woche
				return $Cooldown*604800;
			case 'm'://Monat
				return mktime(date('H'),date('i'),date('s'),date("n")+$Cooldown)-time();
			default:
				return null;
		}
	}
	//Ermittelt den Ablaufzeitpunkt des Cooldowns
	public static function cooldownEnd($Cooldown, $Interval, $startTime){
		switch($Interval)
		{
			case 'i'://Minute
				return $Cooldown*60+$startTime;
			case 'h'://Stunde
				return $Cooldown*3600+$startTime;
			case 'd'://Tag
				return $Cooldown*86400+$startTime;
			case 'w'://Woche
				return $Cooldown*604800+$startTime;
			case 'm'://Monat
				return mktime(date('H',$startTime),date('i',$startTime),date('s',$startTime),date("n",$startTime)+$Cooldown);
			default:
				return null;
		}
	}

	//Prüft ob ein Produkt zur Zeit im Scheduler ist und der Cooldown noch nicht abgelaufen ist
	public static function ProductCooldownPending($ProductId, $UserId, $ShopId){
		$ExpiredItem = $_SESSION['Index']->db->fetchOneRow("
		SELECT ExecutionTime,DisabledUntil,Cooldown
		FROM mc_transfers
		WHERE ShopId='$ShopId' AND UserId='$UserId' AND ProductId='$ProductId'
		ORDER BY ExecutionTime is null DESC, ExecutionTime DESC LIMIT 1");
		if(($ExpiredItem->Cooldown > 0) && (!$ExpiredItem->ExecutionTime || time() < $ExpiredItem->DisabledUntil)){
			return true;
		}
		return false;
	}

	public static function CheckProductDisabled($ProdId, $GamerId, $ShopId){
		if($disabled = $_SESSION['Index']->db->fetchOne("SELECT DisabledUntil FROM mc_inventory WHERE ShopId='$ShopId' AND GamerId='$GamerId' AND ProductId='$ProdId' ORDER BY DisabledUntil DESC LIMIT 1")){
			// Prüfen ob der Cooldown tatsächlich abgelaufen ist. Dazu muss er auch begonnen haben.
			return time() < $disabled;
		}
		return false;
	}

	#region public static function CheckLabelAllowed($ShopId, $label, $id = '-1')
	/* Prüft, ob das angegebene Label verwendet werden darf. Beim erstellen von neuen Items braucht $id nicht angegeben zu werden */
	public static function CheckLabelAllowed($ShopId, $label, $id = '-1')
	{
		#region Label hat ungültiges Format
		if((strlen($label) < 1) || (strlen($label) > 45))
			return false;
		#end

		#region Label bereits vergeben
		foreach($_SESSION['Index']->db->iterate(Item::GetItemQuery($ShopId)) as $row)
		{
			if(($row->Id != $id) && ($row->Label == $_POST['label']))
			{
				return false;
			}
		}
		#end

		return true;
	}
	#end

	#region public static function CheckDescriptionAllowed($description)
	/* Prüft, ob die angegebene Beschreibung verwendet werden darf. */
	public static function CheckDescriptionAllowed($description)
	{
		return (strlen($_POST['shortdescription']) < 65535);
	}
	#end

	#region public static function CheckShortDescriptionAllowed($shortdescription)
	/* Prüft, ob die angegebene Kurzbeschreibung verwendet werden darf. */
	public static function CheckShortDescriptionAllowed($shortdescription)
	{
		return (strlen($shortdescription) < 140);
	}
	#end

	#region public static function CheckPointsAllowed($points)
	/* Prüft, ob die angegebenen Punkte verwendet werden dürfen. */
	public static function CheckPointsAllowed($points){
		return isNumber($points, true);
	}
	#end

	#region public static function CheckMenueAllowed($ShopId, $menu)
	/* Prüft, ob das angegebene Menü verwendet werden darf. */
	public static function CheckMenueAllowed($ShopId, $menu){
		//$_POST['group'] muss eine Zahl sein und es muss in mc_productGroups eine Zeile mit der Id und der ShopId geben
		if(!isNumber($ShopId) || !isNumber($menu, true) || ($menu != $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_productGroups WHERE Id='$menu' AND ShopId='$ShopId'")))
			return false;
		return true;
	}
	#end

	#region public static function CheckItemSetItemsAllowed($ShopId, &$itemAmount, $itemId)
	/* Prüft, ob die übergebenen Werte als Items in einem Set verwendet werden dürfen.
	 * Der Parameter $checkedAmount enthält nach dem Funktionsaufruf ein Array mit folgendem Format:
	 * ItemId -> array(  //ItemId ist die Id des Items aus der Datenbank
			'Amount' => Amount,  //Amount ist der eingegebene Wert
			'Error' => Error)  true, wenn der Wert fehlerhaft ist
	 * Wenn mindestens ein fehlerhafter Wert übergeben wurde, wird false zurückgegeben, ansonsten true
	 */
	public static function CheckItemSetItemsAllowed($ShopId, $itemAmount, $itemId, &$checkedAmount){
		if(!isNumber($ShopId,1))
			return false;

		#region Die maximal erlaube Anzahl ermitteln; gegen flooding
		$max = $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_products WHERE ShopId='0' OR ShopId='$ShopId'");
		#end

		#region Anzahl der übergebenen Werte ermitteln
		$count_itemId = count($itemId);
		$count_itemAmount = count($itemAmount);
		#end

		#region Array-Größen überprüfen
		if(!is_array($itemAmount) || !is_array($itemId) || ($count_itemId != $count_itemAmount) || ($count_itemAmount > $max))
		{
			return false;
		}
		#end

		#region Prüfen, ob in den Arrays korrekte Werte stehen
		$checkedAmount = null;
		$korrekt = true;
		$alle_null = true;
		for($i=0; $i<$count_itemId; $i++)
		{
			$cur_id = $itemId[$i];
			#region Id hat gültiges Format
			#region Ja
			if(isNumber($cur_id))
			{
				#region Die Id existiert
				if($_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_products WHERE Id='$cur_id' AND (ShopId='$ShopId' OR ShopId='0')") > 0)#Suche nach der Id in den ShopItems bzw. bei den Default-Items
				{
					$cur_amount = trim($itemAmount[$i]);
					if($cur_amount) $alle_null = false;
					#region Die angegebene Anzahl hat gültiges Format
					#region Ja
					if(isNumber($cur_amount))
					{
						$checkedAmount[$cur_id] = array('Amount' => (int)$cur_amount, 'Error' => false);
					}
					elseif(isNumber($cur_amount, true) || $cur_amount == '')
					{
						continue; //Leere Mengenwerte werden übersprungen und dadurch gelöscht
					}
					#end
					#region Nein
					else
					{
						$checkedAmount[$cur_id] = array('Amount' => $cur_amount, 'Error' => true);
						$korrekt = false;
					}
					#end
					#end
				}
				#end
				#region Die Id ist ungültig
				else
				{
					$korrekt = false;
				}
				#end
			}
			#end
			#region Nein
			else
			{
				$korrekt = false;
			}
			#end
			#end
		}
		#end
		if($alle_null)
			return 2;
		if($korrekt)
			return 0;
		return 1;
	}
	#end

	#region public static function CheckCustomCommandAllowed($command)
	/* Prüft, ob die angegebenen Punkte verwendet werden dürfen. */
	public static function CheckCustomCommandAllowed($command){
		return ((strlen($command) > 0) && ($command[0] != '/'));
	}
	#end

	#region public static function GetItemName($ShopId, $ItemId)
	//Ermittelt zu einer ItemId das Label
	public static function GetItemName($ShopId, $ItemId){
		if(!isNumber($ItemId) || !isNumber($ShopId))
			return null;
		return $_SESSION['Index']->db->fetchOne("SELECT Label FROM mc_products WHERE Id='$ItemId' AND ShopId='$ShopId'");
	}
	#end

	#region GetItemQuery
	public static function GetItemQuery($ShopId, $onlyEnabled = false)
	{
		$enabled = '';
		if($onlyEnabled) {$enabled = "AND Enabled='1'";}

		$sql = "SELECT * FROM mc_products AS i WHERE (ShopId='$ShopId'$enabled)";

		if(!$onlyEnabled)
		{
			$sql .= " OR (ShopId='0' AND Id NOT IN (SELECT Id FROM mc_products WHERE ShopId='$ShopId'))";
		}
		$sql .= " ORDER BY i.Enabled DESC, i.Label ASC";
		return $sql;
	}
	#end

	#region getImagePath($Image)
	public static function getImagePath($Image, $Preview = false){
		$path = $_SESSION['Index']->relImgPath.($Preview ? 'preview/' : '');
		if($Image == null)
		{
			$return = $path.$_SESSION['Index']->default_img;
		}
		else
		{
			$return = $path.$Image;
#region NUR TEMPORÄR
			if(!file_exists($return))
			{
				$return = $path.$_SESSION['Index']->default_img;
			}
#end
		}
		return $return;
	}
	#end
}
?>