<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MruQianggouAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();

		




	}

	// 首页显示，店铺发送
	public function index(){
		include"./Lib/Action/Wap/mru.php";
			if($_GET['id']){
				//分类查询出
				$type=M('mru_ceshi')->where(array('id'=>$_GET['id']))->getField(type);
				switch ($type){
					case '减肥':$type=1;break;
					case '美容':$type=2;break;
					case '亚健康':$type=3;break;
					case '其它':$type=4;break;
				}
				$count      = M('mru_qianggou')->where(array('token'=>$_GET['token'],'state2'=>1,'type'=>$type))->count();
				$Page       = new Page($count,15);
				$show       = $Page->show();
				$list = M('mru_qianggou')->where(array('token'=>$_GET['token'],'state2'=>1,'type'=>$type))->field('content',true)->order('sort')->limit($Page->firstRow.','.$Page->listRows)->select();
				$this->assign('page',$show);
				$this->assign('list',$list);

				switch ($type){
					case 1:$type='减肥';break;
					case 2:$type='美容';break;
					case 3:$type='亚健康';break;
					case 4:$type='其它';break;
				}
				//P($type);die;
				$this->assign('type',$type);
			}elseif($_GET['aid']){

				
				$count      = M('mru_qianggou')->where(array('token'=>$_GET['token'],'aid'=>$_GET['aid'],'state2'=>1))->count();
				$Page       = new Page($count,15);
				$show       = $Page->show();
				$list = M('mru_qianggou')->where(array('token'=>$_GET['token'],'aid'=>$_GET['aid'],'state2'=>1))->order('sort')->limit($Page->firstRow.','.$Page->listRows)->select();
				if (!$list) script("该店面下没有发布项目");
				$this->assign('page',$show);
				$this->assign('list',$list);
				$firstRow=ceil($count/15);//总数除以每页显示多少条，有小数加1 得到总页数
				$this->assign('firstRow',$firstRow);//把总页数分配过去
				
			
				
			}else{
				$token=$this->token;
				//$list=M('mru_qianggou')->where(array('token'=>$token))->field('content',true)->select();

				$count      = M('mru_qianggou')->where(array('token'=>$token,'state2'=>1))->count();
				$Page       = new Page($count,15);
				$show       = $Page->show();
				$list = M('mru_qianggou')->where(array('token'=>$token,'state2'=>1))->field('content',true)->order('sort')->limit($Page->firstRow.','.$Page->listRows)->select();
  
				$this->assign('page',$show);
				$this->assign('list',$list);
				$firstRow=ceil($count/15);//总数除以每页显示多少条，有小数加1 得到总页数
				$this->assign('firstRow',$firstRow);//把总页数分配过去
				
			}

			$this->UDisplay();



	}

	public function show(){

        if($_GET['openid']){
        	$openid=$_GET['openid'];
        }else{
        	$openid=$_GET['op'];
        }
		$this->assign('openid',$openid);
	
		include"./Lib/Action/Wap/mru.php";

        $list=M('mru_qianggou')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);

		$this->UDisplay();
	}

	public function fx(){



		
		
		
		if(IS_AJAX){
			/*$aData['token']=$this->token;
			 $aData['openid']=$this->openid;
			$aData['reg_time']=time();
			$oHomefx_user=M('Homefx_user');
			if($oHomefx_user->where(array('token'=>$aData['token'],'openid'=>$aData['openid']))->find()){
			$arr['state']=1;
			}else{
			$idWxuser=M('Wxuser')->where(array('token'=>$this->token))->getField('id');//得到共公号ID
			$aWxusers=M('Wxusers')->field('city,province,country')->where(array('uid'=>$idWxuser,'openid'=>$this->openid))->find();//得到本人的名字省市资料
			$aData['loc_province']=$aWxusers['province'];
			$aData['loc_city']=$aWxusers['city'];
			$aUsercenter_memberlist=M('Usercenter_memberlist')->field('name,phone,address')->where(array('uid'=>$idWxuser,'openid'=>$this->openid))->find();//得本人的一些资料
			$aData['phone']=$aUsercenter_memberlist['phone'];
			$aData['name']=$aUsercenter_memberlist['name'];
			if($oHomefx_user->add($aData)){
			$arr['state']=1;
			}else{
			$arr['state']=0;
			}*/
			$arr['state']=1;
			echo json_encode($arr);
		}else {
		
			/* $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$where = array('token' => $this->token, 'id' => $id);
			$product = $this->product_model->where($where)->find();
			if (empty($product)) {
				$this->redirect(U('Store_new/products',array('token' => $this->token,'wecha_id' => $this->wecha_id,'dopenid'=>$this->dopenid)));
			}
			$product['intro'] = isset($product['intro']) ? htmlspecialchars_decode($product['intro']) : '';
			$this->assign('product', $product);
		
		
			$id = $this->_get("id");//商品id
			$where['id'] = array('eq', $id);
			$aProduct = $this->product_model->where($where)->find();
			$this->assign("shop", $aProduct); */
		
		/* 	$sImageUrl = $this->getCode();
			$this->assign("image", $sImageUrl);//生成二维码图片 */
		
			/**
			 * 没有受权，获取appid
			 */
			$appidInfo=M('Diymen_set')->where(array('token'=>$this->token))->find();
			$this->assign('appidInfo',$appidInfo);
		
			//查出已有多少人分亨
			$pNum=M('Homenice_user')->where(array('token'=>$this->token))->count();
			$this->assign('pNum',$pNum);
			
			
			$list=M('mru_qianggou')->where(array('id'=>$_GET['id']))->find();
			$this->assign('list',$list);
			$this->assign('openid',$_GET['openid']);
			$this->assign('token',$_GET['token']);
			$this->assign("image", $this->getCode());//生成二维码图片
			
			/**
			 * 没有受权，获取appid
			 */
			$appidInfo=M('Diymen_set')->where(array('token'=>$this->token))->find();
			$this->assign('appidInfo',$appidInfo);
			
			
			$this->assign('content',M('mru_mt')->where(array('token'=>TO))->getField("content2"));
			
			$this->UDisplay();
		}
	}

public function zf(){
	//如果没有注册去注册
  
	MruMember("MruMhyzx/zc",$_GET['openid']);
	

	
	//M('mru_hb')->
	//没有镇资料去镇
	$data=M('mru_jfb')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->find();
    if(!$data['tel']) script("手机不能为空哦","Mrugr/index",get(token,openid));
    if(!$data['address']) $data['address']='该用户没镇写地址';
	$data['orderid'] =$this->getSn();//获取唯一订单号
	$data['token']=$this->_get('token');
	$data['wecha_id']=$this->_get('openid');//openid
	$data['type']="par";//par无规格son有
	$data['productid']=$this->_get('id');//商品id
	
	$data['dopenid']=$this->_get('dopenid');//商品id
 	$data['total']=1;//数量默认1
 	//查出价格
 	$list=M("mru_qianggou")->where(array('id'=>$_GET['id']))->find();
 	$price=$list['price3'];
 	$data['logistics']=rtrim($data['logistics'],',');
 	$data['price']=$price;//价格
	$data['tel']=$data['tel'];//手机
	$data['truename']=$data['name']?:'-';//姓名
	$data['address']=$data['address'];;//地址
	$data['time']=time();//下单时间
	//$data['paid']=0;//0未付款1已付款
	unset($data['id']);
	
	
	if($_GET['hb']){
		$_GET['hb']=ltrim($_GET['hb'],'undefined');
		$_GET['hb']=rtrim($_GET['hb'],',');
		$hb=explode(',',$_GET['hb']);
		$hbs=M('mru_hb')->where(array('id'=>array('in',$hb)))->select();
		foreach ($hbs as $v){
			$hbprice+=$v['price'];
			$data['logistics'].=$v['id'].",";
			if($hbprice>$price){
				$data['logistics']=rtrim($data['logistics'],',');
				$b=M("product_cart_new")->add($data);//订单表添加
				$list2=M("product_cart_new")->where(array('id'=>$b))->find();
				script("","MruWdyhj/zf",get1(orderid,$list2['orderid'],openid,token));
			}
		}
	}
	$data['logistics']=rtrim($data['logistics'],',');
	$b=M("product_cart_new")->add($data);//订单表添加
	
	if($b){
		rz('限时抢购','产生了一笔订单请在后台会员中心->订单管理中查看详情');
	}else{
		echo "<script>alert('订单生成失败');</script>";
	}
	$this->assign('price',$price-$hbprice);
	$this->assign('orderid',$data['orderid']);
    $this->UDisplay();
}





	/**
	 * @param string $pid
	 * @return mixed生成二维码图片
	 */
	public function getCode() {

		$userinfo=M("Wxusers")->field("id")->where(array("openid"=>$this->openid))->find();

		$parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 200'.$userinfo['id'].'}}}';
        if (M('Code')->where(array('id' => $userinfo['id']))->find()) {
            M('Code')->where(array('id'=>$userinfo['id']))->data(array(
                'value' => serialize(array(
                    'shop_id'   => $this->_get("id"),
                    'openid'    => $_GET['openid'],
                    'user_id'   => M('Wxuser')->where(array('token'=>$this->token))->getField('id')
                ))
            ))->save();
        }else{
            M('Code')->data(array(
                'id'        => $userinfo['id'],
                'value' => serialize(array(
                    'shop_id'   => $this->_get("id"),
                    'openid'    => $_GET['openid'],
                    'user_id'   => M('Wxuser')->where(array('token'=>$this->token))->getField('id')
                ))
            ))->add();
        }

		/*获取access_token*/

		$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
		//p($api);
		if($api){

			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];


			$json = json_decode(file_get_contents($url_get));
			$access_token = $json->access_token;
			// $access_token = "GVkLr2R7pPpgnmCHovoSkJqYugUzNA1y4crUwpZlenhMk80qRD9ijinh2O8BwL3ACwqoCGxohSlath0OJK5AslJH7dSnNx9foGvJ_UjTEdU";

			$imgSource = $this->creatTicket($access_token, $parament);

		}

		return $imgSource['header']['url'];

	}

	/**
	 * @param $token
	 * @param $parament
	 * @return array   生成二维码
	 */
	public function creatTicket($token, $parament) {
		/*发送数据到微信服务器端并获取数据*/

		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";

		$result = $this->api_notice_increment($url, $parament);
		$jsonInfo = json_decode($result, true);
		$ticket = $jsonInfo['ticket'];

		/*根据ticket获取图片资源*/

		$url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url2);

		curl_setopt($ch, CURLOPT_HEADER, 0);

		curl_setopt($ch, CURLOPT_NOBODY, 0);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpInfo = curl_getinfo($ch);
		return array_merge(array('body'=>$package), array('header'=>$httpInfo));
	}
	// 获取唯一订单号
	public function getSn(){

		return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}
	
	
	public function confirm(){
	/* 	$list=M('mru_hb')->where(array('token'=>TO,'state'=>0,'openid'=>$_GET['openid']))->select();
		//查出价格
		$price3=M('mru_qianggou')->where(get(id))->getField('price3');
		$str=0;
		//判断红包是否超出
		foreach ($list as $ke=>$v){
		     $v['price'];
		}
		P($list); */
		echo '<script>if(confirm("你要使用红包购买吗")){ location.href="'.U('yes',get1(hb,10,id,token,openid,dopenid)).'";}else{ location.href="'.U('no',get(id,token,openid,dopenid)).'"; };</script>';
		if($list){
			
		}else{
			script("","zf",get(id,token,openid,dopenid));
		}
		
		
	}
	
	public function yes(){
		script("","zf",get1(hb,10,id,token,openid,dopenid));
	}
	
	public function no(){
		script("","zf",get(id,token,openid,dopenid));
	}
	
	
   public function hb(){
   	
   	 $list=M('mru_hb')->where(array('token'=>TO,'openid'=>OP,'state'=>0))->select();
   	 if(!$list) script("","zf",get(token,openid,id,dopenid));
   	 $this->assign('price',M('mru_qianggou')->where(get(id))->getField('price3'));
   	 $this->assign('list',$list);
   	 
   	 $this->UDisplay();
   }


}
?>
