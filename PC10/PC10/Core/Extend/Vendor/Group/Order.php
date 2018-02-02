<?php
/*
 *
 */
class Order
{
    private $token;
    private $openid;

    function __construct($token, $openid)
    {
        $this->token    = $token;
        $this->openid   = $openid;
    }

    /*
     * 下一个订单
     */
    public function createOrder(
        $is_new,//是否新开团
        $single,//是否单独购买
        $gid,//如果非开团，则购买的团id
        $iTotal,
        $iNeedTotal,
        $product_id,
        $tnumber,
        $number,
        $yhjid,
        $use_yu_er,
        $movieid,
        &$iErr,
        &$ret
    ) {
        //购买的商品数量还足够
        $aProductInfo = M('groupbuy_product')->where(array('id' => $product_id))->find();
        //如果已经下过单2件以上就不再能买
        if(M('Group_order')->where(array(
            'openid' => $this->openid,
            'status' => 1
        ))->sum('number') > 1){
                $iErr = 2011;
                return false;
        } ;

        //已经卖出的数量
        $iTotalBuy  = Arr::get(M('Group_order')->field('sum(number) as num')->where(array(
            'token'      => $this->token,
            'product_id' => $product_id,
            'status'     => 1
        ))->find(), 'num');
        if (($iTotalBuy + $number) > $aProductInfo['groupnum']) {
            $iErr = 2001;
            return false;
        }
        $this->Group = M('Group_info');
        $this->Group->startTrans();

        if ($is_new) {//开团
            if(!$gid=M('group_info')->data(array(
                'token'         => $this->token,
                'openid'        => $this->openid,
                'product_id'    => $product_id,
                'tnumber'       => $tnumber,
                'add_time'      => date('Y-m-d H:i:s')
            ))->add()){
                $this->Group->rollback();
                $iErr = 2002;
                return false;
            };
        }else if($gid){//已有团，这是直接购买
            $aGroupInfo = M('group_info')->where(array(
                'id'    => $gid
            ))->find();
            if (!$aGroupInfo) {
                $iErr = 2003;
                $this->Group->rollback();
                return false;
            }
        }else{
            //单独购买
            $gid = 0;
        }
        $orderid = $this->createOrderID();
        $ret['orderid'] = $orderid;
        $ret['gid']     = $gid;
        //如果有使用优惠券，则需要将这个优惠券设置成已使用状态
        Vendor('Group.Yhj');
        $Yhj = new Yhj($this->token, $this->openid);
        if ($yhjid) {
            if(!$Yhj->used($yhjid)){
                $this->Group->rollback();
                return false;
            };
        }
        //如果是后台设置的整点，则送优惠券,送电影票的价格
        $iSendYhjId = null;
        $aMsg = json_decode($aProductInfo['groupmsg'], true);
        $iSinglePrice = Arr::get($aMsg, $tnumber, 0);
        if($aProductInfo['card_time'] && $iSinglePrice){
            foreach(explode(',', $aProductInfo['card_time']) as $time){
                if ($time == date('H:i')) {
                    if(!$iSendYhjId=$Yhj->send($iSinglePrice, 2, 1)){
                        $this->Group->rollback();
                        return false;
                    };
                }
            };
        }
        if(!$oid=M('Group_order')->data(array(
            'token'         => $this->token,
            'openid'        => $this->openid,
            'orderid'       => $orderid,
            'product_id'    => $product_id,
            'gid'           => $gid,
            'tnumber'       => $tnumber,
            'number'        => $number,
            'yhjid'         => $yhjid,
            'use_yu_er'     => $use_yu_er,
            'total'         => $iTotal,
            'need_total'    => max(0, $iNeedTotal),
            'single'        => $single,
            'sn'            => $this->createSN(8, $number),
            'movieid'       => $movieid,
            'send_yhj_id'   => $iSendYhjId,
            'add_time'      => date('Y-m-d H:i:s'),
            'status'        => (int)$single
        ))->add()){
            $this->Group->rollback();
            return false;
        };

        $this->Group->commit();
        return $oid;
    }

    public function createOrderID()
    {
        $year_code = array('A','B','C','D','E','F','G','H','I','J');
        return $year_code[intval(date('Y'))-2010].
            strtoupper(dechex(date('m'))).date('d').
            substr(time(),-5).substr(microtime(),2,5).sprintf('d',rand(0,99));
    }

    public function getIdByOrderid($orderid)
    {
        $this->Order = M('Group_order');
        return M('Group_order')->where(array('orderid' => $orderid))->getField('id');
    }

    /*
     * 结束|处理一个订单
     */
    public function dealOrder($iOrderID)
    {
        Vendor('Group.Yhj');
        $Yhj        = new Yhj($this->token, $this->openid);
        $this->Order = M('Group_order');
        //开启事务
        $this->Order->startTrans();
        if($aOrder=M('Group_order')->where(array('id' => $iOrderID))->find()){
            //如果有使用优惠券
            $iYhj = 0;
            if($aOrder['yhjid']){
                $aYhjInfo = $Yhj->get($aOrder['yhjid']);
                //下单前就已经给设置成已用
                /*
                if(!$Yhj->used($aOrder['yhjid'])){
                    $this->Order->rollback();
                    $Yhj->unused($aOrder['yhjid']);
                    return 1002;//优惠券使用失败
                };
                */
                $iYhj = $aYhjInfo['number'];
            }
            //如果有使用余额
            //余额信息应为总额 - 优惠券 - 剩余付款
            $iYuEMoney = $aOrder['total'] - $iYhj - $aOrder['need_total'];
            if ($aOrder['use_yu_er'] && $iYuEMoney > 0) {
                $aUser = M('Vanke_user')->where(array(
                   'token'         => $this->token,
                   'openid'        => $this->openid,
                ))->find();
                if ($aUser['money'] < $iYuEMoney) {
                    $this->Order->rollback();
                    if ($aOrder['yhjid']) {
                        $Yhj->unused($aOrder['yhjid']);
                    }
                    return 1003;//余额不够
                }
                if(!M('Vanke_user')->where(array(
                   'token'         => $this->token,
                   'openid'        => $this->openid
                ))->data(array('money' => $aUser['money'] - $iYuEMoney))->save()){
                    $this->Order->rollback();
                    if ($aOrder['yhjid']) {
                        $Yhj->unused($aOrder['yhjid']);
                    }
                    return 1004;//使用余额失败
                };
            }
            //如果有整点送优惠券，则将其设置成可用
            if ($aOrder['send_yhj_id']) {
                if(!$Yhj->unused($aOrder['send_yhj_id'])){
                    $this->Order->rollback();
                    if ($aOrder['yhjid']) {
                        $Yhj->unused($aOrder['yhjid']);
                    }
                    return 1005;//更新订单失败
                };
            }
            //订单结束
            if(!$aOrder['single'] AND !M('Group_order')
                ->where(array('id' => $iOrderID))
                ->data(array('status' => 1))
                ->save()
            ){
                $this->Order->rollback();
                if ($aOrder['yhjid']) {
                    $Yhj->unused($aOrder['yhjid']);
                }
                return 1006;//更新订单失败
            };
            //判断团是否结束
            $iHisNum = (int)M('Group_order')
                ->where(array(
                    'status'=> 1,
                    'token' => $this->token,
                    'gid'   => $aOrder['gid']
                ))
                ->field('distinct openid')
                ->count();
            $aGroupInfo = M('Group_info')->where(array(
                'token' => $this->token,
                'id'    => $aOrder['gid']
            ))->find();
            WL(print_r($aGroupInfo, true) . '|' . $iHisNum, 'tmpLog');
            if (
                !$aOrder['single'] AND
                ($aGroupInfo['tnumber'] <= $iHisNum) && $aGroupInfo['status'] == 0) {
                if(!$aGroupInfo = M('Group_info')->where(array(
                    'token' => $this->token,
                    'id'   => $aOrder['gid']
                ))->data(array('status' => 1))->save()){
                    $this->Order->rollback();
                    if ($aOrder['yhjid']) {
                        $Yhj->unused($aOrder['yhjid']);
                    }
                    return 1007;
                };
            }
            $this->Order->commit();
            return true;
        }else{
            return 1001;//订单不存在
        };
    }

    public function createSN($length, $number)
    {
        $aSN = array();
        for ($i = 0; $i < $number; $i++) {
            $aSN[] = $this->createSN2($length);
        }
        return ',' . implode(',', $aSN) . ',';
    }

    /*
     *  生成串码
     */
    public function createSN2($length = 12)
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < $length;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + $lengh ] ) ) - $g & 0x1F ],
            $f++
        );
        return $d;
    }


}
