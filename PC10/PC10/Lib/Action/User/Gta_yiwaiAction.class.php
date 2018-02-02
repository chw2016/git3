<?php
/**
 *  李铭  P2P系统
 * 2015.6.24
 **/
class Gta_yiwaiAction extends Gta_commonAction
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
                    'name' => '意外险订单',
                    'url' => U('gta_life_order', array('token' => $this->_sToken))
                ),
            );
        }else{
            return array(
                array(
                    'name' => '意外险种',
                    'url' => U('index', array('token' => $this->_sToken))
                ),
              /*  array(
                    'name' => '个人意外险',
                    'url' => U('one', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '旅游意外险',
                    'url' => U('luyou', array('token' => $this->_sToken))
                ),*/
                array(
                    'name' => '意外险订单',
                    'url' => U('gta_life_order', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '意外险设置',
                    'url' => U('life_set', array('token' => $this->_sToken))
                ),



            );
        }
    }


    /**
     *  主险
     **/
    public function index()
    {
        $aWhere['token'] = $this->_sToken;
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                         'name' => '添加意外险',
                         'url' => U('add_product',array('token'=>$this->token))
                     ),
                ),
                'tips' => array(
                    '这里可以添加意外险!'
                ),
                'Table_Header' => array(
                    'ID', '险种名称', 'logo小图标', '操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '保险项目',
                        'url' => U('two_product')
                    ),
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

            M('Gta_yiwai')->where($aWhere)->count(),
            M('Gta_yiwai')->field('id,name,pic_logo')->where($aWhere),
            array($this,'index1')

        );
        $this->UDisplay('show1');
    }
    public function index1($data){
        foreach($data as $k=>$v){
            $data[$k]['pic_logo']="<img src=".$v['pic_logo']."  width='70' />";

        }
        return $data;
    }

    //添加主险
    public function add_product(){
        $this->assign('tishi','您如果添加了图文介绍，代表直接展示，不需要用户再输入投保金额!');
        $this->add('Gta_yiwai',array(
            array('title'=>"意外险名称",'type'=>"input",'name'=>"name",'msg'=>'意外险名称不能为空'),
            array('type'=>"img",'many'=>array(
                array('title'=>"logo小图标",'name'=>"pic_logo")
            )),
        ),U('index',array('token'=>$this->token)));
    }
    //修改主险
    public function edit_product(){
        $this->assign('tishi','您如果添加了图文介绍，代表直接展示，不需要用户再输入投保金额!');
        $this->Edit('Gta_yiwai',array(
            array('title'=>"意外险名称",'type'=>"input",'name'=>"name",'msg'=>'意外险名称不能为空'),
            array('type'=>"img",'many'=>array(
                array('title'=>"logo小图标",'name'=>"pic_logo")
            )),
        ),U('index',array('token'=>$this->token)));
    }
    //删除主险
    public function del_product(){
        $this->del('Gta_yiwai');
    }
    /**
     *  副险
     **/
    public function two_product()
    {
        //根据名字不同
        $k_name=M('Gta_yiwai')->where(array('id'=>$_GET['id']))->getField('name');
        if($k_name=='个人意外险'){
            Header("location:".U('one'));
            die;
        }
        if($k_name=='旅游意外险'){
            Header("location:".U('luyou'));
            die;
        }
        $aWhere['token'] = $this->_sToken;
        $aWhere['yid'] = $_GET['id'];
        session('yiwai_yid',$_GET['id']);
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
                        'name' => '添加项目',
                        'url' => U('add_two_product',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '返回',
                        'url' => U('index',array('token'=>$this->token))
                    ),



                ),
                'tips' => array(
                    '【'.M('Gta_yiwai')->where(array('id'=>$_GET['id']))->getField('name').'】下面的保险项目列表!'
                ),
                'Table_Header' => array(
                    'ID', '项目名称','金额范围', '操作'
                ),
                'List_Opt' => array(
                   /* array(
                        'name' => '产品项目',
                        'url' => U('two_product_product')
                    ),*/
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

            M('Gta_yiwai_item')->where($aWhere)->count(),
            M('Gta_yiwai_item')->field('id,name,money_range')->where($aWhere)

        );
        $this->UDisplay('show1');
    }

    //添加副险
    public function add_two_product(){
        $this->add('Gta_yiwai_item',array(
            array('title'=>"项目名称",'type'=>"input",'name'=>"name",'msg'=>'项目名称不能为空'),
            array('title'=>"金额范围",'type'=>"input",'name'=>"money_range",'msg'=>'金额范围不能为空','placeholder'=>'例如填写:5万-10万'),

        ),U('two_product',array('token'=>$this->token,'id'=>session('yiwai_yid'))),array($this,'add_two_product1'));
    }
    public function add_two_product1($data){
        $data['yid']=session('yiwai_yid');
        return $data;
    }
    //修改副险
    public function edit_two_product(){
        $this->Edit('Gta_yiwai_item',array(
            array('title'=>"项目名称",'type'=>"input",'name'=>"name",'msg'=>'项目名称不能为空'),
            array('title'=>"金额范围",'type'=>"input",'name'=>"money_range",'msg'=>'金额范围不能为空','placeholder'=>'例如填写:5万-10万'),
        ),U('two_product',array('token'=>$this->token,'id'=>session('yiwai_yid'))),array($this,'add_two_product1'));
    }
    //删除副险
    public function del_two_product(){
        $this->del('Gta_yiwai_item');
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
    //人寿险订单
    public function gta_life_order()
    {
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
                    //'abc' => 123,
                    //  'id' => 'name',//如果主键不是id，则需要设置
                    'HeadHover' => U('gta_life_order', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        /* array(
                             'name' => '添加人寿险主险',
                             'url' => U('add_product',array('token'=>$this->token))
                         ),*/

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
                        'ID', '客户姓名', '购买险种','状态','下单时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '查看',
                            'url' => U('yiwai_info')
                        ),
                    ),
                    'search'=>array(
                        array('title'=>'险种','name'=>'li_title'),
                        array('title'=>'客户姓名','name'=>'li_name'),
                        array('type'=>'br'),
                        array('title'=>'下单时间','name'=>'be_add_time','type'=>'between')
                    )
                ),

                M('Gta_yiwai_order')->where($aWhere)->count(),
                M('Gta_yiwai_order')->field('id,name,title,status,add_time')->order('add_time desc')->where($aWhere),
                array($this,'gta_life_order1')
            );
        }else{
            $this->table(
                array(
                    //'abc' => 123,
                    //  'id' => 'name',//如果主键不是id，则需要设置
                    'HeadHover' => U('gta_life_order', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        /* array(
                             'name' => '添加人寿险主险',
                             'url' => U('add_product',array('token'=>$this->token))
                         ),*/

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
                        'ID', '客户姓名', '购买险种','状态','下单时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '查看',
                            'url' => U('yiwai_info')
                        ),
                    ),
                    'search'=>array(
                        array('title'=>'险种','name'=>'li_title'),
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

                M('Gta_yiwai_order')->where($aWhere)->count(),
                M('Gta_yiwai_order')->field('id,name,title,status,add_time')->order('add_time desc')->where($aWhere),
                array($this,'gta_life_order1')
            );
        }

        $this->assign('ring',1);
        $lasttime =M('Gta_yiwai_order')->where(array('token'=>$this->token))->order('add_time desc')->limit(1)->getField('add_time');
        $this->assign('ring_ring','/upload/ring/yiwai.mp3');
        $this->assign('ring_model','Gta_yiwai_order');
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
        $list = M('Gta_yiwai_order')
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
            $list[$k]['type'] = '意外险';
            $uid = M('Gta_yiwai_order')->where(array('id'=>$v['id']))->getField('uid');
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

        Excel::arr2ExcelDownload($list,array('数据ID','订单编号','产品名称','产品金额','客户姓名','成交时间','订单状态','产品类型','成单人','一级关系人','上二级关系人'),'意外险订单');

    }







    //人寿险订单详情
    public function yiwai_info(){
        if(IS_POST){

            if(M('Gta_yiwai_order')->where(array('id'=>$_GET['id']))->save(array('yonjing'=>$_POST['yonjing'],'status'=>3))){
                //算一二级佣金
                $this->common2('Gta_yiwai_order',$_GET['id'],'Gta_yiwai_set',$_POST['yonjing'],7);
                $this->success2('设置成功',U('Gta_yiwai/gta_life_order',array('token'=>$this->token)));
            }else{
                $this->error2('投置失败');
            }
        }else{
            $info=M('Gta_yiwai_order')->find($_GET['id']);
            $info['items']=json_decode($info['items']);
            $info['items']=json2arr($info['items']);
            $info['imgs']=explode(',',$info['imgs']);
            $info1=M('Wxusers')->field('nickname,headimgurl')->where(array('uid'=>$this->wxuser_id,'openid'=>$info['openid']))->find();

            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            $this->assign('info',$info);
            $this->assign('info1',$info1);
            $this->display($this->tpl_dir.'yiwai_info.html');
        }

    }
    //处理订单
    //贷款操作状态的处理
    public function chuli(){
        //echo 4;die;
        if(IS_POST){
            if($_POST['str']==1){//去处理
                if(M('Gta_yiwai_order')->where(array('id'=>$_GET['id']))->save(array('status'=>1))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }

            }
            if($_POST['str']==2){//改变为成功还是失败
                if(M('Gta_yiwai_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$_GET['kk']))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }
            if($_POST['str']==3){//设置金额
                if(M('Gta_yiwai_order')->where(array('id'=>$_GET['id']))->save(array('money'=>$_GET['kk']))){
                   // $this->common('Gta_yiwai_order',$_GET['id'],'Gta_yiwai_set',$_GET['kk'],7);
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
            if(M('Gta_yiwai_order')->where(array('id'=>$_GET['id']))->find()){
                if(M('Gta_yiwai_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$status))){
                    echo json_encode(array('status'=>1,'info'=>'操作成功'));die;
                }else{
                    echo json_encode(array('status'=>0,'info'=>'操作失败'));die;
                }
            }else{
                $this->error('非法操作');
            }
        }
    }
    //险种设置
    public function life_set(){
         //echo 88;die;

        $this->Edit('Gta_yiwai_set',array(
           /* array('title'=>"金卡赠送总佣金",'type'=>"input",'name'=>"jifen1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"白金赠送总佣金",'type'=>"input",'name'=>"jifen2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"钻石赠送总佣金",'type'=>"input",'name'=>"jifen3",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"1级下属佣金比例",'type'=>"input",'name'=>"bili1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级下属佣金比例",'type'=>"input",'name'=>"bili2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"3级下属佣金比例",'type'=>"input",'name'=>"bili3",'placeholder'=>'请保证1,2,3级佣金比例之和等于1'),*/
            array('title'=>"1级佣金比例",'type'=>"input",'name'=>"one",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级佣金比例",'type'=>"input",'name'=>"two",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"个人意外险图片规格",'type'=>"textarea1",'name'=>"content2",'placeholder'=>'多个请用英文逗号隔开,例如:身份证,房产证'),
            array('title'=>"旅游意外险图片规格",'type'=>"textarea1",'name'=>"content3",'placeholder'=>'多个请用英文逗号隔开,例如:身份证,房产证'),
            array('title'=>"其他意外险图片规格",'type'=>"textarea1",'name'=>"content",'placeholder'=>'多个请用英文逗号隔开,例如:身份证,房产证'),
            array('title'=>"职业种类",'type'=>"textarea1",'name'=>"zhiye",'placeholder'=>'多个请用英文逗号隔开,例如:程序员,教师'),
        ),U('index',array('token'=>$this->token)));
    }
    //个人意外险
    public function one()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['type']=1;
       // echo $this->_sToken;
      //  p(M('Gta_yiwa2')->field('id,name')->where(array('token'=>$this->_sToken))->select());die;
        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('one', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加分类',
                        'url' => U('add_one',array('token'=>$this->token))
                    ),



                ),
                'tips' => array(
                    '个人意外险分类列表!'
                ),
                'Table_Header' => array(
                    'ID', '分类名称', '操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '保险项目',
                        'url' => U('one_product')
                    ),
                    array(
                        'name' => '修改',
                        'url' => U('edit_one')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_one')
                    ),
                ),
            ),

            M('Gta_yiwa2')->where($aWhere)->count(),
            M('Gta_yiwa2')->field('id,name')->where($aWhere)
        );
        //echo M('Gta_yiwa2')->getLastSql();die;
        $this->UDisplay('show1');
    }
    public function add_one(){
        $this->add('Gta_yiwa2',array(
            array('title'=>"分类名称",'type'=>"input",'name'=>'name','msg'=>'名称不能为空'),
        ),U('one',array('token'=>$this->token)),array($this,'add_one1'));
    }
    public function add_one1($data){
        $data['type']=1;
        return $data;
    }
    public function edit_one(){
        $this->Edit('Gta_yiwa2',array(
            array('title'=>"分类名称",'type'=>"input",'name'=>'name','msg'=>'名称不能为空'),
        ),U('one',array('token'=>$this->token)));
    }
    public function del_one(){
        $this->del('Gta_yiwa2');
    }
    //保险项项目列表
    public function one_product(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['yid']=$_GET['id'];
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            $aWhere['yid']=$_GET['id'];
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('one', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加产品',
                        'url' => U('add_oneproduct',array('token'=>$this->token,'yid'=>$_GET['id']))
                    ),


                ),
                'tips' => array(
                    '个人意外险 【'.M('Gta_yiwa2')->where(array('id'=>$_GET['id']))->getField('name').'】分类下的产品列表'
                ),
                'Table_Header' => array(
                    'ID', '险种名称','保险公司图片','价格', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('edit_oneproduct',array('yid'=>$_GET['id']))
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_oneproduct')
                    ),

                ),
                'search'=>array(
                    array('title'=>'产品名称','name'=>'li_name'),

                )
            ),

            M('Gta_ywproduct')->where($aWhere)->count(),
            M('Gta_ywproduct')->field('id,name,logo,price')->where($aWhere),
            array($this,'home1')

        );
        $this->UDisplay('show1');
    }
    public function home1($data){
        foreach($data as $k=>$v){
            $data[$k]['logo']="<img src='".$v['logo']."' width=70px />";
        }
        return $data;
    }
    //添加项目
    public function add_oneproduct(){
        $this->add('Gta_ywproduct',array(
            array('title'=>"产品名称",'type'=>"input",'name'=>'name'),
            array('type'=>"img",'many'=>array(
                array('title'=>"公司logo",'name'=>"logo")
            )),
            array('title'=>"产品价格",'type'=>"input",'name'=>'price'),
            array('title'=>"保障程度",'type'=>"input",'name'=>'sort','placeholder'=>'排序用的，填写数字，值越大代表保障程度越高'),
            array('title'=>"简介",'type'=>"textarea1",'name'=>"content",'placeholder'=>''),
            array('title'=>"动态添加项",'type'=>"kuozhang",'placeholder'=>''),
            array('title'=>"产品价格",'type'=>"hidden_true",'name'=>'yid','value'=>$_GET['yid']),
        ),U('one_product',array('token'=>$this->token,'id'=>$_GET['yid'])));
    }
    public function edit_oneproduct(){
        $this->Edit('Gta_ywproduct',array(
            array('title'=>"产品名称",'type'=>"input",'name'=>'name'),
            array('type'=>"img",'many'=>array(
                array('title'=>"公司logo",'name'=>"logo")
            )),
            array('title'=>"产品价格",'type'=>"input",'name'=>'price'),
            array('title'=>"保障程度",'type'=>"input",'name'=>'sort','placeholder'=>'排序用的，填写数字，值越大代表保障程度越高'),
            array('title'=>"简介",'type'=>"textarea1",'name'=>"content",'placeholder'=>''),
            array('title'=>"动态添加项",'type'=>"kuozhang",'placeholder'=>''),
        ),U('one_product',array('token'=>$this->token,'id'=>$_GET['yid'])));
    }
    public function del_oneproduct(){
        $this->del('Gta_ywproduct');
    }
    //下面是旅游
    public function luyou()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['type']=2;
        // echo $this->_sToken;
        //  p(M('Gta_yiwa2')->field('id,name')->where(array('token'=>$this->_sToken))->select());die;
        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('luyou', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加分类',
                        'url' => U('add_luyou',array('token'=>$this->token))
                    ),



                ),
                'tips' => array(
                    '旅游意外险分类列表!'
                ),
                'Table_Header' => array(
                    'ID', '分类名称', '操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '保险项目',
                        'url' => U('luyou_product')
                    ),
                    array(
                        'name' => '修改',
                        'url' => U('edit_luyou')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_luyou')
                    ),
                ),
            ),

            M('Gta_yiwa2')->where($aWhere)->count(),
            M('Gta_yiwa2')->field('id,name')->where($aWhere)
        );
        //echo M('Gta_yiwa2')->getLastSql();die;
        $this->UDisplay('show1');
    }
    public function add_luyou(){
        $this->add('Gta_yiwa2',array(
            array('title'=>"分类名称",'type'=>"input",'name'=>'name','msg'=>'名称不能为空'),
            array('title'=>"分类名称",'type'=>"hidden_true",'name'=>'type','value'=>'2'),
        ),U('luyou',array('token'=>$this->token)));
    }

    public function edit_luyou(){
        $this->Edit('Gta_yiwa2',array(
            array('title'=>"分类名称",'type'=>"input",'name'=>'name','msg'=>'名称不能为空'),
        ),U('luyou',array('token'=>$this->token)));
    }
    public function del_luyou(){
        $this->del('Gta_yiwa2');
    }
    public function luyou_product(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['yid']=$_GET['id'];
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            $aWhere['yid']=$_GET['id'];
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('luyou', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加产品',
                        'url' => U('add_luyouproduct',array('token'=>$this->token,'yid'=>$_GET['id']))
                    ),


                ),
                'tips' => array(
                    '旅游意外险 【'.M('Gta_yiwa2')->where(array('id'=>$_GET['id']))->getField('name').'】分类下的产品列表'
                ),
                'Table_Header' => array(
                    'ID', '险种名称','保险公司图片','价格', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('edit_luyouproduct',array('yid'=>$_GET['id']))
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_luyouproduct')
                    ),

                ),
                'search'=>array(
                    array('title'=>'产品名称','name'=>'li_name'),

                )
            ),

            M('Gta_ywproduct')->where($aWhere)->count(),
            M('Gta_ywproduct')->field('id,name,logo,price')->where($aWhere),
            array($this,'home1')

        );
        $this->UDisplay('show1');
    }
    public function add_luyouproduct(){
        $this->add('Gta_ywproduct',array(
            array('title'=>"产品名称",'type'=>"input",'name'=>'name'),
            array('type'=>"img",'many'=>array(
                array('title'=>"公司logo",'name'=>"logo")
            )),
            array('title'=>"产品价格",'type'=>"input",'name'=>'price'),
            array('title'=>"保障程度",'type'=>"input",'name'=>'sort','placeholder'=>'排序用的，填写数字，值越大代表保障程度越高'),
            array('title'=>"简介",'type'=>"textarea1",'name'=>"content",'placeholder'=>''),
            array('title'=>"动态添加项",'type'=>"kuozhang",'placeholder'=>''),
            array('title'=>"产品价格",'type'=>"hidden_true",'name'=>'yid','value'=>$_GET['yid']),
        ),U('luyou_product',array('token'=>$this->token,'id'=>$_GET['yid'])));
    }
    public function edit_luyouproduct(){
        $this->Edit('Gta_ywproduct',array(
            array('title'=>"产品名称",'type'=>"input",'name'=>'name'),
            array('type'=>"img",'many'=>array(
                array('title'=>"公司logo",'name'=>"logo")
            )),
            array('title'=>"产品价格",'type'=>"input",'name'=>'price'),
            array('title'=>"保障程度",'type'=>"input",'name'=>'sort','placeholder'=>'排序用的，填写数字，值越大代表保障程度越高'),
            array('title'=>"简介",'type'=>"textarea1",'name'=>"content",'placeholder'=>''),
            array('title'=>"动态添加项",'type'=>"kuozhang",'placeholder'=>''),
        ),U('luyou_product',array('token'=>$this->token,'id'=>$_GET['yid'])));
    }
    public function del_luyouproduct(){
        $this->del('Gta_ywproduct');
    }
}