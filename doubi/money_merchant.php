<?php
session_start();
    include "mysqldbread.php";
    require "jssdk.php";
    $jssdk = new JSSDK($APP_ID,$APP_SECRET);
    $signPackage = $jssdk->GetSignPackage();
    $code = $_GET['code'];
    $user_openid = $_SESSION['users_openid'];
    $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$APP_ID&secret=$APP_SECRET&code=$code&grant_type=authorization_code";
    $return_results = file_get_contents($access_token_url);
    $return_json =json_decode($return_results);
    $access_token = $return_json->access_token;
    $openid = $return_json->openid;
    $get_openid = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
    $edopenid = file_get_contents($get_openid);
    $head = json_decode($edopenid);
    $request = $_REQUEST['openid'];
    if(!isset($_SESSION["headimgurl"])){
    	$_SESSION["openid"] = $openid;
    	$_SESSION["headimgurl"] = $head->headimgurl;
	}
    if(empty($openid) && empty($request)){
        header('Location:'.$gj_url.'/money_index.php');
    }else{
        $m_id = $db->get_row("select * from card_merchant where openid='$openid'");
        $m_id = object_array($m_id);
    }
    if($request){
        $m_id = $db->get_row("select * from card_merchant where openid='$request'");
        $m_id = object_array($m_id);
    }
    $group = $db->get_results("select id,group_name from device_group_info where status=1 order by ords");
$group = object_array($group);
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>商家基本信息</title>
    <link rel="stylesheet" href="./money_css/scrollbar.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/pay.css?v=201606241" />
    <link rel="stylesheet" href="./money_css/frozen.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/frozen1.css?v=20160624" />
    <link rel="stylesheet" href="./money_css/table.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/common.css?v=20160623" />
    <style>
        .ui-border-l{border-left:none;}
		.ucenter-menu li.active>i.icon-invite {
    background-position: -235px -180px;
}
    </style>
</head>
<body ontouchstart>
<div id="header">
    <a href="">商家基本信息</a>
</div>
<div class="content">
    <section class="ui-container">
        <div class="space-10" style="margin-top: 5px;    border-bottom: 1px solid #e0e0e0;"></div>
        <div class="ui-form-item ui-form-item-r ui-border-b" style="display:none">
            <button type="button" class="ui-border-l">商家appid</button>
            <input type="text" placeholder="商家微信appid" class="appid" readonly="readonly" value="<?php echo $APP_ID;?>">
        </div>
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >名称</button>
            <input type="text" placeholder="请输入你的名称" class="name">
        </div>
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >地址</button>
            <input type="text" placeholder="请输入你的地址" class="address">
        </div>
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >电话号码</button>
            <input type="text" placeholder="请输入电话号码" class="phone">
        </div><!--
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >逗币数</button>
            <input type="text" placeholder="请输入逗币数" class="total" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
        </div>-->
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >备注</button>
            <input type="text" placeholder="请输入备注信息（可不填）" class="remarks">
        </div>
        <!--<div class="ui-form-item ui-form-item-r ui-border-b">
            <select name="group_num" id="device_group_id">
            </select>
            <button type="button" class="ui-border-l">群组编号</button>
        </div>-->

        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >群组</button>
        <ul class="um-list um-list-form" id="J_chooseProject">
            <li><label class="label"></label><input type="text" placeholder="请选择群组" id="customer_project" readonly="readonly" class="color-blue" data-ids="" value=""><i class="icon-arrow-gray"></i></li>
        </ul>
        </div>

        <input type="hidden" class="lat_lon" value="">
        <?php if(empty($openid)){?>
        <input type="hidden" value="<?php echo $_REQUEST['openid'];?>" class="openid">
        <?php }else{?>
            <input type="hidden" value="<?php echo $openid;?>" class="openid">
        <?php } ?>
        <div class="ui-btn-wrap">
            <button class="ui-btn-lgs ui-btn-primary btn-all-update">
                确定
            </button>
            <?php if(empty($m_id)){?>
            <button class="ui-btn-lgs ui-btn-primary" style="margin-top: 10px;" onclick="location.href='money_index.php'">
                修改商家信息
            </button>

            <?php }else{?>
                <button class="ui-btn-lgs ui-btn-primary" style="margin-top: 10px;" onclick="location.href='money_update_merchant.php?openid=<?php echo $_SESSION["openid"];?>'">
                    修改商家信息
                </button>
            <?php } ?>
        </div>
    </section>
</div>

<section class="dialog-choose-project animated fast slideInUp" style="display:none;">
    <div class="dcp-main">
        <div class="ui-form ui-border-t">
            <div class="ui-form-item ui-form-item-switchs ui-border-b">
                <input type="input" class="search" name="search" placeholder="搜索" style="height:40px;" onkeyup="search()"/>
                <!--<label class="ui-checkbox">
                    <input type="button" class="sea" onclick="searcdh()" value="搜索">
                </label>-->
            </div>
                <?php foreach($group as $v){?>
                    <div class="ui-form-item ui-form-item-switch ui-border-b">
                        <span><?php echo $v['group_name'];?></span>
                        <label class="ui-checkbox">
                            <input type="checkbox" class="cur_house" value="<?php echo $v['id'];?>">
                        </label>
                    </div>
                <?php }?>
        </div>
        <div class="space-50"></div>
        <div class="space-50"></div>
    </div>
</section>
<aside class="account-submit account-submit-fixed" style="display:none;">
    <p class="um-tips"><em>提示：</em>最多只能选择7个群组</p>
    <div class="ui-btn-group-tiled ui-btn-wrap">
        <button class="ui-btn-lg ui-btn-danger" style="background:#00938d" id="J_submitProjectChoose">确认</button> <button class="ui-btn-lg" id="J_cancelProjectChoose">取消</button>
    </div>
</aside>

<div class="footer">
    <menu class="ucenter-menu">
        <ul>
            <li class="active">
                <i class="icon-home"></i><br>商家
            </li>
            <?php if($m_id == NULL){?>
                <li onclick="location.href='money_index.php'">
                    <i class="icon-profile" style="width:25px;"></i><br>充值
                </li>
                <li onclick="location.href='money_index.php'">
                    <i class="icon-invite" style="width:26px;"></i><br>优惠
                </li>
            <?php }else{
                if(empty($openid)){
            ?>
                <li onclick="location.href='money_recharge.php?openid=<?php echo $_REQUEST['openid'];?>'">
                    <i class="icon-profile" style="width:25px;"></i><br>充值
                </li>
                <li onclick="location.href='money_favorable.php?openid=<?php echo $_REQUEST['openid'];?>'">
                    <i class="icon-invite" style="width:26px;"></i><br>优惠
                </li>
            <?php }else{?>
                <li onclick="location.href='money_recharge.php?openid=<?php echo $openid;?>'">
                    <i class="icon-profile" style="width:25px;"></i><br>充值
                </li>
                <li onclick="location.href='money_favorable.php?openid=<?php echo $openid;?>'">
                    <i class="icon-invite" style="width:26px;"></i><br>优惠
                </li>
            <?php } }?>
        </ul>
    </menu>

</div>
<input type="hidden" value="<?php echo $user_openid;?>" class="user_openid">
</body>
<script type="text/javascript" src="./money_js/zepto.js"></script>
<script type="text/javascript" src="./money_js/frozen.js"></script>
<script type="text/javascript" src="./money_js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="./money_js/getLocation.js"></script>
<script type="text/javascript" src="./money_js/getscript"></script>
<script type="text/javascript" src="./money_js/switchery.min.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">

    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'checkJsApi',
            'getLocation',
            'hideOptionMenu'
        ]
    });
    $(function(){
        wx.ready(function () {
            wx.hideOptionMenu();
        });
    })
    wx.ready(function () {
            wx.getLocation({
                type: 'wgs84',
                success: function (res) {
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    var speed = res.speed; // 速度，以米/每秒计
                    var accuracy = res.accuracy; // 位置精度
                    $('.lat_lon').val(longitude+','+latitude);
                },
                cancel: function (res) {
                    alert('用户拒绝授权获取地理位置');
                },
                error:function(res){
                    alert('获取地理位置失败');
                }
            });
    });
    $(function(){
        var address = "";
        $(".address").val("正在获取位置");
        getCity(function(a){
            address = a.province+
                a.city +
                a.district +
                a.street +
                a.streetNumber;

            $(".address").val(address);

        })
        setTimeout(function(){
            if(address== ""){
                $(".address").val("获取位置失败");
                return false;
            }
        },6000)
        if (Array.prototype.forEach) {
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            elems.forEach(function(html) {
                var switchery = new Switchery(html);
            });
        } else {
            var elems = document.querySelectorAll('.js-switch');
            for (var i = 0; i < elems.length; i++) {
                var switchery = new Switchery(elems[i]);
            }
        }
    });
    function search(){
        var search = $('.search').val();
        var aLi = $('.ui-form-item-switch');
        var is_all_none = 1;
        for(var i=0;i<aLi.length;i++){
            var span=$(aLi[i]).find('span');
            if((span.text().indexOf(search))>=0){//indexOf()匹配字符串
                $(aLi[i]).css('display','block');//aLi[i]必须要有美元符号才可以操作CSS
                is_all_none = 0;
            }else{
                $(aLi[i]).css('display','none');
            }
        }
        /*if(is_all_none == 1){
            alert('亲，什么都没有搜到哦！');
        }*/
    }
    //--------------------------------------------------
    Zepto(function($) {
        var chooseProject=$('#J_chooseProject');
        var J_submitProjectChoose=$('#J_submitProjectChoose');
        var J_cancelProjectChoose=$('#J_cancelProjectChoose');
        var customer_project=$('#customer_project');
        var dialogChooseProject=$('.dialog-choose-project');
        var accountSubmitFixed=$('.account-submit-fixed');
        chooseProject.click(function(){
            dialogChooseProject.removeClass('fadeOutDown').show();
            setTimeout(function(){
                accountSubmitFixed.show();
            },600);
            //$('html,body').css('overflow','hidden');
        });
        //关闭
        J_cancelProjectChoose.click(function(){
            dialogChooseProject.addClass('fadeOutDown').hide();
            accountSubmitFixed.hide();
            //$('html,body').css('overflow','auto');
        });
        var dcpuiform=$('.dcp-main .ui-form');
        var dcpformitem=$('.dcp-main .ui-form-item');
        dcpformitem.click(function(){

            var input=$(this).find('input');
            // $("#customer_project").val(input.val());
            if(input.prop('checked')){
                input.prop('checked',false);
            }else{
                var checked=dcpuiform.find(':checked');
                /*for(var i=0;i<checked.length;i++){只能选择一个
                checked[i].checked = false;
                }*/
                var arr=[];
                checked.each(function(){
                    arr.push($(this).val());
                });
                if(arr.length>6){
                    return false;
                }
                input.prop('checked',true);
            }
        });
        //确定选择
        J_submitProjectChoose.click(function(){
            var checked=dcpuiform.find(':checked');
            var arr=[];
            checked.each(function(){
                arr.push($(this).val());
            });
            customer_project.val('您已选择了 '+arr.length+' 个群组').attr('data-ids',arr.join(','));
            J_cancelProjectChoose.trigger('click');
        });
        /*var el = $.loading({
            content: '拼命加载中...'
        });
        $.post("query_device_group.php?action=group", function (reg) {
            if (reg.msg == 1) {
                $.each(reg.data, function (i, o) {
                    $('#device_group_id').append(
                        '<option value="' + o.id + '">' + o.group_name + '</option>'
                    );
                });
            } else {
                $.dialog({
                    content: '没有数据',
                    button: ['我知道了']
                });
            }
            el.hide();
        }, 'json');*/

        var reg = {
            phone:/(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)|(^0{0,1}1[0|1|2|3|4|5|6|7|8|9][0-9]{9}$)/
        };
        $('.btn-all-update').tap(function(){
            var phone = $('.phone').val();
            var appid = $('.appid').val();
            var name = $('.name').val();
            var lat_lon = $('.lat_lon').val();
            var address = $('.address').val();
            //var total = $('.total').val();
            var remarks = $('.remarks').val();
            var openid = $('.openid').val();
            var device_group_id = $('#device_group_id').val();
            var user_openid = $('.user_openid').val();
            var pids=customer_project.attr('data-ids');
            if(!appid){
                $.dialog({
                    content: '请输入appid',
                    button: ['ok']
                });
                return false;
            }
            if(!name){
                $.dialog({
                    content: '请输入名称',
                    button: ['ok']
                });
                return false;
            }
            if(!address){
                $.dialog({
                    content: '请输入地址',
                    button: ['ok']
                });
                return false;
            }
            if(reg.phone.test(phone) == false){
                $.dialog({
                    content: '请输入正确的号码',
                    button: ['ok']
                });
                return false;
            }
            if(pids == ''){
                $.dialog({
                    content: '请至少选择一个群组',
                    button: ['ok']
                });
                return false;
            }
            var el = $.loading({
                content: '录入中'
            });
            var DATA = {
                appid:appid,
                name:name,
                address:address,
                phone:phone,
                remarks:remarks,
                lat_lon:lat_lon,
                openid:openid,
                device_group_id:pids,
                user_openid:user_openid
            };
            $.post("money_query_device_group.php?action=mer_insert",DATA, function (reg) {
                if (reg.msg == 1) {
                    var DG = $.dialog({
                        content: '录入成功',
                        button: ['ok']
                    });
                    DG.on('dialog:action',function(e){
                        document.location.href="money_recharge.php?openid="+openid;
                    });
                }else if (reg.msg == 3){
                    $.dialog({
                        content: '你已经填写信息了',
                        button: ['ok']
                    });
                }else if (reg.msg == 4){
                    $.dialog({
                        content: '该号码已经注册',
                        button: ['ok']
                    });
                }
                else{
                    $.dialog({
                        content: '录入失败',
                        button: ['ok']
                    });
                }
                el.hide();
            }, 'json');
        })
    });
    //--------------------------------------------------
    useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>