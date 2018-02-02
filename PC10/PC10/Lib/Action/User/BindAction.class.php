<?php
class BindAction extends UserAction{
    function index(){
        $id=$this->_get('id','intval');
        $token=$this->_get('token','trim');
        $info=M('Wxuser')->find($id);
        if(!empty($token)){
            $wxuser = M('Wxuser')->where(array('token'=>$token))->find();
            if($wxuser['status'] == 1){
                $this->assign('is_open',1);
            }else{
                $this->assign('is_open',2);
            }
        }else{
            $this->assign('is_open',2);
        }
        session('token',$token);
        session('wxid',$info['id']);
        //第一次登陆　创建　功能所有权
        $token_open=M('Token_open');
        $toback=$token_open->field('id,queryname')->where(array('token'=>session('token'),'uid'=>session('uid')))->find();
        $open['uid']=session('uid');
        $open['token']=session('token');
        //遍历功能列表
        $group=M('User_group')->field('id,name')->where('status=1')->select();
        $check=explode(',',$toback['queryname']);
        $this->assign('check',$check);

        $this->display();
    }

    public function closeBind(){
        if(M('Wxuser')->where(array('token'=>session('token')))->data(array('status'=>0))->save()){
            $this->success('解除绑定成功',U('Bind/index'));
        }else{
            $this->error('解除绑定失败',U('Bind/index'));
        }
    }

    function auto(){
        Vendor('weixin.WX_Remote_Opera');
        $username=$this->_post('username','trim');
        $password=$this->_post('password','trim');
        $userModel = M("Wxuser");
        $ro = new WX_Remote_Opera();
        $token=$ro->test_login($username,$password);
        //$token=true;
        if(!$token){
            echo $this->ajaxReturn(array('code'=>'-1','msg'=>'用户名或者密码错误请重试'));
            return false;
        }else{
            if($token!=''){
                $ro->init($username,$password);
                //Array ( [wx_account] => diaobaojiecao [fakeid] => 3083415613 [nickname] => 屌爆段子 [ghid] => gh_8da4455c132d )
                $info=$ro->get_account_info();
                //$info=true;
                if(!$info){
                    echo $this->ajaxReturn(array('code'=>'-1','msg'=>'绑定失败请重试点击联系客服绑定'));
                    return false;
                }else{
                    $ro->getheadimg($info['fakeid']);
                    $ro->getqrcode($info['fakeid']);
                    $uid = session('uid');
                    $res = $userModel->where("uid=".$uid)->find();
                    $userModel->fakeid = $info['fakeid'];
                    //$userModel->fakeid = '123';
                    $userModel->uid = $uid;
                    $userModel->wx_id = $info['ghid'];
                    //$userModel->wx_id = 'gh_3e45235';
                    $userModel->wx_p = $password;
                    $my_token = md5(microtime());
                    $userModel->token = $my_token;
                    $userModel->wxname = $info['wx_account'];
                    //$userModel->wxname = 'jiecao';
                    $userModel->wx_a = $username;
                    $userModel->name = $info['nickname'];
                    //$userModel->name = '屌爆段子';
                    $userModel->status = 0;
                    $userModel->bind_time = time();
                    if(!$res){
                        $userModel->add();
                    }else{
                        if($res['status'] == 0){
                            $userModel->status = 1;
                            $userModel->token = $res['token'];
                            $userModel->save();
                        }else{
                            echo $this->ajaxReturn(array('code'=>'0','msg'=>'您已经绑定成功了的'));
                            return true;
                        }
                    }
                    $res = $ro->quick_set_api($my_token,'http://v.wapwei.com/index.php?g=Home&m=Weixin&a=index&token='.$my_token);
                    if($res['ret'] == 0){
                        echo $this->ajaxReturn(array('code'=>'0','msg'=>'绑定成功请重新登录','token'=>$my_token));
                        return true;
                    }else{
                        echo $this->ajaxReturn(array('code'=>'-1','msg'=>'绑定失败请重试点击联系客服绑定'));
                        return false;
                    }
                }
            }
        }
    }
}