<?php
/*酒店订单管理*/
class OrderManageAction extends UserAction{
	public $token;
	public $hotel_roomlist;
	public $wxuser;
	public $uid;
	public $hotel_hotellist;
	public $hotel_orderinfo;
	
	//构造函数
	public function _initialize() {
		parent::_initialize();
		$this->hotel_roomlist = M("Hotel_roomlist");  
		$this->wxuser = M("Wxuser");				  
		$this->hotel_hotellist = M("Hotel_hotellist");
		$this->hotel_orderinfo = M("Hotel_orderinfo");
		$this->token = session('token');			  
		$this->uid = session('uid');
		$uid['uid'] = $this->uid;
		$message = $this->hotel_hotellist->where($uid)->select();
		$this->assign('message',$message);
		$room_type = $this->hotel_roomlist->where($uid)->select();
		$this->assign('room_type',$room_type);
	}

	//默认情况下显示所有的订单
	public function index(){
		if((IS_POST) && ($this->token == $_GET['token'])){
			$array['username'] = $_POST['username'];
			$array['tel'] = $_POST['tel'];
			$array['pay_status'] = $_POST['pay_status'];
			$array['is_group'] = $_POST['is_group'];
			$array['is_scode'] = $_POST['is_scode'];
			$array['status'] = $_POST['status'];
			$array['hotel_id'] = $_POST['hotel_id'];
			$array['room_type'] = $_POST['room_type'];
			foreach($array as $k=>$v){
				if($v !== ''){
					$arr[$k] = $v; 
				}
			}
			$this->assign('arr',$arr);
			$arr['uid'] = $this->uid;
			$count = $this->hotel_orderinfo->where($arr)->count();
			$page=new Page($count,15);
			foreach($arr as $key=>$val) {
				$Page->parameter   .=   "$key=".urlencode($val).'&';
			}
			$data = $this->hotel_orderinfo->where($arr)->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('data',$data);
		}elseif(($this->token == $_GET['token']) && (!IS_POST)){
			$arr['uid'] = $this->uid;
			$count = $this->hotel_orderinfo->where($arr)->count();
			$page=new Page($count,15);
			$data = $this->hotel_orderinfo->where($arr)->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('data',$data);
		}
		$this->display();
	}
	//订单详情
	public function detail(){
	    if($this->token == $_GET['token']){
	        if(IS_POST){
	            $w['token'] = $this->token;
	            $w['order_sn'] = $_GET['order_sn'];
	            $info = $this->hotel_orderinfo->where($w)->find();
                $this->assign('info',$info);
                echo json_encode($info);
	        }
	    }
	}
	
	//处理订单
	public function deal(){
	    if($this->token == $_GET['token']){
	        if(IS_POST){
	            $w['token'] = $this->token;
	            $w['order_sn'] = $_GET['order_sn'];
	            $info = $this->hotel_orderinfo->where($w)->find();
	            $this->assign('info',$info);
	            echo json_encode($info);
	        }
	    }
	}
    
	public function dealup(){
	    if($this->token == $_GET['token']){
	        $w['token'] = $this->token;
	        $w['order_sn'] = $_GET['order_sn'];
	        $data['check_out_time'] = strtotime($_POST['deal_check_out_time'].'&nbsp'.data('H:i:s'));
	        $data['other_price'] = $_POST['deal_other_price'];
	        $data['pay_status'] = $_POST['deal_pay_status'];
	        $data['deal_order_price'] = $_POST['deal_order_price'];
	        $back = $this->hotel_orderinfo->where($w)->save($data);
	        if($back == true){
	           $this->ajaxReturn(array('status'=>1,'info'=>'保存成功'));
    		}else{
    		   $this->ajaxReturn(array('status'=>-1,'info'=>'保存失败'));
    		}
	    }
	}

	


}