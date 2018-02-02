<?php
/**
 * Created by PhpStorm.台铃
 * User: zhou
 * Date: 2015/6/17
 * Time: 15:51
 */

class TailgsAction extends TableAction
{
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
        // $this->pz = D('Tailg_user');
    }

    //一级
    protected function setHeader()
    {
        return array(
            array(
                'name' => '管理员管理',
                'url' => U('Tailg/index', array('token' => $this->_sToken))
            ),

        );
    }

    //显示
    public function index()
    {
        $this->table(
            array(
               //如果主键不是id，则需要设置
                'HeadHover' => U('Tailg/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name' => '添加管理员',//2级
                        'url' => U('Tailgs/add_admin', array('token' => $_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息；',
                    '进入用户管理的入口：'.C('site_url').'index.php?g=User&m=Branch&a=index&token='.$this->_sToken.'&modulename=Tailg_admin'
                ),
                'Table_Header' => array(//4级
                    'ID','区域', '管理员账号','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('Tailgs/save_admin', array('token' => $_SESSION['token']))
                    ),
                    /*连接上可能会带其他的参数，则参考如下   （案例Tailg/index）*/
                    array(
                        'name' => '用户管理',
                        'tkey' =>'aid',
                        'tval'=>'id',
                        'url' => U('Tailg/index', array('token' => $_SESSION['token']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url' => U('Tailgs/delete_admin', array('token' => $_SESSION['token']))
                    ),
                ),

            ),
            M('Tailg_admin')->where(array('token' => $_SESSION['token']))->count(),
            M('Tailg_admin')->field('id,address,administrator')->where(array('token' => $_SESSION['token']))
        // array($this,'abc')
        );
    }

    public function add_save($aaa){
        $this->$aaa('Tailg_admin',array(
            /*
             * 如果有需求要在input框中提示信息,则加入一个'msg'的键值，表示input 里面的placeholder属性；
             * 如果在input框后面有需求加备注，则加一个'bast'的键值，作用：input后面显示的一个内容，作为一种提醒信息；
             * 如果要求input框是只读的，则在后面添加一个键值为‘readonly’,表示input为只读框。
             * */
            array('title'=>"admin",'type'=>"input",'name'=>"administrator",'value'=>'administrator','msg'=>'请填写管理员的账号','bast'=>'','readonly'=>''),
            array('title'=>"密码",'type'=>"password",'name'=>"password",'value'=>'password'),
            array('title'=>"区域",'type'=>"input",'name'=>"address",'value'=>'address','msg'=>'请填写管理员所在的区域','bast'=>'','readonly'=>''),

        ),U('Tailgs/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
        $data['token'] = $this->token;
        $data['password']=MD5($data['password']);
        $data['add_time']= date('Y-m-d H:i:s');
        return $data;
    }
    //添加
    public function add_admin(){
        $this->add_save(add);
    }
    //编辑
    public function save_admin(){
        $this->add_save(Edit);
    }


    //删除
    public function delete_content(){
        $this->del('Tailg_admin');
    }

}