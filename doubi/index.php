<?php
session_start();
ini_set('date.timezone','Asia/Shanghai');
require_once('mysqldb.php');
require_once('WxPay.JsApiPay.php');
//$tools = new JsApiPay();
//$openId = $tools->GetOpenid();
/*$access_token = $tools->data();
$access_token_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openId&lang=zh_CN";
$return_results = file_get_contents($access_token_url);
$return_json =json_decode($return_results);
$openid_array = object_array($return_json);
$is_openid = $db->get_row("select * from service_userinfo where openid='$openId'");
if($is_openid == false && !empty($openid_array['sex'])){
	$datas['id'] = md5(uniqid());
	$datas['wno'] = $WNO;
	$datas['nickname'] = $openid_array['nickname'];
	$datas['sex'] = $openid_array['sex'];
	$datas['openid'] = $openId;
	$datas['language'] = $openid_array['language'];
	$datas['city'] =$openid_array['city'];
	$datas['province'] = $openid_array['province'];
	$datas['country'] = $openid_array['country'];
	$datas['headimgurl'] = $openid_array['headimgurl'];
	$datas['subscribe'] = 1;
	$datas['type'] = '1';
	$datas['create_time'] = date('Y-m-d H:i:s',time());
	$datas['subscribe_time'] = date('Y-m-d H:i:s',time());
	$datas['subscribe_datetime'] = date('Y-m-d H:i:s',time());
	$id = $db->query("insert into service_userinfo set ".$db->get_set($datas));
}*/
$openId = 'oXgRcuKECWvDLQo9M-7_TaBkdfDY';
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
    <link rel="stylesheet" type="text/css" href="css/zepto.alert.css?15">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
        html {height: 100%;-webkit-tap-highlight-color:rgba(0,0,0,0); -webkit-tap-highlight:rgba(0,0,0,0);-webkit-text-size-adjust:none;overflow:-moz-scrollbars-vertical;}
        body {font-family:"Microsoft Yahei"; width: 640px; max-width: 640px; min-width: 640px; height: 100%; margin: 0 auto; background: #EEEEEE;}
        header{margin:0 auto;   height: 168px;}
        header .top{float:left; height:168px;font-size:32px; text-align:center;}
        header .t1{background:#ea7215;width:100%;}
        header .t2{background:#119fac;width:40%;}
        header .top a{display:block; width:100%;height:100%;color:#fff;font-size:32px;}
        header .top span{display:block;padding:35px 0 0; text-align:center;}
        .top span img{width:55px;}
        img{border:none;}
        a{text-decoration:none;}
        .menu{background:#fff;width:100%; height:92px; }
        .content{background:#eee;padding:26px 0px;}
        .content ul{ clear:both;margin:0;padding:0;overflow:hidden;}
        .content li{float:left;background:#d8d8d8;width:149px;height:62px;line-height:62px;margin:0 8px 0 0; display:inline-block;border-radius:25px 25px 0 0;font-size:28px; color:#666;text-align:center;}
        .content li.on{background:#0fadbb;color:#fff;}
        .box{background:#fff;}
        .list{width:100%;height:90px;margin:0 0 8px;border-bottom:1px #ddd solid;    padding: 16px 0;}
        .list .btt{width:440px;float:left;font-size:18px; color:#444;margin-left:5%;}
        .list .btt p{margin:0;display:block; padding:5px 0 0 0;}
        .list .btt b{font-size:28px; color:#0fadbb;font-weight:100;}
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
        .title img{
            width: 30px;
        }
        .title span{
            color: #fff;
            margin-left: 20px;
        }
        .lists{
            width: 100%;
            height: 90px;
            margin: 0 0 18px;
            border-bottom: 1px #ddd solid;
            padding: 16px 0;
        }
        .title{ clear:both;background:#c1cecf;font-size:28px;color:#666;height:56px;line-height:56px;font-weight:100;text-indent:1em;display:none}
        .menu a img{
            width: 52px;
            height: 41px;
            margin-right: 10px;
            padding-top: 8px;
        }
        .scode{
            width: 330px;
            margin-left: 6%;
            font-size: 28px;
            color: #2ab690;
            height: 42px;
        }
        .menu .biao{
            width: 90%;
            height: 30px;
            text-align: left;
            padding-left:6%;
            display: block;
            font-size: 20px;
            color: #2ab690;
        }
    </style>
</head>
<body>
<?php
$type = $_GET['type'];
$groupId = $_GET['groupId'];
$domainUrl = $_GET['domainUrl'];
$defaultCode = $_GET['defaultCode'];
$deviceCommand = $_GET['deviceCommand'];
$dourl = '';
if(!empty($domainUrl)){
	$_SESSION['current_type'] = $type;
	$_SESSION['current_group'] = $groupId;
	$_SESSION['default_code'] = $defaultCode;
	$_SESSION['device_command'] = $deviceCommand;
	$_SESSION['domainUrl'] = $domainUrl;
}else{
	$dourl = $_SESSION['domainUrl'];
	$type = $_SESSION['current_type'];
	$groupId = $_SESSION['current_group'];
	$defaultCode = $_SESSION['default_code'];
    $deviceCommand=$_SESSION['device_command'];
}
$adv = $db->get_row("select id from weixin_qcode where status=1 and device_code='$deviceCommand' and del_flag=0");
?>
		<header>
				<div class="top t1">
					<?php $url_params = "group_id=$groupId"."&type=".$type."&default_code=".$defaultCode ;?>
					<a href="<?php if(empty($domainUrl)){echo $dourl;}else{echo $domainUrl;}?>/wxpay/redirect.php?<?php echo $url_params;?>"><span><img src="imgs/weixin.png" style="margin-bottom: 12px;"/></span>点击微信支付</a>
				</div>
				<!--<div class="top t2">
				<?php //if(empty($domainUrl)){echo $dourl;}else{echo $domainUrl;}?>/wxpay/redirect.php?<?php //echo $url_params;?>
					<a href="play.php?openid=<?php //echo $openId;?>"><span><img src="imgs/doubi.png" /></span>逗币免费中心</a>
				</div>-->
	</header>
	<div class="space-10"></div>
	<div class="title"><img src="imgs/doubi.png" /><span>攒逗币，免费玩娃娃机 （10逗币=1元）</span></div>
	<section>
		<div class="menu" <?php if($adv==false){echo "style='display:none'";}?>>
			<a href="http://t.7i1.cn/scan/showWexinPublicAndApp?device_command=<?php echo $deviceCommand;?>">
				<span class="scode"><img src="imgs/weixin_logos.png" width="50" height="50"/>点击关注微信&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b style="margin-left: 30%;">免费玩</b></span><br/>
				<span class="biao">①点开长按三秒识别图片 ②关注微信  ③点击启动信息</span>
			</a>
		</div>

		<div class="content">
			<?php //$old_url = "groupId=".$groupId."&type=".$type."&defaultCode=".$defaultCode."&domainUrl=".$domainUrl."deviceCommand=".$deviceCommand;?>
			<!--<div class="list" style="background: #fff;color:#EA0F40;border-top:2px solid #0fadbb;" onclick="location.href='http://doubi.7i1.cn/doubi/play.php?<?php //echo $old_url;?>'">
				<div class="btt">
					<b style="color:#EA0F40;">新版上线过渡，原有版本请点击</b>
				</div>
			</div>-->
			<!--<div class="list" style="background: #fff;color:#EA0F40;border-top:2px solid #0fadbb;" onclick="location.href='http://tt.7i1.cn/doubi/money_kefu_code.php'">
				<div class="btt">
					<b style="color:#EA0F40;">全民扫码关注，免费玩</b>
					<p>扫码关注客服号，逗币礼包送不停</p>
				</div>
			</div>-->
			<!--<ul>
				<li id="mod0" class="on" onClick="rshow('mod','menu_con_','0');">推荐</li>
				<li id="mod1" onClick="rshow('mod','menu_con_','1');">人气</li>
				<li id="mod2" onClick="rshow('mod','menu_con_','2');">发现</li>
				<li id="mod3" onClick="rshow('mod','menu_con_','3');" style="margin:0 0 0;">热评</li>
			</ul>-->
			<?php 
				//$group_id = $_GET['groupId'];
				$device_code = $_GET['deviceCode'];
				$cardConfigDiscoverysSql = "SELECT 	cc.* FROM	card_config cc ,card_merchant cm WHERE	cc.card_status = '1'
				and cc.type=1 AND  cm.id = cc.merchant_id AND  TO_DAYS(NOW()) BETWEEN TO_DAYS(cc.begin_timestamp) AND
				 TO_DAYS(cc.end_timestamp) AND cc.del_flag = '0' AND cc.surplus_quantity > 0 and
				 INSTR(cm.default_group_id ,'$groupId')> 0 ORDER BY	rand()";
				$cardConfigRecommendsSqL = "";
				$cardConfigEvaluatesSql = "";
				$cardConfigStarLevels = "";
				$cardConfigDiscoverysResults = $db->get_results($cardConfigDiscoverysSql);
				if($cardConfigDiscoverysResults!=null){
			?>

			<div class="box" id="menu_con_0" >
				<?php foreach($cardConfigDiscoverysResults as $cc ){?>
					<div class="list" >
						<div class="btt" onclick="location.href='merchant_detail.php?merchant_id=<?php echo $cc->merchant_id;?>'">
							<b><?php echo $cc->title;?></b>（<?php echo $cc->sub_title;?>）
							<p><span><?php echo $cc->brand_name;?></span><?php echo $cc->description;?></p>
						</div>
						<div class="xing">
							<?php if($cc->star_level == 1){?>
								<span class="icon-star-full on"></span>
							<?php }?>
							<?php if($cc->star_level == 2){?>
								<?php for($i=1;$i<=2;$i++){ ?>
									<span class="icon-star-full on"></span>
								<?php }?>
							<?php }?>
							<?php if($cc->star_level == 3){?>
								<?php for($i=1;$i<=3;$i++){ ?>
									<span class="icon-star-full on"></span>
								<?php }?>
							<?php }?>
							<?php if($cc->star_level == 4){?>
								<?php for($i=1;$i<=4;$i++){?>
									<span class="icon-star-full on"></span>
								<?php }?>
							<?php }?>
							<?php if($cc->star_level == 5){?>
								<?php for($i=1;$i<=5;$i++){ ?>
									<span class="icon-star-full on"></span>
								<?php }?>
							<?php }?>
						</div>
						<div class="lingquan">
							<!--<span><?php /*echo $cc->star_level;*/?></span>-->
							<span id="surId<?php echo $cc->id;?>" vid = "<?php echo ($cc->quantity - $cc->surplus_quantity);?>" >
									已领<?php echo ($cc->quantity - $cc->surplus_quantity);?>次
							</span>
							<div class="lq" ccid="<?php echo $cc->id;?>" quantity="<?php echo $cc->quantity;?>" 
									merchantid="<?php echo $cc->merchant_id;?>"
							>领券</div>
						</div>
					</div>
				<?php } ?>
				<!--<div class="ad" onclick="location.href='http://<?php /*echo $_SERVER['HTTP_HOST'];*/?>/weixin_pay/index.php'">
					广告图片
				</div>-->
			</div>
			<?php }	
			?>
			<?php
			//设备的区域  $groupId
			$area = $db->get_row("select area_id from device_group_info where id='$groupId' and del_flag=0");
			$area = object_array($area);
			$area_id = $db->get_row("select id,parent_ids from sys_area where del_flag=0 and id='$area[area_id]'");
			$area_id = object_array($area_id);
			//删除逗号
			$parent_ids = trim($area_id['parent_ids'],',');
			//组成数组
			$parent_ids = explode(',',$parent_ids);
			$ads = $db->get_results("select * from card_deposit where del_flag=0 order by create_date desc");
			foreach($ads as $k=>$va){
				//删除逗号
				$dou = trim($va->area_id,',');
				//组成数组
				$area_ids = explode(',',$dou);
				foreach($area_ids as $v){
					if(in_array($v,$parent_ids)){?>
						<div class="list" style="background: #fff;border-top:2px solid #0fadbb;" onclick="location.href='money_dpay_index.php?id=<?php echo $va->id;?>&money=<?php echo intval($va->price);?>&merchant_id=<?php echo $va->merchant_id;?>'">
							<div class="btt" style="width:92%;">
								<b><?php echo $va->title;?></b><!--（送<?php /*echo $va->present_price;*/?>个逗币）-->
								<p><span><?php /*echo $va->present_price;*/?></span><?php echo $va->sub_title;?></p>
							</div>
						</div>
			<?php }}}?>
			<?php
				$pro = $db->get_results("select * from card_product where del_flag=0 order by create_date desc");
			foreach($pro as $va){
			?>
			<div class="list" style="background: #fff;border-top:2px solid #0fadbb;" onclick="location.href='shopping_index.php?id=<?php echo $va->id;?>'">
				<div class="btt">
					<b><?php echo $va->name;?></b><!--（送<?php /*echo $va->present_price;*/?>个逗币）-->
					<p><span><?php /*echo $va->present_price;*/?></span><?php echo $va->description;?></p>
				</div>
				<div class="lingquan">
					<span>
							已买人数：<?php echo $va->purchase_people;?>
					</span>
				</div>
			</div>
			<?php }?>

			<div class="list" style="       bottom: 0;left:0;background: #fff;border-top:2px solid #0fadbb;text-align: center;height:120px;font-size:28px;height:230px;" >
			梦想广加招商合作加盟<br/>
				<img width="200" src="http://7xlcfz.com1.z0.glb.clouddn.com/qrcode_for_gh_02a8fc124767_430.jpg">
			</div>
			
			<div class="box" id="menu_con_1">

			</div>
			
			<div class="box" id="menu_con_2" style="display:none;">
				
			</div>
			
			
			<div class="box" id="menu_con_3" style="display:none;">
				
			</div>
		</div>
	</section>
	
	<input type="hidden" id="openId" value="<?php echo $openId;?>"/>
	<input type="hidden" id="deviceCode" value="<?php echo $defaultCode;?>"/>

<script>
		function $(id){
		return document.getElementById(id);
		}	
		
		function rshow(modtag,modcontent,modk) {
		for(i=0; i <4; i++) {
		if (i==modk) {
		$(modtag+i).className="on";$(modcontent+i).style.display="block";}
		else {
		$(modtag+i).className=""+i;$(modcontent+i).style.display="none";}
		}
		}
</script>
<script type="text/javascript" src="js/zepto.min.js"></script>
<script type="text/javascript" src="js/zepto.alert.js"></script>
<script type="text/javascript" src="js/fastclick.min.js"></script>
<script type="text/javascript" src="js/index.js?10002" charset="UTF-8"></script>
</body>
</html>