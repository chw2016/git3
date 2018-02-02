<?php
class AddhnAction extends CliAction {

    public $token = '339e1af488be0adaa98c9709c9f0d701';

    public function hnadd()
    {
        $user= M('Hn_users')
        ->where(array('id'=>array('between',array(47, 64))))
        ->select();
        foreach($user as $k=>$v)
        {

           $info['token']=$this->token;
           $info['uid']=$v['id'];
           $info['status']=1;
           $info['add_time']=time();
           $info['type_id']=$v['id'];
           $info['content']='新用户注册';
           $info['yonjing']=50;
           $info['name']=$v['name'];
           M('hn_yonjing_jl')->add($info);
        }
}
}
