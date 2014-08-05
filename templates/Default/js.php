<?php

require_once('../../lib/class/multipleFileCompressor.php');

$jsCache = new Caching('js','text/javascript','js/',array('jquery-1.7.2.min.js',
	'jquery-ui-1.8.19.custom.min.js',
	'jquery.address-1.4.min.js',
	'jquery.rotate.2.2.js',
	'jquery.slide.js',
	'jquery.form.js',
	'jquery.tools.min.js',
	'jqueryselectbox.js',
	'custom.js'));
$jsCache->send();

?>