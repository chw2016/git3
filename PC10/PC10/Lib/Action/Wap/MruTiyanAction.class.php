<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruTiyanAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		
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
		$this->UDisplay();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
		include"./Lib/Action/Wap/mrufx.php";
        $list=M('mru_tiyan')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
       // MruMember("Mhyzx/zc",$_SESSION['openid']);
        
        
		$this->UDisplay();
	}
	
	public function ty(){
		
	if(IS_POST){
			
		 

			  
			 
			//
			//  $plcount=M('mru_pl')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->count();
				/*   $conut=M('mru_wyyy')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'name'=>$_POST['name'],'type'=>$_POST['type']))->count();
			  
			  
 		  if($conut==1||$conut>1){
			  	echo '<script>alert("该预约下你已经预约过了");location.href="'.U('MruMhyzxsy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
			  }  */
			  
	/* 		  $_POST['time_xs']=$_POST['time'].",".$_POST['xs']; */
			   //print_r($_POST['name']);die;
			  //求这个时间的预约总数
/* 			  $time_xs=M('mru_wyyy')->where(array('time_xs'=>$_POST['time_xs'],'name'=>$_POST['name']))->select();
			  $timecount=count($time_xs); */
			
/* 			  //求特殊表这个时间的预约总数
			  $timeyd2=M('mru_yd2')->where(array('time'=>$_POST['time_xs'],'pid'=>$_POST['name']))->getField('zhi');
			  //$timeyd2count=count($timeyd2);
			  if($timeyd2){
			  	
			  	if($timecount>=$timeyd2){
			  		//echo "<script>alert('这个时间段预约人数已满！');history.back();</script>";die;
			  		
			  		echo '<script>alert("这个时间段预约人数已满！");location.href="'.U('MruMhyzxsy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
			  	}
			  }else{ */
			  	
			  	//求默认表这个时间的预约总数
	/* 		  	$timeyd=M('mru_yd')->where(array('pid'=>$_POST['name']))->getField("hour".$_POST['xs']);
			  	P($timeyd);die; */
/* 			  	if($timecount>=5){
			  	
			  		//echo "<script>alert('这个时间段预约人数已满！');history.back();</script>";die;
			  		echo '<script>alert("这个时间段预约人数已满！");location.href="'.U('MruMhyzxsy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
			  	} */
			  /* } */
			  
			 
			  
		      $_POST['openid']=$_GET['openid'];
		      $_POST['time_xs']=$_POST['time'].",".$_POST['xs'];
		      $b=M('mru_wyyy')->add($_POST);
		       
			  if($b){
			  	$mdian=M('mru_mdian')->where(array('id'=>$_POST['mdian']))->getField('name');
			  	$content=$mdian."下预约了类型".$_POST['type']."名称".$_POST['name']."时间段".$_POST['time'].$_POST['xs'];
			  	rz('我要预约',$content);
			  	 //echo "<script>alert('预约成功');history.back();</script>";
			  	M('mru_tiyan')->where(array('token'=>TO,'name'=>$_POST['name']))->setInc('num',1);
			  	 echo '<script>alert("预约成功");location.href="'.U('MruTiyan/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
			  	 
			  	 
			  }else{
			  	//echo "<script>alert('预约失败');history.back();</script>";
			  	echo '<script>alert("预约失败");location.href="'.U('MruTiyan/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
			  }
		}else{
			MruMember("MruMhyzx/zc",$_SESSION['openid']);
			
			$list=M('mru_tiyan')->where(array('id'=>$_GET['id']))->find();
			$this->assign('list',$list);
			$token=$this->token;
			
			//查出市区
			$shis=M('mru_shi')->where(array('token'=>$token))->select();
			$this->assign('shis',$shis);
			//查出一周的时间
			for ($i=0; $i<7; $i++){
				$aDate[] = date('Y-m-d', strtotime('+'.$i.' days'));
			} //取得一周的时间  days为一天的单位, 不写默认有time()            strtotime('+'.$i.' days')==strtotime(time().'+'.$i.' days')
			
			$this->assign('Date',$aDate);
			//找到一天的9点到20到
			$sj=range(9, 20);
			$this->assign('aid',$_GET['aid']);
			$this->assign('sj',$sj);
			$this->UDisplay();
			
		}

	}

	
	
	
	public function dian(){
	
		if(IS_AJAX){
			$token=$this->_post('token');
				
			$id=$this->_post('id');
			//$shi=M('mru_shi')->where(array('id'=>$id))->find();
			if($id==1){
				$mdians=M('mru_mdian')->where(array('token'=>$token))->select();
			}else{
				$mdians=M('mru_mdian')->where(array('shi'=>$id,'token'=>$token))->select();
			}
				
				
			//$mdians2=getInfo(20000,$mdians,$shi['position_y'],$shi['position_x']);
			$str="<option value=''>请选择店铺</option>";
			foreach ($mdians as $v){
				$str.=<<<str
           <option value="{$v['id']}">{$v['name']}</option>
str;
			}
			$res['str']=$str;
			$res['page']=22;
			$this->ajaxReturn($res);
		}
	
	
	}
	
	public function dz(){
	
		$token=$this->_post('token');
		$id=$this->_post('id');
		$dz=M('mru_mdian')->where(array('id'=>$id))->find();
		$yy=M('mru_dianphd')->where(array('aid'=>$id,'state'=>1,'state2'=>1,'token'=>$token,'audit'=>1))->order("sort")->select();
		$str2="<option value=''>请选择预约</option>";
		foreach ($yy as $v){
			$str2.=<<<str
           <option value="{$v['id']}">{$v['dname']}</option>
str;
		}
		$res['dz']=$dz['dz'];
		$res['dh']=$dz['dh'];
		$res['str2']=$str2;
		$this->ajaxReturn($res);
	}
	
	public function ajax () {
	
		$yy=M('mru_dianphd')->where(array('aid'=>$_POST['aid'],'type'=>$_POST['type'],'token'=>$_POST['token'],'state'=>1,'state2'=>1,'audit'=>1))->order("sort")->select();
		$str2="<option value=''>请选择预约</option>";
		foreach ($yy as $v){
			$str2.=<<<str
           <option value="{$v['id']}">{$v['dname']}</option>
str;
		}
	
		$res['str2']=$str2;
	
		$this->ajaxReturn($res);
	}
	

	
}
?>
