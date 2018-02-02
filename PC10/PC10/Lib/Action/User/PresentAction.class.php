<?php
class PresentAction extends UserAction{
	//奖品设置列表
	public function index(){
		// echo 123;
		$id=$this->_get("id","intval");//活动id
		$data=M("wxgift")->where(array("id"=>$id))->count();
		$page = new Page($count, 20);
		$data = M("wxgift")->where(array("lid" => $id))->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign("page", $page->show());
		$this->assign("id", $id);
		$this->assign("data", $data);
		$this->display();
	}

	public function add(){
		if (IS_POST) {
			$model = M("wxgift");
			if ($model->create()) {
				if ($model->add($_POST)) {
					$this->success2("添加奖品成功", U("Present/index", array("id" => $_POST['lid'])));
				}else{
					$this->error2("添加失败，请重试");
				}
			} else {
				$this->error2($model->getError(), U("Present/index", array("id" => $_POST['lid'])));
			}
		} else {
			$id = $this->_get("id", "intval"); //活动id
			$this->assign("id", $id);
			$this->display();
		}
	}

	public function edit(){
		if (IS_POST) {
			$id = $_POST['id']; //修改奖品id
			$data = M("wxgift")->field("lid")->where(array("id" => $id))->find();
			$lid = $data['lid'];
			if (M("wxgift")->where(array("id" => $id))->save($_POST)) {
				$this->success2("修改成功", U("Present/index", array("id" => $lid)));
			}else{
				$this->error2("修改失败，请重试");
			}
		} else {
			$id = $this->_get("id", "intval"); //修改奖品id
			$data = M("wxgift")->where(array("id" => $id))->find();
			$this->assign("data", $data);
			$this->display();
		}
	}

	public function del(){
		$id = $this->_get("id", "intval");
		$data = M("wxgift")->field("lid")->where(array("id" => $id))->find();
		if (M("wxgift")->where(array("id" => $id))->delete()) {
			$this->success2("删除成功", U("Present/index", array("id" => $data['lid'])));
		}else{
			$this->error2("删除失败，请重试",U("Present/index", array("id" => $data['lid'])));
		}
	}
}