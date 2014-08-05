{if $ID}{$main->prepare("Itemdetails_$ID")}{else}{$main->prepare('Itemdetails')}{/if}
<script type="text/javascript">
/*
$(".InItemsets .ItemWrapper[title]").tooltip({
	'effect': 'slide',
	'close': function(ev, ui){
		alert('');
		$(this).remove();
	}
});
*/
</script>
<div class="ItemDetails">
{if $ITEMDETAILS_ERROR}
	{$ITEMDETAILS_ERROR_INVALID_ITEM}
{else}
	<div class="CartButton"><a href="?show=Cart&amp;add={$ITEMDETAILS_ITEM_ID}" class="ajaxAddToCart blueButton" title="{$ITEMDETAILS_BUY_NOW}">{$ITEMDETAILS_BUY_BUTTON}</a></div>
	<h2 class="ItemLabel">{$ITEMDETAILS_LABEL}</h2>
	<div>
		<div class="ItemInfos">
			<table><tr><td><img class="ItemImage" src="{$ITEMDETAILS_IMAGE}" /></td>
					<td>
						{if $ITEMDETAILS_DESCRIPTION}<div>{$ITEMDETAILS_DESCRIPTION}</div>{/if}
						<div>{$ITEMDETAILS_POINTS_LABEL}: {$ITEMDETAILS_POINTS} {$POINTSYSTEM}</div>
						<div>{$ITEMDETAILS_BUY_COUNTER_LABEL}</div>
					</td>
				</tr>
			</table>
		</div>

		{if $ITEMDETAILS_INFO_LIST}
		<div class="ItemInfos"><h3>{$ITEMDETAILS_LIST_DESCRIPTION}</h3>
			<div class="InItemsets">
			{foreach from=$ITEMDETAILS_INFO_LIST item=itemInfo}
				{if ($itemInfo.Image != null)}
				<div class="ItemWrapper" title="{$itemInfo.Amount}x {$itemInfo.Name}" >
					<img src="{$itemInfo.Image}" style="width:75px; height:75px;" />
					<div class="InItemsetAmount">{$itemInfo.Amount}</div>
				</div>
				{/if}
				{* <div class="tooltip">{$itemInfo.Amount}x {$itemInfo.Name}</div> *}
			{/foreach}
			</div>
		</div>
		{/if}
	</div>
{/if}
</div>