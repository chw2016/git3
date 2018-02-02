<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/9
 * Time: 16:30
 */
class MediaAction extends UserAction{

    private $teToken = null;

    public function _initialize() {
        /*特殊账号*/
        $this->teToken = '8a71b21a11dd5212bd74cee41dafab64';
        $this->assign(array(
            'teToken'=>$this->teToken
        ));
        parent::_initialize();
    }
    /*任务中心管理*/
    public function task(){
        //echo $this->teToken;exit;
        $taskmodel = M('Media_task');
       // $labelmodel = M('Media_label');
        $classificationmodel = M('Media_classification');
        $oenterprise  = M('Media_enterprise');
        if(IS_POST){
            $duty = $_POST['duty'];
            if($duty ==0){
                $count = $taskmodel->where(array('token'=>$this->token,'is_recommend'=>1))->count();
                $Page = new Page($count,15);
                $show = $Page->show();
                $list = $taskmodel->where(array('token'=>$this->token,'is_recommend'=>1))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
                foreach($list as $key=>$value){
                    $classification = $classificationmodel->where(array('id'=>$value['cid'],'token'=>$this->token))->find();
                    $list[$key]['cname'] = $classification['cname'];
                    $aenterprise = $oenterprise->where(array('id'=>$value['qid']))->find();
                    if($value['qid']){
                        $list[$key]['qid'] = $aenterprise['username'];
                    }else{
                        $list[$key]['qid'] = '官方';
                    }

                }
                $this->assign('list',$list);
                $this->assign('page',$show);
                $this->display();
            }elseif($duty ==1){
                $count = $taskmodel->where(array('token'=>$this->token))->count();
                $Page = new Page($count,15);
                $show = $Page->show();
                $list = $taskmodel->where(array('token'=>$this->token))->order('commission desc,addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
                foreach($list as $key=>$value){
                    $classification = $classificationmodel->where(array('id'=>$value['cid'],'token'=>$this->token))->find();
                    $list[$key]['cname'] = $classification['cname'];
                    $aenterprise = $oenterprise->where(array('id'=>$value['qid']))->find();
                    if($value['qid']){
                        $list[$key]['qid'] = $aenterprise['username'];
                    }else{
                        $list[$key]['qid'] = '官方';
                    }
                }
                $this->assign('list',$list);
                $this->assign('page',$show);
                $this->display();
            }
        }else{
            $count = $taskmodel->where(array('token'=>$this->token))->count();
            $Page = new Page($count,15);
            $show = $Page->show();
            $list = $taskmodel->where(array('token'=>$this->token))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($list as $key=>$value){
                $classification = $classificationmodel->where(array('id'=>$value['cid'],'token'=>$this->token))->find();
                $list[$key]['cname'] = $classification['cname'];
                $aenterprise = $oenterprise->where(array('id'=>$value['qid']))->find();
                if($value['qid']){
                    $list[$key]['qid'] = $aenterprise['username'];
                }else{
                    $list[$key]['qid'] = '官方';
                }
            }
            $this->assign('list',$list);
            $this->assign('page',$show);
            $this->display();
        }

    }
    //添加任务
    public function addtask(){
        $taskmodel = M('Media_task');
        $labelmodel = M('Media_label');
        $classificationmodel = M('Media_classification');
        if(IS_AJAX){
            $variable['id'] = $_POST['id']?$_POST['id']:'';

            $variable['key'] = $_POST['key'];
            $variable['title'] = $_POST['title'];
            $variable['cid'] = $_POST['cid'];
            $variable['lid'] = $_POST['lid'];
            $variable['pic'] = $_POST['pic'];
            $variable['starttime'] = $_POST['starttime'];
            $variable['endtime'] = $_POST['endtime'];
            $variable['commission'] = $_POST['commission'];
            $variable['abstract'] = $_POST['abstract'];
            $variable['content'] = $_POST['content'];
            $variable['pid'] =!empty($_POST['pid'])?$_POST['pid']:'';
            $variable['type'] = $_POST['type'];
            $variable['is_recommend'] = $_POST['is_recommend'];
            $variable['status'] = $_POST['status'];
            $variable['token'] = $this->token;
            $variable['is_task'] =!empty($_POST['is_task'])?$_POST['is_task']:'';
            if($variable['id']){
                $savetasks = $taskmodel->where(array('token'=>$this->token,'id'=>$variable['id']))->save($variable);
                if($savetasks){
                    $this->success('编辑成功！',U(MODULE_NAME.'/task',array('token'=>session('token'))));
                }else{
                    $this->error('编辑失败！',U(MODULE_NAME.'/addtask',array('token'=>session('token'),'tid'=>$variable['id'])));
                }
            }else{
                $variable['addtime'] = time();
                $variable['date'] = date('Y-m-d');
                $addtasks = $taskmodel->data($variable)->add();
                if($addtasks){
                    $this->success('添加成功！',U(MODULE_NAME.'/task',array('token'=>session('token'))));
                }else{
                    $this->error('添加失败！',U(MODULE_NAME.'/addtask',array('token'=>session('token'))));
                }

            }
        }else{
            $tid = $_GET['tid']?$_GET['tid']:'';
            $productmodel = M('Product_new');
            if($tid){
                $key = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
                $this->assign('data',$key);
                $task = $taskmodel->where(array('token'=>$this->token,'id'=>$tid))->find();
                $classification = $classificationmodel->where(array('token'=>$this->token))->select();
                $label = $labelmodel->where(array('token'=>$this->token))->select();
                $product = $productmodel->where(array('token'=>$this->token))->order('time desc')->select();
                $aTask = $taskmodel->field('id,title')->where(array('token'=>$this->token,'pid'=>array('neq','')))->order('addtime desc')->select();
                //print_r($aTask);exit;
                $this->assign(array(
                    'product'=>$product,
                    'task'=>$task,
                    'label'=>$label,
                    'tid'=>$tid,
                    'aTask'=>$aTask,
                    'classification'=>$classification
                ));
                $this->display();
            }else{
                $key = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
                $this->assign('data',$key);
                $classification = $classificationmodel->where(array('token'=>$this->token))->select();
                $label = $labelmodel->where(array('token'=>$this->token))->select();
                $aTask = $taskmodel->field('id,title')->where(array('token'=>$this->token,'pid'=>array('NEQ','')))->order('addtime desc')->select();
                $product = $productmodel->where(array('token'=>$this->token))->order('time desc')->select();
                $this->assign('label',$label);
                $this->assign('aTask',$aTask);
                $this->assign('product',$product);
                $this->assign('tid',$tid);
                $this->assign('classification',$classification);
                $this->display();
            }
        }

    }
    /*任务添加时标签的选择*/
    public function ajaxPid(){
        if(IS_AJXA){
            $iCid = $this->_post('cid','intval');
            $sToken = $this->token;
            $oLabelModel = M('Media_label');
            $getResult = $oLabelModel->where(array(
                'token'=>$sToken,
                'cid'=>$iCid
            ))->select();
            // $name = array();
            $str = "";
            foreach($getResult as $key => $value){
                //$name[$key] = $getResult[$key]['name'];
               // <input id="task{weikucms:$i}" name="labels" value="{weikucms:$so.id}" type="checkbox"/>{weikucms:$so.lname}
                //<option value="'.$value["id"].'">'.$value["name"].'</option>
                $str.=" <div style='width:98px; float: left;' class='lab' >".
                    "<input type='checkbox' name='labels' value='".$value['id']."'>".$value['lname']."</div>";
            }
            $array = array(
                'option' => $str
            );
            $array = json_encode($array,true);
            echo $array;
        }
    }
    //删除任务
    public function deltask(){
        $taskmodel = M('Media_task');
        $id =$_GET['tid'];
        $dtask = $taskmodel->where(array('token'=>$this->token,'id'=>$id))->delete();
        if($dtask){
            $this->success('删除成功！',U(MODULE_NAME.'/task',array('token'=>session('token'))));
        }else{
            $this->error('删除失败！',U(MODULE_NAME.'/task',array('token'=>session('token'))));
        }
    }

    /*查看领取任务的详情*/
    public function taskuser(){
        $iTid = $_GET['tid'];
        $oUserTask =M('Media_user_tasks');
        $oWxuserModel = M('Wxuser');
        $oWxusersModel = M('Wxusers');
        $icount =$oUserTask->where(array(
            'token'=>$this->token,
            'task_id'=>$iTid
        ))->count();
        $page = new Page($icount,15);
        $show = $page->show();
        $aUserTask = $oUserTask->where(array(
            'token'=>$this->token,
            'task_id'=>$iTid
        ))->order('add_time desc')->limit(
            $page->firstRow.','.$page->listRows)->select();
        foreach($aUserTask as $iK=>$aValure){
            $aUser = $oWxuserModel->where(array(
                'token'=>$this->token
            ))->find();
            $aUsers = $oWxusersModel->where(array(
                'uid'=>$aUser['id'],
                'openid'=>$aUserTask['openid']
            ))->find();
            $aUsertasks = $oUserTask->where(array(
                'openid'=>$this->openid,
                'token'=>$this->$aUserTask['openid']
            ))->find();
            $aUserTask[$iK]['nickname'] = $aUsers['nickname'];
            $aUserTask[$iK]['name'] = $aUsertasks['nickname'];
        }

        $this->assign(array(
            'users'=>$aUserTask,
            'page'=>$show
        ));
        $this->display();
    }


    /*任务管理之大类管理*/
    public function classification(){
        $classificationmodel = M('Media_classification');
        $count = $classificationmodel->where(array('token'=>$this->token))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $list = $classificationmodel->where(array('token'=>$this->token))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
    public function addclassification(){
    //实现添加、修改大类
        $classificationmodel = M('Media_classification');
        if(IS_AJAX){
            $variable['id'] = $_POST['id']?$_POST['id']:'';
            $variable['cname'] = $_POST['cname'];
            $variable['token'] = $this->token;
            $data = $classificationmodel->where(array('token'=>$this->token,'cname'=>$variable['cname']))->find();
            if($data){
                $this->error('任务大类名称已存在！',U(MODULE_NAME.'/addclassification',array('token'=>session('token'))));
            }else{
                if($variable['id']){
                    $savedata = $classificationmodel->where(array('token'=>$this->token,'id'=>$variable['id']))->save($variable);
                    if($savedata){
                        $this->success('编辑成功！',U(MODULE_NAME.'/classification',array('token'=>session('token'))));
                    }else{
                        $this->error('编辑失败！',U(MODULE_NAME.'/addclassification',array('token'=>session('token'),'cid'=>$variable['id'])));
                    }
                }else{
                    $variable['addtime'] = time();
                    $adddata = $classificationmodel->data($variable)->add();
                    if($adddata){
                        $this->success('操作成功！',U(MODULE_NAME.'/classification',array('token'=>session('token'))));
                    }else{
                        $this->error('操作失败！',U(MODULE_NAME.'/addclassification',array('token'=>session('token'),'cid'=>$variable['id'])));
                    }
                }
            }
        }else{
            $cid = $_GET['cid']?$_GET['cid']:'';
            if($cid){
                $task = $classificationmodel->where(array('token'=>$this->token,'id'=>$cid))->find();
                $this->assign('task',$task);
                $this->display();
            }else{
                $this->display();
            }
        }
  }
   public function delclassification(){
    //任务管理页面；实现删除大类
        $classificationmodel = M('Media_classification');
        $id =$_GET['cid'];
        $dtask = $classificationmodel->where(array('token'=>$this->token,'id'=>$id))->delete();
        if($dtask){
            $this->success('删除成功！',U(MODULE_NAME.'/classification',array('token'=>session('token'))));
        }else{
            $this->error('删除失败！',U(MODULE_NAME.'/classification',array('token'=>session('token'))));
        }
  }

/*任务管理之标签管理*/
   public function label(){
    //查看大类列表
        $labelmodel = M('Media_label');
        $classificationmodel = M('Media_classification');
        $count = $labelmodel->where(array('token'=>$this->token))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $list = $labelmodel->where(array('token'=>$this->token))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $key=>$value){
            $classification = $classificationmodel->where(array('token'=>$this->token,'id'=>$value['cid']))->find();
            $list[$key]['cname'] = $classification['cname'];
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
   public function addlabel(){
    //任务管理页面；实现添加、修改标签
        $labelmodel = M('Media_label');
        $classificationmodel = M('Media_classification');
        if(IS_AJAX){
            $variable['id'] = $_POST['id']?$_POST['id']:'';
            $variable['cid'] = $_POST['cid'];
            $variable['lname'] = $_POST['lname'];
            $variable['token'] = $this->token;
            $data = $labelmodel->where(array('token'=>$this->token,'lname'=>$variable['lname'],'cid'=>$variable['cid']))->find();
            if($data){
                $this->error('此任务标签名称已存在！',U(MODULE_NAME.'/addlabel',array('token'=>session('token'))));
            }else{
                if($variable['id']){
                    $savedata = $labelmodel->where(array('token'=>$this->token,'id'=>$variable['id']))->save($variable);
                    if($savedata){
                        $this->success('编辑成功！',U(MODULE_NAME.'/label',array('token'=>session('token'))));
                    }else{
                        $this->error('编辑失败！',U(MODULE_NAME.'/addlabel',array('token'=>session('token'),'cid'=>$variable['id'])));
                    }
                }else{
                    $variable['addtime'] = time();
                    $adddata = $labelmodel->data($variable)->add();
                    if($adddata){
                        $this->success('操作成功！',U(MODULE_NAME.'/label',array('token'=>session('token'))));
                    }else{
                        $this->error('操作失败！',U(MODULE_NAME.'/addlabel',array('token'=>session('token'),'cid'=>$variable['id'])));
                    }
                }
            }
        }else{
            $lid = $_GET['lid']?$_GET['lid']:'';
            if($lid){
                $classification = $classificationmodel->where(array('token'=>$this->token))->select();
                $task = $labelmodel->where(array('token'=>$this->token,'id'=>$lid))->find();
                $this->assign('classification',$classification);
                $this->assign('task',$task);
                $this->display();
            }else{
                $classification = $classificationmodel->where(array('token'=>$this->token))->select();
                $this->assign('classification',$classification);
                $this->display();
            }
        }
  }
   public function dellabel(){
    //任务管理页面；实现删除标签
        $labelmodel = M('Media_label');
        $id =$_GET['lid'];
        $dtask = $labelmodel->where(array('token'=>$this->token,'id'=>$id))->delete();
        if($dtask){
            $this->success('删除成功！',U(MODULE_NAME.'/label',array('token'=>session('token'))));
        }else{
            $this->error('删除失败！',U(MODULE_NAME.'/label',array('token'=>session('token'))));
        }
    }
  /*老张的添加，从这里开始*/
  // 财务管理
  public function financial(){
       $usersModel  = M('Media_users');
       $tasksModel = M('Media_user_tasks');
       $count = $usersModel->where(array('token'=>$this->token))->count();  
       $Page = new Page($count,15);    
       $show = $Page->show();
       $list = $usersModel->where(array('token'=>$this->token))->limit($Page->firstRow.','.$Page->listRows)->select();

       //红包金额暂且搁置
       $this->assign('info',$list);
       $this->assign('page',$show);
       $this->display();	
  }
  // 财务管理支付绑定
  public function payment(){
       $this->display();
  }
  // 推广明细
  public function details(){
       if ($this->_get('type') == "extension") {
           // 这里是推广明细
           $tasksModel = M('Media_user_tasks');
           $count = $tasksModel->where(array('token'=>$this->token,'openid'=>$this->_get('openid')))->count();  
           $Page = new Page($count,15);    
           $show = $Page->show();
           $list = $tasksModel->field('task_name,add_time,money')->where(array('token'=>$this->token,'openid'=>$this->_get('openid')))->limit($Page->firstRow.','.$Page->listRows)->select();
       }elseif ($this->_get('type') == "invitation") {
           // 这里是邀请明细
           $usersModel  = M('Media_users');
           $count = $usersModel->where(array('token'=>$this->token,'from_openid'=>$this->_get('openid')))->count();  
           $Page = new Page($count,15);    
           $show = $Page->show();
           $list = $usersModel->field('nickname,add_time')->where(array('token'=>$this->token,'from_openid'=>$this->_get('openid')))->limit($Page->firstRow.','.$Page->listRows)->select();
           $this->assign('invitation',1);
       }else{
           // 红包明细 
       }
       $this->assign('page',$show);
       $this->assign('info',$list);
       $this->display();
  }
  // 结算
  public function accounts(){
       $id = $this->_get('tid','intval');
       $usersModel = M('Media_users');
       $list = $usersModel->field('nickname,bank_card,bank_name,money')->where(array('id'=>$id))->find();
       $this->assign('info',$list);
       $this->display();
  }
  // 结算后的金额
  public function counts(){
       if(IS_AJAX){
            if (IS_POST) {
                $id = $this->_get('id','intval');
                $moneyGo = $this->_post('moneyGo');
                $money = M('Media_users')->where(array('id'=>$id))->getField('money');
                // 最开始的金额数量
                $begin = $money;
                $money = $money - $moneyGo;
                // 结算后的金额数量
                $end = $money;
                $data = array(
                        'money' => $money
                    );
                $record = array(
                        'uid' => $id,
                        'now_money' => $end,
                        'before_money' => $begin,
                        'lose_money' => $moneyGo,
                        'set_date' => date("Y-m-d H:i:s"),
                        'token' => $this->_get('token')
                    );
                $infos = M('Media_users')->where(array('id'=>$id))->save($data);
                $insertRecord = M('Media_setrecord')->data($record)->add();
                if ($infos && $insertRecord) {
                    echo $this->encode(array('status'=>1,'info'=>'结算成功!','url'=>'index.php?g=User&m=Media&a=financial&token='.$this->token));
                }else{
                     echo $this->encode(array('status'=>0,'info'=>'结算失败!'));
                }
            }
       }
  }

  // 用户中心
  public function userCenter(){
       $oUserModel = M('Wxuser');
       $oUsersModel = M('Wxusers');
       $oInvitModel = M('Media_inviterecord');
       $usersModel  = M('Media_users');
       $count = $usersModel->where(array('token'=>$this->token))->count();  
       $Page = new Page($count,15);    
       $show = $Page->show();
       $list = $usersModel->order('add_time desc')->where(array('token'=>$this->token))->limit($Page->firstRow.','.$Page->listRows)->select();
       foreach ($list as $key => $value) {
           $aUser = $oUserModel->where(array('token'=>$this->token))->find();
           $aUsers= $oUsersModel->where(array('uid'=>$aUser['id'],'openid'=>$value['openid']))->find();
           $list[$key]['name'] = $aUsers['nickname'];
           $info = $this->tongji($value['openid']);
           $aMans = array();
           $aMans[0] = $info['iOne'];
           $aMans[1] = $info['iTwo'];
           $aMans[2] = $info['iThree'];
           $aMans[3] = $info['total'];

           //$aMans[3] = intval($oInvitModel->where(array('token'=>$this->token,'openid'=>$value['openid']))->count())-1;
           $list[$key]['aMans'] = $aMans;
       }
       $this->assign('info',$list);
       $this->assign('page',$show);
       $this->display();
  }


    /*分销会员的统计*/
    public function tongji($openid){
        $set=M('Product_setting_new')->field('one,two,three')->where(array('token'=>$this->token))->find();
        $iOne = $iTwo = $iThree = 0;
        $aOne = $aTwo = $aThree = array();
        if($set['one']>0){
            $list=M('Media_users')
                ->field('id,nickname,openid,date,qq,phone')
                ->where(array(
                    'from_openid'=>$openid,
                    'token'     =>$this->token,
                    'status'    =>1,
                    /* 'is_buy'    => 1*/
                ))->select();
            $iOne = count($list);
            $aOne = $list;
        }

        if($set['one']>0&&$set['two']>0){
            //    $list1=M('Media_users')->field("tp_media_users.nickname,tp_media_users.openid,a.nickname as nickname2,a.openid as openid2")->join("join tp_media_users as a on tp_media_users.openid=a.from_openid")->where(array('tp_media_users.from_openid'=>$this->openid))->select();
            $list1='';
            foreach($list as $k=>$v){
                $list1=M('Media_users')
                    ->field('id,nickname,openid,date,qq,phone')
                    ->where(array(
                        'from_openid'=>$v['openid'],
                        'token'=>$this->token,
                        'status'=>1,
                        /* 'is_buy' => 1*/
                    ))->select();
                foreach($list1 as $v){
                    $v['message'] = 2;
                    $iTwo++;
                    $aTwo[] = $v;
                    array_push($list,$v);
                }
                if($set['one']>0&&$set['two']>0&&$set['three']>0){
                    //    $list1=M('Media_users')->field("tp_media_users.nickname,tp_media_users.openid,a.nickname as nickname2,a.openid as openid2")->join("join tp_media_users as a on tp_media_users.openid=a.from_openid")->where(array('tp_media_users.from_openid'=>$this->openid))->select();
                    $list2='';
                    foreach($list1 as $k=>$v){
                        $list2=M('Media_users')
                            ->field('id,nickname,openid,date,qq,phone')
                            ->where(array(
                                'from_openid'=>$v['openid'],
                                'token'=>$this->token,
                                'status'=>1,
                                /* 'is_buy' => 1*/
                            ))->select();
                        foreach($list2 as $v){
                            $v['message'] = 3;
                            $iThree++;
                            $aThree[] = $v;
                            array_push($list,$v);
                        }

                    }
                }
            }
        }
        $alist =array_merge($aOne,$aTwo,$aThree);
        foreach($alist as $k=>$val){
            $aUser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $aUsers = M('Wxusers')->where(array('uid'=>$aUser['id'],'openid'=>$val['openid']))->find();
            $alist[$k]['openid'] = $aUsers['nickname'];
        }
        $info['iOne'] = $iOne;
        $info['iTwo'] = $iTwo;
        $info['iThree'] = $iThree;
        $info['total'] = $iThree + $iTwo + $iOne;
        $info['list'] = $alist;
        return $info;
       // P($info);

    }
   /*个人信息详情*/
    public function UserInfo(){
        $oUserModel = M('Wxuser');
        $oUsersModel = M('Wxusers');
        $UserModel = M('Media_users');
        $where = array('token'=>$this->token,'openid'=>$this->_get('openid'));
        $list = $UserModel->where($where)->find();
        $aUser = $oUserModel->where(array('token'=>$this->token))->find();
        $aUsers= $oUsersModel->where(array('uid'=>$aUser['id'],'openid'=>$list['openid']))->find();
        $aUseres= $oUsersModel->where(array('uid'=>$aUser['id'],'openid'=>$list['from_openid']))->find();
        //print_r($list);exit;  来自谁    昵称
        $list['name'] = $aUsers['nickname'];
        $list['lname'] = $aUseres['nickname'];

        $this->assign('info',$list);
        $this->display();
    }
    /*个人邀请记录*/
    public function InviterInfo(){
        $openid = $this->_get('openid');
        $info = $this->tongji($openid);
        /*$count = array_count_values($info);
        $Page = new Page($count,15);
        $show = $Page->show();*/
        $list = $info['list'];
        //print_r($list);
        $this->assign('info',$list);
        //$this->assign('page',$show);
        $this->display();
    }
    /*个人佣金记录*/
    public function yongjinInfo(){
        $oUserModel = M('Wxuser');
        $oUsersModel = M('Wxusers');
        $UserModel = M('Edia_user_commission');
        $UsersModel = M('Media_users');
        $where = array('token'=>$this->token,'openid'=>$this->_get('openid'));
        $count = $UserModel->where($where)->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $list = $UserModel->order('add_time desc')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $k=>$val){
            $aUser = $oUserModel->where(array('token'=>$this->token))->find();
            $aUsers= $oUsersModel->where(array('uid'=>$aUser['id'],'openid'=>$val['g_openid']))->find();
            $aUseres = $UsersModel->where(array('token'=>$this->token,'opneid'=>$val['g_openid']))->find();
            $list[$k]['name'] = $aUseres['nickname'];
            $list[$k]['nickname'] = $aUsers['nickname'];
            $aOrder = M('Product_cart_new')->where(array('orderid'=>$val['orderid']))->find();  //price  订单额
            //$aYjinfo[$ks]['name']=$aProduct['name'];  //商品名
            $list[$k]['price'] = $aOrder['price'];  //订单额
        }
        $this->assign('info',$list);
        $this->assign('page',$show);
        $this->display();
    }


      // 站内消息显示
     public function addInfo(){
        $stationModel = M('Media_station');
        $count = $stationModel->where(array('token'=>$this->token))->count();
        $Page = new Page($count,15);
        $lists = $stationModel->order('info_time desc')->where(array('token'=>$this->token))->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('info',$lists);
        $this->assign('page',$Page->show());
        $this->display();
     }

    //会员提升
    public function SetUser(){

        $oUserModel = M('Media_users');
        $iTem = $oUserModel->where(array('token'=>$this->token,'openid'=>$this->_get('openid')))->find();
        if($iTem){
            $data['type'] =1;
           $com = $oUserModel->where(array('token'=>$this->token,'openid'=>$this->_get('openid')))->save($data);
            if($com){
                $this->success('提升成功！',U(MODULE_NAME.'/userCenter',array('token'=>$this->token)));
            }else{
                $this->error('提升失败！',U(MODULE_NAME.'/userCenter',array('token'=>$this->token)));
            }
        }else{
            $this->error('非法操作！',U(MODULE_NAME.'/userCenter',array('token'=>$this->token)));
        }
    }

     // 添加站内消息
     public function station(){
        $stationModel = M('Media_station');
        $id = $this->_get('id','intval');
        $tid = $this->_get('tid','intval');
         $aInfo = $stationModel->where(array('id'=>$id))->find();
         $this->assign('info',$aInfo);
        $this->assign('id',$id);
        $this->assign('tid',$tid);
        $this->display();
     }
     // 站内消息数据接收
     public function stationData(){
        $stationModel = M('Media_station');
        if (IS_AJAX) {
            if (IS_POST) {
                if(isset($_POST['type']) == 'add'){
                    // 添加数据行
                    $data = array(
                            'title' => $_POST['infoTitle'],
                            'type' => 1,
                            'content' => htmlspecialchars_decode($_POST['content']),
                            'token' => $this->_get('token'),
                            'info_time' => date("Y-m-d H:i:s")
                        );
                    $token = $this->_get('token');
                    $tid = $this->_get('tid','intval');
                    $info = $stationModel->data($data)->add();
                    if ($info) {
                         $this->success('添加成功！',U(MODULE_NAME.'/addInfo',array('token'=>$token,'tid'=>$tid)));
                    }else{
                        $this->error('添加失败！',U(MODULE_NAME.'/addInfo',array('token'=>$token,'tid'=>$tid)));
                    }
                }else{
                    // 编辑数据行
                     $data = array(
                            'title' => $_POST['infoTitle'],
                            'content' => htmlspecialchars_decode($_POST['content']),
                         'type'=>1
                        );
                   
                     $info = $stationModel->where(array('id'=>$this->_post('id','intval')))->save($data);
                     if ($info) {
                         $this->success('修改成功！',U(MODULE_NAME.'/addInfo',array('token'=>$this->_get('token'),'tid'=>$this->_get('tid','intval'))));
                    }else{
                        $this->error('修改失败！',U(MODULE_NAME.'/addInfo',array('token'=>$this->_get('token'),'tid'=>$this->_get('tid','intval'))));
                    }
                }
            }
        }
     } 
     // 站内消息删除
     public function stationDel(){
        $where['id'] = $this->_get('id','intval');
        if (M('Media_station')->where($where)->delete()) {
            $this->success('删除成功！',U(MODULE_NAME.'/addInfo',array('token'=>$this->_get('token'),'tid'=>$this->_get('tid','intval'))));
        }else{
            $this->error('删除失败！',U(MODULE_NAME.'/addInfo',array('token'=>$this->_get('token'),'tid'=>$this->_get('tid','intval'))));
        }
     }
     // 站内消息查看
     public function scan(){
        $where['id'] = $this->_get('id','intval');
        $infos = M('Media_station')->where($where)->find();
        $this->assign('tid',$where['uid']);
        $this->assign('info',$infos);
        $this->display();
     }
     // 在结束过后生成一条结算记录
     public function setRecord(){
        $setRecordModel = M('Media_setrecord');
        $tid = $this->_get('tid');
        $token = $this->_get('token');
        $count = $setRecordModel->where(array('uid'=>$tid,'tp_media_setrecord.token'=>$token))->join(array('tp_media_users as users on tp_media_setrecord.uid = users.id'))->count();
        $Page = new Page($count,15);
        $lists = $setRecordModel->where(array('uid'=>$tid,'tp_media_setrecord.token'=>$token))->join(array('tp_media_users as users on tp_media_setrecord.uid = users.id'))->field('nickname,before_money,now_money,lose_money,set_date,bank_name,bank_card')->order('set_date desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('info',$lists);
        $this->assign('page',$Page->show());
        $this->display();
     }


     // 配置中心
     public function setCenter(){
         if (M('Media_setcenter')->select()) {
            $setCenter = M('Media_setcenter')->where(array('token'=>$this->token))->find();
            $this->assign('info',$setCenter);
         }
        $this->display();
     }
     // 配置中心接收数据
     public function setCenterData(){
        if (IS_AJAX) {
            if(IS_POST){
                #修改
                 $data = array(
                            'redfirst' => $this->_post('redFirst'),
                            'redsecond' => $this->_post('redSecond'),
                            'redThere' =>$this->_post('redThere'),
                            'invite' => $this->_post('inviteAmount'),
                            'money'=>$this->_post('money'),
                            'token' => $this->_get('token')
                        );
                if (M('Media_setcenter')->where(array('token'=>$this->_get('token')))->select()) {
                    //print_r($data);exit;
                    $setCenter = M('Media_setcenter')->field('id')->where(array('token'=>$this->_get('token')))->find();
                    $info = M('Media_setcenter')->where(array('id'=>$setCenter['id'],'token'=>$this->_get('token')))->save($data);
                }else{
                    # 添加

                    $info = M('Media_setcenter')->data($data)->add();
                    
                }
                if ($info) {
                    $this->success('处理成功！',U(MODULE_NAME.'/setCenter',array('token'=>$this->_get('token'))));
                }else{
                    $this->error('处理失败！',U(MODULE_NAME.'/setCenter',array('token'=>$this->_get('token'))));
                }
            }
        }
     }

     // 统计报表
     public function report(){
        $token = $this->_get('token');
        $cartNewModel = M('product_cart_new');
        $catNewModel = M('product_cat_new');
        $newModel = M('product_new');
        $orderCount = $cartNewModel->where(array('token'=>$token))->count();
        $sellOrder = $cartNewModel->where(array('token'=>$token,'paid'=>1))->count();
        $wxUserModel = M('wxuser');
        $wxUsersModel = M('wxusers');
        $wxid = $wxUserModel->where(array('token'=>$token))->getField('id');
        $fansCount = $wxUsersModel->where(array('uid'=>$wxid))->count();
        $data = array(
                'orderTotal' => $orderCount,
                'sellTotal' => $sellOrder,
                'fansTotal' => $fansCount
            );
        $this->assign('info',$data);
        $count = $catNewModel->join(array('RIGHT JOIN tp_product_new as newTable on tp_product_cat_new.id = newTable.catid'))->where(array('tp_product_cat_new.token'=>$token))->count();
        $Page = new Page($count,15);
        $lists = $catNewModel->order('tp_product_cat_new.time desc')->join(array('RIGHT JOIN tp_product_new as newTable on tp_product_cat_new.id = newTable.catid'))->where(array('tp_product_cat_new.token'=>$token))->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($lists as $key => $value) {
            $sellnum = $cartNewModel->where(array('token'=>$token,'productid'=>$value['id']))->count();
            $lists[$key]['sellnum'] = $sellnum;
        }
       
	    $this->assign('list',$lists);
        $this->assign('page',$Page->show());
        $this->display();
     }
     // 订单总数数据以及销售总数
     public function orderInfo(){
        $token = $this->_get('token');
        $cartNewModel = M('product_cart_new');
        if (isset($_GET['type']) == 'sell') {
            $where = array(
                    'token' => $token,
                    'paid' => 1
                );
            $data = $this->Pages($cartNewModel,$where);
            $this->assign('flag',1);
        }else{
             $where = array('token' => $token);
             $data = $this->Pages($cartNewModel,$where);
        }
        
        
        $this->assign('info',$data['info']);
        // 分页链接
        $this->assign('page',$data['show']);
        $this->display();
     }
     // 分页函数
     protected function Pages($table,$condition = ''){
        // condition 例如：$condition = array('token'=>$token,'pid'=>1);
        $count = $table->where($condition)->count();
        $Page = new Page($count,15);
        $lists = $table->order('time desc')->where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();
        $data = array(
                'info' => $lists,
                'show' => $Page->show()
            );
        return $data;
     }
     // 单品排行
     public function sell(){
        $token = $this->_get('token');
        $cartNewModel = M('product_cart_new');
        $catNewModel = M('product_cat_new');
        $newModel = M('product_new');
        $orderCount = $cartNewModel->where(array('token'=>$token))->count();
        $sellOrder = $cartNewModel->where(array('token'=>$token,'paid'=>1))->count();
        $wxUserModel = M('wxuser');
        $wxUsersModel = M('wxusers');
        $wxid = $wxUserModel->where(array('token'=>$token))->getField('id');
        $fansCount = $wxUsersModel->where(array('uid'=>$wxid))->count();
        $data = array(
                'orderTotal' => $orderCount,
                'sellTotal' => $sellOrder,
                'fansTotal' => $fansCount
            );
        $this->assign('info',$data);
        // 获取商品销售报表
      
       $this->assign('info',$data);
        $count = $catNewModel->join(array('RIGHT JOIN tp_product_new as newTable on tp_product_cat_new.id = newTable.catid'))->where(array('tp_product_cat_new.token'=>$token))->count();
        $Page = new Page($count,15);
        $lists = $catNewModel->order('tp_product_cat_new.time desc')->join(array('RIGHT JOIN tp_product_new as newTable on tp_product_cat_new.id = newTable.catid'))->where(array('tp_product_cat_new.token'=>$token))->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($lists as $key => $value) {
            $sellnum = $cartNewModel->where(array('token'=>$token,'productid'=>$value['id']))->count();
            $lists[$key]['sellnum'] = $sellnum;
        }
        $list = $this->multi_array_sort($lists,'sellnum',SORT_DESC);
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
     }
     // 二维数组排序
     protected function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
        if(is_array($multi_array)){ 
            foreach ($multi_array as $row_array){ 
                if(is_array($row_array)){ 
                    $key_array[] = $row_array[$sort_key]; 
                }else{ 
                    return false; 
                } 
            } 
        }else{ 
            return false; 
        } 
        array_multisort($key_array,$sort,$multi_array); 
        return $multi_array; 
    }
    // 订单来源
    public function from(){
        $token = $this->_get('token');
        $cartNewModel = M('product_cart_new');
        $where = array(
                'token' => $token,
                'paid' => 1
            );
        $data = $this->Pages($cartNewModel,$where);
        $this->assign('info',$data['info']);
        // 分页链接
        $this->assign('page',$data['show']);
        $this->display();
    }
    
    
    /*活动营销中心*/
    public function marketing(){
        /*活动列表*/
        $marketingactivitymodel = M('Media_marketingactivity');
        $count = $marketingactivitymodel->where(array('token'=>$this->token))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $activity = $marketingactivitymodel->where(array('token'=>$this->token))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('activity',$activity);
        $this->assign('page',$show);
        $this->display();
    }

    public function addmarketing(){
        /*添加活动*/
        $marketingactivitymodel = M('Media_marketingactivity');
        if(IS_AJAX){
            $variable['id'] = !empty($_POST['id'])?$_POST['id']:'';
            $variable['title'] = $_POST['title'];
            $variable['pic'] = $_POST['pic'];
            $variable['starttime'] = $_POST['starttime'];
            $variable['endtime'] = $_POST['endtime'];
            $variable['abstract'] = $_POST['abstract'];
            $variable['content'] = $_POST['content'];
            $variable['number'] = $_POST['number'];
            $variable['tel'] = $_POST['tel'];
            $variable['token'] = $this->token;
            if($variable['id']){
                $saveactivity = $marketingactivitymodel->where(array('token'=>$this->token,'id'=>$variable['id']))->save($variable);
                if($saveactivity){
                    $this->success('编辑成功！',U(MODULE_NAME.'/marketing',array('token'=>session('token'))));
                }else{
                    $this->error('编辑失败！',U(MODULE_NAME.'/addmarketing',array('token'=>session('token'),'mid'=>$variable['id'])));
                }
            }else{
                $variable['addtime'] = time();
                $addactivitys = $marketingactivitymodel->data($variable)->add();
                if($addactivitys){
                    $this->success('操作成功！',U(MODULE_NAME.'/marketing',array('token'=>session('token'))));
                }else{
                    $this->error('操作失败！',U(MODULE_NAME.'/addmarketing',array('token'=>session('token'),'mid'=>$variable['id'])));
                }
            }
        }else{
            $id = $_GET['mid'];
            if($id){
                $activity = $marketingactivitymodel->where(array('id'=>$id,'token'=>$this->token))->find();
                $this->assign('activity',$activity);
                $this->assign('id',$id);
                $this->display();
            }else{
                $this->display();
            }
        }
    }

    public function delmarketing(){
        /*删除活动*/
        $marketingactivitymodel = M('Media_marketingactivity');
        $delactivity = $marketingactivitymodel->where(array('token'=>$this->token,'id'=>$_GET['mid']))->delete();
        if($delactivity){
            $this->success('删除成功！',U(MODULE_NAME.'/marketing',array('token'=>session('token'))));
        }else{
            $this->error('删除失败！',U(MODULE_NAME.'/marketing',array('token'=>session('token'))));
        }
    }

    public function marketingactor(){
        /*每个活动的参与者*/
        $marketingactormodel = M('Media_marketingactor');
        $count = $marketingactormodel->where(array('token'=>$this->token,'mid'=>$_GET['mid']))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $actor = $marketingactormodel->where(array('token'=>$this->token,'mid'=>$_GET['mid']))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($actor as $key=>$valure){
            $nuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $nusers = M('Wxusers')->where(array('uid'=>$nuser['id'],'openid'=>$valure['openid']))->find();
            $actor[$key]['nickname'] = $nusers['nickname'];
        }
        $this->assign('actor',$actor);
        $this->assign('page',$show);
        $this->display();
    }

    public function delmarketingactor(){
        /*删除活动*/
        $marketingactormodel = M('Media_marketingactor');
        $delactivity = $marketingactormodel->where(array('token'=>$this->token,'id'=>$_GET['aid'],'mid'=>$_GET['mid']))->delete();
        if($delactivity){
            $this->success('删除成功！',U(MODULE_NAME.'/marketingactor',array('token'=>session('token'),'mid'=>$_GET['mid'])));
        }else{
            $this->error('删除失败！',U(MODULE_NAME.'/marketingactor',array('token'=>session('token'),'mid'=>$_GET['mid'])));
        }
    }

    /*广告位图片*/
    public function guanggao(){
            /*'app'=>'PhotoWall',
             'type'=>'img1',*/
        $oImgModel = M('Imag');
        $this->assign(array(
            'taskpic'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Media','type'=>'taskpic'))->find(),
            'tuanpic'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Media','type'=>'tuanpic'))->find(),
            'homepic'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Media','type'=>'homepic'))->find(),
        ));

        $this->display();
    }
    /*
     * 提现管理
     * */
    public function tixianinfo(){
        $otiModel = M('Media_withdrawals');
        $oUserModel = M('Media_users');
        if($_GET['token']){
            $where = array(
                'token'=>$this->token,
                'openid'=>$_GET['openid']
            );
        }else{
            $where = array(
                'token'=>$this->token
            );
        }

        $count = $otiModel->where($where)->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $alist = $otiModel->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($alist as $k=>$val){
            $aUrser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $aUrsers = M('Wxusers')->where(array('uid'=>$aUrser['id'],'openid'=>$alist['openid']))->find();
            $aUser = $oUserModel->where(array('token'=>$this->token,'openid'=>$val['openid']))->find();
            if($aUser['nickname']){
                $alist[$k]['nickname'] = $aUser['nickname'];
            }else{
                $alist[$k]['nickname'] = $aUrsers['nickname'];
            }
        }
        $this->assign(array(
            'alist'=>$alist,
            'page'=>$show
        ));
        $this->display();
    }

    public function ticheck(){
        $otiModel = M('Media_withdrawals');
        $oUserModel = M('Media_users');
        $iTem = $otiModel->where(array('id'=>$_GET['id']))->find();
        //print_r($iTem);exit;
        $aUser = $oUserModel->where(array('token'=>$this->token,'openid'=>$iTem['openid']))->find();

        if(IS_AJAX){
            $iTems =$otiModel->where(array('id'=>$_POST['id']))->find();

            if($iTems == false){
                $this->error2('非法操作');
            }
            $_POST['check_time'] = date('Y-m-d H:i:s');

            if($otiModel->where(array('id'=>$_POST['id']))->save($_POST)){
                if($_POST['status'] ==4){
		            $number =  (int) $iTems['number'];
                    $oUserModel->where(array('id'=>$aUser['id']))->setInc('yongjin',$number);
                }
                $this->success('操作成功！',U(MODULE_NAME.'/tixianinfo',array('token'=>session('token'))));
            }else{
                $this->error('操作失败！',U(MODULE_NAME.'/ticheck',array('token'=>session('token'),'id'=>$_POST['id'])));
            }
        }
        /*print_r($iTem);
        echo '<br/>';
        print_r($aUser);*/
        $this->assign(array(
            'item'=>$iTem,
            'users'=>$aUser,
            'ExtraBtn' => array(
                array(
                    'url'  => U('Media/tixianinfo', array('token' => $this->token)),
                    'name' => '返回'
                )
            )
        ));

        $this->display();
    }

    /*企业账号管理*/
    public function enterprise(){
        $oEnterprise = M('Media_enterprise');
        $count = $oEnterprise->where(array('token'=>$this->token))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $list = $oEnterprise->where(array('token'=>$this->token))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();

    }
    public function setenter(){

        $oEnterprise = M('Media_enterprise');

        if(IS_POST){
            $eid = $_REQUEST['id'];
            $itme = $oEnterprise->where(array('id'=>$eid))->find();
            if($itme) {
                $_POST['password'] = md5($_POST['password']);
                $setsave = $oEnterprise->where(array('id' => $eid))->save($_POST);
                if ($setsave) {
                    $this->success('操作成功！',U(MODULE_NAME.'/enterprise',array('token'=>session('token'))));
                }else{
                    $this->error('操作失败！',U(MODULE_NAME.'/enterprise',array('token'=>session('token'))));
                }

            }else{
                $_POST['password'] = md5($_POST['password']);
                $_POST['token'] = $_REQUEST['token'];
                $_POST['addtime'] = date('Y-m-d H:i:s');
                $setadd = $oEnterprise->data($_POST)->add();
                if($setadd){
                    $this->success('操作成功！',U(MODULE_NAME.'/enterprise',array('token'=>session('token'))));
                }else{
                    $this->error('操作失败！',U(MODULE_NAME.'/enterprise',array('token'=>session('token'))));
                }
            }
        }else{
            $id = $_REQUEST['id'];
            $aerter = $oEnterprise->where(array('id'=>$id))->find();
            $this->assign('info',$aerter);
        }
       $this->assign(array(
            'ExtraBtn' => array(
                array(
                    'url'  => U('Media/enterprise',array('token' => $this->token)),
                    'name' => '返回'
                )
            ),
        ));
        $this->display('setenter');
    }

    public function delenter(){
        $oEnterprise = M('Media_enterprise');
        if($oEnterprise->where(array('id'=>$_REQUEST['id']))->find()){
            if($oEnterprise->where(array('id'=>$_REQUEST['id']))->delete()){
                $this->success('操作成功！',U(MODULE_NAME.'/enterprise',array('token'=>session('token'))));
            }else{
                $this->error('操作失败！',U(MODULE_NAME.'/enterprise',array('token'=>session('token'))));
            }
        }else{
            $this->error('非法操作');
        }
    }

    /*企业积分充值*/
    public function activescore(){
        $oenterModel = M("Media_enterprise");
        $oscoreModel = M("Media_enterprise_score");
        if(IS_AJAX){
            $id = $_POST['pid'];
            $iTem = $oenterModel->where(array('id'=>$id))->find();
            if(!$iTem) $this->error2('非法操作');
            $type = $_POST['type'];
            $_POST['token'] = $this->token;
            $_POST['add_time'] = date('Y-m-d H:i:s');
            if($type == 0){
                $iTure = $oenterModel->where(array('id'=>$id))->setDec('score',$_POST['score']);
            }elseif($type == 1){
                $iTure = $oenterModel->where(array('id'=>$id))->setInc('score',$_POST['score']);
            }
            if($iTure){
                if($oscoreModel->add($_POST)){
                    $this->success('操作成功！',U(MODULE_NAME.'/enterprise',array('token'=>session('token'))));
                }else{
                    $this->error('操作失败！',U(MODULE_NAME.'/activescore',array('token'=>session('token'))));
                }
            }else{
                $this->error('操作失败！',U(MODULE_NAME.'/activescore',array('token'=>session('token'))));
            }

        }
        $this->display('activescore');
    }

    /*企业积分流水记录*/
    public function scoreinfo(){
        $oEnterprise = M('Media_enterprise_score');
        $count = $oEnterprise->where(array('token'=>$this->token,'pid'=>$_GET['id']))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $list = $oEnterprise->where(array('token'=>$this->token,'pid'=>$_GET['id']))->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $k=>$val){
            switch($val['type']){
                case 0: $list[$k]['type'] = '减少'; break;
                case 1: $list[$k]['type'] = '增加'; break;
                default: $list[$k]['type'] = '其它';
            }
            if($val['openid']){
                $user = M('Wxuser')->where(array('token'=>$this->_sToken))->find();
                $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$val['openid']))->find();
                $list[$k]['openid'] = $users['nickname'];
            }else{
                $list[$k]['openid'] = '总部';
            }
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }

    /*个人积分详情记录*/
    public function userscore(){
        $oEnterprise = M('Media_user_score');
        $count = $oEnterprise->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $list = $oEnterprise->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $k=>$val){
            $task = M('Media_task')->where(array('id'=>$val['cid']))->find();
            $list[$k]['cid'] = $task['title'];
            switch($val['type']){
                case 1:$list[$k]['type'] = '转发';break;
                case 2:$list[$k]['type'] = '阅读';break;
                case 3:$list[$k]['type'] = '分享阅读';break;
                case 4:$list[$k]['type'] = '积分兑换';break;
                case 5:$list[$k]['type'] = '第一次进入平台';break;
                default:$list[$k]['type'] = '其他';
            }
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }



}
