<?php
	class SubestateModel extends Model{
	protected $_validate = array(
			array('name','require','子楼盘名称不能为空',1),
			array('title','require','楼盘名称不能为空',1),
			array('content','require','子楼盘介绍不能为空',1),
	 );
	protected $_auto = array (    
	
	);
}

?>