<?php
header('Content-type: text/html; charset=utf-8');

$headers = getallheaders();
foreach ($headers as $name => $content) {
    echo "headers[$name] = $content<br />\n";
}
echo $_SERVER['REQUEST_URI'].'<br>Länge: '.strlen($_SERVER['REQUEST_URI']);

$hosts = array(
'toxicbuild.net',
'threeredstonersfac.no-ip.biz',
'test.mcgalaxy.beastnode.net',
'shadowscraft.servegame.com',
's27.hosthorde.com',
'play.minermovies.com',
'play.mcearth.dk',
'play.gamebasement.net',
'ouab.zapto.org',
'minetherapy.com',
'mineslmb.no-ip.biz',
'mikemcserver.tk',
'mczyber.net',
'mc14.crew.sk',
'mc.relaxcraft.net',
'mc.mibblecraft.com',
'mc.jumalauta.net',
'mc.blushell.net',
'mastersofminecraft.no-ip.org',
'kaleydra.de',
'jonascraftable.zapto.org',
'ftb.tec-your-life.de',
'chroma.selfhost.me',
'EternityPvP.net'
);
print_r($hosts);
foreach($hosts as $value){
	echo $value.' '.gethostbyname($value).'<br>';
}
?>