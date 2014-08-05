<?php
defined('_LOGIN') or die("Security block!");

#region Für Diese Woche noch extras berechen
$w = date('w');#0 (für Sonntag) bis 6 (für Samstag)
$dEnde=($w==0?0:7-$w);
$dStart = $dStart-6;
#endregion

#region Zeiträume
$zeiten = array(
'Heute' => array(
		'start' => mktime(0,0,0),
		'end' => mktime(24,0,0)),
	'Gestern' => array(
		'start' => mktime(-24,0,0),
		'end' => mktime(0,0,0)),
	'Diese Woche' => array(
		'start' => mktime(0,0,0,date('n'),date('j')+$dStart),
		'end' => mktime(24,0,0,date('n'),date('j')+$dEnde)),
	'Dieser Monat' => array(
		'start' => mktime(0,0,0,date('n'),1),
		'end' => mktime(24,0,0,date('n'),date('t'))),
	'Gesamt' => array(
		'start' => 0,
		'end' => time()+1,
	));
#endregion

#region Funktionen zum Daten ermitteln
function alsEuro($cents){
	return str_replace('.',',',sprintf('%01.2f',$cents/100)).' €';
}

function EigenerUmsatz($time,$db){
	return $db->fetchOne("SELECT Sum(Difference) FROM mc_ouraccount WHERE Time>='{$time['start']}' AND Time<'{$time['end']}'");
}
function EinzahlungenSpieler($time,$db){
	return $db->fetchOne("SELECT SUM(Revenue) FROM mc_gameraccounts WHERE Action='INPAYMENT' AND Time>='{$time['start']}' AND Time<'{$time['end']}'");
}
function AusgabenSpieler($time,$db){
	return $db->fetchOne("SELECT SUM(Revenue) FROM mc_gameraccounts WHERE Action='BOUGHT_ITEM' AND Time>='{$time['start']}' AND Time<'{$time['end']}'");
}
function AuszahlungenAnShopbetreiber($time,$db){
	return $db->fetchOne("SELECT SUM(Difference) FROM mc_customeraccounts WHERE PayoutStatus>0 AND Time>='{$time['start']}' AND Time<'{$time['end']}'");
}
#end
#region style+sciprt
echo <<<html
<script type="text/javascript">
<!--
function submitenter(myfield,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;

	if (keycode == 13)
	{
		if(navigator.userAgent.search(/Firefox/)>0) return true;
		else
		{
			myfield.form.submit();
			return false;
		}
	}
	else return true;
}
//-->
</script>
<style type="text/css">
p{
	margin-top: 30px;
	text-align: center;
}
table{
	border: 0;
	border-spacing: 0;
	margin: auto;
	white-space: nowrap;
}
th,td{
	padding: 4px;
	text-align: left;
}

table.overview tr > *:nth-child(n+2){
	padding: 4px;
	text-align:right;
	border-left: 1px solid #999;
}
table.overview tr:nth-child(2n+2) > *{
	border-top: 1px solid #999;
	background-color: #eee;
}


table.transactions td,
table.transactions th{
	text-align:center;
}
table.transactions tr:nth-child(2n+2) > *{
	border-top: 1px solid #999;
	background-color: #eee;
}
table.transactions td:last-child{
	text-align:right;
	width: 80px;
}
table.transactions tr:nth-child(n+2) > *{
	border-top: 1px solid #999;
}
.auszahlung{
	color: #f00;
}
</style>
<div style="overflow-x: auto; overflow-y: hidden;">
<table class="overview"><tr><th></th>
html;
#endregion

#region Benutzer-Zeiten bearbeiten
if(!isset($_SESSION['customTimes']))
	$_SESSION['customTimes'] = array();
if(isset($_GET['del']) && array_key_exists($_GET['del'],$_SESSION['customTimes']))
{
	unset($_SESSION['customTimes'][$_GET['del']]);
}
$custom = array();
if($_POST['customStart'] && $_POST['customEnd'])
{
	$start = strtotime($_POST['customStart']);
	$end = strtotime($_POST['customEnd']);
	if($start > 0 && $end > $start)
	{
		$_SESSION['customTimes'][] = array('start' => $start, 'end' => $end);
	}
}
#endregion
#region Übersicht
foreach($zeiten as $value => $row){
	echo '<th>'.$value.'</th>';
}
foreach($_SESSION['customTimes'] as $key => $value){
	echo '<th><a href="?p='.$_GET['p'].'&amp;del='.$key.'"><img src="delete.png" title="Diesen Bereich löschen" style="border:0;" /></a>  '.date('d.m.Y', $value['start']).'<br />bis '.date('d.m.Y', $value['end']).'</th>';
}
echo '
<td>
	<form action="?p='.$_GET['p'].'" method="post">
		Von <input type="text" name="customStart" value="'.htmlspecialchars($customStart).'" onKeyPress="return submitenter(this,event)" style="width: 90px;" /><br />
		Bis <input type="text" name="customEnd" value="'.htmlspecialchars($customEnd).'" onKeyPress="return submitenter(this,event)" style="width: 90px;" />
		<input type="submit" style="display: none;" />
	</form>
</td>
</tr>
<tr>
<td>Einzahlungen der Spieler</td>';
foreach($zeiten as $row){
	echo '<td>'.alsEuro(EinzahlungenSpieler($row,$db)).'</td>';
}
foreach($_SESSION['customTimes'] as $row){
	echo '<td>'.alsEuro(EinzahlungenSpieler($row,$db)).'</td>';
}
echo '
<td></td>
</tr>
<tr>
<td>Ausgaben der Spieler</td>';
foreach($zeiten as $row){
	echo '<td>'.alsEuro(AusgabenSpieler($row,$db)).'</td>';
}
foreach($_SESSION['customTimes'] as $row){
	echo '<td>'.alsEuro(AusgabenSpieler($row,$db)).'</td>';
}
echo '
<td></td>
</tr>
<tr>
<td>Auszahlungen an Shopbetreiber</td>';
foreach($zeiten as $row){
	echo '<td>'.alsEuro(AuszahlungenAnShopbetreiber($row,$db)).'</td>';
}
foreach($_SESSION['customTimes'] as $row){
	echo '<td>'.alsEuro(AuszahlungenAnShopbetreiber($row,$db)).'</td>';
}
echo '
<td></td>
</tr>
<tr>
<td>Eigener Umsatz</td>';
foreach($zeiten as $row){
	echo '<td>'.alsEuro(EigenerUmsatz($row,$db)).'</td>';
}
foreach($_SESSION['customTimes'] as $row){
	echo '<td>'.alsEuro(EigenerUmsatz($row,$db)).'</td>';
}
echo '
<td></td>
</tr>
</table>
</div>';
#end
$sum = 0;
$output = '';
foreach($db->iterate("SELECT CustomersId, Difference, Time, 'ca' AS 'Table', c.MinecraftName AS 'Name' FROM mc_customeraccounts AS ca
LEFT JOIN mc_customers AS c ON  c.Id=ca.CustomersId
WHERE PayoutStatus>0

UNION SELECT GamerId,Difference,Time, 'ga', g.Minecraftname FROM mc_gameraccounts AS ga
LEFT JOIN mc_gamer AS g ON g.Id=ga.GamerId
WHERE Action='1'

UNION SELECT '0',Difference,Time,'oa',oa.PayoutMail FROM mc_ouraccount AS oa
WHERE PayoutMail IS NOT NULL

ORDER BY Time DESC") as $row)
{
	if($row->Table == 'ca')
	{
		$text = 'Auszahlung an '.$row->Name;
		$row->Difference = -$row->Difference;
	}
	elseif($row->Table == 'ga')
	{
		if($row->Difference >= 0)
		{
			$text = 'Einzahlung von '.$row->Name;
		}
		else
		{
			$text = 'Auszahlung an '.$row->Name;
		}
	}
	elseif($row->Table == 'oa')
	{
		$row->Difference = -$row->Difference;
		$text = 'Privatentnahme von '.$row->Name;
	}
	else
	{
		$text = '';
	}
	$sum += $row->Difference;
	$output .= '
<tr>
	<td>'.date('d.m.Y',strtotime($row->Time)).'</td>
	<td>'.$text.'</td>
	<td'.($row->Difference<0?' class="auszahlung"':'').'>'.alsEuro($row->Difference/POINTS_PER_EURO).'</td>
</tr>';
}
echo '<p>Saldo zum '.date('d.m.Y H:i:s').': '.alsEuro($sum/POINTS_PER_EURO).'</p>';
echo <<<html
<table class="transactions">
<tr>
	<th>Datum</th>
	<th>Beteiligter</th>
	<th>Betrag</th>
</tr>{$output}
</table>
html;
?>