<?php

class user
{
	public $userId;
	
	public $db = null;

	public function __construct()
	{
		$this->db = $GLOBALS['db'];
	}

	public function islogged()
	{
		if(cookie::is_set(C('USER_AUTH_KEY')))
		{
			return true;
		}else{
			return false;
		}
	}
	
	public function signin($signinId,$password)
	{

	}
}

?>
