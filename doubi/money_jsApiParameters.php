<?php
ini_set('date.timezone','Asia/Shanghai');
require_once "./lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';
require_once('mysqldb.php');
$action = trim($_GET['action']);
if($action == 'weixin_pay'){

    $money = $_POST['money'];
    $tmoney = $money/10;
    $openid = $_POST['openid'];
    $merchant_id = $db->get_row("select * from card_merchant where openid='$openid' and status=1 and del_flag=0");
    $merchant_id = object_array($merchant_id);
    $out_trade_no = WxPayConfig::MCHID.date("YmdHis");
    $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
    $log = Log::Init($logHandler, 15);
    $tools = new JsApiPay();
    $input = new WxPayUnifiedOrder();
    $input->SetBody("广加充值");
    $input->SetAttach("广加提供");
    $input->SetOut_trade_no($out_trade_no);
    $input->SetTotal_fee(100*$tmoney);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("广加");
    $input->SetNotify_url($gj_url."/money_notify.php");
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openid);
    $order = WxPayApi::unifiedOrder($input);
    $jsApiParameters = $tools->GetJsApiParameters($order);
    $app_id = $order["appid"];
    $prepay_id = 'prepay_id='.$order['prepay_id'];
	$doubi_info = array(
        'id' => guid(),
        'user_id' => $openid,
        'merchant_id' => $merchant_id['id'],
        'quantity' => $money,
        'coin_type' => '2',
        'coin_status' => '0',
        'user_type' => '2',
		'prepay_id'=>$prepay_id,
        'create_by'=>$openid,
        'update_by'=>$openid,
        'out_trade_no'=>$out_trade_no,
        'create_date' => date('Y-m-d H:i:s', time()),
        'update_date' => date('Y-m-d H:i:s', time())
    );
    $result = $db->query("INSERT INTO card_doubi SET ". $db->get_set($doubi_info));
    echo $jsApiParameters;
}
//支付成功
if($action == 'weixin_update'){
    //更新支付信息表
    $appid = $_POST['appid'];
    $prepay_id = $_POST['prepay_id'];
    $openid = $_POST['openid'];
    $money = $_POST['money'];
	$date = date('Y-m-d H:i:s', time());

	$results = $db->query("update card_doubi set coin_status=1,update_date='$date' where coin_type=2 and user_id='$openid' and prepay_id = '$prepay_id'");

    $merchant_id = $db->get_row("select * from card_merchant where openid='$openid' and status=1 and del_flag=0");
    $merchant_id = object_array($merchant_id);
    //商家
    $reg = $merchant_id['total_doubi']+trim($_POST['money']);
    $uid = $db->query("update card_merchant set total_doubi=$reg where openid='$openid'");
    if ($uid) {
        $all = $db->get_row("select * from card_merchant where openid='$openid' and status=1 and del_flag=0");
        $all = object_array($all);
        echo $all['total_doubi'];
    } else {
        echo 0;
    }
}
?>

