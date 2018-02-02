<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-1
 * Time: 上午11:09
 */
class IntelligentAction extends BaseAction{

    /*
    public $imei;

    protected function _initialize(){
        parent::_initialize();
        $this->token = '5d8a87bab30de695954b17fc835b9d12';
        $this->oIntelligentDeviceUsersModel = M('Intelligent_devices_users');
        $this->oIntelligentGeoListModel = M('Intelligent_geo_list');
        $this->imei = $_REQUEST['imei'];
        //$this->imei = $this->_post('imei');
        if(!$this->imei){
            //echo $this->encode(array('code'=>-1,'msg'=>'非法操作'));exit;
        }
    }

    protected function authimei(){
        $deviceData = $this->oIntelligentDeviceUsersModel->where(array(
            'imei'  => $this->imei,
            'is_on' => 1,
            'token' => $this->token
        ))->find();
        if($deviceData){
            return $deviceData;
        }else{
            return false;
        }
    }

    protected function getOpenid($deviceData){
        return $this->oIntelligentDeviceUsersModel->where(array(
            'device_id'=>$deviceData['id'],
            'imei'=>$this->imei
        ))->find();
    }

    public function test(){

        $this->display('test');
    }

    public function pushgeo(){
        //$lat = $this->_post('lat');
        $lat= $_REQUEST['lat'];
        $lng = $_REQUEST['lng'];
        //$lng = $this->_post('lng');
        if($lng && $lat) {
            if ($deviceData = $this->authimei()) {
                    $aGeolistData = array();
                    $aGeolistData['token'] = $this->token;
                    $aGeolistData['imei'] = $this->imei;
                    $aGeolistData['openid'] =$this->openid;
                    $aGeolistData['lat'] = $lat;
                    $aGeolistData['lng'] = $lng;
                    $aGeolistData['date'] = date("Y-m-d");
                    $aGeolistData['add_time'] = date("Y-m-d H:i:s");
                    if($this->oIntelligentGeoListModel->add($aGeolistData)){
                        echo $this->encode(array('code'=>0,'msg'=>'成功'));
                    }else{
                        echo $this->encode(array('code'=>-4,'msg'=>'系统繁忙'));
                    }
            }else{
                echo $this->encode(array('code'=>-2,'msg'=>'参数错误'));
            }
        }else{
            echo $this->encode(array('code'=>-1,'msg'=>'缺少参数'));
        }

    }

    public function pushimg(){
        if($deviceData =$this->authimei()){
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 2000000 ;// 设置附件上传大小
            $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath =  './upload/deviceimages/'.date("Ym").'/';// 设置附件上传目录
            if(!$upload->upload()) {// 上传错误提示错误信息
                echo $this->encode(array('code'=>-1,'msg'=>$upload->getErrorMsg()));
            }else{// 上传成功
                $info = $upload->getUploadFileInfo();
                $aImageData['image_path'] = $upload->savePath.$info[0]['savename'];
                $aImageData['add_time'] = date("Y-m-d H:i:s");
                $aImageData['date'] = date("Y-m-d");
                $aImageData['imei'] = $this->imei;
                $aImageData['token'] = $this->token;
                $aImageData['openid'] = $deviceData['openid'];
                if(M('Intelligent_images')->add($aImageData)){
                    echo $this->encode(array('code'=>0,'msg'=>'成功'));
                }else{
                    echo $this->encode(array('code'=>-3,'msg'=>'系统繁忙'));
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'参数错误'));
        }
    }

    public function pushvideo(){
        if($deviceData =$this->authimei()){
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 2000000 ;// 设置附件上传大小
            $upload->allowExts  = array('mp4');// 设置附件上传类型
            $upload->savePath =  './upload/devicevideo/'.date("Ym").'/';// 设置附件上传目录
            if(!$upload->upload()) {// 上传错误提示错误信息
                echo $this->encode(array('code'=>-1,'msg'=>$upload->getErrorMsg()));
            }else{// 上传成功
                $info = $upload->getUploadFileInfo();
                $aImageData['image_path'] = $upload->savePath.$info[0]['savename'];
                $aImageData['add_time'] = date("Y-m-d H:i:s");
                $aImageData['date'] = date("Y-m-d");
                $aImageData['imei'] = $this->imei;
                $aImageData['token'] = $this->token;
                $aImageData['openid'] = $deviceData['openid'];
                if(M('Intelligent_video')->add($aImageData)){
                    echo $this->encode(array('code'=>0,'msg'=>'成功'));
                }else{
                    echo $this->encode(array('code'=>-3,'msg'=>'系统繁忙'));
                }
            }
        }else{
            echo $this->encode(array('code'=>-2,'msg'=>'参数错误'));
        }
    }
    */
}
