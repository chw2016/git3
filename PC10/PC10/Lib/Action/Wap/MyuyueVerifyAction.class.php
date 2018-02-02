<?php
/*
 * 旅游
 */
class MyuyueVerifyAction extends BaseAction{
    /*
     * Tpl Dir
     */
    private $Vanke;
    protected $_sTplBaseDir = 'Wap/default/vanke';

    protected function _initialize(){
        parent::_initialize();
    }

    /*
     *  验证
     */
    public function index()
    {
        if (IS_POST) {
            try{
                Vendor('Group.Order');
                $Order = new Order($this->token, $this->openid);
                $ret = $Order->verifyYuyue($_REQUEST['verify']);
                if($ret) {
                    return $this->jret(0);
                }else{
                    return $this->jret(-1);
                }
            }catch(Exception $E){
                return $this->jret(-1, $E->getMessage());
            }
        }else{
            $this->UDisplay('yuyue_verify');
        }
    }
}
