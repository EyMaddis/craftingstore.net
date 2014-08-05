<?php
defined('_LOGIN') or die("Security block!");
define('_DOMAIN','.craftingstore.net');

if(isNumber($_GET['go']))
{
	$ShopId = $_GET['go'];
	$CustomerId = $db->fetchOne("SELECT CustomersId FROM mc_shops WHERE Id='$ShopId' Limit 1");
	if(isNumber($CustomerId))
	{
		$_SESSION['CustomerId'] = $CustomerId;
		require_once('../lib/lib.include.php');
		$_SESSION['Index'] = new Index();
		$_SESSION['Index']->adminShop = new Shop($ShopId);
		setLocation('');
	}
}

echo <<<html
<style type="text/css">
.center{
	text-align: center;
}
</style>

<h3>Shop-Administration</h3><br />
<table border="1">
<tr>
	<th>Shop-Name</th>
	<th>Domain</th>
	<th>Besitzer</th>
	<th>Registrierte Spieler</th>
	<!--<th>gesperrt</th>-->
	<th>Online</th>
	<th></th>
</tr>
html;

foreach($db->iterate("SELECT
	s.Id,
	s.Label,
	s.Subdomain,
	s.Domain,
	c.FirstName,
	c.SurName,
	(SELECT COUNT(*) FROM mc_permittedshops WHERE ShopId=s.Id) AS PlayerCount,
	s.ServerOnline
FROM mc_shops AS s
LEFT JOIN mc_customers AS c
ON c.Id=s.CustomersId
ORDER BY ServerOnline DESC, PlayerCount DESC, Subdomain ASC, FirstName ASC, SurName ASC") as $row)
{
	echo '
<tr>
	<td>'.htmlspecialchars($row->Label).'</td>
	<td>'.($row->Domain ? '<a href="http://'.htmlspecialchars($row->Domain).'" target="_blank">'.htmlspecialchars($row->Domain).'</a>':'<a href="http://'.htmlspecialchars($row->Subdomain)._DOMAIN.'" target="_blank">'.htmlspecialchars($row->Subdomain).'</a>').'</td>
	<td>'.htmlspecialchars(($row->SurName && $row->FirstName ? $row->FirstName.' '.$row->SurName : $row->FirstName.$row->SurName)).'</td>
	<td class="right">'.$row->PlayerCount.'</td>
	<!--<td class="center"><input type="checkbox" name="disabled[]" value="'.htmlspecialchars($row->Id).'" /></td>-->
	<td class="center">'.$row->ServerOnline.'</td>
	<td><a href="?p='.$_GET['p'].'&amp;go='.htmlspecialchars($row->Id).'" target="_blank" title="Shop-Administration aufrufen"><img src="link_go.png" border="0" /></a></td>
</tr>';
}

echo '</table>';

?>