{$main->prepare('ItemEdit', $params)}
{if $ADM_NEW_ITEM_SAVED}
<input type="hidden" name="addItemId" value="{$ADM_ITEM_EDIT_ID}" />
<div style="margin-bottom:15px;margin-left:25px;">{$ADM_NEW_ITEM_SAVED}<br />
<input type="submit" name="itemEditCancelButton" value="{$BACK}" /></div>
{elseif $ADM_EDIT_ITEM_SAVED}
<div style="margin-bottom:15px;margin-left:25px;">{$ADM_EDIT_ITEM_SAVED}<br />
<input type="submit" name="itemEditCancelButton" value="{$BACK}" /></div>
{elseif isset($ADM_ITEM_EDIT_ID)}
<input type="hidden" name="itemEditId" value="{$ADM_ITEM_EDIT_ID}" />
<div style="margin-bottom:15px;margin-left:25px;overflow:auto">
	<h3{if $ADM_ITEM_EDIT_TRANSFER_SUCCESS == 1} class="successful"{elseif $ADM_ITEM_EDIT_TRANSFER_SUCCESS == 2} class="error"{else if $ADM_ITEM_EDIT_TRANSFER_SUCCESS == 3} class="notice"{/if}>{if $ADM_ITEM_EDIT_TRANSFER_INFO}<span title="{$ADM_ITEM_EDIT_TRANSFER_INFO}">{$ADM_ITEM_EDIT_TITLE}</span>{else}{$ADM_ITEM_EDIT_TITLE}{/if}
		<input type="submit" title="{$ADM_ITEM_EDIT_CANCEL}" class="smallImage cancel" value="+" name="itemEditCancelButton">
		<input type="submit" title="{$ADM_ITEM_EDIT_BUTTON}" class="smallImage accept" value="+" name="itemEditSaveButton">
		<input type="submit" title="{$ADM_ITEM_EDIT_TEST_BUTTON}" class="smallImage test" value="-" name="itemEditTestButton">
	</h3>
	<div class="wideContent">
		<div>
			<div>{$ADM_ITEM_EDIT_NAME}{if $ADM_ITEM_EDIT_INVALID_NAME} <img title="{$ADM_ITEM_EDIT_INVALID_NAME}" alt="Fehler-Bildchen mit hover" src="http://secure.minecraftshop.net/templates/Secure/images/error.png" class="error">{/if}</div>
			<div{if $ADM_ITEM_EDIT_INVALID_NAME} class="error"{/if}><input name="itemEditName" value="{$ADM_ITEM_EDIT_NAME_VALUE}" type="text" class="common_width" /></div>

			<div>{$ADM_ITEM_EDIT_INGAME}</div>
			<div><input name="itemEditIngame" value="{$ADM_ITEM_EDIT_INGAME_VALUE}" type="text" class="common_width" /></div>

			<div>{$ADM_ITEM_EDIT_MINEID_VALUE}{if $ADM_ITEM_EDIT_INVALID_ID} <img title="{$ADM_ITEM_EDIT_INVALID_ID}" alt="Fehler-Bildchen mit hover" src="http://secure.minecraftshop.net/templates/Secure/images/error.png" class="error">{/if}</div>
			<div{if $ADM_ITEM_EDIT_INVALID_ID} class="error"{/if}><input name="itemEditMineId" value="{$ADM_ITEM_EDIT_ID_VALUE}" type="text" size="5" />:<input type="text" name="itemEditValue" value="{$ADM_ITEM_EDIT_VALUE_VALUE}" size="2" /></div>

			<div>{$ADM_ITEM_EDIT_LORE}</div>
			<div><textarea name="itemEditLore" class="common_width">{$ADM_ITEM_EDIT_LORE_VALUE}</textarea></div>

			<div>{$ADM_ITEM_EDIT_ENCHES}</div>
			<div>
{if $ADM_ITEM_EDIT_SELECTED_ENCHES_VALUE}
<div class="wideContent">
	<div>
		<table>
			{foreach $ADM_ITEM_EDIT_SELECTED_ENCHES_VALUE item=row}<tr>
				<td style="width:100%">{$row.Name}:{$row.Amount}</td>
				<td><input type="submit" name="enchRemoveButton[{$row.Id}]" value="-" class="smallImage delete" title="{$ADM_ITEM_EDIT_ENCH_DELETE_INFO}" /><input type="hidden" name="selectedEnches[{$row.Id}]" value="{$row.Id}"><input type="hidden" name="selectedEnchesStrength[{$row.Id}]" value="{$row.Amount}"></td>
			</tr>{/foreach}
		</table>
	</div>
</div>
{/if}
{if $ADM_ITEM_EDIT_AVAILABLE_ENCHES_LIST}
<div class="wideContent">
	<div>
		<table>
			<tr>
				<td style="width:100%"><select name="newEnchId" style="width:calc(100% - 7px)"><option value="0">{$ADM_PRODUCT_EDIT_OPTION_PLEASE_SELECT}</option>{foreach $ADM_ITEM_EDIT_AVAILABLE_ENCHES_LIST item=item key=key}<option value="{$key}"{if $key == $ADM_PRODUCT_EDIT_SELECTED_ENCH} selected="selected"{/if}>{$item}</option>{/foreach}</select></td><td>:</td><td><input type="text" name="newEnchStrength" value="{$ADM_PRODUCT_EDIT_ENCH_STRENGTH}" size="4" /></td>
				<td><input type="submit" name="enchAddButton" value="+" class="smallImage accept" title="{$ADM_ITEM_EDIT_ENCH_ADD_INFO}" /></td>
			</tr>
		</table>
	</div>
</div>
{/if}
{if $ADM_ITEM_EDIT_ENCHES_INFO}<div class="info_box"><div>{$ADM_ITEM_EDIT_ENCHES_INFO}</div></div>{/if}
			</div>
		</div>
	</div>
</div>
{/if}