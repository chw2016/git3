<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-9
 * Time: 下午3:54
 */

class JishiAction extends BaseAction{

    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/jishi';

    /**
     *  Token
     **/
    protected $_sToken = null;

    public function _initialize(){
        $this->_sToken = $this->_get('token');
        parent::_initialize();
    }

    /*
     * 产品展示
     */
    public function chanpinzhanshi(){
        #分类
        $iType  = (int)FC::G('type');
        $aWhere['token'] = $this->_sToken;
        if ($iType) {
            $aWhere['finances_product_type_id'] = $iType;
        }
        $this->assign(array(
            'type'        => $iType,
            #产品分类
            'ProductType' => Arr::changeIndexToKVMap(D('Finances_product_type')
                ->field('id, title')
                ->where(array('token' => $this->_sToken))
                ->select(), 'id', 'title'),
            #产品
            'Product'   => D('Finances_product')
                ->field('id, title, image, price')
                ->where($aWhere)
                ->select()
        ));
        $this->UDisplay('chanpinzhanshi');
    }

    /*
     * 产品详细
     */
    public function chanpindetail(){
        $iPID    = (int)FC::G('id');
        $Product = D('Finances_product')->where(array('id' => $iPID, 'token' => $this->_sToken))->select();
        if (!$iPID || !$Product) {
            $this->redirect(U('Jishi/chanpinzhanshi', array('token' => $this->_sToken)));
            return false;
        }
        $this->assign(array(
            'Product' => $Product[0]
        ));
        $this->UDisplay('chanpindetail');
    }

    /*
     * 理财产品
     */
    public function licaichanpin(){
        #分类
        $iArea = (int)FC::G('area');
        $aWhere['token'] = $this->_sToken;
        if ($iArea) {
            $aWhere['finances_area_id'] = $iArea;
        }
        $this->assignArea();
        $this->assign(array(
            'iArea'        => $iArea,
            #产品
            'Product'   => D('Finances_product')
                ->field('id, title, image, price')
                ->where($aWhere)
                ->select()
        ));
        $this->UDisplay('licaichanpin');
    }

    /*
     * 理财产品
     */
    public function licaichanpinjieshao(){
        $iPID    = (int)FC::G('id');
    
        $Product = D('Finances_product')->where(array('id' => $iPID, 'token' => $this->_sToken))->find();
        if (!$iPID || !$Product) {
            $this->redirect(U('Jishi/chanpinzhanshi', array('token' => $this->_sToken)));
            return false;
        }
        $aProduct =array_filter(explode('|',$Product['finances_planner_id']));
        foreach($aProduct as $iKey=>$iVoluer){
            $aProducts = D('Finances_planner')
                        ->where(array('id' => $iVoluer, 'token' => $this->_sToken))
                        ->find();
            $aProduct[$iKey] = $aProducts;
        }       
        $this->assign(array(
            #产品本身介绍
            'Product' => $Product,
            #产品相关理财师介绍
            'Planner' => $aProduct
        ));
        $this->UDisplay('licaichanpinjieshao');
    }

    /*
     * 下单页面
     */
    public function doorder(){
        $iPID    = (int)FC::G('id');
        $Product = D('Finances_product')->where(array('id' => $iPID, 'token' => $this->_sToken))->find();
        if (!$iPID || !$Product) {
            $this->redirect(U(
                'Jishi/chanpinzhanshi',
                array('token' => $this->_sToken, 'openid' => $this->openid)
            ));
            return false;
        }
        $Order   = D('Finances_order');
        $this->assign(array(
            'user'=>array_shift($Order->where(array(
                    'token'=>$this->_sToken,
                    'openid'=>$this->openid
                ))->order('add_time desc')->select()),
            'Product' => $Product,
            'day0'=> date('m月d日'),
            'day1'=> date("m月d日",strtotime("+1 day")),
            'day2'=> date("m月d日",strtotime("+2 day")),
            'day3'=> date("m月d日",strtotime("+3 day")),
            'date0'=> date('Y-m-d'),
            'date1'=> date('Y-m-d',strtotime("+1 day")),
            'date2'=> date('Y-m-d',strtotime("+2 day")),
            'date3'=> date('Y-m-d',strtotime("+3 day"))
        ));
        $this->UDisplay('doorder');
    }
    public function order()
    {
        $iPID = (int)FC::GP('id');
        $iPRID = (int)FC::GP('pid');
        $Product = D('Finances_product')->where(array('id' => $iPID, 'token' => $this->_sToken))->find();
        /*if (!$iPID || !$Product) {
            exit($this->ajaxReturn(array('status'=>-1,'info'=>'产品不存在，也许已下架')));
        }*/
        #理财师信息
        $Product = isset($Product) ? $Product : array();
        $Planner = D('Finances_planner')
            ->where(array('id' =>$iPRID, 'token' => $this->_sToken))
            ->find();
        $Planner = isset($Planner) ? $Planner : array();
        $Order   = D('Finances_order');
        if ($Order->add(array(
            'token' => $this->_sToken,
            'openid' => $this->openid,
            'order_num' => substr(md5(sprintf('%s%d', '*&!#$)', time())), 0, 16),
            'finances_planner_name' => Arr::get($Planner, 'name', ''),
            'finances_planner_image' => Arr::get($Planner, 'image', ''),
            'finances_product_title' => Arr::get($Product, 'title', ''),
            'finances_product_image' => Arr::get($Product, 'image', ''),
            'finances_product_desc' => Arr::get($Product, 'desc', ''),
            'time'                  => date('Y-m-d H:i:s',strtotime(FC::GP('time'))),
            'tel'                   => FC::GP('tel'),
            'address'               => FC::GP('address'),
            'price'                 => Arr::get($Product, 'price',''),
            'is_paid'               => 0,
            'status'                => 1,
            'reserve_time'          => date('Y-m-d H:i:s',strtotime(FC::GP('time'))),
            'add_time'              => date('Y-m-d H:i:s')
        ))) {
            exit($this->ajaxReturn(array('status'=>0, 'info'=>'')));
        }else{
            exit($this->ajaxReturn(array('status'=>-1, 'info'=>$Order->getError())));
        }
    }

    /**
     *  （取消）预约
     **/
    public function order_yuyue()
    {
        $iStatus = FC::GP('status');
        if (in_array($iStatus, array(0, 1)) AND D('Finances_order')->where(array(
            'token'  => $this->_sToken,
            'openid' => $this->openid,
            'id'     => FC::GP('id')
        ))->save(array('status' => $iStatus))) {
            exit($this->ajaxReturn(array('status'=>0,'info'=>'操作成功')));
        }else{
            exit($this->ajaxReturn(array('status'=>1,'info'=>'操作失败，请重新操作')));
        }
    }

    /**
     *  下单成功
     **/
    public function order_success()
    {
        $iOrderID = (int)FC::GP('id');
        $Model = D('Finances_order')->where(array('id' => $iOrderID, 'token' => $this->_sToken))->select();
        if (!$iOrderID|| !$Model || $Model[0]['status'] == 0) {
            $this->redirect(U(
                'Jishi/chanpinzhanshi',
                array('token' => $this->_sToken, 'openid' => $this->openid)
            ));
            return false;
        }
        $Model = $Model[0];

        $this->assign(array(
            #客服电话信息
            'kf'    => array_shift(D('Finances_config')->where(array('token' => $this->_sToken))->select()),
            'order' => $Model
        ));
        $this->UDisplay('order_success');
    }

    /**
     *  取消订单
     **/
    public function del_order()
    {
        if (D('Finances_order')->where(array(
            'token'  => $this->_sToken,
            'openid' => $this->openid,
            'id'     => FC::GP('id')
        ))->save(array('status' => 0))) {
            exit($this->ajaxReturn(array('status'=>0,'info'=>'取消成功')));
        }else{
            exit($this->ajaxReturn(array('status'=>1,'info'=>'取消失败，请重新操作')));
        }
    }

    /*
     * 理财师列表
     */
    public function licailist(){
        $iArea = (int)FC::G('area');
        $iType = FC::G('type');
        $aWhere['token'] = $this->_sToken;
        #地区
        if ($iArea) {
            $aWhere['finances_area_id'] = $iArea;
            $aArea = D('Finances_area')->where(array(
                'token'=>$this->_sToken,
                'id'=>$iArea
            ))->find();
        }
        #内、外部
        if ($iType !== null AND array_key_exists($iType, D('Finances_planner')->getType())) {
            $aWhere['type'] = $iType;
        }
        $this->assign(array(
            'iArea'     => $iArea,
            'iType'     => $iType,
            'sArea'     =>$aArea['title'],
            #理财师
            'Planner'   => D('Finances_planner')
                ->where($aWhere)
                ->select()
        ));
        $this->assignArea();
        $this->UDisplay('licailist');
    }

    /*
     * 理财师详细
     */
    public function licaishidetail(){
        $iID     = (int)FC::G('id');
        $Planner = D('Finances_planner')->where(array('id' => $iID, 'token' => $this->_sToken))->select();
        if (!$iID || !$Planner) {
            $this->redirect(U('Jishi/chanpinzhanshi', array('token' => $this->_sToken)));
            return false;
        }
        $aWhere = array('token' => $this->_sToken, 'finances_planner_id' => array('like','%|'.$iID.'|%'));
        $aAvgPrice = D('Finances_product')
                    ->where($aWhere)
                    ->field('avg(price) as price')
                    ->select();
        $iAvgPrice = count($aAvgPrice) > 0 ?  $aAvgPrice[0]['price'] : 0;

        $this->assign(array(
            'bCollect' => !!D('Finances_collect')->where(array(
                'token' => $this->_sToken,
                'openid' => $this->openid,
                'finances_planner_id' => $iID
            ))->count(),//是否收藏
            'Planner' => $Planner[0],//理财师信息
            'Product' => D('Finances_product')
                ->where($aWhere)
                ->select(),
                'iAvgPrice' => $iAvgPrice
            ));
        $this->UDisplay('licaishidetail');
    }

    /**
     *  用户中心
     **/
    public function user_center()
    {
        #客服数据
        $this->assign(array(
            #客服电话信息
            'kf'    => array_shift(D('Finances_config')->where(array('token' => $this->_sToken))->select()),
        ));
        $this->UDisplay('user_center');
    }

    /**
     *  我的收藏
     **/
    public function my_collect()
    {
        $aCollect = D('Finances_collect')
            ->where(array(
                'token' => $this->_sToken,
                'openid' => $this->openid
            ))
            ->select();
        if ($aCollect) foreach ($aCollect as $iK => $aV) {
            $aPlanner = array_shift(D('Finances_planner')
                ->where(array('token' => $this->_sToken, 'id' => $aV['finances_planner_id']))
                ->select());
            if ($aPlanner && count($aPlanner) > 0) {
                $aCollect[$iK]['planner'] = $aPlanner;
            }else{
                unset($aCollect[$iK]);
            }
        }
        $this->assign(array(
            'collect' => $aCollect
        ));
        $this->UDisplay('my_collect');
    }

    /**
     *  我的订单
     **/
    public function my_order()
    {
        #预约的单子
        $aOrder = D('Finances_order')
            ->where(array(
                'token' => $this->_sToken,
                'openid' => $this->openid,
                'status' => 1
            ))->order('add_time desc')->select();
        #取消的的单子
        $aCancelOrder = D('Finances_order')
            ->where(array(
                'token' => $this->_sToken,
                'openid' => $this->openid,
                'status' => 0
            ))->order('add_time desc')->select();
        $this->assign(array(
            'order' => $aOrder,
            'cancel' => $aCancelOrder
        ));
        $this->UDisplay('my_order');
    }


    /**
     *  (取消)收藏
     **/
    public function collect()
    {
        $iType      = FC::GP('type');
        $iPlannerID = FC::GP('id');
        if (1 == $iType) {//取消收藏
            if (D('Finances_collect')->where(array(
                'token'  => $this->_sToken,
                'openid' => $this->openid,
                'finances_planner_id'     => $iPlannerID
            ))->delete()) {
                exit($this->ajaxReturn(array('status'=>0,'info'=>'取消收藏成功')));
            }else{
                exit($this->ajaxReturn(array('status'=>1,'info'=>'服务器异常，请重新收藏')));
            }
        }else{//收藏
            if (D('Finances_collect')->add(array(
                'token'  => $this->_sToken,
                'openid' => $this->openid,
                'finances_planner_id'     => $iPlannerID,
                'add_time'            => date('Y-m-d H:i:s')
            ))) {
                exit($this->ajaxReturn(array('status'=>0,'info'=>'收藏成功')));
            }else{
                exit($this->ajaxReturn(array('status'=>1,'info'=>'服务器异常，请重新收藏')));
            }
        }
    }

    public function assignArea()
    {
        #产品地区
        $this->assign(array(
            'Area' => Arr::changeIndexToKVMap(D('Finances_area')
                ->field('id, title')
                ->where(array('token' => $this->_sToken))
                ->select(), 'id', 'title')
        ));
    }

    /*
     *预约理财师
     */
    public function licaishiyuyue()
    {
        $iID     = (int)FC::G('id');
        $Planner = D('Finances_planner')->where(array('id' => $iID, 'token' => $this->_sToken))->find();
        if (!$iID || !$Planner) {
            $this->redirect(U('Jishi/chanpinzhanshi', array('token' => $this->_sToken)));
            return false;
        }
        $Order   = D('Finances_order');
        $this->assign(array(
            'user'=>array_shift($Order->where(array(
                'token'=>$this->_sToken,
                'openid'=>$this->openid
            ))->order('add_time desc')->select()),
            'planner' => $Planner,
            'day0'=> date('m月d日'),
            'day1'=> date("m月d日",strtotime("+1 day")),
            'day2'=> date("m月d日",strtotime("+2 day")),
            'day3'=> date("m月d日",strtotime("+3 day")),
            'date0'=> date('Y-m-d'),
            'date1'=> date('Y-m-d',strtotime("+1 day")),
            'date2'=> date('Y-m-d',strtotime("+2 day")),
            'date3'=> date('Y-m-d',strtotime("+3 day"))
        ));

        $this->UDisplay('licaishiyuyue');
    }
}
