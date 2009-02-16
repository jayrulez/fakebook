<?php

class user
{
	public $userId;
	
	public $db = null;
	
	public $profile = null;
	
	public $user_table;

    static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }
	
	public function __construct()
	{
		$this->db      = $GLOBALS['db'];
		//$this->profile = $GLOBALS['profile'];
		//$this->db         = db::getInstance();
		//$this->profile    = profile::getInstance();
		$this->user_table = C('DB_PREFIX').C('USER_TABLE');
	}

	public function getUserId_handle($handle)
	{
		$sql = "SELECT id FROM {$this->user_table} 
				WHERE email='{$handle}' 
				OR account='{$handle}'
		";
		$userId = $this->db->fetch_array($this->db->query($sql));
		return $userId[0];
	}
	
	public function islogged()
	{
		if(session::get(C('USER_AUTH_KEY')))
		{
			return true;
		}else{
			return false;
		}
	}
	
	public function login($loginId,$loginpwd,$autosignin)
	{
		if(input::isEmail($loginId))
		{
			$handle = 'email';
		}else{
			$handle = 'account';
		}
		$chkHandle = "SELECT COUNT(*) 
					  FROM {$this->user_table} 
					  WHERE {$handle}='{loginId}'
		";
		$countHandle = $this->db->fetch_array($this->db->query($chkHandle));
		if($countHandle[0]>0)
		{
			$chkAccount = "SELECT COUNT(*) 
						   FROM {$this->user_table} 
						   WHERE {$handle}='{$loginId}' 
						   AND password='{$loginPwd}'
			";
			$countAccount = $this->db->fetch_array($this->db->query($chkAccount));
			if($countAccount[0]>0)
			{
				if($this->set_login($loginId,$loginpwd,$autosignin))
				{
					return 0;
				}else{
					return 3;
				}
			}else{
				return 2;
			}
		}else{
			return 1;
		}
	}
	
	public function set_login($loginId,$loginpwd,$autosignin)
	{
		$userId = $this->getUserId_handle($loginId);
		if($autosignin)
		{
			cookie::set('loginId',C('COOKIE_EXPIRE'));
			cookie::set('loginPwd',C('COOKIE_EXPIRE'));
		}
		/*$sql = "SELECT *
				FROM {$this->user_table}
				WHERE id='{$userId}'
		";
		$userInfo = $this->db->fetch_assoc($this->db->query($sql));
		$upUser = "UPDATE {$this->user_table}
				   SET visits=visits+1
				   WHERE id='{$userId}'
		";
		if(session::set('userInfo',$userInfo)&&session::set('userId',$userId))
		{
			return true;
		}else{
			return false;
		}*/
	}
	
	
	
	public function create($account = array())
	{
	
	}
	
	public function remove($userId)
	{
	
	}
}

?>
