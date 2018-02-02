<?php
class CheckAction extends BaseAction
{

    protected $_sTplBaseDir = 'Wap/default/check';

    public function _initialize()
    {
        $this->autoShare = true;       /*分享限制*/
        parent::_initialize();
    }
        public function tuis(){
            $i = M('Check_hy')->where(array(
                'vipsec'=>array(
                    'eq',1
                )))->select();

        }
}