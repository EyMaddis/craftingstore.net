{$main->prepare('Content')}
{foreach from=$DISPLAYABLE_CONTENT_TEMPLATES item=Template}
	{assign var=ContentId value=$Template.1}
	<div class="contentBox" id="contentBox{$ContentId}">{include file=$Template.0|cat:'.tpl'}</div>
{/foreach}
{assign var=ContentId value=0}