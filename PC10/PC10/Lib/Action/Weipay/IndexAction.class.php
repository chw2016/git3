<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-4-28
 * Time: 上午11:29
 * To change this template use File | Settings | File Templates.
 */
class IndexAction extends UserAction{

    public $weipay;

    public function _initialize() {
        //parent::_initialize();
        $this->weipay=M('Weipay_config');
    }

    public function index(){
        $config = $this->weipay->where(array('token'=>$this->token))->find();
        if(IS_POST){
            $row['pid']=$this->_post('pid');
            $row['key']=$this->_post('key');
            $row['tenpaypartner']=$this->_post('tenpaypartner');
            $row['tenpaykey']=$this->_post('tenpaykey');
            $row['name']=$this->_post('name');
            $row['token']=$this->_post('token');
            $row['open']=$this->_post('open');
            if ($config){
                $where=array('token'=>$this->token);
                $this->weipay->where($where)->save($row);
            }else {
                $this->weipay->add($row);
            }
            $this->success('设置成功',U('Weipay/index',$where));
        }else{
            echo 123;exit;
            $this->assign('config',$config);
            $this->display('Weipay:Weipay:Weipay_index');
        }
    }



}