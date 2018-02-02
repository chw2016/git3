<?php
/*
 * Laundry Created by 訾超 in 2014-10-13
 */
class LaundryAction extends BaseAction {
    public function index() {
        $w ['position'] = array ('gt', 0 );
        $w ['token'] = $this->token;
        $goods = M ( 'Laundry_goods' )->order ( 'position asc' )->where ( $w )->select ();
        $this->assign ( 'goods', $goods );
        $columns = M ( 'Laundry_columns' )->where ( array ('token' => $this->token ) )->select ();
        $this->assign ( 'columns', $columns );
        $this->display ();
    }
    //商品展示页
    public function goods() {
        $w ['id'] = $_GET ['goods_id'];
        $goods = M ( 'Laundry_goods' )->where ( $w )->find ();
        $goods ['goods_brief'] = htmlspecialchars_decode ( $goods ['goods_brief'], ENT_QUOTES );
        $this->assign ( 'goods', $goods );
        if(M ( 'Laundry_online_franchisee' )->where ( array ('token' => $this->token, 'online_openid' => $_GET['openid'] ) )->find ()){
            $o = M ( 'Laundry_online_franchisee' )->where ( array ('token' => $this->token, 'online_openid' => $_GET['openid'] ) )->find ();
            $info = M('Laundry_customers')->order('id desc')->where(array('online_id'=>$o['id'],'token'=>$this->token))->find();
            $key = 0;
        }else{
            $info = M('Laundry_customers')->where(array('token'=>$this->token,'c_openid'=>$_GET['openid']))->find();
        }
        $this->assign('info',$info);
        $this->assign('key',$key);
        $this->display ();
    }
    //定位  经度，纬度
    //R*arccos(cos(lat1*pi()/180 )*cos(lat2*pi()/180)*cos(lng1*pi()/180 -lng2*pi()/180)+ sin(lat1*pi()/180 )*sin(lat2*pi()/180))
    public function latANDlong(){
        if(IS_POST){
            /*
             *  $lat1 = 22.573627416641;  //我的位置纬度
                $lat2 = 22.573977;        //百度地图给的我所在公司附近一个地点的纬度
                $lng1 = 113.88627711346;  //我的位置经度
                $lng2 = 113.885825;       //百度地图给的我所在公司附近一个地点的经度
                $R = 6370996.81;          //地球的半径，单位是米
                //下面这个公式是计算球面上两点弧长的公式
                $distance = $R*acos(cos($lat1*pi()/180 )*cos($lat2*pi()/180)*cos($lng1*pi()/180 -$lng2*pi()/180)+ sin($lat1*pi()/180 )*sin($lat2*pi()/180));
                echo $distance;  //测试结果：60.54米，根据经验判断这个数值大致是正确的
             *
             * */
            $lat1 = $_POST['lati'];   //113
            $lng1 = $_POST['long'];   //22
            $R = 6370996.81;
            $onlinList = M('Laundry_online_franchisee')->where(array('token'=>$this->token))->select();
            if($onlinList){
                foreach($onlinList as $k => $v){
                    $lat2 = $v['latitude'];
                    $lng2 = $v['longitude'];
                    $distance[$v['id']] = $R*acos(cos($lat1*pi()/180 )*cos($lat2*pi()/180)*cos($lng1*pi()/180 -$lng2*pi()/180)+ sin($lat1*pi()/180 )*sin($lat2*pi()/180));
                }
                //取出距离最近的前五个在线加盟商
                asort($distance);
                $distance = array_slice($distance,0,5,true);
                foreach($onlinList as $k => $v){
                    foreach($distance as $key => $value){
                        if($v['id'] == $key){
                            $value = explode(".",$value);
                            $onlinList[$k]['distance'] = $value[0] / 1000;
                        }
                    }
                }

                foreach($distance as $k => $v){
                    foreach($onlinList as $key => $value){
                        if($k == $value['id']){
                            $info[] = $onlinList[$key];
                        }
                    }
                }


                $this->ajaxReturn($info,'定位成功,点击选择',1);
            }else{
                exit(json_encode(array('info'=>'未查到服务商','status'=>0)));
            }
        }
    }
    //下订单
    public function order(){

        if(IS_POST){
            if(($_POST['o'] == undefined) || ($_POST['online'] == '正在定位，请稍后…' || $_POST['online'] == '定位成功,点击选择')){
                exit(json_encode(array('info'=>'服务商不能为空','status'=>-1)));
            }
            if($_POST['washing_date'] == ''){
                exit(json_encode(array('info'=>'服务日期不能为空','status'=>-1)));
            }
            if($_POST['washing_time'] == ''){
                exit(json_encode(array('info'=>'服务时段不能为空','status'=>-1)));
            }
            //print_r($_POST);exit();
            $data['goods_id'] = $_POST['goods_id'];
            $data['order_person_tel'] = $_POST['c_tel'];
            $customer = M('Laundry_customers')->where(array('c_tel'=>$data['order_person_tel']))->find();
            $data['order_person_name'] = $customer['c_name'];
            $data['order_person_zone'] = $customer['c_town'];
            if($customer['online_id'] == 0){
                $data['order_person_openid'] = $customer['c_openid'];
            }else{
                $franchisee = M('Laundry_online_franchisee')->where(array('id'=>$customer['online_id']))->find();
                $data['order_person_openid'] = $franchisee['online_openid'];
            }
            $data['online_id'] = $_POST['o'];
            $data['washing_date'] = $_POST['washing_date'];
            $data['washing_time'] = $_POST['washing_time'];
            if($_POST['paytype'] == 1){
                $data['order_payment_status'] = 1;
            }elseif($_POST['paytype'] == 2){
                $data['order_payment_status'] = 2;
            }elseif($_POST['paytype'] == 4){
                $data['order_payment_status'] = 3;
            }
            $data['special_requirements'] = $_POST['special_requirements'];
            $data['token'] = $this->token;
            $data['order_sn'] = date('YmdHis').mt_rand(0000,9999);
            $data['order_addtime'] = strtotime(date('Y-m-d H:i:s'));
            $goods = M('Laundry_goods')->where(array('id'=>$data['goods_id']))->find();
            $data['order_goods'] = $goods['goods_name'];
            if(($_POST['order_goods_num'] == null) || ($_POST['order_goods_num'] == '')){
                $data['order_goods_num'] = 1;
            }else{
                $data['order_goods_num'] = $_POST['order_goods_num'];
            }
            $data['order_price'] = $goods['goods_price'] * $data['order_goods_num'];
            $data['order_address'] = $customer['c_province'].$customer['c_city'].$customer['c_town'].$customer['c_address'];
            
            if($_POST['key'] == 0){
                $data['order_type'] = 1;
                $data['order_person_id'] = $_POST['online_id'];
            }elseif($_POST['key'] == ''){
                $data['order_type'] = 0;
                $data['c_id'] = $customer['id'];
            }
            if(M('Laundry_order')->add($data)){
                $this->ajaxReturn(array('info'=>'下单成功','status'=>1,'url'=>'index.php?g=Wap&m=Laundry&a=ordersuccess&token=' . $this->token . '&openid=' . $data ['order_person_openid'].'&order_sn='.$data['order_sn']));
            }else{
                $this->ajaxReturn(array('info'=>'下单失败','status'=>0,'url'=>'index.php?g=Wap&m=Laundry&a=goods&token=' . $this->token . '&openid=' . $data ['order_person_openid']));
            }
        }
        $this->display ();
    }
    public function ordersuccess() {
        $order = M('Laundry_order')->where(array('order_sn'=>$_GET['order_sn']))->find();
        $this->assign('order',$order);
        $this->display ();
    }
    public function orders(){
        $onlineInfo = M('Laundry_online_franchisee')->where(array('token'=>$this->token,'online_openid'=>$_GET['openid']))->find();
        if(!empty($onlineInfo)){
            $this->assign('key',1);
        }
        $w['order_person_openid'] = $_GET['openid'];
        $w['order_status'] = 1;
        $w['order_logistics_status'] = array('lt',4);
        $orders = M('Laundry_order')->order('id desc')->where($w)->select();
        if($orders){
            $key = 1;
            $this->assign('key',$key);
        }
        $this->assign('orders',$orders);
        $this->display();
    }

    public function doneorders(){
        $customer = M('Laundry_customers')->where(array('token'=>$this->token,'c_openid'=>$_REQUEST['openid']))->find();
        $online = M('Laundry_online_franchisee')->where(array('token'=>$this->token,'online_openid'=>$_REQUEST['openid']))->find();
        if($customer || $online){
            $w['token'] = $this->token;
            $w['order_person_openid'] = $_REQUEST['openid'];
            $w['order_logistics_status'] = array('egt',4);
            $orderLst = M('Laundry_order')->where($w)->select();
            if(!empty($orderLst)){
                $this->assign('orderList',$orderLst);
            }else{
                $this->assign('key',1);
            }

        }else{
            echo "非法操作";exit;
        }
        $this->display();
    }
   
    public function cancel(){
        if(IS_POST){
            if(M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$_POST['order_sn'],'order_status'=>$_POST['status']))->find()){
                $data['order_status'] = 0;
                if(M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$_POST['order_sn'],'order_status'=>$_POST['status']))->save($data)){
                    $this->ajaxReturn(array('info'=>'取消成功！','status'=>1,'url'=>'index.php?g=Wap&m=Laundry&a=cancel&token=' . $this->token .'&openid=' .$_GET['openid'] . '&status=1'));
                }else{
                    $this->ajaxReturn(array('info'=>'取消失败！','status'=>0,'url'=>'index.php?g=Wap&m=Laundry&a=cancel&token=' . $this->token .'&openid=' .$_GET['openid'] . '&status=0'));
                }
            }else{
                $this->ajaxReturn(array('info'=>'非法操作！','status'=>2,'url'=>'index.php?g=Wap&m=Laundry&a=cancel&token=' . $this->token .'&openid=' .$_GET['openid'] . '&status=2'));
            }
        }
        $this->display();
    }
    /*
     * Others
    */
    public function others(){
        $w['columns_id'] = $_GET['columns_id'];
        $goods = M('Laundry_goods')->where($w)->select();
        $this->assign('goods',$goods);
        $this->display();
    } 
    /*
     * Customer
     */
    public function customer() {
        $w ['openid'] = $_GET ['openid'];
        $w ['goods_id'] = $_GET ['goods_id'];
        $this->assign ( 'w', $w );
        if (M ( 'Laundry_customers' )->where ( array ('c_openid' => $w ['openid'] ) )->find ()) {
            $c = M ( 'Laundry_customers' )->where ( array ('c_openid' => $w ['openid'] ) )->find ();
            $this->assign ( 'c', $c );
            $key = 1;
        } elseif (M ( 'Laundry_online_franchisee' )->where ( array ('token' => $this->token, 'online_openid' => $w ['openid'] ) )->find ()) {
            $o = M ( 'Laundry_online_franchisee' )->where ( array ('token' => $this->token, 'online_openid' => $w ['openid'] ) )->find ();
            $this->assign ( 'o', $o );
            $key = 0;
            $c = M('Laundry_customers')->order('id desc')->where(array('online_id'=>$o['id'],'token'=>$this->token))->find();
            $this->assign('c',$c);
        }
        $this->assign ( 'key', $key );
        if (IS_POST) {
            //print_r($_POST);exit;
            if(!$_POST ['c_province_id']){
                exit(json_encode(array('info'=>'请填写省份','status'=>-1)));
            }
            if(!$_POST ['c_city_id']){
                exit(json_encode(array('info'=>'请填写地级市','status'=>-1)));
            }
            if(!$_POST ['c_town_id']){
                exit(json_encode(array('info'=>'请填写市县区','status'=>-1)));
            }
            if(!$_POST ['c_address']){
                exit(json_encode(array('info'=>'请填写详细地址','status'=>-1)));
            }

            $good_id = $_POST ['goods_id'];
            $data ['c_name'] = $_POST ['c_name'];
            $data ['c_tel'] = $_POST ['c_tel'];

            $data ['c_province'] = $_POST ['c_province'];
            $data ['c_province_id'] = $_POST ['c_province_id'];

            $data ['c_city'] = $_POST ['c_city'];
            $data ['c_city_id'] = $_POST ['c_city_id'];

            $data ['c_town'] = $_POST ['c_town'];
            $data ['c_town_id'] = $_POST ['c_town_id'];

            $data ['c_address'] = $_POST ['c_address'];
            $data ['token'] = $this->token;
            if (($_POST ['key'] == 1) || ($_POST ['key'] == NULL)) {
                $data ['c_openid'] = $_POST ['c_openid'];
                $data ['c_source'] = 0;
                $data ['online_id'] = 0;
                $data ['brand_id'] = 0;
                if ($_POST ['key'] == 1) {
                    if (M ( 'Laundry_customers' )->where ( array ('token' => $this->token, 'openid' => $data ['c_openid'] ) )->save ( $data )) {
                        $this->ajaxReturn ( array ('info' => '保存成功', 'status' => 1, 'url' => 'index.php?g=Wap&m=Laundry&a=goods&token=' . $this->token . '&openid=' . $data ['c_openid'] . '&goods_id=' . $good_id ) );
                    } else {
                        $this->ajaxReturn ( array ('info' => '保存失败', 'status' => 0 ) );
                    }
                } elseif ($_POST ['key'] == NULL) {
                    if (M ( 'Laundry_customers' )->add ( $data )) {
                        $this->ajaxReturn ( array ('info' => '保存成功', 'status' => 1, 'url' => 'index.php?g=Wap&m=Laundry&a=goods&token=' . $this->token . '&openid=' . $data ['c_openid'] . '&goods_id=' . $good_id ) );
                    } else {
                        $this->ajaxReturn ( array ('info' => '保存失败', 'status' => 0 ) );
                    }
                }
            } elseif ($_POST ['key'] == 0) {
                $data ['c_openid'] = '';
                $data ['c_source'] = 1;
                $d ['openid'] = $_POST ['c_openid'];
                $franchisee = M ( 'Laundry_online_franchisee' )->where ( array ('online_openid' => $d ['openid'] ) )->find ();
                $data ['online_id'] = $franchisee ['id'];
                $data ['brand_id'] = $franchisee ['brand_id'];
                if(M('Laundry_customers')->where(array('c_tel'=>$data['c_tel']))->find()){
                    if(M('Laundry_customers')->where(array('c_tel'=>$data['c_tel']))->save($data)){
                        $this->ajaxReturn ( array ('info' => '保存成功', 'status' => 1, 'url' => 'index.php?g=Wap&m=Laundry&a=goods&token=' . $this->token . '&openid=' . $d ['openid'] . '&goods_id=' . $good_id ) );
                    } else {
                        $this->ajaxReturn ( array ('info' => '保存失败', 'status' => 0 ) );
                    }
                }else{
                    if (M ( 'Laundry_customers' )->add ( $data )) {
                        $this->ajaxReturn ( array ('info' => '保存成功', 'status' => 1, 'url' => 'index.php?g=Wap&m=Laundry&a=goods&token=' . $this->token . '&openid=' . $d ['openid'] . '&goods_id=' . $good_id ) );
                    } else {
                        $this->ajaxReturn ( array ('info' => '保存失败', 'status' => 0 ) );
                    }
                }
            }
        }
        $this->display ();
    }
    /*
     * 会员中心
     */
    public function membercenter() {
        $customers = M('Laundry_customers')->where(array('token'=>$this->token,'c_openid'=>$_GET['openid']))->find();
        $onlineInfo = M('Laundry_online_franchisee')->where(array('token'=>$this->token,'online_openid'=>$_GET['openid']))->find();
        if($customers){
            $this->assign('money',$customers['balance']);
            if(IS_POST){
                if(!empty($_REQUEST['fee'])){
                    $d['c_openid'] = $_REQUEST['openid'];
                    $d['token'] = $this->token;
                    $d['c_flow_amount'] = $_REQUEST['fee'];
                    $d['c_flow_recorde_type'] = 1;
                    $d['flow_type'] = 1;
                    $d['c_flow_recorde_addtime'] = date('Y-m-d H:i:s');
                    $customerInfo = M('Laundry_customers')->where(array('token'=>$this->token,'c_openid'=>$_REQUEST['openid']))->find();
                    $d['balance'] = $customerInfo['balance'];
                    $d['c_name'] = $customerInfo['c_name'];
                    $d['recharge_channel'] = 2;
                    if($lastid = M('Laundry_customers_liquidity')->add($d)){
                        $this->ajaxReturn($lastid,'生成订单成功，正在跳转…',1);
                    }else{
                        $this->ajaxReturn($lastid,'生成订单失败',0);
                    }
                }
            }
        }elseif($onlineInfo){
            $this->assign('money',$onlineInfo['balance']);
            if(IS_POST){
                if(!empty($_REQUEST['fee'])){
                    $onlineInfo = M('Laundry_online_franchisee')->where(array('token'=>$this->token,'online_openid'=>$_GET['openid']))->find();
                    $d['uid'] = $this->tpl['uid'];
                    $d['token'] = $this->token;
                    $d['online_id'] = $onlineInfo['id'];
                    $d['brand_id'] = $onlineInfo['brand_id'];
                    $d['online_flow_amount'] = $_REQUEST['fee'];
                    $d['online_flow_type'] = 1;
                    $d['flow_type'] = 1;
                    $d['online_flow_time'] = time();
                    $d['balance'] = $onlineInfo['balance'];
                    $d['online_name'] = $onlineInfo['online_name'];
                    if($lastid = M('Laundry_online_franchisee_liquidity')->add($d)){
                        $this->ajaxReturn($lastid,'生成订单成功，正在跳转…',1);
                    }else{
                        $this->ajaxReturn($lastid,'生成订单失败',0);
                    }
                }
            }
        }
        $this->display ();
    }
    /*
     * 洗客账单
     */
    public function customersliquidity(){
        $customers = M('Laundry_customers')->where(array('token'=>$this->token,'c_openid'=>$_GET['openid']))->find();
        $onlineInfo = M('Laundry_online_franchisee')->where(array('token'=>$this->token,'online_openid'=>$_GET['openid']))->find();
        if($customers){
            $orderList = M('Laundry_order')->where(array('token'=>$this->token,'order_person_openid'=>$_GET['openid']))->select();
            foreach($orderList as $k => $v){
                if($v['order_pay_status'] == 1){
                    $array[$k]['time'] = date('Y-m-d H:i:s',$v['order_addtime']);
                    $array[$k]['money'] = $v['order_price'];
                    $array[$k]['type'] = 2;
                    $array[$k]['status'] = 1;
                    $array[$k]['profit'] = 0;
                }
            }
            $customersLiquidity = M('Laundry_customers_liquidity')->where(array('token'=>$this->token,'c_openid'=>$_GET['openid']))->select();
            foreach($customersLiquidity as $k => $v){
                $arr[$k]['time'] = $v['c_flow_recorde_addtime'];
                $arr[$k]['money'] = $v['c_flow_amount'];
                $arr[$k]['type'] = $v['flow_type'];
                $arr[$k]['status'] = $v['c_flow_status'];
                $arr[$k]['profit'] = $v['offers_proportion'] * $v['c_flow_amount'];
            }
        }elseif($onlineInfo){
            $onlineFranchisee = M('Laundry_online_franchisee_liquidity')->where(array('token'=>$this->token,'online_id'=>$onlineInfo['id']))->select();
            foreach($onlineFranchisee as $k => $v){
                $arr[$k]['time'] = date('Y-m-d H:i:s',$v['online_flow_time']);
                $arr[$k]['money'] = $v['online_flow_amount'];
                $arr[$k]['type'] = $v['flow_type'];
                $arr[$k]['status'] = $v['online_flow_status'];
                $arr[$k]['profit'] = $v['offers_proportion'] * $v['online_flow_amount'];
            }
            $orderList = M('Laundry_order')->where(array('token'=>$this->token,'order_person_openid'=>$_GET['openid']))->select();
            foreach($orderList as $k => $v){
                if($v['order_pay_status'] == 1){
                    $array[$k]['time'] = date('Y-m-d H:i:s',$v['order_addtime']);
                    $array[$k]['money'] = $v['order_price'];
                    $array[$k]['type'] = 2;
                    $array[$k]['status'] = 1;
                    $array[$k]['profit'] = 0;
                }
            }
        }
        if(empty($array) && !empty($arr)) {
            $allRecorder = $arr;
        }
        if(!empty($array) && empty($arr)){
            $allRecorder = $array;
        }
        if(!empty($array) && !empty($arr)){
            $allRecorder = array_merge($array,$arr);
        }
        if(empty($array) && empty($arr)){
            $allRecorder = array();
        }
        if(empty($allRecorder)){
            $this->assign('key',1);
        }
        $allRecorder = array_reverse($allRecorder);
        foreach($allRecorder as $k => $v){
            $allprofit = $v['profit'] + $allprofit;
        }
        $this->assign('allprofit',$allprofit);
        $this->assign('allRecorder',$allRecorder);
        $this->display();
    }
    /*
     * 账户支付
     */
    public function local(){
        if(IS_POST){
            $orderInfo = M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$_REQUEST['orderid'],'order_person_openid'=>$_REQUEST['openid']))->find();
            if($orderInfo['order_price'] == $_REQUEST['price']){
                if($orderInfo['order_pay_status'] == 1){
                    exit(json_encode(array('info'=>'该订单已经支付！','status'=>-2)));
                }else{
                    $customer = M('Laundry_customers')->where(array('token'=>$this->token,'c_openid'=>$_REQUEST['openid']))->find();
                    $online = M('Laundry_online_franchisee')->where(array('token'=>$this->token,'online_openid'=>$_REQUEST['openid']))->find();
                    if($customer) {
                        if($customer['balance'] < $orderInfo['order_price']){
                            exit(json_encode(array('info'=>'您账户余额不足','status'=>-3)));
                        }else{
                            $data['balance'] = $customer['balance'] - $_REQUEST['price'];
                            $data['token'] = $this->token;
                            $data['c_name'] = $customer['c_name'];
                            $data['c_flow_amount'] = $_REQUEST['price'];
                            $data['c_flow_recorde_addtime'] = date('Y-m-d H:i:s');
                            $data['c_flow_recorde_type'] = 1;
                            $data['c_openid'] = $_REQUEST['openid'];
                            $data['c_flow_recorde_type'] = 1;
                            $data['c_flow_status'] = 1;
                            $data['flow_type'] = 2;
                            mysql_query("START TRRANSACTION");
                            $res1 = M('Laundry_customers')->where(array('token'=>$this->token,'c_openid'=>$_REQUEST['openid']))->setField('balance',$data['balance']);
                            $res2 = M('Laundry_customers_liquidity')->add($data);
                            $res3 = M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$orderInfo['order_sn']))->setField('order_payment_status',3);
                            $res4 = M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$orderInfo['order_sn']))->setField('order_pay_status',1);
                            if($res1 && $res2 && $res3 && $res4){
                                mysql_query("COMMIT");
                                exit(json_encode(array('info'=>'支付成功','status'=>1)));
                            }else{
                                mysql_query('ROLLBACK');
                                exit(json_encode(array('info'=>'支付支付失败','status'=>0)));
                            }
                        }
                    }elseif($online){
                        if($online['balance'] < $orderInfo['order_price']){
                            exit(json_encode(array('info'=>'您账户余额不足','status'=>-3)));
                        }else{
                            $data['token'] = $this->token;
                            $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
                            $data['uid'] = $wxuser['uid'];
                            $data['online_id'] = $online['id'];
                            $data['brand_id'] = $online['brand_id'];
                            $data['online_flow_amount'] = $orderInfo['order_price'];
                            $data['online_flow_status'] = 1;
                            $data['online_flow_type'] = 1;
                            $data['online_flow_time'] = strtotime(date('Y-m-d H:i:s'));
                            $data['online_name'] = $online['online_name'];
                            $data['balance'] = $online['balance'] - $orderInfo['order_price'];
                            $data['flow_type'] = 2;
                            mysql_query("START TRANSACTION");
                            $res1 = M('Laundry_online_franchisee')->where(array('token'=>$this->token,'online_openid'=>$orderInfo['order_person_openid']))->setField('balance',$data['balance']);
                            $res2 = M('Laundry_online_franchisee_liquidity')->add($data);
                            $res3 = M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$orderInfo['order_sn']))->setField('order_payment_status',3);
                            $res4 = M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$orderInfo['order_sn']))->setField('order_pay_status',1);
                            if($res1 && $res2 && $res3 && $res4){
                                mysql_query("COMMIT");
                                exit(json_encode(array('info'=>'支付成功','status'=>1)));
                            }else{
                                mysql_query('ROLLBACK');
                                exit(json_encode(array('info'=>'支付支付失败','status'=>0)));
                            }
                        }
                    }
                }
            }else{
                exit(json_encode(array('info'=>'你在耍流氓','status'=>-1)));
            }
        }
    }
    /*
     * 在线加盟商激活
     */
    public function activate() {
        $data ['token'] = $_GET ['token'];
        $d ['online_openid'] = $_GET ['openid'];
        if(M('Laundry_online_franchisee')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->find()){
            $this->redirect('Laundry/customersliquidity',array('token'=>$this->token,'openid'=>$_GET['openid']));
        }
        $this->assign ( 'data', $data );
        $this->assign ( 'd', $d );
        if (M ( 'Laundry_online_franchisee' )->where ( array ('token' => $data ['token'], 'online_openid' => $d ['online_openid'] ) )->find ()) {
            $key = 1;
            $this->assign ( 'key', $key );
        }
        if (IS_POST) {
            $data ['token'] = $_POST ['token'];
            $d ['online_openid'] = $_POST ['online_openid'];
            $d ['online_act_time'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
            $data ['online_login_name'] = $_POST ['online_login_name'];
            $data ['online_tel'] = $_POST ['online_tel'];
            $data ['online_pwd'] = $_POST ['online_pwd'];
            $data ['nonline_password'] = md5 ( $data ['online_pwd'] );
            if (M ( 'Laundry_online_franchisee' )->where ( $data )->find ()) {
                if (M ( 'Laundry_online_franchisee' )->where ( $data )->save ( $d )) {
                    $this->ajaxReturn ( array ('info' => '恭喜您!已经激活成功~', 'status' => 1 ) );
                } else {
                    $this->ajaxReturn ( array ('info' => '糟糕！激活失败了 ！', 'status' => 0 ) );
                }
            }
        }
        $this->display ();
    }
    /*
     * 员工激活
     */
    public function employeesactivate(){
        if(M('Laundry_employees')->where(array('token'=>$this->token,'employees_openid'=>$_GET['openid']))->find()){
            $this->redirect('index.php?g=Wap&m=Laundry&a=employeesactivatesucc&token=' . $this->token .'&openid=' . $_GET['openid']);
        }else{
            $this->assign('get',$_GET);
            if(IS_POST){
                if(M('Laundry_employees')->where(array('token'=>$this->token,'employees_name'=>trim($_POST['name']),'employees_tel'=>trim($_POST['tel']),'employees_pwd'=>trim($_POST['pwd'])))->find()){
                    $data['employees_activetime'] = date('Y-m-d H:i:s');
                    $data['employees_openid'] = $_POST['openid'];
                    if(M('Laundry_employees')->where(array('token'=>$this->token,'employees_name'=>trim($_POST['name']),'employees_tel'=>trim($_POST['tel']),'employees_pwd'=>trim($_POST['pwd'])))->save($data)){
                        $this->ajaxReturn(array('info'=>'激活成功','status'=>1));
                    }else{
                        $this->ajaxReturn(array('info'=>'激活失败','status'=>0));
                    }
                }else{
                    $this->ajaxReturn(array('info'=>'激活失败,填写信息有误','status'=>0));
                }
            }
            $this->display();
        }
    }
    /*
     * 员工激活成功
     */
    public function employeesactivatesucc(){
        $this->display();
    }

    /*
     * 员工领取洗衣袋
     * 1.洗衣袋扫描之后应该首先判断洗衣袋是不是已经有了管理员，如果没有管理员，则跳转到领取洗衣袋的页面
     * 如果有了管理员，则应该判断扫描者是否为洗衣袋管理员，如果不是，则跳转到不能领取洗衣袋的页面
     * 如果为洗衣袋的管理员,则代表该管理员已经首先扫描过该洗衣袋二维码，然后跳转到给洗衣袋选择状态的页面
     * 例如：
     * 甲员工首先扫描了洗衣袋，其他员工再扫描洗衣袋二维码时，就会跳转到：sorry~我是编号bag_sn号洗衣袋，我已经被领取了哦~
     * 如果是甲员工领取了洗衣袋，之后甲员工再次扫描，则跳转到选择洗衣袋状态
     */
    public function bag(){
        $bagdata = M('Laundry_bag')->where(array('token'=>$this->token,'id'=>$_GET['bagid'],'bag_manager_openid'=>$_GET['openid']))->find();
	if($bagdata){
            //print_r($bagdata);exit;
	    $this->redirect('Wap/Laundry/employees',array('token'=>$this->token,'openid'=>$_GET['openid'],'bagid'=>$_GET['bagid']));	    
        }else{
            $bagInfo = M('Laundry_bag')->where(array('id'=>$_GET['bagid']))->find();
            $employees = M('Laundry_employees')->where(array('token'=>$this->token,'employees_openid'=>$_GET['openid']))->find();
            $this->assign('employees',$employees);
            $this->assign('bagInfo',$bagInfo);
            $this->assign('get',$_GET);
            if(IS_POST){
                $bagInfo = M('Laundry_bag')->where(array('token'=>$this->token,'id'=>$_POST['bagid']))->find();
                if(M('Laundry_employees')->where(array('token'=>$this->token,'employees_openid'=>$_POST['openid']))->find()){
                    if(($bagInfo['bag_status'] == 0) && (($bagInfo['bag_manager_openid'] == null) ||($bagInfo['bag_manager_openid'] == ''))){
                        $data['bag_manager_openid'] = $_POST['openid'];
                        $d = M('Laundry_employees')->where(array('token'=>$this->token,'employees_openid'=>$data['bag_manager_openid']))->find();
                        $data['bag_manager_name'] = $d['employees_name'];
                        $data['bag_manager_tel'] = $d['employees_tel'];
                        $data['is_recive'] = 1;
                        $where['id'] = $_POST['bagid'];
                        if(M('Laundry_bag')->where($where)->save($data)){
                            $this->ajaxReturn(array('info'=>'恭喜您~领取洗衣袋成功了哦~','status'=>1));
                        }else{
                            $this->ajaxReturn(array('info'=>'sorry~领取洗衣袋失败了，请再扫描一次~','status'=>0));
                        }
                    }
                }else{
                    $this->ajaxReturn(array('info'=>'sorry,您不是本店员工或者为未激活员工，不可以领取洗衣袋的~','status'=>-1));
                }
            }
            $this->display();
        }
    }
    /*
     * 领取袋子之后跳到主页
     */
    public function employees(){
        $employeesInfo = M('Laundry_employees')->where(array('token'=>$this->token,'employees_openid'=>$_GET['openid']))->find();
        $this->assign('employeesInfo',$employeesInfo);
        $bagInfo = M('Laundry_bag')->where(array('token'=>$this->token,'bag_manager_openid'=>$_GET['openid'],'id'=>$_GET['bagid']))->find();
        $this->assign('bagInfo',$bagInfo);
        $this->display();
    }
    /*
     * 把订单添加在洗衣袋里面
     */
    public function search(){
        $bagInfo = M('Laundry_bag')->where(array('token'=>$this->token,'id'=>$_GET['bagid'],'bag_manager_openid'=>$_GET['openid']))->find();
        $this->assign('bagInfo',$bagInfo);
        if(IS_POST){
            //print_r($_POST);exit;
            if(strlen(trim($_POST['value'])) == 11){
                //查找选择的是现金付款，或者是已经付款的订单，才派员工去领取order_pay_status=1（确认支付），order_payment_status=0（现金支付）
                //$orderInfo = M('Laundry_order')->where(array('token'=>$this->token,'order_person_tel'=>$_POST['value'],'order_logistics_status'=>'-1'))->select();
                $sql = "select * from tp_laundry_order WHERE token='".$this->token."' and order_logistics_status='-1' and order_person_tel='".$_POST['value']."' and (order_pay_status=1 or order_payment_status=0)";
                $orderInfo = M('Laundry_order')->query($sql);
            }else{
                $w['order_person_name'] = array('like',"%".$_POST['value']."%");
                $orderInfo = M('Laundry_order')->where("token='".$this->token."' and order_logistics_status='-1' and order_person_name='".$_POST['value']."' and (order_pay_status=1 or order_payment_status=0)")->select();
            }
            if($orderInfo){
                $this->ajaxReturn($orderInfo,'',1);
            }
        }else{
            $this->display();
        }
    }
    /*
     * 添加订单到洗衣袋成功页面
     */
    public function additemsucc(){
        $this->assign('get',$_GET);
        if(IS_POST){
            $orderInfo = M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$_POST['order_sn']))->find();
            $bagInfo = M('Laundry_bag')->where(array('token'=>$this->token,'bag_sn'=>$_POST['bag_sn']))->find();
            $data['token'] = $this->token;
            $data['bag_manager_openid'] = $bagInfo['bag_manager_openid'];
            $data['bagid'] = $bagInfo['id'];
            $data['bag_sn'] = $_POST['bag_sn'];
            $data['orderid'] = $orderInfo['id'];
            $data['order_sn'] = $_POST['order_sn'];
            $data['addtime'] = date('Y-m-d H:i:s');
            if(M('Laundry_bag_order')->add($data)){
                $d['order_logistics_status'] = 0;
                $d['updatetime'] = date('Y-m-d H:i:s');
                M('Laundry_order')->where(array('order_sn'=>$_POST['order_sn'],'token'=>$this->token))->save($d);
                $this->ajaxReturn(array('info'=>'添加成功','status'=>1,'url'=>'index.php?g=Wap&m=Laundry&a=additemsucc&token=' . $this->token.'&openid='.$data['bag_manager_openid'].'&bagid='.$data['bagid']));
            }else{
                $this->ajaxReturn(array('info'=>'添加失败','status'=>0));
            }
        }
        $this->display();
    }
    /*
     * 从袋子中移除订单
     */
    public function removeitem(){
        if(IS_POST){
            if(M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$_POST['order_sn']))->find()){
                $data['order_logistics_status'] = -1;
                if(M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$_POST['order_sn']))->save($data)){
                    M('Laundry_bag_order')->where(array('token'=>$this->token,'order_sn'=>$_POST['order_sn'],'bag_manager_openid'=>$_POST['openid']))->setField(array('is_clear'=>1,'clear_time'=>date('Y-m-d H:i:s')));
                    $this->ajaxReturn(array('info'=>'移除成功','status'=>1));
                }else{
                    $this->ajaxReturn(array('info'=>'移除失败','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'找不到该订单','status'=>2));
            }
        }
    }
    /*
     * 洗衣袋的状态选择
     */
    public function bagstatusselect(){
        $bagInfo = M('Laundry_bag')->where(array('token'=>$this->token,'id'=>$_GET['bagid']))->find();
        $this->assign('bagInfo',$bagInfo);
        $employees = M('Laundry_employees')->where(array('token'=>$this->token,'employees_openid'=>$_GET['openid']))->find();
        $this->assign('employees',$employees);
        if(IS_POST){
            $a = M('Laundry_bag_order')->where(array('token'=>$this->token,'bag_sn'=>$_POST['bag_sn']))->select();
            if($a){
                foreach ($a as $k => $v){
                    $w['order_sn'][$k] = array('eq',$v['order_sn']);
                }
                array_push($w['order_sn'], 'or');
                $d['order_logistics_status'] = $_POST['bagStatus'];
                $d['order_pay_status'] = 1;
                $d['updatetime'] = date('Y-m-d H:i:s');
                $w['token'] = $this->token;
                if(M('Laundry_order')->where($w)->save($d)){
                    $b['bag_status'] = $_POST['bagStatus']; 
                    M('Laundry_bag')->where(array('token'=>$this->token,'bag_sn'=>$_POST['bag_sn']))->save($b);
                    $this->ajaxReturn(array('info'=>'提交成功','status'=>1));
                }else{
                    $this->ajaxReturn(array('info'=>'提交失败','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'袋子里面没有订单','status'=>2));
            }
        }
        $this->display();
    }
    /*
     * 袋子中的订单展示
     */
    public function bagorder(){
        $this->assign('get',$_GET);
        $list = M('Laundry_bag_order')->where(array('token'=>$this->token,'bag_manager_openid'=>$_GET['openid'],'bagid'=>$_GET['bagid'],'is_clear'=>0))->select();
        if($list){
            $key = 1;
            $this->assign('key',$key);
            foreach($list as $k => $v){
                $a[$k] = M('Laundry_order')->where(array('token'=>$this->token,'order_sn'=>$v['order_sn']))->find();
            }
            $this->assign('a',$a);
        }
        $this->display();
    }
    /*
     * 清空袋子，包括袋子的管理员，一起也清空了，所以会跳出袋子的页面
     */
    public function clear(){
        $bagInfo = M('Laundry_bag')->where(array('token'=>$this->token,'id'=>$_GET['bagid'],'bag_manager_openid'=>$_GET['openid']))->find();
        $this->assign('bagInfo',$bagInfo);
        if(IS_POST){
            //print_r($_POST);exit();
            $bagOrder = M('Laundry_bag_order')->where(array('token'=>$this->token,'bag_sn'=>$_POST['bag_sn'],'bag_manager_id'=>$_POST['openid']))->select();
            //print_r($bagOrder);exit;
            //如果袋子里面有订单，则查询袋子和订单对应的表，将里面的is_clear字段置为0，代表解除绑定
            if($bagOrder){
                $data['is_clear'] = 1;
                $data['clear_time'] = date('Y-m-d H:i:s');
                if(M('Laundry_bag_order')->where(array('token'=>$this->token,'bag_sn'=>$_POST['bag_sn'],'bag_manager_id'=>$_POST['openid']))->save($data)){
                    $d['bag_manager_name'] = '';
                    $d['bag_manager_openid'] = '';
                    $d['bag_manager_tel'] = '';
                    $d['bag_status'] = 0;
                    $d['is_recive'] = 0;
                    if(M('Laundry_bag')->where(array('token'=>$this->token,'bag_manager_openid'=>$_POST['openid'],'bag_sn'=>$_POST['bag_sn']))->save($d)){
                        $this->ajaxReturn(array('info'=>'解除成功！','status'=>1,'url'=>'index.php?g=Wap&m=Laundry&a=exit'));
                    }else{
                        $this->ajaxReturn(array('info'=>'解除失败！','status'=>0));
                    }
                }
                //如果袋子里面没有订单，则直接清除袋子表中管理员的信息就可以了
            }else{
                $d['bag_manager_name'] = '';
                $d['bag_manager_openid'] = '';
                $d['bag_manager_tel'] = '';
                $d['bag_status'] = 0;
                $d['is_recive'] = 0;
                if(M('Laundry_bag')->where(array('token'=>$this->token,'bag_manager_openid'=>$_POST['openid'],'bag_sn'=>$_POST['bag_sn']))->save($d)){
                    $this->ajaxReturn(array('info'=>'解除成功！','status'=>1,'url'=>'index.php?g=Wap&m=Laundry&a=exit'));
                }else{
                    $this->ajaxReturn(array('info'=>'解除失败！','status'=>0));
                }
            }
        }
        $this->display();
    }



 























}