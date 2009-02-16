<?php

class profile
{
	public $userId;
	
	public $db = null;
	public $user = null;

    static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }
	
	public function __construct()
	{
		$this->db   = $GLOBALS['db'];
		$this->user = $GLOBALS['user'];

		//$this->db   = db::getInstance();
		//$this->user = user::getInstance();
		//$this->userId = $this->user->getId();
	}
}

?>
