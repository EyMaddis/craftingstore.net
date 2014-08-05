{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('Statistics')}
<table class="StatsSelection">
<tr>
	<td class="special">{$ADM_STATS_SELECT_STAT}</td>
	<!--<td{if $ADM_STATS_NAVIGATION_ACTIVE == 'GamerStats'} class="clicked"{/if}>
		<a title="" href="?show=Statistics&amp;c=GamerStats">
			<div>Spieler</div>
		</a>
	</td>-->
	<td{if $ADM_STATS_NAVIGATION_ACTIVE == 'ItemStats'} class="clicked"{/if}>
		<a title="" href="?show=Statistics&amp;c=ItemStats">
			<span>Items</span>
		</a>
	</td>
	<td{if $ADM_STATS_NAVIGATION_ACTIVE == 'LatestSales'} class="clicked"{/if}>
		<a title="{$ADM_LATEST_TITLE}" href="?show=Statistics&amp;c=LatestSales">
			<span>{$ADM_LATEST_TITLE}</span>
		</a>
	</td>
</tr>
</table>{include file=$CONTENT}
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}