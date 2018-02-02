<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class JdWzAction extends TableAction {
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
        $this->pz	   = D('jd_wz');
        $this->token=session('token');
        
        
        //是否审核
        $this->state='status';//改这
        $this->assign('state',$this->state);
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '顾问管理',
                'url'  => U('JdAdviser/index', array('token' => $this->_sToken))
            ),
        		
        		array(
        				'name'=>'方案管理',
        				'url'  => U('JdWz/index', array('token' => $this->_sToken))
        		),
          
            array(
                'name'=>'用户管理',
                'url'  => U('JdUser/index', array('token' => $this->_sToken))
            ),
            array(
                'name'=>'招标方案与活动管理',
                'url'  => U('JdUser/tender', array('token' => $this->_sToken))
            ),
            /* array(
                 'name'=>'交标管理',
                 'url'  => U('JdUser/scale', array('token' => $this->_sToken))
             ),*/


          
        );

    }
    //显示
    public function index2(){
    	$aWhere['token']=$_SESSION['token'];
    	//搜索
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
    		$aWhere['token'] =$_SESSION['token'];
    	}//结束
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdWz/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加',//2级
                        'url'    => U('JdWz/add_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name'=>'方案审核',
                        'url'  => U('JdWz/checkWz', array('token' => $this->_sToken))
                    ),
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID',  '标题', '作者', '行业', '标签','审核状态', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('JdWz/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdWz/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
            		
            		'search'=>array(
            				  array('title'=>'标题查询','name'=>'li_title','placeholder'=>'请输入标题模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
            				// array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
            				//array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
            			
            		)

            ),
            M('jd_wz')->where($aWhere)->count(),
            M('jd_wz')->field('id,title,name,hy,tags,status')->where($aWhere)->order("add_time desc"),
            array($this,'abc')
        );
    }
    
    //全选操作
    public function qx(){
    	$id=implode(',', $_REQUEST['list']);
    	if(!$_REQUEST['list']) script("全选后此操作才有效");
    	$list=M('jd_wz')->limit(0,1)->select();
    	if($list[0][fw]==2){
    		$list=M('jd_wz')->where(array('id'=>array('in',$id)))->save(array('fw'=>1));//改这
    	}else{
    		$list=M('jd_wz')->where(array('id'=>array('in',$id)))->save(array('fw'=>2));//改这
    	}
    	
    	if($list) {
    		$this->success2('操作成功');
    	}else{
    		$this->error2('操作失败');
    	}
    }
    
    
    //推荐方案审查
    public function index(){

    	$this->assign('qx','共享范围操作');//全选  方法名qx
    	$aWhere['token']=$_SESSION['token'];
    	//搜索
    	if(IS_POST){
    		/*$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
    		$aWhere['token'] =$_SESSION['token'];*/
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
                if(session('?where_p')){
                    $aWhere=session('where_p');
                }

        }
    	
        if($_GET['action']=='check'){
            $data=array('id'=>$_GET['id'],'status'=>1);
            if(M('jd_wz')->save($data)){
                $this->success2('审核成功！');
            }else{
                $this->error2('审核失败，数据服务器繁忙...');
            }
        }
        //P($aWhere);

        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdWz/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加方案',//2级
                        'url'    => U('JdWz/add_content',array('token'=>$_SESSION['token']))
                    ),
                		
                    array(
                            'name'   => '标签管理',//2级
                            'url'    => U('JdTag/index',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name'   => '行业管理',//2级
                        'url'    => U('JdWz/industry',array('token'=>$_SESSION['token']))
                    ),
                		
                    array(
                            'name'   => 'EXCEL导入',//2级
                            'url'    => U('Usercenter/dl',array('token'=>$_SESSION['token'],'id'=>3730,'type'=>1))
                    )
                ),
                'tips' => array(//3级
                    '<b style="font-size:14px;" >特别注意:点V图标可取消审核，点X图际可通过审核</b>'
                ),
               /* 'Table_Header' => array(//4级
                    'ID',  '标题', '推荐人','联系方式', '作者', '行业','添加时间','审核状态','共享范围','发送email次数','分亨数','排序', '操作'
                ),*/
                'Table_Header' => array(//4级
                    'ID',  '标题', '作者', '行业','添加时间','共享范围','发送email次数','点赞数','分亨数','排序','审核状态', '操作'
                ),
                'List_Opt' => array(
                		
          /*           array(
                        'name'=>'审核',
                        'url'=>U('JdWz/sh',array('token'=>$_SESSION['token'],'action'=>'check')),
                    ),
                	array(
                		'name'=>'取消审核',
                		'url'=>U('JdWz/no_sh',array('token'=>$_SESSION['token'],'action'=>'check')),
                	), */
                		
                    array(
                        'name' => '编辑',
                        'url'  => U('JdWz/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '查看评论',
                        'url'  => U('JdUser/evalute',array('token'=>$_SESSION['token'],'type'=>'plan'))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdWz/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),

            		'search'=>array(
            				array('title'=>'标题查询','name'=>'li_title','placeholder'=>'请输入标题模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
            				array('title'=>'方案类型查询','name'=>'eq_type','type'=>'select','many'=>array(
            						array('value'=>'微文浏览', 'name'=>'微文浏览'),
            						array('value'=>'售前方案', 'name'=>'售前方案'),
            						array('value'=>'实施方案', 'name'=>'实施方案'),
            						array('value'=>'市场方案', 'name'=>'市场方案'),
            						array('value'=>'样板方案', 'name'=>'样板方案'),
            						//array('value'=>'3','name'=>'旅游1'),
            				)),
            		)
            ),
            M('jd_wz')->where($aWhere)->count(),
            M('jd_wz')->field('id,title,name,hy,add_time,fw,email_num,praise,fx_num,sort,status')->where($aWhere)->order("sort, add_time desc"),
            array($this,'abc')
        );
    }
    public function abc($data){
        foreach($data as $k=>$v){

        	/*$data[$k]['user_id']=M('jd_user')->where(array('id'=>$v['user_id']))->getField('name')?
        						 M('jd_user')->where(array('id'=>$v['user_id']))->getField('name'):
        						 $v['user_id'];*/
        	//$data[$k]['name']=str_substr($v['name'],8,'...');
        	//$data[$k]['title']=str_substr($v['title'],8,'...');
            $data[$k]['add_time'] = date('Y/m/d H:i',$v['add_time']);
            switch ($v['fw']){
            	case 1:$data[$k]['fw']='圈子内公开';break;
            	case 2:$data[$k]['fw']='全面公开';break;
		        case 3:$data[$k]['fw']='VIP私享';break;
            }
            $aIndustry = M('Jd_industry')->where(array('token'=>$this->_sToken,'id'=>$v['hy']))->find();

        }
        return $data;
    }
    
  
    //定义增改函数
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('JdWz2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
        } */
        $aIndustry = M('Jd_industry')->field('id as value,industry as content')->where(array('token'=>$this->_sToken))->select();
        foreach ($aIndustry as $ke => $v){
        	$aIndustry[$ke]['value'] = $v['content'];
        }
        $aIndustry = array_merge(array(array('value'=>0,'content'=>'选择行业类型')),$aIndustry);
        
        $this->$aaa('jd_wz',array(
            /*如果有需求要在input框中提示信息,则加入一个'placinfo'的键值，表示input 里面的placeholder属性*/

            array('title'=>"标题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写标题'),
            array('title'=>"方案亮点",'type'=>"textarea2",'name'=>"ld",'value'=>'ld'),
        		//array('title'=>"方案亮点",'type'=>"input",'name'=>"ld",'value'=>'ld'),
        		array('title'=>"联系电话",'type'=>"input",'name'=>"tel",'value'=>'tel'),
        		
        	array('title'=>"方案链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'注意:前面加http://'),
        	array('title'=>"方案关健字",'type'=>"input",'name'=>"gjz",'value'=>'gjz','tishi'=>'多个关健字请用","隔开。比如：金蝶,万普,百度'),
        	array('type'=>'select','title'=>"方案类型",'name'=>"type",'value'=>'type','msg'=>'请选择行业类型','many'=>array(
        			array('content'=>'选择方案类型'),
        			array('value'=>'微文浏览','content'=>'微文浏览'),
        			array('value'=>'售前方案','content'=>'售前方案'),
        			array('value'=>'实施方案','content'=>'实施方案'),
        			array('value'=>'市场方案','content'=>'市场方案'),
        			array('value'=>'样板方案','content'=>'样板方案'),
        	)),
            array('type'=>'select','title'=>"共享范围",'name'=>"fw",'value'=>'fw','msg'=>'请选择行业类型','many'=>array(
                array('value'=>'', 'content'=>'请选择共享范围'),
         		array('value'=>'1', 'content'=>'圈子内公开'),
                array('value'=>'2','content'=>'全面公开'),
                array('value'=>'3','content'=>'vip私享'),
            )), 
        		
        		
            array('title'=>"作者",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写作者'),

            array('type'=>'select','title'=>"行业",'name'=>"hy",'value'=>'hy','msg'=>'请选择行业类型','many'=>$aIndustry/*array(
                            array('content'=>'请选择行业'),
                            array('value'=>'食品', 'content'=>'食品'),
                            array('value'=>'服务','content'=>'服务'),
                    )*/),//$aIndustry


            array('type'=>'checkbox','title'=>'标签','name'=>"tags",'value'=>'tags','msg'=>'请选择标签','many'=>$this->getTags()),

        	array('title'=>"文件后缀",'type'=>"number",'name'=>"hz",'value'=>'hz','tishi'=>'如:输入doc 或 docx 或 pdf 或 ppt 或 pptx 或 txt 或 xls或 xlsx'),
            array('title'=>"图文详细",'type'=>"textarea2",'name'=>"content",'value'=>'content'),

        ),U('JdWz/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    /*
     * 获得系统预定标签
     */
    protected function getTags(){
        $tags=M('jd_tag')->select();
        foreach($tags as $key=>$value){
            $arr[$key]['value']=$value['id'];
            $arr[$key]['content']=$value['name'];
        }
        return $arr;
    }
    public function bbc($data){
        $data['password']=MD5($data['password']);
        $data['add_time']=time();
        $data['status']=1;
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
        //M('jd_wz')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        
        
        $status = M('jd_wz')->where(get(id))->getField('status');
        if($status){
        	script("已审核的方案不能删除");
        }else{
        	$this->del('jd_wz');
        }
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable('jd_wz');
    }





    /*行业管理*/
    public function industry(){
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdWz/index',array('token'=>$_SESSION['token'])),//栏目样式
                'Head_Opt' => array(

                    array(
                        'name'   => '添加行业',//2级
                        'url'    => U('JdWz/add_industry',array('token'=>$_SESSION['token']))
                    ),
                		
                		array(
                				'name'   => '返回',//2级
                				'url'    => U('JdWz/index')
                		),
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '行业名称','操作'
                ),
                'List_Opt' => array(



                    array(
                        'name' => '编辑',
                        'url'  => U('JdWz/save_industry',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdWz/delete_industry',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('Jd_industry')->where(array('token'=>$_SESSION['token']))->count(),
            M('Jd_industry')->field('id,industry')->where(array('token'=>$_SESSION['token']))
            //array($this,'xxabc')
        );
    }

    //定义增改函数
    public function add_saves($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('JdWz2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
        } */
        $this->$aaa('Jd_industry',array(
            array('title'=>"行业名称",'type'=>"input",'name'=>"industry",'value'=>'industry','msg'=>'请填写行业名称'),
        ),U('JdWz/industry',array('token'=>$_SESSION['token'])),array($this,'industryinfo'));
    }

    public function industryinfo($data){
        $data['token'] = $this->_sToken;
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    //添加
    public function add_industry(){
        $this->add_saves(add);
    }
    //编辑
    public function save_industry(){
        $this->add_saves(Edit);
    }
    //删除
    public function delete_industry(){
        //M('jd_wz')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Jd_industry');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajaxs(){
        $this->sortajaxTable('Jd_industry');
    }
    
    
    //是否审核  只 能主健是id, 才能是否审核   有意见自己去开发！
    public function stateAjax(){
        
    	$this->stateAjaxTable('jd_wz',$this->state);
    }






   

}

