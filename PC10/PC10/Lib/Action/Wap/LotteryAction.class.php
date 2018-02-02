<?php
class LotteryAction extends BaseAction{

    public $guize = null;
    public $usercenterdata = null;

    public function _initialize(){
        parent::_initialize();
        if($guize = M('Usercenter_score_guize')->where(array('token'=>$this->token,'app_id'=>18))->find()){
            $this->guize = $guize;
            $usersModel = M('Wxusers');
            $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $usercenterdata = M('Usercenter_memberlist')->where(array('uid'=>$wxuser['id'],'openid'=>$this->openid))->find();
            $this->usercenterdata = $usercenterdata;
            $this->assign('usercenterdata',$this->usercenterdata);
            if($guize['is_member'] == 1){
                if(!$usercenterdata){
                    $this->redirect('Home/Nofind/isnotmember',array('token'=>$this->token,'openid'=>$this->openid));
                }
            }
            if($guize['is_score'] == 1){
                if(!$usercenterdata){
                    $this->redirect('Home/Nofind/isnotmember',array('token'=>$this->token,'openid'=>$this->openid));
                }else{

                }
            }
        }

    }

    public function index(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        /*  if(!strpos($agent,"MicroMessenger")) {
              echo '此功能只能在微信浏览器中使用';exit;
          }*/
        $token		= $this->_get('token');
        $wecha_id	= $this->_get('wecha_id');
        $id 		= $this->_get('id');
        $redata		= M('Lottery_record');
        $where 		= array('token'=>$token,'wecha_id'=>$wecha_id,'lid'=>$id);

        $record 	= $redata->where($where)->find();

        if($record == Null){
            $info = array('token'=>$token,'wecha_id'=>$wecha_id,'lid'=>$id,'starttime'=>time());
            $redata->add($info);
            $record = $redata->where($where)->find();
        }

        $Lottery 	= M('Lottery')->where(array('id'=>$id,'token'=>$token,'type'=>1,'status'=>1))->find();

        //1.活动过期,显示结束

        //4.显示奖项,说明,时间
        if ($Lottery['enddate'] < time()) {
            $data['end'] = 1;
            $data['endinfo'] = $Lottery['endinfo'];
            $this->assign('Dazpan',$data);

            $this->display();
            exit();
        }
        $data['On'] 		= $Lottery['displayjpnums'];
        $data['token'] 		= $token;
        $data['wecha_id']	= $record['wecha_id'];
        $data['lid']		= $record['lid'];
        $data['rid']		= $record['id'];
        $data['usenums'] 	= $record['usenums'];
        $data['canrqnums']	= $Lottery['canrqnums'];
        $data['fist'] 		= $Lottery['fist'];
        $data['second'] 	= $Lottery['second'];
        $data['third'] 		= $Lottery['third'];
        $data['four'] 		= $Lottery['four'];
        $data['five'] 		= $Lottery['five'];
        $data['six'] 		= $Lottery['six'];
        $data['fistnums'] 	= $Lottery['fistnums'];
        $data['secondnums'] = $Lottery['secondnums'];
        $data['thirdnums'] 	= $Lottery['thirdnums'];
        $data['fournums'] 	= $Lottery['fournums'];
        $data['fivenums'] 	= $Lottery['fivenums'];
        $data['sixnums'] 	= $Lottery['sixnums'];
        $data['info']		= $Lottery['info'];
        $data['txt']		= $Lottery['txt'];
        $data['sttxt']		= $Lottery['sttxt'];
        $data['title']		= $Lottery['title'];
        $data['statdate']	= $Lottery['statdate'];
        $data['enddate']	= $Lottery['enddate'];
        $db=M('Lottery');
        $data['click'] = $Lottery['click']+1;
        $db->where('id='.$Lottery['id'])->save($data);
        $this->assign('Dazpan',$data);
        //var_dump($data);exit();

        $this->display();
    }

    protected function get_rand($proArr) {
        /* $result = '';
         $proSum = array_sum($proArr);
         foreach ($proArr as $key => $proCur) {
             $randNum = mt_rand(1, $proSum);
             if ($randNum <= $proCur) {
                 $result = $key;
                 break;
             } else {
                 $proSum -= $proCur;
             }
         }
         unset ($proArr);
         return $result; */
        $result = ' ';
        $akey = array_keys($proArr);
        $aValure = array_values($proArr);
        $proSum = array_sum($proArr);
        $randNum = mt_rand(1, $proSum);
        $iOne = $aValure[0];
        $iTwo = $iOne + $aValure[1];
        $iThree = $iTwo + $aValure[2];
        $iFoure = $iThree + $aValure[3];
        $iFive = $iFoure + $aValure[4];
        $iSix = $iFive + $aValure[5];
        $iServer = $iSix + $aValure[6];

        if(0 < $randNum and $randNum<=$iOne){
            $result = $akey[0];
        }elseif($iOne < $randNum and $randNum<=$iTwo){
            $result = $akey[1];
        }elseif($iTwo < $randNum and $randNum<=$iThree){
            $result = $akey[2];
        }elseif($iThree < $randNum and $randNum<=$iFoure){

            $result = $akey[3];
        }elseif($iFoure < $randNum and $randNum<=$iFive){
            $result = $akey[4];
        }elseif($iFive < $randNum and $randNum<=$iSix){
            $result = $akey[5];
        }elseif($iSix < $randNum and $randNum<=$iServer){
            $result = $akey[6];
        }

        return $result;
    }

    protected function get_prize($id){
        $Lottery 	= M('Lottery')->where(array('id'=>$id,'type'=>1,'status'=>1))->find();
        $fistnumes = intval(($Lottery['fistnums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $secondnumes = intval(($Lottery['secondnums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $thirdnumes= intval(($Lottery['thirdnums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $fournumes = intval(($Lottery['fournums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $fivenumes = intval(($Lottery['fivenums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $sixnumes = intval(($Lottery['sixnums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $otheres = 100 - ($fistnumes + $secondnumes +$thirdnumes + $fournumes + $fivenumes + $sixnumes);

        $prize_arr = array(
            '0' => array('id'=>1,'prize'=>'一等奖','v'=>$fistnumes),
            '1' => array('id'=>2,'prize'=>'二等奖','v'=>$secondnumes),
            '2' => array('id'=>3,'prize'=>'三等奖','v'=> $thirdnumes),
            '3' => array('id'=>4,'prize'=>'四等奖','v'=> $fournumes),
            '4' => array('id'=>5,'prize'=>'五等奖','v'=> $fivenumes),
            '5' => array('id'=>6,'prize'=>'六等奖','v'=> $sixnumes),
            '6' => array('id'=>7,'prize'=>'谢谢参与','v'=> $otheres)
        );

        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val['v'];
        }
        //-------------------------------
        //随机抽奖[如果预计活动的人数为1为各个奖项100%中奖]
        //-------------------------------
        if ($Lottery['allpeople'] == 1) {

            if ($Lottery['fistlucknums'] <= $Lottery['fistnums']) {
                $prizetype = 1;
            }else{
                $prizetype = 7;
            }

        }else{
            $prizetype = $this->get_rand($arr);
            $db=M('Lottery');
            $data['joinnum'] = $Lottery['joinnum']+1;
            $db->where('id='.$Lottery['id'])->save($data);
        }

        //$winprize = $prize_arr[$rid-1]['prize'];

        switch($prizetype){
            case 1:

                if ($Lottery['fistlucknums'] > $Lottery['fistnums']) {
                    $prizetype = '';
                    //$winprize = '谢谢参与';
                }else{

                    $prizetype = 1;
                    M('Lottery')->where(array('id'=>$id))->setInc('fistlucknums');
                }
                break;

            case 2:
                if ($Lottery['secondlucknums'] > $Lottery['secondnums']) {
                    $prizetype = '';
                    //$winprize = '谢谢参与';
                }else{
                    //判断是否设置了2等奖&&数量
                    if(empty($Lottery['second']) && empty($Lottery['secondnums'])){
                        $prizetype = '';
                        //$winprize = '谢谢参与';
                    }else{ //输出中了二等奖
                        $prizetype = 2;
                        M('Lottery')->where(array('id'=>$id))->setInc('secondlucknums');
                    }

                }
                break;

            case 3:
                if ($Lottery['thirdlucknums'] > $Lottery['thirdnums']) {
                    $prizetype = '';
                    // $winprize = '谢谢参与';
                }else{
                    if(empty($Lottery['third']) && empty($Lottery['thirdnums'])){
                        $prizetype = '';
                        // $winprize = '谢谢参与';
                    }else{
                        $prizetype = 3;
                        M('Lottery')->where(array('id'=>$id))->setInc('thirdlucknums');
                    }

                }
                break;

            case 4:
                if ($Lottery['fourlucknums'] > $Lottery['fournums']) {
                    $prizetype =  '';
                    // $winprize = '谢谢参与';
                }else{
                    if(empty($Lottery['four']) && empty($Lottery['fournums'])){
                        $prizetype =  '';
                        //$winprize = '谢谢参与';
                    }else{
                        $prizetype = 4;
                        M('Lottery')->where(array('id'=>$id))->setInc('fourlucknums');
                    }
                }
                break;

            case 5:
                if ($Lottery['fivelucknums'] > $Lottery['fivenums']) {
                    $prizetype =  '';
                    //$winprize = '谢谢参与';
                }else{
                    if(empty($Lottery['five']) && empty($Lottery['fivenums'])){
                        $prizetype =  '';
                        //$winprize = '谢谢参与';
                    }else{
                        $prizetype = 5;
                        M('Lottery')->where(array('id'=>$id))->setInc('fivelucknums');
                    }
                }
                break;

            case 6:
                if ($Lottery['sixlucknums'] > $Lottery['sixnums']) {
                    $prizetype =  '';
                    // $winprize = '谢谢参与';
                }else{
                    if(empty($Lottery['six']) && empty($Lottery['sixnums'])){
                        $prizetype =  '';
                        //$winprize = '谢谢参与';
                    }else{
                        $prizetype = 6;
                        M('Lottery')->where(array('id'=>$id))->setInc('sixlucknums');
                    }

                }
                break;

            default:
                $prizetype =  '';
                //$winprize = '谢谢参与';

                break;
        }

        return $prizetype;
    }

    public function getajax(){

        $token 		=	$this->_post('token');
        $wecha_id	=	$this->_post('oneid');
        $id 		=	$this->_post('id');
        $rid 		= 	$this->_post('rid');
        $redata 	=	M('Lottery_record');
        $where 		= 	array('token'=>$token,'wecha_id'=>$wecha_id,'lid'=>$id);
        $record 	=	$redata->where($where)->find();
        // 1. 中过奖金
        if ($record['islottery'] == 1) {
            //$norun = 1;
            $sn	 	 = $record['sn'];
            $uname	 = $record['wecha_name'];
            $prize	 = $record['prize'];
            $tel 	 = $record['phone'];
            $date = date("Y年m月d日",$record['time']);
            $msg = "尊敬的:<font color='red'>".$uname."</font>,您已经中过<font color='red'> ".$prize."</font> 了,您的领奖序列号:<font color='red'> ".$sn." </font>请您牢记及尽快与我们联系.中奖日期为".$date;
            echo '{"norun":1,"msg":"'.$msg.'"}';
            exit;
        }
        // 2. 抽奖次数是否达到
        $Lottery 	= M('Lottery')->where(array('id'=>$id,'token'=>$token,'type'=>1,'status'=>1))->find();

        if ($record['usenums'] >= $Lottery['canrqnums'] ) {
            $norun 	 =  2;
            $usenums =  $record['usenums'];
            $canrqnums=	$Lottery['canrqnums'];
            echo '{
				"norun":'.$norun.',
				"usenums":"'.$usenums.'",
				"canrqnums":"'.$canrqnums.'",
				"id":"'.$id.'",
				"token":"'.$token.'",
				"type":"'.$type.'",
				"status":"'.$status.'"
			}';
            exit;
        }else {
            $listtime = date("Y-m-d",$record['starttime']);
            $listtime1 = strtotime($listtime);
            $listtime2 = $listtime1+ 3600*24;
            if($record['starttime']>$listtime1){
                $days = ceil(( time() - $listtime2)/3600/24);
                $degree = $Lottery['everycanrqnums'] * ($days+1);
                if ($record['usenums'] >= $degree) {
                    echo '{"norun":1,"msg":"亲，今天抽奖次数已用完"}';
                    exit;
                } else {
                    //每次请求先增加 使用次数 usenums
                    /*修改积分*/
                    if ($this->guize['is_score'] == 1) {
                        if ($this->usercenterdata['score'] < $this->guize['need_score']) {
                            $msg = "尊敬的用户您的积分不够了哦,赶快去会员中心签到获取积分吧";
                            echo '{"norun":1,"msg":"' . $msg . '"}';
                            exit;
                        }
                        $usercenter_scored_recordModel = M('Usercenter_score_record');
                        $memberlistModel = M("Usercenter_memberlist");
                        $data['token'] = $this->token;
                        $data['openid'] = $wecha_id;
                        $data['type'] = 4;
                        $data['score'] = -($this->guize['need_score']);
                        $data['add_time'] = time();
                        $usercenter_scored_recordModel->add($data);
                        $memscore = $this->usercenterdata['score'] + $data['score'];
                        $memberlistModel->where(array('token' => $this->token, 'openid' => $wecha_id))->save(array('score' => $memscore));
                    }
                    M('Lottery_record')->where(array('id' => $rid))->setInc('usenums');
                    $record = M('Lottery_record')->where(array('id' => $rid))->find();
                    $prizetype = $this->get_prize($id);
                    if ($prizetype >= 1 || $prizetype <= 6) {
                        $sn = $this->create_password();
                        echo '{"success":1,"sn":"' . $sn . '","prizetype":"' . $prizetype . '","usenums":"' . $record['usenums'] . '"}';
                    } else {
                        echo '{"success":0,"prizetype":"","usenums":"' . $record['usenums'] . '"}';
                    }
                    exit;
                }
            }else{
                $days = ceil(( time() - $listtime2)/3600/24);
                $degree = $Lottery['everycanrqnums'] * $days;
                if ($record['usenums'] >= $degree) {
                    echo '{"norun":1,"msg":"亲，今天抽奖次数已用完"}';
                    exit;

                } else {


                    //每次请求先增加 使用次数 usenums
                    /*修改积分*/
                    if ($this->guize['is_score'] == 1) {
                        if ($this->usercenterdata['score'] < $this->guize['need_score']) {
                            $msg = "尊敬的用户您的积分不够了哦,赶快去会员中心签到获取积分吧";
                            echo '{"norun":1,"msg":"' . $msg . '"}';
                            exit;
                        }
                        $usercenter_scored_recordModel = M('Usercenter_score_record');
                        $memberlistModel = M("Usercenter_memberlist");
                        $data['token'] = $this->token;
                        $data['openid'] = $wecha_id;
                        $data['type'] = 4;
                        $data['score'] = -($this->guize['need_score']);
                        $data['add_time'] = time();
                        $usercenter_scored_recordModel->add($data);
                        $memscore = $this->usercenterdata['score'] + $data['score'];
                        $memberlistModel->where(array('token' => $this->token, 'openid' => $wecha_id))->save(array('score' => $memscore));
                    }
                    M('Lottery_record')->where(array('id' => $rid))->setInc('usenums');
                    $record = M('Lottery_record')->where(array('id' => $rid))->find();
                    $prizetype = $this->get_prize($id);
                    if ($prizetype >= 1 || $prizetype <= 6) {
                        $sn = $this->create_password();
                        echo '{"success":1,"sn":"' . $sn . '","prizetype":"' . $prizetype . '","usenums":"' . $record['usenums'] . '"}';
                    } else {
                        echo '{"success":0,"prizetype":"","usenums":"' . $record['usenums'] . '"}';
                    }
                    exit;
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


    //中奖后填写信息
    public function add(){
        if($_POST['action'] ==  'add' ){
            $lid 				= $this->_post('lid');
            $wechaid 			= $this->_post('wechaid');
            $data['sn']			= $this->_post('sncode');
            $data['phone'] 		= $this->_post('tel');
            $data['prize']		= $this->_post('prizetype');
            $data['wecha_name'] = $this->_post('wxname');
            $data['time']		= time();
            $data['islottery']	= 1;

            $rollback = M('Lottery_record')->where(array('lid'=> $lid,
                'wecha_id'=>$wechaid))->save($data);

            echo'{"success":1,"msg":"恭喜！尊敬的 '.$data['wecha_name'].',请您保持手机通畅！你的领奖序号:'.$data['sn'].',中奖日期为：'.date("Y年m月d日",$data['time']).'"}';
            exit;
        }
    }
}

?>