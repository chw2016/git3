<?php
	session_start();
	ini_set('date.timezone','Asia/Shanghai');
	require_once 'WxPay.JsApiPay.php';
	require_once 'mysqldb.php';
	//$tools = new JsApiPay();
	//$openid = $tools->GetOpenid();
	$openid = 'oXgRcuKECWvDLQo9M-7_TaBkdfDY';
	if(!empty($openid)){
		$_SESSION['shop_openid'] = $openid;
	}
	$id = isset($_GET['id']) && !empty($_GET['id'])?$_GET['id']:"03cca6b887714e86807831ba223c4f0f";
	$one = $db->get_row("select * from card_product where del_flag=0 and id='$id'");
	if(empty($one)){exit();}
	$one = object_array($one);
	$img = explode(',',$one['main_img']);
	$imgs = array();
	foreach($img as $v){
		$imgs[]['img'] = $v;
	}
	//访问次数
$update_click = $db->query("update card_product set clicks=clicks+1 where id='$id'");
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><script type="text/javascript" async="" src="./shopping_js/rt.js"></script>
<meta charset="utf-8">
<title><?php echo $one['name'];?></title>
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="stylesheet" type="text/css" href="./shopping_css/global.css">
<style type="text/css">
.banner-item {
	min-height: 200px;
}
.banner-item img {
	display: block;
	width: 100%;
}
.price-item {
	height: 180px;
	background: url(http://ebiz.viewlayer.cn/theme/images/bg-pricetab.jpg);
	padding: 0 10px;
}
.price-item .price-tab {
	height: 90px;
	border-bottom: 2px dotted #000;
	text-align: right;
}
.price-item .price-tab em {
	height: 90px;
	line-height: 90px;
	color: #FFAF18;
	font-weight: bold;
	font-size: 48px;
	font-size: 11vw;
	margin-right: 10px;
	float: left;
}
.price-item .price-tab span {
	width: 50px;
	display: inline-block;
	margin-top: 30px;
}
.price-item .price-tab span label {
	width: 50px;
	display: inline-block;
	font-size: 14px;
	text-align: center;
	color: #B6B3AD;
}
.price-item .amount-tab {
	overflow: hidden;
	clear: both;
	height: 40px;
	line-height: 40px;
}
.price-item .amount-tab .timmer {
	float: right;
	color: #FFF;
}
.price-item .amount-tab .timmer em {
	color: #FF6F20;
	padding: 0 2px;
}
.price-item .amount-tab .amount{
	color: #9B968E;
}
.price-item .amount-tab .amount em {
	color: #FFF;
	padding: 0 2px;
}
.price-item .btn-tab a {
	display: block;
	background: #ea7215;
	line-height: 40px;
	height: 40px;
	border-radius: 5px;
	font-weight: bold;
	font-size: 16px;
	text-align: center;
	color: #FFF;
}
.detail-item h2 {
	color: #CDCDCD;
	background: #EBEBEB url(http://ebiz.viewlayer.cn/theme/images/icon-title.png) 13px 11px no-repeat;
	background-size: 10px;
	height: 30px;
	line-height: 30px;
	padding-left: 29px;
}
.detail-content{
	padding: 10px;
	line-height: 25px;
}
.detail-pic img{
	display: block;
	width: 100%;
}
a.totop {
	position: fixed;
	bottom: 0;
	width: 50px;
	height: 50px;
	display: block;
	z-index: 9;
}
.btn-item {
	height: 50px;
	background: #ea7215 url(http://ebiz.viewlayer.cn/theme/images/btn-totop.png) 18px 18px no-repeat;
	background-size: 15px;
	bottom: 0;
	position: fixed;
	width: 100%;
}
.btn-item a {
	height: 50px;
	line-height: 50px;
	display: block;
	border-left: 1px solid #FFF;
	text-align: center;
	font-size: 16px;
	color: #FFF;
	margin: 0 0 0 50px;
	font-weight: bold;
}
.info{
	padding-bottom: 50px;
}
#im{
	position: fixed;
	left: 15px;
	bottom: 65px;
	width: 50px;
	height: 50px;
	background-color: rgba(255, 255, 255, 0.8);
	border-radius: 3px;
	background-image: url(http://ebiz.viewlayer.cn/theme/images/im.png);
	background-size: 25px 25px;
	background-position: center 20%;
	background-repeat: no-repeat;
	color: #666;
	font-size: 10px;
	text-align: center;
}
#im span{
	position: absolute;
	bottom: 6%;
	left: 0;
	text-align: center;
	width: 100%;
	transform: scale(0.85);
	-webkit-transform: scale(0.85);
	transform-origin: center;
	-webkit-transform-origin: center;
}
.comment-title{
	width: 100%;
	height: 32px;
	line-height: 32px;
	background-color: #e8373c;
	color: #ffffff;
	font-size: 14px;
	text-align: center;
}
.comment{
	width: 100%;
	height: 360px;
	margin-bottom: 20px;
	overflow: hidden;
}
.comment-inner{
	width: 100%;
}
.comment-name{
	line-height: 25px;
	color: #e8373c;
	margin-left: 10px;
	font-size: 14px;
}
.comment-content{
	line-height: 25px;
	margin-left: 10px;
	font-size: 14px;
	color: #666666;
	border-bottom: 1px dotted #cccccc;
}
</style>
<script type="text/javascript" src="./shopping_js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="./shopping_js/81"></script>
<script type="text/javascript">
	var end_timestamp = "1461938490";
	var this_time = Date.parse(new Date()) / 100;
	$(document).ready(function(){
		
		timedCount();
		//countPrice();
		loadImage();
	});

	//计时器
	var timedCount=function (){

		this_time=this_time + 1;
		if(this_time != 0){
			setTimeout("timedCount()",1000);
		}
		var sub_all_sec = end_timestamp - this_time;
		var sub_day = parseInt(sub_all_sec / 86400);
		var sub_hour = parseInt((sub_all_sec%86400) / 3600);
		var sub_min = parseInt((sub_all_sec%3600) / 60);
		var sub_sec = parseInt(sub_all_sec%60);
		if(sub_day < 10){
			sub_day = "0" + sub_day;
		}
		if(sub_hour < 10){
			sub_hour = "0" + sub_hour;
		}
		if(sub_min < 10){
			sub_min = "0" + sub_min;
		}
		if(sub_sec < 10){
			sub_sec = "0" + sub_sec;
		}
		$("#day").html(sub_day);
		$("#hour").html(sub_hour);
		$("#min").html(sub_min);
		$("#sec").html(sub_sec);
	}


	/*var countPrice=function(){
		var price = '198';
		var old_price = 1980;
		var dec_price = old_price - parseInt(price);
		$("#old_price").html('￥' +old_price);
		$("#dec_price").html('￥' +dec_price);
	}*/

	//种子随机数
	function mySeedRandom(){
		var min = 0;
		var max = 1;
		var seed = 0;
		var myDate = new Date();
		seed = myDate.getFullYear() * myDate.getMonth() * myDate.getDate();
	    seed = (seed * 9301 + 49297) % 233280;
	    var rnd = seed / 233280.0;
	    return min + rnd * (max - min);
	}


	var loadImage = function(){
		var src = $(".banner-item img").attr("src");
		var img = new Image();
		var flag = true;
		img.onload = function(){
			loadImages();
		}
		img.onerror = function(){
			loadImages();
		}
		setTimeout(function(){
			loadImages();
		},1500);
		var loadImages = function(){
			if(flag){
				flag = false;
				$(".lazy-img").each(function(){
					var $ele = $(this);
					$ele.attr({'src':$ele.attr("data-src")});
				});
			}
		}
		img.src = src;
	}
</script>
</head>
<body>
	<a name="top"></a>
	<div class="page-item banner-item">
		 <img src="<?php echo $one['title_img'];?>">
	</div>
	<div class="price-item">
		<div class="price-tab">
			<em>¥<?php echo $one['present_price'];?></em>
			<span>
				<label>原价</label>
				<label id="old_price">￥<?php echo $one['original_price'];?></label>
			</span>
			<span>
				<label>折扣</label>
				<label><?php echo $one['discount'];?></label>
			</span>
			<span>
				<label>节省</label>
				<label id="dec_price">￥<?php echo intval($one['original_price'])-intval($one['present_price']);?></label>
			</span>
		</div>
		<div class="amount-tab">
			<!--<span class="timmer">
				<em id="day">00</em>天
				<em id="hour">04</em>小时
				<em id="min">10</em>分钟
				<em id="sec">15</em>秒
			</span>-->
			<span class="amount">
				已有<em id="buy_count"><?php echo $one['purchase_people'];?></em>人购买
			</span>
		</div>
		<div class="btn-tab" onclick="location.href='shopping_address.php?id=<?php echo $one['id'];?>&price=<?php echo $one['present_price'];?>'">
			<a class="btn-buy" href="javascript:void(0);">立即购买（送<?php echo $one['present_price'];?>个逗币）</a>
		</div>
	</div>
	<div class="page-item detail-item">
		<h2>产品介绍</h2>
		<p class="detail-content"><b><?php echo $one['description_title'];?></b><br><?php echo $one['remarks'];?></p>
		<p class="detail-pic">
			<?php foreach($imgs as $key=>$v){?>
				<img class="lazy-img" data-src="<?php echo $v['img'];?>">
			<?php }?>
		</p>
	</div>
	<div class="info">本活动最终解析权归 深圳市聚效互动科技有限公司</div>
	<a href="#top" class="totop"></a>
	<div class="btn-item btn-buy" onclick="location.href='shopping_address.php?id=<?php echo $one['id'];?>&price=<?php echo $one['present_price'];?>'">
		<a href="javascript:void(0)">立即购买（送<?php echo $one['present_price'];?>个逗币）</a>
	</div>
	<script type="text/javascript" src="./shopping_js/ctnry.js"></script>
	<script type="text/javascript">
	</script>

</body></html>