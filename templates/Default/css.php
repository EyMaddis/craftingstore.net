<?php

require_once('../../lib/class/multipleFileCompressor.php');

$cssCache = new Caching('css','text/css','css/',array('cart.css','inventory.css','itemdetails.css','login.css','main.css','jquery-ui-1.8.13.custom.css','registerbox.css','style_red.css','lmenu.css','itemsearch.css','contentbox.css','toppanel.css','itembox.css','smartpaginator.css','transferHistory.css','pendingTransfers.css'));
$cssCache->send();

?>