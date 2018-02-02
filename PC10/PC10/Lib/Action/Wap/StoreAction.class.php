<?php
/**
 *Wap端处理
 * @author NICK
 *
 */
class StoreAction extends BaseAction{

	public $token;
	public $wx_openid;
	public $storeGoodsModel;
	public $storeModel;
	public $storeClassModel;
	public $wxUsersModel;
	public $userModel;
	public $mapModel;
	public $orderModel;
	public $userData;
	public $wxUsersData;

	public function _initialize() {
		parent::_initialize();

		$agent = $_SERVER['HTTP_USER_AGENT'];
		if(!strpos($agent,"MicroMessenger")) {
			//	echo '此功能只能在微信浏览器中使用';exit;
		}

		if ((!session('?token')) || (!session('?openid'))) {
			session('token', $_REQUEST['token']);
			session('wecha_id', $_REQUEST['wecha_id']);
		}
		$this->token =  $_REQUEST['token'];
		$this->wx_openid =  $_REQUEST['wecha_id'];
        if(empty($this->wx_openid)){
            $this->wx_openid=$_REQUEST['openid'];
        }

		$this->storeGoodsModel = M('shopdoor_goods');
		$this->storeModel = M('shopdoor');
		$this->storeClassModel = M('shopdoor_goods_class');
		$this->orderModel = M('shopdoor_goods_order');
		$this->wxUsersModel = M('wxusers');
		$this->userModel = M('wxuser');

		$this->assign('token',$this->token);
		$this->assign('wecha_id', $this->wx_openid);
	}

	/*单击进入，根据地理位置展现出附近的商店*/
	public function index() {

		$condition['token'] = $this->token;
		$userData = $this->userModel->where($condition)->find();
		if (!empty($userData)) {
			unset($condition['token']);
			$condition['uid'] = $userData['id'];
			$condition['openid'] = $this->wx_openid;
			$result = $this->wxUsersModel->where($condition)->find();
			if (!empty($result)) {

				if ($result['city'] != null) {
					$where['wxuser_id'] = $userData['id'];
					//$where['door_adress'] = array('like',"%".$result['city']."%");
					$storeData = $this->storeModel->where($where)->order('id')->select();
				} else {
					$where['wxuser_id'] = $userData['id'];
					$storeData = $this->storeModel->where($where)->order('id')->select();
				}
			} else {
				$where['wxuser_id'] = $userData['id'];
				$storeData = $this->storeModel->where($where)->order('id')->select();
			}
		}

		$this->assign('title', $userData['wxname']);
		$this->assign('data', $storeData);
		$this->display();
	}

	/*商店信息*/
	public function info() {

		//这个是通过$_GET方法获取id得到的一个商品的展示
		$result = $this->storeModel->where(array('id'=>$_GET['id']))->find();
		$this->assign('id', $_GET['id']);
		$this->assign('data', $result);
		$this->display();
	}

	/*是否在营业时间段里面*/
	public function isInServiceTime() {

		$storeId = $_POST['id'];
		$isExist = $this->storeModel->where(array('id'=>$storeId))->find();
       // p($isExist);die;
		if (!empty($isExist)) {
            if($isExist['door_end_time']>$isExist['door_start_time']){//没有过第二天
                if ((intval(date("H")) >= intval($isExist['door_start_time'])) && (intval(date("H")) < intval($isExist['door_end_time']))) {

                    $data = array('status'=> 0, 'body'=>date('ymdHis'), 'errorCode'=>0,'errorMsg'=>null);
                    $this->ajaxReturn($data);
                } else {
                    $data = array('status'=> 0, 'body'=>'is not in serverTime', 'errorCode'=>1,'errorMsg'=>null);
                    $this->ajaxReturn($data);
                }
            }else{
                if (((intval(date("H")) >= intval($isExist['door_start_time'])) && (intval(date("H")) <= 24)||(intval(date("H"))<$isExist['door_end_time']))) {

                    $data = array('status'=> 0, 'body'=>date('ymdHis'), 'errorCode'=>0,'errorMsg'=>null);
                    $this->ajaxReturn($data);
                } else {
                    $data = array('status'=> 0, 'body'=>'is not in serverTime', 'errorCode'=>1,'errorMsg'=>null);
                    $this->ajaxReturn($data);
                }
            }

		}
		$data = array('status'=> 0, 'body'=>'is not exit!', 'errorCode'=>1,'errorMsg'=>null);
		$this->ajaxReturn($data);
	}

	/*商品种类提供选择*/
	public function classList() {

		if (isset($_GET['id'])) {
			$storeId = $_GET['id'];
			//非促销优惠产品类
			$isExist = $this->storeClassModel->where(array('store_id'=>$storeId, 'goods_class_type'=>0))->select();

			//非促销优惠产品类
			$isDeals = $this->storeClassModel->where(array('store_id'=>$storeId, 'goods_class_type'=>1))->select();
			if (($isExist == true) || ($isDeals == true)) {
				//非促销优惠产品分类
				$result = $this->storeModel->where(array('id'=>$storeId))->find();
				$this->assign('storeName', $result['door_name']);
				$this->assign('storeInfo', $result['door_info']);
				$this->assign('storeTime', $result['door_start_time']."到".$result['door_end_time']);
				$this->assign('storeAddress', $result['door_adress']);
				$this->assign('promptInfo', $result['door_prompt_info']);
				$this->assign('storeId', $result['id']);
				$this->assign('data', $isExist);
				$this->assign('isData', $isDeals);

				$wxUserData = $this->wxUsersModel->where(array('openid'=>$this->wx_openid))->find();
				$wxUserNewDatas = array();
				if (!empty($wxUserData)) {
					$wxUserNewDatas['id'] = md5($wxUserData['id']);
					$wxUserNewDatas['name'] = $wxUserData['nickname'];
					$wxUserNewDatas['gender'] = $wxUserData['sex'];
					$wxUserNewDatas['regdate'] = $wxUserData['subscribe_time'];
					$wxUserNewDatas['lastlogindate'] = $wxUserData['last_say_time'];
					$wxUserNewDatas['chosenAddress'] = $wxUserData['city'];
				}
				$wxUserData = $this->encode($wxUserNewDatas);
				$this->assign('userInfoData', $wxUserData);
			}
		}
		$this->display();
	}


	/*进入分店观看产品*/
	public function goodsList() {
		//echo 1;die;
		if (isset($_GET['id'])) {  //门店ID
			$storeId = $_GET['id'];
			$isInStore = $this->storeModel->where(array('id'=>$storeId))->find();
			if (!empty($isInStore)) {
				$result = $this->storeClassModel->where(array('store_id'=>$storeId))->select();

				$this->assign('classData',$result);
				$condition['goods_classify'] = $_GET['classId'];
				$condition['goods_belong'] = $storeId;
				$condition['goods_display'] = 1;
				$result = $this->storeGoodsModel->where($condition)->select();

				if (!empty($result)) {
					$resultData = array();
					foreach ($result as $key => $value){
						$resultData[$key]['productId'] = $value['id'];
						$resultData[$key]['smallImgUrl'] = $value['goods_url'];
						$resultData[$key]['largeImgUrl'] = $value['goods_url'];
						$resultData[$key]['shownamecn'] = $value['goods_name'];
						$resultData[$key]['shownameen'] = $value['goods_name'];
						$resultData[$key]['idesccn'] = $value['goods_inf'];
						$resultData[$key]['idescen'] = $value['goods_inf'];
						$resultData[$key]['price'] = $value['goods_price']*100;
						$resultData[$key]['mFlag'] = 'p';
						$resultData[$key]['baseid'] = $value['goods_classify'];
						$resultData[$key]['size'] = $value['goods_size'];
					}
                    //p($resultData);die;
					$javaScriptData = $this->encode($resultData);
					$this->assign('javaScriptData', $javaScriptData);
				}


				/*查找微信用户的信息*/
				$wxUserData = $this->wxUsersModel->where(array('openid'=>$this->wx_openid))->find();

				if (!empty($wxUserData)) {
					$wxUserNewDatas = array();
					$wxUserNewDatas['id'] = md5($wxUserData['id']);
		//			$wxUserNewDatas['name'] = substr_replace($wxUserData['nickname'], "*", 2) ;//这种不能被解析成json数据
					$wxUserNewDatas['name'] = $wxUserData['nickname'];
					$wxUserNewDatas['gender'] = $wxUserData['sex'];
					$wxUserNewDatas['regdate'] = $wxUserData['subscribe_time'];
					$wxUserNewDatas['lastlogindate'] = $wxUserData['update_time'];
					$wxUserNewDatas['chosenAddress'] = $wxUserData['city'];
					$wxUserNewDatas = $this->encode($wxUserNewDatas);
					$this->assign('userInfoData', $wxUserNewDatas);
				}
// 	 			$wxUserData = $this->encode($wxUserData);
			}
		}

		$classData = $this->storeClassModel->where(array('id'=>$_GET['classId']))->find();


        if($isInStore['door_end_time']>$isInStore['door_start_time']){//没有到第二天
            $this->assign('storeTime', $isInStore['door_start_time']."点到".$isInStore['door_end_time'].'点');
        }else{
            $this->assign('storeTime', $isInStore['door_start_time']."点到凌晨".$isInStore['door_end_time'].'点');
        }

		$this->assign('storeAddress', $isInStore['door_adress']);
		$this->assign('promptInfo', $isInStore['door_prompt_info']);
		$this->assign('className', $classData['goods_class_name']);
		$this->assign('storeName', $isInStore['door_name']);
		$this->assign('storeId',$storeId);
		$this->assign('classId', $_GET['classId']);
		$this->display();
	}

	/*个人中心信息*/
	public function userinfo() {

		$userInfoData = $this->wxUsersModel->where(array('openid'=>$this->wx_openid))->find();
		$orderInfoData = $this->orderModel->where(array('wxopen_id'=>$this->wx_openid))->select();
		foreach ($orderInfoData as $key =>$value) {

			$orderInfoData[$key]['order_ok_time'] = date('Y-m-d', $value['order_ok_time']);
		}
		$this->assign('orderInfoData', $orderInfoData);
		$this->assign("userInfoData", $userInfoData);
		$this->assign('storeId', $_GET['id']);
		$this->display();

	}

	/*购物车提交订单*/
	public function confirm() {

//		$storeId = $_GET['id'];
//		$token = $_GET['token'];
//		$openId = $_GET['wecha_id'];
		if (IS_POST) {
          //  p( $_POST['orderItems']);die;
			session('storeId', $storeId);
			$cart = array();
			$totlePrice = 0;
			$orderData = $_POST['orderItems'];
			$orderData = htmlspecialchars_decode($orderData);
			$orderData = json_decode($orderData);
           // p($orderData);die;
			foreach ($orderData as $key => $value) {
				$cart[$value->mealData->productId]['cnt'] = $value->cnt;
				$cart[$value->mealData->productId]['shownamecn'] = $value->mealData->shownamecn;
				$cart[$value->mealData->productId]['price'] = $value->mealData->price/100;
				$cart[$value->mealData->productId]['totle'] = ($value->cnt)*($value->mealData->price/100);
				$totlePrice += $cart[$value->mealData->productId]['totle'];
			}

			session('totlePrice',$totlePrice);
			session('cart', $cart);
//			session($this->wx_openid, $cart);
			//返回一个order对象的数据
			$i = 0;
			foreach ($cart as $key => $value) {
				$productList[$i]['name'] = $value['shownamecn'];
				$productList[$i]['price'] = $value['price']*100;
				$productList[$i]['totalPrice'] = $value['totle']*100;
				$productList[$i]['quantity'] = $value['cnt'];
				$productList[$i]['promotionType'] = 0;
				$productList[$i]['promotionDesc'] = null;
				$productList[$i]['couponCode'] = null;
				$productList[$i]['promotionCode'] = null;
				$productList[$i]['mealItems'] = null;
				$productList[$i]['meal'] = false;

				$productList[$i]['deliveryFee'] = 0;
				$body['totalPrice'] += $value['totle'];
				$body['subTotalPrice'] += $value['totle'];
				$i++;
			}
			$body['productList'] = $productList;
			$orderId = date('ymdHis').rand(1000,2000);
			$body['orderId'] = $orderId;
			$body['deliveryFee'] = 0;
			$body['orderType'] = 1;
			$body['showCode'] = null;
			$body['couponPromotion'] = array();
			$body['promotionList'] = array();

			$body['spCodes'] = array('FreeDeliveryFeeCode'=>-1, 'FreeSoupCode'=>-1);
			$returnJsonData = array('body'=>$body,'errorCode'=>0,'errorMsg'=>'','status'=>0);
			$returnJsonData = $this->encode($returnJsonData);
			echo $returnJsonData;
//
//			$returnJsonData = array('body'=>"",'errorCode'=>0,'errorMsg'=>date('ymdHis'),'status'=>0);
//			$returnJsonData = $this->encode($returnJsonData);
//			echo $returnJsonData;

		}
	}

	/*显示购物车*/
	public function shoppingcart() {

		$storeId = $_GET['id'];
		$this->assign('storeId', $storeId);
		$orderId = date('ymdHis').rand(1000,2000);
		$cart = session('cart');
		foreach ($cart as $key => $value) {

			$cart[$key]['subtotal'] = ($value['price'])*($value['cnt']);
		}

		//以一定的格式返回数据
		$this->assign('totlePrice', session('totlePrice'));
        //print_r($cart);exit;

		$this->assign('cartData', $cart);
		$this->assign('JsonCartData', $this->encode($cart));
		$this->display();
	}

	/*搜索分店*/
	public function storeSearch() {

		if (isset($_POST['name'])) {
			$result = $this->userModel->where(array('token'=>$this->token))->find();
			$conditons['wxuser_id'] = $result['id'];
// 			$conditons['door_name'] = $_POST['name'];
// 			$conditons['door_name'] = array('like',"%".$_POST['name']."%");
			$conditons['door_adress'] = array('like',"%".$_POST['name']."%");
// 			$conditons['_logic'] = 'OR';
			$storeBackData = $this->storeModel->where($conditons)->select();
			$this->assign("data", $storeBackData);
			$this->display('Store:index');
		}
	}


	/*保存订单*/
	public function save() {

		$storeId = $_GET['id'];
		if (IS_POST) {
         //   p($_POST['orderData']);die;
			$orderData = $_POST['orderData'];
			$orderData = htmlspecialchars_decode($orderData);
			$orderData = json_decode($orderData);
          //  p($orderData);die;

			$userData = $this->userModel->where(array('token'=>$this->token))->find();
			$insertData = array();
			$insertData['order_id'] = date('ymdHis').rand(1000,2000);
			$insertData['wxuser_id'] =  $userData['id'];
			$insertData['wx_openid'] = $this->wx_openid;
			$insertData['door_id'] = $_GET['id'];
			foreach ($orderData as $key => $value) {
				$insertData['order_price'] += $value->cnt*$value->price;
				$insertData['order_info'].= ('商品名称:'.$value->shownamecn.',数量:'.$value->cnt.',单价:'.$value->price.',总价:'.$value->subtotal).'|';
			}

			$insertData['order_ok_time'] = time();
			$insertData['order_status'] = 0;
			$insertData['order_extra_info'] = '';
			$insertData['door_id'] = $storeId;
			$insertData['order_user'] = '';
			$insertData['order_user_phone'] = '';
			$insertData['order_adress'] = '';
			$insertData['order_consume_time'] = (time() + 10*24*3600);
			$insertData['order_consume_type'] = 1;
			$insertData['order_consume_num'] = 1;
			$insertData['order_go_door'] = 0;
			$insertData['order_is_read'] = 0;
			$insertResult = $this->orderModel->add($insertData);
			if ($insertResult) {
				session($this->wx_openid.'lastOrderId', $insertResult);
				$data = array('success'=>1);
				$data = json_encode($data);
				echo $data;
			} else {
				$data = array('error'=>1);
				$data = json_encode($data);
				echo $data;
			}
		}
	}

	public function settlement() {

		$storeId = $_GET['id'];
		$orderInfoData = $this->orderModel->where(array('id'=>session($this->wx_openid.'lastOrderId')))->find();
		//P($orderInfoData);
		$this->assign('totlePrice', $orderInfoData['order_price']);
        $lastInfoData = $this->orderModel->field('order_user,order_user_phone,order_adress')->where("wx_openid='".$this->wx_openid."' and order_user != ''")->order('order_ok_time desc')->limit(1)->find();

        $this->assign('storeId', $storeId);
        $this->assign('lastInfoData', $lastInfoData);
        $this->assign('orderInfoData', $orderInfoData);
        $weipayModel=M('Weipay_config');
        $weipay = $weipayModel->where(array('token'=>$this->token,'is_open'=>1))->find();
        if($weipay){
            $this->assign('is_weipay',1);
        }else{
            $this->assign('is_weipay',0);
        }
		$this->display();
	}

	public function checkOrder() {

		if (IS_POST) {

			$updateData['order_user'] = $_POST['name'];
			$updateData['pay_type'] = $_POST['pay_type'];
			$updateData['wx_openid'] = $this->wx_openid;
			$updateData['order_user_phone'] = $_POST['phone'];
			$updateData['order_adress'] = $_POST['address'];
			$updateData['is_waisong'] = $_POST['is_waisong'];
			$updateData['order_extra_info'] = $_POST['order_extra_info'];
			$updateData['id'] = session($this->wx_openid.'lastOrderId');
			$updateData['sh_time'] = $_POST['sh_time'];

            $findorderData = $this->orderModel->field('door_id')->where(array('id'=>session($this->wx_openid.'lastOrderId')))->find();
            $adminphonedata = M('Shopdoor')->field('door_phone')->where(array('id'=>$findorderData['door_id']))->find();
			$updateResult = $this->orderModel->save($updateData);
            //if($updateData['pay_type'] == 1){
            if ($updateResult) {
                /*结算，发送信息*/

                $sms_config_model=M('config_sms');
                $check=$sms_config_model->where(array('token'=>$this->token))->find();

                //发短信
                if($check){
                    // http://api.sms.cn/mt/?uid=用户账号&pwd=MD5位32密码&mobile=号码&content=内容
                    //$contentdata = "尊敬的".$updateData['order_user']."，感谢您使用微信在线点餐。您的订单本店已收到，正在为您准备，稍后与您联系，谢谢！【".$this->tpl['name']."】";
                    //$contentdata = "您的验证码是".$updateData['order_user']."【".$this->tpl['name']."】";
                    //$contentdata = "【".$this->tpl['name']."】"."尊敬的".$updateData['order_user']."，感谢您使用微信在线点餐。您的订单本店已收到，正在为您准备，稍后与您联系，谢谢！";
                    $contentdata = "【".$this->tpl['name']."】尊敬的".$updateData['order_user']."，您的订单本店已收到稍后与您联系谢谢！";

                    $data['token'] = $this->token;
                    $data['content'] = $contentdata;
                    $data['type'] = 2;
                    $data['phone'] = $updateData['order_user_phone'];
                    $data['add_time'] = time();
                    $data['is_ok'] = 0;

                    //$contentdata2 = $updateData['order_user']."下了,手机号码为！".$updateData['order_user_phone']."【".$this->tpl['name']."】";
                    $contentdata2 = "您的验证码是".$updateData['order_user_phone']."【".$this->tpl['name']."】";

                    $adata['token'] = $this->token;
                    $adata['content'] = $contentdata2;
                    $adata['type'] = 3;
                    $adata['phone'] = $adminphonedata['door_phone'];
                    $adata['add_time'] = time();
                    $adata['is_ok'] = 0;

                    //$contentdata = "您正在设置万普微信平台用户中心手机验证,验证码为：".$data['content']."，请及时验证有效期为3分钟【".$this->tpl['name']."】";
                    //$contentdata = "【".$this->tpl['name']."】验证码为：".$data['content']."，请在30分钟之内按页面提示提交验证码";
                    /*$url = "http://api.sms.cn/mt/?uid=".$check['account']."&encode=utf8&pwd=".md5($check['pwd'].$check['account'])."&mobile=".$phone."&content=".$contentdata;
                    $returncontent = file_get_contents($url);

                    parse_str($returncontent,$arr);
                    */
                    //$contentdata = "您的验证码是".$data['content']."【".$this->tpl['name']."】";


                    $url = 'http://yunpian.com/v1/sms/send.json';
                    $apidata['text'] = urlencode("$contentdata");
                    $apidata ="apikey=".$check['apikey']."&text=".$apidata['text']."&mobile=".$updateData['order_user_phone'];
                    $returndata = $this->api_notice_increment($url,$apidata);
                    $returndata = json_decode($returndata);


                    $apidata2['text'] = urlencode("$contentdata2");
                    $apidata2 ="apikey=".$check['apikey']."&text=".$apidata2['text']."&mobile=".$adminphonedata['door_phone'];
                    $returndata2 = $this->api_notice_increment($url,$apidata2);
                    $returndata2 = json_decode($returndata2);

                    if($returndata && $returndata->code == 0){
                        M('Sms_send_list')->add($data);
                    }
                    if($returndata2 && $returndata2->code == 0){
                        M('Sms_send_list')->add($adata);
                    }

                }

                $userData = $this->userModel->where(array('token'=>($this->token)))->find();
                $wxUserData = $this->wxUsersModel->where(array('uid'=>$userData['id'], 'openid'=>$this->wx_openid))->find();
                $orderData = $this->orderModel->where(array('id'=> $updateData['id']))->find();
                if($updateData['pay_type'] == 1){
                    $notichcontent = $userData['nickname']."您好".$wxUserData['name']."已经收到您的订单,订单编号:".$orderData['id'];
                    $postdata = array('openid'=>$this->wx_openid,'token'=>$this->token,'content'=>$notichcontent);
                    $url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";
                    $data = $this->api_notice_increment($url,http_build_query($postdata));

                    if(!$data){
                        $this->api_notice_increment($url,http_build_query($postdata));
                    }
                }
            //$this->wifip(array('id' => session($this->wx_openid.'lastOrderId')));





                $backData = array('success'=>1);
                $backData = json_encode($backData);
                echo $backData;
            } else {
                $backData = json_encode(array('success'=>0));

                echo $backData;
            }
            //}

            /*else if($updateData['pay_type'] == 2){
                $orderInfoData = $this->orderModel->where(array('id'=>session($this->wx_openid.'lastOrderId')))->find();
                $url = C('JS_API_CALL_URL').$this->token.'/';
                $html = '<html><head></head><body>';
                $html .= '<form method="post" name="E_FORM" action='.$url.'>';
                $html .= "<input type='hidden' name='order_money' value='".$orderInfoData['order_price']."' />";
                $html .= "<input type='hidden' name='type' value='Store_goods_order' />";
                $html .= "<input type='hidden' name='order_text' value='".$orderInfoData['order_info']."' />";
                $html .= "<input type='hidden' name='orderid' value='".session($this->wx_openid.'lastOrderId')."' />";
                $html .= "<input type='hidden' name='openid' value='".$this->wx_openid."' />";
                $html .= '</form><script type="text/javascript">document.E_FORM.submit();</script>';
                $html .= '</body></html>';
                echo $html;
                exit;
            }  */
		}
	}


    public function notice($updateData)
    {
        $sms_config_model=M('config_sms');
	$token = $_GET['token'];
	$openid = $updateData['wx_openid'];
        if($check=$sms_config_model->where(array('token'=>$token))->find()){
            $contentdata = "【".$this->tpl['name']."】尊敬的".$updateData['order_user']."，您的订单本店已收到稍后与您联系谢谢！";
            $data['token'] = $updateData['token'];
            $data['content'] = $contentdata;
            $data['type'] = 2;
            $data['phone'] = $updateData['order_user_phone'];
            $data['add_time'] = time();
            $data['is_ok'] = 0;

            $contentdata2 = "您的验证码是".$updateData['order_user_phone']."【".$this->tpl['name']."】";
            $adata['token'] = $updateData['token'];
            $adata['content'] = $contentdata2;
            $adata['type'] = 3;
            $adata['phone'] = $adminphonedata['door_phone'];
            $adata['add_time'] = time();
            $adata['is_ok'] = 0;

            $url = 'http://yunpian.com/v1/sms/send.json';
            $apidata['text'] = urlencode("$contentdata");
            $apidata ="apikey=".$check['apikey']."&text=".$apidata['text']."&mobile=".$updateData['order_user_phone'];
            $returndata = $this->api_notice_increment($url,$apidata);
            $returndata = json_decode($returndata);


            $apidata2['text'] = urlencode("$contentdata2");
            $apidata2 ="apikey=".$check['apikey']."&text=".$apidata2['text']."&mobile=".$adminphonedata['door_phone'];
            $returndata2 = $this->api_notice_increment($url,$apidata2);
            $returndata2 = json_decode($returndata2);

            if($returndata && $returndata->code == 0){
                M('Sms_send_list')->add($data);
            }
            if($returndata2 && $returndata2->code == 0){
                M('Sms_send_list')->add($adata);
            }
        };

        $userData = $this->userModel->where(array('token'=>($token)))->find();
        $wxUserData = $this->wxUsersModel->where(array('uid'=>$userData['id'], 'openid'=>$openid))->find();
        $orderData = $this->orderModel->where(array('id'=> $updateData['id']))->find();
        $notichcontent = $userData['nickname']."您好".$wxUserData['name']."已经收到您的订单,订单编号:".$orderData['id'];
        $postdata = array('openid'=>$openid,'token'=>$token,'content'=>$notichcontent);
        $url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";
        $data = $this->api_notice_increment($url,http_build_query($postdata));

        if(!$data){
            $this->api_notice_increment($url,http_build_query($postdata));
        }
    }

    function wifip($aWhere, $extra='', $force=false){
        $rowData = $this->orderModel
            ->where($aWhere)
            ->find();

        if (!$rowData || $rowData['is_new']) {//已经打印了
            if (!$force) {
                return;
            }
        }

        $a = $rowData['order_info'];
    	$a=str_replace('商品名称:','', $a);
    	$a=str_replace('单价:','', $a);
    	$a=str_replace('数量:','', $a);
    	$a=str_replace('总价:','', $a);
    	$a=explode('|',$a);
    	$a=array_filter($a);
    	$str[]=explode(',',$a['0']);
    	$str='';
    	foreach ($a as $ke=>$v){
    		$str[]=explode(',',$v);
    	}
    	$str2='';
        foreach ($str as $ke=>$v){
        if(strlen($v['0'])==6){
            $str2.=$v['0'].'       '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
        }elseif(strlen($v['0'])==9){
            $str2.=$v['0'].'     '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
        }elseif(strlen($v['0'])==12){
            $str2.=$v['0'].'   '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
        }elseif(strlen($v['0'])==15){
            $str2.=$v['0'].' '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
        }else{
            $str2.=$v['0'].'   '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
        }
        }




        $order_ok_time = date('Y-m-d H:i:s', $rowData['order_ok_time']);
        $beizhu = $rowData['order_extra_info'] ?:'无';

        switch ($rowData['order_status']){
            case 0:$rowData['order_status']='未付款';break;
            case 1:$rowData['order_status']='已付款';break;
            case 2:$rowData['order_status']='已取消';break;
        }


        $content="兴顺餐饮
".
"订单号:{$rowData['id']}
".
"------------------
"."送达时间:{$rowData['sh_time']}
".
"------------------
"."买家：{$rowData['order_user']}
".
"是否付款:{$rowData['order_status']}
".
"下单时间：{$order_ok_time}
".
"******************
".
"------------------
".
"商品名称 数量 单价 总价
{$str2}
".
"备注:{$beizhu}
".
"------------------
".
"合计：{$rowData['order_price']}元(含配送费￥0.0)
".
"------------------
".
"送货地址：{$rowData['order_adress']}
".
"联系电话：{$rowData['order_user_phone']}
".
"{$extra}";

    $w=new WifiPrint($_REQUEST['token']);

    WL('print:' . $content);

    $this->orderModel
        ->where($aWhere)
        ->data(array('is_new' => 1))
        ->save();

	return $w->test($content);
    }


	/*订单列表*/
	public function orderList() {

// 		$lastOrderId = session(lastOrderId);
// 		if ($lastOrderId) {
// 			$orderInfoData = $this->orderModel->where(array('id'=> $lastOrderId))->find();
// 			if (!empty($orderInfoData)) {
// 				$this->assign('orderInfoData', $orderInfoData);
// 			}
// 		}

		$condition['wx_openid'] = $this->wx_openid;
		$queryResult = $this->orderModel->where($condition)->order('order_ok_time desc')->select();
		if (!empty($queryResult)) {
			$userInfoData = $this->wxUsersModel->where(array('openid'=>$this->wx_openid))->find();

            //is_new  0代表是新单，1代表旧单，已经推送过消息
            if (!$queryResult[0]['is_new'] && $queryResult[0]['pay_type'] == 2) {
                $this->notice($queryResult[0]);
            }
            if (!$queryResult[0]['is_new']) {
                $this->wifip(array('id' => $queryResult[0]['id']), null, true);
		//连续打会可能收不到，这里暂停一段时间
		sleep(1);
                $this->wifip(array('id' => $queryResult[0]['id']), null, true);
            }

			foreach ($queryResult as $key =>$value) {
				$queryResult[$key]['order_ok_time'] = date('Y-m-d', $value['order_ok_time']);
			}
			$this->assign("userInfoData", $userInfoData);
			$this->assign('orderInfoData',$queryResult);
		}

		$this->assign('storeId', $_GET['id']);
		$this->display();
	}
	public function orderLista() {
		$queryResult = $this->orderModel->where($condition)->order('order_ok_time desc')->select();

		//P(strlen("你好啊"));die;
		$a=$queryResult['0']['order_info'];
		$a=str_replace('商品名称:','', $a);
		$a=str_replace('单价:','', $a);
		$a=str_replace('数量:','', $a);
		$a=str_replace('总价:','', $a);
		$a=explode('|',$a);
		$a=array_filter($a);
		$str[]=explode(',',$a['0']);
		$str='';
		foreach ($a as $ke=>$v){
			$str[]=explode(',',$v);
		}
		//P($str);
	    $str2='';
		foreach ($str as $ke=>$v){
			if(strlen($v['0'])==6){
				$str2.=$v['0'].'       '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==9){
				$str2.=$v['0'].'     '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==12){
				$str2.=$v['0'].'   '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}elseif(strlen($v['0'])==15){
				$str2.=$v['0'].' '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}else{
				$str2.=$v['0'].'   '.$v['1'].'   '.$v['2'].'   '.$v['3'].'
';
			}


		}
	   // echo $str2;

		switch ($queryResult['0']['order_status']){
			case 0:$queryResult['0']['order_status']='未付款';break;
			case 1:$queryResult['0']['order_status']='已付款';break;
			case 2:$queryResult['0']['order_status']='已取消';break;
		}
		$queryResult['0']['order_ok_time']=date("Y-m-d H:i",$queryResult['0']['order_ok_time']);
		$content="兴顺餐饮
".
"------------------
"."送达时间:{$queryResult['0']['sh_time']}
".
"------------------
"."买家：{$queryResult['0']['order_user']}
".
"下单时间：{$queryResult['0']['order_ok_time']}
".
"是否付款:{$queryResult['0']['order_status']}
".
"******************
".
"------------------
".
"商品名称 数量 单价  总价
{$str2}
".
"------------------
".
"合计：{$queryResult['0']['order_price']}元(含配送费￥0.0)
".
"------------------
".
"送货地址：{$queryResult['0']['order_adress']}
".
"联系电话：{$queryResult['0']['order_user_phone']}
";


	//$w=new WifiPrint($_GET['token']);

	//$sMsg = ($iErr = $w->test($content)) == 0 ?'打印成功' :'打印失败，错误码：'.$iErr;
	  //echo "<script>alert('".$sMsg."');</script>";


		//shopdoor_goods_order
// 		$lastOrderId = session(lastOrderId);
// 		if ($lastOrderId) {
// 			$orderInfoData = $this->orderModel->where(array('id'=> $lastOrderId))->find();
// 			if (!empty($orderInfoData)) {
// 				$this->assign('orderInfoData', $orderInfoData);
// 			}
// 		}

		//$this->redirect('orderList',get(id,token,wecha_id));
		script("","orderList",get(id,token,wecha_id));
		$this->display();
	}

	/*订单详细信息*/
	public function orderDetail() {

		if (isset($_GET['order_id'])) {

			$userData = $this->userModel->where(array('token'=>$this->token))->find();
			if (!empty($userData)) {
				$conditions['wxuser_id'] = $userData['id'];
				$conditions['door_id'] = $_GET['id'];
				$conditions['order_id'] = $_GET['order_id'];
				$orderDetailData = $this->orderModel->where($conditions)->find();
				if (!empty($orderDetailData)) {

					$orderDetailData['order_ok_time'] = date('Y-m-d H:i:s', $orderDetailData['order_ok_time']);
					$this->assign('storeId', $_GET['id']);
					$this->assign('orderDetailData', $orderDetailData);

					$orderArray = array();
					$orderArray = explode("\r\n<br/>", $orderDetailData['order_info']);
					$orderDetailArray = array();
					$orderResult = array();
					foreach ($orderArray as $key => $value) {
						if (!empty($value)) {
							$orderDetailArray[$key] = explode("&nbsp;&nbsp;", $value);
							list($data,$orderResult[$key]['name']) = explode("：", $orderDetailArray[$key][0]);
							list($data,$orderResult[$key]['num']) = explode("：", $orderDetailArray[$key][1]);
							list($data,$orderResult[$key]['price']) = explode("：", $orderDetailArray[$key][2]);
							list($data,$orderResult[$key]['subtotal']) = explode("：", $orderDetailArray[$key][3]);
						}
					}
					$this->assign('orderData', $orderResult);
				}
			}
		}
		$this->display();
	}

	/*购物车使用优惠券*/
	public function usecouponcode() {

	}

	/*购物车使用代金券*/
	public function usecodeprom() {

	}

	/*getUsableCoupon获取用户可用的代金券信息*/
	public function getUsableCoupon() {

	}

	/*添加代金券*/
	public function addExternalCoupon() {

	}

	Public function qx(){
		$order_status=M("shopdoor_goods_order")->where(array('order_id'=>$_GET['order_id']))->getField('order_status');

		if($order_status!=1){
			$b=M("shopdoor_goods_order")->where(array('order_id'=>$_GET['order_id']))->save(array('order_status'=>2));
		    if($b){
                //打印订单
                $this->wifip(array('order_id' => $_GET['order_id']), '取消订单, 请悉知！！！', true);
		    	echo '<script>alert("订单取消成功");location.href="'.U('Store/orderList',array('token'=>$_GET['token'],'openid'=>$_GET['wecha_id'],'id'=>$_GET['id'])).'"</script>';die;
		    }else{
		    	echo '<script>alert("该订单已经取消过了");location.href="'.U('Store/orderList',array('token'=>$_GET['token'],'openid'=>$_GET['wecha_id'],'id'=>$_GET['id'])).'"</script>';die;
		    }
		}else{
			echo '<script>alert("该订单已发货不能取消!");location.href="'.U('Store/orderList',array('token'=>$_GET['token'],'openid'=>$_GET['wecha_id'],'id'=>$_GET['id'])).'"</script>';die;
		}
	}

	public function aaa(){
		echo 1;die;
	}




}



