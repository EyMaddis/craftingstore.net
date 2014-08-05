<?php
defined('_MCSHOP') or die("Security block!");

class ItemGroups extends aDisplayable
{
	private $nstree = null;
	public function __construct()
	{
		$this->nstree = $_SESSION['Index']->createNestedSet($_SESSION['Index']->adminShop);
	}
	public function prepareDisplay()
	{
		if(isNumber($_GET['node']) || $_GET['node'] == '-1'){
			$node = $_GET['node'];
		}
		#region Node neu erstellen/bearbeiten
		if($_GET['mode'] == 'edit' && $node){
			$_SESSION['Index']->assign_direct('ADM_ITEM_GROUPS_MODE',1);

			#region Wenn Daten übergeben werden, prüfen, ob diese gültig sind
			if(count($_POST))
			{
				if(!$_POST['label'])
				{
					$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_ADD_ERROR','ADM_ITEM_GROUP_ADD_ERROR_LABEL');
				}
				elseif(!isNumber($_POST['group'],true) || ($_POST['group'] != '0' && $_POST['group'] != $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_productGroups WHERE Id='{$_POST['group']}' LIMIT 1")))
				{
					$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_ADD_ERROR','ADM_ITEM_GROUP_ADD_ERROR_GROUP');
				}
				elseif($this->nstree->containsImmediateName($_POST['group'], $_POST['label'], $node))
				{
					$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_ADD_ERROR','ADM_ITEM_GROUP_ADD_ERROR_LABEL_DOUBLE');
				}
				else
				{
					$save = true;
				}
			}
			#endregion

			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_CHANGE_NAME');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_CHANGE_DESCRIPTION');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_CHANGE_ROOT_GROUP');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_CHANGE_IS_ENABLED');
			#region Neues Node erstellen
			if($node == -1)
			{
				$_SESSION['Index']->assign_say('ADM_ITEM_GROUPS_TITLE','ADM_ITEM_GROUPS_NEW_HEADER');

				if($save)
				{
					$this->nstree->insertChildNode($_POST['label'], $_POST['group'], $_POST['enabled'], $_POST['description']);
					$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_ADD_DONE',array($_POST['label']));
				}
			}
			#endregion
			#region Node bearbeiten
			else
			{
				$_SESSION['Index']->assign_say('ADM_ITEM_GROUPS_TITLE','ADM_ITEM_GROUPS_EDIT_HEADER');

				$nodeInfo = $_SESSION['Index']->db->fetchOneRow("SELECT * FROM mc_productGroups WHERE Id='$node' AND ShopId='{$_SESSION['Index']->adminShop->getId()}'");
				if(!$nodeInfo)
				{
					$_SESSION['Index']->assign_say('ADM_ITEM_GROUPS_NODE_NOT_FOUND_ERROR');
					$_SESSION['Index']->assign_say('BACK');
				}
				elseif($save)
				{
					#region prüfen, ob die Gruppe in die angegebene Untergruppe verschoben werden soll und auch darf
					$curParentNode = $this->nstree->getParentNode($node);
					if($curParentNode != $_POST['group']) //Das aktuelle und neue Elternelement unterscheiden sich
					{
						if(!$this->nstree->containsNode($node, $_POST['group'])) //Die neue Gruppe ist eine Untergruppe zum editierten Node
						{
							$this->nstree->moveNodeToChild($node, $_POST['group']);
						}
						else //Das neue ParentNode ist ungültig, es darf nicht gespeichert werden
						{
							$save = false;
						}
					}
					#endregion
					if($save)
					{
						$_SESSION['Index']->db->query("UPDATE mc_productGroups SET Label='".mysql_real_escape_string($_POST['label'])."', Description='".mysql_real_escape_string($_POST['description'])."', Enabled='".($_POST['enabled']?1:0)."' WHERE ShopId='{$_SESSION['Index']->adminShop->getId()}' AND Id='$node' LIMIT 1");
						$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_EDIT_DONE',array($_POST['label']));
					}
				}
			}
			#endregion
			#region Die aktuellen Werte ausgeben
			if(!$save)
			{
				$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_SAVE');
				$_SESSION['Index']->assign_direct('ADM_ITEM_GROUP_NODE',$node);
				$_SESSION['Index']->assign('ADM_ITEM_GROUP_ID',isset($_POST['group'])?$_POST['group']:$this->nstree->getParentNode($node)->Id);
				$_SESSION['Index']->assign('ADM_ITEM_GROUP_LABEL',isset($_POST['label'])?$_POST['label']:$nodeInfo->Label);
				$_SESSION['Index']->assign('ADM_ITEM_GROUP_DESCRIPTION',isset($_POST['description'])?$_POST['description']:$nodeInfo->Description);
				$_SESSION['Index']->assign('ADM_ITEM_GROUP_ENABLED',isset($_POST['enabled'])?($_POST['enabled']?1:0):$nodeInfo->Enabled);
				$_SESSION['Index']->assign('ADM_ITEM_GROUPS_LIST',$this->nstree->treeAsArray(($node>0?$node:null), true, true, 5, 10));

			}
			#endregion
		}
		#endregion
		#region Node löschen
		elseif($_GET['mode'] == 'del' && $node && ($label =/*EIN GLEICHHEITSZEICHEN IST RICHTIG*/ $_SESSION['Index']->db->fetchOne("SELECT Label FROM mc_productGroups WHERE Id='$node' AND ShopId='{$_SESSION['Index']->adminShop->getId()}'")))
		{
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUPS_TITLE','ADM_ITEM_GROUPS_DELETE_NODE_HEADER');
			$_SESSION['Index']->assign_direct('ADM_ITEM_GROUPS_MODE',2);
			if($_GET['do'])
			{
				$this->nstree->deleteNode($node);
				$_SESSION['Index']->db->query("UPDATE mc_products SET Enabled='0' WHERE ShopId='{$_SESSION['Index']->adminShop->getId()}' AND GroupId NOT IN (SELECT Id FROM mc_productGroups WHERE ShopId='{$_SESSION['Index']->adminShop->getId()}')");
				$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_DELETE_TEXT','ADM_ITEM_GROUP_DELETE_ACCEPTED',array($label));
				$_SESSION['Index']->assign_direct('ADM_ITEM_GROUP_DELETED',1);
			}
			else
			{
				$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_DELETE_TEXT','ADM_ITEM_GROUP_DELETE_QUESTION',array($label));
				$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_DELETE_ACCEPT');
				$_SESSION['Index']->assign_direct('ADM_ITEM_GROUP_NODE',$node);
			}
		}
		#endregion
		#region Übersicht anzeigen
		else
		{
			if($_GET['mode'] == 'copy'){
				$_SESSION['Index']->db->query("DELETE FROM mc_productGroups WHERE ShopId='{$_SESSION['Index']->adminShop->getId()}'");
				$_SESSION['Index']->db->query("INSERT INTO mc_productGroups (Id,ShopId,Label,lft,rgt,Description,Enabled) SELECT Id,'{$_SESSION['Index']->adminShop->getId()}',Label,lft,rgt,Description,Enabled FROM mc_productGroups WHERE ShopId='0'");
			}
			if($_GET['mode'] == 'move' && $node && isNumber($lft = $_GET['lft']) && isNumber($rgt = $_GET['rgt'])){
				if($_GET['d'] == 'up' && ($node == $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_productGroups WHERE Id='$node' AND lft='$lft' AND rgt='$rgt'")))
				{
					$this->nstree->moveLft($node);
				}
				elseif($_GET['d'] == 'down' && ($node == $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_productGroups WHERE Id='$node' AND lft='$lft' AND rgt='$rgt'")))
				{
					$this->nstree->moveRgt($node);
				}
			}
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUPS_TITLE','ADM_ITEM_GROUPS_LIST_HEADER');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_NEW');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_CHANGE');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_DELETE');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_DEACTIVATED');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_MOVE_UP');
			$_SESSION['Index']->assign_say('ADM_ITEM_GROUP_MOVE_DOWN');

			$nodeArray = $this->nstree->treeAsArray(null,false,true,10);
			if(count($nodeArray) == 0){
				$_SESSION['Index']->assign_say('ADM_ITEM_GROUPS_NO_ITEMS');
				$_SESSION['Index']->assign_say('ADM_ITEM_GROUPS_COPY');
				$nodeArray = $this->nstree->treeAsArray(null,false,true,10,null,true);
			}
			$_SESSION['Index']->assign('ADM_ITEM_GROUPS_LIST', $nodeArray);
		}
		#endregion
		$_SESSION['Index']->assign_say('BACK');
	}
}
?>