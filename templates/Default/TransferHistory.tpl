{$main->prepare('TransferHistory')}
<div class="TransferHistory">
	<div class="head">Übertragungsstatistik</div>
	<div><div style="float:left"><a href="{$TH_BACK_TO_ITEMS_URL}" class="ajaxlink">&laquo; {$TH_BACK_TO_ITEMS_LABEL}</a></div>
		{if $TH_NO_PRODUCTS}<div style="clear:both">{$TH_NO_PRODUCTS}</div>{else}
		<div style="float:right">
		{if $PREVIOUS_PAGE}
		<a href="{$PAGE_URL}{$FIRST_PAGE}" class="ajaxlink">←</a>
		<a href="{$PAGE_URL}{$PREVIOUS_PAGE}" class="ajaxlink">&laquo;</a>
		{/if}
		{for $p=$START_PAGE; $p<=$END_PAGE; $p++}
			{if $p == $CURRENT_PAGE}
				<b>{$p}</b>
			{else}
				<a href="{$PAGE_URL}{$p}" class="ajaxlink">{$p}</a>
			{/if}
		{/for}
		{if $NEXT_PAGE}
		<a href="{$PAGE_URL}{$NEXT_PAGE}" class="ajaxlink">&raquo;</a>
		<a href="{$PAGE_URL}{$LAST_PAGE}" class="ajaxlink">→</a>
		{/if}
		</div>
		<table>
			<tr>
				<td colspan="2">Produkt</td>
				<td>Anzahl</td>
				<td>Übertragungszeit</td>
				<td>Status</td>
			</tr>
			{foreach from=$HISTORY_ITEMS item=historyItem}
			<tr>
				<td><img src="{$historyItem.Image}" title="{$historyItem.Amount}x {$historyItem.Label}" alt="{$historyItem.Image}" /></td><td style="vertical-align:middle">{$historyItem.Label}</td>
				<td>{$historyItem.Amount}</td>
				<td>{$historyItem.TransferTime}</td>
				<td><img src="./templates/{$TEMPLATE}/images/ico/{$historyItem.TransferImage}" title="{$historyItem.TransferInfo}" alt="{$historyItem.TransferImage}" /></td>
			</tr>
			{/foreach}
		</table>{/if}
	</div>
</div>