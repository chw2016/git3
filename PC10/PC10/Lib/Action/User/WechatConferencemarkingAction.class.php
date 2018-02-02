<?php
/*
 * The WeChat conference marking
 * Created by zichao in 2014-09-13
 * Notice:If you want to modify this code,please do note by English.
 */
class WechatConferencemarkingAction extends UserAction{
    /*
     * The index page show the activities of meeting
     * Managers can modify,add or delete that
     * and managers also can modify,add or delete teachers by Click on the Getinto buttom
     * otherwise they can set up audience's number
     */
    public function index(){
        if($this->token = $_GET['token']){
            $a['token'] = $this->token;
            $act = M('Wechatact')->where($a)->select();
            $this->assign('act',$act);
            
        }
        $this->display();
    }
    /*
     * Managers can modify the activities of meeting if the $op = 1,
     * else if the $op != 1,managers can add this.
     */
    public function manage(){
        if($this->token == $_GET['token']){
            $op = $_GET['op']?$_GET['op']:0;
            if(IS_POST){
                $op = $_POST['op']?$_POST['op']:0;
                $data['name'] = $_POST['name'];
                $data['address'] = $_POST['address'];
                $data['starttime'] = $_POST['starttime'];
                $data['endtime'] = $_POST['endtime'];
                $data['pic'] = $_POST['pic'];
                $data['qrcode'] = $_POST['qrcode'];
                $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
                $data['uid'] = session('uid');
                $data['token'] = $this->token;
                $data['notice'] = $_POST['notice'];
                $data['top_pic'] = $_POST['top_pic'];
                $data['background_pic'] = $_POST['background_pic'];

                $data['id'] = $_POST['id'];
                //$data['verification_code_num'] = floor($_POST['verification_code_num']);
                //$data['res_verification_code_num'] = floor($_POST['res_verification_code_num']);
                //print_r($data);exit();
                if($op == 0){
                    $data['last_edit_time'] = $data['addtime'];
                    //print_r($data);exit();
                    if(M('Wechatact')->add($data)){
                        //print_r($data);exit();
                        $this->success('添加成功','index.php?g=User&m=WechatConferencemarking&a=index&token='.$this->token);
                    } else {
                        $this->error('添加失败', 'index.php?g=User&m=WechatConferencemarking&a=manage&op=0&token='.$this->token);
                    }
                }elseif($op == 1){
                    $w['id'] = $_POST['id'];
                    $w['token'] = $this->token;
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    //print_r($data);exit();
                    if (M('Wechatact')->where($w)->save($data)) {
                        $this->success('编辑成功','./index.php?g=User&m=WechatConferencemarking&a=index&token='.$this->token);
                    } else {
                        $this->error('编辑失败', './index.php?g=User&m=WechatConferencemarking&a=manager&op=1&token='.$this->token.'&id='.$data['id']);
                    }
                }
            }
            /*
             * When the manage modify kindergarten information the qr code be created.
            * This qr code contain kgid;userid;openid;token.
            */
            if ($op == 1) {
                $this->create();
                $w['id'] = $_GET['actid'];
                $w['token'] = $this->token;
                $this->assign('w',$w);
                //print_r($w);exit();
                $info = M('Wechatact')->where($w)->find();
                $this->assign('info',$info);
            }
        }
        $this->assign('op',$op);
        $this->display();
    }
    /*
     * This function can user for delete the activities.
     */
    public function del(){
        if($this->token == $_GET['token']){
            if(IS_POST){
                $w['token'] = $_GET['token'];
                $w['id'] = $_GET['actid'];
                if(M('Wechatact')->where($w)->find()){
                    M('Wechatact')->where($w)->delete();
                    M('Wechatlecturer')->where(array('token'=>$w['token'],'actid'=>$w['id']))->delete();
                    M('Wechatlecturer')->where(array('token'=>$w['token'],'actid'=>$w['id']))->delete();
                    M('Wechatverification_code')->where(array('token'=>$w['token'],'actid'=>$w['id']))->delete();
                    $this->success('删除成功', './index.php?g=User&m=WechatConferencemarking&a=index&token='.$this->token.'&actid='.$w['actid']);
                }
            }
        }
    }
    /*
     * Manage can add/modify teacher and create verification code
     */
    public function show(){
        if($this->token == $_GET['token']){
            $w['token'] = $_GET['token'];
            $w['id'] = $_GET['actid'];
            $this->assign('w',$w);
            $act = M('Wechatact')->where($w)->find();
            $this->assign('act',$act);
        }
        $this->display();   
    }
    /*
     * Display teacher and manage teacher
     */
    public function teacher(){
        if($this->token == $_GET['token']){
            $w['actid'] = $_GET['actid'];
            $w['token'] = $this->token;
            $this->assign('w',$w);
            //print_r($w);
            $lecturer = M('Wechatlecturer')->order('id asc')->where($w)->select();
            $a = count($lecturer);
            for($i=0;$i<$a;$i++){
                $lecturer[$i]['addtime'] = date('Y-m-d H:i:s',$lecturer[$i]['addtime']);
                $lecturer[$i]['last_edit_time'] = date('Y-m-d H:i:s',$lecturer[$i]['last_edit_time']);
            }
            $this->assign('lecturer',$lecturer);
            //print_r($lecturer);
            $this->display();
        }
    }
    /*
     * Add or Edit lecturer
     */
    public function manageteacher(){
        if($this->token == $_GET['token']){
            $w['actid'] = $_GET['actid'];
            $w['token'] = $this->token;
            $this->assign('w',$w);
            //print_r($w);exit();
            $op = $_GET['op']?$_GET['op']:0;
            $this->assign('op',$op);
            if(IS_POST){
                $op = $_POST['op']?$_POST['op']:0;
                $data['key'] = $_POST['key'];
                $data['name'] = $_POST['name'];
                $data['head_pic'] = $_POST['head_pic'];
                $data['actid'] = $_POST['actid'];
                $data['token'] = $this->token;
                $data['uid'] = session('uid');
                if($op == 0){
                    $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    //print_r($data);exit();
                    if(M('Wechatlecturer')->add($data)){
                        $this->success('添加成功','index.php?g=User&m=WechatConferencemarking&a=teacher&token='.$this->token.'&actid='.$data['actid']);
                    }else{
                        $this->error('添加失败','index.php?g=User&m=WechatConferencemarking&a=manageteacher&token='.$this->token.'&actid='.$data['actid']);
                    }
                }elseif($op == 1){
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    $b['actid'] = $_POST['actid'];
                    $b['id'] = $_POST['id'];
                    $b['token'] = $this->token;
                    //print_r($b);exit();
                    if(M('Wechatlecturer')->where($b)->save($data)){
                        $this->success('编辑成功','index.php?g=User&m=WechatConferencemarking&a=teacher&token='.$this->token.'&actid='.$data['actid']);
                    }else{
                        $this->error('编辑失败','index.php?g=User&m=WechatConferencemarking&a=manageteacher&token='.$this->token.'&actid='.$data['actid'].'&id='.$b['id']);
                    }
                }
            }
            /*
             * Edit lecturer
             */
            if($op == 1){
                $a['actid'] = $_GET['actid'];
                $a['id'] = $_GET['id'];
                $a['token'] = $this->token;
                $this->assign('a',$a);
                $lecturer = M('Wechatlecturer')->where($a)->find();
                $this->assign('lecturer',$lecturer);
            }
        }
        $this->display();
    }
    public function del_lecturer(){
        if($this->token == $_GET['token']){
            if(IS_POST){
                $w['actid'] = $_GET['actid'];
                $w['id'] = $_GET['lecturer_id'];
                $w['token'] = $this->token;
                $ie = M('Wechatlecturer')->where($w)->find();
                //print_r($ie);exit();
                if($ie){
                    if(M('Wechatlecturer')->where($w)->delete()){
                        $this->success('删除成功','index.php?g=User&m=WechatConferencemarking&a=teacher&token='.$this->token.'&actid='.$w['actid']);
                    }else{
                        $this->error('删除失败','index.php?g=User&m=WechatConferencemarking&a=teacher&token='.$this->token.'&actid='.$w['actid']);
                    }
                }
            }
        }
    }
    /*
     * verification code
     */
    public function verification_code(){
        if($this->token == $_GET['token']){
            $w['actid'] = $_GET['actid'];
            $this->assign('w',$w);
            if(IS_POST){
                if($_POST['op'] == 1){
                    $data['base_ver_num'] = $_POST['base_ver_num'];
                    $data['actid'] = $_POST['actid'];
                    $data['token'] = session('token');
                    M('Wechatverification_code')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'mark'=>'BS'))->delete();
                    if(M('Wechatact')->where(array('token'=>$data['token'],'id'=>$data['actid']))->save($data)){
                        $a = M('Wechatact')->where(array('token'=>$data['token'],'id'=>$data['actid']))->find();
                        for($i=0;$i<$a['base_ver_num'];$i++){
                            $base_ver_num[$i]['base_ver_code'] = 'BS'.sprintf("%04d",$i).mt_rand(0000,9999);
                            $base_ver_num[$i]['token'] = $data['token'];
                            $base_ver_num[$i]['actid'] = $data['actid'];
                            $base_ver_num[$i]['mark'] = 'BS';
                            $base_ver_num[$i]['addtime'] = strtotime(date('Y-m-d H:i:s'));
                            M('Wechatverification_code')->add($base_ver_num[$i]);
                        }
                        $this->success('生成基础验证码成功！','index.php?g=User&m=WechatConferencemarking&a=verification_code&token='.$data['token'].'&actid='.$data['actid']);
                    }else{
                        $this->error('生成基础验证码失败','index.php?g=User&m=WechatConferencemarking&a=verification_code&token='.$data['token']);
                    }
                }elseif($_POST['op'] == 0){
                    $data['res_ver_num'] = $_POST['res_ver_num'];
                    $data['actid'] = $_POST['actid'];
                    $data['token'] = session('token');
                    M('Wechatverification_code')->where(array('token'=>$data['token'],'actid'=>$data['actid'],'mark'=>'RS'))->delete();
                    if(M('Wechatact')->where(array('token'=>$data['token'],'id'=>$data['actid']))->save($data)){
                        $a = M('Wechatact')->where(array('token'=>$data['token'],'id'=>$data['actid']))->find();
                        for($i=0;$i<$a['res_ver_num'];$i++){
                            $res_ver_num[$i]['res_ver_code'] = 'RS'.sprintf("%04d",$i).mt_rand(0000,9999);
                            $res_ver_num[$i]['token'] = $data['token'];
                            $res_ver_num[$i]['actid'] = $data['actid'];
                            $res_ver_num[$i]['mark'] = 'RS';
                            $res_ver_num[$i]['res_addtime'] = strtotime(date('Y-m-d H:i:s'));
                            M('Wechatverification_code')->where(array('token'=>$data['token'],'actid'=>$data['actid']))->add($res_ver_num[$i]);
                        }
                        $this->success('生成预留验证码成功！','index.php?g=User&m=WechatConferencemarking&a=verification_code&token='.$data['token'].'&actid='.$data['actid']);
                    }else{
                        $this->error('生成预留验证码失败','index.php?g=User&m=WechatConferencemarking&a=verification_code&token='.$data['token']);
                    }
                }
                
            }
            $count = M('Wechatverification_code')->where(array('token'=>$this->token,'actid'=>$w['actid']))->count();
            $page=new Page($count,15);
            $base_ver_code = M('Wechatverification_code')->where(array('token'=>$this->token,'actid'=>$w['actid'],'mark'=>'BS'))->select();
            $res_ver_code = M('Wechatverification_code')->where(array('token'=>$this->token,'actid'=>$w['actid'],'mark'=>'RS'))->select();
            $this->assign('page',$page->show());
            $this->assign('base_ver_code',$base_ver_code);
            $this->assign('res_ver_code',$res_ver_code);
        }
        $this->display();
    }
    /*
     * About qr code
    */
    public function creatTicket($token, $parament) {
        /*发送数据到微信服务器端并获取数据*/
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
        $result = $this->api_notice_increment($url, $parament);
        $jsonInfo = json_decode($result, true);
        $ticket = $jsonInfo['ticket'];
        /*根据ticket获取图片资源*/
        $url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpInfo = curl_getinfo($ch);
        return array_merge(array('body'=>$package), array('header'=>$httpInfo));
    }
    /*
     * Create qr code
    */
    public function create(){
        $w['token'] = $this->token;
        $w['id'] = $_GET['actid'];
        $data = M('Wechatact')->where($w)->find();
        //echo $data['id'];
        $parament = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 104'.$data['id'].'}}}';
        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        if($api){
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
            $ch = curl_init();
            $header = "Accept-Charset: utf-8";
            curl_setopt($ch, CURLOPT_URL, $url_get);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOBODY, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $package = curl_exec($ch);
            $json = json_decode($package);
            $access_token = $json->access_token;
            $imgSource = $this->creatTicket($access_token, $parament);
        }
        $this->assign('imgUrl', $imgSource['header']['url']);
    }

    public function number(){
        $id = $_REQUEST['actid'];
        $token = $this->token;
        //print_r($token);exit;
        $where = array('id'=>$id,'token'=>$token);
        $data = M('Wechatact')->where($where)->find();

        if(IS_POST){
                $info = array('id'=>$_REQUEST['actid'],'token'=>$token);
                $datas =  M('Wechatact')->where($info)->save($_POST);
                if($datas){
                    $this->success('设置成功！', U(MODULE_NAME . '/index', array('token' => session('token'))));
                }else{
                    $this->error('设置失败！', U(MODULE_NAME . '/number', array('token' => session('token'))));
                }
        }
        $this->assign('ginfo',$data);
        $this->display();
    }


}