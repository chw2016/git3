<?php
/**
 *  李铭  P2P系统
 * 2015.6.24
 **/
class Gta_cartAction extends Gta_commonAction
{

    public $_sTplBaseDir = 'User/default/miye';
    public $token;
    public $wxuser_id;
    public $tpl_dir = './tpl/User/default/gta/';

    public function _initialize()
    {
        parent::_initialize();
        //这里是应用权限判断
        if (session('?app_id')) {
            $app_id = explode(',', session('app_id'));
            $myapp = M('App_list')->field('enter_api')->where(array('id' => array('in', $app_id)))->select();
            $app_action = array();
            foreach ($myapp as $k => $v) {
                $a1 = explode('/', $v['enter_api']);
                array_push($app_action, $a1[1] . 'Action');
            }
            if (!in_array(__CLASS__, $app_action)) {
                $this->error2('您没有此应用的权限');
            }
        }
        //echo __CLASS__;

        $this->tpl = "tpl/User/default/helper/";
        //$this->pz=M("No_credit");
        $this->token = session('token');
        $this->wxuser_id = M('Wxuser')->where(array('token' => $this->token))->getField('id');

    }

    protected function setHeader()
    {
        if($_SESSION['gta_cw'] !=''){
            return array(
                array(
                    'name' => '车险订单',
                    'url' => U('gta_life_order', array('token' => $this->_sToken))
                ),
            );
        }else {
            return array(
                array(
                    'name' => '车辆基本险',
                    'url' => U('index', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '车辆附加险',
                    'url' => U('index_two', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '合作公司',
                    'url' => U('index_company', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '车险订单',
                    'url' => U('gta_life_order', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '车险设置',
                    'url' => U('cart_set', array('token' => $this->_sToken))
                ),

            );
        }
    }
    //车险设置
    public function cart_set(){
        //echo 88;die;

        $this->Edit('Gta_cart_set',array(
            /*array('title'=>"金卡赠送总佣金",'type'=>"input",'name'=>"jifen1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"白金赠送总佣金",'type'=>"input",'name'=>"jifen2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"钻石赠送总佣金",'type'=>"input",'name'=>"jifen3",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"1级下属佣金比例",'type'=>"input",'name'=>"bili1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级下属佣金比例",'type'=>"input",'name'=>"bili2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"3级下属佣金比例",'type'=>"input",'name'=>"bili3",'placeholder'=>'请保证1,2,3级佣金比例之和等于1'),*/
            array('title'=>"1级佣金比例",'type'=>"input",'name'=>"one",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级佣金比例",'type'=>"input",'name'=>"two",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"上传图片规格",'type'=>"textarea1",'name'=>"content",'placeholder'=>'多个请用英文逗号隔开,例如:身份证,房产证'),
        ),U('index',array('token'=>$this->token)));
    }


    /**
     *  主险
     **/
    public function index()
    {
         $aWhere['token'] = $this->_sToken;
        $aWhere['type'] = 0;//父级id=0
        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                         'name' => '添加基本险',
                         'url' => U('add_product',array('token'=>$this->token))
                     ),



                ),
                'tips' => array(
                    '这里可以添加车辆基本险!'
                ),
                'Table_Header' => array(
                    'ID', '险种名称', '操作'
                ),
                'List_Opt' => array(


                    array(
                        'name' => '修改',
                        'url' => U('edit_product')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_product')
                    ),


                ),
            ),

            M('Gta_cart')->where($aWhere)->count(),
            M('Gta_cart')->field('id,name')->where($aWhere)

        );
        $this->UDisplay('show1');
    }


    //添加主险
    public function add_product(){
        $this->add('Gta_cart',array(
            array('title'=>"基本险种名称",'type'=>"input",'name'=>"name",'msg'=>'基本险种名称不能为空'),
            array('title'=>"投保金额",'type'=>"textarea1",'name'=>"content",'msg'=>'投保金额不能为空'),
        ),U('index',array('token'=>$this->token)),array($this,'index1'));
    }
    //修改主险
    public function edit_product(){
        $this->Edit('Gta_cart',array(
            array('title'=>"基本险种名称",'type'=>"input",'name'=>"name",'msg'=>'基本险种名称不能为空'),
            array('title'=>"投保金额",'type'=>"textarea1",'name'=>"content",'msg'=>'投保金额不能为空'),
        ),U('index',array('token'=>$this->token)),array($this,'index1'));
    }
    public function index1($data){
        $data['type']=0;
        return $data;
    }
    //删除主险
    public function del_product(){
        $this->del('Gta_cart');
    }

    public function index_two()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['type'] = 1;//父级id=0
        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index_two', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加附加险',
                        'url' => U('add_product2',array('token'=>$this->token))
                    ),



                ),
                'tips' => array(
                    '这里可以添加车辆附加险!'
                ),
                'Table_Header' => array(
                    'ID', '险种名称', '操作'
                ),
                'List_Opt' => array(


                    array(
                        'name' => '修改',
                        'url' => U('edit_product2')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_product2')
                    ),


                ),
            ),

            M('Gta_cart')->where($aWhere)->count(),
            M('Gta_cart')->field('id,name')->where($aWhere)

        );
        $this->UDisplay('show1');
    }
    //添加主险
    public function add_product2(){
        $this->add('Gta_cart',array(
            array('title'=>"附加险种名称",'type'=>"input",'name'=>"name",'msg'=>'附加险种名称不能为空'),
            array('title'=>"投保金额",'type'=>"textarea1",'name'=>"content",'msg'=>'投保金额不能为空'),

        ),U('index_two',array('token'=>$this->token)),array($this,'index2'));
    }
    public function edit_product2(){
        $this->Edit('Gta_cart',array(
            array('title'=>"附加险种名称",'type'=>"input",'name'=>"name",'msg'=>'附加险种名称不能为空'),
            array('title'=>"投保金额",'type'=>"textarea1",'name'=>"content",'msg'=>'投保金额不能为空'),

        ),U('index_two',array('token'=>$this->token)),array($this,'index2'));
    }
    public function index2($data){
        $data['type']=1;
        return $data;
    }
    //删除主险
    public function del_product2(){
        $this->del('Gta_cart');
    }
//合作公司
    public function index_company()
    {
        $aWhere['token'] = $this->_sToken;

        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index_company', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加合作公司',
                        'url' => U('add_company',array('token'=>$this->token))
                    ),



                ),
                'tips' => array(
                    '这里可以添加合作公司!'
                ),
                'Table_Header' => array(
                    'ID', '公司名称', '操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '修改',
                        'url' => U('edit_two_product')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_two_product')
                    ),


                ),
            ),

            M('Gta_cart_company')->where($aWhere)->count(),
            M('Gta_cart_company')->field('id,name')->where($aWhere)

        );
        $this->UDisplay('show1');
    }
    /**
     *  副险
     **/


    //添加副险
    public function add_company(){
        $this->add('Gta_cart_company',array(
            array('title'=>"公司名称",'type'=>"input",'name'=>"name",'msg'=>'公司名称不能为空'),
        ),U('index_company',array('token'=>$this->token,'id'=>session('pid'))));
    }

    //修改副险
    public function edit_two_product(){
        $this->Edit('Gta_cart_company',array(
            array('title'=>"公司名称",'type'=>"input",'name'=>"name",'msg'=>'公司名称不能为空'),
        ),U('index_company',array('token'=>$this->token,'id'=>session('pid'))));
    }
    //删除副险
    public function del_two_product(){
        $this->del('Gta_cart_company');
    }
    /**
     *  产品项目
     **/
    public function two_product_product()
    {
        $aWhere['tp_gta_life_product.token'] = $this->_sToken;
       $aWhere['tp_gta_life_product.lid'] = $_GET['id'];
       session('pid2',$_GET['id']);
        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加产品项目',
                        'url' => U('add_item',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '返回副险',
                        'url' => U('two_product',array('token'=>$this->token,'id'=>session('pid')))
                    ),




                ),
                'tips' => array(
                    '这里可以添加主险下面的副险种!'
                ),
                'Table_Header' => array(
                    'ID', '产品名称', '所属副险名称','公司名称','投保范围','保障程度','保费高低', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '修改',
                        'url' => U('edit_item')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_item')
                    ),


                ),
            ),

            M('Gta_life_product')->join("join tp_gta_life  on tp_gta_life.id=tp_gta_life_product.lid")->where($aWhere)->count(),
            M('Gta_life_product')->join("join tp_gta_life  on tp_gta_life.id=tp_gta_life_product.lid")
                ->field('tp_gta_life_product.id,tp_gta_life_product.title,tp_gta_life.name,tp_gta_life_product.company_name,tp_gta_life_product.range,tp_gta_life_product.degree,tp_gta_life_product.money_gd')
                ->order('add_time desc')->where($aWhere)

        );
        $this->UDisplay('show1');
    }
    //添加项目
    public function add_item(){
        $this->add('Gta_life_product',array(
            array('title'=>"产品名称",'type'=>"input",'name'=>'title','msg'=>'产品名称不能为空'),
            array('title'=>"产品类型",'type'=>"input",'name'=>"product_type",'placeholder'=>'例如可填:年金普通分红'),
            array('title'=>"公司名字",'type'=>"input",'name'=>"company_name",'placeholder'=>'例如可填:天安保险'),
            array('type'=>"img",'many'=>array(
                array('title'=>"公司logo",'name'=>"company_logo")
            )),
            array('title'=>"投保范围",'type'=>"input",'name'=>"range",'placeholder'=>'例如可填:出生满8岁-70周岁'),
            array('title'=>"投保期间",'type'=>"input",'name'=>"qijian",'placeholder'=>'例如可填:致88周岁'),
            array('title'=>"交费方式",'type'=>"input",'name'=>"way",'placeholder'=>'例如可填:一次性交清'),
            array('title'=>"交费年限",'type'=>"input",'name'=>"nianxian",'placeholder'=>'例如可填:3年/5年'),
            array('title'=>"风险计算器",'type'=>"input",'name'=>"jsq",'placeholder'=>'填写一个url地址'),
            array('title'=>"保障程度",'type'=>"input",'name'=>"degree",'placeholder'=>'可填0-10,越大，越排在前面'),
            array('title'=>"保费高低",'type'=>"input",'name'=>"money_gd",'placeholder'=>'可填0-10,越大，越排在前面'),

            array('title'=>'详情','type'=>'textarea','name'=>'content')
        ),U('two_product_product',array('token'=>$this->token,'id'=>session('pid2'))),array($this,'add_item1'));
    }
   public function add_item1($data){
       $data['lid']=session('pid2');
       return $data;
   }
    //修改项目
    public function edit_item(){
        $this->Edit('Gta_life_product',array(
            array('title'=>"产品名称",'type'=>"input",'name'=>'title','msg'=>'产品名称不能为空'),
            array('title'=>"产品类型",'type'=>"input",'name'=>"product_type",'placeholder'=>'例如可填:年金普通分红'),
            array('title'=>"公司名字",'type'=>"input",'name'=>"company_name",'placeholder'=>'例如可填:天安保险'),
            array('type'=>"img",'many'=>array(
                array('title'=>"公司logo",'name'=>"company_logo")
            )),
            array('title'=>"投保范围",'type'=>"input",'name'=>"range",'placeholder'=>'例如可填:出生满8岁-70周岁'),
            array('title'=>"投保期间",'type'=>"input",'name'=>"qijian",'placeholder'=>'例如可填:致88周岁'),
            array('title'=>"交费方式",'type'=>"input",'name'=>"way",'placeholder'=>'例如可填:一次性交清'),
            array('title'=>"交费年限",'type'=>"input",'name'=>"nianxian",'placeholder'=>'例如可填:3年/5年'),
            array('title'=>"风险计算器",'type'=>"input",'name'=>"jsq",'placeholder'=>'填写一个url地址'),
            array('title'=>"保障程度",'type'=>"input",'name'=>"degree",'placeholder'=>'可填0-10,越大，越排在前面'),
            array('title'=>"保费高低",'type'=>"input",'name'=>"money_gd",'placeholder'=>'可填0-10,越大，越排在前面'),

            array('title'=>'详情','type'=>'textarea','name'=>'content')
        ),U('two_product_product',array('token'=>$this->token,'id'=>session('pid2'))),array($this,'add_item1'));
    }
    //删除项目
    public function del_item(){
        $this->del('Gta_life_product');
    }
    //车险订单
    public function gta_life_order()
    {
        //P($_SESSION);
        $aWhere['token'] = $this->_sToken;
        if($_SESSION['gta_cw'] == '核算权限'){
            $aWhere['status'] = 2;
        }elseif($_SESSION['gta_cw'] == '客服权限'){
            $aWhere['status'] = array('in','0,1');
        }elseif($_SESSION['gta_cw'] == '出纳权限'){
            $aWhere['status'] = 3;
        }elseif($_SESSION['gta_cw'] == '会计权限'){
            $aWhere['status'] = array('in','4,5');
        }
        if (IS_POST) {
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
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        if($_SESSION['gta_cw'] != ''){
            $this->table(
                array(
                    'HeadHover' => U('gta_life_order', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        array(
                            'name' => '导出excel表',
                            'type'=>'daochu',
                            'url' => U('excel_order',array('token'=>$this->token))
                        ),
                    ),
                    'tips' => array(
                        '人寿险订单列表!'
                    ),
                    'Table_Header' => array(
                        'ID', '客户姓名','状态','下单时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '查看',
                            'url' => U('gta_cart_info')
                        ),
                    ),
                    'search'=>array(
                        array('title'=>'客户姓名','name'=>'li_name'),
                        array('type'=>'br'),
                        array('title'=>'下单时间','name'=>'be_add_time','type'=>'between')
                    )
                ),

                M('Gta_cart_order')->where($aWhere)->count(),
                M('Gta_cart_order')->field('id,name,status,add_time')->order('add_time desc')->where($aWhere),
                array($this,'gta_life_order1')
            );
        }else{
            $this->table(
                array(
                    'HeadHover' => U('gta_life_order', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        array(
                            'name' => '导出excel表',
                            'type'=>'daochu',
                            'url' => U('excel_order',array('token'=>$this->token))
                        ),
                    ),
                    'tips' => array(
                        '人寿险订单列表!'
                    ),
                    'Table_Header' => array(
                        'ID', '客户姓名','状态','下单时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '查看',
                            'url' => U('gta_cart_info')
                        ),
                    ),
                    'search'=>array(
                        array('title'=>'客户姓名','name'=>'li_name'),
                        array('title'=>'订单状态','name'=>'eq_status','type'=>'select','many'=>array(
                            array('value'=>'0','name'=>'新订单'),
                            array('value'=>'1','name'=>'处理中'),
                            array('value'=>'2','name'=>'待录佣'),
                            array('value'=>'3','name'=>'待处理'),
                            array('value'=>'4','name'=>'待核审'),
                            array('value'=>'5','name'=>'交易成功'),
                            array('value'=>'-2','name'=>'交易失败'),
                        )),
                        array('type'=>'br'),
                        array('title'=>'下单时间','name'=>'be_add_time','type'=>'between')
                    )
                ),

                M('Gta_cart_order')->where($aWhere)->count(),
                M('Gta_cart_order')->field('id,name,status,add_time')->order('add_time desc')->where($aWhere),
                array($this,'gta_life_order1')
            );
        }

        $this->assign('ring',1);
        $lasttime =M('Gta_cart_order')->where(array('token'=>$this->token))->order('add_time desc')->limit(1)->getField('add_time');
        $this->assign('ring_ring','/upload/ring/cart.mp3');
        $this->assign('ring_model','Gta_cart_order');
        $this->assign('lasttime',$lasttime);
        $this->UDisplay('show1');
    }
    public function gta_life_order1($data){
        foreach($data as $k=>$v){
            switch($v['status']){
                case 0: $data[$k]['status']='新订单';break;
                case 1: $data[$k]['status']='处理中';break;
                case 2: $data[$k]['status']='待录佣';break;
                case 3: $data[$k]['status']='待处理';break;
                case 4: $data[$k]['status']='待核审';break;
                case 5: $data[$k]['status']='交易成功';break;
                case -2: $data[$k]['status']='交易失败';break;
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }


    /*订单导出excel_order*/
    public function excel_order(){
        $aWhere=$this->search($_POST);
        $aWhere['token'] = $this->token;
        if($_SESSION['gta_cw'] == '核算权限'){
            $aWhere['status'] = 2;
        }elseif($_SESSION['gta_cw'] == '客服权限'){
            $aWhere['status'] = array('in','0,1');
        }elseif($_SESSION['gta_cw'] == '出纳权限'){
            $aWhere['status'] = 3;
        }elseif($_SESSION['gta_cw'] == '会计权限'){
            $aWhere['status'] = array('in','4,5');
        }
        $list = M('Gta_cart_order')
            ->field('id,orderid,title,money,name,add_time,status')
            ->order('add_time desc')
            ->where($aWhere)
            ->select();
        foreach($list as $k=>$v){
            switch($v['status']){
                case 0: $list[$k]['status']='新订单';break;
                case 1: $list[$k]['status']='处理中';break;
                case 2: $list[$k]['status']='待录佣';break;
                case 3: $list[$k]['status']='待处理';break;
                case 4: $list[$k]['status']='待核审';break;
                case 5: $list[$k]['status']='交易成功';break;
                case -2: $list[$k]['status']='交易失败';break;
            }
            $list[$k]['type'] = '车险';
            $uid = M('Gta_cart_order')->where(array('id'=>$v['id']))->getField('uid');
            $info = M('Gta_users')->where(array('id'=>$uid))->find();
            $list[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            $list[$k]['y1'] = $v['name'];
            $info1=M('Gta_users')->field('name,dopenid')->where(array('openid'=>$info['dopenid']))->find();
            $list[$k]['y2']=$info1['name'];
            unset($list[$k]['dopenid']);
            if($info1['dopenid']){
                $info2=M('Gta_users')->field('name,dopenid')->where(array('openid'=>$info1['dopenid']))->find();
                $list[$k]['y3']=$info2['name'];
            }
            /*if($info2['dopenid']){
                $info3=M('Gta_users')->field('name,dopenid')->where(array('openid'=>$info2['dopenid']))->find();
                $list[$k]['y3']=$info3['name'];
            }*/

        }
        Excel::arr2ExcelDownload($list,array('数据ID','订单编号','产品名称','产品金额','客户姓名','成交时间','订单状态','产品类型','成单人','一级关系人','上二级关系人'),'车险订单');

    }



    //车险订单详情
    public function gta_cart_info(){
        if(IS_POST){
            if(M('Gta_cart_order')->where(array('id'=>$_GET['id']))->save(array('yonjing'=>$_POST['yonjing'],'status'=>3))){
                //算一二级佣金
                $this->common2('Gta_cart_order',$_GET['id'],'Gta_cart_set',$_POST['yonjing'],6);
                $this->success2('设置成功',U('Gta_cart/gta_life_order',array('token'=>$this->token)));
            }else{
                $this->error2('投置失败');
            }
        }else{
            $info=M('Gta_cart_order')->find($_GET['id']);
            $info['one_name']=explode(',',$info['one_name']);
            $info['two_name']=explode(',',$info['two_name']);
            $info['companys']=explode(',',$info['companys']);
            $info['imgs']=explode(',',$info['imgs']);
            $info1=M('Wxusers')->field('nickname,headimgurl')->where(array('uid'=>$this->wxuser_id,'openid'=>$info['openid']))->find();
            $info['oder_info']=json_decode($info['oder_info'],true);
            $num=count($info['oder_info']['money']);
            $money=$info['oder_info']['money'];
            $title=$info['oder_info']['title'];
            $abatement=$info['oder_info']['abatement'];
            for($i=0;$i<$num;$i++){
                $info['oder_info1'][]=array('title'=>$title[$i],'money'=>$money[$i],'abatement'=>$abatement[$i]);
            }
            $this->assign('info1',$info1);
            $this->assign('num',$num);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            //P($info);exit;
            $this->assign('info',$info);
            $this->display($this->tpl_dir.'gta_cart_info.html');
        }

    }
    //处理订单
    //贷款操作状态的处理
    public function chuli(){
        //echo 4;die;
        if(IS_POST){
            if($_POST['str']==1){//去处理
                if(M('Gta_cart_order')->where(array('id'=>$_GET['id']))->save(array('status'=>1))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }

            }
            if($_POST['str']==2){//改变为成功还是失败
                if(M('Gta_cart_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$_GET['kk']))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }
            if($_POST['str']==3){//设置金额
                if(M('Gta_cart_order')->where(array('id'=>$_GET['id']))->save(array('money'=>$_GET['kk']))){
               //     $this->common('Gta_cart_order',$_GET['id'],'Gta_cart_set',$_GET['kk'],6);
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }

        }
    }
    public function chuli2(){
        if(IS_AJAX){
            $status = $_POST['status'];
            if(M('Gta_cart_order')->where(array('id'=>$_GET['id']))->find()){
                if(M('Gta_cart_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$status))){
                    echo json_encode(array('status'=>1,'info'=>'操作成功'));die;
                }else{
                    echo json_encode(array('status'=>0,'info'=>'操作失败'));die;
                }
            }else{
                $this->error('非法操作');
            }
        }
    }



}