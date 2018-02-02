<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruWyyyAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
		if(IS_POST){
			
			  $ffff=M('mru_jfb')->where(array('openid'=>OP,'token'=>TO))->save(array('name2'=>$_POST['dz']));
		
			  if($_POST['type']=='免费体验预约'){
			  	            $_POST['time_xs']=$_POST['time'].",".$_POST['xs'];
			  	            $_POST['openid']=OP;
			  	            $_POST['token']=TO;
			  	            M('mru_wyyy')->add($_POST);
			  	            M('mru_tiyan')->where(array('token'=>TO,'name'=>$_POST['name']))->setInc('num',1);
			  	            rz('我要预约',"免费体验预约请去后台免费体验中查看详情");
			  	            script("报名成功","MruWyyy/index",get(token,openid));
			  	
			  }elseif($_POST['type']=='抢购预约'){
						  	$_POST['time_xs']=$_POST['time'].",".$_POST['xs'];
						  	$_POST['openid']=OP;
						  	$_POST['token']=TO;
						  	M('mru_wyyy')->add($_POST);
						  	rz('我要预约',"抢购预约");
						  	script("报名成功","MruWyyy/index",get(token,openid));
			  }else{
						  	$_POST['openid']=$_GET['openid'];
						  	MruMember("MruMhyzx/zc",$_GET['openid']);
						  	//
						  	//  $plcount=M('mru_pl')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->count();
						  	$conut=M('mru_wyyy')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'name'=>$_POST['name'],'type'=>$_POST['type']))->count();
						  	if($conut==1||$conut>1){
						  		//echo '<script>alert("该预约下你已经预约过了");location.href="'.U('MruMhyzxsy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
						  	}
						  		
						  	$_POST['time_xs']=$_POST['time'].",".$_POST['xs'];
						  	//print_r($_POST['name']);die;
						  	//求这个时间的预约总数
						  	$time_xs=M('mru_wyyy')->where(array('time_xs'=>$_POST['time_xs'],'name'=>$_POST['name']))->select();
						  	$timecount=count($time_xs);
						  		
						  	//求特殊表这个时间的预约总数
						  	$timeyd2=M('mru_yd2')->where(array('time'=>$_POST['time_xs'],'pid'=>$_POST['name']))->getField('zhi');
						  	//$timeyd2count=count($timeyd2);
						  	if($timeyd2){
						  	
						  		if($timecount>=$timeyd2){
						  			//echo "<script>alert('这个时间段预约人数已满！');history.back();</script>";die;
						  			echo '<script>alert("这个时间段预约人数已满！");location.href="'.U('MruWyyy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
						  		}
						  	}else{
						  	
						  		//求默认表这个时间的预约总数
						  		$timeyd=M('mru_yd')->where(array('pid'=>$_POST['name']))->getField("hour".$_POST['xs']);
						  		if($timecount>=$timeyd){
						  			//echo "<script>alert('这个时间段预约人数已满！');history.back();</script>";die;
						  			echo '<script>alert("这个时间段预约人数已满！");location.href="'.U('MruWyyy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
						  		}
						  	}
						  		
						  	
						  		
						  		
						  	$b=M('mru_wyyy')->add($_POST);
						  	if($b){
						  	
						  	
						  		//echo "<script>alert('预约成功');history.back();</script>";
						  		$mdian=M('mru_mdian')->where(array('id'=>$_POST['mdian']))->getField('name');
						  		$content=$mdian."下预约了类型".$_POST['type']."名称".$_POST['name']."时间段".$_POST['time'].$_POST['xs'];
						  		rz('我要预约',$content);
						  		echo '<script>alert("预约成功");location.href="'.U('MruWyyy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
						  		 
						  		 
						  	}else{
						  		//echo "<script>alert('预约失败');history.back();</script>";
						  		echo '<script>alert("预约失败");location.href="'.U('MruWyyy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
						  	}
			  }
			
		}else{
			MruMember("MruMhyzx/zc",$_GET['openid']);
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
			$content='sf地地地圭地地地地';
		    
			if($_GET['aid']){
				$md=M('mru_mdian')->where(array('id'=>$_GET['aid']))->find();
				
				$this->assign('md',$md);
			}
			
			$name2=M('mru_jfb')->where(array('openid'=>OP,'token'=>TO))->getField('name2');
			
			$this->assign('name2',$name2);
			$this->UDisplay();
			
		}
		
	}
	
	
	
	// 首页显示，店铺发送
	public function index2(){
	
		if(IS_POST){
				
			$_POST['openid']=$_GET['openid'];
			
			MruMember("MruMhyzx/zc",$_GET['openid']);
				
	
			//
			//  $plcount=M('mru_pl')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->count();
			$conut=M('mru_wyyy')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'name'=>$_POST['name'],'type'=>$_POST['type']))->count();
				
				
			if($conut==1||$conut>1){
				echo '<script>alert("该预约下你已经预约过了");location.href="'.U('MruMdian/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
			}
				
			$_POST['time_xs']=$_POST['time'].",".$_POST['xs'];
			//print_r($_POST['name']);die;
			//求这个时间的预约总数
			$time_xs=M('mru_wyyy')->where(array('time_xs'=>$_POST['time_xs'],'name'=>$_POST['name']))->select();
			$timecount=count($time_xs);
				
			//求特殊表这个时间的预约总数
			$timeyd2=M('mru_yd2')->where(array('time'=>$_POST['time_xs'],'pid'=>$_POST['name']))->getField('zhi');
			//$timeyd2count=count($timeyd2);
			if($timeyd2){
	
				if($timecount>=$timeyd2){
					//echo "<script>alert('这个时间段预约人数已满！');history.back();</script>";die;
					echo '<script>alert("这个时间段预约人数已满！");location.href="'.U('MruMdian/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
				}
			}else{
	
				//求默认表这个时间的预约总数
				$timeyd=M('mru_yd')->where(array('pid'=>$_POST['name']))->getField("hour".$_POST['xs']);
				if($timecount>=$timeyd){
					//echo "<script>alert('这个时间段预约人数已满！');history.back();</script>";die;
					echo '<script>alert("这个时间段预约人数已满！");location.href="'.U('MruMdian/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
				}
			}
				
	
				
				
			$b=M('mru_wyyy')->add($_POST);
			if($b){
	
	
				//echo "<script>alert('预约成功');history.back();</script>";
				echo '<script>alert("预约成功");location.href="'.U('MruMdian/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
				 
				 
			}else{
				//echo "<script>alert('预约失败');history.back();</script>";
				echo '<script>alert("预约失败");location.href="'.U('MruMdian/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
			}
		}else{
			include"./Lib/Action/Wap/mru.php";
			$token=$this->token;
				
/* 			//查出市区
			$shis=M('mru_shi')->where(array('token'=>$token))->select();
			$this->assign('shis',$shis); */
			//查出一周的时间
			for ($i=0; $i<7; $i++){
				$aDate[] = date('Y-m-d', strtotime('+'.$i.' days'));
			} //取得一周的时间  days为一天的单位, 不写默认有time()            strtotime('+'.$i.' days')==strtotime(time().'+'.$i.' days')
				
			$this->assign('Date',$aDate);
			//找到一天的9点到20到
			$sj=range(9, 20);
			$this->assign('aid',$_GET['aid']);
			$this->assign('sj',$sj);
			
			//P($_GET['aid']);
			//查出该店
			$list=M('mru_mdian')->where(array('id'=>$_GET['aid']))->find();
			$this->assign('list',$list);
			//查出该店下的预约
			$yy=M('mru_dianphd')->where(array('aid'=>$_GET['aid']))->select();
			$this->assign('yy',$yy);
			if(!$yy){
				script("这店铺下没有发布预约");
			}
			MruMember("MruMhyzx/zc",$_GET['openid']);
	
			$this->UDisplay();
				
		}
	
	}
	
	
	public function show(){
        $list=M('mru_dianphd')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
		$this->UDisplay();
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
				//P($mdians);
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
		
		if($_POST['type']=='免费体验预约'){
			$yy=(array)M('mru_dianphd')->where(array('aid'=>$_POST['aid'],'type'=>$_POST['type'],'token'=>$_POST['token'],'state'=>1,'state2'=>1,'audit'=>1))->order("sort")->select();
			$yy2=(array)M('mru_tiyan')->where(array('token'=>TO,'statu'=>1))->select();
			$yy3=array_merge($yy,$yy2);
			$str2="<option value=''>请选择预约</option>";
			foreach ($yy3 as $v){
			$str2.=<<<str
           <option value="{$v['name']}">{$v['name']}</option>
str;
			}
			
			$res['str2']=$str2;
			
			$this->ajaxReturn($res);
		}elseif($_POST['type']=='抢购预约'){
			$list=M('mru_qgj')->where(array('openid'=>OP,'state'=>0,'token'=>TO))->select();
			
			$str2="<option value=''>请选择预约</option>";
			foreach ($list as $v){
				$str2.=<<<str
           <option value="{$v['name']}">{$v['name']}</option>
str;
			}
				
			$res['str2']=$str2;
				
			$this->ajaxReturn($res);
			
		}else{
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

  
	public function index3 (){
		if(IS_AJAX){
			
			$ffff=M('mru_jfb')->where(array('openid'=>OP,'token'=>TO))->save(array('name2'=>$_POST['dz']));
			
			if($_POST['type']=='免费体验预约'){
				$_POST['time_xs']=$_POST['time'].",".$_POST['xs'];
				$_POST['openid']=OP;
				$_POST['token']=TO;
				M('mru_wyyy')->add($_POST);
				M('mru_tiyan')->where(array('token'=>TO,'name'=>$_POST['name']))->setInc('num',1);
				rz('我要预约',"免费体验预约请去后台免费体验中查看详情");
				//script("报名成功","MruWyyy/index",get(token,openid));
			    $res['str']='报名成功';
			}elseif($_POST['type']=='抢购预约'){
				$_POST['time_xs']=$_POST['time'].",".$_POST['xs'];
				$_POST['openid']=OP;
				$_POST['token']=TO;
				M('mru_wyyy')->add($_POST);
				rz('我要预约',"抢购预约");
				//script("报名成功","MruWyyy/index",get(token,openid));
				$res['str']='报名成功';
			}else{
				$_POST['openid']=$_GET['openid'];
				
				//
				//  $plcount=M('mru_pl')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->count();
				$conut=M('mru_wyyy')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'name'=>$_POST['name'],'type'=>$_POST['type']))->count();
				if($conut==1||$conut>1){
					//echo '<script>alert("该预约下你已经预约过了");location.href="'.U('MruMhyzxsy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
				}
			
				$_POST['time_xs']=$_POST['time'].",".$_POST['xs'];
				//print_r($_POST['name']);die;
				//求这个时间的预约总数
				$time_xs=M('mru_wyyy')->where(array('time_xs'=>$_POST['time_xs'],'name'=>$_POST['name']))->select();
				$timecount=count($time_xs);
			
				//求特殊表这个时间的预约总数
				$timeyd2=M('mru_yd2')->where(array('time'=>$_POST['time_xs'],'pid'=>$_POST['name']))->getField('zhi');
				//$timeyd2count=count($timeyd2);
				if($timeyd2){
					if($timecount>=$timeyd2){
						//echo "<script>alert('这个时间段预约人数已满！');history.back();</script>";die;
						//echo '<script>alert("这个时间段预约人数已满！");location.href="'.U('MruWyyy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
						$res['str']='这个时间段预约人数已满！';
						$this->ajaxReturn($res);die;
					}
				}else{
					//求默认表这个时间的预约总数
					
					$timeyd=M('mru_yd')->where(array('pid'=>$_POST['name']))->getField("hour".$_POST['xs']);
					
					if($timecount>=$timeyd){
						//echo "<script>alert('这个时间段预约人数已满！');history.back();</script>";die;
						//echo '<script>alert("这个时间段预约人数已满！");location.href="'.U('MruWyyy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
						$res['str']='这个时间段预约人数已满！';
						$this->ajaxReturn($res);die;
					}
				}
				$_POST['token'] = TO;
				$b=M('mru_wyyy')->add($_POST);
				if($b){
						
						
					//echo "<script>alert('预约成功');history.back();</script>";
					$mdian=M('mru_mdian')->where(array('id'=>$_POST['mdian']))->getField('name');
					$content=$mdian."下预约了类型".$_POST['type']."名称".$_POST['name']."时间段".$_POST['time'].$_POST['xs'];
					rz('我要预约',$content);
					//echo '<script>alert("预约成功");location.href="'.U('MruWyyy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
					$res['str']='预约成功';
						
				}else{
					//echo "<script>alert('预约失败');history.back();</script>";
					//echo '<script>alert("预约失败");location.href="'.U('MruWyyy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
					$res['str']='预约失败';
					$this->ajaxReturn($res);die;
				}
			}
			$this->ajaxReturn($res);
		}
		
	} 
	
	
	
}
?>
