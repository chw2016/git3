<?php
/*
 * 旅游
 */
class MtourAction extends VankeBaseAction{
    /*
     * Tpl Dir
     */
    private $Vanke;
    protected $_sTplBaseDir = 'Wap/default/vanke';

    protected function _initialize(){
        parent::_initialize();
        $this->Tour_info    = M('Tour_info');
        $this->Tour_datenum = M('Tour_datenum');
        $this->toursetting  = M('Tour_setting');
        session('main', U('index'));
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
        foreach($aProduct = $this->Tour_info->where(array(
            'token'         => $this->token
        ))->limit($start . ',' . $num)->select() as $k => $Item){
        };
        $this->assign('product', $aProduct);
        if(IS_AJAX || IS_POST){
            exit($this->fetch('./tpl/Wap/default/vanke/page_tour_index.html'));
        }else{
            //分类的轮播图
            $setting = $this->toursetting->where(array(
                'token' => $this->token
            ))->find();
            $this->assign('imgs', explode(',', Arr::get($setting, 'imgs')));
            $this->UDisplay('tour_index');
        }
    }

    /*
     *  评价
     */
    public function praise()
    {
        if (IS_POST) {
            Vendor('Group.Order');
            if(Order::praise(array(
                'source'    => $_REQUEST['source'],
                'token'     => $this->token,
                'openid'    => $this->openid,
                'pid'       => $_REQUEST['id'],
                'info'      => json_encode($_SESSION['WxvankeUser']),
                'content'   => $_POST['content']
            ))){
                exit($this->jret(0));
            }else{
                exit($this->jret(-1));
            };
        }
    }

    /*
     *  我的订单页
     */
    public function orderlist()
    {
        //团购信息
        $page = max(1, (int)$_REQUEST['page']);
        $num  = 10;
        $start = ($page-1) * $num;
        $MOrder = M('Yuyue_order');
        Vendor('Group.Order');
        $Order = new Order($this->token, $this->openid);
        foreach($aProduct = $MOrder->where(array(
            'token'         => $this->token
        ))->limit($start . ',' . $num)->order('id desc')->select() as $k => $Item){
            $aProduct[$k]['type_name']   = $Order::getTypeName($Item['type']);
            $aProduct[$k]['source_name'] = $Order::getSourceName($Item['source']);
            //商品的信息
            $aProduct[$k]['productinfo'] = $this->getProductInfo(
                $Item['source'],
                $Item['product_id']
            );
        };
        $this->assign('orderlist', $aProduct);
        if(IS_AJAX || IS_POST){
            exit($this->fetch('./tpl/Wap/default/vanke/page_order_list.html'));
        }else{
            $this->UDisplay('orderlist');
        }
    }

    /*
     *  订单详情
     */
    public function order_detail()
    {
        $id     = $_REQUEST['id'];
        $MOrder = M('Yuyue_order');
        $Info  = $MOrder->where(array(
            'id' => $id
        ))->find();
        Vendor('Group.Order');

        $Order = new Order($this->token, $this->openid);
        $Info['type_name']   = $Order::getTypeName($Info['type']);
        $Info['source_name'] = $Order::getSourceName($Info['source']);
        //商品的信息
        $Info['productinfo'] = $this->getProductInfo(
            $Info['source'],
            $Info['product_id']
        );
        $aSN     = explode(',', $Info['sn']);
        $aSNTime = explode(',', $Info['sn_used_time']);
        $aCombineSN = array();
        foreach ($aSN as $k => $v) {
            if ($v) {
                $aCombineSN[$v] = isset($aSNTime[$k]) ? $aSNTime[$k] : '';
            }
        }

        $aPraiseInfo = Order::getPraiseById($_REQUEST['source'], $id);
        if ($aPraiseInfo) {
            $aPraiseInfo['info'] = json_decode($aPraiseInfo['info'], true);
        }
        $this->assign(array(
            'info'       => $Info,
            'sn'         => $aCombineSN,
            'praiseInfo' => $aPraiseInfo
        ));
        $this->UDisplay('order_detail');
    }

    public function getProductInfo($source, $pid)
    {
        if ($source == 1) {
            $Model = M('Yuyue_goods');
        }else if($source == 2){
            $Model = $this->Tour_info;
        }else{
            return array();
        }
        return $Model->where(array(
            'token' => $this->token,
            'id'    => $pid
        ))
        ->field('name, url')
        ->find();
    }

    /*
     * 预约详情
     */
    public function tour_detail()
    {
        $id = $_REQUEST['id'];
        if (!$id) {
            return $this->redirect(U('index'));
        }
        $info = $this->Tour_info->where(array(
            'token' => $this->token,
            'id'    => $id
        ))->find();

        $month = $this->getMonth();

        $calc=new Calendar();
        Vendor('Group.Order');
        foreach(
            $aPraiseInfo=Order::getPraiseById(Order::ORDER_SOURCE_TOUR, $id, 0, 3)
            as
            $k => $v
        ){
            $aPraiseInfo[$k]['info'] = json_decode($v['info'], true);
        };
        $this->assign(array(
            'id'        => $_GET['id'],
            'info'      => $info,
            'source'    => Order::ORDER_SOURCE_TOUR,
            'imgs'      => explode(',', Arr::get($info, 'imgs')),
            'praiseInfo'=> $aPraiseInfo,
            'time_info' => $calc->showCalendar($this->Tour_datenum->where(array(
                'token'     => $this->token,
                'month'       => $month,
                'tour_id'   => $id
            ))->select(),$id, $this->token)
        ));


        // 三种日期
        $baseUrl = '?g=Wap&m=Mtour&a=tour_detail&id=';

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

        $this->UDisplay('tour_detail');
    }

    /*
     * 评论列表
     */
    public function praise_list()
    {
        Vendor('Group.Order');
        $iStart = max(0, (int)$_REQUEST['page']);
        $id = $_REQUEST['id'];
        foreach(
            $aPraiseInfo=Order::getPraiseById($_REQUEST['source'], $id, $iStart, 15)
            as
            $k => $v
        ){
            $aPraiseInfo[$k]['info'] = json_decode($v['info'], true);
        };
        $this->assign('praiseInfo', $aPraiseInfo);
        if (IS_AJAX) {
            exit($this->fetch('./tpl/Wap/default/vanke/page_praise_list.html'));
        }else{
            $this->UDisplay('praise_list');
        }
    }


    public function getMonth()
    {
        $y   = $_REQUEST['y'] ? $_REQUEST['y'] : date('Y');
        $m1  = $_REQUEST['m1'] ? $_REQUEST['m1'] : date('m');
        return $y . '-' . $m1;
    }

    /*
     *  预约订单
     */
    public function tour_order()
    {
        $id     = $_POST['id'];
        $month  = $_POST['month'] == '-' ? date('Y-m') : $_POST['month'] ;
        $day    = $_POST['day'];
        //查商品
        $Product = $this->Tour_info->where(array(
            'id'    => $id
        ))->find();
        if (!$Product) {
            return $this->redirect(U('index'));
        }
        $this->assign(array(
            'product'   => $Product,
            'month'     => $month,
            'day'       => $day,
            'dataInfo'  => $this->Tour_datenum->where(array(
                'token'     => $this->token,
                'tour_id'   => $id,
                'month'     => $month,
                'dates'     => $day
            ))->find()
        ));
        $this->UDisplay('tour_order');
    }

    /*
     *  验证
     */
    public function yuyue_verify()
    {
        if (IS_POST) {
            try{
                Vendor('Group.Order');
                $Order = new Order($this->token, $this->openid);
                $ret = $Order->verifyYuyue($_REQUEST['verify']);
                if($ret) {
                    return $this->jret(0);
                }else{
                    return $this->jret(-1);
                }
            }catch(Exception $E){
                return $this->jret(-1, $E->getMessage());
            }
        }else{
            $this->UDisplay('yuyue_verify');
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
        $family = $_POST['family'];
        $month  = $_POST['month'];
        $dates  = $_POST['day'];
        $number = $_POST['number'];
        try{
            Vendor('Group.Order');
            $Order = new Order($this->token, $this->openid);
            $aData = $Order->createTourOrder($pid, $family, $month,$dates,$number);
            $this->jret(0, '', $aData);
        }catch(Exception $E){
            $code = $E->getCode();
            $code = $code ? $code : -1;
            $this->jret($code, $E->getMessage());
        }
    }

    /*
     *  微信支付成功后的逻辑处理
     *  回调
     */
    public function wx_pay()
    {
        $this->Vanke = $Vanke = new Vanke();
        $aRet = json_decode($this->Vanke->getOrderStatus($_REQUEST['orderid']), true);
        if ($aRet['code']==200) {
            $aRet = $aRet['data'];
        }
        if ($aRet['status'] == 'payment_completed') {
            $orderid    = $_REQUEST['orderid'];
            $money      = $_REQUEST['money'];
            $product_id = $_REQUEST['product_id'];
            //$gid        = $_REQUEST['gid'];
            Vendor('Group.Order');
            $Order = new Order($this->token, $this->openid);
            //成功之后的处理
            $mRet = $Order->dealYuyueOrder($orderid);
            WL('支付成功回调数据'.print_r($_REQUEST, true), 'vanke_log.log');
            if($mRet == true){//成功
                return $this->redirect(U('Mtour/orderlist'));
            }else{
                WL('支付成功回调失败'.print_r($_REQUEST, true) . '|' . $mRet, 'vanke_error.log');
                return $this->redirect(U('index'));
            }
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
    protected function _showDate($c='',$id='', $token)
    {
        $aTmp = array();
        foreach ($c as $k => $row) {
            $aTmp['_' . $row['dates']] = $row;
        }
        $y   = $_REQUEST['y'] && $_REQUEST['y'] != '-' ? $_REQUEST['y'] : date('Y');
        $m1  = $_REQUEST['m1'] && $_REQUEST['m1'] != '-' ? $_REQUEST['m1'] : date('m');
        $y_m = $y . '-' . $m1;
        //计算出已经卖出的数量
        Vendor('Group.Order');
        $aSoldInfo = Arr::changeIndexToKVMap(M('Yuyue_order')->where(array(
            'token'         => $token,
            'source'        => Order::ORDER_SOURCE_TOUR,
            'product_id'    => $id,
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
                    $num    = (int)$c['_'.$i]['totalnum'];
                    $money  = $c['_'.$i]['money'];
                    $num    = $num - (int)$aSoldInfo[$i];
                    $this->_table .="<div class='day-info a'y_m='".$y_m."'d='".$i."'>￥<span class='money'>{$money}</span></div><div class='day-info info'>剩<span class='num'>{$num}</span></div>";
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
                    $money  = $c['_'.$i]['money'];
                    $num = $num - (int)$aSoldInfo[$i];
                    $this->_table .="<div class='day-info a'y_m='".$y_m."'d='".$i."'>￥<span class='money'>{$money}</span></div><div class='day-info info'>剩<span class='num'>{$num}</span></div>";
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
    public function showCalendar($b='',$id='',$token='')
    {
        $this->_showTitle();
        $this->_showDate($b,$id, $token);
        return $this->_table;
    }

}
