<?php
class UserAction extends BaseAction{
	protected function _initialize(){
		parent::_initialize();
		define("TO2",$_SESSION['token']);
//        $funclist = C('funclist');
//        if(session('gid') == 1){
//            if(in_array(MODULE_NAME,$funclist[1])){
//                $this->error2('您当前使用的是万普免费版还不能使用此功能哦,请联系客服');
//            }
//        }
//
//        if(session('gid') == 2){
//            if(in_array(MODULE_NAME,$funclist[2])){
//                $this->error2('您当前使用的是万普展示版还不能使用此功能哦,请联系客服');
//            }
//        }
//        if(session('gid') == 3){
//            if(in_array(MODULE_NAME,$funclist[3])){
//                $this->error2('您当前使用的是万普功能版还不能使用此功能哦,请联系客服');
//            }
//        }
        /* $module = array('Classify','Tmpls','Flash','speeddial','Reply_info');
        $where['enter_api'] = array('like','%'.$this->getActionName().'%');
        $app = M('App_list')->field('id')->where($where)->find();
        if(!$app){
            $this->error2('您没有权限访问此应用哦,请联系客服');
        }else{
            $mapp = M('User_app_list')->where(array('uid'=>session('uid'),'token'=>session('token'),'app_id'=>$app['id']))->find();
            if($mapp['is_pay'] == 1){
                if($mapp['try_type'] != 0){
                    if($mapp['end_date'] < date("Y-m-d H:i:s",time())){
                        $this->error2('您的应用已到期,请联系客服');
                    }
                }
            }else if($mapp['is_pay'] == 0){
                if($mapp['try_type'] != 0){
                    if($mapp['end_date'] < date("Y-m-d H:i:s",time())){
                        $this->error2('您的应用试用期已到期,请联系客服');
                    }
                }
            }
        }
        */

        $myapp = M('User_app_list')->field('tp_user_app_list.app_id,tp_user_app_list.id,tp_app_list.app_name')
            ->join("left join tp_app_list on tp_app_list.id = tp_user_app_list.app_id")
            ->where(array('status'=>1,'uid'=>session('uid'),'token'=>session('token')))->select();

        $app_list_model = M('App_list');
        foreach($myapp as $k=>$v){
            $app_list = array();
            if($v['is_pay'] == 0){
                if($v['try_type'] != 0){
                    if($v['end_date'] < date("Y-m-d H:i:s",time())){
                        unset($myapp[$k]);
                        continue;
                    }
                }
            }else if($v['is_pay'] == 1){
                if($v['try_type'] != 0){
                    if($v['end_date'] < date("Y-m-d H:i:s",time())){
                        unset($myapp[$k]);
                        continue;
                    }
                }
            }
            $app_list = $app_list_model->where(array('id'=>$v['app_id'],'is_open'=>1))->find();
            $myapp[$k]['app_name'] =  $app_list['app_name'];
            $myapp[$k]['enter_api'] =  $app_list['enter_api'];
            $myapp[$k]['pic'] =  $app_list['pic'];
        }
        $this->assign('app_list',$myapp);
		$userinfo=M('User_group')->where(array('id'=>session('gid')))->find();
		$wecha=M('Wxuser')->field('*')->where(array('token'=>session('token'),'uid'=>session('uid')))->find();
        $token = session('token');

        $keywords = M('Keyword')->field('id,keyword,module')->group('keyword,module')->
            where(array('token'=>session('token')))->order('module')->select();
        $this->assign('keywords',$keywords);
        $this->assign('wecha',$wecha);
		$this->assign('token',session('token'));
		$this->assign('userinfo',$userinfo);
		$this->assign('gid',session('gid'));
        //这里加个判断应用的
        if(session('?app_id')){
            $app_id=explode(',',session('app_id'));
            $myapp=M('App_list')->field('id,app_name,enter_api,pic')->where(array('id'=>array('in',$app_id)))->select();

            $this->assign('app_id',1);
          //判断有没有资格
          //  p($myapp);die;
        }
        $this->assign('app_list',$myapp);
       // die;


		if(session('uid')==false){
			$this->redirect('Home/Index/login');
		}
        /*if(session('token')==false){
            $this->error('请先绑定您的微信公众账号哦',U('Bind/index'));
        }*/
        //P($_SESSION);
        $wxuser = M('Wxuser')->where(array('token'=>session('token')))->find();
        $this->_iTid   = $wxuser['id'];

	}
}
