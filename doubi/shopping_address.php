<?php
	session_start();
	$openid = $_SESSION['shop_openid'];
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><script async="" src="./shopping_js/gt.js"></script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
<title>限时秒杀</title>
<link rel="stylesheet" type="text/css" href="./shopping_css/phone-reset.css">
<script type="text/javascript" src="./shopping_js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="./shopping_js/regionv3.js"></script>
<script type="text/javascript" src="./shopping_js/81"></script>
<style type="text/css">
body{
	background-color: #e3e3e3;
}
.wrapper{
	box-sizing: border-box;
	width: 100%;
	height: auto;
	padding: 3.5%;
}
.header{
	box-sizing: border-box;
	width: 100%;
	height: 50px;
	line-height: 50px;
	padding-left: 6%;
	background-color: #f6f6f6;
	color: #666666;
	font-size: 16px;
}
.content-info{
	box-sizing: border-box;
	width: 100%;
	background-color: #ffffff;
	padding: 5px 6% 15px 6%;
	margin-bottom: 20px;
}
.content-info-inner{
	width: 100%;
	height: 50px;
}
.content-info p{
	float: right;
	box-sizing: border-box;
	width: 75%;
	height: 50px;
	border-bottom: 1px solid #999999;
	overflow: hidden;
}
.ios .content-info p{
	border-bottom: 0.5px solid #999999;
}
.content-info label{
	float: left;
	width: 25%;
	height: 50px;
	line-height: 53px;
	color: #666666;
	font-size: 14px;
}
.content-info input[type="text"],.content-info input[type="tel"]{
	width: 100%;
	height: 20px;
	line-height: 20px;
	padding: 15px 0;
	-webkit-appearance: none;
	border: none;
	font-size: 14px;
}
.content-info input[name="mobile"]{
	width: 54%;
}
#btn-code{
	float: right;
	width: 45%;
	height: 30px;
	margin-top: 10px;
	-webkit-appearance: none;
	border: none;
	background-color: #dd3636;
	color: #ffffff;
	border-radius: 4px;
	font-size: 12px;
}
.content-address{
	box-sizing: border-box;
	width: 100%;
	padding: 1em 6%;
	background-color: #ffffff;
}
.content-address:after{
	display: block;
	height: 0;
	content: "";
	clear: both;
}
.content-address select{
	float: right;
	box-sizing: border-box;
	width: 75%;
	height: 35px;
	padding-left: 5px;
	margin-bottom: 1em;
	font-size: 14px;
	-webkit-appearance: none;
	border: 1px solid #999999;
	border-radius: 4px;
	color: #999999;
	background-image: url(http://res.viewlayer.cn/ebiz/octtest/select-arrow3.png);
	background-color: #ffffff;
	background-size: auto 100%;
	background-position: right;
	background-repeat: no-repeat;
	background-clip: border-box;
}
.ios .content-address select{
	border: 0.5px solid #999999;
}
.content-address label{
	float: left;
	width: 25%;
	height: 35px;
	line-height: 35px;
	color: #666666;
	font-size: 14px;
}
.content-address p{
	float: right;
	box-sizing: border-box;
	width: 75%;
	height: 50px;
	border-bottom: 1px solid #999999;
	overflow-y: hidden;
}
.ios .content-address p{
	border-bottom: 0.5px solid #999999;
}
.content-address input{
	height: 20px;
	line-height: 20px;
	width: 100%;
	padding: 15px 0;
	-webkit-appearance: none;
	border: none;
	font-size: 14px;
}
.content-address-detail{
	float: left;
	width: 100%;
	height: 50px;
}
.content-address-detail label{
	height: 50px;
	line-height: 53px;
}
.content-paymode{
	box-sizing: border-box;
	width: 100%;
	padding: 1em 6%;
	background-color: #ffffff;
	margin-top: 20px;
}
.content-paymode div{
	display: none;
	height: 50px;
	line-height: 50px;
	font-size: 14px;
	color: #333333;
}
.content-paymode img{
	float: left;
	height: 34px;
	margin-top: 8px;
}
#btn-buys{
	display: block;
	width: 48%;
	height: 50px;
	margin: 20px 0;
	line-height: 50px;
	float: right;
	text-align: center;
	background-color: #019e97;
	border-radius: 4px;
	color: #ffffff;
	font-size: 16px;
}
#btn-buy{
	display: block;
	width: 100%;
	height: 50px;
	float: left;
	margin: 20px 0;
	line-height: 50px;
	text-align: center;
	background-color: #ff8b00;
	border-radius: 4px;
	color: #ffffff;
	font-size: 16px;
}
</style>
</head>
<body class="ios">
	<script type="text/javascript">
		var u = navigator.userAgent;
		if((u.indexOf("iPhone")>-1||u.indexOf("iPad")>-1)&&window.devicePixelRatio>=2){
			document.body.setAttribute("class","ios");
		}
	</script>
	<div class="wrapper">
		<p class="header">收货人信息</p>
		<div class="content-info">
			<div class="content-info-inner">
				<label>收货人</label>
				<p>
					<input id="name" name="name" type="text">
				</p>
			</div>
			<div class="content-info-inner">
				<label>手机号</label>
				<p>
					<input id="mobile" name="mobile" type="tel" style="width:100%">
				</p>
			</div>
					</div>
		<div class="content-address">
			<label>所在地区</label>
			<select name="province" id="province" style="color: rgb(51, 51, 51);"><option value="0">省份</option><option value="340000">安徽</option><option value="110000">北京</option><option value="500000">重庆</option><option value="350000">福建</option><option value="620000">甘肃</option><option value="440000" selected="selected">广东</option><option value="450000">广西</option><option value="520000">贵州</option><option value="460000">海南</option><option value="130000">河北</option><option value="230000">黑龙江</option><option value="410000">河南</option><option value="420000">湖北</option><option value="430000">湖南</option><option value="320000">江苏</option><option value="360000">江西</option><option value="220000">吉林</option><option value="210000">辽宁</option><option value="150000">内蒙古</option><option value="640000">宁夏</option><option value="630000">青海</option><option value="370000">山东</option><option value="310000">上海</option><option value="140000">山西</option><option value="610000">陕西</option><option value="510000">四川</option><option value="120000">天津</option><option value="650000">新疆</option><option value="540000">西藏</option><option value="530000">云南</option><option value="330000">浙江</option></select>
			<select name="city" id="city" style="color: rgb(51, 51, 51);"><option value="0">城市</option><option value="440100">广州</option><option value="440200">韶关</option><option value="440300" selected="selected">深圳</option><option value="440400">珠海</option><option value="440500">汕头</option><option value="440600">佛山</option><option value="440700">江门</option><option value="440800">湛江</option><option value="440900">茂名</option><option value="441200">肇庆</option><option value="441300">惠州</option><option value="441400">梅州</option><option value="441500">汕尾</option><option value="441600">河源</option><option value="441700">阳江</option><option value="441800">清远</option><option value="441900">东莞</option><option value="442000">中山</option><option value="445100">潮州</option><option value="445200">揭阳</option><option value="445300">云浮</option></select>
			<select name="district" id="district" style="color: rgb(51, 51, 51);"><option value="0">区县</option><option value="440303">罗湖</option><option value="440304">福田</option><option value="440305" selected="selected">南山</option><option value="440306">宝安</option><option value="440307">龙岗</option><option value="440308">盐田</option><option value="440307">坪山新区 (龙岗区)</option><option value="440307">大鹏新区 (龙岗区)</option><option value="440306">光明新区 (宝安区)</option><option value="440306">龙华新区 (宝安区)</option></select>
			<div class="content-address-detail">
				<label>详细地址</label>
				<p>
					<input id="address" name="address" type="text">
				</p>
			</div>
		</div>
		<div class="content-paymode" style="display:none">
		</div>
		<div style="display:none;">
			<input type="hidden" id="preProvince" value="广东">
			<input type="hidden" id="preCity" value="深圳">
			<input type="hidden" id="preDistrict" value="南山区">
			<input type="hidden" id="province_str" name="province_str" value="">
			<input type="hidden" id="city_str" name="city_str" value="">
			<input type="hidden" id="district_str" name="district_str" value="">
			<input type="hidden" id="aid" name="aid" value="3049">
			<input type="hidden" id="pid" name="pid" value="81">
			<input type="hidden" id="cid" name="cid" value="2">
			<input type="hidden" id="tid" name="tid" value="95">
			<input type="hidden" id="paymode" name="paymode" value="">
			<input type="hidden" id="proid"  value="<?php echo $_GET['id'];?>">
			<input type="hidden" id="price"  value="<?php echo $_GET['price'];?>">
			<input type="hidden" id="openid"  value="<?php echo $openid;?>">
			<input type="hidden" id="location"  value="<?php echo $_SERVER['HTTP_HOST'];?>">
		</div>
		<a id="btn-buy" class="btn-buy" href="javascript:void(0)">在线支付（送币）</a>
		<a id="btn-buys" class="btn-online" href="javascript:void(0)" style="display:none">货到付款</a>
	</div>
	<script type="text/javascript">
	$(function(){
		$('.btn-buy').click(function(){
			if($('.btn-buy').hasClass('img')){
				return false;
			}
			var name = $("#name").val();
			var mobile = $("#mobile").val();
			var province = $("#province").val();
			var city = $("#city").val();
			var district = $("#district").val();
			var proid = $("#proid").val();
			var price = $("#price").val();
			var openid = $("#openid").val();
			var address = $("#address").val();
			var province_str = $("#province").find("option:selected").text();
			var city_str = $("#city").find("option:selected").text();
			if(district != '0'){
				var district_str = $("#district").find("option:selected").text();
			}
			$("#province_str").val(province_str);
			$("#city_str").val(city_str);
			$("#district_str").val(district_str);
			if(name == ''){
				alert('请正确填写收货人姓名');
				return false;
			}
			if(mobile == ''){
				alert('请正确填写联系手机');
				return false;
			}
			var re = /^1[3|4|5|8|7][0-9]\d{8}$/;
			if(mobile.length != 11||!re.test(mobile)){
				alert('请填写有效的手机号码');
				return false;
			}
			if(province == '0' || city == '0' || district == '0'){
				alert('请正确填写收货城市');
				return false;
			}
			if(address == ''){
				alert('请正确填写收货地址');
				return false;
			}
			$('.btn-buy').addClass('img');
			if (typeof WeixinJSBridge == "undefined"){
				if( document.addEventListener ){
					document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
				}else if (document.attachEvent){
					document.attachEvent('WeixinJSBridgeReady', jsApiCall);
					document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
				}
			}else{
				var jsApiParameters =  null;
				$('.btn-buy').html('购买中...');
				$.ajax({
					type: 'POST',
					url: 'shopping_jsApiParameters.php?action=weixin_pay',
					data: {"money":price,"openid":openid},
					dataType: 'text',
					timeout: 3000,
					async:false,
					success: function(data){
						if(0 != data){
							jsApiParameters = data;
						}

						$('.btn-buy').removeClass('img');
						$('.btn-buy').html('在线购买');
					},
					error: function(xhr, type){
						alert('支付异常，请重新支付');
						$('.btn-buy').html('在线购买');
						$('.btn-buy').removeClass('img');
					}
				});
				if(jsApiParameters != null){
					jsApiCall(jsApiParameters);
				}
			}
		});
		function jsApiCall(jsApiParameters)
		{
			var jsPs = eval('(' + jsApiParameters + ')');
			var proid = $("#proid").val();
			var name = $("#name").val();
			var mobile = $("#mobile").val();
			var province = $("#province").val();
			var city = $("#city").val();
			var district = $("#district").val();
			var address = $("#address").val();
			var uopenid = $('#openid').val();
			var location = $('#location').val();
			var sprice = $('#price').val();
			var province_str = $("#province").find("option:selected").text();
			var city_str = $("#city").find("option:selected").text();
			if(district != '0'){
				var district_str = $("#district").find("option:selected").text();
			}
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				jsPs ,
				function(res){
					if(res.err_msg == "get_brand_wcpay_request:ok" ){
						$.ajax({
							type: 'POST',
							url: 'shopping_jsApiParameters.php?action=weixin_update',
							data: {"appid":jsPs.appId,"prepay_id":jsPs.package,"openid":$('#openid').val(),
								"price":$('#price').val(),"proid":proid,
								"address":address,"province":province_str,"city":city_str,"district_str":district_str,
								"name":name,"phone":mobile},
							dataType: 'text',
							async:false,
							success: function(data){
								alert('支付成功');								
								document.location.href="shopping_order.php?openid="+uopenid+"&id="+proid+'&sprice='+sprice;
							},
							error: function(xhr, type){
								alert('支付异常，请重新充值');
							}
						});
					}else{
						alert('支付失败');
					}
				}
			);
		}




		//货到付款
		$('.btn-online').click(function(){
			if($('.btn-online').hasClass('img')){
				return false;
			}
			var proid = $("#proid").val();
			var name = $("#name").val();
			var mobile = $("#mobile").val();
			var province = $("#province").val();
			var city = $("#city").val();
			var district = $("#district").val();
			var address = $("#address").val();
			var uopenid = $('#openid').val();
			var province_str = $("#province").find("option:selected").text();
			var city_str = $("#city").find("option:selected").text();
			if(district != '0'){
				var district_str = $("#district").find("option:selected").text();
			}
			$("#province_str").val(province_str);
			$("#city_str").val(city_str);
			$("#district_str").val(district_str);
			if(name == ''){
				alert('请正确填写收货人姓名');
				return false;
			}
			if(mobile == ''){
				alert('请正确填写联系手机');
				return false;
			}
			var re = /^1[3|4|5|8|7][0-9]\d{8}$/;
			if(mobile.length != 11||!re.test(mobile)){
				alert('请填写有效的手机号码');
				return false;
			}
			if(province == '0' || city == '0' || district == '0'){
				alert('请正确填写收货城市');
				return false;
			}
			if(address == ''){
				alert('请正确填写收货地址');
				return false;
			}
			$('.btn-online').addClass('img');
			$('.btn-online').html('购买中...');
			$.post("shopping_ajax.php?action=address",{"proid":proid,"address":address,"province":province_str,"city":city_str,"district_str":district_str,"mobile":mobile,"name":name},function(data){
				if(data == 1){
					alert('购买成功');
					document.location.href="shopping_order.php?openid="+uopenid+"&id="+proid+"&sprice=20";
				}else{
					alert('购买失败');
				}
				$('.btn-online').html('货到付款');
				$('.btn-online').removeClass('img');
			},'json');
		});
	})
	</script>


</body></html>