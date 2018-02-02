<?php
class HotelResAction extends BaseAction{
	public $token;
	public $hotel_roomlist;
	public $wxuser;
	public $uid;
	public $hotel_hotellist;
	public $hotel_orderinfo;
	//构造函数
	public function _initialize() {
		parent::_initialize();
		$this->hotel_roomlist = M("Hotel_roomlist");  //实例化酒店房间管理表
		$this->wxuser = M("Wxuser");				  //实例化平台管理人员表
		$this->hotel_hotellist = M("Hotel_hotellist");//实例化酒店管理表
		$this->hotel_orderinfo = M("Hotel_orderinfo");
		$this->uid = session('uid');

	}
	public function index(){
		if($this->token == $_GET['token']){
				$w['uid'] = $this->uid;
				$w['hotel_id'] = $_GET['hotel_id'];
				$w['id'] = $_GET['room_type_id'];
				$info = $this->hotel_roomlist->where($w)->find();
				$data['room_price'] = $_GET['room_price'];
				$this->assign('data',$data);
				$this->assign('info',$info);
		}
		$this->display();
	}
	public function book(){
	    if(IS_POST){
	        $data['check_in_time'] = strtotime($_POST['check_in_time'].'&nbsp'.date('H:i:s'));
	        $data['check_out_time'] = strtotime($_POST['check_out_time'].'&nbsp'.date('H:i:s'));
	        $data['room_number'] = $_POST['room_number'];
	        $data['room_res_time'] = $_POST['room_res_time'];
	        $data['other_require'] = $_POST['other_require'];
	        $data['username'] = $_POST['username'];
	        $data['tel'] = $_POST['tel'];
	        $data['status'] = $_POST['status'];
	        $data['guest_source'] = 1;
	        $data['is_schedule'] = 1;
	        if($data['status'] == 1){
	            $data['pay_status'] = 1;
	        }else{
	            $data['pay_status'] = 0;
	        }
	        $data['uid'] = $this->uid;
	        $data['hotel_id'] = $_GET['hotel_id'];
	        $message = $this->hotel_roomlist->where(array('token'=>$this->token,'hotel_id'=>$data['hotel_id']))->find();
	        $data['room_type'] = $_GET['room_type']; 
	        $data['hotel_name'] = $message['hotel_name'];
	        $data['order_sn'] = 'WP' . date('Ymd') . mt_rand(10000,99999);
	        $data['price'] = $_GET['room_price'] * $data['room_number'];
	        $data['order_price'] = $_GET['room_price'] * $data['room_number'];
	        $data['order_sub_time'] = strtotime(date('Y-m-d H:i:s'));
	        $m = $this->hotel_roomlist->where(array('token'=>$this->token,'hotel_id'=>$_GET['hotel_id'],'room_type'=>$_GET['room_type']))->find();
	        $curc['can_use_room_count'] = $m['room_count']-$data['room_number'];
	        $this->hotel_roomlist->where(array('token'=>$this->token,'hotel_id'=>$_GET['hotel_id'],'room_type'=>$_GET['room_type']))->save($curc);
	        if($this->hotel_orderinfo->add($data)){
	           $this->success('添加成功','index.php?g=Vhotel&m=OrderSuccess&a=index&token='.$this->token.'&order_sn='.$data['order_sn']);
	        } else {
	           $this->error('添加失败', 'index.php?g=Vhotel&m=HotelSelect&a=index&token='.$this->token);
	        }
	    }
	   $this->display();
	}
	
	























}

?>