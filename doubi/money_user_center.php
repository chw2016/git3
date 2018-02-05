<?php
session_start();
include "mysqldb.php";
require "jssdk.php";
$jssdk = new JSSDK($APP_ID,$APP_SECRET);
$signPackage = $jssdk->GetSignPackage();
$nickname = $_SESSION['nickname'];
$openid = $_SESSION['users_openid'];
$sex = $_SESSION['sex'];
$language = $_SESSION['language'];
$city = $_SESSION['city'];
$province = $_SESSION['province'];
$country = $_SESSION['country'];
$headimgurl = $_SESSION['headimgurls'];
if(empty($openid)){
    header('Location:money_user_openid.php');
    exit();
}
$sql = "select openid from weixin_userinfo where openid='$openid'";
$uid = $db->get_row($sql);
$uid = object_array($uid);
if($uid){
    $value = $gj_url.'/money_index.php?user_openid='.$openid;
}else{
    //商家推广
    $mer_sql = "select openid from card_merchant where openid='$openid' and del_flag=0";
    $mer = $db->get_row($mer_sql);
    $mer = object_array($mer);
    if($mer){
        $datas['id'] = md5(uniqid());
        $datas['wno'] = $WNO;
        $datas['nickname'] = $nickname;
        $datas['sex'] = $sex;
        $datas['openid'] = $openid;
        $datas['language'] = $language;
        $datas['city'] = $city;
        $datas['province'] = $province;
        $datas['country'] = $country;
        $datas['headimgurl'] = $headimgurl;
        $datas['subscribe'] = 1;
        $datas['type'] = '01000000';//商家推广
        $datas['create_time'] = date('Y-m-d H:i:s',time());
        $datas['subscribe_time'] = date('Y-m-d H:i:s',time());
        $datas['subscribe_datetime'] = date('Y-m-d H:i:s',time());
        $id = $db->query("insert into weixin_userinfo set ".$db->get_set($datas));
        $value = $gj_url.'/money_index.php?user_openid='.$openid;
    }else{
        //用户扫描
        $data['id'] = md5(uniqid());
        $data['wno'] = $WNO;
        $data['nickname'] = $nickname;
        $data['sex'] = $sex;
        $data['openid'] = $openid;
        $data['language'] = $language;
        $data['city'] = $city;
        $data['province'] = $province;
        $data['country'] = $country;
        $data['headimgurl'] = $headimgurl;
        $data['subscribe'] = 1;
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['subscribe_time'] = date('Y-m-d H:i:s',time());
        $data['subscribe_datetime'] = date('Y-m-d H:i:s',time());
        $id = $db->query("insert into weixin_userinfo set ".$db->get_set($data));
        $value = $gj_url.'/money_index.php?user_openid='.$openid;
    }
}
?>
<html><head>
    <title>用户二维码</title>
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
        .code-conters {
            font-size: 12px;
            background-size: 100%;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
<div class="code-header">
    <div><?php echo $nickname;?>分享</div>
    <div class="code-bottom">免费推广增客流</div>
</div>
<div class="code-main">

</div>
<div class="code-conters" style="background: #119fac;">
    <div class="code-size">
        免费推广说明：<br/>
        1、扫二维码填写资料注册（填写商家基本信息）<br/>
        2、设置优惠条件（承诺消费送币额度）<br/>
        3、充值购买逗币，生成送币二维码（供顾客消费后扫码领币）<br/>
    </div>
	<p style="text-align: center;color: #fff;font-size: 20px;">如有兴趣参与免费推广或遇到任何问题，可扫码咨询</p>
    <div style="    width: 65%;margin: 0 auto;text-align: center;paddin-bottom:10px">
        <img src="http://7xlcfz.com1.z0.glb.clouddn.com/kefus.png">
    </div>
</div>
</body>
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
    $(function(){
        $(".code-main").qrcode({
            width: 200, //宽度
            height:200, //高度
            foreground:'rgb(51, 51, 51)',
            text: "<?php echo $value; ?>"
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