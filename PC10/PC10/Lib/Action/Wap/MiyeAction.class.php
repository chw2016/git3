<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class MiyeAction extends BaseAction{
	public $openid;
	public $token;
	public $wxuser;
	public $wxsuser;
	public $dianpu;
	public $order;
	public $pinzhong;
	public $api;
	public $logo;

	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		session('token',$_GET['token']);
		session('openid',$_GET['openid']);
		$this->openid = session('openid');
		$this->token = session('token');
		$this->wxuser = M('wxuser');
		$this->wxusers = M('wxusers');
		$this->dianpu = M('Miye_dianpu');
		$this->order = M('Miye_order');
		$this->pinzhong = M('Miye_pinzhong');
		$this->xiaoqu = M('Miye_xiaoqu');
		$this->logo = M('Miye_logo');
		$this->api = "ft9tCNRzY3LkR1z1hRAwyIC4";
		$logo = $this->logo->field('icon')->where(array('token'=>$this->token))->find();
        $headName = $logo['icon'];
		if(!$logo){
			$logo = $this->wxuser->field('headerpic')->where(array('token'=>$this->token))->find();
			$headName = $logo['headerpic'];
		}
		$this->assign('logo',$headName);
	}

	// 首页显示，店铺发送
	public function index(){
		
	 
		
		// 首先获取品种
		$pinzhong = $this->pinzhong->where(array('token' => $this->_get('token')))
		    ->field('pinzhong,id')
		    ->select();
		$this->assign('pinzhong',$pinzhong); 
		
		
		$xiaoqu = $this->xiaoqu->where(array('token' => $this->_get('token')))
		->field('xiaoqu,id')
		->select();
		$this->assign('xiaoqu',$xiaoqu);
		
		
		$wm = M('miye_wm')->where(array('token' => $this->_get('token')))
		->field('name,id')
		->select();
		$this->assign('wm',$wm);
		
		//P($wm);
		//如果$token存在这个数组里就让小区不显示
		$token=$this->_get('token');
		 $tokenaa=in_array($token,array('a'=>'233eb0b413256696f977e4c963645877'));
		if($tokenaa){
			$this->assign('tokenaa',1);
		} 
		$this->display();
	}

	// 根据接收数据的地址，返回数据
	public function localReturn(){
		if (IS_AJAX) {
			if (IS_POST) {
				// 获取到地址以及经纬度
				// $address = $this->_post('');
				$token = $this->_get('token');
				$lat = $this->_post('lat');
				$lng = $this->_post('lng');
				// 如果经纬度为空的情况下
				if(!$lng && !$lat){
                    $ip=get_client_ip();
                    $url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
                    $location=json_decode(file_get_contents($url),true);
                    $lng=floatval($location['content']['point']['x']);//起点x坐标
                    $lat=floatval($location['content']['point']['y']);//起点y坐标
                }
                $findAdd = $this->dianpu->where(array('token'=>$token))
                    ->field('id,name,longitude,latitude')
                    ->select();
                // echo $token;exit;    
                // print_r($filterAdd);exit;
                //获取到过滤后的数据
                $filterAdd = $this->getInfo(50000,$findAdd,$lng,$lat);

                $strsNameJson = "";
                foreach ($filterAdd as $key => $value) {
                	$dianpuName = array(
                	    'name' => $value['name'],
                	    'id' => $value['id']
                	);
                	$dianpuNameId[$key] = $dianpuName;
                }
                Log::write('miye_result'.print_r($dianpuNameId, true));
                $strsNameJson = json_encode($dianpuNameId,true);
                echo $strsNameJson;exit;
			}
		}
	}

	
	// 根据接收数据的地址，返回数据
	public function localReturn2(){
		if (IS_AJAX) {
			if (IS_POST) {
				$token = $this->_get('token');
				$lat = $this->_post('lat');
				$lng = $this->_post('lng');
				
				$findAdd = M('miye_xiaoqu')->field('id,xiaoqu,position_x,position_y')
				->select();
				
				// echo $token;exit;
				// print_r($filterAdd);exit;
				//获取到过滤后的数据
				$filterAdd = $this->getInfo2(50000,$findAdd,$lat,$lng);
				$str="";
			   foreach ($filterAdd as $v){
$str.=<<<str
             <li class="miList1" data-id="{$v['id']}" >{$v['xiaoqu']}</li>
str;
			   	
			   }
			   $str2="";
			   $a=0;
			   foreach ($filterAdd as $key =>$v){
			   	$a++;
			         if($a==1){
$str2.=<<<str
                 {$v['xiaoqu']}
str;
			   			 
			          }
			   }
				
				$res['str']=$str;
				$res['page']=$str2;
				$this->ajaxReturn($res);
				
				// 获取到地址以及经纬度
				// $address = $this->_post('');
				/*
				// 如果经纬度为空的情况下
				if(!$lng && !$lat){
					$ip=get_client_ip();
					$url="http://api.map.baidu.com/location/ip?ak=".$this->api."&ip=".$ip."&coor=bd09ll";
					$location=json_decode(file_get_contents($url),true);
					$lng=floatval($location['content']['point']['x']);//起点x坐标
					$lat=floatval($location['content']['point']['y']);//起点y坐标
				}
				$findAdd = $this->dianpu->where(array('token'=>$token))
				->field('id,name,longitude,latitude')
				->select();
				// echo $token;exit;
				// print_r($filterAdd);exit;
				//获取到过滤后的数据
				$filterAdd = $this->getInfo(50000,$findAdd,$lng,$lat);
	
				$strsNameJson = "";
				foreach ($filterAdd as $key => $value) {
					$dianpuName = array(
							'name' => $value['name'],
							'id' => $value['id']
					);
					$dianpuNameId[$key] = $dianpuName;
				}
				Log::write('miye_result'.print_r($dianpuNameId, true));
				$strsNameJson = json_encode($dianpuNameId,true);
				echo $strsNameJson;exit;*/
			}
		}
	}
	
	// 发送数据插入到数据库里面
	public function sends(){
		if (IS_AJAX) {
			if (IS_POST) {
				$pinzhongId = $this->_post('mi');
				$tel = $this->_post('phone');
				$storeId = $this->_post('shopId');
				$address = $this->_post('address');
                $sNotice = $this->_post('notice');
                $abc = $this->_post('abc');
				$desc = !empty($sNotice)?$sNotice:'';
				$addTime = date("Y-m-d H:i:m");
				$token = $this->_get('token');
				//$sOpenID = $this->_get('openid');
				//$openid = !empty($sOpenID) ? $sOpenID : '';
                $openid = $this->_post('openid');
				
				$orderCreate = $this->order->data(array(
					    'token' => $token,
					    'openid' => $openid,
					    'pinzhong_id' => $pinzhongId,
					    'mobile' => $tel,
					    'dianpu_id' => $storeId,
					    'desc' => $desc,
					    'status' => 0,
					    'add_time' => $addTime,
						'xiaoqu' => $abc
					))->add();
				$id = mysql_insert_id();
				if($orderCreate){
					echo json_encode(array(
						    'status'=>0,
						    'info'=>'下单成功!',
						    'url'=> U('Miye/orderSuccess', array(
						    	'token' => $token, 
						    	'openid' => $openid,
						    	'id' => $id
						    ))
						));
				}else{
					echo json_encode(array(
						    'status'=>1,
						    'info'=>'下单失败!'
						));
				}
				exit;
			}
		}
	}

	// 距离查询
	protected function getInfo($r,$data=array(),$long1 ,$lat1){
        $newdata = array();
        // $R = 6370996.81;
        foreach($data as $k => $v){
            $lat2=$v['latitude'];
            $long2=$v['longitude'];
            
            $distance = $this->getDis($long1,$lat1,$long2,$lat2);
            if($r >= $distance){
               $newdata[$v['id']] = $v;
               $newdata[$v['id']]['distance']=$distance;
            }else{
                unset($data[$k]);
            }      
        }
        ksort($newdata,SORT_NUMERIC);
        return $newdata;
    }
    
    
    // 距离查询
    protected function getInfo2($r,$data=array(),$long1 ,$lat1){
    	$newdata = array();
    	// $R = 6370996.81;
    	foreach($data as $k => $v){
    		$lat2=$v['position_x'];
    		$long2=$v['position_y'];
    
    		$distance = $this->getDis($long1,$lat1,$long2,$lat2);
    		if($r >= $distance){
    			$newdata[$v['id']] = $v;
    			$newdata[$v['id']]['distance']=$distance;
    		}else{
    			unset($data[$k]);
    		}
    	}
    	ksort($newdata,SORT_NUMERIC);
    	return $newdata;
    }

    // 计算轨迹
    protected function rad($d){  
        return number_format($d * 3.1415926535898 / 180.0,10,'.','');  
    }  
    // 根据经纬度计算距离
    protected function getDis($long1,$lat1,$long2,$lat2){
        /*
        $EARTH_RADIUS = 6378137;  
        $radLat1 = $this->rad($lat1);  
        $radLat2 = $this->rad($lat2);  
        $a = $radLat1 - $radLat2;  
        $b = $this->rad($lng1) - $this->rad($lng2);  
        $s = 2 * asin(sqrt(pow(sin($a/2),2) +  
        cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));  
        $s = $s *$EARTH_RADIUS;  
        $s = round($s * 10000) / 10000;  
        return $s;
         */
        $R = 6370996.81;
        return $R*acos(cos($lat1*pi()/180 )*cos($lat2*pi()/180)*cos($long1*pi()/180 -$long2*pi()/180)+ sin($lat1*pi()/180 )*sin($lat2*pi()/180));
        
    }

    // 测试json
    public function testJson(){
    	$data[1] = array('name'=>'zhangsan','age'=>12);
    	$data[2] = array('name'=>'zhangsan','age'=>12);
    	$data = json_encode($data,true);
    	echo $data;
    }

    // 下单成功后的页面
    public function orderSuccess(){
        $id = $this->_get('id','intval');
    	$openid=$this->order->where("id=".$id)->field("openid")->find();
 	  	$orderTime = $this->order->where(array(
    		    'id' => $id
    		))
    	    ->getField('add_time');
    	$orderTime = strtotime($orderTime);
    	$this->assign('times',$orderTime); 

    	httpMethod('http://v.wapwei.com/index.php?g=Home&m=Auth&a=sendTextMsg', array(
    			'token'=> $this->get('token'),
    			'openid'=> $openid['openid'],
    			'content'=> '恭喜你下单成功！'
    	));
    	$this->display();
    }

    // 取消订单
    public function cancelOrder(){
    	$id = $this->_get('id','intval');
    	$token = $this->_get('token');
    	$sOpenID = $this->_get('openid');
		$openid = !empty($sOpenID) ? $sOpenID : '';
    	$changeStatus = $this->order->where(array(
    		    'id' => $id
    		))
    	    ->save(array(
    	    	    'status' => 1
    	    	));
    	if($changeStatus){
            httpMethod('http://v.wapwei.com/index.php?g=Home&m=Auth&a=sendTextMsg', array(
                    'token'=> $token,
                    'openid'=> $openid,
                    'content'=> '亲，你的订单取消成功！'
            ));
    		echo json_encode(array(
    			    'status'=>0,
				    'info'=>'取消成功!',
				    'url'=> U('Miye/index', array(
				    	'token' => $token, 
				    	'openid' => $openid
				    ))
    			));
    	}else{
    		echo json_encode(array(
    			    'status'=>1,
				    'info'=>'取消失败!'
    			));
    	}
    }
}
?>
