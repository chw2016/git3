<?php
/**
 *语音回复
**/
class ClassifyAction extends UserAction{
	public $shuju;
	static public $treeList = array();

	public function index(){
		$db=D('Classify');
		//自己加上的
		$token=session('token');
		$cate=$db->where(array('token'=>$token))->order('pid asc')->select();
		$dataD = self::tree($cate);
		$this->assign('dataD',$dataD);
		$where['token']=session('token');
		$where['pid']=0;
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order('sorts desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
        $this->assign('hover2',1);
		$this->display();
	}

	public function add(){

		//自己加上的
		$table=M('Classify');
		// $token='f17f0d1e02a8976cf065163525547260';
		$token=$this->_get('token')?$this->_get('token'):$this->token;
		$where['token']=$token;
		$getD=M('Classify')->where($where)->order('sorts,pid')->select();
        $data = self::tree($getD);

		$this->assign('info', $data);
		$id=$this->_get('id','intval');
		//自己加上的
        $this->assign('hover2',1);
		$this->display();



	}

	public function edit(){
		$id=$this->_get('id');
		$token=$this->_get('token');
		$where['id']=$id;
		$where['token']=$token;
		$db=M('Classify');
		$result=$db->where($where)->find();
		$res=$db->where(array('token'=>$where['token']))->order('sorts,pid')->select();
		$data = self::tree($res);
		$this->assign('info' ,$result);
		$this->assign('data' ,$data);
		$this->display();
	}

    static public function tree(&$data,$pid = 0,$count = 1) {
        foreach ($data as $key => $value){
            if($value['pid']==$pid){
                $value['Count'] = $count;
                self::$treeList []=$value;
                unset($data[$key]);
                self::tree($data,$value['id'],$count+1);
            }
        }
        return self::$treeList ;
    }

	/* static public function dele(&$data,$pid = 0,$count = 1) {
        foreach ($data as $key => $value){
            if($value['pid']==$pid){
                $value['Count'] = $count;
                self::$treeList []=$value;
                unset($data[$key]);
                self::tree($data,$value['id'],$count+1);
            }
        }
        return self::$treeList ;
    }*/
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		// $result=D(MODULE_NAME)->where(array('id')=>)
		if(D(MODULE_NAME)->where($where)->delete()){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){
		//自行添加的
		//自行添加的
		$db=M('Classify');
		$pid=empty($_POST['pid'])?"":$_POST['pid'];
		$name=empty($_POST['name'])?"":$_POST['name'];
		$info=empty($_POST['info'])?"":$_POST['info'];
		$sorts=empty($_POST['sorts'])?"":$_POST['sorts'];
		$img=empty($_POST['img'])?"":$_POST['img'];
		$bg=empty($_POST['bg'])?"":$_POST['bg'];
        $color=empty($_POST['color'])?"":$_POST['color'];
		$url=empty($_POST['url'])?"":$_POST['url'];
		$icon=empty($_POST['icon'])?"":$_POST['icon'];
		$status=$_POST['status']?$_POST['status']:0;
		$token=$this->_get('token');
		$data['name']=$name;
		$data['info']=$info;
		$data['sorts']=$sorts;
		$data['img']=$img;
		$data['bg']=$bg;
		$data['url']=$url;
		$data['icon']=$icon;
		$data['status']=$status;
		$data['token']=$token;
        $data['pid']=$pid;
        $data['color']=$color;


		$res=$db->add($data);
		if ($res) {
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}


		//自行添加的
		//自行添加的

		//原来的代码
		// $this->all_insert();
	}
	public function upsave(){
	   $this->all_save();
	}

	 public function getCatid(){
	   $sid=$_GET['pp'];
	   $where['token']=session('token');
      if ($sid=="微汽车" or $sid=="微会员" or $sid=="微房产" or $sid=="微医疗" or $sid=="微美容"
          or $sid=="微喜帖" or $sid=="微食品" or $sid=="微健身" or $sid=="微政务" or $sid=="微旅游" or $sid=="微花店")
      {  $c= M("ctourl");
	   $where['name']=$sid;
	   $data=$c->where($where)->select();
	   echo json_encode($data);}

	   if ($sid=="刮刮卡")
      {  $c= M("lottery");
	   $where['type']=2;
	   $data=$c->where($where)->select();
	   echo json_encode($data);}
     if ($sid=="微投票")
     {  $c= M("Vote");
         $data=$c->where($where)->select();
         echo json_encode($data);
     }
     if ($sid=="微相册")
     {  $c= M("Photo");
         $data=$c->where($where)->select();
         echo json_encode($data);
     }
     if ($sid=="微表单")
     {  $c= M("Selfform");
         $data=$c->where($where)->select();
         echo json_encode($data);
     }

     if ($sid=="微信墙")
     {  $c= M("Wxq");
         $data=$c->where($where)->select();
         echo json_encode($data);
     }

     if ($sid=="微全景")
     {  $c= M("Panorama");
         $data=$c->where($where)->select();
         echo json_encode($data);
     }
     if ($sid=="微预约")
     {  $c= M("Yuyue");
         $data=$c->where($where)->select();
         echo json_encode($data);
     }
         if ($sid=="大转盘")
      {  $c= M("lottery");
	   $where['type']=1;
	   $data=$c->where($where)->select();
	   echo json_encode($data);}

	  if ($sid=="优惠券")
      {  $c= M("lottery");
	   $where['type']=3;
	   $data=$c->where($where)->select();
	   echo json_encode($data);}

	     if ($sid=="砸金蛋")
      {  $c= M("lottery");
	   $where['type']=4;
	   $data=$c->where($where)->select();
	   echo json_encode($data);}
	  // $c= M("Car_cx");
	   //$where['token']=session('token');
	   //$where['pp']=$sid;
	   //$data=$c->where($where)->select();
	   //echo json_encode($data);
  }

	/*public function addcate(){
		$id=$this->_get('id','intval');
		$data=M('Classify')->where(array('id'=>$id))->select();
		$newt=M('Category');
		$newdata['name']=$data['name'];
		$newdata['pid']=1;
		$newdata['sort']=1;
		$newdata['token']=$data['token'];
		$newt->data($newdata)->add();
	}*/
}
?>
