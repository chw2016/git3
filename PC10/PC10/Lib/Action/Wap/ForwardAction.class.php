<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/12/4
 * Time: 11:48
 */
class ForwardAction extends BaseAction{
	public $token;
	public $openid;
	public $lid;
	public function _initialize(){
		parent::_initialize();
		//是否通过微信访问
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if(!strpos($agent,"MicroMessenger")) {
			exit;
		}
		$this->token=$this->_get("token");
		$this->openid=$this->_get("openid");
		$this->lid=$this->_get("lid");
		//微信号，公众号，活动是否正确
		if(!M("wxuser")->where(array("token"=>$this->token))->find() || !M("wxusers")->where(array('openid'=>$this->openid))->find() || !M("lottery")->where(array('id'=>$this->lid))->find()){
			die("访问地址不正确");
		}
		//活动是否过期
		$linfo=M("lottery")->field("status,statdate,enddate,info,txt,sttxt,starpicurl")->where(array("id"=>$this->lid))->find();
		if($linfo['status']==0 || $linfo['status']==2 || time()<=$linfo['statdate'] || time()>=$linfo['enddate']){
			die("活动还没有开始或者已经结束");
		}else{
			$this->assign("linfo",$linfo);
		}
        $this->assign("tpl",$this->tpl);
		$this->assign("lid",$this->lid);
		$this->assign("token",$this->token);
		$this->assign("openid",$this->openid);
	}

	public function index(){
        //引入微信分享js接口

        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);

		$g1=M("wxgift")->field("id,pic,gname")->where(array("lid"=>$this->lid))->limit(0,4)->select();
		$g2=M("wxgift")->field("id,pic,gname")->where(array("lid"=>$this->lid))->limit(9,1)->select();
		$g3=M("wxgift")->field("id,pic,gname")->where(array("lid"=>$this->lid))->limit(4,1)->select();
		$g4=M("wxgift")->field("id,pic,gname")->where(array("lid"=>$this->lid))->limit(5,4)->select();
		$g4=array_reverse($g4);
		$data=M("wxget")->join("join tp_wxinfo on tp_wxget.uid=tp_wxinfo.uid")->where(array('tp_wxget.lid'=>$this->lid))->select();
		$this->assign("data",$data);
		//分配linkid，分第一次进入，和非第一次进入
		$uinfo=M("wxlottery")->field("linkid,linktaches,losttaches")->where(array("uid"=>$this->openid,"lid"=>$this->lid))->find();
		if(empty($uinfo)){
			//第一次进入
			$linkid=$this->setFirst($this->lid,$this->openid);
			$this->assign("total",0);
			$this->assign("linkid",$linkid);
		}else{
			//非第一次进入
			$this->trans($this->lid,$this->openid);
            $new=M("wxlottery")->field("linkid,linktaches,losttaches,lottertimes")->where(array("uid"=>$this->openid,"lid"=>$this->lid))->find();
            $this->assign("total",$new['linktaches']-$new['losttaches']);
            $this->assign("times",$new['lottertimes']);
			$this->assign("linkid",$new['linkid']);
		}
		$ainfo=M("lottery")->where(array("id"=>$this->lid,'token'=>$this->token))->find();
		$this->assign("ainfo",$ainfo);
		$this->assign("g2",$g2);
		$this->assign("g1",$g1);
		$this->assign("g3",$g3);
		$this->assign("g4",$g4);
		$this->display();
	}

	//分享
	public function share(){
	        //微信号，公众号，活动是否正确
	        if($this->token == 'ec60f633808334ace70cabce58e50b6f') {
	            //cfo特殊处理
                $users = M("Wxusers")->where(array('openid' => $this->openid,'status'=>1))->find();
	            if (!$users) {
			      $this->redirect('https://mp.weixin.qq.com/cgi-bin/appmsg?t=media/appmsg_edit&action=edit&lang=zh_CN&token=150398775&type=10&appmsgid=204336925&isMul=0');
	            }
	        }
		$uinfo=M("wxlottery")->field("linkid,linktaches,losttaches")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->find();
		$linkid = $this->_get("linkid");
		$datalinkid = $uinfo['linkid'];

		if(!preg_match("/".$datalinkid."/",$linkid)){
			die("分享失败");
		}
		$g1=M("wxgift")->field("id,pic,gname")->where(array("lid"=>$this->lid))->limit(0,4)->select();
		$g2=M("wxgift")->field("id,pic,gname")->where(array("lid"=>$this->lid))->limit(9,1)->select();
		$g3=M("wxgift")->field("id,pic,gname")->where(array("lid"=>$this->lid))->limit(4,1)->select();
		$g4=M("wxgift")->field("id,pic,gname")->where(array("lid"=>$this->lid))->limit(5,4)->select();
		$g4=array_reverse($g4);
		$data=M("wxget")->join("join tp_wxinfo on tp_wxget.uid=tp_wxinfo.uid")->where(array('tp_wxget.lid'=>$this->lid))->select();
		$this->assign("data",$data);
//		$this->assign("ainfo",$ainfo);
//		$ainfo=M("lottery")->where(array("id"=>$this->lid))->find();
        $this->trans($this->lid,$this->openid);
        $new=M("wxlottery")->field("linkid,linktaches,losttaches")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->find();
        $ainfo=M("lottery")->where(array("id"=>$this->lid,'token'=>$this->token))->find();
		$this->assign("total",$new['linktaches']-$new['losttaches']);
		$this->assign("ainfo",$ainfo);
		$this->assign("g2",$g2);
		$this->assign("g1",$g1);
		$this->assign("g3",$g3);
		$this->assign("g4",$g4);
		$this->display();
	}
	public function trans($lid,$openid){
		$data=M("wxlottery")->field("losttaches,linktaches,lottertimes")->where(array("uid"=>$openid,"lid"=>$lid))->find();
		$dtaches=intval($data['linktaches'])-intval($data['losttaches']);
		$uinfo=array();
		if($dtaches>=5){
			$addtimes=floor($dtaches/5);
			$addlost=$addtimes*5;
			$uinfo['lottertimes']=$data['lottertimes']+$addtimes;
			$uinfo['losttaches']=$data['losttaches']+$addlost;
			M("wxlottery")->where(array("uid"=>$openid,'lid'=>$lid))->save($uinfo);
		}
	}
	

	//增加环数
	public function addTaches(){
		$data=M("wxlottery")->field("linktaches")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->find();
		$new['linktaches']=$data['linktaches']+1;
		if(M("wxlottery")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->save($new)){
			$this->ajaxReturn(array("status"=>1,"info"=>"帮助成功"));
		}else{
			$this->ajaxReturn(array("status"=>1,"info"=>"帮助失败"));
		}
	}
	//抽奖方法
	public function start(){
		//判断用户是否还有抽奖机会
		$uinfo=M("wxlottery")->field("lottertimes")->where(array('lid'=>$this->lid,'uid'=>$this->openid))->find();
		if(intval($uinfo['lottertimes'])<=0){
			$this->ajaxReturn(array("status"=>0,"info"=>"您的转发数不够哦!"));
		}
//		//判断用户有没有中过奖
//		if(M("wxget")->where(array("openid"=>$this->openid,"lid"=>$this->lid))->find()){
//			$this->ajaxReturn(array("status"=>0,"info"=>"别灰心下次再来哦"));
//		}
		$data=M("wxgift")->field("id,gname,prob")->where(array("lid"=>$this->lid))->limit(10)->select();
		$result=$this->lottery($data);
		$ginfo=M("wxgift")->field("id,tnum,gname,gnum")->where(array("lid"=>$this->lid,"gname"=>$result['gift']))->find();
		$actnum=intval($ginfo['tnum'])-intval($ginfo['gnum']);
		if($ginfo['level']==="不中奖"){
			$this->ajaxReturn(array("status"=>0,"info"=>"别灰心下次再来哦"));
		}
		if($actnum<=0){
			$this->ajaxReturn(array("status"=>0,"info"=>"别灰心下次再来哦"));
		}else{
			//减少抽奖次数
			$save['lottertimes']=intval($uinfo['lottertimes'])-1;
			M("wxlottery")->where(array('lid'=>$this->lid,'uid'=>$this->openid))->save($save);
			//如果没有领取完，则往奖品表插入数据
			$ids=array_map("array_shift",$data);
			$row=array_keys($ids,$ginfo['id'],true);//显示商品在序列表中的位置
			$save['gnum']=$ginfo['gnum']+1;
			$add['gid']=$ginfo['id'];
			$add['lid']=$this->lid;
			$add['gtime']=date("Y-m-d H:i:s",time());
			$add['sn']="SN".time().rand(1,10000);
			$add['uid']=$this->openid;
			$pinfo['id']=$ginfo['id'];
			$pinfo['row']=$row[0]+1;
			if(M("wxgift")->where(array("id"=>$ginfo['id']))->save($save) && M("wxget")->add($add)){
				$this->ajaxReturn(array("status"=>1,"info"=>"恭喜您中了{$ginfo['gname']}","data"=>$pinfo));
			}
		}
	}

	//第一次插入数据并返回
	public function setFirst($lid,$openid) {
		$data['lid']=$lid;
		$data['uid'] = $openid;
		$data['logintime'] = date('Y-m-d H:i:s', time()); //第一次登录时间
		$data['linkid']="WP".$openid.time();
		$data['lottertimes']=0;
		if(M("wxlottery")->add($data)){
			return $data['linkid'];
		}
	}
	//随机抽奖
	public function lottery($gift = array()) {
		foreach ($gift as $k => $v) {
			$data[$v['id']] = $v['prob'];
		}
		$rid = $this->get_rand($data);
		foreach ($gift as $k => $v) {
			if ($rid == $v['id']) {
				$res['gift'] = $v['gname'];
			} else {
				$res['no'][] = $v['gname'];
			}
		}
		return $res;
	}

	//获取中奖概率
	public function get_rand($data = array()) {
		$result = "";
		$sum = array_sum($data); //获取概率总和
		foreach ($data as $k => $v) {
			$num = rand(1, $sum);
			if ($num <= $v) {
				$result = $k;
				break;
			} else {
				$sum-=$v;
			}
		}
		unset($data);
		return $result;
	}

	//添加中奖资料页面
	public function ginfo(){
		$ginfo=M("wxget")->field("tp_wxgift.level,tp_wxgift.gname")->join("join tp_wxgift on tp_wxgift.id=tp_wxget.gid")->where(array('tp_wxget.lid'=>$this->lid,'uid'=>$this->openid))->select();
		$uinfo=M("wxinfo")->field("id,uname,tel,address,seng,si,xian")->where(array('uid'=>$this->openid,'lid'=>$this->lid))->find();
        $this->assign("cinfo",$this->tpl);
		$this->assign("uinfo",$uinfo);
		$this->assign("ginfo",$ginfo);
		$this->display();
	}
        //添加填写资料信息
	public function addinfo(){
		if(IS_POST){
            $_POST['seng']=$_POST['s_province'];
            $_POST['si']=$_POST['s_city'];
            $_POST['xian']=$_POST['s_county'];
			$_POST['uid']=$this->openid;
			$_POST['lid']=$this->lid;
			if(M("wxinfo")->add($_POST)){
				$this->success2("资料填写成功",U('Forward/index',array('openid'=>$this->openid,'lid'=>$this->lid,'token'=>$this->token)));
			}else{
				$this->error2("资料填写失败");
			}
		}
	}

	public function editinfo(){
		if(IS_POST){
            $_POST['seng']=$_POST['s_province'];
            $_POST['si']=$_POST['s_city'];
            $_POST['xian']=$_POST['s_county'];
			if(M("wxinfo")->where(array('id'=>$_POST['sid']))->save($_POST)){
				$this->success2("资料修改成功",U('Forward/index',array('openid'=>$this->openid,'lid'=>$this->lid,'token'=>$this->token)));
			}else{
				$this->error2("资料修改失败");
			}
		}
	}
}