<?php
/**
 * Created by PhpStorm.(每天商品信息更新)
 * User: Administrator
 * Date: 2015/9/26 0026
 * Time: 17:18
 */


class YndgetmoneyAction extends CliAction {
    public  $token;

    public function _initialize(){
        $this->token=$_GET['token'];
    }

    /*获取当天的商品LQ和CQ*/
    public function getvalues(){
        $ynd = new Ynd();
        $list = $ynd->getProductInf('','','');
        $info = array(

        );
        return $info;
    }

    public function updataproduct(){
        $info = $this->getvalues();
        $list = M('Ynd_product')->where(array())->select();
        $data['parameter'] = $info['parameter'];
        $data['LQ'] = $info['info'];
        $data['CQ'] = $info['CQ'];


    }




}