<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/11
 * Time: 9:03
 */
class Service_staffModel extends Model{
    protected $_validate = array(
        array('aid','require','请选择小区',1),
        array('sex',array(2,1),'请选择性别的类型',3,'in'),
        array('staff_name','require','请填写维修师的姓名',1),
        array('tel','require','请填写维修师的电话',1),
        array('tel','number','非法操作',1)
    );
    protected $_auto = array(
        array('add_time','getCurDate',self::MODEL_INSERT,'callback'),
        array('token','gettoken',self::MODEL_INSERT,'callback')
    );

    public function getCurDate(){
        return date('Y-m-d H:i:s');
    }
    public static function gettoken(){
        return session('token');
    }
}