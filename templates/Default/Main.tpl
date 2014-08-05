{$main->useAjax(true)}{$main->prepare('Main')}<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
	<meta http-equiv="cache-control" content="no-cache" />
	<link rel="shortcut icon" type="image/x-icon" href="templates/{$TEMPLATE}/favicon.ico" />
	<title>{$MAIN_TITLE}</title>
	<!-- Stylesheets der Template-Abschnitte //-->
	<link rel="stylesheet" href="templates/{$TEMPLATE}/css.php" />

	<!-- jquery //-->
	<script type="text/javascript" src="templates/{$TEMPLATE}/js.php"></script>
	<script type="text/javascript">
		var template = "{$TEMPLATE}";
		var status;
		var logged_in = 0;
	</script>
	<style type="text/css">
{$CUSTOMCSS}
	</style>
</head>
<body>
<noscript>
	<div style="background:#fff; position:fixed; left:0px; top:0px; width:100%; height:100%; text-align:center; z-index:1000;"></div>

	<div style="z-index:1001; width: 100%; height: 100%; float: left; position: fixed">
		<div style="z-index:1001; width:300px; margin: 100px auto; background-color: #FFF; border:1px solid #f00; padding:15px; text-align:center;">
		  <h2 style="color:red;">Caution!</h2>
		  <p>This site needs <strong>Javascript</strong> to work properly! </p>
		  <p>Please activate Javascript or you <strong>cannot</strong> use this site!</p>
		  <p><br /></p>
		  <p>If you need help to activate this, visit this link: <a href="http://www.activatejavascript.org/" target="_blank" style="font-size: 1.2em; color: blue; font-weight: bold;">Click me</a>!</p>
		</div>
	</div>
</noscript>

{include file="Toppanel.tpl"}

<div class="wrapper">
	<div class="background">
		<div class="page">
			<div class="header">{include file="Header.tpl"}</div>
			{include file="LMenu.tpl"}
			<div class="main">{include file="Content.tpl"}</div>
			<div class="Right">{include file="Right.tpl"}</div>
			<div class="clear"></div>
			<div class="footer">{include file="Footer.tpl"}</div>
		</div>
	</div>
</div>
{include file="Feedback.tpl"}
<div style="display:hidden" id="somedialog"></div>
</body>
</html>