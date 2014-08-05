<?php
/************************
**
** File:	 	lang.class.php
**	Author: 	Mathis Neumann
**	Date:		24/05/2011
**	Desc:		class to display text in various languages
**
*************************/
defined('_MCSHOP') or die("Security block!");

class Lang
{
	private $LangId;
	private $LangTag;

	private $Translations = null; // 2-Dimensional Array! $this->Translations[STRING][0] => Text, [1] => boolean: parseBBCode!

	public function __construct($db){
		$languages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$languages[] = DEFAULT_LANGUAGE_TAG;
		foreach($languages as $lang)
		{
			$part = explode(';',$lang);
			if($row = $db->fetchOneRow("SELECT Id,Tag FROM mc_languages WHERE Tag LIKE '".mysql_real_escape_string($part[0])."%' ORDER BY Tag ASC"))
			{
				$this->LangId = $row->Id;
				$this->LangTag = $row->Tag;
				$this->refresh($db);
				return;
			}
		}
	}

	public function say($string, $arguments = null, $specialchars = true){ // uses vsprintf to swap strings like %s or %d to the given data (from array)
		#region Übersetzung aus der Datenbank holen
		if(!$this->Translations[$string]){
			$row = $_SESSION['Index']->db->fetchOneRow("SELECT Label,Translation,parseBBCode FROM mc_translations WHERE Label='".mysql_real_escape_string($string)."' AND LanguagesId='{$this->LangId}' LIMIT 1");
			if($row->Label){
				$this->Translations[$row->Label][0] = $row->Translation;
				$this->Translations[$row->Label][1] = $row->parseBBCode;
				$_SESSION['Index']->db->query("UPDATE mc_translations SET isUsed='1' WHERE Label='{$row->Label}'");
			}
		}
		#end

		$translation = $this->Translations[$string][0];
		$parseBBCode = $this->Translations[$string][1];

		if($translation === null){
			$translation = $string;
		}

		if ($arguments != null){
			$translation = vsprintf($translation, $arguments);
		}
		if($specialchars){
			$translation = htmlspecialchars($translation);
		}
		if($parseBBCode){
			$translation = BBCode::parse($translation);
		}
		return $translation;
	}

	public function getLangId(){
		return $this->LangId;
	}
	public function getLangTag(){
		return $this->LangTag;
	}

	public function setLanguage($Lang){
		$Lang = strtolower($Lang);//ausschließlich Kleinbuchstaben
		if($this->LangId != $Lang && isNumber($Lang)) //$Lang ist eine Nummer
		{
			if($Tag = $_SESSION['Index']->db->fetchOne("SELECT Tag FROM mc_languages WHERE Id='$Lang'")) //Gibt es zu der ID eine Sprache?
			{
				$this->LangId = $Lang;
				$this->LangTag = $Tag;
				$this->refresh($_SESSION['Index']->db);
			}
		}
		//$Lang ist keine Nummer und anders als die aktuelle Sprache
		elseif($this->LangTag != $Lang && $Id = $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_languages WHERE Tag='".mysql_real_escape_string($Lang)."'")) //Gibt es zu der Sprache eine ID?
		{
			$this->LangId = $Id;
			$this->LangTag = $Lang;
			$this->refresh($_SESSION['Index']->db);
		}
	}
	private function refresh($db){ // clear language session and rebuild it
		$this->Translations = null;
		$this->parseBBCodeList = null;
		$this->Translations = array();
		/*
		foreach($db->iterate("SELECT Label,Translation,parseBBCode FROM mc_translations WHERE LanguagesId='{$this->LangId}'") as $row)
		{
			$this->Translations[$row->Label][0] = $row->Translation;
			$this->Translations[$row->Label][1] = $row->parseBBCode;
		}
		*/
	}

	public static function GetDirect($LangId, $string, $arguments = null, $specialchars = true){ // uses vsprintf to swap strings like %s or %d to the given data (from array)
		$translation = $_SESSION['Index']->db->fetchOne(sprintf("SELECT Translation FROM mc_translations WHERE Label='".mysql_real_escape_string($string)."' AND LanguagesId='$LangId' LIMIT 1"));
		if($translation === null)
			$translation = $string;
		//Dies folgenden beiden Zeilen werden irgendwann mal rausfliegen
		elseif(!$_SESSION['Index']->db->fetchOne("SELECT isUsed FROM mc_translations WHERE Label='".mysql_real_escape_string($string)."'"))
			$_SESSION['Index']->db->query("UPDATE mc_translations SET isUsed='1' WHERE Label='".mysql_real_escape_string($string)."'");

		if ($arguments == null)
		{
			if($specialchars)
			{
				$translation = htmlspecialchars($translation);
				if($row->parseBBCode == 1) $translation = BBCode::parse($translation);
					return $translation;
			}
			if($row->parseBBCode == 1) $translation = BBCode::parse($translation);
			return $translation;
		}
		elseif (is_array($arguments))
		{	
			if ($specialchars){
				$translation = htmlspecialchars(vsprintf($translation, $arguments));
			}
			if($row->parseBBCode == 1) $translation = BBCode::parse($translation);
				return $translation;
		}
		else
		{
			setError("Arguments for language method are not given in a valid array!", __FILE__, __LINE__);
			return "Oooops! That was not supposed to happen! ERROR!";
		}
	}
}
?>