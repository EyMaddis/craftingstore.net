<?php

function smarty_function_menu($params, &$smarty)
{
	if(!$params['endCloseMulti'])
		$params['endCloseMulti'] = 1;
	$html = '';
	for($i=0; $i<count($params['rows']); $i++)
	{
		$href = '?show={Itembox}&amp;groupId='.$params['rows'][$i]->Id;

		$outputLevel = $params['rows'][$i]->Level+1;
		//Der aktuelle Eintrag hat Kinder
		if($params['rows'][$i]->Level < $params['rows'][$i+1]->Level)
		{
			$html .= sprintf($params['groupHead'], $outputLevel, $href, $params['rows'][$i]->Label);
		}
		//Der aktuelle Eintrag ist auf der obersten Ebene und hat keine Kinder oder ist der Letzte in der Reihe
		elseif(($params['rows'][$i]->Level == 0) && ((!$params['rows'][$i+1]) || ($params['rows'][$i+1]->Level == 0)))
		{
			$html .= sprintf($params['singleLevel1'], $outputLevel, $href, $params['rows'][$i]->Label);
		}
		//Der aktuelle Eintrag hat keine Kinder, aber das nachfolgende Element ist auf gleicher Ebene
		elseif($params['rows'][$i]->Level == $params['rows'][$i+1]->Level)
		{
			if($params['rows'][$i]->Level == 0)
			{
				$html .= sprintf($params['groupHead'].$params['groupHeadClose'], $outputLevel, $href, $params['rows'][$i]->Label);
			}
			else
			{
				$html .= sprintf($params['single'], $outputLevel, $href, $params['rows'][$i]->Label);
			}
		}
		// Der aktuelle Eintrag ist der Letzte in der Reihe
		else
		{
			$html .= sprintf($params['groupLast'], $outputLevel, $href, $params['rows'][$i]->Label);
			$diff =  $params['rows'][$i]->Level - $params['rows'][$i+1]->Level;
			$html .= str_repeat($params['groupHeadClose'], $diff); //Restlichen Einträge schließen
		}
	}
	return $html;
}

?>