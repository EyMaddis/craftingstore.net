{include file="CenterStructure.tpl"}
{$main->prepare('Register')}
<div class="h1 green"><span>{$REGISTER_TITLE}</span></div>
<div class="Inner green">
{if $REGISTER_NOT_COMPLETED}
<p>{$REGISTER_NOT_COMPLETED}</p>
<p><a href="{$REGISTER_NOT_COMPLETED_TO_RESEND_URL}">{$REGISTER_NOT_COMPLETED_TO_RESEND}</a></p>
{elseif $REGISTER_RESEND}
<p>{$REGISTER_RESEND}</p>
<form action="?show=Register&amp;resendMail&amp;shop={$SHOP_ID}" method="post">
	{if $RESEND_MAIL_ERROR}<p>{$RESEND_MAIL_ERROR}</p>{/if}
	<table class="set_center alternating">
		<tr>
			<td{if $RESEND_MAIL_MAIL_WRONG} class="error"{/if}>{$RESEND_MAIL}</td>
			<td>{if $RESEND_MAIL_MAIL_WRONG}<div class="extraInfo">{$RESEND_MAIL_MAIL_WRONG}</div>{/if}<input type="text" name="mail" /></td>
		</tr>
		<tr>
			<td{if $RESEND_MAIL_PW_WRONG} class="error"{/if}>{$REGISTER_PASSWORD}</td>
			<td>{if $RESEND_MAIL_PW_WRONG}<div class="extraInfo">{$RESEND_MAIL_PW_WRONG}</div>{/if}<input type="password" name="pw" /></td>
		</tr>
		<tr>
			<td{if $REGISTER_CAPTCHA_INVALID} class="error"{/if}>{$REGISTER_CAPTCHA}</td>
			<td>{if $REGISTER_CAPTCHA_INVALID}<div class="extraInfo">{$REGISTER_CAPTCHA_INVALID}</div>{/if}{include file="Captcha.tpl"}
			</td>
		</tr>
		<tr>
			<td><a href="{$RESEND_MAIL_CANCEL_URL}" class="button">{$RESEND_MAIL_CANCEL}</a></td>
			<td><input type="submit" value="{$RESEND_MAIL_DO}" name="submit" /></td>
		</tr>
	</table>
</form>
{elseif $REGISTER_RESEND_DONE}
<p>{$REGISTER_RESEND_DONE}</p><br />
<a href="{$RESEND_MAIL_BACK_URL}" class="button">{$RESEND_MAIL_BACK}</a>
{elseif $VERIFICATION_ALREADY_DONE}
<p>{$VERIFICATION_ALREADY_DONE}<br /><a href="?show=Login&amp;shop={$SHOP_ID}">{$VERIFICATION_CONFIRMED_TO_LOGIN}</a></p></p>
{elseif $VERIFICATION_CONFIRMED}
<p>{$VERIFICATION_CONFIRMED}<br /><a href="?show=Login&amp;shop={$SHOP_ID}">{$VERIFICATION_CONFIRMED_TO_LOGIN}</a></p>
{elseif $VERIFICATION_ERROR}
<p>{$VERIFICATION_ERROR}<br /><a href="?show=Register&amp;resendMail&amp;shop={$SHOP_ID}">{$VERIFICATION_ERROR_TO_RESEND}</a></p>
{else if $REGISTER_SUCCESSFUL_1}
<p>{$REGISTER_SUCCESSFUL_1}<br />{$REGISTER_SUCCESSFUL_2}</p>
{else}
<form action="?show=Register&amp;shop={$SHOP_ID}" method="post">
	<table class="set_center alternating">
		<tr>
			<td{if $REGISTER_NAME_ERROR} class="error"{/if}>{$REGISTER_NICKNAME}</td>
			<td><input type="text" name="nickname" value="{$REGISTER_NICKNAME_VALUE}" />{if $REGISTER_NAME_ERROR}<div class="extraInfo">{$REGISTER_NAME_ERROR}</div>{/if}</td>
		</tr>
		<tr>
			<td{if $REGISTER_MINECRAFTNAME_ERROR} class="error"{/if}>{$REGISTER_MINECRAFTNAME}</td>
			<td><input type="text" name="minecraftname" value="{$REGISTER_MINECRAFTNAME_VALUE}" />{if $REGISTER_MINECRAFTNAME_ERROR}<div class="extraInfo">{$REGISTER_MINECRAFTNAME_ERROR}</div>{/if}</td>
		</tr>
		<tr>
			<td colspan="{if $REGISTER_ERROR}3{else}2{/if}" class="spacer" />
		</tr>
		<tr>
			<td{if $REGISTER_MAIL_ERROR_1} class="error"{/if}>{$REGISTER_MAIL}</td>
			<td><input type="text" name="mail" value="{$REGISTER_MAIL_VALUE}" onpaste="return false" />{if $REGISTER_MAIL_ERROR_1}<div class="extraInfo">{$REGISTER_MAIL_ERROR_1}</div>{/if}{if $REGISTER_MAIL_ERROR_ACCEPT}<input type="hidden" value="{$REGISTER_MAIL_VALUE}" name="mail_accept" />{/if}</td>
		</tr>
		<tr>
			<td{if $REGISTER_MAIL_ERROR_2} class="error"{/if}>{$REGISTER_MAIL_REPEAT}</td>
			<td><input type="text" name="mail2" value="{$REGISTER_MAIL_REPEAT_VALUE}" onpaste="return false" />{if $REGISTER_MAIL_ERROR_2}<div class="extraInfo">{$REGISTER_MAIL_ERROR_2}</div>{/if}</td>
		</tr>
		<tr>
			<td colspan="{if $REGISTER_ERROR}3{else}2{/if}" class="spacer" />
		</tr>
		<tr>
			<td{if $REGISTER_PW_INVALID} class="error"{/if}>{$REGISTER_PASSWORD}</td>
			<td><input type="password" name="pw" />{if $REGISTER_PW_INVALID}<div class="extraInfo">{$REGISTER_PW_INVALID}</div>{/if}</td>
		</tr>
		<tr>
			<td{if $REGISTER_PWS_NOT_EQUAL} class="error"{/if}>{$REGISTER_PASSWORD_REPEAT}</td>
			<td><input type="password" name="pw2" />{if $REGISTER_PWS_NOT_EQUAL}<div class="extraInfo">{$REGISTER_PWS_NOT_EQUAL}</div>{/if}</td>
		</tr>
		<tr>
			<td{if $REGISTER_CAPTCHA_INVALID} class="error"{/if}>{$REGISTER_CAPTCHA}</td>
			<td>{include file="Captcha.tpl"}{if $REGISTER_CAPTCHA_INVALID}<div class="extraInfo">{$REGISTER_CAPTCHA_INVALID}</div>{/if}
			</td>
		</tr>
		<tr>
			<td colspan="{if $REGISTER_ERROR}3{else}2{/if}" class="spacer" />
		</tr>
		<tr>
			<td><a class="button" href="?show=Login&amp;shop={$SHOP_ID}">{$REGISTER_CANCEL}</a></td>
			<td><input type="submit" value="{$REGISTER_DO}" name="submit" /></td>
		</tr>
	</table>
</form>
{/if}
</div>
{include file="CenterStructure.tpl"}