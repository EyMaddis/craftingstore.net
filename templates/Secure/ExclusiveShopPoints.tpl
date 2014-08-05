{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('ExclusiveShopPoints')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_DISTRIBUTE_BONUS_TITLE}</span></div>
	<div class="boxcontent">
	{if $INPUT}
		<form action="?show=ExclusiveShopPoints" method="post">
			<table style="width:60%" class="alternating">
				<tr>
					<td>{$ADM_DISTRIBUTE_BONUS_PLAYER}</td>
					<td><select name="player">{foreach $PLAYER item=item}<option value="{$item.Id}"{if $item.selected} selected="selected"{/if}>{$item.Name}</option>{/foreach}</select></td>
				</tr>
				<tr>
					<td>{$ADM_DISTRIBUTE_BONUS_AMOUNT}</td>
					<td><input type="text" name="points" size="4" value="{$POINTS}"></td>
				</tr>
				<tr>
					<td><a href="{$ADM_BONUS_BACK_URL}" class="button">{$ADM_BONUS_CANCEL}</a></td>
					<td><input type="submit" name="submit" value="{$ADM_BONUS_GIVE}" /></td>
				</tr>
			</table>
		</form>
	{else}
		<div>{$ADM_BONUS_GIVEN}<br /><a href="{$ADM_BONUS_BACK_URL}" class="button">{$ADM_BONUS_BACK}</a></div>
	{/if}
	</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}