<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "./lib/WxPay.Api.php";
require_once './lib/WxPay.Notify.php';
require_once 'log.php';
require_once 'mysqldb.php';

//初始化日志
$logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	private $db;

	public function setDb($db){
		$this->db = $db;
	}
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		//更新充值pay_info记录，商户订单号
		if(array_key_exists("out_trade_no", $data)){
		    $result = $this->db->query("update card_doubi set coin_status=1,transaction_id='$data[transaction_id]' where out_trade_no='$data[out_trade_no]' and user_id='$data[openid]'");

			//更新数据库
			return true;
		}
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->setDb($db);
$notify->Handle(false);
