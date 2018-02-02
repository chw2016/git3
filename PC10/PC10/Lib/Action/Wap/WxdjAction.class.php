<?php

/**
 * 微信兑奖  李铭   2015.9.1
 *
 */
class WxdjAction extends BaseAction{
    public $token;
    public $wecha_id = 'gh_aab60b4c5a39';
    public $product_model;
    public $product_cat_model;
    public $session_cart_name;
    public $dopenid;
    public $_sTplBaseDir = 'Wap/default/wxdj/a';
    public $wxusers_id;//wxusers 表的id
    public $uid = null;

    public function _initialize() {
        if(in_array(ACTION_NAME,array('code','store','yuyue','user_index'))){
            if(!IS_POST){
                $this->autoShare = true;
            }

        }
        parent::_initialize();
        //echo '321';exit;
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger")) {
            //	echo '此功能只能在微信浏览器中使用';exit;
        }
        $this->token = isset($_REQUEST['token']) ? htmlspecialchars($_REQUEST['token']) : session('token');
        $this->session_cart_name = 'session_cart_products_' . $this->token;
        $this->assign('token', $this->token);
        $this->wecha_id	= isset($_REQUEST['wecha_id']) ? htmlspecialchars($_REQUEST['wecha_id']) : '';
        //$this->openid	= isset($_REQUEST['openid']) ? htmlspecialchars($_REQUEST['openid']) : '';
        /*
        if(!$this->openid){
            $this->openid	= isset($_REQUEST['wecha_id']) ? htmlspecialchars($_REQUEST['wecha_id']) : '';
        }
         */
        $this->dopenid	= isset($_REQUEST['dopenid']) ? htmlspecialchars($_REQUEST['dopenid']) : '';
        $this->wecha_id = $this->openid;
        $this->assign('wecha_id', $this->wecha_id);
        $this->assign('openid', $this->openid);
        //print_r($this->product_cat_model);exit;
        $this->assign('staticFilePath', str_replace('./','/',THEME_PATH.'common/css/store'));
        /*
        * 引入微信js接口
        */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);

        /**
         * 得wxusers id
         */

        $this->wxusers_id=M('Wxuser')->where(array('token'=>$this->token))->getField('id');
        $this->wxusers_id=M('Wxusers')->where(array('uid'=>$this->wxusers_id,'openid'=>$this->openid))->getField('id');
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $this->uid = $wxuser['id'];
	$this->token = '5d8a87bab30de695954b17fc835b9d12';
	$this->assign('token', $this->token);

    }
    public function index()
	{
      //p($_GET['secret']);
		//自动触发扫一扫页面,在自定义菜单那里就会触发的
		if(!isset($_GET['type'])){
			$this->assign('end',1);
		}else{
		if(isset($_GET['secret'])){
			
			$secret = $_GET['secret'];
			//$sql_sn = M('js_sn')->where(array('token'=>$this->token,'sn'=>$sn))->getField('sn');
			//$is_sn = M('js_sn')->where(array('token'=>$this->token,'sn'=>$sn,'secret'=>$secret))->getField('is_sn');
			$sql_secret = M('js_sn')->where(array('token'=>$this->token,'secret'=>$secret))->getField('secret');
			//if($id = M('js_users')->where(array('token'=>$this->token)))//防止重复扫描
			//验证数据库中是否找到sn和secret密码
			//WL('sn_log'.print_r($_POST, true));
			//if($is_sn == 1)//是否验证SN
			//{
					if($sql_secret)
					{
						$id = M('js_sn')->where(array('token'=>$this->token,'secret'=>$secret))->getField('id');
						$is_dj = M('js_sn')->where(array('token'=>$this->token,'secret'=>$secret))->getField('is_dj');
						$sid = M('js_dj')->where(array('token'=>$this->token,'sid'=>$id))->find();
						//是否兑奖过
						if($is_dj==1)//已兑奖
						{//跳到已对过奖的页面，即是不中奖页面
						
							$name = M('wxusers')->where(array('openid'=>$this->openid))->getField('nickname');
							$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
							$data['secret'] = $secret;
							//$data['sn'] = $sn;
							$data['uid'] = $wxid;
							$data['token'] = $this->token;
							$data['info'] = '重复兑奖';
							$data['name'] = $name;
							$data['add_time'] = time();
							$log = M('js_log')->where(array('token'=>$this->token))->add($data);
							//跳到重复兑奖的页面，你已经兑过
							$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
							if(M('js_dj')->where(array('token'=>$this->token,'uid'=>$wxid,'sid'=>$id))->getField('s_phone'))
							{
								//msg($this->token,$this->openid,'该刮奖卡您已兑过奖！');
								header('location:'.U('nosql1',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
							}else{
							//跳到不中奖页面
								//msg($this->token,$this->openid,'未中奖！谢谢购买产品！');
								header('location:'.U('nosql',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
							}
						}else{//未兑奖
							//$data['is_dj'] = 1;
							//$js_sn = M('js_sn')->where(array('token'=>$this->token,'sn'=>$sn,'secret'=>$secret))->save($data);
							//判断js_sn is_yj是否中奖
							$yj = M('js_sn')->field('is_yj')->where(array('token'=>$this->token,'secret'=>$secret))->find();
							
							if($yj['is_yj'] != 0)
							{
								//'中奖';
								//$data['is_dj'] = 2;
								//$js_sn = M('js_sn')->where(array('token'=>$this->token,'sn'=>$sn,'secret'=>$secret))->save($data);
								$note = M('js_sn')->where(array('secret'=>$secret))->find();
								if($note)
								{	
									$sid = M('js_notes')->where(array('secret'=>$secret))->find();
									
									if($sid){
											if($sid['openid'] == $this->openid){
												header('location:'.U('expiry',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
											}else{
												header('location:'.U('nosql',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
											}
									}else{
										$re['openid'] = $this->openid;
										$re['secret'] = $secret;
										$re['add_time'] = time();
										$nid = M('js_notes')->add($re);
										header("Location:".U('expiry',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
									}
								}
								
								


								//$this->display('./tpl/Wap/default/wxdj/a/Wxdj_zhong.html');
							}else{
								//echo 'is_yj==0不中奖';
								$name = M('wxusers')->where(array('openid'=>$this->openid))->getField('nickname');
								$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
								$data['secret'] = $secret;
								//$data['sn'] = $sn;
								$data['uid'] = $wxid;
								$data['token'] = $this->token;
								$data['info'] = '中奖无效';
								$data['name'] = $name;
								$data['add_time'] = time();
								$log = M('js_log')->where(array('token'=>$this->token))->add($data);
								header("Location:".U('yj',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
							}
						}
					}
					else{//找不到记录，数据库中没有此记录,跳到不中奖页面
					
						$name = M('wxusers')->where(array('openid'=>$this->openid))->getField('nickname');
						$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
						$data['secret'] = $secret;
						//$data['sn'] = $sn;
						$data['uid'] = $wxid;
						$data['token'] = $this->token;
						$data['info'] = '无此记录';
						$data['name'] = $name;
						$data['add_time'] = time();
						$log = M('js_log')->where(array('token'=>$this->token))->add($data);
						//msg($this->token,$this->openid,'未中奖！谢谢购买产品！');
						header("Location:".U('nosql',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
					}
		}
		
}
		$this->assign('type',$type);
        $this->UDisplay('Wxdj_index3');

    }
    public function index1(){
		if(isset($_GET['secret'])){
			$secret = $_GET['secret'];
			$sql_secret = M('js_sn')->where(array('token'=>$this->token,'secret'=>$secret))->getField('secret');
					if($sql_secret)
					{
						//$id = M('js_sn')->where(array('token'=>$this->token,'secret'=>$secret))->getField('id');
						//$is_dj = M('js_sn')->where(array('token'=>$this->token,'secret'=>$secret))->getField('is_dj');
						//echo $is_dj;die;
						//$sid = M('js_dj')->where(array('token'=>$this->token,'sid'=>$id))->find();
						//是否兑奖过
						if($is_dj==1)//已兑奖
						{//跳到已对过奖的页面，即是不中奖页面
							$name = M('wxusers')->where(array('openid'=>$this->openid))->getField('nickname');
							$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
							$data['secret'] = $secret;
							//$data['sn'] = $sn;
							$data['uid'] = $wxid;
							$data['token'] = $this->token;
							$data['info'] = '重复兑奖';
							$data['name'] = $name;
							$data['add_time'] = time();
							$log = M('js_log')->where(array('token'=>$this->token))->add($data);
							//跳到重复兑奖的页面，你已经兑过
							$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
							if(M('js_dj')->where(array('token'=>$this->token,'uid'=>$wxid,'sid'=>$id))->getField('s_phone'))
							{
								//msg($this->token,$this->openid,'该刮奖卡您已兑过奖！');
								header('location:'.U('nosql1',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
							}else{
							//跳到不中奖页面
								//msg($this->token,$this->openid,'未中奖！谢谢购买产品！');
								header('location:'.U('nosql',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
							}
						}else{//未兑奖
							//$data['is_dj'] = 1;
							//$js_sn = M('js_sn')->where(array('token'=>$this->token,'sn'=>$sn,'secret'=>$secret))->save($data);
							//判断js_sn is_yj是否中奖
							$yj = M('js_sn')->field('is_yj')->where(array('token'=>$this->token,'secret'=>$secret))->find();
							
							if($yj['is_yj'] != 0)
							{
								//'中奖';
								//$data['is_dj'] = 2;
								//$js_sn = M('js_sn')->where(array('token'=>$this->token,'sn'=>$sn,'secret'=>$secret))->save($data);
								header("Location:".U('expiry',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
								//$this->display('./tpl/Wap/default/wxdj/a/Wxdj_zhong.html');
							}else{
								//echo 'is_yj==0不中奖';
								$name = M('wxusers')->where(array('openid'=>$this->openid))->getField('nickname');
								$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
								$data['secret'] = $secret;
								//$data['sn'] = $sn;
								$data['uid'] = $wxid;
								$data['token'] = $this->token;
								$data['info'] = '中奖无效';
								$data['name'] = $name;
								$data['add_time'] = time();
								$log = M('js_log')->where(array('token'=>$this->token))->add($data);
								header("Location:".U('yj',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
							}
						}
					}
					else{//找不到记录，数据库中没有此记录,跳到不中奖页面
						$name = M('wxusers')->where(array('openid'=>$this->openid))->getField('nickname');
						$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
						$data['secret'] = $secret;
						//$data['sn'] = $sn;
						$data['uid'] = $wxid;
						$data['token'] = $this->token;
						$data['info'] = '无此记录';
						$data['name'] = $name;
						$data['add_time'] = time();
						$log = M('js_log')->where(array('token'=>$this->token))->add($data);
						//msg($this->token,$this->openid,'未中奖！谢谢购买产品！');
						header("Location:".U('nosql',array('token'=>$this->token,'openid'=>$this->openid,'secret'=>$secret)));
					}
			//}
        }else{
            $this->UDisplay();
        }
    }
	public function yj()
	{
		$this->UDisplay();
	}
	public function update()
	{
		//$sn = $_POST['sn'];
		$secret = $_GET['secret'];
		$SN['is_dj']=1;
		if(M('js_sn')->where(array('token'=>$this->token,'secret'=>$_POST['secret']))->save($SN))
		{
			echo 1;
		}else{
			echo 2;
		}
		
	}
	public function nosql()
	{
		$this->UDisplay();
	}
	public function nosql1()
	{
		$this->UDisplay();
	}/*
	public function submit()
	{	
		//兑奖表
		$snid = M('js_sn')->where(array('token'=>$this->token,'secret'=>$_POST['secret'],'sn'=>$_POST['sn']))->getField('id');
		$_POST['token'] = $this->token;
		$_POST['sid'] =  $snid;
		$_POST['uid'] = $this->wxusers_id;
		$_POST['d_time']  = time();
		$_POST['s_name'] = $_POST['reg_name'];
		$_POST['s_phone'] = $_POST['reg_phone'];
		$_POST['s_address'] = $_POST['tag_code'];
		$_POST['status'] = 4;
		$_POST['type'] = 1;//兑奖
		M('js_dj')->where(array('token'=>$this->token))->add($_POST);
		
		//用户表
		$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
		$data['name']     = $_POST['reg_name'];
		$data['phone']    = $_POST['reg_phone'];
		$data['address']  = $_POST['tag_code'];
		$data['token']    = $this->token;
		$data['openid']   = $this->openid;
		$data['uid']      = $wxid;
		$data['status']   = 1;
		$data['add_time'] = time();
		if(M('js_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->add($data))
		{
			echo json_encode(array('status'=>1));
		}else{
			echo json_encode(array('status'=>2));
		}
		
	}*/
	//兑奖用户
	public function djyj()
	{
		
		$this->UDisplay();
	}
	public function yjuser()
	{
		//$sn = $_GET['sn'];
		$secret = $_GET['secret'];
		$wxid = M('wxusers')
		->where(array('openid'=>$this->openid))
		->getField('id');
			$snid = M('js_sn')
		->where(array('token'=>$this->token,'secret'=>$secret))
		->getField('id');
		$data['token']=$this->token;
		$data['uid'] = $wxid;
		$data['sid'] = $snid;
		$data['add_time'] = time();

		$info = M('js_users')->where(array(
			'token'=>$this->token,
			'is_if'=>1,
			'openid'=>$this->openid
			))->find();
		M('js_yj')->add($data);

		$this->assign('info',$info);
		$this->UDisplay();
	}
	public function user_submit()
	{	
		//兑奖表
		$snid = M('js_sn')->where(array('token'=>$this->token,'secret'=>$_POST['secret']))->getField('id');
		$_POST['token'] = $this->token;
		$_POST['sid'] =  $snid;
		$_POST['uid'] = $this->wxusers_id;
		$_POST['d_time']  = time();
		$_POST['s_name'] = $_POST['reg_name'];
		$_POST['s_phone'] = $_POST['reg_phone'];
		$_POST['s_address'] = $_POST['tag_code'];
		$_POST['status'] = 4;
		$_POST['type'] = 1;//兑奖
		$userid = M('js_dj')->where(array('token'=>$this->token))->add($_POST);
		
		//js_sn
		$SN['is_dj']=1;
		M('js_sn')->where(array('token'=>$this->token,'secret'=>$_POST['secret']))->save($SN);
		//用户表
		$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
		$data['name']     = $_POST['reg_name'];
		$data['phone']    = $_POST['reg_phone'];
		$data['address']  = $_POST['tag_code'];
		$data['img1']  = $_POST['img1'];
		$data['img2']  = $_POST['img2'];
		$data['token']    = $this->token;
		$data['openid']   = $this->openid;
		$data['uid']      = $wxid;
		$data['status']   = 1;
		$data['add_time'] = time();
		$data['type'] = 1;
		$data['userid'] = $userid;
		$salesman = M('js_set')->where(array('token'=>$this->token))->getField('salesman');
		$salesphone = M('js_set')->where(array('token'=>$this->token))->getField('salesphone');
		
		if(M('js_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->add($data))
		{	
			senddj($salesphone,$salesman);
			echo json_encode(array('status'=>1));
		}else{
			echo json_encode(array('status'=>2));
		}
		
	}
	//摇奖用户
	public function yj_submit()
	{
		//兑奖表
		$snid = M('js_sn')->where(array('token'=>$this->token,'secret'=>$_POST['secret']))->getField('id');
		$_POST['token'] = $this->token;
		$_POST['sid'] =  $snid;
		$_POST['uid'] = $this->wxusers_id;
		$_POST['d_time']  = time();
		$_POST['s_name'] = $_POST['reg_name'];
		$_POST['s_phone'] = $_POST['reg_phone'];
		$_POST['s_address'] = $_POST['tag_code'];
		$_POST['status'] = 4;
		$_POST['type'] = 2;//摇奖
		$userid = M('js_dj')->where(array('token'=>$this->token))->add($_POST);
		//$id = M('js_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'phone'=>$_POST['reg_phone']));
		//echo $id;die;
		//用户表
		if(M('js_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'phone'=>$_POST['reg_phone']))->find()){
			$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
			
			$res['is_dj'] = 1;
			$t = M('js_sn')
			->where(array('token'=>$this->token,'secret'=>$_POST['secret']))
			->save($res);
			$data['name']     = $_POST['reg_name'];
			$data['phone']    = $_POST['reg_phone'];
			$data['address']  = $_POST['tag_code'];
			$data['token']    = $this->token;
			$data['openid']   = $this->openid;
			$data['img1']  = $_POST['img1'];
			$data['img2']  = $_POST['img2'];
			$data['uid']      = $wxid;
			$data['status']   = 1;
			$data['is_if']   = 0;
			$data['add_time'] = time();
			$data['type'] = 2;//type=2摇奖的用户
			$data['old'] = 1;
			if(M('js_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->add($data))
			{
				echo json_encode(array('status'=>1));
			}else{
				echo json_encode(array('status'=>2));
			}

		}else{
			$res['is_dj'] = 1;
			$t = M('js_sn')
			->where(array('token'=>$this->token,'secret'=>$_POST['secret']))
			->save($res);
			$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
			$data['name']     = $_POST['reg_name'];
			$data['phone']    = $_POST['reg_phone'];
			$data['address']  = $_POST['tag_code'];
			$data['token']    = $this->token;
			$data['openid']   = $this->openid;
			$data['img1']  = $_POST['img1'];
			$data['img2']  = $_POST['img2'];
			$data['uid']      = $wxid;
			$data['status']   = 1;
			$data['is_if']   = 0;
			$data['add_time'] = time();
			$data['type'] = 2;
			$data['userid'] = $userid;
			if(M('js_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->add($data))
			{
				echo json_encode(array('status'=>1));
			}else{
				echo json_encode(array('status'=>2));
			}
		}
		
	}
	
	
	//不中奖页面
	
    public function text(){
        $data=array(
            array(
                'id'=>5
            ),
            array(
                'id'=>6
            ),
        );
        $url="http://testwap.com/index.php?g=User&m=Wxdj&a=get_sn&token=5d8a87bab30de695954b17fc835b9d12";
       $a=new Request();
       p($a->post($a,$data));

    }
	public function expiry()//填写用户信息
	{	
		
		$this->UDisplay('Wxdj_expiry');
	}
	public function zhong()
	{
		$this->UDisplay();
	}

}
class Request
{
    public static function post($url, $post_data = '', $timeout = 5)
    {//curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($post_data != '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        return $file_contents;
    }
}
