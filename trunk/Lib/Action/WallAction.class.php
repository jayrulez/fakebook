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
		
		$this->assign('list',$Wall);
		$this->assign('count',$count);
		
		$this->display();
		
	}
	
	public function _empty()
	{
		$this->redirect('','ERROR');
	}

}

?>