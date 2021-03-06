<?php

class ZhengwuposterAction extends UserAction{
	public function index(){
		$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();

		$db=D('Zhengwuposter');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$tj=M('Zhengwuposter')->where(array('token'=>$this->_GET('token')))->count();
		$this->assign('tj',$tj);
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	public function add(){
	$db=M('Zhengwu');
		$where['token']=session('token');
		$info=$db->where($where)->select();	
		$this->assign('info',$info);
		$this->display();

	}
	
	public function edit(){
		$id=$this->_get('id','intval');
		$info=M('Zhengwuposter')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){

	$tj=M('Zhengwuposter')->where(array('token'=>SESSION('token'),'subestatename'=>$_POST['subestatename']))->count();
	if($tj==0){
	$this->all_insert();
	}
	else
	{
	$this->error('操作失败,已有记录！请删除原有海报！',U(MODULE_NAME.'/index'));
	}
	}
	public function upsave(){
		$this->all_save();
	}
}
?>
