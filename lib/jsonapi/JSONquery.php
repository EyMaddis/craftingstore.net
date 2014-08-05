<?php
/************************
**
**	File:		jsonquery.php
**	Author:	Mathis Neumann
**	Date:		07/01/2012
**	Desc:		Make API calls to Minecraft Server via JSON
**
*************************/
defined("_MCSHOP") or die("Security Block!");

require_once(dirname(__FILE__).'/JSONAPI.php');

/*
INVALID_ITEMID
INVALID_AMOUNT
INVALID_DAMAGE_VALUE
UNKNOWN_PLAYER
INVALID_ENCHANTMENTS
INVALID_PLAYERNAME
SUCCESS
*/

class JSONquery extends JSONAPI
{
	private $shop, $shopInfo, $ShopId;
	public function __construct($ShopId, $host = null, $port = null, $uname = null, $pword = null, $salt = null){ //Parameter aus der Datenbank übernehmen
		if($ShopId){
			$this->ShopId = $ShopId;
			$this->shop = new Shop($ShopId, $host = null, $port = null, $uname = null, $pword = null, $salt = null);
			$this->shopInfo = $this->shop->getShopInfo();
			$host = $this->shopInfo->ServerHost;
			$port = $this->shopInfo->ServerPort;
			$uname = $this->shopInfo->ServerUser;
			$pword = $this->shopInfo->ServerPassword;
			$salt =  $this->shopInfo->ServerSalt;
		}
		//var_dump($this->shopInfo);
		parent::__construct($host, $port, $uname, $pword, $salt);
	}
	public static function sendValidation($host, $port, $uname, $pword, $salt, $player, $msg, $uuid){ // Stellt Verbindung mit dem Server her, ohne den Konstrukter mit ShopId zu verwenden. Für Verifizierung eines neuen Servers
		$server = new JSONquery(false, $host, $port, $uname, $pword, $salt);
		return $server->call("store.sendMessage", array($player, $msg, $uuid));
	}

	#region transferProduct($UserId, $TransferId)
	public function transferProduct($UserId, $TransferId){
		#region Eingabevalidierung
		if(!isNumber($UserId))
			return "USER_ID_NOT_VALID";
		if(!isNumber($TransferId))
			return "INVALID_ID";
		if(!($PlayerName = $this->shop->getMinecraftnameOfPlayerId($UserId)))
			return "USER_NOT_FOUND";
		#end

		$uuid = $_SESSION['Index']->db->getUUID();

		startTransaction("
			mc_inventory WRITE,
			mc_inventory AS i2 WRITE,
			mc_inventory AS i3 WRITE");
		$changed = $_SESSION['Index']->db->update("UPDATE mc_inventory SET Locked='$uuid' WHERE Id='$TransferId' AND ShopId='{$this->ShopId}' AND GamerId='$UserId' AND Locked='0' AND TransferTime is NULL LIMIT 1");

		if(!$changed){
			rollback();
			return "ITEM_NOT_FOUND";
		}

		$InventoryInfo = $_SESSION['Index']->db->fetchOneRow("SELECT ProductId, Amount, (SELECT i2.ProductId FROM mc_inventory AS i2 WHERE i2.ShopId='{$this->ShopId}' AND i2.GamerId='$UserId' AND i2.DisabledUntil>='".time()."' AND i2.ProductId = (SELECT i3.ProductId FROM mc_inventory AS i3 WHERE i3.Id='$TransferId' LIMIT 1) LIMIT 1) AS Disabled FROM mc_inventory WHERE Id='$TransferId' LIMIT 1");
		if($InventoryInfo->Disabled){
			rollback();
			return "ITEM_DISABLED";
		}
		commit();

		$calls = array('functions' => array(), 'params' => array());

		$disabledTime = $this->addProduct($calls, $PlayerName, $this->ShopId, $InventoryInfo->ProductId, $InventoryInfo->Amount, $uuid);

		try{
			$results = $this->call($calls['functions'], $calls['params']);
			if($jsonQuery->lastError){
				$_SESSION['Index']->db->update("UPDATE mc_inventory SET Locked='0', result='".mysql_real_escape_string(serialize($jsonQuery->lastError))."' WHERE Locked='$uuid' LIMIT 1");
				return 'JSON_ERROR';
			}
			elseif($results['success']){
				$_SESSION['Index']->db->update("UPDATE mc_inventory SET Locked='0', TransferTime='".time()."', DisabledUntil='$disabledTime', result='".mysql_real_escape_string(serialize($results))."' WHERE Locked='$uuid' LIMIT 1");
				return 'TRANSFERED';
			}
			else{
				$_SESSION['Index']->db->update("UPDATE mc_inventory SET Locked='0', result='".mysql_real_escape_string(serialize($results))."' WHERE Locked='$uuid' LIMIT 1");
				return 'CONNECTION_ERROR';
			}
		}catch(Exception $e){
			return 'UNKNOWN_ERROR';
		}
	}
	#end

	#region Command zu einem kompletten Produkt erstellen
	private function addProduct(&$calls, $PlayerName, $ShopId, $ProductId, $Amount, $uuid = null){
		$ProductInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Label,CustomCommand, NeedsPlayerOnline, CustomCommandEnd, CooldownNeedsPlayer, Cooldown, CooldownInterval, DisableDuringCooldown FROM mc_products WHERE Id='$ProductId' AND ShopId='$ShopId'");

		if($uuid != null){
			$this->addInfoMessage($calls, $uuid, $PlayerName, $ProductInfo->Label, $Amount);
		}

		//Items des Produkts hinzufügen
		$this->addItemsOfProduct($calls, $PlayerName, $ShopId, $ProductId, $Amount);

		//Enthaltene Produkte ebenfalls hinzufügen
		foreach($_SESSION['Index']->db->iterate("SELECT ProductId,Amount FROM mc_ProductsInProduct WHERE ShopId='$ShopId' AND ParentProductId='$ProductId'") as $products){
			$this->addProduct($calls, $PlayerName, $ShopId, $products->ProductId, $products->Amount);
		}

		//Commands hinzufügen
		$disabledTime = 0;
		if($ProductInfo->CustomCommand != null){
			$this->addCommandsParsed($calls, $ProductInfo->CustomCommand, $PlayerName, $Amount, $ProductInfo->NeedsPlayerOnline, 0);
		}
		if($ProductInfo->Cooldown != null && $ProductInfo->CustomCommandEnd != null){
			$disabledTime = Item::cooldownToSeconds($ProductInfo->Cooldown, $ProductInfo->CooldownInterval);
			$this->addCommandsParsed($calls, $ProductInfo->CustomCommandEnd, $PlayerName, $Amount, $ProductInfo->CooldownNeedsPlayer, $disabledTime / 60);
			if($ProductInfo->DisableDuringCooldown)
				return $disabledTime + time();
		}
		return 0;
	}
	#end

	private function addInfoMessage(&$calls, $uuid, $PlayerName, $ProductLabel, $Amount){
		$calls['functions'][] = 'store.sendMessage';
		$calls['params'][] = array($PlayerName, $_SESSION['Index']->lang->say('ITEM_TRANSFERED_INGAME', array($ProductLabel, $Amount)), $uuid);
	}

	#region Commands zu einem Produkt ermitteln
	private function addCommandsParsed(&$calls, $serializedCommands, $PlayerName, $Amount, $Online, $Time){
		#region Alle zugehörigen Kommandos verarbeiten
		foreach(unserialize($serializedCommands) as $call){
			$amountFound = JSONquery::setCommandVars($call, $PlayerName, $Amount);
			#region Command zu den calls hinzufügen
			#region json (zZ deaktiviert)
			/*if(($json = json_decode($call,1)) !== null){
				#region Die Anzahl wurde bereits eingerechnet
				if($amountFound){
					$calls['functions'][] = $json['function'];
					$calls['params'][] = $json['params'];
					//$calls['Id'][] = $Id;
				}
				#end
				#region Die Anzahl wurde noch nicht eingerechnet, die Funktion muss entsprechend oft aufgerufen werden
				else{
					for($i=0;$i<$Amount;$i++){
						$calls['functions'][] = $json['function'];
						$calls['params'][] = $json['params'];
						//$calls['Id'][] = $Id;
					}
				}
				#end
			}*/
			#end
			#region normale Kommandos
			// else{
			if($amountFound){
				$calls['functions'][] = 'store.executeCommand';
				$calls['params'][] = array(
						(string)$PlayerName,
						(string)$Online,
						(string)$call,
						(string)$Time
					);
			}
			else{
				for($i=0;$i<$Amount;$i++){
					$calls['functions'][] = 'store.executeCommand';
					$calls['params'][] = array(
						(string)$PlayerName,
						(string)$Online,
						(string)$call,
						(string)$Time
					);
				}
			}
			// }
			#end
			#end
		}
		#end
	}
	#end
	#region Items zu einem Produkt ermitteln
	private function addItemsOfProduct(&$calls, $PlayerName, $ShopId, $ProductId, $Amount){
		foreach($_SESSION['Index']->db->iterate("SELECT i.Id, i.Name, i.Ingame, i.MineId, i.Damage, i.Lore, iip.Amount
			FROM mc_ItemsInProduct AS iip
			JOIN mc_items AS i
			ON i.Id=iip.ItemId AND i.ShopId='$ShopId'
			WHERE iip.ShopId='$ShopId' AND iip.ProductId='$ProductId'") as $item){

			#region Enchantments ermitteln
			$enchantments = '';
			foreach($_SESSION['Index']->db->iterate("SELECT e.Name, eii.Strength
			FROM mc_EnchInItem AS eii
			JOIN mc_ench AS e
			ON e.Id=eii.EnchId AND (e.ShopId='0' OR e.ShopId='$ShopId')
			WHERE eii.ShopId='$ShopId' AND eii.ItemId='{$item->Id}'") as $enchantment){
				if($enchantments) $enchantments .= ',';
				$enchantments .= $enchantment->Name;
				if($enchantment->Strength != null) $enchantments .= ':'.$enchantment->Strength;
			}
			#end

			$calls['functions'][] = 'store.addItem';
			$calls['params'][] = array(
				(string)htmlspecialchars($PlayerName),
				(string)$item->MineId,
				(string)($Amount * $item->Amount),
				(string)($item->Damage != null ? $item->Damage : '0'),
				(string)htmlspecialchars($item->Ingame ? $item->Ingame : $item->Name),
				(string)JSONquery::nl2newline(htmlspecialchars($item->Lore)),
				(string)$enchantments
			);
		}
	}
	#end

	private static function nl2newline($text){
		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace("\r", "\n", $text);
		return str_replace("\n", '<newline>', $text);
	}
	private static function setCommandVars(&$call, $PlayerName, $Amount){
		//$pattern = '/(?<=")(%%AMOUNT%%|[0-9]+)( *[\+\-\*\/](%%AMOUNT%%|[0-9]+))*(?=")/'; # " werden berücksichtigt
		$pattern = '/([0-9]+ *[\+\-\*\/] *)*(%%AMOUNT%%)( *[\+\-\*\/] *[0-9]+)*/';
		#region %%PLAYER%% durch den Spielernamen ersetzen
		$call = str_replace('%%PLAYER%%', $PlayerName, $call);
		#end

		#region %%AMOUNT%% durch die Anzahl ersetzen, sofern es nur EIN mal vorkommt
		$amountFound = false;
		if(preg_match_all($pattern, $call, $matches) === 1){
			$split = preg_split($pattern, $call);
			$newValue = str_replace('%%AMOUNT%%', $Amount, $matches[0][0]);
			if(eval("\$newValue = $newValue;") === null){
				$call = $split[0].$newValue.$split[count($split)-1];
				//$call = preg_replace($pattern, $newValue, $call);
				$amountFound = true;
			}
		}
		#end

		return $amountFound;
	}

	#region public static function TransferCommand($ShopId, $Command, $Minecraftname, $Online)
	// Überträgt einen Befehl genau ein mal direkt an einen User.
	// Gibt false zurück, wenn die Übertragung nicht initiiert werden konnte, ansonsten einen String mit dem Ergebnis.
	public static function TransferCommand($ShopId, $Command, $Minecraftname, $Online){
		$jsonQuery = new JSONquery($ShopId);

		JSONquery::setCommandVars($Command, $Minecraftname, 1);
		$results = $jsonQuery->call('store.executeCommand', array((string)$Minecraftname,(string)$Online, (string)$Command, '0'));

		// URL-Fehler || JsonApi Fehler
		if($jsonQuery->lastError || $results['result'] != 'success')
			return false;
		return $results['success'];
	}
	#end

	#region TransferItem
	public static function TransferItem($ShopId, $Minecraftname, $itemEditName,
		$itemEditMineId, $itemEditValue, $itemEditLore, $enchantments){
		$jsonQuery = new JSONquery($ShopId);

		$results = $jsonQuery->call('store.addItem', array(
				(string)$Minecraftname,
				(string)$itemEditMineId,
				(string)'1',
				(string)$itemEditValue,
				(string)$itemEditName,
				(string)$itemEditLore,
				(string)$enchantments
			));

		// URL-Fehler || JsonApi Fehler
		if($jsonQuery->lastError || $results['result'] != 'success')
			return false;
		return $results['success'];
	}
	#end
}

?>