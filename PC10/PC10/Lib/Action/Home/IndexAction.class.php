<?php
class IndexAction extends Action{
	//关注回复

    public function  _initialize(){
        define('RES', C('site_url').THEME_PATH . 'common');
        define('STATICS', C('site_url').TMPL_PATH . 'static');
    }

    public function index(){
    	//张湘南改
    	//第4步:把存在cookie里面帐号密码输出到模板
        if($_COOKIE['serializedata']){
    		$unserializedata=unserialize($_COOKIE['serializedata']);//反序列化
    		$this->assign('username',$unserializedata['username']);
    		$this->assign('password',$unserializedata['password']);
    	}
		$this->display();
	}
	//张湘南改
	public function login(){
		//第4步:把存在cookie里面帐号密码输出到模板
		if($_COOKIE['serializedata']){
			$unserializedata=unserialize($_COOKIE['serializedata']);//反序列化
			$this->assign('username',$unserializedata['username']);
			$this->assign('password',$unserializedata['password']);
		}
		$this->display();
	}
	public function resetpwd(){
		$uid=$this->_get('uid','intval');
		$code=$this->_get('code','trim');
		$rtime=$this->_get('resettime','intval');
		$info=M('Users')->find($uid);
		if( (md5($info['uid'].$info['password'].$info['email'])!==$code) || ($rtime<time()) ){
			$this->error('非法操作',U('Index/index'));
		}
		$this->assign('uid',$uid);
		$this->display();
	}
}
