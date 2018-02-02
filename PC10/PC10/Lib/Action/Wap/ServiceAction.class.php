<?php
/**
 * WAP端
 * @author NICK
 *
 */
class ServiceAction extends BaseAction{

	public $token;
	public $openid;
	public $userModel;
	public $userDatas;
	public $wxUserModel;
	public $wxUserDatas;
	public $orderModel;
	public $storeModel;
	public $staffModel;
	public $appraiseModel;
	public $cid;
	
	
	public function __construct() {
		parent::_initialize();
		
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if(!strpos($agent,"MicroMessenger")) {
			//	echo '此功能只能在微信浏览器中使用';exit;
		}
		
		if ((!session('?token')) || (!session('?openid'))) {
			session('token', $_REQUEST['token']);
			session('openid', $_REQUEST['openid']);
		}
		if (!session('?cid')) {
			session('cid', $_REQUEST['cid']);
		}
		$this->cid = session('cid');		
		$this->token = session('token');
		$this->openid = session('openid');
				
		$this->orderModel = M('server_order');		
		$this->storeModel = M('service_store');		
		$this->staffModel = M('staff');	
		$this->appraiseModel = M('appraise');		
				
		$this->userModel = M('wxuser');
		$this->userDatas = $this->userModel->where(array('token'=>$this->token))->find();
		
		$this->wxUserModel = M('wxusers');
		$this->wxUserDatas = $this->wxUserModel->where(array('uid'=>$this->userDatas['id'], 'openid'=>$this->openid))->find();
		
		$this->assign('cid', $this->cid);
		$this->assign('token', $this->token);
		$this->assign('openid', $this->openid);
	}
	
	/*Wap端初始进入，显示是否进行绑定*/
	public function index() {		
		$style = $_GET['style']?$_GET['style']:0;
		
		if (0 == $style) {
			if (IS_POST) {											
				if (($_POST['token'] != $this->token) && ($_POST['openid'] != $this->openid)) {					
					$backInfo = array('status'=>0, 'info'=>'非法数据请求');
					echo $this->encode($backInfo);
				} else {
					/*员工第一登录，进行绑定*/
					if ($_POST['Identity'] == 'cj') {
						$queryDatas = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'staff_id'=>$_POST['username']))->find();
						if(!empty($queryDatas)) {
							if (md5($_POST['password'] == $queryDatas['password'])) {								
								$this->staffModel->where(array('id'=>$queryDatas['id']))->save(array('wxusers_id'=>$this->wxUserDatas['id'], 'is_bind'=>0));
								$arrayData = array('status'=>100, 'info'=>'登录成功', 'url'=>'index.php?g=Wap&m=Service&a=index&token='.$this->token.'&openid='.$this->openid);
								echo $this->encode($arrayData);
								exit();
							} 
						} 
						$arrayData = array('status'=>101, 'info'=>'登录失败', 'url'=>'index.php?g=Wap&m=Service&a=index&token='.$this->token.'&openid='.$this->openid);
						echo $this->encode($arrayData);
					} elseif ($_POST['Identity'] == 'cp') {
						/*目前是针对一个人一辆车的情况*/
						$serviceCarModel = M('service_car');
						$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
						if (empty($isExistData)) {
							$insertDatas['car_models'] = $_POST['carname'];
							$insertDatas['car_licence'] = $_POST['cpnum'];
							$insertDatas['car_username'] = $_POST['name'];
							$insertDatas['user_phone'] = $_POST['phone'];
							$insertDatas['wxusers_id'] = $this->wxUserDatas['id'];
							$insertDatas['wxuser_id'] = $this->userDatas['id'];
							$insertDatas['vip_name'] = $_POST['name'].rand(1000, 10000);
							$insertDatas['is_bind'] = 0;
							$insertInfo = $serviceCarModel->where()->add($insertDatas);
							if ($insertInfo == true) {
								session('cid', $insertInfo);
								$this->cid = session('cid');
								/*第一次绑定，成为会员，给用户一定的积分*/								
								$walletModel = M('service_wallet');
								$haveWalletInfo = $walletModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
								/*查找一个积分的规则数据*/
								$scoreModel = M('service_score');
								$scoreInfo = $scoreModel->where(array('wxuser_id'=>$this->userDatas['id']))->find();
								/*添加积分*/								
								if (empty($haveWalletInfo)) {
									$walletData = array(
										'wxuser_id'=>$this->userDatas['id'],
										'wxusers_id'=>$this->wxUserDatas['id'],
										'score'=>$scoreInfo['start_score'],
										'vip_rank'=>0,
										'money'=>0,
										'phone'=>$_POST['phone'],
										'name'=>$_POST['name']
									);
									$walletInfo = $walletModel->where()->add($walletData);
								}
								
								$backInfo = array('status'=>1, 'info'=>'绑定成功','url'=>'index.php?g=Wap&m=Service&a=index&style=2&token='.$this->token.'&openid='.$this->openid.'&cid='.$insertInfo);
								echo $this->encode($backInfo);
							} else {
								$backInfo = array('status'=>2, 'info'=>'绑定失败','url'=>'index.php?g=Wap&m=Service&a=index&token='.$this->token.'&openid='.$this->openid);
								echo $this->encode($backInfo);
							}
						} else {
							/**/
							$insertDatas['car_models'] = $_POST['carname'];
							$insertDatas['car_licence'] = $_POST['cpnum'];
							$insertDatas['car_username'] = $_POST['name'];
							$insertDatas['user_phone'] = $_POST['phone'];
							$insertDatas['wxusers_id'] = $this->wxUserDatas['id'];
							$insertDatas['wxuser_id'] = $this->userDatas['id'];
							$insertDatas['vip_name'] = $_POST['name'].rand(1000, 10000);
							$insertDatas['is_bind'] = 0;
							$insertInfo = $serviceCarModel->where(array('id'=>$isExistData['id']))->save($insertDatas);
						}
					}									
				}			
			} else {				
				/*只能先判断是否有车系*/
				$serviceCarModel = M('service_car');
				$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				if (!empty($isExistData)) {
					$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'is_bind'=>0))->find();
					if (!empty($isExistData)) {
						$this->assign('data', $isExistData);
						$this->assign('style', 2);
						$this->display('tpl/Wap/default/Service_index01.html');
					} else {
						$this->display('tpl/Wap/default/Service_index_2.html');
						exit();
					}				
					
				} else {
					
					$isStaff = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'is_bind'=>0))->find();					
					if (!empty($isStaff)) {
						$this->display('tpl/Wap/default/Service_staff.html');
						exit();
					} else {
						$this->display('tpl/Wap/default/Service_index_2.html');
					}										
				}
			}			
		} elseif (2 == $_GET['style']) {
			$serviceCarModel = M('service_car');	
			$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'id'=>$this->cid))->find();
			$this->assign('wxusers_img', $this->wxUserDatas['fakeid']);
			$this->assign('data', $isExistData);		
			$this->assign('style', 2);
			$this->display('tpl/Wap/default/Service_index01.html');
		} else {
			/*用户没有绑定车牌号，跳过*/
			$this->assign('style', 1);
			$this->display('tpl/Wap/default/Service_index01.html');
		}
	}

	/*添加车子型号（这个是针对于以前跳过然后再添加车型）*/
	public function addCar() {		
		if (IS_POST) {
			if (($_POST['token'] != $this->token) && ($_POST['openid'] != $this->openid)) {					
				$backInfo = array('status'=>0, 'info'=>'非法数据请求');
				echo $this->encode($backInfo);
			} else {					
				$serviceCarModel = M('service_car');
				$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'car_licence'=>$_POST['cpnum']))->find();					
				if (empty($isExistData)) {		
					$insertDatas['car_models'] = $_POST['carname'];
					$insertDatas['car_licence'] = $_POST['cpnum'];
					$insertDatas['car_username'] = $_POST['name'];
					$insertDatas['wxusers_id'] = $this->wxUserDatas['id'];
					$insertDatas['wxuser_id'] = $this->userDatas['id'];
					$insertInfo = $serviceCarModel->where()->add($insertDatas);
					if ($insertInfo == true) {
						session('cid', $insertInfo);
						$backInfo = array('status'=>1, 'info'=>'绑定成功','url'=>'index.php?g=Wap&m=Service&a=index&style=2&token='.$this->token.'&openid='.$this->openid.'&cid='.$insertInfo);
						echo $this->encode($backInfo);
					} else {
						$backInfo = array('status'=>2, 'info'=>'插入数据失败','url'=>'index.php?g=Wap&m=Service&a=index&token='.$this->token.'&openid='.$this->openid);
						echo $this->encode($backInfo);
					}
				} else {
					session('cid', $isExistData['id']);
					$backInfo = array('status'=>3, 'info'=>'用户已经绑定','url'=>'index.php?g=Wap&m=Service&a=index&style=2&token='.$this->token.'&openid='.$this->openid.'&cid='.$isExistData['id']);
					echo $this->encode($backInfo);
				}
			}
		} else {
			$this->display();
		}
	}
	
	/*显示个人车子信息*/
	public function showInfo() {		
		
		if (1 == $_GET['type']) { 
			/*显示个人汽车信息*/
			$serviceCarModel = M('service_car');
			$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'id'=>session('cid')))->find();
			if (!empty($isExistData)) {
				$jsonData = array(
							'car_name'=>$isExistData['car_models'],
							'cpnum' =>$isExistData['car_licence'],
							'uname'	=>$isExistData['car_username'],
							'phone'	=>$isExistData['user_phone'],
							'fnum'	=>$isExistData['car_frame'],
							'sptime'=>$isExistData['regist_time'],
							'dltime'=>$isExistData['license_time'],
							'dtype'	=>$isExistData['license_type'],
							'bxtime'=>$isExistData['insurance_time']
				);
				/*有些是汽车行业的，需要领证时间，下次只要在前端做一个判断，就可以控制车牌号是否显示*/
				$this->assign('jsonData', $this->encode($jsonData));
				$this->assign('wxusers_img', $this->wxUserDatas['headimgurl']);
				$this->assign('data', $isExistData);
				$this->assign('time', date('Y-m-d'));
			}			
			$this->display();
		} elseif (2 == $_GET['type']) {
			/*显示个人预约之类的信息*/
			$serviceCarModel = M('service_car');
			$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'id'=>session('cid')))->find();
			$this->assign('wxusers_img', $this->wxUserDatas['fakeid']);
			$this->assign('data', $isExistData);
			$this->display('tpl/Wap/default/Service_me.html');
		}
		
	}
	
	/*修改车牌号*/
	public function editCar() {		
		$type = $_GET['type']?$_GET['type']:0;
		
		if (0 == $type) {
			if (IS_POST) {	
				/*个人汽车信息的一个修改保存数据*/			
				$serviceCarModel = M('service_car');
				$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'id'=>$this->cid))->find();
				if (!empty($isExistData)) {
					$acceptData = array(
							'car_models'=>$_POST['carname'],
							'car_licence' =>$_POST['cpnum'],
							'car_username'=>$_POST['czname'],
							'user_phone'=>$_POST['phone'],
							'car_frame'	=>$_POST['fnum'],
							'regist_time'=>$_POST['sptime'],
// 							'license_time'=>$_POST['dltime'],
// 							'license_type'	=>$_POST['dtype'],
							'insurance_time'=>$_POST['bxtime']
					);										
					$insertInfo = $serviceCarModel->where(array('id'=>$this->cid))->save($acceptData);
					if ($insertInfo !== false) {						
						$walletModel = M('service_wallet');
						$backInfo = array('status'=>100, 'info'=>'修改成功','url'=>'index.php?g=Wap&m=Service&a=index&style=2&token='.$this->token.'&openid='.$this->openid);
						echo $this->encode($backInfo);
					} else {
						$backInfo = array('status'=>1, 'info'=>'插入数据失败','url'=>'index.php?g=Wap&m=Service&a=showInfo&type=1&token='.$this->token.'&openid='.$this->openid);
						echo $this->encode($backInfo);
					}
				} else {
					$backInfo = array('status'=>2, 'info'=>'非法用户插入','url'=>'index.php?g=Wap&m=Service&a=showInfo&type=1&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backInfo);
				}
			}				
		} elseif (2 == $type) {
			/*修改车牌号*/
			$serviceCarModel = M('service_car');
			if (IS_POST) {
				$updata['car_licence'] = $_POST['cpnum'];
				$updateInfo = $serviceCarModel->where(array('id'=>$this->cid))->save($updata);
				if ($updateInfo !== false) {
					$backData =array('status'=>100, 'info'=>'修改成功', 'url'=>'index.php?g=Wap&m=Service&a=index&style=2&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);
				}
			} else {
				$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'id'=>session('cid')))->find();
				$this->assign('cpnum',$isExistData['car_licence']);
				$this->display('tpl/Wap/default/Service_editCar_2.html');
			}						
		}
	}
	
	/*添加和修改额外信息*/
	public function extraInfo() {
	if (IS_POST) {
		/*额外信息修改保存*/
		$serviceCarModel = M('service_car');
			$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'id'=>$this->cid))->find();
			if (!empty($isExistData)) {
				$acceptData = array(
						'car_models'=>$_POST['carname'],
						'car_licence' =>$_POST['car_licence'],
						'car_username'=>$_POST['czname'],
						'user_phone'=>$_POST['phone'],
						'car_frame'=>$_POST['fnum'],
						'car_style'	=>$_POST['carStyle'],
						'qq'=>$_POST['qq'],
						'email'=>$_POST['mail']
				);
				$insertInfo = $serviceCarModel->where(array('id'=>$this->cid))->save($acceptData);
				if ($insertInfo !== false) {
					$backInfo = array('status'=>100, 'info'=>'修改成功','url'=>'index.php?g=Wap&m=Service&a=index&style=2&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backInfo);
				} else {
					$backInfo = array('status'=>1, 'info'=>'插入数据失败','url'=>'index.php?g=Wap&m=Service&a=showInfo&type=1&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backInfo);
				}
			} else {
				$backInfo = array('status'=>2, 'info'=>'非法用户插入','url'=>'index.php?g=Wap&m=Service&a=showInfo&type=1&token='.$this->token.'&openid='.$this->openid);
				echo $this->encode($backInfo);
			}			
		} else {
			/*额外信息的展示*/
			$serviceCarModel = M('service_car');
			$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'id'=>session('cid')))->find();
			if (!empty($isExistData)) {
				$jsonData = array(
							'car_name'=>$isExistData['car_models'],
							'cpnum' =>$isExistData['car_licence'],
							'uname'	=>$isExistData['car_username'],
							'phone'	=>$isExistData['user_phone'],
							'fnum'	=>$isExistData['car_frame'],
							'carStyle'=>$isExistData['car_style'],
							'qq'	=>$isExistData['qq'],
							'mail'=>$isExistData['email']
				);
				/*有些是汽车行业的，需要领证时间，下次只要在前端做一个判断，就可以控制车牌号是否显示*/
				$this->assign('jsonData', $this->encode($jsonData));
				$this->assign('data', $isExistData);
				$this->assign('time', date('Y-m-d'));
			}			
			$this->display();
		}
		
	}
	
	
	/*我的消息展示*/
	public function mynews() {
		$type = $_GET['type']?$_GET['type']:1;
		if (1 == $type) {
			//这里的信息数据不清楚
			$this->assign('opType', 1);
			$this->display();
		} elseif (2 == $type) {
			$this->assign('opType', 2);
			$this->display();
		}
	}
	
	/*我的钱包*/
	public function wallet() {
		$walletModel = M('service_wallet');
		$walletInfo = $walletModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();		
		if (!empty($walletInfo) && ($walletInfo['money'] != 0)) {
			$consumeModel = M('service_consume');
			$consumeInfo = $consumeModel->where(array('wallet_id'=>$walletInfo['id']))->find();
			$this->assign('walletInfo', $walletInfo);
			$this->assign('consumeInfo', $consumeInfo);
			$this->assign('isNull', 2);
			$this->display();
		} else {
			$this->assign('isNull', 1);
			$this->display();
		}		
	}
	
	/*捡钱活动*/
	public function activity() {
		$this->assign('isNull', 1);
		$this->display();
	}
	
	/*我的顾问*/
	public function kefu() {
		$serviceInfo = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'staff_type'=>0))->select();
		$this->assign('serviceInfo', $serviceInfo);
		$this->display();
	}
	
	/*我的养修*/
	public function appoint() {
		$type = $_GET['type']?$_GET['type']:1;
		if (1 == $type) {
			$appointInfo = $this->orderModel->order('check_time')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'staff_type'=>0))->select();
			if (!empty($appointInfo)) {
				foreach ($appointInfo as $key=>$value) {
					$appointInfo[$key]['check_time'] = date('Y-m-d H:i:s', $value['check_time']);
				}
				$this->assign('appointExit', 2);
				$this->assign('appointInfo', $appointInfo);
				$this->display();
			} else {
				$this->assign('appointExit', 1);
				$this->display();
			}
			
		} elseif (2 == $type) {
			//$this->display('tpl/Wap/default/Service_appoint_2.html');
		} elseif (3 == $type) {
			
			if (IS_POST) {
				$orderId = date('ymdHis').rand(1000,2000);
				$carModel = M('service_car');
				$server_type = $_POST['project']?$_POST['project']:0;
				$carInfo = $carModel->where(array('id'=>$this->cid))->find();
				$insertData = array(
					'order_id' => $orderId,
					'wxuser_id'=>$this->userDatas['id'],
					'server_info'=>$_POST[curkm],
					'status'=> 0,
					'appoint_time'=>$_POST['yytime'],
					'appoint_store'=>$_POST['store'],
					'server_type'=>$server_type,
					'consume_type'=> 0,
					'wxusers_id'=>$this->wxUserDatas['id'],
					'order_user'=>$_POST['name'],
					'order_user_tel'=>$_POST['phone'],
					'server_car_no'=>$carInfo['car_licence'],
					'check_time'=>time()
				);
				/**
				 * 这里将添加积分机制
				 */ 
				
				/*发送消息*/
				$notichcontent = $this->userDatas['nickname']."您好".$this->wxUserDatas['name']."已经收到您的订单,订单编号:".$orderId.=",我们将全心为你分配员工进行服务";
				$postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
				$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
				$data = $this->api_notice_increment($url,http_build_query($postdata));
				
				if ($this->orderModel->where()->add($insertData)) {
					$backData = array('status'=>100, 'info'=>'预约成功', 'url'=>'index.php?g=Wap&m=Service&a=appoint&type=1&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);
				} else {
					$backData = array('status'=>1, 'info'=>'预约失败', 'url'=>'index.php?g=Wap&m=Service&a=appoint&type=3&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);
				}
			} else {
				/*查询销售顾问*/
				$serviceInfo = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'staff_type'=>0))->select();
				$this->assign('serviceInfo', $serviceInfo);
				/*查询用户信息*/
				$serviceCarModel = M('service_car');
				$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'id'=>$this->cid))->find();
				$this->assign('carData', $isExistData);
				$this->display('tpl/Wap/default/Service_appoint_3.html');
			}			
			
		} elseif (4 == $type) {
			if (IS_POST) {
				$longitude = $_POST['long'];
				$latitude = $_POST['Lati'];
				/*计算最近的门店*/
				$storeInfo = $this->storeModel->where(array('wxuser_id'=>$this->userDatas['id']))->select();
				$nearestStores = array();
				if (is_array($storeInfo) && !empty($storeInfo)){
					foreach ($storeInfo as $key => $value) {
						$distance = $this->GetDistance($latitude, $longitude, $value['latitude'], $value['longitude']);
						$nearestStores[$distance] = $value;
					}
				}
				$minDistance = 10000000000;
				foreach ($nearestStores as $k => $val) {
					if ($k <= $minDistance) {
						$minDistance = $k;
					}
				}				
				$nearestStore = $nearestStores[$minDistance];
				$backData = array('status'=>100, 'info'=>'获取成功', 'name'=>$nearestStore['name'], 'address'=>$nearestStore['adress']);
				echo $this->encode($backData);
		
			}
		}
		
	}
	/*计算最短距离*/
	function GetDistance($lat1, $lng1, $lat2, $lng2)
	{
		$EARTH_RADIUS = 6378.137;
		// 		$radLat1 = rad($lat1);
		$radLat1 = $lat1* 3.1415926535898/180.0;
		//echo $radLat1;
		$radLat2 = $lat2*3.1415926535898/180.0;
		$a = $radLat1 - $radLat2;
		$b = ($lng1* 3.1415926535898 / 180.0) - ($lng2* 3.1415926535898 / 180.0);
		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
		$s = $s *$EARTH_RADIUS;
		$s = round($s * 10000) / 10000;
		return $s;
	}
	
	
	/*我的续保询价*/
	public function secure() {
		
		if (1 == $_GET['type']) {
			$this->display();
		} elseif (2 == $_GET['type']) {
			$this->display('tpl/Wap/default/Service_secure_2.html');
		} elseif (3 == $_GET['type']) {
			$this->display('tpl/Wap/default/Service_secure_3.html');
		} else {
			$this->display();
		}
		
	}
	
	/*我的救援*/
	public function help() {
		if (1 == $_GET['type']) {
			$this->display();
		} else {
			
		}
		
	}
	
	/*我要投诉*/
	public function complain() {
		$type = $_GET['type']?$_GET['type']:1;
		if (1 == $type) {
			if (IS_POST) {
				$insertDatas = array(
					'wxuser_id'=>$this->userDatas['id'],
					'wxusers_id'=>$this->wxUserDatas['id'],
					'complain_info'=>$_POST['reason'],
					'complain_username'=>$_POST['name'],
					'complain_phone'=>$_POST['tel'],
					'complain_time'=>time()
				);
				$complainModel = M('service_complain');
				$backInfo = $complainModel->where()->add($insertDatas);
				if ($backInfo == true) {
					$backData =array('status'=>100, 'info'=>'提交成功,我们将快速处理!!!', 'url'=>'index.php?g=Wap&m=Service&a=complain&type=2&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);
				} else {
					$backData =array('status'=>1, 'info'=>'提交失败', 'url'=>'index.php?g=Wap&m=Service&a=complain&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);
				}
			} else {
				$serviceCarModel = M('service_car');
				$isExistData = $serviceCarModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				$this->assign('data', $isExistData);
				$this->display();
			}			
		} elseif (2 == $type) {
			$complainModel = M('service_complain');
			$complainInfo = $complainModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->order('complain_time')->select();
			if (!empty($complainInfo)) {
				$this->assign('isnull', 0);
				foreach ($complainInfo as $key=>$value) {
					$complainInfo[$key]['complain_time'] = date('Y-m-d', $value['complain_time']);
				}
				$this->assign('data', $complainInfo);
			} else {
				$this->assign('isnull', 1);
			}			
			$this->display('tpl/Wap/default/Service_complain_2.html');
		} 
		
	}
	
	/*违章查询*/
	public function rulesQuery() {
		$this->display();
	}
	
	/*服务点评*/
	public function appraise() {
		$type = $_GET['type']?$_GET['type']:1;	
		if (1 == $type) {
			if (IS_POST) {		
				$isStaff = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'staff_id'=>$_POST['staffId']))->find();
				$isOrder = $this->orderModel->where(array('wxuser_id'=>$this->userDatas['id'], 'order_id'=>$_POST['orderId']))->find();
				if ((!empty($isStaff)) && (!empty($isOrder))) {		
					$isAppraise = $this->appraiseModel->where(array('wxuser_id'=>$this->userDatas['id'], 'staff_id'=>$isStaff['id'], 'order_id'=>$isOrder['id']))->find();
// 					$isAppraise = $this->appraiseModel->where(array('wxuser_id'=>$this->userDatas['id'], 'staff_id'=>$_POST['staffId'], 'order_id'=>$isOrder['id']))->find();
					if (empty($isAppraise)) {
						$insertDatas = array(
									'wxuser_id'=>$this->userDatas['id'],
									'staff_id'=>$isStaff['id'],
// 									'staff_id'=>$_POST['staffId'],
									'wxusers_id'=>$this->wxUserDatas['id'],
									'order_id'=>$isOrder['id'],
									'score'=>$_POST['score'],
									'appraise_info'=>$_POST['appriseInfo'],
									'appraise_name'=>$isOrder['order_user']						
								);
						if($this->appraiseModel->where()->add($insertDatas)){
							$updateBackInfo = $this->orderModel->where(array('id'=>$isOrder['id']))->save(array('order_is_appraise'=>1));
							if ($updateBackInfo !== false) {
								
								/*这里到时候需要添加积分机制*/
								$scoreModel = M('service_score');
								$isScore = $scoreModel->where(array('wxuser_id'=>$this->userDatas['id']))->find();
								if (!empty($isScore)) {
									$walletModel = M('service_wallet');
									$isWallet = $walletModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
									if (!empty($isWallet)) {
										if (trim($_POST['appriseInfo']) != null) {
											$score1 = $isScore['depict_score'];
										}
										$score = $isWallet['score'] + $isScore['appraise_score'] + $score1;
										$walletBack = $walletModel->where(array('id'=>$isWallet['id']))->save(array('score'=>$score));
									}
								}
								
								$backData =array('status'=>100, 'info'=>'评价成功', 'url'=>'index.php?g=Wap&m=Service&a=index&style=2&token='.$this->token.'&openid='.$this->openid);
								echo $this->encode($backData);
							} else {
								$backData =array('status'=>1, 'info'=>'评价失败', 'url'=>'index.php?g=Wap&m=Service&a=appoint&style=2&token='.$this->token.'&openid='.$this->openid);
								echo $this->encode($backData);
							}						
						} else {
							$backData =array('status'=>3, 'info'=>'评价失败', 'url'=>'index.php?g=Wap&m=Service&a=appoint&style=2&token='.$this->token.'&openid='.$this->openid);
							echo $this->encode($backData);
						}
					} else {
						$backData =array('status'=>2, 'info'=>'该订单已评价', 'url'=>'index.php?g=Wap&m=Service&a=index&style=2&token='.$this->token.'&openid='.$this->openid);
						echo $this->encode($backData);
					}				
				} else {
					$backData =array('status'=>4, 'info'=>'评价失败', 'url'=>'index.php?g=Wap&m=Service&a=appoint&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);
				}					
			} else {
				if (isset($_GET['staffId']) && isset($_GET['orderId'])){
					$isStaff = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'staff_id'=>trim($_GET['staffId'])))->find();
					$isOrder = $this->orderModel->where(array('wxuser_id'=>$this->userDatas['id'], 'order_id'=>trim($_GET['orderId'])))->find();
// 					$isOrder = $this->orderModel->where(array('wxuser_id'=>$this->userDatas['id'], 'order_id'=>'1408151714421129'))->find();
					if ((!empty($isStaff)) && (!empty($isOrder))) {
						$isAppraise = $this->appraiseModel->where(array('wxuser_id'=>$this->userDatas['id'], 'staff_id'=>$isStaff['id'], 'order_id'=>$isOrder['id']))->find();
						if (!empty($isAppraise)) {
							$this->assign('isnull', 1);
							$this->assign('legal', 0);
							$this->assign('orderId', $_GET['orderId']);
							$this->assign('staffId', $_GET['staffId']);
							$this->display();
						} else {
							$this->assign('isnull', 0);
							$this->assign('orderId', $_GET['orderId']);
							$this->assign('staffId', $_GET['staffId']);
							$this->display();
						}		
					} else {
						$this->assign('isnull', 1);
						$this->assign('legal', 1);
						$this->assign('orderId', $_GET['orderId']);
						$this->assign('staffId', $_GET['staffId']);
						$this->display();
					}												
				}
			}
		} elseif (2 == $type) {
			$orderInfo = $this->orderModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'order_is_appraise'=>0, 'status'=>1))->select();
			if (empty($orderInfo)) {
				$this->assign('appraiseExit', 0);
			} else {
				$this->assign('appraiseExit', 1);
				$this->assign('data', $orderInfo);
			}
			
			$this->display('tpl/Wap/default/Service_appraise_2.html');
		}	
		
		
		
	}
	
	/*取消订单*/
	public function cancel() {
		if (IS_POST) {
			$isExist = $this->orderModel->where(array('id'=>$_POST['id']))->find();
			if (!empty($isExist)) {
				$updataInfo = $this->orderModel->where(array('id'=>$_POST['id']))->save(array('status'=>2));
				if ($updataInfo !== false) {
					$orderId = $isExist['order_id'];
					/*发送信息*/
					$notichcontent = $this->userDatas['nickname']."您好".$this->wxUserDatas['name']."已经收到您的订单取消信息,订单编号:".$orderId.=",谢谢您对我们的认可！！";
					$postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
					$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";
					$data = $this->api_notice_increment($url,http_build_query($postdata));
					
					$backData = array('status'=>100, 'info'=>'取消成功', 'url'=>'index.php?g=Wap&m=Service&a=appoint&type=1&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);
					exit();
				}
			}
		} 
			$backData = array('status'=>1, 'info'=>'取消失败', 'url'=>'index.php?g=Wap&m=Service&a=appoint&type=1&token='.$this->token.'&openid='.$this->openid);
			echo $this->encode($backData);		
	}
	
	public function detail() {
		if (isset($_GET['id'])) {
			$orderInfo = $this->orderModel->where(array('id'=>$_GET['id']))->find();
			if (!empty($orderInfo)) {
				$this->assign('data', $orderInfo);
				$this->assign('status', $orderInfo['status']);
				$this->display();
			} else {
				
			}
		}
		
	}
	
	/**
	 * staff 相关信息
	 */
	public function staffInfo() {
		$staffDatas = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		if (!empty($staffDatas)) {
			$storeInfo = $this->storeModel->where(array('id'=>$staffDatas['belong_id']))->find();
			$staffDatas['belong_id'] = $storeInfo['name'];
			$this->assign('storeInfo', $storeInfo);
			$this->assign('data', $staffDatas);
		}
		$this->display();
	}
	
	/*staff manager 工作安排*/
	public function staffMan() {
		$staffInfo = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		$staffManInfo = $this->orderModel->where(array('wxuser_id'=>$this->userDatas['id'], 'server_staff_id'=>$staffInfo['staff_id'], 'status'=>3))->order('appoint_time')->select();
		if (!empty($staffInfo)) {
			$this->assign('isnull',2);
			$this->assign('data', $staffManInfo);
			$this->display();
		} else {
			$this->assign('isnull',1);
			$this->display();
		}		
	}
	
	/* staff news 信息*/
	public function staffNews() {
		$this->display();
	}
	
	/*staff judge估价*/
	public function  staffEval() {
		$this->display();
	}
	
	/*员工修改密码*/
	public function staffEdit() {
		if (IS_POST) {
			$oldpassword = trim($_POST['oldpassword']);
			$newpassword = trim($_POST['newpassword']);
			$checkpassword = trim($_POST['checkpassword']);
			$isStaff = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($isStaff)) {
				if (md5($oldpassword) != $isStaff['password']) {
					$backData = array('status'=>1, 'info'=>'密码错误', 'url'=>'index.php?g=Wap&m=Service&a=staffEdit&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);	
				} else {
					if ($newpassword != $checkpassword) {
						$backData = array('status'=>2, 'info'=>'密码错误', 'url'=>'index.php?g=Wap&m=Service&a=staffEdit&token='.$this->token.'&openid='.$this->openid);
						echo $this->encode($backData);
					} else {
						$password = md5($newpassword);
						$updataBack = $this->staffModel->where(array('id'=>$isStaff['id']))->save(array('password'=>$password));
						if ($updataBack !== false) {
							$backData = array('status'=>100, 'info'=>'修改成功', 'url'=>'index.php?g=Wap&m=Service&a=staffInfo&token='.$this->token.'&openid='.$this->openid);
							echo $this->encode($backData);
						} else {
							$backData = array('status'=>3, 'info'=>'修改失败', 'url'=>'index.php?g=Wap&m=Service&a=staffEdit&token='.$this->token.'&openid='.$this->openid);
							echo $this->encode($backData);
						}
					}
				}
			}
		} else {
			$this->display();
		}
	}
	
	/*员工生成临时二维码*/
	public function staffCode() {
		
		$isStaff = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		$parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$isStaff['id'].'}}}';
		
		/*获取access_token*/
		$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
		if($api){
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
			$json = json_decode(file_get_contents($url_get));
			$access_token = $json->access_token;
			$imgSource = $this->creatTicket($access_token, $parament);
		}	
// 		print_r($imgSource);
// 		exit();	
// 		print_r($imgSource['header']['url']);
// 		exit();
		$this->assign('imgUrl', $imgSource['header']['url']);
		$this->display();
	}
	
	/*The two-dimensional code  BY NICK  */
	public function creatTicket($token, $parament) {
		 
		/*发送数据到微信服务器端并获取数据*/
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
		$result = $this->api_notice_increment($url, $parament);
		$jsonInfo = json_decode($result, true);
		$ticket = $jsonInfo['ticket'];

		/*根据ticket获取图片资源*/
		$url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";	
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpInfo = curl_getinfo($ch);
		return array_merge(array('body'=>$package), array('header'=>$httpInfo));
	}
	
	
	/*扫描二维码，获取订单消息*/
	public function get2code() {
		
		
	}
	
	public function judge() {
		if (IS_POST) {
			$isStaff = $this->staffModel->where(array('wxuser_id'=>$this->userDatas['id'], 'staff_id'=>$_POST['staffId']))->find();
			$isOrder = $this->orderModel->where(array('wxuser_id'=>$this->userDatas['id'], 'order_id'=>$_POST['orderId']))->find();
			if ((!empty($isStaff)) && (!empty($isOrder))) {
				$isAppraise = $this->appraiseModel->where(array('wxuser_id'=>$this->userDatas['id'], 'staff_id'=>$isStaff['id'], 'order_id'=>$isOrder['id']))->find();
				if (empty($isAppraise)) {
					$insertDatas = array(
							'wxuser_id'=>$this->userDatas['id'],
							'staff_id'=>$isStaff['id'],
							'wxusers_id'=>$this->wxUserDatas['id'],
							'order_id'=>$isOrder['id'],
							'score'=>$_POST['score'],
							'appraise_info'=>$_POST['appriseInfo'],
							'appraise_name'=>$isOrder['order_user']
					);
					if($this->appraiseModel->where()->add($insertDatas)){
						$updateBack = $this->orderModel->where(array('id'=>$isOrder['id']))->save(array('order_is_appraise'=>1));
						if ($updateBack !== false) {
		
							/*这里到时候需要添加积分机制*/
							$scoreModel = M('service_score');
							$isScore = $scoreModel->where(array('wxuser_id'=>$this->userDatas['id']))->find();
							if (!empty($isScore)) {
								$walletModel = M('service_wallet');
								$isWallet = $walletModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
								if (!empty($isWallet)) {
									$score = $isWallet['score'] + $isScore['appraise_score'];
									$walletBack = $walletModel->where(array('id'=>$isWallet['id']))->save(array('score'=>$score));
								}
							}
		
							$backData =array('status'=>100, 'info'=>'评价成功', 'url'=>'index.php?g=Wap&m=Service&a=judge&token='.$this->token.'&openid='.$this->openid);
							echo $this->encode($backData);
						} else {
							$backData =array('status'=>1, 'info'=>'评价失败', 'url'=>'index.php?g=Wap&m=Service&a=judge&token='.$this->token.'&openid='.$this->openid);
							echo $this->encode($backData);
						}
					} else {
						$backData =array('status'=>3, 'info'=>'评价失败', 'url'=>'index.php?g=Wap&m=Service&a=judge&token='.$this->token.'&openid='.$this->openid);
						echo $this->encode($backData);
					}
				} else {
					$backData =array('status'=>2, 'info'=>'该订单已评价', 'url'=>'index.php?g=Wap&m=Service&a=judge&token='.$this->token.'&openid='.$this->openid);
					echo $this->encode($backData);
				}
			} else {
				$backData =array('status'=>4, 'info'=>'评价失败', 'url'=>'index.php?g=Wap&m=Service&a=judge&token='.$this->token.'&openid='.$this->openid);
				echo $this->encode($backData);
			}
		} else {
				$orderId = date('ymdHis').rand(1000,2000);
				$AddDatas = array(						
								'order_id'=>$orderId,
								'wxuser_id'=>$this->userDatas['id'],
								'status'=>1,
								'appoint_time'=>date('Y年m月d日 H:i:s', time()),
								'check_time'=>time(),
								'consume_type'=>0,
								'server_ok_time'=>time(),
								'order_is_read'=>1
							);
				$this->orderModel->add($AddDatas);
				$this->assign('orderId', $orderId);
				$this->display();
		}
	}
	
	
	/*我要签到*/
	public function sign() {
		$today = strtotime("today");
		$tomorrow = $today + 24*60*60;
		if (IS_POST) {
			/*查找积分设置规则*/
			$scoreModel = M('service_score');
			$scoreInfo = $scoreModel->where(array('wxuser_id'=>$this->userDatas['id']))->find();
			$score = rand($scoreInfo['sign_min'], $scoreInfo['sign_max']);
			/*添加签到积分*/
			$walletModel = M('service_wallet');
			$walletInfo = $walletModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();						
			if (!empty($walletInfo)) {
				$score = $walletInfo['score'] + $score;
				$walletUpdate = $walletModel->where(array('id'=>$walletInfo['id']))->save(array('score'=>$score));
				if ($walletInfo !== false) {
					/*标注今天已经签到*/
					/*先判断是否签到了*/
					$signModel = M('member_card_sign');
					$conditions['token'] = $this->token;
					$conditions['wecha_id'] = $this->openid;
					$conditions['sign_time'] = array('between',array($today,$tomorrow));
					$isSign = $signModel->where($conditions)->find();
						
					if (!empty($isSign)) {
						echo $this->encode(array('error'=>100, 'info'=>'你已经签到了'));
					} else {
						$signUpdate = $signModel->where()->add(array('token'=>$this->token,'wecha_id'=>$this->openid,'sign_time'=>time(),'is_sign'=>1));
						if ($signUpdate == true) {
							echo $this->encode(array('status'=>100, 'info'=>'签到成功', 'url'=>'index.php?g=Wap&m=Service&a=sign&token='.$this->token.'&openid='.$this->openid));
						}
					}			
				} 
			} else {
				echo $this->encode(array('error'=>100, 'info'=>'签到失败'));
			}
			
		} else {
			$walletModel = M('service_wallet');
			$walletInfo = $walletModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($walletInfo)) {
				$this->assign('score', $walletInfo['score']);
			}
			
			$signModel = M('member_card_sign');
			$conditions['token'] = $this->token;
			$conditions['wecha_id'] = $this->openid;
			$conditions['sign_time'] = array('between',array($today,$tomorrow));
			$isSign = $signModel->where($conditions)->find();
			
			if (!empty($isSign)) {
				$this->assign('sign', 1);
			} else {
				$this->assign('sign', 0);
			}
			
			$this->display();
		}
	}
	
	/*注销*/
	public function logout() {
		$wxusersId = $this->wxUserDatas['id'];
		$wxuserId = $this->userDatas['id'];
		$findBack = M('service_car')->where(array('wxuser_id'=>$wxuserId, 'wxusers_id'=>$wxusersId))->find();
		if (!empty($findBack)) {
			$delBack = M('service_car')->where(array('id'=>$findBack['id']))->save(array('is_bind'=>1));
			if ($delBack) {
				$this->success2('注销成功', 'index.php?g=Wap&m=Service&a=delRe&token='.$this->token.'&openid='.$this->openid);
			} else {
				$this->error2('注销失败');
			}
		} else {
			$findBack = M('staff')->where(array('wxuser_id'=>$wxuserId, 'wxusers_id'=>$wxusersId))->find();
			if (!empty($findBack)) {
				$delBack = M('staff')->where(array('id'=>$findBack['id']))->save(array('is_bind'=>1));
				if ($delBack) {
					$this->success2('注销成功', 'index.php?g=Wap&m=Service&a=delRe&token='.$this->token.'&openid='.$this->openid);
				} else {
					$this->error2('注销失败');
				}
			}
		}
	}
	
	public function delRe() {
		$this->display();
	}
}