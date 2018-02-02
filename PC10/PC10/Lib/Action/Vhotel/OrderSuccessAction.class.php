<?php
class OrderSuccessAction extends BaseAction{
	public $uid;
	public $hotel_orderinfo;
	//构造函数
	public function _initialize() {
		parent::_initialize();
		$this->hotel_orderinfo = M("Hotel_orderinfo");  //实例化酒店房间管理表
		$this->uid = session('uid');
	}
	public function index(){
  	    if($this->token == $_GET['token']){
	        $where['uid'] = $this->uid;
	        $where['order_sn'] = $_GET['order_sn'];	
	        $data = $this->hotel_orderinfo->where($where)->find();
	        $this->assign('data',$data);
	        $time = explode('|',$data['room_res_time']);
	        $this->assign('time',$time);
	    }  
		$this->display();
	}




















}

?>