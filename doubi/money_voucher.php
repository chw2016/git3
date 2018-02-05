<?php
    session_start();
    require_once "mysqldbread.php";
    if(!isset($_SESSION['openId']) && !isset($_GET['openid'])){
        exit();
    }
    if(isset($_SESSION['openId']) && !isset($_GET['openid'])){
		$openid = $_SESSION['openId'];
	}
	if(isset($_GET['openid']) && !isset($_SESSION['openId'])){
		$openid = $_GET['openid'];
	}
    $sql = "SELECT 	cc.* FROM	card_config cc,card_doubi cd WHERE cc.del_flag=0 and cd.del_flag=0 and cd.user_type=1 and cd.user_id='$openid' AND
      cd.card_config_id=cc.id and cc.card_status=1";
    $cardConfigDiscoverysResults = $db->get_results($sql);
$cardConfigDiscoverysResults = object_array($cardConfigDiscoverysResults);
?>
<!DOCTYPE html>
<html lang="ch" manifest="">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>安全扫码-广加传媒</title>
    <meta name="Keywords" content="安全扫码-广加传媒" />
    <meta name="Description" content="安全扫码-广加传媒" />
    <meta name="apple-touch-fullscreen" content="YES">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="pragram" content="no-cache">
    <meta name="viewport" content="width=640, user-scalable=no, target-densitydpi=device-dpi">
    <style>
        html {height: 100%;-webkit-tap-highlight-color:rgba(0,0,0,0); -webkit-tap-highlight:rgba(0,0,0,0);-webkit-text-size-adjust:none;overflow:-moz-scrollbars-vertical;}
        body {font-family:"Microsoft Yahei"; width: 640px; max-width: 640px; min-width: 640px; height: 100%; margin: 0 auto; background: #EEEEEE;}
        header{margin:0 auto;   height: 168px;}
        header .top{float:left; height:168px;font-size:32px; text-align:center;}
        header .t1{background:#ff8b00;width:60%;}
        header .t2{background:#119fac;width:40%;}
        header .top a{display:block; width:100%;height:100%;color:#fff;font-size:26px;}
        header .top span{display:block;padding:35px 0 0; text-align:center;}
        .top span img{width:55px;}
        img{border:none;}
        a{text-decoration:none;}
        .menu{background:#119fac;width:100%; height:92px; }
        .content{background:#eee;padding-top: 50px;}
        .content ul{ clear:both;margin:0;padding:0;overflow:hidden;}
        .content li{float:left;background:#d8d8d8;width:149px;height:62px;line-height:62px;margin:0 8px 0 0; display:inline-block;border-radius:25px 25px 0 0;font-size:28px; color:#666;text-align:center;}
        .content li.on{background:#0fadbb;color:#fff;}
        .box{background:#fff;}
        .list{width:100%;height:90px;margin:0 0 18px;border-bottom:1px #ddd solid;    padding: 16px 0;}
        .list .btt{width:440px;float:left;font-size:18px; color:#444;margin-left:5%;}
        .list .btt p{margin:0;display:block; padding:5px 0 0 0;}
        .list .btt b{font-size:34px; color:#0fadbb;font-weight:100;}
        .list .btt span{font-size:30px;height:24px;}
        .list .lingquan{width:110px;float:left;margin-top: 5px;margin-left: 8px;}
        .list .lingquan span{display:block;height:22px;text-align:right;font-size:20px;}
        .list .lingquan a{float:right;width:102px;height:40px;line-height:40px; background:#0fadbb;margin:5px 0 0 0;border-radius:8px;color:#fff;font-size:26px;text-align:center;}
        .list .lingquan .lq{float:right;width:102px;height:40px;line-height:40px; background:#0fadbb;margin:5px 0 0 0;border-radius:5px;color:#fff;font-size:26px;text-align:center;}
        .boxnone{display:none;}
        .icon-star-full{
            font-size: 18px;
            color: #F39A20;
        }
        .ad{
            width: 100%;
            height: 100px;
            text-align: center;
            line-height: 100px;
            color: #000;
            font-size: 30px;
        }
        .title{
            background-color: #ffffff;
            height: 60px;
            line-height: 60px;
            padding: 0 10px;
            position: fixed;
            width: 100%;
            font-size: 24px;
            margin-left: 5px;
            color: #d1d1d1;
            border-bottom: 2px solid #e6e6e6;
            margin-bottom: 10px;
        }
        .menu a img{
            width: 52px;
            height: 41px;
            margin-right: 10px;
            padding-top: 8px;
        }
        .scode{
            width: 330px;
            margin-left: 29%;
            font-size: 28px;
            color: #fff;
            height: 42px;
        }
        .menu .biao{
            width: 100%;
            height: 30px;
            text-align: center;
            display: block;
            font-size: 20px;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="title" onclick="location.href='<?php echo $gj_url;?>/play.php'">返回</div>
<section>
    <div class="content">
            <div class="box" id="menu_con_0" >
                <?php foreach($cardConfigDiscoverysResults as $cc ){?>
                    <div class="list">
                        <div class="btt">
                            <b><?php echo $cc['title'];?></b>（<?php echo $cc['sub_title'];?>）
                            <p><span><?php echo $cc['brand_name'];?></span><?php echo $cc['description'];?></p>
                        </div>
                        <div class="xing">
                            <?php if($cc['star_level'] == 1){?>
                                <span class="icon-star-full on"></span>
                            <?php }?>
                            <?php if($cc['star_level'] == 2){?>
                                <?php for($i=1;$i<=2;$i++){ ?>
                                    <span class="icon-star-full on"></span>
                                <?php }?>
                            <?php }?>
                            <?php if($cc['star_level'] == 3){?>
                                <?php for($i=1;$i<=3;$i++){ ?>
                                    <span class="icon-star-full on"></span>
                                <?php }?>
                            <?php }?>
                            <?php if($cc['star_level'] == 4){?>
                                <?php for($i=1;$i<=4;$i++){?>
                                    <span class="icon-star-full on"></span>
                                <?php }?>
                            <?php }?>
                            <?php if($cc['star_level'] == 5){?>
                                <?php for($i=1;$i<=5;$i++){ ?>
                                    <span class="icon-star-full on"></span>
                                <?php }?>
                            <?php }?>
                        </div>
                        <div class="lingquan">
                            <!--<span><?php /*echo $cc->star_level;*/?></span>-->
							<span >
									已领<?php echo ($cc['quantity'] - $cc['surplus_quantity']);?>次
							</span>
                            <div class="lq">领券</div>
                        </div>
                    </div>
                <?php } ?>
            </div>
    </div>
</section>

</body>
</html>