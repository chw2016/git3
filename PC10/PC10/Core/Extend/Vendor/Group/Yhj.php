<?php
/*
 *
 */
class Yhj
{
    private $token;
    private $openid;
    const STATUS_NORMAL = 0;
    const STATUS_USED   = 1;
    const STATUS_PREUSE = 2;//待激活
    function __construct($token, $openid)
    {
        $this->token    = $token;
        $this->openid   = $openid;
    }

    /*
     *  获取优惠券的信息
     */
    public function get($id)
    {
        return M('Yhj')->where(array(
            'token'     => $this->token,
            'openid'    => $this->openid,
            'id'        => $id
        ))->find();
    }

    public function select($status=0, $iPage=null, $iPageNum=15)
    {
        $extra = array();
        if ($status !== null) {
            $extra['status'] = $status;
        }
        $Yhj = M('Yhj')->where($extra + array(
            'token'     => $this->token,
            'openid'    => $this->openid
        ))->order('status asc, id desc');
        if ($iPage) {
            $Yhj->limit(($iPage -1) * $iPageNum . ',' . $iPageNum);
        }
        return $Yhj->select();
    }

    /*
     *  使用优惠券的信息
     */
    public function send($number=0, $iStatus=0, $from=0)
    {
        return M('Yhj')->data(array(
            'token'     => $this->token,
            'openid'    => $this->openid,
            'sn'        => $this->createSN(),
            'status'    => $iStatus,
            'number'    => $number,//金额
            'get_from ' => $from,//金额
            'add_time'  => date('Y-m-d H:i:s')
        ))->add();
    }

    /*
     *  还原使用优惠券的信息
     */
    public function unused($id)
    {
        return M('Yhj')->where(array(
            'token'     => $this->token,
            'openid'    => $this->openid,
            'id'        => $id
        ))->data(array(
            'status'    => self::STATUS_NORMAL,
            'use_time'  => null
        ))->save();
    }

    /*
     *  使用优惠券的信息
     */
    public function used($id)
    {
        return M('Yhj')->where(array(
            'token'     => $this->token,
            'openid'    => $this->openid,
            'id'        => $id
        ))->data(array(
            'status'    => self::STATUS_USED,
            'use_time'  => date('Y-m-d H:i:s')
        ))->save();
    }
    /*
     *  生成串码
     */
    public function createSN($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }

}
