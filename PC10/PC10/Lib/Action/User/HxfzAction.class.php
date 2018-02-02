<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2015/5/11
 * Time: 9:36
 */
class HxfzAction extends TableAction{
    protected $_sTplBaseDir = 'User/default/hxfz';

    public function _initialize()
    {
        parent::_initialize();
    }
    protected function setHeader(){
        return array(
            array(
                'name' => '图片管理',
                'url'  => U('Hxfz/index', array('token' => $this->_sToken))
            )
        );
    }

    public function index(){
        $oImgaModel = M('Imag');
        $this->assign(array(
            'onepic'=>$oImgaModel->where(array('token'=>$this->_sToken,'app'=>'Hxfz','type'=>'indexpicone'))->find(),
            'twopic'=>$oImgaModel->where(array('token'=>$this->_sToken,'app'=>'Hxfz','type'=>'indextwo'))->find(),
            'therepic'=>$oImgaModel->where(array('token'=>$this->_sToken,'app'=>'Hxfz','type'=>'indexthere'))->find(),
        ));

        $this->UDisplay('index');

    }


}