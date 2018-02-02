<?php
class BaseAction extends Action
{
    public $openid = null;
    public $wxusers = null;
    public $token = null;
    public $tpl =null;
    public $autoShare = false;

    protected function _initialize()
    {
    	$this->assign('site_url', $site_url = C('site_url'));
    	$this->assign('cur_url', $cur_url = urlencode(__SELF__));
        define('RES', C('site_url').THEME_PATH . 'common');
        define('STATICS', C('site_url').TMPL_PATH . 'static');
        //Mack
        if($_REQUEST['token']){
        	$_SESSION['wtoken']=$_REQUEST['token'];
        }
        define('TO',$_SESSION['wtoken']);
        if($_REQUEST['openid']){
        	$_SESSION['openid']=$_REQUEST['openid'];
        }
        define('OP',$_SESSION['openid']);
        $where['token']=$this->_get('token','trim');
        $this->token = $where['token'] ?:$_SESSION['wtoken'];
        $this->openid= $_REQUEST['openid']?$_REQUEST['openid']:$_SESSION['openid'];
        $tpl=M('Wxuser')->where($where)->find();
        $this->tpl = $tpl;

        $adminuserdata = M('Users')->field('viptime,status')->where(array('id'=>$tpl['uid']))->find();
        if($adminuserdata){
            if($adminuserdata['viptime'] < time() || $adminuserdata['status'] == 0){
                $this->redirect('Home/Nofind/isover');
            }
        }
        if($this->openid){
            $wxusers = M('Wxusers')->where(array('uid'=>$tpl['id'],'openid'=>$this->openid))->find();
            if($wxusers){
                $this->wxusers = $wxusers;
            }
            $this->assign('openid',$this->openid);
        }
        //新增两个
        session('tid',$tpl['id']);
        session('oid',$this->wxusers['id']);

        $this->assign('token',$this->token);
	    $this->assign('wxusers',$this->wxusers);
        $this->assign('action', $this->getActionName());
        $this->assign('tpl', $tpl);
        $appidInfo=M('Diymen_set')->where(array('token'=>$this->token))->find();
        $this->assign('appidInfo',$appidInfo);

        $redirect_uri = urlencode(sprintf('%s/Home/Auth/share/token/%s/openid/%s/jump_url/%s', $site_url, $this->token, $this->openid, base64_encode(urlencode(__SELF__))));
        if ($tpl['is_auth'] == 1) {
           $sUrl = sprintf('https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=1&component_appid=wxe7be6810523b9ea2#wechat_redirect', $tpl['authorizer_appid'], $redirect_uri);
        }else{
           $sUrl=  sprintf('https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect', $appidInfo['appid'], $redirect_uri);
        }
        $this->shareUrl = $sUrl;
        $this->assign('shareUrl', $sUrl);





        /*
         * 引入微信js接口
        */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $this->signPackage = $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);

        //$this->autoShare();
    }


    protected function all_insert($name = '', $back = '/index',$type=true)
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->add();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
                    'Product',
                	'Store',
                	'Test',
                    'Selfform',
                    'Xt',
                    'Medical',
                    'Reservation',
                    'Shipin',
                    'Vote' ,
                    'Wxq'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']     = $id;
                    $data['module']  = $name;
                    $data['token']   = session('token');
                    $data['keyword'] = $_POST['keyword'];
                    M('Keyword')->add($data);
                }
                $this->success('操作成功', U(MODULE_NAME . $back),$type);
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back),$type);
            }
        }
    }

    protected function all_insert2($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error2($db->getError());
        } else {
            $id = $db->add();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
                    'Product',
                	'Store',
                	'Test',
                    'Selfform',
                    'Xt',
                    'Medical',
                    'Reservation',
                    'Shipin',
                    'Vote',
                    'Wxq'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']     = $id;
                    $data['module']  = $name;
                    $data['token']   = session('token');
                    $data['keyword'] = $_POST['keyword'];
                    M('Keyword')->add($data);
                }
                $this->success2('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error2('操作失败', U(MODULE_NAME . $back));
            }
        }
    }

    protected function insert($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error2($db->getError());
        } else {
            $id = $db->add();
            if ($id == true) {
                $this->success2('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error2('操作失败', U(MODULE_NAME . $back));
            }
        }
    }
    protected function save($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error2($db->getError());
        } else {
            $id = $db->save();
            if ($id == true) {
                $this->success2('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error2('操作失败', U(MODULE_NAME . $back));
            }
        }
    }
    protected function all_save($name = '', $back = '/index',$type=true,$seach)
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->save();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
                    'Product',
                	'Store',
                	'Test',
                    'Selfform',
                    'Xt',
                    'Medical',
                    'Reservation',
                    'Shipin',
                    'Vote',
                    'Wxq'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']    = $_POST['id'];
                    $data['module'] = $name;
                    $data['token']  = session('token');
                    $da['keyword']  = $_POST['keyword'];
                    M('Keyword')->where($data)->save($da);
                }
                if($seach){
                    $this->success('操作成功', U('Img/index',array('seach'=>$seach)),$type);
                }else{
                    $this->success('操作成功', U(MODULE_NAME . $back),$type);
                }

            } else {
                if($seach){
                    $this->success('操作失败', U('Img/index',array('seach'=>$seach)),$type);
                }else{
                    $this->error('操作失败', U(MODULE_NAME . $back),$type);
                }

            }
        }
    }

    protected function all_save2($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error2($db->getError());
        } else {
            $id = $db->save();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
                    'Product',
                	'Store',
                	'Test',
                    'Selfform',
                    'Xt',
                    'Medical',
                    'Reservation',
                    'Shipin',
                    'Vote',
                    'Wxq'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']    = $_POST['id'];
                    $data['module'] = $name;
                    $data['token']  = session('token');
                    $da['keyword']  = $_POST['keyword'];
                    M('Keyword')->where($data)->save($da);
                }
                $this->success2('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error2('操作失败', U(MODULE_NAME . $back));
            }
        }
    }

    protected function all_del($id, $name = '', $back = '/index',$type=true)
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->delete($id)) {
            $this->ajaxReturn('操作成功', U(MODULE_NAME . $back),$type);
        } else {
            $this->ajaxReturn('操作失败', U(MODULE_NAME . $back),$type);
        }
    }
    protected function _upload(){
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize = 3292200;
        //设置上传文件类型
        $upload->allowExts = explode(',','jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = './data/attachments/';
        //设置需要生成缩略图，仅对图像文件有效
        $upload->thumb = true;
        // 设置引用图片类库包路径
        $upload->imageClassPath = '@.ORG.Image';
        //设置需要生成缩略图的文件后缀
        $upload->thumbPrefix = 'm_';
        //生产2张缩略图
        //设置缩略图最大宽度
        $upload->thumbMaxWidth = '720';
        //设置缩略图最大高度
        $upload->thumbMaxHeight = '400';
        //设置上传文件规则
        $upload->saveRule = uniqid;
        //删除原图
        $upload->thumbRemoveOrigin = true;
        if (!$upload->upload()) {
            //捕获上传异常
            return $upload->getErrorMsg();
        }else{
            //取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
            return $uploadList;
        }
    }



    public function encode($var) {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false'; // Lowercase necessary!
            case 'integer':
            case 'double':
                return sprintf( '"%s"', $var);
            case 'resource':
            case 'string':
                return '"'. str_replace(array("\r", "\n", "\t", '\\\'', "/"),
                    array('\r', '\n', '\t', '\'', '\\/'),
                    addslashes($var)) .'"';
            case 'array':
                // Arrays in JSON can't be associative. If the array is empty or if it
                // has sequential whole number keys starting with 0, it's not associative
                // so we can go ahead and convert it as an array.
                if ( empty ($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                    $output = array();
                    foreach ($var as $v) {
                        $output[] = $this->encode($v);
                    }
                    return '['. implode(',', $output) .']';
                }
            // Otherwise, fall through to convert the array as an object.
            case 'object':
                $output = array();
                foreach ($var as $k => $v) {
                    $output[] =  $this->encode(strval($k)) .':'.  $this->encode($v);
                }
                return '{'. implode(',', $output) .'}';
            default:
                return 'null';
        }
    }

    public function api_notice_increment($url, $data){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }else{
            return $tmpInfo;
        }
    }

    public function getWxAccesstoken(){
        $array = array();
        $array['access_token'] = '';
        $array['code'] = 0;
        $array['msg'] = '成功';
        if($this->tpl['is_auth'] == 1){
            if($this->tpl['service_type_info'] == 2 || ($this->tpl['verify_type_info'] >=0 && ($this->tpl['service_type_info'] == 0 || $this->tpl['service_type_info'] == 1))){
                $componseaccess = $this->getAccesstoken();
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=".$componseaccess['component_access_token'];
                    $repostdata = array();
                    $repostdata['component_appid'] = 'wxe7be6810523b9ea2';
                    $repostdata['authorizer_appid'] = $this->tpl['authorizer_appid'];
                    $repostdata['authorizer_refresh_token'] = $this->tpl['authorizer_refresh_token'];
                    $repostdata = $this->encode($repostdata);
                    $json = $this->api_notice_increment($accessurl,$repostdata);
                    $json = json_decode($json);
                    $array['access_token'] = $json->authorizer_access_token;
                }else{
                    $array['code'] = -1;
                    $array['msg'] = '获取失败组件access_token';
                }
            }else{
                $array['code'] = -2;
                $array['msg'] = '您的帐号类型还没有此权限哦';
            }

        }else {
            $api = M('Diymen_set')->where(array('token' => session('token')))->find();
            $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $api['appid'] . '&secret=' . $api['appsecret'];

            if($api['appid']==false||$api['appsecret']==false){
                $array['code'] = -3;
                $array['msg'] = '必须先填写AppId与AppSecret';
            }else{
                $json = json_decode(file_get_contents($url_get));
                $array['access_token'] = $json->access_token;
            }

        }

        return $array;

    }

    public function getAccesstoken(){
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
        $data = array();
        $data['component_appid'] = 'wxe7be6810523b9ea2';
        $data['component_appsecret'] = '0c79e1fa963cd80cc0be99b20a18faeb';
        $res = M('Open_ticket_set')->where(array('AppId'=>'wxe7be6810523b9ea2'))->find();
        if($res['ComponentVerifyTicket'] != null){
            $data['component_verify_ticket'] = $res['ComponentVerifyTicket'];
        }
        $data = $this->encode($data);
        $returndata = $this->api_notice_increment($url,$data);
        $returndata = json_decode($returndata,true);
        return $returndata;
    }

    public function getAccessTokenByTwoType(){
        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        if($api){
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
            //echo $url_get;exit;
            $json=json_decode(file_get_contents($url_get));
            if(!isset($json->access_token)){
                return false;
            }else{
                return $json->access_token;
            }
        }else{
	        $url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
	        $data = array();
	        $data['component_appid'] = 'wxe7be6810523b9ea2';
	        $data['component_appsecret'] = '0c79e1fa963cd80cc0be99b20a18faeb';
	        $res = M('Open_ticket_set')->where(array('AppId'=>'wxe7be6810523b9ea2'))->find();
	        if($res['ComponentVerifyTicket'] != null){
	            $data['component_verify_ticket'] = $res['ComponentVerifyTicket'];
	        }
	        $data = $this->encode($data);
	        $returndata = $this->api_notice_increment($url,$data);
	        $returndata = json_decode($returndata,true);
	        return $returndata->component_access_token;
        }
    }

    /**
     *  模板分配
     **/
    public function UDisplay($sTemplate = '', $sSuffix='.html')
    {
    	$this->autoshare();
    	$sTemplate = empty($sTemplate) ? (MODULE_NAME.'_'.ACTION_NAME) : $sTemplate;
        $this->display(sprintf('%s%s/%s%s', TMPL_PATH, $this->_sTplBaseDir, $sTemplate, $sSuffix));
    }


    /*
    分享接口
    $aData
    var shareTitle="{weikcums:$aData.title}";
    var imgUrl="{weikcums:$aData.imgUrl}";
    var descContent="{weikcums:$aData.descContent}";
    var shareUrl="{weikcums:$aData.shareUrl}";
    */
    protected function wxJSDK($aData,$wxJSSDK = 'wxJSSDK'){
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);
        $this->assign('aData',$aData);
        $this->assign($wxJSSDK,$this->fetch('./tpl/Wap/default/public/wxJSSDK.html'));
    }

    public function autoShare()
    {
        if (!$this->autoShare) {
            return false;
        }
        $imgUrl = Arr::get($this->tpl, 'headpicurl');
        $sShare = <<<HTML
    <script src="/tpl/static/wapweiui/media/js/jquery-1.11.1.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

    <script type="text/javascript">
        var shareTitle='';
        var imgUrl="{$imgUrl}";
        var descContent="";
        var shareUrl= '{$this->shareUrl}';

        $(function() {
            var config = {};
            if(typeof(shareConfig) == 'function'){
                config = shareConfig();
            }
            config = $.extend({}, {
	    	'descContent' : $('title').text(),
             'shareTitle' : $('title').text(),
             'imgUrl'     : '{$imgUrl}',
		    'shareUrl'	 : '{$this->shareUrl}'
            }, config);
            shareTitle = config.shareTitle;
            imgUrl     = config.imgUrl;
            descContent= config.descContent;
	        shareUrl	= config.shareUrl;
        });

        wx.config({
            debug: false,
            appId: "{$this->signPackage['appId']}",
            timestamp: {$this->signPackage['timestamp']},
            nonceStr: "{$this->signPackage['nonceStr']}",
            signature: "{$this->signPackage['signature']}",
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage',// 所有要调用的 API 都要加到这个列表中
				'onMenuShareQQ',
				'onMenuShareWeibo',
				'scanQRCode',
				'hideMenuItems'
            ]
        });
        wx.ready(function () {
            if(typeof(wxready) == 'function'){
                wxready();
            }
            // 在这里调用 API
            wx.onMenuShareTimeline({
                title: shareTitle, // 分享标题
                link: shareUrl, // 分享链接
                imgUrl: imgUrl, // 分享图标
                success: function () {
                    if(typeof(shareSuccess) == 'function'){
                        shareSuccess('onMenuShareTimeline');
                    }
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareAppMessage({
                title: shareTitle, // 分享标题
                desc: descContent, // 分享描述
                link: shareUrl, // 分享链接
                imgUrl: imgUrl, // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
					if(typeof(shareSuccess) == 'function'){
                        shareSuccess('onMenuShareAppMessage');
                    }
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareQQ({
                title: shareTitle, // 分享标题
                desc: descContent, // 分享描述
                link: shareUrl, // 分享链接
                imgUrl: imgUrl, // 分享图标
                success: function () {
                   // 用户确认分享后执行的回调函数
				   if(typeof(shareSuccess) == 'function'){
                        shareSuccess('onMenuShareQQ');
                    }
                },
                cancel: function () {
                   // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareWeibo({
                title: shareTitle, // 分享标题
                desc: descContent, // 分享描述
                link: shareUrl, // 分享链接
                imgUrl: imgUrl, // 分享图标
                success: function () {
                   // 用户确认分享后执行的回调函数
				   if(typeof(shareSuccess) == 'function'){
                        shareSuccess('onMenuShareWeibo');
                    }
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareQZone({
                title: shareTitle, // 分享标题
                desc: descContent, // 分享描述
                link: shareUrl, // 分享链接
                imgUrl: imgUrl, // 分享图标
                success: function () {
					if(typeof(shareSuccess) == 'function'){
                        shareSuccess('onMenuShareQZone');
                    }
                   // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

        });

    </script>
HTML;

       // if ($this->is_auth()) {
            echo $sShare;
       // }
    }

    /*
     *  判断一个m和a是否授权
     */
    public function is_auth($m=null, $a=null)
    {

        $m = $m ? $m : MODULE_NAME;
        $a = $a ? $a : ACTION_NAME;
        return !!M('Diymen_class')->where(array(
            'token' => $this->token,
            'url'   => array('like', "%m=".$m."&%"),
            'url'   => array('like', "%a=".$a."&%")
        ))->getField('auth_url');
    }
    public function ring(){
        if(isset($_GET['lasttime'])){
            $where['token'] = $this->token;
            $where['add_time'] = array('gt',urldecode($_GET['lasttime']));
            $neworder = M($_GET['ring_model'])->where($where)->getField('add_time');
            if($neworder){
                $this->ajaxReturn(array('code'=>0,'msg'=>'success','lasttime'=>$neworder));
            }else{
                $this->ajaxReturn(array('code'=>1));
            }
        }else{
            $this->ajaxReturn(array('code'=>-1,'msg'=>'非法请求'));
        }
    }

    public function jret($iCode=0, $sMsg='', $aData=array())
    {
        exit(json_encode(array(
            'status'    => $iCode,
            'msg'       => $sMsg,
            'data'      => $aData
        )));
    }
}
?>

