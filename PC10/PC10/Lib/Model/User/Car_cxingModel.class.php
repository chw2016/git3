<?php
	class Car_cxingModel extends Model{
	protected $_validate = array(
			array('year','require','年款不能为空',1),
			array('price','require','指导价不能为空',1),
			array('saleprice','require','经销商报价不能为空',1),
			array('picurl','require','图片不能为空',1),
			
	 );
	protected $_auto = array (    
	
	);
}

?>