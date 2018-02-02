<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MrugrAction extends BaseAction{
	
	public $_sTplBaseDir = 'Wap/default/mru';
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		if(!$_GET['openid'] || !$_GET['token']){
			die('系统繁忙，请稍后再试');
		}
		
		
	}

	// 首页显示，店铺发送
	public function index(){
        include"./Lib/Action/Wap/mru.php";
		MruMember("Mhyzx/zc",$_GET['openid']);
		//每次经过这里都把微信图片姓名更新一次
		$uid=M('wxuser')->where(array('token'=>$_GET['token']))->getField('id');
		$wxusers=M('wxusers')->where(array('openid'=>$_GET['openid'],'uid'=>$uid))->find();
		M('mru_jfb')->where(get(openid,token))->save(array('name'=>$wxusers['nickname'],'pic'=>$wxusers['headimgurl']));
	    $list=M('mru_jfb')->where(get(openid,token))->find();
		$this->assign('list',$list);
	    $this->UDisplay();
	}
	
	public function ajax(){
		if(IS_AJAX){
			$key=$_POST['key'];
			$val=$_POST['val'];
			$b=M('mru_jfb')->where(get(token,openid))->save(array($key=>$val));
			$this->ajaxReturn($b);
		}
	}
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
        $list=M('mru_tiyan')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
		$this->UDisplay();
	}

	
}
?>
