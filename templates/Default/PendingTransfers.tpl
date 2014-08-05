{$main->prepare('PendingTransfers')}
{if $PENDING_TITLE}
<div class="Pending">
	<h3><a href="#">{$PENDING_TITLE}</a></h3>
	<div>
		<div class="pendingItems">
			{if $PENDING_TRANSFER_INFO}<div>{$PENDING_TRANSFER_INFO}</div>{/if}
			<div>
				<a href="{$PENDING_REFRESH_URL}" class="pendingReload"><img src="./templates/{$TEMPLATE}/images/ico/arrow_refresh.png" width="16px" height="16px" title="{$PENDING_REFRESH}" alt="{$PENDING_REFRESH}" /></a>
				<div><a href="{$TRANSFER_HISTORY_URL}" class="pendingLoadHistory">{$TRANSFER_HISTORY_TITLE}</a></div>
			</div>
		{if $PENDING_NO_ITEMS}{$PENDING_NO_ITEMS}
		{else}
			{foreach from=$PENDING_ITEMS item=pendingItem}
			<div>
				{if $pendingItem.TransferInfo == 1}
					<img src="./templates/{$TEMPLATE}/images/ico/server_delete.png" title="{$PENDING_NEEDS_SERVER_ONLINE}" alt="{$PENDING_NEEDS_SERVER_ONLINE}" />
				{else}
					<a href="{$PENDING_TRANSFER_URL}{$pendingItem.Id}" class="doTransfer"> <img src="./templates/{$TEMPLATE}/images/ico/transmit_go.png" title="{$PENDING_DO_TRANSFER}" alt="{$PENDING_DO_TRANSFER}" /></a>
				{/if}
				<img src="{$pendingItem.Image}" title="{$pendingItem.Amount}x {$pendingItem.Label}" alt="{$pendingItem.Image}" />
				<div>{$pendingItem.Label}</div>
			</div>
			{/foreach}
		{/if}
		</div>
	</div>
</div>
{/if}