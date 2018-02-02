<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class WyypAction extends BaseAction{
	

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		header("Content-type:text/html;charset=utf-8");
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		$token=$this->token;
	    $list=M('mru_zhaopin')->where(array('token'=>$token))->field('content',true)->select();
	    
/* 	    $count      = M('mru_wyyp')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_wyyp')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);*/
	    
	    switch ($list['sex']){
	    	case 1:$list['sex']='男';break;
	    	case 2:$list['sex']='女';break;
	    	case 3:$list['sex']='不限';break;
	    }
	   // print_r($list);die;
	    $this->assign('list',$list);  
	    
	   // p($list);
		$this->display();
	}
	
	
	public function show(){
		
		include"./Lib/Action/Wap/mru.php";
		
		
        $list=M('mru_zhaopin')->where(array('id'=>$_GET['id']))->find();
        
        
        
        switch ($list['sex']){
        	case 1:$list['sex']='男';break;
        	case 2:$list['sex']='女';break;
        	case 3:$list['sex']='不限';break;
        }
       // print_r($list);
        $this->assign('list',$list);
		$this->display();
	}
	
	
	public function show2(){
		
		
	    if(IS_POST){
	    	//p($_POST);die;
	    	$b=M('mru_jl')->add($_POST);
	    	if($b){
	    		echo "<script>alert('提交成功');</script>";
	    		
	    		
	    		//echo 11;die;
	    		$subject='我要应聘';// $subject是主题 'webname' => '多迪小说站',  多迪小说站网站用户激活
	    		$body="姓名：{$_POST['name']}<br/>
	    		性别：{$_POST['age']}<br/>
	    		年龄：{$_POST['sex']}<br/>
	    		学历：{$_POST['xl']}<br/>
	    		籍贯：{$_POST['jg']}<br/>
	    		电话：{$_POST['dh']}<br/>
	    		邮箱：{$_POST['yx']}<br/>
	    		联系地址：{$_POST['dz']}<br/>
	    		工作经验：{$_POST['content']}<br/>
	    		";//内容
	    		$to='ymr@ymr.cn';//收件人
	    		$res=send_email($subject, $body, $to);//调用发送邮件函数send_email()
	    		if($res['success']){//$res['success']如果为真就是发送成功  把邮箱发过去了， 为了下面方法public function regok()
	    			echo '<script>location.href="'.U('Jlwm/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';
	    		}else{
	    			echo '<script>location.href="'.U('Jlwm/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';
	    		}
	    		
	    		
	    	}else{
	    		echo "<script>alert('提交失败');history.back();</script>";
	    	}
	     }else{
	     	include"./Lib/Action/Wap/mru.php";
	     	$this->display();
	    }
		
	}

	
}
?>
