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
		$language = Cookie::get('language');
		$this->assign('language',$language);
		
		$this->display();
	}

	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>