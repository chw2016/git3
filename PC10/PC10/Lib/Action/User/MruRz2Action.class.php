<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class MruRz2Action extends TableAction {
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
        $this->pz	   = D('rz');
    }
    //一级
    protected function setHeader(){
        	return include "./Lib/Action/User/Mru_top.php";
    }
    //显示
    public function index(){

        //$_SESSION['token']=$_SESSION['token'];
    	
    	 
    	//排序
             	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$_SESSION['token'];
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
                    $_SESSION['aWhere']=$aWhere;//排序
                }//结束 
		    	if($_SESSION['aWhere'])$aWhere=$_SESSION['aWhere'];//排序
		    	//P($aWhere);
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('MruRz2/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                  /*   array(
                        'name'   => '添加',//2级
                        'url'    => U('MruRz2/add_content',array('token'=>$_SESSION['token']))
                    ), */
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID|id', '会员昵称|openid','操作时间|add_time', '标题|name', '操作内容|content', '操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('MruRz2/xx',array('token'=>$_SESSION['token']))
                                            ), */

             /*        array(
                        'name' => '编辑',
                        'url'  => U('MruRz2/save_content',array('token'=>$_SESSION['token']))
                    ), */
                    array(
                        'name' => '删除',
                        'url'  => U('MruRz2/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                'search'=>array(
                                              //  array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                               // array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                //array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
                                              /*   array('title'=>'分类','name'=>'eq_sex','type'=>'select','many'=>array(
							                        array('value'=>'1','name'=>'男'),
							                        array('value'=>'2','name'=>'女'),
							                        //array('value'=>'3','name'=>'旅游1'),
							                    )) */
                                )
            ),
            M('rz')->where($aWhere)->count(),
            M('rz')->field('id,openid,add_time,name,content')->order("add_time desc")->where($aWhere),
        array($this,'abc')
        );
    }
    public function abc($data){
        foreach($data as $k=>$v){
           $data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            $info = M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('name');
            $user = M("Wxuser")->where(array('token'=>$_SESSION['token']))->find();
           $data[$k]['openid']= $info ? $info : M("Wxusers")->where(array('uid'=>$user['id'],'openid'=>$v['openid']))->getField('nickname');


        }
        return $data;
    }
    //定义增改函数
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('MruRz22')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
                } */
    	//链接$url=C('site_url')."index.php?g=User&m=Zp5&a=a&id=".$_GET['id'];
        $this->$aaa('rz',array(
            /*如果有需求要在input框中提示信息,则加入一个'placinfo'的键值，表示input 里面的placeholder属性*/
            //array('title'=>"较长input框",'type'=>"longinput",'name'=>"cname",'value'=>'cname','msg'=>'请填写较长input框'),
            array('title'=>"姓名",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写input框'),
        	array('type'=>'radio','title'=>"点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写点选框','many'=>array(
        				array('value'=>'1','content'=>'男'),
        				array('value'=>'2','content'=>'女'),
        	)),
        		
            //array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd'),
            //	array('title'=>"隐藏",'type'=>"hidden",'name'=>"id",'msg'=>'请填写标题咯','value'=>'id'),
            //array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写开始时间','value'=>'k_time'),
            //	array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写结束时间','value'=>'j_time'),
            //	array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num','msg'=>'请填写数量',),
        	//array('title'=>"链接",'type'=>"a",'url'=>$url,'name'=>"链接" ,'target'=>'1'),
            //			array('type'=>'radio','title'=>"特殊点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
            /* 		array('type'=>'select','title'=>"下拉列表",'name'=>"sex2",'value'=>'sex2','msg'=>'请选择下拉列表','many'=>array(
                            array('content'=>'选择'),
                            array('value'=>'1', 'content'=>'一年以上'),
                            array('value'=>'2','content'=>'二年以上'),
                            array('value'=>'3','content'=>'三年以上'),
                            array('value'=>'4','content'=>'不限'),
                    )), */
            //	array('type'=>'select','title'=>"特殊下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
/*             array('type'=>'checkbox','title'=>'复选框', '全选'=>1, 'name'=>"sex3",'value'=>'sex3','msg'=>'请选择复选框','many'=>array(
                array('value'=>'1','content'=>'蓝','checked'=>1),
                array('value'=>'2','content'=>'红','checked'=>1),
            )),


             //	array('type'=>'checkbox','title'=>'特殊复选框','全选'=>1, 'name'=>'jy', 'value'=>'jy','msg'=>'请填写标题咯','many'=>$list),
            array('type'=>'img','many'=>array(
                array('title'=>"图片1",'type'=>"img",'name'=>"pic",'value'=>'pic','width'=>50,'height'=>50),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic2",'value'=>'pic2','width'=>50,'height'=>50),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic3",'value'=>'pic3','width'=>50,'height'=>50)
            )),
            array('title'=>"小的复文本框",'type'=>"textarea2",'name'=>"content2",'value'=>'content2'),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
            array('title'=>"经纬度",'type'=>"map",'name'=>'name2', 'lng'=>"position_x",'lat'=>'position_y'), */
        ),U('MruRz2/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
        
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
        //M('rz')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('rz');
    }
    
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable('rz');
    }
    //是否显示  只 能主健是id,子段是is_show 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable('rz');
    }



}

