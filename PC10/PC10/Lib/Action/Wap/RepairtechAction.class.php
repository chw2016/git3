<?php
/*
 * 技师相关功能、wap
 */
class RepairtechAction extends BaseAction{
    public $token;
    public $openid;
    public $userModel;
    public $userDatas;
    public $wxuserModel;
    public $wxuserDatas;
    public $weiobj;
    
    public static $treelist = array();
    
    public function __construct(){
        parent::_initialize();
        if(!session('?token') || !session('?openid')){
            session('token',$_REQUEST['token']);
            session('openid',$_REQUEST['openid']);
        }
        
        $this->token = $_REQUEST['token'];
        $this->openid = $_REQUEST['openid'];
        
        $this->userModel = M('wxuser');
        $this->userDatas = $this->userModel->where(array('token'=>$this->token))->find();    
        
        $this->wxUserModel = M('wxusers');       
        $this->wxuserDatas = $this->wxUserModel->where(array('uid'=>$this->userDatas['id'],'openid'=>$this->openid))->find();
        
        
        $this->assign('token',$this->token);
        $this->assign('openid',$this->openid);
    }
    
    /*客留单*/
    public function reception(){
        if(empty($this->token) || empty($this->openid)) exit('非法操作！');
        $eleClass = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>0))->order('sort')->select();//M('repair_class')->where(array('wxuser_id'=>$this->wxuserDatas['id'],'pid'=>0))->order('sort')->select();        
        $this->assign('bigClass',$eleClass);
        //$this->assign('type',2);       
        $this->display();
    }
    
    /*技师订单操作*/
    public function techOrderOpr(){
                
        if(IS_POST){
            //插入客留单数据
            $jsonData = $_POST['jsonstr'];
            $jsonData = htmlspecialchars_decode($jsonData);
            $jsonData = json_decode($jsonData);
            $type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : 1;
            //获取当前的时间，格式为：18:00:00，用户获取下单时间
            $time = date('H:i:s');
            if($type == 6){
                $insertData = array(
                    'wxuser_id'=>$this->userDatas['id'],
                    //'wxusers_id'=>$this->wxuserDatas['id'],
                    'order_nid'=>$this->getSn(),
                    //'finish_time' => strtotime(trim($jsonData[0]->repairdate).' '.$time),
                    'order_time'=>strtotime(trim($jsonData[0]->receivedate).' '.$time),
                    'order_name' => trim($jsonData[0]->uname),
                    'order_tel' => trim($jsonData[0]->uphone),
                    'order_address' => trim($jsonData[0]->uaddress),
                    'brand' => trim($jsonData[0]->brand),
                    'repair_ele' => trim($jsonData[0]->electric),
                    'service_cont' => trim($jsonData[0]->scontent) ,
                    'type' => trim($jsonData[0]->repairtype),
                    //'is_replacement' => trim($jsonData[0]->replacement),
                    'repairfee' => trim($jsonData[0]->repairfee),
                    'appoint_day' => trim($jsonData[0]->receivedate),
                    'appoint_time' => $time,
                    'order_time' => strtotime(trim($jsonData[0]->receivedate).' '.$time),
                    'grab_time' => strtotime(trim($jsonData[0]->receivedate).' '.$time),
                    'appoint_get_date' => strtotime(trim($jsonData[0]->completedate).' '.$time),
                    //'period' => trim($jsonData[0]->warrantyperiod),
                    'fault_info' =>  trim($jsonData[0]->descrition),
                    'staff_tel' => trim($jsonData[0]->techphone),
                    'repairorder_type'=> 3,
                    'status' => 2,
                    'is_read' => 1
                );
        
                //客留单绑定技师
                $repairStaff = M('repair_staff')->where(array('staff_telphone'=>trim($jsonData[0]->techphone)))->find();
                $insertData['agent_id'] = $repairStaff['agent_id'];
                
                //客留单绑定用户
                $repairUser = M('repair_user')->where(array('bind_phone'=>trim($jsonData[0]->uphone)))->find();
                if($repairUser){
                    $insertData['wxusers_id'] = $repairUser['wxusers_id'];
                }
                //echo '<pre>'; var_dump($repairStaff);exit;
               //插入数据
                $insertBack = M('repair_order')->add($insertData);
                if($insertBack){
                    echo $this->encode(array('status'=>100,'info'=>'电子客留单录入成功！','url'=>'index.php?g=Wap&m=Repair&a=myOrder&type=1&token='.$this->token.'&openid='.$this->openid));
                    //向用户发送普通消息
                    if($repairUser){
                     //发送服务号普通消息
                        $wxfan = M('wxusers')->where(array('id'=>$repairUser['wxusers_id'],'uid'=>$this->userDatas['id']));
                        $notichcontent = '1号服务微信技师已创建您的客留单，请到【用户专区】-【我的主页】-【订单】-【服务中】查看。';
                        $postdata = array('openid'=>$wxfan['openid'],'token'=>$this->token,'content'=>$notichcontent);
                        $url = C('site_url').'index.php?g=Home&m=Auth&a=sendTextMsg';
                        $data = $this->api_notice_increment($url,http_build_query($postdata));
                    }else{
                         //发送手机短息
                                
                    }
                        
               }else{
                        echo $this->encode(array('status'=>1,'info'=>'电子客留单录入失败，请重试！'));
               }
            }
        }
    }

    /*获取数据*/
    public function getData(){
       if(IS_POST){
            $type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : 1;
            
            if($type == 6){
                $techInfo = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxuserDatas['id']))->find();
                //var_dump($techInfo);exit;
                if($techInfo){
                    echo $this->encode(array('status'=>100,'info'=>$techInfo['staff_telphone']));
                }
            }
        }
    }    
    
    
    /*************************************************以下为公共函数*************************************************/
    /*获取电子保修单的永久二维码*/
    public function warrantyCode() {
        $parament = '{"action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 666}}}';
        /*获取access_token*/
        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        if($api){
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
            $json = json_decode(file_get_contents($url_get));
            $access_token = $json->access_token;
            $imgSource = $this->creatTicket($access_token, $parament);
        }
        $this->assign('imgUrl', $imgSource['header']['url']);
        $this->display();
    }
    
    /*The two-dimensional code  BY NICK  */
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
    
    /*
     * 生成唯一订单SN
     */
    public function getSn(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }    
}
