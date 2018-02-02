if (typeof SCRM == "undefined") var SCRM = {initTime:(new Date()).valueOf(),intervalTime:300};
//获取对象
var $$=function(id){
	return document.getElementById(id);
};

$(function(){
	var recordTelLoading = 0;
	$('a[ahref^="tel:"]').live('click',function(){
		var aaa = $(this);
		
		if(recordTelLoading)
		{
			window.location.href = aaa.attr('ahref');
			return false;
		}
		
		recordTelLoading = 1;
		
		postData = $(this).data('teldata');
		if( typeof postData == 'undefined' )
		{
			window.location.href = aaa.attr('ahref');
			return false;
		}
		else
		{
			postData.location = window.location.href;
		}
		
		$.post(
			'ajax.php?mod=user&code=recordTelData',
			postData,
			function(d)
			{
				//recordTelLoading = 0;
				window.location.href = aaa.attr('ahref');
			},
			'json'
		);
	});
	
	$('#Pop_tel_box').live('click',function(){
		$(this).remove();
	});
	
});

//提示错误消息
var show_tip=function(text){
	var tip = '<div id="msgtip" class="msgtip" >'+text+'</div>';
	$('body').append(tip);
	$('#msgtip').animate({'top':0}, 200, function(){
		setTimeout(function(){
			$('#msgtip').animate({'height':'0'}, 300, function(){
				$(this).remove();
			});
		}, 1000);
	});
	return false;
};

var show_success_tip = function(text,time)
{
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

var show_confirm_tip = function(text){
    if (typeof(text) == "undefined" || text=="") { 
		text = "确定删除吗？"; 
    }
    if($("#confirm_tips").length==0){
        arr = [];
        arr.push("<div id=\"confirm_tips\" class=\"popDeleteBox\">");
        arr.push("    <div class=\"background\"></div>");
        arr.push("    <div class=\"popDelete clearfix\">");
        arr.push("        <h3>"+ text +"</h3>");
        arr.push("        <ul>");
        arr.push("            <li><a tag=1 href=\"#\">确定</a></li>");
        arr.push("            <li><a onclick=\"$('#confirm_tips').remove();\" href=\"javascript:;\">取消</a></li>");
        arr.push("        </ul>");
        arr.push("    </div>");
        arr.push("</div>");
        var htmlContent =arr.join("\n");
        $("body").append(htmlContent);
        loadbombBox();
    }else{
        $("#confirm_tips").remove();
    }
    return false;
}

//样式 div等做出修改，showAlert方法做出对应的的修改 @zhangyaqi 20140301
var showAlert2 = function(title, content, options){

	var layers = '<div id="Pop_tel_box" class="popDeleteBox" >';
		layers += '    <div class="background"></div>';
		layers += '    <div class="popDelete clearfix" >';
		layers += '       <h3>'+content+'</h3>';
		layers += '        <ul>';
		if(!options || (options.sureFun && !options.cancelFun)){
			layers += '            <li><a href="javascript:;" onclick="closeAlert();" >取消</a></li>';
			layers += '            <li><a href="javascript:void(0);" tag=1 onclick="'+(options ? options.sureFun : 'closeAlert')+'();">确定</a></li>';			
		}else if(options.sureFun && options.cancelFun){			
			layers += '            <li><a href="javascript:void(0);" onclick="'+options.cancelFun+'();" class="gray">'+(options.cancelTitle ? options.cancelTitle:'重设')+'</a></li>';
			layers += '            <li><a href="javascript:void(0);" tag=1 onclick="'+options.sureFun+'(); ">'+(options.sureTitle ? options.sureTitle:'确定')+'</a></li>';
		}
		layers += '      </ul>';
		layers += '    </div>';
		layers += '</div>';
		
        $("body").append(layers);
        
		loadbombBox();
		return false;		
};

var show_error_tip = function(text,time)
{
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
var show_load_tip = function(text){
	if (typeof(text) == "undefined") { 
		text = '加载中，请稍候...';
	}
	if($('#show_load_tip').size()>0) return false;
	var wh = $(window).height();
	var top = (wh -48)/2+$(document).scrollTop();
	var okImg = imgSiteUrl+'load.gif';
	var tip = '<div id="show_load_tip" class="tipsBox" style="z-index:100000;position: absolute;top:'+top+'px;"><span><img src='+okImg+' width="39" height="39" /></span><p>'+text+'</p></div>';
	$('body').append(tip);
	
	return false;
};
var show_load_suc_tip = function(){
	$("#show_load_tip").remove();
};

//验证是否为正数
var checkNum = function(a)
{
	var patrn=/^\d+(.\d+)?$/; 
	if (!patrn.exec(a)) return false;
	return true;
};

//验证姓名
var checkName = function(a)
{
	var patrn = /^\s*[\u4e00-\u9fa5]{1,10}[.·]{0,1}[\u4e00-\u9fa5]{1,10}\s*$/; 
	if (!patrn.exec(a)) return false;
	return true;
};

//验证手机号码
var checkPhone = function(a)
{
	var patrn = /^((?:13|15|18)\d{9}|0(?:10|2\d|[3-9]\d{2})[1-9]\d{6,7})$/;
	if (!patrn.exec(a)) return false;
	return true;
};

 //验证车牌号码
var checkCp = function(a)
{
	var rtn = false;
	var patrns = ['//^[\u4e00-\u9fa5]{1}[a-zA-Z]{1}([a-zA-Z0-9]{5}|[a-zA-Z0-9]{4}[\u4e00-\u9fa5]{1})$//', '//^[a-zA-Z]{2}[a-zA-Z0-9]{5}$//', '//^使[a-zA-Z0-9]{6}$//', '//^WJ[\u4e00-\u9fa5]{1}[a-zA-Z0-9]{5}$//', '//^WJ[0-9]{2}[a-zA-Z0-9]{5}$//', '//^WJ[0-9]{2}(消|边|通|森|金|警|电)[a-zA-Z0-9]{4}$//'];
	
	for(var i = 0;i < patrns.length; i++){
		var patrn = patrns[i];
		patrn = patrn.replace(/\/\//g,"\/");
		var re = eval(patrn);//转成正则
		if (re.exec(a))
		{    
			rtn =  true;
			break;
		}
	} 
	return rtn;
};

//验证车架号
var checkVIN = function(a)
{
	var patrn = /^[a-zA-Z0-9]{6,20}$/
	if (!patrn.exec(a)) return false;
	return true;
}

//验证字母加数字
var checkAbc = function(a)
{
	var patrn=/^[a-zA-Z0-9]+$/; 
	if (!patrn.exec(a)) return false;
	return true;	
}

//验证发动机号
var checkEngineNO = function(a)
{
	var patrn = /^[-a-zA-Z0-9]{4,20}$/
	if (!patrn.exec(a)) return false;
	return true;
}

//提示框
var showAlert = function(title, content, options){
	var top = ($(document).height()-200)/2;
	var layers = '<div id="Pop_tel_box" style="background:url('+img_site_url+'/default/bank_back.png);" class="layers">';
		layers += '<div class="Pop_tel_box"><div class="Pop_tel_con" style="margin-top:'+top+'px;">';
		layers += '<h1>'+title+'</h1>';
		layers += '<h2>'+content+'</h2>';
		if(!options || (options.sureFun && !options.cancelFun)){
			layers += '<div style="text-align:center; margin:0 auto 10px;overflow:hidden;width:145px;">';
			layers += '<div class="submit_box_s">';
			layers += '<a href="javascript:void(0);" onclick="'+(!options?'closeAlert':options.sureFun)+'();"><font class="btn D_F_Gray">确定</font>';
			layers += '<img src="'+img_site_url+'/default/gray_back_s.png"></a></div>';	
		}else if(options.sureFun && options.cancelFun){
			layers += '<div style="text-align:center; margin:0 auto 10px;overflow:hidden;width:250px;">';
			layers += '<div class="submit_box_ss f_l">';
			layers += '<a href="javascript:void(0);" onclick="'+options.cancelFun+'();">';
			layers += '<font class="btn">'+(options.cancelTitle ? options.cancelTitle:'重设')+'</font>';
			layers += '<img src="'+img_site_url+'/default/submit_back_ss.png"></a></div>';
			layers += '<div class="submit_box_ss f_r">';
			layers += '<a href="javascript:void(0);" onclick="'+options.sureFun+'();">';
			layers += '<font class="btn D_F_Gray">'+(options.sureTitle ? options.sureTitle:'确定')+'</font>';
			layers += '<img src="'+img_site_url+'/default/gray_back_ss.png"></a></div>';
		}
		layers += '</div></div></div></div>';
	$('body').append(layers);
};

//关闭提示框
var closeAlert=function(){
	$('#Pop_tel_box').remove();
};

/**
 * 得到ajax对象
 */
var getajaxHttp=function () {
    var xmlHttp;
    try {
        // Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
        } catch (e) {
            // Internet Explorer
            try {
                xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                alert("您的浏览器不支持AJAX！");
                return false;
            }
        }
    }
    return xmlHttp;
};
/**
 * 发送ajax请求
 * url--url
 * methodtype(post/get)s
 * con (true(异步)|false(同步))
 * parameter(参数)
 * functionName(回调方法名，不需要引号,这里只有成功的时候才调用)
 * (注意：这方法有二个参数，一个就是xmlhttp,一个就是要处理的对象)
 * obj需要到回调方法中处理的对象
 */
var ajaxrequest=function(url,methodtype,con,parameter,functionName,obj){
    var xmlhttp=getajaxHttp();
    xmlhttp.onreadystatechange=function(){
        if(xmlhttp.readyState==4){
            //HTTP响应已经完全接收才调用
            functionName(xmlhttp,obj);
        }
    };
    xmlhttp.open(methodtype,url,con);
    xmlhttp.send(parameter);
};

/**显示信息模块的方向
 * @author zhangyaqi
 * @date 2014-01-10
 * @param content 插入的内容  如果content的值为display,代表div已经存在，直接显示动作
 * @param direction 弹出动作的目标方向：top、bottom、left、right四个值
 * @param back_div(jQery对象选择器） 为指定模块添加  “弹出动作的返回效果”
 */
var global_show_info = function(content,direction,back_div,other_function)
{
	switch(direction){
		case 'top':
			var h = $(document).height();
			var style_t_b_l_r = 'left:0px; top:'+h+'px; width:100%; min-height:'+h+'px;';
			var animate_val = h;
			break;
		case 'bottom':
			var h = $(document).height();
			var style_t_b_l_r = 'left:0px; bottom:'+h+'px; width:100%; min-height:'+h+'px;';
			var animate_val = h;
			break;
		case 'left':
			var w = $(document).width();
			var h = $(document).height();
			var style_t_b_l_r = 'left:'+w+'px; top:0px; width:100%; min-height:'+h+'px;';
			var animate_val = w;
			break;
		case 'right':
			var w = $(document).width();
			var h = $(document).height();
			var style_t_b_l_r = 'right:'+w+'px; top:0px; width:100%; height:auto;';
			var animate_val = w;
			break;
		default:
			direction = 'top';
			var h = $(document).height();
			var style_t_b_l_r = 'left:0px; top:'+h+'px; width:100%; min-height:'+h+'px;';
			var animate_val = h;
			break;
	}
	
	if(content=='display'){
		$('#global_show_info').show();
	}
	else{
		var sendMsgBody = '<div id="global_show_info" style="z-index:999; position:absolute; '+style_t_b_l_r+' overflow:hidden; background-color:#fff;">'+content+'</div>';
		$('body').append(sendMsgBody);
		if(other_function)
		{
			var back_function = 'global_close_show_info("global_show_info","'+direction+'","'+animate_val+'","'+other_function+'");';
		}
		else
		{
			var back_function = 'global_close_show_info("global_show_info","'+direction+'","'+animate_val+'");';
		}
		$(back_div).attr('onclick',back_function);

		global_loadbombBox();//调用loading的效果
	}
	
	var animate_date = {};
	animate_date[direction]=0;
	$('#global_show_info').animate(animate_date, 200);
	
}

/**关闭已经显示的信息模块
 * @author zhangyaqi
 * @date 2014-01-10
 * @param div_id 要关闭的div模块的id
 * @param animate_key jqury中animate的方向
 * @param animate_val animate_key对应的值
 */
var global_close_show_info = function(div_id,animate_key,animate_val,other_function)
{
	var animate_date = {};
	animate_date[animate_key]= animate_val+'px';
	
	$('#'+div_id).animate(animate_date, 200, function(){
		$('#'+div_id).hide();
	});
	if(other_function)
	{
		eval(other_function+'()');
	}
}


//重载loading图片的样式（缩放）
function global_loadbombBox(){
	//request data for centering
	var global_windowWidth = document.documentElement.clientWidth;
	var global_windowHeight = document.documentElement.clientHeight;
	var global_bombBoxHeight = $("#global_loadbombBox").height();
	var global_bombBoxWidth = $("#global_loadbombBox").width();

	$("#global_loadbombBox").css({
		"top": (global_windowHeight - global_bombBoxHeight)/2,
		"left": (global_windowWidth - global_bombBoxWidth)/2
	});
}
/*
window.onload = function() {  
	global_loadbombBox();
}
window.onresize = function() {
	global_loadbombBox();
}*/

var loadbombBox = function(){
	//request data for centering
	var windowHeight = document.documentElement.clientHeight;
	var bombBoxHeight = $(".popDelete").height();
	$(".popDelete").css({
		"top": (windowHeight - bombBoxHeight)/2
	});
	$(".background").click(function(){
            if($("#confirm_tips").length>0){
                $("#confirm_tips").remove();
            }
        });
	$('.background').css('height',windowHeight+'px');
}

window.onload = window.onresize = function() {  
	loadbombBox();
}

var Request = {    
    QueryString: function(a) {        
        var b = location.search.match(new RegExp("[?&]" + a + "=([^&]*)(&?)", "i")); 
        return b ? b[1] : b    
    },
    GetTag:function(){
        return location.hash.substr(1);
    }
}

$(document).ready(function(){
    (function ($, document, undefined) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return unRfc2068(decodeURIComponent(s.replace(pluses, ' ')));
	}

	function unRfc2068(value) {
		if (value.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape
			value = value.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}
		return value;
	}

	function fromJSON(value) {
		return config.json ? JSON.parse(value) : value;
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (value === null) {
				options.expires = -1;
			}

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		var result = key ? null : {};
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = decode(parts.join('='));

			if (key && key === name) {
				result = fromJSON(cookie);
				break;
			}

			if (!key) {
				result[name] = fromJSON(cookie);
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== null) {
			$.cookie(key, null, options);
			return true;
		}
		return false;
	};

})(jQuery, document);

}
);