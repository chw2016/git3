<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-4-28
 * Time: 上午10:26
 * To change this template use File | Settings | File Templates.
 */
class CreateurlAction extends BaseAction{

    public function index(){
        Vendor('weipay.WxPayHelper');
        $id = $this->_get('id');
        $price = $this->_get('price');  //价格分
        $token = $this->_get('token');
        $name = urldecode($this->_get('name'));
        if(empty($id) || empty($price) || empty($token) ||empty($name)){
            $this->ajaxReturn(array('code'=>-1,'msg'=>'参数不能为空'));
            exit;
        }
        if(!is_numeric($price)){
            $this->ajaxReturn(array('code'=>-2,'msg'=>'金额参数错误'));
            exit;
        }
        $config = M('Weipay_config')->where(array('token'=>$token))->find();
        $orderlist = M('Weipay_order_list');
        $orderdata = $orderlist->where(array('token'=>$token,'gid'=>$config['uid'].'_'.$id))->find();
        if(!$orderdata){
            $data= array();
            $data['gid'] = $config['uid'].'_'.$id;
            $data['price'] = $price;
            $data['token'] = $token;
            $data['name'] = $name;
            $data['uid'] = $config['uid'];
            $data['status'] = 0;
            $data['add_time'] = time();
            if($orderlist->data($data)->add()){
                if($config){
                    $appid = $config['appid'];  //appid
                    $appkey = $config['appkey'];
                    $signtype= "sha1"; //method
                    $partnerkey = $config['partnerkey'];//通加密串
                    $appsecret =  $config['appsecret'];
                    $wxPayHelper = new WxPayHelper($appid,$appkey,$signtype,$partnerkey,$appsecret);
                    $url = $wxPayHelper->create_native_url($config['uid'].'_'.$id);
                    echo "<img src='http://qrcoder.sinaapp.com?t=".$url."' />";
                    exit;
                }else{
                    $this->ajaxReturn(array('code'=>-3,'msg'=>'error'));
                }
            }
        }else{
            $this->ajaxReturn(array('code'=>-4,'msg'=>'error'));
        }

    }


}