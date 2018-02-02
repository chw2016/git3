<?php
class YulanAction extends BaseAction{
	public function index(){
	//	$agent = $_SERVER['HTTP_USER_AGENT'];
		//if(!strpos($agent,"MicroMessenger")) {
		//	echo '此功能只能在微信浏览器中使用';exit;
	//	}
		$token=$this->_get('token');
		$db=M('app');
		$getData=$db->where(array('token'=>$token))->order('id desc')->find();
		/*print_r($getData['state']);
		exit();*/
		$this->assign('state',$getData['state']);
		$this->display();	
    }
}
?>