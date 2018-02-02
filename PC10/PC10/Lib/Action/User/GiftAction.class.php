<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GiftAction extends UserAction {

    //添加奖品列表
    public function index() {
        $id = $this->_get("id", "intval"); //活动id
        $count = M("wxgift")->where(array("lid" => $id))->count();
        $page = new Page($count, 20);
        $data = M("wxgift")->where(array("lid" => $id))->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign("page", $page->show());
        $this->assign("id", $id);
        $this->assign("data", $data);
        $this->display();
    }

    //添加奖品
    public function add() {
        if (IS_POST) {
            $model = M("wxgift");
            if ($model->create()) {
                if ($model->add($_POST)) {
                    $this->success2("添加奖品成功", U("Gift/index", array("id" => $_POST['lid'])));
                }else{
                    $this->error2("添加失败，请重试");
                }
            } else {
                $this->error2($model->getError(), U("Gift/index", array("id" => $_POST['lid'])));
            }
        } else {
            $id = $this->_get("id", "intval"); //活动id
            $this->assign("id", $id);
            $this->display();
        }
    }

    public function save() {
        if (IS_POST) {
            $id = $_POST['gid']; //修改奖品id
            $data = M("wxgift")->field("lid")->where(array("id" => $id))->find();
            $lid = $data['lid'];
            if (M("wxgift")->where(array("id" => $id))->save($_POST)) {
                $this->success2("更新成功", U("Gift/index", array("id" => $lid)));
            }else{
                $this->error2("更新失败，请重试");
            }
        } else {
	        $id = $this->_get("id", "intval"); //修改奖品id
            $data = M("wxgift")->where(array("id" => $id))->select();
            $this->assign("data", $data);
            $this->display();
        }
    }

    public function del() {
        $id = $this->_get("id", "intval");
        $data = M("wxgift")->field("lid")->where(array("id" => $id))->select();
        $lid = $data[0]['lid'];
        if (M("wxgift")->where(array("id" => $id))->delete()) {
            $this->success2("删除成功", U("Gift/index", array("id" => $lid)));
        }else{
            $this->error2("删除失败，请重试",U("Gift/index", array("id" => $lid)));
        }
    }

}
