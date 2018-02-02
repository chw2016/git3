<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/11
 * Time: 9:03
 */
class Service_areaModel extends Model{
    protected $_validate = array(
        array('address','require','请选择小区地址',1),
        array('ursename','require','请填写小区名称',1),
        array('tel','require','请填写小区管理的联系电话',1),
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