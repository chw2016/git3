<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruhdAction extends BaseAction{
	
	public $_sTplBaseDir = 'Wap/default/mru';
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		
		if($_GET['aid']){
			$token=$this->token;
			$count      = M('mru_huodong')->where(array('token'=>$token,'state2'=>1,'status'=>1,'aid'=>$_GET['aid']))->count();
			$Page       = new Page($count,15);
			$show       = $Page->show();
			$list = M('mru_huodong')->where(array('token'=>$token,'state2'=>1,'status'=>1,'aid'=>$_GET['aid']))->order('sort')->limit($Page->firstRow.','.$Page->listRows)->select();
			if (!$list) script("该店面下没有发布活动");
			$this->assign('page',$show);
			$this->assign('list',$list);
			$firstRow=ceil($count/15);//总数除以每页显示多少条，有小数加1 得到总页数
			$this->assign('firstRow',$firstRow);//把总页数分配过去
			
		}else{
			$token=$this->token;
			$count      = M('mru_huodong')->where(array('token'=>$token,'state2'=>1,'status'=>1,'aid'=>0))->count();
			$Page       = new Page($count,15);
			$show       = $Page->show();
			$list = M('mru_huodong')->where(array('token'=>$token,'state2'=>1,'status'=>1,'aid'=>0))->order('sort')->limit($Page->firstRow.','.$Page->listRows)->select();
			$this->assign('page',$show);
			$this->assign('list',$list);
			$firstRow=ceil($count/15);//总数除以每页显示多少条，有小数加1 得到总页数
			$this->assign('firstRow',$firstRow);//把总页数分配过去
			
			
		}
		
	    
	  //  p($list);
		$this->UDisplay();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
        $list=M('mru_huodong')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
       // P($list);
		$this->UDisplay();
	}
	
	
	public function bm(){
		//判断会员
		MruMember("Mhyzx/zc",$_GET['openid']);
		
		$uid=$_GET['uid'];
		//P(TOKEN);die;
		$bb=M('mru_hubm')->where(array('uid'=>$uid,'token'=>$_GET['token'],'openid'=>$_GET['openid']))->find();
		//P($bb);die;
	    if($bb){
	    	//echo "<script>alert('该活动下你已经报名了');history.back();</script>";
	    	echo '<script>alert("该活动下你已经报名了");location.href="'.U('Mruhd/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'aid'=>$_GET['aid'])).'";</script>';die;
	    }else{
	    	$_GET['add_time']=time();
	    	$b=M('mru_hubm')->add($_GET);
	    	if($b){
	    		//echo "<script>alert('报名成功');history.back();</script>";
	    		echo '<script>alert("报名成功");location.href="'.U('Mruhd/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'aid'=>$_GET['aid'])).'";</script>';die;
	    	}else{
	    		//echo "<script>alert('报名失败');history.back();</script>";
	    		echo '<script>alert("报名失败");location.href="'.U('Mruhd/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'aid'=>$_GET['aid'])).'";</script>';die;
	    	}
	    	
	    }
		  
		
	}

	
	
	public function index2(){
	
	
			$token=$this->token;
			$count      = M('mru_huodong')->where(array('token'=>$token,'state2'=>1,'status'=>1,'aid'=>array('neq',0)))->count();
			$Page       = new Page($count,15);
			$show       = $Page->show();
			$list = M('mru_huodong')->where(array('token'=>$token,'state2'=>1,'status'=>1,'aid'=>array('neq',0)))->order('sort')->limit($Page->firstRow.','.$Page->listRows)->select();
			$this->assign('page',$show);
			$this->assign('list',$list);
			$firstRow=ceil($count/15);//总数除以每页显示多少条，有小数加1 得到总页数
			$this->assign('firstRow',$firstRow);//把总页数分配过去
			$this->UDisplay("Mruhd_index");
	}
	
}
?>
