<?php

class profile
{
	public $userId;
	
	public $db = null;
	public $user = null;

	public function __construct()
	{
		$this->db   = $GLOBALS['db'];
		$this->user = $GLOBALS['user'];
		//$this->userId = $this->user->getId();
	}
}

?>
