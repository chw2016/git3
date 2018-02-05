<?php
ini_set('date.timezone','Asia/Shanghai');
require_once('mysqldb.php');
$merchant_id = $_GET['merchant_id'];
$merchant_id = $db->get_row("select * from card_merchant where id='$merchant_id' and del_flag=0 and status=1");
if(empty($merchant_id)){
    exit('信息有误');
}
?>
<html><head>
    <title>商家信息详情</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/frozen.css?v=20160623">
    <link rel="stylesheet" href="./css/mobi.css?v=20160623">
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
    <span class="return-text">商家信息详情</span>
</div>
<div class="space-20"></div>
<header class="ucenter-t" style="margin-top: 30px;">
    商家信息详情</header>
<section class="ucenter-main animated fadeInDown" style="margin-bottom: 3px;">
    <div class="space-10"></div>
    <ul class="um-list um-list-form">
        <li>
            <label for="cuser_name" class="label">商家名称</label><input readonly="readonly" class="title" type="text" value="<?php echo $merchant_id->name;?>">
        </li>
        <li>
            <label for="cuser_name" class="label">联系电话</label><input readonly="readonly" type="text" class="sub_title" value="<?php echo $merchant_id->phone;?>">
        </li>
    </ul>
</section>
<header class="ucenter-t" style="margin-top: 30px;">
    地址</header>
<section class="ucenter-main animated fadeInDown">
    <div class="space-10"></div>
    <ul class="um-list um-list-form" style="    height: 100px;">
        <li><textarea class="description" rows="6" cols="35" readonly="readonly"><?php echo $merchant_id->address;?></textarea></li>
    </ul>
</section>
</body>
<script type="text/javascript">
    var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>