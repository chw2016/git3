<?php
/**
 *  李铭  P2P系统
 * 2015.6.24
 **/
class Gta_fenxiaoAction extends Table1Action
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
        $aQuanxian = array('会计权限','出纳权限');
        $quanxian = $_SESSION['gta_cw'];
        if($quanxian){
            if(!in_array($quanxian,$aQuanxian)){
                $this->error2('您没有此应用的权限');
            }
        }
        $this->tpl = "tpl/User/default/helper/";
        //$this->pz=M("No_credit");
        $this->token = session('token');
        $this->wxuser_id = M('Wxuser')->where(array('token' => $this->token))->getField('id');

    }

    protected function setHeader()
    {
        return array(
            array(
                'name' => '用户管理',
                'url' => U('user_index', array('token' => $this->_sToken))
            ),
        /*    array(
                'name' => '分销商',
                'url' => U('index', array('token' => $this->_sToken))
            ),*/
            array(
                'name' => '会员等级',
                'url' => U('adduserlevel', array('token' => $this->_sToken))
            ),
            array(
                'name' => '提现管理',
                'url' => U('tixianinfo', array('token' => $this->_sToken))
            ),
        );
    }


    /**
     *  分销商列表
     **/
    public function index()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['img']=array('neq','');
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
                         'name' => '添加分销商',
                         'url' => U('add_product',array('token'=>$this->token))
                     ),




                ),
                'tips' => array(
                    '这里可以添加人寿险主险种!'
                ),
                'Table_Header' => array(
                    'ID', '二维码','姓名','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '删除',
                        'url' => U('del_product')
                    ),


                ),
            ),

            M('Gta_users')->where($aWhere)->count(),
            M('Gta_users')->field('id,img,name')->where($aWhere),
            array($this,'index1')

        );
        $this->UDisplay('show1');
    }
    public function index1($data){
        foreach($data as $k=>$v){
            $data[$k]['img']="<img src=".$v['img']."  width='120' />";

        }
        return $data;
    }

    //添加分销商
    public function add_product(){
        if(IS_POST){
          //  p($_POST);die;
            $a=M('Gta_users')->where(array('member_sn'=>$_POST['member_sn']))->getField('id');
            $code=new Code($this->token,'183'.$a);
            $data['img']=$code->getYJCode();
            if(M('Gta_users')->where(array('member_sn'=>$_POST['member_sn']))->save($data)){
                $this->success2('添加成功',U('index',array('token'=>$this->token)));
            }else{
                $this->error2('添加失败',U('index',array('token'=>$this->token)));
            }

        }else{
            $select=M('Gta_users')->field('member_sn')->where(array('token'=>$this->token))->select();
            $this->assign('select',$select);
            $this->display($this->tpl_dir.'fenxiao.html');
        }

    }

    //删除主险
    public function del_product(){
        if(M('Gta_users')->where(array('id'=>$_GET['id']))->save(array('img'=>''))){
            $this->success2('删除成功',U('index',array('token'=>$this->token)));
        }else{
            $this->error2('删除失败',U('index',array('token'=>$this->token)));

        }
    }
    /**
     *  下级成员
     **/
    public function two_product()
    {
        $aWhere['tp_gta_users.token'] = $this->_sToken;
        $aWhere['tp_gta_users.dopenid']=M('Gta_users')->where(array('id'=>$_GET['id']))->getField('openid');
       // p($aWhere);die;

     //   session('pid',$_GET['id']);
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
                        'name' => '返回',
                        'url' => U('index',array('token'=>$this->token))
                    ),
                ),
                'tips' => array(
                    '这里可以添加主险下面的副险种!'
                ),
                'Table_Header' => array(
                    'ID', '微信昵称', '微信图像', '关系绑定时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看',
                        'url' => U('two_product_product')
                    ),



                ),
            ),

            M('Gta_users')->join("join tp_wxusers on tp_wxusers.id=tp_gta_users.uid")->where($aWhere)->order('tp_gta_users.add_time desc')->count(),
            M('Gta_users')->join("join tp_wxusers on tp_wxusers.id=tp_gta_users.uid")->field('tp_gta_users.id,tp_wxusers.nickname,tp_wxusers.headimgurl,tp_gta_users.t_add_time')->where($aWhere)->order('tp_gta_users.add_time desc'),
            array($this,'two_product1')

        );
        $this->UDisplay('show1');
    }
    public function two_product1($data){
        foreach($data as $k=>$v){
            $data[$k]['headimgurl']="<img src=".$v['headimgurl']."  width='70' />";
        }
        $data[$k]['t_add_time']=date('Y-d-m H:i:s',$v['t_add_time']);
        return $data;
    }

//用户列表
    public function user_index()
    {
        $aWhere['tp_gta_users.token'] = $this->_sToken;
        if (IS_POST) {
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('user_index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                   array(
                        'name' => '导出excel表',
                       'type'=>'daochu',
                        'url' => U('excel_info',array('token'=>$this->token))
                    ),
                ),
                'tips' => array(
                    '用户列表!'
                ),
                'Table_Header' => array(
                    'ID','会员编号','真实姓名','手机号码','余额(元)','佣金(元)','会员等级','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '详情',
                        'url' => U('user_info',array('token'=>$this->token))
                    ),

                    array(
                        'name' => '邀请记录',
                        'url' => U('yaoqing',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '佣金记录',
                        'url' => U('yongjing',array('token'=>$this->token))
                    ),




                ),
                'search'=>array(
                  array('title'=>'会员编号','name'=>'li_member_sn'),
                    array('title'=>'真实姓名','name'=>'li_name'),
                    array('title'=>'会员等级','name'=>'eq_dengji','type'=>'select','many'=>array(
                        array('value'=>'金卡','name'=>'金卡'),
                        array('value'=>'白金','name'=>'白金'),
                        array('value'=>'钻石','name'=>'钻石'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'关注时间','name'=>'be_add_time','type'=>'between')
                )
            ),
            M('Gta_users')->where($aWhere)->order('add_time')->count(),
            M('Gta_users')->field('id,member_sn,name,phone,money,jifeng,dengji')->order('add_time desc')->order('img desc')->where($aWhere),
           /* M('Gta_users')->join("join tp_wxusers on tp_wxusers.id=tp_gta_users.uid")->where($aWhere)->order('tp_gta_users.add_time')->order('img desc')->count(),
            M('Gta_users')->join("join tp_wxusers on tp_wxusers.id=tp_gta_users.uid")->field('tp_gta_users.id,tp_gta_users.member_sn,tp_wxusers.nickname,tp_gta_users.name,tp_gta_users.phone,tp_gta_users.money,tp_gta_users.jifeng,tp_gta_users.dengji')->order('tp_gta_users.add_time')->order('img desc')->where($aWhere),*/
            array($this,'user_index1')

        );
        $this->UDisplay('show1');
    }
    public function user_index1($data){

        return $data;
    }
    //用户详情
    public function user_info(){
        $info=M('Gta_users')->find($_GET['id']);
        $this->assign('info',$info);
        $this->display($this->tpl_dir.'user_info.html');

    }
    //邀请记录
    public function yaoqing()
        {
            $openid=M('Gta_users')->where(array('id'=>$_GET['id']))->getField('openid');
            $list=$this->tojing($openid);
            $this->assign('list',$list);

          //  p($list);die;
            $this->display($this->tpl_dir.'yaoqing.html');


            //   $this->UDisplay('show1');

    }
    /*分销会员的统计*/
    public function tojing($openid){
        $list=array();
        $list_one=array();
        $list_two=array();
        $list_three=array();
        $list1=M('Gta_users')
            ->join("join tp_wxusers on tp_wxusers.id=tp_gta_users.uid")
            ->field('tp_gta_users.t_add_time,tp_gta_users.id,tp_gta_users.member_sn,tp_gta_users.openid,tp_wxusers.nickname,tp_wxusers.headimgurl,tp_gta_users.name,tp_gta_users.phone')
            ->where(array(
                'tp_gta_users.dopenid'=>$openid,
                'tp_gta_users.token'     =>$this->token,
                /* 'is_buy'    => 1*/
            ))->order("tp_gta_users.t_add_time desc")->select();
        //  p($list1);
        if($list1){//为真 走第二级
            foreach($list1 as $k=>$v){
                //    $list1[$k]['jibie']='第一级';
                $v['jibie']='第一级';
                array_push($list_one,$v);
                $list2=M('Gta_users')
                    ->join("join tp_wxusers on tp_wxusers.id=tp_gta_users.uid")
                    ->field('tp_gta_users.t_add_time,tp_gta_users.id,tp_gta_users.member_sn,tp_gta_users.openid,tp_wxusers.nickname,tp_wxusers.headimgurl,tp_gta_users.name,tp_gta_users.phone')
                    ->where(array(
                        'tp_gta_users.dopenid'=>$v['openid'],
                        'tp_gta_users.token'     =>$this->token,
                        /* 'is_buy'    => 1*/
                    ))->order("tp_gta_users.t_add_time desc")->select();
                //  p($list);
                if($list2){//为真  走第三级
                    foreach($list2 as $k=>$v){
                        //    $list2[$k]['jibie']='第二级';
                        $v['jibie']='第二级';
                        array_push($list_two,$v);
                        $list3=M('Gta_users')
                            ->join("join tp_wxusers on tp_wxusers.id=tp_gta_users.uid")
                            ->field('tp_gta_users.t_add_time,tp_gta_users.id,tp_gta_users.member_sn,tp_gta_users.openid,tp_wxusers.nickname,tp_wxusers.headimgurl,tp_gta_users.name,tp_gta_users.phone')
                            ->where(array(
                                'tp_gta_users.dopenid'=>$v['openid'],
                                'tp_gta_users.token'     =>$this->token,
                            ))->order("tp_gta_users.t_add_time desc")->select();
                        if($list3){
                            foreach($list3 as $k=>$v){
                                array_push($list_three,$v);
                            }
                        }


                    }
                }
            }
        }
        array_push($list,$list_one,$list_two,$list_three);
        return $list;
    }
    //佣金记录
    public function yongjing()
    {
        $aWhere['token'] = $this->_sToken;
        $aWhere['openid']=M('Gta_users')->where(array('id'=>$_GET['id']))->getField('openid');
        if (IS_POST) {
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('user_index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                          'name' => '返回用户列表',
                          'url' => U('user_index',array('token'=>$this->token))
                      ),




                ),
                'tips' => array(
                    '佣金记录!'
                ),
                'Table_Header' => array(
                    'ID','产生佣金的订单号码', '佣金(元)','客户姓名','产生时间',
                ),
                'List_Opt' => array(

                 /*   array(
                        'name' => '邀请记录',
                        'url' => U('yaoqing',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '佣金记录',
                        'url' => U('yongjing',array('token'=>$this->token))
                    ),*/




                ),
              /*  'search'=>array(
                    array('title'=>'会员编号','name'=>'li_member_sn'),
                    array('title'=>'真实姓名','name'=>'li_name'),
                    array('title'=>'会员等级','name'=>'eq_dengji','type'=>'select','many'=>array(
                        array('value'=>'金卡','name'=>'金卡'),
                        array('value'=>'白金','name'=>'白金'),
                        array('value'=>'钻石','name'=>'钻石'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'关注时间','name'=>'be_add_time','type'=>'between')
                )*/
            ),

            M('Edia_user_commission')->where($aWhere)->order('add_time desc')->count(),
            M('Edia_user_commission')->field('id,orderid,yj,g_name,add_time')->where($aWhere)->order('add_time desc')->where($aWhere),
            array($this,'yongjing1')

        );
        $this->UDisplay('show1');
    }
    public function yongjing1($data){
        foreach($data as $k=>$v){
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    //会员等级设置
    public function userlevel(){
        $userScoremodel = M('Usercenter_level');
        $data = $userScoremodel->where(array('token'=>$this->token))->select();
        $this->assign('hover',6);
        $this->assign('data',$data);
        $this->display($this->tpl_dir.'Usercenter_userlevel.html');
    }
    // 新增会员等级
    public function adduserlevel(){
        if(IS_POST){

        }else{
            $info=M('Gta_user_dengji')->where(array('token'=>$this->token))->find();
            $this->assign('info',$info);
            $this->assign('hover',6);
            $this->display($this->tpl_dir.'Usercenter_adduserlevel.html');
        }

    }
    //修改会员待级

    public function edituserlevel(){
        if(IS_POST){
            $data = array();
            $data['name'] = $_POST['name'];
            $data['level_type'] = $_POST['level_type'];
            $data['score'] = $_POST['score'];
            $where['id'] = $_POST['id'];
            $where['token'] = $this->token;
            if(M('Usercenter_level')->where($where)->save($data)){
                $this->success('操作成功', U(MODULE_NAME . '/userlevel'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/adduserlevel'),true);
            }
        }else{
            $where['id']=$this->_get('id','intval');
            $where['token']=$this->token;
            $data = D('Usercenter_level')->where($where)->find();
            $this->assign('hover',6);
            $this->assign('data',$data);
            $this->display($this->tpl_dir.'Usercenter_edituserlevel.html');
        }
    }

    public function insertuserlevel(){
        if(IS_POST){
            $_POST['token']=$this->token;
            if(M('Gta_user_dengji')->where(array('token'=>$this->token))->find()){//有
                if(M('Gta_user_dengji')->where(array('token'=>$this->token))->save(array('yongjing1'=>$_POST['yongjing1'],'yongjing2'=>$_POST['yongjing2']))!==false){
                    $this->success('操作成功', U(MODULE_NAME . '/adduserlevel'),true);
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/adduserlevel'),true);
                }

            }else{//无
                if(M('Gta_user_dengji')->add($_POST)){
                        $this->success('操作成功', U(MODULE_NAME . '/adduserlevel'),true);
                    }else{
                    $this->error('操作失败', U(MODULE_NAME . '/adduserlevel'),true);
                }
            }
            $userScoremodel = M('Usercenter_level');

        }
    }
//删除会员等级
    public function deluserlevel(){
        $where['id']=$this->_get('id','intval');
        $where['token']=$this->token;
        if(D('Usercenter_level')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/userlevel'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/userlevel'));
        }
    }
    /*
 * 提现管理
 * */
    public function tixianinfo(){
        $otiModel = M('Tixianjl');
        $oUserModel = M('Media_users');

        $where['token'] = $this->token;
        if($_SESSION['gta_cw'] == '出纳权限'){
            $where['status'] = 2;
        }elseif($_SESSION['gta_cw'] == '会计权限'){
            $where['status'] = array('in','1,3');
        }
        if($_GET['status']){
            $where['status'] = $_GET['status'];
        }
        $count = $otiModel->where($where)->order('add_time desc')->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $alist = $otiModel->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->order('add_time desc')->select();
        foreach($alist as $k=>$val){
            // $aUser = $oUserModel->where(array('token'=>$this->token,'openid'=>$val['openid']))->find();
            /**
             * 这里修改了，拿用户呢称
             */
            $usersModel = M('Wxusers');
            $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
            $userdata = $usersModel->where(array('uid'=>$wxuser['id'],'status'=>1,'openid'=>$val['openid']))->find();
            $alist[$k]['nickname'] = $userdata['nickname'];
        }
        $this->assign(array(
            'alist'=>$alist,
            'page'=>$show,
            'hover'=>7
        ));
        $this->display($this->tpl_dir.'tixianinfo.html');
    }
    /**
     * 提现查看详情
     */
    public function ticheck(){
        $otiModel = M('Tixianjl');
        $iTem = $otiModel->where(array('id'=>$_GET['id']))->find();
        // p($iTem);die;
        $this->assign('iTem',$iTem);
        //print_r($iTem);exit;
        if(IS_AJAX){
         //   p($_POST);die;
            $iTems =$otiModel->where(array('id'=>$_POST['id']))->find();
           /* if(session('gta_cw')!='出纳权限'){
                $this->error2('您没有权限');
            }*/

            if($iTems == false){
                $this->error2('非法操作');
            }
            $_POST['check_time'] = date('Y-m-d H:i:s');
            if($otiModel->where(array('id'=>$_POST['id']))->save($_POST)){
                $info=$otiModel->where(array('id'=>$_POST['id']))->find();
                if($_POST['status']==3){//打款
                    if($iTems['type']==1){//余额
                        M('Gta_users')->where(array('token'=>$this->token,'openid'=>$info['openid']))->setDec('money',$info['number']);
                    }
                    if($iTems['type']==2){//佣金
                        M('Gta_users')->where(array('token'=>$this->token,'openid'=>$info['openid']))->setDec('jifeng',$info['number']);
                    }
                }

                $this->success('操作成功！',U(MODULE_NAME.'/tixianinfo',array('token'=>session('token'))));
            }else{
                $this->error('操作失败！',U(MODULE_NAME.'/ticheck',array('token'=>session('token'),'id'=>$_POST['id'])));
            }
        }

           // p($iTem);die;
        $this->assign(array(
            'item'=>$iTem,
            'ExtraBtn' => array(
                array(
                    'url'  => U('Store_new/tixianinfo', array('token' => $this->token)),
                    'name' => '返回'
                )
            )
        ));

        $this->display($this->tpl_dir.'ticheck.html');
    }
    public function life_set(){
         //echo 88;die;

        $this->Edit('Gta_user_dengji',array(
            array('title'=>"上传图片规格",'type'=>"textarea1",'name'=>"content",'placeholder'=>'多个请用英文逗号隔开,例如:身份证,房产证'),
        ),U('index',array('token'=>$this->token)));
    }
    //导出提现记录
    public function excel(){
        if($_SESSION['gta_cw'] == '出纳权限'){
            $where['status'] = 2;
        }elseif($_SESSION['gta_cw'] == '会计权限'){
            $where['status'] = array('in','1,3');
        }
        $where['token'] = $this->token;
        $data=M('Tixianjl')->field('true_name,phone,number,bank_name,bank_card,type,status,add_time,check_time')->where($where)->select();
        foreach($data as $k=>$v){
            if($v['type']==1){
                $data[$k]['type']='余额';
            }
            if($v['type']==2){
                $data[$k]['type']='佣金';
            }
            if($v['status']==1){
                $data[$k]['status']='未审核';
            }
            if($v['status']==2){
                $data[$k]['status']='已核审打款中';
            }
            if($v['status']==3){
                $data[$k]['status']='已打款';
            }
            if($v['status']==4){
                $data[$k]['status']='审核未通过';
            }
        }

        Excel::arr2ExcelDownload($data,array('姓名','手机号码','提现金额','开户行','银行卡号','提现类别','状态','申请时间','审核时间'),'提现记录');

    }
    //导出用户
    public function excel_info(){
        $where=$this->search($_POST);
       // p($where);
        $data= M('Gta_users')
           ->join("join tp_wxusers on tp_wxusers.id=tp_gta_users.uid")
           ->field('tp_gta_users.member_sn,tp_gta_users.name,tp_gta_users.phone,tp_gta_users.cart,tp_gta_users.bank_name,tp_gta_users.bank_num,
           tp_gta_users.money,tp_gta_users.jifeng,tp_gta_users.add_time,tp_gta_users.dopenid
           ')->order('tp_gta_users.add_time')->order('img desc')->where($where)->select();
        foreach($data as $k=>$v){
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            $info1=M('Gta_users')->field('name,dopenid')->where(array('openid'=>$v['dopenid']))->find();
            $data[$k]['y1']=$info1['name'];
            unset($data[$k]['dopenid']);
            if($info1['dopenid']){
                $info2=M('Gta_users')->field('name,dopenid')->where(array('openid'=>$info1['dopenid']))->find();

                $data[$k]['y2']=$info2['name'];

            }
            if($info2['dopenid']){
                $info3=M('Gta_users')->field('name,dopenid')->where(array('openid'=>$info2['dopenid']))->find();
                $data[$k]['y3']=$info3['name'];
            }
           /* if(){

            }*/

        }
        Excel::arr2ExcelDownload($data,array('会员编号','姓名','手机号码','身份证信息','银行卡开户行','银行卡号码','余额','佣金','注册时间','上一级关系人','上二级关系人'),'会员表格');
       // Excel::arr2ExcelDownload($data,array('会员编号','姓名','手机号码','身份证信息','银行卡开户行','银行卡号码','余额','佣金','注册时间','上一级关系人','上二级关系人','上三级关系人'),'会员表格');
    }
}