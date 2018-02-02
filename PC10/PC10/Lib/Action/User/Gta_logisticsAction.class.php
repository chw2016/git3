<?php
/**
 *  李铭  P2P系统
 * 2015.6.24
 **/
class Gta_logisticsAction extends Gta_commonAction
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
                    'name' => '责任险订单',
                    'url' => U('index', array('token' => $this->_sToken))
                ),
            );
        }else{
            return array(
                array(
                    'name' => '责任险订单',
                    'url' => U('index', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '责任险设置',
                    'url' => U('logistics_set', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '责任险产品',
                    'url' => U('one', array('token' => $this->_sToken))
                ),
            );
        }
    }

    //人寿险订单
    public function index()
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
                    'HeadHover' => U('index', array('token' => $this->_sToken)),
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
                        '物流险订单列表!'
                    ),
                    'Table_Header' => array(
                        'ID','保险分类', '客户姓名','状态','下单时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '查看',
                            'url' => U('logistics_info')
                        ),
                    ),
                    'search'=>array(
                        array('title'=>'客户姓名','name'=>'li_name'),
                        array('type'=>'br'),
                        array('title'=>'下单时间','name'=>'be_add_time','type'=>'between')
                    )
                ),

                M('Gta_logistics_order')->where($aWhere)->count(),
                M('Gta_logistics_order')->field('id,title,name,status,add_time')->order('add_time desc')->where($aWhere),
                array($this,'gta_life_order1')
            );
        }else{
            $this->table(
                array(
                    //'abc' => 123,
                    //  'id' => 'name',//如果主键不是id，则需要设置
                    'HeadHover' => U('index', array('token' => $this->_sToken)),
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
                        '物流险订单列表!'
                    ),
                    'Table_Header' => array(
                        'ID','保险分类', '客户姓名','状态','下单时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '查看',
                            'url' => U('logistics_info')
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

                M('Gta_logistics_order')->where($aWhere)->count(),
                M('Gta_logistics_order')->field('id,title,name,status,add_time')->order('add_time desc')->where($aWhere),
                array($this,'gta_life_order1')
            );
        }

        $this->assign('ring',1);
        $lasttime =M('Gta_logistics_order')->where(array('token'=>$this->token))->order('add_time desc')->limit(1)->getField('add_time');
        $this->assign('ring_ring','/upload/ring/zeren.mp3');
        $this->assign('ring_model','Gta_logistics_order');
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
        $list = M('Gta_logistics_order')
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
            $list[$k]['type'] = '责任险';
            $uid = M('Gta_logistics_order')->where(array('id'=>$v['id']))->getField('uid');
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
        Excel::arr2ExcelDownload($list,array('数据ID','订单编号','产品名称','产品金额','客户姓名','成交时间','订单状态','产品类型','成单人','一级关系人','上二级关系人'),'责任险订单');

    }



    //人寿险订单详情
    public function logistics_info(){
        if(IS_POST){
            if(M('Gta_logistics_order')->where(array('id'=>$_GET['id']))->save(array('yonjing'=>$_POST['yonjing'],'status'=>3))){
                //算一二级佣金
                $this->common2('Gta_logistics_order',$_GET['id'],'Gta_logistics_set',$_POST['yonjing'],8);
                $this->success2('设置成功',U('Gta_logistics/index',array('token'=>$this->token)));
            }else{
                $this->error2('投置失败');
            }
        }else{
            $info=M('Gta_logistics_order')->find($_GET['id']);
            $info['imgs']=explode(',',$info['imgs']);
            $info1=M('Wxusers')->field('nickname,headimgurl')->where(array('uid'=>$this->wxuser_id,'openid'=>$info['openid']))->find();
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            $info['info']=json2arr(json_decode($info['info']));
            foreach($info['info'] as $k=>$v){
                $info['info'][$k]['unit']=M('Gta_zeren')->where(array('name'=>$v['name']))->getField('unit');
            }
            $this->assign('info',$info);
            //   p($info);die;
            $this->assign('info1',$info1);
            $this->display($this->tpl_dir.'logistics_info.html');
        }

    }
    //处理订单
    //贷款操作状态的处理
    public function chuli(){
        //echo 4;die;
        if(IS_POST){
            if($_POST['str']==1){//去处理
                if(M('Gta_logistics_order')->where(array('id'=>$_GET['id']))->save(array('status'=>1))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }

            }
            if($_POST['str']==2){//改变为成功还是失败
                if(M('Gta_logistics_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$_GET['kk']))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }
            if($_POST['str']==3){//设置金额
                if(M('Gta_logistics_order')->where(array('id'=>$_GET['id']))->save(array('money'=>$_GET['kk']))){
              //      $this->common('Gta_logistics_order',$_GET['id'],'Gta_logistics_set',$_GET['kk'],8);
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
            if(M('Gta_logistics_order')->where(array('id'=>$_GET['id']))->find()){
                if(M('Gta_logistics_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$status))){
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
    public function logistics_set(){

        $this->Edit('Gta_logistics_set',array(
            array('title'=>"上传图片规格",'type'=>"textarea1",'name'=>"content",'placeholder'=>'多个请用英文逗号隔开,例如:身份证,房产证'),
           /* array('title'=>"金卡赠送总佣金",'type'=>"input",'name'=>"jifen1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"白金赠送总佣金",'type'=>"input",'name'=>"jifen2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"钻石赠送总佣金",'type'=>"input",'name'=>"jifen3",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"1级下属佣金比例",'type'=>"input",'name'=>"bili1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级下属佣金比例",'type'=>"input",'name'=>"bili2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"3级下属佣金比例",'type'=>"input",'name'=>"bili3",'placeholder'=>'请保证1,2,3级佣金比例之和等于1'),*/
            array('title'=>"1级佣金比例",'type'=>"input",'name'=>"one",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级佣金比例",'type'=>"input",'name'=>"two",'placeholder'=>'例如填写:0.3代表赠送30%'),
        ),U('index',array('token'=>$this->token)));
    }
    //责任险产品分类
    public function one()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['type']=3;
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
                    '责任险分类列表!'
                ),
                'Table_Header' => array(
                    'ID', '分类名称', '操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '保险项目',
                        'url' => U('two_product')
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
            array('title'=>"分类名称",'type'=>"hidden_true",'name'=>'type','value'=>'3'),
        ),U('one',array('token'=>$this->token)));
    }

    public function edit_one(){
        $this->Edit('Gta_yiwa2',array(
            array('title'=>"分类名称",'type'=>"input",'name'=>'name','msg'=>'名称不能为空'),
        ),U('one',array('token'=>$this->token)));
    }
    public function del_one(){
        $this->del('Gta_yiwa2');
    }
    //以下是责任险产品
    public function two_product()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['pid'] = $_GET['id'];
        session('pid',$_GET['id']);
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
                        'name' => '添加保险',
                        'url' => U('add_two_product',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '返回分类',
                        'url' => U('one',array('token'=>$this->token))
                    ),
                ),
                'tips' => array(
                    '这里可以添加 【'.M('Gta_yiwa2')->where(array('id'=>$_GET['id']))->getField('name').'】 下面的保险!'
                ),
                'Table_Header' => array(
                    'ID', '名称','单位', '简介', '操作'
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

            M('Gta_zeren')->where($aWhere)->count(),
            M('Gta_zeren')->field('id,name,unit,left(content,20)')->where($aWhere)

        );
        $this->UDisplay('show1');
    }


    //添加保险险
    public function add_two_product(){
        $this->add('Gta_zeren',array(
            array('title'=>"保险名称",'type'=>"input",'name'=>"name",'msg'=>'保险名称不能为空','placeholder'=>'请填写保险名称'),
            array('title'=>"单位",'type'=>"input",'name'=>"unit",'placeholder'=>'比说填:万元'),

            array('title'=>"保险简介",'type'=>"textarea1",'name'=>"content",'msg'=>'保险简介不能为空','placeholder'=>'请别超过100个字'),

        ),U('two_product',array('token'=>$this->token,'id'=>session('pid'))),array($this,'add_two_product1'));
    }
    public function add_two_product1($data){
        $data['pid']=session('pid');
        return $data;
    }
    //修改副险
    public function edit_two_product(){
        $this->Edit('Gta_zeren',array(
            array('title'=>"保险名称",'type'=>"input",'name'=>"name",'msg'=>'保险名称不能为空','placeholder'=>'请填写保险名称'),
            array('title'=>"单位",'type'=>"input",'name'=>"unit",'placeholder'=>'比说填:万元'),

            array('title'=>"保险简介",'type'=>"textarea1",'name'=>"content",'msg'=>'保险简介不能为空','placeholder'=>'请别超过100个字'),

        ),U('two_product',array('token'=>$this->token,'id'=>session('pid'))),array($this,'add_two_product1'));
    }
    //删除副险
    public function del_two_product(){
        $this->del('Gta_zeren');
    }
}