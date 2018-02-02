/*
 =============================================================
 V1.36 2016/04/22 12:12
 正式版

 V1.37 2016/04/22 16:22
 更新UrlClass的参数设定与删除对页面后缀的添加与参数

 V1.38 2016/04/22 17:45
 更新注释

 V1.52 2016/04/23 12:22
 1、修复UrlClass设定自定义Url操作时的bug
 2、更新部分注释
 3、传参严格化
 4、_StateObject对应的操作方法更名

 V1.53 2016/04/23 15:45
 修复传参判断逻辑bug

 V1.54 2016/04/23 17:24
 1、将CookieClass中的addCookie方法改为setCookie
 2、修改CookieClass中的getCookie方法的返回值

 V1.80 2016/05/28 11:05
 1、合并PageScrollClass和PageWidthHeight，统一为PageClass
 2、对页面位置获取和设定做了浏览器兼容处理
 3、新增获取页面宽高的方法，并重命名部分方法
 4、修正bug

 V1.90 2016/05/28 11:40
 1、新增生成GUID码的方法
 2、使用通用获取随机位置的算法

 V2.00 2016/06/02 14:05
 扩展Date对象，增加format函数来格式化时间字符串

 V2.01 2016/06/09 10:22
 更新注释

 V2.03 2016/06/21 11:34
 1、修复UrlClass处理默认Url调用相关函数无法执行的问题
 2、修复已有参数情况下跳转不会携带后缀的情况
 =============================================================
 */
//noinspection JSUnusedGlobalSymbols
/**
 * 自定义通用JS工具
 * *若需要用到UrlClass并且URL模式为PATHINFO的话，可以在载入该js文件的标签内写入data-mvc-name的属性键入需要排除的关键值
 *
 * @author Quasar
 * @version 2.03
 *
 * Updated on 2016/06/21 11:34
 * Created on 2016/01/09 10:00
 */
var Quasar = {
	/**
	 * 构造函数
	 * 根据页面标签属性用于初始化_Config
	 * @private
	 */
	_init              :function(){
		var script = document.getElementById('Quasar');
		Quasar._Config.MVC_Name = script.getAttribute('data-mvc-name');
		Quasar._Config.Page_Suffix = script.getAttribute('data-page-suffix');
	},
	/**
	 * 页面URL配置参数
	 * @private
	 */
	_Config            :{
		MVC_Name   :null,
		Page_Suffix:null
	},
	/**
	 * 错误查询参数
	 * @private
	 */
	_Error             :{
		status:true,
		code  :0,
		info  :'',
		from  :'',
		data  :null
	},
	/**
	 * 设定错误信息
	 *
	 * @param status 成功失败状态，true/false
	 * @param code 错误码
	 * @param info 提示信息
	 * @param from 错误来源
	 * @param [data] 附加数据信息
	 *
	 * @returns boolean
	 * @private
	 */
	_setError          :function(status, code, info, from, data){
		if(typeof arguments[0] == 'undefined' || typeof arguments[1] == 'undefined' || typeof arguments[2] == 'undefined' || typeof arguments[3] == 'undefined') return false;
		Quasar._Error.status = arguments[0];
		Quasar._Error.code = arguments[1];
		Quasar._Error.info = arguments[2];
		Quasar._Error.from = arguments[3];
		if(arguments[4]) Quasar._Error.data = arguments[4];
		else Quasar._Error.data = null;
		return true;
	},
	/**
	 * 浏览器信息
	 * @type {{Name: null|string, Version: null|string, Engine: null|string, Platform: null|string}}
	 * @constructor
	 */
	BrowserCheckerClass:function(){
		this.BrowserInfo = {
			Name:null, Version:null, Engine:null, Platform:null
		};
		/**
		 * 已支持的浏览器列表
		 * @type {{Name: {_IE: string, _FF: string, _GC: string, _OPR: string, _MX: string, _360: string, _360C: string, _AS: string}, Engine: {_We: string, _Tr: string, _Ge: string, _Pr: string, _Bl: string}}}
		 * @private
		 */
		var _knonw_type_ = {
			Name     :{
				_IE  :'Internet Explorer',
				_FF  :'Firefox',
				_GC  :'Chrome',
				_OPR :'Opera',
				_MX  :'Maxthon',
				_360 :'360安全浏览器',
				_360C:'360极速浏览器',
				_AS  :'Safari'
			}, Engine:{
				_We:'Webkit', _Tr:'Trident', _Ge:'Gecko', _Pr:'Presto', _Bl:'Blink'
			}
		};

		/**
		 * 初始化浏览器信息
		 * @param obj 类对象
		 *
		 * @returns {boolean}
		 */
		function init(obj){
			var useragent = navigator.userAgent.toLowerCase();
			var browserinfo;
			if((browserinfo = useragent.match(/firefox\/[\d.]+/)) && (browserinfo = browserinfo[0])){
				obj.BrowserInfo.Name = _knonw_type_.Name._FF;
				obj.BrowserInfo.Version = browserinfo.substr(8, browserinfo.length);
				obj.BrowserInfo.Engine = _knonw_type_.Engine._Ge;
				obj.BrowserInfo.Platform = getThePlatform(useragent);
				return true;
			}
			if((browserinfo = useragent.match(/opr\/[\d.]+/)) && (browserinfo = browserinfo[0])){
				obj.BrowserInfo.Name = _knonw_type_.Name._OPR;
				obj.BrowserInfo.Version = browserinfo.substr(4, browserinfo.length);
				obj.BrowserInfo.Engine = _knonw_type_.Engine._We;
				obj.BrowserInfo.Platform = getThePlatform(useragent);
				return true;
			}
			if((browserinfo = useragent.match(/maxthon\/[\d.]+/)) && (browserinfo = browserinfo[0])){
				obj.BrowserInfo.Name = _knonw_type_.Name._MX;
				obj.BrowserInfo.Version = browserinfo.substr(8, browserinfo.length);
				obj.BrowserInfo.Engine = _knonw_type_.Engine._We;
				obj.BrowserInfo.Platform = getThePlatform(useragent);
				return true;
			}
			if((browserinfo = useragent.match(/rv:[\d.]+/)) && (useragent.match(/trident\/[\d.]+/)) && (browserinfo = browserinfo[0])){
				obj.BrowserInfo.Name = _knonw_type_.Name._IE;
				obj.BrowserInfo.Version = browserinfo.substr(3, browserinfo.length);
				obj.BrowserInfo.Engine = _knonw_type_.Engine._Tr;
				obj.BrowserInfo.Platform = getThePlatform(useragent);
				return true;
			}
			if((browserinfo = useragent.match(/msie [\d.]+/)) && (browserinfo = browserinfo[0])){
				obj.BrowserInfo.Name = _knonw_type_.Name._IE;
				obj.BrowserInfo.Version = browserinfo.substr(5, browserinfo.length);
				obj.BrowserInfo.Engine = _knonw_type_.Engine._Tr;
				obj.BrowserInfo.Platform = getThePlatform(useragent);
				return true;
			}
			if((useragent.match(/360se/))){
				obj.BrowserInfo.Name = _knonw_type_.Name._360;
				obj.BrowserInfo.Version = "Unknown";
				obj.BrowserInfo.Engine = _knonw_type_.Engine._We;
				obj.BrowserInfo.Platform = getThePlatform(useragent);
				return true;
			}
			if((browserinfo = useragent.match(/chrome\/[\d.]+/)) && (browserinfo = browserinfo[0])){
				obj.BrowserInfo.Name = _knonw_type_.Name._GC;
				obj.BrowserInfo.Version = browserinfo.substr(7, browserinfo.length);
				obj.BrowserInfo.Engine = _knonw_type_.Engine._We;
				obj.BrowserInfo.Platform = getThePlatform(useragent);
				return true;
			}
			if((browserinfo = useragent.match(/mac[\s\S]*safari\/[\d.]+/))){
				browserinfo = useragent.match(/safari\/[\d.]+/)[0];
				obj.BrowserInfo.Name = _knonw_type_.Name._AS;
				obj.BrowserInfo.Version = browserinfo.substr(7, browserinfo.length);
				obj.BrowserInfo.Engine = _knonw_type_.Engine._We;
				obj.BrowserInfo.Platform = getThePlatform(useragent);
				return true;
			}
		}

		/**
		 * 获取运行环境类型
		 * @param useragent UserAgent字符串
		 *
		 * @returns string
		 */
		function getThePlatform(useragent){
			if(useragent.match(/mobile|iphone/)) return 'Mobile: iOS';
			if(useragent.match(/mobile|android/)) return 'Mobile: Android';
			return 'Computer Browser';
		}

		init(this);
	},
	/**
	 * Cookie处理类
	 * @constructor
	 */
	CookieClass        :function(){
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 设定Cookie
		 * @param name 添加Cookie的名称
		 * @param value 添加Cookie的值
		 * @param expiresHours 添加Cookie的过期时间(单位：小时)
		 */
		this.setCookie = function(name, value, expiresHours){
			var cookieString = name+"="+encodeURI(value);
			// 判断是否设置过期时间
			if(expiresHours>0){
				var date = new Date();
				date.setTime(date.getTime()+expiresHours*3600*1000);
				cookieString = cookieString+"; expires="+date.toUTCString();
			}
			document.cookie = cookieString;
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 获取Cookie
		 * @param name 获取Cookie的名称
		 *
		 * @returns string|null 返回Cookie的值，无对应name的Cookie则返回null
		 */
		this.getCookie = function(name){
			var strCookie = document.cookie;
			var arrCookie = strCookie.split("; ");
			for(var i = 0; i<arrCookie.length; i++){
				var arr = arrCookie[i].split("=");
				if(arr[0] == name) return decodeURI(arr[1]);
			}
			return null;
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 删除Cookie
		 * @param name 删除Cookie的名称
		 */
		this.delCookie = function(name){
			var date = new Date();
			date.setTime(date.getTime()-10000);
			document.cookie = name+"=''; expires="+date.toUTCString();
		};
	},
	/**
	 * 数据处理类
	 * @constructor
	 */
	DatasCheckerClass  :function(){
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 判断数据是否符合格式要求
		 * 支持邮箱地址、手机号码判断
		 * @param type 验证数据类型(e-mail, c-tel)
		 * @param str 待检测的数据
		 *
		 * @returns {boolean}    返回结果为true则数据格式合法，为false则数据格式不合法
		 */
		this.isRightFormat = function(type, str){
			var pattern = null;
			switch(type.toLowerCase()){
				case 'e-mail':
					pattern = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
					break;
				case 'c-tel':
					pattern = /(^13|^15|^14|^18)\d{9,}/g;
					break;
				default :
					return false;
					break;
			}
			return !!pattern.test(str);
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 判断数据是否为空
		 * null, undefined, '', ""均视为空
		 * NaN, 0则不为空
		 * @param data 待检测的数据
		 *
		 * @returns {number}    返回结果1则data数据为空，0则data数据不为空
		 */
		this.isEmpty = function(data){
			var result;
			if('number' == typeof data){
				if(isNaN(data)) result = true;
			}
			else{
				switch(data){
					case 0:
						result = false;
						break;
					case null:
					case undefined:
					case '':
					case "":
						result = true;
						break;
					default:
						result = false;
				}
			}
			return result ? 1 : 0;
		};
	},
	/**
	 * Url处理类
	 *
	 *     * *附录1
	 * 全局返回码
	 *    0：操作成功
	 *   11：函数缺少必要参数
	 *   12：参数类型错误
	 * 1001：未知的URL格式
	 * 1002：不能获取URL的域名
	 * 1003：不能获取URL的端口号
	 * 1004：不能获取URL的协议
	 * 1005：不能获取URL的目录路径和参数串
	 * 2001：不满足URL格式
	 * 3001：URL不存在参数
	 * 3002：指定的Key在URL中不存在
	 * 3999：没有任何操作
	 * 4002：_StateObject元素不存在
	 * 4003：_StateObject元素不允许删除
	 * 5001：非法的JS对象
	 * 5002：无法访问JS对象属性
	 * 5003：JS对象存在对象迭代
	 * 9991：缺少排除参数
	 * 9992：排除参数格式错误
	 * 9993：缺少页面后缀
	 * 9994：页面后缀格式错误
	 *
	 * @param mode URL模式，0表示普通模式，1表示PATHINFO模式
	 * @param except except只在PATHINFO模式下生效，表示排除的参数索引（用于框架排除特定名称），格式为"/param1/param2/..."
	 * @param suffix 页面文件后缀
	 * @returns {undefined|Object}
	 * @constructor
	 */
	UrlClass           :function(mode, except, suffix){
		/**
		 * 类的基本配置信息
		 *
		 * @type {{mode: number, except: string|null, suffix: string}}
		 * @private
		 */
		var _Config = {
			mode  :0,
			except:null,
			suffix:''
		};
		/**
		 * 页面配置信息
		 * 主要用于浏览器后退前进触发的onpopstate事件，该事件会获取pushState传递过去的第一个参数
		 * @type {{url: string, title: string}}
		 */
		var _StateObject = {
			url  :location.href.toString(),
			title:document.title.toString()
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 为_StateObject添加元素
		 * @param key 元素的key值
		 * @param val 需要添加元素的值
		 *
		 * @returns boolean 若操作成功status为true，否则为false
		 */
		this.setStateObjectParam = function(key, val){
			if(arguments.length<2){
				Quasar._setError(false, 11, '函数缺少必要参数', 'UrlClass/setStateObjectParam()');
				return false;
			}
			if(typeof key == 'number' || typeof key == 'string'){
				_StateObject[key] = val;
				Quasar._setError(true, 0, '为_StateObject添加元素操作成功', 'UrlClass/setStateObjectParam()', key+'='+val);
				return true;
			}else{
				Quasar._setError(false, 12, '参数类型错误', 'UrlClass/setStateObjectParam()');
				return false;
			}
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 为_StateObject删除元素
		 * @param key 元素的key值
		 *
		 * @returns boolean 若操作成功status为true，否则为false
		 */
		this.delStateObjectParam = function(key){
			if(!this.isTheStateObjectParamExist(key)){
				Quasar._setError(false, 4002, '_StateObject元素不存在', 'UrlClass/delStateObjectParam()', key);
				return false;
			}
			if(key.toLowerCase().toString() == 'title' || key.toLowerCase().toString() == 'url'){
				Quasar._setError(false, 4003, '_StateObject元素不允许删除', 'UrlClass/delStateObjectParam()', key);
				return false;
			}else{
				Quasar._setError(true, 0, '为_StateObject删除元素操作成功', 'UrlClass/delStateObjectParam()', key);
				delete _StateObject[key];
				return true;
			}
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 获取_StateObject
		 * @returns {{url: string, title: string}}
		 */
		this.getStateObject = function(){
			return _StateObject;
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 判断_StateObject的key值是否存在
		 * @param key 元素的key值
		 *
		 * @returns {boolean} 若存在则返回true，否则返回false
		 */
		this.isTheStateObjectParamExist = function(key){
			return !!_StateObject[key];
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 重置_StateObject的url值为当前页面的url
		 */
		this.resetStateObject = function(){
			_StateObject.url = location.href.toString();
			_StateObject.title = document.title;
		};
		/**
		 * 获取URL参数且以json对象形式{"params":{"key": "value"}, "length": length}返回
		 * @param url 传入的URL字符串，默认值为当前页面的URL
		 * @param [opt] 若传入自定义URL且当前URL模式不为0，则必须传入该值指定需要排除的关键字串和页面后缀，格式为{except:"/param1/param2/...", suffix:".html"}
		 *
		 * @returns null|Object 返回URL的参数值JSON数据，若无参数则返回NULL，有则返回参数的json对象
		 */
		this.getUrlParamsJSON = function(url, opt){
			if(arguments.length<1) url = _StateObject.url.toString();
			else{
				if(arguments.length == 1 && _Config.mode != 0){
					Quasar._setError(false, 11, '函数缺少必要参数', 'UrlClass/getUrlParamsJSON()');
					return null;
				}else if(!opt.except || !opt.suffix){
					Quasar._setError(false, 12, '参数类型错误', 'UrlClass/getUrlParamsJSON()');
					return null;
				}
			}
			var result;
			if(_Config.mode == 0){
				//noinspection JSDuplicatedDeclaration
				var i = url.indexOf('?');
				if(i == -1){
					Quasar._setError(false, 3001, 'URL不存在参数', 'UrlClass/getUrlParamsJSON()');
					return null;
				}
				//noinspection JSDuplicatedDeclaration
				var list = url.substr(i+1, url.length).split('&');
				result = '{"params" : { ';
				for(i = 0; i<list.length; i++){
					var t = list[i].split('=');
					result += '"'+t[0]+'" : "'+t[1]+'", ';
				}
				result = result.substr(0, result.length-2)+'}, ';
				result += '"length" : '+list.length+'}';
				Quasar._setError(true, 0, '成功将URL参数转为JSON格式', 'UrlClass/getUrlParamsJSON()');
				return eval('('+result+')');
			}else{
				//noinspection JSDuplicatedDeclaration
				var i = (arguments.length<1) ? (url.indexOf(_Config.except)+_Config.except.length) : (url.indexOf(opt.except)+opt.except.length);
				if(!url.substr(i, url.length).match(/\/([.#\w\u4E00-\u9FA5\uF900-\uFA2D-]+)\/([.#\w\u4E00-\u9FA5\uF900-\uFA2D-]+)/)){
					Quasar._setError(false, 3001, 'URL不存在参数', 'UrlClass/getUrlParamsJSON()');
					return null;
				}
				//noinspection JSDuplicatedDeclaration
				var list = url.substr(i+1, url.length).split('/');
				result = '{"params" : { ';
				for(i = 0; list.length>(i+1); i += 2) result += '"'+list[i]+'" : "'+list[i+1]+'", ';
				// <全局去掉页面后缀>
				result = result.replace(new RegExp(((arguments.length<1) ? (_Config.suffix) : (opt.suffix)), 'g'), '');
				// </全局去掉页面后缀>
				// <去掉最后的页面后缀>
				//var j = result.lastIndexOf(_Config.suffix);
				//result = result.substr(0, j)+result.substr(j, result.length).replace(_Config.suffix, '');
				// </去掉最后的页面后缀>
				result = result.substr(0, result.length-2)+'}, ';
				result += '"length" : '+Math.floor(list.length/2)+'}';
				Quasar._setError(true, 0, '成功将URL参数转为JSON格式', 'UrlClass/getUrlParamsJSON()');
				return eval('('+result+')');
			}
		};
		/**
		 * 将未嵌套的JSON数据转为标准的URL参数串
		 * @param json 需要转换的JSON数据
		 *
		 * @returns null|string 操作成功statue则为true否则返回false
		 */
		this.parseJSONtoParamsString = function(json){
			if(typeof json != 'object'){
				Quasar._setError(false, 5001, '非法的JS对象', 'UrlClass/parseJSONtoParamsString()', json);
				return null;
			}
			var count = 0, result = '';
			for(var key in json.params){
				if(json.params.hasOwnProperty(key)){
					if(typeof json.params[key] == 'object'){
						Quasar._setError(false, 5003, 'JS对象存在对象迭代', 'UrlClass/parseJSONtoParamsString()', json);
						return null;
					}
					if(_Config.mode == 0){
						if(count == 0) result += '?';
						else result += '&';
						result += key+'='+json.params[key];
						count++;
					}else{
						result += '/'+key+'/'+json.params[key];
						count++;
					}
				}
				else{
					Quasar._setError(false, 5002, '无法访问JS对象属性', 'UrlClass/parseJSONtoParamsString()', json);
					return null;
				}
			}
			if(result == '') Quasar._setError(false, 5001, 'JSON数据不符合格式要求', 'UrlClass/parseJSONtoParamsString()', json);
			else Quasar._setError(true, 0, '将未嵌套的JSON数据转为标准的URL参数串操作成功', 'UrlClass/parseJSONtoParamsString()', json);
			return result;
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 获取URL的指定参数名的值
		 * @param key 参数名
		 * @param [url] 传入的URL字符串，默认值为当前页面的URL
		 * @param [opt] 若传入自定义URL且当前URL模式不为0，则必须传入该值指定需要排除的关键字串和页面后缀，格式为{except:"/param1/param2/...", suffix:".html"}
		 *
		 * @returns null|string 返回参数名的参数值，若无参数名则返回NULL，有则返回参数值
		 */
		this.getUrlParam = function(key, url, opt){
			if(arguments.length<2) url = _StateObject.url.toString();
			else{
				if(arguments.length == 2 && _Config.mode != 0){
					Quasar._setError(false, 11, '函数缺少必要参数', 'UrlClass/getUrlParam()');
					return null;
				}else if(!opt.except || !opt.suffix){
					Quasar._setError(false, 12, '参数类型错误', 'UrlClass/getUrlParamsJSON()');
					return null;
				}
			}
			var list = (arguments.length<2) ? this.getUrlParamsJSON() : this.getUrlParamsJSON(url, opt);
			if(!list){
				Quasar._setError(false, 3001, 'URL不存在参数', 'UrlClass/getUrlParam()');
				return null;
			}
			/** @namespace list.params */
			if(list.params.hasOwnProperty(key)){
				Quasar._setError(true, 0, '获取URL的'+key+'参数成功', 'UrlClass/getUrlParam()', key);
				return list.params[key];
			}
			else{
				Quasar._setError(false, 3001, '指定的Key在URL中不存在', 'UrlClass/getUrlParam()');
				return null;
			}
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 删除URL指定的参数
		 * *调用该函数会修改对应的URL配置，若不及时跳转页面可能造成实际页面和配置不一致的情况
		 * @param key 参数名
		 * @param [url] 传入的URL字符串，默认值为当前页面的URL
		 * @param [opt] 若传入自定义URL且当前URL模式不为0，则必须传入该值指定需要排除的关键字串和页面后缀，格式为{except:"/param1/param2/...", suffix:".html"}
		 *
		 * @returns boolean|string 操作失败返回false，操作成功返回删除参数后的url字符串
		 */
		this.delUrlParam = function(key, url, opt){
			if(arguments.length<2) url = _StateObject.url.toString();
			else{
				if((arguments.length == 2 && _Config.mode != 0) || arguments.length == 0){
					Quasar._setError(false, 11, '函数缺少必要参数', 'UrlClass/delUrlParam()');
					return false;
				}else if(!opt.except || !opt.suffix){
					Quasar._setError(false, 12, '参数类型错误', 'UrlClass/delUrlParam()');
					return false;
				}
			}
			var urllist = (arguments.length<2) ? this.getUrlParamsJSON() : this.getUrlParamsJSON(url, opt);
			if(urllist == null){
				Quasar._setError(false, 3001, 'URL不存在参数', 'UrlClass/delUrlParam()', key);
				return false;
			}
			// 1、key不存在
			if(!((arguments.length<2) ? this.isTheUrlParamExist(key) : this.isTheUrlParamExist(key, url, opt))){
				Quasar._setError(false, 3002, '指定的Key在URL中不存在', 'UrlClass/delUrlParam()', key);
				return false;
			}
			delete urllist.params[key];
			if(_Config.mode == 0){
				_StateObject.url = url.substr(0, url.indexOf('?'))+this.parseJSONtoParamsString(urllist);
				Quasar._setError(true, 0, '删除URL指定的参数操作成功', 'UrlClass/delUrlParam()', key);
				return _StateObject.url;
				// var index = url.indexOf('?'+key+'=');
				// var newurl;
				// //2、key为唯一的参数
				// if(index != -1 && urllist.length == 1){
				// 	newurl = url.substr(0, index);
				// 	_StateObject.url = newurl;
				// 	return {status:true, code:0, data:newurl};
				// }
				// //3、存在若干参数，且key在第一位
				// if(index != -1 && urllist.length>1){
				// 	var pl = url.substr(index+key.length+2, url.length);
				// 	newurl = url.substr(0, index+1)+pl.substr((pl.indexOf('&'))+1, pl.length);
				// 	_StateObject.url = newurl;
				// 	return {status:true, code:0, data:newurl};
				// }
				// index = url.indexOf('&'+key+'=');
				// if(index != -1){
				// 	var front = url.substr(0, index);
				// 	var behind = url.substr(index+key.length+2, url.length);
				// 	var bindex = behind.indexOf('&');
				// 	//4、key不在第一位同时不为最后一个参数
				// 	if(bindex != -1){
				// 		newurl = front+behind.substr(bindex, behind.length);
				// 		_StateObject.url = newurl;
				// 		return {status:true, code:0, data:newurl};
				// 	}
				// 	//5、key不在第一位同时为最后一个参数
				// 	else{
				// 		newurl = front;
				// 		_StateObject.url = newurl;
				// 		return {status:true, code:0, data:front};
				// 	}
				// }
				// return {status:false, code:3999, data:url};
			}else{
				if(arguments.length<2) _StateObject.url = url.substr(0, url.indexOf(_Config.except)+_Config.except.length)+this.parseJSONtoParamsString(urllist)+_Config.suffix;
				else _StateObject.url = url.substr(0, url.indexOf(opt.except)+opt.except.length)+this.parseJSONtoParamsString(urllist)+opt.suffix;
				Quasar._setError(true, 0, '删除URL指定的参数操作成功', 'UrlClass/delUrlParam()', key);
				return _StateObject.url;
			}
		};
		/**
		 * 设定URL参数
		 * *调用该函数会修改对应的URL配置，若不及时跳转页面可能造成实际页面和配置不一致的情况
		 * @param key 设定的参数名
		 * @param val 设定的参数值
		 * @param [url] 传入的URL字符串，默认值为当前页面的URL
		 * @param [opt] 若传入自定义URL且当前URL模式不为0，则必须传入该值指定需要排除的关键字串和页面后缀，格式为{except:"/param1/param2/...", suffix:".html"}
		 *
		 * @returns boolean|string 操作失败返回false，操作成功返回设定参数后的URL
		 */
		this.setUrlParam = function(key, val, url, opt){
			if(arguments.length<2 || (arguments.length == 3 && _Config.mode != 0)){
				Quasar._setError(false, 11, '函数缺少必要参数', 'UrlClass/setUrlParam()');
				return false;
			}else{
				if(arguments.length == 2) url = _StateObject.url.toString();
				else if(!opt.except || !opt.suffix){
					Quasar._setError(false, 12, '参数类型错误', 'UrlClass/setUrlParam()', opt);
					return false;
				}
			}
			if(typeof key != 'number' && typeof key != 'string'){
				Quasar._setError(false, 12, '参数类型错误', 'UrlClass/setUrlParam()');
				return false;
			}
			// 无参数的url&有参数的url两种情况
			var list = (arguments.length == 2) ? this.getUrlParamsJSON() : this.getUrlParamsJSON(url, opt);
			Quasar._setError(true, 0, '设定URL参数操作成功', 'UrlClass/setUrlParam()', key+'='+val);
			if(_Config.mode == 0){
				if(list){
					if(((arguments.length == 2) ? this.isTheUrlParamExist(key) : this.isTheUrlParamExist(key, url, opt))){
						var curvalue = list.params[key];
						_StateObject.url = url.toString().replace(key+'='+curvalue, key+'='+val);
					}
					else _StateObject.url = url+'&'+key+'='+val;
				}
				else _StateObject.url = url+'?'+key+'='+val;
				return _StateObject.url;
			}else{
				if(((arguments.length == 2) ? this.isTheUrlParamExist(key) : this.isTheUrlParamExist(key, url, opt))){
					var jplist = (arguments.length == 2) ? this.getUrlParamsJSON() : this.getUrlParamsJSON(url, opt);
					jplist.params[key] = val;
					if(arguments.length == 2) _StateObject.url = url.substr(0, url.indexOf(_Config.except)+_Config.except.length)+this.parseJSONtoParamsString(jplist)+_Config.suffix;
					else _StateObject.url = url.substr(0, url.indexOf(opt.except)+opt.except.length)+this.parseJSONtoParamsString(jplist)+_Config.suffix;
				}else{
					if(arguments.length == 2) _StateObject.url = url.replace(_Config.suffix, '')+'/'+key+'/'+val+_Config.suffix;
					else _StateObject.url = url.replace(opt.suffix, '')+'/'+key+'/'+val+opt.suffix;
				}
				return _StateObject.url;
			}
		};
		/**
		 * 获取URL的参数字符串
		 * @param [url] 传入的URL字符串，默认值为当前页面的URL
		 * @param [opt] 若传入自定义URL且当前URL模式不为0，则必须传入该值指定需要排除的关键字串和页面后缀，格式为{except:"/param1/param2/...", suffix:".html"}
		 *
		 * @returns null|string 返回参数字符串，若无则返回NULL，有则返回参数的字符串
		 */
		this.getUrlParamsString = function(url, opt){
			if(arguments.length<1) url = _StateObject.url.toString();
			else{
				if(arguments.length == 1 && _Config.mode != 0){
					Quasar._setError(false, 11, '函数缺少必要参数', 'UrlClass/getUrlParamsString()');
					return null;
				}else if(!opt.except || !opt.suffix){
					Quasar._setError(false, 12, '参数类型错误', 'UrlClass/getUrlParamsString()');
					return null;
				}
			}
			if(_Config.mode == 0){
				//noinspection JSDuplicatedDeclaration
				var index = url.indexOf('?');
				if(index == -1) return null;
				else return url.substr(index, url.length);
			}else{
				//noinspection JSDuplicatedDeclaration
				var index = arguments.length == 1 ? (url.indexOf(_Config.except)+_Config.except.length) : (url.indexOf(opt.except)+opt.except.length);
				if(index>=url.length) return null;
				else return url.substr(index, url.length);
			}
		};
		/**
		 * 判断URL的参数是否存在
		 * @param key URL的参数
		 * @param [url] 传入的URL字符串，默认值为当前页面的URL
		 * @param [opt] 若传入自定义URL且当前URL模式不为0，则必须传入该值指定需要排除的关键字串和页面后缀，格式为{except:"/param1/param2/...", suffix:".html"}
		 *
		 * @returns boolean 若存在则返回true，不存在则返回false
		 */
		this.isTheUrlParamExist = function(key, url, opt){
			if(arguments.length<2) url = _StateObject.url;
			else{
				if(arguments.length == 2 && _Config.mode != 0){
					Quasar._setError(false, 11, '函数缺少必要参数', 'UrlClass/isTheUrlParamExist()');
					return false;
				}else if(!opt.except || !opt.suffix){
					Quasar._setError(false, 12, '参数类型错误', 'UrlClass/isTheUrlParamExist()');
					return false;
				}
			}
			if(_Config.mode == 0){
				var paramstr = (arguments.length<2) ? this.getUrlParamsString() : this.getUrlParamsString(url, opt);
				if(paramstr.indexOf('?'+key+'=') != -1) return true;
				if(paramstr.indexOf('&'+key+'=') != -1){
					Quasar._setError(true, 0, '指定参数存在', 'UrlClass/isTheUrlParamExist()', key);
					return true;
				}else{
					Quasar._setError(false, 3002, '指定的Key在URL中不存在', 'UrlClass/isTheUrlParamExist()', key);
					return false;
				}
			}else{
				var paramjson = (arguments.length<2) ? this.getUrlParamsJSON() : this.getUrlParamsJSON(url, opt);
				if(paramjson == null){
					Quasar._setError(false, 3002, '指定的Key在URL中不存在', 'UrlClass/isTheUrlParamExist()', key);
					return false;
				}
				for(var pjkey in paramjson['params']){
					if(key == pjkey){
						Quasar._setError(true, 0, '指定参数存在', 'UrlClass/isTheUrlParamExist()', key);
						return true;
					}
				}
			}
		};
		/**
		 * 判断当前浏览器是否支持history对象和HTML5的pushState特性
		 *
		 * @returns {boolean} 支持则返回true，不支持则返回false
		 */
		this.isAllowHistory = function(){
			return !!(window.history && history.pushState);
		};
		/**
		 * 匹配并获取URL信息
		 * @param [type] 需要返回数据的类型，默认值为url。枚举为[domain, port, protocol, ext, url]，默认为url
		 * @param [url] 需要匹配的URL字符串，默认值为当前页面的URL
		 *
		 * @returns null|string 匹配成功返回匹配字符串，否则返回false
		 */
		this.getUrlInfo = function(type, url){
			if(arguments.length<1){
				url = _StateObject.url.toString();
				type = 'url';
			}
			if(arguments.length == 1){
				url = _StateObject.url.toString();
			}
			var regex = /([\w-]+:[/]{2,3})([.#\w\u4E00-\u9FA5\uF900-\uFA2D-]+)(:([0-9]{1,5}))?([-.%&#?=/\w]*)/;
			var result;
			if(result = url.match(regex)){
				var code;
				switch(type.toLowerCase()){
					case 'domain':
						code = result[2] ? [0, '匹配成功', 2] : [1002, '不能获取URL的域名'];
						break;
					case 'port':
						code = result[4] ? [0, '匹配成功', 4] : [1003, '不能获取URL的端口号'];
						break;
					case 'protocol':
						code = result[1] ? [0, '匹配成功', 1] : [1004, '不能获取URL的协议'];
						break;
					case 'ext':
						code = result[5] ? [0, '匹配成功', 5] : [1005, '不能获取URL的目录路径和参数串'];
						break;
					case 'url':
					default:
						code = result[0] ? [0, '匹配成功', 0] : [2001, '不满足URL格式'];
						break;
				}
				Quasar._setError(code[0] == 0, code[0], code[1], 'UrlClass/getUrlInfo()', url);
				return code[0] == 0 ? result[code[2]] : null;
			}
			Quasar._setError(false, 1001, '未知的URL格式', 'UrlClass/getUrlInfo()', url);
			return null;
		};
		/**
		 * 设定URL或者进行页面跳转
		 * *模式1下需要支持pushState()的浏览器支持
		 * @param url 需要设定或跳转的URL
		 * @param [mode] 设定模式，默认值1。0/1表示设定(不会刷新页面)/跳转
		 *
		 * @returns {boolean}
		 */
		this.setUrl = function(url, mode){
			if(arguments.length<2) mode = 1;
			if(arguments.length<1){
				Quasar._setError(false, 11, '函数缺少必要参数', 'UrlClass/setUrl()');
				return false;
			}
			_StateObject.url = url;
			try{
				if(this.isAllowHistory() && mode == 1)window.history.pushState(_StateObject, _StateObject.title, _StateObject.url);
				else location.href = _StateObject.url;
			}catch(Error){
				if(Error.code == 18){
					console.log(Error.name+': '+Error.message);
					console.log("您执行了一个跨域跳转");
					if(confirm("You wanna go to a external link, do it?")) location.href = _StateObject.url;
				}
			}
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 判断是否为URL格式
		 * @param [url] 需要判断的字符串，默认值为当前页面的URL
		 *
		 * @returns boolean
		 */
		this.isUrl = function(url){
			if(arguments.length<1) url = _StateObject.url.toString();
			//var regex = "^((https|http|ftp|rtsp|igmp|file|rtspt|rtspu):\/\/)?" //协议
			//	+"(((([0-9]|1[0-9]{2}|[1-9][0-9]|2[0-4][0-9]|25[0-5])[.]{1}){3}([0-9]|1[0-9]{2}|[1-9][0-9]|2[0-4][0-9]|25[0-5]))" //IP
			//	+"|"
			//	+"([0-9a-zA-Z\u4E00-\u9FA5\uF900-\uFA2D-]+[.]{1})+[a-zA-Z-]+)" //域名
			//	+"(:[0-9]{1,4})?" // 端口
			//	+"((/?)|(/[0-9a-zA-Z_!~*'().;?:@&=+$,%#-]+)+/?){1}"; //参数
			var result = this.getUrlInfo('url', url);
			return !!result;
		};
		/**
		 * UrlClass构造函数
		 * @param obj 类对象
		 *
		 * @returns undefined|Object
		 * @private
		 */
		function _init(obj){
			Quasar._init();
			if(mode == undefined || mode == null) return obj;
			else{
				mode = parseInt(mode);
				if(mode == 0) return obj;
				if(mode == 1){
					_Config.mode = 1;
					if("undefined" == typeof except){
						if(Quasar._Config.MVC_Name == null || Quasar._Config.MVC_Name == ''){
							Quasar._setError(false, 9991, '缺少排除参数', 'UrlClass/_init()');
							return undefined;
						}
						else except = Quasar._Config.MVC_Name;
					}else except = except.toString();
					if(except.match(/\/([.#\w\u4E00-\u9FA5\uF900-\uFA2D-]+)\/([.#\w\u4E00-\u9FA5\uF900-\uFA2D-]+)\/([.#\w\u4E00-\u9FA5\uF900-\uFA2D-]+)/)) _Config.except = except;
					else{
						Quasar._setError(false, 9992, '排除参数格式错误', 'UrlClass/_init()');
						return undefined;
					}
					if("undefined" == typeof suffix){
						if(Quasar._Config.Page_Suffix == null || Quasar._Config.Page_Suffix == ''){
							Quasar._setError(false, 9993, '缺少页面后缀', 'UrlClass/_init()');
							return undefined;
						}
						else suffix = Quasar._Config.Page_Suffix;
					}else suffix = suffix.toString();
					if(suffix.match(/[.][0-9a-zA-Z_]+/)) _Config.suffix = suffix;
					else{
						Quasar._setError(false, 9994, '页面后缀格式错误', 'UrlClass/_init()');
						return undefined;
					}
				}
			}
			return obj;
		}

		return _init(this);
	},
	/**
	 * 页面处理类
	 *
	 * @constructor
	 */
	PageClass          :function(){
		var self = this;
		var _Attr = {
			IntervalFlag:null,
			IsScroll    :false,
			PagePosition:0
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 开始滚动
		 *
		 * @param [offset] 单位毫秒。滚动一个像素的间隔时间
		 * @param [mode] 滚动模式。枚举为[0, 1]，0表示到底部重新回到顶部循环滚动，1表示到底部即停止
		 *
		 */
		this.startScroll = function(offset, mode){
			if(_Attr.IsScroll) return false;
			else{
				if(!arguments[1]) mode = 0;
				if(!arguments[0]) offset = 100;
				else mode = 1;
				_Attr.IsScroll = true;
				_Attr.IntervalFlag = setInterval(function(){
					var curpos = _getPagePosition();
					_setPagePosition(curpos+1);
					if(_isBottom()){
						if(mode == 0){
							_setPagePosition(0);
							//noinspection JSUnusedAssignment
							_Attr.PagePosition = 0;
						}
						if(mode == 1){
							//noinspection JSPotentiallyInvalidUsageOfThis
							self.stopScroll();
						}
					}
					_Attr.PagePosition = _getPagePosition();
				}, offset);
			}
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 停止滚动
		 */
		this.stopScroll = function(){
			//noinspection JSCheckFunctionSignatures
			clearInterval(_Attr.IntervalFlag);
			_Attr.IsScroll = false;
		};
		/**
		 * 获取当前可视区域对于全页的位置
		 *
		 * @returns number 返回页面位置
		 * @private
		 */
		var _getPagePosition = function(){
			var docetop = document.documentElement.scrollTop;
			var doctop = document.body.scrollTop;
			if(docetop == docetop == 0) return doctop;
			if(docetop == 0 && doctop != 0) return doctop;
			if(doctop == 0 && docetop != 0) return docetop;
			return 0;
		};
		/**
		 * 设定可视区域对于全页的位置
		 *
		 * @returns number 返回页面位置
		 * @private
		 */
		var _setPagePosition = function(pos){
			document.documentElement.scrollTop = pos;
			document.body.scrollTop = pos;
		};
		/**
		 * 判定是否到底部
		 *
		 * @returns {boolean}
		 * @private
		 */
		var _isBottom = function(){
			return _Attr.PagePosition == _getPagePosition();
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 获取可视页面宽高
		 *
		 * @returns {{width: number, height: number}}
		 */
		this.getViewSize = function(){
			var winWidth = 0;
			var winHeight = 0;
			if(window.innerWidth) winWidth = window.innerWidth;
			else if((document.body) && (document.body.clientWidth)) winWidth = document.body.clientWidth;
			if(window.innerHeight) winHeight = window.innerHeight;
			else if((document.body) && (document.body.clientHeight)) winHeight = document.body.clientHeight;
			if(document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth){
				winHeight = document.documentElement.clientHeight;
				winWidth = document.documentElement.clientWidth;
			}
			return {width:winWidth, height:winHeight};
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 获取页面宽高
		 *
		 * @returns {{width: number, height: number}}
		 */
		this.getPageSize = function(){
			return {width:document.body.offsetWidth, height:document.body.offsetHeight};
		}
	},
	/**
	 * 字符处理类
	 * @constructor
	 */
	StringClass        :function(){
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 创建随机字符串
		 *
		 * @param [length] 生成的随即字符串的长度，默认为8
		 * @param [type] 生成的随即字符串的字符类型，默认为NW。枚举为W/W+/W-/N/C的组合（对应大小写字母/大写字母/小写字母/数字/特殊字符）
		 *
		 * @return string 返回随机字符串
		 */
		this.makeRandomString = function(length, type){
			length = arguments[0] ? arguments[0] : 8;
			type = arguments[1] ? arguments[1] : 'NW';
			var chars = ['abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '0123456789', '~!@#$%^&()[]{}_+=-;.,'];
			switch(type.toUpperCase()){
				case 'N':
					chars = chars[2];
					break;
				case 'NW':
				case 'WN':
					chars = chars[0]+chars[1]+chars[2];
					break;
				case 'NC':
				case 'CN':
					chars = chars[2]+chars[3];
					break;
				case 'NW+':
				case 'W+N':
					chars = chars[1]+chars[2];
					break;
				case 'NW-':
				case 'W-N':
					chars = chars[0]+chars[2];
					break;
				case 'W+':
					chars = chars[1];
					break;
				case 'W-':
					chars = chars[0];
					break;
				case 'W':
					chars = chars[0]+chars[1];
					break;
				case 'W+C':
				case 'CW+':
					chars = chars[1]+chars[3];
					break;
				case 'W-C':
				case 'CW-':
					chars = chars[0]+chars[3];
					break;
				case 'WC':
				case 'CW':
					chars = chars[0]+chars[1]+chars[3];
					break;
				default:
					chars = chars[0]+chars[1]+chars[2];
					break;
			}
			var result = '';
			for(var i = 0; i<length; i++) result += chars[_getRandomPosition(chars.length)];
			return result;
		};
		//noinspection JSUnusedGlobalSymbols
		/**
		 * 生成GUID码
		 *
		 * @param [lucase] 大小写标识。枚举为[u, l]，u表示输出的GUID为大写，l为小写
		 * @returns string|null
		 */
		this.makeGUID = function(lucase){
			lucase = arguments[0] ? arguments[0] : 'l';
			var result = '';
			var chars = 'abcdef0123456789';
			//noinspection JSDuplicatedDeclaration
			for(var i = 0; i<8; i++) result += chars[_getRandomPosition(chars.length)];
			result += '-';
			//noinspection JSDuplicatedDeclaration
			for(var i = 0; i<4; i++) result += chars[_getRandomPosition(chars.length)];
			result += '-';
			//noinspection JSDuplicatedDeclaration
			for(var i = 0; i<4; i++) result += chars[_getRandomPosition(chars.length)];
			result += '-';
			//noinspection JSDuplicatedDeclaration
			for(var i = 0; i<4; i++) result += chars[_getRandomPosition(chars.length)];
			result += '-';
			//noinspection JSDuplicatedDeclaration
			for(var i = 0; i<12; i++) result += chars[_getRandomPosition(chars.length)];
			if(lucase.toLowerCase() == 'l') return result;
			if(lucase.toLowerCase() == 'u') return result.toUpperCase();
			return null;
		};
		/**
		 * 获取随机位置算法
		 *
		 * @param [opt] 区间[0-opt]
		 * @returns {number}
		 * @private
		 */
		var _getRandomPosition = function(opt){
			opt = arguments[0] ? arguments[0] : 13;
			var pos = Math.random();
			pos *= opt;
			pos *= Math.PI;
			pos *= Math.E;
			pos *= Math.random();
			pos *= opt;
			pos = Math.round(pos);
			pos %= opt;
			return pos;
		}
	}
};
/**
 * 为Date对象原型添加时间格式化函数
 *
 * @param pattern 时间模式。y, M, d, H, m, s, S, w, W, q分别代表年、月、日、时、分、秒、毫秒、周（中文）、周（英文）、季度
 * @returns string 返回时间模式对应的时间字符串
 */
Date.prototype.format = function(pattern){
	var o = {
		"d+":this.getDate(),
		"H+":this.getHours(),
		"m+":this.getMinutes(),
		"s+":this.getSeconds(),
		"q+":Math.floor((this.getMonth()+3)/3),
		"S" :this.getMilliseconds()
	};
	if(/(y+)/.test(pattern)) pattern = pattern.replace(new RegExp(RegExp.$1, 'g'), (this.getFullYear()+"").substr(4-RegExp.$1.length));
	if(/(W+)/.test(pattern)) pattern = pattern.replace(new RegExp(RegExp.$1, 'g'), _getFormat('week_cn', RegExp.$1.length, this));
	if(/(M+)/.test(pattern)) pattern = pattern.replace(new RegExp(RegExp.$1, 'g'), _getFormat('month', RegExp.$1.length, this));
	if(/(w+)/.test(pattern)) pattern = pattern.replace(new RegExp(RegExp.$1, 'g'), _getFormat('week_en', RegExp.$1.length, this));
	for(var k in o)
		if(new RegExp("("+k+")").test(pattern)){ //noinspection JSUnfilteredForInLoop
			pattern = pattern.replace(new RegExp(RegExp.$1, 'g'), (RegExp.$1.length == 1) ? (o[k]) : (("00"+o[k]).substr((""+o[k]).length)));
		}
	return pattern;
	function _getFormat(type, len, obj){
		type = type.toLowerCase();
		if(type == 'week_cn'){
			var cn = [
				'日', '一', '二', '三', '四', '五', '六'
			];
			switch(len){
				case 1:
					return cn[obj.getDay()];
					break;
				case 2:
					return '周'+cn[obj.getDay()];
					break;
				case 3:
				default:
					return '星期'+cn[obj.getDay()];
					break;
			}
		}
		if(type == 'week_en'){
			var en = {
				abbreviation:['Sun', 'Mon', 'Tue', 'Wed', 'Thr', 'Fri', 'Sat'],
				full        :[
					"Sunday",
					"Monday",
					"Tuesday",
					"Wednesday",
					"Thursday",
					"Friday",
					"Saturday"
				]
			};
			switch(len){
				case 1:
					return obj.getDay();
					break;
				case 2:
					return en.abbreviation[obj.getDay()];
					break;
				case 3:
				default:
					return en.full[obj.getDay()];
					break;
			}
		}
		if(type == 'month'){
			var month = {
				abbreviation:[
					'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
				],
				full        :[
					'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
				]
			};
			switch(len){
				case 1:
					return obj.getMonth()+1;
					break;
				case 2:
					return ("00"+(obj.getMonth()+1)).substr((""+(obj.getMonth()+1)).length);
					break;
				case 3:
					return month.abbreviation[obj.getMonth()+1];
					break;
				case 4:
				default:
					return month.full[obj.getMonth()+1];
					break;
			}
		}
	}
};