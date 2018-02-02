<?php
class DjAction extends ApiAction {
    public function _initialize()
    {
        $this->Model = M('Js_sn');
        $this->token = 'xxx';
    }


    public function index()
    {
        /*
        $data2 = array(
            array(
                'secret'    => 'xxx',
                'sn'        => 'yyy',
                'is_yj'     => 0,
            ),
            array(
                'secret'    => 'xxx',
                'sn'        => 'yyy',
                'is_yj'     => 1,
            ),
        );
        */
        //$_REQUEST['data'] = '[{"sn":"123456789012345678901","card":"123456","is_yj":false,"customer_code":"100006    ","f_time":"2015-09-16T14:11:50.557"}]';
        foreach($aData=json_decode($_REQUEST['data'], true) as $k => $data){
            $data['token'] = $this->token;
            if ($data['card']) {
                if($this->Model->where(array('card' => $data['card']))->count() > 0){
                    if(false === $this->Model->where(array('card' => $data['card']))->data($data)->save()){
                        exit($this->error(1003, '系统繁忙，请稍后再试'));
                    };
                }else{
                    /*
                    if(!$this->Model->data($data)->add()){
                        exit($this->error(1004, '系统繁忙，请稍后再试'));
                    };
                    */
                }
            }
        };
        exit($this->success(0));
    }
}
