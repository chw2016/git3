<?php
require_once "mysqldbread.php";
require "jssdk.php";
$jssdk = new JSSDK($APP_ID,$APP_SECRET);
$signPackage = $jssdk->GetSignPackage();
$openid = trim($_GET['openid']);
$merchant_id = $db->get_row("select * from card_merchant where openid='$openid' and status=1 and del_flag=0");
$list = $db->get_results("select * from card_doubi where  user_type = 2 and coin_status = 1 and del_flag = 0 and merchant_id='$merchant_id->id' order by create_date desc limit 10");
$list = object_array($list);
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>收支列表</title>
    <link rel="stylesheet" href="./money_css/frozen.css?v=20160623">
    <link rel="stylesheet" href="./money_css/scrollbar.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/pay.css?v=20160623" />
	<style>
		.ucenter-menu li.active>i.icon-invite {
    background-position: -235px -180px;
}
	</style>
</head>
<body>
<div id="header">
    <a href="">收支列表</a>
</div>
<div class="tab-box-main">
    <div>金额</div>
    <div>收支类型</div>
    <div>状态</div>
    <div>系统时间</div>
</div>
<div id="wrapper" style="overflow: hidden; left: 0px;">
    <div id="scroller" style="transition-property: transform; transform-origin: 0px 0px 0px; transform: translate(0px, -1618px) scale(1) translateZ(0px);">
        <ul id="thelist">
            <li>
                <table>
                    <tr>
                        <td>金额</td>
                        <td>收支类型</td>
                        <td>状态</td>
                        <td>系统时间</td>
                    </tr>
                </table>
            </li>
            <?php foreach($list as $v){?>
                <li>
                    <table>
                        <tr>
                            <td class="word-break"><?php echo $v['quantity'];?></td>
                            <td><?php if($v['coin_type'] == 1){echo '消费';}else{echo '充值';}?></td>
                            <td><?php if($v['coin_status'] == 1){echo '有效';}else{echo '无效';}?></td>
                            <td><?php echo substr($v['create_date'],0,11);?></td>
                        </tr>
                    </table>
                </li>
            <?php }?>
        </ul>
        <div id="pullUp" class="">
            <span class="pullUpIcon"></span><span class="pullUpLabel">上拉加载更多...</span>
        </div>
    </div>
    <div class="myScrollbarV" style="pointer-events: none; transition-property: opacity; transition-duration: 350ms; overflow: hidden; opacity: 0;"><div style="pointer-events: none; transition-property: transform; transition-timing-function: cubic-bezier(0.33, 0.66, 0.66, 1); transform: translate(0px, 398px) translateZ(0px); height: 135px;"></div></div></div>
<div class="footer">
    <menu class="ucenter-menu">
        <ul>
            <li onclick="location.href='money_merchant.php?openid=<?php echo $_REQUEST['openid']?>'">
                <i class="icon-home"></i><br>商家
            </li>
            <li onclick="location.href='money_recharge.php?openid=<?php echo $_REQUEST['openid'];?>'" class="active">
                <i class="icon-profile" style="width:25px;"></i><br>充值
            </li>
            <li onclick="location.href='money_favorable.php?openid=<?php echo $_REQUEST['openid'];?>'">
                <i class="icon-invite" style="width:26px;"></i><br>优惠
            </li>
        </ul>
    </menu>
</div>
<input type="hidden" value="<?php echo $openid;?>" class="openid">
<script src='./money_js/iscroll.js'></script>
<script src='./money_js/jquery-1.9.1.min.js'></script>
<script src='./money_js/jsweixin1.0.js'></script>
</body>
<script type="text/javascript">
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
    var myScroll,
        pullDownEl, pullDownOffset,
        pullUpEl, pullUpOffset,
        generatedCount = 2,
        hide = 1;
    /**
     * 初始化iScroll控件
     */
    function loaded() {
        pullUpEl = document.getElementById('pullUp');
        pullUpOffset = pullUpEl.offsetHeight;
        myScroll = new iScroll('wrapper', {
            scrollbarClass: 'myScrollbar', /* 重要样式 */
            useTransition: true,
            vScrollbar:true,
            onRefresh: function () {
                if (pullUpEl.className.match('loading')) {
                    pullUpEl.className = '';
                    if(hide == 1){
                        pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
                    }else{
                        pullUpEl.querySelector('.pullUpLabel').innerHTML = '别拉我了，已经没有数据了';
                    }
                }
            },
            onScrollMove: function () {
                if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
                    pullUpEl.className = 'flip';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '松手开始更新...';
                    this.maxScrollY = this.maxScrollY;
                } else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
                    pullUpEl.className = '';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
                    this.maxScrollY = pullUpOffset;
                }
            },
            onScrollEnd: function () {
                if (pullUpEl.className.match('flip')) {
                    pullUpEl.className = 'loading';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载中...';
                    pullUpAction();
                }
            }
        });
        setTimeout(function () { document.getElementById('wrapper').style.left = '0'; }, 800);
    }
    document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
    document.addEventListener('DOMContentLoaded', loaded, false);
    /**
     * 滚动翻页 （自定义实现此方法）
     * myScroll.refresh();		// 数据加载完成后，调用界面更新方法
     */
    function pullUpAction () {
        setTimeout(function () {
            var openid = "<?php echo $openid;?>";
            $.post("money_query_device_group.php?action=money",{page:generatedCount,openid:openid},function(json){
                if(json.msg==2){
                    hide = 2;
                    $('.pullUpLabel').html('别拉我了，已经没有数据了');
                    return false;
                }
                $.each(json,function(index,array){
                    var coin_type = '';
                    var coin_status = '';
                    if(array['coin_type']==1){
                        coin_type = '消费';
                    }else{
                        coin_type = '充值';
                    }
                    if(array['coin_status']==1){
                        coin_status = '有效';
                    }else{
                        coin_status = '无效';
                    }
                    $('#thelist').append(
                        '<li>'
                        +'<table>'
                        +'<tr>'
                        +'<td class="word-break">'+array['quantity']+'</td>'
                        +'<td>'+coin_type+'</td>'
                        +'<td>'+coin_status+'</td>'
                        +'<td>'+array['create_date'].substring(0,10)+'</td>'
                        +'</tr>'
                        +'</table>'
                        +'</li>'
                    );
                });
                generatedCount++;
            },'json');
            myScroll.refresh();
        }, 1000);
    }
    var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>