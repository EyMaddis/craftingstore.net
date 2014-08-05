<?php
defined('_MCSHOP') or die("Security block!");

abstract class aDisplayable
{
	protected $name;
	public abstract function prepareDisplay();
	#public abstract function display();
}
?>