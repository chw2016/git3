<?php
/**
 *  技术支持：张湘南
 *  添加栏目新功能步骤:1.先在add_save方法选择栏目功能下添加记录 
 *                    2.在CmsZlm下add_save方法添加过记录
 *                    3.在CmsZlm下index处在两个"在这下面外加"添加2条记录
 *  添加内容新功能步骤:1.先在add_save方法选择栏目功能下添加记录   
 *                    2.在CmsZlm下add_save方法添加过记录
 **/
class CmsCmsAction extends CmsAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/cms';

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
        $this->pz	   = D('Cms_lm');
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '自定义开发',
                'url'  => U('CmsCms/index', array('token' => $this->_sToken))
            ),
        		

        );
    }
    //显示
    public function index(){
    	$_SESSION['lmurl']=$_SERVER['REQUEST_URI'];//存地址
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('CmsCms/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加',//2级
                        'url'    => U('CmsCms/add_content',array('token'=>$_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '点击V图标可以关闭显示，点击X图标可以打开显示，点击添加时间可以按添加时间进行排序等等~~'
                ),
                'Table_Header' => array(//4级
                    'ID', '栏目名','表名', 'token值','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('CmsCms/xx',array('token'=>$_SESSION['token']))
                                            ), */

    	array(
    			'name' => '管理子栏目',
    			'url'  => U('CmsZl/index',array('token'=>$_SESSION['token']))
    	),
                    array(
                        'name' => '编辑',
                        'url'  => U('CmsCms/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('CmsCms/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Cms_lm')->where(array('token'=>$_SESSION['token'],'bz'=>2))->count(),
            M('Cms_lm')->field('id,lm,b,token')->where(array('token'=>$_SESSION['token'],'bz'=>2))
        //array($this,'abc')
        );
    }
    public function abc($data){
        foreach($data as $k=>$v){
            
            switch ($v['bz']){
                case 1:$data[$k]['bz']='列表内容页';break;
                case 2:$data[$k]['bz']='自定义开发';break;
            }
            

        }
        return $data;
    }
    //定义增改函数
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('CmsCms2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
                } */
    	
        $this->$aaa('Cms_lm',array(
        		array('title'=>"表名",'type'=>"input",'name'=>"b",'value'=>'b','msg'=>'请填写input框','tishi'=>'不带前叠,必需子段id,uid,token'),
                array('title'=>"栏目名称",'type'=>"input",'name'=>"lm",'value'=>'lm','msg'=>'请填写input框'),
        		array('type'=>'checkbox','title'=>'选择功能','全选'=>1,'name'=>"lmgl",'value'=>'lmgl','many'=>array(
        				
        				
        				array('value'=>'is_show','checked'=>1 ,'content'=>'是否显示'),
        				array('value'=>'state','content'=>'审核状态'),
        				array('value'=>'add_time','checked'=>1,'content'=>'添加时间'),
        				array('value'=>'sort','checked'=>1,'content'=>'排序'),
        				
        				array('value'=>'name','checked'=>1,'content'=>'栏目1'),
        				array('value'=>'ts1','content'=>'栏目2'),
        				array('value'=>'ts2','content'=>'栏目3'),
        				array('value'=>'ts3','content'=>'栏目4'),
        				array('value'=>'ts4','content'=>'栏目5'),
        				
        				array('value'=>'namepic','content'=>'图片1'),
        				array('value'=>'cpic','content'=>'图片2'),
        				array('value'=>'pic3','content'=>'图片3'),
        				array('value'=>'pic4','content'=>'图片4'),
        				array('value'=>'pic5','content'=>'图片5'),
        				
        				array('value'=>'type','content'=>'类型点选框'),
        				array('value'=>'xl','content'=>'下拉列表'),
        				array('value'=>'fx','content'=>'复选框'),
        				
        				array('value'=>'k_time','content'=>'开始时间'),
        				array('value'=>'j_time','content'=>'结束时间'),
        				array('value'=>'map','content'=>'经纬度'),
        				
        				array('value'=>'cname','content'=>'内容栏目1'),
        				array('value'=>'address','content'=>'内容栏目2'),
        				array('value'=>'tel','content'=>'内容栏目3'),
        				array('value'=>'url','content'=>'内容栏目4'),
        				array('value'=>'ts5','content'=>'描述1'),
        				array('value'=>'ms','content'=>'描述2'),
        				array('value'=>'content','content'=>'内容1'),
        				array('value'=>'ts6','content'=>'内容2'),
        				array('value'=>'listlb','content'=>'列表页轮播图'),
        				array('value'=>'showlb','content'=>'内容页轮播图'),
        				array('value'=>'zl','content'=>'子类信息'),
        				array('value'=>'contentlb','content'=>'内容页轮播图(每页不同)'),
        				array('value'=>'ck','content'=>'查看详情'),
        				array('value'=>'save','checked'=>1,'content'=>'编辑'),
        				array('value'=>'delete','checked'=>1, 'content'=>'删除'),
        				array('value'=>'qx','content'=>'全选'),
        	    )),
        		

        		

        		
        		array('title'=>"是否显示",'type'=>"input",'name'=>"is_show",'value'=>'is_show','默认'=>'是否显示|is_show','tishi'=>'是否显示|is_show'),
        		array('title'=>"添加时间",'type'=>"input",'name'=>"add_time",'value'=>'add_time','默认'=>'添加时间|add_time','tishi'=>'添加时间|add_time'),
        		array('title'=>"审核状态",'type'=>"input",'name'=>"state",'value'=>'state','默认'=>'审核状态|state','tishi'=>'审核状态|state'),
        		
        		array('title'=>"栏目1",'type'=>"input",'name'=>"name",'value'=>'name','默认'=>' 标题|name','tishi'=>'(查询条件) 标题|name|提示信息'),
        		array('title'=>"栏目2",'type'=>"input",'name'=>"ts1",'value'=>'ts1','默认'=>'名称|ts1','tishi'=>'(查询条件) 名称|ts1|提示信息'),
        		array('title'=>"栏目3",'type'=>"input",'name'=>"ts2",'value'=>'ts2','默认'=>'名称|ts2','tishi'=>'显示栏目 名称|ts2|提示信息'),
        		array('title'=>"栏目4",'type'=>"input",'name'=>"ts3",'value'=>'ts3','默认'=>'名称|ts3','tishi'=>'显示栏目 名称|ts3|提示信息'),
        		array('title'=>"栏目5",'type'=>"input",'name'=>"ts4",'value'=>'ts4','默认'=>'名称|ts4','tishi'=>'显示栏目 名称|ts4|提示信息'),
        		
        		array('title'=>"图片1",'type'=>"input",'name'=>"namepic",'value'=>'namepic','默认'=>'图片|namepic|建议图片大小10×10,请按建议上传可避免图片变形','tishi'=>'显示栏目 图片|namepic|建议图片大小10×10,请按建议上传可避免图片变形'),
        		array('title'=>"图片2",'type'=>"input",'name'=>"cpic",'value'=>'cpic','默认'=>'图片|cpic','tishi'=>'图片|cpic|提示信息'),
        		array('title'=>"图片3",'type'=>"input",'name'=>"pic3",'value'=>'pic3','默认'=>'图片|pic3','tishi'=>'图片|pic3|提示信息'),
        		array('title'=>"图片4",'type'=>"input",'name'=>"pic4",'value'=>'pic4','默认'=>'图片|pic4','tishi'=>'图片|pic4|提示信息'),
        		array('title'=>"图片5",'type'=>"input",'name'=>"pic5",'value'=>'pic5','默认'=>'图片|pic5','tishi'=>'图片|pic5|提示信息'),
        		
        		array('title'=>"类型点选框",'type'=>"input",'name'=>"type",'value'=>'type','默认'=>'类型|type','tishi'=>'类型|type|提示信息'), 
        		array('title'=>"下拉列表",'type'=>"input",'name'=>"xl",'value'=>'xl','默认'=>'名称|xl','tishi'=>'名称|xl|提示信息'),
        		array('title'=>"复选框",'type'=>"input",'name'=>"fx",'value'=>'fx','默认'=>'名称|fx','tishi'=>'名称|fx|提示信息'),
        		
        		array('title'=>"开始时间",'type'=>"input",'name'=>"k_time",'value'=>'k_time','默认'=>'开始时间|k_time','tishi'=>'开始时间|k_time|提示信息'),
        		array('title'=>"结束时间",'type'=>"input",'name'=>"j_time",'value'=>'j_time','默认'=>'结束时间|j_time','tishi'=>'结束时间|j_time|提示信息'),
        		array('title'=>"经纬度",'type'=>"input",'name'=>"map",'value'=>'map','默认'=>'经纬度|position_x|position_y|','tishi'=>'子段map  经纬度|position_x|position_y|提示信息'),
        		
        		array('title'=>"内容栏目1",'type'=>"input",'name'=>"cname",'value'=>'cname','默认'=>'标题|cname','tishi'=>'标题|cname|提示信息'),
        		array('title'=>"内容栏目2",'type'=>"input",'name'=>"address",'value'=>'address','默认'=>'地址|address','tishi'=>'地址|address|提示信息'),
        		array('title'=>"内容栏目3",'type'=>"input",'name'=>"tel",'value'=>'tel','默认'=>'手机|tel','tishi'=>'手机|tel|提示信息'),
        		array('title'=>"内容栏目4",'type'=>"input",'name'=>"url",'value'=>'url','默认'=>'链接|url','tishi'=>'链接|url|提示信息'),
        		
        		array('title'=>"描述1",'type'=>"input",'name'=>"ts5",'value'=>'ts5','默认'=>'名称|ts5','tishi'=>'名称|ts5|提示信息'),
        		array('title'=>"描述2",'type'=>"input",'name'=>"ms",'value'=>'ms','默认'=>'描述|ms','tishi'=>'描述|ms|提示信息'),
        		
        		
        		array('title'=>"内容1",'type'=>"input",'name'=>"content",'value'=>'content','默认'=>'内容|content','tishi'=>'内容|content|提示信息'),
        		array('title'=>"内容2",'type'=>"input",'name'=>"ts6",'value'=>'ts6','默认'=>'名称|ts6','tishi'=>'名称|ts6|提示信息'),
        		
        		

        		
	       		array('title'=>"提示信息",'type'=>"input",'name'=>"xx",'value'=>'xx','默认'=>'提示信息:你可以在这里管理信息','tishi'=>'提示信息子段xx '),
        		array('title'=>"子类信息",'type'=>"input",'name'=>"zl",'value'=>'zl','默认'=>'体验信息|8','tishi'=>'子段zl 体验信息|8(8是子栏目id) '),
        		array('title'=>"全选",'type'=>"input",'name'=>"qx",'value'=>'qx','默认'=>'全部删除|Cmsqx/qx','tishi'=>'子段qx 全部删除|Cmsqx/qx'),
        		
        ),U('CmsCms/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
    	if(!$data['page'])$data['page']=15;
    	$data['bz']=2;
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
        //M('Cms_lm')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Cms_lm');
    }


    public function sc(){
    	$_SESSION['lmurl']=$_SERVER['REQUEST_URI'];//存地址
    	//$_SESSION['token']=$_SESSION['token'];
    	/*     	//搜索
    	 if(IS_POST){
    	$_POST=$_REQUEST;
    	$aWhere=$this->search($_POST);
    	$aWhere['token'] =$_SESSION['token'];
    	//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    	}//结束 */
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('CmsCms/sc', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '添加',//2级
    									'url'    => U('CmsCms/add_contentsc',array('token'=>$_SESSION['token']))
    							)
    					),
    					'tips' => array(//3级
    							'你可以在这里管理信息'
    					),
    					'Table_Header' => array(//4级
    							'ID', '栏目名', '项目英文目录名', '列表英文文件名','内容英文文件名','每页显示多少条', 'token值','操作'
    					),
    					'List_Opt' => array(
    
    							/*               			array(
    							 'name' => '其它信息',
    									'url'  => U('CmsCms/xx',array('token'=>$_SESSION['token']))
    							), */
    
    							array(
    									'name' => '管理子栏目',
    									'url'  => U('CmsZlm_sc/index',array('token'=>$_SESSION['token']))
    							),
    							array(
    									'name' => '编辑',
    									'url'  => U('CmsCms/save_contentsc',array('token'=>$_SESSION['token']))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('CmsCms/delete_contentsc',array('token'=>$_SESSION['token']))
    							),
    					),
    					/*         		//搜索
    					 'search'=>array(
    					 		array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
    					 		array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
    					 		array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
    	)//结束 */
    	),
    			M('Cms_lmsc')->where(array('token'=>$_SESSION['token']))->count(),
    			M('Cms_lmsc')->field('id,lm,xm,list,show,page,token')->where(array('token'=>$_SESSION['token']))
    			// array($this,'abc')
    	);
    }


    //定义增改函数
    public function add_savesc($aaa){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('CmsCms2')->select();//被添加内容的表
    	 foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('Cms_lmsc',array(
    			array('title'=>"栏目名称",'type'=>"input",'name'=>"lm",'value'=>'lm','msg'=>'请填写input框'),
    			array('title'=>"项目英文目录名",'type'=>"input",'name'=>"xm",'value'=>'xm','msg'=>'请填写input框'),
    			array('title'=>"列表英文文件名",'type'=>"input",'name'=>"list",'value'=>'list','msg'=>'请填写input框'),
    			array('title'=>"内容英文文件名",'type'=>"input",'name'=>"show",'value'=>'show','msg'=>'请填写input框'),
    			array('title'=>"每页显示多少条",'type'=>"number",'name'=>"page",'value'=>'page','tishi'=>'不镇默认是15条'),
    
    			array('type'=>'checkbox','title'=>'选择功能','name'=>"lmgl",'value'=>'lmgl','many'=>array(
    					array('value'=>'name','content'=>'标题'),
    					array('value'=>'price','content'=>'原价'),
    					array('value'=>'vprice','content'=>'会员价'),
    					array('value'=>'num','content'=>'库存'),
    					array('value'=>'vnum','content'=>'购买数量'),
    					array('value'=>'namepic','content'=>'列表页图片'),
    					array('value'=>'type','content'=>'类型'),
    					array('value'=>'address','content'=>'地址'),
    					array('value'=>'tel','content'=>'手机'),
    					array('value'=>'jf','content'=>'获取积分'),
    					array('value'=>'hb','content'=>'获取红包'),
    					array('value'=>'ms','content'=>'商品描述'),
    					array('value'=>'cname','content'=>'内容标题'),
    					array('value'=>'wg_gg','content'=>'外观规格'),
    					array('value'=>'content','content'=>'内容'),
    					array('value'=>'listlb','content'=>'列表页轮播图'),
    					array('value'=>'showlb','content'=>'内容页轮播图'),
    					array('value'=>'contentlb','content'=>'内容页轮播图(每页不同)'),
    					
    		
    			)),
    
    	),U('CmsCms/sc',array('token'=>$_SESSION['token'])),array($this,'bbcsc'));
    
    }
    
    
/*     */
    
    public function bbcsc($data){
    	if(!$data['page'])$data['page']=15;
    	return $data;
    }
    //添加
    public function add_contentsc(){
    	$this->add_savesc(add);
    }
    //编辑
    public function save_contentsc(){
    	$this->add_savesc(Edit);
    }
    //删除
    public function delete_contentsc(){
    	//M('Cms_lm')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
    	$this->del('Cms_lmsc');
    }
    
    
    //单页
    public function dy(){
    	$_SESSION['lmurl']=$_SERVER['REQUEST_URI'];//存地址
    	//$_SESSION['token']=$_SESSION['token'];
    	/*     	//搜索
    	 if(IS_POST){
    	$_POST=$_REQUEST;
    	$aWhere=$this->search($_POST);
    	$aWhere['token'] =$_SESSION['token'];
    	//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    	}//结束 */
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('CmsCms/dy', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '添加',//2级
    									'url'    => U('CmsCms/add_contentdy',array('token'=>$_SESSION['token']))
    							)
    					),
    					'tips' => array(//3级
    							'你可以在这里管理信息'
    					),
    					'Table_Header' => array(//4级
    							'ID', '栏目名', '项目英文目录名','内容英文文件名', 'token值','操作'
    					),
    					'List_Opt' => array(
    
    							/*               			array(
    							 'name' => '其它信息',
    									'url'  => U('CmsCms/xx',array('token'=>$_SESSION['token']))
    							), */
    
    							array(
    									'name' => '管理子栏目',
    									'url'  => U('CmsDy/index',array('token'=>$_SESSION['token']))
    							),
    							array(
    									'name' => '编辑',
    									'url'  => U('CmsCms/save_contentdy',array('token'=>$_SESSION['token']))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('CmsCms/delete_contentdy',array('token'=>$_SESSION['token']))
    							),
    					),
    					/*         		//搜索
    					 'search'=>array(
    					 		array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
    					 		array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
    					 		array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
    	)//结束 */
    	),
    			M('Cms_dy')->where(array('token'=>$_SESSION['token']))->count(),
    			M('Cms_dy')->field('id,lm,xm,show,token')->where(array('token'=>$_SESSION['token']))
    			// array($this,'abc')
    	);
    }
    
    
    //定义增改函数
    public function add_savedy($aaa){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('CmsCms2')->select();//被添加内容的表
    	 foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('Cms_dy',array(
    			array('title'=>"栏目名称",'type'=>"input",'name'=>"lm",'value'=>'lm','msg'=>'请填写input框'),
    			array('title'=>"项目英文目录名",'type'=>"input",'name'=>"xm",'value'=>'xm','msg'=>'请填写input框'),
    			array('title'=>"内容英文文件名",'type'=>"input",'name'=>"show",'value'=>'show','msg'=>'请填写input框'),
    
    			array('type'=>'checkbox','title'=>'选择功能','name'=>"lmgl",'value'=>'lmgl','many'=>array(
    					array('value'=>'name','content'=>'内容标题'),
    					array('value'=>'pic','content'=>'图片'),
    					array('value'=>'lb','content'=>'轮播图'),
    					array('value'=>'content','content'=>'内容'),
    			)),
    
    	),U('CmsCms/dy',array('token'=>$_SESSION['token'])),array($this,'bbcdy'));
    
    }
    
    
    /*     */
    
    public function bbcdy($data){
    	if(!$data['page'])$data['page']=15;
    	return $data;
    }
    //添加
    public function add_contentdy(){
    	$this->add_savedy(add);
    }
    //编辑
    public function save_contentdy(){
    	$this->add_savedy(Edit);
    }
    //删除
    public function delete_contentdy(){
    	//M('Cms_lm')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
    	$this->del('Cms_dy');
    }
    
    
    
    
    //用户中心
    
    //单页
    public function yh(){
    	$_SESSION['lmurl']=$_SERVER['REQUEST_URI'];//存地址
    	//$_SESSION['token']=$_SESSION['token'];
    	/*     	//搜索
    	 if(IS_POST){
    	$_POST=$_REQUEST;
    	$aWhere=$this->search($_POST);
    	$aWhere['token'] =$_SESSION['token'];
    	//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    	}//结束 */
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('CmsCms/yh', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '添加',//2级
    									'url'    => U('CmsCms/add_contentyh',array('token'=>$_SESSION['token']))
    							)
    					),
    					'tips' => array(//3级
    							'你可以在这里管理信息'
    					),
    					'Table_Header' => array(//4级
    							'ID', '栏目名', '项目英文目录名','内容英文文件名', 'token值','操作'
    					),
    					'List_Opt' => array(
    
    							/*               			array(
    							 'name' => '其它信息',
    									'url'  => U('CmsCms/xx',array('token'=>$_SESSION['token']))
    							), */
    
    							array(
    									'name' => '管理子栏目',
    									'url'  => U('CmsYh/index',array('token'=>$_SESSION['token']))
    							),
    							array(
    									'name' => '编辑',
    									'url'  => U('CmsCms/save_contentyh',array('token'=>$_SESSION['token']))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('CmsCms/delete_contentyh',array('token'=>$_SESSION['token']))
    							),
    					),
    					/*         		//搜索
    					 'search'=>array(
    					 		array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
    					 		array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
    					 		array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
    					 )//结束 */
    			),
    			M('Cms_lmyh')->where(array('token'=>$_SESSION['token']))->count(),
    			M('Cms_lmyh')->field('id,lm,xm,show,token')->where(array('token'=>$_SESSION['token']))
    			// array($this,'abc')
    	);
    }
    
    
    //定义增改函数
    public function add_saveyh($aaa){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('CmsCms2')->select();//被添加内容的表
    	 foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('Cms_lmyh',array(
    			array('title'=>"栏目名称",'type'=>"input",'name'=>"lm",'value'=>'lm','msg'=>'请填写input框'),
    			array('title'=>"项目英文目录名",'type'=>"input",'name'=>"xm",'value'=>'xm','msg'=>'请填写input框'),
    			array('title'=>"内容英文文件名",'type'=>"input",'name'=>"show",'value'=>'show','msg'=>'请填写input框'),
    
    			array('type'=>'checkbox','title'=>'选择功能','name'=>"lmgl",'value'=>'lmgl','many'=>array(
    					array('value'=>'name','content'=>'内容标题'),
    					array('value'=>'pic','content'=>'图片'),
    					array('value'=>'lb','content'=>'轮播图'),
    					array('value'=>'content','content'=>'内容'),
    			)),
    
    	),U('CmsCms/yh',array('token'=>$_SESSION['token'])),array($this,'bbcyh'));
    
    }
    
    
    /*     */
    
    public function bbcyh($data){
    	if(!$data['page'])$data['page']=15;
    	return $data;
    }
    //添加
    public function add_contentyh(){
    	$this->add_saveyh(add);
    }
    //编辑
    public function save_contentyh(){
    	$this->add_saveyh(Edit);
    }
    //删除
    public function delete_contentyh(){
    	//M('Cms_lm')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
    	$this->del('Cms_lmyh');
    }
    

}

