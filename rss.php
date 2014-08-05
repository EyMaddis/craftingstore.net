<?php
define("_MCSHOP", true);
require_once(dirname(__FILE__).'/lib/lib.include.php');

if(getCurrentSubdomain($_SERVER['HTTP_HOST']) != 'info') setLocation('404');

header("Content-Type: application/rss+xml");
$db = MySqlDatabase::getInstance();
if(!$db->isConnected())
{
    try
    {
        $db->connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
        $db->query("SET NAMES 'utf8'");
    }
    catch (Exception $e)
    {
        die("ERROR: ".$e->getMessage());
    }
}

$sql = "SELECT Id, Label, Subdomain, RegTime FROM mc_shops ORDER BY RegTime DESC LIMIT 0,10;";
$result = $db->query($sql);
echo '<?xml version="1.0" encoding="utf-8"?>
 
<rss version="2.0">
 
  <channel>
    <title>Newest Shops</title>
    <link>http://minecraftshop.net</link>
    <description>The newest created Shops at Minecraftshop.net</description>
    <language>en</language>
    <copyright>Minecraftshop.net</copyright>
 '; 
 
while ($shop = mysql_fetch_assoc($result)) 
{
echo '<item>
      <title>'.$shop['Label'].'</title>
      <description></description>
      <link>http://'.$shop['Subdomain'].'.minecraftshop.net/</link>
      <guid>'.$shop['Id'].'</guid>
      <pubDate>'.$shop['RegTime'].'</pubDate>
    </item> ';
}
echo "  </channel>
 
</rss>";
?>