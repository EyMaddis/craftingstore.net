<?php
defined('_MCSHOP') or die("Security block!");

class CustomCSS extends aDisplayable
{
	public function prepareDisplay()
	{
		$templateId = 0;
		if (isset($_POST['css']))
		{
			$css = mysql_real_escape_string(strip_tags($_POST['css']));
			$sql = "SELECT COUNT(*) FROM mc_customcss WHERE `ShopId` = '".$_SESSION['Index']->adminShop->getId()."' AND `TemplateId` = '".$templateId."'";
			$count = $_SESSION['Index']->db->fetchOne($sql);
			if ($count < 1) {
				$sql = "INSERT INTO mc_customcss (`ShopId`, `TemplateId`, `Css`) VALUES ('".$_SESSION['Index']->adminShop->getId()."', '".$templateId."', '".$css."')";
				$_SESSION['Index']->db->query($sql);
			}
			else
			{
				$sql = "UPDATE mc_customcss SET `Css` = '".$css."' WHERE `ShopId` = '".$_SESSION['Index']->adminShop->getId()."' AND `TemplateId` = '".$templateId."' LIMIT 1";
				$_SESSION['Index']->db->query($sql);
			}
		}
		
		$sql = "SELECT `Css` FROM mc_customcss WHERE `ShopId` = '".$_SESSION['Index']->adminShop->getId()."' AND `TemplateId` = '".$templateId."' LIMIT 1"; 
		$css = $_SESSION['Index']->db->fetchOne($sql);
		
		
		// language and text assossiactions
		$_SESSION['Index']->assign_say('ADM_DESIGN_CUSTOMCSS_TITLE');
		$_SESSION['Index']->assign_say('ADM_DESIGN_CUSTOMCSS_INFO');
		$_SESSION['Index']->assign_say('ADM_DESIGN_CUSTOMCSS_SAVE');
		$_SESSION['Index']->assign('ADM_DESIGN_CUSTOMCSS_CSS', $css);
	}
}

?>