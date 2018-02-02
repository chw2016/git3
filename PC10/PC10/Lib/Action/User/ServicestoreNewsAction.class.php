<?php
/**
 *  活动送积分（anyi）
 **/
class ServicestoreNewsAction extends TableAction {
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
             array(
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
             ),
            array(
                'name' => '车架号管理',
                'url'  => U('ServicestoreNews/index')
            ),
        );
    }

    //显示
    public function index(){

        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                'HeadHover' => U('ServicestoreNews/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name' => '导入信息',//2级
                        'url' => U('ServicestoreNews/add_frame', array('token' => $_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '您可以在这里管理数据'
                ),
                'Table_Header' => array(//4级
                    'ID','车架号', '操作'
                ),
                'List_Opt' => array(
                    /* array(
                         'name' => '编辑',
                         'url' => U('Tailg/save_content', array('token' => $_SESSION['token'],'aid'=>$this->_iAid))
                     ),*/

                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url' => U('ServicestoreNews/delete_yanzheng', array('token' => $_SESSION['token']))
                    ),
                ),

            ),
            M('Service_frame')->where($aWhere)->count(),
            M('Service_frame')->field('id,frame')->where($aWhere)
        // array($this,'abc')
        );
    }

    public function add_frame(){

        $oTailgModel = M('Service_frame');
        if(IS_AJAX){
            $ainfons =explode('|',trim($_POST['info']));
            array_pop($ainfons);
            foreach($ainfons as $val){
                $ainfots['frame'] = $val;
               // $ainfots = array_combine($name,$val);
                $ainfots['token'] = $this->_sToken;
                $ainfots['add_time'] = date('Y-m-d H:i:s');
                $aFind = $oTailgModel->where(array('frame'=>$ainfots['frame'],'token'=>$this->_sToken))->find();
                if($aFind){
                    $this->error($ainfots['frame'].'添加失败');
                }
                $istrue =  $oTailgModel->add($ainfots);
            }
            if($istrue){
                $this->success('添加成功',U('ServicestoreNews/index',array('token'=>$this->_sToken)));
            }else{
                $this->error('添加失败');
            }
        }
        $this->assign(array(
            'ExtraBtn' => array(
                array(
                    'url'  => U('ServicestoreNews/index',array('token' => $this->_sToken)),
                    'name' => '返回'
                )
            ),
        ));

        $this->UDisplay('insetthree');
    }


    //删除
    public function delete_yanzheng(){
        $this->del('Service_frame');
    }
}