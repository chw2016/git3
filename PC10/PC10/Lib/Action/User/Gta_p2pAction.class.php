<?php
/**
 *  李铭  P2P系统
 * 2015.6.24
 **/
class Gta_p2pAction extends Gta_commonAction
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
        return array(
            array(
                'name' => 'p2p投资产品',
                'url' => U('index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '投资订单',
                'url' => U('touzi_order', array('token' => $this->_sToken))
            ),
        /*    array(
                'name' => '借款订单',
                'url' => U('licai_order', array('token' => $this->_sToken))
            ),*/

            array(
                'name' => 'p2p设置',
                'url' => U('p2p_set', array('token' => $this->_sToken))
            ),
            array(
                'name' => '轮播图',
                'url' => U('imgs', array('token' => $this->_sToken))
            ),


        );
    }


    /**
     *  投资产品列表
     **/
    public function index()
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
                'HeadHover' => U('Gta_p2p/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                         'name' => '添加投资产品',
                         'url' => U('add_product',array('token'=>$this->token))
                     ),



                ),
                'tips' => array(
                    '这里可以添加p2p投资产品!'
                ),
                'Table_Header' => array(
                    'ID', '产品名字', '融资金额(元)', '年化收益率', '期限(月)', '安全保障','推荐', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '修改',
                        'url' => U('edit_product')
                    ),


                ),
            ),

            M('P2p_touzi')->where($aWhere)->count(),
            M('P2p_touzi')->field('id,title,money,rate,time_limit,safe,tui_jian')->where($aWhere)->order('tui_jian desc,add_time desc'),
            array($this,'index1')

        );
        $this->UDisplay('show1');
    }
    public function index1($data){
        foreach($data as $k=>$v){
            if($v['tui_jian']==1){
                $data[$k]['tui_jian']='是';
            }else{
                $data[$k]['tui_jian']='否';
            }
            $data[$k]['rate']=($v['rate']*100).'%';
            $data[$k]['safe']=$v['safe'].'%';
        }
        return $data;
    }

    //添加投资产品
    public function add_product(){
        $this->add('P2p_touzi',array(
            array('type'=>"img",'name'=>"img",'many'=>array(
                array('title'=>'展示图片','name'=>'img')
            )),
            array('title'=>"产品标题",'type'=>"input",'name'=>"title",'msg'=>'产品标题不能为空'),
            array('title'=>"融资金额(元)",'type'=>"input",'name'=>"money",'msg'=>'融资金额不能为空'),
            array('title'=>"年化收益率",'type'=>"input",'name'=>"rate",'msg'=>'年化收益率不能为空','placeholder'=>'例如填:0.15,表示15%'),
            array('title'=>"期限(月)",'type'=>"input",'name'=>"time_limit",'msg'=>'期限不能为空','placeholder'=>'例如填:3,表示期限是3个月'),
            array('title'=>"安全保障(%)",'type'=>"input",'name'=>"safe",'msg'=>'安全保障不能为空','placeholder'=>'例如填:80,表示安全保障80%'),
            array('title'=>"还款方式",'type'=>"radio",'name'=>"huankuan",'msg'=>'还款方式不能为空','many'=>array(
                array('content'=>'先息后本','value'=>1),
                array('content'=>'等额本息','value'=>2),
            )),
            array('title'=>"起拍价格(元)",'type'=>"input",'name'=>"start_price"),
            array('title'=>"是否首页推荐",'type'=>"radio",'name'=>"tui_jian",'many'=>array(
                array('content'=>'否','value'=>0),
                array('content'=>'是','value'=>1),
            )),
            array('title'=>"抵压信息资料",'type'=>"textarea",'name'=>"danbao_info"),
            array('title'=>"借款人信息资料",'type'=>"textarea_1",'name'=>"loan_info"),
        ),U('index',array('token'=>$this->token)));
    }
    //修改投资产品
    public function edit_product(){
        $this->Edit('P2p_touzi',array(
            array('type'=>"img",'name'=>"img",'many'=>array(
                array('title'=>'展示图片','name'=>'img')
            )),
            array('title'=>"产品标题",'type'=>"input",'name'=>"title",'msg'=>'产品标题不能为空'),
            array('title'=>"融资金额(元)",'type'=>"input",'name'=>"money",'msg'=>'融资金额不能为空'),
            array('title'=>"年化收益率",'type'=>"input",'name'=>"rate",'msg'=>'年化收益率不能为空','placeholder'=>'例如填:0.15,表示15%'),
            array('title'=>"期限(月)",'type'=>"input",'name'=>"time_limit",'msg'=>'期限不能为空','placeholder'=>'例如填:3,表示期限是3个月'),
            array('title'=>"安全保障(%)",'type'=>"input",'name'=>"safe",'msg'=>'安全保障不能为空','placeholder'=>'例如填:80,表示安全保障80%'),
            array('title'=>"还款方式",'type'=>"radio",'name'=>"huankuan",'msg'=>'还款方式不能为空','many'=>array(
                array('content'=>'先息后本','value'=>1),
                array('content'=>'等额本息','value'=>2),
            )),
            array('title'=>"起拍价格(元)",'type'=>"input",'name'=>"start_price"),
            array('title'=>"是否首页推荐",'type'=>"radio",'name'=>"tui_jian",'many'=>array(
                array('content'=>'否','value'=>0),
                array('content'=>'是','value'=>1),
            )),
            array('title'=>"抵压信息资料",'type'=>"textarea",'name'=>"danbao_info"),
            array('title'=>"借款人信息资料",'type'=>"textarea_1",'name'=>"loan_info"),
        ),U('index',array('token'=>$this->token)));
    }
    /**
     *  投资订单列表
     **/
    public function touzi_order()
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
                'HeadHover' => U('touzi_order', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                   /* array(
                        'name' => '添加投资产品',
                        'url' => U('add_product',array('token'=>$this->token))
                    ),*/
                    array(
                        'name' => '导出excel表',
                        'type'=>'daochu',
                        'url' => U('excel_order',array('token'=>$this->token))
                    ),


                ),
                'tips' => array(
                    'p2p投资订单列表!'
                ),
                'Table_Header' => array(
                    'ID', '产品名字', '投资人姓名', '投资金额(元)','年收益率','投资期限','收益金额(元)', '状态', '下单时间', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '详情',
                        'url' => U('p2p_info')
                    ),


                ),
                'search' => array(
                    array('title'=>'真实名字','name'=>'li_name'),
                    array('title'=>'订单状态','name'=>'eq_status','type'=>'select','many'=>array(

                        array('value'=>'2','name'=>'投资中'),
                        array('value'=>'3','name'=>'投资完期'),

                    )),
                    array('type'=>'br'),
                    array('title'=>'下单时间','name'=>'be_add_time','type'=>'between')
                )
            ),

            M('Touzi_order')->where($aWhere)->count(),
            M('Touzi_order')->field('id,title,name,money,rate,time_limit,uid,status,add_time')->where($aWhere)->order('add_time desc'),
            array($this,'touzi_order1')

        );
        $this->assign('ring',1);
        $lasttime =M('Touzi_order')->where(array('token'=>$this->token))->order('add_time desc')->limit(1)->getField('add_time');
        $this->assign('ring_ring','/upload/ring/a.mp3');
        $this->assign('ring_model','Touzi_order');
        $this->assign('lasttime',$lasttime);
        $this->UDisplay('show1');
    }
    public function touzi_order1($data){
        foreach($data as $k=>$v){
            if($v['status']==0){
                $data[$k]['status']='新申请';
            }
            if($v['status']==2){
                $data[$k]['status']='投资中';
            }
            if($v['status']==-2){
                $data[$k]['status']='交易失败';
            }
            if($v['status']==1){
                $data[$k]['status']='处理中';
            }
            if($v['status']==3){
                $data[$k]['status']='投资完期';
            }
            if($v['status']==4){
                $data[$k]['status']='已清算';
            }
            $data[$k]['rate']=($v['rate']*100).'%';
            $data[$k]['uid']=round($v['money']*$v['rate']/365*min((time()-$v['add_time'])/3600/24,30*$v['time_limit']),2);
            $data[$k]['time_limit']=$v['time_limit'].'个月';
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }

    /*订单导出excel_order*/
    public function excel_order(){
        $aWhere=$this->search($_POST);
        $aWhere['token'] = $this->token;

        $list = M('Touzi_order')
            ->field('id,orderid,title,money,name,add_time,status')
            ->order('add_time desc')
            ->where($aWhere)
            ->select();
        foreach($list as $k=>$v){
            if($v['status']==0){
                $data[$k]['status']='新申请';
            }
            if($v['status']==2){
                $data[$k]['status']='投资中';
            }
            if($v['status']==-2){
                $data[$k]['status']='交易失败';
            }
            if($v['status']==1){
                $data[$k]['status']='处理中';
            }
            if($v['status']==3){
                $data[$k]['status']='投资完期';
            }
            if($v['status']==4){
                $data[$k]['status']='已清算';
            }
            $list[$k]['type'] = 'P2P投资';
            $uid = M('Touzi_order')->where(array('id'=>$v['id']))->getField('uid');
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
        Excel::arr2ExcelDownload($list,array('数据ID','订单编号','产品名称','产品金额','客户姓名','成交时间','订单状态','产品类型','成单人','一级关系人','上二级关系人'),'P2P投资订单');

    }




    //投资订单详情
    public function p2p_info(){
        if(IS_POST){
            if(M('Touzi_order')->where(array('id'=>$_GET['id']))->save(array('yonjing'=>$_POST['yonjing']))){
                //算一二级佣金
                $this->common2('Touzi_order',$_GET['id'],'Gta_p2p_set',$_POST['yonjing'],4);
                $this->success2('设置成功');
            }else{
                $this->error2('投置失败');
            }
        }else{
            $info1=(array)M('Touzi_order')->field('js_type,time_limit,rate,money,old_money,orderid,openid,name,phone,status,add_time,title')->find($_GET['id']);
            $info=(array)M('Wxusers')->field('nickname,headimgurl')->where(array('uid'=>$this->wxuser_id,'openid'=>$info1['openid']))->find();
            $info=array_merge($info,$info1);
            unset($info['openid']);
            $info['imgs']=explode(',',$info['imgs']);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            $syjl=M('Gta_syjl')->where(array('type'=>1,'orderid'=>$_GET['id']))->order('id')->select();
           // p($syjl);
            $this->assign('syjl',$syjl);
            $this->assign('info',$info);
            $this->display($this->tpl_dir.'p2p_info.html');
        }

    }
    //投资订单处理
    public function chuli(){
        //echo 4;die;
        if(IS_POST){
            if($_POST['str']==1){//去处理
                if(M('Touzi_order')->where(array('id'=>$_GET['id']))->save(array('status'=>1))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }

            }
            if($_POST['str']==2){//改变为成功还是失败
                if(M('Touzi_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$_GET['kk']))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }
            if($_POST['str']==3){//设置金额
                if(M('Touzi_order')->where(array('id'=>$_GET['id']))->save(array('money'=>$_GET['kk']))){
                 //   $this->common('Touzi_order',$_GET['id'],'Gta_p2p_set',$_GET['kk'],4);
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }

        }
    }
    //轮播图
    public function imgs()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['type'] =1;
        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('imgs', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加图片',
                        'url' => U('add_img',array('token'=>$this->token))
                    ),



                ),
                'tips' => array(
                    '轮播图列表!'
                ),
                'Table_Header' => array(
                    'ID', '图片','跳转地址', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '修改',
                        'url' => U('edit_img')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_img')
                    ),


                ),
            ),

            M('Gta_imgs')->where($aWhere)->count(),
            M('Gta_imgs')->field('id,img,url')->where($aWhere),
            array($this,'imsg1')

        );
        $this->UDisplay('show1');
    }
    public function imsg1($data){
        foreach($data as $k=>$v){
            $data[$k]['img']="<img src='".$v['img']."' width=100px  />";
        }
        return $data;
    }
    public function add_img(){
        $this->add('Gta_imgs',array(
            array('type'=>"img",'many'=>array(
                array('title'=>'图片','name'=>'img')
            )),
            array('type'=>'input','title'=>'链接','name'=>'url','placeholder'=>'您如果填写了地址,才会跳转地址')


        ),U('imgs',array('token'=>$this->token)),array($this,'add_img1'));
    }
    public function edit_img(){
        $this->Edit('Gta_imgs',array(
            array('type'=>"img",'many'=>array(
                array('title'=>'图片','name'=>'img'),

            )),
                array('type'=>'input','title'=>'链接','name'=>'url','placeholder'=>'您如果填写了地址,才会跳转地址')


        ),U('imgs',array('token'=>$this->token)),array($this,'add_img1'));
    }
    public function del_img(){
        $this->del('Gta_imgs');
    }
    public function add_img1($data){
        $data['type']=1;
        return $data;
    }
    //p2p设置
    public function p2p_set(){
        $this->Edit('Gta_p2p_set',array(
            /*array('title'=>"金卡赠送总佣金",'type'=>"input",'name'=>"jifen1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"白金赠送总佣金",'type'=>"input",'name'=>"jifen2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"钻石赠送总佣金",'type'=>"input",'name'=>"jifen3",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"1级下属佣金比例",'type'=>"input",'name'=>"bili1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级下属佣金比例",'type'=>"input",'name'=>"bili2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"3级下属佣金比例",'type'=>"input",'name'=>"bili3",'placeholder'=>'请保证1,2,3级佣金比例之和等于1'),*/
            array('title'=>"1级佣金比例",'type'=>"input",'name'=>"one",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级佣金比例",'type'=>"input",'name'=>"two",'placeholder'=>'例如填写:0.3代表赠送30%'),
        ),U('touzi_order',array('token'=>$this->token)));
    }
    //已到期的项目结算
    public function jiesu(){
        if(IS_POST){
            if(M($_POST['model'])->where(array('id'=>$_GET['id']))->save(array('status'=>4))){
                echo json_encode(array('status'=>1));die;
            }else{
                echo json_encode(array('status'=>0));die;
            }
        }
    }
}