<?php

class user
{
	public $userId;
	
	public $db = null;
	
	public $profile = null;
	
	public $user_table;
	
	public function __construct()
	{
		$this->db      = $GLOBALS['db'];
		import('fakebook.lib.profile');
		//$this->profile = $GLOBALS['profile'];
		//$this->db         = db::getInstance();
		$this->profile    = profile::getInstance();
		$this->user_table = C('DB_PREFIX').C('USER_TABLE');
	}

    static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }
	
	public function getUserId_handle($handle)
	{
		$sql = "SELECT id FROM {$this->user_table} 
				WHERE email='{$handle}'
		";
		$userId = $this->db->fetch_array($this->db->query($sql));
		return $userId[0];
	}
	
	public function getUserInfo_userId($userId)
	{
		$sql = "SELECT * FROM {$this->user_table} 
				WHERE id='{$userId}'
		";
		$userInfo = $this->db->fetch_assoc($this->db->query($sql));
		return $userInfo;
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
	
	public function login($loginId,$loginPwd,$autosignin)
	{
		$chkHandle = "SELECT COUNT(*) 
					  FROM {$this->user_table} 
					  WHERE email='{$loginId}'
		";
		$countHandle = $this->db->fetch_array($this->db->query($chkHandle));
		if($countHandle[0]>0)
		{
			$chkAccount = "SELECT COUNT(*) 
                           FROM {$this->user_table} 
                           WHERE email='{$loginId}' 
                           AND password='{$loginPwd}'
			";
			$countAccount = $this->db->fetch_array($this->db->query($chkAccount));
			if($countAccount[0]>0)
			{
				if($this->set_login($loginId,$loginPwd,$autosignin))
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
	
	public function set_login($loginId,$loginPwd,$autosignin)
	{
		$userId = $this->getUserId_handle($loginId);
		if($autosignin)
		{
			cookie::set('loginId',C('COOKIE_EXPIRE'));
			cookie::set('loginPwd',C('COOKIE_EXPIRE'));
		}

		$userInfo = $this->getUserInfo_userId($userId);
		
		if(session::set('userInfo',$userInfo)&&session::set(C('USER_AUTH_KEY'),$userId))
		{
			return true;
		}else{
			return false;
		}
	}
	
	
	
	public function create($account = array())
	{
	
	}
	
	public function remove($userId)
	{
	
	}
}

?>
