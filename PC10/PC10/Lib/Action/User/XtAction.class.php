<?php

class XtAction extends UserAction{
	public function index(){
		
		$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();

		/*if(!strpos($token_open['queryname'],'histickers')){
            $this->error2('您还未开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>$_SESSION['token'],'id'=>session('wxid'))));}
		*/
		
		$db=D('xt');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	public function add(){
		$this->display();
	}
	
	public function edit(){
		$id=$this->_get('id','intval');
		$info=M('xt')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	
	public function del(){
		$where['id']=$this->_get('id','intval');
		if(D('xt')->where($where)->delete()){
			$like['pid']=$where['id'];
			$like['token']=session('token');
			$like['module']='Xt';
			D('keyword')->where($like)->delete();
			$this->success2('操作成功');
		}else{
			$this->error2('操作失败');
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