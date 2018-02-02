<?php
/**
 * Created by PhpStorm.
 * User: anyi
 * Date: 2015/7/14
 * Time: 10:07
 */
class LaborAction extends BaseAction{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/labor';

    /**
     *  Token
     **/
    protected $_sToken = null;
    public function  _initialize(){
        $this->_sToken = $this->_get('token');
        parent::_initialize();
        /** 引入微信js接口 **/

        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();

        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);
    }

    /*劳务公开*/
    public function index(){
        $this->UDisplay('index');
    }

    /*劳务服务—政策法规*/
    public function policy(){
        $oModel = M('Labor_services');
        $list = $oModel->where(array('token'=>$this->token))->order('add_time desc')->select();
        $this->assign('list',$list);
        $this->UDisplay('policy');
    }
    /*内页*/
    public function infos(){
        $id = $_GET['id'];
        $info = M('Labor_services')->where(array('token'=>$this->token,'id'=>$id))->find();
        $this->assign('info',$info);
        $this->UDisplay('contents');
    }

    /*聊天室入口*/
    public function entrancee(){
        $this->UDisplay('entrancee');
    }

    /*验证*/
    public function chat(){
        $oModel = M('Labor_code');
        $code = $_POST['code'];
        if($oModel->where(array('code'=>$code,'token'=>$this->token))->find()){
            $this->success('欢迎进入');
        }else{
            $this->error('系统繁忙');
        }
    }
    /*聊天室*/
    public function room(){
        $this->UDisplay('room');
    }


    /*员工企业服务*/
    public function enterprise(){
        $this->UDisplay('enterprise');
    }


    /*信息上报内容*/
    public function login(){
        if(IS_AJAX){
            $code = $_POST['code'];
            $password =$_POST['password'];
            if(M('Labor_enterprise')->where(array('token'=>$this->token,'code'=>$code))->find()){
                $isfind = M('Labor_enterprise')
                    ->where(array('token'=>$this->token,'code'=>$code,'password'=>$password))
                    ->find();
                if($isfind){
                    session('user',$isfind);
                    $this->success('登陆成功！');
                }else{
                    $this->error('密码不正确！');
                }
            }else{
                $this->error('此组织机构代码不存在！');
            }
        }
        $this->UDisplay('login');
    }

    /*提示信息*/
    public function typechangs(){
        $id = $_POST['id'];
        $isTure = M('Labor_enterprise')->where(array('id'=>$id))->save($_POST);
    }
    /*企业信息*/
    public function company(){
        $user = M('Wxuser')->where(array('token'=>$this->token))->find();
        $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$this->openid))->find();
        $this->assign('user',$users);

        $sessions = $_SESSION['user'];
        $id = $sessions['id'];
        $info = M('Labor_enterprise')->where(array('id'=>$id))->find();
        $this->assign('info',$info);
        $this->UDisplay('company');
    }
    public function setcompany(){
        $user = M('Wxuser')->where(array('token'=>$this->token))->find();
        $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$this->openid))->find();
        $sessions = $_SESSION['user'];
        $id = $sessions['id'];
        $info = M('Labor_enterprise')->where(array('id'=>$id))->find();
        $this->assign('info',$info);
        $this->assign('user',$users);
        $this->UDisplay('setcompany');
    }
    /*企业信息编辑*/
    public function ajaxcompany(){

        $data = $_POST;
        $bask = json_encode($data);
        //$baskes = json_decode($bask,'ture');
        $basks = array(
            'status'=>3,
            'bask'=>$bask,
            'type'=>1
        );
        $oModel = M('Labor_enterprise');
        $isTure = $oModel->where(array('id'=>$_POST['id']))->save($basks);
        if($isTure){
            $this->success('申请成功！');
        }else{
            $this->error('系统繁忙！');
        }

    }

    /*员工申诉*/
    public function appeal(){
        $type = $_GET['type'];
        $this->assign('type',$type);
        $oAppealModel = M('Labor_appeal');
        $aList = $oAppealModel->where(array('token'=>$this->token,'openid'=>$this->openid))->select();
        $count = $oAppealModel->where(array('token'=>$this->token,'openid'=>$this->openid))->count();
        $this->assign(array(
           'count'=>$count,
            'list'=>$aList
        ));
        $this->UDisplay('appeal');
    }
    /*员工申诉提交*/
    public function ajaxappeal(){
        $oAppealModel = M('Labor_appeal');
        $_POST['add_time'] = date('Y-m-d H:i:s');
        $_POST['token'] = $this->token;
        $_POST['openid'] = $this->openid;
        $isTure = $oAppealModel->add($_POST);
        if($isTure){
            $this->success('申诉中，请等待！');
        }else{
            $this->error('系统繁忙！');
        }
    }





    /*就业服务*/
    public function obtain(){
        $this->UDisplay('obtain');
    }

    /*统一的文章列表页*/
    public function lists(){
        $list = M('Labor_text')
            ->where(array(
                'token'=>$this->token,
                'type'=>$_GET['type']))
            ->order('add_time desc')
            ->select();
        $this->assign('list',$list);
        $this->UDisplay('lists');
    }

    /*统一的内容页面*/
    public function contents(){
        $info = M('Labor_text')
            ->where(array(
                'id'=>$_GET['id']))
            ->find();
        $this->assign('info',$info);
        $this->UDisplay('contents');
    }

    /*招聘公司*/
    public function recruitment(){
        $list = M('Labor_recruitment')
            ->where(array(
                'token'=>$this->token,
            ))->select();
        $this->assign('list',$list);
        $this->UDisplay('recruitment');
    }
    /*招聘岗位列表页*/
    public function office(){
        $oModel = M('Labor_message');
        $list = $oModel
            ->where(array(
                'token'=>$this->token,
                'rid'=>$_GET['rid']
            ))->select();
        $this->assign('list',$list);
        $this->UDisplay('office');
    }
    /*招聘职位页*/
    public function position(){
        $oModel = M('Labor_message');
        $info = $oModel->where(array(
            'id'=>$_GET['oid']
        ))->find();
        $info['description'] =htmlspecialchars_decode($info['description'],ENT_QUOTES);
        $info['requirement'] =htmlspecialchars_decode($info['requirement'],ENT_QUOTES);
        $cinfo = M('Labor_recruitment')->where(array('id'=>$info['rid']))->find();
        $cinfo['abstract'] = htmlspecialchars_decode($cinfo['abstract'],ENT_QUOTES);
        $this->assign(array(
            'info'=>$info,
            'cinfo' =>$cinfo

        ));
        $this->UDisplay('position');
    }
    /*提交申述*/
    public function shenxu(){
        //echo 8;die;
        //var_dump($_POST);die();
        $db=M('Labor_appeal');
        $data=array(
            'token'=>$this->token,
            'openid'=>$this->openid,
            'name' => $this->_post('name'),
            'sex' => $this->_post('sex'),
            'barthday' => $this->_post('barthday'),
            'education' => $this->_post('Education'),
            'phone' => $this->_post('tel'),
            'info' => $this->_post('Event'),
            'company' => $this->_post('Company'),
            'identity_id'=>$this->_post('id'),
            'origin'=>$this->_post('place'),
            'return'=>$this->_post('Result'),
            'address'=>$this->_post('address'),
            'add_time'=>date("Y-m-d,H:m;s",time()),
        );
        $result=$db->add($data);

        if($result){
            $count = M('Labor_appeal')->where(array('opeid'=>$this->openid))->count();
            $list=M('Labor_appeal')->where(array('opeid'=>$this->openid))->select();
          //  var_dump($list);
            $this->assign('list',$list);
            $this->assign('count',$count);
            echo json_encode(array('status'=>1,'info'=>'申述成功'));
        }else{
            echo json_encode(array('status'=>0,'info'=>'申述失败'));
        }

    }


}