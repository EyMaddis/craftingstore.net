{$main->useAjax(false)}{$main->prepare('Buypoints')}<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
<head>
	<title>{$BUYPOINTS_TITLE}</title>
	<link href="{$SECURE_URL}/templates/Secure/css/style.css" rel="stylesheet" type="text/css" />
{literal}
	<style type="text/css">
		input[type=radio]{
			display: none;
		}
		.hideButton{
			display:none;
		}
		.Inner{
			text-align:center;
		}
		.containter {
			height:220px;
			width:750px;
			margin:auto;
			overflow:hidden;
		}
		.containter > div {
			margin:auto;
			position:relative;
			padding:10px;
		}
		.containter > div > label > div > div {
			padding:20px;
		}

		div.smallContainer {
			width:730px;
			height:130px;
			top:35px;
		}
		div.mediumContainer {
			width:500px;
			height:160px;
			top:-130px;
		}
		div.largeContainer {
			width:200px;
			height:200px;
			top:-330px;
		}

		.CenterBox .Inner {
			width:auto;
		}

		.smallContainer > label > div {
			width:130px;
			height:130px;
			border-radius:12px;
			font-size:8pt;
		}
		.mediumContainer > label > div {
			width:160px;
			height:160px;
			border-radius:16px;
			font-size:8pt;
		}
		.largeContainer > label > div {
			width:200px;
			height:200px;
			border-radius:20px;
			font-size:8pt;
		}

		.coal {
			box-shadow:0px 0px 3px 3px #545454;
			background-color:#545454;
			color:#fff;
		}
		.iron {
			box-shadow:0px 0px 3px 3px #E6D4C8;
			background-color:#E6D4C8;
		}
		.gold {
			box-shadow:0px 0px 3px 3px #FDF491;
			background-color:#FDF491;
		}
		.diamant {
			box-shadow:0px 0px 3px 3px #5ADCC5;
			background-color:#5ADCC5;
		}
		.emerald {
			box-shadow:0px 0px 3px 3px #7FE79C;
			background-color:#7FE79C;
		}

		label.active > div {
			box-shadow: 0px 0px 3px 3px #000;
		}
		label.active .coal {
			background-color:#363636;
			color:#fff;
		}

		label.active .iron {
			background-color:#D3B49F;
		}
		label.active .gold {
			background-color:#FBEF56;
		}
		label.active .diamant {
			background-color:#2CCDB1;
		}
		label.active .emerald {
			background-color:#4CDD76;
		}
	</style>

	<script type="text/javascript">

	var lastActive = null;

	function setClass(element, newClass){
		document.getElementById('buyButton').removeAttribute("class");
		document.getElementById('dummyButton').setAttribute("class", "hideButton");
		if(lastActive != null){
			lastActive.setAttribute("class", "");
		}
		element.setAttribute("class", newClass);
		lastActive = element;
	}

	window.onload = function(e){
		document.getElementById('buyButton').setAttribute("class", "hideButton");
		document.getElementById('dummyButton').setAttribute("class", "button disabled");
	}
	</script>
{/literal}
</head>
<body>{include file="Feedback.tpl"}
<table class="CenterTable">
	<tr>
		<td>
<div style="width:800px; margin:auto;" class="CenterBox">

<div class="h1"><span>{$SECURE_BUYPOINTS_TITLE}</span></div>

<div class="Inner">

<form name="_xclick" action="{$BUYPOINTS_FORM_ACTION}" method="post">
<div>{$BUYPOINTS_INFO}</div>
<div class="containter">
	<div class="smallContainer">
		<label for="paket{$BUYPOINTS_PACKAGES.0.id}" onclick="setClass(this, 'active')">
			<div class="left coal">
				<div>
					<input id="paket{$BUYPOINTS_PACKAGES.0.id}" type="radio" name="amount" value="{$BUYPOINTS_PACKAGES.0.money}" />
					{$BUYPOINTS_PACKAGES.0.name}<br>
					{$BUYPOINTS_PACKAGES.0.price} {$POINTSYSTEM}<br>
					{$BUYPOINTS_PACKAGES.0.money_display} {$CURRENCY_SYMBOL}<br>
					<img src="{$MAIN_URL}templates/{$TEMPLATE}/images/buypoints/{$BUYPOINTS_PACKAGES.0.image}" />
				</div>
			</div>
		</label>
		<label for="paket{$BUYPOINTS_PACKAGES.4.id}" onclick="setClass(this, 'active')">
			<div class="right emerald">
				<div>
					<input id="paket{$BUYPOINTS_PACKAGES.4.id}" type="radio" name="amount" value="{$BUYPOINTS_PACKAGES.4.money}" />
					{$BUYPOINTS_PACKAGES.4.name}<br>
					{$BUYPOINTS_PACKAGES.4.price} {$POINTSYSTEM}<br>
					{$BUYPOINTS_PACKAGES.4.money_display} {$CURRENCY_SYMBOL}<br>
					<img src="{$MAIN_URL}templates/{$TEMPLATE}/images/buypoints/{$BUYPOINTS_PACKAGES.4.image}" />
				</div>
			</div>
		</label>
	</div>
	<div class="mediumContainer">
		<label for="paket{$BUYPOINTS_PACKAGES.1.id}" onclick="setClass(this, 'active')">
			<div class="left iron">
				<div>
					<input id="paket{$BUYPOINTS_PACKAGES.1.id}" type="radio" name="amount" value="{$BUYPOINTS_PACKAGES.1.money}" />
					{$BUYPOINTS_PACKAGES.1.name}<br>
					{$BUYPOINTS_PACKAGES.1.price} {$POINTSYSTEM}<br>
					{$BUYPOINTS_PACKAGES.1.money_display} {$CURRENCY_SYMBOL}<br>
					<img src="{$MAIN_URL}templates/{$TEMPLATE}/images/buypoints/{$BUYPOINTS_PACKAGES.1.image}" />
				</div>
			</div>
		</label>
		<label for="paket{$BUYPOINTS_PACKAGES.3.id}" onclick="setClass(this, 'active')">
			<div class="right diamant">
				<div>
					<input id="paket{$BUYPOINTS_PACKAGES.3.id}" type="radio" name="amount" value="{$BUYPOINTS_PACKAGES.3.money}" />
					{$BUYPOINTS_PACKAGES.3.name}<br>
					{$BUYPOINTS_PACKAGES.3.price} {$POINTSYSTEM}<br>
					{$BUYPOINTS_PACKAGES.3.money_display} {$CURRENCY_SYMBOL}<br>
					<img src="{$MAIN_URL}templates/{$TEMPLATE}/images/buypoints/{$BUYPOINTS_PACKAGES.3.image}" />
				</div>
			</div>
		</label>
	</div>
	<div class="largeContainer">
		<label for="paket{$BUYPOINTS_PACKAGES.2.id}" onclick="setClass(this, 'active')">
			<div class="gold">
				<div>
					<input id="paket{$BUYPOINTS_PACKAGES.2.id}" type="radio" name="amount" value="{$BUYPOINTS_PACKAGES.2.money}" />
					{$BUYPOINTS_PACKAGES.2.name}<br>
					{$BUYPOINTS_PACKAGES.2.price} {$POINTSYSTEM}<br>
					{$BUYPOINTS_PACKAGES.2.money_display} {$CURRENCY_SYMBOL}<br>
					<img src="{$MAIN_URL}templates/{$TEMPLATE}/images/buypoints/{$BUYPOINTS_PACKAGES.2.image}" />
				</div>
			</div>
		</label>
	</div>
</div>

<div class="footer">
	<table style="width:90%; margin:auto;">
		<tr>
			<td><a href="{$BUYPOINTS_RETURNURL}" title="{$BACK}" class="button" style="float:left">{$BACK}</a></td>
			<td style="text-align:right">
				<input type="submit" name="submit" value="{$BUYPOINTS_FORM_SUBMIT}" id="buyButton">
				<input type="button" value="{$BUYPOINTS_FORM_SUBMIT_WAIT}" id="dummyButton" class="button disabled hideButton">
			</td>
		</tr>
	</table>
</div>

<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="{$BUYPOINTS_SELLER}">
<input type="hidden" name="currency_code" value="EUR">
<input type="hidden" name="custom" value="{$GAMER_ID}">
<input type="hidden" name="return" value="{$BUYPOINTS_RETURNURL}">
<input type="hidden" name="notify_url" value="{$BUYPOINTS_NOTIFYURL}">
</form>

</div>

</div>
		</td>
	</tr>
</table>
</body>
</html>