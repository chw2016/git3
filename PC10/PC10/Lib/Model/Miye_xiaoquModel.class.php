<?php
class Miye_xiaoquModel extends Model{
  
	protected $_validate =array(
		array('xiaoqu','require','请输入小区名称',1),
		array('xiaoqu','','小区已存在，请重新输入品种名称',0, 'unique')

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
