<?php 
class SpreadAction extends UserAction{
	//微推广首页
	public function index(){
		$db=M('Spread');
		$token=$this->_get('token');
		$count=$db->where(array('token'=>$token))->count();
		$page=new Page($count,15);
		$info=$db->where(array('token'=>$token))->order('id')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	//添加ID
	public function add(){
		$this->display();
	}
	//查看参加人数
	public function check(){
		//上述为查看的代码
		//上述为查看的代码
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$where['sid']=$id;
		$where['token']=$token;
		$db=M('Spread_user');
		$count=$db->where($where)->count();
		// $result_1=$db->where($where)->select();
		$db_1=M('Spread');
		$result=$db_1->where(array('id'=>$id,'token'=>$token))->find();
		$page=new Page($count,15);
		$info=$db->where($where)->order('id')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->assign('res',$result);
		$this->assign('count',$count);
        $this->assign('cid',$id);
		$this->display();
	}
	//编辑某条记录
	public function edit(){
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$where['id']=$id;
		$where['token']=$token;
		$db=M('Spread');
		$result=$db->where($where)->find();
		// print_r($result);exit();
		$this->assign('editContent',$result);
		$this->display();
	}
	//删除某条记录

    public function pubprize(){
        $id=$this->_get('id','intval');
        $ifprize=$this->_get('ifprize','intval');
        $cid=$this->_get('cid','intval');
        if($ifprize){
            $data['ifprize'] = $ifprize;
        }else{
            $data['ifprize'] = 0;
        }
        $where['Id']=$id;
        $db=M('Spread_user');
        $result=$db->where($where)->save($data);
        if ($result) {
            $this->success2('操作成功！',U(MODULE_NAME.'/check',array('token'=>$this->token,'id'=>$cid)));
        }else{
            $this->error2('操作失败！',U(MODULE_NAME.'/check',array('token'=>$this->token,'id'=>$cid)));
        }
    }

	public function del(){
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$where['id']=$id;
		$where['token']=$token;
		$db=M('Spread');
		$result=$db->where($where)->delete();
        $result1 = M('Keyword')->where(array('token'=>$token,'pid'=>$where['id'],'module'=>'Spread'))->delete();
		if ($result || $result1) {
			$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$token)));
		}else{
			$this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>$token)));
		}
	}
	//添加add方法所传递过来的值
	public function addForm(){
		$data['keyword']=$_POST['name'];
		$data['activityname']=$_POST['active'];
		$data['introduction']=$_POST['info'];
		$data['startdate']=$_POST['startdate'];
		$data['enddate']=$_POST['enddate'];
		$data['imgurl']=$_POST['img'];
		$data['token']=$_GET['token'];
		//还有如何添加用户的uid
		$db=M('Spread');
		$result=$db->data($data)->add();
        $getJustData=$db->where(array('activityname'=>$data['activityname'],'token'=>$data['token']))->find();
        $database['pid']  = $getJustData['id'];
        $database['module'] = 'Spread';
        $database['token']  = $data['token'];
        $database['keyword']  = $data['keyword'];
        M('Keyword')->data($database)->add();
		if($result){
			$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$data['token'])));
		}else{
			$this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>$data['token'])));
		}
	}
	//保存save方法所改变的值
	public function saveForm(){
		$id=$this->_get('id','intval');
		$db=M('Spread');
		$data['keyword']=$_POST['name'];
		$data['activityname']=$_POST['active'];
		$data['introduction']=$_POST['info'];
		$data['startdate']=$_POST['startdate'];
		$data['enddate']=$_POST['enddate'];
		// $data['prize']=$_POST['giftname'];
		$data['imgurl']=$_POST['img'];
		$data['token']=$_GET['token'];
		$result=$db->where(array('id'=>$id))->save($data);
		$database['pid']  = $id;
        $database['module'] = 'Spread';
        $database['token']  = $data['token'];
        $database['keyword']  = $data['keyword'];
        M('Keyword')->where(array('pid'=>$id,'token'=>$data['token']))->save($database);

		if ($result) {
			$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$data['token'])));
		}else{
			$this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>$data['token'])));
		}
	}
	//奖品设置
	public function setting(){
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$db=M('Spread');
		$result=$db->where(array('id'=>$id,'token'=>$token))->find();
		if(!empty($result['prize'])){
			$this->assign('present',$result['prize']);
		}
		$this->assign('res', $result);
		$this->display();
	}
	//保存奖品设置
	public function giftSetting(){
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$giftName=$_POST['giftname'];
		$data['prize']=$giftName;
		$db=M('Spread');
		$result=$db->where(array('id'=>$id))->save($data);
		if ($result) {
			$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$token,'id'=>$id)));
		}else{
			$this->error('操作失败！',U(MODULE_NAME.'/setting',array('token'=>$token,'id'=>$id)));
		}
	}
	//生成二维码显示界面(待定。。。。。。)
	public function create(){
		$id=$this->_get('id','intval');
		$token=$this->_get('token');
		$db=M('Spread');
		$result=$db->where(array('id'=>$id,'token'=>$token))->find();
		$res=M('Spread_user')->where(array('sid'=>$id,'token'=>$token))->find();
        // print_r($res);exit();
       $parament = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 101'.$result['id'].'}}}';
		
		
		$code = new Code($this->token,'101'.$result['id']);


		$this->assign('result' ,$res);

		$this->assign('res', $result);
		$this->assign('imgUrl', $code->getYJCode());
		$this->display();
	}


    public function creatTicket($token, $parament) {
		 
		/*发送数据到微信服务器端并获取数据*/
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
		$result = $this->api_notice_increment($url, $parament);
		$jsonInfo = json_decode($result, true);
		$ticket = $jsonInfo['ticket'];

		/*根据ticket获取图片资源*/
		$url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";	
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpInfo = curl_getinfo($ch);
		return array_merge(array('body'=>$package), array('header'=>$httpInfo));
	}

}
?>