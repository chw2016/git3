<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/10
 * Time: 17:30
 */
class ServiceareaAction extends TableAction{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'User/default/servicearea';

    public function _initialize()
    {
        session('padmin',0);
        $this->areas = D('Service_area');
        $this->staffs = D('Service_staff');
        $this->bulletins = D('Service_bulletin');
        $this->orderal = D('Service_order');
        $this->assesses = D('Service_assess');
        $this->users = D('Service_urse');
        parent::_initialize();
    }

    protected function setHeader(){
        return array(
            array(
                'name' => '小区管理',
                'url'  => U('Servicearea/index', array('token' => $this->_sToken))
            )
        );
    }

    /*小区管理*/
    public function index(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'kid' => 'aid',//如果主键不是id，则需要设置
                'HeadHover' => U('Servicearea/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name'   => '添加小区',
                        'url'    => U('Servicearea/areaCreate')
                    )
                ),
                'tips' => array(
                    '你可以在这里管理区域信息',
                    '进入区域的入口：'.C('site_url').'index.php?g=User&m=Branch&a=index&token='.$this->_sToken.'&modulename=Service_area'
                ),
                'Table_Header' => array(
                    'ID', '小区名称', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '进入小区',
                        'url'  => U('Serviceareas/order',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name' => '编辑',
                        'url'  => U('Servicearea/areaCreate')
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Servicearea/areaDel')
                    )
                )
            ),
            $this->areas->where($aWhere)->count(),
            $this->areas->field('id,ursename')->where($aWhere)
        );
    }
    #添加修改小区
    public function areaCreate(){
        $oAreasModel = $this->areas;
        if(IS_AJAX){
            $iAid = isset($_POST['id'])?$_POST['id']:'';
            if($iAid){
                //修改
                $iTem = $oAreasModel->where(array('id'=>$iAid))->find();
                if(!$iTem) $this->error2('非法操作');
                $_POST['password'] = md5($_POST['password']);
                if($oAreasModel->create()){
                    if($oAreasModel->where(array('id'=>$iAid))->save($_POST)){
                        $this->success2('编辑成功！',U('Servicearea/index'));
                    }else{
                        $this->error2('编辑失败！');
                    }
                }else{
                    $this->error2($oAreasModel->getError());
                }
            }else{
                //add
                $_POST['password'] = md5($_POST['password']);
                if($oAreasModel->create()){
                    if($oAreasModel->add()){
                        $this->success2('添加成功！',U('Servicearea/index'));
                    }else{
                        $this->error2('添加失败！');
                    }
                }else{
                    $this->error2($oAreasModel->getError());
                }
            }
        }else{
            $where = array('id'=>$_GET['aid']);
            $aAreas = $oAreasModel->where($where)->find();
            $this->areaAdd();
            $this->assign(array(
                'aAreas' =>$aAreas,
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Servicearea/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                )
            ));
            $this->UDisplay('areaCreate');
        }
    }
    #选择小区的select
    public function areaAdd(){
        $oAreaModel = $this->areas;
        $this->assign(array(
            'areaed'=>array('-1' => '请选择区域')+Arr::changeIndexToKVMap(
                $oAreaModel->where(array(
                    'token'=>$this->_sToken
                ))->select(),'id','ursename'))
        );
    }

    #删除小区
    public function areaDel(){
        $oAreasModel = $this->areas;
        $iTem = $this->areas->where(array('id'=>FC::G('aid')))->find();
        if (!$iTem) $this->error2('非法操作！');
        if ($oAreasModel->where(array('id' => FC::G('aid')))->delete()) {
            $this->success2('删除成功', U('Servicearea/index', array('token' => $this->token)));
        } else {
            $this->error2('删除失败');
        }
    }

}
