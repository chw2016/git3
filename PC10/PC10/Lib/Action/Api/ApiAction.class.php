<?php
class ApiAction extends BaseAction {

    protected $_sign = 'JJ*&@FJL)!_#Q=';

    public function success($sMsg='', $aData=array())
    {
        return json_encode(array(
            'status'  => 0,
            'message' => $sMsg,
            'data'    => $aData
        ));
    }

    public function checkSign($aData=array())
    {
        asort($aData);
        return $_REQUEST['sign'] == md5(http_build_query($aData).'&key='.$this->_sign);
    }
    public function error($iStatus=-1, $sMsg='', $aData=array())
    {
        return json_encode(array(
            'status'  => $iStatus,
            'message' => $sMsg,
            'data'    => $aData
        ));
    }
}
