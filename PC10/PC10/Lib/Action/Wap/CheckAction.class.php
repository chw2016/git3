<?php
    class CheckAction extends BaseAction{

        protected $_sTplBaseDir = 'Wap/default/check';
        public function _initialize()
        {
            $this->autoShare = true;       /*分享限制*/
            parent::_initialize();
        }
        /*index首页*/

        public function index(){
            $viphtye =  M('Check_hy')->where(array(
                'openid'=> $this->openid,
                'token'=> $this->token
            ))->find();                                             /*查询会员等级*/

            $wxusers = M('Wxusers')
                ->where(array(
                    'openid'=>$this->openid
                ))->find();                                         /*查询微信名称*/
            $this->assign('wxusers', $wxusers);
            $jieji=$viphtye['vipsec'];
            $this->assign('zhehy',$jieji);
            $ns = $_GET['ns'];
            $this->assign('ns',$ns);
            $this->assign('checkid',createOrderID());    /*唯一checkid*/
            $this->display();
        }

  /*inndex页面，查询页*/
        public function inndex(){

            $cars = M('Check');
            $list = $cars->where(array(
                'openid' => $this->openid,
                'token' => $this->token))
                ->order('id desc')->find();                                                              /*在这里前查询他的历史记录的一条*/

            $listt = $cars->where(array(
                'openid' => $this->openid,
                'token' => $this->token,
            ))->order('id desc')->group('cph,cjhLastNo6,fdjLastNo6')->select();         /*在这里前查询他的历史记录全部*/

            $viphtye =  M('Check_hy')->where(array(
                'openid'=> $this->openid,
                'token'=> $this->token
            ))->find();                                                                                 /*查询是否会员*/

            $cehao = $_POST['cph'];                                                                   /*接收post传过来的值*/
            $fadon = $_POST['fdjLastNo6'];
            $cheja = $_POST['cjhLastNo6'];
            $time = date('Y-m');
            if (IS_POST){                                                                             /*查询数据库的openid是否大于4次*/
                $cars = M('Check')->where(array(
                    'openid' => $this->openid,
                    'token' => $this->token,
                    'date_time' => array(
                        'like', "$time%"
                    )
                ))->count('openid');
                $tianj['openid'] = $this->openid;                                                            /*添加数据导入查询的记录*/
                $tianj['token'] = $this->token;
                $tianj['cph'] = $_POST['cph'];
                $tianj['cjhLastNo6'] = $_POST['cjhLastNo6'];
                $tianj['fdjLastNo6'] = $_POST['fdjLastNo6'];
                $tianj['date_time'] = date('Y-m-d');

                M('Check')->add($tianj);

                if ($cars <= 3) {                                                                       /*如果他的次数小于4就让他直接查询*/
                    $this->redirect('pay', array('openid' => $this->openid, 'token' => $this->token,'cph'=>$_POST['cph']));
                }else{
                    /*是里是会员权限*/
                    $huiyan = M('Check_hy')->where(array(                                        /*会员等级判断*/
                        'token' => $this->token,
                        'openid' => $this->openid
                    ))->find();
                    /* $gaoji = $huiyan['expert'];                                                  高级会员*/
                    $viphui = $huiyan['vipsec'];                                                  /*vip秘书会员*/
                    if($viphui==1){
                       /* echo '你好亲爱的vip秘书会员';*/
                        $this->redirect('pay', array('openid' => $this->openid, 'token' => $this->token,'cph'=>$_POST['cph']));
                    }else{
                        if($viphui==2){
                             $gaojicx = M('Check_did')->where(array(
                                 'token'=>$this->token,
                                 'openid'=>$this->openid
                             ))->order()->find();
                             $fuk = strtotime($gaojicx['fk_time']);                      /*查询到的付款时间*/
                            $dqan = time('Y-m-d H:i:s');                                        /*查询当前时间*/
                                                                                        /*判断高级会员是否大于一年*/
                            if($dqan - $fuk > 31536000){
                                $ieui['vipsec'] = 3;
                                M('Check_hy')->where(array('openid'=>$this->openid))->save($ieui);
                                $this->redirect('inndex', array('openid' => $this->openid, 'token' => $this->token));
                                 }else{
                                  /* echo '你好亲爱的高级会员';*/
                                $this->redirect('pay', array('openid' => $this->openid, 'token' => $this->token,'cph'=>$_POST['cph']));
                                }
                            }
                        }
                    if(empty($huiyan)){                                             /*将用户的openid插入数据库*/
                        $rhui['openid'] = $this->openid;
                        $rhui['token'] = $this->token;
                        $rhui['vipsec'] = 3;
                        $rhui['hy_time'] =date('Y-m-d H:i:s');
                        M('Check_hy')->add($rhui);
                        $this->assign(array('shouf'=>'你的免费查询次数已完，继续查询将扣去0.1元手术费',
                            'token'=>$this->token,
                            'openid'=>$this->openid,
                            'buzhu'=>$xianca['account']
                        ));
                        }
                                                                                    /*不是高级，不是vip，已经超过4次免费机会，收费*/
                    if($viphui==3){
                        $xianca = M('Check_hy')->where(array(
                            'openid'=> $this->openid,
                            'token'=> $this->token
                        ))->find();
                        $this->assign(array('shouf'=>'你的免费查询次数已完，继续查询将扣去0.1元手术费',
                            'token'=>$this->token,
                            'openid'=>$this->openid,
                            'buzhu'=>$xianca['account']
                        ));
                        $cars = M('Check');
                        $list = $cars->where(array(
                            'openid' => $this->openid,
                            'token' => $this->token))
                            ->order('id desc')->find();            /*在这里前查询他的历史记录的一条*/
                        $this->assign('checkid',createOrderID());  /*随机id*/
                         }
                    }
                }
                                   /*这是历史记录并且等于get传过来的值*/
                if($_GET['id']!='') {
                    $id = $_GET['id'];
                    $shuce = M('Check')->where(array(
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'id' => $id
                    ))->find();                                                 /*用id查询历史记录*/

                    $cars = M('Check')->where(array(
                        'openid' => $this->openid,
                        'token' => $this->token,
                        'date_time' => array(
                            'like', "$time%"
                        )
                    ))->count('openid');
                    if ($cars <= 3) {                                                                       /*如果他的次数小于4就让他直接查询*/
                        $this->redirect('pay', array('openid' => $this->openid, 'token' => $this->token,'cph'=>$_POST['cph']));
                    } else {
                        /*是里是会员权限*/
                        $huiyan = M('Check_hy')->where(array(                                        /*会员等级判断*/
                            'token' => $this->token,
                            'openid' => $this->openid
                        ))->find();
                        /* $gaoji = $huiyan['expert'];                                                  高级会员*/
                        $viphui = $huiyan['vipsec'];                                                  /*vip秘书会员*/
                        if($viphui==1){
                           /* echo '你好亲爱的vip秘书会员';*/
                            $this->redirect('pay', array('openid' => $this->openid, 'token' => $this->token,'cph'=>$_POST['cph']));
                        }else{
                            if($viphui==2){
                                $gaojicx = M('Check_did')->where(array(
                                    'token'=>$this->token,
                                    'openid'=>$this->openid
                                ))->order()->find();
                                $fuk = strtotime($gaojicx['fk_time']);                        /*查询到的付款时间*/
                                $dqan = time('Y-m-d H:i:s');                                        /*查询当前时间*/
                                /*判断高级会员是否大于一年*/
                                if($dqan - $fuk > 31536000){
                                    $ieui['vipsec'] = 3;
                                    M('Check_hy')->where(array('openid'=>$this->openid))->save($ieui);
                                    $this->redirect('inndex', array('openid' => $this->openid, 'token' => $this->token));
                                }else{
                                    /* echo '你好亲爱的高级会员';*/
                                    $this->redirect('pay', array('openid' => $this->openid, 'token' => $this->token,'cph'=>$_POST['cph']));
                                }
                            }
                        }
                        if($viphui==3){
                            $xianca = M('Check_hy')->where(array(
                                'openid'=> $this->openid,
                                'token'=> $this->token
                            ))->find();
                            $this->assign(array('shouf'=>'你的免费查询次数已完，继续查询将扣去0.1元手术费',
                                'token'=>$this->token,
                                'openid'=>$this->openid,
                                'buzhu'=>$xianca['account']
                            ));
                            $cars = M('Check');
                            $list = $cars->where(array(
                                'openid' => $this->openid,
                                'token' => $this->token))
                                ->order('id desc')->find();
                            $this->assign('checkid',createOrderID());
                        }
                    }
                }

                $this->assign('http',C('site_url'));
                 $wxusers = M('Wxusers')
                    ->where(array(
                        'openid'=>$this->openid
                    ))->find();                                         /*查询微信名称*/
                $this->assign('wxusers', $wxusers);
                $jieji=$viphtye['vipsec'];
                $this->assign('checkid',createOrderID());    /*唯一checkid*/
                $this->assign('zhehy',$jieji);
                $this->assign('listq', $listt);
                $this->assign('list', $list);
                $this->display('inndex', array('openid' => $this->openid, 'token' => $this->token));
         }
  /*js收费0.1元*/
        public function shoufei(){
            Vendor('WebSocket.Jiaotong');
            $jk = new Jiaotong();
            $cph = base64_encode($_GET['cph']);
            $cjhLastNo6 =$_GET['cjhLastNo6'];
            $cl ='02';
            $fdjLastNo6 =$_GET['fdjLastNo6'];
            $islogin = 'True';
            $pwd = 'c6e9ec1117bc9795e5f960e9db3d841ba6ac2c59';
            $key = 'test';
            $jied = $jk->QueryWz($cph,$cjhLastNo6,$cl,$fdjLastNo6,$islogin,$pwd,$key);   /*未查的方法接口*/

            echo $jied;exit;

            $_GET['cph'];
            $_GET['fdjLastNo6'];
            $_GET['cjhLastNo6'];
            $liuchu['token'] =$this->token;
            $liuchu['openid'] = $this->openid;
            $liuchu['path'] = '交通查询缴费0.1';
            $liuchu['checkid'] = $_GET['dingdan'];
            $liuchu['money'] = 0.1;
            $liuchu['turnover'] = 1;
            $liuchu['qian_time'] = date('Y-m-d H:i:s');
            M('Check_xuli')->add($liuchu);                        /*插入消费记录*/
            $hyb = M('Check_hy')->where(array(
                'openid'=>$this->openid,
                'token'=>$this->token
            ))->find();

            $je = $hyb['account'];
            $ade = $je - 0.1;
            $liujidchu['account'] = $ade;
           $cg = M('Check_hy')->where(array(
                'openid'=>$this->openid,
                'token'=>$this->token
            ))->save($liujidchu);
            if($cg){
                $this->redirect('pay',array(
                    'token'=>$this->token,
                    'openid'=>$this->openid,
                    'cheng'=>'付费成功'
                ));
            }
        }

        /*虚拟帐户支付页*/
        public function xunizhif(){
            /*查询虚拟帐户于额*/
            $hybiao = M('Check_hy')->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))->find();
            $zqian = $hybiao['account'];        /*虚拟帐户于额*/
            $zcqian = $_GET['dispose'];         /*罚款金额*/
            $vip = $hybiao['vipsec'];
            $jied = $zqian-$zcqian;
            if($vip==3){                        /*插入在会员数据库*/
                $hy_jia ['vipsec'] = 2;
                $hy_jia ['account'] = $jied;
                M('Check_hy')->where(array(
                    'token'=>$this->token,
                    'openid'=>$this->openid
                ))->save($hy_jia);

                $kiuei ['token'] = $this->token;                       /*这是记录他已经的罚单*/
                $kiuei ['openid'] = $this->openid;
                $kiuei ['cph'] = $_GET['cph'];
                $kiuei ['site'] = $_GET['site'];
                $kiuei ['site_time'] = $_GET['site_time'];
                $kiuei ['fk_time'] = date('Y-m-d H:i:s');
                $kiuei ['dispose'] =  $zcqian;
                $kiuei ['payment'] = 1;
                M('Check_did')->add($kiuei);

                $iuejp ['token']= $this->token;                               /*把记录导入是金钱流出记录表 xuli*/
                $iuejp ['openid']= $this->openid;
                $iuejp ['path']= '交通缴费';
                $iuejp ['checkid'] = $_GET['checkid'];
                $iuejp ['money']= $zcqian;
                $iuejp ['turnover']= 1;
                $iuejp ['qian_time']=date('Y-m-d H:i:s');
                M('Check_xuli')->add($iuejp);
                $this->redirect('indent',array('token'=>$this->token,'openid'=>$this->openid));
            }else{
                $hy_jia ['account'] = $jied;
                M('Check_hy')->where(array(
                    'token'=>$this->token,
                    'openid'=>$this->openid
                ))->save($hy_jia);

                $kiuei ['token'] = $this->token;                       /*这是记录他已经的罚单*/
                $kiuei ['openid'] = $this->openid;
                $kiuei ['cph'] = $_GET['cph'];
                $kiuei ['site'] = $_GET['site'];
                $kiuei ['site_time'] = $_GET['site_time'];
                $kiuei ['fk_time'] = date('Y-m-d H:i:s');
                $kiuei ['dispose'] =  $zcqian;
                $kiuei ['payment'] = 1;
                M('Check_did')->add($kiuei);

                $iuejp ['token']= $this->token;                               /*把记录导入是金钱流出记录表 xuli*/
                $iuejp ['openid']= $this->openid;
                $iuejp ['path']= '交通缴费';
                $iuejp ['checkid'] = $_GET['checkid'];
                $iuejp ['money']= $zcqian;
                $iuejp ['turnover']= 1;
                $iuejp ['qian_time']=date('Y-m-d H:i:s');
                M('Check_xuli')->add($iuejp);
                $this->redirect('indent',array('token'=>$this->token,'openid'=>$this->openid));
            }

        }
/*分享设置*/
        public function fengx(){
            $timd = date('Y-m');
            $liuchu = M('Check_xuli')->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid,
                'turnover'=>array('eq',5),
                'qian_time'=>array( 'like', "$timd%"),'and'
            ))->find();
                if(empty($liuchu)){
                        $hyb = M('Check_hy')->where(array(
                            'token'=>$this->token,
                            'openid'=>$this->openid
                        ))->find();
                      $hyq = $hyb['account'] + 2;
                      $jide ['account']=$hyq;
                    M('Check_hy')->where(array('openid'=>array('eq',$this->openid)))->save($jide);

                    $liuchu ['path'] = '分享赠送';
                    $liuchu ['checkid'] = createOrderID();
                    $liuchu ['money'] = 2;
                    $liuchu ['turnover'] = 5;
                    $liuchu ['qian_time'] = date('Y-m-d H:i:s');
                    M('Check_xuli')->add($liuchu);

                    $this->success('首次分享赠送两元！');
                }else{
                    $this->error('分享成功！');
                }

        }
/*查看罚款页*/
        public function indent(){
/*查询数据库是否有付款后的记录*/

/*如果payment等于1那么就是已付款*/
            $fky = M('Check_did')->where(array(
                'openid'=>$this->openid,
                'token'=>$this->token,
                'payment'=>array('eq',1)
            ))->order('site_time')->select();

            $this->assign(array(
                'wanc'=>$fky,
                'openid'=>$this->openid,
                'token'=>$this->token
            ));

            $this->display();
        }
/*在线支付页*/
        public function pay(){
            $yue = M('Check_hy')->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))->find();
            $this->assign('cph',$_GET['cph']);
            $this->assign('hyye',$yue['account']); /*会员于额*/
            $this->assign('checkid',createOrderID());    /*唯一checkid*/
            $this->assign('list',$_GET);
            $this->assign('cheng',$_GET['cheng']);   /*提示查询付费成功*/
            $this->display();
        }

/*在线确定收费页*/
        public function qued(){
             $this->redirect('indent',array('token'=>$this->token,'openid'=>$this->openid));
        }


/*这是表单年审页*/
        public function form(){
                if(IS_POST){
                    $fomeid['openid'] = $this->openid;
                    $fomeid['token'] = $this->token;;
                    $fomeid['username'] = $_POST['name'];
                    $fomeid['dianh'] = $_POST['tel'];
                    $fomeid['cph'] = $_POST['car'];
                    $fomeid['sbcph'] = $_POST['look'];
                    $fomeid['fadong'] = $_POST['motor'];
                    $fomeid['nstime'] = date('Y-m-d H:i:s');
                    $nians = M('check_ns')->add($fomeid);
                    if($nians){
                        $this->redirect('index', array('openid' => $this->openid, 'token' => $this->token,'ns'=>'保存成功'));
                    }
                }
            $this->display();
        }
 /*付款导入会员资料，付款记录*/
        public function jiluye(){
            echo "你好你确定你付款了吗，亲爱的";
        }

/*用户质料导出页*/
        public function daochu(){
            $oeuoe =  M('Check_hy')->field('id,openid,expert,vipsec,account,hy_time')->select();
           /* $jiet = C('site_url');
            foreach($oeuoe as $k => $v){
                $oeuoe[$k]['url'] = "$jiet/index.php?g=Wap&m=Gjlog&a=daochu&&id=".$v['id'];
            }*/
            Excel::arr2ExcelDownload($oeuoe,
                array('ID','openid', '是否高级', '是否vip秘书','虚拟于额', '加入时间'
                ), '交通用户导出');
            }
    }



 ?>