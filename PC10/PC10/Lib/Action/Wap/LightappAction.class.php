<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-9-11
 * Time: 下午10:38
 * To change this template use File | Settings | File Templates.
 */
class LightappAction extends BaseAction{

    public function _initialize(){
        parent::_initialize();
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $this->wxuser = $wxuser;
        $this->assign('wxuser',$wxuser);
    }

    public function index(){
        $lightapp  = M('Lightapp')->where(array('token'=>$this->token,'id'=>$_GET['id']))->find();
        $this->assign('lightapp',$lightapp);
        $this->display();
    }




}