<?php
/**
 * 
 * @author NICK
 *
 */
class 	ServicestoreAction extends UserAction {
	public $token;
	public $userModel;
	public $serviceStoreModel;
	public $wxUserModel;
	public $userInfoData;
	// 	public $wxUserInfoData;
	public $serverOrderModel;
	public $storeId;
	
	public function _initialize() {
	
		parent::_initialize();
		if (!session('?token')) {
			session('token', $_GET['token']);
		}
	
		if (!session('?storeId')) {
			session('storeId', $_GET['storeId']);
		}
		$this->storeId = session('storeId');
		$this->assign('storeId', $this->storeId);
		$this->token = session('token');
		$this->assign('token', $this->token);
		$this->serverOrderModel = M('server_order');
		$this->serviceStoreModel = M("service_store");
		$this->userModel = M('wxuser');
		$this->userInfoData = $this->userModel->where(array('token'=>$this->token))->find();
	
		$this->wxUserModel = M('wxusers');
	}
	
	/*分部管理系统登录*/
	public function login() {
		
		if (IS_POST) {
			$storeName = $_POST['name'];
			$result = $this->serviceStoreModel->where(array('name'=>$storeName))->find();
			if (is_array($result) && !empty($result)) {
				if (md5($_POST['password']) == $result['password']) {
					session('storeId', $result['id']);
					$this->storeId = session('storeId');
					$data = array('status'=>1, 'info'=>'登录成功', 'url'=>'index.php?g=User&m=Servicestore&a=index&token='.$this->token.'&storeId='.$this->storeId);
					$data = $this->encode($data);
					echo $data;
				} else {
					$data = array('status'=>0, 'info'=>'密码错误，登录失败', 'url'=>'index.php?g=User&m=Service&a=login&token='.$this->token);
					$data = $this->encode($data);
					echo $data;
				}
			} else {
				$data = array('status'=>0, 'info'=>'未能查找到对应的用户，登录失败', 'url'=>'index.php?g=User&m=Service&a=login&token='.$this->token);
				$data = $this->encode($data);
				echo $data;
			} 
		} else {
			$allStoreDatas = $this->serviceStoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->select();
			$this->assign('allStoreDatas', $allStoreDatas);
			$this->display();
		}
	}
	
	/*进入分部管理系统显示页*/
	public function index() {
		if (is_array($this->userInfoData) && !empty($this->userInfoData)) {
			$condition['wxuser_id'] = $this->userInfoData['id'];
			$condition['store_id'] = $_GET['storeId'];
			/*产看所有售后服务预约订单数*/
			$serverOrderCount = $this->serverOrderModel->where($condition)->count();
			$this->assign('serverOrderCount', $serverOrderCount);
				
			/*列举售后服务未处理的预约订单数和基本信息*/
			import('ORG.Util.Page');
			$condition['status'] = 4;
			$count = $this->serverOrderModel->where($condition)->count();
			$page = new Page($count, 4);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $this->serverOrderModel->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
			$show = $page->show();
			foreach ($list as $key => $value) {
				$list[$key]['check_time'] = date('Y-m-d H:i:s', $value['check_time']);
			}
			$this->assign('list', $list);
			$this->assign('page', $show);
			$this->assign('serverNoOrderCount', $count);
			/*列举售后服务未处理的预约订单数*/
// 			$condition['status'] = 0;
// 			$serverNoOrderCount = $this->serverOrderModel->where($condition)->count();
// 			$this->assign('serverNoOrderCount', $serverNoOrderCount);
			/*列举售后服务已经处理的预约订单数*/
			$condition['status'] = 1;
			$serverYesOrderCount = $this->serverOrderModel->where($condition)->count();
			$this->assign('serverYesOrderCount', $serverYesOrderCount);
			/*列举售后客户总数*/
			$serverWxuserCount = $this->wxUserModel->where(array('uid'=>$this->userInfoData['id'], 'status'=>1))->count();
			$this->assign('serverWxuserCount', $serverWxuserCount);
		}
		$this->display();
	}
	
	/*订单显示*/
	public function orderInfo() {
	
		if (isset($_GET['type'])) {
			$type = $_GET['type'];
			$this->assign('type', $type);
			$condition['wxuser_id'] = $this->userInfoData['id'];
			
			$condition['store_id'] = $this->storeId;
			if ($_GET['type'] != 2) {
				$condition['status'] = $_GET['type'];
			}
			import('ORG.Util.Page');
			$count = $this->serverOrderModel->where($condition)->count();
			$page = new Page($count, 16);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $this->serverOrderModel->where($condition)->order('check_time')->page($nowPage.','.$page->listRows)->select();
			foreach ($list as $key => $value) {
				$list[$key]['check_time'] = date('Y-m-d H:i:s', $value['check_time']);
				$is_exist = $this->serviceStoreModel->where(array('id' => $value['store_id']))->find();
				if ($is_exist == true) {
					$list[$key]['name'] = $is_exist['name'];
				}
			}
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
		} else {
				
		}
		$this->display();
	}
	

	/*显示所有员工信息*/
	public function showStaff() {
	
		$staffModel = M('staff');
		// 		$staffInfoDatas = $staffModel->join('tp_service_store on tp_staff.belong_id = tp_service_store.id')->select();//这样有比较多的字段相同
		$staffInfoDatas = $staffModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'belong_id'=>$this->storeId))->select();
		foreach ($staffInfoDatas as $key => $value) {
			$storeName = $this->serviceStoreModel->where(array('id'=>$value['belong_id']))->find();
			$staffInfoDatas[$key]['belong_id'] = $storeName['name'];
		}
		$this->assign('staffInfoDatas', $staffInfoDatas);
		$this->display();
	}
	
	/*管理员工（删除和编辑公司职员）*/
	public function managerStaff() {
		$staffModel = M('staff');
		$operation = $_GET['op']?$_GET['op']:0;
		if (IS_POST) {
			$operation = $_POST['op']?$_POST['op']:0;
			$acceptData['name'] = $_POST['name'];
			$acceptData['staff_id'] = $_POST['staffid'];
			$acceptData['staff_type'] = $_POST['display'];
			$acceptData['password'] = md5($_POST['password']);
			$acceptData['age'] = $_POST['age'];
			$acceptData['sex'] = $_POST['sex'];
			$storeResult = $this->serviceStoreModel->where(array('name'=>$_POST['store']));
			if (is_array($storeResult) && !empty($storeResult)) {
				$acceptData['belong_id'] = $storeResult['id'];
			}
			$acceptData['staff_logo'] = $_POST['logourl'];
			$acceptData['telephone'] = $_POST['telephone'];
			$acceptData['wxuser_id'] = $this->userInfoData['id'];
			if (0 == $operation) {
				if ($staffModel->add($acceptData)) {
					$this->success('添加成功','index.php?g=User&m=Servicestore&a=index&token='.$this->token.'&storeId='.$this->storeId);
				} else {
					$this->error('添加失败', 'index.php?g=User&m=Servicestore&a=managerStaff&op=0&token='.$this->token.'&storeId='.$this->storeId);
				}
			} elseif (1 == $operation) {
				$data['id'] = $_POST['id'];
				if ($staffModel->where(array('id'=>$_POST['id']))->save($acceptData)) {
					$this->success('编辑成功','./index.php?g=User&m=Servicestore&a=index&token='.$this->token.'&storeId='.$this->storeId);
				} else {
					$this->error('编辑失败', './index.php?g=User&m=Servicestore&a=managerStaff&op=1&token='.$this->token.'&id='.$data['id'].'&storeId='.$this->storeId);
				}
			}
		}
		if (1 == $operation) {
			$staffId = $_GET['id'];
			$is_exist = $staffModel->where(array('id'=>$staffId))->find();
			if ($is_exist == true) {
				$this->assign('token', $this->token);
				$this->assign("data", $is_exist);
			}
		}
		$storeInfoData = $this->serviceStoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->select();
		foreach ($storeInfoData as $key => $value) {
				
		}
		$this->assign('storeInfoData', $storeInfoData);
		$this->assign('op',$operation);
		$this->display();
	}
	
	/*删除员工*/
	public function delStaff() {
	
		if (IS_POST) {
			$staffId = $_GET['id'];
			$staffModel = M('staff');
			$staffInfoDatas = $staffModel->where(array('id'=>$_GET['id']))->find();
			if (!empty($staffInfoDatas)) {
	
				if ($staffModel->where(array('id'=>$_GET['id']))->delete()) {
					/*删除评价信息*/
					$appraiseModel = M('appraise');
					$appraiseModel->where(array('staff_id'=>$_GET['id']))->delete();
					$this->success('删除成功','./index.php?g=User&m=Servicestore&a=index&token='.$this->token.'&storeId='.$this->storeId);
				} else {
					$this->error('删除失败','./index.php?g=User&m=Servicestore&a=showStaff&token'.$this->token.'&storeId='.$this->storeId);
				}
			} else {
				$this->error('不存在该用户，删除失败','./index.php?g=User&m=Servicestore&a=showStaff&token'.$this->token.'&storeId='.$this->storeId);
			}
		}
		$this->error('非法提交数据，删除失败','./index.php?g=User&m=Servicestore&a=showStaff&token'.$this->token.'&storeId='.$this->storeId);
	}
	
	/*预约订单进行分配*/
	public function orderAllot() {
	
		if (IS_POST) {
			$orderId = $_POST['id'];
			$order_id = $_POST['orderId'];
			$staffName = $_POST['staff'];
			$storeName = $_POST['store'];
			$result = $this->serviceStoreModel->where(array('name'=>$storeName, 'wxuser_id'=>$this->userInfoData['id']))->find();
			if (is_array($result) && !empty($result)) {
				$storeId = $result['id'];
				$staffModel = M('staff');
				$staffData = $staffModel->where(array('belong_id'=>$storeId, 'name'=>$staffName))->find();		
				if (!empty($staffData)) {
					$updateDatas['store_id'] = $storeId;
					$updateDatas['server_staff_id'] = $staffData['id'];
					$updateDatas['server_staff_name'] = $staffData['name'];
					$updateDatas['status'] = 3;
					$updateBack = $this->serverOrderModel->where(array('id'=>$orderId))->save($updateDatas);
					if ($updateBack = true) {
						$this->success('处理成功','index.php?g=User&m=Servicestore&a=orderInfo&token='.$this->token.'&type=4&storeId='.$this->storeId);
					} else {
						$this->error('处理失败','./index.php?g=User&m=Servicestore&a=orderInfo&token'.$this->token.'&type=0&storeId='.$this->storeId);
					}
				} else {
					$this->error('未找到对应的员工','./index.php?g=User&m=Servicestore&a=orderInfo&token'.$this->token.'&type=0&storeId='.$this->storeId);
				}
			} else {
				$this->error('未找到对应的售后分部','./index.php?g=User&m=Servicestore&a=orderInfo&token'.$this->token.'&type=0&storeId='.$this->storeId);
			}
		} else {
			if (isset($_GET['orderId']) && isset($_GET['type'])) {
				if (0 == $_GET['type']) {
					$orderInfoData = $this->serverOrderModel->where(array('id'=>$_GET['orderId']))->find();
					$orderInfoData['check_time'] = date('Y-m-d H:i:s', $orderInfoData['check_time']);
					$this->assign('orderInfoData', $orderInfoData);
					$dealingOrderDatas = $this->serverOrderModel->where(array('store_id'=>$this->storeId, 'status'=>3))->select();
					$staffModel = M('staff');
					if (!empty($dealingOrderDatas)) {
						$conditions['id'] = array();
						$conditions['belong_id'] = $this->storeId;
						foreach ($dealingOrderDatas as $key =>$value) {
							array_push($conditions['id'], array('neq',$value['server_staff_id']));
						}
						$staffDatas = $staffModel->where($conditions)->select();
					} else {
						$staffDatas = $staffModel->where(array('belong_id'=>$this->storeId))->select();
					}
					$storeInfoData = $this->serviceStoreModel->where(array('wxuser_id'=>$this->userInfoData['id'],'id'=>$this->storeId))->select();
					$this->assign('staffDatas', $staffDatas);
					$this->assign('storeInfoData', $storeInfoData);
					$this->display();
					
// 					$orderInfoData = $this->serverOrderModel->where(array('id'=>$_GET['orderId']))->find();
// 					$orderInfoData['check_time'] = date('Y-m-d H:i:s', $orderInfoData['check_time']);
// 					$this->assign('orderInfoData', $orderInfoData);
// // 					$storeInfoData = $this->serviceStoreModel->where(array('wxuser_id'=>$this->userInfoData['id'],'id'=>$orderInfoData['store_id']))->select();
// 					$storeInfoData = $this->serviceStoreModel->where(array('wxuser_id'=>$this->userInfoData['id'],'id'=>$this->storeId))->select();			
// 					$dealingOrderDatas = $this->serverOrderModel->where(array('store_id'=>$this->storeId, 'status'=>4))->select();
// 					$staffModel = M('staff');
// 					if (!empty($dealingOrderDatas)) {
// 						$conditions['id'] = array();
// 						$conditions['belong_id'] = $this->storeId;
// 						foreach ($dealingOrderDatas as $key =>$value) {
// 							array_push($conditions['id'], array('neq',$value['server_staff_id']));
// 						}
// 						$staffDatas = $staffModel->where($conditions)->select();
// 					} else {
// 						$staffDatas = $staffModel->where()->select();
// 					}
// 					$this->assign('staffDatas', $staffDatas);
// 					$this->assign('storeInfoData', $storeInfoData);
// 					$this->display();
				}
			}
		}
	}

	/*根据订单id查看评价appraise或根据售后服务员工号查询评价appraise*/
	public function see() {
	
		if (isset($_GET['type'])) {
			$appraiseModel = M('appraise');
			if (1 == $_GET['type']) {
				if (isset($_GET['orderId'])) {
					$appraiseDatas = $appraiseModel->where(array('order_id'=>$_GET['orderId']))->select();
					if (!empty($appraiseDatas)) {
						$staffInfo = M('staff')->where(array('id'=>$appraiseDatas['staff_id']))->find();
						foreach ($appraiseDatas as $key =>$value) {
							$appraiseDatas[$key]['staff_id'] = $staffInfo['name'];
							$orderInfo= $this->serverOrderModel->where(array('id'=>$_GET['orderId']))->find();
							$appraiseDatas[$key]['order_id'] = $orderInfo['order_id'];
						}						
						$this->assign('appraiseDatas', $appraiseDatas);
					}
				}
			} elseif (2 == $_GET['type']) {
				if (isset($_GET['id'])) {
					$appraiseDatas = $appraiseModel->where(array('staff_id'=>$_GET['id']))->select();
					if (!empty($appraiseDatas)) {
						$staffInfo = M('staff')->where(array('id'=>$_GET['id']))->find();
						foreach ($appraiseDatas as $key =>$value) {
							$appraiseDatas[$key]['staff_id'] = $staffInfo['name'];
							$orderInfo= $this->serverOrderModel->where(array('id'=>$value['order_id']))->find();
							$appraiseDatas[$key]['order_id'] = $orderInfo['order_id'];
						}
						$this->assign('appraiseDatas', $appraiseDatas);
					}
				}
			}
		}
		$this->display();
	}
	
	/*礼品兑换*/
	public function exchange() {		
		if (IS_POST) {
			$vip_name = trim($_POST['title']);
			$offset = trim($_POST['name']);
			$num = intval(trim($_POST['num']));
			if (($offset != 1) && ($offset != 2) && ($offset != 3)) {
				$this->error2('请选择兑换礼品！');
			} else {				
				$carModel = M('service_car');
				$carInfo = $carModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'vip_name'=>$vip_name))->find();
				if (!empty($carInfo)) {
					/*查到存在这个用户，现在就要看看他有多少积分了*/
					$walletModel = M('service_wallet');
					$walletInfo = $walletModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'wxusers_id'=>$carInfo['wxusers_id']))->find();
					if (!empty($walletInfo)) {
						/*积分修改*/
						$scoreModel = M('service_score');
						$scoreData = $scoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->find();
						if (!empty($scoreData)) {
							list($gift_name_1, $gift_name_2, $gift_name_3) = explode(':', $scoreData['gift_name']);
							list($gift_num_1, $gift_num_2, $gift_num_3) = explode(':', $scoreData['gift_num']);
							list($gift_score_1, $gift_score_2, $gift_score_3) = explode(':', $scoreData['gift_score']);
							$gift = array(
									array('gift_name'=>$gift_name_1, 'gift_num'=>$gift_num_1, 'gift_score'=>$gift_score_1),
									array('gift_name'=>$gift_name_2, 'gift_num'=>$gift_num_2, 'gift_score'=>$gift_score_2),
									array('gift_name'=>$gift_name_3, 'gift_num'=>$gift_num_3, 'gift_score'=>$gift_score_3)
							);
							$single_score = intval($gift[($offset-1)]['gift_score']);							
							$consume_score = $num * $single_score;
							$scoreAll = $walletInfo['score']-$consume_score;
							
							$gift[($offset-1)]['gift_num'] = ( intval($gift[($offset-1)]['gift_num']) - $num );
							$walletUpdate = $walletModel->where(array('id'=>$walletInfo['id']))->save(array('score'=>$scoreAll));
							/*修改库存量*/
							foreach ($gift as $key => $value) {
								$gift_num .=$value['gift_num'].":";
							}							
							$gift_num = substr($gift_num, 0, strlen($gift_num)-1);
							$scoreUpdate = $scoreModel->where(array('id'=>$scoreData['id']))->save(array('gift_num'=>$gift_num));
							
							/*写入消费记录中*/
							$consumeModel = M('service_consume');
							$insertData = array(
									'wallet_id'=>$walletInfo['id'], 
									'time'=>time(), 
									'consume_info'=>'兑换了'.$num.$gift[($offset-1)]['gift_name'],
									'consume_score'=>(-$consume_score),
									'extra_score'=>$scoreAll									
							);
							$consumeInsert = $consumeModel->where()->add($insertData);
							
							/*兑换记录*/
							
							if (($walletUpdate !== false) && ($scoreUpdate !== false)) {
								$this->success2('兑换成功',U('Servicestore/index', array('token'=>$this->token, 'storeId'=>$this->storeId)));
							} else {
								$this->error2('服务器繁忙,请稍候再试');
							}
							
						}	
					} else {
						$this->error2('系统中不存在该会员用户！');
					}
				} else {
					$this->error2('系统中不存在该会员用户！');
				}
			}
		} else {
			$giftModel = M('service_score');
			$giftData = $giftModel->where(array('wxuser_id'=>$this->userInfoData['id']))->find();
			if (!empty($giftData)) {
				list($giftName1, $giftName2, $giftName3) = explode(':', $giftData['gift_name']);
				list($giftNum1, $giftNum2, $giftNum3) = explode(":", $giftData['gift_num']);
				list($giftScore1, $giftScore2, $giftScore3) = explode(":", $giftData['gift_score']);
			}
			$giftInfo = array(
							array(
									'gift_name'=>$giftName1,
									'gift_num'=>$giftNum1,
									'gift_score'=>$giftScore1
							),
							array(
									'gift_name'=>$giftName2,
									'gift_num'=>$giftNum2,
									'gift_score'=>$giftScore2
							),
							array(
									'gift_name'=>$giftName3,
									'gift_num'=>$giftNum3,
									'gift_score'=>$giftScore3
							)
						);
			session('gift', $giftInfo);
			$this->assign('data', $giftInfo);			
			$this->display();
		}
	}
	
	/*付款添加积分*/
	public function addScore() {
		
		if (IS_POST) {	

			$vip_name = trim($_POST['title']);			
			$orderId = trim($_POST['name']);
			$payType = trim($_POST['service_type']);
			$exchange_score = trim($_POST['exchange']);
			$serviceInfo = trim($_POST['service']);
			$money = trim($_POST['money']);
			$isOrder = $this->serverOrderModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'order_id'=>$orderId))->find();
			if (!empty($isOrder)) {
				/*付款添加积分*/			
				$walletModel = M('service_wallet');
				$isWxUser = $walletModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'wxusers_id'=>$isOrder['wxusers_id']))->find();
				if (!empty($isWxUser)) {
					/*给用户账户添加积分*/
					$giftModel = M('service_score');
					$giftData = $giftModel->where(array('wxuser_id'=>$this->userInfoData['id']))->find();
					
					if (1 == $payType) {
						$ratio = $giftData['buy_score'];
						$extraScore = (floatval($money)*$ratio)/100 + $isWxUser['score'];
						$extraScore = $extraScore - $exchange_score;
					} elseif (2 == $payType) {
						$ratio = $giftData['pay_float_score'];
						$extraScore = (floatval($money)*$ratio)/100 + $isWxUser['score'];
						$extraScore = $extraScore - $exchange_score;
					}					
					$walletBack = $walletModel->where(array('id'=>$isWxUser['id']))->save(array('score'=>$extraScore));
					
					/*写入用户消费记录*/
					$consumeModel = M('service_consume');
					$insertData = array(
							'wallet_id'=>$isWxUser['id'],
							'consume_money'=>$money,
							'time'=>time(),
							'consume_info'=>$serviceInfo,
							'consume_score'=>floatval($money)*$ratio/100,
							'extra_score'=>$extraScore
					);
					
					$consumeInsert = $consumeModel->where()->add($insertData);
					/*兑换礼品记录*/
					$exchangeModel = M('service_exchange');
					$paymoney = floatval(trim($_POST['money']));					
					$payscore = floatval(trim($_POST['money']))*$ratio/100;
					
					$insertArray = array(
									'wxuser_id'=>$this->userInfoData['id'],
									'wxusers_id'=>$isWxUser['wxusers_id'],
									'pay_money'=>$paymoney,
									'pay_ratio'=>$ratio,
									'pay_score'=>$payscore
								);
					$exchangeBack = $exchangeModel->where()->add($insertArray);
					
					
					
					/*订单维修信息的更改*/
					$updateDatas = array(
									'store_id'=>$this->storeId,
									'server_info'=>$serviceInfo,
									'price'=>floatval($money),
									'status'=>1,
									'server_ok_time'=>time()
								);
					$orderBack = $this->serverOrderModel->where(array('id'=>$isOrder['id']))->save($updateDatas);
					$this->success2('付款成功',U('Servicestore/index', array('token'=>$this->token, 'storeId'=>$this->storeId)));
				} 
			} else {
				$this->error2('不存在该订单号');
			}
		} else {
			$this->display();
		}
	}

	/*查找vip账户信息*/
	public function vipInfo() {
		if (IS_POST) {
			$carModel = M('service_car');
			$vip_name = trim($_POST['title']);
			$carInfo = $carModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'vip_name'=>$vip_name))->find();
			if (!empty($carInfo)) {
				/*查到存在这个用户，现在就要看看他有多少积分了*/
				$walletModel = M('service_wallet');
				$walletInfo = $walletModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'wxusers_id'=>$carInfo['wxusers_id']))->find();
				if (!empty($walletInfo)) {
					$backInfo = array('status'=>100, 'score'=>$walletInfo['score']);
					echo $this->encode($backInfo);
				} else {
					$this->error2('系统中不存在该会员用户！');
				}
			} else {
				$this->error2('系统中不存在该会员用户！');
			}
		}
	}
	
	/*查看礼品积分兑换信息*/
	public function scoreInfo() {
		
		if (IS_POST) {
			$scoreModel = M('service_score');
			$scoreData = $scoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->find();
			if (!empty($scoreData)) {
				list($gift_name_1, $gift_name_2, $gift_name_3) = explode(':', $scoreData['gift_name']);
				list($gift_num_1, $gift_num_2, $gift_num_3) = explode(':', $scoreData['gift_num']);
				list($gift_score_1, $gift_score_2, $gift_score_3) = explode(':', $scoreData['gift_score']);
				$gift = array(
							array('gift_name'=>$gift_name_1, 'gift_num'=>$gift_num_1, 'gift_score'=>$gift_score_1),
							array('gift_name'=>$gift_name_2, 'gift_num'=>$gift_num_2, 'gift_score'=>$gift_score_2),
							array('gift_name'=>$gift_name_3, 'gift_num'=>$gift_num_3, 'gift_score'=>$gift_score_3)
						);
				$offset = trim($_POST['name']);
				if (0 == $offset) {
					$this->error2('请选择兑换礼品！');
				} else {
					$gift_score = $gift[($offset-1)]['gift_score'];
					$gift_num = $gift[($offset-1)]['gift_num'];
					$backInfo = array('status'=>100, 'gift_score'=>$gift_score, 'gift_num'=>$gift_num);
					echo $this->encode($backInfo);
				}
				
			}
		}		
	}
	
	/*查找VIP所有订单信息*/
	public function vipOrder() {
		if (IS_POST) {
			$carModel = M('service_car');
			$vip_name = trim($_POST['title']);			
			$carInfo = $carModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'vip_name'=>$vip_name))->find();
			if (!empty($carInfo)) {
				/*查到存在这个用户，现在就要看看他的订单*/
				$orderInfo = $this->serverOrderModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'wxusers_id'=>$carInfo['wxusers_id'], 'store_id'=>trim($_GET['storeId']), 'status'=>3))->select();
				if (!empty($orderInfo)) {
					$backInfo = array('status'=>100, 'result'=>$orderInfo);
					echo $this->encode($backInfo);
				} else {
					$this->error2('该会员在系统中不存在订单！');
				}
			} else {
				$this->error2('系统中不存在该会员用户！');
			}
		}
	}
	
	/*根据用户名和付账信息，查询相关的信息*/
	public function vipDatas() {
		if (IS_POST) {
			$scoreModel = M('service_score');
			$scoreData = $scoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->find();
			if (!empty($scoreData)) {
				/*积分兑换金钱的比例*/
				if (1 == trim($_POST['type'])) {
					$score_to_money = $scoreData['score_buy_money'];
					$score_to_info = "1000个积分可以抵换".($score_to_money*10)."元";
				} elseif (2 == trim($_POST['type'])) {
					$score_to_money = $scoreData['score_to_money'];
					$score_to_info = "1000个积分可以抵换".($score_to_money*10)."元";
				}
				
				/*查找用户的可用积分*/
				$carModel = M('service_car');
				$vip_name = trim($_POST['name']);
				$carInfo = $carModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'vip_name'=>$vip_name))->find();
				if (!empty($carInfo)) {
					$walletModel = M('service_wallet');
					$walletInfo = $walletModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'wxusers_id'=>$carInfo['wxusers_id']))->find();
					$haveScore = $walletInfo['score'];
				} else {
					$this->error2('系统中不存在该会员用户！');
				}				
				$backInfo = array('status'=>100, 'score_to_money'=>$score_to_money, 'score_to_info'=>$score_to_info, 'score'=>$haveScore);
				echo $this->encode($backInfo);
								
			}
			$this->error2('总部系统还未设置积分制度！');
		}
	}
	
}