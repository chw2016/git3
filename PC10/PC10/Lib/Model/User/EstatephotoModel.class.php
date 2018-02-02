<?php
	class EstatephotoModel extends Model{
	protected $_validate = array(
			array('name','require','名称不能为空',1),
			array('content','require','介绍不能为空',1),
			array('picurl','require','相册图片不能为空',1),
	 );
	protected $_auto = array (    
	
	);
}

?>