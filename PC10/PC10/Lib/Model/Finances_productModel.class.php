<?php
class Finances_productModel extends Model{

	protected $_validate =array(
        array('finances_area_id','require','请选择产品地区',1),
        array('finances_area_id','checkArea','请选择地区',1, 'callback'),
        array('finances_planner_id','require','请选择理财师',1),
        array('finances_planner_id','','请选择理财师',0, 'unique'),
        array('finances_product_type_id','require','请选择产品类型',1),
        array('finances_product_type_id','checkProductID','请选择产品类型',1, 'callback'),
		array('title','require','请输入产品名称',1),
		array('title','','产品已存在，请重新输入产品名称',1, 'unique'),
        array('image','require','请上传产品图文封面',1),
        array('entity_price','require','请输入产品实体价',1),
        array('price','require','请输入产品价格',1),
	);

	protected $_auto = array (
		array('add_time','time',self::MODEL_INSERT,'function'),
		array('token','gettoken',self::MODEL_INSERT,'callback')
	);

    public function checkArea($iAreaID)
    {
        return !!M('finances_area')->where(array('id' => $iAreaID))->count();
    }

    /*public function checkPlanner($iPlanner)
    {
        return !!M('finances_planner')->where(array('id' => $iPlanner))->count();
    }*/

    public function checkProductID($iProductID)
    {
        return M('finances_product_type')->where(array('id' => $iProductID))->find();
    }

	function gettoken(){
		return session('token');
	}

}
