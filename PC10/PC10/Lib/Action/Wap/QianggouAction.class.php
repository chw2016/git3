<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class QianggouAction extends BaseAction{


	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();

		




	}

	// 首页显示，店铺发送
	public function index(){
		
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

			$this->display();



	}

	public function show(){




        $list=M('mru_qianggou')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);

		$this->display();
	}

	public function fx(){
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


		$this->display();
	}

public function zf(){
	//如果没有注册去注册

	MruMember("Mhyzx/zc",$_GET['openid']);
	
	//没有镇资料去镇
	$data=M('mru_mhyzxsy')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->find();
	if(!$data){
		echo '<script>alert("您还没镇写个人资料!请镇写个人资料");location.href="'.U('Mrugr/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';die;
	}
	if(!$data['tel']){
		echo '<script>alert("手机没镇!请去个人资料镇写手机号");location.href="'.U('Mrugr/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';die;
	}
	
	if(!$data['dz']){
		echo '<script>alert("地址没镇!请去个人资料镇写地址");location.href="'.U('Mrugr/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';die;
	}
	$data['orderid'] =$this->getSn();//获取唯一订单号
	$data['token']=$this->_get('token');
	$data['wecha_id']=$this->_get('openid');//openid
	$data['type']="par";//par无规格son有
	$data['productid']=$this->_get('id');//商品id
	$data['dopenid']=$this->_get('dopenid');//商品id
 	$data['total']=1;//数量默认1
 	//查出价格
 	$price=M("mru_qianggou")->where(array('id'=>$_GET['id']))->getField(price3);
	$data['price']=$price;//价格
	$data['tel']=$data['tel'];//手机
	$data['truename']=$data['name'];//姓名
	$data['address']=$data['dz'];;//地址
	$data['time']=time();//下单时间
	//$data['paid']=0;//0未付款1已付款
	unset($data['id']);

	$b=M("product_cart_new")->add($data);//订单表添加
	if($b){
		echo "<script>alert('订单生成成功');</script>";
	}else{
		echo "<script>alert('订单生成失败');</script>";
	}
	$this->assign('price',$price);
	$this->assign('orderid',$data['orderid']);
    $this->display();
}





	/**
	 * @param string $pid
	 * @return mixed生成二维码图片
	 */
	public function getCode() {

		$userinfo=M("Wxusers")->field("id")->where(array("openid"=>$this->openid))->find();

		$parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 200'.$userinfo['id'].'}}}';

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
	
	



}
?>
