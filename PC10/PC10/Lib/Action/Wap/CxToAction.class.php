<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 前端
 */
class CxToAction extends BaseAction{
	
    
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		$token=$this->token;
	
	    
	    $count      = M('mru_tiyan')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_tiyan')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);
	    $this->assign('list',$list);
	    
	  //  p($list);
		$this->display();
	}
	
	
	public function show(){
        $list=M('mru_tiyan')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
		$this->display();
	}

	
}
?>
