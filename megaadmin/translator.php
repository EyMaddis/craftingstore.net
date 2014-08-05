<?php
defined('_LOGIN') or die("Security block!");

#region Sprache auswählen
if($db->fetchOne("SELECT COUNT(*) FROM mc_languages WHERE Id='".htmlspecialchars($_POST['selectLang'])."'") == 1)
{
	$_SESSION['currentLang'] = $_POST['selectLang'];
}

echo '
<form method="post" action="?p='.$_GET['p'].'">
<select name="selectLang">';
foreach($db->iterate("SELECT Id,Language FROM mc_languages") as $row)
{
	echo '<option value="'.$row->Id.'"'.($_SESSION['currentLang'] == $row->Id?' selected="selected"':'').'>'.htmlspecialchars($row->Language).'</option>';
}
echo '</select><br>
<input type="submit" value="Wählen" />
</form>';
#end


if($_SESSION['currentLang'] > 0)
{

#region Seitenzahlen
$zeilenProSeite = 50;
$seiten = ceil(($countOrigin = $db->fetchOne("SELECT COUNT(*) FROM mc_translations WHERE LanguagesId='1'"))/$zeilenProSeite);
$seite = 0;
if(isNumber($_GET['s'],1) && $_GET['s'] < $seiten)
{
	$seite = $_GET['s'];
}

echo 'Seiten:';
for($i=0; $i<$seiten; $i++){
	if($seite == $i)
		echo ' <a href="?p='.$_GET['p'].'&amp;s='.$i.'"><b>'.($i+1).'</b></a>';
	else
		echo ' <a href="?p='.$_GET['p'].'&amp;s='.$i.'">'.($i+1).'</a>';
}

$limit = ($seite*$zeilenProSeite).','.$zeilenProSeite;
#end


#region Speichern
foreach($db->iterate("
SELECT
	org.Label,
	org.Translation as org,
	trans.Translation AS translation

FROM mc_translations AS org
LEFT JOIN mc_translations AS trans
ON trans.Label=org.Label AND trans.LanguagesId='{$_SESSION['currentLang']}'
WHERE org.LanguagesId='1' LIMIT ".$limit) as $row)
{
	$oldBb = ($_POST['oldBb'][$row->Label] ? true : false);
	$newBb = ($_POST['newBb'][$row->Label] ? true : false);

	if($_POST['translation'][$row->Label] && 
		($_POST['translation'][$row->Label] != $_POST['original'][$row->Label])
		|| ($oldBb != $newBb))
	{
		$translation = mysql_real_escape_string($_POST['translation'][$row->Label]);
		$db->query("INSERT INTO mc_translations (Label,LanguagesId,Translation,parseBBCode) VALUES ('{$row->Label}','{$_SESSION['currentLang']}','$translation','$bbcode')
			ON DUPLICATE KEY UPDATE Translation='$translation',parseBBCode='$newBb'");
	}
}
#end

#region Prozentsatz ermitteln
//Gesamtzahl Einträge der aktuellen Sprache
echo '<br />Übersetzung abgeschlossen zu '.floor(($db->fetchOne("SELECT COUNT(*) FROM mc_translations WHERE LanguagesId='{$_SESSION['currentLang']}' AND translation<>''"))/$countOrigin*100).'%.<br /><br />';
#endregion

echo '<br>
<form method="post" action="?p='.$_GET['p'].'&amp;s='.$_GET['s'].'">
<table style="font-size:8pt; width:100%" class="lang">
<tr>
	<th style="width:1%">Label</th>
	<th style="width:30%">Original-Text</th>
	<th style="width:68%">Übersetzung</th>
	<th style="width:1%">BBCode verwenden</th>
</tr>';

foreach($db->iterate("
SELECT
	org.Label,
	org.Translation AS org,
	trans.Translation AS translation,
	trans.parseBBCode AS oldBb

FROM mc_translations AS org
LEFT JOIN mc_translations AS trans
ON trans.Label=org.Label AND trans.LanguagesId='{$_SESSION['currentLang']}'
WHERE org.LanguagesId='1' LIMIT ".$limit) as $row)
{
	if(strpos($row->org,"\r") !== false || strpos($row->org,"\n") !== false)
	{
		echo '
<tr'.($row->translation ? '' : ' class="empty"').'>
	<td>'.$row->Label.'</td>
	<td><textarea readonly>'.htmlspecialchars($row->org).'</textarea></td>
	<td>
		<input type="hidden" name="original['.$row->Label.']" value="'.htmlspecialchars($row->translation).'" />
		<textarea name="translation['.$row->Label.']">'.htmlspecialchars($row->translation).'</textarea>
	</td>
	<td>
		<input type="hidden" name="oldBb['.$row->Label.']" value="'.$row->oldBb.'" />
		<input type="checkbox" name="newBb['.$row->Label.']" value="1"'.($row->oldBb?' checked="checked"':'').' />
	</td>
</tr>';
	}
	else
	{
		echo '
<tr'.($row->translation ? '' : ' class="empty"').'>
	<td>'.$row->Label.'</td>
	<td><input type="text" readonly value="'.htmlspecialchars($row->org).'" /></td>
	<td>
		<input type="hidden" name="original['.$row->Label.']" value="'.htmlspecialchars($row->translation).'" />
		<input type="text" name="translation['.$row->Label.']" value="'.htmlspecialchars($row->translation).'" />
	</td>
	<td>
		<input type="hidden" name="oldBb['.$row->Label.']" value="'.$row->oldBb.'" />
		<input type="checkbox" name="newBb['.$row->Label.']" value="1"'.($row->oldBb?' checked="checked"':'').' />
	</td>
</tr>';
	}
}
echo '</table><br>
<input type="submit" value="Speichern" />
</form>';
#end
}
?>