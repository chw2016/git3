<?php
	session_start();
    include "mysqldbread.php";
    require "jssdk.php";
    $type = $_GET['type'];
    $groupId = $_GET['groupId'];
    $domainUrl = $_GET['domainUrl'];
    $defaultCode = $_GET['defaultCode'];
    $deviceCommand = $_GET['deviceCommand'];

    $_SESSION['domainUrl']=$domainUrl;
    $_SESSION['current_type']=$type;
    $_SESSION['current_group']=$groupId;
    $_SESSION['default_code']=$defaultCode;
    $_SESSION['device_command']=$deviceCommand;
    $REDIRECT_URI= $gj_url.'/index.php';
    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$APP_ID&redirect_uri=$REDIRECT_URI&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    header("location:$url");
?>