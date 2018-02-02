<?php
//wap留言模块

class LiuyanAction extends BaseAction{
	public $token;
	public $wecha_id;
	public function __construct(){
		parent::__construct();
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		$this->token = $this->_get('token');
		$this->assign('token',$this->token);

		$this->wecha_id	= $this->_get('wecha_id');

		if (!$this->wecha_id){
			$this->wecha_id='null';
		}
		$this->assign('wecha_id',$this->wecha_id);
		
	}

	//留言列表视图
	public function index(){

		$this->token = $this->_get('token');
		$this->wecha_id	= $this->_get('wecha_id');
		$this->createtime = time();
		$db = M('liuyan');
        $oConfigModel = M('Liuyan_config');
		$rep = M('reply_info');
        $apai = $oConfigModel->where(array('token'=>$this->token))->find();
        if($apai['type']==1){
            $this->repic = $rep->where(array('infotype'=> "Liuyan", 'token'=> $this->token))->field('picurl')->find();
            $this->info = $db->order('createtime')->where(array('token'=> $this->token,'wecha_id'=>$this->openid))->order('createtime DESC')->select();
        }else{
            $this->repic = $rep->where(array('infotype'=> "Liuyan", 'token'=> $this->token))->field('picurl')->find();
            $this->info = $db->order('createtime')->where(array('token'=> $this->token))->order('createtime DESC')->select();
        }

		$this->display();

	}


	
	public function add(){
		if($_POST['action'] == 'liuyan'){
			
			$db = M('liuyan');

			if($db->add($_POST)){
				echo '留言成功';
			}else{
				echo '留言失败';
			}
		}
	}
	
	
	//删除留言处理
	public function del(){
		$this->token = $this->_get('token');
		$this->wecha_id	= $this->_get('wecha_id');
		$db = M('liuyan');
		$id = $_GET['id'];
		if(IS_GET){
			$db = M('Liuyan');
            $res = $db->where(array('id'=>$id))->find();
            if($res['wecha_id'] != null && $res['wecha_id'] == $this->wecha_id) {
                $db->where(array('id'=>$id))->delete();
            }
			
			header("location:".U('Liuyan/index',array('token'=> $this->token, 'wecha_id'=> $this->wecha_id)));
		}


}

















}







?>