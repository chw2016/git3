<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-9
 * Time: 下午3:14
 */
class IntelAction extends BaseAction{


    protected function _initialize(){
        parent::_initialize();
        $this->oIntelImei = M('Intel_imei');
        $this->oIntelligentDeviceUsersModel = M('Intelligent_devices_users');
        $this->imei = $_REQUEST['imei'];
        $this->token = $_REQUEST['token'];
	/*
        if ($_REQUEST['nodata'] != 1 AND  !$this->getUserBind()) {
            //用户没有绑定过设备
            $this->redirect('Intel/nobind', array('nodata' => 1));
            die;
        }
	*/
        $this->assign('config', Arr::changeIndexToKVMap($this->getDefaultMessage(), 'key', 'value'));
    }

    public function nobind()
    {
        $this->display();
    }

    public function recharge()
    {
        if (IS_POST) {
            $type   =   $_REQUEST['type'];
            $price  =   $_REQUEST['price'];
            $imei   =   $_REQUEST['imei'];
            $iccid  =   $_REQUEST['iccid'];
            $iOrderid = createOrderID();
            if(M('intel_recharge')->data(array(
                'token' => $this->token,
                'openid'    => $this->openid,
                'orderid'   => $iOrderid,
                'imei'  => $imei,
                'iccid' => $iccid,
                'type'  => $type,
                'price' => $price,
                'add_time'  => date('Y-m-d H:i:s')
            ))->add()){
                $this->jret(0, '', array('orderid' => $iOrderid));
            }else{
                $this->jret(-1);
            };
        }else{
            $this->assign('bindList', $aBindList=$this->getBindList($this->openid));
            $this->display();
        }
    }

    public function download()
    {
        header('Content-type:text/html;charset=utf-8');
        //$file_name  =   str_replace(C('site_url'), '', $_GET['filename']);
	$file_name = './upload/devicevideo/201507/55af084ace362.mp4';
        //用以解决中文不能显示出来的问题
        $file_name      = iconv('utf-8','gb2312',$file_name);
        $file_sub_path  = $_SERVER['DOCUMENT_ROOT'].'/';
        $file_path      = $file_sub_path.$file_name;
        //首先要判断给定的文件存在与否
        if(!file_exists($file_path)){
            die('没有该文件文件');
        }
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        Header("Content-Disposition: attachment; filename=".time().'.mp4');
        $buffer=1024;
        $file_count=0;
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=fread($fp,$buffer);
            $file_count+=$buffer;
            echo $file_con;
        }
        fclose($fp);
    }

    /*
     *  设置缓存
     */
    public function getCache($sIMEI, $sType)
    {
        $sKey  = md5(sprintf('%s%s', $sIMEI, $sType));
        $mCache = false;
        if($mCache = cache($sKey)){
            cache($sKey, null);
        }
        return $mCache;
    }

    public function removeCache($sIMEI, $sType)
    {
        $sKey  = md5(sprintf('%s%s', $sIMEI, $sType));
        cache($sKey, null);
    }

    /*
     * 用户是否绑定
     */
    public function getUserBind($imei,$openid){
        return $this->oIntelligentDeviceUsersModel->where(array(
            'imei'   => $imei,
            'openid' => $openid,
            'token'  => $this->token,
            'is_on' => 1
        ))->find();
    }


    /*
     * 获取当前用户绑定结果
     */
    public function getUserBindDeviceOn($openid){
        return $this->oIntelligentDeviceUsersModel->where(array(
            'openid'=>$openid,
            'token'=>$this->token,
            'is_on'=>1
        ))->find();
    }


    /*
     * 获取绑定设备列表
     */
    public function getBindList($openid){
        return $this->oIntelligentDeviceUsersModel->where(array(
            'openid'=>$openid,
            'token'=>$this->token,
            'is_on' => 1
        ))->select();
    }

    public function setcenter(){
        if (IS_AJAX) {
            set_time_limit(0);
            $mCallBack = false;
            $aRet = array('code' => -1);
            switch ($_REQUEST['type']) {
                case 'guard':
                    //$mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    $aRet = $this->guard($_REQUEST['is_close']);
                    break;
                case 'road':
                    //$mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    $aRet = $this->road($_REQUEST['is_real']);
                    break;
                case 'shake':
                    //$mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    $aRet = $this->shake($_REQUEST['pulsation']);
                    break;
                case 'roadrange':
                    //$mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    $aRet = $this->roadrange($_REQUEST['times']);
                    break;
                case 'carType':
                    //$mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    httpMethod(U('Api/Paipai/saveMsg', array(
                        'imei'   => $this->imei,
                        'type'  => $_REQUEST['type'],
                        'value' => $_REQUEST['value']
                    ), true, false, true));
                    $aRet = $this->carType($_REQUEST['cattype']);
                    break;
                case 'photo':
                    //$mCallBack = C('site_url').$this->getCache($this->imei, $_REQUEST['type']);
                    $aRet = $this->photo($_REQUEST['picsize']);
                    break;
                case 'video':
                    //$mCallBack = C('site_url').$this->getCache($this->imei, $_REQUEST['type']);
                    $aRet = $this->video($_REQUEST['videotime']);
		    break;
                case 'hideBtn'://隐藏开关
                    $sCacheKey = 'PAIPAI_GEO_'.$this->imei;
                    cache($sCacheKey, null);
                    $aRet = $this->hideBtn($_REQUEST['type'], $_REQUEST['value']);
                    break;
                case 'fadongji'://发动机
                case 'chepaihao'://车牌号
                case 'chejiahao'://车架号
                    $aRet = $this->hideBtn($_REQUEST['type'], $_REQUEST['value']);
                    break;
            }
            $aRet = json_decode($aRet, true);
            if ($aRet['code'] == 0) {
                echo ($this->encode(array(
                    'code'  => $aRet['code'],
                    'msg'   => '',
                    'data'  => ''
                )));
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'操作失败'));
            }
            return;
        }else{
            #default
            $this->assign('cartype',$this->getCarType(0));
            $this->assign('bindList', $aBindList=$this->getBindList($this->openid));
            if (!$this->imei AND count($aBindList) > 0) {
	    	$Url =  U('Intel/setcenter', array('token' => $this->token, 'imei' => $aBindList[0]['imei'], 'openid' => $this->openid));
		$this->redirect($Url);
                return;
            }
            $this->display();
        }
    }

    /*
     * 设置开关
     */
    public function hideBtn($key, $value)
    {
        if($Message = M('Intel_message')->where(array(
            'token' => $this->token,
            'imei'  => $this->imei,
            'key'   => $key
        ))->find()){
            if(M('Intel_message')->where(array(
                'token' => $this->token,
                'imei'  => $this->imei,
                'key'   => $key
            ))->data(array('value' => $value))->save()){
                return json_encode(array('code' => 0));
            }else{
                return json_encode(array('code' => -1));
            };
        }else{
            if(M('Intel_message')->data(array(
                'token' => $this->token,
                'imei'  => $this->imei,
                'key'   => $key,
                'value' => $value
            ))->add()){
                return json_encode(array('code' => 0));
            }else{
                return json_encode(array('code' => -1));
            }
        }
    }

    /*
     *  获取结果
     */
    public function getResult()
    {
        if (IS_AJAX) {
            set_time_limit(0);
            $mCallBack = false;
            $file = '';
            switch ($_REQUEST['type']) {
                case 'guard':
                    $mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    //$aRet = $this->guard($_REQUEST['is_close']);
                    break;
                case 'road':
                    $mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    //$aRet = $this->road($_REQUEST['is_real']);
                    break;
                case 'shake':
                    $mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    //$aRet = $this->shake($_REQUEST['pulsation']);
                    break;
                case 'roadrange':
                    $mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    //$aRet = $this->roadrange($_REQUEST['times']);
                    break;
                case 'carType':
                    $mCallBack = $this->getCache($this->imei, $_REQUEST['type']);
                    //$aRet = $this->carType($_REQUEST['cattype']);
                    break;
                case 'photo':
                    $mCallBack = C('site_url').$this->getCache($this->imei, $_REQUEST['type']);
                    //$aRet = $this->photo($_REQUEST['picsize']);
                    break;
                case 'video':
                    $file      = $this->getCache($this->imei, $_REQUEST['type']);
                    $mCallBack = C('site_url').$file;
                    //$aRet = $this->video($_REQUEST['videotime']);
                    break;
            }
            if ($aRet['code'] == 0) {
                echo ($this->encode(array(
                    'code'  => (int)($mCallBack === false or $mCallBack == C('site_url')),
                    'msg'   => '',
                    'data'  => $mCallBack,
                    'file'  => $file
                )));
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'操作失败'));
            }
            return;
        }
    }

    /*
     *配置第二页
     * */
    public function setcentertwo(){
        $this->assign('bindList', $aList=$this->getBindList($this->openid));
        $this->assign('imei',
            $_REQUEST['imei'] ?
            $_REQUEST['imei'] :
            ($aList ? $aList[0]['imei'] : null)
        );
        $this->display();
    }

    /*
     * 轨迹回放页
     * */
    public function locus(){
        $this->assign('aBindList', $aBindList=$this->getBindList($this->openid));
        if ($this->imei) {
            //if (IS_AJAX) {
                //$this->beforeApi();
                //$aMsg = json_decode($this->doApi('acc', 1),true);

                $aHis = M('Intel_geo')
                    ->table('tp_intel_geo force index (addtime_imei)')
                    ->where(array(
                    'token' => $this->token,
                    'imei'  => $this->imei,
                    'add_time' => array('gt', date('Y-m-d H:i:s', strtotime('-61 seconds')))
                ))->order('id desc')->find();

                //为了查询是否有上报地理位置
                $aLatlng = array(null,null);
                if ($aHis) {
                    $aLatlng = transgps($aHis['latitude'], $aHis['longitude'], true);
                }

                #最后的轨迹
                /*
                echo $this->encode(array(
                    //20s内为在线，1分钟内为休眠,否则为离线
                    'alive' => time() - strtotime($aHis['add_time']),
                    'his'   => array(
                        'acc'        => $aHis['acc'],
                        'latitude'   => $aLatlng[0],
                        'add_time'   => isset($aHis['add_time']) ? $aHis['add_time'] : null,
                        'longitude'  => $aLatlng[1]
                    )
                ));
                */

                $iTime = time() - strtotime($aHis['add_time']);
                if($iTime <= 20 && $aHis['acc']== '1'){
                    $status = '正常开机';
                }else if($iTime <= 60){
                    $status = '休眠';
                }else{
                    $status = '离线';
                }
                $this->assign('record', array(
                    //10s内为在线，1分钟内为休眠,否则为离线
                    'alive' => time() - strtotime($aHis['add_time']),
                    'status'=> $status,
                    'his'   => array(
                        'acc'        => $aHis['acc'],
                        'latitude'   => $aLatlng[0],
                        'add_time'   => isset($aHis['add_time']) ? $aHis['add_time'] : null,
                        'longitude'  => $aLatlng[1],
                        'latlng'     => $aLatlng[1] . ',' . $aLatlng[0],
			'lnglat'     => $aLatlng[0] . ',' . $aLatlng[1]
                    )
                ));
                //return;
            //}
        }else{
            if (count($aBindList) > 0) {
                $this->redirect(U('Intel/locus', array('token' => $this->token, 'openid' => $this->openid, 'imei' => $aBindList[0]['imei'])));
            }
        }
        $this->display();
    }

    /*
     * 地图展示页
     * */
    public function map(){
        $this->assign('bindList', $this->getBindList($this->openid));
        $this->assign('imei', $_GET['imei']);
        if (IS_AJAX) {

            $iPage = max(1, $_REQUEST['page']);
            $iNum  = 200;
            $_GET['beginTime'] = empty($_GET['beginTime']) ? date('Y-m-d H:i', time() - 3600) : $_GET['beginTime'];
            $_GET['endTime'] = empty($_GET['endTime']) ? date('Y-m-d H:i', time()) : $_GET['endTime'];
            $aData = M('Intel_geo')
                ->field('id, bd_longitude as longitude, bd_latitude as latitude, add_time')
                ->where(array(
                    'hide'     => 0,
                    'imei'     => $_GET['imei'],
                    'add_time' => array('between', $_GET['beginTime'].','.$_GET['endTime'])
                ))
                ->limit(($iPage-1) * $iNum.','.$iNum)
                ->select();

            if (count($aData) > 0) {
                $Latlng = new Latlng();
                $aGEO = self::transfer($aData, $Latlng, 2);
                if (count($aGEO) > 0) {
                    exit(json_encode(array('code' => 0, 'data' => $aGEO)));
                }else{
                    exit(json_encode(array('code' => -1)));
                }
            }else{
                exit(json_encode(array('code' => -1)));
            }
        }

        $this->assign('param', $_GET);
        $this->assign('imei', $_GET['imei']);
        $this->display('./tpl/Wap/default/Intel_map.html');
    }

    public static function transfer($aData=array(), $Latlng, $type=1)
    {
        /*
        foreach ($aData as $k => $v) {
            if ($type == 1) {
                $aRet = $Latlng->transform2Mars($v['latitude'], $v['longitude']);
            }else{
                $aRet = transgps($v['latitude'], $v['longitude'], true);
            }
            $aData[$k]['latitude']  = $aRet[0];
            $aData[$k]['longitude'] = $aRet[1];
        }
        */
        return $aData;
    }


    /*
     * 地图展示页
     * */
    public function position(){
        /*
        $this->beforeApi();
        $aRet = json_decode($this->doApi('position', 1), true);
        */
        $aRet = array('code'=>0,'msg'=>'成功','msg'=>$sReturn);
        exit(json_encode($aRet));

        /*
        if ($aRet['code'] != 0 ) {
            $this->assign('sendfail', 1);
        }
        $this->display();
        */
    }

    /*
     *  获取设备当前位置
     */
    public function getPosition($iOn=0){
        //$sPos = $this->getCache($this->imei, 'position');
        //$sPos = '113.84012166666668,22.704970000000003';
        //$aData = explode(',', $sPos);
        $Geo = M('Intel_geo')->where(array('imei' => $this->imei))->order('id desc')->find();
        $aData = array();
        if ($Geo) {
            $sPos = $Geo['longitude'] . ',' . $Geo['latitude'];
            $aData = explode(',', $sPos);
        }else{
            $sPos = false;
        }

        if (false == $sPos) {
            exit(json_encode(array('status' => -1)));
        }else{
            $Latlng = new Latlng();
            $aLatlng = $Latlng->transform2Mars($aData[1], $aData[0]);
            exit(json_encode(array(
                'status' => 0,
                'data' => array(
                    'lat' => $aLatlng[0],
                    'lng' => $aLatlng[1]
                ))));
        }
    }


    /*
     * 绑定设备
     */
    public function bind(){
        $sIMEI     = $_REQUEST['imei'];
        $aBind  = $this->getUserBind($sIMEI, $this->openid);
        if (count($aBind)) {
            $this->redirect('Intel/setcenter', array(
                'openid' => $this->openid,
                'token'  => $this->token,
                'imei'   => $sIMEI
            ));
            return;
        }
        if(IS_AJAX){
            if (!$sIMEI) {
                exit($this->encode(array('code'=>-2,'msg'=>'异常')));
            }

            #给那个用户发送消息,审核
            $Row = $this->oIntelligentDeviceUsersModel->where(array(
                'imei' => $sIMEI,
                'is_on' => 1
            ))->find();

            //如果有的话，先给用户发消息且机主没有拒绝
            if ($Row) {
                if ($Row['callback'] == 2 && $Row['ask_openid'] == $this->openid) {//已经拒绝
                    exit($this->encode(array('code'=>-2,'msg'=>'对不起，你的申请已经被拒绝')));
                }else if($Row['callback'] == 0 || $Row['callback'] == 1){//新申请，则发消息申请
                    $sMsgNotice = "您好!\n\n".
                      "您的设备号【".$Row['imei']."】被用户【".$_POST['phone']."】申请绑定\n\n".
                      "点击进行同意/拒绝操作";

                    news($Row['token'],$Row['openid'],array(
                        'title'         => '设备申请绑定',
                        'description'   => $sMsgNotice,
                        'url'           => U('Wap/Intel/bind_agree', array(
                            'token' => $Row['token'],
                            'id'    => $Row['id']
                        ), true, false, true),
                        'picurl'        => '',
                    ));

                    $this->oIntelligentDeviceUsersModel->where(array(
                        'imei' => $sIMEI,
                        'is_on' => 1
                    ))->data(array(
                        'ask_openid' => $this->openid,
                        'ask_phone'  => $_POST['phone']
                    ))->save();

                    exit($this->encode(array('code'=>-2,'msg'=>'设备已被其它用户【'.$Row['phone'].'】绑定，已发送申请绑定操作，请耐心等待审核')));
                }
            }

            $bUpdate = true;
            if($Row){
                $bUpdate = $this->oIntelligentDeviceUsersModel->where(array(
                    'imei' => $sIMEI
                ))->save(array(
                    'is_on'     => 0,
                    'callback'  => 0,
                    'ask_phone' => '',
                    'ask_openid'=> '',
                ));
                msg(
                    $Row['token'],
                    $Row['openid'],
                    sprintf('设备【%s】被其它用户绑定，用户手机号码为%d', $Row['imei'], $_POST['phone']));
            }

            /*
             * 添加新的绑定用户
             */
            if($bUpdate) {
                $aAddData['imei'] = $sIMEI;
                $aAddData['token'] = $this->token;
                $aAddData['openid'] = $this->openid;
                $aAddData['bind_time'] = date("Y-m-d H:i:s");
                $aAddData['is_on'] = 1;
                $aAddData['phone'] = $_POST['phone'];
                $aAddData['cartype'] = $_POST['models'];
                if ($this->oIntelligentDeviceUsersModel->add($aAddData)) {
                    echo $this->encode(array('code'=>0,'msg'=>'绑定成功'));
                }else{
                    echo $this->encode(array('code'=>-2,'msg'=>'系统繁忙'));
                }
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'系统繁忙'));
            }
        }else{
            /*
             * 已有绑定设备列表
             */
            $aBindList = $this->getBindList($this->openid);
            //汽车型号
            $this->assign('cartype',$a=$this->getCarType(0));

            $this->assign('imei',$sIMEI);
            $this->assign('aBindList',$aBindList);
            $this->assign('imei',$_GET['imei']);
            $this->display();
        }
    }

    public function bind_agree()
    {
        $id = $_REQUEST['id'];
        $aData = $this->oIntelligentDeviceUsersModel->where(array(
            'id'    => $_REQUEST['id']
        ))->find();
        $agree = $_REQUEST['agree'];
        if ($agree) {
            $iIsOn = 1;
            $callback = $agree;
            if ($agree == 1) {
                msg($aData['token'], $aData['ask_openid'], '你申请的设备号'.$aData['imei'].'已审核通过，请重新绑定');
                $iIsOn = 0;
            }else{
                msg($aData['token'], $aData['ask_openid'], '你申请的设备号'.$aData['imei'].'未通过审核通过，请知晓');
            }
            $this->oIntelligentDeviceUsersModel->where(array(
                'id'    => $id,
                'is_on' => 1
            ))->data(array(
                'callback'   => $callback,
                'is_on'      => $iIsOn
            ))->save();
            exit(json_encode(array('status' => 0, 'data' => array())));
        }
        $this->assign('data', $aData);
        $this->assign('id', $_REQUEST['id']);
        $this->display();
    }

    /*
     *  获取汽车型号
     */
    public function getCarType($iPid = null)
    {
        $iPid = null === $iPid ? $_REQUEST['pid'] : $iPid ;
        $this->cartype = M('Intel_cartype');
        $aData = $this->cartype->where(array('token' => $this->token, 'pid' => $iPid))->select();
        if (IS_AJAX) {
            exit(json_encode(array('status' => 0, 'data' => $aData)));
        }else{
            return $aData;
        }
    }

    /*
     *删除绑定设备
     */
    public function delBindDevice(){
        $sIMEI = $_REQUEST['imei'];
        $bDel = $this->oIntelligentDeviceUsersModel->where(array(
            'imei'  => $sIMEI,
            'token' => $this->token
        ))->delete();
        if($bDel){
            echo $this->encode(array('code'=>0,'msg'=>'成功'));
        }else{
            echo $this->encode(array('code'=>-1,'msg'=>'系统繁忙'));
        }
    }


    /*
     *  获取配置
     */
    public function getDefaultMessage()
    {
        return M('Intel_message')->where(array(
            'token' => $this->token,
            'imei'  => $this->imei
        ))->select();
    }

    /*
     * 接口统一调用方法
     */
    public function beforeApi(){
        Vendor('Socket.socket');
        $this->Socket = new Socket('120.26.207.11', 8585);

        if(!$this->imei) {
            exit($this->encode(array('code'=>-1,'msg'=>'非法操作')));
        }
    }

    /*
     * 调用API操作
     */
    public function doApi($sType, $sValue){
        $sendData['host'] = $this->openid;
        $sendData['sendTo'] = $this->imei;
        $sendData['type'] = $sType;
        $sendData['value'] = $sValue;

        $senddataSign = json_encode($sendData);
        $sign = md5('*&@@^$)!@FSDKIlk123DWEWQa'.$senddataSign);
        $sendData['sign'] = $sign;
        $mRet = false;
        $sReturn = $this->Socket->send(json_encode($sendData), $mRet);
        $i = 0;
        while (!$sReturn or strlen($sReturn) == 0) {
            $this->beforeApi();
	    WL('re-connect' . $this->imei);
            $sReturn = $this->Socket->send(json_encode($sendData), $mRet);
            if ($i > 6) {
                break;
            }
        }
        $aReturn = json_decode($sReturn, true);
        if (strlen($sReturn) == 0 or !$sReturn or $aReturn['status'] != 0) {
		WL('send error:'.print_r($aReturn, true));
            $iStatus = isset($aReturn['status']) ? $aReturn['status'] : -1;
            return $this->encode(array('code'=>$iStatus,'msg'=>'操作失败'));
        }else{
            return $this->encode(array('code'=>0,'msg'=>'成功','msg'=>$sReturn));
        }
    }

    /*
     * 防盗开关
     */
    public function guard($iOn=0){
        $this->beforeApi();
        return $this->doApi('guard', (int)$iOn);
    }

    /*
     * 震动级别
     */
    public function carType($sCarType=''){
        $this->beforeApi();
        return $this->doApi('carType', $sCarType);
    }

    /*
     * 震动级别
     */
    public function shake($iShake=0){
        $this->beforeApi();
        return $this->doApi('shake', $iShake);
    }

    /*
     * 震动级别
     */
    public function road($iRoad=0){
        $this->beforeApi();
        return $this->doApi('road', $iRoad);
    }

    /*
     * 路况间隔
     */
    public function roadrange($iRoadrange=0){
        $this->beforeApi();
        return $this->doApi('roadrange', $iRoadrange);
    }

    /*
     * 天气预报
     */
    public function weather($iWeather){
        $this->beforeApi();
        return $this->doApi('weather', $iWeather);
    }

    /*
     * 拍照
     */
    public function photo($iPhoto=0){
        $this->beforeApi();
        return $this->doApi('photo', $iPhoto);
    }

    /*
     * 视频
     */
    public function video($iSecond=5){
        //$iSecond  5, 10, 30
        $this->removeCache($this->imei, 'video');
        $this->beforeApi();
        return $this->doApi('video', $iSecond);
    }

}
