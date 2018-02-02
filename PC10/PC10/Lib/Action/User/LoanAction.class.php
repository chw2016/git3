<?php
/**
 *  万普分期系统
 **/
class LoanAction extends Table1Action {

    public $_sTplBaseDir = 'User/default/miye';

    public function _initialize()
    {
    	parent::_initialize();
        $this->pz	   = D('No_credit');
        $this->pz1   = D('Credit');
        $this->order  =M('No_credit_order');//订单表


        $this->tpl="tpl/User/default/helper/";

    }

    protected function setHeader(){
    	return array(
              array(
                    'name' => '非信贷产品',
                    'url'  => U('Loan/index', array('token' => $this->_sToken))
                ),
            array(
                    'name' => '信贷产品',
                    'url'  => U('Loan/credit_index', array('token' => $this->_sToken))
                ),
            array(
                'name' => '非信贷产品订单',
                'url' =>  U('Loan/order_index', array('token' => $this->_sToken)),
            ),
            array(
                'name' => '信贷产品订单',
                'url' => U('Loan/credit_order',array('token' => $this->_sToken))
            ),
            array(
                'name' => '会员管理',
                'url'  => U('Loan/users_index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '设置利率',
                'url'  => U('Loan/lilu_index', array('token' => $this->_sToken))
            ),
            array(
                'name' => 'PC端',
                'url'  => U('Loan/pc_index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '更多',
                'url'  => U('Loan/more', array('token' => $this->_sToken))
            ),

            );
    }

    /**
     *  PC端
     **/
    public function pc_index(){
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
                'HeadHover' => U('Loan/pc_index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加关于我们图片',
                        'url' => U('Loan/add_pc_pic',array('token'=>$this->token))
                    ),
                ),
                'tips' => array(
                    '您说要注意什么呢！开心每一天'
                ),
                'Table_Header' => array(
                    'ID', '图片', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('Loan/edit_pc_pic')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('Loan/del_pc_pic')
                    ),
                ),
                /* 'search'=>array(
                       array('title'=>'姓名1','name'=>'li_name'),
                       array('title'=>'姓名2','name'=>'eq_name2'),
                       array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                   )*/
            ),

            M('Loan_pics')->where($aWhere)->count(),
            M('Loan_pics')->field('id,pic')->where($aWhere),
            array($this,'pc_a')


        );
        $this->UDisplay('show1');
    }
    /**
     *  更多
     **/
    public function more(){
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
                'HeadHover' => U('Loan/more', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加文章',
                        'url' => U('Loan/add_more',array('token'=>$this->token))
                    ),
                ),
                'tips' => array(
                    '您说要注意什么呢！开心每一天'
                ),
                'Table_Header' => array(
                    'ID', '标题','小图标', '时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('Loan/edit_more')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('Loan/del_more')
                    ),
                ),
                /* 'search'=>array(
                       array('title'=>'姓名1','name'=>'li_name'),
                       array('title'=>'姓名2','name'=>'eq_name2'),
                       array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                   )*/
            ),

            M('Product_new_article')->where($aWhere)->order('id desc')->count(),
            M('Product_new_article')->field('id,title,pic,createtime')->where($aWhere)->order('id desc'),
            array($this,'more_a')


        );
        $this->UDisplay('show1');
    }
    public function more_a($data){
        foreach($data as $k=>$v){
            $data[$k]['createtime']=date('Y-m-d,H:i:s',$v['createtime']);
            $data[$k]['pic']="<img src='".$v['pic']."' />";
        }
        return $data;
    }

    public function pc_a($data){
        foreach($data as $k=>$v){
           $data[$k]['pic']="<img src='".$v['pic']."' width='100px;' />";
        }
        return $data;
    }
    //删除关于我们图片
    public function del_pc_pic(){
        $this->del('Loan_pics');
    }
    //编辑关于我们图片
    public function edit_pc_pic(){
        $this->Edit('Loan_pics',array(
            array('type'=>'img','many'=>array(
                array('title'=>"图片",'name'=>"pic",'msg'=>'必须上传图片'),


            ))
        ),U('Loan/pc_index',array('token',$this->token)));
    }
    /**
     * 添加更多里面的文章
     */
    public function add_more(){
        $this->add('Product_new_article',array(
            array('title'=>'标题','type'=>'input','name'=>'title','msg'=>'请填写标题'),
            array('title'=>'小图标','type'=>'img','many'=>array(

                array('title'=>"小图标",'name'=>"pic",'msg'=>'必须上传小图标'),
            )),
            array('title'=>'图文详情','type'=>'textarea','name'=>'info','msg'=>'请填写文章内容'),

        ),U('Loan/more',array('token',$this->token)),array($this,'more1'));
    }

    /**
     * 编辑更多里面的文章
     */
    public function del_more(){
        $this->del('Product_new_article');
    }
    public function edit_more(){
        $this->Edit('Product_new_article',array(
            array('title'=>'标题','type'=>'input','name'=>'title','msg'=>'请填写标题'),
            array('title'=>'小图标','type'=>'img','many'=>array(

                array('title'=>"小图标",'name'=>"pic",'msg'=>'必须上传小图标'),
            )),
            array('title'=>'图文详情','type'=>'textarea','name'=>'info','msg'=>'请填写文章内容'),

        ),U('Loan/more',array('token',$this->token)),array($this,'more1'));
    }
    public function more1($data){
        $data['createtime']=time();
        return $data;
    }
    /**
     * 添加关于我们图片
     */
    public function add_pc_pic(){
        $this->add('Loan_pics',array(
            array('type'=>'img','many'=>array(
                array('title'=>"图片",'name'=>"pic",'msg'=>'必须上传图片'),


            ))
        ),U('Loan/pc_index',array('token',$this->token)));
    }
    /**
     *  非信贷产品
     **/
	public function index(){
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
                    'HeadHover' => U('Loan/index', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        array(
                            'name' => '添加非信贷产品',
                            'url' => U('Loan/add_pinzhong',array('token'=>$this->token))
                        ),
                        array(
                            'name' => '分类',
                            'url' => U('Loan/fl_index',array('token'=>$this->token))
                        ),

                    ),
                    'tips' => array(
                        '您说要注意什么呢！开心每一天'
                    ),
                    'Table_Header' => array(
                        'ID', '标题','所属分类','爆款', '行程天数','首付','分几期','每期还款金额','贷款总金额', '操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '编辑',
                            'url' => U('Loan/PinzhongEdit')
                        ),
                        array(
                            'name' => '设置时间',
                            'url' => U('Loan/set_time',array('token'=>$this->token))
                        ),
                        array(
                            'name' => '删除',
                            'url' => U('Loan/PinzhongDel')
                        ),
                    ),
                 /* 'search'=>array(
                        array('title'=>'姓名1','name'=>'li_name'),
                        array('title'=>'姓名2','name'=>'eq_name2'),
                        array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                    )*/
                ),

                $this->pz->where($aWhere)->count(),
                $this->pz->field('id,title,fid,baoku_pic,day_num,shoufu,fenqi,monthly_repayments,loan_total_money')->where($aWhere),
                array($this,'abc1')

            );
        $this->UDisplay('show1');
	}

    /**
     *  分类列表
     **/
    public function fl_index(){
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
                'HeadHover' => U('Loan/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加分类',
                        'url' => U('Loan/add_fl',array('token'=>$this->token))
                    )
                ),
                'tips' => array(
                    '您说要注意什么呢！开心每一天'
                ),
                'Table_Header' => array(
                    'ID', '分类名称','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('Loan/fl_edit')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('Loan/fl_del')
                    ),
                ),
                /* 'search'=>array(
                       array('title'=>'姓名1','name'=>'li_name'),
                       array('title'=>'姓名2','name'=>'eq_name2'),
                       array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                   )*/
            ),

            M('Credit_fl')->where($aWhere)->count(),
            M('Credit_fl')->field('id,title')->where($aWhere)

        );
        $this->UDisplay('show1');
    }
    public function abc1($data){
        foreach($data as $k=>$v){
            if($v['baoku_pic']){
                $data[$k]['baoku_pic']='是';
            }else{
                $data[$k]['baoku_pic']='否';
            }
        }
        foreach($data as $k=>$v){
            $data[$k]['fid']=M('Credit_fl')->where(array('id'=>$v['fid']))->getField('title');
        }
        return $data;
    }
    //添加分类
    public function add_fl(){
        // echo $this->tpl;die;
        $this->add('Credit_fl',array(
            array('title'=>"分类标题",'type'=>"input",'name'=>"title",'msg'=>'分类标题不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"分类logo图",'name'=>"pic",'msg'=>'必须上传图片'),


            ))
        ),U('Loan/fl_index',array('token'=>$this->token)));
    }
    //修改分类
    public function fl_edit(){
        $this->Edit('Credit_fl',array(
            array('title'=>"分类标题",'type'=>"input",'name'=>"title",'msg'=>'分类标题不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"分类logo图",'name'=>"pic",'msg'=>'必须上传图片'),


            ))
        ),U('Loan/fl_index',array('token',$this->token)));
    }
    //删除分类
    public function fl_del(){
        $this->del('Credit_fl');
    }
    /**
     *  添加-非信货产品
     * 如果写了msg代表此字段为必填，值为错误提示内容
     * array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y')   这是添加地图
     **/
    public function add_pinzhong(){
        $fl_select=M('Credit_fl')->field('id as value,title as content')->where(array('token'=>$this->token))->select();

        $is_show=array(
            array('value'=>1,'content'=>'显示日期'),
            array('value'=>2,'content'=>'选择人数'),
            array('value'=>3,'content'=>'其他人信息'),
        );

       // p($fl_select);die;
       // echo $this->tpl;die;
        $this->add('No_credit',array(

            array('title'=>"产品标题",'type'=>"input",'name'=>"title",'msg'=>'产品标题不能为空'),
            array('type'=>'select','title'=>"所属分类",'name'=>"fid",'many'=>$fl_select),
            array('type'=>'img','many'=>array(
                array('title'=>"产品展示图片1",'name'=>"pic",'msg'=>'必须上传一张产品展示图'),
                array('title'=>"产品展示图片2",'name'=>"image1"),
                    array('title'=>"产品展示图片3",'name'=>"image2"),
                        array('title'=>"产品展示图片4",'name'=>"image3"),
                            array('title'=>"产品展示图片5",'name'=>"image4"),
                                array('title'=>"产品展示图片6",'name'=>"image5"),
                array('title'=>"爆款大图",'name'=>"baoku_pic")

            )),
            array('title'=>"出发地点:地点",'type'=>"input",'name'=>"start_address"),
            array('title'=>"目的地点:地点",'type'=>"input",'name'=>"end_address"),
            array('title'=>"行程天数:7天6晚",'type'=>"input",'name'=>"day_num"),
           array('title'=>"费用包含:具体内容",'type'=>"textarea1",'name'=>"content"),
            array('title'=>"首付（元）",'type'=>"input",'name'=>"shoufu",'msg'=>'首付多少元必填！'),
            array('title'=>"分几期",'type'=>"input",'name'=>"fenqi",'msg'=>'分几期必填！'),
          /*  array('title'=>"每期还款金额(元)",'type'=>"input",'name'=>"monthly_repayments",'msg'=>'每期还款金额必填！'),*/
            array('title'=>"贷款总金额(元)",'type'=>"input",'name'=>"loan_total_money"),
            array('title'=>'显示方式','type'=>'checkbox','name'=>'is_show','many'=>$is_show),
            array('title'=>"最大购买数量",'type'=>"input",'name'=>"max"),
            array('title'=>"产品详情介绍",'type'=>"textarea",'name'=>"intro"),
            array('title'=>"预定须知",'type'=>"textarea1",'name'=>"info1"),
            array('title'=>"签证政策",'type'=>"textarea1",'name'=>"info2"),
            array('title'=>"退改规则",'type'=>"textarea1",'name'=>"info3"),
            array('title'=>"联系方式",'type'=>"textarea1",'name'=>"info4"),
           /* array('title'=>"经纬度",'type'=>"map",'name'=>'address','lng'=>"position_x",'lat'=>'position_y'),
            array('title'=>"开始时间",'type'=>"time",'name'=>"start_time"),
            array('title'=>"结束时间",'type'=>"time",'name'=>"end_time"),
            array('type'=>"hidden",'name'=>"kk")*/
        ),U('Loan/index',array('token'=>$this->token)),array($this,'bbc'));
    }

public function bbc($data){//这里处理展示图片的增加
    $data['monthly_repayments']=$data['loan_total_money']/$data['fenqi'];
   // $data['monthly_repayments']='548';
    //echo 1;die;
  //  $data['password']=MD5($data['password']);
  //  $data['start_time']=strtotime($_POST['start_time']);
    //$data['end_time']=strtotime($_POST['end_time']);
   // $data['fenqi']=777;
    return $data;
}
    /**
     * 编辑非售贷产品
     */
    public function PinzhongEdit(){
        $fl_select=M('Credit_fl')->field('id as value,title as content')->where(array('token'=>$this->_sToken))->select();
        $is_show=array(
            array('value'=>1,'content'=>'显示日期'),
            array('value'=>2,'content'=>'选择人数'),
            array('value'=>3,'content'=>'其他人信息'),
        );

        $this->Edit('No_credit',array(
            array('title'=>"产品标题",'type'=>"input",'name'=>"title",'msg'=>'产品标题不能为空'),
            array('type'=>'select','title'=>"所属分类",'name'=>"fid",'many'=>$fl_select),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图片",'name'=>"pic",'msg'=>'一定要给我产品图片咯!'),
                array('title'=>"产品展示图片2",'name'=>"image1"),
                array('title'=>"产品展示图片3",'name'=>"image2"),
                array('title'=>"产品展示图片4",'name'=>"image3"),
                array('title'=>"产品展示图片5",'name'=>"image4"),
                array('title'=>"产品展示图片6",'name'=>"image5"),
                array('title'=>"爆款大图",'name'=>"baoku_pic")
            )),
            array('title'=>"出发地点:地点",'type'=>"input",'name'=>"start_address"),
            array('title'=>"目的地点:地点",'type'=>"input",'name'=>"end_address"),
            array('title'=>"行程天数:7天6晚",'type'=>"input",'name'=>"day_num"),
            array('title'=>"费用包含:具体内容",'type'=>"textarea1",'name'=>"content"),
            array('title'=>"首付（元）",'type'=>"input",'name'=>"shoufu",'msg'=>'首付多少元必填！'),
            array('title'=>"分几期",'type'=>"input",'name'=>"fenqi",'msg'=>'分几期必填！'),
         /*   array('title'=>"每期还款金额(元)",'type'=>"input",'name'=>"monthly_repayments",'msg'=>'每期还款金额必填！'),*/
            array('title'=>"贷款总金额(元)",'type'=>"input",'name'=>"loan_total_money",'msg'=>'贷款总金额必填！'),
            array('title'=>'显示方式','type'=>'checkbox','name'=>'is_show','many'=>$is_show),
            array('title'=>"最大购买数量",'type'=>"input",'name'=>"max"),
            array('title'=>"产品详情介绍",'type'=>"textarea",'name'=>"intro"),
            array('title'=>"预定须知:内容",'type'=>"textarea1",'name'=>"info1"),
            array('title'=>"签证政策:内容",'type'=>"textarea1",'name'=>"info2"),
            array('title'=>"退改规则:内容",'type'=>"textarea1",'name'=>"info3"),
            array('title'=>"联系方式:内容",'type'=>"textarea1",'name'=>"info4"),
        ),U('Loan/index', array('token' => $this->token)),array($this,'bbc'));

    }

    /**
     *  删除非信贷产品种
     **/
    public function PinzhongDel(){
        $this->del('No_credit');
    }

    /**
     * 非信贷产品设置时间列表
     */
    public function set_time(){
        $id=$this->_get('id');
        $shoufu=M('No_credit')->where(array('id'=>$id))->getField('shoufu');
        $list=M('Nocredit_time')->where(array('cid'=>$id,'status'=>1))->select();
        $calc=new Calendar();
        $time_info=$calc->showCalendar($list,$id);

        $this->assign('time_info',$time_info);

        $this->display();
    }
    /**
     * 非信贷产品设置时间
     */
    public function edit_time(){

        $model=M('Nocredit_time');
        $data['cid']=$this->_get('cid');

        $data['y_m']=$this->_get('y_m');
        $a=explode('-',$data['y_m']);
       // p($a);die;
        $data['d']=$this->_get('d');
      //  $data['status']=1;
        $data['token']=$this->token;
       // $data['add_time']=time();
       /* if($model-where(array('cid'=>$_GET['cid'],'y_m'=>$_GET['y_m'],'d'=>$_GET['d']))){

        }*/
        /**
         * 这里是增加
         */
        if(isset($_GET['del'])){//删除

            if(M('Nocredit_time')->where($data)->save(array('status'=>0))){

                $this->success2('设置成功正在跳转',U('set_time',array('token'=>$this->token,'id'=>$_GET['cid'],'y'=>$a[0],'m1'=>$a[1])));
            }else{


                $this->error2('设置失败正在跳转',U('Loan/set_time',array('token'=>$this->token,'id'=>$_GET['cid'])));
            }

        }else{//增加

            if($id=M('Nocredit_time')->where($data)->getField('id')){
                if(M('Nocredit_time')->where(array('id'=>$id))->save(array('status'=>1,'num'=>$_GET['num']))){

                    $this->success2('设置成功正在跳转',U('set_time',array('token'=>$this->token,'id'=>$_GET['cid'],'y'=>$a[0],'m1'=>$a[1])));
                }else{
                    $this->error2('设置失败正在跳转',U('Loan/set_time',array('token'=>$this->token,'id'=>$_GET['cid'])));
                }
            }else{
                $data['status']=1;
                $data['add_time']=time();
                $data['num']=$_GET['num'];
                if($model->add($data)){
                    $this->success2('设置成功正在跳转',U('set_time',array('token'=>$this->token,'id'=>$_GET['cid'],'y'=>$a[0],'m1'=>$a[1])));
                }else{

                    $this->error2('设置失败正在跳转',U('set_time',array('token'=>$this->token,'id'=>$_GET['ID'],'y'=>$a[0],'m1'=>$a[1])));
                }

            }

        }



    }


    /**
     *  信贷产品列表
     **/
    public function credit_index(){
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
                'HeadHover' => U('Loan/credit_index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加信贷产品',
                        'url' => U('Loan/add_credit',array('token'=>$this->_sToken))
                    )
                ),
                'tips' => array(
                    '您说要注意什么呢！开心每一天'
                ),
                'Table_Header' => array(
                    'ID', '名称', '最大分期数','贷款起步金额','贷款最大金额', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('Loan/edit_credit')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('Loan/del_credit')
                    ),
                ),
                /* 'search'=>array(
                       array('title'=>'姓名1','name'=>'li_name'),
                       array('title'=>'姓名2','name'=>'eq_name2'),
                       array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                   )*/
            ),

            $this->pz1->where($aWhere)->count(),
            $this->pz1->field('id,name,max_fenqi,min_price,max_price')->where($aWhere)

        );
        $this->UDisplay('show1');

    }
    /**
     * 信贷产品添加
     */
    public function add_credit(){
        $this->add('Credit',array(
            array('title'=>"产品名称",'type'=>"input",'name'=>"name",'msg'=>'产品名称不能为空','placeholder'=>'这里请填写贷款产品名称'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图片",'name'=>"pic")
            )),
            array('title'=>"贷款起步金额(元)",'type'=>"input",'name'=>"min_price",'msg'=>'贷款起步金额必填咯！','placeholder'=>'例如：1000'),
            array('title'=>"贷款最大金额(元)",'type'=>"input",'name'=>"max_price",'placeholder'=>'例如:1000000'),
            array('title'=>"贷款最大期数",'type'=>"input",'name'=>"max_fenqi",'msg'=>'贷款最大期数必填！','placeholder'=>'例如:6'),
            array('title'=>"利息比例算法",'type'=>"input",'name'=>"lixi_bili",'placeholder'=>'暂定')
        ),U('Loan/credit_index',array('token'=>$this->token)));
    }
    /**
     * 信贷产品编辑
     */
    public function edit_credit(){
        // echo $this->tpl;die;
        $this->Edit('Credit',array(
            array('title'=>"产品名称",'type'=>"input",'name'=>"name",'msg'=>'产品名称不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图片",'name'=>"pic")
            )),
            array('title'=>"贷款起步金额(元)",'type'=>"input",'name'=>"min_price",'msg'=>'贷款起步金额必填咯！'),

            array('title'=>"贷款最大金额(元)",'type'=>"input",'name'=>"max_price"),
            array('title'=>"贷款最大期数",'type'=>"input",'name'=>"max_fenqi",'msg'=>'贷款最大期数必填！'),
            array('title'=>"利息比例算法",'type'=>"input",'name'=>"lixi_bili",'msg'=>'利息比例算法必填咯！')
        ),U('Loan/credit_index',array('token',$this->token)));
    }
    /**
     *  删除信贷产品种
     **/
    public function del_credit(){
        $this->del('Credit');
    }

    /**
     * 非信贷产品订单列表
     */
    public function order_index(){

        $aWhere['tp_no_credit_order.token'] =$this->_sToken;
        //这里加条件
        $aWhere['tp_no_credit_order.type']=1;
        $this->assign('paystatus',10);//进去搜索条件是全部为10
        if(IS_POST){
            $_POST=$_REQUEST;
          //  p($_POST);

            if($_POST['eq_paystatus']==10){
                $this->assign('paystatus',10);
            }
            $aWhere=$this->search($_POST);
            $aWhere['tp_no_credit_order.token'] =$this->_sToken;
            $aWhere['tp_no_credit_order.type']=1;
            /**
             * 条件改变
             */
         //   p($aWhere);die;
            if(array_key_exists('add_time',$aWhere)){//时间
                $aWhere['tp_no_credit_order.add_time']=$aWhere['add_time'];
                $this->assign('start_time',date('Y-m-d H:i:s',$aWhere['tp_no_credit_order.add_time'][1][0]));
                $this->assign('end_time',date('Y-m-d H:i:s',$aWhere['tp_no_credit_order.add_time'][1][1]));
                unset($aWhere['add_time']);
            }
            if(array_key_exists('true_name',$aWhere)){
                $aWhere['tp_credit_users.true_name']=$aWhere['true_name'];
                unset($aWhere['true_name']);
            }

           /* if($_POST['eq_paystatus']==0){
                $aWhere['tp_no_credit_order.paystatus']=0;
                $this->assign('paystatus',2);
            }*/
            if(array_key_exists('paystatus',$aWhere)){
                $aWhere['tp_no_credit_order.paystatus']=$aWhere['paystatus'];
                $this->assign('paystatus',$aWhere['paystatus'][1]);

                unset($aWhere['paystatus']);

            }
            if($aWhere['tp_no_credit_order.paystatus'][1]==10){//为了显示全部
                unset($aWhere['tp_no_credit_order.paystatus']);
            }
            if($aWhere['tp_no_credit_order.paystatus'][1]==11){//已付首付未上传资料
                unset($aWhere['tp_no_credit_order.paystatus']);
                $aWhere['tp_no_credit_order.paystatus']=1;//已支付
                $aWhere['tp_credit_users.pid']='';//以有没有图片来判断
            }
            if($aWhere['tp_no_credit_order.paystatus'][1]==1){//已付首付且上传资料
               // unset($aWhere['tp_no_credit_order.paystatus']);
                $aWhere['tp_no_credit_order.paystatus']=1;//已支付
                $aWhere['tp_credit_users.pid']=array('neq','');//以有没有图片来判断
            }
            if($aWhere['tp_no_credit_order.paystatus'][1]==13){//余期没有还款的
                unset($aWhere['tp_no_credit_order.paystatus']);
                $aWhere['tp_no_credit_order.is_yq']=1;//

            }
          //  p($aWhere);
        }


        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('Loan/order_index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '导出会员数据',
                        'type'=>'daochu',
                        'url' => U('Loan/excel_order',array('token'=>$this->token))
                    )

                ),
                'tips' => array(
                    '非信货产品订单列表'
                ),
                'Table_Header' => array(
                    'ID','订单编号', '产品名称', '真实姓名','首付','分几期','每期还款金额','订单状态','数量','时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '还款记录',
                        'url' => U('Loan/hk_jl',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '用户资料',
                        'url' => U('Loan/userinfo',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '核审',
                        'url' => U('Loan/hs',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '查看',
                        'url' => U('Loan/order_info',array('token'=>$this->token))
                    ),


                ),
                'search'=>array(

                       array('title'=>'姓名','name'=>'li_true_name'),
                         array('title'=>'支付状态','name'=>'eq_paystatus','type'=>1),
                       array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                ),
            /*    'tongji'=>array(
                    array('title'=>'订单总数','name'=>'order_total'),
                    array('title'=>'已付首付总金额','name'=>'zhifu_total'),
                    array('title'=>'贷款总金额(单)','name'=>'loan_total'),
                    array('title'=>'还款总金额(合)','name'=>'repayment_total')
                )*/
            ),

            $bbc['order_total']=$this->order->join("join tp_credit_users on tp_credit_users.id=tp_no_credit_order.uid")
                ->field('tp_no_credit_order.id,tp_no_credit_order.title,tp_credit_users.true_name,
                tp_no_credit_order.shoufu,tp_no_credit_order.fenqi,tp_no_credit_order.monthly_repayments,
                tp_no_credit_order.paystatus,tp_no_credit_order.people_num,
                tp_no_credit_order.add_time')->where($aWhere)->count(),
            $this->order->join("join tp_credit_users on tp_credit_users.id=tp_no_credit_order.uid")
                ->field('tp_no_credit_order.id,tp_no_credit_order.orderid,tp_no_credit_order.title,tp_credit_users.true_name,
                tp_no_credit_order.shoufu,tp_no_credit_order.fenqi,tp_no_credit_order.monthly_repayments,
                tp_no_credit_order.paystatus,tp_no_credit_order.many,
                tp_no_credit_order.add_time')->where($aWhere)->order('tp_no_credit_order.add_time desc'),
            array($this,'order_data')
        );
        //echo 8;
        $bbc['zhifu_total']=M('No_credit_order')->where(array('token'=>$this->_sToken,'type'=>1,'paystatus'=>array('in',array(1,5,7,2,9))))->getField('sum(shoufu)');
        $bbc['loan_total']=M('No_credit_order')->where(array('token'=>$this->_sToken,'type'=>1,'paystatus'=>5))->getField('sum(fenqi*monthly_repayments)');
        $bbc['repayment_total']=M('Hk_jl')->where(array('token'=>$this->_sToken,'paystatus'=>1))->getField('sum(money)');

        $this->assign($bbc);
     //   p($_POST);
        $this->UDisplay('show1');


    }
    //非信贷产品取消订单
    public function order_quxiao(){
        if(M('No_credit_order')->where(array('id'=>$_GET['id']))->getField('paystatus')==0){
            if(M('No_credit_order')->where(array('id'=>$_GET['id']))->save(array('paystatus'=>-1))!==false){
                $this->success2('取消成功');
            }else{
                $this->error2('取消失败');
            }
        }else{
            $this->error2('订单无法取消');
        }


    }
    public function order_data($data){
        foreach($data as $k=>$v){

            /**
             * 人数
             */
            if($v['people_num']==1){
                $data[$k]['people_num']='1人';
            }
            if($v['people_num']==2){
                $data[$k]['people_num']='2人(AA制)';
            }
            if($v['people_num']==3){
                $data[$k]['people_num']='2人(付全部)';
            }

            if($v['type']==1){
                $data[$k]['type']='非信贷';
            }
            if($v['type']==2){
                $data[$k]['type']='信贷';
            }
            if($v['paystatus']==0){
                $data[$k]['paystatus']='未支付';
            }
            if($v['paystatus']==1){
               // $data[$k]['paystatus']='已支付审核中';
                $uid=M('No_credit_order')->where(array('id'=>$v['id']))->getField('uid');
                if($pid=M('Credit_users')->where(array('id'=>$uid))->getField('pid')){//查用户表里面有没有图片
                    $data[$k]['paystatus']='已支付且资料完整';
                }else{
                   $data[$k]['paystatus']='已支付未上传资料';
                  //  $data[$k]['paystatus']=$v['uid'];
                }
            }
            if($v['paystatus']==-1){
                $data[$k]['paystatus']='已取消';
            }
            if($v['paystatus']==5){
                $data[$k]['paystatus']='已支付且通过审核';
                //这里把余期搞出来
                $hs_time=M('No_credit_order')->where(array('id'=>$v['id']))->getField('hs_time');
                $end_time=date('Y-m-d',time());
                $start_time=date('Y-m-d',$hs_time);
                $qisu=getMonthNum($start_time,$end_time);
                for($i=1;$i<=$qisu;$i++){
                       if(!M('Hk_jl')->where(array('oid'=>$v['id'],'qisu'=>$i,'paystatus'=>1))->find()){//余期了
                           $data[$k]['yuqi']='余期';
                           $data[$k]['paystatus']='有余期未还款';
                           break;
                         }
                }

            }
            if($v['paystatus']==2){
                $data[$k]['paystatus']='已支付未通过审核';
            }
            if($v['paystatus']==7){
                $data[$k]['paystatus']='申请退款';
            }
            if($v['paystatus']==8){
                $data[$k]['paystatus']='退款成功';
            }
            if($v['paystatus']==9){
                $data[$k]['paystatus']='退款失败';
            }
            if($v['paystatus']==12){
                $data[$k]['paystatus']='已全额还款';
            }

            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    /**
     * 查看订单
     */
    public function order_info(){
        $info=M('No_credit_order')->field('id,date_time,many,orderid')->where(array('id'=>$_GET['id']))->find();
        $list=M('No_credit_people')->where(array('cid'=>$_GET['id']))->select();
      //  echo 1;

        $this->assign('list',$list);
        $this->assign('info',$info);
        $this->display();
    }
    //导出非信贷产品订单
    public function excel_order(){
        $_POST=$_REQUEST;

        if($_POST['eq_paystatus']==10){
            $this->assign('paystatus',10);
        }
        $aWhere=$this->search($_POST);
        $aWhere['tp_no_credit_order.token'] =$this->_sToken;
        $aWhere['tp_no_credit_order.type']=1;
        /**
         * 条件改变
         */
        //   p($aWhere);die;
        if(array_key_exists('add_time',$aWhere)){//时间
            $aWhere['tp_no_credit_order.add_time']=$aWhere['add_time'];
            $this->assign('start_time',date('Y-m-d H:i:s',$aWhere['tp_no_credit_order.add_time'][1][0]));
            $this->assign('end_time',date('Y-m-d H:i:s',$aWhere['tp_no_credit_order.add_time'][1][1]));
            unset($aWhere['add_time']);
        }
        if(array_key_exists('true_name',$aWhere)){
            $aWhere['tp_credit_users.true_name']=$aWhere['true_name'];
            unset($aWhere['true_name']);
        }

        /* if($_POST['eq_paystatus']==0){
             $aWhere['tp_no_credit_order.paystatus']=0;
             $this->assign('paystatus',2);
         }*/
        if(array_key_exists('paystatus',$aWhere)){
            $aWhere['tp_no_credit_order.paystatus']=$aWhere['paystatus'];
            $this->assign('paystatus',$aWhere['paystatus'][1]);

            unset($aWhere['paystatus']);

        }
        if($aWhere['tp_no_credit_order.paystatus'][1]==10){//为了显示全部
            unset($aWhere['tp_no_credit_order.paystatus']);
        }
        if($aWhere['tp_no_credit_order.paystatus'][1]==11){//已付首付未上传资料
            unset($aWhere['tp_no_credit_order.paystatus']);
            $aWhere['tp_no_credit_order.paystatus']=1;//已支付
            $aWhere['tp_credit_users.pid']='';//以有没有图片来判断
        }
        if($aWhere['tp_no_credit_order.paystatus'][1]==13){//余期没有还款的
            unset($aWhere['tp_no_credit_order.paystatus']);
            $aWhere['tp_no_credit_order.is_yq']=1;//

        }
      //  p($aWhere);
        $data=M('No_credit_order')->join("join tp_credit_users on tp_credit_users.id=tp_no_credit_order.uid")
            ->field('tp_no_credit_order.orderid,tp_no_credit_order.title,tp_credit_users.true_name,
                tp_no_credit_order.shoufu,tp_no_credit_order.fenqi,tp_no_credit_order.monthly_repayments,
                tp_no_credit_order.paystatus,tp_no_credit_order.people_num,
                tp_no_credit_order.add_time')->where($aWhere)->order('tp_no_credit_order.add_time desc')->select();
        foreach($data as $k=>$v){

            /**
             * 人数
             */
            if($v['people_num']==1){
                $data[$k]['people_num']='1人';
            }
            if($v['people_num']==2){
                $data[$k]['people_num']='2人(AA制)';
            }
            if($v['people_num']==3){
                $data[$k]['people_num']='2人(付全部)';
            }

            if($v['type']==1){
                $data[$k]['type']='非信贷';
            }
            if($v['type']==2){
                $data[$k]['type']='信贷';
            }
            if($v['paystatus']==0){
                $data[$k]['paystatus']='未支付';
            }
            if($v['paystatus']==1){
                $data[$k]['paystatus']='已支付审核中';
                /*  if(M('Credit_users')->where(array('id'=>$v['uid']))->getField('pid')){//查用户表里面有没有图片
                        $data[$k]['paystatus']='已支付审核中';
                    }else{
                        $data[$k]['paystatus']='已支付未上传资料1';
                    }*/
            }
            if($v['paystatus']==-1){
                $data[$k]['paystatus']='已取消';
            }
            if($v['paystatus']==5){
                $data[$k]['paystatus']='已支付且通过审核';


            }
            if($v['paystatus']==2){
                $data[$k]['paystatus']='已支付未通过审核';
            }
            if($v['paystatus']==7){
                $data[$k]['paystatus']='申请退款';
            }
            if($v['paystatus']==8){
                $data[$k]['paystatus']='退款成功';
            }
            if($v['paystatus']==9){
                $data[$k]['paystatus']='退款失败';
            }


            if($v['paystatus']==12){
                $data[$k]['paystatus']='已全额还款';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        exportExcel($data,array('订单编号','产品名称','姓名','首付','贷款期数',
            '每期还款金额','订单状态','人数','下单时间'),'非信贷订单');
    }

    /**
     * 信贷产品订单列表
     */
    public function credit_order(){

        $aWhere['tp_no_credit_order.token'] =$this->_sToken;
        $aWhere['tp_no_credit_order.type'] =2;
        $this->assign('paystatus',10);//进去搜索条件全部
        if(IS_POST){
            $_POST=$_REQUEST;

            $aWhere=$this->search($_POST);
            $aWhere['tp_no_credit_order.token'] =$this->_sToken;
            $aWhere['tp_no_credit_order.type']=2;
            /**
             * 条件改变
             */
            if(array_key_exists('add_time',$aWhere)){//时间
                $aWhere['tp_no_credit_order.add_time']=$aWhere['add_time'];
                $this->assign('start_time',date('Y-m-d H:i:s',$aWhere['tp_no_credit_order.add_time'][1][0]));
                $this->assign('end_time',date('Y-m-d H:i:s',$aWhere['tp_no_credit_order.add_time'][1][1]));
                unset($aWhere['add_time']);
            }
            if(array_key_exists('true_name',$aWhere)){
                $aWhere['tp_credit_users.true_name']=$aWhere['true_name'];
                unset($aWhere['true_name']);
            }


            if(array_key_exists('paystatus',$aWhere)){
                $aWhere['tp_no_credit_order.paystatus']=$aWhere['paystatus'];
                $this->assign('paystatus',$aWhere['paystatus'][1]);

                unset($aWhere['paystatus']);

            }
            if($aWhere['tp_no_credit_order.paystatus'][1]==10){//为了显示全部
                unset($aWhere['tp_no_credit_order.paystatus']);
            }
            if($aWhere['tp_no_credit_order.paystatus'][1]==13){//余期未还款
                unset($aWhere['tp_no_credit_order.paystatus']);
                $aWhere['tp_no_credit_order.is_yq']=1;
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('Loan/credit_order', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '导出会员数据',
                        'type'=>'daochu',
                        'url' => U('Loan/excel_credit',array('token'=>$this->token))
                    ),
                ),
                'tips' => array(
                    '信货产品订单列表'
                ),
                'Table_Header' => array(
                    'ID', '标题', '姓名','贷款总金额','分几期','每期还款金额','状态','时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '还款记录',
                        'url' => U('Loan/hk_jl',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '核审',
                        'url' => U('Loan/hs',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '用户资料',
                        'url' => U('Loan/userinfo',array('token'=>$this->token))
                    )
                ),
                'search'=>array(
                    array('title'=>'姓名','name'=>'li_true_name'),
                    array('title'=>'审核状态','name'=>'eq_paystatus','type'=>2),
                    array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                   ),
               /* 'tongji'=>array(
                    array('title'=>'订单总数','name'=>'order_total'),
                    array('title'=>'贷款总金额(单)','name'=>'loan_total'),
                    array('title'=>'还款总金额(合)','name'=>'repayment_total')
                )*/
            ),

            $tongji['order_total']=$this->order->join("join tp_credit_users on tp_credit_users.id=tp_no_credit_order.uid")->where($aWhere)->count(),
            $this->order->join("join tp_credit_users on tp_credit_users.id=tp_no_credit_order.uid")
                ->field('tp_no_credit_order.id,tp_no_credit_order.title,tp_credit_users.true_name,
                tp_no_credit_order.loan_total_money,tp_no_credit_order.fenqi,
                tp_no_credit_order.monthly_repayments,tp_no_credit_order.paystatus,tp_no_credit_order.add_time')->where($aWhere)
            ->order('tp_no_credit_order.add_time desc'),
            array($this,'credit_data')

        );
        $tongji['loan_total']=M('No_credit_order')->where(array('token'=>$this->_sToken,'type'=>2,'paystatus'=>1))->getField('sum(loan_total_money)');
        $tongji['repayment_total']=M('Hk_jl')->where(array('token'=>$this->_sToken,'paystatus'=>1))->getField('sum(money)');
        $this->assign($tongji);
        $this->UDisplay('show1');

    }
    public function credit_data($data){
        foreach($data as $k=>$v){

            if($v['paystatus']==0){
                $data[$k]['paystatus']='正在审核中';
            }
            if($v['paystatus']==-1){
                $data[$k]['paystatus']='订单已取消';
            }
            if($v['paystatus']==1){
                $data[$k]['paystatus']='审核通过';
                //这里把余期搞出来
                $hs_time=M('No_credit_order')->where(array('id'=>$v['id']))->getField('hs_time');
                $end_time=date('Y-m-d',time());
                $start_time=date('Y-m-d',$hs_time);
                $qisu=self::getMonthNum($start_time,$end_time);
                for($i=1;$i<=$qisu;$i++){
                    if(!M('Hk_jl')->where(array('oid'=>$v['id'],'qisu'=>$i,'paystatus'=>1))->find()){//余期了
                        $data[$k]['yuqi']='余期';
                        $data[$k]['paystatus']='有余期未还';
                        break;
                    }
                }
            }
            if($v['paystatus']==2){
                $data[$k]['paystatus']='审核未通过';
            }
            if($v['paystatus']==12){
                $data[$k]['paystatus']='已全额还款';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }

    //审核
    public function hs()
    {

        if (IS_POST) {
            if (isset($_POST['type'])) {//退款
                if (M('No_credit_order')->where(array('id' => $_POST['id']))->save(array('paystatus' => $_POST['paystatus'])) !== false) {
                    /*
                    * 通知微信
                     */
                    $info = M('No_credit_order')->field('id,orderid,title,uid,openid')->find($_POST['id']);
                    $name = M('Credit_users')->where(array('id' => $info['uid']))->getField('true_name');//名字
                    $kk = "";
                    if ($_POST['paystatus'] == 8) {
                        $kk = "退款成功";
                        //发短信
                        $phone = M('Credit_users')->where(array('id' => $info['uid']))->getField('phone');
                         $info1="【如多分期】朋友，我看到了你的退款请求了，我也同意并做了处理了。有些遗憾，你是真的不喜欢“".$info['title']."”还是有其他疑问？不要害羞，放心大胆打给我吧，我想和你谈谈。咱的电话4008622580，客服帅哥等你！";
                        $openidYz = sendPhomeCode($this->token, $phone, $info1);
                        $openidYz = json_decode($openidYz, true);
                        if ($openidYz['code'] == 0) {//为真
                            //发微信
                            $notichcontent ="【如多分期】朋友，我看到了你的退款请求了，我也同意并做了处理了。有些遗憾，你是真的不喜欢“".$info['title']."”还是有其他疑问？不要害羞，放心大胆打给我吧，我想和你谈谈。咱的电话4008622580，客服帅哥等你！";
                            $postdata = array('openid' => $info['openid'], 'token' => $this->token, 'content' => $notichcontent);
                            $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                            $data = $this->api_notice_increment($url, http_build_query($postdata));
                            if (!$data) {
                                $this->api_notice_increment($url, http_build_query($postdata));
                            }
                        }
                    }

                    if ($_POST['paystatus'] == 9) {
                        $kk = "退款失败";
                    }
                   /* $notichcontent = $name . "您好,\n您的订单：" . $info['orderid'] . "\n" . $info['title'] . "\n已经" . $kk . "特发此信息通知您";
                    $postdata = array('openid' => $this->openid, 'token' => $this->token, 'content' => $notichcontent);
                    $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                    $data = $this->api_notice_increment($url, http_build_query($postdata));
                    if (!$data) {
                        $this->api_notice_increment($url, http_build_query($postdata));
                    }*/


                    $this->success2("操作成功", U('order_index', array('token' => $this->token)));
                } else {
                    $this->error2("操作失败", U('order_index', array('token' => $this->token)));
                }
            } else {//贷款
                if (M('No_credit_order')->where(array('id' => $_GET['id']))->getField('type') == 1) {//非信贷的
                    if ($_POST['iftour'] == 1) {//通过
                        $paystatus = 5;
                    }
                    if ($_POST['iftour'] == 2) {//未通过
                        $paystatus = 2;
                    }
                    if (M('No_credit_order')->where(array('id' => $_POST['id']))->save(array('hs_time' => time(), 'paystatus' => $paystatus)) !== false) {
                        //通过的时候把用户认证
                        $uid = M('No_credit_order')->where(array('id' => $_POST['id']))->getField('uid');
                        if ($_POST['iftour'] == 1) {//通过
                            $authentication = 1;
                        }
                        if ($_POST['iftour'] == 2) {//未通过
                            $authentication = -1;
                        }
                        /*
                       * 通知微信
                        */
                        $info = M('No_credit_order')->field('id,orderid,title,uid,date_time,openid')->find($_POST['id']);
                        $name = M('Credit_users')->where(array('id' => $info['uid']))->getField('true_name');//名字
                        $kk = "";

                        if ($_POST['iftour'] == 1) {
                            $kk = "贷款审核通过";
                            //发短信
                            $phone = M('Credit_users')->where(array('id' => $info['uid']))->getField('phone');
                         //   echo $phone;
                           /* $info1 = "【如多分期】嗨，让客官久等啦！经过审核，我们风控官决定放行客官的订单“" . $info['orderid'] . "”。酱紫，带上客官的身份证和学生证，" . $info['date_time'] . "去仁爱医院术前检查先吧！这是订单确认码“" . $info['orderid'] . "”，到医院后拿确认码接受服务即可。
还有不清晰？还是老办法—Call我们的帅哥客服4008622580！";*/
                            $info2 ="【如多分期】嗨，让客官久等啦！经过审核，我们风控官决定放行客官的订单“".$info['orderid']."”。酱紫，带上客官的身份证和学生证，".$info['date_time']."去仁爱医院术前检查先吧！这是订单确认码“".$info['orderid']."”，到医院后拿确认码接受服务即可。还有不清晰？还是老办法—Call我们的帅哥客服4008622580！";
                            $openidYz = sendPhomeCode($this->token, $phone, $info2);
                            $openidYz = json_decode($openidYz, true);
                            //发微信
                            $notichcontent = "【如多分期】嗨，让客官久等啦！经过审核，我们风控官决定放行客官的订单“" . $info['orderid'] . "”。酱紫，带上客官的身份证和学生证，" . $info['date_time'] . "去仁爱医院术前检查先吧！这是订单确认码“" . $info['orderid'] . "”，到医院后拿确认码接受服务即可。
还有不清晰？还是老办法—Call我们的帅哥客服4008622580！";
                            $postdata = array('openid' => $info['openid'], 'token' => $this->token, 'content' => $notichcontent);
                            $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                            $data = $this->api_notice_increment($url, http_build_query($postdata));
                          //  echo $notichcontent;die;
                            if (!$data) {
                                $this->api_notice_increment($url, http_build_query($postdata));
                            }
                            if ($openidYz['code'] == 0) {//为真

                            }
                        }


                        if ($_POST['iftour'] == 2) {
                            $kk = "贷款审核失败";
                            //发短信
                            $phone = M('Credit_users')->where(array('id' => $info['uid']))->getField('phone');
                            $info1= "【如多分期】朋友，很抱歉地告诉客官，客官在我们如多分期平台下的订单“" . $info['orderid'] . "”被我们的风控残忍的拒绝了！白魔女虽狠但长情，不要桑心，试试如多其他产品吧！";
                            $openidYz = sendPhomeCode($this->token, $phone, $info1);
                            $openidYz = json_decode($openidYz, true);
                            if ($openidYz['code'] == 0) {//为真
                                //发微信
                                $notichcontent = "【如多分期】朋友，很抱歉地告诉客官，客官在我们如多分期平台下的订单“" . $info['orderid'] . "”被我们的风控残忍的拒绝了！白魔女虽狠但长情，不要桑心，试试如多其他产品吧！";
                                $postdata = array('openid' => $info['openid'], 'token' => $this->token, 'content' => $notichcontent);
                                $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                                $data = $this->api_notice_increment($url, http_build_query($postdata));
                                if (!$data) {
                                    $this->api_notice_increment($url, http_build_query($postdata));
                                }
                            }
                        }
                        /*$notichcontent = $name."您好,\n您的订单：".$info['orderid']."\n".$info['title']."\n已经".$kk."特发此信息通知您";
                        $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
                        $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                        $data = $this->api_notice_increment($url,http_build_query($postdata));
                        if(!$data){
                            $this->api_notice_increment($url,http_build_query($postdata));
                        }*/

                        //把人物资料插入订单表的user_info字段
                        $info_json = M('No_credit_order')->where(array('id' => $_GET['id']))->getField('uid');
                        $info_json = M('Credit_users')->find($info_json);
                        $info_json = json_encode($info_json);
                        M('No_credit_order')->where(array('id' => $_GET['id']))->save(array('user_info' => $info_json));


                        M('Credit_users')->where(array('id' => $uid))->save(array('authentication' => $authentication));
                        $this->success2("操作成功", U('order_index', array('token' => $this->token)));
                    } else {
                        $this->error2("操作失败", U('order_index', array('token' => $this->token)));
                    }
                } else {//信货的type=2
                    if (M('No_credit_order')->where(array('id' => $_POST['id']))->save(array('hs_time' => time(), 'paystatus' => $_POST['iftour'])) !== false) {
                        //通过的时候把用户认证
                        $uid = M('No_credit_order')->where(array('id' => $_POST['id']))->getField('uid');
                        //把用户信息存到user_info
                        $info2 = M('Credit_users')->find($uid);
                        $info2 = json_encode($info2);
                        M('No_credit_order')->where(array('id' => $_POST['id']))->save(array('user_info' => $info2));

                        if ($_POST['iftour'] == 1) {//通过
                            $authentication = 1;
                        }
                        if ($_POST['iftour'] == 2) {//未通过
                            $authentication = -1;
                        }
                        M('Credit_users')->where(array('id' => $uid))->save(array('authentication' => $authentication));
                        /*
                      * 通知微信
                       */
                        $info = M('No_credit_order')->field('id,orderid,title,uid')->find($_POST['id']);
                        $name = M('Credit_users')->where(array('id' => $info['uid']))->getField('true_name');//名字
                        $kk = "";
                        if ($_POST['iftour'] == 1) {
                            $kk = "贷款审核通过";
                            //发短信
                            $phone = M('Credit_users')->where(array('id' => $info['uid']))->getField('phone');
                            $info1 = "【如多分期】嗨，让客官久等啦！经过审核，我们风控官决定放行客官的订单“". $info['orderid'] ."”。酱紫，带上客官的身份证和学生证，".$info['date_time']."去仁爱医院术前检查先吧！这是订单确认码“".$info['orderid']."”，到医院后拿确认码接受服务即可。
还有不清晰？还是老办法—Call我们的帅哥客服4008622580！";
                            $openidYz = sendPhomeCode($this->token, $phone, $info1);
                            $openidYz = json_decode($openidYz, true);
                            if ($openidYz['code'] == 0) {//为真
                                //发微信
                                $notichcontent = "【如多分期】嗨，让客官久等啦！经过审核，我们风控官决定放行客官的订单“".$info['orderid']."”。酱紫，带上客官的身份证和学生证，".$info['date_time']."去仁爱医院术前检查先吧！这是订单确认码“".$info['orderid']."”，到医院后拿确认码接受服务即可。
还有不清晰？还是老办法—Call我们的帅哥客服4008622580！";
                                $postdata = array('openid' => $info['openid'], 'token' => $this->token, 'content' => $notichcontent);
                                $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                                $data = $this->api_notice_increment($url, http_build_query($postdata));
                                if (!$data) {
                                    $this->api_notice_increment($url, http_build_query($postdata));
                                }

                            }
                            if ($_POST['iftour'] == 2) {
                                $kk = "贷款审核失败";
                                //发短信
                                $phone = M('Credit_users')->where(array('id' => $info['uid']))->getField('phone');
                                $info1 = "【如多分期】朋友，很抱歉地告诉客官，客官在我们如多分期平台下的订单“".$info['orderid']."”被我们的风控残忍的拒绝了！白魔女虽狠但长情，不要桑心，试试如多其他产品吧！";
                                $openidYz = sendPhomeCode($this->token, $phone, $info1
                                );
                                $openidYz = json_decode($openidYz, true);
                                if ($openidYz['code'] == 0) {//为真
                                    //发微信
                                    $notichcontent = "【如多分期】朋友，很抱歉地告诉客官，客官在我们如多分期平台下的订单“" . $info['orderid'] . "”被我们的风控残忍的拒绝了！白魔女虽狠但长情，不要桑心，试试如多其他产品吧！";
                                    $postdata = array('openid' => $info['openid'], 'token' => $this->token, 'content' => $notichcontent);
                                    $url = C('site_url') . "/index.php?g=Home&m=Auth&a=sendTextMsg";
                                    $data = $this->api_notice_increment($url, http_build_query($postdata));
                                    if (!$data) {
                                        $this->api_notice_increment($url, http_build_query($postdata));
                                    }
                                }
                            }
                            /* $notichcontent = $name."您好,\n您的订单：".$info['orderid']."\n".$info['title']."\n已经".$kk."特发此信息通知您";
                             $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
                             $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                             $data = $this->api_notice_increment($url,http_build_query($postdata));
                             if(!$data){
                                 $this->api_notice_increment($url,http_build_query($postdata));
                             }*/
                            $this->success2("操作成功", U('credit_order', array('token' => $this->token)));
                        } else {
                            $this->error2("操作失败", U('credit_order', array('token' => $this->token)));
                        }
                    }
                }
            }
        }else{
                $info = M('No_credit_order')->field('type,paystatus')->where(array('id' => $_GET['id']))->find();

                $this->assign('info', $info);
                //  p($info);
                $this->display();
        }
    }

    //审核用户
    public function hs_user(){
        if(IS_POST){
           // p($_POST);die;
            if(M('Credit_users')->where(array('id'=>$_POST['id']))->save(array('authentication'=>$_POST['authentication']))!==false){
                $this->success2("操作成功",U('users_index',array('token'=>$this->token)));
            }else{
                $this->error2("操作失败",U('users_index',array('token'=>$this->token)));
            }
        }else{
            $info=M('Credit_users')->field('authentication')->find($_GET['id']);
            //p($info);
            $this->assign('info',$info);

            $this->display();
        }


    }
    /**
     * 用户详细资料
     */
    public function userinfo(){
        if(isset($_GET['snnum'])){
            $info = M('Credit_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        }else{
            if(isset($_GET['type'])&&$_GET['type']==1){//这里是会员列表，里面查看详情
                $info = M('Credit_users')->where(array('id' => $_GET['id']))->find();
            }else {//这里是订单列表，里面查看详情,从订单user_info里面拿用户资料
                $list = M('No_credit_order')->field('uid,type,user_info,paystatus')->where(array('id' => $_GET['id']))->find();
                $uid = $list['uid'];
                // echo $list['user_info'];
                if($list['type']==1&&in_array($list['paystatus'],array(0,1,-1,7,8,9))){//首付订单
                    $info=M('Credit_users')->find($list['uid']);
                }else{
                    $info=json_decode($list['user_info'],true);
                    //这里把银行卡信息补充进来成最新的
                    $info1=M('Credit_users')->field('bank_city,bank_card,bank_name')->find($list['uid']);
                    $info['bank_name']=$info1['bank_name'];
                    $info['bank_card']=$info1['bank_card'];
                    $info['bank_city']=$info1['bank_city'];
                }
            }
        }

        /**
         * 拿图片资料
         */
        $a=explode(',',trim($info['pid'],','));

        $info['images']=M('Credit_users_pic')->where(array('id'=>array('in',$a)))->select();
        /**
         * 拿紧急联系人资料
         */
        $b=explode(',',trim($info['lid'],','));
        $info['friends']=M('Credit_users_friend')->where(array('id'=>array('in',$b)))->select();
        $info['weixin_info']=M('Wxusers')->field('nickname,headimgurl')->where(array('id'=>$info['wxusers_id']))->find();
        //p($info);
        $this->assign('type',$list['type']);//这里区分哪种类型 的产品
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 还款记录
     */
    public function hk_jl(){
        $info=M('No_credit_order')->field('hs_time,fenqi,paystatus,type')->where(array('id'=>$_GET['id']))->find();
        $time_date=date('Y-m-d',$info['hs_time']);
        $arr=array();
        for($i=1;$i<=$info['fenqi'];$i++){
            $arr[$i]['start_time']=date("Y-m-d", strtotime('+'.($i-1).' months', $info['hs_time']));
            $a=strtotime($time_date);
            $arr[$i]['end_time']=date("Y-m-d", strtotime('+'.$i.' months', $info['hs_time']));
            $b=strtotime(date("Y-m-d", strtotime('+'.$i.' months', $info['hs_time'])));
            if($hk_jl=M('Hk_jl')->where(array('token'=>$this->token,'oid'=>$_GET['id'],'paystatus'=>1,
            'qisu'=>$i))->find()){
                $arr[$i]['hk_jl']=$hk_jl;
            }else{
                if(time()>strtotime('+'.$i.' months', $info['hs_time'])){//余期
                    $arr[$i]['yuqi']="余期";
                }
            }
        }

        $this->assign('arr',$arr);
       // p($arr);
        $this->assign('info',$info);
        $this->display();

    }

//给出两个日期算中间有多少个月分
  public function getMonthNum($start,$end){
        $start=strtotime($start);
        $end=strtotime($end);
        $start_y=date('y',$start);
        $start_m=date('m',$start);
        $start_d=date('d',$start);

        $end_y=date('y',$end);
        $end_m=date('m',$end);
        $end_d=date('d',$end);
        $m=($end_y-$start_y)*12+$end_m-$start_m;
        if($end_d<=$start_d){
            $m=$m-1;
        }
        return $m;
    }
    //会员管理

    public function users_index(){
        $aWhere['tp_credit_users.token'] =$this->_sToken;
        $this->assign('authentication',10);//开始的搜索条件全部
        if(IS_POST){
      //      p($_POST);
            $_POST=$_REQUEST;
            $aWhere=$this->search($_POST);
            $aWhere['tp_credit_users.token'] =$this->_sToken;
            if(isset($aWhere['nickname'])){
                $aWhere['tp_wxusers.nickname']=$aWhere['nickname'];
            }
            if(isset($aWhere['true_name'])){
                $aWhere['tp_credit_users.true_name']=$aWhere['true_name'];
            }
           if(isset($aWhere['authentication'])){
               if($aWhere['authentication'][1]==10){
                    unset($aWhere['authentication']);
                   $this->assign('authentication',10);
               }else{
                   $aWhere['tp_credit_users.authentication']=$aWhere['authentication'];
                   $this->assign('authentication',$aWhere['authentication'][1]);
               }

            }
            if(isset($aWhere['authentication'])){
                unset($aWhere['authentication']);
            }
            if(isset($aWhere['tp_credit_users.authentication'])&&$aWhere['tp_credit_users.authentication'][1]==0){
                $aWhere['tp_credit_users.pid']='';//没有图片
            }
            if($aWhere['tp_credit_users.authentication'][1]==2){
                $aWhere['tp_credit_users.authentication'][1]=0;
                $aWhere['tp_credit_users.pid']=array('neq','');//有图片
            }

        // p($aWhere);

        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('Loan/users_index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '导出会员数据',
                        'type'=>'daochu',
                        'url' => U('Loan/excel_users',array('token'=>$this->token))
                    )
                ),
                'tips' => array(
                    '会员管理列表！'
                ),
                'Table_Header' => array(
                    '会员号', '微信昵称','真实名字','性别','认证','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '会员详情',
                        'url' => U('Loan/userinfo',array('token'=>$this->token,'type'=>1))
                    ),
                    array(
                        'name' => '会员审核',
                        'url' => U('Loan/hs_user',array('token'=>$this->token,'type'=>1))
                    ),

                ),
                'search'=>array(
                       array('title'=>'微信昵称','name'=>'li_nickname'),
                       array('title'=>'真实姓名','name'=>'li_true_name'),
                       array('title'=>'是否认证','name'=>'eq_authentication','type'=>4),
                )
            ),

            M('Credit_users')->join("join tp_wxusers on tp_wxusers.id=tp_credit_users.wxusers_id")->where($aWhere)->count(),
            M('Credit_users')->join("join tp_wxusers on tp_wxusers.id=tp_credit_users.wxusers_id")->field('tp_credit_users.id,tp_wxusers.nickname,tp_credit_users.true_name,tp_credit_users.sex,tp_credit_users.authentication')->where($aWhere),
            array($this,'sex')

        );
        $this->UDisplay('show1');

    }
    //替换性别 认证
    public function sex($data){
        foreach($data as $k=>$v){
            if($v['sex']==1){
                $data[$k]['sex']='男';
            }
            if($v['sex']==0){
                $data[$k]['sex']=' 女';
            }
            if($v['authentication']==0){
                if(M('Credit_users')->where(array('id'=>$v['id']))->getField('pid')){
                    $info=M('Credit_users')->field('sex,check_card')->find($v['id']);
                    if($info['check_card']>3){
                        $data[$k]['authentication']='新用户申请(可疑用户)';
                    }else{
                        $data[$k]['authentication']='新用户申请';

                    }
                }else{
                    $data[$k]['authentication']='未认证资料不完整';
                }

            }
            if($v['authentication']==1){
                $data[$k]['authentication']='审查通过';
            }
            if($v['authentication']==-1){
                $data[$k]['authentication']='审查未通过';
            }

        }
        return $data;

    }
    //会员重置密码
    public function reset_pwd(){
        if(M('Credit_users')->where(array('id'=>$_GET['id']))->save(array('pwd'=>''))!==false){
            echo json_encode(array('status'=>1));
        }else{
            echo json_encode(array('status'=>1));
        }
    }
    /**
     * 导出会员数据
     */
    public function excel_users(){
        $_POST=$_REQUEST;
        $aWhere=$this->search($_POST);
        $aWhere['tp_credit_users.token'] =$this->_sToken;
        if(isset($aWhere['nickname'])){
            $aWhere['tp_wxusers.nickname']=$aWhere['nickname'];
        }
        if(isset($aWhere['true_name'])){
            $aWhere['tp_credit_users.true_name']=$aWhere['true_name'];
        }
        if(isset($aWhere['authentication'])){
            if($aWhere['authentication'][1]==10){
                unset($aWhere['authentication']);
                $this->assign('authentication',10);
            }else{
                $aWhere['tp_credit_users.authentication']=$aWhere['authentication'];
                $this->assign('authentication',$aWhere['authentication'][1]);
            }

        }
        if(isset($aWhere['authentication'])){
            unset($aWhere['authentication']);
        }
        if(isset($aWhere['tp_credit_users.authentication'])&&$aWhere['tp_credit_users.authentication'][1]==0){
            $aWhere['tp_credit_users.pid']='';//没有图片
        }
        if($aWhere['tp_credit_users.authentication'][1]==2){
            $aWhere['tp_credit_users.authentication'][1]=0;
            $aWhere['tp_credit_users.pid']=array('neq','');//有图片
        }

        $data=M('Credit_users')->join("join tp_wxusers on tp_wxusers.id=tp_credit_users.wxusers_id")
            ->field("tp_wxusers.nickname,tp_credit_users.true_name,tp_credit_users.phone,tp_credit_users.zhiye,
            tp_credit_users.card_num,tp_credit_users.detail_now,tp_credit_users.detail_home,tp_credit_users.school,
            tp_credit_users.xuewei,tp_credit_users.school_time,tp_credit_users.bank_name,tp_credit_users.bank_card,tp_credit_users.bank_city")
            ->where($aWhere)->select();
   //     p($data);
        foreach($data as $k=>$v){
            $data[$k]['card_num']=" ".(string)$v['card_num']." ";
        }
        exportExcel($data,array('微信昵称','姓名','手机号码','职业','身份证号码',
        '现在居住地址','家庭居住地址','毕业学校','学历','在校时间','银行开户行','银行卡号','开户行城市'),'会员资料库');
    }
    /**
     * 导出信贷产品订单
     */
    public function excel_credit(){
        $_POST=$_REQUEST;
        $aWhere=$this->search($_POST);
        $aWhere['tp_no_credit_order.token'] =$this->_sToken;
        $aWhere['tp_no_credit_order.type']=2;
        /**
         * 条件改变
         */
        if(array_key_exists('add_time',$aWhere)){//时间
            $aWhere['tp_no_credit_order.add_time']=$aWhere['add_time'];
            $this->assign('start_time',date('Y-m-d H:i:s',$aWhere['tp_no_credit_order.add_time'][1][0]));
            $this->assign('end_time',date('Y-m-d H:i:s',$aWhere['tp_no_credit_order.add_time'][1][1]));
            unset($aWhere['add_time']);
        }
        if(array_key_exists('true_name',$aWhere)){
            $aWhere['tp_credit_users.true_name']=$aWhere['true_name'];
            unset($aWhere['true_name']);
        }


        if(array_key_exists('paystatus',$aWhere)){
            $aWhere['tp_no_credit_order.paystatus']=$aWhere['paystatus'];
            $this->assign('paystatus',$aWhere['paystatus'][1]);

            unset($aWhere['paystatus']);

        }
        if($aWhere['tp_no_credit_order.paystatus'][1]==10){//为了显示全部
            unset($aWhere['tp_no_credit_order.paystatus']);
        }
        $data=M('No_credit_order')->join("join tp_credit_users on tp_credit_users.id=tp_no_credit_order.uid")
            ->field("tp_no_credit_order.title,tp_credit_users.true_name,tp_credit_users.phone,tp_credit_users.bank_name,tp_credit_users.bank_card,
            tp_credit_users.bank_city,tp_credit_users.card_num,tp_no_credit_order.loan_total_money,tp_no_credit_order.fenqi,
            tp_no_credit_order.monthly_repayments,tp_no_credit_order.paystatus,tp_no_credit_order.add_time")
            ->where($aWhere)->select();

        foreach($data as $k=>$v){

            if($v['paystatus']==0){
                $data[$k]['paystatus']='正在审核中';
            }
            if($v['paystatus']==-1){
                $data[$k]['paystatus']='订单已取消';
            }
            if($v['paystatus']==1){
                $data[$k]['paystatus']='审核通过';
                //这里把余期搞出来
                $hs_time=M('No_credit_order')->where(array('id'=>$v['id']))->getField('hs_time');
                $end_time=date('Y-m-d',time());
                $start_time=date('Y-m-d',$hs_time);
                $qisu=getMonthNum($start_time,$end_time);
                for($i=1;$i<=$qisu;$i++){
                    if(!M('Hk_jl')->where(array('oid'=>$v['id'],'qisu'=>$i,'paystatus'=>1))->find()){//余期了
                        $data[$k]['paystatus']='审核通过有余期';
                        break;
                    }
                }
            }
            if($v['paystatus']==2){
                $data[$k]['paystatus']='审核未通过';
            }
            if($v['paystatus']==12){
                $data[$k]['paystatus']='已全额还款';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }

        exportExcel($data,array('产品名称','货款人姓名','手机号码','开户行','银行卡号','银行所在城市','身份证号码','货款总金额','分多少期还款','每期还款金额',
            '订单状态','下单时间'),'信贷订单');
    }
    //利率页面
    public function lilu_index(){
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
                'HeadHover' => U('Loan/lilu_index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '设置利率',
                        'url' => U('Loan/set_lilu',array('token'=>$this->token))
                    )
                ),
                'tips' => array(
                    '您说要注意什么呢！开心每一天'
                ),
                'Table_Header' => array(
                    '序号','期数', '利率','操作'
                ),
                'List_Opt' => array(
                  array(
                        'name' => '编辑',
                        'url' => U('Loan/edit_lilu',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('Loan/del_lilu')
                    )
                )

            ),
            D('Credit_lilu')->where($aWhere)->count(),
            D('Credit_lilu')->field('id,qisu,lilu')->where($aWhere)->order('qisu')
        );
        $this->UDisplay('show1');

    }
    //设置利率
    public function set_lilu(){
        $this->add('Credit_lilu',array(

            array('title'=>"期数",'type'=>"input",'name'=>"qisu",'msg'=>'期数必须写'),

            array('title'=>"利率",'type'=>"input",'name'=>"lilu",'msg'=>'利率必须填写！'),


        ),U('Loan/lilu_index',array('token',$this->token)));
    }
    //编辑利率
    public function edit_lilu(){
      //  echo 88;die;
        $this->Edit('Credit_lilu',array(

            array('title'=>"期数",'type'=>"input",'name'=>"qisu",'msg'=>'期数必须写'),

            array('title'=>"利率",'type'=>"input",'name'=>"lilu",'msg'=>'利率必须填写！'),


        ),U('Loan/lilu_index',array('token',$this->token)));
    }
    //删除利率
    public function del_lilu(){
        $this->del('Credit_lilu');
    }


}


/**
 * Class 这个类是做设置旅游时间有关的表
 */
class Calendar{
    protected $_table;//table表格
    protected $_currentDate;//当前日期
    protected $_year; //年
    protected $_month; //月
    protected $_days; //给定的月份应有的天数
    protected $_dayofweek;//给定月份的 1号 是星期几
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->_table="";
        $this->_year = isset($_GET["y"])?$_GET["y"]:date("Y");
        $this->_month = isset($_GET["m1"])?$_GET["m1"]:date("m");
        if ($this->_month>12){//处理出现月份大于12的情况
            $this->_month=1;
            $this->_year++;
        }
        if ($this->_month<1){//处理出现月份小于1的情况
            $this->_month=12;
            $this->_year--;
        }
        $yue=intval($this->_month);//把月分比如05变成5
        $this->_currentDate = '<span class="y">'.$this->_year.'</span>年<span class="m">'.$yue.'</span>月份';//当前得到的日期信息
        $this->_days = date("t",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份应有的天数
        $this->_dayofweek = date("w",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份的 1号 是星期几
    }
    /**
     * 输出标题和表头信息
     */
    protected function _showTitle()
    {
        $this->_table="<table style='width: 1000px;'><thead><tr align='center' ><th colspan='7' class='tou'>".$this->_currentDate."</th></tr ></thead>";
        $this->_table.="<tbody><tr >";
        $this->_table .="<td style='color:red'>星期日</td>";
        $this->_table .="<td>星期一</td>";
        $this->_table .="<td>星期二</td>";
        $this->_table .="<td>星期三</td>";
        $this->_table .="<td>星期四</td>";
        $this->_table .="<td>星期五</td>";
        $this->_table .="<td style='color:red'>星期六</td>";
        $this->_table.="</tr>";
    }
    /**
     * 输出日期信息
     * 根据当前日期输出日期信息
     */
    protected function _showDate($c='',$id='')
    {

        $shoufu=M('No_credit')->where(array('id'=>$id))->getField('shoufu');//得着付钱

        $yue=intval($this->_month);//把月分比如05变成5

        $y_m=$this->_year.'-'.$yue;
        /**
         * 重组数组，key=d,且不是这个月的去掉
         */
        //$k=0;
        $No_credit_order_model=M('No_credit_order');//订单表
        foreach($c as $k=>$v){
            if($v['y_m']==$y_m){
              //     $k++;
                $c['_'.$v[d]]=$v;
                $c['_'.$v[d]]['date_time']=$v['y_m'].'-'.$v['d'];
                $c['_'.$v[d]]['people_num']=$No_credit_order_model->where(array('cid'=>$v['cid'],'type'=>1,'paystatus'=>array('in',array(0,1,5,12)),'date_time'=> $c['_'.$v[d]]['date_time']))->getField('sum(people_num)');
                $c['_'.$v[d]]['people_num1']=$No_credit_order_model->where(array('cid'=>$v['cid'],'type'=>1,'paystatus'=>array('in',array(0,1,5,12)),'date_time'=> $c['_'.$v[d]]['date_time'],'people_num'=>3))->count();



                $c['_'.$v[d]]['yu_num']=$c['_'.$v[d]]['num']-($c['_'.$v[d]]['people_num']-$c['_'.$v[d]]['people_num1']);
                unset($c[$k]);
                unset($c['_'.$v[d]]['people_num1']);
            }else{
                unset($c[$k]);
            }
        }

        $nums=$this->_dayofweek+1;
        for ($i=1;$i<=$this->_dayofweek;$i++){//输出1号之前的空白日期
            $this->_table.="<td> </td>";
        }
        //foreach($c as $v) {
            for ($i = 1; $i <= $this->_days; $i++) {//输出天数信息
                if ($nums % 7 == 0) {//换行处理：7个一行

                        $this->_table .= "<td style='height:91px;' ";
                    if(array_key_exists("_".$i,$c)){
                        $this->_table .=" class='red' ";
                    }

                        $this->_table .="><p class='d'>$i</p>";

                     if(array_key_exists("_".$i,$c)){
                        $this->_table .="<p>￥$shoufu</p><p>共{$c['_'.$i]['num']}位,余{$c['_'.$i]['yu_num']}位</p>";
                      }
                    $this->_table .="<input style='display: none; height: 80px;width:90px;' type='text' name='num' class='num'>
                                <input type='button' class='set' value='设置'><input type='button' class='del' value='删除'></td></tr><tr >";

                } else {

                        $this->_table .= "<td style='height:91px;' ";
                    if(array_key_exists("_".$i,$c)){
                        $this->_table .=" class='red' ";
                    }

                         $this->_table .="><p class='d'>$i</p>";

                    if(array_key_exists("_".$i,$c)){
                    $this->_table .="<p>￥$shoufu</p><p>共{$c['_'.$i]['num']}位,余{$c['_'.$i]['yu_num']}位</p>";
                     }


                    $this->_table .="<input style='display: none;height: 80px;width:90px;' type='text' name='num' class='num'>
                                        <input type='button' class='set' value='设置'><input type='button' class='del' value='删除'></td>";


                }
                $nums++;
            }

        $this->_table.="</tbody></table>";
        //获取当前id
        $id=$_GET['id'];
        //这里拼接自己的url地址
        $this->_table.="<h3><a href='?g=User&m=Loan&a=set_time&id=".$id."&y=".($this->_year)."&m1=".($this->_month-1)."'>上一月</a>   ";
        $this->_table.="<a href='?g=User&m=Loan&a=set_time&id=".$id."&y=".($this->_year)."&m1=".($this->_month+1)."'>下一月</a></h3>";
    }
    /**
     * 输出日历
     */
    public function showCalendar($b='',$id='')
    {
        $this->_showTitle();
        $this->_showDate($b,$id);
        return $this->_table;
    }


}
?>
