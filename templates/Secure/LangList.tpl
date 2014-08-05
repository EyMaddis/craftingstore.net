{$main->prepare('LangList')}<div style="position:fixed; right:5px; top:5px;">
	{foreach $LANGS item=item}
	<a href="{$LANG_PRE_URL}setLang={$item.Id}" title="{$item.Language}"><img src="/images/lang/border.png" alt="{$item.Language}" style="background: url(/images/lang/{$item.Image}) center center no-repeat" /></a>
	{/foreach}
</div>