<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/9
 * Time: 15:39
 */
class HengboAction extends BaseAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/hengbo';

    /**
     *  Token
     **/
    protected $_sToken = null;

    public function _initialize()
    {
        $this->_sToken = $this->_get('token');
        parent::_initialize();
    }

    /*
     * 我要招聘
     * */
    #区域
    public function acres(){
        $oAreaModel = M('Recruitment_area');
        $aAres = $oAreaModel->where(array(
            'token'=>$this->_sToken
        ))->select();
        $this->assign(array(
            'aAres'=>$aAres
        ));
        $this->UDisplay('acres');
    }
    #职位
    public function office(){
        $oOfficeModel = M('Recruitment_office');
        $oMessageModel = M('Recruitment_message');
        $aWhere = array('token'=>$this->_sToken,'aid'=>$_GET['aid']);
        $aOffice = $oMessageModel->where($aWhere)->order('add_time desc')->select();
        foreach($aOffice as $iKey=>$aValuer){
            $aData =$oOfficeModel->where(array('id'=>$aValuer['office_id']))->find();
            $aOffice[$iKey]['office_name'] = $aData['oname'];
        }
        $this->assign(array(
            'aOffice'=>$aOffice
        ));
        $this->UDisplay('office');
    }
    #招聘信息
    public function message(){
        $oMessageModel = M('Recruitment_message');
        $oCandidateModel = M('Recruitment_candidate');
        $oOfficeModel = M('Recruitment_office');

        if(IS_AJAX){
            #只做添加。
            $aData = array(
                'oid'=>$_POST['oid'],
                'token'=>$this->token,
                'openid'=>$this->openid,
                'name'=>$_POST['uname'],
                'sex'=>$_POST['sex'],
                'tel'=>$_POST['phone'],
                'barthday'=>$_POST['birth'],
                'educational'=>$_POST['education'],
                'email'=>$_POST['mail'],
                'address'=>$_POST['origin'],
                'winfo'=>$_POST['work'],
                'add_time'=>time(),
                'aid'=>$_POST['aid']
            );
            if($oCandidateModel->add($aData)){
                echo $this->encode(array('codes'=>1,'msg'=>'申请成功!','urles'=>U(MODULE_NAME.'/mymessage',array('token'=>$this->token,'openid'=>$this->openid))));exit;
            }else{
                echo $this->encode(array('codes'=>0,'msg'=>'失败!'));exit;
            }
        }else{
            $aWhere = array('id'=>$_GET['mid'],'token'=>$this->_sToken);
            $aOffices = $oMessageModel->where($aWhere)->find();
            $aDate = $oOfficeModel->where(array('id'=>$aOffices['office_id']))->find();
            $this->assign(array(
                'aOffice'=>$aOffices,
                'aData'=>$aDate,
                'aInfo'=>reset($oCandidateModel->where(array(
                    'token'=>$this->_sToken,
                    'openid'=>$this->openid))->order('add_time desc')->select())
            ));
        }
        $this->UDisplay('message');
    }
    #我的应聘
    public function mymessage(){
        $oCandidateModel = M('Recruitment_candidate');
        $oOfficeModel = M('Recruitment_office');
        $where = array(
            'toekn'=>$this->token,
            'openid'=>$this->openid
        );
        $aMessage =$oCandidateModel->where($where)->order('add_time desc')->select();
        foreach($aMessage as $iKey=>$aValuer){
            $aDate = $oOfficeModel->where(array('id'=>$aValuer['oid']))->find();
            $aMessage[$iKey]['office'] = $aDate['oname'];
        }
        $this->assign(array(
           'aMessage'=>$aMessage
        ));
        $this->UDisplay('mymessage');
    }
    /*
     * 我要报修
     * */
    public function index(){
        $oAreaModel = D('Service_area');
        $this->assign(array(
            'aArea'=>$oAreaModel->where(array('token'=>$this->token))->select()
        ));
        $this->UDisplay('index');
    }
    public function isusers(){
        $oUsersModel = M('Service_urse');
        $where = array(
            'token'=>$this->token,
            'openid'=>$this->openid,
            'aid'=>$_GET['aid']
        );
        if(IS_AJAX){
            if($oUsersModel->where($where)->find()){
                if($_POST['type'] ==1){
                    echo $this->encode(array('codes'=>1,'mgs'=>'成功！','urles'=>U('Wap/Hengbo/orders',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
                }elseif($_POST['type'] ==2){
                    echo $this->encode(array('codes'=>1,'mgs'=>'成功！','urles'=>U('Wap/Hengbo/complaints',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
                }
            }else{
                echo $this->encode(array('codes'=>2,'mgs'=>'您现在还不是该小区的会员，请注册！','urles'=>U('Wap/Hengbo/usersbo',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
            }
        }
    }
    #报修订单
    public function orders(){
        $oOrdersModel = M('Service_order');
        $oUsersModel = M('Service_urse');
        if(IS_AJAX){
            if($oUsersModel->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid,
                'aid'=>$_GET['aid']
            ))->find()){
                $_POST['token'] = $this->token;
                $_POST['openid'] = $this->openid;
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $iTom = $oOrdersModel->add($_POST);
                if($iTom){
                    echo $this->encode(array('codes'=>1,'mgs'=>'提交成功！','urles'=>U('Wap/Hengbo/myorders',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
                }else{
                    echo $this->encode(array('codes'=>0,'mgs'=>'提交失败!'));exit;
                }
            }else{
                echo $this->encode(array('codes'=>2,'mgs'=>'你还不是此小区的会员!','urles'=>U('Wap/Hengbo/usersbo',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
            }

        }else{
            $this->UDisplay('orders');
        }
    }
    #我的报修
    public function myorders(){
        $oOrdersModel = M('Service_order');
        $oUsersModel = M('Service_urse');
        $oStaffModel = M('Service_staff');
        $where = array(
            'token'=>$this->token,
            'openid'=>$this->openid,
            'aid'=>$_GET['aid']
        );
        $iTem = $oOrdersModel->where($where)->order('add_time desc')->select();
        foreach($iTem as $iKey=>$aValue){
            $aUserel =$oUsersModel->where(array('token'=>$this->token,'aid'=>$_GET['aid'],'openid'=>$iTem['openid']))->find();
            $aStaff =$oStaffModel->where(array('token'=>$this->token,'aid'=>$_GET['aid'],'id'=>$iTem['staff_id']))->find();
            $iTem[$iKey]['uname'] = $aUserel['name'];
            $iTem[$iKey]['staff_name'] = $aStaff['staff_name'];
        }
        $this->assign(array(
            'aOrders' => $iTem
        ));
        $this->UDisplay('myorders');
    }
    public function remind(){
        $oOrdersModel = M('Service_order');
        if(IS_AJAX){
            $iTem = $oOrdersModel->where(array('id'=>$_POST['oid']))->find();
            if(!$iTem) $this->error2('非法操作');
            if($oOrdersModel->where(array('id'=>$_POST['oid']))->save(array('stauts'=>1))){
                echo $this->encode(array('codes'=>1,'mgs'=>'提醒成功','urles'=>U('Wap/Hengbo/myorders',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
            }else{
                echo $this->encode(array('codes'=>0,'mgs'=>'提醒失败'));exit;
            }
        }
    }
    #评价页
    public function assess(){
        $oOrdersModel = M('Service_order');
        $oStaffModel = M('Service_staff');
        $oAssessMOdel = M('Service_assess');
        if(IS_AJAX){
            $_POST['token']=$this->token;
            $_POST['openid'] = $this->openid;
            $_POST['add_time'] = date('Y-m-d H:i:s');
            $data['stauts'] = 3;
            $iTem = $oOrdersModel->where(array('id'=>$_POST['oid']))->find();
            if(!$iTem) $this->error2('非法操作');
            if($oOrdersModel->where(array('id'=>$_POST['oid']))->save($data)){

                if($oAssessMOdel->add($_POST)){
                    echo $this->encode(array('codes'=>1,'mgs'=>'评价成功!','urles'=>U('Wap/Hengbo/myorders',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
                }else{
                    echo $this->encode(array('codes'=>0,'mgs'=>'评价失败!','urles'=>U('Wap/Hengbo/assess',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid'],'oid'=>$_POST['oid'],'sid'=>$_GET['sid']))));exit;
                }
            }else{
                echo $this->encode(array('codes'=>2,'mgs'=>'系统繁忙，请稍后!','urles'=>U('Wap/Hengbo/myorders',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
            }
        }else{
            $this->assign(array(
                'aStaff'=>$oStaffModel->where(array(
                    'id'=>$_GET['sid']
                ))->find()
            ));
            $this->UDisplay('assess');
        }
    }
    #公告列表
    public function bulletin(){
        $obulletinModel = M('service_bulletin');
        $this->assign(array(
            'aInfo'=>$obulletinModel->where(array(
                'token'=>$this->token,
                'aid'=>$_GET['aid']))->order('add_time desc')->select()
        ));
        $this->UDisplay('bulletin');
    }
    #公告内容
    public function bulletininfo(){
        $obulletinModel = M('service_bulletin');
        $aInfo = $obulletinModel->where(array(
            'id'=>$_GET['id'],
            'token'=>$this->token,
            'aid'=>$_GET['aid']))->order('add_time desc')->find();
        $this->assign(array(
            'aInfo'=>$aInfo,
            'sText'=>htmlspecialchars_decode($aInfo['info'],ENT_QUOTES)
        ));
        $this->UDisplay('bulletininfo');
    }
    #投诉建议页
    public function complaints(){
        $oComplaintsModel = M('Service_complaints');
        $oUsersModel = M('Service_urse');
        $where = array(
            'token'=>$this->token,
            'openid'=>$this->openid,
            'aid'=>$_GET['aid']
        );
        $iTem = $oUsersModel->where($where)->find();
        if(IS_AJAX){
            if($iTem){
                $_POST['add_time'] = date('Y-m-d H:i:s');
                if($oComplaintsModel->add($_POST)){
                    echo $this->encode(array('codes'=>1,'mgs'=>'谢谢您来的投诉建议，投诉建议成功！','urles'=>U('Wap/Hengbo/index',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
                }else{
                    echo $this->encode(array('codes'=>2,'mgs'=>'系统繁忙，请稍后！'));exit;
                }
            }else{
                echo $this->encode(array('codes'=>0,'mgs'=>'您现在还不是该小区的会员，请注册！','urles'=>U('Wap/Hengbo/usersbo',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
            }
        }else{
            $this->UDisplay('complaints');
        }
}
    #联系物业
    public function areainfo(){
        $oAreaModel = M('Service_area');
        $aList = $oAreaModel->where(array('id'=>$_GET['aid']))->find();
        $this->assign(array(
            'aList'=>$aList,
            'sInfo'=>htmlspecialchars_decode($aList['info'],ENT_QUOTES)
        ));
        $this->UDisplay('areainfo');
    }
    #个人中心
    public function usersbo(){
        $oUsersModel = M('Service_urse');
        $where = array(
            'token'=>$this->token,
            'openid'=>$this->openid,
            'aid'=>$_GET['aid']
        );
        $iTem = $oUsersModel->where($where)->find();
        if(IS_AJAX){
            if($iTem){
                if($oUsersModel->where($where)->save($_POST)){
                    echo $this->encode(array('code'=>1,'mgs'=>'保存成功','urles'=>U('Wap/Hengbo/index',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;
                }else{
                    echo $this->encode(array('code'=>2,'mgs'=>'保存失败'));exit;
                }
            }else{
                $_POST['token'] = $this->token;
                $_POST['openid'] = $this->openid;
                $_POST['add_time']=date('Y-m-d H:i:s');
                if($oUsersModel->add($_POST)){
                    echo $this->encode(array('code'=>1,'mgs'=>'保存成功','urles'=>U('Wap/Hengbo/index',array('token'=>$this->token,'openid'=>$this->openid,'aid'=>$_GET['aid']))));exit;

                }else{
                    echo $this->encode(array('code'=>2,'mgs'=>'保存失败'));exit;
                }
            }
        }else{
            $this->assign(array(
                'aUser'=>$iTem
            ));
            $this->UDisplay('usersbo');
        }
    }

    /*照片墙*/
    public function photo(){
        $oFristModel = M('Imag');
        $oWallModel = M('Photo_wall');
        $oPhotoModel = M('Wx_photo');
        $aFrist = $oFristModel->where(array(
            'token'=>$this->token,
            'app'=>'PhotoWall',
            'type'=>'img1'
        ))->find();
       // print_r($aFrist);exit;
        $aList = $oWallModel->where(array('token'=>$this->token))->order('read_time desc')->limit(6)->select();
        foreach($aList as $iKey=>$aValur){
            $aWall = $oPhotoModel->where(array('id'=>$aValur['pid']))->find();
            $aList[$iKey]['pic'] = $aWall['pic'];
            $aList[$iKey]['add_time'] = $aWall['add_time'];
        }

        $this->assign(array(
            'aList'=>$aList,
            'aImg'=>$aFrist
        ));
        if(IS_AJAX){
            $plen = $this->_post('pLength','intval');
            $fetchResult = $oWallModel->where(array('token'=>$this->token))->order('read_time desc')->limit($plen.',6')->select();
             foreach($fetchResult as $iKey=>$aValur){
                $aWall = $oPhotoModel->where(array('id'=>$aValur['pid']))->find();
                $fetchResult[$iKey]['pic'] = $aWall['pic'];
                $fetchResult[$iKey]['add_time'] = $aWall['add_time'];
            }
            $this->assign('info',$fetchResult);
            $fetch = $this->fetch('./tpl/Wap/default/hengbo/loadMore.html');
            $fetchs = $this->fetch('./tpl/Wap/default/hengbo/loadMores.html');
            echo $this->encode(array(
               'fetch' => $fetch,
               'fetchs' => $fetchs,
               'status' => 0
            ));
            exit;
        }
        $this->UDisplay('photo');
    }





}