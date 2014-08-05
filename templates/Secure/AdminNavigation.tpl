{if !$AdminNavigationPrepared}{$main->useAjax(false)}{$main->prepare('Navigation')}
<div id="Background">
	<div class="NavBottom">
	<table>
		<tr>
			<td>
{if $STRUCTURE_MULTIPLE_SHOPS}
<form action="?show={$STRUCTURE_MULTIPLE_SHOPS_TARGET_PAGE}" method="post">
	<div style="display:inline">Aktueller Server:
		<select name="NAVIGATION_CHANGE_SERVER" onchange="showShopButtons()">{foreach $STRUCTURE_SHOP_LIST item=item}<option value="{$item.Id}"{if $item.Selected} selected="selected"{/if}>{$item.Label} ({$item.Subdomain})</option>{/foreach}</select>
		<input type="submit" value="{$ADM_STRUCTURE_SWITCHSHOP}" id="submit_button" class="reset_display" />
		<input type="reset" value="{$CANCEL}" id="reset_button" onclick="hideShopButtons()" class="reset_display" />
	</div>
</form>
{else}
	Dein Server: <span class="button">{$STRUCTURE_SHOP_LIST.0.Label} ({$STRUCTURE_SHOP_LIST.0.Subdomain})</span>
{/if}
			</td>
			<td class="right"><a href="?show=Account">{$ADM_ACCOUNT_LABEL} {$ADM_ACCOUNT}€</a></td>
		</tr>
	</table>
	</div>
	<div id="OverallWrapper">
		<div id="Header">
			<table id="MainHeader">
				<tr>
					<td style="width:100%">
						<a href="{$NAVIGATION_SHOP_URL}" title="{$NAVIGATION_SHOP_TITLE}"{* target="_blank"*}>
							<img src="{$ADM_STRUCTURE_LOGO}" alt="Logo" class="ShopLogo" />
						</a>
					</td>
					<td>
						<div id="ProfileBox">
							<div><a href="/">Home</a></div>
							<div><a href="?show=LoginServer&amp;logout=do">{$LOGIN_LOGOFF_LABEL}</a></div>
							<div>0 new Messages</div>{if $SHOP_OFFLINE_ERROR}<div class="error" title="{$SHOP_OFFLINE_ERROR_INFO}">{$SHOP_OFFLINE_ERROR}</div>{/if}
						</div>
					</td>
				</tr>
			</table>
		</div>
		<table id="QuickLinks">
			<tr>{foreach $ADM_NAVIGATION item=item}
				<td{if $item.checked} class="clicked"{/if}>
					<a href="?show={$item.target}">
						<span>
							<img alt="" src="{$MAIN_URL}templates/{$TEMPLATE}/images/{$item.img}" /><br />
							{$item.label}
						</span>
					</a>
				</td>{/foreach}
			</tr>
		</table>
		<div id="Content">
{else}
		</div>
		<div id="companyinfowrapper">{$PROJECTNAME} {$FOOTER_VERSION} | &copy; {$COMPANY} | <a href="{$IMPRINT_URL}">{$IMPRINT}</a> | <a href="{$TOS_URL}">{$TERMS_AND_CONDITIONS}</a></div>
	</div>
</div>
{/if}