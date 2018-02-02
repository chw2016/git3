<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * tel:15889394741
 * notice:O2O个人中心
 * Date: 2015/1/9
 * Time: 17:14
 */
class MemberCenterAction extends BaseAction{
    public $openid;
    public $token;

    public $api;
    public $ip;
    public $style;
    //O2O分两种情况，微信用户则不需要登陆，如果不是微信用户则需要注册，使用注册之后的账号进行登陆
    public function _initialize(){
        parent::_initialize();
        $this->token=$this->_get("token");
        $this->openid=$this->_get("openid");

        $agent = $_SERVER['HTTP_USER_AGENT'];

        $this->api=C('baidu_map_api');

        $this->ip=$_SERVER['REMOTE_ADDR'];



       if(strpos($agent,"MicroMessenger")) {

            //微信用户请先关注
            $this->style=1;
            if(!M('Wxusers')->where(array('uid'=>$this->tpl['id'],'openid'=>$this->openid))->find()){
                exit("请先关注");
            }
        }else{
            //非微信用户，请先登录
            $this->style=2;
            if(session("O2O")!=$this->openid){
                $this->redirect(U('Login/Login',array("token"=>$this->token)));
            }
        }
       



        if(isset($_SESSION['lat']) &&  isset($_SESSION['lng'])){
            $this->location=1;
            $this->lng=$_SESSION['lng'];
            $this->lat=$_SESSION['lat'];
            $this->assign("location",$this->location);
            $this->assign("lng",$this->lng);
            $this->assign("lat",$this->lat);
        }

	    $this->assign("style",$this->style);
        $this->assign("tpl",$this->tpl);
        $this->assign("token",$this->token);
        $this->assign("openid",$this->openid);
    }

    //个人中心
    public function index(){
        $orderinfo=$this->OrderDetail($this->openid,$this->token);
        $sign=M("Shop_sign")->field("score")->where(array("token"=>$this->token,'openid'=>$this->openid))->find();

        $userscore = M("Shop_users")->field("score")->where(array("token"=>$this->token,'openid'=>$this->openid))->find();
        if(empty($userscore)){
            $score=0;
        }else{
            $score=$userscore['score'];
        }

        $level=$this->getLevel($score,$this->token,$this->openid);
        if(M("Shop_scoreset")->field("id")->where(array("token"=>$this->token,'status'=>1))->find()){
            $show=1;
            $this->assign("show",$show);
        }
//        echo "<pre>";
//        print_r($orderinfo);exit;
        $QrCode=$this->getCode();//生成二维码图片
        if($this->style==1){
            $user1=M("Wxusers")->field("nickname,headimgurl")->where(array("openid"=>$this->openid,'token'=>$this->token))->find();
            $this->assign("user1",$user1);
        }else{
            $user2=M("Cusers")->field("openid")->where(array("mduser"=>$this->openid,'token'=>$this->token))->find();
            $this->assign("user2",$user2);
        }
        $this->assign("qrcode",$QrCode);
        $this->assign("score",$score);
        $this->assign("level",$level);
        $this->assign("orders",$orderinfo);
        $this->display();
    }

    //获取用户的积分
    public function getLevel($score,$token,$openid){
        $condition['token']=$this->token;
        $condition['openid']=$this->openid;
        $condition['status']=1;
        $condition['scope']=array("egt",$score);

        $level=M("Shopgrade")->field("name,pic,sign_score")->where($condition)->order("scope asc")->find();
        if(!$level){
	     $level=M("Shopgrade")->field("name,pic,sign_score")->where(array('token'=>$this->token))->order("scope desc")->limit(1)->find();
	}
        return $level;
    }
    //获取收藏数据
    public function getCollect(){
        if(IS_AJAX){
            $collect=M("Collect")->field("tp_collect.id,tp_collect.sid,tp_shop.username,tp_shop.des,tp_shop.pic,tp_shop.long,tp_shop.lat")->join("join tp_shop on tp_shop.id=tp_collect.sid")->where(array("tp_collect.openid"=>$this->openid,'tp_collect.token'=>$this->token))->select();
            $long1=$this->_get("lng");
            $lat1=$this->_get("lat");
            if(!$long1 && !$lat1){
                $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$this->ip."&coor=bd09ll";
                $location=json_decode(file_get_contents($url),true);
                $long1=floatval($location['content']['point']['x']);//起点x坐标
                $lat1=floatval($location['content']['point']['y']);//起点y坐标
            }
            $collection=$this->getinfo($collect,$lat1,$long1);
            $this->ajaxReturn(array("status"=>0,"info"=>"操作成功!","shops"=>$collection));
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"非法操作!"));
        }
    }

    //取消个人收藏
    public function cancel(){
        if(IS_AJAX){
            if(M("Collect")->where(array("id"=>$_POST['cid']))->find()){
                if(M("Collect")->where(array("id"=>$_POST['cid']))->delete()){
                    $this->ajaxReturn(array("status"=>1,"info"=>"取消成功!"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"取消失败!"));
                }
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"非法操作!"));
            }
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"非法操作!"));
        }
    }

    //获取订单信息
    public function OrderDetail($openid,$token){
        //主订单信息

        $orderinfo=M("Mainorder")->field("id,buytime,totalmoney,paytype,paystatus,sendstatus,ordernumber,noget_money,score_money,score")->where(array("openid"=>$this->openid,"token"=>$this->token,'paytype'=>array('neq',0),'paystatus'=>array('neq',-1)))->order('buytime desc')->select();

        foreach($orderinfo as $k=>$v){
            $detail=M("Sideorder")->field("tp_shop.username,tp_sidedetail.gname,tp_sidedetail.num,tp_sidedetail.total")->join("join tp_sidedetail on tp_sideorder.id=tp_sidedetail.sid join tp_shop on tp_shop.id=tp_sideorder.sid")->where(array("tp_sideorder.openid"=>$this->openid,"tp_sideorder.token"=>$this->token,"tp_sideorder.mid"=>$v['id']))->select();
            $orderinfo[$k]['detail']=$detail;
        }
        return $orderinfo;
    }

    //店铺距离信息
    public function getinfo($data=array(),$lat1,$long1){
        $R = 6370996.81;
        foreach($data as $k=>$v){
            $lat2=floatval($v['lat']);
            $long2=floatval($v['long']);
            $distance= $R*acos(cos($lat1*pi()/180 )*cos($lat2*pi()/180)*cos($long1*pi()/180 -$long2*pi()/180)+ sin($lat1*pi()/180 )*sin($lat2*pi()/180));
            $data[$k]['distance']=$distance;
        }
        $data=$this->paixu($data,"distance","asc");
        foreach($data as $k=>$v){
            if($v['distance']>=1000){
                $data[$k]['distance']=(round($v['distance']/1000))."公里";
            }else{
                $data[$k]['distance']=round($v['distance'])."米";
            }
        }
        return $data;
    }

    //二维数组排序
    public function paixu($arr,$field,$type="asc"){
        $arr_temp=array();
        foreach($arr as $k=>$v){
            $arr_temp[$v[$field]]=$v;
        }
        if($type=="asc"){
            ksort($arr_temp);
        }else{
            krsort($arr_temp);
        }
        return $arr_temp;
    }

    //下载二维码

    /*员工生成临时二维码*/
    public function getCode() {
        $userinfo=M("Wxusers")->field("id")->where(array("openid"=>$this->openid))->find();
        $parament = '{"action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 150'.$userinfo['id'].'}}}';
        /*获取access_token*/
        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        if($api){
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
            $json = json_decode(file_get_contents($url_get));
            $access_token = $json->access_token;
            $imgSource = $this->creatTicket($access_token, $parament);
        }
        return $imgSource['header']['url'];
    }

    public function creatTicket($token, $parament) {

        /*发送数据到微信服务器端并获取数据*/
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
        $result = $this->api_notice_increment($url, $parament);
        $jsonInfo = json_decode($result, true);
        $ticket = $jsonInfo['ticket'];

        /*根据ticket获取图片资源*/
        $url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpInfo = curl_getinfo($ch);
        return array_merge(array('body'=>$package), array('header'=>$httpInfo));
    }

    //点击签到
    public function sign(){
        if(IS_AJAX){

            $sign=M("Shop_sign")->field("score")->where(array("token"=>$this->token,'openid'=>$this->openid))->find();

            /*
             * 真正用户积分
             */
            $userscore = M("Shop_users")->field("score")->where(array("token"=>$this->token,'openid'=>$this->openid))->find();

            if(empty($userscore)){

                $score=0;

            }else{

                $score=$userscore['score'];

            }

            $ScoreSet=M("Shop_scoreset")->field("day_score,days,scores")->where(array("token"=>$this->token,'status'=>1))->find();

            $level=$this->getLevel($score,$this->token,$this->openid);
            if($level){
                $ScoreSet['day_score'] = $level['sign_score'];
            }

            if($data=M("Shop_sign")->where(array("token"=>$this->token,"openid"=>$this->openid))->find()){
                $lastsign=$data['lastsign'];
                $time=strtotime(date("Y-m-d",$lastsign));
                $oneday=3600*24;
                $twodays=3600*24*2;
                $timediff=time()-$time;
                //已经签到
                if($timediff<=$oneday){
                    $this->ajaxReturn(array("status"=>0,"info"=>"您今天已经签到了哦，明天再来!"));
                }
                //不是连续签到
                if($timediff>=$twodays){
                    $update=M("Shop_sign")->where(array("token"=>$this->token,'openid'=>$this->openid))->save(array("lastsign"=>time(),"score"=>$data['score']+$ScoreSet['day_score']));
                    //更新用户积分
                    $updateUserscore=M("Shop_users")->where(array("token"=>$this->token,'openid'=>$this->openid))->setInc('score',$ScoreSet['day_score']);

                    if($update && $updateUserscore){
                        $score=$data['score']+$ScoreSet['day_score'];
                        $level=$this->getLevel($score,$this->token,$this->openid);
                        $this->ajaxReturn(array("status"=>1,"info"=>"成功获得签到积分".$ScoreSet['day_score'],"score"=>$ScoreSet['day_score'],"level"=>$level));
                    }else{
                        $this->ajaxReturn(array("status"=>0,"info"=>"操作失败，请重试!"));
                    }
                }
                //连续签到
                if($timediff>$oneday && $timediff<$twodays){
                    M("Shop_sign")->where(array("token"=>$this->token,'openid'=>$this->openid))->setInc("serail",1);
                    $totalSerail=intval($data['serail'])+1;
                    if($totalSerail>=$ScoreSet['days']){
                        $update=M("Shop_sign")->where(array("token"=>$this->token,'openid'=>$this->openid))->save(array("serail"=>0,"lastsign"=>time(),"score"=>$data['score']+$ScoreSet['day_score']+$ScoreSet['scores']));

                        $updateUserscore=M("Shop_users")->where(array("token"=>$this->token,'openid'=>$this->openid))->setInc('score',$ScoreSet['day_score']+$ScoreSet['scores']);
                        if($update && $updateUserscore){
                            $score=$data['score']+$ScoreSet['day_score']+$ScoreSet['scores'];
                            $level=$this->getLevel($score,$this->token,$this->openid);
                            $this->ajaxReturn(array("status"=>1,"info"=>"成功获得签到积分".$ScoreSet['day_score'].",连续签到几分奖励".$ScoreSet['scores'],"score"=>$ScoreSet['day_score']+$ScoreSet['scores'],"level"=>$level));
                        }else{
                            $this->ajaxReturn(array("status"=>0,"info"=>"操作失败，请重试!"));
                        }
                    }else{
                        $update=M("Shop_sign")->where(array("token"=>$this->token,'openid'=>$this->openid))->save(array("lastsign"=>time(),"score"=>$data['score']+$ScoreSet['day_score']));

                        $updateUserscore=M("Shop_users")->where(array("token"=>$this->token,'openid'=>$this->openid))->setInc('score',$ScoreSet['day_score']);
                        if($update && $updateUserscore){
                            $score=$data['score']+$ScoreSet['day_score'];
                            $level=$this->getLevel($score,$this->token,$this->openid);
                            $this->ajaxReturn(array("status"=>1,"info"=>"成功获得签到积分".$ScoreSet['day_score'],"score"=>$ScoreSet['day_score'],"level"=>$level));
                        }else{
                            $this->ajaxReturn(array("status"=>0,"info"=>"操作失败，请重试!"));
                        }
                    }
                }
            }else{
                $update['lastsign']=time();
                $update['openid']=$this->openid;
                $update['token']=$this->token;
                $update['score']=$ScoreSet['day_score'];
                $update['type']=$this->style;
                $update['serail']=1;



                if(M("Shop_sign")->add($update)){
                    M("Shop_users")->where(array("token"=>$this->token,'openid'=>$this->openid))->setInc('score',$ScoreSet['day_score']);
                    $score=$ScoreSet['day_score'];
                    $level=$this->getLevel($score,$this->token,$this->openid);
                    $this->ajaxReturn(array("status"=>1,"info"=>"成功获得签到积分".$ScoreSet['day_score']."!","score"=>$update['score'],"level"=>$level));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"操作失败，请重试!"));
                }
            }
        }else{
            exit("非法操作!");
        }
    }

    public function ScoreList(){
        $count=M("Shop_sign")->where(array("token"=>$this->token))->count();
        echo $count;exit;
        $page=new Page($count,20);
        $scoreInfo=M("Shop_sign")->field("id,score,type,openid")->where(array("token"=>$this->token))->select();
        foreach($scoreInfo as $k=>$v){
            $scoreInfo[$k]['level']=$this->getLevel($v['score'],$this->token,$v['openid']);
            if($v['type']==1){
                $user=M("Cusers")->field("openid")->where(array("mduser"=>$v['openid']))->find();
            }else{
                $user=M("Wxusers")->field("nickname")->where(array("openid"=>$v['openid']))->find();
            }
        }
        echo "<pre>";
        print_r($scoreInfo);exit;
    }
}