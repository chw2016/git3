<?php
/**
 * Created by IntelliJ IDEA.
 * User: 李铭
 * Date: 14-8-30
 * Time: 上午11:48
 * To change this template use File | Settings | File Templates.
 */
class UsercenterlmAction extends BaseAction{

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
            $where['score']=array('elt',$userscore[0]['allscore']);
            $userlevel_data = M("Usercenter_level")->where($where)->order('score desc')->limit(1)->find();
        }
        if($usercenterdata['is_bind_st'] == 1){
            $shitidata = M('Usercenter_shitimember')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
            $this->assign('shitidata',$shitidata);
        }
        $this->uid = $wxuser['id'];
        $this->assign('wxuser',$wxuser);
        $this->assign('userdata',$userdata);
        $this->assign('usercenterdata',$usercenterdata);
        $this->usercenterdata = $usercenterdata;
        if($userlevel_data){
            $this->user_qun_id = $userlevel_data['id'];
            $this->assign('userlevelname',$userlevel_data['name']);
        }else{
            $this->assign('userlevelname','普通会员');
        }
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

    /**
     * 首页
     */
    public function index(){
        echo 88;die;
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
        $this->display();

    }

    /*
     * 优惠
     */
    public function salecard(){
        $where = array();
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
            $salecarddata = M('Usercenter_salecard')->where($where)->select();
            $this->assign('salecardcount',$salecardcount);
            $this->assign('salecarddata',$salecarddata);
        }

        $usersalecard = M('Usercenter_user_salecard')->where(array('token'=>$this->token,'openid'=>$this->openid))->select();
        $salecardModel = M('Usercenter_salecard');
        foreach($usersalecard as $k=>$v){
            if(!$salecardModel->where(array('id'=>$v['sale_id']))->find()){
                unset($usersalecard[$k]);
            }
        }

        $this->assign('allow_salecard',1);
        $this->assign('usersalecardcount',count($usersalecard));
        $this->display();
    }

    public function isgetsalecard(){
        $usersalecard = M('Usercenter_user_salecard')->where(array('token'=>$this->token,'openid'=>$this->openid))->select();
        $salecardModel = M('Usercenter_salecard');
        foreach($usersalecard as $k=>$v){
            if(!$salecardModel->where(array('id'=>$v['sale_id']))->find()){
                unset($usersalecard[$k]);
            }
        }

        $this->assign('usersalecardcount',count($usersalecard));
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
        }
        $this->assign('usersalecard',$usersalecard);
        $this->assign('isget_salecard',1);
        $this->display();
    }

    /*
     * 优惠券详细
     */

    public function salecarddetail(){
        $where = array();
        if($_GET['id']){
            $where['id'] = $_GET['id'];
            $saledata = M('Usercenter_salecard')->where($where)->find();
            $usersalecard = M('Usercenter_user_salecard')->where(array('token'=>$this->token,'openid'=>$this->openid,'sale_id'=>$where['id']))->find();
            $this->assign('saledata',$saledata);
            $this->assign('usersalecard',$usersalecard);
            $this->display();
        }
    }

    /*
     * 领取优惠券
     */
    public function getsalecard(){
        if(IS_POST){
            $sid = $_POST['sid'];
            $res = M('Usercenter_user_salecard')->where(array('token'=>$this->token,'openid'=>$this->openid,'sale_id'=>$sid))->find();
            if($res){
                echo $this->encode(array('code'=>-1,'msg'=>'您已经领取过此优惠券了哦'));exit;
            }else{
                $where['id'] = $sid;
                $saledata = M('Usercenter_salecard')->where($where)->find();
                $counts = M('Usercenter_user_salecard')->where(array('token'=>$this->token,'sale_id'=>$sid))->count();
                if($counts < $saledata['numbers']){
                    $data = array();
                    $data['sale_id'] = $sid;
                    $data['sale_name'] = $saledata['name'];
                    $data['sale_sn'] = $this->create_password();
                    $data['money'] = $saledata['sale_money'];
                    $data['start_date'] = $saledata['start_date'];
                    $data['end_date'] = $saledata['end_date'];
                    $data['token'] = $this->token;
                    $data['openid'] = $this->openid;
                    $data['add_time'] = time();
                    $data['status'] = 0;
                    if(M('Usercenter_user_salecard')->add($data)){
                        echo $this->encode(array('code'=>0,'msg'=>'领取成功,可以按照使用描述去使用了哦'));exit;
                    }else{
                        echo $this->encode(array('code'=>-1,'msg'=>'领取失败,请重试..'));exit;
                    }
                }else{
                    echo $this->encode(array('code'=>-1,'msg'=>'您来晚了哦,优惠券已发放完了'));exit;
                }
            }
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
     * 成为会员
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
                    echo $this->encode(array('code'=>0,'msg'=>'保存成功','url'=>C('site_url').'index.php?g=Wap&m=Usercenter&a=index&token='.$this->token."&openid=".$this->openid));exit;
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
                    echo $this->encode(array('code'=>0,'msg'=>'保存成功','url'=>C('site_url').'index.php?g=Wap&m=Usercenter&a=index&token='.$this->token."&openid=".$this->openid));exit;
                }else{
                    echo $this->encode(array('code'=>-2,'msg'=>'保存失败请重试'));exit;
                }
            }
        }else{

            $this->assign('usercenterdata',$usercenterdata);
            $this->assign('memberdata',$memberdata);
            $this->assign('birth_day',explode('-',$memberdata['birth_day']));
            $this->assign('address',explode('|',$memberdata['address']));
            $this->display();
        }
    }


    public function usersign(){
        $signdata = M('Usercenter_sign_set')->where(array('token'=>$this->token,'status'=>1))->find();
        if(!$signdata){
            $signdata = null;
        }
        $usercenter_signmodel = M('Usercenter_sign');
        $usercenter_scored_recordModel = M('Usercenter_score_record');
        $userissign = $usercenter_signmodel->where(array('token'=>$this->token,'openid'=>$this->openid,'sign_date'=>date("Y-m-d",time())))->find();
        $allsigncounts = $usercenter_signmodel->where(array('token'=>$this->token,'openid'=>$this->openid))->count();
        if(IS_POST){
            if($this->is_member){
                if(!$userissign){
                    $newdata = array();
                    $newdata['sign_date'] = date("Y-m-d",time());
                    $newdata['openid'] = $this->openid;
                    $newdata['token'] = $this->token;
                    $newdata['score'] = $signdata['day_score'];
                    if($usercenter_signmodel->add($newdata)){
                        $data = array();
                        $data['token'] = $this->token;
                        $data['openid'] = $this->openid;
                        $data['type'] = 1;
                        $data['score'] = $signdata['day_score'];
                        $data['add_time'] = time();
                        $usercenter_scored_recordModel->add($data);

                        $memdata1 = M("Usercenter_memberlist")->where(array('token'=>$this->token,'openid'=>$this->openid,'uid'=>$this->uid))->find();
						
					    $memscore1 = $memdata1['score']+$signdata['day_score'];

                        M("Usercenter_memberlist")->where(array('token'=>$this->token,'openid'=>$this->openid,'uid'=>$this->uid))->data(array('score'=>$memscore1))->save();
                        $lastdate = date("Y-m-d",time()-$signdata['days']*3600*24);
                        $curdata = date("Y-m-d",time());
                        $lwhere = array();
                        $lwhere['sign_date']=array('between',array($lastdate,$curdata));
                        $counts = $usercenter_signmodel->where($lwhere)->count();
                        if($counts == $signdata['days']){
                            $data = array();
                            $data['token'] = $this->token;
                            $data['openid'] = $this->openid;
                            $data['type'] = 2;
                            $data['score'] = $signdata['scores'];
                            $data['add_time'] = time();
                            $usercenter_scored_recordModel->add($data);
							$memdata = M("Usercenter_memberlist")->where(array('token'=>$this->token,'openid'=>$this->openid,'uid'=>$this->uid))->find();
							
							$memscore = $memdata['score']+$signdata['scores'];

                            M("Usercenter_memberlist")->where(array('token'=>$this->token,'openid'=>$this->openid,'uid'=>$this->uid))->data(array('score'=>$memscore))->save();
                            echo $this->encode(array('code'=>0,'msg'=>'哈哈,成功签到获得'.$signdata['day_score'].'分签到积分,您连续签到'.$signdata['days'].'获得'.$signdata['scores'].'分奖励！'));
                        }else{
                            echo $this->encode(array('code'=>0,'msg'=>'哈哈,成功签到获得'.$signdata['day_score'].'分签到积分'));
                        }
                    }else{
                        echo $this->encode(array('code'=>-2,'msg'=>'签到失败了哦请重试'));
                    }
                }else{
                    echo $this->encode(array('code'=>-2,'msg'=>'您今天已经签过到了哦'));
                }
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'您还不是会员哦,赶快升级会员吧','url'=>C('site_url').'index.php?g=Wap&m=Usercenter&a=joinusercenter&token='.$this->token."&openid=".$this->openid));
            }
        }else{
            $this->assign('signdata',$signdata);
            $this->assign('userissign',$userissign);
            $this->assign('allsigncounts',$allsigncounts);
            $this->display();
        }

    }

    public function usersignlist(){
        $usercenter_signModel = M('Usercenter_sign');
        $signlist = $usercenter_signModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('sign_date desc')->select();
        $this->assign('signlist',$signlist);
        $this->display();
    }

    public function scorerecord(){
        $usercenter_scoreModel = M('Usercenter_score_record');
        $scorerecordlist = $usercenter_scoreModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->select();
        $this->assign('scorerecordlist',$scorerecordlist);
        $this->display();
    }


    public function moneyrecord(){
        $usercenter_moneyModel = M('Usercenter_money_record');
        $moneyrecordlist = $usercenter_moneyModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->select();
        $this->assign('moneyrecordlist',$moneyrecordlist);
        $this->display();
    }

    public function genMoney(){
        $usercenter_money_recordModel = M('Usercenter_money_record');
        if(!empty($_POST['money'])){
            $data['token'] = $this->token;
            $data['openid'] = $this->openid;
            $data['pay_type'] = 1;
            $data['status'] = 0;
            $data['add_time'] = time();
            $data['money'] = intval($_POST['money']);
            if($lastid = $usercenter_money_recordModel->add($data)){
                echo $this->encode(array('code'=>0,'msg'=>'充值订单生成成功,正在跳转..','order_id'=>$lastid));
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'订单生成失败,请重试'));
            }
        }
    }

    public function bindcard(){
        if($_POST['sid'] && $_POST['id']){
            $sid = $_POST['sid'];
            $id = $_POST['id'];
            $shitimember = M('Usercenter_shitimember')->where(array('token'=>$this->token,'id'=>$sid))->find();
            $member = M('Usercenter_memberlist')->where(array('id'=>$id))->find();
            if($shitimember['member_score']){
                $updatescore = $shitimember['member_score'];
            }else{
                $updatescore = 0;
            }

            if($shitimember['member_money']){
                $updatemoney = $shitimember['member_money'];
            }else{
                $updatemoney = 0;
            }

            $res1 = M('Usercenter_shitimember')->where(array('token'=>$this->token,'id'=>$sid))->save(array('status'=>1,'openid'=>$this->openid));
            $res2 = M('Usercenter_memberlist')->where(array('id'=>$id))->save(array('is_bind_st'=>1,'score'=>$member['score']+$updatescore,'money'=>$member['money']+$updatemoney));
            if($res1 && $res2){
                echo $this->encode(array('code'=>0,'msg'=>'绑定成功,正在跳转..'));
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'绑定失败,请重试'));
            }
        }else{
            $shitidata = M('Usercenter_shitimember')->where(array('token'=>$this->token,'member_phone'=>$this->usercenterdata['phone']))->find();
            $this->assign('shitidata',$shitidata);
            $this->assign('usercenterdata',$this->usercenterdata);
            $this->display();
        }
    }

    public function scoreguize(){
        $shangdata = M('Usercenter_set')->where(array('token'=>$this->token))->find();
        $this->assign('shangdata',$shangdata);
        $this->display();
    }

    public function notice(){
        $result = M('Usercenter_notice')->where(array('token'=>$this->token))->order('add_time desc')->select();
        $this->assign('result',$result);
        $this->display();
    }

    public function notice_detail(){
        $result = M('Usercenter_notice')->where(array('token'=>$this->token,'id'=>$_GET['id']))->order('add_time desc')->find();
        $result['content'] = html_entity_decode($result['content']);
        $this->assign('result',$result);
        $this->display();
    }











}