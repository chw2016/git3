<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 * 
 */
class MhyzxAction extends BaseAction{


	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();



	}

	// 首页显示，店铺发送
	public function index(){
		$uid=M('wxuser')->where(array('token'=>$_GET['token']))->getField('id');

/* 		$token=$this->token;
	    $list=M('mru_nhyzx')->where(array('token'=>$token))->field('content',true)->select();

	    $count      = M('mru_nhyzx')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_nhyzx')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);
	    $this->assign('list',$list); */

	  //  p($list);
	    if(IS_POST){
	    	echo 1;
	    }else{
	    	include"./Lib/Action/Wap/mru.php";
	    	//如果已注跳会员中心
	    	$uid=M('wxuser')->where(array('token'=>$_GET['token']))->getField('id');
	    	$b=M('Usercenter_memberlist')->where(array('openid'=>$_GET['openid'],'uid'=>$uid))->find();
	    	if($b){
	    		header('Location:'.U('Mhyzxsy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])));
	    	}else{
	    		$this->display();
	    	}

	    }

	}


	public function zc(){

		//$yzm=validCode('5d8a87bab30de695954b17fc835b9d12','18773123225','12345');
		//P($yzm);die;


		if(IS_POST){
			//P($_SESSION);die;
			


			$token=$this->token;
			
			$list=M('mru_jfb')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid']))->find();
			if($list) script("此用户名已存在",'Mrugr/index',get(token,openid));
			$tel=M('mru_jfb')->where(array('token'=>$_GET['token'],'tel'=>$_POST['phone']))->find();
			if($tel) script("此手机已存在");
				$_POST['openid']=$_GET['openid'];
				//$_POST['token']=$token;

				//判断验证码
				$yzmYz=validCode($_GET['token'],$_POST['phone'],$_POST['yzm']);
				$yzmYz=json_decode($yzmYz,true);
				if($yzmYz['code']==0){
					//echo "<script>alert('手机短信验证成功,请保存资料');</script>";
				}elseif ($yzmYz['code']==-3){
					echo "<script>alert('系统繁忙,请重试!');history.back();</script>";die;
				}elseif ($yzmYz['code']==-2){
					echo "<script>alert('验证超时,3分钟之内有效!');history.back();</script>";die;
				}elseif ($yzmYz['code']==-1){
					echo "<script>alert('验证码错误!');history.back();</script>";die;
				}
				//根据token找出uid
				$_POST['token']=$_GET['token'];
				$_POST['openid']=$_GET['openid'];
				$_POST['tel']=$_REQUEST['phone'];
				$b=M('mru_jfb')->add($_POST);
				if($b){
					echo '<script>alert("手机短信验证成功!注册成功!");location.href="'.U('Mrugr/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';die;
						
				}else{
					echo "<script>alert('注册失败！');history.back();</script>";
				}
			

	    }else{
	    	include"./Lib/Action/Wap/mru.php";
	    	$this->display();
	    }
	}



	public function bd(){

		if(IS_POST){
				$token=$this->token;
				//判断会员
				MruMember("Mhyzx/zc",$_GET['openid']);
				//判断手机
				$tel=M('mru_jfb')->where(array('token'=>$_GET['token'],'tel'=>$_POST['tel']))->find();
				
				if(!$tel) script("请用您资料中的手机号码绑定");
				
				//判断会员卡
				$is_useds=M('member_idcard')->where(array('token'=>$_GET['token'],'idcard'=>$_POST['hyk']))->find();//
				
				if(!$is_useds){
					echo "<script>alert('会员卡不存在！');history.back();</script>";die;
				}elseif($is_useds['is_used']==1){
					//
					echo "<script>alert('该会员卡已绑定其它会员！');history.back();</script>";die;
				
				}
 				
	 			$yzmYz=validCode($_GET['token'],$_POST['tel'],$_POST['yzm']);
				$yzmYz=json_decode($yzmYz,true);
				if($yzmYz['code']==0){
					//echo "<script>alert('手机短信验证成功,请保存资料');</script>";
				}elseif ($yzmYz['code']==-3){
					echo "<script>alert('系统繁忙,请重试!');history.back();</script>";die;
				}elseif ($yzmYz['code']==-2){
					echo "<script>alert('验证超时,3分钟之内有效!');history.back();</script>";die;
				}elseif ($yzmYz['code']==-1){
					echo "<script>alert('验证失败!');history.back();</script>";die;
				}  
				
					//修改会员卡表状态
					M('member_idcard')->where(array('token'=>$_GET['token'],'idcard'=>$_POST['hyk']))->save(array('is_used'=>1,'openid'=>$_GET['openid'],'use_time'=>time()));
					
					//资料表里插入会员卡
					$b=M('mru_jfb')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid']))->save(array('idcard'=>$_POST['hyk']));
					
					$yhjlist=M('mru_wdyhj')->where(array('token'=>$_GET['token']))->limit(0,1)->select();
					$mru_yhj2=M('mru_yhj2')->add(array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'tel'=>$_POST['tel'],'uid'=>$yhjlist[0]['id']));
					if($mru_yhj2){
						
						//获取积分红包记录
						M('mru_xf')->add(array(
								'token'=>$_GET['token'],
								'openid'=>$_GET['openid'],
								'yid'=>$yhjlist['0']['id'],
								'openid'=>time(),
						));
						
						echo '<script>alert("绑定成功!送张优惠卷,请查收！");location.href="'.U('Wdyhj/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';
					}
					
				

			

		}else{


			include"./Lib/Action/Wap/mru.php";
			$this->display();
		}
	}

	public function ajax(){

	/* 	//判断手机短信是否发送
		$openidYz=sendPhomeCode($_GET['token'],$_POST['phone']);
		$openidYz=json_decode($openidYz,true);

		if(!$openidYz['code']==0){
			echo "<script>alert('手机短信发送失败!');history.back();</script>";die;
		}else{
			echo "<script>alert('手机短信发送失败!');history.back();</script>";die;
		} */

		if(IS_AJAX){

			$res['tel']=$_POST['tel'];
			if(!$_POST['tel']){
				$res['str']="请输入手机!";
				$this->ajaxReturn($res);
				die;
			}
			$res['token']=$_GET['token'];
			
			if(!M('mru_yzmtime')->where(array('openid'=>$_GET['openid']))->getField('add_time')){
				M('mru_yzmtime')->add(array('add_time'=>time(),'openid'=>$_GET['openid']));
				$openidYz=sendPhomeCode($_GET['token'],$_POST['tel']);
				$openidYz=json_decode($openidYz,true);
			}else{
				$add_time=M('mru_yzmtime')->where(array('openid'=>$_GET['openid']))->getField('add_time');
			    if(time()-$add_time>59){
			    	$openidYz=sendPhomeCode($_GET['token'],$_POST['tel']);
			    	$openidYz=json_decode($openidYz,true);
			    	M('mru_yzmtime')->where(array('openid'=>$_GET['openid']))->save(array('add_time'=>time()));
			    }else{
			    	$res['str']="请一分钟后再试！";
			    	$this->ajaxReturn($res);
			    	die;
			    }
			}
			

			if(!$openidYz['code']==0){
				$res['str']="手机短信发送失败!";
			}else{
				$res['str']="手机短信发送成功!";
			}
			$this->ajaxReturn($res);
		}

	}


}
?>
