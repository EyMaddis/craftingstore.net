{$main->prepare('Header')}<div class="shopinfo">
	<div class="logo">
		<a href="/"><img src="{$SHOP_LOGO}" alt="{$SHOP_TITLE}" title="{$SHOP_TITLE}" /></a>
	</div>
	<div class="shopdesc">{$SHOP_TITLE}</div>
</div>
{if $TOPMENU_LINKS}<div class="topmenu">
	<ul>
	{foreach from=$TOPMENU_LINKS item=row}
		<li><a href="{$row.link}" target="{$row.target}">{$row.name}</a></li>
	{/foreach}
	</ul>
</div>{/if}