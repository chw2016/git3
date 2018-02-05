<?php
	ini_set('date.timezone','Asia/Shanghai');
	require_once('mysqldb.php');
	$open_id = $_POST['open_id'];
	$device_code = $_POST['device_code'];
	$device_id = $_POST['device_id'];
	$quantity = $_POST['price'];
	$doubi_info = array(
		'id' => guid(),
		'user_id' => $open_id,
		'merchant_id' => '',
		'card_config_id' => '',
		'quantity' => '-'.$quantity,
		'coin_type' => '1',
		'coin_status' => '1',
		'user_type' => '1',
		'device_id'=>$device_id,
		'device_code'=>$device_code,
		'create_by'=>$open_id,
		'update_by'=>$open_id,
		'create_date' => date('Y-m-d H:i:s', time()),
		'update_date' => date('Y-m-d H:i:s', time())
	);
	$result = $db->query("INSERT INTO card_doubi SET ". $db->get_set($doubi_info));
	echo $result;
?>