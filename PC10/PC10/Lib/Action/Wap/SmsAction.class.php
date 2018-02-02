<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-9-25
 * Time: 上午9:17
 * To change this template use File | Settings | File Templates.
 */
class SmsAction extends BaseAction{

    public function _initialize(){
        parent::_initialize();
    }

    public function send_sms(){
        $type = $_REQUEST['type'];
        $phone = $_REQUEST['phone'];
        $info=$_REQUEST['info'];
        if(isset($_REQUEST['token'])){
            $this->token = $_REQUEST['token'];
        }
        if($type && $phone){
            $sms_config_model=M('config_sms');
            $check=$sms_config_model->where(array('token'=>$this->token))->find();
            if($check){
                // http://api.sms.cn/mt/?uid=用户账号&pwd=MD5位32密码&mobile=号码&content=内容

                $data['token'] = $this->token;
                $data['content'] = $this->create_password();
                $data['type'] = $type;
                $data['phone'] = $phone;
                $data['add_time'] = time();
                $data['is_ok'] = 0;
                //$contentdata = "您正在设置万普微信平台用户中心手机验证,验证码为：".$data['content']."，请及时验证有效期为3分钟【".$this->tpl['name']."】";
			    //$contentdata = "【".$this->tpl['name']."】验证码为：".$data['content']."，请在30分钟之内按页面提示提交验证码";
                /*$url = "http://api.sms.cn/mt/?uid=".$check['account']."&encode=utf8&pwd=".md5($check['pwd'].$check['account'])."&mobile=".$phone."&content=".$contentdata;
                $returncontent = file_get_contents($url);

                parse_str($returncontent,$arr);
                */
                //$contentdata = "您的验证码是".$data['content']."【".$this->tpl['name']."】";

                    $contentdata = "亲爱的顾客，您的验证码是".$data['content']."。有效期为半小时，请尽快验证【".$this->tpl['name']."】";
                if($info){//贷款短信发送
                    $contentdata=$info;
                    if($info=='hn'){//海南生活家特有的
                        $contentdata = "【海南生活家】您的动态密码是".$data['content']."，欢迎加入！";
                    }
                }

                $url = 'http://yunpian.com/v1/sms/send.json';
                $apidata['text'] = urlencode("$contentdata");
                $apidata ="apikey=".$check['apikey']."&text=".$apidata['text']."&mobile=".$phone;
                $returndata = $this->api_notice_increment($url,$apidata);
		$returndata = json_decode($returndata);

                if($returndata && $returndata->code == 0){
                    if(M('Sms_send_list')->add($data)){
                        echo $this->encode(array('code'=>0,'msg'=>'success','data'=>$data['content']));exit;
                    }else{
                        echo $this->encode(array('code'=>-1,'msg'=>'fail','data'=>$data['content']));exit;
                    }
                }else{
                    echo $this->encode(array('code'=>-3,'msg'=>'fail'));exit;
                }

            }else{
                echo $this->encode(array('code'=>-2,'msg'=>'fail'));exit;
            }
        }

    }

    public function sms_valid(){
        $type = $_REQUEST['type'];
        $phone = $_REQUEST['phone'];
        $content = $_REQUEST['code'];
        $sms_send_list=M('Sms_send_list');
        if($type && $phone && $content){
            $where['token'] = $this->token;
            $where['phone'] = $phone;
            $where['content'] = $content;
            $where['type'] = $type;
            $where['is_ok'] = 0;
            if($check = $sms_send_list->where($where)->find()){
                if((intval($check['add_time'])+180) > time()){
                   if($sms_send_list->where($where)->save(array('is_ok'=>1))){
                       echo $this->encode(array('code'=>0,'msg'=>'验证成功,请保存资料'));exit;
                   }else{
                       echo $this->encode(array('code'=>-3,'msg'=>'系统繁忙,请重试'));exit;
                   }
                }else{
                    echo $this->encode(array('code'=>-2,'msg'=>'验证超时,3分钟之内有效'));exit;
                }
            }else{
                echo $this->encode(array('code'=>-1,'msg'=>'验证失败'));exit;
            }
        }
    }

    public function create_password($pw_length = 4){
        $chars = '0123456789';

        $password = '';
        for ( $i = 0; $i < $pw_length; $i++ )
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }

        return $password;
    }









}