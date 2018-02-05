<?php
ini_set('date.timezone','Asia/Shanghai');
require_once('mysqldb.php');
$user_id = $_POST['user_id'];
$merchant_id = $_POST['merchant_id'];
$card_config_id = $_POST['card_config_id'];
$quantity = $_POST['quantity'];
$device_code = $_POST['device_code'];


$recs  = $db->get_var("SELECT count(1) FROM card_doubi where type='1' and card_config_id='$card_config_id' and user_id ='$user_id'");
if($recs > 0){
	echo "-1";
	exit;
}
$doubi_info = array(
	'id' => guid(),
	'user_id' => $user_id,
	'merchant_id' => $merchant_id,		
	'card_config_id' => $card_config_id,
	'quantity' => $quantity,
	'coin_type' => '2',
	'coin_status' => '0',
	'user_type' => '1',
	'device_code'=>$device_code,
	'create_by'=>$user_id,
	'update_by'=>$user_id,
	'create_date' => date('Y-m-d H:i:s', time()),
	'update_date' => date('Y-m-d H:i:s', time())	
);


$result = $db->query("INSERT INTO card_doubi SET ". $db->get_set($doubi_info));
if($result == '1'){
	$db->query("UPDATE card_config set surplus_quantity = surplus_quantity-1 where id='$card_config_id'");	
}
echo $result;

?>