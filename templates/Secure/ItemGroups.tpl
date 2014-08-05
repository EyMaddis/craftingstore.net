{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('ItemGroups')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_ITEM_GROUPS_TITLE}</span></div>
	<div class="boxcontent">
		{if $ADM_ITEM_GROUPS_MODE == 1}{*Neue Gruppe/Gruppe bearbeiten*}
		{if $ADM_ITEM_GROUPS_NODE_NOT_FOUND_ERROR}{$ADM_ITEM_GROUPS_NODE_NOT_FOUND_ERROR}<br /><a href="?show=ItemGroups" class="button">{$BACK}</a>
		{elseif $ADM_ITEM_GROUP_ADD_DONE}{$ADM_ITEM_GROUP_ADD_DONE}<br /><a href="?show=ItemGroups" class="button">{$BACK}</a>
		{elseif $ADM_ITEM_GROUP_EDIT_DONE}{$ADM_ITEM_GROUP_EDIT_DONE}<br /><a href="?show=ItemGroups" class="button">{$BACK}</a>
		{else}
		<form method="post" action="?show=ItemGroups&amp;mode=edit&amp;node={$ADM_ITEM_GROUP_NODE}">
		{$ADM_ITEM_GROUP_ADD_ERROR}<table style="width:60%;" class="alternating">
			<tr>
				<td>{$ADM_ITEM_GROUP_CHANGE_NAME}</td>
				<td><input type="text" name="label" value="{$ADM_ITEM_GROUP_LABEL}" /></td>
			</tr>
			<tr>
				<td>{$ADM_ITEM_GROUP_CHANGE_DESCRIPTION}</td>
				<td><textarea name="description">{$ADM_ITEM_GROUP_DESCRIPTION}</textarea></td>
			</tr>
			<tr>
				<td>{$ADM_ITEM_GROUP_CHANGE_ROOT_GROUP}</td>
				<td>
					<select name="group">
					{foreach $ADM_ITEM_GROUPS_LIST item=item}
					<option value="{$item.Id}" style="padding-left:{$item.Ebene}px;"{if $item.Id==$ADM_ITEM_GROUP_ID} selected="selected"{/if}>{$item.Label}</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>{$ADM_ITEM_GROUP_CHANGE_IS_ENABLED}</td>
				<td><input type="checkbox" name="enabled" value="1"{if $ADM_ITEM_GROUP_ENABLED} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td><a href="?show=ItemGroups" class="button">{$BACK}</a></td>
				<td><input type="submit" value="{$ADM_ITEM_GROUP_SAVE}" /></td>
			</tr>
		</table>
		</form>
		{/if}
		{else if $ADM_ITEM_GROUPS_MODE == 2}{*Gruppe löschen*}
		<table style="width:60%" class="alternating">
		{if $ADM_ITEM_GROUP_DELETED}
		<tr><td colspan="2">{$ADM_ITEM_GROUP_DELETE_TEXT}</td></tr>
		<tr><td colspan="2"><a href="?show=ItemGroups" class="button">{$BACK}</a></td></tr>
		{else}
		<tr>
			<td colspan="2">{$ADM_ITEM_GROUP_DELETE_TEXT}</td>
		</tr>
		<tr>
			<td><a href="?show=ItemGroups" class="button">{$BACK}</a></td>
			<td><a class="button" href="?show=ItemGroups&amp;mode=del&amp;node={$ADM_ITEM_GROUP_NODE}&amp;do=1">{$ADM_ITEM_GROUP_DELETE_ACCEPT}</a></td>
		</tr>
		{/if}
		</table>
		{else}{*Gruppenübersicht*}
		<table style="width:60%;" class="alternating">
		<tr><th colspan="2">Menüeinträge</th><th colspan="2"><a href="?show=ItemGroups&amp;mode=edit&amp;node=-1"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/new.png" alt="{$ADM_ITEM_GROUP_NEW}" title="{$ADM_ITEM_GROUP_NEW}" /></a></th></tr>
		{if $ADM_ITEM_GROUPS_NO_ITEMS}
		<tr>
			<td colspan="4">{$ADM_ITEM_GROUPS_NO_ITEMS}<br /><a href="?show=ItemGroups&amp;mode=copy">{$ADM_ITEM_GROUPS_COPY}</a></td>
		</tr>
		{foreach $ADM_ITEM_GROUPS_LIST item=item}
		<tr{if !$item.Enabled} class="deactivated" title="{$ADM_ITEM_GROUP_DEACTIVATED}"{/if}>
			<td colspan="4" style="padding-left:{$item.Ebene}px; text-align:left;">{$item.Label}</td>
		</tr>
		{/foreach}
		{else}
			{foreach $ADM_ITEM_GROUPS_LIST item=item}
			<tr{if !$item.Enabled} class="deactivated" title="{$ADM_ITEM_GROUP_DEACTIVATED}"{/if}>
				<td style="padding-left:{$item.Ebene}px; text-align:left;">{$item.Label}</td>
				<td>
					<div><a href="?show=ItemGroups&amp;mode=move&amp;d=up&amp;node={$item.Id}&amp;lft={$item.lft}&amp;rgt={$item.rgt}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/arrow_up.png" alt="{$ADM_ITEM_GROUP_MOVE_UP}" title="{$ADM_ITEM_GROUP_MOVE_UP}" /></a></div>
					<div><a href="?show=ItemGroups&amp;mode=move&amp;d=down&amp;node={$item.Id}&amp;lft={$item.lft}&amp;rgt={$item.rgt}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/arrow_down.png" alt="{$ADM_ITEM_GROUP_MOVE_DOWN}" title="{$ADM_ITEM_GROUP_MOVE_DOWN}" /></a></div>
				</td>
				<td><a href="?show=ItemGroups&amp;mode=edit&amp;node={$item.Id}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/edit.png" alt="{$ADM_ITEM_GROUP_CHANGE}" title="{$ADM_ITEM_GROUP_CHANGE}" /></a></td>
				<td><a href="?show=ItemGroups&amp;mode=del&amp;node={$item.Id}"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/delete.png" alt="{$ADM_ITEM_GROUP_DELETE}" title="{$ADM_ITEM_GROUP_DELETE}" /></a></td>
			</tr>
			{/foreach}
		{/if}
		</table>
		{/if}
	</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}