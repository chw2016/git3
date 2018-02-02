<?php
/**
 *  李铭  P2P系统
 * 2015.6.24
 **/
class Gta_appsetAction extends Gta_commonAction
{

    public $_sTplBaseDir = 'User/default/miye';
    public $token;
    public $wxuser_id;
    public $tpl_dir = './tpl/User/default/gta/';

    public function _initialize()
    {
        parent::_initialize();
        //这里是应用权限判断
        if (session('?app_id')) {
            $app_id = explode(',', session('app_id'));
            $myapp = M('App_list')->field('enter_api')->where(array('id' => array('in', $app_id)))->select();
            $app_action = array();
            foreach ($myapp as $k => $v) {
                $a1 = explode('/', $v['enter_api']);
                array_push($app_action, $a1[1] . 'Action');
            }
            if (!in_array(__CLASS__, $app_action)) {
                $this->error2('您没有此应用的权限');
            }
        }
        //echo __CLASS__;

        $this->tpl = "tpl/User/default/helper/";
        //$this->pz=M("No_credit");
        $this->token = session('token');
        $this->wxuser_id = M('Wxuser')->where(array('token' => $this->token))->getField('id');

    }

    protected function setHeader()
    {
        return array(
            array(
                'name' => '应用管理',
                'url' => U('index', array('token' => $this->_sToken))
            ),


            array(
                'name' => '更多',
                'url' => U('more', array('token' => $this->_sToken))
            ),
            array(
                'name' => '用户协议设置',
                'url' => U('xy', array('token' => $this->_sToken))
            ),
            array(
                'name' => '提现时间',
                'url' => U('time', array('token' => $this->_sToken))
            ),





        );
    }



    /**
     *  主险
     **/
    public function index()
    {

        $aWhere['token'] = $this->_sToken;
        $aWhere['wxuser_uid']=M('Wxuser')->where(array('token'=>$this->token))->getField('uid');
        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                         'name' => '添加应用管理员',
                         'url' => U('add_product',array('token'=>$this->token))
                     ),
                    array(
                        'name' => '添加财务管理员',
                        'url' => U('cw',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '返回',
                        'url' => U('Home/set', array('token' => $this->_sToken))
                    ),




                ),
                'tips' => array(
                    '应用管理员列表!'
                ),
                'Table_Header' => array(
                    'ID', '帐号','应用','财务', '操作'
                ),
                'List_Opt' => array(


                    array(
                        'name' => '修改',
                        'url' => U('edit_product')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_product')
                    ),


                ),
            ),

            M('App_users')->where($aWhere)->order('add_time desc')->count(),
            M('App_users')->field('id,username,app_id,cw')->order('add_time desc')->where($aWhere),
            array($this,'index1')

        );
        $this->UDisplay('show1');
    }
    public function index1($data){

        foreach($data as $k=>$v){
            $a='';
            $app_id=explode(',',$v['app_id']);
            for($i=0;$i<count($app_id);$i++){
                $a.=M('App_list')->where(array('id'=>$app_id[$i]))->getField('app_name').'/';
            }
            $data[$k]['app_id']=trim($a,'/');
        }

        if(!session('?gta_cg')){
           return;
        }else{
            return $data;
        }

    }


    //添加主险
    public function add_product(){
        if(!session('?gta_cg')){
            $this->error2('没有权限');
        }
        $where['token']=$this->token;
        $where['enter_api']=array('like',array(
            '%User/Gta_life/three_product_product%',
            '%User/Gta_touzi/index%',
            '%User/Gta_p2p/index%',
            '%User/Gta_money/index%',
            '%User/Gta_cart/index%',
            '%User/Gta_yiwai/index%',
            '%User/Gta_logistics/index%',
            '%User/Gta_fenxiao/user_index%'
        ),'OR');

        $select1=M('App_list')->field('id as value,app_name as content')->where($where)->select();

        $this->add('App_users',array(
            array('title'=>"帐号",'type'=>"input",'name'=>"username",'msg'=>'帐号不能为空'),
            array('title'=>"密码",'type'=>"input",'name'=>"password",'msg'=>'密码不能为空'),
            array('title'=>"应用",'type'=>"checkbox",'name'=>"app_id",'many'=>$select1),
        ),U('index',array('token'=>$this->token)),array($this,'add_product1'));
    }
    public function add_product1($data){
        $data['wxuser_uid']=M('Wxuser')->where(array('token'=>$this->token))->getField('uid');
        return $data;
    }
    //修改主险
    public function edit_product(){
        if(!session('?gta_cg')){
            $this->error2('没有权限');
        }
        if(M('App_users')->where(array('id'=>$_GET['id']))->getField('cw')){
            $this->Edit('App_users',array(
                array('title'=>"帐号",'type'=>"input",'name'=>"username",'msg'=>'帐号不能为空'),
                array('title'=>"密码",'type'=>"input",'name'=>"password",'msg'=>'密码不能为空'),
                array('title'=>"应用",'type'=>"radio",'name'=>"cw",'many'=>array(
                    array('content'=>'客服权限','value'=>'客服权限'),
                    array('content'=>'核算权限','value'=>'核算权限'),
                    array('content'=>'出纳权限','value'=>'出纳权限'),
                    array('content'=>'会计权限','value'=>'会计权限')
                )),
            ),U('index',array('token'=>$this->token)),array($this,'add_product1'));
        }else{
            $where['token']=$this->token;
            $where['enter_api']=array('like',array(
                '%User/Gta_life/three_product_product%',
                '%User/Gta_touzi/index%',
                '%User/Gta_p2p/index%',
                '%User/Gta_money/index%',
                '%User/Gta_cart/index%',
                '%User/Gta_yiwai/index%',
                '%User/Gta_logistics/index%',
                '%User/Gta_fenxiao/user_index%'
            ),'OR');
            $select1=M('App_list')->field('id as value,app_name as content')->where($where)->select();

            $this->Edit('App_users',array(
                array('title'=>"帐号",'type'=>"input",'name'=>"username",'msg'=>'帐号不能为空'),
                array('title'=>"密码",'type'=>"input",'name'=>"password",'msg'=>'密码不能为空'),
                array('title'=>"应用",'type'=>"checkbox",'name'=>"app_id",'many'=>$select1),
            ),U('index',array('token'=>$this->token)),array($this,'add_product1'));
        }

    }

    //删除主险
    public function del_product(){
        $this->del('App_users');
    }
    public function more(){
        //echo 88;die;

        $this->Edit('Gta_more',array(
            array('title'=>"链接地址",'type'=>"input",'name'=>"url",'placeholder'=>'保险首页更多连接地址','width'=>'600px'),

        ),U('index',array('token'=>$this->token)));
    }
    //协义
    public function xy(){
        $this->Edit('Gta_more',array(
            array('title'=>"协议内容",'type'=>"textarea_1",'name'=>"xy"),

        ),U('index',array('token'=>$this->token)));
    }
    //新增财务
    public function cw(){
        if(!session('?gta_cg')){
            $this->error2('没有权限');
        }
        $this->add('App_users',array(
            array('title'=>"帐号",'type'=>"input",'name'=>"username",'msg'=>'帐号不能为空'),
            array('title'=>"密码",'type'=>"input",'name'=>"password",'msg'=>'密码不能为空'),
            array('title'=>"应用",'type'=>"radio",'name'=>"cw",'many'=>array(
                array('content'=>'客服权限','value'=>'客服权限'),
                array('content'=>'核算权限','value'=>'核算权限'),
                array('content'=>'出纳权限','value'=>'出纳权限'),
                 array('content'=>'会计权限','value'=>'会计权限')
            )),
        ),U('index',array('token'=>$this->token)),array($this,'add_product1'));
    }
    //提现时间
    public function time(){
        //echo 88;die;

        $this->Edit('Gta_more',array(
            array('title'=>"佣金提现时间(天)",'type'=>"input",'name'=>"time",'placeholder'=>'比例输入15，代表佣金两次提现的间隔不能小于15天','width'=>'600px'),

        ),U('index',array('token'=>$this->token)));
    }

}