<?php
	class Car_guanhuaiModel extends Model{
	protected $_validate = array(
			array('keyword','require','关键词不能为空',1),
			array('picurl','require','图片不能为空',1),
			array('title','require','标题不能为空',1),
			array('content','require','描述不能为空',1),

			
	 );
	protected $_auto = array (    
	
	);
}

?>