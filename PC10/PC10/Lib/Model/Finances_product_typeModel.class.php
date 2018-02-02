<?php
class Finances_areaModel extends Model{

	protected $_validate =array(
		array('title','require','请输入分类名称',1),
		array('title','','分类名已存在，请重新输入分类名称',0, 'unique')
	);

	public $_auto = array (
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
