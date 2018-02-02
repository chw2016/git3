
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/1
 * Time: 17:37
 * Title:招聘后台
 */
class RecruitmentAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'User/default/recruitment';
    /**
     *  Token
     **/
    //private $_sToken = null;
    /**
     *  UID
     **/
    //private $_iUID = null;

    /**
     *  顶部
     **/
    public function _initialize()
    {
        parent::_initialize();
        $this->area	   = D('Recruitment_area');
    }
    protected function setHeader(){
        return array(
            array(
                'name' => '区域/分类管理',
                'url'  => U('Recruitment/index', array('token' => $this->_sToken))
            )
        );
    }

    /*
     * 显示区域列表
     * */
    public function index(){

        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                'kid' => 'aid',//如果主键不是id，则需要设置
                'HeadHover' => U('Recruitment/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name'   => '添加区域/分类',
                        'url'    => U('Recruitment/areasAdd',array('token' => $this->_sToken))
                    )
                ),
                'tips' => array(
                    '你可以在这里管理区域/分类信息',
                    '进入区域的入口：'.C('site_url').'index.php?g=User&m=Branch&a=index&token='.$this->_sToken.'&modulename=Recruitment_area'
                ),
                'Table_Header' => array(
                    'ID', '区域/分类名称', '联系电话','管理员','添加时间', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '区域/分类管理',
                        'url'  => U('Recruitments/index')
                    ),
                    array(
                        'name' => '编辑',
                        'url'  => U('Recruitment/areaEdit')
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Recruitment/delarea')
                    ),
                )
            ),
            $this->area->where($aWhere)->count(),
            $this->area->field('id,username,tel,admin,add_time')->where($aWhere)
        );
    }
    #区域添加
    public function areasAdd(){
        $Model       = $this->area;
        if(IS_AJAX){
            $_POST['aid'] = isset($_POST['aid'])?$_POST['aid']:'';
            $_POST['password'] = isset($_POST['password'])?md5($_POST['psaaword']):'';
            $_POST['token']= $this->_sToken;
            if(($aData = $Model->create()) != false){
                if($Model->add()){
                    $this->success2('区域/分类添加成功',U('Recruitment/index',array('token'=>$this->token)));
                }else{
                    $this->error2('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error2($Model->getError());
            }
        }else{
            $this->areates();
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Recruitment/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
            ));
            $this->UDisplay('areacreate');
        }
    }
    public function areates(){
        $oAreaModel = $this->area;
        $this->assign(array(
            'areas'=>array('-1' => '请选择上级区域') + Arr::changeIndexToKVMap(
                $b = $oAreaModel->where(array(
                    'token'=>$this->_sToken,
                ))->select(),'id','username')
        ));
    }

    #区域管理修改
    public function areaEdit(){
        if(IS_AJAX){
            $model = $this->area;
            $iAid = FC::P('id');
            $_POST['token'] = $this->_sToken;
            $where = array(
                'token' =>FC::P('token'),
                'id'=> $iAid
            );
            $Item = $model->where($where)->find();
            if($Item == false) $this->error2('非法操作');
            $pwd = $_POST['password'];
            $_POST['password'] = !empty($pwd)?md5($pwd):'';
            if($model->create()){
                if($model->where($where)->save($_POST)){
                    $this->success2('修改成功',U('Recruitment/index', array('token' => $this->token)));
                }else{
                    $this->error2('修改失败');
                }
            }else{
                $this->error2($model->getError());
            }
        }else{
            $this->areates();
            $var = array('id'=>$_GET['aid'],'token'=>$this->_sToken);
            $info = $this->area->where($var)->find();
            $this->assign(array(
                'info'=>$info,
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Recruitment/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
            ));
            $this->UDisplay('areacreate');
        }
    }
    #删除区域
    public function delarea(){
        $model = $this->area;
        $where = array('id'=>$_REQUEST['aid'],'token'=>$this->_sToken);
        $Item = $model->where($where)->find();
        if(!$Item) $this->error2('非法操作');
        if($model->where($where)->delete()){
            $this->success2('删除成功！',U('Recruitment/index', array('token' => $this->_sToken)));
        }else{
            $this->error2('删除失败！');
        }
    }


}
