<?php

class WallAction extends BaseAction
{
	public function index()
	{
		$wid = (int)$_GET['wid'];
		$page = (int)$_GET['page'];
		
		$type = $_GET['type'];
		
		if($type == 'u' && getUserName($wid))
		{
			$title = getUserName($wid);
			$map['type'] = 'u';
		}
		else if ($type == 'g' && getGroupName($wid))
		{
			$title = getGroupName($wid);
			$map['type'] = 'g';
		}
		else
		{
			redirect(url('','','home'));
		}
		
		$listRows  =  10;

		$Wall = $this->getWall($wid,$listRows,$page);

		$this->assign('wall',$Wall);
		$this->assign('type',$type);
		$this->assign('title',$title);
		
		$this->display();
	}
	
	public function insert()
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
	
	public function isOwner($id,$wid=0,$fromid=0)
	{
		if(!$wid)
		{
			if(!$post = D('Wall')->find($id))
				return false;
			$wid = $post['wid'];
			$fromid = $post['fromid'];
		}

		if($this->userId == $fromid)
			return true;
	
		if($this->userId == $wid)
			return true;
		
		/* group wall */

		return false;
	}
	
	public function getWall($wid,$listRows=10,$page=1)
	{
		$dao = D('Wall');
		$map['wid'] = $wid;
		$map['del'] = 0;
		
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

		foreach($Wall as &$key)
		{
			$isOwner = array('isOwner'=>$this->isOwner($key['id'],$wid,$key['fromid']));
			$key = array_merge($key,$isOwner);
		}

		$array = array();
		
		$array['wid'] = $wid;
		$array['list'] = $Wall;
		$array['listRows'] = $listRows;
		$array['count'] = $count;
		$array['page'] = $page;
		
		return $array;
	}
	
	public function getWallHeader($count,$listRows,$wid,$type)
	{
		if($count > $listRows){
			return sprintf(L('index_wall_subheader2'),
							"<a href=\"".url('','','wall','app',array('type'=>$type,'id'=>$wid))."\">",
							number_format($count),
							"</a>",
							$listRows);
		} else if($count > 0){
			return sprintf(L('index_wall_subheader1'),$count);
		} else {
			return sprintf(L('index_wall_subheader3'));
		}
		
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}
}

?>