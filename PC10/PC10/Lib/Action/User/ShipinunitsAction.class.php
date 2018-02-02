<?php

class ShipinunitsAction extends UserAction{
	public function index(){
		
		
		$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();

		/*
		*/
		$db=D('Shipinunits');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	public function add(){
		$db=M('Shipinsub');
		$where['token']=session('token');
		$info=$db->where($where)->select();
		$this->assign('info',$info);
		
		
		$this->display();
	}
	
	public function edit(){
		
		$db=M('Shipinsub');
		$where['token']=session('token');
		$info1=$db->where($where)->select();
		$this->assign('info1',$info1);
		
		$id=$this->_get('id','intval');
		$info=M('Shipinunits')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
			$this->success2('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error2('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){
		$this->all_insert2();
	}
	public function upsave(){
		$this->all_save2();
	}
}
?>