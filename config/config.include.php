<?php
/************************
**
** 	File:	 	config.include.php
**	Author: 	Mathis Neumann
**	Date:		30.04.2012
**	Desc:		include of the main configuration
**
*************************/
// some information are change due to security concerns, changed for github
defined('_MCSHOP') or die("Security block!");
define('VERSION', "0.7.5"); //release.state.build


define('SESSION_TIMEOUT', 3600);
define('SESSION_ID_TIMEOUT', 1800);

define('BASE_DOMAIN', 'craftingstore.net');
define('PROJECTNAME','Craftingstore.net');
define('COMPANY','Mathis Neumann Webentwicklung');
define('POWERED_BY','powered by CraftingStore.net');

define('IMPRINT_URL','http://'.BASE_DOMAIN.'/imprint/');
define('TOS_URL','http://'.BASE_DOMAIN.'/imprint/');
define('LEGAL_URL','http://craftingstore.net/legal');


define('DOMAIN', $_SERVER['HTTP_HOST']);
define('DEFAULT_PROTOCOL', "http://");
define('SECURE_URL', "https://secure.".BASE_DOMAIN);
define('DEFAULT_LANGUAGE_TAG', "en");
define('DOC_ROOT', '/var/www/vhosts/minecraftshop.net/httpdocs/live/');

define('NEWS_RSS','http://craftingstore.net/feed/');

define('CURRENCY', "Euro");
define('CURRENCY_SHORT', "€");
define('POINTS_PER_EURO', 10);



define("SQL_HOST", "localhost");
define("SQL_USERNAME", "XXXXXXXXXXXXXXXXX");
define("SQL_PASSWORD", "XXXXXXXXXXXXXXXXX");
define("SQL_DB", "XXXXXXXXXXXXXXXXXXX");


define("_MC_TEMPLATE", 'Default');
define("_MC_ADMIN_TEMPLATE", 'Secure');

define('SMARTY_DIR', dirname (__FILE__).'/../lib/class/Smarty/');

//define a maximum size for the uploaded images in Kb
define ("MAX_LOGO_SIZE","150");


//Paypal
define('PAYPAL_SELLEREMAIL', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('PAYPAL_NOTICEEMAIL', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('PAYPAL_RETURN_IPN', 'https://secure.'.BASE_DOMAIN.'/lib/Payment/Paypal/ipn.php');
define('PAYPAL_FORM_ACTION', 'https://www.sandbox.paypal.com/cgi-bin/webscr');


define('DEFAULT_IMG_DIR', './images/items/');
define('IMAGE_DIR', './images/');
define('ITEM_IMAGE_DIR', './images/items/');
define('ITEM_PREV_IMAGE_DIR', './images/items/preview/');

define('POWEREDBY','<div style="position:absolute; bottom:0px; z-index:1000000; background-color:#fff; right:0; margin: 10px;">Powered by <a href="http://'.BASE_DOMAIN.'.net">Craftingstore.net</a></div>');

define('MAX_PRODUCT_IMAGE_FILE_SIZE', 51200); //50kB
define('MAX_PRODUCT_IMAGE_HEIGHT', 128); //px
define('MAX_PRODUCT_IMAGE_WIDTH', 128); //px

define('CAPTCHA_PUBLIC_KEY','XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('CAPTCHA_PRIVATE_KEY','XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

define('BUG_REPORT_TARGET','http://bugs.craftingstore.net');

define('SERVER_MAX_TRANSFER_FAILURES',4);

?>
