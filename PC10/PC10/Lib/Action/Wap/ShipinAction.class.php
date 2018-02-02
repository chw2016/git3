<?php
class ShipinAction extends BaseAction{

	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}		
		$info=M('Shipin')->where(array('token'=>$token))->find();
		//$zjdp=M('Shipincom')->where(array('token'=>$token))->find();
		//$lp_zjdp=M('Shipincom')->where(array('token'=>$token,'status'=>1,'subestatename'=>$this->_get('title')))->order('sort desc')->select();
		$lp=M('Shipin')->where(array('token'=>$token,'status'=>1,'id'=>$this->_get('id')))->order('id desc')->select();
		//$this->assign('zjdp',$zjdp);
		//$this->assign('lp_zjdp',$lp_zjdp);
		$this->assign('lp',$lp);
		$this->assign('info',$info);
		$this->display();
	}
	

		public function shouye(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}
		
		$info=M('Shipin')->where(array('token'=>$token))->find();
		$lp=M('Shipin')->where(array('token'=>$token,'status'=>1))->order('id desc')->select();
		$this->assign('info',$info);
		$this->assign('lp',$lp);
		$this->display();	
	}
		public function Shipincom_index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}
		$info1=M('Shipin')->where(array('token'=>$token))->find();
		$info=M('Shipincom')->where(array('token'=>$token))->find();
		$lp=M('Shipincom')->where(array('token'=>$token,'status'=>1))->order('id desc')->select();
		$this->assign('info1',$info1);
		$this->assign('info',$info);
		$this->assign('lp',$lp);
		$this->display();	
	}
	
	public function subestate(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}		
		$info=M('Shipinunits')->where(array('token'=>$token))->find();
		$lp=M('Shipinunits')->where(array('token'=>$token,'status'=>1,'sub'=>$this->_get('name')))->order('sort desc')->select();
		$toppic=M('Shipin')->where(array('token'=>$token))->find();
		$this->assign('toppic',$toppic);
		$this->assign('info',$info);
		$this->assign('lp',$lp);
		$this->display();	
	}
	
	public function subestate_index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}	
		$info=M('Shipinsub')->where(array('token'=>$token))->find();
		$lp1=M('Shipin')->where(array('token'=>$token,'status'=>1,'title'=>$this->_get('title')))->order('id desc')->select();
		$lp=M('Shipinsub')->where(array('token'=>$token,'status'=>1,'title'=>$this->_get('title')))->order('sort desc')->select();
		$toppic=M('Shipin')->where(array('token'=>$token))->find();
		$this->assign('toppic',$toppic);
		$this->assign('info',$info);
		$this->assign('lp',$lp);
		$this->assign('lp1',$lp1);
		$this->display();	
	}
	
		public function jianjie(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}	
		$info=M('Shipinsub')->where(array('token'=>$token,'status'=>1,'name'=>$this->_get('name')))->find();
		$this->assign('info',$info);
		$this->display();	
	}
		public function houseunits(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}		
		$info=M('Shipinunits')->where(array('token'=>$token,'id'=>$this->_get('id')))->find();
		$lp_elist=M('Shipinunits')->where(array('token'=>$token,'id'=>$this->_get('id'),'status'=>1))->select();
		$this->assign('info',$info);
		$this->assign('elist',$lp_elist);
		$this->display();
	}


}
?>
