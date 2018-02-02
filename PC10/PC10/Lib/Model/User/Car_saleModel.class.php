<?php
	class Car_saleModel extends Model{
	protected $_validate = array(
			array('name','require','姓名不能为空',1),
			array('picurl','require','头像不能为空',1),
			array('phone','require','电话号码不能为空',1),
			
			
	 );
	protected $_auto = array (    
	
	);
}

?>