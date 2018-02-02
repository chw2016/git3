<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南(anyi测试文件)
 **/
class WxredpayAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

    /**
     *  Token
     **/
    //private $_sToken = null;

    /**
     *  UID
     **/
    //private $_iUID = null;//
    public function _initialize()
    {
        //phpinfo();
        parent::_initialize();
        $this->pz	   = D('Wxredpay');
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '现金红包活动',
                'url'  => U('Wxredpay/index', array('token' => $this->_sToken))
            ),

        );
    }
    //显示
    public function index(){


        //$_SESSION['token']=$_SESSION['token'];
        /*     	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$_SESSION['token'];
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
                }//结束 */
        $this->table(
            array(
                
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Cs/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加现金红包',//2级
                        'url'    => U('Wxredpay/add_content',array('token'=>$_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '活动名称', '开始时间', '结束时间','红包个数','参与人数','红包总金额','发放金额','余额', '开启状态','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看用户',
                        'url'  => U('Wxredpay/reduser',array('token'=>$_SESSION['token']))
                    ),

                    array(
                        'name' => '编辑',
                        'url'  => U('Wxredpay/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('Wxredpay/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束 */
            ),
            M('Wxredpay')->where(array('token'=>$_SESSION['token']))->count(),
            M('Wxredpay')->field('id,act_name,strattime,endtime,total_num,mansnum,total_amount,fanomey,balance,is_open')->where(array('token'=>$_SESSION['token']))->order("id"),
         array($this,'other')
        );

    }
    public function other($data){
        foreach($data as $k=>$val){
            if($val['is_open'] ==0){
                $data[$k]['is_open'] = "开启";
            }elseif($val['is_open'] ==1){
                $data[$k]['is_open'] = "关闭";
            }
            $aTotal_amount = M('Wxredpay')->where(array('id'=>$val['id']))->find();
            $iAmount = M('Redpay_user')->where(array('rid'=>$val['id']))->sum('amount');
            $data[$k]['mansnum'] = intval(M('Redpay_user')->field('distinct(openid) as openid')->where(array('rid'=>$val['id']))->count());
            $data[$k]['total_amount'] = ($val['total_amount'])/100;
            $data[$k]['fanomey'] = $iAmount;
            $data[$k]['balance'] = ($val['total_amount']-$iAmount*100)/100;

        }
        return $data;
    }
    //定义增改函数
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('Cs2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
                } */
        $this->$aaa('Wxredpay',array(
            /*
              * 如果有需求要在input框中提示信息,则加入一个'msg'的键值，表示input 里面的placeholder属性；
              * 如果在input框后面有需求加备注，则加一个'bast'的键值，作用：input后面显示的一个内容，作为一种提醒信息；
              * */
            array('title'=>"活动名称",'type'=>"input",'name'=>"act_name",'value'=>'act_name','msg'=>'请填写活动的名称'),
            array('title'=>"供方名称",'type'=>"input",'name'=>"nick_name",'value'=>'nick_name','msg'=>'请填写如：天虹百货','bast'=>'提供方名称,四个汉字'),
            array('title'=>"商户名称",'type'=>"input",'name'=>"send_name",'value'=>'send_name','msg'=>'请填写如：天虹百货','bast'=>'红包发送者名称,四个汉字'),
            array('title'=>"红包总金额",'type'=>"number",'name'=>"total_amount",'value'=>'total_amount','msg'=>'请填写红包的总金额','bast'=>'元'),
            array('title'=>"红包总个数",'type'=>"number",'name'=>"total_num",'value'=>'total_num','msg'=>'请填写红包的总的个数'),
            array('title'=>"最小红包金额",'type'=>"number",'name'=>"min_value",'value'=>'min_value','msg'=>'最少不低于1元，如1.25','bast'=>'元  注：最少单位为元'),
            array('title'=>"最大红包金额",'type'=>"number",'name'=>"max_value",'value'=>'max_value','msg'=>'最多不高于200元','bast'=>'元  注：最少单位为元'),
            array('title'=>"红包概率",'type'=>"hidden",'name'=>"redchance",'msg'=>'红包概率','value'=>'redchance'),
            array('title'=>"开始时间",'type'=>"time",'name'=>"strattime",'msg'=>'请选择开始时间','value'=>'strattime'),
            array('title'=>"结束时间",'type'=>"time",'name'=>"endtime",'msg'=>'请选择结束时间','value'=>'endtime'),
            array('title'=>"每天领取的最多个数",'type'=>"number",'name'=>"daynum",'value'=>'daynum','msg'=>'请填每天领取的最多个数'),
            array('title'=>"每人领取的最多个数",'type'=>"number",'name'=>"maxnum",'value'=>'maxnum','msg'=>'请填每人领取的最多个数'),
            array('title'=>"红包祝福语",'type'=>"longinput",'name'=>"wishing",'value'=>'wishing','msg'=>'请填写红包祝福语'),
            array('title'=>"备注信息",'type'=>"longinput",'name'=>"remark",'value'=>'remark','msg'=>'请填写活动备注，如：猜越多得越多，快来抢！'),
            /*array('type'=>'select','title'=>"领取方式",'name'=>"type",'value'=>'type','msg'=>'请选择领取方式','many'=>array(
                array('content'=>'请选择领取方式'),
                array('value'=>'0', 'content'=>'直接发放'),
                array('value'=>'1','content'=>'须验证sn码'),
            )),*/
            array('type'=>'select','title'=>"是否开启",'name'=>"is_open",'value'=>'is_open','msg'=>'请选择开启状态','many'=>array(
                array('content'=>'请选择开启状态'),
                array('value'=>'0', 'content'=>'开启'),
                array('value'=>'1','content'=>'关闭'),
            )),

        ),U('Wxredpay/index',array('token'=>$_SESSION['token'])),array($this,'special'),'','',array($this,'headdata'));

    }
    /*针对post上传的数据*/
   public function special($data){
       $data['total_amount'] = $data['total_amount']*100;
       $data['min_value'] = $data['min_value']*100;
       $data['max_value'] = $data['max_value']*100;
       return $data;
   }
    /*编辑数据处理*/
    public function headdata($aData){
        $aData['total_amount'] = $aData['total_amount']/100;
        $aData['min_value'] = $aData['min_value']/100;
        $aData['max_value'] = $aData['max_value']/100;
        return $aData;
    }
    //添加
    public function add_content(){
        $this->add_save(add);
    }
    //编辑
    public function save_content(){
        $this->add_save(Edit);
    }
    //删除
    public function delete_content(){
        //M('Wxredpay')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Wxredpay');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaTable('Wxredpay');
    }




//其它信息开头
    public function reduser(){
        $this->table(
            array(
                //'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Wxredpay/reduser', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Wxredpay/index',array('rid'=>$_GET['id']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','头像', '领取时间', '红包金额'
                ),
                'aListImg' => array(
                    'container' => array('headerimg'),
                    'width'     => 70,
                    'height'    => 70
                ),


            ),
            M('Redpay_user')->where(array('token'=>$_SESSION['token'],'rid'=>$_GET['id']))->count(),
            M('Redpay_user')->field('id,nickname,headerimg,add_time,amount')->where(array('token'=>$_SESSION['token'],'rid'=>$_GET['id'])),
            array($this,'otherinfo')
        );
    }
    public function otherinfo($data){
        foreach($data as $k=>$val){
            /*$aUser = M('Wxuser')->where(array('token'=>$_SESSION['token']))->find();
            $aUsers = M('Wxusers')->where(array('uid'=>$aUser['id'],'openid'=>$val['openid']))->find();
            $data[$k]['openid'] = $aUsers['nickname'];*/
            $data[$k]['amount'] = $val['amount'];
        }
        return $data;
    }


    /*public function save_xx(){
        $this->add_save_xx(Edit);
    }
    public function delete_xx(){
        $this->del('Wxredpay');
    }*/
//其它信息结束

}

