<?php
	class HouseunitsModel extends Model{
	protected $_validate = array(
			array('name','require','户型名称不能为空',1),
			array('sub','require','选择子楼盘不能为空',1),
			array('lc','require','楼层不能为空',1),
			array('area','require','建筑面积不能为空',1),
			array('shi','require','室不能为空',1),
			array('ting','require','厅不能为空',1),
			array('content','require','户型介绍不能为空',1),
			array('picurl','require','户型图片不能为空',1),	
	 );
	protected $_auto = array (    
	
	);
}

?>