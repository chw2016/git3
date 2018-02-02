<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DamonAction extends BaseAction {

	public function _initialize(){
		parent::_initialize();
        $this->assign('token', $this->token);
    }

    /*
     * 接口统一调用方法
     */
    public function beforeApi(){
        Vendor('Socket.socket');
        $this->Socket = new Socket('121.41.56.183', 8585);
        $this->imei = $_REQUEST['imei'];
        if(!$this->imei) {
            exit($this->encode(array('code'=>-1,'msg'=>'非法操作')));
        }
    }

    /*
     * 调用API操作
     */
    public function doApi(){
        $sType = 'photo';
        $sValue= '1';
        $this->beforeApi();
        $sendData['host'] = $this->openid;
        $sendData['sendTo'] = $this->imei;
        $sendData['type'] = $sType;
        $sendData['value'] = $sValue;

        $senddataSign = json_encode($sendData);
        $sign = md5('*&@@^$)!@FSDKIlk123DWEWQa'.$senddataSign);
        $sendData['sign'] = $sign;
        $sReturn = $this->Socket->send(json_encode($sendData));
        $aReturn = json_decode($sReturn, true);
        if (strlen($sReturn) == 0 or $aReturn['status'] != 0) {
            $iStatus = isset($aReturn['status']) ? $aReturn['status'] : -1;
            return $this->encode(array('code'=>$iStatus,'msg'=>'操作失败'));
        }else{
            return $this->encode(array('code'=>0,'msg'=>'成功','msg'=>$sReturn));
        }
    }


    public function show($sName='damon4')
    {

        $iGeoTableId = M('Geo_table')->where(array(
            'token' => $this->token,
            'geo_name' => $sName
        ))->getField('geotable_id');
        $sAK = M('Geo')->where(array(
            'token' => $this->token
        ))->getField('ak');
        $this->assign(array(
            'geotable_id' => $iGeoTableId,
            'ak'          => $sAK
        ));
        $username = M('User')->where(array('id' => 100))->getField('name');
        $this->assign('username', $username);
        $this->display('./tpl/Wap/default/public/geo.html');
    }

    public function test()
    {
        $this->display();
    }

    public function index()
    {
        if (IS_POST) {
            import("@.ORG.BDGeoData.BDGeo");
            $BDGeo = new BDGeo($this->token, 'damon4');
            if($BDGeo->addPOI(
                $_POST['name'],
                $_POST['latitude'],
                $_POST['longitude']
            )){
                $this->success2("地点点添加成功", U("Damon/index", array("token" => $this->token)));
            }else{
                $this->error2('创建失败', U("Damon/index", array("token" => $this->token)));
            };
        }else{

            $this->display('./tpl/Wap/default/public/geo_add.html');
        }
    }

    public function update()
    {
        $_POST['id'] = 864535602;
        $_POST['name'] = '天安门修改';
        $_POST['address'] = '天安门修改';
        $_POST['latitude'] = '39.9768881';
        $_POST['longitude'] = '116.323781';

        import("@.ORG.BDGeoData.BDGeo");
        $BDGeo = new BDGeo($this->token, 'damon3');
        if($BDGeo->updatePOI(
            $_POST['id'],
            $_POST['name'],
            $_POST['address'],
            $_POST['latitude'],
            $_POST['longitude']
        )){
            $this->success2("地点点修改成功", U("Damon/index", array("token" => $this->token)));
        }else{
            $this->error2('修改失败', U("Damon/index", array("token" => $this->token)));
        };
    }

    public function delete()
    {
        $_POST['id'] = 864535602;

        import("@.ORG.BDGeoData.BDGeo");

        $BDGeo = new BDGeo($this->token, 'damon3');
        if($BDGeo->deletePOI($_POST['id'])){
            $this->success2("地点点删除成功", U("Damon/index", array("token" => $this->token)));
        }else{
            $this->error2('删除失败', U("Damon/index", array("token" => $this->token)));
        };
    }

    public function ajax(){
    	if(IS_AJAX){
    		$data=M('mru_mdian')->where(array('baiduid'=>$_POST['baiduid']))->field("id,token")->find();

    		$res['id']=$data['id'];
    		$res['token']=$data['token'];
    		$res['openid']=$_GET['openid'];
    		$this->ajaxReturn($res);
    	}
    }
}
