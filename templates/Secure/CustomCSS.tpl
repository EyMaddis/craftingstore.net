{$main->prepare('CustomCSS')}
<div class="AdminBox">
	<div class="h1"><span>{$ADM_DESIGN_CUSTOMCSS_TITLE}</span></div>
	<div class="boxcontent customcss">
			<form action="" method="post">
			<p>{$ADM_DESIGN_CUSTOMCSS_INFO}</p>
			<p>
				 <textarea name="css" id="css-editor">{$ADM_DESIGN_CUSTOMCSS_CSS}</textarea>
			</p>
			<p>
				 <input type="submit" rows="50" cols="25" name="submit" value="{$ADM_DESIGN_CUSTOMCSS_SAVE}" />
			</p>
			</form>
	</div>
</div>