{if !$AdminStructurePrepared}{$main->useAjax(false)}{$main->prepare('Structure')}<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
<head>
	{include file="header.tpl"}
	<link href="{$MAIN_URL}templates/{$TEMPLATE}/css/codemirror.css" rel="stylesheet" type="text/css" />
    <script src="/templates/{$TEMPLATE}/js/codemirror.js"></script>
    <script src="/templates/{$TEMPLATE}/codemirror/addon/search/searchcursor.js" type="text/javascript"></script>
    <script src="/templates/{$TEMPLATE}/codemirror/addon/search/search.js" type="text/javascript"></script>
    <script src="/templates/{$TEMPLATE}/codemirror/addon/search/match-highlighter.js" type="text/javascript"></script>
    <script src="/templates/{$TEMPLATE}/codemirror/addon/edit/matchbrackets.js" type="text/javascript"></script>
    <script src="/templates/{$TEMPLATE}/codemirror/mode/css/css.js" type="text/javascript"></script>
<script type="text/javascript">{literal}
<!--
function showShopButtons(){
	document.getElementById("submit_button").className = 'reset_display';
	document.getElementById("reset_button").className = 'reset_display';
}
function hideShopButtons(){
	document.getElementById("submit_button").className = 'reset_hide';
	document.getElementById("reset_button").className = 'reset_hide';
}
function toggleProductEditArea(element){
	var inputField = element.parentNode.firstElementChild;
	var div = element.nextElementSibling;

	if(div.style.display == 'none'){
		div.style.display = 'block';
		inputField.value = '';
		element.parentNode.className = 'active';
	}
	else{
		div.style.display = 'none';
		inputField.value = element.id;
		element.parentNode.className = '';
	}
}
function showHidelifetimeSelection(blockAnzahl){
	var targetDisplay = document.getElementById('lifetimeActivator').checked ? '' : 'none';
	for(var i=0; i<blockAnzahl;i++)
	{
		document.getElementById('lifetime' + i).style.display = targetDisplay;
	}
}

window.onload = function(e){
	if(document.getElementById('submit_button') && document.getElementById('submit_button'))
		hideShopButtons();
	if(document.getElementById('productConfig')){

		var closeAreas = document.productEdit.closeAreas.value.split(',');
		for(i=0; i<closeAreas.length; i++){
			toggleProductEditArea(document.getElementById(closeAreas[i]));
		}

		if(document.getElementById('lifetimeActivator'))
			showHidelifetimeSelection(4);
	}


	var cssEditor = document.getElementById('css-editor');

	/*var uiOptions = { path : 'templates/{/literal}{$TEMPLATE}{literal}/codemirror/', searchMode: 'popup', imagePath: 'templates/{/literal}{$TEMPLATE}{literal}/images/' }
	var codeMirrorOptions = {
		mode: "css", // all your normal CodeMirror options go here
		lineNumbers: true,
		theme: 'solarized light'
	}
	var editor = new CodeMirrorUI(cssEditor,uiOptions,codeMirrorOptions);*/
	if(cssEditor){
		var myCodeMirror = CodeMirror.fromTextArea(cssEditor, {
			content: cssEditor.value,
			autoMatchParens: true,
			width: '100%',
			height: '100%',
			textWrapping: false,
			lineNumbers: true,
			tabMode: 'spaces',
			iframeClass: 'ifc',
			indentUnit: 4
		});
	}
}
//-->
{/literal}</script>
</head>
<body>
{include file="Feedback.tpl"}
{else}
</body>
</html>{/if}