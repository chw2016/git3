<?php
class VankeBaseAction extends BaseAction{

    protected function _initialize(){
        parent::_initialize();
        //先跳转，url不允许存有openid信息

        if ($_GET['openid']) {
            unset($_SESSION['WxvankeUser']);
            unset($_SESSION['WxvankeBindUser']);
            //return $this->redirect(U('Wap/Vanke/index'));
        }

        //根据openid获取用户信息
        $this->Vanke = new Vanke();
	$Vanke =  $this->Vanke;
        if(!$aVankeUser = $_SESSION['WxvankeUser']){
            $aVankeUser = $Vanke->getUserInfo($this->openid);
            if (200 != $aVankeUser['code'] || !$this->openid) {
                //失败
                //exit('获取用户信息失败');
                header('Location:'.sprintf('http://szm.vanke.com/shop/pay/checkwx.php?cfrom=%s', urlencode(U('index', array('token' => $this->token), true,false,true))));
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
        //获取用户家属信息
        //unset($_SESSION['WxvankeQSInfo']);
        if(!$aVankeQSInfo = $_SESSION['WxvankeQSInfo']){
            $aVankeQSInfo = $Vanke->getUserQSInfo($aVankeBindUser['data']['customer_id']);
            if (false === $aVankeQSInfo) {
                //失败
                //exit('没有业主信息');
                //header('Location:'.sprintf('http://szm.vanke.com/weixin/index.html?openid=%s&cfrom=%s',$this->openid, urlencode(U('Vanke/index', array('openid' => $this->openid, 'token' => $this->token), true,false,true))));
                $aVankeQSInfo = array();
                //return;
            }
            $_SESSION['WxvankeQSInfo'] = $aVankeQSInfo;
        }
        $this->assign('customer_id', $aVankeBindUser['data']['customer_id']);
        $this->assign('WxvankeQSInfo', $aVankeQSInfo);
        $this->assign('WxvankeBindUser', $aVankeBindUser['data']);
        $this->assign('WxvankeUser', $aVankeUser['data']);
        //活动房产信息
        if($aVankeBindUser['data']['rooms']){
            $this->rooms = $aVankeBindUser['data']['rooms'][0];
        }else{
            $this->rooms = array();
        };
        $this->assign('rooms', $this->rooms);
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
    }

    /*
     *  获取jssdk参数
     */
    public function jssdk($sUrl='')
    {
        return $this->Vanke->getJSSDKConfig($sUrl ? $sUrl : __URL__);
    }
}
