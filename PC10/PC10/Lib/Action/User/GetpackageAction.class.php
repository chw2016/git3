<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-4-28
 * Time: 下午3:41
 * To change this template use File | Settings | File Templates.
 */
class GetpackageAction extends BaseAction{

    private $partner=null;
    private $partnerKey=null;
    private $token = null;
    private $partnerid = 8888888;

    public function index(){
        Vendor('weipay.ArrayToXML');
        $postdata = file_get_contents("php://input");
        $postObj = simplexml_load_string ( $postdata, 'SimpleXMLElement', LIBXML_NOCDATA );
        $openId = $postObj->OpenId;
        $AppId = $postObj->AppId;
        $IsSubscribe = $postObj->IsSubscribe;
        $ProductId = $postObj->ProductId;
        $TimeStamp = $postObj->TimeStamp;
        $NonceStr = $postObj->NonceStr;
        $AppSignature = $postObj->AppSignature;
        $SignMethod = $postObj->SignMethod;


        $paramArr = explode('_',$ProductId);
        $userid = $paramArr[0];
        $config = M('Weipay_config')->where(array('uid'=>$userid))->find();
        $userModel = M('Wxuser');
        $user = $userModel->field('token')->where(array('id'=>$userid,'status'=>1))->find();
        if($config && $user){
            $this->partner =  $config['partnerkey'];
            $this->partnerKey =  $config['partnerkey'];
            $this->token =  $user['token'];
            $orderModel = M('Weipay_order_list');
            $order = $orderModel->where(array("gid='{$ProductId}'"))->find();
            if($order){
                $data=array(
                    "AppId"=>$AppId,
                    "Package"=>$this->getPackage($order['name'],$order['price'],$order['id']),
                    "TimeStamp"=>strtotime(),
                    "NonceStr"=>$NonceStr,
                    "RetCode"=>0,   //RetCode 为0 表明正确，可以定义其他错误；当定义其他错误时，可以在RetErrMsg 中填上UTF8 编码的错误提示信息，比如“该商品已经下架”，客户端会直接提示出来。
                    "RetErrMsg"=>"正确返回",
                    "AppSignature"=>$AppSignature,
                    "SignMethod"=>"sha1"
                );
                //返回生成的xml数据
                echo ArrayToXML::arrtoxml($data);
                exit;
            }else{
                $this->ajaxReturn(array('code'=>-1,'msg'=>'error'));
            }
        }else{
            $this->ajaxReturn(array('code'=>-2,'msg'=>'error'));
        }


    }

    private function createTradeId(){
        $curDateTime = date("YmdHis");
        //date_default_timezone_set(PRC);
        $strDate = date("Ymd");
        $strTime = date("His");
        //4位随机数
        $randNum = rand(1000, 9999);
        //10位序列号,可以自行调整。
        $strReq = $strTime . $randNum;
        /* 商家的定单号 */
        $mch_vno = $curDateTime . $strReq;
        /********************/
        /*todo 保存订单信息到数据库中*/
        /********************/
        return $mch_vno;
    }

    private function getPackage($body,$total_fee,$out_trade_no){
        $ip=$_SERVER["REMOTE_ADDR"];
        if($ip=="::1"||empty($ip)){
            $ip="127.0.0.1";
        }
        $banktype = "WX";
        $fee_type = "1";//费用类型，这里1为默认的人民币
        $input_charset = "GBK";//字符集，这里将统一使用GBK
        $notify_url = "http://v.wapwei.com/index.php?g=User&m=WeipayNotify&token=".$this->token;//支付成功后将通知该地址
        $out_trade_no =$this->createTradeId();//订单号，商户需要保证该字段对于本商户的唯一性
        $partner = $this->partnerid; //商户号
        $spbill_create_ip =$ip;//订单生成的机器IP
        $partnerKey = $this->partnerKey;//这个值和以上其他值不一样是：签名需要它，而最后组成的传输字符串不能含有它。这个key是需要商户好好保存的。
        //首先第一步：对原串进行签名，注意这里不要对任何字段进行编码。这里是将参数按照key=value进行字典排序后组成下面的字符串,在这个字符串最后拼接上key=XXXX。由于这里的字段固定，因此只需要按照这个顺序进行排序即可。
        $signString = "bank_type=".$banktype."&body=".$body."&fee_type=".$fee_type."&input_charset=".$input_charset."&notify_url=".$notify_url."&out_trade_no=".$out_trade_no."&partner=".$partner."&spbill_create_ip=".$spbill_create_ip."&total_fee=".$total_fee."&key=".$partnerKey;
        $md5SignValue =  ("" .strtoupper(md5(($signString))));
        //echo $md5SignValue;
        //然后第二步，对每个参数进行url转码。
        $banktype = $this->encodeURIComponent($banktype);
        $body=$this->encodeURIComponent($body);
        $fee_type=$this->encodeURIComponent($fee_type);
        $input_charset = $this->encodeURIComponent($input_charset);
        $notify_url = $this->encodeURIComponent($notify_url);
        $out_trade_no = $this->encodeURIComponent($out_trade_no);
        $partner = $this->encodeURIComponent($partner);
        $spbill_create_ip = $this->encodeURIComponent($spbill_create_ip);
        $total_fee = $this->encodeURIComponent($total_fee);

        //然后进行最后一步，这里按照key＝value除了sign外进行字典序排序后组成下列的字符串,最后再串接sign=value
        $completeString = "bank_type=".$banktype."&body=".$body."&fee_type=".$fee_type."&input_charset=".$input_charset."&notify_url=".$notify_url."&out_trade_no=".$out_trade_no."&partner=".$partner."&spbill_create_ip=".$spbill_create_ip."&total_fee=".$total_fee;
        $completeString = $completeString."&sign=".$md5SignValue;
        $oldPackageString = $completeString; //记住package，方便最后进行整体签名时取用
        return $completeString;
    }

    //模拟js中的encodeURIComponent方法
    private function encodeURIComponent($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }





}