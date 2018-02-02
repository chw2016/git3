<?php
/**
 * User: zc
 * Date: 14-6-30
 * Time: 上午11:22
 * To change this template use File | Settings | File Templates.
 */
class RoomManageAction extends UserAction{
	public $token;
	public $hotel_roomlist;
	public $hotel_orderinfo;
	public $uid;
	public $hotel_hotellist;
	//构造函数
	public function _initialize() {
		parent::_initialize();
    	$this->hotel_roomlist = M("Hotel_roomlist");  //实例化酒店房间管理表
    	$this->hotel_orderinfo = M("Hotel_orderinfo");				  //实例化平台管理人员表
    	$this->hotel_hotellist = M("Hotel_hotellist");//实例化酒店管理表
    	$this->token = session('token');			  //获取session中平台管理人员的token
    	$this->uid = session('uid');
	}
	//默认情况下不显示房间信息
    public function index(){
     	if(($this->token) == $_GET['token']){
     		$uid['uid'] = $this->uid;
     		$message = $this->hotel_hotellist->where($uid)->select();
     		$this->assign('message',$message);
     		if($uid['hotel_id'] = $_GET['hotel_id']){
     		    $this->assign('uid',$uid);
	     		$uid['uid'] = $this->uid;
	     		$message = $this->hotel_hotellist->where($uid)->select();
	     		$this->assign('message',$message);
	     		$data = $this->hotel_roomlist->where($uid)->select();
	     		
	     		$this->hotel_orderinfo->where($w)->select();
	     		//$data['can_use_room_count'] = ;
	     		$this->assign('data',$data);
     		}
    	} 
    	$this->display();
    }
    //添加房间型号
    public function add(){
	    $uid['uid'] = $this->uid;
	    $message = $this->hotel_hotellist->where($uid)->select();
	    $this->assign('message',$message);
    	if(IS_POST){
    		$data = $this->hotel_roomlist->create();
    		$data['can_use_room_count'] = $data['room_count'];
    		$data['uid'] = $this->uid;
    		$data['hotel_id'] = $_POST['select_hotel'];
    		$a = $this->hotel_hotellist->where(array('id'=>$data['hotel_id']))->find();
    		$data['hotel_name'] = $a['hotel_name'];
    		$message = $this->hotel_roomlist->add($data);
    		if($message){
    			$this->ajaxReturn(array('code'=>0,'msg'=>'保存成功'));
    		}else{
    			$this->ajaxReturn(array('code'=>-1,'msg'=>'保存失败'));
    		}
    	}
    	$this->display();
    }
    //删除房型
    public function delete(){
    	if($this->token == $_GET['token']){
    		$id['id'] = $_GET['id'];
    		$id['hotel_id'] = $_GET['hotel_id'];
    		$is_exist = $this->hotel_roomlist->where($id)->find();
    		if($is_exist == true){
    			$back = $this->hotel_roomlist->where($id)->delete();
    			if($back == true){
	    			$this->ajaxReturn(array('status'=>1,'info'=>'删除成功','url'=>'http://localhost/index.php?g=Hotel&m=RoomManage&a=index&token='.$this->token.'&hotel_id='.$id['hotel_id']));
				}else{
					$this->ajaxReturn(array('status'=>-1,'info'=>'删除失败'));
				}
    		}
    	}
	}
	//编辑房型
	public function edit(){
		$where['id'] = $_GET['id'];
		$where['hotel_id'] = $_GET['hotel_id'];  
		$where['token'] = $this->token;
		if(IS_POST){
			$data = $this->hotel_roomlist->create();
			$data['id'] = $_GET['id'];
			$data['hotel_id'] = $_GET['hotel_id'];
			$data['token'] = $this->token;
			if($this->hotel_roomlist->where($where)->save($data)){
				$this->ajaxReturn(array('code'=>0,'msg'=>'保存成功'));
			}else{
				$this->ajaxReturn(array('code'=>-1,'msg'=>'保存失败'));
			}
		}else{
 			$data = $this->hotel_roomlist->where($where)->find();
 			$this->assign('data',$data);
 			$this->display();
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}