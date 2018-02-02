<?php
class WeChat{
	//属性
	private $_appID;
	private $_appsecret;
	private $_token;

	//构造函数
	public function __construct($appID,$appsecret,$token){
		$this->_appID = $appID;
		$this->_appsecret = $appsecret;
		$this->_token = $token;
	}
	//用curl抓取网页
	private function _request($curl,$https = true,$method="get",$data = null){
		$cu = curl_init();//开启curl函数，返回资源
		curl_setopt($cu,CURLOPT_URL,$curl);//设置访问的url
		curl_setopt($cu,CURLOPT_HEADER,false);//设置不获取头文件
		curl_setopt($cu,CURLOPT_RETURNTRANSFER,true);//只返回页面，不输出
		if($https){
			curl_setopt($cu,CURLOPT_SSL_VERIFYPEER,false);//不做服务器认证
			curl_setopt($cu,CURLOPT_SSL_VERIFYHOST,false);//不做客户端认证
		}
		if($method == 'post'){
			curl_setopt($cu,CURLOPT_POST,1);//设置请求是post方式
			curl_setopt($cu,CURLOPT_POSTFIELDS,$data);//设置post请求数据

		}
		$str = curl_exec($cu);//执行curl，返回结果
		curl_close($cu);//关闭curl
		return $str;
	}
	
    public  function post($url, $post_data = ''){//curl
        $ch = curl_init();
 
        curl_setopt ($ch, CURLOPT_URL, $url);
 
        curl_setopt ($ch, CURLOPT_POST, 1);
 
        if($post_data != ''){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
 
        }
 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
 
        curl_setopt($ch, CURLOPT_HEADER, false);
 
        $file_contents = curl_exec($ch);
		print_r($file_contents);echo 21;die;
        curl_close($ch);
 
        return $file_contents;
 
    }
	public function api_notice_increment($url, $data){
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
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            //curl_close( $ch )
            return $ch;
        }else{
            //curl_close( $ch ) 
            return $tmpInfo;
        }
        curl_close( $ch ) ;
    }
	//创建一个得到getAccesstoken函数
	public function _getAccesstoken(){
		//当前目录下，检查是否有文件存在
		$file ="./accesstoken";
		if(file_exists($file)){
			//获取$file文件内容
			$content = file_get_contents($file);
			//解码
			$content = json_decode($content);
			//判断是否超过有效期
			if(time()-filemtime($file)<$content->expires_in){
				return $content->access_token;
			}
		}
		$content = $this->_request("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->_appID."&secret=".$this->_appsecret);
		
		file_put_contents($file,$content);
		$content = json_decode($content);
		//print_r($content);die;
		return $content->access_token;
	}
	//获取二维码
	//1.首先获取二维码ticket
	//2.在通过ticket获取二维码
	public function _getTicket($expires_secords = 1000,$type="temp",$scene=1){
		if($type=="temp"){
			$QR_SCENE = 'QR_SCENE';
			$data='{"expire_seconds":'.$expires_secords.',"action_name":"QR_STR_SCENE", "action_info":{"scene": {"scene_id":'.$scene.'}}}';
			return $this->api_notice_increment("https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->_getAccesstoken(),$data);
		}else{

		}
	}
}
 ?>