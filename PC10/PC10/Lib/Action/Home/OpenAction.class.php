<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-7-11
 * Time: 上午9:15
 * To change this template use File | Settings | File Templates.
 */
class OpenAction extends Action{

    public $tool = null;
    public $postdata = null;
    public $data = null;
    public $encodingAesKey = null;
    public $token = null;
    public $appId = null;
    public $appsecret_value = null;
    public $webtoken = null;

	public function _initialize() {
        Vendor('weixin.wxBizMsgCrypt');
        $this->encodingAesKey = "6G5T0CZTW964LX48BG6NQY0VS3F1EMXYFGKDI5U23U2";
		$this->token = "D2OALL3";
		$this->appId = "wxe7be6810523b9ea2";
		$this->appsecret_value = "0c79e1fa963cd80cc0be99b20a18faeb";
		$this->tool = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
		$this->postdata = file_get_contents("php://input");
		$this->webtoken = $_GET['token'];


    }

    public function openlogin(){
        if(!$this->webtoken){
            echo '非法请求';
        }else{
            $url = C('site_url').'Home/Open/login/token/'.$this->webtoken;
            echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
        }
    }


    public function index(){
    	//signature=9537a878149615d4f38d2a6a321a4eaf9c34fb32&timestamp=1416907803&nonce=122013420&encrypt_type=aes&msg_signature=e8b3af59d1b8184c578b288e29371c383dc5756b
        $timeStamp = $_GET['timestamp'];
		$nonce = $_GET['nonce'];
        $msg_signature = $_GET['msg_signature'];

        //file_put_contents('2.txt', $_SERVER['QUERY_STRING']);
        //file_put_contents('3.txt', file_get_contents("php://input"));
		// 第三方收到公众号平台发送的消息
		$msg = '';

		$errCode = $this->tool->decryptMsg($msg_signature, $timeStamp, $nonce, $this->postdata, $msg);
		if($errCode == 0){
			$this->setdata($msg);
            if($this->data['InfoType'] == 'component_verify_ticket') {
                if (M('Open_ticket_set')->where(array('AppId' => $this->data['AppId']))->data(array('CreateTime' => $this->data['CreateTime'], 'ComponentVerifyTicket' => $this->data['ComponentVerifyTicket']))->save()) {
                    echo 'success';
                    exit;
                } else {
                    echo '-3';
                }
            }else if($this->data['InfoType'] == 'unauthorized'){
                $wxuserModel = M('Wxuser');
                $res = $wxuserModel->where(array('authorizer_appid'=>$this->data['AuthorizerAppid']))->find();
                if($res){
                    $wxuserModel->where(array('id'=>$res['id']))->save(array('is_auth'=>0,'status'=>0));
                    echo 'success';exit;
                }else{
                    echo '-2';
                }
            }
		}else{
			echo '-1';
		}

    }

    public function login(){
    	$returndata = $this->getAccesstoken();
    	if($returndata['component_access_token']){
    		$recodeurl = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=".$returndata['component_access_token'];
    	    $repostdata = array();
    	    $repostdata['component_appid'] = $this->appId;
    	    $repostdata = $this->encode($repostdata);
    	    $recodedata = $this->api_notice_increment($recodeurl,$repostdata);
    	    $recodedata = json_decode($recodedata,true);
    	    if($recodedata['pre_auth_code']){
    	    	$gotourl = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=".$this->appId."&pre_auth_code=".$recodedata['pre_auth_code']."&redirect_uri=http://v.wapwei.com/Home/Open/start/token/".$this->webtoken;
    			header("Location:".$gotourl);
    		}
    	}
    	
    }

    public function callback(){
        $appid = $_GET['appid'];
        $wxuserModel = M('Wxuser');
        //file_put_contents('2.txt', $_SERVER['QUERY_STRING']);
        //file_put_contents('3.txt', file_get_contents("php://input"));
        //http://v.wapwei.com/Home/Open/callback/appid/wxa78dbfa797889d7a/?signature=c386680598a0587ded505de0fec13122b1a0f283&timestamp=1417081128&nonce=1255895708&encrypt_type=aes&msg_signature=278d7c7837db975f0f42d66621f71d46c41445d1
        $resdata = $wxuserModel->where(array('authorizer_appid'=>$appid))->find();
        if($resdata){
            if($resdata['is_auth'] == 1){
                $timeStamp = $_GET['timestamp'];
                $nonce = $_GET['nonce'];
                $msg_signature = $_GET['msg_signature'];
                //file_put_contents('2.txt', $_SERVER['QUERY_STRING']);
                //file_put_contents('3.txt', file_get_contents("php://input"));
                //file_put_contents('11.txt', file_get_contents("php://input"));
                // 第三方收到公众号平台发送的消息
                $xml_tree = new DOMDocument();
                $xml_tree->loadXML($this->postdata);
                $array_e = $xml_tree->getElementsByTagName('Encrypt');
                $encrypt = $array_e->item(0)->nodeValue;
                $msg = '';

                $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
                $from_xml = sprintf($format, $encrypt);
                $errCode = $this->tool->decryptMsg($msg_signature, $timeStamp, $nonce, $from_xml, $msg);
                //file_put_contents('6.txt', $msg);
                if($errCode == 0) {
                    $this->setdata($msg);
                    
                    $posturl = C('site_url') . "index.php?g=Home&m=Weixin&a=index&token=" . $resdata['token'];
                    $returndata = $this->api_notice_increment($posturl, $msg);
                    //file_put_contents('6.txt', $returndata);
                    $encryptMsg = '';
                    $errCode = $this->tool->encryptMsg($returndata, time(), $nonce, $encryptMsg);
                    if ($errCode == 0) {
                        //file_put_contents('7.txt', $returndata);
                        echo $encryptMsg;
                        exit;
                    }

                }
            }
        }else{
            $timeStamp = $_GET['timestamp'];
            $nonce = $_GET['nonce'];
            $msg_signature = $_GET['msg_signature'];
            //file_put_contents('2.txt', $_SERVER['QUERY_STRING']);
            //file_put_contents('3.txt', file_get_contents("php://input"));
            file_put_contents('12.txt', $_SERVER['QUERY_STRING'].'|',FILE_APPEND);
            file_put_contents('11.txt', file_get_contents("php://input"),FILE_APPEND);
            // 第三方收到公众号平台发送的消息
            $xml_tree = new DOMDocument();
            $xml_tree->loadXML($this->postdata);
            $array_e = $xml_tree->getElementsByTagName('Encrypt');
            $encrypt = $array_e->item(0)->nodeValue;
            $msg = '';

            $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
            $from_xml = sprintf($format, $encrypt);
            $errCode = $this->tool->decryptMsg($msg_signature, $timeStamp, $nonce, $from_xml, $msg);
            //file_put_contents('6.txt', $msg ,FILE_APPEND);
            if($errCode == 0) {
                $this->setdata($msg);
                if($this->data['ToUserName'] == 'gh_3c884a361561'){
                    //file_put_contents('15.txt', $msg ,FILE_APPEND);
                    if($this->data['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT'){
                        $rdata = array(
                            'ToUserName' => $this->data['FromUserName'],
                            'FromUserName' => $this->data['ToUserName'],
                            'CreateTime' => time(),
                            'MsgType' => $this->data['MsgType'],
                            'Content' => 'TESTCOMPONENT_MSG_TYPE_TEXT_callback'
                        );
                        $xml = new SimpleXMLElement('<xml></xml>');
                        $this->data2xml($xml, $rdata);
                        $dxml = $xml->asXML();
                        $encryptMsg = '';
                        $errCode = $this->tool->encryptMsg($dxml, time(), $nonce, $encryptMsg);
                        if ($errCode == 0) {
                            echo $encryptMsg;
                            exit;
                        }

                    }elseif(preg_match('/QUERY_AUTH_CODE/',$this->data['Content'])){
                        file_put_contents('16.txt', $msg,FILE_APPEND);
                        $arr = explode(':',$this->data['Content']);
                        //{"errcode":61003,"errmsg":"component is not authorized by biz acct"}

                        //print_r($this->data);
                        //file_put_contents('16.txt', $this->encode($this->data),FILE_APPEND);
                        $returndata = $this->getAccesstoken();
                        //file_put_contents('16.txt', $this->encode($returndata),FILE_APPEND);
                        //print_r($returndata);
                        
                        if($returndata['component_access_token']){
                            $recodeurl = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=".$returndata['component_access_token'];
                            $repostdata = array();
                            $repostdata['component_appid'] = $this->appId;
                            $repostdata['authorization_code'] = (string)$arr[1];
                            //print_r($repostdata);
                            $repostdata = $this->encode($repostdata);
                            //print_r($repostdata);
                            //file_put_contents('20.txt', $this->encode($returndata),FILE_APPEND);
                            $recodedata = $this->api_notice_increment($recodeurl,$repostdata);
                            //file_put_contents('20.txt', $this->encode($recodedata),FILE_APPEND);
                            $recodedata = json_decode($recodedata,true);
                            if($recodedata['authorization_info']['authorizer_access_token']){
                            //$accesstoken = 'ovcm0rakJz6LEj2YvrrRohYozeAE5NhkihpBG0gvTwHNmhXVh0H2U6wBcxb3NYgoEHI30wQZ3Q6JnriRZ2onl2qfUAYp3ZKK_JX3z10qa7_534KIjU5c9F9EmZ-thyRJ';
                            //if($accesstoken){
                                $openid = $this->data['FromUserName'];
                                //file_put_contents('17.txt', $this->encode($recodedata));
                                $sendurl = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$recodedata['authorization_info']['authorizer_access_token'];
                                $mydata['touser'] = (string)$openid;
                                $mydata['msgtype'] = 'text';
                                $mydata['text'] = array('content'=>$arr[1]."_from_api");
                                $mydata = $this->encode($mydata);
                                $myreturn = $this->api_notice_increment($sendurl,$mydata);
                            }
                            echo '';
                        }

                    }else {
                        $rdata = array(
                            'ToUserName' => $this->data['FromUserName'],
                            'FromUserName' => $this->data['ToUserName'],
                            'CreateTime' => time(),
                            'MsgType' => 'text',
                            'Content' => $this->data['Event'].'from_callback'
                        );

                        $xml = new SimpleXMLElement('<xml></xml>');
                        $this->data2xml($xml, $rdata);
                        $dxml = $xml->asXML();
                        $encryptMsg = '';
                        $errCode = $this->tool->encryptMsg($dxml, time(), $nonce, $encryptMsg);
                        if ($errCode == 0) {
                            echo $encryptMsg;
                            exit;
                        }
                    }

                }else {
                    $posturl = C('site_url') . "index.php?g=Home&m=Weixin&a=index&token=" . $resdata['token'];
                    $returndata = $this->api_notice_increment($posturl, $msg);
                    //file_put_contents('6.txt', $returndata);
                    $encryptMsg = '';
                    $errCode = $this->tool->encryptMsg($returndata, time(), $nonce, $encryptMsg);
                    if ($errCode == 0) {
                        //file_put_contents('7.txt', $returndata);
                        echo $encryptMsg;
                        exit;
                    }
                }
            }
        }



    }

    public function start(){
        # code...
        $auth_code = $_GET['auth_code'];
        $returndata = $this->getAccesstoken();
        if($returndata['component_access_token']){
    		$recodeurl = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=".$returndata['component_access_token'];
    	    $repostdata = array();
    	    $repostdata['component_appid'] = $this->appId;
    	    $repostdata['authorization_code'] = $auth_code;
    	    $repostdata = $this->encode($repostdata);
    	    $recodedata = $this->api_notice_increment($recodeurl,$repostdata);
    	    $recodedata = json_decode($recodedata,true);
    	    if($recodedata['authorization_info']['authorizer_appid']){
    	    	$getdata = array();
    	        $getdata['component_appid'] = $this->appId;
    	        $getdata['authorizer_appid'] = $recodedata['authorization_info']['authorizer_appid'];
    	        $getdata = $this->encode($getdata);
    	    	$geturl="https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=".$returndata['component_access_token'];
    	        $getbackdata = $this->api_notice_increment($geturl,$getdata);
    	        $getbackdata = json_decode($getbackdata,true);
    	        $wxuserModel = M('Wxuser');
                $updata['wxname'] = $getbackdata['authorizer_info']['alias'];
                $updata['name'] = $getbackdata['authorizer_info']['nick_name'];
                $updata['headpicurl'] = $getbackdata['authorizer_info']['head_img'];
                $updata['service_type_info'] = $getbackdata['authorizer_info']['service_type_info']['id'];
                $updata['verify_type_info'] = !empty($getbackdata['authorizer_info']['verify_type_info']['id']) ? $getbackdata['authorizer_info']['verify_type_info']['id'] : '0';
                $updata['wx_openid'] = $getbackdata['authorizer_info']['user_name'];
                $updata['authorizer_appid'] = $getbackdata['authorization_info']['authorizer_appid'];
                $updata['is_auth'] = 1;
                $updata['status'] = 1;
                $updata['authorizer_access_token'] = $recodedata['authorization_info']['authorizer_access_token'];
                $updata['authorizer_refresh_token'] = $recodedata['authorization_info']['authorizer_refresh_token'];
                $updata['auth_time'] = time();
                if($wxuserModel->where(array('token'=>$this->webtoken))->save($updata)){
                    header("Location:".C('site_url')."index.php?g=User&m=Home&a=index&token=".$this->webtoken);
                }else{
                    echo '授权失败';
                }
    	    }
    	}

    }

    public function setdata($xml){
    	$xml = new SimpleXMLElement($xml);
        $xml || exit;
        foreach ($xml as $key => $value) {
            $this->data[$key] = strval($value);
        }	
    }

    public function getAccesstoken(){
    	$url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
    	$data = array();
    	$data['component_appid'] = $this->appId;
    	$data['component_appsecret'] = $this->appsecret_value;
    	$res = M('Open_ticket_set')->where(array('AppId'=>$this->appId))->find();
    	if($res['ComponentVerifyTicket'] != null){
    		$data['component_verify_ticket'] = $res['ComponentVerifyTicket'];
    	}
    	$data = $this->encode($data);
    	$returndata = $this->api_notice_increment($url,$data);
    	$returndata = json_decode($returndata,true);
    	return $returndata;
    }

    public function api_notice_increment($url, $data){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }else{
            return $tmpInfo;
        }
    }

    public function encode($var) {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false'; // Lowercase necessary!
            case 'integer':
            case 'double':
                return sprintf( '"%s"', $var);
            case 'resource':
            case 'string':
                return '"'. str_replace(array("\r", "\n", "\t", '\\\'', "/"),
                    array('\r', '\n', '\t', '\'', '\\/'),
                    addslashes($var)) .'"';
            case 'array':
                // Arrays in JSON can't be associative. If the array is empty or if it
                // has sequential whole number keys starting with 0, it's not associative
                // so we can go ahead and convert it as an array.
                if ( empty ($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                    $output = array();
                    foreach ($var as $v) {
                        $output[] = $this->encode($v);
                    }
                    return '['. implode(',', $output) .']';
                }
            // Otherwise, fall through to convert the array as an object.
            case 'object':
                $output = array();
                foreach ($var as $k => $v) {
                    $output[] =  $this->encode(strval($k)) .':'.  $this->encode($v);
                }
                return '{'. implode(',', $output) .'}';
            default:
                return 'null';
        }
    }

    private function data2xml($xml, $data, $item = 'item'){
        foreach ($data as $key => $value) {
            is_numeric($key) && $key = $item;
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node  = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }

}