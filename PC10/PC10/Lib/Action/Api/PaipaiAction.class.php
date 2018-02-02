<?php
class PaipaiAction extends ApiAction {

    /*
     * 拍拍狗的token值
     */
    private $sToken = '7a22d71ec7b216144ac970cfaaef0084';
    //private $sToken = '5d8a87bab30de695954b17fc835b9d12';

    public function _initialize()
    {
        $this->IMEI = M('Intel_imei');
        $this->GEO  = M('Intel_geo');
        $this->oIntelligentDeviceUsersModel = M('Intelligent_devices_users');
        $this->imei = $_REQUEST['imei'];

        //重置token
        $token = M('Intel_bind_imei')->where(array(
            'imei'  => $this->imei
        ))->getField('token');
        $this->sToken = $token ? $token : '7a22d71ec7b216144ac970cfaaef0084';


        $sSign      = trim($_REQUEST['sign']);
        if (empty($sSign) or md5('3q2isldfajsdfi(*&*^kesdfd)') != $sSign) {
            //exit($this->error(-999, 'signError'));
        }
    }

    /*
     *  添加IMEI
     */
    public function index()
    {
        $sIMEI    = trim($_REQUEST['imei']);
        if (empty($sIMEI)) {
            exit($this->error(-1, 'imei required'));
        }
        $MID = $this->IMEI->query('select max(id) as id from tp_intel_imei');
        $iSenceID = 1;
        if ($MID) {
            $iSenceID = (int)$MID[0]['id'] + 1;
        }
        $iSenceID = (int)('151' . $iSenceID);
        $aData = array('imei' => $sIMEI,'token' => $this->sToken);
        /*
        if ($M = $this->IMEI->where($aData)->find()) {
            exit($this->success('success', array('qrurl' => $M['qrurl'])));
        }
        */
        $Code     = new Code($this->sToken, $iSenceID);
        $sCodeUrl = $Code->getLSCode();
        if ($sCodeUrl) {
            if($this->IMEI->add(array_merge($aData, array(
                'qrurl'   => $sCodeUrl,
                'senceid' => $iSenceID
            )))){
                exit($this->success('success', array('qrurl' => $sCodeUrl)));
            }else{
                exit($this->error(-3, 'sys error'));
            };
        }else{
            exit($this->error(-4, 'error create qrcode'));
        }
    }

    /*
     *  保存
     */
    public function saveMsg()
    {
        $sIMEI  = $_REQUEST['imei'];
        $sType  = $_REQUEST['type'];
        $mValue = $_REQUEST['value'];
        file_put_contents('p.txt', '[' . date('Y-m-d H:i:s') . ']' . $sIMEI.'-'.$sType.'-'.$mValue."\r\n", FILE_APPEND);
        $ointelModel = M('intel_message');
        $iTem = $ointelModel->where(array('token'=>$this->sToken,'imei'=>$sIMEI,'key'=>$sType))->find();

        $isStype = array('guard','road','shake','roadrange','carType','photo','video', 'position');
        if(in_array($sType,$isStype)){
            $data['key'] = $sType;
        }else{
            echo $this->encode(array('codes'=> 3,'msg'=>'非法操作' ));exit;
        }
        $data['imei'] = $sIMEI;
        $data['token'] = $this->sToken;
        $data['value'] = $mValue;
        if (in_array($sType, array('position', 'photo', 'video'))) {
            file_put_contents('pos.txt', $value, FILE_APPEND);
            $this->cache($sIMEI, $sType, $mValue);
            echo $this->encode(array('codes'=>0,'msg'=>'操作成功' ));exit;
            return;
        }
        if($iTem){
            $intelSave = $ointelModel->where(array('token'=>$this->sToken,'imei'=>$sIMEI,'key'=>$sType))->save(array('value' => $mValue));
            if($intelSave !== false){
                $this->cache($sIMEI, $sType, $mValue);
                echo $this->encode(array('codes'=>0,'msg'=>'操作成功' ));exit;
            }else{
                echo $this->encode(array('codes'=>1,'msg'=>'操作失败' ));exit;
            }
        }else{
            $intelAdd = $ointelModel->add($data);
            if($intelAdd){
                $this->cache($sIMEI, $sType, $mValue);
                echo $this->encode(array('codes'=>0,'msg'=>'操作成功' ));exit;
            }else{
                echo $this->encode(array('codes'=>1,'msg'=>'操作失败' ));exit;
            }
        }
    }

    /*
     *  开机调用，给微信用户发消息
     */
    public function startup()
    {
        $sIMEI  = trim($_REQUEST['imei']);
        if (empty($sIMEI)) {
            exit($this->encode(array('codes'=>-1,'msg'=>'imei不得为空')));
        }
        $Device = M('Intelligent_devices_users')->where(array(
            'imei'  => $sIMEI,
            'is_on' => 1
        ))->select();

        if ($Device) {
            msg($Device['token'], $Device['openid'], sprintf('您的设备号【%s】开机了', $Device['imei']));
            exit($this->encode(array('codes'=>0,'msg'=>'消息发送成功')));
        }else{
            exit($this->encode(array('codes'=>-1,'msg'=>'imei不存在')));
        }
    }

    /*
     *  设置缓存
     */
    public function cache($sIMEI, $sType, $mValue)
    {
        file_put_contents('p.txt', 'Cache'.':'.$sIMEI.'-'.$sType.'-'.$mValue."\r\n", FILE_APPEND);
        cache(md5(sprintf('%s%s', $sIMEI, $sType)), $mValue, 600);
    }

    /*
     *  上报地理位置
        分钟为单位
     */
    public function geo()
    {
	die;
        $day    = 86400;
        $sIMEI  = trim($_REQUEST['imei']);
        $fLon   = $_REQUEST['lng'];
        $fLat   = $_REQUEST['lat'];
        $acc    = $_REQUEST['acc'];//acc状态
        $hide   = 0;//是否前端显示
        $sCacheKey = 'PAIPAI_GEO_'.$sIMEI;
        $iHide  = cache($sCacheKey);
        if (!$iHide) {
            //如果开启了隐私模式，则不记录
            if (M('Intel_message')->where(array(
                'imei'  => $sIMEI,
                'token' => $this->sToken,
                'key'   => 'hideBtn',
                'value' => 1
            ))->count()) {
                cache($sCacheKey, 1, $day);
                $hide = 1;
            }else{
                cache($sCacheKey, -1, $day);
            }
        }else{
            $hide = ($iHide == 1) ? 1 : 0;
        }

        if (empty($sIMEI)) {
            exit($this->error(-1, 'params error'));
        }
        if (empty($fLon) AND empty($fLat)) {
            exit($this->success('success'));
        }
        $sDate = date('Y-m-d H:i:s');
        /*
        $IMEI = $this->IMEI->where(array('imei' => $sIMEI, 'token' => $this->sToken))->find();
        if (!$IMEI) {
            exit($this->error(-2, 'imei not exist'));
        }
        if ($this->GEO->where(array(
            'token' => $this->sToken,
            'imei'  => $sIMEI,
            'add_time'  => $sDate
        ))->count()) {
            exit($this->error(-998, '请求频繁'));
        }
*/
        if($this->GEO->add(array(
            'token' => $this->sToken,
            'imei'  => $sIMEI,
            'longitude' => $fLon,
            'latitude'  => $fLat,
            'acc'       => $acc,
            'hide'      => $hide,
            'date'      => date('Y-m-d'),
            'add_time'  => $sDate
        ))){
            exit($this->success('success'));
        }else{
            exit($this->error(-3, 'sys error'));
        }
    }


    protected function authimei(){
        $deviceData = $this->oIntelligentDeviceUsersModel->where(array(
            'imei'  => $this->imei,
            'is_on' => 1,
            'token' => $this->sToken
        ))->find();
        if($deviceData){
            return $deviceData;
        }else{
            return false;
        }
    }


    /*
     * 存图片
     */
    public function pushimg(){
	WL(print_r($_GET, true));
        if($deviceData =$this->authimei()){
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 2000000 ;// 设置附件上传大小
            $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath =  './upload/deviceimages/'.date("Ym").'/';// 设置附件上传目录
            if (!file_exists($upload->savePath)) {
                mkdir($upload->savePath, 0777, true);
            }
            if(!$upload->upload()) {// 上传错误提示错误信息
                echo $this->encode(array('code'=>-1,'msg'=>$upload->getErrorMsg()));
            }else{// 上传成功
                $info = $upload->getUploadFileInfo();
                $aImageData['img_path'] = $upload->savePath.$info[0]['savename'];
                $aImageData['add_time'] = date("Y-m-d H:i:s");
                $aImageData['date'] = date("Y-m-d");
                $aImageData['imei'] = $this->imei;
                $aImageData['token'] = $this->sToken;
                $aImageData['openid'] = $deviceData['openid'];
                if(M('Intelligent_images')->add($aImageData)){
			file_put_contents('url.log', $aImageData['img_path'] . "\r\n", FILE_APPEND);
                    echo $this->encode(array('code'=>0,'msg'=>'成功', 'url' => $aImageData['img_path']));
                }else{
                    echo $this->encode(array('code'=>-3,'msg'=>'系统繁忙'));
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'参数错误'));
        }
    }

    /*
     * 存视频
     */
    public function pushvideo(){
	WL(print_r($_GET, true) . '777');
        if($deviceData =$this->authimei()){
		WL(print_r($_GET, true) . '1');
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 20000000 ;// 设置附件上传大小
            $upload->allowExts  = array('mp4');// 设置附件上传类型
            $upload->savePath =  './upload/devicevideo/'.date("Ym").'/';// 设置附件上传目录
            if (!file_exists($upload->savePath)) {
                mkdir($upload->savePath, 0777, true);
            }
		WL(print_r($_GET, true) . '2');
            if(!$upload->upload()) {// 上传错误提示错误信息
                echo $this->encode(array('code'=>-1,'msg'=>$upload->getErrorMsg()));
            }else{// 上传成功
		WL(print_r($_GET, true) . '3');
                $info = $upload->getUploadFileInfo();
                $aImageData['video_path'] = $upload->savePath.$info[0]['savename'];
                $aImageData['add_time'] = date("Y-m-d H:i:s");
                $aImageData['date'] = date("Y-m-d");
                $aImageData['imei'] = $this->imei;
                $aImageData['token'] = $this->sToken;
                $aImageData['openid'] = $deviceData['openid'];
		WL(print_r($_GET, true) . '4');
                if(M('Intelligent_video')->add($aImageData)){
			file_put_contents('url.log', $aImageData['video_path'] . "\r\n", FILE_APPEND);
                    echo $this->encode(array('code'=>0,'msg'=>'成功', 'url' => $aImageData['video_path']));
			WL(print_r($aImageData, true) . '5');
                }else{
                    echo $this->encode(array('code'=>-3,'msg'=>'系统繁忙'));
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'参数错误'));
        }
    }
}
