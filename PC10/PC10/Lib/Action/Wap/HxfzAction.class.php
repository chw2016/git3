<?php
class HxfzAction extends BaseAction{
    public function index()
    {
        $oImgaModel = M('Imag');
        $this->assign(array(
            'onepic'=>$oImgaModel->where(array('token'=>$this->token,'app'=>'Hxfz','type'=>'indexpicone'))->find(),
            'twopic'=>$a=$oImgaModel->where(array('token'=>$this->token,'app'=>'Hxfz','type'=>'indextwo'))->find(),
            'therepic'=>$oImgaModel->where(array('token'=>$this->token,'app'=>'Hxfz','type'=>'indexthere'))->find(),
        ));

        $this->display('./tpl/Wap/default/Hxfz_tpl2.html');
        //$this->display();
    }
}
