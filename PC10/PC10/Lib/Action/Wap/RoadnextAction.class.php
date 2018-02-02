<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2015/6/4
 * Time: 9:28
 */
class RoadnextAction extends BaseAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/roadnext';

    /**
     *  Token
     **/
    protected $_sToken = null;

    public function _initialize()
    {
        $this->_sToken = $this->_get('token');
        parent::_initialize();
    }

    /*路况资讯*/
    public function traffic(){
        $aClassify = M('Road_traffic_classify')->where(array('token'=>$this->_sToken))->select();
        if(IS_AJAX){
            $iTid = $_POST['cid'];
            $plen = $this->_post('page','intval') * 15;
            if($plen != 0){
                $aInformation = M('Road_traffic_information')->where(array('cid'=>$iTid,'token'=>$this->_sToken,'is_release'=>1))->order('add_time desc')->limit($plen.',15')->select();
            }else{
                $aInformation = M('Road_traffic_information')->where(array('cid'=>$iTid,'token'=>$this->_sToken,'is_release'=>1))->order('add_time desc')->limit(15)->select();
            }
            $this->assign(array('information'=>$aInformation,'type'=>$_POST['type']));

            $info = $this->fetch('./tpl/Wap/default/roadnext/trafficinfo.html');
            echo $this->encode(array(
                'status' => 1,
                'fetch' => $info
            ));exit;
        }else{
            $aClassifys = $aClassify[0];
            $iTid =$aClassifys['id'];
            $aInformation = M('Road_traffic_information')->where(array('cid'=>$iTid,'token'=>$this->_sToken,'is_release'=>1))->order('add_time desc')->limit(15)->select();
            $this->assign(array('information'=>$aInformation,'iTid'=>$iTid,'type'=>$aClassifys['type']));
        }
        $this->assign('classify',$aClassify);
        $this->UDisplay('traffic');
    }
    /*路况资讯详情*/
    public function trafficinfo(){
        $iIid = $_GET['id'];
        $aInformation = M('Road_traffic_information')->where(array('id'=>$iIid,'token'=>$this->_sToken))->find();

        $this->assign(array('info'=>$aInformation));
        $this->UDisplay('info');
    }

    /*高速快览*/
    public function line(){
        $otrafficModel = M('Road_traffic_information'); //车友爆料
        $oVmsModel = M('Road_quickfacts_vms');//出行提示
        $oInfoModel = M('Road_quickfacts_maintain'); //交通事故（施工计划）
        $type = $_GET['type'];
        if(!$type){
            header('Location:'.U('Roadnext/line',array('token'=>$this->token,'openid'=>$this->openid,'type'=>1)));
        }
        if($type == 1){//出行提示
            $aVms = $oVmsModel->where(array('token'=>$this->_sToken/*,'aid'=>$_GET['aid']*/))->order('add_time desc')->limit(5)->select();
            $this->assign('info',$aVms);
            $this->assign('type',$type);
        }
        if($type == 2){//交通事故
            $aInfo = $oInfoModel->where(array('token'=>$this->_sToken,'ctype'=>2))->order('add_time desc')->limit(5)->select();
            $this->assign('info',$aInfo);
            $this->assign('type',$type);
        }
        if($type == 3){//施工计划
            $infos = $oInfoModel->where(array('token'=>$this->_sToken,'ctype'=>1))->order('add_time desc')->limit(5)->select();
            $this->assign('info',$infos);
            $this->assign('type',$type);
        }
        if($type == 4){//车友爆料
            $aBaoliao = $otrafficModel->where(array('token'=>$this->_sToken,'cid'=>0,'is_release'=>1))->order('add_time desc')->limit(5)->select();
            $this->assign('info',$aBaoliao);
            $this->assign('type',$type);
        }
        if(IS_AJAX){
            $plen = $this->_post('page','intval') * 5;
            $type = $_REQUEST['type'];
            if($type == 1){//出行提示
                if($plen){
                    $aVms = $oVmsModel->where(array('token'=>$this->_sToken))->order('add_time desc')->limit($plen.',5')->select();
                }else{
                    $aVms = $oVmsModel->where(array('token'=>$this->_sToken))->order('add_time desc')->limit(5)->select();
                }
                $this->assign('info',$aVms);
                $this->assign('type',$type);
                $info = $this->fetch('./tpl/Wap/default/roadnext/lineinfo.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            if($type == 2){//交通事故
                if($plen){
                    $aInfo = $oInfoModel->where(array('token'=>$this->_sToken,'ctype'=>2))->order('add_time desc')->limit($plen.',5')->select();
                }else{
                    $aInfo = $oInfoModel->where(array('token'=>$this->_sToken,'ctype'=>2))->order('add_time desc')->limit(5)->select();
                }
                $this->assign('info',$aInfo);
                $this->assign('type',$type);
                $info = $this->fetch('./tpl/Wap/default/roadnext/lineinfo.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            if($type == 3){//施工计划
                if($plen){
                    $infos = $oInfoModel->where(array('token'=>$this->_sToken,'ctype'=>1))->order('add_time desc')->limit($plen.',5')->select();
                }else{
                    $infos = $oInfoModel->where(array('token'=>$this->_sToken,'ctype'=>1))->order('add_time desc')->limit(5)->select();
                }

                $this->assign('info',$infos);
                $this->assign('type',$type);
                $info = $this->fetch('./tpl/Wap/default/roadnext/lineinfo.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            if($type == 4){//车友爆料
                if($plen){
                    $aBaoliao = $otrafficModel->where(array('token'=>$this->_sToken,'cid'=>0,'is_release'=>1))->order('add_time desc')->limit($plen.',5')->select();
                }else{
                    $aBaoliao = $otrafficModel->where(array('token'=>$this->_sToken,'cid'=>0,'is_release'=>1))->order('add_time desc')->limit(5)->select();
                }

                $this->assign('info',$aBaoliao);
                $this->assign('type',$type);
                $info = $this->fetch('./tpl/Wap/default/roadnext/lineinfo.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
        }
        $this->UDisplay('line');
    }


    /*司机上传交通资讯*/
    public function settraffic(){
        $aClassify = M('Road_traffic_classify')->where(array('token'=>$this->_sToken))->select();
        $this->assign(array(
            'classify'=>$aClassify,
        ));
        if(IS_AJAX){
            $data = array(
                'token'=>$this->_sToken,
                'openid'=>$_REQUEST['openid'],
                'cid'=>0,
                'type_id'=>$_POST['cid'],
                'content'=>$_POST['contents'],
                'phone'=>$_POST['phone'],
                'type'=>1,
                'add_time'=>date('Y-m-d H:i:s'),
                'pic'=>$_POST['pic']
            );
	   
            $sets =  M('Road_traffic_information')->add($data);
            if($sets){
                echo $this->encode(array(
                    'status' => 1,
                    'msg' => '上传成功，等待审核中'
                ));exit;
            }else{
                echo $this->encode(array(
                    'status' => 0,
                    'msg' => '系统繁忙，请售后！'
                ));exit;
            }
        }
        $this->UDisplay('settraffic');
    }
    /*高速快照*/
    public function police(){
        $oPlatform = M('Road_quickfacts_platform');
        $oLine = M('Road_quickfacts_line');
        $aLine = $oLine->where(array('token'=>$this->_sToken,'aid'=>$_GET['aid']))->select();
        if(IS_AJAX){
            $aplatform = $oPlatform->where(array('token'=>$this->_sToken,'lid'=>$_POST['lid']))->select();
            $this->assign('info',$aplatform);
            $info = $this->fetch('./tpl/Wap/default/roadnext/policeinfo.html');
            echo $this->encode(array(
                'status' => 1,
                'fetch' => $info
            ));exit;
        }

        $this->assign('line',$aLine);
        $this->UDisplay('police');
    }

    public function uploadpic(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 512000;// 设置附件上传大小
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');//设置上传文件类型
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->saveRule = "image_".time();
        $upload->savePath = __ROOT__ . 'upload/' . $this->token . '/image/' . date('Ymd') . '/';// 设置附件上传目录
        if (!$upload->upload()) {    // 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        } else {                      // 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            exit(json_encode($info));
        }
    }

    /*服务区导航*/
    public function index(){
        $classify = M('Road_area')->where(array('token'=>$this->token))->select();
        $this->assign('classify',$classify);
        $oService = M('Road_quickfacts_service');
        if(IS_AJAX){
            $aService = $oService->where(array('token'=>$this->_sToken))->select();
            foreach($aService as $k=>$val){
                $aService[$k]['jl']=floor(getdistance($_POST['lat'],$_POST['lng'],$val['position_y'],$val['position_x']))*2;
                $aService[$aService[$k]['jl']."_".$k]=$aService[$k];
                unset($aService[$k]);
            }
            ksort($aService);
            $this->assign('info',$aService);
            $info = $this->fetch('./tpl/Wap/default/roadnext/indexinfo.html');
            echo $this->encode(array(
                'status' => 1,
                'fetch' => $info
            ));exit;
        }else{
            $aClassifys = $classify[0];
            $iTid =$aClassifys['id'];
            $this->assign('type',$iTid);
            $aService = $oService->where(array('token'=>$this->_sToken))->select();
            $this->assign('info',$aService);
        }
        $this->UDisplay('index');
    }
    public function weixin_img(){
        if(IS_POST){
         //   var_dump($_POST);
	    $_POST['imgs'] = urldecode($_POST['imgs']);
            $img=explode(',',$_POST['imgs']);
            $access_token  = $this->getAccessToken();

            //目录
            $dir="./upload/weixin_imgs/".date('Y',time())."/".date('m',time())."/".date('d',time());
            if(!is_dir($dir)){
                mkdir($dir,0777,true);
            }
            $urls=array();
            foreach($img as $v){
                $mediaid=$v;
                $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaid";
		$fileInfo = downloadWeixinFile($url);
                $filename = $dir."/".getSn().".jpg";//取名字
		
                saveWeixinFile($filename, $fileInfo["body"]);
                $urls['imgs'][]=C('site_url').$filename;
            }
            echo json_encode($urls);


            //json返回图片路径地址


            }
    }
  
  
  
    function getAccessToken(){
        $api=M('Diymen_set')->where(array('token'=>$this->_sToken))->find();
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


    public function baiduapp(){
        $this->UDisplay('baiduapp');
    }


    /*服务建议*/
    public function jianyi(){
        $aUser = M('Wxuser')->where(array('token'=>$this->_sToken))->find();
        $aUsers = M('Wxusers')->where(array('uid'=>$aUser['id'],'openid'=>$this->openid))->find();
        $config = M('Liuyan_config')->where(array('token'=>$this->_sToken))->find();
        $rep = M('reply_info');
        $repic = $rep->where(array('infotype'=> "Liuyan", 'token'=> $this->token))->field('picurl')->find();

        if($config['type']== 0){
            $list = M('Liuyan')->where(array('token'=>$this->_sToken))->order('createtime desc')->select();
        }elseif($config['type']== 1){
            $list = M('Liuyan')->where(array('token'=>$this->_sToken,'wecha_id'=>$this->openid))->order('createtime desc')->select();
        }
        $this->assign(array(
            'ausers'=>$aUsers,
            'list'=>$list,
            'repic'=>$repic
        ));
        $this->UDisplay('jianyi');
    }

    public function ajaxjianyi(){
        $oLiuyan = M('Liuyan');
        if(IS_AJAX){
            $data['title'] = $_POST['nickname'];
            $data['text'] = $_POST['info'];
            $data['phone'] = $_POST['phone'];
            $data['createtime'] = time();
            $data['token'] = $this->_sToken;
            $data['wecha_id'] = $this->openid;
            if($oLiuyan->add($data)){
                echo $this->encode(array(
                    'status' => 1,
                    'msg' => '提交成功'

                ));exit;
            }

        }
    }

    /*高速快照第三版*/
    public function threepolice(){
        $oplatfrom = M('Road_quickfacts_platform');
        $oline = M('Road_quickfacts_line');
        $aline = $oline->where(array('token'=>$this->_sToken))->order('id desc')->limit(1)->select();
        //P($aline[0]['id']);exit;   $aline[0]['id']
        $aplatfrom = $oplatfrom->where(array('token'=>$this->_sToken,'lid'=>1))->select();
        foreach($aplatfrom as $k=>$val){
            $aoplatfrom = $oplatfrom->where(array('token'=>$this->_sToken,'lid'=>$val['olid']))->select();
            $aplatfrom[$k]['aoplatfrom'] = $aoplatfrom;
        }
        //P($aplatfrom);exit;
        $list = $aplatfrom;
        $last = array_pop($list);
        $this->assign(array(
            'list'=>$list,
            'platfrom'=>$aplatfrom,
            'last'=>$last
        ));
        $this->UDisplay('threepolice');
    }

    public function piclan(){
        $this->UDisplay('piclan');
    }




}