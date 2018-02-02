<?php
/* author：zichao */
class HotelSelectAction extends BaseAction{
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
	//选择酒店
	public function index(){
		if($this->token == $_GET['token']){
			$where['token'] = $this->token;
			$hotel =$this->hotel_hotellist->where($where)->select();
			$a = count($hotel);
			for($i=0 ; $i<$a ; $i++){
				$where['hotel_id'] = $hotel[$i]['id'];
				$room = $this->hotel_roomlist->order('price')->where($where)->find();
				$hotel[$i]['lowest'] = $room['price'];
			}
			for($j=0 ; $j < $a ; $j++){
			    $data[$j] = json_decode($hotel[$j]['hotel_service'],true);
			    $hotel[$j] = array_merge($hotel[$j],$data[$j]);
			}
			for($k=1 ; $k <= $a ; $k++){
			    $message = $this->hotel_roomlist->where(array('hotel_id'=>$k))->select();
			    $b = count($message);
			    for($g=0 ; $g < $b ; $g++){
			        $info[$g] = $message[$g]['is_group'];
			    }
                if(array_sum($info) >= 1){
                    $hotel[$k-1]['is_group'] = 1;
                }else{
                    $hotel[$k-1]['is_group'] = 0;
                }
			} 			
			$this->assign('hotel',$hotel);
		}	
		$this->display();
	}
















}