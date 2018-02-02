<?php
class LotteryAction extends UserAction{
	public function index(){
		$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();


	//dump(session('token'));
        /*
		if(session('gid')==1){
			$this->error2('测试用户还不能使用哦',U('Home/Index/price'));
		}
        */
		$user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		$this->assign('group',$group);
		$this->assign('activitynum',$user['activitynum']);
		$list=M('Lottery')->where(array('token'=>session('token'),'type'=>1))->select();
		//echo M('Lottery')->getLastSql();
		$this->assign('count',M('Lottery')->where(array('token'=>session('token'),'type'=>1))->count());
		$this->assign('list',$list);
		$this->display();
	
	}
	public function sn(){
		$id=$this->_get('id');
        if( $_POST['sn']){
            $sn = $_POST['sn'];
            $data=M('Lottery')->where(array('token'=>session('token'),'id'=>$id))->find();
            $record=M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn="'.$sn.'"')->select();
            $recordcount=M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn="'.$sn.'"')->count();
            $datacount=$data['fistnums']+$data['secondnums']+$data['thirdnums']+$data['fournums']+$data['fivenums']+$data['sixnums'];
            $sendCount=M('Lottery_record')->where('lid='.$id.' and sendstutas=1 and sn="'.$sn.'"')->count();
            $this->assign('sendCount',$sendCount);
            $this->assign('datacount',$datacount);
            $this->assign('recordcount',$recordcount);
            $this->assign('record',$record);
            $this->assign('sn',$sn);
        }else{
            $data=M('Lottery')->where(array('token'=>session('token'),'id'=>$id))->find();
            $record=M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn!=""')->select();
            $recordcount=M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn!=""')->count();
            $datacount=$data['fistnums']+$data['secondnums']+$data['thirdnums']+$data['fournums']+$data['fivenums']+$data['sixnums'];
            $sendCount=M('Lottery_record')->where('lid='.$id.' and sendstutas=1 and sn!=""')->count();
            $this->assign('sendCount',$sendCount);
            $this->assign('datacount',$datacount);
            $this->assign('recordcount',$recordcount);
            $this->assign('record',$record);
        }

	
		$this->display();
	
	
	}
	public function add(){
		
		if(IS_POST){
			//add the use times . 
			M('Users')->where(array('id'=>session('uid')))->setInc('activitynum');
			$data=D('lottery');
			$_POST['statdate']=strtotime($this->_post('statdate'));
			$_POST['enddate']=strtotime($this->_post('enddate'));
			$_POST['token']=session('token');
			$this->all_insert2();
		}else{
			$this->display();
		}
	}
	public function setinc(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Lottery')->where($where)->find();
		if($check==false)$this->error2('非法操作');
		$user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		/*
		if($user['activitynum']>=$group['activitynum']){
			$this->error2('您的免费活动创建数已经全部使用完,请充值后再使用');
		}
		*/
		$data=M('Lottery')->where($where)->setInc('status');
		if($data!=false){
			$this->success2('恭喜你,活动已经开始');
		}else{
			$this->error2('服务器繁忙,请稍候再试');
		}

	}
	public function setdes(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Lottery')->where($where)->find();
		if($check==false)$this->error2('非法操作');
		$da['status']='0';
		$data=M('Lottery')->where($where)->save($da);
		if($data!=false){
			$this->success2('活动已经结束','index.php?g=User&m=Lottery&a=index&token='.$_SESSION['token']);
		}else{
			$this->error2('服务器繁忙,请稍候再试','index.php?g=User&m=Lottery&a=index&token='.$_SESSION['token']);
		}
	}
	public function edit(){
		if(IS_POST){
			$data=D('Lottery');
			$_POST['id']=$this->_get('id');
			$_POST['token']=session('token');
			$_POST['statdate']=strtotime($_POST['statdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			if(empty($_POST['fist']) || empty($_POST['fistnums'])){
				$this->error2('必须设置一等奖奖品和数量');
				exit;
			}
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token']);
			$check=$data->where($where)->find();
			if($check==false)$this->error2('非法操作');
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$this->success2('修改成功',U('Lottery/index',array('token'=>session('token'))));
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($data->error2());
			}
		}else{
			$id=$this->_get('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('Lottery');
			$check=$data->where($where)->find();
			if($check==false)$this->error2('非法操作');
			$lottery=$data->where($where)->find();
			$this->assign('lottery',$lottery);

			//dump($_POST);
			$this->display('add');
		}
	
	}
	public function del(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data=M('Lottery');
		$check=$data->where($where)->find();
		if($check==false)$this->error2('非法操作');
		$back=$data->where($wehre)->delete();
		if($back==true){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token')))->delete();
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
	
	
	}
	
	public function sendprize(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data['sendtime'] = time();
		$data['sendstutas'] = 1;
		$back = M('Lottery_record')->where($where)->save($data);
		if($back==true){
			$this->success2('成功发奖');
		}else{
			$this->error2('操作失败');
		}
	}
	
	public function sendnull(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data['sendtime'] = '';
		$data['sendstutas'] = 0;
		$back = M('Lottery_record')->where($where)->save($data);
		if($back==true){
			$this->success2('已经取消');
		}else{
			$this->error2('操作失败');
		}
	}
}


?>