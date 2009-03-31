<?php

class ProfileAction extends BaseAction
{
	public function index()
	{
		$uid = (int)$_GET['id'];
		
		if(!$uid || $uid == $this->userId)
		{
			$uid = $this->userId;
			$Profile = $this->userInfo;
		}
		else
		{
			$dao = D('User');
			$Profile = $dao->find($uid);
		}
		
		$listRows = 5;
		$WallCls = new WallAction;
		$Wall = $WallCls->getWall($uid,$listRows,1);
		$wallSubheader = $WallCls->getWallHeader($Wall['count'],$listRows,$uid,'u');
		
		$this->assign('wall',$Wall);
		$this->assign('wallSubheader',$wallSubheader);
		$this->assign('profile',$Profile);
		
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>