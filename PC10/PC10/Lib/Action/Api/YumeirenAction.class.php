<?php
class YumeirenAction extends ApiAction {
    public function _initialize()
    {
        $this->Model = M('Member_idcard');
        $this->token = 'e756d6be1ec4fab3c5920f3a3437160b';
    }
    public function index()
    {
        $sIDCard = $_REQUEST['idcard'];
        $sPhone  = $_REQUEST['phone'];
        if (empty($this->token) or empty($sIDCard) or strlen($sIDCard) > 16) {
            exit($this->error(-1, 'params empty or error'));
        }
        $aData = array(
            'idcard' => $sIDCard,
            'phone'  => $sPhone
        );
        file_put_contents('ymr_idcard.txt', print_r($aData, true), FILE_APPEND);
        if(!$this->checkSign($aData)){
            exit($this->error(-1, 'sign error'));
        };
        if($this->Model->where($aData)->count()){
            exit($this->error(-1, 'idcard already exists'));
        }else{
            if($this->Model->data($aData + array(
                'add_time' => date('Y-m-d H:i:s'),
                'token'    => $this->token
            ))->add()){
                exit($this->success(0));
            }else{
                exit($this->error(-1, 'system error'));
            }
        }
    }
    public function tmpl($token, $openid, $url, $store, $name, $idcard)
    {
        $aTmpl = array(
            'template_id' => 'I7_Muhf-recyYd2KGzibnr-XixQya9KgucVUR8O6C0A',
            'url'         => $url,
            'topcolor'    => '#000',
            'data'        => array(
                'first' => array(
                    'value' => '您已刷卡成功！
',
                    'color' => '#0000',
                ),
                'productType' => array(
                    'value' => '商品名',
                    'color' => '#0000',
                ),
                'name' => array(
                    'value' => $name,
                    'color' => '#0000',
                ),
                'accountType' => array(
                    'value' => '会员卡号',
                    'color' => '#0000',
                ),
                'account' => array(
                    'value' => $idcard,
                    'color' => '#0000',
                ),
                'time' => array(
                    'value' => date('Y年m月d日 H:i'),
                    'color' => '#0000',
                ),
                'remark' => array(
                    'value' => '
请您对 鱼美人'. $store .' 本次的服务质量进行评价，并可获得积分~
欢迎您的再次光临！',
                    'color' => '#0000',
                ),
            )
        );
        tmpl($token, $openid, $aTmpl);
    }

    /*
     *  推送消费记录
     *  @param $store   门店
     *  @param $custom  消费内容
     */
    public function push()
    {
    	$idcard  = $_REQUEST['idcard'];
        $sStore  = $_REQUEST['store'];
        $sCustom = trim(urldecode($_REQUEST['custom']),"'");
        $sCustom = trim($_REQUEST['custom'],'"');
        file_put_contents('ymr.txt', date('Y-m-d H:i:s').$idcard.$sStore.$sCustom.print_r($_SERVER, true), FILE_APPEND);
        if (empty($this->token) or empty($sStore) or empty($sCustom) ) {
            exit($this->error(-1, 'params empty or error'));
        }
        $aData = array(
            'idcard' => $idcard,
            'store'  => $sStore,
            'custom' => $sCustom,
        );

        if ($this->Model->where(array(
            'idcard' => $idcard
        ))->count() == 0) {
            $this->Model->data(array(
                'token' => $this->token,
                'idcard' => $idcard,
                'add_time' => date('Y-m-d H:i:s')
            ))->add();
        }

        //查出openid token 会员卡表tp_member_idcard
        $list     = M('member_idcard')->where(array('idcard'=>$idcard))->find();
        $userInfo = M('Mru_jfb')->where(array('idcard'=>$idcard))->find();
        if (!$userInfo) {
            return false;
        }
        $token=$list['token'];
        $openid=$userInfo['openid'];
        $url=U('Wap/Mrupl/index2',
		      	array(
                      'idcard'=>$idcard,
                      'time'    => time(),
			          'token'=>$token,
			          'openid'=>$openid,
			          'sStore'=>base64_encode($sStore),
			          'sCustom'=>base64_encode($sCustom)
             ),'','',true);


        //$aDatas="<a href='".$url."' >您在鱼美人'.$sStore.'消费了 '.$sCustom.'</a>";
        $sDate      = date('m月d日');
        $sFullDate  = date('Y年m月d日');
        $aCustom    = json_decode($sCustom, true);
        $sMsg       = '';
        foreach ($aCustom as $sK => $sV) {
            $sMsg .= ''.$sK.'   '.$sV."|";
        }
        $sMsg = trim($sMsg, "|");

        $sMsgNotice = "尊敬的用户，您好!\n\n".
                      "您于{$sFullDate}到{$sStore}成功消费以下内容\n\n".
                      "{$sMsg}\n".
                      "您对本次服务满意吗？鱼美人期待您的评价";

        if ($list) {
            $this->tmpl($list['token'], $openid, $url, $sStore, $sMsg, $idcard);
        }
        /*
        if($list) news($list['token'],$openid,array(
            'title'         => '服务提醒通知',
            'description'   => $sMsgNotice,
            'url'           => $url,
            'picurl'        => '',
        ));
        */
        /*微信发送消息*/


        /*
        if(!$this->checkSign($aData)){
            exit($this->error(-1, 'sign error'));
        };
        */
        //逻辑
        exit($this->success(0));
    }
}
