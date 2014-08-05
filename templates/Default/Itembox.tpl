<div class="_ITEMBOX">
{$main->prepare("Itembox_$ContentId")}
{if (!$ITEMBOX_ITEM_NAVIGATION_DISABLED)}
<div class="itemNavigation">
	<div class="itemSorter">
		<form class="itemSorterForm ajaxForm" action="?show=Itembox_{$ContentId}" method="post">
			<input type="hidden" name="groupId" value="{$ITEMBOX_GROUP_ID}" />
			<select class="sortField" name="sortField">
				<option value="name"{$ITEMBOX_SORT_ALPHABET_SELECT}>{$ITEMBOX_SORT_ALPHABET}</option>
				<option value="price"{$ITEMBOX_SORT_PRICE_SELECT}>{$ITEMBOX_SORT_PRICE}</option>
				<option value="popular"{$ITEMBOX_SORT_POPULARITY_SELECT}>{$ITEMBOX_SORT_POPULARITY}</option>
			</select>
			<select class="sortDirection" name="sortDirection">
				<option value="asc"{$ITEMBOX_SORT_ASCENDING_SELECT}>{$SORT_ASCENDING}</option>
				<option value="desc"{$ITEMBOX_SORT_DESCENDING}>{$SORT_DESCENDING}</option>
			</select>
		{if ($ITEMBOX_TOTAL_PAGES > 1)}
			<select class="currentPage" name="currentPage" size="1">
			{section name="i" start=0 loop=$ITEMBOX_TOTAL_PAGES step=1}
				{if ($ITEMBOX_CURRENT_PAGE == $smarty.section.i.index)}
				<option value="{$smarty.section.i.index}" selected="selected">{$ITEMBOX_PAGE_LABEL} {$smarty.section.i.index + 1}</option>
				{else}
				<option value="{$smarty.section.i.index}">{$ITEMBOX_PAGE_LABEL} {$smarty.section.i.index + 1}</option>
				{/if}
			{/section}
			</select>
		{/if}<br/>
			<label for="itemsPerPage{$ContentId}">{$ITEMBOX_LIMIT_LABEL}</label>
			<input id="itemsPerPage{$ContentId}" name="itemsPerPage" class="ui-widget ui-widget-content ui-corner-left ui-corner-right itemsPerPage" type="text" value="{$ITEMBOX_ITEMS_PER_PAGE}" size="2" maxlength="2" />
			<label for="noSubgroups{$ContentId}">{$ITEMBOX_NO_SUBGROUPS}</label>
			<input id="noSubgroups{$ContentId}" class="noSubgroups" name="noSubgroups" type="checkbox" value="1"{$ITEMBOX_NO_SUBGROUPS_SELECT} />
			<input class="itemSorterSubmit" type="submit" name="submit" value="{$ITEMBOX_UPDATE}" />
		</form>
		{if $ITEMBOX_GROUP_PATH}<div class="ItemCategory">{foreach $ITEMBOX_GROUP_PATH item=item}<a href="{$item.BoxUrl}" class="ajaxlink">&raquo;{$item.Label}</a> &nbsp;{/foreach}&raquo;{$ITEMBOX_CURRENT_GROUP}</div>{/if}
	</div>
</div>
{/if}
<div class="items"{if $Width} style="width:{$Width};"{/if}>
	{if $ITEMBOX_CONTENT_ITEM_ARRAY == null}
		{$ITEMBOX_NO_ITEMS_FOUND}
	{else}
		{foreach from=$ITEMBOX_CONTENT_ITEM_ARRAY item=currentItem}
		<div class="singleItem">
			<div class="ItemPopularity">{$ITEMBOX_BUY_COUNT}{$currentItem.Popularity}x</div>
			<div class="ItemCategory"><a href="{$currentItem.GroupLink}" class="ajaxlink">{$currentItem.GroupLabel}</a></div>
			<div class="ItemLabel">{$currentItem.Label}</div>
			<div class="itemImage" style="background-image:url({$currentItem.Image});"></div>
			{if ($currentItem.Pricetag == 1)}
			<div class="ItemPrice SetPrice">{$currentItem.Points} {$POINTSYSTEM_SHORT}</div>
			{else}
			<div class="ItemPrice">{$currentItem.Points} {$POINTSYSTEM_SHORT}</div>
			{/if}
			<div class="Buttons">
				<div class="DetailsButton"><a href="{$currentItem.DetailsUrl}" class="ajaxItemDetailsDialog greyButton" title="{$ITEMBOX_ITEM_DETAILS_LABEL}">{$ITEMBOX_ITEM_DETAILS_BUTTON}</a></div>
				<div class="CartButton"><a href="{$currentItem.ToCartUrl}" class="ajaxAddToCart blueButton" title="{$ITEMBOX_TO_CART_NOW}">{$ITEMBOX_TO_CART}</a></div>
			</div>
		</div>
		{/foreach}
	{/if}
</div>
</div>