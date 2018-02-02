<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2015/6/4
 * Time: 9:28
 */
class RoadAction extends BaseAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/road';

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
        $iTid = $_GET['cid'];
        if($iTid){
            $aInformation = M('Road_traffic_information')->where(array('cid'=>$iTid,'token'=>$this->_sToken,'is_release'=>1))->order('add_time desc')->select();
        }
        //print_r($aInformation);
        $this->assign(array(
            'classify'=>$aClassify,
            'information'=>$aInformation
        ));
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
        $oAreaModel = M('Road_quickfacts_area');
        $oLineModel = M('Road_quickfacts_line');
        $oInfoModel = M('Road_quickfacts_maintain');
        $aArea = $oAreaModel->where(array('token'=>$this->_sToken))->select();
        $aArea = array_merge(array(array('aname'=>'全省')),$aArea);
        //print_r();exit;
        if($_REQUEST['aid']){
            $aLine = $oLineModel->where(array('aid'=>$_REQUEST['aid'],'token'=>$this->_sToken))->select();
        }else{
            $aLine = $oLineModel->where(array('token'=>$this->_sToken))->select();
        }
        foreach($aLine as $k=>$val){
            $barricade = intval($oInfoModel->where(array('token'=>$this->_sToken,'lid'=>$val['id'],'ctype'=>2))->count());//路障；
            $evert = intval($oInfoModel->where(array('token'=>$this->_sToken,'lid'=>$val['id'],'ctype'=>1))->count());//事故；
            $aLine[$k]['barricade'] = $barricade;
            $aLine[$k]['evert'] = $evert;

        }

        $this->assign(array(
            'area'=>$aArea,
            'line'=>$aLine,
        ));
        $this->UDisplay('line');
    }

    public function ajaxline(){
        $oInfoModel = M('Road_quickfacts_maintain');
        if(IS_AJAX){
            $lid = $_POST['lid'];
            if($_POST['type']){
                $aInfo = $oInfoModel->where(array('lid'=>$lid,'token'=>$this->_sToken,'ctype'=>$_POST['type']))->select();
                $this->assign(array(
                    'info'=>$aInfo,
                    //'lid'=>$lid
                    'ctype'=>$_POST['type']
                ));
                $line_next = $this->fetch('./tpl/Wap/default/road/line_next.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetchs' => $line_next
                ));exit;
            }else{
                $aInfo = $oInfoModel->where(array('lid'=>$lid,'token'=>$this->_sToken,'ctype'=>2))->select();
                $this->assign(array(
                    'info'=>$aInfo,
                    'lid'=>$lid,
                    'ctype'=>$_POST['type']
                ));
                $line_info = $this->fetch('./tpl/Wap/default/road/line_info.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $line_info
                ));exit;
            }
        }

    }

    /*高速预览*/
    public function platfrom(){
        $oplatfrom = M('Road_quickfacts_platform');
        $aplatfrom = $oplatfrom->where(array('lid'=>$_REQUEST['lid']))->order('id')->select();
        foreach($aplatfrom as $k=>$val){
            $aplatfrom[$k]['service'] = $this->aservice($this->_sToken,$val['id']);         //$aservice;
            $aplatfrom[$k]['lvms'] = $this->avms($this->_sToken,$val['id'],1); //$alvms;
            $aplatfrom[$k]['lmaintain'] = $this->amaintain($this->_sToken,$val['id'],1,1); //$almaintain;
            $aplatfrom[$k]['lmaintains'] = $this->amaintain($this->_sToken,$val['id'],2,1);//$almaintains;
            $aplatfrom[$k]['rvms'] = $this->avms($this->_sToken,$val['id'],2);//$arvms;
            $aplatfrom[$k]['rmaintain'] = $this->amaintain($this->_sToken,$val['id'],1,2);//$armaintain;
            $aplatfrom[$k]['rmaintains'] = $this->amaintain($this->_sToken,$val['id'],2,2);//$armaintains;
        }

        $endinfo = end($aplatfrom);
        $startinfo = reset($aplatfrom);
        $this->assign(array(
            'aplatfrom'=>$aplatfrom,
            'startinfo'=>$startinfo,
            'endinfo'=>$endinfo
        ));
        $this->UDisplay('platfrom');
    }
    public function aservice($token,$pid){
        $oservice = M('Road_quickfacts_service'); //服务区
        $aservice = $oservice->where(array('token'=>$token,'pid'=>$pid))->select();
        return $aservice;
    }
    public function avms($token,$pid,$type){
        $ovms = M('Road_quickfacts_vms'); //vms
        $alvms = $ovms->where(array('token'=>$token,'pid'=>$pid,'type'=>$type))->select();
        return $alvms;
    }
    public function amaintain($token,$pid,$ctype,$type){
        $omaintain = M('Road_quickfacts_maintain'); //事件
        $almaintain = $omaintain->where(array('token'=>$token,'pid'=>$pid,'ctype'=>$ctype,'type'=>$type))->select();
        return $almaintain;
    }


    /*
     * 高速预览点击路旁的事件
     * */
    public function ajaxsign(){
        $oplatfrom = M('Road_quickfacts_platform');
        $omaintain = M('Road_quickfacts_maintain'); //事件表
        $oservice = M('Road_quickfacts_service'); //服务区
        $ovms = M('Road_quickfacts_vms');  //vms
        if(IS_AJAX){
            $pid = $_POST['pid'];
            $sid = $_POST['sid'];
            $vid = $_POST['vid'];
            $mid = $_POST['mid'];
            $ctype = $_POST['ctype'];
            $type = $_POST['type'];
            /*站台*/
            if($pid !='' && $sid==''&& $vid==''&& $mid==''&& $ctype==''&& $type==''){
                $aInfo = $oplatfrom->where(array('id'=>$pid))->find();
                $this->assign(array(
                    'info'=>$aInfo,
                ));
                $info = $this->fetch('./tpl/Wap/default/road/platfrominfo.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            /*服务区*/
            if($pid !='' && $sid !=''&& $vid==''&& $mid==''&& $ctype==''&& $type==''){
                $aInfo = $oservice->where(array('pid'=>$pid,'token'=>$this->_sToken))->find();
                $this->assign(array(
                    'info'=>$aInfo,
                ));
                $info = $this->fetch('./tpl/Wap/default/road/service.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            /*vms 左*/
            if($pid !='' && $sid ==''&& $vid !=''&& $mid==''&& $ctype==''&& $type=='1'){
                $aInfo = $ovms->where(array('pid'=>$pid,'token'=>$this->_sToken,'type'=>1))->select();
                $this->assign(array(
                    'info'=>$aInfo,
                ));
                $info = $this->fetch('./tpl/Wap/default/road/vms.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            /*vms 右*/
            if($pid !='' && $sid ==''&& $vid !=''&& $mid==''&& $ctype==''&& $type=='2'){
                $aInfo = $ovms->where(array('pid'=>$pid,'token'=>$this->_sToken,'type'=>2))->select();
                $this->assign(array(
                    'info'=>$aInfo,
                ));
                $info = $this->fetch('./tpl/Wap/default/road/vms.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            /*事件 维护 左*/
            if($pid !='' && $sid ==''&& $vid ==''&& $mid !=''&& $ctype=='1'&& $type=='1'){
                $aInfo = $omaintain->where(array('pid'=>$pid,'token'=>$this->_sToken,'ctype'=>1,'type'=>1))->find();
                $this->assign(array(
                    'info'=>$aInfo,
                ));
                $info = $this->fetch('./tpl/Wap/default/road/maintain.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            /*事件 维护 右*/
            if($pid !='' && $sid ==''&& $vid ==''&& $mid !=''&& $ctype=='1'&& $type=='2'){
                $aInfo = $omaintain->where(array('pid'=>$pid,'token'=>$this->_sToken,'ctype'=>1,'type'=>2))->find();
                $this->assign(array(
                    'info'=>$aInfo,
                ));
                $info = $this->fetch('./tpl/Wap/default/road/maintain.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            /*事件 事故 左*/
            if($pid !='' && $sid ==''&& $vid ==''&& $mid !=''&& $ctype=='2'&& $type=='1'){
                $aInfo = $omaintain->where(array('pid'=>$pid,'token'=>$this->_sToken,'ctype'=>2,'type'=>1))->find();

                $this->assign(array(
                    'info'=>$aInfo,
                ));
                $info = $this->fetch('./tpl/Wap/default/road/maintain.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
            /*事件 事故 右*/
            if($pid !='' && $sid ==''&& $vid ==''&& $mid !=''&& $ctype=='2'&& $type=='2'){
                $aInfo = $omaintain->where(array('pid'=>$pid,'token'=>$this->_sToken,'ctype'=>2,'type'=>2))->find();
                $this->assign(array(
                    'info'=>$aInfo,
                ));
                $info = $this->fetch('./tpl/Wap/default/road/maintain.html');
                echo $this->encode(array(
                    'status' => 1,
                    'fetch' => $info
                ));exit;
            }
        }
    }


    /*收藏主页面*/
    public function collection(){
        $oCollModel = M('Road_collection');
        $oLineModel = M('Road_quickfacts_line');
        $oInfoModel = M('Road_quickfacts_info');
        $aCollection = $oCollModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $aLine = explode(',',$aCollection['linenum']);
        array_pop($aLine);
        foreach($aLine as $k=>$val){
            $alinenum = $oLineModel->where(array('id'=>$val))->find();
            $aLine[$k] = $alinenum;
        }
        foreach($aLine as $k=>$val){
            $barricade = intval($oInfoModel->where(array('lid'=>$val['id'],'type'=>2))->count());//路障；
            $evert = intval($oInfoModel->where(array('lid'=>$val['id'],'type'=>1))->count());//路障；
            $aLine[$k]['barricade'] = $barricade;
            $aLine[$k]['evert'] = $evert;
        }
        $this->assign(array(
            'line'=>$aLine
        ));
        $this->UDisplay('collection');
    }
    /*收藏添加页*/
    public function addcollection(){
        $oCollModel = M('Road_collection');
        $oLineModel = M('Road_quickfacts_line');
        $aLine = $oLineModel->where(array('token'=>$this->token))->select();
        $aCollection = $oCollModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        if(IS_AJAX){
            $iTem = $oCollModel->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
            if($iTem){
                $isSet= $oCollModel->where(array('token'=>$this->token,'openid'=>$this->openid))->save($_POST);
                if($isSet){
                    echo $this->encode(array('status' => 1, 'msg' => '定制成功'));exit;
                }else{
                    echo $this->encode(array('status' => 0, 'msg' => '定制失败'));exit;
                }
            }else{
                $_POST['token'] = $this->token;
                $_POST['openid'] = $this->openid;
                $isSet = $oCollModel->add($_POST);
                if($isSet){
                    echo $this->encode(array('status' => 1, 'msg' => '定制成功'));exit;
                }else{
                    echo $this->encode(array('status' => 0, 'msg' => '定制失败'));exit;
                }
            }
        }
        $this->assign(array(
            'line'=>$aLine,
            'collection'=>$aCollection
        ));
        $this->UDisplay('addcollection');
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
                'cid'=>$_POST['cid'],
                'content'=>$_POST['contents'],
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




}