<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/3
 * Time: 16:31
 * Title:CFO
 */
class CourseAction extends BaseAction{
    public function  _initialize(){
        parent::_initialize();
        /*
          * 引入微信js接口
          * 
          */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();

        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);
    }

    /*课程列表页*/
    public function index(){
        $coursemodel = M('Context_list');
        $oUserModel = M('Course_user');
        $iUser = $oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        if(!$iUser){
            $varalber = array(
                'token'=>$this->token,
                'openid'=>$this->openid,
                'add_time'=>time(),
                'date'=>date('Y-m-d')
            );
            $oUserModel->add($varalber);
        }
        $list = $coursemodel->where(array('token'=>$this->token))->order('addtime desc')->select();
        $this->assign('list',$list);
        $this->display();
    }
    /*课程详情页*/
    public function content(){
        /*
          * 引入微信js接口
          */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();

        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);

        $cid = $_GET['cid'];
        $coursemodel = M('Context_list');
        $coursesmodel = M('Context_shop');
        $list = $coursemodel->where(array('token'=>$this->token,'id'=>$cid))->find();
        $list['context'] = htmlspecialchars_decode($list['context'],ENT_QUOTES);
        $this->assign('list',$list);
        $count = $coursesmodel->where(array('token'=>$this->token,'cid'=>$cid,'pay_status'=>1))->count();
        $counts = $list['number'] - $count;
        $courses = $coursesmodel->where(array('token'=>$this->token,'cid'=>$cid,'pay_status'=>1))->order('time desc')->select();
        $this->assign(
            array(
                'counts'=> $counts,
                'courses'=>$courses,
                'day' =>date('Y-m-d H:i:s')
            ));
        $this->display();
    }
    /*报名参加页*/
    public function set(){
        $cid = $_GET['cid'];
        $coursemodel = M('Context_list');
        $coursesmodel = M('Context_shop');
        $list = $coursemodel->where(array('token'=>$this->token,'id'=>$_GET['cid']))->find();
    
        $aiTem = explode('|',$list['money']);
        //组合价格 张湘南
        $type=explode(',',$list['type']);
        $money=explode(',',$list['money']);
      // $type=array_merge($type,$money);
      $type_money=array_combine($type,$money);
      // P($type_money);
        $record = $coursesmodel
            ->where(array('token'=>$this->token,'cid'=>$cid))
            ->order('id desc')
            ->find();
        $this->assign('hisrecord215445564445544554454545455445', $record);

        if(IS_AJAX){
	    $orderID = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $from_openid = !empty($_POST['from_openid'])?$_POST['from_openid']:'';
            $_POST['time'] = time();
            $_POST['pay_status'] = 0;
            $_POST['cid'] =$cid;
            $_POST['orderID'] = $orderID;
            $_POST['openid']=$_GET['openid'];
            $_POST['token']=$this->token;
            $a=explode('-', $_POST['money']);
            $_POST['type2']=$a['0'];
            $_POST['money']=$a['1'];
            $_POST['hm'] = $_POST['hm'].",";
	        $count = $coursesmodel->where(array('token'=>$this->token,'cid'=>$cid,'pay_status'=>1))->count();
	       
            if($count < $list['number']){
            	
                if($list['money'] == 0){
                    $iTem = $coursesmodel->where(array('token'=>$this->token,'cid'=>$cid,'pay_status'=>1,'openid'=>$this->openid))->find();
                  
                    if($iTem){
                        echo $this->encode(array('code'=>3,'msg'=>'此课程您已经报过名了！','url'=>U(MODULE_NAME.'/index',array('token'=>$this->token,'openid'=>$this->openid))));exit;
                    }else{
                        $_POST['pay_status'] = 1;
                        $course = $coursesmodel->data($_POST)->add();
                    }
                }else{
                	
                	$data=M('Context_shop')->where(array('openid'=>$_GET['openid'],'cid'=>$_POST['cid'],'type2'=>$_POST['type2']))->find();
                    if($data){ echo $this->encode(array('code'=>3,'msg'=>'此课程您已经报过名了！','url'=>U(MODULE_NAME.'/index',array('token'=>$this->token,'openid'=>$this->openid))));exit;}
                	$course = $coursesmodel->data($_POST)->add();
                }
                if($course){

                    if($_POST['money'] == 0 ){
                        if($from_openid ==''){
                        	//print_r($list['score']);
                            $iSucces = M('Course_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->setInc('score',$list['score']);
                            if($iSucces){
                                echo $this->encode(array('code'=>0,'msg'=>'报名成功！','url'=>U(MODULE_NAME.'/share',array('token'=>$this->token,'cid'=>$cid,'openid'=>$this->openid)),'type'=>1));exit;
                            }else{
                            	
                                echo $this->error('获取积分失败');
                            }
                        }else{
                            if(M('Course_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->setInc('score',$list['score'])){
                                if(M('Course_user')->where(array('token'=>$this->token,'openid'=>$from_openid))->setInc('score',$list['scoress'])){
                                    echo $this->encode(array('code'=>0,'msg'=>'报名成功！','url'=>U(MODULE_NAME.'/share',array('token'=>$this->token,'cid'=>$cid,'openid'=>$this->openid)),'type'=>1));exit;
                                }else{
                                	
                                    echo $this->error('获取积分失败');
                                }
                            }else{
                            	
                                echo $this->error('获取积分失败');
                            }
                        }
                    }else{
                        echo $this->encode(array('code'=>0,'msg'=>'报名成功去支付吧!','orderid'=>$orderID));exit;
                    }
                }else{
                    $this->error('报名失败！',U(MODULE_NAME.'/set',array('token'=>$this->token,'cid'=>$cid,'openid'=>$this->openid)));exit;
                }
            }else{
                echo $this->encode(array('code'=>2,'msg'=>'报名人数已满！请联系'.$list['tel'],'url'=>'index.php?g=Wap&m=Course&a=index&token='.$this->token.'&openid='.$this->openid));exit;
            }

        }else{
            $nuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $nusers = M('Wxusers')->where(array('uid'=>$nuser['id'],'openid'=>$this->openid))->find();
            $this->assign(array(
                'list'=>$list,
                'nusers'=>$nusers,
                'aiTem'=>$aiTem,
            	'type_money'=>$type_money
            ));
        }
        //录入成功后下次不用录入 张湘南
        $list=M('Context_shop')->where(array('openid'=>$_GET['openid']))->order("id desc")->limit(0,1)->select();
        if($list){ $this->assign('hisrecord',$list[0]);   }
        
         //选择该项目需镇的资料 子段存在就显示                
         $zl= M('Context_list')->where(array('id'=>$_GET['cid']))->getField('zl');
        
         $zl=explode(',',$zl);
         //P($zl);
         $this->assign('zl',$zl);
         
/*          if(in_array('na1me',$zl)){
         	echo 1;
         }; */
        $this->display();
    }

    /*分享跳转页*/
    public function share(){
        $oCourseModel = M('Context_list');
        $this->assign(array(
           'info'=>$oCourseModel->where(array(
               'token'=>$this->token,
               'id'=>$_GET['cid']
           )) ->find()
        ));
        $this->display();
    }



    /*我的资料页*/
    public function myhome(){
        $oUserModel = M('Course_user');
        $aUser = $oUserModel->where(array(
            'token'=>$this->token,
            'openid'=>$this->openid
        ))->find();
        $this->assign('users',$aUser);
        $this->display();
    }

    /*我的小伙伴页*/
    public function mybrother(){
        $oImgModey = M('Img_zhuanfa');
        $oWxUserModel = M('Wxuser');
        $oWxUsersModel = M('Wxusers');
        $aUser = $oImgModey->where(array(
            'token'=>$this->token,
            'from_openid'=>$this->openid
        ))->order('add_time desc')
            ->group('date')->select();
        foreach($aUser as $iKey => $aValure){
            $aborther = $oImgModey->where(array(
                'token'=>$this->token,
                'from_openid'=>$this->openid,
                'date'=>$aValure['date']
            ))->order('add_time desc')->select();
            foreach($aborther as $iK =>$aVar){
                $aUsers = $oWxUserModel->where(array(
                    'token'=>$this->token
                ))->find();
                $aUserser = $oWxUsersModel->where(array(
                    'uid'=>$aUsers['id'],
                    'openid'=>$aVar['openid']
                ))->find();
                $aborther[$iK]['nickname'] = $aUserser['nickname'];
                $aborther[$iK]['headimgurl'] = $aUserser['headimgurl'];
            }
            $aUser[$iKey]['aborther'] = $aborther;
        }
        $this->assign('user',$aUser);
        $this->display();
    }

    /*转发的列表页*/
    public function zhuanlist(){
        $oImgModel = M('Img');
        $aZhuanfa = $oImgModel->where(array(
            'token'=>$this->token,
            'is_zf'=>1
        ))->select();
        $this->assign(array(
            'zhuanfa'=>$aZhuanfa
        ));
        $this->display();
    }
    //分享成功
    public function sharesuct(){
        $iLid = $_GET['lid'];
        $oModel = M('Img');
        $aWen = $oModel->where(array('id'=>$iLid))->find();
        $this->assign(array(
            'info'=>$aWen
        ));
        $this->display();
    }

    public function score(){
        $token	= $this->_get('token');
        $openid	= $this->_get('openid');
        $model = M('Integralshop');
        $list = $model->where(array('tp_integralshop.token'=>$token))->field('tp_integralshop.*,l.name')->join('left join
tp_usercenter_level as l on tp_integralshop.extent = l.id ')->select();
        //print_r($list);exit;
        $oModel = M('Usercenter_memberlist');
        $aUser = M('Wxuser')->where(array(
            'token'=>$this->token
        ))->find();
        $uid = $aUser['id'];
        $aArr = $oModel->where(array(
            'uid'=>$uid,
            'openid'=>$this->openid
        ))->find();
        $this->assign(array(
            'data'=>$list,
            'aUser'=>$aArr
        ));
        $this->display();
    }

    public function exchange(){

        // echo time();exit;
        $model =M('Course_user');
        $oModel = M('Usercenter_memberlist');
        $where['openid'] = $_GET['openid'];
        $where['token'] = $this->token;
        $aUser = M('Wxuser')->where(array(
            'token'=>$this->token
        ))->find();
        $uid = $aUser['id'];
        $arr = $model->where($where)->find();
        $aArr = $oModel->where(array(
            'uid'=>$uid,
            'openid'=>$this->openid
        ))->find();
        if(!$aArr){
            $this->error("请先填写联系资料！",U('Wap/Usercenter/joinusercenter',array('token'=>$this->token,'openid'=>$this->_get('openid'))));exit;
        }else{
            $list = $model->where($where)->find();//getField('score');
             //print_r($list);exit;
            $croe = $_POST['point'];
            //print_r($croe);exit;
            if($list['score'] < $croe){
                $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/jifenbuzuo',array('token'=>$_GET['token'],'openid'=>$this->_get('openid'))));exit;
            }else{
                $conn = M('Integralshop');
                $where_1['id']=$_POST['exc_id'];
                $a = $conn->where($where)->getField('degree');//在礼品积分表里查找礼品可兑换的次数
                $iLid = $_POST['exc_id'];
                $result = M('Usercenter_score_record');
                $gift = M('Integralshop_individual');
                $term_1 = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'lid'=>$_POST['exc_id']);
                $term_2 = array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'lid'=>$_POST['exc_id'],'time'=>time());
                $term = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>8,'score'=>-$croe,'add_time'=>time(),'titleid'=>$_POST['exc_id']);
                $count = $gift->where($term_1)->count('lid');
                // print_r($count);exit;
                if($a <= $count){
                    $this->error("对不起，您兑换此活动的机会已经用完！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }else{
                    //总积分 = 兑换前积分 - 兑换礼品的积分
                    $data['score'] = $list['score'] - $croe;
                    $arr = $model->where($where)->save($data);//修改个人中心的会员积分
                    $m = $result->data($term)->add();//添加之‘Usercenter_score_record’的积分时间记录
                    $l = $gift->data($term_2)->add(); //
                    if($arr && $m && $l ){
                        $this->success("扣除成功！",U(MODULE_NAME.'/duisushare',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'),'lid'=>$iLid)));
                    }
                }

            }
        }
    }
    public function reveal(){
        $model =  M('Integralshop_individual');
        $where = array('tp_integralshop_individual.openid'=>$_GET['openid']);
        $list = $model->where($where)->field('tp_integralshop_individual.*,l.title,l.integral')->join('left join tp_integralshop as l
on tp_integralshop_individual.lid = l.id ')->select();
        $this->assign('list',$list);
        $this->display();
    }

    /*兑换成功，立即分享*/
    public function duisushare(){
        $oIntegralModel = M('Integralshop');
        $aInfo = $oIntegralModel->where(array(
            'token'=>$this->token,
            'id'=>$_GET['lid']
        ))->find();
        $this->assign('info',$aInfo);
        $this->display();
    }




}
