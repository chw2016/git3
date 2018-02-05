<?php
include "mysqldbread.php";
require "jssdk.php";
$jssdk = new JSSDK($APP_ID,$APP_SECRET);
$signPackage = $jssdk->GetSignPackage();
$openid = $_REQUEST['openid'];
$id = $_REQUEST['id'];
$one = $db->get_row("select * from card_merchant where openid='$openid' and status=1");
$one = object_array($one);
$merchant_id = $db->get_row("select * from card_config where id='$id'and merchant_id='$one[id]' and card_status=1 and del_flag='0' and  surplus_quantity > 0");
$merchant_id = object_array($merchant_id);
if(empty($merchant_id)){
    header('Location:'.$gj_url.'/money_index.php');
}
?>
<html><head>
    <title>优惠信息详情</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./money_css/scrollDate.min.css">
    <link rel="stylesheet" href="./money_css/frozen.css?v=20160623">
    <link rel="stylesheet" href="./money_css/mobi.css?v=20160623">
    <link rel="stylesheet" href="./money_css/scrollbar.css" />
    <link rel="stylesheet" href="./money_css/pay.css?v=20160623" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum- scale=1.0, maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="Cache-Control" content="max-age=0">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <style>
        .status{
            width:164px;
            height:40px;
        }
        .return-top {
            background-color: #ffffff;
            height: 42px;
            line-height: 42px;
            padding: 0 10px;
            position: fixed;
            width:100%;
            border-bottom: 1px solid #e6e6e6;
            z-index: 10;
        }
        .return-text {
            font-size: 15px;
            margin-left: 5px;
            margin-left: 35%;
            color: #EC0E0E;
        }
        .icon-chevron-thin-left.thin-left {
            color: #818181;
            font-size: 16px;
            vertical-align: text-bottom;
        }
    </style>
</head>
<body>
<div class="return-top">
    <span class="icon-chevron-thin-left thin-left"></span>
    <span class="return-text">优惠信息详情</span>
</div>
<div class="space-20"></div>
<header class="ucenter-t" style="margin-top: 30px;">
    优惠信息详情</header>
<section class="ucenter-main animated fadeInDown">
    <div class="space-10"></div>
    <ul class="um-list um-list-form">
        <li>
            <label for="cuser_name" class="label">卡券名</label><input class="title" type="text" maxlength="9" value="<?php echo $merchant_id['title'];?>" readonly="readonly">
        </li>
        <li><label for="cuser_name" class="label">商户名称</label><input type="text" class="brand_name" value="<?php echo $merchant_id['brand_name'];?>" readonly="readonly"></li>
        <li>
            <label for="cuser_name" class="label">券名</label><input type="text" class="sub_title"  value="<?php echo $merchant_id['sub_title'];?>" readonly="readonly">
        </li>
        <li>
            <label for="cuser_name" class="label">单次领取数</label><input type="text"  value="<?php echo $merchant_id['per_num'];?>" readonly="readonly" class="per_num" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
        </li>
        <li>
            <label for="cuser_name" class="label">领取的数量</label><input type="text"  value="<?php echo $merchant_id['quantity'];?>" readonly="readonly" class="quantity" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
        </li>
        <li>
            <label for="cuser_name" class="label">逗币总量</label><input type="text" class="total_doubi" readonly="readonly" readonly="readonly" value="<?php echo $merchant_id['quantity']*$merchant_id['per_num'];?>">
        </li>
        <li>
            <label for="cuser_name" class="label" style="width: 22%;">开始时间</label> <input type="text" placeholder="开始时间" readonly="readonly" class="date" value="<?php echo $merchant_id['begin_timestamp'];?>">
        </li>
        <li>
            <label for="cuser_name" class="label" style="width: 22%;">结束时间</label> <input type="text" placeholder="结束时间" readonly="readonly" class="datetime" value="<?php echo $merchant_id['end_timestamp'];?>">
        </li>
    </ul>
</section>
<header class="ucenter-t" style="margin-top: 30px;">
    描述</header>
<section class="ucenter-main animated fadeInDown">
    <div class="space-10"></div>
    <ul class="um-list um-list-form" style="    height: 100px;">
        <li><textarea class="description" rows="6" cols="35" placeholder="" readonly="readonly"><?php echo $merchant_id['description'];?></textarea></li>
    </ul>
</section>
<input type="hidden" class="openid" value="<?php echo $_GET['openid'];?>">
<aside class="account-submit">
   <!-- <button class="ui-btn-lg ui-btn-danger save_update" type="button" >修改</button>-->
    <div class="space-10"></div>
    <!--<button class="ui-btn-lg ui-btn-danger" type="button" onclick="location.href='volumelist.php?openid=<?php /*echo $_GET['openid'];*/?>'">优惠列表</button>-->
    <div class="space-10"></div>
</aside>
<div class="space-20" style="margin-bottom: 60px;"></div>

<menu class="ucenter-menu">
    <ul>
        <li onclick="location.href='money_merchant.php?openid=<?php echo $_REQUEST['openid']?>'">
            <i class="icon-home"></i><br>商家
        </li>
        <li onclick="location.href='money_recharge.php?openid=<?php echo $_REQUEST['openid'];?>'">
            <i class="icon-profile" style="width:25px;"></i><br>充值
        </li>
        <li class="active">
            <i class="icon-invite" style="width:26px;"></i><br>优惠
        </li>
    </ul>
</menu>

<script type="text/javascript" src="./money_js/zepto.js"></script>
<script type="text/javascript" src="./money_js/frozen.js"></script>
<script src='./money_js/jquery-1.9.1.min.js'></script>
<script src='./money_js/mobiscrollDate.min.js'></script>
<script src='./money_js/date.js'></script>
<script src='./money_js/jsweixin1.0.js'></script>
</body>
<script type="text/javascript">
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'checkJsApi',
            'hideOptionMenu'
        ]
    });
    $(function(){
        wx.ready(function () {
            wx.hideOptionMenu();
        });
    });
    Zepto(function($){
        $('.quantity ,.per_num').blur(function(){
            var per_num = $('.per_num').val();
            var quantity = $('.quantity').val();
            if(per_num && quantity){
                $('.total_doubi').val(quantity*per_num);
            }
        });
    });
    var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>