<?php
session_start();
    require_once "mysqldbread.php";
    $user_openid = $_GET['user_openid'];
$_SESSION['users_openid'] = $user_openid;
    $REDIRECT_URI= $gj_url.'/money_merchant.php';
    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$APP_ID&redirect_uri=$REDIRECT_URI&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
header("location:$url");
?>