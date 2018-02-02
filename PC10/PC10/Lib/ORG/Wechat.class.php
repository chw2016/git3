<?php
class Wechat
{
    private $data = array();
    public  $res=null;
    public  $wxusers=null;

    public function __construct($token)
    {
        $userModel = M('Wxuser');
        $res = $userModel->where(array('token'=>$token))->find();
        $this->res =  $res;
        if($res['status'] == 0){
            if($this->auth($token)){
                $userModel->id = $res['id'];
                $userModel->status = 1;
                $userModel->bind_time = time();
                if($userModel->save()){
                    exit($_GET['echostr']);

				}

            }else{
                return false;
                exit;
            }
        }else {
            $xml = file_get_contents("php://input");
            //$xml = file_put_contents('1.txt',$xml,FILE_APPEND);
            $xml = new SimpleXMLElement($xml);
            $xml || exit;
            foreach ($xml as $key => $value) {
                $this->data[$key] = strval($value);
            }
            $wxusersModel = M('Wxusers');
            $wxusers = $wxusersModel->where(array('uid'=>$res['id'],'openid'=>$this->data['FromUserName']))->find();
            $this->wxusers =$wxusers;

		import("@.ORG.WXAction.WXAction_Text");
		$Text = new WXAction_Text($this->data, $token);
		$Text->handle();

            if($wxusers['fakeid'] == null){
                if($this->data['MsgType'] == 'text'){
                    if($this->data['Content'] != ''){
                        if(!empty($res['wx_a']) && !empty($res['wx_p'])){
                            $create_time = $this->data['CreateTime'];
                            Vendor('weixin.WX_Remote_Opera');
                            $ro = new WX_Remote_Opera();
                            $token=$ro->test_login($res['wx_a'],$res['wx_p']);
                            if($token){
                                $ro->init($res['wx_a'],$res['wx_p']);
                                $msglist=$ro->getmsglist();
                                foreach($msglist as $k=>$v){
                                    if($v['date_time']==$create_time){
                                        $udata = array();
                                        $contactinfo=$ro->getcontactinfo($v['fakeid']);
                                        $udata['fakeid'] = $v['fakeid'];
                                        $udata['nickname'] = $contactinfo['nick_name'];
                                        $udata['country'] = $contactinfo['country'];
                                        $udata['province'] = $contactinfo['province'];
                                        $udata['city'] = $contactinfo['city'];
                                        $udata['sex'] = $contactinfo['gender'];
                                        $wxusersModel->where(array('id'=>$wxusers['id']))->data($udata)->save();
                                        $ro->getheadimg($v['fakeid']);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }elseif($this->data['MsgType'] == 'image'){
                    import("@.ORG.WXAction.WXAction_Image");
                    $Image = new WXAction_Image($this->data, $token);
                    $Image->handle();
		}
            }
            if($this->data['Content'] != ''){
                $msglistModel = M('Msg_list');
                $mdata = array();
                $mdata['uid'] = $res['id'];
                $mdata['to_openid'] = $this->data['ToUserName'];
                $mdata['from_openid'] = $this->data['FromUserName'];
                $mdata['content'] =$this->data['Content'];
                $mdata['type'] = $this->data['MsgType'];
                $mdata['add_time'] = time();
                $msglistModel->data($mdata)->add();
                if(preg_match('/([0-9]|[a-z])+(@){1}(wapwei)/i',$this->data['Content'],$match)){
                    if($res['has_kefu'] == null ||  $res['has_kefu'] == 0){
                        if(count($match) == 4){
                            if($match[2] == '@'){
                                $temp = explode('@',$match[0]);
                                if($temp[0] == $res['id'].substr($res['token'],0,5)){
                                    if($wxusers['is_kefu'] == 1){
                                        $content = "此公众账号已绑定过绑定个人微信客服 ";
                                        $content = iconv("GBK", "UTF-8", $content);
                                        $content = $content.$wxusers['nickname'];
                                    }else{
                                        if($wxusersModel->where(array('uid'=>$res['id'],'openid'=>$this->data['FromUserName']))->data(array('is_kefu'=>1))->save()){
                                            if($userModel->where(array('token'=>$token))->data(array('has_kefu'=>1))->save()){
                                                $content = "成功绑定个人微信客服:";
                                                $content = iconv("GBK", "UTF-8", $content);
                                                $content = $content.$wxusers['nickname'];
                                            }else{
                                                $content = "公众账号绑定个人微信客服失败,请联系万普微盟官网 QQ:4000188887.";
                                                $content = iconv("GBK", "UTF-8", $content);
                                            }
                                        }else{
                                            $content = "公众账号绑定个人微信客服失败,请联系万普微盟官网 QQ:4000188887.";
                                            $content = iconv("GBK", "UTF-8", $content);
                                        }
                                    }
                                    $this->response($content,'text');
                                }
                            }
                        }
                    }
                }
            }

        }

    }
    public function request()
    {
        return $this->data;
    }
    public function response($content, $type = 'text', $flag = 0)
    {
        $this->data = array(
            'ToUserName' => $this->data['FromUserName'],
            'FromUserName' => $this->data['ToUserName'],
            'CreateTime' => NOW_TIME,
            'MsgType' => $type
        );

        $msglistModel = M('Msg_list');
        $mdata = array();
        $mdata['uid'] = $this->res['id'];
        $mdata['to_openid'] = $this->data['ToUserName'];
        $mdata['from_openid'] = $this->data['FromUserName'];
        $mdata['content'] =$content;
        $mdata['type'] = $this->data['MsgType'];
        $mdata['add_time'] = time();
        $msglistModel->data($mdata)->add();

        $this->$type($content);
        $this->data['FuncFlag'] = $flag;
        $xml                    = new SimpleXMLElement('<xml></xml>');
        $this->data2xml($xml, $this->data);
        exit($xml->asXML());
    }

    public function responsekf()
    {
        $this->data = array(
            'ToUserName' => $this->data['FromUserName'],
            'FromUserName' => $this->data['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => 'transfer_customer_service'
        );

        $xml = new SimpleXMLElement('<xml></xml>');
        $this->data2xml($xml, $this->data);
        exit($xml->asXML());

    }


    private function text($content)
    {
        $this->data['Content'] = $content;
    }
    private function music($music)
    {
        list($music['Title'], $music['Description'], $music['MusicUrl'], $music['HQMusicUrl']) = $music;
        $this->data['Music'] = $music;
    }
    private function news($news)
    {
        $articles = array();
        foreach ($news as $key => $value) {
			//$articles[$key]['Url'] = stripslashes(htmlspecialchars_decode($articles[$key]['Url']));
            list($articles[$key]['Title'], $articles[$key]['Description'], $articles[$key]['PicUrl'], $articles[$key]['Url']) = $value;
            $sBaseSiteUrl = str_replace('http://', '', C('site_url'));
            if(
                strpos($articles[$key]['Url'],'wapwei.com') ||
                strpos($articles[$key]['Url'], $sBaseSiteUrl)
            ){
                $articles[$key]['Url']= $articles[$key]['Url'].'&wecha_id='.$this->data['ToUserName'].'&openid='.$this->data['ToUserName'];
                $articles[$key]['Url'] = stripslashes(htmlspecialchars_decode($articles[$key]['Url']));
            }
            if ($key >= 9) {
                break;
            }
        }
        $this->data['ArticleCount'] = count($articles);
        $this->data['Articles']     = $articles;
    }
    private function data2xml($xml, $data, $item = 'item')
    {
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
    private function auth($token)
    {
        $data = array(
            $token,
            $_GET['timestamp'],
            $_GET['nonce']
        );
        $sign = $_GET['signature'];
        //file_put_contents('1.txt',$sign.'--',FILE_APPEND);
        sort($data,SORT_STRING);
        $signature = sha1(implode($data));
        //file_put_contents('1.txt',$signature,FILE_APPEND);
        if($signature === $sign){
            return true;
        }else{
            return false;
        }
    }
}
?>
