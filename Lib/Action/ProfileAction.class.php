<?php

class ProfileAction extends BaseAction
{
	public function index()
	{
		$uid = (int)$_GET['id'];
		
		if(empty($uid))
		{
			if(empty($this->userId)||empty($this->userInfo))
			{
				$this->redirect('','','index');
			}
			else
			{
				$uid = $this->userId;
				$Profile = $this->userInfo;
			}
		}
		else
		{
			if($uid == $this->userId)
			{
				$uid = $this->userId;
				$Profile = $this->userInfo;
			}
			else
			{
				$dao = D('User');
				$Profile = $dao->find($uid);
				if(empty($Profile))
					$this->redirect('','','home');

				if(empty($this->userId)||empty($this->userInfo))
					$this->redirect('','','people','',array('username'=>str_replace(' ','-',$Profile['display_name']),'id'=>$uid));

			}
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
	
	public function people()
	{
		$uid = (int)$_GET['id'];
		
		if(empty($uid))
		{
			$this->redirect('','','index');
		}
		else
		{
			if(empty($this->userId)||empty($this->userInfo))
			{
				$dao = D('User');
				$Profile = $dao->find($uid);
				if(empty($Profile))
					$this->redirect('','','index');
			}
			else
			{
				$this->redirect('','','profile','',array('id'=>$uid));
			}
		}
		
		$this->assign('profile',$Profile);
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>