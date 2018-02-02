<?php
/**
 *文本回复
**/
class ImgAction extends UserAction{

	static public $treeList = array();

	public function index(){
        if(IS_POST){
            $seach = $_GET['seach'];
            $this->success('',U('Img/index',array('token'=>$this->token,'seach'=>$seach)));
        }else{
            $seach = $_GET['seach'];
        }
        if($seach){
            $where['classname'] = array('like','%'.$seach.'%');
            $this->assign('seach',$seach);
        }
        $db=D('Img');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,15);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('store')->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	public function add(){
		//自行添加
		$token=$this->_get('token');
		//自行添加
		$class=M('Classify')->where(array('token'=>session('token')))->order('pid asc')->select();
		if($class==false){$this->error2('请先添加微官网分类',U('Classify/index',array('token'=>session('token'))));}
		$db=M('Classify');
		// $where['token']=session('token');
		$where['token']=$token;
		$info=$db->where($where)->select();
		$sendData = self::tree($class);
		// print_r($info);exit();
        $this->redpay();

		$this->assign('info',$info);
		$this->assign('send',$sendData);
		$this->display();
	}

public function redpay(){
    $oModel = M('Wxredpay');
    $where = array('token'=>$_SESSION['token'],
       'strattime'=>array('elt',date('Y-m-d H:i:s')),
        'endtime'=>array('egt',date('Y-m-d H:i:s')),
        'is_open'=>0,
        'status'=>0
    );
   //print_r($where);exit;
    $aRedpay = $oModel->where($where)->order('id')->select();
//print_r($aRedpay);exit;
   $this->assign('aRedpay',$aRedpay);

}

	static public function tree(&$data,$pid = 0,$count = 1) {
        foreach ($data as $key => $value){
            if($value['pid']==$pid){
                $value['Count'] = $count;
                self::$treeList[]=$value;
                unset($data[$key]);
                self::tree($data,$value['id'],$count+1);
            } 
        }
        return self::$treeList ;
    }

	public function edit(){
		$db=M('Classify');
		$where['token']=session('token');
		$info=$db->where($where)->order('pid asc')->select();
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		$res=D('Img')->where($where)->find();
		$sendData = self::tree($info);
        $this->redpay();
		$this->assign('send',$sendData);
		$this->assign('info',$res);
		$this->assign('res',$info);
		$this->display();
	}
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
			M('Keyword')->where(array('pid'=>$where['id'],'token'=>session('token'),'module'=>'Img'))->delete();
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){
		$this->all_insert();

	}
	public function upsave(){

        //$arr = array('seach'=>$_GET['seach']);
		$this->all_save('','/index','',$seach=$_GET['seach']);
	}


    public function zhuanfa(){
        $db=D('Img_zhuanfa');
        $where['token']=session('token');
        $count=$db->where($where)->count();
        $page=new Page($count,15);
        $info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('times desc')->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->display();
    }
}
?>