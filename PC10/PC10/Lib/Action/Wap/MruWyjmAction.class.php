<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruWyjmAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		header("Content-type:text/html;charset=utf-8");
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		
/* 		$token=$this->token;
	    $list=M('mru_wyjm')->where(array('token'=>$token))->field('content',true)->select();
	    
	    $count      = M('mru_wyjm')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_wyjm')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);
	    $this->assign('list',$list); */
	    
	  //  p($list);
	  if(IS_POST){
	  	$name=$_POST['name'];
	  	$_POST['add_time']=time();
	  //	p($name);die;
	  	$b=M('mru_wyjm')->add($_POST);
	  	
	  	
	  	
	  	
	  	switch ($_POST['age']){
	  		case 1:$_POST['age']='男';break;
	  		case 2:$_POST['age']='女';break;
	  		 
	  	}
	  	
	  	switch ($_POST['xl']){
	  		case 1:$_POST['xl']='小学';break;
	  		case 2:$_POST['xl']='初中';break;
	  		case 3:$_POST['xl']='高中';break;
	  		case 4:$_POST['xl']='大专';break;
	  		case 5:$_POST['xl']='本科';break;
	  		case 6:$_POST['xl']='本科以上';break;
	  	}
	  	
	  	switch ($_POST['jfzj']){
	  		case 1:$_POST['jfzj']='是';break;
	  		case 2:$_POST['jfzj']='否';break;
	  	}
	  	
	  	switch ($_POST['hz']){
	  		case 1:$_POST['hz']='是';break;
	  		case 2:$_POST['hz']='否';break;
	  	}
	  	
	  	if($b){
	  		
	  		
	  		
	  	
	  		
	  		//echo 11;die;
	  		$subject='我要加盟';// $subject是主题 'webname' => '多迪小说站',  多迪小说站网站用户激活
	  		$body="姓名：{$name}<br/>
	  		                  性别：{$_POST['age']}<br/>
	  		               年龄：{$_POST['sex']}<br/>
	  		       学历：{$_POST['xl']}<br/>  		                
	  		          电话：{$_POST['dh']}<br/>
	  		     邮箱：{$_POST['yx']}<br/>
	  		  从事职业：{$_POST['zy']}<br/>
	  		 加盟合作区域：{$_POST['qy']}<br/>
	  		现有投资规模：{$_POST['xtz']}<br/> 
	  		 拟投资规模：{$_POST['ytz']}<br/> 
	  		 是否自有资金：{$_POST['jfzj']}<br/> 
	  		   现经营项目：{$_POST['xm']}<br/> 
	  		  店面面积：{$_POST['mj']}<br/> 
	  		   店面团队人数：{$_POST['num']}<br/> 
	  		   是否整体项目合作：{$_POST['hz']}<br/> 
	  		联系地址：{$_POST['dz']}<br/> 
	  		  加盟需求说明：{$_POST['content']}<br/> 
	  		                 
	  		";//内容
	  		$to='ymr@ymr.cn';//收件人
	  		$res=send_email($subject, $body, $to);//调用发送邮件函数send_email()
	  		if($res['success']){//$res['success']如果为真就是发送成功  把邮箱发过去了， 为了下面方法public function regok()
	  			rz('我要加盟','提交了加盟信息请在后台加入我们中查看详情');
	  			echo "<script>alert('加盟信息提交成功');</script>";
	  			echo '<script>location.href="'.U('MruJlwm/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';
	  		}else{
	  			echo '<script>location.href="'.U('MruJlwm/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';
	  		}
	  		
	  		
	  	}else{
	  		echo "<script>alert('加盟信息提交失败');</script>";
	  		echo '<script>location.href="'.U('MruJlwm/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';
	  	}
	  }else{
	  	include"./Lib/Action/Wap/mru.php";
	  	$this->UDisplay();
	  }
		
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
        $list=M('mru_wyjm')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
		$this->UDisplay();
	}
	
	
	
	public function jm(){
		//P($_SESSION);
		include"./Lib/Action/Wap/mru.php";
/* 		$list=M('mru_wyjm')->where(array('id'=>$_GET['id']))->find();
		$this->assign('list',$list); */
		$list=M('mru_jm')->where(array('token'=>$_GET['token']))->find();
		$this->assign('list',$list);
		/* P($list); */
		$this->UDisplay();
	}

	
}
?>
