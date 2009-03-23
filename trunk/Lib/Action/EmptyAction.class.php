<?php

class EmptyAction extends BaseAction
{
	
	public function index()
	{
		$this->redirect('','ERROR');
	}
	
}
?>