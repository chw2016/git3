<?php
include "mysqldbread.php";
require "jssdk.php";
$jssdk = new JSSDK($APP_ID,$APP_SECRET);
$signPackage = $jssdk->GetSignPackage();
$openid = $_REQUEST['openid'];
$merchant_id = $db->get_row("select * from card_merchant where openid='$openid'");
$merchant_id = object_array($merchant_id);
if(empty($merchant_id)){
    header('Location:'.$gj_url.'/money_index.php');
}
?>
<html><head>
    <title>添加优惠信息</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./money_css/scrollDate.min.css">
    <link rel="stylesheet" href="./money_css/frozen.css?v=201606234">
    <link rel="stylesheet" href="./money_css/mobi.css?v=20160623">
    <link rel="stylesheet" href="./money_css/scrollbar.css" />
    <link rel="stylesheet" href="./money_css/pay.css?v=20160624" />
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
            color: #00938d;
        }
        .icon-chevron-thin-left.thin-left {
            color: #818181;
            font-size: 16px;
            vertical-align: text-bottom;
        }
		.ucenter-menu li.active>i.icon-invite {
    background-position: -235px -180px;
}
    </style>
</head>
<body>
<div class="return-top">
    <span class="icon-chevron-thin-left thin-left"></span>
    <span class="return-text">添加优惠信息</span>
</div>
<div class="space-20"></div>
<header class="ucenter-t" style="margin-top: 30px;">
    添加优惠信息</header>
<section class="ucenter-main animated fadeInDown" style="margin-bottom: 3px;">
    <div class="space-10"></div>
    <ul class="um-list um-list-form">
        <li>
            <label for="cuser_name" class="label">卡券名</label><input class="title" type="text" maxlength="9" placeholder="例如:肯德基名称">
        </li>
        <li><label for="cuser_name" class="label">商户名称</label><input type="text" class="brand_name" placeholder="例如:店名称"></li>
        <li>
            <label for="cuser_name" class="label">券名</label><input type="text" class="sub_title" placeholder="例如:小名称">
        </li>
        <li>
            <label for="cuser_name" class="label">单次领取数</label><input type="text" placeholder="用户领取逗币数量"  class="per_num" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
        </li>
        <li>
            <label for="cuser_name" class="label">领取次数</label><input type="text" placeholder="领取次数" class="quantity" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
        </li>
        <li>
            <label for="cuser_name" class="label">逗币总量</label><input type="text" placeholder="总的数量" class="total_doubi" readonly="readonly">
        </li>
        <li>
            <label for="cuser_name" class="label" style="width: 22%;">开始时间</label> <input type="text" placeholder="开始时间" id="start_time" class="date">
        </li>
        <li>
            <label for="cuser_name" class="label" style="width: 22%;">结束时间</label> <input type="text" placeholder="结束时间" id="end_time" class="datetime">
        </li>
    </ul>
</section>
<button class="ui-btn-lg ui-btn-danger look_preview" type="button" data-img="./imgs/preview.jpg" style="font-size: 12px;margin-top:10px;margin: auto;width: 25%;    line-height: 36px;height: 36px;">查看示例</button>
<header class="ucenter-t" style="margin-top: 30px;">
    描述</header>
<section class="ucenter-main animated fadeInDown">
    <div class="space-10"></div>
    <ul class="um-list um-list-form" style="    height: 100px;">
        <li><textarea class="description" rows="6" cols="35" placeholder=""></textarea></li>
    </ul>
</section>
<input type="hidden" class="openid" value="<?php echo $_GET['openid'];?>">
<aside class="account-submit">
    <button class="ui-btn-lg ui-btn-danger save_update" type="button" >保存</button>
    <div class="space-10"></div>
    <button class="ui-btn-lg ui-btn-danger" type="button" onclick="location.href='money_volumelist.php?openid=<?php echo $_GET['openid'];?>'">优惠列表</button>
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
    })
    Zepto(function($){
        var preview = $('.look_preview');
        preview.on('tap',function(){
            var img=$(this).attr('data-img');
            $.dialog({
                content:'<img src="'+img+'" alt="" width="280" />',
                button:['ok']
            });
        });
        $('.quantity ,.per_num').blur(function(){
            var per_num = $('.per_num').val();
            var quantity = $('.quantity').val();
            if(per_num && quantity){
                $('.total_doubi').val(quantity*per_num);
            }
        });
        $('.save_update').tap(function(){
            var brand_name = $('.brand_name').val();
            var title = $('.title').val();
            var sub_title = $('.sub_title').val();
            var per_num = $('.per_num').val();//
            var quantity = $('.quantity').val();
            var openid =$('.openid').val();
            var end_date = $('.datetime').val();
            var start_time = $('#start_time').val();
            var description = $('.description').val();
            var myReg = /^[\u4e00-\u9fa5]+$/;
            if(!brand_name){
                $.dialog({
                    content: '请输入商户名称',
                    button: ['ok']
                });
                return false;
            }
            if(!title){
                $.dialog({
                    content: '请输入卡券名',
                    button: ['ok']
                });
                return false;
            }
            if(!sub_title){
                $.dialog({
                    content: '请输入券名',
                    button: ['ok']
                });
                return false;
            }
            if(!quantity){
                $.dialog({
                    content: '请输入数量',
                    button: ['ok']
                });
                return false;
            }
            if(myReg.test(per_num)){
                $.dialog({
                    content: '请输入数字',
                    button: ['ok']
                });
                return false;
            }
            if(myReg.test(quantity)){
                $.dialog({
                    content: '请输入数字',
                    button: ['ok']
                });
                return false;
            }
            if(!start_time){
                $.dialog({
                    content: '请选择开始时间',
                    button: ['ok']
                });
                return false;
            }
            if(!end_date){
                $.dialog({
                    content: '请选择结束时间',
                    button: ['ok']
                });
                return false;
            }
            var el=$.loading({
                content:'拼命加载中...'
            });
            var DATA = {
                brand_name:brand_name,
                title:title,
                sub_title:sub_title,
                quantity:quantity,
                begin_timestamp:start_time,
                end_date:end_date,
                per_num:per_num,
                description:description,
                openid:openid
            };
            $.post("money_query_device_group.php?action=config",DATA,function(reg){
                if (reg.msg == 1) {
                    var DG = $.dialog({
                        content: '配置成功',
                        button: ['ok']
                    });
                    DG.on('dialog:action',function(e){
                        document.location.href="money_volumelist.php?openid=<?php echo $_REQUEST['openid'];?>";
                    });
                }else if (reg.msg == 3){
                    $.dialog({
                        content: '你已经配置卡券名了',
                        button: ['ok']
                    });
                }else{
                    $.dialog({
                        content: '配置失败',
                        button: ['ok']
                    });
                }
                el.hide();
            },'json');
        })
    });
   var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>