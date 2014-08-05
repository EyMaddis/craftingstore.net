<?php
defined('_MCSHOP') or die("Security block!");

class Topmenu extends aDisplayable
{
	public function prepareDisplay()
	{			

		//get Current shop ID
		$currShop = $_SESSION['Index']->adminShop->getId();

		// Gibt true zurück, wenn das Item mit der angegebenen Id nach oben verschoben werden darf (position > 1)
		function moveUpAllowed($IdToMove, $CurrentPosition){
			if(!isNumber($IdToMove) || !isNumber($CurrentPosition))
				return false;
			return $_SESSION['Index']->db->fetchOne("SELECT Position FROM mc_topmenu WHERE Id='$IdToMove' AND Position='$CurrentPosition' AND ShopId='{$_SESSION['Index']->adminShop->getId()}'") > 1;
		}
		// Gibt true zurück, wenn das Item mit der angegebenen Id nach unten verschoben werden darf (position < count(*))
		function moveDownAllowed($IdToMove, $CurrentPosition){
			if(!isNumber($IdToMove) || !isNumber($CurrentPosition))
				return false;
			return $_SESSION['Index']->db->fetchOne("SELECT Position FROM mc_topmenu WHERE Id='$IdToMove' AND Position='$CurrentPosition' AND ShopId='{$_SESSION['Index']->adminShop->getId()}'") < $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_topmenu WHERE ShopId='{$_SESSION['Index']->adminShop->getId()}'");
		}
		
		//new entry / edit entry
		if(count($_POST)){
			if(!isset($_POST['name'])){
				$fehler = 1;
			}
			elseif(!isset($_POST['link'])){
				$fehler = 1;
			}
			elseif(!preg_match('/^(((https?|ftps?):\/\/)|(mailto:)).+/', $_POST['link'])){
				$_POST['link'] = 'http://'.$_POST['link'];
				$fehler = 1;
			}
			elseif(!isset($_POST['target'])){
				echo $fehler = 1;
			}
			elseif(!isNumber($_GET['id']) && $_GET['id'] != -1){
				$fehler = 1;
			}
			if($fehler){
				$_SESSION['Index']->assign_say('ADM_CONTENT_TOPNAV_LINK_ERROR');
			}
			else{
				$name = mysql_real_escape_string($_POST['name']);
				$link = mysql_real_escape_string($_POST['link']);
				$target = mysql_real_escape_string($_POST['target']);
				$id = $_GET['id'];
				
				if ($_GET['id'] == -1){
					$newPos = $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_topmenu WHERE ShopId='$currShop'")+1;
					$_SESSION['Index']->db->insert("INSERT INTO mc_topmenu (ShopId, Name, Link, Position, Target) VALUES ('$currShop', '$name', '$link', '$newPos', '$target')");
				}
				else{
					$_SESSION['Index']->db->update("UPDATE mc_topmenu SET Name='$name', Link='$link', Target='$target' WHERE Id='$id'");
				}
				$saved = true;
			}
		}
		
		//moveUp
		if (moveUpAllowed($_GET['moveup'], $_GET['pos'])){
			$_SESSION['Index']->db->query("LOCK TABLES mc_topmenu WRITE");
			$idToMove = $_GET['moveup'];
			$posOfMove = $_SESSION['Index']->db->fetchOne("SELECT Position FROM mc_topmenu WHERE Id='$idToMove'");
			$posOfMoveM = $posOfMove - 1;
			$_SESSION['Index']->db->update("UPDATE mc_topmenu SET Position='$posOfMove' WHERE ShopId='$currShop' AND Position='$posOfMoveM'");
			$_SESSION['Index']->db->update("UPDATE mc_topmenu SET Position='$posOfMoveM' WHERE Id='$idToMove'");
			$_SESSION['Index']->db->query("UNLOCK TABLES");
		}
		//moveDown
		elseif (moveDownAllowed($_GET['movedown'], $_GET['pos'])){
			$_SESSION['Index']->db->query("LOCK TABLES mc_topmenu WRITE");		
			$idToMove = $_GET['movedown'];
			$posOfMove = $_SESSION['Index']->db->fetchOne("SELECT Position FROM mc_topmenu WHERE Id='$idToMove'");
			$posOfMoveM = $posOfMove + 1;
			$_SESSION['Index']->db->update("UPDATE mc_topmenu SET Position='$posOfMove' WHERE ShopId='$currShop' AND Position='$posOfMoveM'");
			$_SESSION['Index']->db->update("UPDATE mc_topmenu SET Position='$posOfMoveM' WHERE Id='$idToMove'");
			$_SESSION['Index']->db->query("UNLOCK TABLES");
		}
		
		//delete
		if (isNumber($_GET['delete'])){
			$IdToDelete = $_GET['delete'];
			$oldPosition = $_SESSION['Index']->db->fetchOne("SELECT Position FROM mc_topmenu WHERE Id='$IdToDelete'");
			$_SESSION['Index']->db->query("DELETE FROM mc_topmenu WHERE Id='$IdToDelete'");
			$_SESSION['Index']->db->query("UPDATE mc_topmenu SET Position=Position-1 WHERE ShopId='$currShop' AND Position>'$oldPosition'");
		}

		//edit
		if ((isNumber($_GET['id']) || $_GET['id'] == -1 || $fehler) && !$saved){
			$_SESSION['Index']->assign("ADM_CONTENT_TOPNAV_EDIT_BOOL", true);
			$_SESSION['Index']->assign_say('ADM_CONTENT_TOPNAV_NEWLINK_TITLE');
			$_SESSION['Index']->assign_say('ADM_CONTENT_TOPNAV_BACK');
			
			if($_GET['id'] == -1){
				$_SESSION['Index']->assign_say('ADM_CONTENT_TOPNAV_SUBMITLINK');
				$_SESSION['Index']->assign('ADM_CONTENT_TOPNAV_EDIT_VALUE_ID', -1);
				$_SESSION['Index']->assign('ADM_CONTENT_TOPNAV_EDIT_VALUE_NAME', $_POST['name']);
				$_SESSION['Index']->assign('ADM_CONTENT_TOPNAV_EDIT_VALUE_LINK', $_POST['link']);
				$_SESSION['Index']->assign('ADM_CONTENT_TOPNAV_EDIT_VALUE_TARGET', $_POST['target']);
			}
			elseif(isNumber($_GET['id'])){
				$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_UPDATELINK");
				$idToEdit = $_GET['id'];
				$_SESSION['Index']->assign("ADM_CONTENT_TOPNAV_EDIT_VALUE_ID", $idToEdit);
				if(count($_POST)){
					$_SESSION['Index']->assign('ADM_CONTENT_TOPNAV_EDIT_VALUE_NAME', $_POST['name']);
					$_SESSION['Index']->assign('ADM_CONTENT_TOPNAV_EDIT_VALUE_LINK', $_POST['link']);
					$_SESSION['Index']->assign('ADM_CONTENT_TOPNAV_EDIT_VALUE_TARGET', $_POST['target']);
				}
				else{
					$row = $_SESSION['Index']->db->fetchOneRow("SELECT Id, Name, Link, Target FROM mc_topmenu WHERE Id='$idToEdit'");
					$_SESSION['Index']->assign("ADM_CONTENT_TOPNAV_EDIT_VALUE_NAME", $row->Name);
					$_SESSION['Index']->assign("ADM_CONTENT_TOPNAV_EDIT_VALUE_LINK", $row->Link);
					$_SESSION['Index']->assign("ADM_CONTENT_TOPNAV_EDIT_VALUE_TARGET", $row->Target);
				}
			}
		}

		//Loading Links		
		
		$number = $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_topmenu WHERE ShopId='$currShop'");
		
		$this->links = array();
		$sql = "SELECT Id, Name, Link, Target, Position FROM mc_topmenu WHERE ShopId='$currShop' ORDER BY Position";
		
		$n = 1;
		foreach($_SESSION['Index']->db->iterate($sql) as $row){
			$first = false;
			$last = false;
			if($n == 1) $first = true;
			if($n == $number) $last = true;

			$this->links[] = array( 'Id' => $row->Id,
				'Name' => $row->Name,
				'Link' => $row->Link,
				'Target' => $row->Target,
				'Position' => $row->Position,
				'first' => $first,
				'last' => $last);

				$n++;
		}
		if($n == 1){
			$_SESSION['Index']->assign_say('ADM_CONTENT_NO_TOPNAV_LINKS');
		}
		else{
			$_SESSION['Index']->assign('ADM_CONTENT_TOPNAV_LINKS', $this->links);
		}
		

		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_TITLE");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_NAME");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_LINK");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_TARGET");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_SUBMITPOSITION");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_NEWENTRY");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_EDIT");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_DELETE");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_MOVEUP");
		$_SESSION['Index']->assign_say("ADM_CONTENT_TOPNAV_MOVEDOWN");
	}
}
?>