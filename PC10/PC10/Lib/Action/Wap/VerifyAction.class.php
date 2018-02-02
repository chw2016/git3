<?php
class VerifyAction extends BaseAction{

    /*
     * Tpl Dir
     */
    private $Vanke;
    protected $_sTplBaseDir = 'Wap/default/vanke';

    protected function _initialize(){
        parent::_initialize();
        $this->Tour_info    = M('Tour_info');
    }

    public function index()
    {
        if ($code=$_GET['code']) {
            //订单信息
            if($_GET['verify']){
                WL('verifyData:' . print_r($_REQUEST, true) . print_r($_SERVER, true), 'vanke_verify.log');
                $Order = M('Yuyue_order')->where(array(
                    'type' => 1,
                    'sn'   => array('like', '%,' . $code . ',%')
                ))->find();
                $aSN  = explode(',', $Order['sn']);
                $aTBaseSN = explode(',', $Order['sn_used_time']);
                $aTSN= array();
                foreach ($aSN as $k => $sn) {
                    if ($sn) {
                        if (!$aTBaseSN[$k] && $sn == $code) {
                            $aTSN[] = date('Y-m-d H:i:s');
                        }else{
                            $aTSN[] = $aTBaseSN[$k];
                        }
                    }
                }
                $Order = M('Yuyue_order')->where(array(
                    'sn'   => array('like', '%,' . $code . ',%')
                ))->data(array('sn_used_time' => ',' . implode(',', $aTSN) . ','))->save();
            }

            $msg = M('Yuyue_order')->where(array(
                'status' => 1,
                'sn'   => array('like', '%,' . $code . ',%')
            ))->find();

            $aSN2 = explode(',', $msg['sn']);
            $aTSN2 = explode(',', $msg['sn_used_time']);
            $time = null;
            foreach ($aSN2 as $key => $sn) {
                if ($sn == $code && $aTSN2[$key]) {
                    $time = $aTSN2[$key];
                }
            }
            $this->assign('time', $time);

            //商品
            //根据openid获取用户信息
            $this->Vanke = $Vanke = new Vanke();
            $aVankeUser = $Vanke->getUserInfo($msg['openid']);
            if (200 == $aVankeUser['code']) {
                $aVankeUser = $aVankeUser['data'];
            }
            /*
            $aVankeBindUser = $Vanke->getVankeBindUser($this->openid);
            if (200 == $aVankeBindUser['code']) {
                $aVankeBindUser = $aVankeBindUser['data'];
            }
            */

            $source = $msg['source'];
            if ($source == 1) {
                $Model = M('Yuyue_goods');
            }else if($source == 2){
                $Model = $this->Tour_info;
            }
            $aProduct = $Model->where(array(
                'id'    => $msg['product_id']
            ))
            ->find();
            $this->assign(array(
                'msg'           => $msg,
                'vankeuser'     => $aVankeUser,
                'bindUser'      => $aVankeBindUser,
                'product'       => $aPro,
                'code'          => $code
            ));
        }
        $this->UDisplay('yy_verify');
    }
}

