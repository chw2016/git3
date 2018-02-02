<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/5
 * Time: 17:26
 */
class Alipay_config_newAction extends UserAction{
    public $alipay_config_db;
    public function _initialize() {
        parent::_initialize();
        $this->alipay_config_db=M('Alipay_config_new');
        if (!$this->token){
            exit();
        }
    }
    public function index(){
        $config = $this->alipay_config_db->where(array('token'=>$this->token))->find();

        if(IS_POST){
            $row['pid']=$this->_post('pid');
            $row['paytype']=$this->_post('paytype');
            $row['key']=$this->_post('key');
            $row['name']=$this->_post('name');
            $row['token']=$this->_post('token');
            $row['open']=$this->_post('open');

            $row['appid']=$this->_post('appid');
            $row['paysignkey']=$this->_post('paysignkey');
            $row['appsecret']=$this->_post('appsecret');
            $row['partnerid']=$this->_post('partnerid');
            $row['partnerkey']=$this->_post('partnerkey');
            //print_r($row);exit;
            if ($config){
                $where=array('token'=>$this->token);
                $this->alipay_config_db->where($where)->save($row);
            }else {
                $this->alipay_config_db->add($row);
            }
            $this->success('设置成功',U('Alipay_config_new/index',$where));
        }else{
            $this->assign('config',$config);
            $this->display();
        }
    }
}

