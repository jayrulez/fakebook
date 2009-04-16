<?php

class WallAction extends BaseAction
{
	public function index()
	{
		$wid = (int)$_GET['wid'];
		$page = (int)$_GET['page'];
		$type = $_GET['type'];
		
		if($type == 'u')
		{
			if($this->userId)
			{
				$user = new ProfileAction;
				$userRelation = $user->getFriendRelation($wid);
			
				if($userRelation == ('me' || 'friend' || 'request'))
				{
					$info = getUserBasicInfo($wid);
					$title = $info['name'];
					$pic = $info['pic_square'];
				}
				else
				{
					$this->redirect('','','home');
				}
			}
			else
			{
				$this->redirect('','','index');
			}
		}
		else if ($type == 'g')
		{
			$map = array();
			$map['id'] = $wid;
			$group = D('Group')->find($map);
			if(empty($group))
			{
				$this->redirect('','','home');
			}
			else
			{
				if(empty($this->userId))
				{
					if($group['privacy'] == 'OPEN')
					{
						$title = $group['name'];
						$groupAccess = 'GUEST';
					}
					else
					{
						$this->redirect('','','index');
					}
				}
				else
				{
					$map = array();
					$map['uid'] = $this->userId;
					$map['gid'] = $wid;
					$member = D('GroupMember')->findAll($map);
				
					if(empty($member) && $group['privacy'] != 'OPEN')
					{
						$this->redirect('','','home');
					}
					else if(empty($member) && $group['privacy'] == 'OPEN')
					{
						$title = $group['name'];
						$groupAccess = 'USER_GUEST';
					}
					else
					{
						$title = $group['name'];
						$groupAccess = strtoupper($member['title']);
					}
				}
			}
		}
		else
		{
			$this->redirect('','','home');
		}
		
		$listRows  =  10;

		$Wall = $this->getWall($wid,$type,$listRows,$page);

		$this->assign('wall',$Wall);
		$this->assign('type',$type);
		$this->assign('title',$title);
		$this->assign('pic',$pic);
		$this->assign('groupAccess',$groupAccess);
		$this->assign('userRelation',$userRelation);
		
		$this->display();
	}
	
	public function insert()
	{
		$post = $_POST;
		if(empty($post))
		{
			$this->redirect('','','home');
		}
		else if(empty($post['content']))
		{
			redirect($_POST["url"]);
		}
		else
		{
			$dao = D("Wall");
			$dao->type = $_POST['type'];
			$dao->text = $_POST['content'];
			$dao->wid = $_POST['wid'];
			$dao->fromid = $this->userId;
			$dao->time = time();
			$dao->add();

			redirect($_POST["url"]);
		}
	}
	
	public function delete()
	{
		$id = (int)$_GET['delete'];
		
		if($this->isOwner($id))
		{
			$dao = D('Wall');
			$dao->find($id);
			$dao->del = '1';
			$dao->save();
		}
		
		redirect($_SERVER["HTTP_REFERER"]);
	}
	
	public function isOwner($id)
	{
		if(!$post = D('Wall')->find($id))
			return false;
		$wid = $post['wid'];
		$fromId = $post['fromid'];
		$type = $post['type'];

		if($type == 'u')
		{
			if($this->userId == $fromId)
				return true;
	
			if($this->userId == $wid)
				return true;
				
			return false;
		}
		else if($type == 'g')
		{
			if(empty($this->userId))
			{
				return false;
			}
			else
			{
				$map = array();
				$map['uid'] = $this->userId;
				$map['gid'] = $wid;
				$member = D('GroupMember')->find($map);
				
				if(empty($member))
				{
					return false;
				}
				else if($member['title'] == 'creator' OR $member['title'] == 'admin')
				{
					return true;
				}
				else
				{
					if($this->userId == $fromId)
						return true;
						
					return false;
				}
			}
		}

		return false;
	}
	
	public function getWall($wid,$type,$listRows=10,$page=1)
	{
		$dao = D('Wall');
		$map = array();
		$map['wid'] = $wid;
		$map['del'] = 0;
		$map['type'] = $type;
		
		$count	= $dao->count($map);
		
		if($page < 1)
			$page = 1;
		
		if($page > $count)
			$page = $count;
		
		$Wall = $dao->where($map)
					->order('time desc')
					->field('id,fromid,text,time,username')
					->limit((($page - 1) * $listRows).','.$listRows)
					->findAll();

		//get wall action
		foreach($Wall as &$key)
		{
			$action = array();
			//wall action: report
			if($this->userId && $this->userId != $key['fromid'])
			{
				$report = array('report'=>'<a onclick="" href="'.url('','','report','',array('type'=>'wall','id'=>$key['id'])).'">'.L('_ACTION_REPORT_').'</a>');
				$action += $report;
			}
			//wall action: delete
			if($this->isOwner($key['id'],$wid,$key['fromid']))
			{
				$delete = array('delete'=>'<a onclick="" href="'.url('','','Wall','',array('action'=>'delete','id'=>$key['id'])).'">'.L('_ACTION_DELETE_').'</a>');
				$action += $delete;
			}
			
			$key += array('action'=>$action);
			
			//get user info
			$key += array('userInfo'=>getUserBasicInfo($key['fromid']));
		}

		$array = array();
		
		$array['wid'] = $wid;
		$array['type'] = $type;
		$array['list'] = $Wall;
		$array['listRows'] = $listRows;
		$array['count'] = $count;
		$array['page'] = $page;
		
		return $array;
	}
	
	public function getWallHeader($count,$listRows,$wid,$type)
	{
		if($count > $listRows){
			return sprintf(L('wall_subheader2'),
							"<a href=\"".url('','','wall','',array('type'=>$type,'id'=>$wid))."\">",
							number_format($count),
							"</a>",
							$listRows);
		} else if($count > 0){
			return sprintf(L('wall_subheader1'),$count);
		} else {
			return sprintf(L('wall_subheader3'));
		}
		
	}

	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>