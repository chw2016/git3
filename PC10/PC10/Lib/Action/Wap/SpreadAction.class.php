<?php 
class SpreadAction extends BaseAction{

	public function index(){
		$db=M('Spread_user');
		$db_1=M('Spread');
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$openid=$this->get('openid');
		$count=$db->where(array('sid'=>$id,'token'=>$token))->count();
		$result=$db->where(array('sid'=>$id,'token'=>$token))->find();
		$res=$db_1->where(array('id'=>$id,'token'=>$token))->find();
		// print_r($result);exit();

        $only=$db->where(array('sid'=>$id,'openid'=>$this->_get('openid'),'token'=>$token))->find();
        if($only){
            $this->assign('userdata',$only);
        }else{
            $this->assign('userdata',null);
        }
		$this->assign('result' ,$result);
		$this->assign('id',$id);
		$this->assign('count' ,$count);
		$this->assign('res' ,$res);
		$this->assign('openid' ,$openid);
		$this->display();
	}
	public function saveActive(){
		$db=M('Spread_user');
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$username=$_POST['username'];
		$phone=$_POST['phone'];
		$data['sid']=$id;
		$data['token']=$token;
		$data['username']=$username;
		$data['phone']=$phone;
		$data['jointime']=date("Y-m-d");
		$data['openid']=$this->_get('openid');
		$only=$db->where(array('sid'=>$id,'openid'=>$this->_get('openid'),'token'=>$token))->find();
		if($only){
			$this->error('抱歉！您已经参与此次活动了！',U(MODULE_NAME.'/spread_success',array('token'=>$data['token'],'id'=>$data['sid'],'openid'=>$this->_get('openid'))));
		}else{
			$result=$db->data($data)->add();
			if($result){
				$this->success('您已成功参与此次活动',U(MODULE_NAME.'/spread_success',array('token'=>$data['token'],'id'=>$data['sid'],'openid'=>$this->_get('openid'))));
			}else{
				$this->error('您未能成功参与此次活动！',U(MODULE_NAME.'/index',array('token'=>$data['token'],'id'=>$data['sid'],'openid'=>$this->_get('openid'))));
			}
		}
		
	}

	public function spread_success(){
        $id=$_GET['id'];
        $db_1=M('Spread');
        $data=$db_1->where(array('id'=>$id,'token'=>$this->token))->find();
        $this->assign("data",$data);
		$this->display();
	}
}

?>