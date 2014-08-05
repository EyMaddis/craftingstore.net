{include file="CenterStructure.tpl"}
{$main->prepare('Profile')}
<div class="h1 green"><span>{$PROFILE_TITLE}</span></div>
<div class="Inner green">
{if $PROFILE_DO_DELETESHOP}
	{if $PROFILE_DELETESHOP_CONFIRMATION || $PROFILE_DELETESHOP_CONFIRMATION_ERROR}<div style="text-align:center;">{$PROFILE_DELETESHOP_CONFIRMATION}{$PROFILE_DELETESHOP_CONFIRMATION_ERROR}</div><br />
	<a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_BACK}</a>
	{else}
	<div style="text-align:center;">{$PROFILE_DELETESHOP_LABEL}</div>
	<div style="float:right;"><a href="{$PROFILE_BASE_URL}&amp;deleteshop={$PROFILE_DELETESHOP_ID}&amp;commit" class="button">{$PROFILE_DELETESHOP_CONFIRM}</a></div>
	<div style="float:left;"><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_DELETESHOP_CANCEL}</a></div>
	{/if}
{elseif $PROFILE_CANCEL_CHANGE_EMAIL}
	<table class="alternating">
		<tr>
			<td colspan="2">{$PROFILE_CANCEL_CHANGE_EMAIL}</td>
		</tr>
		<tr>
			<td style="width:50%;"><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_CONTINUE_EMAIL_EDIT}</a></td>
			<td><a href="{$PROFILE_BASE_URL}&amp;{$PROFILE_CANCEL_CHANGE_MAIL_TARGET}&amp;editmail_cancel" class="button">{$PROFILE_CANCEL_EMAIL_EDIT}</a></td>
		</tr>
	</table>
{elseif $PROFILE_EDITMAIL_DONE}
	{$PROFILE_EDITMAIL_DONE}<br /><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_BACK}</a>
{elseif $PROFILE_EDITMAIL_COMMIT}
	<form action="{$PROFILE_BASE_URL}&amp;editmail&amp;k={$PROFILE_EDITMAIL_KEY}" method="post">
	{$PROFILE_EDITMAIL_COMMIT}
	{if $PROFILE_EDITMAIL_ERROR}<div class="error" style="text-align:center">{$PROFILE_EDITMAIL_ERROR}</div>{/if}
		<table class="alternating set_center">
			<tr>
				<td>{$PROFILE_PASSWORD}</td>
				<td><input type="password" name="password" value=""/></td>
			</tr>
			<tr>
				<td><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_BACK}</a></td>
				<td><input type="submit" value="{$PROFILE_EDITMAIL_SAVE}"/></td>
			</tr>
		</table>
	</form>
{elseif $PROFILE_EDITMAIL_SEND}
	{$PROFILE_EDITMAIL_SEND}<br /><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_BACK}</a>
{elseif $PROFILE_EDITMAIL}
	<form action="{$PROFILE_BASE_URL}&amp;editmail" method="post"><input type="hidden" name="lastmail" value="{$PROFILE_LASTMAIL}" />
	{$PROFILE_EDITMAIL}
	{if $PROFILE_EDITMAIL_ERROR}<div class="error" style="text-align:center">{$PROFILE_EDITMAIL_ERROR}</div>{/if}
		<table class="alternating set_center">
			<tr>
				<td>{$PROFILE_MAIL}</td>
				<td><input type="text" name="mail" value=""/></td>
			</tr>
			<tr>
				<td>{$PROFILE_MAIL_REPEAT}</td>
				<td><input type="text" name="mailrep" value=""/></td>
			</tr>
			<tr>
				<td><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_BACK}</a></td>
				<td><input type="submit" value="{$PROFILE_EDITMAIL_SAVE}"/></td>
			</tr>
		</table>
	</form>
{elseif $PROFILE_EDIT_DONE}
	{$PROFILE_CHANGED}{if $PROFILE_CHANGED_MAIL}<br />{$PROFILE_CHANGED_MAIL}{/if}
	<br /><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_BACK}</a>
{elseif $PROFILE_CHANGE_PW}
	<form action="{$PROFILE_BASE_URL}&amp;editpw" method="post">{if $PROFILE_EDIT_PW_ERROR}<div class="error" style="text-align:center">{$PROFILE_EDIT_PW_ERROR}</div>{/if}
		<table class="alternating">
			<tr>
				<td>{$PROFILE_CURRENT_PASSWORD}</td>
				<td><input type="password" name="oldpw" value=""/></td>
			</tr>
			<tr>
				<td>{$PROFILE_NEW_PASSWORD}</td>
				<td><input type="password" name="newpw" value=""/></td>
			</tr>
			<tr>
				<td>{$PROFILE_NEW_PASSWORD_CONFIRM}</td>
				<td><input type="password" name="pwconfirm" value=""/></td>
			</tr>
			<tr>
				<td><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_BACK}</a></td>
				<td><input type="submit" value="{$PROFILE_EDIT_SAVE}"/></td>
			</tr>
		</table>
	</form>
{elseif $PROFILE_CHANGE_PW_DONE}
	{$PROFILE_PASSWORD_CHANGED}<br /><a href="{$PROFILE_BASE_URL}" class="button">{$PROFILE_BACK}</a>
{else}
	<table class="alternating set_center">
		<tr>
			<td>{$PROFILE_NICKNAME}</td>
			<td>{$PROFILE_NICKNAME_VALUE}</td>
		</tr>
		<tr>
			<td>{$PROFILE_MINECRAFTNAME}</td>
			<td>{$PROFILE_MINECRAFTNAME_VALUE}</td>
		</tr>
		<tr>
			<td>{$PROFILE_MAIL}</td>
			<td><a href="{$PROFILE_BASE_URL}&amp;editmail"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/email_edit.png" title="{$PROFILE_MAIL_CHANGE}" alt="{$PROFILE_MAIL_CHANGE}" /></a> {$PROFILE_MAIL_VALUE}{if $PROFILE_NEW_MAIL_VALUE} ({$PROFILE_NEW_MAIL_VALUE}){/if}</td>
		</tr>
		<tr>
			<td>{$PROFILE_PASSWORD}</td>
			<td><a href="{$PROFILE_BASE_URL}&amp;editpw"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/lock_edit.png" title="{$PROFILE_EDIT_PASSWORD}" alt="{$PROFILE_EDIT_PASSWORD}" /></a> ●●●●●●●●</td>
		</tr>
		<tr>
			<td>{$PROFILE_CURRENT}</td>
			<td><a href="{$PROFILE_BUYPOINTS_URL}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/coins_add.png" title="{$PROFILE_INCREASE_ACCOUNT_DESCRIPTION}" alt="{$PROFILE_INCREASE_ACCOUNT}" /></a> {$PROFILE_CURRENT_DESCRIPTION}</td>
		</tr>
		<tr>
			<td>{$PROFILE_ACTIVATED_SHOPS}</td>
			<td>{if $PROFILE_NO_ACTIVATED_SHOPS}{$PROFILE_NO_ACTIVATED_SHOPS}<br /><a href="http://craftingstore.net" target="_blank">{$PROFILE_NO_ACTIVATED_SHOPS_MORE_INFO}</a>{else}
				{foreach $PROFILE_ACTIVATED_SHOPS_VALUE item=item}
				<a href="{$PROFILE_BASE_URL}&amp;deleteshop={$item.Id}"><img alt="{$PROFILE_LEAVE_SHOP}" src="{$MAIN_URL}templates/{$TEMPLATE}/images/cross.png"/></a> {$item.Label} (<a href="?shop={$item.Id}&amp;red" target="_blank">→ {$item.Url}</a>)<br />
				{/foreach}{/if}
			</td>
		</tr>
	</table>
	{if $PROFILE_BACK_URL}<a href="{$PROFILE_BACK_URL}" class="button">{$PROFILE_BACK_TO_SHOP}</a>
	{else}<a href="{$PROFILE_LOGOFF_URL}" class="button">{$PROFILE_LOGOFF}</a>{/if}
{/if}
</div>
{include file="CenterStructure.tpl"}