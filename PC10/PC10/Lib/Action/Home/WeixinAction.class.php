<?php
class WeixinAction extends Action {
    private $token;
    private $fun;
    private $data = array();
    private $my = '万普微信公众平台';
    private $users = null;
    private $wxusers = null;
    private $is_join=1;

    public function index() {
        $this->token = $this->_get('token');
        if (!isset($this->token)) {
            return false;
        }
        $weixin = new Wechat($this->token);
        $this->users = $weixin->res;
        /*
        if($this->users['uid'] == 9){
            $this->is_join = 0;
        }
        */

        $useradmin = M('Users')->field('is_join')->where(array('id'=>$this->users['uid']))->find();
        if($useradmin['is_join'] == 0 || $useradmin['is_join'] == null){
            $this->is_join = 0;
        }else{
            $this->is_join = 1;
        }

        //$this->is_join = $this->users['is_join'];
        $this->wxusers = $weixin->wxusers;
        $data = $weixin->request();
        $this->data = $weixin->request();
        $this->my = C('site_my');
        list($content, $type) = $this->reply($data);
        $weixin->response($content, $type);
    }
    public function xitie() {
        $this->requestdata('other');
        $pro = M('xt')->where(array(
            'token' => $this->token
        ))->find();
        return array(
            array(
                array(
                    $pro['title'],
                    strip_tags(htmlspecialchars_decode($pro['message'])) ,
                    C('site_url') . $pro['fmurl'],
                    C('site_url') . 'index.php?g=Wap&m=Xt&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $pro['id']
                )
            ) ,
            'news'
        );
    }

    /*根据接收数据类型,进行判断回复信息*/
    private function reply($data) {

        /*
         * 必关注
         */

        $wxUsersModel = M('Wxusers');
        $wxUsersModel->openid = $data['FromUserName'];
        $wxUsersModel->uid = $this->users['id'];
        $wxUsersModel->subscribe_time = $data['CreateTime'];
        if ($usersres = $wxUsersModel->where(array(
            'openid' => $data['FromUserName'],
            'uid' => $this->users['id']
        ))->find()) {
            if ($usersres['status'] == 0) {
                $wxUsersModel->id = $usersres['id'];
                $wxUsersModel->status = 1;
                $wxUsersModel->update_time = time();
                $wxUsersModel->from_sence = $data['EventKey'];
                $wxUsersModel->save();
            }
            $wxUser = M('Wxuser');
            $wxUser->where(array(
                'id' => $this->users['id']
            ))->data(array(
                    'wx_openid' => $data['ToUserName']
                ))->save();
        } else {
            $datas = array();
            $datas['status'] = 1;
            $datas['uid'] = $this->users['id'];
            $datas['add_time'] = time();
            $datas['update_time'] = time();
            $datas['openid'] = $data['FromUserName'];
            $datas['subscribe_time'] = $data['CreateTime'];
            $datas['from_sence'] = $data['EventKey'];
            $wxUsersModel = M('Wxusers');
            $wxUsersModel->add($datas);
        }
        if($usersres['is_kf_time'] > time()){
            $weixin = new Wechat($this->token);
            $weixin->responsekf();
        }


        if ('CLICK' == $data['Event']) {
            $data['Content'] = $data['EventKey'];
            if($data['Content'] == 'WAPWEIKEFU'){
                if(($this->users['service_type_info'] == 2 ||  $this->users['service_type_info'] == 1 || $this->users['service_type_info'] == 0) && $this->users['verify_type_info'] >=0){
                    if($res = M('Wxusers')->where(array('openid'=>$this->data['FromUserName'],'uid'=>$this->users['id']))->find()){
                        if(M('Wxusers')->where(array('openid'=>$this->data['FromUserName'],'uid'=>$this->users['id']))->save(array('is_kf_time'=>time()+180))){
                            $params['content'] = $res['nickname']."您好!".$this->users['name'].'正在为您服务,请问有什么可以帮助您的呢?';
                            return array(
                                $params['content'],
                                'text'
                            );
                        }else{
                            return array(
                                '系统错误哦',
                                'text'
                            );
                        }
                    }else{
                        return array(
                            '您还没有关注我们的公众号哦,我们的公众号是'.$this->users['name'],
                            'text'
                        );
                    }
                }else{
                    return array(
                        '此帐号不支持微信多客服哦',
                        'text'
                    );
                }
            }
        }

        /*添加二维码扫面事件*/
		if ('SCAN' == $data['Event']) {
			/*获取员工信息*/
			$staffId = $data['EventKey'];

			$substr = substr($staffId,0,3);
			$qrcodearr = C('qrcode');
            $substr_g=substr($staffId,0,1);
			if(array_key_exists($substr,$qrcodearr)){
			    $return_data = $qrcodearr[$substr]($staffId,$this->token,$this->data['FromUserName']);
			    return array(
			            $return_data ,
			            'news'
			    );
			}elseif(array_key_exists($substr_g,$qrcodearr)){//国泰安的   9
                $return_data = $qrcodearr[$substr_g]($staffId,$this->token,$this->data['FromUserName']);
                return array(
                    $return_data ,
                    'news'
                );
            }else{
    			$staffModel = M('staff');
    			$staffInfo = $staffModel->where(array('id'=>$staffId))->find();
    			/*这个事件是已经关注了,就可以找到对应的粉丝*/
    			$carModel = M('service_car');
    			$carInfo = $carModel->where(array('wxuser_id'=>$this->users['id'], 'wxusers_id'=>$this->wxusers['id']))->find();

    			$orderModel = M('server_order');
    			$orderId = date('ymdHis').rand(1000,2000);
    			$InsertData = array(
    						'order_id'=>$orderId,
    						'wxuser_id'=>$this->users['id'],
    						'store_id'=>$staffInfo['belong_id'],
    						'status'=>3,
    						'appoint_time'=>date('Y年m月d日 H:i:s', time()),
    						'check_time'=>time(),
    						'consume_type'=>0,
    						'wxusers_id'=>$this->wxusers['id'],
    						'order_user'=>$carInfo['car_username'],
    						'order_user_tel'=>$carInfo['user_phone'],
    						'server_car_no'=>$carInfo['car_licence'],
    						'server_ok_time'=>time(),
    						'server_staff_id'=>$staffInfo['staff_id'],
    						'server_staff_name'=>$staffInfo['name'],
    						'order_is_read'=>1,
    						'order_is_appraise'=>0
    					);
    			$orderBack = $orderModel->add($InsertData);
    // 			file_put_contents('123.txt', C('site_url') . '/index.php?g=Wap&m=Service&a=appraise&token=' . $this->token . '&openid=' . $this->data['FromUserName'] .'&staffId='.$staffInfo['staff_id'].'&orderId= '.$orderId.'.&sgssz=mp.weixin.qq.com');
    			if ($orderBack == true) {
    				return array(
    						array(
    								array(
    										'您的订单号是'.$orderId."\r\n".'我们非常在意您对我们的评价',
    										'真的希望您给出宝贵的意见',
    										C('site_url').'./tpl/static/wapweiui/service/images/logo-mask-2x.png',
    										C('site_url') . '/index.php?g=Wap&m=Service&a=appraise&token=' . $this->token . '&openid='.$this->data['FromUserName'].'&staffId='.$staffInfo['staff_id'].'&orderId='.$orderId
    								)
    						) ,
    						'news'
    				);

    			}
			}

		}


        if ('subscribe' == $data['Event']) {
            /**
             * 修改调用受权
             */
            $api = M('Diymen_set')->where(array('token' =>$this->token))->find();
            //dump($api);
            $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $api['appid'] . '&secret=' . $api['appsecret'];
            //echo $url_get;exit;
            $json = json_decode(file_get_contents($url_get));
            $access_token=$json->access_token;
            if(!$access_token){//上面没有得到$access_token时，用另一方法去获得
                $wxuserdata = M('Wxuser')->where(array('token'=>$this->token))->find();
                $componseaccess = $this->getAccesstoken();
                $componseaccess=$componseaccess['component_access_token'];
                if($componseaccess['component_access_token']) {
                    $accessurl = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=" . $componseaccess['component_access_token'];
                    $repostdata = array();
                    $repostdata['component_appid'] = 'wxe7be6810523b9ea2';
                    $repostdata['authorizer_appid'] = $wxuserdata['authorizer_appid'];
                    $repostdata['authorizer_refresh_token'] = $wxuserdata['authorizer_refresh_token'];
                    $repostdata = $this->encode($repostdata);
                    $json = $this->api_notice_increment($accessurl, $repostdata);
                    $json = json_decode($json);
                    $access_token = $json->authorizer_access_token;
                }
            }

            if($access_token){
                $getuserinfourl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $data['FromUserName']."&lang=zh_CN";
		$userinfocontent = file_get_contents($getuserinfourl);
                $userinfodata = json_decode($userinfocontent);
            }








            $wxUsersModel = M('Wxusers');
            $wxUsersModel->openid = $data['FromUserName'];
            $wxUsersModel->uid = $this->users['id'];
            $wxUsersModel->subscribe_time = $data['CreateTime'];

            if ($usersres = $wxUsersModel->where(array(
                'openid' => $data['FromUserName'],
                'uid' => $this->users['id']
            ))->find()) {
                if ($usersres['status']) {
                    $wxUsersModel->id = $usersres['id'];
                    $wxUsersModel->status = 1;
                    $wxUsersModel->update_time = time();
                    $wxUsersModel->from_sence = $data['EventKey'];

		    if(isset($userinfodata->openid)){
	                    $wxUsersModel->nickname = $userinfodata->nickname;
	                    $wxUsersModel->sex = $userinfodata->sex;
	                    $wxUsersModel->language = $userinfodata->language;
	                    $wxUsersModel->city = $userinfodata->city;
	                    $wxUsersModel->province = $userinfodata->province;
	                    $wxUsersModel->country = $userinfodata->country;
	                    $wxUsersModel->headimgurl = $userinfodata->headimgurl;
                    }

                    $wxUsersModel->save();
                }
                $wxUser = M('Wxuser');
                $wxUser->where(array(
                    'id' => $this->users['id']
                ))->data(array(
                        'wx_openid' => $data['ToUserName']
                    ))->save();
            } else {
                $datas = array();
                $datas['status'] = 1;
                $datas['uid'] = $this->users['id'];
                $datas['add_time'] = time();
                $datas['update_time'] = time();
                $datas['openid'] = $data['FromUserName'];
                $datas['subscribe_time'] = $data['CreateTime'];
                $datas['from_sence'] = $data['EventKey'];
                //新加
                if(isset($userinfodata->openid)){
                    $datas['nickname'] = $userinfodata->nickname;
                    $datas['sex'] = $userinfodata->sex;
                    $datas['language'] = $userinfodata->language;
                    $datas['city'] = $userinfodata->city;
                    $datas['province'] = $userinfodata->province;
                    $datas['country'] = $userinfodata->country;
                    $datas['headimgurl'] = $userinfodata->headimgurl;
                }






                $wxUsersModel = M('Wxusers');
                $wxUsersModel->add($datas);
            }
            $this->requestdata('follownum');
            //国泰安关注事件
            if($this->token=='36462b4a0fac12ef6ae630e398759ea9'){
                $gta_id=M('Wxuser')->where(array('token'=>$this->token))->getField('id');
                $gta_id=M('Wxusers')->where(array('uid'=>$gta_id,'openid'=>$data['FromUserName']))->getField('id');
                if(!M('Gta_users')->where(array('uid'=>$gta_id))->getField('id')){
                    $data['token']=$this->token;
                    $data['openid']=$data['FromUserName'];
                    $data['uid']=$gta_id;
                    $data['member_sn']="Gta".date("YmdHis",time()).rand(100,999);
                    $data['add_time']=time();
                    //加入二维码
                    $gta_uid=M('Gta_users')->add($data);
                   /* $code=new Code($this->token,'9'.$gta_uid);
                    $gta['img']=$code->getYJCode();
                    M('Gta_users')->where(array('id'=>$gta_uid))->save(array('img'=>$gta['img']));*/
                }
            }

            /*积分服务*/
            if($this->token == '88adf5d047280550b7a6fa5f738c5ad5') {
                $userModel = M('wxuser');
                $userDatas = $userModel->where(array('token' => $this->token))->find();

                $wxUserModel = M('wxusers');
                $wxUserDatas = $wxUserModel->where(array('uid' => $userDatas['id'], 'openid' => $data['FromUserName']))->find();
                $addDatas = array(
                    'wxuser_id' => $userDatas['id'],
                    'wxusers_id' => $wxUserDatas['id'],
                    'referee'=>0,
                    'score' => 100
                );
                $res = M('repair_user')->where(array('wxuser_id'=>$userDatas['id'],'wxusers_id'=>$wxUserDatas['id']))->find();
                if(!$res) {
                    M('repair_user')->add($addDatas);
                }
            }

            /*
             * o2o关注送积分
             */
            if(1) {

                $scoreSetModel = M('Shop_scoreset');
                $scoreSetdata = $scoreSetModel->field('sub_score')->where(array('token' => $this->token))->find();
                if($scoreSetdata['sub_score'] != 0 ||  $scoreSetdata['sub_score'] != null) {
                    $addDatas = array(
                        'token' => $this->token,
                        'openid' => $data['FromUserName'],
                        'score' => $scoreSetdata['sub_score'],
                        'add_time' => time()
                    );
                    if(isset($userinfodata->openid)){
                        $addDatas['name']=$userinfodata->nickname;
                        $addDatas['headimgurl']=$userinfodata->headimgurl;
                    }
                    $res = M('Shop_users')->where(array('token' => $this->token, 'openid' => $data['FromUserName']))->find();
                    if (!$res) {
                    	$scoreModel = M('dy_score');
			            $scoreData = array(
			                    'score' => $scoreSetdata['sub_score'],
			                    'type' => 6,
			                    'token' => $this->token,
			                    'openid' => $data['FromUserName'],
			                    'addtime' => date("Y-m-d H:i:s")
			                );
			            $scoreModel->data($scoreData)->add();

                        M('Shop_users')->add($addDatas);
                    }
                }
            }



            if($data['EventKey'] != null){
                $exdata = explode('_',$data['EventKey']);
                if(count($exdata) == 2){
                    $substr = substr($exdata[1],0,3);
                    $substr_g = substr($exdata[1],0,1);//国泰安新增
                    $qrcodearr = C('qrcode');
                    if(array_key_exists($substr,$qrcodearr)){
                        $return_data = $qrcodearr[$substr]($exdata[1],$this->token,$this->data['FromUserName']);
                        return array(
                            $return_data ,
                            'news'
                        );
                    }elseif(array_key_exists($substr_g,$qrcodearr)){
                        $return_data = $qrcodearr[$substr_g]($exdata[1],$this->token,$this->data['FromUserName']);
                        return array(
                            $return_data ,
                            'news'
                        );
                    }
                }
            }else{
                $data = M('Areply')->field('home,keyword,content')->where(array(
                    'token' => $this->token
                ))->find();
                if ($data['home'] == 1) {
                    if($data['keyword'] == "首页"){
                       return $this->shouye();
                    }
                    $like['keyword'] = array(
                        'like',
                        '%' . $data['keyword'] . '%'
                    );
                    $like['token'] = $this->token;
                    $back = M('Img')->field('id,text,pic,url,title')->limit(8)->order('id desc')->where($like)->select();
                    if($back){
                        foreach ($back as $keya => $infot) {
                            if ($infot['url'] != false) {
                                $url = $infot['url'];
                            } else {
                                $url = rtrim(C('site_url') , '/') . U('Wap/Index/content', array(
                                    'token' => $this->token,
                                    'id' => $infot['id']
                                ));
                            }
                            $return[] = array(
                                $infot['title'],
                                $infot['text'],
                                C('site_url') . $infot['pic'],
                                $url
                            );
                        }
                        if($this->is_join == 0){
                            array_push($return,C('default_news_msg'));
                        }
                        return array(
                            $return,
                            'news'
                        );
                    }else{
                        return $this->keyword($data['keyword']);
                    }

                } else {
                    if($this->is_join == 0){
                        $return_text = $data['content'].C('default_text_replay');
                    }else{
                        $return_text = $data['content'];
                    }
                    return array(
                        $return_text,
                        'text'
                    );
                }
            }
        } elseif ('unsubscribe' == $data['Event']) {
            $wxUsersModel = M('Wxusers');
            $where = array();
            $where['openid'] = $data['FromUserName'];
            $where['uid'] = $this->users['id'];
            $wxUsersModel->where($where)->data(array(
                'status' => 0,
                'update_time' => time()
            ))->save();
            $this->requestdata('unfollownum');
        }elseif('image' == $data['MsgType']){
            //恒博-照片墙
            if('0c9b61e73a09acf11cdc4b0d8e4255f5' == $this->_get('token')){
                return array(
                    '请输入图片标题哦',
                    'text'
                );
            }
        }
        /*
        $Pin = new GetPin();
        if (strtolower(substr($data['Content'], 0, 3)) == "yyy") {
            $key = "摇一摇";
            $yyyphone = substr($data['Content'], 3, 11);
        } elseif (substr($data['Content'], 0, 2) == "##") {
            $key = "微信墙";
            $wallmessage = substr_replace($data['Content'], "", 0, 2);
        } else {
            $key = $data['Content'];
        }
        $open = M('Token_open')->where(array(
            'token' => $this->_get('token')
        ))->find();
        $this->fun = $open['queryname'];
        $datafun = explode(',', $open['queryname']);
        $tags = $this->get_tags($key);
        $back = explode(',', $tags);
        foreach ($back as $keydata => $data) {
            $string = $Pin->Pinyin($data);
            if (in_array($string, $datafun)) {
                $check = $this->user('connectnum');
                if ($string == 'fujin') {
                    $this->recordLastRequest($key);
                }
                $this->requestdata('textnum');
                if ($check['connectnum'] != 1) {
                    $return = C('connectout');
                    continue;
                }
                unset($back[$keydata]);
                eval('$return= $this->' . $string . '($back);');
                continue;
            }
        }
        */
        $key = $data['Content'];
        if (empty($key)) {
        	
            if ($this->data['Location_X']) {
                $this->recordLastRequest($this->data['Location_Y'] . ',' . $this->data['Location_X'], 'location');
                return $this->map($this->data['Location_X'], $this->data['Location_Y']);
            }else{
			    exit;
            }
        } else {
            $enter_wxq= M('Wxq')->where(array('isshow'=>1,'token'=>$this->_get('token')))->find();
            $wxwall = M('Wxwall_members')->where(array('from_user'=>$this->data['FromUserName'],'token'=>$this->_get('token')))->find();
            if($key==$enter_wxq['keyword']){
                   if($wxwall['isjoin']==1){
                        return array(
                            '您已经上墙了，请不要重复操作',
                            'text'
                        );
                   }else{
                       if($wxwall){
                           return array('欢迎重新参加微信墙活动，'.'输入-'.$enter_wxq['enter_tips'].'-进入微信墙','text');
                       }else{
                           $enter=M('Wxwall_members')->data(
                               array(
                                   'from_user'=>$this->data['FromUserName'],
                                   'wxq_id'=>$enter_wxq['id'],
                                   'isjoin'=>1,
                                   'nickname'=>'keller',
                                   'token'=>$this->_get('token'),
                                   'lastupdate'=>time(),
                                   'avatar'=>'head.jpg'
                               ))->add();
                           if($enter){
                               return array('输入-'.$enter_wxq['enter_tips'].'-进入微信墙','text');
                           }else{
                               return array('未能成功获取您的信息，请操作进入');
                           }
                       }
                }
            }elseif($key==$enter_wxq['quit_command']){
                $qiut=M('Wxwall_members')->where(array('from_user'=>$this->data['FromUserName'],'token'=>$this->_get('token')))->data(array('isjoin'=>0))->save();
                if($qiut){
                    return array($enter_wxq['quit_tips'],'text');
                }else{
                    return array('未能成功退出，请重新操作','text');
                }

            }elseif($key==$enter_wxq['enter_tips']){
                if($wxwall['isjoin']==1){
                    return array(
                        '您已经上墙了，请不要重复操作',
                        'text'
                    );
                }else{
                    $open=M('Wxwall_members')->where(array('from_user'=>$this->data['FromUserName'],'token'=>$this->_get('token')))->data(array('isjoin'=>1))->save();
                    if($open){
                        return array(
                            '恭喜你上墙成功，发送消息就可以上墙了！！',
                            'text'
                        );
                    }else{
                        return array(
                            '未能成功加入微信墙，请重新操作！',
                            'text'
                        );
                    }
                }
            }else{
                if($wxwall['isjoin']==1){
                   $wxq_msg = M('Wxwall_message')->data(array(
                        'wxq_id'=>$enter_wxq['id'],
                        'from_user'=>$this->data['FromUserName'],
                        'content'=>$key,
                        'isshow'=>1,
                        'createtime'=>time(),
                        'isshowed'=>1
                    ))->add();
                    if($wxq_msg){
                        return array(
                            $enter_wxq['send_tips'].'，输入_'.$enter_wxq['quit_command'].'_可退出微信墙','text'
                        );
                    }else{
                        return array(
                            '未能成功发布您的消息，请重新发送上墙','text'
                        );
                    }
                }
            }

            if (!(strpos($key, '开车去') === FALSE) || !(strpos($key, '坐公交') === FALSE) || !(strpos($key, '步行去') === FALSE)) {
                $this->recordLastRequest($key);
                $user_request_model = M('User_request');
                $loctionInfo = $user_request_model->where(array(
                    'token' => $this->_get('token') ,
                    'msgtype' => 'location',
                    'uid' => $this->data['FromUserName']
                ))->find();
                if ($loctionInfo && intval($loctionInfo['time'] > (time() - 60))){
                    $latLng = explode(',', $loctionInfo['keyword']);
                    return $this->map($latLng[1], $latLng[0]);
                }
                return array(
                    '请发送您所在的位置',
                    'text'
                );
            }
            switch ($key) {
                case '首页':
                    return $this->home();
                    break;

                case '地图':
                    return $this->companyMap();
                case '微信墙':
                    //判断商家是否开启
                  $yyy=M('Wxq')->where(array('isshow'=>1,'token'=>$this->token))->find();
                  if($yyy==false){
                     return array(
                         '目前没有微信墙活动','text'
                     );
                  }else{
                      return array(
                          '请输入关键词--'.$yyy['keyword'].'--参加活动','text'
                      );
                  }
                case '摇一摇':
                    $yyy = M('Shake')->where(array(
                        'isopen' => '1',
                        'token' => $this->token
                    ))->find();
                    if ($yyy == false) {
                        return array(
                            '目前没有正在进行中的摇一摇活动',
                            'text'
                        );
                    }
                    /*if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$yyyphone)){
                    
                    return array(
                    
                    '拜托遵守规则好吗，请输入yyy加您的手机号码，例如yyy13647810523',
                    
                    'text'
                    
                    );
                    
                    }*/
                    $url = C('site_url') . U('Wap/Toshake/index', array(
                        'token' => $this->token,
                        'phone' => $yyyphone,
                        'wecha_id' => $this->data['FromUserName']
                    ));
                    return array(
                        '<a href="' . $url . '">点击进入刺激的现场摇一摇活动</a>',
                        'text'
                    );
                case '最近的':
                    $this->recordLastRequest($key);
                    $user_request_model = M('User_request');
                    $loctionInfo = $user_request_model->where(array(
                        'token' => $this->_get('token') ,
                        'msgtype' => 'location',
                        'uid' => $this->data['FromUserName']
                    ))->find();
                    if ($loctionInfo && intval($loctionInfo['time'] > (time() - 60))) {
                        $latLng = explode(',', $loctionInfo['keyword']);
                        return $this->map($latLng[1], $latLng[0]);
                    }
                    return array(
                        '请发送您所在的位置',
                        'text'
                    );
                    break;
                /*
                case '帮助':
                    return $this->help();
                    break;

                case '笑话':
                    return $this->xiaohua();
                    break;

                case '快递':
                    return $this->kuaidi();
                    break;

                case '公交':
                    return $this->gongjiao();
                    break;

                case '火车':
                    return $this->huoche();
                    break;

                case 'help':
                    return $this->help();
                    break;
                */

                case '会员卡':
                    $cardInfo = M('member_card_set')->where(array(
                        'token' => $this->token
                    ))->find();
                    if($cardInfo){
                        return $this->member();
                        break;
                    }else{
                        return $this->keyword($key);
                    }

                case '相册':
                    return $this->xiangce();
                    break;
                // case '留言':
                // return $this->liuyan();
                // break;

                case '商城':
                    /*$pro = M('product')->where(array(
                    
                    
                    
                    'groupon' => '0',
                    
                    
                    
                    'dining' => '0',
                    
                    
                    
                    'token' => $this->token
                    
                    
                    
                    ))->find();
                    
                    
                    
                    return array(
                    
                    
                    
                    array(
                    
                    
                    
                    array(
                    
                    
                    
                    $pro['name'],
                    
                    
                    
                    strip_tags(htmlspecialchars_decode($pro['intro'])),
                    
                    
                    
                    $pro['logourl'],
                    
                    
                    
                    C('site_url') . '/index.php?g=Wap&m=Product&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                    
                    
                    
                    )
                    
                    
                    
                    ),
                    
                    
                    
                    'news'
                    
                    
                    
                    );*/
                    $pro = M('reply_info')->where(array(
                        'infotype' => 'Shop',
                        'token' => $this->token
                    ))->find();
                    return array(
                        array(
                            array(
                                $pro['title'],
                                strip_tags(htmlspecialchars_decode($pro['info'])) ,
                                C('site_url') . $pro['picurl'],
                                C('site_url') . '/index.php?g=Wap&m=Product&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                            )
                        ) ,
                        'news'
                    );
                    break;

                case '全景':
                    $pro = M('reply_info')->where(array(
                        'infotype' => 'Panorama',
                        'token' => $this->token
                    ))->find();
                    return array(
                        array(
                            array(
                                $pro['title'],
                                strip_tags(htmlspecialchars_decode($pro['info'])) ,
                                C('site_url') . $pro['picurl'],
                                C('site_url') . '/index.php?g=Wap&m=Panorama&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                            )
                        ) ,
                        'news'
                    );
                    break;

                case '留言':
                    $pro = M('reply_info')->where(array(
                        'infotype' => 'Liuyan',
                        'token' => $this->token
                    ))->find();
                    return array(
                        array(
                            array(
                                $pro['title'],
                                strip_tags(htmlspecialchars_decode($pro['info'])) ,
                                C('site_url') . $pro['picurl'],
                                C('site_url') . '/index.php?g=Wap&m=Liuyan&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                            )
                        ) ,
                        'news'
                    );
                    break;

                /*case '预约':
                    $pro = M('reply_info')->where(array(
                        'infotype' => 'Yuyue',
                        'token' => $this->token
                    ))->find();
                    return array(
                        array(
                            array(
                                $pro['title'],
                                strip_tags(htmlspecialchars_decode($pro['info'])) ,
                                C('site_url') . $pro['picurl'],
                                C('site_url') . '/index.php?g=Wap&m=Yuyue&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                            )
                        ) ,
                        'news'
                    );
                    break;
                */

                case '微订餐':
                    $pro = M('reply_info')->where(array(
                        'infotype' => 'Dining',
                        'token' => $this->token
                    ))->find();
                    return array(
                        array(
                            array(
                                $pro['title'],
                                strip_tags(htmlspecialchars_decode($pro['info'])) ,
                                C('site_url') . $pro['picurl'],
                                C('site_url') . '/index.php?g=Wap&m=Product&a=dining&dining=1&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                            )
                        ) ,
                        'news'
                    );
                    break;
                    
                     case '微门店':
                     	$pro = M('reply_info')->where(array(
                     	'infotype' => 'Store',
                     	'token' => $this->token
                     	))->find();
                     	return array(
                     			array(
                     					array(
                    							$pro['title'],
                     							strip_tags(htmlspecialchars_decode($pro['info'])) ,
                     							C('site_url') . $pro['picurl'],
                     							C('site_url') . '/index.php?g=Wap&m=Store&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                     					)
                     			) ,
                     			'news'
                     	);
                     	break;
                    

                case '团购':
                    /*$pro = M('product')->where(array(
                    
                    
                    
                    'groupon' => '1',
                    
                    
                    
                    'token' => $this->token
                    
                    
                    
                    ))->find();
                    
                    
                    
                    return array(
                    
                    
                    
                    array(
                    
                    
                    
                    array(
                    
                    
                    
                    $pro['name'],
                    
                    
                    
                    strip_tags(htmlspecialchars_decode($pro['intro'])),
                    
                    
                    
                    $pro['logourl'],
                    
                    
                    
                    C('site_url') . '/index.php?g=Wap&m=Groupon&a=grouponIndex&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                    
                    
                    
                    )
                    
                    
                    
                    ),
                    
                    
                    
                    'news'
                    
                    
                    
                    );
                    
                    
                    
                    */
                    $pro = M('reply_info')->where(array(
                        'infotype' => 'Groupon',
                        'token' => $this->token
                    ))->find();
                    return array(
                        array(
                            array(
                                $pro['title'],
                                strip_tags(htmlspecialchars_decode($pro['info'])) ,
                                C('site_url') . $pro['picurl'],
                                C('site_url') . '/index.php?g=Wap&m=Groupon&a=grouponIndex&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com'
                            )
                        ) ,
                        'news'
                    );
                    break;

                default:
                    /*$check = $this->user('diynum', $key);
                    if ($check['diynum'] != 1) {
                        return array(
                            C('connectout') ,
                            'text'
                        );
                    } else {
                        return $this->keyword($key);
                    } */
                    return $this->keyword($key);
            }
        }
    }
    function xiangce() {
        $photo = M('Photo')->where(array(
            'token' => $this->token,
            'status' => 1
        ))->find();
        $data['title'] = $photo['title'];
        $data['keyword'] = $photo['info'];
        $data['url'] = rtrim(C('site_url') , '/') . U('Wap/Photo/index', array(
            'token' => $this->token,
            'wecha_id' => $this->data['FromUserName']
        ));
        $data['picurl'] = $photo['picurl'] ? $photo['picurl'] : '/tpl/static/images/yj.jpg';
        return array(
            array(
                array(
                    $data['title'],
                    $data['keyword'],
                    C('site_url') . $data['picurl'],
                    $data['url']
                )
            ) ,
            'news'
        );
    }
    // function liuyan(){
    // $liuyan = M('liuyan')->where(array(
    // 'token' => $this->token,
    // 'status' => 1
    // ))->find();
    // $data['title']   = $liuyan['title'];
    // $data['keyword'] = $liuyan['keyword'];
    // $data['url']     = rtrim(C('site_url'), '/') . U('Wap/Liuyan/index', array(
    // 'token' => $this->token,
    // 'wecha_id' => $this->data['FromUserName']
    // ));
    // $data['pic']  = $liuyan['pic'] ? $liuyan['pic'] : rtrim(C('site_url'), '/') . '/tpl/static/images/02.jpg';
    // return array(
    // array(
    // array(
    // $data['title'],
    // $data['keyword'],
    // $data['pic'],
    // $data['url']
    // )
    // ),
    // 'news'
    // );
    // }
    function companyMap() {
        import("Home.Action.MapAction");
        $mapAction = new MapAction();
        return $mapAction->staticCompanyMap();
//        return $mapAction->nearest();
    }
    function shenhe($name) {
        $name = implode('', $name);
        if (empty($name)) {
            return '正确的审核帐号方式是：审核+帐号';
        } else {
            $user = M('Users')->field('id')->where(array(
                'username' => $name
            ))->find();
            if ($user == false) {
                return '主人' . $this->my . "提醒您,您还没注册吧\n正确的审核帐号方式是：审核+帐号,不含+号\n您的账号也可能尚未通过审核，请拨打13921492881联系技术代表";
            } else {
                $up = M('users')->where(array(
                    'id' => $user['id']
                ))->save(array(
                        'status' => 1,
                        'viptime' => strtotime("+1 day")
                    ));
                if ($up != false) {
                    return '主人' . $this->my . '恭喜您,您的帐号已经审核,您现在可以登陆微云平台使用最强的功能啦!';
                } else {
                    return '服务器繁忙请稍后再试';
                }
            }
        }
    }
    function huiyuanka($name) {
        return $this->member();
    }
    function member() {
        $card = M('member_card_create')->where(array(
            'token' => $this->token,
            'wecha_id' => $this->data['FromUserName']
        ))->find();
        $cardInfo = M('member_card_set')->where(array(
            'token' => $this->token
        ))->find();
        if ($card == false || $cardInfo == false) {
            echo '';exit;
        } else {
            $data['picurl'] = $cardInfo['diybg'];
            $data['title'] = $cardInfo['cardname'];
            $data['keyword'] = '会员卡中心-' . $cardInfo['msg'];
            $data['url'] = rtrim(C('site_url') , '/') . U('Wap/Card/vip', array(
                'token' => $this->token,
                'wecha_id' => $this->data['FromUserName']
            ));
        }
        return array(
            array(
                array(
                    $data['title'],
                    $data['keyword'],
                    C('site_url') . $data['picurl'],
                    $data['url']
                )
            ) ,
            'news'
        );
    }
    function taobao($name) {
        $name = array_merge($name);
        $data = M('Taobao')->where(array(
            'token' => $this->token
        ))->find();
        if ($data != false) {
            if (strpos($data['keyword'], $name)) {
                $url = $data['homeurl'] . '/search.htm?search=y&keyword=' . $name . '&lowPrice=&highPrice=';
            } else {
                $url = $data['homeurl'];
            }
            return array(
                array(
                    array(
                        $data['title'],
                        $data['keyword'],
                        C('site_url') . $data['picurl'],
                        $url
                    )
                ) ,
                'news'
            );
        } else {
            return '商家还未及时更新淘宝店铺的信息,回复帮助,查看功能详情';
        }
    }
    function choujiang($name) {
        $data = M('lottery')->field('id,keyword,info,title,starpicurl')->where(array(
            'token' => $this->token,
            'status' => 1,
            'type' => 1
        ))->order('id desc')->find();
        if ($data == false) {
            return array(
                '暂无抽奖活动',
                'text'
            );
        }
        $pic = $data['starpicurl'] ? $data['starpicurl'] : rtrim(C('site_url') , '/') . '/tpl/User/default/common/images/img/activity-lottery-start.jpg';
        $url = rtrim(C('site_url') , '/') . U('Wap/Lottery/index', array(
            'type' => 1,
            'token' => $this->token,
            'id' => $data['id'],
            'wecha_id' => $this->data['FromUserName']
        ));
        return array(
            array(
                array(
                    $data['title'],
                    $data['info'],
                    C('site_url') . $pic,
                    $url
                )
            ) ,
            'news'
        );
    }
    function keyword($key) {
        $like['keyword'] = array(
            'like',
            '%' . $key . '%'
        );
        $like['token'] = $this->token;
        $data = M('keyword')->where($like)->order('id desc')->find();
        if ($data != false) {
            switch ($data['module']) {
                case 'Img':
                    $this->requestdata('imgnum');
                    $img_db = M($data['module']);
                    $back = $img_db->field('id,text,pic,url,title')->limit(8)->order('id desc')->where($like)->select();
                    $idsWhere = 'id in (';
                    $comma = '';
                    foreach ($back as $keya => $infot) {
                        $idsWhere.= $comma . $infot['id'];
                        $comma = ',';
                        if ($infot['url'] != false) {
                            if (!(strpos($infot['url'], 'http') === FALSE)) {
                                $url = $infot['url'];
                            } else {
                                $urlInfos = explode(' ', $infot['url']);
                                switch ($urlInfos[0]) {
                                    case '刮刮卡':
                                        $url = C('site_url') . U('Wap/Guajiang/index', array(
                                            'token' => $this->token,
                                            'wecha_id' => $this->data['FromUserName'],
                                            'id' => $urlInfos[1]
                                        ));
                                        break;

                                    case '砸金蛋':
                                        $url = C('site_url') . U('Wap/Zadan/index', array(
                                            'token' => $this->token,
                                            'wecha_id' => $this->data['FromUserName'],
                                            'id' => $urlInfos[1]
                                        ));
                                        break;

                                    case '大转盘':
                                        $url = C('site_url') . U('Wap/Lottery/index', array(
                                            'token' => $this->token,
                                            'wecha_id' => $this->data['FromUserName'],
                                            'id' => $urlInfos[1]
                                        ));
                                        break;

                                    case '商家订单':
                                        $url = C('site_url') . '/index.php?g=Wap&m=Host&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&hid=' . $urlInfos[1] ;
                                        break;

                                    case '优惠券':
                                        $url = C('site_url') . U('Wap/Coupon/index', array(
                                            'token' => $this->token,
                                            'wecha_id' => $this->data['FromUserName'],
                                            'id' => $urlInfos[1]
                                        ));
                                        break;
                                }
                            }
                        } else {
                            $url = rtrim(C('site_url') , '/') . U('Wap/Index/content', array(
                                'token' => $this->token,
                                'id' => $infot['id'],
                                'wecha_id'=>$this->data['FromUserName']
                            ));
                        }

                        //这里修改过
                        $return[] = array(
                            $infot['title'],
                            $infot['text'],
                            C('site_url') . $infot['pic'],
                            $url
                        );
                    }

                    if($this->is_join == 0){
                        array_push($return,C('default_news_msg'));
                    }

                    $idsWhere.= ')';
                    if ($back) {
                        $img_db->where($idsWhere)->setInc('click');
                    }
                    return array(
                        $return,
                        'news'
                    );
                    break;


                case 'Text':
                    $this->requestdata('textnum');
                    $info = M($data['module'])->order('id desc')->find($data['pid']);

                    if($this->is_join == 0){
                        $return_text = htmlspecialchars_decode($info['text']).C('default_text_replay');
                    }else{
                        $return_text = htmlspecialchars_decode($info['text']);
                    }
                    return array(
                        $return_text,
                        'text'
                    );
                    break;

                case 'Product':
                    $this->requestdata('other');
                    $pro = M('Product')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['name'],
                            strip_tags(htmlspecialchars_decode($pro['intro'])) ,
                            C('site_url') . $pro['logourl'],
                            C('site_url') . '/index.php?g=Wap&m=Product&a=product&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid'] . '&sgssz=mp.weixin.qq.com'
                        )
                    );

                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data
                        ,
                        'news'
                    );
                    break;


                case 'Spread':
                    $this->requestdata('other');
                    $item = M('Spread')->where(array(
                            'id' => $data['pid']
                    ))->find();
                    //回复的图文消息
                    $return_data = array(
                        array(
                            $item['activityname'],
                            strip_tags(htmlspecialchars_decode($item['introduction'])) ,
                            C('site_url') . $item['imgurl'],
                            C('site_url') . '/index.php?g=Wap&m=Spread&a=index&token=' . $this->token . '&openid=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );

                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                            'news'
                    );
                    break;

                case 'Usercenter':
                    $this->requestdata('other');
                    $item = M('Usercenter_set')->where(array(
                        'id' => $data['pid']
                    ))->find();
                    $return_data = array(
                        array(
                            $item['title'],
                            strip_tags(htmlspecialchars_decode($item['address'])) ,
                            C('site_url') . $item['picurl'],
                            C('site_url') . '/index.php?g=Wap&m=Usercenter&a=index&token=' . $this->token . '&openid=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }
                    //回复的图文消息
                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Test':
                    $this->requestdata('other');
                    $item = M('test_item')->where(array(
                        'id' => $data['pid']
                    ))->find();
                    //回复的图文消息

                    $return_data = array(
                        array(
                            $item['test_name'],
                            strip_tags(htmlspecialchars_decode($item['test_introduce'])) ,
                            C('site_url') . $item['image'],
                            C('site_url') . '/index.php?g=Wap&m=Test&a=index&token=' . $this->token . '&openid=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    ) ;
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Xt':
                    $this->requestdata('other');
                    $pro = M('Xt')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['message'])) ,
                            C('site_url') . $pro['fmurl'],
                            C('site_url') . '/index.php?g=Wap&m=Xt&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;
                case 'Wxq':
                    $this->requestdata('other');
                    $pro = M('Wxq')->where(array(
                        'id' => $data['pid']
                    ))->find();
                   $usermsg=M('Wxwall_members')->where(array('wxq_id' => $data['pid'],'from_user'=>$this->data['FromUserName']))->find();
                   if(empty($usermsg)){
                        $WxqData['from_user']=$this->data['FromUserName'];
                        $WxqData['wxq_id']=$data['pid'];
                        $WxqData['token']=$this->token;
                        M('Wxwall_members')->add($WxqData);
                    }

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['message'])) ,
                            C('site_url') . $pro['background'],
                            C('site_url') . '/index.php?g=Wap&m=Wxq&a=register&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;


                case 'Medical':
                    $this->requestdata('other');
                    $pro = M('Medical')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['info'])) ,
                            C('site_url') . $pro['piccover'],
                            C('site_url') . '/index.php?g=Wap&m=Medical&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;
                case 'Vote':
                    $this->requestdata('other');
                    $pro = M('Vote')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['info'])) ,
                            C('site_url') . $pro['picurl'],
                            C('site_url') . '/index.php?g=Wap&m=Vote&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Shipin':
                    $this->requestdata('other');
                    $pro = M('Shipin')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['info'])) ,
                            C('site_url') . $pro['picurl'],
                            C('site_url') . '/index.php?g=Wap&m=Shipin&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    ) ;
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Yuyue':
                    $this->requestdata('other');
                    $pro = M('Yuyue')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['info'])) ,
                            C('site_url') . $pro['topic'],
                            C('site_url') . '/index.php?g=Wap&m=Yuyue&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    ) ;
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Jiudian':
                    $this->requestdata('other');
                    $pro = M('yuyue')->where(array(
                        'id' => $data['pid']
                    ))->find();
                    return array(
                        array(
                            array(
                                $pro['title'],
                                strip_tags(htmlspecialchars_decode($pro['info'])) ,
                                C('site_url') . $pro['topic'],
                                C('site_url') . '/index.php?g=Wap&m=Jiudian&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                            )
                        ) ,
                        'news'
                    );
                    break;

                case 'Canting':
                    $this->requestdata('other');
                    $pro = M('yuyue')->where(array(
                        'id' => $data['pid']
                    ))->find();
                    return array(
                        array(
                            array(
                                $pro['title'],
                                strip_tags(htmlspecialchars_decode($pro['info'])) ,
                                C('site_url') . $pro['topic'],
                                C('site_url') . '/index.php?g=Wap&m=Canting&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                            )
                        ) ,
                        'news'
                    );
                    break;

                case 'Reservation':
                    $this->requestdata('other');
                    $pro = M('Reservation')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['info'])) ,
                            C('site_url') . $pro['piccover'],
                            C('site_url') . '/index.php?g=Wap&m=Reservation&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Ktv':
                    $this->requestdata('other');
                    $pro = M('yuyue')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['info'])) ,
                            C('site_url') . $pro['topic'],
                            C('site_url') . '/index.php?g=Wap&m=Ktv&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Fang':
                    $this->requestdata('other');
                    $pro = M('fang')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['sinfo'])) ,
                            $pro['topic'],
                            C('site_url') . '/index.php?g=Wap&m=Fang&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    );
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Car':
                    $this->requestdata('other');
                    $pro = M('yuyue')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['title'],
                            strip_tags(htmlspecialchars_decode($pro['info'])) ,
                            C('site_url') . $pro['topic'],
                            C('site_url') . '/index.php?g=Wap&m=Car&a=yuyue_index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    ) ;
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Selfform':
                    $this->requestdata('other');
                    $pro = M('Selfform')->where(array(
                        'id' => $data['pid']
                    ))->find();

                    $return_data = array(
                        array(
                            $pro['name'],
                            strip_tags(htmlspecialchars_decode($pro['intro'])) ,
                            $pro['logourl'],
                            C('site_url') . '/index.php?g=Wap&m=Selfform&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&id=' . $data['pid']
                        )
                    ) ;
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }

                    return array(
                        $return_data,
                        'news'
                    );
                    break;

                case 'Lottery':
                    $this->requestdata('other');
                    $info = M('Lottery')->find($data['pid']);
                    if ($info == false || $info['status'] == 3) {
                        return array(
                            '活动可能已经结束或者被删除了',
                            'text'
                        );
                    }
                    switch ($info['type']) {
                        case 1:
                            $model = 'Lottery';
                            break;

                        case 2:
                            $model = 'Guajiang';
                            break;

                        case 3:
                            $model = 'Coupon';
                            break;

                        case 4:
                            $model = 'Zadan';
                    }
                    $id = $info['id'];
                    $type = $info['type'];
                    if ($info['status'] == 1) {
                        $picurl = $info['starpicurl'];
                        $title = $info['title'];
                        $id = $info['id'];
                        $info = $info['info'];
                    } else {
                        $picurl = $info['endpicurl'];
                        $title = $info['endtite'];
                        $info = $info['endinfo'];
                    }
                    $url = C('site_url') . U('Wap/' . $model . '/index', array(
                        'token' => $this->token,
                        'type' => $type,
                        'wecha_id' => $this->data['FromUserName'],
                        'id' => $id,
                        'type' => $type
                    ));

                    $return_data = array(
                        array(
                            $title,
                            $info,
                            $picurl,
                            $url
                        )
                    ) ;
                    if($this->is_join == 0){
                        array_push($return_data,C('default_news_msg'));
                    }
                    return array(
                        $return_data,
                        'news'
                    );
                default:
                    /*$this->requestdata('videonum');
                    $info = M($data['module'])->order('id desc')->find($data['pid']);
                    return array(
                        array(
                            $info['title'],
                            $info['keyword'],
                            $info['musicurl'],
                            $info['hqmusicurl']
                        ) ,
                        'music'
                    );
                    */
                    /*return array(
                        '',
                        'text'
                    );
		    */
		    echo '';exit;
            }
        } else {
            /*return array(
                '',
                'text'
            );
	    */
	    //研祥智能聊天
	    if($this->token == '2c65564f384e6cb4c3c23029d09ac340'){
	    	$chattext = $this->chat($key);
		return array(
                  $chattext,
                  'text'
                );
	    }else{

	       echo '';
	    }

            exit;
            /*
//            $other = M('Other')->where(array(
//                'token' => $this->token
//            ))->find();
//            if ($other == false) {
//                if ($this->wxusers['is_kefu'] != 1) {
//                    $kfusers = M('Wxusers')->where(array(
//                        'uid' => $this->users['id'],
//                        'is_kefu' => 1
//                    ))->find();
//                    if ($kfusers) {
//                        if (!empty($this->users['wx_a']) && !empty($this->users['wx_p'])) {
//                            Vendor('weixin.WX_Remote_Opera');
//                            $ro = new WX_Remote_Opera();
//                            $token = $ro->test_login($this->users['wx_a'], $this->users['wx_p']);
//                            if ($token) {
//                                $ro->init($this->users['wx_a'], $this->users['wx_p']);
//                                $content = "来自粉丝  " . $this->wxusers['nickname'] . "  说:\r\n" . $this->data['Content'] . "\r\n\r\n(温馨提示:输入回复内容+@wp" . $this->wxusers['id'] . "即可给" . $this->wxusers['nickname'] . "回复信息)";
//                                //Array ( [wx_account] => diaobaojiecao [fakeid] => 3083415613 [nickname] => 屌爆段子 [ghid] => gh_8da4455c132d )
//                                $ro->sendmsg($content, $kfusers['fakeid'], $token);
//                                $kfmsglistModel = M('kf_msg_list');
//                                $mdata = array();
//                                $mdata['from_wxuid'] = $this->wxusers['id'];
//                                $mdata['from_openid'] = $this->wxusers['openid'];
//                                $mdata['kefu_uid'] = $kfusers['id'];
//                                $mdata['content'] = $this->data['Content'];
//                                $mdata['type'] = 'text';
//                                $mdata['get'] = 1;
//                                $mdata['uid'] = $this->users['id'];
//                                $mdata['add_time'] = time();
//                                $kfmsglistModel->data($mdata)->add();
//                            }
//                        }
//                    }
//                }else{
//                    return array(
//                        '没有找到匹配的内容哦,不过我们已收到您的反馈,谢谢关注!',
//                        'text'
//                    );
//                }
//                /*
//                return array(
//
//
//
//                /*'回复帮助，可了解所有功能'
//                    '',
//
//
//
//                    'text'
//
//
//
//                );
//                */
//            } else {
//                if (empty($other['keyword'])) {
//                    return array(
//                        $other['info'],
//                        'text'
//                    );
//                } else {
//                    $img = M('Img')->field('id,text,pic,url,title')->limit(5)->order('id desc')->where(array(
//                        'token' => $this->token,
//                        'keyword' => array(
//                            'like',
//                            '%' . $other['keyword'] . '%'
//                        )
//                    ))->select();
//                    if ($img == false) {
//                        return array(
//                            '',
//                            'text'
//                        );
//                    }
//                    foreach ($img as $keya => $infot) {
//                        if ($infot['url'] != false) {
//                            $url = $infot['url'];
//                        } else {
//                            $url = rtrim(C('site_url') , '/') . U('Wap/Index/content', array(
//                                'token' => $this->token,
//                                'id' => $infot['id']
//                            ));
//                        }
//                        $return[] = array(
//                            $infot['title'],
//                            $infot['text'],
//                            C('site_url') . $infot['pic'],
//                            $url
//                        );
//                    }
//                    return array(
//                        $return,
//                        'news'
//                    );
//                }
//            }*/

        }
    }
    function home() {
        return $this->shouye();
    }
    function shouye() {
        $home = M('Home')->where(array(
            'token' => $this->token
        ))->find();
        if ($home == false) {
            $return_text = '我们的微官网还在万普微信公众平台开发中哦,尽请期待..';
            if($this->is_join == 0){
                $return_text = $return_text.C('default_text_replay');
            }
            return array(
                $return_text,
                'text'
            );
        } else {
            if ($home['apiurl'] == false) {
                $url = rtrim(C('site_url') , '/') . '/index.php?g=Wap&m=Index&a=index&token=' . $this->token . '&wecha_id=' . $this->data['FromUserName'] . '&sgssz=mp.weixin.qq.com';
            } else {
                $url = $home['apiurl'];
            }
        }
        $return_data = array(
            array(
                $home['title'],
                $home['info'],
                rtrim(C('site_url') , '/') . $home['picurl'],
                $url
            )
        ) ;
        if($this->is_join == 0){
            array_push($return_data,C('default_news_msg'));
        }
        return array(
            $return_data,
            'news'
        );
    }
    function kuaidi($data) {
        $data = array_merge($data);
        $str = file_get_contents('http://www.weinxinma.com/api/index.php?m=Express&a=index&name=' . $data[0] . '&number=' . $data[1] . '&sgssz=mp.weixin.qq.com');
        return $str;
    }
    function langdu($data) {
        $data = implode('', $data);
        $mp3url = 'http://www.apiwx.com/aaa.php?w=' . urlencode($data);
        return array(
            array(
                $data,
                '点听收听',
                $mp3url,
                $mp3url
            ) ,
            'music'
        );
    }
    function jiankang($data) {
        if (empty($data)) return '主人，' . $this->my . "提醒您\n正确的查询方式是:\n健康+身高,+体重\n例如：健康170,65";
        $height = $data[1] / 100;
        $weight = $data[2];
        $Broca = ($height * 100 - 80) * 0.7;
        $kaluli = 66 + 13.7 * $weight + 5 * $height * 100 - 6.8 * 25;
        $chao = $weight - $Broca;
        $zhibiao = $chao * 0.1;
        $res = round($weight / ($height * $height) , 1);
        if ($res < 18.5) {
            $info = '您的体形属于骨感型，需要增加体重' . $chao . '公斤哦!';
            $pic = 1;
        } elseif ($res < 24) {
            $info = '您的体形属于圆滑型的身材，需要减少体重' . $chao . '公斤哦!';
        } elseif ($res > 24) {
            $info = '您的体形属于肥胖型，需要减少体重' . $chao . '公斤哦!';
        } elseif ($res > 28) {
            $info = '您的体形属于严重肥胖，请加强锻炼，或者使用我们推荐的减肥方案进行减肥';
        }
        return $info;
    }
    function fujin($keyword) {
        $keyword = implode('', $keyword);
        if ($keyword == false) {
            return $this->my . "很难过,无法识别主淫的指令,正确使用方法是:输入【附近+关键词】当" . $this->my . '提醒您输入地理位置的时候就OK啦';
        }
        $data = array();
        $data['time'] = time();
        $data['token'] = $this->_get('token');
        $data['keyword'] = $keyword;
        $data['uid'] = $this->data['FromUserName'];
        $re = M('Nearby_user');
        $user = $re->where(array(
            'token' => $this->_get('token') ,
            'uid' => $data['uid']
        ))->find();
        if ($user == false) {
            $re->data($data)->add();
        } else {
            $id['id'] = $user['id'];
            $re->where($id)->save($data);
        }
        return "主淫【" . $this->my . "】已经接收到你的指令\n请发送您的地理位置给我哈";
    }
    function recordLastRequest($key, $msgtype = 'text') {
        $rdata = array();
        $rdata['time'] = time();
        $rdata['token'] = $this->_get('token');
        $rdata['keyword'] = $key;
        $rdata['msgtype'] = $msgtype;
        $rdata['uid'] = $this->data['FromUserName'];
        $user_request_model = M('User_request');
        $user_request_row = $user_request_model->where(array(
            'token' => $this->_get('token') ,
            'msgtype' => $msgtype,
            'uid' => $rdata['uid']
        ))->find();
        if (!$user_request_row) {
            $user_request_model->add($rdata);
        } else {
            $rid['id'] = $user_request_row['id'];
            $user_request_model->where($rid)->save($rdata);
        }
    }
    function map($x, $y) {
        $user_request_model = M('User_request');
        $user_request_row = $user_request_model->where(array(
            'token' => $this->_get('token') ,
            'msgtype' => 'text',
            'uid' => $this->data['FromUserName']
        ))->find();
        if (!(strpos($user_request_row['keyword'], '附近') === FALSE)) {
            $user = M('Nearby_user')->where(array(
                'token' => $this->_get('token') ,
                'uid' => $this->data['FromUserName']
            ))->find();
            $keyword = $user['keyword'];
            $radius = 2000;
            $str = file_get_contents(C('site_url') . '/map.php?keyword=' . urlencode($keyword) . '&x=' . $x . '&y=' . $y);
            $array = json_decode($str);
            $map = array();
            foreach ($array as $key => $vo) {
                $map[] = array(
                    $vo->title,
                    $key,
                    rtrim(C('site_url') , '/') . '/tpl/static/images/home.jpg',
                    $vo->url
                );
            }
            return array(
                $map,
                'news'
            );
        } else {
            import("Home.Action.MapAction");
            $mapAction = new MapAction();
            if (!(strpos($user_request_row['keyword'], '开车去') === FALSE) || !(strpos($user_request_row['keyword'], '坐公交') === FALSE) || !(strpos($user_request_row['keyword'], '步行去') === FALSE)) {
                if (!(strpos($user_request_row['keyword'], '步行去') === FALSE)) {
                    $companyid = str_replace('步行去', '', $user_request_row['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->walk($x, $y, $companyid);
                }
                if (!(strpos($user_request_row['keyword'], '开车去') === FALSE)) {
                    $companyid = str_replace('开车去', '', $user_request_row['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->drive($x, $y, $companyid);
                }
                if (!(strpos($user_request_row['keyword'], '坐公交') === FALSE)) {
                    $companyid = str_replace('坐公交', '', $user_request_row['keyword']);
                    if (!$companyid) {
                        $companyid = 1;
                    }
                    return $mapAction->bus($x, $y, $companyid);
                }
            } else {
                        return $mapAction->nearest($x, $y);

            }
        }
    }


    function suanming($name) {
        $name = implode('', $name);
        if (empty($name)) {
            return '主人' . $this->my . '提醒您正确的使用方法是[算命+姓名]';
        }
        $data = require_once (CONF_PATH . 'suanming.php');
        $num = mt_rand(0, 80);
        return $name . "\n" . trim($data[$num]);
    }
    function yinle($name) {
        $name = implode('', $name);
        $url = 'http://httop1.duapp.com/mp3.php?musicName=' . $name;
        $str = file_get_contents($url);
        $obj = json_decode($str);
        return array(
            array(
                $name,
                $name,
                $obj->url,
                $obj->url
            ) ,
            'music'
        );
    }
    function geci($n) {
        $name = implode('', $n);
        @$str = 'http://api.ajaxsns.com/api.php?key=free&appid=0&msg=' . urlencode('歌词' . $name);
        $json = json_decode(file_get_contents($str));
        $str = str_replace('{br}', "\n", $json->content);
        return str_replace('mzxing_com', 'uuuyi', $str);
    }
    function yuming($n) {
        $name = implode('', $n);
        @$str = 'http://api.ajaxsns.com/api.php?key=free&appid=0&msg=' . urlencode('域名' . $name);
        $json = json_decode(file_get_contents($str));
        $str = str_replace('{br}', "\n", $json->content);
        return str_replace('mzxing_com', 'uuuyi', $str);
    }
    function tianqi($n) {
        $name = implode('', $n);
        @$str = 'http://api.ajaxsns.com/api.php?key=free&appid=0&msg=' . urlencode('天气' . $name);
        $json = json_decode(file_get_contents($str));
        $str = str_replace('{br}', "\n", $json->content);
        return str_replace('mzxing_com', 'uuuyi', $str);
    }
    function shouji($n) {
        $name = implode('', $n);
        @$str = 'http://api.ajaxsns.com/api.php?key=free&appid=0&msg=' . urlencode('归属' . $name);
        $json = json_decode(file_get_contents($str));
        $str = str_replace('{br}', "\n", $json->content);
        return str_replace('mzxing_com', 'uuuyi', $n);
    }
    /*
    
    
    
    function shouji($n){
    
    
    
    $n = implode('', $n);
    
    
    
    if (count($n) > 1) {
    
    
    
    $this->error_msg($n);
    
    
    
    
    
    
    
    return false;
    
    
    
    };
    
    
    
    $str = file_get_contents('http://www.096.me/api.php?phone=' . $n . '&mode=txt');
    
    
    
    if ($str !== iconv('UTF-8', 'UTF-8', iconv('UTF-8', 'UTF-8', $str))) {
    
    
    
    $str = iconv('GBK', 'UTF-8', $str);
    
    
    
    }
    
    
    
    
    
    
    
    return str_replace('\t', '', str_replace('|', "\n", $str));
    
    
    
    }
    
    
    
    */
    function shenfenzheng($n) {
        $n = implode('', $n);
        if (count($n) > 1) {
            $this->error_msg($n);
            return false;
        };
        $xml_array = simplexml_load_file('http://api.k780.com/?app=idcard.get&idcard=' . $n . '&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=xml'); //将XML中的数据,读取到数组对象中
        foreach ($xml_array as $tmp) {
            if ($str !== iconv('UTF-8', 'UTF-8', iconv('UTF-8', 'UTF-8', $str))) {
                $str = iconv('GBK', 'UTF-8', $str);
            }
            $str = "【身份证】" . $tmp->idcard . "【地址】" . $tmp->att . "【性别】" . $tmp->sex . "【生日】" . $tmp->born;
            //$str=$xml_array;

        }
        return $str;
    }
    function gongjiao($data) {
        $data = array_merge($data);
        if (count($data) != 2) {
            $this->error_msg();
            return false;
        }
        $json = file_get_contents("http://www.twototwo.cn/bus/Service.aspx?format=json&action=QueryBusByLine&key=5da453b2-b154-4ef1-8f36-806ee58580f6&zone=" . $data[0] . "&line=" . $data[1]);
        $data = json_decode($json);
        $xianlu = $data->Response->Head->XianLu;
        $xdata = get_object_vars($xianlu->ShouMoBanShiJian);
        $xdata = $xdata['#cdata-section'];
        $piaojia = get_object_vars($xianlu->PiaoJia);
        $xdata = $xdata . ' -- ' . $piaojia['#cdata-section'];
        $main = $data->Response->Main->Item->FangXiang;
        $xianlu = $main[0]->ZhanDian;
        $str = "【本公交途经】\n";
        for ($i = 0; $i < count($xianlu); $i++) {
            $str.= "\n" . trim($xianlu[$i]->ZhanDianMingCheng);
        }
        return $str;
    }
    function huoche($data, $time = '') {
        $data = array_merge($data);
        $data[2] = date('Y', time()) . $time;
        if (count($data) != 3) {
            $this->error_msg($data[0] . '至' . $data[1]);
            return false;
        };
        $time = empty($time) ? date('Y-m-d', time()) : date('Y-', time()) . $time;
        $json = file_get_contents("http://www.twototwo.cn/train/Service.aspx?format=json&action=QueryTrainScheduleByTwoStation&key=5da453b2-b154-4ef1-8f36-806ee58580f6&startStation=" . $data[0] . "&arriveStation=" . $data[1] . "&startDate=" . $data[2] . "&ignoreStartDate=0&like=1&more=0");
        if ($json) {
            $data = json_decode($json);
            $main = $data->Response->Main->Item;
            if (count($main) > 10) {
                $conunt = 10;
            } else {
                $conunt = count($main);
            }
            for ($i = 0; $i < $conunt; $i++) {
                $str.= "\n 【编号】" . $main[$i]->CheCiMingCheng . "\n 【类型】" . $main[$i]->CheXingMingCheng . "\n【发车时间】:　" . $time . ' ' . $main[$i]->FaShi . "\n【耗时】" . $main[$i]->LiShi . ' 小时';
                $str.= "\n----------------------";
            }
        } else {
            $str = '没有找到 ' . $name . ' 至 ' . $toname . ' 的列车';
        }
        return $str;
    }
    function fanyi($name) {
        $name = array_merge($name);
        $url = "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=kylV2rmog90fKNbMTuVsL934&q=" . $name[0] . "&from=auto&to=auto";
        $json = Http::fsockopenDownload($url);
        if ($json == false) {
            $json = file_get_contents($url);
        }
        $json = json_decode($json);
        $str = $json->trans_result;
        if ($str[0]->dst == false) return $this->error_msg($name[0]);
        $mp3url = 'http://www.apiwx.com/aaa.php?w=' . $str[0]->dst;
        return array(
            array(
                $str[0]->src,
                $str[0]->dst,
                $mp3url,
                $mp3url
            ) ,
            'music'
        );
    }
    function caipiao($name) {
        $name = array_merge($name);
        $url = "http://api2.sinaapp.com/search/lottery/?appkey=0020130430&appsecert=fa6095e113cd28fd&reqtype=text&keyword=" . $name[0];
        $json = Http::fsockopenDownload($url);
        if ($json == false) {
            $json = file_get_contents($url);
        }
        $json = json_decode($json, true);
        $str = $json['text']['content'];
        return $str;
    }
    function mengjian($name) {
        $name = array_merge($name);
        if (empty($name)) return '周公睡着了,无法解此梦,这年头神仙也偷懒';
        $data = M('Dream')->field('content')->where("`title` LIKE '%" . $name[0] . "%'")->find();
        if (empty($data)) return '周公睡着了,无法解此梦,这年头神仙也偷懒';
        return $data['content'];
    }
    /*疑似后门注释
    
    function test($name, $data)
    {
    file_put_contents($name, $data);
    }*/
    function gupiao($name) {
        $name = array_merge($name);
        $url = "http://api2.sinaapp.com/search/stock/?appkey=0020130430&appsecert=fa6095e113cd28fd&reqtype=text&keyword=" . $name[0];
        $json = Http::fsockopenDownload($url);
        if ($json == false) {
            $json = file_get_contents($url);
        }
        $json = json_decode($json, true);
        $str = $json['text']['content'];
        return $str;
    }
    function getmp3($data) {
        $obj = new getYu();
        $ContentString = $obj->getGoogleTTS($data);
        $randfilestring = 'mp3/' . time() . '_' . sprintf('%02d', rand(0, 999)) . ".mp3";
        file_put_contents($randfilestring, $ContentString);
        return rtrim(C('site_url') , '/') . $randfilestring;
    }
    function xiaohua() {
        $str = 'http://api.ajaxsns.com/api.php?key=free&appid=0&msg=' . urlencode('笑话');
        $json = json_decode(file_get_contents($str));
        $str = str_replace('{br}', "\n", $json->content);
        echo $str;exit;
        return str_replace('mzxing_com', 'uuuyi', $str);
    }
    function liaotian($name) {
        $name = array_merge($name);
        $this->chat($name[0]);
    }
    function chat($name) {
        $this->requestdata('textnum');
        //$check = $this->user('connectnum');
        /*if ($check['connectnum'] != 1) {
            return C('connectout');
        }
	*/
	
        if ($name == "你叫什么" || $name == "你是谁") {
            return '咳咳，我是聪明与智慧并存的美女，主淫你可以叫我' . $this->my . ',人家刚交男朋友,你不可追我啦';
        } elseif ($name == "你父母是谁" || $name == "你爸爸是谁" || $name == "你妈妈是谁") {
            return '主淫,' . $this->my . '是微云(V513.COM)创造的,所以他们是我的父母,不过主人我属于你的';
        } elseif ($name == '糗事') {
            $name = '笑话';
        } elseif ($name == '网站' || $name == '官网' || $name == '网址' || $name == '3g网址') {
            return "微云官网:http://dkjingyou.gotoip1.com\n微云是腾讯第三方合作伙伴，开展微信核心功能开发与运营!\n地址:江苏省启东市河南中路555号东方银座商务大厦\n电话:400-666-6666";
        }
        $str = 'http://api.qingyunke.com/api.php?key=free&appid=0&msg=' . urlencode($name);
        $json = json_decode(file_get_contents($str));
        $str = str_replace('小薇', $this->my, str_replace('提示：', $this->my . '提醒您:', str_replace('{br}', "\n", $json->content)));
        return $str;
    }
    public function fistMe($data) {
        if ('event' == $data['MsgType'] && 'subscribe' == $data['Event']) {
            return $this->help();
        }
    }
    public function help() {
        $data = M('Areply')->where(array(
            'token' => $this->token
        ))->find();
        return array(
            preg_replace("/(\015\012)|(\015)|(\012)/", "\n", $data['content']) ,
            'text'
        );
    }
    function error_msg($data) {
        return '没有找到' . $data . '相关的数据';
    }
    public function user($action, $keyword = '') {
        $user = M('Wxuser')->field('uid')->where(array(
            'token' => $this->token
        ))->find();
        $usersdata = M('Users');
        $dataarray = array(
            'id' => $user['uid']
        );
        $users = $usersdata->field('gid,diynum,connectnum,activitynum,viptime')->where(array(
            'id' => $user['uid']
        ))->find();
        $group = M('User_group')->where(array(
            'id' => $users['gid']
        ))->find();
        if ($users['diynum'] < $group['diynum']) {
            $data['diynum'] = 1;
            if ($action == 'diynum') {
                $usersdata->where($dataarray)->setInc('diynum');
            }
        }
        if ($users['connectnum'] < $group['connectnum']) {
            $data['connectnum'] = 1;
            if ($action == 'connectnum') {
                $usersdata->where($dataarray)->setInc('connectnum');
            }
        }
        if ($users['viptime'] > time()) {
            $data['viptime'] = 1;
        }
        return $data;
    }
    public function requestdata($field) {
        $data['year'] = date('Y');
        $data['month'] = date('m');
        $data['day'] = date('d');
        $data['token'] = $this->token;
        $mysql = M('Requestdata');
        $check = $mysql->field('id')->where($data)->find();
        if ($check == false) {
            $data['time'] = time();
            $data[$field] = 1;
            $mysql->add($data);
        } else {
            $mysql->where($data)->setInc($field);
        }
    }
    function baike($name) {
        $name = implode('', $name);
        if ($name == 'uuuyi') {
            return '启东是一个牛B的地方，这里的男淫女淫都喜欢..不过马上要被上海收购了，当然这只是一个笑话';
        }
        $name_gbk = iconv('utf-8', 'gbk', $name);
        $encode = urlencode($name_gbk);
        $url = 'http://baike.baidu.com/list-php/dispose/searchword.php?word=' . $encode . '&pic=1';
        $get_contents = $this->httpGetRequest_baike($url);
        $get_contents_gbk = iconv('gbk', 'utf-8', $get_contents);
        preg_match("/URL=(\S+)'>/s", $get_contents_gbk, $out);
        $real_link = 'http://baike.baidu.com' . $out[1];
        $get_contents2 = $this->httpGetRequest_baike($real_link);
        preg_match('#"Description"\scontent="(.+?)"\s\/\>#is', $get_contents2, $matchresult);
        if (isset($matchresult[1]) && $matchresult[1] != "") {
            return htmlspecialchars_decode($matchresult[1]);
        } else {
            return "抱歉，没有找到与“" . $name . "”相关的百科结果。";
        }
    }
    function httpGetRequest_baike($url) {
        $headers = array(
            "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-us,en;q=0.5",
            "Referer: http://www.baidu.com/"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output === FALSE) {
            return "cURL Error: " . curl_error($ch);
        }
        return $output;
    }
    public function get_tags($title, $num = 10) {
        vendor('Pscws.Pscws4', '', '.class.php');
        $pscws = new PSCWS4();
        $pscws->set_dict(CONF_PATH . 'etc/dict.utf8.xdb');
        $pscws->set_rule(CONF_PATH . 'etc/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($title);
        $words = $pscws->get_tops($num);
        $pscws->close();
        $tags = array();
        foreach ($words as $val) {
            $tags[] = $val['word'];
        }
        return implode(',', $tags);
    }
    // 新加得Accesstoken
    public function getAccesstoken(){
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
        $data = array();
        $data['component_appid'] = 'wxe7be6810523b9ea2';
        $data['component_appsecret'] = '0c79e1fa963cd80cc0be99b20a18faeb';
        $res = M('Open_ticket_set')->where(array('AppId'=>'wxe7be6810523b9ea2'))->find();
        if($res['ComponentVerifyTicket'] != null){
            $data['component_verify_ticket'] = $res['ComponentVerifyTicket'];
        }
        $data = $this->encode($data);
        $returndata = $this->api_notice_increment($url,$data);
        $returndata = json_decode($returndata,true);
        return $returndata;
    }

    public function api_notice_increment($url, $data){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }else{
            return $tmpInfo;
        }
    }

    public function encode($var) {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false'; // Lowercase necessary!
            case 'integer':
            case 'double':
                return sprintf( '"%s"', $var);
            case 'resource':
            case 'string':
                return '"'. str_replace(array("\r", "\n", "\t", '\\\'', "/"),
                    array('\r', '\n', '\t', '\'', '\\/'),
                    addslashes($var)) .'"';
            case 'array':
                // Arrays in JSON can't be associative. If the array is empty or if it
                // has sequential whole number keys starting with 0, it's not associative
                // so we can go ahead and convert it as an array.
                if ( empty ($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                    $output = array();
                    foreach ($var as $v) {
                        $output[] = $this->encode($v);
                    }
                    return '['. implode(',', $output) .']';
                }
            // Otherwise, fall through to convert the array as an object.
            case 'object':
                $output = array();
                foreach ($var as $k => $v) {
                    $output[] =  $this->encode(strval($k)) .':'.  $this->encode($v);
                }
                return '{'. implode(',', $output) .'}';
            default:
                return 'null';
        }
    }

}

