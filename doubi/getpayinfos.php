<?php
ini_set('date.timezone','Asia/Shanghai');
require_once('mysqldbread.php');
//返回统计值
$openid = $_POST['open_id'];
$sql = "select sum(quantity) quantity from card_doubi where user_id='$openid' and user_type=1 and del_flag=0 and coin_status=1";
$min_result = $db->get_var($sql);
if($min_result){
	echo $min_result;
}else{
	echo 0;
}
?>

