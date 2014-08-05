{include file="CenterStructure.tpl"}
{$main->prepare('ForgotPasswordServer')}
<div class="h1"><span>{$FORGOTPW_SERV_TITLE}</span></div>
<div class="Inner">
	{if $STEP == 1}
	{if $FORGOTPW_SERV_ERROR}<p class="error">{$FORGOTPW_SERV_ERROR}</p>{/if}
	<form action="?show=ForgotPasswordServer" method="post">
		<p>{$FORGOTPW_SERV_INFO}</p>
		<table class="center alternating">
			<tr>
				<td>{$FORGOTPW_EMAIL}</td>
				<td><input type="text" name="email" /></td>
			</tr>
			<tr>
				<td><a href="?show=LoginServer" class="button">{$FORGOTPW_CANCEL}</a></td>
				<td><input type="submit" value="{$FORGOTPW_SEND}" name="submit" /></td>
			</tr>
		</table>
	</form>
	{elseif $STEP == 2}
	<p>{$FORGOTPW_MAIL_SEND}</p>
	<div><a href="?show=LoginServer" class="button">{$FORGOTPW_BACK}</a></div>
	{else}<p>{$FORGOTPW_RESET_DONE}</p>
	<div><a href="?show=LoginServer" class="button">{$FORGOTPW_BACK}</a></div>
	{/if}
</div>
{include file="CenterStructure.tpl"}