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
		
		/*
		 * 
		 * group member
		 * 
		 */
		
		//get members
		$groupMember = getGroupMember($gid);
		
		//get header
		$count = count($groupMember);
		$a1 = "<a href=\"".url('','','members','',array('id'=>$gid))."\">";
		$a2 = "</a>";
		if($count > 8){
			$groupMemberHeader = sprintf(L('_gm_subheader1'),$a1,number_format($count),$a2,8);
		} else {
			$groupMemberHeader = sprintf(L('_gm_subheader2'),$a1,$count,$a2);
		}
		
		//assign
		$this->assign('groupMember',$groupMember);
		shuffle($groupMember);
		$this->assign('groupMemberShuffle',$groupMember);
		$this->assign('groupMemberHeader',$groupMemberHeader);
		
		
		/*
		 * 
		 * mini wall
		 * 
		 */
		$listRows = 5;
		$WallCls = new WallAction;
		$Wall = $WallCls->getWall($gid,'g',$listRows,1);
		$wallSubheader = $WallCls->getWallHeader($Wall['count'],$listRows,$gid,'g');
		$this->assign('wall',$Wall);
		$this->assign('wallSubheader',$wallSubheader);

		//display tmpl
		$this->display();
	}
	
	public function members()
	{
		$this->display();
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>