<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruFuAction extends BaseAction{
	
	public $_sTplBaseDir = 'Wap/default/mru';
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
/* 		$token=$this->token;
	    
	    
	    $count      = M('mru_tiyan')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();*/
	    $list = M('mru_fuwu')->where(array('token'=>$_GET['token']))->order("sort")->select();
	    $this->assign('list',$list); 
	    
	  // p($list);
	  
		$this->UDisplay();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
        $list=M('mru_fuwu')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
        
		$this->UDisplay();
	}

	
}
?>
