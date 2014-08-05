<?php
defined('_MCSHOP') or die("Security block!");

class Admin extends aDisplayable
{
	public function prepareDisplay()
	{
		
		function humanTiming ($time){
			$time = time() - $time; // to get the time since that moment
			$tokens = array (
				31536000 => 'YEAR',
				2592000 => 'MONTH',
				604800 => 'WEEK',
				86400 => 'DAY',
				3600 => 'HOUR',
				60 => 'MINUTE',
				1 => 'SECOND'
			);
			$tokens_plural = array (
				31536000 => 'YEARS',
				2592000 => 'MONTHS',
				604800 => 'WEEKS',
				86400 => 'DAYS',
				3600 => 'HOURS',
				60 => 'MINUTES',
				1 => 'SECONDS'
			);
			// detect biggest possible unit
			foreach ($tokens as $unit => $text) {
				if ($time < $unit) continue;
				$numberOfUnits = floor($time / $unit);
				return array($numberOfUnits,(($numberOfUnits>1)?$tokens_plural[$unit]:$text)); // array(number, NameOfUnit)
			}

		}

		$_SESSION['Index']->assign_say("ADM_ADMIN_TITLE");
		$_SESSION['Index']->assign_say("ADM_ADMIN_DESCRIPTION");

		#region QuickMenu
		$quickmenu = array(
			array('name' => $_SESSION['Index']->lang->say("ADM_PRODUCT_ADD_TITLE"),
				'url' => "?show=ProductEdit&item=0"),
			array('name' => $_SESSION['Index']->lang->say("ADM_ITEM_GROUP_NEW"),
				'url' => "?show=ItemGroups&mode=edit&node=0"),
			array('name' => $_SESSION['Index']->lang->say("ADM_PROFILE_TITLE"),
				'url' => "?show=ProfileServer"),
			array('name' => $_SESSION['Index']->lang->say("LOGIN_LOGOFF_LABEL"),
				'url' => "?show=LoginServer&logout=do")
		);
		//foreach($array as $name => $link)

		$_SESSION['Index']->assign_say("ADM_QUICK_MENU_TITLE");
		$_SESSION['Index']->assign("ADM_QUICK_MENU_ELEMENTS", $quickmenu);
		#end



		#region RSS-Reader for news from the main site
		$doc = new DOMDocument();
		if (!$doc->load(NEWS_RSS))
		{
			setError("News-RSS offline!", __FILE__, __LINE__);
			$arrFeeds = false;
		}
		else {
			$arrFeeds = array();
			
			foreach ($doc->getElementsByTagName('item') as $node) {
				$date_arr = humanTiming(strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue));
				$arrFeeds[] = array ( 
				'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
				'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
				'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
				'date' => $_SESSION['Index']->lang->say("TIME_AGO", array($date_arr[0].' '.$_SESSION['Index']->lang->say($date_arr[1])))
				);
				
			}
		}
		
		$_SESSION['Index']->assign_direct("ADM_ADMIN_RSS", $arrFeeds);
		$_SESSION['Index']->assign_say("ADM_ADMIN_RSS_TITLE");
		$_SESSION['Index']->assign_say("ADM_ADMIN_TO_ARTICLE");
		#end
		
		//TODO:

		/*
		if ($loggedin)
		{
			if (!$serverexists)
			{
				header("location: ?show=ServerSettings&a=1
			}
		}	

		Jeder Customer muss einen Server registriert haben, um diesen zu verwalten, klingt logisch oder? ;)
		*/
	}
}
?>