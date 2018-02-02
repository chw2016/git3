<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-9-11
 * Time: 上午9:40
 * To change this template use File | Settings | File Templates.
 */
class LightappAction extends UserAction{

    public function index(){
        $data = M('Lightapp')->where(array('uid'=>session('uid')))->select();
        $this->assign('info',$data);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            $_POST['uid'] = session('uid');
            $_POST['token'] = $this->token;
            $_POST['add_time'] = time();
            if(M('Lightapp')->add($_POST)){
                $this->success('操作成功',U(MODULE_NAME.'/index'));
            }else{
                $this->error('操作失败',U(MODULE_NAME.'/add'));
            }
        }else{
            $this->display();
        }
    }

    public function edit(){
        if(IS_POST){
            $id = $_POST['id'];
            unset($_POST['id']);
            if(M('Lightapp')->where(array('uid'=>session('uid'),'id'=>$id))->save($_POST)){
                $this->success('操作成功',U(MODULE_NAME.'/index'));
            }else{
                $this->error('操作失败',U(MODULE_NAME.'/edit'));
            }
        }else{
            $lightappdata = M('Lightapp')->where(array('uid'=>session('uid'),'id'=>$_REQUEST['id']))->find();
            $this->assign('lightappdata',$lightappdata);
            $this->display();
        }

    }

    public function del(){
        if(M('Lightapp')->where(array('uid'=>session('uid'),'token'=>$this->token,'id'=>$_REQUEST['id']))->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/index'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/index'));
        }

    }




}