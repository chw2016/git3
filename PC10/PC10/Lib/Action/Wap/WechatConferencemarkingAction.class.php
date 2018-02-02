<?php
/*
 * Created by zichao in 2014-09-17
 */
class WechatConferencemarkingAction extends BaseAction
{
    /*
     * Input verification code and binding.
     */
    public function index()
    {
        $w['actid'] = $_GET['actid'];
        $w['token'] = $_GET['token'];
        $w['openid'] = $_GET['openid'];
        $info = M('Wechatact')->where(array('id' => $w['actid'], 'token' => $w['token']))->find();
        $this->assign('info', $info);
        $this->assign('w', $w);
        if (IS_POST) {
            /* var base_ver_code = $('#base_ver_code').val();
            var actid = $('#actid').val();
            var token = $('#token').val();*/
            $openid = $_POST['openid'];
            $actid = $_POST['actid'];
            $token = $_POST['token'];

            $base_ver_code = $_POST['base_ver_code'];
            $data = M('Wechatverification_code')->where(array('actid' => $actid, 'token' => $token, 'base_ver_code' => $base_ver_code))->find();
            $gdata = M('Wechatverification_code')->where(array('actid' => $actid, 'token' => $token, 'res_ver_code' => $base_ver_code))->find();

            if ($data || $gdata) {
                //&token=f17f0d1e02a8976cf065163525547260&openid=oZcHCty117GCPd5AqgcnbwVLJG6g&actid=26
                $this->success('登陆成功！', U(MODULE_NAME . '/score', array('token' => $token, 'openid' => $openid, 'actid' => $actid)));
            } else {
                $this->error('修改失败！', U(MODULE_NAME . '/index', array('token' => $token, 'openid' => $openid, 'actid' => $actid)));
            }
        }

        /* $a = M('Wechatscore')->where($w)->find();
         if(!$a){
             if(IS_POST){
                 $code['verification_code'] = $_POST['verification_code'];
                 $data['name'] = $_POST['name'];
                 $data['tel'] = $_POST['tel'];
                 $data['token'] = $_POST['token'];
                 $data['actid'] = $_POST['actid'];
                 $data['addtime'] = strtotime(date('Y-m-s H:i:s'));
                 $data['openid'] = $_POST['openid'];
                 if(M('Wechatscore')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'verification_code'=>$code['verification_code']))->find()){
                     $this->error();
                 }else{
                     if(substr($code['verification_code'],0,2) == 'BS'){
                         $data['verification_code'] = $code['verification_code'];
                         //echo substr($data['verification_code'],0,2);exit();
                         if(M('Wechatverification_code')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'base_ver_code'=>$data['verification_code']))->find()){
                             if(M('Wechatscore')->add($data)){
                                 $this->success('进入成功','./index.php?g=Wap&m=WechatConferencemarking&a=score&token='.$data['token'].'&openid='.$data['openid'].'&actid='.$data['actid']);
                             }else{
                                 $this->error('进入失败','./index.php?g=Wap&m=WechatConferencemarking&a=index&token='.$data['token'].'&openid='.$data['openid'].'&actid='.$data['actid']);
                             }
                         }
                     }elseif(substr($code['verification_code'],0,2) == 'RS'){
                         $data['verification_code'] = $code['verification_code'];
                         //print_r($data);exit();
                         //echo substr($data['verification_code'],0,2);exit();
                         if(M('Wechatverification_code')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'res_ver_code'=>$data['verification_code']))->find()){
                             //echo 'Find it!';exit();
                             if(M('Wechatscore')->add($data)){
                                 $this->success('进入成功','./index.php?g=Wap&m=WechatConferencemarking&a=score&token='.$data['token'].'&openid='.$data['openid'].'&actid='.$data['actid']);
                             }else{
                                 $this->error('进入失败','./index.php?g=Wap&m=WechatConferencemarking&a=index&token='.$data['token'].'&openid='.$data['openid'].'&actid='.$data['actid']);
                             }
                         }
                     }
                 }
                 //print_r($data);exit();
             }
         }else{
             $this->assign('a',$a);
             $this->redirect('./index.php?g=Wap&m=WechatConferencemarking&a=score&token='.$w['token'].'&openid='.$w['openid'].'&actid='.$w['actid']);
         }*/
        $this->display();
    }

    public function loginVote()
    {
        $w['actid'] = $_GET['actid'];
        $w['token'] = $this->token;
        $w['openid'] = $this->openid;
        $info = M('Wechatact')->where(array('id' => $w['actid'], 'token' => $w['token']))->find();
        $this->assign('info', $info);
        $this->assign('w', $w);
        if (IS_POST) {
            /* var base_ver_code = $('#base_ver_code').val();
            var actid = $('#actid').val();
            var token = $('#token').val();*/
            $openid = $this->openid;
            $actid = $_POST['actid'];
            $token = $this->token;
            $base_ver_code = $_POST['base_ver_code'];
            $data = M('Wechatverification_code')->where(array('actid' => $actid, 'token' => $token, 'base_ver_code' => $base_ver_code))->find();
            if ($data) {
                $this->success('登陆成功', U(MODULE_NAME . '/score', array('token' => $token, 'openid' => $openid, 'actid' => $actid, 'type' => 1, 'base_ver_code' => $base_ver_code)));
            } else {
                $gdata = M('Wechatverification_code')->where(array('actid' => $actid, 'token' => $token, 'res_ver_code' => $base_ver_code))->find();
                if ($gdata) {
                    $this->success('登陆成功！', U(MODULE_NAME . '/score', array('token' => $token, 'openid' => $openid, 'actid' => $actid, 'type' => 2, 'base_ver_code' => $base_ver_code)));
                } else {
                    $this->error('验证失败', U(MODULE_NAME . '/index', array('token' => $token, 'openid' => $openid, 'actid' => $actid)));
                }
            }

        }
        $this->display();
    }

    public function score()
    {
        $w['actid'] = $_GET['actid'];
        $w['token'] = $_GET['token'];
        $w['openid'] = $_GET['openid'];
        $type = $_GET['type'];
        $base_ver_code = $_GET['base_ver_code'];


        $info = M('Wechatact')->where(array('id' => $w['actid'], 'token' => $w['token']))->find();

        $data = M('Wechatlecturer')->where(array('token' => $w['token'], 'actid' => $w['actid']))->order('id asc')->select();
        $this->assign('data', $data);
        //print_r($data);
        $this->assign('info', $info);
        $this->assign('w', $w);
        $msg = M('Wechatscore')->where($w)->find();
        $msg['your_score'] = json_decode($msg['your_score'], true);
        $this->assign('msg', $msg['your_score']);

        //是否投票
        if(M('Wechatscore')->where($w)->find()){
            $this->assign('is_vote',1);
        }else{
            $this->assign('is_vote',2);
        }

        //print_r($msg);
        if (IS_POST) {
           // print_r($_POST);exit;
            $data = $_POST['info'];
            $info = substr($data, 0, -1);
            //  print_r($info);exit;
            // $db->where(array('id'=>array('in','1,2,5')));
           // $new = M('Wechatlecturer')->where(array('id' => array('in', $info)))->setInc('num');
            if ($_POST['type'] == 1) {
                $new = M('Wechatlecturer')->where(array('id' => array('in', $info)))->setInc('num');
                if ($new) {
                    $res = array('actid' => $_GET['actid'], 'token' => $this->token, 'openid' => $this->openid, 'verification_code' => $_POST['base_ver_code']);
                    $cnew = M('Wechatscore')->add($res);
                    if ($cnew) {
                        $this->success('投票成功！', U(MODULE_NAME . '/score', array('token' => $this->token,'actid' => $_GET['actid'], 'openid' => $this->openid,'base_ver_code'=>$_POST['base_ver_code'],'type'=>$_POST['type'])));
                    } else {
                        $this->error('投票失败！', U(MODULE_NAME . '/score', array('token' => $this->token,'actid' => $_GET['actid'], 'openid' => $this->openid,'base_ver_code'=>$_POST['base_ver_code'],'type'=>$_POST['type'])));
                    }
                }

                } else if ($_POST['type'] == 2) {
                    $new = M('Wechatlecturer')->where(array('id' => array('in', $info)))->setInc('snum');
                    if ($new) {
                        $res = array('actid' => $_GET['actid'], 'token' => $this->token, 'openid' => $this->openid, 'verification_code' => $_POST['base_ver_code']);
                        $cnew = M('Wechatscore')->add($res);
                        if ($cnew) {
                            $this->success('投票成功！', U(MODULE_NAME . '/score', array('token' => $this->token,'actid' => $_GET['actid'], 'openid' => $this->openid,'base_ver_code'=>$_POST['base_ver_code'],'type'=>$_POST['type'])));
                        } else {
                            $this->error('投票失败！', U(MODULE_NAME . '/score', array('token' => $this->token,'actid' => $_GET['actid'], 'openid' => $this->openid,'base_ver_code'=>$_POST['base_ver_code'],'type'=>$_POST['type'])));
                        }
                    }

                }


                //print_r($new);exit;

                /* $data['token'] = $_POST['token'];
                 $data['actid'] = $_POST['actid'];
                 $data['openid'] = $_POST['openid'];
                 $a = M('Wechatscore')->where($data)->find();
                 if($a['your_score'] == ""){
                     //print_r($data);exit();
                     $name = $_POST['lecturername'];
                     $score = $_POST['lecturerscore'];
                     $arr = array(
                             $name=>$score
                     );
                     //print_r($arr);exit();
                     $array['your_score'] = json_encode($arr);
                     $array['score_addtime'] = strtotime(date('Y-m-d H:i:s'));
                     //print_r($array);exit();
                     if(M('Wechatscore')->where($data)->save($array)){
                         $g = M('Wechatlecturer')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'name'=>$name))->find();
                         $h['num'] = $g['num'] + 1;
                         $h['all_score'] = $g['all_score'] + $score;
                         $h['current_average'] = $h['all_score'] / $h['num'];
                         M('Wechatlecturer')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'name'=>$name))->save($h);
                         $this->success();
                     }else{
                         $this->error('提交失败');
                     }
                 }else{
                     $a['your_score'] = json_decode($a['your_score'],true);
                     //print_r($a['your_score']);exit();
                     $name = $_POST['lecturername'];
                     $score = $_POST['lecturerscore'];
                     $arr = array(
                             $name=>$score
                     );
                     //print_r($arr);exit();
                     $b = array_merge($a['your_score'],$arr);
                     //print_r($b);exit();
                     $array['your_score'] = json_encode($b);
                     //print_r($array['your_score']);exit();
                     $array['score_addtime'] = strtotime(date('Y-m-d H:i:s'));
                     if(M('Wechatscore')->where($data)->save($array)){
                         $x = M('Wechatlecturer')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'name'=>$name))->find();
                         $y['all_score'] = $x['all_score'] + $score;
                         $y['num'] = $x['num'] + 1;
                         $y['current_average'] = $y['all_score'] / $y['num'];
                         M('Wechatlecturer')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'name'=>$name))->save($y);
                         $this->success();
                     }else{
                         $this->error();
                     }
                 }*/


        }
        $this->assign('type', $type);
        $this->assign('base_ver_code', $base_ver_code);
        $this->display();
    }
}