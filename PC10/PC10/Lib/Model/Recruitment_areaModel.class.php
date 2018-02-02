<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/2
 * Time: 9:42
 */
class Recruitment_areaModel extends Model{
    protected $_validate = array(
        array('username','require','请输入区域名称',1),
        array('username','checkArea','区域名称已存在，请重新输入区域名称',1, 'callback'),
        array('tel','require','请输入区域管理电话',1),
        array('admin','require','请填写区域管理员的名字',1),
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
    public function checkArea($sAname){
        return M('Recruitment_area')->where(array('token'=>'gettoken','aname'=>$sAname))->find();
    }




}