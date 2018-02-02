

"use strict"

//css ��ʽд��js����
function setCss(){
    $(".tipsBox,.tipsBoxOk").css({
        '-index': '999999',
        'width': '120px',
        'height': '100px',
        'background': '#000',
        opacity: '0.6',
        left: '50%',
        top: '50%',
        margin: '-90px 0 0 -60px',
        'border-radius': '5px',
        position: 'fixed'
    });
    $(".tipsBox span,.tipsBoxOk span").css({
        display: 'block',
        width: '39px',
        height: '39px',
        margin: '23px auto 12px'
    });
    $(".tipsBox p,.tipsBoxOk p").css({
        color: '#fff',
        'font-size': '12px',
        'text-align': 'center'
    });
}
var show_error_tip = function(text,time)
    {
        var imgSiteUrl   = 'http://i.16888.com/4/default/';
       /* $('a').css({
            'border-radius':'',

        })*/
        if (typeof(time) == "undefined") {
        time = 1000;
        }
        //var top = ($(document).height()-200)/2;
        var wh = $(window).height();
        var top = (wh -48)/2+$(document).scrollTop();
        var okImg = imgSiteUrl+'pop_poor.png';
        var tip = '<div id="tips" class="tipsBox" style="z-index:100000;position: absolute;top:'+top+'px;"><span><img src='+okImg+' width="39" height="39" /></span><p>'+text+'</p></div>';
        $('body').append(tip);
        setCss();//�ص�
        if(time>0){
        setTimeout(function(){
        $("#tips").remove();
        }, time);
        }
        return false;
        };
var show_success_tip = function(text,time)
    {
        var imgSiteUrl   = 'http://i.16888.com/4/default/';
        if (typeof(time) == "undefined") {
        time = 1000;
        }

        //var top = ($(document).height()-200)/2;
        var wh = $(window).height();
        var top = (wh -48)/2+$(document).scrollTop();
        var okImg = imgSiteUrl+'pop_tick.png';
        var tip = '<div id="tips" class="tipsBoxOk" style="z-index:100000;position: absolute;top:'+top+'px;"><span><img src='+okImg+' width="39" height="39" /></span><p>'+text+'</p></div>';
        $('body').append(tip);
        setCss();//�ص�
        if(time>0){
        setTimeout(function(){
        $("#tips").remove();
        }, time);
        }
        return false;
        }
