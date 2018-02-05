<?php
    require_once "mysqldb.php";
    $action = trim($_GET['action']);
    if($action == 'group'){
        $group = $db->get_results("select id,group_name from device_group_info where status=1 order by ords");
        $group = object_array($group);
        if($group){
            echo json_encode(array('msg'=>1,'data'=>$group));
        }else{
            echo json_encode(array('msg'=>2));
        }
    }
    //录入商家基本信息
    if($action == 'mer_insert'){
        $appid = trim($_POST['appid']);
        $openid = trim($_POST['openid']);
        $is = $db->get_row("select * from card_merchant where openid='$openid' and status=1 and del_flag=0");
        $is = object_array($is);
        $phone = $db->get_row("select * from card_merchant where phone='$_POST[phone]' and del_flag=0");
        $phone=object_array($phone);
        if($phone){
            echo json_encode(array('msg'=>4));
            exit;
        }
        if($is){
            echo json_encode(array('msg' => 3));
        }else {
            $data['id'] = guid();
            $data['openid'] = trim($_POST['openid']);
            $data['app_id'] = trim($_POST['appid']);
            $data['name'] = trim($_POST['name']);
            $data['lat_lon'] = trim($_POST['lat_lon']);
            $data['address'] = trim($_POST['address']);
            $data['phone'] = trim($_POST['phone']);
            $data['remarks'] = trim($_POST['remarks']);
            $data['create_date'] = date('Y-m-d H:i:s', time());
            $data['update_date'] = date('Y-m-d H:i:s', time());
            $data['default_group_id'] = trim($_POST['device_group_id']);
            $data['user_openid'] = trim($_POST['user_openid']);
            $id = $db->query("INSERT INTO card_merchant SET ". $db->get_set($data));
            if ($id) {
                $cid = $db->get_row("select * from card_merchant where openid='$openid' and status=1 and del_flag=0");
                $cid = object_array($cid);
                $regp['create_by'] = $cid['id'];
                $where = "openid='$openid'";
                //$c = $mon->edit('card_merchant',$regp,$where);
                $c = $db->query("update card_merchant set create_by='$cid[id]' where openid='$openid'");
                //录入默认商家的配置信息
                $datas['id'] = md5(uniqid());
                $datas['merchant_id'] =  $cid['id'];
                $datas['create_date'] =  date('Y-m-d H:i:s', time());
                $datas['brand_name'] = 'KFC';
                $datas['quantity'] = 10;
                $datas['title'] = '肯德基';
                $datas['per_num'] = 2;
                $datas['recommend_ord'] = rand(10,20);//推荐排序
                $datas['sub_title'] = '汉堡包';
                $datas['begin_timestamp'] = date('Y-m-d H:i:s', time());
                $datas['end_timestamp'] =date('Y-m-d H:i:s', time());
                $datas['create_by'] = $cid['id'];
                $datas['create_date'] = date('Y-m-d H:i:s', time());
                $datas['update_date'] = date('Y-m-d H:i:s', time());
                $datas['surplus_quantity'] = 10;
                $datas['type'] = 2;
                $datas['description'] = $cid['name'].'商家默认添加配置信息';
                $id = $db->query("INSERT INTO card_config SET ". $db->get_set($datas));
                if($c){
                    echo json_encode(array('msg' => 1));
                }
            } else {
                echo json_encode(array('msg' => 2));
            }
        }
    }
    //修改商家基本信息
    if($action == 'mer_update'){
            $uopenid = trim($_POST['openid']);
            $cid = $db->get_row("select * from card_merchant where openid='$uopenid' and status=1 and del_flag=0");
        $cid = object_array($cid);
            $data['app_id'] = trim($_POST['appid']);
            $data['name'] = trim($_POST['name']);
            $data['address'] = trim($_POST['address']);
            $data['phone'] = trim($_POST['phone']);
            //$data['total_doubi'] = trim($_POST['total']);
            $data['remarks'] = trim($_POST['remarks']);
            $data['update_by'] = $cid['id'];
            $data['update_date'] = date('Y-m-d H:i:s', time());
            $time = date('Y-m-d H:i:s', time());
            $data['default_group_id'] = trim($_POST['device_group_id']);
            $where = "openid='$uopenid'";
            $id = $db->query("update card_merchant set app_id='$_POST[appid]',name='$_POST[name]',address='$_POST[address]',phone='$_POST[phone]',remarks='$_POST[remarks]',update_by='$cid[id]',update_date='$time',default_group_id='$_POST[device_group_id]' where openid='$uopenid'");
            if ($id) {
                echo json_encode(array('msg' => 1));
            } else {
                echo json_encode(array('msg' => 2));
            }
    }
    //配置卡券表
    if($action == 'config'){
        $title = trim($_POST['title']);
        $begin = strtotime(trim($_POST['begin_timestamp']));
        $end_time = strtotime(trim($_POST['end_date']));
        $begin_timestamp = date('Y-m-d H:i:s',$begin);
        $end_date = date('Y-m-d H:i:s',$end_time);
        $open_id = trim($_POST['openid']);
        $merchant_id = $db->get_row("select * from card_merchant where openid='$open_id' and status=1 and del_flag=0");
        $merchant_id = object_array($merchant_id);
        $is = $db->get_row("select * from card_config where title='$title' and card_status=1 and del_flag=0");
        if($is){
            echo json_encode(array('msg' => 3));
        }else {
            $data['id'] = md5(uniqid());
            $data['merchant_id'] =  $merchant_id['id'];
            $data['create_date'] =  date('Y-m-d H:i:s', time());
            $data['brand_name'] = trim($_POST['brand_name']);
            $data['quantity'] = trim($_POST['quantity']);
            $data['title'] = trim($_POST['title']);
            $data['per_num'] = trim($_POST['per_num']);
            $data['recommend_ord'] = rand(10,20);//推荐排序
            $data['sub_title'] = trim($_POST['sub_title']);
            $data['begin_timestamp'] = $begin_timestamp;
            $data['end_timestamp'] = $end_date;
            $data['create_by'] = $merchant_id['id'];
            $data['create_date'] = date('Y-m-d H:i:s', time());
            $data['update_date'] = date('Y-m-d H:i:s', time());
            $data['surplus_quantity'] = trim($_POST['quantity']);
            $data['description'] = trim($_POST['description']);

            $id = $db->query("INSERT INTO card_config SET ". $db->get_set($data));
            if ($id) {
                echo json_encode(array('msg' => 1));
            } else {
                echo json_encode(array('msg' => 2));
            }
        }
    }
    //优惠配置加载表
    if($action == 'ajax'){
        if($_POST['page']){
            $page = $_POST['page'];
        }else{
            $page = 0;
        }
        $openid = trim($_POST['openid']);
        $PageSize = 10;
        $current = ($page-1)*$PageSize;
        $one = $db->get_row("select * from card_merchant where openid='$openid' and status=1");
        $one = object_array($one);
        if($one){
            $list = $db->get_results("select * from card_config where merchant_id='$one[id]' and card_status=1
                  and TO_DAYS(NOW()) BETWEEN TO_DAYS(begin_timestamp) and  TO_DAYS(end_timestamp)
                  and del_flag='0' and  surplus_quantity > 0 and type=1 order by create_date limit $current,$PageSize");
            $list = object_array($list);
            if($list){
                echo json_encode($list);
            }else{
                echo json_encode(array('msg'=>2));
            }
        }
    }
    //充值加载表
    if($action == 'money'){
        if($_POST['page']){
            $page = $_POST['page'];
        }else{
            $page = 0;
        }
        $openid = trim($_POST['openid']);
        $PageSize = 10;
        $current = ($page-1)*$PageSize;
        $merchant_id = $db->get_row("select * from card_merchant where openid='$openid' and status=1 and del_flag=0");
        $merchant_id = object_array($merchant_id);
        $list = $db->get_results("select * from card_doubi where coin_type = 2 and user_type = 2 and coin_status = 1 and del_flag = 0
            and merchant_id='$merchant_id[id]' order by create_date desc limit  $current,$PageSize");
        $list = object_array($list);
        if($list){
            echo json_encode($list);
        }else{
            echo json_encode(array('msg'=>2));
        }
    }
    //总余额
    if($action == 'all_pay'){
        $open_id = trim($_POST['openid']);
        $merchant_id = $db->get_row("select * from card_merchant where openid='$open_id' and status=1
          and del_flag=0");
        $merchant_id = object_array($merchant_id);
        $all = $merchant_id['total_doubi'];
        if($all){
            echo json_encode(array('msg' => 1,'datas'=>$all));
        } else {
            echo json_encode(array('msg' => 2));
        }
    }
    if($action == 'pay'){
        $open_id = trim($_POST['openid']);
        $merchant_id = $db->get_row("select * from card_merchant where openid='$open_id' and status=1 and del_flag=0");
        $merchant_id = object_array($merchant_id);
        $data['id'] = guid();
        $data['merchant_id'] =  $merchant_id['id'];
        $data['create_date'] =  date('Y-m-d H:i:s', time());
        $data['create_by'] =  $merchant_id['id'];
        $data['quantity'] =  trim($_POST['money']);
        $data['coin_type'] =  2;
        $data['coin_status'] =  1;
        $data['user_type'] =  2;
        $id = $db->query("INSERT INTO card_doubi SET ". $db->get_set($data));
        //商家
        $reg = $merchant_id['total_doubi']+trim($_POST['money']);
        $uid = $db->query("update card_merchant set total_doubi=$reg where openid='$open_id'");
        if ($id && $uid) {
            $totals = $merchant_id['total_doubi'];
            $total = $db->get_row("select sum(quantity) as t from card_doubi where user_type = 2 and coin_status = 1 and del_flag = 0 and coin_type= 1 and merchant_id='$merchant_id[id]'");
            $total = object_array($total);
            $all = $totals+$total['t']+trim($_POST['money']);
            echo json_encode(array('msg' => 1,'datas'=>$all));
        } else {
            echo json_encode(array('msg' => 2));
        }
    }
    //核销
//核销
if($action == 'cancel'){
    $merchant_id = trim($_POST['merchant_id']);
    $user_id = trim($_POST['user_id']);
    $money = trim($_POST['money']);
    $doubi_type = trim($_POST['doubi_type']);
    $openid = $db->get_row("select * from card_merchant where id='$merchant_id' and status=1 and del_flag=0");
    $openid = object_array($openid);
    //查询商家的余额
    $totals = $openid['total_doubi'];
    if($totals <= 0){
        echo json_encode(array('msg'=>4));
        exit();
    }
    //商家不能领取
    /*if($openid['openid'] == $user_id){
        echo json_encode(array('msg'=>5));
        exit();
    }*/
    //有凭证的用户
    $config_sql = "select id from card_config where merchant_id='$merchant_id' and del_flag=0 and type=2";
    $config_id = $db->get_row($config_sql);
    $config_id =object_array($config_id);
    /*$user_type = $db->get_row("select type from card_doubi where  user_type = 1
                 and del_flag = 0 and merchant_id='$merchant_id' and user_id='$user_id' GROUP BY type order by RAND()");
    //判断有凭证的用户
    $user_type = object_array($user_type);*/
    if($doubi_type == 1){
        $data['coin_status'] =  1;
        $times =  date('Y-m-d H:i:s',time());
        $is_ch = $db->get_row("select card_config_id,quantity,user_id,merchant_id from card_doubi where user_id='$user_id' and merchant_id='$merchant_id' and del_flag=0 and type=1 and coin_status=0");
        $is_ch = object_array($is_ch);
        $where = "user_id='$user_id' and merchant_id='$merchant_id' and del_flag=0 and type=1 and card_config_id='$is_ch[card_config_id]'";
        $is = $db->get_row("select id from card_doubi where user_id='$user_id' and merchant_id='$merchant_id' and user_type=1 and card_config_id='$is_ch[card_config_id]' and del_flag=0 and type=1 and coin_status=0");
        $is = object_array($is);
        if(empty($is)){
            echo json_encode(array('msg'=>'3'));//已经全部核销了
        }else{
            $mer['id'] = guid();
            $mer['merchant_id'] =  $is_ch['merchant_id'];
            $mer['card_config_id'] =  $is_ch['card_config_id'];
            $mer['user_id'] =  $openid['openid'];
            $mer['create_date'] =  date('Y-m-d H:i:s', time());
            $mer['create_by'] =  $openid['id'];
            $mer['update_date'] =  date('Y-m-d H:i:s', time());
            $mer['quantity'] =  '-'.$is_ch['quantity'];
            $mer['coin_type'] =  1;
            $mer['coin_status'] =  1;
            $mer['user_type'] =  2;
            mysql_query("BEGIN");
            $user = $db->query("update card_doubi set coin_status=1,update_date='$times' where
              user_id='$user_id' and merchant_id='$merchant_id' and del_flag=0 and type=1 and
              card_config_id='$is_ch[card_config_id]'");

            //商家表减去消费的金额
            $mertoal = $totals-$is_ch['quantity'];
            $updatemer = $db->query("update card_merchant set total_doubi='$mertoal' where id='$merchant_id'");


            $mers = $db->query("INSERT INTO card_doubi SET ". $db->get_set($mer));
            if($mers && $user){
                mysql_query("COMMIT");
                echo json_encode(array('msg'=>1));
            }else{
                mysql_query("ROLLBACK");
                echo json_encode(array('msg'=>2));
            }
        }
    }else if($doubi_type == 3){//交定金的用户
        $times =  date('Y-m-d H:i:s',time());
        $is_ch = $db->get_row("select * from card_config where user_id='$user_id' and merchant_id='$merchant_id' and del_flag=0 and card_status=0");
        $is_ch = object_array($is_ch);
        if(empty($is_ch)){
            echo json_encode(array('msg'=>'3'));//已经全部核销了
        }else{
            $user = $db->query("update card_config set card_status=1,update_date='$times' where user_id='$user_id' and merchant_id='$merchant_id' and del_flag=0 and id='$is_ch[card_config_id]'");
            if($user){
                mysql_query("COMMIT");
                echo json_encode(array('msg'=>1));
            }else{
                mysql_query("ROLLBACK");
                echo json_encode(array('msg'=>2));
            }
        }
    }else if($doubi_type== 2){
        //可以领取多个的逗币
        //查出商家默认的配置信息
        $config2 = $db->get_row("select id,surplus_quantity,per_num from card_config where merchant_id='$merchant_id' and  card_status=1 and del_flag=0 and type=2");
        $config2 = object_array($config2);
        //添加用户记录
        $mer['id'] = guid();
        $mer['merchant_id'] = $merchant_id;
        $mer['card_config_id'] = $config2['id'];
        $mer['user_id'] =  $user_id;
        $mer['create_date'] =  date('Y-m-d H:i:s', time());
        $mer['create_by'] =  $user_id;
        $mer['update_date'] =  date('Y-m-d H:i:s', time());
        $mer['quantity'] =  $money;
        $mer['coin_type'] =  2;
        $mer['coin_status'] =  1;
        $mer['user_type'] =  1;
        $mer['type'] =  2;
        mysql_query("BEGIN");
        $usersno = $db->query("INSERT INTO card_doubi SET ". $db->get_set($mer));
        //添加商家记录
        $datas['id'] = guid();
        $datas['merchant_id'] = $merchant_id;
        $datas['card_config_id'] = $config2['id'];
        $datas['user_id'] =  $user_id;
        $datas['create_date'] =  date('Y-m-d H:i:s', time());
        $datas['create_by'] =  $merchant_id;
        $datas['update_date'] =  date('Y-m-d H:i:s', time());
        $datas['quantity'] =  '-'.$money;
        $datas['coin_type'] =  1;
        $datas['coin_status'] =  1;
        $datas['user_type'] =  2;
        $datas['type'] =  2;
        $merno = $db->query("INSERT INTO card_doubi SET ". $db->get_set($datas));

        //商家表减去消费的金额
        $mertoal = $totals-$money;
        $updatemer = $db->query("update card_merchant set total_doubi='$mertoal' where id='$merchant_id'");
        if($merno && $usersno){
            mysql_query("COMMIT");
            echo json_encode(array('msg'=>1));
        }else{
            mysql_query("ROLLBACK");
            echo json_encode(array('msg'=>2));
        }
    }else if($doubi_type == 4){
        $guid = trim($_POST['guid']);
        //远程送逗币，只能领取一次
        //查出商家默认的配置信息
        $config2 = $db->get_row("select id,surplus_quantity,per_num from card_config where merchant_id='$merchant_id' and  card_status=1 and del_flag=0 and type=2");
        $config2 = object_array($config2);
        //判断用户是否已经领取逗币
        $if_usered = $db->get_row("select * from card_doubi where card_config_id='$config2[id]' and type=2 and coin_status=1 and user_type=1 and remarks='$guid'");
        $is_usered = object_array($if_usered);
        if($is_usered){
            echo json_encode(array('msg'=>5));
            exit();
        }
        //添加用户记录
        $mer['id'] = guid();
        $mer['merchant_id'] = $merchant_id;
        $mer['card_config_id'] = $config2['id'];
        $mer['user_id'] =  $user_id;
        $mer['create_date'] =  date('Y-m-d H:i:s', time());
        $mer['create_by'] =  $user_id;
        $mer['update_date'] =  date('Y-m-d H:i:s', time());
        $mer['quantity'] =  $money;
        $mer['coin_type'] =  2;
        $mer['coin_status'] =  1;
        $mer['user_type'] =  1;
        $mer['type'] =  2;
        $mer['remarks'] = $guid;
        mysql_query("BEGIN");
        $usersno = $db->query("INSERT INTO card_doubi SET ". $db->get_set($mer));
        //添加商家记录
        $datas['id'] = guid();
        $datas['merchant_id'] = $merchant_id;
        $datas['card_config_id'] = $config2['id'];
        $datas['user_id'] =  $user_id;
        $datas['create_date'] =  date('Y-m-d H:i:s', time());
        $datas['create_by'] =  $merchant_id;
        $datas['update_date'] =  date('Y-m-d H:i:s', time());
        $datas['quantity'] =  '-'.$money;
        $datas['coin_type'] =  1;
        $datas['coin_status'] =  1;
        $datas['user_type'] =  2;
        $datas['type'] =  2;
        $merno = $db->query("INSERT INTO card_doubi SET ". $db->get_set($datas));

        //商家表减去消费的金额
        $mertoal = $totals-$money;
        $updatemer = $db->query("update card_merchant set total_doubi='$mertoal' where id='$merchant_id'");
        if($merno && $usersno){
            mysql_query("COMMIT");
            echo json_encode(array('msg'=>1));
        }else{
            mysql_query("ROLLBACK");
            echo json_encode(array('msg'=>2));
        }
    }
}
    /*------------------------------------------------------------------只对用户列表------------------------------------------------------------------*/
    //用户优惠信息
    if($action == 'user_ajax'){
        if($_POST['page']){
            $page = $_POST['page'];
        }else{
            $page = 0;
        }
        $openid = trim($_POST['openid']);
        $PageSize = 10;
        $current = ($page-1)*$PageSize;
        $list = $db->get_results("select * from card_doubi where user_id='$openid' and del_flag=0 and user_type=1  order by create_date desc limit $current,$PageSize");
        $list = object_array($list);
        if($list){
            echo json_encode($list);
        }else{
            echo json_encode(array('msg'=>2));
        }
    }
//客服
if($action == 'kefu'){
    if($_POST['page']){
        $page = $_POST['page'];
    }else{
        $page = 0;
    }
    $openid = trim($_POST['openid']);
    $PageSize = 10;
    $current = ($page-1)*$PageSize;
    $list = $db->get_results("select  cpr.* ,cp.service_number from card_product_receive cpr,card_product cp where cpr.user_openid='$openid' and cpr.del_flag=0 and cpr.merchant_product_id=cp.id limit $current,$PageSize");
    $list = object_array($list);
    if($list){
        echo json_encode($list);
    }else{
        echo json_encode(array('msg'=>2));
    }
}
    //修改类似卡券表
    if($action == 'likes'){
        $title = trim($_POST['title']);
        $begin = strtotime(trim($_POST['begin_timestamp']));
        $end_time = strtotime(trim($_POST['end_date']));
        $begin_timestamp = date('Y-m-d H:i:s',$begin);
        $end_date = date('Y-m-d H:i:s',$end_time);
        $open_id = trim($_POST['openid']);
        $merchant_id = $db->get_row("select * from card_merchant where openid='$open_id' and status=1 and del_flag=0");
        $merchant_id = object_array($merchant_id);
        $is = $db->get_row("select * from card_config where title='$title' and card_status=1 and del_flag=0");
        $is = object_array($is);
        if($is){
            echo json_encode(array('msg' => 3));
        }else {
            $data['id'] = md5(uniqid());
            $data['merchant_id'] =  $merchant_id['id'];
            $data['create_date'] =  date('Y-m-d H:i:s', time());
            $data['brand_name'] = trim($_POST['brand_name']);
            $data['quantity'] = trim($_POST['quantity']);
            $data['title'] = trim($_POST['title']);
            $data['per_num'] = trim($_POST['per_num']);
            $data['recommend_ord'] = rand(10,20);//推荐排序
            $data['sub_title'] = trim($_POST['sub_title']);
            $data['begin_timestamp'] = $begin_timestamp;
            $data['end_timestamp'] = $end_date;
            $data['create_by'] = $merchant_id['id'];
            $data['create_date'] = date('Y-m-d H:i:s', time());
            $data['update_date'] = date('Y-m-d H:i:s', time());
            $data['surplus_quantity'] = trim($_POST['quantity']);
            $data['description'] = trim($_POST['description']);
            $id = $db->query("INSERT INTO card_config SET ". $db->get_set($data));
            if ($id) {
                echo json_encode(array('msg' => 1));
            } else {
                echo json_encode(array('msg' => 2));
            }
        }
    }
?>