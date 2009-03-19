<?php

class IndexAction extends Action
{
	public function _initialize()
	{
		if(Session::get(C('USER_AUTH_KEY')))
		{
			//$this->redirect('','home');
		}
	}

	public function index()
	{
		$this->display();
	}
}

?>