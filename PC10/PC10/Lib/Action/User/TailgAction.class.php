<?php
/**
 * Created by PhpStorm.台铃
 * User: zhou
 * Date: 2015/6/17
 * Time: 15:51
 */

class TailgAction extends TableAction
{
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

    /**
     *  Token
     **/
    //private $_sToken = null;
    private $_iAid = null;
    /**
     *  UID
     **/
    //private $_iUID = null;//
    public function _initialize()
    {
        $this->_iAid = !empty($_GET['branch_id'])? $_GET['branch_id']:$_GET['aid'];
        parent::_initialize();
       // $this->pz = D('Tailg_user');
    }

    //一级
    protected function setHeader()
    {
        return array(
            array(
                'name' => '用户管理',
                'url' => U('Tailg/index', array('token' => $this->_sToken))
            ),

        );
    }

    //显示
    public function index()
    {

        $info = M('Tailg_admin')->where(array('id'=>$this->_iAid))->find();
        $aWhere['token'] =$_SESSION['token'];
        $aWhere['aid'] = $this->_iAid;

           	//搜索
        if(IS_POST){
            $_POST=$_REQUEST;
            $aWhere=$this->search($_POST);
            $aWhere['token'] =$_SESSION['token'];
            $aWhere['aid'] = $this->_iAid;
            //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
        }//结束
        $this->table(
            array(
                'HeadHover' => U('Tailg/index', array('token' => $this->_sToken,'aid'=>$this->_iAid)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name' => '导入信息',//2级
                        'url' => U('Tailg/add_content', array('token' => $_SESSION['token'],'aid'=>$this->_iAid))
                    )
                ),
                'tips' => array(//3级
                    '亲爱的“'.$info['administrator'].'”,欢迎您，您可以在这里管理信息；',
                    '每个用户的初始密码为：000000。'
                ),
                'Table_Header' => array(//4级
                    'ID','用户名', '手机号', 'sim号','IMEI', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('Tailg/save_content', array('token' => $_SESSION['token'],'aid'=>$this->_iAid))
                    ),
                    array(
                        'name' => '查看轨迹',
                        'tkey' =>'username',
                        'tval'=>'username',
                        'url' => U('Wap/Tailg/map', array('token' => $_SESSION['token'],'aid'=>$this->_iAid))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url' => U('Tailg/delete_content', array('token' => $_SESSION['token'],'aid'=>$this->_iAid))
                    ),
                ),
                        		//搜索
                'search'=>array(
                    array('title'=>'IMEI','name'=>'li_IMEI','placeholder'=>'请输入您要查询的IMEI','search'=>'查询'),//li是Table里判断条件 name是子段
                    array('title'=>'用户名名称','name'=>'li_username','placeholder'=>'请输入您要查询的用户名','search'=>'查询'),//eq是Table里判断条件 name是子段

                )//结束
            ),
            M('Tailg_user')->where($aWhere)->count(),
            M('Tailg_user')->field('id,username,phone,sim,IMEI')->where($aWhere)
        // array($this,'abc')
        );
    }

    public function add_content(){

        $oTailgModel = M('Tailg_user');
        if(IS_AJAX){
            $aInfo =explode("\n",$_POST['info']);
            //array_pop($aInfo);
            $name = array('username','sim','phone','IMEI');

            foreach($aInfo as $val){
                $ainfons = explode('|',trim($val));
                $ainfons = array_values($ainfons);
                $ainfots = array_combine($name,$ainfons);
                $ainfots['token'] = $this->_sToken;
                $ainfots['add_time'] = date('Y-m-d H:i:s');
                $ainfots['aid'] = $this->_iAid;
                $aFind = $oTailgModel->where(array('username'=>$ainfots['username'],'token'=>$this->_sToken,'aid'=>$this->_iAid))->find();
                if($aFind){
                    $this->error($ainfots['username'].'的用户添加失败');
                }
                $istrue =  $oTailgModel->add($ainfots);
            }
            if($istrue){
                $this->success('添加成功',U('Tailg/index',array('token'=>$this->_sToken,'aid'=>$this->_iAid)));
            }else{
                $this->error('添加失败');
            }
        }
        $this->assign(array(
            'ExtraBtn' => array(
                array(
                    'url'  => U('Tailg/index',array('token' => $this->_sToken,'aid'=>$this->_iAid)),
                    'name' => '返回'
                )
            ),
        ));

        $this->UDisplay('inset');
    }

    public function add_save($aaa){
        $this->$aaa('Tailg_user',array(
            /*
             * 如果有需求要在input框中提示信息,则加入一个'msg'的键值，表示input 里面的placeholder属性；
             * 如果在input框后面有需求加备注，则加一个'bast'的键值，作用：input后面显示的一个内容，作为一种提醒信息；
             * 如果要求input框是只读的，则在后面添加一个键值为‘readonly’,表示input为只读框。
             * */
            array('title'=>"用户名",'type'=>"input",'name'=>"username",'value'=>'username','msg'=>'请填写用户的名称','bast'=>'','readonly'=>''),
            array('title'=>"密码",'type'=>"password",'name'=>"password",'value'=>'password'),
            array('title'=>"手机号码",'type'=>"input",'name'=>"phone",'value'=>'phone','msg'=>'请填写用户的电话号码','bast'=>'','readonly'=>''),
            array('title'=>"IMEI",'type'=>"input",'name'=>"IMEI",'value'=>'IMEI','msg'=>'请填写用户的IMEI','bast'=>'','readonly'=>''),
            array('title'=>"sim",'type'=>"input",'name'=>"sim",'value'=>'sim','msg'=>'请填写用户的IMEI','bast'=>'','readonly'=>''),
        ),U('Tailg/index',array('token'=>$_SESSION['token'],'aid'=>$this->_iAid)),array($this,'bbc'));

    }
    public function bbc($data){
        $data['aid'] = $this->_iAid;
        $data['token'] = $this->token;
        $data['add_time']= date('Y-m-d H:i:s');
        return $data;
    }

    //编辑
    public function save_content(){
        $this->add_save(Edit);
    }

    //删除
    public function delete_content(){
        $this->del('Tailg_user');
    }

}