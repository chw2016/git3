<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-6
 * Time: 下午2:17
 */
class DuokfAction extends UserAction{

    public function index(){
        $kefulist = M('Duokf_list')->where(array('token'=>$this->token))->select();
        $this->assign('kefulist',$kefulist);
        $this->display();
    }

    public function sysKf(){
       $data = $this->getWxAccesstoken();
       print_r($data);exit;
       if($data['code'] == 0){
           $this->success($data['msg'],array(''));
       }else{
           $this->error($data['msg'],array('url'=>U('User/Duokf/index',array('token'=>$this->token))));
       }
    }

    public function addKf(){

    }



}