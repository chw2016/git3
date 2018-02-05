<?php
	session_start();
	ini_set('date.timezone','Asia/Shanghai');
	require_once "mysqldb.php";
	$action = $_GET['action'];
	if($action=='address'){
		if(empty($_SESSION['shop_openid'])){
			exit();
		}
		$data['id'] = guid();
		$data['merchant_product_id'] = $_POST['proid'];
		$data['user_openid'] = $_SESSION['shop_openid'];
		$data['address'] = $_POST['address'];
		$data['province'] = $_POST['province'];
		$data['city'] = $_POST['city'];
		$data['district'] = $_POST['district_str'];
		$data['phoneno'] = $_POST['mobile'];
		$data['name'] = $_POST['name'];
		$data['create_by'] = guid();
		$data['id'] = guid();
		$data['create_date'] = date('Y-m-d H:i:s',time());
		//$id = $mon->add('card_product_receive',$data);
		$id = $db->query("INSERT INTO card_product_receive SET ".$db->get_set($data));

		$purchase_people = $db->get_row("select purchase_people from card_product where id='$_POST[proid]'");
		$datas = $purchase_people->purchase_people+9;
		$where = "id='$_POST[proid]'";
		$dd = $db->query("update card_product set purchase_people=$datas where id='$_POST[proid]'");
		if($id){
			echo 1;
		}else{
			echo 2;
		}
	}


?>