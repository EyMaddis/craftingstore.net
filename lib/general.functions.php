<?php

#region replace
function replace_arr_val($search, $replacement, $subject)
{
	$return = array();
	foreach($subject as $key => $value)
	{
		if(is_array($value))
		{
			$return[$key] = replace_arr_val($search, $replacement, $value);
		}
		else
		{
			$return[$key] = str_replace($search, $replacement, $value);
		}
	}
	return $return;
}

function replace_val($search, $replacement, $subject)
{
	if(is_array($subject))
	{
		return replace_arr_val($search, $replacement, $subject);
	}
	else
	{
		return str_replace($search, $replacement, $subject);
	}
}
#end
#region find
function find_arr_val($search, $subject, &$out, $hierachical = true, $limit = 0)
{
	if(!isset($out)) $out = array();

	$return = 0;
	foreach($subject as $key => $value)
	{
		if($limit > 0 && $return >= $limit) continue;
		if(is_array($value))
		{
			if($hierachical)
			{
				$return += find_arr_val($search, $value, $_out, $hierachical, $limit);
				$out[$key] = $_out;
			}
			else $return += find_arr_val($search, $value, $out, $hierachical, $limit);
		}
		else
		{
			$return += preg_match_all($search, $value, $_out);
			if($hierachical) $out[$key] = $_out;
			else $out[] = $_out;
		}
	}
	return $return;
}
function find_val($search, $subject, &$out, $hierachical = true, $limit = 0)
{
	$out = array();
	if(is_array($subject))
		//Wenn $out ein Array ist, werden alle gefundenen Ergebnisse in derselben Hierachie zurückgegeben, wie sie gefunden wurden
		return find_arr_val($search, $subject, $out, $hierachical, $limit);
	else return preg_match_all($search, $subject, $out);
}

#end

#region function CurrencyFormatted($value)
# Formatiert den angegebenen Geldbetrag ohne Währungssymbol
function CurrencyFormatted($value)
{
	$formatted = sprintf("%01.2f", $value);
	if(substr($formatted,strlen($formatted)-2) === "00")
	{
		$formatted = substr($formatted,0,strlen($formatted)-3);
	}
	elseif($_SESSION['Index']->say('COMMA') != '.')
	{
		$formatted = str_replace('.',$_SESSION['Index']->say('COMMA'),$formatted);
	}
	return $formatted;
}
#end

#region function array_map_recursive($function, $array)
function array_map_recursive($fn, $ar)
{
	$ra = array();
	foreach($ar as $k => $v)
	{
		$ra[$k] = is_array($v)
		? array_map_recursive($fn, $v)
		: $fn($v);
	}
	return $ra;
}
#end

#region function setLocation($params = null, $host = null, $forceHttps = false)
//Ruft für die aktuelle Subdomain die mit params angegebene Seite auf
function setLocation($params = null, $host = null, $useHttps = null)
{
	$protocol = "http";

	if($useHttps === true || ($useHttps === null && $_SERVER['HTTPS'])){
		$protocol = "https";
	}

	if($params === null) $params = $_SERVER['REQUEST_URI'];
	if($host === null) $host = $_SERVER['HTTP_HOST'];

	if($params[0] != null#mindestens 1 Zeichen lang
		&& ($params[0] == '\\' || $params[0] == '/'))#erstes Zeichen / oder \
	{
		$params = substr($params,1);#erstes Zeichen entfernen
	}
	header('Location: '.$protocol.'://'.$host.'/'.$params);
	die();
}
#end

#region isNumber($value, $acceptzero = false)
/*
	Prüft, ob der Wert $value ausschließlich aus Ziffern besteht
	$acceptzero gibt an, ob auch "0" als Zahl erlaubt ist, oder nicht
*/
function isNumber($value, $acceptzero = false, $acceptNegative = false)
{
	if($acceptzero)
	{
		if($acceptNegative){
			//nur Ziffern eingegeben, ggf. mit vorgestelltem Minus
			return preg_match('/^-?\d+$/', $value);
		}
		else{
			//nur Ziffern eingegeben
			return preg_match('/^\d+$/', $value);
		}
	}
	else
	{
		if($acceptNegative){
			//nur Ziffern, wobei die Zahl nicht nur aus nullen bestehen darf, ggf. mit vorgestelltem Minus
			return preg_match('/^-?([0]*)([1-9]{1}\d*)$/', $value);
		}
		else{
			//nur Ziffern, wobei die Zahl nicht nur aus nullen bestehen darf
			return preg_match('/^([0]*)([1-9]{1}\d*)$/', $value);
		}
	}
}
#end

#region setError($message, $file, $line)
/*
	Setzt eien Fehlermeldung und bricht die Ausführung des Scripts ab
*/
function setError($message, $file, $line = null)
{
	error_log("Error while creating new shop: (".$file.($line ? " in line ".$line : "")."): ".$message);
}
#end

#region function getCurrentSubdomain()
function getCurrentSubdomain($fqdn = null)
{
	if($fqdn === null) $fqdn = $_SERVER['HTTP_HOST'];
	preg_match('/(.*)(?=\.'.str_replace('.','\\.',BASE_DOMAIN).')/i', $fqdn, $matches);
	return $matches[0];
}
#end

#region function random_string_by_length($length)
//generates a random string from A-Z and 0-9 of input length
function random_string_by_length($length)
{
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$count = strlen($chars) - 1;
	$rand = '';
	for($i=0; $i<$length; $i++)
	{
		$rand .= $chars[rand(0, $count)];
	}
	return $rand;
}
#end

#region function random_number_by_length($length)
//generates a random number with length digits
function random_number_by_length($length)
{
	$rand = '';
	for($i=0; $i<$length; $i++)
	{
		$rand .= rand(0, 9);
	}
	return $rand;
}
#end

#region passwort-Krams
#region public static function bcrypt_encode($email, $password, $rounds = "10")
# siehe http://www.phpgangsta.de/schoener-hashen-mit-bcrypt
# Bildet rundenbasiert einen Hashwert aus einer E-Mail-Adresse und einem Passwort
# incrementieren der Runden um 1 verdoppelt die Ausführungsdauer
function bcrypt_encode($email, $password, $rounds = "10")
{
	$string = hash_hmac("whirlpool", str_pad($password, strlen($password)*4, sha1($email), STR_PAD_BOTH), SALT, true);
	$salt = substr(str_shuffle('./0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 22);
	return crypt($string, '$2a$'.$rounds.'$'.$salt);
}
#end

#region public static function bcrypt_check($email, $password, $stored)
# Überprüft, ob die E-Mail-Adresse und das Password denselben Hashwert ergeben, wie der übergebene
function bcrypt_check($email, $password, $stored)
{
	if(!$email || !$password || !$stored) return false;
	$string = hash_hmac("whirlpool", str_pad($password, strlen($password)*4, sha1($email), STR_PAD_BOTH), SALT, true);
	return crypt($string, substr($stored, 0, 30)) == $stored;
}
#end
#end

#region Datenbank
#region startTransaction
function startTransaction($lock = null, $db = null){
	if($db == null) $db = $_SESSION['Index']->db;
	$db->query('SET autocommit=0');
	if($lock !== null)
		$db->query('LOCK TABLES '.$lock);
}
#end
#region commit
function commit($db = null){
	if($db == null) $db = $_SESSION['Index']->db;
	$db->query('COMMIT');
	$db->query('UNLOCK TABLES');
	$db->query('SET autocommit=1');
}
#end
#region rollback
function rollback($db = null){
	if($db == null) $db = $_SESSION['Index']->db;
	$return = mysql_error();
	$db->query('ROLLBACK');
	$db->query('UNLOCK TABLES');
	$db->query("SET autocommit=1");
	return $return;
}
#end
#end

#region isValidIp($ip)
function isValidIp($ip){
	return false !== @inet_pton($ip);
}
#end
#region isValidHostname($hostname)
function isValidHostname($hostname){
	return preg_match("/^(([a-z0-9]|[a-z0-9][a-zA-Z0-9\-]*[a-z0-9])\.)+([a-z0-9]|[a-z0-9][a-zA-Z0-9\-]*[a-z0-9])+$/i",$hostname) == 1;
}
function isInvalidHostnameOrIp($string){
	if (isValidIp($string) || isValidHostname($string)) return false;
	return true;
}

function doSleep($startTime, $minTime){
	$rest = $minTime - microtime(1)+$startTime;
	if($rest > 0)
		usleep($rest*1000000);
}
#end
#region SecondsToTimeString
function secondsToTimeString($s){
	if($s >= 3600){
		$time = floor($s/3600);
		if($time == 1)
			$time .= ' '.$_SESSION['Index']->say('TIME_HOUR');
		else
			$time .= ' '.$_SESSION['Index']->say('TIME_HOURS');
	}
	elseif($s >= 60){
		$time = floor($s/60);
		if($time == 1)
			$time .= ' '.$_SESSION['Index']->say('TIME_MINUTE');
		else
			$time .= ' '.$_SESSION['Index']->say('TIME_MINUTES');
	}
	else{
		$time = $s;
		if($time == 1)
			$time .= ' '.$_SESSION['Index']->say('TIME_SECOND');
		else
			$time .= ' '.$_SESSION['Index']->say('TIME_SECONDS');
	}
	return $time;
}
#end
?>