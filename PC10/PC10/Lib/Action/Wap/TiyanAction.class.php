<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class TiyanAction extends BaseAction{
	

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		include"./Lib/Action/Wap/mrufx.php";
		$token=$this->token;
	    $list=M('mru_tiyan')->where(array('token'=>$token))->field('content',true)->select();
	    
	    $count      = M('mru_tiyan')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,15);
	    $show       = $Page->show();
	    $list = M('mru_tiyan')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->order("sort")->select();
	    $this->assign('page',$show);
	  	$this->assign('list',$list);
	    $firstRow=ceil($count/15);//总数除以每页显示多少条，有小数加1 得到总页数
	    
	    $this->assign('firstRow',$firstRow);//把总页数分配过去
	    //查询是否是会员
	    $uid=M('wxuser')->where(array('token'=>$_GET['token']))->getField('id');
	    $memberlist=M('Usercenter_memberlist')->where(array('openid'=>$_GET['openid'],'uid'=>$uid))->find();
	   
	    $this->assign('memberlist',$memberlist);
		$this->display();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
		include"./Lib/Action/Wap/mrufx.php";
        $list=M('mru_tiyan')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
        
        //查询是否是会员
        $uid=M('wxuser')->where(array('token'=>$_GET['token']))->getField('id');
        $memberlist=M('Usercenter_memberlist')->where(array('openid'=>$_GET['openid'],'uid'=>$uid))->find();
        //P($memberlist);
        $this->assign('memberlist',$memberlist);
        
		$this->display();
	}
	
	public function ty(){
		
		if(IS_POST){
			 $token=$this->_post('token');
			// print_r($token);die;
			MruMember("Mhyzx/zc",$_GET['openid']);
			$_POST['add_time']=time();
			$id=$this->_post('id');
			$_POST['pid']=$this->_post('id');
			unset($_POST['id']);
			$openid=M('mru_tiyan2')->where(array('openid'=>$_POST['openid'],'pid'=>$id,'token'=>$token))->find();
			if($openid){
				script("您已经预约过了","Tiyan/index",get(token,openid));
			}else{
				$b=M('mru_tiyan2')->add($_POST);
				//人数加1
				M('mru_tiyan')->where(array('id'=>$id))->setInc('num',1);
			}
			
			if($b){
				script("预约免费体验信息发布成功","Tiyan/index",get(token,openid));
			}else{
				script("信息发布失败","Tiyan/index",get(token,openid));
			}
			die;
		}else{
			
			
			
			include"./Lib/Action/Wap/mru.php";
			
			MruMember("Mhyzx/zc",$_GET['openid']);
			include"./Lib/Action/Wap/mrufx.php";
			//MemberYz('Mhyzx/zc',$_GET['openid']);
			$token=$this->_get('token');
			$openid=$this->_get('openid');
			$id=$this->_get('id');
			$list=M('mru_tiyan')->where(array('id'=>$id))->find();
			$this->assign('list',$list);
			//p($list);
			//echo 11;die;
			$this->display();
			
		}

	}


	
}
?>
