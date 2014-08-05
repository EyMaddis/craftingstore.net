<?php
/**
 * A class to use nested sets easiely
 * @author Mark Stuppacher <mark@happynet.at>
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 * @version 0.6
 * @
 *
 */
class NestedSet
{
#region Properties
	/**
	 * Mysqli object
	 * @var object
	 */
	protected $db;

	/**
	 * Name of the database table
	 * @var string
	 */
	public $table='';

	/**
	 * Primary key of the database table
	 * @var string
	 */
	public $pk='';

	/**
	 * Namefield in the database table
	 * @var unknown_type
	 */
	public $name='';

	/**
	 * ShopId of the database table
	 * @var integer
	 */
	public $shop=0;

	private $locked = false;
#endregion
#region Methods
	private function _LockTables(){
		if(!$locked)
		{
			$this->db->query("LOCK TABLES {$this->table} WRITE, {$this->table} AS n WRITE, {$this->table} AS p WRITE");
			$locked = true;
		}
	}
	private function _UnlockTables(){
		$this->db->query("UNLOCK TABLES");
		$locked = false;
	}
	/**
	 * Stores a Mysqli object for further use
	 * @param object $mysqli Mysqli object
	 * @return boolean true
	 */
	public function __construct($mysqli){
		$this->db=$mysqli;
		return true;
	}
	/**
	 * Creates the root node
	 * @param string $name Name of the new node
	 * @return boolean true
	 *
	public function createRootNode($name){
		$this-> _LockTables();
		$sql="SELECT rgt FROM {$this->table} WHERE ShopId='{$this->shop->getId()}' ORDER BY rgt DESC LIMIT 1";
		$rgt=$this->db->fetchOne($sql);
		$lft=$rgt+1;
		$rgt=$lft+1;

		$sql="INSERT INTO {$this->table} ({$this->name},lft,rgt,ShopId) VALUES ('".mysql_real_escape_string($name)."','$lft','$rgt','{$this->shop->getId()}')";
		$this->db->query($sql);
		$this->_UnlockTables();
		return true;
	}
	/**
	 * Creates a new node
	 * @param string $name name of the new node
	 * @param integer $lft lft of parent node
	 * @param integer $rgt	rgt of parent node
	 * @return boolean	true
	 */
	protected function insertNode($name,$lft,$rgt,$enabled = false,$description = null){
		$this->_LockTables();
		$this->db->query("UPDATE {$this->table} SET rgt=rgt+2 WHERE ShopId='{$this->shop->getId()}' AND rgt>='$rgt'");
		$this->db->query("UPDATE {$this->table} SET lft=lft+2 WHERE ShopId='{$this->shop->getId()}' AND lft>'$rgt'");
		$id = $this->db->insert("INSERT INTO {$this->table} ({$this->name},lft,rgt,ShopId,Enabled,Description) VALUES ('".mysql_real_escape_string($name)."','$rgt','$rgt'+1,'{$this->shop->getId()}','".($enabled?1:0)."','".mysql_real_escape_string($description)."')");
		$this->_UnlockTables();
		return $id;
	}
	/**
	 * Gets an object with all data of a node
	 * @param integer $id id of the node
	 * @return object object with node-data (id,lft,rgt)
	 */
	protected function getNode($id){
		return $this->db->fetchOneRow("SELECT n.*, COUNT(*)-1 AS Level FROM {$this->table} AS n, {$this->table} AS p
			WHERE n.{$this->pk}='$id' AND n.ShopId='{$this->shop->getId()}' AND p.ShopId='{$this->shop->getId()}' AND n.lft BETWEEN p.lft AND p.rgt
			GROUP BY n.lft
			ORDER BY n.lft");
	}
	/**
	 * Gets an object with all data of a node
	 * @param integer $id id of the node
	 * @return object object with parentNode-data (id,lft,rgt,Label)
	 */
	public function getParentNode($id){
		return $this->db->fetchOneRow("SELECT n.{$this->pk},n.{$this->name},n.lft,n.rgt FROM {$this->table} AS n LEFT JOIN {$this->table} AS p ON p.{$this->pk}='$id' WHERE n.lft<p.lft AND n.rgt>p.rgt AND n.ShopId='{$this->shop->getId()}' AND p.ShopId='{$this->shop->getId()}' ORDER BY (n.rgt-p.rgt) ASC LIMIT 1");
	}
	/**
	 * Checks if parentNode contains childNode
	 * @param integer $parentNode id of the parentNode to search in
	 * @param integer $childNode id of the childNode to search for
	 * @return true if parentNode contains childNode
	 */
	public function containsNode($parentNode,$childNode){
		$p = $this->getNode($parentNode);
		if(!$p)return false;
		$sql = "SELECT n.{$this->pk}
FROM {$this->table} AS n
WHERE n.lft BETWEEN {$p->lft}+1 AND {$p->rgt}-1 AND {$this->pk}='$childNode' AND ShopId='{$this->shop->getId()}'";
		return (bool)$this->db->fetchOne($sql);
	}
	/**
	 * Checks wether parentNode contains a Node with the name $childNodeName where childNode is a direkt Child of parentNode and has not the id $excludeNode
	 * @param integer $parentNode id of the parent Node to search in. user 0 for the root level
	 * @param string $childNodeName name of the childNode to search for
	 * @param integer $excludeNode the id of the node which should be ignored
	 * @return true if parentNode contains childNode, otherwise false
	 */
	public function containsImmediateName($parentNode,$childNodeName,$excludeNode){
		if(!isNumber($parentNode) || !isNumber($excludeNode)){
			return false;
		}
		else
		{
			$p = $this->getNode($parentNode);
			return $_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM (
				SELECT n.*, Count(*)-2 AS Level
				FROM
					{$this->table} AS n,
					{$this->table} AS p
				WHERE n.Id<>'$excludeNode' AND n.lft>{$p->lft} AND n.rgt<{$p->rgt} AND n.ShopId='{$this->shop->getId()}' AND p.ShopId='{$this->shop->getId()}'
				AND n.lft BETWEEN p.lft AND p.rgt
				GROUP BY n.lft
				ORDER BY n.lft
			) AS allChilds
			WHERE Level='{$p->Level}' AND Label='".mysql_real_escape_string($childNodeName)."'");
		}
		return 1;
	}
	/**
	 * Gets an array with all data of a all immediate subnodes
	 * @param integer $id id of the node
	 * @return array with node-data (id,lft,rgt,Label)
	 *
	public function getImmediateSubNodes($id){
		$sql = "SELECT lft,rgt INTO @parent_left,@parent_right FROM {$this->table} WHERE {$this->pk}='$id' AND ShopId='{$this->shop->getId()}';
SELECT n.{$this->pk},n.lft,n.rgt,n.Label
FROM {$this->table} AS n
LEFT JOIN {$this->table} AS ancestor ON
	ancestor.lft BETWEEN @parent_left+1 AND @parent_right-1 AND
	n.lft BETWEEN ancestor.lft+1 AND ancestor.rgt-1
	AND ancestor.ShopId='{$this->shop->getId()}'
WHERE
	n.lft BETWEEN @parent_left+1 AND @parent_right-1 AND
	ancestor.{$this->pk} IS NULL AND ShopId='{$this->shop->getId()}'";

		$result = array();
		foreach($this->db->iterate($sql) as $row)
		{
			$result[] = $row;
		}
		return $result;
	}
	/**
	 * Gets an object with all data of the last immediate subnode
	 * @param integer $id id of the node
	 * @return object with node-data (id,lft,rgt,Label)
	 *
	public function rightMostSiblingSubnode($id){
		$sql = "SELECT lft,rgt INTO @parent_left,@parent_right FROM {$this->table} WHERE {$this->pk}='$id' AND ShopId='{$this->shop->getId()}';
SELECT n.{$this->pk},n.lft,n.rgt,n.Label
FROM {$this->table} AS n
LEFT JOIN {$this->table} AS ancestor ON
	ancestor.lft BETWEEN @parent_left+1 AND @parent_right-1 AND
	n.lft BETWEEN ancestor.lft+1 AND ancestor.rgt-1
	AND ancestor.ShopId='{$this->shop->getId()}'
WHERE
	n.lft BETWEEN @parent_left+1 AND @parent_right-1 AND
	ancestor.{$this->pk} IS NULL AND ShopId='{$this->shop->getId()}'
ORDER BY n.lft DESC
LIMIT 1";
		return $this->db->fetchOneRow($sql);
	}
	/**
	 * Creates a new child node of the node with the given id
	 * @param string $name name of the new node
	 * @param integer $parent id of the parent node
	 * @return boolean true
	 */
	public function insertChildNode($name,$parent,$enabled = false,$description = null){
		$this->_LockTables();
		$p_node = $this->getNode($parent);
		if(!$p_node){
			$this->_UnlockTables();
			return false;
		}
		$id = $this->insertNode($name,$p_node->lft,$p_node->rgt,$enabled,$description);
		$this->_UnlockTables();
		return $id;
	}
	/**
	 * Creates a multi-dimensional array of the whole tree
	 * @return array multi-dimenssional array of the whole tree
	 */
	public function getTree($ExcludeId=null,$withRoot=false,$rootLabel='root',$withDisabled=false,$getDefault=false){
		$sql="SELECT n.{$this->pk}, n.{$this->name}, COUNT(*)-".($withRoot?1:2)." AS Level, ".($getDefault?'\'0\' AS Enabled':'n.Enabled').", n.lft,n.rgt FROM {$this->table} AS n, {$this->table} AS p WHERE n.ShopId='".($getDefault?'0':$this->shop->getId())."' AND p.ShopId='".($getDefault?'0':$this->shop->getId())."' AND n.lft BETWEEN p.lft AND p.rgt".($ExcludeId&&($node=$this->getNode($ExcludeId))?" AND n.lft NOT BETWEEN '{$node->lft}' AND '{$node->rgt}'":'')." GROUP BY n.lft ORDER BY n.lft";

		$isRoot = true;
		$disabledLevel = -1;
		$tree = array();
		foreach($this->db->iterate($sql) as $row)
		{
			if(!$withDisabled)
			{
				if($row->Level <= $disabledLevel)
				{
					$disabledLevel = -1;
				}
				if($disabledLevel > -1) continue;
				if(!$row->Enabled)
				{
					$disabledLevel = $row->Level;
					continue;
				}
			}
			if($withRoot) //ggf. den ersten Wert (root-Node) überspringen
			{
				if($isRoot) $row->Label = $rootLabel;
				$tree[] = $row;
			}
			else
			{
				$withRoot = true;
			}
			$isRoot = false;
		}
		return $tree;
	}
	public function getSubTree($SubFromId,$ExcludeId = null,$withDisabled = false){
		$nodeInfo = $this->getNode($SubFromId);
		if(!$nodeInfo) return false;
		$lft = $nodeInfo->lft;
		$rgt = $nodeInfo->rgt;
		$sql="SELECT n.{$this->pk},n.{$this->name},COUNT(*)-".($withRoot?1:2)." AS Level,n.Enabled FROM {$this->table} AS n,{$this->table} AS p WHERE n.ShopId='{$this->shop->getId()}' AND p.ShopId='{$this->shop->getId()}' AND n.lft>'$lft' AND n.rgt<'$rgt' AND n.lft BETWEEN p.lft AND p.rgt".($ExcludeId&&($node=$this->getNode($ExcludeId))?" AND n.lft NOT BETWEEN '{$node->lft}' AND '{$node->rgt}'":'')." GROUP BY n.lft ORDER BY n.lft";

		$tree = array();
		$disabledLevel = -1;
		foreach($this->db->iterate($sql) as $row)
		{
			if(!$withDisabled)
			{
				if($row->Level <= $disabledLevel)
				{
					$disabledLevel = -1;
				}
				if($disabledLevel > -1) continue;
				if(!$row->Enabled)
				{
					$disabledLevel = $row->Level;
					continue;
				}
			}
			$tree[] = $row;
		}
		return $tree;
	}
	public function treeAsArray($ExcludeId=null,$withRoot=false,$withDisabled=false,$padding_start=null,$padding_add=null,$getDefault=false){
		#region default-Werte
		if($padding_start===null) $padding_start = 0;
		if($padding_add===null) $padding_add = 15;
		#end
		$ADM_ITEM_GROUPS_LIST = array();
		$tree = $this->getTree($ExcludeId,$withRoot,($withRoot?$_SESSION['Index']->say('ADM_ITEM_GROUPS_TOP_NODE',null,false):''),$withDisabled,$getDefault);

		foreach($tree as $row)
		{
			$ADM_ITEM_GROUPS_LIST[] = array(
				'Id' => $row->{$this->pk},
				'Label' => $row->{$this->name},
				'Enabled' => $row->Enabled,
				'Ebene' => $row->Level*$padding_add+$padding_start,
				'lft' => $row->lft,
				'rgt' => $row->rgt
			);
		}
		return $ADM_ITEM_GROUPS_LIST;
	}
	/**
	 * Get the HTML code for an unordered list of the tree
	 * @return string HTML code for an unordered list of the whole tree
	 */
	public function treeAsHtml(){
		$tree=$this->getTree();
		$html="<ul>\n";
		for($i=0; $i<count($tree); $i++){
			$html .= "<li>".$tree[$i]->{$this->name};
			if ($tree[$i]->Level<$tree[$i+1]->Level){
				$html .= "\n<ul>\n";
			}elseif ($tree[$i]->Level == $tree[$i+1]->Level){
				$html .= "</li>\n";
			}else {
				$diff=$tree[$i]->Level-$tree[$i+1]->Level;
				$html .= str_repeat("</li>\n</ul>\n",$diff)."</li>\n";
			}
		}
		$html .= "</ul>\n";
		return $html;
	}
	/**
	 * Deletes a node an all it's children
	 * @param integer $id id of the node to delete
	 * @return boolean true
	 */
	public function deleteNode($id){
		$this->_LockTables();
		$node=$this->getNode($id);
		if(!$node)
			return false;
		
		$sql = array(
			"DELETE FROM {$this->table} WHERE ShopId='{$this->shop->getId()}' AND lft BETWEEN {$node->lft} AND {$node->rgt}",
			"UPDATE {$this->table} SET lft=lft-ROUND(({$node->rgt}-{$node->lft}+1)) WHERE ShopId='{$this->shop->getId()}' AND lft>{$node->rgt}",
			"UPDATE {$this->table} SET rgt=rgt-ROUND(({$node->rgt}-{$node->lft}+1)) WHERE ShopId='{$this->shop->getId()}' AND rgt>{$node->rgt}"
		);
		foreach($sql as $query)
			$this->db->query($query);
		$this->_UnlockTables();
		return true;
	}
	/**
	 * Deletes a node and increases the Level of all children by one
	 * @param integer $id id of the node to delete
	 * @return boolean true
	 */
	public function deleteSingleNode($id){
		$this->_LockTables();
		$node=$this->getNode($id);
		if(!$node)
			return false;
		$sql = array(
			"DELETE FROM {$this->table} WHERE ShopId='{$this->shop->getId()}' AND lft={$node->lft}",
			"UPDATE {$this->table} SET lft=lft-1,rgt=rgt-1 WHERE ShopId='{$this->shop->getId()}' AND lft BETWEEN {$node->lft} AND {$node->rgt}",
			"UPDATE {$this->table} SET lft=lft-2 WHERE ShopId='{$this->shop->getId()}' AND lft>{$node->rgt}",
			"UPDATE {$this->table} SET rgt=rgt-2 WHERE ShopId='{$this->shop->getId()}' AND rgt>{$node->rgt}"
		);
		foreach($sql as $query)
			$this->db->query($query);
		$this->_UnlockTables();
		return true;
	}
	/**
	 * Gets a multidimensional array containing the path to defined node
	 * @param integer $id id of the node to which the path should point
	 * @return array multidimensional array with the data of the nodes in the tree
	 */
	public function getPath($id){
		$sql="SELECT p.{$this->pk},p.{$this->name} FROM {$this->table} n,{$this->table} p WHERE n.ShopId='{$this->shop->getId()}' AND p.ShopId='{$this->shop->getId()}' AND n.lft BETWEEN p.lft AND p.rgt AND n.{$this->pk}='$id' ORDER BY p.lft";
		$path = array();
		foreach($this->db->iterate($sql) as $row)
		{
			$path[] = $row;
		}
		return $path;
	}
	/**
	 * Moves a node to child of target node
	 * @param integer $moveNode id of the node to move
	 * @param integer $siblingTarget id of the node to move the node after
	 * @return true
	 */
	public function moveNodeToChild($moveNode,$newParent){
		$this->_LockTables();
		$sourceNode = $this->getNode($moveNode);
		$targetNode = $this->getNode($newParent);
		$iSize = $sourceNode->rgt - $sourceNode->lft + 1;
		$sql = array(
			// step 1: temporary "remove" moving node
			"UPDATE {$this->table}
			SET lft = 0-(lft),rgt = 0-(rgt)
			WHERE lft >='{$sourceNode->lft}' AND rgt <= '{$sourceNode->rgt}' AND ShopId='{$this->shop->getId()}'",

			// step 2: decrease left and/or right position values of currently 'lower' items (and parents)
			"UPDATE {$this->table} SET lft = lft - '$iSize' WHERE lft > '{$sourceNode->rgt}' AND ShopId='{$this->shop->getId()}'",
			"UPDATE {$this->table} SET rgt = rgt - '$iSize' WHERE rgt > '{$sourceNode->rgt}' AND ShopId='{$this->shop->getId()}'",

			// step 3: increase left and/or right position values of future 'lower' items (and parents)
			"UPDATE {$this->table} SET lft = lft+'$iSize' WHERE lft >= '".($targetNode->rgt > $sourceNode->rgt ? $targetNode->rgt - $iSize : $targetNode->rgt)."' AND ShopId='{$this->shop->getId()}'",
			"UPDATE {$this->table} SET rgt = rgt + '$iSize' WHERE rgt >= '".($targetNode->rgt > $sourceNode->rgt ? $targetNode->rgt - $iSize : $targetNode->rgt)."' AND ShopId='{$this->shop->getId()}'",

			// step 4: move node (and it's subnodes)
			"UPDATE {$this->table}
			SET
				lft = 0-(lft)+".($targetNode->rgt > $sourceNode->rgt ? $targetNode->rgt - $sourceNode->rgt - 1 : $targetNode->rgt - $sourceNode->rgt - 1 + $iSize).",
				rgt = 0-(rgt)+".($targetNode->rgt > $sourceNode->rgt ? $targetNode->rgt - $sourceNode->rgt - 1 : $targetNode->rgt - $sourceNode->rgt - 1 + $iSize)."
			WHERE lft <= '".(0-$sourceNode->lft)."' AND rgt >= '".(0-$sourceNode->rgt)."' AND ShopId='{$this->shop->getId()}'"
		);

		foreach($sql as $query)
			$this->db->query($query);
		$this->_UnlockTables();
		return true;
	}
	/**
	 * Gets the id of a node depending on it's rgt value
	 * @param integer $rgt rgt value of the node
	 * @return integer id of the node
	 */
	protected function getIdRgt($rgt){
		$sql="SELECT {$this->pk} FROM {$this->table} WHERE ShopId='{$this->shop->getId()}' AND rgt='$rgt'";
		$pk=$this->db->fetchOne($sql);
		if(!$pk){
			return false;
		}
		return $pk;
	}
	/**
	 * Moves a node one position to the left staying in the same Level
	 * @param $nodeId id of the node to move
	 * @return boolean true
	 */
	public function moveLft($nodeId){
		$node=$this->getNode($nodeId);
		$brotherId=$this->getIdRgt($node->lft-1);
		if ($brotherId == false){
			return false;
		}
		$this->_LockTables();
		$brother=$this->getNode($brotherId);
	
		$nodeSize=$node->rgt-$node->lft+1;
		$brotherSize=$brother->rgt-$brother->lft+1;
	
		$sql="SELECT {$this->pk} FROM {$this->table} WHERE ShopId='{$this->shop->getId()}' AND lft BETWEEN {$node->lft} AND {$node->rgt}";
		$idsNotToMove=array();
		foreach($this->db->iterate($sql) as $row)
		{
			$idsNotToMove[]=$row->{$this->pk};
		}

		$sql="UPDATE {$this->table} SET lft=lft-$brotherSize,rgt=rgt-$brotherSize WHERE ShopId='{$this->shop->getId()}' AND lft BETWEEN {$node->lft} AND {$node->rgt}";
		$this->db->query($sql);
			
		$sql="UPDATE {$this->table} SET lft=lft+$nodeSize,rgt=rgt+$nodeSize WHERE ShopId='{$this->shop->getId()}' AND lft BETWEEN {$brother->lft} AND {$brother->rgt}";
		for ($i=0; $i<count($idsNotToMove); $i++){
			$sql .= " AND {$this->pk} != ".$idsNotToMove[$i];
		}	
		$this->db->query($sql);
		$this->_UnlockTables();
		return true;
	}
	/**
	 * Gets the id of a node depending on it's lft value
	 * @param integer $lft lft value of the node
	 * @return integer id of the node
	 */
	protected function getIdLft($lft){
		$sql="SELECT {$this->pk} FROM {$this->table} WHERE ShopId='{$this->shop->getId()}' AND lft='$lft'";
		$pk=$this->db->fetchOne($sql);
		if (!$pk){
			return false;
		}
		return $pk;
	}
	/**
	 * Moves a node one position to the right staying in the same Level
	 * @param $nodeId id of the node to move
	 * @return boolean true
	 */
	public function moveRgt($nodeId){
		$node = $this->getNode($nodeId);
		$brotherId=$this->getIdLft($node->rgt+1);
		if ($brotherId == false){
			return false;
		}
		$this->_LockTables();
		$brother = $this->getNode($brotherId);
	
		$nodeSize=$node->rgt-$node->lft+1;
		$brotherSize=$brother->rgt-$brother->lft+1;
	
		$sql="SELECT {$this->pk} FROM {$this->table} WHERE ShopId='{$this->shop->getId()}' AND lft BETWEEN {$node->lft} AND {$node->rgt}";
		foreach($this->db->iterate($sql) as $row)
		{
			$idsNotToMove[]=$row->{$this->pk};
		}
	
		$sql="UPDATE {$this->table} SET lft=lft+$brotherSize,rgt=rgt+$brotherSize WHERE ShopId='{$this->shop->getId()}' AND lft BETWEEN {$node->lft} AND {$node->rgt}";
		$this->db->query($sql);
		
		$sql="UPDATE {$this->table} SET lft=lft-$nodeSize,rgt=rgt-$nodeSize WHERE ShopId='{$this->shop->getId()}' AND lft BETWEEN {$brother->lft} AND {$brother->rgt}";
		for ($i=0; $i<count($idsNotToMove); $i++){
			$sql .= " AND {$this->pk} != ".$idsNotToMove[$i];
		}
		$this->db->query($sql);
		$this->_UnlockTables();
		return true;
	}
}
?>