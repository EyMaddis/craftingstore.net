{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('ServerSettings')}
<div class="AdminBox">
<div class="h1"><span>{$ADM_SERVSET_ADDSERVER_BOX_TITLE}</span></div>
<div class="boxcontent">
{if $ADM_SERVSET_UPDATE_SUCCESSFUL}<h3 style="color: green; text-align:center;">{$ADM_SERVSET_UPDATE_SUCCESSFUL}</h3>{/if}
{if $ADM_SERVSET_UPDATE_FAILED}<h3 style="color: red; text-align:center;">{$ADM_SERVSET_UPDATE_FAILED}</h3>{/if}
<form action="?show=ServerSettings" method="post">
	<div><a href="?show=CreateShop&amp;new" class="button" style="float:right">{$ADM_SERVSET_NEWSERV_LABEL}</a><br /></div>
	<table style="width:100%" class="alternating">
		<tr>
			<td>{$ADM_SERVSET_SERVERHOST_LABEL}</td>
			<td><input{if $ADM_SERVSET_ERROR_EDIT_HOSTNAME} class="error"{/if} type="text" name="serverhost" value="{$ADM_SERVSET_SERVERHOST_VALUE}" /></td> 
		</tr>
		<tr>
			<td>{$ADM_SERVSET_SERVERPORT_LABEL}</td>
			<td><input{if $ADM_SERVSET_ERROR_EDIT_PORT} class="error"{/if} type="text" name="serverport" value="{$ADM_SERVSET_SERVERPORT_VALUE}" /></td>
		</tr>
		<tr>
			<td>{$ADM_SERVSET_SERVERUSER_LABEL}</td>
			<td><input{if $ADM_SERVSET_ERROR_EDIT_APIUSER} class="error"{/if} type="text" name="serveruser" value="{$ADM_SERVSET_SERVERUSER_VALUE}" /></td>
		</tr>
		<tr>
			<td>{$ADM_SERVSET_SERVERPASSWORD_LABEL}</td>
			<td><input{if $ADM_SERVSET_ERROR_EDIT_PW} class="error"{/if} type="text" name="serverpassword" value="" /><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/info.png" title="{$ADM_SERVSET_SERVERPASSWORD_INFO}" width="32px" height="32px" alt="Info" style="vertical-align: middle; style: cursor:help;" /></td>
		</tr>
		<tr>
			<td>{$ADM_SERVSET_SERVERPASSWORD_REPEAT_LABEL}</td>
			<td><input{if $ADM_SERVSET_ERROR_EDIT_PW} class="error"{/if} type="text" name="serverpasswordrepeat" value="" /><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/info.png" title="{$ADM_SERVSET_SERVERPASSWORD_INFO}" width="32px" height="32px" alt="Info" style="vertical-align: middle; style: cursor:help;" /></td>
		</tr>
		<tr>
			<td>{$ADM_SERVSET_SERVERSALT_LABEL}</td>
			<td><input readonly="readonly" type="text" name="serversalt" value="{$ADM_SERVSET_SERVERSALT_VALUE}" /><input type="submit" name="generate" value="{$ADM_SERVSET_NEWSERV_GENERATE_LABEL}" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="savechanges" value="{$ADM_SERVSET_EDIT_SUBMIT}" /></td>
		</tr>
	</table>
</form>
</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}