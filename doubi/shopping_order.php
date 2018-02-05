<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><script async="" src="./shopping_js/gt.js"></script>
<meta charset="utf-8">
<title>下单完成</title>
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta http-equiv="x-dns-prefetch-control" content="on">
<link rel="stylesheet" type="text/css" href="./shopping_css/global.css">
<link rel="stylesheet" type="text/css" href="./shopping_css/order.css">
<style type="text/css">
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
						header('Location:'.$gj_url);
					}
					$id = $_GET['id'];
					$sprice = $_GET['sprice'];
					$img = $db->get_row("select * from card_product where del_flag = 0 and id='$id'");
				$img = object_array($img);
					$time = $db->get_row("select create_date,pay_status from card_product_receive where del_flag = 0 and user_openid='$openid' and merchant_product_id='$id'");
				$time = object_array($time);
				?>
				<img src="<?php echo $img['title_img'];?>">
				<p class="name" style="height: 21.75px; line-height: 21.75px;">商品名称: <?php echo $img['name'];?></p>
				<p style="height: 21.75px; line-height: 21.75px;">下单时间: <?php echo $time['create_date'];?></p>
				<p style="height: 21.75px; line-height: 21.75px;">订单状态: 下单成功 </p>
				<p style="height: 21.75px; line-height: 21.75px;">
					<?php if($sprice != 20){?>
					<b style="color:#f00;margin-right: 4px;font-size: 14px;"><?php echo $sprice;?></b>个逗币已送出</b>
					<?php }?>

				</p>
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
			<div class="product-btns">
				<div class="btn-outter btn-outter-single">
					<a class="btn-contact" href="<?php echo $gj_url;?>">进入逗币中心</a>
				</div>
			</div>
		</div>
		<div class="contact">
			<img src="<?php echo $img['service_qrcode'];?>">
			<p class="wechat-code">联系电话： <span><?php echo $img['service_number'];?></span></p>
			<p class="copyright">©深圳市聚效互动科技有限公司</p>
		</div>
	</div>
</body></html>