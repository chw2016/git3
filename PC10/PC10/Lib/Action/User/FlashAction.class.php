<?php
/**
 *首页幻灯片回复
**/
class FlashAction extends UserAction{
	public function index(){
		$db=D('Flash');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('sorts asc')->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
        $this->assign('hover4',1);
		$this->display();
	}
	public function add(){
        $this->assign('hover4',1);
		$this->display();
	}
	public function edit(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		$res=D('Flash')->where($where)->order('sorts asc')->find();
		$this->assign('info',$res);
        $this->assign('hover4',1);
		$this->display();
	}
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){
		//C('TOKEN_ON',false);
		$this->all_insert();
		
	}
	public function upsave(){
		$this->all_save();
	}
	//接受轮播图的选择
	public function scrollChoice(){
		$token = $this->_get('token');
		$id = $this->_post('scrollid','intval');
		$scrollchoice = $this->_post('scrollchoice');
		$data['ifscroll'] = $scrollchoice;
		$db = M('Flash');
		$result = $db->where(array('token'=>$token,'id'=>$id))->save($data);
		if ($result) {
			$this->success('操作成功',U(MODULE_NAME.'/index',array('token'=>$token)));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index',array('token'=>$token)));
		}
 	}

    /**
     * 公告信息
     */
    public function gonggao(){
        $list=M('Gonggao_info')->where(array('token'=>$this->token))->select();
       // p($list);
        //echo 8;die;
        $hover6=1;
        $this->assign('hover6',$hover6);
        $this->assign('list',$list);
        $this->display();
    }
    /*
     * 添加公告信息
     */
    public function gonggao_add(){
        if(IS_POST){
            $_POST['token']=$this->token;
            $_POST['type']=0;
            $_POST['add_time']=time();
            if(M('Gonggao_info')->add($_POST)){
                $this->success2('操作成功',U('gonggao',array('token'=>$this->token)));
            }else{
                $this->error2('操作失败',U('gonggao',array('token'=>$this->token)));
            }
        }else {
            $hover6 = 1;
            $this->assign('hover6', $hover6);
            $this->display();
        }

    }
    /**
     * 发送公告
     */
    public function gonggao_fs(){
      $id=$this->_get('id');
        $data['type']=1;
        if(M('Gonggao_info')->where(array('id'=>$id))->save($data)){
            $this->success2('操作成功',U('gonggao',array('token'=>$this->token)));
        }else{
            $this->error2('操作失败',U('gonggao',array('token'=>$this->token)));
        }
    }
}
?>