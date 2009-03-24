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
		$Wall	= $dao->findAll($map,'*','time desc','0,10');
		
		$this->assign('list',$Wall);
		$this->assign('count',$count);
		
		$this->display();
		
		/*
		$Wall = $dao->where('wid=$wid')
					->order('time desc')
					->filed('id,fromid,text,time,username')
					->limit(10)
					->findAll();
		
		$this->assign('Wall',$Wall);
		
		$this->display();
		*/
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}

}

?>