<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-8-11
 * Time: 下午5:23
 * To change this template use File | Settings | File Templates.
 */

class ActivityAction extends BaseAction{


    public function _initialize(){
        parent::_initialize();
        //$this->openid = 'orv--t5_5nbCmcihl9ywAJLPvk78';
        if($this->openid){
            $wxusers = M('Wxusers')->where(array('uid'=>$this->tpl['id'],'openid'=>$this->openid))->find();
            if($wxusers){
                $this->wxusers = $wxusers;
            }
            $this->assign('openid',$this->openid);
        }else{
            header('Location:http://m.wapwei.com');
        }
    }

    public function index(){
        $aid = $this->_get('aid');
        $token = $this->_get('token');

        if(!empty($aid) && !empty($token)){
            $activityModel = M('Activity');
            $adata = $activityModel->where(array('aid'=>$aid,'token'=>$token))->find();
            $activityUsersModel = M('Activity_users');
            $r_times = $activityUsersModel->where(array('aid'=>$aid))->sum('r_times');
            $randdata = $activityUsersModel->where(array('aid'=>$aid))->order('rand()')->find();
            $mydata = $activityUsersModel->where(array('aid'=>$aid,'openid'=>$this->openid))->find();
            if($mydata){
                $rank = $activityUsersModel->where('aid='.$aid." AND r_times > ".$mydata['r_times'])->count();
                $rankstr = ceil($rank/2)."~".($rank+ceil($rank/2));
            }else{
                $rankstr = '';
            }
            $alldata = $activityUsersModel->field('nickname')->where(array('aid'=>$aid))->limit(16)->order('add_time desc')->select();
            $this->assign('mydata',$mydata);
            $this->assign('rankstr',$rankstr);
            $this->assign('alldata',$alldata);
            if($adata){
                $this->assign('adata',$adata);
                $this->assign('randdata',$randdata);
                $this->assign('openid',$this->openid);
                $this->assign('aid',$aid);
                $this->assign('token',$token);
                $this->assign('sumusers',$r_times);
                $this->display();
                $this->userread($aid,1);
            }else{
                header('Location:http://m.wapwei.com');
            }
        }else{
            header('Location:http://m.wapwei.com');
        }

    }

    public function joinActivity(){
        $aid = $this->_post('aid');
        $openid = $this->_post('openid');
        $uid = $this->_post('uid');
        $wxa_name = $this->_post('wxa_name');
        $wxa_desc = $this->_post('wxa_desc');
        if(!empty($aid) && !empty($openid) && !empty($uid) && !empty($wxa_name)){
            $activityUsersModel = M('Activity_users');
            $ausersdata = $activityUsersModel->where(array('aid'=>$aid,'uid'=>$uid,'openid'=>$openid))->find();
            if(!$ausersdata){
                $data = array();
                $data['aid'] = $aid;
                $data['openid'] = $openid;
                $data['uid'] = $uid;
                $data['add_time'] = time();
                $data['wxa_name'] = $wxa_name;
                $data['wxa_desc'] = $wxa_desc;
                $data['nickname'] = $this->wxusers['nickname'];
                if($insertid = $activityUsersModel->add($data)){
                    $this->ajaxReturn(array('code'=>0,'msg'=>'success','auid'=>$insertid));
                }else{
                    $this->ajaxReturn(array('code'=>-3,'msg'=>'系统繁忙,请稍后再试！'));
                }
            }else{
                $this->ajaxReturn(array('code'=>-2,'msg'=>'你已经参加活动,赶紧让朋友转发吧'));
            }
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'invalid control'));
            exit;
        }
    }

    public function getCurrent(){
        $aid = $this->_post('aid');
        $uid = $this->_post('uid');
        $activityUsersModel = M('Activity_users');
        $mydata = $activityUsersModel->where(array('aid'=>$aid,'uid'=>$uid))->order('rand()')->limit(1)->find();
        if($mydata){
            $this->ajaxReturn(array('code'=>0,'data'=>$mydata));
        }else{
            $this->ajaxReturn(array('code'=>-1));
        }
    }

    public function selfReport(){
        $aid = $this->_get('aid');
        $uid = $this->_get('uid');
        $activityUsersModel = M('Activity_users');
        if($activityUsersModel->where(array('aid'=>$aid,'uid'=>$uid))->setInc('self_report',1)){
            $this->ajaxReturn(array('code'=>0,'msg'=>'success'));
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'error'));
        }
    }

    public function cancelReport(){
        $aid = $this->_get('aid');
        $uid = $this->_get('uid');
        $auid = $this->_get('auid');
        $activityUsersModel = M('Activity_users');
        if($activityUsersModel->where(array('aid'=>$aid,'openid'=>$this->openid,'id'=>$auid))->setInc('cancel_times',1)){
            $this->ajaxReturn(array('code'=>0,'msg'=>'success'));
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'error'));
        }
    }

    public function himReport(){
        $aid = $this->_get('aid');
        $uid = $this->_get('uid');
        $auid = $this->_get('auid');
        $type = $this->_get('type');
        $activityUsersModel = M('Activity_users');
        if($type == 1){
            $r_type = 'r_times_timeline';
        }else if($type == 2){
            $r_type = 'r_times_friend';
        }
        if($activityUsersModel->where(array('aid'=>$aid,'openid'=>$this->openid,'id'=>$auid))->setInc($r_type,1)){
            $activityUsersModel->where(array('aid'=>$aid,'openid'=>$this->openid,'id'=>$auid))->setInc('r_times',1);
            $this->ajaxReturn(array('code'=>0,'msg'=>'success'));
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'error'));
        }
    }

    public function myshare(){
        $aid = $this->_get('aid');
        $token = $this->_get('token');
        $auid = $this->_get('auid');
        if(!empty($aid) && !empty($token)){
            $activityModel = M('Activity');
            $adata = $activityModel->where(array('aid'=>$aid,'token'=>$token))->find();
            $activityUsersModel = M('Activity_users');
            $r_times = $activityUsersModel->where(array('aid'=>$aid))->sum('r_times');
            $randdata = $activityUsersModel->where(array('aid'=>$aid))->order('rand()')->limit(1)->find();
            $mydata = $activityUsersModel->where(array('aid'=>$aid,'openid'=>$this->openid))->find();

            $rank = $activityUsersModel->where('aid='.$aid." AND r_times > ".$mydata['r_times'])->count();
            $rankstr = ceil($rank/2)."~".($rank+ceil($rank/2));

            $alldata = $activityUsersModel->field('nickname')->where(array('aid'=>$aid))->limit(16)->order('add_time desc')->select();
            $this->assign('mydata',$mydata);
            $this->assign('alldata',$alldata);
            $this->assign('rankstr',$rankstr);
            if($adata){
                $this->assign('adata',$adata);
                $this->assign('randdata',$randdata);
                $this->assign('openid',$this->openid);
                $this->assign('aid',$aid);
                $this->assign('wxname',$this->wxusers['nickname']);
                $this->assign('token',$token);
                $this->assign('sumusers',$r_times);
                $this->display();
                $this->userread($auid,2);
            }else{
                header('Location:http://m.wapwei.com');
            }
        }else{
            header('Location:http://m.wapwei.com');
        }
    }

    private function userread($id,$type){
        if($type == 1){
            $activityModel = M('Activity');
            $activityModel->where(array('id'=>$id))->setInc('user_read',1);
        }else{
            $activityUsersModel = M('Activity_users');
            $activityUsersModel->where(array('id'=>$id))->setInc('user_read',1);
        }
    }

    public function guize(){
        $this->display();
    }


    /*
     * 思维抽奖
     */
    public function choujiang(){
        if(IS_POST){
            $one = array('name'=>'毋海敏','phone'=>'18509299286');
            $two = array(array('name'=>'张恩娣','phone'=>'18629409583'),array('name'=>'刘晨','phone'=>'18009229959'),array('name'=>'张毅','phone'=>'18700875801'));
            $allcounts = M('test_suggest')->where(array('token'=>$this->token,'is_get'=>1))->count();
            if($allcounts < 11){
                if($allcounts < 7) {
                    if ($data = M('test_suggest')->where(array('token' => $this->token, 'is_get' => 0))->select()) {
                        $num = rand(0, count($data));
                        if (M('test_suggest')->where(array('token' => $this->token, 'is_get' => '', 'id' => $data[$num]['id']))->save(array('is_get' => 1, 'prize_type' => '二等奖'))) {
                            echo $this->encode(array('code' => 0, 'msg' => '恭喜手机号码为' . $data[$num]['phone'] . '的' . $data[$num]['name'] . '获得二等奖'));
                        } else {
                            echo $this->encode(array('code' => 0, 'msg' => '系统繁忙请重试'));
                        }

                    } else {
                        echo $this->encode(array('code' => 0, 'msg' => '参与人员都中过奖了'));
                    }
                }

                if($allcounts == 7) {
                    if ($data = M('test_suggest')->where(array('token' => $this->token, 'is_get' => 0))->select()) {
                        $num = rand(0, count($data));
                        if (M('test_suggest')->where(array('token' => $this->token, 'is_get' => '', 'id' => $data[$num]['id']))->save(array('is_get' => 1, 'prize_type' => ''))) {
                            echo $this->encode(array('code' => 0, 'msg' => '恭喜手机号码为' . $two[0]['phone'] . '的' . $two[0]['name'] . '获得二等奖'));
                        } else {
                            echo $this->encode(array('code' => 0, 'msg' => '系统繁忙请重试'));
                        }

                    } else {
                        echo $this->encode(array('code' => 0, 'msg' => '参与人员都中过奖了'));
                    }
                }

                if($allcounts == 8) {
                    if ($data = M('test_suggest')->where(array('token' => $this->token, 'is_get' => 0))->select()) {
                        $num = rand(0, count($data));
                        if (M('test_suggest')->where(array('token' => $this->token, 'is_get' => '', 'id' => $data[$num]['id']))->save(array('is_get' => 1, 'prize_type' => ''))) {
                            echo $this->encode(array('code' => 0, 'msg' => '恭喜手机号码为' . $two[1]['phone'] . '的' . $two[1]['name'] . '获得二等奖'));
                        } else {
                            echo $this->encode(array('code' => 0, 'msg' => '系统繁忙请重试'));
                        }

                    } else {
                        echo $this->encode(array('code' => 0, 'msg' => '参与人员都中过奖了'));
                    }
                }

                if($allcounts == 9) {
                    if ($data = M('test_suggest')->where(array('token' => $this->token, 'is_get' => 0))->select()) {
                        $num = rand(0, count($data));
                        if (M('test_suggest')->where(array('token' => $this->token, 'is_get' => '', 'id' => $data[$num]['id']))->save(array('is_get' => 1, 'prize_type' => ''))) {
                            echo $this->encode(array('code' => 0, 'msg' => '恭喜手机号码为' . $two[2]['phone'] . '的' . $two[2]['name'] . '获得二等奖'));
                        } else {
                            echo $this->encode(array('code' => 0, 'msg' => '系统繁忙请重试'));
                        }

                    } else {
                        echo $this->encode(array('code' => 0, 'msg' => '参与人员都中过奖了'));
                    }
                }

                if($allcounts == 10) {
                    if ($data = M('test_suggest')->where(array('token' => $this->token, 'is_get' => 0))->select()) {
                        $num = rand(0, count($data));
                        if (M('test_suggest')->where(array('token' => $this->token, 'is_get' => '', 'id' => $data[$num]['id']))->save(array('is_get' => 1, 'prize_type' => ''))) {
                            echo $this->encode(array('code' => 0, 'msg' => '恭喜手机号码为' . $one['phone'] . '的' . $one['name'] . '获得一等奖'));
                        } else {
                            echo $this->encode(array('code' => 0, 'msg' => '系统繁忙请重试'));
                        }

                    } else {
                        echo $this->encode(array('code' => 0, 'msg' => '参与人员都中过奖了'));
                    }
                }

            }else{
                echo $this->encode(array('code'=>0,'msg'=>'奖品已经抽完了哦'));
            }

        }else {
            $this->display();
        }
    }



}