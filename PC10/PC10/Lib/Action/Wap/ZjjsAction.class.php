<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class ZjjsAction extends BaseAction{
	

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		$token=$this->token;
/* 	    $list=M('mru_zjjs')->where(array('token'=>$token))->field('content',true)->select();
	    
	    $count      = M('mru_zjjs')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_zjjs')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);
	    $this->assign('list',$list); */
	    
	  //  p($list);
		$this->display();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
        $list=M('mru_zjjs')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
       // P($list);
		$this->display();
	}
	
	public function zj(){
		//张湘南
		include"./Lib/Action/Wap/mru.php";
		$token=$this->_get('token');
		$openid=$this->_get('openid');
		//M('mru_zjjs')
		//$type=$this->
		$count      = M('mru_zjjs')->where(array('token'=>$token))->count();
		$Page       = new Page($count,10);
		$Page->setConfig('theme', "    %upPage%  %linkPage% %downPage% ");
		$show       = $Page->show();
		$list = M('mru_zjjs')->where(array('token'=>$token))->order('sort')->limit($Page->firstRow.','.$Page->listRows)->select();
		//P($list);
		$this->assign('page',$show);
		$this->assign('list',$list); 
		$firstRow=ceil($count/10);//总数除以每页显示多少条，有小数加1 得到总页数
		
		$this->assign('firstRow',$firstRow);
	    //P($show);
		//echo $token.$openid;die;
		
		//p($Page->totalPages);die;
		$this->display();
	}
	
	
	public function show2(){
		include"./Lib/Action/Wap/mru.php";
		$list=M('mru_ppjs')->where(array('id'=>$_GET['id'],'token'=>$_GET['token']))->find();
		$this->assign('list',$list);
		
		$this->display();
	}
	


	
	public function fc(){
		include"./Lib/Action/Wap/mru.php";
		/* 		$token=$this->token;
		  
		 
		$count      = M('mru_tiyan')->where(array('token'=>$token))->count();
		$Page       = new Page($count,10);
		$show       = $Page->show();*/
		$list = M('mru_fc')->where(array('token'=>$_GET['token']))->order("sort")->select();
		$this->assign('list',$list);
		 
		 //p($list);
		 
		$this->display();
	}
	
	
	public function fcshow(){
		include"./Lib/Action/Wap/mru.php";
		$list=M('mru_fc')->where(array('id'=>$_GET['id']))->find();
		$this->assign('list',$list);
		$this->display();
	}
	
	
}
?>
