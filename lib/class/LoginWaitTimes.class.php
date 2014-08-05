<?php
defined('_MCSHOP') or die("Security block!");

class LoginWaitTimes
{
	public static function getRemainingWaitTime($IsAdmin = false){
		$DetermineWaitTime = new LoginWaitTimes($IsAdmin);
		return $DetermineWaitTime->remainingWaitTime();
	}

	public static function getNextWaitTimeAfterLoginError($IsAdmin = false){
		$DetermineWaitTime = new LoginWaitTimes($IsAdmin);

		$DetermineWaitTime->incrementLoginErrorCount();

		$remainingWaitTime = $DetermineWaitTime->remainingWaitTime();
		if($remainingWaitTime == 0){
			$DetermineWaitTime->removeLoginError();
		}

		return $remainingWaitTime;

		/*
		#region Der User musste die längstmögliche Zeitspanne warten und diese ist abgelaufen
		#Der Eintrag wird aus der Datenbank vollständig gelöscht
		if($login_error_information->Count >= count(LoginWaitTimes::$LOGIN_WAIT_TIMES))
		{
			$this->updateLoginError();
		}
		#endregion
		#region Wenn der User länger als die maximale Wait-Time nicht versucht hat, sich einzuloggen, wird der Eintrag aus der Datenbank gelöscht
		else
		{
			//maximale Wartezeit ermitteln
			$wait_time = LoginWaitTimes::$LOGIN_WAIT_TIMES[count(LoginWaitTimes::$LOGIN_WAIT_TIMES)-1];

			//restliche Wartezeit berechnen
			$wait_time_left = $wait_time + strtotime($login_error_information->Time) - time();
			if($wait_time_left <= 0) //Wenn die Wartezeit abgelaufen ist, wird alles für diese IP zurückgesetzt
			{
				$this->removeLoginError();
			}
			else
			{
				$this->updateLoginError();
			}
		}
		#endregion
		*/
	}

	public static function setLoginValid($IsAdmin = false){
		$DetermineWaitTime = new LoginWaitTimes($IsAdmin);
		$DetermineWaitTime->removeLoginError();
	}

	private function removeLoginError(){
		$_SESSION['Index']->db->query("DELETE FROM mc_loginerrors WHERE IP='{$_SERVER['REMOTE_ADDR']}' AND AdminUser='{$this->IsAdmin}'");
	}

	private function incrementLoginErrorCount(){
		$_SESSION['Index']->db->query("INSERT INTO mc_loginerrors (IP,AdminUser) VALUES ('{$_SERVER['REMOTE_ADDR']}','{$this->IsAdmin}') ON DUPLICATE KEY UPDATE COUNT=COUNT+1");
	}

	private function remainingWaitTime(){
		$login_error_information = $_SESSION['Index']->db->fetchOneRow("SELECT Count, Time FROM mc_loginerrors WHERE IP='{$_SERVER['REMOTE_ADDR']}' AND AdminUser='{$this->IsAdmin}' LIMIT 1");
		if(!$login_error_information) //Keine Daten in der Datenbank vorhanden, also rauslöschen
			return 0;

		return $this->getRemainingTime($login_error_information->Count, $login_error_information->Time);
	}
	private function getRemainingTime($tryNumber, $lastLoginTime){
		if($tryNumber < count(LoginWaitTimes::$LOGIN_WAIT_TIMES)) //Wenn noch Loginversuche erlaubt sind
		{
			$wait_time = LoginWaitTimes::$LOGIN_WAIT_TIMES[$tryNumber - 1];
		}
		else //Das war wohl der letzte Versuch
		{
			$wait_time = LoginWaitTimes::$LOGIN_WAIT_TIMES[count(LoginWaitTimes::$LOGIN_WAIT_TIMES) - 1];
		}
		$remainingWaitTime = $wait_time + $lastLoginTime - time();
		if($remainingWaitTime > 0)
			return $remainingWaitTime;
		return 0;
	}


	private static $LOGIN_WAIT_TIMES = array(0, 0, 0, 5, 10, 15, 600);

	private $nextWaitTime = 0;
	private $IsAdmin = false;

	private function __construct($IsAdmin = false)
	{ if($this->IsAdmin) $IsAdmin = true; }
}
?>