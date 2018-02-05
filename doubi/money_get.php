<?php
session_start();
include "mysqldbread.php";
$code = $_GET['code'];
$merchant_id = $_GET['merchant_id'];//商家id
$money = $_SESSION['money'];
$doubi_type = $_SESSION['doubi_type'];
$time = $_SESSION['time'];
$guid = $_SESSION['guid'];
$now_time = date('Y-m-d H:i:s',time());
$ntime = $db->get_row("SELECT TIMESTAMPDIFF(MINUTE,'$time','$now_time') as times");
$access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$APP_ID&secret=$APP_SECRET&code=$code&grant_type=authorization_code";
$return_results = file_get_contents($access_token_url);
$return_json =json_decode($return_results);
$access_token = $return_json->access_token;
$openid = $return_json->openid;//用户id

$openids = $db->get_row("select * from card_merchant where id='$merchant_id' and status=1 and del_flag=0");
$openids = object_array($openids);
//查询商家的余额
$totals = $openids['total_doubi'];
$all = $totals;
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>核销</title>
    <link rel="stylesheet" href="./money_css/scrollbar.css" />
    <link rel="stylesheet" href="./money_css/pay.css" />
    <link rel="stylesheet" href="./money_css/frozen.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/frozen1.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/table.css" />
    <link rel="stylesheet" href="./money_css/common.css" />
</head>
<body ontouchstart>
<div id="header" style="background: #39f;">
    <a href="">核销</a>
</div>

<div class="content">
    <section class="ui-container">
        <div class="space-10" style="margin-top: 210px;"></div>
        <div class="ui-btn-wrap">
            <button class="ui-btn-lgs ui-btn-primary btn-all-update">
                点击确定？
            </button>
        </div>
    </section>
</div>
</body>
<input type="hidden" class="last" value="<?php echo $all;?>">
<input type="hidden" class="guid" value="<?php echo $guid;?>">
<input type="hidden" class="time" value="<?php echo $ntime->times;?>">
<script type="text/javascript" src="./money_js/zepto.js"></script>
<script type="text/javascript" src="./money_js/frozen.js"></script>
<script type="text/javascript" src="./money_js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="./money_js/jsweixin1.0.js"></script>
<script type="text/javascript">
    Zepto(function($) {

        $('.btn-all-update').tap(function(){
            var last = $('.last').val();
            var merchant_id = "<?php echo $merchant_id;?>";
            var user_id = "<?php echo $openid;?>";
            var money = "<?php echo $money;?>";
            var guid = "<?php echo $guid;?>";
            var doubi_type = "<?php echo $doubi_type;?>";
            var times = "<?php echo $ntime->times;?>";
            if(parseInt(last)<parseInt(money)){
                $.dialog({
                    content: '商家余额不足，请商家充值',
                    button: ['ok']
                });
                return false;
            }
            if(times>=2){
                $.dialog({
                    content: '二维码已失效，请让商家刷新二维码',
                    button: ['ok']
                });
                return false;
            }
            var el = $.loading({
                content: '核销中'
            });
            $.post("money_query_device_group.php?action=cancel",{merchant_id:merchant_id,guid:guid,user_id:user_id,money:money,doubi_type:doubi_type}, function (reg) {
                if (reg.msg == 1) {
                    var DG = $.dialog({
                        content: '核销成功',
                        button: ['ok']
                    });
                    DG.on('dialog:action',function(e){
                        document.location.href="money_evidence_record.php?openid="+user_id;
                    });
                }else if (reg.msg == 3){
                    var ed = $.dialog({
                        content: '你已经核销过了',
                        button: ['ok']
                    });
                    ed.on('dialog:action',function(e){
                        WeixinJSBridge.call('closeWindow');
                    });
                }else if (reg.msg == 4){
                    var ed = $.dialog({
                        content: '商家余额不足，请商家充值',
                        button: ['ok']
                    });
                    ed.on('dialog:action',function(e){
                        WeixinJSBridge.call('closeWindow');
                    });
                }else if (reg.msg == 5){
                    var ed = $.dialog({
                        content: '你已经领取过了',
                        button: ['ok']
                    });
                    ed.on('dialog:action',function(e){
                        WeixinJSBridge.call('closeWindow');
                    });
                }else{
                    $.dialog({
                        content: '核销失败',
                        button: ['ok']
                    });
                }
                el.hide();
            }, 'json');
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