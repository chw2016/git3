<?php
/**
 *  活动送积分（anyi）
 **/
class ServiceactiveAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

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
    }
    //一级
    protected function setHeader(){
        return array(
           /* array(
                'name' => '网点管理',
                'url'  => U('ServicestoreNew/index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '援救订单管理',
                'url'  => U('ServicestoreNew/orders', array('token' => $this->_sToken))
            ),
            array(
                'name' => '公司职员管理',
                'url'  => U('ServicestoreNew/staff', array('token' => $this->_sToken))
            ),
            array(
                'name' => '积分设置',
                'url'  => U('ServicestoreNew/integralInstall', array('token' => $this->_sToken))
            ),
            array(
                'name' => '积分商城',
                'url'  => U('ServicestoreNew/store', array('token' => $this->_sToken))
            ),
            array(
                'name' => '会员管理',
                'url'  => U('ServicestoreNew/menber', array('token' => $this->_sToken))
            ),*/
            array(
                'name' => '活动推广管理',
                'url'  => U('Serviceactive/index')
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
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Serviceactive/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加',//2级
                        'url'    => U('Serviceactive/add_content',array('token'=>$_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '描述：此应用主要功能为，前端分享下面的活动，得到相应的积分；',
                    '注意：下面的列表是按添加时间的先后顺序排序的，最新添加的在最前面；',
                    '温馨提示：在前端活动显示的是最新添加的，状态为开启的。'
                ),
                'Table_Header' => array(//4级
                    'ID', '活动主题', '活动得取积分','活动添加时间' , '活动状态','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('Cs/xx',array('token'=>$_SESSION['token']))
                                            ), */
                    /*连接上可能会带其他的参数，则参考如下*/
                    array(
                        'name' => '编辑',
                        'url'  => U('Serviceactive/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Serviceactive/delete_content',array('token'=>$_SESSION['token']))
                    ),
                    /*array(
                        'name' => '查看详情',
                        'url'  => U('Serviceactive/info',array('token'=>$_SESSION['token']))
                    ),*/
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Service_active')->where(array('token'=>$_SESSION['token']))->count(),
            M('Service_active')->field('id,title,store,add_time,status')->where(array('token'=>$_SESSION['token']))->order("id"),
        array($this,'abc')
        );
    }
    public function abc($data){
        foreach($data as $k=>$v){
            switch ($v['status']){
                case 0: $data[$k]['status']= '开启';break;
                case 1: $data[$k]['status']= '关闭';break;
                default:$data[$k]['status']= '其他';break;
            }
        }
        return $data;
    }

    public function set_active($aaa){
        $this->$aaa('Service_active',array(
            array('title'=>'活动主题','type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写活动的主题','bast'=>''),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
            array('title'=>"活动积分",'type'=>"number",'name'=>"store",'msg'=>'','value'=>'store','msg'=>'请填写分享此活动的所得的积分数',),
            array('type'=>'radio','title'=>"是否开启",'name'=>"status",'value'=>'status','msg'=>'','many'=>array(
                array('value'=>'0','content'=>'开启'),
                array('value'=>'1','content'=>'关闭'),
            )),  //	array('type'=>'checkbox','title'=>'特殊复选框', 'name'=>'jy', 'value'=>'jy','msg'=>'请填写标题咯','many'=>$list),
        ),U('Serviceactive/index',array('token'=>$_SESSION['token'])),array($this,'activeinfo'));
    }

    public function activeinfo($data){
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['tid'] = $this->_iTid;
        return $data;
    }

    //添加
    public function add_content(){
        $this->set_active(add);
    }
    //编辑
    public function save_content(){
        $this->set_active(Edit);
    }
    //删除
    public function delete_content(){
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Service_active');
    }
}