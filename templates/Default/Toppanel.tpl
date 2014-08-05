{$main->prepare('Toppanel')}
<div class="toppanel">

<div class="panel">
	<div class="content clearfix">
<table>
	<tr>
		{if $TOP_LOGGEDIN}{* Für den Fall, dass der Nutzer eingeloggt ist *}
		<td class="left_box">
			<div>
				<div>
				<h3>{$LOGIN_SERVER_OWNERS}</h3>
				<p>{$LOGIN_SERVER_OWNERS_DESCRIPTION1}<br />{$LOGIN_SERVER_OWNERS_DESCRIPTION2} <a href="{$SECURE_URL}?show=RegisterAdmin&amp;setLang={$LANG}">{$LOGIN_REGISTER_NOW}</a></p>
			</div>
			</div>
		</td>
		<td class="center_box">
			<div>
				<h3>{$LOGIN_WELCOME}</h3>
				<p>{$LOGIN_WELCOME_DESCRIPTION} <a href="http://{$BASE_URL}" target="_blank">{$LOGIN_WELCOME_LINK}</a></p>
			</div>
		</td>
		<td class="right_box">
			<div>
				<h3>{$CONTROL_CENTER_HEADER}</h3>
				<p><a href="{$PROFILE_URL}">{$LOGIN_PROFILE_LABEL}</a></p>
				<p><a href="{$SECURE_URL}{$LOGOUT_URL}">{$LOGIN_LOGOFF_LABEL}</a></p>
			</div>
		</td>
		{else} {* Für den Fall, dass der Nutzer nicht eingeloggt ist *}
		<td class="left_box">
			<div>
				<h3>{$LOGIN_SERVER_OWNERS}</h3>
				<p>{$LOGIN_SERVER_OWNERS_DESCRIPTION1}<br />{$LOGIN_SERVER_OWNERS_DESCRIPTION2} <a href="{$SECURE_URL}?show=RegisterAdmin&amp;setLang={$LANG}">{$LOGIN_REGISTER_NOW}</a></p>
			</div>
		</td>
		<td class="center_box" style="width:250px;">
			<div>
				<h3>{$LOGIN_WELCOME_HEADER}</h3>
				<p>{$LOGIN_WELCOME_DESCRIPTION} <a href="http://{$BASE_URL}" target="_blank">{$LOGIN_WELCOME_LINK}</a></p>
			</div>
		</td>
		<td class="right_box">
			<div class="LoginBox">
				<h3>{$LOGIN_LOGIN_HEADER}</h3>
				<p>{$LOGIN_REGISTER_DESCRIPTION} <a href="{$SECURE_URL}?show=Register&amp;shop={$SHOP_ID}&amp;setLang={$LANG}">{$LOGIN_REGISTER_NOW}</a></p>
				<p>{$LOGIN_LOGIN_DESCRIPTION} <a href="{$SECURE_URL}?show=Login&amp;shop={$SHOP_ID}&amp;setLang={$LANG}">{$LOGIN_LOGIN_NOW}</a></p>
			</div>
		</td>
		{/if}
	</tr>
</table>

	</div>
</div>

<div class="tab">
	<div class="spacer">
		<ul class="login">
			<li class="left">&nbsp;</li>
			<li>{$TOP_GREETS} {$NICKNAME}</li>
			<li class="sep">|</li>
			<li class="toggleLoginPanel">
				<a class="openLoginPanel" href="#">{$TOP_OPEN}</a>
				<a class="closeLoginPanel" href="#" style="display: none;">{$TOP_CLOSE}</a>			
			</li>
			<li class="right">&nbsp;</li>
		</ul>
	</div>
</div>
</div>