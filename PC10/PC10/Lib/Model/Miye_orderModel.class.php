<?php
class Miye_orderModel extends Model{
	
	const STATUS_UNDEAL = 0;
	
	const STATUS_CANCEL = 1;
	
	const STATUS_COMPLETE = 2;
	
	public function getStatusName($sKey = null)
    {
        $aStatus = array(
            self::STATUS_UNDEAL => '未处理',
            self::STATUS_CANCEL => '取消',
            self::STATUS_COMPLETE => '完成'
        );
        return Arr::get($aStatus, $sKey, $aStatus);
    }
}
