{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('Content')}
<table class="StatsSelection">
<tr>
	<td class="special">{$ADM_CONTENT_SELECT_STAT}</td>
	{foreach from=$ADM_CONTENT_MENU_LIST item=item}
	<td{if $ADM_CONTENT_NAVIGATION_ACTIVE == $item.c} class="clicked"{/if}>
		<a title="{$item.Title}" href="{$ADM_CONTENT_DEFAULT_URL}{$item.c}">
			<span>{$item.Title}</span>
		</a>
	</td>
	{/foreach}
</tr>
</table>{include file=$CONTENT}
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}