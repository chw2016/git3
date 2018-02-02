<?php
class HotelDetailAction extends BaseAction{
    public $hotel_roomlist;
    public $uid;
    public $hotel_hotellist;
    //构造函数
    public function _initialize() {
        parent::_initialize();
        $this->hotel_roomlist = M("Hotel_roomlist");  //实例化酒店房间管理表
        $this->hotel_hotellist = M("Hotel_hotellist");//实例化酒店管理表
        $this->uid = session('uid');
    
    }
    
    public function index(){
        if($this->token == $_GET['token']){
            $where['id'] = $_GET['hotel_id'];
            $hotel = $this->hotel_hotellist->where($where)->find();
            $hotel_service = json_decode($hotel['hotel_service'],true);
            $hotel = array_merge($hotel,$hotel_service);
            $this->assign('hotel',$hotel);
        }
        $this->display();
    }
}