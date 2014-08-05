{* Die Pagination wird innerhalb der normalen Templates vorbereitet *}<div class="refresh"><a href="{$ADM_PAGE_URL}page={$ADM_PAGE}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/refresh.png" alt="refresh" style="width:32px;height:32px;" /></a></div>
{if $ADM_PAGE_SHOW_NUMBERS}
<table class="pagination">
	<tr>
		<td>{if $ADM_PAGE_BEFORE}<a href="{$ADM_PAGE_URL}page=1">&#8592;</a>{/if}&nbsp;{if $ADM_PAGE_BEFORE}<a href="{$ADM_PAGE_URL}page={$ADM_PAGE_BEFORE}">&laquo;</a>{/if}</td>
		<td>
			<div class="pagination">
				{for $p=$ADM_PAGE_START; $p<=$ADM_PAGE_END; $p++}
				<a href="{$ADM_PAGE_URL}page={$p}"{if $p == $ADM_PAGE} class="current"{/if}>{$p}</a>
				{/for}
				{*section name=pages start=$ADM_PAGE_START loop=$ADM_PAGE_END step=1}
				<a href="{$ADM_PAGE_URL}page={$smarty.section.pages.index}"{if $smarty.section.pages.index == $ADM_PAGE} class="current"{/if}>{$smarty.section.pages.index}</a>
				{/section*}
			</div>
		</td>
		<td class="pagination_right">{if $ADM_PAGE_NEXT}<a href="{$ADM_PAGE_URL}page={$ADM_PAGE_NEXT}">&raquo;</a>{/if}&nbsp;{if $ADM_PAGE_NEXT}<a href="{$ADM_PAGE_URL}page={$ADM_PAGE_LAST}">&#8594;</a> {/if}</td>
	</tr>
</table>{/if}