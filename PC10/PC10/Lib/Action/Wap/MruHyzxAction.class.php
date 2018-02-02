<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruHyzxAction extends BaseAction{
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
		$token=$this->token;
	    //$list=M('mru_jf')->where(array('token'=>$token))->field('content',true)->select();
	    
	    /*$count      = M('mru_jf')->where(array('token'=>$token,'state'=>1))->count();
	    $Page       = new Page($count,15);
	    $show       = $Page->show();*/
	    $list = M('mru_jf')->where(array('token'=>$token,'state'=>1))->field('content',true)->order('sort')->select();
	    //$this->assign('page',$show);
	    $this->assign('list',$list);
	    /*$firstRow=ceil($count/15);//总数除以每页显示多少条，有小数加1 得到总页数
	    $this->assign('firstRow',$firstRow);//把总页数分配过去*/
	    
	    //广告位
	    $pic=M('mru_ggw')->select();
	    $this->assign('pic',$pic);
	    //p($pic);
	//  P($list);

		$this->UDisplay();
	}
	
	
	public function show(){
		include"./Lib/Action/Wap/mru.php";
         $list=M('mru_jf')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
        //查出当前积分
        $num=M('mru_jfb')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid']))->getField(num);
        $this->assign('num',$num);
       
        //广告位
        $pic=M('mru_ggw')->select();
        $this->assign('pic',$pic);
		$this->UDisplay();
	}

	
	
	public function ajax(){
		if(IS_AJAX){
			$key=$_POST['key'];
			$val=$_POST['val'];
			$token=$_GET['token'];
			$openid=$_GET['openid'];
			//判断数据是否存在，不存在添加，存在修改 mru_mhyzxsy个人资料表
			$bb=M('mru_mhyzxsy')->where(array('openid'=>$openid,'token'=>$token))->find();
			if($bb){
				$b=M('mru_mhyzxsy')->where(array('openid'=>$openid,'token'=>$token))->save(array($key=>$val));
				if($b){
					$res['str']="修改成功";
				}else{
					$res['str']="修改失败";
				}
			}else{
				$b=M('mru_mhyzxsy')->add(array($key=>$val,'token'=>$token,'openid'=>$openid));
				if($b){
					$res['str']="修改成功";
				}
			}
			$this->ajaxReturn($res);
		}
	}
	
	public function dh(){
		MruMember("Mhyzx/zc",$_GET['openid']);
		//判断库存
		if($_GET['jfvnum']>$_GET['jfnum']||$_GET['jfvnum']==$_GET['jfnum']){
			//echo "<script>alert('库存不足');history.back();</script>";die;
			echo '<script>alert("该物品库存不足");location.href="'.U('MruHyzx/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
			//echo '<script>alert("预约成功");location.href="'.U('MruMhyzxsy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
		}
		if($_GET['jf']>$_GET['num']){
			//echo "<script>alert('积分不够1');history.back();</script>";die;
			echo '<script>alert("积分不够");location.href="'.U('MruHyzx/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
		}
		//查当用户地址
		$dz=M('mru_jfb')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid']))->getField('address');
		if(!$dz){
			//echo "<script>alert('请去个人中心镇写您的地址');history.back();</script>";die;
			echo '<script>alert("请去个人中心镇写您的地址");location.href="'.U('Mrugr/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
		}
		//判断是否已兑换
		if(M('mru_jfxx')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'uid'=>$_GET['uid']))->find()){
			//echo "<script>alert('您已经兑换过一次了');history.back();</script>";die;
			echo '<script>alert("该物品您已经兑换过一次了");location.href="'.U('MruHyzx/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
		}else{
			$_GET['dz']=$dz;
			$_GET['add_time']=time();
			M('mru_jfxx')->add($_GET);
			//扣除积分
			M('mru_jfb')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid']))->setDec('num',$_GET['jf']);
			//已领数量+1
			$a=M('mru_jf')->where(array('token'=>$_GET['token'],'id'=>$_GET['uid']))->setInc('vnum',1);
			//echo "<script>alert('兑换成功');history.back();</script>";die;
			rz('积分兑换','用积分兑换了物品请在后台会员中心->积分兑换中查看详情');
			echo '<script>alert("兑换成功");location.href="'.U('MruHyzx/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'";</script>';die;
		}
	}
}
?>
