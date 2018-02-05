<?php
    require_once "mysqldbread.php";
    require "jssdk.php";
    $jssdk = new JSSDK($APP_ID,$APP_SECRET);
    $signPackage = $jssdk->GetSignPackage();
    $openid = trim($_GET['openid']);
    $one = $db->get_row("select * from card_merchant where openid='$openid' and status=1");
$one = object_array($one);
    if($one){
        $list = $db->get_results("select * from card_config where merchant_id='$one[id]' and card_status=1
              and del_flag='0' and  surplus_quantity > 0 and type=1 order by create_date limit 10");
        $list = object_array($list);
    }
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>优惠列表</title>
    <link rel="stylesheet" href="./money_css/frozen.css?v=20160623">
    <link rel="stylesheet" href="./money_css/scrollbar.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/pay.css?v=20160623" />
</head>
<style>
    #thelist table td{
        width: 20%;
    }
</style>
<body>
<div id="header">
    <a href="">优惠列表</a>
</div>
<div class="tab-box-main">
    <div>支付接口</div>
    <div>收支类型</div>
    <div>总的数量</div>
    <div>系统时间</div>
</div>
<div id="wrapper" style="overflow: hidden; left: 0px;">
    <div id="scroller" style="transition-property: transform; transform-origin: 0px 0px 0px; transform: translate(0px, -1618px) scale(1) translateZ(0px);">
        <ul id="thelist">
            <li>
                <table>
                    <tr>
                        <td>商户名称</td>
                        <td>卡券名</td>
                        <td>券名</td>
                        <td>数量</td>
                        <td>操作</td>
                    </tr>
                </table>
            </li>
            <?php foreach($list as $v){?>
                <li>
                    <table>
                        <tr>
                            <td class="word-break"><?php echo $v['brand_name'];?></td>
                            <td><?php echo $v['title'];?></td>
                            <td><?php echo $v['sub_title'];?></td>
                            <td><?php echo $v['quantity'];?></td>
                            <td><a href="money_volume_detail.php?openid=<?php echo $openid;?>&id=<?php echo $v['id'];?>">详情</a>&nbsp;<a href="money_volume_likes.php?openid=<?php echo $openid;?>&id=<?php echo $v['id'];?>">类似</a></td>
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
            <li onclick="location.href='money_recharge.php?openid=<?php echo $_REQUEST['openid'];?>'">
                <i class="icon-profile" style="width:25px;"></i><br>充值
            </li>
            <li class="active">
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
            $.post("money_query_device_group.php?action=ajax",{page:generatedCount,openid:openid},function(json){
                if(json.msg==2){
                    hide = 2;
                    $('.pullUpLabel').html('别拉我了，已经没有数据了');
                    return false;
                }
                $.each(json,function(index,array){
                    $('#thelist').append(
                        '<li>'
                        +'<table>'
                        +'<tr>'
                        +'<td class="word-break">'+array['brand_name']+'</td>'
                        +'<td>'+array['title']+'</td>'
                        +'<td>'+array['sub_title']+'</td>'
                        +'<td>'+array['quantity']+'</td>'
                        +'<td><a href="money_volume_detail.php?openid=<?php echo $openid;?>&id='+array['id']+'">详情</a>&nbsp;'
                        +'&<a href="money_volume_likes.php?openid=<?php echo $openid;?>&id='+array['id']+'">类似</a></td>'
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