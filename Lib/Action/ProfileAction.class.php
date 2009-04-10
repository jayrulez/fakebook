<?php

class ProfileAction extends BaseAction
{
	public function index()
	{
		$uid = (int)$_GET['id'];
		
		if(empty($uid))
		{
			if(empty($this->userId))
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

				if(empty($this->userId))
					$this->redirect('','','people','',array('username'=>str_replace(' ','-',$Profile['name']),'id'=>$uid));

			}
		}
		
		$this->assign('profile',$Profile);
		
		
		/*
		 * get user wall
		 */
		$listRows = 5;
		$WallCls = new WallAction;
		$Wall = $WallCls->getWall($uid,'u',$listRows,1);
		$wallSubheader = $WallCls->getWallHeader($Wall['count'],$listRows,$uid,'u');
		
		$this->assign('wall',$Wall);
		$this->assign('wallSubheader',$wallSubheader);
		
		
		/*
		 * get user friends
		 */
		$userFriend = getFriend($uid);
		
		foreach($userFriend as &$key)
		{
			$key = current(array_diff($key,array($uid)));
		}
		
		$this->assign('userFriend',$userFriend);
		
		
		/*
		 * get friend subheader
		 */
		$friendCount = count($userFriend);
		
		if(!$userFriend)
		{
			$friendSubheader = L('_friend_subheader3');
		}
		else if($friendCount > 1)
		{
			$friendSubheader = '<a href="'.url('','','friends').'">'.sprintf(L('_friend_subheader1'),$friendCount).'</a>';
		}
		else
		{
			$friendSubheader = '<a href="'.url('','','friends').'">'.sprintf(L('_friend_subheader2'),$friendCount).'</a>';
		}

		
		$this->assign('friendSubheader',$friendSubheader);
		
		
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
			if(empty($this->userId))
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