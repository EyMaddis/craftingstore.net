<?php
defined('_MCSHOP') or die("Security block!");

class Content extends aDisplayable
{
	public function prepareDisplay()
	{
		if($_GET['content'])
		{
			$names = $_GET['content'];
		}
		else if($_GET['show'])
		{
			$names = $_GET['show'];
		}
		elseif($_SESSION['content'])
		{
			$names = $_SESSION['content'];
		}

		if($names)
		{
			#prüfen, ob $_GET['show'] eine Liste sein müsste, oder nicht
			if(DisplayControl::allowedTemplateList($names))
			{
				$_SESSION['content'] = $names;
				#Es ist eine Liste
				$ContentTemplates = array();
				#Die einzelnen angeforderten Objekte Identifizieren
				$names = explode(',', substr(substr($names, 0, -1), 1));
				$i=0;
				foreach($names as $tpl)
				{
					if(DisplayControl::ValidTemplate($tpl))
					{
						$ContentTemplates[] = array($tpl, $i);
						$i++;
					}
				}
				$_SESSION['Index']->assign('DISPLAYABLE_CONTENT_TEMPLATES', $ContentTemplates);
			}
			elseif(DisplayControl::ValidTemplate($names))
			{
				$_SESSION['content'] = $names;
				$_SESSION['Index']->assign('DISPLAYABLE_CONTENT_TEMPLATES', array(array($names, 0)));
			}
			else
			{
				echo 'Ungültige Parameter: '.$names;
			}
		}
	}
}

?>