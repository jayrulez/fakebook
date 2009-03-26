<?php

class BaseAction extends Action
{
	public $userId;
	
	public function _initialize()
	{
		parent::_initialize();

		$this->userId   = Session::get(C('USER_AUTH_KEY'));
		$this->userInfo = Session::get('userInfo');
		/*
		if(empty($this->userId)||empty($this->userInfo))
		{
			$this->redirect('','','login');
		}
		*/
		
		$this->assign('userId',$this->userId);
		$this->assign('userInfo',$this->userInfo);
		$this->assign('username',getUserName($this->userId));
	}
	
	public function __destruct() {}
}

?>