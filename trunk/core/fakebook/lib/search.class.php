<?php

class search
{
	public $userId;
	
	public $db = null;

    static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }
	
	public function __construct()
	{
		$this->db   = $GLOBALS['db'];
	}
}

?>
