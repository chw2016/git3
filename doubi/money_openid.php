<?php
session_start();
include "mysqldbread.php";
$merchant_id = $_GET['merchant_id'];
$money = $_GET['money'];
$doubi_type = $_GET['doubi_type'];
$time = $_GET['time'];
$guid = $_GET['guid'];
$_SESSION['money'] = $money;
$_SESSION['doubi_type'] = $doubi_type;
$_SESSION['time'] = $time;
$_SESSION['guid'] = $guid;
$REDIRECT_URI= $gj_url.'/money_get.php?merchant_id='.$merchant_id;
$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$APP_ID&redirect_uri=$REDIRECT_URI&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
header("location:$url");
?>
