{$main->prepare('LatestSales')}
<div class="AdminBox">
	<div class="h1">{include file="Pagination.tpl"}<span>{$ADM_LATEST_TITLE}</span></div>
	<div class="boxcontent">
		<table class="alternating">
			<tr>
				<th></th>
				<th>{$ADM_LATEST_HEADER_PRODUCT}</th>
				<th>{$ADM_LATEST_HEADER_PLAYER}</th>
				<th>{$ADM_LATEST_HEADER_AMOUNT}</th>
				<th>{$ADM_LATEST_HEADER_DATE}</th>
				<th>{$ADM_LATEST_HEADER_STATUS}</th>
				<th>{$ADM_LATEST_HEADER_REVENUE}</th>
			</tr>
			{if $ADM_LATEST_NO_LIST}
			<tr>
				<td colspan="7">{$ADM_LATEST_NO_LIST}</td>
			</tr>
			{else}
			{foreach $ADM_LATEST_LIST item=item}
			<tr>
				<td><img src="{$MAIN_URL}{$item.Image}" class="itemImage" alt="{$item.Image}" /></td>
				<td>{$item.Item}</td>
				<td>{$item.Name}</td>
				<td>{$item.Amount}</td>
				<td>{$item.Date}</td>
				<td class="{$item.class}" title="{$item.Info}">{$item.Status}</td>
				<td class="{$item.class}">{$item.Difference}</td>
			</tr>
			{/foreach}
			{/if}
		</table>
	</div>
</div>