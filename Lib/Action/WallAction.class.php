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
		
		$dao = D('Wall');
		$map['wid'] = $wid;
		$map['del'] = 0;
		$listRows  =  10;
		
		$count	= $dao->count($map);
		
		if($page < 1)
			$page = 1;
		
		if($page > $count)
			$page = $count;
		
		$Wall = $dao->where($map)
					->order('time desc')
					->field('id,fromid,text,time,username')
					->limit((($page - 1) * $listRows).',10')
					->findAll();

		$this->assign('wid',$wid);
		$this->assign('type',$type);
		$this->assign('title',$title);
		$this->assign('list',$Wall);
		$this->assign('listRows',$listRows);
		$this->assign('count',$count);
		$this->assign('page',$page);
		
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

		redirect($_SERVER["HTTP_REFERER"]);
	}
	
	public function delete()
	{
		$id = (int)$_GET['delete'];
		
		if(isWallOwner($id,$this->userId))
		{
			/*
			$dao = D("Wall");
			$dao->deleteById($_GET['delete']);
			*/
			$dao = D('Wall');
			$dao->find($id);
			$dao->del = '1';
			$dao->save();
		}
		
		redirect($_SERVER["HTTP_REFERER"]);
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}

}

?>