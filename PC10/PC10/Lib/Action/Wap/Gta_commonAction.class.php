<?php
class Gta_commonAction extends BaseAction{






    public function _initialize(){
        parent::_initialize();
    }

    /**
     *$model1   订单表
     * $id    $model表的id
     * $model2    佣金设置表
     * $money   钱
     *$type  订单类型
     * 3人寿险,1贷款,2理财，5财产险,4代表p2p投资险,6车险,7意外险,8物流险
     */
    public function common($mode1,$id,$model2,$money,$type){

        $user_dengji=M('Gta_user_dengji')->where(array('token'=>$this->token))->find();
        $info=M($mode1)->field('openid,orderid,name,title')->where(array('id'=>$id))->find();
        $Gta_users_model=M('Gta_users');
        //1级
        if($dopenid1=$Gta_users_model->where(array('token'=>$this->token,'openid'=>$info['openid']))->getField('dopenid')){
            $dengji=$Gta_users_model->where(array('token'=>$this->token,'openid'=>$info['openid']))->getField('dengji');
            $set=M($model2)->where(array('token'=>$this->token))->find();
            if($dengji=='金卡'){
                $total_yongji=$set['jifen1']*$money;
            }
            // echo $total_yongji;die;
            if($dengji=='白金'){
                $total_yongji=$set['jifen2']*$money;
            }
            if($dengji=='钻石'){
                $total_yongji=$set['jifen3']*$money;
            }
            if($dopenid2=$Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid1))->getField('dopenid')){//有二级
                $dopenid3=$Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid2))->getField('dopenid');
            }else{
                $dopenid3='';
            }

            //根据他有几级，导致比例不一样
            if($dopenid2&&$dopenid3){//有二级跟三级
                $yjbl1=$set['bili1'];
                $yjbl2=$set['bili2'];
                $yjbl3=$set['bili3'];
            }elseif($dopenid2){//只有二级
                $yjbl1=$set['bili1']+$set['bili3'];
                $yjbl2=$set['bili2'];
            }elseif(!$dopenid2){//只有一级
                $yjbl1=1;
            }
            // echo $yjbl1;die;
            //   echo $total_yongji;die;
            // die;
            //1级
            $data1['orderid']=$info['orderid'];
            $data1['openid']=$dopenid1;
            $data1['g_openid']=$info['openid'];
            $data1['g_name']=$info['name'];
            $data1['p_name']=$info['title'];
            $data1['yj']=$total_yongji*$yjbl1;
            //  echo $data1['yj'];die;
            $data1['yjbl']=$yjbl1;
            $data1['add_time']=time();
            $data1['token']=$this->token;
            $data1['type']=$type;
            $Edia_user_commission_model=M('Edia_user_commission');
            $Edia_user_commission_model->add($data1);
            $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid1))->setinc('jifeng',$total_yongji*$yjbl1);
            //推微信消息
            $msg_info1="您的1级客户“".$info['name']."”成功交易“".$info['title']."”,您获得佣金 ".$total_yongji*$yjbl1.'元';
            msg($this->token,$dopenid1,$msg_info1);
            //改变会员等级
            $info1= $Gta_users_model->field('jifeng,dengji')->where(array('token'=>$this->token,'openid'=>$dopenid1))->find();
            if($info1['jifeng']>=$user_dengji['yongjing1']&&$info1['jifeng']<$user_dengji['yongjing2']){//白金
                if($info1['dengji']=='金卡'){
                    $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid1))->save(array('dengji'=>'白金'));
                }
            }elseif($info1['jifeng']>=$user_dengji['yongjing2']){//钻石
                if($info1['dengji']!='钻石'){
                    $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid1))->save(array('dengji'=>'钻石'));
                }
            }
            if($dopenid2){//2级
                $data2['orderid']=$info['orderid'];
                $data2['openid']=$dopenid2;
                $data2['g_openid']=$info['openid'];
                $data2['g_name']=$info['name'];
                $data2['p_name']=$info['title'];
                $data2['yj']=$total_yongji*$yjbl2;
                $data2['yjbl']=$yjbl2;
                $data2['add_time']=time();
                $data2['token']=$this->token;
                $data2['type']=$type;
                $Edia_user_commission_model=M('Edia_user_commission');
                $Edia_user_commission_model->add($data2);
                $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid2))->setinc('jifeng',$total_yongji*$yjbl2);
                //推微信消息
                $msg_info2="您的2级客户“".$info['name']."”成功交易“".$info['title']."”,您获得佣金 ".$total_yongji*$yjbl2.'元';
                msg($this->token,$dopenid2,$msg_info2);
                //改变会员等级
                $info2= $Gta_users_model->field('jifeng,dengji')->where(array('token'=>$this->token,'openid'=>$dopenid2))->find();
                if($info2['jifeng']>=$user_dengji['yongjing1']&&$info2['jifeng']<$user_dengji['yongjing2']){//白金
                    if($info2['dengji']=='金卡'){
                        $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid2))->save(array('dengji'=>'白金'));
                    }
                }elseif($info2['jifeng']>=$user_dengji['yongjing2']){//钻石
                    if($info2['dengji']!='钻石'){
                        $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid2))->save(array('dengji'=>'钻石'));
                    }
                }
                if($dopenid3){//三级
                    $data3['orderid']=$info['orderid'];
                    $data3['openid']=$dopenid3;
                    $data3['g_openid']=$info['openid'];
                    $data3['g_name']=$info['name'];
                    $data3['p_name']=$info['title'];
                    $data3['yj']=$total_yongji*$yjbl3;
                    $data3['yjbl']=$yjbl3;
                    $data3['add_time']=time();
                    $data3['token']=$this->token;
                    $data3['type']=$type;
                    $Edia_user_commission_model=M('Edia_user_commission');
                    $Edia_user_commission_model->add($data3);
                    $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid3))->setinc('jifeng',$total_yongji*$yjbl3);
                    //推微信消息
                    $msg_info3="您的3级客户“".$info['name']."”成功交易“".$info['title']."”,您获得佣金 ".$total_yongji*$yjbl3.'元';
                    msg($this->token,$dopenid3,$msg_info3);
                    //改变会员等级
                    $info3= $Gta_users_model->field('jifeng,dengji')->where(array('token'=>$this->token,'openid'=>$dopenid3))->find();
                    if($info3['jifeng']>=$user_dengji['yongjing1']&&$info3['jifeng']<$user_dengji['yongjing2']){//白金
                        if($info3['dengji']=='金卡'){
                            $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid3))->save(array('dengji'=>'白金'));
                        }
                    }elseif($info3['jifeng']>=$user_dengji['yongjing2']){//钻石
                        if($info3['dengji']!='钻石'){
                            $Gta_users_model->where(array('token'=>$this->token,'openid'=>$dopenid3))->save(array('dengji'=>'钻石'));
                        }
                    }
                }
            }
        }
    }
}

?>