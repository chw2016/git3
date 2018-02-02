<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	static  public $APPID;
	//受理商ID，身份标识
    static  public $MCHID;
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
    static  public  $KEY;
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
    static  public  $APPSECRET;
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
    static  public  $JS_API_CALL_URL;
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
    static  public  $SSLCERT_PATH;
    static  public  $SSLKEY_PATH;
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
    static  public  $NOTIFY_URL;

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
    static  public  $CURL_TIMEOUT;

    static public function set_config($APPID,$MCHID,$KEY,$APPSECRET,$JS_API_CALL_URL,$SSLCERT_PATH,$SSLKEY_PATH,$NOTIFY_URL,$CURL_TIMEOUT){
        
        WxPayConf_pub::$APPID = $APPID;
        WxPayConf_pub::$MCHID = $MCHID;
        WxPayConf_pub::$KEY = $KEY;
        WxPayConf_pub::$APPSECRET = $APPSECRET;
        WxPayConf_pub::$JS_API_CALL_URL = $JS_API_CALL_URL;
        WxPayConf_pub::$SSLCERT_PATH = $SSLCERT_PATH;
        WxPayConf_pub::$SSLKEY_PATH = $SSLKEY_PATH;
        WxPayConf_pub::$NOTIFY_URL = $NOTIFY_URL;
        WxPayConf_pub::$CURL_TIMEOUT = $CURL_TIMEOUT;
        
        /*
        WxPayConf_pub::$APPID = 'wx8888888888888888';
        WxPayConf_pub::$MCHID = '18888887';
        WxPayConf_pub::$KEY = '48888888888888888888888888888886';
        WxPayConf_pub::$APPSECRET = '48888888888888888888888888888887';
        WxPayConf_pub::$JS_API_CALL_URL = 'http://www.xxxxxx.com/demo/js_api_call.php';
        WxPayConf_pub::$SSLCERT_PATH = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_cert.pem';
        WxPayConf_pub::$SSLKEY_PATH = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_key.pem';
        WxPayConf_pub::$NOTIFY_URL = 'http://www.xxxxxx.com/demo/notify_url.php';
        WxPayConf_pub::$CURL_TIMEOUT = 30;
	*/
    }
}
	
?>