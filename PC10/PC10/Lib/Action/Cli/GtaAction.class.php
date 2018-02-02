<?php
class GtaAction extends CliAction {
    /*
     *  计算理财订跟投资订单是否到期了
     */
    public  $token;

    public function _initialize(){
        $this->token=$_GET['token'];
    }
    public function index()//每天晚上0点跑一次
    {
     //   echo 8;die;
        //这是以前到期的结算
        $list1=M('Licai_order')->field('id,time_limit,add_time')->where(array('token'=>$this->token,'is_daoqi'=>0))->select();
        foreach($list1 as $k=>$v){
            if(time()>($v['add_time']+$v['time_limit']*30*24*60*60)){//已过期限
                M('Licai_order')->where(array('id'=>$v['id']))->save(array('is_daoqi'=>1,'status'=>3));
                //把钱结算到余额里面
                $info=M('Licai_order')->field('token,openid,money,rate,time_limit')->where(array('id'=>$v['id']))->find();
                $money=$info['money']+$info['money']*$info['rate']/365*30*$info['time_limit'];
                M('Gta_users')->where(array('token'=>$info['token'],'openid'=>$info['openid']))->setInc('money',$money);
            }
        }
        /*
        $list2=M('Touzi_order')->field('id,time_limit,add_time')->where(array('token'=>$this->token,'is_daoqi'=>0))->select();
        foreach($list2 as $k=>$v){
            if(time()>($v['add_time']+$v['time_limit']*30*24*60*60)){//已过期限
                M('Touzi_order')->where(array('id'=>$v['id']))->save(array('is_daoqi'=>1,'status'=>3));
                //把钱结算到余额里面
                $info=M('Touzi_order')->field('token,openid,money,rate,time_limit')->where(array('id'=>$v['id']))->find();
                $money=$info['money']+$info['money']*$info['rate']/365*30*$info['time_limit'];
                M('Gta_users')->where(array('token'=>$info['token'],'openid'=>$info['openid']))->setInc('money',$money);
            }
        }*/
        //这里收益按月结算给用户，增加到余额里面   按30天算
     //  echo date('Y-m-d',time()-34*60*60*24);
       /* $list1=M('Licai_order')->field('token,uid,money,rate,id,time_limit,add_time,js_type')->where(array('token'=>$this->token,'add_time'=>array('gt',(time()-33*60*60*24))))->select();
        //p($list1);die;
        foreach($list1 as $k=>$v){
            $data['orderid']=$v['id'];
            $data['uid']=$v['uid'];
            $data['add_time']=$v['add_time']+30*60*60*24;
            $data['js_type']=$v['js_type'];//1先息后本，2等额本息
            $data['type']=2;//订单类型，1代表p2p，2代表理财
            if($v['js_type']==1){
                $data['sy_money']=($v['money']*$v['rate']/12)*$v['time_limit'];
                $data['bj_money']=0;
            }
            if($v['js_type']==2){
                $data['sy_money']=($v['money']*$v['rate']/12)*$v['time_limit'];
                $data['bj_money']=$v['money']/$v['time_limit'];
            }
            $data['token']=$v['token'];
            if(!M('Gta_syjl')->where(array('orderid'=>$v['id'],'type'=>2,'add_time'=>($v['add_time']+30*60*60*24)))->find()){//不重复添加
                M('Gta_syjl')->add($data);
                //加钱
                $money=$data['sy_money']+$data['bj_money'];
                M('Gta_users')->where(array('id'=>$data['uid']))->setInc('money',$money);
            }
        }*/
       //下面p2p投资的
        $time=strtotime('- 30day');
        $time=strtotime(date('Y-m-d',$time));
      //  echo $time;
        $list2=M('Touzi_order')->field('id,token,uid,money,rate,id,time_limit,add_time,js_type')->where(array('token'=>$this->token,'js_time'=>$time))->select();



        foreach($list2 as $k=>$v){
            $data['orderid']=$v['id'];
            $data['uid']=$v['uid'];
            $data['add_time']=strtotime(date('Y-m-d',time()));
            $data['js_type']=$v['js_type'];//1先息后本，2等额本息
            $data['type']=1;//订单类型，1代表p2p，2代表理财
            if($v['js_type']==1){
                $data['sy_money']=($v['money']*$v['rate']/12)*$v['time_limit'];
                $data['bj_money']=0;
            }
            if($v['js_type']==2){
                $data['sy_money']=($v['money']*$v['rate']/12)*$v['time_limit'];
                $data['bj_money']=$v['money']/$v['time_limit'];
            }
            $data['token']=$v['token'];
            if(!M('Gta_syjl')->where(array('orderid'=>$v['id'],'type'=>1,'add_time'=>($v['add_time']+30*60*60*24)))->find()){//不重复添加
                M('Gta_syjl')->add($data);
                //改时间
                M('Touzi_order')->where(array('id'=>$v['id']))->save(array('js_time'=>strtotime(date('Y-m-d',time()))));
                //加钱
                $money=$data['sy_money']+$data['bj_money'];
                M('Gta_users')->where(array('id'=>$data['uid']))->setInc('money',$money);
            }
        }


    }

}
