<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class JlwmAction extends BaseAction{
	

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
	    
	  //  p($list);
		$this->display();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
        $list=M('mru_Jlwm')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
		$this->display();
	}

	
}
?>
