<?php 
/**
 * @Author: 老张，前端控制器
 * @Date:   2015-01-07 14:29:07
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-01-29 16:12:25
 */
	class YanxiangAction extends BaseAction {
	    public $openid;
	    public $token;
	    //显示首页
	    public function _initialize(){
	        header("content-Type: text/html; charset=Utf-8");
	        parent::_initialize();

	        if ((!session('?token')) || (!session('?openid'))) {
	            session('token', $_REQUEST['token']);
	            session('openid', $_REQUEST['openid']);
	        }

	        $this->token = $_REQUEST['token'];
	        $this->openid = $_REQUEST['openid'];
	        //$this->openid = "oqem0ju8x0YBHpDV_Bnvop8lk2is";
	        $this->assign('token',$this->token);
	        $this->assign('openid',$this->openid);
	    }

	    // 研祥网店查询1
	    public function netSearch(){
	    	//$db = M('Yanxiang_province');
	    //	$province = $db->select();
            $fengqu=M('Yanxiang_fengqu')->where(array('token'=>$this->token))->select();
           //
            foreach($fengqu as $k=>$v){
                $fengqu[$k]['sheng']=M('Yanxiang_netposition')->join('tp_yanxiang_province as a on a.id=tp_yanxiang_netposition.uid')
                    ->field('a.id,a.province')->group('a.id')
                    ->where(array('tp_yanxiang_netposition.fid'=>$v['id']))->limit(9)->select();
          //      $fengqu[$k]['sheng']=M('')
            }
          //  p($fengqu);die;
	    	$this->assign('fengqu',$fengqu);
	    	$this->display();
	    }

	    // 研祥网点查询2
	    public function netSearchInner(){
	    	
	    	$db = M('yanxiang_netposition');
	    	$provinceId = $_GET['provinceId'];
	    	$getResult = $db->where(array('fid'=>$provinceId))->order('num desc')->select();
	    	$getProvince = M('Yanxiang_fengqu')->where(array('id'=>$provinceId))->find();
	    	// 根据接收的值进行分配
	    	$this->assign('result',$getResult);
	    	$this->assign('Province',$getProvince['name']);
	    	$this->display();
	    }
	     // 研祥网点查询百度地图
	    public function baidu_map(){
	    	// 接收百度地图携带的ID
	    	$id = $_GET['id'];
	    	$getResult = M('yanxiang_netposition')->where(array('id'=>$id))->find();
	    	$this->assign('result',$getResult);
	    	$this->display();
	    }
	    // 研祥保修
	    public function baoxiu(){
         //   echo 4;die;
	    	$this->display();
	    }
	    // 保修期页面显示
	    public function baoxiu2(){
	    	if (IS_POST) {
	    		// 获取二维码
	    		$code = $_POST['two-code'];
	    		//$getResult = M('Yanxiang_baoxiu')->where(array('two_code'=>$code))->find();
	    		//$this->assign('result',$getResult);
			$url = "http://www.evoc.cn:8012/Guarantee?sernr=".$code;
			$content = file_get_contents($url);
			
			//获取开始时间
			$beginTime = substr($content,15,10);
			$endTime = substr($content,44,10);
			//获取到开始时间以及结束时间
	    		// 检测是否在保修期内保修期的字段名称
	    		//$buy_date = explode('-',$getResult['buy_date']);
	    		//$baoxiu_date = $buy_date[0]+$getResult['baoxiu_date']."-".$buy_date[1]."-".$buy_date[2];
	    		// echo $baoxiu_date;exit();
			//分配保修时间结束日期
			$bxTime = explode('-',$endTime);
			//开始日期
			$bxTimes = explode('-',$beginTime);
			//差异
			
			$timeDiff = $bxTime[0] - $bxTimes[0];
			if($bxTimes[0] != "0000"){
				if($timeDiff == 0){
					$timeDiff = "1年";
				}else{
					$timeDiff = $timeDiff."年";	
				}	
			}else{
				$timeDiff = "未知";
			}
			$this->assign('timeData',$timeDiff);
			//分配购买日期
			if($beginTime == "0000-00-00"){
				$beginTime = "未知";
			}
			$this->assign('beginTime',$beginTime);
			//分配结束日期
			if($endTime == "0000-00-00"){
				$endTime = "未知";
			}
			$this->assign('endTime',$endTime);
	    		if ($beginTime == "未知") {
	    			$this->assign('term','抱歉！没有此产品');
	    		}elseif($endTime < date("Y-m-d")){
	    			$this->assign('term','您的产品已经过了保修期');
	    		}elseif($endTime >= date("Y-m-d")){
	    			$this->assign('term','您的产品仍在保修期');
	    		}
	    		$this->display();
	    	}
	    }
	    // 在线客服
	    public function kefu(){
	    	$this->display();
	    }
	    // 新品上市
	    public function newUp(){
	    	// 读取相应的数据
	    	$where = array('token'=> $this->_get('token'));
			//获取商城模版名称
			$wh=array('token'=>$this->token);
			$tpl=D('Wxuser')->where($wh)->find();

			
			if (isset($_POST['search'])) {
				$findCondition['catid'] = intval($_GET['catid']);
				$findCondition['name|des'] = array('like','%'.$_POST['search'].'%');

				$getResult = M('Product_new')->where($findCondition)->select();
				$this->assign('allPro',$getResult);
				$this->assign('flags',1);
				
			}else{
				// 获取相应的商品的分类ID
				$procatid = intval($_GET['catid']);
				// 根据商品ID获取商品
				$getResult = M('Product_new')->where(array('catid'=>$procatid))->limit('0,4')->select();
				$this->assign('allPro',$getResult);
			}
			$diffcatid = intval($_GET['catid']);
			$this->assign('cateid',$diffcatid);
			$this->weixinUser=$tpl;
			$this->tpl=$tpl;
		    //幻灯
	       
			$info = M('Flash')->where($where)->limit(5)->order('id desc')->select();
		

			$date = M('reply_info')->where($where)->find();
			// 产品文章
	        $articledata = M('Product_new_article')->where(array('token'=>$this->token))->limit(2)->select();
	        // 这里是幻灯片
	        $flashdata = M('Product_new_flash')->where(array('token'=>$this->token))->limit(5)->select();
	        // 获取的产品数据
	        // $product_new = M('Product_new')->where(array('catid'=>))->select();
	        $this->assign('product',$product_new);
	        //print_r($date);exit;
			$this->assign('info', $info);
			$this->assign('metaTitle', '商品分类');
			$this->assign('articledata', $articledata);
			$this->assign('flashdata', $flashdata);
			$this->assign('date', $date);

	        //访问历史记录
	        $d['token'] = $this->token;
	        $d['openid'] = $_GET['wecha_id'];
	        $d['dopenid'] = $_GET['dopenid'];
	        $wxusers = M('Wxusers')->field('nickname')->where(array('uid'=>$this->tpl['id'],'openid'=>$_GET['wecha_id']))->find();
	        if($wxusers['nickname']){
	            $d['nickname'] = $wxusers['nickname'];
	        }else{
	            $d['nickname'] = '游客';
	        }
       
	        if(get_client_ip()){
	            $d['ip'] = get_client_ip();
	        }else{
	            $d['ip'] = '未知';
	        }
	        $address = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$d['ip']);
	        $address = json_decode($address,true);
	        if($address['code'] == 0){
	            $d['address'] = $address['data']['country'].','.$address['data']['area'].','.$address['data']['region'].','.$address['data']['city'].','.$address['data']['county'].','.$address['data']['isp'];
	        }elseif($address['code'] == 1){
	            $d['address'] = '未知';
	        }
	        $d['time'] = time();
	        $d['url'] = __SELF__;
	        M('Product_new_visiter_data')->add($d);
			$this->display();
	    }
	    // 新品上市滚动条无限加载数据
	    public function newUpajax(){
	    	$catid = intval($_POST['catid']);
	    	$token = $_POST['token'];
	    	$num = $_GET['num'];
	    	$getResult = M('Product_new')->where(array('catid'=>$catid))->limit($num,2)->select();
	    	$getCount = count($getResult);
	    	if ($getCount == 1) {
	    		// 在如果只有一个的情况下
	    		$str1 = "";
	    		$str1 = $str1."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[0]['name']."</span><span class='shuoming'>".$getResult[0]['des']."</span></div><div class='prductImg'><img src='".$getResult[0]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[0]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[0]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[0]['id']."'>点击购买</a></div></div>";
	    		$RetData = array(
                    		'left' => $str1,
                    		'num' => $getCount
                    	);
                    $json = json_encode($RetData,true);
                    echo $json;
	    	}elseif($getCount == 2){
	    		$str1 = "";
	    		$str1 = $str1."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[0]['name']."</span><span class='shuoming'>".$getResult[0]['des']."</span></div><div class='prductImg'><img src='".$getResult[0]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[0]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[0]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[0]['id']."'>点击购买</a></div></div>";
                    $str2 = "";
                    $str2 = $str2."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[1]['name']."</span><span class='shuoming'>".$getResult[1]['des']."</span></div><div class='prductImg'><img src='".$getResult[1]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[1]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[1]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[1]['id']."'>点击购买</a></div></div>";
                    $RetData = array(
                    		'left' => $str1,
                    		'right' => $str2,
                    		'num' => $getCount
                    	);
                    $json = json_encode($RetData,true);
                    echo $json;
                   
	    	}else{
	    		$str1 = "没有更多数据";
	    		$RetData = array(
	    				'nomore' => $str1,
	    				'num' => $getCount
	    			);
	    		$json = json_encode($RetData,true);
	    		echo $json;
	    	}
	    }
	    // 产品通告页面方法
        //修改为热点应用
	    public function canno(){
	    	$db = M('Yanxiang_annoc');
	    	if (IS_POST) {
	    		$search = $_POST['search'];
	    		$where['title'] = array('like','%'.$search.'%');
	    		$where['explain'] = array('like','%'.$search.'%');
	    		$where['_logic'] = 'OR';
	    		$count = $db->where($where)->count();
				$page = new Page($count,6);
	    		$info=$db->order('id')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
	    		$this->assign('page',$page->show());
				$this->assign('info',$info);
	    	}else{
				$count = $db->count();
				$page = new Page($count,6);
				$info=$db->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
				$this->assign('page',$page->show());
				$this->assign('info',$info);
	    	}
	    	$this->display();
	    }
	    // 产品通告详细页面
	    public function cdetail(){
	    	$where['id'] = $_GET['id'];
	    	$getResult = M('Yanxiang_annoc')->where($where)->find();
	    	$this->assign('info',$getResult);
	    	$this->display();
	    }
	    // 祥哥动态
	    public function artlist(){
	    	$getResult = M('Yanxiang_status')->select();
	    	$this->assign('info',$getResult);
	    	$this->display();
	    }
	    // 祥哥动态内容详情页面
	    public function status(){
	    	$where['id'] = $_GET['id'];
	    	$getResult = M('Yanxiang_status')->where($where)->find();
	    	$this->assign('info',$getResult);
	    	// echo $where['id'];
	    	$this->display();
	    }
	    // 产品全库
	   public function microIndex(){
	   		$where['type'] = 'micro';
	   		$getResult = M('Yanxiang_imgscroll')->where($where)->select();
	   		$this->assign('info',$getResult);
	   		$this->display();
	   }
	   // 产品检索
	   public function cSearch(){
	   		if (IS_POST) {
	   			$search = $_POST['search'];
	   			$where['name'] = array('like','%'.$search.'%');
	    		$where['type'] = array('like','%'.$search.'%');
	    		$where['_logic'] = 'OR';
	    		$getResult = M('Yanxiang_csearch')->where($where)->select();
	    		$this->assign('info',$getResult);
	   		}elseif(isset($_GET['cid'])){
	   			$cid = $_GET['cid'];
	   			$where['uid'] = $cid;
	   			$info = M('Yanxiang_csearch')->where($where)->select();
	   			
	   			$this->assign('info',$info);
	   		}else{
	   			$getResult = M('Yanxiang_csearch')->order('id DESC')->select();
		    	$this->assign('info',$getResult);
	   		}
	   		$cate = M('Yanxiang_catename')->select();
	    	$this->assign('category',$cate);
	   		$this->display();
	   } 
	   //产品检索内容页面
	   public function psearch(){
	   		$id = $_GET['id'];
	   		$getResult = M('Yanxiang_csearch')->where(array('id'=>$id))->find();
	   		$this->assign('info',$getResult);
	   		$this->display();
	   }
	   // 新建专题活动
        public function active(){
            if (IS_POST) {
                $search = $_POST['search'];
                $where['title'] = array('like','%'.$search.'%');
               /* $where['type'] = array('like','%'.$search.'%');*/
                $where['_logic'] = 'OR';
                $getResult = M('Yanxiang_active')->where($where)->select();
                $this->assign('info',$getResult);
            }elseif(isset($_GET['cid'])){
                $cid = $_GET['cid'];
                $where['cid'] = $cid;
                $info = M('Yanxiang_active')->where($where)->select();

                $this->assign('info',$info);
            }else{
                $getResult = M('Yanxiang_active')->order('id DESC')->select();
                $this->assign('info',$getResult);
            }
            $cate = M('Yanxiang_activeclass')->select();
            $this->assign('category',$cate);
             
            $this->display();
        }
	  /* public function active(){
	   		if (IS_POST) {
	   			$search = $_POST['search'];
	   			$where['title'] = array('like','%'.$search.'%');
	    		$where['addtime'] = array('like','%'.$search.'%');
	    		$where['_logic'] = 'OR';
	    		$getResult = M('Yanxiang_active')->order('id DESC')->where($where)->select();
	    		$this->assign('info',$getResult);
	   		}else{
	   			$getResult = M('Yanxiang_active')->order('id DESC')->select();
		    	$this->assign('info',$getResult);
	   		}
	   		
	   		$this->display();
	   } */
	   // 专题活动内容
	   public function pactive(){
	   		$id = $_GET['id'];
	   		$getResult = M('Yanxiang_active')->where(array('id'=>$id))->find();
	   		$this->assign('info',$getResult);
	   		$this->display();
	   }
	   // 行业解决方案
	   public function industy(){
		   	if (IS_POST) {
		   			$search = $_POST['search'];
		   			$where['title'] = array('like','%'.$search.'%');
		    		$where['explain'] = array('like','%'.$search.'%');
		    		$where['_logic'] = 'OR';
		    		$getResult = M('Yanxiang_industy')->order('id DESC')->where($where)->select();
		    		$this->assign('info',$getResult);
		   		}else{
		   			$getResult = M('Yanxiang_industy')->order('id DESC')->select();
			    	$this->assign('info',$getResult);
		   		}
		   		$this->display();
	   }
	   // 行业解决方案内容
	   public function pindusty(){
	   		$id = $_GET['id'];
	   		$getResult = M('Yanxiang_industy')->where(array('id'=>$id))->find();
	   		$this->assign('info',$getResult);
	   		$this->display();
	   }
	   // 360全景
	   public function panorama(){
	   	header("Content-type: text/xml");
	   	if (isset($_GET['o'])){
			ob_clean();
	   		if($_GET['d'] == "yi"){
	   			echo '<?xml version="1.0" encoding="UTF-8"?><panorama id="" hideabout="1"><view fovmode="0" pannorth="0"><start pan="5.5" fov="80" tilt="1.5"/><min pan="0" fov="80" tilt="-90"/><max pan="360" fov="80" tilt="90"/></view><userdata title="" datetime="2013:05:23 21:01:02" description="" copyright="" tags="" author="" source="" comment="" info="" longitude="" latitude=""/><hotspots width="180" height="20" wordwrap="1"><label width="180" backgroundalpha="1" enabled="1" height="20" backgroundcolor="0xffffff" bordercolor="0x000000" border="1" textcolor="0x000000" background="1" borderalpha="1" borderradius="1" wordwrap="1" textalpha="1"/><polystyle mode="0" backgroundalpha="0.2509803921568627" backgroundcolor="0x0000ff" bordercolor="0x0000ff" borderalpha="1"/></hotspots><media/><input tilesize="700" tilescale="1.014285714285714" tile0url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/front.jpg" tile1url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/back.jpg" tile2url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/left.jpg" tile3url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/right.jpg" tile4url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/up.jpg" tile5url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/down.jpg"/><autorotate speed="0.200" nodedelay="0.00" startloaded="1" returntohorizon="0.000" delay="5.00"/><control simulatemass="1" lockedmouse="0" lockedkeyboard="0" dblclickfullscreen="0" invertwheel="0" lockedwheel="0" invertcontrol="1" speedwheel="1" sensitivity="8"/></panorama>';
	   		}elseif($_GET['d'] == "er"){
	   			echo '<?xml version="1.0" encoding="UTF-8"?><panorama id="" hideabout="1"><view fovmode="0" pannorth="0"><start pan="5.5" fov="80" tilt="1.5"/><min pan="0" fov="80" tilt="-90"/><max pan="360" fov="80" tilt="90"/></view><userdata title="" datetime="2013:05:23 21:01:02" description="" copyright="" tags="" author="" source="" comment="" info="" longitude="" latitude=""/><hotspots width="180" height="20" wordwrap="1"><label width="180" backgroundalpha="1" enabled="1" height="20" backgroundcolor="0xffffff" bordercolor="0x000000" border="1" textcolor="0x000000" background="1" borderalpha="1" borderradius="1" wordwrap="1" textalpha="1"/><polystyle mode="0" backgroundalpha="0.2509803921568627" backgroundcolor="0x0000ff" bordercolor="0x0000ff" borderalpha="1"/></hotspots><media/><input tilesize="700" tilescale="1.014285714285714" tile0url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item2/front.jpg" tile1url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item2/back.jpg" tile2url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item2/left.jpg" tile3url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item2/right.jpg" tile4url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item2/up.jpg" tile5url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item2/down.jpg"/><autorotate speed="0.200" nodedelay="0.00" startloaded="1" returntohorizon="0.000" delay="5.00"/><control simulatemass="1" lockedmouse="0" lockedkeyboard="0" dblclickfullscreen="0" invertwheel="0" lockedwheel="0" invertcontrol="1" speedwheel="1" sensitivity="8"/></panorama>';
	   		}elseif($_GET['d'] == "san"){
	   				echo '<?xml version="1.0" encoding="UTF-8"?><panorama id="" hideabout="1"><view fovmode="0" pannorth="0"><start pan="5.5" fov="80" tilt="1.5"/><min pan="0" fov="80" tilt="-90"/><max pan="360" fov="80" tilt="90"/></view><userdata title="" datetime="2013:05:23 21:01:02" description="" copyright="" tags="" author="" source="" comment="" info="" longitude="" latitude=""/><hotspots width="180" height="20" wordwrap="1"><label width="180" backgroundalpha="1" enabled="1" height="20" backgroundcolor="0xffffff" bordercolor="0x000000" border="1" textcolor="0x000000" background="1" borderalpha="1" borderradius="1" wordwrap="1" textalpha="1"/><polystyle mode="0" backgroundalpha="0.2509803921568627" backgroundcolor="0x0000ff" bordercolor="0x0000ff" borderalpha="1"/></hotspots><media/><input tilesize="700" tilescale="1.014285714285714" tile0url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item3/front.jpg" tile1url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item3/back.jpg" tile2url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item3/left.jpg" tile3url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item3/right.jpg" tile4url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item3/up.jpg" tile5url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item3/down.jpg"/><autorotate speed="0.200" nodedelay="0.00" startloaded="1" returntohorizon="0.000" delay="5.00"/><control simulatemass="1" lockedmouse="0" lockedkeyboard="0" dblclickfullscreen="0" invertwheel="0" lockedwheel="0" invertcontrol="1" speedwheel="1" sensitivity="8"/></panorama>';
	   		}elseif($_GET['d'] == "si"){
	   				echo '<?xml version="1.0" encoding="UTF-8"?><panorama id="" hideabout="1"><view fovmode="0" pannorth="0"><start pan="5.5" fov="80" tilt="1.5"/><min pan="0" fov="80" tilt="-90"/><max pan="360" fov="80" tilt="90"/></view><userdata title="" datetime="2013:05:23 21:01:02" description="" copyright="" tags="" author="" source="" comment="" info="" longitude="" latitude=""/><hotspots width="180" height="20" wordwrap="1"><label width="180" backgroundalpha="1" enabled="1" height="20" backgroundcolor="0xffffff" bordercolor="0x000000" border="1" textcolor="0x000000" background="1" borderalpha="1" borderradius="1" wordwrap="1" textalpha="1"/><polystyle mode="0" backgroundalpha="0.2509803921568627" backgroundcolor="0x0000ff" bordercolor="0x0000ff" borderalpha="1"/></hotspots><media/><input tilesize="700" tilescale="1.014285714285714" tile0url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item4/front.jpg" tile1url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item4/back.jpg" tile2url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item4/left.jpg" tile3url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item4/right.jpg" tile4url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item4/up.jpg" tile5url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item4/down.jpg"/><autorotate speed="0.200" nodedelay="0.00" startloaded="1" returntohorizon="0.000" delay="5.00"/><control simulatemass="1" lockedmouse="0" lockedkeyboard="0" dblclickfullscreen="0" invertwheel="0" lockedwheel="0" invertcontrol="1" speedwheel="1" sensitivity="8"/></panorama>';
	   		}elseif($_GET['d'] == "wu"){
	   				echo '<?xml version="1.0" encoding="UTF-8"?><panorama id="" hideabout="1"><view fovmode="0" pannorth="0"><start pan="5.5" fov="80" tilt="1.5"/><min pan="0" fov="80" tilt="-90"/><max pan="360" fov="80" tilt="90"/></view><userdata title="" datetime="2013:05:23 21:01:02" description="" copyright="" tags="" author="" source="" comment="" info="" longitude="" latitude=""/><hotspots width="180" height="20" wordwrap="1"><label width="180" backgroundalpha="1" enabled="1" height="20" backgroundcolor="0xffffff" bordercolor="0x000000" border="1" textcolor="0x000000" background="1" borderalpha="1" borderradius="1" wordwrap="1" textalpha="1"/><polystyle mode="0" backgroundalpha="0.2509803921568627" backgroundcolor="0x0000ff" bordercolor="0x0000ff" borderalpha="1"/></hotspots><media/><input tilesize="700" tilescale="1.014285714285714" tile0url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item5/front.jpg" tile1url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item5/right.jpg" tile2url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item5/back.jpg" tile3url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item5/left.jpg" tile4url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item5/up.jpg" tile5url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item5/down.jpg"/><autorotate speed="0.200" nodedelay="0.00" startloaded="1" returntohorizon="0.000" delay="5.00"/><control simulatemass="1" lockedmouse="0" lockedkeyboard="0" dblclickfullscreen="0" invertwheel="0" lockedwheel="0" invertcontrol="1" speedwheel="1" sensitivity="8"/></panorama>';
	   		}elseif($_GET['d'] == "liu"){
	   				echo '<?xml version="1.0" encoding="UTF-8"?><panorama id="" hideabout="1"><view fovmode="0" pannorth="0"><start pan="5.5" fov="80" tilt="1.5"/><min pan="0" fov="80" tilt="-90"/><max pan="360" fov="80" tilt="90"/></view><userdata title="" datetime="2013:05:23 21:01:02" description="" copyright="" tags="" author="" source="" comment="" info="" longitude="" latitude=""/><hotspots width="180" height="20" wordwrap="1"><label width="180" backgroundalpha="1" enabled="1" height="20" backgroundcolor="0xffffff" bordercolor="0x000000" border="1" textcolor="0x000000" background="1" borderalpha="1" borderradius="1" wordwrap="1" textalpha="1"/><polystyle mode="0" backgroundalpha="0.2509803921568627" backgroundcolor="0x0000ff" bordercolor="0x0000ff" borderalpha="1"/></hotspots><media/><input tilesize="700" tilescale="1.014285714285714" tile0url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item6/front.jpg" tile1url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item6/back.jpg" tile2url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item6/left.jpg" tile3url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item6/right.jpg" tile4url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item6/up.jpg" tile5url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item6/down.jpg"/><autorotate speed="0.200" nodedelay="0.00" startloaded="1" returntohorizon="0.000" delay="5.00"/><control simulatemass="1" lockedmouse="0" lockedkeyboard="0" dblclickfullscreen="0" invertwheel="0" lockedwheel="0" invertcontrol="1" speedwheel="1" sensitivity="8"/></panorama>';
	   		}
	   		else{
	   				echo '<?xml version="1.0" encoding="UTF-8"?><panorama id="" hideabout="1"><view fovmode="0" pannorth="0"><start pan="5.5" fov="80" tilt="1.5"/><min pan="0" fov="80" tilt="-90"/><max pan="360" fov="80" tilt="90"/></view><userdata title="" datetime="2013:05:23 21:01:02" description="" copyright="" tags="" author="" source="" comment="" info="" longitude="" latitude=""/><hotspots width="180" height="20" wordwrap="1"><label width="180" backgroundalpha="1" enabled="1" height="20" backgroundcolor="0xffffff" bordercolor="0x000000" border="1" textcolor="0x000000" background="1" borderalpha="1" borderradius="1" wordwrap="1" textalpha="1"/><polystyle mode="0" backgroundalpha="0.2509803921568627" backgroundcolor="0x0000ff" bordercolor="0x0000ff" borderalpha="1"/></hotspots><media/><input tilesize="700" tilescale="1.014285714285714" tile0url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/front.jpg" tile1url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/back.jpg" tile2url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/left.jpg" tile3url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/right.jpg" tile4url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/up.jpg" tile5url="'.C("site_url").'/tpl/static/wapweiui/Yanxiang/360/item1/down.jpg"/><autorotate speed="0.200" nodedelay="0.00" startloaded="1" returntohorizon="0.000" delay="5.00"/><control simulatemass="1" lockedmouse="0" lockedkeyboard="0" dblclickfullscreen="0" invertwheel="0" lockedwheel="0" invertcontrol="1" speedwheel="1" sensitivity="8"/></panorama>';
	   		}
			
			
		}else {
			$this->display();
		}
	   }
	   // 在线商城
	   public function online_store(){
	   		/*$where['type'] = 'online';
	   		$getResult = M('Yanxiang_imgscroll')->where($where)->select();
	   		$this->assign('info',$getResult);
	   		$this->display();
	   		*
	   		*/
	   	$this->product_cat_model = M('Product_cat_new');
	  // 	$Ycondition['name'] = array('in',array('特惠专区','热卖专区','新品上市','销售排行'));
		
	   	$Ycondition['token'] = $this->token;
	    $cats = $this->product_cat_model->where($Ycondition)->order('id asc')->select();
	  
       // print_r($cats);exit;

	   	$where = array('token'=> $this->_get('token'));
		//获取商城模版名称
		$wh=array('token'=>$this->token);
		$tpl=D('Wxuser')->where($wh)->find();

		//dump($tpl);exit;
		$this->weixinUser=$tpl;
		$this->tpl=$tpl;
	    //幻灯

		$date = M('reply_info')->where($where)->find();

        $flashdata = M('Product_new_flash')->where(array('token'=>$this->token))->limit(5)->select();
        //print_r($date);exit;


		$this->assign('metaTitle', '商品分类');


		$this->assign('flashdata', $flashdata);
		$this->assign('date', $date);
		$this->assign('cats', $cats);





        //访问历史记录
        $d['token'] = $this->token;
        $d['openid'] = $_GET['wecha_id'];
        $d['dopenid'] = $_GET['dopenid'];
        $wxusers = M('Wxusers')->field('nickname')->where(array('uid'=>$this->tpl['id'],'openid'=>$_GET['wecha_id']))->find();
        if($wxusers['nickname']){
            $d['nickname'] = $wxusers['nickname'];
        }else{
            $d['nickname'] = '游客';
        }
       
        if(get_client_ip()){
            $d['ip'] = get_client_ip();
        }else{
            $d['ip'] = '未知';
        }
        $address = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$d['ip']);
        $address = json_decode($address,true);
        if($address['code'] == 0){
            $d['address'] = $address['data']['country'].','.$address['data']['area'].','.$address['data']['region'].','.$address['data']['city'].','.$address['data']['county'].','.$address['data']['isp'];
        }elseif($address['code'] == 1){
            $d['address'] = '未知';
        }
        $d['time'] = time();
        $d['url'] = __SELF__;
        M('Product_new_visiter_data')->add($d);
        // 在这里获取轮播图
        $imgModel = M('Yanxiang_imgscroll');
        $imgResult = $imgModel->where(array('type'=>'online'))->limit(5)->select();
        $this->assign('imgInfo',$imgResult);
           //新增下面三个框
        //3张图片
        $tb=M('Yanxiang_tb')->select();   
        $this->assign('tb',$tb);
       
		$this->display();
	}
	// 销售排行
	public function saleRank(){
		$cartNewModel = M('product_cart_new');
		// 商品名称表
		$productNewModel = M('product_new');
		// 获取排行榜
		// 根据token来获取数据
		// $this->token
		$orderArray = array();
		$where['token'] = $this->token;
		$getResult = $cartNewModel->where($where)->group('productid')->select();
		foreach($getResult as $key => $value){
			$result = $cartNewModel->where(array('productid'=>$value['productid']))->count();
			$orderArray[$key]['productid'] = $value['productid'];
			$orderArray[$key]['num'] = $result;
		}
		foreach ($orderArray as $key => $value) {
			$num[] = $orderArray[$key]['num'];
		}
		arsort($num);
		// 双层循环
		foreach ($num as $key => $value) {
			foreach ($orderArray as $k => $v) {
				if($value == $v['num']){
					$info[] = $orderArray[$key];
				}
			}
		}
		foreach ($info as $key => $value) {
			$getName = $productNewModel->where(array('id'=>$info[$key]['productid']))->find();
			$info[$key]['name'] = $getName['name'];
			$info[$key]['price'] = $getName['vprice'];
			$info[$key]['xing'] = $getName['des'];
			$info[$key]['img'] = $getName['logourl'];
            $info[$key]['productid'] = $getName['id'];

		}
		
		$this->assign('info',$info);
	
		// 二维数组从高到底排序
		$this->display();
			
	}
	//特惠专区
	public function Preferential(){
			// 读取相应的数据
	    	$where = array('token'=> $this->_get('token'));
			//获取商城模版名称
			$wh=array('token'=>$this->token);
			$tpl=D('Wxuser')->where($wh)->find();

			// 获取相应的商品的分类ID
			// $procatid = intval($_GET['catid']);
			if (isset($_POST['search'])) {
				$findCondition['catid'] = intval($_GET['catid']);
				$findCondition['name|des'] = array('like','%'.$_POST['search'].'%');
				$getResult = M('Product_new')->where($findCondition)->select();
				$this->assign('allPro',$getResult);
				$this->assign('flags',1);
				
			}else{
				// 获取相应的商品的分类ID
				$procatid = intval($_GET['catid']);
				// 根据商品ID获取商品
				$getResult = M('Product_new')->where(array('catid'=>$procatid))->limit('0,4')->select();
				$this->assign('allPro',$getResult);
			}
			$diffcatid = intval($_GET['catid']);
			$this->assign('cateid',$diffcatid);
			$this->weixinUser=$tpl;
			$this->tpl=$tpl;
		    //幻灯
	       
			$info = M('Flash')->where($where)->limit(5)->order('id desc')->select();
		

			$date = M('reply_info')->where($where)->find();
			// 产品文章
	        $articledata = M('Product_new_article')->where(array('token'=>$this->token))->limit(2)->select();
	        // 这里是幻灯片
	        $flashdata = M('Product_new_flash')->where(array('token'=>$this->token))->limit(5)->select();
	        // 获取的产品数据
	        // $product_new = M('Product_new')->where(array('catid'=>))->select();
	        $this->assign('product',$product_new);
	        //print_r($date);exit;
			$this->assign('info', $info);
			$this->assign('metaTitle', '商品分类');
			$this->assign('articledata', $articledata);
			$this->assign('flashdata', $flashdata);
			$this->assign('date', $date);

	        //访问历史记录
	        $d['token'] = $this->token;
	        $d['openid'] = $_GET['wecha_id'];
	        $d['dopenid'] = $_GET['dopenid'];
	        $wxusers = M('Wxusers')->field('nickname')->where(array('uid'=>$this->tpl['id'],'openid'=>$_GET['wecha_id']))->find();
	        if($wxusers['nickname']){
	            $d['nickname'] = $wxusers['nickname'];
	        }else{
	            $d['nickname'] = '游客';
	        }
       
	        if(get_client_ip()){
	            $d['ip'] = get_client_ip();
	        }else{
	            $d['ip'] = '未知';
	        }
	        $address = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$d['ip']);
	        $address = json_decode($address,true);
	        if($address['code'] == 0){
	            $d['address'] = $address['data']['country'].','.$address['data']['area'].','.$address['data']['region'].','.$address['data']['city'].','.$address['data']['county'].','.$address['data']['isp'];
	        }elseif($address['code'] == 1){
	            $d['address'] = '未知';
	        }
	        $d['time'] = time();
	        $d['url'] = __SELF__;
	        M('Product_new_visiter_data')->add($d);
			$this->display();
	}
	 // 特惠专区滚动条无限加载数据
	    public function Prefeajax(){
	    	$catid = intval($_POST['catid']);
	    	$token = $_POST['token'];
	    	$num = $_GET['num'];
	    	$getResult = M('Product_new')->where(array('catid'=>$catid))->limit($num,2)->select();
	    	$getCount = count($getResult);
	    	if ($getCount == 1) {
	    		// 在如果只有一个的情况下
	    		$str1 = "";
	    		$str1 = $str1."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[0]['name']."</span><span class='shuoming'>".$getResult[0]['des']."</span></div><div class='prductImg'><img src='".$getResult[0]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[0]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[0]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[0]['id']."'>点击购买</a></div></div>";
	    		$RetData = array(
                    		'left' => $str1,
                    		'num' => $getCount
                    	);
                    $json = json_encode($RetData,true);
                    echo $json;
	    	}elseif($getCount == 2){
	    		$str1 = "";
	    		$str1 = $str1."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[0]['name']."</span><span class='shuoming'>".$getResult[0]['des']."</span></div><div class='prductImg'><img src='".$getResult[0]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[0]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[0]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[0]['id']."'>点击购买</a></div></div>";
                    $str2 = "";
                    $str2 = $str2."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[1]['name']."</span><span class='shuoming'>".$getResult[1]['des']."</span></div><div class='prductImg'><img src='".$getResult[1]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[1]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[1]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[1]['id']."'>点击购买</a></div></div>";
                    $RetData = array(
                    		'left' => $str1,
                    		'right' => $str2,
                    		'num' => $getCount
                    	);
                    $json = json_encode($RetData,true);
                    echo $json;
                   
	    	}else{
	    		$str1 = "没有更多数据";
	    		$RetData = array(
	    				'nomore' => $str1,
	    				'num' => $getCount
	    			);
	    		$json = json_encode($RetData,true);
	    		echo $json;
	    	}
	    }
	//热卖专区
	public function hotSale(){
		// 读取相应的数据
	    	$where = array('token'=> $this->_get('token'));
			//获取商城模版名称
			$wh=array('token'=>$this->token);
			$tpl=D('Wxuser')->where($wh)->find();

			// 获取相应的商品的分类ID
			// $procatid = intval($_GET['catid']);
			if (isset($_POST['search'])) {
				$findCondition['catid'] = intval($_GET['catid']);
				$findCondition['name|des'] = array('like','%'.$_POST['search'].'%');

				$getResult = M('Product_new')->where($findCondition)->select();
				$this->assign('allPro',$getResult);
				$this->assign('flags',1);
				
			}else{
				// 获取相应的商品的分类ID
				$procatid = intval($_GET['catid']);
				// 根据商品ID获取商品
				$getResult = M('Product_new')->where(array('catid'=>$procatid))->limit('0,4')->select();
				$this->assign('allPro',$getResult);
			}
			$diffcatid = intval($_GET['catid']);
			$this->assign('cateid',$diffcatid);
			$this->weixinUser=$tpl;
			$this->tpl=$tpl;
		    //幻灯
	       
			$info = M('Flash')->where($where)->limit(5)->order('id desc')->select();
		

			$date = M('reply_info')->where($where)->find();
			// 产品文章
	        $articledata = M('Product_new_article')->where(array('token'=>$this->token))->limit(2)->select();
	        // 这里是幻灯片
	        $flashdata = M('Product_new_flash')->where(array('token'=>$this->token))->limit(5)->select();
	        // 获取的产品数据
	        // $product_new = M('Product_new')->where(array('catid'=>))->select();
	        $this->assign('product',$product_new);
	        //print_r($date);exit;
			$this->assign('info', $info);
			$this->assign('metaTitle', '商品分类');
			$this->assign('articledata', $articledata);
			$this->assign('flashdata', $flashdata);
			$this->assign('date', $date);

	        //访问历史记录
	        $d['token'] = $this->token;
	        $d['openid'] = $_GET['wecha_id'];
	        $d['dopenid'] = $_GET['dopenid'];
	        $wxusers = M('Wxusers')->field('nickname')->where(array('uid'=>$this->tpl['id'],'openid'=>$_GET['wecha_id']))->find();
	        if($wxusers['nickname']){
	            $d['nickname'] = $wxusers['nickname'];
	        }else{
	            $d['nickname'] = '游客';
	        }
       
	        if(get_client_ip()){
	            $d['ip'] = get_client_ip();
	        }else{
	            $d['ip'] = '未知';
	        }
	        $address = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$d['ip']);
	        $address = json_decode($address,true);
	        if($address['code'] == 0){
	            $d['address'] = $address['data']['country'].','.$address['data']['area'].','.$address['data']['region'].','.$address['data']['city'].','.$address['data']['county'].','.$address['data']['isp'];
	        }elseif($address['code'] == 1){
	            $d['address'] = '未知';
	        }
	        $d['time'] = time();
	        $d['url'] = __SELF__;
	        M('Product_new_visiter_data')->add($d);
			$this->display();
	}
	// 热卖专区滚动条无限加载数据
	    public function hotajax(){
	    	$catid = intval($_POST['catid']);
	    	$token = $_POST['token'];
	    	$num = $_GET['num'];
	    	$getResult = M('Product_new')->where(array('catid'=>$catid))->limit($num,2)->select();
	    	$getCount = count($getResult);
	    	if ($getCount == 1) {
	    		// 在如果只有一个的情况下
	    		$str1 = "";
	    		$str1 = $str1."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[0]['name']."</span><span class='shuoming'>".$getResult[0]['des']."</span></div><div class='prductImg'><img src='".$getResult[0]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[0]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[0]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[0]['id']."'>点击购买</a></div></div>";
	    		$RetData = array(
                    		'left' => $str1,
                    		'num' => $getCount
                    	);
                    $json = json_encode($RetData,true);
                    echo $json;
	    	}elseif($getCount == 2){
	    		$str1 = "";
	    		$str1 = $str1."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[0]['name']."</span><span class='shuoming'>".$getResult[0]['des']."</span></div><div class='prductImg'><img src='".$getResult[0]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[0]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[0]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[0]['id']."'>点击购买</a></div></div>";
                    $str2 = "";
                    $str2 = $str2."<div class='pubu'><div class='top'><span class='xinghao'>".$getResult[1]['name']."</span><span class='shuoming'>".$getResult[1]['des']."</span></div><div class='prductImg'><img src='".$getResult[1]['logourl']."' /></div><div class='prize'><p class='huoqi'>货期：现货</p><p class='shichang'>市场价：<span class='shichangprize' style='display:inline;text-decoration: line-through;'> ".$getResult[1]['price']."</span></p><p class='youhui'>优惠价：<span class='youhuiprize' style='display:inline;color:#f00;'>".$getResult[1]['vprice']."</span></p></div><div class='buys'><a style='display:block;color:#3B9ED7;' href='/index.php?g=Wap&m=Store_new&a=product&token=".$token."&id=".$getResult[1]['id']."'>点击购买</a></div></div>";
                    $RetData = array(
                    		'left' => $str1,
                    		'right' => $str2,
                    		'num' => $getCount
                    	);
                    $json = json_encode($RetData,true);
                    echo $json;
                   
	    	}else{
	    		$str1 = "没有更多数据";
	    		$RetData = array(
	    				'nomore' => $str1,
	    				'num' => $getCount
	    			);
	    		$json = json_encode($RetData,true);
	    		echo $json;
	    	}
	    }
	// 主搜索框搜索结果
	public function sResult(){
		// 读取相应的数据
	    	$where = array('token'=> $this->_get('token'));
			//获取商城模版名称
			$wh=array('token'=>$this->token);
			$tpl=D('Wxuser')->where($wh)->find();

			// 获取相应的商品的分类ID
			// $procatid = intval($_GET['catid']);
			$this->product_cat_model = M('Product_cat_new');
			$Ycondition['name'] = array('in',array('特惠专区','热卖专区','新品上市','销售排行'));
	   		$allCats = $this->product_cat_model->where($Ycondition)->select();
	   		$totalid = array();
	   		foreach ($allCats as $key => $value) {
	   			$totalid[$key] = $value['id'];
	   		}
			if (isset($_POST['search'])) {
				
	   			
	   			$findCondition['catid'] = array('in',$totalid);
				$findCondition['name|des'] = array('like','%'.$_POST['search'].'%');

				$getResult = M('Product_new')->where($findCondition)->select();
				$this->assign('allPro',$getResult);
				
				
			}else{
				
				// 根据商品ID获取商品
				$getResult = M('Product_new')->where(array('catid'=>$procatid))->select();
				$this->assign('allPro',$getResult);
				echo "123";
			}
			$this->weixinUser=$tpl;
			$this->tpl=$tpl;
		    //幻灯
	       
			$info = M('Flash')->where($where)->limit(5)->order('id desc')->select();
		

			$date = M('reply_info')->where($where)->find();
			// 产品文章
	        $articledata = M('Product_new_article')->where(array('token'=>$this->token))->limit(2)->select();
	        // 这里是幻灯片
	        $flashdata = M('Product_new_flash')->where(array('token'=>$this->token))->limit(5)->select();
	        // 获取的产品数据
	        // $product_new = M('Product_new')->where(array('catid'=>))->select();
	        $this->assign('product',$product_new);
	        //print_r($date);exit;
			$this->assign('info', $info);
			$this->assign('metaTitle', '商品分类');
			$this->assign('articledata', $articledata);
			$this->assign('flashdata', $flashdata);
			$this->assign('date', $date);

	        //访问历史记录
	        $d['token'] = $this->token;
	        $d['openid'] = $_GET['wecha_id'];
	        $d['dopenid'] = $_GET['dopenid'];
	        $wxusers = M('Wxusers')->field('nickname')->where(array('uid'=>$this->tpl['id'],'openid'=>$_GET['wecha_id']))->find();
	        if($wxusers['nickname']){
	            $d['nickname'] = $wxusers['nickname'];
	        }else{
	            $d['nickname'] = '游客';
	        }
       
	        if(get_client_ip()){
	            $d['ip'] = get_client_ip();
	        }else{
	            $d['ip'] = '未知';
	        }
	        $address = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=".$d['ip']);
	        $address = json_decode($address,true);
	        if($address['code'] == 0){
	            $d['address'] = $address['data']['country'].','.$address['data']['area'].','.$address['data']['region'].','.$address['data']['city'].','.$address['data']['county'].','.$address['data']['isp'];
	        }elseif($address['code'] == 1){
	            $d['address'] = '未知';
	        }
	        $d['time'] = time();
	        $d['url'] = __SELF__;
	        M('Product_new_visiter_data')->add($d);
			$this->display();
	}

	// 微活动列表
	public function microlist(){
		// 取得数据，先取得第一张表的数据
		$getResult = M('Yanxiang_microimg')->find();
		$getResults = M('Yanxiang_microinner')->where(array('uid'=>$getResult['id']))->select();
		$this->assign('res',$getResult);
      //  p($getResults);
		$this->assign('result',$getResults);
		$this->display();
	}
	// 最新产品
	public function newest(){
		$getResult = M('Yanxiang_csearch')->order('addtime desc')->select();
		$this->assign('info',$getResult);
		$this->display();
	}
}
?>