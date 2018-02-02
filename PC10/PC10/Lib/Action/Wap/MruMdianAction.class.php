<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruMdianAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}
	
	
	

	
	// 首页显示，店铺发送
/* 	public function index(){
	
		if(IS_AJAX){
			    
				$res['lat']=$this->_post('lat');
				$res['lng']=$this->_post('lng');
				
				$res['shiid']=$this->_post('shiid');
				$res['position_x']=$this->_post('position_x');
				$res['position_y']=$this->_post('position_y');
					
				if($res['shiid']){
					$shiid=$res['shiid'];
						
					$shi2=M('mru_shi')->where(array('id'=>$shiid))->find();
					$aaa=M('mru_mdian')->where(array('token'=>$_GET['token']))->field('token',true)->select();
					$list=getInfo(20000,$aaa,$shi2['position_y'],$shi2['position_x'],'position_y','position_x');//getInfo(距离,数据,y,x);第二个参数里有id name x y
				
					foreach ($list as $k=>$v){
						$list[$k]['jl']=floor(getdistance($res['lat'],$res['lng'],$v['position_y'],$v['position_x']));
					}
				
				}else{
				
		
					$list = M('mru_mdian')->where(array('token'=>$this->token))->select();
					
					foreach ($list as $k=>$v){
						$list[$k]['jl']=floor(getdistance($res['lat'],$res['lng'],$v['position_y'],$v['position_x']));
					}
				
				}
				
				$this->assign('list',$list);
				$x = $this->fetch('./tpl/Wap/default/mdian_little.html', $list);
					
				exit($x); 

				
			
		}else{
			
			
		  
			include"./Lib/Action/Wap/mru.php";
			  $token=$this->token;
		    $_SESSION['token']=$_GET['token'];
		    $_SESSION['openid']=$_GET['openid'];
		    //p($_SESSION);
		    //查出市区
		    $shi=M('mru_shi')->where(array('token'=>$token))->field('token',true)->select();
		    $this->assign('shi',$shi);
		    //查出市附近的门
		    $shiid=$this->_get('shiid');
		    if($shiid){
		    	$shi2=M('mru_shi')->where(array('id'=>$shiid))->find();
		    	$aaa=M('mru_mdian')->where(array('token'=>$token))->field('token',true)->select();
		    	$qdd=getInfo(20000,$aaa,$shi2['position_y'],$shi2['position_x']);//getInfo(距离,数据,y,x);第二个参数里有id name x y
		    	//	p($qdd);die;
		    	$this->assign('list',$qdd);
		    }else{
		    	//查询所有门店
		    	$count      = M('mru_mdian')->where(array('token'=>$token))->count();
		    	$Page       = new Page($count,20);
		    	$show       = $Page->show();
		    	$list = M('mru_mdian')->where(array('token'=>$token))->limit($Page->firstRow.','.$Page->listRows)->field('username,password',true)->select();
		    	//$this->assign('page',$show);
		    	$this->assign('list',$list);
		    	 
		    }
		   
		    $this->UDisplay();
	
			
		}
} */
	

public function index(){
		
	if(IS_AJAX){
		
		$res['lat']=$this->_post('lat');
		$res['lng']=$this->_post('lng');
        if(!$res['lng']) include"./Lib/Action/Wap/mru.php";
		$res['shi']=$this->_post('shi');
/* 		$res['position_x']=$this->_post('position_x');
		$res['position_y']=$this->_post('position_y'); */
			
		if($_GET['shi']){
			$list=M('mru_mdian')->where(array('shi'=>$_GET['shi'],'token'=>$_GET['token']))->order("sort")->select();
			//$list=getInfo(20000,$aaa,$shi2['position_y'],$shi2['position_x'],'position_y','position_x');//getInfo(距离,数据,y,x);第二个参数里有id name x y
            
			foreach ($list as $k=>$v){
				$list[$k]['jl']=floor(getdistance($res['lat'],$res['lng'],$v['position_y'],$v['position_x']))*2;
			}
			
			 foreach ($list as $k=>$v){
				$list[$v['jl']+$v['id']]['id']=$v['id'];
				$list[$v['jl']+$v['id']]['pic']=$v['pic'];
				$list[$v['jl']+$v['id']]['name']=$v['name'];
				$list[$v['jl']+$v['id']]['dh']=$v['dh'];
				$list[$v['jl']+$v['id']]['dz']=$v['dz'];
				$list[$v['jl']+$v['id']]['add_time']=$v['add_time'];
				$list[$v['jl']+$v['id']]['token']=$v['token'];
				$list[$v['jl']+$v['id']]['position_x']=$v['position_x'];
				$list[$v['jl']+$v['id']]['position_y']=$v['position_y'];
				$list[$v['jl']+$v['id']]['username']=$v['username'];
				$list[$v['jl']+$v['id']]['password']=$v['password'];
				$list[$v['jl']+$v['id']]['aid']=$v['aid'];
				$list[$v['jl']+$v['id']]['shi']=$v['shi'];
				$list[$v['jl']+$v['id']]['sort']=$v['sort'];
				$list[$v['jl']+$v['id']]['jl']=$v['jl'];
				unset($list[$k]);
				
			}
			

		}else{


			$list = M('mru_mdian')->where(array('token'=>$this->token))->order("sort")->select();
			
			foreach ($list as $k=>$v){
				$list[$k]['jl']=floor(getdistance($res['lat'],$res['lng'],$v['position_y'],$v['position_x']))*2;
			
			}
			foreach ($list as $k=>$v){
				$list[$v['jl']+$v['id']]['id']=$v['id'];
				$list[$v['jl']+$v['id']]['pic']=$v['pic'];
				$list[$v['jl']+$v['id']]['name']=$v['name'];
				$list[$v['jl']+$v['id']]['dh']=$v['dh'];
				$list[$v['jl']+$v['id']]['dz']=$v['dz'];
				$list[$v['jl']+$v['id']]['add_time']=$v['add_time'];
				$list[$v['jl']+$v['id']]['token']=$v['token'];
				$list[$v['jl']+$v['id']]['position_x']=$v['position_x'];
				$list[$v['jl']+$v['id']]['position_y']=$v['position_y'];
				$list[$v['jl']+$v['id']]['username']=$v['username'];
				$list[$v['jl']+$v['id']]['password']=$v['password'];
				$list[$v['jl']+$v['id']]['aid']=$v['aid'];
				$list[$v['jl']+$v['id']]['shi']=$v['shi'];
				$list[$v['jl']+$v['id']]['sort']=$v['sort'];
				$list[$v['jl']+$v['id']]['jl']=$v['jl'];
				unset($list[$k]);
				
			}
			
			

		}
        ksort($list);
		$this->assign('list',$list);
		$x = $this->fetch('./tpl/Wap/default/mru/mdian_little.html', $list);
			
		exit($x);

  
			
	}else{
			
			

		
 		$token=$this->token;


		if($_GET['shi']){
			
			$list=M('mru_mdian')->where(array('shi'=>$_GET['shi'],'token'=>$_GET['token']))->order("sort")->select();
			
		
			//$qdd=getInfo(20000,$aaa,$shi2['position_y'],$shi2['position_x']);//getInfo(距离,数据,y,x);第二个参数里有id name x y

			$this->assign('list',$list);
		}else{
			//查询所有门店
			$count      = M('mru_mdian')->where(array('token'=>$token))->count();
			$Page       = new Page($count,20);
			$show       = $Page->show();
			$list = M('mru_mdian')->where(array('token'=>$token))->limit($Page->firstRow.','.$Page->listRows)->order("sort")->field('username,password',true)->select();
			//$this->assign('page',$show);
			$this->assign('list',$list);

		}
		$_SESSION['token']=$_GET['token'];
		$_SESSION['openid']=$_GET['openid'];
		
		
		$this->UDisplay();

			
	}
}
	
	public function index2(){
		if(IS_AJAX){
			
		 	$res['lat']=$this->_post('lat');
			$res['lng']=$this->_post('lng');
		
			$res['str']=455445;
			
			//$this->ajaxReturn($res);
			
		}
				
		
	}
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
		$id=$_GET['id'];
		$token=$_GET['token'];
		//门店
		$list=M('mru_mdian')->where(array('id'=>$_GET['id']))->find();
		$this->assign('list',$list);
	//	P($list);
		$token=$this->token;
		//限时抢购
		$qianggou=M('mru_qianggou')->query("select * from tp_mru_qianggou where (concat(',', mdiao, ',') like '%,$id,%' and token ='$token' and state=1 and state2=1)  or (aid = $id and token ='$token' and state=1 and state2=1) order by sort");
		
		$this->assign('qianggou',$qianggou);
		//最新活动
		$id=$_GET['id'];
		//$huodong=M('mru_huodong')->query("select * from tp_mru_huodong where (status=1 and state2=1 and aid=0) or (aid=$id and status=1 and state2=1) order by add_time desc limit 0,20");
		//$huodong=M('mru_huodong')->where(array('token'=>$_GET['token'],'aid'=>$_GET['id']))->order('id desc')->select();
		
		$huodong=M('mru_huodong')->query("select * from tp_mru_huodong where (concat(',', mdiaos, ',') like '%,$id,%' and token ='$token' and status=1 and state2=1)  or (aid = $id and token ='$token' and status=1 and state2=1) order by sort");
		//select * from tp_mru_huodong where concat(',', mdiaos, ',') like '%,28,%' or aid = 28;
		//select * from tp_mru_huodong where (concat(',', mdiaos, ',') like '%,28,%' and token ='5d8a87bab30de695954b17fc835b9d12')  or (aid = 28 and token ='5d8a87bab30de695954b17fc835b9d12');
		//p($huodong);die;
		$this->assign('huodong',$huodong);
		

		
		$this->UDisplay();
	}



	
	public function  ajax(){
		include"./Lib/Action/Wap/mru.php";
	}
	
}
?>
