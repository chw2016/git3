<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/18 0018
 * Time: 9:13
 */

class YndgetmoneyAction extends CliAction {
    public  $token;

    public function _initialize(){
        $this->token=$_GET['token'];
    }


    function addrecord($token,$openid,$userid,$info,$money,$type,$rank){
        M('Ynd_record')->add(array(
            'token'=>$token,
            'openid'=>$openid,
            'user_id'=>$userid,
            'info'=>$info,
            'content'=>$money,
            'rank' =>$rank,
            'type' => $type,
            'add_time'=>date('Y-m-d H:i:s')
        ));
    }

    /*发货七天，如若还未付款，按付款为0,并结算3600 = 86400*/
    public function index(){
        $list = M('Ynd_order')->where(array('token'=>$this->token,'send_status'=>1,'pay_status'=>0))->select();
        foreach($list as $key=>$val){
            //发货时间
            $send_time = strtotime($val['send_time']);
            $datatime = $send_time + (86400*7);
            //$pay_time = strtotime($val['pay_time']);
            $userinfo = M('Ynd_user')->where(array('id'=>$val['yuid']))->find();
            if(time()>$datatime){ //大于7天，自动结算
                $data['pay_time'] = date('Y-m-d H:i:s');
                $data['pay_status'] = 1;
                $data['money'] = 0;
                if( M('Ynd_order')->where(array('id'=>$val['id']))->save($data)){
                    if($val['LQ']>$userinfo['LQ']){
                        $LQ = $userinfo['LQ'];
                        $CQ = $userinfo['CQ']-($val['LQ']-$userinfo['LQ']+$val['CQ']);
                        if(M('Ynd_user')
                            ->where(array(
                                'id'=>$val['yuid']))
                            ->save(array(
                                'LQ'=>0,
                                'CQ'=>$CQ
                            ))){
                            $this->addrecord($val['token'],$userinfo['openid'],$userinfo['id'],'商品购买',$LQ,'LQ',1);
                            $this->addrecord($val['token'],$userinfo['openid'],$userinfo['id'],'商品购买',$CQ,'CQ',1);
                        };
                    }else{
                        $LQ = $userinfo['LQ'] - $val['LQ'];
                        $CQ = $userinfo['CQ'] - $val['CQ'];
                        M('Ynd_user')
                            ->where(array(
                                'id'=>$val['yuid']))
                            ->save(array(
                                'LQ'=> $LQ,
                                'CQ'=> $CQ
                            ));
                        $this->addrecord($val['token'],$userinfo['openid'],$userinfo['id'],'商品购买',$LQ,'LQ',1);
                        $this->addrecord($val['token'],$userinfo['openid'],$userinfo['id'],'商品购买',$CQ,'CQ',1);
                    }

                    /*结算*/
                    $orid = M('Ynd_orderinfo')->where(array(
                        'oid'=>$val['id']
                    ))->select();
                    foreach($orid as $key=>$val){
                        $this->balance($val['id']);
                    }
                    M('Ynd_order')->where(array(
                        'id'=>$val['id']
                    ))->save(array(
                        'balance_time'=>date('Y-m-d H:i:s'),
                        'is_balance'=>1
                    ));


                }

            }

        }
    }
    /*购买结算*/
    public function balance($ocid){
        $orinfo = M('Ynd_orderinfo')->where(array('id'=>$ocid))->find();
        $userinfo = $orinfo['user_id'];
        $userinfo = json_decode($userinfo,ture);
        //array('pro_id'=>'num')
        $arr1 = array_keys($userinfo);
        $arr2 = array_values($userinfo);
        // P($arr1);exit;
        for ($i=0; $i < count($arr1) ; $i++) {
            if($arr1[$i] != "a"){
                $info = $this->fangdaninfo($arr1[$i]);
                /*结算加入*///在tp_ynd_orderinfo 中加一个status  默认为0；
                $money = $info['price']*$arr2[$i];
                // M('Ynd_user')->where(array('id'=>$info['user_id']))->setInc('money',$arr2[$i]);
                $userinfoes = M('Ynd_user')->where(array('id'=>$info['user_id']))->find();
                M('Ynd_user')->where(array('id'=>$info['user_id']))->setInc('money',$money);
                //生成日志记录
                $this->addrecord($this->token,$userinfoes['openid'],$info['user_id'],'放单结算',$money,'money',0);
            }

        }
        // M('Ynd_orderinfo')->where(array('id'=>$ocid))->save(array('status'=>1));
        //$info = $this->fangdaninfo();
    }



}