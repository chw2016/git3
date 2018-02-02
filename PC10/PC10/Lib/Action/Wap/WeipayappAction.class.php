<?php
class WeipayappAction extends BaseAction{
    public $token;
    public $wecha_id;
    public $tpl = null;

    protected function _initialize(){
        Vendor('weipay.WxPayPubHelper.WxPayPubHelper');
        Vendor('weipay.WxPayPubHelper.WxPay.pub.config');
        parent::_initialize();


        $appdata = M('Weipay_config')->where(array('token'=>$this->token))->find();
        $this->appdata = $appdata;
        $JS_API_CALL_URL = C('JS_API_CALL_PAY_URL').$this->token;
        $NOTIFY_URL = C('NOTIFY_URL').$this->token;
        $this->NOTIFY_URL = $NOTIFY_URL;
        $SSLCERT_PATH = './upload/'.$this->token.'/apiclient_cert_'.$this->token.'.pem';
        $SSLKEY_PATH = './upload/'.$this->token.'/apiclient_key_'.$this->token.'.pem';
        WxPayConf_pub::set_config($appdata['appid'],$appdata['partnerkey'],$appdata['appkey'],$appdata['appsecret'],$JS_API_CALL_URL,
            $SSLCERT_PATH,$SSLKEY_PATH,$NOTIFY_URL,60);
        $this->token = $_REQUEST['token'];
        $this->assign('token',$this->token);
        $this->openid	= session('openid');
        $this->assign('openid',$this->openid);
    }

    public function index()
    {
        if ($_GET['openid'] || $_GET['wecha_id']) {
            session('openid', $_GET['openid']?:$_GET['wecha_id']);
            $url =  C('site_url').'Wap/Weipayapp/index/token/'.$_GET['token'].'/';
            header('Location:'.$url);
            return;
        }
        $oImgModel = M('Imag');
        $this->assign(array(
            'phone'=>$a = $oImgModel->where(array(
                'token'=>$this->_sToken,
                'app'=>'Weipayapp',
                'type'=>'phones'
            ))->find(),
        ));
        $this->display();
    }

    public function generate()
    {
        $this->type         = 'weipayapp';
        $this->orderid      = time();
        $this->returnurl    = C('site_url').'/Wap/Weipayapp/index';
        $this->order_money  = max(0, (int)$_POST['order_money']);
    }


    //支付
    public function pay(){

        $this->generate();

        $weipayOrder = M('Weipay_order_list');
        /*
        $type = $_POST['type'];
        $orderid = $_POST['orderid'];
		$returnurl = $_POST['returnurl'];
        $order_money = $_POST['order_money'];
        */
        $type        = $this->type;
        $orderid     = $this->orderid;
		$returnurl   = $this->returnurl;
        $order_money = $this->order_money;
        $order_text   = '微信支付';

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
                $paydata['from_orderid'] = "$orderid";
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
                /*
                $this->assign('jsApiParameters',$jsApiParameters);
                $this->assign('order_money',$order_money);
                $this->assign('order_text',$order_text);
                $this->assign('orderid',$orderid);
				$this->assign('returnurl',htmlspecialchars_decode($returnurl));
                $this->assign('order_type',$typearr[$type]);
                */
                $aRet = array(
                    'jsApiParameters' => $jsApiParameters,
                    'order_money'     => $order_money,
                    'order_text'      => $order_text,
                    'orderid'         => $orderid,
                    'returnurl'       => htmlspecialchars_decode($returnurl),
                    'order_type'      => $typearr[$type],
                );
                exit(json_encode(array(
                    'code'  => 0,
                    'data'  => $aRet
                )));
                //$this->display();
            }else{
                exit(json_encode(array(
                    'code'  => 1,
                    'msg'   =>'error'
                )));
            }
        }else{
            exit(json_encode(array(
                'code'  => 1,
                'msg'  =>'付款类型不存在'
            )));
        }
    }

    /*
     * 原生jsp
     */

    public function getpackage(){
        echo 'getpackage';

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
