<?php
defined('_LOGIN') or die("Security block!");

$allowedSortColumns = array('Nickname','Email','MinecraftName','RegTime','Validated');

$sort = ($_GET['sort'] == 'desc' ? 'desc' : 'asc');
$sortReverse = ($sort == 'asc' ? 'desc' : 'asc');

if(in_array($_GET['order'],$allowedSortColumns)){
	$order = " ORDER BY ".$_GET['order'].' '.$sort;
}

echo '<table>
<tr>
	<th><a href="?p='.$_GET['p'].'&sort='.($_GET['order'] == 'Nickname' ? $sortReverse : $sort).'&order=Nickname">Nickname</a></th>
	<th><a href="?p='.$_GET['p'].'&sort='.($_GET['order'] == 'Email' ? $sortReverse : $sort).'&order=Email">Email</a></th>
	<th><a href="?p='.$_GET['p'].'&sort='.($_GET['order'] == 'MinecraftName' ? $sortReverse : $sort).'&order=MinecraftName">MinecraftName</a></th>
	<th><a href="?p='.$_GET['p'].'&sort='.($_GET['order'] == 'RegTime' ? $sortReverse : $sort).'&order=RegTime">RegTime</a></th>
	<th><a href="?p='.$_GET['p'].'&sort='.($_GET['order'] == 'Validated' ? $sortReverse : $sort).'&order=Validated">Validated</a></th>
</tr>';
foreach($db->iterate("SELECT Id,Nickname,Email,MinecraftName,RegTime,Validated FROM mc_gamer".$order) as $row){
	echo "
<tr>
	<td>{$row->Nickname}</td>
	<td>{$row->Email}</td>
	<td>{$row->MinecraftName}</td>
	<td>".date('d.m.Y h:s:i',$row->RegTime)."</td>
	<td>{$row->Validated}</td>
</tr>";
}
echo '</table>';
?>