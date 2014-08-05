{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('ProductEdit')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_PRODUCT_EDIT_TITLE}</span></div>
	<div class="boxcontent">
	{if $ADM_PRODUCT_EDIT_MODE}
		<form action="?show=ProductEdit&amp;item={$ADM_PRODUCT_EDIT_ID}" method="post" enctype="multipart/form-data" name="productEdit"><input type="hidden" name="MAX_FILE_SIZE" value="512000" />
		<input type="submit" style="visibility:hidden;float:left;" name="hidden_button" />{if $ADM_PRODUCT_EDIT_IMG_ID}<input type="hidden" name="imgId" value="{$ADM_PRODUCT_EDIT_IMG_ID}" />{/if}
		<ul class="product_config" id="productConfig"><input type="hidden" name="closeAreas" value="{if $CLOSE_AREAS == NULL}commands,products,items,limit{else}{$CLOSE_AREAS}{/if}" />
			<li class="active"><input type="hidden" name="state[]" />
				<span onclick="toggleProductEditArea(this)" id="general">{$ADM_PRODUCT_EDIT_GENERAL}</span>
				<div>
					<div class="def">
						<div>{$ADM_PRODUCT_EDIT_LABEL}</div>
						<div{if $ADM_PRODUCT_EDIT_LABEL_ERROR} class="error"{/if}>
							<input class="common_width" type="text" name="label" value="{$ADM_PRODUCT_EDIT_LABEL_VALUE}" />
							{if $ADM_PRODUCT_EDIT_LABEL_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" alt="Fehler-Bildchen mit hover" title="{$ADM_PRODUCT_EDIT_LABEL_ERROR}" />{/if}
						</div>{if $ADM_PRODUCT_EDIT_LABEL_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_LABEL_INFO}</div></div>{/if}
					</div>
					<div class="def">
						<div>{$ADM_PRODUCT_EDIT_ICON}</div>
						<div{if $ADM_PRODUCT_EDIT_ICON_ERROR} class="error"{/if}>
							{if $ADM_PRODUCT_EDIT_IMAGE}
								<img src="{$ADM_PRODUCT_EDIT_IMAGE}" class="prod_image" />
								<input type="submit" class="smallImage delete" name="delImageButton" />
							{else if $ADM_PRODUCT_EDIT_IMG_ID}
								<img src="/tmp_img.php?src={$ADM_PRODUCT_EDIT_IMG_ID}" class="prod_image" />
								<input type="submit" class="smallImage delete" name="delImageButton" />
							{/if}
							{if $ADM_PRODUCT_EDIT_IMAGE_CAN_UNDO}{if $ADM_PRODUCT_EDIT_IMAGE || $ADM_PRODUCT_EDIT_IMG_ID}<br />{/if}<input type="submit" class="smallImage imageUndo" name="imageUndoButton" /><br />{/if}
							<input type="file" name="newIcon" /><input type="submit" class="smallImage imageUpload" value="+" name="uploadImageButton" />
							{if $ADM_PRODUCT_EDIT_ICON_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" title="{$ADM_PRODUCT_EDIT_ICON_ERROR}" alt="Fehler-Bildchen mit hover" />{/if}
						</div>{if $ADM_PRODUCT_EDIT_ICON_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_ICON_INFO}</div></div>{/if}
					</div>
					<div class="def">
						<div>{$ADM_PRODUCT_EDIT_DECSCRIPTION}</div>
						<div>
							<textarea name="description" class="common_width" rows="5" cols="30">{$ADM_PRODUCT_EDIT_DECSCRIPTION_VALUE}</textarea>
						</div>{if $ADM_PRODUCT_EDIT_DECSCRIPTION_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_DECSCRIPTION_INFO}</div></div>{/if}
					</div>
					<div class="def">
						<div>{$ADM_PRODUCT_EDIT_GROUP}</div>
						<div{if $ADM_PRODUCT_EDIT_GROUP_ERROR} class="error"{/if}>
							<select name="group" class="common_width">
							{foreach $ADM_PRODUCT_EDIT_GROUP_VALUES item=row}
								<option style="padding-left:{$row.Ebene}px" value="{$row.Id}"{if $row.Id == $ADM_PRODUCT_EDIT_GROUP_VALUE} selected="selected"{/if}>{$row.Label}</option>
							{/foreach}
							</select>{if $ADM_PRODUCT_EDIT_GROUP_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" title="{$ADM_PRODUCT_EDIT_GROUP_ERROR}" />{/if}
						</div>{if $ADM_PRODUCT_EDIT_GROUP_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_GROUP_INFO}</div></div>{/if}
					</div>
					<div class="def">
						<div>{$ADM_PRODUCT_EDIT_POINTS}</div>
						<div{if $ADM_PRODUCT_EDIT_POINTS_ERROR} class="error"{/if}>
							<input type="text" name="points" value="{$ADM_PRODUCT_EDIT_POINTS_VALUE}" /> {$ADM_PRODUCT_EDIT_POINTS_DESC}
							{if $ADM_PRODUCT_EDIT_POINTS_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" alt="Fehler-Bildchen mit hover" title="{$ADM_PRODUCT_EDIT_POINTS_ERROR}" />{/if}
						</div>{if $ADM_PRODUCT_EDIT_POINTS_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_POINTS_INFO}</div></div>{/if}
					</div>
					<div class="def">
						<div>{$ADM_PRODUCT_EDIT_ENABLED}</div>
						<div>
							<input type="checkbox" name="enabled"{if $ADM_PRODUCT_EDIT_ENABLED_VALUE} checked="checked"{/if} />
						</div>{if $ADM_PRODUCT_EDIT_ENABLED_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_ENABLED_INFO}</div></div>{/if}
					</div>
				</div>
			</li>
			<li class="active"><input type="hidden" name="state[]" />
				{if $ADM_PRODUCT_EDIT_COMMANDS_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" title="{$ADM_PRODUCT_EDIT_COMMANDS_ERROR}" alt="Fehler-Bildchen mit hover" />
				{else if $ADM_PRODUCT_EDIT_CONTENT_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" title="{$ADM_PRODUCT_EDIT_CONTENT_ERROR}" alt="Fehler-Bildchen mit hover" />{/if}
				<span onclick="toggleProductEditArea(this)" id="commands">{$ADM_PRODUCT_EDIT_COMMANDS}</span>
				<div {if $ADM_PRODUCT_EDIT_COMMANDS_ERROR} class="error"{/if}>
					<div class="fullContent">
						<div>{$ADM_PRODUCT_EDIT_NEEDS_PLAYER_ONLINE} <input type="checkbox" name="needsPlayerOnline"{if $ADM_PRODUCT_EDIT_NEEDS_PLAYER_ONLINE_VALUE} checked="checked"{/if} /></div>
						{if $ADM_PRODUCT_EDIT_NEEDS_PLAYER_ONLINE_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_NEEDS_PLAYER_ONLINE_INFO}</div></div>{/if}
					</div>
					<div class="wideContent">
						<div>
							<table>
								{foreach $ADM_PRODUCT_EDIT_COMMANDS_VALUE item=command}<tr{if $command.2 == 1} class="success"{elseif $command.2 == 2} class="error"{/if}{if $command.3} title="{$command.3}"{/if}>
									<td style="width:100%"><input type="text" name="customCommands[{$command.0}]" value="{$command.1}" class="common_width" /></td>
									<td><input type="submit" name="customCommandDeleteButton[{$command.0}]" value="" title="{$ADM_PRODUCT_EDIT_DELETE_COMMAND}" class="smallImage delete" /></td>
									<td><input type="submit" name="customCommandTestButton[{$command.0}]" value="" title="{$ADM_PRODUCT_EDIT_TEST_COMMAND}" class="smallImage test" /></td>
								</tr>{/foreach}
							</table>
							<input type="submit" name="customCommandAddButton" value="+" class="smallImage add right" title="{$ADM_PRODUCT_EDIT_COMMAND_NEW}" />
						</div>
						{if $ADM_PRODUCT_EDIT_COMMANDS_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_COMMANDS_INFO}</div></div>{/if}
					</div>
				</div>
			</li>
			<li class="active"><input type="hidden" name="state[]" />
				{if $ADM_PRODUCT_EDIT_CONTENT_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" style="margin-left:10px;" title="{$ADM_PRODUCT_EDIT_CONTENT_ERROR}" alt="Fehler-Bildchen mit hover" />{/if}
				<span onclick="toggleProductEditArea(this)" id="products">{$ADM_PRODUCT_EDIT_PRODUCTS}</span>{if $ADM_PRODUCT_EDIT_PRODUCTS_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" style="margin-left:10px;" title="{$ADM_PRODUCT_EDIT_PRODUCTS_ERROR}" alt="Fehler-Bildchen mit hover" />{/if}
				<div>
					{if $ADM_PRODUCT_EDIT_SELECTED_PRODUCTS_VALUE}
					<div class="wideContent">
						<div>
							<table>
								{foreach $ADM_PRODUCT_EDIT_SELECTED_PRODUCTS_VALUE item=row}<tr>
									<td style="width:100%">{$row.Label} ({$row.Amount})
										<input type="hidden" name="selectedProducts[{$row.Id}]" value="{$row.Id}">
										<input type="hidden" name="selectedProductsAmount[{$row.Id}]" value="{$row.Amount}">
									</td>
									<td><input type="submit" name="productRemoveButton[{$row.Id}]" value="-" class="smallImage delete" title="{$ADM_PRODUCT_EDIT_PRODUCT_DELETE_INFO}" /></td>
								</tr>{/foreach}
							</table>
						</div>
						{if $ADM_PRODUCT_EDIT_PRODUCTS_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_PRODUCTS_INFO}</div></div>{/if}
					</div>
					{/if}
					{if $ADM_PRODUCT_EDIT_AVAILABLE_PRODUCTS_LIST}
					<div class="wideContent">
						<div>
							<table>
								<tr>
									<td style="width:100%"><select name="addProductId" style="width:calc(100% - 14px)"><option value="0">{$ADM_PRODUCT_EDIT_OPTION_PLEASE_SELECT}</option>{foreach $ADM_PRODUCT_EDIT_AVAILABLE_PRODUCTS_LIST item=item key=key}<option value="{$key}"{if $key == $ADM_PRODUCT_EDIT_SELECTED_PRODUCT} selected="selected"{/if}>{$item}</option>{/foreach}</select></td>
									<td style="white-space:nowrap;">{$ADM_PRODUCT_EDIT_AMOUNT} <input type="text" name="addProductAmount" value="{$ADM_PRODUCT_EDIT_PRODUCT_AMOUNT}" size="2" /></td>
									<td><input type="submit" name="productAddButton" value="+" class="smallImage accept" title="{$ADM_PRODUCT_EDIT_PRODUCT_ADD_INFO}" /></td>
								</tr>
							</table>
						</div>
						{if $ADM_PRODUCT_EDIT_PRODUCTS_ADD_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_PRODUCTS_ADD_INFO}</div></div>{/if}
					</div>
					{else}{$ADM_PRODUCT_EDIT_SELECTED_PRODUCTS_EMPTY}{/if}
				</div>
			</li>
			<li class="active"><input type="hidden" name="state[]" />
				{if $ADM_PRODUCT_EDIT_CONTENT_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" style="margin-left:10px;" title="{$ADM_PRODUCT_EDIT_CONTENT_ERROR}" alt="Fehler-Bildchen mit hover" />{/if}
				{if $ADM_PRODUCT_EDIT_ITEMS_NEED_SAVE}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" style="margin-left:10px;" title="{$ADM_PRODUCT_EDIT_ITEMS_NEED_SAVE}" alt="Fehler-Bildchen mit hover" />{/if}
				<span onclick="toggleProductEditArea(this)" id="items">{$ADM_PRODUCT_EDIT_ITEMS}</span>
				<div>{foreach $ADM_PRODUCT_EDIT_SELECTED_ITEMS_VALUE item=row}<input type="hidden" name="selectedItems[{$row.Id}]" value="{$row.Id}"/><input type="hidden" name="selectedItemsAmount[{$row.Id}]" value="{$row.Amount}"/>{/foreach}
{if $editItemParams}
	{include file="ItemEdit.tpl" params=$editItemParams}
{else}
{if $ADM_PRODUCT_EDIT_SELECTED_ITEMS_VALUE}<div class="wideContent">
	<div>
		<table>
			{foreach $ADM_PRODUCT_EDIT_SELECTED_ITEMS_VALUE item=row}<tr>
				<td style="width:100%"><div>{$row.Name} ({$row.Amount})</div></td>
				<td style="vertical-align:top"><input type="submit" name="itemEditButton[{$row.Id}]" value="-" class="smallImage edit" title="{$ADM_PRODUCT_EDIT_ITEM_EDIT_INFO}" /></td>
				<td style="vertical-align:top"><input type="submit" name="itemRemoveButton[{$row.Id}]" value="-" class="smallImage delete" title="{$ADM_PRODUCT_EDIT_ITEM_DELETE_INFO}" /></td>
			</tr>{/foreach}
		</table>
	</div>{if $ADM_PRODUCT_EDIT_ITEM_ADD_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_ITEMS_INFO}</div></div>{/if}
</div>{/if}
<div class="wideContent">
	<div>
		<table>
			<tr>{if $ADM_PRODUCT_EDIT_AVAILABLE_ITEMS_LIST}
				<td style="width:100%"><select style="width:calc(100% - 14px);" name="addItemId"><option value="0">{$ADM_PRODUCT_EDIT_OPTION_PLEASE_SELECT}</option>{foreach $ADM_PRODUCT_EDIT_AVAILABLE_ITEMS_LIST item=item key=key}<option value="{$key}"{if $key == $ADM_PRODUCT_EDIT_SELECTED_ITEM} selected="selected"{/if}>{$item}</option>{/foreach}</select></td>
				<td style="white-space:nowrap;">{$ADM_PRODUCT_EDIT_AMOUNT} <input name="addItemAmount" type="text" size="2" value="{$ADM_PRODUCT_EDIT_ITEM_AMOUNT}"></td>
				<td><input type="submit" title="{$ADM_PRODUCT_EDIT_ITEM_ADD_INFO}" class="smallImage accept" value="+" name="itemAddButton"></td>
			{/if}	<td><input type="submit" title="{$ADM_PRODUCT_EDIT_ITEM_CREATE_INFO}" class="smallImage add" value="+" name="itemCreateButton"></td>
			</tr>
		</table>
	</div>
	{if $ADM_PRODUCT_EDIT_ITEMS_ADD_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_ITEMS_ADD_INFO}</div></div>{/if}
</div>
{/if}
				</div>
			</li>
			<li class="active"><input type="hidden" name="state[]" />
				<span onclick="toggleProductEditArea(this)" id="limit">{$ADM_PRODUCT_EDIT_TIME_LIMIT}</span>
				<div>
					<div class="def">
						<div><label for="lifetimeActivator">{$ADM_PRODUCT_EDIT_ACTIVE}</label></div>
						<div><input id="lifetimeActivator" type="checkbox" name="cooldownActive" onchange="showHidelifetimeSelection(4)"{if $ADM_PRODUCT_EDIT_COOLDOWN_ACTIVE_VALUE} checked="checked"{/if} /></div>
						{if $ADM_PRODUCT_EDIT_LIMIT_ACTIVE_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_LIMIT_ACTIVE_INFO}</div></div>{/if}
					</div>
					<div id="lifetime0" class="def">
						<div>{$ADM_PRODUCT_EDIT_COOLDOWN}</div>
						<div{if $ADM_PRODUCT_EDIT_COOLDOWN_ERROR} class="error"{/if}>
							<input type="text" size="2" style="text-align:right" value="{$ADM_PRODUCT_EDIT_COOLDOWN_VALUE}" name="cooldown">
							<select name="interval">
								<option value="i"{if $ADM_PRODUCT_EDIT_LIFETIME_INTERVAL == 'i'} selected="selected"{/if}>{$MINUTES}</option>
								<option value="h"{if $ADM_PRODUCT_EDIT_LIFETIME_INTERVAL == 'h'} selected="selected"{/if}>{$HOURS}</option>
								<option value="d"{if $ADM_PRODUCT_EDIT_LIFETIME_INTERVAL == 'd'} selected="selected"{/if}>{$DAYS}</option>
								<option value="w"{if $ADM_PRODUCT_EDIT_LIFETIME_INTERVAL == 'w'} selected="selected"{/if}>{$WEEKS}</option>
								<option value="m"{if $ADM_PRODUCT_EDIT_LIFETIME_INTERVAL == 'm'} selected="selected"{/if}>{$MONTHS}</option>
							</select>
							{if $ADM_PRODUCT_EDIT_COOLDOWN_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" title="{$ADM_PRODUCT_EDIT_COOLDOWN_ERROR}" alt="Fehler-Bildchen mit hover" />{/if}
						</div>{if $ADM_PRODUCT_EDIT_COOLDOWN_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_COOLDOWN_INFO}</div></div>{/if}
					</div>
					<div id="lifetime1" class="def">
						<div>{$ADM_PRODUCT_EDIT_COMMANDS_END}</div>
						<div{if $ADM_PRODUCT_EDIT_COMMAND_END_ERROR} class="error"{/if}>
							<table>
								{foreach $ADM_PRODUCT_EDIT_END_COMMANDS_VALUE item=endCommand}<tr{if $endCommand.2 == 1} class="success"{elseif $endCommand.2 == 2} class="error"{/if}>
									<td style="width:100%"><input type="text" name="customCommandsEnd[{$endCommand.0}]" value="{$endCommand.1}" class="common_width" /></td>
									<td><input type="submit" name="customCommandEndDeleteButton[{$endCommand.0}]" value="" title="{$ADM_PRODUCT_EDIT_DELETE_COMMAND}" class="smallImage delete" /></td>
									<td><input type="submit" name="customCommandEndTestButton[{$endCommand.0}]" value="" title="{$ADM_PRODUCT_EDIT_TEST_COMMAND}" class="smallImage test" /></td>
								</tr>{/foreach}
							</table>
							<input type="submit" name="customCommandEndAddButton" value="+" class="smallImage add right" title="{$ADM_PRODUCT_EDIT_COMMAND_NEW}" />
							{if $ADM_PRODUCT_EDIT_COMMAND_END_ERROR}<img class="error" src="{$MAIN_URL}templates/{$TEMPLATE}/images/error.png" title="{$ADM_PRODUCT_EDIT_COMMAND_END_ERROR}" alt="Fehler-Bildchen mit hover" />{/if}
						</div>{if $ADM_PRODUCT_EDIT_COMMANDS_END_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_COMMANDS_END_INFO}</div></div>{/if}
					</div>
					<div id="lifetime2" class="def">
						<div>{$ADM_PRODUCT_EDIT_COOLDOWN_NEEDS_PLAYER}</div>
						<div>
							<input type="checkbox" name="cooldownNeedsPlayer"{if $ADM_PRODUCT_EDIT_COOLDOWN_NEEDS_PLAYER_VALUE} checked="checked"{/if} />
						</div>{if $ADM_PRODUCT_EDIT_COOLDOWN_NEEDS_PLAYER_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_COOLDOWN_NEEDS_PLAYER_INFO}</div></div>{/if}
					</div>
					<div id="lifetime3" class="def">
						<div>{$ADM_PRODUCT_EDIT_DISABLE_DURING_COOLDOWN}</div>
						<div><input type="checkbox" name="disableDuringCooldown"{if $ADM_PRODUCT_EDIT_DISABLE_DURING_COOLDOWN_VALUE} checked="checked"{/if} /></div>
						{if $ADM_PRODUCT_EDIT_DISABLE_DURING_COOLDOWN_INFO}<div class="info_box"><div>{$ADM_PRODUCT_EDIT_DISABLE_DURING_COOLDOWN_INFO}</div></div>{/if}
					</div>
				</div>
			</li>
			<li class="last">
				<a href="?show=Products" class="button left">{$BACK}</a>
				<input type="submit" value="{$ADM_PRODUCT_EDIT_SAVE}" name="save_button" class="right" />
			</li>
		</ul>
		</form>
	{else}
		<p>{$ADM_PRODUCT_EDITED_INFO}</p>
		<p><a href="?show=Products" class="button">{$BACK}</a></p>
	{/if}
	</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}