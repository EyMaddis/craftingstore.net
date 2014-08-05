<?php
defined('_MCSHOP') or die("Security block!");

class User{
	#region Properties
	private $Id = -1;
	private $name = null;
	private $Validated = null;

	public static $verificationTime = 86400; //24 Stunden
	#end

	#region public function __construct($userId = -1)
	public function __construct($userId = -1)
	{
		if(isNumber($userId))
		{
			$userInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Id, Nickname, Validated FROM mc_gamer WHERE Id='$userId' LIMIT 1");
			$this->Id = $userInfo->Id;
			$this->name = $userInfo->Nickname;
			$this->Validated = $userInfo->Validated;
		}
		else
		{
			$this->Id = -1;
		}
	}
	#end

	#region public function getLoginId()
	public function getLoginId()
	{
		return $this->Id;
	}
	#end

	#region public function getName()
	public function getName()
	{
		return $this->name;
	}
	#end

	#region public function getUserDataById($Id)
	// returns the full userdata or false as an "not found"
	public function getUserDataById($Id)
	{
		if(isNumber($Id))
		{
			$userdata = $_SESSION['Index']->db->fetchOneRow("SELECT * FROM mc_gamer WHERE Id = '$Id'");
			return $userdata;
		}
		return false;
	}
	#end

	#region public function tryLogin($name, $pw, $shopId, &$WaitTime)
	# Versucht einen Login mit den übergebenen Daten durchzuführen.
	# Als $name kann sowohl eine E-Mail-Adresse als auch der Nickname eines Benutzers übergeben werden
	//Gibt die Wartezeit für den Loginversuch an (in Sekunden)
	public function tryLogin($name, $pw, $shopId, &$WaitTime)
	{
		$WaitTime = LoginWaitTimes::getRemainingWaitTime();
		if($WaitTime > 0)
			return -3;

		#region Wenn sich die IP einloggen darf, werden die Anmeldedaten überprüft
		$row = $_SESSION['Index']->db->fetchOneRow("SELECT Password, Id, Email, Nickname, Validated FROM mc_gamer WHERE Nickname='".mysql_real_escape_string($name)."' OR Email='".mysql_real_escape_string($name)."' LIMIT 1");
		if(!$row)
			return -2;
		// Der User ist noch nicht freigeschaltet: zur Verifizierung weiterleiten
		if(!$row->Validated)
			return -4;
		//Prüfen ob das Passwort korrekt ist
		if(bcrypt_check($row->Email, $pw, $row->Password)){
			$_SESSION['Index']->db->query("DELETE FROM mc_loginerrors WHERE IP='{$_SERVER['REMOTE_ADDR']}'");
			$_SESSION['Index']->db->query("UPDATE mc_gamer SET IsLoggedIn='1', ResetPasswordToken=NULL, LastLang='".$_SESSION['Index']->lang->getLangId()."' WHERE Id='{$row->Id}'");
			$this->Id = $row->Id;
			$this->name = $row->Nickname;
			$this->Validated = $row->Validated;
			LoginWaitTimes::setLoginValid();

			// weiterleiten/alles ist gut (keine ShopId angegeben oder im Shop freigeschaltet
			if(!$shopId || $_SESSION['Index']->db->fetchOne("SELECT RegTime FROM mc_permittedshops WHERE GamerId='{$row->Id}' AND ShopId='$shopId' LIMIT 1")){
				return 0;
			}
			// nicht für den Shop freigeschaltet, fragen, ob freitgeschaltet werden soll
			else
				return -1;
		}
		// Fehlerhafter Login
		$WaitTime = LoginWaitTimes::getNextWaitTimeAfterLoginError();
		return -2;
		#end
	}
	#end

	#region public static function checkPassword($name, $pw)
	#Prüft, ob das Passwort zu einem User mit dem angegebenen Namen gehört
	public static function checkPassword($name, $pw)
	{
		return User::isPassword($_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_gamer WHERE Nickname='".mysql_real_escape_string($name)."' OR Email='".mysql_real_escape_string($name)."' LIMIT 1"), $pw);
	}
	#end

	#region public static function isPassword($id, $pw)
	#Prüft, ob das Passwort zu einem User mit der angegebenen Id gehört
	public static function isPassword($id, $pw)
	{
		if(isNumber($id)){
			$row = $_SESSION['Index']->db->fetchOneRow("SELECT Email, Password FROM mc_gamer WHERE Id='$id' LIMIT 1");
			return bcrypt_check($row->Email, $pw, $row->Password);
		}
		return false;
	}
	#end

	#region public static function changePassword($id, $oldPw, $newPw)
	#gibt 0 zurück, wenn der Passwort geändert wurde, ansonsten eine Zahl>0
	public static function changePassword($id, $oldPw, $newPw)
	{
		if(User::validPassword($newPw))
		{
			if(isNumber($id))
			{
				$row = $_SESSION['Index']->db->fetchOneRow("SELECT Email, Password FROM mc_gamer WHERE Id='$id' LIMIT 1");
				if($row->Email){
					if(bcrypt_check($row->Email, $oldPw, $row->Password))//steht überhaupt etwas in $row->Email? Wenn nicht, wurde keine Zeile mit der Id gefunden
					{
						$_SESSION['Index']->db->query("UPDATE mc_gamer SET Password='".mysql_real_escape_string(bcrypt_encode($row->Email, $newPw))."' WHERE Id='$id'");
						return 0;
					}
					return 1; #E-Mail und Passwort passen nicht zusammen
				}
				return 2; #Zu der Id wurde keine E-Mail (=kein Eintrag) gefunden
			}
			return 3; #Es wurde keine syntaktisch gültige Id übergeben
		}
		return 4; #Das neue Password entspricht nicht den Passwortrichtlinien
	}
	#end

	#region public function createToken()
	public function createToken()
	{
		$token = sha1(random_string_by_length(10).microtime(1));
		$_SESSION['Index']->db->query("UPDATE mc_gamer SET Token='$token' WHERE Id='{$this->Id}'");
		return $token;
	}
	#end

	#region public function Logout()
	public function Logout($id = null)
	{
		if(!isNumber($id))
			$id = $this->Id;

		$_SESSION['Index']->db->query("UPDATE mc_gamer SET IsLoggedIn='0' WHERE Id='$id'");
		$piwiktmp = $_SESSION['Piwik'];
		session_destroy();
		session_start();
		session_regenerate_id(true); //gegen Session-Hijacking
		$_SESSION['Piwik'] = $piwiktmp;
	}
	#end

	#region public function isLoggedIn()
	public function isLoggedIn(){
		return (($this->Id > 0) && $_SESSION['Index']->db->fetchOne("SELECT IsLoggedIn FROM mc_gamer WHERE Id='{$this->Id}'"));
	}
	#end
	#region public function isValidated()
	public function isValidated(){
		return $this->Validated;
	}
	#end

	#region public static function tryCreateUser($ShopId, $Nickname, $Email, $EmailAccept, $Password, $MinecraftName, &$Error)
	# Versucht einen neuen Benutzer zu erstellen
	# Bei einem Fehler wird false zurückgegeben und $Error enthält ein Array mit genauen Fehlerbeschreibungen
	# Bei Erfolg wird der Wert von $_SESSION['Index']->db->insert(); zurückgegeben
	public static function tryCreateUser($Nickname, &$Email, $EmailAccept, &$Password, $MinecraftName, &$Error){
		$Error = null;
		// $_SESSION['Index']->db->delete("DELETE FROM mc_gamer WHERE Validated='0' AND RegTime<'".(time() - 86400)."'");
		#region Sind alle Werte Ok?
		#region Nickname gültig
		if(!User::validNickname($Nickname))
			$Error['Nickname'] = 1;
		#end
		#region Nickname in Verwendung
		if(User::getIdByNickname($Nickname))
			$Error['Nickname'] = 2;
		#end
		#region E-Mail gültig
		if(!$Email || (($EmailAccept != $Email) && !is_email($Email)))
			$Error['Email'] = 1;
		#end
		#region E-Mail in Verwendung
		elseif(User::getIdByEmail($Email))
			$Error['Email'] = 2;
		#end
		#region MinecraftName gültig
		if(!User::validMinecraftname($MinecraftName))
			$Error['MinecraftName'] = 1;
		#end
		#region Passwort gültig
		if(!User::validPassword($Password))
			$Error['Password'] = 1;
		#end
		#end
		#region Alles OK, es darf versucht werden, den Benutzer anzulegen
		if($Error == null){
			//hash-Wert erzeugen
			$Password = bcrypt_encode($Email, $Password);
			//Werte in die Datenbank eintragen
			//Den User erstellen
			$insertId = $_SESSION['Index']->db->insert("INSERT INTO mc_gamer (Nickname, Email, Password, MinecraftName, RegTime) VALUES ('".mysql_real_escape_string($Nickname)."','".mysql_real_escape_string($Email)."','".mysql_real_escape_string($Password)."','".mysql_real_escape_string($MinecraftName)."','".time()."')");
			if(!$insertId){
				$Error['db'] = 1;
				return false;
			}
			return $insertId;
		}
		#end
		$Password = null;
		return false;
	}

	#region public static function validPassword($pw)
	public static function validPassword($pw)
	{
		// mindestens 8 Zeichen lang
		if(strlen($pw) < 8) return false;
		//mindestens ein Buchstabe und eine Zahl
		if(!preg_match('/[a-zA-Z]/', $pw) || !preg_match('/\d/', $pw)) return false;
		return true;
	}
	#end
	#region private static function validMinecraftname($name)
	private static function validMinecraftname($name)
	{
		if(strlen($name) == 0) return false;
		return true;
	}
	#end
	#region public static function validNickname($name)
	public static function validNickname($name)
	{
		return preg_match('/^[a-zA-Z0-9_\-]+$/i', $name);
	}
	#end
	#end

	#region public static function addPermittedShop($ShopId, $UserId)
	public static function addPermittedShop($ShopId, $UserId)
	{
		if(!isNumber($ShopId) || !isNumber($UserId))
			return false;
		try{
			$_SESSION['Index']->db->insert("INSERT INTO mc_permittedshops (GamerId, ShopId, BonusPoints) SELECT '$UserId','$ShopId',StartingCredit FROM mc_shops WHERE Id='$ShopId'");
			return true;
		}
		catch(Exception $e){
		echo $e;
			return false;
		}
	}
	#end
	#region public static function sendValidationMail($UserId, $ShopId, $validationType = null)
	#$validationType:
	# 1: Mail-Adresse soll geändert werden
	# 2: Passwort wurde vergessen und ein Rücksetzkey soll gesendet werden
	# default: Registrierung abschließen
	public static function sendValidationMail($ShopId, $UserId, $validationType = null)
	{
		if(!isNumber($UserId)) return false;

		if($validationType == 2){#Passwort vergessen
			$userInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Nickname, Email, Password
FROM mc_gamer WHERE Id='$UserId'");
			$validationKey = sha1($userInfo->Email.$userInfo->Password);
			return mail(
			//To (ist weiter unten im Header bereits angegeben)
				null,
			//Subject
				$_SESSION['Index']->say('RESET_PASSWORD_SUBJECT', array(BASE_DOMAIN), false),
			//Message
				$_SESSION['Index']->say('RESET_PASSWORD_MESSAGE',  array(
					$userInfo->Nickname,
					SECURE_URL.'/?show=ForgotPassword&amp;shop='.$ShopId.'&amp;k='.$validationKey), false),
			//Header
				"MIME-Version: 1.0\n"
				."To: {$userInfo->Nickname} <{$userInfo->Email}>\n"
				."From: \"".BASE_DOMAIN."\" <noreply@".BASE_DOMAIN.">\n"
				."Return-Path: noreply@".BASE_DOMAIN."\n"
				."Reply-To: noreply@".BASE_DOMAIN."\n"
				."Bcc: support@craftingstore.net\n"
				."Content-type: text/html; charset=utf-8\n"
				);
		}
	}
	#end
	#region E-Mail ändern
	public function sendChangeMailValidation($NewEmail, $ShopId){
		if(!$NewEmail) return false;
		$userInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Nickname, NewEmail, Password
FROM mc_gamer
WHERE Id='{$this->Id}'");

		$validationKey = User::getChangeMailValidationKey($userInfo->NewEmail,$userInfo->Password);

		#region return mail(...)
		return mail(
		//To (ist weiter unten im Header bereits angegeben)
			null,
		//Subject
			$_SESSION['Index']->say('CHANGE_MAIL_SUBJECT', array($domain), false),
		//Message
			$_SESSION['Index']->say('CHANGE_MAIL_MESSAGE',  array(
				$userInfo->Nickname,
				SECURE_URL.'?show=Profile'.($ShopId ? '&amp;shop='.$ShopId.'' : '').'&amp;editmail&amp;k='.$validationKey), false),
		//Header
				"MIME-Version: 1.0\n"
				."To: {$userInfo->Nickname} <$NewEmail>\n"
				."From: \"".BASE_DOMAIN."\" <noreply@".BASE_DOMAIN.">\n"
				."Return-Path: noreply@".BASE_DOMAIN."\n"
				."Reply-To: noreply@".BASE_DOMAIN."\n"
				."Bcc: support@craftingstore.net\n"
				."Content-type: text/html; charset=utf-8\n"
			);
		#end
	}
	public function checkChangeMailValidation($pw,$key){
		$userInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Email, Password, NewEmail FROM mc_gamer WHERE Id='{$this->Id}'");
		//Mail sollte gar nicht geändert werden
		if($userInfo->NewEmail === null)
			return -1;
		//Falsches Passwort
		if(!bcrypt_check($userInfo->Email, $pw, $userInfo->Password))
			return -2;
		//Falscher Key
		if($key != User::getChangeMailValidationKey($userInfo->NewEmail,$userInfo->Password))
			return -3;
		//Alles gut
		if($_SESSION['Index']->db->update("UPDATE mc_gamer SET Email=NewEmail, NewEmail=null, Password='".mysql_real_escape_string(bcrypt_encode($userInfo->NewEmail, $pw))."' WHERE Id='{$this->Id}'"))
			return true;
	}
	private static function getChangeMailValidationKey($NewEmail, $password){
		return sha1($NewEmail.$password);
	}
	#end

	#region public static function validateUser($ShopId, $UserId, $Code)
	public static function validateUser($ShopId, $UserId, $Code)
	{
		//UserId hat ungültiges Format
		if(!isNumber($UserId) || !isNumber($ShopId)) return 1;
		//Code hat die falsche Länge
		if(strlen($Code) != 40) return 2;

		$userInfo = $_SESSION['Index']->db->fetchOneRow("SELECT g.Nickname, g.Email, g.Password, g.Validated, p.RegTime FROM mc_permittedshops AS p LEFT JOIN mc_gamer AS g ON g.Id=p.GamerId WHERE p.GamerId='$UserId' AND p.ShopId='$ShopId'");

		//User hat sich noch nicht für den Shop registriert
		if(!$userInfo) return 3;

		//User ist bereits freigeschaltet
		if($userInfo->Validated) return 4;

		//24 Stunden sind abgelaufen
		if((strtotime($userInfo->RegTime) + User::$verificationTime) < time())
		{
			$_SESSION['Index']->db->query("DELETE FROM mc_gamer WHERE Id='$UserId' LIMIT 1");
			$_SESSION['Index']->db->query("DELETE FROM mc_gameraccounts WHERE GamerId='$UserId' LIMIT 1");
			$_SESSION['Index']->db->query("DELETE FROM mc_permittedshops WHERE GamerId='$UserId' LIMIT 1");
			return 5;
		}

		//Korrekten Code berechnen
		$validationKey = sha1($userInfo->Email.$userInfo->Password.$userInfo->RegTime);

		//Code ist ungültig
		if($validationKey != $Code) return 6;

		//Code ist gültig
		$_SESSION['Index']->db->query("UPDATE mc_gamer SET Validated='1' WHERE GamerId='$UserId' LIMIT 1");
		return 0;
	}
	#end

	#region public static function getIdByNickname($Nickname)
	public static function getIdByNickname($Nickname)
	{
		$row = $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_gamer WHERE Nickname='".mysql_real_escape_string($Nickname)."' LIMIT 1");
		if($row)
			return $row;
		return 0;
	}
	#end
	#region public static function getIdByEmail($Email)
	public static function getIdByEmail($Email)
	{
		$row = $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_gamer WHERE Email='".mysql_real_escape_string($Email)."' LIMIT 1");
		if($row)
			return $row;
		return 0;
	}
	#end
	#region public static function getIdByMinecraftname($Minecraftname)
	public static function getIdByMinecraftname($Minecraftname){
		$row = $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_gamer WHERE Minecraftname='".mysql_real_escape_string($Minecraftname)."' LIMIT 1");
		if($row)
			return $row;
		return 0;
	}
	#end

	public function getPlayerOnlineStatus(){
		return $_SESSION['Index']->db->fetchOne("SELECT PlayerOnline FROM mc_permittedshops WHERE GamerId='{$this->Id}' AND ShopId='{$_SESSION['Index']->shop->getId()}' LIMIT 1");
	}
	public function setPlayerOnlineStatus($isOnline){
		$_SESSION['Index']->db->update("UPDATE mc_permittedshops SET PlayerOnline='".($isOnline ? '1' : '0')."' WHERE GamerId='{$this->Id}' AND ShopId='{$_SESSION['Index']->shop->getId()}' LIMIT 1");
	}


	#region BuyProduct()
	public function BuyProduct($ProductId, $Amount, $productCosts = array()){
		if(is_array($ProductId)){
			startTransaction("
				mc_gameraccounts WRITE,
				mc_customeraccounts WRITE,
				mc_customeraccounts AS c READ,
				mc_permittedshops WRITE,
				mc_ouraccount WRITE,
				mc_ouraccount AS o READ,
				mc_customers READ,
				mc_inventory WRITE,
				mc_products WRITE,
				mc_ProductsInProduct READ,
				mc_items READ,
				mc_ItemsInProduct READ");

			$sum = 0;
			foreach($productCosts as $costs){
				$sum += $costs;
			}
			if($this->currentBalance() < $sum){
				rollback();
				return "NOT_ENOUGH_MONEY";
			}

			$transferIds = array();
			for($i=0; $i<count($ProductId); $i++){
				if($TransferId = $this->addProductToInventory($ProductId[$i], $Amount[$i], $error, false)){
					$revenue = User::debitPlayer($this->Id, $_SESSION['Index']->shop->getId(), $productCosts[$i], $TransferId, false);
					$_SESSION['Index']->db->query("UPDATE mc_products SET BuyCounter=BuyCounter+{$Amount[$i]}, Revenue=Revenue+$revenue WHERE Id='{$ProductId[$i]}' AND ShopId='{$_SESSION['Index']->shop->getId()}' LIMIT 1");
					$transferIds[] = $TransferId;
				}
				else{
					rollback();
					return $error;
				}
			}
			//Warenkorb löschen
			$_SESSION['UserCart'] = array();
			commit();
			return $transferIds;
		}
		else{
			if(!isNumber($ProductId)){
				return "INVALID_PRODUCT";
			}
			if(!isNumber($Amount)){
				return "INVALID_AMOUNT";
			}
			startTransaction("
				mc_gameraccounts WRITE,
				mc_customeraccounts WRITE,
				mc_customeraccounts AS c READ,
				mc_permittedshops WRITE,
				mc_ouraccount WRITE,
				mc_ouraccount AS o READ,
				mc_customers READ,
				mc_inventory WRITE,
				mc_products WRITE,
				mc_ProductsInProduct READ,
				mc_items READ,
				mc_ItemsInProduct READ");

			$pointsPerItem = $_SESSION['Index']->db->fetchOne("SELECT Points FROM mc_products WHERE Id='$ProductId' AND ShopId='{$_SESSION['Index']->shop->getId()}' LIMIT 1");
			if($pointsPerItem === null){
				rollback();
				return "INVALID_PRODUCT";
			}
			$costs = $pointsPerItem * $Amount;
			if($this->currentBalance() < $costs){
				rollback();
				return "NOT_ENOUGH_MONEY";
			}

			if($TransferId = $this->addProductToInventory($ProductId, $Amount, $error, false)){
				$revenue = User::debitPlayer($this->Id, $_SESSION['Index']->shop->getId(), $costs, $TransferId, false);
				$_SESSION['Index']->db->query("UPDATE mc_products SET BuyCounter=BuyCounter+$Amount, Revenue=Revenue+$revenue WHERE Id='$ProductId' AND ShopId='{$_SESSION['Index']->shop->getId()}' LIMIT 1");
				commit();
				return $TransferId;
			}
			else{
				rollback();
				return $error;
			}
		}
		/*if($TransferId = $this->addProductToScheduler($ProductId, $Amount, $error, false)){
			$revenue = User::debitPlayer($this->Id, $_SESSION['Index']->shop->getId(), $costs, $TransferId, false);
			$_SESSION['Index']->db->query("UPDATE mc_products SET BuyCounter=BuyCounter+$Amount, Revenue=Revenue+$revenue WHERE Id='$ProductId' AND ShopId='{$_SESSION['Index']->shop->getId()}' LIMIT 1");
			commit();
				return true;
		}
		else{
			rollback();
			return $error;
		}
		*/
	}
	#end
	#region addProductToInventory
	private function addProductToInventory($ProductId, $Amount, &$error = null, $lock = true){
		if(!$ProductInfo = $_SESSION['Index']->db->fetchOneRow("SELECT Cooldown, CooldownInterval, DisableDuringCooldown FROM mc_products WHERE Id='$ProductId' AND ShopId='{$_SESSION['Index']->shop->getId()}' LIMIT 1")){
			if($lock) rollback();
			$error = "INVALID_PRODUCT_ID";
			return false;
		}

		#region Wenn der Cooldown des Produkts noch läuft, darf es nicht übertragen werden
		if($ProductInfo->DisableDuringCooldown){
			if(Item::CheckProductDisabled($ProductId, $this->Id, $_SESSION['Index']->shop->getId())){
				if($lock) rollback();
				$error = "PRODUCT_DISABLED";
				return false;
			}
		}
		#end

		#region Alles in Ordnung, das Item kann in die Inventar-Tabelle eingetragen werden
		return $_SESSION['Index']->db->insert("INSERT INTO mc_inventory (ShopId,GamerId,ProductId,Amount,Cooldown,CooldownInterval) VALUES ('{$_SESSION['Index']->shop->getId()}','{$this->Id}','$ProductId','$Amount',".($ProductInfo->DisableDuringCooldown ? "'{$ProductInfo->Cooldown}','{$ProductInfo->CooldownInterval}')" : "null,null)"));
		#end
	}
	#end

#region Inpayment($GamerId, $addEuro)
	//Führt eine Einzahlung auf das normale Konto eines Users durch
	public static function Inpayment($GamerId, $addEuro){
		if(!isNumber($addEuro))
			return false;
		$addPoints = $addEuro*POINTS_PER_EURO;
		$addCents = $addEuro*100;
		$_SESSION['Index']->db->query("INSERT INTO mc_gameraccounts (Time,GamerId,Current,Difference,Revenue,Action) SELECT '".time()."','$GamerId', Current+$addPoints, $addPoints, $addCents, 'INPAYMENT' FROM mc_gameraccounts WHERE GamerId='$GamerId' ORDER BY Time DESC LIMIT 1");
	}
	#end
	#region givePlayerBonusPoints($UserId, $ShopId, $addPoints)
	//Gibt einem bestimmten Spieler innerhalb eines Shops die Bonuspunkte
	public static function givePlayerBonusPoints($UserId, $ShopId, $addPoints){
		if(isNumber($addPoints)){
			startTransaction("mc_permittedshops WRITE, mc_gameraccounts WRITE, mc_gameraccounts AS ga WRITE");
			$_SESSION['Index']->db->query("UPDATE mc_permittedshops SET BonusPoints=BonusPoints+$addPoints WHERE GamerId='$UserId' AND ShopId='$ShopId'");
			$_SESSION['Index']->db->insert("INSERT INTO mc_gameraccounts (Time,GamerId,Current,Difference,BonusDifference,Action,ShopId)
				SELECT '".time()."','$UserId',ga.Current,'0','$addPoints','RECEIVED_BONUS','$ShopId' FROM mc_gameraccounts AS ga WHERE ga.GamerId='$UserId' ORDER BY ga.time DESC LIMIT 1");
			commit();
		}
		elseif(isNumber($addPoints,false,true)){
			startTransaction("mc_permittedshops WRITE, mc_gameraccounts WRITE, mc_gameraccounts AS ga WRITE");
			$bonusPoints = User::getBonusPoints($UserId, $ShopId);
			if($bonusPoints < -$addPoints){
				$addPoints = -$bonusPoints;
			}
			$_SESSION['Index']->db->query("UPDATE mc_permittedshops SET BonusPoints=BonusPoints$addPoints WHERE GamerId='$UserId' AND ShopId='$ShopId'");
			$_SESSION['Index']->db->insert("INSERT INTO mc_gameraccounts (Time,GamerId,Current,Difference,BonusDifference,Action,ShopId)
				SELECT '".time()."','$UserId',ga.Current,'0','$addPoints','REMOVE_BONUS','$ShopId' FROM mc_gameraccounts AS ga WHERE ga.GamerId='$UserId' ORDER BY ga.time DESC LIMIT 1");
			commit();
		}
	}
	#end
	#region debitPlayer($UserId, $ShopId, $debitPoints, $InventoryId = 0)
	// Bucht einen bestimmten Betrag vom Konto eines Spielers ab.
	//	Außerdem wird dem Shop das Geld gutgeschrieben und uns auch.
	// Gibt den Betrag in Cent zurück, der dem Shopbetreiber gutgeschrieben wurde
	public static function debitPlayer($UserId, $ShopId, $debitPoints, $InventoryId = 0, $lock = true){
		if(!isNumber($debitPoints,1))
			return false;

		if($lock) startTransaction("
			mc_gameraccounts WRITE,
			mc_customeraccounts WRITE,
			mc_customeraccounts AS c READ,
			mc_permittedshops WRITE,
			mc_ouraccount WRITE,
			mc_ouraccount AS o READ,
			mc_customers READ");
		$currentBalance = User::getCurrentBalance($UserId, $ShopId, $normalPoints, $bonusPoints);
		if($currentBalance < $debitPoints){
			if($lock) rollback();
			return false;
		}

		$newBonusPoints = $bonusPoints - $debitPoints;
		if($newBonusPoints < 0){
			$subtractNormalPoints = $debitPoints-$bonusPoints;
			$newBonusPoints = 0;
			$subtractBonusPoints = $bonusPoints;
		}
		else{
			$subtractBonusPoints = $debitPoints;
		}

		if($bonusPoints > 0){
			$_SESSION['Index']->db->query("UPDATE mc_permittedshops SET BonusPoints=$newBonusPoints WHERE GamerId='$UserId' AND ShopId='$ShopId' LIMIT 1");
		}
		#region Informationen zum Kaufvorgang in der Tabelle mc_gameraccounts hinterlegen
		$newPoints = $normalPoints - $subtractNormalPoints;
		$normalPointsAsCents = $subtractNormalPoints*100/POINTS_PER_EURO;
		$insertId = $_SESSION['Index']->db->insert("INSERT INTO mc_gameraccounts (GamerId,Current,Difference,Revenue,BonusDifference,Action,ShopId,InventoryId,Time) VALUES ('$UserId','$newPoints','$subtractNormalPoints','$normalPointsAsCents','$subtractBonusPoints','BOUGHT_ITEM','$ShopId','$InventoryId','".time()."')");
		#end
		#region Eintragen erfolgreich => Verdienst buchen
		if($insertId > 0){
			$CustomersId = $_SESSION['Index']->shop->getShopInfo()->CustomersId;
			$EarningRate = $_SESSION['Index']->db->fetchOne("SELECT EarningRate FROM mc_customers WHERE Id='$CustomersId' LIMIT 1");

			$gesUmsatz = $subtractNormalPoints*100/POINTS_PER_EURO;
			$customersGewinn = $gesUmsatz * $EarningRate/100;
			$unserGewinn = $gesUmsatz - $customersGewinn;

			$_SESSION['Index']->db->insert("INSERT INTO mc_customeraccounts (CustomersId,Current,Difference,ShopId,Time) SELECT '$CustomersId',c.Current+$customersGewinn,'$customersGewinn','{$_SESSION['Index']->shop->getId()}','".time()."' FROM mc_customeraccounts AS c WHERE c.CustomersId='$CustomersId' ORDER BY c.Time DESC LIMIT 1");
			$_SESSION['Index']->db->insert("INSERT INTO mc_ouraccount (Current,Difference,Time) SELECT o.Current+$unserGewinn,'$unserGewinn','".time()."' FROM mc_ouraccount AS o ORDER BY o.Time DESC LIMIT 1");

			if($lock) commit();
			return $customersGewinn;
		}
		#end
		if($lock) rollback();
		return false;
	}
	#end

	#region getCurrentBalance($GamerId, $ShopId, &$normalPoints, &$bonusPoints)
	// Ermittelt den aktuellen Kontostand des Spielers ohne Bonuspunkte
	public static function getCurrentBalance($GamerId, $ShopId, &$normalPoints = null, &$bonusPoints = null)
	{
		$normalPoints = User::getNormalPoints($GamerId);
		$bonusPoints = User::getBonusPoints($GamerId, $ShopId);
		return $normalPoints + $bonusPoints;
	}
	public function currentBalance()
	{
		return User::getCurrentBalance($this->Id, $_SESSION['Index']->shop->getId());
	}
	#end
	#region getBonusPoints($GamerId, $ShopId)
	// Ermittelt die Bonuspunkte des aktuellen Spielers
	public static function getBonusPoints($GamerId, $ShopId)
	{
		return $_SESSION['Index']->db->fetchOne("SELECT BonusPoints FROM mc_permittedshops WHERE GamerId='$GamerId' AND ShopId='$ShopId' LIMIT 1");
	}
	#end
	#region bonusPoints()
	// Ermittelt die Bonuspunkte des aktuellen Spielers
	public function bonusPoints()
	{
		return User::getBonusPoints($this->Id, $_SESSION['Index']->shop->getId());
	}
	#end
	#region getNormalPoints($GamerId)
	// Ermittelt den aktuellen Kontostand des Spielers inkl. Bonus pro Shop!
	public static function getNormalPoints($GamerId)
	{
		return $_SESSION['Index']->db->fetchOne("SELECT Current FROM mc_gameraccounts WHERE GamerId='$GamerId' ORDER BY Time DESC LIMIT 1");
	}
	#end
	#region normalPoints()
	// Ermittelt die Bonuspunkte des aktuellen Spielers
	public function normalPoints()
	{
		return User::getNormalPoints($this->Id, $_SESSION['Index']->shop->getId());
	}
	#end

}

?>