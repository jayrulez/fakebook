<?php

class GroupAction extends BaseAction
{
	public function index()
	{
		$gid = (int)$_GET['id'];
		
		if(!$gid || !getGroupInfo($gid))
			$this->redirect('','','home');
			
		$groupInfo = getGroupInfo($gid);
		
		$WallCls = new WallAction;
		$Wall = $WallCls->getWall($gid,5,1);
		
		$this->assign('wall',$Wall);
		$this->assign('groupInfo',$groupInfo);
		
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>