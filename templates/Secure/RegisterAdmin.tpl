{include file="CenterStructure.tpl"}
{$main->prepare('RegisterAdmin')}
	<div class="h1"><span>{$REGISTER_SERVER_TITLE}</span></div>
	<div class="Inner">
{if $betablocker}The beta has not startet yet. Please come back again in a few days!
{elseif $REGISTER_RESEND}
<p>{$REGISTER_RESEND}</p>
<form action="?show=RegisterAdmin&amp;resendMail" method="post">
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
	<p>{$VERIFICATION_ALREADY_DONE}<br /><a href="?show=LoginServer">{$VERIFICATION_CONFIRMED_TO_LOGIN}</a></p></p>
{elseif $VERIFICATION_CONFIRMED}
	<p>{$VERIFICATION_CONFIRMED}<br /><a href="?show=LoginServer">{$VERIFICATION_CONFIRMED_TO_LOGIN}</a></p>
{elseif $VERIFICATION_ERROR}
	<p>{$VERIFICATION_ERROR}<br /><a href="?show=RegisterAdmin&amp;resendMail">{$VERIFICATION_ERROR_TO_RESEND}</a></p>
{elseif $REGISTER_SERVER_SUCCESSFUL_1}
	<p>{$REGISTER_SERVER_SUCCESSFUL_1}<br />{$REGISTER_SERVER_SUCCESSFUL_2}</p>
{else}
<form action="?show=RegisterAdmin" method="post">
	<table class="center alternating">
		<tr>
			<td{if $REGISTER_SERVER_NAME_ERROR} class="error"{/if}>{$REGISTER_SERVER_FIRST_AND_SUR_NAME}</td>
			<td>
				{if $REGISTER_SERVER_NAME_ERROR}<div class="extraInfo">{$REGISTER_SERVER_NAME_ERROR}</div>{/if}
				<input type="text" name="firstname" value="{$REGISTER_SERVER_FIRSTNAME}" style="width:93px" /><input type="text" name="surname" value="{$REGISTER_SERVER_SURNAME}" style="width:93px" />
			</td>
		</tr>
		<tr>
			<td{if $REGISTER_SERVER_MINECRAFTNAME_ERROR} class="error"{/if}>{$REGISTER_SERVER_MINECRAFTNAME}</td>
			<td>
				{if $REGISTER_SERVER_MINECRAFTNAME_ERROR}<div class="extraInfo">{$REGISTER_SERVER_MINECRAFTNAME_ERROR}</div>{/if}
				<input type="text" name="minecraftname" value="{$REGISTER_SERVER_MINECRAFTNAME_VALUE}" style="width:200px" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="spacer" />
		</tr>
		<tr>
			<td{if $REGISTER_SERVER_MAIL_ERROR} class="error"{/if}>{$REGISTER_SERVER_MAIL}</td>
			<td>
				{if $REGISTER_SERVER_MAIL_ERROR}<div class="extraInfo">{$REGISTER_SERVER_MAIL_ERROR}</div>{/if}
				<input type="text" name="mail" value="{$REGISTER_SERVER_MAIL_VALUE}" style="width:200px" onpaste="return false" />{if $REGISTER_SERVER_MAIL_ERROR_ACCEPT}<input type="hidden" value="{$REGISTER_SERVER_MAIL}" name="mail_accept" />{/if}
			</td>
		</tr>
		<tr>
			<td{if $REGISTER_SERVER_MAIL_REPEAT_ERROR} class="error"{/if}>{$REGISTER_SERVER_MAIL_REPEAT}</td>
			<td>
				{if $REGISTER_SERVER_MAIL_REPEAT_ERROR}<div class="extraInfo">{$REGISTER_SERVER_MAIL_REPEAT_ERROR}</div>{/if}
				<input type="text" name="mail2" value="{$REGISTER_SERVER_MAIL2_VALUE}" style="width:200px" onpaste="return false" /></td>
		</tr>
		<tr>
			<td colspan="2" class="spacer" />
		</tr>
		<tr>
			<td{if $REGISTER_SERVER_PW_ERROR} class="error"{/if}>{$REGISTER_SERVER_PASSWORD}</td>
			<td>
				{if $REGISTER_SERVER_PW_ERROR}<div class="extraInfo">{$REGISTER_SERVER_PW_ERROR}</div>{/if}
				<input type="password" name="pw" style="width:200px" />
			</td>
		</tr>
		<tr>
			<td>{$REGISTER_SERVER_PASSWORD_REPEAT}</td>
			<td><input type="password" name="pw2" style="width:200px" /></td>
		</tr>
		<tr>
			<td colspan="2" class="spacer" />
		</tr>
		<tr>
			<td{if $REGISTER_ACCEPT_LEGAL_ERROR} class="error"{/if}>{$REGISTER_SERVER_LEGAL}</td>
			<td>
				{if $REGISTER_ACCEPT_LEGAL_ERROR}<div class="extraInfo">{$REGISTER_ACCEPT_LEGAL_ERROR}</div>{/if}
				<input type="checkbox" name="legal_accepted" /> {$REGISTER_SERVER_LEGAL_DESCRIPTION}
			</td>
		</tr>
		<tr>
			<td colspan="2" class="spacer" />
		</tr>
		<tr>
			<td{if $REGISTER_SERVER_CAPTCHA_INVALID} class="error"{/if}>{$REGISTER_SERVER_CAPTCHA}</td>
			<td>{include file="Captcha.tpl"}{if $REGISTER_SERVER_CAPTCHA_INVALID}<div class="extraInfo">{$REGISTER_SERVER_CAPTCHA_INVALID}</div>{/if}</td>
		</tr>
		<tr>
			<td><a href="{$REGISTER_SERVER_CANCEL_URL}" class="button">{$REGISTER_SERVER_CANCEL}</a></td>
			<td><input type="submit" value="{$REGISTER_SERVER_REGISTER}" name="submit" /></td>
		</tr>
	</table>
</form>
{/if}
</div>
{include file="CenterStructure.tpl"}