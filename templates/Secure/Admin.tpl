{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('Admin')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_ADMIN_TITLE}</span></div>
	<div class="boxcontent">
		<div id="Admin">
			<div id="quickmenu">
				<div class="h1"><span>{$ADM_QUICK_MENU_TITLE}</span></div>
				<div class="boxcontent"><ul>{foreach $ADM_QUICK_MENU_ELEMENTS item=element}<li><a href="{$element.url}">{$element.name}</a></li>{/foreach}</ul></div>
			</div>
			{$ADM_ADMIN_DESCRIPTION}
			<br /><br /><br />
			{if $ADM_ADMIN_RSS}
			<div class="news">
				<h2 id="rss_title">{$ADM_ADMIN_RSS_TITLE}</h2>
				{foreach $ADM_ADMIN_RSS item=feed}
				<div class="single-news">
					<h2><a href="{$feed.link}" title="{$ADM_ADMIN_TO_ARTICLE}">{$feed.title}</a></h2> 
					<span class="news-info">{$feed.date}</span>
					<div class="news-content"><p>{$feed.desc}</p><hr /></div>
				</div>
				{/foreach}
			</div>
			{/if}
		</div>
	</div>
</div>

{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}