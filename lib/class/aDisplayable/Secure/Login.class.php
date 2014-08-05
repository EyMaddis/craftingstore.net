<?php
defined('_MCSHOP') or die("Security block!");
class Login extends aDisplayable{
	private $RedirectTime = 3;

	private function redirect($url){
		$_SESSION['Index']->assign_direct('REDIRECT_TIME',$this->RedirectTime);
		$_SESSION['Index']->assign('REDIRECT_URL',$url);
		$_SESSION['Index']->assign_say('LOGIN_TRY_REDIRECT',array($this->RedirectTime));
	}

	#region Loggt den Spieler aus
	private function logout(){
		if($_GET['logoff']){
			$_SESSION['Index']->user->Logout($_GET['logoff']);
			setLocation('?show=Login&logoff&shop='.$_GET['shop']);
		}
		else{
			$_SESSION['Index']->assign_say('LOGOFF_TITLE');
			$_SESSION['Index']->assign_say('LOGOFF');

			#region Weiterleitung
			//Zum normalen Login
			if(!isNumber($_GET['shop'])){
				$this->redirect('?show=Login');
			}
			elseif($_GET['logoff'] == 'Login'){
				$this->redirect('?show=Login&shop='.$_GET['shop']);
			}
			//Zum Shop weiterleiten
			else{
				$this->redirect('?red&shop='.$_GET['shop']);
			}
			#end
		}
	}
	#end
	public function prepareDisplay(){
		$_SESSION['Index']->assign_say('MAIN_TITLE','LOGIN_MAIN_TITLE');
		if(isset($_GET['logoff'])){
			$this->logout();
		}
		else{
			$lang = $_SESSION['Index']->lang->getLangTag();
			$ShopInfo = Shop::ShopInfo($_GET['shop']);

			#region ShopUrl zusammenbauen, falls ein Shop angegeben ist
			if($ShopInfo){
				if($ShopInfo->Domain)
					$ShopDomain = $ShopInfo->Domain;
				else
					$ShopDomain = $ShopInfo->Subdomain.'.'.BASE_DOMAIN;
				$ShopUrl = 'http://'.$ShopDomain;
			}
			#end

			#region User ist bereits eingeloggt, weiterleiten
			if($_SESSION['Index']->user->isLoggedIn()){
				if($_SESSION['Index']->user->isValidated()){
					if($ShopInfo->Id){
						if($_SESSION['Index']->db->fetchOne("SELECT g.Validated FROM mc_gamer AS g
INNER JOIN mc_permittedshops AS p ON p.ShopId='{$ShopInfo->Id}' AND p.GamerId='{$_SESSION['Index']->user->getLoginId()}'
WHERE GamerId='{$_SESSION['Index']->user->getLoginId()}' LIMIT 1")){
							setLocation('?red&shop='.$ShopInfo->Id);
						}
						else{
							$_SESSION['Index']->assign_say('LOGIN_UNLOCK_SHOP_TITLE');
							if(isset($_GET['unlock'])){
								User::addPermittedShop($ShopInfo->Id, $_SESSION['Index']->user->getLoginId());
								$_SESSION['Index']->assign_say('LOGIN_UNLOCK_SHOP_SUCCESS', array($ShopDomain));
								$this->redirect('?red&shop='.$ShopInfo->Id);
							}
							else{
								$_SESSION['Index']->assign_say('LOGIN_ACTIVATE_ANOTHER_SHOP', array($ShopDomain));
								$_SESSION['Index']->assign_say('LOGIN_ACTIVATE_ANOTHER_SHOP_YES');
								$_SESSION['Index']->assign_say('LOGIN_ACTIVATE_ANOTHER_SHOP_CANCEL');
								$_SESSION['Index']->assign_say('LOGIN_ACTIVATE_ANOTHER_SHOP_CANCEL_DESCRIPTION');
							}
						}
					}
					else{
						setLocation('?show=Profile');
					}
				}
				else{
					//Ein nicht validierter Spieler kann nicht eingeloggt sein
					$_SESSION['Index']->user->Logout();
				}
			}
			#end

			#region Der User hat Logindaten eingegeben
			if(isset($_POST['username']) || isset($_POST['password'])){
				switch($_SESSION['Index']->user->tryLogin($_POST['username'], $_POST['password'], $ShopInfo->Id, $wait_time)){
					case -1:
						//Weiterleiten zur Seite zum Shop freischalten
						setLocation('?show=Login&shop='.$_GET['shop']);
						break;
					case -2:
						if($wait_time > 0)
							$_SESSION['Index']->assign_say('LOGIN_ERROR', 'LOGIN_ERROR_WRONG_DATA', array($wait_time));
						else
							$_SESSION['Index']->assign_say('LOGIN_ERROR', 'LOGIN_ERROR_WRONG_DATA_NO_TIME');
						break;
					case -3:
						$_SESSION['Index']->assign_say('LOGIN_ERROR', 'LOGIN_ERROR_BLOCK_TIME', array($wait_time));
						break;
					case -4:
						if($ShopInfo->Id){
							setLocation('?show=Register&notCompleted&shop='.$ShopInfo->Id);
						}
						else{
							setLocation('?show=Register&notCompleted');
						}
						break;
					default: //Login erfolgreich
						if($ShopInfo->Id)
							$this->redirect('?shop='.$_GET['shop'].'&red');
						else
							$this->redirect('?show=Profile');
						$_SESSION['Index']->assign_say('LOGIN_LOGIN_HEADER');
						$_SESSION['Index']->assign_say('LOGIN_CORRECT_LABEL');
						$login_correct = true;
				}
			}
			#end
			if($ShopInfo)
				$_SESSION['Index']->assign_direct('SHOP_ID',$ShopInfo->Id);
			if(!$login_correct){
				$_SESSION['Index']->assign_say('BACK');
				$_SESSION['Index']->assign('LOGIN_BACK_LINK',$ShopUrl);
				$_SESSION['Index']->assign_say('LOGIN_LOGIN_HEADER');
				$_SESSION['Index']->assign_say('LOGIN_USERNAME');
				$_SESSION['Index']->assign_say('LOGIN_PASSWORD');
				$_SESSION['Index']->assign_say('LOGIN_SUBMIT');

				if($ShopDomain){
					$_SESSION['Index']->assign_say('LOGIN_SHOP_DESCRIPTION',array($ShopDomain));
				}else{
					$_SESSION['Index']->assign_say('LOGIN_SHOP_DESCRIPTION','LOGIN_NO_SHOP_DESCRIPTION');
				}

				$_SESSION['Index']->assign_say('LOGIN_NOT_REGISTERED');
				$_SESSION['Index']->assign_say('LOGIN_FORGOT_PASSWORD');
				$_SESSION['Index']->assign_say('LOGIN_GOT_NO_MAIL');

				$_SESSION['Index']->assign_say('LOGIN_SWITCH_TO_ADMIN_LOGIN');
				$_SESSION['Index']->assign('THIS_URL',urlencode($_SERVER['REQUEST_URI']));
			}
		}
	}
}
?>