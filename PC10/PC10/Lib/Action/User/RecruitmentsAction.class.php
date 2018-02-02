<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/3
 * Time: 17:24
 * Title:招聘区域后台
 */
class RecruitmentsAction extends TableAction{

    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'User/default/recruitments';
    /**
     *  UID
     **/
    private $_iAid = null;


    public function _initialize()
    {
        $this->_iAid = !empty($_GET['branch_id'])? $_GET['branch_id']:$_GET['aid'];
        $this->office = D('Recruitment_office');
        $this->message = D('Recruitment_message');
        $this->info = D('Recruitment_info');
        parent::_initialize();

    }

    protected function setHeader(){
        $arrayOne = array(array(
            'name' => '区域/分类管理',
            'url'  => U('Recruitment/index', array('token' => $this->_sToken))
        ));
        $arrayTwo = array(
            array(
                'name' => '招聘信息管理',
                'url'  => U('Recruitments/index', array('token' => $this->_sToken,'aid'=>$this->_iAid))
            ),
            array(
                'name' => '职位管理',
                'url'  => U('Recruitments/office', array('token' => $this->_sToken,'aid'=>$this->_iAid))
            ),
            array(
                'name' => '招聘信息基础配置',
                'url'  => U('Recruitments/messageInfoAdd', array('token' => $this->_sToken,'aid'=>$this->_iAid))
            ),
            array(
                'name' => '应聘者管理',
                'url'  => U('Recruitments/candidate', array('token' => $this->_sToken,'aid'=>$this->_iAid))
            )
        );
        if(!session('padmin')){
            $arrayTwo = array_merge($arrayOne, $arrayTwo);
        }
        return $arrayTwo;
    }

    /*
     * 显示应聘消息列表
     * */
    public function index(){

        //print_r($this->office);exit;
        $aWhere = array('token'=>$this->_sToken,'aid'=>$this->_iAid);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Recruitments/index', array('token' => $this->_sToken,'aid'=>$this->_iAid)),
                'Head_Opt' => array(
                    array(
                        'name'   => '添加招聘',
                        'url'    => U('Recruitments/messageAdd',array('aid'=>$this->_iAid))
                    )
                ),
                'tips' => array(
                    '你可以在这里管理招聘信息'
                ),
                'Table_Header' => array(
                    'ID','区域/分类', '职位名称','添加时间','发布人', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Recruitments/messageEdit',array('aid'=>$this->_iAid))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Recruitments/delMessage',array('aid'=>$this->_iAid))
                    )
                )
            ),
            $this->message->where($aWhere)->count(),
            $this->message->field('id,aid,office_id,add_time,addname')->where($aWhere),
            array($this,'_meassage')
        );
    }
    #数据处理
    public function _meassage($aData){
        $oAreaModel = M('Recruitment_area');
        $oOfficeModel = $this->office;
        foreach($aData as $key =>$aVal){
            $aArea = $oAreaModel->where(array('id'=>$aVal['aid']))->find();
            $aOffice = $oOfficeModel->where(array('id'=>$aVal['office_id']))->find();
            $aData[$key]['aid'] = $aArea['username'];
            $aData[$key]['office_id'] = $aOffice['oname'];
        }
        return $aData;
    }
    /*
     * 添加招聘信息*/
    public function messageAdd(){
        $oMessageModel = $this->message;
        if(IS_AJAX){
            $_POST['aid'] = $this->_iAid;
            if($oMessageModel->where(array('token'=>$this->_sToken,'aid'=>$this->_iAid,'office_id'=>$_POST['office_id']))->find()){
                echo json_encode(array('info'=>'区域/分类内该职位详情已添加'));
            }else{
                if($oMessageModel->create()){
                    if($oMessageModel->add()){
                        $this->success2('添加成功',U('Recruitments/index',array('token'=>$this->_sToken,'aid'=>$this->_iAid)));
                    }else{
                        echo json_encode(array('info'=>'添加失败'));
                    }
                }else{
                    echo json_encode(array('info'=>'非法操作'.$oMessageModel->getError()));
                }
            }

        }else{
           $this->officeadd();
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Recruitments/index', array('token' => $this->_sToken,'aid'=>$this->_iAid)),
                        'name' => '返回'
                    )
                )
            ));
            $this->UDisplay('messagecreate');
        }
    }
    public function officeadd(){
        $oOfficeModel = $this->office;
        $this->assign(array(
                'office'=>array('-1' => '请选择职位') + Arr::changeIndexToKVMap(
                    $oOfficeModel->where(array(
                        'token'=>$this->_sToken,
                        'aid'=>$this->_iAid
                    ))->select(),'id','oname')
        ));

    }
    /*编辑*/
    public function messageEdit(){
        $oMessageModel = $this->message;
        if(IS_AJAX){
            $awhere = array('id'=>$_POST['id'],'token'=>$this->_sToken);
            $Item = $oMessageModel->where($awhere)->find();
            if($Item == false) $this->error2('非法操作');

            if($oMessageModel->create()){
                if($oMessageModel->where($awhere)->save($_POST)){
                    $this->success2('编辑成功',U('Recruitments/index',array('token'=>$this->_sToken,'aid'=>$this->_iAid)));
                }else{
                    $this->error2('编辑失败');
                }
            }else{
                $this->error2('非法操作'.$oMessageModel->getError());
            }
        }else{
            $this->officeadd();
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Recruitments/index', array('token' => $this->_sToken,'aid'=>$this->_iAid)),
                        'name' => '返回'
                    )
                ),
                'aMessage'=>$oMessageModel->where(array(
                    'token'=>$this->_sToken,
                    'id'=>FC::G('id')
                ))->find()
            ));
            $this->UDisplay('messagecreate');
        }
    }

    /*删除*/
    public function delMessage(){
        $oMessageModel = $this->message;
        $Item = $oMessageModel->where(array('id'=>FC::G('id')))->find();
        if(!$Item) $this->error2('非法操作！');
        if($oMessageModel->where(array('id'=>FC::G('id')))->delete()){
            $this->success2('删除成功',U('Recruitments/index', array('token' => $this->token,'aid'=>$this->_iAid)));
        }else{
            $this->error2('删除失败');
        }
    }
    /*
     * 职位管理
     * */
    public function office(){
        #页面基础配置
        $aWhere = array('token'=>$this->_sToken,'aid'=>$this->_iAid);
        $this->table(
            array(
                'HeadHover'=>U('Recruitments/office', array('token' => $this->_sToken,'aid'=>$this->_iAid)),
                'Head_Opt' => array(
                    array(
                        'name'   => '添加职位',
                        'url'    => U('Recruitments/officAdd',array('aid'=>$this->_iAid))
                    )
                ),
                'tips' => array(
                    '你可以在这里管理职位'
                ),
                'Table_Header' => array(
                    'ID', '区域/分类','职位','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Recruitments/officEdit',array('aid'=>$this->_iAid))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Recruitments/deloffic',array('aid'=>$this->_iAid))
                    )
                )
            ),
            $this->office->where($aWhere)->count(),
            $this->office->field('id,aid,oname')->where($aWhere),
            array($this,'_offices')
        );
    }
    #数据处理
    public function _offices($aData){
        $oAreaModel = M('Recruitment_area');
        foreach($aData as $key=>$aVal){
            $aArea = $oAreaModel->where(array('id'=>$aVal['aid']))->find();
            $aData[$key]['aid'] = $aArea['username'];
        }
        return $aData;
    }

    /*添加职位*/
    public function officAdd(){
        $oOfficeModel = $this->office;
        if(IS_AJAX){
            $_POST['aid'] = $this->_iAid;
            if($oOfficeModel->create()){
                if($oOfficeModel->add()){
                    $this->success2('添加成功',U('Recruitments/office', array('token' => $this->token,'aid'=>$this->_iAid)));
                }else{
                    $this->error('添加失败');
                }
            }else{
                $this->error('非法操作！'.$oOfficeModel->getError());
            }

        }else{
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Recruitments/office', array('token' => $this->_sToken,'aid'=>$this->_iAid)),
                        'name' => '返回'
                    )
                ),
            ));
            $this->UDisplay('officcreate');
        }
    }
    /*编辑*/
    public function officEdit(){
        $oOfficeModel = $this->office;
        if(IS_AJAX){
            $aWhere = array('id'=>$_POST['id']);
            $Item = $oOfficeModel->where($aWhere)->find();
            if($Item == false) $this->error2('非法操作');
            if($oOfficeModel->create()){
                if($oOfficeModel->where($aWhere)->save()){
                    $this->success2('编辑成功',U('Recruitments/office', array('token' => $this->token,'aid'=>$this->_iAid)));
                }else{
                    $this->error2('编辑失败');
                }
            }else{
                $this->error2('非法操作');
            }
        }else{
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Recruitments/office', array('token' => $this->_sToken,'aid'=>$this->_iAid)),
                        'name' => '返回'
                    )
                ),
                'messages'=>$oOfficeModel->where(array(
                    'token'=>$this->_sToken,
                    'id'=>FC::G('id')
                ))->find()
            ));
            $this->UDisplay('officcreate');
        }
    }

    /*删除*/
    public function deloffic(){
        $oOfficeModel = $this->office;
        $Item = $oOfficeModel->where(array('id' => FC::G('id')))->find();
        if (!$Item) $this->error2('非法操作！');
        if ($oOfficeModel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('Recruitments/office', array('token' => $this->token, 'aid' => $this->_iAid)));
        } else {
            $this->error2('删除失败');
        }
    }

    /*
     * 应聘者信息也*/
    public function candidate(){
        #页面基础配置
        $aWhere = array('token'=>$this->_sToken,'aid'=>$this->_iAid);
        $this->table(
            array(
                'HeadHover'=>U('Recruitments/candidate', array('token' => $this->_sToken,'aid'=>$this->$_iAid)),
              /*  'Head_Opt' => array(
                        'name'   => '添加职位',
                        'url'    => U('Recruitments/officAdd')
                    ),*/
                'tips' => array(
                    '你可以在这里管理职位'
                ),
                'Table_Header' => array(
                    'ID', '应聘者','应聘职位','联系方式','出生年月','状态','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '审核',
                        'url'  => U('Recruitments/check',array('aid'=>$this->_iAid))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Recruitments/delcandidate',array('aid'=>$this->_iAid))
                    )
                )
            ),
            D('Recruitment_candidate')->where($aWhere)->count(),
            D('Recruitment_candidate')->field('id,name,oid,tel,barthday,start')->where($aWhere),
            array($this, '_handle')
        );
    }
    #数据处理
    public function _handle($aData){
        $oOfficeModel = $this->office;
        foreach($aData as $key=>$aVal){
            if($aVal['start']==0){
                $aData[$key]['start'] = '未审核';
            }elseif($aVal['start']==1){
                $aData[$key]['start'] = '审核通过';
            }elseif($aVal['start']==2){
                $aData[$key]['start'] = '审核未通过';
            }
            $aOffice = $oOfficeModel->where(array('id'=>$aVal['oid']))->find();
            $aData[$key]['oid'] = $aOffice['oname'];

        }

        return $aData;
    }

    #审核页
    public function check(){
        $ocandidateModel = D('Recruitment_candidate');
        $iCid = $_GET['id'];
        if(IS_AJAX){
            $iTem = $ocandidateModel->where(array('id'=>FC::P('id')))->find();
            if(!$iTem) $this->error2('非法操作');
            $office = M('Recruitment_office')->where(array('id'=>$iTem['oid']))->find();
            if($ocandidateModel->where(array('id'=>FC::P('id')))->save($_POST)){
                FC::mgs($token=$this->_sToken, $openid=$_POST['openid'],
                    $content ='亲，你提交的应聘岗位'.$office['oname'].'已审核通过'.$_POST['info']);
                $this->success2('操作成功', U('Recruitments/candidate', array('token' => $this->token,'aid' => $this->_iAid)));
            }else{
                $this->error2('操作失败');
            }
        }else{
            $aInfo = $ocandidateModel->where(array('id'=>$iCid,'token'=>$this->_sToken))->find();
            $aInfo['winfo'] =htmlspecialchars_decode($aInfo['winfo'],ENT_QUOTES);
            $this->assign(array(
                'candidate'=>$aInfo
            ));
            $this->UDisplay('check');
        }
    }

    /*删除*/
    public function delcandidate(){
        $ocandidateModel = D('Recruitment_candidate');
        $Item = $ocandidateModel->where(array('id' => FC::G('id')))->find();
        if (!$Item) $this->error2('非法操作！');
        if ($ocandidateModel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('Recruitments/candidate', array('token' => $this->token,'aid' => $this->_iAid)));
        } else {
            $this->error2('删除失败');
        }
    }

    /*招聘单位信息*/
    public function messageInfoAdd(){
        $oInfoModel = $this->info;
        $_POST['token'] = $this->_sToken;
        if(IS_AJAX){
            $_POST['aid'] = $_GET['aid'];
            //print_r($_REQUEST['id']);exit;
            if($_REQUEST['id']){
                //编辑
                $aVar = $oInfoModel->where(array('id'=>$_REQUEST['id']))->find();
                if($aVar == false) $this->error2('非法操作');
                if(($aData = $oInfoModel->create()) != false){
                    if($oInfoModel->where(array('id'=>$_REQUEST['id']))->save($_POST)){
                        $this->success2('修改成功', U('Recruitments/messageInfoAdd', array('token' => $this->token,'aid' => $this->_iAid)));
                    }else{
                        $this->error('修改失败');
                    }
                }else{
                    $this->error('非法操作');
                }
            }else{
                #新增
                if(($aData = $oInfoModel->create()) != false){
                    if($oInfoModel->add()){
                        $this->success2('配置成功', U('Recruitments/messageInfoAdd', array('token' => $this->token,'aid' => $this->_iAid)));
                    }else{
                        $this->error('配置失败');
                    }
                }else{
                    $this->error('非法操作');
                }
            }
        }else{
            $this->assign(array(
                'aInfo'=>$oInfoModel->where(array(
                    'token'=>$this->_sToken,
                    'aid'=>$this->_iAid
                ))->find()
            ));
            $this->UDisplay('messageInfo');
        }
    }





}
