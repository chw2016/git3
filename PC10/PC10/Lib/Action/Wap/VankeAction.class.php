<?php
/*
 * 万科团购
 */
class VankeAction extends VankeBaseAction{
    /*
     * Tpl Dir
     */
    protected $Vanke;
    protected $_sTplBaseDir = 'Wap/default/vanke';

    protected function _initialize(){
        parent::_initialize();
        //先跳转，url不允许存有openid信息

        /*
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
        $this->product = M('groupbuy_product');
        $this->date    = date('Y-m-d H:i:s');
        $this->assign(array(
            'token'     => $this->token,
            'openid'    => $this->openid
        ));
        */
    }

    /*
     *  微信支付成功后的逻辑处理
     *  回调
     */
    public function wx_pay()
    {
	WL('post:' . file_get_contents('php://input') . __SELF__, 'vanke_log.log');
	WL('return:' . print_r($_REQUEST, true), 'vanke_log.log');
        $aRet = json_decode($this->Vanke->getOrderStatus($_REQUEST['orderid']), true);
	WL('ret:' . print_r($aRet, true), 'vanke_log.log');
        if ($aRet['code']==200) {
            $aRet = $aRet['data'];
        }
        if ($aRet['status'] == 'payment_completed') {
            $orderid    = $_REQUEST['orderid'];
            $money      = $_REQUEST['money'];
            $product_id = $_REQUEST['product_id'];
            $gid        = $_REQUEST['gid'];
            Vendor('Group.Order');
            $Order = new Order($this->token, $this->openid);
            //成功之后的处理
            $mRet = $Order->dealOrder($Order->getIdByOrderid($orderid));
            WL('支付成功回调数据'.print_r($_REQUEST, true), 'vanke_log.log');
                if($mRet === true){//成功
                    return $this->redirect(U('mygroup'));
                }else{
                    WL('支付成功回调失败'.print_r($_REQUEST, true) . '|' . $mRet, 'vanke_error.log');
                    return $this->redirect(U('index'));
                }
        }else{
                return $this->redirect(U('index'));
        }
    }

    /*
     *  团购页
     */
    public function index()
    {
        //团购信息
        $page = max(1, (int)$_REQUEST['page']);
        $num  = 10;
        $start = ($page-1) * $num;
        foreach($aProduct = $this->product->where(array(
            'token'         => $this->token,
            'start_time'    => array('lt', $this->date),
            'end_time'      => array('gt', $this->date)
        ))->limit($start . ',' . $num)->select() as $k => $Item){
            $aProduct[$k]['groupmsg'] = json_decode($Item['groupmsg'], true);
            $aProduct[$k]['min_price'] = min($aProduct[$k]['groupmsg']);
            $aProduct[$k]['pic']      = explode(',', $Item['pic']);
        };
        $this->assign('product', $aProduct);
        if(IS_AJAX || IS_POST){
            exit($this->fetch('./tpl/Wap/default/vanke/page_pro.html'));
        }else{
            $this->UDisplay('index');
        }
    }

    public function bind_user()
    {
        $this->UDisplay('bind_user');
    }

    public function myyhj()
    {
        Vendor('Group.Yhj');
        $Yhj = new Yhj($this->token, $this->openid);
        $this->assign('list', $Yhj->select(null, $_REQUEST['page']));
        if (IS_POST || IS_AJAX) {
            exit($this->fetch('./tpl/Wap/default/vanke/page_myyhj.html'));
        }else{
            $this->UDisplay('myyhj');
        }
    }

    /*
     *  个人中心
     */
    public function ucenter()
    {
        $this->UDisplay('ucenter');
    }

    /*
     *  团购产品详情
     */
    public function detail()
    {
        $id = $_REQUEST['id'];
        if (!$id) {
            $this->redirect(U('index'));
            return;
        }
        $this->assign('product', $this->getProductById($id));
        $this->assign('id', $id);
        $this->assign(array(
            //开团
            'group' => self::getGroupInfo(
                M('group_info')
                    ->limit(3)
                    ->order('status asc, id desc')
                    ->where(array(
                'token'         => $this->token,
                'product_id'    => $id
            ))->select())
            //评价
        ));
        $this->UDisplay('detail');
    }

    /*
     *  团购产品详情
     */
    public function more_group()
    {
        $page = max(1, (int)$_REQUEST['page']);
        $num  = 15;
        $start = ($page-1) * $num;

        $id = $_REQUEST['id'];
        $this->assign('id', $id);
        $this->assign(array(
            //开团
            'group' => self::getGroupInfo(
                M('group_info')
                    ->limit($start . ',' . $num)
                    ->order('status asc')
                    ->where(array(
                'token'         => $this->token,
                'product_id'    => $id
            ))->select())
            //评价
        ));
        if (IS_POST || IS_AJAX) {
            exit($this->fetch('./tpl/Wap/default/vanke/page_more_group.html'));
        }else{
            $this->UDisplay('more_group');
        }
    }

    public static function getGroupInfo($aData)
    {
        //获取用户信息
        $Vanke = new Vanke(); $aUser = array();
        if($aOpenid = array_filter(array_keys(Arr::changeIndex($aData, 'openid')))){
            foreach ($aOpenid as $op) {
                $aTmpUserInfo   = $Vanke->getUserInfo($op);
                if (200 == $aTmpUserInfo['code']) {
                    $aUser[$op] = $aTmpUserInfo['data'];
                }
            }
        }
        $Obj = new self();
        foreach ($aData as $k => $v) {
            //姓名
            $aData[$k]['userinfo'] = Arr::get($aUser, $v['openid']);
            //已开团人数
            $aData[$k]['total_buy'] = $Obj->getTotalBuyById($v['id'], $v['product_id'], false);
        }
        return $aData;
    }

    public function getProductById($id)
    {
        $aProduct = $this->product->where(array(
            'id'    => $id,
            'token' => $this->token,
        ))->find();
        $aProduct['groupmsg']  = json_decode($aProduct['groupmsg'], true);
        $aProduct['min_price'] = min($aProduct['groupmsg']);
        $aProduct['pic']       = explode(',', $aProduct['pic']);
        $aProduct['total_buy'] = $this->getTotalBuyById(null,$id, true);//总量
        $aProduct['total_buy_number'] = $this->getTotalBuyById(null,$id, false);//总人
        return $aProduct;
    }

    /*
     *  获取一件商品总共售量
     */
    public function getTotalBuyById($gid, $id, $number=false)
    {
        //购买真实数量
        $aExtra = array();
        if ($gid) {
            $aExtra['gid'] = $gid;
        }
        //如果是结束失败的单子，则在算的时候，status要算上退款的
        $Info = M('Group_info')->where(array('id' => $gid))->find();
        if (2 == $Info['status']) {
            $aExtra['status'] = 2;
        }else{
            $aExtra['status'] = 1;
        }
        if ($number) {
            return Arr::get(M('Group_order')->field('sum(number) as num')->where(array(
                'token'      => $this->token,
                'product_id' => $id
            ) + $aExtra)->find(), 'num', 0);
        }else{
            //人头
            return (int)M('Group_order')->field('distinct openid')->where(array(
                'token'      => $this->token,
                'product_id' => $id
            ) + $aExtra)->count();
        }
    }

    /*
     *  购买
     */
    public function buy()
    {
        return $this->redirect(U('index'));
        $id     = $_REQUEST['id'];
        $number = $_REQUEST['number'];
        if (!$id || !$number) {
            $this->redirect(U('index'));
            return;
        }
        $aProduct = $this->getProductById($id);
        if (!isset($aProduct['groupmsg'][$number])) {
            $this->redirect(U('index'));
            return;
        }
        Vendor('Group.Yhj');
        $Yhj = new Yhj($this->token, $this->openid);
        //默认收货地址
        $this->assign(array(
            'product'   => $aProduct,
            //电影院
            'movie'     => M('Group_movie')->where(array(
                'token' => $this->token
            ))->select(),
            'group'     => $aProduct['groupmsg'][$number],
            'address'   => M('address')->where(array(
                'token'     => $this->token,
                'openid'    => $this->openid,
                'is_default' => 1
            ))->find(),
            //优惠券信息
            'yhj'       => $Yhj->select(0),
            'userinfo'  => M('Vanke_user')->where(array(
                'token'     => $this->token,
                'openid'    => $this->openid,
            ))->find(),
            'number'    => $number
        ));
        $this->UDisplay('buy');
    }

    public function address()
    {
        //默认收货地址
        $this->assign(array(
            'address'   => M('address')->where(array(
                'token'     => $this->token,
                'openid'    => $this->openid
            ))->select()
        ));
        $this->UDisplay('address');
    }
    public function add_address()
    {
        $this->UDisplay('add_address');
    }

    public function jsonRet($status=0, $aData=array(), $sMsg='')
    {
        return json_encode(array(
            'status' => $status,
            'data'   => $aData,
            'msg'    => $sMsg
        ));
    }

    /*
     *  下订单
     *  1、使用微信支付   去付款
     *  2、使用优惠券     看优惠券是不是足够，足够就直接成功，否则付款金额要减去优惠券
     *  3、直接使用余额   余额够就直接成功，否则减掉余额再支付
     */
    public function order()
    {
        $is_new     = $_POST['is_new'];//是否是开团
        $single     = $_POST['single'];//是否单独购买
        $movieid    = $_POST['movieid'];//电影院id
        $gid        = $_POST['gid'];//是否是开团
        $product_id = $_POST['product_id'];//商品id
        $tnumber    = $_POST['tnumber'];//参团人数
        $number     = max(1, (int)$_POST['number']);//购买数量
        $use_yu_er  = $_POST['use_yu_er'];//是否使用余额
        $yhjid      = $_POST['yhjid'];//如果使用优惠券，则使用优惠券ID

        //商品信息
        $aProductInfo = $this->getProductById($product_id);
        //总价格
        if (!isset($aProductInfo['groupmsg'][$tnumber])) {
            exit($this->jsonRet(-1, null, '没有开启此团人数的团购'));
        }
        $iTotal = $aProductInfo['groupmsg'][$tnumber] * $number;//应付的总金额
        $iNeedTotal = $iTotal;
        Vendor('Group.Yhj');
        Vendor('Group.Order');
        $Yhj = new Yhj($this->token, $this->openid);
        if ($yhjid) {//如果使用优惠券
            if($aYhj=$Yhj->get($yhjid)){
                if ($aYhj->status != Yhj::STATUS_NORMAL) {
                    exit($this->jsonRet(-1, null, '优惠券已使用完'));
                }
                $iNeedTotal = $iTotal - $aYhj['number'];
            }else{
                exit($this->jsonRet(-1, null, '优惠券不存在'));
            };
        }

        if ($use_yu_er && $iNeedTotal > 0) {
            if($aUser = M('Vanke_user')->where(array(
                'token'     => $this->token,
                'openid'    => $this->openid
            ))->find()){

                $iNeedTotal = $iNeedTotal - $aUser['money'];
            }else{
                exit($this->jsonRet(-1, null, '亲，你的余额为0哦'));
            };
        }

        $Order = new Order($this->token, $this->openid);

        $iErr = 0;
        $iNeedTotal = max(0, $iNeedTotal);

        $aRet = array();
        $one = $iOrderID = $Order->createOrder(
            $is_new,
            $single,
            $gid,
            $iTotal,
            $iNeedTotal,
            $product_id,
            $tnumber,
            $number,
            $yhjid,
            $use_yu_er,
            $movieid,
            $iErr,
            $aRet
        );
        if (!$one) {
            switch ($iErr) {
                case 2001:
                    $sMsg = '亲，库存已不够了哦';
                    break;
                case 2011:
                    $sMsg = '亲，您已经买过了哦';
                    break;
                default:
                    $sMsg = '亲，下单出错了，请重试';
                    break;
            }
            exit($this->jsonRet(-1, null, $sMsg));
        }

        //优惠券就已经足够了，不再需要付款
        if ($iNeedTotal <= 0 && $yhjid) {
            $two = $Order->dealOrder($iOrderID);
            if ($two !== true) {
                exit($this->jsonRet(-2, null, '订单失败'));
            }
            exit($this->jsonRet(0, null, '订单成功'));
        }
        //余额已经够了，直接成功
        $iNeedTotal = max(0, $iNeedTotal);
        if ($iNeedTotal <= 0) {
            $two = $Order->dealOrder($iOrderID);
            if (!$one || $two !== true) {
                exit($this->jsonRet(-3, null, '订单失败'));
            }
            exit($this->jsonRet(0, null, '订单成功'));
        }
        //还需要支付，则返回跳转
        exit($this->jsonRet(1, array(
            'id'        => $iOrderID,
            'need'      => $iNeedTotal,
            'gid'       => $aRet['gid'],
            'orderid'   => $aRet['orderid']
        ),''));
    }

    public function mygroup()
    {
        //我开的团
        $aOwn = M('group_info')->where(array(
            'token' => $this->token,
            'openid'=> $this->openid
        ))->order('add_time desc')->select();
        //商品信息
        if($aId = array_keys(Arr::changeIndex($aOwn, 'product_id'))){
            $aProInfo = Arr::changeIndex(
                M('groupbuy_product')->where(array('id' => array('in', $aId)))->select(), 'id');
            foreach($aOwn as $k => $v){
                $aOwn[$k]['total_buy'] = $this->getTotalBuyById($v['id'], $v['product_id'], false);
                $aOwn[$k]['goods']     = Arr::get($aProInfo, $v['product_id']);
                $aOwn[$k]['price']     = Arr::get(json_decode($aOwn[$k]['goods']['groupmsg'], true), $v['tnumber']);
            };
            $this->assign('own', $aOwn);
        };

        //我参的团
        $aOwnId = array_keys(Arr::changeIndex($aOwn, 'id'));
        $aGid = array_filter(array_keys(Arr::changeIndex(M('group_order')->where(array(
            'token' => $this->token,
            'openid'=> $this->openid
        ))->order('add_time desc')->field('distinct(gid) as gid')->select(), 'gid')));

        if($aJoinId = array_diff($aGid, $aOwnId)){
            $aJoin = M('group_info')->where(array(
                'token' => $this->token,
                'id'    => array('in', $aJoinId)
            ))->select();
            if($aId = array_keys(Arr::changeIndex($aJoin, 'product_id'))){
                $aProInfo = Arr::changeIndex(
                    M('groupbuy_product')->where(array('id' => array('in', $aId)))->select(), 'id');
            };

            foreach($aJoin as $k => $v){
                $aJoin[$k]['total_buy'] = $this->getTotalBuyById($v['id'], $v['product_id'], false);
                $aJoin[$k]['goods']     = Arr::get($aProInfo, $v['product_id']);
                $aJoin[$k]['price']     = Arr::get(json_decode($aJoin[$k]['goods']['groupmsg'], true), $v['tnumber']);
            };
            $this->assign('join', $aJoin);
        };
        $this->UDisplay('mygroup');
    }

    public function mygroup_info()
    {
        if(!$id = $_REQUEST['id']){
            return $this->redirect(U('mygroup'));
        }
        if(!$aGroupInfo=M('group_info')->where(array(
            'token'     => $this->token,
            //'openid'    => $this->openid,
            'id'        => $id
        ))->find()){
            return $this->redirect(U('mygroup'));
        }
        //商品信息
        $aProductInfo = $this->getProductById($aGroupInfo['product_id']);
        //参团人员

        $aSelfData = array();
        foreach($aGroupUser = M('group_order')->where(array(
            'token'     => $this->token,
            'gid'       => $id
        ))->select() as $k => $user){
            $aTUser = $this->Vanke->getUserInfo($user['openid']);
            if (200 == $aTUser['code']) {
                $aTUser = $aTUser['data'];
            }
            if ($user['openid'] == $this->openid AND $user['status'] == 1) {
                if (!$aSelfData) {
                    $aSelfData = $user;
                }
                $aT        = array_filter(explode(',', $user['sn']));
                $aTime     = array_filter(explode(',', $user['used_sn_time']));
                $aTarT     = array();
                foreach ($aT as $k2 => $v) {
                    $aTarT[$v] = $aTime[$k2];
                }
                if (!$aSelfData || !isset($aSelfData['sninfo'])) {
                    $aSelfData['sninfo']   = $aTarT;
                }else{
                    $aSelfData['sninfo']   = $aSelfData['sninfo'] + $aTarT;
                }
                WL(print_r($aSelfData, true));
                $aSelfData['profile']  = $aTUser;
                //处理下
            }
            $aGroupUser[$k]['userinfo']  = $aTUser;
            $aGroupUser[$k]['head']      = Arr::get($aTUserInfo, 'head');
            $aGroupUser[$k]['moviename'] = M('Group_movie')->where(array(
                'id'    => $user['movieid']
            ))->getField('name');
        };

        $this->assign(array(
            'groupinfo'    => $aGroupInfo,//团购信
            'product'      => $aProductInfo,//商品信息
            'self_order'   => $aSelfData,
            'total_buy'    => $this->getTotalBuyById($aGroupInfo['id'], $aGroupInfo['product_id'], false),
            'pmoney'       => Arr::get($aProductInfo['groupmsg'], $aGroupInfo['tnumber']),
            'g_users'      => $aGroupUser//参团人
        ));
        $this->UDisplay('mygroup_info');
    }

    /*
     *  显示玩法
     */
    public function show_rule()
    {
        $this->assign('data', M('Group_setting')->where(array(
            'token' => $this->token
        ))->getField($_GET['type']));
        $this->UDisplay('show_rule');
    }

    public function bind_qs()
    {
        if (IS_POST) {
            unset($_SESSION['WxvankeQSInfo']);
            //添加家属
            $sCustomID = $_REQUEST['customer_id'];
            if (!$sCustomID) {
                return $this->jret(-1, '系统繁忙，请稍后再试');
            }
            $Vanke = new Vanke();
            $aRet = $Vanke->addUserQSInfo($sCustomID, array(
                'name'              => Arr::get($_POST, 'userName'),
                'identity_type'     => Arr::get($_POST, 'identity_type'),
                'identity_number'   => Arr::get($_POST, 'identity_number'),
                'gender'            => Arr::get($_POST, 'gender'),
                'contact'           => array(
                    'type'      => Arr::get($_POST, 'telPhone'),
                    'address'   => Arr::get($_POST, 'addressDetail'),
                    'phone'     => Arr::get($_POST, 'telMobile')
                ),
                'relation'          => Arr::get($_POST, 'relation'),
                'description'       => Arr::get($_POST, 'description'),
            ));
            if (is_array($aRet) AND 200 == $aRet['code'] ) {
                return $this->jret(0);
            }else{
                return $this->jret(-1);
            }
        }else{
            $Vanke = new Vanke();
            $this->assign(array(
                'identity_type' => $Vanke->getIdentityType(),
            ));
            $this->UDisplay('bind_qs');
        }
    }
}
