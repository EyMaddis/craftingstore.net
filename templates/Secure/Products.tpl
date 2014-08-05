{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('Products')}
<div class="AdminBox">
	<div class="h1">{include file="Pagination.tpl"}<span>{$ADM_PRODUCTS_TITLE}</span></div>
	<div class="boxcontent">
		<div>
			<a style="float:right;" href="?show=ProductEdit&amp;item=0" class="button">{$ADM_PRODUCT_ADD_TITLE}</a>
			<form action="?show=Products&amp;page={$ADM_PAGE}" method="post">
				<div>
					<input type="text" name="search" value="{$LAST_SEARCH}" />
					<img src="{$MAIN_URL}templates/{$TEMPLATE}/images/info.png" title="{$ADM_PRODUCTS_SEARCH_INFO}" width="32px" height="32px" alt="Info" class="help" />
					<input type="submit" name="search_button" value="{$ADM_PRODUCTS_SEARCH_SUBMIT}" />
				</div>
			</form>
		</div>
		<form action="?show=Products&amp;page={$ADM_PAGE}{$ADM_SEARCH_URL}" method="post">
			<div style="text-align:right">
				{if $ADM_PRODUCTS_SHOW_ENABLE_ALL}<input type="submit" name="enable_all_items" value="{$ADM_PRODUCTS_ENABLE_ALL_SHORT}" title="{$ADM_PRODUCTS_ENABLE_ALL}" />{/if}
				{if $ADM_PRODUCTS_SHOW_DISABLE_ALL}<input type="submit" name="disable_all_items" value="{$ADM_PRODUCTS_DISABLE_ALL_SHORT}" title="{$ADM_PRODUCTS_DISABLE_ALL}" />{/if}
				<input type="submit" name="submit" value="{$ADM_PRODUCTS_SUBMIT}" />
			</div>
			<table border="0" class="alternating">
				<tr class="header">
					<!--<th class="first"></th>-->
					<th>{$ADM_PRODUCTS_IMAGE}</th>
					<th>{$ADM_PRODUCTS_LABEL}</th>
					<th>{$ADM_PRODUCTS_DESCRIPTION}</th>
					<th>{$ADM_PRODUCTS_GROUP}</th>
					<th>{$ADM_PRODUCTS_POINTS}</th>
					<th>{$ADM_PRODUCTS_ENABLED}</th>
					<th>{$ADM_PRODUCTS_EDIT}</th>
				</tr>
				{if $ADM_PRODUCTS_LIST_EMPTY}
				<tr>
					<td colspan="7">{$ADM_PRODUCTS_LIST_EMPTY}</td>
				</tr>
				{else}
				{foreach from=$ADM_PRODUCTS_LIST item=item}
				<tr>
					<!--<td class="first"><input type="checkbox" name="selectedItems[]" value="{$item.Id}" class="selector" /></td>-->
					<td><img src="{$MAIN_URL}{$item.Image}" class="itemImage" alt="{$item.Image}" /></td>
					<td>{$item.Label}</td>
					<td>{$item.Description}</td>
					<td>{if $item.NoGroupInfo}<span class="error" title="{$item.NoGroupInfo}">{$item.GroupLabel}</span>{else}{$item.GroupLabel}{/if}</td>
					<td>{$item.Points}</td>
					<td><input type="checkbox" name="enabled[]" value="{$item.Id}"{if $item.Enabled} checked="checked"{/if} /></td>
					<td><a href="?show=ProductEdit&amp;item={$item.Id}" title="{$ADM_PRODUCTS_EDIT}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/items/edit.png" height="25" width="25" alt="" /></a></td>
				</tr>
				{/foreach}
				{/if}
			</table>
			<div style="text-align:right"><input type="submit" name="submit" value="{$ADM_PRODUCTS_SUBMIT}" /></div>
		</form>
	</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}