{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('RequestPayout')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_REQUEST_PAYOUT_TITLE}</span></div>
	<div class="boxcontent">
		{if $ADM_ACCOUNT_REQUEST_PAYOUT_FORBIDDEN}
			{$ADM_ACCOUNT_REQUEST_PAYOUT_FORBIDDEN}<br /><a href="?show=Account">{$BACK}</a>
		{else if $ADM_ACCOUNT_REQUEST_PAYOUT_NO_MAIL}
			{$ADM_ACCOUNT_REQUEST_PAYOUT_NO_MAIL} <a href="?show=Profile">{$ADM_ACCOUNT_TO_PROFILE}</a>
		{else if $ADM_REQUEST_PAYOUT_DONE}
			{$ADM_REQUEST_PAYOUT_DONE}<br /><a href="?show=Account">{$BACK}</a>
		{else}
		<div style="text-align: center;">
			<form action="?show=RequestPayout" method="post">
				<h3>{$ADM_REQUEST_PAYOUT_HOW_MUCH}</h3>
				{$ADM_REQUEST_PAYOUT_AMOUNT} <input type="text" class="right" name="amount" value="{$DEFAULT_VALUE}" style="width:50px;" />€ <br />
				{if $ADM_REQUEST_PAYOUT_ERROR}{$ADM_REQUEST_PAYOUT_ERROR}<br />{/if}
				<input type="submit" value="{$ADM_REQUEST_PAYOUT_REQUEST}" />
			</form>
		</div>
		{/if}
	</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}