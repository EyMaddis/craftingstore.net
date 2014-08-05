<?php
defined('_MCSHOP') or die("Security block!");
class LoginServer extends aDisplayable{
	private $RedirectTime = 3;

	public function __construct(){
	}

	private function redirect($url){
		$_SESSION['Index']->assign_direct('REDIRECT_TIME',$this->RedirectTime);
		$_SESSION['Index']->assign('REDIRECT_URL',$url);
		$_SESSION['Index']->assign_say('LOGIN_TRY_REDIRECT',array($this->RedirectTime));
	}

	public function prepareDisplay()
	{
		$_SESSION['Index']->assign_say('MAIN_TITLE','ADM_LOGIN_MAIN_TITLE');
		$_SESSION['Index']->assign_say('ADM_LOGIN_TITLE');

		if($_GET['logout'] == 'do'){
			session_destroy();
			session_regenerate_id(true);
			setLocation('?show=LoginServer&logout=done');
		}
		elseif($_GET['logout'] == 'done'){
			$_SESSION['Index']->assign_say('LOGOFF_SUCCESS');
			$this->redirect('?show=LoginServer');
		}
		elseif($_SESSION['CustomerId']){
			setLocation('');
		}
		else{
			$email = $_POST['aEmail'];
			$pw = $_POST['aPw'];
			if($email && $pw && !$_SESSION['CustomerId']){
				$WaitTime = LoginWaitTimes::getRemainingWaitTime(true);
				if($WaitTime > 0)
				{
					$_SESSION['Index']->assign_say('LOGIN_MESSAGE_LABEL', 'LOGIN_ERROR_BLOCK_TIME', array($WaitTime));
				}
				else
				{
					$Customer = $_SESSION['Index']->db->fetchOneRow("SELECT Id,Password,Validated FROM mc_customers WHERE Email='".mysql_real_escape_string($email)."'");

					#region Spieler wurde noch nicht freigeschaltet, also weiterleiten zur Freischaltung
					if($Customer->Id && !$Customer->Validated)
					{
						setLocation('?show=RegisterAdmin&m='.$_POST['aEmail']);
					}
					#end
					#region Login erfolgreich
					elseif($Customer->Id && bcrypt_check($email, $pw, $Customer->Password))
					{
						LoginWaitTimes::setLoginValid(true);
						#region Es gibt mindestens einen Shop, dann den zuletzt aufgerufenen wieder laden
						$_SESSION['CustomerId'] = $Customer->Id;
						$LastShop = $_SESSION['Index']->db->fetchOneRow("SELECT s.Id, s.Label FROM mc_customers AS c LEFT JOIN mc_shops AS s ON s.Id=c.LastShopId WHERE CustomersId='{$Customer->Id}' ORDER BY Label ASC Limit 1");
						if($LastShop && $LastShop->Label)
						{
							$_SESSION['Index']->adminShop = new Shop($LastShop->Id);
							setLocation('');
						}
						#end
						#region Es gibt noch keinen Shop, weiterleiten zu CreateShop
						else
						{
							$ShopId = $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_shops WHERE CustomersId='{$Customer->Id}' ORDER BY Label ASC Limit 1");
							if($ShopId)
							{
								$_SESSION['Index']->adminShop = new Shop($ShopId);
								setLocation('');
							}
							else
							{
								setLocation('?show=CreateShop&new');
							}
						}
						#end
					}
					#endregion
					#region Login fehlgeschlagen
					else
					{
						$WaitTime = LoginWaitTimes::getNextWaitTimeAfterLoginError(true);
						if($WaitTime > 0)
							$_SESSION['Index']->assign_say('LOGIN_MESSAGE_LABEL', 'LOGIN_ERROR_WRONG_DATA', array($WaitTime));
						else
							$_SESSION['Index']->assign_say('LOGIN_MESSAGE_LABEL', 'LOGIN_ERROR_WRONG_DATA_NO_TIME');
					}
					#endregion
				}
			}

			$_SESSION['Index']->assign_say('ADM_LOGIN_DESCRIPTION');
			$_SESSION['Index']->assign_say('ADM_LOGIN_SWITCH_TO_USER_LOGIN');
			$_SESSION['Index']->assign_say('ADM_LOGIN_OPEN_CUSTOM_SHOP');
			$_SESSION['Index']->assign_say('ADM_LOGIN_REGISTER_NOW');
			$_SESSION['Index']->assign_say('ADM_LOGIN_FORGOT_PASSWORD');
			$_SESSION['Index']->assign_say('ADM_LOGIN_GOT_NO_MAIL');
			$_SESSION['Index']->assign_say('ADM_LOGIN_NOW');

			$_SESSION['Index']->assign_say('ADM_LOGIN_EMAIL');
			$_SESSION['Index']->assign_say('ADM_LOGIN_PASSWORD');
		}
	}
}
?>