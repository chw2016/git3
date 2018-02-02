<?php
class NamecardAction extends BaseAction{
	public function index(){
		$id = $_GET['id'];
		$db = M('Card');
		$arr = $db->find($id);
		$this->assign('data',$arr);
		$this->display();
	}
}