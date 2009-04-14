<?php

class GroupAction extends BaseAction
{
	public function index()
	{
		$gid = (int)$_GET['id'];
		
		//redirect if isnt group
		if(!$gid || !getGroupInfo($gid))
			$this->redirect('','','home');
		
		//get group info
		$groupInfo = getGroupInfo($gid);
		$this->assign('groupInfo',$groupInfo);
		
		
		//get members
		$groupMember = getGroupMember($gid);
		
		$this->assign('groupMember',$groupMember);
		
		
		/*
		 * check access settings
		 */
		if(empty($groupMember))
		{
			if(empty($this->userId))
			{
				$this->redirect('','','index');
			}
			else
			{
				$this->redirect('','','home');
			}
		}
		else if(empty($this->userId))
		{
			if($groupInfo['privacy'] == 'OPEN')
			{
				$groupAccess = 'GUEST';
			}
			else
			{
				$this->redirect('','','index');
			}
		}
		else
		{
			$isMember = false;
			foreach($groupMember as $key)
			{
				if($this->userId == $key['uid'])
				{
					$isMember = true;
					$groupAccess = strtoupper($key['title']);
					break;
				}
			}
			
			if(!$isMember)
			{
				if($groupInfo['privacy'] == 'OPEN')
				{
					$groupAccess = 'USER_GUEST';
				}
				else if($groupInfo['privacy'] == 'CLOSED')
				{
					$this->display('closed');
					exit();
				}
				else
				{
					$this->redirect('','','home');
				}
			}
		}
		
		$this->assign('groupAccess',$groupAccess);
		
		
		/*
		 * group member
		 */
		
		//get header
		if($groupMember)
		{
			$count = count($groupMember);
			if($groupAccess == 'GUEST')
			{
				$a1 = '';
				$a2 = '';
			}
			else
			{
				$a1 = "<a href=\"".url('','','members','',array('id'=>$gid))."\">";
				$a2 = "</a>";
			}

			if($count > 8){
				$groupMemberHeader = sprintf(L('_gm_subheader1'),$a1,number_format($count),$a2,8);
			} else {
				$groupMemberHeader = sprintf(L('_gm_subheader2'),$a1,$count,$a2);
			}
		
			shuffle($groupMember);
			$this->assign('groupMemberShuffle',$groupMember);
		}
		else
		{
			$groupMemberHeader = L('_gm_subheader3');
		}
		
		$this->assign('groupMemberHeader',$groupMemberHeader);
		
		
		/*
		 * mini wall
		 */
		$listRows = 5;
		$WallCls = new WallAction;
		$Wall = $WallCls->getWall($gid,'g',$listRows,1);
		$wallSubheader = $WallCls->getWallHeader($Wall['count'],$listRows,$gid,'g');
		$this->assign('wall',$Wall);
		$this->assign('wallSubheader',$wallSubheader);
		
		
		$this->display();
	}
	
	public function members()
	{
		$gid = (int)$_GET['id'];
		
		//redirect if isnt group
		if(!$gid || !getGroupInfo($gid))
			$this->redirect('','','home');
		
		//get group info
		$groupInfo = getGroupInfo($gid);
		$this->assign('groupInfo',$groupInfo);
		
		//get members
		$groupMember = getGroupMember($gid);
		
		
		/*
		 * check access
		 */
		if(empty($groupMember))
		{
			if(empty($this->userId))
			{
				$this->redirect('','','index');
			}
			else
			{
				$this->redirect('','','home');
			}
		}
		else if(empty($this->userId))
		{
			$this->redirect('','','index');
		}
		else
		{
			$isMember = false;
			foreach($groupMember as $key)
			{
				if($this->userId == $key['uid'])
				{
					$isMember = true;
					break;
				}
			}
			
			if(!$isMember && $groupInfo['privacy'] != 'OPEN')
			{
				$this->redirect('','','home');
			}
		}
		
		
		/*
		 * edit group member list
		 */
		$i = 0;
		$j = 1;
		
		foreach($groupMember as &$key)
		{
			$key = $key + array('key'=>$i);
			$key = $key + array('id'=>$j);
			
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
		
		$this->assign('groupMember',$groupMember);
		
		
		//get member count
		$memberCount = count($groupMember);
		$this->assign('memberCount',$memberCount);
		
		/*
		 * get header
		 */
		$name = '<a href="'.url('','','group','',array('id'=>$gid)).'">'.$groupInfo['name'].'</a>';
		
		$pageHeader = sprintf(L('_index_header'),$name);
		$pageSubheader = sprintf(L('_index_subheader'),$name,$memberCount);
		
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