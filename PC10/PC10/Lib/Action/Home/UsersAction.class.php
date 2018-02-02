<?php
class UsersAction extends Action{

    public function  _initialize(){
        define('RES', C('site_url').THEME_PATH . 'common');
        define('STATICS', C('site_url').TMPL_PATH . 'static');
    }

	public function index(){
		header("Location: /");
	}

	public function checklogin(){
      //  echo 4;die;
		$db=D('Users');
		//print_r($db);die();
       // p($_POST);die;
		$where['username']=$this->_post('username','trim');		
		$pwd=$this->_post('password','trim,md5');
		$res=$db->where($where)->find();
      //  p($res['id']);die;
       // p($res);die;
       // echo $res['id'];die;
		if($res&&($pwd===$res['password'])){
			if($res['status']==0){
				$this->error('请联系在线客户，为你人工审核帐号');exit;
			}
            if(time() > $res['viptime']){
                $this->error('您的账号已到期,请联系在线客户');exit;
            }
            $userModel = M('Wxuser');
            $wxuser = $userModel->where(array('uid'=>$res['id']))->find();
            if(($wxuser['token']=='5d8a87bab30de695954b17fc835b9d12')OR($wxuser['token']=='36462b4a0fac12ef6ae630e398759ea9')){
                session('gta_cg',1);//国泰安超级管理员
            }
            session('token',$wxuser['token']);
			session('uid',$res['id']);
			session('gid',$res['gid']);
			session('uname',$res['username']);
			session('name',$wxuser['name']);
			session('fakeid',$wxuser['fakeid']);
			$info=M('user_group')->find($res['gid']);
			session('diynum',$res['diynum']);
			session('connectnum',$res['connectnum']);
			session('activitynum',$res['activitynum']);
			session('viptime',$res['viptime']);
			session('gname',$info['name']);
			$tt=getdate();
			if($tt['mday']===1){
				$data['id']=$res['id'];
				$data['imgcount']=0;
				$data['textcount']=0;
				$data['musiccount']=0;
				$data['activitynum']=0;
				$db->save($data);
			}
			
			//张湘南
			if($_POST['jz']){
				//选择记住密码，就把帐号密码存cookie
				if($_POST['username']=='keller@wapwei.com'){
					$serializedata=serialize(array('username'=>$_POST['username'],'password'=>$_POST['password']));//序列化数组
					setcookie("serializedata",$serializedata, time()+3600*1640);
				} 

			}else{
				//没有选择记住密码,就清除cookie，不然cookie里面的值一直存在，就一直能分配到模板中显示，既使没有选择记住密码也能看到帐号密码显示出来
				$unserializedata=unserialize($_COOKIE['serializedata']);//反序列化
				if($_POST['username']==$unserializedata['username']){
					setcookie('serializedata',time()-1);
				}
			}
			
			$this->success('登录成功',U('index.php?g=User&m=Home&a=index'));
		}else{
            //新增用户管理 员帐号
            $where['username']=$this->_post('username','trim');
            $pwd=$this->_post('password','trim');

            if($res1=M('App_users')->where(array('username'=>$where['username'],'password'=>$pwd))->find()){
                //这里设置app_id存到session
                session('app_id',$res1['app_id']);
                $res=$db->find($res1['wxuser_uid']);
                $userModel = M('Wxuser');
                $wxuser = $userModel->where(array('uid'=>$res['id']))->find();
                if($res1['cw']){//国泰安财务权限
                    session('gta_cw',$res1['cw']);
                }
                session('token',$wxuser['token']);
                session('uid',$res['id']);
                session('gid',$res['gid']);
                session('uname',$res['username']);
                session('name',$wxuser['name']);
                session('fakeid',$wxuser['fakeid']);
                $info=M('user_group')->find($res['gid']);
                session('diynum',$res['diynum']);
                session('connectnum',$res['connectnum']);
                session('activitynum',$res['activitynum']);
                session('viptime',$res['viptime']);
                session('gname',$info['name']);

                $tt=getdate();
                if($tt['mday']===1){
                    $data['id']=$res['id'];
                    $data['imgcount']=0;
                    $data['textcount']=0;
                    $data['musiccount']=0;
                    $data['activitynum']=0;
                    $db->save($data);
                }

                $this->success('登录成功',U('index.php?g=User&m=Home&a=index'));
            }else{
                $this->error('帐号密码错误',U('Index/login'));

            }
		}

	}

	public function checkreg(){
		$db=D('Users');
        C('TOKEN_ON',false);
		$info=M('User_group')->find(1);
        $_POST['viptime'] = time()+3600*24*7;
        $_POST['status'] = 1;
        $_POST['is_join'] = 0;
		if($db->create()){
			$id=$db->add();
			if($id){
                /*
				if(C('ischeckuser')){
					$this->success('注册成功,请联系在线客服审核帐号',U('User/Index/index'));exit;
				}
                */
				session('uid',$id);
				session('gid',1);
				session('uname',$_POST['username']);
				session('diynum',0);
				session('connectnum',0);
				session('activitynum',0);
				session('gname',$info['name']);


				$this->success('注册成功',U('User/Index/index'),false);
			}else{
				$this->error('注册失败','http://www.wapwei.com/zhuce',false);
			}
		}else{
			$this->error($db->getError(),'http://www.wapwei.com/zhuce',false);
		}
	}

    public function newcheckreg(){
        $db=D('Users');
        C('TOKEN_ON',false);
        $info=M('User_group')->find(1);
        $_POST['viptime'] = time()+3600*24*365;
        $_POST['status'] = 1;
        $_POST['is_join'] = 0;
        $_POST['username'] =$_GET['username'];
        $_POST['password'] = $_GET['password'];
        $_POST['phone'] = $_GET['phone'];
        $callback = $_GET['callback'];
        $isuser = $db->where(array('username'=>trim($_GET['username'])))->find();
        if($isuser){
            echo "{$callback}({'msg':'用户名已存在','code':'-1'})";exit;
        }else{
            if($db->create()){
                $id=$db->add();
                if($id){
                    /*
                    if(C('ischeckuser')){
                        $this->success('注册成功,请联系在线客服审核帐号',U('User/Index/index'));exit;
                    }
                    */


                    $wxuserModel = M("Wxuser");
                    $wxdata['uid'] = $id;
                    $wxdata['token'] = md5(microtime());
                    $wxdata['tpltypeid'] = 12;
                    $wxdata['tpltypename'] = 'tpl_154_index_new';
                    $wxdata['tpllistid'] = 1;
                    $wxdata['tplcontentname'] = 'new4_content';
                    $wxdata['status'] = 0;
                    $wxdata['tplchannelid'] = 5;
                    $wxdata['tplchannelname'] = 'new5_channel';
                    $wxdata['is_auth'] = 0;
                    $wxuserModel->add($wxdata);


                    session('uid',$id);
                    session('gid',1);
                    session('uname',$_POST['username']);
                    session('diynum',0);
                    session('connectnum',0);
                    session('activitynum',0);
                    session('gname',$info['name']);
                    session('token',$wxdata['token']);


                    echo "{$callback}({'msg':'注册成功','code':'0'})";
                }else{
                    echo "{$callback}({'msg':'注册失败','code':'-2'})";
                }
            }else{
                echo "{$callback}({'msg':'注册失败','code':'-3'})";
            }
        }
    }

	public function checkpwd(){

		$where['username']=$this->_post('username');
		$where['email']=$this->_post('email');
		$db=D('Users');
		$list=$db->where($where)->find();
		if($list==false) $this->error('邮箱和帐号不正确',U('Index/regpwd'));

		$smtpserver = C('email_server');
		$port = C('email_port');
		$smtpuser = C('email_user');
		$smtppwd = C('email_pwd');
		$mailtype = "TXT";
		$sender = C('email_user');
		$smtp = new Smtp($smtpserver,$port,true,$smtpuser,$smtppwd,$sender);
		$to = $list['email'];
		$subject = C('pwd_email_title');
		$code = C('site_url').U('Index/resetpwd',array('uid'=>$list['id'],'code'=>md5($list['id'].$list['password'].$list['email']),'resettime'=>time()));
		$fetchcontent = C('pwd_email_content');
		$fetchcontent = str_replace('{username}',$where['username'],$fetchcontent);
		$fetchcontent = str_replace('{time}',date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']),$fetchcontent);
		$fetchcontent = str_replace('{code}',$code,$fetchcontent);
		$body=$fetchcontent;
		//$body = iconv('UTF-8','gb2312',$fetchcontent);
		$send=$smtp->sendmail($to,$sender,$subject,$body,$mailtype);
		$this->success('请访问你的邮箱 '.$list['email'].' 验证邮箱后登录!<br/>');

	}

	public function resetpwd(){
		$where['id']=$this->_post('uid','intval');
		$where['password']=$this->_post('password','md5');
		if(M('Users')->save($where)){
			$this->success('修改成功，请登录！',U('Index/login'));
		}else{
			$this->error('密码修改失败！',U('Index/index'));
		}
	}

}
