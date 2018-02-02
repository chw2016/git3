<?php
/**

 * Created by IntelliJ IDEA.

 * User: 肖国平（small apple）

 * Date: 15-1-6

 * Time: 下午5:47

 * To change this template use File | Settings | File Templates.

 */
class CommercedybAction extends BaseAction{
    public $openid;
    public $token;
    public $api;
    public $ip;
    public $style;//用户登录方式 1为微信登陆 2为客户端登陆
    public $location;
    public $lng;
    public $lat;
    public $shopScoresetdata;
    public $userscore;
    // 根据指定字符串来截取字符
    public $str;    
    public $start_str;    
    public $end_str;    
    public $start_pos;    
    public $end_pos;    
    public $c_str_l;    
    public $contents;
    public $share;
    public $appidInfo;
    public $is_subscribe = 1;
    //O2O分两种情况，微信用户则不需要登陆，如果不是微信用户则需要注册，使用注册之后的账号进行登陆
    public function _initialize(){
        parent::_initialize();
        //$this->token=$this->_get("token");
	    //先做一个测试openid账号
        //$this->openid=$this->_get("openid");
        //$this->openid = "oPTDJt42eoK4zxy6cAoXTiS3V7w";
        
        /*跳转关注*/
        $aNickuser = M('Shop_users')->where(array(
                        'token'=>$this->token,
                        'openid'=>$this->openid
                    ))->find();
        if($this->token == '3db7fee419649f8be761dfc4f6b42ecc'){
	    if($aNickuser['status'] == 0 && ACTION_NAME != 'indexdyb'){
	        $url = C('site_url').'index.php?g=Wap&m=Commercedyb&a=indexdyb&token='.$this->token.'&openid='.$this->openid.'&from_openid='.$_GET['from_openid'];
	        $this->redirect($url);
	    }
	    $this->gotoGuanzhu();
	}



        $agent = $_SERVER['HTTP_USER_AGENT'];
        $this->api=C('baidu_map_api');
        $this->ip=$_SERVER['REMOTE_ADDR'];
        $this->style=1;
        // QQ抓取表情
        require_once('./Common/expression.php');
        $express = implode(',',$express);

        $this->assign('express',$express);
        /*if(strpos($agent,"MicroMessenger")) {
            //微信用户请先关注
            $this->style=1;
            if(!M('Wxusers')->where(array('uid'=>$this->tpl['id'],'openid'=>$this->openid))->find()){
               exit("请先关注");
            }
        }else{
            //非微信用户，请先登录
            $this->style=2;
            if(session("O2O")!=$this->openid || !M("Cusers")->where(array("token"=>$this->token,'mduser'=>$this->openid))->find()){
                $this->redirect(U('Login/Login',array("token"=>$this->token)));
            }
        }
	*/
        $tpl = M('wxuser')->where(array('token'=>$this->token))->find();

	$appid = M('Diymen_set')->where(array('token'=>$this->token))->find();
        $this->assign('appidInfo',$appid);
        // 分享到朋友圈以及好友的数据
	$this->share = array(
            'title' => $tpl['name'].'分享',
            'pic' => $tpl['headpicurl'],
            'descript' => '这是'.$tpl['name'].'的分享'
        );
	$this->assign('share',$this->share);
        
        /*
       * 引入微信js接口
       */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);

        if($_GET['lat'] != 0){
            $_SESSION['lat'] = $_GET['lat'];
        }

        if($_GET['lng'] != 0){
            $_SESSION['lng'] = $_GET['lng'];
        }

        /*
         * 积分金额设定
         */
        $shopScoresetdata = M('Shop_scoreset')->where(array('token'=>$this->token))->find();
        $this->shopScoresetdata = $shopScoresetdata;
        $this->assign('shopScoresetdata',$shopScoresetdata);

        /*
         * 用户积分
         */
        $userscore = M("Shop_users")->where(array("token"=>$this->token,'openid'=>$this->openid))->find();
        $this->userscore = $userscore;

        $this->assign('userscore',$userscore);

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
        $this->assign("is_subscribe",$this->is_subscribe);
    }

    public function putLocation(){
        $_SESSION['lng']=$_POST['lng'];
        $_SESSION['lat']=$_POST['lat'];
        $this->ajaxReturn(array("status"=>1,"info"=>"操作成功!"));
    }
    //O2O首页
    public function index(){
        if(isset($_GET['cname'])){
            $this->assign("cname",$this->_get("cname"));
        }
        $flashes=M("Oflash")->field("pic,url")->where(array("token"=>$this->token,'type'=>1,"tid"=>0))->select();
        $type=M("Shoptype")->field("id,pic,name")->where(array("token"=>$this->token,'position'=>array('in',array(1,0))))->select();
        $type2=M("Shoptype")->field("id,pic,name")->where(array("token"=>$this->token,'position'=>2))->select();
        $set=M("Baseset")->field("topnav,botnav")->where(array("token"=>$this->token))->find();
        $nav=M("Navigation")->field("name,linkurl,pic")->where(array("token"=>$this->token))->limit(4)->select();
        $top=$set['topnav'];
        $bot=$set['botnav'];
        $this->assign("top",$top);
        $this->assign("nav",$nav);
        $this->assign("bot",$bot);
        $this->assign("type",$type);
        $this->assign("type2",$type2);
        $this->assign("flashes",$flashes);
        $this->display();
    }

    //附近社区
    public function nearUnion(){
        $long1=$this->_get("lng");
        $lat1=$this->_get("lat");
        if(!$long1 && !$lat1){
            $ip=get_client_ip();
            $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
            $location=json_decode(file_get_contents($url),true);
            $long1=floatval($location['content']['point']['x']);//起点x坐标
            $lat1=floatval($location['content']['point']['y']);//起点y坐标
        }
        $data=M("Shopunion")->field("id,cname,des,pic,long,lat")->where(array("token"=>$this->token))->select();
        $union=$this->getinfo(50000,$data,$lat1,$long1);
        $this->assign("union",$union);
        $this->display();
    }


    //对应类型下的店铺列表页面
    public function ShopList(){

        import('ORG.IpLocation');// 导入IpLocation类
        $ip = get_client_ip();
        $Ip = new IpLocation("UTFWry.dat"); // 实例化类
        $location = $Ip->getlocation($ip); // 获取某个IP地址所在的位置
        if(isset($_GET['access'])){
            $access=$this->_get("access");
        }else{
            exit("非法操作!");
        }
        if($access=="classfy"){
            if(isset($_GET['typeid'])){
                $typeid=$this->_get("typeid","intval");
            }else{
                exit("非法操作!");
            }
            $long1=$this->_get("lng");
            $lat1=$this->_get("lat");
            if(!$long1 && !$lat1){
                $ip=get_client_ip();
                $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
                $location=json_decode(file_get_contents($url),true);

                $long1=floatval($location['content']['point']['x']);//起点x坐标
                $lat1=floatval($location['content']['point']['y']);//起点y坐标
            }
            $data=M("Shop")->field("id,username,des,pic,long,lat,status,tel,address")->where(array("tid"=>$typeid,"token"=>$this->token,'status'=>1))->order("status desc")->select();
        }
        if($access=="union" || $access=="search"){
            $lat1=floatval($this->_get("lat"));
            $long1=floatval($this->_get("long"));
            if($lat1 && $long1){
                $data=M("shop")->field("id,username,des,pic,long,lat,status,tel")->where(array("token"=>$this->token,'status'=>1))->order("status desc")->select();
            }else{
                exit("非法操作!");
            }
        }

        $ip=get_client_ip();
        $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
        $location=json_decode(file_get_contents($url),true);
        $lng=$location['content']['point']['x'];//起点x坐标
        $lat=$location['content']['point']['y'];//起点y坐标
        $getPosUrl="http://api.map.baidu.com/geocoder/v2/?ak={$this->api}&location={$lat},{$lng}&output=json&pois=0";
        $areainfo=json_decode(file_get_contents($getPosUrl),true);
        $condition['seng'] = array('like','%'.$areainfo['result']['addressComponent']['province'].'%');
        $condition['si'] = array('like','%'.$areainfo['result']['addressComponent']['city'].'%');
        //$condition['xian'] = array('like','%'.$areainfo['result']['addressComponent']['district'].'%');

        /*
         * topher 修复
         */

        $condition['token'] = $this->token;
        $shopmember = M('Shopmember')->where($condition)->find();
        if($shopmember) {
            $flashes = M("Oflash")->field("pic,url")->where(array("token" => $this->token, 'type' => 3, 'tid' => $shopmember['id']))->select();
        }
        /**
         * 出经纬度调这里啊
         */
        $shopdistance = M('Baseset')->where(array('token'=>$this->token))->find();
        $distance = $shopdistance['union_distance'];
        if(!$distance){
			$distance = 99999999999;
        }
        $shopinfo=$this->getinfo($distance,$data,$lat1,$long1);
        //$shopinfo=$data;
        $type=M("Shoptype")->field("id,name")->where(array("token"=>$this->token))->select();
        /**
         * 门店列表提示消息
         */
        $msg=M('Baseset')->where(array('token'=>$this->token))->getField('msg');
        $this->assign("msg",$msg);
        $this->assign("lat",$lat1);
        $this->assign("lng",$long1);
        $this->assign("type",$type);
        $this->assign("shopinfo",$shopinfo);
        $this->assign("flashes",$flashes);
        $this->display();
    }

    //分类ajax商品数据
//    public function ShopAjax(){
//        if(IS_AJAX){
//            $cid=$_POST['cid'];
//            if($cid){
//                $wareinfo=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.name,tp_shopware.price,tp_shopware.vprice,tp_shopware.pic,tp_shop.username")->where(array("tp_shopware.tid"=>$cid))->order("tp_shopware.id asc")->select();
//                $newinfo=array();
//                foreach($wareinfo as $k=>$v){
//                    $newinfo[$v['id']]=$v;
//                }
//                $this->ajaxReturn(array("status"=>1,"info"=>"操作成功!","ware"=>$newinfo));
//            }
//        }
//    }

//    //店铺类型ajax数据
//    public function TypeAjax(){
//        if(IS_AJAX){
//                $data=M("Shop")->field("id,username,des,long,lat,pic")->where(array("token"=>$this->token,"tid"=>$_POST['tid']))->select();
//                $lat1=$this->_get("lat");
//                $long1=$this->_get("lng");
//            if(!$long1 && !$lat1){
//                $ip="113.110.228.94";
//                $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
//                $location=json_decode(file_get_contents($url),true);
//                $long1=floatval($location['content']['point']['x']);//起点x坐标
//                $lat1=floatval($location['content']['point']['y']);//起点y坐标
//            }
//                $shops=$this->getinfo(50,$data,$lat1,$long1);
//                $this->ajaxReturn(array("status"=>1,"info"=>"操作成功!","shops"=>$shops));
//        }else{
//            $this->ajaxReturn(array("status"=>0,"info"=>"非法操作!"));
//        }
//    }
    //对应分类商品
    public function ShopWare(){

        /*if($this->token == 'a5114ab1a60c81d04e86447a0bd123be'){
            //判断常用店铺
            if($_GET['isset'] == 1){
                M('Shop_users')->where(array('token' => $this->token,'openid' => $this->openid))->save(array('shop_id'=>$_GET['wareid']));
            }else {
                $users = M('Shop_users')->where(array('token' => $this->token, 'openid' => $this->openid))->find();
                if ($users['shop_id']) {
                    $_GET['wareid'] = $users['shop_id'];
                } else {
                    $this->redirect(C('site_url') . 'index.php?g=Wap&m=Commerce&a=location&token=' . $this->token . '&openid=' . $this->openid);
                }
            }
        }
        */


        import("Org.Data");

        if(isset($_GET['wareid'])){
            $wareid=$this->_get("wareid","intval");
            $cartinfo=M("Shopclassfy")->field("id,tname,pid,pic")->where(array("branch_id"=>$wareid,'token'=>$this->token))->order("sort asc")->select();
            $shopinfo=M("Shop")->field("username,des,start_time,end_time,yingye_status,id,waimai_price,min_price")->where(array("id"=>$wareid,'token'=>$this->token))->find();
            $wareinfo=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.attr,tp_shopware.stock,tp_shopware.name,tp_shopware.tid,tp_shopware.price,tp_shopware.vprice,tp_shopware.des,tp_shopware.pic,tp_shop.username")->where(array("tp_shopware.sid"=>$wareid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
            $wareinfo1=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.attr,tp_shopware.stock,tp_shopware.name,tp_shopware.tid,tp_shopware.price,tp_shopware.vprice,tp_shopware.des,tp_shopware.pic,tp_shop.username")->where(array("tp_shopware.sid"=>$wareid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();

        }

        if(isset($_GET['cid'])){
            $cartid=$this->_get("cid","intval");
            $shopinfo=M("Shopclassfy")->join("join tp_shop on tp_shop.id=tp_shopclassfy.branch_id")->field("tp_shop.username,tp_shop.start_time,tp_shop.end_time,tp_shop.yingye_status,tp_shop.waimai_price,tp_shop.min_price,tp_shop.id")->where(array("tp_shopclassfy.id"=>$cartid))->find();
            $wareid=M("Shopclassfy")->field("branch_id")->where(array("id"=>$cartid))->find();
            $cartinfo=M("Shopclassfy")->field("id,tname,pid,pic")->where(array("branch_id"=>$wareid['branch_id']))->order("sort asc")->select();
            $wareinfo=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.stock,tp_shopware.tid,tp_shopware.attr,tp_shopware.name,tp_shopware.price,tp_shopware.vprice,tp_shopware.des,tp_shopware.pic,tp_shopware.sid,tp_shop.username")->where(array("tp_shopware.tid"=>$cartid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
            $wareinfo1=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.stock,tp_shopware.tid,tp_shopware.attr,tp_shopware.name,tp_shopware.price,tp_shopware.vprice,tp_shopware.des,tp_shopware.pic,tp_shopware.sid,tp_shop.username")->where(array("tp_shopware.tid"=>$cartid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
        }
        foreach($wareinfo as $ks=>$vs){
            $temp = array();
            $temp = M('Shopclassfy')->where(array('token'=>$this->token,'id'=>$vs['tid']))->find();
            $wareinfo[$ks]['tname'] = $temp['tname'];
        }
        $newinfo=array();
        foreach($wareinfo1 as $k=>$v){
            $newinfo[$v['id']]=$v;
        }
        //$newinfo 商品列表
        $warejson=json_encode($newinfo);
        $flashes=M("Oflash")->field("pic,url")->where(array("token"=>$this->token,'type'=>4,"tid"=>$this->_get("wareid","intval"),"status"=>1))->select();

        $carts=Data::channelLevel($cartinfo);

        /**
         * 得最小外卖单
         */

        $this->assign("carts",$carts);
        $this->assign("wareid",$wareid);
        $this->assign("shopinfo",$shopinfo);
        $this->assign("warejson",$warejson);//JSON格式
        $this->assign("flashes",$flashes);
        $this->assign("wareinfo",$wareinfo);//商品信息表
        $this->assign("cid",$_GET['cid']);
        $this->assign('newinfo',$newinfo);
        if($this->token=='a5114ab1a60c81d04e86447a0bd123be'){
            //小二快快

            $this->display('tpl/Wap/default/Commerce_ShopWare_old.html');
        }else{
            //p($newinfo);
            $this->display();
        }
    }

    public function ShopWareajax(){
        $count = $_REQUEST['count'];
        if(isset($_GET['wareid'])){
            $wareid=$this->_get("wareid","intval");
            $wareinfo=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.stock,tp_shopware.tid,tp_shopware.name,tp_shopware.price,tp_shopware.vprice,tp_shopware.pic,tp_shopware.sid,tp_shop.username")->where(array("tp_shopware.sid"=>$wareid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->limit($count*21,21)->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
        }
        if(isset($_GET['cid'])){
            $cartid=$this->_get("cid","intval");
            $wareinfo=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.stock,tp_shopware.tid,tp_shopware.name,tp_shopware.price,tp_shopware.vprice,tp_shopware.pic,tp_shopware.sid,tp_shop.username")->where(array("tp_shopware.tid"=>$cartid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->limit( $count*21,21)->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
        }
        foreach($wareinfo as $ks=>$vs){
            $temp = array();
            $temp = M('Shopclassfy')->where(array('token'=>$this->token,'id'=>$vs['tid']))->find();
            $wareinfo[$ks]['tname'] = $temp['tname'];
        }

        echo $this->encode($wareinfo);


    }

    //结账页面
    public function account(){
        $goods_list=htmlspecialchars_decode($_GET['goods_list']);
        $goods_list=json_decode($goods_list,true);
        $oid=$this->_get("oid","intval");
        if(!M("Mainorder")->where(array("id"=>$oid))->find()){
            exit("订单不存在");
        }
        $orderinfo=M("Mainorder")->field("id,ordernumber,totalnum,totalmoney")->where(array("id"=>$oid,"status"=>0))->find();
        $address=M("Msinfo")->where(array("openid"=>$this->openid,"token"=>$this->token))->find();
        $money=M("Shop_users")->where(array("openid"=>$this->openid,"token"=>$this->token))->getField('money');
        $dyb=M('Shop_users')->where(array('openid'=>$this->openid,'token'=>$this->token))->getField('dyb');//得到德艺币
        $waimai_price=M('Shop')->where(array('id'=>$_GET['shopid']))->getField('waimai_price');//跑腿费
        $min_price=M('Shop')->where(array('id'=>$_GET['shopid']))->getField('min_price');//起送费
        $this->assign("min_price",$min_price);
        $this->assign("waimai_price",$waimai_price);
        $this->assign("orderinfo",$orderinfo);
        $this->assign("address",$address);
        $this->assign("goods_list",$goods_list['goods_list']);//把购买的商品详细附值下去
        $this->assign("oid",$oid);
        $this->assign("money",$money);
        $this->assign("dyb",$dyb);
        $this->assign('shopid',$_GET['shopid']);
        $this->display();
    }

    //支付时修改订单信息,插入订单地址
    public function EditOrder(){
        if(IS_AJAX){
            $paytype=$this->_get("paytype");
            $ddress['buyname']=$this->_get("uname");
            $ddress['uname']=$this->_get("uname");
            $ddress['tel']=$this->_get("tel");
            $ddress['address']=$this->_get("address");
            $ddress['token']=$this->token;
            $ddress['openid']=$this->openid;
            $ddress['wm']=$this->_post('wm');
            $ddress['noget_money']=$this->_post('waimai_price');//外卖跑腿费钱
            //保存个人资料地址
            $oMsinfo=M('Msinfo');
           // $oMsinfo->add($ddress);
            $aMsinfo=$oMsinfo->where(array("token"=>$this->token,"openid"=>$this->openid))->find();

            if($aMsinfo['id']){
                $oMsinfo->where(array('id'=>$aMsinfo['id']))->save($ddress);
            }else{
                $oMsinfo->add($ddress);
            }
            M("Mainorder")->where(array("id"=>$this->_get('oid',"intval"),"token"=>$this->token,"openid"=>$this->openid))->save($ddress);
            /*$aid=$_POST['wid'];
            $update=M("Msinfo")->field("uname,tel,address")->where(array("id"=>$aid))->find();
            $update['buyname']=$update['uname'];
            $update['instruct']=$_POST['instruct'];*/
            $update['buyname']=$_POST['uname'];
            $update['instruct']=$_POST['instruct'];

            $order = M("Mainorder")->where(array("id"=>$this->_get('oid',"intval"),"token"=>$this->token,"openid"=>$this->openid))->find();
            /*if($order['totalmoney'] < $this->shopScoresetdata['notget_money']){
                $update['noget_money'] = $this->shopScoresetdata['add_money'];
            }else{
                $update['noget_money'] = 0;
            }*/

            $scoreid = $_POST['scoreid'];

            $subscore = 0;//需要减掉的分数
            $addscore = 0;//需要减掉的分数

            $usedscore = $_POST['usedscore'];
            if($usedscore == 2){
                //使用
                if($scoreid){
                    if($this->userscore > $scoreid){
                        $subscore = $scoreid;
                        $update['score_money'] = round($scoreid/$this->shopScoresetdata['moneyscore'],2);
                    }else{
                        $update['score_money'] = 0;
                        $update['score_money'] = 0;
                    }
                }
            }else{
	    	$update['score_money'] = 0;
	    }

            //是否下单送积分
            if($this->shopScoresetdata['orderscore']){

                $update['score'] = round($order['totalmoney']*$this->shopScoresetdata['orderscore']);
                $addscore = $update['score'];
            }else{
                $update['score'] = 0;
            }
	    
            $scoredata = M('dy_score')->field('sum(score) as allscore')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('gt',0)))->select();
            $money['score'] = $scoredata[0]['allscore'];
            $where['token']=$this->token;
            $where['scope']=array('elt',$money['score']);
            $bili = $this->getUserLevelBili($this->token,$this->openid);
            if($bili){
                $update['xianjin_b'] = round(($order['totalmoney']-$update['score_money'])*$bili,1);
            }else{
                $update['xianjin_b'] = 0;
            }
            



            if($paytype=="wxpay"){
                $update['paytype']=1;
		//是否下单送积分
                if($this->shopScoresetdata['orderscore']){
                  $update['score'] = round(($order['totalmoney']-$update['score_money'])*$this->shopScoresetdata['orderscore']);
                  $addscore = $update['score'];
                }else{
                  $update['score'] = 0;
                }
            }
            if($paytype=="yepay"){
                $update['paytype']=5;
		            //是否下单送积分
                if($this->shopScoresetdata['orderscore']){
                  $update['score'] = round(($order['totalmoney']-$update['score_money'])*$this->shopScoresetdata['orderscore']);
                  $addscore = $update['score'];
                }else{
                  $update['score'] = 0;
                }
            }
            if($paytype=="xxpay"){
                $update['paytype']=3;
                $update['score']=0;//线下支付时不产生消费币
                $update['xianjin_b'] = 0;
		$update['score_money']=0;
            }
         //   p($update);die;
            if(M("Mainorder")->where(array("id"=>$this->_get('oid',"intval"),"token"=>$this->token,"openid"=>$this->openid))->save($update)){
                //减掉积分
                //M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setDec('score',$subscore);

                //新增积分
               // M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setInc('score',$addscore);
                //更新复订单地址
                $orderres = M("Mainorder")->where(array("id"=>$this->_get('oid',"intval"),"token"=>$this->token,"openid"=>$this->openid))->find();

                $sideorderdata = M('Sideorder')->where(array('mid'=>$orderres['id']))->select();
                //减少库存
                foreach ($sideorderdata as $korder => $vorder) {
                    $detailInfo = array();
                    $detailInfo=M("Sidedetail")->where(array("sid"=>$vorder['id']))->select();
                    foreach($detailInfo as $v){
                        //减少库存
                        M('Shopware')->where(array('token'=>$this->token,'id'=>$v['gid']))->setDec('stock',$v['num']);
                    }
                }



                M("Sideorder")->where(array('token'=>$this->token,'mid'=>$order['id']))->save(array('buyname'=>$orderres['buyname'],'tel'=>$orderres['tel'],'address'=>$orderres['address']));

                //发送短信通知渠道店铺管理员
                $members=M("Shopmember")->field("tel")->where(array("token"=>$this->token))->select();
                foreach($members as $v){
                    $content="尊敬的客户{$order['buyname']}，您已经成功下单，订单号为{$order['ordernumber']}";
                    $this->sendMessage($v['tel'],$content);
                }

                /*
                 * 通知微信
                 */
                $orderdata = M("Mainorder")->where(array("id"=>$this->_get('oid',"intval"),"token"=>$this->token,"openid"=>$this->openid))->find();
                $orderDetail=M("Sideorder")->field('tp_sidedetail.gname,tp_sidedetail.num,tp_sidedetail.price')->join('join tp_sidedetail on tp_sideorder.id=tp_sidedetail.sid')->where(array('tp_sideorder.mid'=>$this->_get('oid','intval'),"tp_sideorder.token"=>$this->token,"tp_sideorder.openid"=>$this->openid))->select();
                $strDetail="订单详情:\n";
		        foreach ($orderDetail as $k => $v) {
                    $strDetail .= ($k + 1) . '、' . $v['gname'] . '×' . $v['num'] . " = ".$v['num']*$v['price']."元\n";
                }
		


		if($update['score_money'] != 0){
	        	$score_money = $update['score_money'];
	        }else{
			$score_money = 0;	
		}
	        if($update['score'] != 0){
	        	$score = $update['score'];
	        }else{
			$score = 0;
		}
	        if($update['xianjin_b'] != 0){
	        	$xianjin_b = $update['xianjin_b'];
	        }else{
			$xianjin_b = 0;
		}
		
		$shijizhifu = $order['totalmoney']-$update['score_money'];
		
                $notichcontent = $this->wxusers['nickname']."您好,交易提醒\n订单编号：".$orderdata['ordernumber']."\n创建时间:".$orderdata['buytime']."\n订单总额:".$orderdata['totalmoney']."元\n".$strDetail."收货人:".$orderdata['buyname']."\n电话:".$orderdata['tel']."\n地址:".$orderdata['address']."\n实际支付:".$shijizhifu."元\n抵扣得意币:".$score_money."个\n赠送得意币:".$xianjin_b."个\n赠送积分:".$score."分\n感谢您对德亿堡的支持！德亿吃，堡快乐！祝您生活愉快！";
                $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
                $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                $data = $this->api_notice_increment($url,http_build_query($postdata));
                if(!$data){
                    $this->api_notice_increment($url,http_build_query($postdata));
                }
                /**
                 * 余额支付
                 **/
                if($paytype=='yepay') {
                    $oid=$this->_get('oid',"intval");
                    $orderdata = M("Mainorder")->where(array("token" => $this->token, "openid" => $this->openid, "id" => $oid))->find();
                    $update1['paystatus']=1;
                    /**
                     * 把支付状态改为1代表成功
                     */
                    M("Mainorder")->where(array("id"=>$this->_get('oid',"intval"),"token"=>$this->token,"openid"=>$this->openid))->save($update1);
                    /**
                     * 这里已经代表余额支付成功，所有分表订单 支付状态要改为1
                     */
                    M("Sideorder")->where(array("mid"=>$this->_get('oid',"intval"),"token"=>$this->token,"openid"=>$this->openid))->save($update1);
                    
                    /**
                     *Ye_record表里面加入一条记录
                     */

                    $Oshop_users = M('Shop_users');


                    $update2['token']=$this->token;
                    $update2['openid']=$this->openid;
                    $update2['pay_type']=2;
                    $update2['add_time']=time();
                    $update2['money']=$orderdata['totalmoney'] - $orderdata['score_money']+$orderdata['noget_money'];
                    $update2['status']=2;
                    M('Ye_record')->add($update2);
                    $a = $Oshop_users->where(array("openid" => $this->openid, "token" => $this->token))->setDec('money', $orderdata['totalmoney'] - $orderdata['score_money']+$orderdata['noget_money']);//减余额
                    
                    //新增积分
                    if($orderdata['score']>0){
                        $scoreData = array();
                        $scoreData['token']=$this->token;
                        $scoreData['openid']=$this->openid; 
                        $scoreData['score'] = $orderdata['score'];
                        $scoreData['type'] = 9;
                        $scoreData['addtime'] = date("Y-m-d H:i:s",time());
                        M('Dy_score')->add($scoreData);//这里是增加积分
                        M('Shop_users')->where(array("token" => $this->token, "openid" => $this->openid))->setInc('score', $orderdata['score']);
                    
                    }

                    /*
                    德意币抵扣
                    */

                    if($orderdata['score_money']>0){
                        $data3['add_time']=time();
                        $data3['token']=$this->token;
                        $data3['openid']=$this->openid;
                        $data3['num']=$orderdata['xianjin_b'];
                        $data3['type'] = 2;
                        $data3['statuts']=1;
                        $data3['num']=$orderdata['score_money']*(-1);
                        M('Dyb_score')->add($data3);//这里是.Dyb_score得意币记录表里插一条记录，减少得意币

                        $Oshop_users->where(array("token" => $this->token, "openid" => $this->openid))->setDec('dyb', $orderdata['score_money']);//减消费币
                    }

                    /**
                     * 余额支付，又用了得意币来支付.Dyb_score得意币记录表里插一条记录
                     */

                    if($orderdata['xianjin_b'] > 0){
                        $data4['add_time']=time();
                        $data4['token']=$this->token;
                        $data4['openid']=$this->openid;
                        $data4['num']=$orderdata['xianjin_b'];
                        $data4['type'] = 1;
                        $data4['statuts']=1;
                        M('Dyb_score')->add($data4);//这里是.Dyb_score得意币记录表里插一条记录，增加得意币
                        $Oshop_users->where(array("token" => $this->token, "openid" => $this->openid))->setInc('dyb', $orderdata['xianjin_b']);//加消费币
                    }
                    

                    if($a){
                        /*打印订单*/
                        $orderdata = M("Mainorder")->where(array("token" => $this->token, "openid" => $this->openid, "id" => $oid))->find();
                        $tempShop = explode('|', $orderdata['shopid']);
                        $shopData = M('Shop')->where(array('token' => $this->token, 'id' => $tempShop[1]))->find();
                        if($shopData['print_key'] && $shopData['print_domain']){
                            Vendor('Wuxian.WuxianPrint');
                            $wuxianprint = new WuxianPrint($shopData['print_key'],$shopData['print_domain']);
                            $return = $wuxianprint->print_shop_order($orderdata,$strDetail,$this->tpl['name']);
                            if($return['success'] == true){
                                M("Mainorder")->where(array("token" => $this->token, "openid" => $this->openid, "id" => $oid))->save(array('is_print'=>1));
                            }
                        }



                        $this->ajaxReturn(array("status"=>1,"info"=>"下单成功,请等待3秒钟系统正在跳转!"));
                    }else{
                        $this->ajaxReturn(array("status"=>0,"info"=>"下单失败!"));
                    }
                }
                $this->ajaxReturn(array("status"=>1,"info"=>"下单成功,请等待3秒钟系统正在跳转!"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"下单失败!"));
            }
        }else{

        }
    }
    /**
     * 余额支付
     */
   /* public function yezf(){

            $oid=$this->_get('oid',"intval");

        if($a) {
            $this->('支付成功',U('MemberCenterdyb/index',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            $this->error();
        }


    }*/

    //填写地址页面
    public function address(){
        if(IS_POST){
            $_POST['openid']=$this->openid;
            $_POST['token']=$this->token;
            if(M("Msinfo")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));
            }
        }else{
            $oid=$this->_get("oid","intval");
            $this->assign("oid",$oid);
            $this->display();
        }
    }

    //订单地址删除
    public function DeleteAddress(){
        if(IS_AJAX && IS_POST){
            if(M("Msinfo")->where(array("id"=>$_POST['aid']))->delete()){
                $this->ajaxReturn(array("status"=>1));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"删除失败"));
            }
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
        }
    }

    //定位
    public function location(){
        $this->display();
    }

    //查找社区
    public function findUnion(){
        if(IS_POST){
            $condition['token']=$this->token;
            $condition['cname']=array('like','%'.trim($this->_post("cname").'%'));
            $ip=get_client_ip();
            $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
            $location=json_decode(file_get_contents($url),true);
            $long1=floatval($location['content']['point']['x']);//起点x坐标
            $lat1=floatval($location['content']['point']['y']);//起点y坐标
            $unionList=M("Shopunion")->field('id,cname,long,lat,des,pic')->where($condition)->select();
            $unionData=$this->getinfo(1000000000000000,$unionList,$lat1,$long1);
            $this->assign('union',$unionData);
        }
        $this->display();
    }

    //获取对应区域下的社区
    public function getUnion(){
        $seng=trim($this->_get("seng"));
        $si=trim($this->_get("si"));
        $xian=trim($this->_get("xian"));
        $condition['token']=$this->token;
        $condition['seng']=$seng;
        $condition['si']  =$si;
        $condition['xian'] =$xian;
        $data=M("Shopunion")->field("id,cname,long,lat")->where($condition)->select();
        $this->ajaxReturn(array("status"=>1,"data"=>$data));
    }
    //店铺收藏
    public function collect(){
        if(isset($_GET['wareid'])){
            $sid=$this->_get("wareid","intval");
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"非法操作!"));
        }
        if(!M("Shop")->where(array("id"=>$sid,"token"=>$this->token))->find()){
            $this->ajaxReturn(array("status"=>0,"info"=>"操作失败!"));
        }
        if(M("Collect")->where(array("openid"=>$this->openid,'token'=>$this->token,'sid'=>$sid))->find()){
            $this->ajaxReturn(array("status"=>0,"info"=>"您已经收藏过该店铺了!"));
        }
        $join['openid']=$this->openid;
        $join['token']=$this->token;
        $join['sid']=$sid;
        $join['jtime']=date("Y-m-d H:i:s");
        if(M("Collect")->add($join)){
            $this->ajaxReturn(array("status"=>1,"info"=>"收藏成功!"));
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"收藏失败!"));
        }
    }

    //下单页面,分为主订单和副订单
    public function BuyOrder(){
        $orderinfo=htmlspecialchars_decode($_POST['orderinfo']);

        $orderinfo=json_decode($orderinfo,true);

        $data=$this->SetOrder($orderinfo);
        //p($data);die;
        //echo json_encode($data);

        if($oid=$this->OrderInsert($data)){
            $this->ajaxReturn(array("status"=>1,"info"=>"下单成功","oid"=>$oid));
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"下单失败"));
        }
    }

    //下单数据处理
    public function SetOrder($orders){
        $ordernew=array();
        $keys=array();
        foreach($orders['goods_list'] as $k=>$v){
            $keys[]=$v['shopId'];//商店ID
            $v['gid']=$k;//商品ID
            $ordernew[]=$v;
        }
        $keys=array_unique($keys);
        $orderinfo=array();
        foreach($keys as $k=>$v){
            foreach($ordernew as $m=>$n){
                if($n['shopId']==$v){
                    $orderinfo[$v][]=$n;
                }
            }
        }
        $shopmembers = array();
        foreach($keys as $vs){
            $temp = array();
            $temp =  M('Shop')->field('mid')->where(array('id'=>$vs))->find();
            $shopmembers[] = $temp['mid'];
        }
        $shopmembers=array_unique($shopmembers);
        $orderinfo['totalprice']=$orders['TotalPrice'];
        $orderinfo['totalnum']=$orders['TotalCount'];
        $orderinfo['shopid'] = '|'.implode('|',$keys).'|';
        $orderinfo['shopmemberid'] = '|'.implode('|',$shopmembers).'|';
        return $orderinfo;
    }

    //订单数据插入
    public function OrderInsert($data){
        $time=date("Y-m-d H:i:s");
        $main['buytime']=$time;
        $main['ordernumber']=$this->getSn();
        $main['totalmoney']=$data['totalprice'];
        $main['totalnum']=$data['totalnum'];
        $main['token']=$this->token;
        $main['shopid']=$data['shopid'];
        $main['shopmemberid']=$data['shopmemberid'];
        $main['openid']=$this->openid;
        unset($data['totalprice']);
        unset($data['totalnum']);
        $orders=$data;
        //以上为主订单信息
        mysql_query("start transaction");
        mysql_query("begin");
        if($oid=M("Mainorder")->add($main)){
            //插入分订单数据

            $flag=true;
            foreach ($orders as $k => $v) {
                $side=array();
                $side['token']=$this->token;
                $side['openid']=$this->openid;
                $side['mid']=$oid;
                $side['ordernumber']=$main['ordernumber'];
                $side['sid']=$k;
                $side['buytime']=$time;
                if ($sid=M("Sideorder")->add($side)) {
                    $flag = true;
                } else {
                    $flag = false;
                    break;
                }
                foreach ($v as $n) {
                    if($n['count'] > 0) {
                        $detail['gid'] = $n['gid'];
                        $detail['sid'] = $sid;
                        $detail['num'] = $n['count'];
                        $detail['price'] = $n['price'];
                        $detail['total'] = $n['count'] * $n['price'];
                        $detail['gname'] = $n['goodsname'];
                        $detail['pic'] = $n['pic'];
                        if (M("Sidedetail")->add($detail)) {
                            //减少库存
                            //M('Shopware')->where(array('token' => $this->token, 'id' => $n['gid']))->setDec('stock', $n['count']);
                            $flag = true;
                        } else {
                            $flag = false;
                            break;
                        }
                    }else{
                        continue;
                    }
                }
            }
            if($flag){
                mysql_query("commit");
            }else{
                mysql_query("rollback");
                return false;
            }
            return $oid;
        }else{
            return false;
        }
    }

    //百度地图查询最近范围内的数据,$r为查询范围半径，data为数据,lat1为起点经度 long1为起点纬度
    public function getinfo($r=0,$data=array(),$lat1,$long1){
        $R = 6370996.81;
        foreach($data as $k=>$v){
            $lat2=floatval($v['lat']);
            $long2=floatval($v['long']);
            $distance= $R*acos(cos($lat1*pi()/180 )*cos($lat2*pi()/180)*cos($long1*pi()/180 -$long2*pi()/180)+ sin($lat1*pi()/180 )*sin($lat2*pi()/180));
            $data[$k]['distance']=$distance;
            $distances[$v['id']]=$distance;
        }
        sort($distances);
        $data=$this->paixu($r,$data,$distances);
        return $data;
    }

    //二维数组排序
    public function paixu($r,$arr,$distance){
        foreach($distance as $key=>$var){
            foreach($arr as $k=>$v){
                if(intval($v['distance'])<$r){
                    if($v['distance']==$var){
                        $new[$key]=$v;
                        if($v['distance']>=1000){
                            $new[$key]['distance']=(round($v['distance']/1000))."公里";
                        }else{
                            $new[$key]['distance']=round($v['distance'])."米";
                        }
                    }
                }
            }
        }
        return $new;
    }

    // 获取唯一订单号
    public function getSn(){

        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    //发送短信接口
    public function sendMessage($tel,$content){
        $sms_config_model=M('config_sms');
        $check=$sms_config_model->where(array('token'=>$this->token))->find();
        $contentdata="尊敬的客户{$tel},很荣幸的告诉你{$content}";
        if($check){
            $url = 'http://yunpian.com/v1/sms/send.json';
            $apidata['text'] = urlencode("$contentdata");
            $apidata ="apikey=".$check['apikey']."&text=".$apidata['text']."&mobile=".$tel;
            $returndata = $this->api_notice_increment($url,$apidata);
            $returndata = json_decode($returndata,true);
        }
    }

    //客户取消订单
    public function CancelOrder(){
        if(IS_AJAX){
            $id=$this->_get("id","intval");
            $sideorder=M("Sideorder")->field("id")->where(array("token"=>$this->token,"openid"=>$this->openid,"mid"=>$id))->select();
            if(!$sideorder){
                $this->ajaxReturn(array("status"=>0,"info"=>"该订单不存在!"));
            }
            mysql_query("start transaction");
            mysql_query("begin");
            $flag=true;
            foreach($sideorder as $v){
                $sidedetail=M("Sidedetail")->where(array("sid"=>$v['id']))->select();


                foreach($sidedetail as $n){
                    //增加库存
                    if(M('Shopware')->where(array('token'=>$this->token,'id'=>$n['gid']))->setInc('stock',$n['num'])){
                        $flag=true;
                    }else{
                        $flag=false;
                        break;
                    }
                }

                if($flag){
                    if(M("Sideorder")->where(array("id"=>$v['id']))->save(array('paystatus'=>-1))){
                        $flag=true;
                    }else{
                        $flag=false;
                    }
                }
            }
            if($flag){
                if(M("Mainorder")->where(array("token"=>$this->token,"openid"=>$this->openid,"id"=>$id))->save(array('paystatus'=>-1))){
                    $flat=true;
                }else{
                    $fla=false;
                }
            }


            if($flag){
                /*
                 * 通知微信
                 */
                $orderdata = M("Mainorder")->where(array("token"=>$this->token,"openid"=>$this->openid,"id"=>$id))->find();

                /*
                 * 撤回积分
                 */
                M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setDec('score',$orderdata['score']);

                $notichcontent = $this->wxusers['nickname']."您好,取消订单提醒\n订单编号：".$orderdata['ordernumber']."\n取消订单时间:".date("Y-m-d H:i:s");
                $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
                $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                $data = $this->api_notice_increment($url,http_build_query($postdata));
                if(!$data){
                    $this->api_notice_increment($url,http_build_query($postdata));
                }
                mysql_query("commit");


                $this->ajaxReturn(array("status"=>1,"info"=>"取消订单成功!"));
            }else{
                mysql_query("rollback");
                $this->ajaxReturn(array("status"=>0,"info"=>"取消订单失败!"));
            }
        }else{
            exit("非法操作!");
        }
    }

    //客户确认收货
    public function accept(){
        if(IS_AJAX){
            $id=$this->_get("id","intval");
            $data=M("Mainorder")->field("id")->where(array("token"=>$this->token,"openid"=>$this->openid,"id"=>$id))->find();
            $flag=true;
            if($data){
                $sideorder=M("Sideorder")->field("id")->where(array("mid"=>$id))->select();
                foreach($sideorder as $v){
                    if(M("Sideorder")->where(array("id"=>$v['id']))->save(array("sendstatus"=>2))){
                        $flag=true;
                    }else{
                        $flag=false;
                    }
                }
                if($flag){
                    if(M("Mainorder")->where(array("id"=>$id))->save(array("sendstatus"=>2))){
                        mysql_query("commit");
                        $this->ajaxReturn(array("status"=>1,"info"=>"恭喜您收货成功!"));
                    }else{
                        mysql_query("rollback");
                        $this->ajaxReturn(array("status"=>0,"info"=>"操作失败!"));
                    }
                }else{
                    mysql_query("rollback");
                    $this->ajaxReturn(array("status"=>0,"info"=>"操作失败!"));
                }
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"订单不存在!"));
            }
        }else{
            exit("非法操作!");
        }
    }      
    

    //收藏社区
    public function CollectUnion(){
        if(IS_AJAX){
            $Data=M('Shop_collectunion')->where(array('token'=>$this->token,'openid'=>$this->openid,'uid'=>$this->_post('uid','intval')))->find();
            if(empty($Data)){
                $Join['openid']=$this->openid;
                $Join['token']=$this->token;
                $Join['atime']=date('Y-m-d H:i:s');
                $Join['uid']  =$this->_post('uid','intval');
                if(M('Shop_collectunion')->add($Join)){
                    $this->ajaxReturn(array("status"=>1,"info"=>"收藏成功!"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>M('Shop_collectunion')->getError()));
                }
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"您已经收藏该社区了!"));
            }
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"非法操作!"));
        }
    }

    //收藏列表
    public function CollectUnionList(){
        $long1=$this->_get("lng");
        $lat1=$this->_get("lat");
        if(!$long1 && !$lat1){
            $ip=get_client_ip();
            $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
            $location=json_decode(file_get_contents($url),true);
            $long1=floatval($location['content']['point']['x']);//起点x坐标
            $lat1=floatval($location['content']['point']['y']);//起点y坐标
        }
        $data=M("Shop_collectunion")->field("tp_shop_collectunion.id as cid,tp_shopunion.id,tp_shopunion.cname,tp_shopunion.des,tp_shopunion.pic,tp_shopunion.long,tp_shopunion.lat")->join("join tp_shopunion on tp_shopunion.id=tp_shop_collectunion.uid")->where(array("tp_shop_collectunion.token"=>$this->token,'tp_shop_collectunion.openid'=>$this->openid))->select();
        $union=$this->getinfo(50000,$data,$lat1,$long1);
        $this->assign("union",$union);
        $this->display();
    }

    //取消收藏
    public function CancelUnion(){
        if(IS_AJAX){
            if(M("Shop_collectunion")->where(array("id"=>$this->_post("cid","intval")))->delete()){
                $this->ajaxReturn(array("status"=>1,"info"=>"取消成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"操作失败!"));
            }
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"非法操作!"));
        }
    }
    

/**
     * 抽奖活动
     */
    public function activitys(){
        $lotteryModel = M('Lottery');
        $data = $lotteryModel->where(array('token'=>$this->token))->order('statdate desc')->select();
        /*foreach($data as $k=>$v){
            if($v['statdate'] > time()){
                $data[$k]['is_start'] = 1;
            }else if($v['enddate'] < time()){
                $data[$k]['is_start'] = 2;
            }else{
                $data[$k]['is_start'] = 3;
            }
        }*/
        $this->assign('data',$data);

        $this->display();
    }
    /**
     * 积分商城
     */
    public function jifenshop(){
        $token	= $this->_get('token');
        $openid	= $this->_get('openid');
        $model = M('Integralshop');
        $list = $model->where(array('tp_integralshop.token'=>$token))->field('tp_integralshop.*,l.name')->join('left join tp_usercenter_level as l on tp_integralshop.extent = l.id ')->select();
        //print_r($list);exit;
        $this->assign('data',$list);

        $this->display();
    }
    /**
     * 确定兑换
     */
    public function exchange(){

           // echo time();exit;

            $where['openid'] = $_GET['openid'];
            $aUser=M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
            // print_r($list);exit;
            $croe = $_POST['point'];//这个用的积分
            //print_r($croe);exit;
            if($aUser['score'] < $croe){
                $this->error("对不起，积分不够哦！",U(MODULE_NAME.'/reveal',array('token'=>$_GET['token'],'openid'=>$this->_get('openid'))));exit;
            }else{
              $conn = M('Integralshop');
                $where_1['id']=$_POST['exc_id'];
                $a = $conn->where($where)->getField('degree');//在礼品积分表里查找礼品可兑换的次数
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
                }else {
                    //总积分 = 兑换前积分 - 兑换礼品的积分
                    $uid = $_POST['exc_id'];
                    //echo $uid;die;

                    if (M('Integralshop')->where(array('id' => $uid))->setDec('num', '1') && M('Shop_users')->where(array('id' => $aUser['id']))->setDec('score', $croe)) {
                        $gift = M('Integralshop_individual');
                        $data['token']=$this->token;
                        $data['openid']=$this->openid;
                        $data['lid']=$uid;
                        $data['time']=time();
                        $data['store']=$croe;
                        $giftname=M('Integralshop')->where(array('id' => $uid))->getField('giftname');
                        $data['name']=$giftname;
                        $gift->add($data);
                        $this->success("扣除成功！", U(MODULE_NAME . '/reveal', array('token' => $_GET['token'], 'openid' => $this->_get('openid'))));
                    }
                }
            }
        }

    /**
     * 我的礼品
     */
    public function reveal(){
        $model =  M('Integralshop_individual');
        $list=$model->where(array('token'=>$_GET['token'],'openid'=>$this->_get('openid')))->select();
        $this->assign('list',$list);
        $this->display();

    }
    /**
     * 微官网首页
     */

    public function indexdyb(){
        $where['token']=$this->_get('token');
        $where['ifscroll'] = 1;
        $this->pre_url = substr($_SERVER["HTTP_REFERER"],20);
        $info=M('Classify')->where(array('token'=>$this->_get('token'),'pid'=>0,'status'=>1))->order('sorts desc')->select();
        $flash=M('Flash')->where($where)->select();

        $speeddial=M('speeddial')->where(array('token'=>$this->token))->find();
        $copy=D('User_group')->field('iscopyright')->find($gid['gid']);	//查询用户所属组
        $copyright=$copy['iscopyright'];
        $count=count($flash);

        $this->assign('flash',$flash);
        $this->assign('info',$info);
        $this->assign('num',$count);
        //$this->assign('tpl',$this->tpl);
        $this->assign('speeddial',$speeddial);
        $this->assign('copyright',$copyright);

        $this->display();
        
    }
    /**
     * 余额冲值
     */
    public function ye_cz(){
        $usercenter_moneyModel = M('Ye_record');
        $moneyrecordlist = $usercenter_moneyModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->select();
        $this->assign('moneyrecordlist',$moneyrecordlist);
        $this->display();

    }
/**
 * O2O冲值金钱
 */
    public function genMoney(){
        $usercenter_money_recordModel = M('Ye_record');
        if(!empty($_POST['money'])){
            $data['token'] = $this->token;
            $data['openid'] = $this->openid;
            $data['pay_type'] = 1;
            $data['status'] = 1;
            $data['add_time'] = time();
            $data['money'] = intval($_POST['money']);
            if($lastid = $usercenter_money_recordModel->add($data)){
                echo $this->encode(array('code'=>0,'msg'=>'充值订单生成成功,正在跳转..','order_id'=>$lastid));
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'订单生成失败,请重试'));
            }
        }
    }
    /**
     * 德艺币记录
     */
    public function dyb_jl(){
        $usercenter_moneyModel = M('Dyb_score');
        $moneyrecordlist = $usercenter_moneyModel->where(array('token'=>$this->token,'openid'=>$this->openid,'statuts'=>1))->order('add_time desc')->select();
        $this->assign('moneyrecordlist',$moneyrecordlist);

        $this->display();
    }
    /**
     * 创意积分记录
     */
    public function dyb_jf(){
        $usercenter_moneyModel = M('Dy_score');
        $sCount=M('Dy_score')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('gt',0)))->getField('sum(score) as sum');//得总积分
        $xiaofeijifen=M('Dy_score')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('lt',0)))->getField('sum(score) as sum');//得总积分
        $user = M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $moneyrecordlist = $usercenter_moneyModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('addtime desc')->select();
        $this->assign('moneyrecordlist',$moneyrecordlist);
        $this->assign('sCount',$sCount);
        $this->assign('xiaofeijifen',$xiaofeijifen);
        $this->assign('user',$user);
        $this->display();
    }



    /***************************************************************************************/
    // 社区主页
    public function zoneIndex(){
        $memberdata = M('Shop_users')->where(array('token' => $this->token, 'openid' => $this->openid))->find();
        /*if(!$memberdata['truename']){
            $this->redirect(U('Wap/Commercedyb/user_edit',array('openid'=>$this->openid,'token'=>$this->token)));
        }*/
        if(!$memberdata['truename']){
            $showtips = 1;
        }else{
            $showtips = 0;
        }
        // 微信关注数据表
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
        $userLvlModel = M('dy_userlvl');
        $setScoreModel = M('dy_setscore');
        $setScoreResult = $setScoreModel->where(array('token'=>$this->token))->setInc('visiter',1);
        $getUid = $wxModel->field('id')->where(array('token'=>$this->token))->find();
        $statusInfo = $wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->getField('status');
        //这里打开
       /* if(!$wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() || $statusInfo == 0){
            
            exit($this->display('./tpl/Wap/default/Commercedyb_noticedyb.html'));
        }*/
        //echo 3;die;

        // 如果用户进入到界面的话增加创意积分20分
        $shouci = $setScoreModel->field('shouci')->where(array('token'=>$this->token))->find();
        if(!$userLvlModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find()){
            if($wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() && $statusInfo != 0){
                $userLvlResult = $userLvlModel->data(array(
                    'openid' => $this->openid,
                    'token' => $this->token,
                    'lvl' => 1,
                    'noticetime' => date('Y-m-d H:i:s'),
                    'score' => $shouci['shouci']
                ))->add();
            }elseif($wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() && $statusInfo == 0){
                $deleteLvL = $userLvlModel->where(array(
                        'openid' => $this->openid,
                        'token' => $this->token
                    ))->delete();
            }
        }
        // 首先获取话题的数量以及访问量
        /*$dyZoneModel = M('Dy_zone');
        if ($dyZoneModel->where(array('token'=>$this->token))->find()) {
           // 如果查询到数据不做任何事情
        }else{
            $innsertInfo = $dyZoneModel->data(array('visiter'=>0,'token'=>$this->token,'content'=>'今日公告！'))->add();
        }

        zoneResult = $dyZoneModel->where(array('token'=>$this->token))->find();*/

        $noticeModel = M('Dy_notice');
        $finds = $noticeModel->where(array('token'=>$this->token))->order('time desc')->limit(3)->select();
        $this->assign('lists',$finds);
       // p($finds);die;
        // 用户发表帖子表
        $dyCommunityModel = M('Dy_community');
    
        // 用户点赞以及回复表
        $dyUserModel = M('Dy_users');

        // 根据openid判断当前的用户是否点击了赞
        $commentResult = $dyCommunityModel->order('sort desc,add_time desc')->where(array('token'=>$this->token))->limit('0,10')->select();
        // 首先加入所有的数据进去
        $commentResults = $dyCommunityModel->order('sort desc,add_time desc')->where(array('token'=>$this->token))->select();
        foreach ($commentResults as $key => $value) {
            $zan = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$this->token,'openid'=>$this->openid))->find();
            

            if ($zan) {
                $commentResults[$key]['zan'] = $zan['ifzan'];
            }else{
               
            }
        }
        
        foreach ($commentResult as $key => $value) {
            $result = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$this->token,'comment'=>array('NEQ',''),'replay_id'=>0))->order('comment_time desc')->limit(3)->select();
            // 如果replay_id不等于0
            foreach ($result as $k => $v) {
                if($v['replay_id'] != 0){
                    $findNickName = $dyUserModel->where(array('id'=>$v['replay_id'],'token'=>$this->token))->find();
                    $result[$k]['nickname'] = $v['nickname']."  回复  ".$findNickName['nickname'];
                }
            }
            // 统计评论条数
            $totalpinglun = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$this->token,'comment'=>array('NEQ',''),'replay_id'=>0))->count();
            $commentResult[$key]['totalCount'] = $totalpinglun;
            // 统计一下赞的个数
            $zanCount = $dyUserModel->field('count(openid) as oid')->where(array('uid'=>$value['id'],'ifzan'=>1))->group('openid')->select();
            $zanCount = count($zanCount);
            // 统计评论数量
            $pinglunCount = $dyUserModel->where(array('uid'=>$value['id'],'comment'=>array('NEQ','')))->count();
            $commentResult[$key]['comments'] = $result;
            $commentResult[$key]['dianzan'] = $zanCount;
            $commentResult[$key]['pinglun'] = $pinglunCount;
            $zan = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$this->token,'openid'=>$this->openid))->find();

            if ($zan) {
                $commentResult[$key]['zan'] = $zan['ifzan'];
            }else{
                // 添加一条数据无效
                $microUser['nickname'] = !empty($microUser['nickname']) ? $microUser['nickname']:'游客';
               
                $commentResult[$key]['zan'] = 0;
            }
        
            
        }
       // 统计粉丝数量
        // $uids = $wxModel->where(array('token'=>$this->token))->getField('id');
        $countMicroUser = $setScoreModel->where(array('token'=>$this->token))->getField('visiter');
        $this->assign('countMicroUser',$countMicroUser);
        $countComm = $dyCommunityModel->where(array('token'=>$this->token))->count();
        // 用户点赞以及回复表
       
        $this->assign('zone',$zoneResult);
        $this->assign('countComm',$countComm);
        // 分配评论的数据
        // print_r($commentResult);exit; 
        $this->assign('comment',$commentResult);
        
        //分配openid
        $this->assign('openid',$this->openid);
        $this->assign('showtips',$showtips);
        // 执行等级更改
        $this->changeLvl();
        // 如果是通过表单提交的话
        if($this->token=='55cad4ba46c41a8fde9c84274e36fa83'){//如多分期的
            $this->assign('foot',4);
        }
        $this->display();
    }





    // 话题榜
    public function huati_index(){
        // 微信关注数据表
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
        $userLvlModel = M('dy_userlvl');
        $setScoreModel = M('dy_setscore');
        $setScoreResult = $setScoreModel->where(array('token'=>$this->token))->setInc('visiter',1);
        $getUid = $wxModel->field('id')->where(array('token'=>$this->token))->find();
        $statusInfo = $wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->getField('status');
        /* if(!$wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() || $statusInfo == 0){

             exit($this->display('./tpl/Wap/default/Commercedyb_noticedyb.html'));
         }*/
        //echo 3;die;

        // 如果用户进入到界面的话增加创意积分20分
        $shouci = $setScoreModel->field('shouci')->where(array('token'=>$this->token))->find();
        if(!$userLvlModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find()){
            if($wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() && $statusInfo != 0){
                $userLvlResult = $userLvlModel->data(array(
                    'openid' => $this->openid,
                    'token' => $this->token,
                    'lvl' => 1,
                    'noticetime' => date('Y-m-d H:i:s'),
                    'score' => $shouci['shouci']
                ))->add();
            }elseif($wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() && $statusInfo == 0){
                $deleteLvL = $userLvlModel->where(array(
                    'openid' => $this->openid,
                    'token' => $this->token
                ))->delete();
            }
        }
        // 首先获取话题的数量以及访问量
        /*$dyZoneModel = M('Dy_zone');
        if ($dyZoneModel->where(array('token'=>$this->token))->find()) {
           // 如果查询到数据不做任何事情
        }else{
            $innsertInfo = $dyZoneModel->data(array('visiter'=>0,'token'=>$this->token,'content'=>'今日公告！'))->add();
        }

        zoneResult = $dyZoneModel->where(array('token'=>$this->token))->find();*/

        $noticeModel = M('Dy_notice');
        $finds = $noticeModel->where(array('token'=>$this->token))->order('time desc')->limit(3)->select();
        $this->assign('lists',$finds);
        // p($finds);die;
        // 用户发表帖子表
        $dyCommunityModel = M('Dy_community');

        // 用户点赞以及回复表
        $dyUserModel = M('Dy_users');

        // 根据openid判断当前的用户是否点击了赞
        $commentResult = $dyCommunityModel->order('sort desc,add_time desc')->where(array('token'=>$this->token,'type'=>1))->limit('0,10')->select();
        // 首先加入所有的数据进去
        $commentResults = $dyCommunityModel->order('sort desc,add_time desc')->where(array('token'=>$this->token,'type'=>1))->select();
        foreach ($commentResults as $key => $value) {
            $zan = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$this->token,'openid'=>$this->openid))->find();


            if ($zan) {
                $commentResults[$key]['zan'] = $zan['ifzan'];
            }else{

            }
        }

        foreach ($commentResult as $key => $value) {
            $result = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$this->token,'comment'=>array('NEQ',''),'replay_id'=>0))->order('comment_time desc')->limit(3)->select();
            // 如果replay_id不等于0
            foreach ($result as $k => $v) {
                if($v['replay_id'] != 0){
                    $findNickName = $dyUserModel->where(array('id'=>$v['replay_id'],'token'=>$this->token))->find();
                    $result[$k]['nickname'] = $v['nickname']."  回复  ".$findNickName['nickname'];
                }
            }
            // 统计评论条数
            $totalpinglun = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$this->token,'comment'=>array('NEQ',''),'replay_id'=>0))->count();
            $commentResult[$key]['totalCount'] = $totalpinglun;
            // 统计一下赞的个数
            $zanCount = $dyUserModel->field('count(openid) as oid')->where(array('uid'=>$value['id'],'ifzan'=>1))->group('openid')->select();
            $zanCount = count($zanCount);
            // 统计评论数量
            $pinglunCount = $dyUserModel->where(array('uid'=>$value['id'],'comment'=>array('NEQ','')))->count();
            $commentResult[$key]['comments'] = $result;
            $commentResult[$key]['dianzan'] = $zanCount;
            $commentResult[$key]['pinglun'] = $pinglunCount;
            $zan = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$this->token,'openid'=>$this->openid))->find();

            if ($zan) {
                $commentResult[$key]['zan'] = $zan['ifzan'];
            }else{
                // 添加一条数据无效
                $microUser['nickname'] = !empty($microUser['nickname']) ? $microUser['nickname']:'游客';

                $commentResult[$key]['zan'] = 0;
            }

        }
        //按点赞数排
        if(isset($_GET)&&$_GET['sort']==1){
            foreach($commentResult as $k=>$v){
                $a=$v['dianzan'].'_'.$v['id'];
                $commentResult[$a]=$v;
                unset($commentResult[$k]);
            }
            krsort($commentResult);

        }
        //按评论数排序
        if(isset($_GET)&&$_GET['sort']==2){
            foreach($commentResult as $k=>$v){
                $a=$v['pinglun'].'_'.$v['id'];
                $commentResult[$a]=$v;
                unset($commentResult[$k]);
            }
            krsort($commentResult);

        }


        // 统计粉丝数量
        // $uids = $wxModel->where(array('token'=>$this->token))->getField('id');
        $countMicroUser = $setScoreModel->where(array('token'=>$this->token))->getField('visiter');
        $this->assign('countMicroUser',$countMicroUser);
        $countComm = $dyCommunityModel->where(array('token'=>$this->token))->count();
        // 用户点赞以及回复表

        $this->assign('zone',$zoneResult);
        $this->assign('countComm',$countComm);
        // 分配评论的数据
        // print_r($commentResult);exit;
        $this->assign('comment',$commentResult);

        //分配openid
        $this->assign('openid',$this->openid);

        // 执行等级更改
        $this->changeLvl();
        // 如果是通过表单提交的话
        //如多分期项目加
        if($this->token=='55cad4ba46c41a8fde9c84274e36fa83'){
            $this->assign('foot',4);
        }
        $this->display();
    }
    // 点击加载更多
    public function loadMore(){
        if(IS_AJAX){
            if(IS_POST){

               $leng = $_POST['bbs'];
                //这里区分话题还是首页加载更多
                if(isset($_POST['type'])){
                    $type=1;
                }else{
                    $type=array('in',array(0,1));
                }
               $getThing = M('dy_community')
                   ->where(array('token'=>$this->token,'type'=>$type))
                   ->order('add_time desc')
                   ->limit($leng.",8")
                   ->select();
                if(!$getThing){
                    echo json_encode(array(
                        'status' => 1,
                        'info' => '没有更多数据!'
                    ));
                    exit();
                }


                foreach ($getThing as $key => $value) {
                    $usersResult = M('dy_users')->where(array(
                            'uid'=>$value['id'],
                            'token' => $value['token'],
                            'replay_id' => 0
                        ))
                    ->limit(3)
                    ->select();
                    $getThing[$key]['comments'] = $usersResult;
                    $countArticle = M('dy_users')->where(array(
                        'ifzan'=>1,
                        'uid'=>$value['id']
                    ))
                    ->count();
                    $countComment = M('dy_users')->where(array(
                        'uid' => $value['id'],
                        'comment' => array('NEQ','')
                    ))
                    ->count();
                    $zan = M('dy_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'uid'=>$value['id']))->getField('ifzan');
                    if(!$zan){
                        $zan = 0;
                    }
                    $getThing[$key]['dianzan'] = $countArticle;
                    $getThing[$key]['pinglun'] = $countComment;
                    $getThing[$key]['totalCount'] = $countComment;
                    $getThing[$key]['zan'] = $zan;
                }
                $this->assign('comment',$getThing);
                $fetch = $this->fetch('./tpl/Wap/default/Commercedyb_loadMore.html');
                echo json_encode(array(
                        'fetch' => $fetch,
                        'status' => 0,
                        'info' => '加载完毕!'
                    ));
            }
        }
        
    }


    // 点击加载更多,话题页面
    public function loadMore1(){
        if(IS_AJAX){
            if(IS_POST){
                $leng = $_POST['bbs'];
               // p($_POST);die;
                // 接收文章ID
                $wxModel = M('wxuser');
                $wxsModel = M('wxusers');
                $getUid = $wxModel->field('id')->where(array('token'=>$this->token))->find();
                $statusInfo = $wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->getField('status');
                $articleID = $this->_get('articleId','intval');
                $communityModel = M('dy_community');
                $dyUserModel = M('dy_users');
                $findArticle = $communityModel->where(array(
                    'token' => $this->token,
                    'id' => $articleID
                ))
                    ->find();
                $findComment = $dyUserModel->where(array(
                    'token' => $this->token,
                    'uid' => $articleID,
                    'comment' => array(
                        'NEQ',''
                    )
                ))->limit($leng.",8")->select();
                if(!$findComment){
                    echo json_encode(array(
                        'status' => 1,
                        'info' => '没有更多数据!'
                    ));
                    exit();
                }

                foreach ($findComment as $k => $v) {
                    if($v['replay_id'] != 0){
                        $findNickName = $dyUserModel->where(array('id'=>$v['replay_id'],'token'=>$this->token))->find();
                        $findComment[$k]['nickname'] = $v['nickname']."  回复  ".$findNickName['nickname'];
                        $findComment[$k]['dianzan']=$dyUserModel->where(array('replay_id'=>$v['id'],'ifzan'=>1))->count();
                        $findComment[$k]['pinglun']=$dyUserModel->where(array('replay_id'=>$v['id'],'comment'=>array('NEQ','')))->count();
                    }else{//等于0的代表这条是评论的话主的
                        $findComment[$k]['dianzan']=$dyUserModel->where(array('replay_id'=>$v['id'],'ifzan'=>1))->count();
                        $findComment[$k]['pinglun']=$dyUserModel->where(array('replay_id'=>$v['id'],'comment'=>array('NEQ','')))->count();
                        /**
                         * 判断此条是谁被我点赞过
                         */
                        if($dyUserModel->where(array('replay_id'=>$v['id'],'ifzan'=>1,'openid'=>$this->openid))->find()){
                            $findComment[$k]['isme_zan']=1;
                        }else{
                            $findComment[$k]['isme_zan']=0;
                        }
                        // $findComment[$k]['isme_zan']=$dyUserModel->where()
                    }
                }

                $totalpinglun = $dyUserModel->where(array(
                    'uid'=>$articleID,
                    'token'=>$this->token,
                    'comment'=>array('NEQ','')
                ))->count();
                $zanCount = $dyUserModel->field('count(openid) as oid')->where(array(
                    'uid'=>$articleID,
                    'ifzan'=>1
                ))->group('openid')->select();
                $zanCount = count($zanCount);
                // 收集到是否点赞
                $ifzan = $dyUserModel->field('ifzan')->where(array(
                    // 'from_openid' => $findArticle['openid'],
                    'uid' => $findArticle['id'],
                    'token' => $this->token,
                    'openid' => $this->openid
                ))->find();
                $findArticle['comments'] = $findComment;
                $findArticle['ifzan'] = $ifzan['ifzan'];
                $findArticle['dianzan'] = $zanCount;
                $findArticle['pinglun'] = $totalpinglun;
                //echo $this->openid;
                //  p($findArticle);
                // die;
                $this->assign('article',$findArticle);
                /*$getThing = M('dy_community')
                    ->where(array('token'=>$this->token))
                    ->order('add_time desc')
                    ->limit($leng.",8")
                    ->select();
                if(!$getThing){
                    echo json_encode(array(
                        'status' => 1,
                        'info' => '没有更多数据!'
                    ));
                    exit();
                }
                foreach ($getThing as $key => $value) {
                    $usersResult = M('dy_users')->where(array(
                        'uid'=>$value['id'],
                        'token' => $value['token'],
                        'replay_id' => 0
                    ))
                        ->limit(3)
                        ->select();
                    $getThing[$key]['comments'] = $usersResult;
                    $countArticle = M('dy_users')->where(array(
                        'ifzan'=>1,
                        'uid'=>$value['id']
                    ))
                        ->count();
                    $countComment = M('dy_users')->where(array(
                        'uid' => $value['id'],
                        'comment' => array('NEQ','')
                    ))
                        ->count();
                    $zan = M('dy_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'uid'=>$value['id']))->getField('ifzan');
                    if(!$zan){
                        $zan = 0;
                    }
                    $getThing[$key]['dianzan'] = $countArticle;
                    $getThing[$key]['pinglun'] = $countComment;
                    $getThing[$key]['totalCount'] = $countComment;
                    $getThing[$key]['zan'] = $zan;
                }*/

                $fetch = $this->fetch('./tpl/Wap/default/Commercedyb_loadMore1.html');
                echo json_encode(array(
                    'fetch' => $fetch,
                    'status' => 0,
                    'info' => '加载完毕!'
                ));
            }
        }

    }



    // 根据指定字符截取字符串的方法
    protected function get_str($str,$start_str,$end_str){        
        $this->str = $str;        
        $this->start_str = $start_str;        
        $this->end_str = $end_str;     
        $this->start_pos = strpos($this->str,$this->start_str)+strlen($this->start_str);
        $this->end_pos = strpos($this->str,$this->end_str);        
        $this->c_str_l = $this->end_pos - $this->start_pos;        
        $this->contents = substr($this->str,$this->start_pos,$this->c_str_l);        
        return $this->contents;    
    }   

    // 点赞的不同
    public function zan(){
        if(IS_AJAX){
            $usersModel = M('Dy_users');
            if (IS_POST) {
               // p($_POST);die;
                $token = $this->_get('token');
                $openid = $this->_post('openid');
                $like = explode('-',$this->_post('like'));
                $zan = $like[0];
                $id = $like[1];
                $wxuserModel = M('wxuser');
                $wxusersModel = M('wxusers');
                $wxuid = $wxuserModel->field('uid')->where(array('token'=>$token))->find();
                $wxuserInfo = $wxusersModel->field('headimgurl,nickname')->where(array(
                        'uid' => $wxuid,
                        'openid' => $openid
                    ))->find();
                if ($usersModel->where(array('token'=>$token,'openid'=>$openid,'uid'=>$id))->count()) {
                    
                    if($zan == 1){
                        $result = $usersModel->where(array('uid'=>$id,'token'=>$token,'openid'=>$openid))->save(array('ifzan'=>0));
                    }else{
                        $result = $usersModel->where(array('uid'=>$id,'token'=>$token,'openid'=>$openid))->save(array('ifzan'=>1));
                    }
                    if ($result) {
                        echo json_encode(array('info'=>'操作成功！','status'=>0),true);
                    }else{
                        echo json_encode(array('info'=>'操作失败！','status'=>1),true);
                    }
                }else{
                    
                    $insertData = array(
                            'uid' => $id,
                            'token' => $token,
                            'openid' => $openid,
                            'ifzan' => 1,
                            'comment' => '',
                            'comment_time' => date("Y-m-d H:i:s"),
                            'replay_id' => 0,
                            'headimg' => $wxuserInfo['headimg'],
                            'nickname' => $wxuserInfo['nickname']
                        );
                    if($usersModel->data($insertData)->add()){
                       echo json_encode(array(
                           'info'=>'操作成功！',
                           'status'=>0
                           ),true);
                    }else{
                        echo json_encode(array(
                            'info'=>'操作失败！',
                            'status'=>1
                            ),true);
                    }
                }
                
            }
        }
    }
    // 点赞的不同.这里评论话题主的
    public function zan1(){
        if(IS_AJAX){
            $usersModel = M('Dy_users');
            if (IS_POST) {//like-0代表的是还没有点赞
              //   p($_POST);die;
                $token = $this->_get('token');
                $openid = $this->_post('openid');
                $like = explode('-',$this->_post('like'));
                $zan = $like[0];
               // $id = $like[1];
                $replay_id=$this->_post('replay_id');
                if($zan){//这里代表取消点赞
                    if($id= $usersModel->where(array('uid'=>$like[1],'token'=>$token,'openid'=>$openid,'ifzan'=>1,'replay_id' =>$replay_id))->getField('id')){
                        $result = $usersModel->where(array('id'=>$id))->save(array('ifzan'=>0));
                        if ($result) {
                            echo json_encode(array('info'=>'操作成功！','status'=>0),true);
                        }else{
                            echo json_encode(array('info'=>'操作失败！','status'=>1),true);
                        }
                    }


                }else{//这里代表要新增点赞
                    $insertData = array(
                        'uid' => $like[1],
                        'token' => $token,
                        'openid' => $openid,
                        'ifzan' => 1,
                        'comment' => '',
                        'comment_time' => date("Y-m-d H:i:s"),
                        'replay_id' =>$replay_id
                      //  'headimg' => $wxuserInfo['headimg'],
                       // 'nickname' => $wxuserInfo['nickname']
                    );
                    if($usersModel->data($insertData)->add()){
                        echo json_encode(array(
                            'info'=>'操作成功！',
                            'status'=>0
                        ),true);
                    }else{
                        echo json_encode(array(
                            'info'=>'操作失败！',
                            'status'=>1
                        ),true);
                    }
                }
                /*
                $wxuserModel = M('wxuser');
                $wxusersModel = M('wxusers');
                $wxuid = $wxuserModel->field('uid')->where(array('token'=>$token))->find();
                $wxuserInfo = $wxusersModel->field('headimgurl,nickname')->where(array(
                    'uid' => $wxuid,
                    'openid' => $openid
                ))->find();
                if ($usersModel->where(array('token'=>$token,'openid'=>$openid,'uid'=>$id))->count()) {

                    if($zan == 1){
                        $result = $usersModel->where(array('uid'=>$id,'token'=>$token,'openid'=>$openid))->save(array('ifzan'=>0));
                    }else{
                        $result = $usersModel->where(array('uid'=>$id,'token'=>$token,'openid'=>$openid))->save(array('ifzan'=>1));
                    }
                    if ($result) {
                        echo json_encode(array('info'=>'操作成功！','status'=>0),true);
                    }else{
                        echo json_encode(array('info'=>'操作失败！','status'=>1),true);
                    }
                }else{

                    $insertData = array(
                        'uid' => $id,
                        'token' => $token,
                        'openid' => $openid,
                        'ifzan' => 1,
                        'comment' => '',
                        'comment_time' => date("Y-m-d H:i:s"),
                        'replay_id' => 0,
                        'headimg' => $wxuserInfo['headimg'],
                        'nickname' => $wxuserInfo['nickname']
                    );
                    if($usersModel->data($insertData)->add()){
                        echo json_encode(array(
                            'info'=>'操作成功！',
                            'status'=>0
                        ),true);
                    }else{
                        echo json_encode(array(
                            'info'=>'操作失败！',
                            'status'=>1
                        ),true);
                    }
                }*/

            }
        }
    }
    // 评论数据接收,这里是直接评论的话题
    public function comments(){
        if(IS_AJAX){
            if (IS_POST) {
                $setScoreModel = M('dy_setscore');
                $token = $this->_get('token');
                $openid = $this->openid;
                //$token = $this->token;    //
                $id = $this->_post('id','intval');
                // 评论的openid能够获取得到
                $usersModel = M('Dy_users');
                $wxModel = M('wxuser');
                $wxsModel = M('wxusers');
                $uids = $wxModel->where(array('token'=>$token))->getField('id');
                $microUser = $wxsModel->field('nickname,headimgurl')->where(array('uid'=>$uids,'openid'=>$openid))->find();
                $microUser['nickname'] = !empty($microUser['nickname']) ? $microUser['nickname']:'游客';

                
                $pinglun = $this->_post('content');
                if(strpos($pinglun,']')){
                    // $cutts = $this->get_str($pinglun,'[',']');
                    $pinglun = str_replace(array("[","]"), array("<img src='./Common/qqMagic/",".gif' />"), $pinglun);
                }


                $data = array(
                    'img'=>$_POST['img'],//图片
                        'token' => $token,
                        'openid' => $openid,
                        'uid' => $id,
                        'comment_time' => date('Y-m-d H:i:s'),
                        'ifzan' => 0,
                        'replay_id' => 0,
                        'nickname' => $microUser['nickname'],
                        'headimg' => $microUser['headimgurl'],
                        'comment' => htmlspecialchars_decode($pinglun)
                    );
                $info = $usersModel->data($data)->add();
                
                if ($info) {
                    $pinglun = $setScoreModel->field('pinglun,limits,huifu,fabiao')->where(array('token'=>$this->token))->find();
                    $ping = M('dy_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'comment_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59"))),'replay_id'=>0))->count();
                    $hui = M('dy_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'comment_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59"))),'replay_id'=>array('NEQ',0)))->count();
                    
                    $fatieCount = M('dy_community')->where(array('token'=>$this->token,'openid'=>$this->openid,'add_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59")))))->count();
                    $ping = $ping * $pinglun['pinglun'];
                    $hui = $hui * $pinglun['huifu'];
                    $fa = $fatieCount * $pinglun['fabiao'];

                    $allScore = $ping + $hui + $fa;
                    if(1){
                        echo json_encode(array('info'=>'评论成功！获得'.$pinglun['pinglun'].'个积分','status'=>0,'url'=>'index.php?g=Wap&m=Commercedyb&a=zoneIndex&token='.$token.'&openid='.$openid),true);
                    }else{
                        echo json_encode(array('info'=>'评论成功！今日积分已上限','status'=>0,'url'=>'index.php?g=Wap&m=Commercedyb&a=zoneIndex&token='.$token.'&openid='.$openid),true);exit;
                    }
                    $pinglun = M('dy_setscore')->where(array('token'=>$this->token))->getField('pinglun');
                    if(!$pinglun){
                        $pinglun=0;
                    }
                    M('shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->setInc('score',$pinglun);
                    $this->addScore($token,$openid,2,$pinglun);
                }else{
                    echo json_encode(array('info'=>'评论失败！','status'=>1),true);
                }
            }
            
        }
    }
    // 上传图片处
    //上传图片uploads类
    public function uploadsT(){
        import('ORG.Net.UploadFile');//导入上传类
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg' ,'png' ,'gif');// 设置附件上传类型
        $upload->savePath =  './upload/dyb/';// 设置附件上传目录
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }
        if($upload->upload()){
            // echo "<script language='JavaScrip'>alert('上传成功！);</script>";
            $info =  $upload->getUploadFileInfo();
            $imgpath=$info[0]['savepath'].$info[0]['savename'];
            $arr = array(
                    'name'=>$info[0]['savename'],
                    'pic'=>$imgpath,
                    'size'=>$size
            );
            echo json_encode($arr);
        }else{
            $error = $this->error($upload->getErrorMsg());
        }
    }
    // 发帖
    public function sendLetter(){
        if (IS_AJAX) {
            if (IS_POST) {
                $wxModel = M('wxuser');
                $wxsModel = M('wxusers');
                $setScoreModel = M('dy_setscore');
                $token = $this->_get('token');
                $openid = $this->_post('openid');
                $dyCommunityModel = M('Dy_community');
                $uids = $wxModel->where(array('token'=>$this->token))->getField('id');

                $microUser = $wxsModel->field('nickname,headimgurl')->where(array('uid'=>$uids,'openid'=>$openid))->find(); 
                $img = $this->_post('img');
                // 接收评论数据
                $pinglun = $this->_post('showimg');
                if(strpos($pinglun,']')){
                    // $cutts = $this->get_str($pinglun,'[',']');
                    $pinglun = str_replace(array("[","]"), array("<img src='./Common/qqMagic/",".gif' />"), $pinglun);
                }
                // echo $pinglun;exit;
                $showimg = htmlspecialchars_decode($pinglun);
                $dengji = M('dy_userlvl')->where(array(
                        'token' => $this->token,
                        'openid' => $this->openid
                    ))->getField('lvl');
                /**
                 * 话题贴的话加标题，type=1
                 */
                $title=$this->_post('title');
                if($title){
                    $type=1;
                }else{
                    $type=0;
                }
                $data = array(
                        'type'=>$type,
                        'title'=>$title,
                        'nickname' => $microUser['nickname'],
                        'headimg' => $microUser['headimgurl'],
                        'lvl' => $dengji,
                        'content' => $showimg,
                        'img_url' => $img,
                        'token' => $token,
                        'openid' => $openid,
                        'add_time' => date("Y-m-d H:i:s"),
                        'address' => htmlspecialchars_decode($this->_post('position'))
                    );

                if ($info = $dyCommunityModel->data($data)->add()) {
                    $fabiao = $setScoreModel->field('pinglun,limits,huifu,fabiao')->where(array('token'=>$this->token))->find();

                    $ping = M('dy_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'comment_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59"))),'replay_id'=>0))->count();

                    $hui = M('dy_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'comment_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59"))),'replay_id'=>array('NEQ',0)))->count();

                    $fatieCount = M('dy_community')->where(array('token'=>$this->token,'openid'=>$this->openid,'add_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59")))))->count();

                    $ping = $ping * $fabiao['pinglun'];
                   $hui = $hui * $fabiao['huifu'];
                    
                    $fa = $fatieCount * $fabiao['fabiao'];
                    $allScore = $ping + $hui + $fa;

                    if($allScore <= $fabiao['limits']){
                        echo json_encode(array(
                            'info'=>'发帖成功！获得'.$fabiao['fabiao'].'个积分',
                            'status'=>0,
                            'url'=> U('Wap/Commercedyb/zoneIndex',array('token'=>$token,'openid'=>$openid))
                            ),true);
                        $fatie = M('dy_setscore')->where(array('token'=>$this->token))->getField('fabiao');
                        if(!$fatie){
                            $fatie=0;
                        }
                        $this->addScore($token,$openid,1,$fatie);
                        M('shop_users')->where(array('token'=>$token,'openid'=>$openid))->setInc('score',$fatie);
                    }else{
                        echo json_encode(array(
                            'info'=>'发帖成功！今日积分已上限',
                            'status'=>0,
                            'url'=> U('Wap/Commercedyb/zoneIndex',array('token'=>$token,'openid'=>$openid))
                            ),true);
                    }
                    
                       
                }else{
                    echo json_encode(array(
                        'info'=>'发帖失败！',
                        'status'=>1
                        ),true);
                }

            }
        }
    }

    // 添加积分
    protected function addScore($token,$openid,$type = 1,$score = 1){
         // 添加积分
        $scoreModel = M('dy_score');
        $shopUserModel = M('shop_users');
        $countScore = $scoreModel->where(array('token'=>$token,'openid'=>$openid,'addtime'=>date("Y-m-d")))->count();
        if($countScore < 10){
            $scoreData = array(
                    'score' => $score,
                    'type' => $type,
                    'token' => $token,
                    'openid' => $openid,
                    'addtime' => date("Y-m-d H:i:s")
                );
            $scoreModel->data($scoreData)->add();
        }
    }
    // 我的社区
    public function myZone(){
        $openid = $this->_get('openid');
        $token = $this->_get('token');
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
        $dyCommunityModel = M('Dy_community');
        $uids = $wxModel->where(array('token'=>$token))->getField('id');
        
        $microUser = $wxsModel->field('nickname,headimgurl')->where(array('uid'=>$uids,'openid'=>$openid))->find();
        $microUser['nickname'] = isset($microUser['nickname']) ? $microUser['nickname']:'游客';
        $this->assign('openid',$openid);
        $this->assign('nickname',$microUser['nickname']);
        $this->assign('headimg',$microUser['headimgurl']);

        $dyUsers = M('dy_users');
        $dyCommunityModel = M('Dy_community');

        $myreplay = $dyUsers->where(array('token'=>$this->token,'openid'=>$this->openid))->order('comment_time desc')->select();
        $replayMy = array();
        foreach ($myreplay as $key => $value) {
            $res = $dyUsers->where(array('token'=>$this->token,'replay_id'=>$value['id'],'is_read'=>0))->select();
            foreach ($res as $ks => $vs) {
                # code...
                $replayMy[] = $vs;
            }
        }

        $mainCommunity = $dyCommunityModel->join("tp_dy_users as du on du.uid= tp_dy_community.id")->where(array('tp_dy_community.token'=>$this->token,'tp_dy_community.openid'=>$this->openid,'du.replay_id'=>0,'du.is_read'=>0))->select();
        foreach ($mainCommunity as $k => $v) {
            array_push($replayMy, $v);
        }
        $this->assign('countComm',count($replayMy));    


        $this->display();
    }

    //余额
    public function yue(){
        $shopUserModel = M('Shop_users');
        $money = $shopUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign('money',$money);
        $this->display();
    }

    // 话题
    public function myTopic(){
        // 用户发表帖子表
        $dyCommunityModel = M('Dy_community');
        // 根据openid判断当前的用户是否点击了赞
        $openid = $this->_get('openid');
        $commentResult = $dyCommunityModel->order('add_time desc')->where(array('token'=>$this->token,'openid'=>$openid,'type'=>1))->select();

         // 微信关注数据表
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
       
        
        $countComm = $dyCommunityModel->where(array('token'=>$this->token,'openid'=>$openid,'type'=>1))->count();
        // 用户点赞以及回复表
        $uids = $wxModel->where(array('token'=>$this->token))->getField('id');
        
        $microUser = $wxsModel->field('nickname,headimgurl')->where(array('uid'=>$uids,'openid'=>$openid))->find();
        
        $microUser['nickname'] = !empty($microUser['nickname']) ? $microUser['nickname']:'游客';
        $this->assign('openid',$openid);
        $this->assign('nickname',$microUser['nickname']);
        $this->assign('headimg',$microUser['headimgurl']);
        $this->assign('countComm',$countComm);
        $this->assign('comment',$commentResult);
    
        $this->display();
    }
    // 个人中心
    public function userCenter(){
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
        $userLvlModel = M('dy_userlvl');
        $shopUserModel = M('Shop_users');
        $communityModel = M('dy_community');
        $money = $shopUserModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $community = $communityModel->field('lvl')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        if(!$userLvlModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find()){
                if($wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() && $statusInfo != 0){
                     $userLvlResult = $userLvlModel->data(array(
                        'openid' => $this->openid,
                        'token' => $this->token,
                        'lvl' => 1,
                        'noticetime' => date('Y-m-d H:i:s'),
                        'score' => 20
                    ))->add();
                }elseif($wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() && $statusInfo == 0){
                    $deleteLvL = $userLvlModel->where(array(
                            'openid' => $this->openid,
                            'token' => $this->token
                        ))->delete();
                }
            }
            if(!$community){
                $lvl = 1;
            }else{
                $lvl = $community['lvl'];
            }

            $money['create_score'] = M('dy_score')->where(array(
                    'openid' => $this->openid,
                    'token' => $this->token
                ))
                ->field('score')
                ->select();
            $scores = 0;
            foreach ($money['create_score'] as $key => $value) {
                $score += $value['score'];
            }
            $money['create_score'] = $score;
            $dyCommunityModel = M('Dy_community');
            $uids = $wxModel->where(array('token'=>$this->token))->getField('id');
        
            $microUser = $wxsModel->field('nickname,headimgurl')->where(array('uid'=>$uids,'openid'=>$this->openid))->find();

            $microUser['nickname'] = !empty($microUser['nickname']) ? $microUser['nickname']:'游客';

            $scoredata = M('dy_score')->field('sum(score) as allscore')->where(array('token'=>$this->token,'openid'=>$this->openid,'score'=>array('gt',0)))->select();
            $money['allscore'] = $scoredata[0]['allscore'];
            $where['token']=$this->token;
            $where['scope']=array('elt',$money['allscore']);
            $userlevel_data = M("Shopgrade")->where($where)->order('scope desc')->limit(1)->find();
            $lvl = $this->getUserLevel($this->token,$this->openid);



            $this->assign('nickname',$microUser['nickname']);
            $this->assign('headimg',$microUser['headimgurl']);
            $this->assign('openid',$this->openid);
            $this->assign('money',$money);
            $this->assign('lvl',$lvl);
            // 执行等级更改
            $this->changeLvl();
            $this->display();
    }
    // 我的回复
    public function myReplay(){
        $openid = $this->_get('openid');
        $token = $this->_get('token');
        // 用户发表帖子表
        $dyCommunityModel = M('Dy_community');
        // 用户点赞以及回复表
        $dyUserModel = M('Dy_users');

        // 根据openid判断当前的用户是否点击了赞
        // $commentResult = $dyCommunityModel->order('add_time desc')->select();

         // 微信关注数据表
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
        $findOut = $dyUserModel->field("count(uid) as alls,uid as id")->where(array(
                'token' => $this->token,
                'openid' => $this->openid,
                'comment' => array('NEQ','')
            ))
            ->group('uid')
            ->select();
        
        $commentResult = array();
        foreach ($findOut as $k => $v) {
            $commentResult[$k] = $dyCommunityModel->where(array('id'=>$v['id'],'token'=>$token))->find();
        }
        foreach ($commentResult as $key => $value) {
        $where['uid'] = $value['id'];
        $where['token'] = $this->token;
        $where['comment'] = array('NEQ','');
        $where['replay_id'] = 0;
            $result = $dyUserModel->where($where)->order('comment_time desc')->limit(3)->select();
            $totalCount = $dyUserModel->where($where)->order('comment_time desc')->count();
            $commentResult[$key]['comments'] = $result;
            $commentResult[$key]['totalCount'] = $totalCount;
            $zan = $dyUserModel->where(array('uid'=>$value['id'],'token'=>$token,'openid'=>$this->openid))->find();
            $uids = $wxModel->where(array('token'=>$token))->getField('id');
            $microUser = $wxsModel->field('nickname,headimgurl')->where(array('uid'=>$uids,'openid'=>$this->openid))->find();
    
            // 添加一条数据无效
            $microUser['nickname'] = isset($microUser['nickname']) ? $microUser['nickname']:'游客';
        }
        
        $countComm = $dyUserModel->where(array('token'=>$token,'openid'=>$openid))->count();
        // 用户点赞以及回复表
       
       // $this->assign('zone',$zoneResult);
        $this->assign('countComm',$countComm);
        $this->assign('comment',$commentResult);
        // print_r($commentResult);exit;
    
        //分配openid
        $this->assign('openid',$this->openid);
        $this->display();
    }
    // 人气榜
    public function popular(){
        // 发帖表
        $dyCommunityModel = M('Dy_community');
        // 社区表
        $dyZoneModel = M('Dy_zone');
        // 评论表
        $dyUserModel = M('Dy_users');
        // 发帖次数以及回复次数统计表
        $popularModel = M('Dy_popluar');
        // 人气榜为文章的发帖次数加上评论的次数
        $visiter = $dyZoneModel->field('visiter')->where(array('token'=>$this->token))->find();
        $count = $dyCommunityModel->where(array('token'=>$this->token))->count();
        // 访问量
        $setScoreModel = M('dy_setscore');
        $setScoreResult = $setScoreModel->where(array('token'=>$this->token))->setInc('visiter',1);

        $Group = $dyCommunityModel->field('count(openid) as count,openid')->group('openid')->select();
        $comment = $dyCommunityModel->where(array('token'=>$this->token))->select();
      
        $commGroup = $dyCommunityModel->field('count(openid) as count,openid,headimg,nickname,lvl')->where(array('token'=>$this->token))->group('openid')->select();
        //p($commGroup);
       //放数据
        foreach ($commGroup as $key => $value) {
            if ($popularModel->where(array('token'=>$this->token,'openid'=>$value['openid']))->find()) {
                // 如果有的话在原有的基础上面更新
                $where['openid'] = $value['openid'];
                $user['comment'] = array('NEQ','');
                /*$user['from_openid'] = $value['openid'];*/
                $selectReplay = $dyUserModel->where($user)->count();
                $data = array(
                        'send_time' => $value['count'],
                        'replay_time' => $selectReplay,
                        'total' => $value['count'] + $selectReplay,
                        'lvl' => $value['lvl']
                    );
                $info = $popularModel->where($where)->save($data);
            }else{
                // 如果没有的话就新增
                 $where['comment'] = array('NEQ','');
                 // $where['from_openid'] = $value['openid'];
                 $selectReplay = $dyUserModel->where($where)->count();
                 $data = array(
                    'openid' => $value['openid'],
                    'token' => $this->token,
                    'send_time' => $value['count'],
                    'replay_time' => $selectReplay ,
                    'lvl' => $value['lvl'],
                    'headimg' => $value['headimg'],
                    'nickname' => $value['nickname'],
                    'total' => $value['count'] + $selectReplay
                );
                $info = $popularModel->data($data)->add();
            }
        }
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
        // $uids = $wxModel->where(array('token'=>$this->token))->getField('id');
        // 统计粉丝数量
        $countMicroUser = $setScoreModel->where(array('token'=>$this->token))->getField('visiter');
        $this->assign('countMicroUser',$countMicroUser);
        // 取数据
        $result = $popularModel->field('headimg,nickname,total,lvl')->order('total desc,id desc')->where(array('token'=>$this->token))->group('openid')->select();
       // p($result);die;
        $this->assign('info',$result);
        $this->assign('visiter',$visiter);
        $this->assign('count',$count);
        //如多分期项目添加
        if($this->token=='55cad4ba46c41a8fde9c84274e36fa83'){
            $this->assign('foot',4);
        }
        $this->display();
    }
    // 星级排行榜
    public function stars(){
        $shopUsersModel = M('Shop_users');
        $dyCommunityModel = M('Dy_community');
        $dyZoneModel = M('Dy_zone');
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
        $userLvlModel = M('Shop_users');
        
        $setScoreModel = M('dy_setscore');
        $setScoreResult = $setScoreModel->where(array('token'=>$this->token))->setInc('visiter',1);

        $userLvlResult = $userLvlModel->where(array('token'=>$this->token))->order('score desc')->limit(20)->select();
        foreach ($userLvlResult as $key => $value) {
            $userLvlResult[$key]['lvl'] = $this->getUserLevel($this->token,$value['openid']);
        }

        $countMicroUser = $setScoreModel->where(array('token'=>$this->token))->getField('visiter');
        $this->assign('countMicroUser',$countMicroUser);
        $visiter = $dyZoneModel->field('visiter')->where(array('token'=>$this->token))->find();
        $count = $dyCommunityModel->where(array('token'=>$this->token))->count();
        $this->assign('info',$userLvlResult);
        $this->assign('visiter',$visiter);
        $this->assign('count',$count);
        //如多分期项目添加
        if($this->token=='55cad4ba46c41a8fde9c84274e36fa83'){
            $this->assign('foot',4);
        }
        $this->display();
    }

    public function getUserLevel($token,$openid){
        $scoredata = M('dy_score')->field('sum(score) as allscore')->where(array('token'=>$token,'openid'=>$openid,'score'=>array('gt',0)))->select();
        $users = M('shop_users')->where(array('token'=>$token,'openid'=>$openid))->find();
        $score = $scoredata[0]['allscore']+$users['other_score'];
        $where['token']=$token;
        $where['scope']=array('elt', $score);
        $userlevel_data = M("Shopgrade")->where($where)->order('scope desc')->limit(1)->find();
        return $userlevel_data['name'];
    }
    
    public function getUserLevelBili($token,$openid){
        $scoredata = M('dy_score')->field('sum(score) as allscore')->where(array('token'=>$token,'openid'=>$openid,'score'=>array('gt',0)))->select();
        $users = M('shop_users')->where(array('token'=>$token,'openid'=>$openid))->find();
        $score = $scoredata[0]['allscore']+$users['other_score'];
        $where['token']=$token;
        $where['scope']=array('elt', $score);
        $userlevel_data = M("Shopgrade")->where($where)->order('scope desc')->limit(1)->find();
        return $userlevel_data['bili'];
    }


    // 二维数组排序
    protected function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
        if(is_array($multi_array)){ 
            foreach ($multi_array as $row_array){ 
                if(is_array($row_array)){ 
                    $key_array[] = $row_array[$sort_key]; 
                }else{ 
                    return false; 
                } 
            } 
        }else{ 
            return false; 
        } 
        array_multisort($key_array,$sort,$multi_array); 
        return $multi_array; 
    }
    // 我的回复
    public function myInfo(){
        $dyUsers = M('dy_users');
        $dyCommunityModel = M('Dy_community');

        $myreplay = $dyUsers->where(array('token'=>$this->token,'openid'=>$this->openid))->order('comment_time desc')->select();
        $replayMy = array();
        foreach ($myreplay as $key => $value) {
            $res = $dyUsers->where(array('token'=>$this->token,'replay_id'=>$value['id']))->select();
            foreach ($res as $ks => $vs) {
                # code...
                $replayMy[] = $vs;
            }
        }

        $mainCommunity = $dyCommunityModel->join("tp_dy_users as du on du.uid= tp_dy_community.id")->where(array('tp_dy_community.token'=>$this->token,'tp_dy_community.openid'=>$this->openid,'du.replay_id'=>0))->select();
        foreach ($mainCommunity as $k => $v) {
            array_push($replayMy, $v);
        }
        $this->assign('comment',$replayMy);
        $this->assign('countComm',count($replayMy));
        $this->display();
    }
    
    //由社区页面进入
    public function lookUpDetails(){
         // 微信关注数据表
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');
        
        $getUid = $wxModel->field('id')->where(array('token'=>$this->token))->find();
        $statusInfo = $wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->getField('status');
        //改成了返
       /* if(!$wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() || $statusInfo == 0){
            
            exit($this->display('./tpl/Wap/default/Commercedyb_noticedyb.html'));
        }*/
        // 接收文章ID
        $articleID = $this->_get('articleId','intval');
        $communityModel = M('dy_community');
        $dyUserModel = M('dy_users');
        $findArticle = $communityModel->where(array(
                'token' => $this->token,
                'id' => $articleID
            ))->order('add_time desc')
            ->find();
        $findComment = $dyUserModel->where(array(
                'token' => $this->token,
                'uid' => $articleID,
                'comment' => array(
                        'NEQ',''
                    )
            ))->order('comment_time desc')->select();
        
        foreach ($findComment as $k => $v) {
                if($v['replay_id'] != 0){
                    $findNickName = $dyUserModel->where(array('id'=>$v['replay_id'],'token'=>$this->token))->find();
                    $findComment[$k]['nickname'] = $v['nickname']."  回复  ".$findNickName['nickname'];
                }
            }
            
        $totalpinglun = $dyUserModel->where(array(
                'uid'=>$articleID,
                'token'=>$this->token,
                'comment'=>array('NEQ','')
                ))->count();
        $zanCount = $dyUserModel->field('count(openid) as oid')->where(array(
                'uid'=>$articleID,
                'ifzan'=>1
                ))->group('openid')->select();
        $zanCount = count($zanCount);
        // 收集到是否点赞
        $ifzan = $dyUserModel->field('ifzan')->where(array(
                // 'from_openid' => $findArticle['openid'],
                'uid' => $findArticle['id'],
                'token' => $this->token,
                'openid' => $this->openid
            ))->find();
        $findArticle['comments'] = $findComment;
        $findArticle['ifzan'] = $ifzan['ifzan'];
        $findArticle['dianzan'] = $zanCount;
        $findArticle['pinglun'] = $totalpinglun;
        $this->assign('article',$findArticle);
        /*
          阅读消息
        */
        if($_GET['is_read'] == 1){
            $comment_id  = $_GET['comment_id'];
            if($comment_id){
                M('Dy_users')->where(array('id'=>$comment_id))->save(array('is_read'=>1));
            }
        }  

        if($_GET['type'] == 'myinfo'){
            $this->display('./tpl/Wap/default/Commercedyb_lookUpMyInfo.html');
        }elseif($_GET['type'] == 'myreplay'){
            $this->display('./tpl/Wap/default/Commercedyb_lookUpMyReplay.html');
        }else{
            $this->display();
        }
    } 

    // 删除自己的帖子
    public function deletes(){
        $did = $this->_get('did','intval');
        $communityModel = M('dy_community');
        $dyUserModel = M('dy_users');
        if($dyUserModel->where(array('uid'=>$did,'token'=>$this->token))->select()){
            if($communityModel->where(array('id'=>$did,'token'=>$this->token))->delete() && $dyUserModel->where(array('uid'=>$did,'token'=>$this->token))->delete()){
                echo json_encode(array(
                        'status' => 0,
                        'info' => '删除成功',
                        'url' => U('Commercedyb/zoneIndex', array(
                                'token' => $this->token, 
                                'openid' => $this->openid
                        ))
                    ),true);
            }else{
                echo json_encode(array(
                        'status' => 1,
                        'info' => '删除失败'
                    ),true);
            }
        }else{
            if($communityModel->where(array('id'=>$did,'token'=>$this->token))->delete()){
                 echo json_encode(array(
                        'status' => 0,
                        'info' => '删除成功',
                        'url' => U('Commercedyb/zoneIndex', array(
                                'token' => $this->token, 
                                'openid' => $this->openid
                        ))
                    ),true);
            }else{
                echo json_encode(array(
                        'status' => 1,
                        'info' => '删除失败'
                    ),true);
            }
        }
        
    }

    // 删除自己的评论,内置内容变成空的，真正的删除
    public function delComment(){
        $id = $this->_get('id','intval');
        $token = $this->_get('token');
        $openid = $this->_get('openid');
        $dyUserModel = M('dy_users');
     //   p($_GET);die;

        $flag = $dyUserModel->where(array(
                'id' => $id
            ))->delete();
        if ($flag) {
            if(isset($_GET['url_type'])){//这里证明是话题贴过来的
                echo json_encode(array(
                    'status' => 0,
                    'info' => '删除评论成功',
                    'url' => U('Commercedyb/lookUpDetails1', array(
                        'token' => $token,
                        'openid' => $openid,
                        'articleId'=>$_GET['articleId']
                    ))
                ),true);
            }else{
                echo json_encode(array(
                    'status' => 0,
                    'info' => '删除评论成功',
                    'url' => U('Commercedyb/zoneIndex', array(
                        'token' => $token,
                        'openid' => $openid
                    ))
                ),true);
            }

        }else{
            echo json_encode(array(
                    'status' => 1,
                    'info' => '删除评论失败'
                ),true);
        }
    }

    // 插入回复数据
    public function replayData(){
        if(IS_AJAX){
            if(IS_POST){
                $wxuser = M('wxuser');
                $wxusers = M('wxusers');
                $userLvlModel = M('dy_userlvl');
                $setScoreModel = M('dy_setscore');
                $token = $this->_get('token');
                $currentOpenid = $this->_post('replay');
                $article = $this->_post('articleOpenid');
                $ifzan = $this->_post('ifzan');
                $uid = $this->_post('id','intval');
                $pid = $this->_post('pid','intval');

                $comment = htmlspecialchars_decode($this->_post('content'));
                // 微信名称
                $uids = $wxuser->field('id')->where(array('token'=>$token))->find();
                $personal = $wxusers->field('headimgurl,nickname')->where(array('openid'=>$currentOpenid,'uid'=>$uids['id']))->find();
                $headimg = $personal['headimgurl'];
                $nickname = $personal['nickname'];
                $nickname = !empty($nickname) ? $nickname : "游客";
                // 加入一条数据
                $data = array(
                        'uid' => $uid,
                        'token' => $token,
                        'openid' => $currentOpenid,
                        'ifzan' => $ifzan,
                        'comment' => $comment,
                        'comment_time' => date("Y-m-d H:i:s"),
                        // 'from_openid' => $article,
                        'replay_id' => $pid,
                        'headimg' => $headimg,
                        'nickname' => $nickname
                    );
                if(M('dy_users')->data($data)->add()){
                    $huifu = $setScoreModel->field('pinglun,limits,huifu,fabiao')->where(array('token'=>$this->token))->find();
                    $ping = M('dy_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'comment_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59"))),'replay_id'=>0))->count();
                    $hui = M('dy_users')->where(array('token'=>$this->token,'openid'=>$this->openid,'comment_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59"))),'replay_id'=>array('NEQ',0)))->count();
                    $fatieCount = M('dy_community')->where(array('token'=>$this->token,'openid'=>$this->openid,'add_time'=>array('between',array(date("Y-m-d 0:0:0"),date("Y-m-d 23:59:59")))))->count();
                    
                    $ping = $ping * $pinglun['pinglun'];
                    $hui = $hui * $pinglun['huifu'];
                    $fa = $fatieCount * $pinglun['fabiao'];
                    $allScore = $ping + $hui + $fa;
                    if($allScore <= $huifu['limits']){
                        echo json_encode(array(
                            'status' => 0,
                            'info' => '回复成功,获得'.$huifu['huifu'].'个积分',
                            'url' => U('Commercedyb/zoneIndex',array(
                                'token'=>$token,
                                'openid'=>$openid
                             ))
                        ),true);
                    }else{
                        echo json_encode(array(
                            'status' => 0,
                            'info' => '回复成功,今日积分已上限',
                            'url' => U('Commercedyb/zoneIndex',array(
                                'token'=>$token,
                                'openid'=>$openid
                             ))
                        ),true);
                    }
                    $huifu = M('dy_setscore')->where(array('token'=>$this->token))->getField('huifu');
                    $this->addScore($token,$currentOpenid,3,$huifu);
                }else{
                    echo json_encode(array(
                            'status' => 1,
                            'info' => '回复失败'
                        ),true);
                }
            }
        }
    }

    // 公告
    public function notices(){
        $noticeModel = M('Dy_notice');
        $notice = $noticeModel->where(array('token'=>$this->token,'id'=>$this->_get('id','intval')))->find();   
        $notice['content'] = htmlspecialchars_decode($notice['content']);
        $this->assign('info',$notice);
        $this->display();
    }

    public function shareSuccess(){
        // 用户分享成功后进入到此页面进行的操作
        $userLvlModel = M('shop_users');
        $setScoreModel = M('dy_setscore');
        $find = $setScoreModel->where(array('token'=>$this->token))->getField('zhuanfa');
        $this->addScore($this->token,$this->openid,5,$find);
        if($userLvlModel->where(array('token'=>$this->token,'openid'=>$this->openid))->setInc('score',$find)){
            echo json_encode(array(
                'status' => 0,
                'info' => '分享成功！增加'.$find.'个积分',
                'url' =>  U('Wap/Commercedyb/zoneIndex',array('token'=>$this->token,'openid'=>$this->openid))
            ));
        }else{
             echo json_encode(array(
                'status' => 1,
                'info' => '分享失败!'
            ));
        }
    }
    // 改变等级的通用性函数
    protected function changeLvl(){
        // 首先要改变等级的是三张表
        $dyCommunityModel = M('dy_community');//用户发帖表
        $popularModel = M('Dy_popluar');//人气排行榜表
        $userLvlModel = M('dy_userlvl');//用户积分表 
        $score = $userLvlModel->where(array(
            'token'=>$this->token,
            'openid'=>$this->openid
        ))->getField('score');
        if($score >= 0 && $score <= 1000){
            // 一级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel);
        }elseif($score >= 1001 && $score <= 3000){
            // 二级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,2);
        }elseif($score >= 3001 && $score <= 6000){
            // 三级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,3);
        }elseif($score >= 6001 && $score <= 10000){
            // 四级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,4);
        }elseif($score >= 10001 && $score <= 15000){
            // 五级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,5);
        }elseif($score >= 15001 && $score <= 20000){
            // 六级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,6);
        }elseif($score >= 20001 && $score <= 25000){
            // 七级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,7);
        }elseif($score >= 25001 && $score <= 30000){
            // 八级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,8);
        }elseif($score >= 30001 && $score <= 35000){
            // 九级
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,9);
        }else{
            // 终极会员
            $this->changeLvlSecond($userLvlModel,$dyCommunityModel,$popularModel,10);
        }
    }

    // 一些实质性的改变
    protected function changeLvlSecond($table1,$table2,$table3,$lvls = 1){
         $userLvlResult = $table1->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))->save(array('lvl'=>$lvls));
         $dyCommunityResult = $table2->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))->save(array('lvl'=>$lvls));
         $popularResult = $table3->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))->save(array('lvl'=>$lvls));
    }

/*******************************************************上述张学怿缩写*/
    /**
     * 用户资料编辑
     */
    public function user_edit(){
        if(IS_AJAX){
            $data=$_POST;

            $data=array_filter($data);
            if(!empty($data)){
                M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);


                $data1['code']=1;
                $data1['msg']='成功';
                echo json_encode($data1);
            }else{
                $data1['code']=0;
                $data1['msg']='失败';
                echo json_encode($data1);
            }



        }else {
            $memberdata = M('Shop_users')->where(array('token' => $this->token, 'openid' => $this->openid))->find();
            $this->assign('memberdata', $memberdata);


            $this->display();
        }
    }

    /*
    余额支付密码设置与修改
    */
    public function yepasswd(){
        if(IS_AJAX){
            $yepasswd = $_POST['yepasswd'];
            if(M('Shop_users')->where(array('token' => $this->token, 'openid' => $this->openid))->save(array('yepasswd'=>$yepasswd))){
                echo $this->encode(array('code'=>0,'msg'=>'修改成功'));exit;
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'系统繁忙'));exit;
            }
        }else{
            $memberdata = M('Shop_users')->where(array('token' => $this->token, 'openid' => $this->openid))->find();
            $this->assign('memberdata',$memberdata);
            $this->display();
        }
    }


    public function gotoGuanzhu(){
        $aNickuser = M('Wxusers')->where(array(
                        'uid'=>$this->tpl['id'],
                        'openid'=>$this->openid,
                    ))->find();
        if($_GET['from_openid']){
            $this->openid = $_GET['from_openid'];
        }
        if($aNickuser['status'] != 1){
            $url = C('site_url').'index.php?g=Home&m=Nofind&a=isnotsub&token='.$this->token.'&openid='.$this->openid.'&from_openid='.$_GET['from_openid'];
            $this->is_subscribe = 0;
            //$this->redirect($url);
        }

    }

    /**
     * 话题主页面
     */
    public function lookUpDetails1(){
        $wxModel = M('wxuser');
        $wxsModel = M('wxusers');

        $getUid = $wxModel->field('id')->where(array('token'=>$this->token))->find();
        $statusInfo = $wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->getField('status');
        //改成了返
        /* if(!$wxsModel->where(array('uid'=>$getUid['id'],'openid'=>$this->openid))->find() || $statusInfo == 0){

             exit($this->display('./tpl/Wap/default/Commercedyb_noticedyb.html'));
         }*/
        // 接收文章ID
        $articleID = $this->_get('articleId','intval');
        $iszhuanfa = $this->_get('iszhuanfa','intval');
        $communityModel = M('dy_community');
        $dyUserModel = M('dy_users');
        $findArticle = $communityModel->where(array(
            'token' => $this->token,
            'id' => $articleID
        ))->find();
        $findComment = $dyUserModel->where(array(
            'token' => $this->token,
            'uid' => $articleID,
            'comment' => array(
                'NEQ',''
            )
        ))->order('comment_time desc')->limit(20)->select();//这里限制20条

        foreach ($findComment as $k => $v) {
            if($v['replay_id'] != 0){
                $findNickName = $dyUserModel->where(array('id'=>$v['replay_id'],'token'=>$this->token))->find();
                $findComment[$k]['nickname'] = $v['nickname']."  回复  ".$findNickName['nickname'];
                $findComment[$k]['dianzan']=$dyUserModel->where(array('replay_id'=>$v['id'],'ifzan'=>1))->count();
                $findComment[$k]['pinglun']=$dyUserModel->where(array('replay_id'=>$v['id'],'comment'=>array('NEQ','')))->count();
            }else{//等于0的代表这条是评论的话主的
                $findComment[$k]['dianzan']=$dyUserModel->where(array('replay_id'=>$v['id'],'ifzan'=>1))->count();
                $findComment[$k]['pinglun']=$dyUserModel->where(array('replay_id'=>$v['id'],'comment'=>array('NEQ','')))->count();
                /**
                 * 判断此条是谁被我点赞过
                 */
                if($dyUserModel->where(array('replay_id'=>$v['id'],'ifzan'=>1,'openid'=>$this->openid))->find()){
                    $findComment[$k]['isme_zan']=1;
                }else{
                    $findComment[$k]['isme_zan']=0;
                }
               // $findComment[$k]['isme_zan']=$dyUserModel->where()
            }
        }

        $totalpinglun = $dyUserModel->where(array(
            'uid'=>$articleID,
            'token'=>$this->token,
            'comment'=>array('NEQ','')
        ))->count();
        $zanCount = $dyUserModel->field('count(openid) as oid')->where(array(
            'uid'=>$articleID,
            'ifzan'=>1
        ))->group('openid')->select();
        $zanCount = count($zanCount);
        // 收集到是否点赞
        $ifzan = $dyUserModel->field('ifzan')->where(array(
            // 'from_openid' => $findArticle['openid'],
            'uid' => $findArticle['id'],
            'token' => $this->token,
            'openid' => $this->openid
        ))->find();
        $findArticle['comments'] = $findComment;
        $findArticle['ifzan'] = $ifzan['ifzan'];
        $findArticle['dianzan'] = $zanCount;
        $findArticle['pinglun'] = $totalpinglun;
        //echo $this->openid;
      //  p($findArticle);
       // die;
        $this->assign('article',$findArticle);
        $this->assign('iszhuanfa',$iszhuanfa);
        // p($findArticle);die;
        // print_r($findArticle);exit;
        if($_GET['type'] == 'myinfo'){
            $this->display('./tpl/Wap/default/Commercedyb_lookUpMyInfo.html');
        }elseif($_GET['type'] == 'myreplay'){
            $this->display('./tpl/Wap/default/Commercedyb_lookUpMyReplay.html');
        }else{
            $this->display();
        }
    }

}
