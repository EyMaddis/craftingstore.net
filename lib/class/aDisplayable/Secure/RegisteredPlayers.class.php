<?php
defined('_MCSHOP') or die("Security block!");

class RegisteredPlayers extends aDisplayable
{
	private $pagination;
	function __construct()
	{
		$this->pagination = new Pagination();
	}
	public function prepareDisplay()
	{
		$_SESSION['Index']->assign_say('SHOP_TITLE');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_BOX_TITLE');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_ID_LABEL');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_NICKNAME_LABEL');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_MINECRAFTNAME_LABEL');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_REVENUE_LABEL');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_EMAIL_LABEL');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_EMAIL');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_GIVEBONUS');
		$_SESSION['Index']->assign_say('ADM_PLAYERS_BONUS_LABEL');

		$this->pagination->prepare($_SESSION['Index']->db->fetchOne("SELECT COUNT(*) FROM mc_gamer AS g INNER JOIN mc_permittedshops AS p on p.GamerId=g.id AND p.ShopId='".$_SESSION['Index']->adminShop->getId()."'"), '?show=RegisteredPlayers'.$searchPagination, $first, $number);


		$players = array();
		foreach($_SESSION['Index']->db->iterate("SELECT g.Id, g.Nickname, g.MinecraftName, SUM(ga.Difference+ga.BonusDifference) AS Revenue, p.BonusPoints AS Bonus
FROM mc_gamer AS g
INNER JOIN mc_permittedshops AS p
on p.GamerId=g.Id AND p.ShopId='".$_SESSION['Index']->adminShop->getId()."'
LEFT JOIN mc_gameraccounts AS ga
ON ga.GamerId=g.Id AND ga.ShopId='".$_SESSION['Index']->adminShop->getId()."' AND ga.Action='BOUGHT_ITEM'
GROUP BY g.Id
ORDER BY Revenue DESC LIMIT $first,$number") as $row)
		{
			$players[] = array(#'Id' => $row->Id,
				'Nickname' => $row->Nickname,
				'MinecraftName' => $row->MinecraftName,
				'Revenue' => ($row->Revenue ? $row->Revenue : '0'),
				'Bonus' => ($row->Bonus ? $row->Bonus : '0'),
				'email_url' => './?show=EmailPlayer&playerId='.$row->Id);
		}
		$_SESSION['Index']->assign('ADM_PLAYERS_PLAYERS', $players);
	}
}
?>