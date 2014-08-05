{$main->prepare('Footer')}
<div style="vertical-align: middle; overflow: auto; margin-bottom: 20px;">
<div class="langchanger">
	{foreach $FOOTER_LANG item=item}
	<a href="?setLang={$item.Id}" title="{$item.Language}"><img src="/images/lang/border.png" alt="{$item.Language}" style="background: url(/images/lang/{$item.Image}) center center no-repeat" /></a>
	{/foreach}
</div>
</div>

<table>
<tr>
	<td style="width:33%">{$POWERED_BY}</td>
	<td style="width:34%; text-align:center">&copy; {$COMPANY}<br />v{$FOOTER_VERSION}</td>
	<td style="width:33%; text-align:right"><a href="{$TOS_URL}">Terms of Service</a><br /><a href="{$IMPRINT_URL}">Impressum</a></td>
</tr>
</table>