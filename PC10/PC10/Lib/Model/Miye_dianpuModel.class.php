<?php
class Miye_dianpuModel extends Model{

	protected $_validate =array(
		array('name','require','请输入店铺名称',1),
		array('name','','店铺已存在，请勿重复添加',0, 'unique')
	);

	protected $_auto = array (
		array('add_time','getCurDate',self::MODEL_INSERT,'callback'),
		array('token','gettoken',self::MODEL_INSERT,'callback')
	);

    public function getCurDate()
    {
        return date('Y-m-d H:i:s');
    }

	function gettoken(){
		return session('token');
	}

}
