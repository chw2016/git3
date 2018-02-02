<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class YxLbAction extends TableAction {
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
        $this->pz	   = D('yanxiang_lb');
    }
    //一级
    protected function setHeader(){
    	$type=$_SESSION['type'];
        return array(
       
        		
        		array(
        				'name' => '返回',
        				'url'  => $_SESSION['url']
        		),

        );
    }
    //显示
    public function index(){
      if($_GET['type']) $_SESSION['type']=$_GET['type'];
		    	
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('YxLb/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加轮播图',//2级
                        'url'    => U('YxLb/add_content',array('token'=>$_SESSION['token']))
                    ),
                		
                		
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID','轮播图片','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('YxLb/xx',array('token'=>$_SESSION['token']))
                                            ), */

                    array(
                        'name' => '编辑',
                        'url'  => U('YxLb/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('YxLb/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('yanxiang_lb')->where(array('type'=>$_SESSION['type']))->count(),
            M('yanxiang_lb')->where(array('type'=>$_SESSION['type']))->field('id,pic'),
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
        $list=M('cms_lm')->where(array('token'=>$_SESSION['token']))->select();
        foreach ($list as $k=>$v){
        	$list[$k]['content']=$v['lm'];//把内容子段改成content
        	$list[$k]['value']=$v['id'];//把id子段改成value
        	unset($list[$k]['name']);//删除原来的内容子段
        }
        
        $this->$aaa('yanxiang_lb',array(

           array('type'=>'img','many'=>array(
        				array('title'=>"轮播图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
           )),
        		
           array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url'),
           array('title'=>"标题",'type'=>"input",'name'=>"name",'value'=>'name','tishi'=>'很少用,可以不用镇哦'),
        	
        ),U('YxLb/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
        
        $data['type']=$_SESSION['type'];
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
        //M('yanxiang_lb')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('yanxiang_lb');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable('yanxiang_lb');
    }
    //是否显示  只 能主健是id,子段是is_show 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable('yanxiang_lb');
    }
}

