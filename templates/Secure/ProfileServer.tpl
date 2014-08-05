{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('ProfileServer')}
<div class="AdminBox">
<div class="h1"><span>{$ADM_PROFILE_TITLE}</span></div>
<div class="boxcontent">
	<form action="?show=ProfileServer" method="post">
		<table style="width: 500px;" class="alternating">
			<tr>
				<td>{$ADM_PROFILE_NAME}</td>
				<td{if $ADM_PROFILE_ERROR} colspan="2"{/if}>{$ADM_PROFILE_NAME_VALUE}</td>
			</tr>
			<tr>
				<td>{$ADM_PROFILE_MAIL}</td>
				<td{if $ADM_PROFILE_ERROR} colspan="2"{/if}>{$ADM_PROFILE_MAIL_VALUE}</td>
			</tr>
			<tr>
				<td>{$ADM_PROFILE_MINECRAFTNAME}</td>
				<td{if $ADM_PROFILE_ERROR} colspan="2"{/if}>{$ADM_PROFILE_MINECRAFT_NAME_VALUE}</td>
			</tr>
			<tr>
				<td>{$ADM_PROFILE_PAYPAL}</td>
				<td><input type="text" value="{$ADM_PROFILE_PAYPAL_VALUE}" name="paypal" />
					<img src="{$MAIN_URL}/templates/{$TEMPLATE}/images/info.png" title="{$ADM_PROFILE_PAYPAL_INFO}" width="32px" height="32px" alt="Info" style="vertical-align: middle; cursor: help;" />
				</td>
				{if $ADM_PROFILE_ERROR}<td>{$ADM_PROFILE_PAYPAL_ERROR}<input type="hidden" value="{$ADM_PROFILE_PAYPAL_VALUE}" name="paypal_accept" /></td>{/if}
			</tr>
			<tr>
				<td>{$ADM_PROFILE_PW}</td>
				<td><input type="password" name="passwordfirst" /><img src="{$MAIN_URL}/templates/{$TEMPLATE}/images/info.png" title="{$ADM_PROFILE_PW_INFO}" width="32px" height="32px" alt="Info" style="vertical-align: middle; cursor: help;" /></td>
			</tr>
			<tr>
				<td>{$ADM_PROFILE_PWCHANGE}</td>
				<td><input type="password" name="passwordsecond" /><img src="{$MAIN_URL}/templates/{$TEMPLATE}/images/info.png" title="{$ADM_PROFILE_PW_INFO}" width="32px" height="32px" alt="Info" style="vertical-align: middle; cursor: help;" /></td>
			</tr>
			<tr>
				<td><input type="reset" value="Abbrechen" /></td>
				<td><input type="submit" value="{$ADM_PROFILE_SUBMIT}" /></td>
			</tr>
		</table>
	</form>
</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}