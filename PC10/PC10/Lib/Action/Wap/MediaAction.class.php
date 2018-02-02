<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-9
 * Time: 下午3:54
 */

class MediaAction extends BaseAction{

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
        $oUserModel =M("Media_users");
        $oRecordMOdel = M('Media_record');
        $oSetcenter = M('Media_setcenter');
        $oInviterecordModel = M('Media_inviterecord');
        $oSettingModel = M('Product_setting_new');
        $isUser = $oUserModel->where(array(
            'token'=>$this->token,
            'openid'=>$this->openid
        ))->find();
	    $isSet = $oSettingModel->where(array('token'=>$this->token))->find();
        $iScore = $oSetcenter->where(array(
            'token'=>$this->token
        ))->find();
	if(!$iScore['invite']){
	    $iScore['invite'] = 0;
	}
        $aUsererr = M('Wxuser')->where(array('token'=>$this->token))->find();
	$aUsererrs = M('Wxusers')->where(array('uid'=>$aUsererr['id'],'openid'=>$this->openid))->find();
	
        if($aUsererrs['status'] != 1){
	if($_GET['from_openid']){
        	$this->openid = $_GET['from_openid'];
        }
            $url = C('site_url').'index.php?g=Home&m=Nofind&a=isnotsub&token='.$this->token.'&openid='.$this->openid.'&from_openid='.$_GET['from_openid'];

	            $this->redirect($url);
        }
        

        if(!$isUser){
            $dopenid = !empty($_GET['from_openid'])?$_GET['from_openid']:'';
            $data['token'] = $this->token;
            $data['openid'] = $this->openid;
            $data['from_openid'] = $dopenid;
            $data['money']=$iScore['invite'];
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['date'] = date('Y-m-d');
            if($isSet['get_distribution'] = 2){
                $data['type'] = 1;
            }else{
                $data['type'] = 0;
            }

            $aInvites = array(
                'date'=>date('Y-m-d'),
                'add_time'=>date('Y-m-d H:i:s'),
                'token'=>$this->token,
                'give_openid'=>$this->openid,

            );
            if($oUserModel->add($data)){//自己 （零级）
                $aData['score'] = $iScore['invite'];
                $aData['type'] = 1;
                $aData['openid'] = $this->openid;
                $aData['token'] = $this->token;
                $aData['add_time'] = date('Y-m-d H:i:s');
                if($oRecordMOdel->add($aData)){
                    if(!M('Usercenter_memberlist')->where(array('uid'=>$aUsererr['id'],'openid'=>$this->openid))->find()){
                       $usercenter_model = M('Usercenter_set');
                       $usercenterdata = $usercenter_model->field('is_openphone,u_prefix')->where(array('token'=>$this->token))->find();
                       $prefix = 'WP';
                       if($usercenterdata){
                              $prefix = $usercenterdata['u_prefix'];
                           }
                        $sn = $prefix.$aUsererr['id'].date("Ymd",time()).rand(100,999);
                       M('Usercenter_memberlist')->add(array(
                        'uid'=>$aUsererr['id'],
                        'openid'=>$this->openid,
                        'score'=>0,
                        'money'=>0,
                        'member_sn'=>$sn
                       ));
		            }
                    $firstScoreRecored=array(
		                'token'=>$this->token,
                        'openid'=>$this->openid,
                        'type'=>10,
                        'score'=>$iScore['invite'],
                        'add_time'=>time()
                    );

                    $aInvites['openid'] = $this->openid;
                    $myUser = M('Wxusers')->where(array('uid'=>$aUsererr['id'],'openid'=>$aInvites['openid']))->find();
                    $myUsers = $oUserModel->where(array('token'=>$this->token,'openid'=>$aInvites['openid']))->find();
                    $aInvites['type'] = 0;   //表示自己
                    $aInvites['headpic'] = $myUser['headimgurl'];
                    $aInvites['nickname'] = $myUser['nickname'];
                    $aInvites['name'] = $myUsers['nickname'];
                    $aInvites['score']=$iScore['invite'];
                    $oInviterecordModel->data($aInvites)->add();//添加邀请的一条记录
                    msg($token=$this->token,$openid=$this->openid,$content="欢迎您成为我们平台的一员，系统奖励积分".$iScore['invite']."分，加油哦！");


                    M('Usercenter_score_record')->add($firstScoreRecored);
		            M('Usercenter_memberlist')->where(array('uid'=>$aUsererr['id'],'openid'=>$this->openid))->setInc('score',$iScore['invite']);
                    if(!empty($dopenid)){   //判断是否有上级（一级）
                        if($oUserModel->where(array('token'=>$this->token,'openid'=>$dopenid))->setInc('money',$iScore['redfirst'])){

                            $aInvites['openid'] = $dopenid;
			    
                            $myUsera = M('Wxusers')->where($a=array('uid'=>$aUsererr['id'],'openid'=>$this->openid))->find();
			    
                            $myUsersa = $oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
                            $aInvites['type'] = 1;   //表示上一级
                            $aInvites['headpic'] = $myUsera['headimgurl'];
                            $aInvites['nickname'] = $myUsera['nickname'];
                            $aInvites['name'] = $myUsersa['nickname'];
                            $aInvites['score']=$iScore['redfirst'];
			    //print_r($aInvites);
                            $oInviterecordModel->data($aInvites)->add();//添加邀请的一条记录
                            msg($token=$this->token,$openid=$dopenid,$content="恭喜您！".$myUsera['nickname']."成为你的一级伙伴，系统奖励积分".$iScore['redfirst']."分，加油哦！");

                            $aData1['score'] = $iScore['redfirst'];
                            $aData1['type'] = 1;
                            $aData1['openid'] = $dopenid;
                            $aData1['token'] = $this->token;
                            $aData1['add_time'] = date('Y-m-d H:i:s');
                            if($oRecordMOdel->add($aData1)){
			    
                            M('Usercenter_memberlist')->where(array('uid'=>$aUsererr['id'],'openid'=>$dopenid))->setInc('score',$iScore['redfirst']);
                            M('Usercenter_score_record')->add(array('token'=>$this->token,
                                    'openid'=>$dopenid,
                                    'type'=>10,
                                    'score'=>$iScore['redfirst'],
                                    'add_time'=>time()
                            ));
                                $aIsval = $oUserModel->where(array('token'=>$this->token,'openid'=>$dopenid))->find();

				                if($aIsval['from_openid']){//判断是否有上上级（二级）
                                    if($oUserModel->where(array(
                                        'token'=>$this->token,
                                        'openid'=>$aIsval['from_openid']))->setInc('money',$iScore['redsecond'])){

                                        $aInvites['openid'] = $aIsval['from_openid'];
                                        $myUserb = M('Wxusers')->where(array('uid'=>$aUsererr['id'],'openid'=>$this->openid))->find();
                                        $myUsersb = $oUserModel->where(array('token'=>$this->token,'openid'=>$aInvites['openid']))->find();
                                        $aInvites['type'] = 2;   //表示上二级
                                        $aInvites['headpic'] = $myUserb['headimgurl'];
                                        $aInvites['nickname'] = $myUserb['nickname'];
                                        $aInvites['name'] = $myUsersb['nickname'];
                                        $aInvites['score']=$iScore['redsecond'];
                                        $oInviterecordModel->data($aInvites)->add();//添加邀请的一条记录
                                        msg($token=$this->token,$openid=$aIsval['from_openid'],$content="恭喜您！".$myUserb['nickname']."成为你的一级伙伴，系统奖励积分".$iScore['redsecond']."分，加油哦！");

                                        $aData2['score'] = $iScore['redsecond'];
                                        $aData2['type'] = 1;
                                        $aData2['openid'] = $aIsval['from_openid'];
                                        $aData2['token'] = $this->token;
                                        $aData2['add_time'] = date('Y-m-d H:i:s');
                                        M('Usercenter_memberlist')->where(array('uid'=>$aUsererr['id'],'openid'=>$aIsval['from_openid']))->setInc('score',$iScore['redsecond']);
                                        M('Usercenter_score_record')->add(array('token'=>$this->token,
                                            'openid'=>$aIsval['from_openid'],
                                            'type'=>10,
                                            'score'=>$iScore['redsecond'],
                                            'add_time'=>time()
                                        ));
                                        if($oRecordMOdel->add($aData2)){
                                            $aIsvals = $oUserModel->where(array('token'=>$this->token,'openid'=>$aIsval['from_openid']))->find();
                                            if($aIsvals['from_openid']){ //判断是否有上上上级（三级）
                                                if($oUserModel->where(array(
                                                    'token'=>$this->token,
                                                    'openid'=>$aIsvals['from_openid']))->setInc('money',$iScore['redThere'])){

                                                    $aInvites['openid'] = $aIsvals['from_openid'];
                                                    $myUserc = M('Wxusers')->where(array('uid'=>$aUsererr['id'],'openid'=>$this->openid))->find();
                                                    $myUsersc = $oUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
                                                    $aInvites['type'] = 3;   //表示上三级
                                                    $aInvites['headpic'] = $myUserc['headimgurl'];
                                                    $aInvites['nickname'] = $myUserc['nickname'];
                                                    $aInvites['name'] = $myUsersc['nickname'];
                                                    $aInvites['score']=$iScore['redThere'];
                                                    $oInviterecordModel->data($aInvites)->add();//添加邀请的一条记录
                                                    msg($token=$this->token,$openid=$aIsvals['from_openid'],$content="恭喜您！".$myUserc['nickname']."成为你的一级伙伴，系统奖励积分".$iScore['redThere']."分，加油哦！");

                                                    $aData3['score'] = $iScore['redThere'];
                                                    $aData3['type'] = 1;
                                                    $aData3['openid'] = $aIsvals['from_openid'];
                                                    $aData3['token'] = $this->token;
                                                    M('Usercenter_memberlist')->where(array('uid'=>$aUsererr['id'],'openid'=>$aIsvals['from_openid']))->setInc('score',$iScore['redThere']);
                                                    M('Usercenter_score_record')->add(array('token'=>$this->token,
                                                        'openid'=>$aIsvals['from_openid'],
                                                        'type'=>10,
                                                        'score'=>$iScore['redThere'],
                                                        'add_time'=>time()
                                                    ));
                                                    if($oRecordMOdel->add($aData3)){
                                                        echo $this->encode(array('code'=>0,'mgs'=>'恭喜您成为我们的一员！'));exit;
                                                    }
                                                }
                                            }

                                        }
                                    }
                                }
                            }
                            
                        }
                    }
                }
            }
        }
    }

   public function udisplay($action=''){
        //$sTokens = '5d8a87bab30de695954b17fc835b9d12';
       $sTokens = $_GET['token'];
       $action = ACTION_NAME;//获取当前操作名称
        if($sTokens =='8a71b21a11dd5212bd74cee41dafab64'){
            $this->display('theme1:Media:'.$action);
        }else{
            $this->display();
        }
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
        $where['pid'] = array('eq',0);

        $where['status'] = 1;
        $where['token'] = $this->token;

        /*
         * 任务数据
         */
        if($this->token=='8a71b21a11dd5212bd74cee41dafab64'){
            $gtask = !empty($_GET['task'])?$_GET['task']:'';
            //echo $gtask;//exit;
            if($gtask == 1){
                $where['date'] = date('Y-m-d');
                $taskdata = M('Media_task')->where($where)->order('addtime desc')->select();
            }elseif($gtask==2){
                $where['is_recommend'] = 1;
                $taskdata = M('Media_task')->where($where)->order('addtime desc')->select();
            }else{
                $taskdata = M('Media_task')->where($where)->order('addtime desc')->select();
            }
        }else{
            $count = M('Media_task')->where($where)->count();
            $Page = new Page($count,10);
            $show = $Page->show();
            $taskdata = M('Media_task')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('addtime desc')->select();
        }

       // print_r($taskdata);exit;

        /*
         * 大分类数据
         */
        $bigcate = M('Media_classification')->where(array('token'=>$this->token))->order('id desc')->select();
        foreach($bigcate as $key=>$value){
            $smallcate = M('Media_label')->where(array('cid'=>$value['id'],'token'=>$this->token))->select();
            $bigcate[$key]['son'] = $smallcate;
        }

        /*小分类数据*/
        $labeldata = M('Media_label')->where(array('token'=>$this->token))->order('cid desc')->group('lname')->select();

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
                //echo $fetchtask; exit;
                echo $this->encode(array(
                    'fetch' => $fetchtask,
                    'status' => 0
                ));
                exit;
            }
            if($this->token=='8a71b21a11dd5212bd74cee41dafab64'){
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
                        $varable['date'] = date('Y-m-d');
                        $aTaskdata = M('Media_task')->where($varable)->order('id desc')->select();
                    }elseif($gtask==2){
                        $varable['is_recommend'] = 1;
                        $aTaskdata = M('Media_task')->where($varable)->order('id desc')->select();
                    }else{
                        $aTaskdata = M('Media_task')->where($varable)->order('id desc')->select();
                    }
                    //$aTaskdata = M('Media_task')->where($varable)->order('id desc')->select();
                    $this->assign('aTask',$aTaskdata);
                    $fetchs = $this->fetch('./tpl/Wap/theme1/taskcontent.html');
                    echo $this->encode(array(
                        'coders'=>1,
                        'mgs'=>$fetchs
                    ));exit;

                }else{
                    if($_POST['cid']){
                        $labeldata = M('Media_label')->where(array('token'=>$this->token,'cid'=>$_POST['cid']))->order('cid desc')->select();
                        $gtask = !empty($_GET['task'])?$_GET['task']:'';
                        if($gtask == 1){
                            $ctaskdata = M('Media_task')->where(array('token'=>$this->token,'cid'=>$_POST['cid'],'date'=>date('Y-m-d')))->order('id desc')->select();
                        }elseif($gtask==2){
                            $ctaskdata = M('Media_task')->where(array('token'=>$this->token,'cid'=>$_POST['cid'],'is_recommend'=>1))->order('id desc')->select();
                        }else{
                            $ctaskdata = M('Media_task')->where(array('token'=>$this->token,'cid'=>$_POST['cid']))->order('id desc')->select();
                        }
                        //$ctaskdata = M('Media_task')->where(array('token'=>$this->token,'cid'=>$_POST['cid']))->order('id desc')->select();
                        $this->assign('labeldata',$labeldata);
                        $this->assign('taskdata',$ctaskdata);
                        $fetch = $this->fetch('./tpl/Wap/theme1/classbelas.html');
                        echo $this->encode(array(
                            'status' => 1,
                            'fetch' => $fetch
                        ));
                        exit;
                    }else{
                        /*echo 1234;exit;*/
                        $gtask = !empty($_GET['task'])?$_GET['task']:'';
                        $where1['token'] = $this->token;
                        //echo $gtask;//exit;
                        if($gtask == 1){
                            $where1['date'] = date('Y-m-d');
                            $taskdata = M('Media_task')->where($where1)->order('id desc')->select();
                        }elseif($gtask==2){
                            $where1['is_recommend'] = 1;
                            $taskdata = M('Media_task')->where($where1)->order('id desc')->select();
                        }else{
                            $taskdata = M('Media_task')->where($where1)->order('id desc')->select();
                        }
                        $this->assign('aTask',$taskdata);
                        $fetchs = $this->fetch('./tpl/Wap/theme1/taskcontent.html');
                        echo $this->encode(array(
                            'coders'=>1,
                            'mgs'=>$fetchs
                        ));exit;
                    }

                }
            }else{
                echo $this->encode(array('code'=>0,'datas'=>$taskdata));exit;
            }

        }else{
            $this->assign('taskdata',$taskdata);
        }
        $this->assign(array(
            'page'=>$show,
            'labeldata'=>$labeldata,
            'title'=>$_POST['title'],
            'bigcate'=>$bigcate,
            'gtask' =>$gtask,
            'taskpic'=>M('Imag')->where(array('token'=>$this->token,'app'=>'Media','type'=>'taskpic'))->find()
        ));

        $this->udisplay();
    }




    /*
     * 任务类容展示
     */
    public function taskdetail(){
        $oUserTaskModel = M('Media_user_tasks');
        $oTaskViewModel = M('Media_task_view');
        $oUserModel = M('Media_users');
        $taskModel = M('Media_task');
        $sFromOpenid = $this->_get('from_openid');
        $iTid = $this->_get('tid');
        $sViewOpenid = $this->openid;
        $oProductmodel = M('Product_new');
        if($sFromOpenid){
	
            //存在，即用户用分享链接进入，记录浏览记录
            $aIsExsist = $oTaskViewModel->where(array(
                'from_openid'=>$sFromOpenid,
                'task_id'=>$iTid,
                'view_openid'=>$$sViewOpenid
            ))->find();
	    
            if(!$aIsExsist){
                //新增记录，更新任务浏览次数
                $aData = array(
                    'from_openid'=>$sFromOpenid,
                    'task_id'=>$iTid,
                    'view_openid'=>$sViewOpenid,
                    'token'=>$this->token,
                    'add_time'=>date('Y-m-d H:i:s',time()),
                    'date'=>date('Y-m-d',time())
                );
	
                $bIsSet = $oTaskViewModel->data($aData)->add();
                if($bIsSet){
		
                    $aUserTask = $oUserTaskModel->where(array(
                        'openid'=>$sFromOpenid,
                        'token'=>$this->token,
                        'task-id'=> $iTid
                    ))->find();
                    /*用户余额变更*/
                    $aUser = $oUserModel->where(array(
                        'token'=>$this->token,
                        'openid'=> $sFromOpenid
                    ))->find();
                    $aTask = $taskModel->where(array(
                        'id' =>$iTid,
                        'token'=>$this->token
                    ))->find();
                    $iMoney['money'] = $aUser['money'] + $aTask['commission'];
                    $bSaveUser = $oUserModel->where(array(
                        'token'=>$this->token,
                        'openid'=> $sFromOpenid
                    ))->save($iMoney);
                    $aWhere = array(
                        'clicks'=> $aUserTask['clicks']+1
                    );
                    $bUerTask = $oUserTaskModel->where(array(
                        'openid'=>$sFromOpenid,
                        'token'=>$this->token,
                        'task-id'=> $iTid
                    ))->save($aWhere);
                }

            }
        }
        $where['token'] = $this->token;
        $where['id'] = $_GET['tid'];
        $task = $taskModel->where($where)->find();
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
        $this->udisplay();
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
        $sToken = $this->token;
        $sOpenid = $this->openid;
        $oUserTask = M('Media_user_tasks');
        $oTaskModel = M('Media_task');
        if(IS_AJAX){
            $_POST['task_id'] = $_POST['tid'];
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
                if($btasks){
                    $aNumber['number'] = $aTask['number']+1;
                    $bTaskes = $oTaskModel->where(array(
                        'token'=>$this->token,
                        'id'=>$_POST['task_id']
                    ))->save($aNumber);
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
        $this->udisplay();
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
        $this->udisplay();
    }


    /*
     * 个人资料
     */
    public function myhome(){
        $token = $this->token;
        $openid = $this->openid;
        $oUsersModel = M('Media_users');
        $oSetcenter = M('media_setcenter');
        $aUser = M('Wxuser')->
                where(array(
                'token'=>$token
                ))->find();

        $aNickuser = M('Wxusers')->
                    where(array(
                        'uid'=>$aUser['id'],
                        'openid'=>$openid,
			'status'=>1
                    ))->find();
       // print_r($aNickuser);exit;
        $aUsers = $oUsersModel->where(array(
            'token'=>$token,
            'openid'=>$openid
            ))->find();

        $aSetcenter = $oSetcenter->where(array(
            'token'=>$this->token
        ))->find();
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
                   /* if($isUser['passwd']){
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

            $sIsFromOpenid = isset($_GET['from_openid'])?$_GET['from_openid']:'';
            $aData['token'] = $token;
            $aData['openid'] = $openid;
            $aData['from_openid'] = $sIsFromOpenid;
            $aData['date'] = date('Y-m-d',time());
            $aData['add_time'] = date('Y-m-d H:i:s',time());

            $bAddusers = $oUsersModel->data($aData)->add();
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
        $this->assign('aScores',$aScores);
        $this->udisplay();
    }

    /*
     * 我的收入（李总）
     * */
    public function myincomes(){
        $iType = $this->_get('iType');
        $oUserTaskModel = M('Media_user_tasks');
        $oTaskModel = M('Media_task');
        $oMediaUser = M('Media_users');
        $oSetcenter = M('Media_setcenter');
        $oInviterecordModel = M('Media_inviterecord');//邀请积分记录表
        $oEdiaModel = M('Edia_user_commission');  //佣金记录表
        //$onetime = date('Y-m-d'); //一天时间
        $time = strtotime('-2 day', time());
        $beginTime = date('Y-m-d 00:00:00', $time);
        $endTime = date('Y-m-d 23:59:59', time()); /*array('between',array('1','8'));*/
        $threetime = array('between',array($beginTime,$endTime));   //三天时间
        $monthtime = array('like',array('like','%'.date('Y-m').'%'));
        /*个人详请*/
        $aMediaUser = $oMediaUser->where(array(
            'token'=>$this->token,
            'openid'=>$this->openid
        ))->find();
        if($iType == 2){
            /*M('Media_user_tasks')->where(array('token'=>$this->token,'openid'=>$this->openid))->sum('clicks');  收益统计*/
            $iOneMediascore = $oInviterecordModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>date('Y-m-d')))->sum('score');
            $fThreeDayscore = $oInviterecordModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$threetime))->sum('score');
            $monthscore = $oInviterecordModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$monthtime))->sum('score');
            /*明细*/
            $aInvitere = $oInviterecordModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('id desc')->select();
            /*邀请积分列表*/
            $aInvlist = $oInviterecordModel->where(array('token'=>$this->token,'openid'=>$this->openid))->group('date')->order('id desc')->select();
            foreach($aInvlist as $k=>$val){
                $iInvitereScore = $oInviterecordModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$val['date']))->sum('score');
                $aInvlist[$k]['ascore'] = $iInvitereScore;
            }
            /*
             * 邀请总积分
             * */
            $iSumscore = $oInviterecordModel->where(array('token'=>$this->token,'openid'=>$this->openid))->sum('score');

            /*
             * 可兑换积分
             * */
            $aUrser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $aScores = M('Usercenter_memberlist')->where(array('uid'=>$aUrser['id'],'openid'=>$this->openid))->find();
            $this->assign(array(
                'iOneMediascore' => $iOneMediascore,
                'fThreeDayscore' => $fThreeDayscore,
                'monthscore' => $monthscore,
                'aInvitere' => $aInvitere,
                'aInvlist' => $aInvlist,
                'iSumscore'=> $iSumscore,
                'aScores'=> $aScores
            ));
        }elseif($iType == 3){
            /*佣金统计*/
            $iOneMediaMoney = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>date('Y-m-d')))->sum('yj');
            $ifThreeDayMoney = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$threetime))->sum('yj');
            $monthMoney = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>$monthtime))->sum('yj');

           /*任务明细*/
            $ataskList = $oUserTaskModel->where(array('token'=>$this->token,'openid'=>$this->openid))->select();
            foreach($ataskList as $kv=>$values){
                $ataskvas = $oTaskModel->where(array('id'=>$values['task_id']))->find();
                $ataskList[$kv]['pic'] = $ataskvas['pic'];
            }
            /*分销订单*/
            $afenxiaoList = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('id desc')->select();
            foreach($afenxiaoList as $ks=>$vals){
                 $aProduct = M('Product_new')->where(array('id'=>$vals['productid']))->find();   //name  商品名
                $aOrder = M('Product_cart_new')->where(array('orderid'=>$vals['orderid']))->find();  //price  订单额
                $afenxiaoList[$ks]['name']=$aProduct['name'];  //商品名
                $afenxiaoList[$ks]['price'] = $aOrder['price'];  //订单额
            }
            /*佣金总额*/
            $iYongjinAll = $oEdiaModel->where(array('token'=>$this->token,'openid'=>$this->openid))->sum('yj');


            $this->assign(array(
                'iOneMediaMoney'=>$iOneMediaMoney,
                'ifThreeDayMoney'=>$ifThreeDayMoney,
                'monthMoney'=>$monthMoney,
                'ataskList'=>$ataskList,
                'afenxiaoList'=>$afenxiaoList,
                'aMediaUser'=>$aMediaUser,
                'iYongjinAll'=>$iYongjinAll

            ));
        }
        $aProduct = M('Product_cart_new')->where(array('token'=>$this->token,'wecha_id'=>$this->openid))->find();
        if($aMediaUser['type'] == 0){
            if($aProduct){
                $isSaveuser = $oMediaUser->where(array(
                    'token'=>$this->token,
                    'openid'=>$this->openid
                ))->save(array('type'=>1));
                if($isSaveuser){
                    $aMediaUser = $oMediaUser->where(array(
                        'token'=>$this->token,
                        'openid'=>$this->openid
                    ))->find();
                }
            }
        }

        $this->assign('isBuy',$aProduct);
        $this->assign('aMediaUser',$aMediaUser);
        $this->assign('iType',$iType);
        $this->display('myincomes');

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
            $aUseres = $oUseres->where(array('uid'=>$aUser['id'],'openid'=>$val['openid']))->find();
            $aUserlist[$k]['name'] = $aUseres['nickname'];
            $aUserlist[$k]['headimgurl'] = $aUseres['headimgurl'];
            $aUserlist[$k]['number'] = intval(M('Media_inviterecord')->where(array('token'=>$this->token,'openid'=>$val['openid']))->count())-1;
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

            if($aUser['bank_name'] && $aUser['bank_card'] && $aUser['alipay_account'] && $aUser['phone'] && $aUser['passwd']){
                echo $this->encode(array('codes'=>1,'urles'=>'index.php?g=Wap&m=Media&a=withdrawals&token='.$this->token.'&openid='.$this->openid));exit;
            }else{
                echo $this->encode(array('codes'=>0,'msg'=>'您的资料还未完善，请至少填写个人密码，开户银行，开户人姓名，银行卡号和联系电话','urles'=>'index.php?g=Wap&m=Media&a=myhome&token='.$this->token.'&openid='.$this->openid));exit;
            }
        }
        $info = $oUrseModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign(array(
            'info'=>$info,
            'minmoney'=>$oSetcenter->where(array('token'=>$this->token))->find()
        ));
        $this->udisplay();
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
                                'urles'=>'index.php?g=Wap&m=Media&a=withdrawalsinfo&token='.$this->token.'&openid='.$this->openid));exit;
                        }else{
                            echo $this->encode(array('codes'=>0,
                                'msg'=>'系统繁忙，请稍后',
                                'urles'=>'index.php?g=Wap&m=Media&a=withdrawals&token='.$this->token.'&openid='.$this->openid));exit;
                        }
                    }
                }else{
                    echo $this->encode(array('codes'=>3,
                        'msg'=>'您的余额不足',
                        'urles'=>'index.php?g=Wap&m=Media&a=withdrawals&token='.$this->token.'&openid='.$this->openid));exit;
                }

            }
        }
        $this->assign(array(
            'list'=>$oModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->select()
        ));
        $this->udisplay();
    }

    /*
     * 我的小伙伴
     */
    public function mybrother(){
        $oUsersModel = M('Media_users');
	    $oinviterecord  = M('Media_inviterecord');
        $oWxuser = M('Wxuser');
        $oWxusers = M('Wxusers');
        $iType = $_GET['type'];
        //$aUsera = M()->query("select * from tp_media_users where token = '$this->token' and from_openid = '$this->openid' ORDER BY add_time desc");


        $aOnes = intval($oinviterecord->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>1))->count());
	$aSecend = intval($oinviterecord->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>2))->count());
	$aTow = intval($oinviterecord->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>3))->count());

        /*$iCountOne = count($aOne);
        $iCountOne = count($aOne);
        $iCountOne = count($aOne);*/


        $myname = $oUsersModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $oUsersModel->where(array('token'=>$this->token,'openid'=>$myname['from_openid']))->find();
        $aMyUrser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $aMyUrsers = M('Wxusers')->where(array('uid'=>$aMyUrser['id'],'openid'=>$myname['from_openid']))->find();
        $this->assign(array(

            'iCountOne' =>$a= count($aOnes),
            'iCountTwo' =>$b= count($aSecend),
            'iCountThr' =>$c= count($aTow),
            'upname'=>$aMyUrsers
        ));
	//print_r($a);exit;

        $this->udisplay();

    }
    public function mybrothers(){
        $oUsersModel = M('Media_users');
        $oinviterecord  = M('Media_inviterecord');
        $oWxuser = M('Wxuser');
        $oWxusers = M('Wxusers');
        $iType = $_GET['type'];
        //$aUsera = M()->query("select * from tp_media_users where token = '$this->token' and from_openid = '$this->openid' ORDER BY add_time desc");

        if($iType ==1){
            $aUser = $oinviterecord->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>1))->select();
        }elseif($iType==2){
            $aUser = $oinviterecord->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>2))->select();
        }elseif($iType==3){
            $aUser = $oinviterecord->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>3))->select();
        }


        $iCount = count($aUser);
        /*任务总量*/

       // $aUserel = M('Media_users')->where(array('token'))->select();
        $this->assign(array(
           'info' => $aUser,
            'iCount'=>$iCount

        ));
        $this->udisplay();
    }

    /*
     * 营销活动
     * */
    public function marketing(){
        $markmodel = M('Media_marketingactivity');
        $market = $markmodel->where(array('token'=>$this->token))->order('addtime desc')->select();
        $this->assign('market',$market);
        $this->display();
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
        $this->udisplay();
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
                $this->udisplay();
            }
        }
    }
    /*个人中心*/
    public function myCenter(){
        /*个人资料*/
        $aUser = M('Media_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $iDay = date('Y-m-d') -$aUser['date'];

        /*任务总量*/
        $itask = M('Media_user_tasks')->where(array('token'=>$this->token,'openid'=>$this->openid))->count();//任务总量
        $iclick =M('Media_user_tasks')->where(array('token'=>$this->token,'openid'=>$this->openid))->sum('clicks');//阅读总量
        $iDeal = M('Edia_user_commission')->where(array('token'=>$this->token,'g_openid'=>$this->openid))->count();//成交量

        #统计信息个数
        $iInfo =M('Media_station')->where(array('token'=>$this->token,'type'=>1))->count(); //系统消息
        $iInfos = M('Media_station')->where(array('token'=>$this->token,'type'=>2))->count(); //任务消息
        $this->assign(array(
            'itask'=>$itask,
            'iclick'=>intval($iclick),
            'iDeal' =>$iDeal,
            'iInfo'=>$iInfo,
            'iInfos'=>$iInfos,
            'aUser'=>$aUser,
            'day'=>$iDay,
            'homepic'=>M('Imag')->where(array('token'=>$this->token,'app'=>'Media','type'=>'homepic'))->find()
        ));
        $this->udisplay();
    }


}