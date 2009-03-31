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
		
		$WallCls = new WallAction;
		$Wall = $WallCls->getWall($uid,5,1);
		
		$this->assign('wall',$Wall);
		$this->assign('profile',$Profile);
		
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>