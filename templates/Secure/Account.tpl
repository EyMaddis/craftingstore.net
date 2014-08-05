{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('Account')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_ACCOUNTS_TITLE}</span></div>
	<div class="boxcontent multiboxes">
	<div style="background:yellow; border:dashed 1px red; padding: 15px; font-weight:bold; width: 870px; text-align: center">Not part of the beta!</div>
		<div>
			<div class="h1"><span>{$ADM_ACCOUNTS_OVERVIEW_TITLE}</span></div>
			<div class="boxcontent">
<table class="alternating">
	<tr>
		<td>{$ADM_ACCOUNT_REVENUE_CURRENT_SHOP_LABEL}</td>
		<td>{$ADM_ACCOUNT_REVENUE_CURRENT_SHOP}€</td>
	</tr>
	<tr>
		<td>{$ADM_ACCOUNT_REVENUE_ALL_SHOP_LABEL}</td>
		<td>{$ADM_ACCOUNT_REVENUE_ALL_SHOP}€</td>
	</tr>
	<tr>
		<td>{$ADM_ACCOUNT_ACCOUNT_CURRENT_LABEL}</td>
		<td>{$ADM_ACCOUNT_ACCOUNT_CURRENT}€</td>
	</tr>
</table>
			</div>
		</div>
		<div>
			<div class="h1"><span>{$ADM_ACCOUNTS_PAYOUTS_TITLE}</span></div>
			<div class="boxcontent">
				<div class="subtitle">
				{if $ADM_ACCOUNT_REQUEST_PAYOUT}
					<a href="?show=RequestPayout" class="button"><img src="{$MAIN_URL}/templates/{$TEMPLATE}/images/coins_add.png" title="{$ADM_ACCOUNT_REQUEST_PAYOUT}" style="margin-bottom: -3px;" /> {$ADM_ACCOUNT_REQUEST_PAYOUT}</a>
				{else if $ADM_ACCOUNT_REQUEST_PAYOUT_NO_MAIL}
					{$ADM_ACCOUNT_REQUEST_PAYOUT_NO_MAIL} <a href="?show=ProfileServer">{$ADM_ACCOUNT_TO_PROFILE}</a>
				{else}
					{$ADM_ACCOUNT_REQUEST_PAYOUT_FORBIDDEN}
				{/if}
				</div>
				<table class="alternating">
					<tr>
						<th>Buchungsdatum</th>
						<th>Valuta</th>
						<th>Betrag</th>
						<th>Status</th>
					</tr>
{if $ADM_ACCOUNTS_PAYOUTS_NONE}
					<tr>
						<td colspan="4">{$ADM_ACCOUNTS_PAYOUTS_NONE}</td>
					</tr>
{else}
	{foreach $ADM_ACCOUNTS_PAYOUTS item=item}
					<tr>
						<td>{$item.Transaction}</td>
						<td>{$item.Valuta}</td>
						<td>{$item.Difference} €</td>
						<td><img src="{$MAIN_URL}/templates/{$TEMPLATE}/images/{$item.Icon}" title="{$item.Info}" alt="{$item.Info}" /></td>
					</tr>
	{/foreach}
{/if}
				</table>
			</div>
		</div>
	</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}