<?php
class YumeirenAction extends CliAction {

    public $token = 'e756d6be1ec4fab3c5920f3a3437160b';

    public function modify()
    {
        return;
        $Model = M('Mru_pl');
        foreach($Model->where(array('id' => 109))->select() as $Row){
            $Model
                ->where(array('id' => $Row['id']))
                ->data(array('content' => base64_encode($Row['content'])))
                ->save();
        }
    }

    public function index()
    {
        set_time_limit(0);
        $aArr = array(
/*
            'a.xlsx',
            'b.xlsx',
            'c.xlsx',
            'd.xlsx',
            'e.xlsx',
            'f.xlsx',
            'g.xlsx',
		*/
            'h.xlsx',
            'i.xlsx',
            'j.xlsx'
        );

        $this->Model = M('Member_idcard');
        $this->token = 'e756d6be1ec4fab3c5920f3a3437160b';

        foreach ($aArr as $excel) {
            $aData = Excel::excel2Arr('/wapwei/web/default/v/public_html/excel/'.$excel);
            if ($aData) {
                foreach($aData as $v) {
			if(count($v) <2 || !$v[1]){continue;}
                    if($this->Model->where(array(
                        'token' => $this->token,
                        'idcard' => $v[1]
                    ))->count() > 0){
                        file_put_contents('push.log', 'old card:' . $v[1] . '_' . $v[0] . "\r\n", FILE_APPEND);
                        continue;
                    };
                    $this->Model->data(array(
                        'token' => $this->token,
                        'idcard' => $v[1],
                        'phone'  => $v[0],
                        'add_time' => date('Y-m-d H:i:s')
                    ))->add();
                    file_put_contents('push.log', 'new card:' . $v[1] . '_' . $v[0] . "\r\n", FILE_APPEND);
                }
            }
        }
    }

    public function fixBug()
    {
    	die;
        foreach(M('mru_jfb2')->where(array('id' => array('lt', 939)))->field('id,name,sex,tel,address,pic')->select() as $Row){
            $User = M('mru_jfb')->where(array('id' => $Row['id']))->find();
            $aData = array();
            if ($User['name'] == '') {
                $aData['name'] = $Row['name'];
            }
	    if ($User['pic'] == '') {
                $aData['pic'] = $Row['pic'];
            }
	    /*
            $aData['sex'] = $Row['sex'];
            if ($User['tel'] == '') {
                $aData['tel'] = $Row['tel'];
            }
            if ($User['address'] == '东莞') {
                $aData['address'] = $Row['address'];
            }
	    */
            if(M('mru_jfb')->where(array('id' => $Row['id']))->data($aData)->save()){
                echo "修复成功<br />";
            }else{
                echo "修复失败<br />".print_r($Row, true) . print_r($aData, true);
            };
        };
    }

    public function fixBug2()
    {
        die;
        foreach(M('mru_jfb')
            ->where(array('id' => array('gt', 938)))
            ->field('id,name, token, openid')
            ->select() as $Row
        ){
            if ($Row['name'] == '') {
                $User = M('wxusers')->field('nickname')->where(array('token' => $Row['token'], 'openid' => $Row['openid']))->find();
                if(M('mru_jfb')->where(array('id' => $Row['id']))->save(array('name' => $User['nickname']))){
                    file_put_contents('update_log', $Row['id']. '|', FILE_APPEND);
                    echo '成功';
                }else{
                    echo '失败';
                };
            }
        }
    }

    public function sn()
    {
    	die;
        foreach(M('mru_yhj2')->where(array('token' => 'e756d6be1ec4fab3c5920f3a3437160b'))->select() as $Row){
            $sn = abs(crc32(microtime(true).rand(100,999)));
            if (strlen($sn) < 10) {
                $sn = str_pad($sn, 10, '0', STR_PAD_RIGHT);
            }else{
                $sn = substr($sn, 0, 10);
            }
            if(M('mru_yhj2')->where(array('id' => $Row['id']))->save(array('sn_code' => $sn))){
                echo "成功".print_r($Row, true);
            }else{
                echo "失败";
            };
        }
    }

    public function cart()
    {
        $token = 'e756d6be1ec4fab3c5920f3a3437160b';
        foreach(M('product_cart_new')->field('id,truename, wecha_id')->where(array('token' => $token))->select() as $Row){
            $User = M('wxusers')->field('nickname')->where(array('token' => $token, 'openid' => $Row['wecha_id']))->find();
            if ($Row['truename'] == '' || $Row['truename'] == '-') {
                if ($User['nickname']) {
                    M('product_cart_new')
                        ->where(array('token' => $token, 'id' => $Row['id']))
                        ->save(array('truename' => $User['nickname']));
                }
            }
        };
    }
}
