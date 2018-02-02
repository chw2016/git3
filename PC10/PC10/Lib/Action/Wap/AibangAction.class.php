<?php
/**
 * @Author: zhang
 * @Date:   2015-04-08 08:52:48
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-04-25 15:29:30
 */
/**
 * @Author: zhang
 * @Date:   2015-01-31 16:14:14
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-11 15:00:36
 */
class AibangAction extends BaseAction{
	 public $token;
	 public $openid;
         public $api;
	 public function _initialize(){
	        parent::_initialize();
       
	        $this->api = "ft9tCNRzY3LkR1z1hRAwyIC4";

        /*
       * 引入微信js接口
       */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);
        //$this->openid = "oz5bXt13p7l1EqPvRLy85LM8IqQQ";
    }
    // 注册页面
    public function register(){
        if (isset($_GET['type'])) {
            // 如果存在type的话，首先接收openid
            $openid = $_GET['openid'];
            $regModel = M('Aibang_register');
            $result = $regModel->field('name,zone,channel,address,telphone,pic,desc')->where(array('openid'=>$openid) )->find();
            $this->assign('infos',$result);
	    //print_r($result);exit;
            $this->assign('flag',1);
        }
        $cateModel = M('Aibang_cate');
        $where['pid'] = 0;
        $info = $cateModel->where($where)->select();
        $this->assign('info',$info);
    	$this->display();
    }
    // 显示页面
    public function store(){
        //首先获取ID
        $id = $this->_get('id','intval');
        $openid = $_GET['openid'];
        $collModel = M('Aibang_collection');
        if ($collModel->where(array('uid'=>$id,'openid'=>$openid))->find()) {
            $this->assign('collect',1);
	   
        }
        if (isset($_GET['type'])) {
            $this->assign('flag',1);
        }
        $id = $this->_get('id','intval');
        $getResult = M('Aibang_register')->where(array('id'=>$id))->find();
        // 100个赞抵消一个差评
        if ($getResult['bad'] >= 1) {
            if ($getResult['good'] >= 100) {
                $getResult['good'] = $getResult['good'] - 100;
                $getResult['bad'] = $getResult['bad'] - 1;
                $data = array(
                        'good' => $getResult['good'],
                        'bad' => $getResult['bad']
                    );
                $infos = M('Aibang_register')->where(array('id'=>$id))->save($data);
                if ($infos) {
                    $getResult = M('Aibang_register')->where(array('id'=>$id))->find();
                }
            }
        }
        $channel = explode('>',$getResult['channel']);
        $getResult['channel'] = $channel[1];
        $getifbad = M('Aibang_userclick')->where(array('openid'=>$_GET['openid']))->getField('ifbad');
        $getTime = M('Aibang_userclick')->where(array('openid'=>$_GET['openid']))->getField('datetime');
        if ($getTime != date("Y-m-d")) {
            $data = array('ifbad'=>'no');
            $result = M('Aibang_userclick')->where(array('openid'=>$_GET['openid']))->save($data);
        }
        $currentime = date("Y-m-d");
        $this->assign('getifbad',$getifbad);
        $this->assign('infos',$getResult);
        $this->assign('getTime',$getTime);
        $this->assign('currentime',$currentime);
        $this->display();
    }
    // 注册页面请求数据
    public function regAcc(){
        if (IS_POST) {
            $city = $_POST['city'];
            $cityModel = M('Aibang_city');
            $zoneModel = M('Aibang_zone');
            $citys = $cityModel->where(array('city'=>$city))->find();
            $zones = $zoneModel->where(array('uid'=>$citys['id']))->select();
            if ($citys && $zones) {
                $str = "";
                foreach ($zones as $key => $value) {
                    $str = $str."<li>".$value['zone']."</li>";
                }
              
                $array = array(
                        'city' => $str
                    );
                $json = json_encode($array,true);
                echo $json;
            }else{
	    	$str = "";
		$str = $str."<li>暂无数据</li>";
                $array = array(
                        'city' => $str
                    );
		    
                $json = json_encode($array,true);
                echo $json;
            }
        }
    }
    // 频道页面的数据接收
    public function regsAcc(){
        if (IS_POST) {
            $cate = $_POST['cate'];
            $cate3 = $_POST['cate3'] ? $_POST['cate3'] : '';
	    
            $cateModel = M('Aibang_cate');
            $info = $cateModel->where(array('cate'=>$cate))->find();
            $infos = $cateModel->where(array('pid'=>$info['id']))->select();
            if ($cate3) {
	        
                $info3 = $cateModel->where(array('cate'=>$cate3))->find();
                $infos3 = $cateModel->where(array('pid'=>$info3['id']))->select();
                if ($infos3) {
                    $strs = "";
                    foreach ($infos3 as $key => $value) {
                        $strs = $strs."<li class='3_channel'>".$value['cate']."</li>";
                    }
		    
                    $array = array(
                            'cate3' => $strs
                        );
                    $json = json_encode($array,true);
                    echo $json;
                }else{
                    // 这里是没有数据的情况下返回的
		     $array = array(
                            'cate3' => 'kong'
                        );
		     $json = json_encode($array,true);
                     echo $json;		
                }
            }else{
                if ($infos) {
                    // 这里是有数据的情况下返回的数据
                    $str = "";
                    foreach ($infos as $key => $value) {
                        $str = $str."<li class='2_channel'>".$value['cate']."</li>";
                    }
                  
                    $array = array(
                            'catename' => $str
                        );
                    $json = json_encode($array,true);
                    echo $json;
                }else{
                    // 这里是没有数据的情况下返回的数据
		    $array = array(
                            'catename' => 'kong'
                        );
		     $json = json_encode($array,true);
                     echo $json;
                }
            }
        }
    }
    // 注册页面数据接收
    public function sign(){
        if (IS_POST) {
	    $getResult = M('Aibang_register')->where(array('openid'=>$_GET['openid']))->find();	
            if ($getResult) {
                $this->error("抱歉！您已注册过商圈",U(MODULE_NAME.'/register'));
            }else{
                // 频道的数据判断
                $cateModel = M('Aibang_cate');
                $channel = $_POST['channel'];
            $channel = htmlspecialchars_decode($channel);

                if (strpos($channel,'>')) {
                     $channelArr = explode('>',$channel);
                     $result = $cateModel->where(array('cate'=>$channelArr[0]))->find();

                }else{
                     $result = $cateModel->where(array('cate'=>$channel))->find();
                }
            
                $array = array(
                        'name' => $_POST['shm'],
                        'zone' => $_POST['zone'],
                        'channel' => htmlspecialchars_decode($_POST['channel']),
                        'address' => $_POST['address'],
			'desc' => $_POST['desc'],
                        'telphone' => $_POST['tel'],
                        'token' => $this->token,
                        'openid'=> $this->openid,
                        'status' => 0,
                        'reg_time' => date("Y-m-d"),
                        'uid' => $result['id'],
                        'good' => 0,
                        'bad' => 0,
                        'longitude' => $_POST['lng'],
                        'latitude' => $_POST['lat'],
                        'pic' => $_POST['pic'],
                        'ifseat' => '有座'
                    );

                if(M('Aibang_register')->data($array)->add()){
                   $this->success("提交成功！请等待审核。",U(MODULE_NAME.'/register',array('token'=>$this->token,'openid'=>$this->openid)));
                }else{
                    $this->error("提交失败！",U(MODULE_NAME.'/register'));
                }
            }
           
        }
    }
    // 主要的列表页面显示
    public function index(){
        $cateModel = M('Aibang_cate');
        $result = $cateModel->where(array('pid'=>0))->select();
        $this->assign('cate',$result);
        $this->assign('openid',$this->openid);
	//echo $this->openid;exit;
        $this->display();
    }
    // 列表页面的显示
    public function innerList(){
        // 指定搜索情况下
        $oShopRegModel = M('Aibang_register');

        $sSearch = !empty($_REQUEST['name']) ? $_REQUEST['name'] : '';
        $sType = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';
        $fLat = !empty($_REQUEST['lat']) ? $_REQUEST['lat'] : '';
        $fLng = !empty($_REQUEST['lng']) ? $_REQUEST['lng'] : '';
        $iKms = !empty($_REQUEST['kms']) ? $_REQUEST['kms'] : 5000;
        $iHaopin = !empty($_REQUEST['haopin']) ? $_REQUEST['haopin'] : '';
        // 购物类型
        // $buyType = !empty($_REQUEST['types']) ? $_REQUEST['types'] : '';
        if($sSearch){
           $where['name|address|channel|desc'] = array('like','%'.$sSearch.'%');  
        }

        if($sType){
           if($sType == 1){
              $sType = '消费';
           }elseif($sType == 2){
              $sType = '购物';
           }
           $where['channel'] = array('like','%'.$sType.'%'); 
        }


        $aShopData = $oShopRegModel->where($where)->select();

        $union=$this->getinfo($iKms,$aShopData,$fLng,$fLat);
        $total = count($union);
        // echo $total;
        //echo count($union);
        $newdata = array();
        if($iHaopin == 1){
            foreach ($union as $key => $value) {
                 $newdata[$value['good'].'_'.$key] = $value;
            }
            krsort($newdata,SORT_NUMERIC);
            $union =  $newdata;
        }
        $openid = $_REQUEST['openid'];
        $this->assign('sopenid',$openid);

        foreach($union as $key => $value){
            $val = explode('>',$value['channel']);
            $union[$key]['channel'] = $val[1];
        }
        /*$num = 0;
        $unions = array();
        foreach($union as $k => $v){
            $unions[$num] = $v;
            $num++;
        }
        $union = $unions;*/

        $times = !empty($_REQUEST['times']) ? $_REQUEST['times'] : 0;
        $countSSS = count($union);
        $union = array_slice($union,($times)*10,10);
        if(IS_AJAX){
            // 如果通过ajax请求的话,非点击加载
            if(count($union) == 0){
                echo $this->encode(array(
                    'info' => '没有更多数据',
                    'status' => 1,
                    'count' => $countSSS
                ));exit;
            }
            $this->assign('info',$union);
            $htmls = $this->fetch('./tpl/Wap/default/Aibang_innerList_page.html');
            // $count = count($union);
            echo $this->encode(array(
                    'html' => $htmls,
                    'count' => $countSSS,
                    'status' => 0
                ));
        }else{
            $this->assign('info',$union);
            $this->assign('countStore',$total);
            // print_r($union);exit;
            $this->assign('type',$sType);
            $this->assign('lat',$fLat);
            $this->assign('lng',$fLng);
            $this->assign('kms',$iKms);
            $this->assign('haopin',$iHaopin);
            $this->assign('search',$sSearch);
            // 分配类型
            // $this->assign('buyType',$buyType);
            $this->display();
        }
}



    // 图片上传
    public function uploadT(){
        import('ORG.Net.UploadFile');//导入上传类
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg' ,'png' ,'gif');// 设置附件上传类型
        $upload->savePath =  './upload/Aibang/';// 设置附件上传目录
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }
        if($upload->upload()){
            $info =  $upload->getUploadFileInfo();
            $imgpath=$info[0]['savepath'].$info[0]['savename'];
            $arr = array(
                    'name'=>$info[0]['savename'],
                    'pic'=>$imgpath,
                    'size'=>$size
            );
            $id = $this->_get('id','intval');
            $regModel = M('Aibang_register');
            if ($_GET['pic'] == 1) {
                $data = array('img_1'=>$imgpath);
                $result = $regModel->where(array('id'=>$id))->save($data);
            }elseif($_GET['pic'] == 2){
                $data = array('img_2'=>$imgpath);
                $result = $regModel->where(array('id'=>$id))->save($data);
            }elseif($_GET['pic'] == 3){
                $data = array('img_3'=>$imgpath);
                $result = $regModel->where(array('id'=>$id))->save($data);
            }elseif($_GET['pic'] == 4){
                $data = array('img_4'=>$imgpath);
                $result = $regModel->where(array('id'=>$id))->save($data);
            }elseif($_GET['pic'] == 5){
                $data = array('img_5'=>$imgpath);
                $result = $regModel->where(array('id'=>$id))->save($data);
            }elseif($_GET['pic'] == 6){
                $data = array('img_6'=>$imgpath);
                $result = $regModel->where(array('id'=>$id))->save($data);
            }elseif($_GET['pic'] == 7){
                $data = array('img_7'=>$imgpath);
                $result = $regModel->where(array('id'=>$id))->save($data);
            }elseif($_GET['pic'] == 8){
                $data = array('pic'=>$imgpath);
                $result = $regModel->where(array('id'=>$id))->save($data);
            }
            echo json_encode($arr);
        }else{
            $error = $this->error($upload->getErrorMsg());
        }
    } 
    // 点解列表页提交过来的数据
    public function Link(){
        $name = $_POST['name'];
        $openid = $_POST['openid'];
        $token = $_POST['token'];
        $regModel = M('Aibang_register');
        $result = $regModel->field('id,openid')->where(array('name'=>$name))->find();
        if ($openid == $result['openid']) {
             $data = array(
                'url' => 'index.php?g=Wap&m=Aibang&a=store&token='.$token.'&openid='.$this->openid.'&id='.$result['id'].'&type=same'
            );
        }else{
            $data = array(
                'url' => 'index.php?g=Wap&m=Aibang&a=store&token='.$token.'&openid='.$this->openid.'&id='.$result['id']
            );
        }
        
        $data = json_encode($data,true);
        echo $data;
    }
    // 修改数据
    public function fixed(){
        $id = $this->_get('id','intval');
        if (isset($_POST['storeName'])) {
            // 店铺名
            $data = array(
                    'name' => $_POST['name']
                );
            $result = M('Aibang_register')->where(array('id'=>$id))->save($data);
        }elseif (isset($_POST['seatName'])) {
            // 座位的数据
             $data = array(
                    'ifseat' => $_POST['seat']
                );
            $result = M('Aibang_register')->where(array('id'=>$id))->save($data);
        }elseif (isset($_POST['caiName'])) {
            // 插入频道页面数据
            $result = M('Aibang_register')->where(array('id'=>$id))->getField('channel');
            $result = explode(">", $result);
            $result[1] = $_POST['cai'];
            $result = implode(">",$result);
            $data = array(
                    'channel' => $result
                );
            $result = M('Aibang_register')->where(array('id'=>$id))->save($data);
        }elseif (isset($_POST['addre'])) {
            $data = array(
                    'address' => $_POST['address'],
                    'longitude' => $_POST['lng'],
                    'latitude' => $_POST['lat']
                );
            $result = M('Aibang_register')->where(array('id'=>$id))->save($data);
        }else{
            $data = array(
                    'telphone' => $_POST['telphone']
                );
            $result = M('Aibang_register')->where(array('id'=>$id))->save($data);
        }
    }
    // 中转数据
    public function Transit(){
        $id = $this->_get('id','intval');
        // 纬度
        $lng = $this->_post('lng');
        // 经度
        $lat = $this->_post('lat');
        // 地址
        $address = $this->_post('address');
        $data = array(
                'url' => 'index.php?g=Wap&m=Aibang&a=baidumap&token='.$this->_get('token').'&openid='.$this->_get('openid').'&id='.$id.'&lat='.$lat.'&lng='.$lng.'&address='.$address
            );
        $data = json_encode($data,true);
        echo $data;
    }
    // 进入到地图
    public function baidumap(){
        // 获取到经纬度以及地址
        $lat = $_GET['lat'];
        $lng = $_GET['lng'];
        $address = $_GET['address'];
        $data = array(
                'lat' => $lat,
                'lng' => $lng,
                'address' => $address
            );
        $this->assign('info',$data);
        $this->display();
    }
    // 点击好评以及差评
    public function comment(){
        $userModel = M('Aibang_userclick');
        $regModel = M('Aibang_register');
        $zanModel = M('Aibang_zanrecord');
        $id = $this->_get('id','intval');
        $token = $this->_get('token');
        $openid = $this->_get('openid');
        $type = $this->_post('type');
        
        // 如果是好评的话，一天五个
        $iZanCounts = $zanModel->where(array(
            'type'=>1,
            'token'=>$this->token,
            'openid'=>$this->openid,
            'date'=>date('Y-m-d')
            ))->count();

        $iBuZanCounts = $zanModel->where(array(
            'type'=>-1,
            'token'=>$this->token,
            'openid'=>$this->openid,
            'date'=>date('Y-m-d')
            ))->count();

        $iZanOnlyStoreCounts = $zanModel->where(array(
            'type'=>1,
            'token'=>$this->token,
            'openid'=>$this->openid,
            'date'=>date('Y-m-d'),
            'uid' => $id,
            ))->count();
        // 这里是点赞
        $isReadyshangjia = $regModel->where(array('id'=>$id,'token'=>$this->token))->find();
        if(!$isReadyshangjia || $isReadyshangjia['status'] != 1 ){
            echo $this->encode(array(
                'status'=>0,
                'info'=>'该商家没被审核不可以对它进行评价哦!'
            ));exit;
        }
	if($iBuZanCounts >=1){
	        echo $this->encode(array(
	        'status' => 0,
	        'info' => '今天已对商家点过差评了，不能使用差评了'
	        ));exit;   
	}
        if($type == 1){
            if($iZanOnlyStoreCounts >= 1){
                echo $this->encode(array(
                    'status'=>0,
                    'info'=>'今天已对商家点过赞了!'
                    ));exit;
            }
            if($iZanCounts >= 5){
                echo $this->encode(array(
                    'status' => 0,
                    'info' => '今天已对商家点过五次赞了'
                    ));exit;
            }else{
               if($iBuZanCounts >=1){
                  echo $this->encode(array(
                    'status' => 0,
                    'info' => '今天已对商家点过差评了哦'
                    ));exit;
               }else{
                   //插入点赞记录，更新次数
                   $info = $zanModel->data(array(
                    'token' => $token,
                    'openid' => $openid,
                    'uid' => $id,
                    'date' => date("Y-m-d"),
                    'add_time' => time(),
                    'type' => $type
                    ))->add();

                   $updateDianPu = $regModel->where(array(
                    'id' => $id
                    ))->setInc('good',1);

                   $goodTime = $regModel->where(array(
                    'id'=>$id
                    ))->getField('good'); 

                   echo $this->encode(array(
                    'status' => 1,
                    'info' => '点赞成功',
                    'good' => $goodTime
                    ));exit;
               }   
            }
        }else if($type == -1){
                // 这里是点差评

                if($iZanOnlyStoreCounts >= 1){
                        echo $this->encode(array(
                        'status' => 0,
                        'info' => '今天已对商家点过赞了，不能使用差评了'
                        ));exit;   
                }
		if($iZanCounts >= 1){
                    echo $this->encode(array(
                        'status' => 0,
                        'info' => '今天已对商家点过赞了，不能使用差评了'
                    ));exit;
                }
                if($iBuZanCounts >=1){
                    echo $this->encode(array(
                        'status' => 0,
                        'info' => '今天已对商家点过赞了，不能使用差评了'
                        ));exit;
                }else{
                    //插入点不赞记录，更新次数
                    $info = $zanModel->data(array(
                        'token' => $token,
                        'openid' => $openid,
                        'uid' => $id,
                        'date' => date("Y-m-d"),
                        'add_time' => time(),
                        'type' => $type
                        ))->add();
                    

                    $updateDianPu = $regModel->where(array(
                    'id' => $id
                    ))->setInc('bad',1);

                    $badTime = $regModel->where(array(
                    'id'=>$id
                    ))->getField('bad'); 

                    echo $this->encode(array(
                    'status' => 1,
                    'info' => '点差成功',
                    'bad' => $badTime
                    ));exit;
                }
            }
    }
    // 获取当前的周数
    protected function getWeekNow(){
        $datearr = getdate();
        $year = strtotime($datearr['year'].'-1-1');
        $startdate = getdate($year);
        $firstweekday = 7-$startdate['wday'];//获得第一周几天
        $yday = $datearr['yday']+1-$firstweekday;//今年的第几天
        return ceil($yday/7)+1;
    }
    
     //百度地图查询最近范围内的数据,$r为查询范围半径，data为数据,lat1为起点经度 long1为起点纬度
    public function getinfo($r,$data=array(),$lat1,$long1){
        $newdata = array();
        // $R = 6370996.81;
        foreach($data as $k=>$v){
            $lat2=$v['latitude'];
            $long2=$v['longitude'];
            
            $distance = $this->getDis($long1,$lat1,$lat2,$long2);
            if($r >= $distance){
               $newdata[$distance.'_'.$v['id']] = $v;
               $newdata[$distance.'_'.$v['id']]['distance']=$distance;
            }else{
                unset($data[$k]);
            }      
        }
        ksort($newdata,SORT_NUMERIC);
        return $newdata;
    }
    public function rad($d){  
        return number_format($d * 3.1415926535898 / 180.0,10,'.','');  
    }  
    // 根据经纬度计算距离
    public function getDis($lng1,$lat1,$lng2,$lat2){
        $EARTH_RADIUS = 6378137;  
        $radLat1 = $this->rad($lat1);  
        //echo $radLat1;  
        $radLat2 = $this->rad($lat2);  
        $a = $radLat1 - $radLat2;  
        $b = $this->rad($lng1) - $this->rad($lng2);  
        $s = 2 * asin(sqrt(pow(sin($a/2),2) +  
        cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));  
        $s = $s *$EARTH_RADIUS;  
        $s = round($s * 10000) / 10000;  
        return $s;
    }
    
    // 收藏夹显示
    public function collection(){
        
        $collModel = M('Aibang_collection');
        $regModel = M('Aibang_register');
        $result = $collModel->field('uid')->where(array('openid'=>$this->openid,'token'=>$this->_get('token')))->select();
        foreach ($result as $key => $value) {
            $regres = $regModel->field('name,channel,telphone,openid,token,pic')->where(array('id'=>$value['uid']))->find();
            $collect[$key]['name'] = $regres['name'];
            $channel = explode('>',$regres['channel']);
            $collect[$key]['channel'] = $channel[1];
            $collect[$key]['telphone'] = $regres['telphone'];
            $collect[$key]['id'] = $value['uid'];
            $collect[$key]['openid'] = $this->openid;
            $collect[$key]['token'] = $regres['token'];
            $collect[$key]['pic'] = $regres['pic'];
        }
        $this->assign('info',$collect);
        $this->display();
    }
   //收藏
   public function collect(){
        if (IS_POST) {
            $id = $this->_get('id','intval');
            $openid = $_GET['openid'];
            $token = $_GET['token'];
            $data = array(
                    'uid' => $id,
                    'openid' => $openid,
                    'token' => $token
                );
            if (M('Aibang_collection')->data($data)->add()) {
               $this->success("收藏成功！",U(MODULE_NAME.'/register'));
            }else{
                $this->error("收藏成功！",U(MODULE_NAME.'/register'));
            }
        }
   }
   // 取消收藏
   public function delcollect(){
        if (IS_POST) {
            $id = $this->_get('id','intval');
            $openid = $_GET['openid'];
            $token = $_GET['token'];
           
            if (M('Aibang_collection')->where(array('openid'=>$openid,'uid'=>$id))->delete()) {
               $this->success("取消收藏成功！",U(MODULE_NAME.'/register'));
            }else{
                $this->error("取消收藏失败！",U(MODULE_NAME.'/register'));
            }
        }
   }
   // 图片的滞空
   public function drop(){
        // 删除
        $regModel = M('Aibang_register');
        $id = $this->_get('id');
        if ($_GET['del'] == 1) {
            $data = array('img_1'=>'');
            $result = $regModel->where(array('id'=>$id))->save($data);
        }elseif($_GET['del'] == 2){
            $data = array('img_2'=>'');
            $result = $regModel->where(array('id'=>$id))->save($data);
        }elseif($_GET['del'] == 3){
            $data = array('img_3'=>'');
            $result = $regModel->where(array('id'=>$id))->save($data);
        }elseif($_GET['del'] == 4){
            $data = array('img_4'=>'');
            $result = $regModel->where(array('id'=>$id))->save($data);
        }elseif($_GET['del'] == 5){
            $data = array('img_5'=>'');
            $result = $regModel->where(array('id'=>$id))->save($data);
        }elseif($_GET['del'] == 6){
            $data = array('img_6'=>'');
            $result = $regModel->where(array('id'=>$id))->save($data);
        }elseif($_GET['del'] == 7){
            $data = array('img_7'=>'');
            $result = $regModel->where(array('id'=>$id))->save($data);
        }elseif($_GET['del'] == 8){
            $data = array('pic'=>'');
            $result = $regModel->where(array('id'=>$id))->save($data);
        }
   }
   // 图片文字的添加以及修改
   public function Fonts(){
        if(IS_POST){
            $id = $this->_get('id','intval');
            $regModel = M('Aibang_register');
            if($_POST['img1'] == 1){
                $data = array(
                        'font_1' => $_POST['font1']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);    
            }elseif ($_POST['img1'] == 2) {
                 $data = array(
                        'font_2' => $_POST['font2']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['img1'] == 3) {
                 $data = array(
                        'font_3' => $_POST['font3']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['img1'] == 4) {
                 $data = array(
                        'font_4' => $_POST['font4']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['img1'] == 5) {
                 $data = array(
                        'font_5' => $_POST['font5']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['img1'] == 6) {
                 $data = array(
                        'font_6' => $_POST['font6']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['img1'] == 7) {
                 $data = array(
                        'font_7' => $_POST['font7']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }
        }
   }
   // 图片初始化时添加文字
   public function addfonts(){
        if (IS_POST) {
            $regModel = M('Aibang_register');
            $id = $this->_get('id','intval');
            if ($_POST['addfont'] == 1) {
                if ($_POST['types'] == 1) {
                    
                    $data = array(
                        'font_1' => ''
                    );
                    $info = $regModel->where(array('id'=>$id))->save($data);
                }
                $data = array(
                        'font_1' => $_POST['font1']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['addfont'] == 2) {
                if ($_POST['types'] == 2) {
                    
                    $data = array(
                        'font_2' => ''
                    );
                    $info = $regModel->where(array('id'=>$id))->save($data);
                }
                $data = array(
                        'font_2' => $_POST['font2']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['addfont'] == 3) {
                if ($_POST['types'] == 3) {
                    
                    $data = array(
                        'font_3' => ''
                    );
                    $info = $regModel->where(array('id'=>$id))->save($data);
                }
                $data = array(
                        'font_3' => $_POST['font3']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['addfont'] == 4) {
                if ($_POST['types'] == 4) {
                    
                    $data = array(
                        'font_4' => ''
                    );
                    $info = $regModel->where(array('id'=>$id))->save($data);
                }
                $data = array(
                        'font_4' => $_POST['font4']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['addfont'] == 5) {
                if ($_POST['types'] == 5) {
                    
                    $data = array(
                        'font_5' => ''
                    );
                    $info = $regModel->where(array('id'=>$id))->save($data);
                }
                $data = array(
                        'font_5' => $_POST['font5']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['addfont'] == 6) {
                if ($_POST['types'] == 6) {
                    
                    $data = array(
                        'font_6' => ''
                    );
                    $info = $regModel->where(array('id'=>$id))->save($data);
                }
                $data = array(
                        'font_6' => $_POST['font6']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }elseif ($_POST['addfont'] == 7) {
                 if ($_POST['types'] == 7) {
                    
                    $data = array(
                        'font_7' => ''
                    );
                    $info = $regModel->where(array('id'=>$id))->save($data);
                }
                $data = array(
                        'font_7' => $_POST['font7']
                    );
                $info = $regModel->where(array('id'=>$id))->save($data);
            }
        }
   }

    public function my(){
        $store = M('Aibang_register')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        if($store){
            $this->redirect(U('Wap/Aibang/store',array('token'=>$this->token,'openid'=>$this->openid,'type'=>'name','id'=>$store['id'])));
        }else{
            $this->redirect(U('Wap/Aibang/register',array('token'=>$this->token,'openid'=>$this->openid)));
        }
    }

    public function dianzanlist(){
        $list = M('Aibang_zanrecord')->where(array('uid'=>$_GET['uid']))->order('add_time desc')->select();
        foreach($list as $key=>$val){
            $users = M('Wxusers')->where(array('uid'=>$this->tpl['id'],'openid'=>$val['openid']))->find();
            $list[$key]['nickname'] = $users['nickname'];
        }
        $this->assign('list',$list);
        $this->display();
    }
}
