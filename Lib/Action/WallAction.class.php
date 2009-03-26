<?php

class WallAction extends BaseAction
{
	public function index()
	{
		$wid = $_GET['wid'];
		$page = $_GET['page'];
		$dao = D('Wall');
		$map['wid'] = $wid;
		$listRows  =  10;
		
		$count	= $dao->count($map);
		
		$Wall = $dao->where($map)
					->order('time desc')
					->field('id,fromid,text,time,username')
					->limit('0,10')
					->findAll();
		
		$walltype = getTypeById($wid);
		
		if($walltype == 'user')
		{	
			$walltitle = getUserName($wid);	
		}
		else if ($walltype == 'group')
		{
			$walltitle = getGroupName($wid);
		}

		$this->assign('wid',$wid);
		$this->assign('walltype',$walltype);
		$this->assign('walltitle',$walltitle);
		$this->assign('list',$Wall);
		$this->assign('count',$count);
		
		$this->display();
		
	}
	
	public function insert()
	{
		dump($_POST);
	}
	
	public function delete()
	{
		dump($_GET);
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}

}

?>