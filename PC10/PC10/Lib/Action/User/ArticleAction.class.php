<?php 
	class ArticleAction extends UserAction{
		public function index(){
			$db=M('Img');
			$where['token']=session('token');
			$data=$db->where($where)->order('click desc')->select();
			$count=$db->where($where)->count();
			// echo $count;
			/*echo "<pre>";
			print_r($data);*/
			// exit();
			$page=new Page($count,25);
			$info=$db->where($where)->order('click desc')->limit($page->firstRow.','.$page->listRows)->select();
			/*print_r($info);
			exit();*/
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->assign('data',$data);
			$this->assign('count',$count);
			$this->display();
		}
	}
?>