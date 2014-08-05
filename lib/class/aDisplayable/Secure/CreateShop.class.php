<?php
defined('_MCSHOP') or die("Security block!");

class CreateShop extends aDisplayable
{
	public function prepareDisplay()
	{
		if(isset($_GET['new'])){$_SESSION['CreateShop'] = array();}

	$_SESSION['Index']->assign_say("ADM_SERVSET_NEWSERV_TITLE");
	//$_SESSION['NewServer_Mode'] = "nomode";
	/* if($_POST['configmode'] == "generate" || $_POST['configmode'] == "setup") $_SESSION['NewServer_Mode'] = $_POST['configmode'];
	if($_GET['configmode'] == "generate" || $_GET['configmode'] == "setup") $_SESSION['NewServer_Mode'] = $_GET['configmode'];

	if(!isset($_GET['configmode']) && !isset($_POST['configmode'])) $_SESSION['NewServer_Mode'] = "nomode";
	*/

	$_SESSION['Index']->assign_say('BACK');

	if(!$_SESSION['CreateShop']['Step'])
		$_SESSION['CreateShop']['Step'] = 0;

	if ($_GET['configmode'] == "setup"){
		$_SESSION['NewServer_Mode'] = "setup";
		$_SESSION['CreateShop']['Step'] = 1;
	}

	if(isset($_POST['step0generate'])){
		// initialize next step for generation of the config
		$_SESSION['CreateShop']['Step'] = 1;
		$_SESSION['NewServer_Mode'] = "generate";
	}

	$_SESSION['Index']->assign_direct("ADM_SERVSET_NEWSERV_MODE", $_SESSION['NewServer_Mode']);
	if(empty($_SESSION['CreateShop']['servername']))
		$_SESSION['CreateShop']['servername'] = "Bukkit Server";

	if(empty($_SESSION['CreateShop']['apiuser']))
		$_SESSION['CreateShop']['apiuser'] = "webshop";

	if(empty($_SESSION['CreateShop']['port']))
		$_SESSION['CreateShop']['port'] = "20059";

	if(empty($_SESSION['CreateShop']['apisalt']))
		$_SESSION['CreateShop']['apisalt'] = random_string_by_length(15);

	if(empty($_SESSION['CreateShop']['apipassword']))
		$_SESSION['CreateShop']['apipassword'] = random_string_by_length(15);

	#region one step back
	if(($_SESSION['CreateShop']['Step'] == 1 || $_SESSION['CreateShop']['Step'] == 2 || $_SESSION['CreateShop']['Step'] == 3) 
	&& $_GET['back']){
		$_SESSION['CreateShop']['Step']--;
		$_SESSION['CreateShop']['code'] = "";
	}
	#end

	#region Step-1
	if($_SESSION['CreateShop']['Step'] == 0){
	}
	elseif($_SESSION['CreateShop']['Step'] == 1){

		if($_SESSION['NewServer_Mode'] != "setup"){ // Automatically generate a config file for the user to set up JSONAPI

			$_SESSION['CreateShop']['tmpconfig'] = random_number_by_length(10);

			//$salt = random_string_by_length(32);
			// $password = random_string_by_length(32);
			$_SESSION['CreateShop']['servername'] = "Bukkit Server";
			//$_SESSION['configgenerator_tmpconfig'] = array("ID" => $configgenerator_tmpconfig, "PASSWORD" => $password, "SALT" => $salt, "SERVERNAME" => $servername);
			$_SESSION['Index']->assign_direct('ADM_SERVSET_NEWSERV_GENERATE_HOSTNAME_SAVE',$_POST['hostname']);
			$_SESSION['CreateShop']['hostname']=$_POST['hostname'];
			if (isset($_POST['hostname']) && isInvalidHostnameOrIp($_POST['hostname'])){
				$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_HOSTNAME', true);
			}
			elseif(isset($_POST['hostname'])){ //correct hostname/ip
				$_SESSION['CreateShop']['Step'] = 2;
			}
			$_SESSION['Index']->assign_say("ADM_SERVSET_NEWSERV_GENERATED_HOSTNAME_LABEL"); // Hostname and IP of your Minecraftserver
			$_SESSION['Index']->assign_say("ADM_NEWSERVER_DOWNLOAD_CONFIG"); // Config will get generated 
			$_SESSION['Index']->assign_say("ADM_NEWSERVER_DOWNLOAD_CONFIG_LABEL"); // Click to here download
			$_SESSION['Index']->assign("ADM_NEWSERVER_DOWNLOAD_CONFIG_URL", './configgenerator.php?tmpconfig='.$_SESSION['CreateShop']['tmpconfig']);
		}
		else {
			#region generate new API-PW
			if(isset($_GET['generatePw'])){ //Es wird auf den generiern-Button geklickt
				//generates the random salt
				$_SESSION['CreateShop']['apipassword'] = random_string_by_length(32);
			}
			#end
			#region generate new API-Salt
			if(isset($_GET['generateSalt'])){ //Es wird auf den generiern-Button geklickt
				//generates the random salt
				$_SESSION['CreateShop']['apisalt'] = random_string_by_length(32);
			}
			#end
			#region Process the Form-Data and go to the next Step
			elseif($_POST && !$_POST['back']){
				$nextstep = true;
				#region hostname
				//$ValidIpAddressRegex = "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$^";

				//$ValidHostnameRegex = "^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$^";
				//if(preg_match('/^[a-z0-9]*$/',$_POST['hostname']) !== 1){

				if (isInvalidHostnameOrIp($_POST['hostname'])){
					$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_HOSTNAME', true);
					$nextstep = false;
				}
				else{
					$_SESSION['CreateShop']['hostname'] = $_POST['hostname'];
				}
				#end
				#region apiuser
				if(!$_POST['apiuser']){
					$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_APIUSER', true);
					$nextstep = false;
				}
				else{
					$_SESSION['CreateShop']['apiuser'] = $_POST['apiuser'];
				}
				#end
				#region port
				if(!isNumber($_POST['port'])){
					$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_PORT', true);
					$nextstep = false;
				}
				else{
					$_SESSION['CreateShop']['port'] = $_POST['port'];
				}
				#end
				#region apipassword
				if(!$_POST['apipassword']){
					$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_PWR', true);
					$nextstep = false;
				}
				else{
					$_SESSION['CreateShop']['apipassword'] = $_POST['apipassword'];
				}
				#end
				#region apisalt
				if(!$_POST['apisalt']){
					$_SESSION['Index']->assign_say('ADM_SERVSET_ERROR_SALT');
					$nextstep = false;
				}
				else{
					$_SESSION['CreateShop']['apisalt'] = $_POST['apisalt'];
				}
				#end

				if($nextstep){
					$_SESSION['CreateShop']['Step'] = 2;
				}
			}
			#end
			//leer machen, falls auf den zurück-Link geklickt wird.
			$_SESSION['CreateShop']['code'] = "";
		}// mode = setup
	}
	#end

	#region Step-2
	if($_SESSION['CreateShop']['Step'] == 2){
		#region neuen Code generieren und zum Server schicken
		if($_GET['newcode']){
			$_SESSION['CreateShop']['code'] = random_string_by_length(5); //random string from A-Z; length:8
			$customerId = $_SESSION['CustomerId'];
			//String, der an den Minecraftserver gesendet wird
			$msg = $_SESSION['Index']->lang->say('ADM_SERVSET_NEWSERV_GAME_VERIFICATION', array($_SESSION['CreateShop']['code']));
			//Dieser Spieler bekommt die Nachricht
			$player = $_SESSION['Index']->db->fetchOne("SELECT MinecraftName FROM mc_customers WHERE Id='".mysql_real_escape_string($customerId)."'");
			
			//Send Validation to the Server
			$result = JSONquery::sendValidation($_SESSION['CreateShop']['hostname'],
				$_SESSION['CreateShop']['port'],
				$_SESSION['CreateShop']['apiuser'], $_SESSION['CreateShop']['apipassword'],
				$_SESSION['CreateShop']['apisalt'], $player, $msg, $_SESSION['Index']->db->getUUID());
			if($result){ //@Todo: wird das result tatsächlich richtig geprüft? was gibt store.sendMessage zurück?
				if ($result['result'] == "error"){
					$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_DIRECT', $_SESSION['Index']->lang->say('ADM_SERVSET_NEWSERV_WRONG_CREDENTIALS')); 
					$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_DIRECT', "wrong server data!"); //Wrong server data";
					$_SESSION['CreateShop']['code'] = "";
				}
			}
			else{
				$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_DIRECT', $_SESSION['Index']->lang->say('ADM_SERVSET_NEWSERV_WRONG_SERVER_DATA'));
				$_SESSION['CreateShop']['code'] = "";
			}
		}
		#end
		elseif(count($_POST)){
			#region Code korrekt
			if($_SESSION['CreateShop']['code'] == $_POST['verifycode'] && $_POST['verifycode'] != ""){
				$_SESSION['CreateShop']['Step'] = 3;
			}
			#end
			#region Code fehlerhaft
			elseif(isset($_POST['verifycode'])){
				$InvalidCode = true;
			}
			#end
		}
	}
	#end

	#region Step-3
	if($_SESSION['CreateShop']['Step'] == 3){
		if(isset($_POST['serverlabel'])){
			$_SESSION['CreateShop']['serverlabel'] = trim($_POST['serverlabel']);
			if(!$_SESSION['CreateShop']['serverlabel']){
				$InvalidServerlabel = true;
			}
			$_SESSION['CreateShop']['subdomain'] = trim($_POST['subdomain']);
			if(!$_SESSION['CreateShop']['subdomain'] || !preg_match('/^[0-9a-z\-]+$/i',$_SESSION['CreateShop']['subdomain']) || $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_shops WHERE subdomain='".mysql_real_escape_string($_SESSION['CreateShop']['subdomain'])."'")){
				$InvalidSubdomain = true;
			}
			$_SESSION['CreateShop']['template'] = 1;
			// $_SESSION['CreateShop']['template'] = $_POST['template'];
		}

		$InvalidTemplate = true;
		$templates = array();
		foreach($_SESSION['Index']->db->iterate("SELECT Id, Label FROM mc_templates WHERE Public='1'") as $row){
			$templates[] = array(
			'id' => $row->Id,
			'name' => $row->Label);

			if($_SESSION['CreateShop']['template'] == $row->Id){
				$InvalidTemplate = false;
			}
		}

		if(isset($_POST['serverlabel']) && !$InvalidServerlabel && !$InvalidSubdomain && !$InvalidTemplate){
			$_SESSION['CreateShop']['Step'] = 4;
		}
	}
	#end

	#region Step 4
	if($_SESSION['CreateShop']['Step'] == 4){
		//insert into db
		startTransaction();
		try{
			$RowId = $_SESSION['Index']->db->insert(sprintf("INSERT INTO mc_shops
			(Label, Subdomain, TemplateId, CustomersId, ServerHost, ServerPort, ServerUser, ServerPassword, ServerSalt) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
			mysql_real_escape_string($_SESSION['CreateShop']['serverlabel']),
			mysql_real_escape_string($_SESSION['CreateShop']['subdomain']),
			mysql_real_escape_string($_SESSION['CreateShop']['template']),
			$_SESSION['CustomerId'],
			mysql_real_escape_string($_SESSION['CreateShop']['hostname']),
			mysql_real_escape_string($_SESSION['CreateShop']['port']),
			mysql_real_escape_string($_SESSION['CreateShop']['apiuser']),
			mysql_real_escape_string($_SESSION['CreateShop']['apipassword']),
			mysql_real_escape_string($_SESSION['CreateShop']['apisalt'])));
			$_SESSION['Index']->db->insert("INSERT INTO mc_productGroups (Id, ShopId, Label, lft, rgt, Enabled) VALUES ('1','$RowId','root','1','2','1')");
			commit();

			$_SESSION['Index']->adminShop = new Shop($RowId);
			#region Aktuelle Shopauswahl in der Datenbank speichern
			$_SESSION['Index']->db->query("UPDATE mc_customers SET LastShopId='$RowId' WHERE Id='{$_SESSION['CustomerId']}' LIMIT 1");
			#end
		}
		catch(Exception $e){
			//@todo: log $e
			error_log(__FILE__."/".__LINE__." - Error while creating new shop: ".$e);
			rollback();
			$error = true;
		}
		//clear session data in case he wants to add a second server. If the configuration is canceled and opened again the old values will be present.
		$_SESSION['CreateShop'] = null;
		$_SESSION['CreateShop']['Step'] = 4;
	}
	#end

	#region Ausgabe
	$_SESSION['Index']->assign_say('ADM_GET_HELP');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_FAQ_LINK',array("http://".BASE_DOMAIN));
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_REQUIREMENTS_LABEL');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SETUPSERVER_LABEL');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_VERIFYDATA_LABEL');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SHOPSETTINGS_LABEL');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_COMPLETE_LABEL');
	$_SESSION['Index']->assign_say('OR');

	// Requirements
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_LATEST_VERSION_JSONAPI');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_LATEST_VERSION_PLUGIN');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_LATEST_VERSION_BUKKIT');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_JSONAPI_CREDITS');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_REQUIREMENT_PUBLIC_SERVER');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SETUP_REQUIREMENTS');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_JSONAPI_INSTALLED_QUESTION');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_JSONAPI_INSTALLED_ANSWER');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_JSONAPI_EXPERT');

	// Autogenerate Config
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_CONFIG_GENERATION_INFO');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_DOWNLOAD_CONFIG');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST_STOP');
	//$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST_DOWNLOAD');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST_DOWNLOAD_JSONAPI');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST_COPY_CONFIG');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST_COPY_CONFIG_OVERWRITE');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST_RESTART');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST_ENTER_IP');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_STEPLIST_VERIFY');
	$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_VERIFY');

	if($error){
		$_SESSION['Index']->assign_say('ADM_ERROR');
	}
	else{//var_dump($_SESSION['CreateShop']['Step']);
		$_SESSION['Index']->assign_direct('ADM_SERVSET_NEWSERV_STEP', $_SESSION['CreateShop']['Step']);
		if($_SESSION['CreateShop']['Step'] == 1){
			if($InvalidServerData)
				$_SESSION['Index']->assign_say('ADM_SERVSET_INVALID_SERVER');

			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_HOSTNAME_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_APIUSER_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_APIPASSWORD_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_APIPASSWORDREPEAT_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_APISALT_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_APISALT_INFO');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_APISALT');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SUBMIT_LABEL');

			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_HOSTNAME_SAVE', $_SESSION['CreateShop']['hostname']);
			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_APIUSER_SAVE', $_SESSION['CreateShop']['apiuser']);
			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_APIPW', $_SESSION['CreateShop']['apipassword']);
			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_APISALT', $_SESSION['CreateShop']['apisalt']);
			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_PORT_SAVE', $_SESSION['CreateShop']['port']);
		}
		elseif($_SESSION['CreateShop']['Step'] == 2){
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_NEWCODE_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_NEWCODE_INFO');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_INFO', array($_SESSION['CreateShop']['apisalt']));
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_FAQ_LINK');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SENDNEWCODE_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_FAQ_LABEL');
			$_SESSION['Index']->assign_direct('ADM_SERVSET_NEWSERV_API_SALT', $_SESSION['CreateShop']['apisalt']);
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_EDITDATA_LABEL');


			if($_GET['newcode'] && !$_SESSION['CreateShop']['code']){
				//Es wurde ein neuer Code angefordert, der aber nicht erfolgreich übertragen werden konnte
				$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_CODESEND_ERROR');
				//var_dump($_SESSION['CreateShop']);
			}
			elseif($_SESSION['CreateShop']['code']){
				$_SESSION['Index']->assign_direct('ADM_SERVSET_CODESEND', true); //es wurde mindestens ein Code zum Server gesendet
				if($_GET['newcode']){ //Es wurde eine neuer Code angefordert
					$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_CODESEND_SUCCESSFUL');
				}

				if($InvalidCode){ //Vom Benutzer wurde ein falscher Code eingegeben
					$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_WRONGCODE');
				}

				$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_VERIFYINGCODE_LABEL'); //Label für das Eingabefeld
				$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SUBMIT_LABEL'); //Label für den Bestätigen-Button
			}
			else{
				$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_NEWCODE_LABEL');
			}
		}
		elseif($_SESSION['CreateShop']['Step'] == 3){
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SUBMIT_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_EDITDATA_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SERVERLABEL_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SUBDOMAIN_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SERVERLABEL_LABEL_INFO');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SUBDOMAIN_LABEL_INFO');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_TEMPLATE_LABEL');
			if($InvalidSubdomain){
				$_SESSION['Index']->assign_say('ADM_SERVSET_ERROR_SUBDOMAIN');
			}
			if($InvalidServerlabel){
				$_SESSION['Index']->assign_say('ADM_SERVSET_ERROR_SERVERLABEL');
			}
			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_TEMPLATES',$templates);

			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_SERVERLABEL', $_SESSION['CreateShop']['serverlabel']);
			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_SUBDOMAIN', $_SESSION['CreateShop']['subdomain']);
			$_SESSION['Index']->assign('ADM_SERVSET_NEWSERV_TEMPLATE', $_SESSION['CreateShop']['template']);
		}
		else{
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_FINISH_CONFIG_HEADER');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_FINISH_CONFIG');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_CONGRATULATIONS_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SUCCESSFUL_LABEL');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_CONGRATULATIONS');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_SUCCESSMESSAGE');
			$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_TO_ADMINISTRATION');
			$_SESSION['Index']->assign_direct('ADM_SERVSET_NEWSERV_ID', $RowId);
		}

		#region Abbrechen-Button anzeigen, falls der Admin bereits einen oder mehrere Shops eingerichtet hat
		if($_SESSION['Index']->adminShop != null){
			$_SESSION['Index']->assign_say('ADM_SERVSET_CANCEL');
		}
		#end
	}
	#end
	}
}
?>