<?php

class GroupAction extends BaseAction
{
	public function index()
	{
		$gid = (int)$_GET['id'];
		
		if(!$gid || !getGroupInfo($gid))
			$this->redirect('','','home');
			
		$groupInfo = getGroupInfo($gid);
		
		$listRows = 5;
		$WallCls = new WallAction;
		$Wall = $WallCls->getWall($gid,$listRows,1);
		$wallSubheader = $WallCls->getWallHeader($Wall['count'],$listRows,$gid,'g');
		
		$this->assign('wall',$Wall);
		$this->assign('wallSubheader',$wallSubheader);
		$this->assign('groupInfo',$groupInfo);
		
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>