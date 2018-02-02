<?php
/**
 *  李铭  贷款，理财系统
 **/
class Gta_touziAction extends Gta_commonAction{

    public $_sTplBaseDir = 'User/default/miye';
    public $token;
    public $wxuser_id;
    public $tpl_dir = './tpl/User/default/gta/';

    public function _initialize()
    {
    	parent::_initialize();
        //这里是应用权限判断
        if(session('?app_id')){
            $app_id=explode(',',session('app_id'));
            $myapp=M('App_list')->field('enter_api')->where(array('id'=>array('in',$app_id)))->select();
            $app_action=array();
            foreach($myapp as $k=>$v){
                $a1=explode('/',$v['enter_api']);
                array_push($app_action,$a1[1].'Action');
            }
            if(!in_array(__CLASS__,$app_action)){
                $this->error2('您没有此应用的权限');
            }
        }

        $this->tpl="tpl/User/default/helper/";
        //$this->pz=M("No_credit");
        $this->token = session('token');
        $this->wxuser_id=M('Wxuser')->where(array('token'=>$this->token))->getField('id');
      
    }
    
    protected function setHeader(){
        if($_SESSION['gta_cw'] !=''){
            return array(
                array(
                    'name' => '贷款订单',
                    'url'  => U('index', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '理财订单',
                    'url'  => U('licai_order', array('token' => $this->_sToken))
                ),
            );
        }else{
            return array(
                  array(
                        'name' => '贷款订单',
                        'url'  => U('index', array('token' => $this->_sToken))
                    ),
                array(
                    'name' => '理财产品',
                    'url'  => U('licai', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '理财订单',
                    'url'  => U('licai_order', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '理财设置',
                    'url'  => U('licai_set', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '轮播图',
                    'url' => U('imgs', array('token' => $this->_sToken))
                ),



                );
        }
    }





    /**
     *  贷款订单
     **/
	public function index(){
 
            $aWhere['token'] =$this->_sToken;
        if($_SESSION['gta_cw'] == '核算权限'){
            $aWhere['status'] = 2;
        }elseif($_SESSION['gta_cw'] == '客服权限'){
            $aWhere['status'] = array('in','0,1');
        }elseif($_SESSION['gta_cw'] == '出纳权限'){
            $aWhere['status'] = 3;
        }elseif($_SESSION['gta_cw'] == '会计权限'){
            $aWhere['status'] = array('in','4,5');
        }
            if(IS_POST){
                $_POST=$_REQUEST;
                $aWhere=$this->search($_POST);
                $aWhere['token'] =$this->_sToken;
            }
        if($_SESSION['gta_cw'] != ''){
            $this->table(
                array(
                    //'abc' => 123,
                    //  'id' => 'name',//如果主键不是id，则需要设置
                    'HeadHover' => U('index', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        array(
                            'name' => '导出excel表',
                            'type'=>'daochu',
                            'url' => U('excel_orders',array('token'=>$this->token))
                        ),
                    ),
                    'tips' => array(
                        '贷款订单列表'
                    ),
                    'Table_Header' => array(
                        'ID', '贷款人姓名','贷款金额','贷款期限(天)', '状态','申请时间', '操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '查看',
                            'url' => U('index_info')
                        ),
                    ),
                    'search'=>array(
                        array('title'=>'真实名字','name'=>'li_name'),
                        array('type'=>'br'),
                        array('title'=>'下单时间','name'=>'be_add_time','type'=>'between')
                    )
                ),
                M('Gta_loan')->where($aWhere)->order('add_time desc')->count(),
                M('Gta_loan')->field('id,name,c_money,qi_xian,status,add_time')->order('add_time desc')->where($aWhere),
                array($this,'index1')
            );
        }else{
            $this->table(
                array(
                    //'abc' => 123,
                  //  'id' => 'name',//如果主键不是id，则需要设置
                    'HeadHover' => U('index', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        array(
                            'name' => '贷款设置',
                            'url' => U('loan_set',array('token'=>$this->token))
                        ),
                        array(
                            'name' => '贷款类型上传资料设置',
                            'url' => U('loan_set1',array('token'=>$this->token))
                        ),
                        array(
                            'name' => '导出excel表',
                            'type'=>'daochu',
                            'url' => U('excel_orders',array('token'=>$this->token))
                        ),
                    ),
                    'tips' => array(
                        '贷款订单列表'
                    ),
                    'Table_Header' => array(
                        'ID', '贷款人姓名','贷款金额','贷款期限(天)', '状态','申请时间', '操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '查看',
                            'url' => U('index_info')
                        ),

                      
                    ),
                 'search'=>array(
                        array('title'=>'真实名字','name'=>'li_name'),
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
                M('Gta_loan')->where($aWhere)->order('add_time desc')->count(),
                M('Gta_loan')->field('id,name,c_money,qi_xian,status,add_time')->order('add_time desc')->where($aWhere),
                array($this,'index1')
            );
        }
        $this->assign('ring',1);
        $lasttime =M('Gta_loan')->where(array('token'=>$this->token))->order('add_time desc')->limit(1)->getField('add_time');
        $this->assign('ring_ring','/upload/ring/daikuan.mp3');
        $this->assign('ring_model','Gta_loan');
        $this->assign('lasttime',$lasttime);
        $this->UDisplay('show1');
	}
    public function index1($data){
        foreach($data as $k=>$v){
            $data[$k]['add_time']=date('Y-m-d h:i:s',$v['add_time']);
            switch($v['status']){
                case 0: $data[$k]['status']='新订单';break;
                case 1: $data[$k]['status']='处理中';break;
                case 2: $data[$k]['status']='待录佣';break;
                case 3: $data[$k]['status']='待处理';break;
                case 4: $data[$k]['status']='待核审';break;
                case 5: $data[$k]['status']='交易成功';break;
                case -2: $data[$k]['status']='交易失败';break;
            }
        }
        return $data;
    }

/*订单导出excel_order*/
public function excel_orders(){
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

    $list = M('Gta_loan')
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
        $list[$k]['type'] = '贷款';
        $uid = M('Gta_loan')->where(array('id'=>$v['id']))->getField('uid');
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
    Excel::arr2ExcelDownload($list,array('数据ID','订单编号','产品名称','产品金额','客户姓名','成交时间','订单状态','产品类型','成单人','一级关系人','上二级关系人'),'贷款订单');

}





    //贷款设置
    public function loan_set(){
       $this->Edit('Gta_loan_set',array(
          /* array('title'=>"金卡赠送总佣金",'type'=>"input",'name'=>"jifen1",'placeholder'=>'例如填写:0.3代表赠送30%'),
           array('title'=>"白金赠送总佣金",'type'=>"input",'name'=>"jifen2",'placeholder'=>'例如填写:0.3代表赠送30%'),
           array('title'=>"钻石赠送总佣金",'type'=>"input",'name'=>"jifen3",'placeholder'=>'例如填写:0.3代表赠送30%'),
           array('title'=>"1级下属佣金比例",'type'=>"input",'name'=>"bili1",'placeholder'=>'例如填写:0.3代表赠送30%'),
           array('title'=>"2级下属佣金比例",'type'=>"input",'name'=>"bili2",'placeholder'=>'例如填写:0.3代表赠送30%'),
           array('title'=>"3级下属佣金比例",'type'=>"input",'name'=>"bili3",'placeholder'=>'请保证1,2,3级佣金比例之和等于1'),*/
           array('title'=>"1级佣金比例",'type'=>"input",'name'=>"one",'placeholder'=>'例如填写:0.3代表赠送30%'),
           array('title'=>"2级佣金比例",'type'=>"input",'name'=>"two",'placeholder'=>'例如填写:0.3代表赠送30%'),
           array('title'=>"贷款图文详情介绍",'type'=>"textarea",'name'=>"info"),
           array('title'=>"贷款类型设置",'type'=>"textarea1",'name'=>"type",'placeholder'=>'多个类型请以英文逗号隔开,例如:生意贷,保单贷'),
        ),U('index',array('token'=>$this->token)));
    }
    //贷款类型上传资料设置
    //贷款设置
    public function loan_set1(){
        $info=M('Gta_loan_set')->where(array('token'=>$this->token))->getField('type');
        $info=explode(',',$info);
        if(IS_POST){
            for($i=0;$i<count($info);$i++){
                if(M('Gta_loantype_set')->where(array('name'=>$info[$i],'token'=>$this->token))->find()){
                //    echo $_POST['content'.$i]."<BR />";
                    M('Gta_loantype_set')->where(array('name'=>$info[$i],'token'=>$this->token))->save(array('content'=>$_POST['content'.$i]));
                }else{
                    M('Gta_loantype_set')->add(array('content'=>$_GET['content'.$i],'name'=>$info[$i],'token'=>$this->token));
                }
            }
            $this->success2('设置成功',U('index'));

        }else{
            foreach($info as $k=>$v){
                if($arr=M('Gta_loantype_set')->field('name,content')->where(array('token'=>$this->token,'name'=>$v))->find()){
                    $info[$k]= $arr;
                }else{
                    $info[$k]=array('name'=>$v);
                }
            }
            $this->assign('info',$info);
            $this->display('tpl/User/default/gta/loan_set1.html');
        }


    }
    //理财设置
    public function licai_set(){
        $this->Edit('Gta_licai_set',array(
            /*array('title'=>"金卡赠送总佣金",'type'=>"input",'name'=>"jifen1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"白金赠送总佣金",'type'=>"input",'name'=>"jifen2",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"钻石赠送总佣金",'type'=>"input",'name'=>"jifen3",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"1级下属佣金比例",'type'=>"input",'name'=>"bili1",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级下属佣金比例",'type'=>"input",'name'=>"bili2",'placeholder'=>'例如填写:0.3代表赠送30%'),*/
            array('title'=>"1级佣金比例",'type'=>"input",'name'=>"one",'placeholder'=>'例如填写:0.3代表赠送30%'),
            array('title'=>"2级佣金比例",'type'=>"input",'name'=>"two",'placeholder'=>'例如填写:0.3代表赠送30%'),
            /*array('title'=>"3级下属佣金比例",'type'=>"input",'name'=>"bili3",'placeholder'=>'请保证1,2,3级佣金比例之和等于1'),*/
        ),U('licai_order',array('token'=>$this->token)));
    }
    //查看贷款订单详情
    public function index_info(){
        if(IS_POST){
            if(M('Gta_loan')->where(array('id'=>$_GET['id']))->save(array('yonjing'=>$_POST['yonjing'],'status'=>3))){
                //算一二级佣金
                $this->common2('Gta_loan',$_GET['id'],'Gta_loan_set',$_POST['yonjing'],1);
                $this->success2('设置成功',U('Gta_life/gta_life_order',array('token'=>$this->token)));
            }else{
                $this->error2('投置失败');
            }
        }else{
            $info1=(array)M('Gta_loan')->field('money,type,orderid,openid,name,phone,c_money,qi_xian,status,add_time,imgs')->find($_GET['id']);
            $info=(array)M('Wxusers')->field('nickname,headimgurl')->where(array('uid'=>$this->wxuser_id,'openid'=>$info1['openid']))->find();

            $info=array_merge($info,$info1);
            unset($info['openid']);
            $info['imgs']=explode(',',$info['imgs']);
            //  p($info);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            $this->assign('info',$info);
            $this->display($this->tpl_dir.'info.html');
        }

    }
    //贷款操作状态的处理
    public function chuli(){
        //echo 4;die;
        if(IS_POST){
            if($_POST['str']==1){//去处理
                if(M('Gta_loan')->where(array('id'=>$_GET['id']))->save(array('status'=>1))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }

            }
            if($_POST['str']==2){//改变为成功还是失败
                if(M('Gta_loan')->where(array('id'=>$_GET['id']))->save(array('status'=>$_GET['kk']))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }
            if($_POST['str']==3){//设置金额
                if(M('Gta_loan')->where(array('id'=>$_GET['id']))->save(array('money'=>$_GET['kk']))){
                 //   $this->common('Gta_loan',$_GET['id'],'Gta_loan_set',$_GET['kk'],1);
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
            if(M('Gta_loan')->where(array('id'=>$_GET['id']))->find()){
                if(M('Gta_loan')->where(array('id'=>$_GET['id']))->save(array('status'=>$status))){
                    echo json_encode(array('status'=>1,'info'=>'操作成功'));die;
                }else{
                    echo json_encode(array('status'=>0,'info'=>'操作失败'));die;
                }
            }else{
                $this->error('非法操作');
            }
        }
    }
    //理财订单处理

    public function licai_chuli(){
        if(IS_POST){
            if($_POST['str']==1){//去处理
                if(M('Licai_order')->where(array('id'=>$_GET['id']))->save(array('status'=>1))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }

            }
            if($_POST['str']==2){//改变为成功还是失败
                if(M('Licai_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$_GET['kk']))){
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }
            if($_POST['str']==3){//设置金额
                if(M('Licai_order')->where(array('id'=>$_GET['id']))->save(array('money'=>$_GET['kk']))){
                 //   $this->common('Licai_order',$_GET['id'],'Gta_licai_set',$_GET['kk'],2);
                    echo json_encode(array('status'=>1));die;
                }else{
                    echo json_encode(array('status'=>0));die;
                }
            }

        }
    }


    public function chuli3(){
        if(IS_AJAX){
            $status = $_POST['status'];
            if(M('Licai_order')->where(array('id'=>$_GET['id']))->find()){
                if(M('Licai_order')->where(array('id'=>$_GET['id']))->save(array('status'=>$status))){
                    echo json_encode(array('status'=>1,'info'=>'操作成功'));die;
                }else{
                    echo json_encode(array('status'=>0,'info'=>'操作失败'));die;
                }
            }else{
                $this->error('非法操作');
            }
        }
    }

    //理财产品列表
    public function licai(){
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            $_POST=$_REQUEST;
            $aWhere=$this->search($_POST);
            $aWhere['token'] =$this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('licai', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                         'name' => '添加理财产品',
                         'url' => U('add_licai',array('token'=>$this->token))
                     ),


                ),
                'tips' => array(
                    '您可以在这里添加理财产品'
                ),
                'Table_Header' => array(
                    'ID', '产品名称','安全性','投资额度(元)', '期限(月)','回报率','首页推荐', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '修改',
                        'url' => U('edit_licai')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_licai')
                    ),


                ),
                'search'=>array(
                    array('title'=>'产品名字','name'=>'li_title'),
                )
            ),

            M('Licai')->where($aWhere)->count(),
            M('Licai')->field('id,title,safe,money,time_limit,rate,tui_jian')->where($aWhere)->order('tui_jian desc,add_time desc'),
            array($this,'licai1')

        );
        $this->UDisplay('show1');
    }
    public function licai1($data){
        foreach($data as $k=>$v){
            if($v['tui_jian']==0){
                $data[$k]['tui_jian']='否';
            }else{
                $data[$k]['tui_jian']='是';
            }
        }
        return $data;
    }
    //添加理财产品

    public function add_licai(){
        $this->add('Licai',array(
            array('title'=>"产品标题",'type'=>"input",'name'=>"title",'msg'=>'产品标题不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品展示图片1",'name'=>"pic",'msg'=>'必须上传一张产品图片'),
            )),
            array('title'=>"产品安全性",'type'=>"input",'name'=>"safe",'placeholder'=>'例如填:80%'),
            array('title'=>"投资额度(单位元)",'type'=>"input",'name'=>"money"),
            array('title'=>"期限(月)",'type'=>"input",'name'=>"time_limit",'placeholder'=>'例如填:2'),
           array('title'=>"回报率",'type'=>"input",'name'=>"rate",'placeholder'=>'例如填:0.15'),
            array('title'=>"最低投资额",'type'=>"input",'name'=>"start_price"),
            array('title'=>"是否在首页推荐",'type'=>"radio",'name'=>"tui_jian",'many'=>array(
                array('content'=>'否','value'=>0),
                array('content'=>'是','value'=>1),
            )),

            array('title'=>"产品详情介绍",'type'=>"textarea",'name'=>"info"),

        ),U('licai',array('token'=>$this->token)));
    }
    //修改理财产品

    public function edit_licai(){
        $this->Edit('Licai',array(
            array('title'=>"产品标题",'type'=>"input",'name'=>"title",'msg'=>'产品标题不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品展示图片1",'name'=>"pic",'msg'=>'必须上传一张产品图片'),
            )),
            array('title'=>"产品安全性",'type'=>"input",'name'=>"safe",'placeholder'=>'例如填:80%'),
            array('title'=>"投资额度(单位元)",'type'=>"input",'name'=>"money"),
            array('title'=>"期限(月)",'type'=>"input",'name'=>"time_limit",'placeholder'=>'例如填:5'),
            array('title'=>"回报率",'type'=>"input",'name'=>"rate",'placeholder'=>'例如填:0.15'),
            array('title'=>"最低投资额",'type'=>"input",'name'=>"start_price"),
            array('title'=>"是否在首页推荐",'type'=>"radio",'name'=>"tui_jian",'many'=>array(
                array('content'=>'否','value'=>0),
                array('content'=>'是','value'=>1),
            )),

            array('title'=>"产品详情介绍",'type'=>"textarea",'name'=>"info"),

        ),U('licai',array('token'=>$this->token)));
    }
    //删除理财产品
    public function del_licai(){
        $this->del('Licai');
    }
    //理财订单

    public function licai_order(){
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            $_POST=$_REQUEST;
            $aWhere=$this->search($_POST);
            $aWhere['token'] =$this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('licai_order', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                   /* array(
                        'name' => '添加理财产品',
                        'url' => U('add_licai',array('token'=>$this->token))
                    ),*/
                    array(
                        'name' => '导出excel表',
                        'type'=>'daochu',
                        'url' => U('excel_order',array('token'=>$this->token))
                    ),
                ),
                'tips' => array(
                    '理财订单列表'
                ),
                'Table_Header' => array(
                    'ID', '产品名称','客户姓名','投资金额','投资期限','年收益率','收益金额','状态', '下单时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '详情',
                        'url' => U('licai_order_info')
                    ),


                ),
                'search'=>array(
                    array('title'=>'产品名字','name'=>'li_title'),
                    array('title'=>'申请人姓名','name'=>'li_name'),
                    array('title'=>'订单状态','name'=>'eq_status','type'=>'select','many'=>array(

                        array('value'=>'2','name'=>'交易成功'),

                        array('value'=>'3','name'=>'投资完期'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'下单时间','name'=>'be_add_time','type'=>'between')
                )
            ),

            M('Licai_order')->where($aWhere)->count(),
            M('Licai_order')->field('id,title,name,money,time_limit,rate,uid,status,add_time')->where($aWhere)->order('add_time desc'),
            array($this,'licai_order1')

        );
        $this->assign('ring',1);
        $lasttime =M('Licai_order')->where(array('token'=>$this->token))->order('add_time desc')->limit(1)->getField('add_time');
        $this->assign('ring_ring','/upload/ring/licai.mp3');
        $this->assign('ring_model','Licai_order');
        $this->assign('lasttime',$lasttime);
        $this->UDisplay('show1');
    }
    public function licai_order1($data){
        foreach($data as $k=>$v){
            if($v['status']==0){
                $data[$k]['status']='新申请';
            }
            if($v['status']==2){
                $data[$k]['status']='交易成功';
            }
            if($v['status']==-2){
                $data[$k]['status']='交易失败';
            }
            if($v['status']==3){
                $data[$k]['status']='投资完期';
            }
            if($v['status']==1){
                $data[$k]['status']='处理中';
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

        $list = M('Licai_order')
            ->field('id,orderid,title,money,name,add_time,status')
            ->order('add_time desc')
            ->where($aWhere)
            ->select();
        foreach($list as $k=>$v){
            if($v['status']==0){
                $data[$k]['status']='新申请';
            }
            if($v['status']==2){
                $data[$k]['status']='交易成功';
            }
            if($v['status']==-2){
                $data[$k]['status']='交易失败';
            }
            if($v['status']==3){
                $data[$k]['status']='投资完期';
            }
            if($v['status']==1){
                $data[$k]['status']='处理中';
            }
            $list[$k]['type'] = '投资理财';
            $uid = M('Licai_order')->where(array('id'=>$v['id']))->getField('uid');
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
        Excel::arr2ExcelDownload($list,array('数据ID','订单编号','产品名称','产品金额','客户姓名','成交时间','订单状态','产品类型','成单人','一级关系人','上二级关系人'),'投资理财订单');

    }



    //理财订单详情
    public function licai_order_info(){
        if(IS_POST){
            if(M('Licai_order')->where(array('id'=>$_GET['id']))->save(array('yonjing'=>$_POST['yonjing']))){
                //算一二级佣金
                $this->common2('Licai_order',$_GET['id'],'Gta_licai_set',$_POST['yonjing'],2);
                $this->success2('设置成功');
            }else{
                $this->error2('投置失败');
            }
        }else{
            $info=M('Licai_order')->find($_GET['id']);
            $info1=M('Wxusers')->field('nickname,headimgurl')->where(array('uid'=>$this->wxuser_id,'openid'=>$info['openid']))->find();
            $info_user=M('Gta_users')->field('name,phone')->where(array('id'=>$info['uid']))->find();
            $this->assign('info_user',$info_user);
            $this->assign('info',$info);
            $this->assign('info1',$info1);
            $this->display($this->tpl_dir.'licai_order_info.html');
        }

    }
    //轮播图
    public function imgs()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['type'] =2;
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
                array('title'=>'图片','name'=>'img')
            )),
            array('type'=>'input','title'=>'链接','name'=>'url','placeholder'=>'您如果填写了地址,才会跳转地址')


        ),U('imgs',array('token'=>$this->token)),array($this,'add_img1'));
    }
    public function del_img(){
        $this->del('Gta_imgs');
    }
    public function add_img1($data){
        $data['type']=2;
        return $data;
    }



}
?>
