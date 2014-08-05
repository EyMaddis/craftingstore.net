<?php
defined('_MCSHOP') or die("Security block!");
// VERIFIZIERUNGSCODE - "test" rausnehmen// CUSTOMERID und MinecraftName dynamisch!!!
class ServerSettings extends aDisplayable{
	public function prepareDisplay()
	{
		//server id we deal with
		if (isset($_GET['server']))
		$serverid = $_GET['server'];

		/////////////////
		// Edit Server //
		/////////////////
		$update = true;
		//current server id
		$serverid = $_SESSION['Index']->adminShop->getId();
		if (!empty($_POST) && isset($_POST['generate']))
		{
			//generates the random salt
			$rand = random_string_by_length(32);
			$changeserversalt=true;
		}
		elseif (!empty($_POST)  && isset ($_POST['savechanges']))
		{
			if (!empty($_POST['serverhost']) && !empty($_POST['serverport']) && !empty($_POST['serveruser']))
			{
				$host = mysql_real_escape_string($_POST['serverhost']);
				$port = mysql_real_escape_string($_POST['serverport']);
				$user = mysql_real_escape_string($_POST['serveruser']);
				$salt = mysql_real_escape_string($_POST['serversalt']);
			}
			else
			{
				$update = false;
				if (empty($_POST['serverhost']))
				{
						$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_EDIT_HOSTNAME', true);
					}
					if (empty($_POST['serveruser']))
					{
						$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_EDIT_APIUSER', true);
					}
					if (empty($_POST['serverport']))
					{
						$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_EDIT_PORT', true);
					}
			}
			if (!empty($_POST['serverpassword']) && !empty($_POST['serverpasswordrepeat']))
			{
				$pw = $_POST['serverpassword'];
				$pw2 = $_POST['serverpasswordrepeat'];
				if ($pw != $pw2)
				{
					$update=false;
					$_SESSION['Index']->assign_direct('ADM_SERVSET_ERROR_EDIT_PW', true);
				}
			}
			else
			{
				$pw = $_SESSION['Index']->db->fetchOne("SELECT ServerPassword FROM mc_shops WHERE Id='$serverid'");
			}

			if ($update)
			{
				$_SESSION['Index']->db->query("UPDATE mc_shops SET ServerHost='$host', ServerPort='$port', ServerUser='$user', ServerSalt='$salt', ServerPassword='$pw' WHERE Id='$serverid' Limit 1");
				$_SESSION['Index']->assign_say('ADM_SERVSET_UPDATE_SUCCESSFUL');
			}
			else
			{
				$_SESSION['Index']->assign_say('ADM_SERVSET_UPDATE_FAILED');
			}
		}

		$_SESSION['Index']->assign_say('ADM_SERVSET_ADDSERVER_BOX_TITLE');
		$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_LABEL');
		$_SESSION['Index']->assign_say('ADM_SERVSET_NEWSERV_GENERATE_LABEL');
		//title
		$_SESSION['Index']->assign_say('ADM_SERVSET_EDITSERVER_BOX_TITLE');
		$_SESSION['Index']->assign_say('ADM_SERVSET_EDIT_SUBMIT');
		$_SESSION['Index']->assign_say('ADM_SERVSET_SERVERPASSWORD_INFO');
		$_SESSION['Index']->assign_say('ADM_BACK');
		//labels
		$_SESSION['Index']->assign_say('ADM_SERVSET_SERVERHOST_LABEL');
		$_SESSION['Index']->assign_say('ADM_SERVSET_SERVERPORT_LABEL');
		$_SESSION['Index']->assign_say('ADM_SERVSET_SERVERUSER_LABEL');
		$_SESSION['Index']->assign_say('ADM_SERVSET_SERVERPASSWORD_LABEL');
		$_SESSION['Index']->assign_say('ADM_SERVSET_SERVERPASSWORD_REPEAT_LABEL');
		$_SESSION['Index']->assign_say('ADM_SERVSET_SERVERSALT_LABEL');
		// Get existing server-data
		$serverdata = $_SESSION['Index']->db->fetchOneRow("SELECT ServerHost, ServerPort, ServerUser, ServerSalt
													FROM mc_shops 
													WHERE Id='$serverid'");
		$_SESSION['Index']->assign('ADM_SERVSET_SERVERHOST_VALUE', $serverdata->ServerHost);
		$_SESSION['Index']->assign('ADM_SERVSET_SERVERPORT_VALUE', $serverdata->ServerPort);
		$_SESSION['Index']->assign('ADM_SERVSET_SERVERUSER_VALUE', $serverdata->ServerUser);
		if (isset($rand))
			$_SESSION['Index']->assign('ADM_SERVSET_SERVERSALT_VALUE', $rand);
		else
			$_SESSION['Index']->assign('ADM_SERVSET_SERVERSALT_VALUE', $serverdata->ServerSalt);
	}
}
?>