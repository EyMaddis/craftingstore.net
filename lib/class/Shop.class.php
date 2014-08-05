<?php
/************************
**
** 	File:	 	shop.class.php
**	Author: 	Mathis Neumann
**	Date:		08/01/2012
**	Desc:		class to fetch information about the shop
**
*************************/

defined('_MCSHOP') or die("Security block!");

class Shop	// includes the whole data about a shop with a given ID
{
	private $shopID;

	private $shopInfo = null;
	public function getShopInfo($column = null){ // Gets all information from row with shopID - returns an array with all field
		if($column == null){
			if($this->shopInfo == null) // check if updateShopInfo() was used before
			{
				$this->updateShopInfo();
			}
			return $this->shopInfo;
		}
		try{
			return $_SESSION['Index']->db->fetchOne("SELECT ".mysql_real_escape_string($column)." FROM mc_shops WHERE Id='{$this->shopID}'");
		}catch(Exception $e){
			return null;
		}
	}

	public function __construct($shopId){
		$this->shopID = $shopId;
	}

	public function getId(){ // return the Shop ID
		if(!isset($this->shopID))
		{
			setError("No shopID found!", __FILE__, __LINE__);
			return false;
		}
		else
		{
			return $this->shopID;
		}
	}

	public function updateShopInfo(){ // The shop information are saved in the session to reduce traffic
		// just needed if getShopInfo was not parsed before.
		if(!isset($this->shopID))
		{
			setError("No shop selected!", __FILE__, __LINE__);
		}
		else
		{
			$this->shopInfo = $_SESSION['Index']->db->fetchOneRow("SELECT * FROM mc_shops WHERE Id='{$this->shopID}'");
		}
	}

	public function transfer($userId, $transferId){
		$server = new JSONquery($this->shopID);
		$result = $server->transferProduct($userId, $transferId);

		if($result == "CONNECTION_ERROR" || $result == "JSON_ERROR"){
			$this->transferError();
		}
		elseif($result == 'TRANSFERED'){
			$this->resetTransferError();
		}
		return $result;
	}

	public function setServerStatus($isOnline){
		if($this->getShopInfo()->ServerOnline != $isOnline)
			$_SESSION['Index']->db->update("UPDATE mc_shops SET ServerOnline='".($isOnline ? SERVER_MAX_TRANSFER_FAILURES : '0')."' WHERE Id='{$this->shopID}'");
	}
	public function transferError(){
		$_SESSION['Index']->db->update("UPDATE mc_shops SET ServerOnline=ServerOnline-1 WHERE Id='{$this->shopID}' AND ServerOnline>0");
	}
	public function resetTransferError(){
		if(isNumber(SERVER_MAX_TRANSFER_FAILURES))
			$_SESSION['Index']->db->update("UPDATE mc_shops SET ServerOnline=".SERVER_MAX_TRANSFER_FAILURES." WHERE Id='{$this->shopID}'");
	}
	public function getServerStatus(){
		return $this->getShopInfo()->ServerOnline;
	}
	public static function validShopId($ShopId){
		return (isNumber($ShopId) && ($ShopId == $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_shops WHERE Id='$ShopId'")));
	}
	public static function ShopInfo($ShopId){
		if(isNumber($ShopId))
			return $_SESSION['Index']->db->fetchOneRow("SELECT Id,Label,Subdomain,Domain FROM mc_shops WHERE Id='$ShopId'");
	}
	public static function getSubdomain($ShopId, $completeSubdomains = false, &$isDomain = null){
		if(!isNumber($ShopId))
			return null;
		$row = $_SESSION['Index']->db->fetchOneRow("SELECT Subdomain,Domain FROM mc_shops WHERE Id='$ShopId'");
		if($row->Domain){
			$isDomain = true;
			return $row->Domain;
		}
		$isDomain = false;
		if($completeSubdomains)
			return $row->Subdomain.'.'.BASE_DOMAIN;
		return $row->Subdomain;
	}

	public function getMinecraftnameOfPlayerId($UserId){
		if(isNumber($UserId)){
			return $_SESSION['Index']->db->fetchOne("SELECT MinecraftName FROM mc_gamer WHERE Id='$UserId'"); //%%PLAYER%% wird durch $PlayerName ersetzt
		}
		return false;
	}
}

?>