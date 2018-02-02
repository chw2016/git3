<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-7-14
 * Time: 上午10:08
 * To change this template use File | Settings | File Templates.
 */
class BaozhiyuanAction extends BaseAction{


    public function _initialize(){
        parent::_initialize();
        //$this->openid = 'orv--t5_5nbCmcihl9ywAJLPvk78';
        if($this->openid){
            $wxusers = M('Wxusers')->where(array('uid'=>120,'openid'=>$this->openid))->find();
            if($wxusers){
                $this->wxusers = $wxusers;
            }
            $this->assign('openid',$this->openid);
        }
    }

    public function duihuan(){
        $this->display();
    }

    public function address(){
		$q_code = $_REQUEST['q_code'];
        $q_secret = $_REQUEST['q_secret'];
        $type = $_REQUEST['type'];
        if(IS_POST){
            $storeModel = new Model('Store','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
            if($storedata = $storeModel->field('store_id')->where(array('store_owner_card'=>$this->token))->find()){
                $store_id = $storedata['store_id'];
                $memberModel = new Model('Member','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                if($memberdata = $memberModel->field('member_id')->where(array('belong_store_id'=>$store_id,'openid'=>$this->openid))->find()){
                    $memberid = $memberdata['member_id'];
                }else{
                    $mdata=array();
                    $mdata['member_name'] = $this->wxusers['nickname'];
                    $mdata['member_avatar'] = $this->wxusers['fakeid'];
                    $mdata['member_sex'] = $this->wxusers['sex'];
                    $mdata['openid'] = $this->openid;
                    $mdata['belong_store_id'] = $store_id;
                    if($insertid = $memberModel->data($mdata)->add()){
                        $memberid = $insertid;
                    }
                }

                $addressModel = new Model('Address','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');

				$adrdata = array();
				$adrdata['member_id'] = $memberid;
				$adrdata['true_name'] = $_POST['consignee'];
				$adrdata['area_id'] = $_POST['areaid'];
				$adrdata['city_id'] = $_POST['city_id'];
				$adrdata['area_info'] = $_POST['area_info'];
				$adrdata['address'] = $_POST['address'];
				$adrdata['mob_phone'] = $_POST['phone_mob'];
				if($address_id = $addressModel->data($adrdata)->add()){
					$this->ajaxReturn(array('code'=>0,'msg'=>'保存地址成功,请返回点击重新兑换','q_code'=>$q_code,'q_secret'=>$q_secret,'type'=>$type,'address_id'=>$address_id));
				}else{
					$this->ajaxReturn(array('code'=>-1,'msg'=>'保存地址错误,请重试'));
				}

            }


        }else{
			$this->assign('q_code',$q_code);
			$this->assign('q_secret',$q_secret);
			$this->assign('type',$type);
            $this->display();
        }
    }

    public function getduihuan(){
        $q_code = $_REQUEST['q_code'];
        $q_secret = $_REQUEST['q_secret'];
        $type = $_REQUEST['type'];
		$dtype = $_REQUEST['dtype'];
		$address_id = $_REQUEST['address_id'];
		if(!isset($dtype)){
			$dtype = 1;
		}else{
		    $dtype = 2;
		}
        $q_code_card_model = M('Qcode_card');
        $qdata = $q_code_card_model->where(array('q_code'=>$q_code,'type'=>$type,'q_secret'=>$q_secret,'token'=>$this->token))->find();
        if($qdata){
            if($qdata['status'] == 0){
                /*
                $storeModel = new Model('Store','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                if($storedata = $storeModel->field('store_id')->where(array('store_owner_card'=>$this->token))->find()){
                    $store_id = $storedata['store_id'];
                    $memberModel = new Model('Member','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                    if($memberdata = $memberModel->field('member_id')->where(array('belong_store_id'=>$store_id,'openid'=>$this->openid))->find()){
                        $memberid = $memberdata['member_id'];
                    }else{
                        $mdata=array();
                        $mdata['member_name'] = $this->wxusers['nickname'];
                        $mdata['member_avatar'] = $this->wxusers['fakeid'];
                        $mdata['member_sex'] = $this->wxusers['sex'];
                        $mdata['openid'] = $this->openid;
                        $mdata['belong_store_id'] = $store_id;
                        if($insertid = $memberModel->add()){
                            $memberid = $insertid;
                        }else{
                            $this->ajaxReturn(array('code'=>-3,'msg'=>'系统繁忙'));
                        }
                    }

                }else{
                    $this->ajaxReturn(array('code'=>-3,'msg'=>'系统繁忙'));
                }
                */
                $storeModel = new Model('Store','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                if($storedata = $storeModel->field('store_id,store_name')->where(array('store_owner_card'=>$this->token))->find()){
                    $store_id = $storedata['store_id'];
                    $memberModel = new Model('Member','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                    if($memberdata = $memberModel->field('member_id')->where(array('belong_store_id'=>$store_id,'openid'=>$this->openid))->find()){
                        $memberid = $memberdata['member_id'];
                    }else{
                        $mdata=array();
                        $mdata['member_name'] = $this->wxusers['nickname'];
                        $mdata['member_avatar'] = $this->wxusers['fakeid'];
                        $mdata['member_sex'] = $this->wxusers['sex'];
                        $mdata['openid'] = $this->openid;
                        $mdata['belong_store_id'] = $store_id;
                        if($insertid = $memberModel->add()){
                            $memberid = $insertid;
                        }
                    }

                }
                $addressModel = new Model('Address','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                if($addressdata = $addressModel->where(array('address_id'=>$address_id))->find()){
                    //xiadan
                    $orderModel = new Model('Order','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                    $orderdata = array();
                    $orderdata['order_sn'] = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
                    if($orderModel->where(array('order_sn'=>$orderdata['order_sn']))->find()){
		       $orderdata['order_sn'] = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);	
		    }
		    $orderdata['seller_id'] = 87;
                    if($type == 1){
                        $orderdata['goods_amount'] = 498;
                        $orderdata['order_amount'] = 498;
                    }else if($type == 2){
                        $orderdata['goods_amount'] = 698;
                        $orderdata['order_amount'] = 698;
                    }else if($type == 3){
                        $orderdata['goods_amount'] = 998;
                        $orderdata['order_amount'] = 998;
                    }
                    $orderdata['store_id'] = $store_id;
                    $orderdata['store_name'] = $storedata['store_name'];
                    $orderdata['buyer_id'] = $memberid;
                    $orderdata['buyer_name'] = $this->wxusers['nickname'];
                    $orderdata['add_time'] = time();
                    $orderdata['order_type'] = 0;
                    $orderdata['discount'] = 0;
                    $orderdata['shipping_fee'] = 0;
                    $orderdata['evaluation_status'] = 0;
                    $orderdata['voucher_id'] = 0;
                    $orderdata['voucher_price'] = 0;
                    $orderdata['payment_id'] = 6;
                    $orderdata['payment_name'] = '礼品券兑换';
                    $orderdata['payment_code'] = 'code';
                    $orderdata['out_sn'] = date("Ymd",time()).substr(time(),0,8);
                    $orderdata['payment_time'] = time();
                    $orderdata['order_state'] = 60;
                    $orderdata['daddress_id'] = $addressdata['address_id'];
                    if($orderid = $orderModel->data($orderdata)->add()){
                        $orderaddressModel = new Model('Order_address','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                        if($orderaddressModel->data(array('order_id'=>$orderid,'true_name'=>$addressdata['true_name'],'area_id'=>$addressdata['area_id'],
                        'city_id'=>$addressdata['city_id'],'area_info'=>$addressdata['area_info'],'address'=>$addressdata['address'],
                        'mob_phone'=>$addressdata['mob_phone']))->add());


                        $ordergoodsModel =  new Model('Order_goods','shopnc_','mysql://root:d5HKKLFJIC89sJKSw599@112.124.62.6/shopnc');
                        $ordergoodsData = array();
                        $ordergoodsData['order_id'] = $orderid;
                        $ordergoodsData['goods_num'] = 1;
                        $ordergoodsData['goods_returnnum'] = 0;
                        $ordergoodsData['stores_id'] = $store_id;
                        if($type == 1){
                            $ordergoodsData['goods_id'] = 90;
                            $ordergoodsData['goods_name'] = '鲍之源珍滋498型套装';
                            $ordergoodsData['spec_id'] = 191;
                            $ordergoodsData['goods_price'] = 498;
                            $ordergoodsData['goods_image'] = '4_4e584230b031d82b470ac8d89c16d1f7.jpg_small.jpg';
                        }else if($type == 2){
                            $ordergoodsData['goods_id'] = 91;
                            $ordergoodsData['goods_name'] = '鲍之源珍熙698型套装';
                            $ordergoodsData['spec_id'] = 193;
                            $ordergoodsData['goods_price'] = 698;
                            $ordergoodsData['goods_image'] = '4_14b186ff1cb813ed43c91ca2f519dbca.jpg_small.jpg';
                        }else if($type == 3){
                            $ordergoodsData['goods_id'] = 92;
                            $ordergoodsData['goods_name'] = '鲍之源御珍998型套装';
                            $ordergoodsData['spec_id'] = 192;
                            $ordergoodsData['goods_price'] = 998;
                            $ordergoodsData['goods_image'] = '4_89f2abc962e2bfe6cea74c1c4ace8f22.jpg_small.jpg';
                        }
                        if($ordergoodsModel->data($ordergoodsData)->add()){

                            if($q_code_card_model->where(array('q_code'=>$q_code,'type'=>$type,'q_secret'=>$q_secret,'token'=>$this->token))
                                ->data(array('status'=>1,'wxuid'=>$this->wxusers['id'],'order_sn'=>$orderdata['order_sn'],'order_id'=>$orderid,
                                    'phone'=>$addressdata['mob_phone'],'true_name'=>$addressdata['true_name'],'address_info'=>$addressdata['area_info'].$addressdata['address']))->save()){
                                $param = array();
                                $param['title'] = '系统订单通知';
								$param['store_id'] = 4;
                                $param['description'] = '恭喜您兑换成功,感谢您对鲍之源的支持,您的订单我们已收到，我们的工作人员会尽快处理！订单编号为:'. $orderdata['order_sn'];
                                $param['url'] ='http://mall.wapwei.com/index.php?act=member&op=order&openid='.$this->openid;
                                $param['picurl'] = '';
                                $this->send_wx_msg($param);
								if($dtype == 2){
									header("Location:http://mall.wapwei.com/index.php?act=member&op=show_order&order_id=".$orderid."&openid=".$this->openid."&bid=4");
								}else{
                                   $this->ajaxReturn(array('code'=>0,'msg'=>'兑换成功,返回我的订单即可查看兑换状态','order_id'=>$orderid));
								}
                            }else{
                                $this->ajaxReturn(array('code'=>-2,'msg'=>'系统繁忙'));
                            }
                        }else{
                            $this->ajaxReturn(array('code'=>-5,'msg'=>'系统繁忙'));
                        }
                    }else{
                        $this->ajaxReturn(array('code'=>-2,'msg'=>'兑换失败,请重试'));
                    }
                }else{
                    $this->ajaxReturn(array('code'=>5,'msg'=>'礼品券正常,请先填写收货地址','q_code'=>$q_code,'q_secret'=>$q_secret,'type'=>$type));
                }
            }else if($qdata['status'] == 1){
                $this->ajaxReturn(array('code'=>-2,'msg'=>'卡券已被使用'));
            }
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'您输入的卡券有问题哦'));
        }
    }

    private function send_wx_msg($param){
        $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>json_encode($param));
        $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendNewsMsg";
        $data = $this->api_notice_increment($url,http_build_query($postdata));
        if(!$data){
            $this->api_notice_increment($url,http_build_query($postdata));
        }
    }




}
