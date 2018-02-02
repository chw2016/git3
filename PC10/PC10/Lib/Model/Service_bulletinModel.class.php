<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/11
 * Time: 9:04
 */
class Service_bulletinModel extends Model{
    protected $_validate = array(
        array('aid','require','请选择小区',1),
        array('title','require','请填写公告主题',1),
        array('info','require','请填写公告内容',1)
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