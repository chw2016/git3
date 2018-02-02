<?php
	class Car_ppModel extends Model{
	protected $_validate = array(
			array('name','require','品牌名称不能为空',1),
			array('picurl','require','图片不能为空',1),
			
	 );
	protected $_auto = array (    
	
	);
}

?>