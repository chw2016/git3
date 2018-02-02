<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * tel:15889394741
 * Date: 2015/3/23
 * project:太阳库
 * Time: 10:06
 */
class SunPoolAction extends BaseAction{
    public $token;
    public $openid;
    protected function _initialize(){
        parent::_initialize();
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $this->token=$this->_get('token');
        $this->openid=$this->_get('openid');
        $this->assign('token',$this->token);
        $this->assign('openid',$this->openid);
        $this->assign("tpl",$this->tpl);
//        if(!strpos($agent,"MicroMessenger")) {
//            //微信用户请先关注
//            exit("该功能只能在微信浏览器中使用");
//        }
//        if(!M('Wxusers')->where(array('uid'=>$this->tpl['id'],'openid'=>$this->openid))->find()){
//            exit("请先关注");
//        }
    }

    //按用电量
    public function ByEcNum(){
        $this->display();
    }

    //按安装面积
    public function BySize(){
        $this->display();
    }

    //效益评估第一步，计算隆亮
    public function BeniftCapacity(){
        $this->display();
    }

    //效益评估第二部计算总额
    public function BeniftTotal(){
        $this->display();
    }

    //安装资料填写
    public function ErectInfo(){
        if(IS_AJAX){
            $_POST['token']=$this->token;
            $_POST['openid']=$this->openid;
            $_POST['add_time'] = date("Y-m-d H:i:s");
            if(M("Sunpool_erect")->add($_POST)){
                $this->ajaxReturn(array('status'=>1, 'info'=>'填写资料成功'));
            }else{
                $this->ajaxReturn(array('status'=>0, 'info'=>'填写失败'));
            }
        }else{
            $this->display();
        }
    }

}