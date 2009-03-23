<?php

Class EmptyAction extends BaseAction{
	
	public function index()
	{
		$this->redirect('','ERROR');
	}
	
}
?>