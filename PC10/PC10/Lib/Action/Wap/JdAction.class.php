<?php
class JdAction extends BaseAction{
    public $_sTplBaseDir = 'Wap/default/Jd';
    static $treeList = array();
    // 初始化_initialize
    public function _initialize(){
        if(in_array(ACTION_NAME,array('detailed','activeinfo'))){
            if(!IS_AJAX){
                $this->autoShare = true;
            }
        }
        parent::_initialize();
        $this->token=session('wtoken');
        session('token',session('wtoken'));
        $xx=M('jd_xx')->find();
        $this->xx=$xx;
        $this->assign('xx',$xx);
	$this->User = M('Jd_user')->where(array('name' => $_COOKIE['user']))->find();
        $this->assign('User', $this->User);

        /*
         * 引入微信js接口
        */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);

        //行业
        $types=M('jd_industry')->select();
        $this->assign('types',$types);
        //gu问类型
        $adv_cate=M('jd_adv_cate')->select();
       	$this->assign('adv_cate',$adv_cate);
        $this->assign('huodong', $_GET['type'] == 1);
    }


    //首页，方案列表
    public function index(){
        //此页无实际意义，直接跳转至方案搜索页
        $this->redirect(U('search',array('token'=>session('token'),'openid'=>$this->openid)));
        $page=$_POST['p'];
        empty($page) ? $p=0 : $p=$page;
        $list=M('jd_wz')->where(array('token'=>$this->token))->limit($p,$p+5)->select();
        foreach($list as $k=>$v){
            $tagId=explode(',',$v['tags']);
            $v['tag']=M('jd_tag')->field('name,color')->where(array('id'=>array('in',$tagId)))->select();
            $arr[$k]=$v;
        }
        $this->assign('list',$arr);
        $this->UDisplay();
    }
    //搜索功能
/*     public function search(){

        $hy=$_POST['hy'];//行业
        $tag=$_POST['tag'];//标签
        $word=$_POST['word'];//搜索关键词
        $map['token']=session('wtoken');
        if(!empty($hy)) $map['hy']=$hy;
        if(!empty($tag)){
            $map['tags']=array('like','%'.$tag.'%');
        }
        if(!empty($word)){
            $map['content']=array('like','%'.$word.'%');
        }
        $map['status']=1;
        if($_GET['type']) $map['type']=$_GET['type'];

        $page=$_POST['p'];
        empty($page) ? $p=0 : $p=$page;
        $list=M('jd_wz')->where($map)->limit($p,$p+5)->order("sort")->select();
        foreach($list as $k=>$v){
            $tagId=explode(',',$v['tags']);
            $v['tag']=M('jd_tag')->field('name,color')->where(array('id'=>array('in',$tagId)))->select();
            $arr[$k]=$v;
        }
        // 读取标签信息
        $tags= M('jd_tag')->where(array('token'=>$this->token))->field('id,name')->select();
        //从文章列表中读取不重复的行业信息
        $hy=M('jd_wz')->where(array('token'=>$this->token))->field('hy')->group('hy')->select();

        if(IS_POST){
            if(!empty($list)){//有搜索结果
                $this->assign('list',$arr);
                $rsStr=$this->fetch('./tpl/Wap/default/Jd/searchLi.html');
                $this->ajaxReturn(array('status'=>1,'rs'=>$rsStr));
            }else{//没有搜索结果
                $this->ajaxReturn(array('status'=>0,'msg'=>'没有符合条件的记录！'));
            }
        }else{
            $this->assign('tags',$tags);
            $this->assign('list',$arr);

            $this->assign('hy',$hy);
            $this->UDisplay();
        }
    } */
    //$tags 是个id字符串：比如21,22,23
    public function tag($tags){
    	$tags=explode(',', $tags);
    	$tag=M('jd_tag')->where(array('id'=>array(in,$tags)))->select();
    	$str='';
    	foreach ($tag as $v){
    		$str.=$v['name'].",";
    	}
    	$str=rtrim($str,',');
    	return  $str;
    }

    //方案详情
    public function detailed(){
        if(IS_POST){
            M('jd_wz')->where(array('id'=>$_GET['id']))->setInc('fx_num',1);
        }else{
            $id=$_GET['id'];
            if(empty($id)) script("参数无效！");
            $id=intval($id);
            $detail=M('jd_wz')->where(array('token'=>$this->token,id=>$id))->find();
            $User = M('Jd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
			/*
			  未找到微信openid  继续找用户登录id
			*/
			if(!$User){
				$User = M('Jd_user')->where(array('token'=>$this->token,'name'=>$_COOKIE['user']))->find();
			}
            if($detail['fw']==1){
                if($User['is_login']!=1){
                    $_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
                    script("圈内私享，请关注‘周公犯困’并登录或注册！",'login',array('token'=>$this->token,'openid'=>$this->openid,'type'=>$_GET['type']));
                }
            }elseif($detail['fw']==3){
                if($User['is_login']!=1){

                    $_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
                    script("圈内VIP私享，请关注‘周公犯困，了解圈友权益，注册或登录！",'login',array('token'=>$this->token,'openid'=>$this->openid,'type'=>$_GET['type']));
                }else if($User['grade'] != 1){
                    script("圈内VIP私享，请关注‘周公犯困，了解圈友权益！",'index',array('token'=>$this->token,'openid'=>$this->openid,'type'=>$_GET['type']));
                }
            }
            if($detail['fw']==1 || $detail['fw']==3){
                if($User['is_show'] ==0 || $User['state'] == 0){
                    script("用户已被禁用或者反审，请联系管理员！",'index',array('token'=>$this->token,'openid'=>$this->openid,'type'=>$_GET['type']));
                }
            }
            $info = M('jd_xx')->where(array('token'=>$_SESSION['token']))->find();
            $data = $this->lastrecord($_GET['id'],$User['name'],'plan'); //点赞状态
            $evaluate = $this->evaluatelist($_GET['id'],'plan');
            //P($evaluate);
            $evacount = count($evaluate);
            $this->assign(array(
                'info'=>$info,
                'data'=>$data,
                'evaluate'=>$evaluate,
                'evacount'=>$evacount
            ));
            $this->assign('detail',$detail);
            $this->assign('str',$this->tag($detail['tags']));
            //标签
            $this->UDisplay();
        }

    }

    //检查权限
    public function check_detailed(){
        $id=$_POST['id'];
        if(empty($id)) {
            return $this->redirect(U('Jd/uCenter',array('token'=>$this->token,'openid'=>$this->openid)));
        }
        $id=intval($id);
        $detail=M('jd_wz')->where(array('token'=>$this->token,id=>$id))->find();
	    $User = M('Jd_user')->where(array('name' =>$_COOKIE['user']))->find();
		/*
			未找到微信openid  继续找用户登录id
		*/
		if(!$User){
			$User = M('Jd_user')->where(array('token'=>$this->token,'name'=>$_COOKIE['user']))->find();
		}
		
        if($detail['fw']==1){
            if(!$_SESSION['is_login']==1){
                $_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
                exit(json_encode(array(
                    'code' => 1,
                    'msg' => "圈内私享，请关注‘周公犯困’并登录或注册！",
                    'url' => U('login',array('token'=>$this->token,'openid'=>$this->openid,'type'=>$_GET['type']))
                )));
            }
        }elseif($detail['fw']==3){
            if(!$_SESSION['is_login']==1){

                $_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
                exit(json_encode(array(
                    'code' => 1,
                    'msg' => "圈内VIP私享，请关注‘周公犯困，了解圈友权益，注册或登录！",
                    'url'   => U('login',array('token'=>$this->token,'openid'=>$this->openid,'type'=>$_GET['type']))
                )));
            }else if($User['grade'] != 1){
                exit(json_encode(array(
                    'code' => 2,
                    'msg' => "圈内VIP私享，请关注‘周公犯困，了解圈友权益！",
                    'url'   => U('index',array('token'=>$this->token,'openid'=>$this->openid,'type'=>$_GET['type']))
                )));
            }
        }
        if($detail['fw']==1 || $detail['fw']==3){
            if($User['is_show'] ==0 || $User['state'] == 0){
                exit(json_encode(array(
                    'code' => 2,
                    'msg' => "用户已被禁用或者反审，请联系管理员！",
                    'url'   => U('index',array('token'=>$this->token,'openid'=>$this->openid,'type'=>$_GET['type']))
                )));
            }
        }
        exit(json_encode(array(
            'code' => 0,
            'msg'  => '',
            'url'  => ''
        )));
    }


    //方案详情
    public function recoms2(){


    	$id=$_GET['id'];
    	if(empty($id)) script("参数无效！");
    	$id=intval($id);
    	$detail=M('jd_wz')->where(array('token'=>$this->token,id=>$id))->find();
        $User = M('Jd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        if($detail['fw']==1){
            if($User['is_login']!=1){
                $_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
                script("圈内私享，请关注‘周公犯困’并登录或注册！",'login',array('token'=>$this->token,'openid'=>$this->openid));
            }
        }elseif($detail['fw']==3){
            if($User['is_login']!=1){
                $_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
                script("圈内VIP私享，请关注‘周公犯困，了解圈友权益，注册或登录！",'login',array('token'=>$this->token,'openid'=>$this->openid));
            }else{
                script("圈内VIP私享，请关注‘周公犯困，了解圈友权益！",'index',array('token'=>$this->token,'openid'=>$this->openid));
            }
        }
    	$this->assign('info',$detail);
    	$this->assign('str',$this->tag($detail['tags']));
    	$this->assign('phone',$_SESSION['user']['phone']);
    	$this->assign('name',$_SESSION['user']['name']);

    	//标签



    	$this->UDisplay();
    }

  /*   //顾问列表
    public function lists(){
        if(IS_POST){
	        $cate=$_POST['cate'];
	        $word=$_POST['word'];
	        //组织搜索条件
	        $map['token']=session('token');
	        $map['status']=1;
	        if($_GET['type']) $map['type']=$_GET['type'];
	        if(!empty($cate)){
	            $map['cate']=$cate;
	        }
	        if(!empty($word)){
	            $map['name']=array('like','%'.$word.'%');
	        }
	        $adviser=m('jd_adviser')->where($map)->order("sort")->select();
	        if(empty($adviser)){
	            $msg=array('status'=>0,'msg'=>'没有筛选记录。');
	        }else{
	            $this->assign('adviser',$adviser);
	            $rsstr=$this->fetch('./tpl/Wap/default/Jd/listSearch.html');
	            $msg=array('status'=>1,'rs'=>$rsstr);
	        }
	        $this->ajaxreturn($msg,'json');
        }else{

	        $cate=m('jd_adv_cate')->field('name')->where(array('token'=>$this->token))->order("sort")->select();
	        $this->assign('cate',$cate);

	        $aWhere['status']=1;
	        $aWhere['token']=TO;
	        //if($_GET['type']) $aWhere['type']=$_GET['type'];
	        $adviser=m('jd_adviser')->where($aWhere)->order("sort")->select();


	        $this->assign('adviser',$adviser);
	        $this->udisplay();
        }
    } */

    //顾问详情
    public function adviser(){
        $id=$_GET['id'];
        if(empty($id)) script('参数无效！');
        //判断用户是否登录  redirect(U('login',array('token'=>session('token'))))
        if(session('is_login')!=1){
        	$_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
        	script("圈内私享，请关注‘周公犯困’并登录或注册！","login",array('token'=>$this->token,'openid'=>$this->openid));
        }
        $user = $_SESSION['user'];
        //$user=session('user');
        //读取顾问详情
        $adviser=M('jd_adviser')->where(array('token'=>$this->token,'id'=>$id))->find();
        //读取并组织顾问预约设置状况
        for($i=0;$i<=14;$i++){
          //判定用户是否已预约
            $s=$this->allDayToStr($i);
            $isApply=M('jd_apply')->where(array('token'=>$this->token,'user_id'=>$user['id'],'adviser_id'=>$id,'time'=>$s['start']))->find();
            $arr[$i]['date']=date('m月d日',$s['start']);
            $arr[$i]['week']=$this->getWeek(date('w',$s['start']));
            $config=$this->getConfig($id,$s['start']);//取得当前日期设置值
            $arr[$i]['values']=$config;
            $arr[$i]['time']=$s['start'];
            $arr[$i]['adv_id']=$id;
            $arr[$i]['status']=empty($isApply) ? 0:1;//已预约
            #统计当日已预约数
            $applys=M('jd_apply')->where(array('token'=>session('token'),'adviser_id'=>$id,'time'=>$s['start']))->count('id');
            $applyStatus=M('jd_apply')->where(array('status'=>1,'token'=>session('token'),'adviser_id'=>$id,'time'=>$s['start']))->count('id');
            $arr[$i]['apply']=(($applys<$config) && ($applyStatus<1))?1:0;#当日已预约数小于当日预约阀值并且预约阀值大于0才可被预约
        }
        $data = $this->lastrecord($_GET['id'],$this->uname(),'adviser'); //点赞状态
        $this->assign('data',$data);
        $list=substr($adviser['exper'],10);
        $this->assign('config',$arr);
        $this->assign('adviser',$adviser);

        $this->UDisplay();
    }
    //进行用户预约
    public function setApplp(){
        if(IS_AJAX){
            if(session('is_login')!=1){
                $this->ajaxReturn(array('status'=>0,'msg'=>'登录后才可以执行此操作','url'=>U('login',array('token'=>$this->token,'openid'=>$this->openid))),'json');
                die;
            }

           // $user=session('user');
            $user = $_SESSION['user'];
            $data=array(
                'user_id'=>$user['id'],
                'adviser_id'=>$_POST['adviserId'],
                'time'=>$_POST['time'],
            );
            //判定当前用户是否已在当天预约了当前顾问
            $isApply=M('jd_apply')->where(array('token'=>session('token'),'user_id'=>$data['user_id'],'adviser_id'=>$data['adviser_id'],'time'=>$data['time']))->find();
            if(!empty($isApply)) $this->ajaxReturn(array('status'=>0,'msg'=>'您已在当天预约过当前顾问！'),'json');
            //查询预约已预约数
            $count=M('jd_apply')->where(array('token'=>session('token'),'adviser_id'=>$data['adviser_id'],'time'=>$data['time']))->count('id');
            if($count>=$this->getConfig($data['adviser_id'],$data['time'])){//判断是否约满
                $this->ajaxReturn(array('status'=>0,'msg'=>'当前预约已满，请选择其他时间！'));
            }else{
                $this->ajaxReturn(array(//验证成功，表示可以预约，返回，前端跳转到申请信息填写页面
                    'status'=>1,
                    'msg'=>'',
                   'url'=>U('a',array('adviser_id'=>$data['adviser_id'],'time'=>$data['time'])),
                ),'json');
                // if(M('jd_apply')->add($data)){
                    // $this->ajaxReturn(array('status'=>1,'msg'=>'预约成功！'));
                // }else{
                    // $this->ajaxReturn(array('status'=>0,'msg'=>'预约失败，服务器繁忙！'));
                // }
            }
        }

    }
    public function a(){
        if(IS_POST){
            $user=session('user');
            $data=$_POST;

            $data['user_id']=$user['id'];
            $data['add_time']=time();
            $data['token']=session('token');

            if(preg_match('/^1[\d]{10}$/',$_POST['tel'])==0){
            	script("请输入正确的手机");

            }

            if(M('jd_apply')->add($data)){
            	$subject="来自".$user['name']."申请顾问的信";
            	$body="
            	邮件内容<br/>
            	项目名称:{$_POST['xm']}<br/>
            	申请人:{$_POST['name']}<br/>
            	联系电话:{$_POST['tel']}<br/>
            	所属行业:{$_POST['hy']}<br/>
            	竞争对手:{$_POST['ds']}<br/>
            	申请机构:{$_POST['jg']}<br/>
            	申请内容:{$_POST['content']}<br/>
            	";
            	$xx=$this->xx;

            	$toees = $xx['email1'];//
            	$toees = explode(',',$toees);
            	foreach ($toees as $ke => $v){
            		send_email($subject,$body,$v);
            	}



            	script('申请预约成功,请等待管理员处理！','myApply',array('token'=>$this->token,'openid'=>$this->openid));
            }


        }else{
            $iYid = $_GET['yid'];

            if($iYid){
                $info = M('jd_apply')->where(array('id'=>$iYid))->find();
                $this->assign('info',$info);

            }
            $data=array(
                'adviser_id'=>$_GET['adviser_id'],
                'time'=>$_GET['time'],
            );
            $this->assign('data',$data);
            $this->UDisplay();
        }
    }
    //用户登录
    public function login(){
       /* P($_SESSION);*/
        if(IS_AJAX){
            //获得必须的数据
            $name=$_POST['u_name'];
            $password=md5($_POST['u_password']);
            $u=M('jd_user')->where(array('name'=>$name,'password'=>$password,'token'=>session('wtoken')))->find();


            if(empty($u)){
                $msg=array('status'=>0,'msg'=>'用户名或密码错误！');
            }else{//登录用户

                if($u['is_show']==0){
                	$msg=array('status'=>0,'msg'=>'被管理员禁用！');
                	$this->ajaxReturn($msg,'json');die;
                }
                if($u['state']==0){
                	$msg=array('status'=>0,'msg'=>'请联系管理员审核！');
                	$this->ajaxReturn($msg,'json');die;
                }
                if(!$u['openid']){
                    M('Jd_user')->where(array('token'=>$this->token,'name'=>$name))->save(array('openid'=>$this->openid,'is_login'=>1));
                }else{
                    M('Jd_user')->where(array('token'=>$this->token,'name'=>$name))->save(array('is_login'=>1));
                }

                setcookie('user',$name,time() + 86400);
                //session_start();
                //$u = M('jd_user')->where(array('name'=>$_COOKIE['user'],'token'=>session('wtoken')))->find();
              /*  P($u);exit;*/
                $_SESSION['user']=$u;
                $_SESSION['is_login']=1;
                cookie('is_login',1);
               /* if($_SESSION['url']){
                	$msg=array('status'=>1,'url'=>$_SESSION['url']);
                }else{*/
                   // echo 8;
                	$msg=array('status'=>1,'url'=>U('uCenter',array('token'=>$this->token,'openid'=>$this->openid)));
               // }
             }
            $this->ajaxReturn($msg,'json');
        }else{
            $type = $_GET['type'];
            if($type == 1){

                M('Jd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->save(array('is_login'=>0));
                //session_destroy();//这里不是把所有session都关了
                $_SESSION['user']= null;
                $_SESSION['is_login']=null;
                setcookie('user', null,time()-3600);
                setcookie('is_login', null,time()-3600);
            }

            $this->assign('token',$this->token);
            $uid=M('wxuser')->where(array('token'=>TO))->getField('id');
            $wxusers=M('wxusers')->where(array('openid'=>OP,'uid'=>$uid))->find();
            $this->assign('wxusers',$wxusers);
		
		$token = TO ?:$_REQUEST['token'];
		$openid = OP ?:$_REQUEST['openid'];
		$bLogin = M('Jd_user')->where(array('token' => $token, 'openid' => $openid))->getField('is_login');
	    if($bLogin || $_SESSION['is_login']==1){
	    	$_SESSION['is_login'] = 1;
		
                return $this->redirect(U('uCenter', array('token'=>$token,'openid'=>$openid)));
            } //script("",'uCenter',array('token'=>$this->token,'openid'=>$this->openid));
           // session_destroy();
            $this->UDisplay();
        }

    }
    //用户个人中心
    public function uCenter(){
        //读取展示所需要的数据
        session_start();
        if($_SESSION['is_login']!=1){
            $this->redirect(U('login',(array('token'=>$this->token))));
        }else{
            $da=M('jd_user');
            $this->assign('user',session('user'));

            $this->assign('pic',$_SESSION['user']['head']);
            $this->UDisplay();
        }
    }
    //用户注册
    public function register(){
        if(IS_AJAX){
            $name=$_POST['u_name'];
            $password=$_POST['u_password'];
            $rePassword=$_POST['re_u_password'];
            $user_name=M('jd_user')->where(array('token'=>TO,'name'=>$name))->find();
            //检查数据是否重复
            if(!empty($user_name)){
                $msg=array('status'=>0,'msg'=>'用户名已存在！');
                $this->ajaxReturn($msg,'json');
                die;
            }
            $user_email=M('jd_user')->where(array('token'=>$this->token,'email'=>$_POST['u_email']))->find();
            if(!empty($user_email)){
                $msg=array('status'=>0,'msg'=>'邮箱已存在！');
                $this->ajaxReturn($msg,'json');
                die;
            }
            $user_phone=M('jd_user')->where(array('token'=>$this->token,'phone'=>$_POST['u_phone']))->find();
            if(!empty($user_phone)){
                $msg=array('status'=>0,'msg'=>'此手机号码已存在！');
                $this->ajaxReturn($msg,'json');
                die;
            }
            //检查密码
            if($password!=$rePassword){
                $msg=array('status'=>0,'msg'=>'两次填写的密码不一致！');
                $this->ajaxReturn($msg,'json');
                die;
            }
            //验证无误，准备数据
            $data=array(
                'true_name'=>$_POST['true_name'],
                'name'=>$_POST['u_name'],
                'password'=>md5($password),
                'email'=>$_POST['u_email'],
                'phone'=>$_POST['u_phone'],
                'qq'=>$_POST['u_qq'],
            	'type'=>$_POST['type'],
            	'hb'=>$_POST['hb'],
            	'is_show'=>1,
            	'state'=>0,
            	'add_time'=>time(),
            	'head'=>$_POST['img'],
                'token'=>TO,
                'grade'=>$_POST['grade'],
                'openid'=>$this->openid,
               /* 'sp_time'=>time()*/
            );
            $status=M('jd_user')->add($data);
            if($status){
                $msg=array('status'=>1,'url'=>U('login',array('token'=>$this->token)));
                $subject = "用户注册消息";

                switch ($data['type']){
                	case 0:$data['type']='总部';break;
                	case 1:$data['type']='机构';break;
                	case 2:$data['type']='伙伴';break;
                }


                $body = "周公犯困有新用户注册，来自{".$data['type']."}组织名称{".$data['hb']."}的{".$data['name']."}，烦请审核！";
                $info = M('jd_xx')->where(array('token'=>$this->token))->find();
                $toees = $info['email4'];//
                $toees = explode(',',$toees);

                $info = M('jd_xx')->where(array('token'=>$_SESSION['token']))->find();

                foreach ($toees as $ke => $v){
                	send_email($subject,$body,$v);
                    //发邮箱给用户
                    send_email('注册确认信','['.$_POST['u_name'].']您的注册申请周公犯困已接收，如需及时开通，请线下提供K/3 Cloud方案一
                    份至 307658771@qq.com或sixuan_hu@kingdee.com,您的开通信息我们会第一时间邮件通知您。<br /><br />'.$info['content'],$_POST['u_email']);
                }
            }else{
                $msg=array('status'=>0,'msg'=>'注册失败服务器繁忙...');
            }
            $this->ajaxReturn($msg,'json');
        }else{
        	$wxusers=wxusers();
        	$this->assign('wxusers',$wxusers);
            $this->UDisplay();
        }
    }

    /*修改密码*/
    public function modify(){
        $oMOdel = M('Jd_user');
        $sessions = $_COOKIE['user'];
        $user= $this->User;
	if(IS_AJAX){
            $opassword = md5($_POST['opassword']);
            if($opassword != $user['password']){
                echo $this->error('旧密码输入错误！');exit;
            }else if($_POST['opassword'] == ''){
	    	echo $this->error('请输入旧密码！');exit;
	    }else if ($_POST['password'] == ''){
	    	echo $this->error('请输入新密码！');exit;
	    }else if ($_POST['password'] == $_POST['opassword']){
	    	echo $this->error('新密码和旧密码一致，请重新输入新密码！');exit;
	    }else{
                $isSave = $oMOdel
                    ->where(array('name'=>$sessions,'token'=>$_SESSION['token']))
                    ->save(array('password'=>md5($_POST['password'])));
                if($isSave){
                    $this->success('修改成功',U('Jd/uCenter',array('token'=>$_SESSION['token'],'openid'=>$this->openid)));
                }else{
                    $this->error('系统繁忙！');
                }
            }

        }
        $this->assign('user',$user);
        $this->UDisplay();
    }
    //我的顾问申请】
    public function myApply(){
        if(session('is_login')!=1) $this->redirect(U('login',(array('token'=>$this->token))));
        $user=session('user');
        //读取用户顾问申请记录
        $apply=M('jd_apply')->where(array('token'=>session('token'),'user_id'=>$user['id']))->select();
        foreach($apply as $key=>$value){
            $map=array('id'=>$value['adviser_id']);
                $apply[$key]['name']=M('jd_adviser')->where($map)->getField('name');
                $apply[$key]['head']=M('jd_adviser')->where($map)->getField('head');
                $apply[$key]['exper']=M('jd_adviser')->where($map)->getField('exper');
                $apply[$key]['trade']=M('jd_adviser')->where($map)->getField('trade');
                $apply[$key]['status']=$value['status'];
                $apply[$key]['oid']=$value['adviser_id'];
                $apply[$key]['aid']=$value['id'];

        }
        $this->assign('apply',$apply);


        $this->UDisplay();
    }
    //我的顾问推荐
    public function myAdviser(){
        if(session('is_login')!=1) $this->redirect(U('login',(array('token'=>$this->token,'openid'=>$this->openid))));
        $user=session('user');

            $list=M('jd_adviser')->where(array('token'=>TO,'groom'=>$user['id']))->order("add_time desc")->select();
           // P($list)
            $this->assign('list',$list);

        $this->UDisplay();
    }

    //我的方案推荐
    public function myWz(){
        if(session('is_login')!=1) $this->redirect(U('login',(array('token'=>$this->token))));
        $user=session('user');
            $list=M('jd_wz')->where(array('token'=>session('token'),'plan'=>$user['id']))->order("add_time desc")->select();
            foreach($list as $k=>$v){
            	$tagId=explode(',',$v['tags']);
            	$list[$k]['tag']=M('jd_tag')->field('name,color')->where(array('id'=>array('in',$tagId)))->select();

            }

            $this->assign('list',$list);
   // P($list);
        $this->UDisplay();
    }
    //我要推荐,顾问
    public function recom(){
        if(IS_POST){
            if(session('is_login')!=1){
                script('无法执行此操作，用户为登录！');
                die;
            }
            $user=session('user');
            $data=array(
                'name'=>$_POST['name'],
            		'type'=>$_POST['type'],
            		'cate'=>$_POST['cate'],
            		'gs'=>$_POST['gs'],
                'phone'=>$_POST['phone'],
                'trade'=>$_POST['trade'],
                'remark'=>$_POST['remark'],
                'exper'=>$_POST['exper'],
                'referee'=>$_POST['referee'],
                'ref_phone'=>$_POST['ref_phone'],
                'status'=>0,
                'token'=>session('token'),
                'head'=>'./tpl/Wap/default/common/Jd/images/head.jpg',
                'groom'=>$user['id'],
                'add_time'=>time()
            );



            if(preg_match('/^1[\d]{10}$/',$_POST['phone'])==0){
            	script("请输入正确的手机");

            }

            if(preg_match('/^1[\d]{10}$/',$_POST['ref_phone'])==0){
            	script("请输入正确的推荐人电话");

            }




            	$subject="来自".$user['name']."的推荐顾问信,烦请审核";
            	$body="
            	邮件内容<br/>
            	顾问姓名:{$_POST['name']}<br/>
            	顾问分类:{$_POST['type']}<br/>
            	顾问类型:{$_POST['cate']}<br/>
            	所属公司:{$_POST['gs']}<br/>
            	联系电话:{$_POST['phone']}<br/>
            	擅长领域:{$_POST['trade']}<br/>
            	主要履历:{$_POST['remark']}<br/>
            	项目经历:{$_POST['exper']}<br/>
            	";
            	$xx=$this->xx;
            	$toees = $xx['email2'];//
            	$toees = explode(',',$toees);
            	foreach ($toees as $ke => $v){
            		send_email($subject,$body,$v);
            	}




            if(M('jd_adviser')->add($data)){
                script('推荐成功！请等待管理员处理！',"myAdviser",array('token'=>$this->token,'openid'=>$this->openid));
            }else{
                script('推荐失败！服务器繁忙...');
            }
        }else{
            $iRid = $_GET['rid'];
            if($iRid){
                $info = M('jd_adviser')->where(array('id'=>$iRid))->find();
                $this->assign('info',$info);
            }


        	$this->assign('phone',$_SESSION['user']['phone']);
        	$this->assign('name',$_SESSION['user']['name']);
            $this->UDisplay();
        }
    }
    //我要推荐方案
    public function recomWz(){
            if(IS_POST){

            	if(preg_match('/^1[\d]{10}$/',$_POST['tel'])==0){
            		script("请输入正确的手机");
            	}


            $_POST['tags']=implode(',',$_REQUEST['tags']);

            if(session('is_login')!=1){
                script('无法执行此操作，用户为登录！');
                die;
            }
            $user=session('user');
            $data=array(
            	'tags'=>$_POST['tags'],
                'name'=>$_POST['name'],
                'title'=>$_POST['title'],
                'hy'=>$_POST['hy'],
            		'type'=>$_POST['type'],
                'content'=>$_POST['content'],
                'status'=>0,
                'token'=>session('token'),
                'plan'=>$user['id'],
            	'url' => $_POST['url'],
            	'tel' => $_POST['tel'],
            	'ld' => $_POST['ld'],
            	'user_id' => $_SESSION['user']['id'],
                'add_time'=>time()
            );


            if(M('jd_wz')->add($data)){





            	$subject="来自".$user['name']."推荐方案的信,烦请审核";
            	$body="
            	邮件内容<br/>
            	方案名称:{$_POST['title']}<br/>
            	方案类型:{$_POST['type']}<br/>
            	方案所属行业:{$_POST['hy']}<br/>
            	方案作者:{$_POST['name']}<br/>
            	方案链接:{$_POST['url']}<br/>
            	方案亮点:{$_POST['ld']}<br/>
            	联系电话:{$_POST['tel']}<br/>
            	方案内容:{$_POST['content']}<br/>
            	";
            	$xx=$this->xx;
            	//P($xx['email3']);die;

            	$toees = $xx['email3'];//
            	$toees = explode(',',$toees);
            	foreach ($toees as $ke => $v){
            		send_email($subject,$body,$v);
            	}



                script('推荐成功！请等待管理员审核','myWz',array('token'=>$this->token,'openid'=>$this->openid));
            }else{
                script('推荐失败！服务器繁忙...');
            }
        }else{
                $iWid = $_GET['wid'];
                if($iWid){
                    $info = M('jd_wz')->where(array('id'=>$iWid))->find();
                    $aIndustrys = M('Jd_industry')->where(array('id'=>$info['hy'],'token'=>$this->_sToken))->find();
                    $info['hy'] =$aIndustrys['industry'];
                    $this->assign('info',$info);
                }
            $aIndustry = M('Jd_industry')->where(array('token'=>$this->_sToken))->select();
            $this->assign('aIndustry',$aIndustry);
        	$this->assign('name',$_SESSION['user']['name']);
        	$tag=M('jd_tag')->select();
        	$this->assign('tag',$tag);
            $this->UDisplay();
        }

    }

    //根据给定时间戳返回星期
    protected function getWeek($s){
        $week=array('星期天','星期一','星期二','星期三','星期四','星期五','星期六');
        return $week[$s];
    }

    //获得当天以后第N天的起始时间戳和结束时间戳
    protected function allDayToStr($day) {
        $start=strtotime(date('Y-m-d',strtotime('+0 day')));//获得今天零点时候的时间戳
        $end=strtotime(date('Y-m-d',strtotime('+1 day')))-1;//获得今天的结束时间戳
        //生成时间差
        if(is_numeric($day)){
            $dayMis=3600*24*$day;
            $arr=array(
                'start'=>$start+$dayMis,
                'end'=>$end+$dayMis
            );
            return $arr;
        }else{
            return false;
        }
    }
    //获得顾问申请配置
    protected function getConfig($id,$time){
        $data=M('jd_config')->field('values')->where(array('token'=>session('token'),'adv_id'=>$id,'time'=>$time))->find();
        //P($data);
        if($data<1){
          return 0;
        }else{
            return $data['values'];
        }
    }

    public function code(){
        $code = rand(100000,999999);
        return $code;
    }

    public function aaa(){
    	if(IS_POST){
    		if(!$_POST['u_name']) script("用户名为空");
    		if(!$_POST['u_email']) script("邮箱为空");
    		if(!$_POST['u_phone']) script("电话为空");
    		$list=M('jd_user')->where(array('token'=>TO,'name'=>$_POST['u_name']))->find();
    		if(!$list) script("帐号不存在");
    	    if($_POST['u_email']!=$list['email']) script("邮箱不正确");
    	    if($_POST['u_phone']!=$list['phone']) script("手机不正确");
            $code = $this->code();
            if(M('jd_user')->where(array('token'=>TO,'name'=>$_POST['u_name']))->save(array('password'=>md5($code)))){
                $body = "
                您重新获取了一个新的密码，密码为：{$code}<br/>
    		    提示：如若想重新修改密码，可以登录进去，进行“修改密码”的操作！<br/>";
                $emailfs=send_email("周公犯困账号密码找回",$body,$list['email']);
                if($emailfs){
                    //cache(md5($list['email']),$code,1800);
                    script("新密码已发送至您的注册邮箱，请查收！","login",array('token'=>$this->token,'uid'=>$list['id'],'openid'=>$this->openid));
                }else{
                    script("邮箱发送失败，请重新操作","aaa",array('token'=>$this->token,'uid'=>$list['id'],'openid'=>$this->openid));
                }
            }else{
                script("密码修改失败","aaa",array('token'=>$this->token,'uid'=>$list['id'],'openid'=>$this->openid));
            }
    	}else{
    		$this->UDisplay();
    	}

    }

    public function bbb(){
    	if(IS_POST){
            $info = M('jd_user')->where(array('token'=>TO,'id'=>$_GET['uid']))->find();
            if(cache(md5($info['email'])) != $_POST['code']) script("验证码错误！");
    		if(!$_POST['u_password']) script("密码为空");
    		if(!$_POST['re_u_password']) script("确认密码为空");
    		if($_POST['u_password']!=$_POST['re_u_password']) script("俩次密码不一致");
    		M('jd_user')->where(array('token'=>TO,'id'=>$_GET['uid']))->save(array('password'=>md5($_POST['u_password'])));
            cache(md5($info['email']),null);
    		script("密码修改成功","login",array('token'=>$this->token,'openid'=>$this->openid));
    	}else{
    		$this->UDisplay();
    	}

    }

    public function ccc(){
    	if(IS_POST){
    		if(!$_POST['u_email']) script("邮箱为空");
    		if(preg_match('/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/',$_POST['u_email'])==0){
    			script("请输入正确的邮箱");
    		}
    		$list=M('jd_wz')->where(get(id))->find();
    		//找出标签
    		$tags=explode(',',$list['tags']);
    		$tags=M('jd_tag')->where(array(id=>array(in,$tags)))->select();
    		$tags=Arr::changeIndexToKVMap($tags, 'id', 'name');
    		$tags=implode(',', $tags);

    		$list['add_time']=date("Y-m-d h:i",$list['add_time']);
    		$subject="标题".$list['title'];
            $url = $list['url'];
    		$body="
    		方案标题：{$list['title']}<br/>
    		方案作者：{$list['name']}<br/>
    		发布时间：{$list['add_time']}<br/>
    		方案标签：{$tags}<br/>
    		方案行业：{$list['hy']}<br/>
    		方案关键字：{$list['gjz']}<br/>
    		方案链接：<a href='".$url."'>{$url}</a><br/>
    		方案描述：{$list['content']}<br/>
    		";
    		$to=$_POST['u_email'];
            $body = $_POST['html'];
    		$aaa=send_email($subject,$body,$to);
    		if($aaa['success']){
    			script("邮件发送成功","detailed",get1(token,TO,id));
    		}else{
    			script("邮件发送失败","detailed",get1(token,TO,id));
    		}

    	}else{
            $info = M('jd_xx')->where(array('token'=>$_SESSION['token']))->find();
    		$list=M('jd_wz')->where(get(id))->find();
    		$this->assign('list',$list);
    		$this->assign('email',$_SESSION['user']['email']);
            $this->assign('xx',$info);

    		$this->UDisplay();
    	}

    }
    //评价
    public function talk(){
        if(IS_AJAX){
            if(session('is_login')!=1) $this->ajaxReturn(array('status'=>0,'msg'=>'需要登录....','url'=>U('login',array('token'=>$this->token,'openid'=>$this->openid))));
            $data=array(
                'oid'=>$_POST['oid'],
                'aid'=>$_POST['aid'],
                'type'=>$_POST['type'],
                'content'=>$_POST['content'],
                'status'=>1,
                'token'=>TO,
                'add_time'=>time()
            );
            if(empty($data['content'])){
                $msg=array('status'=>0,'msg'=>'无效的评价！评价内容空。');
                $this->ajaxReturn($msg,'json');
            }
            //数据写入

            if(M('jd_talk')->where(array('oid'=>$_POST['oid'],'aid'=>$_POST['aid'],'type'=>$_POST['type'],'token'=>TO))->find()){
            	$msg=array('status'=>0,'msg'=>'已经评价过了');
            }else{
            	if(M('jd_talk')->add($data)){
            		$msg=array('status'=>1,'msg'=>'感谢您的评价！');
            	}else{
            		$msg=array('status'=>0,'msg'=>'评价失败！数据服务器繁忙...');
            	}
            }

            $this->ajaxReturn($msg,'json');
        }
    }



    public function lists(){
    	if(IS_POST){
    /* 		$cate=$_POST['cate'];
    		$word=$_POST['word'];
    		//组织搜索条件
    		$map['token']=session('token');
    		$map['status']=1;
    		if($_GET['type']) $map['type']=$_GET['type'];
    		if(!empty($cate)){
    			$map['cate']=$cate;
    		}
    		if(!empty($word)){
    			$map['name']=array('like','%'.$word.'%');
    		}
    		$adviser=m('jd_adviser')->where($map)->order("sort")->select();
    		if(empty($adviser)){
    			$msg=array('status'=>0,'msg'=>'没有筛选记录。');
    		}else{
    			$this->assign('adviser',$adviser);
    			$rsstr=$this->fetch('./tpl/Wap/default/Jd/listSearch.html');
    			$msg=array('status'=>1,'rs'=>$rsstr);
    		}
    		$this->ajaxreturn($msg,'json'); */

    		$n=$_POST['n']*20;
    		$cate=m('jd_adv_cate')->field('name')->where(array('token'=>$this->token))->order("sort")->select();
    		$this->assign('cate',$cate);
    		$aWhere['status']=1;
    		$aWhere['token']=TO;
    		if($_GET['type']) $aWhere['type']=$_GET['type'];
    		if($_GET['cate']) $aWhere['cate']=$_GET['cate'];
    		if($_GET['search']) $aWhere['name']=array('like','%'.$_GET['search'].'%');
    		$adviser=m('jd_adviser')->where($aWhere)->limit($n,20)->order("sort")->select();
            foreach($adviser as $k=>$val){
                $data = $this->lastrecord($val['id'],$this->uname(),'adviser');
                $adviser[$k]['praisetype'] = $data['type'];
            }
    		$this->assign('adviser',$adviser);
    		$x = $this->fetch('./tpl/Wap/default/Jd/lists_ajax.html', $adviser);//内容放进来
    		exit($x);

    	}else{

    		$cate=m('jd_adv_cate')->field('name')->where(array('token'=>$this->token))->order("sort")->select();
    		$this->assign('cate',$cate);
    		$aWhere['status']=1;
    		$aWhere['token']=TO;
    		if($_GET['type']) $aWhere['type']=$_GET['type'];
    		if($_GET['cate']) $aWhere['cate']=$_GET['cate'];
    		if($_GET['search']) $aWhere['name']=array('like','%'.$_GET['search'].'%');
			$adviser=m('jd_adviser')->where($aWhere)->limit(0,20)->order("sort")->select();
            foreach($adviser as $k=>$val){
                $data = $this->lastrecord($val['id'],$this->uname(),'adviser');
                $adviser[$k]['praisetype'] = $data['type'];
            }

    		$this->assign('adviser',$adviser);
    		$this->udisplay();
    	}
    }


    public function search(){
        if ($_GET['type']) {
            session('type', $_GET['type']);
            $this->type = session('type');
        }
     	if(IS_AJAX){
     		$n=$_POST['n']*20;
     		//标签
     		$tags= M('jd_tag')->where(array('token'=>$this->token))->field('id,name')->select();
     		$this->assign('tags',$tags);
     		//行业
     		$hy=M('jd_wz')->where(array('token'=>$this->token))->field('hy')->group('hy')->select();
     		$this->assign('hy',$hy);
     		//方案
     		$aWhere['status']=1;
     		$aWhere['token']=TO;
     		if($this->type) $aWhere['type']=$this->type;
     		if($_GET['hy']) $aWhere['hy']=$_GET['hy'];
     		if($_GET['tags']) $aWhere['tags']=array('like','%'.$_GET['tags'].'%');

     		if($_GET['search']){
     			$aWhere1['title']=array('like','%'.$_GET['search'].'%');
     			$aWhere1['name']=array('like','%'.$_GET['search'].'%');
     			$aWhere1['gjz']=array('like','%'.$_GET['search'].'%');
     			$aWhere1['_logic'] = 'or';//_logic固定值
     			$aWhere['_complex'] = $aWhere1;//_complex固定值
     		}
     		$list=M('jd_wz')->where($aWhere)->limit($n,20)->order("sort, add_time desc")->select();
     		foreach($list as $k=>$v){
     			$tagId=explode(',',$v['tags']);
     			$list[$k]['tag']=M('jd_tag')->field('name,color')->where(array('id'=>array('in',$tagId)))->select();
                $data = $this->lastrecord($v['id'],$_COOKIE['uname'],'plan');
                $list[$k]['praisetype'] = $data['type'];
                $list[$k]['evaluate'] = $this->evaluatelist($v['id'],'plan');
                $list[$k]['count'] = count($list[$k]['evaluate']);
     		}

     		$this->assign('list',$list);
     		$x = $this->fetch('./tpl/Wap/default/Jd/search_ajax.html', $list);//内容放进来
     		exit($x);
    	}else{
    		//标签
    		$tags= M('jd_tag')->where(array('token'=>$this->token))->field('id,name')->select();
    		$this->assign('tags',$tags);
    		//行业
            //$aIndustry = M('Jd_industry')->where(array('token'=>$this->_sToken,'id'=>$v['hy']))->find();
    		$hy=M('Jd_industry')->where(array('token'=>$this->token))->select();
    		$this->assign('hy',$hy);
    		//方案
    		$aWhere['status']=1;
    		$aWhere['token']=TO;

    		if($_GET['type']) $aWhere['type']=$_GET['type'];
    		if($_GET['hy']) $aWhere['hy']=$_GET['hy'];
    		if($_GET['tags']) $aWhere['tags']=array('like','%'.$_GET['tags'].'%');
    		if($_GET['search']){
    			$aWhere1['title']=array('like','%'.$_GET['search'].'%');
    			$aWhere1['name']=array('like','%'.$_GET['search'].'%');
    			$aWhere1['gjz']=array('like','%'.$_GET['search'].'%');
    			$aWhere1['_logic'] = 'or';//_logic固定值
    			$aWhere['_complex'] = $aWhere1;//_complex固定值
    		}


    		$list=M('jd_wz')->where($aWhere)->limit(0,20)->order("sort, add_time desc")->select();
    		foreach($list as $k=>$v){
    			$tagId=explode(',',$v['tags']);
    			$list[$k]['tag']=M('jd_tag')->field('name,color')->where(array('id'=>array('in',$tagId)))->select();
                $data = $this->lastrecord($v['id'],$this->uname(),'plan');
                $list[$k]['praisetype'] = $data['type'];
                $list[$k]['evaluate'] = $this->evaluatelist($v['id'],'plan');
                $list[$k]['count'] = count($list[$k]['evaluate']);
            }
           //echo count($list);exit;
    		$this->assign('list',$list);
    		$this->UDisplay();

    	}

    }
    /*找用户名称uname*/
    public function uname(){
        $user = M('Jd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
		/*
		  未找到微信openid  继续找用户登录id
		*/
		if(!$user){
			$user = M('Jd_user')->where(array('token'=>$this->token,'name'=>$_COOKIE['user']))->find();
		}
        if($user['name']){
            $user = $user['name'];
        }else{
            $tuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $ousers = M('Wxusers')->where(array('uid'=>$tuser['id'],'openid'=>$this->openid))->find();
            $user = $ousers['nickname'];
        }

        return $user;
    }


    public function headpic(){
        $user = M('Jd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
		/*
		  未找到微信openid  继续找用户登录id
		*/
		if(!$user){
			$user = M('Jd_user')->where(array('token'=>$this->token,'name'=>$_COOKIE['user']))->find();
		}
        if($user['head']){
            $headpic = $user['head'];
        }else{
            $tuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $ousers = M('Wxusers')->where(array('uid'=>$tuser['id'],'openid'=>$this->openid))->find();
            $headpic = $ousers['headimgurl'];
        }

        return $headpic;
    }


    /*点赞数*/
    public function praise(){
        if(IS_AJAX){
            $type = $_GET['type'];
            $figure = $_POST['figure'];
            if($type == 'plan'){  //方案
                if(M('Jd_wz')->where(array('id'=>$_POST['id']))->find()){
                    if($figure == 1){
                        if($this->uname()){
                            M('Jd_wz')->where(array('id'=>$_POST['id']))->setInc('praise',1);
                            $this->record(1,$_POST['id'],'plan',$this->uname());
                        }else{
                            $this->error('您还没登录哦，请先登录');
                        }

                    }elseif($figure == -1){
                        if($this->uname()) {
                            M('Jd_wz')->where(array('id' => $_POST['id']))->setDec('praise', 1);
                            $this->record(-1, $_POST['id'], 'plan', $this->uname());
                        }else{
                            $this->error('您还没登录哦，请先登录');
                        }
                    }
                }else{
                    $this->error('非法操作！');
                }
            }elseif($type == 'active'){   //活动与招标
                if(M('Jd_tender')->where(array('id'=>$_POST['id']))->find()){
                    if($figure == 1){
                        if($this->uname()) {
                            M('Jd_tender')->where(array('id' => $_POST['id']))->setInc('number', 1);
                            $this->record(1, $_POST['id'], 'active', $this->uname());
                        }else{
                            $this->error('您还没登录哦，请先登录');
                        }
                    }elseif($figure == -1){
                        if($this->uname()) {
                            M('Jd_tender')->where(array('id' => $_POST['id']))->setDec('number', 1);
                            $this->record(-1, $_POST['id'], 'active', $this->uname());
                        }else{
                            $this->error('您还没登录哦，请先登录');
                        }
                    }
                }else{
                    $this->error('非法操作！');
                }
            }elseif($type == 'adviser'){
                if(M('Jd_adviser')->where(array('id'=>$_POST['id']))->find()){
                    if($figure == 1){
                        if($this->uname()) {
                            M('Jd_adviser')->where(array('id' => $_POST['id']))->setInc('praise', 1);
                            $this->record(1, $_POST['id'], 'adviser', $this->uname());
                        }else{
                            $this->error('您还没登录哦，请先登录');
                        }
                    }elseif($figure == -1){
                        if($this->uname()) {
                            M('Jd_adviser')->where(array('id' => $_POST['id']))->setDec('praise', 1);
                            $this->record(-1, $_POST['id'], 'adviser', $this->uname());
                        }else{
                            $this->error('您还没登录哦，请先登录');
                        }
                    }
                }else{
                    $this->error('非法操作！');
                }
            }
        }
    }


    /*点赞记录*/
    public function record($type,$tid,$style,$uname){
        $model = M('Jd_praise');

        if($type && $tid && $style &&$uname){

           if($model->add(array(
                'token'     =>$this->token,
                'openid'    =>$this->openid,
                'type'      => $type,
                'tid'       =>$tid,
                'style'     =>$style,
                'uname'     => $uname,
                'add_time'  => date('Y-m-d H:i:s')
            ))){
                $this->success('成功！');
            }else{
               $this->error('失败');
           };

        }
    }

    /*最后的记录*/
    public function lastrecord($tid,$uname,$style){
        $model = M('Jd_praise');
		$user = $this->uname();
        //$datas = $model->where(array('tid'=>$tid,'uname'=>$uname,'style'=>$style,'openid'=>$this->openid))->order('add_time desc')->select();
		$datas = $model->where(array('tid'=>$tid,'uname'=>$uname,'style'=>$style,'uname'=>$user))->order('add_time desc')->select();
        $data = $datas[0];
        return $data;
    }


    /*评价页面*/
    public function evaluate(){
        $tid = $_GET['tid'];
        $style = $_GET['type'];
        if($style == 'plan'){
            $titles = M('Jd_wz')->where(array('id'=>$tid))->find();
            $title = $titles['title'];
        }elseif($style == 'active'){
            $titles = M('Jd_tender')->where(array('id'=>$tid))->find();
            $title = $titles['title'];
        }

        $this->assign(array(
            'title'=>$title,
            'list'=> $this->evaluatelist($tid,$style)
        ));
        $this->UDisplay('evaluate');
    }
    /*评价展示*/
    public function evaluatelist($tid,$style){
        $model = M('Jd_evaluation');
        $list = $model->where(array(
            'style' =>$style,
            'tid'   =>$tid,
            'state'=>1
        ))->order('add_time')->select();

        foreach($list as $key=>$val){
            $list[$key]['upuname'] = $model->where(array('id'=>$val['pid']))->getField('uname');

        }
        self::$treeList = array();
        $dataD = self::tree($list);
        return $dataD;
    }

    /**/
    //static public $treeList = array();
    static function tree($data,$pid = 0,$count = 1) {

        foreach ($data as $key => $value){
            if($value['pid']==$pid){
                $value['Count'] = $count;
                self::$treeList []=$value;
                unset($data[$key]);
                self::tree($data,$value['id'],$count+1);
            }
        }
        return self::$treeList ;
    }

    /*评价*/
    public function evaluateactive(){
        $model = M('Jd_evaluation');
        $style = $_GET['type'];
        //$user = M('Jd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        if(IS_AJAX){
            $data = array(
                'token'     =>$this->token,
                'uname'     =>$this->uname(),
                'tid'       =>$_POST['tid'],
                'pid'       =>$_POST['pid'] ? $_POST['pid'] : 0,
                'content'  =>$_POST['content'],
                'add_time' =>date('Y-m-d H:i:s'),
                'openid'   =>$this->openid,
                'style'    =>$style,
                'state'    =>1,
                'headpic' =>$this->headpic()
            );
            if($this->uname()){
                if($style == 'plan'){
                    if(M('Jd_wz')->where(array('id'=>$_POST['tid']))->find()){
                        $this->addevaluate($data);
                    }else{
                        $this->error('非法操作！');
                    }
                }elseif($style == 'active'){
                    if(M('Jd_tender')->where(array('id'=>$_POST['tid']))->find()){
                        $this->addevaluate($data);
                    }else{
                        $this->error('非法操作！');
                    }
                }
            }else{
                $this->error('您还没登录哦，请先登录！');
            }


        }
    }

    public function addevaluate($data){
        $model = M('Jd_evaluation');
        $is_update = $model->add($data);
        if($is_update){
            $data = $model->where(array('id'=>$is_update))->find();
            if($data['pid'] == 0){
                $path = 0;
            }else{
                $updata = $model->where(array('id'=>$data['pid']))->getField('path');
                $path = $updata. '-'.$is_update;
            }

            if($model->where(array('id'=>$is_update))->save(array('path'=>$path))){
                $this->success('评价成功！');
            }else{
                $this->error('评价失败！');
            }
        }
    }



    public function so(){
    	$_POST['searchwwww']=trim($_POST['searchwwww']);
    	script("",'lists',get1(search,$_POST['searchwwww'],token,cate,type));
    }

    public function so2(){
    	$_POST['searchwwww']=trim($_POST['searchs']);
    	script("",'search',get1(search,$_POST['searchwwww'],token,hy,tags,type));
    }


    //查询
    public function so3(){
    	$searchs=$_POST['searchs'];
    	$type=$_GET['type'];
    	$list=M("jd_wz")->query("select * from tp_jd_wz where (title like '%$searchs%' and type='$type') or (name like '%$searchs%' and type='$type') or(gjz like '%$searchs%' and type='$type') order by sort");

      	foreach($list as $k=>$v){
    		$tagId=explode(',',$v['tags']);
    		$list[$k]['tag']=M('jd_tag')->field('name,color')->where(array('id'=>array('in',$tagId)))->select();
    		$arr[$k]=$v;
    	}
        $this->assign('searchs',$searchs);
    	$this->assign('list',$list);
    	$this->UDisplay('Jd_search');
    }

    public function ajax(){

    	if(IS_AJAX){

    		//htmlspecialchars_decode($_POST['html');
    		htmlspecialchars_decode($_POST['html']);

    		if(preg_match('/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/',$_POST['email'])==0){
    			$res['str']='请输入正确的邮箱';
    			$this->ajaxReturn($res);
    			die;
    		}

    		/* $body=$_POST['html'];
    		$res['str']=$body; */
    		$body=htmlspecialchars_decode($_POST['html']);
    		$to=$_POST['email'];
    		$subject="金蝶用户".$_SESSION['user']['name']."的来信";
    		$aaa=send_email($subject,$body,$to);
    		if($aaa['success']){
                //记录邮箱
                cookie('fs_email',$_POST['email'],60*60*24*30);
                //加次数
              //  M()
                M('jd_wz')->where(array('id'=>$_GET['id']))->setInc('email_num',1);
    			$res['str']='邮件发送成功';
    		}else{
    			$res['str']='邮件发送失败';
    		}

    		$this->ajaxReturn($res);
    	}
    }

    /*顾问申请详情*/
    public function abs(){
        $iYid = $_GET['yid'];

        if($iYid){
            $info = M('jd_apply')->where(array('id'=>$iYid))->find();
            $this->assign('info',$info);

        }
        $data=array(
            'adviser_id'=>$_GET['adviser_id'],
            'time'=>$_GET['time'],
        );
        $this->assign('data',$data);
        $this->UDisplay('abs');
    }
    /*顾问推荐详情*/
    public function recoms(){
        $iRid = $_GET['rid'];
        if($iRid){
            $info = M('jd_adviser')->where(array('id'=>$iRid))->find();
            $this->assign('info',$info);
        }


        $this->assign('phone',$_SESSION['user']['phone']);
        $this->assign('name',$_SESSION['user']['name']);
        $this->UDisplay('recoms');
    }
    /*方案推荐详情*/
    public function recomWzs(){
        $iWid = $_GET['wid'];
        if($iWid){
            $info = M('jd_wz')->where(array('id'=>$iWid))->find();
            $aIndustrys = M('Jd_industry')->where(array('id'=>$info['hy'],'token'=>$this->_sToken))->find();
            $info['hy'] =$aIndustrys['industry'];
            $this->assign('info',$info);
        }
        $aIndustry = M('Jd_industry')->where(array('token'=>$this->_sToken))->select();
        $this->assign('aIndustry',$aIndustry);
        $this->assign('name',$_SESSION['user']['name']);
        $tag=M('jd_tag')->select();
        $this->assign('tag',$tag);
        $this->UDisplay('recomWzs');
    }

	public function adviserShow(){
		$list=M('jd_adviser')->where(get(id))->find();
		$this->assign('list',$list);
		$this->UDisplay();
	}
    /*个人资料页*/
    public function myconment(){
        $oModel = M('Jd_user');
        $sessions = $_COOKIE['user'];//$_SESSION['user'];
        $info = $oModel->where(array('name'=>$sessions,'token'=>$this->token))->find();
       // P($info);
        $this->assign(array(
            'info'=>$info
        ));
        $this->UDisplay('myconment');
    }
    /*资料修改*/
    public function setuser(){
        $iTem =  M('Jd_user')->where(array('id'=>$_POST['id']))->find();
        if(!$iTem){
            $this->success2('非法操作！');exit;
        }
        if(M('Jd_user')->where(array('id'=>$_POST['id']))->save($_POST)){
            $this->success2('修改成功！');
        }else{
            $this->error2('系统繁忙...');
        }
    }

    /*招标、活动列表页*/
    public function activelist(){
        $oModel = M('Jd_tender');
        $list = $oModel->where(array('token'=>$this->token,'state'=>1, 'type' => (int)$_GET['type']))->order('sort, add_time desc ')->select();
        foreach($list as $k=>$val){
            $list[$k]['time'] = time();
            $list[$k]['onetime'] = strtotime($val['startday']);
            $list[$k]['twotime'] = strtotime($val['endday']);
            $data = $this->lastrecord($val['id'],$this->uname(),'active');
            $list[$k]['praisetype'] = $data['type'];
            $list[$k]['evaluate'] = $this->evaluatelist($val['id'],'active');
            $list[$k]['count'] = count($list[$k]['evaluate']);
        }
        $this->assign(array(
            'list'     =>$list
        ));
        $this->UDisplay('activelist');
    }
    /*活动详情页*/
    public function activeinfo(){
        if(IS_POST){//分亨回调加1
            M('Jd_tender')->where(array('id'=>$_GET['id']))->setInc('fx_num',1);
        }else{
            $oModel = M('Jd_tender');
            $info = $oModel->where(array('id'=>$_GET['id']))->find();
            $this->assign('info',$info);
            $this->assign('isHD', $info['type'] == 1);
            $this->assign('validTime', strtotime($info['startday']) <= time() && time() <= strtotime($info['endday']));
            $oRmodel = M('Jd_registration');
            $aRegistration = $oRmodel->where(array('tid'=>$_GET['id'],'token'=>$this->token,'state'=>1))->order('id desc')->select();
            $this->assign('rescount',count($aRegistration));
            $this->assign('regist',$aRegistration);
            $scale = M('Jd_scale')->where(array('token'=>$this->token,'tid'=>$_GET['id'],'state'=>1))->select();
            $this->assign('scacount',count($scale));
            $User = M('Jd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
            $data = $this->lastrecord($_GET['id'],$User['name'],'active'); //点赞状态

            $evaluate = $this->evaluatelist($_GET['id'],'active');
            $evacount = count($evaluate);
            $this->assign(array(
                'data'=>$data,
                'evaluate'=>$evaluate,
                'evacount'=>$evacount
            ));
            $this->assign('scale',$scale);
            $this->UDisplay('activeinfo');
        }

    }

    /*报名资料页*/
    public function setactive(){
        $oModel = M('Jd_tender');
        $info = $oModel->where(array('id'=>$_GET['id']))->find();
        $this->assign('info',$info);
        $this->UDisplay('setactive');
    }

    /*报名*/
    public function activeajax(){
        $_POST['add_time'] = date('Y-m-d H:i:s');
        $_POST['token'] = $this->token;
        $_POST['state']  = 1;
        $_POST['headpic'] = $this->headpic();
        $oModel = M('Jd_registration');
        if($oModel->add($_POST)){
            script('报名成功','activelist',array('token'=>$this->token,'openid'=>$this->openid));
        }else{
            script('报名失败');
        }

    }

    /*静态文件，“入会须知”*/
    public function membership(){
        $info = M('Jd_text')->where(array('token'=>$_SESSION['token'],'type'=>0))->order('id desc')->select();
        $infos = $info[0];
        $this->assign('info',$infos);
        $this->UDisplay('membership');
    }


    /*评价、报名列表*/

    public function somelist(){
        $tid = $_GET['id'];
        $type = $_GET['type'];
        if($type == 'enlist'){
            $list = $this->to_enlist(M('Jd_registration'),$tid);
        }elseif($type == 'scale'){
            $list = $this->to_enlist(M('Jd_scale'),$tid);
        }
        $this-> assign(array(
            'list'=>$list,
            'listcount'=>count($list)
        ));
        $this->UDisplay('somelist');
    }

    public function to_enlist($model,$tid){
        if($model && $tid){
            $list = $model->where(array(
                'token'=>$this->token,
                'tid'=>$tid,
                'state'=>1
            ))->order('id')->select();
            return $list;
        }else{
            $this->error2('非法操作！');
        }
    }









}

