{$main->prepare('Inventory')}
{if !$INV_DO_TRANSFER_ITEMS}
	<div class="Inventar">
		<h3><a href="#">{$INV_INVENTORY_TITLE}</a></h3>
		<div>
			<div class="inventarItems">
		{if $INV_IS_LOGGED_IN}
			{if $INV_HAS_ITEMS}
				<form class="ItemsToGameForm" name="ItemsToGameForm" action="?show=Inventory&amp;transfer=1" method="post">
					{foreach from=$INV_CURRENT_ITEMS item=currentInventarItem}
					<div>
						<input type="checkbox" class="inventarCheckbox" id="check{$currentInventarItem.itemId}" name="itemId[]" value="{$currentInventarItem.itemId}" />
						<label for="check{$currentInventarItem.itemId}">
							<img src="{$currentInventarItem.image}" width="25px" height="25px" title="{$currentInventarItem.amount}x {$currentInventarItem.label}" alt="{$currentInventarItem.image}" />
						</label>
					</div>
					{/foreach}
					<input type="submit" class="itemsToGameSubmit" value="{$INV_TRANSFER_SUBMIT}" />
				</form>
			{else}{$INV_NO_ITEMS}{/if}
		{else}
			<div style="width:100%;"><a href="#" class="otherOpenLoginPanel">{$INV_PLEASE_LOGIN}</a></div>
		{/if}
			</div>
		</div>
	</div>
{else}
	<div class="transferItems">
		<div>{$INV_TRANSFER_TITLE}</div>
		{if $INV_ERROR}{$INV_ERROR}
		{else if $INV_SHOW_ITEM_LIST}
			<form action="?show=Inventory&amp;transfer=1" class="itemToGameForm" method="post">
				<table>
			{if $INV_SELECTED_ITEMS_LIST}
				{foreach from=$INV_SELECTED_ITEMS_LIST item=row}
					<tr{if ($row.error)} class="error"{/if}>
						<td><input type="hidden" value="{$row.itemId}" name="itemId[]" /></td>
						<td><img src="{$row.image}" alt="{$row.image}" style="width:25px; height:25px; vertical-align:middle;" /></td>
						<td>{$row.label}</td>
						<td><input type="text" value="{$row.amount}" name="amount[]" style="width:30px; text-align:right;" />/{$row.sum}</td>
					</tr>
				{/foreach}
			{/if}
			{if $INV_SELECTED_ITEMS_LIST && $INV_UNSELECTED_ITEMS_LIST}<tr><td colspan="4"><hr style="margin:4px;" /></td></tr>{/if}
			{if $INV_UNSELECTED_ITEMS_LIST}
				{foreach from=$INV_UNSELECTED_ITEMS_LIST item=row}
					<tr>
						<td><input type="hidden" value="{$row.itemId}" name="itemId[]" /></td>
						<td><img src="{$row.image}" alt="{$row.image}" style="width:25px; height:25px; vertical-align:middle;" /></td>
						<td>{$row.label}</td>
						<td><input type="text" value="{$row.amount}" name="amount[]" style="width:30px; text-align:right;" />/{$row.sum}</td>
					</tr>
				{/foreach}
			{/if}
				</table>
				<div class="transferItemButton">
					<input type="submit" class="AjaxSubmit" value="{$INV_TRANSFER_SUBMIT}" />
				</div>
			</form>
		{else}
			{foreach from=$INV_TRANSFERED_ITEMS item=row}
				<div{if (!$row.success)} style="background-color:#ffaaaa"{/if}>
					<img src="{$row.image}" alt="{$row.image}" style="width:32px; height:32px; vertical-align:middle;" />
					{$row.amount} {$row.label}
					{if ($row.success)}
						<img src="{$templatedir}images/ico/accept.png" alt="success" style="width:16px; height:16px; vertical-align:middle;" />
					{else}
						<img src="{$templatedir}images/ico/cross.png" alt="error" style="width:16px; height:16px; vertical-align:middle;" title="Das Produkt konnte nicht übertragen werden. Möglicherweise musst Du auf dem Server eingeloggt sein, damit das Produkt übertragen werden kann." />
					{/if}
				</div>
			{/foreach}
		{/if}
		<br /><a href="#" class="transferClose">{$INV_CLOSE}</a>
	</div>
{/if}