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
            ),
            array(
                'name' => '维修订单管理',
                'url'  => U('Servicearea/order', array('token' => $this->_sToken))
            ),
            array(
                'name' => '会员管理',
                'url'  => U('Servicearea/urses', array('token' => $this->_sToken))
            ),
            array(
                'name' => '公告管理',
                'url'  => U('Servicearea/bulletin', array('token' => $this->_sToken))
            ),
            array(
                'name' => '评价管理',
                'url'  => U('Servicearea/assess', array('token' => $this->_sToken))
            ),
            array(
                'name' => '维修师管理',
                'url'  => U('Servicearea/staff', array('token' => $this->_sToken))
            )
        );
    }

    /*小区管理*/
    public function index(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
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
                    'ID', '小区名称','管理电话','小区地址', '操作'
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
            $this->areas->field('id,ursename,tel,address')->where($aWhere)
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
            $where = array('id'=>$_GET['id']);
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
        $iTem = $this->areas->where(array('id'=>FC::G('id')))->find();
        if (!$iTem) $this->error2('非法操作！');
        if ($oAreasModel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('Servicearea/index', array('token' => $this->token)));
        } else {
            $this->error2('删除失败');
        }
    }
    /*
     * 维修订单管理
     * */
    public function order(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Servicearea/order', array('token' => $this->_sToken)),
                /*'Head_Opt' => array(
                    array(
                        'name'   => '添加招聘',
                        'url'    => U('Servicearea/areaCreate')
                    )
                ),*/
                'tips' => array(
                    '你可以在这里管理订单信息'
                ),
                'Table_Header' => array(
                    'ID', '小区名称','下单人','地址','下单时间','状态', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '订单处理',
                        'url'  => U('Servicearea/process')
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Servicearea/orderDel')
                    )
                )
            ),
            $this->orderal->where($aWhere)->count(),
            $this->orderal->field('id,aid,openid,address,add_time,stauts')->where($aWhere),
            array($this, '_handle')
        );
    }
    public function _handle($aData){
        $oAreaModel = D('Service_area');
        $oUserModel = D('Service_urse');
        foreach($aData as $key=>$aVal){
            $aArea = $oAreaModel->where(array('id'=>$aVal['aid']))->find();
            $aUsers = $oUserModel->where(array('token'=>$this->token,'openid'=>$aVal['openid'],'aid'=>$aVal['aid']))->find();
            $aData[$key]['aid'] = $aArea['ursename'];
            $aData[$key]['openid'] = $aUsers['name'];
            /*订单状态(0表示未处理，1表示被提醒过，2表示处理，3表示评价过*/
            if($aVal['stauts']== 0){
                $aData[$key]['stauts'] = '未处理';
            }elseif($aVal['stauts']== 1){
                $aData[$key]['stauts'] = '被提醒过';
            }elseif($aVal['stauts']== 2){
                $aData[$key]['stauts'] = '已处理';
            }elseif($aVal['stauts']== 3){
                $aData[$key]['stauts'] = '评价过';
            }
        }
        return $aData;
    }
    #订单处理
    public function process(){
        $oUserMOdel = $this->users;
        $oAreasMOdel = $this->areas;
        $oStaffsMOdel = $this->staffs;
        $oOrdersMOdel = $this->orderal;
        $aWhere = array('id'=>$_GET['id'],'token'=>$this->_sToken);
        $iTem = $oOrdersMOdel->where($aWhere)->find();

        if(IS_AJAX){
            $iTems= $oOrdersMOdel->where(array('id'=>FC::P('id')))->find();
            if(!$iTems) $this->error2('非法操作');
            $_POST['stauts'] = 2;
            if($oOrdersMOdel->where(array('id'=>FC::P('id')))->save($_POST)){
                FC::mgs($token=$this->_sToken,$openid=$iTem['openid'],$content='您的报修订单已处理，请注意查看，谢谢！');
                $this->success2('处理成功', U('Servicearea/order', array('token' => $this->token,'id'=>FC::P('id'))));
            }else{
               $this->error2('分配失败！');
            }
        }else{
            $this->assign(array(
                'aNiname' => array_merge(array('-1' => '请选择维修师'),Arr::changeIndexToKVMap(
                    $oStaffsMOdel->where(array(
                        'token'=>$this->_sToken,
                        'aid'=>$iTem['aid']
                    ))->select(),'id','staff_name')),
                'aAreas'=>$oAreasMOdel->where(array('id'=>$iTem['aid']))->find(),
                'aUsers'=>$oUserMOdel->where(array('token'=>$this->_sToken,'openid'=>$iTem['openid'],'aid'=>$iTem['aid']))->find(),
                'aOrders' =>$iTem
            ));
            $this->UDisplay('process');
        }
    }
    #删除订单
    public function orderDel(){
        $oOrdersMOdel = $this->orderal;
        $iTem = $oOrdersMOdel->where(array('id'=>FC::G('id')))->find();
        if (!$iTem) $this->error2('非法操作！');
        if ($oOrdersMOdel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('Servicearea/order', array('token' => $this->token)));
        } else {
            $this->error2('删除失败');
        }
    }
    /*
     * 会员管理
     * */
    public function urses(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Servicearea/urses', array('token' => $this->_sToken)),
                /*'Head_Opt' => array(
                    array(
                        'name'   => '添加招聘',
                        'url'    => U('Servicearea/areaCreate')
                    )
                ),*/
                'tips' => array(
                    '你可以在这里管理会员'
                ),
                'Table_Header' => array(
                    'ID', '会员名','电话','小区名称','操作'
                ),
                'List_Opt' => array(
                    /*array(
                        'name' => '订单处理',
                        'url'  => U('Servicearea/process')
                    ),*/
                    array(
                        'name' => '删除',
                        'url'  => U('Servicearea/ursesDel')
                    )
                )
            ),
            $this->users->where($aWhere)->count(),
            $this->users->field('id,name,tel,aid')->where($aWhere),
            array($this,'_users')
        );
    }
    public function _users($aData){
        $oAreaModel = D('Service_area');
        foreach($aData as $key=>$aVal){
            $aArea = $oAreaModel->where(array('id'=>$aVal['aid']))->find();
            $aData[$key]['aid'] = $aArea['ursename'];
        }
        return $aData;
    }
    #删除会员
    public function ursesDel(){
        $oUserMOdel = $this->users;
        $iTem = $oUserMOdel->where(array('id'=>FC::G('id')))->find();
        if (!$iTem) $this->error2('非法操作！');
        if ($oUserMOdel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('Servicearea/urses', array('token' => $this->token)));
        } else {
            $this->error2('删除失败');
        }
    }
    /*
     * 公告管理
     * */
    public function bulletin(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Servicearea/bulletin', array('token' => $this->_sToken)),
               'Head_Opt' => array(
                    array(
                        'name'   => '添加公告',
                        'url'    => U('Servicearea/bulletinCreate')
                    )
                ),
                'tips' => array(
                    '你可以在这里管理公告'
                ),
                'Table_Header' => array(
                    'ID', '小区名称','公告主题','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Servicearea/bulletinCreate')
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Servicearea/bulletinDel')
                    )
                )
            ),
            $this->bulletins->where($aWhere)->count(),
            $this->bulletins->field('id,aid,title')->where($aWhere),
            array($this,'_bulletin')
        );
    }
    public function _bulletin($aData){
        $oAreaModel = D('Service_area');
        foreach($aData as $key=>$aVal){
            $aArea = $oAreaModel->where(array('id'=>$aVal['aid']))->find();
            $aData[$key]['aid'] = $aArea['ursename'];
        }
        return $aData;
    }
    #添加修改公告
    public function bulletinCreate(){
        $oBulletinsMOdel = $this->bulletins;
        if(IS_AJAX){
            $iAid = isset($_POST['id'])?$_POST['id']:'';
            if($iAid){
                //修改
                $iTem = $oBulletinsMOdel->where(array('id'=>$iAid))->find();
                if(!$iTem) $this->error2('非法操作');
                if($oBulletinsMOdel->create()){
                    if($oBulletinsMOdel->where(array('id'=>$iAid))->save($_POST)){
                        $this->success2('编辑成功！',U('Servicearea/bulletin'));
                    }else{
                        $this->error2('编辑失败！');
                    }
                }else{
                    $this->error2($oBulletinsMOdel->getError());
                }
            }else{
                //add
                if($oBulletinsMOdel->create()){
                    if($oBulletinsMOdel->add()){
                        $this->success2('添加成功！',U('Servicearea/bulletin'));
                    }else{
                        $this->error2('添加失败！');
                    }
                }else{
                    $this->error2($oBulletinsMOdel->getError());
                }
            }
        }else{
            $where = array('id'=>$_GET['id']);
            $aAreas = $oBulletinsMOdel->where($where)->find();
            $this->areaAdd();
            $this->assign(array(
                'aAreas' =>$aAreas,
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Servicearea/bulletin', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                )
            ));
            $this->UDisplay('bulletinCreate');
        }
    }
    #删除公告
    public function bulletinDel(){
        $oBulletinsMOdel = $this->bulletins;
        $iTem = $oBulletinsMOdel->where(array('id'=>FC::G('id')))->find();
        if (!$iTem) $this->error2('非法操作！');
        if ($oBulletinsMOdel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('Servicearea/bulletin', array('token' => $this->token)));
        } else {
            $this->error2('删除失败');
        }
    }
    /*
     * 维修师管理
     * */
    public function staff(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Servicearea/staff', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name'   => '新增维修师',
                        'url'    => U('Servicearea/staffCreate')
                    )
                ),
                'tips' => array(
                    '你可以在这里管理维修师'
                ),
                'Table_Header' => array(
                    'ID', '小区名称','维修师','电话','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Servicearea/staffCreate')
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Servicearea/staffDel')
                    )
                )
            ),
            $this->staffs->where($aWhere)->count(),
            $this->staffs->field('id,aid,staff_name,tel')->where($aWhere),
            array($this,'_staff')
        );
    }
    public function _staff($aData){
        $oAreaModel = D('Service_area');
        foreach($aData as $key=>$aVal){
            $aArea = $oAreaModel->where(array('id'=>$aVal['aid']))->find();
            $aData[$key]['aid'] = $aArea['ursename'];
        }
        return $aData;
    }
    #添加维修师
    public function staffCreate(){
        $oStaffsMOdel = $this->staffs;
        if(IS_AJAX){
            $iAid = isset($_POST['id'])?$_POST['id']:'';
            if($iAid){
                //修改
                $iTem = $oStaffsMOdel->where(array('id'=>$iAid))->find();
                if(!$iTem) $this->error2('非法操作');
                if($oStaffsMOdel->create()){
                    if($oStaffsMOdel->where(array('id'=>$iAid))->save($_POST)){
                        $this->success2('编辑成功！',U('Servicearea/staff'));
                    }else{
                        $this->error2('编辑失败！');
                    }
                }else{
                    $this->error2($oStaffsMOdel->getError());
                }
            }else{
                //add
                if($oStaffsMOdel->create()){
                    if($oStaffsMOdel->add()){
                        $this->success2('添加成功！',U('Servicearea/staff'));
                    }else{
                        $this->error2('添加失败！');
                    }
                }else{
                    $this->error2($oStaffsMOdel->getError());
                }
            }
        }else{
            $where = array('id'=>$_GET['id']);
            $aAreas = $oStaffsMOdel->where($where)->find();
            $this->areaAdd();
            $this->assign(array(
                'aAreas' =>$aAreas,
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Servicearea/staff', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                )
            ));
            $this->UDisplay('staffCreate');
        }
    }
    #删除维修师
    public function staffDel(){
        $oStaffsMOdel = $this->staffs;
        $iTem = $oStaffsMOdel->where(array('id'=>FC::G('id')))->find();
        if (!$iTem) $this->error2('非法操作！');
        if ($oStaffsMOdel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('Servicearea/staff', array('token' => $this->token)));
        } else {
            $this->error2('删除失败');
        }
    }
    /*
     * 评价管理
     * */
    public function assess(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'kid' => 'aid',//如果主键不是id，则需要设置
                'HeadHover' => U('Servicearea/assess', array('token' => $this->_sToken)),
                /*'Head_Opt' => array(
                    array(
                        'name'   => '添加公告',
                        'url'    => U('Servicearea/bulletinCreate')
                    )
                ),*/
                'tips' => array(
                    '你可以在这里管理评价'
                ),
                'Table_Header' => array(
                    'ID', '小区名称','维修师','评价度','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看',
                        'url'  => U('Servicearea/seeAsseaa')
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Servicearea/assessDel')
                    )
                )
            ),
            $this->assesses->where($aWhere)->count(),
            $this->assesses->field('id,aid,staff_id,rating')->where($aWhere),
            array($this,'_asseaa')
        );
    }
    public function _asseaa($aData){
        $oAreaModel = D('Service_area');
        $oStaffModel = D('Service_staff');
        foreach($aData as $key=>$aVal){
            $aArea = $oAreaModel->where(array('id'=>$aVal['aid']))->find();
            $aStaff = $oStaffModel->where(array('id'=>$aVal['staff_id']))->find();
            $aData[$key]['aid'] = $aArea['ursename'];
            $aData[$key]['staff_id'] = $aStaff['staff_name'];
            if($aVal['rating'] ==0){
                $aData[$key]['rating'] = '好评';
            }elseif($aVal['rating'] ==1){
                $aData[$key]['rating'] = '一般';
            }elseif($aVal['rating'] ==2){
                $aData[$key]['rating'] = '差评';
            }
        }
        return $aData;
    }
    #查看评价
    public function seeAsseaa(){
        $oAaaeaaMOdel = $this->assesses;
        $aWhere = array('id'=>$_GET['id']);
        $aValure = $oAaaeaaMOdel->where($aWhere)->find();
        $this->assign(array(
            'aAssess'=>$aValure
        ));
        $this->UDisplay('seeAsseaa');
    }
    #删除评价
    public function assessDel(){
        $oAaaeaaMOdel = $this->assesses;
        $iTem = $oAaaeaaMOdel->where(array('id'=>FC::G('id')))->find();
        if (!$iTem) $this->error2('非法操作！');
        if ($oAaaeaaMOdel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('Servicearea/assess', array('token' => $this->token)));
        } else {
            $this->error2('删除失败');
        }
    }
}
