<?php
class Finances_configModel extends Model{

	protected $_validate =array(
		array('longitude','require','请输入经度',1),
		array('latitude','require','请输入纬度',1),
		array('tel','require','请输入联系我们-电话',1),
		array('email','require','请输入联系我们-邮件',1),
		array('fax','require','请输入联系我们-传真',1),
		array('address','require','请输入地址',1),
		array('customer_phone','require','请输入客服电话',1),
	);

	protected $_auto = array (
		array('token','gettoken',self::MODEL_INSERT,'callback')
	);

	public function gettoken(){
		return session('token');
	}
}
