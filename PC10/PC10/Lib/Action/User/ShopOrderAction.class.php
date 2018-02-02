<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * Tel:15889394741;
 * Notice:门店订单
 * Date: 2014/12/29
 * Time: 14:33
 */
class ShopOrderAction extends UserAction{
    public $token;
    public $branch_id;
    public $member_id;
    public function _initialize() {
        parent::_initialize();
        if(isset($_GET['token'])){
            $this->token=$this->_get("token");
        }else{
            exit("非法操作!");
        }
        $this->branch_id=$this->_get("branch_id");
        $this->member_id=$this->_get("member_id");
        if(isset($_GET['resour']) && $_GET['resour']!="index"){
            if($this->branch_id!=session("branch_id") && $this->member_id!=session("member_id")){
                exit("非法操作!");
            }
        }

        $orderdata = M('Sideorder')->where(array('token'=>$this->token,"sid"=>$this->branch_id))->order('buytime desc')->find();
        if($orderdata != null){
            $this->assign('lasttime',$orderdata['buytime']);
        }else{
            $this->assign('lasttime',date("Y-m-d H:i:s",time()));
        }

        $this->assign("member_id",$this->member_id);
        $this->assign("branch_id",$this->branch_id);
        $this->assign("token",$this->token);
    }

    //总店显示订单列表
    public function All()
    {
        if (isset($_GET['type'])) {
            $condition = array();
            $condition['token'] = $this->token;
            $condition['buyname'] = array('neq','');

            if(isset($_GET['paystatus'])){
                $condition['paystatus'] = array('eq',$_GET['paystatus']); 
            }else{
                $condition['paystatus'] = array('neq',-1);
            }

            if(isset($_GET['paytype'])){
                $condition['paytype'] = array('eq',$_GET['paytype']); 
            }else{
                $condition['paytype'] = array('neq',-1);
            }

            $type = $this->_get("type");
            //今日订单
            if($type==1){
                $time=strtotime(date("Y-m-d"));
                $start=date("Y-m-d H:i:s",$time);
                $end=date("Y-m-d H:i:s",$time+3600*24-1);
                $condition['buytime'] = array("between", array($start,$end));
                $today=1;
             //   $condition1['paystatus']=1;
            }
            //条件筛选订单
            if ($type == 4) {
               // p($_GET);die;
                /**
                 * 店铺
                 */
                if(isset($_GET['shopid'])&&$_GET['shopid'] != 0){
                    $condition['shopid'] = array('like','%'.$this->_get("shopid").'%');
                }
                /**
                 * 支付状态
                 */
                if(isset($_GET['paystatus'])&&$_GET['paystatus'] != 2){
                    $condition['paystatus'] = array('eq',$this->_get("paystatus"));
                }

                /**
                 * 付款方式
                 */
                if(isset($_GET['paytype'])&&$_GET['paytype'] != 0){
                    $condition['paytype'] = array('eq',$this->_get("paytype"));
                }

               //p($condition);die;

                if (isset($_GET['start']) && isset($_GET['end'])) {
                    $condition['buytime'] = array("between", array($this->_get("start"), $this->_get("end")));
                } elseif (isset($_GET['start'])) {
                    $condition['buytime'] = array("egt", $this->_get("start"));
                } elseif (isset($_GET['end'])) {
                    $condition['buytime'] = array("egt", $this->_get("end"));
                }

                if (isset($_GET['buyname'])) {
                    $condition['buyname'] = array('like','%'.$this->_get("buyname").'%');
                }
                if (isset($_GET['tel'])) {
                    $condition['tel'] = array('like','%'.$this->_get("tel").'%');
                }

                if (isset($_GET['ordernumber'])) {
                    $condition['ordernumber'] = array('like','%'.$this->_get("ordernumber").'%');
                }

                if (isset($_GET['shopmemberid']) && $_GET['shopmemberid'] != 0) {
                    $condition['shopmemberid'] = array('like','%|'.$this->_get("shopmemberid").'|%');
                }

            }
            if($this->_get("buyname") == '') {
                $condition['buyname'] = array('neq', '');
            }
           // $condition['paytype']=array('neq',0);

            $count = M("Mainorder")->field("id,ordernumber,buyname,tel,address,buytime,paystatus")->where($condition)->count();

            $page = new Page($count, 20);

            $orderinfo = M("Mainorder")->field("wm,id,ordernumber,buyname,tel,address,buytime,paystatus,totalmoney,paytype,sendstatus,noget_money,score,score_money,xianjin_b")->where($condition)->limit($page->firstRow . ',' . $page->listRows)->order('buytime desc')->select();
           //这里是导出数据不分页
            $orderinfo1 = M("Mainorder")->field("wm,id,ordernumber,buyname,tel,address,buytime,paystatus,totalmoney,paytype,sendstatus,noget_money,score,score_money,xianjin_b")->where($condition)->order('buytime desc')->select();
            //条件带上支付成功
           // p($condition);die;
            $condition1=$condition;
            //p($condition1);die;
            if(isset($today)){//这里是判断是否是今日订单
                $condition1['paystatus']=1;
                //得总的消费币消费
                $dyb_total=M('Mainorder')->where($condition1)->getField('sum(score_money)');
                //总的消费金额
                $f_totalmoney=M('Mainorder')->where($condition1)->getField('sum(totalmoney-score_money)');
                //余额支付总金额:
                $condition2=$condition1;
                if($_GET['paytype'] == 5 || !isset($_GET['paytype'])){
                    $condition2['paytype']=array('eq',5);  
                }else{
                   $condition2['paytype']=array('eq',-1);
                }
                $y_total=M('Mainorder')->where($condition2)->count();

                $y_totalmoney=M('Mainorder')->where($condition2)->getField('sum(totalmoney-score_money)');
                //微信支付总金额:
                $condition3=$condition1;
                if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
                   $condition3['paytype']=array('eq',1);
                }else{
                   $condition3['paytype']=array('eq',-1);
                }
                $w_total=M('Mainorder')->where($condition3)->count();
                $w_totalmoney=M('Mainorder')->where($condition3)->getField('sum(totalmoney-score_money)');
                //线下支付总金额:
                $condition4=$condition1;
                if($_GET['paytype'] == 3 || !isset($_GET['paytype'])){
                   $condition4['paytype']=array('eq',3);
                }else{
                   $condition4['paytype']=array('eq',-1);
                }
                $x_total=M('Mainorder')->where($condition4)->count();
                $x_totalmoney=M('Mainorder')->where($condition4)->getField('sum(totalmoney)');

                $condition5=$condition1;
                if(!isset($_GET['paytype'])){
                    $condition5['paytype']=array('neq',-1);
                }else{
                    $condition5['paytype']=$_GET['paytype'];
                }
                $wm_total = M('Mainorder')->where($condition5)->getField('sum(noget_money)');
            }else{

                //得总的消费币消费
                $dyb_total=M('Mainorder')->where($condition1)->getField('sum(score_money)');
                //总的消费金额
                $f_totalmoney=M('Mainorder')->where($condition1)->getField('sum(totalmoney-score_money)');
                //余额支付总金额:
                $condition2=$condition1;
                if($_GET['paytype'] == 5 || !isset($_GET['paytype']) ){
                   $condition2['paytype']=array('eq',5);
                }else{
                   $condition2['paytype']=array('eq',-1);
                }
                $y_total=M('Mainorder')->where($condition2)->count();
                $y_totalmoney=M('Mainorder')->where($condition2)->getField('sum(totalmoney-score_money)');
                //微信支付总金额:
                $condition3=$condition1;
                if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
                   $condition3['paytype']=array('eq',1);
                }else{
                   $condition3['paytype']=array('eq',-1);
                }
                $w_total=M('Mainorder')->where($condition3)->count();
                $w_totalmoney=M('Mainorder')->where($condition3)->getField('sum(totalmoney-score_money)');
                //线下支付总金额:
                $condition4=$condition1;
                if($_GET['paytype'] == 3 || !isset($_GET['paytype'])){
                   $condition4['paytype']=array('eq',3);
                }else{
                   $condition4['paytype']=array('eq',-1);
                }
                $x_total=M('Mainorder')->where($condition4)->count();
                $x_totalmoney=M('Mainorder')->where($condition4)->getField('sum(totalmoney)');

                $condition5=$condition1;
                if(!isset($_GET['paytype'])){
                    $condition5['paytype']=array('neq',-1);
                }else{
                    $condition5['paytype']=$_GET['paytype'];
                }
                $wm_total = M('Mainorder')->where($condition5)->getField('sum(noget_money)');
                
            }





        } else {



            $condition['buyname']=array('neq','');
         //   $condition['paytype']=array('in','1,2,3,4,5,6');
            $condition['token']=$this->token;
            if(isset($_GET['paystatus'])){
                $condition['paystatus'] = array('eq',$_GET['paystatus']); 
            }else{
                $condition['paystatus'] = array('neq',-1);
            }

            if(isset($_GET['paytype'])){
                $condition['paytype'] = array('eq',$_GET['paytype']); 
            }else{
                $condition['paytype'] = array('neq',-1);
            }
          //  $condition['paytype']=array('neq','1');
            //$condition['paystatus']=array('neq',0);





           /* $condition['_logic']="OR";


            $map['buyname']=array('neq','');
            $map['paytype']=array('eq','1');
            $map['paystatus']=array('neq',0);
            $map['token']=$this->token;
            $map['_complex']=$condition;*/

           /*$condition=array(array(
                'token'=>$this->token,
                'paytype'=>array('in','2,3,4,5,6')
            ),array(
                'paytype'=>array('eq',1),
                'paystatus'=>array('neq',0),
                'token'=>$this->token
            ));*/



            $count = M("Mainorder")->field("id,ordernumber,buyname,tel,address,buytime,paystatus")->where($condition)->count();
            /**
             * $condition['paytype']=array('eq',1)
             * $condition['paystatus']=array('eq',0);
             */
            $page = new Page($count, 20);

            $orderinfo = M("Mainorder")->field("wm,id,ordernumber,buyname,tel,address,buytime,paystatus,totalmoney,paytype,sendstatus,noget_money,score,score_money,xianjin_b")->where($condition)->limit($page->firstRow . ',' . $page->listRows)->order('buytime desc')->select();
            //这里是导出数据不分页
            $orderinfo1 = M("Mainorder")->field("wm,id,ordernumber,buyname,tel,address,buytime,paystatus,totalmoney,paytype,sendstatus,noget_money,score,score_money,xianjin_b")->where($condition)->order('buytime desc')->select();

            //条件带上支付成功
         //  echo M("Mainorder")->getLastSql();die;
            $condition1=$condition;
            //得总的消费币消费
            $dyb_total=M('Mainorder')->where($condition1)->getField('sum(score_money)');
            //总的消费金额
            $f_totalmoney=M('Mainorder')->where($condition1)->getField('sum(totalmoney-score_money)');
            //余额支付总金额:
            $condition2=$condition1;
            if($_GET['paytype'] == 5 || !isset($_GET['paytype']) ){
               $condition2['paytype']=array('eq',5);
            }else{
                   $condition2['paytype']=array('eq',-1);
            }
            $y_totalmoney=M('Mainorder')->where($condition2)->getField('sum(totalmoney-score_money)');
            $y_total=M('Mainorder')->where($condition2)->count();
            $this->assign('y_total',$y_total);
            //微信支付总金额:
            $condition3=$condition1;
            if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
              $condition3['paytype']=array('eq',1);
            }else{
                   $condition3['paytype']=array('eq',-1);
            }

            $w_totalmoney=M('Mainorder')->where($condition3)->getField('sum(totalmoney-score_money)');
            $w_total=M('Mainorder')->where($condition3)->count();
            $this->assign('w_total',$w_total);
            //线下支付总金额:
            $condition4=$condition1;
            if($_GET['paytype'] == 3 || !isset($_GET['paytype'])){
               $condition4['paytype']=array('eq',3);
            }else{
               $condition4['paytype']=array('eq',-1);
            }

            $condition5=$condition1;
            if(!isset($_GET['paytype'])){
                $condition5['paytype']=array('neq',-1);
            }else{
                $condition5['paytype']=$_GET['paytype'];
            }
            $wm_total = M('Mainorder')->where($condition5)->getField('sum(noget_money)');

            $x_totalmoney=M('Mainorder')->where($condition4)->getField('sum(totalmoney)');
            $x_total=M('Mainorder')->where($condition4)->count();

        }



        foreach ($orderinfo as $k => $v) {
            $orderinfo[$k]['diff'] = $this->diff($v['buytime']);
        }

        $totalorder = M("Mainorder")->field("id")->where(array("token" => $this->token,'paystatus'=>array('neq',-1)))->count();
        $payed = M("Mainorder")->field("id")->where(array("token" => $this->token, "paystatus" => 1))->count();
        $totalmoney = M("Mainorder")->field("id")->where(array("token" => $this->token, "paystatus" => 1))->sum("totalmoney");
        $buytotal = M("Mainorder")->field("id")->where(array("token" => $this->token, "paystatus" => 1))->count("distinct openid");
        if ($totalorder) {
            $buyrate = (number_format($payed / $totalorder, 2) * 100) . "%";
        } else {
            $buyrate = 0;
        }
        if ($buytotal) {
            $singlerate = $payed / $buytotal;
        } else {
            $singlerate = 0;
        }

        /*
         * 运营商数据
         */
        $shopmember = M('Shopmember')->where(array('token'=>$this->token))->select();
        /**
         * 店铺列表
         */
        $shop_d= M('Shop')->where(array('token'=>$this->token))->select();


        $this->assign("totalorder", $totalorder);
        $this->assign("buyrate", $buyrate);
        $this->assign("totalmoney", $totalmoney);
        $this->assign("singlerate", $singlerate);
        $this->assign("list", $orderinfo);
        $this->assign("shopmember", $shopmember);
        $this->assign("shop_d", $shop_d);
        $this->assign("dyb_total", $dyb_total);
        $this->assign("f_totalmoney", $f_totalmoney);
        $this->assign("count", $count);
        $this->assign("y_totalmoney", $y_totalmoney);
        $this->assign("w_totalmoney", $w_totalmoney);
        $this->assign("x_totalmoney", $x_totalmoney);
        $this->assign("page", $page->show());
        $this->assign("start",$_GET['start']);
        $this->assign("end", $_GET['end']);
        $this->assign("buyname", $_GET['buyname']);
        $this->assign("tel", $_GET['tel']);
        $this->assign('ordernumber', $_GET['ordernumber']);
        $this->assign('shopmemberid', $_GET['shopmemberid']);
        $this->assign('shopid', $_GET['shopid']);
        $this->assign('paystatus', isset($_GET['paystatus'])?$_GET['paystatus']:2);
        $this->assign('paytype', isset($_GET['paytype'])?$_GET['paytype']:0);
        $this->assign("mtype", 1);
        session('a',1);
      //  p($orderinfo);die;
        session('export_order',$orderinfo1);//把数据存到session里用来打印咯
        
       //echo $w_total;die;
        $this->assign('x_total',$x_total);
        $this->assign('y_total',$y_total);
        $this->assign('w_total',$w_total);
        $this->assign('wm_total',$wm_total);
//        p($orderinfo);die;
        $this->display("All");
    }


    //总店订单详情
    public function OrderDetail(){
        if(isset($_GET['id'])){
            $mid=$this->_get("id","intval");
        }else{
            exit("非法操作!");
        }
        $count=M("Sideorder")->field("id,ordernumber,buytime")->where(array("token"=>$this->token,"mid"=>$mid))->count();
        $page=new Page($count,2);
        $sideinfo=M("Sideorder")->join("join tp_shop on tp_shop.id=tp_sideorder.sid join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->field("tp_mainorder.instruct,tp_sideorder.id,tp_sideorder.ordernumber,tp_sideorder.buytime,tp_shop.username")->where(array("tp_sideorder.token"=>$this->token,"tp_sideorder.mid"=>$mid))->limit($page->firstRow.','.$page->listRows)->select();
        foreach($sideinfo as $k=>$v){
            $totalnum=0;
            $totalprice=0;
            $detail=M("Sidedetail")->where(array("sid"=>$v['id']))->select();
            foreach($detail as $va){
                $totalnum+=$va['num'];
                $totalprice+=$va['total'];
            }
            $sideinfo[$k]['detail']=$detail;
            $sideinfo[$k]['totalnum']=$totalnum;
            $sideinfo[$k]['totalprice']=$totalprice;
        }
        $this->assign("page",$page->show());
        $this->assign("sideinfo",$sideinfo);
        $this->display("AllDetail");
    }

    //渠道管理员订单列表
    public function MemberAll(){
        if (isset($_GET['type'])) {
            $condition = array();
            $condition['tp_sideorder.token'] = $this->token;
            $condition['tp_shop.mid']=$this->member_id;
            $type = $this->_get("type");
            //今日订单
            if($type==1){
                $time=strtotime(date("Y-m-d"));
                $start=date("Y-m-d H:i:s",$time);
                $end=date("Y-m-d H:i:s",$time+3600*24-1);
                $condition['tp_sideorder.buytime'] = array("between", array($start,$end));
            }
            //条件筛选订单
            if ($type == 4) {
                if (isset($_GET['start']) && isset($_GET['end'])) {
                    $condition['tp_sideorder.buytime'] = array("between", array($this->_get("start"), $this->_get("end")));
                } elseif (isset($_GET['start'])) {
                    $condition['tp_sideorder.buytime'] = array("egt", $this->_get("start"));
                } elseif (isset($_GET['end'])) {
                    $condition['tp_sideorder.buytime'] = array("egt", $this->_get("end"));
                }

                if (isset($_GET['buyname'])) {
                    $condition['tp_mainorder.buyname'] = $this->_get("buyname");
                }
                if (isset($_GET['tel'])) {
                    $condition['tp_mainorder.tel'] = $this->_get("tel");
                }

                if (isset($_GET['ordernumber'])) {
                    $condition['tp_sideorder.ordernumber'] = $this->_get("ordernumber");
                }

            }

            $condition['tp_mainorder.buyname']=array('neq','');

            $count = M("Sideorder")->join("join tp_shop on tp_sideorder.sid=tp_shop.id join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->where($condition)->count();

            $page = new Page($count, 30);

            $orderinfo=M("Sideorder")->field("tp_shop.username,tp_mainorder.buyname,tp_mainorder.paytype,tp_mainorder.tel,tp_mainorder.address,tp_sideorder.id,tp_sideorder.mid,tp_sideorder.ordernumber,tp_sideorder.sendstatus,tp_sideorder.paystatus,tp_sideorder.buytime")->join("join tp_shop on tp_sideorder.sid=tp_shop.id join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->where($condition)->limit($page->firstRow . ',' . $page->listRows)->order('tp_sideorder.buytime desc')->select();

        } else {

            $condition=array();

            $condition['tp_sideorder.token']=$this->token;

            $condition['tp_shop.mid']=$this->member_id;

            $count=M("Sideorder")->join("join tp_shop on tp_sideorder.sid=tp_shop.id")->where($condition)->count();

            $page=new Page($count,30);

            $orderinfo=M("Sideorder")->field("tp_shop.username,tp_mainorder.buyname,tp_mainorder.paytype,tp_mainorder.tel,tp_mainorder.address,tp_sideorder.id,tp_sideorder.mid,tp_sideorder.ordernumber,tp_sideorder.sendstatus,tp_sideorder.paystatus,tp_sideorder.buytime")->join("join tp_shop on tp_sideorder.sid=tp_shop.id join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->where($condition)->limit($page->firstRow . ',' . $page->listRows)->order('tp_sideorder.buytime desc')->select();

        }
        foreach($orderinfo as $key=>$val){
            if(M('Mainorder')->where(array('token'=>$this->token,'id'=>$val['mid'],'buyname'=>array('neq','')))->find()){
                continue;
            }else{
                unset($orderinfo[$key]);
            }
        }


        foreach($orderinfo as $k=>$v){
            $totalnum=0;
            $totalprice=0;
            $orderinfo[$k]['diff']=$this->diff($v['buytime']);
            $detail=M("Sidedetail")->field("num,total,gname")->where(array("sid"=>$v['id']))->select();
            foreach($detail as $va){
                $totalnum+=$va['num'];
                $totalprice+=$va['total'];
            }
            $orderinfo[$k]['detail']=$detail;
            $orderinfo[$k]['totalnum']=$totalnum;
            $orderinfo[$k]['totalprice']=$totalprice;
        }
        $totalorder = M("Sideorder")->join("join tp_shop on tp_shop.id=tp_sideorder.sid")->where(array("tp_sideorder.token"=>$this->token,"tp_shop.mid"=>$this->member_id))->count();
        $payed = M("Sideorder")->join("join tp_shop on tp_shop.id=tp_sideorder.sid")->where(array("tp_sideorder.token"=>$this->token,"tp_sideorder.paystatus"=>1,"tp_shop.mid"=>$this->member_id))->count();
        $totalmoney = M("Sideorder")->join("join tp_shop on tp_shop.id=tp_sideorder.mid join tp_sidedetail on tp_sidedetail.sid=tp_sideorder.id")->where(array("tp_sideorder.token"=>$this->token,"tp_shop.mid"=>$this->member_id,"tp_sideorder.paystatus"=>1))->sum("tp_sidedetail.total");
        if(!$totalmoney){
            $totalmoney=0;
        }
        $buytotal = M("Sideorder")->join("join tp_shop on tp_sideorder.sid=tp_shop.id")->where(array("tp_sideorder.token"=>$this->token,"tp_shop.mid"=>$this->member_id,"tp_sideorder.paystatus"=>1))->count("distinct openid");
        if ($totalorder) {
            $buyrate = (number_format($payed / $totalorder, 2) * 100) . "%";
        } else {
            $buyrate = 0;
        }
        if ($buytotal) {
            $singlerate = $totalorder / $buytotal;
        } else {
            $singlerate = 0;
        }
        $this->assign("totalorder", $totalorder);
        $this->assign("buyrate", $buyrate);
        $this->assign("totalmoney", $totalmoney);
        $this->assign("singlerate", $singlerate);
        $this->assign("list", $orderinfo);
        $this->assign("page", $page->show());
        $this->assign("mtype", 1);
        $this->display();
    }


//    //渠道管理员订单详情
//    public function MemberDetail(){
//        if(isset($_GET['id'])){
//            $mid=$this->_get("id","intval");
//        }else{
//            exit("非法操作!");
//        }
//        $count=M("Sideorder")->field("id,ordernumber,buytime")->where(array("token"=>$this->token,"mid"=>$mid))->count();
//        $page=new Page($count,2);
//        $sideinfo=M("Sideorder")->join("join tp_shop on tp_shop.id=tp_sideorder.sid")->field("tp_sideorder.id,tp_sideorder.ordernumber,tp_sideorder.buytime,tp_shop.username")->where(array("tp_sideorder.token"=>$this->token,"tp_sideorder.mid"=>$mid))->limit($page->firstRow.','.$page->listRows)->select();
//        foreach($sideinfo as $k=>$v){
//            $totalnum=0;
//            $totalprice=0;
//            $detail=M("Sidedetail")->where(array("sid"=>$v['id']))->select();
//            foreach($detail as $va){
//                $totalnum+=$va['num'];
//                $totalprice+=$va['total'];
//            }
//            $sideinfo[$k]['detail']=$detail;
//            $sideinfo[$k]['totalnum']=$totalnum;
//            $sideinfo[$k]['totalprice']=$totalprice;
//        }
//        $this->assign("page",$page->show());
//        $this->assign("sideinfo",$sideinfo);
//        $this->display();
//    }

    //分店订单总列表与今日订单列表
    public function BranchAll(){
        if(isset($_GET['type'])){
            $type=$this->_get("type","intval");
            $condition=array();
            $condition['tp_sideorder.token']=$this->token;
            $condition['tp_sideorder.sid']=$this->branch_id;



            //今日订单
            if($type==1){
                $time=strtotime(date("Y-m-d"));
                $start=date("Y-m-d H:i:s",$time);
                $end=date("Y-m-d H:i:s",$time+3600*24-1);
                $condition['tp_sideorder.buytime'] = array("between", array($start,$end));
                $today=1;//设定一个变量，来判断是否为今日 订单
            }
            //未确认订单
            if($type==2){
                $condition['tp_sideorder.sendstatus']=0;
            }
            //已发货订单
            if($type==3){
                $condition['tp_sideorder.sendstatus']=1;
            }
            if($type==4){
                $condition['tp_sideorder.sendstatus']=2;
            }

            if($type==5){
                /**
                 * 支付状态
                 */

                if(isset($_GET['paystatus'])&&$_GET['paystatus'] != 2){
                    $condition['tp_mainorder.paystatus'] = array('eq',$this->_get("paystatus"));
                }
                /**
                 * 付款方式
                 */
                if(isset($_GET['paytype'])&&$_GET['paytype'] != 0){
                    $condition['tp_mainorder.paytype'] = array('eq',$this->_get("paytype"));
                }
                if (isset($_GET['start']) && isset($_GET['end'])) {
                    $condition['tp_sideorder.buytime'] = array("between", array($this->_get("start"), $this->_get("end")));
                } elseif (isset($_GET['start'])) {
                    $condition['tp_sideorder.buytime'] = array("egt", $this->_get("start"));
                } elseif (isset($_GET['end'])) {
                    $condition['tp_sideorder.buytime'] = array("egt", $this->_get("end"));
                }

                if (isset($_GET['buyname'])) {
                    $condition['tp_mainorder.buyname'] =  array('like','%'.$this->_get("buyname").'%');
                }
                if (isset($_GET['tel'])) {
                    $condition['tp_mainorder.tel'] =  array('like','%'.$this->_get("tel").'%');
                }
                if (isset($_GET['address'])) {
                    $condition['tp_mainorder.address'] =  array('like','%'.$this->_get("address").'%');
                }


                if (isset($_GET['ordernumber'])) {
                    $condition['tp_sideorder.ordernumber'] = array('like','%'.$this->_get("ordernumber").'%');
                }
            }
            $condition['tp_sideorder.buyname']=array('neq','');

            $count=M("Sideorder")->field("tp_mainorder.buyname,tp_mainorder.tel,tp_mainorder.address,tp_sideorder.id,tp_sideorder.ordernumber,tp_sideorder.sendstatus,tp_sideorder.buytime,tp_sideorder.paystatus,tp_mainorder.xianjin_b")->join("join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->where($condition)->order('tp_sideorder.buytime desc ')->count();
            $page=new Page($count,30);

            $orderinfo=M("Sideorder")->field("tp_mainorder.wm,tp_mainorder.is_print,tp_mainorder.buyname,tp_mainorder.tel,tp_mainorder.paytype,tp_mainorder.instruct,tp_mainorder.address,tp_mainorder.noget_money,tp_mainorder.score,tp_mainorder.score_money,tp_sideorder.id,tp_sideorder.mid,tp_sideorder.mid,tp_sideorder.ordernumber,tp_sideorder.sendstatus,tp_sideorder.buytime,tp_sideorder.paystatus,tp_mainorder.xianjin_b")->join("join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->where($condition)->order('tp_sideorder.buytime desc')->select();

            //条件带上支付成功

            if(isset($today)){//今日订单在这里算
                $time=strtotime(date("Y-m-d"));
                $start=date("Y-m-d H:i:s",$time);
                $end=date("Y-m-d H:i:s",$time+3600*24-1);
                $condition1['buytime'] = array("between", array($start,$end));
                $condition1['paystatus']=1;
                $condition1['buyname'] = array('neq','');
                $condition1['token']=$this->token;
                $condition1['shopid']='|'.$this->branch_id.'|';
                //得总的消费币消费
                $dyb_total=M('Mainorder')->where($condition1)->getField('sum(score_money)');

                //总的消费金额
                $f_totalmoney=M('Mainorder')->where($condition1)->getField('sum(totalmoney-score_money)');
                //余额支付总金额:
                $condition2=$condition1;
                if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
                   $condition2['paytype']=array('eq',5);
                }else{
                   $condition2['paytype']=array('eq',-1);
                }
                $y_totalmoney=M('Mainorder')->where($condition2)->getField('sum(totalmoney-score_money)');
                //微信支付总金额:
                $condition3=$condition1;
                if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
                   $condition3['paytype']=array('eq',1);
                }else{
                   $condition3['paytype']=array('eq',-1);
                }
                $w_totalmoney=M('Mainorder')->where($condition3)->getField('sum(totalmoney-score_money)');
                //线下支付总金额:
                $condition4=$condition1;
                if($_GET['paytype'] == 3 || !isset($_GET['paytype'])){
                   $condition4['paytype']=array('eq',3);
                }else{
                   $condition4['paytype']=array('eq',-1);
                }
                $x_totalmoney=M('Mainorder')->where($condition4)->getField('sum(totalmoney)');
              //  echo M('Mainorder')->getLastSql();

                $condition5=$condition1;
                if(!isset($_GET['paytype'])){
                    $condition5['paytype']=array('neq',-1);
                }else{
                    $condition5['paytype']=$_GET['paytype'];
                }
                $wm_total=M('Mainorder')->where($condition5)->getField('sum(noget_money)');

            }else{
                if (isset($_GET['start']) && isset($_GET['end'])) {
                    $condition1['buytime'] = array("between", array($this->_get("start"), $this->_get("end")));
                } elseif (isset($_GET['start'])) {
                    $condition1['buytime'] = array("egt", $this->_get("start"));
                } elseif (isset($_GET['end'])) {
                    $condition1['buytime'] = array("egt", $this->_get("end"));
                }

                if (isset($_GET['buyname'])) {
                    $condition1['buyname'] =  array('like','%'.$this->_get("buyname").'%');
                }
                if (isset($_GET['tel'])) {
                    $condition1['tel'] =  array('like','%'.$this->_get("tel").'%');
                }
                if (isset($_GET['address'])) {
                    $condition1['address'] =  array('like','%'.$this->_get("address").'%');
                }


                if (isset($_GET['ordernumber'])) {
                    $condition1['ordernumber'] = array('like','%'.$this->_get("ordernumber").'%');
                }




                if(isset($_GET['paystatus'])){
                    $condition1['paystatus'] = array('eq',$_GET['paystatus']); 
                }else{
                    $condition1['paystatus'] = array('neq',-1);
                }

                if(isset($_GET['paytype'])){
                    $condition1['paytype'] = array('eq',$_GET['paytype']); 
                }else{
                    $condition1['paytype'] = array('neq',-1);
                }



                $condition1['token']=$this->token;
                $condition1['shopid']='|'.$this->branch_id.'|';
                $condition1['buyname'] = array('neq','');
                //p($condition1);
                $condition1['paystatus']=1;
                //得总的消费币消费
                $dyb_total=M('Mainorder')->where($condition1)->getField('sum(score_money)');
                //总的消费金额
                $f_totalmoney=M('Mainorder')->where($condition1)->getField('sum(totalmoney-score_money)');
                //余额支付总金额:
                $condition2=$condition1;
                if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
                   $condition2['paytype']=array('eq',5);
                }else{
                   $condition2['paytype']=array('eq',-1);
                }
                $y_totalmoney=M('Mainorder')->where($condition2)->getField('sum(totalmoney-score_money)');
                //微信支付总金额:
                $condition3=$condition1;
                if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
                   $condition3['paytype']=array('eq',1);
                }else{
                   $condition3['paytype']=array('eq',-1);
                }
                $w_totalmoney=M('Mainorder')->where($condition3)->getField('sum(totalmoney-score_money)');
                //线下支付总金额:
                $condition4=$condition1;
                if($_GET['paytype'] == 3 || !isset($_GET['paytype'])){
                   $condition4['paytype']=array('eq',3);
                }else{
                   $condition4['paytype']=array('eq',-1);
                }
                //线下支付总金额

                $x_totalmoney=M('Mainorder')->where($condition4)->getField('sum(totalmoney)');
                //echo M('Mainorder')->getLastSql();
             //   $x_totalmoney='45';

                $condition5=$condition1;
                if(!isset($_GET['paytype'])){
                    $condition5['paytype']=array('neq',-1);
                }else{
                    $condition5['paytype']=$_GET['paytype'];
                }
                $wm_total=M('Mainorder')->where($condition5)->getField('sum(noget_money)');
                
            }

        }else{//不是搜索走这里


            $count=M("Sideorder")->field("tp_mainorder.buyname,tp_mainorder.tel,tp_mainorder.address,tp_sideorder.id,tp_sideorder.ordernumber,tp_sideorder.sendstatus,tp_sideorder.buytime,tp_sideorder.paystatus")->join("join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->where(array("tp_sideorder.token"=>$this->token,"tp_sideorder.sid"=>$this->branch_id,'tp_sideorder.buyname'=>array('neq','')))->count();

            $page=new Page($count,30);

            $orderinfo=M("Sideorder")->field("tp_mainorder.paystatus,tp_mainorder.is_print,tp_mainorder.is_print,tp_mainorder.wm,tp_mainorder.paytype,tp_mainorder.instruct,tp_mainorder.buyname,tp_mainorder.tel,tp_mainorder.address,tp_mainorder.noget_money,tp_mainorder.score,tp_mainorder.score_money,tp_sideorder.id,tp_sideorder.mid,tp_sideorder.ordernumber,tp_sideorder.sendstatus,tp_sideorder.buytime,tp_sideorder.sendstatus,tp_sideorder.paystatus,tp_mainorder.xianjin_b")->join("join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->where(array("tp_sideorder.token"=>$this->token,"tp_sideorder.sid"=>$this->branch_id,'tp_sideorder.buyname'=>array('neq','')))->limit($page->firstRow.','.$page->listRows)->order('tp_sideorder.buytime desc')->select();

            $condition1['token']=$this->token;
            $condition1['shopid']='|'.$this->branch_id.'|';
            $condition1['buyname'] = array('neq','');
            if(isset($_GET['paystatus'])){
                $condition1['paystatus'] = array('eq',$_GET['paystatus']); 
            }else{
                $condition1['paystatus'] = array('neq',-1);
            }

            if(isset($_GET['paytype'])){
                $condition1['paytype'] = array('eq',$_GET['paytype']); 
            }else{
                $condition1['paytype'] = array('neq',-1);
            }
            //p($condition1);die;
            //得总的消费币消费
            $dyb_total=M('Mainorder')->where($condition1)->getField('sum(score_money)');

            //总的消费金额
            $condition1['paystatus']=1;
            $f_totalmoney=M('Mainorder')->where($condition1)->getField('sum(totalmoney-score_money)');
           // echo M('Mainorder')->getLastSql();

            //余额支付总金额:
            $condition2=$condition1;
            if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
               $condition2['paytype']=array('eq',5);
            }else{
               $condition2['paytype']=array('eq',-1);
            }
            $y_totalmoney=M('Mainorder')->where($condition2)->getField('sum(totalmoney-score_money)');
            //微信支付总金额:
            $condition3=$condition1;
            if($_GET['paytype'] == 1 || !isset($_GET['paytype'])){
               $condition3['paytype']=array('eq',1);
            }else{
               $condition3['paytype']=array('eq',-1);
            }
            $w_totalmoney=M('Mainorder')->where($condition3)->getField('sum(totalmoney-score_money)');
            //线下支付总金额:
            $condition4=$condition1;
            if($_GET['paytype'] == 3 || !isset($_GET['paytype'])){
               $condition4['paytype']=array('eq',3);
            }else{
               $condition4['paytype']=array('eq',-1);
            }
            //所有订单线下支付总金额
            $condition4['paystatus']=1;
            $x_totalmoney=M('Mainorder')->where($condition4)->getField('sum(totalmoney)');


            $condition5=$condition1;
            if(!isset($_GET['paytype'])){
                $condition5['paytype']=array('neq',-1);
            }else{
                $condition5['paytype']=$_GET['paytype'];
            }
            $wm_total=M('Mainorder')->where($condition5)->getField('sum(noget_money)');


        }

        /**
         * 这里改导出数据
         */
       // $orderinfo3=$orderinfo;
       // $orderinfo3
        foreach($orderinfo as $k=>$v){
            $totalnum=0;
            $totalprice=0;
            $orderinfo[$k]['diff']=$this->diff($v['buytime']);
            /**
             * 这里sid是怎么回事
             */
            $detail=M("Sidedetail")->field("num,total,gname")->where(array("sid"=>$v['id']))->select();
            foreach($detail as $va){
                $totalnum+=$va['num'];
                $totalprice+=$va['total'];
            }
            $orderinfo[$k]['detail']=$detail;
            $orderinfo[$k]['totalnum']=$totalnum;
            $orderinfo[$k]['totalprice']=$totalprice;
        }

        $totalorder=M("Sideorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,'buyname'=>array('neq','')))->count();
        $payed=M("Sideorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"paystatus"=>1,'buyname'=>array('neq','')))->count();
        $payinfo=M("Sideorder")->field("tp_sidedetail.total")->join("tp_sidedetail on tp_sidedetail.sid=tp_sideorder.id")->where(array("tp_sideorder.token"=>$this->token,"tp_sideorder.sid"=>$this->branch_id,"tp_sideorder.paystatus"=>1,'tp_sideorder.buyname'=>array('neq','')))->select();
        $totalmoney=0;
        foreach($payinfo as $v){
            $totalmoney+=$v['total'];
        }
        $buytotal = M("Sideorder")->where(array("token" => $this->token,"branch_id"=>$this->branch_id,"paystatus" => 1,'buyname'=>array('neq','')))->count("distinct openid");
        $unsure=M("Sideorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"sendstatus"=>0,'buyname'=>array('neq','')))->count();
        $send=M("Sideorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"sendstatus"=>1,'buyname'=>array('neq','')))->count();
        $accept=M("Sideorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"sendstatus"=>2,'buyname'=>array('neq','')))->count();
        if ($totalorder) {
            $buyrate = (number_format($payed / $totalorder, 2) * 100) . "%";
        } else {
            $buyrate = 0;
        }
        if ($buytotal) {
            $singlerate = $payed / $buytotal;
        } else {
            $singlerate = 0;
        }
        $this->assign("totalorder",$totalorder);
        $this->assign("buyrate",$buyrate);
        $this->assign("totalmoney",$totalmoney);
        $this->assign("singlerate",round($singlerate,2));
        $this->assign("unsure",$unsure);
        $this->assign("send",$send);
        $this->assign("accept",$accept);
        $this->assign("list",$orderinfo);
        $this->assign("page",$page->show());

        $this->assign("dyb_total", $dyb_total);
        $this->assign("f_totalmoney", $f_totalmoney);
        $this->assign("count", $count);
        $this->assign("y_totalmoney", $y_totalmoney);
        $this->assign("w_totalmoney", $w_totalmoney);
        $this->assign("x_totalmoney", $x_totalmoney);

        $this->assign("start",$_GET['start']);
        $this->assign("end", $_GET['end']);
        $this->assign("address", $_GET['address']);
        $this->assign("buyname", $_GET['buyname']);
        $this->assign("tel", $_GET['tel']);
        $this->assign('ordernumber', $_GET['ordernumber']);
        $this->assign('paytype', isset($_GET['paytype'])?$_GET['paytype']:0);
        $this->assign('paystatus', isset($_GET['paystatus'])?$_GET['paystatus']:2);
        $this->assign("mtype",1);
        $this->assign("type",$type);
        $x_total=0;
        $w_total=0;
        $y_total=0;
        foreach($orderinfo as $v){
            if($v['paytype']==1){
                $w_total++;
            }elseif($v['paytype']==3){
                $x_total++;
            }elseif($v['paytype']==5){
                $y_total++;
            }else{

            }
        }
        //echo $w_total;die;

        session('export_order',$orderinfo);
        $this->assign('x_total',$x_total);
        $this->assign('y_total',$y_total);
        $this->assign('w_total',$w_total);
        $this->assign('wm_total',$wm_total);
        $this->display();
    }

    //分店删除子订单
    public function delSon(){
        if(isset($_GET['id'])){
            $id=$this->_get("id","intval");
            $m=M("Sideorder")->field("mid")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$id))->find();
            $mid=$m['mid'];
            $detailInfo=M("Sidedetail")->where(array("sid"=>$id))->select();
            $totalnum=0;
            $totalprice=0;
            foreach($detailInfo as $v){
                //增加库存
                //改变了这里，取消订单不加库存了
                M('Shopware')->where(array('token'=>$this->token,'id'=>$v['gid']))->setInc('stock',$v['num']);
                $totalprice+=$v['total'];
                $totalnum+=$v['num'];
            }
            mysql_query("start transaction");
            mysql_query("begin");
            $orderdata = M("Mainorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$mid))->find();
            if(M("Sideorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$id))->save(array('paystatus'=>-1)) && M("Mainorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$mid))->setDec("totalnum",$totalnum) && M("Mainorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$mid))->setDec("totalmoney",$totalprice) && M("Mainorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$mid))->save(array('paystatus'=>-1))){
                /*
                 * 撤回积分
                 */
                M('Shop_users')->where(array("token"=>$this->token,"openid"=>$orderdata['openid']))->setDec('score',$orderdata['score']);
                mysql_query("commit");
                $this->success2("操作成功!");
            }else{
                mysql_query("rollback");
                $this->success2("操作失败!");
            }
        }else{
            exit("非法操作!");
        }
    }

    //分店删除子订单
    public function getSon(){
        if(isset($_GET['id'])){
            $id=$this->_get("id","intval");
            $m=M("Sideorder")->field("mid")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$id))->find();
            $mid=$m['mid'];
            $detailInfo=M("Sidedetail")->field("total,num")->where(array("sid"=>$id))->select();
            $totalnum=0;
            $totalprice=0;
            foreach($detailInfo as $v){
                $totalprice+=$v['total'];
                $totalnum+=$v['num'];
            }
            mysql_query("start transaction");
            mysql_query("begin");
            $orderdata = M("Mainorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$mid))->find();
            $shopScoresetdata = M('Shop_scoreset')->where(array('token'=>$this->token))->find();
            $addscore=$orderdata['score'];
            $subscore=($orderdata['score_money'])*$shopScoresetdata['moneyscore'];

            if(M("Sideorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$id))->save(array('sendstatus'=>2,'paystatus'=>1)) && M("Mainorder")->where(array("token"=>$this->token,"sid"=>$this->branch_id,"id"=>$mid))->save(array('sendstatus'=>2,'paystatus'=>1))){
                M('Shop_users')->where(array("token"=>$this->token,"openid"=>$orderdata['openid']))->setInc('score',$addscore);//新增积分
                M('Shop_users')->where(array("token"=>$this->token,"openid"=>$orderdata['openid']))->setDec('score',$subscore);//减掉织分
                mysql_query("commit");
                $this->success2("操作成功!");
            }else{
                mysql_query("rollback");
                $this->success2("操作失败!");
            }
        }else{
            exit("非法操作!");
        }
    }

    //发货与取消发货
    public function changeStatus(){
        if(isset($_GET['status'])){
            $status=$this->_get("status","intval");
            $orderinfo = M("Sideorder")->where(array("token"=>$this->token,"branch_id"=>$this->branch_id,"id"=>$this->_get("id","intval")))->find();
            if($status==1){
                mysql_query("start transaction");
                mysql_query("begin");
                $res1 = M("Sideorder")->where(array("token"=>$this->token,"branch_id"=>$this->branch_id,"id"=>$this->_get("id","intval")))->save(array("sendstatus"=>1));
                $res2 = M('Mainorder')->where(array('id'=>$orderinfo['mid']))->save(array('sendstatus'=>1));
                if($res1 && $res2){
                    mysql_query("commit");
                    $this->success2("操作成功!");
                }else{
                    mysql_query("rollback");
                    $this->error2("操作失败!");
                }
            }
            if($status==3){//3代表是批量发货
                $arr=explode('-',$_GET['id']);
                $arr=array_filter($arr);
                mysql_query("start transaction");
                mysql_query("begin");
                $res1 = M("Sideorder")->where(array("token"=>$this->token,"branch_id"=>$this->branch_id,"mid"=>array('in',$arr)))->save(array("sendstatus"=>1));
                $res2 = M('Mainorder')->where(array('id'=>array('in',$arr)))->save(array('sendstatus'=>1));
                if($res1 && $res2){
                    mysql_query("commit");
                    $this->success2("操作成功!");
                }else{
                    mysql_query("rollback");
                    $this->error2("操作失败!");
                }
            }
            if($status==4){//4代表是批量确认收货
                $arr=explode('-',$_GET['id']);
                $arr=array_filter($arr);
                mysql_query("start transaction");
                mysql_query("begin");
                $res1 = M("Sideorder")->where(array("token"=>$this->token,"branch_id"=>$this->branch_id,"mid"=>array('in',$arr)))->save(array("sendstatus"=>2));
                $res2 = M('Mainorder')->where(array('id'=>array('in',$arr)))->save(array('sendstatus'=>2));
                if($res1 && $res2){
                    mysql_query("commit");
                    $this->success2("操作成功!");
                }else{
                    mysql_query("rollback");
                    $this->error2("操作失败!");
                }
            }
            if($status==2){
                mysql_query("start transaction");
                mysql_query("begin");
                $res1 = M("Sideorder")->where(array("token"=>$this->token,"branch_id"=>$this->branch_id,"id"=>$this->_get("id","intval")))->save(array("sendstatus"=>0));
                $res2 = M('Mainorder')->where(array('id'=>$orderinfo['mid']))->save(array('sendstatus'=>0));
                if($res1 && $res2){
                    mysql_query("commit");
                    $this->success2("操作成功!");
                }else{
                    mysql_query("rollback");
                    $this->error2("操作失败!");
                }
            }
        }else{
            exit("非法操作!");
        }
    }

    //计算距离下单日期的时间
    public function diff($btime){
        $d1=strtotime($btime);
        $d2=time();
        $diff=abs($d1-$d2);
        $out='';
        $vals=array('天'=>'86400','时'=>'3600','分'=>'60','秒'=>'1');
        foreach($vals as $key=>$value){
            if($diff>=$value){
                $d=round($diff/$value);
                $diff%=$value;
                $out.=$d.$key;
            }
        }
        return $out;
    }

    //总店删除订单和管理员删除订单
    public function DelOrder(){
        $type=$this->_get("type","intval");//1为总店删除 2为管理员删除
        mysql_query("start transaction");
        mysql_query("begin");
        if(M("Mainorder")->where(array("id"=>$this->_get("id","intval")))->delete()){
            if(M("Sideorder")->where(array("mid"=>$this->_get("id")))->delete()){
                mysql_query("commit");
                if($type==1){
                    $this->success2("删除成功!",U("ShopOrder/All",array("token"=>$this->token)));
                }else{
                    $this->success2("删除成功!",U("ShopOrder/Member",array("token"=>$this->token,'member_id'=>$this->member_id)));
                }
            }else{
                mysql_query("rollback");
                if($type==1){
                    $this->success2("删除失败!",U("ShopOrder/All",array("token"=>$this->token)));
                }else{
                    $this->success2("删除失败!",U("ShopOrder/Member",array("token"=>$this->token,'member_id'=>$this->member_id)));
                }
            }
        }else{
            $this->error2("删除失败!");
        }
    }

    //总店退货或者管理员退货
    public function BackOrder(){
        $type=$this->_get("type","intval");
        if(M("Mainorder")->where(array("id"=>$this->_get("id","intval")))->save(array("status"=>3))){
            if($type==1){
                $this->success2("退货成功!",U("ShopOrder/All",array("token"=>$this->token)));
            }else{
                $this->success2("退货成功!",U("ShopOrder/Member",array("token"=>$this->token,'member_id'=>$this->member_id)));
            }
        }else{
            if($type==1){
                $this->success2("退货失败!",U("ShopOrder/All",array("token"=>$this->token)));
            }else{
                $this->success2("退货失败!",U("ShopOrder/Member",array("token"=>$this->token,'member_id'=>$this->member_id)));
            }
        }
    }

    //总店发货或者管理员发货
    public function SendOrder(){
        $type=$this->_get("type","intval");
        if(M("Mainorder")->where(array("id"=>$this->_get("id","intval")))->save(array("status"=>1))){
            if($type==1){
                $this->success2("订单状态修改成功!",U("ShopOrder/All",array("token"=>$this->token)));
            }else{
                $this->success2("订单状态修改成功!",U("ShopOrder/Member",array("token"=>$this->token,'member_id'=>$this->member_id)));
            }
        }else{
            if($type==1){
                $this->success2("订单状态修改失败!",U("ShopOrder/All",array("token"=>$this->token)));
            }else{
                $this->success2("订单状态修改失败!",U("ShopOrder/Member",array("token"=>$this->token,'member_id'=>$this->member_id)));
            }
        }
    }

    public function gettimeorder(){
        if(isset($_GET['lasttime'])){
            $where['token'] = $this->token;
            $where['sid'] = $this->branch_id;
            $where['buyname'] = array('neq','');
            $where['buytime'] = array('gt',urldecode($_GET['lasttime']));
            $neworder = M('Sideorder')->where($where)->order('buytime desc')->find();
            $unorders = M('Sideorder')->where($where)->order('buytime desc')->count();
            if($unorders){
                $this->ajaxReturn(array('code'=>0,'msg'=>'success','data'=>$unorders,'lasttime'=>$neworder['buytime']));
            }else{
                $this->ajaxReturn(array('code'=>0,'msg'=>'success','data'=>0));
            }
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'非法请求'));
        }
    }
    /**
     * 导出
     */
    public function export_order(){
        $data=session('export_order');
        //p($data);die;
       foreach($data as $k=>$v){
            if($v['wm']==0){
                $data[$k]['wm']='外卖';
            }
           if($v['wm']==1){
               $data[$k]['wm']='堂吃';
           }
           if($v['wm']==2){
               $data[$k]['wm']='打包';
           }
           if($v['paystatus']==0){
               $data[$k]['paystatus']='未付款';
           }
           if($v['paystatus']==1){
               $data[$k]['paystatus']='已付款';
           }
           if($v['paystatus']==-1){
               $data[$k]['paystatus']='已取消订单';
           }
           if($v['paytype']==1){
               $data[$k]['paytype']='微信支付';
           }
           if($v['paytype']==3){
               $data[$k]['paytype']='线下付款';
           }
           if($v['paytype']==5){
               $data[$k]['paytype']='余额支付';
           }
           if($v['sendstatus']==0){
               $data[$k]['sendstatus']='未发货';
           }
           if($v['sendstatus']==1){
               $data[$k]['sendstatus']='已发货';
           }
           if($v['sendstatus']==2){
               $data[$k]['sendstatus']='已收货';
           }

        }
        exportExcel($data,array('类型','id','订单号码','姓名','电话号码','地址','购买时间','是否支付','总共多少钱',
        '支付类型','是否送货','跑腿费','获得消费币','抵扣的金额'),'商品订单记录');

    }

    /**
     * 打印
     */
    public function is_print(){
        if(IS_POST){
            $id=$this->_post('id');
            $data['is_print']=1;
            //查询是不是新用户首次成功支付完成下单 对应商店id
          //  echo 88;die;
            $info=M('Mainorder')->field('id,token,openid,buytime,shopid')->find($id);
            if(M('Mainorder')->where(array('paystatus'=>1,'shopid'=>$info['shopid'],'token'=>$info['token'],'openid'=>$info['openid'],'id'=>array('neq',$id),'buytime'=>array('lt',$info['buytime'])))->find()){
               // echo 1;die;
                if(M('Mainorder')->where(array('id'=>$id))->save($data)){
                    echo json_encode(array('status'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }
            }else{//新用户
             //   echo 2;die;
                if(M('Mainorder')->where(array('id'=>$id))->save($data)){
                    echo json_encode(array('status'=>1,'new'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }
            }

        }

    }
}