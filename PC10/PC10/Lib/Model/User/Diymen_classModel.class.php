<?php
class Diymen_classModel extends Model{
	protected $_validate = array(
			array('title','require','菜单名称必须填写',1),
			array('keyword','require','关联关键字必须填写',1),

	 );
	protected $_auto = array (		
		array('token','getToken',Model:: MODEL_BOTH,'callback'),
	);
	function getToken(){	
		return $_SESSION['token'];
	}
}

?>
