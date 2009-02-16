<?php

class input
{
	public function __construct()
	{
	
	}
	
	public function db_sanitize($map=array())
	{
	
	}
	
	public function sanitize_login_data($data)
	{
		//sanitize data before return
		return $data;
	}
	
	public function isEmail($string)
	{
		return false;
	}
}

?>