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
		 * get current user's friends
		 */
		$currentUserFriend = getFriend($uid);
		shuffle($currentUserFriend);
		$i = 0;
		$j = 1;
		
		foreach($currentUserFriend as &$key)
		{
			$key = array('uid'=>current(array_diff($key,array($uid))));
			$key = $key + array('key'=>$i);
			$key = $key + array('id'=>$j);
			
			$j++;
			
			if($i == 2)
			{
				$i = 0;
			}
			else
			{
				$i++;
			}
		}
		
		$this->assign('currentUserFriend',$currentUserFriend);
		
		
		/*
		 * get friend subheader
		 */
		$friendCount = count($currentUserFriend);
		
		if(!$currentUserFriend)
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

		$this->assign('friendCount',$friendCount);
		$this->assign('friendSubheader',$friendSubheader);
		
		
		/*
		 * get user group
		 */
		$userGroup = getUserGroup($uid);
		
		$this->assign('userGroup',$userGroup);
		
		
		/*
		 * if current user is my friend
		 */
		if($uid == $this->userId)
		{
			$userRelation = 'me';
		}
		else if(in_array($uid,$this->userFriend))
		{
			$userRelation = 'friend';
		}
		else
		{
			$map['uid_from'] = $uid;
			$map['uid_to'] = $this->userId;
			$friendRequest = D('FriendRequest')->find($map);
			if($friendRequest)
			{
				$userRelation = 'friendrequest';
			}
		}
		
		$this->assign('userRelation',$userRelation);
		
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