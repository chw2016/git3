<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MrucsAction extends BaseAction{
	
	public $_sTplBaseDir = 'Wap/default/mru';
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		$token=$this->token;
	    
	    
/* 	    $count      = M('mru_ceshi')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_ceshit')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);
	    $this->assign('list',$list); */
		$list=M('mru_ceshi')->where(array('token'=>$_GET['token']))->order("sort")->select();
		$this->assign('list',$list); 
	
		$this->UDisplay();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
        $list=M('mru_ceshit')->where(array('pid'=>$_GET['id']))->order("sort")->select();
       // P($list);
        $this->assign('list',$list);
        switch ($list['0']['type']){
        	case 1:$list['0']['type']='减肥';break;
        	case 2:$list['0']['type']='美容';break;
        	case 3:$list['0']['type']='亚健康';break;
        	case 4:$list['0']['type']='其它';break;
        }
        $this->assign('type',$list['0']['type']);
		$this->UDisplay();
	//	P($list);
	}
	
	
	public function ajax(){
	    if(IS_AJAX){
	    	
	    	
	    	M('mru_da')->where(array('id'=>array('gt',0)))->delete();
	    	foreach ($_REQUEST['data'] as $ke=>$v){
	    		M('mru_da')->add(array('token'=>$_GET['token'],'uid'=>$ke,name=>$v));
	    	}
    		//$res['str']=$_REQUEST['data'];
    		$res['page']=22;
    		$this->ajaxReturn($res);
        }
	}
	
	public function da(){
		include"./Lib/Action/Wap/mru.php";
		$list=M('mru_da')->query("select c.uid,c.name,a.name as name2,a.aaname,a.bbname,a.ccname,a.ddname from tp_mru_da c left join tp_mru_ceshit a on c.uid=a.id");
		
		foreach ($list as $ke=>$v){
			
			$list[$ke]['title']=$v[$v['name'].$v['name'].'name'];
		}
		
		$this->assign('list',$list);
		//P($list);
		
		//分类查询出
		$type=M('mru_ceshi')->where(array('id'=>$_GET['id']))->getField(type);
		switch ($type){
			case '减肥':$type=1;break;
			case '美容':$type=2;break;
			case '亚健康':$type=3;break;
			case '其它':$type=4;break;
		}
		$count      = M('mru_qianggou')->where(array('token'=>$_GET['token'],'state2'=>1,'type'=>$type))->count();
		$Page       = new Page($count,100);
		$show       = $Page->show();
		$list2 = M('mru_qianggou')->where(array('token'=>$_GET['token'],'state2'=>1,'type'=>$type))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);
		$this->assign('list2',$list2);
			
		switch ($type){
			case 1:$type='减肥';break;
			case 2:$type='美容';break;
			case 3:$type='亚健康';break;
			case 4:$type='其它';break;
		}
		//P($type);die;
		//P($list2);
		$this->assign('type',$type);
		
	
		$this->UDisplay();
	}
	
	
	
	

	
}
?>
