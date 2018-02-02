<?php
/**

 * Created by IntelliJ IDEA.

 * User: 肖国平

 * Date: 15-1-6

 * Time: 下午5:47

 * To change this template use File | Settings | File Templates.

 */
class CommerceAction extends BaseAction{
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
    //O2O分两种情况，微信用户则不需要登陆，如果不是微信用户则需要注册，使用注册之后的账号进行登陆
    public function _initialize(){
        parent::_initialize();
        $this->token=$this->_get("token");
        $this->openid=$this->_get("openid");
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $this->api=C('baidu_map_api');
        $this->ip=$_SERVER['REMOTE_ADDR'];
        $this->style=1;

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
        $userscore = M("Shop_users")->field("score")->where(array("token"=>$this->token,'openid'=>$this->openid))->find();
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
        $type=M("Shoptype")->field("id,pic,name,sort")->where(array("token"=>$this->token,'position'=>array('in',array(1,0))))->order('sort asc')->select();
	    $type2=M("Shoptype")->field("id,pic,name,sort")->where(array("token"=>$this->token,'position'=>2))->order('sort asc')->select();
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
        if(isset($_POST['distance'])) {
            $distance = $_POST['distance'];
        }else{
            $distance = 10000;
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
        $data=M("Shopunion")->field("id,cname,des,seng,si,xian,pic,long,lat,address")->where(array("token"=>$this->token))->select();
        $shopdistance = M('Baseset')->where(array('token'=>$this->token))->find();
        $distance = $shopdistance['union_distance'];

	$union=$this->getinfo($distance*1000,$data,$lat1,$long1);
        $this->assign("union",$union);
        $this->assign("distance",$distance);
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
            $long1=session("lng");
            $lat1=session("lat");
            if(!$long1 && !$lat1){
                $ip=get_client_ip();
                $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
                $location=json_decode(file_get_contents($url),true);

                $long1=floatval($location['content']['point']['x']);//起点x坐标
                $lat1=floatval($location['content']['point']['y']);//起点y坐标
            }
            $data=M("Shop")->field("id,username,des,pic,long,lat,status,tel,address,map_url,start_time,end_time,is_show")->where(array("tid"=>$typeid,"token"=>$this->token))->order("status asc")->select();
        }
        if($access=="union" || $access=="search"){
            $lat1=session("lat");
            $long1=session("lng");
            if($lat1 && $long1){
                $data=M("shop")->field("id,username,des,pic,long,lat,status,tel,address,map_url,start_time,end_time,is_show")->where(array("token"=>$this->token))->order("status asc")->select();
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
            $flashes = M("Oflash")->field("pic,url")->where(array("token" => $this->token, 'type' => 3, 'tid' => $shopmember['id'],'status'=>1))->select();
        }
        $shopdistance = M('Baseset')->where(array('token'=>$this->token))->find();
	    $shopinfo=$this->getinfo($shopdistance['distance']*1000,$data,$lat1,$long1);
        $type=M("Shoptype")->field("id,name")->where(array("token"=>$this->token))->select();
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
        if(IS_POST){
            $n=($_POST['n']-1)*6+21;
            $wareid=$this->_get("wareid","intval");
            if(isset($_GET['cid'])){
                $cartid=$this->_get("cid","intval");
                $list=M("Shopware")
                    ->join("join tp_shop on tp_shop.id=tp_shopware.sid")
                    ->field("tp_shopware.id,tp_shopware.sid,tp_shopware.stock,tp_shopware.tid,tp_shopware.attr,tp_shopware.name,tp_shopware.price,tp_shopware.des,tp_shopware.vprice,tp_shopware.pic,tp_shopware.sid,tp_shop.username")
                    ->where(array("tp_shopware.tid"=>$cartid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))
                    ->limit($n,6)->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
            }else{
                $list=M("Shopware")
                    ->join("join tp_shop on tp_shop.id=tp_shopware.sid")
                    ->field("tp_shopware.id,tp_shopware.sid,tp_shopware.attr,tp_shopware.stock,tp_shopware.name,tp_shopware.tid,tp_shopware.price,tp_shopware.des,tp_shopware.vprice,tp_shopware.pic,tp_shop.username")
                    ->where(array("tp_shopware.sid"=>$wareid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))
                    ->limit($n,6)->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
            }


            $this->assign('list', $list);
            //p($list);

            $x = $this->fetch('./tpl/Wap/default/Commerce_ShopWare_old2.html',$list);//内容放进来
            exit($x);

        }else{
            import("Org.Data");

            if(isset($_GET['wareid'])){
                $wareid=$this->_get("wareid","intval");
                $cartinfo=M("Shopclassfy")->field("id,tname,pid")->where(array("branch_id"=>$wareid,'token'=>$this->token))->order("sort asc")->select();
                $shopinfo=M("Shop")->field("username,des,start_time,end_time,yingye_status,id")->where(array("id"=>$wareid,'token'=>$this->token))->find();
                $wareinfo=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.attr,tp_shopware.stock,tp_shopware.name,tp_shopware.tid,tp_shopware.price,tp_shopware.des,tp_shopware.vprice,tp_shopware.pic,tp_shop.username")->where(array("tp_shopware.sid"=>$wareid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->limit(0,21)->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
                $wareinfo1=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.attr,tp_shopware.stock,tp_shopware.name,tp_shopware.tid,tp_shopware.price,tp_shopware.des,tp_shopware.vprice,tp_shopware.pic,tp_shop.username")->where(array("tp_shopware.sid"=>$wareid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();

            }

            if(isset($_GET['cid'])){
                $cartid=$this->_get("cid","intval");
                $shopinfo=M("Shopclassfy")->join("join tp_shop on tp_shop.id=tp_shopclassfy.branch_id")->field("tp_shop.username,tp_shop.id,tp_shop.start_time,tp_shop.end_time,tp_shop.yingye_status")->where(array("tp_shopclassfy.id"=>$cartid))->find();
                $wareid=M("Shopclassfy")->field("branch_id")->where(array("id"=>$cartid))->find();
                $cartinfo=M("Shopclassfy")->field("id,tname,pid")->where(array("branch_id"=>$wareid['branch_id']))->order("sort asc")->select();
                $wareinfo=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.stock,tp_shopware.tid,tp_shopware.attr,tp_shopware.name,tp_shopware.price,tp_shopware.des,tp_shopware.vprice,tp_shopware.pic,tp_shopware.sid,tp_shop.username")->where(array("tp_shopware.tid"=>$cartid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->limit(0,21)->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
                $wareinfo1=M("Shopware")->join("join tp_shop on tp_shop.id=tp_shopware.sid")->field("tp_shopware.id,tp_shopware.sid,tp_shopware.stock,tp_shopware.tid,tp_shopware.attr,tp_shopware.name,tp_shopware.price,tp_shopware.des,tp_shopware.vprice,tp_shopware.pic,tp_shopware.sid,tp_shop.username")->where(array("tp_shopware.tid"=>$cartid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
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
            $warejson=json_encode($newinfo);
            $flashes=M("Oflash")->field("pic,url")->where(array("token"=>$this->token,'type'=>4,"tid"=>$_GET['wareid'],"status"=>1))->limit(5)->select();
            $carts=Data::channelLevel($cartinfo);
            $this->assign("carts",$carts);
            $this->assign("wareid",$wareid);
            $this->assign("shopinfo",$shopinfo);
            $this->assign("warejson",$warejson);
            $this->assign("flashes",$flashes);
            $this->assign("wareinfo",$wareinfo);
            $this->assign("cid",$_GET['cid']);

            $this->assign("bbk",$shopinfo['des']);
            //echo $shopinfo['des'];die;
            if($this->token=='a5114ab1a60c81d04e86447a0bd123be'){//a5114ab1a60c81d04e86447a0bd123be
                //小二快快
                $this->display('tpl/Wap/default/Commerce_ShopWare_old.html');
            }else{
                $this->display();
            }
        }



    }
    public function scroll(){
        $n=($_POST['n']-1)*6+21;
        $wareid=$this->_get("wareid","intval");
        if(isset($_GET['cid'])){
            $cartid=$this->_get("cid","intval");
            $list=M("Shopware")
                ->join("join tp_shop on tp_shop.id=tp_shopware.sid")
                ->field("tp_shopware.id,tp_shopware.sid,tp_shopware.stock,tp_shopware.tid,tp_shopware.attr,tp_shopware.name,tp_shopware.price,tp_shopware.des,tp_shopware.vprice,tp_shopware.pic,tp_shopware.sid,tp_shop.username")
                ->where(array("tp_shopware.tid"=>$cartid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))
                ->limit($n,6)->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
        }else{
            $list=M("Shopware")
                ->join("join tp_shop on tp_shop.id=tp_shopware.sid")
                ->field("tp_shopware.id,tp_shopware.sid,tp_shopware.attr,tp_shopware.stock,tp_shopware.name,tp_shopware.tid,tp_shopware.price,tp_shopware.des,tp_shopware.vprice,tp_shopware.pic,tp_shop.username")
                ->where(array("tp_shopware.sid"=>$wareid,"tp_shopware.token"=>$this->token,"tp_shopware.status"=>1,"tp_shopware.stock"=>array('gt',0)))
                ->limit($n,6)->order("tp_shopware.sort asc,tp_shopware.tid desc")->select();
        }


        $this->assign('list', $list);
        //p($list);

        $x = $this->fetch('./tpl/Wap/default/Commerce_ShopWare_old2.html',$list);//内容放进来
        exit($x);
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
        $oid=$this->_get("oid","intval");
        if(!M("Mainorder")->where(array("id"=>$oid))->find()){
            exit("订单不存在");
        }
        $orderinfo=M("Mainorder")->field("id,ordernumber,totalnum,totalmoney")->where(array("id"=>$oid,"status"=>0))->find();
        $address=M("Msinfo")->where(array("openid"=>$this->openid,"token"=>$this->token))->select();
        $this->assign("orderinfo",$orderinfo);
        $this->assign("address",$address);
        $this->assign("oid",$oid);
        $this->display();
    }

    //支付时修改订单信息,插入订单地址
    public function EditOrder(){
        if(IS_AJAX) {
            $paytype = $this->_get("paytype");
            $aid = $_POST['wid'];
            $update = M("Msinfo")->field("uname,tel,address")->where(array("id" => $aid))->find();
            $update['buyname'] = $update['uname'];
            $update['instruct'] = $_POST['instruct'];

            $order = M("Mainorder")->where(array("id" => $this->_get('oid', "intval"), "token" => $this->token, "openid" => $this->openid))->find();
            if ($order['totalmoney'] < $this->shopScoresetdata['notget_money']) {
                $update['noget_money'] = $this->shopScoresetdata['add_money'];
            } else {
                $update['noget_money'] = 0;
            }

            $scoreid = $_POST['scoreid'];

            $subscore = 0;//需要减掉的分数
            $addscore = 0;//需要减掉的分数

            $usedscore = $_POST['usedscore'];
            if ($usedscore == 2) {
                //使用
                if ($scoreid) {
                    if ($this->userscore > $scoreid) {
                        $subscore = $scoreid;
                        $update['score_money'] = round($scoreid / $this->shopScoresetdata['moneyscore'], 2);
                    } else {
                        $update['score_money'] = 0;
                    }
                }
            }

            //是否下单送积分
            if ($this->shopScoresetdata['orderscore']) {
                $update['score'] = $order['totalmoney'] * $this->shopScoresetdata['orderscore'];
                $addscore = $update['score'];
            } else {
                $update['score'] = 0;
            }

//            M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setInc('score',$addscore);


            if ($paytype == "wxpay") {
                $update['paytype'] = 1;
                $paytypetext = '微信支付';

                if (M("Mainorder")->where(array("id" => $this->_get('oid', "intval"), "token" => $this->token, "openid" => $this->openid))->save($update)) {
                    //减掉积分
                    //M('Shop_users')->where(array("token" => $this->token, "openid" => $this->openid))->setDec('score', $subscore);

                    //                //新增积分
                    //                M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setInc('score',$addscore);
                    //更新复订单地址
                    $orderres = M("Mainorder")->where(array("id" => $this->_get('oid', "intval"), "token" => $this->token, "openid" => $this->openid))->find();

                    M("Sideorder")->where(array('token' => $this->token, 'mid' => $order['id']))->save(array('buyname' => $orderres['buyname'], 'tel' => $orderres['tel'], 'address' => $orderres['address'], 'paytype' => $orderres['paytype']));
                    //新增减库存
                    $sideorderdata = M('Sideorder')->where(array('mid' => $orderres['id']))->select();
                    //减少库存
                    foreach ($sideorderdata as $korder => $vorder) {
                        $detailInfo = array();
                        $detailInfo = M("Sidedetail")->where(array("sid" => $vorder['id']))->select();
                        foreach ($detailInfo as $v) {
                            //减少库存
                            M('Shopware')->where(array('token' => $this->token, 'id' => $v['gid']))->setDec('stock', $v['num']);
                        }
                    }

                    $this->ajaxReturn(array("status" => 1, "info" => "下单成功,正在跳转微信支付...!"));
                } else {
                    $this->ajaxReturn(array("status" => 0, "info" => "下单失败!"));
                }
            }
            if ($paytype == "alipay") {
                $update['paytype'] = 2;
                $paytypetext = '支付宝支付';
            }
            if ($paytype == "delivery") {
                $update['paytype'] = 3;
                $paytypetext = '货到付款';

                if (M("Mainorder")->where(array("id" => $this->_get('oid', "intval"), "token" => $this->token, "openid" => $this->openid))->save($update)) {
                    //减掉积分
                    M('Shop_users')->where(array("token" => $this->token, "openid" => $this->openid))->setDec('score', $subscore);

    //                //新增积分
    //                M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setInc('score',$addscore);
                    //更新复订单地址
                    $orderres = M("Mainorder")->where(array("id" => $this->_get('oid', "intval"), "token" => $this->token, "openid" => $this->openid))->find();

                    $sideorderdata = M('Sideorder')->where(array('mid' => $orderres['id']))->select();
                    //减少库存
                    foreach ($sideorderdata as $korder => $vorder) {
                        $detailInfo = array();
                        $detailInfo = M("Sidedetail")->where(array("sid" => $vorder['id']))->select();
                        foreach ($detailInfo as $v) {
                            //减少库存
                            M('Shopware')->where(array('token' => $this->token, 'id' => $v['gid']))->setDec('stock', $v['num']);
                        }
                    }


                    M("Sideorder")->where(array('token' => $this->token, 'mid' => $order['id']))->save(array('buyname' => $orderres['buyname'], 'tel' => $orderres['tel'], 'address' => $orderres['address'], 'paytype' => $orderres['paytype']));

                    //发送短信通知渠道店铺管理员
                    $members = M("Shopmember")->field("tel")->where(array("token" => $this->token))->select();
                    foreach ($members as $v) {
                        $content = "尊敬的客户{$order['buyname']}，您已经成功下单，订单号为{$order['ordernumber']}";
                        $this->sendMessage($v['tel'], $content);
                    }

                    /*
                     * 通知微信
                     */
                    $orderdata = M("Mainorder")->where(array("id" => $this->_get('oid', "intval"), "token" => $this->token, "openid" => $this->openid))->find();
                    $orderDetail = M("Sideorder")->field('tp_sidedetail.gname,tp_sidedetail.num')->join('join tp_sidedetail on tp_sideorder.id=tp_sidedetail.sid')->where(array('tp_sideorder.mid' => $this->_get('oid', 'intval'), "tp_sideorder.token" => $this->token, "tp_sideorder.openid" => $this->openid))->select();
                    $strDetail = "订单详情:\n";
                    foreach ($orderDetail as $k => $v) {
                        $strDetail .= ($k + 1) . '、' . $v['gname'] . '×' . $v['num'] . "\n";
                    }

                    if($this->token != 'a5114ab1a60c81d04e86447a0bd123be'){
                        $endStr = "\n请耐心等待店家送货.请即时确认收货，以便生成积分兑换商品!";
                    }else{
                        $endStr='';
                    }

                    /*
                      获取店铺ID
                    */
                    $tempShop = explode('|', $order['shopid']);
                    $shopData = M('Shop')->where(array('token' => $this->token, 'id' => $tempShop[1]))->find();

                    $notichcontent = $this->wxusers['nickname'] . "您好,交易提醒\n订单编号：" . $orderdata['ordernumber'] . "\n创建时间:" . $orderdata['buytime'] . "\n订单总额:" . $orderdata['totalmoney'] . "元\n" . "支付方式:" . $paytypetext . "\n" . $strDetail . "收货人:" . $orderdata['buyname'] . "\n电话:" . $orderdata['tel'] . "\n地址:" . $orderdata['address'] . "\n配送商家:" . $shopData['username'] . "\n商家电话:" . $shopData['tel'] . $endStr;
                    $postdata = array('openid' => $this->openid, 'token' => $this->token, 'content' => $notichcontent);
                    $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                    $data = $this->api_notice_increment($url, http_build_query($postdata));
                    if (!$data) {
                        $this->api_notice_increment($url, http_build_query($postdata));
                    }

                    /*
                     * 发给店家
                     */
                    $aStaff = M('Shop_staff')->where(array('token' => $this->token, 'sid' => $shopData['id']))->find();
                    if ($aStaff) {
                        $notichcontent = "有新订单了哦\n订单编号：" . $orderdata['ordernumber'] . "\n创建时间:" . $orderdata['buytime'] . "\n订单总额:" . $orderdata['totalmoney'] . "元\n" . "支付方式:" . $paytypetext . "\n" . $strDetail . "收货人:" . $orderdata['buyname'] . "\n电话:" . $orderdata['tel'] . "\n地址:" . $orderdata['address'];
                        $postdata = array('openid' => $aStaff['openid'], 'token' => $this->token, 'content' => $notichcontent);
                        $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                        $data = $this->api_notice_increment($url, http_build_query($postdata));
                        if (!$data) {
                            $this->api_notice_increment($url, http_build_query($postdata));
                        }
                    }


                    $this->ajaxReturn(array("status" => 1, "info" => "下单成功,请等待3秒钟系统正在跳转!"));
                } else {
                    $this->ajaxReturn(array("status" => 0, "info" => "下单失败!"));
                }
           }

        }else{

        }
    }

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
            if(isset($_POST['seng']) && $_POST['seng'] !='请选择省份'){
                $condition['seng']=$_POST['seng'];
            }
            if(isset($_POST['si']) && $_POST['si'] !='请选择城市'){
                $condition['si']=$_POST['si'];
            }
            if(isset($_POST['xian']) && $_POST['xian'] !='请选择地区'){
                $condition['xian']=$_POST['xian'];
            }
            if(trim($this->_post("cname"))) {
                $condition['cname'] = array('like', '%' . trim($this->_post("cname") . '%'));
            }


            /*$ip=get_client_ip();
            $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
            $location=json_decode(file_get_contents($url),true);
	    */
            $long1=session('lng');//起点x坐标
            $lat1=session('lat');//起点y坐标
            $unionList=M("Shopunion")->field('id,cname,long,lat,des,pic,address,seng,si,xian')->where($condition)->select();

            $unionData=$this->getinfo(10000000000,$unionList,$lat1,$long1);
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
            $keys[]=$v['shopId'];
            $v['gid']=$k;
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
                //M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setDec('score',$orderdata['score']);

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
            $data=M("Mainorder")->field("id,totalmoney")->where(array("token"=>$this->token,"openid"=>$this->openid,"id"=>$id))->find();
            $flag=true;
            if($data){
                $sideorder=M("Sideorder")->field("id")->where(array("mid"=>$id))->select();
                foreach($sideorder as $v){
                    if(M("Sideorder")->where(array("id"=>$v['id']))->save(array("sendstatus"=>2,'paystatus'=>1))){
                        $flag=true;
                    }else{
                        $flag=false;
                    }
                }
                if($flag){
                    if(M("Mainorder")->where(array("id"=>$id))->save(array("sendstatus"=>2,'paystatus'=>1))){

                        //增加积分
                        if($this->shopScoresetdata['orderscore']){
                            $update['score'] = $data['totalmoney']*$this->shopScoresetdata['orderscore'];
                            $addscore = $update['score'];
                        }else{
                            $update['score'] = 0;
                        }
                        M('Shop_users')->where(array("token"=>$this->token,"openid"=>$this->openid))->setInc('score',$addscore);

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
        $data=M("Shop_collectunion")->field("tp_shop_collectunion.id as cid,tp_shopunion.id,tp_shopunion.seng,tp_shopunion.si,tp_shopunion.xian,tp_shopunion.cname,tp_shopunion.des,tp_shopunion.pic,tp_shopunion.long,tp_shopunion.lat,tp_shopunion.address")->join("join tp_shopunion on tp_shopunion.id=tp_shop_collectunion.uid")->where(array("tp_shop_collectunion.token"=>$this->token,'tp_shop_collectunion.openid'=>$this->openid))->select();
        $union=$this->getinfo(10000000000000,$data,$lat1,$long1);
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
    
    public function bindstaff(){
        if(IS_POST){
            $iStaffUser = trim($_POST['staff_name']);
            $iStaffPwd = trim($_POST['staff_pwd']);
            if(M('Shop_staff')->where(array('staff_user'=>$iStaffUser,'staff_pwd'=>md5($iStaffPwd),'openid'=>''))->find()){
                if(M('Shop_staff')->where(array('staff_user'=>$iStaffUser,'staff_pwd'=>md5($iStaffPwd)))->save(array('openid'=>$this->openid))){
                    echo $this->encode(array('code'=>0,'msg'=>'绑定成功'));
                }else{
                    echo $this->encode(array('code'=>-1,'msg'=>'绑定失败'));
                }
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'账户名或者密码错误,绑定失败'));
            }
        }else{
            $this->display();
        }
    }
    
    /*活动*/
    public function activitise(){
        $sToken = $this->token;
        $sOpenid = $this->openid;
        $oActivitiseModel = M('Shop_activities');
        $oShopModel = M('Shopware');
        $iBranchid = $_GET['branch_id'];
        if(IS_AJAX){
            /*
             * 插入mainorder订单
             */
            $bIsorder = M('Mainorder')->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid,
                'order_type'=>1,
                'shopid'=>'|'.$_GET['branch_id'].'|'))
                ->find();
            if($bIsorder){
                $this->ajaxReturn(array("status"=>-4,"info"=>"您已经免费下过单了哦!"));exit;
            }
            $maindata['ordernumber'] = $this->getSn();
            $maindata['totalnum'] = 1;
            $maindata['totalmoney'] = 0;
            $maindata['buytime'] = date("Y-m-d H:i:s");
            $maindata['buyname'] = $_POST['taname'];
            $maindata['tel'] = $_POST['tatel'];
            $maindata['address'] = $_POST['address'];
            $maindata['token'] = $this->token;
            $maindata['openid'] = $this->openid;
            $maindata['instruct'] = $_POST['myname'].'送给你的礼物，还说了一些话:'.$_POST['info']."  本次活动礼物由小二快快免费赠送.";
            $maindata['paytype'] = 3;
            $maindata['paystatus'] = 1;
            $maindata['order_type'] = 1;
            $maindata['shopid'] = "|".$_GET['branch_id']."|";

            if($iInsertId = M('Mainorder')->add($maindata)){
                $aSideorder['mid'] = $iInsertId;
                $aSideorder['sid'] = $_GET['branch_id'];
                $aSideorder['token'] = $this->token;
                $aSideorder['openid'] = $this->openid;
                $aSideorder['ordernumber'] = $maindata['ordernumber'];
                $aSideorder['buytime'] = $maindata['buytime'];
                $aSideorder['paystatus'] = 1;
                $aSideorder['buyname'] = $_POST['taname'];
                $aSideorder['tel'] = $_POST['tatel'];
                $aSideorder['address'] = $_POST['address'];
                $aSideorder['sendstatus'] = 0;
                $aSideorder['order_type'] = 1;
                if($iInsertSIdeId = M('Sideorder')->add($aSideorder)){
                    $goodsdata = M('Shopware')->where(array('id'=>$_POST['goodid']))->find();
                    $aDetail['sid'] = $iInsertSIdeId;
                    $aDetail['num'] = 1;
                    $aDetail['price'] = $goodsdata['price'];
                    $aDetail['gname'] = $goodsdata['name'];
                    $aDetail['gid'] = $goodsdata['id'];
                    $aDetail['total'] =  $goodsdata['price'];
                    if(M('Sidedetail')->add($aDetail)){
                        $this->ajaxReturn(array("status"=>0,"info"=>"成功!"));
                    }else{
                        $this->ajaxReturn(array("status"=>-3,"info"=>"系统繁忙!"));
                    }
                }else{
                    $this->ajaxReturn(array("status"=>-2,"info"=>"系统繁忙!"));
                }
            }else{
                $this->ajaxReturn(array("status"=>-1,"info"=>"系统繁忙!"));
            }
            /*
             * 插入sideorder订单
             */

            /*
             * 插入sidedetail订单
             */

        }else{
            $aInfo = $oActivitiseModel->where(array(
                'token'=>$sToken,
                'branch_id'=>$iBranchid,
                'id'=>$_GET['id']
            ))->find();
            $aProduct = $oShopModel->where(array(
                'id'=>$aInfo['poid'],
                'token'=>$sToken,
                'sid'=>$iBranchid
            ))->find();
            $aProducts = $oShopModel->where(array(
                'id'=>$aInfo['pwid'],
                'token'=>$sToken,
                'sid'=>$iBranchid
            ))->find();
            $this->assign(array(
                'activitise'=>$aInfo,
                'product'=>$aProduct,
                'products'=>$aProducts
            ));
            $this->display();
        }
    }

}
