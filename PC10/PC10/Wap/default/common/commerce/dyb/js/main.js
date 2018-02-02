/* 
* @Author: 星辉
* @Date:   2015-02-06 10:23:20
* @Last Modified by:   星辉
* @Last Modified time: 2015-03-18 14:18:37
*/

"use strict"
var show_error_tip = function(text,time)
{
    var imgSiteUrl   = 'http://i.16888.com/4/default/';
    if (typeof(time) == "undefined") {
        time = 1000;
    }
    //var top = ($(document).height()-200)/2;
    var wh = $(window).height();
    var top = (wh -48)/2+$(document).scrollTop();
    var okImg = imgSiteUrl+'pop_poor.png';
    var tip = '<div id="tips" class="tipsBox" style="z-index:100000;position: absolute;top:'+top+'px;"><span><img src='+okImg+' width="39" height="39" /></span><p>'+text+'</p></div>';
    $('body').append(tip);

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
	
	if(time>0){
		setTimeout(function(){
				$("#tips").remove();
			}, time);
	}
	return false;
}