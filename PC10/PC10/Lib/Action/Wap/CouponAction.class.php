<?php
class CouponAction extends BaseAction{

    public $guize = null;
    public $usercenterdata = null;

    public function _initialize(){
        parent::_initialize();
        if($guize = M('Usercenter_score_guize')->where(array('token'=>$this->token,'app_id'=>19))->find()){
            $this->guize = $guize;
            $usersModel = M('Wxusers');
            $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $usercenterdata = M('Usercenter_memberlist')->where(array('uid'=>$wxuser['id'],'openid'=>$this->openid))->find();
            $this->usercenterdata = $usercenterdata;
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
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$wecha_id	= $this->_get('wecha_id');
		$id 		= $this->_get('id');
		//0.判断是否有该用户记录,没有则记录		
		$redata		= M('Lottery_record');
		$where 		= array('token'=>$token,'wecha_id'=>$wecha_id,'lid'=>$id);
		$record 	= $redata->where($where)->find();		
		if($record == Null){
			$redata->add($where);
			$record = $redata->where($where)->find();
		}
		$Lottery 	= M('Lottery')->where(array('id'=>$id,'token'=>$token,'type'=>3))->find();
		//var_dump($Lottery);
		//0. 判断优惠券是否领完了
        if($this->guize['is_score'] == 1){
            if($this->usercenterdata['score'] < $this->guize['need_score']){
                $data['end'] = 1;
                $data['msg'] = "尊敬的用户您的积分不够了哦,赶快去用户中心签到获取积分吧";
            }else{

                if($Lottery['fistlucknums']  == $Lottery['fistnums'] &&
                    $Lottery['secondlucknums'] == $Lottery['secondnums'] &&
                    $Lottery['thirdlucknums']  == $Lottery['thirdnums'] && $Lottery['fourlucknums']  == $Lottery['fournums']
                    && $Lottery['fivelucknums']  == $Lottery['fivenums'] && $Lottery['sixlucknums']  == $Lottery['sixnums']){
                    $data['end'] = 1;
                    $data['msg'] = "您来晚了,优惠券已经被领完了.";

                }else{

                    if ($Lottery['enddate'] < time()) { //过期
                        $data['end'] = 2;
                        $data['endinfo'] = $Lottery['endinfo'];
                        $data['endimg']  = empty($Lottery['endpicurl']) ? 1 : $Lottery['endpicurl'];
                    }else{
                        //2.活动还没过期,接着是否领过了
                        if ($record['islottery'] == 1) {
                            $data['end'] = 3;
                            $data['msg'] = "您已经领取过优惠券了";
                            $data['wxname']=$record['wecha_name'];
                            $data['sn']  = $record['sn'];
                            $data['myprize'] 	= $record['prize'];
                        }else{

                            //是否已经够次数
                            if ($record['usenums'] >= $Lottery['canrqnums'] ) {
                                $data['prizetype'] = 7; //啥都没了
                                $data['zjl']	  = 0;
                                $data['usenums']  = $record['usenums'];
                                $data['winprize']	   = "抽奖次数已经达到";
                                //exit;
                            }else{


                                $usercenter_scored_recordModel = M('Usercenter_score_record');
                                $memberlistModel = M("Usercenter_memberlist");
                                $cdata['token'] = $this->token;
                                $cdata['openid'] = $wecha_id;
                                $cdata['type'] = 6;
                                $cdata['score'] = -($this->guize['need_score']);
                                $cdata['add_time'] = time();
                                $usercenter_scored_recordModel->add($cdata);
                                $memscore = $this->usercenterdata['score']+$cdata['score'];
                                $memberlistModel->where(array('token'=>$this->token,'openid'=>$wecha_id))->save(array('score'=>$memscore));

                                //3.没有领过,次数没达到,开始随机发放优惠券
                                $db=M('Lottery');
                                $data['joinnum'] = $Lottery['joinnum']+1;
                                $db->where('id='.$Lottery['id'])->save($data);
                                M('Lottery_record')->where(array('id'=>$record['id']))->setInc('usenums');
                                $record = M('Lottery_record')->where(array('id'=>$record['id']))->find();
                                //排除没有设置的优惠券
                                //奖品数 != 已经领取该奖品数 => 还有奖品
                                if ($Lottery['thirdlucknums'] != $Lottery['thirdnums']) {
                                    if(!empty($Lottery['third'])){
                                        $data['prizetype'] = 3;
                                        $data['winprize']	   = $Lottery['third'];
                                        $data['zjl']	   = 1;
                                        $data['sncode'] = uniqid();
                                        M('Lottery')->where(array('id'=>$id))->setInc('thirdlucknums');
                                    }else{
                                        $data['prizetype']= 7; //啥都没了
                                        $data['zjl']	  = 0;
                                        $data['winprize'] = "这次真不走运.";
                                    }

                                }elseif ($Lottery['secondlucknums'] != $Lottery['secondnums']) {
                                    if(!empty($Lottery['second'])){
                                        $data['prizetype'] = 2;
                                        $data['winprize']  = $Lottery['second'];
                                        $data['zjl']	   = 1;
                                        $data['sncode']    = uniqid();
                                        M('Lottery')->where(array('id'=>$id))->setInc('secondlucknums');
                                    }else{
                                        $data['prizetype'] = 7; //啥都没了
                                        $data['zjl']	  = 0;
                                        $data['winprize']	   = "这次真不走运.";
                                    }

                                }elseif ($Lottery['fistlucknums']  != $Lottery['fistnums']){
                                    if(!empty($Lottery['fist'])){
                                        $data['prizetype'] = 1;
                                        $data['sncode'] = uniqid();
                                        $data['winprize']	   = $Lottery['fist'];
                                        $data['zjl']	   = 1;
                                        M('Lottery')->where(array('id'=>$id))->setInc('fistlucknums');
                                    }else{
                                        $data['prizetype'] = 7; //啥都没了
                                        $data['zjl']	  = 0;
                                        $data['winprize']	   = "这次真不走运.";
                                    }

                                }elseif ($Lottery['fourlucknums']  != $Lottery['fournums']){
                                    if(!empty($Lottery['four'])){
                                        $data['prizetype'] = 4;
                                        $data['sncode'] = uniqid();
                                        $data['winprize']	   = $Lottery['four'];
                                        $data['zjl']	   = 1;
                                        M('Lottery')->where(array('id'=>$id))->setInc('fourlucknums');
                                    }else{
                                        $data['prizetype'] = 7; //啥都没了
                                        $data['zjl']	  = 0;
                                        $data['winprize']	   = "这次真不走运.";
                                    }

                                }elseif ($Lottery['fivelucknums']  != $Lottery['fivenums']){
                                    if(!empty($Lottery['five'])){
                                        $data['prizetype'] = 5;
                                        $data['sncode'] = uniqid();
                                        $data['winprize']	   = $Lottery['five'];
                                        $data['zjl']	   = 1;
                                        M('Lottery')->where(array('id'=>$id))->setInc('fivelucknums');
                                    }else{
                                        $data['prizetype'] = 7; //啥都没了
                                        $data['zjl']	  = 0;
                                        $data['winprize']	   = "这次真不走运.";
                                    }

                                }elseif ($Lottery['sixlucknums']  != $Lottery['sixnums']){
                                    if(!empty($Lottery['six'])){
                                        $data['prizetype'] = 6;
                                        $data['sncode'] = uniqid();
                                        $data['winprize']	   = $Lottery['six'];
                                        $data['zjl']	   = 1;
                                        M('Lottery')->where(array('id'=>$id))->setInc('sixlucknums');
                                    }else{
                                        $data['prizetype'] = 7; //啥都没了
                                        $data['zjl']	  = 0;
                                        $data['winprize']	   = "这次真不走运.";
                                    }

                                }else{
                                    $data['prizetype'] = 7; //啥都没了
                                    $data['zjl']	   = 0;
                                    $data['winprize']	   = "真心木有了,下次来早点吧";
                                }

                            }//以上没领过
                        }//在没达到次数
                    }

                }

            }
        }else{

            if($Lottery['fistlucknums']  == $Lottery['fistnums'] &&
               $Lottery['secondlucknums'] == $Lottery['secondnums'] &&
               $Lottery['thirdlucknums']  == $Lottery['thirdnums'] && $Lottery['fourlucknums']  == $Lottery['fournums']
            && $Lottery['fivelucknums']  == $Lottery['fivenums'] && $Lottery['sixlucknums']  == $Lottery['sixnums']){
               $data['end'] = 1;
               $data['msg'] = "您来晚了,优惠券已经被领完了.";

            }else{

                if ($Lottery['enddate'] < time()) { //过期
                     $data['end'] = 2;
                     $data['endinfo'] = $Lottery['endinfo'];
                     $data['endimg']  = empty($Lottery['endpicurl']) ? 1 : $Lottery['endpicurl'];
                }else{
                    //2.活动还没过期,接着是否领过了
                    if ($record['islottery'] == 1) {
                         $data['end'] = 3;
                         $data['msg'] = "您已经领取过优惠券了";
                         $data['wxname']=$record['wecha_name'];
                         $data['sn']  = $record['sn'];
                         $data['myprize'] 	= $record['prize'];
                    }else{

                        //是否已经够次数
                        if ($record['usenums'] >= $Lottery['canrqnums'] ) {
                            $data['prizetype'] = 7; //啥都没了
                            $data['zjl']	  = 0;
                            $data['usenums']  = $record['usenums'];
                            $data['winprize']	   = "抽奖次数已经达到";
                            //exit;
                        }else{
                            //3.没有领过,次数没达到,开始随机发放优惠券
                                              $db=M('Lottery');
                                             $data['joinnum'] = $Lottery['joinnum']+1;
                                              $db->where('id='.$Lottery['id'])->save($data);
                            M('Lottery_record')->where(array('id'=>$record['id']))->setInc('usenums');
                            $record = M('Lottery_record')->where(array('id'=>$record['id']))->find();
                            //排除没有设置的优惠券
                            //奖品数 != 已经领取该奖品数 => 还有奖品
                            if ($Lottery['thirdlucknums'] != $Lottery['thirdnums']) {
                                if(!empty($Lottery['third'])){
                                    $data['prizetype'] = 3;
                                    $data['winprize']	   = $Lottery['third'];
                                    $data['zjl']	   = 1;
                                    $data['sncode'] = uniqid();
                                    M('Lottery')->where(array('id'=>$id))->setInc('thirdlucknums');
                                }else{
                                    $data['prizetype']= 7; //啥都没了
                                    $data['zjl']	  = 0;
                                    $data['winprize'] = "这次真不走运.";
                                }

                            }elseif ($Lottery['secondlucknums'] != $Lottery['secondnums']) {
                                if(!empty($Lottery['second'])){
                                    $data['prizetype'] = 2;
                                    $data['winprize']  = $Lottery['second'];
                                    $data['zjl']	   = 1;
                                    $data['sncode']    = uniqid();
                                    M('Lottery')->where(array('id'=>$id))->setInc('secondlucknums');
                                }else{
                                    $data['prizetype'] = 7; //啥都没了
                                    $data['zjl']	  = 0;
                                    $data['winprize']	   = "这次真不走运.";
                                }

                            }elseif ($Lottery['fistlucknums']  != $Lottery['fistnums']){
                                if(!empty($Lottery['fist'])){
                                    $data['prizetype'] = 1;
                                    $data['sncode'] = uniqid();
                                    $data['winprize']	   = $Lottery['fist'];
                                    $data['zjl']	   = 1;
                                    M('Lottery')->where(array('id'=>$id))->setInc('fistlucknums');
                                }else{
                                    $data['prizetype'] = 7; //啥都没了
                                    $data['zjl']	  = 0;
                                    $data['winprize']	   = "这次真不走运.";
                                }

                            }elseif ($Lottery['fourlucknums']  != $Lottery['fournums']){
                                if(!empty($Lottery['four'])){
                                    $data['prizetype'] = 4;
                                    $data['sncode'] = uniqid();
                                    $data['winprize']	   = $Lottery['four'];
                                    $data['zjl']	   = 1;
                                    M('Lottery')->where(array('id'=>$id))->setInc('fourlucknums');
                                }else{
                                    $data['prizetype'] = 7; //啥都没了
                                    $data['zjl']	  = 0;
                                    $data['winprize']	   = "这次真不走运.";
                                }

                            }elseif ($Lottery['fivelucknums']  != $Lottery['fivenums']){
                                if(!empty($Lottery['five'])){
                                    $data['prizetype'] = 5;
                                    $data['sncode'] = uniqid();
                                    $data['winprize']	   = $Lottery['five'];
                                    $data['zjl']	   = 1;
                                    M('Lottery')->where(array('id'=>$id))->setInc('fivelucknums');
                                }else{
                                    $data['prizetype'] = 7; //啥都没了
                                    $data['zjl']	  = 0;
                                    $data['winprize']	   = "这次真不走运.";
                                }

                            }elseif ($Lottery['sixlucknums']  != $Lottery['sixnums']){
                                if(!empty($Lottery['six'])){
                                    $data['prizetype'] = 6;
                                    $data['sncode'] = uniqid();
                                    $data['winprize']	   = $Lottery['six'];
                                    $data['zjl']	   = 1;
                                    M('Lottery')->where(array('id'=>$id))->setInc('sixlucknums');
                                }else{
                                    $data['prizetype'] = 7; //啥都没了
                                    $data['zjl']	  = 0;
                                    $data['winprize']	   = "这次真不走运.";
                                }

                            }else{
                                $data['prizetype'] = 7; //啥都没了
                                $data['zjl']	   = 0;
                                $data['winprize']	   = "真心木有了,下次来早点吧";
                            }

                       }//以上没领过
                    }//在没达到次数
                }

            }
        }
	
		$data['token'] 		= $token;
		$data['wecha_id']	= $record['wecha_id'];		
		$data['lid']		= $record['lid'];
		$data['rid']		= $record['id']; 
		$data['usenums']	= $record['usenums'];
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
		$data['aginfo']		= $Lottery['aginfo'];
		$data['txt']		= $Lottery['txt'];
		$data['title']		= $Lottery['title'];
		$data['statdate']	= $Lottery['statdate'];
		$data['enddate']	= $Lottery['enddate'];
                   $db=M('Lottery'); 
                    $data['click'] = $Lottery['click']+1;
                    $db->where('id='.$Lottery['id'])->save($data);		
		$this->assign('Coupon',$data);
		//var_dump($data);exit();
		$this->display();
	}
	
	//中奖后填写信息
	public function add(){
		 if($_POST['action'] ==  'add' ){
			$lid 				= $this->_post('lid');
			$wechaid 			= $this->_post('wechaid');
			$data['sn']			= $this->_post('sncode');
			$data['phone'] 		= $this->_post('tel');
			$data['prize']		= $this->_post('winprize');
			$data['wecha_name'] = $this->_post('wxname');
			$data['time']		= time(); 
			$data['islottery']	= 1;
			$data['usenums']	= 1;			

			$rollback = M('Lottery_record')->where(array('lid'=> $lid,
				'wecha_id'=>$wechaid))->save($data);
			
			echo'{"success":1,"msg":"恭喜！尊敬的<font color=red>'.$data['wecha_name'].'</font>请您保持手机通畅！你的领奖序号:<font color=red>'.$data['sn'].'</font>"}';
			exit;	
		}
	}
}
	
?>