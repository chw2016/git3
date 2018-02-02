<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class YxDtAction extends TableAction {
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
        $this->pz	   = D('Yanxiang_status');
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '返回',
                'url'  => U('Yanxiang/index', array('token' => $this->_sToken))
            ),

        );
    }
    //显示
    public function index(){

        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('YxDt/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加研详动态',//2级
                        'url'    => U('YxDt/add_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID|id', '标题', '图片', '添加时间|add_time','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('YxDt/xx',array('token'=>$_SESSION['token']))
                                            ), */

                    array(
                        'name' => '编辑',
                        'url'  => U('YxDt/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('YxDt/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
             
            ),
            M('Yanxiang_status')->count(),
            M('Yanxiang_status')->field('id,title,pic,times')->order("times desc"),
       array($this,'abc')
        );
    }
    public function abc($data){
        foreach($data as $k=>$v){
     
            $data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
            

        }
        return $data;
    }
    //定义增改函数
    public function add_save($aaa){
    
    

    }
    public function bbc($data){
        
        //$data['times']=date("Y-m-d",time());
        return $data;
    }
    //添加
    public function add_content(){
    	
    	
    	$this->add('Yanxiang_status',array(
    			array('title'=>"标题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写input框'),
    			 
    			array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'<a style="color:red;">注意:如果镇了链接前台跳的是链接地址,前面加http://</a>'),
    			array('type'=>'img','many'=>array(
    					array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
    			)),
    			array('title'=>"描述",'type'=>"textarea2",'name'=>"ms",'value'=>'ms','msg'=>'请填写input框'),
    			array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
    			 
    	),U('YxDt/index',array('token'=>$_SESSION['token'])),array($this,'bbc'),'','times');
    	
      
    }
    //编辑
    public function save_content(){
     
        $this->Edit('Yanxiang_status',array(
        		array('title'=>"标题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写input框'),
        
        		array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'<a style="color:red;">注意:如果镇了链接前台跳的是链接地址,前面加http://</a>'),
        		array('type'=>'img','many'=>array(
        				array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
        		)),
        		array('title'=>"描述",'type'=>"textarea2",'name'=>"ms",'value'=>'ms','msg'=>'请填写input框'),
        		array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
        
        ),U('YxDt/index',array('token'=>$_SESSION['token'])),array($this,'bbc'),'');
    }
    //删除
    public function delete_content(){
        //M('Yanxiang_status')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Yanxiang_status');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable('Yanxiang_status');
    }
    //是否显示  只 能主健是id,子段是is_show 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable('Yanxiang_status');
    }







}

