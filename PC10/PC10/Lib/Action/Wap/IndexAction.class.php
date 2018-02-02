<?php
class IndexAction extends BaseAction{
	const HOME_BASE = 1;
    public $tpl;	//微信公共帐号信息
	private $info;	//分类信息
	private $wecha_id;
	private $copyright;
	private $pre_url ;
	public function _initialize(){
		parent::_initialize();
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$where['token']=$this->_get('token','trim');
		$tpl=D('Wxuser')->where($where)->find();
		$info=M('Classify')->where(array('token'=>$this->_get('token'),'pid'=>0,'status'=>1))->order('sorts asc')->select();
		$gid=D('Users')->field('gid')->find($tpl['uid']);
		$copy=D('User_group')->field('iscopyright')->find($gid['gid']);	//查询用户所属组
		$this->copyright=$copy['iscopyright'];
        $this->wecha_id = $this->_get('openid','intval');
		if(!$this->wecha_id){
            $this->wecha_id=$this->_get('wecha_id','intval');
        }

        foreach($info as $key=>$value){
            if ($value['url'] != null && !strpos($value['url'],'wapwei.com')) {
                $info[$key]['url'] = $value['url'] . "?openid=" . $this->wecha_id . "&token=" . $this->token . "&";
            }
        }
		for ($i=0; $i < count($info); $i++) {
				if(strpos($info[$i]['url'],'m=Lottery'))
			    	$info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Guajiang'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Product'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Card'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Selfform'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Car_baoyang'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Coupon'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Zadan'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Medical'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Vote'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');
				else if (strpos($info[$i]['url'],'m=Reservation'))
			        $info[$i]['url'] .= '&wecha_id='.$this->_get('wecha_user');


			}


		$diymen=M('Diymen_class');
		$pid=0;
		$diymens=$diymen->where(array('token'=> $where['token'],'pid'=>$pid))->select();
		$dataArray = array();
		foreach($diymens as $key=>$value){
            $diymen_child = $diymen->where(array('pid'=>$value['id'],'token'=>$where['token']))->select();
            $dataArray[$value['id']] = $diymen_child;
        }

        $this->assign('dataArray', $dataArray);

		$this->assign('diy_child', $array);
		$this->assign('diymens',$diymens);
		$this->info=$info;
		$this->tpl=$tpl;
	}


	public function classify(){
		$this->assign('info',$this->info);

		$this->display($this->tpl['tpltypename']);
	}

	public function index(){
        $where['token']=$this->_get('token');
        $where['ifscroll'] = 1;
        $this->pre_url = substr($_SERVER["HTTP_REFERER"],20);
        $flash=M('Flash')->where($where)->order('sorts asc')->select();
        $speeddial=M('speeddial')->where($where)->find();
		$count=count($flash);
		if(M('App')->where(array('state'=>1,'token'=>$where['token']))->find() && !isset($_COOKIE['is_show'])){
			$this->redirect('Wap/Index/showApp',array('token'=>$where['token']));
		}else{
			$this->assign('flash',$flash);
			$this->assign('info',$this->info);
			$this->assign('num',$count);
			$this->assign('tpl',$this->tpl);
			$this->assign('speeddial',$speeddial);
			$this->assign('copyright',$this->copyright);
			$aValue = M('Guangwang_base_set')->where(array(
            'token' => $this->token,
            'type'  => self::HOME_BASE,
            ))->find();
            $aValue = $aValue ? json_decode($aValue['value'],true) : array();
			$aValue['bottom_txt'] = explode("\r\n", $aValue['bottom_txt']);
			$bgImage = M('imag')->where(array(
				'token'=>$this->token
				))
			    ->order('add_time desc')
			    ->field('pic')
			    ->find();
			switch ($this->tpl['tpltypename']) {
				case 'tpl_162_index_new':
					$this->assign('value',$aValue);
					$this->assign('bg',$bgImage);
					break;
			}
		//	p($this->info);die;
			//echo $this->tpl['tpltypename'];die;

			$this->display($this->tpl['tpltypename']);
		}
	}

	public function showApp(){
		if(!isset($_COOKIE['is_show'])){
			$result = M('App')->where(array('state'=>1,'token'=>$this->token))->find();
			$this->assign('result',$result);
			$this->display();
		}else{
			setcookie("is_show", "1", time()+3600*24*7);
            $this->redirect('Wap/Index/Index',array('token'=>$where['token']));
		}
	}

	public function lists(){
		//我的控制器
		$table=M('Classify');
		$data['id']=$this->_get('classid');
		$data['token']=$this->_get('token');
		$v = $table->where(array('id'=>$data['id'],'token'=>$data['token']))->find();
		//p($v);die;
		if(($data['token'] == 'b52cb95923ac3d962e8155ec9fcd11fd') && ($v['name']=='创业大赛')){
			$info = $table->where(array('pid'=>$data['id'],'token'=>$data['token']))->select();
			$this->assign('info',$info);
			$this->tpl['tplchannelid']=9;
			$this->display('new9_channel');
			die;
		}
        if($table->where(array('pid'=>$data['id'],'token'=>$data['token']))->find()){

        	$this->redirect('Index/channel', array('id'=>$data['id'],'token'=>$data['token'],'openid'=>$_GET['openid']));
        }else{
			//上述我的控制器
			$where['token']=$this->_get('token','trim');
			$db=D('Img');
			$p=$this->_get('p','intval',0);
			if($p) $p-=1;
			$where['classid']=$this->_get('classid','intval');
			$res=$db->where($where)->order('store')->limit(($p*5).',5')->select();
			//$res=$db->where($where)->select();
			$result=$db->where($where)->order('store')->select();
			$count=$db->where($where)->count();
			$p+=1;
			$this->assign('page',(ceil($count/5)));
			$this->assign('p',$p);

			$phpSelf = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
			$queryString = '';
			if (!empty($_SERVER['QUERY_STRING']))
			{
			 $queryString = '?' . $_SERVER['QUERY_STRING'];
			}
	            $fwhere['token']=$this->_get('token');
	            // $where['pid'] = $this->_get('id');
	            $fwhere['ifscroll'] = 2;
	            $fresult=M('Flash')->where($fwhere)->limit('0,6')->select();
	            $this->assign('flash' ,$fresult);
		        $this->assign('pre_url',($phpSelf . $queryString));

	            foreach($result as $key=>$value){
	                if ($value['url'] != null && !strpos($value['url'],'wapwei.com')) {
	                    $result[$key]['url'] = $value['url'] . "?openid=" . $this->wecha_id . "&token=" . $this->token . "&";
	                }
	            }

			$this->assign('info',$this->info);
			$this->assign('tpl',$this->tpl);
			$this->assign('res',$res);
			$this->assign('result',$result);
			$this->assign('copyright',$this->copyright);


            if($this->token=='3db7fee419649f8be761dfc4f6b42ecc'){//德亿堡用这里
                $this->display("tpl/Wap/default/index_dyb_list.html");
            }else {

                $this->display($this->tpl['tpllistname']);
            }

	   }
	}

	public function content(){
	        $type = isset($_GET['type']) ? $_GET['type'] : 2;
		$id=$this->_get('id','intval');
		$db=M('Img');
		$where['token']=$this->_get('token','trim');
		// $where['id']=array('neq',$id);
		$where['id']=$id;

		$lists=$db->where($where)->limit(5)->order('uptatetime')->select();
        foreach($lists as $key=>$value){
            if ($value['url'] != null && !strpos($value['url'],'wapwei.com')) {
                $lists[$key]['url'] = $value['url'] . "?openid=" . $this->wecha_id . "&token=" . $this->token . "&";
            }
        }
	    /*
         * 引入微信js接口
         */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);


        /*
         * 存在dopenid
         */
        $dopenid = $this->_get('dopenid','trim');
        if($dopenid){
            D('Img_zhuanfa')->where(array('token'=>$this->token,'openid'=>$dopenid,'article_id'=>$id))->setInc('times',1);
        }


		// $where['id']=$this->_get('id','intval');
		$res=$db->where($where)->find();
        /*判断是否开启红包*/
        if($res['is_redpay'] !=0){
            if($type == 2){
	            $cashs = new cash($this->token,$res['is_redpay'],$this->openid);

	            $str = $cashs->cash_info();
		    }

            $this->assign('redinfo',$str);
        }

        if($res['url'] != ''){
		    $url = htmlspecialchars_decode(trim($res['url']));
		    header('Location:'.$url);
		    exit;
	    }

        /*进入图文页将信息存入tp_course_user表*/
        $oUrseModel = M('Course_user');
        if($res['is_zf'] == 1 AND $this->openid){
            $aUser = $oUrseModel->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))->find();
            if(!$aUser){
                $oUrseModel->add(array(
                    'token'=>$this->token,
                    'openid'=>$this->openid,
                    'from_openid'=>isset($_GET['dopenid'])?$_GET['dopenid']:'',
                    'add_time'=>time(),
                    'date'=>date('Y-m-d',time()),
                    'score'=>0
                ));
            }
        }
		$this->assign('info',$this->info);	//分类信息
		$this->assign('lists',$lists);		//列表信息
		$this->assign('res',$res);			//内容详情;
		$this->assign('tpl',$this->tpl);
		$this->assign('copyright',$this->copyright);	//版权是否显示
		$data['click'] = $res['click']+1;
        $db->where(array('id'=>$id))->save($data);
		$this->display($this->tpl['tplcontentname']);


	}
	public function channel(){
		$db=M('Flash');
		$spee=M('Speeddial');
		$where['token']=$this->_get('token');
	        $where['pid'] = $this->_get('id');
	    if($where['token'] == 'b52cb95923ac3d962e8155ec9fcd11fd'){
	    	$bg=M('Classify')->where(array('id'=>$_GET['id']))->getField('bg');
	    	$this->assign('bg',$bg);
		}else{
			$bg = '/tpl/static/wapweiui/weiweb/new_213/images/2.png';
			$this->assign('bg',$bg);
		}
		$result=$db->where(array('token'=>$this->_get('token'),'ifscroll'=>2))->limit('0,6')->select();
		$this->assign('flash' ,$result);
		$db_1=M('Classify');

		$result_1=$db_1->where($where)->order('sorts desc,id desc')->select();


        foreach($result_1 as $key=>$value){
            if ($value['url'] != null && !strpos($value['url'],'wapwei.com')) {
                $result_1[$key]['url'] = $value['url'] . "?openid=" . $this->wecha_id . "&token=" . $this->token . "&";
            }
        }

		$res_1=$spee->where(array('token'=>$where['token']))->find();
		if(!$result_1){
			$this->redirect('Index/lists', array('token'=>$where['token'],'classid'=>$where['pid'],'openid'=>$_GET['openid']));
		}else{
			//	echo $this->tpl['tplchannelname'];die;
			$this->assign('info' ,$result_1);

			$this->assign('res_1', $res_1);
            if($where['pid'] == '3764'){
                $this->tpl['tplchannelid'] = 8;
                $this->tpl['tplchannelname'] ="new8_channel";

            }
            $this->display($this->tpl['tplchannelname']);//显示无限级分类的东西


	    }
	}

    public function yifujin_baojia(){
        $db=M('Img');
        $where['token']=$this->_get('token','trim');
        //$where['id']=array('neq',(int)$_GET['id']);
        $where['id']=42;
        $lists=$db->where($where)->limit(5)->order('uptatetime')->select();
        //$where['id']=$this->_get('id','intval');
        $where['id']=42;
        $res=$db->where($where)->find();
        $this->assign('info',$this->info);	//分类信息
        $this->assign('lists',$lists);		//列表信息
        $this->assign('res',$res);			//内容详情;
        $this->assign('tpl',$this->tpl);
        $this->assign('copyright',$this->copyright);	//版权是否显示
        $this->display('baojia_content');
        $data['click'] = $res['click']+1;
        $db->where('id='.$where['id'])->save($data);

    }

    public function yifujin_baojia2(){
        $db=M('Img');
        $where['token']=$this->_get('token','trim');
        //$where['id']=array('neq',(int)$_GET['id']);
        $where['id']=49;
        $lists=$db->where($where)->limit(5)->order('uptatetime')->select();
        //$where['id']=$this->_get('id','intval');
        $where['id']=49;
        $res=$db->where($where)->find();
        $this->assign('info',$this->info);	//分类信息
        $this->assign('lists',$lists);		//列表信息
        $this->assign('res',$res);			//内容详情;
        $this->assign('tpl',$this->tpl);
        $this->assign('copyright',$this->copyright);	//版权是否显示
        $this->display('baojia_content2');
        $data['click'] = $res['click']+1;
        $db->where('id='.$where['id'])->save($data);

    }

	public function flash(){
		$where['token']=$this->_get('token','trim');
		$flash=M('Flash')->where($where)->select();
		$count=count($flash);
		$this->assign('flash',$flash);
		$this->assign('info',$this->info);
		$this->assign('num',$count);
		$this->display('ty_index');
	}

    public function sendmsg(){
        $swhere['token']=$this->token;
        $swhere['id']=6000;
        $imgsacticle = M('Img')->where($swhere)->find();
    	news($this->token,$this->openid,array(
		   	'title'=>$imgsacticle['title'],
		   	'description'=>$imgsacticle['title'],
		   	'url'=>C('site_url').'index.php?g=Wap&m=Index&a=content&id='.$imgsacticle['id'].'&redarticle_id='.$id.'&token='.$this->token."&openid=".$this->openid,
		   	'picurl'=>C('site_url').$imgsacticle['pic']
	    ));
	}

    public function zhuanfa(){
        $db=D('Img_zhuanfa');
        $article_id = $_POST['article_id'];
        $article = M('Img')->where(array('token'=>$this->token,'id'=>$article_id))->find();
        $data['openid'] = $this->openid;
        $data['article_id'] = $article_id;
        $data['score'] = $article['score'];
        $data['token'] = $this->token;
        $data['add_time'] = time();
        $data['date'] = date('Y-m-d',time());
        $data['from_openid'] = $_GET['dopenid']?$_GET['dopenid']:'';
        $data['nick_name'] = $this->wxusers['nickname'];
        $data['headurl'] = $this->wxusers['headimgurl'];
        if($db->where(array('token'=>$this->token,'article_id'=>$article_id,'openid'=>$this->openid))->find()){
            if($db->where(array('token'=>$this->token,'article_id'=>$article_id,'openid'=>$this->openid))->setInc('share_times',1)){
                echo $this->encode(array('code' => 0, 'msg' => '分享成功','info'=>1));
            }else{
                echo $this->encode(array('code' => -1, 'msg' => '分享失败'));
            }
        }else {
            if ($db->add($data)) {
                /*$aImger = M('Img')->where(array(
                    'token'=>$this->token,
		            'id'=>$article_id
                ))->find();
                $bSetUser = M('Course_user')->where(array(
                    'token'=>$this->token,
                    'openid'=>$this->openid
                ))->setInc('score',$aImger['score']);
                if($_GET['dopenid']){
                    $bSetUsers = M('Course_user')->where(array(
                        'token'=>$this->token,
                        'openid'=>$_GET['dopenid']
                    ))->setInc('score',$aImger['score']);
                }
                */
	            if($_GET['redarticle_id']){
		            $res=M('Img')->where(array('token'=>$this->token,'id'=>$_GET['redarticle_id']))->find();
			        if($res['is_redpay'] !=0){
				        $cashs = new cash($this->token,$res['is_redpay'],$this->openid);
			            $str = $cashs->cash_info();
			        }
		        }

			    echo $this->encode(array('code' => 0, 'msg' => $str));
            }else{
                echo $this->encode(array('code' => -1, 'msg' => '分享失败'));
            }

        }
    }

}
