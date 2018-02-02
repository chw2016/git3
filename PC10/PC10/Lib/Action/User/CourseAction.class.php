<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/3
 * Time: 8:50
 */
class CourseAction extends UserAction{

    /*课程列表*/
   public function index(){
        $count = M('Context_list')->where(array('token'=>$this->token))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $list = M('Context_list')->where(array('token'=>$this->token))->limit($Page->firstRow.','.$Page->listRows)->order('addtime desc')->select();
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
   }
    /*添加修改课程*/
    public function  addcourse(){
    	
        $operation = $_GET['op']?$_GET['op']:0;
        if(IS_AJAX){
            
            $_POST['token'] = $this->token;
            $_POST['zl']=implode($_REQUEST['zl'],',');
           
            if($operation == 0){
       		$_POST['addtime'] = time();
                if(M('Context_list')->data($_POST)->add()){
                    $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
                }else{
                    $this->error('操作失败！',U(MODULE_NAME.'/addcourse',array('token'=>session('token'))));
                }
            }elseif($operation ==1){
       
                if(M('Context_list')->where(array('token'=>$this->token,'id'=>$_GET['lid']))->save($_POST)){
                    $this->success('编辑成功！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
                }else{
                    $this->error('编辑失败！',U(MODULE_NAME.'/addcourse',array('token'=>session('token'),'lid'=>$_GET['lid'])));
                }
            }
        }else{
            if($operation == 1){
                $list = M('Context_list')->where(array('token'=>$this->token,'id'=>$_GET['lid']))->find();
                //选择该项目需镇的资料 修改默认选中
                $zl=explode(',',$list['zl']);
                $this->assign('zl',$zl);
                $this->assign('list',$list);
                
            }
            $this->assign('op',$operation);
            //选择该项目需镇的资料
            //M('fieow')->getDbfied();
           // $list=M('context_shop')->getDbFields();
            $listZd=M('context_shop')->query("select column_name from information_schema.columns where table_schema='weikucms' and table_name='tp_context_shop'");
            $this->assign('listZd',$listZd);
             ##数据库名
             
            

            
            
            $this->display();
        }
     
    }
    /*删除课程*/
    public function delcourse(){
        $is_list = M('Context_list')->where(array('id'=>$_GET['lid'],'token'=>$this->token))->find();
        if($is_list){
            $back = M('Context_list')->where(array('id'=>$_GET['lid'],'token'=>$this->token))->delete();
            if($back){
                $this->success('删除成功！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
            }else{
                $this->error('删除失败！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
            }
        }
    }

    /*报名成员查看*/
    public function member(){
      
	$cid = $_GET['lid'];
        $contextshop = M('Context_shop');
        $count = $contextshop->where(array('token'=>$this->token,'cid'=>$cid))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        if($cid){
            $list = $contextshop->where(array('token'=>$this->token,'cid'=>$cid))->limit($Page->firstRow.','.$Page->listRows)->order('time desc')->select();
        }else{
            $list = $contextshop->where(array('token'=>$this->token))->limit($Page->firstRow.','.$Page->listRows)->order('time desc')->select();
        }
        foreach($list as $key=>$value){
            $nuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $nusers = M('Wxusers')->where(array('uid'=>$nuser['id'],'openid'=>$value['openid']))->find();
            $list[$key]['nickname'] = $nusers['nickname'];
            $courses = M('Context_list')->where(array('token'=>$this->token,'id'=>$value['cid']))->find();
            $list[$key]['title'] = $courses['title'];
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
}