<?php

class Index
{
	private $Version;
	private $template;

#region Properties
	#region some general properties
	private $initDone = false;
	public $default_img = "default.png";
	public $default_set_img = "defaultset.png";
	public $relImgPath = './images/items/';
	#end

	public $db;
	public $shop;
	public $adminShop;
	public $user;
	public $smarty;
	public $lang;
	public $displayControl;
	#region NestedSet, zur Menü-Verwaltung
	public $nstree;
	#end
/*
	#region ContentBoxes
	public $contentBoxes;
	#end
*/

#end

	#region __construct()
	public function __construct()
	{
		$this->Version = VERSION;
		$this->initDatabase();
		$this->initShop($_SERVER['HTTP_HOST']);
		$this->initLanguage();
		$this->initSmarty();
		$this->initDisplayControl();
		$this->initUser();
		$this->nstree = $this->createNestedSet($this->shop);
	}
	#end

	#region Version überprüfen
	#region public function CheckVersion()
	public function CheckVersion()
	{
		if($this->Version != VERSION)
		{
			$_SESSION['Index']->user->Logout();
			session_destroy();
			setLocation($_SERVER['REQUEST_URI']);
		}
	}
	#end
	#region public function getVersion()
	public function getVersion()
	{
		return $this->Version;
	}
	#end
	#end

	#region Initialisierung beim Start
	#region private function initDisplayControl()
	private function initDisplayControl()
	{
		$this->displayControl = new DisplayControl();
	}
	#end
	#region private function initDatabase()
	private function initDatabase()
	{
		$this->db = MySqlDatabase::getInstance();
		$this->connectDatabase();
	}
	public function connectDatabase()
	{
		if(!$this->db->isConnected())
		{
			try
			{
				$this->db->connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
				$this->db->query("SET NAMES 'utf8'");
			}
			catch (Exception $e)
			{
				die($e->getMessage());
			}
		}
	}
	#end
	#region private function initSmarty()
	private function initSmarty()
	{
		$this->smarty = new Smarty();

		$this->smarty->security = true;

		$this->smarty->compile_dir = DOC_ROOT.'template_c/';
		$this->smarty->config_dir = DOC_ROOT.'config/';
		$this->smarty->cache_dir = DOC_ROOT.'cache/';
	}
	#end
	#region private function initLanguage()
	private function initLanguage()
	{
		$this->lang = new Lang($this->db);
	}
	#end

	#region private function initUser()
	private function initUser()
	{
		$this->user = new User();
	}
	#end

	#region private function initShop()
	private function initShop($fqdn)
	{
		$ShopId = -1;
		$sub = getCurrentSubdomain($fqdn);
		if(!in_array($sub, $_SESSION['BLOCKED_SUBDOMAINS']))
		{
			if($sub){
				$shopInfo = $this->db->fetchOneRow("SELECT Id,Domain FROM mc_shops WHERE Subdomain='".mysql_real_escape_string($sub)."' LIMIT 1");
				if($shopInfo->Domain){
					setLocation('',$shopInfo->Domain);
				}
				else{
					$ShopId = $shopInfo->Id;
				}
			}
			else{
				$ShopId = $this->db->fetchOne("SELECT Id FROM mc_shops WHERE Domain='".mysql_real_escape_string($fqdn)."' LIMIT 1");
			}

			if(!$ShopId)
			{
				//Subdomain wurde nicht gefunden
				setLocation('404');
			}
			else
			{
				$this->shop = new Shop($ShopId);
				$this->template = $this->db->fetchOne("SELECT Directory FROM mc_shops AS s LEFT JOIN mc_templates AS t ON s.TemplateId=t.Id WHERE s.Id='{$this->shop->getId()}'");
			}
		}
		elseif(!in_array($sub, $_SESSION['ALLOWED_SUBDOMAINS']))
		{
			// Default-Seite ohne Subdomain
			setLocation('',BASE_DOMAIN);
		}
	}
	#end

	#region private function initNestedSet()
	public function createNestedSet(&$shop)
	{
		$nstree = new NestedSet($this->db);
		$nstree->pk = 'Id';
		$nstree->name = 'Label';
		$nstree->table = 'mc_productGroups';
		$nstree->shop = &$shop;
		return $nstree;
	}
	#end
	/*#region private function initContentBoxes()
	private function initContentBoxes()
	{
		$this->contentBoxes = new ContentBoxes();
	}
	#end
*/

	#region Smarty Wrapper-Funktionen
	public function assign_direct($name, $value)
	{
		$this->smarty->assign($name, $value);
	}
	public function assign($name, $value)
	{
		if(is_array($value))
		{
			$this->smarty->assign($name, array_map_recursive('htmlspecialchars', $value));
		}
		else
		{
			$this->smarty->assign($name, htmlspecialchars($value));
		}
	}
	/*
	 * 3 Möglichkeiten:
		 1. $name = string
			=> Smarty-Name = $name und Wert aus der Datenbank = $name
		 2. $name = string, $v1 = string
			=> Smarty-Name = $name und Wert aus der Datenbank = $v1
		 3. $name = string, $v1 = array
			=> Smarty-Name = $name und Wert aus der Datenbank = $name, Werte aus $v1 werden in %s bzw. %d des lang-Strings eingefügt
		 4. $name = string, $v1 = string, $v2 = array
			=> Smarty-Name = $name und Wert aus der Datenbank = $v1, Werte aus $v2 werden in %s bzw. %d des lang-Strings eingefügt
	 */
	public function assign_say($name, $v1 = null, $v2 = null, $specialchars = true)
	{
		if($v1 == null)
		{
			$this->smarty->assign($name, $this->lang->say($name, null, $specialchars));
		}
		elseif(is_array($v1))
		{
			$this->smarty->assign($name, $this->lang->say($name, $v1, $specialchars));
		}
		else
		{
			$this->smarty->assign($name, $this->lang->say($v1, $v2, $specialchars));
		}
	}
	public function say($string, $arguments = null, $specialchars = true)
	{
		return $this->lang->say($string, $arguments, $specialchars);
	}
	#end


	public function Display(){
		$this->displayControl->setDefaultSmarty($this->smarty);

		#region Sprachänderung übernehmen
		if(isset($_GET['setLang']))
			$this->lang->setLanguage($_GET['setLang']);
		#end

		#Macht die Domain inkl. Subdomain für alle tpl-Dateien verfügbar
		$this->assign('MAIN_URL', MAIN_URL);
		#Und hier nur die Hauptseite
		$this->assign('BASE_URL', BASE_DOMAIN);
		$this->assign('DEFAULT_PROTOCOL', DEFAULT_PROTOCOL);
		#region SECURE_URL zuweisen
		if(USE_SSL)
			$this->assign('SECURE_URL', "https://secure.".BASE_DOMAIN);
		else
			$this->assign('SECURE_URL', "http://secure.".BASE_DOMAIN);
		$this->assign('LANG', $this->lang->getLangTag());
		if($this->shop) $this->assign('SHOP_ID', $this->shop->getId());
		#end


		#Währungsvariablen
		$_SESSION['Index']->assign_say('POINTSYSTEM');
		$_SESSION['Index']->assign_say('POINTSYSTEM_SHORT');
		$_SESSION['Index']->assign_direct('SECURE_URL',SECURE_URL);

		$sub = getCurrentSubdomain();

		#region Login/Register/Admin-Mode
		if($sub == 'secure'){
			if(USE_SSL && !$_SERVER['HTTPS']) setLocation(null,null,true);#Kein https? Dann aber schnell!

			#region Tell the scripts, where they can find their data
			$this->smarty->template_dir = DOC_ROOT.'templates/'._MC_ADMIN_TEMPLATE.'/';
			$this->smarty->assign('templatedir', 'templates/'._MC_ADMIN_TEMPLATE.'/');
			$this->smarty->assign('TEMPLATE', _MC_ADMIN_TEMPLATE);
			#end

			#region erlaubte Seiten definieren
			$allowedWithoutUser = array( //Diese Seiten darf jeder User sehen, auch wenn er nicht eingeloggt ist
				'Register',
				'Login',
				'ForgotPassword',
				'RegisterAdmin',
				'LoginServer',
				'ForgotPasswordServer',
				'Message'
			);
			$allowedWithUser = array( //Diese Seiten sind nur erlaubt, wenn der (normale) User tatsächlich eingeloggt ist
				'Buypoints',
				'Profile'
			);
			$allowedWithoutShop = array( //Ein eingeloggter Admin darf die Seite sehen, auch wenn er noch keinen Shop erstellt hat
				'CreateShop'
			);
			$allowedWithShop = array( //Ein eingeloggter Admin darf diese Seiten nur sehen, wenn er einen Shop in der Shop-Verwaltung aufgerufen ist
				'Account',
				'Admin',
				'Content',
				'ExclusiveShopPoints',
				'EmailPlayer',
				'ItemGroups',
				'ProductEdit',
				'Products',
				'Logo',
				'Navigation',
				'ProfileServer',
				'RegisteredPlayers',
				'RequestPayout',
				'ServerSettings',
				'Statistics'
			);
			#end
			#region normaler User
			// User ist nicht eingeloggt, nur bestimmte Seiten können betrachtet werden
			if(in_array($_GET['show'], $allowedWithoutUser))
			{
				$page = $_GET['show'];
			}
			// normaler User ist eingeloggt, es dürfen zusätzlich noch weitere Seiten betrachtet werden
			elseif($this->user->isLoggedIn() && in_array($_GET['show'], $allowedWithUser))
			{
				$page = $_GET['show'];
			}
			#end
			#region Admin-Bereich
			//Admin hat einen Shop
			elseif($_SESSION['Index']->adminShop && in_array($_GET['show'], $allowedWithShop))
			{
				$page = $_GET['show'];
			}
			//Admin hat keinen Shop
			elseif($_SESSION['CustomerId'] && in_array($_GET['show'],$allowedWithoutShop))
			{
				$page = $_GET['show'];
			}
			#end
			#region Standardseite anzeigen
			//Admin hat einen Shop, aber keine Seite in $_GET['show'] angegeben
			elseif($_SESSION['Index']->adminShop)
			{
				$page = 'Admin';
			}
			//Admin hat keinen Shop, darf also nur einen Shop erstellen
			elseif($_SESSION['CustomerId'])
			{
				$page = 'CreateShop';
			}
			//User ist nicht eingeloggt
			else
			{
				$page = 'Login';
			}
			#end
			$this->displayControl->Display($page);
		}
		#end
		#region Normal
		elseif($_GET['show'])
		{
			$this->assign('IS_LOGGED_IN',$_SESSION['Index']->user->isLoggedIn());
			$this->smarty->setTemplateDir(DOC_ROOT.'templates/'.$this->template.'/');
			$this->smarty->addTemplateDir(DOC_ROOT.'templates/'._MC_TEMPLATE.'/');
			$this->smarty->assign('templatedir', 'templates/'.$this->template.'/');
			$this->smarty->assign('TEMPLATE', $this->template);
			$this->displayControl->Display($_GET['show']);
		}
		else
		{
			$this->assign('IS_LOGGED_IN',$_SESSION['Index']->user->isLoggedIn());
			$this->smarty->setTemplateDir(DOC_ROOT.'templates/'.$this->template.'/');
			$this->smarty->addTemplateDir(DOC_ROOT.'templates/'._MC_TEMPLATE.'/');
			$this->smarty->assign('templatedir', 'templates/'.$this->template.'/');
			$this->smarty->assign('TEMPLATE', $this->template);
			$this->smarty->Display('Main.tpl');
		}
		#end
	}
}

?>