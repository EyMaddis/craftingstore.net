{$main->prepare('Account')}{if $ACCOUNT_TITLE}
<div class="Kontostand">
	<h3><a href="#">{$ACCOUNT_TITLE}</a></h3>
	<div>
		<p>{$ACCOUNT_CURRENT} {$ACCOUNT_CURRENT_POINTS}
		{if $ACCOUNT_CURRENT_BONUSPOINTS != false}
		<span class="bonuspoints" title="{$ACCOUNT_BONUSPOINTS}">+{$ACCOUNT_CURRENT_BONUSPOINTS}</span>{/if} {$POINTSYSTEM}</p>
		<div style="text-align: center;">
		<a href="{$ACCOUNT_REDIRECT}" title="{$ACCOUNT_PAY_MORE}">{$ACCOUNT_PAY_MORE}</a></div>
	</div>
</div>{/if}