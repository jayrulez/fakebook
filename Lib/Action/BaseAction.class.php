<?php

class BaseAction extends Action
{
	public $userId;
	
	public function _initialize()
	{
		parent::_initialize();

		$this->userId   = Session::get(C('USER_AUTH_KEY'));
		$this->userInfo = Session::get('userInfo');
		
		if(empty($this->userId))
		{
			$language = Cookie::get('language');
		}
		else
		{
			$language = $this->userInfo['language'];
		}
		
		$this->assign('userId',$this->userId);
		$this->assign('userInfo',$this->userInfo);
		$this->assign('language',$language);
	}
	
	public function __destruct() {}
}

?>