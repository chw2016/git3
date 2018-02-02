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

    public function show($sName='damon3')
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
        $this->display('./tpl/Wap/default/public/geo.html');
    }

    public function index()
    {
        if (IS_POST) {
            import("@.ORG.BDGeoData.BDGeo");
            $BDGeo = new BDGeo($this->token, 'damon3');
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
    		$res['str']=11;
    		$res['page']=22;
    		$this->ajaxReturn($res);
    	}
    }
}
