<?php
/**
 * Created by PhpStorm.金蝶pc端
 * User: zhang
 * Date: 2015/8/19
 * Time: 10:11
 */

class JdPcAction extends BaseAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/jdpc';

    public function _initialize()
    {

        parent::_initialize();
    }

    /*登陆*/
    public function login(){
        $model = M('Jd_user');
        if(IS_AJAX){
            $name = $_POST['name'];
            $password = md5($_POST['password']);
            //echo $name ."|".$password ."|" .$this->token;
            if($user = $model->where(array('name'=>$name,'password'=>$password,'token'=>$this->token))->find()){
                session('user',$user);
                $this->success('欢迎进入...',U('JdPc/index',array('token'=>$this->token)));
            }else{
                $this->error('账号或者密码有误');
            }
        }
        $this->UDisplay('login');
    }

    /*方案上传页*/
    public function index(){
        $model = M('Jd_wz');
        $user = $_SESSION['user'];
        if(IS_AJAX){

            $data=array(
                'tags'=>$_POST['tags'],
                'name'=>$_POST['name'],
                'title'=>$_POST['title'],
                'hy'=>$_POST['hy'],
                'type'=>$_POST['type'],
                'content'=>$_POST['content'],
                'status'=>0,
                'token'=>$this->token,
                'plan'=>$user['id'],
                'url' => $_POST['url'],
                'tel' => $_POST['tel'],
                'ld' => $_POST['ld'],
                'user_id' => $user['id'],
                'add_time'=>time()
            );
            if($model->add($data)){
                $this->success('上传失败！');
            }else{
                $this->error('上传成功！');
            }

        }
        $aIndustry = M('Jd_industry')->where(array('token'=>$this->token))->select();
        $this->assign('aIndustry',$aIndustry);
        $tag=M('jd_tag')->where(array('token'=>$this->token))->select();
        $this->assign('tag',$tag);
        $this->assign('user',$user);
        $this->UDisplay('index');
    }





}