<?php
class SellcarAction extends UserAction{
	//显示主页面
	public function index(){
		$db=M('sellcar');
		$token=$this->_get('token');
		$count=$db->where(array('token'=>$token))->count();
		$page=new Page($count,15);
		$info=$db->where(array('token'=>$token))->order('id')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('res',$info);
		// print_r($info);exit();
		$this->display();
	}
	//删除记录
	public function del(){
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$where['id']=$id;
		$where['token']=$token;
		$db=M('sellcar');
		$result=$db->where($where)->delete();
		if ($result) {
			$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$token)));
		}else{
			$this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>$token)));
		}
	}

	//查看详情
	public function look(){
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$where['id']=$id;
		$where['token']=$token;
		$db=M('sellcar');
		$result=$db->where($where)->find();
		$this->assign('res' ,$result);
		// print_r($result);exit();
		$this->display();
	} 
}
?>