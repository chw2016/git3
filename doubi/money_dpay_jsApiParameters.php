<?php 
ini_set('date.timezone','Asia/Shanghai');
require_once "./lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';
require_once 'mysqldb.php';
$action = trim($_GET['action']);
if($action == 'weixin_pay'){
    $money = $_POST['money'];
    $openid = $_POST['openid'];
    $merchant_id = $_POST['merchant_id'];
    $out_trade_no = WxPayConfig::MCHID.date("YmdHis").rand(10000,99999);
    $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
    $log = Log::Init($logHandler, 15);
    $tools = new JsApiPay();
    $input = new WxPayUnifiedOrder();
    $input->SetBody("广加充值");
    $input->SetAttach("广加提供");
    $input->SetOut_trade_no($out_trade_no);
    $input->SetTotal_fee(1);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("广加");
    $input->SetNotify_url("http://www.sniperchw.com/doubi/money_dpay_notify.php");
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openid);
    $input->SetSub_Mch_id("1341024101");
    $order = WxPayApi::unifiedOrder($input);
    $jsApiParameters = $tools->GetJsApiParameters($order);
    $app_id = $order["appid"];
    $prepay_id = 'prepay_id='.$order['prepay_id'];
   $doubi = '';
    if($money == '50'){
        $doubi = '500';
    }elseif($money == '100'){
        $doubi = '1000';
    }elseif($money == '10'){
        $doubi = '100';
    }elseif($money == '200'){
        $doubi = '2000';
    }else{
        $doubi = '100';
    }
    $doubi_info = array(
        'id' => guid(),
        'user_id' => $openid,
        'merchant_id'=>$merchant_id,
        'quantity' => $doubi,
        'coin_type' => '4',
        'coin_status' => '0',
        'user_type' => '1',
        'type' => '3',
        'create_by'=>$openid,
        'update_by'=>$openid,
        'out_trade_no' => $out_trade_no,
        'prepay_id' => $prepay_id,
        'create_date' => date('Y-m-d H:i:s', time()),
        'update_date' => date('Y-m-d H:i:s', time())
    );
    $result = $db->query("INSERT INTO card_doubi SET ". $db->get_set($doubi_info));
   $out = json_encode(array('out_trade_no'=>$out_trade_no));
    $newJson = json_encode(
        array_merge(
            json_decode($jsApiParameters, true),
            json_decode($out, true)
        )
    );
    echo $newJson;
}
//支付成功
if($action == 'weixin_update'){
    $openid = $_POST['openid'];
    $merchant_id = $_POST['merchant_id'];
    $out_trade_no = $_POST['out_trade_no'];
    $prepay_id = $_POST['prepay_id'];
    //添加配置信息表
    $guid = guid();
    //更新逗币的配置id字段
    $date = date('Y-m-d H:i:s', time());
    $results = $db->query("update card_doubi set coin_status=1,update_date='$date',card_config_id='$guid' where prepay_id='$prepay_id' and coin_type=4 and user_id='$openid' and merchant_id='$merchant_id'");
    $datas['id'] = $guid;
    $datas['merchant_id'] =  $merchant_id;
    $datas['create_date'] =  date('Y-m-d H:i:s', time());
    $datas['brand_name'] = '定金';
    $datas['quantity'] = 10;
    $datas['title'] = '定金';
    $datas['per_num'] = 2;
    $datas['card_status'] = 0;
    $datas['user_id'] = $openid;
    $datas['recommend_ord'] = rand(10,20);//推荐排序
    $datas['sub_title'] = '定金';
    $datas['begin_timestamp'] = date('Y-m-d H:i:s', time());
    $datas['end_timestamp'] =date('Y-m-d H:i:s', time());
    $datas['create_by'] = $openid;
    $datas['create_date'] = date('Y-m-d H:i:s', time());
    $datas['update_date'] = date('Y-m-d H:i:s', time());
    $datas['surplus_quantity'] = 10;
    $datas['type'] = 2;
    $datas['description'] = '定金默认添加配置信息';
    $result = $db->query("INSERT INTO card_config SET ". $db->get_set($datas));
}
?>

