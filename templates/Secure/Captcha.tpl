{$main->prepare('Captcha')}
<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k={$CAPTCHA_KEY}"></script>
<noscript>
	<iframe src="http://www.google.com/recaptcha/api/noscript?k={$CAPTCHA_KEY}" height="300" width="500" frameborder="0"></iframe><br/>
	<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
	<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
</noscript>