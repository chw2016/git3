<?php
/*
 * Created by 訾超 in 2014-09-24
 * Notice: If you want to modify this code please do note by English.
 */
class LaundryAction extends UserAction {
	public function _initialize() {
		parent::_initialize ();
		if (session ( 'brand_id' ) == $_GET ['brand_id']) {
			$brand_id = $_GET ['brand_id'];
			$this->assign ( 'brand_id', $brand_id );
		} elseif (session ( 'brand_id' ) !== $_GET ['brand_id']) {
			$this->redirect ( 'index.php?g=User&m=Branch&a=index&token=' . $this->token . '&modulename=Laundry_brandserviceproviders' );
		}
	}
	public function index(){

	    if($this->token == $_GET['token']){
	        /*
	         * Customers
	         */
            $customers = new Model();
            $sql = "select c_town,count(c_town) as counts from tp_laundry_customers where token='".$this->token . "'  group by c_town";
            $result = $customers->query($sql);
            foreach($result as $k => $v){
                $arr[$k] = $result[$k]['c_town'];
            }
            $arr = json_encode($arr);

            $this->assign('arr',$arr);
            foreach($result as $k => $v){
                $array[$k]['value'] = $result[$k]['counts'];
                $array[$k]['name'] = $result[$k]['c_town'];
            }
            $result = json_encode($array);
            $this->assign('result',$result);
            /*
             * year
             */
	        $year = date ( 'Y' ) % 4;
            $month = date ( 'm' );
            if ($year == 0) {
                $date = array ('01' => '31', '02' => '29', '03' => '31', '04' => '30', '05' => '31', '06' => '30', '07' => '31', '08' => '31', '09' => '30', '10' => '31', '11' => '30', '12' => '31' );
            } else {
                $date = array ('01' => '31', '02' => '28', '03' => '31', '04' => '30', '05' => '31', '06' => '30', '07' => '31', '08' => '31', '09' => '30', '10' => '31', '11' => '30', '12' => '31' );
            }
            $all = array(); //总订单数组 
            $done = array(); // 总确认订单数组
            $outstanding = array(); // 总未确认订单数组

            /*
             * Orders
            */
            foreach($date as $k => $v){
               $start_month = strtotime(date('Y').'-'.$k.' 00:00:00'); //The start time of each month
               $end_month = strtotime(date('Y').'-'.$k.'-'.$v.' 23:59:59'); //The end time of each month

               $f ['token'] = $this->token;
            
               $ww['order_addtime'] = array (array ('egt', $start_month ), array ('elt', $end_month ) );
               $allorders = M('Laundry_order')->where($ww)->select();
               $y = count($allorders);     //All orders in this month
               $all[] = $y;
               $f ['updatetime'] = array (array ('egt', $start_month ), array ('elt', $end_month ) );
               $g ['order_logistics_status'] = array('egt', 4);
               $doneorders = M('Laundry_order')->where(array('token'=>$f['token'],'updatetime'=>$f['updatetime'],'order_logistics_status'=>$g['order_logistics_status']))->select();
               $x = count($doneorders);    //All completed orders in this month
               $done[] = $x;
               $z = $y - $x;               //All outstanding orders in this month
               $outstanding[] = $z;
            }
            $orders = M('Laundry_order')->where(array('token'=>$this->token))->count();
            
            $this->assign('orders',$orders);
            $all = json_encode($all);
            $this->assign('all',$all);
            $done = json_encode($done);
            $this->assign('done',$done);
            $outstanding = json_encode($outstanding);
            $this->assign('outstanding',$outstanding);
            //print_r($orders).'<br/>';
            //print_r($all).'<br/>';
            //print_r($done);
            
            /*
             * Brand
            */
            $brandMonthEarning = array();
            $brand_month_earning = M('Laundry_brandserviceproviders')->field('username,id')->where(array('token'=>$this->token))->select();
            //print_r($brand_month_earning);
            foreach($brand_month_earning as $ks => $vs){
                $tempanyData = array();
                $tempdataForMax = array();
                foreach($date as $k => $v){
                    $anyData = array();
                    $brand_flow_time = array();
                    $start_month = strtotime(date('Y').'-'.$k); //The start time of each month
                    $end_month = strtotime(date('Y').'-'.$k.'-'.$v); //The end time of each month
                    $brand_flow_time = array (array ('egt', $start_month ), array ('elt', $end_month ) );
                    $anyData = M('Laundry_brandserviceproviders_liquidity')->field('sum(brand_earnings) as alls')->where(array('token'=>$this->token,'brand_id'=>$vs['id'],'brand_flow_time'=>$brand_flow_time))->select();
                    if($anyData[0]['alls'] == NULL){
                        $tempanyData[] = 0;
                    }else{
                        $tempanyData[] =$anyData[0]['alls'];
                    }
                }
                $tempdataForMax['name'] = $vs['username'];
                $b[] = $vs['username'];
                $tempdataForMax['type'] = 'line';
                $tempdataForMax['smooth'] = true;
                $tempdataForMax['itemStyle'] = array(
                                                'normal'=>array(
                                                        'areaStyle'=>array(
                                                                'type'=> 'default'
                                                                     )
                                                         )
                                               );
                $tempdataForMax['data'] = $tempanyData;
                $brandMonthEarning[] = $tempdataForMax;
            }
            //print_r($b);
            $this->assign('b',json_encode($b));
            $this->assign('a',json_encode($brandMonthEarning));
            //print_r(json_encode($brandMonthEarning));
            /*
             * Franchisee
             */
            $onlineMonthEarning = array();
            $online = M('Laundry_online_franchisee')->field('online_name,id')->where(array('token'=>$this->token))->select();
            //print_r($online);
            foreach($online as $key => $value){
                $tempAnyDate = array();
                $tempDateForMax = array();
                foreach($date as $k => $v){
                    $ANYdate = array();
                    $start_month = strtotime(date('Y').'-'.$k); //The start time of each month
                    $end_month = strtotime(date('Y').'-'.$k.'-'.$v); //The end time of each month
                    $online_flow_time = array (array ('egt', $start_month ), array ('elt', $end_month ) );
                    $ANYdate = M('Laundry_online_franchisee_liquidity')->field('sum(online_earnings) as online_alls')->where(array('token'=>$this->token,'online_id'=>$value['id'],'online_flow_time'=>$online_flow_time))->select();
                    if($ANYdate[0]['online_alls'] == NULL){
                        $tempAnyDate[] = 0;
                    }else{
                        $tempAnyDate[] =$ANYdate[0]['online_alls'];
                    }
                }
                $tempDateForMax['name'] = $value['online_name'];
                $c[] = $value['online_name'];
                $tempDateForMax['type'] = 'line';
                $tempDateForMax['stack'] = '总量';
                $tempDateForMax['itemStyle'] = array(
                                                'normal'=>array(
                                                        'areaStyle'=>array(
                                                                'type'=> 'default'
                                                        )
                                                )
                                        );
                $tempDateForMax['data'] = $tempAnyDate;
                $onlineMonthEarning[] =  $tempDateForMax;
            }
            //print_r($c);
            $this->assign('o',json_encode($onlineMonthEarning));
            //print_r(json_encode($tempDateForMax));
            //print_r($tempDateForMax);
            $this->assign('c',json_encode($c));
	    }
	    $this->display();
	}
	/*
	 * Bags
	 */
	public function bag() {
	    
		if ($this->token == $_GET ['token']) {
			$c ['bag_sn'] = trim ( $_POST ['bag_sn'] );
			$c ['bag_manager_name'] = trim ( $_POST ['bag_manager_name'] );
			$c ['is_recive'] = trim ( $_POST ['is_recive'] );
			foreach ( $c as $k => $v ) {
				if ($v !== "") {
					$arr [$k] = $v;
				}
			}
			$this->assign ( 'arr', $arr );
			$arr ['token'] = $_GET ['token'];
			$count = M ( 'laundry_bag' )->where ( $arr )->count ();
			$page = new Page ( $count, 10 );
			$bag = M ( 'laundry_bag' )->where ( $arr )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
			$this->assign ( 'page', $page->show () );
			$this->assign ( 'bag', $bag );
		}
		$this->display ();
	}
	public function addbag() {
		if ($this->token == $_GET ['token']) {
			if (IS_POST) {
				$data ['bag_num'] = $_POST ['bag_num'];
				if (M ( 'laundry_bag' )->order ( 'id desc' )->limit ( 1 )->find ()) {
					$baginfo = M ( 'laundry_bag' )->order ( 'id desc' )->limit ( 1 )->find ();
                   // print_r($baginfo);exit;
					for($i = 1; $i <= $data ['bag_num']; $i ++) {
						$d [$i + $baginfo ['id']] ['token'] = $this->token;
						$d [$i + $baginfo ['id']] ['uid'] = session ( 'uid' );
						$d [$i + $baginfo ['id']] ['bag_sn'] = 'BAG' . sprintf ( '%05d', $i + $baginfo ['id'] ) . mt_rand ( 0000, 9999 );
						$d [$i + $baginfo ['id']] ['bag_addtime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
						$d [$i + $baginfo ['id']] ['bag_qrcode'] = $this->create ( $i + $baginfo ['id'] );
						$d [$i + $baginfo ['id']] ['bag_status'] = 0;
						M ( 'Laundry_bag' )->add ( $d [$i + $baginfo ['id']] );
					}
					if (M ( 'Laundry_bag' )->where ( array ('token' => $this->token, 'id' => $baginfo ['id'] + $data ['bag_num']) )->select ()) {
						$this->success ( '增加袋子成功', 'index.php?g=User&m=Laundry&a=bag&token=' . $this->token );
					} else {
						$this->success ( '增加袋子成功', 'index.php?g=User&m=Laundry&a=bag&token=' . $this->token );
					}
				} else {
					for($i = 1; $i <= $data ['bag_num']; $i ++) {
						$d [$i] ['token'] = $this->token;
						$d [$i] ['uid'] = session ( 'uid' );
						$d [$i] ['bag_sn'] = 'BAG' . sprintf ( '%05d', $i ) . mt_rand ( 0000, 9999 );
						$d [$i] ['bag_addtime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );

						$d [$i] ['bag_status'] = 0;
						$lastid = M ( 'Laundry_bag' )->add ( $d [$i] );
                        $bagcode = $this->create ( $lastid );
                        M ( 'Laundry_bag' )->where(array('id'=>$lastid))->save(array('bag_qrcode'=>$bagcode));
					}
					if (M ( 'Laundry_bag' )->where ( array ('token' => $this->token) )->select ()) {
						$this->success ( '增加袋子成功', 'index.php?g=User&m=Laundry&a=bag&token=' . $this->token );
					} else {
						$this->error ( '增加袋子失败', 'index.php?g=User&m=Laundry&a=bag&token=' . $this->token );
					}
				}
			}
		}
		$this->display ();
	}
	/*
	 * Delete bag
	 */
	public function del() {
		if ($this->token == $_GET ['token']) {
			$w ['id'] = $_GET ['bag_id'];
			$w ['token'] = $this->token;
			if (M ( 'Laundry_bag' )->where ( $w )->delete ()) {
				$this->success ( '删除成功', 'index.php?g=User&m=Laundry&a=bag&token=' . $this->token );
			} else {
				$this->error ( '删除失败', 'index.php?g=User&m=Laundry&a=bag&token=' . $this->token );
			}
		}
	}
	/*
	 * Delete all bags
	 */
	public function del_all() {
		if ($this->token == $_GET ['token']) {
			$w ['token'] = $this->token;
			if (M ( 'Laundry_bag' )->where ( $w )->delete ()) {
				$this->success ( '删除成功', 'index.php?g=User&m=Laundry&a=bag&token=' . $this->token );
			} else {
				$this->error ( '删除失败', 'index.php?g=User&m=Laundry&a=bag&token=' . $this->token );
			}
		}
	}
	
	/*
	 * About qr code
	 */
	public function creatTicket($token, $parament) {
		/* 发送数据到微信服务器端并获取数据 */
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
		$result = $this->api_notice_increment ( $url, $parament );
		$jsonInfo = json_decode ( $result, true );
		$ticket = $jsonInfo ['ticket'];
		/* 根据ticket获取图片资源 */
		$url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
		$ch = curl_init ();
		$header = "Accept-Charset: utf-8";
		curl_setopt ( $ch, CURLOPT_URL, $url2 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_NOBODY, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$package = curl_exec ( $ch );
		$httpInfo = curl_getinfo ( $ch );
		return array_merge ( array (
				'body' => $package 
		), array (
				'header' => $httpInfo 
		) );
	}
	/*
	 * Create qr code
	 */
	public function create($id) {
		$data ['id'] = $id;
        //print_r($data);exit;
		$parament = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 11' . $data ['id'] . '}}}';
		$api = M ( 'Diymen_set' )->where ( array ('token' => $this->token) )->find ();
		if ($api) {
			$url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $api ['appid'] . '&secret=' . $api ['appsecret'];
			$ch = curl_init ();
			$header = "Accept-Charset: utf-8";
			curl_setopt ( $ch, CURLOPT_URL, $url_get );
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt ( $ch, CURLOPT_NOBODY, 0 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			$package = curl_exec ( $ch );
			$json = json_decode ( $package );
			$access_token = $json->access_token;
			$imgSource = $this->creatTicket ( $access_token, $parament );
		}
		$this->assign ( 'imgUrl', $imgSource ['header'] ['url'] );
		return $imgSource ['header'] ['url'];
	}
	/*
	 * Customers
	 */
	public function customers() {
		if ($this->token == $_REQUEST ['token']) {
			$c ['c_name'] = trim ( $_REQUEST ['c_name'] );
			$c ['c_tel'] = trim ( $_REQUEST ['c_tel'] );
			$c ['c_zone'] = trim ( $_REQUEST ['c_zone'] );
            $c ['status'] = trim ( $_REQUEST ['status'] );
			$c ['brand_id'] = $_REQUEST ['brand_id'];
			foreach ( $c as $k => $v ) {
				if (($v !== '') && $v !== NULL) {
					$arr [$k] = $v;
				}
			}
			$arr ['token'] = session('token');
			$arr ['uid'] = session ( 'uid' );
			$this->assign ( 'arr', $arr );
			if ( $arr ['c_zone']) {
				$arr ['c_zone'] = array (array ('like', '%' . $arr ['c_zone'] . '%'));
			}
            if ( $arr ['c_name']) {
                $arr ['c_name'] = array (array ('like', '%' . $arr ['c_name'] . '%'));
            }
            if ($arr ['c_tel']) {
                $arr ['c_tel'] = array (array ('like', '%' . $arr ['c_tel'] . '%'));
            }
            if ($arr ['status'] == 1) {
                $arr ['c_openid'] = array('neq','');
            }else if($arr ['status'] == 2){
                $arr ['c_openid'] = array('eq','');
            }
			$count = M ( 'Laundry_customers' )->where ( $arr )->count ();
			$page = new Page ( $count, 10 );
			$customers = M ( 'Laundry_customers' )->where ( $arr )->limit ( $page->firstRow . ',' . $page->listRows )->order('balance desc,id desc')->select ();
			$this->assign ( 'page', $page->show () );
			$this->assign ( 'customers', $customers );
		}
		$this->display ();
	}

    public function delcustomers(){
        $where['id']=$this->_get('id','intval');
        $where['token']=session('token');
        if(D('Laundry_customers')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/customers',array('token'=>session('token'))));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/customers',array('token'=>session('token'))));
        }
    }
	public function customer() {
		if ($this->token == $_GET ['token']) {
			if (IS_POST) {
				$w ['id'] = $_POST ['c_id'];
				$w ['token'] = $this->token;
				$w ['uid'] = session ( 'uid' );
				$customer = M ( 'Laundry_customers' )->where ( $w )->find ();
                if($customer ['c_openid']){
                    $wxuser = M ( 'Wxusers' )->where ( array ('uid' => $w ['uid'], 'openid' => $customer ['c_openid']) )->find ();
                    $customer ['nickname'] = $wxuser ['nickname'];
                }else{
                    $customer ['nickname'] = '未知';
                }
				if ($customer) {
					$this->ajaxReturn ( $customer, '返回成功', 1 );
				} else {
					$this->ajaxReturn ( 0, '返回失败', 0 );
				}
			}
		}
		$this->display ();
	}
	/*
	 * Bill
	 */ 
	public function bill() {
		if ($this->token == $_GET ['token']) {
			$w ['token'] = $this->token;
			$w ['uid'] = session ( 'uid' );
			if (isset ( $_GET ['customer_id'] )) {
				$w ['c_id'] = $_GET ['customer_id'];
				$this->assign ( 'w', $w );
				$bill = M ( 'Laundry_customers_liquidity' )->where ( $w )->select ();
			} elseif (isset ( $_GET ['id'] )) {
				$w ['online_id'] = $_GET ['id'];
				$this->assign ( 'w', $w );
				$bill = M ( 'Laundry_online_franchisee_liquidity' )->where ( $w )->select ();
				$a = count ( $bill );
				for($i = 0; $i < $a; $i ++) {
					$bill [$i] ['online_flow_time'] = date ( 'Y-m-d H:i:s', $bill [$i] ['online_flow_time'] );
				}
			} else {
				if (isset ( $_GET ['brand_id'] )) {
					$w ['brand_id'] = $_GET ['brand_id'];
				} elseif (isset ( $_GET ['b_id'] )) {
					$w ['brand_id'] = $_GET ['b_id'];
				}
				$this->assign ( 'w', $w );
				$bill = M ( 'Laundry_brandserviceproviders_liquidity' )->where ( $w )->select ();
				$a = count ( $bill );
				for($i = 0; $i < $a; $i ++) {
					$bill [$i] ['online_flow_time'] = date ( 'Y-m-d H:i:s', $bill [$i] ['online_flow_time'] );
				}
			}
			$this->assign ( 'bill', $bill );
		}
		$this->display ();
	}
	/*
	 * Customers's order
	 */
	public function order() {
        if(session('token')) {
            $this->token = session('token');
        }else{
            $this->token = $_REQUEST['token'];
        }
        $where['token'] = $this->token;
        if($_REQUEST['order_sn']){
            $where['order_sn'] = array(array('like','%' .$_REQUEST['order_sn'].'%'));
        }
        if($_REQUEST['online_id']){
            if($_REQUEST['online_id'] == -1){
                $where['online_id'] = 0;
            }else{
                $where['online_id'] = $_REQUEST['online_id'];
            }

        }
        if($_REQUEST['order_person_name']){
            $where['order_person_name'] = array(array('like','%' .$_REQUEST['order_person_name'].'%'));
        }
        if($_REQUEST['order_person_tel']){
            $where['order_person_tel'] = array(array('like','%' .$_REQUEST['order_person_tel'].'%'));
        }

        $franchisee = M('Laundry_online_franchisee')->where(array('token'=>$this->token))->select();

        $count = M ( 'Laundry_order' )->where ( $where )->count();
        $page = new Page ( $count, 10 );
        $orderList = M('Laundry_order')->order('id desc')->where($where)->limit ( $page->firstRow . ',' . $page->listRows )->order('order_addtime desc')->select();
        if($orderList){
            foreach($orderList as $k => $v){
                $orderList[$k]['order_addtime'] = date('Y-m-d H:i:s',$v['order_addtime']);
            }
            $this->assign('orderList',$orderList);
        }
        $this->assign('order_sn',$_REQUEST['order_sn']);
        $this->assign('franchisee',$franchisee);
        $this->assign('order_person_name',$_REQUEST['order_person_name']);
        $this->assign('order_person_tel',$_REQUEST['order_person_tel']);
        $this->assign('online_id',$_REQUEST['online_id']);
        $this->assign('token',$this->token);
        $this->assign('brand_id',$_REQUEST['brand_id']);
        $this->assign ( 'page', $page->show () );

		$this->display ();
	}

    /*
     * delorder
     */
    public function delorder() {
        $where['id']=$_REQUEST['id'];
        $brand_id = $_REQUEST['brand_id'];
        $where['token']=session('token');
        if(D('Laundry_order')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/order',array('token'=>session('token'),'brand_id'=>$brand_id)));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/order',array('token'=>session('token'),'brand_id'=>$brand_id)));
        }
    }


    /*
     * cancel order
     */
    public function cancelorder() {
        $where['id']=$_REQUEST['id'];
        $brand_id = $_REQUEST['brand_id'];
        $where['token']=session('token');
        if(D('Laundry_order')->where($where)->data(array('order_logistics_status'=>'-2'))->save()){
            $this->success2('操作成功',U(MODULE_NAME.'/order',array('token'=>session('token'),'brand_id'=>$brand_id)));
        }else{
            $this->error2('操作失败',U(MODULE_NAME.'/order',array('token'=>session('token'),'brand_id'=>$brand_id)));
        }
    }


	/*
	 * Brand
	 */
	public function brand() {
		if ($this->token == $_REQUEST ['token']) {
			$c ['brand_name'] = trim ( $_REQUEST ['brand_name'] );
			$c ['brand_tel'] = trim ( $_REQUEST ['brand_tel'] );
			$c ['brand_zone'] = trim ( $_REQUEST ['brand_zone'] );
			$c ['id'] = $_GET ['brand_id'];
			foreach ( $c as $k => $v ) {
				if (($v !== '') and ($v !== NULL)) {
					$arr [$k] = $v;
				}
			}
			$arr ['token'] = session ( 'token' );;
			$arr ['uid'] = session ( 'uid' );
			$this->assign ( 'arr', $arr );
			if ($arr ['brand_zone']) {
				$arr ['brand_zone'] = array (
						array (
								'like',
								'%' . $arr ['brand_zone'] . '%' 
						) 
				);
			}
            if ($arr ['brand_name']) {
                $arr ['brand_name'] = array (
                    array (
                        'like',
                        '%' . $arr ['brand_name'] . '%'
                    )
                );
            }
            if ($arr ['brand_tel']) {
                $arr ['brand_tel'] = array (
                    array (
                        'like',
                        '%' . $arr ['brand_tel'] . '%'
                    )
                );
            }
			$count = M ( 'Laundry_brandserviceproviders' )->where ( $arr )->count ();
			$page = new Page ( $count, 10 );
			$brand = M ( 'Laundry_brandserviceproviders' )->where ( $arr )->limit ( $page->firstRow . ',' . $page->listRows )->order('balance desc')->select ();
			$this->assign ( 'page', $page->show () );
			$this->assign ( 'brand', $brand );
		}
		$this->display ();
	}
	public function branddetail() {
		if ($this->token == $_GET ['token']) {
			if (IS_POST) {
				$w ['id'] = $_POST ['brand_id'];
				$w ['token'] = $this->token;
				$w ['uid'] = session ( 'uid' );
				$brand = M ( 'Laundry_brandserviceproviders' )->where ( $w )->find ();
				$brand ['brand_addtime'] = date ( 'Y-m-d H:i:s', $brand ['brand_addtime'] );
				// print_r($brand);exit();
				if ($brand) {
					$this->ajaxReturn ( $brand, '返回成功', 1 );
				} else {
					$this->ajaxReturn ( 0, '返回失败', 0 );
				}
			}
		}
		$this->display ();
	}
	public function brandmanage() {
		if ($this->token == $_GET ['token']) {
			$op = $_GET ['op'] ? $_GET ['op'] : 0;
			if ($op == 1) {
				$w ['id'] = $_GET ['b_id'];
				$w ['token'] = $this->token;
				$w ['uid'] = session ( 'uid' );
				$this->assign ( 'w', $w );
				$brand = M ( 'Laundry_brandserviceproviders' )->where ( $w )->find ();
				$this->assign ( 'brand', $brand );
			}
			if (IS_POST) {
				$op = $_POST ['op'] ? $_POST ['op'] : 0;
				$d ['brand_name'] = $_POST ['brand_name'];
				$d ['brand_tel'] = $_POST ['brand_tel'];
				$d ['brand_zone'] = $_POST ['brand_zone'];
				$d ['brand_address'] = $_POST ['brand_address'];
				$d ['brand_pwd'] = $_POST ['brand_pwd'];
				$d ['password'] = md5 ( $d ['brand_pwd'] );
				$d ['longitude'] = $_POST ['longitude'];
				$d ['latitude'] = $_POST ['latitude'];
				$d ['username'] = $_POST ['brand_login_name'];
				$d ['token'] = $this->token;
				$d ['uid'] = session ( 'uid' );
				if ($op == 1) {
					// print_r($d);exit();
					if (M ( 'laundry_brandserviceproviders' )->where ( array (
							'token' => $this->token,
							'uid' => session ( 'uid' ),
							'id' => $_POST ['brand_id'] 
					) )->save ( $d )) {
						$this->ajaxReturn ( array (
								'info' => '编辑成功',
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=brand&token=' . $this->token 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => '编辑失败',
								'status' => 0,
								'url' => 'index.php?g=User&m=Laundry&a=brandmanage&token=' . $this->token 
						) );
					}
				} elseif ($op == 0) {
					$d ['brand_addtime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
					if (M ( 'Laundry_brandserviceproviders' )->add ( $d )) {
						$this->ajaxReturn ( array (
								'info' => '添加成功',
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=brand&token=' . $this->token 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => '添加失败',
								'status' => 0,
								'url' => 'index.php?g=User&m=Laundry&a=brandmanage&token=' . $this->token 
						) );
					}
				}
			}
			$this->assign ( 'op', $op );
		}
		$this->display ();
	}
    public function del_brand(){
        $brandInfo = M('Laundry_brandserviceproviders')->field('id,token')->where(array('token'=>$this->token,'id'=>$_GET['b_id']))->find();
        if($brandInfo['token'] == $_GET['token'] && $brandInfo){
            $brandSon = M('Laundry_online_franchisee')->where(array('token'=>$this->token,'brand_id'=>$brandInfo['id']))->field('id')->find();
            if($brandSon){
                exit(json_encode(array('info'=>'此品牌服务商下面有服务商，不能删除','status'=>0)));
            }else{
                if(M('Laundry_brandserviceproviders')->where(array('id'=>$brandInfo['id']))->delete()){
                    M('Laundry_brandserviceproviders')->where(array('token'=>$this->token,'brand_id'=>$brandInfo['id']))->delete();
                    exit(json_encode(array('info'=>'删除成功','status'=>1)));
                }
            }
        }else{
            exit(json_encode(array('info'=>'非法操作','status'=>-1)));
        }
    }
	/*
	 * Franchiess
	 */
	public function franchisee() {
		if ($this->token == $_REQUEST ['token']) {
			$c ['online_name'] = trim ( $_REQUEST ['online_name'] );
			$c ['online_tel'] = trim ( $_REQUEST ['online_tel'] );
			$c ['online_zone'] = trim ( $_REQUEST ['online_zone'] );
			$c ['brand_id'] = $_REQUEST ['brand_id'];
			foreach ( $c as $k => $v ) {
				if (($v !== '') and ($v !== NULL)) {
					$arr [$k] = $v;
				}
			}
			$arr ['token'] = session('token');
			$arr ['uid'] = session ( 'uid' );
			$this->assign ( 'arr', $arr );
			if ($arr ['online_zone'] ) {
				$arr ['online_zone'] = array (
						array (
								'like',
								'%' . $arr ['online_zone'] . '%' 
						) 
				);
			}
            if ($arr ['online_name'] ) {
                $arr ['online_name'] = array (
                    array (
                        'like',
                        '%' . $arr ['online_name'] . '%'
                    )
                );
            }
            if ($arr ['online_tel'] ) {
                $arr ['online_tel'] = array (
                    array (
                        'like',
                        '%' . $arr ['online_tel'] . '%'
                    )
                );
            }
			$count = M ( 'Laundry_online_franchisee' )->where ( $arr )->count ();
			$page = new Page ( $count, 10 );
			$online = M ( 'Laundry_online_franchisee' )->where ( $arr )->limit ( $page->firstRow . ',' . $page->listRows )->order('balance desc')->select ();
			$this->assign ( 'page', $page->show () );
			$this->assign ( 'online', $online );
		}
		$this->display ();
	}
	public function franchiseemanage() {
		if ($this->token == $_GET ['token']) {
			$op = $_GET ['op'] ? $_GET ['op'] : 0;
			$this->assign ( 'op', $op );
			$a ['brand_id'] = $_GET ['brand_id'];
			$this->assign ( 'a', $a );
			if ($op == 1) {
				$w ['id'] = $_GET ['online_id'];
				$w ['brand_id'] = $_GET ['brand_id'];
				$w ['token'] = $this->token;
				$w ['uid'] = session ( 'uid' );
				$this->assign ( 'w', $w );
				$online = M ( 'Laundry_online_franchisee' )->where ( $w )->find ();
				$this->assign ( 'online', $online );
			}
			if (IS_POST) {
				$op = $_POST ['op'] ? $_POST ['op'] : 0;
				$w ['online_id'] = $_POST ['online_id'];
				$data ['brand_id'] = $_POST ['brand_id'];
				$data ['online_name'] = $_POST ['online_name'];
				$data ['online_tel'] = $_POST ['online_tel'];
				$data ['online_zone'] = $_POST ['online_zone'];
				$data ['online_address'] = $_POST ['online_address'];
                $data ['longitude'] = $_POST ['longitude'];
                $data ['latitude'] = $_POST ['latitude'];
				$data ['online_pwd'] = $_POST ['online_pwd'];
				$data ['online_password'] = md5 ( $data ['online_pwd'] );
				$data ['online_login_name'] = $_POST ['online_login_name'];
				$data ['online_addtime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
				$data ['token'] = $this->token;
				$data ['uid'] = session ( 'uid' );
				if ($op == 0) {
					if (M ( 'Laundry_online_franchisee' )->add ( $data )) {
						$this->ajaxReturn ( array (
								'info' => "添加成功",
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=franchisee&token=' . $this->token . '&brand_id=' . $data ['brand_id'] 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => "添加失败",
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=franchiseemanage&token=' . $this->token . '&brand_id=' . $data ['brand_id'] 
						) );
					}
				} elseif ($op == 1) {
					if (M ( 'Laundry_online_franchisee' )->where ( array (
							'token' => $this->token,
							'brand_id' => $data ['brand_id'],
							'id' => $w ['online_id'] 
					) )->save ( $data )) {
						$this->ajaxReturn ( array (
								'info' => "添加成功",
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=franchisee&token=' . $this->token . '&brand_id=' . $data ['brand_id'] 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => "添加失败",
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=franchiseemanage&token=' . $this->token . '&brand_id=' . $data ['brand_id'] 
						) );
					}
				}
			}
		}
		$this->display ();
	}
	public function franchiseedetail() {
		if ($this->token == $_GET ['token']) {
			if (IS_POST) {
				$w ['id'] = $_POST ['online_id'];
				$w ['token'] = $this->token;
				$w ['uid'] = session ( 'uid' );
				$online = M ( 'Laundry_online_franchisee' )->where ( $w )->find ();
				$online ['online_addtime'] = date ( 'Y-m-d H:i:s', $online ['online_addtime'] );
				if ($online) {
					$this->ajaxReturn ( $online, '返回成功！', 1 );
				} else {
					$this->ajaxReturn ( 0, '返回失败', 0 );
				}
			}
		}
	}
	public function del_franchisee() {
		if ($this->token == $_GET ['token']) {
			$w ['id'] = $_GET ['franchisee_id'];
			$data ['brand_id'] = $_GET ['brand_id'];
			if (M ( 'Laundry_online_franchisee' )->where ( $w )->find ()) {
				if (M ( 'Laundry_online_franchisee' )->where ( $w )->delete ()) {
					$this->ajaxReturn ( array (
							'info' => '删除成功',
							'status' => 1,
							'url' => 'index.php?g=User&m=Laundry&a=franchisee&token=' . $this->token . '&brand_id=' . $data ['brand_id'] 
					) );
				} else {
					$this->ajaxReturn ( array (
							'info' => '删除失败',
							'status' => 0,
							'url' => 'index.php?g=User&m=Laundry&a=franchisee&token=' . $this->token . '&brand_id=' . $data ['brand_id'] 
					) );
				}
			}
		}
	}
	/*
	 * Employees
	 */
	public function employees() {
		if ($this->token == $_GET ['token']) {
			$c ['employees_name'] = trim ( $_POST ['employees_name'] );
			$c ['employees_tel'] = trim ( $_POST ['employees_tel'] );
			$c ['employees_zone'] = trim ( $_POST ['employees_zone'] );
			foreach ( $c as $k => $v ) {
				if (($v !== '') and ($v !== NULL)) {
					$arr [$k] = $v;
				}
			}
			$arr ['token'] = $this->token;
			$arr ['uid'] = session ( 'uid' );
			$this->assign ( 'arr', $arr );
			if (isset ( $arr ['employees_zone'] )) {
				$arr ['employees_zone'] = array (
						array (
								'like',
								'%' . $arr ['employees_zone'] . '%' 
						) 
				);
			}
			
			$count = M ( 'Laundry_employees' )->where ( $arr )->count ();
			$page = new Page ( $count, 10 );
			$employees = M ( 'Laundry_employees' )->where ( $arr )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
			$this->assign ( 'page', $page->show () );
			$this->assign ( 'employees', $employees );
		}
		$this->display ();
	}
	public function employeesmanage() {
		if ($this->token == $_GET ['token']) {
			$op = $_GET ['op'] ? $_GET ['op'] : 0;
			$this->assign ( 'op', $op );
			if ($op == 1) {
				$w ['id'] = $_GET ['employees_id'];
				$w ['token'] = $this->token;
				$w ['uid'] = session ( 'uid' );
				$this->assign ( 'w', $w );
				$employees = M ( 'Laundry_employees' )->where ( $w )->find ();
				$this->assign ( 'employees', $employees );
			}
			if (IS_POST) {
				$op = $_POST ['op'] ? $_POST ['op'] : 0;
				$w ['employees_id'] = $_POST ['employees_id'];
				$data ['employees_name'] = $_POST ['employees_name'];
				$data ['employees_pic'] = $_POST ['employees_pic'];
				$data ['employees_tel'] = $_POST ['employees_tel'];
				$data ['employees_zone'] = $_POST ['employees_zone'];
				$data ['employees_address'] = $_POST ['employees_address'];
				$data ['employees_pwd'] = $_POST ['employees_pwd'];
				$data ['employees_password'] = md5 ( $data ['employees_password'] );
				$data ['employees_addtime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
				$data ['token'] = $this->token;
				$data ['uid'] = session ( 'uid' );
				// print_r($data);exit();
				if ($op == 0) {
					if (M ( 'Laundry_employees' )->add ( $data )) {
						$this->ajaxReturn ( array (
								'info' => "添加成功",
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=employees&token=' . $this->token 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => "添加失败",
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=employeesmanage&token=' . $this->token 
						) );
					}
				} elseif ($op == 1) {
					if (M ( 'Laundry_employees' )->where ( array (
							'token' => $this->token,
							'id' => $w ['employees_id'] 
					) )->save ( $data )) {
						$this->ajaxReturn ( array (
								'info' => "编辑成功",
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=employees&token=' . $this->token 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => "编辑失败",
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=employeesmanage&token=' . $this->token . '&id=' . $data ['employees_id'] 
						) );
					}
				}
			}
		}
		$this->display ();
	}
	public function del_employees() {
		if ($this->token == $_GET ['token']) {
			$w ['id'] = $_GET ['employees_id'];
			if (M ( 'Laundry_employees' )->where ( $w )->find ()) {
				if (M ( 'Laundry_employees' )->where ( $w )->delete ()) {
					$this->ajaxReturn ( array (
							'info' => '删除成功',
							'status' => 1,
							'url' => 'index.php?g=User&m=Laundry&a=employees&token=' . $this->token 
					) );
				} else {
					$this->ajaxReturn ( array (
							'info' => '删除失败',
							'status' => 0,
							'url' => 'index.php?g=User&m=Laundry&a=employees&token=' . $this->token 
					) );
				}
			}
		}
		$this->display ();
	}
	/*
	 * Mall
	 */
	public function goods() {
		if ($this->token == $_GET ['token']) {
			$count = M ( 'Laundry_goods' )->where ( array ('token' => $this->token,'uid' => session ( 'uid' ) ) )->count ();
			$page = new Page ( $count, 15 );
			$goods = M ( 'Laundry_goods' )->where ( array('token'=>$this->token) )->order ( 'position desc' )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
			$this->assign ( 'page', $page->show () );
			$a = count ( $goods );
			for($i = 0; $i < $a; $i ++) {
				$goods [$i] ['goods_addtime'] = date ( 'Y-m-d H:i:s', $goods [$i] ['goods_addtime'] );
			}
			$this->assign ( 'goods', $goods );
		}
		$this->display ();
	}
	public function goodsmanage() {
		if ($this->token == $_GET ['token']) {
			$op = $_GET ['op'] ? $_GET ['op'] : 0;
			$this->assign ( 'op', $op );
			$columns = M ( 'Laundry_columns' )->select ();
			$this->assign ( 'columns', $columns );
			if ($op == 1) {
				$w ['id'] = $_GET ['goods_id'];
				$this->assign ( 'w', $w );
				$goods = M ( 'Laundry_goods' )->where ( $w )->find ();
				$this->assign ( 'goods', $goods );
			}
			if (IS_POST) {
				$op = $_POST ['op'] ? $_POST ['op'] : 0;
				$data ['goods_name'] = $_POST ['goods_name'];
				$data ['goods_pic'] = $_POST ['goods_pic'];
				$data ['goods_index_pic'] = $_POST ['goods_index_pic'];
				$data ['goods_brief'] = $_POST ['goods_brief'];
				$data ['position'] = $_POST ['position'];
				$data ['is'] = $_POST['is'];
				$c = M ( 'Laundry_goods' )->where ( array (
						'position' => $data ['position'] 
				) )->find ();
				if ($c) {
					$d ['position'] = 0;
					M ( 'Laundry_goods' )->where ( array (
							'id' => $c ['id'] 
					) )->save ( $d );
					$data ['position'] = $_POST ['position'];
				}
				$data ['columns_id'] = $_POST ['columns_id'];
				$data ['columns_name'] = $_POST ['columns_name'];
				$data ['goods_price'] = $_POST ['goods_price'];
				$data ['market_price'] = $_POST ['market_price'];
				$data ['token'] = $this->token;
				$data ['uid'] = session ( 'uid' );
				$w ['id'] = $_POST ['goods_id'];
				if ($op == 1) {
					$data ['goods_lastedittime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
					if (M ( 'Laundry_goods' )->where ( $w )->save ( $data )) {
						$this->ajaxReturn ( array (
								'info' => '编辑成功',
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=goods&token=' . $this->token 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => '编辑失败',
								'status' => 0,
								'url' => 'index.php?g=User&m=Laundry&a=goodsmanage&token=' . $this->token 
						) );
					}
				} elseif ($op == 0) {
					$data ['goods_addtime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
					$data ['goods_lastedittime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
					if (M ( 'Laundry_goods' )->add ( $data )) {
						$this->ajaxReturn ( array (
								'info' => '添加成功',
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=goods&token=' . $this->token 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => '添加失败',
								'status' => 0,
								'url' => 'index.php?g=User&m=Laundry&a=goodsmanage&token=' . $this->token 
						) );
					}
				}
			}
		}
		$this->display ();
	}
	public function del_goods() {
		if ($this->token == $_GET ['token']) {
			$w ['id'] = $_GET ['goods_id'];
			if (M ( 'Laundry_goods' )->where ( $w )->find ()) {
				if (M ( 'Laundry_goods' )->where ( $w )->delete ()) {
					$this->ajaxReturn ( array (
							'info' => '删除成功',
							'status' => 1,
							'url' => 'index.php?g=User&m=Laundry&a=goods&token=' . $this->token 
					) );
				} else {
					$this->ajaxReturn ( array (
							'info' => '删除失败',
							'status' => 0,
							'url' => 'index.php?g=User&m=Laundry&a=goods&token=' . $this->token 
					) );
				}
			}
		}
	}
	public function columns() {
		if ($this->token == $_GET ['token']) {
			$columns = M ( 'Laundry_columns' )->where(array('token'=>$this->token))->select ();
			$a = count ( $columns );
			for($i = 0; $i < $a; $i ++) {
				$columns [$i] ['columns_addtime'] = date ( 'Y-m-d H:i:s', $columns [$i] ['columns_addtime'] );
                $columns[$i]['columns_intro'] = htmlspecialchars_decode($columns[$i]['columns_intro']);
			}
			$this->assign ( 'columns', $columns );
		}
		$this->display ();
	}
	public function columnsmanage() {
		if ($this->token == $_GET ['token']) {
			$op = $_GET ['op'] ? $_GET ['op'] : 0;
			$this->assign ( 'op', $op );
			if ($op == 1) {
				$w ['id'] = $_GET ['columns_id'];
				$this->assign ( 'w', $w );
				$columns = M ( 'Laundry_columns' )->where ( $w )->find ();
				$this->assign ( 'columns', $columns );
			}
			if (IS_POST) {
				$op = $_POST ['op'] ? $_POST ['op'] : 0;
				$data ['columns_name'] = $_POST ['columns_name'];
				$data ['columns_pic'] = $_POST ['columns_pic'];
				$data ['columns_intro'] = $_POST ['columns_intro'];
				$data ['token'] = $this->token;
				$w ['id'] = $_POST ['columns_id'];
				if ($op == 1) {
					$data ['columns_lastedittime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
					if (M ( 'Laundry_columns' )->where ( $w )->save ( $data )) {
						$this->ajaxReturn ( array (
								'info' => '编辑成功',
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=columns&token=' . $this->token 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => '编辑失败',
								'status' => 0,
								'url' => 'index.php?g=User&m=Laundry&a=columnsmanage&token=' . $this->token 
						) );
					}
				} elseif ($op == 0) {
					$data ['columns_addtime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
					$data ['columns_lastedittime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
					if (M ( 'Laundry_columns' )->add ( $data )) {
						$this->ajaxReturn ( array (
								'info' => '添加成功',
								'status' => 1,
								'url' => 'index.php?g=User&m=Laundry&a=columns&token=' . $this->token 
						) );
					} else {
						$this->ajaxReturn ( array (
								'info' => '添加失败',
								'status' => 0,
								'url' => 'index.php?g=User&m=Laundry&a=columnsmanage&token=' . $this->token 
						) );
					}
				}
			}
		}
		$this->display ();
	}
	public function del_columns() {
		if ($this->token == $_GET ['token']) {
			$w ['id'] = $_GET ['columns_id'];
			if (M ( 'Laundry_columns' )->where ( $w )->find ()) {
				if (M ( 'Laundry_goods' )->where ( array (
						'columns_id' => $w ['id'] 
				) )->find ()) {
					$this->ajaxReturn ( array (
							'info' => '该分类下有商品，不能删除',
							'status' => 0,
							'url' => 'index.php?g=User&m=Laundry&a=columns&token=' . $this->token 
					) );
				} else {
					M ( 'Laundry_columns' )->where ( $w )->delete ();
					$this->ajaxReturn ( array (
							'info' => '删除成功',
							'status' => 1,
							'url' => 'index.php?g=User&m=Laundry&a=columns&token=' . $this->token 
					) );
				}
			}
		}
	}
	/*
	 * Recharge
	 */
	public function recharge() {
		if ($this->token == $_GET ['token']) {
			$w = explode ( '&', __SELF__ );
			$array = array (
					'customer_id' => 'Laundry_customers',
					'b_id' => 'Laundry_brandserviceproviders',
					'franchisee_id' => 'Laundry_online_franchisee' 
			);
			$arr = array (
					'customer_id' => 'customers',
					'b_id' => 'brand',
					'franchisee_id' => 'franchisee' 
			);
			$ar = array (
					'customers' => 'Laundry_customers_liquidity',
					'brand' => 'Laundry_brandserviceproviders_liquidity',
					'franchisee' => 'Laundry_online_franchisee_liquidity' 
			);
			if (! isset ( $w [5] )) {
				$a = explode ( '=', $w [4] );
				$this->assign ( 'a', $a );
				$where ['id'] = $_GET [$a [0]];
				if (array_key_exists ( $a [0], $array )) {
					$x = $array [$a [0]];
					$info = M ( $x )->where ( $where )->find ();
				}
				$u = $arr [$a [0]];
				$g = 0;
			} elseif (isset ( $w [5] ) && isset ( $w [4] )) {
				$a = explode ( '=', $w [4] );
				$b = explode ( '=', $w [5] );
				$this->assign ( 'a', $a );
				$this->assign ( 'b', $b );
				$where ['id'] = $_GET [$b [0]];
				if (array_key_exists ( $b [0], $array )) {
					$x = $array [$b [0]];
					$info = M ( $x )->where ( $where )->find ();
				}
				$u = $arr [$b [0]];
				$g = 1;
				$brand = M ( 'Laundry_brandserviceproviders' )->where ( array (
						'brand_id' => $a [1] 
				) )->find ();
				$this->assign ( 'brand', $brand );
			}
			$this->assign ( 'info', $info );
			$this->assign ( 'x', $x );
			$this->assign ( 'where', $where );
			$this->assign ( 'u', $u );
			$this->assign ( 'g', $g );
			if (IS_POST) {
				$y ['id'] = $_POST ['id'];
				$y ['recharge_amount'] = $_POST ['recharge_amount'];
				if($y ['recharge_amount'] < 0){
				    $this->ajaxReturn(array('info'=>'非法操作！','status'=>2));
				}else{
				    $z = explode ( '+', $y ['id'] );
				    $data ['balance'] = $_POST ['balance'] + $y ['recharge_amount'] + $y ['recharge_amount'] * $_POST ['offers_proportion'];
				    $data ['all_earnings'] = $_POST ['all_earnings'] + $y ['recharge_amount'] * $_POST ['offers_proportion'];
				    $x = $_POST ['x'];
				    $where ['id'] = $_POST ['where'];
				    $u = $_POST ['u'];
				    if (M ( $x )->where ( $where )->save ( $data )) {
				        $k = M ( $x )->where ( $where )->find ();
				        if ($u == 'customers') {
				            $d ['c_name'] = $k ['c_name'];
				            $d ['c_id'] = $k ['id'];
				            $d ['c_tel'] = $k ['c_tel'];
				            $d ['c_flow_amount'] = $y ['recharge_amount'];
				            $d ['c_flow_recorde_addtime'] = date ( 'Y-m-d H:i:s' );
				            $d ['c_flow_recorde_type'] = 0;
				            $d ['offers_proportion'] = $_POST ['offers_proportion'];
				            $d ['c_flow_status'] = 1;
				            $d ['brand_id'] = $k ['brand_id'];
                            $d['c_openid'] = $k['c_openid'];
				        } elseif ($u == 'brand') {
				            $d ['brand_name'] = $k ['brand_name'];
				            $d ['brand_id'] = $k ['id'];
				            $d ['brand_flow_amount'] = $y ['recharge_amount'];
				            $d ['brand_flow_time'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
				            $d ['brand_flow_type'] = 0;
				            $d ['offers_proportion'] = $_POST ['offers_proportion'];
				            $d ['brand_flow_status'] = 1;
				            $d ['brand_earnings'] = $y ['recharge_amount'] * $_POST ['offers_proportion'];
				            M ( $x )->where ( $where )->save ( $k );
				        } elseif ($u == 'franchisee') {
				            $d ['brand_id'] = $k ['brand_id'];
				            $d ['online_id'] = $k ['id'];
				            $d ['online_name'] = $k ['online_name'];
				            $d ['online_flow_amount'] = $y ['recharge_amount'];
				            $d ['online_flow_time'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
				            $d ['online_flow_type'] = 0;
				            $d ['offers_proportion'] = $_POST ['offers_proportion'];
				            $d ['online_flow_status'] = 1;
				            $d ['online_earnings'] = $y ['recharge_amount'] * $_POST ['offers_proportion'];
				        }
				        $d ['balance'] = $data ['balance'];
				        $d ['uid'] = session ( 'uid' );
				        $d ['token'] = $this->token;
				        if ($_POST ['g'] == 0) {
				            $d ['recharge_channel'] = 0;
				        } elseif ($_POST ['g'] == 1) {
				            $v = M ( 'Laundry_brandserviceproviders' )->where ( array (
				                    'id' => $k ['brand_id']
				            ) )->find ();
				            $v ['balance'] = $v ['balance'] - $y ['recharge_amount'] + $y ['recharge_amount'] * $_POST ['offers_proportion'];
				            $v ['all_earnings'] = $v ['all_earnings'] + $y ['recharge_amount'] * $_POST ['offers_proportion'];
				            M ( 'Laundry_brandserviceproviders' )->where ( array (
				            'id' => $k ['brand_id']
				            ) )->save ( $v );
				            $e ['uid'] = session ( 'uid' );
				            $e ['token'] = $this->token;
				            $e ['brand_id'] = $d ['brand_id'];
				            $e ['brand_name'] = $v ['brand_name'];
				            $e ['brand_flow_amount'] = $y ['recharge_amount'];
				            $e ['brand_flow_time'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
				            $e ['brand_flow_type'] = 0;
				            $e ['offers_proportion'] = $_POST ['offers_proportion'];
				            $e ['brand_flow_status'] = 1;
				            $e ['flow_type'] = 2;
				            $e ['recharge_channel'] = 2;
				            $e ['brand_earnings'] = $y ['recharge_amount'] * $_POST ['offers_proportion'];
				            M ( 'Laundry_brandserviceproviders_liquidity' )->add ( $e );
				            $d ['recharge_channel'] = 1;
				        }
				        if (( int ) $y ['recharge_amount'] >= 0) {
				            $d ['flow_type'] = 1;
				        } elseif (( int ) $y ['recharge_amount'] < 0) {
				            $d ['flow_type'] = 2;
				        }
				        M ( $ar [$u] )->add ( $d );
				        $this->ajaxReturn ( array (
				                'info' => "充值成功！",
				                'status' => 1
				        ) );
				    } else {
				        $this->ajaxReturn ( array (
				                'info' => "充值失败！",
				                'status' => 0
				        ) );
				    }
				}
			}
		}
		$this->display ();
	}
}






















