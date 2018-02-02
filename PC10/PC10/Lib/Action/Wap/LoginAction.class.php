<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * tel:15889394741
 * notice:O2O登陆注册
 * Date: 2015/1/8
 * Time: 17:41
 */
class LoginAction extends BaseAction{
    public $token;
    public function _initialize(){
        parent::_initialize();
        $this->token=$this->_get("token");
        $this->assign("tpl",$this->tpl);
        $this->assign("token",$this->token);
    }

    //非微信用户登录
    public function Login(){
        if(IS_POST){
            $_POST['password']=md5(trim($_POST['password']));
            if(M("Cusers")->where(array("openid"=>$_POST['openid'],'password'=>$_POST['password'],"token"=>$this->token))->find()){
                session("O2O",md5($_POST['openid']));
                $this->ajaxReturn(array("status"=>1,"info"=>"登陆成功","openid"=>md5($_POST['openid'])));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"登陆失败"));
            }
        }else{
            $this->display();
        }
    }

    //非微信用户注册
    public function Register(){
        if(IS_POST){
            $_POST['rtime']=date("Y-m-d H:i:s");
            $_POST['password']=md5(trim($_POST['password']));
            $_POST['token']=$this->_get("token");
            $_POST['mduser']=md5($_POST['openid']);
            if(M("Cusers")->where(array("openid"=>$_POST['openid'],'token'=>$this->token))->find()){
                $this->ajaxReturn(array("status"=>0,"info"=>"该账号已经注册"));
            }
            if(M("Cusers")->add($_POST)){
                session("O2O",md5($_POST['openid']));
                $this->ajaxReturn(array("status"=>1,"info"=>"注册成功","openid"=>md5($_POST['openid'])));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"注册失败"));
            }
        }else{
            $this->display();
        }
    }

}