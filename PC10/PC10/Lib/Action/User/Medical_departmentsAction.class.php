<?php

class Medical_departmentsAction extends UserAction{
	public function index(){
		$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();
		$where['token']= session('token');
		$db=D('Medical_departments');
		$count=$db->where($where)->count();
		$page=new Page($count,25);	
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();	
		$infoa=M('Medical_setup_control')->where(array('token'=>$this->_GET('token')))->find();
		$this->assign('infoa',$infoa);
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	public function add(){
		$this->display();
	}
	public function edit(){
		$id=$this->_get('id','intval');
		$info=M('Medical_departments')->find($id);
		$infoa=M('Medical_setup_control')->where(array('token'=>$this->_GET('token')))->find();
	   $this->assign('infoa',$infoa);
		$this->assign('info',$info);
		$this->display();
	}
	public function del(){
	$token=$this->_get('token');
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
			$this->error2('操作成功',U('Medical_departments/index',array('token'=>$token)));
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
