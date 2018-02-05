<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><script async="" src="./shopping_js/gt.js"></script>
<meta charset="utf-8">
<title>下单完成</title>
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta http-equiv="x-dns-prefetch-control" content="on">
<link rel="stylesheet" type="text/css" href="./money_css/global.css">
<style type="text/css">
.wrapper{
	display: block;
	width: 100%;
	height: auto;
	background-color: #eeeeee;
}
.top-bar{
	box-sizing: border-box;
	width: 100%;
	height: 20px;
	line-height: 20px;
	padding-left: 5px;
	background-color: #d03535;
	color: #ffffff;
	font-size: 12px;
}
.logistics{
	position: relative;
	width: 100%;
	height: 110px;
	background-color: #eeeeee;
	overflow: hidden;
}
.logistics *{
	position: absolute;
}
.span-pack{
	top: -35px;
	left: 22px;
	width: 4px;
	height: 50px;
	background-color: #BABDBF;
	opacity: 0;
	z-index: 10;
	animation: span-pack .3s ease forwards;
	-webkit-animation: span-pack .3s ease forwards;
}
@keyframes span-pack{
	0%{top:-35px;opacity: 0;}
	100%{top:0;opacity: 1;}
}
@-webkit-keyframes span-pack{
	0%{top:-35px;opacity: 0;}
	100%{top:0;opacity: 1;}
}
.icon-pack{
	display: block;
	top: 22px;
	left: 11px;
	width: 26px;
	height: 26px;
	background-size: cover;
	z-index: 20;
	opacity: 0;
	animation: icon-pack .3s ease .3s forwards;
	-webkit-animation: icon-pack .3s ease .3s forwards;
}
@keyframes icon-pack{
	0%{opacity: 0;transform: scale(1);}
	50%{opacity: 0.6;transform: scale(1.2);}
	100%{opacity: 1;transform: scale(1);}
}
@-webkit-keyframes icon-pack{
	0%{opacity: 0;-webkit-transform: scale(1);}
	50%{opacity: 0.6;-webkit-transform: scale(1.2);}
	100%{opacity: 1;-webkit-transform: scale(1);}
}
.p-pack{
	top: 30px;
	left: 640px;
	width: 100%;
	height: 12px;
	line-height: 12px;
	color: #666666;
	opacity: 0;
	animation: p-pack .3s ease .6s forwards;
	-webkit-animation: p-pack .3s ease .6s forwards;
}
@keyframes p-pack{
	0%{left:640px;opacity: 0;}
	100%{left: 48px;opacity: 1;}
}
@-webkit-keyframes p-pack{
	0%{left:640px;opacity: 0;}
	100%{left: 48px;opacity: 1;}
}
.span-car{
	top: -75px;
	left: 22px;
	width: 4px;
	height: 75px;
	background-color: #e6e6e6;
	opacity: 0;
	animation: span-car .3s ease .9s forwards;
	-webkit-animation: span-car .3s ease .9s forwards;
}
@keyframes span-car{
	0%{top: -75px;opacity: 0;}
	100%{top:35px;opacity: 1;}
}
@-webkit-keyframes span-car{
	0%{top: -75px;opacity: 0;}
	100%{top:35px;opacity: 1;}
}
.icon-car{
	display: block;
	top: 59.5px;
	left: 11px;
	width: 26px;
	height: 26px;
	background-size: cover;
	z-index: 20;
	opacity: 0;
	animation: icon-car .3s ease 1.2s forwards;
	-webkit-animation: icon-car .3s ease 1.2s forwards;
}
@keyframes icon-car{
	0%{opacity: 0;transform: scale(1);}
	50%{opacity: 0.6;transform: scale(1.2);}
	100%{opacity: 1;transform: scale(1);}
}
@-webkit-keyframes icon-car{
	0%{opacity: 0;-webkit-transform: scale(1);}
	50%{opacity: 0.6;-webkit-transform: scale(1.2);}
	100%{opacity: 1;-webkit-transform: scale(1);}
}
.p-car{
	top: 67.5px;
	left: 640px;
	width: 100%;
	height: 12px;
	line-height: 12px;
	color: #b2b2b2;
	opacity: 0;
	animation: p-car .3s ease 1.5s forwards;
	-webkit-animation: p-car .3s ease 1.5s forwards;
}
@keyframes p-car{
	0%{left: 640px;opacity: 0;}
	100%{left: 48px;opacity: 1;}
}
@-webkit-keyframes p-car{
	0%{left: 640px;opacity: 0;}
	100%{left: 48px;opacity: 1;}
}
.product{
	box-sizing: border-box;
	width: 100%;
	margin-bottom: 10px;
	background-color: #ffffff;
}
.product-info{
	box-sizing: border-box;
	width: 100%;
	height: auto;
	padding: 10px;
}
.product-info:after{
	display: block;
	height: 0;
	content: "";
	clear: both;
}
.product-info img{
	float: left;
	width: 35%;
	height: auto;
	margin-right: 12px;
}
.product-info p{
	line-height: 17px;
	font-size: 12px;
	color: #999999;
}
.product-info .name{
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.price-pre{
	text-decoration: line-through;
	margin-right: 5px;
}
.price-cur{
	color: #d03636;
	margin-right: 5px;
}
.price-label{
	display: inline-block;
	padding: 0 5px;
	background-color: #d03636;
	color: #ffffff;
}
.product-btns{
	box-sizing: border-box;
	width: 100%;
	padding: 10px;
	border-top: 1px solid #eeeeee;
}
.product-btns:after{
	display: block;
	height: 0;
	content: "";
	clear: both;
}
.btn-outter{
	float: left;
	width: 50%;
}
.btn-outter-single{
	width: 100%;
}
.btn-outter a{
	display: block;
	box-sizing: border-box;
	padding: 10% 0;
	height: 0;
	line-height: 0;
	border-radius: 2px;
	text-align: center;
}
.btn-outter-single a{
	padding: 5% 0;
}
.btn-cancel{
	margin-right: 5px;
	background-color: #999999;
	color: #ffffff;
}
.btn-cancel:active{
	background-color: #c3c3c3;
}
.btn-outter-single .btn-contact{
	margin-left: 0;
}
.btn-contact{
	margin-left: 5px;
	background-color: #0fadbb;
	color: #ffffff;
}
.btn-contact:active{
	background-color: #8cbd5e;
}
.problem{
	width: 100%;
	padding-bottom: 10px;
	margin-bottom: 10px;
	background-color: #ffffff;
}
.problem:after{
	display: block;
	height: 0;
	content: "";
	clear: both;
}
.problem-title{
	box-sizing: border-box;
	width: 100%;
	height: 30px;
	line-height: 30px;
	padding-left: 10px;
	color: #666666;
	font-size: 14px;
	border-bottom: 1px solid #eeeeee;
}
.problem-box{
	width: 100%;
}
.question{
	box-sizing: border-box;
	width: 100%;
	padding: 10px 0 5px;
	color: #999999;
}
.dot{
	position: relative;
	top: -1.5px;
	display: inline-block;
	width: 6px;
	height: 6px;
	margin: 0px 5px 0 10px;
	border-radius: 6px;
	background-color: #d03636;
	vertical-align: middle;
}
.answer{
	display: none;
	position: relative;
	box-sizing: border-box;
	width: 100%;
	padding: 10px 15px;
	background-color: #eeeeee;
}
.answer-arrow{
	position: absolute;
	top: -5px;
	left: 7px;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-bottom: 7px solid #eeeeee;
}
.answer p{
	line-height: 17px;
	color: #999999;
}
.answer p a{
	color: #0000cc;
	text-decoration: underline;
}
.contact{
	background-color: #ffffff;
	padding-top: 10px;
	padding-bottom: 12px;
}
.contact img{
	display: block;
	width: 76%;
	height: auto;
	margin: 0 auto;
}
.contact p{
	margin-top: 5px;
	text-align: center;
	color: #999999;
}
.contact .wechat-code{
	font-size: 14px;
	color: #666666;
	font-weight: bold;
}
.wechat-code span{
	-webkit-user-select: all;
	user-select: all;
}
.contact .link{
	margin-top: 20px;
	font-weight: bold;
}
.contact .link a{
	color: #0000cc;
	text-decoration: underline;
}
.contact .copyright{
	color: #cccccc;
	font-size: 12px;
	margin-top: 5px;
	transform: scale(0.85);
	transform-origin: top;
}
	.cimg{
		width: 100px;
		height: 100px;
		float: right;
		position: absolute;
		margin-left: 230px;
		margin-top: -116px;
	}
	.cimg img{
		width:80%;
	}
</style>
</head>
<body>
	<div class="wrapper">
		<div class="logistics">
			<span class="span-pack"></span>
			<i class="icon-pack"></i>
			<p class="p-pack">您的商品已打包，准备出库</p>
			<span class="span-car"></span>
			<i class="icon-car"></i>
			<p class="p-car">等待出库，快递小哥将火速赶到</p>
		</div>
		<div class="product">
			<div class="product-info">
				<?php
					require_once "mysqldbread.php";
					$openid = $_GET['openid'];
					if(empty($openid)){
						header('Location:'.$gj_url.'/money_adv_index.php?id='.$_GET['id']);
					}
					$id = $_GET['id'];
					$sprice = $_GET['sprice'];
					$img = $db->get_row("select * from card_product where id='$id'");
				$img = object_array($img);
					$time = $db->get_row("select create_date,pay_status from card_product_receive where del_flag = 0 and user_openid='$openid' and merchant_product_id='$id'");
				$time = object_array($time);
				?>
				<img src="<?php echo $img['title_img'];?>">
				<p class="name" style="height: 21.75px; line-height: 21.75px;">商品名称: <?php echo $img['name'];?></p>
				<p style="height: 21.75px; line-height: 21.75px;">下单时间: <?php echo $time['create_date'];?></p>
				<p style="height: 21.75px; line-height: 21.75px;">订单状态: 下单成功 </p>

				<p style="height: 21.75px; line-height: 21.75px;">
					<span class="price-pre">¥<?php echo $img['original_price'];?></span>
					<span class="price-cur">¥<?php echo $img['present_price'];?></span>
					<span class="price-label">
										<?php
											if($time['pay_status']==1){
												echo '货到付款';
											}else{
												echo '在线购买';
											}
										?>
										</span>
				</p>
			</div>
			<div class="cimg"><img src="imgs/sdoubi.png"></div>
			<!--<div class="product-btns">
				<div class="btn-outter btn-outter-single">
					<a class="btn-contact" href="http://<?php /*echo $_SERVER['HTTP_HOST'];*/?>/doubi/">进入逗币中心</a>
				</div>
			</div>-->
		</div>
		<div class="contact">
			<img src="<?php echo $img['service_qrcode'];?>">
			<p class="wechat-code">联系电话： <span><?php echo $img['service_number'];?></span></p>
			<p class="copyright">©深圳市聚效互动科技有限公司</p>
		</div>
	</div>
</body></html>