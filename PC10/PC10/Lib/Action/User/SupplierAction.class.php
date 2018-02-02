<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/21
 * Time: 16:02
 */
class SupplierAction extends TableAction{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'User/default/supplier';
    /**
     *  Token
     **/
    //private $_sToken = null;
    /**
     *  UID
     **/
    //private $_iUID = null;

    /**
     *  顶部
     **/
    public function _initialize()
    {
    
        $this->iSid = $_GET['sid'];
        parent::_initialize();
        $this->supplier	   = D('Product_supplier');
        $this->shop = D('Product_new');
        $this->image = M('Product_image_new');
    }
    protected function setHeader(){
        return array(
            array(
                'name' => '商品管理',
                'url'  => U('Supplier/index', array('token' => $this->_sToken,'sid'=>$this->iSid))
            ),
            array(
                'name' => '订单管理',
                'url'  => U('Supplier/orders', array('token' => $this->_sToken,'sid'=>$this->iSid))
            )
        );
    }
    public function index(){
        $aWhere = array('token'=>$this->_sToken,'sid'=>$this->iSid);

        $this->table(
            array(
                //'kid' => 'aid',//如果主键不是id，则需要设置
                'HeadHover' => U('Supplier/index', array('token' => $this->_sToken,'sid'=>$this->iSid)),
                'Head_Opt' => array(
                    array(
                        'name'   => '添加商品',
                        'url'    => U('Supplier/addNew',array('token' => $this->_sToken,'sid'=>$this->iSid))
                    )
                ),
                'tips' => array(
                    '你可以在这里管理商品信息',
                ),
                'Table_Header' => array(
                    'ID', '商品名称', '图片','添加时间','状态', '操作'
                ),
                //显示表头里是图片的字段，并且设置图片大小
                'aListImg' => array(
                    'container' => array('logourl'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Supplier/addNew',array('token' => $this->_sToken,'sid'=>$this->iSid))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Supplier/delShop',array('token' => $this->_sToken,'sid'=>$this->iSid))
                    ),
                )
            ),
            $this->shop->where($aWhere)->count(),
            $this->shop->field('id,name,logourl,time,status')->where($aWhere),
            array($this,'_handle')
        );
	

    }
    public function _handle($aData){
        foreach($aData as $ik=>$aVal){
            $aData[$ik]['time'] = date('Y-m-d H:i:s',$aVal['time']);
            if($aVal['status'] == 3){
                $aData[$ik]['status'] = '未审核';
            }else{
                $aData[$ik]['status'] = '审核通过';
            }

        }
        return($aData);
    }

    /*添加修改商品*/

    public function addNew(){
        $this->shop = D('Product_new');
        $this->image = M('Product_image_new');
        $oCatModel = M('Product_cat_new');

        $this->assign(array(
            'imageList'=>$a=$this->image->where(array('pid'=>$_GET['id']))->select(),
            'aShop'=>$this->shop->where(array('id'=>$_GET['id']))->find(),
            'aCat'=>$oCatModel->where(array('token'=>$this->_sToken))->select()
        ));
        //print_r($a);exit;
        $this->UDisplay('addNew');
    }
     public function createShop(){
         $oShopModel = $this->shop;
         //$oImgaModel = $this->image;
         if(IS_AJAX){
             $data = !empty($_POST['id'])?$_POST['id']:'';
             $product['token'] = $_POST['token'];
             $product['catid'] = $_POST['catid'];
             $product['name'] = $_POST['name'];
             $product['num'] = $_POST['num'];
             $product['logourl'] = $_POST['logourl'];
             $product['des'] = $_POST['des'];
             $product['intro'] = $_POST['intro'];
             $product['sid'] = $this->iSid;
             $product['time'] = time();
             $product['status'] = 3;
             $images = isset($_POST['images']) ? htmlspecialchars_decode($_POST['images']) : '';
             if(!$data){
                 if($iInsertId=$oShopModel->add($product)){
                     if (!empty($images)) {
                         $product_image = M('Product_image_new');
                         $images = json_decode($images, true);
                         $iamgelist = $product_image->field('id')->where(array('pid' => $iInsertId))->select();
                         $oldImageId = array();
                         foreach ($iamgelist as $val) {
                             $oldImageId[$val['id']] = $val['id'];
                         }
                         foreach ($images as $row) {
                             if (empty($row['image'])) continue;
                             $data_d = array('pid' => $iInsertId, 'image' => $row['image']);
                             if ($row['id']) {
                                 unset($oldImageId[$row['id']]);
                                 $product_image->where(array('id' => $row['id'], 'pid' => $iInsertId))->save($data_d);
                             } else {
                                 $product_image->add($data_d);
                             }
                         }
                         $this->success2('添加成功',U('Supplier/index',array('token'=>$this->_sToken,'sid'=>$this->iSid)));

                     }else{
                         $this->error2('添加失败',U('Supplier/addNew',array('token'=>$this->_sToken,'sid'=>$this->iSid)));

                     }
                 }else{
                     $this->error2('添加失败',U('Supplier/addNew',array('token'=>$this->_sToken,'sid'=>$this->iSid)));
                 }
             }else{
                 $iTem = $oShopModel->where(array('id'=>$data))->find();
                 if(!$iTem) $this->error2('非法操作');
                 if($oShopModel->where(array('id'=>$data))->save($product)){
                     if (!empty($images)) {
                         $product_image = M('Product_image_new');
                         $images = json_decode($images, true);
                         $iamgelist = $product_image->field('id')->where(array('pid' => $data))->select();
                         $oldImageId = array();
                         foreach ($iamgelist as $val) {
                             $oldImageId[$val['id']] = $val['id'];
                         }
                         foreach ($images as $row) {
                             if (empty($row['image'])) continue;
                             $data_d = array('pid' => $data, 'image' => $row['image']);
                             if ($row['id']) {
                                 unset($oldImageId[$row['id']]);
                                 $product_image->where(array('id' => $row['id'], 'pid' => $data))->save($data_d);
                             } else {
                                 $product_image->add($data_d);
                             }
                         }
                         $this->success2('编辑成功',U('Supplier/index',array('token'=>$this->_sToken,'sid'=>$this->iSid)));
                     }else{
                         $this->error2('编辑失败',U('Supplier/addNew',array('token'=>$this->_sToken,'sid'=>$this->iSid)));
                     }
                 }else{
                     $this->error2('编辑失败',U('Supplier/addNew',array('token'=>$this->_sToken,'sid'=>$this->iSid)));
                 }
             }
         }
     }

    public function delShop(){
        $this->shop = D('Product_new');
        $this->image = M('Product_image_new');
        $where = array('id'=>$_REQUEST['id'],'token'=>$this->_sToken);
        $aWhere = array('pid'=>$_REQUEST['id']);
        $Item = $this->shop->where($where)->find();
        if(!$Item) $this->error2('非法操作');
        if($this->shop->where($where)->delete()){
            if($this->image->where($aWhere)->delete()){
                $this->success2('删除成功！',U('Supplier/index', array('token'=>$this->_sToken,'sid'=>$this->iSid)));
            }else{
                $this->error2('商品删除成功！');
            }
        }else{
            $this->error2('删除失败！');
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
            $arr['sid'] =$_REQUEST['sid'];
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

            if($_POST['paid']) {
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
                if ($orderter['type'] == 'pay') {
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
                $this->assign('orders', $orders);
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
            $where['sid'] = $_REQUEST['sid'];
            $where['token']=$_REQUEST['token'];
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
                $this->assign('orders', $orders);

            }
        }
        $this->UDisplay('orders');
    }

    // }

    public function orderInfo()
    {
        $this->product_model = M('Product_new');
        $this->product_cat_model = M('Product_cat_new');
        $product_cart_model = M('product_cart_new');
        $this->product_detail_model = M('Product_detail_new');
        $thisOrder = $product_cart_model->where(array('id' => intval($_GET['id']),'token'=>$this->token))->find();
        $quan = M('Sn')->where(array('id'=>intval($thisOrder['sid']),'token'=>$this->token))->find();

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

            $this->success('修改成功', U('Supplier/orderInfo', array('token' =>$this->token, 'id' => $thisOrder['id'],'sid'=>$_GET['sid'])));
        } else {
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
                        }
                        $total+=intval($crul[1]);
                        $price += intval($crul[2]*$crul[1]);

                    }
                    $price = intval($price + $thisOrder['mailpay']);

                    $this->assign('host',$host);
                    $this->assign('totalCount',$total);
                    $this->assign('priceCount',$price);

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
            $this->assign('thisOrder',$thisOrder);

            $this->assign('quan',$quan);

            $this->UDisplay('orderInfo');
        }
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
        }
        $this->success('操作成功',U('Supplier/orders', array('token' =>$this->_sToken,'sid'=>$_GET['sid'])));
        //$this->success('操作成功',$_SERVER['HTTP_REFERER']);
    }



}