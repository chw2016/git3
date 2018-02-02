<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruHyAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		$token=$this->token;
	    
	    
	    $list      = M('mru_hy')->where(array('token'=>$token,'openid'=>$_GET['openid']))->select();
	  
	   
	    foreach ($list as $ke=>$v){
	    	$data=M('mru_jfb')->where(array('openid'=>$v['dopenid'],'token'=>TO))->find();
	    	$list[$ke]['name']=$data['name'];
	    	$list[$ke]['pic']=$data['pic'];
	    }
	  
	    $this->assign('list',$list);
		$this->UDisplay();
	}
	
	
	public function show(){
        $list=M('mru_hy')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
		$this->UDisplay();
	}

	
}
?>
