<?php
/**
 * Created by PhpStorm.(用户权限管理)
 * User: Administrator
 * Date: 2015/10/8 0008
 * Time: 14:02
 */

class Gta_userAction extends TableAction {
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

    protected function setHeader(){
        return array(
            array(
                'name' => '权限账号管理',
                'url'  => U('Gta_user/index', array('token' => $this->_sToken))
            ),
        );
    }


    /*
     * 加盟管理
     * */
    public function index()
    {
        $aWhere = array('token' => $_SESSION['token']);
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Gta_user/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name'   => '添加权限',
                        'url'    => U('Gta_user/add_user')
                    ),
                ),
                'tips' => array(
                    '你可以在这里管理权限信息',
                    '进入客服权限的入口：'.C('site_url').'index.php?g=User&m=Branch&a=index&token='.$this->_sToken.'&modulename=Gta_access&type=kefu',
                    '进入核算权限的入口：'.C('site_url').'index.php?g=User&m=Branch&a=index&token='.$this->_sToken.'&modulename=Gta_access&type=hesuan',
                    '进入出纳权限的入口：'.C('site_url').'index.php?g=User&m=Branch&a=index&token='.$this->_sToken.'&modulename=Gta_access&type=chuna',
                    '进入会计权限的入口：'.C('site_url').'index.php?g=User&m=Branch&a=index&token='.$this->_sToken.'&modulename=Gta_access&type=kuaiji'
                ),
                'Table_Header' => array(
                    'ID', '权限职位','权限账号','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Gta_user/updata_uses')
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Gta_user/del_uses')
                    ),
                ),
            ),
            M('Gta_access')->where($aWhere)->count(),
            M('Gta_access')->field('id,accessname,username')->where($aWhere)
        );
    }

    public function set_content($aaa){
        $this->$aaa('Gta_access',array(
            array('type'=>'select','title'=>"权限名称",'name'=>"accesstype",'value'=>'accesstype','msg'=>'请选择权限','many'=>array(
                array('content'=>'请选择权限'),
                array('value'=>'0', 'content'=>'客服'),
                array('value'=>'1','content'=>'核算'),
                array('value'=>'2','content'=>'出纳'),
                array('value'=>'3','content'=>'会计'),
                )),
            array('title'=>"权限账号",'type'=>"input",'name'=>"username",'value'=>'username','msg'=>'请填写权限账号','bast'=>''),
            array('title'=>"权限密码",'type'=>"input",'name'=>"mpassword",'value'=>'mpassword','msg'=>'请填写权限密码','bast'=>''),

        ),U('Gta_user/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));
    }

    public function bbc($data){
        if(M('Gta_access')->where(array(
            'token'=>$this->_sToken,
            'accesstype'=>$data['accesstype']
        ))->find()){
            $this->error2('该权限已存在不需在添加');
        }
        $data['save_time'] = date('Y-m-d H:i:s');
        $data['password'] = md5($data['mpassword']);
        switch($data['accesstype']){
            case 0;$data['accessname'] = '客服';break;
            case 1;$data['accessname'] = '核算';break;
            case 2;$data['accessname'] = '出纳';break;
            case 3;$data['accessname'] = '会计';break;
        }
        return $data;
    }

    public function add_user(){
        $this->set_content(add);
    }

    public function set_contents($aaa){
        $this->$aaa('Gta_access',array(
            array('type'=>'select','title'=>"权限名称",'name'=>"accesstype",'value'=>'accesstype','readonly'=>1,'msg'=>'请选择权限','many'=>array(
                array('content'=>'请选择权限'),
                array('value'=>'0', 'content'=>'客服'),
                array('value'=>'1','content'=>'核算'),
                array('value'=>'2','content'=>'出纳'),
                array('value'=>'3','content'=>'会计'),
            )),
            array('title'=>"权限账号",'type'=>"input",'name'=>"username",'value'=>'username','msg'=>'请填写权限账号','bast'=>''),
            array('title'=>"权限密码",'type'=>"input",'name'=>"mpassword",'value'=>'mpassword','msg'=>'请填写权限密码','bast'=>''),

        ),U('Gta_user/index',array('token'=>$_SESSION['token'])),array($this,'bbcs'));
    }


    public function bbcs($data){
        $data['save_time'] = date('Y-m-d H:i:s');
        $data['password'] = md5($data['mpassword']);
        return $data;
    }

    public function updata_uses(){
        $this->set_contents(Edit);
    }
    //删除
    public function del_uses(){
        $this->del('Gta_access');
    }







}