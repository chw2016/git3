<?php
class TaxloginAction extends BaseAction{
    public function login(){
        echo $this->openid;
       $openid = '29939848';
        Vendor('weixin.Tax_Remote_WP');
        $Tax_remote_WP= new Tax_remote_WP();
        if($_POST){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $checkcode = $_POST['checkcode'];
            $isUkey = 20;
            $cookieDir = APP_PATH.'Tax/cookie/'.$openid.'.tmp';
            $time = APP_PATH.'Tax/time/'.$openid.'.txt';
            $login = $Tax_remote_WP->login($username,$password,$checkcode,$isUkey,$cookieDir,$time,$openid);
            if($login){
                if($Declaredata = $Tax_remote_WP->noDeclare($cookieDir)){
                    echo $Declaredata;
                }else{
                    echo 234;exit;
                    $Tax_remote_WP->init($openid);
                    $this->redirect('http://test.wap.com/index.php?g=Wap&m=Taxlogin&a=login');
                }
            }else{
                echo 456;exit;
                $Tax_remote_WP->init($openid);
                $this->redirect('http://test.wap.com/index.php?g=Wap&m=Taxlogin&a=login');
            }
        }else{
            $Tax_remote_WP->init($openid);
            $this->assign('openid',$openid);
            $this->display();
        }
    }
    public function userCheck(){
        $openid =$_GET['openid'];
        if($_GET['username']){
            $username = $_GET['username'];
            Vendor('weixin.Tax_Remote_WP');
            $Tax_remote_WP= new Tax_remote_WP();
            $cookieDir = APP_PATH.'Tax/cookie/'.$openid.'.tmp';
            $Tax_remote_WP->userCheck($openid,$username,$cookieDir);
        }else{
            return false;
        }
    }
    public function yjsb(){
        $openid = '29939848';
        Vendor('weixin.Tax_Remote_WP');
        $Tax_remote_WP= new Tax_remote_WP();
        if($_GET){
            $sqqx_q = $_GET['sqqx_q'];
            $sqqx_z = $_GET['sqqx_z'];
            $sqqx_z = $_GET['sqqx_z'];
            $pzzl_dm = $_GET['pzzl_dm'];
            $cookieDir = APP_PATH.'Tax/cookie/'.$openid.'.tmp';
            $Tax_remote_WP->yjsb($sqqx_q,$sqqx_z,$sqqx_z,$openid,$cookieDir);
        }else{
            echo 'Ò»¼üÉê±¨Ê§°Ü';
        }
    }
}