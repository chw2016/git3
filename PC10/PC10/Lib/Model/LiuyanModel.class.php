<?php
class LiuyanModel extends Model{

	protected $_validate =array(

	);
	
	protected $_auto = array (
		array('uid','getuser',self::MODEL_INSERT,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('uptatetime','time',self::MODEL_BOTH,'function'),
		array('token','gettoken',self::MODEL_INSERT,'callback'),

	);
	
	public function getuser(){
		return session('uid');
	}
	
	
	function gettoken(){
		return session('token');
	}
	
}