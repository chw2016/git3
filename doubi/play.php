<?php
session_start();
ini_set('date.timezone','Asia/Shanghai');
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//$out_trade_no = WxPayConfig::MCHID.date("YmdHis");
$appId = WxPayConfig::APPID;

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();
//$openId = 'oXgRcuKECWvDLQo9M-7_TaBkdfDY';
if(empty($_SESSION['openId'])){
	$_SESSION['openId'] = $openId;
}
//只读数据库
require_once('mysqldbread.php');

$group_id = $_SESSION['current_group'];
$type = $_SESSION['current_type'];
//充值送逗币显示根据区域
$group_info = "select * from device_group_info where id='$group_id' and del_flag=0";
$group_info = $db->get_row($group_info);
$group_info = object_array($group_info);
if(!empty($group_info['doubi_back'])){
	$sqlnum = "select sum(quantity) quantity from card_doubi where user_id='$_SESSION[openId]' and user_type!=2 and del_flag=0 and coin_status=1";
	$min_result = $db->get_var($sqlnum);
}else{
	$sqlnum = "select sum(quantity) quantity from card_doubi where user_id='$_SESSION[openId]' and user_type=1 and del_flag=0 and coin_status=1";
	$min_result = $db->get_var($sqlnum);
}
?>
<!DOCTYPE html>
<html lang="ch" manifest="">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>广加智能终端控制台</title>
	<meta charset="utf-8"><meta name="apple-touch-fullscreen" content="YES">
	<meta name="format-detection" content="telephone=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta http-equiv="Expires" content="-1">
	<meta http-equiv="pragram" content="no-cache">
	<meta name="viewport" content="width=640, user-scalable=no, target-densitydpi=device-dpi">
	<link rel="stylesheet" type="text/css" href="css/play.css?002">
	<link rel="stylesheet" type="text/css" href="css/zepto.alert.css?15">

	<!--移动端版本兼容 -->
</head>
<body>

	<header>
		<aside class="top_left">
			<div class="sha"><img src="./imgs/doubi.png"></div>
			<div class="xia">逗币</div>
			<div class="mon"><b><span id="balances"><?php if(empty($min_result)){echo 0;}else{echo $min_result;}?></span></b></div>
		</aside>
		<div class="title">
			<table>
				<tr>
					<td onclick="location.href='<?php echo $gj_url;?>/money_voucher.php?openid=<?php echo $openId;?>'">已领凭证</td>
				</tr>
				<tr>
					<td onclick="location.href='<?php echo $gj_url;?>/money_evidence_record.php?openid=<?php echo $openId;?>'">消费记录</td>
				</tr>
			</table>
		</div>
	</header>
	<div class="clear" style="clear:both"></div>
	<div class="hen">点击机器对应字母启动，若扣余额未启动，10分钟后重新扫码退币</div>
	<section>
		<?php
		if($type == 2){
			$showDeviceCode=$_SESSION['default_code'];
			$sql = "select * from device_group where status='1' and del_flag=0 and group_id='".$group_id."' order by ords";
		}else {
			$showDeviceCode = $group_id;
		}

		$domainUrl = $_SESSION['domainUrl'];
		$url_params = "group_id=$group_id"."&type=".$type."&default_code=".$showDeviceCode ;
		?>

		<ul id="consume">
		<?php
		if($type == 2){
				///$query_result = $db->query($sql);

				?>

				<?php
					$results = $db->get_results($sql);
					foreach($results as $vv ){
					?>
					<li   dataid="<?php echo $vv->pay_price;?>" dno="<?php echo $vv->device_command;?>"  did="<?php echo $vv->device_id;?>" dprice="<?php echo intval($vv->pay_price)*10?>"
					cd="<?php echo $vv->group_code;?>"
					><b><?php echo $vv->group_code;?></b>&nbsp;
						<?php echo ($vv->pay_price*10)."逗币/次";?>
					</li>
					<?php }

		} else {
		?>
		<!--class="lion"-->
		<li  dno="<?php echo $showDeviceCode;?>" cd="A"
		><b>A</b><img src="imgs/icon_01.png" />1逗币</li>

		<?php
		}
			//关闭连接 old
			//$db->close();
		?>
		</ul>
	</section>
	<!--<footer>
		<div class="menu" style="position: relative;">
			<a href="http://www.7i1.cn/wxok/code.php?openid=<?php /*echo $openId;*/?>" style="color:#fff;text-decoration:none;">
         <b style="font-size:30px;color:#fff;margin-bottom:-21px;display:block;">
            点击客服联系</b><br/>温馨提示:若扣余额未启动,10分钟内重新扫码,自动退币.</a>
   </div>
</footer>-->

	<div class="footer">
		<menu class="ucenter-menu">
			<ul>
				<li onclick="location.href='http://www.7i1.cn/wxok/code.php?openid=<?php echo $_SESSION['openId'];?>'">联系客服</li>
				<li onclick="location.href='<?php echo $domainUrl;?>/wxpay/redirect.php?<?php echo $url_params;?>'">微信支付</li>
				
					<li onclick="location.href='<?php echo $gj_url;?>/index.php'">点击返回</li>
			
			</ul>
		</menu>
	</div>
<input type="hidden" id="default_code" value="<?php echo $showDeviceCode;?>"/>
<input type="hidden" id="current_m" value="<?php echo $min_p;?>"/>
<input type="hidden" id="openId" value="<?php echo $openId;?>"/>
<input type="hidden" id="appId" value="<?php echo $appId;?>"/>

<script type="text/javascript" src="js/zepto.min.js"></script>
<script type="text/javascript" src="js/zepto.alert.js"></script>
<script type="text/javascript" src="js/fastclick.min.js"></script>
<script type="text/javascript" src="js/app.js?10001" charset="UTF-8"></script>
</body>
</html>