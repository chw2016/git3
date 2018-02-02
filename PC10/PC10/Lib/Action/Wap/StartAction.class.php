<?php

/*
 * authou:肖国平.
 * Tel:15889394741
 */

class StartAction extends BaseAction {

    public function index() {
        $aid=$this->_get("aid","intval");
	    $ainfo=M("lottery")->field("title")->where(array("id"=>$aid))->find();
        $data=M("lottery")->where(array("id"=>$aid,"status"=>1))->select();
	    if($data[0]['status']==0){
		    echo "活动还没有开始哦，敬请期待";
		    return false;
	    }
	    if($data[0]['status']==2){
		    echo "活动已经暂停，请关注";
		    return false;
	    }
	    if($data[0]['status']==3){
		    echo "活动已经结束";
		    return false;
	    }
        //判断用户是通过推广进入还是正常进入
        $link = $this->_get("linkid");//获取推广链接id
        //查找该微信用户的上次抽奖操作记录
        if ($link){
            //判断推广连接是否匹配
            $uid=$this->_get("uid");//绑定的推广openid帐号
            $data=M("wxlottery")->field("linkid,linktaches")->where(array("uid"=>$uid,"lid"=>$aid))->select();
            if($data[0]['linkid']!=$link){
                echo "推广地址不正确";
                return false;
            }
	        $this->assign("title",$ainfo['title']);
	        $this->assign("linkid",$data[0]['linkid']);
	        $this->assign("aid",$aid);
	        $this->assign("uid",$uid);
	        $this->display("share");
        } else {
            //关注进入
            $openid=$this->_get("openid");
	        $wx=M("wxusers")->field("openid")->where(array("openid"=>$openid))->find();//微信用户信息
			if(empty($wx)){
				echo "亲，您还没有关注，请先关注";
				return false;
			}else{
				$uid=$openid;
			}
            $data = M("wxlottery")->where(array("uid" => $uid,"lid"=>$aid))->select();
            //判断用户是否为第一次进入
            if (empty($data)) {
                //第一次进入，插入一条记录
                $this->setFirst($uid,$aid);
                $ftache = 6;
            } else {
                if($data[0]['lottertimes']>0){
                    $ftache=6;
                }else{
                    $trueTache = $this->getTache($data,$uid,$aid);
	                $this->transTache($trueTache, $data, $uid,$aid);//转换方法
	                $new=M("wxlottery")->where(array("uid" => $uid,"lid"=>$aid))->find();
	                if($new['lottertimes']>0){
		                $ftache=6;
	                }else{
		                $ftache = ($trueTache % 6)-1;
	                }
                }
                //将增加的环数转换为有效增加抽奖次数

            }
            $new = M("wxlottery")->where(array("uid" => $uid))->select();
            $final = $new[0]['lottertimes'] - $new[0]['losttimes'];
            $link_id=$new[0]['linkid'];
	        $this->assign("title",$ainfo['title']);//标题
            $this->assign("linkid",$link_id);//分配对应链接id
            $this->assign("tacha", $ftache); //分配剩余环数
            $this->assign("uid", $uid); //分配抽奖id
            $this->assign("lid",$aid);//活动id
            $this->display();
        }
    }
	//分享活动时增加环数
	public function addTache(){
		$uid=$this->_get("uid");
        $lid=$this->_get("aid");
		$data=M("wxlottery")->where(array("uid"=>$uid,"lid"=>$lid))->find();
		$new['linktaches']=$data['linktaches']+1;
		if(M("wxlottery")->where(array("uid"=>$uid,"lid"=>$lid))->save($new)){
			$this->ajaxBack(1,"已帮好友点亮");
		}else{
			$this->ajaxBack(0,"点亮失败");
		}
	}
    //判断活动是否开始或者结束
    public function getAct($aid){
        $data=M("lottery")->where(array("id"=>$aid,"status"=>1))->select();
        if($data[0]['statdate']<time() && $data[0]['enddate']>time()){
            return true;
        }else{
            return false;
        }
    }
    //将环数转换为抽奖次数
    public function transTache($trueTache, $data, $uid,$aid) {
        $lotter = (floor($trueTache / 6)) * 3; //环数转化为抽奖次数
        $losttache = $data[0]['losttaches'] + $lotter * 2; //实际减少环数
        $lotter = $lotter + $data[0]['lottertimes'];
        $save['losttaches'] = $losttache;
        $save['lottertimes'] = $lotter;
        //更新数据
        M("wxlottery")->where(array('uid' => $uid,"lid"=>$aid))->save($save);
        return $lotter; //返回总抽奖次数
    }

    //获得当前有效总环数
    public function getTache($data,$uid,$aid) {
        date_default_timezone_set("PRC");
        $info=M("wxlottery")->field("logintime")->where(array("uid"=>$uid,"lid"=>$aid))->select();
        $atime=strtotime(date("Y-m-d",strtotime($info[0]['logintime'])));
        $now = strtotime(date("Y-m-d", time())); //当前时间
        $tache = ($now - $atime) / (3600 * 24) + $data[0]['linktaches']+1 ;
        $useTache = $data[0]['losttaches'];
        return $trueTache = $tache - $useTache;
    }

    //第一次登录用户追加记录
    public function setFirst($uid,$aid) {
        $Model = M("wxlottery");
        $first = array();
	    $first['lid']=$aid;
        $first['uid'] = $uid;
        $first['logintime'] = date('Y-m-d H:i:s', time()); //第一次登录时间
        $first['lottertimes'] = "1"; //第一次抽奖次数加1
        $first['linkid']="WP".$uid.time();
        $Model->add($first);
    }

    //执行抽奖
    public function start() {
        $uid=$this->_get("uid");//微信用户id
        $lid=$this->_get("lid","intval");//获取对应活动id
        $Model = M("wxlottery");
        $data = $Model->where(array("uid" => $uid,"lid"=>$lid))->select();
        if ($data[0]['lottertimes'] <= 0) {
            $this->ajaxBack(0, "抱歉，您的抽奖机会已用尽。将活动链接分享至朋友圈或发送给好友，6位好友点击使一体化六环被点亮后即可获得再多三次抽奖机会。");
        } else {
            $arr['lottertimes'] = $data[0]['lottertimes'] - 1; //减少一次
            //执行抽奖
            M("wxlottery")->where(array("uid" => $uid))->save($arr);
            $con = M("wxgift")->field("id,gname,prob,level")->where(array("lid" =>$lid))->select(); //活动id暂时不考虑
            //获取所中奖品信息
            $data = $this->lottery($con);
            $gift = $data['gift']; //所中奖品
            $giftinfo = M("wxgift")->field("level")->where(array("gname" => $gift))->select();
            $level = $giftinfo[0]['level']; //中奖级别
            $this->action($level, $gift, $uid,$lid);
        }
    }

    //中奖处理
    public function action($level, $gift, $uid,$lid) {
        //没有中奖
        if ($level == "不中奖") {
            $this->ajaxBack("0", "别灰心，下次还有机会哦!");
        } else {
            //判断所中奖品是否发完
            date_default_timezone_set("PRC");
            $gifts = M("wxgift")->where(array("gname" => $gift))->select(); //查询所中奖品对应信息
            $info=M("lottery")->field("statdate")->where(array("id"=>$lid))->select();//活动开始时间
            $login = strtotime(date("Y-m-d",$info[0]['statdate']));//活动开始日期
            $now = strtotime(date("Y-m-d", time()));
            $day = ($now - $login) / (3600 * 24) + 1; //距离活动开始的天数
	        $ip=$_SERVER['REMOTE_ADDR'];//获取当前用户ip地址
            if ($gifts[0]['gnum'] >= $day * $gifts[0]['dnum']) {
                $this->ajaxBack(0, "别灰心，下次还有机会哦!");
            } else if ($gifts[0]['tnum'] <= $gifts[0]['gnum']) {
                $this->ajaxBack(0, "别灰心，下次还有机会哦!");
            } else {
                //已经中奖，插入数据
                if(M("wxget")->where(array("uid"=>$uid,"lid"=>$lid))->select()){
                    $this->ajaxBack(0,"别灰心，下次还有机会哦!");
                }else if(M("wxget")->where(array("ip"=>$ip))->find()){
	                $this->ajaxBack(0,"别灰心，下次还有机会哦!");
                }else{
                    $gid = $gifts[0]['id'];
                    $gnum = $gifts[0]['gnum'];
                    $gnum = $gnum + 1;
                    $sn="SN".time().rand(1,10000);
                    $lid=$lid;
                    M("wxgift")->where(array("id" => $gid))->save(array("gnum" => $gnum)); //修改中奖品产品数量
                    M("wxget")->add(array("gid" => $gid, "lid"=>$lid,"uid" => $uid,"ip"=>$ip,"sn"=>$sn,"gtime"=>date("Y-m-d H:i:s",time())));
                    $mes = "恭喜你中了{$level},所中奖品为{$gift}";
                    $this->ajaxBack(1, $mes,array("gid"=>$gid));
                }
            }
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

    //ajax返回json数据
    public function ajaxBack($status, $mes, $data = array()) {
        echo json_encode(
                array(
                    "status" => $status,
                    "mes" => $mes,
                    "data" => $data
                )
        );
    }

    //中奖之后填写中奖资料
    public function result(){
        if(IS_POST){
            $model=M("wxinfo");
            if($model->add($_POST)){
	            $this->error2("添加成功");

            }else{
	            $this->errot("添加失败");
            }
        }else{
            $uid=$this->_get("uid");//用户openid
            $gid=$this->_get("gid","intval");//查找用户所中奖品的奖品id
            $data=M("wxgift")->field("level,gname,lid")->where(array("id"=>$gid))->find();
	        $info=M("wxinfo")->field("id")->where(array("uid"=>$uid,"gid"=>$gid))->find();
	        $this->assign("info",$info);
            $this->assign("data",$data);
            $this->assign("uid",$uid);
            $this->display();
        }
    }
	//显示所有中奖列表
   public function results(){
	   if(IS_POST){
		   if(isset($_POST['id'])){
				//修改
			   if(M("wxinfo")->where(array("id"=>$_POST['id']))->save($_POST)){
				   $this->success2("操作成功");
			   }else{
				   $this->success2("操作失败");
			   }
		   }else{
				//追加
			   if(M("wxinfo")->add($_POST)){
				   $this->success2("操作成功");
			   }else{
				   $this->success2("操作失败");
			   }
		   }
	   }else{
		   $uid=$this->_get("uid");//微信用户ipenid
		   $lid=$this->_get("lid","intval");
		   $sql="select level,gname,id from tp_wxgift where id=(select gid from tp_wxget where uid='{$uid}' and lid={$lid})";
		   $data=M("wxgift")->query($sql);
		   $info=M("wxinfo")->field("id,uname,tel,address")->where(array("lid"=>$lid,"uid"=>$uid))->find();
		   if(!empty($info)){
			   $id=$info['id'];
			   $this->assign("id",$id);
		   }
		   $this->assign("info",$info);
		   $this->assign("data",$data);
		   $this->assign("uid","$uid");
		   $this->assign("lid",$lid);
		   $this->display();
	   }
   }
}
