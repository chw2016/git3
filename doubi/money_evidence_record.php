<?php
    session_start();
    require_once "mysqldbread.php";
    if(empty($_SESSION['openId'])){
        $openid = $_GET['openid'];
    }else{
        $openid = $_SESSION['openId'];
    }
    $list = $db->get_results("select * from card_doubi where user_id='$openid' and del_flag=0 and user_type=1 order by create_date desc limit 10");
$list = object_array($list);
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>凭证记录</title>
    <link rel="stylesheet" href="./money_css/frozen.css">
    <link rel="stylesheet" href="./money_css/scrollbar.css" />
    <link rel="stylesheet" href="./money_css/pay.css" />
</head>
<body>
<div id="header">
    <a href="">凭证记录</a>
</div>
<div id="wrapper" style="overflow: hidden; left: 0px;">
    <div id="scroller" style="transition-property: transform; transform-origin: 0px 0px 0px; transform: translate(0px, -1618px) scale(1) translateZ(0px);">
        <ul id="thelist">
            <li>
                <table>
                    <tr>
                        <td>豆币数量</td>
                        <td>类型</td>
                        <td>状态</td>
                        <td>系统时间</td>
                    </tr>
                </table>
            </li>
            <?php foreach($list as $v){?>
                <li style="line-height: 30px;">
                    <table>
                        <tr>
                            <td class="word-break"><?php echo $v['quantity'];?></td>
                            <td><?php if($v['coin_type']=='1'){echo '消费';}else{echo '充值';}?></td>
                            <td><?php
                                    if($v['coin_type'] == '1'){
                                            if($v['coin_status'] == 0){
                                                echo '刚领取';
                                            }else if($v['coin_status'] == 1){
                                                echo '有效';
                                            }else{
                                                echo '已过期';
                                            }
                                    }else{
                                        if($v['coin_status'] == 0){
                                            echo '无效';
                                        }else{
                                            echo '有效';
                                        }
                                    }
                                ?></td>
                            <td><?php echo $v['create_date'];?></td>
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
       <span onclick="location.href='<?php echo $gj_url;?>/play.php'">
           后退</span>
    <div class="kefu" onclick="location.href='<?php echo $gj_url;?>/money_kefu.php?openid=<?php echo $openid;?>'">
        订单详情
    </div>
    </menu>
</div>
<style>
    .kefu{
        width: 100px;
        height: 100px;
        color:#f43d30;
        float: right;
        margin-right: 40px;
    }
.ucenter-menu{
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 40px;
    line-height: 40px;
    background: #fff;
    z-index: 10;
    color: #B9B3B3;
    padding-left: 26px;
    text-align: left;
    border-top: 1px solid #f0f0f0;
}
</style>
<input type="hidden" value="<?php echo $openid;?>" class="openid">
<script src='./money_js/iscroll.js'></script>
<script src='./money_js/jquery-1.9.1.min.js'></script>
</body>
<script type="text/javascript">
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
            $.post("money_query_device_group.php?action=user_ajax",{page:generatedCount,openid:openid},function(json){
                if(json.msg==2){
                    hide = 2;
                    $('.pullUpLabel').html('别拉我了，已经没有数据了');
                    return false;
                }
                var coin_type = '';
                var coin_status = '';
                $.each(json,function(index,array){
                    if(array['coin_type'] == '1'){
                        coin_type = '消费';
                        if(array['coin_status'] == 0){
                            coin_status = '刚领取';
                        }else if(array['coin_status'] == 1){
                            coin_status = '有效';
                        }else{
                            coin_status = '已过期';
                        }
                    }else{
                        if(array['coin_status'] == 0){
                            coin_status = '无效';
                        }else{
                            coin_status = '有效';
                        }
                        coin_type = '充值';
                    }
                    $('#thelist').append(
                        '<li style="line-height: 30px;">'
                        +'<table>'
                        +'<tr>'
                        +'<td class="word-break">'+array['quantity']+'</td>'
                        +'<td>'+coin_type+'</td>'
                        +'<td>'+coin_status+'</td>'
                        +'<td>'+array['create_date']+'</td>'
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