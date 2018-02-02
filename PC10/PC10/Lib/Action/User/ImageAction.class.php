<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/10
 * Time: 10:01
 */
class ImageAction extends UserAction{
    public function _initialize(){
        parent::_initialize();
        $this->imag = D('Imag');
    }
    public function imgaCreate(){
        $oImagModel = $this->imag;
        $aWhere = array(
            'app'=>$_REQUEST['app'],
            'token'=>$this->token,
            'type'=>$_REQUEST['type']
        );
        $aData = $oImagModel->where($aWhere)->find();
        if(IS_POST){
            if($aData){
                //修改
                if($oImagModel->where($aWhere)->save($_POST)){

                    $this->success('修改成功！');
                }else{
                    $this->error('没有修改！');
                }
            }else{
                //新增
                $_POST['add_time'] = date('Y-m-d H:i:s');
                if($oImagModel->add($_POST)){
                    $this->success('上传成功！');
                }else{
                    $this->error('上传失败！');
                }
            }
        }else{
            $this->assign(array(
                'data'=>$aData
            ));
            $this->display('imgaCreate');
        }
    }
}