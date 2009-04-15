<?php

class HomeAction extends BaseAction
{
	public function index()
	{
		if(empty($this->userId))
		{
			$this->redirect('','','index');
		}
		
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>