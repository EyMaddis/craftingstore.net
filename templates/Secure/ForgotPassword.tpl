{include file="CenterStructure.tpl"}
{$main->prepare('ForgotPassword')}
<div class="h1"><span>{$FORGOTPW_TITLE}</span></div>
<div class="Inner">
{if $FORGOTPW_MAIL_SEND}<p>{$FORGOTPW_MAIL_SEND}</p>
	<div><a href="?show=Login&amp;shop={$SHOP_ID}" class="button">{$FORGOTPW_BACK}</a></div>
{elseif $FORGOTPW_MAIL_ERROR}<p>{$FORGOTPW_MAIL_ERROR}</p>
	<div><a href="?show=Login&amp;shop={$SHOP_ID}" class="button">{$FORGOTPW_BACK}</a></div>
{else}
	{if $FORGOTPW_ERROR}<p class="error">{$FORGOTPW_ERROR}</p>{/if}
	<form action="?show=ForgotPassword&amp;shop={$SHOP_ID}" method="post">
		<p>{$FORGOTPW_INFO}</p>
		<table class="set_center alternating">
			<tr>
				<td>{$FORGOTPW_USER_MAIL}</td>
				<td><input type="text" name="text" /></td>
			</tr>
			<tr>
				<td{if $REGISTER_CAPTCHA_INVALID} class="error"{/if}>{$REGISTER_CAPTCHA}</td>
				<td>{if $REGISTER_CAPTCHA_INVALID}<div class="extraInfo">{$REGISTER_CAPTCHA_INVALID}</div>{/if}
					{include file="Captcha.tpl"}
				</td>
			</tr>
			<tr>
				<td><a href="?show=Login&amp;shop={$SHOP_ID}" class="button">{$FORGOTPW_CANCEL}</a></td>
				<td><input type="submit" value="{$FORGOTPW_SEND}" name="submit" /></td>
			</tr>
		</table>
	</form>
{/if}
</div>
{include file="CenterStructure.tpl"}