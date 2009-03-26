<?php

class WallAction extends BaseAction
{
	public function index()
	{
		$wid = (int)$_GET['wid'];
		$page = (int)$_GET['page'];
		$dao = D('Wall');
		$map['wid'] = $wid;
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
		
		$walltype = getTypeById($wid);
		
		if($walltype == 'user')
		{	
			$walltitle = getUserName($wid);	
		}
		else if ($walltype == 'group')
		{
			$walltitle = getGroupName($wid);
		} else
		{
			redirect(url('','','home'));
		}

		$this->assign('wid',$wid);
		$this->assign('walltype',$walltype);
		$this->assign('walltitle',$walltitle);
		$this->assign('list',$Wall);
		$this->assign('listRows',$listRows);
		$this->assign('count',$count);
		$this->assign('page',$page);
		
		$this->display();
		
	}
	
	public function insert()
	{
		$dao = D("Wall");
		$dao->time = time();
		$dao->text = $_POST['content'];
		$dao->wid = $_POST['wid'];
		$dao->fromid = $this->userId;
		$dao->add();

		redirect($_SERVER["HTTP_REFERER"]);
	}
	
	public function delete()
	{
		$id = (int)$_GET['delete'];
		
		if(isWallOwner($id,$this->userId))
		{
			$dao = D("Wall");
			$dao->deleteById($_GET['delete']);
		}
		
		redirect($_SERVER["HTTP_REFERER"]);
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}

}

?>