<?php
class LoanAction extends CliAction {
    /*
     *  计算订单余期状态
     */
    public  $token='55cad4ba46c41a8fde9c84274e36fa83';
    public $token_phone;
    public function _initialize(){
        $this->token_phone=M('speeddial')->where(array('token'=>$this->token))->getField('phone');
    }
    public function index()//每天晚上0点跑一次
    {


        $list1=(array)M('No_credit_order')->field('id,type,paystatus,is_yq,hs_time')->where(array('type'=>1,'paystatus'=>5,'token'=>$this->token))->select();
        $list2=(array)M('No_credit_order')->field('id,type,paystatus,is_yq,hs_time')->where(array('type'=>2,'paystatus'=>1,'token'=>$this->token))->select();
        $list=array_merge($list1,$list2);

        foreach($list as $k=>$v){
            $end_time=date('Y-m-d',time());
            $start_time=date('Y-m-d',$v['hs_time']);
            $qisu=getMonthNum($start_time,$end_time);
            $a=0;
            for($i=1;$i<=$qisu;$i++){
               if(!M('Hk_jl')->where(array('oid'=>$v['id'],'qisu'=>$i,'paystatus'=>1))->find()){//余期了
                    $a=1;
                     break;
                }
            }
            if($a==1){//已余期
                M('No_credit_order')->where(array('id'=>$v['id']))->save(array('is_yq'=>1));
            }else{
                M('No_credit_order')->where(array('id'=>$v['id']))->save(array('is_yq'=>0));
            }
        }


    }
    /**
     * 发短信  15分钟跑一次吧
     */
    public function sendSms(){
      //  echo $this->token_phone;
        $orders=M('No_credit_order')->where(array('add_time'=>array('elt',(time()-15*60)),'token'=>$this->token,'type'=>1,'paystatus'=>array('in',array(0,1))))->select();
       // p($orders);

        foreach($orders as $k=>$v){
            if($v['paystatus']==0&&$v['sms']==0){//下单未支付
                $phone=M('Credit_users')->where(array('id'=>$v['uid']))->getField('phone');

           //     $order=M('No_credit_order')->where(array('id'=>$_GET['id']))->getField('orderid');


                $info="【如多分期】亲，恭喜你抢购成功如多分期".$v['title']."。如此好运，怎不继续？打开如多分期公众号，一键支付，开始你的美丽蜕变。有问题？打这个".$this->token_phone."(24小时后若未付款订单自动取消)。";
                $openidYz=sendPhomeCode($this->token,$phone,$info);
                $openidYz=json_decode($openidYz,true);
                if($openidYz['code']==0){//为真
                    $notichcontent ="【如多分期】亲，恭喜你抢购成功如多分期".$v['title']."。如此好运，怎不继续？打开如多分期公众号，一键支付，开始你的美丽蜕变。有问题？打这个".$this->token_phone."(24小时后若未付款订单自动取消)。";
                    $postdata = array('openid'=>$v['openid'],'token'=>$this->token,'content'=>$notichcontent);
                    $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                    $data = $this->api_notice_increment($url,http_build_query($postdata));
                    if(!$data){
                        $this->api_notice_increment($url,http_build_query($postdata));
                    }
                    M('No_credit_order')->where(array('id'=>$v['id']))->save(array('sms'=>1));
                }
            }

            if($v['paystatus']==1&&$v['sms']!=2){//支付首付，没有填资料,有没有图片来判断
                 if(!M('Credit_users')->where(array('id'=>$v['uid']))->getField('pid')){
                     $phone=M('Credit_users')->where(array('id'=>$v['uid']))->getField('phone');
                     $info="【如多分期】客官，你下的如多分期".$v['title']."产品一枚成功完成支付啦！你造么？你离美丽蜕变只剩最后一步：打开订单页，继续提交个人资料信息，然后我们将尽快审核你的信息。有问题？打这个".$this->token_phone."，咱们的男神客服为您服务！";
                     $openidYz=sendPhomeCode($this->token,$phone,$info);
                     $openidYz=json_decode($openidYz,true);
                     if($openidYz['code']==0){//为真
                         $notichcontent ="【如多分期】客官，你下的如多分期".$v['title']."产品一枚成功完成支付啦！你造么？你离美丽蜕变只剩最后一步：打开订单页，继续提交个人资料信息，然后我们将
                         尽快审核你的信息。有问题？打这个".$this->token_phone."，咱们的男神客服为您服务！";
                         $postdata = array('openid'=>$v['openid'],'token'=>$this->token,'content'=>$notichcontent);
                         $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                         $data = $this->api_notice_increment($url,http_build_query($postdata));
                         if(!$data){
                             $this->api_notice_increment($url,http_build_query($postdata));
                         }
                         M('No_credit_order')->where(array('id'=>$v['id']))->save(array('sms'=>2));
                         }
                     }
            }

            if($v['paystatus']==1&&$v['sms']!=3){//支付首付，填了资料,有没有图片来判断
                if(M('Credit_users')->where(array('id'=>$v['uid']))->getField('pid')){
                    $phone=M('Credit_users')->where(array('id'=>$v['uid']))->getField('phone');

                    //     $order=M('No_credit_order')->where(array('id'=>$_GET['id']))->getField('orderid');
                   $info="【如多分期】亲，我收到你的订单啦，是“".$v['title']."”么？真好！接下来是订单处理时间，我们在1天内给您回馈处理结果。心急打这个".$this->token_phone."，咱们的男神客服为您服务！";
                    $openidYz=sendPhomeCode($this->token,$phone,$info);
                    $openidYz=json_decode($openidYz,true);
                    if($openidYz['code']==0){//为真
                        $notichcontent ="【如多分期】亲，我收到你的订单啦，是“".$v['title']."”么？真好！接下来是订单处理时间，我们在1天内给您回馈处理结果。心急打这个".$this->token_phone."，咱们的男神客服为您服务！";
                        $postdata = array('openid'=>$v['openid'],'token'=>$this->token,'content'=>$notichcontent);
                        $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                        $data = $this->api_notice_increment($url,http_build_query($postdata));
                        if(!$data){
                            $this->api_notice_increment($url,http_build_query($postdata));
                        }
                        M('No_credit_order')->where(array('id'=>$v['id']))->save(array('sms'=>3));
                    }
                }
            }
        }

    }

    /**
     * 1小时跑一次吧
     * 自动取消订单
     */
    public function quxiao(){
        $orders=M('No_credit_order')->where(array('add_time'=>array('elt',(time()-60*24*60)),'token'=>$this->token,'type'=>1,'paystatus'=>0))->save(array('paystatus'=>-1));


    }

}
