<?php

/**
 * 仁豪  李铭
 *
 */
class HnAction extends BaseAction{
    public $token;
    public $wecha_id = 'gh_aab60b4c5a39';
    public $product_model;
    public $product_cat_model;
    public $session_cart_name;
    public $dopenid;
    public $_sTplBaseDir = 'Wap/default/hn';
    public $wxusers_id;//wxusers 表的id
    public $uid = null;

    public function _initialize() {
        if(in_array(ACTION_NAME,array('code','book','re','photo','index','store','yuyue','user_index'))){
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
        $this->assign('
            ', $this->token);
        $this->wecha_id	= isset($_REQUEST['wecha_id']) ? htmlspecialchars($_REQUEST['wecha_id']) : '';
        // if($_REQUEST['openid']){
        //     $_SESSION['openid'] = $_REQUEST['openid'];
        // }
        // $this->openid = $_SESSION['openid'];
        $this->openid = isset($_REQUEST['openid']) ? htmlspecialchars($_REQUEST['openid']) : session('openid');
        $this->session_cart_name = 'session_cart_products_' . $this->openid;
        $this->assign('
            ', $this->openid); 
         
        
        $this->dopenid	= isset($_REQUEST['dopenid']) ? htmlspecialchars($_REQUEST['dopenid']) : '';
        //$this->wecha_id = $this->openid;
        $this->assign('wecha_id', $this->wecha_id);
        //$this->assign('openid', $this->openid);
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

    }
    public function index()
	{
		
		$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		//if(!$id){
		//	header("Location:".U('zhuce',array('token'=>$this->token,'openid'=>$this->openid)));
		//}else{
       /* if(IS_POST){
            $n=$_POST['n']*5;
            $index = M('hn_houses')
                ->field('id,logopic,title,other,jianjie,yonjing1,yonjing2')
                ->where(array('token'=>$this->token,'openid'=>$this->openid))
                ->limit($n,5)->order('sort desc')->select();
            $this->assign('index',$index);
            $html=$this->fetch('./tpl/Wap/default/hn/Hn_index_page.html');
            exit($html);*/
        //}else{
            //$count = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
            $count = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
			$jcount = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('j_yonjing');

            //$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
            $phone = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('phone');
            $aKufu = M('hn_tuijian')->where(array('token'=>$this->token,'uid'=>$id))->select();
            $iKufu = count($aKufu);
            $c_phone = M('hn_users')->where(array('token'=>$this->token,'from_phone'=>$phone))->select();
            $c_phone = count($c_phone);
            $count = $count-$jcount;
            $index = M('hn_houses')->field('id,logopic,title,other,jianjie,yonjing1,yonjing2')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('sort desc')->select();
            $info4 = M('hn_set')->where(array('token'=>$this->token))->getField('info4');

/*
            $yonz = M('hn_yonjing_jl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('add_time desc')->find();
			$xiao = M('Hn_xiaofeijl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('addtime desc')->find();
			$t = M('hn_bank')->field('sum(money) as c')->where(array('token'=>$this->token,'uid'=>$id,'status'=>array('neq','-1')))->order('addtime desc')->find();
			$y = ($yonz['c']-($xiao['c']+$t['c']));
	*/		
	    //佣金余额
	    $yonjing = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
        $yonz = M('hn_yonjing_jl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('add_time desc')->find();
        $xiao = M('Hn_xiaofeijl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('addtime desc')->find();
        $t = M('hn_bank')->field('sum(money) as c')->where(array('token'=>$this->token,'uid'=>$id,'status'=>array('neq','-1')))->order('addtime desc')->find();
        $y = ($yonjing-($xiao['c']+$t['c']));

            $this->assign('iKufu',$iKufu);
			$this->assign('y',$y);
			$this->assign('info4',$info4);
            $this->assign('index',$index);
            $this->assign('c_phone',$c_phone);
            $this->assign('count',$count);
            $this->assign('user_id',$id);//是否是经纪人
            $this->UDisplay();
       // }
		//}

    }
	//我要看房
	public function look()
	{
        /*if(IS_POST){
            $n=$_POST['n']*5;
            $index = M('hn_houses')
                ->field('id,logopic,title,other,jianjie,yonjing1,yonjing2')
                ->where(array('token'=>$this->token,'openid'=>$this->openid))
                ->limit($n,5)->order('sort desc')->select();
            $this->assign('index',$index);
            $html=$this->fetch('./tpl/Wap/default/hn/Hn_index_page.html');
            exit($html);
        }else{*/
            $count = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
            $id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
            $phone = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('phone');
            $aKufu = M('hn_tuijian')->where(array('token'=>$this->token,'uid'=>$id))->select();
            $iKufu = count($aKufu);
            $c_phone = M('hn_users')->where(array('token'=>$this->token,'from_phone'=>$phone))->select();
            $c_phone = count($c_phone);
			$lookinfo = M('hn_set')->where(array('token'=>$this->token))->getField('lookinfo');
            $index = M('hn_houses')->field('id,logopic,title,other,jianjie,yonjing1,yonjing2')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('sort desc')->select();
			$this->assign('iKufu',$iKufu);
            $this->assign('index',$index);
            $this->assign('lookinfo',$lookinfo);
            $this->assign('c_phone',$c_phone);
            $this->assign('count',$count);
            $this->assign('user_id',$id);//是否是经纪人
            $this->UDisplay();
        //}

    }
	//活动规则
	public function rules()
	{	
		$info = M('hn_set')->where(array('token'=>$this->token))->getField('info1');
		$this->assign('info',$info);
		$this->UDisplay();
	}
	//注册协议
	public function xie()
	{	
		$xie = M('hn_set')->where(array('token'=>$this->token))->getField('xie');
		$this->assign('xie',$xie);
		$this->UDisplay();
	}
    //注册经经人
    public function zhuce(){
            $this->UDisplay();
    }
	public function zhu()
	{
            $yzmYz=validCode($this->token,$_POST['phone'],$_POST['code']);
            $yzmYz=json_decode($yzmYz,true);
            if($yzmYz['code']!=0){
               echo json_encode(array('status'=>2));
                die;
            }
            if($_POST['from_phone'] !=''){
                $time = date('Y-m'.'-1 00:00:00');//当前时间
                $month = strtotime($time.'+1 month');//下个月
                $mon = strtotime($time);//当前这个月
                $p= M('Hn_users')
                ->field('from_phone,add_time')
                ->where(array('token'=>$this->token,'from_phone'=>$_POST['from_phone'],
                    'add_time'=>array(array('gt',$mon),array('lt',$month))))
                ->count();

                if($p>20)
                {
                	echo json_encode(array('status'=>4));
                	exit;
                }
        	}

            
			//判断推荐人手机号码
			$phone = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'phone'=>$_POST['phone']))->getField('phone');
			if($phone){
				
				echo json_encode(array('status'=>3));
				die;
			}
			//判断推荐人手机号码
			if($_POST['from_phone'] != ''){
				if($id= M('Hn_users')->where(array('token'=>$this->token,'phone'=>$_POST['from_phone']))->getField('id')){
				   $name= M('Hn_users')->where(array('token'=>$this->token,'phone'=>$_POST['from_phone']))->getField('name');
				   $a=M('Hn_set')->where(array('token'=>$this->token))->getField('yonjing1');
				   if(M('Hn_users')->where(array('id'=>$id))->setInc('yonjing',$a)){
					   $info['token']=$this->token;
					   $info['uid']=$id;
					   $info['status']=1;
					   $info['add_time']=time();
					   $info['type_id']=$id;
					   $info['content']='推荐经纪人';
					   $info['yonjing']=$a;
					   $info['name']=$_POST['name'];
					   M('hn_yonjing_jl')->add($info);
				   }
				 }
			}
			$_POST['openid']=$this->openid;
            $_POST['token']=$this->token;
            $_POST['name']=$_POST['name'];
            $_POST['yonjing']=50;
            $_POST['fid1']=$_POST['fid1'];
            $_POST['fid2']=$_POST['fid2'];
            $_POST['phone']=$_POST['phone'];
            $_POST['from_phone']=$_POST['from_phone'];
            $_POST['add_time']=time();
			$_POST['status'] = 1;
            $_POST['uid']=$this->wxusers_id;
            $uid = M('hn_users')->add($_POST);

            if($uid){
			   $info['token']=$this->token;
	           $info['uid']=$uid;
	           $info['status']=1;
	           $info['add_time']=time();
	           $info['type_id']=$uid;
	           $info['content']='新用户注册';
	           $info['yonjing']=50;
	           $info['name']=$_POST['name'];
	           $d = M('hn_yonjing_jl')->add($info);
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
	}
    //验证手机号码
    public function is_phone(){
        //先去发验证码
        if(IS_POST){
            if(cookie('code_time')){//有cookie值不发码
                $res['status']=2;
            }else{
                $phone=$this->_post('phone');
                $openidYz=sendPhomeCode($this->token,$phone,'hn');
                $openidYz=json_decode($openidYz,true);
                cookie('code_time',time(),120);//设置两分钟
                $res='';
                if($openidYz['code']==0){
                    $res['status']=1;
                    $res['str']="验证码发送成功!";
                }else{
                    $res['status']=0;
                    $res['str']="验证码发送失败!";
                }
            }
            echo json_encode($res);
        }
    }
	//客户
	public function customer()
	{
		
		$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		if(!$id){
			header("Location:".U('zhuce',array('token'=>$this->token,'openid'=>$this->openid)));
			//$this->UDisplay("Hn_zhuce.html");header("Location: ".U('Home/Member/index'));
		}else{
			$tui = M('hn_tuijian')->where(array('token'=>$this->token,'uid'=>$id))->select();
		$usid = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		$this->assign('tui',$tui);
		$this->assign('usid',$usid);
		$this->UDisplay();
		}
		
	}
	//佣金排行榜
	public function randing()
	{
       /* if(IS_POST){
            $n=$_POST['n']*10;*/
            $userid = M('hn_users')->where(array('token'=>$this->token))->getField('id');
            $name = M('hn_users')
			->field('id,name')
            ->where(array('token'=>$this->token))
            ->select();
			//->getField('name');
            /*$rand = M('hn_yonjing_jl')
	            ->field('uid,name,id,sum(yonjing) as yongjin')
	            ->where(array('token'=>$this->token))
	            ->group("uid")
	            ->order("yongjin desc")
	            ->select();*/
			//foreach($rand as $v){
				//if($v[''])
			//}
	           // p($rand);
            //$n++;
			
             /*foreach($rand as $key=>$v)
             {
					foreach($name  as $k =>$t)
					{
						if($t['id'] ==$v['uid'])
						{
							$rand[$key]['user_name'] = $t['name'];
						}
					}
             		
             	//}
             }*/
            //$rand[$key]['id']=$n++;
            
            //$this->assign('rand',$rand);
            //$html=$this->fetch("./tpl/Wap/default/hn/Hn_randing_page.html");
            //exit($html);




        //}else{*/
            //$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
			$rand = M('hn_users')->where(array('token'=>$this->token))->field('name,yonjing,id')->order('yonjing desc')->limit(10)->select();
            //$rand = M('hn_users')->where(array('token'=>$this->token))->field('name,yonjing,id')->order('yonjing desc')->limit(30)->select();
            //$rand = M('hn_yonjing_jl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('add_time desc')->find();
           
            $title=M('hn_set')->where(array('token'=>$this->token))->getField('content1');
            $this->assign('rand',$rand);
            $this->assign('title',$title);
            //  p($rand);die;

            $this->UDisplay();
       // }

	}
	//等级表
	public function deng()
	{
		 $this->UDisplay();
	}
	//推荐买房
	public function recommend()
	{	
		$id = $this->_get('id');
		if($id)
		{
			
			$re_house = M('hn_houses')->where(array('token'=>$this->token,'id'=>$id))->field('title,id')->find();
			$this->assign('id',$id);
			$this->assign('re_house',$re_house);
			
		}else
		{
			$house = M('hn_houses')->where(array('token'=>$this->token))->field('title,id')->select();
			$phone = M('hn_tuijian')->field('phone')->where(array('token'=>$this->token))->select();

			$this->assign('house',$house);
		}
		$this->UDisplay();
	}
	//推荐买房核对
	public function recommend_check()
	{	
		
		if(M('hn_tuijian')->where(array('token'=>$this->token,'phone'=>$this->_post('customer_phone')))->getField('phone'))
		{
			echo json_encode(array('status'=>2));
			exit;
		}
		
		$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		$_POST['name']=$this->_post('customer_name');
		$_POST['phone']=$this->_post('customer_phone');
		$_POST['status']= 0;
		$_POST['hid']=$this->_post('pids');
		$_POST['other']=$this->_post('remark');
		$_POST['add_time']=time();
		$_POST['token']=$this->token;
	//	$uid = M('hn_tuijian')->where(array('token'=>$this->token,'uid'=>$id))->getField('uid');
		$_POST['uid'] = $id;
		if(M('hn_tuijian')->add($_POST)){
                echo json_encode(array('status'=>1));
        }else{
                echo json_encode(array('status'=>0));
        }
	}
	//银行卡
	public function bind()
	{	
		$name = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
		$this->assign('v',$name);
		$this->UDisplay();
	}
	//银行卡绑定核对
	public function bank_check()
	{
		//$_POST['token'] = $this->token;
		//$_POST['openid'] = $this->openid;
		$_POST['update_time'] = time();
		$_POST['alipay']=$this->_post('user2');
		$_POST['idcard']=$this->_post('bank_idcard');
		$_POST['bank']=$this->_post('bank_name');
		$_POST['brand']=$this->_post('bank_card');
		$_POST['img']=$this->_post('img');
		$_POST['img1']=$this->_post('img1');
		$_POST['img2']=$this->_post('img2');
		if(M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($_POST)){
                echo json_encode(array('status'=>1));
        }else{
                echo json_encode(array('status'=>0));
        }
	}
	///个人中心
	public function center()
	{
		$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		if(!$id){
			header("Location:".U('zhuce',array('token'=>$this->token,'openid'=>$this->openid)));
		}
		else{
			/*$pm = M('hn_yonjing_jl')
	            ->field('uid,name,id,sum(yonjing) as yongjin')
	            ->where(array('token'=>$this->token,'openid'=>$this->openid))
	            ->group("uid")
	            ->order("yongjin desc")
	            ->select();*/
	    $yon  = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
		$pm=M('hn_users')->field('id,yonjing,token')->where(array('token'=>$this->token,'yonjing'=>array('gt',$yon)))->count();
		 //foreach($pm as $k=>$v){
		 //	$pm = $k+1;
		 	//if($v['uid']==$id){
		 	//   $pm=$k+1;		
		 	//}else{
			  //  $pm = 1;
			//}
	    //}
		$name = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('name');
		$img = M('wxusers')->where(array('openid'=>$this->openid))->getField('headimgurl');
		$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		$phone = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('phone');
		$yonjing = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
		$count = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
		$jcount = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('j_yonjing');
		$count = $count-$jcount;
		$aKufu = M('hn_tuijian')->where(array('token'=>$this->token,'uid'=>$id))->select();
		$iKufu = count($aKufu);
		$c_phone = M('hn_users')->where(array('token'=>$this->token,'from_phone'=>$phone))->select();
		$c_phone = count($c_phone);
		$brand = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('brand');
		$alipay = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('alipay');
		$usid = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');

        //佣金余额
        $yonz = M('hn_yonjing_jl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('add_time desc')->find();
        $xiao = M('Hn_xiaofeijl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('addtime desc')->find();
        $t = M('hn_bank')->field('sum(money) as c')->where(array('token'=>$this->token,'uid'=>$id,'status'=>array('neq','-1')))->order('addtime desc')->find();
        $y = ($yonjing-($xiao['c']+$t['c']));

        $this->assign('alipay',$alipay);
		$this->assign('y',$y);
		$this->assign('brand',$brand);
		$this->assign('usid',$usid);
		$this->assign('c_name',$name);
		$this->assign('img',$img);
		$this->assign('c_phone',$c_phone);
		$this->assign('iKufu',$iKufu);
		$this->assign('pm',$pm);
		$this->assign('count',$count);
		$this->assign('yonjing',$yonjing);
		$this->UDisplay();
		}
	}
	//银行卡绑定说明
	public function bindGuide()
	{
		$this->UDisplay();
		
	}
	//余额提现
	public function balance()
	{	
		$count = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
		$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		$v = M('hn_bank')->where(array('token'=>$this->token,'uid'=>$id,'status'=>0))->getField('status');
		$mo = M('hn_bank')->where(array('token'=>$this->token,'uid'=>$id,'status'=>0))->field('money')->select();
		$sum=0;
		foreach($mo as $v){
			$sum+=$v['money'];
		}
		$this->assign('cv',$v);
		$this->assign('sum',$sum);
		$this->assign('count',$count);
		
		$this->UDisplay();
	}
	//余额核对
	public function b_check()
	{
		$v = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
		$_POST['addtime'] = time();
		$_POST['uid'] = $v['id'];
		$_POST['brand'] = $v['brand'];
		$_POST['alipay'] = $v['alipay'];
		$_POST['token'] = $this->token;
		$_POST['status'] = 0;
		$_POST['money'] = $this->_post('t_money');
		if(M('hn_bank')->add($_POST))
		{
			echo json_encode(array('status'=>1));
		}else
		{
			echo json_encode(array('status'=>0));
		}
	}
	//微楼书
	public function book()
	{
		$id = $this->_get('id');
		$wei = M('hn_houses')->where(array('token'=>$this->token,'id'=>$id))->field('title,back_pic,id,jianjie,logopic,content,info,info2,info3,lng,lat')->find();
		$this->assign('wei',$wei);
		$this->UDisplay();
	}
	//佣金记录
	public function commission()
	{
		$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		$name = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('name');
		$yon = M('hn_yonjing_jl')->where(array('token'=>$this->token,'uid'=>$id))->order('add_time desc')->select();

		$yonz = M('hn_yonjing_jl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('add_time desc')->find();
		$users_id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		$conusme = M('Hn_xiaofeijl')->where(array('token'=>$this->token,'uid'=>$id))->order('addtime desc')->select();
        // $where['token']=$this->token;
        // $where['uid']  = $uid;
        // $where['status']= array('neq');
		$tixian = M('hn_bank')->where(array('token'=>$this->token,'uid'=>$id,'status'=>0))->order('addtime desc')->select();
		$all = M('hn_bank')->where(array('token'=>$this->token,'uid'=>$id,'status'=>1))->order('addtime desc')->select();
	    
		$count = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
		$xiao = M('Hn_xiaofeijl')->field('sum(yonjing) as c')->where(array('token'=>$this->token,'uid'=>$id))->order('addtime desc')->find();

		$t = M('hn_bank')->field('sum(money) as c')->where(array('token'=>$this->token,'uid'=>$id,'status'=>array('neq','-1')))->order('addtime desc')->find();
		$this->assign('with',$tixian);//提现
        $this->assign('all',$all);
		$tx = intval($t['c'])+ intval($xiao['c']);
        $this->assign('t',$t);
		$this->assign('xiao',$xiao);
        $this->assign('tx',$tx);
		$this->assign('yonz',$yonz);
		$this->assign('yon',$yon);
		$this->assign('count',$count);
		$this->assign('name',$name);
		$this->assign('conusme',$conusme);//消费表
		$this->UDisplay();
	}
	//意见
	public function assin()
	{
		$this->UDisplay();
	}
	//意见
	public function a_check()
	{
		$v = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
		$_POST['add_time'] = time();
		$_POST['uid'] = $v['id'];
		$_POST['token'] = $this->token;
		$_POST['content'] = $this->_post('assign');
		if(M('hn_feedback')->add($_POST))
		{
			echo json_encode(array('status'=>1));
		}else
		{
			echo json_encode(array('status'=>0));
		}
	}
	//修改姓名
	public function editname()
	{	$phone = M('hn_users')
		->where(array('token'=>$this->token,'openid'=>$this->openid))->getfield('phone');
		$this->assign('phone',$phone);
		$this->UDisplay();
	}
	//修改姓名
	public function edit_check()
	{
		$aData['name'] = $this->_post('edit');
		$aData['phone'] = $this->_post('phones');
		$aData['update_time'] = time();
		$id = M('hn_users')
		->where(array('token'=>$this->token,'openid'=>$this->openid))->getfield('id');
		//$name = M('hn_yonjing_jl')
		//->where(array('token'=>$this->token,'uid'=>$id))
		//->getField('name');
		if(M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($aData)){
				/*$data['name'] = $this->_post('edit');
				$t = M('hn_yonjing_jl')
				->where(array('token'=>$this->token,'uid'=>$id))
				->save($data);*/
                echo json_encode(array('status'=>1));
        }else{
                echo json_encode(array('status'=>0));
        }
	}
	//二维码
	public function code()
	{	$share = M('hn_set')->where(array('token'=>$this->token))->getField('share');
		$title = M('hn_set')->where(array('token'=>$this->token))->getField('title');
		$this->assign('share',$share);
		$this->assign('title',$title);
		$this->UDisplay();
	}
	//微楼书详情
	public function title()
	{	
		$id = $this->_get('id');
		$con = M('hn_houses')->where(array('token'=>$this->token,'id'=>$id))->field('id,jianjie,logopic,content,info,info2,lng,lat')->find();
		$this->assign('con',$con);	
		$this->UDisplay();
	}
	//微楼书相册
	public function title1()
	{	
		$id = $this->_get('id');
		$con = M('hn_houses')->where(array('token'=>$this->token,'id'=>$id))->field('info,info2,lng,lat,title')->find();
		$this->assign('con',$con);
        //echo 8;die;
		$this->UDisplay();
	}
	//微楼书区位
	public function title2()
	{	
		$id = $this->_get('id');
		$qu = M('hn_houses')->where(array('token'=>$this->token,'id'=>$id))->field('info,lat,lng,title')->find();
		$this->assign('qu',$qu);	
		$this->UDisplay();
	}
	public function info()
	{	
		$info5 = M('hn_set')->where(array('token'=>$this->token))->getField('info5');
		$this->assign('info5',$info5);	
		$this->UDisplay();
	}//客户
	public function info1()
	{	
		$info5 = M('hn_set')->where(array('token'=>$this->token))->getField('jin');
		$this->assign('info5',$info5);	
		$this->UDisplay();
	}
    public function title4()
    {   
        $photo = M('hn_set')->where(array('token'=>$this->token))->getField('photo');
        $this->assign('photo',$photo);  
        $this->UDisplay();
    }
	//微楼书
	public function title3()
	{	
		$id = $this->_get('id');
		$qu = M('hn_houses')->where(array('token'=>$this->token,'id'=>$id))->field('info3,lat,lng')->find();
		$this->assign('qu',$qu);	
		$this->UDisplay();
	}
	//联系我们
	public function contact()
	{	
		$c = M('hn_set')->where(array('token'=>$this->token))->getField('content');
		$this->assign('c',$c);	
		$this->UDisplay();
	}
	//商城
	public function integralshop()
	{
		$token	= $this->token;
        $openid	= $this->openid;
        $model = M('Integralshop');
        $list = $model->where(array('tp_integralshop.token'=>$token,'tp_integralshop.is_up'=>1))->field('tp_integralshop.*,l.name')->join('left join tp_usercenter_level as l on tp_integralshop.extent = l.id ')->order('starttime asc')->select();
        foreach($list as $k=>$val){
            $ikucount =intval($val['num']) - intval(M('Integralshop_individual')->where(array('lid'=>$val['id'],'token'=>$token))->count());

            $list[$k]['ikucount'] = $ikucount;
        }
		$cou = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
        $this->assign('data',$list);
        $this->assign('cou',$cou);
        $this->UDisplay();
	}
	//购买
	/*public function exchange()
	{
		$_POST['excid'] = $this->_POST['exc_id'];
		$_POST['point'] = $this->_POST['point'];
		$_POST['excid'] = $this->_POST['title'];
		$user_yon = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
		if($user_yon<$_POST['point']){
			echo json_encode(array('status'=>1));
		}
		
	}*/
	//经纪人页面
    public function jin()
    {
        $phone = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('phone');
        $f = M('hn_users')->where(array('token'=>$this->token,'from_phone'=>$phone))->order('add_time desc')->select();
        //$jin = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'phone'=>$f))->select();
        $this->assign('jin',$f);
        $this->UDisplay();
	}
	public function kehu()
	{
		$id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		$kuhu = M('hn_tuijian')->where(array('token'=>$this->token,'uid'=>$id))->order('add_time desc')->select();
        $this->assign('kehu',$kuhu);
        $this->UDisplay();
	}
	//照片
	public function photo()
	{
		$photo = M('wx_photo')->where(array('token'=>$this->token))->order('add_time desc')->select();
        $share = M('hn_set')->where(array('token'=>$this->token))->getField('sharep');
        $title = M('hn_set')->where(array('token'=>$this->token))->getField('titlep');
        $img = M('hn_set')->where(array('token'=>$this->token))->getField('p_img');
        $v = M('hn_set')->where(array('token'=>$this->token))->getField('photo');
        $this->assign('title',$title);
        $this->assign('share',$share);
        $this->assign('openid',$this->openid);
        $this->assign('photo',$v);
        $this->assign('img',$img);
		$this->assign('p',$photo);
		$this->UDisplay();
	}
	//加载图片
	public function addphoto()
	{
			$n=$_POST['n']*4;
            $photo = M('wx_photo')->where(array('token'=>$this->token))->order('add_time desc')->limit($n,4)->select();
            $n++;
            $this->assign('photo',$photo);
            $html=$this->fetch("./tpl/Wap/default/hn/Hn_photo_page.html");
            exit($html);
	}
	public function re()
	{
		$photo = M('wx_photo')->where(array('token'=>$this->token))->order('click_num desc')->select();
         $img = M('hn_set')->where(array('token'=>$this->token))->getField('p_img');
        $v = M('hn_set')->where(array('token'=>$this->token))->getField('photo');
        $share = M('hn_set')->where(array('token'=>$this->token))->getField('sharep');
        $title = M('hn_set')->where(array('token'=>$this->token))->getField('titlep');
		$this->assign('p',$photo);
        $this->assign('title',$title);
        $this->assign('share',$share);
        $this->assign('photo',$v);
        $this->assign('img',$img);
       
		$this->UDisplay();
	}
	//我的照片
	public function wo()
	{
		$photo = M('wx_photo')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->select();
		$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
		$com = M('wx_photo_comment')->join('tp_wxusers on tp_wx_photo_comment.uid=tp_wxusers.id')->where(array('tp_wx_photo_comment.uid'=>$wxid))->order('tp_wx_photo_comment.add_time desc')->select();
		//$all = M('wx_photo_comment')->join('join tp_wx_photo on tp_wx_photo.id = tp_wx_photo_comment.pid')->join('join tp_wxusers on tp_wxusers.id = tp_wx_photo_comment.uid')->where(array('tp_wx_photo.openid'=>$this->openid,'tp_wx_photo_comment.uid'=>$wxid))->select();
		//$this->assign('all',$all);
		$this->assign('p',$photo);
		$this->assign('com',$com);
		$this->UDisplay();
	}
	//点赞
	public function addNum(){
        $id = $this->_post('id');
		$db = M('wx_photo');
        if(!empty($id)){
            $where = array('token'=>$this->token,'id'=>$id);
            $cnum = $db->where($where)->setInc('click_num');
            $c = M('wx_photo')->where($where)->getField('click_num');
            echo $c;
        }
       
        
	}

    //取消点赞
    public function comm(){
        $db = M('wx_photo');
        $id = $this->_post('id');
        if(!empty($id)){
            $where = array('token'=>$this->token,'id'=>$id);
            $cnum = $db->where($where)->setDec('click_num');
            $c = M('wx_photo')->where($where)->getField('click_num');
            echo $c;
        }
    }



	//评论
	public function comment(){
		$pid = $this->_get('pid');
		$com = M('wx_photo')->where(array('token'=>$this->token,'id'=>$pid))->find();
		$wxid = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
		$new = M('wx_photo_comment')
				->join("join tp_wxusers on tp_wxusers.id = tp_wx_photo_comment.uid")
				->field("tp_wxusers.nickname,tp_wx_photo_comment.content,tp_wx_photo_comment.add_time")->where(array('tp_wx_photo_comment.pid'=>$pid))->order('tp_wx_photo_comment.add_time desc')->select();	
		$this->assign('com',$com);
		$this->assign('wxid',$wxid);
		session('openid',$this->openid);
		$this->assign('new',$new);
		$this->UDisplay();
	}
	//添加照片墙
	public function add_photo()
	{
		$data['token'] = $this->token;
		$data['openid'] = $this->openid;
		$data['create_time'] = time();
		$data['media_id'] = time();
		$data['msg_id'] = time();
		$data['click_num'] = 0;
		$data['comment_num'] = 0;
		$data['pic'] = $this->_post('t');
		$data['add_time'] =date('Y-m-d H:i:s',time());
		$data['local_pic'] = $this->_post('t');
		if(M('wx_photo')->where(array('token'=>$this->token,'openid'=>$this->openid))->add($data))
		{
			echo json_encode(array('status'=>1));
		}else
		{
			echo json_encode(array('status'=>2)); 
		}
	}

	//评论成功
	public function comment_check()
	{	
		$wxuser_id = M('wxusers')->where(array('openid'=>$this->openid))->getField('id');
		$_POST['pid'] = $this->_post('pid');
		$_POST['content'] = $this->_post('content');
		$_POST['uid'] = $wxuser_id;
		$_POST['add_time'] = time();
		M('wx_photo')->where(array('id'=>$this->_post('pid')))->setInc('comment_num');
		$t = M('wx_photo_comment')->add($_POST);
		if($t){
			echo 1;
		}else{
			echo 2;
		}
	}
	//新的商城
	public function shop()
	{
		$shop = M('hn_shop')->where(array('token'=>$this->token,'status'=>0))->order('sort desc')->select();
		$add = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->field('address,s_name,s_phone')->find();
		$this->assign('add',$add);
		$this->assign('shop',$shop);
		$this->UDisplay();
	}	
    //订单列表
    public function order_list(){
        $uid=M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
        $list=M('hn_xiaofeijl')->where(array('token'=>$this->token,'uid'=>$uid))->order('addtime desc')->select();
        $this->assign('list',$list);
		$this->UDisplay();
    }
	//确认收货
	public function affirm()
	{
		$id = $this->_post('id');
		//$xiao_status = M('hn_xiaofeijl')->where(array('token'=>$this->token,'id'=>$id))->getField('status');
		$data['status'] = 2;
		$ver = M('hn_xiaofeijl')->where(array('token'=>$this->token,'id'=>$id))->save($data);
		if($ver)
		{
			echo 1;
		}else
		{
			echo 2;
		}
	}
    public function address(){
		$name = $this->_get('s_name');
		$id = $this->_get('id');
		$price = $this->_get('price');
		$add = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->field('address,s_name,s_phone')->find();
		$this->assign('name',$name);
		$this->assign('id',$id);
		$this->assign('price',$price);
		$this->assign('add',$add);
        $this->UDisplay();
    }
	/*public function add_check()
	{
		$data['s_name'] = $this->_post('s_name');
		$data['s_phone'] = $this->_post('s_phone');
		$data['address'] = $this->_post('address');
		$data['update_time'] = time();
		$uid = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);
		if($uid)
		{
			echo 1;
		}
		else
		{
			echo 2;
		}
	}*/
	public function shop_buy()
	{	
		$users_id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');
		$users_yonjing = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yonjing');
		if($users_yonjing<$this->_post('s_price'))
		{
			echo json_encode(array('status'=>3));
			exit;
		}
		$_POST['pid'] = $this->_post('s_id');
		$_POST['status'] = 0;
		$_POST['uid'] = $users_id;//对应用户的id
		$_POST['addtime'] = time();
		$_POST['token'] = $this->token;
		$_POST['yonjing'] = $this->_post('s_price');
		$_POST['s_name'] = $this->_post('s_name');
		
		$_POST['shop_name'] = $this->_post('shou_name');
		$_POST['phone'] = $this->_post('shou_phone');
		$_POST['address'] = $this->_post('shou_address');
		$_POST['other'] = $this->_post('other');
        $_POST['pic']=M('hn_shop')->where(array('id'=>$_POST['s_id']))->getField('pic');
		
		$data['s_name'] = $this->_post('shou_name');
		$data['s_phone'] = $this->_post('shou_phone');
		$data['address'] = $this->_post('shou_address');
		$data['update_time'] = time();
		$uid = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);
		//$us = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->setDec('yonjing',$this->_post('s_price'));
		//消费加在一起
		$us = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->setInc('j_yonjing',$this->_post('s_price'));
		$shop_id = M('hn_xiaofeijl')->add($_POST);
		if($shop_id)
		{
			echo json_encode(array('status'=>1));
		}
		else
		{
			echo json_encode(array('status'=>2));
		}
	}
}