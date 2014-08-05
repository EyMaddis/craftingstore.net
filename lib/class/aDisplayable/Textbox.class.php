<?php
/************************
**
** File:	 	textBox.class.php
**	Author: 	Rasmus Epha
**	Date:		08.01.2012
**	Desc:		Eine Box, die einen beliebigen Text aufnehmen kann
**
*************************/
defined('_MCSHOP') or die("Security block!");


class Textbox extends aContentBox
{
	public $title = "Überschrift";
	public $content = "Hier steht ein beliebiger Text";

	public function prepareDisplay()
	{
		$_SESSION['Index']->assign('TEXTBOX_TITLE', $this->title);
		$_SESSION['Index']->assign('TEXTBOX_CONTENT', $this->content);
	}
}

?>