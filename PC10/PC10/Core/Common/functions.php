<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * Think 标准模式公共函数库
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 */

/**
 * 错误输出
 * @param mixed $error 错误
 * @return void
 */
function halt($error) {
    $e = array();
    if (APP_DEBUG) {
        //调试模式下输出错误信息
        if (!is_array($error)) {
            $trace          = debug_backtrace();
            $e['message']   = $error;
            $e['file']      = $trace[0]['file'];
            $e['class']     = isset($trace[0]['class'])?$trace[0]['class']:'';
            $e['function']  = isset($trace[0]['function'])?$trace[0]['function']:'';
            $e['line']      = $trace[0]['line'];
            $traceInfo      = '';
            $time = date('y-m-d H:i:m');
            foreach ($trace as $t) {
                $traceInfo .= '[' . $time . '] ' . $t['file'] . ' (' . $t['line'] . ') ';
                $traceInfo .= $t['class'] . $t['type'] . $t['function'] . '(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=')<br/>';
            }
            $e['trace']     = $traceInfo;
        } else {
            $e              = $error;
        }
    } else {
        //否则定向到错误页面
        $error_page         = C('ERROR_PAGE');
        if (!empty($error_page)) {
            redirect($error_page);
        } else {
            if (C('SHOW_ERROR_MSG'))
                $e['message'] = is_array($error) ? $error['message'] : $error;
            else
                $e['message'] = C('ERROR_MESSAGE');
        }
    }
    // 包含异常页面模板
    include C('TMPL_EXCEPTION_FILE');
    exit;
}

/**
 * 自定义异常处理
 * @param string $msg 异常消息
 * @param string $type 异常类型 默认为ThinkException
 * @param integer $code 异常代码 默认为0
 * @return void
 */
function throw_exception($msg, $type='ThinkException', $code=0) {
    if (class_exists($type, false))
        throw new $type($msg, $code, true);
    else
        halt($msg);        // 异常类型不存在则输出错误信息字串
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

/**
 * 404处理
 * 调试模式会抛异常
 * 部署模式下面传入url参数可以指定跳转页面，否则发送404信息
 * @param string $msg 提示信息
 * @param string $url 跳转URL地址
 * @return void
 */
function _404($msg='',$url='') {
    APP_DEBUG && throw_exception($msg);
    if($msg && C('LOG_EXCEPTION_RECORD')) Log::write($msg);
    if(empty($url) && C('URL_404_REDIRECT')) {
        $url    =   C('URL_404_REDIRECT');
    }
    if($url) {
        redirect($url);
    }else{
        send_http_status(404);
        exit;
    }
}

/**
 * 设置当前页面的布局
 * @param string|false $layout 布局名称 为false的时候表示关闭布局
 * @return void
 */
function layout($layout) {
    if(false !== $layout) {
        // 开启布局
        C('LAYOUT_ON',true);
        if(is_string($layout)) { // 设置新的布局模板
            C('LAYOUT_NAME',$layout);
        }
    }else{// 临时关闭布局
        C('LAYOUT_ON',false);
    }
}

/**
 * URL组装 支持不同URL模式
 * @param string $url URL表达式，格式：'[分组/模块/操作@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $redirect 是否跳转，如果设置为true则表示跳转到该URL地址
 * @param boolean $domain 是否显示域名
 * @return string
 */
function U($url='',$vars='',$suffix=true,$redirect=false,$domain=false) {
    // 解析URL
    $info   =  parse_url($url);
    $url    =  !empty($info['path'])?$info['path']:ACTION_NAME;
    if(false !== strpos($url,'@')) { // 解析域名
        list($url,$host)    =   explode('@',$info['path'], 2);
    }
    // 解析子域名
    if(isset($host)) {
        $domain = $host.(strpos($host,'.')?'':strstr($_SERVER['HTTP_HOST'],'.'));
    }elseif($domain===true){
        $domain = $_SERVER['HTTP_HOST'];
        if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 开启子域名部署
            $domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
            // '子域名'=>array('项目[/分组]');
            foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                if(false === strpos($key,'*') && 0=== strpos($url,$rule[0])) {
                    $domain = $key.strstr($domain,'.'); // 生成对应子域名
                    $url    =  substr_replace($url,'',0,strlen($rule[0]));
                    break;
                }
            }
        }
    }

    // 解析参数
    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }

    // URL组装
    $depr = C('URL_PATHINFO_DEPR');
    if($url) {
        if(0=== strpos($url,'/')) {// 定义路由
            $route      =   true;
            $url        =   substr($url,1);
            if('/' != $depr) {
                $url    =   str_replace('/',$depr,$url);
            }
        }else{
            if('/' != $depr) { // 安全替换
                $url    =   str_replace('/',$depr,$url);
            }
            // 解析分组、模块和操作
            $url        =   trim($url,$depr);
            $path       =   explode($depr,$url);
            $var        =   array();
            $var[C('VAR_ACTION')]       =   !empty($path)?array_pop($path):ACTION_NAME;
            $var[C('VAR_MODULE')]       =   !empty($path)?array_pop($path):MODULE_NAME;
            if(C('URL_CASE_INSENSITIVE')) {
                $var[C('VAR_MODULE')]   =   parse_name($var[C('VAR_MODULE')]);
            }
            if(!C('APP_SUB_DOMAIN_DEPLOY') && C('APP_GROUP_LIST')) {
                if(!empty($path)) {
                    $group                  =   array_pop($path);
                    $var[C('VAR_GROUP')]    =   $group;
                }else{
                    if(GROUP_NAME != C('DEFAULT_GROUP')) {
                        $var[C('VAR_GROUP')]=   GROUP_NAME;
                    }
                }
                if(C('URL_CASE_INSENSITIVE') && isset($var[C('VAR_GROUP')])) {
                    $var[C('VAR_GROUP')]    =  strtolower($var[C('VAR_GROUP')]);
                }
            }
        }
    }

    if(C('URL_MODEL') == 0) { // 普通模式URL转换
        $url        =   __APP__.'?'.http_build_query(array_reverse($var));
        if(!empty($vars)) {
            $vars   =   urldecode(http_build_query($vars));
            $url   .=   '&'.$vars;
        }
    }else{ // PATHINFO模式或者兼容URL模式
        if(isset($route)) {
            $url    =   __APP__.'/'.rtrim($url,$depr);
        }else{
            $url    =   __APP__.'/'.implode($depr,array_reverse($var));
        }
        if(!empty($vars)) { // 添加参数
            foreach ($vars as $var => $val)
                $url .= $depr.$var . $depr . $val;
        }
        if($suffix) {
            $suffix   =  $suffix===true?C('URL_HTML_SUFFIX'):$suffix;
            if($pos = strpos($suffix, '|')){
                $suffix = substr($suffix, 0, $pos);
            }
            if($suffix && $url[1]){
                $url  .=  '.'.ltrim($suffix,'.');
            }
        }
    }
    if($domain) {
        $url   =  (is_ssl()?'https://':'http://').$domain.$url;
    }
    if($redirect) // 直接跳转URL
        redirect($url);
    else
        return $url;
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
    //多行URL地址支持
    $url        = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $expire 缓存有效期（秒）
 * @return mixed
 */
function cache($name,$value='',$expire=0) {
    static $cache   =   '';
    if(is_array($expire)){
        // 缓存操作的同时初始化
        $type       =   isset($expire['type'])?$expire['type']:'';
        $cache      =   Cache::getInstance($type,$expire);
    }elseif(is_array($name)) { // 缓存初始化
        $type       =   isset($name['type'])?$name['type']:'';
        $cache      =   Cache::getInstance($type,$name);
        return $cache;
    }elseif(empty($cache)) { // 自动初始化
        $cache      =   Cache::getInstance();
    }
    if(''=== $value){ // 获取缓存值
        // 获取缓存数据
        return $cache->get($name);
    }elseif(is_null($value)) { // 删除缓存
        return $cache->rm($name);
    }else { // 缓存数据
        $expire     =   is_numeric($expire)?$expire:NULL;
        return $cache->set($name, $value, $expire);
    }
}

/**
 * 全局缓存设置和读取
 * @param string $name 缓存名称
 * @param mixed $value 缓存值
 * @param integer $expire 缓存有效期（秒）
 * @param string $type 缓存类型
 * @param array $options 缓存参数
 * @return mixed
 */
function S($name, $value='', $expire=null, $type='',$options=null) {
    static $_cache = array();
    //取得缓存对象实例
    $cache = Cache::getInstance($type,$options);
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            $result = $cache->rm($name);
            if ($result)
                unset($_cache[$type . '_' . $name]);
            return $result;
        }else {
            // 缓存数据
            $cache->set($name, $value, $expire);
            $_cache[$type . '_' . $name] = $value;
        }
        return;
    }
    if (isset($_cache[$type . '_' . $name]))
        return $_cache[$type . '_' . $name];
    // 获取缓存数据
    $value = $cache->get($name);
    $_cache[$type . '_' . $name] = $value;
    return $value;
}

/**
 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 * @param string $name 缓存名称
 * @param mixed $value 缓存值
 * @param string $path 缓存路径
 * @return mixed
 */
function F($name, $value='', $path=DATA_PATH) {
    static $_cache  = array();
    $filename       = $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            return unlink($filename);
        } else {
            // 缓存数据
            $dir            =   dirname($filename);
            // 目录不存在则创建
            if (!is_dir($dir))
                mkdir($dir,0755,true);
            $_cache[$name]  =   $value;
            return file_put_contents($filename, strip_whitespace("<?php\treturn " . var_export($value, true) . ";\n?>"));
        }
    }
    if (isset($_cache[$name]))
        return $_cache[$name];
    // 获取缓存数据
    if (is_file($filename)) {
        $value          =   include $filename;
        $_cache[$name]  =   $value;
    } else {
        $value          =   false;
    }
    return $value;
}

/**
 * 取得对象实例 支持调用类的静态方法
 * @param string $name 类名
 * @param string $method 方法名，如果为空则返回实例化对象
 * @param array $args 调用参数
 * @return object
 */
function get_instance_of($name, $method='', $args=array()) {
    static $_instance = array();
    $identify = empty($args) ? $name . $method : $name . $method . to_guid_string($args);
    if (!isset($_instance[$identify])) {
        if (class_exists($name)) {
            $o = new $name();
            if (method_exists($o, $method)) {
                if (!empty($args)) {
                    $_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
                } else {
                    $_instance[$identify] = $o->$method();
                }
            }
            else
                $_instance[$identify] = $o;
        }
        else
            halt(L('_CLASS_NOT_EXIST_') . ':' . $name);
    }
    return $_instance[$identify];
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * XML编码
 * @param mixed $data 数据
 * @param string $encoding 数据编码
 * @param string $root 根节点名
 * @return string
 */
function xml_encode($data, $encoding='utf-8', $root='think') {
    $xml    = '<?xml version="1.0" encoding="' . $encoding . '"?>';
    $xml   .= '<' . $root . '>';
    $xml   .= data_to_xml($data);
    $xml   .= '</' . $root . '>';
    return $xml;
}

/**
 * 数据XML编码
 * @param mixed $data 数据
 * @return string
 */
function data_to_xml($data) {
    $xml = '';
    foreach ($data as $key => $val) {
        is_numeric($key) && $key = "item id=\"$key\"";
        $xml    .=  "<$key>";
        $xml    .=  ( is_array($val) || is_object($val)) ? data_to_xml($val) : $val;
        list($key, ) = explode(' ', $key);
        $xml    .=  "</$key>";
    }
    return $xml;
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 */
function session($name,$value='') {
    $prefix   =  C('SESSION_PREFIX');
    if(is_array($name)) { // session初始化 在session_start 之前调用
        if(isset($name['prefix'])) C('SESSION_PREFIX',$name['prefix']);
        if(C('VAR_SESSION_ID') && isset($_REQUEST[C('VAR_SESSION_ID')])){
            session_id($_REQUEST[C('VAR_SESSION_ID')]);
        }elseif(isset($name['id'])) {
            session_id($name['id']);
        }
        ini_set('session.auto_start', 0);
        if(isset($name['name']))            session_name($name['name']);
        if(isset($name['path']))            session_save_path($name['path']);
        if(isset($name['domain']))          ini_set('session.cookie_domain', $name['domain']);
        if(isset($name['expire']))          ini_set('session.gc_maxlifetime', $name['expire']);
        if(isset($name['use_trans_sid']))   ini_set('session.use_trans_sid', $name['use_trans_sid']?1:0);
        if(isset($name['use_cookies']))     ini_set('session.use_cookies', $name['use_cookies']?1:0);
        if(isset($name['cache_limiter']))   session_cache_limiter($name['cache_limiter']);
        if(isset($name['cache_expire']))    session_cache_expire($name['cache_expire']);
        if(isset($name['type']))            C('SESSION_TYPE',$name['type']);
        if(C('SESSION_TYPE')) { // 读取session驱动
            $class      = 'Session'. ucwords(strtolower(C('SESSION_TYPE')));
            // 检查驱动类
            if(require_cache(EXTEND_PATH.'Driver/Session/'.$class.'.class.php')) {
                $hander = new $class();
                $hander->execute();
            }else {
                // 类没有定义
                throw_exception(L('_CLASS_NOT_EXIST_').': ' . $class);
            }
        }
        // 启动session
        if(C('SESSION_AUTO_START'))  session_start();
    }elseif('' === $value){
        if(0===strpos($name,'[')) { // session 操作
            if('[pause]'==$name){ // 暂停session
                session_write_close();
            }elseif('[start]'==$name){ // 启动session
                session_start();
            }elseif('[destroy]'==$name){ // 销毁session
                $_SESSION =  array();
                session_unset();
                session_destroy();
            }elseif('[regenerate]'==$name){ // 重新生成id
                session_regenerate_id();
            }
        }elseif(0===strpos($name,'?')){ // 检查session
            $name   =  substr($name,1);
            if($prefix) {
                return isset($_SESSION[$prefix][$name]);
            }else{
                return isset($_SESSION[$name]);
            }
        }elseif(is_null($name)){ // 清空session
            if($prefix) {
                unset($_SESSION[$prefix]);
            }else{
                $_SESSION = array();
            }
        }elseif($prefix){ // 获取session
            return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;
        }else{
            return isset($_SESSION[$name])?$_SESSION[$name]:null;
        }
    }elseif(is_null($value)){ // 删除session
        if($prefix){
            unset($_SESSION[$prefix][$name]);
        }else{
            unset($_SESSION[$name]);
        }
    }else{ // 设置session
        if($prefix){
            if (!is_array($_SESSION[$prefix])) {
                $_SESSION[$prefix] = array();
            }
            $_SESSION[$prefix][$name]   =  $value;
        }else{
            $_SESSION[$name]  =  $value;
        }
    }
}

/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $options cookie参数
 * @return mixed
 */
function cookie($name, $value='', $option=null) {
    // 默认设置
    $config = array(
        'prefix'    =>  C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire'    =>  C('COOKIE_EXPIRE'), // cookie 保存时间
        'path'      =>  C('COOKIE_PATH'), // cookie 保存路径
        'domain'    =>  C('COOKIE_DOMAIN'), // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!empty($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config     = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        return isset($_COOKIE[$name]) ? json_decode(MAGIC_QUOTES_GPC?stripslashes($_COOKIE[$name]):$_COOKIE[$name]) : null; // 获取指定Cookie
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            $value  = json_encode($value);
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

/**
 * 加载动态扩展文件
 * @return void
 */
function load_ext_file() {
    // 加载自定义外部文件
    if(C('LOAD_EXT_FILE')) {
        $files      =  explode(',',C('LOAD_EXT_FILE'));
        foreach ($files as $file){
            $file   = COMMON_PATH.$file.'.php';
            if(is_file($file)) include $file;
        }
    }
    // 加载自定义的动态配置文件
    if(C('LOAD_EXT_CONFIG')) {
        $configs    =  C('LOAD_EXT_CONFIG');
        if(is_string($configs)) $configs =  explode(',',$configs);
        foreach ($configs as $key=>$config){
            $file   = CONF_PATH.$config.'.php';
            if(is_file($file)) {
                is_numeric($key)?C(include $file):C($key,include $file);
            }
        }
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
	$type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = ip2long($ip);
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code) {
    static $_status = array(
        // Success 2xx
        200 => 'OK',
        // Redirection 3xx
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',  // 1.1
        // Client Error 4xx
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        // Server Error 5xx
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:'.$code.' '.$_status[$code]);
    }
}

function getMethod($url){
    return file_get_contents($url);
}


function httpMethod($url,$data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $info = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Errno'.curl_error($ch);
    }

    curl_close($ch);
    return $info;
}

function getLiangshi($keywords){
   $res = getMethod("http://m.aweb.com.cn/price/search.shtml?priceVo.areaString=".urlencode($keywords));
   preg_match_all('/<span\s+class="f46.*>(.*)<\/span>/isU',$res,$match);
   if(count($match[1]) == 4){
        $content .="您好,今日".$keywords."地区报价\r\n";
        $content .= '生猪(外三元):'.$match[1][0]."元/公斤\r\n";
        $content .= '仔猪(15公斤):'.$match[1][1]."元/公斤\r\n";
        $content .= '玉米(14%水份):'.$match[1][2]."元/吨\r\n";
        $content .= '豆粕(43%蛋白):'.$match[1][3]."元/吨\r\n";

        $content .="以上价格仅供参考。";
        return $content;
   }else{
       return '对不起，我们暂时无法返回你要查的地区价格请输入地名来继续查询';
   }
}


function Kindergarten($secenid,$token,$openid){
    $id = substr($secenid,2);
    if($id && $token && $openid){
        $kdata = M('Kindergarten')->field('id,notice,kgaddress,kgname,kgpic')->where(array('token'=>$token,'id'=>$id))->find();
        return $return_data  = array(
                array(
                        '欢迎来到'.$kdata['kgname'],
                        $kdata['kgaddress'],
                        C('site_url').$kdata['kgpic'],
                        C('site_url') . '/index.php?g=Wap&m=Kindergarten&a=index&token=' . $token . '&openid=' .$openid. '&kgid='.$id
                )
        );
    }
}

function Spread($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid){
        $kdata = M('Spread')->field('id,activityname,introduction,imgurl')->where(array('token'=>$token,'id'=>$id))->find();
        return $return_data  = array(
            array(
                $kdata['activityname'],
                $kdata['introduction'],
                C('site_url').$kdata['imgurl'],
                C('site_url') . '/index.php?g=Wap&m=Spread&a=index&token=' . $token . '&openid=' .$openid. '&id='.$id
            )
        );
    }
}


function Wechatact($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid){
        $kdata = M('Wechatact')->field('id,notice,name,pic')->where(array('token'=>$token,'id'=>$id))->find();
        return $return_data  = array(
                array(
                        $kdata['name'],
                        htmlspecialchars_decode($kdata['notice'],ENT_QUOTES),
                        C('site_url').$kdata['pic'],
                        C('site_url') . '/index.php?g=Wap&m=WechatConferencemarking&a=index&token=' . $token . '&openid=' .$openid. '&actid='.$id
                )
        );
    }
}

/*由你定个人二维码*/
function GetYnduser($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid){
        $kdata = M('Ynd_user')->where(array(
            'id' => $id,
            'token'   => $token
        ))->find();
        return $return_data  = array(
            array(
                '',
                '',
                '',
                C('site_url') . '/index.php?g=Wap&m=Intel&a=bind&token=' . $token . '&openid=' .$openid. '&dopenid='.$kdata['openid']
            )
        );
    }
}


/*
 *  拍拍狗扫二维码事件
 */
function Intel($secenid,$token,$openid)
{
    $id = substr($secenid,2);
    if($id && $token && $openid){
        $kdata = M('Intel_imei')->where(array(
            'senceid' => $secenid,
            'token'   => $token
        ))->find();
        return $return_data  = array(
            array(
                '请点击绑定设备',
                '移动管理您的设备,imei号为:【'.$kdata['imei'].'】,时间:'.date('Y-m-d H:i:s'),
                '',
                C('site_url') . '/index.php?g=Wap&m=Intel&a=bind&token=' . $token . '&openid=' .$openid. '&imei='.$kdata['imei']
            )
        );
    }
}
//国泰安扫二维码推广事件
function Gtafenxiao($secenid,$token,$openid){
    $id = substr($secenid,1);
    //补加
    if(!M('Gta_users')->where(array('token'=>$token,'openid'=>$openid))->find()){
        $data1['token']=$token;
        $data1['openid']=$openid;
        $data1['add_time']=time();
        $token_id=M('Wxuser')->where(array('token'=>$token))->getField('id');
        $data1['uid']=M('Wxusers')->where(array('uid'=>$token_id,'openid'=>$openid))->getField('id');
        $data1['token']=$token;
        $data1['member_sn']="Gta".date("YmdHis",time()).rand(100,999);
        M('Gta_users')->add($data1);
    }
    if(!M('Gta_users')->where(array('token'=>$token,'openid'=>$openid))->getField('dopenid')){//没有推荐人
        $from_openid=M('Gta_users')->where(array('id'=>$id))->getField('openid');
        //不许互推A->B,B->A
        $b=M('Gta_users')->where(array('id'=>$id))->getField('dopenid');
        if($b AND ($b==$openid)){
            return $return_data  = array(
                array(
                    '推荐关注失败',
                    '不能互相推荐',
                    '',
                    C('site_url') . '/index.php?g=Wap&m=Gta&a=index&token=' . $token . '&wecha_id=' .$openid.'&openid='.$openid
                )
            );
            die;
        }
        if($from_openid==$openid){
            return $return_data  = array(
                array(
                    '推荐关注失败',
                    '您不能成功自己的下级',
                    '',
                    C('site_url') . '/index.php?g=Wap&m=Gta&a=index&token=' . $token . '&wecha_id=' .$openid.'&openid='.$openid
                )
            );
        }else{
            M('Gta_users')->where(array('token'=>$token,'openid'=>$openid))->save(array('dopenid'=>$from_openid,'t_add_time'=>time()));
            return $return_data  = array(
                array(
                    '推荐关注成功',
                    '绑定上下级关系成功',
                    '',
                    C('site_url') . '/index.php?g=Wap&m=Gta&a=index&token=' . $token . '&wecha_id=' .$openid.'&openid='.$openid
                )
            );
        }

    }else{//  有推荐人
        return $return_data  = array(
            array(
                '推荐关注失败',
                '用户已经被推荐过了',
                '',
                C('site_url') . '/index.php?g=Wap&m=Gta&a=index&token=' . $token . '&wecha_id=' .$openid.'&openid'.$openid
            )
        );
    }

}

function Laundry_bag($secenid,$token,$openid){
    $id = substr($secenid,2);
    if($id && $token && $openid){
        $kdata = M('Laundry_bag')->field('bag_sn,bag_status,id')->where(array('token'=>$token,'id'=>$id))->find();
        return $return_data  = array(
                array(
                        '我是96洗衣袋，我的编号是'.$kdata['bag_sn'],
                        htmlspecialchars_decode($kdata['bag_status'],ENT_QUOTES),
			'',
                        C('site_url') . '/index.php?g=Wap&m=Laundry&a=bag&token=' . $token . '&openid=' .$openid. '&bagid='.$id
                )
        );
    }
}

function Commerce($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid ){
        $wxUserModel = M('wxusers');
        $shopUsersmodel = M('Shop_users');
        $wxUserDatas = $wxUserModel->field('openid')->where(array('id' => $id))->find();
        //推荐人
        $tuijianren = $shopUsersmodel->where(array('openid'=>$wxUserDatas['openid'],'token'=>$token))->find();
        $beituijianren = $shopUsersmodel->where(array('openid'=>$openid,'token'=>$token))->find();
        if($beituijianren && $tuijianren){
            //是否已推荐
            if($beituijianren['from_openid'] == null){
                //更新推荐人
                if($shopUsersmodel->where(array('openid'=>$openid,'token'=>$token))->save(array('from_openid'=>$wxUserDatas['openid']))){
                    $scoreSetModel = M('Shop_scoreset');
                    $scoreSetdata = $scoreSetModel->field('tuijian_score')->where(array('token' => $token))->find();
                    if($shopUsersmodel->where(array('openid'=>$wxUserDatas['openid'],'token'=>$token))->setInc('score',$scoreSetdata['tuijian_score'])){
                        return $return_data  = array(
                            array(
                                '推荐关注成功',
                                '推荐者将获得'.$scoreSetdata['tuijian_score'].'积分',
                                '',
                                C('site_url') . '/index.php?g=Wap&m=MemberCenter&a=index&token=' . $token . '&wecha_id=' .$openid
                            )
                        );
                    }else{
                        echo '';exit;
                    }
                }else{
                    echo '';exit;
                }
            }else{
                echo '';exit;
            }

        }else{
            echo '';exit;
        }
    }
}

function Gettable($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid ){
        //http://v.wapwei.com/index.php?g=Wap&m=Product&a=cats&token=f17f0d1e02a8976cf065163525547260&wecha_id=oljl8twwrEbtqD7Q131fmvA2bnxU&tableid=2
        $kdata = M('Reply_info')->field('id,info,title,picurl')->where(array('token'=>$token,'infotype'=>'Dining'))->find();
	$tabledata = M('Product_diningtable')->where(array('token'=>$token,'id'=>$id))->find();
//        print_r($kdata);exit;
        return $return_data  = array(
            array(
                $kdata['title'],
                $tabledata['intro'],
                C('site_url').$kdata['picurl'],
                C('site_url') . '/index.php?g=Wap&m=Product&a=cats&token=' . $token . '&wecha_id=' .$openid. '&dining=1&tableid='.$id
            )
        );
    }
}

function Getfenxiao($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid ){
        //http://v.wapwei.com/index.php?g=Wap&m=Product&a=cats&token=f17f0d1e02a8976cf065163525547260&wecha_id=oljl8twwrEbtqD7Q131fmvA2bnxU&tableid=2
        $codes = M('Code')->where(array('id'=>$id))->find();
        $val = unserialize($codes['value']);
        $kdata = M("Mru_qianggou")->field('id,title,pic')->where(array('id'=>$val['shop_id']))->find();
        $user = M('Wxusers')->where(array('id'=>$val['user_id']))->find();
        if ($token == 'e756d6be1ec4fab3c5920f3a3437160b') {//鱼美人
            $sUrl = C('site_url') . '/index.php?g=Wap&m=MruQianggou&a=show&token=' . $token . '&wecha_id=' .$openid.'&openid='.$openid.'&dopenid='.$val['openid'].'&id='.$val['shop_id'];
        }else{
            $sUrl = C('site_url') . '/index.php?g=Wap&m=Store_shop&a=product&token=' . $token . '&wecha_id=' .$openid.'&openid='.$openid.'&dopenid='.$user['openid'].'&id='.$val['shop_id'];
        }
        return $return_data  = array(
            array(
                $kdata['title'],
                '欢迎购买！',
                C('site_url').$kdata['pic'],
                $sUrl
            )
        );
    }
}


function Getscore($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid ){
        $userModel = M('wxuser');
        $userDatas = $userModel->where(array('token' => $token))->find();
        $wxUserModel = M('wxusers');
        $wxUserDatas = $wxUserModel->where(array('uid' => $userDatas['id'], 'openid' => $openid))->find();
        $refre = M('Repair_user')->where(array('id'=>$id))->find();
        if($refre){
            $res = M('Repair_user')->where(array('wxuser_id'=>$userDatas['id'],'wxusers_id'=>$wxUserDatas['id']))->find();
            if($res['referee'] == 0 || $res['referee'] == null){
                M('Repair_user')->where(array('wxuser_id'=>$userDatas['id'],'wxusers_id'=>$wxUserDatas['id']))->save(array('referee'=>$refre['wxusers_id']));
                M('Repair_user')->where(array('id'=>$id))->setInc('score',5);

                return $return_data  = array(
                    array(
                        '推荐关注成功',
                        '推荐关注将获得5积分',
                        '',
                        C('site_url') . '/index.php?g=Wap&m=Repair&a=wxCenter&token=' . $token . '&wecha_id=' .$openid
                    )
                );
            }else{
                echo '';exit;
            }
        }else{
            echo '';exit;
        }

    }
}

function addMediauser($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid ){
        $wxUserModel = M('wxusers');
        $oUsersModel = M('Media_users');
        $oRecordMOdel = M('Media_record');
        $oSetcenter = M('Media_setcenter');
        $oSettingModel = M('Product_setting_new');
        $oInviterecordModel = M('Media_inviterecord');
        $wxUserDatas = $wxUserModel->where(array('id' => $id))->find();
        $setCenter = $oSetcenter->where(array('token'=>$token))->find();
        $isSet = $oSettingModel->where(array('token'=>$token))->find();

        /*第一次进来，会员中心，微传播中心相应添加记录*/
        if(!M('Usercenter_memberlist')->where(array('uid'=>$wxUserDatas['id'],'openid'=>$openid))->find()){
            $usercenter_model = M('Usercenter_set');
            $usercenterdata = $usercenter_model->field('is_openphone,u_prefix')->where(array('token'=>$wxUserDatas['token']))->find();
            $prefix = 'WP';
            if($usercenterdata){
                $prefix = $usercenterdata['u_prefix'];
            }
            $sn = $prefix.$wxUserDatas['id'].date("Ymd",time()).rand(100,999);
            M('Usercenter_memberlist')->add(array(
                'uid'=>$wxUserDatas['id'],
                'openid'=>$openid,
                'score'=>0,
                'money'=>0,
                'member_sn'=>$sn
            ));
        }

        $firstScoreRecored=array(
            'token'=>$wxUserDatas['token'],
            'openid'=>$openid,
            'type'=>10,
            'score'=>$setCenter['invite'],
            'add_time'=>time()
        );
        M('Usercenter_score_record')->add($firstScoreRecored);
        M('Usercenter_memberlist')->where(array('uid'=>$wxUserDatas['id'],'openid'=>$openid))->setInc('score',$setCenter['invite']);

        $aData['token'] = $token;
        $aData['openid'] = $openid;
        $aData['from_openid'] = $wxUserDatas['openid'];
        $aData['date'] = date('Y-m-d',time());
        $aData['add_time'] = date('Y-m-d H:i:s',time());
        $aData['money']=$setCenter['invite'];
        if($isSet['get_distribution'] = 2){
            $aData['type'] = 1;
        }else{
            $aData['type'] = 0;
        }
        $aData['type'] = 1;
        $ress = $oUsersModel->where(array('token'=>$token,'openid'=>$openid))->find();

        if(!$ress){
            $bAddusers = $oUsersModel->data($aData)->add();
            $oRecordMOdel->add(array(
                'score' => $setCenter['invite'],
                'type' => 1,
                'openid' => $openid,
                'token' => $token,
                'add_time' => date('Y-m-d H:i:s')
            ));
            $oInviterecordModel->add(array(
                'date'=>date('Y-m-d'),
                'add_time'=>date('Y-m-d H:i:s'),
                'token'=>$token,
                'give_openid'=> $openid,
                'openid'=> $openid,
                'type' => 0,   //表示自己
                'headpic' => $wxUserDatas['headimgurl'],
                'nickname' => $wxUserDatas['nickname'],
                'score'=> $setCenter['invite']
            ));
            if($wxUserDatas['openid']){  //第一级
                M('Usercenter_memberlist')->where(array('uid'=>$wxUserDatas['id'],'openid'=>$wxUserDatas['openid']))->setInc('score',$setCenter['redfirst']);
                M('Usercenter_score_record')->add(array('token'=>$token,
                    'openid'=>$wxUserDatas['openid'],
                    'type'=>10,
                    'score'=>$setCenter['redfirst'],
                    'add_time'=>time()
                ));
                $oUsersModel->where(array('token'=>$token,'openid'=>$wxUserDatas['openid']))->setInc('money',$setCenter['redfirst']);
                $oRecordMOdel->add(array(
                    'score' => $setCenter['redfirst'],
                    'type' => 1,
                    'openid' => $wxUserDatas['openid'],
                    'token' => $token,
                    'add_time' => date('Y-m-d H:i:s')
                ));
                $oInviterecordModel->add(array(
                    'date'=>date('Y-m-d'),
                    'add_time'=>date('Y-m-d H:i:s'),
                    'token'=>$token,
                    'give_openid'=> $openid,
                    'openid'=> $wxUserDatas['openid'],
                    'type' => 1,   //表示上级
                    'headpic' => $wxUserDatas['headimgurl'],
                    'nickname' => $wxUserDatas['nickname'],
                    'score'=> $setCenter['redfirst']
                ));
                msg($token=$this->token,$openid=$wxUserDatas['openid'],$content="恭喜您！".$wxUserDatas['nickname']."成为你的一级伙伴，系统奖励积分".$setCenter['redfirst']."分，加油哦！");
                $secuser = $oUsersModel->where(array('token'=>$token,'openid'=>$wxUserDatas['openid']))->find();
                if($secuser['from_openid']){//判断是否有第二级
                    M('Usercenter_memberlist')->where(array('uid'=>$wxUserDatas['id'],'openid'=>$secuser['from_openid']))->setInc('score',$setCenter['redsecond']);
                    M('Usercenter_score_record')->add(array('token'=>$this->token,
                        'openid'=>$secuser['from_openid'],
                        'type'=>10,
                        'score'=>$setCenter['redsecond'],
                        'add_time'=>time()
                    ));
                    $oUsersModel->where(array('token'=>$token,'openid'=>$secuser['from_openid']))->setInc('money',$setCenter['redsecond']);
                    $oRecordMOdel->add(array(
                        'score' => $setCenter['redsecond'],
                        'type' => 1,
                        'openid' => $secuser['openid'],
                        'token' => $token,
                        'add_time' => date('Y-m-d H:i:s')
                    ));
                    $oInviterecordModel->add(array(
                        'date'=>date('Y-m-d'),
                        'add_time'=>date('Y-m-d H:i:s'),
                        'token'=>$token,
                        'give_openid'=> $openid,
                        'openid'=>$secuser['from_openid'],
                        'type' => 2,   //表示上上级
                        'headpic' => $wxUserDatas['headimgurl'],
                        'nickname' => $wxUserDatas['nickname'],
                        'score'=> $setCenter['redsecond']
                    ));
                    msg($token=$this->token,$openid=$secuser['from_openid'],$content="恭喜您！".$wxUserDatas['nickname']."成为你的二级伙伴，系统奖励积分".$setCenter['redsecond']."分，加油哦！");
                    $thruser = $oUsersModel->where(array('token'=>$token,'openid'=>$secuser['from_openid']))->find();
                    if($thruser['from_openid']){//判断是否有第三级
                        M('Usercenter_memberlist')->where(array('uid'=>$wxUserDatas['id'],'openid'=>$thruser['from_openid']))->setInc('score',$setCenter['redThere']);
                        M('Usercenter_score_record')->add(array('token'=>$token,
                            'openid'=>$thruser['from_openid'],
                            'type'=>10,
                            'score'=>$setCenter['redThere'],
                            'add_time'=>time()
                        ));
                        $oUsersModel->where(array('token'=>$token,'openid'=>$thruser['from_openid']))->setInc('money',$setCenter['redThere']);
                        $oRecordMOdel->add(array(
                            'score' => $setCenter['redThere'],
                            'type' => 1,
                            'openid' => $thruser['openid'],
                            'token' => $token,
                            'add_time' => date('Y-m-d H:i:s')
                        ));
                        $oInviterecordModel->add(array(
                            'date'=>date('Y-m-d'),
                            'add_time'=>date('Y-m-d H:i:s'),
                            'token'=>$token,
                            'give_openid'=> $openid,
                            'openid'=>$thruser['from_openid'],
                            'type' => 3,   //表示上上级
                            'headpic' => $wxUserDatas['headimgurl'],
                            'nickname' => $wxUserDatas['nickname'],
                            'score'=> $setCenter['redThere']
                        ));
                        msg($token=$this->token,$openid=$thruser['from_openid'],$content="恭喜您！".$wxUserDatas['nickname']."成为你的三级伙伴，系统奖励积分".$setCenter['redThere']."分，加油哦！");

                    }

                }
            }
            if($bAddusers ){
	        if($token == 'b2287ad9fcd91c362e58f5b2b3d37858'){
	                return $return_data  = array(
	                    array(
	                        '关注成功',
	                        '感谢你关注成功',
	                        '',
	                        C('site_url') . '/index.php?g=Wap&m=Media&a=myhome&token=' . $token . '&openid=' .$openid
	                    )
	                );
		}else{
	          echo '';exit;
		}
            }else{
                echo '';exit;
            }
        }else{
	     if($token == 'b2287ad9fcd91c362e58f5b2b3d37858'){
	             return $return_data  = array(
	                    array(
	                        '关注成功',
	                        '您已关注',
	                        '',
	                        C('site_url') . '/index.php?g=Wap&m=Media&a=myhome&token=' . $token . '&openid=' .$openid
	                    )
	            );
	    }else{
	        echo '';exit;
	    }
        }

    }
}




function Getmyscore($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid ){
        $userModel = M('wxuser');
        $userDatas = $userModel->where(array('token' => $token))->find();
        $wxUserModel = M('wxusers');
        $wxUserDatas = $wxUserModel->where(array('uid' => $userDatas['id'], 'openid' => $openid))->find();
        $refre = M('Repair_agent')->where(array('id'=>$id))->find();
        if($refre){
            $res = M('Repair_user')->where(array('wxuser_id'=>$userDatas['id'],'wxusers_id'=>$refre['wxusers_id']))->find();
            if($res['referee'] == 0 || $res['referee'] == null){
                M('Repair_user')->where(array('wxuser_id'=>$userDatas['id'],'wxusers_id'=>$wxUserDatas['id']))->save(array('referee'=>$refre['wxusers_id']));
                M('Repair_agent')->where(array('id'=>$id))->setInc('score',5);
                return $return_data  = array(
                    array(
                        '推荐关注成功',
                        '推荐关注将获得5积分',
                        '',
                        C('site_url') . '/index.php?g=Wap&m=Repair&a=wxCenter&token=' . $token . '&wecha_id=' .$openid
                    )
                );
            }else{
                echo '';exit;
            }
        }else{
            echo '';exit;
        }

    }
}

/*
员工二维码
*/

function Getservicestaff($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid){
        $kdata = M('Staff')->field('id,name,staff_id,staff_logo')->where(array('token'=>$token,'id'=>$id))->find();
        return $return_data  = array(
            array(
                '欢迎对工号为'.$kdata['staff_id'].'的'.$kdata['name'].'评价',
                '',
                C('site_url').$kdata['staff_logo'],
                C('site_url') . '/index.php?g=Wap&m=ServicestoreNew&a=evaluation&token=' . $token . '&openid=' .$openid. '&staffid='.$id
            )
        );
    }
}

/*
 * 台铃首次扫二维码关注
 * */
function TailgActive($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid){
        return $return_data  = array(
            array(
                '欢迎加入我们的平台',
                '',
                '',
                C('site_url') . '/index.php?g=Wap&m=Serviceactive&a=index&token=' . $token . '&openid=' .$openid.'&cid='.$id
            )
        );
    }
}


/*
 * 微信收款首次扫二维码关注
 * */
function Weipayapp($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($token && $openid){
        return $return_data  = array(
            array(
                '点击支付',
                '',
                C('site_url') . '/tpl/static/weipay.jpg',
                C('site_url') . '?g=Wap&m=Weipayapp&a=index&token=' . $token
            )
        );
    }
}


/*
 * 易配单会员二维码
 * */
function Getypduser($secenid,$token,$openid){
    $id = substr($secenid,3);
    if($id && $token && $openid){
        $kdata =  M('Media_users')->where(array('token'=>$token,'id'=>$id))->find();
        return $return_data  = array(
            array(
                '欢迎加入我们的平台',
                '',
                '',
                C('site_url') . '/index.php?g=Wap&m=Medias&a=myhome&token=' . $token . '&openid=' .$openid. '&from_openid='.$kdata['openid'].'&type=1'
            )
        );
    }
}




function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
    if(function_exists("mb_substr")){
        if ($suffix && strlen($str)>$length)
            return mb_substr($str, $start, $length, $charset)."...";
        else
            return mb_substr($str, $start, $length, $charset);
    }
    elseif(function_exists('iconv_substr')) {
        if ($suffix && strlen($str)>$length)
            return iconv_substr($str,$start,$length,$charset)."...";
        else
            return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}





















