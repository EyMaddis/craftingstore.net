<?php
#Zeigt temporäre hochgeladene Bilder an

session_start();

if(isset($_GET['src']))
{
	if(isset($_SESSION[$_GET['src']]) && isset($_SESSION[$_GET['src']]['imgType']))
	{
		header('Cache-Control: no-cache private');
		header('Content-Type: '.$_SESSION[$_GET['src']]['imgType']);
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.strlen($_SESSION[$_GET['src']]['tmpImg']));
		print($_SESSION[$_GET['src']]['tmpImg']);
	}
}
?>