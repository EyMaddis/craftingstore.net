{include file="CenterStructure.tpl"}
{$main->prepare('CreateShop')}
<div class="h1"><span>{$ADM_SERVSET_NEWSERV_TITLE}</span> <a href="{$ADM_SERVSET_NEWSERV_FAQ_LINK}" target="_blank" style="float: left; margin-top:-5px;"><img src="{$MAIN_URL}/templates/{$TEMPLATE}/images/info.png" /></a></div>
<div class="inner"> 
    
    
<table id="assistent"><tr>
	<td style="vertical-align: top; width: 280px;">
		<div class="assistent-left">
			<div{if $ADM_SERVSET_NEWSERV_STEP == 0} class="assistent-selected"{/if}>{$ADM_SERVSET_NEWSERV_REQUIREMENTS_LABEL}</div>
			<div{if $ADM_SERVSET_NEWSERV_STEP == 1} class="assistent-selected"{/if}>{$ADM_SERVSET_NEWSERV_SETUPSERVER_LABEL}</div>
			<div{if $ADM_SERVSET_NEWSERV_STEP == 2} class="assistent-selected"{/if}>{$ADM_SERVSET_NEWSERV_VERIFYDATA_LABEL}</div>
			<div{if $ADM_SERVSET_NEWSERV_STEP == 3} class="assistent-selected"{/if}>{$ADM_SERVSET_NEWSERV_SHOPSETTINGS_LABEL}</div>
			<div{if $ADM_SERVSET_NEWSERV_STEP > 3} class="assistent-selected"{/if}>{$ADM_SERVSET_NEWSERV_COMPLETE_LABEL}</div>
		</div>
	</td>
	<td style="vertical-align:top;">
		<div class="assistent-right">
	{if $ADM_ERROR}{$ADM_ERROR}{else}
    <span style="color:red; font-weight: bold;">{$ADM_SERVSET_ERROR_DIRECT}</span>
	{if $ADM_SERVSET_NEWSERV_STEP == 0}
            
            <div id="setup-instructions">
                <h1>{$ADM_SERVSET_NEWSERV_SETUP_REQUIREMENTS}:</h1>
                <p>
                    <ol>
                        <li>{$ADM_SERVSET_NEWSERV_LATEST_VERSION_BUKKIT} <a href="http://dl.bukkit.org/" target="_blank">Bukkit</a></li>
                        <li>{$ADM_SERVSET_NEWSERV_LATEST_VERSION_JSONAPI} <a href="http://dev.bukkit.org/server-mods/jsonapi/" target="_blank">JSONAPI</a> {$ADM_SERVSET_NEWSERV_JSONAPI_CREDITS}</li>
                        <li>{$ADM_SERVSET_NEWSERV_LATEST_VERSION_PLUGIN} <a href="http://craftingstore.net/download" target="_blank">CraftingStoreConnector Plugin</a></li>
                        <li>{$ADM_SERVSET_NEWSERV_REQUIREMENT_PUBLIC_SERVER}</li>
                    </ol>
                </p>
            </div>

            <div id="jsonapi-question">
                <span>{$ADM_SERVSET_NEWSERV_JSONAPI_INSTALLED_QUESTION}</span>
                <br /><br />
                <form method="post" action="?show=CreateShop">
                    <input type="submit" name="step0generate" value="{$ADM_SERVSET_NEWSERV_JSONAPI_INSTALLED_ANSWER}" /><br /><br />
                    {$OR} <a href="./?show=CreateShop&amp;configmode=setup">{$ADM_SERVSET_NEWSERV_JSONAPI_EXPERT}</a>
                </form>
                
            </div>  
	{elseif $ADM_SERVSET_NEWSERV_STEP == 1}
            {if $ADM_SERVSET_NEWSERV_MODE == "setup"}  
		<form method="post" action="?show=CreateShop">
                    <input name="configmode" value="setup" type="hidden" />
			{if $ADM_SERVSET_INVALID_SERVER}<div class="error">{$ADM_SERVSET_INVALID_SERVER}</div>{/if}
			<div>
				<label for="hostname">{$ADM_SERVSET_NEWSERV_HOSTNAME_LABEL}</label><br />
				<input id="hostname"{if $ADM_SERVSET_ERROR_HOSTNAME} class="error"{/if} type="text" name="hostname" value="{$ADM_SERVSET_NEWSERV_HOSTNAME_SAVE}" />:<input{if $ADM_SERVSET_ERROR_PORT} class="error"{/if} type="text" name="port" value="{$ADM_SERVSET_NEWSERV_PORT_SAVE}" size="2"/>
			</div>
			<div>
				<label for="apiuser">{$ADM_SERVSET_NEWSERV_APIUSER_LABEL}<br />
					<input id="apiuser"{if $ADM_SERVSET_ERROR_APIUSER} class="error"{/if} type="text" name="apiuser" value="{$ADM_SERVSET_NEWSERV_APIUSER_SAVE}" />
				</label>
			</div>
			<div>
				<label for="apipw">{$ADM_SERVSET_NEWSERV_APIPASSWORD_LABEL}<br />
					<input id="apipw" class="left{if $ADM_SERVSET_ERROR_PWR} error{/if}" type="text" name="apipassword" value="{$ADM_SERVSET_NEWSERV_APIPW}" />
					<a href="?show=CreateShop&amp;generatePw=1" class="button" style="margin-top:3px; width:16px; height:16px; padding:3px;"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/assistent/refresh.png" alt="refresh" width="16px;" height="16px" /></a>
				</label>
			</div>
			<div>
				<label for="apisalt">{$ADM_SERVSET_NEWSERV_APISALT_LABEL}
					<img src="{$MAIN_URL}/templates/{$TEMPLATE}/images/info.png" title="{$ADM_SERVSET_NEWSERV_APISALT_INFO}" width="32px" height="32px" alt="Info" style="margin-top: 10px; cursor: help; float:right;" /><br />
					<input id="apisalt" type="text" class="left" name="apisalt" value="{$ADM_SERVSET_NEWSERV_APISALT}" />
					<a href="?show=CreateShop&amp;generateSalt=1" class="button" style="margin-top:3px; width:16px; height:16px; padding:3px;"><img src="{$MAIN_URL}templates/{$TEMPLATE}/images/assistent/refresh.png" alt="refresh" width="16px;" height="16px" /></a>
				</label>
			</div>
			<div>{if $ADM_SERVSET_CANCEL}<a href="?show=CreateShop&amp;back=1" style="float:right" class="button">{$BACK}</a>{/if}
				<input type="submit" value="{$ADM_SERVSET_NEWSERV_SUBMIT_LABEL}" />
			</div>
                        
                        
                        
		</form>
            {else}
                            {$ADM_SERVSET_NEWSERV_CONFIG_GENERATION_INFO}:<br /><br />
                            <div style="text-align: center"><a href="{$ADM_NEWSERVER_DOWNLOAD_CONFIG_URL}" target="_blank">{$ADM_SERVSET_NEWSERV_DOWNLOAD_CONFIG}<img src="./templates/Secure/images/download.png" align="center" /></a></div>
                            <br /> <br />
                            <b>{$ADM_SERVSET_NEWSERV_STEPLIST}:</b>
                            <ol>
                                <li>{$ADM_SERVSET_NEWSERV_STEPLIST_STOP}</li>
                                <li>{$ADM_SERVSET_NEWSERV_STEPLIST_DOWNLOAD_JSONAPI}</li>
                                <li>{$ADM_SERVSET_NEWSERV_STEPLIST_COPY_CONFIG} <i><b>/plugins/jsonapi/</i></b> {$ADM_SERVSET_NEWSERV_STEPLIST_COPY_CONFIG_OVERWRITE}</li>
                                <li>{$ADM_SERVSET_NEWSERV_STEPLIST_RESTART}</li>
                                <li>{$ADM_SERVSET_NEWSERV_STEPLIST_ENTER_IP}</li>
                                <li>{$ADM_SERVSET_NEWSERV_STEPLIST_VERIFY}:</li>
                            </ol>
                                <br />
                                        {if $ADM_SERVSET_INVALID_SERVER}<div class="error">{$ADM_SERVSET_INVALID_SERVER}</div>{/if}
                                <div>
                                    <form method="post" action="./?show=CreateShop">
                                        <label for="hostname">{$ADM_SERVSET_NEWSERV_GENERATED_HOSTNAME_LABEL}</label><br />
                                        <input placeholder="123.123.123.123" id="hostname"{if $ADM_SERVSET_ERROR_HOSTNAME} class="error"{/if} type="text" name="hostname" value="{$ADM_SERVSET_NEWSERV_GENERATE_HOSTNAME_SAVE}" />
                                        <input type="submit" value="{$ADM_SERVSET_NEWSERV_VERIFY}" />
                                    </form>
                                </div>
                                <br /><br />
                            <form method="get" action="./">
                                <input type="hidden" name="show" value="CreateShop" />
                                <input type="hidden" name="back" value="1" />
                                <input type="submit" value="{$BACK}" />
                            </form>
                     
            {/if}
	{else if $ADM_SERVSET_NEWSERV_STEP == 2}
		<form method="post" action="?show=CreateShop">
			<div style="font-size:8pt;">{$ADM_SERVSET_NEWSERV_INFO}</div>
		{if $ADM_SERVSET_NEWSERV_CODESEND_ERROR}
			<div class="error">{$ADM_SERVSET_NEWSERV_CODESEND_ERROR}</div>
		{else}
			{if $ADM_SERVSET_NEWSERV_CODESEND_SUCCESSFUL}
				<div style="color:#090">{$ADM_SERVSET_NEWSERV_CODESEND_SUCCESSFUL}</div>
			{/if}
			{if $ADM_SERVSET_CODESEND}
				<div>
					<label for="verifycode">
					{if $ADM_SERVSET_NEWSERV_WRONGCODE}
						<span class="error">{$ADM_SERVSET_NEWSERV_WRONGCODE}</span>
					{else}
						{$ADM_SERVSET_NEWSERV_VERIFYINGCODE_LABEL}
					{/if}
					</label><br />
					<input{if $ADM_SERVSET_NEWSERV_WRONGCODE} class="error"{/if} type="text" name="verifycode" id="verifycode" />
					<input type="submit" value="{$ADM_SERVSET_NEWSERV_SUBMIT_LABEL}" />
				</div>
				<div>{$ADM_SERVSET_NEWSERV_NEWCODE_INFO}<a href="?show=CreateShop&amp;newcode=1" class="button">{$ADM_SERVSET_NEWSERV_NEWCODE_LABEL}</a>
				</div>
			{else}
				<div>
					{$ADM_SERVSET_NEWSERV_SENDNEWCODE_LABEL}
						<a href="?show=CreateShop&amp;newcode=1" class="button">{$ADM_SERVSET_NEWSERV_NEWCODE_LABEL}</a>
				</div>
			{/if}
		{/if}
			<div><a href="?show=CreateShop&amp;back=1" class="button left">{$ADM_SERVSET_NEWSERV_EDITDATA_LABEL}</a></div>
		</form>
	{else if $ADM_SERVSET_NEWSERV_STEP == 3}
		<form method="post" action="?show=CreateShop">
			<div>
				<label for="serverlabel">{$ADM_SERVSET_NEWSERV_SERVERLABEL_LABEL}</label><br />
				<label for="serverlabel" class="smallInfo">{$ADM_SERVSET_NEWSERV_SERVERLABEL_LABEL_INFO}</label><br />
				<input{if $ADM_SERVSET_ERROR_SERVERLABEL} class="error"{/if} type="text" name="serverlabel" id="serverlabel" value="{$ADM_SERVSET_NEWSERV_SERVERLABEL}" />
			</div>
			<div>
				<label for="subdomain">{$ADM_SERVSET_NEWSERV_SUBDOMAIN_LABEL}</label><br />
				<label for="subdomain" class="smallInfo">{$ADM_SERVSET_NEWSERV_SUBDOMAIN_LABEL_INFO}</label><br />
				http://<input{if $ADM_SERVSET_ERROR_SUBDOMAIN} class="error"{/if} type="text" name="subdomain" id="subdomain" value="{$ADM_SERVSET_NEWSERV_SUBDOMAIN}" style="width:auto;"/>.craftingstore.net<br />
				{if $ADM_SERVSET_ERROR_SUBDOMAIN}<br /><span style="color: red;">{$ADM_SERVSET_ERROR_SUBDOMAIN}</span>{/if}
			</div>
			<!--<div>
				<label for="template">{$ADM_SERVSET_NEWSERV_TEMPLATE_LABEL}</label><br />
				<select name="template" id="template">
				{foreach $ADM_SERVSET_NEWSERV_TEMPLATES item=templates}
					<option value="{$templates.id}"{if $ADM_SERVSET_NEWSERV_TEMPLATE == $templates.id} selected="selected"{/if}>{$templates.name}</option>
				{/foreach}
				</select>
			</div>-->
			<div>
				<a href="?show=CreateShop&amp;back=1" class="button left">{$ADM_SERVSET_NEWSERV_EDITDATA_LABEL}</a>
				<input type="submit" value="{$ADM_SERVSET_NEWSERV_SUBMIT_LABEL}" />
			</div>
		</form>
	{else}
		
		<div style="text-align:center;"><h3>{$ADM_SERVSET_NEWSERV_FINISH_CONFIG_HEADER}</h3>
             <p>
                {$ADM_SERVSET_NEWSERV_FINISH_CONFIG}
             </p>
            
        </div>
        <div>
			<h2 style="text-align: center; color: #090;">{$ADM_SERVSET_NEWSERV_CONGRATULATIONS}</h2>
		</div>
		<div style="text-align:center;">
			<img src="{$MAIN_URL}/templates/{$TEMPLATE}/images/assistent/success.png" alt="success" /><br />
			<p>{$ADM_SERVSET_NEWSERV_SUCCESSMESSAGE}</p>
		</div>
		<div style="text-align:center">
			<a href="?show=ServerSettings" class="button">{$ADM_SERVSET_NEWSERV_TO_ADMINISTRATION}</a>
		</div>
	{/if}{/if}
		</div>
	</td>
</tr></table>
                


</div>
{include file="CenterStructure.tpl"}