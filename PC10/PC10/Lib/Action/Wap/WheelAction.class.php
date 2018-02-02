<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/12/4
 * Time: 11:48
 */
class WheelAction extends BaseAction{
    public $token;
    public $openid;
    public $lid;
    public $lottery;
    public function _initialize(){
        parent::_initialize();
        import("ORG.Lotterys");
        $this->token=$this->_get("token");
        $this->openid=$this->_get("openid");
        $this->lid=$this->_get("lid");
        $this->lottery=new Lotterys();
        if(!$this->lottery->check($this->lid,$this->openid,$this->token)){
            echo "活动结束";
            exit;
        }
        $this->assign("tpl",$this->tpl);
        $this->assign("lid",$this->lid);
        $this->assign("token",$this->token);
        $this->assign("openid",$this->openid);
    }

    //访问抽奖页面
    public function index(){
        $ginfo=M("wxgift")->field("id,gname,pic")->where(array("lid"=>$this->lid,"token"=>$this->token))->order("id asc")->limit(8)->select();
        $linfo=M("lottery")->field("id,info,statdate,enddate,canrqnums")->where(array("id"=>$this->lid,"token"=>$this->token))->find();
        if(!$data=M("wxlottery")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->find()){
            $this->lottery->setFirst($this->openid,$this->lid);
        }
        $uinfo=M("wxlottery")->field("lottertimes")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->find();
        $this->assign("uinfo",$uinfo);
        $this->assign("linfo",$linfo);
        $this->assign("g1",$ginfo);
        $this->assign("g2",$ginfo);
        $this->assign("g3",$ginfo);
        $this->assign("g4",$ginfo);
        $this->assign("g5",$ginfo);
        $this->assign("g6",$ginfo);
        $this->display();
    }

    //执行抽奖
    public function start(){
        if(!$this->check($this->openid,$this->lid)){
            $this->ajaxReturn(array("status"=>0,"info"=>"亲，别灰心下次再来哦!"));
        }
        $ginfo=M("wxgift")->where(array("openid"=>$this->openid,"lid"=>$this->lid))->order("id asc")->limit(8)->select();
        $gifts=$this->lottery->raffles($ginfo);//结果为所中奖品id
        $getInfo=M("wxgift")->field("tnum")->where(array("id"=>$gifts['yes']))->find();
        if($getInfo['tnum']<=0){
            $this->ajaxReturn(array("status"=>0,"info"=>"亲，别灰心下次再来哦!"));
        }
        $newInfo=array_map("array_shift",$ginfo);
        $row=array_keys($newInfo,$gifts['yes'],true);//显示商品在序列表中的位置
        $pos=$row[0]+1;
        $wxget['uid']=$this->openid;
        $wxget['lid']=$this->lid;
        $wxget['gid']=$gifts['yes'];
        $wxget['sn']="SN".time();
        $wxget['gtime']=date("Y-m-d H:i:s",time()).rand(1,1000);
        $wxget['ip']=$_SERVER["REMOTE_ADDR"];
        if($tid=M("wxget")->add($wxget)){
            $this->ajaxReturn(array("status"=>1,"info"=>"恭喜您中奖了!","data"=>array("tid"=>$tid,"pos"=>$pos)));
        }
    }

    //抽奖判断
    public function check($uid,$lid){
        //M("wxget")->where(array("lid"=>$lid,"uid"=>$uid))->find()
        $uinfo=M("wxlottery")->field("lottertimes")->where(array("lid"=>$lid,"uid"=>$uid))->find();
        if($uinfo['lottertimes']<=0){
            return false;
        }else{
            M("wxlottery")->where(array("lid"=>$lid,"uid"=>$uid))->setDec("lottertimes");
            M("wxlottery")->where(array("lid"=>$lid,"uid"=>$uid))->setInc("losttimes");
        }
        return true;
    }

    //填写资料地址
    public function touch(){
        $winfo=M("wxinfo")->field("uname,tel,address")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->find();
        $ginfo=M("wxget")->field("tp_wxgift.gname,tp_wxgift.level")->join("tp_wxgift on tp_wxgift.id=tp_wxget.gid")->where(array("tp_wxget.lid"=>$this->lid,"tp_wxget.uid"=>$this->openid))->select();
        $this->assign("ginfo",$ginfo);
        $this->assign("winfo",$winfo);
        $this->display();
    }

    public function address(){
        $_POST['uid']=$this->openid;
        $_POST['lid']=$this->lid;
        if(M("wxinfo")->field("id")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->find()){
            if(M("wxinfo")->where(array("lid"=>$this->lid,"uid"=>$this->openid))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>"修改失败"));
            }
        }else{
            if(M("wxinfo")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>"添加失败"));
            }
        }
    }
}