<?php
/**
 * @Author: zhang
 * @Date:   2015-04-20 15:29:47
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-04-21 17:06:46
 */
class TourAction extends BaseAction{
    public $token;
    public $openid;
    public $wxuser;
    public $wxusers;

    public function _initialize(){
        parent::_initialize();

        $this->parentid = $_REQUEST['pid'] ? $_REQUEST['pid'] : 0;
        if ($this->parentid) {
            $this->tour_title = M('Product_cat_new')->where(array('id' => $this->parentid))->getField('name');
        }else{
            $this->tour_title = '出境特价游';
        }
        $this->wxuser = M('wxuser');
        $this->wxusers = M('wxusers');
        $this->assign('token',$this->token);
        $this->assign('tour_title',$this->tour_title);
        $this->assign('openid',$this->openid);
    }

    // 主页显示数据
    public function tourIndex(){
    	$catNewModel = M('Product_cat_new');
    	$productNewModel = M('Product_new');
        $first = $catNewModel->field('name,id')->where(array(
        	    'token'=>$this->token,
        	    'parentid'=>$this->parentid
        	    ))
                ->order('time desc')
                ->select();
        foreach($first as $key=>$value){
        	$first[$key]['country'] = $catNewModel->where(array(
        		'token'=>$this->token,
        		'parentid'=>$value['id']
        		))
        	->field('name,id')
        	->order('time desc')
        	->select();
        }

        // 首先查询第一条数据
        $firstData = $catNewModel->where(array(
        		  'token'=>$this->token,
        		  'parentid'=>$first[0]['id']
        		))
        	    ->field('id')
        	    ->order('time desc')
        	    ->find();

        $firstCatid = $productNewModel->where(array(
        	  'token'=>$this->token,
        	  'catid'=>$firstData['id']
        	))
            ->limit(8)
            ->select();

        $countFirstCatid = count($firstCatid);
        $this->assign('count',$countFirstCatid);
        $this->assign('show',$firstCatid);
        $this->assign('info',$first);
        $this->display();
    }

    // ajax切换数据
    public function reCity(){
    	if(IS_AJAX){
    		if(IS_POST){
                /*
		        $pid = $_POST['pid'];
			    $cid = $_POST['cid'];
    			$pid = !empty($pid) ? $pid : '';
    			$cid = !empty($cid) ? $cid : '';
                */
		        $pid = (isset($_POST['cid']) AND $_POST['cid']) ? $_POST['cid'] : $_POST['pid'];
    			$cid = !empty($pid) ? $pid : '';
    			$productNewModel = M('Product_new');
    			$getResult = $productNewModel->where(array(
    				'token'=>$this->token,
    				'catid'=>$cid
    				))
                ->order('id desc')
    			//->limit(8)
    			->select();
    			if(!$getResult){
    			    echo json_encode(array(
    			        'info' => '暂无数据！',
    			        'status' => 1
    			    ),true);
    			    exit();
    			}
                foreach($getResult as $key=>$v){

                    $getResult[$key]['name']=mb_substr($v['name'],0,14, 'utf-8');
                }
                //p($getResult);die;
    			$this->assign('info',$getResult);
    			$fetch = $this->fetch('./tpl/Wap/default/Tour_ajax.html');
    			echo json_encode(array(
                         'info' => $fetch,
                         'status' => 0
    				),true);
    			    exit();
    		}
    	}
    }

    // 无限加载
    public function load(){
    	if(IS_AJAX){
    		if(IS_POST){
		        $asize = $_POST['asize'];
			$cid = $_POST['cid'];
    			$asize = !empty($asize) ? $_POST['asize'] : '';
    			$cid = !empty($cid) ? $_POST['cid'] : '';
    			$productNewModel = M('Product_new');
    			$getResult = $productNewModel->where(array(
    				'token'=>$this->token,
    				'catid'=>$cid
    				))
    			->limit($asize.',6')
    			->select();
    			if(!$getResult){
    			    echo json_encode(array(
    			        'info' => '已显示全部',
    			        'status' => 1
    			    ),true);
    			    exit();
    			}
    			$this->assign('info',$getResult);
    			$fetch = $this->fetch('./tpl/Wap/default/Tour_ajax.html');
    			echo json_encode(array(
                         'info' => $fetch,
                         'status' => 0
    				),true);
    			    exit();
    		}
    	}
    }

    // 第二页显示
    public function second(){
    	$id = $this->_get('id','intval');
    	$productNewModel = M('Product_new');
    	$getResult = $productNewModel->where(array(
    		   'token'=>$this->token,
    		   'openid'=>$this->openid,
    		   'id'=>$id
    		))
    	    ->find();

        if($getResult['extend']){
            $getResult['extend']=json_decode($getResult['extend'],true);
            foreach ($getResult['extend'] as $k => $v) {
                $getResult['extend'][$k] = $aV = explode('|', $v);
                if (!isset($aV[1]) or !$aV[1]) {//没有就默认第一个
                    $getResult['extend'][$k][1] = $aV[0];
                }
            }
        }

    	$getResult['intro'] = htmlspecialchars_decode($getResult['intro']);
    	$data = json_decode($getResult['tourInfo'],true);
    	// print_r($data);exit;
    	$getResult['chufadi'] = $data['starting'];
    	$getResult['mudidi'] = $data['destination'];
    	$getResult['riqi'] = $data['fatDate'];
    	$getResult['showImg'] = M('Product_image_new')
					    	->field('image')
					    	->where(array('pid'=>$id))
					    	->select();
		$str = "";
		foreach($getResult['showImg'] as $key => $value){
			$str.="{content:\"<a href='#'><div class='imgSlider'><img src='".$value['image']."'/></div></a>\"},";
		}
		$this->assign('imgshow',$str);
		$this->assign('info',$getResult);
    	$this->display();
    }

    // 立即预定
    public function yuding(){
    	$id = $this->_get('id','intval');
        session('price',$_GET['price']);
        session('date',$_GET['date']);
    	$productNewModel = M('Product_new');
    	$getResult = $productNewModel->where(array(
    		   'token'=>$this->token,
    		   'openid'=>$this->openid,
    		   'id'=>$id
    		))
    	    ->find();
    	$data = json_decode($getResult['tourInfo'],true);
    	$getResult['chufadi'] = $data['starting'];
    	$getResult['mudidi'] = $data['destination'];
    	$getResult['riqi'] = $data['fatDate'];
    	$this->assign('info',$getResult);
    	$this->display();
    }

    // 立即支付
    public function alipay(){
    	if(IS_AJAX){

    		if(IS_POST){

    			$data['orderid'] = $this->getSn();
    			$data['wecha_id'] = $this->openid;
    			$data['time'] = time();
    			$data['total'] = 1;
    			$data['productid'] = $this->_post('productid');
    			// 表明为旅游类型
    			$data['ordertype'] = 3;
    			$data['token'] = $this->token;
                $row=array();
    			$row['selectedDates'] = $this->_post('selectedDates');
    			$row['adult'] = $this->_post('adult');
    			$row['child'] = $this->_post('child');
    			if($row['child'] == "请选择儿童人数"){
    				$row['child'] = 0;
    			}
                //这里个人资料
                $tour=explode(',',$_POST['tour']);
                $phone=explode(',',$_POST['phone']);
                $car_name=explode(',',$_POST['car_name']);
                $cardno=explode(',',$_POST['cardno']);
                for($i=0;$i<count($tour);$i++){
                    $row[$i]['tour']=$tour[$i];
                    $row[$i]['phone']=$phone[$i];
                    $row[$i]['car_name']=$car_name[$i];
                    $row[$i]['cardno']=$cardno[$i];
                }


                //把日期加进去
                $row['date']=session('date');
    			$rows = htmlspecialchars_decode(json_encode($row,true));
    			$data['usertravelinfo'] = $rows;
    			$travelprice = M('Product_new')->where(array(
    				'token'=>$this->token,
    				'id'=>$data['productid']
    				))
    			->field('price,vprice')
    			->find();
    		//	$data['price'] = $travelprice['vprice'];
                $data['price']=session('price');//从session里面取出价格
    			// echo "123";exit;
                //加手机号码
                $data['truename']=$tour[0];
                $data['tel']=$phone[0];
                $data['type']='par';
    			if(M('product_cart_new')->data($data)->add()){
    				$this->ajaxReturn(array('info'=>'订单已生成','status'=>0,'orderid'=>$data['orderid']),'JSON');
    			}else{
    				$this->ajaxReturn(array('info'=>'订单生成失败','status'=>1),'JSON');
    			}
    		}
    	}
    }

     // 获取唯一订单号
    protected function getSn(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
    // 显示订单
    public function showOrder(){
    	$info = M('product_cart_new')->field('id,productid,price,usertravelinfo,orderid')->where(array(
    		'token' => $this->token,
    		'openid' => $this->openid,
    		'ordertype' => 3,
    		'dining' => 0
    	))->select();
    	foreach ($info as $key => $value) {
    		$name = M('Product_new')->where(array(
    			'token'=>$this->token,
    			'id'=>$value['productid']
    			))
    		->field('name,logourl')
    		->find();
    		$info[$key]['title'] = $name['name'];
    		$info[$key]['logourl'] = $name['logourl'];
    		$thing = json_decode($value['usertravelinfo'],true);
    		$info[$key]['date'] = $thing['selectedDates'];
    	}
    	$this->assign('info',$info);
    	$this->display();
    }
    // 取消订单
    public function cancel(){
        if(IS_AJAX){
        	if(IS_POST){
        		$id = $this->_post('id','intval');
        		$change = M('product_cart_new')->where(array(
        			'token'=>$this->token,
        			'id'=>$id
        			))
        		->save(array(
        			'dining'=>-1
        			));
        		if($change){
        			$this->ajaxReturn(array('info'=>'订单取消成功','status'=>0),'JSON');
    			}else{
    				$this->ajaxReturn(array('info'=>'订单取消失败','status'=>1),'JSON');
    			}
        	}
        }
    }

}
