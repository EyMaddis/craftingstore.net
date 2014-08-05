<?php
defined('_MCSHOP') or die("Security block!");
// die(getcwd());

class ProductEdit extends aDisplayable
{
	private $nstree = null;
	public function __construct(){
		$this->nstree = $_SESSION['Index']->createNestedSet($_SESSION['Index']->adminShop);
	}
	public function prepareDisplay(){
		require_once('ProductEdit.ItemListUpdate.class.php');
		require_once('ProductEdit.ProductListUpdater.class.php');

#region Funktionen für die Listen, ggf noch auslagern
//Bereitet eine Commandlist für die Serialisierung und das Speichern in der Datenbank vor
function saveableCommandArray($CommandList){
	//Custom-Commands für die Datenbank aufbereiten:
	$commands = array();
	foreach($CommandList as $key => $value)
	{
		if(trim($value[1]) == ""){
			unset($CommandList[$key]);
		}
		else{
			$commands[] = $CommandList[$key][1];
		}
	}
	return array_values($commands);
}
//Nimmt ein deserialisiertes Array aus der Datenbank an
function displayableCommandArray($commands){
	if($commands === false){
		return array(array(0,'',0));
	}
	$commandList = array();
	foreach($commands as $command){
		$commandList[count($commandList)] = array(count($commandList),$command);
	}
	return $commandList;
}

function updateCommands($CommandArray, $AddButton, $DeleteButton, $TestButton, $testOnline, &$hasCommands = false){
	$list = array();
	if(is_array($CommandArray)){
		foreach($CommandArray as $key => $value)
		{
			if(!isset($DeleteButton[$key])) //Das Item, dessen Löschen-Button gedrückt wurde, wird nicht übernommen
			{
				$value = ltrim($value,'/ ');
				$list[] = $value;
				if($value) $hasCommands = true;
			}
		}
	}

	for($i = 0; $i < count($list); $i++){
		$success = 0;
		$message = 0;
		$transferResult = null;
		if(isset($TestButton[$i])){
			$Minecraftname = $_SESSION['Index']->db->fetchOne("SELECT Minecraftname FROM mc_customers INNER JOIN mc_shops ON mc_shops.CustomersId=mc_customers.Id WHERE mc_shops.Id='{$_SESSION['Index']->adminShop->getId()}' LIMIT 1");
			if(JSONquery::TransferCommand($_SESSION['Index']->adminShop->getId(), $list[$i], $Minecraftname, $testOnline)){
				$message = $_SESSION['Index']->lang->say('COMMAND_TRANSFER_SUCCESS');
				$success = 1;
			}
			else{
				$message = $_SESSION['Index']->lang->say('TRANSFER_ERROR');
				$success = 2;
			}
			
		}
		$list[$i] = array($i, $list[$i], $success, $message);
	}

	if($AddButton || count($list) == 0){
		$list[] = array(count($list),'',0);
	}
	return $list;
}
#end

		$ProdId = $_GET['item'];

		$_SESSION['Index']->assign_say('BACK');
		$_SESSION['Index']->assign_say('ADM_ITEMS_EDIT_TITLE');

		$ShopId = $_SESSION['Index']->adminShop->getId();
		if(!isNumber($ProdId, 1) || (($ProdId != '0') && (1 != $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_products WHERE Id='$ProdId' AND ShopId='$ShopId'")))){
			$_SESSION['Index']->assign_say('ADM_ITEMS_INFO','ADM_ITEM_EDIT_ERROR');
			return;
			//Ein Standard-Item wird geändert und muss daher kopiert werden Products::CopyItem($ProdId, $ShopId); #!#
		}
		#end

		$itemListUpdater = new ItemListUpdater($ShopId, $ProdId);
		$productListUpdater = new ProductListUpdater($ShopId, $ProdId);

		$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_ID', $ProdId);
		$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_MODE', 1);

		#region Die per POST übergebenen Parameter merken
		#region Status der auf- und zugeklappten Bereiche
		$closeArea = null;
		if(is_array($_POST['state']))
			foreach($_POST['state'] as $value)
			{
				if($value){
					if($closeArea) $closeArea .= ','.$value;
					else $closeArea .= $value;
				}
			}
		if($closeArea !== null)
			$_SESSION['closeArea'] = $closeArea;
		else
			$closeArea = $_SESSION['closeArea'];
		#end

		#region Allgemein
		$label = $_POST['label'];
		$newIcon = $_POST['newIcon'];
		$delImageButton = $_POST['delImageButton'];
		$imageUndoButton = $_POST['imageUndoButton'];
		$uploadImageButton = $_POST['uploadImageButton'];
		$imgId = $_POST['imgId'];
		//$customImage = 1;
		$description = $_POST['description'];
		$group = $_POST['group'];
		$points = $_POST['points'];
		$enabled = isset($_POST['label']) ? $_POST['enabled'] : true;
		#end
		#region Befehle
		$needsPlayerOnline = isset($_POST['label']) ? $_POST['needsPlayerOnline'] : true;
		$customCommands = $_POST['customCommands'];
		$customCommandAddButton = $_POST['customCommandAddButton'];
		$customCommandDeleteButton = $_POST['customCommandDeleteButton'];
		$customCommandTestButton = $_POST['customCommandTestButton'];
		#end
		#region Zeitliche Begrenzung
		$cooldownActive = $_POST['cooldownActive'];
		$cooldown = $_POST['cooldown'];
		$interval = count($_POST) ? $_POST['interval'] : 'd';
		$cooldownNeedsPlayer = count($_POST) ? $_POST['cooldownNeedsPlayer'] : false;

		$customCommandsEnd = $_POST['customCommandsEnd'];
		$customCommandEndAddButton = $_POST['customCommandEndAddButton'];
		$customCommandEndDeleteButton = $_POST['customCommandEndDeleteButton'];
		$customCommandEndTestButton = $_POST['customCommandEndTestButton'];
		$disableDuringCooldown = count($_POST) ? $_POST['disableDuringCooldown'] : true;
		#end
		#end

		if(count($_POST) > 0)
		{
			$itemListUpdater->update($_POST['selectedItems'], $_POST['selectedItemsAmount'], $_POST['addItemId'], $_POST['addItemAmount'], $_POST['itemRemoveButton'], $_POST['itemEditButton']);
			$productListUpdater->update($_POST['selectedProducts'], $_POST['selectedProductsAmount'], $_POST['addProductId'], $_POST['addProductAmount'], $_POST['productRemoveButton']);
		}
		else
		{
			$itemListUpdater->load();
			$productListUpdater->load();
		}


		#region Bei einem neuen Item die Überschrift entsprechend setzen
		if($ProdId == 0){
			$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_TITLE', 'ADM_PRODUCT_ADD_TITLE');
			if(count($_POST) == 0){
				$customCommandList = $customCommandEndList = array(array(0,'',0));
				// $selectedProducts = updateProductList($ProdId, $ShopId, $selectedProducts, $selectedProductsAmount, $newProductId, $newProductAmount, null, null, $fullProductList);
			}
		}
		#end
		#region Bei einem vorhandenen Item gibts 'ne andere Überschrift und ggf. Startwerte
		else{
			$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_TITLE', 'ADM_PRODUCT_EDIT_TITLE');
			//Bei einem vorhandenen Item werden die Standardwerte ausgelesen, wenn keine POST-Werte übergeben wurden
			if(count($_POST) == 0){
				$itemInfo = $_SESSION['Index']->db->fetchOneRow("SELECT * FROM mc_products WHERE Id='$ProdId' AND ShopId='$ShopId' LIMIT 1");
				#region Allgemein
				$label = $itemInfo->Label;
				$imgId = $itemInfo->Image;
				if($imgId){
					$_SESSION['tmpProdImg'.$ProdId]['orgImage'] = $imgId;
					$_SESSION['tmpProdImg'.$ProdId]['delete'] = false;
				}
				else{
					unset($_SESSION['tmpProdImg'.$ProdId]);
				}
				$description = $itemInfo->Description;
				$group = $itemInfo->GroupId;
				$points = $itemInfo->Points;
				$enabled = $itemInfo->Enabled;
				#end
				#region Befehle
				$needsPlayerOnline = $itemInfo->NeedsPlayerOnline;
				$customCommandList = displayableCommandArray(unserialize($itemInfo->CustomCommand));
				#end

				$cooldown = $itemInfo->Cooldown;
				$cooldownActive = $cooldown > 0;
				$interval = $itemInfo->CooldownInterval;
				$cooldownNeedsPlayer = $itemInfo->CooldownNeedsPlayer;

				$customCommandEndList = displayableCommandArray(unserialize($itemInfo->CustomCommandEnd));
				$disableDuringCooldown = $itemInfo->DisableDuringCooldown;
			}
		}
		#end

#region Eingaben prüfen und verarbeiten
if(count($_POST) > 0){
	$save = true;
#region Allgemein
// Label
	if(!Item::CheckLabelAllowed($ShopId, $label, $ProdId)){
		$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_LABEL_ERROR');
		$save = false;
	}
// Bild
	if($delImageButton || $imageUndoButton){
		#region aktuelles Bild löschen
		//Merken, dass das Originalbild gelöscht werden muss (ggf. wird dies durch den Rückgängig-Button gleich wieder aufgehoben)
		if($_SESSION['tmpProdImg'.$ProdId]['orgImage'] && !$_SESSION['tmpProdImg'.$ProdId]['delete']){
			$_SESSION['tmpProdImg'.$ProdId]['delete'] = true;
		}
		//Falls ein Bild bereits hochgeladen wurde, wird dieses gelöscht
		else{
			unset($_SESSION[$imgId]);
		}
		#end
		//Originalbild wiederherstellen
		if($imageUndoButton){
			$_SESSION['tmpProdImg'.$ProdId]['delete'] = false;
		}
		$save = false;
	}
	if($_FILES['newIcon'] && $_FILES['newIcon']['name']){//Selbst wenn der Löschen-Button gedrückt wird, wird das gerade ausgewählte Bild hochgeladen
		$uploadedIcon = $_FILES['newIcon'];
		#region Upload Fehler
		$imgError = null;
		if(!is_uploaded_file($uploadedIcon['tmp_name'])){
			$imgError = "ADM_PRODUCT_EDIT_ICON_ERROR_UPLOAD";
		}
		/* Dateigrößenprüfung, findet in createThumbnail statt
		elseif(($uploadedIcon['error'] == 2) || (filesize($uploadedIcon['tmp_name']) > MAX_PRODUCT_IMAGE_FILE_SIZE)){
		//Datei zu groß
			$imgError = "Datei zu groß";
		}
		*/
		elseif($uploadedIcon['error'] != 0){
			$imgError = "ADM_PRODUCT_EDIT_ICON_ERROR_UNKNOWN";
		}
		else{
			//Versuche die Bilddatei zu validieren und die Größe anzupassen
			switch(FileUpload::createThumbnail($uploadedIcon['tmp_name'], $uploadedIcon['tmp_name'], MAX_PRODUCT_IMAGE_WIDTH, MAX_PRODUCT_IMAGE_HEIGHT)){
				case -2:
					$imgError = "ADM_PRODUCT_EDIT_ICON_ERROR_INVALID";
					break;
				case -3:
					$imgError = "ADM_PRODUCT_EDIT_ICON_ERROR_SCALE";
					$imgErrorExtra = array(MAX_PRODUCT_IMAGE_WIDTH,MAX_PRODUCT_IMAGE_HEIGHT);
					break;
			}
		}
		#end
		if($imgError){
			$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ICON_ERROR', $imgError, $imgErrorExtra);
			$save = false;
		}
		else{
			#region aktuelles Bild löschen
			//Merken, dass das Originalbild gelöscht werden muss.
			if($_SESSION['tmpProdImg'.$ProdId]['orgImage'] && !$_SESSION['tmpProdImg'.$ProdId]['delete']){
				$_SESSION['tmpProdImg'.$ProdId]['delete'] = true;
			}
			//Falls ein Bild bereits hochgeladen wurde, wird dieses gelöscht
			else{
				unset($_SESSION[$imgId]);
			}
			#end

			//Neue Id für das gerade hochgeladene Bild
			$imgId = md5(microtime(true));
			//Bild-Typ merken
			$_SESSION[$imgId]['imgType'] = $uploadedIcon['type'];
			//Datei in die Session lesen
			$h = fopen($uploadedIcon['tmp_name'], "rb");
			$_SESSION[$imgId]['tmpImg'] = fread($h, filesize($uploadedIcon['tmp_name']));
			fclose($h);
			//ggf. vorher hochgeladenes Bild löschen
		}
	}
// Menü-Gruppe
	if(!Item::CheckMenueAllowed($_SESSION['Index']->adminShop->getId(), $group)){
		$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_GROUP_ERROR');
		$save = false;
	}
// Punkte
	if(!Item::CheckPointsAllowed($points)){
		$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_POINTS_ERROR');
		$save = false;
	}
// Produkt aktiv
	$enabled = $enabled == true; //string -> boolean
#end

#region Befehle/Produkte/Items
	// Spieler muss online sein
	$needsPlayerOnline = $needsPlayerOnline ? true : false;

	$customCommandList = updateCommands($customCommands, $customCommandAddButton, $customCommandDeleteButton, $customCommandTestButton, $needsPlayerOnline, $hasCommands);

	if(!($hasCommands || $hasProducts || $itemListUpdater->hasItems)){
		$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_CONTENT_ERROR');
		$save = false;
	}
	else{
		
	}

	if((isset($_POST['save_button']) || isset($_POST['hidden_button'])) && isset($_POST['itemEditId'])){
		$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ITEMS_NEED_SAVE');
		$save = false;
	}

#end

#region Zeitliche Begrenzung
	if($cooldownActive){
		if(!isNumber($cooldown)){
			$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COOLDOWN_ERROR');
			$save = false;
		}

		if(!in_array($interval,array('i','h','d','w','m'))){ //Schutz gegen falsche Daten
			$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COOLDOWN_ERROR');
			$interval = 'd';
			$save = false;
		}
		$disableDuringCooldown = $disableDuringCooldown ? true : false;
		$cooldownNeedsPlayer = $cooldownNeedsPlayer ? true : false;

		$customCommandEndList = updateCommands(
			$customCommandsEnd,
			$customCommandEndAddButton,
			$customCommandEndDeleteButton,
			$customCommandEndTestButton,
			$cooldownNeedsPlayer
		);
	}
	else{
		$cooldown = null;
		$customCommandEndList = array(array(0,''));
	}
#end


#region Bei bestimmten Buttons nicht speichern
	if($delImageButton
		|| $uploadImageButton
		|| $imageUndoButton
		|| $customCommandAddButton
		|| $customCommandDeleteButton
		|| $customCommandTestButton
		|| $productRemoveButton
		|| $customCommandEndAddButton
		|| $customCommandEndDeleteButton
		|| $customCommandEndTestButton

		|| !$productListUpdater->CanSave || isset($_POST['productAddButton'])
		|| !$itemListUpdater->CanSave || isset($_POST['itemAddButton'])

		|| isset($_POST['itemEditButton'])
		|| isset($_POST['itemEditSaveButton'])
		|| isset($_POST['itemEditCancelButton'])
		|| isset($_POST['itemEditTestButton'])
		|| isset($_POST['enchRemoveButton'])
		|| isset($_POST['enchAddButton'])
		|| isset($_POST['itemCreateButton'])
	){
		$save = false;
	}
#end
}
#end

#region Speichern
if($save){
	function imageToFile($imgId){
		if($_SESSION[$imgId]){
			$ext = '.png';
			if(stripos($_SESSION[$imgId]['imgType'], 'jpeg') !== false ||stripos($_SESSION[$imgId]['imgType'], 'jpg') !== false){
				$ext = '.jpg';
			}
			elseif(stripos($_SESSION[$imgId]['imgType'], 'gif') !== false){
				$ext = '.gif';
			}
			$name = $imgId;
			while(file_exists(ITEM_IMAGE_DIR.$name.$ext))
			{
				$name = md5(microtime());
			}
			$h = fopen(ITEM_IMAGE_DIR.$name.$ext, 'wb');
			fputs($h, $_SESSION[$imgId]['tmpImg']);
			fclose($h);
			unset($_SESSION[$imgId]);
			FileUpload::createThumbnail(ITEM_IMAGE_DIR.$name.$ext, ITEM_PREV_IMAGE_DIR.$name.$ext, 32, 32);
			return $name.$ext;
		}
		return '';
	}
	$imgFile = imageToFile($imgId);
	#region Item ist neu
	if($ProdId == 0){
		#region Produkt hinzufügen
		$serializeableCommandArray = saveableCommandArray($customCommandList);
		$serializeableCommandEndArray = saveableCommandArray($customCommandEndList);

		$newProductId = $_SESSION['Index']->db->insert("INSERT INTO mc_products (
			ShopId,
			Label,
			Description,
			Image,
			CustomImage,
			Points,
			GroupId,
			Enabled,
			NeedsPlayerOnline,
			CooldownNeedsPlayer,
			CustomCommand,
			Cooldown,
			CooldownInterval,
			CustomCommandEnd,
			DisableDuringCooldown,
			HasSetItems
		)
		VALUES (
			'".$_SESSION['Index']->adminShop->getId()."',
			'".mysql_real_escape_string($label)."',
			'".mysql_real_escape_string($description)."',
			'".mysql_real_escape_string($imgFile)."',
			'1','$points','$group','$enabled','$needsPlayerOnline','$cooldownNeedsPlayer',".
			(count($serializeableCommandArray)>0 ? "'".mysql_real_escape_string(serialize($serializeableCommandArray))."'" : 'null').",
			".($cooldown ? "'$cooldown','$interval',".(count($serializeableCommandEndArray)>0 ? "'".mysql_real_escape_string(serialize($serializeableCommandEndArray))."'" : 'null').",'$disableDuringCooldown'" : "null,null,null,null").
			",'".(count($productListUpdater->selectedProductsList) + count($itemListUpdater->selectedItems))."')");
		#end

		$productListUpdater->save($newProductId);
		$itemListUpdater->save($newProductId);

		$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_MODE', 0);
		$_SESSION['Index']->assign_say('ADM_PRODUCT_EDITED_INFO');
	}
	#end
	#region Item wird geändert
	else{
		$serializeableCommandArray = saveableCommandArray($customCommandList);
		$serializeableCommandEndArray = saveableCommandArray($customCommandEndList);

		startTransaction('mc_products WRITE, mc_ProductsInProduct WRITE, mc_ItemsInProduct WRITE');
		#region mc_products aktualisieren
		$orgImageIsCustom = $_SESSION['Index']->db->fetchOne("SELECT CustomImage FROM mc_products WHERE ShopId='$ShopId' AND Id='$ProdId'");
		$_SESSION['Index']->db->query("UPDATE mc_products SET "
			."Label='".mysql_real_escape_string($label)."',"
			."Description='".mysql_real_escape_string($description)."',"
			.($_SESSION['tmpProdImg'.$ProdId]['delete'] || $imgFile
				? "Image='".mysql_real_escape_string($imgFile)."',CustomImage='1',"
				: '')
			."Points='$points',"
			."GroupId='$group',"
			."Enabled='$enabled',"
			."NeedsPlayerOnline='$needsPlayerOnline',"
			."CooldownNeedsPlayer='$cooldownNeedsPlayer',"
			."HasSetItems='".(count($productListUpdater->selectedProductsList) + count($itemListUpdater->selectedItems))."',"
			.(count($serializeableCommandArray) > 0
				? "CustomCommand='".mysql_real_escape_string(serialize($serializeableCommandArray))."',"
				: 'CustomCommand=null,')
			.($cooldown
				? "Cooldown='$cooldown',
						CooldownInterval='$interval',
						CustomCommandEnd=".(count($serializeableCommandEndArray)>0 ? "'".mysql_real_escape_string(serialize($serializeableCommandEndArray))."'" : 'null').",
						DisableDuringCooldown='$disableDuringCooldown'"
				: "Cooldown='0',
						CooldownInterval=null,
						CustomCommandEnd=null,
						DisableDuringCooldown=null"
			)
		." WHERE Id='$ProdId' AND ShopId='$ShopId' LIMIT 1");
		#end

		$productListUpdater->save();
		$itemListUpdater->save();
		commit();
		#region Das ursprüngliche Bild ggf. löschen
		if($_SESSION['tmpProdImg'.$ProdId]['delete'] && $orgImageIsCustom){
			unlink(ITEM_IMAGE_DIR.$_SESSION['tmpProdImg'.$ProdId]['orgImage']);
			unlink(ITEM_PREV_IMAGE_DIR.$_SESSION['tmpProdImg'.$ProdId]['orgImage']);
			$img = '';
		}
		#end
		$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_MODE', 0);
		$_SESSION['Index']->assign_say('ADM_PRODUCT_EDITED_INFO');
	}
	#end
}
#end
#region Daten ausgeben zum Überprüfen durch den Nutzer
else{
	#region Werte ausgeben
	#region Bereiche Auf/Zuklappen
	$_SESSION['Index']->assign_direct('CLOSE_AREAS', $closeArea);
	#end


#region Allgemein
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_LABEL_VALUE', $label);
	#region Bild
	if($_SESSION['tmpProdImg'.$ProdId]['orgImage'] && !$_SESSION['tmpProdImg'.$ProdId]['delete']){
		$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_IMAGE', ITEM_IMAGE_DIR.$_SESSION['tmpProdImg'.$ProdId]['orgImage']);
		$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_IMG_ID', $imgId);
	}
	elseif(isset($_SESSION[$imgId])){
		$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_IMG_ID', $imgId);
	}
	if($_SESSION['tmpProdImg'.$ProdId]['orgImage'] && ($_SESSION[$imgId] || $_SESSION['tmpProdImg'.$ProdId]['delete'])){
		$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_IMAGE_CAN_UNDO', 1);
	}
	#end

	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_DECSCRIPTION_VALUE', $description);
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_GROUP_VALUE', $group);
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_GROUP_VALUES', $this->nstree->treeAsArray(null,true));
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_POINTS_VALUE', $points);
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_ENABLED_VALUE', $enabled);
#end
#region Befehle
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_NEEDS_PLAYER_ONLINE_VALUE', $needsPlayerOnline);
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_COMMANDS_VALUE', $customCommandList);
#end
#region Produkte
	if($productListUpdater->selectedProductsList) $_SESSION['Index']->assign('ADM_PRODUCT_EDIT_SELECTED_PRODUCTS_VALUE', $productListUpdater->selectedProductsList);
	else $_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_SELECTED_PRODUCTS_EMPTY');
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_AVAILABLE_PRODUCTS_LIST', $productListUpdater->availableProductsList);
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_SELECTED_PRODUCT', $productListUpdater->addProductId);
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_PRODUCT_AMOUNT', $productListUpdater->addProductAmount);
#end
#region Items
	if(isset($_POST['itemEditId']) && !isset($_POST['itemEditCancelButton'])){
		$_SESSION['Index']->assign_direct('editItemParams', array('ItemId' => $_POST['itemEditId']));
	}
	elseif($itemListUpdater->editItemId > 0){
		$_SESSION['Index']->assign_direct('editItemParams', array('ItemId' => $itemListUpdater->editItemId));
	}
	elseif(isset($_POST['itemCreateButton']) || count($itemListUpdater->availableItems) == 0){
		$_SESSION['Index']->assign_direct('editItemParams', array('ItemId' => 0));
	}
	

	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_SELECTED_ITEMS_VALUE', $itemListUpdater->selectedItems);
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_AVAILABLE_ITEMS_LIST', $itemListUpdater->availableItems);
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_SELECTED_ITEM', $itemListUpdater->addItemId);
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_ITEM_AMOUNT', $itemListUpdater->addItemAmount);

#end
#region Cooldown
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_COOLDOWN_ACTIVE_VALUE', $cooldownActive);
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_COOLDOWN_VALUE', $cooldown);
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_LIFETIME_INTERVAL', $interval);
	$_SESSION['Index']->assign('ADM_PRODUCT_EDIT_END_COMMANDS_VALUE', $customCommandEndList);
#end
	$_SESSION['Index']->assign_say('MINUTES');
	$_SESSION['Index']->assign_say('MONTHS');
	$_SESSION['Index']->assign_say('DAYS');
	$_SESSION['Index']->assign_say('WEEKS');
	$_SESSION['Index']->assign_say('HOURS');
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_COOLDOWN_NEEDS_PLAYER_VALUE', $cooldownNeedsPlayer);
	$_SESSION['Index']->assign_direct('ADM_PRODUCT_EDIT_DISABLE_DURING_COOLDOWN_VALUE', $disableDuringCooldown);
	#end

	#region Sprach-Texte ausgeben
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_OPTION_PLEASE_SELECT');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_AMOUNT');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_GENERAL');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_CONTENT');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_TIME_LIMIT');

	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ITEM_CREATE_INFO');

	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_LABEL');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ICON');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_DECSCRIPTION');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_GROUP');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_POINTS');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_POINTS_DESC');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ENABLED');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_NEEDS_PLAYER_ONLINE');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COMMANDS');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_PRODUCTS');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ACTIVE');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COOLDOWN');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COMMANDS_END');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_DISABLE_DURING_COOLDOWN');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_SAVE');

	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_TEST_COMMAND');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_DELETE_COMMAND');

	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_LABEL_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ICON_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_DECSCRIPTION_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_GROUP_INFO');
	$OurEarningReate = 100 - $_SESSION['Index']->db->fetchOne("SELECT EarningRate FROM mc_customers WHERE Id='{$_SESSION['Index']->adminShop->getShopInfo()->CustomersId}' LIMIT 1");
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_POINTS_INFO',array(CurrencyFormatted(1/POINTS_PER_EURO).' '.CURRENCY_SHORT, $OurEarningReate));
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ENABLED_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_NEEDS_PLAYER_ONLINE_INFO');

	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COMMANDS_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COMMAND_NEW');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_PRODUCTS_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_PRODUCTS_ADD_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_PRODUCT_ADD_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_PRODUCT_DELETE_INFO');

	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ITEMS');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ITEMS_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ITEMS_ADD_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ITEM_ADD_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ITEM_DELETE_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_ITEM_EDIT_INFO');

	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_LIMIT_ACTIVE_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COOLDOWN_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COMMANDS_END_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COOLDOWN_NEEDS_PLAYER');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_COOLDOWN_NEEDS_PLAYER_INFO');
	$_SESSION['Index']->assign_say('ADM_PRODUCT_EDIT_DISABLE_DURING_COOLDOWN_INFO');
	#end
}
#end
	}
}


?>