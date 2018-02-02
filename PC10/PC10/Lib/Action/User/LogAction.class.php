<?php

 
class LogAction extends Action {

    public function index(){
		if($this->isAjax()){
			if(!$_POST['username']){
				$this->ajaxReturn('', '用户名和密码不能为空', -1);
			}
			$condition['name'] = $this->_post('username', 'trim');
			$password = $this->_post('password', 'trim');
			
			$admin = M('admin')->field(true)->where($condition)->find();
			if(!$admin){
				$this->ajaxReturn('', '该用户名不存在', 0);
			}
			if($admin['password'] != md5(crypt($password,$admin['salt']))){
				$this->ajaxReturn('', '密码不正确', -2);
			}
			if($admin['status'] == 3){
				$this->ajaxReturn('', '该帐号已经被限制', -3);
			}
			
			$cond['agid'] = array('eq',$admin['agid']);
			$auth = M('admingroup')->field(true)->where($cond)->find();
			session('auth',$auth);

			
			//加入session
			session('aid', $admin['aid']);
			session('agid', $admin['agid']);
			session('username', $admin['name']);			
			$this->ajaxReturn($admin['name'], '登录成功', 1);
		}
        $this->display();
    }
	

}