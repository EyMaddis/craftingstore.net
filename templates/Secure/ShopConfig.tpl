{$main->prepare('ShopConfig')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_CONTENT_SHOP_CONFIG_TITLE}</span></div>
	<div class="boxcontent">
		<form method="post" enctype="multipart/form-data" action="">
			<input type="hidden" name="MAX_FILE_SIZE" value="{$ADM_CONTENT_LOGO_MAX_SIZE}" />
			<table class="alternating">
				<tr>
					<td>{$ADM_CONTENT_STARTING_CREDIT}</td>
					<td><input type="text" name="starting_credit" style="text-align:right;width:40px;" value="{$ADM_CONTENT_STARTING_CREDIT_VALUE}" />{$POINTSYSTEM}</td>
				</tr>
				<tr>
					<td>{$ADM_CONTENT_LOGO_TITLE}</td>
					<td>{if $ADM_CONTENT_LOGO_MESSAGE != -1}<div class="extraInfo">{$ADM_CONTENT_LOGO_MESSAGE}</div>{/if}<input type="file" name="image" /><img width="32px" height="32px" class="help" alt="Info" title="{$ADM_CONTENT_LOGO_INFO}" src="{$MAIN_URL}templates/{$TEMPLATE}/images/info.png"></td>
				</tr>
				<tr>
					<td>{$ADM_CONTENT_HOSTNAME_TITLE}</td>
					<td><input type="text" name="custom_domain" value="{$ADM_CONTENT_HOSTNAME_VALUE}" /><img width="32px" height="32px" class="help" alt="Info" title="{$ADM_CONTENT_HOSTNAME_DESCRIPTION}" src="{$MAIN_URL}templates/{$TEMPLATE}/images/info.png"></td>
				</tr>
				<tr>
					<td><input type="reset" value="{$ADM_CONTENT_SHOP_CONFIG_CANCEL}" /></td>
					<td><input name="submit" type="submit" value="{$ADM_CONTENT_SHOP_CONFIG_SUBMIT}" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>