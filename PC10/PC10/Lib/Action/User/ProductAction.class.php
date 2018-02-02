<?php
class ProductAction extends UserAction{
	public $token;
	public $product_model;
	public $product_cat_model;
	public $isDining;
	public function _initialize() {
		parent::_initialize();
		//
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		if((!isset($_GET['dining'])&&!strpos($token_open['queryname'],'shop'))||(isset($_GET['dining'])&&!strpos($token_open['queryname'],'dx'))){
           // $this->error2('您还未开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}
		//是否是餐饮
		if (isset($_GET['dining'])&&intval($_GET['dining'])){
			$this->isDining=1;
		}else {
			$this->isDining=0;
		}

        if ($this->isDining){
            $where['dining']=1;
        }else {
            $where['dining']=0;
        }
        $where['token'] = $this->_session('token');
        $where['handled'] = 0;
        $unorders = M('Product_cart')->where($where)->count();
        $orderdata = M('Product_cart')->where($where)->order('time desc')->find();
        if($orderdata != null){
            $this->assign('lasttime',$orderdata['time']);
        }else{
            $this->assign('lasttime',time());
        }
        $this->assign('unorders',$unorders);

		$this->assign('isDining',$this->isDining);
	}

    public function gettimeorder(){
        if(isset($_GET['lasttime']) && is_numeric($_GET['lasttime'])){
            if ($this->isDining){
                $where['dining']=1;
            }else {
                $where['dining']=0;
            }
            $pmodel = M('Product_cart');
            $where['token'] = $this->_session('token');
            $where['handled'] = 0;

            $where['time'] = array('gt',$_GET['lasttime']);
            $unorders = $pmodel->where($where)->count();
            $neworder = $pmodel->where($where)->order('time desc')->find();
            if($unorders){
                $this->ajaxReturn(array('code'=>0,'msg'=>'success','data'=>$unorders,'lasttime'=>$neworder['time']));
            }else{
                $this->ajaxReturn(array('code'=>0,'msg'=>'success','data'=>0));
            }
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'非法请求'));
        }
    }

	public function index(){		
		$catid=intval($_GET['catid']);
		$catid=$catid==''?0:$catid;
		$product_model=M('Product');
		$product_cat_model=M('Product_cat');
		$where=array('token'=>session('token'));
		if ($catid){
			$where['catid']=$catid;
		}
		$where['dining']=$this->isDining;
		$where['groupon']=array('neq',1);
        if(IS_POST){
            $key = $this->_post('searchkey');
            if(empty($key)){
                $this->error2("关键词不能为空");
            }

            $map['token'] = $this->get('token'); 
            $map['name|intro|keyword'] = array('like',"%$key%"); 
            $list = $product_model->where($map)->select(); 
            $count      = $product_model->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
        }else{
        	$count      = $product_model->where($where)->count();
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
        	$list = $product_model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        }
		$this->assign('page',$show);		
		$this->assign('list',$list);
		$this->assign('isProductPage',1);
		
		$this->display();		
	}
	public function cats(){		
		/*
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();

		if(!strpos($token_open['queryname'],'adma')){
            $this->error2('您还未开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));}
		 */
		$parentid=intval($_GET['parentid']);
		$parentid=$parentid==''?0:$parentid;
		$data=M('Product_cat');
		$where=array('parentid'=>$parentid,'token'=>session('token'));
		$where['dining']=$this->isDining;
        if(IS_POST){
            $key = $this->_post('searchkey');
            if(empty($key)){
                $this->error2("关键词不能为空");
            }

            $map['token'] = $this->get('token'); 
            $map['name|des'] = array('like',"%$key%"); 
            $list = $data->where($map)->select(); 
            $count      = $data->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
        }else{
        	$count      = $data->where($where)->count();
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
        	$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        }
		$this->assign('page',$show);		
		$this->assign('list',$list);
		if ($parentid){
			$parentCat = $data->where(array('id'=>$parentid))->find();
		}
		$this->assign('parentCat',$parentCat);
		$this->assign('parentid',$parentid);
		$this->display();		
	}
	public function catAdd(){ 
		if(IS_POST){
			if ($this->isDining){
				$this->insert('Product_cat','/cats?dining=1&parentid='.$this->_post('parentid'));
			}else {
			$this->insert('Product_cat','/cats?parentid='.$this->_post('parentid'));
			}
		}else{
			$parentid=intval($_GET['parentid']);
			$parentid=$parentid==''?0:$parentid;
			$this->assign('parentid',$parentid);
			$this->display('catSet');
		}
	}
	public function catDel(){
		if($this->_get('token')!=session('token')){$this->error2('非法操作');}
        $id = $this->_get('id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Product_cat');
            $check=$data->where($where)->find();
            if($check==false)   $this->error2('非法操作');
            $product_model=M('Product');
            $productsOfCat=$product_model->where(array('catid'=>$id))->select;
            if (count($productsOfCat)){
            	$this->error2('本分类下有商品，请删除商品后再删除分类',U('Product/cats',array('token'=>session('token'),'dining'=>$this->isDining)));
            }
            $back=$data->where($wehre)->delete();
            if($back==true){
            	if (!$this->isDining){
                $this->success2('操作成功',U('Product/cats',array('token'=>session('token'),'parentid'=>$check['parentid'])));
            	}else {
            		$this->success2('操作成功',U('Product/cats',array('token'=>session('token'),'parentid'=>$check['parentid'],'dining'=>1)));
            	}
            }else{
                 $this->error2('服务器繁忙,请稍后再试',U('Product/cats',array('token'=>session('token'))));
            }
        }        
	}
	public function catSet(){
        $id = $this->_get('id'); 
		$checkdata = M('Product_cat')->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error2("没有相应记录.您现在可以添加.",U('Product/catAdd'));
        }
		if(IS_POST){ 
            $data=D('Product_cat');
            $where=array('id'=>$this->_post('id'),'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error2('非法操作');
			if($data->create()){
				if($data->where($where)->save($_POST)){
					if (!$this->isDining){
						$this->success2('修改成功',U('Product/cats',array('token'=>session('token'),'parentid'=>$this->_post('parentid'))));
					}else {
						$this->success2('修改成功',U('Product/cats',array('token'=>session('token'),'parentid'=>$this->_post('parentid'),'dining'=>1)));
					}
					
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($data->geterror2());
			}
		}else{ 
			$this->assign('parentid',$checkdata['parentid']);
			$this->assign('set',$checkdata);
			$this->display();	
		
		}
	}
	public function add(){ 
		if(IS_POST){
			$this->all_insert2('Product','/index?token='.session('token').'&dining='.$this->isDining);
		}else{
			//分类
			$data=M('Product_cat');
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			if ($this->isDining){
				$catWhere['dining']=1;
			}else {
				$catWhere['dining']=0;
			}
			$cats=$data->where($catWhere)->select();
			if (!$cats){
				 $this->error2("请先添加分类",U('Product/catAdd',array('token'=>session('token'),'dining'=>$this->isDining)));
				 exit();
			}
			$this->assign('cats',$cats);
			$catsOptions=$this->catOptions($cats,0);
			$this->assign('catsOptions',$catsOptions);
			//
			$this->assign('isProductPage',1);
			$this->display('set');
		}
	}
	/**
	 * 商品类别ajax select
	 *
	 */
	public function ajaxCatOptions(){
		$parentid=intval($_GET['parentid']);
		$data=M('Product_cat');
		$catWhere=array('parentid'=>$parentid,'token'=>session('token'));
		$cats=$data->where($catWhere)->select();
		$str='';
		if ($cats){
			foreach ($cats as $c){
				$str.='<option value="'.$c['id'].'">'.$c['name'].'</option>';
			}
		}
		$this->show($str);
	}
	public function set(){
        $id = $this->_get('id'); 
        $product_model=M('Product');
        $product_cat_model=M('Product_cat');
		$checkdata = $product_model->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error2("没有相应记录.您现在可以添加.",U('Product/add'));
        }
		if(IS_POST){ 
            $where=array('id'=>$this->_post('id'),'token'=>session('token'));
			$check=$product_model->where($where)->find();
			if($check==false)$this->error2('非法操作');
			if($product_model->create()){
				if($product_model->where($where)->save($_POST)){
					$this->success2('修改成功',U('Product/index',array('token'=>session('token'),'dining'=>$this->isDining)));
					$keyword_model=M('Keyword');
					$keyword_model->where(array('token'=>session('token'),'pid'=>$this->_post('id'),'module'=>'Product'))->save(array('keyword'=>$this->_post('keyword')));
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($product_model->geterror2());
			}
		}else{
			//分类
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			if ($this->isDining){
				$catWhere['dining']=1;
			}
			$cats=$product_cat_model->where($catWhere)->select();
			$this->assign('cats',$cats);
			
			$thisCat=$product_cat_model->where(array('id'=>$checkdata['catid']))->find();
			$childCats=$product_cat_model->where(array('parentid'=>$thisCat['parentid']))->select();
			$this->assign('thisCat',$thisCat);
			$this->assign('parentCatid',$thisCat['parentid']);
			$this->assign('childCats',$childCats);
			$this->assign('isUpdate',1);
			$catsOptions=$this->catOptions($cats,$checkdata['catid']);
			$childCatsOptions=$this->catOptions($childCats,$thisCat['id']);
			$this->assign('catsOptions',$catsOptions);
			$this->assign('childCatsOptions',$childCatsOptions);
			//
			$this->assign('set',$checkdata);
			$this->assign('isProductPage',1);
			$this->display();	
		
		}
	}
	//商品类别下拉列表
	public function catOptions($cats,$selectedid){
		$str='';
		if ($cats){
			foreach ($cats as $c){
				$selected='';
				if ($c['id']==$selectedid){
					$selected=' selected';
				}
				$str.='<option value="'.$c['id'].'"'.$selected.'>'.$c['name'].'</option>';
			}
		}
		return $str;
	}
	public function del(){
		$product_model=M('Product');
		if($this->_get('token')!=session('token')){$this->error2('非法操作');}
        $id = $this->_get('id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $check=$product_model->where($where)->find();
            if($check==false)   $this->error2('非法操作');

            $back=$product_model->where($where)->delete();
            if($back==true){
            	$keyword_model=M('Keyword');
            	$keyword_model->where(array('token'=>session('token'),'pid'=>$id,'module'=>'Product'))->delete();
                $this->success2('操作成功',U('Product/index',array('token'=>session('token'),'dining'=>$this->isDining)));
            }else{
                 $this->error2('服务器繁忙,请稍后再试',U('Product/index',array('token'=>session('token'))));
            }
        }        
	}
	public function orders(){
		$product_cart_model=M('product_cart');
		if (IS_POST){
			if ($_POST['token']!=$this->_session('token')){
				exit();
			}
			for ($i=0;$i<40;$i++){
				if (isset($_POST['id_'.$i])){
					$thiCartInfo=$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->find();
					if ($thiCartInfo['handled']){
					$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>0));
					}else {
						$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>1));
					}
				}
			}
			$this->success2('操作成功',U('Product/orders',array('token'=>session('token'),'dining'=>$this->isDining)));
		}else{
			

			$where=array('token'=>$this->token);
			if ($this->isDining){
				$where['dining']=1;
			}else {
				$where['dining']=0;
			}

            if(isset($_GET['id']) && !empty($_GET['id'])){
                $where['tableid']= $_GET['id'];
                $this->assign('tableid',$_GET['id']);
            }

            if($_GET['start_date'] && $_GET['end_date']){
                $start_date = $_GET['start_date']." 00:00:00";
                $end_date = $_GET['end_date']." 23:59:59";
                $where['time']=array('between',array(strtotime($start_date),strtotime($end_date)));
                $this->assign('start_date',$_GET['start_date']);
                $this->assign('end_date',$_GET['end_date']);
            }


			$where['groupon']=array('neq',1);
			if(IS_POST){
				$key = $this->_post('searchkey');
				if(empty($key)){
					$this->error2("关键词不能为空");
				}

				$where['truename|address'] = array('like',"%$key%");
				$orders = $product_cart_model->where($where)->select();
				$count      = $product_cart_model->where($where)->limit($Page->firstRow.','.$Page->listRows)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
			}else {
				if (isset($_GET['handled'])){
					$where['handled']=intval($_GET['handled']);
				}
				$count      = $product_cart_model->where($where)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
                //$where['tableid'] = $this->_get('id','intval');

//                $condition['token'] = $this->_get('token');
				$orders=$product_cart_model->where($where)->order('time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
//                print_r($condition);exit;
//                echo $where['tableid'];exit;
			}


			$unHandledCount=$product_cart_model->where(array('token'=>$this->_session('token'),'handled'=>0))->count();
			$this->assign('unhandledCount',$unHandledCount);

           // print_r($orders);exit;
			$this->assign('orders',$orders);

			$this->assign('page',$show);
			$this->display();
		}
	}
	public function orderInfo(){
		$this->product_model=M('Product');
		$this->product_cat_model=M('Product_cat');
		$product_cart_model=M('product_cart');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		if (strtolower($thisOrder['token'])!=strtolower($this->_session('token'))){
			exit();
		}
		if (IS_POST){
			if (intval($_POST['sent'])){
				$_POST['handled']=1;
			}
			$product_cart_model->where(array('id'=>$thisOrder['id']))->save(array('sent'=>intval($_POST['sent']),'logistics'=>$_POST['logistics'],'logisticsid'=>$_POST['logisticsid'],'handled'=>1,'paid'=>1));
            if($_POST['sent']){
                 $notichcontent = $this->wxusers['nickname']."您好".$this->tpl['name']."订单成功,您的订单号:".$_GET['id'];
            }else{
                 $notichcontent = $this->wxusers['nickname']."您好".$this->tpl['name']."订单失败,您的订单号:".$_GET['id'].",详情电话：184-8996-6261";
            }
            $postdata = array('openid'=>$thisOrder['wecha_id'],'token'=>$this->token,'content'=>$notichcontent);
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
            $data = $this->api_notice_increment($url,http_build_query($postdata));

            if(!$data){
                $this->api_notice_increment($url,http_build_query($postdata));
            }

			$this->success2('修改成功',U('Product/orderInfo',array('token'=>session('token'),'id'=>$thisOrder['id'])));
		}else {
			//订餐信息
			$product_diningtable_model=M('product_diningtable');
			if ($thisOrder['tableid']) {
				$thisTable=$product_diningtable_model->where(array('id'=>$thisOrder['tableid']))->find();
				$thisOrder['tableName']=$thisTable['name'];
			}
			//
			$this->assign('thisOrder',$thisOrder);
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
				$list=$this->product_model->where(array('id'=>array('in',$ids)))->select();
			}
			if ($list){
				$i=0;
				foreach ($list as $p){
					$list[$i]['count']=$carts[$p['id']]['count'];
					$i++;
				}
			}
			$this->assign('products',$list);
			//
			$this->assign('totalFee',$totalFee);
			$this->display();
		}
	}
	public function deleteOrder(){
		$product_model=M('product');
		$product_cart_model=M('product_cart');
		$product_cart_list_model=M('product_cart_list');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		$id=$thisOrder['id'];
		if ($thisOrder['token']!=$this->_session('token')){
			exit();
		}
		//
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
		$this->success2('操作成功',$_SERVER['HTTP_REFERER']);
	}
	//桌台管理
	public function tables(){
		$product_diningtable_model=M('product_diningtable');
		if (IS_POST){
			if ($_POST['token']!=$this->_session('token')){
				exit();
			}
			for ($i=0;$i<40;$i++){
				if (isset($_POST['id_'.$i])){
					$thiCartInfo=$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->find();
					if ($thiCartInfo['handled']){
					$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>0));
					}else {
						$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>1));
					}
				}
			}
			$this->success2('操作成功',U('Product/orders',array('token'=>session('token'))));
		}else{
			

			$where=array('token'=>$this->_session('token'));
			if(IS_POST){
				$key = $this->_post('searchkey');
				if(empty($key)){
					$this->error2("关键词不能为空");
				}

				$where['truename|address'] = array('like',"%$key%");
				$orders = $product_cart_model->where($where)->select();
				$count      = $product_cart_model->where($where)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
			}else {
				
				$count      = $product_diningtable_model->where($where)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
				$tables=$product_diningtable_model->where($where)->order('taxis ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
			}

			$this->assign('tables',$tables);
			$this->assign('page',$show);
			$this->display();
		}
	}
	public function tableAdd(){ 
		if(IS_POST){
			$this->insert('Product_diningtable','/tables?dining=1');
		}else{
            $this->assign('isUpdate',0);
			$this->display('tpl/User/default/Product_tableSet.html');
		}
	}
	public function tableSet(){
		$product_diningtable_model=M('product_diningtable');
        $id = $this->_get('id'); 
		$checkdata = $product_diningtable_model->where(array('id'=>$id))->find();
		if(IS_POST){ 
            $where=array('id'=>$this->_post('id'),'token'=>session('token'));
			$check=$product_diningtable_model->where($where)->find();
			if($check==false)$this->error2('非法操作');
			if($product_diningtable_model->create()){
				if($product_diningtable_model->where($where)->save($_POST)){
					$this->success2('修改成功',U('Product/tables',array('token'=>session('token'),'dining'=>1)));
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($data->geterror2());
			}
		}else{
			$this->assign('set',$checkdata);
			$this->display();	
		
		}
	}
	public function tableDel(){
		if($_REQUEST['token']!=session('token')){$this->error2('非法操作');}
        $id = $_REQUEST['id'];
        if($id){
            $where=array('id'=>$id,'token'=>session('token'));
            $product_diningtable_model=M('product_diningtable');
            $check=$product_diningtable_model->where($where)->find();
            if($check==false)   $this->error2('非法操作');
           
            $back=$product_diningtable_model->where($where)->delete();
            if($back==true){
            	$this->success2('操作成功',U('Product/tables',array('token'=>session('token'),'dining'=>1)));
            }else{
                 $this->error2('服务器繁忙,请稍后再试',U('Product/tables',array('token'=>session('token'),'dining'=>1)));
            }
        }        
	}
    //生成二维码显示界面(待定。。。。。。)
    public function create(){
        $id=$this->_get('id','intval');
        $token=$this->_get('token');
        $db=M('Product_diningtable');

        $result=$db->where(array('id'=>$id,'token'=>$token))->find();

       // $res=M('Spread_user')->where(array('sid'=>$id,'token'=>$token))->find();

        $parament = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 121'.$result['id'].'}}}';
       // print_r($parament);exit();

        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
       // print_r($api);exit();
        if($api){
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
            $json = json_decode(file_get_contents($url_get));
            $access_token = $json->access_token;
            $imgSource = $this->creatTicket($access_token, $parament);

        }


//        $this->assign('result' ,$res);

        $this->assign('res', $result);
        $this->assign('imgUrl', $imgSource['header']['url']);
        $this->display();
    }


    public function creatTicket($token, $parament) {

        /*发送数据到微信服务器端并获取数据*/
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
        $result = $this->api_notice_increment($url, $parament);
//        print_r($result);exit();
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
}


?>