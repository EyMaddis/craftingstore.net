{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('RegisteredPlayers')}
<div class="AdminBox">
	<div class="h1">{include file="Pagination.tpl"}<span>{$ADM_PLAYERS_BOX_TITLE}</span></div>
	<div class="boxcontent">
		<table border="0" class="alternating">
			<tr>
				{*<th>{$ADM_PLAYERS_ID_LABEL}</th>*}
				<th>{$ADM_PLAYERS_NICKNAME_LABEL}</th>
				<th>{$ADM_PLAYERS_MINECRAFTNAME_LABEL}</th>
				<th>{$ADM_PLAYERS_REVENUE_LABEL}</th>
				<th>{$ADM_PLAYERS_BONUS_LABEL}</th>
				<!--<th>{$ADM_PLAYERS_EMAIL_LABEL}</th>-->
			</tr>
			{foreach from=$ADM_PLAYERS_PLAYERS item=player}
			<tr>
				{*<td>{$player.Id}</td>*}
				<td>{$player.Nickname}</td>
				<td>{$player.MinecraftName}</td>
				<td class="transfercomplete">{$player.Revenue}</td>
				<td>{$player.Bonus} <a href="?show=ExclusiveShopPoints&amp;g={$player.Id}" title="{$ADM_PLAYERS_GIVEBONUS}"><img src="templates/{$TEMPLATE}/images/coins_add.png" alt="coins_add" width="16" height="16" style="top:50%; margin-top:-8px; right: 5px" /></a></td>
				<!-- NOT WORKING IN BETA <td>
					<div class="PlayerContactButton">
						<a href="{$player.email_url}" class="AjaxButton">
							<span>{$ADM_PLAYERS_EMAIL}</span>
						</a>
					</div>
				</td> -->
			</tr>
			{/foreach}
		</table>
	</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}