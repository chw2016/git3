<?php
class Finances_orderModel extends Model{

    public function getStatusName($sKey = null)
    {
        $aStatus = array(
            0 => '取消',
            1 => '正常'
        );
        return Arr::get($aStatus, $sKey, $aStatus);
    }
}
