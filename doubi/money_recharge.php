<?php
session_start();
$headimgurl = $_SESSION['headimgurl'];
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>商家充值中心</title>
    <meta charset="utf-8">
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta content="eric.wu" name="author">

    <meta content="telephone=no, address=no" name="format-detection">
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <link rel="stylesheet" type="text/css" href="./money_css/frozen.css?v=20160623">
    <link rel="stylesheet" type="text/css" href="./money_css/mobi.css?v=20160624">
    <link rel="stylesheet" href="./money_css/table.css" />
    <link rel="stylesheet" href="./money_css/common.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/scrollbar.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/pay.css?v=20160624"/>
    <link rel="stylesheet" href="./money_css/frozen1.css?v=20160625" />
	<style>
	.ucenter-menu li.active>i.icon-invite {
    background-position: -235px -180px!important;
}
	</style>
</head>
<body>
<header>
    <h3 class="header-top">商家充值中心</h3>
</header>
<div class="logos">
    <img src="<?php echo $headimgurl;?>">
</div>
<div class="contentre">
    <input class="money-inputs" value="" placeholder="请输入充值的逗币数量"  onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))"/>
</div>
<div class="money" style="margin: 0 auto; text-align: center;margin-top:10px;font-size: 16px;">
    逗币余额：<em class="balance">0</em>
</div>
<div class="money_list"><a href="money_list.php?openid=<?php echo $_REQUEST[openid];?>">收支列表</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if($merchant_id['total_doubi']>0){?>
    <a href="money_shoper_code.php?openid=<?php echo $_REQUEST[openid];?>&merchant_id=<?php echo $merchant_id['id'];?>">我的专属二维码</a>
<?php }?>
</div>
<aside class="account-submit">
    <button class="ui-btn-dangers" type="button" id="pay" onclick="callpay()">确定</button>
    <br/>
</aside>
<input type="hidden" class="openid" value="<?php echo $_REQUEST['openid']?>" id="openId">
<div class="footer">
    <menu class="ucenter-menu">
        <ul>
            <li onclick="location.href='money_merchant.php?openid=<?php echo $_REQUEST['openid']?>'">
                <i class="icon-home"></i><br>商家
            </li>
            <li class="active">
                <i class="icon-profile" style="width:25px;"></i><br>充值
            </li>
            <li onclick="location.href='money_favorable.php?openid=<?php echo $_REQUEST['openid'];?>'">
                <i class="icon-invite" style="width:26px;"></i><br>优惠
            </li>
        </ul>
    </menu>
</div>
</body>
<script type="text/javascript" src="./money_js/zepto.js"></script>
<script type="text/javascript" src="./money_js/frozen.js"></script>
<script type="text/javascript" src="./money_js/jquery-1.9.1.min.js"></script>
<script src='./money_js/jsweixin1.0.js'></script>
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
    Zepto(function($) {
        var reg = {num:/^[1-9][0-9]*$/};
        var openid = $('.openid').val();
        var el=$.loading({
            content:'加载中'
        });
        $.post("money_query_device_group.php?action=all_pay",{openid:openid},function (data){
            if(data.msg == 1){
                    $('.balance').html(data.datas);
            }else{
                $('.balance').html('0');
            }
            el.hide();
        },'json');
        $('#pay').tap(function () {
            var money  = $('.money-inputs').val();
            var myReg = /^[\u4e00-\u9fa5]+$/;
            var reg = /.*\..*/;
            var openid = $('.openid').val();
            var tmoney = money/10;
            if (!money) {
                $.dialog({
                    content: '请输入逗币数量',
                    button: ['ok']
                });
                return false;
            }
            if(myReg.test(money)){
                $.dialog({
                    content: '请输入数字',
                    button: ['ok']
                });
                return false;
            }
            if(money<10){
                $.dialog({
                    content: '数量不能小于10个',
                    button: ['ok']
                });
                return false;
            }
            if(reg.test(tmoney)){
                $.dialog({
                    content: '请输入整数',
                    button: ['ok']
                });
                return false;
            }
            var el=$.loading({
                content:'正在充值'
            });
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else {
                var jsApiParameters = null;
                $.ajax({
                    type: 'POST',
                    url: 'money_jsApiParameters.php?action=weixin_pay',
                    data: {"money": money, "openid": openid},
                    dataType: 'text',
                    timeout: 3000,
                    async: false,
                    success: function (data) {
                        if (0 != data) {
                            jsApiParameters = data;
                        }
                        el.hide();
                    },
                    error: function (xhr, type) {
                        $.dialog({
                            content: '充值异常，请重新充值',
                            button: ['ok']
                        });
                        el.hide();
                    }
                });
                if (jsApiParameters != null) {
                    jsApiCall(jsApiParameters);
                }
            }
        });
    });
    function jsApiCall(jsApiParameters)
    {
        var jsPs = eval('(' + jsApiParameters + ')');
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            jsPs ,
            function(res){
                if(res.err_msg == "get_brand_wcpay_request:ok" ){
                    $.ajax({
                        type: 'POST',
                        url: 'money_jsApiParameters.php?action=weixin_update',
                        data: {"appid":jsPs.appId,"prepay_id":jsPs.package,"openid":$('.openid').val(),"money":$('.money-inputs').val()},
                        dataType: 'text',
                        async:false,
                        success: function(data){
                            $('.balance').html(data);
                        },
                        error: function(xhr, type){
                            alert('充值异常，请重新充值');
                        }
                    });
                }else{
                    alert('充值失败');
                }
            }
        );
    }

   var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>