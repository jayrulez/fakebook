<?php

class FriendsAction extends BaseAction
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
			if(empty($this->userId))
			{
				$this->redirect('','','index');
			}
			else if($uid == $this->userId)
			{
				$uid = $this->userId;
				$Profile = $this->userInfo;
			}
			else
			{
				$user = new ProfileAction;
				$userRelation = $user->getFriendRelation($uid);
				if($userRelation == 'stranger')
					$this->redirect('','','profile','',array('id'=>$uid));
			}
		}
		
		$dao = D('User');
		$Profile = $dao->find($uid);
		
		$this->assign('profile',$Profile);
		
		
		/*
		 * get current user's friends
		 */
		$currentUserFriend = getFriend($uid);
		$i = 0;
		$j = 1;
		
		foreach($currentUserFriend as &$key)
		{
			$key = array('uid'=>current(array_diff($key,array($uid))));
			$key += array('userInfo'=>getUserBasicInfo($key['uid']));
			$key += array('key'=>$i);
			$key += array('id'=>$j);
			
			$j++;
			
			if($i == 9)
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
		 * get user count
		 */
		$friendCount = count($currentUserFriend);
		$this->assign('friendCount',$friendCount);
		
		
		/*
		 * get header
		 */
		$name = '<a href="'.url('','','profile','',array('id'=>$uid)).'">'.$Profile['name'].'</a>';
		
		$pageHeader = sprintf(L('_friends_header'),$name);
		
		if($currentUserFriend)
		{
			$pageSubheader = sprintf(L('_friends_subheader1'),$name,$friendCount);
		}
		else
		{
			$pageSubheader = sprintf(L('_friends_subheader2'),$name);
		}
		
		
		$this->assign('pageHeader',$pageHeader);
		$this->assign('pageSubheader',$pageSubheader);
		
		
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>