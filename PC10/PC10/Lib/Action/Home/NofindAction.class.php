<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-7-11
 * Time: 上午9:15
 * To change this template use File | Settings | File Templates.
 */
class NofindAction extends Action{

    public $openid = null;
    public $from_openid = null;
    public $token = null;

    protected function _initialize()
    {
        $this->openid = $_GET['openid'];
        $this->from_openid = $_GET['from_openid'];
        $this->token = $_GET['token'];
    }

    public function isover(){
        define('STATICS', C('site_url').TMPL_PATH . 'static');
        $this->display();
    }

    public function isnotmember(){
        define('STATICS', C('site_url').TMPL_PATH . 'static');
        $this->assign('openid',$_GET['openid']);
        $this->assign('token',$_GET['token']);
        $this->display();
    }

    public function isnotsub(){
        define('STATICS', C('site_url').TMPL_PATH . 'static');
        $token = $_GET['token'];
        if(!empty($token)) {
            $Wxuser = M('Wxuser')->where(array('token' => $token))->find();
            if($token == '70f38fdd1c5189197bc275469aa9b481'){
                $aWhere = array(
                    'token'=>$token,
                    'status'=>0
                );
                $aArea =   M('Service_active')
                    ->where($aWhere)
                    ->order('id desc')
                    ->select();
                $aAreas= $aArea[0];
                $oUsercode = new Code($this->token,'186'.$aAreas['id']);
                $sImageUrl = $oUsercode->getLSCode();
            }else{
                $sImageUrl = $this->getCode();
                $this->assign("image", $sImageUrl);//生成二维码图片
            }
            $this->assign('wxuser',$Wxuser);
            $this->assign('image',$sImageUrl);
            $this->display();
        }

    }

    /**
     * @param string $pid
     * @return mixed生成二维码图片
     */
    public function getCode() {
        if($this->from_openid){
            $this->openid = $this->from_openid;
        }
        $userinfo=M("Wxusers")->field("id")->where(array("openid"=>$this->openid))->find();

        $parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 500'.$userinfo['id'].'}}}';

        /*获取access_token*/

        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        //p($api);
        if($api){

            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];


            $json = json_decode(file_get_contents($url_get));
            $access_token = $json->access_token;
           // $access_token = "GVkLr2R7pPpgnmCHovoSkJqYugUzNA1y4crUwpZlenhMk80qRD9ijinh2O8BwL3ACwqoCGxohSlath0OJK5AslJH7dSnNx9foGvJ_UjTEdU";

            $imgSource = $this->creatTicket($access_token, $parament);

        }

        return $imgSource['header']['url'];

    }

    /**
     * @param $token
     * @param $parament
     * @return array   生成二维码
     */
    public function creatTicket($token, $parament) {
        /*发送数据到微信服务器端并获取数据*/

        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";

        $result = $this->api_notice_increment($url, $parament);
        $jsonInfo = json_decode($result, true);
        $ticket = $jsonInfo['ticket'];

        /*根据ticket获取图片资源*/

        $url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url2);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_NOBODY, 0);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpInfo = curl_getinfo($ch);
        return array_merge(array('body'=>$package), array('header'=>$httpInfo));
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




}