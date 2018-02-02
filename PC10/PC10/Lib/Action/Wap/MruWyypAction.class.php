<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruWyypAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		
		parent::_initialize();
		
		header("Content-type:text/html;charset=utf-8");
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		$token=$this->token;
	    $list=M('mru_zhaopin')->where(array('token'=>$token))->field('content',true)->order("sort")->select();
	    
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
		$this->UDisplay();
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
		$this->UDisplay();
	}
	
	
	public function show2(){
		
		
	    if(IS_POST){
	    	//p($_POST);die;
	    	$_POST['add_time']=time();
	    	$_POST['pid']=$_GET['id'];
	    	$_POST['openid']=$_GET['openid'];
	    	//P();die;
	    	$b=M('mru_jl')->where(get1(pid,$_GET['id'],token,openid))->find();
	    	if($b){
	    	  script('该职位下你已经提交过了','MruJlwm/index',get(openid,token));
	    	}
	    	
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
	    	
	    	rz('我要应聘','提交了应聘信息请在后台加入我们中查看详情');
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
	    		$to='ymr@ymr.cn';//收件人ymr@ymr.cn
	    		$res=send_email($subject, $body, $to);//调用发送邮件函数send_email()
	    		if($res['success']){//$res['success']如果为真就是发送成功  把邮箱发过去了， 为了下面方法public function regok()
	    			echo '<script>location.href="'.U('MruJlwm/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';
	    		}else{
	    			echo '<script>location.href="'.U('MruJlwm/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';
	    		}
	    		
	    		
	    	}else{
	    		echo "<script>alert('提交失败');history.back();</script>";
	    	}
	     }else{
	     	include"./Lib/Action/Wap/mru.php";
	     	  $list=M('mru_zhaopin')->where(array('id'=>$_GET['id']))->find();
	     	 $this->assign('list',$list);
	     	$this->UDisplay();
	    }
		
	}

	
}
?>
