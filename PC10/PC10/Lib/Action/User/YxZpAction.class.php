<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class YxZpAction extends TableAction {
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
        $this->pz	   = D('yanxiang_zp');
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
                'HeadHover' => U('YxZp/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加微招聘',//2级
                        'url'    => U('YxZp/add_content',array('token'=>$_SESSION['token']))
                    ),
                		
                	                       		array(
                				'name'   => '市场微招聘轮播图管理',//2级
                				'url'    => U('YxLb/index',array('type'=>'微招聘'))
                		),
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID|id', '职业', '图片', '添加时间|add_time','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('YxZp/xx',array('token'=>$_SESSION['token']))
                                            ), */

                    array(
                        'name' => '编辑',
                        'url'  => U('YxZp/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('YxZp/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
             
            ),
            M('yanxiang_zp')->count(),
            M('yanxiang_zp')->field('id,title,pic,times')->order("times desc"),
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
       
        $this->$aaa('yanxiang_zp',array(
            array('title'=>"职业",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写input框'),
        	
        	array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'<a style="color:red;">注意:如果镇了链接前台跳的是链接地址,前面加http://</a>'),
        	array('type'=>'img','many'=>array(
        				array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
        	)),
        	/* 	array('type'=>'select','title'=>"职 业类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
        				array( 'content'=>'请选择职 业类型'),
        				array('value'=>'2','content'=>'二年以上'),
        				array('value'=>'3','content'=>'三年以上'),
        				array('value'=>'4','content'=>'不限'),
        		)), */
        		
        	array('title'=>"描述",'type'=>"textarea2",'name'=>"ms",'value'=>'ms','msg'=>'请填写input框'),
        	array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
         
        ),U('YxZp/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
        
        $data['times']=date("Y-m-d",time());
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
        //M('yanxiang_zp')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('yanxiang_zp');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable('yanxiang_zp');
    }
    //是否显示  只 能主健是id,子段是is_show 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable('yanxiang_zp');
    }







}

