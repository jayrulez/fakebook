<?php

class WallAction extends BaseAction
{
	public function index()
	{
		$id = $_GET['id'];
		$page = $_GET['page'];
		$this->assign('id',$id);
		$this->assign('page',$page);
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}

}

?>