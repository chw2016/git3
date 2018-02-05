<?php
require_once "mysqldbread.php";
require "jssdk.php";
$jssdk = new JSSDK($APP_ID,$APP_SECRET);
$signPackage = $jssdk->GetSignPackage();
$openid = trim($_GET['openid']);
$list = $db->get_row("select * from card_merchant where openid='$openid' and status=1");
$list = object_array($list);
$all = $db->get_results("select id,group_name from device_group_info where status=1  order by ords");
$all = object_array($all);
$lists = explode(',',$list['default_group_id']);
foreach($all as $key => $v){
    if(in_array($v['id'],$lists)){
        $all[$key]['is'] = 1;
    }
}
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>商家基本信息修改</title>
    <link rel="stylesheet" href="./money_css/scrollbar.css" />
    <link rel="stylesheet" href="./money_css/pay.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/frozen.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/frozen1.css?v=20160624" />
    <link rel="stylesheet" href="./money_css/table.css?v=20160623" />
    <link rel="stylesheet" href="./money_css/common.css" />
</head>
<body ontouchstart>
<div id="header">
    <a href="">商家基本信息修改</a>
</div>

<div class="content">
    <section class="ui-container">
        <div class="space-10" style="margin-top: 5px;    border-bottom: 1px solid #e0e0e0;"></div>
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l">商家appid</button>
            <input type="text" placeholder="商家微信appid" class="appid" value="<?php echo $list['app_id'];?>" readonly="readonly">
        </div>
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >名称</button>
            <input type="text" placeholder="请输入你的名称" class="name" value="<?php echo $list['name'];?>">
        </div>
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >地址</button>
            <input type="text" placeholder="请输入你的地址" class="address" value="<?php echo $list['address'];?>">
        </div>
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >电话号码</button>
            <input type="text" placeholder="请输入电话号码" class="phone" value="<?php echo $list['phone'];?>">
        </div><!--
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >逗币数</button>
            <input type="text" value="<?php /*echo $list['total_doubi'];*/?>" placeholder="请输入逗币数" class="total" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
        </div>-->
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >备注</button>
            <input type="text" placeholder="请输入备注信息（可不填）" class="remarks" value="<?php echo $list['remarks'];?>">
        </div>
        <div class="ui-form-item ui-form-item-r ui-border-b">
            <button type="button" class="ui-border-l" >群组</button>
            <ul class="um-list um-list-form" id="J_chooseProject">
                <li><label class="label"></label><input type="text" placeholder="请选择群组" id="customer_project" readonly="readonly" class="color-blue" data-ids="" value=""><i class="icon-arrow-gray"></i></li>
            </ul>
        </div>
        <input type="hidden" value="<?php echo $openid;?>" class="openid">
        <div class="ui-btn-wrap">
            <button class="ui-btn-lgs ui-btn-primary btn-all-update">
                修改
            </button>
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
            <?php foreach($all as $v){?>
                <div class="ui-form-item ui-form-item-switch ui-border-b">
                    <span><?php echo $v['group_name'];?></span>
                    <label class="ui-checkbox">
                        <input type="checkbox" class="cur_house" value="<?php echo $v['id'];?>" <?php if($v['is'] == 1){ echo "checked='checked'";}?>>
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
            <li onclick="location.href='money_recharge.php?openid=<?php echo $_REQUEST['openid'];?>'">
                <i class="icon-profile" style="width:25px;"></i><br>充值
            </li>
            <li onclick="location.href='money_favorable.php?openid=<?php echo $_REQUEST['openid'];?>'">
                <i class="icon-invite" style="width:26px;"></i><br>优惠
            </li>
        </ul>
    </menu>

</div>
</body>
<script type="text/javascript" src="./money_js/zepto.js"></script>
<script type="text/javascript" src="./money_js/frozen.js"></script>
<script type="text/javascript" src="./money_js/jquery-1.9.1.min.js"></script>
<script src='./money_js/jsweixin1.0.js'></script>
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
        var reg = {
            phone:/(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)|(^0{0,1}1[0|1|2|3|4|5|6|7|8|9][0-9]{9}$)/
        };
        $('.btn-all-update').tap(function(){
            var phone = $('.phone').val();
            var appid = $('.appid').val();
            var name = $('.name').val();
            var address = $('.address').val();
            //var total = $('.total').val();
            var remarks = $('.remarks').val();
            var openid = $('.openid').val();
            var device_group_id = $('#device_group_id').val();
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
                content: '修改中.'
            });
            var DATA = {
                appid:appid,
                name:name,
                address:address,
                phone:phone,
                remarks:remarks,
                openid:openid,
                device_group_id:pids
            };
            $.post("money_query_device_group.php?action=mer_update",DATA, function (reg) {
                if (reg.msg == 1) {
                    var DG = $.dialog({
                        content: '修改成功',
                        button: ['ok']
                    });
                    DG.on('dialog:action',function(e){
                        document.location.href="money_recharge.php?openid=<?php echo $_REQUEST['openid'];?>";
                    });
                }else{
                    $.dialog({
                        content: '修改失败',
                        button: ['ok']
                    });
                }
                el.hide();
            }, 'json');
        })
    });
    var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }
</script>
</html>