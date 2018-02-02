<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-9
 * Time: 下午3:54
 */

class MediasAction extends BaseAction{

    protected $_sTplBaseDir = 'Wap/default/medias';



    public function _initialize(){

        parent::_initialize();
        /*
              * 引入微信js接口
            */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $this->assign('appidInfo',$appdata);
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);
    }

    /*
     * 任务中心
     */
    public function taskcenter(){
        if(isset($_REQUEST['title'])){
            $where['title'] = array('like',array('like','%'.$_POST['title'].'%'));
        }

        if(isset($_REQUEST['cid'])){
            $where['cid'] = $_POST['cid'];
        }

        if(isset($_REQUEST['lid'])){
            $where['lid'] = array('like',array('like','%|'.$_REQUEST['lid'].'|%'));
        }
        //$where['pid'] = array('eq',0);

        $where['status'] = 1;
        $where['token'] = $this->token;

        /*
         * 任务数据
         */

        $gtask = !empty($_GET['task'])?$_GET['task']:'';
        if($gtask == 1){
            $where['dates'] = date('Y-m-d');
            $taskdata = M('Media_task')->where($where)->order('addtime desc')->select();
            foreach($taskdata as $k=>$value){
                $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                $taskdata[$k]['report_times'] = $dianji['report_times'];
            }
        }elseif($gtask==2){
            $where['is_recommend'] = 1;
            $taskdata = M('Media_task')->where($where)->order('addtime desc')->select();
            foreach($taskdata as $k=>$value){
                $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                $taskdata[$k]['report_times'] = $dianji['report_times'];
            }
        }else{
            $taskdata = M('Media_task')->where($where)->order('addtime desc')->select();
            foreach($taskdata as $k=>$value){
                $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                $taskdata[$k]['report_times'] = $dianji['report_times'];
            }
        }

//print_r($taskdata);exit;

        /*
         * 大分类数据
         */
        $bigcate = M('Media_classification')->where(array('token'=>$this->token))->order('id desc')->select();
        foreach($bigcate as $key=>$value){
            $smallcate = M('Media_label')->where(array('cid'=>$value['id'],'token'=>$this->token))->select();
            $bigcate[$key]['son'] = $smallcate;
        }

        /*小分类数据*/
        $labeldata = M('Media_label')->where(array('token'=>$this->token))->order('cid desc')->select();
        //P($labeldata);exit;

        /*
        * 小分类数据
        */
        if(IS_AJAX) {
            $type= $_GET['type'];
            if($type==1){
                $plen = $this->_post('page','intval') * 3;
                $taskdatas = M('Media_task')->where($where)->limit($plen.',3')->select();
                $this->assign('info',$taskdatas);
                $fetchtask = $this->fetch('./tpl/Wap/default/task_jiazai.html');
                echo $this->encode(array(
                    'fetch' => $fetchtask,
                    'status' => 0
                ));
                exit;
            }

            if($_POST['lid']){
                $isCid = !empty($_POST['cid']);
                if($isCid){
                    $varable = array(
                        'token'=>$this->token,
                        'cid'=>$_POST['cid'],
                        'lid'=>array('like',array('like','%|'.$_REQUEST['lid'].'|%'))
                    );
                }else{
                    $varable = array(
                        'token'=>$this->token
                    );
                }

                $gtask = !empty($_GET['task'])?$_GET['task']:'';
                if($gtask == 1){
                    $varable['dates'] = date('Y-m-d');
                    $aTaskdata = M('Media_task')->where($varable)->order('id desc')->select();
                    foreach($aTaskdata as $k=>$value){
                        $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                        $aTaskdata[$k]['report_times'] = $dianji['report_times'];
                    }
                }elseif($gtask==2){
                    $varable['is_recommend'] = 1;
                    $aTaskdata = M('Media_task')->where($varable)->order('id desc')->select();
                    foreach($aTaskdata as $k=>$value){
                        $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                        $aTaskdata[$k]['report_times'] = $dianji['report_times'];
                    }
                }else{
                    $aTaskdata = M('Media_task')->where($varable)->order('id desc')->select();
                    foreach($aTaskdata as $k=>$value){
                        $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                        $aTaskdata[$k]['report_times'] = $dianji['report_times'];
                    }
                }
                //$aTaskdata = M('Media_task')->where($varable)->order('id desc')->select();
                $this->assign('aTask',$aTaskdata);
                $fetchs = $this->fetch('./tpl/Wap/default/medias/taskcontent.html');
                echo $this->encode(array(
                    'coders'=>1,
                    'mgs'=>$fetchs
                ));exit;

            }else{
                if($_POST['cid']){
                    $labeldata = M('Media_label')->where(array('token'=>$this->token,'cid'=>$_POST['cid']))->order('cid desc')->select();
                    $gtask = !empty($_GET['task'])?$_GET['task']:'';
                    if($gtask == 1){
                        $ctaskdata = M('Media_task')->where(array('token'=>$this->token,'cid'=>$_POST['cid'],'dates'=>date('Y-m-d')))->order('id desc')->select();
                        foreach($ctaskdata as $k=>$value){
                            $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                            $ctaskdata[$k]['report_times'] = $dianji['report_times'];
                        }
                    }elseif($gtask==2){
                        $ctaskdata = M('Media_task')->where(array('token'=>$this->token,'cid'=>$_POST['cid'],'is_recommend'=>1))->order('id desc')->select();
                        foreach($ctaskdata as $k=>$value){
                            $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                            $ctaskdata[$k]['report_times'] = $dianji['report_times'];
                        }
                    }else{
                        $ctaskdata = M('Media_task')->where(array('token'=>$this->token,'cid'=>$_POST['cid']))->order('id desc')->select();
                        foreach($ctaskdata as $k=>$value){
                            $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                            $ctaskdata[$k]['report_times'] = $dianji['report_times'];
                        }
                    }
                    $this->assign('bigcate',$bigcate);
                    $this->assign('labeldata',$labeldata);
                    $this->assign('taskdata',$ctaskdata);
                    $fetch = $this->fetch('./tpl/Wap/default/medias/classbelas.html');
                    echo $this->encode(array(
                        'status' => 1,
                        'fetch' => $fetch
                    ));
                    exit;
                }else{
                    $gtask = !empty($_GET['task'])?$_GET['task']:'';
                    $where1['token'] = $this->token;
                    if($gtask == 1){
                        $where1['dates'] = date('Y-m-d');
                        $taskdata = M('Media_task')->where($where1)->order('id desc')->select();
                        foreach($taskdata as $k=>$value){
                            $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                            $taskdata[$k]['report_times'] = $dianji['report_times'];
                        }
                    }elseif($gtask==2){
                        $where1['is_recommend'] = 1;
                        $taskdata = M('Media_task')->where($where1)->order('id desc')->select();
                        foreach($taskdata as $k=>$value){
                            $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                            $taskdata[$k]['report_times'] = $dianji['report_times'];
                        }
                    }else{
                        $taskdata = M('Media_task')->where($where1)->order('id desc')->select();
                        foreach($taskdata as $k=>$value){
                            $dianji = M('Media_user_tasks')->where(array('openid'=>$this->openid,'task_id'=>$value['id']))->find();
                            $taskdata[$k]['report_times'] = $dianji['report_times'];
                        }
                    }
                    $this->assign('aTask',$taskdata);
                    $fetchs = $this->fetch('./tpl/Wap/default/medias/taskcontent.html');
                    echo $this->encode(array(
                        'coders'=>1,
                        'mgs'=>$fetchs
                    ));exit;
                }

            }

        }else{
            $this->assign('taskdata',$taskdata);
        }
        //P($bigcate);exit;
        $this->assign(array(
            'labeldata'=>$labeldata,
            'title'=>$_POST['title'],
            'bigcate'=>$bigcate,
            'gtask' =>$gtask,
            'taskpic'=>M('Imag')->where(array('token'=>$this->token,'app'=>'Media','type'=>'taskpic'))->find()
        ));

        $this->udisplay('taskcenter');
    }



    /*
     * 任务类容展示
     */
    public function taskdetail(){

        $oUserTaskModel = M('Media_user_tasks');//分享记录表
        $oTaskViewModel = M('Media_task_view');//浏览表
        $oUserModel = M('Media_users');   //用户表
        $taskModel = M('Media_task');    //任务表
        $oUserscore = M('Media_user_score'); //个人积分记录表
        $oEnterscore = M('Media_enterprise_score');  //企业积分流水记录表
        $oenterModel = M("Media_enterprise");  //企业消息
        $sFromOpenid = $this->_get('from_openid');
        $iTid = $this->_get('tid');
        $sViewOpenid = $this->openid;
        $oProductmodel = M('Product_new');
        $iQid = $_GET['qid'];

        $where['token'] = $this->token;
        $where['id'] = $_GET['tid'];
        $task = $taskModel->where($where)->find();
        $isready = $oTaskViewModel->where(array('token'=>$this->token,'view_openid'=>$sViewOpenid,'task_id'=>$iTid))->find();

        if(!$isready){
            /*添加浏览记录*/
            $oTaskViewModel->add(array(
                'task_id'=>$iTid,
                'from_openid'=>$sFromOpenid,
                'view_openid'=>$sViewOpenid,
                'token'=>$this->token,
                'add_time'=>date('Y-m-d H:i:s'),
                'date'=>date('Y-m-d')));
            /*是否从别人分享链接进来*/
            if($sFromOpenid){
                /*点击量增加*/
                $oUserTaskModel->where(array('openid'=>$sFromOpenid,'task_id'=>$iTid))->setInc('clicks',1);
                /*添加积分*/
                if($iQid){  //是否由企业发布的任务
                    $oenterModel->where(array('id'=>$iQid))->setDec('score',$task['commission']);
                    $oEnterscore->add(array(
                        'cid'=>$iTid,
                        'qid'=>$iQid,
                        'score'=>$task['commission'],
                        'type'=>0,
                        'token'=>$this->token,
                        'openid'=>$sFromOpenid,
                        'add_time'=>date('Y-m-d H:i:s'),
                    ));
                }
                $oUserModel->where(array('token'=>$this->token,'openid'=>$sFromOpenid))->setInc('money',$task['commission']);
                /*增加一条积分记录*/
                $oUserscore->add(array(
                    'cid'=>$iTid,
                    'openid'=>$sFromOpenid,
                    'token' => $this->token,
                    'gopenid'=>$sViewOpenid,
                    'score'=>$task['commission'],
                    'add_time'=>date('Y-m-d H:i:s'),
                    'type'=>3,
                    'date'=>date('Y-m-d')));
            }
            if($iQid){  //是否由企业发布的任务
                $oenterModel->where(array('id'=>$iQid))->setDec('score',$task['commission']);
                $oEnterscore->add(array(
                    'cid'=>$iTid,
                    'qid'=>$iQid,
                    'score'=>$task['commission'],
                    'type'=>0,
                    'token'=>$this->token,
                    'openid'=>$sViewOpenid,
                    'add_time'=>date('Y-m-d H:i:s'),
                ));
            }
            /*个人阅读添加积分*/
            $oUserModel->where(array('token'=>$this->token,'openid'=>$sViewOpenid))->setInc('money',$task['commission']);
            /*增加一条积分记录*/
            $oUserscore->add(array(
                'cid'=>$iTid,
                'openid'=>$sViewOpenid,
                'token' => $this->token,
                'gopenid'=>'',
                'score'=>$task['commission'],
                'add_time'=>date('Y-m-d H:i:s'),
                'type'=>2,
                'date'=>date('Y-m-d')));


        }

        $task['content'] = html_entity_decode($task['content']);
        $product = explode('|',$task['is_task']);
        array_shift($product);
        array_pop($product);
        foreach($product as $aVar){
            $aProduct[] = $taskModel->where(array('id'=>$aVar))->find();
        }


        $sImageUrl = $this->getCode();
        $this->assign("image", $sImageUrl);//生成二维码图片


        $this->assign('aProduct',$aProduct);
        $this->assign('task',$task);
        $this->udisplay('taskdetail');
    }

    /**
     * @param string $pid
     * @return mixed生成二维码图片
     */
    public function getCode() {
        if($_GET['from_openid']){
            $this->openid = $_GET['from_openid'];
        }
        $userinfo=M("Wxusers")->field("id")->where(array("openid"=>$this->openid))->find();

        $parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 500'.$userinfo['id'].'}}}';

        /*获取access_token*/

        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        //p($api);
        if($api){

            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];


            $json = json_decode(file_get_contents($url_get));
            $access_token = $json->access_token;
            // $access_token = "GVkLr2R7pPpgnmCHovoSkJqYugUzNA1y4crUwpZlenhMk80qRD9ijinh2O8BwL3ACwqoCGxohSlath0OJK5AslJH7dSnNx9foGvJ_UjTEdU";

            $imgSource = $this->creatTicket($access_token, $parament);

        }

        return $imgSource['header']['url'];

    }

    /**
     * @param $token
     * @param $parament
     * @return array   生成二维码
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
     *推荐计数接口
     */
    public function sharecount(){
        $oUserModel = M('Media_users');   //用户表
        $oUserscore = M('Media_user_score'); //个人积分记录表
        $oEnterscore = M('Media_enterprise_score');  //企业积分流水记录表
        $oenterModel = M("Media_enterprise");  //企业消息
        $sToken = $this->token;
        $sOpenid = $this->openid;
        $oUserTask = M('Media_user_tasks');
        $oTaskModel = M('Media_task');
        if($_REQUEST['tid']){
            $_POST['task_id'] = $_REQUEST['tid'];
            $_POST['add_time'] = date('Y-m-d H:i:s',time());
            $_POST['date'] = date('Y-m-d',time());
            $aTask = $oTaskModel->where(array(
                'token'=>$this->token,
                'id'=>$_POST['task_id']
            ))->find();
            $aInfo = $oUserTask->where(array(
                'openid'=>$sOpenid,
                'token'=>$sToken,
                'task_id'=>$_POST['task_id']
            ))->find();

            if($aInfo){
                $_POST['report_times'] = $aInfo['report_times'] + 1;
                $bTask = $oUserTask->where(array(
                    'openid'=>$sOpenid,
                    'token'=>$sToken,
                    'task_id'=>$_POST['task_id']
                ))->save($_POST);
                $bTaskes = $oTaskModel->where(array(
                    'token'=>$this->token,
                    'id'=>$_POST['task_id']
                ))->setInc('number',1);
                if($bTask){
                    echo $this->encode(array('code'=>0,'mgs'=>'分享成功'));exit;
                }else{
                    echo $this->encode(array('code'=>1,'mgs'=>'分享失败'));exit;
                }
            }else{
                $_POST['token'] = $sToken;
                $_POST['openid'] = $sOpenid;
                $_POST['task_name'] = $aTask['title'];
                $_POST['type'] = $aTask['cid'];
                $_POST['report_times'] = 1;
                $btasks = $oUserTask->data($_POST)->add();
                $iQid = $_POST['qid'];
                if($iQid){
                    $oenterModel->where(array('id'=>$iQid))->setDec('score',$aTask['commission']);
                    $oEnterscore->add(array(
                        'cid'=>$_POST['task_id'],
                        'qid'=>$iQid,
                        'score'=>$aTask['commission'],
                        'type'=>0,
                        'token'=>$this->token,
                        'openid'=>$sOpenid,
                        'add_time'=>date('Y-m-d H:i:s'),
                    ));
                }
                $oUserModel->where(array('token'=>$this->token,'openid'=>$sOpenid))->setInc('money',$aTask['commission']);
                /*增加一条积分记录*/
                $oUserscore->add(array(
                    'cid'=>$_POST['task_id'],
                    'openid'=>$sOpenid,
                    'token' => $this->token,
                    'gopenid'=>'',
                    'score'=>$aTask['commission'],
                    'add_time'=>date('Y-m-d H:i:s'),
                    'type'=>1,
                    'date'=>date('Y-m-d')));

                if($btasks){
                    $aNumber['number'] = $aTask['number']+1;
                    $bTaskes = $oTaskModel->where(array(
                        'token'=>$this->token,
                        'id'=>$_POST['task_id']
                    ))->setInc('number',1);
                    echo $this->encode(array('code'=>0,'mgs'=>'分享成功'));exit;
                }else{
                    echo $this->encode(array('code'=>1,'mgs'=>'分享失败'));exit;
                }
            }
        }
    }


    /*
     *消息中心
     */
    public function message(){
        $sToken = $this->token;
        $iType = $_GET['type'];
        $oMessage = M('Media_station');
        $aMessage =$oMessage->where(array(
            'token'=>$sToken,
            'type'=>$iType
        ))->order(
            'info_time desc')->select();
        // print_r($aMessage);exit;
        $this->assign(array(
            'message'=>$aMessage,
            'type' =>$iType
        ));
        //print_r($aMessage);exit;
        $this->udisplay('message');
    }

    /*
     * 消息详情展示页
     */

    public function messagecontent(){
        $sToken = $this->token;
        $oMessage = M('Media_station');
        $aContent = $oMessage->where(array(
            'token'=>$sToken,
            'id' =>$_GET['mid']
        ))->find();
        $this->assign(array(
            'info'=>$aContent
        ));
        $this->udisplay('messagecontent');
    }


    /*
     * 个人资料
     */
    public function myhome(){
        $token = $this->token;
        $openid = $this->openid;
        $oUsersModel = M('Media_users');
        $oSetcenter = M('media_setcenter');
        $aUser = M('Wxuser')->where(array('token'=>$token))->find();

        $aNickuser = M('Wxusers')->where(array(
                'uid'=>$aUser['id'],
                'openid'=>$openid,
                'status'=>1
        ))->find();

        $aUsers = $oUsersModel->where(array(
            'token'=>$token,
            'openid'=>$openid
        ))->find();

        $aSetcenter = $oSetcenter->where(array(
            'token'=>$this->token
        ))->find();

        $iScore = $oSetcenter->where(array(
            'token'=>$this->token
        ))->find();
        if(!$iScore['invite']){
            $iScore['invite'] = 0;
        }
        $aUsererr = M('Wxuser')->where(array('token'=>$this->token))->find();
       
        if($aUsers){
            if(IS_AJAX){

                $_POST['add_time'] = date('Y-m-d H:i:s',time());
                $_POST['token'] = $token;
                $_POST['openid'] = $openid;
                $_POST['date'] = date('Y-m-d',time());
                /*?社交平台*/
                $aUseres = $oUsersModel->where(array(
                    'token'=>$token,
                    'openid'=>$openid
                ))->find();
                if(!$aUseres['phone'] &&!$aUseres['yongjin']&&$aUseres['from_openid']){
                    $_POST['yongjin'] = $aSetcenter['invite'];
                    $saveUsers = $oUsersModel->where(array(
                        'token'=>$token,
                        'openid'=>$openid
                    ))->save($_POST);
                    if($saveUsers){

                        $bUseres = $oUsersModel->where(array(
                            'token'=>$this->token,
                            'openid'=>$aUseres['from_openid']
                        ))->setInc('money',$aSetcenter['invite']);
                        if($bUseres){
                            echo $this->encode(array('code'=>0,'datas'=>'成功！'));exit;
                        }else{
                            echo $this->encode(array('code'=>1,'datas'=>'失败！'));exit;
                        }
                    }else{
                        echo $this->encode(array('code'=>1,'datas'=>'失败！'));exit;
                    }
                }else{
                    $isUser =  $oUsersModel->where(array(
                        'token'=>$token,
                        'openid'=>$openid
                    ))->find();
                    /*if($isUser['passwd']){
                        if($_POST['passwd']){
                            if($isUser['passwd'] !=$_POST['passwd']){
                                echo $this->encode(array('code'=>3,'datas'=>'失败！'));exit;
                            }
                        }else{
                            echo $this->encode(array('code'=>4,'datas'=>'失败！'));exit;
                        }

                    }*/
                    $saveUsers = $oUsersModel->where(array(
                        'token'=>$token,
                        'openid'=>$openid
                    ))->save($_POST);
                    if($saveUsers){
                        echo $this->encode(array('code'=>0,'datas'=>'成功！'));exit;
                    }else{
                        echo $this->encode(array('code'=>1,'datas'=>'失败！'));exit;
                    }
                }
            }else{
                $this->assign(array(
                    'nick' => $aNickuser,
                    'users' => $aUsers
                ));
                /*跳转关注*/
                /* if($_GET['from_openid']){
                     $this->openid = $_GET['from_openid'];
                 }
                 if(!$aNickuser){
                     $url = C('site_url').'index.php?g=Home&m=Nofind&a=isnotsub&token='.$this->token.'&openid='.$this->openid.'&from_openid='.$_GET['from_openid'];

                     $this->redirect($url);
                 }*/
            }

        }else {

            $dopenid = !empty($_GET['from_openid'])?$_GET['from_openid']:'';
            $data['token'] = $this->token;
            $data['openid'] = $this->openid;
            $data['from_openid'] = $dopenid;
            $data['money']=$iScore['invite'];
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['date'] = date('Y-m-d');
            $oUsersModel->add($data);
            M('Media_user_score')->add(array(
                'cid'=>'',
                'openid'=>$this->openid,
                'token' => $this->token,
                'gopenid'=>'',
                'score'=>$iScore['invite'],
                'add_time'=>date('Y-m-d H:i:s'),
                'type'=>5,
                'date'=>date('Y-m-d')));

            $aFinduser = $oUsersModel->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))->find();
            $this->assign(array(
                'nick' => $aNickuser,
                'users' => $aFinduser
            ));
            /*跳转关注*/
            /* if($_GET['from_openid']){
                 $this->openid = $_GET['from_openid'];
             }

             if(!$aNickuser){
                 $url = C('site_url').'index.php?g=Home&m=Nofind&a=isnotsub&token='.$this->token.'&openid='.$this->openid.'&from_openid='.$_GET['from_openid'];
                 $this->redirect($url);
             }*/

        }
        $aUrser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $aScores = M('Usercenter_memberlist')->where(array('uid'=>$aUrser['id'],'openid'=>$this->openid))->find();
        $amyInfo = $oUsersModel->where(array('token'=>$token, 'openid'=>$openid))->find();
        $this->assign('aScores',$aScores);
        $oUsercode = new Code($this->token,'180'.$amyInfo['id']);
        $img = $oUsercode->getLSCode();
        $this->assign('img',$img);
        $this->udisplay('myhome');
    }



    /*
     * 我的收入（原始）
     */
    public function myincome(){
        $iType = $this->_get('iType');
        $oUserTaskModel = M('Media_user_tasks'); //分享任务表
        $oTaskModel = M('Media_task');      //任务详情表
        $oMediaUser = M('Media_users');      //平台用户表
        $oUserscore = M('Media_user_score'); //个人积分记录表
        $oInviterecordModel = M('Media_inviterecord');//邀请积分记录表
        $oEdiaModel = M('Edia_user_commission');  //佣金记录表
        //$onetime = date('Y-m-d'); //一天时间
        $onestime = date('Y-m-d',strtotime('-1 day')); //昨天日期
        $time = strtotime('-2 day', time());
        $beginTime = date('Y-m-d 00:00:00', $time);
        $endTime = date('Y-m-d 23:59:59', time()); /*array('between',array('1','8'));*/
        $threetime = array('between',array($beginTime,$endTime));   //三天时间
        $monthtime = array('like',array('like','%'.date('Y-m').'%'));
        $aMediaUser = $oMediaUser->where(array(    //个人消息
            'token'=>$this->token,
            'openid'=>$this->openid
        ))->find();
        if($iType ==2){//积分收入
            $type = array('in','1,2,3,5');
            $iYeasDayscore = $oUserscore->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>$type,'date'=>$onestime))->sum('score');
            $iOneMediascore = $oUserscore->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>$type,'date'=>date('Y-m-d')))->sum('score');
            $fThreeDayscore = $oUserscore->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>$type,'date'=>$threetime))->sum('score');
            $monthscore = $oUserscore->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>$type,'date'=>$monthtime))->sum('score');
            $this->assign(array(
                'iYeasDayscore'=> intval($iYeasDayscore),
                'iOneMediascore' =>intval($iOneMediascore),
                'fThreeDayscore' =>intval($fThreeDayscore),
                'monthscore' => intval($monthscore),
            ));
            $alist = $oUserscore->where(array('token'=>$this->token,'openid'=>$this->openid))->group('date')->select();
            foreach ($alist as $ks=>$vals) {
                $alist[$ks]['score'] = intval($oUserscore->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>$type,'date'=>$vals['date']))->sum('score'));
                $info = $oUserscore->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>$type,'date'=>$vals['date']))->select();
                foreach($info as $k=>$vales){
                    $clicks = intval(M('Media_task_view')->where(array('token'=>$this->token,'task_id'=>$vales['cid']))->count());
                    $info[$k]['clicks'] = $clicks;
                    $content = $oTaskModel->where(array('id'=>$vales['cid']))->find();
                    $info[$k]['number'] = $content['number'];
                    $info[$k]['key'] = $content['key'];
                    $info[$k]['abstract'] = $content['abstract'];
                    $info[$k]['task_name'] = $content['title'];
                    $info[$k]['commission'] = $content['commission'];
                }
                $alist[$ks]['info'] = $info;
            }


            $this->assign('alist',$alist);


        }elseif($iType ==3){//佣金收入
            $iYeasDaysMoney = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$onestime))->sum('yj');
            $iOneMediaMoney = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>date('Y-m-d')))->sum('yj');
            $ifThreeDayMoney = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$threetime))->sum('yj');
            $monthMoney = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$monthtime))->sum('yj');

            $this->assign(array(
                'iYeasDaysMoney'=>intval($iYeasDaysMoney),
                'iOneMediaMoney'=>intval($iOneMediaMoney),
                'ifThreeDayMoney'=>intval($ifThreeDayMoney),
                'monthMoney'=>intval($monthMoney),
            ));
            $alist = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid))->group('date')->order('id desc')->select();
            foreach($alist as $k=>$val){
                $yongjin = intval($oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$val['date']))->sum('yj'));
                $alist[$k]['yongjin'] = $yongjin;
                $info = $oUserTaskModel->join('tp_media_task ON tp_media_user_tasks.task_id = tp_media_task.id ')->where(array('tp_media_user_tasks.token'=>$this->token,'tp_media_user_tasks.openid'=>$this->openid,'tp_media_user_tasks.date'=>$val['date'],'tp_media_task.pid'=>array('neq',0)))->select();
                $alist[$k]['info'] = $info;
            }
            $this->assign('alist',$alist);

        }
        //收入明细

    //print_r($alist);exit;
        $this->assign('aMediaUser',$aMediaUser);
        $this->assign('iType',$iType);
        $this->udisplay('myincome');

    }

    /*
     * 佣金排行榜(根据的是会员的佣金余额进行的排名)
     * */
    public function ranking(){
        $oMediaUser = M('Media_users');
        $oUser = M('Wxuser');
        $oUseres = M('Wxusers');
        $aUserlist = $oMediaUser->where(array('token'=>$this->token))->order('yongjin desc')->select();
        foreach ($aUserlist as $k=>$val) {
            $aUser = $oUser->where(array('token'=>$this->token))->find();
            $aUseres = $oUseres->where(array('uid'=>$aUser['id'],'openid'=>$aUserlist['openid']))->find();
            $aUserlist[$k]['name'] = $aUseres['nickname'];
            $aUserlist[$k]['headimgurl'] = $aUseres['$aUseres'];
            $aUserlist[$k]['number'] = intval(M('Media_inviterecord')->where(array('token'=>$this->token,'openid'=>$this->openid))->count());
            $aUserlist[$k]['dates'] = date("Y/m/d",strtotime($val['add_time']));
            $aUserlist[$k]['ranks'] = intval(M('Media_users')->where(array('token'=>$this->token,'yongjin'=>array('gt', $val['yongjin'])))->count())+1;
        }
        $aYongjin = $oMediaUser->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $irank = intval($oMediaUser->where(array('token'=>$this->token,'yongjin'=>array('gt',$aYongjin['yongjin'])))->count())+1;

        $this->assign(array(
            'aUserlist'=>$aUserlist,
            'aYongjin'=>$aYongjin,
            'irank'=>$irank
        ));
        $this->display('ranking');
    }

    /*
     * 提现验证页面
     * */
    public function withdrawals(){
        $oUrseModel = M('Media_users');
        $oSetcenter = M('Media_setcenter');
        if(IS_AJAX){
            $aUser = $oUrseModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();

            if($aUser['bank_name'] && $aUser['bank_card'] && $aUser['alipay_account'] && $aUser['phone'] ){
                echo $this->encode(array('codes'=>1,'urles'=>'index.php?g=Wap&m=Medias&a=withdrawals&token='.$this->token.'&openid='.$this->openid));exit;
            }else{
                echo $this->encode(array('codes'=>0,'msg'=>'您的资料还未完善，请至少填写个人密码，开户银行，开户人姓名，银行卡号和联系电话','urles'=>'index.php?g=Wap&m=Medias&a=myhome&token='.$this->token.'&openid='.$this->openid.'&type=1'));exit;
            }
        }
        $info = $oUrseModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign(array(
            'info'=>$info,
            'minmoney'=>$oSetcenter->where(array('token'=>$this->token))->find()
        ));
        $this->udisplay('withdrawals');
    }

    /*
     * 提现记录
     * */
    public function withdrawalsinfo(){
        $oModel = M('Media_withdrawals');
        $oUserModel = M('Media_users');
        if(IS_AJAX){
            if($info = $oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find()){
                if($info['yongjin']>$_POST['money']){
                    if($oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->setDec('yongjin',$_POST['money'])){
                        $_POST['add_time'] = date('Y-m-d H:i:s');
                        $_POST['status'] = 1;
                        $_POST['token'] = $this->token;
                        $_POST['openid'] = $this->openid;
                        $_POST['number'] = $_POST['money'];
                        // print_r($_POST['money']);exit;
                        if($oModel->add($_POST)){
                            echo $this->encode(array('codes'=>1,
                                'msg'=>'提现申请成功',
                                'urles'=>'index.php?g=Wap&m=Medias&a=withdrawalsinfo&token='.$this->token.'&openid='.$this->openid));exit;
                        }else{
                            echo $this->encode(array('codes'=>0,
                                'msg'=>'系统繁忙，请稍后',
                                'urles'=>'index.php?g=Wap&m=Medias&a=withdrawals&token='.$this->token.'&openid='.$this->openid));exit;
                        }
                    }
                }else{
                    echo $this->encode(array('codes'=>3,
                        'msg'=>'您的余额不足',
                        'urles'=>'index.php?g=Wap&m=Medias&a=withdrawals&token='.$this->token.'&openid='.$this->openid));exit;
                }

            }
        }
        $this->assign(array(
            'list'=>$oModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->select()
        ));
        $this->udisplay('withdrawalsinfo');
    }

    /*
     * 我的小伙伴
     */
    public function mybrother(){
        $oUsersModel = M('Media_users');
        $oWxuser = M('Wxuser');
        $oWxusers = M('Wxusers');

        $set=M('Product_setting_new')->field('one,two,three')->where(array('token'=>$this->token))->find();
        $iOne = $iTwo = $iThree = 0;
        $aOne = $aTwo = $aThree = array();
        if($set['one']>0){
            $list=M('Media_users')
                ->field('nickname,openid,date,qq,phone')
                ->where(array(
                    'from_openid'=>$this->openid,
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
                    ->field('nickname,openid,date,qq,phone')
                    ->where(array(
                        'from_openid'=>$v['openid'],
                        'token'=>$this->token,
                        'status'=>1,
                       /* 'is_buy' => 1*/
                    ))->select();
                foreach($list1 as $v){
                    $iTwo++;
                    $aTwo[] = $v;
                    array_push($list,$v);
                }
                if($set['one']>0&&$set['two']>0&&$set['three']>0){
                    //    $list1=M('Media_users')->field("tp_media_users.nickname,tp_media_users.openid,a.nickname as nickname2,a.openid as openid2")->join("join tp_media_users as a on tp_media_users.openid=a.from_openid")->where(array('tp_media_users.from_openid'=>$this->openid))->select();
                    $list2='';
                    foreach($list1 as $k=>$v){
                        $list2=M('Media_users')
                            ->field('nickname,openid,date,qq,phone')
                            ->where(array(
                                'from_openid'=>$v['openid'],
                                'token'=>$this->token,
                                'status'=>1,
                               /* 'is_buy' => 1*/
                            ))->select();
                        foreach($list2 as $v){
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
            $aUser = $oWxuser->where(array('token'=>$this->token))->find();
            $aUsers = $oWxusers->where(array('uid'=>$aUser['id'],'openid'=>$val['openid']))->find();
            $alist[$k]['openid'] = $aUsers['nickname'];
        }
       // P($alist);exit;
        $aUser = $oUsersModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find(); //总佣金
        $mybrother = $iOne+$iTwo+$iThree;
        $aTask = intval(M('Media_user_tasks')->where(array('token'=>$this->token,'openid'=>$this->openid))->count());
        $this->assign(array(
            'info'=>$alist,
            'mybrother'=>$mybrother,
            'aTask'=>$aTask,
            'aUser'=>$aUser,
            'tuanpic'=>$a=M('Imag')->where(array('token'=>$this->token,'app'=>'Media','type'=>'tuanpic'))->find()
        ));

        $this->udisplay('mybrother');

    }


    /*
     * 营销活动
     * */
    public function marketing(){
        $markmodel = M('Media_marketingactivity');
        $market = $markmodel->where(array('token'=>$this->token))->order('addtime desc')->select();
        $this->assign('market',$market);
        $this->udisplay('marketing');
    }

    /*
     * 营销活动内容
     * */
    public function marketingcon(){
        $mid = $_GET['mid'];
        $markmodel = M('Media_marketingactivity');
        $actormodel = M('Media_marketingactor');
        $list = $markmodel->where(array('token'=>$this->token,'id'=>$mid))->find();
        $list['content'] = htmlspecialchars_decode($list['content'],ENT_QUOTES);
        $this->assign('list',$list);
        $count = $actormodel->where(array('token'=>$this->token,'mid'=>$mid))->count();
        $counts = $list['number'] - $count;
        $countes = $actormodel->where(array('token'=>$this->token,'mid'=>$mid))->order('addtime desc')->select();
        $this->assign('countes',$countes);
        $this->assign('counts',$counts);
        $this->udisplay('marketingcon');
    }

    /*
     * 营销活动参与者信息
     * */
    public function actor(){
        $mid = $_GET['mid'];

        $actormodel = M('Media_marketingactor');
        $actors = $actormodel->where(array('token'=>$this->token,'openid'=>$this->openid,'mid'=>$mid))->find();
        if(IS_AJAX){

            $variable['id'] = $_POST['id']?$_POST['id']:'';
            $variable['aname'] = $_POST['aname'];
            $variable['phone'] = $_POST['phone'];
            $variable['address'] = $_POST['address'];
            $variable['addtime'] = time();
            $variable['content'] = $_POST['content']?$_POST['content']:'';
            $variable['token'] = $this->token;
            $variable['openid'] = $this->openid;
            $variable['mid'] = $mid;

            if(!$actors){
                $addactor = $actormodel->data($variable)->add();
                if($addactor){
                    echo $this->encode(array('code'=>1,'msg'=>'提交成功！','url'=>'index.php?g=Wap&m=Media&a=marketing&token='.$this->token.'&openid='.$this->openid));exit;
                }else{
                    echo $this->encode(array('code'=>2,'msg'=>'提交失败，请重新提交'));exit;
                }
            }else{
                echo $this->encode(array('code'=>0,'msg'=>'你已经填写过，不需提交'));exit;
            }
        }else{
            if($actors){
                $this->assign('actors',$actors);
                $this->display();
            }else{
                $nuser = M('Wxuser')->where(array('token'=>$this->token))->find();
                $nusers = M('Wxusers')->where(array('uid'=>$nuser['id'],'openid'=>$this->openid))->find();
                $this->assign('nusers',$nusers);
                $this->udisplay('actor');
            }
        }
    }
    /*个人中心*/
    public function myCenter(){
        /*个人资料*/
        $aUser = M('Media_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $iDay = (strtotime(date('Y-m-d')) -strtotime($aUser['date']))/(3600*24);
        /*任务总量*/
        $itask = M('Media_user_tasks')->where(array('token'=>$this->token,'openid'=>$this->openid))->count();//任务总量
        $iclick =M('Media_user_tasks')->where(array('token'=>$this->token,'openid'=>$this->openid))->sum('clicks');//阅读总量
        $iDeal = M('Edia_user_commission')->where(array('token'=>$this->token,'g_openid'=>$this->openid))->count();//成交量

        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $wxusers = M('Wxusers')->where(array('uid'=>$wxuser['id'],'openid'=>$this->openid))->find();
        #统计信息个数
        $iInfo =M('Media_station')->where(array('token'=>$this->token,'type'=>1))->count(); //系统消息
       // $iInfos = M('Media_station')->where(array('token'=>$this->token,'type'=>2))->count(); //任务消息
        $this->assign(array(
            'itask'=>$itask,
            'iclick'=>intval($iclick),
            'iDeal' =>$iDeal,
            'iInfo'=>$iInfo,
            'aWxuser'=>$wxusers,
            //'iInfos'=>$iInfos,
            'aUser'=>$aUser,
            'day'=>$iDay,
            'homepic'=>M('Imag')->where(array('token'=>$this->token,'app'=>'Media','type'=>'homepic'))->find()
        ));
        $this->udisplay('myCenter');
    }


}
