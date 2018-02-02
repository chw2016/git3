<?php
/**
 * Created by IntelliJ IDEA.
 * User: 訾超
 * Date: 14-11-10
 * Time: 上午10:35
 * To change this template use File | Settings | File Templates.
 */
class DistributionAction extends BaseAction{


    public function _initialize(){
        parent::_initialize();

    }
    /*//登录
    public function login(){
        $this->display();
    }*/
    //注册
    public function register(){
        if(M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->find()){
            $this->redirect('Distribution/home',array('token'=>$this->token,'openid'=>$_GET['openid']));
        }
        $parent = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_GET['dopenid']))->find();
        if($parent){
            $this->assign('parentInfo',$parent);
            $this->assign('parent',1);
        }else{
            $this->assign('parent',0);
        }
        $this->assign('get',$_GET);
        if(IS_POST){
            $_POST['reg_time'] = date('Y-m-d H:i:s');
            $_POST['last_login_time'] = date('Y-m-d H:i:s');
            $_POST['token'] = $this->token;
            $_POST['dopenid'] = $_POST['dopenid'];
            if(M('Homenice_user')->add($_POST)){
                $snInfo = M('Sn')->where(array('token'=>$this->token,'stauts'=>0,'openid'=>''))->limit('1')->find();
                M('Sn')->where(array('token'=>$this->token,'sn'=>$snInfo['sn']))->setField(array('openid'=>trim($_POST['openid']),'get_time'=>time()));
                $this->ajaxReturn(array('info'=>'注册成功,正在跳转','status'=>1,'url'=>'index.php?g=Wap&m=Distribution&a=home&token='.$this->token.'&openid='.$_POST['openid']));
            }else{
                $this->ajaxReturn(array('info'=>'注册失败,请稍后再试','status'=>0));
            }
	    //print_r($_POST);EXIT;
        }
        $this->display();
    }
    //首页
    public function home(){
        //微信头像获取
        $wxusersInfo = M('Wxusers')->where(array('uid'=>$this->tpl['uid'],'openid'=>$this->openid))->find();
        $this->assign('wxuserInfo',$wxusersInfo);

        $appidInfo = M('Diymen_set')->where(array('token'=>$this->token))->find();
        $diymenidInfo = M('Diymen_class')->where(array('token'=>$this->token,'title'=>'WP_YONGJIN','is_show'=>0))->find();
        $this->assign('appidInfo',$appidInfo);
        $this->assign('diymenidInfo',$diymenidInfo);

        //邀请人数
        $myClients = M('Homenice_user')->where(array('token'=>$this->token,'dopenid'=>$_GET['openid']))->count();
        $this->assign('myClients',$myClients);
        //佣金
        $mycommission = M('Homenice_commission')->where(array('token'=>$this->token,'ws_openid'=>$_GET['openid']))->select();
        foreach($mycommission as $k => $v){
            $allMycommission = $v['single_price']*$v['attribution']*$v['commission_proportion'] + $allMycommission;
        }
        $this->assign('allMycommission',$allMycommission);
        $userInfo = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->find();

        //历史记录
        $history = M('Product_new_visiter_data')->where(array('token'=>$this->token,'dopenid'=>$_GET['openid']))->count();
        $this->assign('history',$history);
        //红包
        $myRedpacketNum = M('Sn')->where(array('token'=>$this->token,'openid'=>$_GET['openid'],'status'=>0))->count();


        /*
       * 引入微信js接口
       */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);



        $this->assign('num',$myRedpacketNum);
        if($userInfo){
            $this->assign('userInfo',$userInfo);
            $this->display();
        }else{
            $this->redirect('Wap/Distribution/error');
        }
    }
    //佣金
    public function commission(){
        $userInfo = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->find();
        if($userInfo){
            $this->assign('userInfo',$userInfo);
            $mycommission = M('Homenice_commission')->where(array('token'=>$this->token,'ws_openid'=>$_GET['openid'],'is_commission'=>1))->select();
            foreach($mycommission as $k => $v){
                $allMycommission +=$mycommission[$k]['single_price'] * $mycommission[$k]['attribution'] * $mycommission[$k]['commission_proportion'];
                if($v['ispay'] == 1){
                    $havpay += $mycommission[$k]['single_price'] * $mycommission[$k]['attribution'] * $mycommission[$k]['commission_proportion'];
                }
                if($v['ispay'] == 2){
                    $haventpay += $mycommission[$k]['single_price'] * $mycommission[$k]['attribution'] * $mycommission[$k]['commission_proportion'];
                }
                $this->assign('havpay',$havpay);
                $this->assign('haventpay',$haventpay);
                if(IS_POST){
                    $this->assign('post',$_POST);
                    if($_POST['ispay'] !== "3" ){
                        if($_POST['ispay'] !== $mycommission[$k]['ispay']){
                            unset($mycommission[$k]);
                        }
                    }
                }
            }
            $this->assign('allMycommission',$allMycommission);
            $this->assign('mycommission',$mycommission);
        }else{
            $this->redirect('Wap/Distribution/error');
        }
        $this->display();
    }
    //银行
    public function bankcard(){
        $bankInfo = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->find();
        if($bankInfo){
            $this->assign('bankInfo',$bankInfo);
        }else{
            $this->redirect('Wap/Distribution/error');
        }
        if(IS_POST){
            if(M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_REQUEST['openid']))->save($_POST)){
                exit(json_encode(array('info'=>'保存成功！','status'=>1,'url'=>'index.php?g=Wap&m=Distribution&a=commission&token='.$this->token.'&openid='.$_REQUEST['openid'])));
            }else{
                exit(json_encode(array('info'=>'保存失败！','status'=>0)));
            }
        }
        $this->display();
    }
    public function myclient(){
        $user = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->field('openid')->find();
        if($user['openid'] == $_GET['openid']){
            $myClient = M('Homenice_user')->where(array('token'=>$this->token,'dopenid'=>$_GET['openid']))->select();
            $this->assign('myClient',$myClient);
            $this->display();
        }else{
            $this->redirect('Wap/Distribution/error');
        }

    }
 /*   //红包
    public function redpacket(){
        $userInfo = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->find();
        if($userInfo){
            $myRedpacketList = M('Sn')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->select();
            if($myRedpacketList){
                $this->assign('RPList',$myRedpacketList);
            }
            $this->display();
        }else{
            $this->redirect('Wap/Distribution/error');
        }

    }*/
    //历史记录
    public function clienthistory(){
        $userInfo = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->find();
        if($userInfo){
            $sql = "select n.name,v.nickname,v.time,v.address,v.url from tp_product_new_visiter_data AS v LEFT JOIN tp_product_new AS n ON n.id=v.product_id WHERE v.token='".$this->token."' and v.dopenid='".$_GET['openid']."'";
            $res = M('Product_new_visiter_data')->query($sql);
            foreach($res as $k => $v){
                if(strpos($v['url'],'a=cats') == true){
                    $res[$k]['name'] = '商城主页';
                }elseif(strpos($v['url'],'a=products') == true){
                    $res[$k]['name'] = '商品分类页';
                }
                if($v['time'] > strtotime("3 day") || $v['time'] < strtotime("-3 day")){
                    unset($res[$k]);
                }
            }
            $this->assign('res',$res);
            $this->display();
        }else{
            $this->redirect('Wap/Distribution/error');
        }
    }






}