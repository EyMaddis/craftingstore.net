{$main->useAjax(false)}{$main->prepare('LoginServer')}<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
<head>
	{include file="header.tpl"}
	{if $REDIRECT_URL && $REDIRECT_TIME}<meta http-equiv="refresh" content="{$REDIRECT_TIME}; url={$REDIRECT_URL}">{/if}
	{literal}<script type="text/javascript">
	function focused(element, text){
		if(element.value == text)
		{
			element.value = '';
		}
	}
	function blured(element, text){
		if(element.value == '')
		{
			element.value = text;
		}
	}
	</script>{/literal}
</head>
<body>
{include file="Feedback.tpl"}
{include file="LangList.tpl"}
<table class="CenterTable">
	<tr>
		<td>
<div class="CenterBox">
{if $LOGOFF_SUCCESS}
	<div class="h1"><span>{$ADM_LOGIN_TITLE}</span></div>
	<div class="Inner"><p>{$LOGOFF_SUCCESS}<br /><a href="{$REDIRECT_URL}">{$LOGIN_TRY_REDIRECT}</a></p></div>
{else}
<div class="h1"><span>{$ADM_LOGIN_TITLE}</span></div>
<div class="Inner">{if $LOGIN_MESSAGE_LABEL}<p class="error">{$LOGIN_MESSAGE_LABEL}</p>{/if}
	<p>{$ADM_LOGIN_DESCRIPTION}</p>
	<form action="?show=LoginServer" method="post">
		<table class="alternating set_center">
			<tr>
				<td style="width:50%">{$ADM_LOGIN_EMAIL}:</td>
				<td style="width:50%"><input type="text" name="aEmail" tabindex="1" value="{$ADM_LOGIN_EMAIL}" onfocus="focused(this,'{$ADM_LOGIN_EMAIL}');" onblur="blured(this,'{$ADM_LOGIN_EMAIL}');" /></td>
			</tr>
			<tr>
				<td>{$ADM_LOGIN_PASSWORD}:</td>
				<td>
					<input type="password" name="aPw" tabindex="2" value="{$ADM_LOGIN_PASSWORD}" onfocus="focused(this,'{$ADM_LOGIN_PASSWORD}');" onblur="blured(this,'{$ADM_LOGIN_PASSWORD}');" /><br />
					<a href="?show=ForgotPasswordServer" tabindex="4">{$ADM_LOGIN_FORGOT_PASSWORD}</a>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" id="LoginSubmit" tabindex="3" value="{$ADM_LOGIN_NOW}" /></td>
			</tr>
		</table>
	</form><br />
	<div class="right" style="text-align:right;">
		<a href="?show=RegisterAdmin">{$ADM_LOGIN_OPEN_CUSTOM_SHOP}</a><br />
		<a href="?show=RegisterAdmin&amp;resendMail">{$ADM_LOGIN_GOT_NO_MAIL}</a><br />
	</div>
	<div><a href="?show=Login">{$ADM_LOGIN_SWITCH_TO_USER_LOGIN}</a></div>
</div>
{/if}
</div>
{include file="AgbFooter.tpl"}
		</td>
	</tr>
</table>
</body>
</html>