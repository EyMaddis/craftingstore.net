<?php
defined('_LOGIN') or die("Security block!");

if(isNumber($_GET['accept']))
{
	if(1 == $db->fetchOne("SELECT COUNT(*) FROM mc_customeraccounts WHERE ShopId IS NULL AND (PayoutStatus='{$_GET['accept']}' OR Id='{$_GET['accept']}')"))
	{
		$row = $db->fetchOneRow("SELECT CustomersId,Current,Difference,PayoutStatus,PayoutMail FROM mc_customeraccounts WHERE Id='{$_GET['accept']}'");
		$db->query("INSERT INTO mc_customeraccounts (CustomersId,Current,Difference,PayoutStatus,PayoutMail) VALUES ('{$row->CustomersId}','{$row->Current}','{$row->Difference}','{$_GET['accept']}','{$row->PayoutMail}')");
		setLocation('/megaadmin/?p='.$_GET['p']);
	}
}


echo 'Auszahlungsübersicht:<br />
<table border="1" style="white-space: nowrap;">
<tr>
	<th>Buchung</th>
	<th>Valuta</th>
	<th>Name</th>
	<th>Kundennummer</th>
	<th>Paypal-<br />Empfängeradresse</th>
	<th>Betrag</th>
	<th></th>
</tr>';
$erledigteAuszahlungen = array();
foreach($db->iterate("SELECT
c1.Id,
cu.FirstName,
cu.SurName,'?' AS CustomerNumber,
-- cu.CustomerNumber,
c1.Difference,
c1.PayoutMail,
c1.Time AS ValutaTime,
c2.Time AS BookingTime

FROM mc_customeraccounts AS c1
LEFT JOIN mc_customeraccounts AS c2
ON c2.PayoutStatus=c1.Id

LEFT JOIN mc_customers AS cu
ON c1.CustomersId=cu.Id

WHERE c1.PayoutStatus=0
ORDER BY c1.Time DESC") as $row)
{
	echo '
<tr>
	<td>'.($row->BookingTime?date('d.m.Y H:i:s', strtotime($row->BookingTime)):'').'</td>
	<td>'.date('d.m.Y H:i:s', strtotime($row->ValutaTime)).'</td>
	<td>'.$row->FirstName.' '.$row->SurName.'</td>
	<td>'.$row->CustomerNumber.'</td>
	<td>'.$row->PayoutMail.'</td>
	<td>'.str_replace('.', ',', sprintf('%01.2f', $row->Difference/100)).' €</td>
	<td>'.($row->BookingTime==0?'<a href="?p='.$_GET['p'].'&amp;accept='.$row->Id.'" title="Auszahlung als Bestätigt markieren">Ausstehend</a>':'Erledigt').'</td>
</tr>';
}
if(!$row)
{
	echo '<tr><td colspan="7">keine Auszahlungen beantragt</td></tr>';
}
echo '</table>';


?>