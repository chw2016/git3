<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ActiveAction extends UserAction {

    //显示活动列表
	public $token;
	public function _initialize(){
		parent::_initialize();
		$this->token=$this->_get("token");
		$this->assign("token",$this->token);
	}
    public function index() {
        $count = M("lottery")->where(array("token" => $this->token, "type" => 15))->count();
        $page = new Page($count, 20);
        $data = M("lottery")->field('id,title,keyword,statdate,enddate,status,click')->where(array("token" => $this->token, "type" => 15))->limit($page->firstRow . ',' . $page->listRows)->select();
	    foreach($data as $k=>$v){
		    $id=$v['id'];
		    $count=M("wxlottery")->where(array("lid"=>$id))->count();
		    $data[$k]['join']=$count;
	    }
	    $this->assign("page", $page->show());
        $this->assign("data", $data);
        $this->display();
    }

    //添加活动信息
    public function add() {
        if (IS_POST) {
            $model = D("lottery");
            $data = $_POST;
            if (strtotime($data['statdate']) > strtotime($data['enddate'])) {
                $this->error2('起始时间不能大于结束时间');
                return false;
            }
            $data['type'] = 15;
            $data['statdate'] = strtotime($data['statdate']);
            $data['enddate'] = strtotime($data['enddate']);
            $data['token'] = $this->token;
            if ($model->create() != false) {
                if ($model->add($data)) {
                    $this->success2("活动创建成功", U("Active/index", array("token" => $data['token'])));
                } else {
                    $this->error2("活动创建失败");
                }
            } else {
                $this->error2('服务器繁忙,请稍候再试');
            }
        } else {
            $this->display();
        }
    }

    //对活动进行编辑
    public function edit() {
        if (IS_POST) {
            $model = D("lottery");
            $_POST['statdate'] = strtotime($_POST['statdate']);
            $_POST['enddate'] = strtotime($_POST['enddate']);
            if ($_POST['statdate'] > $_POST['enddate']) {
                $this->error2('起始时间不能大于结束时间', U("Active/index", array("token" => $this->token)));
                return false;
            }
            if ($model->create() != false) {
                if ($model->where(array("id" => $_POST['id']))->save($_POST)) {
	                $this->success2("修改成功",U('Active/index',array('token'=>$this->token)));
                } else {
                    $this->error2('修改失败');
                }
            } else {
                $this->error2('服务器繁忙,请稍候再试');
            }
        } else {
            $id = $this->_get("id"); //获取活动id
            $data = M("lottery")->field('id,title,keyword,statdate,enddate,status,txt,sttxt,info,endtite,endinfo,starpicurl')->where(array("id" => $id))->find();
            $this->assign("data", $data);
            $this->display();
        }
    }

    //删除活动,同时删除奖品信息，中奖信息 sn码
    public function del() {
        $id = $this->_get("id"); //活动id
        if (M("lottery")->where(array("id" => $id))->delete()) {
            M("wxget")->where(array("lid" => $id))->delete();//删除中奖表信息
            M("wxgift")->where(array("lid" => $id))->delete();//删除活动奖品表信息
            M("wxinfo")->where(array("lid" => $id))->delete();//删除领取奖品表信息
	        $this->success2("活动删除成功", U("Active/index", array("token" => $this->token)));
        } else {
            $this->error2("活动删除失败", U("Active/index", array("token" => $this->token)));
        }
    }

    //中奖管理svn
    public function sn() {
        $lid=$this->_get("id","intval");
	$count=M("wxget")->join("join tp_wxgift on tp_wxget.gid=tp_wxgift.id join tp_wxinfo on tp_wxget.uid=tp_wxinfo.uid")->where(array('tp_wxget.lid'=>$lid))->count();
        $page=new Page($count,20);
        $data=M("wxget")->field("tp_wxget.id,tp_wxget.sn,tp_wxget.status,tp_wxgift.gname,tp_wxget.gtime,tp_wxinfo.uname,tp_wxinfo.tel,tp_wxinfo.address,tp_wxinfo.seng,tp_wxinfo.si,tp_wxinfo.xian")->join("join tp_wxgift on tp_wxget.gid=tp_wxgift.id join tp_wxinfo on tp_wxget.uid=tp_wxinfo.uid")->where(array('tp_wxget.lid'=>$lid))->limit($page->firstRow,$page->listRows)->select();
        $this->assign("count",$count);
	$this->assign("lid",$lid);
        $this->assign("data",$data);
        $this->assign("page",$page->show());
        $this->display();
    }
    
    public function getGift(){
       if(M("wxget")->where(array("id"=>$this->_get("id")))->save(array('status'=>1))){
             $this->success2("已领取",U('Active/sn',array('id'=>$this->_get("lid"))));
       }else{
             $this->error2("领取失败");
       }
    }
    
    public function uniq($data){
    	foreach ($data as $k => $v) {
    		if($v['sn']==$data[$k+1]['sn']){
    			unset($data[$k+1]);
    		}
    	}
    	return $data;
    }

    //活动开始修改状态
    public function start() {
        $id = $this->_get("id", "intval");
        if (M("lottery")->where(array("id" => $id))->save(array("status" => 1))) {
            $this->success2("活动开始", U("Active/index", array("token" => $this->token)));
        }
    }

    //活动结束修改状态
    public function end() {
        $id = $this->_get("id", "intval");
        if (M("lottery")->where(array("id" => $id))->save(array("status" => 2))) {
            $this->success2("活动已经结束", U("Active/index", array("token" => $this->token)));
        }
    }
}
