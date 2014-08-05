<?php
defined('_MCSHOP') or die("Security block!");
class Mysql
{
	private $mysql_link = null;
	public function connect()
	{
		$this->mysql_link = @mysql_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD) or die('Error establishing database connection!');
		@mysql_select_db(SQL_DB) or die("Could not select database!");
		mysql_query("SET NAMES 'utf8'");
		return $this->mysql_link;
	}

	public function query($query)
	{
		return mysql_query($query);
	}

	public function disconnect()
	{
		mysql_close($this->mysql_link);
	}
	public function fetchRow()
	{
		return mysql_fetch_assoc($this->result);
	}

	public function count()
	{
		if($this->counter==NULL && is_resource($this->result))
		{
			$this->counter=mysql_num_rows($this->result);
		}

		return $this->counter;
	}
}

?>