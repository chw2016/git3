<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-5-28
 * Time: 上午9:51
 * To change this template use File | Settings | File Templates.
 */
class AuthAction extends Action{


    public function index(){
        $code = $_GET['code'];
        $token = $_GET['token'];
        $id = $_GET['id'];
        if($token && $code && $id){
            $menudata = M('Diymen_class')->where(array('token' => $token, 'id' => $id))->find();
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        $jump_url = htmlspecialchars_decode($menudata['url']) . "&openid=" . $openid;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata && $menudata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $jump_url = htmlspecialchars_decode($menudata['url']) . "&openid=" . $openid . "&wecha_id=" . $openid;
                        header('Location:' . $jump_url);
                    } else {
                        $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }
    }
    //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa78dbfa797889d7a&redirect_uri=http://v.wapwei.com/index.php/Home/Auth/allindex/token/5d8a87bab30de695954b17fc835b9d12/id/774&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect

    public function allindex(){
        $token = $_GET['token'];
        $id = $_GET['id'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $id){
            $menudata = M('Diymen_class')->where(array('token' => $token, 'id' => $id))->find();
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        $jump_url = htmlspecialchars_decode($menudata['url']) . "&openid=" . $openid;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata && $menudata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        $jump_url = htmlspecialchars_decode($menudata['url']) . "&openid=" . $openid;

                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }

    public function allshareindex(){
        $token = $_GET['token'];
        $dopenid = $_GET['dopenid'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];

        if($token && $code){

            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){

                if($componseaccess['component_access_token']){

                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);

		    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){

                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);

                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        $jump_url =  C('site_url').'index.php?g=Wap&m=Distribution&a=register&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
                        //echo $jump_url;exit;
			header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $content = json_decode($content,true);
                    //print_r($data);exit;
                    //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx969b1ea58140b14a&redirect_uri=http://v.wapwei.com/index.php/Home/Auth/allindex/token/f17f0d1e02a8976cf065163525547260/id/2579/dopenid/&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect
                    if ($content) {
                        $openid = $content['openid'];
                        $access_token = $content['access_token'];
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        $jump_url =  C('site_url').'index.php?g=Wap&m=Distribution&a=register&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }


    public function getdistributioninfo(){
        $token = $_GET['token'];
        $dopenid = $_GET['dopenid'];
	$type = $_GET['type'];
	$catid = $_GET['catid'];
	$product_id = $_GET['product_id'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];

        if($token && $code){

            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){

                if($componseaccess['component_access_token']){

                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);

		    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){

                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);

                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
			if($catid == 0 && $product_id == 0){
				$jump_url =  C('site_url').'index.php?g=Wap&m=Store_new&a=cats&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
			}elseif($product_id !== 0 && $catid == 0){
				$jump_url =  C('site_url').'index.php?g=Wap&m=Store_new&a=product&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&id=".$product_id;
			}elseif($catid !== 0){
				$jump_url =  C('site_url').'index.php?g=Wap&m=Store_new&a=products&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&catid=".$catid;
			}

                        //echo $jump_url;exit;
			header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $content = json_decode($content,true);
                    //print_r($data);exit;
                    //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx969b1ea58140b14a&redirect_uri=http://v.wapwei.com/index.php/Home/Auth/allindex/token/f17f0d1e02a8976cf065163525547260/id/2579/dopenid/&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect
                    if ($content) {
                        $openid = $content['openid'];
                        $access_token = $content['access_token'];
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
			if($catid == 0 && $product_id == 0){
				$jump_url =  C('site_url').'index.php?g=Wap&m=Store_new&a=cats&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
			}elseif($product_id !== 0 && $catid == 0 ){
				$jump_url =  C('site_url').'index.php?g=Wap&m=Store_new&a=product&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&id=".$product_id;
			}elseif($catid !== 0){
				$jump_url =  C('site_url').'index.php?g=Wap&m=Store_new&a=products&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&catid=".$catid;
			}
                        //$jump_url =  C('site_url').'index.php?g=Wap&m=Distribution&a=register&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }

    /**
     * 分亨钟姐项目回调
     */
    public function getdistributioninfozj(){
        $token = $_GET['token'];
        $dopenid = $_GET['dopenid'];
        $type = $_GET['type'];
        $catid = $_GET['catid'];
        $product_id = $_GET['product_id'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];

        if($token && $code){

            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){

                if($componseaccess['component_access_token']){

                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);

                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){

                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);

                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        if($catid == 0 && $product_id == 0){
                            $jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=cats&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
                        }elseif($product_id !== 0 && $catid == 0){
                            $jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=product&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&id=".$product_id;
                        }elseif($catid !== 0){
                            $jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=products&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&catid=".$catid;
                        }

                        //echo $jump_url;exit;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $content = json_decode($content,true);
                    //print_r($data);exit;
                    //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx969b1ea58140b14a&redirect_uri=http://v.wapwei.com/index.php/Home/Auth/allindex/token/f17f0d1e02a8976cf065163525547260/id/2579/dopenid/&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect
                    if ($content) {
                        $openid = $content['openid'];
                        $access_token = $content['access_token'];
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        if($catid == 0 && $product_id == 0){
                            $jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=cats&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
                        }elseif($product_id !== 0 && $catid == 0 ){
                            $jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=product&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&id=".$product_id;
                        }elseif($catid !== 0){
                            $jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=products&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&catid=".$catid;
                        }
                        //$jump_url =  C('site_url').'index.php?g=Wap&m=Distribution&a=register&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }



    /**
     * 分亨钟姐项目回调
     */
    public function getdistributioninfozj2(){
    	$token = $_GET['token'];
    	$dopenid = $_GET['dopenid'];
    	$id = $_GET['type'];
    	$catid = $_GET['catid'];
    	$product_id = $_GET['product_id'];
    	$querystring = $_SERVER['QUERY_STRING'];
    	parse_str($querystring,$queryarr);
    	$code = $queryarr['code'];

    	if($token && $code){

    		$wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
    		$componseaccess = $this->getComAccesstoken();
    		if($wxuserdata['is_auth'] == 1){

    			if($componseaccess['component_access_token']){

    				$accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
    				$json = file_get_contents($accessurl);

    				$json = json_decode($json,true);
    				if($json['access_token'] && $json['openid']){

    					$openid = $json['openid'];
    					$access_token =$json['access_token'];
    					$getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
    					$userinfocontent = file_get_contents($getuserinfourl);

    					$userinfodata = json_decode($userinfocontent);
    					if ($userinfodata) {
    						$wxuserModel = M('Wxuser');
    						$wxusersModel = M('Wxusers');
    						$wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
    						$updatedata = array();
    						$updatedata['nickname'] = $userinfodata->nickname;
    						$updatedata['sex'] = $userinfodata->sex;
    						$updatedata['language'] = $userinfodata->language;
    						$updatedata['city'] = $userinfodata->city;
    						$updatedata['province'] = $userinfodata->province;
    						$updatedata['country'] = $userinfodata->country;
    						$updatedata['headimgurl'] = $userinfodata->headimgurl;
    						if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
    							$wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
    						}
    					}
    			/* 		if($catid == 0 && $product_id == 0){
    						$jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=cats&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
    					}elseif($product_id !== 0 && $catid == 0){
    						$jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=product&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&id=".$product_id;
    					}elseif($catid !== 0){
    						$jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=products&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&catid=".$catid;
    					} */

    					$jump_url =  C('site_url').'index.php?g=Wap&m=MruQianggou&a=show&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&id=".$product_id;

    					//echo $jump_url;exit;
    					header('Location:' . $jump_url);
    				}
    			}else{
    				$this->error('获取失败组件access_token');exit;
    			}
    		}else {
    			$appdata = M('Diymen_set')->where(array('token' => $token))->find();
    			if ($appdata) {
    				//$appdata['appid'] = 'wxa78dbfa797889d7a';
    				//$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
    				$apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
    				$content = file_get_contents($apiurl);
    				$content = json_decode($content,true);
    				//print_r($data);exit;
    				//https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx969b1ea58140b14a&redirect_uri=http://v.wapwei.com/index.php/Home/Auth/allindex/token/f17f0d1e02a8976cf065163525547260/id/2579/dopenid/&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect
    				if ($content) {
    					$openid = $content['openid'];
    					$access_token = $content['access_token'];
    					//https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
    					$getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
    					$userinfocontent = file_get_contents($getuserinfourl);
    					$userinfodata = json_decode($userinfocontent);
    					if ($userinfodata) {
    						$wxuserModel = M('Wxuser');
    						$wxusersModel = M('Wxusers');
    						$wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
    						$updatedata = array();
    						$updatedata['nickname'] = $userinfodata->nickname;
    						$updatedata['sex'] = $userinfodata->sex;
    						$updatedata['language'] = $userinfodata->language;
    						$updatedata['city'] = $userinfodata->city;
    						$updatedata['province'] = $userinfodata->province;
    						$updatedata['country'] = $userinfodata->country;
    						$updatedata['headimgurl'] = $userinfodata->headimgurl;
    						if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
    							$wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
    						}
    					}
    		/* 			if($catid == 0 && $product_id == 0){
    						$jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=cats&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
    					}elseif($product_id !== 0 && $catid == 0 ){
    						$jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=product&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&id=".$product_id;
    					}elseif($catid !== 0){
    						$jump_url =  C('site_url').'index.php?g=Wap&m=Store_shop&a=products&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&catid=".$catid;
    					} */

    					$jump_url =  C('site_url').'index.php?g=Wap&m=MruQianggou&a=show&token='.$token."&dopenid=".$dopenid."&openid=".$openid."&id=".$product_id;

    					//echo $jump_url;exit;
    					header('Location:' . $jump_url);

    					//$jump_url =  C('site_url').'index.php?g=Wap&m=Distribution&a=register&token='.$token."&dopenid=".$dopenid."&openid=".$openid;

    				} else {
    					echo $this->encode(array('code' => -1, 'msg' => 'error'));
    				}
    			}
    		}
    	}else{
    		echo $this->encode(array('code'=>-2,'msg'=>'null error'));
    	}
    }


    /**
     * 如多分期回调
     */
    public function loan(){
        $token = $_GET['token'];
        $dopenid = $_GET['dopenid'];
        $type = $_GET['type'];
     //   $catid = $_GET['catid'];
     //   $product_id = $_GET['product_id'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];

        if($token && $code){

            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){

                if($componseaccess['component_access_token']){

                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);

                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){

                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);

                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        $jump_url =  C('site_url').'index.php?g=Wap&m=Loan&a=index&token='.$token."&dopenid=".$dopenid."&openid=".$openid;



                        //echo $jump_url;exit;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $content = json_decode($content,true);
                    //print_r($data);exit;
                    //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx969b1ea58140b14a&redirect_uri=http://v.wapwei.com/index.php/Home/Auth/allindex/token/f17f0d1e02a8976cf065163525547260/id/2579/dopenid/&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect
                    if ($content) {
                        $openid = $content['openid'];
                        $access_token = $content['access_token'];
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        $jump_url =  C('site_url').'index.php?g=Wap&m=Loan&a=index&token='.$token."&dopenid=".$dopenid."&openid=".$openid;


                        //$jump_url =  C('site_url').'index.php?g=Wap&m=Distribution&a=register&token='.$token."&dopenid=".$dopenid."&openid=".$openid;
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }



    public function sendTextMsg(){
        $token = $_REQUEST['token'];
        $openid = $_REQUEST['openid'];
        $content = $_REQUEST['content'];
        if(!empty($token) && !empty($openid)){
            $code = $this->isMyUsers($token,$openid);
            if($code == 1){
                $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
                $componseaccess = $this->getComAccesstoken();
                if($wxuserdata['is_auth'] == 1) {
                    if ($componseaccess['component_access_token']) {
                        $accessurl = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=".$componseaccess['component_access_token'];
                        $repostdata = array();
                        $repostdata['component_appid'] = 'wxe7be6810523b9ea2';
                        $repostdata['authorizer_appid'] = $wxuserdata['authorizer_appid'];
                        $repostdata['authorizer_refresh_token'] = $wxuserdata['authorizer_refresh_token'];
                        $repostdata = $this->encode($repostdata);
                        $json = $this->api_notice_increment($accessurl,$repostdata);
                        $json = json_decode($json);
                        if($json->authorizer_access_token){
                            $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $json->authorizer_access_token;
                            $api_content = array();
                            $api_content['touser'] = $openid;
                            $api_content['msgtype'] = "text";
                            $api_content['text'] = array(
                                'content' => $content
                            );
                            $api_content = $this->encode($api_content);
                            $returninfo = $this->api_notice_increment($url, $api_content);
                            if ($returninfo) {
                                echo $this->encode(array('code' => 0, 'msg' => '发送成功'));
                            }
                        }else{
                            echo $this->encode(array('code' => -1, 'msg' => '获取access_token失败'));
                        }
                    }
                }else {
                    $accesstoken = $this->getAccessToken($token);
                    if ($accesstoken) {
                        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accesstoken;
                        $api_content = array();
                        $api_content['touser'] = $openid;
                        $api_content['msgtype'] = "text";
                        $api_content['text'] = array(
                            'content' => $content
                        );
                        $api_content = $this->encode($api_content);
                        $returninfo = $this->api_notice_increment($url, $api_content);
                        if ($returninfo) {
                            echo $this->encode(array('code' => 0, 'msg' => '发送成功'));
                        }
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => '获取access_token失败'));
                    }
                }
            }else if($code == 2){
                echo $this->encode(array('code'=>-2,'msg'=>'不能给没有关注您的用户发送消息'));
            }else{
                echo $this->encode(array('code'=>-3,'msg'=>'非法请求'));
            }

        }




    }

    public function sendNewsMsg(){
        $token = $_REQUEST['token'];
        $openid = $_REQUEST['openid'];
        $content = $_REQUEST['content'];
        if(!empty($token) && !empty($openid)){
            $code = $this->isMyUsers($token,$openid);
            if($code == 1){
                $accesstoken = $this->getAccessToken($token);
                if($accesstoken){
                    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$accesstoken;
                    $api_content = array();
                    $api_content['touser']= $openid;
                    $api_content['msgtype']= "news";
                    $api_content['news']= array(
                        'articles'=>array(json_decode($content,true))
                    );
                    $api_content = $this->encode($api_content);
                    $returninfo = $this->api_notice_increment($url,$api_content);
                    if($returninfo){
                        echo $this->encode(array('code'=>0,'msg'=>'发送成功'));
                    }
                }else{
                    echo $this->encode(array('code'=>-1,'msg'=>'获取access_token失败'));
                }
            }else if($code == 2){
                echo $this->encode(array('code'=>-2,'msg'=>'不能给没有关注您的用户发送消息'));
            }else{
                echo $this->encode(array('code'=>-3,'msg'=>'非法请求'));
            }

        }
    }


    private function isMyUsers($token,$openid){
        $Wxuser=M('Wxuser')->field('id')->where(array('token'=>$token))->find();
        if($Wxuser){
            $Wxusers=M('Wxusers')->field('id')->where(array('uid'=>$Wxuser['id'],'openid'=>$openid,'status'=>1))->find();
            if($Wxusers){
                return 1;
            }else{
                return 2;
            }
        }else{
            return 3;
        }

    }

    private function getAccessToken($token){
        $api=M('Diymen_set')->where(array('token'=>$token))->find();
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
            return false;
        }
    }

    public function sendTemplateMsg(){
        $token = $_REQUEST['token'];
        $openid = $_REQUEST['openid'];
        $data = $_REQUEST['data'];
        if(!empty($token) && !empty($openid)){
            $code = $this->isMyUsers($token,$openid);
            if($code == 1){
                $accesstoken = $this->getAccessToken($token);
                if($accesstoken){
                    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$accesstoken;
                    $api_content = $data;
                    $returninfo = $this->api_notice_increment($url,$api_content);
                    if($returninfo){
                        echo $this->encode(array('code'=>0,'msg'=>$returninfo));
                    }
                }else{
                    echo $this->encode(array('code'=>-1,'msg'=>'获取access_token失败'));
                }
            }else if($code == 2){
                echo $this->encode(array('code'=>-2,'msg'=>'不能给没有关注您的用户发送消息'));
            }else{
                echo $this->encode(array('code'=>-3,'msg'=>'非法请求'));
            }
        }
    }

    private function api_notice_increment($url, $data){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
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

    public function getComAccesstoken(){
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

    public function toupiaoget(){
        $token = $_GET['token'];
        $wid = $_GET['wid'];

        $mid = $_GET['mid'];
        $sid = $_GET['sid'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $wid && $mid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Works&a=poll&token=".$token."&mid=".$mid."&sid=".$sid."&wid=".$wid."&openid=" . $openid;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Works&a=poll&token=".$token."&mid=".$mid."&sid=".$sid."&wid=".$wid."&openid=" . $openid;

                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }

    //建南国旅
    public function forward(){
        $token = $_GET['token'];
        $openid=$_GET['openid'];
        $lid=$_GET['lid'];
        $linkid=$_GET['linkid'];

        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        if($userinfodata.$openid==$_GET['openid']){
                            $url=C("site_url")."index.php?g=Wap&m=Forward&a=index&token=".$token."&openid=".$_GET['openid']."&lid=".$lid."&";
                            header('Location:' . $url);
                        }else{
                            $url=C("site_url")."index.php?g=Wap&m=Forward&a=share&token=".$token."&openid=".$_GET['openid']."&lid=".$lid."&linkid=".$linkid."&";
                            header('Location:' . $url);
                        }
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        //$jump_url = C('site_url') . "index.php?g=Wap&m=Works&a=poll&token=".$token."&mid=".$mid."&sid=".$sid."&id=".$wid."&openid=" . $openid;

                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        if($userinfodata.$openid==$_GET['openid']){
                            $url=C("site_url")."index.php?g=Wap&m=Forward&a=index&token=".$token."&openid=".$_GET['openid']."&lid=".$lid."&";
                            header('Location:' . $url);
                        }else{
                            $url=C("site_url")."index.php?g=Wap&m=Forward&a=share&token=".$token."&openid=".$_GET['openid']."&lid=".$lid."&linkid=".$linkid."&";
                            header('Location:' . $url);
                        }
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }


       public function zhuanarticle(){
        $token = $_GET['token'];
        $id = $_GET['id'];
        $dopenid = $_GET['dopenid'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $id){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Index&a=content&token=".$token."&type=1&id=".$id."&dopenid=".$dopenid."&openid=" . $openid;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Index&a=content&token=".$token."&type=1&id=".$id."&dopenid=".$dopenid."&openid=" . $openid;

                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }

    public function kechengbaoming(){
        $token = $_GET['token'];
        $id = $_GET['cid'];
        $from_openid = $_GET['from_openid'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $id && $from_openid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Course&a=content&token=".$token."&cid=".$id."&openid=" . $openid."&from_openid=".$from_openid;

                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Course&a=content&token=".$token."&cid=".$id."&openid=" . $openid."&from_openid=".$from_openid;

                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }


     /*自媒体任务分享*/
    public function taskshare(){
        $token = $_GET['token'];
        $tid = $_GET['tid'];
        $from_openid = $_GET['openid'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $tid && $from_openid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Media&a=taskdetail&token=".$token."&tid=".$tid."&from_openid=".$from_openid."&openid=" . $openid;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        //$jump_url = C('site_url') . "index.php?g=Wap&m=Works&a=poll&token=".$token."&tid=".$tid."&from_openid=".$from_openid."&openid=" . $openid;
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Media&a=taskdetail&token=".$token."&tid=".$tid."&from_openid=".$from_openid."&openid=" . $openid;
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }

    /*
     *  统一分享
     */
    public function share(){
        $token          = $_GET['token'];
        $from_openid    = $_GET['openid'];
	WL('jump_url'.$_GET['jump_url']);
        $url            = urldecode(base64_decode($_GET['jump_url']));
	WL($url);
        //将url中的openid去掉
        $aUrl = parse_url($url);
        parse_str($aUrl['query'], $a);
        if(isset($a['openid'])) unset($a['openid']);
        $aUrl['query']   = http_build_query($a);
        $jump_url       = C('site_url') . $aUrl['path'] . '?' . $aUrl['query'];

	WL($jump_url);
        $querystring    = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $from_openid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();

            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        $jump_url       = $jump_url ."&dopenid=" . $from_openid . "&openid=" . $openid . "&wecha_id=" . $openid;
                        WL('wxconfig:'.$jump_url);
			header('Location:' . $jump_url);

                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        //$jump_url = C('site_url') . "index.php?g=Wap&m=Media&a=taskdetail&token=".$token."&tid=".$tid."&from_openid=".$from_openid."&openid=" . $openid;
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        $jump_url       = $jump_url ."&dopenid=" . $from_openid . "&openid=" . $openid  . "&wecha_id=" . $openid;;
                        WL('wxconfig:'.$jump_url);
			header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }


    /*自媒体营销活动*/

    public function marketingactor(){
        $token = $_GET['token'];
        $mid = $_GET['mid'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $mid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Media&a=marketingcon&token=".$token."&mid=".$mid."&openid=" . $openid;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Media&a=marketingcon&token=".$token."&mid=".$mid."&openid=" . $openid;
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }

    /*自媒体分享我的资料页*/
    public function getMyhome(){
        $token = $_GET['token'];
        $from_openid= $_GET['from_openid'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $from_openid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Media&a=myhome&token=".$token."&from_openid=".$from_openid."&openid=" . $openid;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Media&a=myhome&token=".$token."&from_openid=".$from_openid."&openid=" . $openid;
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }

    // 德亿堡分享
    public function commercedyb(){
        $token = $_GET['token'];
        $dopenid = $_GET['dopenid'];
        $querystring = $_SERVER['QUERY_STRING'];
        $type = !empty($_GET['type']) ?  $_GET['type'] : 0;
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $dopenid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        if($type == 0){
                            $jump_url = C('site_url') . "index.php?g=Wap&m=Commercedyb&a=indexdyb&token=".$token."&openid=".$openid."&dopenid=".$dopenid;
                        }else{
                            $jump_url = C('site_url') . "index.php?g=Wap&m=Commercedyb&a=indexdyb&token=".$token."&openid=".$openid."&dopenid=".$dopenid;
                        }
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        if($type == 0){
                            $jump_url = C('site_url') . "index.php?g=Wap&m=Commercedyb&a=indexdyb&token=".$token."&openid=".$openid."&dopenid=".$dopenid;
                        }else{
                            $jump_url = C('site_url') . "index.php?g=Wap&m=Commercedyb&a=indexdyb&token=".$token."&openid=".$openid."&dopenid=".$dopenid;
                        }
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }
    /*CFO积分兑换后的分享*/
    public function getcfoshare(){
        $token = $_GET['token'];
        $from_openid= $_GET['from_openid'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $from_openid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Course&a=score&token=".$token."&from_openid=".$from_openid."&openid=" . $openid;
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        $jump_url = C('site_url') . "index.php?g=Wap&m=Course&a=score&token=".$token."&from_openid=".$from_openid."&openid=" . $openid;
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }


    // 德亿堡分享
    public function commercedybhuati(){
        $token = $_GET['token'];
        $dopenid = $_GET['dopenid'];
        $id = $_GET['id'];
        $type = $_GET['type'];
        $querystring = $_SERVER['QUERY_STRING'];
        parse_str($querystring,$queryarr);
        $code = $queryarr['code'];
        if($token && $code && $dopenid){
            $wxuserdata = M('Wxuser')->where(array('token'=>$token))->find();
            $componseaccess = $this->getComAccesstoken();
            if($wxuserdata['is_auth'] == 1){
                if($componseaccess['component_access_token']){
                    $accessurl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=".$wxuserdata['authorizer_appid']."&code=".$code."&grant_type=authorization_code&component_appid=wxe7be6810523b9ea2&component_access_token=".$componseaccess['component_access_token'];
                    $json = file_get_contents($accessurl);
                    $json = json_decode($json,true);
                    if($json['access_token'] && $json['openid']){
                        $openid = $json['openid'];
                        $access_token =$json['access_token'];
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        //http://v.wapwei.com/index.php?g=Wap&m=Works&a=poll&token=7591997f281dbe891bd7668112736d02&mid=24&sid=0&wid=50&openid=oZigIjxG9GDdtOF0uFnfx-nvpMAQ
                        if($type == 1){
                            $jump_url = C('site_url') . "index.php?g=Wap&m=Commercedyb&a=lookUpDetails1&token=".$token."&openid=".$openid."&from_openid=".$dopenid."&articleId=".$id;
                        }else{
                            $jump_url = C('site_url') . "index.php?g=Wap&m=Commercedyb&a=lookUpDetails&token=".$token."&openid=".$openid."&from_openid=".$dopenid."&articleId=".$id;
                        }
                        header('Location:' . $jump_url);
                    }
                }else{
                    $this->error('获取失败组件access_token');exit;
                }
            }else {
                $appdata = M('Diymen_set')->where(array('token' => $token))->find();
                if ($appdata) {
                    //$appdata['appid'] = 'wxa78dbfa797889d7a';
                    //$appdata['appsecret'] = 'e494f91e1e442f016d5484c6f789560a';
                    $apiurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appdata['appid'] . "&secret=" . $appdata['appsecret'] . "&code=" . $code . "&grant_type=authorization_code";
                    $content = file_get_contents($apiurl);
                    $data = json_decode($content);
                    if ($data) {
                        $openid = $data->openid;
                        $access_token = $data->access_token;
                        if($type == 1){
                            $jump_url = C('site_url') . "index.php?g=Wap&m=Commercedyb&a=lookUpDetails1&token=".$token."&openid=".$openid."&from_openid=".$dopenid."&articleId=".$id;
                        }else{
                            $jump_url = C('site_url') . "index.php?g=Wap&m=Commercedyb&a=lookUpDetails&token=".$token."&openid=".$openid."&from_openid=".$dopenid."&articleId=".$id;
                        }
                        //https://api.weixin.qq.com/sns/userinfo?access_token=OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw&openid=oLVPpjqs9BhvzwPj5A-vTYAX3GLc
                        $getuserinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token;
                        $userinfocontent = file_get_contents($getuserinfourl);
                        $userinfodata = json_decode($userinfocontent);
                        if ($userinfodata) {
                            $wxuserModel = M('Wxuser');
                            $wxusersModel = M('Wxusers');
                            $wxuserdata = $wxuserModel->field('id')->where(array('token' => $token))->find();
                            $updatedata = array();
                            $updatedata['nickname'] = $userinfodata->nickname;
                            $updatedata['sex'] = $userinfodata->sex;
                            $updatedata['language'] = $userinfodata->language;
                            $updatedata['city'] = $userinfodata->city;
                            $updatedata['province'] = $userinfodata->province;
                            $updatedata['country'] = $userinfodata->country;
                            $updatedata['headimgurl'] = $userinfodata->headimgurl;
                            if ($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
                                $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save();
                            }
                        }
                        header('Location:' . $jump_url);
                    } else {
                        echo $this->encode(array('code' => -1, 'msg' => 'error'));
                    }
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'null error'));
        }
    }


}
