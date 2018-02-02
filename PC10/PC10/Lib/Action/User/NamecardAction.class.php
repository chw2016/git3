<?php
class NamecardAction extends UserAction{

	public function index(){
		
		$db=M('Card');
		$token=$this->_get('token');
		$count=$db->where(array('token'=>$token))->count();
		$page=new Page($count,15);
		$info=$db->where(array('token'=>$token))->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('res',$info);
		// print_r($info);exit(); 
		
		$this->display();
	}
	//删除数据

    public function del(){
		$where['id']=$_REQUEST['id'];
		if(M('Card')->where($where)->delete()){
			
			$this->success('操作成功',U('Namecard/index',array('token'=>$this->token)));
		}else{
			$this->error('操作失败',U('Namecard/index',array('token'=>$this->token)));
		}
	}
	/*
	*显示添加页面
	*/
	public function classify(){
		$this->display();
	}
	public function upsave(){
		$data['token'] = $this->_get('token');
		$data['name'] = $_POST['name'];
		$data['city'] = $this->_post('city');
		$data['headerpic'] = $this->_post('headerpic');
		$data['profession'] = $this->_post('profession');
		$data['cellphone'] = $this->_post('cellphone');
		$data['email'] = $this->_post('email');
		$data['qq'] = $this->_post('qq');
		$data['weixin'] = $this->_post('weixin');
		$data['url'] = $this->_post('url');
		$data['address'] = $this->_post('address');
		$db = M('Card');
		$result = $db->data($data)->add();
		if($result){
			
			$this->success('操作成功',U('Namecard/index',array('token'=>$this->token)));
		}else{
			$this->error('操作失败',U('Namecard/index',array('token'=>$this->token)));
		}
	}
	/*
	*显示修改页面
	*
	*/
	public function modify(){
		$id = $_GET['id'];
		$db = M('Card');
		$arr = $db->find($id);
		$this->assign('data',$arr);
		$this->display();
	}
	
	public function update(){
		$token = $this->_get('token');
		$id = $this->_get('id','intval');
		$db = M('card');
		$data['name'] = $_POST['name'];
		$data['city'] = $_POST['city'];
		$data['headerpic'] = $_POST['headerpic'];
		$data['profession'] = $_POST['profession'];
		$data['cellphone'] = $_POST['cellphone'];
		$data['email'] = $_POST['email'];
		$data['qq'] = $_POST['qq'];
		$data['weixin'] = $_POST['weixin'];
		$data['url'] = $_POST['url'];
		$data['address'] = $_POST['address'];
		$coint = $db->where(array('id'=>$id))->save($data);
		if($coint == 1){
			$this->success('数据修改成功',U(MODULE_NAME.'/index',array('token'=>$token)));
		}else{
			$this->error('数据修改失败',U(MODULE_NAME.'/index',array('token'=>$token)));
		}
	}



	
 }
