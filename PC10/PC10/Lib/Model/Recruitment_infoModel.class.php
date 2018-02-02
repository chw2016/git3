<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/8
 * Time: 9:16
 */
class Recruitment_infoModel extends Model{
    protected $_validate = array(
        array('aid','require','非法操作',1),
        array('address','require','请填写工作地址',1),
        array('tel','require','请填写公司招聘联系电话',1),
        array('line','require','请填写公司招聘的乘车路线'),
        array('name','require','请填写公司招聘的联系人')
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