<?php
class EstateAction extends BaseAction{
	/* 首页 index页面
	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}
		$estate=M('Estate')->where(array('token'=>$token,'status'=>1))->order('id desc')->select();
		$info=M('classify')->where(array('token'=>$token))->find();
		$gfl=M('classify')->where(array('token'=>$token,'status'=>1))->order('id')->select();
		//dump($estate);
		$this->assign('estate',$estate);
		$this->assign('info',$info);
		$this->assign('gfl',$gfl);
		$this->display();
		
	}
	*/
	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}		
		$info=M('Estate')->where(array('token'=>$token))->find();
		$zjdp=M('Estatecom')->where(array('token'=>$token))->find();
		$lp_zjdp=M('Estatecom')->where(array('token'=>$token,'status'=>1))->order('id desc')->select();
		$lp=M('Estate')->where(array('token'=>$token,'status'=>1,'id'=>$this->_get('id')))->order('id desc')->select();
		//dump($estate);
		$this->assign('zjdp',$zjdp);
		$this->assign('lp_zjdp',$lp_zjdp);
		$this->assign('lp',$lp);
		$this->assign('info',$info);
		$this->display();
	}
	
	
		public function Estate_index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}
		
		$info=M('Estate')->where(array('token'=>$token))->find();
		$lp=M('Estate')->where(array('token'=>$token,'status'=>1))->order('id desc')->select();
		//dump($estate);
		$this->assign('toppic',$toppic);
		$this->assign('info',$info);
		$this->assign('lp',$lp);
		$this->display();	
	}
	
	
	public function Subestate(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}		
		$info=M('Houseunits')->where(array('token'=>$token))->find();
		$lp=M('Houseunits')->where(array('token'=>$token,'status'=>1,'sub'=>$this->_get('name')))->order('id desc')->select();
		$toppic=M('Estate')->where(array('token'=>$token))->find();
		//dump($estate);
		$this->assign('toppic',$toppic);
		$this->assign('info',$info);
		$this->assign('lp',$lp);
		$this->display();	
	}
	
	public function Subestate_index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}	
		$info=M('Subestate')->where(array('token'=>$token))->find();
		
		$lp=M('Subestate')->where(array('token'=>$token,'status'=>1,'title'=>$this->_get('title')))->order('id desc')->select();
		$toppic=M('Estate')->where(array('token'=>$token))->find();
		//dump($estate);
		$this->assign('toppic',$toppic);
		$this->assign('info',$info);
		$this->assign('lp',$lp);
		$this->display();	
	}
	
	
		public function Houseunits(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}		
		$info=M('Houseunits')->where(array('token'=>$token,'id'=>$this->_get('id')))->find();
		$lp_elist=M('Houseunits')->where(array('token'=>$token,'id'=>$this->_get('id'),'status'=>1))->select();
		
		//dump($estate);
		$this->assign('info',$info);
		$this->assign('elist',$lp_elist);
		$this->display();
		
	
	}
		public function Estatecom(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}		
		$info=M('Estatecom')->where(array('token'=>$token))->find();
		$lp_zjdp=M('Estatecom')->where(array('token'=>$token,'status'=>1))->order('id desc')->select();
		$infofl=M('classify')->where(array('token'=>$token))->find();
		$gfl=M('classify')->where(array('token'=>$token,'status'=>1))->order('id')->select();
		//dump($estate);
		$this->assign('infofl',$infofl);
		$this->assign('gfl',$gfl);
		$this->assign('info',$info);
		$this->assign('zjdp',$lp_zjdp);
		$this->display();
		
	
	}
		public function Estatephoto(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}		
	
		$info=M('Estatephoto')->where(array('token'=>$token))->find();
		$xc=M('Estatephoto')->where(array('token'=>$token,'status'=>1))->order('id desc')->select();
		$toppic=M('Estate')->where(array('token'=>$token))->find();
		//dump($estate);
			$this->assign('toppic',$toppic);
		$this->assign('info',$info);
		$this->assign('xc',$xc);
		$this->display();
		
	
	}
	
		public function Estatephoto_list(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token==false){
			echo '数据不存在';exit;
		}			
		$info=M('Estatephoto')->where(array('token'=>$token))->find();
		$xc=M('Estatephoto')->where(array('token'=>$token,'status'=>1,'name'=>$this->_get('name')))->order('id desc')->select();
		$toppic=M('Estate')->where(array('token'=>$token))->find();
		//dump($estate);
		$this->assign('toppic',$toppic);
		$this->assign('info',$info);
		$this->assign('xc',$xc);
		$this->display();
		
	
	}
	
}
?>
