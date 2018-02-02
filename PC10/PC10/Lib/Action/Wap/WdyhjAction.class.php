<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class WdyhjAction extends BaseAction{
	

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		$list=M('mru_yhj2')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>0))->select();
		foreach ($list as $k=>$v){
			$data=M('mru_wdyhj')->where(array('id'=>$v['uid']))->find();
			$list[$k]['yzm']=$data['yzm'];
			$list[$k]['price']=$data['price'];
			$list[$k]['pic']=$data['pic'];
		}
		$this->assign('list',$list);
 		
/*		if($uid){
			$uid=implode($uid, ',');
			$list=M('mru_wdyhj')->query("select * from tp_mru_wdyhj where id  in ($uid)");
			$this->assign('list',$list);
		}else{
			
		} */
		
		
		//P($list);
		//M('mru_wdyhj')->where(array('id'=>$uid))->
/* 		$token=$this->token;
	    $list=M('mru_wdyhj')->where(array('token'=>$token))->field('content',true)->select();
	    
	    $count      = M('mru_wdyhj')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_wdyhj')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);*/
	   
	     
	  //  p($list);
		$this->display();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
    
        
        $list=M('mru_yhj2')->where(array('id'=>$_GET['id']))->select();
        foreach ($list as $k=>$v){
        	$data=M('mru_wdyhj')->where(array('id'=>$v['uid']))->find();
        	$list[$k]['yzm']=$data['yzm'];
        	$list[$k]['price']=$data['price'];
        	$list[$k]['pic']=$data['pic'];
        	$list[$k]['name']=$data['name'];
        	$list[$k]['j_time']=$data['j_time'];
        	$list[$k]['content']=$data['content'];
        }
        $this->assign('list',$list);
        
      
        $this->assign('list',$list[0]); 
		
		
		$this->display();
	}

	
}
?>
