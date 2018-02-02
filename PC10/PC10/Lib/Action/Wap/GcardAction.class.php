<?php
class GcardAction extends BaseAction{
	private $wecha_id;
	public function _initialize(){
		parent::_initialize();		
		if (isset($_GET['wecha_id'])&&$_GET['wecha_id']){
			$_SESSION['wecha_id']=$_GET['wecha_id'];
			$this->wecha_id=$this->_get('wecha_id');
		}
		if (isset($_SESSION['wecha_id'])){
			$this->wecha_id=$_SESSION['wecha_id'];
		}
	}
	public function index(){
		$gid	  =  $this->_get('gid');
		$pid	  =  $this->_get('pid');
		$token 	  = $this->_get('token');
		$wecha_id	 =  $this->_get('wecha_id');
		$where=array('gid'=>$gid,'token'=>$token);			
		$check=M('Gcard_diy')->where($where)->find();
		if($check==false)$this->error('非法操作');	
		$copy = M('Home')->where(array('token'=>$token))->find();	
		$info  =  M('Gcard_diy')->where(array('id'=>$pid,'gid'=>$gid,'wecha_id'=>$wecha_id,'token'=>$token))->find();
		$pid = $info['id'];
		$key = M('Keyword')->where(array('pid'=>$pid,'token'=>$token,'wecha_id'=>$wecha_id,'module'=>'GcardDiy'))->find(); 
		$picurl  =  M('Gcard')->where(array('taxis'=>$gid,'token'=>$token))->find();
		$share = $this->_get('share');
        if($share=='true'){
		$lock = '1';
		}
		$this->assign('lock',$lock);
		$wecha=M('Wxuser')->field('wxname,wxid')->where(array('token'=>$token))->find();
		$this->assign('wecha',$wecha);
		$lott  =  M('Lottery')->where(array('token'=>$token))->find();
		$hdid = $lott['id'];
		if($check==false){
		$url = '/index.php?g=Wap&m=Index&token='. $token .'&show=mp.weixin.qq.com';
		}else{
		switch($lott['keyword']){
				case '大转盘':
                $url = '/index.php?g=Wap&m=Lottery&a=index&token=' . $token . '&wecha_id=' . $this->wecha_id . '&id=' .$hdid ;
				break;
				case '优惠券':
                $url = '/index.php?g=Wap&m=Coupon&a=index&token=' . $token . '&wecha_id=' . $this->wecha_id . '&id=' .$hdid ;
				break;
				case '刮刮卡':
                $url = '/index.php?g=Wap&m=Guajiang&a=index&token=' . $token . '&wecha_id=' . $this->wecha_id . '&id=' .$hdid ;
				break;
		   }
		}
		$this->assign('url',$url);
		$this->assign('info',$info);
		$this->assign('copy',$copy);
		$this->assign('gid',$gid);
		$this->assign('pid',$pid);
		$this->assign('token',$token);
		$this->assign('wecha',$wecha);
		$this->assign('wecha_id',$wecha_id);
		$this->assign('key',$key);
		$this->assign('picurl',$picurl);
		$this->display();
	}
	public function lists(){
	    $token 	  = $this->_get('token');
	    $wecha_id	= $this->_get('wecha_id');		
		$db=D('Gcard');
		$where['token'] = $this->_get('token');
		$copy = M('Home')->where($where)->find();
		$info = $db->where($where)->order('taxis ASC')->select();	
		$this->assign('copy',$copy);
		$this->assign('list',$info);
		$this->assign('token',$token);
		$this->assign('wecha_id',$wecha_id);
		$this->display();
	}	
	public function view(){
		$id	  =  $this->_get('gid');
		$token 	  = $this->_get('token');
		$wecha_id	= $this->_get('wecha_id');
		$where=array('taxis'=>$id,'token'=>$token);			
		$check=M('Gcard')->where($where)->find();
		if($check==false)$this->error('非法操作');	
        $copy = M('Home')->where(array('token'=>$token))->find();		
		$arr  =  M('Gcard')->where(array('taxis'=>$id,'token'=>$token))->find();
		$pid = $arr['id'];
		$wecha_id	 =  $this->_get('wecha_id');
		$key = M('Keyword')->where(array('pid'=>$pid,'token'=>$token,'module'=>'Gcard'))->find(); 
		$info = M('Gcard')->where(array('id'=>$pid,'token'=>$token))->find(); 		
		$lott  =  M('Lottery')->where(array('token'=>$token))->find();
		$hdid = $lott['id'];
		if($check==false){
		$url = '/index.php?g=Wap&m=Index&token='. $this->token .'&show=mp.weixin.qq.com';
		}else{
		switch($lott['keyword']){
				case '大转盘':
                $url = '/index.php?g=Wap&m=Lottery&a=index&token=' . $token . '&wecha_id=' . $this->wecha_id . '&id=' .$hdid ;
				break;
				case '优惠券':
                $url = '/index.php?g=Wap&m=Coupon&a=index&token=' . $token . '&wecha_id=' . $this->wecha_id . '&id=' .$hdid ;
				break;
				case '刮刮卡':
                $url = '/index.php?g=Wap&m=Guajiang&a=index&token=' . $token . '&wecha_id=' . $this->wecha_id . '&id=' .$hdid ;
				break;
		   }
		}
		$this->assign('url',$url);		
		$this->assign('info',$info);
		$this->assign('copy',$copy);
		$this->assign('id',$id);
		$this->assign('token',$token);
		$this->assign('wecha_id',$wecha_id);
		$this->assign('key',$key);
		$this->display();
	}	
	public function edit(){
	    $id	  =  $this->_get('gid');
	    $token	  =  $this->_get('token');
		$wecha_id	 =  $this->_get('wecha_id');
		if(IS_POST){			
			$data=D('Gcard_diy');		
			if($data->create()!=false){				
				if($id=$data->add()){
					$data1['pid']=$id;
					$data1['module']='GcardDiy';
					$data1['token']=$token;
					$data1['wecha_id']=$wecha_id;
					$data1['keyword']='贺卡DIY'.$id;
					M('Keyword')->add($data1);
					$this->success('添加成功');
					$this->redirect(U('Gcard/index',array('token'=>$_GET['token'],'gid'=>$_GET['id'],'pid'=>$id,'wecha_id'=>$_GET['wecha_id'])));
				}else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
		$info = M('Gcard')->where(array('taxis'=>$id,'token'=>$token))->find(); 		
		$this->assign('info',$info);
		    $this->assign('token',$token);
		    $this->assign('id',$id);
			$this->assign('wecha_id',$wecha_id);
			$this->display();
		}	

	}
	
}
?>