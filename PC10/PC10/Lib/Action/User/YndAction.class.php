<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南(由你定后台)
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
            array(
                'name' => '结算规则',
                'url' => U('Ynd/rule', array('token' => $this->_sToken))
            ),
            array(
                'name' => '订单管理',
                'url' => U('Ynd/orderlist', array('token' => $this->_sToken))
            ),
            array(
                'name' => '放单管理',
                'url' => U('Ynd/fangdanlist', array('token' => $this->_sToken))
            ),
            array(
                'name' => '用户相关信息日志',
                'url' => U('Ynd/log', array('token' => $this->_sToken))
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
//P($_SESSION);exit;
        $this->assign('qx','一键审核');//全选  方法名qx
        $aWhere = array('token'=>$this->_sToken);
        //搜索
        if(IS_POST){
            /*$_POST=$_REQUEST;
            $aWhere=$this->search($_POST);
            $aWhere['token'] =$_SESSION['token'];*/
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(session('?where_p')){
                $aWhere=session('where_p');
            }

        }
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
                    'ID', '会员号','昵称','性别','电话', '金额', 'CQ','LQ',/*'会员等级',*/'会员状态','类型','注册地址','操作'
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
                        'url'  => U('Ynd/del_user')
                    ),
                ),
                		//搜索

                 'search'=>array(
                    // array('title'=>'标题查询','name'=>'li_title','placeholder'=>'请输入标题模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                     array('title'=>'状态','name'=>'eq_status','type'=>'select','many'=>array(
                         array('value'=>'-1', 'name'=>'未审核'),
                         array('value'=>'1', 'name'=>'审核通过，未激活'),
                         array('value'=>'2', 'name'=>'正式会员'),
                         array('value'=>'3', 'name'=>'未注册'),
                         //array('value'=>'3','name'=>'旅游1'),
                     )),
                     array('title'=>'类型','name'=>'eq_type','type'=>'select','many'=>array(
                         array('value'=>'0', 'name'=>'普通用户'),
                         array('value'=>'1', 'name'=>'放单用户'),

                         //array('value'=>'3','name'=>'旅游1'),
                     )),
                 )
            ),
            M('Ynd_user')->where($aWhere)->count(),
            //rank,
            M('Ynd_user')->field('id,uname,openid,sex,phone,money,CQ,LQ,status,type')->order("add_time desc")->where($aWhere),
            array($this,'userindex')
        );
    }

    public function userindex($data){
        foreach($data as $key=>$val){
	        $wxinfo = $this->wxinfo($val['openid']);
            $info = $this->info($val['id']);
            $data[$key]['openid'] = $wxinfo['nickname'];
            switch($val['status']){
                case -1;$data[$key]['status'] = '未审核';break;
                case 1;$data[$key]['status'] = '审核通过，但未激活';break;
                case 2;$data[$key]['status'] = '正式会员';break;
                case 3;$data[$key]['status'] = '未注册的用户';break;
            }
            switch($val['sex']){
                case 0:$data[$key]['sex'] = '男';break;
                case 1:$data[$key]['sex'] = '女';break;
            }
            switch($val['type']){
                case 0: $data[$key]['type'] = '普通用户';break;
                case 1: $data[$key]['type'] = '放单用户';break;
            }
            $data[$key]['address'] = $info['location_p'].$info['location_c'];
        }
        return $data;
    }

    public function del_user(){
        $this->del('Ynd_user');
    }
    /*多选或者全选操作*/
    function qx(){
        //$id=implode(',', $_REQUEST['list']);
        $id = $_REQUEST['list'];
        foreach($id as $val){
            if(-1 == M('Ynd_user')->where(array('id'=>$val))->getField('status')){
               $list =  M('Ynd_user')->where(array('id'=>$val))->save(array('status'=>1));
            }
        }
        if($list) {
            $this->success2('操作成功');
        }else{
            $this->error2('操作失败');
        }
        //P($_REQUEST['list']);exit;
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
    function shenheuser(){
        if(IS_AJAX){
            $user_id = $_GET['id'];
            if(M('Ynd_user')->where(array('id'=>$user_id))->save(array('status'=>1))){
                $this->success('操作成功！');
            }else{
                $this->error('操作失败！');
            };
        }
    }

    function fangdanuser(){
        if(IS_AJAX){
            $user_id = $_GET['id'];
            $user = M('Ynd_user')->where(array('id'=>$user_id))->find();
            if($user['status'] !=2){
                $this->error('非正式会员，不能成为放单类型！');
            }else{
                if(M('Ynd_user')->where(array('id'=>$user_id))->save(array('type'=>1))){
                    $this->success('操作成功！');
                }else{
                    $this->error('操作失败！');
                };
            }

        }
    }




    /*商品管理*/
    //显示
    public function index(){
        $aWhere['token'] = $this->_sToken;
        if($_GET['cat_id']){
            $aWhere['cat_id'] = $_GET['cat_id'];
            $info = M('Product_cat_new')->where(array('id'=>$_GET['cat_id']))->getField('name');
        }else{
            $info = "全部";
        }

        $this->table(
            array(
                'abc'=>123,
               // 'id' => 'pro_id',//如果主键不是id，则需要设置
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
                    'ID', '所属分类', '商品名称', '建议价格','总部存量' ,'系统总存量','序列号','放单量', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '放单详情',
                        'url'  => U('Ynd/fangdanlist',array('token'=>$this->_sToken,'type'=>'shop'))
                    ),
                    array(
                        'name' => '查看评价',
                        'url'  => U('Ynd/seepingjia',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                    array(
                        'name' => '编辑',
                        'url'  => U('Ynd/saveproduct',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                    array(
                        'name' => '下线',
                        'url'  => U('Ynd/updoun',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
                    ),
                    array(
                        'name' => '产品更新',
                        'url'  => U('Ynd/updatashop',array('token'=>$this->_sToken,'cat_id'=>$_GET['cat_id']))
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
            M('Ynd_product')->where($aWhere)->count(),
            M('Ynd_product')->field('id,cat_id,name,price,num,sunnum,sorts,fnum')->where($aWhere)->order("sorts,id desc"),
        array($this,'indexes')
        );
    }

    public function indexes($data){
        foreach($data as $key=>$val){
            $data[$key]['cat_id'] = M('Product_cat_new')->where(array('id'=>$val['cat_id']))->getField('name');
        }
        return $data;
    }

    public function updoun(){
        $pro_id = $_GET['id'];
        $product = $this->product($pro_id);
        if($product['fnum']>0){
            $this->error2('此商品还有放单的没有售完，暂不能下线');
        }else{
            if(M('Ynd_product')->where(array('id'=>$pro_id))->save(array('status'=>-1))){
                $this->success2('下线成功');
            }else{
                $this->error2('操作失败');
            }
        }
    }

    /*添加修改商品*/
    public function set_product($aaa){

        $this->$aaa('Ynd_product',array(
            //array('title'=>"商品所属分类",'type'=>"input",'name'=>"cat_name",'value'=>'cat_name','msg'=>'请填写商品所属的分类名称'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"商品名称",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写商品的名称','bast'=>'注意：添加的新的商品只会在凌晨三点以后才会在线上看到。'/*,'readonly'=>1*/),
            array('title'=>"商品所产地",'type'=>"input",'name'=>"p_area",'value'=>'p_area','msg'=>'请填写商品产地'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"商品规格",'type'=>"input",'name'=>"norms",'value'=>'norms','msg'=>'请填写商品的规格'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"产品参数1",'type'=>"number",'name'=>"parameter",'value'=>'parameter','msg'=>'请填写产品参数1'),
            array('title'=>"商品购买参考价格",'type'=>"number",'name'=>"price",'value'=>'price','msg'=>'请填写商品的价格',),
            //array('title'=>"购买所需的LQ币",'type'=>"number",'name'=>"LQ",'value'=>'LQ','msg'=>'请填写购买所需的LQ币','readonly'=>1),
            //array('title'=>"购买所需的CQ币",'type'=>"number",'name'=>"CQ",'value'=>'CQ','msg'=>'请填写购买所需的CQ币','readonly'=>1),
            //array('title'=>"商品放单建议价",'type'=>"number",'name'=>"fprice",'value'=>'fprice','msg'=>'请填写放单所需要的价格',),
           // array('title'=>"放单所需的LQ币",'type'=>"number",'name'=>"SLQ",'value'=>'SLQ','msg'=>'请填写放单所需的LQ币',),
            //array('title'=>"放单所需的CQ币",'type'=>"number",'name'=>"SCQ",'value'=>'SCQ','msg'=>'请填写放单所需的CQ币',),
            array('title'=>"总部库存",'type'=>"number",'name'=>"num",'value'=>'num','msg'=>'请填写库存',),
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
           /* array('type'=>'select','title'=>"商品结算规则",'name'=>"rule",'value'=>'rule','msg'=>'选择商品结算规则','many'=>array(
                array('content'=>'选择商品结算规则'),
                array('value'=>'1', 'content'=>'A：最低放单价优先，相同放单价按放单时间优先的原则处理'),
                array('value'=>'2','content'=>'B：放单时间优先的原则处理'),
            ))*/
        ),U('Ynd/index',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id'])),array($this,'productinfo'));
    }

    public function productinfo($data){
        $data['token'] = $this->_sToken;
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['sunnum'] = $data['num'];
        $data['cat_id'] = $_GET['cat_id'];
        return $data;
    }

    /*添加产品*/
    public function addproduct(){
        $this->set_product(add);
    }
    /*修改产品*/
    public function saveproduct(){
        $pro_id = $_GET['id'];
        $product = M('Ynd_product')->where(array('id'=>$pro_id))->find();
        $this->assign(array(
            'product'=>$product
        ));
        //$this->display('saveproduct');
        $this->saves_product(Edit);
    }




    /*添加修改商品*/
    public function saves_product($aaa){

        $this->$aaa('Ynd_product',array(
            //array('title'=>"商品所属分类",'type'=>"input",'name'=>"cat_name",'value'=>'cat_name','msg'=>'请填写商品所属的分类名称'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"商品名称",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写商品的名称'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"商品所产地",'type'=>"input",'name'=>"p_area",'value'=>'p_area','msg'=>'请填写商品产地'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"商品规格",'type'=>"input",'name'=>"norms",'value'=>'norms','msg'=>'请填写商品的规格'/*,'bast'=>'备注说明','readonly'=>1*/),
            array('title'=>"产品参数1",'type'=>"number",'name'=>"parameter",'value'=>'parameter','msg'=>'请填写产品参数1','readonly'=>1),
            array('title'=>"商品购买参考价格(原)",'type'=>"number",'name'=>"price",'value'=>'price','msg'=>'请填写商品的价格','readonly'=>1),
            array('title'=>"商品购买参考价格(改)",'type'=>"number",'name'=>"fprice",'value'=>'fprice','msg'=>'请填写商品修改后的价格',),
            array('title'=>"购买所需的LQ币",'type'=>"number",'name'=>"LQ",'value'=>'LQ','msg'=>'请填写购买所需的LQ币','readonly'=>1),
            array('title'=>"购买所需的CQ币",'type'=>"number",'name'=>"CQ",'value'=>'CQ','msg'=>'请填写购买所需的CQ币','readonly'=>1),
            //array('title'=>"商品放单建议价",'type'=>"number",'name'=>"fprice",'value'=>'fprice','msg'=>'请填写放单所需要的价格',),
            // array('title'=>"放单所需的LQ币",'type'=>"number",'name'=>"SLQ",'value'=>'SLQ','msg'=>'请填写放单所需的LQ币',),
            //array('title'=>"放单所需的CQ币",'type'=>"number",'name'=>"SCQ",'value'=>'SCQ','msg'=>'请填写放单所需的CQ币',),
            array('title'=>"库存(原)",'type'=>"number",'name'=>"num",'value'=>'num','msg'=>'请填写库存','readonly'=>1),
            array('title'=>"库存",'type'=>"number",'name'=>"gnum",'value'=>'gnum','msg'=>'请填写库存',),
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
            /* array('type'=>'select','title'=>"商品结算规则",'name'=>"rule",'value'=>'rule','msg'=>'选择商品结算规则','many'=>array(
                 array('content'=>'选择商品结算规则'),
                 array('value'=>'1', 'content'=>'A：最低放单价优先，相同放单价按放单时间优先的原则处理'),
                 array('value'=>'2','content'=>'B：放单时间优先的原则处理'),
             ))*/
        ),U('Ynd/index',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id'])),array($this,'productinfos'));
    }

    public function productinfos($data){
        $data['token'] = $this->_sToken;
        $data['sunnum'] = $data['gnum'];
        $data['cat_id'] = $_GET['cat_id'];
	//P($data);exit;
        return $data;
    }



    public function ajaxproduct(){
        if(IS_AJAX){
            $pro_id = $_GET['pro_id'];
            if(M('Ynd_product')->where(array('id'=>$pro_id))->save()){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }
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
            $wxinfo = $this->wxinfo($val['openid']);
            $data[$key]['openid'] = $wxinfo['nickname'];
	    //$data[$key]['openid'] = $this->wxinfo($val['openid'])['nickname'];
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
        );
        if($_GET['type'] == 'shop'){
            if($_GET['id']){
                $aWhere['tid'] =$_GET['id'];
            }
        }else{
            if($_GET['id']){
                $aWhere['yuid'] =$_GET['id'];
            }
        }

        if($_GET['cat_id']){
            $aWhere['tid'] =$_GET['cat_id'];
        }
        //搜索
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
          /*  session('where_p',null);
            session('where_p',$aWhere);*/
        }else{
            //get 过来P分页时，带上条件查询数据
            if(session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        if($aWhere['add_time']){
            $aWhere['add_time'][1][0] = date('Y-m-d H:i:s',$aWhere['add_time'][1][0]);
            $aWhere['add_time'][1][1] = date('Y-m-d H:i:s',$aWhere['add_time'][1][1]);
        }
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
                    '放单ID','会员号','商品ID','放单商品','支付金额','放单数量','放单单价','放单指数','所需LQ','所需CQ', '放单时间'
                ),
                'List_Opt' => array(
                    /*array(
                        'name' => '详情',
                        'url'  => U('Ynd/orderinfo',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))
                    ),*/
                  /*  array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Ynd/delete_fangdan',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['cat_id']))
                    ),*/
                ),

                'search'=>array(
                    array('title'=>'放单时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Ynd_fangdan')->where($aWhere)->count(),
            M('Ynd_fangdan')->field('id,yuid,tid as a,tid,money,num,fprice,exponent,SLQ,SCQ,add_time')->where($aWhere)->order("id desc"),
            array($this,'fangdanlistindex')
        );
    }
    public function fangdanlistindex($data){
        foreach($data as $key=>$val){
            $shopinfo = $this->shopinfo($val['tid']);
            $info = $this->info($val['yuid']);
            $data[$key]['tid'] = $shopinfo['name'];
            $data[$key]['yuid'] = $info['uname'];
        }
        return $data;
    }
    public function delete_fangdan(){
        $this->del('Ynd_fangdan');
    }

    /*订单列表*/
    public function orderlist(){
        $aWhere = array(
            'token'=>$this->_sToken,
        );
        if($_GET['id']){
            $aWhere['yuid'] =$_GET['id'];
        }
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
                    '订单ID','会员号','购买数量','所需金额','所需LQ','所需CQ', '购买时间','付款状态','发货状态','结算状态','签收状态', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '详情',
                        'url'  => U('Ynd/orderinfo',array('token'=>$_SESSION['token'],'type'=>$_GET['type'],'cat_id'=>$_GET['id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Ynd/delete_orderlist',array('token'=>$_SESSION['token'],'cat_id'=>$_GET['id']))
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
            M('Ynd_order')->field('id,yuid,num,money,LQ,CQ,add_time,pay_status,send_status,is_balance,is_take')->where($aWhere)->order("id desc"),
            array($this,'orderlistindex')
        );
    }

    public function orderlistindex($data){
        foreach($data as $key=>$val){
            //$data[$key]['pro_id'] = $this->shopinfo($val['pro_id'])['name'];
	        $info = $this->info($val['yuid']);
            $data[$key]['yuid'] = $info['uname'];
            switch($val['pay_status']){
                case 0:$data[$key]['pay_status'] = '未付款';break;
                case 1:$data[$key]['pay_status'] = '已付款';break;
            }
            switch($val['send_status']){
                case 0:$data[$key]['send_status'] = '未发货';break;
                case 1:$data[$key]['send_status'] = '已发货';break;
            }
            switch($val['is_balance']){
                case 0:$data[$key]['is_balance'] = '未结算';break;
                case 1:$data[$key]['is_balance'] = '已结算';break;
            }
            switch($val['is_take']){
                case 0:$data[$key]['is_take'] = '未签收';break;
                case 1:$data[$key]['is_take '] = '已签收';break;
            }
        }
        return $data;
    }

    public function delete_orderlist(){
        $this->del('Ynd_order');
    }


    /*商品信息*/
    function product($pro_id){
        $info = M('Ynd_product')
            ->where(array('id'=>$pro_id))
            ->find();
        return $info;
    }

    /*查看放单的情况*/
    function fangdaninfo($fid){
        if('a'==$fid){
            $info['yuid'] = '总部';
            $info['exponent'] = '';
            $info['fprice'] = '';
        }else{
            $info = M('Ynd_fangdan')->where(array('id'=>$fid))->find();

        }
        return $info;
    }
    /*订单详情*/
    public function orderinfo(){
        $orderinfo = M('Ynd_order')->where(array('id'=>$_GET['id']))->find();
        $userinfo = $this->info($orderinfo['yuid']);
        $shoplist = M('Ynd_orderinfo')->where(array('oid'=>$_GET['id']))->select();
        $shopaddress = M('Ynd_address')->where(array('id'=>$orderinfo['address_id']))->find();

        foreach($shoplist as $key=>$val){
            $shoplist[$key]['pro_id'] = $this->product($val['pro_id']);
            $arr1 = json_decode($val['user_id'],true);
            $arr2 = array_keys($arr1);
            $arr3 = array_values($arr1);
            $fangdaninfo = array();
            for($i=0;$i<count($arr2);$i++){
                $fangdaninfo[$i]['num'] = $arr3[$i];
                $fangdaninfo[$i]['pinfo'] = $this->fangdaninfo($arr2[$i]);
            }
            $shoplist[$key]['fuinfos'] = $fangdaninfo;
        }
        //P($shoplist);
        $this->assign(array(
            'log'=>M('Kuaizhao')->where(array('id'=>$orderinfo['log_id']))->find(),
            'logs'=>M('Kuaizhao')->select(),
            'shoplist'=>$shoplist,
            'orderinfo'=>$orderinfo,
            'userinfo' =>$userinfo,
            'shopaddress' =>$shopaddress
        ));
        $this->display('orderinfo');
    }

    /**/
    public function logorder(){
        if(IS_AJAX){
            $id = $_GET['id'];
            $iTem = M('Ynd_order')->where(array('id'=>$_GET['id']))->find();
            if(!$iTem){$this->error('非法操作！');}
            $_POST['send_status'] = 1;
            $_POST['send_time'] = date('Y-m-d H:i;s');
            if(M('Ynd_order')->where(array('id'=>$_GET['id']))->save($_POST)){
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }
    }

    /*规则列表*/
    public function rule(){
        $aWhere = array(
            'token'=>$this->_sToken,
        );
        $this->table(array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Ynd/rule', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '更改规则',//2级
                        'url'    => U('Ynd/add_rule',array('token'=>$this->_sToken))
                    ),
                ),
                'tips' => array(//3级
                    '你可以在这里查看以往的规则'
                ),
                'Table_Header' => array(//4级
                    'ID','时间','规则'
                ),

                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Ynd_rule')->where($aWhere)->count(),
            M('Ynd_rule')->field('id,add_time,rule')->where($aWhere)->order("add_time desc"),
            array($this,'ruleindex')
        );
    }

    public function ruleindex($data){
        foreach($data as $key=>$val){
            switch($val['rule']){
                case 1:$data[$key]['rule'] = 'A:按最低放单指数优先，相同放单指数，放单时间优先的原则。';break;
                case 2:$data[$key]['rule'] = 'B:放单时间优先的原则';break;
            }
        }
        return $data;
    }

    /*更改规则*/
    public function set_rule($aaa){
        $this->$aaa('Ynd_rule',array(
             array('type'=>'select','title'=>"商品结算规则",'name'=>"rule",'value'=>'rule','msg'=>'选择商品结算规则','many'=>array(
                 array('content'=>'选择商品结算规则'),
                 array('value'=>'1', 'content'=>'A：最低放单价优先，相同放单价按放单时间优先的原则处理'),
                 array('value'=>'2','content'=>'B：放单时间优先的原则处理'),
             ))
        ),U('Ynd/rule',array('token'=>$_SESSION['token'])),array($this,'ruleinfos'));
    }

    public function ruleinfos($data){
        $data['token'] = $this->_sToken;
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function add_rule(){
        $this->set_rule(add);
    }

    /*商城首页图片上传管理*/

    public function piclist(){
        $oImgModel = M('Imag');
        $this->assign(array(
            'phone1'=>$oImgModel->where(array('token'=>$this->_sToken, 'app'=>'Ynd','type'=>'phone1'))->find(),
            'phone2'=>$oImgModel->where(array('token'=>$this->_sToken, 'app'=>'Ynd','type'=>'phone2'))->find(),
            'phone3'=>$oImgModel->where(array('token'=>$this->_sToken, 'app'=>'Ynd','type'=>'phone3'))->find(),
            'phone4'=>$oImgModel->where(array('token'=>$this->_sToken, 'app'=>'Ynd','type'=>'phone4'))->find(),
            'phone5'=>$oImgModel->where(array('token'=>$this->_sToken, 'app'=>'Ynd','type'=>'phone5'))->find(),
            'phone6'=>$oImgModel->where(array('token'=>$this->_sToken, 'app'=>'Ynd','type'=>'phone6'))->find(),
            'phone7'=>$oImgModel->where(array('token'=>$this->_sToken, 'app'=>'Ynd','type'=>'phone7'))->find(),
            'phone8'=>$oImgModel->where(array('token'=>$this->_sToken, 'app'=>'Ynd','type'=>'phone8'))->find(),

        ));
        $this->display('piclist');
    }




    /*Y用户相关信息日志*/
    public function log(){
       //' echo date('w');
        $aWhere = array(
            'token'=>$this->_sToken
        );
        $this->table(array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Ynd/log', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(),
                'tips' => array(//3级
                    '你可以在这里查看每个人的操作日志'
                ),
                'Table_Header' => array(//4级
                    'ID','相关操作','操作时间','操作人的微信昵称'
                ),

                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Ynd_recoads')->where($aWhere)->count(),
            M('Ynd_recoads')->field('id,info,add_time,openid')->where($aWhere)->order("add_time desc"),
            array($this,'logindex')
        );
    }
    public function logindex($data){
        foreach($data as $key=>$value){
            $opin = $this->wxinfo($value['openid']);
            $data[$key]['openid'] = $opin['nickname'];
        }
        return $data;
    }



    public function rerules(){
        $kan = M('Ynd_rule')->where(array('token'=>$this->token))->select();
        if(IS_AJAX){
            $cha = M('Ynd_rule')->where(array(
                'token'=>$this->token,
                'xq'=>$_POST['qx']
            ))->find();
            if($cha){
                $_POST['rule'] = $_POST['rule'];
                $_POST['xq'] =   $_POST['qx'];
                $_POST['add_time']=date('Y-m-d H:i:s');
                $_POST['token'] = $this->token;
                if(M('Ynd_rule')->where(array('token'=>$this->token,'xq'=>$_POST['qx']))->save($_POST)){
                    $this->success('修改成功！',U('Ynd/rerules',array('token'=>$this->token,'openid'=>$this->openid)));
                }else{
                    $this->error('修改失败');
                }
            }else {
                $_POST['rule'] = $_POST['rule'];
                $_POST['xq'] = $_POST['qx'];
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['token'] = $this->token;
                if (M('Ynd_rule')->add($_POST)) {
                    $this->success('操作成功！', U('Ynd/rerules', array('token' => $this->token, 'openid' => $this->openid)));
                } else {
                    $this->error('操作失败');
                }
            }
        }

        $this->assign('list',$kan);
        $this->display();
    }








}