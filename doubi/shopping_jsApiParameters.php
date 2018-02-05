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
    $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'shop.log');
    $log = Log::Init($logHandler, 15);
    $tools = new JsApiPay();
    $input = new WxPayUnifiedOrder();
    $input->SetBody("广加支付");
    $input->SetAttach("广加提供");
    $input->SetOut_trade_no($out_trade_no);
    //$input->SetTotal_fee(100*$money);
    $input->SetTotal_fee(100*$money);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("广加");
    $input->SetNotify_url($gj_url."/shopping_notify.php");
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openid);
    $order = WxPayApi::unifiedOrder($input);
    $jsApiParameters = $tools->GetJsApiParameters($order);
    $app_id = $order["appid"];
    $prepay_id = 'prepay_id='.$order['prepay_id'];
    $doubi_info = array(
        'id' => guid(),
        'user_id' => $openid,
        'quantity' => $money,
        'coin_type' => '3',
        'coin_status' => '0',
        'user_type' => '1',
        'create_by'=>$openid,
        'update_by'=>$openid,
        'prepay_id'=>$prepay_id,
        'out_trade_no' => $out_trade_no,
        'create_date' => date('Y-m-d H:i:s', time()),
        'update_date' => date('Y-m-d H:i:s', time())
    );
    // $result = $mon->add('card_doubi',$doubi_info);
    $result = $db->query("INSERT INTO card_doubi SET ". $db->get_set($doubi_info));
    echo $jsApiParameters;
}
//支付成功
if($action == 'weixin_update'){
    //更新支付信息表
    $appid = $_POST['appid'];
    $prepay_id = $_POST['prepay_id'];
    $openid = $_POST['openid'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s', time());
    $results = $db->query("update card_doubi set coin_status=1,update_date='$date' where coin_type=3 and user_id='$openid' and prepay_id='$prepay__id'");
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
    $id = $db->query("INSERT INTO card_product_receive SET ". $db->get_set($data));
    //充值送币

    $purchase_people = $db->get_row("select purchase_people from card_product where id='$_POST[proid]'");
    //$datass['purchase_people'] = $purchase_people->purchase_people+9;
    $datass = $purchase_people->purchase_people+9;
    $wheres = "id='$_POST[proid]'";
    $dd = $db->query("update card_product set purchase_people=$datass where id='$_POST[proid]'");
}
?>

