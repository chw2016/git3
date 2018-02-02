<?php
/*
 * wap端
 * 微信服务号粉丝功能 * 
 */

class RepairfanAction extends BaseAction{
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
    
    /*电子保修单显示*/
    public function wxWarranty(){        
        if(empty($this->token) || empty($this->openid)) exit('非法操作！');
        $eleClass = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>0))->order('sort')->select();//M('repair_class')->where(array('wxuser_id'=>$this->wxuserDatas['id'],'pid'=>0))->order('sort')->select();        
        $this->assign('bigClass',$eleClass);
        $this->assign('type',2);       
        $this->display();
    }
    
    /*电子保修单插入数据，和订单共用一张表，repairorder_type为2是是电子保修单*/
    public function wxWarn(){
        //echo '<pre>';
        // var_dump($eleClass);exit;        
       if(IS_POST){
            $jsonData = $_POST['jsonstr'];
            $jsonData = htmlspecialchars_decode($jsonData);
            $jsonData = json_decode($jsonData);
            $type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : 1;
            //获取当前的时间，格式为：18:00:00，用户获取下单时间
            $time = date('H:i:s');
            if($type == 2){
                $insertData = array(
                    //'wxuser_id' => $this->userDatas['id'],
                    //'wxusers_id' => $this->wxuserDatas['id'],
                    //'oder_nid' => $this->getSn(),
                    'wxuser_id'=>$this->userDatas['id'],
                    'wxusers_id'=>$this->wxuserDatas['id'],
                    'order_nid'=>$this->getSn(),
                    'appoint_day' => $jsonData[0]->repairdate,
                    'appoint_time' => $time,
                    'order_time' => strtotime(trim($jsonData[0]->repairdate).' '.$time),
                    'grab_time' => strtotime(trim($jsonData[0]->repairdate).' '.$time),
                    'finish_time' => strtotime(trim($jsonData[0]->repairdate).' '.$time),
                    'order_name' => trim($jsonData[0]->uname),
                    'order_tel' => trim($jsonData[0]->uphone),
                    'order_address' => trim($jsonData[0]->uaddress),
                    'repair_ele' => trim($jsonData[0]->electric),
                    'service_cont' => trim($jsonData[0]->scontent) ,
                    'type' => trim($jsonData[0]->repairtype),
                    'is_replacement' => trim($jsonData[0]->replacement),
                    'repairfee' => trim($jsonData[0]->repairfee),
                    'period' => trim($jsonData[0]->warrantyperiod),
                    'fault_info' =>  trim($jsonData[0]->descrition),
                    'staff_tel' => trim($jsonData[0]->techphone),
                    'repairorder_type'=> 2,
                    'status' => 2,
                    'is_read' => 1                    
                );
                
                //判断技师手机号是否存在
                $repairStaff = M('repair_staff')->where(array('staff_telphone'=>trim($jsonData[0]->techphone)))->find();
                $insertData['agent_id'] = $repairStaff['agent_id'];
                //echo '<pre>'; var_dump($repairStaff);exit;
                if($repairStaff){
                    //插入数据
                    $insertBack = M('repair_order')->add($insertData);                   
                    if($insertBack){
                        echo $this->encode(array('status'=>100,'info'=>'电子保修单录入成功！','url'=>'index.php?g=Wap&m=Repair&a=wxOrder&type=1&token='.$this->token.'&openid='.$this->openid));
                        //向技师发送普通消息
                        $wxfan = M('wxusers')->where(array('id'=>$repairStaff['wxusers_id']))->find();
                        
                        $notichcontent = '请确认用户的电子保修单！';
                        $postdata = array('openid'=>$wxfan['openid'],'token'=>$this->token,'content'=>$notichcontent);
                        $url = C('site_url').'index.php?g=Home&m=Auth&a=sendTextMsg';
                        $data = $this->api_notice_increment($url,http_build_query($postdata));     
                    }else{
                        echo $this->encode(array('status'=>1,'info'=>'电子保修单录入失败，请重试！'));
                    }
                }else{
                    echo $this->encode(array('status'=>6,'info'=>'技师手机号有误，请重新输入！！'));
                }
                
            }
       }
    }
    
    /*用户订单操作*/
    public function userOrderOpr(){

    }
    
    /*************************************************以下为公共函数*************************************************/
    /*
     * 生成唯一订单SN
     */
    public function getSn(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}