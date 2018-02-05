<?php 
ini_set('date.timezone','Asia/Shanghai');
require_once "./lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';
require_once('mysqldb.php');
$action = trim($_GET['action']);
if($action == 'weixin_pay'){
    $money = $_POST['money'];
    $openid = $_POST['openid'];
    $out_trade_no = WxPayConfig::MCHID.date("YmdHis");
    $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
    $log = Log::Init($logHandler, 15);
    $tools = new JsApiPay();
    $input = new WxPayUnifiedOrder();
    $input->SetBody("广加支付");
    $input->SetAttach("广加提供");
    $input->SetOut_trade_no($out_trade_no);
    $input->SetTotal_fee(100*$money);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("广加");
    $input->SetNotify_url($gj_url."/money_adv_notify.php");
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openid);
    $order = WxPayApi::unifiedOrder($input);
    $jsApiParameters = $tools->GetJsApiParameters($order);
    $app_id = $order["appid"];
    $prepay_id = 'prepay_id='.$order['prepay_id'];
    $pay_info = array(
        'id' => guid(),
        'app_id' => WxPayConfig::APPID,
        'from_username' => $openid,
        'type'=>'1',
        'out_trade_no'=>$out_trade_no,
        'ticket' => $money,
        'prepay_id'=>$prepay_id,
        'contents' => $jsApiParameters,
        'create_date'=>date('Y-m-d H:i:s',time()),
    );
    //$result = $mon->add('pay_info',$pay_info);
    $result = $db->query("INSERT INTO pay_info SET ".$db->get_set($pay_info));
    if($result==false){
        echo "0";
    }else {
        echo $jsApiParameters;
    }
}
//支付成功
if($action == 'weixin_update'){
    //更新支付信息表
    $appid = $_POST['appid'];
    $prepay_id = $_POST['prepay_id'];
    $openid = $_POST['openid'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s', time());
    $results = $db->query("update pay_info set status=1,update_date='$date' where type=1 and prepay_id='$prepay_id' and from_username='$openid' and app_id='$appid'");
    //地址
    $data['id'] = guid();
    $data['merchant_product_id'] = $_POST['proid'];
    $data['user_openid'] = $openid;
    $data['address'] = $_POST['address'];
    $data['province'] = $_POST['province'];
    $data['city'] = $_POST['city'];
    $data['district'] = $_POST['district_str'];
    $data['phoneno'] = $_POST['phone'];
    $data['name'] = $_POST['name'];
    $data['create_by'] = guid();
    $data['id'] = guid();
    $data['pay_status'] = 2;
    $data['create_date'] = date('Y-m-d H:i:s',time());
    $data['update_date'] = date('Y-m-d H:i:s',time());
    $id = $db->query("INSERT INTO card_product_receive SET ". $db->get_set($data));
    $purchase_people = $db->get_row("select purchase_people from card_product where id='$_POST[proid]'");
    $datass = $purchase_people->purchase_people+9;
    $dd = $db->query("update card_product set purchase_people=$datass where id='$_POST[proid]'");
}
?>

