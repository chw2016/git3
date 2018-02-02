<?php
	class Car_cxModel extends Model{
	protected $_validate = array(
			array('name','require','名称不能为空',1),
			array('picurl','require','图片不能为空',1),
			
	 );
	protected $_auto = array (    
	
	);
}

?>