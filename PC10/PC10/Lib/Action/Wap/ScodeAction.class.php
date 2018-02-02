<?php
class ScodeAction extends BaseAction{

    public function index(){
        /*
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"MicroMessenger")) {
            echo '此功能只能在微信浏览器中使用';exit;
        }
        */
        $openid = $this->_get('wecha_id');
        $token = $this->_get('token');
        $this->assign('openid',$openid);
        $this->assign('token',$token);
        $this->display();
    }

    public function ilikevalid(){
        $m_scode = $this->_post('m_scode');
        $a_scode = $this->_post('a_scode');
        $token = $this->_post('token');
        $res = M('Wxuser')->where(array('token'=>$token))->find();
        if($res){
            if(!empty($m_scode) && !empty($a_scode)){
                $scodeModel = M('Scode');
                $resdata = $scodeModel->where(array('m_code'=>$m_scode,'a_code'=>$a_scode,'uid'=>$res['uid']))->find();
                if($resdata){
                    if($resdata['status'] == 0){
                        $this->ajaxReturn(array('code'=>0,'msg'=>'恭喜！您所查询的产品为iLIKE官方正品','data'=>array('uid'=>$res['uid'],'id'=>$resdata['id'])));
                    }else if($resdata['status'] == 1){
                        $this->ajaxReturn(array('code'=>-4,'msg'=>'您的产品为正品，并已激活保险'));
                    }
                }else{
                    $this->ajaxReturn(array('code'=>-3,'msg'=>'对不起！您输入的防伪码有误或产品为仿品'));
                }
            }else{
                $this->ajaxReturn(array('code'=>-1,'msg'=>'非法请求'));
            }
        }else{
            $this->ajaxReturn(array('code'=>-2,'msg'=>'非法请求'));
        }

    }

    public function confirmilike(){
        $openid = $this->_post('openid');
        $uid = $this->_post('uid');
        $id = $this->_post('id');
        if(!empty($openid) && !empty($uid) && !empty($id)){
            $scodeModel = M('Scode');
            $resdata = $scodeModel->where(array('uid'=>$uid,'id'=>$id))->find();
            if($resdata){
                if($resdata['status'] == 0){
                    if($scodeModel->where(array('uid'=>$uid,'id'=>$id))->data(array('status'=>1,'update_time'=>time(),'openid'=>$openid))->save()){
                        $this->ajaxReturn(array('code'=>0,'msg'=>'您的产品保险已激活'));
                    }else{
                        $this->ajaxReturn(array('code'=>-5,'msg'=>'保险激活失败'));
                    }
                }else if($resdata['status'] == 1){
                    $this->ajaxReturn(array('code'=>-4,'msg'=>'恭喜！您所查询的产品为iLIKE官方正品'));
                }
            }else{
                $this->ajaxReturn(array('code'=>-3,'msg'=>'非法请求'));
            }
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'非法请求'));
        }

    }
}

?>