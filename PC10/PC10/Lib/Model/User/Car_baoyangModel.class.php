<?php
	class Car_baoyangModel extends Model{
	protected $_validate = array(
			array('keyword','require','关键词不能为空',1),
			array('picurl','require','图文封面不能为空',1),
			array('title','require','标题不能为空',1),
			array('address','require','地址不能为空',1),
			array('phone','require','联系方式不能为空',1),
			array('starttime','require','开始时间不能为空',1),
			array('endtime','require','结束时间不能为空',1),
			
	 );
	protected $_auto = array (    
	
	);
}

?>