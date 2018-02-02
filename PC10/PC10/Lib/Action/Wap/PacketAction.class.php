<?php
class PacketAction extends BaseAction{
	//正常用户进入
	/**
	 *
	 */
	 public function _initialize(){
	     parent::_initialize();
	     $agent = $_SERVER['HTTP_USER_AGENT'];
             if(!strpos($agent,"MicroMessenger")) {
                  echo '此功能只能在微信浏览器中使用';exit;
              }
	 }
	public function index(){
		$openid=$this->_get("openid");//对应用户的token
		$lid=$this->_get("lid","intval");//活动id
		$ainfo=M("lottery")->field("title,click,info,statdate,enddate")->where(array("id"=>$lid))->find();
		$new['click']=$ainfo['click']+1;//点击率
		M("lottery")->where(array("id"=>$lid))->save($new);
		//判断用户是否过期
		$this->check($lid,$openid);
		//判断用户是否关注
		$wxs=M("wxusers")->field("uid")->where(array("openid"=>$openid))->find();//查找该微信号关注的公众号
		$wx=M("wxuser")->field("id,token")->where(array("token"=>$this->_get("token")))->find();//查询进入帐号关注的公众号
		if($wxs['uid']!=$wx['id']){
			echo "亲，您还没有关注，请先关注再来哦!";
			return false;
		}
		//判断用户是否为第一次进入
		$data=M("wxlottery")->where(array("uid"=>$openid,"lid"=>$lid))->find();

		if(empty($data)){
			//第一次进入,数据库插入记录
			$linkid=$this->setFirst($lid,$openid);
			$this->assign("linkid",$linkid);
		}else{
			$linkid=$data['linkid'];
			$this->assign("linkid",$linkid);
			$sql="select id from tp_wxget  where lid={$lid} and uid='{$openid}' and tel !=''";
			if(M("wxget")->query($sql)){
				$this->redirect("Packet/invite",array('token' => $this->_get('token'), "uid"=>$openid,"lid"=>$lid,"linkid"=>$linkid));exit;
			}
		}
		//获取排行帮数据
		$this->assign("statdate",$ainfo['statdate']);
		$this->assign("enddate",$ainfo['enddate']);
		$rank=$this->rank($openid,$lid);
		$row=$rank[0]['row'];
		$integrity=$rank[0]['integritys'];
		//我的节操信息
		$tels=M("wxget")->where(array('lid'=>$lid,'uid'=>$openid))->find();
                if(!empty($tels)){
                    $this->assign("gid",$tels['gid']);//中将id
                }
		$sn=$tels['sn'];
		$tel=$tels['tel'];
		//获取奖品信息
		$this->assign("sn",$sn);
		$this->assign("tel",$tel);
		$rinfo=$this->getinfo($openid,$lid);
		$count=M("lottery")->where(array("id"=>$lid))->find();//统计活动参与人数
		$count=$count['click'];
		$this->assign("rinfo",$rinfo);
		$this->assign("row",$row);//自己名次
		$this->assign("integrity",$integrity);//自己节操数
		$this->assign("rank",$rank);//节操排行榜数据
		$this->assign("title",$ainfo['title']);//活动标题
		$this->assign("count",$count);//活动数量
		$this->assign("rule",$ainfo['info']);//活动规则
		$this->assign("lid",$lid);//活动id
		$this->assign("openid",$openid);//用户id
		$this->display();
	}

	public function getinfo($openid,$lid){
		$rinfo = M('Wxget')->field("tp_wxget.sn,tp_wxget.id,tp_wxget.lid,tp_wxget.status,tp_wxget.tel,tp_wxgift.gname,tp_wxgift.ginfo")->join("join tp_wxgift on tp_wxgift.id = tp_wxget.gid")->where(array('tp_wxget.lid'=>$lid,'tp_wxget.uid'=>$openid))->find();
		return $rinfo;
	}
	//获取排行榜数据
	public function rank($uid,$lid){
//        echo $uid;echo $lid;
		$sql="select tel,integrity,uid from tp_wxlottery where lid={$lid} and tel!='' order by integrity desc";
		$userAll=M("wxlottery")->query($sql);
//		$userAll=M("wxlottery")->field("tel,integrity,uid")->where(array("lid"=>$lid))->order("integrity desc")->select();
		if(!empty($userAll)){
			foreach($userAll as $k=>$v){
				if($v['uid']==$uid){
					$row=$k+1;
					$integrity=$v['integrity'];
					$userAll[0]['row']=$row;
					$userAll[0]['integritys']=$integrity;
				}
			}
		}

		return $userAll;
	}

	//邀请分享
	public function invite(){
		$lid=$this->_get("lid","intval");
		$uid=$this->_get("uid");
		$linkid=$this->_get("linkid");
//		echo $lid;echo $uid;echo $linkid;exit;
		$ainfo=M("lottery")->field("info,statdate,enddate,starpicurl,txt,sttxt")->where(array("id"=>$lid))->find();
		$rank=$this->rank($uid,$lid);
		$row=$rank[0]['row'];
		$integrity=$rank[0]['integritys'];
		$tels=M("wxget")->where(array('lid'=>$lid,'uid'=>$uid))->find();
		$tel=$tels['tel'];
                if(!empty($tels)){
                    $this->assign("gid",$tels['gid']);//中将id
                 }
		//统计活动人数
		$count=M("lottery")->field("click")->where(array("id"=>$lid))->find();
		$count=$count['click'];
		$rinfo=$this->getinfo($uid,$lid);
                $this->assign("title",$ainfo['title']);
		$this->assign("txt",$ainfo['txt']);
		$this->assign("sttxt",$ainfo['sttxt']);
		$this->assign("starpicurl",$ainfo['starpicurl']);
		$this->assign("statdate",$ainfo['statdate']);
		$this->assign("enddate",$ainfo['enddate']);
		$this->assign("tel",$tel);
		$this->assign("rinfo",$rinfo);
		$this->assign("row",$row);//自己名次
		$this->assign("integrity",$integrity);//自己节操数
		$this->assign("rank",$rank);//节操排行榜数据
		$this->assign("count",$count);//数量
		$this->assign("rule",$ainfo['info']);//规则
		$this->assign("lid",$lid);//分配值到节操页面
		$this->assign("linkid",$linkid);
		$this->assign("uid",$uid);//分配值到节操页面
		$this->display();
	}

	//分享进入页面
	public function share(){
		$lid=$this->_get("lid","intval");//活动id
		$uid=$this->_get("uid");//对应用户的openid
		$this->check($lid,$uid);
		$linkid=$this->_get("linkid");//分享进入凭证id
		$data=M("wxlottery")->field("linkid,integrity")->where(array("lid"=>$lid,"uid"=>$uid))->find();
		if($linkid!=$data['linkid']){
			echo "推广地址不正确";
			return false;
		}
		$ainfo=M("lottery")->field("title,info,statdate,enddate,click")->where(array("id"=>$lid))->find();
		$rank=$this->rank($uid,$lid);
		$this->assign("rank",$rank);
		//统计活动人数
		$this->assign("title",$ainfo['title']);
		$this->assign("ainfo",$ainfo);
		$this->assign("lid",$lid);//分配值到节操页面
		$this->assign("uid",$uid);//分配值到节操页面
		$this->assign("linkid",$linkid);
		$this->display();//进入捡节操界面
	}
	//执行获取红包
	public function start(){
		$openid=$this->_get("openid");
		$lid=$this->_get("lid");
		$data=M("wxget")->where(array('uid'=>$openid,"lid"=>$lid))->find();//查询该用户这个活动有没有中奖
		if(empty($data)){
			//没有领取过红包，执行抽奖过程
			$gifts=M("wxgift")->field("id,gname,prob")->where(array("lid"=>$lid))->select();
			$result=$this->lottery($gifts);
			$ginfo=M("wxgift")->field("id,tnum,gname,gnum")->where(array("lid"=>$lid,"gname"=>$result['gift']))->find();
			$actnum=intval($ginfo['tnum'])-intval($ginfo['gnum']);
			if($actnum<=0){
				$this->ajaxReturn(array("status"=>2,"info"=>"红包已领完"));
			}else{
				//如果没有领取完，则往奖品表插入数据
				$save['gnum']=$ginfo['gnum']+1;
				$add['gid']=$ginfo['id'];
				$add['lid']=$lid;
				$add['gtime']=date("Y-m-d H:i:s",time());
				$add['sn']="SN".time().rand(1,10000);
				$add['uid']=$openid;
				if(M("wxgift")->where(array("id"=>$ginfo['id']))->save($save) && M("wxget")->add($add)){
					$this->ajaxReturn(array("status"=>1,"info"=>"恭喜您中了{$ginfo['gname']}","gid"=>$ginfo['id']));
				}
			}
		}else{
			//已经领取过红包
			$this->ajaxReturn(array("status"=>0,"info"=>"亲,您已经领取过红包了哦,赶紧分享给您的朋友吧"));
		}
	}
	//帮好友捡节操
	public function integrity(){
		$lid=$this->_get("lid","intval");
		$uid=$this->_get("uid");
		$data=M("wxlottery")->field("integrity")->where(array("lid"=>$lid,"uid"=>$uid))->find();
		$new['integrity']=$data['integrity']+1;
		if(M("wxlottery")->where(array("uid"=>$uid,"lid"=>$lid))->save($new)){
			$this->ajaxReturn(array("status"=>1));
		}else{
			$this->ajaxReturn(array("status"=>0));
		}
	}

	//进入页面之前判断,判断活动是否到期
	public function check($lid,$uid){
		$lottery=M("lottery")->where(array("id"=>$lid))->find();
                $end=$lottery['enddate']+3600*24-1;
		if($lottery['status']!=1 || $end<time()){
			$count=M("lottery")->field("click")->where(array("id"=>$lid))->find();
			$count=$count['click'];
			$ainfo=M("lottery")->field("info,statdate,enddate,starpicurl,txt,sttxt")->where(array("id"=>$lid))->find();
			$rank=$this->rank($uid,$lid);
			$tels=M("wxget")->where(array('lid'=>$lid,'uid'=>$uid))->find();
			$tel=$tels['tel'];
                        if(!empty($tels)){
                              $this->assign("gid",$tels['gid']);//中将id
                        }
			$this->assign("tel",$tel);
			$row=$rank[0]['row'];
			$integrity=$rank[0]['integritys'];
			$rinfo=$this->getinfo($uid,$lid);
			$this->assign("ainfo",$ainfo);
			$this->assign("title",$ainfo['title']);
			$this->assign("rinfo",$rinfo);
			$this->assign("row",$row);//自己名次
			$this->assign("integrity",$integrity);//自己节操数
			$this->assign("rank",$rank);//节操排行榜数据
			$this->assign("count",$count);
			$this->assign("rule",$ainfo['info']);
			$this->assign("uid",$uid);
			$this->assign("lid",$lid);
			$this->display("end");
			exit;
		}
		return true;
	}


	//第一次进入插入记录数据
	public function setFirst($lid,$openid) {
		$data['lid']=$lid;
		$data['uid'] = $openid;
		$data['logintime'] = date('Y-m-d H:i:s', time()); //第一次登录时间
		$data['linkid']="WP".$openid.time();
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

	//红包结果
	public function result(){
		$finish=$this->_get("finish");
		if($finish=="红包已领完"){
			$finish="finish";
			$this->assign("finish",$finish);
		}
		$openid=$this->_get("openid");
		$lid=$this->_get("lid","intval");
		$ainfo=M("lottery")->field("title")->where(array("id"=>$lid))->find();
		$sns=M("wxget")->field("sn")->where(array("lid"=>$lid,"uid"=>$openid))->find();
		$sn=$sns['sn'];
		$this->assign("sn",$sn);
		$gid=$this->_get("gid");
		$data=M("wxgift")->field("gname,ginfo")->where(array("id"=>$gid))->find();
		$user=M("wxlottery")->field("linkid")->where(array("lid"=>$lid,"uid"=>$openid))->find();
		$linkid=$user['linkid'];
		$this->assign("title",$ainfo['title']);
		$this->assign("linkid",$linkid);
		$this->assign("data",$data);
		$this->assign("openid",$openid);
		$this->assign("lid",$lid);
		$this->display();
	}

	//ajax领取
	public function draw(){
		$uid=$this->_get("uid");
		$lid=$this->_get("lid");
		$tel=$this->_get("tel");
		if(M("wxlottery")->where(array("uid"=>$uid,"lid"=>$lid))->save(array("tel"=>$tel)) && M("wxget")->where(array("uid"=>$uid,"lid"=>$lid))->save(array("tel"=>$tel))){
			$this->ajaxReturn(array("status"=>1,"info"=>"领取成功","url"=>'/index.php?g=Wap&m=Packet&a=invite&uid='.$uid.'&linkid='.$_POST['linkid']."&lid=".$lid));
		}else{
			if(M("wxget")->where(array("uid"=>$uid,"lid"=>$lid))->save(array("tel"=>$tel))){
				$this->ajaxReturn(array("status"=>0,"info"=>"领取失败"));
			}else{
				$this->ajaxReturn(array("status"=>1,"info"=>"操作成功","url"=>'/index.php?g=Wap&m=Packet&a=invite&uid='.$uid.'&linkid='.$_POST['linkid']."&lid=".$lid));
			}
		}
	}

	//兑奖
	public function exchange(){
		$data=M("lottery")->field("password")->where(array("id"=>$this->_get("lid")))->find();
		if(md5($_POST['password'])===$data['password']){
			if(M("wxget")->where(array("id"=>$this->_get('gid')))->save(array("status"=>1))){
				$this->ajaxReturn(array("status"=>1,"info"=>"兑奖成功"));
			}else{
				$this->ajaxReturn(array("status"=>0,"info"=>"兑换失败"));
			}
		}else{
			$this->ajaxReturn(array("status"=>0,"info"=>"密码错误"));
		}
	}
}
?>