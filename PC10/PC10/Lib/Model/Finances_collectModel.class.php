<?php
class Finances_collectModel extends Model{

	protected $_validate =array(
		array('openid','require','openid不得为空',1),
		array('finances_planner_id','require','请填写关注理财师',1),
	);

	protected $_auto = array (
		array('add_time','getCurDate',self::MODEL_INSERT,'callback'),
		array('token','gettoken',self::MODEL_INSERT,'callback')
	);

	public function gettoken(){
		return session('token');
	}

}
