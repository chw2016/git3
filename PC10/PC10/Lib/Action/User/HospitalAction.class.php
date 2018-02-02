<?php

/**

 * Created by PhpStorm.

 * User: xiao

 * Date: 2014/12/24

 * Time: 16:22

 */

class HospitalAction extends UserAction{

    public $token;

    public function _initialize() {

        parent::_initialize();

        $this->token=$this->_get("token");

        $this->assign("token",$this->token);

    }

    public function yuyuelist(){
        $db=D('Wxappoint');
        $sjHospitalModel =M("sj_hospital",null);
        $where['token']=session('token');
        $count=$db->where($where)->count();
        $page=new Page($count,15);
        $info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('atime desc')->select();
        foreach($info as $key => $value){
            $findHospitalName = $sjHospitalModel->field('name')->where(array('id'=>$value['oid']))->find();
            $info[$key]['otherName'] = $findHospitalName['name'];
        }
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->display();
    }

    public function delyuyue(){
        $where['id']=$this->_get('id','intval');
        $where['token']=session('token');
        if(M("Wxappoint")->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/yuyuelist'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/yuyuelist'));
        }
    }


    public function index(){

        $data=M("sj_hospital",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("token"=>$this->token))->select();

        $this->assign("list",$data);

        $this->display();

    }



    public function add(){

        if(IS_POST){

            if(M("sj_hospital",NULL,"mysql://root@localhost:3306/health")->add($_POST)){

                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));

            }else{

                $this->ajaxReturn(array("status"=>0,"info"=>"添加成功"));

            }

        }else{

            $this->display();

        }

    }



    public function edit(){

        if(IS_POST){

            if(M("sj_hospital",NULL,"mysql://root@localhost:3306/health")->where(array("id"=>$_POST['id']))->save($_POST)){

                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));

            }else{

                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));

            }

        }else{

            $data=M("sj_hospital",NULL,"mysql://root@localhost:3306/health")->where(array("id"=>$this->_get("id")))->find();

            $this->assign("data",$data);

            $this->display();

        }

    }



    public function del(){

        if(M("sj_hospital",NULL,"mysql://root@localhost:3306/health")->where(array("id"=>$_POST['id']))->delete()){

            $this->ajaxReturn(array("status"=>1,"info"=>"删除成功"));

        }else{

            $this->ajaxReturn(array("status"=>0,"info"=>"删除失败"));

        }

    }

}