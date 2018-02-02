<?php
/**
 * Created by IntelliJ IDEA.
 * User: 李铭
 * Date: 14-8-30
 * Time: 上午11:48
 * To change this template use File | Settings | File Templates.
 */
class UsercenterzjAction extends BaseAction{

    public $uid = null;
    public $user_qun_id = null;
    public $is_member = null;
    public $usercenterdata = null;

    public function _initialize(){
        parent::_initialize();
        $usersModel = M('Wxusers');
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $userdata = $usersModel->where(array('uid'=>$wxuser['id'],'status'=>1,'openid'=>$this->openid))->find();

        $usercenterdata = M('Usercenter_memberlist')->where(array('uid'=>$wxuser['id'],'openid'=>$this->openid))->find();


        $userscore = M('Usercenter_score_record')->field('sum(score) as allscore')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('gt',0),'type'=>3))->select();
        if($usercenterdata){
            $this->is_member = true;
            $where['token']=$this->token;
            $where['score']=array('elt',$userscore[0]['allscore']);//小于等于
            $userlevel_data = M("Usercenter_level")->where($where)->order('score desc')->limit(1)->find();
        }
        if($usercenterdata['is_bind_st'] == 1){
            $shitidata = M('Usercenter_shitimember')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
            $this->assign('shitidata',$shitidata);
        }
        $this->uid = $wxuser['id'];
        $this->assign('wxuser',$wxuser);
        $this->assign('userdata',$userdata);//会员个人资料表1

        $this->assign('usercenterdata',$usercenterdata);//会员个人资料详细表
        $this->usercenterdata = $usercenterdata;
        if($userlevel_data){
            $this->user_qun_id = $userlevel_data['id'];
            $this->assign('userlevelname',$userlevel_data['name']);
        }else{
            $this->assign('userlevelname','普通会员');
        }

        $this->assignPrivate();
    }


    function assignPrivate(){
        switch($this->token){
            case 'b873d389377626a99006d613c85fb3a6'://益笙行
                $phone = '020-31137759';
                break;
            case '233eb0b413256696f977e4c963645877'://银波米业
                $phone = '0755-28535698';
                break;
            case '6278b84cdaa4f05c9bff030d6338d270'://关爱天使
                $phone = '4000055238';
                break;
            case '1d63eefb5dcb19cf48fff098c99781ce'://环球旅游
                $phone = '4006662433';
                break;
            case '079029b2a38dbde9963ab9f4cd5fe721'://大众招聘网
                $phone = '4006684428';
                break;
            default:
                $phone = '40088-59059';
                break;
        }
        $this->assign('phone', $phone);
    }


    public function activitys(){
        $lotteryModel = M('Lottery');
        $data = $lotteryModel->where(array('token'=>$this->token))->order('statdate desc')->select();
        foreach($data as $k=>$v){
            if($v['statdate'] > time()){
                $data[$k]['is_start'] = 1;
            }else if($v['enddate'] < time()){
                $data[$k]['is_start'] = 2;
            }else{
                $data[$k]['is_start'] = 3;
            }
        }
        $this->assign('data',$data);
        $this->display();
    }

    public function index(){
        if(!M('Usercenter_memberlist')->where(array('token'=>$_GET['token'],'openid'=>$_GET['openid']))->find()){
            $this->assign('status',1);//设定一个状态
            $this->display("tpl/Wap/default/Usercenterzj_joinusercenter2.html");die;
        }
        $where = array();
        $lwhere = array();
        $lwhere['token']=$this->token;
        $lwhere['statdate']=array('lt',time());
        $lwhere['enddate']=array('gt',time());
        $lotteryModel = M('Lottery');
        $lotterycounts = $lotteryModel->where($lwhere)->count();
        $this->assign('lotterycounts',$lotterycounts);
        $usercenter_signmodel = M('Usercenter_sign');
        $userissign = $usercenter_signmodel->where(array('token'=>$this->token,'openid'=>$this->openid,'sign_date'=>date("Y-m-d",time())))->find();
        $this->assign('userissign',$userissign);

        $noticestartdate = strtotime(date("Y-m-d",time())." 00:00:00");
        $noticeenddate = strtotime(date("Y-m-d",time()+3600*24*10)." 00:00:00");
        $notices = M('Usercenter_notice')->where(array('token'=>$this->token,'add_time'=>array('between',array($noticestartdate,$noticeenddate))))->count();
        $this->assign('notices',$notices);
        if($this->is_member){
            if($this->user_qun_id){
                $where['user_qun_id'] = array('in',array($this->user_qun_id,0));
            }else{
                $where['user_qun_id'] = 0;
            }
            $where['token'] = $this->token;
            $where['start_date'] = array('lt',date("Y-m-d H:i:s"));
            $where['end_date'] = array('gt',date("Y-m-d H:i:s"));
            $salecardcount = M('Usercenter_salecard')->where($where)->count();

            $this->assign('salecardcount',$salecardcount);
        }else{
            $this->assign('salecardcount',0);
        }
        //得我的佣金
        $homenice_commission=M('Media_users');
        $attribution=$homenice_commission->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('yongjin');
        $this->assign('attribution',$attribution);
        //得我的客户
        $set=M('Product_setting_new')->field('one,two,three')->where(array('token'=>$this->token))->find();
        if($set['one']>0){
            $list=M('Media_users')
                ->field('nickname,openid,add_time')
                ->where(array(
                    'from_openid'=>$this->openid,
                    'token'=>$this->token,
                    'status'=>1,
                    'is_buy' => 1
                ))->select();
        }
        if($set['one']>0&&$set['two']>0){
            foreach($list as $k=>$v){
                $list1=M('Media_users')
                    ->field('nickname,openid,add_time')
                    ->where(array(
                        'from_openid'=>$v['openid'],
                        'token'=>$this->token,
                        'status'=>1,
                        'is_buy' => 1
                    ))->select();
                foreach($list1 as $v){
                    array_push($list,$v);
                }
                if($set['one']>0&&$set['two']>0&&$set['three']>0){
                    foreach($list1 as $k=>$v){
                        $list2=M('Media_users')
                            ->field('nickname,openid,add_time')
                            ->where(array(
                                'from_openid'=>$v['openid'],
                                'token'=>$this->token,
                                'status'=>1,
                                'is_buy' => 1
                            ))->select();
                        foreach($list2 as $v){
                            array_push($list,$v);
                        }
                    }
                }
            }
        }
        $count=count($list);
        $this->assign('count',$count);
        //得我的订单
        $dNum=M('Product_cart_new')->where(array('token'=>$this->token,'wecha_id'=>$this->openid))->count();
        $this->assign('dNum',$dNum);
        $this->display();

    }

    /*
     * 我的优惠卷
     */
    public function salecard(){
        if($_GET['status']){//已使用过的
            $list=M('Sn')->where(array('token'=>$this->token,'openid'=>$_GET['openid'],'status'=>$_GET['status']))->select();
            $this->assign('k',1);
        }else{
            $list=M('Sn')->where(array('token'=>$this->token,'openid'=>$_GET['openid'],'status'=>0))->select();
        }

        $this->assign('salecarddata',$list);

        $this->display();
    }





    /*
     * 优惠券详细
     */

    public function salecarddetail(){
        $where = array();

        if($_GET['id']){
            $where['id'] = $_GET['id'];
            $list=M('Snname')->where($where)->find();

           // $usersalecard = M('Usercenter_user_salecard')->where(array('token'=>$this->token,'openid'=>$this->openid,'sale_id'=>$where['id']))->find();
            $this->assign('saledata',$list);
            //$this->assign('usersalecard',$usersalecard);
            $this->display();
        }
    }



    public function create_password($pw_length = 8){
        $chars = '0123456789';

        $password = '';
        for ( $i = 0; $i < $pw_length; $i++ )
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }

        return $password;
    }

    /*
     * 商家
     */
    public function shangjia(){
        $shangdata = M('Usercenter_set')->where(array('token'=>$this->token))->find();
        $this->assign('shangdata',$shangdata);
        $this->display();
    }


    /*
     * 我的帐户
     */
    public function joinusercenter(){
        $memberlistModel = M('Usercenter_memberlist');
        $memberdata = $memberlistModel->where(array('uid'=>$this->uid,'openid'=>$this->openid))->find();
        $usercenter_model = M('Usercenter_set');
        $usercenterdata = $usercenter_model->field('is_openphone,u_prefix')->where(array('token'=>$this->token))->find();
        if(IS_POST){
            $memberdataphone = $memberlistModel->where(array('uid'=>$this->uid,'phone'=>$_POST['phone']))->find();
            $openidmember = $memberlistModel->where(array('uid'=>$this->uid,'openid'=>$this->openid,'phone'=>$_POST['phone']))->find();
            if($memberdataphone){
                if(!$openidmember) {
                    echo $this->encode(array('code' => -3, 'msg' => '对不起手机号码已存在'));
                    exit;
                }
            }

            $data =array();
            $data['uid']= $this->uid;
            $data['openid']= $this->openid;
            if($_POST['is_bind']){
                $data['is_bind']= 1;
            }else{
                $data['is_bind']= 0;
            }
            $data['name']= $_POST['name'];
            $data['phone']= $_POST['phone'];
            $data['birth_day']= $_POST['birth_year'].'-'.$_POST['birth_month'].'-'.$_POST['birth_date'];
            $data['address']= $_POST['addr_prov'].'|'. $_POST['addr_city'].'|'.$_POST['addr_area'].'|'.$_POST['address'];
            $data['update_time'] = time();
            if($memberdata == null){
                $data['score'] = 0;
                $data['money'] = 0;
                $prefix = 'WP';
                if($usercenterdata){
                    $prefix = $usercenterdata['u_prefix'];
                }
                $data['member_sn'] = $prefix.$this->uid.date("Ymd",time()).rand(100,999);
                if($memberlistModel->add($data)){
                    echo $this->encode(array('code'=>0,'msg'=>'保存成功','url'=>'/index.php?g=Wap&m=Usercenterzj&a=index&token='.$this->token."&openid=".$this->openid));exit;
                }else{
                    echo $this->encode(array('code'=>-1,'msg'=>'保存失败请重试'));exit;
                }
            }else{
                $udata = array();
                if($_POST['is_bind']){
                    $udata['is_bind']= 1;
                }else{
                    $udata['is_bind']= 0;
                }
                $udata['name']= $_POST['name'];
                $udata['phone']= $_POST['phone'];
                $udata['birth_day']= $_POST['birth_year'].'-'.$_POST['birth_month'].'-'.$_POST['birth_date'];
                $udata['address']= $_POST['addr_prov'].'|'. $_POST['addr_city'].'|'.$_POST['addr_area'].'|'.$_POST['address'];
                $udata['update_time'] = time();
                if($memberlistModel->where(array('openid'=>$this->openid,'uid'=>$this->uid))->data($udata)->save()){
                    echo $this->encode(array('code'=>0,'msg'=>'保存成功','url'=>'/index.php?g=Wap&m=Usercenterzj&a=joinusercenter&token='.$this->token."&openid=".$this->openid));exit;
                }else{
                    echo $this->encode(array('code'=>-2,'msg'=>'保存失败请重试'));exit;
                }
            }
        }else{

            $this->assign('usercenterdata',$usercenterdata);
            $this->assign('memberdata',$memberdata);
            $this->assign('birth_day',explode('-',$memberdata['birth_day']));
            $this->assign('address',explode('|',$memberdata['address']));
            $a=$this->_get('type');
            if($a==1){//如果为真就代表，第二次过来的是修改个人资料页面
                $this->display("tpl/Wap/default/Usercenterzj_joinusercenter2.html");
            }else{
                $this->display();
            }

        }
    }

    /**
     * 积分明细
     */
    public function jifen(){
        $memberlistModel = M('Usercenter_memberlist');
        $aScore= $memberlistModel->where(array('uid'=>$this->uid,'openid'=>$this->openid))->getField('score');
        $mUsercenter_score_record=M('Usercenter_score_record');
        $aList=$mUsercenter_score_record->where(array('uid'=>$this->uid,'openid'=>$this->openid))->order('add_time desc')->select();
        $this->assign('score',$aScore);
        $this->assign('list',$aList);
        $this->display();
    }







    /**
     * 充值明细
     */
    public function moneyrecord(){
        $usercenter_moneyModel = M('Usercenter_money_record');
        $moneyrecordlist = $usercenter_moneyModel->where(array('token'=>$this->token,'openid'=>$this->openid,'status'=>1))->order('add_time desc')->select();
        $this->assign('moneyrecordlist',$moneyrecordlist);
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $usercenterdata = M('Usercenter_memberlist')->where(array('uid'=>$wxuser['id'],'openid'=>$this->openid))->find();
        $this->assign('on',1);
        $this->display();
    }
    /**
     * 消费明细
     */
    public function xiaofe(){
        $product_cart_new=M('Product_cart_new');
        $list=$product_cart_new->field('time,price')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('time desc')->select();
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * 充值钱
     */
    public function genMoney(){
        if(IS_AJAX) {
            $usercenter_money_recordModel = M('Usercenter_money_record');
            if (!empty($_POST['money'])) {
                $data['token'] = $this->token;
                $data['openid'] = $this->openid;
                $data['pay_type'] = 1;
                $data['status'] = 0;
                $data['add_time'] = time();
                $data['money'] = intval($_POST['money']);
                if ($lastid = $usercenter_money_recordModel->add($data)) {
                    echo $this->encode(array('code' => 0, 'msg' => '充值订单生成成功,正在跳转..', 'order_id' => $lastid));
                } else {
                    echo $this->encode(array('code' => -1, 'msg' => '订单生成失败,请重试'));
                }
            }
        }else{
            $this->display();
        }

    }
    /*获取用户的微信昵称及商城的里面的姓名*/
    public function username($openid){
        $user = M('Wxuser')->where(array('token'=>$this->token))->find();
        $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$openid))->find();
        $usernames['name'] = $users['nickname'];
        $pusers = M('Product_address')->where(array('uid'=>$openid))->find();
        $usernames['pname'] = $pusers['name'];
        return $usernames;
    }


    /**
     * 我的客户  attribution 佣金
     */
    public function kefu(){

        $set=M('Product_setting_new')->field('one,two,three')->where(array('token'=>$this->token))->find();

        $iOne = $iTwo = $iThree = 0;
        $aOne = $aTwo = $aThree = array();

        if($set['one']>0){
            $list=M('Media_users')
                ->field('nickname,openid,add_time')
                ->where(array(
                    'from_openid'=>$this->openid,
                    'token'     =>$this->token,
                    'status'    =>1,
                    'is_buy'    => 1
                ))->select();
            foreach ($list as $k=>$val) {
                $info = $this->username($val['openid']);
                $list[$k]['name'] = $info['name'];
                $list[$k]['pname'] = $info['pname'];
            }

            $iOne = count($list);
            $aOne = $list;
        }


        if($set['one']>0&&$set['two']>0){
        //    $list1=M('Media_users')->field("tp_media_users.nickname,tp_media_users.openid,a.nickname as nickname2,a.openid as openid2")->join("join tp_media_users as a on tp_media_users.openid=a.from_openid")->where(array('tp_media_users.from_openid'=>$this->openid))->select();
            $list1='';
            foreach($list as $k=>$v){
                $list1=M('Media_users')
                    ->field('nickname,openid,add_time')
                    ->where(array(
                        'from_openid'=>$v['openid'],
                        'token'=>$this->token,
                        'status'=>1,
                        'is_buy' => 1
                    ))->select();
                foreach($list1 as $v){
                    $info = $this->username($v['openid']);
                    $v['name'] = $info['name'];
                    $v['pname'] = $info['pname'];
                    $iTwo++;
                    $aTwo[] = $v;
                    array_push($list,$v);
                }
                if($set['one']>0&&$set['two']>0&&$set['three']>0){
                    //    $list1=M('Media_users')->field("tp_media_users.nickname,tp_media_users.openid,a.nickname as nickname2,a.openid as openid2")->join("join tp_media_users as a on tp_media_users.openid=a.from_openid")->where(array('tp_media_users.from_openid'=>$this->openid))->select();
                    $list2='';
                    foreach($list1 as $k=>$v){
                        $list2=M('Media_users')
                            ->field('nickname,openid,add_time')
                            ->where(array(
                                'from_openid'=>$v['openid'],
                                'token'=>$this->token,
                                'status'=>1,
                                'is_buy' => 1
                            ))->select();
                        foreach($list2 as $v){
                            $info = $this->username($v['openid']);
                            $v['name'] = $info['name'];
                            $v['pname'] = $info['pname'];
                            $iThree++;
                            $aThree[] = $v;
                            array_push($list,$v);
                        }

                    }
                }
            }
        }
       // $homenice_commission=M('Homenice_commission');
       // $list=$homenice_commission->where(array('token'=>$this->token,'ws_openid'=>$this->openid))->order('add_time desc')->getField('order_name,attribution,add_time');
       // $list=M('Homenice_user')->where(array('token'=>$this->token,'dopenid'=>$this->openid,'status'=>1))->select();
        $this->assign('list',$list);
        $this->assign('showDetail', $this->_assignPri());
        $this->assign('one', $iOne);
        $this->assign('two', $iTwo);
        $this->assign('three', $iThree);

        $this->assign('aone', $aOne);
        $this->assign('atwo', $aTwo);
        $this->assign('athree', $aThree);
        $info1=M('media_users')->field('from_openid,tuijian_time')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        if($info1['from_openid']){
            $info1['nickname']=M('Wxusers')->where(array('uid'=>$this->uid,'openid'=>$info1['from_openid']))->getField('nickname');
         //   p($info1);
            $this->assign('dd_nickname',$info1);
        }else{
            $this->assign('dd_nickname','');
        }

        $this->display();
    }

    public function _assignPri()
    {
        $bShowDetail = true;
        switch ($this->token) {
            case '6278b84cdaa4f05c9bff030d6338d270':
                $bShowDetail = false;
                break;
            case '5d8a87bab30de695954b17fc835b9d12':
                $bShowDetail = false;
                break;
            case '079029b2a38dbde9963ab9f4cd5fe721':
                $bShowDetail = false;
                break;
        }
        return $bShowDetail;
    }

    /**
     *  我的佣金
     */
    public function commission(){
        $homenice_commission=M('Edia_user_commission');
        $list=$homenice_commission
            ->where(array('token'=>$this->token,'openid'=>$this->openid))
            ->order('add_time desc')
            ->field('status,add_time,yj')
            ->select();
        $iTotalYJ = 0;//佣金总量
        foreach ($list as $l) {
            $iTotalYJ += $l['yj'];
        }
        $iUnTiXianYJ = 0;
        $iUnTiXianYJ = M('Media_users')->where(array(
            'token'=>$this->token,
            'openid'=>$this->openid
        ))->getField('yongjin');

        $this->assign('iTotalYJ',$iTotalYJ);
        $this->assign('iUnTiXianYJ',$iUnTiXianYJ);
        $this->assign('list',$list);
        $info1=M('media_users')->field('from_openid,tuijian_time')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        if($info1['from_openid']){
            $info1['nickname']=M('Wxusers')->where(array('uid'=>$this->uid,'openid'=>$info1['from_openid']))->getField('nickname');
            //   p($info1);
            $this->assign('dd_nickname',$info1);
        }else{
            $this->assign('dd_nickname','');
        }
        $this->display();
    }

    /**
     * 分销产品列表
     *
     */
    public function fengxia(){
        $product_new=M('Product_new');
        $list=$product_new->where(array('token'=>$this->token))->select();
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * 积分兑换
     */
    public function jifenshop(){
        $token	= $this->_get('token');
        $openid	= $this->_get('openid');
        $model = M('Integralshop');
        $integralshop_individual=M('Integralshop_individual');
        //$list = $model->where(array('tp_integralshop.token'=>$token))->field('tp_integralshop.*,l.name')->join('left join tp_usercenter_level as l on tp_integralshop.extent = l.id ')->select();
        //print_r($list);exit;
        //p($list);die;
        $where['num']=array('gt',0);
        $where['token']=array('eq',$token);
        $list = $model->field('giftname,num,pic,degree,integral,id')->where($where)->select();
        //获得已兑换的次数
        foreach($list as $key=>$v){
            $list[$key]['ci']=$integralshop_individual->where(array('lid'=>$v['id'],'token'=>$token,'openid'=>$openid))->count();
        }
        $this->assign('data',$list);
        $this->display();
    }
    /**
     * 确定兑换
     */
    public function exchange(){

        // echo time();exit;

        $where['openid'] = $_GET['openid'];
        $aUser=M('Usercenter_memberlist')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        // print_r($list);exit;
        $croe = $_POST['point'];//这个用的积分
        //print_r($croe);exit;

        if($aUser['score'] < $croe){
            $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/reveal',array('token'=>$_GET['token'],'openid'=>$this->_get('openid'))));exit;
        }else{
            $conn = M('Integralshop');
            $where_1['id']=$_POST['exc_id'];
            $a = $conn->where($where_1)->getField('degree');//在礼品积分表里查找礼品可兑换的次数
            // print_r($a);exit;
            $result = M('Usercenter_score_record');
            $gift = M('Integralshop_individual');
            $term_1 = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'lid'=>$_POST['exc_id']);
            $term_2 = array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'lid'=>$_POST['exc_id'],'time'=>time());
            $term = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>8,'score'=>-$croe,'add_time'=>time(),'titleid'=>$_POST['exc_id']);
            $count = $gift->where($term_1)->count('lid');
            // print_r($count);exit;
            if($a <= $count){
                $this->error("对不起，您兑换此活动的机会已经用完！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
            }else {
                //总积分 = 兑换前积分 - 兑换礼品的积分
                $uid = $_POST['exc_id'];
                //echo $uid;die;

                if (M('Integralshop')->where(array('id' => $uid))->setDec('num', '1') && M('Usercenter_memberlist')->where(array('id' => $aUser['id']))->setDec('score', $croe)) {
                    $gift = M('Integralshop_individual');
                    $data['token']=$this->token;
                    $data['openid']=$this->openid;
                    $data['lid']=$uid;
                    $data['time']=time();
                    $data['store']=$croe;
                    $giftname=M('Integralshop')->where(array('id' => $uid))->getField('giftname');
                    $data['name']=$giftname;
                    $gift->add($data);
                    $this->success("扣除成功！", U(MODULE_NAME . '/reveal', array('token' => $_GET['token'], 'openid' => $this->_get('openid'))));
                }
            }
        }
    }
    /**
     * 我的礼品
     */
    public function reveal(){
        $model =  M('Integralshop_individual');
        $list=$model->where(array('token'=>$_GET['token'],'openid'=>$this->_get('openid')))->select();
        $this->assign('list',$list);
        $this->display();

    }
    /**
     * 提现
     */
    public function tixian(){
        $oModel = M('Tixianjl');
        $oUserModel = M('Media_users');
        if(IS_AJAX){
           // p($_POST);DIE;
            $_POST['add_time']=date('Y-m-d h:i:s',time());
            $_POST['token']=$this->token;
            $_POST['openid']=$this->openid;
            $_POST['number']=$this->_post('money');
            if($oModel->add($_POST)){
                $data['true_name']=$this->_post('true_name');
                $data['phone']=$this->_post('phone');
                $data['bank_card']=$this->_post('bank_card');
                $data['bank_name']=$this->_post('bank_name');
                $oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);
                //这里就把金钱给去掉
                $oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->setDec('yongjin',$_POST['money']);
                echo $this->encode(array('codes'=>1,
                    'msg'=>'提现申请成功',
                    'urles'=>'index.php?g=Wap&m=Usercenterzj&a=tixian_jl&token='.$this->token.'&openid='.$this->openid));exit;
            }else{
                echo $this->encode(array('codes'=>0,
                    'msg'=>'系统繁忙，请稍后',
                    'urles'=>'index.php?g=Wap&m=Usercenterzj&a=tixian&token='.$this->token.'&openid='.$this->openid));exit;
            }
           // p($_POST);die;
       //    $data['']

          /*  if($info = $oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find()){
                if($info['yongjin']<$_POST['money']){
                    if($oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->setDec('yongjin',$_POST['money'])){
                        $_POST['add_time'] = date('Y-m-d H:i:s');
                        $_POST['status'] = 1;
                        $_POST['token'] = $this->token;
                        $_POST['openid'] = $this->openid;
                        if($oModel->add($_POST)){
                            echo $this->encode(array('codes'=>1,
                                'msg'=>'提现申请成功',
                                'urles'=>'index.php?g=Wap&m=Usercenterzj&a=tixian_jl&token='.$this->token.'&openid='.$this->openid));exit;
                        }else{
                            echo $this->encode(array('codes'=>0,
                                'msg'=>'系统繁忙，请稍后',
                                'urles'=>'index.php?g=Wap&m=Usercenterzj&a=tixian&token='.$this->token.'&openid='.$this->openid));exit;
                        }
                    }
                }else{
                    echo $this->encode(array('codes'=>3,
                        'msg'=>'您的余额不足',
                        'urles'=>'index.php?g=Wap&m=Usercenterzj&a=tixian&token='.$this->token.'&openid='.$this->openid));exit;
                }

            }*/
        }
        $info=$oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 提现操作
     */
    /*
 * 提现记录
 * */
    public function tixian_jl(){

        $list=M('Tixianjl')->where(array('token'=>$this->token,'openid'=>$this->openid))->select();
        $this->assign('list',$list);
        $this->display();
    }

}
