<?php
class RoomAction extends BaseAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/room';

    public function _initialize()
    {
        parent::_initialize();
        $this->code     = trim($_REQUEST['code']);
        $this->token    = trim($_REQUEST['token']);
        $this->openid   = trim($_REQUEST['openid']);
        $this->role     = trim($_REQUEST['role']);

    }

    /*聊天室*/
    public function room(){

        $title = M('Labor_code')->where(array('code'=>$this->code,'token'=>$this->token))->getField('title');
        $aMsg =M('Room_msg')->where(array(
            'token'     => $this->token,
            'code'      => $this->code,
            'msg'       => array('neq', '')
        ))->order('add_time')->select();
        foreach($aMsg as $k=>$val){
            switch($val['role']){
                case 0:$aMsg[$k]['msg'] = "企业说：".$val['msg'];break;
                case 1:$aMsg[$k]['msg'] = "员工说：".$val['msg'];break;
                case 2:$aMsg[$k]['msg'] = "新区劳动科说：".$val['msg'];break;
                case 3:$aMsg[$k]['msg'] = "大鹏劳动办说：".$val['msg'];break;
                case 4:$aMsg[$k]['msg'] = "南澳劳动办说：".$val['msg'];break;
                case 5:$aMsg[$k]['msg'] = "葵涌劳动办说：".$val['msg'];break;
            }
        }
        $this->assign('title',$title);
        $this->assign('role',$this->role);
        $this->assign('msg', $this->getDetailsData($aMsg));
        $this->UDisplay('room');
    }

    public function getDetailsData($aMsg)
    {
        $aUser = array_keys(Arr::changeIndex($aMsg, 'openid'));
        $uid = M('wxuser')->where(array('token' => $this->token))->getField('id');
        $aUserInfo = array();
        if ($aUser) {
            $aUserInfo = Arr::changeIndex(M('wxusers')->where(array(
                'uid'   => $uid,
                'openid' => array('in', $aUser)
            ))->field('nickname,openid, headimgurl')->select(), 'openid');
        }
        foreach ($aMsg as $k => $v) {
            $aMsg[$k]['info'] = Arr::get($aUserInfo, $v['openid']);
        }
        return $aMsg;
    }

    public function send()
    {
        $msg   = trim($_REQUEST['msg']);
        if($this->code AND $this->token AND $_GET['role'] AND $msg AND $id=M('Room_msg')->add(array(
            'token'     => $this->token,
            'openid'    => $this->openid,
            'code'      => $this->code,
            'role'      =>$_GET['role'],
            'msg'       => $msg,
            'add_time'  => time()
        ))){
            exit(json_encode(array('code' => 0, 'id' => $id)));
        }else{
            exit(json_encode(array('code' => -1, 'msg' => $msg)));
        };
    }

    public function getMessages()
    {
        $maxid = trim($_REQUEST['maxid']);
        if($_REQUEST['notmy']){
            $aNotMy = array('openid' => array('neq', $this->openid));
        }else{
            $aNotMy = array();
        };

        if($this->code AND $this->token AND $this->role AND $maxid AND $aData = M('Room_msg')->where(array(
            'token'     => $this->token,
            'code'      => $this->code,
            'role'      => $this->role,
            'msg'       => array('neq', ''),
            'id'        => array('gt', $maxid)
        ) + $aNotMy)->select()){
            exit(json_encode(array('code' => 0, 'data' => $this->getDetailsData($aData))));
        }else{
            exit(json_encode(array('code' => -1)));
        }
    }
}
