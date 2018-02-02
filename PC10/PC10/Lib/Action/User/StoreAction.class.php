<?php
/**
 * 门店管理系统后台
 * @author Nick
 *
 */
class StoreAction extends UserAction {
	public $token;
	public $wxUser;
	public $storeGoods;
	public $goodsClass;
	public $shopdoor;
	public $shopOrders;
	public $storeId;
	
	public function _initialize() {
		parent::_initialize();
		$this->wxUser = M("wxuser");		
		$this->storeGoods = M('shopdoor_goods');
		$this->shopdoor = M('shopdoor');
		$this->goodsClass = M('shopdoor_goods_class');
		$this->shopOrders = M('shopdoor_goods_order');
		$token_open = M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		$this->token = session('token');
		if (!session('?id')) {
			session('id', $_GET['storeId']);
		}
		$this->storeId = session('id');
		$this->assign('token',$this->token);
		$this->assign('storeId', $this->storeId);
	}
	
	/*
	 * 用户登录
	 */
	public function login() {
		
		if (IS_POST) {
			$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
			$is_exist = $this->shopdoor->where(array('id'=>$_POST['name'], 'wxuser_id'=>$wxuser_id['id']))->find();
			if ( md5($_POST['password'])== $is_exist['door_password']) {
// 				session_start();
// 				$_SESSION["id"] = $_POST['name'];
// 				$this->storeId =  $_SESSION['id'];
// 				$this->success('登录成功','./index.php?g=User&m=Store&a=index&token='.$this->token);
				session('id', $_POST['name']);
				$this->storeId = session('id');
				$data = array('status'=>1, 'info'=>'登录成功', 'url'=>'index.php?g=User&m=Store&a=index&token='.$this->token.'&storeId='.$this->storeId);				
				$data = $this->encode($data);
				echo $data;
			} else {
// 				$this->error('登录失败','./index.php?g=User&m=Store&a=login&token'.$this->token);
				$data = array('status'=>0, 'info'=>'登录失败', 'url'=>'index.php?g=User&m=Store&a=login&token='.$this->token);
				$data = $this->encode($data);
				echo $data;
			}
			
		} else {
			$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
			$stores = $this->shopdoor->where(array('wxuser_id'=>$wxuser_id['id']))->select();
			$this->assign('token', $this->token);
			$this->assign('data', $stores);
			$this->display();
		}		
	}
	
	/*
	 * 后台管理系统登录初始页面
	 */	
	public function index() {

		//订单总数
		$timetemp = strtotime('today');
		$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
		$condition = array('wxuser_id'=>$wxuser_id['id'],'order_user'=>array('neq','') ,'order_user_phone'=>array('neq',''),'door_id'=>$this->storeId);
		$orderNum = $this->shopOrders->where($condition)->count();
		
		//今日订单
		$condition['order_ok_time'] = array('between', array($timetemp, ($timetemp + 24*60*60)));
		$todayNum = $this->shopOrders->where($condition)->count();
		
		//未处理订单
		
		unset($condition['order_ok_time']);
		$condition['order_status'] = 0;
		import('ORG.Util.Page');
		$count = $this->shopOrders->where($condition)->count();
		$page = new Page($count, 4);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;		
		$list = $this->shopOrders->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
		
		$show = $page->show();		
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->assign('count', $count);
		
		$this->assign('todayNum', $todayNum);
		$this->assign('orderNum', $orderNum);
		$this->display();				
	}
	
	/*
	 * 分店今日所有订单列出
	 */
	public function sToday() {
		
		$timetemp = strtotime("today");
		$condition['door_id'] = $this->storeId;
		$condition['order_user'] = array('neq','');
		$condition['order_user_phone'] = array('neq','');
		$condition['order_ok_time'] = array('between', array($timetemp, ($timetemp + 24*60*60)));
		import('ORG.Util.Page');
		$count = $this->shopOrders->where($condition)->count();
		$page = new Page($count, 16);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;		
		$list = $this->shopOrders->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
		foreach ($list as $key => $value) {
			$list[$key]['order_ok_time'] = date('Y-m-d H-i-s', $value['order_ok_time']);
		}
		
		$show = $page->show();		
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->assign('count', $count);
		$this->display();
	}
	
	/*
	 * 分店所有订单列出
	 */
	public function sAll() {
		
		$condition['door_id'] = $this->storeId;
		$condition['order_user'] = array('neq','');
		$condition['order_user_phone'] = array('neq','');
		import('ORG.Util.Page');
		$count = $this->shopOrders->where($condition)->count();
		$page = new Page($count, 16);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;		
		$list = $this->shopOrders->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
		foreach ($list as $key => $value) {
			$list[$key]['order_ok_time'] = date('Y-m-d H-i-s', $value['order_ok_time']);
		}
		
		$show = $page->show();		
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->assign('count', $count);
		$this->display();
	}
	
	public function sDealOrder() {
	
		$userData = $this->wxUser->where(array('token'=>$this->token))->find();
		if (!empty($userData)) {
	
			import('ORG.Util.Page');
			$conditions['wxuser_id'] = $userData['id'];
			$conditions['order_status'] = 0;
			$conditions['door_id'] = $this->storeId;
			
			$count = $this->shopOrders->where($conditions)->count();
			$page = new Page($count, 16);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $this->shopOrders->where($conditions)->order('id')->page($nowPage.','.$page->listRows)->select();
			foreach ($list as $key => $value) {
				$list[$key]['order_ok_time'] = date('Y-m-d H-i-s', $value['order_ok_time']);
				$is_exist = $this->shopdoor->where(array('door_id' => $value['door_id']))->find();
				if ($is_exist == true) {
					$list[$key]['door_name'] = $is_exist['door_name'];
				}
			}
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			$this->assign('count', $count);
		}
		$this->display();
	}
	
	/*
	 * 订单处理
	 */	
	public function deal() {
		$condition = array('id'=>$_GET['id']);
		$is_exist = $this->shopOrders->where($condition)->find();
		if ($is_exist == true) {
			$back = $this->shopOrders->where($condition)->setField('order_status',1);
			if ($back == true) {
				$this->success('处理成功','./index.php?g=User&m=Store&a=index&token='.$this->token.'&storeId='.$this->storeId);
			} else {
				$this->error('处理失败','./index.php?g=User&m=Store&a=index&token'.$this->token.'&storeId='.$this->storeId);
				}
		} else {
			$this->error('处理失败','./index.php?g=User&m=Store&a=index&toke='.$this->token.'&storeId='.$this->storeId);
		}		
	}
	
	/*
	 * 查看具体订单详情
	 */
	public function oneDetail() {
		
		$condition = array('id'=>$_GET['id']);
		$is_exist = $this->shopOrders->where($condition)->find();
		$is_exist['order_ok_time'] = date('Y-m-d H:i:s', $is_exist['order_ok_time']);
		if ($is_exist == true) {
			$this->assign('data', $is_exist);
		}
		$this->display();
	}
	
	/*
	 * 商品管理
	 */
	public function manager() {
		
		$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
		$condition = array('wxuser_id'=>$wxuser_id['id'], 'goods_belong'=>$this->storeId);
		import('ORG.Util.Page');
		$count = $this->storeGoods->where($condition)->count();
		$page = new Page($count, 5);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;		
		$list = $this->storeGoods->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
		$show = $page->show();		
		$this->assign('list', $list);
		$this->assign('page', $show);		
		$this->display();
	}
	
	/*
	 * 商品添加和编辑
	 */
	public function goods() {
		$operation = $_GET['op']?$_GET['op']:0; //操作默认情况下是添加分店的状况
		if (IS_POST) {
			$operation = $_POST['op']?$_POST['op']:0;
			$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
			$data['wxuser_id'] = $wxuser_id['id'];			
			$data['goods_name'] = $_POST['name'];
			$data['goods_size'] = $_POST['size'];
			$data['goods_price'] = $_POST['price'];
			$data['goods_inf'] = $_POST['intro'];
			$data['goods_url'] = $_POST['logourl'];
			$data['goods_belong'] = $this->storeId;
			$data['goods_classify'] = $_POST['classify'];
			$data['goods_display'] = $_POST['display'];
			$data['goods_is_deals'] = $_POST['deals'];
			if (0 == $operation) {
				if ($this->storeGoods->add($data)) {
					$this->success('添加成功','./index.php?g=User&m=Store&a=manager&token='.$this->token.'&storeId='.$this->storeId);
				} else {
					$this->error('添加失败', './index.php?g=User&m=Store&a=goods&op=0&token='.$this->token.'&storeId='.$this->storeId);
				}
			} elseif (1 == $operation) {
				$data['id'] = $_POST['id'];
				if ($this->storeGoods->where()->save($data)) {
					$this->success('编辑成功','./index.php?g=User&m=Store&a=manager&token='.$this->token.'&storeId='.$this->storeId);
				} else {
					$this->error('编辑失败', './index.php?g=User&m=Store&a=goods&op=1&token='.$this->token.'&id='.$data['id']);
				}
			}
		}
		//编辑的情况
		if (1 == $operation) {
			$orderId = $_GET['id'];
			$is_exist = $this->storeGoods->where(array('id'=>$orderId))->find();
			if ($is_exist == true) {				
				$this->assign('data', $is_exist);	
			}
		}
		$result = $this->goodsClass->where(array('store_id'=>$this->storeId))->select();
		$this->assign('classData',$result);
		$this->assign('op',$operation);
		$this->display();
	}
	
	/*
	 * 商品删除
	 */
	public function del() {
		
		$goodId = $_GET['id'];
		$is_exist = $this->storeGoods->where(array('id'=>$goodId))->find();
		if ($is_exist == true) {
			$back = $this->storeGoods->where(array('id'=>$goodId))->delete();
			if ($back == true) {
				$this->success('删除成功','./index.php?g=User&m=Store&a=manager&token='.$this->token.'&storeId='.$this->storeId);
			} else {
				$this->error('删除失败','./index.php?g=User&m=Store&a=manager&token'.$this->token.'&storeId='.$this->storeId);
			}
		} else {
			$this->error('删除失败','./index.php?g=User&m=Shopdoor&a=manager&token='.$this->token.'&storeId='.$this->storeId);
		}
	}
	
	/*
	 * 分类管理
	 */
	public function classify() {
		
		$classId = $_GET['id'];
		$operation = $_GET['op']?$_GET['op']:0;				
		if (1 == $operation) {
			$is_exist = $this->goodsClass->where(array('id'=>$_GET['id']))->find();
			if ($is_exist == true) {
				$back = $this->goodsClass->where(array('id'=>$_GET['id']))->delete();
				if ($back == true) {
					$this->success('删除成功','./index.php?g=User&m=Store&a=classify&token='.$this->token.'&storeId='.$this->storeId);
				} else {
					$this->error('删除失败','./index.php?g=User&m=Store&a=classify&token'.$this->token.'&storeId='.$this->storeId);
				}
			} else {
				$this->error('删除失败','./index.php?g=User&m=Store&a=classify&token'.$this->token.'&storeId='.$this->storeId);
			}
		}
		
		$result = $this->goodsClass->where(array('store_id'=>$this->storeId))->select();
		$this->assign('data', $result);
		$this->display();
	}
	
	/*
	 * 添加和编辑分类
	 */
	public function eClassify() {
		
		$operation = $_GET['op']?$_GET['op']:0; //操作默认情况下是添加分店的状况
		if (IS_POST) {
			$operation = $_POST['op']?$_POST['op']:0;
			$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
			$data['wxuser_id'] = $wxuser_id['id'];			
			$data['goods_class_name'] = $_POST['name'];
			$data['store_id'] = $this->storeId;
			$data['goods_class_type'] = $_POST['deals'];
			if (0 == $operation) {				
				if ($this->goodsClass->add($data)) {
					$this->success('添加成功','./index.php?g=User&m=Store&a=classify&token='.$this->token.'&storeId='.$this->storeId);
				} else {
					$this->error('添加失败', './index.php?g=User&m=Store&a=classify&op=0&token='.$this->token.'&storeId='.$this->storeId);
				}
			} elseif (1 == $operation) {
				$data['id'] = $_POST['id'];
				if ($this->goodsClass->where()->save($data)) {
					$this->success('编辑成功','./index.php?g=User&m=Store&a=classify&token='.$this->token.'&storeId='.$this->storeId);
				} else {
					$this->error('编辑失败', './index.php?g=User&m=Store&a=classify&op=1&token='.$this->token.'&id='.$data['id'].'&storeId='.$this->storeId);
				}
			} 
		}
		//编辑的情况
		if (1 == $operation) {
			$orderId = $_GET['id'];
			$is_exist = $this->goodsClass->where(array('id'=>$orderId))->find();
			if ($is_exist == true) {				
				$this->assign('data', $is_exist);	
			}
		} 
		$this->assign('op',$operation);
		$this->display();
	}
		
	/*无限刷新，获取新的订单信息*/
	public function hasNewOrder(){
		
		if (IS_POST) {			
			if ($_POST['type'] == 'all') {								
				$userData = $this->wxUser->where(array('token'=>$this->token))->find();
				if (!empty($userData)) {
					$conditions['wxuser_id'] = $userData['id'];
					$conditions['order_is_read'] = 0;
					$condition['order_user'] = array('neq',null);
		                        $condition['order_user_phone'] = array('neq',null);
					$allOrderResult = $this->shopOrders->where($conditions)->select();
					if (!empty($allOrderResult)) {
						$data = array('status'=>1, 'info'=>'有新的订单！', 'url'=>'./index.php?g=User&m=Shopdoor&a=index&token='.$this->token.'&storeId='.$this->storeId);
						$data = $this->encode($data);
						echo $data;
					}else {
						$data = array('status'=>0, 'info'=>'没有新订单!');
						$data = $this->encode($data);
						echo $data;
					}	
				} else {
					$data = array('status'=>0, 'info'=>'长时间没更新，用户不存在!');
					$data = $this->encode($data);
					echo $data;
				}
			} else {
				$userData = $this->wxUser->where(array('token'=>$this->token))->find();
				
				if (!empty($userData)) {
					$conditions['wxuser_id'] = $userData['id'];
					$conditions['order_is_read'] = 0;
					$conditions['door_id'] = $this->storeId;
                    $condition['order_user'] = array('neq',null);
                    $condition['order_user_phone'] = array('neq',null);
					$allOrderResult = $this->shopOrders->where($conditions)->select();					
					if (!empty($allOrderResult)) {
						foreach ($allOrderResult as $key => $value) {
							$allOrderResult[$key]['order_is_read'] = 1;
							$updateResult = $this->shopOrders->where(array('id'=>$value['id']))->save($allOrderResult[$key]);
						}
// 						foreach ($allOrderResult as $key => $value) {
// 							$updateResult = $this->shopOrders->where()->save($allOrderResult[$key]);
// 						}
						
						$data = array('status'=>1, 'info'=>'有新的订单！', 'url'=>'./index.php?g=User&m=Store&a=index&token='.$this->token.'&storeId='.$this->storeId);
						$data = $this->encode($data);
						echo $data;
					}else {
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
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}