<?php
    include "mysqldbread.php";
    //用户广告推荐入口
    $REDIRECT_URI= $gj_url.'/money_user_image.php';
    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$APP_ID&redirect_uri=$REDIRECT_URI&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    header("location:$url");
?>