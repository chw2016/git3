<?php
class Finances_plannerModel extends Model{

	public $_validate =array(
        array('finances_area_id','require','请选择产品地区',1),
        array('finances_area_id','checkArea','请选择地区',1, 'callback'),
		array('name','require','请输入理财师姓名',1),
		array('name','','此理财师已存在，请勿重复添加',1, 'unique'),
        array('image','require','请上传头像',1),
        array('type',array(1,0),'请选择正常的类型',3,'in'),
        array('stars',array(1,0),'请选择正常的类型',3,'in'),
	);

	protected $_auto = array (
		array('add_time','time',self::MODEL_INSERT,'function'),
		array('token','gettoken',self::MODEL_INSERT,'callback')
	);

    public function getType()
    {
        return array(
            0 => '内部',
            1 => '外部'
        );
    }

    public function getStars()
    {
        return array(
            -1 => '请选择星级',
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5
        );
    }
    public function getSpecialtyLevel()
    {
        return array(
            -1 => '请选择专业等级',
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        );
    }

    public function getCommunicationLevel()
    {
        return array(
            -1 => '请选择沟通等级',
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        );
    }

    public function getServerLevel()
    {
        return array(
            -1 => '请选择服务等级',
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        );
    }

    public function checkArea($iAreaID)
    {
        return !!M('finances_area')->where(array('id' => $iAreaID))->count();
    }

	function gettoken(){
		return session('token');
	}

}
