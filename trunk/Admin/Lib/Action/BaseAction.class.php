<?php

class IndexAction extends Action
{
	public $userId;

	public function _initialize()
	{		
		if(!Session::get(C('ADMIN_AUTH_KEY')))
		{
			$this->redirect('signin','Public');
		}
		
		if(empty($this->userId))
		{
			$this->userId = Session::get(C('ADMIN_AUTH_KEY'));
		}
		
		$this->assign('userId',$this->userId);
	}
	
	public function __destruct() {}
}

?>