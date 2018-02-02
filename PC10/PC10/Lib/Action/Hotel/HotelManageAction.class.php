<?php
class HotelManageAction extends UserAction{
	
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
		$this->token = session('token');			  //获取session中平台管理人员的token
		$this->uid = session('uid');
	}
    public function Index(){
        $hotelListModel = M('Hotel_hotellist');
        $where = array('uid'=>session('uid'),'token'=>session('token'));
        $count=$hotelListModel->where($where)->count();
        $page=new Page($count,15);
        $info=$hotelListModel->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->display();
    }
    public function add(){
        if(IS_POST){
            $service = array();
            if($_POST['sw'] == 1){
                $service['sw'] = 1;
            }else{
                $service['sw'] = 0;
            }
            if($_POST['wf'] == 1){
                $service['wf'] = 1;
            }else{
                $service['wf'] = 0;
            }
            if($_POST['tc'] == 1){
                $service['tc'] = 1;
            }else{
                $service['tc'] = 0;
            }
            if($_POST['ct'] == 1){
                $service['ct'] = 1;
            }else{
                $service['ct'] = 0;
            }
            if($_POST['yy'] == 1){
                $service['yy'] = 1;
            }else{
                $service['yy'] = 0;
            }if($_POST['hy'] == 1){
                $service['hy'] = 1;
            }else{
                $service['hy'] = 0;
            }
            if($_POST['swz'] == 1){
                $service['swz'] = 1;
            }else{
                $service['swz'] = 0;
            }
            if($_POST['jx'] == 1){
                $service['jx'] = 1;
            }else{
                $service['jx'] = 0;
            }
            if($_POST['rs'] == 1){
                $service['rs'] = 1;
            }else{
                $service['rs'] = 0;
            }
            if($_POST['jc'] == 1){
                $service['jc'] = 1;
            }else{
                $service['jc'] = 0;
            }
            if($_POST['xy'] == 1){
            	$service['xy'] = 1;
            }else{
            	$service['xy'] = 0;
            }
            if($_POST['js'] == 1){
            	$service['js'] = 1;
            }else{
            	$service = 0;
            }
            $hotelListModel = M('Hotel_hotellist');
            $_POST['uid'] = session('uid');
            $_POST['hotel_service'] = json_encode($service);
            $hotelListModel->create();
            if($hotelListModel->add()){
                $this->ajaxReturn(array('code'=>0,'msg'=>'保存成功'));
            }else{
                $this->ajaxReturn(array('code'=>-1,'msg'=>'保存失败'));
            }
        }else{
            $this->display();
        }
    }

    public function edit(){
        $id = $_REQUEST['id'];
       
        if($id){
            $hotelListModel = M('Hotel_hotellist');
            if(IS_POST){
                $id = $_POST['id'];
                $service = array();
                if($_POST['sw'] == 1){
                    $service['sw'] = 1;
                }else{
                    $service['sw'] = 0;
                }
                if($_POST['wf'] == 1){
                    $service['wf'] = 1;
                }else{
                    $service['wf'] = 0;
                }
                if($_POST['tc'] == 1){
                    $service['tc'] = 1;
                }else{
                    $service['tc'] = 0;
                }
                if($_POST['ct'] == 1){
                    $service['ct'] = 1;
                }else{
                    $service['ct'] = 0;
                }
                if($_POST['yy'] == 1){
                    $service['yy'] = 1;
                }else{
                    $service['yy'] = 0;
                }if($_POST['hy'] == 1){
                    $service['hy'] = 1;
                }else{
                    $service['hy'] = 0;
                }
                if($_POST['swz'] == 1){
                    $service['swz'] = 1;
                }else{
                    $service['swz'] = 0;
                }
                if($_POST['jx'] == 1){
                    $service['jx'] = 1;
                }else{
                    $service['jx'] = 0;
                }
                if($_POST['rs'] == 1){
                    $service['rs'] = 1;
                }else{
                    $service['rs'] = 0;
                }
                if($_POST['jc'] == 1){
                    $service['jc'] = 1;
                }else{
                    $service['jc'] = 0;
                }
                if($_POST['xy'] == 1){
                	$service['xy'] = 1;
                }else{
                	$service['xy'] = 0;
                }
                if($_POST['js'] == 1){
                	$service['js'] = 1;
                }else{
                	$service['js'] = 0;
                }
                $_POST['uid'] = session('uid');
                $_POST['hotel_service'] = json_encode($service);
                if($hotelListModel->where(array('id'=>$id,'uid'=>session('uid'),'token'=>session('token')))->save($_POST)){
                    $this->ajaxReturn(array('code'=>0,'msg'=>'保存成功'));
                }else{
                    $this->ajaxReturn(array('code'=>-1,'msg'=>'保存失败'));
                }
            }else{
                $data = $hotelListModel->where(array('id'=>$id,'uid'=>session('uid'),'token'=>session('token')))->find();
                $data['hotel_service'] = json_decode($data['hotel_service'],true);
                $this->assign('data',$data);
                $this->display();
            }
        }
    } 
    public function del(){
    	if($this->token == $_GET['token']){
    		$id['hotel_id'] = $_GET['id'];
    		$roomlist = $this->hotel_roomlist->where($id)->select();
    		if(empty($roomlist)){
    		    $w['id'] = $id['hotel_id'];
    			$back = $this->hotel_hotellist->where($w)->delete();
    			if($back == true){
    				$this->ajaxReturn(array('status'=>1,'info'=>'删除成功','url'=>'http://localhost/index.php?g=Hotel&m=HotelManage&a=index&token='.$this->token));
				}else{
					$this->ajaxReturn(array('status'=>-1,'info'=>'删除失败'));
				}
    		}else{
    		    $this->ajaxReturn(array('status'=>-1,'info'=>'删除失败!此酒店下面存在房型.若想继续删除，请先删除该酒店下的房型.'));
    		}
    	}
    }
}
?>