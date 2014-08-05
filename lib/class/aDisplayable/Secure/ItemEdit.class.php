<?php
defined('_MCSHOP') or die("Security block!");

class ItemEdit extends aDisplayable
{
	public $ItemId = 0;

	private static function updateEnchList($ShopId, $selectedEnchesArray, $selectedEnchesStrength, &$newEnchId, &$newEnchStrength, $addButton, $deleteButton, &$fullEnchList){
		$selectedEnches = null;

		$fullEnchList = array();
		// foreach($_SESSION['Index']->db->iterate("SELECT Id, Name FROM mc_ench WHERE (ShopId='$ShopId' OR (ShopId='0' AND Id NOT IN (SELECT Id FROM mc_items WHERE ShopId='$ShopId'))) ORDER BY ShopId ASC, Name ASC") as $row){
		foreach($_SESSION['Index']->db->iterate("SELECT Id, Name FROM mc_ench WHERE ShopId='$ShopId' OR ShopId='0' ORDER BY Name ASC") as $row){
			$fullEnchList[$row->Id] = $row->Name;
		}

		//Vorhandene Items übernehmen
		if(is_array($selectedEnchesArray)){
			foreach($selectedEnchesArray as $key => $value)
			{
				//nur Items, die in der List drin sind und nicht gelöscht werden sollen, werden übernommen
				if(isset($fullEnchList[$key]) && !isset($deleteButton[$key])){
					// Rekursiv überprüfen, ob das Produkt das hinzuzufügende Produkt nicht bereits enthält
					$selectedEnches[] = array(
						'Id' => $key,
						'Name' => $fullEnchList[$key],
						'Amount' => $selectedEnchesStrength[$key]
					);
					unset($fullEnchList[$key]);
				}
			}
		}
		//Das neue Item ebenfalls mit hinzufügen
		if(isset($fullEnchList[$newEnchId]) && isset($addButton) && isNumber($newEnchStrength,1)){
			$selectedEnches[] = array(
				'Id' => $newEnchId,
				'Name' => $fullEnchList[$newEnchId],
				'Amount' => $newEnchStrength
			);
			unset($fullEnchList[$newEnchId]);
			$newEnchId = $newEnchStrength = null;
		}

		if(count($fullEnchList) == 0) $fullEnchList = null;
		return $selectedEnches;
	}

	public static function isValidData($ShopId, $ItemId, $itemEditName, $itemEditMineId, $itemEditValue, &$noName = false, &$invalidMineId = false, &$nameInUse = false){
		if(!$itemEditName){
			//Kein Name angegeben
			$noName = true;
		}
		elseif((!isNumber($itemEditMineId)) || ($itemEditValue && !isNumber($itemEditValue))){
			//Die Id ist keine Zahl
			$invalidMineId = true;
		}
		elseif($_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_items WHERE name='".mysql_real_escape_string($itemEditName)."' AND Id<>'{$ItemId}' AND ShopId='$ShopId' LIMIT 1")){
			//Der Name ist bereits vergeben
			$nameInUse = true;
		}
		else{
			return true;
		}
		return false;
	}

	public function prepareDisplay(){
		if(!isNumber($this->ItemId, 1)){
			$this->ItemId = -1;
			return;
		}
		$ShopId = $_SESSION['Index']->adminShop->getId();

		#region POST-Daten übernehmen
		//$Minecraftname = $_POST['testerName'];
		$Minecraftname = $_SESSION['Index']->db->fetchOne("SELECT Minecraftname FROM mc_customers INNER JOIN mc_shops ON mc_shops.CustomersId=mc_customers.Id WHERE mc_shops.Id='{$_SESSION['Index']->adminShop->getId()}' LIMIT 1");
		$itemEditSaveButton = $_POST['itemEditSaveButton'];
		$itemEditTestButton = $_POST['itemEditTestButton'];

		if(!isset($_POST['itemEditName'])){
			$itemInfo = $_SESSION['Index']->db->fetchOneRow("SELECT * FROM mc_items WHERE Id='{$this->ItemId}' AND ShopId='$ShopId' LIMIT 1");

			$itemEditName = $itemInfo->Name;
			$itemEditIngame = $itemInfo->Ingame;
			$itemEditMineId = $itemInfo->MineId;
			$itemEditValue = $itemInfo->Damage;
			$itemEditLore = $itemInfo->Lore;

			$selectedEnches = array();
			$selectedEnchesStrength = array();
			foreach($_SESSION['Index']->db->iterate("SELECT EnchId,Strength FROM mc_EnchInItem WHERE ShopId='$ShopId' AND ItemId='{$this->ItemId}'") as $row){
				$selectedEnches[$row->EnchId] = $row->EnchId;
				$selectedEnchesStrength[$row->EnchId] = $row->Strength;
			}
			$newEnchId = $newEnchStrength = null;
		}
		else{
			$itemEditName = $_POST['itemEditName'];
			$itemEditIngame = $_POST['itemEditIngame'];
			$itemEditMineId = $_POST['itemEditMineId'];
			$itemEditValue = $_POST['itemEditValue'];
			$itemEditLore = $_POST['itemEditLore'];
		
			$selectedEnches = $_POST['selectedEnches'];
			$selectedEnchesStrength = $_POST['selectedEnchesStrength'];
			$newEnchId = $_POST['newEnchId'];
			$newEnchStrength = $_POST['newEnchStrength'];
		}

		$enchAddButton = $_POST['enchAddButton'];
		$enchRemoveButton = $_POST['enchRemoveButton'];
		#end

		$selectedEnches = ItemEdit::updateEnchList($ShopId, $selectedEnches, $selectedEnchesStrength, $newEnchId, $newEnchStrength, $enchAddButton, $enchRemoveButton, $fullEnchList);

		if($itemEditTestButton){
			if(ItemEdit::isValidData($ShopId, $this->ItemId, $itemEditName, $itemEditMineId, $itemEditValue, $noName, $invalidMineId, $nameInUse)){
				$insertEnches = '';
				if(is_array($selectedEnches)){
					foreach($selectedEnches as $value){
						if(isNumber($value['Id']) && isNumber($value['Amount']) && $enchName = $_SESSION['Index']->db->fetchOne("SELECT Name FROM mc_ench WHERE (ShopId='0' OR ShopId='$ShopId') AND Id='".mysql_real_escape_string($value['Id'])."'")){
							if($insertEnches) $insertEnches .= ',';
							$insertEnches .= $enchName.':'.$value['Amount'];
						}
					}
				}
				$result = JSONquery::TransferItem($ShopId, $Minecraftname, $itemEditIngame, $itemEditMineId, $itemEditValue, $itemEditLore, $insertEnches);
				if($result === false){//Übertragungsfehler
					$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','TRANSFER_ERROR');
					$success = 2;
				}
				else{
					switch($result){
						case 'SUCCESS':
							$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','ITEM_TRANSFER_SUCCESS');
							$success = 1;
							break;
						case 'INVALID_ITEMID':
							$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','ITEM_TRANSFER_INVALID_ITEMID');
							$success = 3;
							break;
						case 'INVALID_AMOUNT':
							$success = 3;
							$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','ITEM_TRANSFER_INVALID_AMOUNT');
							break;
						case 'INVALID_DAMAGE_VALUE':
							$success = 3;
							$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','ITEM_TRANSFER_INVALID_DAMAGE_VALUE');
							break;
						case 'UNKNOWN_PLAYER':
							$success = 3;
							$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','ITEM_TRANSFER_UNKNOWN_PLAYER');
							break;
						case 'INVALID_ENCHANTMENTS':
							$success = 3;
							$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','ITEM_TRANSFER_INVALID_ENCHANTMENTS');
							break;
						case 'INVALID_PLAYERNAME':
							$success = 3;
							$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','ITEM_TRANSFER_INVALID_PLAYERNAME');
							break;
						default:
							$success = 3;
							$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TRANSFER_INFO','ITEM_TRANSFER_UNKNOWN_ERROR');
					}
				}
			}
			else{
				echo "Bitte korrigiere die Daten.";
			}
		}
		elseif($itemEditSaveButton){
			try{
				startTransaction("mc_items WRITE, mc_EnchInItem WRITE");
				#region Item in die DB schreiben
				if(ItemEdit::isValidData($ShopId, $this->ItemId, $itemEditName, $itemEditMineId, $itemEditValue, $noName, $invalidMineId, $nameInUse)){
					if(!$itemEditValue){
						$itemEditValue = 'null';
					}
					else{
						$itemEditValue = "'".mysql_real_escape_string($itemEditValue)."'";
					}

					if($this->ItemId == 0){//Neu erstellen
						$createNew = true;
						$this->ItemId = $_SESSION['Index']->db->insert("INSERT INTO mc_items (ShopId,Name,Ingame,MineId,Damage,Lore) VALUES ('$ShopId','".mysql_real_escape_string($itemEditName)."','".mysql_real_escape_string($itemEditIngame)."','$itemEditMineId',$itemEditValue,'".mysql_real_escape_string($itemEditLore)."')");
					}
					else{//altes aktualisieren
						$createNew = false;
						$_SESSION['Index']->db->insert("UPDATE mc_items SET Name='".mysql_real_escape_string($itemEditName)."',Ingame='".mysql_real_escape_string($itemEditIngame)."',MineId='$itemEditMineId',Damage=$itemEditValue,Lore='".mysql_real_escape_string($itemEditLore)."' WHERE ShopId='$ShopId' AND Id='{$this->ItemId}' LIMIT 1");
						$_SESSION['Index']->db->query("DELETE FROM mc_EnchInItem WHERE ShopId='$ShopId' AND ItemId='{$this->ItemId}'");
					}
					$insertEnches = '';
					if(is_array($selectedEnches)){
						foreach($selectedEnches as $value){
							if($insertEnches) $insertEnches .= ',';
							$insertEnches .= "('$ShopId','{$this->ItemId}','{$value['Id']}','{$value['Amount']}')";
						}
					}
					if($insertEnches) $_SESSION['Index']->db->insert('INSERT INTO mc_EnchInItem (ShopId,ItemId,EnchId,Strength) VALUES '.$insertEnches);
					commit();
					$_SESSION['Index']->assign_say(($createNew ? 'ADM_NEW_ITEM_SAVED' : 'ADM_EDIT_ITEM_SAVED'), array($itemEditName));
					$_SESSION['Index']->assign_direct('ADM_ITEM_EDIT_ID', $this->ItemId);
					$this->ItemId = -1;
					return;
				}
				rollback();
				#end

				#region Fehlerausgabe
				if($noName) $_SESSION['Index']->assign_say('ADM_ITEM_EDIT_INVALID_NAME','ADM_ITEM_EDIT_NO_NAME');
				elseif($nameInUse) $_SESSION['Index']->assign_say('ADM_ITEM_EDIT_INVALID_NAME','ADM_ITEM_EDIT_NAME_IN_USE');
				elseif($invalidMineId) $_SESSION['Index']->assign_say('ADM_ITEM_EDIT_INVALID_ID');
				#end
			}
			catch(Exception $e){
				rollback();
			}
		}


		$_SESSION['Index']->assign('ADM_ITEM_EDIT_ID', $this->ItemId);

		$_SESSION['Index']->assign('ADM_ITEM_EDIT_NAME_VALUE', $itemEditName);
		$_SESSION['Index']->assign('ADM_ITEM_EDIT_INGAME_VALUE', $itemEditIngame);
		$_SESSION['Index']->assign('ADM_ITEM_EDIT_ID_VALUE', $itemEditMineId);
		$_SESSION['Index']->assign('ADM_ITEM_EDIT_VALUE_VALUE', $itemEditValue);
		$_SESSION['Index']->assign('ADM_ITEM_EDIT_LORE_VALUE', $itemEditLore);

		$_SESSION['Index']->assign('ADM_ITEM_EDIT_SELECTED_ENCHES_VALUE', $selectedEnches);
		$_SESSION['Index']->assign('ADM_ITEM_EDIT_AVAILABLE_ENCHES_LIST', $fullEnchList);
		$_SESSION['Index']->assign('ADM_ITEM_EDIT_SELECTED_ENCH', $newEnchId);
		$_SESSION['Index']->assign('ADM_ITEM_EDIT_ENCH_STRENGTH', $newEnchStrength);

		if($this->ItemId){
			$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TITLE');
			$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_BUTTON');
		}
		else{
			$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TITLE','ADM_ITEM_CREATE_TITLE');
			$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_BUTTON','ADM_ITEM_CREATE_BUTTON');
		}
		$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_CANCEL');
		$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_TEST_BUTTON');

		$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_ENCHES_INFO');
		$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_NAME');
		$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_INGAME');
		$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_MINEID_VALUE');
		$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_LORE');
		$_SESSION['Index']->assign_say('ADM_ITEM_EDIT_ENCHES');

		$_SESSION['Index']->assign('ADM_ITEM_EDIT_TRANSFER_SUCCESS', $success);


		$this->ItemId = -1;
	}
}
?>