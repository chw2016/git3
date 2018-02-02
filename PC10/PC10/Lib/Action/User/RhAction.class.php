<?php
/**
 *  万普分期系统
 **/
class RhAction extends Table1Action {

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
                    'name' => '免费量房预约订单',
                    'url'  => U('index', array('token' => $this->_sToken))
                ),
            array(
                'name' => '门店列表',
                'url'  => U('store', array('token' => $this->_sToken))
            ),
            array(
                'name' => '样板间列表',
                'url'  => U('houses', array('token' => $this->_sToken))
            ),
            array(
                'name' => '样板间预约单',
                'url'  => U('store_index', array('token' => $this->_sToken))
            ),
            );
    }


    /**
     *  预约列表
     **/
	public function index(){
            $aWhere['token'] =$this->_sToken;
             $aWhere['type']=0;
            if(IS_POST){
                foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                    if(substr($k,0,2)=='be'&&$v){
                        $_POST[$k]=$v;
                    }
                }
                $aWhere = $this->search($_POST);

                $aWhere['token'] = $this->_sToken;
                $aWhere['type']=0;
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
                    'HeadHover' => U('index', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                      /*  array(
                            'name' => '添加非信贷产品',
                            'url' => U('Loan/add_pinzhong',array('token'=>$this->token))
                        ),
                      */

                    ),
                    'tips' => array(
                        '预约列表'
                    ),
                    'Table_Header' => array(
                        'ID', '姓名','手机号码','省', '市','状态','预约时间'
                    ),
                    'List_Opt' => array(
                       /* array(
                            'name' => '处理',
                            'url' => U('')
                        ),*/

                    ),
                  'search'=>array(
                          array('title'=>'姓名','name'=>'li_name'),
                          array('title'=>'订单状态','name'=>'eq_status','type'=>'select','many'=>array(
                          array('value'=>'0','name'=>'新预约'),
                          array('value'=>'1','name'=>'已处理'),
                      )),
                      array('type'=>'br'),
                        array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                    )
                ),

                M('Rh_yuyue')->where($aWhere)->count(),
                M('Rh_yuyue')->field('id,name,phone,location_p,location_c,status,add_time')->where($aWhere),
                array($this,'abc1')

            );
        $this->UDisplay('show1');
	}

    /**
     *  楼盘预约列表
     **/
    public function store_index(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['type']=1;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            $aWhere['type']=1;
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
                'HeadHover' => U('store_index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    /*  array(
                          'name' => '添加非信贷产品',
                          'url' => U('Loan/add_pinzhong',array('token'=>$this->token))
                      ),
                    */

                ),
                'tips' => array(
                    '预约列表'
                ),
                'Table_Header' => array(
                    'ID', '姓名','手机号码','预约楼盘','省', '市','状态','预约时间'
                ),
                'List_Opt' => array(
                    /* array(
                         'name' => '处理',
                         'url' => U('')
                     ),*/

                ),
                'search'=>array(
                    array('title'=>'姓名','name'=>'li_name'),
                    array('title'=>'楼盘名称','name'=>'li_store_name'),
                    array('title'=>'订单状态','name'=>'eq_status','type'=>'select','many'=>array(
                        array('value'=>'0','name'=>'新预约'),
                        array('value'=>'1','name'=>'已处理'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                )
            ),

            M('Rh_yuyue')->where($aWhere)->count(),
            M('Rh_yuyue')->field('id,name,phone,store_name,location_p,location_c,status,add_time')->where($aWhere),
            array($this,'abc1')

        );
        $this->UDisplay('show1');
    }


    public function abc1($data){
        foreach($data as $k=>$v){
            if($v['status']==0){
                $data[$k]['status']="<a href='".U('chuli',array('id'=>$v['id']))."'>新预约</a>";
            }
            if($v['status']==1){
                $data[$k]['status']='已处理';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    //出理预约
    public function chuli(){
        $status=M('Rh_yuyue')->where(array('id'=>$_GET['id']))->getField('status');
        if($status==0){
            if(M('Rh_yuyue')->where(array('id'=>$_GET['id']))->save(array('status'=>1))){
                $this->success2('订单状态改变成功');
            }else{
                $this->error2('操作失败');
            }
        }
    }



    /**
     *  门店列表
     **/
    public function store(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['type'] =1;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
            $aWhere['type'] =1;

            session('where_p',null);
            session('where_p',$aWhere);
          //  p($aWhere);die;
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
                'HeadHover' => U('store', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加门店',
                        'url' => U('add_store',array('token'=>$this->_sToken))
                    )
                ),
                'tips' => array(
                    '门店列表'
                ),
                'Table_Header' => array(
                    'ID', '名店名称','图片', '市', '操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '平面图',
                        'url' => U('store_pic')
                    ),
                    array(
                        'name' => '编辑',
                        'url' => U('edit_store')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_store')
                    ),
                ),
                 'search'=>array(
                       array('title'=>'名称','name'=>'li_name'),

                   )
            ),

            M('Rh_store')->where($aWhere)->count(),
            M('Rh_store')->field('id,name,logo,location_c')->where($aWhere),
            array($this,'store1')

        );
        $this->UDisplay('show1');

    }
    public function store1($data){
        foreach($data as $k=>$v){
            $data[$k]['logo']="<img src='".$v['logo']."' width='100' />";
        }
        return $data;
    }
    /**
     * 门店添加
     */
    public function add_store(){
        $this->add('Rh_store',array(
            array('title'=>"门店名称",'type'=>"input",'name'=>"name",'msg'=>'门店名称不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图片",'name'=>"logo")
            )),
            array('title'=>"门店电话号码",'type'=>"input",'name'=>"tel",'msg'=>'门店电话号码必填咯！'),
            array('title'=>"门店地址选择",'type'=>"address"),
            array('title'=>"门店详细地址",'type'=>"input",'name'=>"address",'msg'=>'门店详细地址必须填写'),
            array('title'=>"门店地图位置",'type'=>"map1",'lng'=>"lng",'lat'=>'lat'),
            array('type'=>"hidden_true",'name'=>"type",'value'=>'1'),
        ),U('store',array('token'=>$this->token)));
    }
    /**
     * 门店编辑
     */
    public function edit_store(){
        $this->Edit('Rh_store',array(
            array('title'=>"门店名称",'type'=>"input",'name'=>"name",'msg'=>'门店名称不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图片",'name'=>"logo")
            )),
            array('title'=>"门店电话号码",'type'=>"input",'name'=>"tel",'msg'=>'门店电话号码必填咯！'),
            array('title'=>"门店地址选择",'type'=>"address"),
            array('title'=>"门店详细地址",'type'=>"input",'name'=>"address",'msg'=>'门店详细地址必须填写'),
            array('title'=>"门店地图位置",'type'=>"map1",'lng'=>"lng",'lat'=>'lat'),
            array('type'=>"hidden_true",'name'=>"type",'value'=>'1'),
        ),U('store',array('token'=>$this->token)));
    }

    public function houses(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['type'] =2;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
            $aWhere['type'] =2;

            session('where_p',null);
            session('where_p',$aWhere);
            //  p($aWhere);die;
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
                'HeadHover' => U('houses', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加楼盘',
                        'url' => U('add_houses',array('token'=>$this->_sToken))
                    )
                ),
                'tips' => array(
                    '样板间列表'
                ),
                'Table_Header' => array(
                    'ID', '名店名称','图片', '市', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '平面图',
                        'url' => U('houses_pic')
                    ),
                    array(
                        'name' => '编辑',
                        'url' => U('edit_houses')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_store')
                    ),
                ),
                'search'=>array(
                    array('title'=>'名称','name'=>'li_name'),

                )
            ),

            M('Rh_store')->where($aWhere)->count(),
            M('Rh_store')->field('id,name,logo,location_c')->where($aWhere),
            array($this,'store1')

        );
        $this->UDisplay('show1');

    }

    /**
     * 门店添加
     */
    public function add_houses(){
        $this->add('Rh_store',array(
            array('title'=>"楼盘名称",'type'=>"input",'name'=>"name",'msg'=>'楼盘名称不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图片",'name'=>"logo")
            )),
            array('title'=>"楼盘电话号码",'type'=>"input",'name'=>"tel",'msg'=>'楼盘电话号码必填咯！'),
            array('title'=>"楼盘地址选择",'type'=>"address"),
            array('title'=>"楼盘详细地址",'type'=>"input",'name'=>"address",'msg'=>'楼盘详细地址必须填写'),
            array('title'=>"楼盘地图位置",'type'=>"map1",'lng'=>"lng",'lat'=>'lat'),
            array('title'=>"样版间三维全景连接图",'type'=>"input",'name'=>"yangb"),
            array('title'=>"产品清单",'type'=>"textarea",'name'=>'info'),
            array('type'=>"hidden_true",'name'=>"type",'value'=>'2'),
        ),U('houses',array('token'=>$this->token)));
    }
    /**
     * 门店编辑
     */
    public function edit_houses(){
        $this->Edit('Rh_store',array(
            array('title'=>"楼盘名称",'type'=>"input",'name'=>"name",'msg'=>'楼盘名称不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图片",'name'=>"logo")
            )),
            array('title'=>"楼盘电话号码",'type'=>"input",'name'=>"tel",'msg'=>'楼盘电话号码必填咯！'),
            array('title'=>"楼盘地址选择",'type'=>"address"),
            array('title'=>"楼盘详细地址",'type'=>"input",'name'=>"address",'msg'=>'楼盘详细地址必须填写'),
            array('title'=>"楼盘地图位置",'type'=>"map1",'lng'=>"lng",'lat'=>'lat'),
            array('title'=>"样版间三维全景连接图",'type'=>"input",'name'=>"yangb"),
            array('title'=>"产品清单",'type'=>"textarea",'name'=>'info'),
            array('type'=>"hidden_true",'name'=>"type",'value'=>'2'),
        ),U('houses',array('token'=>$this->token)));
    }
    /**
     *  删除门店
     **/
    public function del_store(){
        $this->del('Rh_store');
    }
    //门店平面图
    public function store_pic(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['pid'] =$_GET['id'];
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
            $aWhere['type'] =1;

            session('where_p',null);
            session('where_p',$aWhere);
            //  p($aWhere);die;
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
                'HeadHover' => U('store', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加平面图',
                        'url' => U('add_pic',array('token'=>$this->_sToken,'pid'=>$_GET['id']))
                    ),
                     array(
                         'name' => '返回门店列表',
                         'url' => U('store',array('token'=>$this->_sToken))
                     )
                ),
                'tips' => array(
                    '这里是 ['.M('Rh_store')->where(array('id'=>$_GET['id']))->getField('name')."] 平面图列表"
                ),
                'Table_Header' => array(
                    'ID','图片', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('edit_pic',array('token'=>$this->token,'pid'=>$_GET['id']))
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_pic')
                    ),
                ),

            ),

            M('Gta_imgs')->where($aWhere)->count(),
            M('Gta_imgs')->field('id,img')->where($aWhere),
            array($this,'store_pic1')

        );
        $this->UDisplay('show1');

    }
    public function store_pic1($data){
        foreach($data as $k=>$v){
            $data[$k]['img']="<img src='".$v['img']."' width='150' />";
        }
        return $data;
    }

    //添加平面图
    public function add_pic(){
        $this->add('Gta_imgs',array(

            array('type'=>'img','many'=>array(
                array('title'=>"图片",'name'=>"img")
            )),

            array('type'=>"hidden_true",'name'=>"pid",'value'=>$_GET['pid']),
        ),U('store_pic',array('token'=>$this->token,'id'=>$_GET['pid'])));
    }
    public function edit_pic(){
        $this->Edit('Gta_imgs',array(

            array('type'=>'img','many'=>array(
                array('title'=>"图片",'name'=>"img")
            )),


        ),U('store_pic',array('token'=>$this->token,'id'=>$_GET['pid'])));
    }
    public function del_pic(){
        $this->del('Gta_imgs');
    }

    public function houses_pic(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['pid'] =$_GET['id'];
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
            $aWhere['type'] =2;

            session('where_p',null);
            session('where_p',$aWhere);
            //  p($aWhere);die;
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
                'HeadHover' => U('houses', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加平面图',
                        'url' => U('houses_add_pic',array('token'=>$this->_sToken,'pid'=>$_GET['id']))
                    ),
                    array(
                        'name' => '返回楼盘列表',
                        'url' => U('houses',array('token'=>$this->_sToken))
                    )
                ),
                'tips' => array(
                    '这里是 ['.M('Rh_store')->where(array('id'=>$_GET['id']))->getField('name')."] 平面图列表"
                ),
                'Table_Header' => array(
                    'ID','图片', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('houses_edit_pic',array('token'=>$this->token,'pid'=>$_GET['id']))
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('houses_del_pic')
                    ),
                ),

            ),

            M('Gta_imgs')->where($aWhere)->count(),
            M('Gta_imgs')->field('id,img')->where($aWhere),
            array($this,'store_pic1')

        );
        $this->UDisplay('show1');

    }
    public function houses_add_pic(){
        $this->add('Gta_imgs',array(

            array('type'=>'img','many'=>array(
                array('title'=>"图片",'name'=>"img")
            )),

            array('type'=>"hidden_true",'name'=>"pid",'value'=>$_GET['pid']),
        ),U('houses_pic',array('token'=>$this->token,'id'=>$_GET['pid'])));
    }
    public function houses_edit_pic(){
        $this->Edit('Gta_imgs',array(

            array('type'=>'img','many'=>array(
                array('title'=>"图片",'name'=>"img")
            )),


        ),U('houses_pic',array('token'=>$this->token,'id'=>$_GET['pid'])));
    }
    public function houses_del_pic(){
        $this->del('Gta_imgs');
    }




}
?>
