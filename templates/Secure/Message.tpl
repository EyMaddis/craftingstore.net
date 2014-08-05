{include file="CenterStructure.tpl"}
{$main->prepare('Message')}
<div class="h1"><span>{$MESSAGE_TITLE}</span></div>
<div class="Inner">{if $MESSAGE_SUCCESS}
{$MESSAGE_SUCCESS}<br/><a href="{$MESSAGE_BACK_URL}" class="button">{$MESSAGE_BACK}</a>
{else}{$MESSAGE_DESCRIPTION}
<form action="{$MESSAGE_SUBMIT_URL}" method="POST">
	<table class="alternating set_center">
		<tr>
			<td>{$MESSAGE_MAIL}</td>
			<td><input type="text" name="mail" style="width:90%;"{$MESSAGE_MAIL_VALUE} /></td>
		</tr>
		<tr>
			<td{if $MESSAGE_SUBJECT_ERROR} class="error"{/if}>{$MESSAGE_SUBJECT}</td>
			<td>{if $MESSAGE_SUBJECT_ERROR}<div class="extraInfo">{$MESSAGE_SUBJECT_ERROR}</div>{/if}<input type="text" name="subject" style="width:90%;" value="{$MESSAGE_SUBJECT_VALUE}" /></td>
		</tr>
		<tr>
			<td{if $MESSAGE_CONTENT_ERROR} class="error"{/if}>{$MESSAGE_CONTENT}</td>
			<td>{if $MESSAGE_CONTENT_ERROR}<div class="extraInfo">{$MESSAGE_CONTENT_ERROR}</div>{/if}<textarea name="content" name="content" style="width:90%; height:200px;">{$MESSAGE_CONTENT_VALUE}</textarea></td>
		</tr>
		<tr>
			<td{if $MESSAGE_CAPTCHA_ERROR} class="error"{/if}>{$MESSAGE_CAPTCHA}</td>
			<td>{include file="Captcha.tpl"}{if $MESSAGE_CAPTCHA_ERROR}<div class="extraInfo">{$MESSAGE_CAPTCHA_ERROR}</div>{/if}</td>
		</tr>
		<tr>
			<td><a href="{$MESSAGE_BACK_URL}" class="button">{$MESSAGE_BACK}</a></td>
			<td><input type="submit" value="{$MESSAGE_SUBMIT}"></td>
		</tr>
	</table>
</form>
{/if}
</div>
{include file="CenterStructure.tpl"}