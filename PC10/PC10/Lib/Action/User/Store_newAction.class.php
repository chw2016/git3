<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/4
 * Time: 9:11
 */
class Store_newAction extends UserAction {

    static public $treeList = array();

    function setHeader(){
        $aHeader = array(
            0   => array(
                'class'         => 'tab_nav first js_top',
                'data-id'       => 'total',
                'data-index'    => 0,
                'href'          => U('Store_new/index',array('token'=>$this->token)),
                'name'          => '商品分类管理'
            ),
            1   => array(
                'class'         => 'tab_nav first js_top',
                'data-id'       => 'total',
                'data-index'    => 0,
                'href'          => U('Store_new/product',array('token'=>$this->token)),
                'name'          => '商品管理'
            ),
            2   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/orders',array('token'=>$this->token)),
                'name'  => '订单管理'
            ),
            3   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/setting',array('token'=>$this->token)),
                'name'  => '商城设置'
            ),
            4   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Shoptmpls/index',array('token'=>$this->token)),
                'name'  => '商城模板选择'
            ),
            5   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/articleindexfl',array('token'=>$this->token)),
                'name'  => '商城文章'
            ),
            6   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/flashindex',array('token'=>$this->token)),
                'name'  => '广告轮播'
            ),
            7   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/sn',array('token'=>$this->token)),
                'name'  => '商城优惠券'
            ),
            8   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Weipay/index',array('token'=>$this->token)),
                'name'  => '在线支付设置'
            ),
            9   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/analyse',array('token'=>$this->token)),
                'name'  => '数据分析'
            ),
            10   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/gg',array('token'=>$this->token)),
                'name'  => '商城广告'
            ),
            11   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/supplier',array('token'=>$this->token)),
                'name'  => '供货商管理'
            ),
            12   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/yj',array('token'=>$this->token)),
                'name'  => '佣金管理'
            ),
            13   => array(
                'class' => 'tab_nav  js_top sub',
                'data-id' => 'today',
                'data-index' => 1,
                'href'  => U('Store_new/tixianinfo',array('token'=>$this->token)),
                'name'  => '提现管理'
            ),
        );
        switch ($this->token) {
            case 'a2a2cd98f8da2729ab743d64d0b08f0b'://万科
                $aH = array(0);
                break;

            case '7713e3f8a43af2ab6ab16c183274a979';//由你定
                $aH = array(0);
                break;
            /*case '5d8a87bab30de695954b17fc835b9d12';//由你定
                $aH = array(0);
                break;*/
            default:
                $aH = null;
                break;
        }
        if ($aH) {
            $aRet = array();
            foreach ($aHeader as $k => $v) {
                if (in_array($k , $aH)) {
                    $aRet[] = $v;
                }
            }
        }else{
            $aRet = $aHeader;
        }
        $this->assign('header', $aRet);
    }

    #显示分类管理主页面
    public function index(){
        $this->setHeader();
        $data = M('Product_cat_new');
        $where = array('token' => session('token'));
//        if (IS_POST) {
//            $key = $this->_post('searchkey');
//            if(empty($key)){
//                $this->error("关键词不能为空");
//            }
//
//            $map['token'] = $this->get('token');
//            $map['name|des'] = array('like',"%$key%");
//            $list = $data->where($map)->select();
//            $count      = $data->where($map)->count();
//            $Page       = new Page($count,20);
//            $show       = $Page->show();
//        } else {
//            $count      = $data->where($where)->count();
//            $Page       = new Page($count,20);
//            $show       = $Page->show();
            $list = $data->where($where)->order('parentid desc,sort asc')->select();
 //       }
//order('pid asc')
//        $this->assign('page',$show);
     // print_r(count($list));

        $dataD = self::tree($list);
     //   print_r($dataD);exit;
        $this->assign('list',$dataD);
  //      $this->assign('list',$list);
//        if ($parentid){
//            $parentCat = $data->where(array('id'=>$parentid))->find();
//        }
//        $this->assign('parentCat',$parentCat);
//        $this->assign('parentid',$parentid);

        $this->display();

    }
    #新增分类管理
    public function catAdd(){
//        $where['token'] = session('token');
//        $where['id'] = $_GET['id'];
//        $m = M('Product_cat_new');
//        if($_GET['id']){
//            $conn = $m->where($where)->find();
//            $this->assign('set',$conn);
//        }
        $token = session('token');
        $where['token']=$token;

        $getD=M('Product_cat_new')->where($where)->order('parentid desc')->select();

        $data = self::tree($getD);
        //print_r($data);exit;
        $this->assign('info', $data);
        $this->display();
    }
    public function catupdate(){
        $m = M('Product_cat_new');
        $where['token'] = session('token');
        $where['id'] = $_GET['id'];
        $data = array('token' => session('token'),
            'url'       => $this->_post('url'),
            'cattype'   => $this->_post('cattype'),
            'name'      => $this->_post('name'),
            'logourl'   => $this->_post('logourl'),
            'norms'     => $this->_post('norms'),
            'color'     => $this->_post('color'),
            'des'       => $this->_post('des'),
            'time'      => $this->_post('time'),
            'gg_pic'    => $this->_post('gg_pic'),
            'label'     => $this->_post('label'),
            'sort'      => $this->_post('sort'),
            'label_pic' => $this->_post('label_pic'),
            'parentid'  => $this->_post('parentid'));//{name:name,logourl:logourl,norms:norms,color:color,des:des},

            $result = $m->data($data)->add();
        //print_r($result);exit();
            if($result){
                $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$data['token'])));
            }else{
                $this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>$data['token'])));
        }
    }



    public function catset(){
        $this->assign('get',$_GET);
        $where['token'] = session('token');
        $where['id'] = $_GET['id'];
        $m = M('Product_cat_new');
        $conn = $m->where($where)->find();
        $res=$m->where(array('token'=>$where['token']))->select();
        $data = self::tree($res);
        $this->assign('info', $data);
      // p($conn);die;
        $pname=M('Product_cat_new')->where(array('id'=>$conn['parentid']))->getField('name');
        //echo $pname;die;
        $this->assign('set',$conn);
        $this->assign('pname',$pname);
        $this->display();

//        $this->display();
    }
    public function catsave(){

        $m = M('product_cat_new');
        /*$where['token'] = $_GET['token'];
        $where['id'] = $_GET['id'];*/

        $data = array(
            "url" => $this->_post('url'),
            "cattype" => $this->_post('cattype'),
            "name" => $this->_post('name'),
            "logourl" => $this->_post('logourl'),
            "norms" => $this->_post('norms'),
            "color" => $this->_post('color'),
            "des" => $this->_post('des'),
            "time" => $this->_post('time'),
            'gg_pic'=>$this->_post('gg_pic'),
            "parentid" => $this->_post('parentid'),
            'label'=>$this->_post('label'),
            'label_pic'=>$this->_post('label_pic'),
            'sort'=>$this->_post('sort'),
        );
        //echo $this->_post('id');exit;
        //print_r($_POST);exit;

        $result = $m->where(array('token'=>$this->_get('token'),'id'=>$this->_post('id')))->save($data);

        if($result){
            $this->success('修改成功！',U(MODULE_NAME.'/index',array('token'=>$this->_get('token'))));
        }else{
            $this->error('修改失败！',U(MODULE_NAME.'/index',array('token'=>$this->_get('token'))));
        }

    }

    static public function tree(&$data,$parentid = 0,$count = 1) {
        foreach ($data as $key => $value){
            if($value['parentid']==$parentid){
                $value['Count'] = $count;
                self::$treeList []=$value;
                unset($data[$key]);
                self::tree($data,$value['id'],$count+1);
            }
        }
        return self::$treeList ;
    }
   /* static public function tree(&$data,$pid = 0,$count = 1) {
        foreach ($data as $key => $value){
            if($value['pid']==$pid){
                $value['Count'] = $count;
                self::$treeList []=$value;
                unset($data[$key]);
                self::tree($data,$value['id'],$count+1);
            }
        }
        return self::$treeList ;
    }

    /**
     * 删除分类
     */
    public function catDel() {
        if($this->_get('token')!=session('token')){$this->error('非法操作');}
        $id = $this->_get('id');

            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Product_cat_new');
            $check=$data->where($where)->find();

            $back=$data->where($where)->delete();
            $shop=M("Product_new")->where(array("catid"=>$id))->delete();
            if($back==true || $shop==true){

                    $this->success('操作成功',U('Store_new/index',array('token'=>session('token'),'parentid'=>$check['parentid'])));

            }else{
                $this->error('服务器繁忙,请稍后再试',U('Store_new/index',array('token'=>session('token'))));
            }

    }


    /**
     * 分类属性列表
     */
    public function norms() {
        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $catid = intval($_GET['catid']);
        if($checkdata = M('Product_cat_new')->where(array('id' => $catid, 'token' => session('token')))->find()){
            $this->assign('catData', $checkdata);
        } else {
            $this->error("没有选择相应的分类.",U('Store_new/index'));
        }

        $data = M('Norms_new');
        $where = array('catid' => $catid, 'type' => $type);
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
       // print_r($list);exit;
        //p($list);
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('catid', $catid);
        $this->assign('type', $type);
        $this->display();
    }

    /**
     * 分类规格的操作
     */
    public function normsAdd() {
         $type = intval($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
        if($data = M('Product_cat_new')->where(array('id' => $this->_get('catid'), 'token' => session('token')))->find()){
            $this->assign('catData', $data);
        } else {
            $this->error("没有选择相应的分类.", U('Store_new/index'));
        }
        if (IS_POST) {
            $data = D('Norms_new');
//            print_r($_POST);exit;
            $id = intval($this->_post('id'));
            if ($id) {
                $where = array('id' => $id, 'type' =>$this->_get('type'), 'catid' => $this->_get('catid'));
               // $check = $data->where($where)->find();
              //  if ($check == false) $this->error('非法操作');
           // }
           // if ($data->create()) {
                if ($data->where($where)->save($_POST)) {
                    $this->success('修改成功', U('Store_new/norms',array('token' => session('token'), 'catid' => $this->_get('catid'), 'type' => $type)));
                } else {
                    $this->error('操作失败');
                }
            } else {
                if ($data->add($_POST)) {
                    $this->success('添加成功', U('Store_new/norms',array('token' => session('token'), 'catid' => $this->_get('catid'), 'type' => $type)));
                } else {
                    $this->error('操作失败');
                }
            }
           // } else {
                $this->error($data->getError());
            }
         else {
            $data = M('Norms_new')->where(array('id' => $this->_get('id'), 'type' => $type, 'catid' => $this->_get('catid')))->find();
            //print_r($data);die;
            $this->assign('catid', $this->_get('catid'));
            $this->assign('type', $type);
            $this->assign('token', session('token'));
            $this->assign('set', $data);
            $this->display();
        }
    }

    /**
     *属性的删除
     */
    public function normsDel() {
       if($_REQUEST['token'] != session('token')){$this->error('非法操作');}
        $id = intval($_REQUEST['id']);
        $catid = intval($_REQUEST['catid']);
        $type = intval($_REQUEST['type']);
        if(IS_POST){
            $where = array('id' => $id, 'type' => $type, 'catid' => $catid);
            $data = M('norms_new');
            $check = $data->where($where)->find();
            if($check == false) $this->error('非法操作');
            $back = $data->where($where)->delete();
            //print_r($back);exit;
            if ($back) {
                $this->success('操作成功！',U(MODULE_NAME.'/norms',array('type' => $type, 'catid' => $check['catid'])));
            }else{
                $this->error('操作失败！',U(MODULE_NAME.'/norms',array('type' => $type, 'catid' => $check['catid'])));
            }

        }
    }

    /**
     * 分类属性列表
     */
    public function attributes() {
        $catid = intval($_GET['catid']);
        //print_r($catid);exit;
        $checkdata = M('Product_cat_new')->where(array('id' => $catid, 'token' => session('token')))->find();
        $this->assign('catData', $checkdata);
        $data = M('Attribute_new');
        $where = array('catid' => $catid, 'token' => session('token'));
        //print_r($where);exit;
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->assign('catid', $catid);
        $this->display();
    }

    /**
     * 分类属性的操作
     */
    public function attributeAdd() {
       if($checkdata = M('Product_cat_new')->where(array('id' => $_REQUEST['catid'], 'token' => session('token')))->find())
        {
            //print_r($checkdata);exit;
            $this->assign('catData', $checkdata);
         } else {
            $this->error("没有选择相应的分类.", U('Store_new/index'));
            $this->error("没有选择相应的分类.", U('Store_new/index'));
        }
        if (IS_POST) {
          // print_r($_POST);exit;
            $data = D('Attribute_new');
            $id = intval($this->_post('id'));
            $catid = intval($this->_post('catid'));
          //  if ($id) {
                $where = array('id' => $id, 'token' => session('token'), 'catid' => $catid);
              //  $check = $data->where($where)->find();
             //   if ($check == false) $this->error('非法操作');
            //}
           // if ($data->create()) {
                if ($id) {
                    if ($data->where($where)->save($_POST)) {
                        $this->success('修改成功', U('Store_new/attributes',array('token' => session('token'), 'catid' => $this->_post('catid'))));
                    } else {
                        $this->error('操作失败');
                    }
                } else {
                    if ($data->add($_POST)) {
                        $this->success('添加成功', U('Store_new/attributes',array('token' => session('token'), 'catid' => $this->_post('catid'))));
                    } else {
                        $this->error('操作失败');
                    }
                }
            //} else {
                $this->error($data->getError());
            }
       // } else {
            $data = M('Attribute_new')->where(array('id' => $_REQUEST['id'], 'token' => session('token'), 'catid' => $_REQUEST['catid']))->find();
            $this->assign('catid', $this->_get('catid'));
            $this->assign('token', session('token'));
            $this->assign('set', $data);
            $this->display();
       // }
    }

    /**
     *属性的删除
     */
    public function attributeDel() {
        if($_REQUEST['token'] != session('token')){$this->error('非法操作');}
        $id = intval($_REQUEST['id']);
        $catid = intval($_REQUEST['catid']);
        if(IS_POST){
            $where = array('id' => $id, 'token' => session('token'), 'catid' => $catid);
            $data = M('Attribute_new');
            $check = $data->where($where)->find();
            if($check == false) $this->error('非法操作');
            if ($back = $data->where($where)->delete()) {
                $this->success('操作成功',U('Store_new/attributes', array('token' => session('token'), 'catid' => $catid)));
            } else {
                $this->error('服务器繁忙,请稍后再试',U('Store_new/attributes', array('token' => session('token'), 'catid' => $catid)));
            }
        }
    }

    /**
     * 商品列表
     */
    public function product() {
        $catid = intval($_GET['catid']);
        $product_model = M('Product_new');
        $product_cat_model = M('Product_cat_new');
        $where = array('token' => session('token'), 'groupon' => 0);
        if ($catid){
            $where['catid'] = $catid;
        }
        if(IS_POST){
            $catsid = intval($_GET['catid']);
           if($catsid){
                $map['catid'] = $catsid;
               // print_r($map['catid']);exit;
                $map['name'] = array('like',"%".$_POST['name']."%");
                $map['token'] = $this->get('token');
                $list = $product_model->where($map)->select();
               foreach($list as $ik=>$aVal){
                   $supplier = M('Product_supplier')->where(array('id'=>$aVal['sid']))->find();
                   $list[$ik]['sname'] = $supplier['username'];
               }
                $count      = $product_model->where($map)->count();
                $Page       = new Page($count,20);
                $show       = $Page->show();
                $this->assign('name',$_POST['name']);
           }else{
               $map['name'] = array('like',"%".$_POST['name']."%");
               $map['token'] = $this->get('token');
               $list = $product_model->where($map)->select();
               foreach($list as $ik=>$aVal){
                   $supplier = M('Product_supplier')->where(array('id'=>$aVal['sid']))->find();
                   $list[$ik]['sname'] = $supplier['username'];
               }
               $count      = $product_model->where($map)->count();
               $Page       = new Page($count,20);
               $show       = $Page->show();
               $this->assign('name',$_POST['name']);
           }
        } else {
            $count      = $product_model->where($where)->count();
            $Page       = new Page($count,20);
            $show       = $Page->show();
            $list = $product_model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($list as $ik=>$aVal){
                $supplier = M('Product_supplier')->where(array('id'=>$aVal['sid']))->find();
                $list[$ik]['sname'] = $supplier['username'];
            }
        }
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('isProductPage',1);
        $this->assign('catid', $catid);
        $this->display();
    }
    /**
     * 商品评价
     */
    public function pj(){
        $pid=$this->_get('id');
        $where['pid']=$pid;
        // $list=M('Product_comment')->where(array('pid'=>$pid))->select();
        $count = M('Product_comment')->where($where)->count();
        $page = new Page($count,20);
        $orders = M('Product_comment')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('list', $orders);
         $this->assign('page', $page->show());
        $catid=$this->_get('catid');
        $this->assign('catid', $catid);
        $name=$this->_get('name');
        $this->assign('name', $name);
        $this->display();
    }
    #添加商品
    /**
     * 添加商品
     */
    public function addNew() {
        $catid = intval($_GET['catid']);
        $id = intval($_GET['id']);
        if($productCatData = M('Product_cat_new')->where(array('id' => $catid, 'token' => session('token')))->find()){
            $this->assign('catData', $productCatData);
        } else {
            $this->error("没有选择相应的分类.", U('Store_new/index'));
        }
        //产品的规格

        $normsData = M("Norms_new")->where(array('catid' => $catid))->select();
        //p($normsData);
        $colorData = $formatData = array();
        foreach ($normsData as $row) {
            if ($row['type']) {
                $colorData[] = $row;
            } else {
                $formatData[] = $row;
            }
            $normsList[$row['id']] = $row['value'];
        }
        if ($id && ($product = M('Product_new')->where(array('catid' => $catid, 'token' => session('token'), 'id' => $id))->find())) {
            $attributeData = M("Product_attribute_new")->where(array('pid' => $id))->select();
            $productDetailData = M("Product_detail_new")->where(array('pid' => $id))->select();
            $productimage = M("Product_image_new")->where(array('pid' => $id))->select();
            $colorList = $formatList = $pData = array();
            foreach ($productDetailData as $p) {
                $p['formatName'] = $normsList[$p['format']];
                $p['colorName'] = $normsList[$p['color']];
                $formatList[] = $p['format'];
                $colorList[] = $p['color'];
                $pData[] = $p;
            }
            $travelInfo = json_decode($product['tourInfo'],true);
            //print_r($travelInfo);exit;
            $product['chufadi'] = $travelInfo['starting'];
            $product['mudidi'] = $travelInfo['destination'];
            $product['riqi'] = $travelInfo['fatDate'];
            $this->assign('set', $product);
            $this->assign('formatList', $formatList);
            $this->assign('colorList', $colorList);
            $this->assign('imageList', $productimage);
            //print_r($productimage);die;
        } else {
            //分类产品的属性
            $data = M("Attribute_new")->where(array('catid' => $catid))->select();
            $attributeData = array();
            foreach ($data as $row) {
                $row['aid'] = $row['id'];
                $row['id'] = 0;
                $attributeData[] = $row;
            }
        }
        //这里是扩展
      //  p($product);die;
        if(!empty($product['extend'])){
            $extend=json_decode($product['extend'],true);
           $this->assign('extend',$extend);
        }else{
            $this->assign('extend',0);
        }


       /* if(){

        }*/
        /**
         * 商城设置
         */
        $is_d = M('Product_setting_new')->where(array('token'=>$this->token))->getField('is_distribution');//是否开启分销
        $this->assign('is_d',$is_d);
        $this->assign('color', $this->color);
        $this->assign('attributeData', $attributeData);
        $this->assign('normsData', $normsData);
        $this->assign('colorData', $colorData);
        $this->assign('formatData', $formatData);
        $this->assign('productCatData', $productCatData);
        $this->assign('productDetailData', $pData);
        $this->assign('catid', $catid);
        $this->display('set_new');
        /*if($this->token == "5d8a87bab30de695954b17fc835b9d12"){
            $this->display('shop');
        }else{

        }*/

    }

    /**
     * 增加商品
     */
    public function productSave() {
        $long = isset($_POST['long']) ? htmlspecialchars($_POST['long']) : '';//商品排序
        $wide = isset($_POST['wide']) ? htmlspecialchars($_POST['wide']) : '';//商品排序
        $high = isset($_POST['high']) ? htmlspecialchars($_POST['high']) : '';//商品排序
        $paixu = isset($_POST['paixu']) ? htmlspecialchars($_POST['paixu']) : '0';//商品排序
        $token = isset($_POST['token']) ? htmlspecialchars($_POST['token']) : '';
        $catid = isset($_POST['catid']) ? intval($_POST['catid']) : 0;
        $num = isset($_POST['num']) ? intval($_POST['num']) : 0;
        $pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
        $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
        $keyword = isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : '';
        $pic = isset($_POST['pic']) ? htmlspecialchars($_POST['pic']) : '';
        $price = isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '';
        $vprice = isset($_POST['vprice']) ? htmlspecialchars($_POST['vprice']) : '';
        $oprice = isset($_POST['oprice']) ? htmlspecialchars($_POST['oprice']) : '';
        $mailprice = isset($_POST['mailprice']) ? htmlspecialchars($_POST['mailprice']) : '';
        $intro = isset($_POST['intro']) ? $_POST['intro'] : '';
        $attribute = isset($_POST['attribute']) ? htmlspecialchars_decode($_POST['attribute']) : '';
        $norms = isset($_POST['norms']) ? htmlspecialchars_decode($_POST['norms']) : '';
        $images = isset($_POST['images']) ? htmlspecialchars_decode($_POST['images']) : '';
      //  p($images);die;
        $sort = isset($_POST['sort']) ? intval($_POST['sort']) : 100;
        $des=isset($_POST['des']) ? $_POST['des'] :"";
        $bili=isset($_POST['bili']) ? $_POST['bili'] :"0.001";//默认佣金比例为0.001
	$starting = !empty($_POST['starting']) ? $_POST['starting'] :"";
	        $destination = !empty($_POST['destination']) ? $_POST['destination'] :"";
	        $fatDate = !empty($_POST['fatDate']) ? $_POST['fatDate'] :"";
        $day_num = !empty($_POST['day_num']) ? $_POST['day_num'] :"";
        $content = !empty($_POST['content']) ? $_POST['content'] :"";
        $shoufu = !empty($_POST['shoufu']) ? $_POST['shoufu'] :"";
        $fenqi = !empty($_POST['fenqi']) ? $_POST['fenqi'] :"";
        $monthly_repayments = !empty($_POST['monthly_repayments']) ? $_POST['monthly_repayments'] :"";
        $loan_total_money = !empty($_POST['loan_total_money']) ? $_POST['loan_total_money'] :"";
        //这里是扩展
        if(empty($_POST['extend1'])){

             $extend='';
        }else{
             $extend=array();

            $cc1=explode(',',$_POST['extend1']);
            $cc2=explode(',',$_POST['val1']);
            for($i=0;$i<count($cc1);$i++){
                if($cc1[$i]){
                    $extend[$cc1[$i]]=empty($cc2[$i])?"":$cc2[$i];
                }

            }

            $extend = htmlspecialchars_decode(json_encode($extend,true));

        }


        $iftour = !empty($_POST['iftour']) ? $_POST['iftour'] :"";
	        if (empty($starting) && empty($destination) && empty($fatDate)) {
	            $tourInfo = '';
	        }else{
	            $tourInfo = array(
	                'starting'=>$starting,
	                'destination'=>$destination,
	                'fatDate'=>$fatDate
	            );
	            $tourInfo = htmlspecialchars_decode(json_encode($tourInfo,true));

	        }

        if ($token != session('token')) {
            exit(json_encode(array('error_code' => true, 'msg' => '不合法的数据')));
        }
        if (empty($name)) {
            exit(json_encode(array('error_code' => true, 'msg' => '商品不能为空')));
        }
        if (empty($catid)) {
            exit(json_encode(array('error_code' => true, 'msg' => '商品分类不能为空')));
        }
        $data = array('long'=>$long,'wide'=>$wide,'high'=>$high,'extend'=>$extend,'paixu'=>$paixu,'bili'=>$bili,'token' => $token, 'num' => $num, 'sort' => $sort, 'catid' => $catid,
            'name' => $name, 'price' => $price, 'mailprice' => $mailprice, 'vprice' => $vprice, 'oprice' => $oprice, 'intro' => $intro,
            'logourl' => $pic, 'keyword' => $keyword, 'time' => time(),'des'=>$des,'tourInfo'=>$tourInfo,'iftour'=>$iftour,
            'day_num'=>$day_num,'content'=>$content,'shoufu'=>$shoufu,'fenqi'=>$fenqi,'monthly_repayments'=>$monthly_repayments,'loan_total_money'=>$loan_total_money,
        );
        $product = M('Product_new');
        if ($pid && $obj = $product->where(array('id' => $pid, 'token' => $token))->find()) {
            $product->where(array('id' => $pid, 'token' => $token))->save($data);
        } else {
            $pid = $product->add($data);
        }
        if (empty($pid)) {
            exit(json_encode(array('error_code' => false, 'msg' => '商品添加出错了')));
        }

        if (!empty($attribute)) {
            $product_attribute = M('Product_attribute_new');
            $attribute = json_decode($attribute, true);
            foreach ($attribute as $row) {
                $data_a = array('pid' => $pid, 'aid' => $row['aid'], 'name' => $row['name'], 'value' => $row['value']);
                if ($row['id']) {
                    $product_attribute->where(array('id' => $row['id'], 'pid' => $pid))->save($data_a);
                } else {
                    $product_attribute->add($data_a);
                }
            }
        }

        if (!empty($norms)) {
            $product_detail = M('Product_detail_new');
            $norms = json_decode($norms, true);
            $detailList = $product_detail->field('id')->where(array('pid' => $pid))->select();
            $oldDetailId = array();
            foreach ($detailList as $val) {
                $oldDetailId[$val['id']] = $val['id'];

            }
            foreach ($norms as $row) {
                $data_d = array('pid' => $pid, 'format' => $row['format'], 'color' => $row['color'], 'num' => $row['num'], 'price' => $row['price'], 'vprice' => $row['vprice']);
                if ($row['id']) {
                    unset($oldDetailId[$row['id']]);
                    $product_detail->where(array('id' => $row['id'], 'pid' => $pid))->save($data_d);
                } else {
                    $product_detail->add($data_d);
                }
            }
            //删除上次剩余的库存
            foreach ($oldDetailId as $id) {
                $product_detail->where(array('id' => $id, 'pid' => $pid))->delete();
            }
        }
        if (!empty($images)) {
            $product_image = M('Product_image_new');
            $images = json_decode($images, true);
            $iamgelist = $product_image->field('id')->where(array('pid' => $pid))->select();
            $oldImageId = array();
            foreach ($iamgelist as $val) {
                $oldImageId[$val['id']] = $val['id'];
            }
            foreach ($images as $row) {
                if (empty($row['image'])) continue;
                $data_d = array('pid' => $pid, 'image' => $row['image']);
                if ($row['id']) {
                    unset($oldImageId[$row['id']]);
                    $product_image->where(array('id' => $row['id'], 'pid' => $pid))->save($data_d);
                } else {
                    $product_image->add($data_d);
                }
            }
            //删除上次剩余的库存
            foreach ($oldImageId as $id) {
                $product_image->where(array('id' => $id, 'pid' => $pid))->delete();
            }
        }
        exit(json_encode(array('error_code' => false, 'msg' => '商品操作成功')));
    }

    /**
     * 删除商品
     */
    public function del(){
        $product_model=M('Product_new');
        if($_REQUEST['token']!=session('token')){$this->error('非法操作');}
        $id = $_REQUEST['id'];
        if(IS_POST){
            $where=array('id'=>$id,'token'=>session('token'));
            $check=$product_model->where($where)->find();
            if($check==false)   $this->error('非法操作');

            $back=$product_model->where($where)->delete();
            if($back==true){
                $keyword_model=M('Keyword_new');
                $keyword_model->where(array('token'=>session('token'),'pid'=>$id,'module'=>'Product'))->delete();
                $this->success('操作成功',U('Store_new/product',array('token'=>session('token'),'catid'=>$_REQUEST['catid'],'dining'=>$this->isDining)));
            }else{
                $this->error('服务器繁忙,请稍后再试',U('Store_new/product',array('token'=>session('token'))));
            }
        }
    }


    /**
     * 商城设置
     */
    public function setting()
    {
        $setting = M('Product_setting_new');
        $obj = $setting->where(array('token' => session('token')))->find();


            if (IS_POST) {
                //print_r($_POST);exit;
                //   p($_POST);die;
                if ($obj) {
                    $t = $setting->where(array('token' => session('token')))->save($_POST);
                    //print_r($t);exit;

                    if ($t) {
                        $this->success('修改成功',U('Store_new/index',array('token' => session('token'))));
                    } else {
                        $this->error('操作失败');
                    }
                } else {
                    $pid = $setting->add($_POST);
                    if ($pid) {
                        $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
                    }else{
                        $this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
                    }

                }
            } else {
                $this->assign('setting', $obj);
                $this->display();
            }
        }


/**
*订单管理
 *
 **/
    public function orders()
    {
        if(isset($_GET['tg'])){
            $where['tg']=$this->_get('tg');
            $arr['tg']=$this->_get('tg');
        }
        $product_cart_model = M('product_cart_new');
        if (IS_POST) {
            //print_r($_POST);exit;
            // {truename:truename,tel:tel,paid:paid,sent:sent,statdate:statdate,enddate:enddate},
            $arr['token'] = $_REQUEST['token'];
            if($_POST['truename']) {
                $arr['truename'] = $_POST['truename'];
            }
            if($_POST['tel']) {
                $arr['tel'] = $_POST['tel'];
            }
            if($_POST['orderid']) {
                $arr['orderid'] = $_POST['orderid'];
            }

            if($_POST['paid'] != -1) {
                $arr['paid'] = $_POST['paid'];
            }
            if($_POST['sent']) {
                $arr['sent'] = $_POST['sent'];
            }
            if($_POST['handled']){
                $arr['handled'] = $_POST['handled'];
            }
            if($_POST['statdate']){
                $date['statdate'] = $_POST['statdate'];
            }
            if($_POST['enddate']){
                $date['enddate'] = $_POST['enddate'];
            }
            if($date['statdate'] and $date['enddate']){
                $arr['time'] = array(array('gt',strtotime($date['statdate'])), array('lt', strtotime($date['enddate'])));
            }
            $count = $product_cart_model->where($arr)->count();
            $page = new Page($count,10);
            $orders = $product_cart_model->where($arr)->order('time DESC')->limit($page->firstRow.','.$page->listRows)->select();
	    foreach($orders as $key=>$orderter) {
                if ($orderter['type'] == 'par') {
                    if ($orderter['productid'] == 0 && $orderter['info'] != "") {
                        $data = $orderter['info'];
                        $data = substr($data, 0, -1);
                        $ruldata = explode('/', $data);
                        $host = array();
                        foreach($ruldata as $k=>$rul){
                            $host[] = explode('-',$rul);
                        }
                        $orders[$key]['totalCount'] = 0;
                        $orders[$key]['priceCount'] = 0;
                        foreach($host as $c=>$crul){
                            $orders[$key]['totalCount'] +=intval($crul[1]);
                            $orders[$key]['priceCount'] += intval($crul[2]*$crul[1]);
                        }
                        $orders[$key]['priceCount']=$orders[$key]['priceCount']+ $orderter['mailpay'];
                    }elseif($orderter['productid'] != 0 && $orderter['info'] == ""){
                        $priceCount = $orderter['price'] * $orderter['total'] + $orderter['mailpay'];
                        $orders[$key]['priceCount'] = $priceCount;
                        $orders[$key]['totalCount'] = $orderter['total'];
                    }
                } elseif ($orderter['type'] == 'son') {
                    if ($orderter['productid'] == 0 && $orderter['info'] != "") {
                        $data = $orderter['info'];
                        $data = substr($data, 0, -1);
                        $ruldata = explode('/', $data);
                        $host = array();
                        foreach($ruldata as $k=>$rul){
                            $host[] = explode('-',$rul);
                        }
                        $orders[$key]['totalCount'] = 0;
                        $orders[$key]['priceCount'] = 0;
                        foreach($host as $c=>$crul){
                            $orders[$key]['totalCount'] +=intval($crul[1]);
                            $orders[$key]['priceCount'] += intval($crul[2]*$crul[1]);
                        }
                        $orders[$key]['priceCount']=$orders[$key]['priceCount']+ $orderter['mailpay'];
                    }elseif($orderter['productid'] != 0 && $orderter['info'] == ""){
                        $priceCount = $orderter['price'] * $orderter['total'] + $orderter['mailpay'];
                        $orders[$key]['priceCount'] = $priceCount;
                        $orders[$key]['totalCount'] = $orderter['total'];
                    }
                }
                $this->assign('page', $page->show());
            if($this->token='e756d6be1ec4fab3c5920f3a3437160b'){//鱼美人
                foreach($orders as $k=>$v){
                    if($ym_name=M('Mru_jfb')->where(array('token'=>$this->token,'openid'=>$v['wecha_id']))->getField('name')){
                        $orders[$k]['truename']=$ym_name;
                    }
                }

            }
                $this->assign('orders', $orders);
                //p($orders);
            }

            $this->assign('truename',$_POST['truename']);
            $this->assign('tel',$_POST['tel']);
            $this->assign('orderid',$_POST['orderid']);
            $this->assign('paid',$_POST['paid']);
            $this->assign('sent',$_POST['sent']);
            $this->assign('handled',$_POST['handled']);
            $this->assign('statdate',$_POST['statdate']);
            $this->assign('enddate',$_POST['enddate']);
        }else{
            $where['token']=$_REQUEST['token'];





            if($_GET['truename']) {
                $where['truename'] = $_GET['truename'];
            }
            if($_GET['tel']) {
                $where['tel'] = $_GET['tel'];
            }
            if($_GET['orderid']) {
                $where['orderid'] = $_GET['orderid'];
            }

            if(isset($_GET['paid']) AND $_GET['paid'] != -1) {
                $where['paid'] = $_GET['paid'];
            }
            if($_GET['sent']) {
                $where['sent'] = $_GET['sent'];
            }
            if($_GET['handled']){
                $where['handled'] = $_GET['handled'];
            }
            if($_GET['statdate']){
                $date['statdate'] = $_GET['statdate'];
            }
            if($_GET['enddate']){
                $date['enddate'] = $_GET['enddate'];
            }
            if($date['statdate'] and $date['enddate']){
                $where['time'] = array(array('gt',strtotime($date['statdate'])), array('lt', strtotime($date['enddate'])));
            }







            $count = $product_cart_model->where($where)->count();
            $page = new Page($count,10);
            $orders = $product_cart_model->where($where)->order('time DESC')->limit($page->firstRow.','.$page->listRows)->select();

            foreach($orders as $key=>$orderter) {

                if ($orderter['type'] == 'par') {
                    if ($orderter['productid'] == 0 && $orderter['info'] != "") {
                        $data = $orderter['info'];
                        $data = substr($data, 0, -1);
                        $ruldata = explode('/', $data);
                        $host = array();
                        foreach($ruldata as $k=>$rul){
                            $host[] = explode('-',$rul);
                        }
                        $orders[$key]['totalCount'] = 0;
                        $orders[$key]['priceCount'] = 0;
                        foreach($host as $c=>$crul){
                            $orders[$key]['totalCount'] +=intval($crul[1]);
                            $orders[$key]['priceCount'] += intval($crul[2]*$crul[1]);
                        }
                        $orders[$key]['priceCount']=$orders[$key]['priceCount']+ $orderter['mailpay'];
                    }elseif($orderter['productid'] != 0 && $orderter['info'] == ""){
                        $priceCount = $orderter['price'] * $orderter['total'] + $orderter['mailpay'];
                        $orders[$key]['priceCount'] = $priceCount;
                        $orders[$key]['totalCount'] = $orderter['total'];
                    }

                } elseif ($orderter['type'] == 'son') {
                    if ($orderter['productid'] == 0 && $orderter['info'] != "") {
                        $data = $orderter['info'];
                        $data = substr($data, 0, -1);
                        $ruldata = explode('/', $data);
                        $host = array();
                        foreach($ruldata as $k=>$rul){
                            $host[] = explode('-',$rul);
                        }
                        $orders[$key]['totalCount'] = 0;
                        $orders[$key]['priceCount'] = 0;
                        foreach($host as $c=>$crul){
                            $orders[$key]['totalCount'] +=intval($crul[1]);
                            $orders[$key]['priceCount'] += intval($crul[2]*$crul[1]);
                        }
                        $orders[$key]['priceCount']=$orders[$key]['priceCount']+ $orderter['mailpay'];
                    }elseif($orderter['productid'] != 0 && $orderter['info'] == ""){
                        $priceCount = $orderter['price'] * $orderter['total'] + $orderter['mailpay'];
                        $orders[$key]['priceCount'] = $priceCount;
                        $orders[$key]['totalCount'] = $orderter['total'];
                    }
                }
                    $this->assign('page', $page->show());
                if($this->token='e756d6be1ec4fab3c5920f3a3437160b'){//鱼美人
                    foreach($orders as $k=>$v){
                        if($ym_name=M('Mru_jfb')->where(array('token'=>$this->token,'openid'=>$v['wecha_id']))->getField('name')){
                            $orders[$k]['truename']=$ym_name;
                        }
                    }

                }
                    $this->assign('orders', $orders);
            }
        }
        //p($orders);die;
        #统计数据
        $this->stat($product_cart_model);
        if (!isset($_REQUEST['paid'])) {
            $_REQUEST['paid'] = -1;
        }
        $this->assign('param',$_REQUEST);
        $this->display();
    }

    //统计数据
    public function stat($Model)
    {
        $aBase['token']=$_REQUEST['token'];
        $aBase['paid'] = 1;
        #今天
        $aToday = array(
            array('gt',strtotime(date('Y-m-d'))),
            array('lt', strtotime('+1 days'))
        );
        #本月
        $aMonth = array(
            array('gt',strtotime(date('Y-m-01'))),
            array('lt', strtotime('+1 days'))
        );
        $aStat = array();
        //今日订单数
        $aStat['today_num'] = $Model->where(array_merge($aBase, array(
            'time' => $aToday
        )))->count();
        //今日订单总额
        $aStat['today_price'] = $Model->where(array_merge($aBase, array(
            'time' => array(
                array('gt',strtotime(date('Y-m-d'))),
                array('lt', strtotime('+1 days')))
            ))
        )->sum('price * total');
        //本月订单数
        $aStat['month_num'] = $Model->where(array_merge($aBase, array(
            'time' => $aMonth
        )))->count();
        //本月订单金额
        $aStat['month_price'] = $Model->where(array_merge($aBase, array(
            'time' => $aMonth
        )))->sum('price * total');
        //总订单数
        $aStat['total_num'] = $Model->where($aBase)->count();
        //总订单金额
        $aStat['total_price'] = $Model->where($aBase)->sum('price * total');
        $this->assign('stat', $aStat);
    }

    public function orderInfo()
    {

        $this->product_model = M('Product_new');
        $this->product_cat_model = M('Product_cat_new');
        $product_cart_model = M('product_cart_new');
        $this->product_detail_model = M('Product_detail_new');
        //print_r($this->token);exit;

        $thisOrder = $product_cart_model->where(array('id' => intval($_GET['id']),'token'=>$this->token))->find();
        $quan = M('Sn')->where(array('id'=>intval($thisOrder['sid']),'token'=>$this->token))->find();

        //print_r($thisOrder);exit;
//        print_r($thisOrder['info']);
//
        //检查权限

        if (IS_POST) {
            // print_r($_POST);exit;
            if (intval($_POST['sent'])) {
                $_POST['handled'] = 1;
            }
            $product_cart_model->where(array('id' => $thisOrder['id']))->save(array('sent' => intval($_POST['sent']), 'paid' => intval($_POST['paid']), 'logistics' => $_POST['logistics'], 'logisticsid' => $_POST['logisticsid'], 'handled' => 1));
            //TODO 发货的短信提醒
	    /*
            if ($_POST['sent']) {
                $company = D('Company')->where(array('token' => $thisOrder['token'], 'isbranch' => 0))->find();
                $userInfo = D('Userinfo_new')->where(array('token' => $thisOrder['token'], 'wecha_id' => $thisOrder['wecha_id']))->find();
                Sms::sendSms($this->token, "您在{$company['name']}商城购买的商品，商家已经给您发货了，请您注意查收", $userInfo['tel']);
            }
	    */

            //
            /************************************************/
            if (intval($_POST['paid']) && intval($thisOrder['price'])) {
                $member_card_create_db = M('Member_card_create_new');
                $wecha_id = $thisOrder['wecha_id'];
                $userCard = $member_card_create_db->where(array('token' => $this->token, 'wecha_id' => $wecha_id))->find();
                $member_card_set_db = M('Member_card_set_new');
                $thisCard = $member_card_set_db->where(array('id' => intval($userCard['cardid'])))->find();
                $set_exchange = M('Member_card_exchange_new')->where(array('cardid' => intval($thisCard['id'])))->find();
                //
                $arr['token'] = $this->token;
                $arr['wecha_id'] = $wecha_id;
                $arr['expense'] = $thisOrder['price'];
                $arr['time'] = time();
                $arr['cat'] = 99;
                $arr['staffid'] = 0;
                $arr['score'] = intval($set_exchange['reward']) * $order['price'];
                M('Member_card_use_record_new')->add($arr);
                $userinfo_db = M('Userinfo_new');
                $thisUser = $userinfo_db->where(array('token' => $thisCard['token'], 'wecha_id' => $arr['wecha_id']))->find();
                $userArr = array();
                $userArr['total_score'] = $thisUser['total_score'] + $arr['score'];
                $userArr['expensetotal'] = $thisUser['expensetotal'] + $arr['expense'];
                $userinfo_db->where(array('token' => $thisCard['token'], 'wecha_id' => $arr['wecha_id']))->save($userArr);
            }
            /************************************************/
            //

            $this->success('修改成功', U('Store_new/orderInfo', array('token' => session('token'), 'id' => $thisOrder['id'])));
        } else {
            //订餐信息
            //   $product_diningtable_model = M('product_diningtable_new');
            // print_r($product_diningtable_model);exit;
//            if ($thisOrder['tableid']) {
//                $thisTable=$product_diningtable_model->where(array('id'=>$thisOrder['tableid']))->find();
//                $thisOrder['tableName']=$thisTable['name'];
//            }
            //
            if($thisOrder['type'] == 'par'){
                $opinfo = $this->product_model->where(array('id' => $thisOrder['productid'],'token'=>$this->token))->find();
                $priceCount = $thisOrder['price']*$thisOrder['total']+$thisOrder['mailpay'];
                $this->assign('products',$opinfo);
                $this->assign('totalCount',$thisOrder['total']);
                $this->assign('priceCount',$priceCount);

            }
            if($thisOrder['type'] == 'son'){
                if ($thisOrder['productid'] == 0 && $thisOrder['info'] != "") {

                    $data = $thisOrder['info'];
                    $data = substr($data, 0, -1);
                    $ruldata = explode('/', $data);
                    $host = array();
                    foreach($ruldata as $k=>$rul){
                        $host[] = explode('-',$rul);
                    }
                    $total = 0;
                    $price = 0;
                    foreach($host as $c=>$crul){

                        if($crul[3] =='par'){

                            $ginfo = $this->product_model->where(array('id' => $crul[0],'token'=>$this->token))->find();

                            $host[$c][4]=$ginfo['logourl'];
                            $host[$c][5]=$ginfo['name'];

                        }elseif($crul[3] =='son'){

                            $tinfo = $this->product_detail_model->where(array('id' => $crul[0],'token'=>$this->token))->find();


                            $ginfo = $this->product_model->where(array('id' => $tinfo['pid'],'token'=>$this->token))->find();
                            $linfo = M('Norms_new')->where(array('id' => $tinfo['format'],'token'=>$this->token))->find();
                            $vinfo = M('Norms_new')->where(array('id' => $tinfo['color'],'token'=>$this->token))->find();
                            $jinfo = M('Product_cat_new')->where(array('id'=>$vinfo['catid'],'token'=>$this->token))->find();
                            $host[$c][4] = $ginfo['logourl'];   // 商品图片
                            $host[$c][5] = $ginfo['name'];      // 商品名称
                            $host[$c][6] = $jinfo['norms'];     // 商品规格
                            $host[$c][7] = $jinfo['color'];     // 商品外观
                            $host[$c][8] = $linfo['value'];     // 商品规格值
                            $host[$c][9] = $vinfo['value'];     // 商品外观值

                            $host[$c][10] = $vinfo['catid'];     // 商城ID值

                            //array_push($crul,$ginfo['logourl'] ,$ginfo['name'],$jinfo['norms'],$jinfo['color'],$linfo['value'],$vinfo['value']);
                            // print_r($crul);exit;
                        }
                        $total+=intval($crul[1]);
                        $price += intval($crul[2]*$crul[1]);

                    }
                    $price = intval($price + $thisOrder['mailpay']);

                    $this->assign('host',$host);
                    $this->assign('totalCount',$total);
                    $this->assign('priceCount',$price);



                    /*              if ($thisOrder['type'] == 'par') {
                                        $ginfo = array();
                                        $total=0;
                                        $price = 0;
                                        foreach ($ruldata as $k => $host) {
                                            //print_r($host);exit;
                                            $value = explode('-', $host);
                                            $total+=intval($value[1]);
                                            $price += intval($value[2]);
                                            $ginfo[] = $this->product_model->where(array('id' => $value[0]))->find();

                                        }


                                        $this->assign('products', $ginfo);
                                        $this->assign('totalCount',$total);
                                        $this->assign('priceCount',$price);
                                    } else {

                                        $linfo = array();
                                       // print_r($ruldata);exit;
                                        $total=0;
                                        $price = 0;
                                        foreach ($ruldata as $t => $chost) {
                                            //print_r($t);exit;
                                            $value = explode('-', $chost);
                                            $total+=intval($value[1]);
                                            $price+=intval($value[2]);
                                            $linfo[] = $this->product_detail_model ->where(array('id'=>$value[0]))->find();
                                        }

                                        $ginfo = array();
                                        foreach($linfo as $c=>$hinfo){
                                            $ginfo[] = $this->product_model->where(array('id' => $hinfo['pid']))->find();

                                        }
                                        $this->assign('products', $ginfo);
                                        $this->assign('totalCount',$total);
                                        $this->assign('priceCount',$price);


                                    }*/
                }
                if($thisOrder['productid'] != 0 && $thisOrder['info'] ==""){
                    $openinfo = $this->product_detail_model->where(array('id' => $thisOrder['productid'],'token'=>$this->token))->find();
                    //print_r($openinfo);exit;
                    $ginfo = $this->product_model->where(array('id' => $openinfo['pid'],'token'=>$this->token))->find();
                    $linfo = M('Norms_new')->where(array('id' => $openinfo['format'],'token'=>$this->token))->find();
                    $vinfo = M('Norms_new')->where(array('id' => $openinfo['color'],'token'=>$this->token))->find();
                    $jinfo = M('Product_cat_new')->where(array('id'=>$vinfo['catid'],'token'=>$this->token))->find();
                    $host[4] = $ginfo['logourl'];   // 商品图片
                    $host[5] = $ginfo['name'];      // 商品名称
                    $host[6] = $jinfo['norms'];     // 商品规格
                    $host[7] = $jinfo['color'];     // 商品外观
                    $host[8] = $linfo['value'];     // 商品规格值
                    $host[9] = $vinfo['value'];     // 商品外观值

                    $host[10] = $vinfo['catid'];     // 商城ID值



                   // $opinfo = $this->product_model->where(array('id' => $thisOrder['productid'],'token'=>$this->token))->find();
                    $priceCount = $thisOrder['price']*$thisOrder['total']+$thisOrder['mailpay'];
                    $this->assign('products',$host);
                    $this->assign('totalCount',$thisOrder['total']);
                    $this->assign('priceCount',$priceCount);

                }

            }
            /**
             * 这里是购物车购买商品
             */
            if($thisOrder['is_cart']){
                $pro_list=M('Product_sidedetail')->where(array('cid'=>$thisOrder['id']))->select();
                $this->assign('pro_list',$pro_list);
               // p($pro_list);
            }else{
                $this->assign('product_info',$aProductInfo = $this->product_model
                    ->where(array(
                        'id' => $thisOrder['productid'],
                        'token'=>$this->token
                    ))->find());
            }
            /**
             * 这里处理旅游订单
             */

            if($thisOrder['ordertype']==3){
                $thisOrder['usertravelinfo']=json_decode($thisOrder['usertravelinfo'],true);
            }
            //处理扩展
            if($thisOrder['extend']){
                $thisOrder['extend']=json_decode($thisOrder['extend'],true);
            }
           // p($thisOrder);
            $this->assign('thisOrder',$thisOrder);

            $this->assign('quan',$quan);

            $this->display();
        }
    }

    /**
     * 计算一次购物的总的价格与数量
     * @param array $carts
     */
    public function getCat($carts)
    {
        if (empty($carts)) {
            return array();
        }
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
        $data = array();
        if ($dids) {
            $dids = array_unique($dids);
            $detail = M('Product_detail_new')->where(array('id'=> array('in', $dids)))->select();
            foreach ($detail as $row) {
                $row['colorName'] = isset($norms[$row['color']]) ? $norms[$row['color']] : '';
                $row['formatName'] = isset($norms[$row['format']]) ? $norms[$row['format']] : '';
                $row['count'] = isset($carts[$row['pid']][$row['id']]['count']) ? $carts[$row['pid']][$row['id']]['count'] : 0;
                $productList[$row['pid']]['detail'][] = $row;
                $data[$row['pid']]['total'] = isset($data[$row['pid']]['total']) ? intval($data[$row['pid']]['total'] + $row['count']) : $row['count'];
                $data[$row['pid']]['totalPrice'] = isset($data[$row['pid']]['totalPrice']) ? intval($data[$row['pid']]['totalPrice'] + $row['count'] * $row['price']) : $row['count'] * $row['price'];//array('total' => $totalCount, 'totalPrice' => $totalFee);
            }
        }
        //商品的详细列表
        $list = array();
        foreach ($productList as $pid => $row) {
            if (!isset($data[$pid]['total'])) {
                $data[$pid] = array();
                $row['count'] = $data[$pid]['total'] = isset($carts[$pid]['count']) ? $carts[$pid]['count'] : (isset($carts[$pid]) && is_int($carts[$pid]) ? $carts[$pid] : 0);
                $data[$pid]['totalPrice'] = $data[$pid]['total'] * $row['price'];
            }
            $row['formatTitle'] =  isset($catlist[$row['catid']]['norms']) ? $catlist[$row['catid']]['norms'] : '';
            $row['colorTitle'] =  isset($catlist[$row['catid']]['color']) ? $catlist[$row['catid']]['color'] : '';
            $list[] = $row;
        }
        return array($list, $data, $mailPrice);
        die;
        if (empty($carts)) {
            return array();
        }
        $pdata = $data = $list = $ids = $detail_list = $products = array();
        $mailPrice = 0;
        foreach ($carts as $pid => $rowset) {
            $totalCount = $totalFee = 0;
            $tmp = $this->product_model->where(array('id'=> $pid))->find();
            if (is_array($rowset)) {
                $norms = $cntArray = $dids = array();
                foreach ($rowset as $did => $count) {
                    if (!in_array($did, $dids)){
                        array_push($dids, $did);
                        $cntArray[$did] = $count['count'];
                    }
                    $totalCount += $count['count'];
                }
                $normsdata = M('norms_new')->where(array('catid' => $tmp['catid']))->select();
                foreach ($normsdata as $r) {
                    $norms[$r['id']] = $r['value'];
                }
                if ($dids) {
                    $temp = M('Product_detail_new')->where(array('id'=> array('in', $dids), 'pid' => $pid))->select();
                    foreach ($temp as $row) {
                        if (isset($rowset[$row['id']])) {
                            $row['colorName'] = isset($norms[$row['color']]) ? $norms[$row['color']] : '';
                            $row['formatName'] = isset($norms[$row['format']]) ? $norms[$row['format']] : '';
                            $row['count'] = $cntArray[$row['id']];
                            $totalFee += $cntArray[$row['id']] * $row['price'];
                            $detail_list[$pid][] = $row;
                        }
                    }
                }
            } else {
                $totalCount += $rowset;
                $totalFee += $rowset * $tmp['price'];
                $pdata[$pid] = $rowset;
            }
            $mailPrice = max($mailPrice, $tmp['mailprice']);
            $data[$pid] = array('total' => $totalCount, 'totalPrice' => $totalFee);
        }

        $ids = array_keys($carts);
        if (count($ids)) {
            $tmp = $this->product_model->where(array('id'=>array('in', $ids)))->select();
            foreach ($tmp as $row) {
                if (isset($detail_list[$row['id']])) {
                    if ($catData = M('Product_cat_new')-> where(array('id' => $row['catid']))->find()) {
                        $row['formatTitle'] =  $catData['norms'];
                        $row['colorTitle'] =  $catData['color'];
                    }
                    $row['detail'] =  $detail_list[$row['id']];
                } else {
                    $row['detail'] = '';
                    $row['count'] = $pdata[$row['id']];
                }
                $list[] = $row;
            }
        }
        return array($list, $data, $mailPrice);

        $list = $ids = $detail_list = $products = array();
        $carts = empty($carts) ? $this->_getCart() : $carts;
        $pdata = $data = array();
        $mailPrice = 0;
        foreach ($carts as $pid => $rowset) {
            $totalCount = $totalFee = 0;
            $tmp = $this->product_model->where(array('id'=> $pid))->find();
            if (is_array($rowset)) {
                $norms = $cntArray = $dids = array();
                foreach ($rowset as $did => $count) {
                    if (!in_array($did, $dids)){
                        array_push($dids, $did);
                        $cntArray[$did] = $count['count'];
                    }
                    $totalCount += $count['count'];
                }
                $normsdata = M('norms_new')->where(array('catid' => $tmp['catid']))->select();
                foreach ($normsdata as $r) {
                    $norms[$r['id']] = $r['value'];
                }
                if ($dids) {
                    $temp = M('Product_detail_new')->where(array('id'=> array('in', $dids), 'pid' => $pid))->select();
                    foreach ($temp as $row) {
                        if (isset($rowset[$row['id']])) {
                            $row['colorName'] = isset($norms[$row['color']]) ? $norms[$row['color']] : '';
                            $row['formatName'] = isset($norms[$row['format']]) ? $norms[$row['format']] : '';
                            $row['count'] = $cntArray[$row['id']];
                            $totalFee += $cntArray[$row['id']] * $row['price'];
                            $detail_list[$pid][] = $row;
                        }
                    }
                }
            } else {
                $totalCount += $rowset;
                $totalFee += $rowset * $tmp['price'];
                $pdata[$pid] = $rowset;
            }
            $mailPrice = max($mailPrice, $tmp['mailprice']);
            $data[$pid] = array('total' => $totalCount, 'totalPrice' => $totalFee);
        }
        $ids = array_keys($carts);
        if (count($ids)) {
            $tmp = $this->product_model->where(array('id'=>array('in', $ids)))->select();
            foreach ($tmp as $row) {
                if (isset($detail_list[$row['id']])) {
                    if ($catData = M('Product_cat_new')-> where(array('id' => $row['catid']))->find()) {
                        $row['formatTitle'] = $catData['norms'];
                        $row['colorTitle'] = $catData['color'];
                    }
                    $row['detail'] =  $detail_list[$row['id']];
                } else {
                    $row['detail'] = '';
                    $row['count'] = $pdata[$row['id']];
                }
                $list[] = $row;
            }
        }
        return array($list, $data, $mailPrice);
    }

    public function deleteOrder(){
        $product_model=M('product_new');
        $product_cart_model=M('product_cart_new');
        $product_cart_list_model=M('product_cart_list_new');
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
        if (empty($thisOrder['paid'])) {
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
//			foreach ($carts as $k => $c){
//				if (is_array($c)){
//					$productid=$k;
//					$price=$c['price'];
//					$count=$c['count'];
//					$product_model->where(array('id'=>$k))->setDec('salecount',$c['count']);
//				}
//			}
        }
        $this->success('操作成功',U('Store_new/orders', array('token' => session('token'))));
        //$this->success('操作成功',$_SERVER['HTTP_REFERER']);
    }

    public function sn(){
        $sn = M('Snname');
        $token = session('token');
        $count      = $sn->where(array('token'=>$token))->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $host = $sn->where(array('token'=>$token))->limit(20)->order('starttime desc')->select();
        $time = time();
        $this->assign('host',$host);
        //print_r($host);exit;
        $this->assign('page',$show);
        $this->assign('time',$time);
        $this->display();
    }

    public function snset()
    {
        if (IS_POST) {
//{snname:snname,num:num,length:length,starttime:starttime,endtime:endtime,amount:amount,token:'{weikucms:$token}'},
            $data['snname'] = $_POST['snname'];
            $data['num'] = $_POST['num'];
            $data['length'] = $_POST['length'];
            $data['starttime'] = strtotime($_POST['starttime']);


            $data['endtime'] = strtotime($_POST['endtime']);
            $data['amount'] = $_POST['amount'];
            $data['token'] = $_POST['token'];
            $data['pic'] = $_POST['pic'];


            if(M('Snname')->data($data)->add()){
                $data['sid'] = mysql_insert_id();
            $passwords = array();
            $str = '0123456789abcdefghijklmnopqrstuvwxyz';
            while (count($passwords) < $data['num']) {
                $np = '';
                for ($i = 0; $i < $data['length']; $i++) {
                    $np .= $str[mt_rand(0, strlen($str) - 1)];
                }
                $passwords[$np] = $np;
            }
            foreach ($passwords as $value) {
                $data['sn'] = $value;
               $list =  M('Sn')->add($data);
            }
            if($list){

                    $this->success('生成成功！', U(MODULE_NAME . '/sn', array('token' => session('token'))));
                }
            }else{
                $this->error('生成失败！',U(MODULE_NAME.'/sn',array('token'=>session('token'))));
            }
        }
            $this->display();
    }

    public function sndel(){
        $token = session('token');
        $id = $_GET['id'];
       if( M('Snname')->where(array('token'=>$token,'id'=>$id))->delete()){
           $this->success('删除成功！', U(MODULE_NAME . '/sn', array('token' => session('token'))));
       }else{
           $this->error('删除失败！',U(MODULE_NAME.'/sn',array('token'=>session('token'))));
       }
    }

    public function sndetails(){
        $token = session('token');
        $sid = $_REQUEST['sid'];
        $sn = M('sn');
        $count      = $sn->where(array('token'=>$token,'sid'=>$sid))->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = $sn->where(array('token'=>$token,'sid'=>$sid))->limit(20)->select();
        //print_r($list);exit;
        $time = time();
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->assign('time',$time);
        $this->display();
    }

    /**
     * 商城文章分类列表页
     */
    public function articleindexfl()
    {
        $db = M('Product_new_articlefl');
        $where['token'] = session('token');
        $count = $db->where($where)->count();
        $page = new Page($count, 15);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

        $this->assign('page', $page->show());
        $this->assign('info', $info);

        $this->display();

    }
    /**
     * 商城文章列表页
     */
    public function articleindex()
    {
        $db = M('Product_new_article');
        $where['token'] = session('token');
        $count = $db->where($where)->count();
        $page = new Page($count, 15);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('sort desc,createtime desc')->select();
        foreach($info as $k=>$v){
            if($v['fid']>0){
                $info[$k]['fname']=M('Product_new_articlefl')->where(array('id'=>$v['fid']))->getfield('name');
            }else{
                $info[$k]['fname']="无分类";
            }
        }
        $this->assign('page', $page->show());
        $this->assign('info', $info);

        $this->display();

    }
    /**
     * 添加商城文章分类
     */
    public function articleadd_fl(){
        if(IS_POST){
            $_POST['token']=$this->token;
            $model=M('Product_new_articlefl');
            $model->create();
            if($model->add()){
                $this->success2('操作成功', U(MODULE_NAME . '/articleindexfl'));
            }else{
                $this->error2('操作失败', U(MODULE_NAME . '/articleindexfl'));
            }
        }else {
            $this->display();
        }
    }

    /**
     * 修改商城文章分类
     */
    public function articlefledit(){
        $model=M('Product_new_articlefl');
        if(IS_POST){
            $_POST['token']=session('token');
            $model->create();
            if($model->save()){
                $this->success2('修改成功', U(MODULE_NAME . '/articleindexfl'));
            }else{
                $this->error2('修改失败', U(MODULE_NAME . '/articleindexfl'));
            }
        }else{
            $id=$this->_get('id');
            $info=$model->find($id);
            $this->assign('info',$info);
            $this->display();

        }

    }

    /**
     * 删除商城文章分类
     */
    public function articlefldel(){
        $id=$this->_get('id');
        $model=M('Product_new_articlefl');
        if($model->delete($id)){
            $this->success2('删除成功', U(MODULE_NAME . '/articleindexfl'));
        }else{
            $this->error2('删除失败', U(MODULE_NAME . '/articleindexfl'));
        }
    }

    /**
     * 添加商成文章
     */
    public function articleadd()
    {
        if (IS_POST) {
            $_POST['token'] = session('token');
            $_POST['createtime'] = time();
            if(M('Product_new_article')->add($_POST)){
                $this->success('操作成功', U(MODULE_NAME . '/articleindex'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/articleindex'),true);
            }
        } else {
            $model=M('Product_new_articlefl');
            $flDate=$model->select();//文章分类数据
            $this->assign('flDate',$flDate);
            $this->display();
        }
    }

    public function articleedit(){
        if(IS_POST){
            $data = array();
            $where['id'] = $_POST['id'];
            unset($_POST['id']);
            $where['token'] = $this->token;
            if(M('Product_new_article')->where($where)->save($_POST)){
                $this->success('操作成功', U(MODULE_NAME . '/articleindex'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/articleindex'),true);
            }
        }else {
            $where['id'] = $this->_get('id', 'intval');
            $where['token'] = session('token');
            $data = D('Product_new_article')->where($where)->find();
            $model=M('Product_new_articlefl');
            $flDate=$model->select();//文章分类数据
            $this->assign('flDate',$flDate);
            $this->assign('data', $data);
            $this->display();
        }
    }

    public function articledel(){
        $where['id']=$this->_get('id','intval');
        $where['token']=session('token');
        if(D('Product_new_article')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/articleindex'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/articleindex'));
        }
    }


    public function flashindex(){
        $db=M('Product_new_flash');
        $where['token']=session('token');
        $count=$db->where($where)->count();
        $page=new Page($count,15);
        $info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('add_time desc')->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->display();

    }

    public function flashadd(){
        if(IS_POST){
            $_POST['token'] = session('token');
            $_POST['add_time'] = time();
            if(M('Product_new_flash')->add($_POST)){
                $this->success('操作成功', U(MODULE_NAME . '/flashindex'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/flashindex'),true);
            }
        }else {
            $this->display();
        }
    }


    public function flashedit(){
        if(IS_POST){
            $where['id'] = $_POST['id'];
            unset($_POST['id']);
            $where['token'] = $this->token;
            if(M('Product_new_flash')->where($where)->save($_POST)){
                $this->success('操作成功', U(MODULE_NAME . '/flashindex'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/flashindex'),true);
            }
        }else {
            $where['id'] = $this->_get('id', 'intval');
            $where['token'] = session('token');
            $data = D('Product_new_flash')->where($where)->find();
            $this->assign('data', $data);
            $this->display();
        }
    }

    public function flashdel(){
        $where['id']=$this->_get('id','intval');
        $where['token']=session('token');
        if(D('Product_new_flash')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/flashindex'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/flashindex'));
        }
    }

    public function analyse(){
        /*折线图*/
        $a = strtotime(date("Y-m-d"),time());
        $b = $a + (3600*24);

        $token = session('token');
        /*$esc = strtotime("2015-01-03");
        $esc1 = strtotime("2015-01-04");*/
        /*$times = array(array('gt',$esc),array('lt',$esc1));
        $lest = M('Product_new_visiter_data')->where(array('token'=>$token,'time'=>$times))->count();
        $lests = M('Product_new_visiter_data')->where(array('token'=>$token,'time'=>$times))->count('distinct(ip)');
        print_r($lest);
        echo "<br/>";
        print_r($lest);
        exit;*/

        /*echo $esc."<br/>";
        echo $esc1."<br/>";*/

        $j = array();

        $k = "";
        $c = "";
        $d = "";
        for ($i=$a; $i>=$a-(3600*24*15); $i -= 3600*24) {
            $j[] = date("Y-m-d",$i);
//            $k = $k."\"".date("Y-m-d",$i)."\"".",";
            $k = $k.date("Y-m-d",$i).",";
            $token = $this->token;
            $cate_id = $_GET['cate_id'];
            $product_id = $_GET['product_id'];
            if($cate_id && $product_id ){
                $times = array(array('gt',$i),array('lt',$i+3600*24));
                //$map['id'] = array(array('gt',1),array('lt',10)) ;
                $lest = M('Product_new_visiter_data')->where(array('token'=>$token,'cate_id'=>$cate_id,'product_id'=>$product_id,'time'=>$times))->count();
                $lests = M('Product_new_visiter_data')->where(array('token'=>$token,'cate_id'=>$cate_id,'product_id'=>$product_id,'time'=>$times))->count('distinct(ip)');
                $c = $c.$lest.",";
                $d = $d.$lests.",";


            }elseif($cate_id){
                $times = array(array('gt',$i),array('lt',$i+3600*24));
                $lest = M('Product_new_visiter_data')->where(array('token'=>$token,'cate_id'=>$cate_id,'time'=>$times))->count();
                $lests = M('Product_new_visiter_data')->where(array('token'=>$token,'cate_id'=>$cate_id,'time'=>$times))->count('distinct(ip)');
                $c = $c.$lest.",";
                $d = $d.$lests.",";

            }else{

                $times = array(array('gt',$i),array('lt',$i+3600*24));

                $lest = M('Product_new_visiter_data')->where(array('token'=>$token,'time'=>$times))->count();
                $lests = M('Product_new_visiter_data')->where(array('token'=>$token,'time'=>$times))->count('distinct(ip)');
                $c = $c.$lest.",";
                $d = $d.$lests.",";

            }
        }

        $k = $k."]";
        $k = preg_replace('/,]/','',$k);
        $karray = explode(',',$k);
        $karray = array_reverse($karray);
        $o = "[";
        foreach($karray as $key=>$value){
            $o = $o."\"".$value."\"".",";
        }
        $o = $o."]";
        $o = preg_replace('/,]/',']',$o);

        $c = $c."]";
        $c = preg_replace('/,]/','',$c);
        $carray = explode(',',$c);

        $carray = array_reverse($carray);
        $e = "[";
        foreach($carray as $key=>$value){
            $e = $e."$value".",";
        }
        $e = $e."]";
        $e = preg_replace('/,]/',']',$e);


        $d = $d."]";
        $d = preg_replace('/,]/','',$d);
        $darray = explode(',',$d);

        $darray = array_reverse($darray);
        $g = "[";
        foreach($darray as $key=>$value){
            $g = $g."$value".",";
        }
        $g = $g."]";
        $g = preg_replace('/,]/',']',$g);
        $this->assign('text',$e);
        $this->assign('test',$o);
        $this->assign('texts',$g);
        /*print_r($c);
        echo "<br/>";
        print_r($d);
        exit;*/
       //$this->assign('number',count($j));
        /*饼图1*/
        $timess = array(array('gt',time()-604800),array('lt',time()));
        $dopenid = array('neq',null);
//        $s = M('Product_new_visiter_data')->where(array('token'=>$token,/*'time'=>$timess,*/'$dopenid'=>$dopenid))->select();
//        $g = M('Product_new_visiter_data')->where(array('token'=>$token,/*'time'=>$timess,*/))->select();
        $resule = M('Product_new_visiter_data')->where(array('token'=>$token,'time'=>$timess,'$dopenid'=>$dopenid))->count();
        $resules = M('Product_new_visiter_data')->where(array('token'=>$token,'time'=>$timess))->count();
        $resulet = $resules - $resule;
        $this->assign('resule',$resule);
        $this->assign('resulet',$resulet);
        /*饼图2*/
        //SELECT *,count(address) as counts FROM `tp_product_new_visiter_data` WHERE token = 'f17f0d1e02a8976cf065163525547260' GROUP BY address;
        //$data=$modal->field("count(*) as count,name")->group("name")->select();
        $ests = M('Product_new_visiter_data')->field("count(*) as count,address")->where(array('token'=>$token,'time'=>$timess))->group('address')->order('count desc')->limit(5)->select();
        $home = "[";
        $homes = "[";
        foreach($ests as $key=>$value){
            $ests[$key]['address'] = str_replace(',','',$value['address']);
           // "\"".date("Y-m-d",$i)."\"".","; {value:335, name:'其他'},
            $home = $home."'".$ests[$key]['address']."',";
            $homes = $homes."{value:".$ests[$key]['count'].", name:"."'".$ests[$key]['address']."'}".",";
        }
        $home = $home."]";
        $home = preg_replace('/,]/',']',$home);
        $this->assign('home',$home);

        $homes = $homes."]";
        $homes = preg_replace('/,]/',']',$homes);
        $this->assign('homes',$homes);


        //print_r($homes);exit;


        /*echo "<pre>";
        print_r($s);exit;*/
        /*实时在线*/

        $count      = M('Product_new_visiter_data')->where(array('token'=>$token))->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $tobe = M('Product_new_visiter_data')->where(array('token'=>$token))->limit($Page->firstRow.','.$Page->listRows)->order('time desc')->select();
        foreach($tobe as $key => $value){
            $product = M('Product_cat_new')->where(array('token'=>$token,'id'=>$value['cate_id']))->find();
            $products = M('Product')->where(array('token'=>$token,'cate_id'=>$value['cate_id'],'id'=>$value['product_id']))->find();
            $tobe[$key]['cate'] = $product['name'];
            $tobe[$key]['product'] = $products['name'];
        }
        $this->assign('set',$tobe);
        $this->assign('page', $show);
        $this->display();
    }


    /**
     * 团购
     */
    public function tg()
    {
        if (IS_POST) {
            $where['tp_product_new.name'] = array('like', "%" . $_POST['name'] . "%");
            $oProduct_new_tg = M('Product_new_tg');
            $where['tp_product_new_tg.token'] = $this->token;
            $count = $oProduct_new_tg->join("join tp_product_new on tp_product_new.id=tp_product_new_tg.pid")->where($where)->count();
            $Page = new Page($count, 20);
            $show = $Page->show();
            $list = $oProduct_new_tg->join("join tp_product_new on tp_product_new.id=tp_product_new_tg.pid")
                ->field('tp_product_new.price,tp_product_new.name,tp_product_new.time,tp_product_new_tg.id,tp_product_new_tg.pid,tp_product_new_tg.price as tg_price,tp_product_new_tg.end_time')
                ->where($where)
                ->limit($Page->firstRow . ',' . $Page->listRows)->select();

        } else {
            $oProduct_new_tg = M('Product_new_tg');
            $where['tp_product_new_tg.token'] = $this->token;
            $count = $oProduct_new_tg->join("join tp_product_new on tp_product_new.id=tp_product_new_tg.pid")->where($where)->count();
            $Page = new Page($count, 20);
            $show = $Page->show();
            $list = $oProduct_new_tg->join("join tp_product_new on tp_product_new.id=tp_product_new_tg.pid")
                ->field('tp_product_new.price,tp_product_new.name,tp_product_new.time,tp_product_new_tg.id,tp_product_new_tg.pid,tp_product_new_tg.price as tg_price,tp_product_new_tg.end_time')
                ->where($where)
                ->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 添加团购商品
     */
    public function addtg()
    {
        if (IS_POST) {

            $oProduct_new_tg = M('Product_new_tg');
            $_POST['token'] = $this->token;

            $_POST['start_time'] = strtotime($this->_post('start_time'));
            $_POST['end_time'] = strtotime($this->_post('end_time'));
            $oProduct_new_tg->create();


            if ($oProduct_new_tg->add()) {
                $data['status'] = 1;
                M('Product_new')->where(array('id' => $_POST['pid']))->save($data);
                $this->success2("添加成功", U('tg', array('token' => $this->token)));
            } else {
                $this->error2('添加失败', U('tg', array('token' => $this->token)));
            }
        } else {

            $plist = M('Product_new')->where(array('token' => $this->token, 'status' => 0))->select();
            $this->assign('plist', $plist);

            $this->display();
        }
    }

    /**
     * 修改团购商品
     */
    public function tg_edit()
    {
        if (IS_POST) {

            $_POST['start_time'] = strtotime($this->_post('start_time'));
            $_POST['end_time'] = strtotime($this->_post('end_time'));
            $_POST['token'] = $this->token;
            if (M('Product_new_tg')->save($_POST)) {
                if ($_POST['pid'] != $_POST['opid']) {//修改了商品
                    $data['status'] = 0;
                    M('Product_new')->where(array('id' => $_POST['opid']))->save($data);//旧商品变为0状态
                    $data1['status'] = 1;
                    M('Product_new')->where(array('id' => $_POST['pid']))->save($data1);//修改过来的商品状态为1
                }
                $this->success2("修改成功", U('tg', array('token' => $this->token)));

            } else {
                $this->error2("修改成功", U('tg', array('token' => $this->token)));
            }
        } else {
            $tg_shop = M('Product_new_tg')->find($_GET['id']);
            $this->assign('tg', $tg_shop);
            $plist = M('Product_new')->where(array('token' => $this->token, 'status' => 0))->select();
            $a = M('Product_new')->where(array('id' => $tg_shop['pid']))->find();
            $plist[] = $a;
            $this->assign('plist', $plist);
            $this->display();
        }
    }

    /**
     * 删除团购商品
     */
    public function tg_del()
    {
        $id = $this->_get('id');
        $pid = M('Product_new_tg')->where(array('id' => $id))->getField('pid');
        if (M('Product_new_tg')->delete($id)) {
            $data['status'] = 0;
            M('Product_new')->where(array('id' => $pid))->save($data);
            $this->success2("删除成功", U('tg', array('token' => $this->token)));
        } else {
            $this->error2('删除失败', U('tg', array('token' => $this->token)));
        }
    }

    /**
     * 预售商品
     */
    public function yg()
    {
        if (IS_POST) {
            $where['tp_product_new.name'] = array('like', "%" . $_POST['name'] . "%");
            $oProduct_new_tg = M('Product_yg');
            $where['tp_product_yg.token'] = $this->token;
            $count = $oProduct_new_tg->join("join tp_product_new on tp_product_new.id=tp_product_yg.pid")->where($where)->count();
            $Page = new Page($count, 20);
            $show = $Page->show();
            $list = $oProduct_new_tg->join("join tp_product_new on tp_product_new.id=tp_product_yg.pid")
                ->field('tp_product_new.price,tp_product_new.name,tp_product_new.time as add_time,tp_product_yg.id,tp_product_yg.pid,tp_product_yg.price as yg_price,tp_product_yg.time')
                ->where($where)
                ->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $oProduct_new_tg = M('Product_yg');
            $where['tp_product_yg.token'] = $this->token;
            $count = $oProduct_new_tg->join("join tp_product_new on tp_product_new.id=tp_product_yg.pid")->where($where)->count();
            $Page = new Page($count, 20);
            $show = $Page->show();
            $list = $oProduct_new_tg->join("join tp_product_new on tp_product_new.id=tp_product_yg.pid")
                ->field('tp_product_new.price,tp_product_new.name,tp_product_new.time as add_time,tp_product_yg.id,tp_product_yg.pid,tp_product_yg.price as yg_price,tp_product_yg.time')
                ->where($where)
                ->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 添加预售商品
     */
    public function addyg()
    {
        if (IS_POST) {
            $oMoel = M('Product_yg');
            $_POST['token'] = $this->token;
            $_POST['time'] = strtotime($this->_post('time'));
            $oMoel->create();
            if ($oMoel->add()) {
                $data['status'] = 2;
                M('Product_new')->where(array('id' => $_POST['pid']))->save($data);
                $this->success2("添加成功", U('yg', array('token' => $this->token)));
            } else {
                $this->error2('添加失败', U('yg', array('token' => $this->token)));
            }

        } else {
            $plist = M('Product_new')->where(array('token' => $this->token, 'status' => 0))->select();
            $this->assign('plist', $plist);
            $this->display();
        }
    }
    /**
     * 修改预售商品
     */
    public function yg_edit(){
        if (IS_POST) {
            $_POST['time'] = strtotime($this->_post('time'));
            $_POST['token'] = $this->token;
            if (M('Product_yg')->save($_POST)) {
                if ($_POST['pid'] != $_POST['opid']) {//修改了商品
                    $data['status'] = 0;
                    M('Product_new')->where(array('id' => $_POST['opid']))->save($data);//旧商品变为0状态
                    $data1['status'] =2;
                    M('Product_new')->where(array('id' => $_POST['pid']))->save($data1);//修改过来的商品状态为1
                }
                $this->success2("修改成功", U('yg', array('token' => $this->token)));

            } else {
                $this->error2("修改成功", U('yg', array('token' => $this->token)));
            }
        }else{
            $tg_shop = M('Product_yg')->find($_GET['id']);
            $this->assign('tg', $tg_shop);
            $plist = M('Product_new')->where(array('token' => $this->token, 'status' => 0))->select();
            $a = M('Product_new')->where(array('id' => $tg_shop['pid']))->find();
            $plist[] = $a;
            $this->assign('plist', $plist);
            $this->display();
        }
 }
    /**
     * 删除预售商品
     */
    public function del_yg(){
        $id = $this->_get('id');
        $pid = M('Product_yg')->where(array('id' => $id))->getField('pid');
        if (M('Product_yg')->delete($id)) {
            $data['status'] = 0;
            M('Product_new')->where(array('id' => $pid))->save($data);
            $this->success2("删除成功", U('yg', array('token' => $this->token)));
        } else {
            $this->error2('删除失败', U('yg', array('token' => $this->token)));
        }
    }
    /**
     * 品牌列表
     */
    public function brand(){
        if(IS_POST){
            $model = M('Product_brand');
            $where['tp_product_brand.name'] = array('like', "%" . $_POST['name'] . "%");
            $where['tp_product_brand.token'] = $this->token;
            $count = $model->join("join tp_product_cat_new on tp_product_cat_new.id=tp_product_brand.cid")->field('tp_product_brand.id,tp_product_brand.name,tp_product_brand.add_time,tp_product_brand.cid,tp_product_cat_new.name as cname')->where($where)->count();
            $Page = new Page($count, 20);
            $show = $Page->show();
            $list = $model->join("join tp_product_cat_new on tp_product_cat_new.id=tp_product_brand.cid")->field('tp_product_brand.id,tp_product_brand.name,tp_product_brand.add_time,tp_product_cat_new.name as cname')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('add_time desc')->select();
        }else{
            $model = M('Product_brand');
            $where['tp_product_brand.token'] = $this->token;
            $count = $model->join("join tp_product_cat_new on tp_product_cat_new.id=tp_product_brand.cid")->field('tp_product_brand.id,tp_product_brand.name,tp_product_brand.add_time,tp_product_brand.cid,tp_product_cat_new.name as cname')->where($where)->count();
            $Page = new Page($count, 20);
            $show = $Page->show();
            $list = $model->join("join tp_product_cat_new on tp_product_cat_new.id=tp_product_brand.cid")->field('tp_product_brand.id,tp_product_brand.name,tp_product_brand.add_time,tp_product_brand.cid,tp_product_cat_new.name as cname')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('tp_product_brand.add_time desc')->select();
        }

        $this->assign('page', $show);
        $this->assign('list', $list);

        $this->display();
    }
    /**
     * 品牌文章列表页
     */
    public function abrand_list(){
        $model = M('Product_abrand');
        $where['bid'] = $this->_get('bid');
        $count = $model->where($where)->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $list = $model->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('sort desc,addtime desc')->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }
    /**
     * 品牌文章核审
     */
    public function heshen(){
        $model=M('Product_abrand');
        $model->create();
        $id=$this->_get('id');
        $data['status']=$this->_get('status');

        if($model->where(array('id'=>$id))->save($data)){
            $this->success2("操作成功", U('abrand_list', array('token' => $this->token,'bid'=>$_GET['bid'])));
        }else{
            $this->error2("操作失败", U('abrand_list', array('token' => $this->token,'bid'=>$_GET['bid'])));
        }

    }
    /**
     * 添加品牌
     */
    public function add_brand(){
        $model=M('Product_brand');
        if(IS_POST){
            $_POST['add_time']=time();
            $_POST['token']=$this->token;
            $_POST['pwd']=MD5($_POST['pwd']);
            $model->create();
            if($model->add()){
                $this->success2("添加成功", U('brand', array('token' => $this->token)));
            }else{
                $this->error2('添加失败', U('brand', array('token' => $this->token)));
            }
        }
        $catList=M('Product_cat_new')->where(array('token'=>$this->token,'parentid'=>0))->select();
        $this->assign('catList', $catList);
        $this->display();
    }
    /**
     * 修改品牌
     */
    public function br_edit(){
        if (IS_POST) {
            $_POST['token'] = $this->token;
            $opwd=M('Product_brand')->where(array('id'=>$_POST['id']))->getField('pwd');
            if($_POST['pwd']!=$opwd){
                $_POST['pwd']=MD5($_POST['pwd']);
            }
            if (M('Product_brand')->save($_POST)) {
                $this->success2("修改成功", U('brand', array('token' => $this->token)));

            } else {
                $this->error2("修改成功", U('brand', array('token' => $this->token)));
            }
        }else{
            $catList=M('Product_cat_new')->where(array('token'=>$this->token,'parentid'=>0))->select();
            $this->assign('catList', $catList);
            $tg_shop = M('Product_brand')->find($_GET['id']);
            $this->assign('tg', $tg_shop);
            $this->display();
        }
    }
    /**
     *删除品牌
     */
    public function del_brand(){
        $id = $this->_get('id');
        if (M('Product_brand')->delete($id)) {
            $this->success2("删除成功", U('brand', array('token' => $this->token)));
        } else {
            $this->error2('删除失败', U('brand', array('token' => $this->token)));
        }
    }

    /*
     * 商城文字广告
     * */
    public function gfont(){
        $oFontModel = M('Product_gfont');
        if(IS_AJAX){
            $iTem = $oFontModel->where(array('token'=>$this->token))->find();
            $_POST['token'] = $this->token;
            $_POST['add_time'] = date('Y-m-d H:i:s');
            if($iTem){
                $savefont = $oFontModel->where(array('token'=>$this->token))->save($_POST);
                if($savefont){
                    $this->success2("修改成功", U('Store_new/gfont', array('token' => $this->token)));
                }else{
                    $this->error2('修改失败', U('Store_new/gfont', array('token' => $this->token)));
                }
            }else{
                $addfont = $oFontModel->add($_POST);
                if($addfont){
                    $this->success2("添加成功", U('Store_new/gfont', array('token' => $this->token)));
                }else{
                    $this->error2('添加失败', U('Store_new/gfont', array('token' => $this->token)));
                }
            }
        }else{
            $info = $oFontModel->where(array('token'=>$this->token))->find();
            $this->assign('info',$info);
        }
        $this->display();
    }



    /*
     * 发送系统消息（列表，添加编辑，删除）
     * */
    public function systeminfo(){
        $db  = M('Product_systeminfo');
        $where['token']=session('token');
        $count=$db->where($where)->count();
        $page=new Page($count,15);
        $info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('add_time desc')->select();
        $this->assign('page',$page->show());
        $this->assign('list',$info);
        $this->display();
    }
    public function insetsystem(){
        $oFontModel = M('Product_systeminfo');
        if(IS_AJAX){
            $fid = $_POST['id'];
            $_POST['token'] = $this->token;
            $_POST['add_time'] = date('Y-m-d H:i:s');
            if($fid){
                $savefont = $oFontModel->where(array('id'=>$fid))->save($_POST);
                if($savefont){
                    $this->success2("修改成功", U('Store_new/systeminfo', array('token' => $this->token)));
                }else{
                    $this->error2('修改失败', U('Store_new/insetsystem', array('token' => $this->token)));
                }
            }else{
                $addfont = $oFontModel->add($_POST);
                if($addfont){
                    $this->success2("添加成功", U('Store_new/systeminfo', array('token' => $this->token)));
                }else{
                    $this->error2('添加失败', U('Store_new/insetsystem', array('token' => $this->token)));
                }
            }
        }else{
            $gid = $_GET['id'];
            $info = $oFontModel->where(array('id'=>$gid))->find();
            $this->assign('info',$info);
        }
        $this->display();
    }
    public function delsystem(){
        $id = $this->_get('id');
        if (M('Product_systeminfo')->delete($id)) {
            $this->success2("删除成功", U('Store_new/systeminfo', array('token' => $this->token)));
        } else {
            $this->error2('删除失败', U('Store_new/systeminfo', array('token' => $this->token)));
        }
    }

    /**
     * 商城广告
     */
    public function gg()
    {

        $db=M('Imag');
        $type=$db->where(array('token'=>session('token'),'app'=>'Store_new'))->getField('MAX(type)');//位置来到第几了
        $this->assign('type',$type);
        $where['token']=session('token');
        $count=$db->where($where)->count();
        $page=new Page($count,15);
        $info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('type')->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);

        $this->display();
    }



    /**
     * 添加商城广告
     */
    public function ggadd(){
        if(IS_POST){
            $_POST['token'] = session('token');
            $_POST['add_time'] = date('Y-m-d h:i:s',time());
            if(M('Imag')->add($_POST)){
                $this->success('操作成功', U(MODULE_NAME . '/gg'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/gg'),true);
            }
        }else {
            $db=M('Imag');
            $type=$db->where(array('token'=>session('token'),'app'=>'Store_new'))->getField('MAX(type)');//位置来到第几了
            $this->assign('type',$type);
            $this->display();
        }
    }

    /**
     * 删除商城广告
     */
    public function ggdel(){
        $where['id']=$this->_get('id','intval');
        $where['token']=session('token');
        if(M('Imag')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/gg'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/gg'));
        }
    }

    /**
     * 修改商城广告
     */
    public function ggedit(){
        if(IS_POST){
            $model=M('Imag');
            $_POST['token']=session('token');
            $model->create();
            if($model->save()){
                $this->success('操作成功', U(MODULE_NAME . '/gg'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/gg'),true);
            }
        }else {
            $where['id'] = $this->_get('id', 'intval');
            $where['token'] = session('token');
            $data = M('Imag')->where($where)->find();
            $this->assign('data', $data);
            $this->display();
        }
    }


    /**
     * 供货商列表
     */
    public function supplier() {
        $oSupModel = M('Product_supplier');
        $aWhere = array('token'=>$this->token);
        $count = $oSupModel->where($aWhere)->count();
        $Page       = new Page($count,20);
        $aList = $oSupModel->where($aWhere)->limit($Page->firstRow.','.$Page->listRows)->order('add_time desc')->select();
        $this->assign(array(
            'list'=>$aList,
            'page'=>$Page->show()
        ));
        $this->display();
    }

    /*添加编辑供货商*/
    public function cerateSup(){
        $oSupModel = M('Product_supplier');
        if(IS_AJAX){
            $data = !empty($_POST['id'])?$_POST['id']:'';

            if($data){
                #编辑
                $iTem = $oSupModel->where(array('id'=>$data))->find();
                if(!$iTem) $this->error2('非法操作');
                $_POST['password'] = md5($_POST['password']);
                if($oSupModel->where(array('id'=>$data))->save($_POST)){
                    $this->success('编辑成功',U('Store_new/supplier',array('token'=>$this->token)));
                }else{
                    $this->success('编辑失败',U('Store_new/cerateSup',array('token'=>$this->token,'id'=>$data)));
                }
            }else{
                #新增
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['password'] = md5($_POST['password']);
                if($oSupModel->add($_POST)){
                    $this->success('添加成功',U('Store_new/supplier',array('token'=>$this->token)));
                }else{
                    $this->success('添加失败',U('Store_new/cerateSup',array('token'=>$this->token,'id'=>$data)));
                }
            }
        }else{
            $iSid = $_GET['id'];
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Store_new/supplier', array('token' => $this->token)),
                        'name' => '返回'
                    )
                ),
                'supplier'=>$oSupModel->where(array(
                    'id'=>$iSid
                ))->find()
            ));
            $this->display();
        }
    }

    /*删除供货商*/
    public function delSup(){
        $oSupModel = M('Product_supplier');
        $iTem = $oSupModel->where(array('id'=>$_GET['id']))->find();
        if(!$iTem) $this->error2('非法操作');
        if($oSupModel->where(array('id'=>$_GET['id']))->delete()){
            $this->success('删除成功',U('Store_new/supplier',array('token'=>$this->token)));
        }else{
            $this->error('删除失败',U('Store_new/supplier',array('token'=>$this->token)));
        }
    }

    /*供货商供货的商品审核*/
    public function check(){
        $oProductModel = M('Product_new');
        $aWhere = array('id'=>$_GET['id']);
        $iTem = $oProductModel->where($aWhere)->find();
        if(!$iTem) $this->error2('非法操作');
        if($oProductModel->where($aWhere)->save(array('status'=>0))){
            /*$this->redirect()*/
            $this->success('审核成功',U('Store_new/product',array('token'=>$this->token,'cid'=>$_GET['cid'])), false);
        }else{
            $this->error('审核失败',U('Store_new/product',array('token'=>$this->token,'cid'=>$_GET['cid'])), false);
        }
    }
    /**
     * 佣金管理
     */
    public function yj(){
        $db=M('Media_users');
        $where['tp_media_users.token']=$this->token;
        $where['tp_media_users.status']=1;
        $where['tp_media_users.from_openid']=array('neq', '');
        $count=$db->where($where)->count();
        $page=new Page($count,25);
        $list=$db->field('tp_media_users.id,tp_media_users.openid,tp_media_users.yongjin,tp_media_users.add_time')
        //->join("left join tp_wxusers on tp_wxusers.openid=tp_media_users.openid")
        ->where($where)->limit($page->firstRow.','.$page->listRows)
        ->order('tp_media_users.yongjin desc')
        ->select();

        $aOpenid = array();
        foreach ($list as $Row) {
            $aOpenid[] = $Row['openid'];
        }
	if($aOpenid){
	$aUser = Arr::changeIndexToKVMap(
            M('wxusers')->where(array('openid' => array('in', $aOpenid)))->select()
        ,'openid', 'nickname');
	}else{
		$aUser = array();
	}

        foreach ($list as $key => $L) {
            $list[$key]['nickname'] = Arr::get($aUser, $L['openid']);
        }
        #分销统计
        $this->stat_fx($db);
        $this->assign('page',$page->show());
        $this->assign('list', $list);
        $this->display();
    }

    /*
     *  分销统计
     */
    public function stat_fx($Model)
    {
        $Commission = M('Edia_user_commission');
        $aBase['token']=$_REQUEST['token'];
        $aBase['from_openid']=array('neq', '');
        #今天
        $aToday = array(
            array('gt',date('Y-m-d')),
            array('lt', date('Y-m-d',strtotime('+1 days')))
        );
        $aIToday = array(
            array('gt',strtotime(date('Y-m-d'))),
            array('lt', strtotime('+1 days'))
        );
        #本月
        $aMonth = array(
            array('gt',date('Y-m-01')),
            array('lt', date('Y-m-d',strtotime('+1 days')))
        );
        $aIMonth = array(
            array('gt',strtotime(date('Y-m-01'))),
            array('lt', strtotime('+1 days'))
        );
        $aStat = array();
        //今日分销人数
        $aStat['today_num'] = $Model->where(array_merge($aBase, array(
            'add_time' => $aToday
        )))->count();
        //今日分销金额
        $aStat['today_yj'] = (int)$Commission->where(array_merge($aBase, array(
            'add_time' => $aIToday,
            'status'   => 1
        )))->sum('yj');
        //本月分销人数
        $aStat['month_num'] = $Model->where(array_merge($aBase, array(
            'add_time' => $aMonth
        )))->count();
        //本月分销金额
        $aStat['month_yj'] = $Commission->where(array_merge($aBase, array(
            'add_time' => $aIMonth,
            'status'   => 1
        )))->sum('yj');
        //总分销人数
        $aStat['total_num'] = $Model->where($aBase)->count();
        //总分销金额
        $aStat['total_yj'] = $Commission->where(array_merge($aBase, array('status' => 1)))->sum('yj');
        $this->assign('stat', $aStat);
    }
    /**
     * 删除佣金人
     */
    public function del_yj(){
        $id=$this->_get(id);
        $data['status']=0;

        if(M('Media_users')->where(array('id'=>$id))->save($data)){
            $this->success2("操作成功",U('Store_new/yj',array('token'=>$this->token)));
        }else{
            $this->error2('操作失败',array('token'=>$this->token));
        }

    }
    /**
     * 个人结算列表
     */
    public function js(){
        $db=M('Edia_user_commission');
        if(IS_POST){
            if($_POST['status']<2){
                $where['status']=$this->_post('status');
            }
            if($_POST['statdate']&&$_POST['enddate']){
                $stata=strtotime($_POST['statdate']);
                $end=strtotime($_POST['enddate']);
                $where['add_time']=array('between',array($stata,$end));
            }
            $where['token']=$this->token;
            $where['openid']=$this->openid;
            $count=$db->where($where)->count();
            $page=new Page($count,25);
            $list=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('')->select();
            $this->assign('page',$page->show());
            $this->assign('list', $list);
            $this->display();
        }else{

            $where['token']=$this->token;
            $where['openid']=$this->openid;

            $count=$db->where($where)->count();
            $page=new Page($count,25);
            $list=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('')->select();
            $this->assign('page',$page->show());
            $this->assign('list', $list);
            $this->display();
        }

    }
    /**
     * 正式结算
     */
    public function jiesu(){
        /**
         * 异步ajax过来，一键结算
         */
        if(IS_AJAX){
            $id=$this->_post('id');
            $id=explode('-',$id);
            $id=array_filter($id);
            $js = M('Edia_user_commission');
            $data['status'] = 1;
            if($js->where(array('id' =>array('in',$id)))->save($data)){
                foreach($id as $v){
                    $yongjin = $js->where(array('id' => $v))->getField('yj');
                    M('Media_users')->where(array('token' => $_GET['token'], 'openid' => $_GET['openid']))->setInc('yongjin', $yongjin);
                }

                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else {
            /**
             * 这里是单个跳转过来结算
             */
            $js = M('Edia_user_commission');
            $id = $this->_get('id');
            $yongjin = $js->where(array('id' => $id))->getField('yj');
            $data['status'] = 1;
            if ($js->where(array('id' => $id))->save($data) && M('Media_users')->where(array('token' => $_GET['token'], 'openid' => $_GET['openid']))->setInc('yongjin', $yongjin)) {
                $this->success2("成功", U('Store_new/js', array('token' => $_GET['token'])));
            } else {
                $this->error2("失败", U('Store_new/js', array('token' => $_GET['token'])));
            }
        }
    }
    /*
     * 提现管理
     * */
    public function tixianinfo(){
        $otiModel = M('Tixianjl');
        $oUserModel = M('Media_users');
        $where = array(
            'token'=>$this->token
        );
        $count = $otiModel->where($where)->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $alist = $otiModel->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($alist as $k=>$val){
           // $aUser = $oUserModel->where(array('token'=>$this->token,'openid'=>$val['openid']))->find();
            /**
             * 这里修改了，拿用户呢称
             */
            $usersModel = M('Wxusers');
            $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $userdata = $usersModel->where(array('uid'=>$wxuser['id'],'status'=>1,'openid'=>$val['openid']))->find();
            $alist[$k]['nickname'] = $userdata['nickname'];
        }
        $this->assign(array(
            'alist'=>$alist,
            'page'=>$show
        ));
        $this->display();
    }
    /**
     * 提现查看详情
     */
    public function ticheck(){
        $otiModel = M('Tixianjl');
        $oUserModel = M('Media_users');
        $iTem = $otiModel->where(array('id'=>$_GET['id']))->find();
       // p($iTem);die;
        $this->assign('iTem',$iTem);
        //print_r($iTem);exit;
        $aUser = $oUserModel->where(array('token'=>$this->token,'openid'=>$iTem['openid']))->find();
        if(IS_AJAX){
            $iTems =$otiModel->where(array('id'=>$_POST['id']))->find();

            if($iTems == false){
                $this->error2('非法操作');
            }
            $_POST['check_time'] = date('Y-m-d H:i:s');
            if($otiModel->where(array('id'=>$_POST['id']))->save($_POST)){
                if($_POST['status']==4){//未通过时把钱还回给他
                    $info=$otiModel->where(array('id'=>$_POST['id']))->find();
                     $oUserModel->where(array('token'=>$this->token,'openid'=>$info['openid']))->setInc('yongjin',$info['number']);
                }

                $this->success('操作成功！',U(MODULE_NAME.'/tixianinfo',array('token'=>session('token'))));
            }else{
                $this->error('操作失败！',U(MODULE_NAME.'/ticheck',array('token'=>session('token'),'id'=>$_POST['id'])));
            }
        }
        /*print_r($iTem);
        echo '<br/>';
        print_r($aUser);*/

        $this->assign(array(
            'item'=>$iTem,
            'users'=>$aUser,
            'ExtraBtn' => array(
                array(
                    'url'  => U('Store_new/tixianinfo', array('token' => $this->token)),
                    'name' => '返回'
                )
            )
        ));

        $this->display();
    }


    public function mru()
    {

    	$this->product_model = M('Product_new');
    	$this->product_cat_model = M('Product_cat_new');
    	$product_cart_model = M('product_cart_new');
    	$this->product_detail_model = M('Product_detail_new');
    	//print_r($this->token);exit;

    	$thisOrder = $product_cart_model->where(array('id' => intval($_GET['id']),'token'=>$this->token))->find();
    	$quan = M('Sn')->where(array('id'=>intval($thisOrder['sid']),'token'=>$this->token))->find();
       	//查出商品名 张湘南
       	$thisOrder['title']=M('mru_qianggou')->where(array('id'=>$thisOrder['productid']))->getField('title');
    	//print_r($thisOrder);exit;
    	//        print_r($thisOrder['info']);
    	//
    	//检查权限

    	if (IS_POST) {
    		// print_r($_POST);exit;
    		if (intval($_POST['sent'])) {
    			$_POST['handled'] = 1;
    		}
    		$product_cart_model->where(array('id' => $thisOrder['id']))->save(array('sent' => intval($_POST['sent']), 'paid' => intval($_POST['paid']), 'logistics' => $_POST['logistics'], 'logisticsid' => $_POST['logisticsid'], 'handled' => 1));
    		//TODO 发货的短信提醒
    		if ($_POST['sent']) {
    			$company = D('Company_new')->where(array('token' => $thisOrder['token'], 'isbranch' => 0))->find();
    			$userInfo = D('Userinfo_new')->where(array('token' => $thisOrder['token'], 'wecha_id' => $thisOrder['wecha_id']))->find();
    			Sms::sendSms($this->token, "您在{$company['name']}商城购买的商品，商家已经给您发货了，请您注意查收", $userInfo['tel']);
    		}

    		//
    		/************************************************/
    		if (intval($_POST['paid']) && intval($thisOrder['price'])) {
    			$member_card_create_db = M('Member_card_create_new');
    			$wecha_id = $thisOrder['wecha_id'];
    			$userCard = $member_card_create_db->where(array('token' => $this->token, 'wecha_id' => $wecha_id))->find();
    			$member_card_set_db = M('Member_card_set_new');
    			$thisCard = $member_card_set_db->where(array('id' => intval($userCard['cardid'])))->find();
    			$set_exchange = M('Member_card_exchange_new')->where(array('cardid' => intval($thisCard['id'])))->find();
    			//
    			$arr['token'] = $this->token;
    			$arr['wecha_id'] = $wecha_id;
    			$arr['expense'] = $thisOrder['price'];
    			$arr['time'] = time();
    			$arr['cat'] = 99;
    			$arr['staffid'] = 0;
    			$arr['score'] = intval($set_exchange['reward']) * $order['price'];
    			M('Member_card_use_record_new')->add($arr);
    			$userinfo_db = M('Userinfo_new');
    			$thisUser = $userinfo_db->where(array('token' => $thisCard['token'], 'wecha_id' => $arr['wecha_id']))->find();
    			$userArr = array();
    			$userArr['total_score'] = $thisUser['total_score'] + $arr['score'];
    			$userArr['expensetotal'] = $thisUser['expensetotal'] + $arr['expense'];
    			$userinfo_db->where(array('token' => $thisCard['token'], 'wecha_id' => $arr['wecha_id']))->save($userArr);
    		}
    		/************************************************/
    		//

    		$this->success('修改成功', U('Store_new/orderInfo', array('token' => session('token'), 'id' => $thisOrder['id'])));
    	} else {
    		//订餐信息
    		//   $product_diningtable_model = M('product_diningtable_new');
    		// print_r($product_diningtable_model);exit;
    		//            if ($thisOrder['tableid']) {
    		//                $thisTable=$product_diningtable_model->where(array('id'=>$thisOrder['tableid']))->find();
    		//                $thisOrder['tableName']=$thisTable['name'];
    		//            }
    		//
    		if($thisOrder['type'] == 'par'){
    			$opinfo = $this->product_model->where(array('id' => $thisOrder['productid'],'token'=>$this->token))->find();
    			$priceCount = $thisOrder['price']*$thisOrder['total']+$thisOrder['mailpay'];
    			$this->assign('products',$opinfo);
    			$this->assign('totalCount',$thisOrder['total']);
    			$this->assign('priceCount',$priceCount);

    		}
    		if($thisOrder['type'] == 'son'){
    			if ($thisOrder['productid'] == 0 && $thisOrder['info'] != "") {

    				$data = $thisOrder['info'];
    				$data = substr($data, 0, -1);
    				$ruldata = explode('/', $data);
    				$host = array();
    				foreach($ruldata as $k=>$rul){
    					$host[] = explode('-',$rul);
    				}
    				$total = 0;
    				$price = 0;
    				foreach($host as $c=>$crul){

    					if($crul[3] =='par'){

    						$ginfo = $this->product_model->where(array('id' => $crul[0],'token'=>$this->token))->find();

    						$host[$c][4]=$ginfo['logourl'];
    						$host[$c][5]=$ginfo['name'];

    					}elseif($crul[3] =='son'){

    						$tinfo = $this->product_detail_model->where(array('id' => $crul[0],'token'=>$this->token))->find();


    						$ginfo = $this->product_model->where(array('id' => $tinfo['pid'],'token'=>$this->token))->find();
    						$linfo = M('Norms_new')->where(array('id' => $tinfo['format'],'token'=>$this->token))->find();
    						$vinfo = M('Norms_new')->where(array('id' => $tinfo['color'],'token'=>$this->token))->find();
    						$jinfo = M('Product_cat_new')->where(array('id'=>$vinfo['catid'],'token'=>$this->token))->find();
    						$host[$c][4] = $ginfo['logourl'];   // 商品图片
    						$host[$c][5] = $ginfo['name'];      // 商品名称
    						$host[$c][6] = $jinfo['norms'];     // 商品规格
    						$host[$c][7] = $jinfo['color'];     // 商品外观
    						$host[$c][8] = $linfo['value'];     // 商品规格值
    						$host[$c][9] = $vinfo['value'];     // 商品外观值

    						$host[$c][10] = $vinfo['catid'];     // 商城ID值

    						//array_push($crul,$ginfo['logourl'] ,$ginfo['name'],$jinfo['norms'],$jinfo['color'],$linfo['value'],$vinfo['value']);
    						// print_r($crul);exit;
    					}
    					$total+=intval($crul[1]);
    					$price += intval($crul[2]*$crul[1]);

    				}
    				$price = intval($price + $thisOrder['mailpay']);

    				$this->assign('host',$host);
    				$this->assign('totalCount',$total);
    				$this->assign('priceCount',$price);



    				/*              if ($thisOrder['type'] == 'par') {
    				 $ginfo = array();
    				$total=0;
    				$price = 0;
    				foreach ($ruldata as $k => $host) {
    				//print_r($host);exit;
    				$value = explode('-', $host);
    				$total+=intval($value[1]);
    				$price += intval($value[2]);
    				$ginfo[] = $this->product_model->where(array('id' => $value[0]))->find();

    				}


    				$this->assign('products', $ginfo);
    				$this->assign('totalCount',$total);
    				$this->assign('priceCount',$price);
    				} else {

    				$linfo = array();
    				// print_r($ruldata);exit;
    				$total=0;
    				$price = 0;
    				foreach ($ruldata as $t => $chost) {
    				//print_r($t);exit;
    				$value = explode('-', $chost);
    				$total+=intval($value[1]);
    				$price+=intval($value[2]);
    				$linfo[] = $this->product_detail_model ->where(array('id'=>$value[0]))->find();
    				}

    				$ginfo = array();
    				foreach($linfo as $c=>$hinfo){
    				$ginfo[] = $this->product_model->where(array('id' => $hinfo['pid']))->find();

    				}
    				$this->assign('products', $ginfo);
    				$this->assign('totalCount',$total);
    				$this->assign('priceCount',$price);


    				}*/
    			}
    			if($thisOrder['productid'] != 0 && $thisOrder['info'] ==""){
    				$openinfo = $this->product_detail_model->where(array('id' => $thisOrder['productid'],'token'=>$this->token))->find();
    				//print_r($openinfo);exit;
    				$ginfo = $this->product_model->where(array('id' => $openinfo['pid'],'token'=>$this->token))->find();
    				$linfo = M('Norms_new')->where(array('id' => $openinfo['format'],'token'=>$this->token))->find();
    				$vinfo = M('Norms_new')->where(array('id' => $openinfo['color'],'token'=>$this->token))->find();
    				$jinfo = M('Product_cat_new')->where(array('id'=>$vinfo['catid'],'token'=>$this->token))->find();
    				$host[4] = $ginfo['logourl'];   // 商品图片
    				$host[5] = $ginfo['name'];      // 商品名称
    				$host[6] = $jinfo['norms'];     // 商品规格
    				$host[7] = $jinfo['color'];     // 商品外观
    				$host[8] = $linfo['value'];     // 商品规格值
    				$host[9] = $vinfo['value'];     // 商品外观值

    				$host[10] = $vinfo['catid'];     // 商城ID值



    				// $opinfo = $this->product_model->where(array('id' => $thisOrder['productid'],'token'=>$this->token))->find();
    				$priceCount = $thisOrder['price']*$thisOrder['total']+$thisOrder['mailpay'];
    				$this->assign('products',$host);
    				$this->assign('totalCount',$thisOrder['total']);
    				$this->assign('priceCount',$priceCount);

    			}

    		}
    		/**
    		 * 这里是购物车购买商品
    		 */
    		if($thisOrder['is_cart']){
    			$pro_list=M('Product_sidedetail')->where(array('cid'=>$thisOrder['id']))->select();
    			$this->assign('pro_list',$pro_list);
    		}
    		/**
    		 * 这里处理旅游订单
    		 */

    		if($thisOrder['ordertype']==3){
    			$thisOrder['usertravelinfo']=json_decode($thisOrder['usertravelinfo'],true);
    		}
    		//处理扩展
    		if($thisOrder['extend']){
    			$thisOrder['extend']=json_decode($thisOrder['extend'],true);
    		}
    		// p($thisOrder);
    		$this->assign('thisOrder',$thisOrder);

    		$this->assign('quan',$quan);

    		$this->display();
    	}
    }


}
