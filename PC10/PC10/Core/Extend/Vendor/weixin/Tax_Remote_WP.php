<?php

class Tax_Remote_WP {

    private $cookieFile;
    private $loginFile;
    private $checkcode;
    private $lastTimeFile;
    private $expire = 3;
    private $user;

    public function init($user){
        //$user is openid;File cookie is to save users cookies;lastTimeFile is to save users last login time,to make sure
        //login is not login time out.checkcode is to save checkcode users images;function init() can get explore's cookie
       // and return the cookie;

        $this->user = $user;
        $this->cookieFlie = APP_PATH.'Tax/cookie/'.$this->user.'.tmp';
        $this->lastTimeFile = APP_PATH.'Tax/time/'.$this->user.'.txt';
        $this->checkcode = APP_PATH.'Tax/checkcode/'.$this->user.'.png';
        if(!file_exists($this->cookieFile)){
            $fh = fopen($this->cookieFile,"w");
            fclose($fh);
        }
        if(!file_exists($this->checkcode)){
            $fl=fopen($this->checkcode,'w');
            fclose($fl);
        }
        if(!file_exists($this->lastTimeFile)){
            $fc=fopen($this->lastTimeFile,'w');
            fclose($fc);
        }
        $needLogin=true;
        $nowTime=time();
        if($lastTime=file_get_contents($this->lastTimeFile)){

        }else{
            $lastTime=0;
        }

        if(($nowTime-$lastTime)<=$this->expire){
            $needLogin=false;
        }
        if($needLogin==true){
            $checkcode = $this->getCheckcode($this->cookieFlie);
            $url="http://etax.szgs.gov.cn/applicaton?namespace=security&origin=hnav_bar.jsp&event=link.login";
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_HEADER,0);
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');
            curl_setopt($ch,CURLOPT_CONNECTTIMEOU,0);
            curl_setopt($ch,CURLOPT_COOKIEFILE,$this->cookieFlie);
            curl_exec($ch);
            $this->lastTimeFile = file_put_contents($this->lastTimeFile,time());
            if($this->cookieFile = file_get_contents($this->cookieFile) &&  $this->lastTimeFile && $checkcode){  //$this->cookieFile is request cookie
                return true;
                curl_close($ch);
            }else{
                return false;
            }
        }else{
            if($this->cookieFile=file_get_contents($this->cookieFile)){
                return true;
            }else{
                return false;
            }
        }
    }

//  Function getCheckcode is to get checkcode,before you use this function ,you need excue init() first;
    public function getCheckcode($cookieFile){
        $url="http://etax.szgs.gov.cn/nsywgl/gxhfw/checkCodeImg.jsp";
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_REFERER,$url);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        curl_setopt($ch,CURLOPT_COOKIEJAR,$cookieFile);
        $img=curl_exec($ch);
        $fp = fopen($this->checkcode,'w');
        $checkImg = fwrite($fp,$img);
        fclose($fp);
        curl_close($ch);
        if($checkImg){
            return true;
        }else{
            return false;
        }
    }

    public function login($user,$password,$checkcode,$isUkey,$cookieDir,$time,$openid){
        $url = 'http://etax.szgs.gov.cn/wbyhlogin.do?method=nsr_login';
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_REFERER,'http://etax.szgs.gov.cn/applicaton?namespace=security&origin=hnav_bar.jsp&event=link.login');
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        curl_setopt($ch,CURLOPT_COOKIEFILE,$cookieDir);
        $post['username'] = $user;
        $post['password'] = $password;
        $post['checkcode'] = $checkcode;
        $post['isUkey'] = $isUkey;
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($post));
        $img=curl_exec($ch);
        if($img){
            return true;
        }else{
            return false;
        }
       //echo $img;
    }

    public function userCheck($openid,$username,$cookieDir){
        $url = 'http://etax.szgs.gov.cn/wbyhlogin.do?method=yzUkey&username='.$username;
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_REFERER,'http://etax.szgs.gov.cn/applicaton?namespace=security&origin=hnav_bar.jsp&event=link.login');
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        curl_setopt($ch,CURLOPT_COOKIEFILE,$cookieDir);
        curl_setopt($ch,CURLOPT_COOKIEJAR,$cookieDir);
        $img=curl_exec($ch);
        curl_close($ch);
        if($img){
            echo $img;
        }else{
            return false;
        }
    }

    public function noDeclare($cookieDir){
        $url = 'http://etax.szgs.gov.cn/nsywgl/bsfwt/sb/common.do?method=CheckNsrsbjs';
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_REFERER,'http://etax.szgs.gov.cn/applicaton?namespace=security&origin=hnav_bar.jsp&event=link.login');
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        curl_setopt($ch,CURLOPT_COOKIEFILE,$cookieDir);
        curl_setopt($ch,CURLOPT_COOKIEJAR,$cookieDir);
        $img=curl_exec($ch);
        if(!strpos($img,'验证码有误' ) && !strpos($img,'重新登录')){
            $str = '/nsywgl/bsfwt/sb/common/js/sb_common.js';
            $toStr = '/Tax/js/sb_common.js';
            $content = str_replace($str,$toStr,$img);
            $str1 = '/resources/js';
            $toStr1 = '/Tax/js' ;
            $content = str_replace($str1,$toStr1,$content);
           // $content = str_replace('charset=GBK','charset=UTF-8',$content);
            file_put_contents(APP_PATH.'/Tax/js/text.html',$content);
            return $content;
        }else{
            return false;
        }
        curl_close($ch);
    }

    public function yjsb($sqx_q,$sqqx_z,$pzzl_dm,$cookieDir){
        $url = "http://etax.szgs.gov.cn/nsywgl/bsfwt/sb/common.do?method=yjsb&sqqx_q=".$sqqx_q. "&sqqx_z=" .$sqqx_z. + "&pzzl_dm=" .$sqqx_z;
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_REFERER,'http://etax.szgs.gov.cn/applicaton?namespace=security&origin=hnav_bar.jsp&event=link.login');
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        curl_setopt($ch,CURLOPT_COOKIEFILE,$cookieDir);
        curl_setopt($ch,CURLOPT_COOKIEJAR,$cookieDir);
        $img=curl_exec($ch);
        if($img){
            echo "<script>alert('一键申报成功')</script>";
        }else{
            echo "<script>alert('一键申报失败')</script>";
        }
        curl_close($ch);
    }




} 