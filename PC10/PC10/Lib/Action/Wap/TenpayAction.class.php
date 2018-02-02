<?php
class TenpayAction extends BaseAction{
	public $token;
	public $wecha_id;
	public $tenpayConfig;
	public function __construct(){
		$this->token = $this->_get('token');
		$this->wecha_id	= $this->_get('wecha_id');
		if (!$this->token){
			//
			$product_cart_model=M('product_cart');
			$out_trade_no = $this->_get('out_trade_no');
			$order=$product_cart_model->where(array('orderid'=>$out_trade_no))->find();
			if (!$order){
				$order=$product_cart_model->where(array('id'=>intval($this->_get('out_trade_no'))))->find();
			}
			$this->token=$order['token'];
		}
		//读取支付宝配置
		$tenpay_config_db=M('alipay_config');
		$this->tenpayConfig=$tenpay_config_db->where(array('token'=>$this->token))->find();
	}
	public function pay(){
	$price=$_GET['price'];
		//
		$tenpayConfig=$this->tenpayConfig;
		//
		if(!$price)exit('必须有价格才能支付');
import("@.ORG.Tenpay.RequestHandler");
		

/* 获取提交的订单号 */
$out_trade_no = $_REQUEST["orderid"];
/* 获取提交的商品名称 */
$product_name = $_REQUEST["orderid"];
/* 获取提交的商品价格 */
$order_price = $_REQUEST["price"];
/* 获取提交的备注信息 */

/* 支付方式 */
if ($this->tenpayConfig['open']==2)
$trade_mode=1;

if ($this->tenpayConfig['open']==3)
$trade_mode=2;

$strDate = date("Ymd");
$strTime = date("His");

/* 商品价格（包含运费），以分为单位 */
$total_fee = $order_price*100;

/* 商品名称 */
$desc = "商品：".$product_name.",价格:".$order_price;

/* 创建支付请求对象 */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($this->tenpayConfig['tenpaykey']);
$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");

//----------------------------------------
//设置支付参数 
//----------------------------------------
$reqHandler->setParameter("partner", $this->tenpayConfig['tenpaypartner']);
$reqHandler->setParameter("out_trade_no", $out_trade_no);
$reqHandler->setParameter("total_fee", $total_fee);  //总金额
$reqHandler->setParameter("return_url", C('site_url').'/index.php?g=Wap&m=Tenpay&a=return_url');
$reqHandler->setParameter("notify_url", C('site_url').'/Lib/Action/Wap/payNotifyUrl.php');
$reqHandler->setParameter("body", $desc);
$reqHandler->setParameter("bank_type", "0");  	  //银行类型，默认为财付通
//用户ip
$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
$reqHandler->setParameter("fee_type", "1");               //币种
$reqHandler->setParameter("subject",$product_name);          //商品名称，（中介交易时必填）

//系统可选参数
$reqHandler->setParameter("sign_type", "MD5");  	 	  //签名方式，默认为MD5，可选RSA
$reqHandler->setParameter("service_version", "1.0"); 	  //接口版本号
$reqHandler->setParameter("input_charset", "GBK");   	  //字符集
$reqHandler->setParameter("sign_key_index", "1");    	  //密钥序号

//业务可选参数
$reqHandler->setParameter("attach", "");             	  //附件数据，原样返回就可以了
$reqHandler->setParameter("product_fee", "");        	  //商品费用
$reqHandler->setParameter("transport_fee", "0");      	  //物流费用
$reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间
$reqHandler->setParameter("time_expire", "");             //订单失效时间
$reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
$reqHandler->setParameter("goods_tag", "");               //商品标记
$reqHandler->setParameter("trade_mode",$trade_mode);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
$reqHandler->setParameter("transport_desc","");              //物流说明
$reqHandler->setParameter("trans_type","1");              //交易类型
$reqHandler->setParameter("agentid","");                  //平台ID
$reqHandler->setParameter("agent_type","");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
$reqHandler->setParameter("seller_id","");                //卖家的商户号



//请求的URL
$reqUrl = $reqHandler->getRequestURL();

//获取debug信息,建议把请求和debug信息写入日志，方便定位问题
/**/
$debugInfo = $reqHandler->getDebugInfo();
echo '<script>location.href="'.$reqUrl.'";</script>';   
echo "<br/>" . '<a href="'.$reqUrl.'" target="_blank">确认支付</a>';


	}
		
				
		
	
	
	//同步数据处理
	public function return_url (){

import("@.ORG.Tenpay.RequestHandler");
import("@.ORG.Tenpay.ResponseHandler");
import("@.ORG.Tenpay.function");



$resHandler = new ResponseHandler();
$resHandler->setKey($this->tenpayConfig['tenpaykey']);

//判断签名
if($resHandler->isTenpaySign()) {
	
	//通知id
	$notify_id = $resHandler->getParameter("notify_id");
	//商户订单号
	$out_trade_no = $resHandler->getParameter("out_trade_no");
	//财付通订单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");
	//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
	$discount = $resHandler->getParameter("discount");
	//支付结果
	$trade_state = $resHandler->getParameter("trade_state");
	//交易模式,1即时到账
	$trade_mode = $resHandler->getParameter("trade_mode");
	
	
	if("1" == $trade_mode ) {
		if( "0" == $trade_state){ 
			$out_trade_no = $this->_get('out_trade_no');

			//$trade_no =  $this->_get('trade_no');
			
				$product_cart_model=M('product_cart');
				$order=$product_cart_model->where(array('orderid'=>$out_trade_no))->find();
				$sepOrder=0;
				if (!$order){
					$order=$product_cart_model->where(array('id'=>$out_trade_no))->find();
					$sepOrder=1;
				}
				if($order){
					if($order['paid']==1){exit('该订单已经支付,请勿重复操作');}
					if (!$sepOrder){
						$product_cart_model->where(array('orderid'=>$out_trade_no))->setField('paid',1);
					}else {
						$product_cart_model->where(array('id'=>$out_trade_no))->setField('paid',1);
					}
					
					//if($back!=false&&$add!=false){
						$this->redirect(U('Product/my',array('token'=>$order['token'],'wecha_id'=>$order['wecha_id'],'success'=>1)));
					//}else{
						//$this->error('充值失败,请在线客服,为您处理',U('User/Index/index'));
					//}
				}else{						
					exit('订单不存在：'.$out_trade_no);
				}
				
			
	
		} else {
			//当做不成功处理
			echo "<br/>" . "即时到帐支付失败" . "<br/>";
		}
	}elseif( "2" == $trade_mode  ) {
		if( "0" == $trade_state) {
		
		
		
		$out_trade_no = $this->_get('out_trade_no');
		
			//$trade_no =  $this->_get('trade_no');

			//$result = $_GET['result'];
			
				$product_cart_model=M('product_cart');
				$order=$product_cart_model->where(array('orderid'=>$out_trade_no))->find();
				$sepOrder=0;
				if (!$order){
					$order=$product_cart_model->where(array('id'=>$out_trade_no))->find();
					$sepOrder=1;
				}
				if($order){
					if($order['paid']==1){exit('该订单已经支付,请勿重复操作');}
					if (!$sepOrder){
						$product_cart_model->where(array('orderid'=>$out_trade_no))->setField('paid',1);
					}else {
						$product_cart_model->where(array('id'=>$out_trade_no))->setField('paid',1);
					}
					
					//if($back!=false&&$add!=false){
						$this->redirect(U('Product/my',array('token'=>$order['token'],'wecha_id'=>$order['wecha_id'],'success'=>1)));
					//}else{
						//$this->error('充值失败,请在线客服,为您处理',U('User/Index/index'));
					//}
				}else{						
					exit('订单不存在：'.$out_trade_no);
				}
		
		} else {
			//当做不成功处理
			echo "<br/>" . "中介担保支付失败" . "<br/>";
		}
	}
} else {
	echo "<br/>" . "认证签名失败" . "<br/>";
	//echo $resHandler->getDebugInfo() . "<br>";
}
	

	}


		
	public function notify_url(){


echo "success";
	
}	
	
}
?>