<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/10/27
 * Time: 11:37
 */
class IntegralshopAction extends BaseAction{
    public function index(){
        $token	= $this->_get('token');
        $openid	= $this->_get('openid');
        $model = M('Integralshop');
        $list = $model->where(array('tp_integralshop.token'=>$token,'tp_integralshop.is_up'=>1))->field('tp_integralshop.*,l.name')->join('left join tp_usercenter_level as l on tp_integralshop.extent = l.id ')->order('starttime asc')->select();
        foreach($list as $k=>$val){
            $ikucount =intval($val['num']) - intval(M('Integralshop_individual')->where(array('lid'=>$val['id'],'token'=>$token))->count());

            $list[$k]['ikucount'] = $ikucount;
        }
        $this->assign('data',$list);
        if($this->token=='55cad4ba46c41a8fde9c84274e36fa83'){
            $this->assign('foot',3);
        }
        $this->display();
    }

    public function exchange(){
        $model =M('Usercenter_memberlist');
        $where['openid'] = $_GET['openid'];
        $snnum = time().rand(1000,9999);  //生成sn码
		//海南生活
		if($this->token == '5d8a87bab30de695954b17fc835b9d12'){
            $aIntegral= M('Integralshop')->where(array('id'=>$_POST['exc_id']))->find();
            $startime  = strtotime($aIntegral['starttime']);
            $endtime = strtotime($aIntegral['endtime']);
            if($startime>= time()){
                $this->error('该礼品现在还未开启兑换，请关注！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }elseif(time()>=$endtime){
                $this->error('该活动已结束，请再去查看其他的礼品！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }
            $score =M('hn_users')->where($where)->getField('yonjing');
            $croe = intval($_POST['point']);
            if($score < $croe){
                $this->error("对不起，佣金不够哦！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }else{
                $conn = M('Integralshop');
                $where_1['id']=$_POST['exc_id'];
                $a = $conn->where(array('token'=>$this->token,'id'=>$_POST['exc_id']))->getField('degree');//在礼品积分表里查找礼品可兑换的次数
                $id = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id');//在礼品积分表里查找礼品可兑换的次数
                $result = M('dy_score');
                $gift = M('Integralshop_individual');
                $term_1 = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'lid'=>$_POST['exc_id']);
                $term_2 = array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'lid'=>$_POST['exc_id'],'time'=>time(),'snnum'=>$snnum);
                $term = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>7,'score'=>-$croe,'addtime'=>date("Y-m-d H:i:s"));
                $count = $gift->where($term_1)->count('lid');

                if($count >= $a){
                    $this->error("对不起，您兑换此活动的机会已经用完！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }else{
                    //总积分 = 兑换前积分 - 兑换礼品的积分
                    $data['score'] = $score - $croe;
                    $arr = M('hn_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->setDec('yonjing',$croe);//修改个人中心的会员积分
                    $m = $result->data($term)->add();//添加之‘Usercenter_score_record’的积分时间记录
                    $l = $gift->data($term_2)->add(); //
                    /*$oUserscore = M('hn_shop'); //个人积分记录表
                    $oUserscore->add(array(
                        'uid'=>$id,
                        'openid'=>$this->openid,
                        'token' => $this->token,
                        'address'=>'122',
                        'yonjing'=>$croe,
                        'addtime'=>time(),
                        'name'=>'实得分',
                        'phone'=>'实得dsd分',
					));*/
                    if($arr && $m && $l ){
                        $usenum = intval(M('Integralshop_individual')->where(array('lid'=>$_POST['exc_id'],'token'=>$this->token))->count());
                        if($aIntegral['num'] <= $usenum){
                            M('$conn')->where(array('token'=>$this->token,'id'=>$_POST['exc_id']))->save(array('is_up'=>2));
                        }
                        $this->success("扣除成功！",U(MODULE_NAME.'/reveal',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                    }else{
                        $this->error("系统繁忙扣除失败！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                    }
                }


            }

        }
		//海南生活结束
		
        if($this->token == '3db7fee419649f8be761dfc4f6b42ecc'){
            $aIntegral= M('Integralshop')->where(array('id'=>$_POST['exc_id']))->find();
            $startime  = strtotime($aIntegral['starttime']);
            $endtime = strtotime($aIntegral['endtime']);
            if($startime>= time()){
                $this->error('该礼品现在还未开启兑换，请关注！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }elseif(time()>=$endtime){
                $this->error('该活动已结束，请再去查看其他的礼品！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }
            $score = M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('score');
            $croe = intval($_POST['point']);
            if($score < $croe){
                $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                    ['uid'],'openid'=>$this->_get('openid'))));exit;
            }else{
                $conn = M('Integralshop');
                $where_1['id']=$_POST['exc_id'];
                $a = $conn->where(array('token'=>$this->token,'id'=>$_POST['exc_id']))->getField('degree');//在礼品积分表里查找礼品可兑换的次数
                $result = M('dy_score');
                $gift = M('Integralshop_individual');
                $term_1 = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'lid'=>$_POST['exc_id']);
                $term_2 = array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'lid'=>$_POST['exc_id'],'time'=>time(),'snnum'=>$snnum);
                $term = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>7,'score'=>-$croe,'addtime'=>date("Y-m-d H:i:s"));
                $count = $gift->where($term_1)->count('lid');

                if($count >= $a){
                    $this->error("对不起，您兑换此活动的机会已经用完！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }else{
                    //总积分 = 兑换前积分 - 兑换礼品的积分
                    $data['score'] = $score - $croe;
                    $arr = M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->setDec('score',$croe);//修改个人中心的会员积分
                    $m = $result->data($term)->add();//添加之‘Usercenter_score_record’的积分时间记录
                    $l = $gift->data($term_2)->add(); //
                    if($arr && $m && $l ){
                        $usenum = intval(M('Integralshop_individual')->where(array('lid'=>$_POST['exc_id'],'token'=>$this->token))->count());
                        if($aIntegral['num'] <= $usenum){
                            M('$conn')->where(array('token'=>$this->token,'id'=>$_POST['exc_id']))->save(array('is_up'=>2));
                        }
                        $this->success("扣除成功！",U(MODULE_NAME.'/reveal',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                    }else{
                        $this->error("系统繁忙扣除失败！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                    }
                }     


            }
        }
        /*易派单*/
        //if($this->token == '5d8a87bab30de695954b17fc835b9d12'){
        if($this->token == '8a71b21a11dd5212bd74cee41dafab64'){
            $aIntegral= M('Integralshop')->where(array('id'=>$_POST['exc_id']))->find();
            $startime  = strtotime($aIntegral['starttime']);
            $endtime = strtotime($aIntegral['endtime']);
            if($startime>= time()){
                $this->error('该礼品现在还未开启兑换，请关注！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }elseif(time()>=$endtime){
                $this->error('该活动已结束，请再去查看其他的礼品！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }
            $score =M('Media_users')->where($where)->getField('money');
            $croe = intval($_POST['point']);
            if($score < $croe){
                $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }else{
                $conn = M('Integralshop');
                $where_1['id']=$_POST['exc_id'];
                $a = $conn->where(array('token'=>$this->token,'id'=>$_POST['exc_id']))->getField('degree');//在礼品积分表里查找礼品可兑换的次数
                $result = M('dy_score');
                $gift = M('Integralshop_individual');
                $term_1 = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'lid'=>$_POST['exc_id']);
                $term_2 = array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'lid'=>$_POST['exc_id'],'time'=>time(),'snnum'=>$snnum);
                $term = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>7,'score'=>-$croe,'addtime'=>date("Y-m-d H:i:s"));
                $count = $gift->where($term_1)->count('lid');

                if($count >= $a){
                    $this->error("对不起，您兑换此活动的机会已经用完！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }else{
                    //总积分 = 兑换前积分 - 兑换礼品的积分
                    $data['score'] = $score - $croe;
                    $arr = M('Media_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->setDec('money',$croe);//修改个人中心的会员积分
                    $m = $result->data($term)->add();//添加之‘Usercenter_score_record’的积分时间记录
                    $l = $gift->data($term_2)->add(); //
                    $oUserscore = M('Media_user_score'); //个人积分记录表
                    $oUserscore->add(array(
                        'cid'=>'',
                        'openid'=>$this->openid,
                        'token' => $this->token,
                        'gopenid'=>'',
                        'score'=>$croe,
                        'add_time'=>date('Y-m-d H:i:s'),
                        'type'=>4,
                        'date'=>date('Y-m-d')));
                    if($arr && $m && $l ){
                        $usenum = intval(M('Integralshop_individual')->where(array('lid'=>$_POST['exc_id'],'token'=>$this->token))->count());
                        if($aIntegral['num'] <= $usenum){
                            M('$conn')->where(array('token'=>$this->token,'id'=>$_POST['exc_id']))->save(array('is_up'=>2));
                        }
                        $this->success("扣除成功！",U(MODULE_NAME.'/reveal',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                    }else{
                        $this->error("系统繁忙扣除失败！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                    }
                }


            }

        }


       if($this->token=='55cad4ba46c41a8fde9c84274e36fa83'){//这里如多分期  公共号的积分系统
      //  if($this->token=='5d8a87bab30de695954b17fc835b9d12'){
            //p($_POST);die;
            //积分jifeng_add+score-jifeng_back
            $score_arr = M('Credit_users')->field('jifeng_back,jifeng_add')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
            $score1=M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('score');
            $score=$score_arr['jifeng_add']+$score1-$score_arr['jifeng_back'];
            $croe = $_POST['point'];
            if($score < $croe){
                $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }else{
                $gift = M('Integralshop_individual');
                $term_2 = array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'lid'=>$_POST['exc_id'],'time'=>time(),'snnum'=>$snnum);
                $l = $gift->data($term_2)->add(); //
              //  $gift->data($term_2)->add();
                //$data['jifeng_back'] = $croe;
               $arr = M('Credit_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->setInc('jifeng_back',$croe);//修改个人中心的会员积分
                //在记录表里插一条数据
                $data3['token']=$this->token;
                $data3['openid']=$this->openid;
                $data3['add_time']=time();
                $data3['score']=(int)('-'.$croe);
                $data3['type']=2;
                M('Loan_score_jl')->add($data3);
                if($arr && $l ){
                    $this->success("扣除成功！",U(MODULE_NAME.'/reveal',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }else{
                    $this->error("系统繁忙扣除失败！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }
            }
        }


        
        $arr = $model->where($where)->find();
        if(!$arr){
            $this->error("您还不是会员，请注册！",U('Wap/Usercenter/index',array('token'=>$_GET['token'],'id'=>$_GET
            ['uid'],'openid'=>$this->_get('openid'))));exit;
    	    
    	}
	    if($this->token != 'b47234062c938be7ad20f0f82f0241a2'){
	        if(!$arr){
		    //o2o商城关联特殊处理
    		    if($this->token != 'b47234062c938be7ad20f0f82f0241a2'){
    		            $this->error("您还不是会员，请注册！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
    		            ['uid'],'openid'=>$this->_get('openid'))));exit;
    		    }
	        }else{

	            $list = $model->where($where)->getField('score');
                $dyb_score=M('Shop_users')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->getField('score');//社区积分
             //   p($dyb_score);die;
	            $croe = $_POST['point'];
		    
		        $aIntegral= M('Integralshop')->where(array('id'=>$_POST['exc_id']))->find();
	            $startime  = strtotime($aIntegral['starttime']);
	            $endtime = strtotime($aIntegral['endtime']);
    		    if($startime>= time()){
    		        $this->error('该礼品现在还未开启兑换，请关注！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
    		        ['uid'],'openid'=>$this->_get('openid'))));exit;
    		    }elseif(time()>=$endtime){
    		        $this->error('该活动已结束，请再去查看其他的礼品！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
    		        ['uid'],'openid'=>$this->_get('openid'))));exit;
    		    }
	            /*根据活动找到他所需的会员等级*/
	            $suoxusc = M('Usercenter_level')->where(array('id'=>$aIntegral['extent']))->find();
	            $zuserscore = M('Usercenter_score_record')->field('sum(score) as allscore')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('gt',0),'type'=>array('not in','99')))->select();
	            $fscore = intval(M('Usercenter_score_record')->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>99))->sum('score'));
	            $userscore = $zuserscore[0]['allscore'] + $fscore;
	            if($userscore<$suoxusc['score']){
	               $this->error('您的等级暂未能兑换该礼品！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
	               ['uid'],'openid'=>$this->_get('openid'))));exit;
	            }

	            if(($list+$dyb_score) < $croe){
	                $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
	                ['uid'],'openid'=>$this->_get('openid'))));exit;
	            }else{
	                $conn = M('Integralshop');
	                $where_1['id']=$_POST['exc_id'];
	                $a = $conn->where(array('token'=>$this->token,'id'=>$_POST['exc_id']))->getField('degree');//在礼品积分表里查找礼品可兑换的次数
	               // print_r($a);exit;
	                $result = M('Usercenter_score_record');
	                $gift = M('Integralshop_individual');
	                $term_1 = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'lid'=>$_POST['exc_id']);
	                $term_2 = array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'lid'=>$_POST['exc_id'],'time'=>time());
	                $term = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>8,'score'=>-$croe,'add_time'=>time(),'titleid'=>$_POST['exc_id']);
	                $count = $gift->where($term_1)->count('lid');
	               // print_r($count);exit;
	                if($a <= $count){
	                    $this->error("对不起，您兑换此活动的机会已经用完！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
	                }else{
	                    //总积分 = 兑换前积分 - 兑换礼品的积分
                        //先扣会员中心表的积分，扣完再扣社区表的积分
                        if($list - $croe>=0){
                            $data['score'] = $list - $croe;
                            $arr = $model->where($where)->save($data);//修改个人中心的会员积分
                        }else{//不够再来扣社会积分
                            $hehe=$dyb_score-($croe-$list);
                            $arr = $model->where($where)->save(array('score'=>0));
                            M('Shop_users')->where(array('token'=>$this->token,'openid'=>$_GET['openid']))->save(array('score'=>$hehe));
                        }

	                    $m = $result->data($term)->add();//添加之‘Usercenter_score_record’的积分时间记录
	                    $l = $gift->data($term_2)->add(); //
	                    if($arr && $m && $l ){
	                        $this->success("扣除成功！",U(MODULE_NAME.'/reveal',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));
	                    }
	                }

	            }

		    }
      }else{

            $alistInfo =M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();

	        $list = $alistInfo['score'];
            $croe = $_POST['point'];
            $aIntegral= M('Integralshop')->where(array('id'=>$_POST['exc_id']))->find();
            $startime  = strtotime($aIntegral['starttime']);
            $endtime = strtotime($aIntegral['endtime']);
    	    if($startime>= time()){
    	        $this->error('该礼品现在还未开启兑换，请关注！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
    	        ['uid'],'openid'=>$this->_get('openid'))));exit;
    	    }elseif(time()>=$endtime){
    	        $this->error('该活动已结束，请再去查看其他的礼品！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
    	        ['uid'],'openid'=>$this->_get('openid'))));exit;
    	    }
            /*根据活动找到他所需的会员等级*/
            $suoxusc = M('Usercenter_level')->where(array('id'=>$aIntegral['extent']))->find();
            $zuserscore = M('Usercenter_score_record')->field('sum(score) as allscore')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('gt',0),'type'=>array('not in','99')))->select();
            $fscore = intval(M('Usercenter_score_record')->where(array('token'=>$this->token,'openid'=>$this->openid,'type'=>99))->sum('score'));
            $userscore = $zuserscore[0]['allscore'] + $fscore;

            if($userscore<$suoxusc){
               $this->error('您的等级暂未能兑换该礼品！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
               ['uid'],'openid'=>$this->_get('openid'))));exit;
            }
            if($list < $croe){
                $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET
                ['uid'],'openid'=>$this->_get('openid'))));exit;
            }else{
                $conn = M('Integralshop');
                $where_1['id']=$_POST['exc_id'];
                $a = $conn->where(array('token'=>$this->token,'id'=>$_POST['exc_id']))->getField('degree');//在礼品积分表里查找礼品可兑换的次数
                $result = M('Usercenter_score_record');
                $gift = M('Integralshop_individual');
                $term_1 = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'lid'=>$_POST['exc_id']);
                $term_2 = array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'lid'=>$_POST['exc_id'],'time'=>time(),'snnum'=>$snnum);
                $term = array('token'=>$_GET['token'],'openid'=>$_GET['openid'],'type'=>8,'score'=>-$croe,'add_time'=>time(),'titleid'=>$_POST['exc_id']);
                $count = $gift->where($term_1)->count('lid');
                if($a <= $count){
                    $this->error("对不起，您兑换此活动的机会已经用完！",U(MODULE_NAME.'/index',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));exit;
                }else{
                    //总积分 = 兑换前积分 - 兑换礼品的积分
                    $data['score'] = $list - $croe;
                    $arr = M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);//修改个人中心的会员积分
                    $m = $result->data($term)->add();//添加之‘Usercenter_score_record’的积分时间记录
                    $l = $gift->data($term_2)->add(); //
                    if($arr && $m && $l ){
                        $this->success("扣除成功！",U(MODULE_NAME.'/reveal',array('token'=>$_GET['token'],'id'=>$_GET['uid'],'openid'=>$this->_get('openid'))));
                    }
                }

            }
      
       }
    }

        //兑换列表
    public function reveal(){
        $model =  M('Integralshop_individual');
        $where = array('tp_integralshop_individual.openid'=>$_GET['openid']);
        $list = $model->where($where)->field('tp_integralshop_individual.*,l.title,l.integral')->join('left join tp_integralshop as l
on tp_integralshop_individual.lid = l.id ')->select();
        foreach($list as $k=>$val){
            $shop = M('Shop')->where(array('id'=>$val['shop_id']))->find();
            $list[$k]['shop'] = $shop['username'];
        }

        $sCount=M('Dy_score')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('gt',0)))->getField('sum(score) as sum');//得总积分
        $xiaofeijifen=M('Dy_score')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('lt',0)))->getField('sum(score) as sum');//得总积分
        $user = M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign('dengji',$this->getUserLevel($this->token,$this->openid));
        $this->assign('sCount',$sCount);
        $this->assign('xiaofeijifen',$xiaofeijifen);
        $this->assign('user',$user);
        $this->assign('list',$list);
        $this->display();

    }
    //确认竞换


    public function getUserLevel($token,$openid){
        $scoredata = M('dy_score')->field('sum(score) as allscore')->where(array('token'=>$token,'openid'=>$openid,'score'=>array('gt',0)))->select();
        $users = M('shop_users')->where(array('token'=>$token,'openid'=>$openid))->find();
        $score = $scoredata[0]['allscore']+$users['other_score'];
        $where['token']=$token;
        $where['scope']=array('elt', $score);
        $userlevel_data = M("Shopgrade")->where($where)->order('scope desc')->limit(1)->find();
        return $userlevel_data['name'];
    }


}