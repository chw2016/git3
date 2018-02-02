<?php

class XtblessAction extends UserAction{
	public function index(){
		$db=D('xtbless');
		$where['token']=session('token');
		$where['hid']=$this->_get('hid');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	

	
	public function del(){
		$where['id']=$this->_get('id','intval');
		if(D('xtbless')->where($where)->delete()){
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
	}
	
}
?>