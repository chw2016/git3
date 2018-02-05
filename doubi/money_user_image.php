<?php
session_start();
include "mysqldbread.php";
require "jssdk.php";
$jssdk = new JSSDK($APP_ID,$APP_SECRET);
$signPackage = $jssdk->GetSignPackage();
$codes = $_GET['code'];
$access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$APP_ID&secret=$APP_SECRET&code=$codes&grant_type=authorization_code";
$return_results = file_get_contents($access_token_url);
$return_json =json_decode($return_results);
$access_token = $return_json->access_token;
$openid = $return_json->openid;
$get_openid = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
$edopenid = file_get_contents($get_openid);
$head = json_decode($edopenid);
$nickname = $head->nickname;
$sex = $head->sex;
$language = $head->language;
$city = $head->city;
$province = $head->province;
$country = $head->country;
$headimgurl = $head->headimgurl;
if(!empty($openid)){
    $_SESSION['nickname']  = $nickname;
    $_SESSION['users_openid']  = $openid;
    $_SESSION['sex']        =$sex;
    $_SESSION['language']  = $language;
    $_SESSION['city']     =$city;
    $_SESSION['province'] = $province;
    $_SESSION['country']  =$country;
    $_SESSION['headimgurls']=$headimgurl;
}else{
    $openid = $_SESSION['users_openid'];
    $headimgurl = $_SESSION['headimgurls'];
}
$value = $gj_url.'/money_index.php?user_openid='.$openid;
?>
<html><head>
    <title>用户二维码</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum- scale=1.0, maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="Cache-Control" content="max-age=0">
    <meta name="apple-touch-fullscreen" content="yes">
    <link rel="stylesheet" type="text/css" href="./money_css/frozen.css">
    <link rel="stylesheet" type="text/css" href="./money_css/mobi.css">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <style>
    .code-mains{
        width: 70px;
        height: 70px;
        border-radius: 50%;
        overflow: hidden;
        float: left;
    }
        .buttons{
            width: 60%;
            height: 40px;
            border: 1px solid #ea7215;
            border-radius: 5px;
            margin: 0 auto;
            text-align: center;
            margin-bottom: 20px;
            background-size: 3px 3px;
            background: #D82741;
            margin-top: 20px;
            background: -webkit-gradient(linear, left top, left bottom, from(#E84B6E), to(#E20C2C) );
        }
        .center{
            color: #fff;
            line-height: 40px;
        }
        .account-fixed{
            height: 64px;
            position: relative;
        }
        .top1{
            width: 100%;
            height: 20px;
            font-size: 18px;
        }
        .top2{
             width: 100%;
             height: 20px;
             font-size: 14px;
        }
        #conter{
            padding: 15px;
        }
        .status{
            padding: 15px;
        }
        #detail{
            width: 100%;
            height: 30px;
            text-align: center;
            margin-top: 12px;
        }
        .col{
            color: #eb1212;
        }
    .huo{
        color: #eb1212;
    }
        .loading{
            width: 84%;
            height: 90px;
            background: #fff;
            margin: 0 auto;
            padding-top: 16px;
            padding-left: 20px;
            margin-top: 6px;
            padding-right: 20px;
        }
        .fadeInRight{
            float: right;
            width: 68%;
            height: 70px;
            line-height: 70px;
            border-bottom: 1px solid #ccc;
            font-weight: bold;
        }
    </style>
</head>
<body>
<header>
    <div class="account-fixed">
        <div class="top1">推荐商家享逗币</div>
        <div class="top2">(逗币礼包送送送)</div>
    </div>
</header>
<section>
    <p style="padding: 15px;"><b>推荐商家扫描加入广加平台免费推广栏目：</b><b class="col">凡商家在成功推广后，获得666个逗币！</b>
        成功推荐1户，送666个逗币；成功推荐2户商家，即可获得1332个逗币，以此累加！
    </p>
    <div id="conter" style="display: none;">
        <b >活动细则：</b><br/>
        ①被推荐商家资格：未在广加平台参与任何推广的商家，经审核后即可参与推广。<br/>
        ②成功推荐一户的奖励将在商家成功参与推广后的24小时内由广加充值到逗币免费中心，可自行查询赠送记录。<br/>
        ③若赠送逗币没有正常到账，推荐人可24小时后致电广加客服或扫描二维码加微信反馈给我司，将给予及时处理。<br/>
        ④推荐人应与商家或企业确认参与意愿，如出现推荐的不是商家或企业，恶意推荐等欺诈、舞弊情况，我司将取消推荐人参加此次活动及兑换逗币的资格，并保留采取其他相应法律措施的权利。
    </div>
    <div id="detail">
        详情展开^
    </div>
</section>
<div class="loading">
    <div class="code-mains">
        <img src="<?php echo $headimgurl;?>">
    </div>
    <div class="fadeInRight">
        即刻参与推广，逗币礼包送不停
    </div>
</div>
<div class="status">
    使用说明：<br/>
    ①点击开始推荐分享二维码<br/>
    ②分享商家扫码注册参与免费推广<br/>
    ③商家推广成功后获得666个逗币。<br/>
<br/>
    例：推荐二维码分享给贡茶店老板，供老板注册参与免费推广，推广成功即可获得666个逗币，进入逗币中心可查询赠送记录。
</div>
<div class="buttons" onclick="window.location.href='<?php echo $gj_url;?>/money_user_center.php'">
    <button type="button" class="center">开始推荐</button>
</div>
</body>
<script src='./money_js/jsweixin1.0.js'></script>
<script>
    document.getElementById("detail").onclick = function(){
        var conter=document.getElementById("conter");
        if(conter.style.display=='none'){
            conter.style.display='block';
        }else{
            conter.style.display='none';
        }
    }
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage'
        ]
    });
    wx.ready(function () {
        wx.onMenuShareTimeline({
            title: '广加推广',
            link: '<?php echo $value; ?>',
            imgUrl: 'http://www.guangjia3.com/guangjia3/logo.ico',
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //分享到朋友
        wx.onMenuShareAppMessage({
            title: '广加推广',
            desc: '广加推广，注册成为商家',
            link: '<?php echo $value; ?>',
            imgUrl: 'http://www.guangjia3.com/guangjia3/logo.ico',
            type: '',
            dataUrl: '',
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    })
   var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>