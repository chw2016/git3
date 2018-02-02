<?php
 class WuxianPrint {
	function __construct($key,$domain){
		$this->key = $key;
		$this->domain = "http://".$domain;
		define('SITE','/service/sendPrintData.php?');
	}

	public function  print_shop_order($orderdata,$strDetail,$name){
		$paytype = '微信支付';
		if($orderdata['paytype'] == 5){
			$paytype = '余额支付';
		}
		$paywm = "(外卖)";
		if($orderdata['wm'] == 1){
			$paywm = "(堂吃)";
		}else if($orderdata['wm'] == 2){
			$paywm = "(打包)";
		}

        $noget_money = 0;
        $score_money = 0;
        $score = 0;
        $xianjin_b = 0;
        if($orderdata['noget_money'] != 0){
        	$noget_money = '跑腿费:+'.$orderdata['noget_money']."元\n";
        }
	if($orderdata['score_money'] != 0){
        	$score_money = $orderdata['score_money'];
        }
        if($orderdata['score'] != 0){
        	$score_money = $orderdata['score'];
        }
        if($orderdata['xianjin_b'] != 0){
        	$score_money = $orderdata['xianjin_b'];
        }
        $totalmoney = round(($orderdata['totalmoney']+$orderdata['noget_money']-$orderdata['score_money']),2);


		$content = $name."商品订单\n订单号 ：".$orderdata['ordernumber']."\n下单时间：".$orderdata['buytime']."\n支付方式：".$paytype."\n打单时间：".date("Y-m-d H:i:s")."\n收货人：".$orderdata['buyname'].$paywm."\n联系电话：".$orderdata['tel']."\n收货地址：".$orderdata['address']."\n备注信息：".$orderdata['instruct']."\n".$strDetail.$noget_money."\n订单总额 ：".$orderdata['totalmoney']."元"."\n实际支付 ：".$totalmoney."元\n抵扣得意币:".$score_money."\n赠送德意币:".$score.$xianjin_b;
		file_put_contents('1.txt',$content);
		$array['type'] = "text";  // qrcode   
		$array['content'] = $content;  // content 编码 utf8 
		$now = time();
		$array['createtime'] = $now;
		$si= $this->EncyptUrl($array , $this->key);

		$array['content'] = urlencode($array['content']);

		$url = $this->domain.SITE."content={$array['content']}&type=text&key=".$si."&createtime=".$now;
		$res = file_get_contents($url);
		return json_decode($res,true);
		
	}

	public function EncyptUrl($data , $key) {
         ksort($data, SORT_STRING);
         $__av = array_values($data);
         $src_string = implode(",", $__av) . $key;
         $md5 = md5($src_string);
         return $md5;
	}

}

