<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/2
 * Time: 9:43
 */
class Recruitment_messageModel extends Model{
    protected $_validate = array(
        array('office_id','require','请选择职位名称',1),
        array('info','require','请填写职位相关内容',1),
        array('addname','require','请输入发布人的姓名',1)

    );

    protected $_auto = array(
        array('add_time','getCurDate',self::MODEL_INSERT,'callback'),
        array('token','gettoken',self::MODEL_INSERT,'callback'),
    );

    public function getCurDate(){
        return date('Y-m-d H:i:s');
    }
    public static function gettoken(){
       return session('token');
    }

}