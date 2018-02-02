<?php
class MemberAction extends UserAction{

public function cardrec(){

		$token = $this->_post('token');
		$wecha_id = $this->_post('wecha_id');
		$truename = $this->_post('truename');
		

		$cardre = M('Member_card_sign')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->select();
		$this->assign('cardre',$cardre);
		$this->assign('truename',$truename);
		//var_dump($tbsign);
		$this->display();
	}

	public function index(){
		$sql=M('Member');
		$data['token']=$this->_get('token');
		$data['uid']=session('uid');
		$member=$sql->field('homepic')->where($data)->find();
		$this->assign('member',$member);
		$list=M('Userinfo')->where(array('token'=>$data['token']))->select();
		
		if(IS_POST){
			 
			$key = $this->_post('searchkey');
			if(empty($key)){
				$this->error2("关键词不能为空.");
			}
			$map['token'] = $this->get('token'); 
			$map['tel|wechaname'] = array('like',"%$key%"); 
			$list = M('Userinfo')->where($map)->select();
			 
			 
		}
		$this->assign('list',$list);
		$tbsign = M('Member_card_sign')->where(array('token'=>$data['token']))->select();
		$this->assign('tbsign',$tbsign);
		//var_dump($tbsign);
		$this->display();
	}
	public function add(){
		$sql=M('Member');
		$data['token']=$this->_get('token');
		$data['uid']=session('uid');
		$member=$sql->field('id')->where($data)->find();
		$pic['homepic']=$this->_post('homepic');
		if($member!=false){
			$back=$sql->where($data)->save($pic);
			if($back){
				$this->success2('更新成功');
			}else{
				$this->error2('服务器繁忙，请稍后再试1');
			}
		}else{
			$data['homepic']=$pic['homepic'];
			$back=$sql->add($data);
			if($back){
				$this->success2('更新成功');
			}else{
				$this->error2('服务器繁忙，请稍后再试');
			}
		}
	
	}
	public function del(){
		$data['token']=$this->_get('token');
		$data['id']=$this->_get('id');
		$back=M('Userinfo')->where($data)->delete();
		if($back){
			$this->success2('操作成功');
		}else{
			$this->error2('服务器繁忙，请稍候再试');
		} 
	}

	//------------------------------------------
	// 添加消费积分记录
	//------------------------------------------

	public function edit(){

		if(!IS_POST){
			$this->error2('没有提交任何东西');exit;	
		}

		$token = $this->_post('token');
		$wecha_id = $this->_post('wecha_id');
		$add_expend = (int)$this->_post('add_expend');
		$add_Balance = (int)$this->_post('add_Balance');
		$total_score = (int)$this->_post('total_score');
		
		$add_expend_time = $this->_post('add_expend_time');
		
		if($add_expend < 0){
			$this->error2('消费金额不能小于0元');exit;	
		}
		if($add_Balance < 0){
			$this->error2('充值金额不能小于0元');exit;	
		}
	
	
		
		//获取商家设置 tp_member_card_exchange
		$exchange = M('Member_card_exchange');
		$getset = $exchange->where(array('token'=>$token))->find();
		//var_dump($getset['continuation']); 
		// 积分 = 消费总金额 * $getset['continuation']
		$userinfo = M('Userinfo')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->find();
      
	  if ($total_score==$userinfo['total_score']){
		if($add_Balance == 0 && $add_expend==0){
			$this->error2('充值和消费金额不能同时为0元');exit;	
		}
	     if ($userinfo['Balance']+$add_Balance <$add_expend)
		{
			$this->error2('消费金额应小于余额');exit;	
		}
		 $data['token']    = $token;
		 $data['wecha_id'] = $wecha_id;
		 $data['sign_time'] = strtotime($add_expend_time);
		 $data['score_type'] = 2;
		 $data['expense']  = ceil($add_expend * $getset['reward']);
		 $data['sell_expense'] = $add_expend; //消费金额
		 $data['add_Balance'] = $add_Balance;
		 
		 //var_dump($data);exit;
		 $back = M('Member_card_sign')->data($data)->add();

		 //总记录 
		 $da['total_score']   = $userinfo['total_score'] +  $data['expense'];
		 $da['Balance']   = $userinfo['Balance'] +  $data['add_Balance']-$data['sell_expense'];
         $da['expend_score']  = $userinfo['expend_score'] + $data['expense'];
         $da['add_expend']    = $add_expend;
         $da['add_expend_time']=strtotime($add_expend_time);
         $back2 = M('Userinfo')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->save($da);
        if($back && $back2){
			$this->success2('操作成功');
		}else{
			$this->error2('服务器繁忙，请稍候再试');
		} 
		
		} 
			 if ($total_score!=$userinfo['total_score']){
				 if($add_Balance == 0 && $add_expend==0){
				 $da['total_score']   = $total_score;
		  $back2 = M('Userinfo')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->save($da);
        if($back2){
			$this->success2('操作成功');
		}else{
			$this->error2('服务器繁忙，请稍候再试');
		} 
		}
		
		else 
		 $this->error2('修改积分时请将充值金额和消费金额同时修改为0元');exit;
		 }
		
		
		
	}
}
?>