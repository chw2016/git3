<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-4-28
 * Time: 下午2:19
 * To change this template use File | Settings | File Templates.
 */
class WeipayNotifyAction extends BaseAction{

    public function index(){
        /**
        后台通知通过请求中的notify_url 进行，采用post 机制。返回通知中的参数一致，url
        包含如下内容：
        见【微信公众号支付】公众号支付接口文档V2.2.pdf 中通知接口部分

        同时，在postData 中还将包含xml 数据。数据如下：
        <xml>
        <OpenId><![CDATA[111222]]></OpenId>
        <AppId><![CDATA[wwwwb4f85f3a797777]]></AppId>
        <IsSubscribe>1</IsSubscribe>
        <TimeStamp> 1369743511</TimeStamp>
        <NonceStr><![CDATA[jALldRTHAFd5Tgs5]]></NonceStr>
        <AppSignature><![CDATA[bafe07f060f22dcda0bfdb4b5ff756f973aecffa]]>
        </AppSignature>
        <SignMethod><![CDATA[sha1]]></ SignMethod >
        </xml>
        商户需要对这些参数进行保存和判断用户的支付状态
         */
        // 获取微信通知接口postData信息
        $postdata = file_get_contents("php://input");
        $postObj = simplexml_load_string ( $postdata, 'SimpleXMLElement', LIBXML_NOCDATA );
        $trade_state =$_GET ["trade_state"];//支付状态
        $out_trade_no = $_GET ["out_trade_no"];//订单号
        /*****************     Todo 还有很多其他参数需要保存起来，参数列表详见文档 **************************/
        if($trade_state==0){
            echo "success";
        }else{
            echo "false";
        }
    }

}