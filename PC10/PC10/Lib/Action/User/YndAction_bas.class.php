<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南(anyi路况在线)
 **/
class YndAction extends TableAction
{
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

    /**
     *  Token
     **/
    //private $_sToken = null;

    public function _initialize()
    {
        //$this->_sToken = $_SESSION['token'];
        parent::_initialize();

    }

    protected function setHeader()
    {
        return array(
            array(
                'name' => '用户管理',
                'url' => U('Ynd/user', array('token' => $this->_sToken))
            ),
            array(
                'name' => '商品分类管理',
                'url' => U('Store_new/index', array('token' => $this->_sToken))
            ),

        );
    }

    /*查询用户微信资料*/
    public function wxinfo($openid){
        $wxuser = M('Wxuser')->where(array(
            'token'=>$this->_sToken
        ))->find();
        $wxinfo = M('Wxusers')->where(array(
            'uid'=>$wxuser['id'],
            'openid'=>$openid
        ))->find();
        return $wxinfo;
    }
    /*单个用户查询*/
    public function info($user_id){
        $info = M('Ynd_user')
            ->where(array('id'=>$user_id))
            ->find();
        return $info;
    }

    /*用户管理*/
    public function user(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                'HeadHover' => U('Ynd/user', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    /*array(
                        'name'   => '加盟广告信息',
                        'url'    => U('Shengou/ucontent')
                    ),*/
                ),
                'tips' => array(
                    '你可以在这里管理用户信息'
                ),
                'Table_Header' => array(
                    'ID', '会员号','昵称','性别','电话', '金额', 'CQ','LQ','会员等级','会员状态','类型','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '个人账户',
                        'url'  => U('Ynd/account',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name' => '个人订单',
                        'url'  => U('Ynd/orderlist',array('token'=>$this->_sToken,'type'=>1))
                    ),
                    array(
                        'name' => '个人放单',
                        'url'  => U('Ynd/fangdanlist',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name' => '资料详情',
                        'url'  => U('Ynd/seeuse',array('token'=>$this->_sToken))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Shengou/del_use')
                    ),
                ),
            ),
            M('Ynd_user')->where($aWhere)->count(),
            M('Ynd_user')->field('id,uname,openid,sex,phone,money,CQ,LQ,rank,status,type')->order("add_time desc")->where($aWhere),
            array($this,'userindex')
        );
    }

    public function userindex($data){
        foreach($data as $key=>$val){
            $data[$key]['openid'] = $this->wxinfo('openid')['nickname'];
            switch($val['status']){
                case 0;$data[$key]['status'] = '未审核';break;
                case 1;$data[$key]['status'] = '审核通过，但未激活';break;
                case 2;$data[$key]['status'] = '正式会员';break;
            }
            switch($val['type']){
                case 0: $data[$key]['type'] = '普通用户';break;
                case 1: $data[$key]['type'] = '放单用户';break;
            }
        }
        return $data;
    }

    /*个人账户管理*/
    public function account(){
        $info = M('Ynd_user')->where(array('id'=>$_GET['id']))->find();
        $aWhere['token'] = $this->_sToken;
        $aWhere['user_id'] = $_GET['id'];
        if($_GET['type']){
            $aWhere['type'] = $_GET['type'];
        }
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Ynd/user', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '提现查看',//2级
                        'url'    => U('Ynd/account',array('token'=>$this->_sToken,'id'=>$_GET['id'],'type'=>1))
                    ),
                    array(
                        'name'   => '充值查看',//2级
                        'url'    => U('Ynd/account',array('token'=>$this->_sToken,'id'=>$_GET['id'],'type'=>2))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '会员号', '金额', '类型', '添加时间', '审核时间', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '审核',
                        'url'  => U('Ynd/cheack',array('token'=>$_SESSION['token']))
                    ),
                   /* array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Cs/delete_content',array('token'=>$_SESSION['token']))
                    ),*/
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Ynd_money')->where($aWhere)->count(),
            M('Ynd_money')->field('id,user_id,money,type,add_time,administer_time')->where($aWhere)->order("add_time desc"),
        array($this,'accountinfo')
        );
    }

    public function accountinfo($data){
        foreach($data as $key=>$val){
            $info = M('Ynd_user')->where(array('id'=>$val['user_id']))->find();
            $data[$key]['user_id'] = $info['uname'];
            switch($val['type']){
                case 1:$data[$key]['type'] = '提现';break;
                case 2:$data[$key]['type'] = '充值';break;
            }
        }
        return $data;
    }



    /*个人资料详情*/
    public function seeuse(){
        $user_id = $_GET['id'];
        $user = M('Ynd_user')->where(array('id'=>$user_id))->find();
        $bank = M('Ynd_bank')->where(array('token'=>$this->_sToken,'user_id'=>$user_id))->find();
        $address = M('Ynd_address')->where(array('token'=>$this->_sToken,'user_id'=>$user_id))->select();
        $this->assign(array(
            'wxuser'=>$this->wxinfo($user['openid']),
            'user'   =>$user,
            'bank'   =>$bank,
            'address'=>$address
        ));
        $this->display('seeuse');
    }



    /*商品管理*/
    //显示
    public function index(){
        if($_GET['cat_id']){
            $info = M('Product_cat_new')->where(array('id'=>$_GET['cat_id']))->getField('name');
        }else{
            $info = "全部";
        }

        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Store_new/index', array('token' => $this->_sToken,'cat_id'=>$_GET['cat_id'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加商品',//2级
                        'url'    => U('Ynd/addproduct',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                     array(
                         'name'   => '商城首页广告图管理',//2级
                         'url'    => U('Ynd/piclist',array('token'=>$this->_sToken))
                     )
                ),
                'tips' => array(//3级
                    '你可以在这里管理<span style="font-size: 16px;">"'.$info.'"</span>商品'
                ),
                'Table_Header' => array(//4级
                    'ID', '所属分类', '商品名称', '建议价格', '库存量','序列号', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看评价',
                        'url'  => U('Ynd/seepingjia',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                    array(
                        'name' => '编辑',
                        'url'  => U('Ynd/saveproduct',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Ynd/delete_product',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))
                    ),
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Ynd_product')->where(array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))->count(),
            M('Ynd_product')->field('id,cat_id,name,price,num,sorts')->where(array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))->order("sorts,id desc"),
        array($this,'indexes')
        );
    }

    public function indexes($data){
        foreach($data as $key=>$val){
            $data[$key]['cat_id'] = M('Product_cat_new')->where(array('id'=>$val['cat_id']))->getField('name');
        }
        return $data;
    }

    /*添加修改商品*/
    public function set_product($aaa){

        $this->$aaa('Ynd_product',array(
            //array('title'=>"商品所属分类",'type'=>"input",'name'=>"cat_name",'value'=>'cat_name','msg'=>'请填写商品所属的分类名称'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"商品名称",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写商品的名称'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"商品所产地",'type'=>"input",'name'=>"p_area",'value'=>'p_area','msg'=>'请填写商品产地'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"商品规格",'type'=>"input",'name'=>"norms",'value'=>'norms','msg'=>'请填写商品的规格'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"产品参数1",'type'=>"number",'name'=>"parameter",'value'=>'parameter','msg'=>'请填写商品的价格',),
            array('title'=>"商品购买建议价格",'type'=>"number",'name'=>"price",'value'=>'price','msg'=>'请填写商品的价格',),
            array('title'=>"购买所需的LQ币",'type'=>"number",'name'=>"LQ",'value'=>'LQ','msg'=>'请填写购买所需的LQ币',),
            array('title'=>"购买所需的CQ币",'type'=>"number",'name'=>"CQ",'value'=>'CQ','msg'=>'请填写购买所需的CQ币',),
            //array('title'=>"商品放单建议价",'type'=>"number",'name'=>"fprice",'value'=>'fprice','msg'=>'请填写放单所需要的价格',),
            array('title'=>"放单所需的LQ币",'type'=>"number",'name'=>"SLQ",'value'=>'SLQ','msg'=>'请填写放单所需的LQ币',),
            array('title'=>"放单所需的CQ币",'type'=>"number",'name'=>"SCQ",'value'=>'SCQ','msg'=>'请填写放单所需的CQ币',),
            array('title'=>"库存",'type'=>"number",'name'=>"num",'value'=>'num','msg'=>'请填写库存',),
            array('type'=>'img','many'=>array(
                array('title'=>"商品图片",'type'=>"img",'name'=>"pic",'value'=>'pic','width'=>320,'height'=>250),
                array('title'=>"商品广告图片1",'type'=>"img",'name'=>"img1",'value'=>'img1','width'=>320,'height'=>250),
                array('title'=>"商品广告图片2",'type'=>"img",'name'=>"img2",'value'=>'img2','width'=>320,'height'=>250),
                array('title'=>"商品广告图片3",'type'=>"img",'name'=>"img3",'value'=>'img3','width'=>320,'height'=>250),
                array('title'=>"商品广告图片4",'type'=>"img",'name'=>"img4",'value'=>'img4','width'=>320,'height'=>250),
               // array('title'=>"商品广告图片5",'type'=>"img",'name'=>"img5",'value'=>'img5','width'=>320,'height'=>250),
               // array('title'=>"商品广告图片6",'type'=>"img",'name'=>"img6",'value'=>'img6','width'=>320,'height'=>250),
            )),
            array('title'=>"商品简介",'type'=>"textarea",'name'=>"abstract",'value'=>'abstract'),
            array('title'=>"商品详情介绍",'type'=>"textarea_1",'name'=>"content",'value'=>'content'),
            array('title'=>"排序",'type'=>"number",'name'=>"sorts",'value'=>'sorts','msg'=>'请填写序列号','bast'=>'数字愈少在前端的排位愈靠前，可以为负数'),
            array('type'=>'select','title'=>"商品结算规则",'name'=>"rule",'value'=>'rule','msg'=>'选择商品结算规则','many'=>array(
                array('content'=>'选择商品结算规则'),
                array('value'=>'1', 'content'=>'A：最低放单价优先，相同放单价按放单时间优先的原则处理'),
                array('value'=>'2','content'=>'B：放单时间优先的原则处理'),
            ))
        ),U('Ynd/index',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id'])),array($this,'productinfo'));
    }

    public function productinfo($data){
        $data['token'] = $this->_sToken;
        if(!$_GET['id']){
            $data['add_time'] = date('Y-m-d H:i:s');
        }
        $data['sumnum'] = $data['num'];
        $data['cat_id'] = $_GET['cat_id'];
        return $data;
    }

    /*添加产品*/
    public function addproduct(){
        $this->set_product(add);
    }
    /*修改产品*/
    public function saveproduct(){
        $this->set_product(Edit);
    }
    /*删除产品*/
    public function delete_product(){
        $this->del('Ynd_product');
    }

    /*查看评价*/
    public function seepingjia(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'tid' =>$_GET['id']
        );
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Store_new/index', array('token' => $this->_sToken,'cat_id'=>$_GET['cat_id'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Ynd/index',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                ),
                'tips' => array(//3级
                    '你可以在这里查看评价'
                ),
                'Table_Header' => array(//4级
                    'ID', '商品名称', '评价内容', '评价时间','评价人', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Ynd/delete_pingjia',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))
                    ),
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Ynd_evaluate')->where($aWhere)->count(),
            M('Ynd_evaluate')->field('id,tid,content,add_time,openid')->where($aWhere)->order("id desc"),
            array($this,'seepingjiaindex')
        );
    }

    public function seepingjiaindex($data){
        foreach($data as $key=>$val){
            $data[$key]['tid'] = M('Ynd_product')->where(array('id'=>$val['tid']))->getField('name');
            $data[$key]['openid'] = $this->wxinfo($val['openid'])['nickname'];
        }
        return $data;
    }



    /*查看商品属性*/
    public function shopinfo($pro_id){
        $info = M('Ynd_product')
            ->where(array('id'=>$pro_id))
            ->find();
        return $info;
    }
    /*删除产品*/
    public function delete_pingjia(){
        $this->del('Ynd_evaluate');
    }



    /*放单列表*/
    public function fangdanlist(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'yuid' =>$_GET['id'],
        );
        $this->table(  array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Store_new/user', array('token' => $this->_sToken,'cat_id'=>$_GET['cat_id'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Ynd/user',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                ),
                'tips' => array(//3级
                    '你可以在这里查看评价'
                ),
                'Table_Header' => array(//4级
                    '放单ID','会员号','放单商品','放单数量','放单单价','所需LQ','所需CQ', '放单时间','操作'
                ),
                'List_Opt' => array(
                    /*array(
                        'name' => '详情',
                        'url'  => U('Ynd/orderinfo',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))
                    ),*/
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Ynd/delete_fangdan',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))
                    ),
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Ynd_fangdan')->where($aWhere)->count(),
            M('Ynd_fangdan')->field('id,yuid,tid,num,money,SLQ,SCQ,add_time')->where($aWhere)->order("id desc"),
            array($this,'fangdanlistindex')
        );
    }
    public function fangdanlistindex($data){
        foreach($data as $key=>$val){
            $data[$key]['tid'] = $this->shopinfo($val['tid'])['name'];
            $data[$key]['yuid'] = $this->info($val['yuid'])['uname'];
        }
        return $data;
    }


    /*订单列表*/
    public function orderlist(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'yuid' =>$_GET['id'],
        );
        $this->table(  array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Store_new/user', array('token' => $this->_sToken,'cat_id'=>$_GET['cat_id'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Ynd/user',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                ),
                'tips' => array(//3级
                    '你可以在这里查看评价'
                ),
                'Table_Header' => array(//4级
                    '订单ID','会员号','购买数量','所需金额','所需LQ','所需CQ', '购买时间','状态', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '详情',
                        'url'  => U('Ynd/orderinfo',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Ynd/delete_pingjia',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))
                    ),
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Ynd_order')->where($aWhere)->count(),
            M('Ynd_order')->field('id,yuid,num,money,LQ,CQ,add_time,status')->where($aWhere)->order("id desc"),
            array($this,'orderlistindex')
        );
    }

    public function orderlistindex($data){
        foreach($data as $key=>$val){
            //$data[$key]['pro_id'] = $this->shopinfo($val['pro_id'])['name'];
            $data[$key]['yuid'] = $this->info($val['yuid'])['uname'];
        }
        return $data;
    }

    /*订单详情*/
    public function orderinfo(){
        $orderinfo = M('Ynd_order')->where(array('id'=>$_GET['id']))->find();
        $shoplist = M('Ynd_orderinfo')->where(array('oid'=>$_GET['id']))->select();
        foreach($shoplist as $key=>$val){

        }
        $this->assign(array(
            'logs'=>M('Kuaizhao')->select(),
            'shoplist'=>$shoplist,
        ));
        $this->display('orderinfo');
    }





}