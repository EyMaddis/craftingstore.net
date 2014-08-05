{$main->prepare('ItemSearch')}
<div class="Itemsearch">
	<h3><a href="#">{$ITEMSEARCH_TITLE}</a></h3>
	<div>
		<form action="?show={literal}{Itembox}{/literal}" method="post" class="searchForm">
			<div style="text-align: center;">
				<input class="searchfield pad" type="text" name="search" onblur="blured(this,'{$ITEMSEARCH_WORDS}');" onfocus="focused(this,'{$ITEMSEARCH_WORDS}');" value="{$ITEMSEARCH_WORDS}" /><br />
	{$ITEMSEARCH_SEARCH_IN}<select class="searchfield pad" name="searchgroup"><option value="-1">{$ITEMSEARCH_SEARCH_EVERYWHERE}</option>
	{foreach $ITEMSEARCH_GROUPLIST item=item}<option value="{$item.Id}">
	{section name=padding start=2 loop=$item.Level*4 step=1}&nbsp;{/section}{$item.Label}</option>{/foreach}
	</select><br />
				<label for="searchInSubgroups">{$ITEMBOX_NO_SUBGROUPS} <input type="checkbox" name="noSubgroups" id="searchInSubgroups" class="pad" /></label><br />
				<input type="submit" value="{$ITEMSEARCH_FIND}" class="submit" />
			</div>
		</form>
	</div>
</div>