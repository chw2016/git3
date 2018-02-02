<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/1/20
 * Time: 10:51
 */
class ServicestoreNewAction extends BaseAction{
    public $token;
    public $wx_openid;
    public $wxUsersModel;
    public $userModel;
    public $storeModel;


    public function _initialize() {
        parent::_initialize();

        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"MicroMessenger")) {
            //	echo '此功能只能在微信浏览器中使用';exit;
        }

        if ((!session('?token')) || (!session('?openid'))) {
            session('token', $_REQUEST['token']);
            session('wecha_id', $_REQUEST['wecha_id']);
        }
        $this->token =  $_REQUEST['token'];
        $this->wx_openid =  $_REQUEST['wecha_id'];
        $this->wxUsersModel = M('wxusers');
        $this->userModel = M('wxuser');
        $this->storeModel = M('Service_store');
        $this->assign('token',$this->token);
        $this->assign('wecha_id', $this->wx_openid);

    }
    /*门店列表页*/
    public function index(){
        $condition['token'] = $this->token;
        $condition['type'] = $_GET['type'];
        $store = $this->storeModel->where($condition)->select();
      //  p($store);
        $this->assign('store',$store);
       //p($store);
        $this->display();
    }
    public function baidu_map(){
        $condition['token'] = $this->token;
        $condition['id'] = intval($_GET['store_id']);
        $result= $this->storeModel->where($condition)->find();
        $this->assign('result',$result);
        $this->display();
    }
/*生成会员号*/
    public function vip_name(){
        $vip_name = abs(crc32(microtime(true).rand(100,999)));
        if (strlen($vip_name) < 10) {
            $vip_name = str_pad($vip_name, 10, '0', STR_PAD_RIGHT);
        }else{
            $vip_name = substr($vip_name, 0, 10);
        }
        return $vip_name;
    }
    /*会员资料：
     1、首先判断是不是会员；
     2、不是会员则需要进行注册；
     3、是会员则显示会员信息，可以对其进行修改*/
    public function register(){


        $userModel = M('wxuser');
        $userDatas = $userModel->where(array('token'=>$this->token))->find();
        $wxUserModel = M('wxusers');
        $wxUserDatas = $wxUserModel->where(array('uid'=>$this->userDatas['id'], 'openid'=>$this->openid))->find();
        //if($wxUserDatas){
            $profile = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
            if($profile){

                if(IS_POST){
                    $insertDatas['car_username'] =isset($_POST['car_username'])?$_POST['car_username']:'' ;
                    $insertDatas['user_phone'] = isset($_POST['user_phone'])?$_POST['user_phone']:'';
                    $insertDatas['car_frame'] = isset($_POST['car_frame'])?$_POST['car_frame']:'';
                    $insertDatas['wxusers_id'] = isset($wxUserDatas['id'])?$wxUserDatas['id']:'';
                    $insertDatas['wxuser_id'] = isset($userDatas['id'])?$userDatas['id']:'';
                    $insertDatas['type'] = isset($_POST['type'])?$_POST['type']:'';
                    $insertDatas['register_time'] = time();
                    $insertDatas['is_bind'] = 0;
                    $insertInfo = M('Service_profile')->where(array('id'=>$profile['id']))->save($insertDatas);
                    if($insertInfo){
                        if($_POST['type']==2){
                            $wxuser_id = M('Wxuser')->where(array('token'=>$this->token))->getField('id');
                            $staffs = M('Staff')->where(array('wxuser_id'=>$wxuser_id,'openid'=>$this->openid))->find();
                            if(!$staffs){
                                M('Staff')->add(array(
                                    'wxuser_id'=>$wxuser_id,
                                    'openid'=>$_POST['openid'],
                                    'name' => $_POST['car_username'],
                                    'telephone' =>$_POST['user_phone']
                                ));
                            }


                        }
                        if($_POST['province'] == '昆明市'){

                            $frame = M('Service_frame')->where(array('token'=>$this->token,'frame'=>$insertDatas['car_frame'],'type'=>0))->find();

                            if($insertDatas['car_username'] && $insertDatas['user_phone'] && $frame){
                                M('Service_frame')->where(array('token'=>$this->token,'frame'=>$insertDatas['car_frame']))->save(array('type'=>1,'openid'=>$this->openid));
                                $cash = new cash($this->token, 21, $this->openid);
                                $cash->cash_info();
                            }else{
                                $this->ajaxReturn(array('status' => 1, 'info' => '此车架号已被注册', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=register&token=' . $this->token . '&openid=' . $this->openid));
                            }

                            $this->ajaxReturn(array('status'=>100, 'info'=>'修改成功，并获取红包一个！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=register&token='.$this->token.'&openid='.$this->openid));

                        }else{
                            $this->ajaxReturn(array('status'=>100, 'info'=>'修改成功','url'=>'index.php?g=Wap&m=ServicestoreNew&a=register&token='.$this->token.'&openid='.$this->openid));
                        }


                    }else{
                        $this->ajaxReturn(array('status'=>1, 'info'=>'您没有修改任何信息！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=register&token='.$this->token.'&openid='.$this->openid));
                    }

                }else{
                    $this->assign('data',$profile);
                }
            }else{
                if(IS_POST){
                    $insertDatas['car_username'] =isset($_POST['car_username'])?$_POST['car_username']:'' ;
                    $insertDatas['user_phone'] = isset($_POST['user_phone'])?$_POST['user_phone']:'';
                    $insertDatas['car_frame'] = isset($_POST['car_frame'])?$_POST['car_frame']:'';
                    $insertDatas['wxusers_id'] = isset($wxUserDatas['id'])?$wxUserDatas['id']:'';
                    $insertDatas['wxuser_id'] = isset($userDatas['id'])?$userDatas['id']:'';
                    $insertDatas['vip_name'] = $this->vip_name();
                    $insertDatas['token'] = isset($_POST['token'])?$_POST['token']:'';
                    $insertDatas['openid'] = isset($_POST['openid'])?$_POST['openid']:'';
                    $insertDatas['register_time'] = time();
                    $insertDatas['type'] = isset($_POST['type'])?$_POST['type']:'';
                    $insertDatas['is_bind'] = 0;
                   /*if(M('Service_profile')->where(array('token'=>$insertDatas['token'],'user_phone'=>$insertDatas['user_phone']))->find()){
                       $this->ajaxReturn(array('status'=>2, 'info'=>'该手机号已注册！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=register&token='.$this->token.'&openid='.$this->openid));exit;
                   }*/

                    if(M('Service_profile')->where(array('token'=>$insertDatas['token'],'openid'=>$insertDatas['openid']))->find()){
                        $this->ajaxReturn(array('status'=>2, 'info'=>'您已经是会员了，不需注册！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=register&token='.$this->token.'&openid='.$this->openid));
                    }else{
                        if(M('Service_profile')->where(array('token'=>$this->token,'user_phone'=>$_POST['user_phone']))->find()){
                            $this->ajaxReturn(array('status'=>1, 'info'=>'该手机号码已经被注册！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=register&token='.$this->token.'&openid='.$this->openid));
                        }else {

                            $insertInfo = M('Service_profile')->data($insertDatas)->add();
                            if ($insertInfo) {
                                if($_POST['type']==2){
                                    $wxuser_id = M('Wxuser')->where(array('token'=>$this->token))->getField('id');
                                    M('Staff')->add(array(
                                        'wxuser_id'=>$wxuser_id,
                                        'openid'=>$_POST['openid'],
                                        'name' => $_POST['car_username'],
                                        'telephone' =>$_POST['user_phone']
                                    ));

                                }
                                if($_POST['province'] == '昆明市'){
                                    $infose = M('Service_profile')->where(array('token'=>$this->token))->order('id desc')->find();
                                    if($infose['car_username'] && $infose['user_phone']){
                                        $cash = new cash($this->token, 19, $this->openid);
                                        $cash->cash_info();
                                    }
                                    $frame = M('Service_frame')->where(array('token'=>$this->token,'frame'=>$infose['car_frame'],'type'=>0))->find();

                                    if($infose['car_username'] && $infose['user_phone'] && $frame){
                                        M('Service_frame')->where(array('token'=>$this->token,'frame'=>$infose['car_frame']))->save(array('type'=>1,'openid'=>$this->openid));
                                        $cash = new cash($this->token, 21, $this->openid);
                                        $cash->cash_info();
                                    }else{
                                        $this->ajaxReturn(array('status' => 1, 'info' => '此车架号已被注册', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=register&token=' . $this->token . '&openid=' . $this->openid));
                                    }
                                    $this->ajaxReturn(array('status' => 100, 'info' => '注册成功,并获取红包！', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=order&token=' . $this->token . '&openid=' . $this->openid));
                                }else{
                                    $this->ajaxReturn(array('status' => 100, 'info' => '注册成功', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=order&token=' . $this->token . '&openid=' . $this->openid));
                                }

                            } else {
                                $this->ajaxReturn(array('status' => 1, 'info' => '注册失败', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=register&token=' . $this->token . '&openid=' . $this->openid));
                            }
                        }
                    }
                }
            }

//        }else{
//            echo "亲，您还没关注我们哦！";
//        }
        $this->display();
    }

    /*订单相关页面
    1、下单页面；
    a、判断是否会员；yes进入下单页面进行下单，no提示不是会员，提示请注册会员，点击进入注册页面。
    2、下单记录页面
    记录会员的订单信息，如果有订单这在进行就显示正在处理的实时情况。实现30S刷一次新。
    */
    //下单页面
    public function order(){
        /*默认情况下的姓名与联系方式*/
        $profile = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign('profile',$profile);
        /*print(date('YmdHis'));
        echo '<br/>';
        print_r(time());exit;*/
       
        if($profile){
            if(IS_POST){
                $condition['token'] = $_POST['token'];
                $condition['vip_name'] = $profile['vip_name'];
                $condition['openid'] = $_POST['openid'];
                $condition['orderID'] = date('ymdHis').rand(1000,2000);//订单号
                $condition['oname'] = $_POST['oname'];
                $condition['ophone'] = $_POST['ophone'];
                if($_POST['reason'] == 0){
                    $condition['reason'] = "断电并离店很远";
                }elseif($_POST['reason'] == 1){
                    $condition['reason'] = "扎胎";
                }else{
                    $condition['reason'] = "无法启动并离店很远";
                }
                $condition['oaddress'] = $_POST['address'];
                $condition['otime'] = time();
                $condition['status'] = 0;
                if($profile['car_frame']) {
                    /*自动分配服务网店*/
                    $newtime = intval(date("Hi"));
                    $aWhere['token'] = $this->token;
                    $aWhere['type'] = 0;
                    $aWhere['zid']=array('neq','');
                    if($newtime>1800 || $newtime<830){
                        $aWhere['rank'] = 1;
                    }
                    $aService = M('Service_store')->where($aWhere)->select();
                    foreach($aService as $k=>$val){
                        $aService[$k]['jl']=floor(getdistance($_POST['lng'],$_POST['lat'],$val['longitude'],$val['latitude']));
                        $aService[$aService[$k]['jl']."_".$k]=$aService[$k];
                        unset($aService[$k]);
                    }
                    ksort($aService, SORT_NUMERIC);
                    WL('distant' . print_r($aService, true) . ', post:' . print_r($_POST, true));
                    $frernt = reset($aService);
                    $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
                    $person = M('Staff')->where(array('wxuser_id'=>$wxuser['id'],'id'=>$frernt['zid']))->find();
                    $condition['storeID'] = $frernt['id'];
                    $condition['status'] = 1;
                    $condition['staffID'] = $person['id'];
                    if($person){
                        $addto = M('Service_orders')->data($condition)->add();
                    }else{
                        $this->ajaxReturn(array('status' => 0, 'info' => '呼救失败', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=order&token=' . $this->token . '&openid=' . $this->openid));
                    }
                    if ($addto) {

                        msg($this->token,$person['openid'],$person['name'].'站长：您好，在'.$condition['oaddress'].'附近有电话为'.$condition['ophone'].'的'.$profile['car_username'].'发出'.$condition['reason'].'的请求，敬请快速安排人员服务。'."
                        <a href='http://api.map.baidu.com/marker?location=".$_POST['lat'].",".$_POST['lng']."&title=这里&name=这里&content=这里&output=html&src=weiba|weiweb#' style='width: 100%'>查看地图</a>");
                        $this->ajaxReturn(array('status' => 1, 'info' => '呼救成功', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=orderInfo&token=' . $this->token . '&openid=' . $this->openid));
                    } else {
                        $this->ajaxReturn(array('status' => 0, 'info' => '呼救失败', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=order&token=' . $this->token . '&openid=' . $this->openid));
                    }
                }else{
                    $this->ajaxReturn(array('status' => 2, 'info' => '由于您还未在本公司买车，暂不提供该服务！', 'url' => 'index.php?g=Wap&m=ServicestoreNew&a=order&token=' . $this->token . '&openid=' . $this->openid));
                }
            }
            $this->display('tpl/Wap/default/ServicestoreNew_order.html');
        }else{
            $this->display('tpl/Wap/default/ServicestoreNew_register.html');
        }

    }

    //订单记录页面
    public function orderInfo(){
        
       $list = M('Service_orders')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('otime desc')->select();

        foreach($list as $k=>$value){
            $staff = M('Staff')->where(array('id'=>$value['staffID']))->find();
            $list[$k]['sname'] = $staff['name'];
            $list[$k]['tel'] = $staff['telephone'];
        }
       $this->assign('list',$list);
       $info = M('Service_orders')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('otime desc')->find();
       $this->assign('info',$info);
        $this->display();
    }

    /*
     * 我要评价包括：
     * 1、评价页面；
     * 2、评价记录页面
     * */

    public function evaluation(){
        /*默认情况下的姓名,联系方式及车架号*/
        $userModel = M('wxuser');
        $userDatas = $userModel->where(array('token'=>$this->token))->find();
        $wxUserModel = M('wxusers');
        $wxUserDatas = $wxUserModel->where(array('uid'=>$this->userDatas['id'], 'openid'=>$this->openid))->find();
        $profile = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign('profile',$profile);
        $staffs = M('Staff')->where(array('id'=>$_GET['staffid'],'token'=>$this->token))->find();
        $this->assign('staff',$staffs);
        /*首先判断传过来的工号是否存在，存在后在进行后面的程序*/
        if(IS_POST){
        /*
         * 评价领取积分：
         * 1、一天内第一次对某职工评价获取积分
         *   （1）判断一天是否是第一次对公司的职工评价；
         * 2、若是呼救成功了，一天时间内评价获取积分
         * */
            $condition['staffID'] = $_POST['staffID'];
            $condition['ename'] = $_POST['ename'];
            $condition['ephone'] = $_POST['ephone'];
            $condition['eframe'] = $_POST['eframe'];
            $condition['evaluation'] = $_POST['evaluation'];
            $condition['evaluation_info'] = $_POST['reason'];
            $condition['token'] = $_POST['token'];
            $condition['openid'] = $_POST['openid'];
            $condition['etime'] = time();
            $userInfo = $this->userModel->where(array('token'=>$condition['token']))->find();
            $staff = M('Staff')->where(array('wxuser_id'=>$userInfo['id'],'staff_id'=>$condition['staffID']))->find();
            if($staff){
                $where['time'] = array(array('gt',strtotime(date("Y-m-d",time())." 00:00:00")),array('lt', strtotime(date
                    ("Y-m-d",time())." 23:59:59")));
                $where['token'] = $this->token;
                $where['openid'] = $this->openid;
                $appraoses = M('Service_appraise')->where($where)->find();
                $profiles = M('Service_profile')->where(array('token'=>$condition['token'],'openid'=>$condition['openid']))->find();
                $integral = $profiles['integral'];
                $integrals = M('Service_integral')->where(array('token'=>$this->token))->find();
                $integrales = $integrals['store'];  //维修后在一天内第一次评价后获取的积分
                $integraler = $integrals ['stores'];  //在一天内第一次填写评价内容获取的积分
                $orders = M('Service_orders')->where(array('token'=>$where['token'],'openid'=>$this->openid,'otime'=>$where['time']))->find();
                if(!$profile){
                    $insertDatas['car_username'] =isset($_POST['ename'])?$_POST['ename']:'' ;
                    $insertDatas['user_phone'] = isset($_POST['ephone'])?$_POST['ephone']:'';
                    $insertDatas['car_frame'] = isset($_POST['eframe'])?$_POST['eframe']:'';
                    $insertDatas['wxusers_id'] = isset($wxUserDatas['id'])?$wxUserDatas['id']:'';
                    $insertDatas['wxuser_id'] = isset($userDatas['id'])?$userDatas['id']:'';
                    $insertDatas['vip_name'] = isset($_POST['ename'])?$_POST['ename'].rand(1000, 10000):'';
                    $insertDatas['token'] = isset($_POST['token'])?$_POST['token']:'';
                    $insertDatas['openid'] = isset($_POST['openid'])?$_POST['openid']:'';
                    $insertDatas['register_time'] = time();
                    $insertDatas['is_bind'] = 0;
                    M('Service_profile')->data($insertDatas)->add();
                }
                if($orders){
                    if($appraoses){
                        $appraise = M('Service_appraise')->data($condition)->add();
                        if($appraise){
                            $this->ajaxReturn(array('status'=>100,'info'=>'评价成功','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluationInfo&token='.$this->token.'&openid='.$this->openid));
                        }else{
                            $this->ajaxReturn(array('status'=>1,'info'=>'评价失败','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
                        }
                    }else{
                        $appraise = M('Service_appraise')->data($condition)->add();
                        if($appraise){
                            $integralers['integral'] = $integral + $integraler; //原有的个人积分 + 在一天内第一次填写评价内容获取的积分
                            $sintegral = M('Service_profile')->where(array('token'=>$condition['token'],'openid'=>$condition['openid']))->save($integralers);//会员积分改变
                            if($sintegral){
                                $data = array(
                                    'token'=>$this->token,
                                    'openid'=>$this->openid,
                                    'time'=>time(),
                                    'info'=>$integraler,
                                    'road'=>1);
                                if(M('Service_integralinfo')->data($data)->add()){
                                    $this->ajaxReturn(array('status'=>100,'info'=>'评价成功,获取积分'.$integraler.'分','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluationInfo&token='.$this->token.'&openid='.$this->openid));
                                }else{
                                    $this->ajaxReturn(array('status'=>1,'info'=>'评价失败','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
                                }
                            }else{
                                $this->ajaxReturn(array('status'=>1,'info'=>'评价失败','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
                            }
                        }else{
                            $this->ajaxReturn(array('status'=>1,'info'=>'评价失败','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
                        }
                    }
                }else{
                    if($appraoses){
                        $appraise = M('Service_appraise')->data($condition)->add();
                        if($appraise){
                            $this->ajaxReturn(array('status'=>100,'info'=>'评价成功','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluationInfo&token='.$this->token.'&openid='.$this->openid));
                        }else{
                            $this->ajaxReturn(array('status'=>1,'info'=>'评价失败','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
                        }
                    }else{
                        $appraise = M('Service_appraise')->data($condition)->add();
                        if($appraise){
                            $integralers['integral'] = $integral + $integrales; //原有的个人积分 + 在一天内第一次填写评价内容获取的积分
                            $sintegral = M('Service_profile')->where(array('token'=>$condition['token'],'openid'=>$condition['openid']))->save($integralers);//会员积分改变
                            if($sintegral){
                                $data = array(
                                    'token'=>$this->token,
                                    'openid'=>$this->openid,
                                    'time'=>time(),
                                    'info'=>$integrales,
                                    'road'=>2);
                                if(M('Service_integralinfo')->data($data)->add()){
                                    $this->ajaxReturn(array('status'=>100,'info'=>'评价成功,获取积分'.$integrales.'分！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluationInfo&token='.$this->token.'&openid='.$this->openid));
                                }else{
                                    $this->ajaxReturn(array('status'=>1,'info'=>'评价失败','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
                                }
                            }else{
                                $this->ajaxReturn(array('status'=>1,'info'=>'评价失败','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
                            }
                        }else{
                            $this->ajaxReturn(array('status'=>1,'info'=>'评价失败','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
                        }
                    }
                }
            }else{
                $this->ajaxReturn(array('status'=>0,'info'=>'没有您要评价的工号','url'=>'index.php?g=Wap&m=ServicestoreNew&a=evaluation&token='.$this->token.'&openid='.$this->openid));
            }
        }
        $this->display();
    }
    //评论显示页
    public function evaluationInfo(){
        //Service_appraise
        $token = $this->token;
        $openid = $this->openid;
        $infon = M('Service_appraise')->where(array('token'=>$token,'openid'=>$openid))->order('etime desc')->select();
        $this->assign('info',$infon);
        $this->display();
    }

    /*
     * 签到页面（签到获取积分）
     * */

    public function integral(){
        //一，是否是会员，二，判断是否一天内有签到
        $userModel = M('wxuser');
        $userDatas = $userModel->where(array('token'=>$this->token))->find();
        $wxUserModel = M('wxusers');
        $wxUserDatas = $wxUserModel->where(array('uid'=>$this->userDatas['id'], 'openid'=>$this->openid))->find();
        $profile = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();

        if($profile){
            $token = $this->token;
            $openid = $this->openid;
            $info = M('Service_integral')->where(array('token'=>$token))->find();
            $pinfo= M('Service_profile')->where(array('token'=>$token,'openid'=>$openid))->find();
            $this->assign('data',$info['stors']);
            if(IS_POST){
                $integral = $info['stors'] + $pinfo['integral'];
                $where['time'] = array(array('gt',strtotime(date("Y-m-d",time())." 00:00:00")),array('lt', strtotime(date
                    ("Y-m-d",time())." 23:59:59")));
                $where['token'] = $token;
                $where['openid'] = $openid;
                $where['road'] = 3;
                $integralinfo = M('Service_integralinfo')->where($where)->find();
                if($integralinfo){
                    $this->ajaxReturn(array('status'=>0,'info'=>'您今天已经签过到了，记得明天再来哦！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=integral&token='.$this->token.'&openid='.$this->openid));
                }else{
                    $data = array(
                        'token'=>$token,
                        'openid'=>$openid,
                        'time'=>time(),
                        'info'=>$info['stors'],
                        'road'=>3
                    );
                    if(M('Service_integralinfo')->data($data)->add()){
                        if(M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->save(array('integral'=>$integral))){
                            $this->ajaxReturn(array('status'=>1,'info'=>'签到成功，记得明天再来哦！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=integral&token='.$this->token.'&openid='.$this->openid));
                        }else{
                            $this->ajaxReturn(array('status'=>2,'info'=>'签到失败！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=integral&token='.$this->token.'&openid='.$this->openid));
                        }
                    }else{
                        $this->ajaxReturn(array('status'=>2,'info'=>'签到失败！','url'=>'index.php?g=Wap&m=ServicestoreNew&a=integral&token='.$this->token.'&openid='.$this->openid));
                    }
                }
            }else{
                $this->display('tpl/Wap/default/ServicestoreNew_integral.html');
            }
        }else{
            $this->display('tpl/Wap/default/ServicestoreNew_register.html');
        }


    }

    /*
     * 积分兑换礼品：
     * 1、礼品展示兑换列表；
     * 2、兑换记录页面。在每条记录下记录是否领取。没领取的点击按钮进入下个页面
     * 3、领取页面。
     *
     * */
    //礼品展示兑换页

    public function exchanger(){
        $token	= $this->_get('token');
        $openid	= $this->_get('openid');
        $model = M('Integralshop');
        $list = $model->where(array('tp_integralshop.token'=>$token))->field('tp_integralshop.*,l.name')->join('left join tp_usercenter_level as l on tp_integralshop.extent = l.id ')->select();
        //print_r($list);exit;
        $this->assign('data',$list);
        $this->display();
    }

    public function exchange(){
        //判断是否是会员，

        $profile = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();

        if($profile){
            if(IS_POST){
                $croe = $_POST['point'];
                $score = $profile['integral'];
                if($profile['is_locks'] ==1){
                    $this->error('由于您在本公司还有未还的物品，请归还后再来兑换，谢谢配合！',U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }
                if($score < $croe){
                    $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }else{
                    $conn = M('Integralshop');
                    $lid=$_POST['exc_id'];
                    $result = $conn->where(array('token'=>$this->token,'id'=>$lid))->find();//在礼品积分表里查找礼品可兑换的次数
                    $gift = M('Integralshop_individual');
                    $term_1 = array('token'=>$this->token,'openid'=>$this->openid,'lid'=>$lid);
                    $term_2 = array('token'=>$this->token,'openid'=>$this->openid,'lid'=>$lid,'time'=>time(),'store'=>$croe);
                    $count = $gift->where($term_1)->count('lid');//计算兑换某礼品的总次数。
                    
                    if($result['degree']<=$count){
                        $this->error("对不起，您兑换此活动的机会已经用完！",U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                    }else{
                        $data['integral'] = $score - $croe;
                        $arr = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);
                        if($arr){
	    
                            if(M('Service_integralinfo')->data(array('token'=>$this->token,'openid'=>$this->openid,'time'=>time(),'info'=>-$croe,'road'=>5))->add()){
                                if($result['is_lock']==1){
                                    $term_2['is_use'] = 1;
                                    $isSuccess = M('Integralshop_individual')->data($term_2)->add();
                                    if($isSuccess){
                                        $this->success("扣除成功！",U(MODULE_NAME.'/exchangerList',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));
                                    }else{
                                        $this->error("兑换失败！",U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                                    }
                                }elseif($result['is_lock']==2){
                                    $term_2['is_use'] = 2;
                                    $term_2['use_time'] = date('Y-m-d,H:i:s');
                                    $isSuccess = M('Integralshop_individual')->data($term_2)->add();
                                    if($isSuccess){
                                        $cash = new cash($this->token, 22, $this->openid);
                                        $cash->cash_info();
                                        $this->success("扣除成功！",U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'),'type'=>250)));
                                    }else{
                                        $this->error("兑换失败！",U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                                    }
                                }else{
                                    $term_2['is_use'] = 1;
                                    $isSuccess = M('Integralshop_individual')->data($term_2)->add();
                                    if($isSuccess){
                                        $this->success("扣除成功！",U(MODULE_NAME.'/exchangerList',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));
                                    }else{
                                        $this->error("兑换失败！",U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                                    }
                                }
                            }else{
                                $this->error("兑换失败！",U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                            }
                        }else{
                            $this->error("兑换失败！",U(MODULE_NAME.'/exchanger',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                        }
                    }
                }
            }else{
              $this->display();
            }
        }else{
            $this->error("您还不是会员，请注册！",U(MODULE_NAME.'/register',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
        }
    }
    //兑换记录页面
    public function exchangerList(){
        $model =  M('Integralshop_individual');
        $list = $model->where(array('token'=>$this->token,'openid'=>$this->openid))->select();
        foreach($list as $key=>$value){
            $integral = M('Integralshop')->where(array('token'=>$this->token,'id'=>$value['lid']))->find();
            $list[$key]['name'] = $integral['title'];
        }
        $user = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $info = $model->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign('info',$info);
        $this->assign('user',$user);
       // print_r($list);exit;
        $this->assign('list',$list);
        $this->display();

    }



    //领取页面
    public function exchangerInfo(){
        $info = M('Integralshop')->where(array('token'=>$this->token,'id'=>$_GET['lid']))->find();
        $this->assign('data',$info);
        $prefile = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign('prefile',$prefile);
        $aWhere = array('token'=>$this->token,
            'openid'=>$this->openid,
            'id'=>$_GET['id']);
        $shop =  M('Integralshop_individual')->where($aWhere)->find();
        $this->assign('shop',$shop);
        if(IS_POST){
            $data['staffID'] = $_POST['staffID'];
            $data['is_use'] = $_POST['is_use'];
            $staffind =M('Staff')->where(array('token'=>$this->token,'staff_id'=>$data['staffID']))->find();
            if($staffind){
                if($info['is_lock'] == 0){
                    $restus = M('Integralshop_individual')->where($aWhere)->save($data);
                }elseif($info['is_lock'] == 1){
                    if($data['is_use'] ==3){
                        M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->save(array('is_locks'=>1));
                    }elseif($data['is_use'] ==2){
                        M('Service_profile')->where(array('token'=>$this->token,'openid'=>$this->openid))->save(array('is_locks'=>0));
                    }
                    $restus = M('Integralshop_individual')->where($aWhere)->save($data);
                }
                if($restus){
                    $this->error("领取成功！",U(MODULE_NAME.'/exchangerList',array('token'=>$_GET['token'],'openid'=>$this->_get('openid'))));
                }else{
                    $this->error("领取失败！",U(MODULE_NAME.'/exchangerInfo',array('token'=>$_GET['token'],'id'=>$_GET['id'],'openid'=>$this->openid,'lid'=>$$info['id'])));
                }
            }else{
                $this->error("该工号不存在！",U(MODULE_NAME.'/exchangerInfo',array('token'=>$_GET['token'],'id'=>$_GET['id'],'openid'=>$this->openid,'lid'=>$$info['id'])));
            }
        }else{
            $this->display();
        }

    }

    public function lingqu(){
        if(IS_AJAX){
            if($_POST['province'] == '昆明市'){
                if(M('Tailg_hongbao')->where(array('token'=>$this->token,'openid'=>$this->openid))->find()){
                    $this->error('您已领取！');
                }else{
                    $cash = new cash($this->token, 26, $this->openid);
                    $cash->cash_info();
                    if($cash->ret){
                        $data = array('token'=>$this->token,'openid'=>$this->openid,'add_time'=>date('Y-m-d H:i:s'));
                        if(M('Tailg_hongbao')->add($data)){
                            $this->success('恭喜获得一个红包！');
                        }else{
                            $this->error('获取失败！');
                        }
                    }else{
                        $this->error('系统原因，获取失败！');
                    }

                }
            }else{
                $this->error('您现在未在昆明市！');
            }
        }
        $this->display();
    }


}