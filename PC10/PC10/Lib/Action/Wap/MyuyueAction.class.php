<?php
/*
 * 预约
 */
class MyuyueAction extends VankeBaseAction{
    /*
     * Tpl Dir
     */
    private $Vanke;
    protected $_sTplBaseDir = 'Wap/default/vanke';

    protected function _initialize(){
        parent::_initialize();
        session('main', U('index'));
        /*
        //先跳转，url不允许存有openid信息
        if ($_GET['openid']) {
            unset($_SESSION['WxvankeUser']);
            unset($_SESSION['WxvankeBindUser']);
            //return $this->redirect(U('Wap/Vanke/index'));
        }

        //根据openid获取用户信息
        $this->Vanke = $Vanke = new Vanke();
        if(!$aVankeUser = $_SESSION['WxvankeUser']){
            $aVankeUser = $Vanke->getUserInfo($this->openid);
            if (200 != $aVankeUser['code'] || !$this->openid) {
                //失败
                //exit('获取用户信息失败');
                header('Location:'.sprintf('http://szm.vanke.com/shop/pay/checkwx.php?cfrom=%s', urlencode(U('Vanke/index', array('token' => $this->token), true,false,true))));
                return;

            }
            $_SESSION['WxvankeUser'] = $aVankeUser;
        };
        //unset($_SESSION['WxvankeBindUser']);
        if(!$aVankeBindUser = $_SESSION['WxvankeBindUser']){
            $aVankeBindUser = $Vanke->getVankeBindUser($this->openid);
            $aVankeBindUser = json_decode($aVankeBindUser, true);
            if (200 != $aVankeBindUser['code']) {
                //失败
                //exit('没有业主信息');
                header('Location:'.sprintf('http://szm.vanke.com/weixin/index.html?openid=%s&cfrom=%s',$this->openid, urlencode(U('Vanke/index', array('openid' => $this->openid, 'token' => $this->token), true,false,true))));
                return;
            }
            $_SESSION['WxvankeBindUser'] = $aVankeBindUser;
        };
        $this->assign('WxvankeBindUser', $aVankeBindUser['data']);
        $this->assign('WxvankeUser', $aVankeUser['data']);
        //业主信息
        $aUserInfo = M('Vanke_bind_user')->where(array(
            'token' => $this->token,
            'openid'=> $this->openid,
            'type'  => 1
        ))->find();
        if (!$aUserInfo && ACTION_NAME != 'bind_user') {//去绑定业主信息
            //return $this->redirect('');
            //exit('去绑定业主信息');
        }
        $this->assign('vankeUser', M('Vanke_user')->where(array(
            'token' => $this->token,
            'openid'=> $this->openid
        ))->find());
        $this->assign('bindUser', $aUserInfo);
        */

        $this->YuyueGoods       = D('Yuyue_goods');
        $this->YuyueGoodsInfo   = D('Yuyue_datenum');
        $this->cattype          = D('Yuyue_cattype');
        $this->yysetting        = D('Yuyue_setting');

        $this->date    = date('Y-m-d H:i:s');
        $this->assign(array(
            'token'     => $this->token,
            'openid'    => $this->openid
        ));
    }

    /*
     *  旅游首页
     */
    public function index()
    {
        //团购信息
        $page = max(1, (int)$_REQUEST['page']);
        $num  = 10;
        $start = ($page-1) * $num;
        $aExtra = array();
        if ($_REQUEST['cid']) {
            $aExtra = array('cid' => $_REQUEST['cid']);
        }
        foreach($aProduct = $this->YuyueGoods->where($aExtra + array(
            'token'         => $this->token
        ))->limit($start . ',' . $num)->select() as $k => $Item){
        };
        $this->assign('product', $aProduct);
        if(IS_AJAX || IS_POST){
            exit($this->fetch('./tpl/Wap/default/vanke/yy_page_index.html'));
        }else{
            //分类信息
            $this->assign('cattype', $this->cattype->where(array(
                'token' => $this->token
            ))->select());
            //分类的轮播图
            $setting = $this->yysetting->where(array(
                'token' => $this->token
            ))->find();
            $this->assign('imgs', explode(',', Arr::get($setting, 'imgs')));
            $this->UDisplay('yy_index');
        }
    }

    /*
     * 预约详情
     */
    public function yy_detail()
    {
        $id = $_REQUEST['id'];
        if (!$id) {
            return $this->redirect(U('index'));
        }
        $info = $this->YuyueGoods->where(array(
            'token' => $this->token,
            'id'    => $id
        ))->find();

        $month = self::getMonth();

        $calc  = new Calendar();
        Vendor('Group.Order');
        foreach(
            $aPraiseInfo=Order::getPraiseById(Order::ORDER_SOURCE_YUYUE, $id, 0, 3)
            as
            $k => $v
        ){
            $aPraiseInfo[$k]['info'] = json_decode($v['info'], true);
        };
        $this->assign(array(
            'info'      => $info,
            'imgs'      => explode(',', Arr::get($info, 'imgs')),
            'source'    => Order::ORDER_SOURCE_YUYUE,
            'id'         => $id,
            'praiseInfo' => $aPraiseInfo,
            'time_info' => $calc->showCalendar($this->YuyueGoodsInfo->where(array(
                'token'       => $this->token,
                'shequ'       => $this->rooms['project_item_name'],
                'month'       => $month,
                'product_id'  => $id
            ))->select(),$id, $this->token, $this->rooms)
        ));

        // 三种日期
        $baseUrl = '?g=Wap&m=Myuyue&a=yy_detail&id=';

        $last_y= date('Y', strtotime('+1 month'));
        $last_m= date('m', strtotime('+1 month'));
        $last_2y= date('Y', strtotime('+2 month'));
        $last_2m= date('m', strtotime('+2 month'));

        $last= $baseUrl .$_GET['id'].'&y='.$last_y.'&m1='.$last_m.'&type=2';
        $now=$baseUrl . $_GET['id'].  '&y='.date('Y').'&m1='.date('m').'&type=1';
        $last_2=$baseUrl . $_GET['id'].'&y='.$last_2y.'&m1='.$last_2m.'&type=3';
        $this->assign('last',$last);
        $this->assign('now',$now);
        $this->assign('last_2',$last_2);



        $this->UDisplay('yy_detail');
    }


    public static function getMonth()
    {
        $y   = $_REQUEST['y'] ? $_REQUEST['y'] : date('Y');
        $m1  = $_REQUEST['m1'] ? $_REQUEST['m1'] : date('m');
        return $y . '-' . $m1;
    }


    /*
     *  预约订单
     */
    public function yy_order()
    {
        $id     = $_REQUEST['id'];
        $month  = $_REQUEST['month'] == '-' ? date('Y-m') : $_REQUEST['month'] ;
        $day    = $_REQUEST['day'];

        //查商品
        $Product = $this->YuyueGoods->where(array(
            'id'    => $id
        ))->find();
        if (!$Product) {
            return $this->redirect(U('index'));
        }

        Vendor('Group.Yhj');
        $Yhj = new Yhj($this->token, $this->openid);
        //$Yhj->checkYhj($this->rooms, $Product['price']);

        $rooms = $this->rooms;
        $project_item_name = Arr::get($rooms, 'project_item_name');
        $delivery_date     = date('Y-m-d H:i:s', strtotime(Arr::get($rooms, 'delivery_date')));
        $UniqKey           = md5($project_item_name . $delivery_date);

        $url = U('Myuyue/yy_order',null, true, false, true);
        $this->assign(array(
            'id'        => $id,
            'month'     => $month,
            'day'       => $day,
            'product'   => $Product,
            'url'       => $url,
            'jssdk'     => $this->jssdk($url),
            'month'     => $month,
            'yhj'       => $Yhj->select(0, 0, 15, array(
                'get_from'      => Yhj::FROM_SHEQU,
                'unique_key'    => $UniqKey
            )),
            'day'       => $day,
            'shequ'     => M('Yuyue_shequ')->where(array(
                'token' => $this->token
            ))->select()
        ));
        $this->UDisplay('yy_order');
    }

    /*
     *
     */
    function sendYhj(){
        Vendor('Group.Yhj');
        $Yhj = new Yhj($this->token, $this->openid);
        $iErr = null;
        if($Yhj->checkYhj($this->rooms, $_REQUEST['price'], $iErr)){//成功
            $this->jret(0, '', array('finished' => 1));
        }else{//失败
            if($iErr == 1001){
                $sMsg = '未在活动范围内';
            }elseif($iErr == 1003){
                $sMsg = '已经送过优惠券咯~';
            }else{
                $sMsg = '系统繁忙，请稍后再试';
            }
            $this->jret($iErr, $sMsg, array('err' => $iErr));
        }
    }

    /*
     *  处理订单页
     *  @param product_id  商品id
     *  @param month       月份
     *  @param day         日期
     *  @param number      数量
     */
    public function order()
    {
        $pid    = $_POST['product_id'];
        $month  = $_POST['month'];
        $dates  = $_POST['day'];
        $number = $_POST['number'];
        $shequ  = $_POST['shequ'];
        $yhjid  = $_POST['yhj_id'];

        Vendor('Group.Yhj');
        $Yhj = new Yhj($this->token, $this->openid);
        if($Yhj->isUsed($yhjid)){
            $yhjid = null;
        }

        try{
            Vendor('Group.Order');
            $Order = new Order($this->token, $this->openid);
            $aData = $Order->createYuyueOrder($pid,$month,$dates,$number, $shequ, $yhjid);
            if ($yhjid) {
                //如果有优惠券id,则直接处理订单
                if($Order->dealYuyueOrder($aData['orderid'])){
                    $this->jret(0, '', array('finished' => 1));
                }else{
                    $this->jret(-1, '处理订单失败');
                };
            }
            $this->jret(0, '', $aData);
        }catch(\Exception $E){
            $this->jret($E->getCode(), $E->getMessage());
        }
    }

    /*
     *  微信支付成功后的逻辑处理
     *  回调
     */
    public function wx_pay()
    {
        $orderid = $_REQUEST['orderid'];
        $this->Vanke = $Vanke = new Vanke();
        $aRet = json_decode($this->Vanke->getOrderStatus($orderid), true);
        if ($aRet['code']==200) {
            $aRet = $aRet['data'];
        }else{
            WL('wx_pay error:' . print_r($aRet, true), 'vanke_log.log');
            return;
        }
        if ($aRet['status'] == 'payment_completed') {
            Vendor('Group.Order');
            $Order = new Order($this->token, $this->openid);
            if($Order->dealNormalOrder($orderid)){
                WL('支付成功回调成功'.print_r($_REQUEST, true), 'vanke_error.log');
                return $this->redirect(U('Mtour/orderlist'));
            }else{
                WL('支付成功回调失败'.print_r($_REQUEST, true), 'vanke_error.log');
                return $this->redirect(U('index'));
            };
        }else{
            return $this->redirect(U('index'));
        }
    }

}
















/**
 * Class 这个类是做设置旅游时间有关的表
 */
class Calendar{
    protected $_table;//table表格
    protected $_currentDate;//当前日期
    protected $_year; //年
    protected $_month; //月
    protected $_days; //给定的月份应有的天数
    protected $_dayofweek;//给定月份的 1号 是星期几
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->_table="";
        $this->_year = isset($_GET["y"])?$_GET["y"]:date("Y");
        $this->_month = isset($_GET["m1"])?$_GET["m1"]:date("m");
        if ($this->_month>12){//处理出现月份大于12的情况
            $this->_month=1;
            $this->_year++;
        }
        if ($this->_month<1){//处理出现月份小于1的情况
            $this->_month=12;
            $this->_year--;
        }
        $yue=intval($this->_month);//把月分比如05变成5
        $this->_currentDate = '<span class="y">'.$this->_year.'</span>年<span class="m">'.$yue.'</span>月份';//当前得到的日期信息
        $this->_days = date("t",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份应有的天数
        $this->_dayofweek = date("w",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份的 1号 是星期几
    }
    /**
     * 输出标题和表头信息
     */
    protected function _showTitle()
    {
     //   $this->_table="<table style='width: 1000px;'><thead><tr align='center' ><th colspan='7' class='tou'>".$this->_currentDate."</th></tr ></thead>";

        $this->_table="<table id='ducalendar' border='0' cellspacing='0' cellpadding='0' >";
      //  $this->_table.="<tbody><tr >";
        $this->_table.="<tbody><tr class='ducalendar-week'>";
       //$this->_table .="<td style='color:red'>星期日</td>";
        $this->_table .="<td style='color:red'>日</td>";
        $this->_table .="<td>一</td>";
        $this->_table .="<td>二</td>";
        $this->_table .="<td>三</td>";
        $this->_table .="<td>四</td>";
        $this->_table .="<td>五</td>";
        $this->_table .="<td style='color:red'>六</td>";
        $this->_table.="</tr>";
    }
    /**
     * 输出日期信息
     * 根据当前日期输出日期信息
     */
    protected function _showDate($c='',$id='', $token, $rooms)
    {
        $aTmp = array();
        foreach ($c as $k => $row) {
            $aTmp['_' . $row['dates']] = $row;
        }
        //$y_m = $_REQUEST['y'] . '-' . $_REQUEST['m1'];
        $y_m   = MyuyueAction::getMonth();
        //计算出已经卖出的数量
        $aSoldInfo = Arr::changeIndexToKVMap(M('Yuyue_order')->where(array(
            'token'         => $token,
            'product_id'    => $id,
            'shequ'         => $rooms['project_item_name'],
            'month'         => $y_m,
            'type'          => array('egt', 0)
        ))
        ->field('dates, sum(num) as num')
        ->group('dates')
        ->select(), 'dates', 'num');

        $c = $aTmp;
        $nums=$this->_dayofweek+1;
        $this->_table.="<tr class='ducalendar-days'>";
        for ($i=1;$i<=$this->_dayofweek;$i++){//输出1号之前的空白日期
            $this->_table.="<td> </td>";
        }
        //foreach($c as $v) {
        for ($i = 1; $i <= $this->_days; $i++) {//输出天数信息
            if ($nums % 7 == 0) {//换行处理：7个一行

                $this->_table .= "<td  ";
                if(array_key_exists("_".$i,$c)){
                    $this->_table .=" class='red' ";
                }

                $this->_table .=">$i";


                if(array_key_exists("_".$i,$c)){
                    $num = (int)$c['_'.$i]['totalnum'];
                    $num = $num - (int)$aSoldInfo[$i];
                    $this->_table .="<div class='day-info a'y_m='".$y_m."'d='".$i."'></div><div class='day-info info'>剩{$num}</div>";
                }
                $this->_table .="</td></tr><tr class='ducalendar-days'>";

            } else {

                $this->_table .= "<td  ";
                if(array_key_exists("_".$i,$c)){
                    $this->_table .=" class='red' ";
                }

                $this->_table .=">$i";

                if(array_key_exists("_".$i,$c)){
                    $num = (int)$c['_'.$i]['totalnum'];
                    $num = $num - (int)$aSoldInfo[$i];
                    $this->_table .="<div class='day-info a'y_m='".$y_m."'d='".$i."'></div><div class='day-info info'>剩{$num}</div>";
                }


                $this->_table .="</td>";


            }
            $nums++;
        }

        $this->_table.="</tbody></table>";
        //获取当前id
        $id=$_GET['id'];
        $this->_table.="<input type='hidden' name='last' flag='?g=Wap&m=Loan&a=no_credit_xiadan&id=".$id."&y=".($this->_year)."&m1=".($this->_month-1)."'>";
    }
    /**
     * 输出日历
     */
    public function showCalendar($b='',$id='',$token='', $rooms=array())
    {
        $this->_showTitle();
        $this->_showDate($b,$id, $token, $rooms);
        return $this->_table;
    }

}
