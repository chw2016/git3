<?php
class ZadanAction extends BaseAction{

    public function _initialize(){
        parent::_initialize();
        if($guize = M('Usercenter_score_guize')->where(array('token'=>$this->token,'app_id'=>22))->find()){
            $this->guize = $guize;
            $usersModel = M('Wxusers');
            $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $usercenterdata = M('Usercenter_memberlist')->where(array('uid'=>$wxuser['id'],'openid'=>$this->openid))->find();
            $this->usercenterdata = $usercenterdata;
            $this->assign('usercenterdata',$this->usercenterdata);
            if($guize['is_member']){
                if(!$usercenterdata){
                    $this->redirect('Home/Nofind/isnotmember',array('token'=>$this->token,'openid'=>$this->openid));
                }
            }
            if($guize['is_score']){
                if(!$usercenterdata){
                    $this->redirect('Home/Nofind/isnotmember',array('token'=>$this->token,'openid'=>$this->openid));
                }else{

                }
            }
        }

    }

	public function index(){
		
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
	 
		$token	  =  $this->_get('token');
		$wecha_id = $this->_get('wecha_id');
		if (!$wecha_id){
			$wecha_id='null';
		}
		
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

		$Lottery =	M('Lottery')->where(array('id'=>$id,'token'=>$token,'type'=>4))->find(); 
		$data = array();
		
		if ($Lottery['enddate'] < time()) {
			 $data['usenums'] = 3;
			 $data['endinfo'] = $Lottery['endinfo'];
			 $this->assign('Zadan',$data);
			 $this->display();
			 exit();
		}
		
			if ($record['islottery'] == 1) {
				
				$data['usenums'] = 2;
				$data['sncode']	 = $record['sn'];
				$data['uname']	 = $record['wecha_name'];				 
				$data['winprize']	= $record['prize'];
			}else{

				if ($record['usenums'] >= $Lottery['canrqnums'] ) {
					//次数已经达到限定
					$data['usenums'] = 1;
					$data['winprize']	= '抽奖次数已用完';
				}else {
                    $listtime = date("Y-m-d",$record['starttime']);
                    $listtime1 = strtotime($listtime);
                    $listtime2 = $listtime1+ 3600*24;
                    if($record['starttime']>$listtime1){
                        $days = ceil(( time() - $listtime2)/3600/24);
                        $degree = $Lottery['everycanrqnums'] * ($days+1);
                    if ($record['usenums'] >= $degree) {
                        $data['winprize'] = '今天可抽次数已完';
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
                            $cdata['type'] = 7;
                            $cdata['score'] = -($this->guize['need_score']);
                            $cdata['add_time'] = time();
                            $usercenter_scored_recordModel->add($cdata);
                            $memscore = $this->usercenterdata['score']+$cdata['score'];
                            $memberlistModel->where(array('token'=>$this->token,'openid'=>$wecha_id))->save(array('score'=>$memscore));

                            M('Lottery_record')->where(array('id'=>$record['id']))->setInc('usenums');
                            $record = M('Lottery_record')->where(array('id'=>$record['id']))->find();
                            $firstNum=intval($Lottery['fistnums'])-intval($Lottery['fistlucknums']);
                            $secondNum=intval($Lottery['secondnums'])-intval($Lottery['secondlucknums']);
                            $thirdNum=intval($Lottery['thirdnums'])-intval($Lottery['thirdlucknums']);
                            $fourthNum=intval($Lottery['fournums'])-intval($Lottery['fourlucknums']);
                            $fifthNum=intval($Lottery['fivenums'])-intval($Lottery['fivelucknums']);
                            $sixthNum=intval($Lottery['sixnums'])-intval($Lottery['sixlucknums']);
                            $multi=intval($Lottery['canrqnums']);//最多抽奖次数
                            $prize_arr = array(
                                '0' => array('id'=>1,'prize'=>'一等奖','v'=>$firstNum,'start'=>0,'end'=>$firstNum),
                                '1' => array('id'=>2,'prize'=>'二等奖','v'=>$secondNum,'start'=>$firstNum,'end'=>$firstNum+$secondNum),
                                '2' => array('id'=>3,'prize'=>'三等奖','v'=>$thirdNum,'start'=>$firstNum+$secondNum,'end'=>$firstNum+$secondNum+$thirdNum),
                                '3' => array('id'=>4,'prize'=>'四等奖','v'=>$fourthNum,'start'=>$firstNum+$secondNum+$thirdNum,'end'=>$firstNum+$secondNum+$thirdNum+$fourthNum),
                                '4' => array('id'=>5,'prize'=>'五等奖','v'=>$fifthNum,'start'=>$firstNum+$secondNum+$thirdNum+$fourthNum,'end'=>$firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum),
                                '5' => array('id'=>6,'prize'=>'六等奖','v'=>$sixthNum,'start'=>$firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum,'end'=>$firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum+$sixthNum),
                                '6' => array('id'=>7,'prize'=>'谢谢参与','v'=>(intval($Lottery['allpeople']))*$multi-($firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum+$sixthNum),'start'=>$firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum+$sixthNum,'end'=>intval($Lottery['allpeople'])*$multi)
                            );


                            foreach ($prize_arr as $key => $val) {
                                $arr[$val['id']] = $val;
                            }
                            if ($Lottery['allpeople'] == 1) {

                                if ($Lottery['fistlucknums'] <= $Lottery['fistnums']) {
                                    $rid = 1;
                                }else{
                                    $rid = 4;
                                }

                            }else{
                                $data['joinnum'] = $Lottery['joinnum']+1;
                                $rid = $this->get_rand($arr,intval($Lottery['allpeople'])*$multi);
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
                                        if(empty($Lottery['fournums']) && empty($Lottery['fournums'])){
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
                                        if(empty($Lottery['fivenums']) && empty($Lottery['fivenums'])){
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
                                        if(empty($Lottery['sixnums']) && empty($Lottery['sixnums'])){
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
                        $firstNum=intval($Lottery['fistnums'])-intval($Lottery['fistlucknums']);
                        $secondNum=intval($Lottery['secondnums'])-intval($Lottery['secondlucknums']);
                        $thirdNum=intval($Lottery['thirdnums'])-intval($Lottery['thirdlucknums']);
                        $fourthNum=intval($Lottery['fournums'])-intval($Lottery['fourlucknums']);
                        $fifthNum=intval($Lottery['fivenums'])-intval($Lottery['fivelucknums']);
                        $sixthNum=intval($Lottery['sixnums'])-intval($Lottery['sixlucknums']);
                        $multi=intval($Lottery['canrqnums']);//最多抽奖次数
                        $prize_arr = array(
                            '0' => array('id'=>1,'prize'=>'一等奖','v'=>$firstNum,'start'=>0,'end'=>$firstNum),
                            '1' => array('id'=>2,'prize'=>'二等奖','v'=>$secondNum,'start'=>$firstNum,'end'=>$firstNum+$secondNum),
                            '2' => array('id'=>3,'prize'=>'三等奖','v'=>$thirdNum,'start'=>$firstNum+$secondNum,'end'=>$firstNum+$secondNum+$thirdNum),
                            '3' => array('id'=>4,'prize'=>'四等奖','v'=>$fourthNum,'start'=>$firstNum+$secondNum+$thirdNum,'end'=>$firstNum+$secondNum+$thirdNum+$fourthNum),
                            '4' => array('id'=>5,'prize'=>'五等奖','v'=>$fifthNum,'start'=>$firstNum+$secondNum+$thirdNum+$fourthNum,'end'=>$firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum),
                            '5' => array('id'=>6,'prize'=>'六等奖','v'=>$sixthNum,'start'=>$firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum,'end'=>$firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum+$sixthNum),
                            '6' => array('id'=>7,'prize'=>'谢谢参与','v'=>(intval($Lottery['allpeople']))*$multi-($firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum+$sixthNum),'start'=>$firstNum+$secondNum+$thirdNum+$fourthNum+$fifthNum+$sixthNum,'end'=>intval($Lottery['allpeople'])*$multi)
                        );


                        foreach ($prize_arr as $key => $val) {
                            $arr[$val['id']] = $val;
                        }
                        if ($Lottery['allpeople'] == 1) {

                            if ($Lottery['fistlucknums'] <= $Lottery['fistnums']) {
                                $rid = 1;
                            }else{
                                $rid = 4;
                            }

                        }else{
                            $data['joinnum'] = $Lottery['joinnum']+1;
                            $rid = $this->get_rand($arr,intval($Lottery['allpeople'])*$multi);
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
                                    if(empty($Lottery['fournums']) && empty($Lottery['fournums'])){
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
                                    if(empty($Lottery['fivenums']) && empty($Lottery['fivenums'])){
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
                                    if(empty($Lottery['sixnums']) && empty($Lottery['sixnums'])){
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
                    $data['zjl'] = $zjl;
                    $data['wecha_id'] = $record['wecha_id'];
                    $data['lid'] = $record['lid'];
                    $data['winprize'] = $winprize;
                }
                    }else{
                        $days = ceil(( time() - $listtime2)/3600/24);
                        $degree = $Lottery['everycanrqnums'] * $days;
                        if($record['usenums'] >= $degree){
                            $data['winprize']	= '今天可抽次数已完';

                        }else{
                            if ($this->guize['is_score'] == 1) {
                                if ($this->usercenterdata['score'] < $this->guize['need_score']) {
                                    $zjl = false;
                                    $data['usenums'] = 1;
                                    $winprize = '尊敬的用户您的积分不够了哦,赶快去用户中心签到获取积分吧';
                                } else {
                                    $usercenter_scored_recordModel = M('Usercenter_score_record');
                                    $memberlistModel = M("Usercenter_memberlist");
                                    $cdata['token'] = $this->token;
                                    $cdata['openid'] = $wecha_id;
                                    $cdata['type'] = 7;
                                    $cdata['score'] = -($this->guize['need_score']);
                                    $cdata['add_time'] = time();
                                    $usercenter_scored_recordModel->add($cdata);
                                    $memscore = $this->usercenterdata['score'] + $cdata['score'];
                                    $memberlistModel->where(array('token' => $this->token, 'openid' => $wecha_id))->save(array('score' => $memscore));

                                    M('Lottery_record')->where(array('id' => $record['id']))->setInc('usenums');
                                    $record = M('Lottery_record')->where(array('id' => $record['id']))->find();
                                    $firstNum = intval($Lottery['fistnums']) - intval($Lottery['fistlucknums']);
                                    $secondNum = intval($Lottery['secondnums']) - intval($Lottery['secondlucknums']);
                                    $thirdNum = intval($Lottery['thirdnums']) - intval($Lottery['thirdlucknums']);
                                    $fourthNum = intval($Lottery['fournums']) - intval($Lottery['fourlucknums']);
                                    $fifthNum = intval($Lottery['fivenums']) - intval($Lottery['fivelucknums']);
                                    $sixthNum = intval($Lottery['sixnums']) - intval($Lottery['sixlucknums']);
                                    $multi = intval($Lottery['canrqnums']);//最多抽奖次数
                                    $prize_arr = array(
                                        '0' => array('id' => 1, 'prize' => '一等奖', 'v' => $firstNum, 'start' => 0, 'end' => $firstNum),
                                        '1' => array('id' => 2, 'prize' => '二等奖', 'v' => $secondNum, 'start' => $firstNum, 'end' => $firstNum + $secondNum),
                                        '2' => array('id' => 3, 'prize' => '三等奖', 'v' => $thirdNum, 'start' => $firstNum + $secondNum, 'end' => $firstNum + $secondNum + $thirdNum),
                                        '3' => array('id' => 4, 'prize' => '四等奖', 'v' => $fourthNum, 'start' => $firstNum + $secondNum + $thirdNum, 'end' => $firstNum + $secondNum + $thirdNum + $fourthNum),
                                        '4' => array('id' => 5, 'prize' => '五等奖', 'v' => $fifthNum, 'start' => $firstNum + $secondNum + $thirdNum + $fourthNum, 'end' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum),
                                        '5' => array('id' => 6, 'prize' => '六等奖', 'v' => $sixthNum, 'start' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum, 'end' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum + $sixthNum),
                                        '6' => array('id' => 7, 'prize' => '谢谢参与', 'v' => (intval($Lottery['allpeople'])) * $multi - ($firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum + $sixthNum), 'start' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum + $sixthNum, 'end' => intval($Lottery['allpeople']) * $multi)
                                    );


                                    foreach ($prize_arr as $key => $val) {
                                        $arr[$val['id']] = $val;
                                    }
                                    if ($Lottery['allpeople'] == 1) {

                                        if ($Lottery['fistlucknums'] <= $Lottery['fistnums']) {
                                            $rid = 1;
                                        } else {
                                            $rid = 4;
                                        }

                                    } else {
                                        $data['joinnum'] = $Lottery['joinnum'] + 1;
                                        $rid = $this->get_rand($arr, intval($Lottery['allpeople']) * $multi);
                                    }


                                    $winprize = $prize_arr[$rid - 1]['prize'];
                                    $zjl = false;

                                    switch ($rid) {
                                        case 1:

                                            if ($Lottery['fistlucknums'] > $Lottery['fistnums']) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {

                                                $zjl = true;
                                                M('Lottery')->where(array('id' => $id))->setInc('fistlucknums');
                                            }
                                            break;

                                        case 2:
                                            if ($Lottery['secondlucknums'] > $Lottery['secondnums']) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                //判断是否设置了2等奖&&数量
                                                if (empty($Lottery['second']) && empty($Lottery['secondnums'])) {
                                                    $zjl = false;
                                                    $winprize = '谢谢参与';
                                                } else { //输出中了二等奖
                                                    $zjl = true;
                                                    M('Lottery')->where(array('id' => $id))->setInc('secondlucknums');
                                                }

                                            }
                                            break;

                                        case 3:
                                            if ($Lottery['thirdlucknums'] > $Lottery['thirdnums']) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                if (empty($Lottery['third']) && empty($Lottery['thirdnums'])) {
                                                    $zjl = false;
                                                    $winprize = '谢谢参与';
                                                } else {
                                                    $zjl = true;
                                                    M('Lottery')->where(array('id' => $id))->setInc('thirdlucknums');
                                                }

                                            }
                                            break;

                                        case 4:
                                            if ($Lottery['fourlucknums'] > $Lottery['fournums']) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                if (empty($Lottery['fournums']) && empty($Lottery['fournums'])) {
                                                    $zjl = false;
                                                    $winprize = '谢谢参与';
                                                } else {
                                                    $zjl = true;
                                                    M('Lottery')->where(array('id' => $id))->setInc('fourlucknums');
                                                }

                                            }
                                            break;

                                        case 5:
                                            if ($Lottery['fivelucknums'] > $Lottery['fivenums']) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                if (empty($Lottery['fivenums']) && empty($Lottery['fivenums'])) {
                                                    $zjl = false;
                                                    $winprize = '谢谢参与';
                                                } else {
                                                    $zjl = true;
                                                    M('Lottery')->where(array('id' => $id))->setInc('fivelucknums');
                                                }

                                            }
                                            break;
                                        case 6:
                                            if ($Lottery['sixlucknums'] > $Lottery['sixnums']) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                if (empty($Lottery['sixnums']) && empty($Lottery['sixnums'])) {
                                                    $zjl = false;
                                                    $winprize = '谢谢参与';
                                                } else {
                                                    $zjl = true;
                                                    M('Lottery')->where(array('id' => $id))->setInc('sixlucknums');
                                                }

                                            }
                                            break;

                                        default:
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                            break;
                                    }
                                }
                            } else {
                                M('Lottery_record')->where(array('id' => $record['id']))->setInc('usenums');
                                $record = M('Lottery_record')->where(array('id' => $record['id']))->find();
                                $firstNum = intval($Lottery['fistnums']) - intval($Lottery['fistlucknums']);
                                $secondNum = intval($Lottery['secondnums']) - intval($Lottery['secondlucknums']);
                                $thirdNum = intval($Lottery['thirdnums']) - intval($Lottery['thirdlucknums']);
                                $fourthNum = intval($Lottery['fournums']) - intval($Lottery['fourlucknums']);
                                $fifthNum = intval($Lottery['fivenums']) - intval($Lottery['fivelucknums']);
                                $sixthNum = intval($Lottery['sixnums']) - intval($Lottery['sixlucknums']);
                                $multi = intval($Lottery['canrqnums']);//最多抽奖次数
                                $prize_arr = array(
                                    '0' => array('id' => 1, 'prize' => '一等奖', 'v' => $firstNum, 'start' => 0, 'end' => $firstNum),
                                    '1' => array('id' => 2, 'prize' => '二等奖', 'v' => $secondNum, 'start' => $firstNum, 'end' => $firstNum + $secondNum),
                                    '2' => array('id' => 3, 'prize' => '三等奖', 'v' => $thirdNum, 'start' => $firstNum + $secondNum, 'end' => $firstNum + $secondNum + $thirdNum),
                                    '3' => array('id' => 4, 'prize' => '四等奖', 'v' => $fourthNum, 'start' => $firstNum + $secondNum + $thirdNum, 'end' => $firstNum + $secondNum + $thirdNum + $fourthNum),
                                    '4' => array('id' => 5, 'prize' => '五等奖', 'v' => $fifthNum, 'start' => $firstNum + $secondNum + $thirdNum + $fourthNum, 'end' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum),
                                    '5' => array('id' => 6, 'prize' => '六等奖', 'v' => $sixthNum, 'start' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum, 'end' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum + $sixthNum),
                                    '6' => array('id' => 7, 'prize' => '谢谢参与', 'v' => (intval($Lottery['allpeople'])) * $multi - ($firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum + $sixthNum), 'start' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum + $sixthNum, 'end' => intval($Lottery['allpeople']) * $multi)
                                );


                                foreach ($prize_arr as $key => $val) {
                                    $arr[$val['id']] = $val;
                                }
                                if ($Lottery['allpeople'] == 1) {

                                    if ($Lottery['fistlucknums'] <= $Lottery['fistnums']) {
                                        $rid = 1;
                                    } else {
                                        $rid = 4;
                                    }

                                } else {
                                    $data['joinnum'] = $Lottery['joinnum'] + 1;
                                    $rid = $this->get_rand($arr, intval($Lottery['allpeople']) * $multi);
                                }


                                $winprize = $prize_arr[$rid - 1]['prize'];
                                $zjl = false;

                                switch ($rid) {
                                    case 1:

                                        if ($Lottery['fistlucknums'] > $Lottery['fistnums']) {
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        } else {

                                            $zjl = true;
                                            M('Lottery')->where(array('id' => $id))->setInc('fistlucknums');
                                        }
                                        break;

                                    case 2:
                                        if ($Lottery['secondlucknums'] > $Lottery['secondnums']) {
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        } else {
                                            //判断是否设置了2等奖&&数量
                                            if (empty($Lottery['second']) && empty($Lottery['secondnums'])) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else { //输出中了二等奖
                                                $zjl = true;
                                                M('Lottery')->where(array('id' => $id))->setInc('secondlucknums');
                                            }

                                        }
                                        break;

                                    case 3:
                                        if ($Lottery['thirdlucknums'] > $Lottery['thirdnums']) {
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        } else {
                                            if (empty($Lottery['third']) && empty($Lottery['thirdnums'])) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                $zjl = true;
                                                M('Lottery')->where(array('id' => $id))->setInc('thirdlucknums');
                                            }

                                        }
                                        break;

                                    case 4:
                                        if ($Lottery['fourlucknums'] > $Lottery['fournums']) {
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        } else {
                                            if (empty($Lottery['fournums']) && empty($Lottery['fournums'])) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                $zjl = true;
                                                M('Lottery')->where(array('id' => $id))->setInc('fourlucknums');
                                            }

                                        }
                                        break;

                                    case 5:
                                        if ($Lottery['fivelucknums'] > $Lottery['fivenums']) {
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        } else {
                                            if (empty($Lottery['fivenums']) && empty($Lottery['fivenums'])) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                $zjl = true;
                                                M('Lottery')->where(array('id' => $id))->setInc('fivelucknums');
                                            }

                                        }
                                        break;
                                    case 6:
                                        if ($Lottery['sixlucknums'] > $Lottery['sixnums']) {
                                            $zjl = false;
                                            $winprize = '谢谢参与';
                                        } else {
                                            if (empty($Lottery['sixnums']) && empty($Lottery['sixnums'])) {
                                                $zjl = false;
                                                $winprize = '谢谢参与';
                                            } else {
                                                $zjl = true;
                                                M('Lottery')->where(array('id' => $id))->setInc('sixlucknums');
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
                            $data['zjl'] = $zjl;
                            $data['wecha_id'] = $record['wecha_id'];
                            $data['lid'] = $record['lid'];
                            $data['winprize'] = $winprize;

                        }
                    }
			} //end if;
		} // end first if; 
		
		$data['usecout'] 	= intval($record['usenums']);
		$data['canrqnums']	= $Lottery['canrqnums'];
		$data['fist'] 		= $Lottery['fist'];
		$data['second'] 	= $Lottery['second'];
		$data['third'] 		= $Lottery['third'];
		$data['fistnums'] 	= $Lottery['fistnums'];
		$data['secondnums'] = $Lottery['secondnums'];
		$data['thirdnums'] 	= $Lottery['thirdnums'];
        $data['fournums'] = $Lottery['fournums'];
        $data['fivenums'] 	= $Lottery['fivenums'];
        $data['sixnums'] 	= $Lottery['sixnums'];
		$data['info']		= $Lottery['info'];
		$data['endinfo']		= $Lottery['endinfo'];
		$data['txt']		= $Lottery['txt'];
		$data['sttxt']		= $Lottery['sttxt'];
		$data['title']		= $Lottery['title'];
		$data['statdate']	= $Lottery['statdate'];
		$data['enddate']	= $Lottery['enddate'];
		$data['click'] = $Lottery['click']+1;
        M('Lottery')->where('id='.$Lottery['id'])->save($data);
	
		$this->assign('Zadan',$data);

		$prizeStr='<p>一等奖: '.$Lottery['fist'];
		if ($Lottery['displayjpnums']){
			$prizeStr.='奖品数量:'.$Lottery['fistnums'];
		}
		$prizeStr.='</p>';
		if ($Lottery['second']){
			$prizeStr.='<p>二等奖: '.$Lottery['second'];
			if ($Lottery['displayjpnums']){
				$prizeStr.='奖品数量:'.$Lottery['secondnums'];
			}
			$prizeStr.='</p>';
		}
		if ($Lottery['third']){
			$prizeStr.='<p>三等奖: '.$Lottery['third'];
			if ($Lottery['displayjpnums']){
				$prizeStr.='奖品数量:'.$Lottery['thirdnums'];
			}
			$prizeStr.='</p>';
		}
        if ($Lottery['four']){
            $prizeStr.='<p>四等奖: '.$Lottery['four'];
            if ($Lottery['displayjpnums']){
                $prizeStr.='奖品数量:'.$Lottery['fournums'];
            }
            $prizeStr.='</p>';
        }
        if ($Lottery['five']){
            $prizeStr.='<p>五等奖: '.$Lottery['five'];
            if ($Lottery['displayjpnums']){
                $prizeStr.='奖品数量:'.$Lottery['fivenums'];
            }
            $prizeStr.='</p>';
        }
        if ($Lottery['six']){
            $prizeStr.='<p>六等奖: '.$Lottery['six'];
            if ($Lottery['displayjpnums']){
                $prizeStr.='奖品数量:'.$Lottery['sixnums'];
            }
            $prizeStr.='</p>';
        }
		$this->assign('prizeStr',$prizeStr);
		
		$this->display();
		
	}
	protected function get_rand($proArr,$total) { 
		    $result = 7; 
		    $randNum = mt_rand(1, $total); 
		    foreach ($proArr as $k => $v) {
		    	
		    	if ($v['v']>0){//奖项存在或者奖项之外
		    		if ($randNum>$v['start']&&$randNum<=$v['end']){
		    			$result=$k;
		    			break;
		    		}
		    	}
		    }
		    WL($total . '|' . $randNum . '|' . print_r($proArr, true));
		    return $result; 
	}
	
	

	public function add(){
		if($_POST['action'] ==  'add'  ){
			$lid 				= $this->_post('lid');
			$wechaid 			= $this->_post('wechaid');
			$data['phone'] 		= $this->_post('tel');
			$data['wecha_name'] = $this->_post('wxname');
			$data['prize']		= $this->_post('prize');
			$data['islottery'] 	= 1;
			$data['time']		= time();
			$data['sn']			= $this->create_password();
			$rollback = M('Lottery_record')->where(array('lid'=> $lid,
				'wecha_id'=>$wechaid))->save($data);
			echo'{"success":1,"msg":"恭喜！尊敬的'.$data['wecha_name'].'请您保持手机通畅！请您牢记的领奖号:'.$data['sn'].'"}';
			exit;
		}
/*
		$record = M('Lottery_record');
		$data['phone'] 		= $this->_post('tel');
		$data['wecha_name'] = $this->_post('wxname');
		$data['islottery'] 	= 1;
		$data['time']		= time();
		$data['sn']			= uniqid();
		$rollback = $record->where(array('lid'=>$this->_post('lid') ,
				'wecha_id'=>$this->_post('wechaid') ))->save($data);
				
				*/
	}

    public function create_password($pw_length = 8){
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';

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