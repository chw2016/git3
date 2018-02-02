<?php
/**
 * Created by PhpStorm.
 * User: anyi
 * Date: 2015/7/14
 * Time: 10:06
 */

class RoomAction extends TableAction {
    public $_sTplBaseDir = 'User/default/togethernext';
    public function _initialize()
    {
        parent::_initialize();
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' =>'聊天室',
                'url'  => U('Room/index', array('token' => $this->_sToken))
            ),
        );
    }
    /*聊天室列表页*/
    public function index(){
        //$this->_sToken=$this->_sToken;
        /*     	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$this->_sToken;
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
                }//结束 */
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Room/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '创建聊天室',//2级
                        'url'    => U('Room/setmediation',array('token'=>$this->_sToken))
                    ),


                ),
                'tips' => array(//3级
                    '你可以在这里管理相关内容。'
                ),
                'Table_Header' => array(//4级
                    'ID', '主题', '简介', '聊天室验证码','创建时间','操作'
                ),
                'List_Opt' => array(

                    /*连接上可能会带其他的参数，则参考如下*/
                    /*array(
                        'name' => '用户管理',
                        'tkey' =>'aid', //参数名称；
                        'tval'=>'id',  //参数值
                        'url' => U('Tailg/index', array('token' => $this->_sToken))
                    ),*/

                    array(
                        'name' => '编辑',
                        'url'  => U('Room/setmediation',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name' => '导出记录',
                        'url'  => U('ExportExcel/record',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name' => '场景实时',
                        'tkey' =>'code',
                        'tval'=>'code',
                        'url'  => U('Wap/Room/room',array('token'=>$this->_sToken,'shut'=>1))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Room/delete_mediation',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_code')->where(array('token'=>$this->_sToken))->count(),
            M('Labor_code')->field('id,title,bask,code,add_time')->where(array('token'=>$this->_sToken))->order("add_time"),
            array($this,'mediationinfo')
        );
    }
    public function mediationinfo($data){
        return $data;
    }

    public function record(){
        $room = M('Labor_code')->where(array('id'=>$_GET['id']))->find();
        $info = M('Room_msg')->field('openid,add_time,msg,code')->where(array('code'=>$room['code'],'token'=>$this->token))->order('add_time')->select();
        foreach($info as $key=>$val){
            $user = M('Wxuser')->where(array('token'=>$this->token))->find();
            $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$val['openid']))->find();
            $info[$key]['openid'] = $users['nickname'];
        }
       // P($info);exit;
        exportExcel($info,array('聊天者','时间','内容','聊天室验证码'),$room['title'].'聊天记录');
    }

    /*创建聊天室*/
    public function setmediation(){
        $oModel = M('Labor_code');
        if(IS_POST){
            $id = $_POST['id'];
            $isFind = $oModel->where(array('id'=>$id))->find();
            if($isFind){
                if($oModel->where(array('id'=>$id))->save($_POST)){
                    $this->success('操作成功！');
                }else{
                    $this->error('操作失败!');
                }
            }else{
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['token'] = $this->token;
                $code = $this->randStr();
                $isFinds = $oModel->where(array('code'=>$code,'token'=>$this->token))->find();
                if($isFinds){
                    $this->error('自动生成随机码有重复，创建失败!');
                }else{
                    $_POST['code'] = $code;
                    if($oModel->add($_POST)){
                        $this->success('创建成功！');
                    }else{
                        $this->error('创建失败!');
                    }
                }
            }
        }else{
            $lid = $_GET['id'];
            $info = $oModel->where(array('id'=>$lid))->find();
            $this->assign('info',$info);
        }
        $this->assign(array(
            'ExtraBtn' => array(
                array(
                    'url'  => U('Room/index', array('token' => $this->_sToken)),
                    'name' => '返回'
                )
            ),
        ));
        $this->UDisplay('setmediation');
    }


    public function randStr($len=6) {
        $chars='abdefghijkmnpqrstvwxy23456789'; // characters to build the password from
        mt_srand((double)microtime()*1000000*getmypid()); // seed the random number generater (must be done)
        $code='';
        while(strlen($code)<$len)
            $code.=substr($chars,(mt_rand()%strlen($chars)),1);
        return $code;
    }


    //删除
    public function delete_mediation(){
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Labor_code');
    }

}