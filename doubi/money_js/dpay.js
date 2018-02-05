function jsApiCall(jsApiParameters)
{
    var jsPs = eval('(' + jsApiParameters + ')');
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        jsPs ,
        function(res){
            alert(jsPs.package);
            if(res.err_msg == "get_brand_wcpay_request:ok" ){
                $.ajax({
                    type: 'POST',
                    url: 'money_dpay_jsApiParameters.php?action=weixin_update',
                    data: {"openid":$('.openid').val(),"money":$('.money').val(),"merchant_id":$('.merchant_id').val(),"prepay_id":jsPs.package,"out_trade_no":$('.out_trade_no').val()},
                    dataType: 'text',
                    async:false,
                    success: function(data){
                        alert('支付成功');
                    },
                    error: function(xhr, type){
                        alert('支付异常，请重新支付');
                    }
                });
            }
        }
    );
}
function callpay()
{
    if($('.payup').hasClass('img')){
        return false;
    }
    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    }else{
        var jsApiParameters =  null;
        var money = $('.money').val();
        var openid = $('.openid').val();
        $('.payup').html('支付中...').addClass('img');
        $.ajax({
            type: 'POST',
            url: 'money_dpay_jsApiParameters.php?action=weixin_pay',
            data: {"money":money,"openid":openid,"merchant_id":$('.merchant_id').val()},
            dataType: 'text',
            timeout: 3000,
            async:false,
            success: function(data){
                if(0 != data){
                    var datas = delete data.out_trade_no;
                    jsApiParameters = data;
                }
                $('.out_trade_no').val(JSON.parse(data).out_trade_no);
                $('.payup').html('预存送逗币').removeClass('img');
            },
            error: function(xhr, type){
                alert('支付异常，请重新支付');
                $('.payup').html('预存送逗币').removeClass('img');
            }
        });
        if(jsApiParameters != null){
            jsApiCall(jsApiParameters);
        }
    }
}