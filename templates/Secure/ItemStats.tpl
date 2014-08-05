{$main->prepare('ItemStats')}
<div class="AdminBox">
	<div class="h1">{include file="Pagination.tpl"}<span>{$ADM_ITEMSTATS_TITLE}</span></div>
	<div class="boxcontent">
		<table class="alternating">
			<tr>
				<th></th>
				<th>{$ADM_ITEMSTATS_ITEM}</th>
				<th>{$ADM_ITEMSTATS_GROUP}</th>
				<th>{$ADM_ITEMSTATS_POINTS}</th>
				<th>{$ADM_ITEMSTATS_BUYCOUNT}</th>
				<th>{$ADM_ITEMSTATS_REVENUE}</th>
			</tr>
			{foreach $ADM_ITEM_LIST item=item}
			<tr>
				<td><img src="{$MAIN_URL}{$item.Image}" class="itemImage" alt="{$item.Image}" /></td>
				<td>{$item.Label}</td>
				<td>{$item.GroupLabel}</td>
				<td>{$item.Points}</td>
				<td>{$item.BuyCounter}</td>
				<td>{$item.Revenue}</td>
			</tr>
			{/foreach}
		</table>
	</div>
</div>