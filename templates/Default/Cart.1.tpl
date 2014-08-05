{if $ACCOUNT_CURRENT}<div class="accountInfo">{$ACCOUNT_CURRENT} {$ACCOUNT_CURRENT_POINTS} {if $ACCOUNT_CURRENT_BONUSPOINTS > 0}<span class="bonuspoints" title="{$ACCOUNT_BONUSPOINTS}">+{$ACCOUNT_CURRENT_BONUSPOINTS}</span>{/if} {$POINTSYSTEM}</div>{/if}
<table class="cartOverview">
	<tr>
		 <th></th>
		 <th style="width:100%">{$BUYITEM_PRODUCTS_NAME_lABEL}</th>
		 <th>{$AMOUNT}</th>
		 <th>{$POINTSYSTEM}</th>
		 {if !$NO_CHANGE}<th style="width:16px"></th>{/if}
	</tr>
	{if $CART_EMPTY}
	<tr>
		<td colspan="{if $NO_CHANGE}4{else}5{/if}" style="text-align:center">{$CART_EMPTY}</td>
	</tr>{else}
	{foreach from=$CART_DATA item=product}
	<tr>
		 <td><img src="{$product.Image}" width="32px" /></td>
		 <td>{$product.Label}</td>
		 <td>{if $product.OnlyOnce || $NO_CHANGE}{$product.Amount}{else}<input type="text" name="amount[{$product.Id}]" value="{$product.Amount}" />{/if}</td>
		 <td>{$product.Points}</td>
		 {if !$NO_CHANGE}<td><a href="?show=Cart&amp;popup&amp;remove={$product.Id}" class="_CART_LINK"><img src="./templates/{$TEMPLATE}/images/ico/cross.png" /></a></td>{/if}
	</tr>
	{/foreach}{/if}
	<tr class="total">
		 <td colspan="3" class="totalCostsLabel">{$BUYITEM_TOTAL_COSTS_LABEL}</td>
		 <td class="totalCosts">{$BUYITEM_TOTAL_COSTS}</td>
		 {if !$NO_CHANGE}<td></td>{/if}
	</tr>
</table>