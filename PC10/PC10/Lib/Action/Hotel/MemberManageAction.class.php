<?php
class MemberManageAction extends UserAction{
	
	public $token;
	public $hotel_roomlist;
	public $wxuser;
	public $uid;
	public $hotel_hotellist;
	public $hotel_member;
	public $hotel_orderinfo;
	
	//构造函数
	public function _initialize(){
		parent::_initialize();
		$this->hotel_hotellist = M("Hotel_hotellist");//实例化酒店管理表
		$this->hotel_member = M("Hotel_member");	  //实例化酒店会员表
		$this->hotel_orderinfo = M("Hotel_orderinfo");//实例化订单表
		$this->hotel_roomlist = M("Hotel_roomlist");  //实例化房型表
		$this->token = session('token');			  //获取session中平台管理人员的token
		$this->uid = session('uid');
		$where['token'] = $this->token;
		$where['uid'] = $this->uid;
		$message = $this->hotel_hotellist->where($where)->select();
		$this->assign('message',$message);
	}
	//默认情况下显示所有的会员
	public function index(){
		if(($this->token == $_GET['token']) && (!IS_POST)){
			$count = $this->hotel_member->where($where)->count();
			$page=new Page($count,15);
			$data = $this->hotel_member->where($where)->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('data',$data);
		}elseif(($this->token == $_GET['token']) && (IS_POST)){
			$array['username'] = $_POST['username'];
			$array['tel'] = $_POST['tel'];
			$array['lever'] = $_POST['lever'];
			$array['hotel_name'] = $_POST['hotel_name'];
			$array['member_sn'] = $_POST['member_sn'];
			foreach($array as $k=>$v){
				if($v !== ''){
					$where[$k] = $v;
				}
			}
			$count = $this->hotel_member->where($where)->count();
			$page=new Page($count,15);
			$data = $this->hotel_member->where($where)->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('data',$data);
		}
		$this->display();
	}
	//会员管理，可以查询会员的积分兑换信息，
	public function manage(){
		if($this->token == $_GET['token']){
			$condition['member_sn'] = $_POST['member_sn'];
			$condition['token'] = $this->token;
			$condition['uid'] = $this->uid;
			$data = $this->hotel_member->where($condition)->find();
			$this->assign('data',$data);
			echo json_encode($data);
		}
	}
	public function history(){
		if($this->token == $_GET['token']){
			$where['member_sn'] = '';
			$where['uid'] = $this->uid;				
			$data = $this->hotel_member->where()->find();
			$message = $data['openid'];
			$message['uid'] = $this->uid; 
			$info = $this->hotel_orderinfo->where($message)->select();
			$this->assign('info',$info);
		}
		$this->display();
	}



	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>