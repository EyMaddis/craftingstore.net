{$main->prepare('Topmenu')}
<div class="AdminBox">

{if $ADM_CONTENT_TOPNAV_EDIT_BOOL}
	<div class="h1"><span>{$ADM_CONTENT_TOPNAV_NEWLINK_TITLE}</span></div>
	<div class="boxcontent">
		<div id="NewLink">{$ADM_CONTENT_TOPNAV_LINK_ERROR}
			<form name="newlink" method="post" action="?show=Content&amp;c=Topmenu&amp;id={$ADM_CONTENT_TOPNAV_EDIT_VALUE_ID}">
				<table style="width:60%;" class="alternating">
					<tr>
						<td>{$ADM_CONTENT_TOPNAV_NAME}</td>
						<td><input name="name" type="text" value="{$ADM_CONTENT_TOPNAV_EDIT_VALUE_NAME}"/></td>
					</tr>
					<tr>
						<td>{$ADM_CONTENT_TOPNAV_LINK}</td>
						<td><input name="link" type="text" value="{$ADM_CONTENT_TOPNAV_EDIT_VALUE_LINK}"/></td>
					</tr>
					<tr>
						<td>{$ADM_CONTENT_TOPNAV_TARGET}</td>
						<td><input name="target" type="text" value="{$ADM_CONTENT_TOPNAV_EDIT_VALUE_TARGET}"/></td><!-- INFO -->
					</tr>
					<tr>
						<td><a style="float:left;" class="button" href="?show=Content&amp;c=Topmenu">{$ADM_CONTENT_TOPNAV_BACK}</a></td>
						<td><input style="float:right;" type="submit" name="submitNewLink" value="{$ADM_CONTENT_TOPNAV_SUBMITLINK}{$ADM_CONTENT_TOPNAV_UPDATELINK}" /></td>
					</tr>
				</table>
			</form>
		</div>
		</div>
	</div>
{else}
	<div class="h1"><span>{$ADM_CONTENT_TOPNAV_TITLE}</span></div>
	<div class="boxcontent">
		<div>{$ADM_CONTENT_TOPNAV_INFO}</div>
		<div style="margin-bottom:5px;">
			<a href="?show=Content&amp;c=Topmenu&amp;id=-1" class="button" style="float:right;">{$ADM_CONTENT_TOPNAV_NEWENTRY}</a>
		</div>
		<table class="alternating">
			<tr>
				<th>{$ADM_CONTENT_TOPNAV_NAME}</th>
				<th>{$ADM_CONTENT_TOPNAV_LINK}</th>
				<th>{$ADM_CONTENT_TOPNAV_TARGET}</th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
			{if $ADM_CONTENT_NO_TOPNAV_LINKS}
			<tr>
				<td colspan="6">{$ADM_CONTENT_NO_TOPNAV_LINKS}</td>
			</tr>
			{else}
			{foreach $ADM_CONTENT_TOPNAV_LINKS item=link}
			<tr>
				<td>{$link.Name}</td>
				<td>{$link.Link}</td>
				<td>{$link.Target}</td>
				<td>
					<div>{if !$link.first}{*No Linking if already first entry*}<a href="?show=Content&amp;c=Topmenu&amp;pos={$link.Position}&amp;moveup={$link.Id}">{/if}<img src="{$MAIN_URL}templates/{$TEMPLATE}/images/arrow_up.png" alt="{$ADM_CONTENT_TOPNAV_MOVEUP}" title="{$ADM_CONTENT_TOPNAV_MOVEUP}" />{if !$link.first}</a>{/if}</div>
					<div>{if !$link.last}{*No Linking if already last entry*}<a href="?show=Content&amp;c=Topmenu&amp;pos={$link.Position}&amp;movedown={$link.Id}">{/if}<img src="{$MAIN_URL}templates/{$TEMPLATE}/images/arrow_down.png" alt="{$ADM_CONTENT_TOPNAV_MOVEDOWN}" title="{$ADM_CONTENT_TOPNAV_MOVEDOWN}" />{if !$link.last}</a>{/if}</div>
				</td>
				<td><a href="?show=Content&amp;c=Topmenu&amp;id={$link.Id}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/edit.png" alt="{$ADM_CONTENT_TOPNAV_EDIT}" title="{$ADM_CONTENT_TOPNAV_EDIT}" /></a></td>
				<td><a href="?show=Content&amp;c=Topmenu&amp;delete={$link.Id}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/delete.png" alt="{$ADM_CONTENT_TOPNAV_DELETE}" title="{$ADM_CONTENT_TOPNAV_DELETE}" /></a></td>
			</tr>
			{/foreach}
			{/if}
		</table>
	</div>
{/if}
</div>