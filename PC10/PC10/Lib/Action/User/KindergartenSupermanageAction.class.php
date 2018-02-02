<?php
/*
 * Created by zichao in 2014-09-16
 */
class KindergartenSupermanageAction extends UserAction{
    public $token;
    public $kg_super_meb;
    public function _initialize() {
        parent::_initialize();
        $this->kg_super_meb = M("Kg_super_meb");
        $this->token = session('token');
        $this->uid = session('uid');
    }
    public function index(){
        if($this->token == $_GET['token']){
            session('mm',1);
            $this->assign('mm',session('mm'));
            $w['token'] = $this->token;
            $count = $this->kg_super_meb->where($w)->count();
            $page = new Page($count,15);
            $m = $this->kg_super_meb->where($w)->limit($page->firstRow.','.$page->listRows)->order('addtime desc')->select();
	    foreach($m as $key=>$val){
	        $m[$key]['lever'] = $val['lever'] % 2;
	    }
            $this->assign('page',$page->show());
            $this->assign('info',$m);
        }
        $this->display();
    }
    
    public function register(){
	$this->kg_super_meb = M("Kg_super_meb_register");
        if($this->token == $_GET['token']){
            session('mm',1);
            $this->assign('mm',session('mm'));
            $w['token'] = $this->token;
            $count = $this->kg_super_meb->where($w)->count();
            $page = new Page($count,15);
            $m = $this->kg_super_meb->where($w)->limit($page->firstRow.','.$page->listRows)->order('addtime desc')->select();

            $this->assign('page',$page->show());
            $this->assign('m',$m);
        }
        $this->display();
    }
    
    
    public function manage(){
        if($this->token == $_GET['token']){
            $op = $_GET['op']?$_GET['op']:0;
            if(IS_POST){
                $op = $_POST['op']?$_POST['op']:0;
                $data['username'] = $_POST['username'];
                $data['name'] = $_POST['name'];
                $data['tel'] = $_POST['tel'];
                $data['pwd'] = $_POST['password'];
                $data['password'] = md5($_POST['password']);
                $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
                $data['uid'] = $this->uid;
                $data[token] = $this->token;
                if($op == 0){
                    $data['last_edit_time'] = $data['addtime'];
                    if($lastid = $this->kg_super_meb->add($data)){
                        $this->success('添加成功','index.php?g=User&m=KindergartenSupermanage&a=index&token='.$this->token);
                    } else {
                        $this->error('添加失败', 'index.php?g=User&m=KindergartenSupermanage&a=manager&op=0&token='.$this->token);
                    }
                }elseif($op == 1){
                    $w['id'] = $_GET['id'];
                    $w['uid'] = $this->uid;
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    if ($this->kg_super_meb->where($w)->save($data)) {
                        $this->success('编辑成功','./index.php?g=User&m=KindergartenSupermanage&a=index&token='.$this->token);
                    } else {
                        $this->error('编辑失败', './index.php?g=User&m=KindergartenSupermanage&a=manager&op=1&token='.$this->token);
                    }
                }
            }
            if ($op == 1) {
                $w['id'] = $_GET['id'];
                $w['uid'] = $this->uid;
                $m = $this->kg_super_meb->where($w)->find();
                $this->assign('m',$m);
            }
        }
        $this->assign('op',$op);
        $this->display();
    }
    
    public function del(){
        if($this->token == $_GET['token']){
            $w['id'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $isexist = $this->kg_super_meb->where($w)->find();
            if($isexist == true){
                //$a = M('Kindergarten')->where(array('userid'=>$w['id']))->select();
                //print_r($a);exit();
                $back = $this->kg_super_meb->where($w)->delete();
                if($back == true){
                    M('Kindergarten')->where(array('userid'=>$w['id']))->delete();
                    M('Kgteacher')->where(array('userid'=>$w['id']))->delete();
                    M('Kgspecialcourse')->where(array('userid'=>$w['id']))->delete();
                    M('Kgmy')->where(array('userid'=>$w['id']))->delete();
                    M('Kgfeedback')->where(array('userid'=>$w['id']))->delete();
                    M('Kgexcitingact')->where(array('userid'=>$w['id']))->delete();
                    $this->success('删除成功','./index.php?g=User&m=KindergartenSupermanage&a=index&token='.$this->token);
                }else{
                    $this->error('删除失败','./index.php?g=User&m=KindergartenSupermanage&a=teacher&token'.$this->token);
                }
            }
        }else{
            $this->error('删除失败','./index.php?g=User&m=KindergartenSupermanage&a=index&token='.$this->token);
        }
    }
    
    public function changestatus(){
        $this->kg_super_meb = M("Kg_super_meb_register");
	if($_GET['status']){
           $status = $_GET['status'];	
	}else{
	    $status = 0;
	}

        if($this->token == $_GET['token']){

            $w['id'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $isexist = $this->kg_super_meb->where($w)->find();
            if($isexist == true){
                //$a = M('Kindergarten')->where(array('userid'=>$w['id']))->select();
                //print_r($a);exit();
                $back = $this->kg_super_meb->where($w)->save(array('status'=>$status));
                if($back){
                    $this->success2('更新成功','./index.php?g=User&m=KindergartenSupermanage&a=register&token='.$this->token);
                }else{
                    $this->error2('更新失败','./index.php?g=User&m=KindergartenSupermanage&a=register&token'.$this->token);
                }
            }
        }else{
            $this->error2('更新失败','./index.php?g=User&m=KindergartenSupermanage&a=register&token='.$this->token);
        }
    }
    
    public function delregister(){
        $this->kg_super_meb = M("Kg_super_meb_register");
        if($this->token == $_GET['token']){
            $w['id'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $isexist = $this->kg_super_meb->where($w)->find();
            if($isexist == true){
                //$a = M('Kindergarten')->where(array('userid'=>$w['id']))->select();
                //print_r($a);exit();
                $back = $this->kg_super_meb->where($w)->delete();
                if($back == true){
                    $this->success('删除成功','./index.php?g=User&m=KindergartenSupermanage&a=register&token='.$this->token);
                }else{
                    $this->error('删除失败','./index.php?g=User&m=KindergartenSupermanage&a=register&token'.$this->token);
                }
            }
        }else{
            $this->error('删除失败','./index.php?g=User&m=KindergartenSupermanage&a=register&token='.$this->token);
        }
    }
    
    
    public function upvip(){
        if($this->token == $_GET['token']){
            if(IS_POST){
	            $postdata = (string)($_POST['userid']);
                $lever = explode("x",$postdata );
                $data['lever'] =  $lever[0] + 1;
                //print_r($lever);exit();
                if(M('Kg_super_meb')->where(array('token'=>$this->token,'id'=>$lever[1]))->save($data)){
        		    if(($lever[0] % 2) == 0){
                        $this->ajaxReturn(array('info'=>"升级成功",'status'=>1,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=index&token='.$this->token));
                    }else{
                        $this->ajaxReturn(array('info'=>"降级成功",'status'=>1,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=index&token='.$this->token));
                    }
                }else{
                    $this->ajaxReturn(array('info'=>"升级失败",'status'=>0,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=index&token='.$this->token));
                }
            }
        }
        $this->display();
    }
    public function vipdes(){
        if($this->token == $_GET['token']){
            $vipdes = $this->kg_super_meb->where(array('token'=>$this->token))->find();
            $this->assign('vipdes',$vipdes);
            if(IS_POST){
                $d['seniormember'] = $_POST['seniormember'];
                $d['ordinarymember'] = $_POST['ordinarymember'];
                $w['token'] = $this->token;
                if($this->kg_super_meb->where($w)->save($d)){
                    $this->ajaxReturn(array('info'=>"提交成功",'status'=>1,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=index&token='.$this->token));
                }else{
                    $this->ajaxReturn(array('info'=>"提交失败",'status'=>0,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=vipdes&token='.$this->token));
                }
            }
        }
        $this->display();
    }


    /*
     * Child
    */
    public function child(){
        if($this->token == $_GET['token']){
            
            $w['userid'] = $_GET['userid'];
            $w['id'] = $_GET['id'];
            $this->assign('w',$w);
            $a = M('Kg_childproducts')->where(array('token'=>$this->token))->select();
            $this->assign('a',$a);
        }
        $this->display();
    }
    public function childmanage(){
        if($this->token == $_GET['token']){
            $op = $_GET['op']?$_GET['op']:0;
            $this->assign('op',$op);
            if($op == 1){
                $a['id'] = $_GET['id'];
                $a['token'] = $this->token;
                $child = M('Kg_childproducts')->where($a)->find();
                $this->assign('child',$child);
            }
            if(IS_POST){
                $op = $_POST['op']?$_POST['op']:0;
                $data['name'] = $_POST['name'];
                $data['pic'] = $_POST['pic'];
                $data['intro'] = $_POST['intro'];
                $data['token'] = $this->token;
                //print_r($_POST);exit();
                if($op == 1){
                    $data['last_edit_time'] = date('Y-m-d H:i:s');
                    if(M('Kg_childproducts')->where(array('id'=>$_POST['id']))->save($data)){
                        $this->ajaxReturn(array('info'=>'编辑成功！','status'=>1,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=child&token='.$this->token));
                    }else{
                        $this->ajaxReturn(array('info'=>'编辑失败！','status'=>0,'url'=>'./index.php?g=User&m=KindergartenSupermanage&op=1&a=child&token='.$this->token));
                    }
                }elseif($op == 0){
                    $data['addtime'] = date('Y-m-d H:i:s');
                    $data['last_edit_time'] = date('Y-m-d H:i:s');
                    if(M('Kg_childproducts')->add($data)){
                        $this->ajaxReturn(array('info'=>'保存成功！','status'=>1,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=child&token='.$this->token));
                    }else{
                        $this->ajaxReturn(array('info'=>'保存成功！','status'=>0,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=childmanage&token='.$this->token));
                    }
                }
            }
        }
        $this->display();
    }
    public function del_child(){
        if($this->token == $_GET['token']){
            $w['id'] = $_GET['id'];
            $w['token'] = $this->token;
            if(M('Kg_childproducts')->where($w)->delete()){
                $this->ajaxReturn(array('info'=>'删除成功！','status'=>1,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=child&token='.$this->token));
            }else{
                $this->ajaxReturn(array('info'=>'删除失败！','status'=>0,'url'=>'./index.php?g=User&m=KindergartenSupermanage&a=child&token='.$this->token));
            }
        }
    }
    public function childpreview(){
        if($this->token == $_GET['token']){
            $info = M('Kg_childproducts')->where(array('token'=>$this->token,'id'=>$_GET['id']))->find();
            $info['intro'] = htmlspecialchars_decode($info['intro'],ENT_QUOTES);
            $this->assign('info',$info);
        }
        $this->display();
    }
    



















}