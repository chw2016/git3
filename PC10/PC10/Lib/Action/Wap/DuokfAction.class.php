<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-6
 * Time: 下午2:20
 */
class DuokfAction extends BaseAction{

    public function _initialize(){
        parent::_initialize();
    }

    public function sendkfmsg(){
        if(($this->tpl['service_type_info'] == 2 ||  $this->tpl['service_type_info'] == 1) && $this->tpl['verify_type_info'] >=0){
            if($res = M('Wxusers')->where(array('openid'=>$this->openid,'uid'=>$this->tpl['id']))->find()){
                if(M('Wxusers')->where(array('openid'=>$this->openid,'uid'=>$this->tpl['id']))->save(array('is_kf_time'=>time()+180))){
                    $params['token'] = $this->token;
                    $params['openid'] = $this->openid;
                    $params['content'] = $res['nickname']."您好!".$this->tpl['name'].'正在为您服务,请问有什么可以帮助您的呢?';
                    $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                    $data = $this->api_notice_increment($url,http_build_query($params));
                    if(!$data){
                        $this->api_notice_increment($url,http_build_query($params));
                    }else{
                        echo $this->encode(array('code'=>0,'msg'=>'系统已收到您的咨询'));
                    }
                }else{
                    echo $this->encode(array('code'=>-3,'msg'=>'系统错误,请重试'));
                }
            }else{
                echo $this->encode(array('code'=>-3,'msg'=>'您还没有关注我们的公众号哦,我们的公众号是'.$this->tpl['name']));
            }
        }else{
            echo $this->encode(array('code'=>-1,'msg'=>'该商城还没开通微信多客服哦'));
        }

    }

}