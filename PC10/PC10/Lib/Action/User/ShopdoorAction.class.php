<?php
/**
 *
 * 微门店
 * @author NICK
 *
 */
class ShopdoorAction extends UserAction {
	public $token;
	public $shopDoor;
	public $wxUser;
	public $shopOrder;

	public function _initialize() {
		parent::_initialize();
		$this->wxUser = M("wxuser");
		$this->shopDoor = M("shopdoor");
		$this->shopOrder = M("shopdoor_goods_order");
		$this->token = session('token');
		$this->assign('token',$this->token);
	}

	/*
	 * 默认情况下，展现门店所有订单信息
	 */
	public function index() {

		$conditions['token'] = $this->token;
		$wxuser_id = $this->wxUser->where($conditions)->find();
		if (!empty($wxuser_id)) {
			$condition['wxuser_id'] = $wxuser_id['id'];
			//所有订单数
			$orderNum = $this->shopOrder->where($condition)->count();
			//列举所有分店
			import('ORG.Util.Page');
			$count = $this->shopDoor->where($condition)->count();
			$page = new Page($count, 4);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $this->shopDoor->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);


			$timetemp = strtotime("today");
			$condition['order_ok_time'] = array('between', array($timetemp, ($timetemp + 24*60*60)));
			//今日订单数
			$dayNum = $this->shopOrder->where($condition)->count();
			//所有分店未处理的订单数
			$notDealOrderNum = $this->shopOrder->where(array('wxuser_id'=>$wxuser_id['id'], 'order_status'=>0))->count();
		}
		$this->assign('orderNum',$orderNum);
		$this->assign('dayNum',$dayNum);
		$this->assign('notDealOrderNum',$notDealOrderNum);
        $this->assign('token',$conditions['token']);
		$this->display();
	}

	/*
	 * 今日所有订单基本详情
	 */
	public function sToday() {

		$timetemp = strtotime("today");
		$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
		$condition['wxuser_id'] = $wxuser_id['id'];
		$condition['order_ok_time'] = array('between', array($timetemp, ($timetemp + 24*60*60)));
		import('ORG.Util.Page');
		$count = $this->shopOrder->where($condition)->count();
		$page = new Page($count, 16);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$list = $this->shopOrder->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
		foreach ($list as $key => $value) {
			$list[$key]['order_ok_time'] = date('Y-m-d H-i-s', $value['order_ok_time']);
			$is_exist = $this->shopDoor->where(array('id' => $value['door_id']))->find();
			if ($is_exist == true) {
				$list[$key]['door_name'] = $is_exist['username'];
			}
		}

		$show = $page->show();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->assign('count', $count);
		$this->display();
	}

	/*
	 * 所有订单基本详情
	 */
	public function sAll() {
		$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
		$condition['wxuser_id'] = $wxuser_id['id'];

		import('ORG.Util.Page');
		$count = $this->shopOrder->where($condition)->count();
		$page = new Page($count, 16);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$list = $this->shopOrder->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
		foreach ($list as $key => $value) {
			$list[$key]['order_ok_time'] = date('Y-m-d H-i-s', $value['order_ok_time']);
			$is_exist = $this->shopDoor->where(array('id' => $value['door_id']))->find();
			if ($is_exist == true) {
				$list[$key]['username'] = $is_exist['username'];
			}
		}

		$show = $page->show();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->assign('count', $count);
		$this->display();
	}

	/*
	 *显示所用分店的未处理的订单信息
	 */
	public function sDealOrder() {

		$userData = $this->wxUser->where(array('token'=>$this->token))->find();
		if (!empty($userData)) {

			import('ORG.Util.Page');
			$conditions['wxuser_id'] = $userData['id'];
			$conditions['order_status'] = 0;
			$count = $this->shopOrder->where($conditions)->count();
			$page = new Page($count, 16);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $this->shopOrder->where($conditions)->order('door_id desc')->page($nowPage.','.$page->listRows)->select();
			foreach ($list as $key => $value) {
				$list[$key]['order_ok_time'] = date('Y-m-d H-i-s', $value['order_ok_time']);
				$is_exist = $this->shopDoor->where(array('id' => $value['door_id']))->find();
				if ($is_exist == true) {
					$list[$key]['username'] = $is_exist['username'];
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
	 * 所有会员基本详情
	 */
	public function showvip() {


	}

	/*
	 * 删除分店
	 */
	public function del() {
		$orderId = $_GET['id'];
		$is_exist = $this->shopDoor->where(array('id'=>$orderId))->find();
		if ($is_exist == true) {
			$back = $this->shopDoor->where(array('id'=>$orderId))->delete();
			if ($back == true) {
				$this->success('删除成功','./index.php?g=User&m=Shopdoor&a=index&token='.$this->token);
			} else {
				$this->error('删除失败','./index.php?g=User&m=Shopdoor&a=index&token'.$this->token);
			}
		} else {
			$this->error('删除失败','./index.php?g=User&m=Shopdoor&a=index&token='.$this->token);
		}
	}

	/*
	 *添加分店和编辑分店综合起来
	 */
    public function manager() {

        $operation = $_GET['op']?$_GET['op']:0; //操作默认情况下是添加分店的状况
        if (IS_POST) {
            $operation = $_POST['op']?$_POST['op']:0;
            $wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
            $data['wxuser_id'] = $wxuser_id['id'];
            $data['username'] = $_POST['name'];
            $data['door_adress'] = $_POST['address'];
            $data['door_phone'] = $_POST['tel'];
            $data['door_start_time'] = $_POST['starttime'];
            $data['door_end_time'] = $_POST['endtime'];
            $data['door_pwd'] = $_POST['password'];
            $data['password'] = md5($_POST['password']);
            $data['door_image_url'] = $_POST['logourl'];
            $data['door_longitude'] = $_POST['longitude'];
            $data['door_latitude'] = $_POST['latitude'];
            $data['door_info'] = $_POST['intro'];
            $data['door_prompt_info'] = $_POST['actionIntro'];
            $data['token'] = session('token');

            $host = $this->shopDoor->where(array('username'=>$data['username'],'token'=>$data['token']))->find();

                if (0 == $operation) {
                    if($host){
                        $this->error('对不起，用户名已存在，请重新填写！', 'index.php?g=User&m=Shopdoor&a=manager&op=0&token='.$this->token);
                    }else{
                        if ($this->shopDoor->add($data)) {
                            $this->success('添加成功','index.php?g=User&m=Shopdoor&a=index&token='.$this->token);
                        } else {
                            $this->error('添加失败', 'index.php?g=User&m=Shopdoor&a=manager&op=0&token='.$this->token);
                        }
                    }
                } elseif (1 == $operation) {
                    $data['id'] = $_POST['id'];
                    if ($this->shopDoor->where(array('id'=>$_POST['id']))->save($data)) {
                        $this->success('编辑成功','./index.php?g=User&m=Shopdoor&a=index&token='.$this->token);
                    } else {
                        $this->error('编辑失败', './index.php?g=User&m=Shopdoor&a=manager&op=1&token='.$this->token.'&id='.$data['id']);
                    }
                }
            }

            if (1 == $operation) {
                $orderId = $_GET['id'];
                $is_exist = $this->shopDoor->where(array('id'=>$orderId))->find();
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
    public function dy(){    	
    //P($_POST);  	
    	$a=$_POST['order_info'];

    	$a=str_replace('商品名称:','', $a); $a=str_replace('商品名称：',',', $a);
    	$a=str_replace('单价:','', $a);$a=str_replace('单价：',',', $a);
    	$a=str_replace('数量:','', $a);$a=str_replace('数量：',',', $a);
    	$a=str_replace('总价:','', $a);$a=str_replace('总价：',',', $a);
    	$a=explode('|',$a);
    	$a=array_filter($a);
    	$str[]=explode(',',$a['0']);
    	$str='';
    	foreach ($a as $ke=>$v){
    		$str[]=explode(',',$v);
    	}
    	$str2='';
    		foreach ($str as $ke=>$v){
			$v = array_values(array_filter($v));
			if(strlen($v['0'])==6){
				$str2.=$v['0'].'       '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==9){
				$str2.=$v['0'].'     '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==12){
				$str2.=$v['0'].'   '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==15){
				$str2.=$v['0'].' '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}else{
				$str2.=$v['0'].'   '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}

    	
    	switch ($_POST['order_status']){
    		case 0:$_POST['order_status']='未付款';break;
    		case 1:$_POST['order_status']='已付款';break;
    		case 2:$_POST['order_status']='已取消';break;
    	}
	$beizhu = $_POST['order_extra_info'] ?:'无';
        $content="兴顺餐饮
".
"------------------
"."送达时间:{$_POST['sh_time']}
".
"------------------
"."买家：{$_POST['order_user']}
".
"下单时间：{$_POST['order_ok_time']}
".
"是否付款:{$_POST['order_status']}
".
"******************
".
"------------------
".
"商品名称 数量 单价 总价
{$str2}
".
"备注:{$beizhu}
".
"------------------
".
"合计：{$_POST['order_price']}元(含配送费￥0.0)
".
"------------------
".
"送货地址：{$_POST['order_adress']}
".
"联系电话：{$_POST['order_user_phone']}
";
$w=new WifiPrint($_POST['token']);

	$sMsg = ($iErr = $w->test($content)) == 0 ?'打印成功' :'打印失败，错误码：'.$iErr;
	  echo "<script>alert('".$sMsg."');history.back();</script>";
    }
 }

    public function delete(){
		if($this->shopOrder->where(array(
            'id'    => $_REQUEST['id'],
            'token' => $_REQUEST['token']
        ))->delete()){
            exit(json_encode(array('code' => 0)));
        }else{
            exit(json_encode(array('code' => -1)));
        };
    }
}
