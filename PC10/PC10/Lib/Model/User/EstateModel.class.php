<?php
	class EstateModel extends Model{
	protected $_validate = array(
			array('title','require','图文消息标题不能为空',1),
			array('keyword','require','图文消息触发关键词不能为空',1),
			array('picurl','require','图文消息封面不能为空',1),
			array('toppicurl','require','楼盘头部图片不能为空',1),
			array('roompicurl','require','户型头部图片不能为空',1),
			array('address','require','地址不能为空',1),	
	 );
	protected $_auto = array (    
	
	);
}

?>