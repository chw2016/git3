<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/2
 * Time: 9:43
 */
class Recruitment_officeModel extends Model{
    protected $_validate = array(
        //array('aid','require','非法操作',1),
        array('oname','require','请填写职位名称',1),
        array('aname','checkArea','职位名已存在，请重新输入职位名称',1, 'callback')
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
    public function checkArea($sOname){
        return M('Recruitment_office')->where(array('token'=>'gettoken','oname'=>$sOname))->find();
    }
}