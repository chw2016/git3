<?php
class GcardAction extends UserAction{
	public function index(){
		$db=D('Gcard');
		$where['token']=session('token');
		$count=$db->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order('taxis ASC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('list',$info);	
		$this->display();
	}
	public function diy(){
		$db=D('Gcard_diy');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order('id ASC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('list',$info);	
		$this->display();
	}	
	public function add(){
	    $token	  =  $this->_get('token');
		if(IS_POST){			
			$data=D('Gcard');
			$_POST['token']=session('token');			
			if($data->create()!=false){				
				if($id=$data->add()){
					$data1['pid']=$id;
					$data1['module']='Gcard';
					$data1['token']=session('token');
					$data1['wecha_id']='';
					$data1['keyword']='贺卡'.$taxis;
					M('Keyword')->add($data1);
					$this->success2('添加成功',U('Gcard/index'));
				}else{
					$this->error2('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error2($data->geterror());
			}
		}else{
			$this->assign('Token',$token);
			$this->display();
		}

	}
	public function set(){
		if(IS_POST){
			$data=D('Gcard');
			$_POST['id']=$this->_get('id');
			$_POST['token']=session('token');				
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token']);			
			$check=$data->where($where)->find();
			if($check==false)$this->error2('非法操作');
			if($data->create()){				
				if($id=$data->where($where)->save($_POST)){
					$data1['pid']=$_POST['id'];
					$data1['module']='Gcard';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success2('修改成功');
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($data->geterror2());
			}
			
		}else{
			$id=$this->_get('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('Gcard');
			$check=$data->where($where)->find();
			if($check==false)$this->error2('非法操作');
			$info=$data->where($where)->find();		
		    $this->assign('set',$info);
		    $this->display();
		}
	}	

	public function delete(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data=M('Gcard');
		$check=$data->where($where)->find();
		if($check==false)$this->error2('非法操作');
		$back=$data->where($wehre)->delete();
		if($back==true){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Gcard'))->delete();
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
	}

	public function del_diy(){
		$id  =  $this->_get('id');
		$token	  =  $this->_get('token');
		$wecha_id	  =  $this->_get('wecha_id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data=M('Gcard_diy');
		$check=$data->where($where)->find();
		if($check==false)$this->error2('非法操作');
		$back=$data->where($wehre)->delete();
		if($back==true){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'wecha_id'=>$wecha_id,'module'=>'GcardDiy'))->delete();
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
	}	
	
	
		
}



?>