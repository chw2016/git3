<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2015/7/21
 * Time: 8:48
 */
class ShenghongAction extends BaseAction{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/shenghong';

    public function _initialize() {

        parent::_initialize();
    }

    /*招聘的分类页*/
    public function index(){
        $oImgModel = M('Imag');
        $this->assign(array(
            'phone'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Shengou','type'=>'phones'))->find(),
        ));
        $oAreaModel = M('Recruitment_area');
        $aAres = $oAreaModel->where(array(
            'token'=>$this->token
        ))->select();
        $this->assign(array(
            'alist'=>$aAres
        ));
        $this->UDisplay('index');
    }

    /*对应分类里面的职业列表页*/
    public function classify(){
        $oImgModel = M('Imag');
        $this->assign(array(
            'phone2'=>$a=$oImgModel->where(array('token'=>$this->token,'app'=>'Shengou','type'=>'phones2'))->find(),
        ));

        $oOfficeModel = M('Recruitment_office');
        $aWhere = array('token'=>$this->token,'aid'=>$_GET['cid']);
        $aOffice = $oOfficeModel->where($aWhere)->order('add_time desc')->select();
        $this->assign(array(
            'aOffice'=>$aOffice
        ));
        $this->UDisplay('classify');
    }

    /*职位介绍页*/
    public function message(){
        $oImgModel = M('Imag');
        $this->assign(array(
            'phone3'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Shengou','type'=>'phones3'))->find(),
        ));
        $oMessageModel = M('Recruitment_message');
        $oOfficeModel = M('Recruitment_office');
        $oInfoModel =   M('Recruitment_info');
        $oCandidateModel = M('Recruitment_candidate');
        $info = $oMessageModel
            ->where(array(
                'token'=>$this->token,
                'aid'=>$_GET['cid'],
                'office_id'=>$_GET['oid']
            ))->find();
        $info['info'] =htmlspecialchars_decode($info['info'],ENT_QUOTES);
        $information = $oInfoModel
            ->where(array(
                'token'=>$this->token,
                'aid'=>$_GET['cid']
            ))->find();
        $office = $oOfficeModel
            ->where(array(
                'token'=>$this->token,
                'id'=>$_GET['oid']
            ))->find();
        $this->assign(array(
            'office'=>$office,
            'info'=>$info,
            'information'=>$information,
            'aInfo'=>reset($oCandidateModel->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid))->order('add_time desc')->select()),
        ));
        $this->UDisplay('message');
    }

    /*简历上传*
    name:naem,sex:sex,barthday:barthday,email:email,educational:educational,tel:tel,address:address:address,winfo:winfo
    */
    public function ajaxmessage(){
        $db=M('Recruitment_candidate');
        $data=array(
            'token'=>$this->token,
            'openid'=>$this->openid,
            'name' => $this->_post('name'),
            'sex' => $this->_post('sex'),
            'barthday' => $this->_post('barthday'),
            'email' => $this->_post('email'),
            'educational' => $this->_post('educational'),
            'tel' => $this->_post('tel'),
            'address' => $this->_post('address'),
            'winfo' => $this->_post('winfo'),
            'add_time'=>time(),
            'aid'=>$_POST['aid'],
            'oid'=>$_POST['oid']
        );
        $result=$db->add($data);
        if($result){
            $info = M('Recruitment_area')
                ->where(array('id'=>$_POST['aid']))
                ->find();
            $office = M('Recruitment_office')
                ->where(array(
                    'token'=>$this->token,
                    'id'=>$_GET['oid']
                ))->find();
            switch($this->_post('sex')){
                case 0:$sex = '男';break;
                case 1:$sex = '女';break;
            }
            $body = "
                招聘职位：{$info['username']}_{$office['oname']}<br/>
                姓名：{$this->_post('name')}</br>
                性别：{$sex}</br>
                生日：{$this->_post('barthday')}</br>
                学历：{$this->_post('educational')}</br>
                邮件：{$this->_post('email')}</br>
                电话：{$this->_post('tel')}</br>
                地址：{$this->_post('address')}</br>
                工作经历：{$this->_post('winfo')}<br/>
            ";
            send_email('微信应聘',$body,'zhaopin@shpcb.com');
            /*send_email('微信招聘提醒',
                date('Y年m月d日 H:i:s').'在微信公众平台上投了'.$info['username'].'下的'.$office['oname'].'，请登陆公众号后台查阅！',
                'zhaopin@shpcb.com');*/
            $this->success('操作成功',U('Shenghong/alist',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            $this->error('操作失败',U('Shenghong/message',array('token'=>$this->token,'openid'=>$this->openid,'oid'=>$_POST['oid'],'cid'=>$_POST['aid'])));
        }

    }
    /*简历记录*/
    public function alist(){
        $oCandidateModel = M('Recruitment_candidate');
        $type = $_GET['type'];
        if($type){
            $oCandidateModel->where(array('id'=>$_GET['id']))->save(array('is_del'=>1));
        }
        $aWhere = array(
            'token'=>$this->token,
            'openid'=>$this->openid,
            'is_del'=>0
        );
        $list = $oCandidateModel->where($aWhere)->select();
        foreach($list as $k=>$val){
            $office = M('Recruitment_office')->where(array('id'=>$val['oid']))->find();
            $list[$k]['oid'] = $office['oname'];
            $user = M('Wxuser')->where(array('token'=>$this->token))->find();
            $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$val['openid']))->find();
            $list[$k]['herdurl'] = $users['headimgurl'];
        }
        $count = $oCandidateModel->where($aWhere)->count();
        $this->assign(array(
            'list'=>$list,
            'count'=>$count
        ));


        $this->UDisplay('alist');
    }

   /*简历删除*/
    /*public function del_message(){
        $db=M('Recruitment_candidate');
        $iTem = $db->where(array('id'=>$_GET['id']))->find();
        if(!$iTem) $this->error('非法操作！');exit;
        if($db->where(array('id'=>$_GET['id']))->save(array('is_del'=>1))){
            $this->success('删除成功');
        }else{
            $this->error('删除失败！');exit;
        }


    }*/


}