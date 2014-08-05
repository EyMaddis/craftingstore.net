<?php
defined('_MCSHOP') or die("Security block!");

class ExclusiveShopPoints extends aDisplayable
{
	public function prepareDisplay()
	{
		$anzeigen = true;
		if($_POST['submit'])
		{
			$points = $_POST['points'];
			$playerId = $_POST['player'];

			//Player-Id ist gültig
			$validPlayer = isNumber($playerId) && ($playerId == $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_gamer AS g INNER JOIN mc_permittedshops AS p ON g.Id=p.GamerId WHERE g.Id='$playerId' AND p.ShopId='".$_SESSION['Index']->adminShop->getId()."' LIMIT 1"));

			if(isNumber($points,false,true) && $validPlayer)
			{
				User::givePlayerBonusPoints($playerId, $_SESSION['Index']->adminShop->getId(), $points);
				$anzeigen = false;
			}
		}
		else
		{
			$points = $_GET['p'];
		}

		if($anzeigen){
			$_SESSION['Index']->assign_direct('INPUT', true);

			$player = array();
			foreach($_SESSION['Index']->db->iterate("SELECT g.Id,g.Nickname FROM mc_gamer AS g INNER JOIN mc_permittedshops AS p ON g.Id=p.GamerId WHERE p.ShopId='".$_SESSION['Index']->adminShop->getId()."'") as $row)
			{
				$player[] = array('Id' => $row->Id, 'Name' => $row->Nickname, 'selected' => ($playerId ? $row->Id == $playerId : $row->Id == $_GET['g']));
			}
			$_SESSION['Index']->assign('PLAYER', $player);
			$_SESSION['Index']->assign('POINTS', $points);
			$_SESSION['Index']->assign_say('ADM_BONUS_GIVE');
			$_SESSION['Index']->assign_say('ADM_BONUS_CANCEL');
			$_SESSION['Index']->assign_say('ADM_DISTRIBUTE_BONUS_PLAYER');
			$_SESSION['Index']->assign_say('ADM_DISTRIBUTE_BONUS_AMOUNT');
			$_SESSION['Index']->assign('ADM_BONUS_BACK_URL','?show=RegisteredPlayers');
		}
		else{
			$_SESSION['Index']->assign_say('ADM_BONUS_GIVEN', array($_SESSION['Index']->db->fetchOne("SELECT Nickname FROM mc_gamer AS g INNER JOIN mc_permittedshops AS p ON g.Id=p.GamerId WHERE g.Id='$playerId' AND p.ShopId='".$_SESSION['Index']->adminShop->getId()."' LIMIT 1"), $points));
			$_SESSION['Index']->assign('ADM_BONUS_BACK_URL','?show=RegisteredPlayers');
			$_SESSION['Index']->assign_say('ADM_BONUS_BACK');
		}
		$_SESSION['Index']->assign_say('ADM_DISTRIBUTE_BONUS_TITLE');
	}
}
?>