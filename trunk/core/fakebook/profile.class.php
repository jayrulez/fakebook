<?php

class profile
{
	public $userId;
	
	public $db = null;

	public function __construct()
	{
		$this->db = $GLOBALS['db'];
	}
}

?>
