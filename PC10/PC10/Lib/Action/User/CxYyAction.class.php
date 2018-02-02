<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class CxYyAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/together';

    /**
     *  Token
     **/
    //private $_sToken = null;

    /**
     *  UID
     **/
    //private $_iUID = null;//
    public function _initialize()
    {
        parent::_initialize();
        $this->pz	   = D('Cx_lc');
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '会员管理',
                'url'  => U('CxZc/index', array('token' => $this->_sToken))
            ),
        		
        		array(
        				'name' => '会议室',
        				'url'  => U('CxYy/index', array('token' => $this->_sToken))
        		),
        		
        		array(
        				'name' => '管理登陆识别码',
        				'url'  => U('CxZc/sbm', array('token' => $this->_sToken))
        		),
        

        );
    }
    //显示
    public function index(){
//P($aDate);   
        //$_SESSION['token']=$_SESSION['token'];
    	$aWhere['token']=$_SESSION['token'];
           	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$_SESSION['token'];
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
                }//结束 
        $this->table(
            array(
                
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('CxYy/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加会议室',//2级
                        'url'    => U('CxYy/add_content',array('token'=>$_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '楼层', '会议室','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('CxYy/xx',array('token'=>$_SESSION['token']))
                                            ), */
    	array(
    			'name' => '设置固定预约',
    			'url'  => U('CxYy/yy',array('token'=>$_SESSION['token']))
    	),
                		
                		array(
                				'name' => '前台预约人员信息',
                				'url'  => U('CxYy/xx',array('token'=>$_SESSION['token']))
                		),
                    array(
                        'name' => '编辑',
                        'url'  => U('CxYy/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('CxYy/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
            		
            		'search'=>array(
            				//  array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
            				// array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
            				//array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
            				array('title'=>'按楼层查询','name'=>'eq_lc','type'=>'select','many'=>array(
            						array('value'=>'1','name'=>'一楼'),
            						array('value'=>'2','name'=>'二楼'),
            						array('value'=>'3','name'=>'三楼'),
            						//array('value'=>'3','name'=>'旅游1'),
            				))
            		)
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Cx_lc')->where($aWhere)->count(),
            M('Cx_lc')->field('id,lc,name')->where($aWhere)->order("lc"),
         array($this,'abc')
        );
    }
    public function abc($data){
        foreach($data as $k=>$v){

            switch ($v['lc']){
                case 1:$data[$k]['lc']='一楼';break;
                case 2:$data[$k]['lc']='二楼';break;
                case 3:$data[$k]['lc']='三楼';break;
                case 4:$data[$k]['lc']='四楼';break;
            }


        }
        return $data;
    }
    //定义增改函数
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('CxYy2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
                } */
        $this->$aaa('Cx_lc',array(
            //array('title'=>"较长input框",'type'=>"longinput",'name'=>"cname",'value'=>'cname','msg'=>'请填写较长input框','tishi'=>'提示'),
            array('title'=>"会议室名称:",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写input框'),
            //array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd'),
            //	array('title'=>"隐藏",'type'=>"hidden",'name'=>"id",'msg'=>'请填写标题咯','value'=>'id'),
            //array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写开始时间','value'=>'k_time'),
            //	array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写结束时间','value'=>'j_time'),
            //	array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num','msg'=>'请填写数量',),

            //			array('type'=>'radio','title'=>"特殊点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
            		array('type'=>'select','title'=>"楼层",'name'=>"lc",'value'=>'lc','msg'=>'请选择下拉列表','many'=>array(
                            array('content'=>'请选择楼层'),
                            array('value'=>'1', 'content'=>'一楼'),
                            array('value'=>'2','content'=>'二楼'),
                            array('value'=>'3','content'=>'三楼'),
                            array('value'=>'4','content'=>'四楼'),
                    )),
            //	array('type'=>'select','title'=>"特殊下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
/*             array('type'=>'checkbox','title'=>'复选框','name'=>"sex3",'value'=>'sex3','msg'=>'请选择复选框','many'=>array(
                array('value'=>'1','content'=>'蓝'),
                array('value'=>'2','content'=>'红'),
            )), */


/*             array('type'=>'radio','title'=>"楼层",'name'=>"lc",'value'=>'lc','msg'=>'请填写点选框','many'=>array(
                array('value'=>'1','content'=>'一楼'),
                array('value'=>'2','content'=>'二楼'),
            	array('value'=>'3','content'=>'三楼'),
            	array('value'=>'4','content'=>'四楼'),
            )), */  //	array('type'=>'checkbox','title'=>'特殊复选框', 'name'=>'jy', 'value'=>'jy','msg'=>'请填写标题咯','many'=>$list),
           /*  array('type'=>'img','many'=>array(
                array('title'=>"图片1",'type'=>"img",'name'=>"pic",'value'=>'pic','width'=>50,'height'=>50),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic2",'value'=>'pic2','width'=>50,'height'=>50),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic3",'value'=>'pic3','width'=>50,'height'=>50)
            )),
            array('title'=>"小的复文本框",'type'=>"textarea2",'name'=>"content2",'value'=>'content2'),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
            array('title'=>"经纬度",'type'=>"map",'name'=>'name2', 'lng'=>"position_x",'lat'=>'position_y'), */
        ),U('CxYy/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
        $data['password']=MD5($data['password']);
        $data['sex']=1;
        return $data;
    }
    //添加
    public function add_content(){
        $this->add_save(add);
    }
    //编辑
    public function save_content(){
        $this->add_save(Edit);
    }
    //删除
    public function delete_content(){
        M('cx_yd2')->where(array('pid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Cx_lc');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable('Cx_lc');
    }
    //是否显示 只 能主健是id,子段是is_show 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable('Cx_lc');
    }
   



    
    
    
    
    
    
    
    
    
    
    //其它信息开头
    public function xx(){
    	$aWhere['token']=$_SESSION['token'];
    	$aWhere['pid']=$_GET['id'];
 /*    	$aWhere['x_time']=array('gt',time()); */
    	//组合时间
    	$a = -86400;//因为for有个$a+=86400; 为了取当前选定时间-86400
    	for ($i=0; $i<15; $i++){
    		$a+=86400;
    		$aDate[] = date('Y-m-d',time()+$a);
    	} //取得当前选定时间，当前选定后一天时间
    	foreach ($aDate as $ke=>$v){
    		$list[$ke]['value']=strtotime($v);
    		$list[$ke]['name']=$v;
    	}
    
/*     	array(
    			array('value'=>'1434211200','name'=>'1111'),
    			array('value'=>'2','name'=>'女'),
    			//array('value'=>'3','name'=>'旅游1'),
    	)) */
    	//排序
    	if($_GET['sortInt']==1){
    		$_GET['sortInt']=0;
    		$_GET['sort']=$_GET['sort']." ".desc;
    	}elseif($_GET['sortInt']==0){
    		$_GET['sortInt']=1;
    	}
    	if($_GET['sort']){
    		$sort=$_GET['sort'];
    	}else{
    		unset($_SESSION['aWhere']);
    		//$sort="sort";
    	}//排序
    	//搜索
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
    		$aWhere['token'] =$_SESSION['token'];
    		$aWhere['pid']=$_GET['id'];
/*     		$aWhere['x_time']=array('gt',time()); */
    		//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    		$_SESSION['aWhere']=$aWhere;//排序
    	}//结束
    	
     /*    P($aWhere); */
    	if($_SESSION['aWhere'])$aWhere=$_SESSION['aWhere'];//排序
    	
    	
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('CxYy/index', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '返回',//2级
    									'url'    => U('CxYy/index',array('uid'=>$_GET['id']))
    							)
    					),
    					'tips' => array(//3级
    							'你可以在这里管理信息'
    					),
    					'Table_Header' => array(//4级
    							'ID|id', '楼层|lc', '会议室|name', '部门-姓名|name', '主题|type', '预约时间|x_time', '时间段|j_time', '操作'
    					),
    					'List_Opt' => array(
    
    
    
    			/* 				array(
    									'name' => '查看',
    									'url'  => U('CxYy/save_xx',array('token'=>$_SESSION['token']))
    							), */
    							array(
    									'name' => '删除',
    									'url'  => U('CxYy/delete_xx',array('token'=>$_SESSION['token']))
    							),
    					),
    					
    					'search'=>array(
    							//  array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
    							// array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
    							//array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
    							array('title'=>'按日期查询','name'=>'eq_p_time','type'=>'select','many'=>$list)
    					)
    					
    
    			),
    			M('cx_yd2')->where($aWhere)->count(),
    			M('cx_yd2')->field('id,lc,name,zhi,type,x_time,j_time')->order($sort)->where($aWhere),
    			array($this,'xxabc')
    	);
    }
    public function xxabc($data){
    	foreach($data as $k=>$v){
    		$b=M('cx_member')->where(array('id'=>$v['zhi']))->find();
    		$data[$k]['zhi']=$b['bm']."-".$b['name'];
    		$data[$k]['x_time']=date('Y-m-d',$v['x_time']);
    		
    		switch ($v['j_time']){
    			case 1:$data[$k]['j_time']='8:30-9:00';break;
    			case 2:$data[$k]['j_time']='9:00-9:30';break;
    			case 3:$data[$k]['j_time']='9:30-10:00';break;
    			case 4:$data[$k]['j_time']='10:00-10:30';break;
    			case 5:$data[$k]['j_time']='10:30-11:00';break;
    			case 6:$data[$k]['j_time']='11:00-11:30';break;
    			case 7:$data[$k]['j_time']='11:30-12:00';break;
    			case 8:$data[$k]['j_time']='12:00-12:30';break;
    			case 9:$data[$k]['j_time']='12:30-13:00';break;
    			case 10:$data[$k]['j_time']='13:00-13:30';break;
    			case 11:$data[$k]['j_time']='13:30-14:00';break;
    			case 12:$data[$k]['j_time']='14:00-14:30';break;
    			case 13:$data[$k]['j_time']='14:30-15:00';break;
    			case 14:$data[$k]['j_time']='15:00-15:30';break;
    			case 15:$data[$k]['j_time']='15:30-16:00';break;
    			case 16:$data[$k]['j_time']='16:00-16:30';break;
    			case 17:$data[$k]['j_time']='16:30-17:00';break;
    			case 18:$data[$k]['j_time']='17:00-17:30';break;
    			case 19:$data[$k]['j_time']='17:30-18:00';break;
    			case 20:$data[$k]['j_time']='18:00-18:30';break;
    			case 21:$data[$k]['j_time']='18:30-19:00';break;
    			case 22:$data[$k]['j_time']='19:00-19:30';break;
    			case 23:$data[$k]['j_time']='19:30-20:00';break;
    			case 24:$data[$k]['j_time']='20:00-20:30';break;
    			case 25:$data[$k]['j_time']='20:30-21:00';break;
    		}
    		
    
    	}
    	return $data;
    }
    //定义增改函数
    public function add_save_xx($aaa){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('CsCs2')->select();//被添加内容的表
    	 foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('common_cs',array(
    			/* 	array('title'=>"较长input框",'type'=>"longinput",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'), */
    			array('title'=>"input框",'type'=>"input",'name'=>"pinzhong",'value'=>'pinzhong','tishi'=>'链接:http://v.wapwei.co'),
    			array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd'),
    			array('title'=>"隐藏",'type'=>"hidden",'name'=>"id",'msg'=>'请填写标题咯','value'=>'id'),
    			array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
    			array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
    			array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
    			array('type'=>'radio','title'=>"点选框1",'name'=>"sex",'value'=>'sex','msg'=>'请选择点选框','many'=>array(
    					array('value'=>'1','content'=>'男'),
    					array('value'=>'2','content'=>'女'),
    			)),
    			//	array('type'=>'radio','title'=>"特殊点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
    			array('type'=>'select','title'=>"下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
    					array('value'=>'1', 'content'=>'一年以上'),
    					array('value'=>'2','content'=>'二年以上'),
    					array('value'=>'3','content'=>'三年以上'),
    					array('value'=>'4','content'=>'不限'),
    			)),
    			//		array('type'=>'select','title'=>"特殊下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
    			array('type'=>'checkbox','title'=>'复选框','name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
    					array('value'=>'1','content'=>'蓝'),
    					array('value'=>'2','content'=>'红'),
    			)),
    			//		array('type'=>'checkbox','title'=>'特殊复选框', 'name'=>'jy', 'value'=>'jy','msg'=>'请填写标题咯','many'=>$list),
    			array('type'=>'img','many'=>array(
    					array('title'=>"图片1",'type'=>"img",'name'=>"pic",'value'=>'pic','whidth'=>50,'height'=>50),
    					array('title'=>"图片2",'type'=>"img",'name'=>"pic2",'value'=>'pic','whidth'=>50,'height'=>50),
    					array('title'=>"图片2",'type'=>"img",'name'=>"pic3",'value'=>'pic','whidth'=>50,'height'=>50)
    			)),
    			array('title'=>"小的复文本框",'type'=>"textarea2",'name'=>"hd_time",'value'=>'hd_time'),
    			array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
    			array('title'=>"经纬度",'type'=>"map",'name'=>'name', 'lng'=>"position_x",'lat'=>'position_y'),
    	),U('CsCs/xx',array('token'=>$_SESSION['token'],'id'=>$_GET['uid'])),array($this,'bbc'),1);
    
    }
    public function save_xx(){
    	$this->add_save_xx(Edit);
    }
    public function delete_xx(){
    	$this->del('common_cs');
    }
    //其它信息结束
    
    
    
    
    
    
    
    
    
    
    

    

//其它信息开头
   public function yy(){
   	$pid=$this->_get('id');
  	$list=M('cx_yd')->where(array('token'=>$_SESSION['token'],'pid'=>$_GET['id']))->field('time,zhi')->select();//查出特殊表time，zhi
   	$list = Arr::changeIndexToKVMap($list, 'time', 'zhi');//将2维数组转为1维数组, 2维数组里的time做为1维数组里的键, 2维数组里的zhi做为1维数组里的值,
    
   	$this->assign('pid',$pid);
   	$this->assign('list',$list);
  	for ($i=0; $i<7; $i++){//取得一周的时间  days为一天的单位, 不写默认有time()   strtotime('+'.$i.' days')==strtotime(time().'+'.$i.' days')
   		$aDate[] = date('Y-m-d', strtotime('+'.$i.' days'));
   		//$wDate[] = date('w', strtotime('+'.$i.' days'));
   	}
   	$aTime = range(9, 20);//建立一个包含指定范围的1维数组,数组里的值9,10,11.....20
   	 
   	$this->assign('dates', $aDate);
   	$this->assign('atime', $aTime);
   	//清理数据
   	$time=time();
   	
   	M('cx_yd2')->query("delete from tp_cx_yd2 where $time>add_time+43200");
   	$this->display();
   }
    
   
   public function yd(){
   	if(IS_POST){
   		if(!$_POST['zhi']){
   		  M('cx_yd')->where(get2(token,$_SESSION['token'],pid,$_GET['id'],time))->delete();	
   		  script("预定删除成功","yy",get(time,id));
   		}
   		if(M('cx_yd')->where(get2(token,$_SESSION['token'],pid,$_GET['id'],time))->find()){
   			//查出楼层与会一室名
   			$list=M('cx_lc')->where(get(id))->find();
   			M('cx_yd')->where(get2(token,$_SESSION['token'],pid,$_GET['id'],time))->save(array('time'=>$_GET['time'],'zhi'=>$_POST['zhi'],'add_time'=>time(),'pid'=>$_GET['id'],'token'=>$_SESSION['token'],'lc'=>$list['lc'],'name'=>$list['name']));
   			script("预定修改成功","yy",get(id));
   		}
   		//查出楼层与会一室名
   		$list=M('cx_lc')->where(get(id))->find();
   		if(M('cx_yd')->add(array('time'=>$_GET['time'],'zhi'=>$_POST['zhi'],'add_time'=>time(),'pid'=>$_GET['id'],'token'=>$_SESSION['token'],'lc'=>$list['lc'],'name'=>$list['name']))){
   			script("预定成功","yy",get(id));
   		}
   		
   	}else{
   		$list = M('cx_member')->select();
   		$this->assign('list', $list);
   		//默认选中
   	    $this->assign('zhi',M('cx_yd2')->where(get2(pid,$_GET['id'],token,$_SESSION['token'],time))->getField('zhi'));
   		$this->display();
   	}

   }
//其它信息结束

}

