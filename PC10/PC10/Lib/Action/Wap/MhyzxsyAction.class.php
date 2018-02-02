<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MhyzxsyAction extends BaseAction{
	

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
/* 		$token=$this->token;
	    $list=M('mru_nhyzx')->where(array('token'=>$token))->field('content',true)->select();
	    
	    $count      = M('mru_nhyzx')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_nhyzx')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);
	    $this->assign('list',$list); */
	    
	  //  p($list);
	    //查出会员名
		$name=M('mru_jfb')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid']))->getField(name);
		$this->assign('name',$name);
	
		//查出积分,红包，
		$list=M('mru_jfb')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid']))->field("num,hongbao")->find();
	    $this->assign('list',$list);
		include"./Lib/Action/Wap/mru.php";
		$this->display();
	}
	
	
	public function zc(){
		include"./Lib/Action/Wap/mru.php";
		$this->display();
	}
	public function gr(){
		$list=M("mru_jfb")->where(get(openid,token))->find();
		if(!$list['name']||!$list['sex']||!$list['address']||!$list['tel']){
		    	script("资料没镇完整,必须镇写昵称,性别,电话,地址","Mrugr/index",get(openid,token));
		    	
		}else{
			if($_SESSION['url']){
				echo '<script>alert("镇写完成");location.href="'.C('site_url').$_SESSION['url'].'"</script>';die;
			}else{
				header('Location:'.U('index',get(openid,token)));
			}
			
		}
	}
	
	

	
}
?>
