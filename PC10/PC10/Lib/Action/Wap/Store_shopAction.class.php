<?php

/**
 * Class Store_shopAction 万普商城
 *
 * 李铭
 */
class Store_shopAction extends BaseAction{
	public $token;
	public $wecha_id = 'gh_aab60b4c5a39';
	public $product_model;
	public $product_cat_model;
	public $session_cart_name;
    public $dopenid;
    public $bNew=false;

	public function _initialize() {

		parent::_initialize();
        if (in_array($_GET['token'], array('233eb0b413256696f977e4c963645877'))) {
            $this->bNew = true;
        }
        //大众招聘网去掉底部
        if (in_array($_GET['token'], array('079029b2a38dbde9963ab9f4cd5fe721'))) {
            $this->hideBottom = true;
        }
        //echo '321';exit;
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if (!strpos($agent, "MicroMessenger")) {
			//	echo '此功能只能在微信浏览器中使用';exit;
		}
		$this->token = isset($_REQUEST['token']) ? htmlspecialchars($_REQUEST['token']) : session('token');
		$this->session_cart_name = 'session_cart_products_' . $this->token;
		$this->assign('token', $this->token);
		$this->wecha_id	= isset($_REQUEST['wecha_id']) ? htmlspecialchars($_REQUEST['wecha_id']) : '';
		$this->openid	= isset($_REQUEST['openid']) ? htmlspecialchars($_REQUEST['openid']) : '';
		if(!$this->openid){
			$this->openid	= isset($_REQUEST['wecha_id']) ? htmlspecialchars($_REQUEST['wecha_id']) : '';
		}
        #页面上传
        $this->dopenid = M('Media_users')->where(array(
            'openid'        => $this->openid
        ))->getField('from_openid');
        #还有一种情况就是已经有关系 ，但是不是点击的分销链接,这个时候dopenid在关系表里
        if (!$this->dopenid) {
            $this->dopenid	= isset($_REQUEST['dopenid']) ? htmlspecialchars($_REQUEST['dopenid']) : '';
        }

        //普金的隐藏logo
        $bHideBottomLogo = false;
        if ($_GET['token'] == '5e06d4d7477b35508c07d8ec81250286') {
            $bHideBottomLogo = true;
        }


		$this->wecha_id = $this->openid;
		$this->assign('wecha_id', $this->wecha_id);
		$this->assign('openid', $this->openid);
		$this->product_model = M('Product_new');
		$this->product_cat_model = M('Product_cat_new');
        //print_r($this->product_cat_model);exit;
		$this->assign('staticFilePath', str_replace('./','/',THEME_PATH.'common/css/store'));
		//购物车

		$calCartInfo = $this->calCartInfo();
		$where['token']=$this->token;
		$kefu=M('Kefu_new')->where($where)->find();
		$this->assign('kefu',$kefu);
		$this->assign('totalProductCount', $calCartInfo[0]);
		$this->assign('totalProductFee', $calCartInfo[1]);

		$cats = $this->product_cat_model->where(array('token' => $this->token))->order('id asc')->select();

        #银波米业的
        if ($_GET['token'] == '233eb0b413256696f977e4c963645877') {
            $this->assign('fix_nav', '1');
        }else{
            $this->assign('fix_nav', '0');
        }


        /*
        * 引入微信js接口
        */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);

        //p($cats);exit;
        /**
         * 首页备分类加两个商品
         */

        $iLimit = $this->bNew?20:2;
        if ($this->bNew) {
            foreach($cats as $key=>$v){
                $where['openid']=$this->openid;
                if($this->product_model->where($where)->limit($iLimit)->select()) {
                    $cats[$key]['shop'] = $this->product_model->where($where)->limit($iLimit)->order('paixu desc,id desc')->select();
                    if(!$cats[$key]['shop'][1]){
                        unset($cats[$key]['shop'][1]);
                    }
                    break;
                }else{//分类下面没有商品时，不显示分类跟商品
                    unset($cats[$key]);
                }
            }
        }else{
            foreach($cats as $key=>$v){
                $where['catid']=array('eq',$v['id']);
                if($this->product_model->where($where)->limit($iLimit)->order('paixu desc,id desc')->select()) {
                    $cats[$key]['shop'] = $this->product_model->where($where)->limit($iLimit)->order('paixu desc,id desc')->select();
                    if(!$cats[$key]['shop'][1]){
                        unset($cats[$key]['shop'][1]);
                    }
                }else{//分类下面没有商品时，不显示分类跟商品
                    unset($cats[$key]);
                }
            }
        }

       // p($cats[0]['shop']);die;
        //p($cats[0]['shop']);exit;

		$this->assign('cats', $cats);
        /**
         * 底部导航分类
         */
        $foot = $this->product_cat_model->where(array('token' => $this->token,'parentid'=>0))->order('id asc')->limit(4)->select();

        $this->assign('foot', $foot);
	}

	function remove_html_tag($str){  //清除HTML代码、空格、回车换行符
		//trim 去掉字串两端的空格
		//strip_tags 删除HTML元素
		$str = trim($str);
		$str = @preg_replace('/<script[^>]*?>(.*?)<\/script>/si', '', $str);
		$str = @preg_replace('/<style[^>]*?>(.*?)<\/style>/si', '', $str);
		$str = @strip_tags($str,"");
		$str = @ereg_replace("\t","",$str);
		$str = @ereg_replace("\r\n","",$str);
		$str = @ereg_replace("\r","",$str);
		$str = @ereg_replace("\n","",$str);
		$str = @ereg_replace(" ","",$str);
		$str = @ereg_replace("&nbsp;","",$str);
		return trim($str);
	}

	public function index() {
		$this->assign('tpl',$this->tpl);
		$this->assign('metaTitle', '商品分类');

	}

	public function cats() {
        /**
         * 首页分类显示两个商品
         */

		$where = array('token'=> $this->_get('token'));
			//获取商城模版名称
		$wh=array('token'=>$this->token);
		$tpl=D('Wxuser')->where($wh)->find();

        if($this->token == '233eb0b413256696f977e4c963645877'){
            $this->assign('contact', '0755-28535698');
        }

		//dump($tpl);exit;
		$this->weixinUser=$tpl;
		$this->tpl=$tpl;
	    //幻灯
       //echo 123;exit;
		$info = M('Flash')->where($where)->limit(5)->order('id desc')->select();
        //p($info);die;
		//print_r($info);exit;
		/*for($i=1;$i<6;$i++){
			if(!empty($info['img'.$i])){
				$info['img'][]=$info['img'.$i];
				unset($info['img'.$i]);
			}
		}
		*/
        $iLimit = 2;
        if ($this->bNew) {
            $iLimit = 100;
        }

		$date = M('reply_info')->where($where)->find();

        $articledata = M('Product_new_article')->where(array('token'=>$this->token))->limit($iLimit)->select();
        $flashdata = M('Product_new_flash')->where(array('token'=>$this->token))->select();
        //print_r($date);exit;
		$this->assign('flash', $info);
        //p($info);
		$this->assign('metaTitle', '商品分类');
		$this->assign('articledata', $articledata);
        if($this->token=='b9749efed643ce858d90a743253ee975'){
            $this->assign('flash_list', $flashdata);
        }else{
            $this->assign('flashdata', $flashdata);
        }

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
        /**
         * 快速分类导航
         */
        $fast_carts=$this->product_cat_model->where(array('token' => $this->token,'label'=>array('neq','')))->order('id asc')->select();
        //p($fast_carts);
        $this->assign('fast_carts',$fast_carts);
	if($this->bNew){
		$this->display('./tpl/Wap/default/Store_shop_cats_add.html');
	}else{
        if($this->token=='b9749efed643ce858d90a743253ee975'){
            $this->display('./tpl/Wap/default/Store_shop_cats2.html');
        }else{
            $this->display();
        }

	}
	}

    /**
     * 商品列表
     */
	public function products() {

        //获取商城模版名称
        $wh=array('token'=>$this->token);
        $tpl=D('Wxuser')->where($wh)->find();
        $this->weixinUser=$tpl;
        $this->tpl=$tpl;

		$where = array('token' => $this->token, 'groupon' => 0);
		$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
		if ($catid) {
			$where['catid'] = $catid;
			$thisCat = $this->product_cat_model->where(array('id'=>$catid))->find();
			$this->assign('thisCat', $thisCat);
		}
		if (IS_POST){
			$key = $this->_post('search_name');
            $this->redirect('/index.php?g=Wap&m=Store_new&a=products&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&keyword=' . $key.'&dopenid='.$this->dopenid);
		}
		if (isset($_GET['keyword'])){
            $where['name|intro|keyword'] = array('like', "%".$_GET['keyword']."%");
            $this->assign('isSearch', 1);
		}
		$count = $this->product_model->where($where)->count();
		$this->assign('count', $count);
		//排序方式
		$method = isset($_GET['method']) && ($_GET['method']=='DESC' || $_GET['method']=='ASC') ? $_GET['method'] : 'DESC';
		$orders = array('time', 'discount', 'price', 'salecount');
		$order = isset($_GET['order']) && in_array($_GET['order'], $orders) ? $_GET['order'] : 'time';
		$this->assign('order', $order);
		$this->assign('method', $method);

		$products = $this->product_model->where($where)->order("paixu desc, " . $order.' '.$method)->select();
		$this->assign('products', $products);

		$name = isset($thisCat['name']) ? $thisCat['name'] . '列表' : "商品列表";

		$this->assign('metaTitle', $name);

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
        $d['cate_id'] = $_GET['catid'];
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
        if(!$this->tpl['shoptpllistname']){
            $this->tpl['shoptpllistname'] = '100_products';
        }

        if($this->bNew){
            $this->display('./tpl/Wap/default/Store_shop_products_new.html');
        }else{
            $this->display();
        }
       // $this->display($this->tpl['shoptpllistname']);


	}
	public function hot_goods() {
        //获取商城模版名称
        $wh=array('token'=>$this->token);
        $tpl=D('Wxuser')->where($wh)->find();
        $this->weixinUser=$tpl;
        $this->tpl=$tpl;

		$where = array('token' => $this->token, 'groupon' => 0);
		$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
        $thisCat['name'] = '热卖商品';
        $this->assign('thisCat', $thisCat);

		$count = $this->product_model->where($where)->count();
		$this->assign('count', $count);
		//排序方式
		$hot_ids =M('Product_setting_new')
            ->where(array('token'=>$this->token))
            ->getField('hot_goods');

        $where['id'] = array('in', explode(',', $hot_ids));
		$products = $this->product_model->where($where)->select();
		$this->assign('products', $products);
        //滚动加载
        if(IS_AJAX){
            $plen = $this->_post('page','intval') * 4;
            $aList = $this->product_model->where($where)->limit($plen.',4')->select();
            $this->assign('info',$aList);
            $fetch = $this->fetch('./tpl/Wap/default/Store_page.html');
            echo $this->encode(array(
                'fetch' => $fetch,
                'status' => 0
            ));
            exit;
        }else{
            //echo 8945465;exit;
        }

        //echo $this->tpl['shoptpllistname'];exit;
        //$this->display($this->tpl['shoptpllistname']);
        $this->display('./tpl/Wap/default/Store_shop_products_new.html');
	}



	public function ajaxProducts(){
		$where=array('token'=>$this->token);
		if (isset($_GET['catid'])){
			$catid=intval($_GET['catid']);
			$where['catid']=$catid;
		}
		$page = isset($_GET['page']) && intval($_GET['page'])>1 ? intval($_GET['page']) : 2;
		$pageSize = isset($_GET['pagesize']) && intval($_GET['pagesize']) > 1 ? intval($_GET['pagesize']) : 8;

		$method = isset($_GET['method']) && ($_GET['method']=='DESC' || $_GET['method']=='ASC') ? $_GET['method'] : 'DESC';
		$orders = array('time', 'discount', 'price', 'salecount');
		$order = isset($_GET['order']) && in_array($_GET['order'], $orders) ? $_GET['order'] : 'time';
		$start=($page-1)*$pageSize;
		$products = $this->product_model->where($where)->order("sort ASC, " . $order.' '.$method)->limit($start . ',' . $pageSize)->select();
		$str='{"products":[';
		if ($products){
			$comma='';
			foreach ($products as $p){
				$str.=$comma.'{"id":"'.$p['id'].'","catid":"'.$p['catid'].'","storeid":"'.$p['storeid'].'","name":"'.$p['name'].'","price":"'.$p['price'].'","token":"'.$p['token'].'","keyword":"'.$p['keyword'].'","salecount":"'.$p['salecount'].'","logourl":"'.$p['logourl'].'","time":"'.$p['time'].'","oprice":"'.$p['oprice'].'"}';
				$comma=',';
			}
		}
		$str.=']}';
		$this->show($str);
	}

    /**
     * 商品详情页
     */
	public function product() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$where = array('token' => $this->token, 'id' => $id);
		$product = $this->product_model->where($where)->find();
		if (empty($product)) {
			$this->redirect(U('Store_new/products',array('token' => $this->token,'wecha_id' => $this->wecha_id,'dopenid'=>$this->dopenid)));
		}
		$product['intro'] = isset($product['intro']) ? htmlspecialchars_decode($product['intro']) : '';
		$this->assign('product', $product);

		if ($product['endtime']){
			$leftSeconds = intval($product['endtime'] - time());
			$this->assign('leftSeconds', $leftSeconds);
		}
        $normsData = M("Norms_new")->where(array('catid' => $product['catid']))->select();
        foreach ($normsData as $row) {
        	$normsList[$row['id']] = $row['value'];
        }
        if($productCatData = M('Product_cat_new')->where(array('id' => $product['catid'], 'token' => $this->token))->find()) {
        	$this->assign('catData', $productCatData);
        }
		$colorDetail = $normsDeatail = $productDetail = array();
		$attributeData = M("Product_attribute_new")->where(array('pid' => $product['id']))->select();

		$productDetailData = M("Product_detail_new")->where(array('pid' => $product['id']))->select();
		if($productDetailData){
			foreach ($productDetailData as $p) {
				$p['formatName'] = $normsList[$p['format']];
				$p['colorName'] = $normsList[$p['color']];

				$formatData[$p['format']] = $colorData[$p['color']] = $productDetail[] = $p;

				$colorDetail[$p['color']][] = $p;
				$normsDetail[$p['format']][] = $p;
			}
			$this->assign('productDetail', $productDetail);
			$this->assign('normsDetail', $normsDetail);
			$this->assign('colorDetail', $colorDetail);
			$this->assign('formatData', $formatData);
			$this->assign('colorData', $colorData);
		}else{
			$this->assign("id",$id);
		}

		$productimage = M("Product_image_new")->where(array('pid' => $product['id']))->select();
		$this->assign('imageList', $productimage);
		$this->assign('attributeData', $attributeData);
		$this->assign('metaTitle', $product['name']);
		$product['intro'] = str_replace(array('&lt;','&gt;','&quot;','&amp;nbsp;'),array('<','>','"',' '), $product['intro']);
		$intro = $this->remove_html_tag($product['intro']);
		$intro = mb_substr($intro, 0, 30,'utf-8');
		$this->assign('intro',$intro);

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

        $d['product_id'] = $_GET['id'];
        if(get_client_ip()){
            $d['ip'] = get_client_ip();
        }else{
            $d['ip'] = '未知';
        }
        $catInfo = M('Product_new')->where(array('token'=>$this->token,'id'=>$_GET['id']))->field('catid')->find();
        $d['cate_id'] = $catInfo['catid'];
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

        //商品详情
        //$this->productDetails(false, $id);
        $where = array('token' => $this->token, 'id' => $id);
        $product = $this->product_model->where($where)->field("intro,des")->find();
        $product['intro']=html_entity_decode($product['intro']);
        $comment = M('Product_comment')->where(array('pid'=>$id))->select();
        //$comment = M('Product_comment')->where(array('pid'=>$id))->select();
        $this->assign('lists',$comment);
        $this->assign('nums',$_GET['num']);
        $this->assign('products',$product);
        //
        $is_d_1 = M('Product_setting_new')->where(array('token'=>$this->token))->find();

        $this->assign('is_d_1',$is_d_1);
        #银波隐藏我要分销
        $this->assign('bHideFx', $_GET['token'] == '233eb0b413256696f977e4c963645877');
        //分销
        $this->fx_level();
     //   p($product);die;
        $this->autoShare = false;
	$this->display();
	//$this->UDisplay("./Wap/default/Store_shop_product");
	}


    /*
     *  确定分销关系
     */
    public function fx_level()
    {
        $dopenid  = $_REQUEST['dopenid'];
        if($dopenid == $this->openid){
            return;
        };

        //如果开启了分销而且是默认开启的
		$open=M("Product_setting_new")->where(array('token'=>$this->token))->find();
        #是否为互推
        $bBoth = M('Media_users')->where(array(
            'openid'        => $dopenid,
            'from_openid'   => $this->openid
        ))->count() > 0;
        if ($bBoth) {
            return;
        }

        if ($open['is_distribution'] AND in_array($open['get_distribution'], array(2,3)) AND $dopenid) {
            //先确定得到佣金人的
            $m_user = M('Media_users');

	    if($id=$m_user->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid,
		'from_openid' => array('neq', '')
            ))->getField('id')){
	    	return false;
	    }

            if(!$m_user->where(array(
                'token'=> $this->token,
                'openid'=>$dopenid
            ))->find()){//不是徽商就加上
                $data3['token'] = $this->token;
                $data3['openid'] = $dopenid;
                $data3['from_openid']='';

                $iID = M('Wxuser')
                    ->where(array('token' => $this->token))
                    ->getField('id');

                $data3['nickname'] = M('Wxusers')
                    ->where(array('uid' => $iID, 'openid' => $dopenid))
                    ->getField('nickname');

                $data3['add_time'] =date('Y-m-d H:i:s',time());
                $m_user->add($data3);
            }
            //再确定两个人关系的徽商记录
            if($id=$m_user->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))->getField('id')){
                $m_user->where(array('id'=>$id))->save(array('from_openid'=>$dopenid));
            }else{
                $data33['token'] = $this->token;
                $data33['openid'] = $this->openid;
                $data33['from_openid'] = $dopenid;

                $iID = M('Wxuser')
                    ->where(array('token' => $this->token))
                    ->getField('id');

                $data33['nickname'] = M('Wxusers')
                    ->where(array('uid' => $iID, 'openid' => $this->openid))
                    ->getField('nickname');

                $data33['add_time'] =date('Y-m-d H:i:s',time());

                $m_user->add($data33);

            }


        }
    }

    /**
     * 商品图文详情 规格参数 评价
     */
    public function productDetails($bShow=true, $iID=''){

        $id = isset($_GET['id']) ? intval($_GET['id']) :$iID;
        if($id){
            $where = array('token' => $this->token, 'id' => $id);
            $product = $this->product_model->where($where)->field("intro,des")->find();
            $product['intro']=html_entity_decode($product['intro']);
            $comment = M('Product_comment')->where(array('pid'=>$id))->select();
            //$comment = M('Product_comment')->where(array('pid'=>$id))->select();
            $this->assign('list',$comment);
            $this->assign('num',$_GET['num']);
            $this->assign('product',$product);
        }

        if($bShow) $this->display();
    }
    /**
     * 商品规格参数
     */
    public function productParameters(){
        $this->display('productDetails');
    }
    /**
     * 商品评价
     */
    public function productPingjia(){

    }
	/**
	 * 添加购物车
	 */
	public function addProductToCart() {
		$count = isset($_GET['count']) ? intval($_GET['count']) : 1;
		if (empty($this->wecha_id)) {
			echo false;
			die;
		}
		$carts = $this->_getCart();
		$id = intval($_GET['id']);
		$did = isset($_GET['did']) ? intval($_GET['did']) : 0;//商品的详细id,即颜色与尺寸
		if (isset($carts[$id])) {
			if ($did) {
				if (isset($carts[$id][$did])) {
					$carts[$id][$did]['count'] += $count;
				} else {
					$carts[$id][$did]['count'] = $count;
				}
			} else {
				$carts[$id] += $count;
			}
		} else {
			if ($did) {
				$carts[$id][$did]['count'] = $count;
			} else {
				$carts[$id] = $count;
			}
		}
		$_SESSION[$this->session_cart_name] = serialize($carts);
		$calCartInfo = $this->calCartInfo();
		echo $calCartInfo[0].'|'.$calCartInfo[1];
	}

	private function calCartInfo($carts=''){
		$totalCount = $totalFee = 0;
		if (!$carts) {
			$carts = $this->_getCart();
		}
		$data = $this->getCat($carts);
		if (isset($data[1])) {
			foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
			}
		}

		return array($totalCount, $totalFee, $data[2]);
	}

	private function _getCart() {
		if (!isset($_SESSION[$this->session_cart_name])||!strlen($_SESSION[$this->session_cart_name])){
			$carts = array();
		} else {
			$carts=unserialize($_SESSION[$this->session_cart_name]);
		}
		return $carts;
	}

	/**
	 * 购物车列表
	 */
	public function cart(){
		if (empty($this->wecha_id)) {
			unset($_SESSION[$this->session_cart_name]);
		}
		$totalCount = $totalFee = 0;
		$data = $this->getCat($this->_getCart());
		if (isset($data[1])) {
			foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
			}
		}
		$list = $data[0];
		$this->assign('products', $list);
		$this->assign('totalFee', $totalFee);
		$this->assign('totalCount', $totalCount);
		$this->assign('metaTitle','购物车');
		$this->display();
	}

	public function Buy(){
        $row['orderid'] =substr($this->wecha_id, -1, 4) . date("YmdHis");
		$row['token'] = $this->token;
		$row['wecha_id'] = $this->wecha_id;
		$row['time'] = time();
		$row['productid']=$this->_get("did");
		$row['total']=$this->_get("count");
		$row['type']=$this->_get("type");
		if($this->_get("type")=="son"){
			$data=M("Product_detail_new")->field("pid,price,vprice")->where(array("id"=>$this->_get("did")))->find();
			$parent=M("Product_new")->field("mailprice")->where(array(array("id"=>$data['pid'])))->find();
			$mailpay=$parent['mailprice'];
		}else{
			$data=M("Product_new")->field("mailprice,price,vprice")->where(array("id"=>$this->_get("did")))->find();
			$mailpay=$data['mailprice'];
		}
		if(M("Usercenter_memberlist")->where(array("openid"=>$this->wecha_id))->find()){
			$row['price']=$data['vprice'];
		}else{
			$row['price']=$data['vprice'];
		}
		$totalpay=intval($row['total'])*intval($row['price']);
		$open=M("Product_setting_new")->field("price")->where(array("token"=>$this->token))->find();
		if($totalpay>=$open['price'] && $open['price']!=0){
			$row['mailpay']=0;
		}else{
			$row['mailpay']=$mailpay;
		}
		$pid=M("Product_cart_new")->add($row);//添加订单
		$data['pid']=$pid;
		$data['wecha_id']=$this->wecha_id;
		if($addr=M("Product_address")->where(array("uid"=>$this->wecha_id))->find()){
			$save['truename']=$addr['name'];
			$save['tel']=$addr['tel'];
			$save['address']=$addr['seng']."省(市)".$addr['si']."市(区)".$addr['xian']."县".$addr['detail'];
			if(M("Product_cart_new")->where(array("id"=>$pid))->save($save)){

				$this->ajaxReturn(array("status"=>1,"info"=>"添加成功","data"=>$data));
			}
		}else{
			$this->ajaxReturn(array("status"=>0,"info"=>"请先填写地址","data"=>$data));
		}
	}

	public function address(){
//		if(M("Product_address")->add($_POST)){
//			$data['tel']=$_POST['tel'];
//			$data['truename']=$_POST['name'];
//			$data['address']=$_POST['seng']."省(市)".$_POST['si']."市(区)".$_POST['xian']."县".$_POST['detail'];
//			if(M("Product_cart_new")->where(array("id"=>$_POST['pid']))->save($data)){
//                $this->redirect("Store_new/show",array("id"=>$_POST['pid']));
//			}else{
//               $this->error2("操作失败");
//			}
//		}else{
//			$this->error2("操作失败");
//		}
		if(IS_POST){
			$data['tel']=$_POST['tel'];
			$data['truename']=$_POST['name'];
			$data['address']=$_POST['seng']."省(市)".$_POST['si']."市(区)".$_POST['xian']."县".$_POST['detail'];
			$_POST['uid']=$_GET['wecha_id'];

			if(M("Product_cart_new")->where(array("id"=>$_POST['pid']))->save($data) && M("Product_address")->add($_POST)){

                $this->redirect("Store_new/show",array("pid"=>$_POST['pid'],"token"=>$_POST['token'],"wecha_id"=>$_POST['uid'],'openid'=>$this->openid,'dopenid'=>$this->dopenid));
			}else{
               $this->error2("操作失败");
			}
		}else{
			$this->assign("pid",$this->_get("pid","intval"));
			$this->assign("wecha_id",$this->wecha_id);
			$this->display();
		}
	}

    /**
     * 邮寄地址
     */
	public function edit(){
		if(IS_POST){
            $address=M('Product_address');
            //p($_POST);die;
            if(M("Product_address")->where(array("uid"=>$_POST['openid']))->find()){
                $aPost = $_POST;
                unset($aPost['id']);
                if(false !== M("Product_address")->where(array("id"=>$_POST['id']))->save($aPost)) {
                    $this->redirect("Store_shop/show", array("token" => $_POST['token'], "openid" => $_POST['uid'], 'id' => $_POST['sid'], 'dopenid' => $_POST['dopenid'], 'type' => 1));
                }
            }else{
                $address->create();
                if($address->add()){
                    $this->redirect("Store_shop/show", array("token" => $_POST['token'], "openid" => $_POST['uid'], 'id' => $_POST['sid'], 'dopenid' => $_POST['dopenid'], 'type' => 1));

                }
            }
		}else{
			$data=M("Product_address")->where(array("uid"=>$this->_get("uid")))->find();
			$this->assign("data",$data);
			$this->assign("uid",$this->_get("uid"));
			$this->assign("sid",$this->_get("sid"));
			$this->display();
		}
	}
    /**
     * 使用我的优惠卷
     */
    public function isgetsalecard(){
        $usersalecard = M('Sn')->where(array('token'=>$this->token,'openid'=>$this->openid,'status'=>0))->select();
        $this->assign('usersalecard',$usersalecard);
        $this->display();
    }
    /**
     * 我的优惠卷
     */
    public function salecard(){
        $salecardlist = M('Sn')->where(array('token'=>$this->token,'openid'=>$this->openid,'endtime'=>array('gt',time())))->select();
        $this->assign('salecardlist',$salecardlist);

        $this->display();
    }

    function getWuliu(){
        $aWuliu = array();
        switch($this->token){
        case '233eb0b413256696f977e4c963645877'://银波
            $aWuliu = array('快递配送', '附近配送点配送');
            break;
        case '6278b84cdaa4f05c9bff030d6338d270'://关爱天使
            $aWuliu = array('韵达', '中通', '圆通');
            break;
        case 'b873d389377626a99006d613c85fb3a6'://益笙行
            $aWuliu = array('快递(当天发货)');
            break;
        default:
            $aWuliu = array('普通快递', '邮政包裹');
        }
        $this->assign('wuliu', $aWuliu);
    }
    // 获取唯一订单号
    public function getSn(){

        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
    /**
     * 我要购买详细页
     */
	public function show(){
        $this->getWuliu();
        /**
         * 自己的修改
         */
        if(IS_AJAX){//提交订单处理

           // $data['orderid'] =substr($this->wecha_id, -1, 4) . date("YmdHis");

            $data['orderid'] =$this->getSn();
            $data['token']=$this->_get('token');
            $data['wecha_id']=$this->_get('openid');
            $data['type']=$this->_get('type');
            $data['productid']=$this->_get('productid');
            $data['total']=$this->_post('total');
           // $data['price']=$this->_post('price');
            $data['tel']=$this->_post('tel');
            $data['truename']=$this->_post('truename');
            $data['address']=$this->_post('address');
            $data['time']=time();
           $data['logistics']=$this->_post('logistics');
            /**
             * 开启分销且，购买人没有上级时在订单表里面插入dopenid
             */
            if($this->dopenid&&($this->openid!=$this->dopenid)){
                //如果开启了分销
                $is_d = M('Product_setting_new')->where(array('token'=>$this->token))->find();
		if($is_d['is_distribution'] == 1 ){
                    $sFromOpenId = M('Media_users')
                        ->where(array('token'=>$this->token,'openid'=>$this->openid))
                        ->getField('from_openid');
                    if(
                        $sFromOpenId == $this->dopenid or
                        !$sFromOpenId
                    ){
                        $data['dopenid']=$this->dopenid;
                    }
                }
            }

            /**
             * 这块是算邮寄费用
             */
            if($this->_get("type")=="son"){
                $data1=M("Product_detail_new")->field("pid,price,vprice")->where(array("id"=>$this->_get("productid")))->find();
                $parent=M("Product_new")->field("mailprice")->where(array(array("id"=>$data1['pid'])))->find();
                $mailpay=$parent['mailprice'];
            }else{
                $data1=M("Product_new")->field("mailprice,price,vprice")->where(array("id"=>$this->_get("productid")))->find();
                $mailpay=$data1['mailprice'];
            }
            $data['price']=$data1['vprice'];//设置价格
            $open=M("Product_setting_new")->field("price,cut_price")->where(array("token"=>$this->token))->find();
            $totalpay=intval($data['total'])*intval($data1['price']);
            if($totalpay>=$open['price'] && $open['price']!=0){
                $data['mailpay']=0;
            }else{
                $data['mailpay']=$mailpay;
            }
            /**
             * 这块是算邮寄费用结束
             */
            if($this->_post('sid')>0){
                $data['sid']=$this->_post('sid');
            }
            if($_POST['note']){
                $data['note']=$this->_post('note');
            }
            $oProduct_cart_new=M("Product_cart_new");
            if($this->_get('pid')){
                $data['id']=$this->_get('pid');
                if($oProduct_cart_new->save($data)){
                    $arr['state']=1;
                    $arr['orderid']= $data['orderid'];
                }else{
                    $arr['state']=0;
                }
            }else{
                if($oProduct_cart_new->add($data)){
                    $arr['state']=1;
                    $arr['orderid']= $data['orderid'];
                }else{
                    $arr['state']=0;
                }
            }
            /*
                * 通知微信
                */
            if($_GET['type']=='par'){
                $sName=M('Product_new')->where(array('id'=>$data['productid']))->getField('name');
            }else{//有规格的商品
                $sName=M('Product_detail_new')->join("join tp_product_new as a on a.id=tp_product_detail_new.pid")
                    ->where(array('tp_product_detail_new.id'=>$data['productid']))->getField('a.name');
            }
            $strDetail="订单详情:\n".$sName." × ".$data['total'];
            $notichcontent = $this->wxusers['nickname']."您好,交易提醒\n订单编号：".$data['orderid']."\n创建时间:".$data['time']."\n订单总额:".$_POST['price']."元\n".$strDetail."\n收货人:".$data['truename']."\n电话:".$data['tel']."\n地址:".$data['address']."\n祝您身体健康，万事如意";
            $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
            $data = $this->api_notice_increment($url,http_build_query($postdata));
            if(!$data){
                $this->api_notice_increment($url,http_build_query($postdata));
            }

            echo json_encode($arr);

        }else {


            $id = $this->_get("id");//商品id
            $where['id'] = array('eq', $id);
            $product = $this->product_model->where($where)->find();
            //p($product);die;
            /**
             * 加入购物车
             */
            $data2=array();
            $name = $product['name'];
            $vprice = $product['price'];
            $logourl = $product['logourl'];
            $token=$this->token;
            if(session("?".$token)){
                $carta=session("$token");
                if(!array_key_exists($id,$carta)){
                    $carta[$id] = array('id' => $id, 'name' => $name,'vprice'=>$vprice,'logourl'=>$logourl);
                    $data2['status']=0;
                }
            }else{
                $carta[$id] = array('id' => $id, 'name' => $name,'vprice'=>$vprice,'logourl'=>$logourl);
                $data2['status']=0;
            }
            session("$token", $carta,3600*24);

            /**
             * 或取地址
             */
            $address = M('Product_address')->where(array('uid' => $_GET['openid']))->find();
            $this->assign("address", $address);
            $pid = $this->_get("pid");//订单id
            $address = M("Product_address")->where(array("uid" => $this->wecha_id))->find();
            if ($user = M("Usercenter_memberlist")->field("score")->where(array("openid" => $this->wecha_id))->find()) {
                $this->assign("user", $user);
            }
            if ($coupon = M("sn")->where(array("openid" => $this->wecha_id, "status" => 0, 'endtime' => array('gt', time())))->select()) {
                $this->assign("coupon", $coupon);
            }
            if ($change = M("Product_setting_new")->field("score,price,is_distribution")->where(array("token" => $this->token))->find()) {
                $this->assign("change", $change);
            }
            $data = $this->getinfo($pid);//已存在的订单去支付

            if(!empty($product)){
             //   p($product);
                $this->assign("shop", $product);
            }else{
                $product1['price']=$data['price'];
                $product1['name']=$data['name'];
                $product1['num']=20;
                $product1['price']=$data['price']/$data['total'];
                $this->assign("shop", $product1);


            }
            if ($this->dopenid) {
                $is_d = M('Product_setting_new')->where(array('token' => $this->token))->find();
                //如果开启了分销
                if ($is_d['is_distribution'] == 1) {
                    /**
                     * 这里是以前的
                     */
                   /* $d['token'] = $this->token;
                    $d['ws_openid'] = $this->dopenid;
                    $wsInfo = M('Homenice_user')->where(array('token' => $this->token, 'openid' => $d['ws_openid']))->find();
                    $d['ws_name'] = $wsInfo['name'];
                    $d['order_price'] = $data['price'] * $data['total'];
                    $d['order_id'] = $data['orderid'];
                    if (!M('Homenice_commission')->where(array('order_id' => $data['orderid']))->find()) {
                        $d['order_name'] = $data['truename'];
                        $d['add_time'] = date('Y-m-d H:i:s');
                        $distributionRecorder = M('Homenice_commission')->add($d);
                    }*/

                }
            }
            $this->assign("data", $data);
            //得到总共有多少优惠卷
            $usercenter_user_salecard=M('Sn');
            $snum=$usercenter_user_salecard->where(array('token'=>$this->token,'openid'=>$this->openid))->count();
            $this->assign('snum',$snum);
            /**
             * 取优惠卷,取到的SID必须是他自己的
             */
            if($_GET['sid']&&$usercenter_user_salecard->where(array('token'=>$this->token,'openid'=>$this->openid,'id'=>$_GET['sid']))->find()){

                $slist=$usercenter_user_salecard->field('id,amount,snname')->where(array('id'=>$_GET['sid']))->find();

                $this->assign('slist',$slist);
            }
            /**
             * 商城设置
             */
            $product_setting_new=M('Product_setting_new');
            $y_price=$product_setting_new->where(array('token'=>$this->token))->getField('price');//多少元免邮费
            $this->assign('y_price',$y_price);

            /**
             * 商品 套餐，属性
             */
            $product_shuxi=M('Product_detail_new')->field('tp_product_detail_new.color,tp_product_detail_new.id,tp_product_detail_new.pid,tp_product_detail_new.format,tp_product_detail_new.num,
            tp_product_detail_new.price,tp_product_detail_new.vprice,tp_norms_new.value')->join("join tp_norms_new on tp_norms_new.id=tp_product_detail_new.format")->where(array('tp_product_detail_new.pid'=>$id))->select();
            foreach($product_shuxi as $k=>$v){
              $product_shuxi[$k]['color_value']=M('Norms_new')->where(array('id'=>$v['color']))->getField('value');
            }
           // p($product_shuxi);
            $this->assign('product_shuxi',$product_shuxi);

            $this->display();
        }
	}

    /**
     * @param string $pid
     * @return mixed  分销
     */
    public function fx(){
        if(IS_AJAX){
            /*$aData['token']=$this->token;
            $aData['openid']=$this->openid;
            $aData['reg_time']=time();
            $oHomefx_user=M('Homefx_user');
            if($oHomefx_user->where(array('token'=>$aData['token'],'openid'=>$aData['openid']))->find()){
                $arr['state']=1;
            }else{
                $idWxuser=M('Wxuser')->where(array('token'=>$this->token))->getField('id');//得到共公号ID
                $aWxusers=M('Wxusers')->field('city,province,country')->where(array('uid'=>$idWxuser,'openid'=>$this->openid))->find();//得到本人的名字省市资料
                $aData['loc_province']=$aWxusers['province'];
                $aData['loc_city']=$aWxusers['city'];
                $aUsercenter_memberlist=M('Usercenter_memberlist')->field('name,phone,address')->where(array('uid'=>$idWxuser,'openid'=>$this->openid))->find();//得本人的一些资料
                $aData['phone']=$aUsercenter_memberlist['phone'];
                $aData['name']=$aUsercenter_memberlist['name'];
                if($oHomefx_user->add($aData)){
                    $arr['state']=1;
                }else{
                    $arr['state']=0;
                }*/
                $arr['state']=1;
            echo json_encode($arr);
        }else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $where = array('token' => $this->token, 'id' => $id);
            $product = $this->product_model->where($where)->find();
            if (empty($product)) {
                $this->redirect(U('Store_new/products',array('token' => $this->token,'wecha_id' => $this->wecha_id,'dopenid'=>$this->dopenid)));
            }
            $product['intro'] = isset($product['intro']) ? htmlspecialchars_decode($product['intro']) : '';
            $this->assign('product', $product);


            $id = $this->_get("id");//商品id
            $where['id'] = array('eq', $id);
            $aProduct = $this->product_model->where($where)->find();
            $this->assign("shop", $aProduct);
            $data['shop_id'] = $id;
            $user = M('Wxuser')->where(array('token'=>$this->token))->find();
            $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$this->openid))->find();
            $data['user_id'] = $users['id'];
           // p($data);
            $posts = serialize($data);

            if($cid = M('Code')->add(array('value'=>$posts))){
                $sImageUrl = $this->getCode($cid);
                $this->assign("image", $sImageUrl);//生成二维码图片
            }


            /**
             * 没有受权，获取appid
             */
            $appidInfo=M('Diymen_set')->where(array('token'=>$this->token))->find();
            $this->assign('appidInfo',$appidInfo);

            //查出已有多少人分亨
            $pNum=M('Homenice_user')->where(array('token'=>$this->token))->count();
            $this->assign('pNum',$pNum);
            $this->display();
        }
    }
    /**
     * @param string $pid
     * @return mixed生成二维码图片
     */
    public function getCode($id) {

        //$userinfo=M("Wxusers")->field("id")->where(array("openid"=>$this->openid))->find();"'.$this->iSenceID.'"

        $parament = '{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": "200'.$id.'"}}}';

        /*获取access_token*/

        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        //p($api);
        if($api){

            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];


            $json = json_decode(file_get_contents($url_get));
            $access_token = $json->access_token;
           // $access_token = "GVkLr2R7pPpgnmCHovoSkJqYugUzNA1y4crUwpZlenhMk80qRD9ijinh2O8BwL3ACwqoCGxohSlath0OJK5AslJH7dSnNx9foGvJ_UjTEdU";

            $imgSource = $this->creatTicket($access_token, $parament);

        }

        return $imgSource['header']['url'];

    }

    /**
     * @param $token
     * @param $parament
     * @return array   生成二维码
     */
    public function creatTicket($token, $parament) {
        /*发送数据到微信服务器端并获取数据*/

        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";

        $result = $this->api_notice_increment($url, $parament);
        $jsonInfo = json_decode($result, true);
        $ticket = $jsonInfo['ticket'];

        /*根据ticket获取图片资源*/

        $url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url2);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_NOBODY, 0);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpInfo = curl_getinfo($ch);
        return array_merge(array('body'=>$package), array('header'=>$httpInfo));
    }
	//查找订单里面对应商品的信息，属性
	public function getinfo($pid=""){
		$data=M("Product_cart_new")->field("is_cart,id,orderid,productid,total,price,sent,logistics,logisticsid,address,wecha_id,paid,truename,tel,type,mailpay")->where(array("id"=>$pid))->find();//订单信息
		if($data['type']=="par"){
			$fid=$data['productid'];
            $info['productid']=$data['productid'];//商品id
		}else{
			$goods=M("Product_detail_new")->field("pid,format,color,vprice")->where(array("id"=>$data[productid]))->find();
			$fid=$goods['pid'];//父id
			$attrs['format']=$goods['format'];
			$attrs['color']=$goods['color'];
			$attr="";
			foreach($attrs as $v){
				$vae=M("Norms_new")->where(array("id"=>$v))->find();
				$attr.=" ".$vae['value'];
			}
			$info['attr']=$attr;
            $info['productid']=$goods['pid'];//商品id
			//$info['productid']=$data['productid'];
		//	$info['parid']=$goods['pid'];
		}
        //购物车订单的商品列表
        if($data['is_cart']){
            $data['cart_info']=M('Product_sidedetail')->where(array('cid'=>$data['id']))->select();
        }
		$ginfo=M("Product_new")->field("name,logourl,mailprice")->where(array("id"=>$fid))->find();
		$info['truename']=$data['truename'];
		$info['tel']=$data['tel'];
		$info['id']=$data['id'];
        $info['sent']=$data['sent'];
        $info['logistics']=$data['logistics'];
		$info['orderid']=$data['orderid'];
		$info['name']=$ginfo['name'];
		$info['logourl']=$ginfo['logourl'];
		$info['total']=$data['total'];
		$info['price']=$data['price'];
		$info['mailpay']=$data['mailpay'];
		$info['address']=$data['address'];
		$info['wecha_id']=$data['wecha_id'];
		$info['paid']=$data['paid'];
        $info['logisticsid']=$data['logisticsid'];
        $info['is_cart']=$data['is_cart'];
        $info['cart_info']=$data['cart_info'];
		return $info;
	}
	/**
	 * 计算一次购物的总的价格与数量
	 * @param array $carts
	 */
	public function getCat($carts = '')
	{
		$carts = empty($carts) ? $this->_getCart() : $carts;
		//邮费
		$mailPrice = 0;
		//商品的IDS
		$pids = array_keys($carts);

		//商品分类IDS
		$productList = $cartIds = array();
		if (empty($pids)) {
			return array(array(), array(), array());
		}

		$productdata = $this->product_model->where(array('id'=> array('in', $pids)))->select();
		foreach ($productdata as $p) {
			if (!in_array($p['catid'], $cartIds)) {
				$cartIds[] = $p['catid'];
			}
			$mailPrice = max($mailPrice, $p['mailprice']);
			$productList[$p['id']] = $p;
		}
		//商品规格参数值
		$catlist = $norms = array();
		if ($cartIds) {
			$normsdata = M('norms_new')->where(array('catid' => array('in', $cartIds)))->select();
			foreach ($normsdata as $r) {
				$norms[$r['id']] = $r['value'];
			}
			//商品分类
			$catdata = M('Product_cat_new')-> where(array('id' => array('in', $cartIds)))->select();
			foreach ($catdata as $cat) {
				$catlist[$cat['id']] = $cat;
			}
		}
		$dids = array();
		foreach ($carts as $pid => $rowset) {
			if (is_array($rowset)) {
				$dids = array_merge($dids, array_keys($rowset));
			}
		}
		//商品的详细
		$totalprice = 0;
		$data = array();
		if ($dids) {
			$dids = array_unique($dids);
			$detail = M('Product_detail_new')->where(array('id'=> array('in', $dids)))->select();
			foreach ($detail as $row) {
				$row['colorName'] = isset($norms[$row['color']]) ? $norms[$row['color']] : '';
				$row['formatName'] = isset($norms[$row['format']]) ? $norms[$row['format']] : '';
				$row['count'] = isset($carts[$row['pid']][$row['id']]['count']) ? $carts[$row['pid']][$row['id']]['count'] : 0;
				if ($this->fans['getcardtime'] > 0) {
					$row['price'] = $row['vprice'] ? $row['vprice'] : $row['price'];
				}
				$productList[$row['pid']]['detail'][] = $row;
				$data[$row['pid']]['total'] = isset($data[$row['pid']]['total']) ? intval($data[$row['pid']]['total'] + $row['count']) : $row['count'];
				$data[$row['pid']]['totalPrice'] = isset($data[$row['pid']]['totalPrice']) ? intval($data[$row['pid']]['totalPrice'] + $row['count'] * $row['price']) : $row['count'] * $row['price'];//array('total' => $totalCount, 'totalPrice' => $totalFee);
				$totalprice += $data[$row['pid']]['totalPrice'];
			}
		}
		//商品的详细列表
		$list = array();
		foreach ($productList as $pid => $row) {
			if (!isset($data[$pid]['total'])) {
				$row['count'] = $data[$pid]['total'] = isset($carts[$pid]['count']) ? $carts[$pid]['count'] : (isset($carts[$pid]) && is_int($carts[$pid]) ? $carts[$pid] : 0);
				if ($this->fans['getcardtime'] > 0) {
					$row['price'] = $row['vprice'] ? $row['vprice'] : $row['price'];
				}
				$data[$pid]['totalPrice'] = $data[$pid]['total'] * $row['price'];
				$totalprice += $data[$pid]['totalPrice'];
			}
			$row['formatTitle'] =  isset($catlist[$row['catid']]['norms']) ? $catlist[$row['catid']]['norms'] : '';
			$row['colorTitle'] =  isset($catlist[$row['catid']]['color']) ? $catlist[$row['catid']]['color'] : '';
			$list[] = $row;
		}
		if ($obj = M('Product_setting_new')->where(array('token' => $this->token))->find()) {
			if ($totalprice >= $obj['price']) $mailPrice = 0;
		}
		return array($list, $data, $mailPrice);
	}

	public function deleteCart(){
		$products=array();
		$ids=array();
		$carts=$this->_getCart();
		$did = isset($_GET['did']) ? intval($_GET['did']) : 0;
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if ($did) {
			unset($carts[$id][$did]);
			if (empty($carts[$id])) {
				unset($carts[$id]);
			}
		} else {
			unset($carts[$id]);
		}
		$_SESSION[$this->session_cart_name] = serialize($carts);
		$this->redirect(U('Store_new/cart',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'],'dopenid'=>$_GET['dopenid'])));
	}
	public function ajaxUpdateCart(){
		$count = isset($_GET['count']) ? intval($_GET['count']) : 1;
		$carts = $this->_getCart();
		$id = intval($_GET['id']);
		$did = isset($_GET['did']) ? intval($_GET['did']) : 0;
		if (isset($carts[$id])) {
			if ($did) {
				$carts[$id][$did]['count'] = $count;
			} else {
				$carts[$id] = $count;
			}
		} else {
			if ($did) {
				$carts[$id][$did]['count'] = $count;
			} else {
				$carts[$id] = $count;
			}
		}
		$_SESSION[$this->session_cart_name] = serialize($carts);
		$calCartInfo = $this->calCartInfo();
		echo $calCartInfo[0].'|'.$calCartInfo[1];
	}

	//保存订单
	public function ordersave() {
        //echo $this->dopenid;exit;
//		$id=$this->_get("id","intval");
//		$carts=M("Product_cart_new")->where(array("id"=>$id))->find();
//		echo "<pre>";
//		print_r($carts);exit;
		$row = array();
		$row['orderid'] = $orderid = substr($this->wecha_id, -1, 4) . date("YmdHis");
		$row['truename'] = $this->_post('truename');
		$row['tel'] = $this->_post('tel');
		$row['address'] = $this->_post('address');
		$row['token'] = $this->token;
		$row['wecha_id'] = $this->wecha_id;
		$row['time'] = $time = time();
		$row['paymode'] = isset($_POST['paymode']) ? intval($_POST['paymode']) : 1;
		//积分
		$score = isset($_POST['score']) ? intval($_POST['score']) : 0;
		$normal_rt = 0;
		$carts = $this->_getCart();
		if ($carts){
			$calCartInfo = $this->calCartInfo($carts);
			$setting = M('Product_setting_new')->where(array('token' => $this->token))->find();
			$totalprice = $calCartInfo[1] + $calCartInfo[2];
			if ($score && $setting && $setting['score'] > 0 && $this->fans['total_score'] >= $score) {
				$totalprice -= $score / $setting['score'];
				if ($totalprice < 0) {
					$score = ($calCartInfo[1] + $calCartInfo[2]) * $setting['score'];
					$totalprice = 0;
					$row['paid'] = 1;
				}
			}
			$row['total'] = $calCartInfo[0];
			$row['price'] = $totalprice;
			$row['diningtype'] = 0;
			$row['buytime'] = '';
			$row['tableid'] = 0;
			$row['info'] = serialize($carts);
			$row['groupon']=0;
			$row['dining'] = 0;
			$row['score'] = $score;

			$product_cart_model = M('product_cart_new');
			$normal_rt = $product_cart_model->add($row);
            //保存一条分销信息
            if($this->dopenid){
                $is_d = M('Product_setting_new')->where(array('token'=>$this->token))->find();
                //如果开启了分销
                if($is_d['is_distribution'] == 1){
                    $d['token'] = $this->token;
                    $d['ws_openid'] = $this->dopenid;
                    $wsInfo = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$d['ws_openid']))->find();
                    $d['ws_name'] = $wsInfo['name'];
                    $d['order_price'] = $row['price'];
                    $d['order_id'] = $row['orderid'];
                    $d['order_name'] = $row['truename'];
                    $d['add_time'] = date('Y-m-d H:i:s');
                    //print_r($d);exit;
                    $distributionRecorder = M('Homenice_commission')->add($d);
                }
            }
			//TODO 发货的短信提醒
			if ($normal_rt) {
				$info=M('delisms_new')->where(array('token'=>$this->token))->find();
			$phone=$info['phone'];
			$user=$info['name'];//短信平台帐号
			$pass=md5($info['password']);//短信平台密码
			$smsstatus=$info['shangcheng'];//短信平台状态
			$content = $this->sms();
			if ($smsstatus == 1) {
				if ($content) {
					$smsrs = file_get_contents('http://api.smsbao.com/sms?u='.$user.'&p='.$pass.'&m='.$phone.'&c='.urlencode($content));
					//$log = file_get_contents('http://www.test.com/test.php?u=' . $user . '&p=' . $pass . '&m=' . $phone . '&test=' . urlencode($content));
				}
			}
			//发送短信通知结束

			// 增加 发送邮件
			$info=M('deliemail_new')->where(array('token'=>$this->token))->find();
			$emailstatus=$info['shangcheng'];
			$emailreceive=$info['receive'];
			$content = $this->sms();
			if($info['type'] == 1){
			$emailsmtpserver=$info['smtpserver'];
			$emailport=$info['port'];
			$emailsend=$info['name'];
			$emailpassword=$info['password'];
			}else{
			$emailsmtpserver=C('email_server');
			$emailport=C('email_port');
			$emailsend=C('email_user');
			$emailpassword=C('email_pwd');
			}
			$emailuser=explode('@', $emailsend);
			$emailuser=$emailuser[0];
			if ($emailstatus == 1) {
				if ($content) {
					date_default_timezone_set('PRC');
					require("class.phpmailer.php");
					$mail = new PHPMailer();
					$mail->IsSMTP();                                      // set mailer to use SMTP
					$mail->Host = "$emailsmtpserver";  // specify main and backup server
					$mail->SMTPAuth = true;     // turn on SMTP authentication
					$mail->Username = "$emailuser"; // SMTP username
					$mail->Password = "$emailpassword"; // SMTP password
					$mail->From = $emailsend;
					$mail->FromName = C('site_name');
					$mail->AddAddress("$emailreceive", "商户");
					//$mail->AddAddress("ellen@example.com");                  // name is optional
					$mail->AddReplyTo($emailsend, "Information");

					$mail->WordWrap = 50;                                 // set word wrap to 50 characters
					//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
					//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
					$mail->IsHTML(false);                                  // set email format to HTML

					$mail->Subject = '您的商城订单';
					$mail->Body    = $content;
					$mail->AltBody = "";

					if(!$mail->Send())
					{
					   echo "Message could not be sent. <p>";
					   echo "Mailer Error: " . $mail->ErrorInfo;
					   exit;
					}
					echo "Message has been sent";
				}
			}
			}
		}
		if ($normal_rt){
			$product_model = M('product_new');
			$product_cart_list_model = M('product_cart_list_new');
			$crow = array();
			$tdata = $this->getCat($carts);
			foreach ($carts as $k => $c){
				$crow['cartid'] = $normal_rt;
				$crow['productid'] = $k;
				$crow['price'] = $tdata[1][$k]['totalPrice'];//$c['price'];
				$crow['total'] = $tdata[1][$k]['total'];
				$crow['wecha_id'] = $row['wecha_id'];
				$crow['token'] = $row['token'];
				$crow['time'] = $time;
				$product_cart_list_model->add($crow);

				//增加销量
				$product_model->where(array('id'=>$k))->setInc('salecount', $tdata[1][$k]['total']);
			}

			//删除库存
			foreach ($carts as $pid => $rowset) {
				$total = 0;
				if (is_array($rowset)) {
					foreach ($rowset as $did => $ro) {
						M('Product_detail_new')->where(array('id' => $did, 'pid' => $pid, 'num' => array('gt', $ro['count'])))->setDec('num', $ro['count']);
						$total += $ro['count'];
					}
				} else {
					$total = $rowset;
				}
				$product_model->where(array('id' => $pid, 'num' => array('gt', $total)))->setDec('num', $total);
			}
			$_SESSION[$this->session_cart_name] = null;
			unset($_SESSION[$this->session_cart_name]);
			//保存个人信息
			if ($_POST['saveinfo']){
				$userinfo_model = M('Userinfo_new');
				$thisUser = $userinfo_model->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
				$this->assign('thisUser', $thisUser);
				$userRow=array('tel'=>$row['tel'],'truename'=>$row['truename'],'address'=>$row['address']);
				if ($thisUser) {
					$userinfo_model->where(array('id' => $thisUser['id']))->save($userRow);
					$userinfo_model->where(array('id' => $thisUser['id'], 'total_score' => array('gt', $score)))->setDec('total_score', $score);
					F('fans_token_wechaid', NULL);
				} else {
					$userRow['token']=$this->token;
					$userRow['wecha_id']=$this->wecha_id;
					$userRow['wechaname']='';
					$userRow['qq']=0;
					$userRow['sex']=-1;
					$userRow['age']=0;
					$userRow['birthday']='';
					$userRow['info']='';
					$userRow['total_score']=0;
					$userRow['sign_score']=0;
					$userRow['expend_score']=0;
					$userRow['continuous']=0;
					$userRow['add_expend']=0;
					$userRow['add_expend_time']=0;
					$userRow['live_time']=0;
					$userinfo_model->add($userRow);
				}
			}

			$alipayConfig = M('Alipay_config_new')->where(array('token' => $this->token))->find();
			if ($alipayConfig['open'] && $totalprice && $row['paymode'] == 1) {
				$this->redirect(U('Alipay/pay',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'success' => 1, 'from'=> 'Store', 'orderName' => $orderid, 'single_orderid' => $orderid, 'price' => $totalprice)));
			} else {
				$this->redirect(U('Store_new/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'],'success'=>1)));
			}
			exit(json_encode(array('error_code' => false, 'msg' => 'ok', 'orderid' => $orderid, 'price' => $calCartInfo[1] + $calCartInfo[2], 'orderName'=> $orderid, 'isopen'=> $alipayConfig['open'])));
		} else {
			exit(json_encode(array('error_code' => true, 'msg' => '订单生产失败')));
		}
	}
	public function sms(){
		$where['token']=$this->token;
		$where['wecha_id']=$this->wecha_id;
		$where['printed']=0;
		$this->product_cart_model=M('product_cart');
		$count      = $this->product_cart_model->where($where)->count();
		$orders=$this->product_cart_model->where($where)->order('time DESC')->limit(0,1)->select();

		$now=time();
		if ($orders){
			$thisOrder=$orders[0];
			switch ($thisOrder['diningtype']){
				case 0:
					$orderType='购物';
					break;
				case 1:
					$orderType='点餐';
					break;
				case 2:
					$orderType='外卖';
					break;
				case 3:
					$orderType='预定餐桌';
					break;
			}

			//订餐信息
			$product_diningtable_model=M('product_diningtable_new');
			if ($thisOrder['tableid']) {
				$thisTable=$product_diningtable_model->where(array('id'=>$thisOrder['tableid']))->find();
				$thisOrder['tableName']=$thisTable['name'];
			}else{
				$thisOrder['tableName']='未指定';
			}
			$str="订单类型：".$orderType."\r\n订单编号：".$thisOrder['id']."\r\n姓名：".$thisOrder['truename']."\r\n电话：".$thisOrder['tel']."\r\n地址：".$thisOrder['address']."\r\n桌台：".$thisOrder['tableName']."\r\n下单时间：".date('Y-m-d H:i:s',$thisOrder['time'])."\r\n";
			//
			$carts=unserialize($thisOrder['info']);

			//
			$totalFee=0;
			$totalCount=0;
			$products=array();
			$ids=array();
			foreach ($carts as $k=>$c){
				if (is_array($c)){
					$productid=$k;
					$price=$c['price'];
					$count=$c['count'];
					//
					if (!in_array($productid,$ids)){
						array_push($ids,$productid);
					}
					$totalFee+=$price*$count;
					$totalCount+=$count;
				}
			}
			if (count($ids)){
				$products=$this->product_model->where(array('id'=>array('in',$ids)))->select();
			}
			if ($products){
				$i=0;
				foreach ($products as $p){
					$products[$i]['count']=$carts[$p['id']]['count'];
					$str.=$p['name']."  ".$products[$i]['count']."份  单价：".$p['price']."元\r\n";
					$i++;
				}
			}
			$str.="合计：".$thisOrder['price']."元";
			return $str;
		}else {
			return '';
		}
	}

	//增加sms内容止//

	public function orderCart() {
		if (empty($this->wecha_id)) {
			unset($_SESSION[$this->session_cart_name]);
		}
		$setting = M('Product_setting_new')->where(array('token' => $this->token))->find();
		//是否要支付
		$alipayConfig = M('Alipay_config_new')->where(array('token' => $this->token))->find();
		$this->assign('alipayConfig', $alipayConfig);
		$totalCount = $totalFee = 0;
		$data = $this->getCat($this->_getCart());
		if (empty($data[0])) {
			$this->redirect(U('Store/cart', array('token' => $this->token, 'wecha_id' => $this->wecha_id,'dopenid'=>$_GET['dopenid'])));
		}
		if (isset($data[1])) {
			foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
			}
		}
		if (empty($totalCount)) {
			$this->error('没有购买商品!', U('Store_new/cart', array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
		}
		$list = $data[0];
		$this->assign('products', $list);
		$this->assign('totalFee', $totalFee);
		$this->assign('totalCount', $totalCount);
		$this->assign('mailprice', $data[2]);
		$this->assign('metaTitle', '购物车结算');
		$this->display();
	}

	public function my(){
        //var gurl="<php>echo C('site_url');</php>"+"Home/Nofind/isnotsub/token/"+"{weikucms:$token}";
        if(!$this->openid){
            $this->redirect(C('site_url').U('Home/Nofind/isnotsub/',array('token'=>$this->token)));
        }else{
            if(!M("Wxusers")->where(array("openid"=>$this->openid,"uid"=>$this->tpl['id']))->find()){
                $this->redirect(C('site_url').U('Home/Nofind/isnotsub/',array('token'=>$this->token)));
            }
        }
		$offset = 5;
		$page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
		$start = ($page - 1) * $offset;
		$product_cart_model = M('product_cart_new');
		$orders = $product_cart_model->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id, 'groupon' => 0))->limit($start, $offset)->order('time DESC')->select();
		$count = $product_cart_model->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id, 'groupon' => 0))->count();
		$list = array();
		if ($orders){
			foreach ($orders as $o){
				$products = unserialize($o['info']);
				$pids = array_keys($products);
				$o['productInfo'] = array();
				if ($pids) {
					$o['productInfo'] = M('product_new')->where(array('id' => array('in', $pids)))->select();
				}
				$list[] = $o;
			}
		}
		$totalpage = ceil($count / $offset);
		$this->assign('orders', $list);
		$this->assign('ordersCount', $count);
		$this->assign('totalpage', $totalpage);
		$this->assign('page', $page);
		$this->assign('metaTitle', '我的订单');

		//是否要支付
		$alipayConfig = M('Alipay_config_new')->where(array('token' => $this->token))->find();
		$this->assign('alipayConfig',$alipayConfig);
		$this->display();
	}

	public function myDetail(){
		$cartid = isset($_GET['cartid']) && intval($_GET['cartid'])? intval($_GET['cartid']) : 0;
		$product_cart_model = M('product_cart_new');

		$list = array();
		if ($cartObj = $product_cart_model->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'id' => $cartid))->find()){
			$products = unserialize($cartObj['info']);
			$data = $this->getCat($products);
			$pids = array_keys($products);
			$cartObj['productInfo'] = array();
			if ($pids) {
				$cartObj['productInfo'] = M('product_new')->where(array('id' => array('in', $pids)))->select();
			}

			$totalCount = $totalFee = 0;
			if (isset($data[1])) {
				foreach ($data[1] as $pid => $row) {
					$totalCount += $row['total'];
					$totalFee += $row['totalPrice'];
				}
			}
			$list = $data[0];
			$this->assign('products', $list);
			$this->assign('totalFee', $totalFee);
			$this->assign('totalCount', $totalCount);
			$this->assign('mailprice', $data[2]);
			$this->assign('cartData', $cartObj);
			$this->assign('cartid', $cartid);
		}
		$this->assign('metaTitle', '我的订单');
		$this->display();
	}

	public function cancelCart(){
		$cartid = isset($_GET['cartid']) && intval($_GET['cartid'])? intval($_GET['cartid']) : 0;
		$product_model=M('product_new');
		$product_cart_model = M('product_cart_new');
		$product_cart_list_model = M('product_cart_list_new');
		$thisOrder = $product_cart_model->where(array('id'=> $cartid))->find();
		if (empty($thisOrder)) {
			exit(json_encode(array('error_code' => true, 'msg' => '没有此订单')));
		}
		$id = $thisOrder['id'];
		if (empty($thisOrder['paid'])) {
			//删除订单和订单列表
			$product_cart_model->where(array('id' => $cartid))->delete();
			$product_cart_list_model->where(array('cartid' => $cartid))->delete();
			//还原积分
			$userinfo_model = M('Userinfo_new');
			$thisUser = $userinfo_model->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
			$userinfo_model->where(array('id' => $thisUser['id']))->setInc('total_score', $thisOrder['score']);
			F('fans_token_wechaid', NULL);
			//商品销量做相应的减少
			$carts = unserialize($thisOrder['info']);
			//还原库存
			foreach ($carts as $pid => $rowset) {
				$total = 0;
				if (is_array($rowset)) {
					foreach ($rowset as $did => $row) {
						M('product_detail_new')->where(array('id' => $did, 'pid' => $pid))->setInc('num', $row['count']);
						$total += $row['count'];
					}
				} else {
					$total = $rowset;
				}
				$product_model->where(array('id' => $pid))->setInc('num', $total);
				$product_model->where(array('id' => $pid))->setDec('salecount', $total);
			}
			exit(json_encode(array('error_code' => false, 'msg' => '订单取消成功')));
		}
		exit(json_encode(array('error_code' => true, 'msg' => '购买成功的订单不能取消')));
	}

	public function updateOrder(){
		$product_cart_model = M('product_cart_new');
		$thisOrder = $product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		if ($thisOrder['wecha_id']!=$this->wecha_id){
			exit();
		}
		$this->assign('thisOrder',$thisOrder);
		$carts = unserialize($thisOrder['info']);
		$totalCount = $totalFee = 0;
		$listNum = array();
		$data = $this->getCat($carts);
		if (isset($data[1])) {
			foreach ($data[1] as $pid => $row) {
				$totalCount += $row['total'];
				$totalFee += $row['totalPrice'];
				$listNum[$pid] = $row['total'];
			}
		}
		$list = $data[0];
		$this->assign('products', $list);
		$this->assign('totalFee', $totalFee);
		$this->assign('listNum', $listNum);
		$this->assign('metaTitle','修改订单');
		//是否要支付
		$alipayConfig = M('Alipay_config_new')->where(array('token' => $this->token))->find();
		$this->assign('alipayConfig', $alipayConfig);
		$this->display();
	}
	public function deleteOrder(){
		$product_model=M('product_new');
		$product_cart_model=M('product_cart_new');
		$product_cart_list_model=M('product_cart_list_new');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		$id=$thisOrder['id'];
		if ($thisOrder['wecha_id']!=$this->wecha_id||$thisOrder['handled']==1){
			exit();
		}
		//删除订单和订单列表
		$product_cart_model->where(array('id'=>$id))->delete();
		$product_cart_list_model->where(array('cartid'=>$id))->delete();
		//商品销量做相应的减少
		$carts=unserialize($thisOrder['info']);
		foreach ($carts as $k=>$c){
			if (is_array($c)){
				$productid=$k;
				$price=$c['price'];
				$count=$c['count'];
				$product_model->where(array('id'=>$k))->setDec('salecount',$c['count']);
			}
		}
		$this->redirect(U('Store_new/my',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'])));
	}

	/**
	 * 支付成功后的回调函数
	 */
	public function payReturn() {
	   $orderid = $_GET['orderid'];
	   if ($order = M('Product_cart_new')->where(array('orderid' => $orderid, 'token' => $this->token))->find()) {
			//TODO 发货的短信提醒
			if ($order['paid']) {
				$userInfo = D('Userinfo_new')->where(array('token' => $this->token, 'wecha_id' => $this->wecha_id))->find();
				Sms::sendSms($this->token, "您的顾客{$userInfo['truename']}刚刚对订单号：{$orderid}的订单进行了支付，请您注意查看并处理");
			}
			$this->redirect(U('Store_new/my',array('token' => $this->token,'wecha_id' => $this->wecha_id)));
	   }else{
	      exit('订单不存在');
	    }
	}

	//个人中心
	public function userCenter(){
		$yes=M("Product_cart_new")->where(array("wecha_id"=>$this->wecha_id,"paid"=>1))->count();
		$no=M("Product_cart_new")->where(array("wecha_id"=>$this->wecha_id,"paid"=>0))->count();
		$send=M("Product_cart_new")->where(array("wecha_id"=>$this->wecha_id,"sent"=>1))->count();
		$nsend=M("Product_cart_new")->where(array("wecha_id"=>$this->wecha_id,"sent"=>0))->count();
		$userinfo=M("wxusers")->field("nickname,headimgurl")->where(array("openid"=>$this->wecha_id))->find();
		//会员数据
		if($user=M("Usercenter_memberlist")->field("score")->where(array("openid"=>$this->wecha_id))->find()){
			$this->assign("user",$user);
		}

		//本店用户数据
		if($score=M("Userinfo_new")->field("total_score")->where(array("wecha_id"=>$this->wecha_id))->find()){
			$this->assign("score",$score);
		}
        if($change=M("Product_setting_new")->field("score,price,is_distribution")->where(array("token"=>$this->token))->find()){
            $this->assign("change",$change);
        }
		$this->assign("userinfo",$userinfo);
		$this->assign("send",$send);
		$this->assign("nsend",$nsend);
		$this->assign("yes",$yes);
		$this->assign("no",$no);
		$this->display();
	}

	public function orders(){
      //  p($userdata);die;
        $usersModel = M('Wxusers');
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $userdata = $usersModel->where(array('uid'=>$wxuser['id'],'status'=>1,'openid'=>$this->openid))->find();
        if(IS_POST){//提交评论
            $model=M('Product_comment');
            $_POST['addtime']=time();
            $model->create();
            $_POST['name']=$userdata['nickname'];
            $_POST['pic']=$userdata['headimgurl'];
            if($model->add($_POST)){

                $this->ajaxReturn(array("status"=>1));
            }else{

                $this->ajaxReturn(array("status"=>0));
            }

        }

		$act=$this->_get("act","intval");
		if($act==0){//没有支付的订单
			$data=M("Product_cart_new")->field("id,logistics,logisticsid")->where(array("wecha_id"=>$this->wecha_id,"paid"=>0))->order('time desc')->select();
		}
		if($act==1){
			$data=M("Product_cart_new")->field("id,logistics,logisticsid")->where(array("wecha_id"=>$this->wecha_id,"paid"=>1))->order('time desc')->select();
		}
		if($act==2){//所有订单
			$data=M("Product_cart_new")->field("id,logistics,logisticsid")->where(array("wecha_id"=>$this->wecha_id))->order('time desc')->select();
		}
		if($act==3){//已付款，已发货，
			$data=M("Product_cart_new")->field("id,logistics,logisticsid")->where(array("wecha_id"=>$this->wecha_id,"sent"=>1))->order('time desc')->select();
		}
		if($act==4){
			$data=M("Product_cart_new")->field("id,logistics,logisticsid")->where(array("wecha_id"=>$this->wecha_id,"sent"=>0))->order('time desc')->select();
		}
		if(empty($data)){
            $this->assign("data",$data);

		}


		foreach($data as $k=>$v){

			$data[$k]=$this->getinfo($v['id']);
            //加评论
            if($a=M('Product_comment')->where(array('oid'=>$v['id']))->getField('content')){
                $data[$k]['pingjia']=$a;
            }
		}

      // p($data);
		$this->assign("data",$data);
       // p($data);die;
      //  p($data);die;
        /**
         * 得用户图像，名字数据
         */

      // p($userdata);

        $this->assign("userdata",$userdata);
		$this->display();
	}
    /**
     * 第二次去支付，从订单那边过来的
     */
    public function zhifu(){
        $this->display();
    }
    /**
     * 确认收货
     */
    public function queren(){
        $data['sent']=2;
        if(M("Product_cart_new")->where(array("id"=>$this->_get("id","intval")))->save($data)){
            $this->ajaxReturn(array("status"=>1,"info"=>"已确认收货"));
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
        }
    }

    /**
     * 取消订单
     */
	public function cancel(){
		if(M("Product_cart_new")->where(array("id"=>$this->_get("id","intval")))->delete()){
			$this->ajaxReturn(array("status"=>1,"info"=>"订单取消成功"));
		}else{
			$this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
		}
	}

	//支付时，修改订单信息
    public function change(){
        if(M("Product_cart_new")->where(array('id'=>$this->_get("id")))->save(array("sid"=>$this->_get("sid"),'score'=>$this->_get("score")))){
            $this->ajaxReturn(array("status"=>1,"info"=>"订单操作成功"));
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
        }
    }



    public function article(){
        $where['id'] = $this->_get('id', 'intval');
        $where['token'] = $this->token;
        $data = D('Product_new_article')->where($where)->find();
        $this->assign('res', $data);
        $this->display();
    }
    /**
     * 添加购物车
     */
    public function add_cart(){
        if(IS_AJAX) {
           // echo 4;die;
        //  p($_GET);die;
            $data=array();
            $data['pid']=$id = $this->_get('id');
            $data['name']=$name = $this->_get('name');
            $data['price']=$vprice = $this->_get('vprice');
            $data['logourl']=$logourl = $this->_get('logourl');
            $data['num']=$num = $this->_get('num');
            $data['norms_new']=isset($_GET['norms_new'])?$_GET['norms_new']:'';
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            if(!M('Product_cat_shop')->where(array('token'=>$this->token,'openid'=>$this->openid,'pid'=>$_GET['id'],'norms_new'=>$data['norms_new']))->find()){

                M('Product_cat_shop')->add($data);
                echo json_encode(array('status'=>0));
            }else{

                echo json_encode(array('status'=>0));
            }
           // echo $data['pid'];die;
          /*  if(session('?carta')){
                $carta=session('carta');
                if(!array_key_exists($id,$carta)){
                    $carta[$id] = array('id' => $id, 'name' => $name,'vprice'=>$vprice,'logourl'=>$logourl,'num'=>$num,'norms_new'=>$norms_new);
                    $data['status']=0;
                }
            }else{
                $carta[$id] = array('id' => $id, 'name' => $name,'vprice'=>$vprice,'logourl'=>$logourl,'num'=>$num,'norms_new'=>$norms_new);
                $data['status']=0;
            }
          //  $carta=json_encode($carta);
            session('carta', $carta);
         //   p($carta);die;
            echo json_encode($data);*/
        }


    }
    /**
     *  购物车列表
     */

    public function list_cart(){
      //  echo 1;
        $token=$this->token;
        /**
         * 得收货地址
         */
     //   $address=M('Product_address')->where(array('uid'=>$this->openid))->find();
       // $this->assign('address',$address);
      //  $this->assign('data',session("$token"));
        $data=M('Product_cat_shop')->where(array('token'=>$this->token,'openid'=>$this->openid))->select();
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 购物车购买
     */
    public function Buy_cart(){
      // p($_POST);die;
        $productid=$this->_post('pid');
        $productid=explode(',',$productid);
        $num=$this->_post('num');
        $num=explode(',',$num);
        $price_post=$this->_post('price');
        $price_post=explode(',',$price_post);
        $norms=$this->_post('norms');
        $norms=explode(',',$norms);


        $len=count($productid);
        $row['total']=0;
        $total_price=0;
        for($i=0;$i<$len;$i++){//算总价格
          //  $price1=M('Product_new')->where(array('id'=>$productid[$i]))->getField('price');
         //   $total_price=$total_price+$price1*$num[$i];
            $row['total']=$row['total']+$num[$i];
            $total_price=$total_price+$price_post[$i]*$num[$i];
        }
        $row['productid']=$productid[0];
        $row['is_cart']=1;
        $row['orderid'] =substr($this->wecha_id, -1, 4) . date("YmdHis");
        $row['token'] = $this->token;
        $row['wecha_id'] = $this->openid;
        $row['time'] = time();
        //  $row['productid']=$this->_get("did");
        // $row['total']=$this->_get("count");
        $row['type']=$this->_get("type");
        if($this->_get("type")=="son"){//商品套餐
            $data=M("Product_detail_new")->field("pid,price,vprice")->where(array("id"=>$productid[0]))->find();
            $parent=M("Product_new")->field("mailprice")->where(array(array("id"=>$data['pid'])))->find();
            $mailpay=$parent['mailprice'];
        }else{
            $data=M("Product_new")->field("mailprice,price,vprice")->where(array("id"=>$productid[0]))->find();
            $mailpay=$data['mailprice'];
        }
        if(M("Usercenter_memberlist")->where(array("openid"=>$this->wecha_id))->find()){
            //$row['price']=$data['vprice'];
            $row['price']=$total_price;//购物车总价格
        }else{
            //  $row['price']=$data['vprice'];//这里不考虑是否为会员，都用vprice价格
            $row['price']=$total_price;//购物车总价格
        }
        $totalpay=intval($row['total'])*intval($row['price']);
        $open=M("Product_setting_new")->field("price")->where(array("token"=>$this->token))->find();
        if($totalpay>=$open['price'] && $open['price']!=0){
            $row['mailpay']=0;
        }else{
            $row['mailpay']=$mailpay;
        }
        $pid=M("Product_cart_new")->add($row);//添加订单
        $data['pid']=$pid;
        $data['wecha_id']=$this->wecha_id;
        if($addr=M("Product_address")->where(array("uid"=>$this->wecha_id))->find()){
            $save['truename']=$addr['name'];
            $save['tel']=$addr['tel'];
            $save['address']=$addr['seng']."省(市)".$addr['si']."市(区)".$addr['xian']."县".$addr['detail'];
            if(M("Product_cart_new")->where(array("id"=>$pid))->save($save)){
                if($this->dopenid){
                    $is_d = M('Product_setting_new')->where(array('token'=>$this->token))->find();
                    //如果开启了分销
                    if($is_d['is_distribution'] == 1 ){
                        $d['token'] = $this->token;
                        $d['ws_openid'] = $this->dopenid;
                        $wsInfo = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$d['ws_openid']))->find();
                        $d['ws_name'] = $wsInfo['name'];
                        $d['order_price'] = $totalpay;
                        $d['order_id'] = $row['orderid'];
                        $d['order_name'] = $addr['name'];
                        $d['add_time'] = date('Y-m-d H:i:s');
                        if(!M('Homenice_commission')->where(array('order_id'=>$row['orderid']))->find()){
                            $distributionRecorder = M('Homenice_commission')->add($d);
                        }

                    }
                }
                /**
                 * 购物车订单表商品详情
                 *
                 */


                for($i=0;$i<$len;$i++){
                    if($num[$i]>0){
                        $pList=M('Product_new')->field('name,logourl,price')->where(array('id'=>$productid[$i]))->find();
                        $data1['pid']=$productid[$i];
                        $data1['num']=$num[$i];
                        $data1['cid']=$pid;
                        $data1['price']=$price_post[$i];//指定用vip价格
                        $data1['gname']=$pList['name'];
                        $data1['gpic']=$pList['logourl'];
                        $data1['norms']=$norms[$i];//规格
                        M('Product_sidedetail')->add($data1);
                    }

                }
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功","data"=>$data,'pid'=>$pid));
            }
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"请先填写地址","data"=>$data,'pid'=>$pid));
        }
    }
    /**
     * 购物车购买详细页
     */
    public function cart_show(){
        /**
         * 自己的修改
         */
        $pid=$this->_get("pid");//订单id主键

        /**
         * 得地址
         */
        $address=M('Product_cart_new')->field('truename,tel,address,orderid')->where(array('id'=>$pid))->find();
        $this->assign('address',$address);
        $orderid=$address['orderid'];
        $data=M('Product_sidedetail')->where(array('cid'=>$pid))->select();
        $this->assign("orderid",$orderid);
        $this->assign("data",$data);
        // p($data);
        //echo 4;die;
        $this->display();
    }
    /**
     * 购物车取消商品
     */
    public function cancel_cart(){
        if(IS_POST){
          //  p($_POST['id']);die;
         /*   $token=$this->token;
            $id=$this->_post('id');
            $carta=session("carta");
            unset($carta[$id]);
            session("carta",$carta);*/
            if(M('Product_cat_shop')->delete($_POST['id'])){
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));

            }
        }
    }
    /**
     * 购物车过来的邮寄地址
     */
    public function edit_cart(){
        if(IS_POST){
            $address=M('Product_address');
            if(M("Product_address")->where(array("uid"=>$_POST['uid']))->find()){

                if(M("Product_address")->where(array('uid'=>$_POST['uid']))->save($_POST)) {
                //    p($_POST);
                    $data['truename']=$this->_post('name');
                    $data['tel']=$this->_post('tel');
                    $data['address']=$_POST['seng']."省(市)".$_POST['si']."市(区)".$_POST['xian']."县".$_POST['detail'];
                    M('Product_cart_new')->where(array('id'=>$_POST['pid']))->save($data);
                    $this->redirect("Store_shop/cart_show", array("token" => $_POST['token'], "openid" => $_POST['uid'], 'pid' => $_POST['pid'], 'dopenid' => $_POST['dopenid'], 'type' => 2));
                }
            }else{
                $address->create();
                if($address->add()){

                    $this->redirect("Store_shop/list_cart", array("token" => $_POST['token'], "openid" => $_POST['uid'], 'dopenid' => $_POST['dopenid']));

                }
            }
        }else{
            $data=M("Product_address")->where(array("uid"=>$_GET['openid']))->find();
            $this->assign("data",$data);
            $this->assign("uid",$this->_get("uid"));
            $this->assign("id",$this->_get("id"));
            $this->display();
        }
    }
}

?>
