<?php
ini_set('date.timezone','Asia/Shanghai');
require_once('mysqldbread.php');
$device_code = $_POST["device_code"];

//判断不在线
$query_online_sql  = "select * from device_group dg where device_command='".$device_code."' and online_status='0' ";
$query_result1 = $db->query($query_online_sql);
if($query_result1){
	if(1 == count($query_result1)){
		echo "1";
		exit;
	}	
}

echo guid();
?>

