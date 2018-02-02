<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-9-10
 * Time: 上午10:11
 * To change this template use File | Settings | File Templates.
 */
class FlagAction extends BaseAction{

    public $wxuser = null;

    public function _initialize(){
        parent::_initialize();
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $this->wxuser = $wxuser;
        $this->assign('wxuser',$wxuser);
    }

    public function index(){
        $this->display();
    }

    public function getFlagdata(){
        if(IS_POST){
            $where=array();
            $where['uid'] = array(array('eq',$this->wxuser['uid']),'and');
            if($_POST['reg_num']){
                $where['reg_num'] = array(array('eq',$_POST['reg_num']),'or');
            }
            if($_POST['apply_person']){
                $where['apply_person'] = array(array('eq',$_POST['apply_person']),'or');
            }
            if($_POST['flag_name']){
                $where['flag_name'] = array(array('eq',$_POST['flag_name']),'or');
            }
            $flagres = M('Flag')->field('id')->where($where)->select();
            $idsarr = array();
            foreach($flagres as $v){
                $idsarr[] = $v['id'];
            }
            if($flagres){
                echo $this->encode(array('code'=>0,'msg'=>'查到了正在进入...','data'=>implode('|',$idsarr)));
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'未能查询到信息哦'));
            }
        }
    }

    public function listdata(){
        $ids = $_GET['ids'];
        if($ids){
            $idarr = explode('|',$ids);
            $where['uid'] = $this->wxuser['uid'];
            $where['id'] = array('in',$idarr);
            $flaglistres = M('Flag')->where($where)->select();
            $this->assign('flaglistres',$flaglistres);
            $this->display();
        }
    }

    public function result(){
        $flagres = M('Flag')->where(array('uid'=>$this->wxuser['uid'],'id'=>$_GET['id']))->find();
        $this->assign('flagres',$flagres);
        $this->display();
    }










}