<?php
class GuajiangAction extends BaseAction{

    public $guize = null;
    public $usercenterdata = null;

    public function _initialize(){
        parent::_initialize();
        $this->assign('hideMenu', in_array($_GET['token'], array(
            'xxxxxx'
        )));
        if($guize = M('Usercenter_score_guize')->where(array('token'=>$this->token,'app_id'=>20))->find()){
            $this->guize = $guize;
            $usersModel = M('Wxusers');
            $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $usercenterdata = M('Usercenter_memberlist')->where(array('uid'=>$wxuser['id'],'openid'=>$this->openid))->find();
            $this->usercenterdata = $usercenterdata;
            $this->assign('usercenterdata',$this->usercenterdata);
            if($guize['is_member']==1){
                if(!$usercenterdata){
                    $this->redirect('Home/Nofind/isnotmember',array('token'=>$this->token,'openid'=>$this->openid));
                }
            }
            if($guize['is_score']==1){
                if(!$usercenterdata){
                    $this->redirect('Home/Nofind/isnotmember',array('token'=>$this->token,'openid'=>$this->openid));
                }else{

                }
            }
        }

    }

	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		$token	  =  $this->_get('token');
		$wecha_id = $this->_get('wecha_id');
	
		$id 	  = $this->_get('id');

		$redata	  = M('Lottery_record');
		$where	  = array('token'=>$token,'wecha_id'=>$wecha_id,'lid'=>$id);
		$record   = $redata->where($where)->find();
		if($record == Null){
            $info = array('token'=>$token,'wecha_id'=>$wecha_id,'lid'=>$id,'starttime'=>time());
			$redata->add($info);
			//sleep(1);
			$record =$redata->where($where)->find();
		} 

		$Lottery =	M('Lottery')->where(array('id'=>$id,'token'=>$token,'type'=>2,'status'=>1))->find();
        $fistnumes = intval(($Lottery['fistnums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $secondnumes = intval(($Lottery['secondnums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $thirdnumes= intval(($Lottery['thirdnums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $fournumes = intval(($Lottery['fournums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $fivenumes = intval(($Lottery['fivenums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $sixnumes = intval(($Lottery['sixnums']/($Lottery['allpeople']*$Lottery['canrqnums'])*100));
        $otheres = 100 - ($fistnumes + $secondnumes +$thirdnumes + $fournumes + $fivenumes + $sixnumes);
        $db=M('Lottery');
		$data = array();

		if ($Lottery['enddate'] < time()) {
			 $data['usenums'] = 3;
			 $data['endinfo'] = $Lottery['endinfo'];
			 $this->assign('Guajiang',$data);
			 $this->display();
			 exit();
		}
        if ($record['islottery'] == 1) {

            $data['usenums'] = 2;
            $data['sncode']	 = $record['sn'];
            $data['uname']	 = $record['wecha_name'];
            $data['prize'] 	 = $record['prize'];
			$date = date("Y年m月d日",$record['time']);
			$msg = "尊敬的:<font color='red'>".$data['uname']."</font>,您已经中过<font color='red'> ".$data['prize']."</font> 了,您的领奖序列号:<font color='red'> ".$record['sn']." </font>请您牢记及尽快与我们联系.中奖日期为".$date;

            $data['winprize']	=$msg;
        }else{


            if ($record['usenums'] >= $Lottery['canrqnums'] ) {
                //次数已经达到限定
                $data['usenums'] = 1;
                $data['winprize']	= '抽奖次数已用完';
            }else{
                $listtime = date("Y-m-d",$record['starttime']);
                $listtime1 = strtotime($listtime);
                $listtime2 = $listtime1+ 3600*24;
                if($record['starttime']>$listtime1){
                    $days = ceil(( time() - $listtime2)/3600/24);
                    $degree = $Lottery['everycanrqnums'] * ($days+1);
                    if($record['usenums'] >= $degree){
                        $data['winprize']	= '今天可抽次数已完';

                    }else{
                        if($this->guize['is_score'] == 1){
                            if($this->usercenterdata['score'] < $this->guize['need_score']){
                                $zjl = false;
                                $data['usenums'] = 1;
                                $winprize	= '尊敬的用户您的积分不够了哦,赶快去用户中心签到获取积分吧';
                            }else{
                                $usercenter_scored_recordModel = M('Usercenter_score_record');
                                $memberlistModel = M("Usercenter_memberlist");
                                $cdata['token'] = $this->token;
                                $cdata['openid'] = $wecha_id;
                                $cdata['type'] = 5;
                                $cdata['score'] = -($this->guize['need_score']);
                                $cdata['add_time'] = time();
                                $usercenter_scored_recordModel->add($cdata);
                                $memscore = $this->usercenterdata['score']+$cdata['score'];
                                $memberlistModel->where(array('token'=>$this->token,'openid'=>$wecha_id))->save(array('score'=>$memscore));

                            M('Lottery_record')->where(array('id'=>$record['id']))->setInc('usenums');
                            $record = M('Lottery_record')->where(array('id'=>$record['id']))->find();
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
                           // print_r($arr);exit;
                            if ($Lottery['allpeople'] == 1) {

                                if ($Lottery['fistlucknums'] <= $Lottery['fistnums']) {
                                    $rid = 1;
                                }else{
                                    $rid = 6;
                                }

                            }else{
                                $rid = $this->get_rand($arr);
                                $data['joinnum'] = $Lottery['joinnum']+1;
                                $db->where('id='.$Lottery['id'])->save($data);
                            }


                            $winprize = $prize_arr[$rid-1]['prize'];
                            $zjl = false;

                            switch($rid){
                                case 1:

                                    if ($Lottery['fistlucknums'] > $Lottery['fistnums']) {
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{

                                        $zjl	= true;
                                        M('Lottery')->where(array('id'=>$id))->setInc('fistlucknums');
                                    }
                                    break;

                                case 2:
                                    if ($Lottery['secondlucknums'] > $Lottery['secondnums']) {
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        //判断是否设置了2等奖&&数量
                                        if(empty($Lottery['second']) && empty($Lottery['secondnums'])){
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        }else{ //输出中了二等奖
                                            $zjl	= true;
                                            M('Lottery')->where(array('id'=>$id))->setInc('secondlucknums');
                                        }

                                    }
                                    break;

                                case 3:
                                    if ($Lottery['thirdlucknums'] > $Lottery['thirdnums']) {
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        if(empty($Lottery['third']) && empty($Lottery['thirdnums'])){
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        }else{
                                            $zjl	= true;
                                            M('Lottery')->where(array('id'=>$id))->setInc('thirdlucknums');
                                        }

                                    }
                                    break;
                                case 4:
                                    if ($Lottery['fourlucknums'] > $Lottery['fournums']) {
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        if(empty($Lottery['four']) && empty($Lottery['fournums'])){
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        }else{
                                            $zjl	= true;
                                            M('Lottery')->where(array('id'=>$id))->setInc('fourlucknums');
                                        }

                                    }
                                    break;

                                case 5:
                                    if ($Lottery['fivelucknums'] > $Lottery['fivenums']) {
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        if(empty($Lottery['five']) && empty($Lottery['fivenums'])){
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        }else{
                                            $zjl	= true;
                                            M('Lottery')->where(array('id'=>$id))->setInc('fivelucknums');
                                        }

                                    }
                                    break;
                                case 6:
                                    if ($Lottery['sixlucknums'] > $Lottery['sixnums']) {
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        if(empty($Lottery['six']) && empty($Lottery['sixnums'])){
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        }else{
                                            $zjl	= true;
                                            M('Lottery')->where(array('id'=>$id))->setInc('sixlucknums');
                                        }

                                    }
                                    break;
                                default:
                                    $zjl = false;
                                    $winprize = '谢谢参与';
                                    break;
                            }

                        }

                    }else{
                        M('Lottery_record')->where(array('id'=>$record['id']))->setInc('usenums');
                        $record = M('Lottery_record')->where(array('id'=>$record['id']))->find();
                        $prize_arr = array(
                            '0' => array('id'=>1,'prize'=>'一等奖','v'=>$fistnumes),
                            '1' => array('id'=>2,'prize'=>'二等奖','v'=>$secondnumes),
                            '2' => array('id'=>3,'prize'=>'三等奖','v'=> $thirdnumes),
                            '3' => array('id'=>4,'prize'=>'四等奖','v'=> $fournumes),
                            '4' => array('id'=>5,'prize'=>'五等奖','v'=> $fivenumes),
                            '5' => array('id'=>6,'prize'=>'六等奖','v'=> $sixnumes),
                            '6' => array('id'=>7,'prize'=>'谢谢参与','v'=> $otheres)
                        );

                        $arr = array();
                        foreach ($prize_arr as $key => $val) {
                            $arr[$val['id']] = $val['v'];
                        }
		


                        if ($Lottery['allpeople'] == 1) {  //设置参与总人数为1时

                            if ($Lottery['fistlucknums'] <= $Lottery['fistnums']) {
                                $rid = 1;
                            }else{
                                $rid = 6;
                            }

                        }else{

                            $rid = $this->get_rand($arr);
                            $data['joinnum'] = $Lottery['joinnum']+1;   //参与人数加1
                            $db->where('id='.$Lottery['id'])->save($data);
                        }
                        $winprize = $prize_arr[$rid-1]['prize'];
                        $zjl = false;

                        switch($rid){
                            case 1:
                                if ($Lottery['fistlucknums'] > $Lottery['fistnums']) {
                                     $zjl = false;
                                     $winprize = '谢谢参与';
                                }else{
                                    $zjl	= true;
                                    M('Lottery')->where(array('id'=>$id))->setInc('fistlucknums');
                                }
                            break;

                            case 2:
                                if ($Lottery['secondlucknums'] > $Lottery['secondnums']) {
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                }else{
                                    //判断是否设置了2等奖&&数量
                                    if(empty($Lottery['second']) && empty($Lottery['secondnums'])){
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{ //输出中了二等奖
                                        $zjl	= true;
                                        M('Lottery')->where(array('id'=>$id))->setInc('secondlucknums');
                                    }

                                }
                            break;

                            case 3:
                                if ($Lottery['thirdlucknums'] > $Lottery['thirdnums']) {
                                     $zjl = false;
                                     $winprize = '谢谢参与';
                                }else{
                                    if(empty($Lottery['third']) && empty($Lottery['thirdnums'])){
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        $zjl	= true;
                                        M('Lottery')->where(array('id'=>$id))->setInc('thirdlucknums');
                                    }

                                }
                            break;
                            case 4:
                                if ($Lottery['fourlucknums'] > $Lottery['fournums']) {
                                    $zjl = false;
                                    $winprize = '谢谢参与';
                                }else{
                                    if(empty($Lottery['four']) && empty($Lottery['fournums'])){
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        $zjl	= true;
                                        M('Lottery')->where(array('id'=>$id))->setInc('fourlucknums');
                                    }

                                }
                                break;

                            case 5:
                                if ($Lottery['fivelucknums'] > $Lottery['fivenums']) {
                                    $zjl = false;
                                    $winprize = '谢谢参与';
                                }else{
                                    if(empty($Lottery['five']) && empty($Lottery['fivenums'])){
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        $zjl	= true;
                                        M('Lottery')->where(array('id'=>$id))->setInc('fivelucknums');
                                    }

                                }
                                break;
                            case 6:
                                if ($Lottery['sixlucknums'] > $Lottery['sixnums']) {
                                    $zjl = false;
                                    $winprize = '谢谢参与';
                                }else{
                                    if(empty($Lottery['six']) && empty($Lottery['sixnums'])){
                                        $zjl = false;
                                        $winprize = '谢谢参与';
                                    }else{
                                        $zjl	= true;
                                        M('Lottery')->where(array('id'=>$id))->setInc('sixlucknums');
                                    }

                                }
                                break;
                            default:
                                    $zjl = false;
                                    $winprize = '谢谢参与';
                                    break;
                        }
                    }

                //$data['prizeid']  	= $rid;
                $data['zjl'] 		= $zjl;
                $data['wecha_id']	= $record['wecha_id'];
                $data['lid']		= $record['lid'];
                $data['winprize']	= $winprize;


                    }

                }
                //end if;
            } //end second if;
		} // end first if; 

        $data['On'] 		= $Lottery['displayjpnums'];
		$data['usecout'] 	= $record['usenums'];
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
        $data['wecha_id'] = $wecha_id;
        $data['click'] = $Lottery['click']+1;
                
        $db->where('id='.$Lottery['id'])->save($data);
		$this->assign('Guajiang',$data);
		$this->display();
		
	}
	
	protected function get_rand($proArr) { 
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
		 /*   foreach ($proArr as $key => $proCur) {
		        $randNum = mt_rand(1, $proSum);  //产生随机数
		        if ($randNum <= $proCur) { 
		            $result = $key; 
		            break; 
		        } else { 
		            $proSum -= $proCur; 
		        } 
		    } 
		    unset ($proArr);
		    return $result; */
	} 
	

	public function add(){
		if($_POST['action'] ==  'add'  ){
			$lid 				= $this->_post('lid');
			$wechaid 			= $this->_post('wechaid');
			$data['phone'] 		= $this->_post('tel');
			$data['wecha_name'] = $this->_post('wxname');
			$data['prize'] = $this->_post('prize');
			$data['islottery'] 	= 1;
			$data['time']		= time();
			$data['sn']			= $this->create_password();
			$rollback = M('Lottery_record')->where(array('lid'=> $lid,
				'wecha_id'=>$wechaid))->save($data);
			echo'{"success":1,"msg":"恭喜！尊敬的'.$data['wecha_name'].'请您保持手机通畅！请您牢记的领奖号:'.$data['sn'].',中奖日期为：'.date("Y年m月d日",$data['time']).'"}';
			exit;
		}

		$record = M('Lottery_record');
		$data['phone'] 		= $this->_post('tel');
		$data['wecha_name'] = $this->_post('wxname');
		$data['prize'] = $this->_post('prize');
		$data['islottery'] 	= 1;
		$data['time']		= time();
		$data['sn']			= uniqid();
		$rollback = $record->where(array('lid'=>$this->_post('lid') , 'wecha_id'=>$this->_post('wechaid') ))->save($data);


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
	
}
?>