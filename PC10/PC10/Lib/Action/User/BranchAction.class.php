<?php
/*
 * Created by 訾超 in 2014-09-15
 *
 * 这个控制器类是用来获取模块名和方法名称的
 */
class BranchAction extends BaseAction{
    /*
     * Home page
     */
    public function index(){
        /*
         * Judge which one application of WPPT(万普平台)
         */

        $modulename = $_REQUEST['modulename'];
         $token = $_REQUEST['token'];
        //echo $token;
        $branch = C('Branch');
        if(array_key_exists($modulename,$branch)){
            $act = $branch[$modulename];
            //print_r($act);
            $module = $act['m'];
            $action = $act['a'];
            $branchid = $act['branchid'];
            //echo $module;
        }
        /*
         * 分店接口登录要用到这个控制器，这里要统一一下数据表中的登录名和登录密码的字段 ，
         * 所以希望在做有分店或者有用到这个控制器的项目之前，先看看这里，把表中的登录名和密码的字段改成和如下一样的，否则会影响到其他项目
         *
         */
        if(IS_POST){
            $w['username'] = $_POST['user_name'];
            $w['password'] = md5($_POST['password']);
            //print_r($w);exit();
            $token = $_POST['token'];
            $branchid = $_POST['branchid'];
            $action = $_POST['action'];
            $module = $_POST['module'];
            $modulename = $_POST['modulename'];
            //echo $token;exit();
            $data = M($modulename)->where(array('username'=>$w['username'],'password'=>$w['password'],'token'=>$token))->find();
            session('token',$token);
            $a = M('Wxuser')->where(array('token'=>$token))->find();
            session('username',$w['username']);
            session('fakeid',$a['fakeid']);
            session('name',$a['name']);
            session('uid',$a['uid']);
            if($data){
                //$a = './index.php?g=User&m='.$module.'&a='.$action.'&token='.$token.'&'.$branchid.'='.$data['id'];
                //echo $a;
                session($branchid,$data['id']);
                session('padmin',1);
                //张湘南
                session('aid',$data['id']);
                session('modulename',$_REQUEST['modulename']);

                $this->success('登陆成功','./index.php?g=User&m='.$module.'&a='.$action.'&token='.$token.'&'.$branchid.'='.$data['id'].'&padmin=1'.'&modulename='.$_REQUEST['modulename']);
            }else{
                $this->error('账号或密码错误','./index.php?g=User&m=Branch&a=index&token='.$token);
            }
                if($_GET['yuyue']==123){
                    echo 1;exit;
                }
        }else{
            $this->assign('action',$action);
            $this->assign('branchid',$branchid);
            $this->assign('token',$token);
            $this->assign('module',$module);
            $this->assign('modulename',$modulename);
            $this->display();
        }
    }

    Public function mru(){
        if(IS_POST){
    		$list=M('mru_mdian')->where(array('username'=>$_POST['user_name'],'password'=>Md5($_POST['password'])))->find();
    		if(!$list) script("帐号或密码错误");
    		//如果不把这些存session就登不进后台
    		$a = M('Wxuser')->where(array('token'=>'e756d6be1ec4fab3c5920f3a3437160b'))->find();
    		session('fakeid',$a['fakeid']);
    		session('name',$a['name']);
    		session('uid',$a['uid']);

    		$_SESSION['token']='e756d6be1ec4fab3c5920f3a3437160b';
    		$_SESSION['aid']=$list['id'];
    		script("","Dianphd/ck");
    	}else{

    		$this->display();
    	}
    }
}