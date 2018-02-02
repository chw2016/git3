<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/1/20
 * Time: 10:51
 */
class ServiceactiveAction extends BaseAction{

    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/serviceactive';


    public function _initialize() {
        if(in_array(ACTION_NAME,array('index'))){
            if(!IS_AJAX){
                $this->autoShare = true;
            }
        }
        parent::_initialize();

    }

    public function index(){
        $aWhere = array(
            'tid'=>session('tid'),
            'status'=>0
        );
        if(!$_GET['cid']){
            $aArea =   M('Service_active')
                ->where($aWhere)
                ->order('id desc')
                ->select();
            $aAreas= $aArea[0];
            if($aAreas){
                $url = U('Wap/Serviceactive/index',
			array(
			'token'=>$this->token,
			'openid'=>$this->openid,
			'cid'=>$aAreas['id']
			));
		$this->redirect($url);
		return;
            }
        }
        $infos =  M('Service_active')
            ->where(array(
                'id'=>$_GET['cid']
            ))->find();
        if($_GET['dopenid']){

            $data = array(
                'token'=>$this->token,
                'tid'=>session('tid'),
                'dopenid'=>$this->openid,
                'openid'=>$_GET['dopenid'],
                'type'=>1,
                'add_time' =>date('Y-m-d H:i:s'),
                'cid'       =>$infos['id'],
                'store'     =>$infos['store'],
            );
            $is_find = M('Service_active_user')
                ->where(array(
                    'tid'=>session('tid'),
                    'openid'=>$_GET['dopenid'],
                    'dopenid'=>$this->openid,
                    'cid'=>$infos['id']
                ))->find();
            $users = M('Wxusers')->where(array(
                'uid'=>session('tid'),
                'openid'=>$this->openid
            ))->find();
            $taiUser =  M('Service_profile')
                ->where(array(
                    'token'=>$this->token,
                    'openid'=>$_GET['dopenid']
                ))->find();
            if(!$is_find && $taiUser){
                if(M('Service_active_user')->add($data)) {
                    msg($this->token,$_GET['dopenid'],'恭喜您，您通过好友'.$users['nickname'].'点击阅读您分享的'.$infos['title'].'获得'.$infos['store'].'分积分！');
                    M('Service_profile')
                        ->where(array(
                            'token'=>$this->token,
                            'openid'=>$_GET['dopenid']
                        ))
                        ->setInc('integral',$infos['store']);
                }
            }
        }

        $aNickuser = M('Wxusers')
            ->where(array(
                'uid'=>session('tid'),
                'openid'=>$this->openid,
                'status'=>1
            ))->find();
       if(!$aNickuser){
            $url = C('site_url').'index.php?g=Home&m=Nofind&a=isnotsub&token='.$this->token.'&openid='.$this->openid.'&dopenid='.$_GET['dopenid'];
            $this->redirect($url);
        }

        $this->assign(array(
            'info'=>$infos
        ));
        $this->UDisplay('index');
    }

    public function share(){
        $oModel = M('Service_active_user');
        $userModel = M('Service_profile');
        $aUser = $userModel
            ->where($a=array(
                'token'=>$this->token,
                'openid'=>$this->openid
            ))
            ->find();
	    WL(print_r($a,true));
        if($aUser){
            $info = M('Service_active')
                ->where(array(
                    'id'=>$_GET['cid']
                ))->find();
            $data = array(
                'openid'    =>$this->openid,
                'token'     =>$this->token,
                'tid'       =>$_SESSION['tid'],
                'cid'       =>$info['id'],
                'store'     =>$info['store'],
                //  'dopenid'   =>$_GET['dopenid'],
                'add_time' =>date('Y-m-d H:i:s'),
                'type'     =>0
            );
            $is_share = $oModel
                ->where(array(
                    'tid'=>session('tid'),
                    'type'=>0,
                    'cid'=>$_GET['cid'],
                    'openid'=>$this->openid
                ))->find();
            if(!$is_share){
                if($oModel->add($data)){
                    $userModel
                        ->where(array(
                            'token'=>$this->token,
                            'openid'=>$this->openid
                        ))
                        ->setInc('integral',$info['store']);
                    msg($this->token,$this->openid,'恭喜您，通过分享'.$info['title'].'获得'.$info['store'].'分积分！');
                }

            }
        }else{
            $this->error('您现在还不是会员，请去“会员资料”注册后再分享得积分！');
        }

    }















}