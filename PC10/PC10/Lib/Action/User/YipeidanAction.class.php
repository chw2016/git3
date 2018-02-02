<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2015/7/1
 * Time: 17:28
*/
class YipeidanAction extends TableAction
{
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

    /**
     *  Token
     **/
    //private $_sToken = null;

    public function _initialize()
    {
        //$this->_sToken = $_SESSION['token'];
        parent::_initialize();

    }

    //一级
    protected function setHeader()
    {
        return array(
            array(
                'name' => '入驻商户管理',
                'url' => U('Yipeidan/index', array('token' => $this->_sToken))
            ),
        );
    }

    //显示
    public function index()
    {
        $this->table(
            array(
                // 'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Yipeidan/index', array('token' => $this->_sToken)),//栏目样式
               /* 'Head_Opt' => array(
                    array(
                        'name' => '添加分类',//2级
                        'url' => U('Road/traffic_add', array('token' => $_SESSION['token']))
                    )
                ),*/
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID','姓名','联系方式','微信号','经营品类','添加时间', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '详情',
                        'url' => U('Yipeidan/index_save', array('token' => $_SESSION['token']))
                    ),
                    array(
                        'type' => 1,
                        'name' => '删除',
                        'url' => U('Yipeidan/index_delete', array('token' => $_SESSION['token']))
                    ),
                ),
            ),
            M('Product_shoplogin')->where(array('token' => $_SESSION['token']))->count(),
            M('Product_shoplogin')->field('id,name,tel,weixin,shopclassfiy,add_time')->where(array('token' => $_SESSION['token']))->order("id")
        // array($this,'abc')
        );
    }

    public function set_index($aaa){
        $this->$aaa('Product_shoplogin',array(
            array('title'=>"姓名",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写姓名','bast'=>''),
            array('title'=>"联系方式",'type'=>"input",'name'=>"tel",'value'=>'tel','msg'=>'请填写联系方式','bast'=>''),
            array('title'=>"微信号",'type'=>"input",'name'=>"weixin",'value'=>'weixin','msg'=>'请填写微信号','bast'=>''),
            array('title'=>"经营品类",'type'=>"input",'name'=>"shopclassfiy",'value'=>'shopclassfiy','msg'=>'请填写所经营的品类','bast'=>''),
            array('title'=>"留言",'type'=>"textarea2",'name'=>"message",'value'=>'message'),
        ),U('Yipeidan/index',array('token'=>$_SESSION['token'],'id'=>$_GET['cid'])));
    }
    public function index_save(){
        $this->set_index(Edit);
    }
    public function index_delete(){
        $this->del('Product_shoplogin');
    }


}