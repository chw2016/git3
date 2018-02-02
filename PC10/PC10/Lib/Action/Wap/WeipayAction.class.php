<?php
class WeipayAction extends BaseAction{
    public $token;
    public $wecha_id;
    public $tpl = null;

    protected function _initialize(){
        Vendor('weipay.WxPayPubHelper.WxPayPubHelper');
        Vendor('weipay.WxPayPubHelper.WxPay.pub.config');
        parent::_initialize();


        $appdata = M('Weipay_config')->where(array('token'=>$this->token))->find();
        $this->appdata = $appdata;
        $JS_API_CALL_URL = C('JS_API_CALL_URL').$this->token;
        $NOTIFY_URL = C('NOTIFY_URL').$this->token;
        $this->NOTIFY_URL = $NOTIFY_URL;
        $SSLCERT_PATH = './upload/'.$this->token.'/apiclient_cert_'.$this->token.'.pem';
        $SSLKEY_PATH = './upload/'.$this->token.'/apiclient_key_'.$this->token.'.pem';
        WxPayConf_pub::set_config($appdata['appid'],$appdata['partnerkey'],$appdata['appkey'],$appdata['appsecret'],$JS_API_CALL_URL,
            $SSLCERT_PATH,$SSLKEY_PATH,$NOTIFY_URL,60);
        $this->token = $_REQUEST['token'];
        $this->assign('token',$this->token);
        $this->openid	= $_REQUEST['openid'];
        $this->assign('openid',$this->openid);
    }


    //支付
    public function index(){

        $weipayOrder = M('Weipay_order_list');
        $type = $_POST['type'];
        $orderid = $_POST['orderid'];
		$returnurl = $_POST['returnurl'];
        $order_money = $_POST['order_money'];
		//$order_money = 0.01;
        if($this->token == '31aa4be9214503027cec352f37c3051a'){
	  $order_text = $_POST['order_text'];
	}else{
           $order_text = '万普微信支付';
	}

        if(array_key_exists($type,C('weipay_type')) && $orderid){
            if($this->appdata && $this->openid){
                $timeStamp = time();
                $out_trade_no = WxPayConf_pub::$APPID."$timeStamp";
                $typearr = C('weipay_type');
                /*
                 * 生成支付订单
                 */
                $paydata['token'] = $this->token;
                $paydata['orderid'] = $out_trade_no;
                $paydata['status'] = 0;
                $paydata['add_time'] = time();
                $paydata['openid'] = $this->openid;
                $paydata['order_type'] = $typearr[$type];
                $paydata['order_money'] = $order_money;
                $paydata['from_orderid'] = $orderid;
                $paydata['order_table'] = $type;
                $weipayOrder->add($paydata);

                /*
                 * 启动支付
                 */
                $jsApi = new JsApi_pub();
                //=========步骤1：网页授权获取用户openid============
                //通过code获得openid
                /* if (!isset($_GET['code'])){
                     //触发微信返回code码
                     $url = $jsApi->createOauthUrlForCode($this->$JS_API_CALL_URL);
                     Header("Location: $url");
                 }else{
                     //获取code码，以获取openid
                     $code = $_GET['code'];
                     $jsApi->setCode($code);
                     $openid = $jsApi->getOpenId();
                 }

                */


                //=========步骤2：使用统一支付接口，获取prepay_id============
                //使用统一支付接口
                $unifiedOrder = new UnifiedOrder_pub();

                //设置统一支付接口参数
                //设置必填参数
                //appid已填,商户无需重复填写
                //mch_id已填,商户无需重复填写
                //noncestr已填,商户无需重复填写
                //spbill_create_ip已填,商户无需重复填写
                //sign已填,商户无需重复填写
                $unifiedOrder->setParameter("openid",$this->openid);//商品描述
                $unifiedOrder->setParameter("body",$order_text);//商品描述
                //自定义订单号，此处仅作举例

                $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
                $unifiedOrder->setParameter("total_fee",$order_money*100);//总金额
                $unifiedOrder->setParameter("notify_url",$this->NOTIFY_URL);//通知地址
                $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
                $attach = '';
                //非必填参数，商户可根据实际情况选填
                //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
                //$unifiedOrder->setParameter("device_info","XXXX");//设备号
                //$unifiedOrder->setParameter("attach",$attach);//附加数据
                //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
                //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
                //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
                //$unifiedOrder->setParameter("openid","XXXX");//用户标识
                //$unifiedOrder->setParameter("product_id","XXXX");//商品ID

                $prepay_id = $unifiedOrder->getPrepayId();
                //=========步骤3：使用jsapi调起支付============
                $jsApi->setPrepayId($prepay_id);

                $jsApiParameters = $jsApi->getParameters();
                $this->assign('jsApiParameters',$jsApiParameters);
                $this->assign('order_money',$order_money);
                $this->assign('order_text',$order_text);
                $this->assign('orderid',$orderid);
				$this->assign('returnurl',htmlspecialchars_decode($returnurl));
                $this->assign('order_type',$typearr[$type]);
                $this->display();
            }else{
                echo 'error';exit;
            }
        }else{
            echo '付款类型不存在';
        }



    }

    /*
     * 原生jsp
     */

    public function getpackage(){
        echo 'getpackage';

    }

    /*
     * notify
     */
    public function notify(){
        $logpath = LOG_PATH.date('y_m_d').'.weipay_log';
        //使用通用通知接口
        $notify = new Notify_pub();

	Log::write(print_r($_SERVER, true) . file_get_contents('php://input'),'INFO','',$logpath,'');
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        Log::write($xml,'INFO','',$logpath,'');
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();

        echo $returnXml;

        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======

        //以log文件形式记录回调信息
        /*
        $log_ = new Log_();
        $log_name="./notify_url.log";//log文件路径
        $log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
        */
        $message = "【接收到的notify通知】:\n".$xml."\n";

        Log::write($message,'INFO','',$logpath,'');
        if($notify->checkSign() == TRUE)
        {
            $paydata = $notify->getData();
            $weipayOrder = M('Weipay_order_list');
            $orderres = $weipayOrder->where(array('orderid'=>$paydata['out_trade_no'],'openid'=>$paydata['openid']))->find();
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作

                Log::write("【return_code通信出错】:\n".$xml."\n",'INFO','',$logpath,'');
            }elseif($notify->data["result_code"] == "FAIL"){
                //此处应该更新一下订单状态，商户自行增删操作
                Log::write("【result_code通信出错】:\n".$xml."\n",'INFO','',$logpath,'');
            }elseif($notify->data["result_code"] == "SUCCESS"){
                //此处应该更新一下订单状态，商户自行增删操作
                if($orderres){
                    $res = $weipayOrder->where(array('orderid'=>$paydata['out_trade_no'],'openid'=>$paydata['openid']))->save(array('status'=>1));
                    if($res){
                    Log::write(print_r($orderres, true),'INFO','',$logpath,'');
                        if($orderres['order_table'] == 'Store_goods_order'){
                            $orderTablemodel = M('Shopdoor_goods_order');
                            $orderTablemodel->where(array('order_id'=>$orderres['from_orderid'],'wx_openid'=>$paydata['openid']))->save(array('order_status'=>1));
                            $list=$orderTablemodel->where(array('order_id'=>$orderres['from_orderid'],'wx_openid'=>$paydata['openid']))->find();




                            $a=$list['order_info'];
                            $a=str_replace('商品名称:','', $a);
                            $a=str_replace('单价:','', $a);
                            $a=str_replace('数量:','', $a);
                            $a=str_replace('总价:','', $a);
                            $a=explode('|',$a);
                            $a=array_filter($a);
                            $str[]=explode(',',$a['0']);
                            $str='';
                            foreach ($a as $ke=>$v){
                            	$str[]=explode(',',$v);
                            }
                            //P($str);
                            $str2='';
             foreach ($str as $ke=>$v){
			if(strlen($v['0'])==6){
				$str2.=$v['0'].'       '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==9){
				$str2.=$v['0'].'     '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==12){
				$str2.=$v['0'].'   '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==15){
				$str2.=$v['0'].' '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}else{
				$str2.=$v['0'].'   '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}


		}


                            switch ($list['order_status']){
                            	case 0:$list['order_status']='未付款';break;
                            	case 1:$list['order_status']='已付款';break;
                            	case 2:$list['order_status']='已取消';break;
                            }
                            $beizhu = $list['order_extra_info'];
                            $list['order_ok_time']=date("Y-m-d H:i",$list['order_ok_time']);
//"是否付款:{$list['order_status']}
//".
                            $content="兴顺餐饮
".
"------------------
"."送达时间:{$list['sh_time']}
".
"------------------
"."买家：{$list['order_user']}
".
"下单时间：{$list['order_ok_time']}
".
"******************
".
"------------------
".
"商品名称 数量 单价 总价
{$str2}
".
"备注:{$beizhu}
".
"是否付款:{$list['order_status']}
".
"------------------
".
"合计：{$list['order_price']}元(含配送费￥0.0)
".
"------------------
".
"送货地址：{$list['order_adress']}
".
"联系电话：{$list['order_user_phone']}
";


                            //$w=new WifiPrint($_GET['token']);
                            //$sMsg = ($iErr = $w->test($content)) == 0 ?'打印成功' :'打印失败，错误码：'.$iErr;
                            //echo "<script>alert('".$sMsg."');</script>";



                        }elseif ($orderres['order_table'] == 'Paipai'){
			Log::write(print_r(array(
                                    'openid'    =>$orderres['openid'],
                                    'token'     =>$orderres['token'],
                                    'orderid'   => $orderres['from_orderid']
                                ),true),'INFO','',$logpath,'');
                            M('Intel_recharge')
                                ->where(array(
                                    'openid'    =>$orderres['openid'],
                                    'token'     =>$orderres['token'],
                                    'orderid'   => $orderres['from_orderid']
                                ))
                                ->data(array(
                                    'status'    => 1
                                ))
                                ->save();
                        }else if($orderres['order_table'] == 'Shopnc_order'){
                            //其他订单
                            $orderTablemodel = new Model('Order', 'shopnc_', 'mysql://root:d5HKkJ1238GrDjw599@112.124.62.6/shopnc');
                            $orderTablemodel->where(array('order_sn' => $orderres['from_orderid']))->save(array('order_state' => 60));
                        }else if($orderres['order_table'] == 'Usercenter_order_o2o'){ //O2O会员冲值
                            $orderTablemodel = M('Ye_record');
                            $orderTablemodel->where(array('id'=>$orderres['from_orderid']))->save(array('status'=>3));
                            /*  直接升级 */
                            $shopUsers = new Shopusers($orderres['token'],$orderres['openid'],0,$orderres['order_money']);
                            $shopUsers->updateGrade();
                            M('Shop_users')->where(array('openid'=>$orderres['openid'],'token'=>$orderres['token']))->setInc('money',$orderres['order_money']);
                        }else if($orderres['order_table'] == 'Usercenter_order'){
                            //其他订单
                            $orderTablemodel = M('Usercenter_money_record');
                            $orderTablemodel->where(array('id'=>$orderres['from_orderid']))->save(array('status'=>1));
                            $wxuser=M('Wxuser')->where(array('token'=>$orderres['token']))->find();
                            M('Usercenter_memberlist')->where(array('openid'=>$orderres['openid'],'uid'=>$wxuser['id']))->setInc('money',$orderres['order_money']);
                        }else if ($orderres['order_table'] == 'jiaotong_check'){      /*交通订单*/

                            $kiuei ['openid'] = $_POST['openid'];                       /*这是记录他已经的罚单*/
                            $kiuei ['cph'] = $_POST[''];
                            $kiuei ['site'] = $_POST[''];
                            $kiuei ['site_time'] = $_POST[''];
                            $kiuei ['fk_time'] = date('Y-m-d H:i:s');
                            $kiuei ['dispose'] =  $_POST['orderid'];
                            $kiuei ['payment'] = 1;
                            M('Check_did')->add($kiuei);

                            $iuejp ['token']= $this->token;                               /*把记录导入是金钱流出记录表 xuli*/
                            $iuejp ['openid']= $this->openid;
                            $iuejp ['path']= '交通缴费';
                            $iuejp ['checkid'] = $_POST['orderid'];
                            $iuejp ['money']= $_POST['dispose'];
                            $iuejp ['turnover']= 1;
                            $iuejp ['qian_time']=date('Y-m-d H:i:s');
                            M('Check_xuli')->add($iuejp);

                            $huiyund = M('Check_hy')->where(array('openid'=>$this->openid))->find();        /*一次支付判断会员等级*/
                            if($huiyund['vipsec']==3) {                                                         /*如果支付前还是注册会员，就给他升级*/
                                $idje['vipsec'] = 2;
                                $loief = M('Check_hy')->where(array('openid' => $this->openid))->save($idje);
                            }

                        }else if($orderres['order_table'] == 'jiaotong_check_yi'){                                      /*01.元扣费页面*/
                                $hyliucu ['path'] = '查询扣费0.1';
                                $hyliucu ['account'] = $_POST['check_money'];
                                $hyliucu ['checkid'] = $_POST['orderid'];
                                $hyliucu ['turnover'] = 1;
                                $hyliucu ['money'] = 0.1;
                                $hyliucu ['qian_time'] = date('Y-m-d H:i:s');
                                M('Check_xuli')->save($hyliucu);

                        }else if($orderres['order_table'] == 'jiaotong_check_cwhy'){                                    /*交通充值*/
                                $hyb = M('Check_hy')->where(array('openid'=>$_POST))->find();
                                if(empty($hyb['account'])){
                                    $hybadd ['vipount'] = 1;
                                    $hybadd ['account'] = $_POST['check_money'];
                                    M('Check_hy')->where(array('openid'=>$_POST['openid']))->save($hybadd);

                                    $hyliucu ['path'] = 'vip充值';
                                    $hyliucu ['account'] = $_POST['check_money'];
                                    $hyliucu ['checkid'] = $_POST['orderid'];
                                    $hyliucu ['turnover'] = 3;
                                    $hyliucu ['qian_time'] = date('Y-m-d H:i:s');
                                    M('Check_hy')->save($hyliucu);
                                }else{
                                    $hyq = $hyb['account'];
                                    $mang = $_POST['check_money'];
                                    $jiaqian = $hyq + $mang;
                                    $hybadd ['vipount'] = 1;
                                    $hybadd ['account'] = $jiaqian;
                                    M('Check_hy')->where(array('openid'=>$_POST['openid']))->save($hybadd);

                                    $hyliucu ['path'] = 'vip充值';
                                    $hyliucu ['account'] = $_POST['check_money'];
                                    $hyliucu ['checkid'] = $_POST['orderid'];
                                    $hyliucu ['turnover'] = 3;
                                    $hyliucu ['qian_time'] = date('Y-m-d H:i:s');
                                    M('Check_hy')->save($hyliucu);

                                }
                        }else if($orderres['order_table'] == 'Loan') {//Yo贷款订单
                            if($qing=strstr($orderres['from_orderid'],'_')){//还贷款过来的
                                $qing1=$qing;//保存下
                                $qing=trim($qing,'_');
                                //echo $qing;
                                $true_order=str_replace($qing1,'',$orderres['from_orderid']);
                                $order_info=M('No_credit_order')->where(array('orderid'=>$true_order))->find();
                                $data['oid']=$order_info['id'];
                                $data['money']=$order_info['monthly_repayments'];
                                $data['add_time']=time();
                                $data['token']=$order_info['token'];
                                $data['openid']=$order_info['openid'];
                                $data['paytype']=1;//微信支付
                                $data['paystatus']=1;
                                $data['qisu']=$qing;//期数
                                //这里没有加入uid
                                M('Hk_jl')->add($data);
                                //查询他是否是最后一期还款，如果是，就把订单变成最已全部还款状态
                                if($qing==$order_info['fenqi']){
                                    M('No_credit_order')->where(array('id'=>$order_info['id']))->save(array('paystatus'=>12));
                                }
                            }else{//首付过来
                                M("No_credit_order")->where(array("orderid"=>$orderres['from_orderid']))->save(array("paystatus"=>1,'paytype'=>1));//修改订单状态1代表微信支付

                                //如果是老用户就发短信发微信
                               // $pid=M('No_credit_order')->where(array())
                                $info=M('No_credit_order')->field('uid,token,title,openid')->where(array("orderid"=>$orderres['from_orderid']))->find();
                                if(M('Credit_users')->where(array('id'=>$info['uid']))->getField('pid')){//有图片代表老用户
                                    //发送短信消息
                                    $phone=M('Credit_users')->where(array('id'=>$info['uid']))->getField('phone');
                                    $token_phone=M('speeddial')->where(array('token'=>$info['token']))->getField('phone');
                                  //  $order=M('No_credit_order')->where(array('id'=>$_GET['id']))->getField('orderid');
                                    $info1="【如多分期】亲，我收到你的订单啦，是“".$info['title']."”么？真好！接下来是订单处理时间，我们在1天内给您回馈处理结果。有问题？打这个".$token_phone."，咱们的男神客服为您服务！";

                                    $openidYz=sendPhomeCode($this->token,$phone,$info1);
                                    $openidYz=json_decode($openidYz,true);
                                    M('No_credit_order')->where(array("orderid"=>$orderres['from_orderid']))->save(array('sms'=>3));//改状态发短信的
                                    //发微信
                                    $notichcontent ="【如多分期】亲，我收到你的订单啦，是“".$info['title']."”么？真好！接下来是订单处理时间，我们在1天内给您回馈处理结果。有问题？打这个".$token_phone."，咱们的男神客服为您服务！";
                                    msg($info['token'],$info['openid'],$notichcontent);
                                  /*  $postdata = array('openid'=>$info['openid'],'token'=>$info['token'],'content'=>$notichcontent);
                                    $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                                    $data = $this->api_notice_increment($url,http_build_query($postdata));
                                    if(!$data){
                                        $this->api_notice_increment($url,http_build_query($postdata));
                                    }*/

                                }


                               //把第一期给付了
                               /* $order_info=M('No_credit_order')->where(array('orderid'=>$orderres['from_orderid']))->find();
                                $data['oid']=$order_info['id'];
                                $data['money']=$order_info['monthly_repayments'];
                                $data['add_time']=time();
                                $data['token']=$order_info['token'];
                                $data['openid']=$order_info['openid'];
                                $data['paytype']=1;//微信支付

                                $data['paystatus']=1;
                                $data['qisu']=1;//期数
                                //这里没有加入uid
                                M('Hk_jl')->add($data);*/


                            }
                        }else if($orderres['order_table'] == 'Gta_chongzhi') {//国泰安会员冲值
                            M('Usercenter_money_record')->where(array('id'=>$orderres['from_orderid']))->save(array('status'=>1));
                            M('Gta_users')->where(array('token'=>$orderres['token'],'openid'=>$orderres['openid']))->setInc('money',$orderres['order_money']);
                           // $data['openid']=$orderres['openid'];

                        }else if($orderres['order_table'] == 'Store_new_goods_order') {//商城订单


                            //其他订单
                            $orderinfo = M("Product_cart_new")->field("total,productid,type,sid,score,price,truename")->where(array("orderid" => $orderres['from_orderid']))->find();//订单信息
                            $member = M("Usercenter_memberlist")->field("score")->where(array("openid" => $orderres['openid']))->find();//会员积分信息
                            $dscore = $member['score'] - $orderinfo['score'];
                            if ($orderinfo['sid'] != 0) {
                                M("sn")->where(array("id" => $orderinfo['sid']))->save(array("status" => 1));//修改优惠券状态
                            }
                            if ($dscore >= 0) {
                                M("Usercenter_memberlist")->where(array("openid" => $orderres['openid']))->save(array("score" => $dscore));//减少会员积分
                            }

	                        if($orderinfo['type']=="son"){
		                        $goods=M("Product_detail_new")->field("num")->where(array("id"=>$orderinfo['productid']))->find();
		                        $num=intval($goods['num'])-intval($orderinfo['total']);
		                        M("Product_detail_new")->where(array("id"=>$orderinfo['productid']))->save(array("num"=>$num));
	                        }else{
		                        $goods=M("Product_new")->field("num")->where(array("id"=>$orderinfo['productid']))->find();
		                        $num=intval($goods['num'])-intval($orderinfo['total']);
		                        M("Product_new")->where(array("id"=>$orderinfo['productid']))->save(array("num"=>$num));
	                        }
	                        M("Product_cart_new")->where(array("orderid"=>$orderres['from_orderid']))->save(array("paid"=>1));//修改订单状态
                            /*
                             * 下面是分销
                             *
                             */
                            $dopenid = M('Product_cart_new')->where(array('orderid' => $orderres['from_orderid']))->getField('dopenid');
                            #是否为互推
                            $bBoth = M('Media_users')->where(array(
                                'openid'    => $dopenid,
                                'from_openid'   => $orderres['openid']
                            ))->count() > 0;
			    $bSelf = ($dopenid == $orderres['openid']);
                            //如果$dopenid为真就去绑定他们的关系
                            if ($dopenid AND !$bBoth AND !$bSelf) {
                                //先确定得到佣金人的
                                $m_user = M('Media_users');
                                if(!$m_user->where(array('token'=> $orderres['token'],'openid'=>$dopenid))->find()){//不是徽商就加上
                                    $data3['token'] = $orderres['token'];
                                    $data3['openid'] = $dopenid;
                                    $data3['from_openid']='';
                                    $data3['is_buy'] = 0;
                                    $iID = M('Wxuser')
                                        ->where(array('token' => $orderres['token']))
                                        ->getField('id');

                                    $data3['nickname'] = M('Wxusers')
                                        ->where(array('uid' => $iID, 'openid' => $dopenid))
                                        ->getField('nickname');
                                    $data3['add_time'] =date('Y-m-d H:i:s',time());
                                    $m_user->add($data3);
                                }
                                //再确定两个人关系的徽商记录
                                if($id=$m_user->where(array('token'=>$orderres['token'],'openid'=>$orderres['openid']))->getField('id')){
                                    $m_user->where(array('id'=>$id))->save(array(
                                        'from_openid'=>$dopenid,
                                        'tuijian_time'=>time(),
                                        'is_buy'     => 1
                                    ));
                                }else{
                                    $data33['token'] = $orderres['token'];
                                    $data33['openid'] = $orderres['openid'];
                                    $data33['from_openid'] = $dopenid;
                                    $data33['tuijian_time'] = time();
                                    $data33['is_buy'] = 1;

                                    $iID = M('Wxuser')
                                        ->where(array('token' => $orderres['token']))
                                        ->getField('id');

                                    $data33['nickname'] = M('Wxusers')
                                        ->where(array('uid' => $iID, 'openid' => $orderres['openid']))
                                        ->getField('nickname');

                                    $data33['add_time'] =date('Y-m-d h:i:s',time());
                                    $m_user->add($data33);

                                }


                            }
                            /**
                             * 搞佣金
                             */

                            $set1 = M('Product_setting_new')->where(array('token' => $orderres['token']))->find();
                            if (
                                ($set1['is_distribution'] == 1 || $set1['is_distribution'] == 3)
                                AND !$bBoth  AND !$bSelf
                            ) {
                                //1级佣金
                                if ($set1['one'] != 0.00) {
                                    $one = M('Media_users')->where(array('token' => $orderres['token'], 'openid' => $orderres['openid']))->find();
                                    if ($one) {
                                        $data4['orderid'] = $orderres['from_orderid'];
                                        $data4['openid'] = $one['from_openid'];
                                        $data4['add_time'] = time();
                                        $data4['yj'] = $iYongJin = $set1['one'] * $orderinfo['price'] * $orderinfo['total'];
                                        $data4['yjbl'] = $set1['one'];
                                        $this->autoJS($data4['openid'], $iYongJin, $iStatus);
                                        $data4['status']=$iStatus;
                                        $data4['g_openid']=$orderres['openid'];
                                        $data4['productid']=$orderinfo['productid'];
                                        $data4['token']= $orderres['token'];
                                        $date4['date'] = date('Y-m-d');
                                        M('Edia_user_commission')->add($data4);
                                    }

                                }
                                //2级佣金
                                if ($set1['two'] != 0.00) {
                                    $two = M('Media_users')->where(array('token' => $orderres['token'], 'openid' => $one['from_openid']))->find();
                                    if ($two) {
                                        $data4['orderid'] = $orderres['from_orderid'];
                                        $data4['openid'] = $two['from_openid'];
                                        $data4['add_time'] = time();
                                        $data4['yj'] = $set1['two'] * $orderinfo['price'] * $orderinfo['total'];
                                        $data4['yjbl'] = $set1['two'];
                                        $data4['g_openid']=$orderres['openid'];
                                        $this->autoJS($data4['openid'], $data4['yj'], $iStatus);
                                        $data4['status']=$iStatus;
                                        $data4['productid']=$orderinfo['productid'];
                                        $data4['token']= $orderres['token'];
                                        $date4['date'] = date('Y-m-d');
                                        M('Edia_user_commission')->add($data4);
                                    }
                                }
                                //3级佣金
                                if ($set1['three'] != 0.00) {
                                    $three = M('Media_users')->where(array('token' => $orderres['token'], 'openid' => $two['from_openid']))->find();
                                    if ($three) {
                                        $data4['orderid'] = $orderres['from_orderid'];
                                        $data4['openid'] = $three['from_openid'];
                                        $data4['add_time'] = time();
                                        $data4['yj'] = $set1['three'] * $orderinfo['price'] * $orderinfo['total'];
                                        $data4['yjbl'] = $set1['three'];
                                        $this->autoJS($data4['openid'], $data4['yj'], $iStatus);
                                        $data4['status']=$iStatus;
                                        $data4['g_openid']=$orderres['openid'];
                                        $data4['productid']=$orderinfo['productid'];
                                        $data4['token']= $orderres['token'];
                                        $date4['date'] = date('Y-m-d');
                                        M('Edia_user_commission')->add($data4);
                                    }
                                }
                        }else if($orderres['order_table'] == 'mru') {

                        	/*
                        	M("Product_cart_new")->where(array("orderid"=>$orderres['from_orderid']))->save(array("paid"=>1));//修改订单状态
                        	//dopenid查出积分
                        	$productid=M("Product_cart_new")->where(array("orderid"=>$orderres['from_orderid']))->getField('productid');
                        	$jf=M('mru_qianggou')->where(array('id'=>$productid))->getField('jf');
                        	//发抢购卷
                        	//查出抢购卷价格
                        	$productid=M("Product_cart_new")->where(array("orderid"=>$orderres['from_orderid']))->find();
                        	$qgj=M('mru_qianggou')->where(array('id'=>$productid['productid']))->find();
                        	//插入抢购卷
                        	     $name="购买".$qgj['title']."获取";
                        	     M('mru_qgj')->add($a=array(
                        				'token'=>$productid['token'],
                        				'openid'=>$productid['wecha_id'],
                        				'price'=>$qgj['price3'],
                        				'add_time'=>time(),
                        				'yzm'=>$orderres['from_orderid'],
                        				'name'=>$name
                        		));
                                file_put_contents('/tmp/log.log', '我来发优惠券'.print_r($_SERVER, true) . print_r($a, true) . '|' . date('Y-m-d H:i:s'), FILE_APPEND);




                        	//查出红包
                        	$productid=M("Product_cart_new")->where(array("orderid"=>$orderres['from_orderid']))->getField('productid');
                        	$hongbao=M('mru_qianggou')->where(array('id'=>$productid))->getField('hongbao');

                        	$list=M("Product_cart_new")->where(array("orderid"=>$orderres['from_orderid']))->find();
                        	$name="购买".$list['title']."获取";
                        	if($list['dopenid']){
                        		//给好友+积分
								if(!M('mru_jfb')->where(array('openid'=>$list['dopenid'],'token'=>$list['token']))->setInc('num',$jf)){
                        			$jfb=M('mru_jfb')->add(array('openid'=>$list['dopenid'],'token'=>$list['token'],'num'=>$jf));
                        			//获取积分红包记录
                        			M('mru_xf')->add(array(
                        					'token'=>$list['token'],
                        					'openid'=>$list['dopenid'],
                        					'num'=>$jf,
                        					'fs'=>$name,
                        					'add_time'=>time(),
                        			));
                        		}else{
                        			//获取积分红包记录
                        			M('mru_xf')->add(array(
                        					'token'=>$list['token'],
                        					'openid'=>$list['dopenid'],
                        					'num'=>$jf,
                        					'fs'=>$name,
                        					'add_time'=>time(),
                        			));
                        		}


                        		//发红包
                        		//查出红包价格
                        		$productid=M("Product_cart_new")->where(array("orderid"=>$orderres['from_orderid']))->find();
                        		$qgj=M('mru_qianggou')->where(array('id'=>$productid['productid']))->find();
                        		//插入红包
                        		$name="购买".$qgj['title']."获取";
                        		if($qgj['hongbao']){
                        			M('mru_hb')->add(array(
                        					'token'=>$productid['token'],
                        					'openid'=>$list['dopenid'],
                        					'price'=>$qgj['hongbao'],
                        					'add_time'=>time(),
                        					'yzm'=>$orderres['from_orderid'],
                        					'name'=>$name
                        			));
                        			M('mru_xf')->add(array(
                        					'token'=>$list['token'],
                        					'openid'=>$list['dopenid'],
                        					'hongbao'=>$qgj['hongbao'],
                        					'fs'=>$name,
                        					'add_time'=>time(),
                        			));

                        		} */


                        		/* //给好友+红包
                        		if(!M('mru_jfb')->where(array('openid'=>$list['dopenid'],'token'=>$list['token']))->setInc('hongbao',$hongbao)){
                        			$jfb=M('mru_jfb')->add(array('openid'=>$list['dopenid'],'token'=>$list['token'],'hongbao'=>$hongbao));
                        			//获取积分红包记录
                        			M('mru_xf')->add(array(
                        					'token'=>$list['token'],
                        					'openid'=>$list['dopenid'],
                        					'hongbao'=>$hongbao,
                        					'fs'=>'购买商量获取红包',
                        					'add_time'=>time(),
                        			));
                        		}else{
                        			//获取积分红包记录
                        			M('mru_xf')->add(array(
                        					'token'=>$list['token'],
                        					'openid'=>$list['dopenid'],
                        					'num'=>$jf,
                        					'fs'=>'购买商量获取红包',
                        					'add_time'=>time(),
                        			));
                        		} */

                        	}
                        	/* //本人+积分
                        	if(!M('mru_jfb')->where(array('openid'=>$list['wecha_id'],'token'=>$list['token']))->setInc('num',$jf)){
                        		$jfb=M('mru_jfb')->add(array('openid'=>$list['openid'],'token'=>$list['token'],'num'=>$jf));
                        		//获取积分红包记录
                        		M('mru_xf')->add(array(
                        				'token'=>$_GET['token'],
                        				'openid'=>$_GET['openid'],
                        				'num'=>$jf,
                        				'openid'=>time(),
                        		));

                        		echo "<script>alert('积分+$jf');</script>";
                        	} */
                        	//购买人数加1
                        	M('mru_qianggou')->where(array('id'=>$productid))->setInc('num',1);
                        //}
                        }elseif($orderres['order_table'] == 'Health_orders'){
                            //修改订单状态
                            M("Product_cart_new")->where(array("orderid"=>$orderres['from_orderid']))->save(array("paid"=>1));//修改订单状态
                        }elseif($orderres['order_table'] == 'Laundry_recharge'){
                            //96洗衣店充值
                            M('Laundry_online_franchisee_liquidity')->where(array('id'=>$orderres['from_orderid']))->setField(array('online_flow_status'=>1));
                            $liquidityInfo = M('Laundry_online_franchisee_liquidity')->where(array('id'=>$orderres['from_orderid']))->field('online_id')->find();
                            $onlineInfo = M('Laundry_online_franchisee')->where(array('id'=>$liquidityInfo['online_id']))->find();
                            M('Laundry_online_franchisee')->where(array('id'=>$liquidityInfo['online_id']))->setField(array('balance'=>floatval($onlineInfo['balance'])+floatval($orderres['order_money'])));
                        }elseif($orderres['order_table'] == 'Laundry_order'){
                            //96洗衣店订单付款
                            M('Laundry_order')->where(array('order_sn'=>$orderres['from_orderid']))->setField(array('order_pay_status'=>1,'order_payment_status'=>2));
                        }elseif($orderres['order_table'] == 'Ieat_Ticket'){
                            //Ieat联盟购买团购券
                            M('Ieat_mall_ticket')->where(array('token'=>$orderres['token'],'ticket_id'=>$orderres['from_orderid']))->setField(array('is_buy'=>1,'openid'=>$orderres['openid']));
                            $ticketInfo = M('Ieat_mall_ticket')->where(array('token'=>$orderres['token'],'ticket_id'=>$orderres['from_orderid']))->field('ticket_sn,start_date,end_date，other_set')->find();
                            $otherSet = json_decode($ticketInfo['other_set'],true);
                            if($otherSet['sstk'] == 1){
                                $sstk = "支持随时退款";
                            }elseif($otherSet['sstk'] == 0){
                                $sstk = "不支持随时退款";
                            }
                            if($otherSet['gqtk'] == 1){
                                $gqtk = "支持过期退款";
                            }elseif($otherSet['sstk'] == 0){
                                $gqtk = "不支持过期退款";
                            }
                            $url = C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";
                            $contentText = "尊敬的用户，感谢您购买Ieat联盟团购券，请妥善保管保管您的团购券消费编号:".$ticketInfo['ticket_sn'].",使用有效期是".$ticketInfo['start_date']."至".$ticketInfo['end_date']."。此团购券".$sstk."、".$gqtk."祝您生活愉快！";
                            $postData = array('token'=>$orderres['token'],'openid'=>$orderres['openid'],'content'=>$contentText);
                            $this->api_notice_increment($url,http_build_query($postData));
                        }elseif($orderres['order_table'] == 'Ieat_Food'){
                            //特 限食物购买
                            M('Ieat_mall_food_order')->where(array('token'=>$orderres['token'],'id'=>$orderres['from_orderid']))->setField(array('is_buy'=>1,'openid'=>$orderres['openid']));
                            // $ticketInfo = M('Ieat_mall_ticket')->where(array('token'=>$orderres['token'],'ticket_id'=>$orderres['from_orderid']))->field('ticket_sn,start_date,end_date，other_set')->find();
                            // $otherSet = json_decode($ticketInfo['other_set'],true);
                            // if($otherSet['sstk'] == 1){
                            //     $sstk = "支持随时退款";
                            // }elseif($otherSet['sstk'] == 0){
                            //     $sstk = "不支持随时退款";
                            // }
                            // if($otherSet['gqtk'] == 1){
                            //     $gqtk = "支持过期退款";
                            // }elseif($otherSet['sstk'] == 0){
                            //     $gqtk = "不支持过期退款";
                            // }
                            // $url = C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";
                            // $contentText = "尊敬的用户，感谢您购买Ieat联盟团购券，请妥善保管保管您的团购券消费编号:".$ticketInfo['ticket_sn'].",使用有效期是".$ticketInfo['start_date']."至".$ticketInfo['end_date']."。此团购券".$sstk."、".$gqtk."祝您生活愉快！";
                            // $postData = array('token'=>$orderres['token'],'openid'=>$orderres['openid'],'content'=>$contentText);
                            // $this->api_notice_increment($url,http_build_query($postData));
                        }elseif($orderres['order_table']=="Commerce_Order"){
                            $openid=$orderres['openid'];
                            $orderid=$orderres['from_orderid'];
                            $order=M("Mainorder")->where(array("token"=>$orderres['token'],"openid"=>$openid,"ordernumber"=>$orderid))->find();
                            if(!$order){
                                exit("该订单不存在!");
                            }
                            if($order['paystatus']==1){
                                exit("该订单已经支付，请勿重复操作!");
                            }


                            $update['paystatus'] = 1;
                            $paytypetext = '微信支付';


                            if (M("Mainorder")->where(array("id" => $order['id'], "token" => $orderres['token'], "openid" => $orderres['openid']))->save($update)) {
                                //减掉积分
                                M('Shop_users')->where(array("token" => $orderres['token'], "openid" => $orderres['openid']))->setDec('score', $order['score_money']);

                                //                //新增积分
                                //                M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setInc('score',$addscore);
                                //更新复订单地址
                                $orderres = M("Mainorder")->where(array("id" => $order['id'], "token" =>$orderres['token'], "openid" => $orderres['openid']))->find();

                                $sideorderdata = M('Sideorder')->where(array('mid' => $orderres['id']))->select();
                                //减少库存
                                foreach ($sideorderdata as $korder => $vorder) {
                                    $detailInfo = array();
                                    $detailInfo = M("Sidedetail")->where(array("sid" => $vorder['id']))->select();
                                    foreach ($detailInfo as $v) {
                                        //减少库存
                                        //这里修改下，这里不减库存
                                     //   M('Shopware')->where(array('token' => $orderres['token'], 'id' => $v['gid']))->setDec('stock', $v['num']);
                                    }
                                }


                                M("Sideorder")->where(array('token' => $orderres['token'], 'mid' => $order['id']))->save(array('buyname' => $orderres['buyname'], 'tel' => $orderres['tel'], 'address' => $orderres['address'], 'paytype' => $orderres['paytype'],'paystatus'=>1));


                                /*
                                 * 通知微信
                                 */
                                $orderdata = M("Mainorder")->where(array("id" => $order['id'], "token" => $orderres['token'], "openid" => $orderres['openid']))->find();
                                $orderDetail = M("Sideorder")->field('tp_sidedetail.gname,tp_sidedetail.num,tp_sidedetail.price')->join('join tp_sidedetail on tp_sideorder.id=tp_sidedetail.sid')->where(array('tp_sideorder.mid' => $order['id'], "tp_sideorder.token" => $orderres['token'], "tp_sideorder.openid" => $orderres['openid']))->select();
                                $strDetail = "订单详情:\n";
                                foreach ($orderDetail as $k => $v) {
		                    $strDetail .= ($k + 1) . '、' . $v['gname'] . '×' . $v['num'] . " = ".$v['num']*$v['price']."元\n";
		                }

                                if($orderres['token'] != 'a5114ab1a60c81d04e86447a0bd123be'){
                                    $endStr = "\n请耐心等待店家送货.请即时确认收货，以便生成积分兑换商品!";
                                }else{
                                    $endStr='';
                                }

                                /*
                                  获取店铺ID
                                */
                                $tempShop = explode('|', $order['shopid']);
                                $shopData = M('Shop')->where(array('token' => $orderres['token'], 'id' => $tempShop[1]))->find();

                                $notichcontent = $this->wxusers['nickname'] . "您好,交易提醒\n订单编号：" . $orderdata['ordernumber'] . "\n创建时间:" . $orderdata['buytime'] . "\n订单总额:" . $orderdata['totalmoney'] . "元\n" . "支付方式:" . $paytypetext . "\n" . $strDetail . "收货人:" . $orderdata['buyname'] . "\n电话:" . $orderdata['tel'] . "\n地址:" . $orderdata['address'] . "\n配送商家:" . $shopData['username'] . "\n商家电话:" . $shopData['tel'] . $endStr;
                                $postdata = array('openid' => $orderres['openid'], 'token' => $orderres['token'], 'content' => $notichcontent);
                                $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                                $data = $this->api_notice_increment($url, http_build_query($postdata));
                                if (!$data) {
                                    $this->api_notice_increment($url, http_build_query($postdata));
                                }

                                /*
                                 * 发给店家
                                 */
                                $aStaff = M('Shop_staff')->where(array('token' => $orderres['token'], 'sid' => $shopData['id']))->find();
                                if ($aStaff) {
                                    $notichcontent = "有新订单了哦\n订单编号：" . $orderdata['ordernumber'] . "\n创建时间:" . $orderdata['buytime'] . "\n订单总额:" . $orderdata['totalmoney'] . "元\n" . "支付方式:" . $paytypetext . "\n" . $strDetail . "收货人:" . $orderdata['buyname'] . "\n电话:" . $orderdata['tel'] . "\n地址:" . $orderdata['address'];
                                    $postdata = array('openid' => $aStaff['openid'], 'token' => $orderres['token'], 'content' => $notichcontent);
                                    $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                                    $data = $this->api_notice_increment($url, http_build_query($postdata));
                                    if (!$data) {
                                        $this->api_notice_increment($url, http_build_query($postdata));
                                    }
                                }
                                Log::write("成功",'INFO','',$logpath,'');
                            } else {
                                Log::write("失败",'INFO','',$logpath,'');
                            }

                        }elseif($orderres['order_table']=="Course_order"){
                            //课程支付回调业务
                            $orderid = $orderres['from_orderid'];
                            $bZhifu = M('Context_shop')->where(array('openid'=>$paydata['openid'],'token'=>$orderres['token'],'orderID'=>$orderid))->save(array('pay_status'=>1));
                            if($bZhifu){
                                $aFindkechen = M('Context_shop')->where(array(
                                    'token'=>$orderres['token'],
                                    'orderID'=>$orderres['from_orderid']
                                ))->find();
                                $aKechen = M('Context_list')->where(array(
                                    'token'=>$orderres['token'],
                                    'id' =>$aFindkechen['cid']
                                ))->find();
                                $aUser = M('Course_user')->where(array(
                                    'token'=>$orderres['token'],
                                    'openid'=>$paydata['openid']
                                ))->setInc('score',$aKechen['score']);
                                if($aFindkechen['from_openid']){
                                    $aUsers = M('Course_user')->where(array(
                                        'token'=>$orderres['token'],
                                        'openid'=>$aFindkechen['from_openid']
                                    ))->setInc('score',$aKechen['scores']);
                                }
                                if(!$aUser){
                                    exit("该订单不存在!");
                                }
                            }else{
                                exit("该订单不存在!");
                            }
                        }elseif($orderres['order_table']=='Ynd_usercz'){  //由你定钱包充值
			WL('Ynd_usercz:' . print_r($orderres, true));
                            $openid = $orderres['openid'];
                            $userinfo = M('Ynd_user')->where(array('token'=>$orderres['token'],'openid'=>$openid))->find();
                            $data = array(
                                'money'=>$userinfo['money'] + $orderres['order_money']
                            );
                            if(M('Ynd_user')->where(array('id'=>$userinfo['id']))->save($data)){
                                if(M('Ynd_money')->add(array(
                                    'user_id' =>$userinfo['id'],
                                    'token' =>$orderres['token'],
                                    'openid' =>$orderres['openid'],
                                    'money' =>$orderres['order_money'],
                                    'type' =>2,
                                    'status' =>2,
                                    'add_time' =>date('Y-m-d H:i:s'),
                                    'administer_time' =>date('Y-m-d H:i:s')
                                ))){
                                   /* M('Ynd_record')->add(array(
                                        'user_id' =>$userinfo['id'],
                                        'token' =>$orderres['token'],
                                        'openid' =>$orderres['openid'],
                                        'type'=>'money',
                                        'rank'=>1,
                                        'info'=> '钱包充值',
                                        'content'=>$orderres['money'],
                                        'add_time' =>date('Y-m-d H:i:s')
                                    ));*/
                                    exit("充值成功");
                                }else{
                                    exit("充值失败");
                                }
                            }
                        }elseif($orderres['order_table']=='Ynd_fangdanpay'){  //由你定放单支付
				WL('Ynd_fangdanpay:' . print_r($orderres, true));
                            $openid = $orderres['openid'];
                            $userinfo = M('Ynd_user')->where(array('token'=>$orderres['token'],'openid'=>$openid))->find();
                            $data = array('status'=>1);
                            $aWhere = array(
                                'token'=>$orderres['token'],
                                'yuid' =>$userinfo['id'],
                                'payorderID'=>$orderres['from_orderid']
                            );
                            if(M('Ynd_fangdan')->where($aWhere)->save($data)){
                               /* M('Ynd_record')->add(array(
                                    'user_id' =>$userinfo['id'],
                                    'token' =>$orderres['token'],
                                    'openid' =>$orderres['openid'],
                                    'type'=>'money',
                                    'rank'=>0,
                                    'info'=> '放单支付',
                                    'content'=>$orderres['money'],
                                    'add_time' =>date('Y-m-d H:i:s')
                                ));*/
                                exit("放单成功");
                            }else{
                                exit("放单失败");
                            }
                        }elseif($orderres['order_table']=='Ynd_orderpay'){  //由你定付款支付
                            WL('Ynd_orderpay:' . print_r($orderres, true));
			    $openid = $orderres['openid'];
                            $userinfo = M('Ynd_user')->where(array('token'=>$orderres['token'],'openid'=>$openid))->find();
                            $data = array(
                                'pay_status'=>1,
                                'pay_time'=>date('Y-m-d H;i:s'));
                            $aWhere = array(
                                'token'=>$orderres['token'],
                                'yuid' =>$userinfo['id'],
                                'payorderID'=>$orderres['from_orderid']
                            );
                            if(M('Ynd_order')->where($aWhere)->save($data)){
                                exit("付款成功");
                            }else{
                                exit("付款失败");
                            }
                        }elseif($orderres['order_table']=='Commerce_dyb'){//德亿币订单
                            $openid=$orderres['openid'];
                            $orderid=$orderres['from_orderid'];
                            $order=M("Mainorder")->where(array("token"=>$orderres['token'],"openid"=>$openid,"ordernumber"=>$orderid))->find();
                            if(!$order){
                                exit("该订单不存在!");
                            }
                            if($order['paystatus']==1){
                                exit("该订单已经支付，请勿重复操作!");
                            }


                            $update['paystatus'] = 1;
                            $paytypetext = '微信支付';


                            if (M("Mainorder")->where(array("id" => $order['id'], "token" => $orderres['token'], "openid" => $orderres['openid']))->save($update)) {
                                $data4['add_time']=time();
                                $data4['token']=$orderres['token'];
                                $data4['openid']=$orderres['openid'];
                                $data4['num']=$order['xianjin_b'];
                                $data4['type']=1;
                                $data4['statuts']=1;
                                M('Dyb_score')->add($data4);//这里是.Dyb_score得意币记录表里插一条记录，增加得意币
                                //新增得意币
                                M('Shop_users')->where(array("token"=>$orderres['token'],"openid"=>$orderres['openid']))->setInc('dyb',$order['xianjin_b']);
                                //减掉得意币
                                if($order['score_money']>0){
                                    $data4['type']=2;
                                    $data4['num']= $order['score_money']*(-1);
                                    M('Dyb_score')->add($data4);//这里是.Dyb_score得意币记录表里插一条记录，减少得意币
                                    M('Shop_users')->where(array("token" => $orderres['token'], "openid" => $orderres['openid']))->setDec('dyb', $order['score_money']);
                                }

                                /*
                                更新积分
                                */
                                if($order['score']>0){
                                    $scoreData = array();
                                    $scoreData['token']=$orderres['token'];
                                    $scoreData['openid']=$orderres['openid'];
                                    $scoreData['score'] = $order['score'];
                                    $scoreData['type'] = 8;
                                    $scoreData['addtime'] = date("Y-m-d H:i:s",time());
                                    M('Dy_score')->add($scoreData);//这里是增加积分
                                    M('Shop_users')->where(array("token" => $orderres['token'], "openid" => $orderres['openid']))->setInc('score', $order['score']);
                                }



                                //更新复订单地址
                                $orderres = M("Mainorder")->where(array("id" => $order['id'], "token" =>$orderres['token'], "openid" => $orderres['openid']))->find();

                                $sideorderdata = M('Sideorder')->where(array('mid' => $orderres['id']))->select();
                                //减少库存
                                foreach ($sideorderdata as $korder => $vorder) {
                                    $detailInfo = array();
                                    $detailInfo = M("Sidedetail")->where(array("sid" => $vorder['id']))->select();
                                    foreach ($detailInfo as $v) {
                                        //减少库存
                                        M('Shopware')->where(array('token' => $orderres['token'], 'id' => $v['gid']))->setDec('stock', $v['num']);
                                    }
                                }


                                M("Sideorder")->where(array('token' => $orderres['token'], 'mid' => $order['id']))->save(array('buyname' => $orderres['buyname'], 'tel' => $orderres['tel'], 'address' => $orderres['address'], 'paytype' => $orderres['paytype'],'paystatus'=>1));


                                /*
                                 * 通知微信
                                 */
                                $orderdata = M("Mainorder")->where(array("id" => $order['id'], "token" => $orderres['token'], "openid" => $orderres['openid']))->find();
                                $orderDetail = M("Sideorder")->field('tp_sidedetail.gname,tp_sidedetail.num')->join('join tp_sidedetail on tp_sideorder.id=tp_sidedetail.sid')->where(array('tp_sideorder.mid' => $order['id'], "tp_sideorder.token" => $orderres['token'], "tp_sideorder.openid" => $orderres['openid']))->select();
                                $strDetail = "订单详情:\n";
                                foreach ($orderDetail as $k => $v) {
                                    $strDetail .= ($k + 1) . '、' . $v['gname'] . '×' . $v['num'] . "\n";
                                }

                                /*
                                  获取店铺ID
                                */
                                $tempShop = explode('|', $order['shopid']);
                                $shopData = M('Shop')->where(array('token' => $orderres['token'], 'id' => $tempShop[1]))->find();
                                if($shopData['print_key'] && $shopData['print_domain']){
                                    Vendor('Wuxian.WuxianPrint');
                                    $wuxianprint = new WuxianPrint($shopData['print_key'],$shopData['print_domain']);
                                    $return = $wuxianprint->print_shop_order($orderdata,$strDetail,$this->tpl['name']);
                                    if($return['success'] == true){
                                        M("Mainorder")->where(array("id" => $order['id'], "token" => $orderres['token'], "openid" => $orderres['openid']))->save(array('is_print'=>1));
                                    }
                                }


                                $notichcontent = $this->wxusers['nickname'] . "您好,交易提醒\n订单编号：" . $orderdata['ordernumber'] . "\n创建时间:" . $orderdata['buytime'] . "\n订单总额:" . $orderdata['totalmoney'] . "元\n" . "支付方式:" . $paytypetext . "\n" . $strDetail . "收货人:" . $orderdata['buyname'] . "\n电话:" . $orderdata['tel'] . "\n地址:" . $orderdata['address'] . "\n配送商家:" . $shopData['username'] . "\n商家电话:" . $shopData['tel'] . "\n感谢您对德亿堡的支持！德亿吃，堡快乐！祝您生活愉快！";
                                $postdata = array('openid' => $orderres['openid'], 'token' => $orderres['token'], 'content' => $notichcontent);
                                $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                                $data = $this->api_notice_increment($url, http_build_query($postdata));
                                if (!$data) {
                                    $this->api_notice_increment($url, http_build_query($postdata));
                                }


                                Log::write("成功",'INFO','',$logpath,'');
                            } else {
                                Log::write("失败",'INFO','',$logpath,'');
                            }

                        }
                    }
                    }

                }else{
                    Log::write("【订单更新失败】:Store_goods_order-ID-".$paydata['out_trade_no'],'INFO','',$logpath,'');
                }
                Log::write("【通信成功】:\n".$xml."\n",'INFO','',$logpath,'');
            }

            //商户自行增加处理流程,
            //例如：更新订单状态
            //例如：数据库操作
            //例如：推送支付完成信息
        }



    /*
     *  自动 结算
     */
    public function autoJS($openid, $iYongJin, &$iStatus = 0)
    {
        $is_auto = M('Product_setting_new')
            ->where(array('token'=>$this->token))
            ->getField('is_auto_js');
        if ($is_auto) {
            $iStatus = 1;
            return M('Media_users')->where(array(
                'openid' => $openid,
                'token' => $this->token
            ))->setInc('yongjin', $iYongJin);
        }
        return true;
    }

    /*
     *  维权通知
     */
    public function feedback(){
       echo 'feedback';

    }

    /*
     * 警告通知
     */
    public function warning(){
        echo 'warning';

    }

}


?>
