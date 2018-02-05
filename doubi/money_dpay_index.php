<?php 
	ini_set('date.timezone','Asia/Shanghai');
	require_once "./lib/WxPay.Api.php";
	require_once "WxPay.JsApiPay.php";
	require_once 'log.php';
	require_once "mysqldb.php";
	$tools = new JsApiPay();
	$openid = $tools->GetOpenid();
	$openid = 'og5WUjmApU2pOqbZlxrppXCNhsio';
	//$money = isset($_GET['money']) && !empty($_GET['money']) ? $_GET['money'] :1;
	$id = isset($_GET['id']) && !empty($_GET['id']) ? $_GET['id'] :1;
	$merchant_id = isset($_GET['merchant_id']) && !empty($_GET['merchant_id']) ? $_GET['merchant_id']:1;
	$detail = $db->get_row("select `desc`,`title`,`url`,`adv_img`,`small_title`,`price`,`remarks` from card_deposit where del_flag=0 and id='$id'");
	$detail = object_array($detail);
	//访问次数
	$update_click = $db->query("update card_deposit set clicks=clicks+1 where id='$id'");
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="stylesheet" href="./css/frozen.css">
	<link rel="stylesheet" href="./css/mobi.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum- scale=1.0, maximum-scale=1.0,user-scalable=no">
	<meta http-equiv="Cache-Control" content="max-age=0">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>交定金</title>
	<style>
		.menu{
			position: fixed;
			left: 0;
			bottom: 0;
			width: 100%;
			height: 45px;
			line-height: 45px;
			font-size: 18px;
			background: #ff8b00	;
			color: #fff;
			z-index: 10;
			text-align: center;
		}
		.payup{
			font-size: 18px;
			height: 40px;
			line-height: 40px;
			color:#fff;
			display: block;
			width: 46%;
			border-radius: 5px;
			background-color: #ff8b00;
			margin-bottom: 60px;
		}
		.fadeInDown{
			padding: 10px;
		}
		.header{
			position: fixed;
			top: 0;
			z-index: 1000;
			width: 100%;
			text-align: center;
			color: #fff;
			font-weight: 800;
			font-size: 20px;
			line-height: 40px;
			height: 40px;
			background-color: #ff8b00;
		}
	</style>
</head>
<body>
<section class="header">
	<?php echo $detail['title'];?>
	</section>
<div class="space-20"></div>
<header class="ucenter-t" style="margin-top: 30px;">
	信息详情</header>
<section class="ucenter-main animated fadeInDown">
	<?php echo $detail['small_title'];?>
	<div class="space-20"></div>
	<?php echo $detail['desc'];?>
	<div class="space-20"></div>
	<img src="<?php echo $detail['adv_img'];?>">
	<div class="space-20"></div>
	<?php echo $detail['remarks'];?>
</section>
<div class="space-20" style="margin-bottom: 60px;"></div>
	<div align="center">
		<button  type="button" class="payup" onclick="callpay()" >预存送逗币</button>
	</div>
<div class="footer" style="margin-top: 20px;">
	<menu class="menu" onclick="location.href='<?php echo $detail['url'];?>'">
		详情
	</menu>
</div>
</body>
<input type="hidden" class="money" value="<?php echo $detail['price'];?>">
<input type="hidden" class="pay_id" value="<?php echo $id;?>">
<input type="hidden" class="openid" value="<?php echo $openid;?>">
<input type="hidden" class="merchant_id" value="<?php echo $merchant_id;?>">
<input type="hidden" class="out_trade_no" value="">
<script type="text/javascript" src="money_js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="money_js/dpay.js?10002"></script>
</html>