<?php
/**
 *
 **/
class WeixinfanesAction extends TableAction {
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
        $this->pz	   = D('wxuser_classify');
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '分组管理',
                'url'  => U('Weixinfanes/index', array('token' => $this->_sToken))
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
        $this->table(
            array(
                'abc'=>123,
               /* 'kid' => 'type',*/
                //'id' => 'id',//如果主键不是id，则需要设置
                'tid'=>'type',
                'HeadHover' => U('Weixinfanes/index', array('token' => $_SESSION['token'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加分组',//2级
                        'url'    => U('Weixinfanes/add_content',array('token'=>$_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '分组名称','人数','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('Weixinfanes/xx',array('token'=>$_SESSION['token']))
                                            ), */
                    array(
                        'name' => '查看粉丝',
                        'url'  => U('Weixinfans/index',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '编辑',
                        'url'  => U('Weixinfanes/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Weixinfanes/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('wxuser_classify')->where(array('token'=>$_SESSION['token']))->count(),
            M('wxuser_classify')->field('id,classifyname,number')->where(array('token'=>$_SESSION['token']))->order("id"),
         array($this,'indexOther')
        );

    }
    public function indexOther($aData){
        foreach($aData as $k=>$val){
            $aiUid = M('wxuser_classify')->where(array('id'=>$val['id']))->find();
            $iNumber = intval(M('Wxusers')->where(array('uid'=>$aiUid['uid'],'type'=>$val['id']))->count());
            $aData[$k]['number'] = $iNumber;
        }
        return $aData;
    }

    //定义增改函数
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('Weixinfanes2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
                } */
        $this->$aaa('wxuser_classify',array(
            array('title'=>"分组名称",'type'=>"input",'name'=>"classifyname",'value'=>'classifyname','msg'=>'请填分组名称'),

        ),U('Weixinfanes/index',array('token'=>$_SESSION['token'])),array($this,'otherclassify'));

    }

    public function otherclassify($data){

        $aWxuser = M('Wxuser')->where(array('token'=>$_SESSION['token']))->find();
        $data['uid'] = $aWxuser['id'];
        $data['add_time'] = date('Y-m-d H:i:s');

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
        //M('wxuser_classify')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('wxuser_classify');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaTable('wxuser_classify');
    }
}





