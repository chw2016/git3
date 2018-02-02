<?php
class SunPoolAction extends Table1Action {
    public $_sTplBaseDir = 'User/default/miye';
    public function _initialize()
    {
        parent::_initialize();


        $this->tpl="tpl/User/default/helper/";

    }
    protected function setHeader(){
        return array(
            array(
                'name' => '安装申请',
                'url'  => U('SunPool/index', array('token' => $this->_sToken))
            ),



        );
    }

    public function index(){
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            $_POST=$_REQUEST;
            $aWhere=$this->search($_POST);
            $aWhere['token'] =$this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//���������id������Ҫ����
                'HeadHover' => U('SunPool/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                 /*   array(
                        'name' => '��ӷ��Ŵ��Ʒ',
                        'url' => U('Loan/add_pinzhong',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '����',
                        'url' => U('Loan/fl_index',array('token'=>$this->token))
                    ),*/

                ),
                'tips' => array(
                    '用户安装申请列表'
                ),
                'Table_Header' => array(
                    'ID','姓名','手机号码','省','市','安装面积(㎡)','是否业主','邮箱','申请时间'
                ),
                'List_Opt' => array(
                    /*array(
                        'name' => '�༭',
                        'url' => U('Loan/PinzhongEdit')
                    ),
                    array(
                        'name' => '����ʱ��',
                        'url' => U('Loan/set_time',array('token'=>$this->token))
                    ),
                    array(
                        'name' => 'ɾ��',
                        'url' => U('Loan/PinzhongDel')
                    ),*/
                ),
                /* 'search'=>array(
                       array('title'=>'����1','name'=>'li_name'),
                       array('title'=>'����2','name'=>'eq_name2'),
                       array('title'=>'���ʱ��','name'=>'be_add_time','type'=>'between')
                   )*/
            ),

            M('Sunpool_erect')->where($aWhere)->count(),
            M('Sunpool_erect')->field('id,uname,tel,province,city,squre,host,email,add_time')->where($aWhere),
            array($this,'abc')


        );
        $this->UDisplay('show1');
    }
    public function abc($data){
        foreach($data as $k=>$v){
            if($v['host']==1){
                $data[$k]['host']='是';
            }
            if($v['host']==0){
                $data[$k]['host']='否';
            }




        }
       /* $data[$k]['host']
        if($){

        }*/
        return $data;
}

}

?>