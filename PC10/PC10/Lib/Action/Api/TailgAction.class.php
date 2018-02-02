<?php
class TailgAction extends ApiAction {

    /*
     * 台铃的API
     *  功能：
            1、上报经纬度
            2、收集回复
     */
    private $sToken = '70f38fdd1c5189197bc275469aa9b481';

    public function _initialize()
    {
        $this->GEO = M('Tailg_geo');
    }

    /*
     *  保存
     */
    public function saveMsg()
    {
        $sIMEI  = $_REQUEST['imei'];
        $sType  = $_REQUEST['type'];
        $mValue = $_REQUEST['value'];
        self::L($sIMEI.'-'.$sType.'-'.$mValue);
        $ointelModel = M('intel_message');
        $iTem = $ointelModel->where(array(
            'token'=>$this->sToken,
            'imei'=>$sIMEI,
            'key'=>$sType
        ))->find();
        $aValue = explode('#', $mValue);
        if ($sType == 'A116') {
            $mValue = implode(',', array($aValue[3], $aValue[4], $aValue[5]));
        }else{
            $mValue = $aValue[3];
        }
        $data['imei'] = $sIMEI;
        $data['token'] = $this->sToken;
        $data['key']   = $sType;
        $data['value'] = $mValue;
        $this->cache($sIMEI, $sType, $mValue);
        if($iTem){
            $intelSave = $ointelModel->where(array(
                'token'=>$this->sToken,
                'imei'=>$sIMEI,
                'key'=>$sType
            ))->save(array('value' => $mValue));
            if($intelSave){
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
     *  设置缓存
     */
    public function cache($sIMEI, $sType, $mValue)
    {
        cache(md5(sprintf('%s%s', $sIMEI, $sType)), $mValue, 600);
    }

    /*
     *  上报地理位置
        分钟为单位
     */
    public function geo()
    {
        $sIMEI  = trim($_REQUEST['imei']);
        $fLon   = self::getLatLng($_REQUEST['lng']);
        $fLat   = self::getLatLng($_REQUEST['lat']);
        $status = $_REQUEST['status'];
        $sDate  = $_REQUEST['date'];
        if (empty($sIMEI)) {
            exit($this->error(-1, 'params error'));
        }
        /*
        if (empty($fLon) AND empty($fLat)) {
            exit($this->success('success'));
        }
        */
        if ($this->GEO->where(array(
            'token'     => $this->sToken,
            'imei'      => $sIMEI,
            'add_time'  => $sDate
        ))->count()) {
            exit($this->error(-998, '请求频繁'));
        }
        if($iID=$this->GEO->add(array(
            'token' => $this->sToken,
            'imei'  => $sIMEI,
            'longitude' => $fLon,
            'latitude'  => $fLat,
            'create_time' => time(),
            'status'    => $status,
            'add_time'  => $sDate
        ))){
            $this->cache($sIMEI, 'A111', $fLon.','.$fLat);
            exit($this->success('success'));
        }else{
            exit($this->error(-3, 'sys error'));
        }
    }

    public static function getLatLng($latlng)
    {
        if (!$latlng) {
            return '';
        }
        $iBai = (int)($latlng/100);
        $fen  = ($latlng - $iBai * 100)/60;
        return $iBai + $fen;
    }

    /*
     *
     */
    public static function L($s)
    {
        file_put_contents('tailg.txt', $s ."\r\n", FILE_APPEND);
    }

}
