{$main->useAjax(false)}{$main->prepare('Login')}<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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
<div class="CenterBox green">
{if $LOGOFF_TITLE}
<div class="h1"><span>{$LOGOFF_TITLE}</span></div>
<div class="Inner"><p>{$LOGOFF}<br /><a href="{$REDIRECT_URL}">{$LOGIN_TRY_REDIRECT}</a></p></div>
{elseif $LOGIN_ACTIVATE_ANOTHER_SHOP}
<div class="h1"><span>{$LOGIN_UNLOCK_SHOP_TITLE}</span></div>
<div class="Inner">
	<p>
		{$LOGIN_ACTIVATE_ANOTHER_SHOP}<br />
		<a href="?show=Login&amp;unlock&amp;shop={$SHOP_ID}" class="button left">{$LOGIN_ACTIVATE_ANOTHER_SHOP_YES}</a>
		<a href="?show=Login&amp;logoff=Login&amp;shop={$SHOP_ID}" title="{$LOGIN_ACTIVATE_ANOTHER_SHOP_CANCEL_DESCRIPTION}" class="button right">{$LOGIN_ACTIVATE_ANOTHER_SHOP_CANCEL}</a>
	</p>
</div>
{elseif $LOGIN_UNLOCK_SHOP_SUCCESS}
<div class="h1"><span>{$LOGIN_UNLOCK_SHOP_TITLE}</span></div>
<div class="Inner"><p>{$LOGIN_UNLOCK_SHOP_SUCCESS}<br /><a href="{$REDIRECT_URL}">{$LOGIN_TRY_REDIRECT}</a></p></div>
{elseif $LOGIN_CORRECT_LABEL}
<div class="h1"><span>{$LOGIN_LOGIN_HEADER}</span></div>
<div class="Inner"><p>{$LOGIN_CORRECT_LABEL}<br /><a href="{$REDIRECT_URL}">{$LOGIN_TRY_REDIRECT}</a></p></div>
{else}
<div class="h1"><span>{$LOGIN_LOGIN_HEADER}</span></div>
<div class="Inner">
	{if $LOGIN_ERROR}<p class="error">{$LOGIN_ERROR}</p>{/if}
	<p>{$LOGIN_SHOP_DESCRIPTION}</p>
	<form action="?show=Login&amp;shop={$SHOP_ID}" method="post">
		<table class="alternating set_center">
			<tr>
				<td style="width:50%">{$LOGIN_USERNAME}:</td>
				<td style="width:50%"><input type="text" name="username" value="{$LOGIN_USERNAME}" onfocus="focused(this,'{$LOGIN_USERNAME}');" onblur="blured(this,'{$LOGIN_USERNAME}');" /></td>
			</tr>
			<tr>
				<td>{$LOGIN_PASSWORD}:</td>
				<td>
					<input type="password" name="password" value="{$LOGIN_PASSWORD}" onfocus="focused(this,'{$LOGIN_PASSWORD}');" onblur="blured(this,'{$LOGIN_PASSWORD}');" /><br />
					<a href="?show=ForgotPassword&amp;shop={$SHOP_ID}">{$LOGIN_FORGOT_PASSWORD}</a>
				</td>
			</tr>
			<tr>
				<td class="right">{if $LOGIN_BACK_LINK}<a href="{$LOGIN_BACK_LINK}" class="button">{$BACK}</a>{/if}</td>
				<td><input type="submit" name="submit" value="{$LOGIN_SUBMIT}" /></td>
			</tr>
		</table>
	</form><br />
	<div class="right" style="text-align:right;">
		<a href="?show=Register&amp;shop={$SHOP_ID}">{$LOGIN_NOT_REGISTERED}</a><br />
		<a href="?show=Register&amp;resendMail&amp;shop={$SHOP_ID}">{$LOGIN_GOT_NO_MAIL}</a><br />
		<a href="?show=Message&amp;back={$THIS_URL}">{$LOGIN_SUBMIT_MESSAGE}</a>
	</div>
	<div><a href="?show=LoginServer">{$LOGIN_SWITCH_TO_ADMIN_LOGIN}</a></div>
</div>
{/if}
</div>
{include file="AgbFooter.tpl"}
		</td>
	</tr>
</table>
</body>
</html>