<?php

class BaseAction extends Action
{
	public $userId;

	public function _initialize()
	{
	    /*
		if(!Session::get(C('USER_AUTH_KEY')))
		{
			$this->redirect('','login');
		}
		*/
		if(empty($this->userId))
		{
			$this->userId = Session::get(C('USER_AUTH_KEY'));
		}
		
		$this->assign('userId',$this->userId);
	}
	
	public function __destruct() {}
}

?>