<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class Mruqd2Action extends BaseAction{
	public $_sTplBaseDir = 'Wap/default/mru';

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		$token=$this->token;
	    
	    
	    $count      = M('mru_tiyan')->where(array('token'=>$token))->count();
	    $Page       = new Page($count,10);
	    $show       = $Page->show();
	    $list = M('mru_tiyan')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign('page',$show);
	    $this->assign('list',$list);
	    
	  //  p($list);
		$this->display();
	}
	
	
	public function show(){
        $list=M('mru_tiyan')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
		$this->display();
	}
	
	
	public function usersign(){
		$signdata = M('Usercenter_sign_set')->where(array('token'=>$this->token,'status'=>1))->find();
		if(!$signdata){
			$signdata = null;
		}
		$usercenter_signmodel = M('Usercenter_sign');
		$usercenter_scored_recordModel = M('Usercenter_score_record');
		$userissign = $usercenter_signmodel->where(array('token'=>$this->token,'openid'=>$this->openid,'sign_date'=>date("Y-m-d",time())))->find();
		$allsigncounts = $usercenter_signmodel->where(array('token'=>$this->token,'openid'=>$this->openid))->count();
		if(IS_POST){
			if(M('mru_jfb')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token']))->find()){
				if(!$userissign){
					$newdata = array();
					$newdata['sign_date'] = date("Y-m-d",time());
					$newdata['openid'] = $this->openid;
					$newdata['token'] = $this->token;
					$newdata['score'] = $signdata['day_score'];
					if($usercenter_signmodel->add($newdata)){
						$data = array();
						$data['token'] = $this->token;
						$data['openid'] = $this->openid;
						$data['type'] = 1;
						$data['score'] = $signdata['day_score'];
						$data['add_time'] = time();
						$usercenter_scored_recordModel->add($data);
						$uid=M('wxuser')->where(array('token'=>$_GET['token']))->getField('id');
						$memdata1 = M("Usercenter_memberlist")->where(array('uid'=>$uid,'openid'=>$this->openid,'uid'=>$this->uid))->find();
	
						$memscore1 = $memdata1['score']+$signdata['day_score'];
	
						M("Usercenter_memberlist")->where(array('uid'=>$uid,'openid'=>$this->openid,'uid'=>$this->uid))->data(array('score'=>$memscore1))->save();
						$lastdate = date("Y-m-d",time()-$signdata['days']*3600*24);
						$curdata = date("Y-m-d",time());
						$lwhere = array();
						$lwhere['sign_date']=array('between',array($lastdate,$curdata));
						$counts = $usercenter_signmodel->where($lwhere)->count();
						if($counts == $signdata['days']){
							$data = array();
							$data['token'] = $this->token;
							$data['openid'] = $this->openid;
							$data['type'] = 2;
							$data['score'] = $signdata['scores'];
							$data['add_time'] = time();
							$usercenter_scored_recordModel->add($data);
							$memdata = M("Usercenter_memberlist")->where(array('uid'=>$uid,'openid'=>$this->openid,'uid'=>$this->uid))->find();
								
							$memscore = $memdata['score']+$signdata['scores'];
	
							M("Usercenter_memberlist")->where(array('uid'=>$uid,'openid'=>$this->openid,'uid'=>$this->uid))->data(array('score'=>$memscore))->save();
							//张湘南
							M('mru_jfb')->where(array('openid'=>$data['openid'],'token'=>$data['token']))->setInc('num',$signdata['day_score']);
							M('mru_jfb')->where(array('openid'=>$data['openid'],'token'=>$data['token']))->setInc('num',$signdata['scores']);
							echo $this->encode(array('code'=>0,'msg'=>'哈哈,成功签到获得'.$signdata['day_score'].'分签到积分,您连续签到'.$signdata['days'].'获得'.$signdata['scores'].'分奖励！'));
						}else{
							//张湘南
							$b=M('mru_jfb')->where(array('openid'=>$data['openid'],'token'=>$data['token']))->setInc('num',$signdata['day_score']);
							if(!$b){
								M('mru_jfb')->add(array('openid'=>$data['openid'],'token'=>$data['token'],'num'=>$signdata['day_score']));
							}
							echo $this->encode(array('code'=>0,'msg'=>'哈哈,成功签到获得'.$signdata['day_score'].'分签到积分'));
						}
					}else{
						echo $this->encode(array('code'=>-2,'msg'=>'签到失败了哦请重试'));
					}
				}else{
					echo $this->encode(array('code'=>-2,'msg'=>'您今天已经签过到了哦'));
				}
			}else{
				echo $this->encode(array('code'=>-1,'msg'=>'您还没有镇写会员资料哦,赶快先镇写资料吧','url'=>C('site_url').'index.php?g=Wap&m=Mrugr&a=index&token='.$this->token."&openid=".$this->openid));
			}
		}else{
			$this->assign('signdata',$signdata);
			
			$this->assign('userissign',$userissign);
			$this->assign('allsigncounts',$allsigncounts);
			MruMember('Mhyzx/zc',$_GET['openid']);
			$this->display();
		}
	
	}

	
}
?>
