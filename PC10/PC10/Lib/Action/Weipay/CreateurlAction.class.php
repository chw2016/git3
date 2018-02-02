<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-4-28
 * Time: 上午10:26
 * To change this template use File | Settings | File Templates.
 */
class CreateurlAction extends Action{

    public function index(){
        Vendor('weipay.WxPayHelper');

        $goodsid = $this->_get('goodsid');
        $uid = $this->_get('uid');
        $token = $this->_get('token');

        $appid = "wxf8b4f85f3a794e77";  //appid
        $appkey = "2Wozy2aksie1puXUBpWD8oZxiD1DfQuEaiC7KcRATv1Ino3mdopKaPGQQ7TtkNySuAmCaDCrw4xhPY5qKTBl7Fzm0RgR3c0WaVYIXZARsxzHV2x7iwPPzOz94dnwPWSn"; //paysign key
        $signtype= "sha1"; //method
        $partnerkey = "8934e7d15453e97507ef794cf7b0519d";//通加密串
        $appsecret =  "09cb46090e586c724d52f7ec9e60c9f8";



        $wxPayHelper = new WxPayHelper($appid,$appkey,$signtype,$partnerkey,$appsecret);
        echo $wxPayHelper->create_native_url($goodsid);
    }


}