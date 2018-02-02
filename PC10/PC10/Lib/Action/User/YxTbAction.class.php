<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class YxTbAction extends TableAction {
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
        $this->pz	   = D('Yanxiang_tb');
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
                'HeadHover' => U('YxTb/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加图片',//2级
                        'url'    => U('YxTb/add_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID|id', '图片','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('YxTb/xx',array('token'=>$_SESSION['token']))
                                            ), */

                    array(
                        'name' => '编辑',
                        'url'  => U('YxTb/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('YxTb/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
             
            ),
            M('Yanxiang_tb')->count(),
            M('Yanxiang_tb')->field('id,pic'),
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
       
        $this->$aaa('Yanxiang_tb',array(
            
        	
/*        	array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'<a style="color:red;">注意:如果镇了链接前台跳的是链接地址,前面加http://</a>'),*/
        	array('type'=>'img','many'=>array(
        				array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
        	)),
        		array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url'),
         
        ),U('YxTb/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
        
        return $data;
    }
    //添加
    public function add_content(){
    	$count=M('Yanxiang_tb')->count();
    	if($count>2) script("你已经添加3张了");
        $this->add_save(add);
    }
    //编辑
    public function save_content(){
        $this->add_save(Edit);
    }
    //删除
    public function delete_content(){
        //M('Yanxiang_tb')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Yanxiang_tb');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable('Yanxiang_tb');
    }
    //是否显示  只 能主健是id,子段是is_show 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable('Yanxiang_tb');
    }


    
    
    
    
   

}

