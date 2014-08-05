<?php
defined('_MCSHOP') or die("Security block!");
/*
	Hilft zu unterscheiden, ob eine Box im mittleren Bereich angezeigt wird
	und somit beim $main->prepare auch eine Id mitgegeben werden kann
*/
abstract class aContentBox extends aDisplayable
{
	public $id;
}

?>