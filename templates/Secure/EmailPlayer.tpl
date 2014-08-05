{include file="AdminStructure.tpl"}
{include file="AdminNavigation.tpl"}
{$main->prepare('EmailPlayer')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_EMAIL_PLAYER_TITLE} {$ADM_EMAIL_PLAYER_PLAYERNAME}</span></div>
	<div class="boxcontent emailplayer">
			<form action="" method="post">
			<p><label for="subject">{$ADM_EMAIL_PLAYER_SUBJECT}<span style="font-size: 0.9em; color: red;">*</span></p>
			<p></label><input type="text" name="subject" /></p>
			<p><label for="text" height="500" width="500">{$ADM_EMAIL_PLAYER_MESSAGE}<span style="font-size: 0.9em; color: red;">*</span> </label></p>
			<p>
				 <textarea name="text"></textarea>
			</p>
			<p>
				 <input type="submit" name="submit" value="{$ADM_EMAIL_PLAYER_SEND}" />
			</p>
			</form>
	</div>
</div>
{include file="AdminNavigation.tpl"}
{include file="AdminStructure.tpl"}