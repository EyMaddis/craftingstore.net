<?php

function smarty_function_contentBoxes($params, &$smarty)
{
	return null;
	#user_func_callback($params[''])
	return "<div>".$params['nr']->getHtml()."</div>";
}

?>