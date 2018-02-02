<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 *  张湘南
 **/
class XyxxAction extends TableAction {
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
        $this->pz	   = D('Context_shop');
	 }
    //一级
    protected function setHeader(){
    	return array(
                array(
                    'name' => '课程管理',
                    'url'  => U('Course/index', array('token' =>$_SESSION['token']))
                ),
    			array(
    					'name' => '报名学员管理',
    					'url'  => U('Xyxx/index', array('token' =>$_SESSION['token']))
    			),
    			 
    			

            );
    }
    //显示
 	public function index(){
 	
 		//$_SESSION['token']=$_SESSION['token'];
/*     	//搜索
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
    		$aWhere['token'] =$_SESSION['token'];
    		//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    	}//结束 */
 		
 		//搜索
 		unset($_SESSION['aWhere']);
 		if(IS_POST){
 			$_POST=$_REQUEST;
 			$aWhere=$this->search($_POST);
 		
 			//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
 			$_SESSION['aWhere']=$aWhere;//排序
 		}//结束
 		
 		//P($aWhere);
		$this->table(
        	array(
                //'abc'=>123,
            	//'kid' => 'typeid',//如果主键不是id，则需要设置
            	'HeadHover' => U('Xyxx/index', array('token' => $this->_sToken)),//栏目样式 
            	'Head_Opt' => array(
    /*             	array(
                    	'name'   => '添加',//2级
                    	'url'    => U('Xyxx/add_content',array('token'=>$_SESSION['token']))
                	) */
            			
            			
            			array(
            					'name'   => '导出数据',
            					'url'    => U('ExportExcel/Xyxx')
            			),
            			
            			
            	),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
            	'Table_Header' => array(//4级
                	'ID', '课程名称', '姓名', '性别', '类型', '费用', '联系电话','操作'
            	),
            	'List_Opt' => array(

/*               			array(
            					'name' => '其它信息',
              					'url'  => U('Xyxx/xx',array('token'=>$_SESSION['token']))
            			), */
            			
                	array(
                    	'name' => '查看详细',
                    	'url'  => U('Xyxx/save_content',array('token'=>$_SESSION['token'],'deleteToken'=>1))
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('Xyxx/delete_content',array('token'=>$_SESSION['token'],'deleteToken'=>1))
	                ),
            	),
        			
        			'search'=>array(
        					//  array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
        					// array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
        					//array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
        					array('title'=>'分类','name'=>'eq_type2','type'=>'select','many'=>array(
        				/* 			array('value'=>'','name'=>'全部'), */
        							array('value'=>'十公里','name'=>'十公里'),
        							array('value'=>'半程','name'=>'半程'),
        							array('value'=>'全程','name'=>'全程'),
        							
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
        	M('Context_shop')->where($aWhere)->count(),
        	M('Context_shop')->field('id,cid,name,sex,type2,money,tel')->where($aWhere)->order("id desc"),
           array($this,'abc')
         );
	}
 	public function abc($data){
		foreach($data as $k=>$v){
	
			//sql语句
			$data[$k]['cid']=M('Context_list')->where(array('id'=>$v['cid']))->getField('title');
			//外加子段

			
		}
		return $data;
	}
	//定义增改函数
	public function add_save($aaa){

		$this->$aaa('Context_shop',array(
				array('title'=>"姓名",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写input框1'),
				array('title'=>"性别",'type'=>"input",'name'=>"sex",'value'=>'sex','msg'=>'请填写input框1'),
				
				array('title'=>"出生日期 ",'type'=>"input",'name'=>"age",'value'=>'age','msg'=>'请填写input框1'),
				array('title'=>"曾参加过马拉松赛事名称及组别",'type'=>"input",'name'=>"mc",'value'=>'mc','msg'=>'请填写input框1'),
				array('title'=>"以往马拉松成绩",'type'=>"input",'name'=>"cj",'value'=>'cj','msg'=>'请填写input框1'),
				array('title'=>"是否有赛事举办地户籍或居住证",'type'=>"input",'name'=>"jjz",'value'=>'jjz','msg'=>'请填写input框1'),
				
				
				array('title'=>"国籍",'type'=>"input",'name'=>"gj",'value'=>'gj','msg'=>'请填写input框1'),
				array('title'=>"所居城市",'type'=>"input",'name'=>"address",'value'=>'address','msg'=>'请填写input框1'),
				array('title'=>"证件类型",'type'=>"input",'name'=>"type",'value'=>'type','msg'=>'请填写input框1'),
				array('title'=>"证件号码",'type'=>"input",'name'=>"hm",'value'=>'hm','msg'=>'请填写input框1'),
				array('title'=>"血型",'type'=>"input",'name'=>"xx",'value'=>'xx','msg'=>'请填写input框1'),
				array('title'=>"电子邮箱地址",'type'=>"input",'name'=>"email",'value'=>'email','msg'=>'请填写input框1'),
				array('title'=>"联系电话",'type'=>"input",'name'=>"tel",'value'=>'tel','msg'=>'请填写input框1'),
				array('title'=>"紧急联系人",'type'=>"input",'name'=>"jname",'value'=>'jname','msg'=>'请填写input框1'),
				array('title'=>"紧急联系人电话",'type'=>"input",'name'=>"jtel",'value'=>'jtel','msg'=>'请填写input框1'),
				array('title'=>"紧急联系人关系",'type'=>"input",'name'=>"gx",'value'=>'gx','msg'=>'请填写input框1'),
				array('title'=>"衣服尺码",'type'=>"input",'name'=>"cm",'value'=>'cm','msg'=>'请填写input框1'),
				array('title'=>"类型",'type'=>"input",'name'=>"type2",'value'=>'type2','msg'=>'请填写input框1'),
				array('title'=>"费用",'type'=>"input",'name'=>"money",'value'=>'money','msg'=>'请填写input框1'),
				
		
		),U('Xyxx/index',array('token'=>$_SESSION['token'])),array($this,'bbc'),1);
		
	}
	public function bbc($data){
		$data['password']=MD5($data['password']);
		$data['add_time']=time();
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
    	//M('Context_shop')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Context_shop');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见自己开发！
    public function sortajax(){
    	$this->sortajaTable('Context_shop');
    }
    //是否显示 只 能主健是id,子段是is_show 才能是否显示  有意见自己开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable('Context_shop');
    }
    
    
    
//其它信息开头
    public function xx(){
		$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('Xyxx/index', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '返回',//2级
    									'url'    => U('Xyxx/index',array('uid'=>$_GET['id']))
    							)
    					),
    					'tips' => array(//3级
    							'你可以在这里管理信息'
    					),
    					'Table_Header' => array(//4级
    							'ID', '标题', '标题', '标题', '标题', '标题', '标题', '标题', '标题', '标题', '标题', '标题','操作'
    					),
    					'List_Opt' => array(
    

    							 
    							array(
    									'name' => '查看',
    									'url'  => U('Xyxx/save_xx',array('token'=>$_SESSION['token']))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('Xyxx/delete_xx',array('token'=>$_SESSION['token']))
    							),
    					),

    			),
    			M('Context_shop')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id']))->count(),
    			M('Context_shop')->field('id')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id'])),
    			array($this,'xxabc')
    	);
    }
     public function xxabc($data){
    	foreach($data as $k=>$v){
    		$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
    		$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
    		$data[$k]['content']=substr(htmlspecialchars_decode($v['content']),0,78);
    		$data[$k]['state']="{$v['state']}"?'活动已开启':'活动关闭';
    		switch ($v['sex']){
    			case 1:$data[$k]['sex']='男';break;
    			case 2:$data[$k]['sex']='女';break;
    			case 3:$data[$k]['sex']='不限';break;
    		}
    		//sql语句
    		$data[$k]['uid']=M('mru_jf')->where(array('id'=>$v['uid']))->getField('name');
    		//特殊复选框显示，先把保存在$v['mdiao']里的id偏历出来，然后用表的id=$v['mdiao']保存的id 把名字查询出来值给临时变量
    		foreach (explode(',',$v['mdiao']) as $ke=>$s){//字符转数组遍历
    			$str=M('mru_mdian')->where(array('id'=>$s))->field('name')->find();
    			$str=implode(',', $str);//$str是个一维数组，转成字符串
    			$data[$k]['aaa'].=$str.',';//用,拼结起来，值给临时变量
    		}
    		$data[$k]['mdiao']=$data[$k]['aaa'];//临时变量的值给正常子段赋值，改变正常子段
    		$data[$k]['mdiao']=rtrim($data[$k]['mdiao'],',');//去右边,
    		unset($data[$k]['aaa']);//删除临时变量   结束处!
    			
    	}
    	return $data;
    }
    //定义增改函数
    public function add_save_xx($aaa){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('Xyxx2')->select();//被添加内容的表
    		foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('Context_shop',array(
    		/* 	array('title'=>"较长input框",'type'=>"longinput",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'), */
    			array('title'=>"input框",'type'=>"input",'name'=>"name",'value'=>'name'),
    			array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd','msg'=>'请填写标题咯'),
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
    	),U('Xyxx/xx',array('token'=>$_SESSION['token'],'id'=>$_GET['uid'])),array($this,'bbc'),1);
    
    }
    public function save_xx(){
    	$this->add_save_xx(Edit);
    }
    public function delete_xx(){
    	$this->del('Context_shop');
    }
//其它信息结束
    
}
?>
