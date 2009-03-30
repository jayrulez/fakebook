<?php

class GroupAction extends BaseAction
{
	public function index()
	{
		$gid = (int)$_GET['id'];
		
		if(!$gid || !getGroupInfo($gid))
			$this->redirect('','','home');
			
		$groupInfo = getGroupInfo($gid);
		
		$this->assign('groupInfo',$groupInfo);
		
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>