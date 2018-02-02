<?php
/**
 * 
 * @author NICK
 *
 */
class 	ServiceAction extends UserAction {	
	
	public $token;
	public $userModel;
	public $serviceStoreModel;
	public $wxUserModel;
	public $userInfoData;
// 	public $wxUserInfoData;
	public $serverOrderModel;
	
	public function _initialize() {
		
		parent::_initialize();
		if (!session('?token')) {
			session('token', $_GET['token']);
		}
		
		$this->token = session('token');
		$this->assign('token', $this->token);
		$this->serverOrderModel = M('server_order');
		$this->serviceStoreModel = M("service_store");
		$this->userModel = M('wxuser');
		$this->userInfoData = $this->userModel->where(array('token'=>$this->token))->find();
		
		$this->wxUserModel = M('wxusers');
// 		$this->wxUserInfoData = $this->wxUserModel->where(array('uid'=>$this->userInfoData['id'], 'openid'=>$this->openid));
	}
	
	public function index() {
		
		if (is_array($this->userInfoData) && !empty($this->userInfoData)) {
			$condition['wxuser_id'] = $this->userInfoData['id'];
			/*产看所有售后服务预约订单数*/
			$serverOrderCount = $this->serverOrderModel->where($condition)->count();
			$this->assign('serverOrderCount', $serverOrderCount);
			
			/*列举售后服务所有分部的基本信息*/
			import('ORG.Util.Page');
			$count = $this->serviceStoreModel->where($condition)->count();
			$page = new Page($count, 4);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $this->serviceStoreModel->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			  
			/*列举售后服务未处理的预约订单数*/
			$condition['status'] = 0;
			$serverNoOrderCount = $this->serverOrderModel->where($condition)->count();
			$this->assign('serverNoOrderCount', $serverNoOrderCount);
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
				$list[$key]['server_ok_time'] = date('Y-m-d H:i:s', $value['server_ok_time']);
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
	
	/*管理分部（添加和编辑分部）*/
	public function manager() {		

		$operation = $_GET['op']?$_GET['op']:0; //操作默认情况下是添加分店的状况
		if (IS_POST) {
			$operation = $_POST['op']?$_POST['op']:0;
// 			$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
			$data['wxuser_id'] = $this->userInfoData['id'];
			$data['name'] = $_POST['name'];
			$data['adress'] = $_POST['address'];
			$data['phone'] = $_POST['tel'];
			$data['start_time'] = $_POST['starttime'];
			$data['end_time'] = $_POST['endtime'];
			$data['password'] = md5($_POST['password']);
			$data['image_url'] = $_POST['logourl'];
			$data['longitude'] = $_POST['longitude'];
			$data['latitude'] = $_POST['latitude'];
			$data['info'] = $_POST['intro'];
			$data['prompt_info'] = $_POST['actionIntro'];
			if (0 == $operation) {
				if ($this->serviceStoreModel->add($data)) {
					$this->success('添加成功','index.php?g=User&m=Service&a=index&token='.$this->token);
				} else {
					$this->error('添加失败', 'index.php?g=User&m=Service&a=manager&op=0&token='.$this->token);
				}
			} elseif (1 == $operation) {
				$data['id'] = $_POST['id'];
				if ($this->serviceStoreModel->where(array('id'=>$_POST['id']))->save($data)) {
					$this->success('编辑成功','./index.php?g=User&m=Service&a=index&token='.$this->token);
				} else {
					$this->error('编辑失败', './index.php?g=User&m=Service&a=manager&op=1&token='.$this->token.'&id='.$data['id']);
				}
			}
		}
		if (1 == $operation) {
			$orderId = $_GET['id'];
			$is_exist = $this->serviceStoreModel->where(array('id'=>$orderId))->find();
			if ($is_exist == true) {
				$this->assign('token', $this->token);
				$this->assign('longitude', $longitude);
				$this->assign('latitude', $latitude);
				$this->assign("data", $is_exist);
			}
		}
		$this->assign('op',$operation);
		$this->display();
	}
	
	/*删除分部*/
	public function del() {
		$orderId = $_GET['id'];
		$is_exist = $this->serviceStoreModel->where(array('id'=>$orderId))->find();
		if ($is_exist == true) {
			$back = $this->serviceStoreModel->where(array('id'=>$orderId))->delete();
			if ($back == true) {
				$this->success('删除成功','./index.php?g=User&m=Service&a=index&token='.$this->token);
			} else {
				$this->error('删除失败','./index.php?g=User&m=Service&a=index&token'.$this->token);
			}
		} else {
			$this->error('删除失败','./index.php?g=User&m=Service&a=index&token='.$this->token);
		}
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
			$storeResult = $this->serviceStoreModel->where(array('name'=>$_POST['store'], 'wxuser_id'=>$this->userInfoData['id']))->find();
			if (is_array($storeResult) && !empty($storeResult)) {
				$acceptData['belong_id'] = $storeResult['id'];
			}			
			$acceptData['staff_logo'] = $_POST['logourl'];
			$acceptData['telephone'] = $_POST['telephone'];
			$acceptData['wxuser_id'] = $this->userInfoData['id'];
			if (0 == $operation) {
				if ($staffModel->add($acceptData)) {
					$this->success('添加成功','index.php?g=User&m=Service&a=index&token='.$this->token);
				} else {
					$this->error('添加失败', 'index.php?g=User&m=Service&a=managerStaff&op=0&token='.$this->token);
				}
			} elseif (1 == $operation) {
				$data['id'] = $_POST['id'];
				if ($staffModel->where(array('id'=>$_POST['id']))->save($acceptData)) {
					$this->success('编辑成功','./index.php?g=User&m=Service&a=index&token='.$this->token);
				} else {
					$this->error('编辑失败', './index.php?g=User&m=Service&a=managerStaff&op=1&token='.$this->token.'&id='.$data['id']);
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
	
	/*显示所有员工信息*/
	public function showStaff() {
		
		
		$staffModel = M('staff');
// 		$staffInfoDatas = $staffModel->join('tp_service_store on tp_staff.belong_id = tp_service_store.id')->select();//这样有比较多的字段相同
		$staffInfoDatas = $staffModel->where(array('wxuser_id'=>$this->userInfoData['id']))->select();
		foreach ($staffInfoDatas as $key => $value) {
			$storeName = $this->serviceStoreModel->where(array('id'=>$value['belong_id']))->find();
			$staffInfoDatas[$key]['belong_id'] = $storeName['name'];
		}
		$this->assign('staffInfoDatas', $staffInfoDatas);
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
					$this->success('删除成功','./index.php?g=User&m=Service&a=index&token='.$this->token);
				} else {
					$this->error('删除失败','./index.php?g=User&m=Service&a=showStaff&token'.$this->token);
				}
			} else {
				$this->error('不存在该用户，删除失败','./index.php?g=User&m=Service&a=showStaff&token'.$this->token);
			}			
		}
		$this->error('非法提交数据，删除失败','./index.php?g=User&m=Service&a=showStaff&token'.$this->token);
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
				$updateDatas['store_id'] = $storeId;
// 				$updateDatas['server_staff_id'] = $staffData['id'];
// 				$updateDatas['server_staff_name'] = $staffData['name'];
				$updateDatas['status'] = 4;
				$updateBack = $this->serverOrderModel->where(array('id'=>$orderId))->save($updateDatas);
				if ($updateBack == true) {
					$this->success('处理成功','index.php?g=User&m=Service&a=orderInfo&token='.$this->token.'&type=0');
				} else {
					$this->error('处理失败','./index.php?g=User&m=Service&a=orderInfo&token'.$this->token.'&type=0');
			}
				
				
// 				$staffModel = M('staff');
// 				$staffData = $staffModel->where(array('belong_id'=>$storeId, 'name'=>$staffName))->find();
// 				if (!empty($staffData)) {
// 					$updateDatas['store_id'] = $storeId;
// 					$updateDatas['server_staff_id'] = $staffData['id'];
// 					$updateDatas['server_staff_name'] = $staffData['name'];
// 					$updateDatas['status'] = 4;
// 					$updateBack = $this->serverOrderModel->where(array('id'=>$orderId))->save($updateDatas);
// 					if ($updateBack == true) {
// 						$this->success('处理成功','index.php?g=User&m=Service&a=orderInfo&token='.$this->token.'&type=0');
// 					} else {
// 						$this->error('处理失败','./index.php?g=User&m=Service&a=orderInfo&token'.$this->token.'&type=0');
// 					}
// 				} else {
// 					$this->error('未找到对应的员工','./index.php?g=User&m=Service&a=orderInfo&token'.$this->token.'&type=0');
// 				}				
			} else {
				$this->error('未找到对应的售后分部','./index.php?g=User&m=Service&a=orderInfo&token'.$this->token.'&type=0');
			}		
		} else {
			if (isset($_GET['orderId']) && isset($_GET['type'])) {
				if (0 == $_GET['type']) {
					$orderInfoData = $this->serverOrderModel->where(array('id'=>$_GET['orderId']))->find();
// 					$orderInfoData['check_time'] = date('Y-m-d H:i:s', $orderInfoData['check_time']);
					$this->assign('orderInfoData', $orderInfoData);
					$storeInfoData = $this->serviceStoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->select();
					$this->assign('storeInfoData', $storeInfoData);
					$this->display();
				}				
			}			
		}
	}
	
	/*根据门店名查找所有的符合条件的员工*/
	public function storeStaff() {
		
		if (IS_POST) {
			$storeName = $_POST['store'];
			$time = $_POST['time'];
			$result = $this->serviceStoreModel->where(array('name'=>$storeName))->find();
			if (is_array($result) && !empty($result)) {
				$storeId = $result['id'];
				$dealingOrderDatas = $this->serverOrderModel->where(array('store_id'=>$storeId, 'status'=>3))->select();
				$staffModel = M('staff');
				if (!empty($dealingOrderDatas)) {
					$conditions['id'] = array();
					$conditions['belong_id'] = $storeId;
					foreach ($dealingOrderDatas as $key =>$value) {
						array_push($conditions['id'], array('neq',$value['server_staff_id']));
					}
					$staffDatas = $staffModel->where($conditions)->select();
// 					print_r($staffDatas);
// 					exit();
				} else {
					$staffDatas = $staffModel->where(array('belong_id'=>$storeId))->select();
					if (empty($staffDatas)) {
						$backData = array('status'=>2, 'info'=>'这个门店目前还没有员工，请添加员工哦！！！', 'num'=>count($staffDatas));
						echo $this->encode($backData);
						exit();
					}
				}
				$backData = array('status'=>1, 'num'=>count($staffDatas));
				echo $this->encode($backData);		
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

	public function hasNewOrder(){
		if (IS_POST) {
			if (!empty($this->userInfoData)) {
				$conditons['wxuser_id'] = $this->userInfoData['id'];
				$conditons['order_is_read'] = 0;
				$isNewOrder = $this->serverOrderModel->where($conditons)->select();
				if (!empty($isNewOrder)) {
					foreach ($isNewOrder as $key => $value) {
						$isNewOrder[$key]['order_is_read'] = 1;
						$this->serverOrderModel->where(array('id'=>$value['id']))->save($isNewOrder[$key]);
					}
					$newsCount = count($isNewOrder);
					$data = array('status'=>1, 'info'=>'有新的订单！', 'num'=>$newsCount, 'url'=>'./index.php?g=User&m=Service&a=index&token='.$this->token);
					$data = $this->encode($data);
					echo $data;
				} else {
					$data = array('status'=>0, 'info'=>'没有新订单!');
					$data = $this->encode($data);
					echo $data;
				}
			} else {
				$data = array('status'=>0, 'info'=>'长时间没更新，用户不存在!');
				$data = $this->encode($data);
				echo $data;
			}			
		}
	}
	
	public function setScore() {
		if (IS_POST) {
			$scoreModel = M('service_score');
			$insertDatas = array(
							'wxuser_id'=>$this->userInfoData['id'],							
							'start_score'=>trim($_POST['start_score']),
							'charge_score'=>trim($_POST['charge_score']),
							'preferential'=>trim($_POST['title']),
							'pre_score'=>floatval(trim($_POST['pre_score'])),
							'depict_score'=>trim($_POST['appraise_score']),
							'appraise_score'=>floatval(trim($_POST['appraise'])),
							'buy_score'=>floatval(trim($_POST['buy_score'])),
							'payment_score'=>floatval(trim($_POST['payment_score'])),
							'pay_float_score'=>floatval(trim($_POST['float_score'])),
							'gift_name'=>trim($_POST['gift_1']).":".trim($_POST['gift_2']).":".trim($_POST['gift_3']),
							'gift_num'=>trim($_POST['num_1']).":".trim($_POST['num_2']).":".trim($_POST['num_3']),
							'gift_score'=>trim($_POST['gift_score_1']).":".trim($_POST['gift_score_2']).":".trim($_POST['gift_score_3']),
							'sign_min'=>trim($_POST['sign_min']),
							'sign_max'=>trim($_POST['sign_max']),
							'score_to_money'=>trim($_POST['score_to_money']),
							'score_buy_money'=>trim($_POST['score_buy_money'])
						);
			$isScore = $scoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->find();
			if (!empty($isScore)) {
				$insertBackInfo = $scoreModel->where(array('id'=>$isScore['id']))->save($insertDatas);
				if ($insertBackInfo == true) {
					$this->success2('更改成功',U('Service/index', array('token'=>$this->token)));
				} else {
					$this->error2('服务器繁忙,请稍候再试');
				}								
			} else {
				$insertBackInfo = $scoreModel->where()->add($insertDatas);
				if ($insertBackInfo == true) {
					$this->success2('添加成功',U('Service/index'));
				} else {
					$this->error2('服务器繁忙,请稍候再试');
				}
			}
			
		} else {
			$scoreModel = M('service_score');
			$findData = $scoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->find();
			
			list($findData['gift_name_1'], $findData['gift_name_2'], $findData['gift_name_3']) = explode(':', $findData['gift_name']);
			list($findData['gift_num_1'], $findData['gift_num_2'], $findData['gift_num_3']) = explode(':', $findData['gift_num']);
			list($findData['gift_score_1'], $findData['gift_score_2'], $findData['gift_score_3']) = explode(':', $findData['gift_score']);
			
			$this->assign('data', $findData);
			$this->display();
		}
		
	}
}