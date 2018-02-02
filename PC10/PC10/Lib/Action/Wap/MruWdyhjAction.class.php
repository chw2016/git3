<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruWdyhjAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();



	}

	// 首页显示，店铺发送
	public function index(){

		include"./Lib/Action/Wap/mru.php";
		//查出优惠卷
		$list=M('mru_yhj2')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>0))->order("add_time desc")->select();
		foreach ($list as $k=>$v){
			$data=M('mru_wdyhj')->where(array('id'=>$v['uid']))->find();
			$list[$k]['yzm']=$data['yzm'];
			$list[$k]['price']=$data['price'];
			$list[$k]['pic']=$data['pic'];
		}
		$this->assign('list',$list);

		//查出抢购卷
 		$qgj=M('mru_qgj')->where(array('openid'=>$_GET['openid'],'state'=>0))->order("add_time desc")->select();
 		$this->assign('qgj',$qgj);
 		//查出红包
 		$hb=M('mru_hb')->where(array('openid'=>$_GET['openid'],'state'=>0))->order("add_time desc")->select();
 		$this->assign('hb',$hb);

 		if(!$list && !$qgj && !$hb){
 			$this->assign('xx','暂无优惠券');
 		}

		$this->UDisplay();
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





		$this->UDisplay();
	}

	public function zf(){

		if($_GET['orderid']){
			//查出商品
			$list=M("Product_cart_new")->where(array("orderid"=>$_GET['orderid']))->find();
			$productid=$list['productid'];
			if($list['logistics']){
				$list['logistics']=explode(',', $list['logistics']);
				$hb=M('mru_hb')->where(array('id'=>array('in',$list['logistics'])))->save(array('state'=>1));
				if(!$hb) script("红包已使用","index",get(token,openid,dopenid));
			}

			$isSave = M("Product_cart_new")->where(array("orderid"=>$_GET['orderid']))->save(array("paid"=>1));//修改订单状态
			//购买人数+1
			$qgj=M('mru_qianggou')->where(array('id'=>$productid))->setInc('num',1);
			$qgj=M('mru_qianggou')->where(array('id'=>$productid))->find();

			$name=$qgj['title'];
			$yzm = mt_rand('1',100).time();
            $isFind = M('mru_qgj')->where(array(
                'token'=>$_GET['token'],
                'openid'=>$_GET['openid'],
                'orderid'=>$_GET['orderid']
            ))->find();
            if(!$isFind && $isSave){
                $b=M('mru_qgj')->add($a=array(
                    'token'=>$_GET['token'],
                    'openid'=>$_GET['openid'],
                    'price'=>$qgj['price3'],
                    'add_time'=>time(),
                    'yzm'=>$yzm,
                    'state'=>0,
                    'uid'=>$productid,
                    'name'=>$name,
                    'orderid'=>$_GET['orderid']
                ));
                file_put_contents('/tmp/log.log', '我来发优惠券'.print_r($_SERVER, true) . print_r($a, true) . '|' . date('Y-m-d H:i:s'), FILE_APPEND);
                //给好友+红包
                $yzm2 = mt_rand('1',100).time();
                if($list['dopenid'] && $qgj['hongbao']){
                    $bb=M('mru_hb')->add(array(
                        'token'=>$_GET['token'],
                        'openid'=>$list['dopenid'],
                        'name'=>$qgj['title'],
                        'add_time'=>time(),
                        'price'=>$qgj['hongbao'],
                        'state'=>0,
                        'uid'=>$productid,
                        'yzm'=>$yzm2
                    ));
                    //if($bb) echo "<script>alert('给好友送了一个价值".$qgj['hongbao']."的红包');</script>";
                    $bb=M('mru_hy')->where(array('openid'=>$list['dopenid'],'dopenid'=>$_GET['openid'],'token'=>$_GET['token']))->find();
                    if(!$bb) M('mru_hy')->add(array('openid'=>$list['dopenid'],'dopenid'=>$_GET['openid'],'token'=>$_GET['token'],'add_time'=>time(),'name'=>$qgj['title']));

                    //获取积分红包记录
                    M('mru_xf')->add(array(
                        'token'=>$_GET['token'],
                        'openid'=>$list['dopenid'],
                        'hongbao'=>$qgj['hongbao'],
                        'fs'=>'购买限时抢购商品',
                        'name'=>$productid,
                        'add_time'=>time(),
                    ));
                    if($b) script("购买成功!获取一张价值".$qgj['price3']."的抢购券","index",get(token,openid,dopenid));
                    return;
                }
                //把好友存进好友表
                if($b) script("购买成功!获取一张价值".$qgj['price3']."的抢购券","index",get(token,openid,dopenid));
                return;

            }else{
                $this->redirect(U('index', get(token, openid, dopenid)));
                return;
            }
		}

	}

	public function show2(){

		$list2=M('mru_qianggou')->where(array('id'=>$_GET['uid']))->find();
		if($_GET['type']=='红包'){
			$list=M('mru_hb')->where(array('id'=>$_GET['id']))->find();
			$list2['content']=M('mru_mt')->field('content')->getField('content');
		}else{
			$list=M('mru_qgj')->where(array('id'=>$_GET['id']))->find();
		}

		$this->assign('list',$list);
		$this->assign('list2',$list2);
		$this->assign('type',$_GET['type']);
		$this->UDisplay();
	}


}
?>
