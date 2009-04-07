<?php

class AccountAction extends BaseAction
{
	public function index()
	{
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>