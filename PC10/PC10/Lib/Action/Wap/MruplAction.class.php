<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruplAction extends BaseAction{
	
	public $_sTplBaseDir = 'Wap/default/mru';
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){

/* 		$token=$this->token;
	    $list=M('mru_pl')->where(array('token'=>$token))->field('content',true)->select();
	    
	    $count      = M('mru_pl')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_pl')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);
	    $this->assign('list',$list); */
	    
	  //  p($list);
	  if(IS_POST){
	  	  $_POST['add_time']=time();
	  	  if(M('mru_pl')->where(post(openid,token,mdain,name))->find()){
	  	  	script("你已经评论过了","index",get(token,openid,id));die;
	  	  }
	  	  $b=M('mru_pl')->add($_POST);
	  	  
	  	  if($b){
	  	  	
	  	  	$plcount=M('mru_pl')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'name'=>$_POST['name']))->count();
	  	  	if($plcount==1){
	  	  		//第一次评论加积分
	  	  		M('mru_jfb')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->setInc('num',10);
	  	  		
	  	  		//获取积分红包记录
	  	  		M('mru_xf')->add(array(
	  	  				'token'=>$_GET['token'],
	  	  				'openid'=>$_GET['openid'],
	  	  				'num'=>10,
	  	  				'add_time'=>time(),
	  	  		));
	  	  		script("评论成功!积分+10","index",get(token,openid,id));

	  	  	}else{
	  	  		script("评论成功","MruQianggou/index",get(token,openid));
	  	  	}
	  	  }else{
	  	  	echo "<script>alert('评论失败');history.back();</script>";
	  	  }
	  }else{
	  	include"./Lib/Action/Wap/mru.php";
	  	//查出商品
	  	$data=M('mru_qianggou')->where(array('id'=>$_GET['id']))->find();
	  	if(!$data) script("商品不存在");
	  	//查出门店
	  	$dname=M('mru_mdian')->where(array('id'=>$data['aid'],'token'=>$_GET['token']))->getField('name');
	  	if(!$dname)$dname="鱼美人公司总部";
	  	$this->assign('dname',$dname);
	  	$this->assign('data',$data);
	  	
	  	$where=array(
	  			'token'=>$_GET['token'],
	  			'mdain'=>$dname,
	  			'name'=>$data['title'],
	  			'openid'=>$_GET['openid']//评论公自己可见
	  			);

	  
	  	//$list=M('mru_pl')->where($where)->select();
	  	
	  	
	  	$count      = M('mru_pl')->where($where)->count();
	  	$Page       = new Page($count,30);
	  	$show       = $Page->show();
	  	$list = M('mru_pl')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	  	$this->assign('page',$show);
	  	$this->assign('list',$list);

	  // P($data);

	  	$this->UDisplay();
	  }
		
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
        $list=M('mru_pl')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
		$this->UDisplay();
	}
	
	
	public function xx(){
		$orderid='2015060252495455';//$_GET['orderid'];
		$productid=M('product_cart_new')->where(array('orderid'=>$orderid))->getField('productid');
		$list=M('mru_qianggou')->where(array('id'=>$productid))->find();
		$time=date("Y年m月d日",time());
		//$list['aid']=28;
		if($list['aid']){
			$dname=M('mru_mdian')->where(array('id'=>$list['aid']))->getField('name');
		}else{
			$dname='鱼美人店';
		}
		
		$this->assign('dname',$dname);
		$this->assign('time',$time);
		$this->assign('list',$list);
		//P($list);
		$this->UDisplay();
	}

	
	public function index2(){
		/* 		$token=$this->token;
		 $list=M('mru_pl')->where(array('token'=>$token))->field('content',true)->select();
		 
		$count      = M('mru_pl')->where(array('token'=>$token))->count();
		$Page       = new Page($count,10);
		$show       = $Page->show();
		$list = M('mru_pl')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);
		$this->assign('list',$list); */
		 
		//  p($list);

		if(IS_POST){
			$_POST['add_time']=time();


            $awhere1 = array(
                'token'=>$this->token,
                'openid'=>$this->openid,
                'get_time'=>$_GET['time'],
                'mdian'=> base64_decode(str_replace(" ","+",$_GET['sStore'])), 
            );
			if(M('mru_pl')->where($awhere1)->find()){
				script("你已经评论过了","index2",get(token,openid,id,idcard,sStore,sCustom,time));exit;
			}
           /* $_POST['']*/
            $_POST['get_time'] = $_GET['time'];
            $_POST['content'] = base64_encode($_POST['content']);
			$b=M('mru_pl')->add($_POST);
			if($b){
                //第一次评论加积分
				M('mru_jfb')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->setInc('num',10);
				//获取积分红包记录
				M('mru_xf')->add(array(
                        'token'=>$_GET['token'],
                        'openid'=>$_GET['openid'],
                        'num'=>10,
                        'add_time'=>time(),
					));
				script("评论成功!积分+10","index2",get(token,openid,id,idcard,sStore,sCustom,time));
			}else{
				echo "<script>alert('评论失败');history.back();</script>";
			}
		}else{
			$idcard=$_GET['idcard'];//"123456";
			$token=$_GET['token'];//"5d8a87bab30de695954b17fc835b9d12";
			$openid=$_GET['openid'];//"123";
			$sStore=$_GET['sStore'];//"小分店";
			$sCustom=base64_decode($_GET['sCustom']);
			//$sCustom=substr($sCustom,1,strpos($sCustom,':')-1);
					// $sCustom=json_decode($sCustom,true);
			$sCustom=json_decode($sCustom,true);//"洗衣面奶";
			
			//P($sCustom); 
			$str='';
			foreach ($sCustom as $ke => $v){
				$str.=$ke.":".$v."&nbsp&nbsp";
			}
			//echo $str;
            $sStores = base64_decode(str_replace(" ","+",$sStore));
	        $this->assign('sStore',$sStores);
	        $this->assign('sCustom',$str);
	        $sCustom=$str;
			$where=array(
					'token'=>$_GET['token'],
					'mdain'=>$sStores,
					//'name'=>$sCustom,
					'openid'=>$openid//评论公自己可见
			);
			//$list=M('mru_pl')->where($where)->select();
			$count      = M('mru_pl')->where($where)->count();
			$Page       = new Page($count,30);
			$show       = $Page->show();
			$list = M('mru_pl')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($list as $k=>$val){
                $list[$k]['content'] = base64_decode($val['content']);
            }
			$this->assign('page',$show);
			$this->assign('list',$list);
			$this->UDisplay();
		}
	
	}
	
}
?>
