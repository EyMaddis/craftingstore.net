<?php
defined('_MCSHOP') or die("Security block!");

class DisplayControl
{
	private $displayableObjects = array();

	#region public function prepare($name)
	public function prepare($name, array $params = null)
	{
		$classname = DisplayControl::getClassname($name);

		if($this->displayableObjects[$classname])
		{
			if($params != null){
				foreach($params as $key => $value){
					$this->displayableObjects[$classname]->$key = $value;
				}
			}

			$this->displayableObjects[$classname]->prepareDisplay();
			return;
		}

		if(class_exists($classname))
		{
			$this->displayableObjects[$classname] = new $classname();

			if($params != null){
				foreach($params as $key => $value){
					$this->displayableObjects[$classname]->$key = $value;
				}
			}
			$this->prepare($classname);
		}
	}
	#endregion

	#region public static function allowedTemplateList($name)
	public static function allowedTemplateList($name)
	{
		return preg_match('/^{([,a-zA-Z])*}$/', $name);
	}
	#endregion

	#region public function Display($name)
	public function Display($name)
	{
		$classname = DisplayControl::getClassname($name);
		#region normales Objekt anzeigen
		if(class_exists($classname))
		{
			$_SESSION['Index']->smarty->assign('ContentId', DisplayControl::getIndex($name));
			$_SESSION['Index']->smarty->Display($classname.'.tpl');
		}
		#endregion
		/*
		#region Admin-Objekt anzeigen
		elseif(class_exists('Admin'.$classname))
		{
			$_SESSION['Index']->smarty->assign('ContentId', DisplayControl::getIndex($name));
			$_SESSION['Index']->smarty->Display($classname.'.tpl');
		}
		#endregion
		*/
		#region mehrere Objekte anzeigen
		elseif(DisplayControl::allowedTemplateList($classname))
		{
			$_SESSION['Index']->smarty->Display('Content.tpl');
		}
		#endregion
		#region Objekt ist ungültig
		else
		{
			setLocation('404');
			die();
		}
		#endregion
	}
	#endregion

	#region public static function ValidTemplate($name)
	public static function ValidTemplate($tpl_name)
	{
		return class_exists($tpl_name);# || class_exists('Admin'.$tpl_name);
	}
	#endregion

	#region public static function getClassname($name)
	#Gibt für einen gültigen ObjectIdentifier den Klassennamen zurück.
	#Für einen ungültigen Namen kann Müll rauskommen.
	public static function getClassname($name)
	{
		$split = explode('_', $name);
		return $split[0];
	}
	#endregion

	#region public static function getIndex($name)
	#Gibt für einen gültigen ObjectIdentifier die Indexnummer zurück.
	#Liegt der Name im Format <Klassenname> (ohne Indexnummer) vor, wird 0 zurückgegeben.
	#Für einen ungültigen Namen kann Müll rauskommen.
	public static function getIndex($name)
	{
		$split = explode('_', $name);
		return ($split[1] != null ? $split[1] : 0);
	}
	#endregion
	
	#region public function setDefaultSmarty(&$smarty)
	public function setDefaultSmarty(&$smarty)
	{
		#Clear all old Values
		$smarty->clearAllAssign();

		#The Templates can call the prepare-Method with their name
		$smarty->assign('main', $this);

		#Are we using ajax? Tell the Templates!
		$smarty->assign('ajax', $this->useAjax);
	}
	#endregion

	#region public function useAjax($useAjax)
	public function useAjax($useAjax)
	{
		if($this->useAjax == $useAjax)#keine Veränderung
			return;
		$this->useAjax = $useAjax;

		#When changing this option, tell the Templates!
		$_SESSION['Index']->assign('ajax', $this->useAjax);
	}
	#endregion
}
?>