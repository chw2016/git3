<?php
    include "mysqldbread.php";
    $openid = $_REQUEST['openid'];
    require "jssdk.php";
    $jssdk = new JSSDK($APP_ID,$APP_SECRET);
    $signPackage = $jssdk->GetSignPackage();
    $merchant_ids = $db->get_row("select * from card_merchant where openid='$openid'");
$merchant_ids = object_array($merchant_ids);
    if(empty($merchant_ids)){
        header('Location:'.$gj_url.'/money_index.php');
    }
    $merchant_id = $_GET['merchant_id'];//二维码内容
    $value = $gj_url.'/money_openid.php?merchant_id='.$merchant_id.'&money=1';
    if($_POST){
        $money = trim($_POST['money']);
        $doubi_type = trim($_POST['doubi_type']);
        $time = date('Y-m-d H:i:s',time());
        $value =$gj_url.'/money_openid.php?merchant_id='.$merchant_id.'&money='.$money."&doubi_type=".$doubi_type."&time=".$time."&guid=".guid();
    }
?>
<html><head>
    <title>二维码</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="./money_css/frozen.css">
    <link rel="stylesheet" type="text/css" href="./money_css/mobi.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum- scale=1.0, maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="Cache-Control" content="max-age=0">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <style>
        .code-conter{
            font-size: 12px;
            height: 160px;
            background: url("./img/code_02.png") no-repeat;
            background-size: 100%;
            width: 100%;
            position: inherit;
            bottom: 0;
        }
        .doubi_type{
            width: 100%;
            height: 50px;
            text-align: center;
            margin: 0 auto;
            line-height: 50px;
            padding-left: 38%;
            margin-bottom: 10px;
            border: 1px solid #e6e6e6;
            border-radius: 5px;
        }
		#num{
			background:#00938d;
		}
    </style>
</head>
<body>
    <div class="code-header">
            <div><?php echo $merchant_ids['name'];?>商家的专属二维码</div>
            <div class="code-bottom">让用户扫一扫进行核销</div>
    </div>
    <form action="" method="post" id="submit">
        <div class="contentre">
            <input class="money-inputs" name="money" value="<?php echo $money;?>" placeholder="请输入送币的数量"  onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))"/>
        </div>
        <div class="space-10">
        </div>
        <aside class="account-submit">
            <select class="doubi_type" name="doubi_type">
                <option value="1" <?php if(isset($doubi_type) && $doubi_type==1){echo "selected='selected'";}?>>优惠券核销</option>
                <option value="3" <?php if(isset($doubi_type) && $doubi_type==3){echo "selected='selected'";}?>>定金核销</option>
                <option value="2" <?php if(isset($doubi_type) && $doubi_type==2){echo "selected='selected'";}?>>送逗币（多次）</option>
                <option value="4" <?php if(isset($doubi_type) && $doubi_type==4){echo "selected='selected'";}?>>远程送逗币</option>
            </select>
            <button class="ui-btn-dangers" type="submit" id="num">生成二维码</button>
            <br/>
        </aside>
		<div class="code-main">
        </div>
    </form>
    <div class="code-conter">
        <div class="code-size" style="    padding-top: 30px;">
                   关注大法：扫一扫、长按二维码识别、让用户扫一扫进行核销，请用手机截图保存
        </div>
    </div>
</body>
<input type="hidden" value="<?php echo $value;?>" class="qrcode">
<script type="text/javascript" src="./money_js/zepto.js"></script>
<script type="text/javascript" src="./money_js/frozen.js"></script>
<script src='./money_js/jquery-1.9.1.min.js'></script>
<script type="text/javascript" src="./money_js/jquery.qrcode.min.js"></script>
<script src='./money_js/jsweixin1.0.js'></script>
<script>
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
    $(function(){
        $(".code-main").qrcode({
            width: 200, //宽度
            height:200, //高度
            foreground:'rgb(51, 51, 51)',
            text: $('.qrcode').val()
        });
    });
        $('#num').click(function(){
            var money  = $('.money-inputs').val();
            var merchant_id  = "<?php echo $merchant_id;?>";
            var myReg = /^[\u4e00-\u9fa5]+$/;
            if (!money) {
                alert('请输入逗币数量');
                return false;
            }
            if(myReg.test(money)){
                alert('请输入数字');
                return false;
            }
            $('#submit').submit();
        });
    var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>