<?php
class AlipayCAction extends BaseAction{
    public $token;
	public $openid;
	public $alipayConfig;
	public function __construct(){
		$this->token = $this->_get('token');
		$this->	openid= $this->_get('openid');
		if (!$this->token){
			$Model=M('Mainorder');
			$orderid = $this->_get('orderid',"intval");
			$order=$Model->where(array('id'=>$orderid))->find();
//			if (!$order){
//				$order=$product_cart_model->where(array('id'=>intval($this->_get('out_trade_no'))))->find();
//			}
			$this->token=$order['token'];
		}
		//读取配置
		$alipay_config_db=M('Alipay_config_new');
		$this->alipayConfig=$alipay_config_db->where(array('token'=>$this->token))->find();
	}
	public function pay(){
		//参数数据
		$price=$this->_get("price");
		$orderName=$_GET['orderName'];
		$orderid=$this->_get("orderid","intval");
        $alipayConfig=$this->alipayConfig;
        if($alipayConfig['paytype']!="alipay"){
            exit("不支持该付款方式");
        }
		if (!$orderid){
			$orderid=$this->_get("single_orderid","intval");//单个订单
		}
		$from=isset($_GET['from'])?$_GET['from']:'O2O';
		if(!$price)exit('必须有价格才能支付');
		import("@.ORG.Alipay.AlipaySubmit");
        if($alipayConfig['paytype']=="alipay"){
            $alipayConfig['paytype']="Alipaytype";
            header('Location:?g=Wap&m='.$alipayConfig['paytype'].'&a=pay&price='.$price.'&orderName='.$orderName.'&single_orderid='.$orderid.'&from='.$from.'&token='.$this->token);
        }
	}
}
?>