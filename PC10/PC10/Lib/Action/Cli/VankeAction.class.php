<?php
/*
 *   主要处理逾期未结束的团购信息还款
 */
class VankeAction extends CliAction {

    public $t = '5d8a87bab30de695954b17fc835b9d12';

    public function index()
    {
        set_time_limit(0);
        $i     = 1;
        $iPage = 20;
        $time  = date('Y-m-d H:i:s', strtotime('-1 day'));
        Vendor('Group.Yhj');

        //未完结的，且超时的团
        $this->order = M('Group_order');
	WL(date('Y-m-d H:i:s') . '->开始处理开团组信息', 'vanke.log');
        while($Group=M('Group_info')->where(array(
            'token'     => $this->t,
            'status'    => 0,
            'add_time'  => array('lt', $time)
        ))->limit(($i-1) * $iPage .','.$iPage)->select()){
            $i++;
            foreach($Group as $k => $G){
                WL('开始处理开团组信息'.print_r($G, true), 'vanke.log');
                $this->order->startTrans();
                //找到其团购的人员
                foreach ($aGroupUser = M('Group_order')->where(array(
                    'token'     => $this->t,
                    'gid'       => $G['id']
                ))->select() as $aK => $aUser){
                    //事务处理
                    #给每个人退钱
                    //如果有使用优惠券
                    $iYhjMoney = 0;

                    if ($aUser['yhjid']) {
                        $Yhj = new Yhj($this->t, $aUser['openid']);
                        if (!$Yhj->unused($aUser['yhjid'])) {
                            WL('处理开团组信息失败'.print_r($G, true).'将优惠券设置成已使用状态失败', 'vanke.log');
                            $this->order->rollback();
                            continue;
                        }
                        $aYhjInfo  = $Yhj->get($aUser['yhjid']);
                        $iYhjMoney = Arr::get($aYhjInfo,'number', 0);
                    }
                    $iLeft = $aUser['total'] - $iYhjMoney;
                    if ($iLeft > 0) {
                        //给用户余额补上  要扣除优惠券里的钱
                        if(!$VankeUser = M('Vanke_user')->where(array(
                            'token'     => $this->t,
                            'openid'    => $aUser['openid']
                        ))->find()){
                            if(!M('Vanke_user')->add($aTData=array(
                                'token'     => $this->t,
                                'openid'    => $aUser['openid'],
                                'money'     => $iLeft,
                                'add_time'  => date('Y-m-d H:i:s')
                            ))){
                                WL('处理开团组信息失败'.print_r($G, true).'用户添加余额失败'.print_r($aTData, true), 'vanke.log');
                                $this->order->rollback();
                                continue;
                            };
                        }else{
                            if(!M('Vanke_user')->where($aTData=array(
                                'token'     => $this->t,
                                'openid'    => $aUser['openid'],
                            ))->data(array(
                                'money' => $VankeUser['money'] + $iLeft
                            ))->save()){
                                WL('处理开团组信息失败'.print_r($G, true).'用户添加余额失败'.print_r($aTData, true), 'vanke.log');
                                $this->order->rollback();
                                continue;
                            };
                        };
                    }
                    //将订单状态改成已退款
                    if(!M('Group_order')
                        ->where(array('id' => $aUser['id']))
                        ->data(array('status' => 2))
                        ->save()){
                            WL('处理开团组信息失败'.print_r($G, true).'处理订单为已退款失败'.print_r($aUser, true), 'vanke.log');
                            $this->order->rollback();
                            continue;
                    };
                };
                if(!M('Group_info')
                    ->where(array('id' => $G['id']))
                    ->data(array('status' => 2))
                    ->save()){
                        WL('处理开团组信息失败'.print_r($G, true).'将团设置成失效失败', 'vanke.log');
                        $this->order->rollback();
                        continue;
                };
                //更新团为失败
                WL('处理开团组信息成功'.print_r($G, true), 'vanke.log');
                $this->order->commit();
            }
        }
    }

    /*
     *  先把付款人数小于指定的先设置为未成团
     */
    public function reback()
    {
        set_time_limit(0);
        $aOrderInfo = M('Group_info')->select();
        foreach ($aOrderInfo as $k => $v) {
            //本团人数
            $iHisNum = (int)count(M('Group_order')
                ->where(array(
                    'status'=> 1,
                    'gid'   => $v['id']
                ))
                ->field('distinct openid')
                ->select());
            if ($iHisNum >= $v['tnumber']) {
                $status = 1;
            }else{
                $status = 0;
            }
            WL(M('Group_order')->getLastSql() . '--->' . print_r($v, true) . '-->' . $iHisNum . '|-->' . $status, 'vanke_back.log');
            M('Group_info')
                ->where(array('id' => $v['id']))
                ->data(array('status' => $status))->save();
        }
    }

    public function update()
    {
        set_time_limit(0);
        //找到所有未完成订单
        $this->Vanke = $Vanke = new Vanke();
        $aOrder = M('Group_order')->field('orderid')->where(array('status' => 0))->select();
        foreach ($aOrder as $k => $Order) {
            $orderid = $Order['orderid'];
            if (!$orderid) {
                continue;
            }
            $aRet = json_decode($this->Vanke->getOrderStatus($orderid), true);
            if ($aRet['code']==200) {
                WL('查询成功'.print_r($aRet, true), 'vanke_error.log');
                $aRet = $aRet['data'];
            }else{
                WL('查询失败' . print_r($Order, true) .print_r($aRet, true), 'vanke_error.log');
                continue;
            }
            if ($aRet['status'] == 'payment_completed') {
                Vendor('Group.Order');
                $Order = new Order($this->t, $this->openid);
                //成功之后的处理
                $mRet = $Order->dealOrder($Order->getIdByOrderid($orderid));
                if($mRet === true){//成功
                    WL('支付成功'.print_r($Order, true), 'vanke_error.log');
                }else{
                    WL('支付失败'.$mRet, 'vanke_error.log');
                }
            }
        }
    }

    /*
     *  成团的付款人名单
     */
    public function export()
    {
        set_time_limit(0);
        $Vanke = new Vanke();
        $aData = array();
        $i     = 0;
        //找到影院
        $Movie = Arr::changeIndexToKVMap(M('group_movie')->select(), 'id', 'name');
        foreach($aGroup=M('Group_info')->where(array('status' => 1))->select() as $G){
            //找到里面的人
            foreach(M('Group_order')->where(array(
                'gid'    => $G['id'],
                'status' => 1
            ))->select() as $U){
                $User = $Vanke->getUserInfo($U['openid']);
                if (200 == $User['code']) {
                    $User = $User['data'];
                }else{
                    $User = array();
                }
                if (!isset($aData[$i])) {
                    $aData[$i] = array();
                }
                $aData[$i] = array(
                    '团id'          => $G['id'],
                    '用户'          => $User['profile']['nickname'],
                    '选择影院'      => Arr::get($Movie, $U['movieid']),
                    '参团人数'      => $U['tnumber'],
                    '购买数量'      => $U['number'],
                    '总价格'        => $U['total'],
                    '是否使用优惠券'  => $U['yhjid'] ? '是' : '否',
                    'sn'            => trim($U['sn'], ',')
                );
                $i++;
            }
        }
        Excel::arr2ExcelDownload($aData);
    }

    /*
     *  导出只买过1件商品的用户
     */
    public function one()
    {
        $aOneUser = array_keys(Arr::changeIndex(M('Group_order')->where(array(
            'status'    => 1
        ))->group('openid')
        ->field('openid, sum(number) as n')
        ->having('n = 1')
        ->select(), 'openid'));
        $Vanke = new Vanke();
        $Movie = Arr::changeIndexToKVMap(M('group_movie')->select(), 'id', 'name');
        $aData = array();
        foreach ($aOneUser as $u) {
            //$User = $Vanke->getUserInfo($u);
            $aVankeBindUser = json_decode($Vanke->getVankeBindUser($u),true);
            if ($aVankeBindUser['code'] == 200) {
                $aData[] = array(
                    '姓名'  => $aVankeBindUser['data']['name'],
                    '手机'  => $aVankeBindUser['data']['phones'][0]['number'],
                );
            }else{
                $aData[] = array(
                    '姓名'  => $u,
                    '手机'  => ''
                );
            }
        }
        Excel::arr2ExcelDownload($aData);
    }


    /*
     *  导出影院
     */
    public function data()
    {
        $Vanke = new Vanke();
        //所有影院
        //所有成团的gid
        $aGid = array_keys(Arr::changeIndex(
            M('Group_info')->where(array('status' => 1))->select()
        ,'id'));
        $i = 0;
        $aData = array();
        foreach(M('Group_movie')->select() as $k => $movie){
            //找到影院的所有成团人员信息
            foreach(M('Group_order')->where(array(
                'movieid'   => $movie['id'],
                'gid'   => array('in', $aGid),
                'status'=> 1
            ))->select() as $order){
                //通过openid找到用户信息
                $User = $Vanke->getUserInfo($order['openid']);
                if (200 == $User['code']) {
                    $User = $User['data'];
                }
                if (!isset($aData[$i])) {
                    $aData[$i] = array();
                }
                $snT = explode(',', $order['used_sn_time']);
                foreach(explode(',', $order['sn']) as $isn => $sn){
                    if (!$sn) {
                        continue;
                    }
                    $aData[$i]['影院']          = $movie['name'];
                    $aData[$i]['姓名']          = $BindUser['name'];
                    $aData[$i]['sn']          = $sn;
                    $aData[$i]['使用时间']    = $snT[$isn];
                    $i++;
                };
            }
        }
        Excel::arr2ExcelDownload($aData);
    }

    /*
     *  导出影院的sn码及使用状态
     */
    public function all()
    {
        $Vanke = new Vanke();
        //所有影院
        //所有成团的gid
        $bUsed = $_REQUEST['use'];
        $aGid = array_keys(Arr::changeIndex(
            M('Group_info')->where(array('status' => 1))->select()
        ,'id'));
        $i = 0;
        $aData = array();
        foreach(M('Group_movie')->select() as $k => $movie){
            //找到影院的所有成团人员信息
            $aExtra = array();
            if ($bUsed) {
                $aExtra = array('used_sn_time' => array('neq', ''));
            }
            foreach(M('Group_order')->where($aExtra + array(
                'movieid'   => $movie['id'],
                'gid'   => array('in', $aGid),
                'status'=> 1
            ))->select() as $order){
                //通过openid找到用户信息
                $BindUser = json_decode($Vanke->getVankeBindUser($order['openid']), true);
                if (200 == $BindUser['code']) {
                    $BindUser = $BindUser['data'];
                }
                //通过openid找到微信用户信息
                $aUser = $Vanke->getUserInfo($order['openid']);
                if (200 == $aUser['code']) {
                    $aUser = $aUser['data'];
                }
                if (!isset($aData[$i])) {
                    $aData[$i] = array();
                }
                $snT = explode(',', $order['used_sn_time']);
                foreach(explode(',', $order['sn']) as $isn => $sn){
                    if (!$sn) {
                        continue;
                    }
                    //如果是已使用的
                    if ($bUsed && !$snT[$isn]) {
                        continue;
                    }
                    $aData[$i]['影院']       = $movie['name'];
                    $aData[$i]['姓名']       = $BindUser['name'];
                    $aData[$i]['微信昵称']   = $aUser['profile']['nickname'];
                    $aData[$i]['手机']       = $BindUser['phones'][0]['number'];
                    $aData[$i]['是否准业主'] = $BindUser['ispreowner'] ? '是' : '否';
                    $aData[$i]['业主类型']   = $BindUser['owner_type'];
                    $aData[$i]['购买张数']   = $order['number'];
                    $aData[$i]['购买总金额'] = $order['need_total'];
                    $aData[$i]['下单时间']   = $order['add_time'];
                    $aData[$i]['sn']         = $sn;
                    $aData[$i]['使用时间']   = $snT[$isn];
                    $i++;
                }
            }
        }
        Excel::arr2ExcelDownload($aData);
    }
}
