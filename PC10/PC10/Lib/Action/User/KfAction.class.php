<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-5-3
 * Time: 下午6:17
 * To change this template use File | Settings | File Templates.
 */
class KfAction extends UserAction{

    public function addview(){
        $token = session('token');
        $wxuser = M('Wxuser')->where(array('token'=>$token))->find();
        if($wxuser['has_kefu'] == 1){
            $this->assign('has_kefu',1);
            $kefu = M('Wxusers')->where(array('uid'=>$wxuser['id'],'is_kefu'=>1))->find();
            $this->assign('is_kefu',$kefu);
        }else{
            $this->assign('has_kefu',0);
        }
        $this->assign('token',$wxuser['id'].substr($token,0,5));
        $this->display();
    }



}