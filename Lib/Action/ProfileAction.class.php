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
					$this->redirect('','','people','',array('name'=>str_replace(' ','-',$Profile['name']),'id'=>$uid));

			}
		}
		
		$this->assign('profile',$Profile);
		
		
		/*
		 * get current user's friends
		 */
		$currentUserFriend = getFriend($uid);
		foreach($currentUserFriend as &$key)
		{
			$key = array('uid'=>current(array_diff($key,array($uid))));
		}
		
		/*
		 * get friend relation
		 */
		$userRelation = $this->getFriendRelation($uid);
		
		$this->assign('userRelation',$userRelation);
		
		
		/*
		 * redirect for stranger
		 */
		if($userRelation == 'stranger')
		{
			$this->redirect('','','s','',array('id'=>$uid));
		}
		
		
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
			$friendSubheader = '<a href="'.url('','','friends','',array('id'=>$uid)).'">'.sprintf(L('_friend_subheader1'),$friendCount).'</a>';
		}
		else
		{
			$friendSubheader = '<a href="'.url('','','friends','',array('id'=>$uid)).'">'.sprintf(L('_friend_subheader2'),$friendCount).'</a>';
		}

		$this->assign('friendCount',$friendCount);
		$this->assign('friendSubheader',$friendSubheader);
		
		
		/*
		 * get user info
		 */
		shuffle($currentUserFriend);
		$j = 0;
		for($i = 0;$i < $friendCount;$i++)
		{
			$friend_uid = $currentUserFriend[$i]['uid'];
			$currentUserFriendShuffle[$i]['uid'] = $friend_uid;
			$currentUserFriendShuffle[$i]['userInfo'] = getUserBasicInfo($friend_uid);
			$currentUserFriendShuffle[$i]['key'] = $j;
			$currentUserFriendShuffle[$i]['id'] = $i + 1;
			if($j == 2)
			{
				$j = 0;
			}
			else
			{
				$j++;
			}
			if($i == 5)
			{
				break;
			}
		}
		
		$this->assign('currentUserFriend',$currentUserFriendShuffle);
		
		/*
		 * get user group
		 */
		$userGroupAll = getUserGroup($uid);
		$userGroup = array();
		
		if(empty($userGroupAll))
		{
			$userGroup = $userGroupAll;
		}
		else if($this->userId == $uid)
		{
			$userGroup = $userGroupAll;
			foreach($userGroup as &$key)
			{
				$map['id'] = $key['gid'];
				$info = D('Group')->find($map);
				$key += array('info'=>$info);
			}
		}
		else
		{
			$j = 0;
			for($i=0;$i < count($userGroupAll);$i++)
			{
				$map['id'] = $userGroupAll[$i]['gid'];
				$info = D('Group')->find($map);
				
				if($info['privacy'] != 'SECRET')
				{
					$userGroup[$j] = $userGroupAll[$i]+array('info'=>$info);
					$j++;
				}
			}
		}
		
		$this->assign('userGroup',$userGroup);
		
		
		/*
		 * get user wall
		 */
		$listRows = 5;
		$WallCls = new WallAction;
		$Wall = $WallCls->getWall($uid,'u',$listRows,1);
		$wallSubheader = $WallCls->getWallHeader($Wall['count'],$listRows,$uid,'u');
		
		$this->assign('wall',$Wall);
		$this->assign('wallSubheader',$wallSubheader);

		
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
		
		
		/*
		 * get current user's friends
		 */
		$currentUserFriend = getFriend($uid);
		shuffle($currentUserFriend);
		
		foreach($currentUserFriend as &$key)
		{
			$key = array('uid'=>current(array_diff($key,array($uid))));
		}
		
		//get user info
		for($i = 0;$i < count($currentUserFriend);$i++)
		{
			$friend_uid = $currentUserFriend[$i]['uid'];
			$currentUserFriendShuffle[$i]['uid'] = $friend_uid;
			$currentUserFriendShuffle[$i]['userInfo'] = getUserBasicInfo($friend_uid);
			if($i == 7)
			{
				break;
			}
		}
		
		$this->assign('currentUserFriend',$currentUserFriendShuffle);
		
		
		$this->display();
	}
	
	public function stranger()
	{
		$uid = (int)$_GET['id'];
		
		if(empty($uid))
		{
			$this->redirect('','','index');
		}
		else
		{
			$dao = D('User');
			$Profile = $dao->find($uid);
			if(empty($Profile))
				$this->redirect('','','index');
		}
		
		$this->assign('profile',$Profile);
		
		
		/*
		 * get current user's friends
		 */
		$currentUserFriend = getFriend($uid);
		shuffle($currentUserFriend);
		
		foreach($currentUserFriend as &$key)
		{
			$key = array('uid'=>current(array_diff($key,array($uid))));
		}
		
		//get user info
		for($i = 0;$i < count($currentUserFriend);$i++)
		{
			$friend_uid = $currentUserFriend[$i]['uid'];
			$currentUserFriendShuffle[$i]['uid'] = $friend_uid;
			$currentUserFriendShuffle[$i]['userInfo'] = getUserBasicInfo($friend_uid);
			if($i == 7)
			{
				break;
			}
		}
		
		$this->assign('currentUserFriend',$currentUserFriendShuffle);
		
		
		$this->display();
	}
	
	public function getFriendRelation($uid)
	{
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
			$friend = D('FriendRequest')->find($map);
			if($friend)
			{
				$userRelation = 'request';
			}
			else
			{
				$map['uid_from'] = $uid;
				$map['uid_to'] = $this->userId;
				$friend = D('FriendRequest')->find($map);
				if($friend)
				{
					$userRelation = 'confirm';
				}
				else
				{
					$userRelation = 'stranger';
				}
				
			}
		}
		
		return $userRelation;
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>