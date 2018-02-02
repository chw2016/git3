<?php
class RoomSelectAction extends BaseAction{
	public $token;
	public $hotel_roomlist;
	public $wxuser;
	public $uid;
	public $hotel_hotellist;
	//构造函数
	public function _initialize() {
		parent::_initialize();
		$this->hotel_roomlist = M("Hotel_roomlist");  //实例化酒店房间管理表
		$this->wxuser = M("Wxuser");				  //实例化平台管理人员表
		$this->hotel_hotellist = M("Hotel_hotellist");//实例化酒店管理表

	}
	
	
	public function index(){
		if($this->token == $_GET['token']){
			$where['token'] = $this->token;
			$where['id'] = $_GET['hotel_id'];
			$hotel = $this->hotel_hotellist->where($where)->find();
			$arr = json_decode($hotel['hotel_service'],true);
			$hotel = array_merge($hotel,$arr);
			$w['token'] = $this->token;
			$w['hotel_id'] = $_GET['hotel_id'];
			$roomlist = $this->hotel_roomlist->where($w)->select();
			$data = json_encode($roomlist);
			$this->assign('hotel',$hotel);
			$this->assign('roomlist',$roomlist);
			
		}
		$this->display();
	}
		

}