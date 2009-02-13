<?php

class db
{
	public $link = false;
	
	public $db   = null;

	public function __construct()
	{
		$dbhost = C('DBHOST');
		$dbname = C('DBNAME');
		$dbuser = C('DBUSER');
		$dbpass = C('DBPASS');
		
		if(!$this->connect($dbhost,$dbname,$dbuser,$dbpass))
		{
			throw_exception(L('_DB_CONNECTION_ERROR_'));
		}
	}
	
	public function connect($dbhost,$dbname,$dbuser,$dbpass)
	{
		$this->link = @mysql_connect($dbhost,$dbuser,$dbpass);
		if(!$this->link)
		{
			return false;
		}else{
			$this->db = @mysql_select_db($dbname);
			if(!$this->db)
			{
				return false;
			}else{
				return true;
			}
		}
	}
	
	public function close($link)
	{
		@mysql_close($link);
	}
	
	public function __destruct()
	{
		$this->close($this->link);
	}
	
	public function query($sql)
	{
		return mysql_query($sql,$this->link);
	}
	
	public function fetch_array($query)
	{
		return mysql_fetch_array($query,$this->link);
	}
	
	public function fetch_assoc($query)
	{
		return mysql_fetch_assoc($query,$this->link);
	}
}

?>
