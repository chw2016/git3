(function () { 
   var params = {}; 
   
   //Document对象数据 
   if(document) {
	   params.domain = document.domain || '';
	   params.url = document.URL || '';
	   params.title = document.title || '';
	   params.referrer = document.referrer || '';
   }

   //navigator对象数据 
   if(navigator) { 
		params.browser = navigator.appName || '';
		params.bversion = parseFloat(navigator.appVersion) || '';
		params.platform = navigator.platform || '';
		params.useragent = navigator.userAgent || '';
		params.iscookie = navigator.cookieEnabled || '';
   }
   
   //获取当前页面
   if(window){
	   var strUrl=window.location.href;
		strUrl=strUrl.split("/");
		var cpage=strUrl[strUrl.length-1];
		if(cpage.indexOf("?") > -1){
			strUrl=cpage.split("?");
			cpage=strUrl[0];
		}
		params.pagename = cpage;
	}
	
   //解析_maq配置
   if(_maq) {
	   for(var i in _maq) {
		   switch(_maq[i][0]) {
				case '_setAccount': params.account = _maq[i][1];
				break;
				case '_areaid': params.areaid = _maq[i][1] || 0;
				break;
				case '_typeid': params.typeid = _maq[i][1] || 0;
				break;
				case '_uid': params.uid = _maq[i][1] || 0;
				break;
				case '_regtime': params.regtime = _maq[i][1] || 0;
				break;
			   default:
				break;
		   }
	   }
   }
   
   //拼接参数串 
   var args = ''; 
   for(var i in params) { 
	   if(args != '') { 
			args += '&';
	   }
		args += i + '=' + encodeURIComponent(params[i]);
   }
 
   //通过Image对象请求后端脚本
   window.onload=function(){
		var img = new Image(1, 1);
		img.src = 'http://log.autoeo.com/1.gif?' + args;
   }

   //获取坐标 
	document.onclick=function(){
		var param = {};
		e = arguments[0] || window.event;
		param.scanx = e.clientX;
		param.scany = e.clientY;
	    var arg = 'scanx=' + encodeURIComponent(e.clientX) +'&scany=' + encodeURIComponent(e.clientY);
	   var img = new Image(1, 1);
		img.src = 'http://log.autoeo.com/1.gif?' + args + '&' + arg;
	}
	
})();