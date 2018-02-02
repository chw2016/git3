<?php

/**
 * 国泰安  李铭   2015.6.25
 *
 */
class GtaAction extends Gta_commonAction{
    public $token;
    public $wecha_id = 'gh_aab60b4c5a39';
    public $product_model;
    public $product_cat_model;
    public $session_cart_name;
    public $dopenid;
    public $_sTplBaseDir = 'Wap/default/gta';
    public $wxusers_id;//wxusers 表的id


    public function _initialize() {
        if(in_array(ACTION_NAME,array('index','licai_index','p2p_index','user_index','loan_new'))){
            $this->autoShare = true;
        }
        parent::_initialize();
        //echo '321';exit;
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger")) {
            //	echo '此功能只能在微信浏览器中使用';exit;
        }
        $this->token = isset($_REQUEST['token']) ? htmlspecialchars($_REQUEST['token']) : session('token');
        $this->session_cart_name = 'session_cart_products_' . $this->token;
        $this->assign('token', $this->token);
        $this->wecha_id	= isset($_REQUEST['wecha_id']) ? htmlspecialchars($_REQUEST['wecha_id']) : '';
        $this->openid	= isset($_REQUEST['openid']) ? htmlspecialchars($_REQUEST['openid']) : '';
        if(!$this->openid){
            $this->openid	= isset($_REQUEST['wecha_id']) ? htmlspecialchars($_REQUEST['wecha_id']) : '';
        }
        $this->dopenid	= isset($_REQUEST['dopenid']) ? htmlspecialchars($_REQUEST['dopenid']) : '';
        $this->wecha_id = $this->openid;
        $this->assign('wecha_id', $this->wecha_id);
        $this->assign('openid', $this->openid);
        //print_r($this->product_cat_model);exit;
        $this->assign('staticFilePath', str_replace('./','/',THEME_PATH.'common/css/store'));
        /*
        * 引入微信js接口
        */
        Vendor('weixin.jssdk');
        $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
        $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
        $signPackage = $jssdk->GetSignPackage($this->token);
        $this->assign('signPackage',$signPackage);

        /**
         * 得wxusers id
         */

        $this->wxusers_id=M('Wxuser')->where(array('token'=>$this->token))->getField('id');
        $this->wxusers_id=M('Wxusers')->where(array('uid'=>$this->wxusers_id,'openid'=>$this->openid))->getField('id');
    }
    /*个人信息*/
    public function userinfo(){
        $info = M('Gta_users')->where(array(
            'token'=>$this->token,
            'openid'=>$this->openid
        ))->find();
        return $info;
    }

    /*判断是否已经注册过*/
    public function isUser(){
        $info = $this->userinfo();
	P($info);
        if(!$info['phone']){
            header("location:".U('user_my_info',array('token'=>$this->token,'openid'=>$this->openid)));exit;
        }
    }

    //首页
    public function index(){
        $info = $this->userinfo();
        $flash=M('Flash')->field('img,url')->where(array('token'=>$this->token))->select();
        $this->assign('flash_list',$flash);
        $token_img=M('Wxuser')->where(array('token'=>$this->token))->getField('headpicurl');
        $this->assign('token_img',$token_img);
        $is_auth=M('Wxuser')->where(array('token'=>$this->token))->getField('is_auth');
        $more_url=M('Gta_more')->where(array('token'=>$this->token))->getField('url');
        $this->assign('more_url',$more_url);
        $this->assign('is_auth',$is_auth);
        if($info['phone']){
            $this->assign('userinfo',$info);
        }

        //p($flash);
        $this->UDisplay("index");
    }
    //人寿险主险
    public function life_index(){

           /* $list=M('Gta_life')->field('id,pid,name,logo_pic')->where(array('token'=>$this->token,'pid'=>0))->select();
            $flash=M('Flash')->field('img,url')->where(array('token'=>$this->token))->select();
            $this->assign('flash',$flash);
            $this->assign('list',$list);*/
        $leibie=M('Gta_life_set')->where(array('token'=>$this->token))->getField('label');
        $leibie=explode(',',$leibie);
        $this->assign('leibie',$leibie);
        // p($leibie);
        $this->UDisplay();
    }
    //副险
    public function lifeinfo(){
        if(IS_POST){
            $where=array();
            if($_POST['label']){
                $where['leibie']=$this->_post('label');
            }
            if($_POST['qixian']){
                $where['qixian']=$this->_post('qixian');
            }
            if($_POST['baoe']){
                $where['baoe']=$this->_post('baoe');
            }
            if($_POST['fenhong']){
                $where['fenhong']=$this->_post('fenhong');
            }
            session('life_zhu',$where);
            echo json_encode(array('status'=>1));
        }else{
            $leibie=M('Gta_life_set')->where(array('token'=>$this->token))->getField('label');
            $leibie=explode(',',$leibie);
            $this->assign('leibie',$leibie);
            $this->UDisplay("Gta_life_indexfu");
        }
    }
    public function lifeinfo2(){
        if(IS_POST){

            //先找主险
            $zwhere=session('life_zhu');
            $zwhere['type']=1;
            $zwhere['token']=$this->token;
            $order='';
            if($_POST['paixu']){
                $order=$_POST['paixu'].' desc';
            }
            $list=M('Gta_life_product')->field('id ,title')->where($zwhere)->order($order)->select();
            $fwhere=array();
            if($_POST['leibie']){
                $fwhere['leibie']=$this->_post('leibie');
            }
            if($_POST['qixian']){
                $fwhere['qixian']=$this->_post('qixian');
            }
            if($_POST['baoe']){
                $fwhere['baoe']=$this->_post('baoe');
            }
            if($_POST['fenhong']){
                $fwhere['fenhong']=$this->_post('fenhong');
            }
            $fwhere['type']=2;
            $fwhere['token']=$this->token;

            foreach($list as $k=>$v){
                $fwhere['company_name']=M('Gta_life_product')->where(array('id'=>$v['id']))->getField('company_name');
                $list[$k]['fu']=M('Gta_life_product')->field('id,title')->where($fwhere)->select();
            }
            $this->assign('list',$list);
            $x = $this->fetch('./tpl/Wap/default/gta/life_indexfu2.html',$list);//内容放进来
            exit($x);
        }
    }


    //人寿险副险
    public function life_index2()
{
    if (IS_POST) {
        $label = explode(',', $_GET['label']);
        $list = array();
        $where['token'] = $this->token;
        $sql = "select id,title from tp_gta_life_product WHERE lid = '".session('life_lid')."' AND token ='" . $this->token . "' and";
        for ($i = 0; $i < count($label); $i++) {
            $sql .= " label like '%" . $label[$i] . "%' AND";
        }
        $sql = substr($sql, 0, -3);
        $list1 = M('Gta_life_product')->query($sql);
        echo json_encode($list1);

    } else {
        $flash = M('Flash')->field('img,url')->where(array('token' => $this->token))->select();
        $this->assign('flash', $flash);
        $label = M('Gta_life_set')->where(array('token' => $this->token))->getField('label');
        $label = explode(',', $label);
        $this->assign('label', $label);
        session('life_lid',$_GET['id']);
        //  $list=M('Gta_life')->field('id,name')->where(array('token'=>$this->token,'pid'=>$_GET['id']))->select();
        //   $this->assign('list',$list);
        $this->UDisplay();
    }
}
    //人寿险产品项目
    public function life_index3(){
        $order='';
        if(isset($_GET['sort'])){//排序过来的
            $order.=$_GET['sort'].' desc,';
        }
        $order.=" add_time desc";
        $list=M('Gta_life_product')->field('id,title')->where(array('token'=>$this->token,'lid'=>$_GET['id']))->order($order)->select();
        $this->assign('list',$list);
        $this->UDisplay();
    }
    //产品详情
    public function life_details(){
        //p($_GET);die;
        $info=M('Gta_life_product')->where(array('id'=>$_GET['id']))->find();
        $fid=explode(',',$_GET['fid']);
        $fulist=array();
        $fu_name='';
        foreach($fid as $k=>$v){
            $fulist[$k]=M('Gta_life_product')->field('title,content,range,qijian,way,nianxian')->where(array('id'=>$v))->find();
            $fu_name.=$fulist[$k]['title'].',';
        }
        $fu_name=trim($fu_name,',');
        session('life_fu_name',$fu_name);
        $this->assign('fulist',$fulist);

        $this->assign('info',$info);
        $this->UDisplay();
    }
    //人寿险录入个人资料
    public function input_life_data(){
        if(IS_POST){
            $this->isUser();
            //    p($_POST);die;
            if($_POST['sex']=='男'){
                $_POST['sex']=1;
            }else{
                $_POST['sex']=2;
            }
            session('life_sex',$_POST['sex']);
            session('life_year',$_POST['year']);
            session('life_name',$_POST['name']);
            session('life_age',$_POST['age']);
            session('life_phone',$_POST['phone']);
            /*if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){
               M('Gta_users')->where(array('id'=>$id))->save(array('name'=>$_POST['name'],'phone'=>$_POST['phone']));
            }else{
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['add_time']=time();
                M('Gta_users')->add($data);
            }*/
            header("location:".U('input_life_imgs',array('token'=>$this->token,'openid'=>$this->openid,'id'=>$_GET['id'])));
        }else{
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->field('name,phone')->find();
            $this->assign('info',$info);
           // session('life_money',$_GET['money']);
            session('life_id',$_GET['id']);
            $this->UDisplay();
        }

    }
    //人寿险录入图片资料
    public function input_life_imgs(){
     //   session('life_money',$_GET['money']);
        if(IS_POST){
            //p($_POST);
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['name']=session('life_name');
            $data['sex']=session('life_sex');
            $data['age']=session('life_age');
            $data['year']=session('life_year');
            $data['phone']=session('life_phone');
            $data['is_health']=$this->_post('is_health');
            $data['health_info']=$this->_post('health_info');
            $data['orderid']=getSn();
          //  $data['start_money']=session('life_money');
            $data['fu_name']=session('life_fu_name');
            $data['imgs']=$this->_post('imgs');
            $data['add_time']=time();
            $data['uid']=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');
            $data['lid']=session('life_id');
          //  echo $_GET['id'];die;
            $data['title']=M('Gta_life_product')->where(array('id'=>session('life_id')))->getField('title');
            if(M('Gta_life_order')->add($data)){
                session('life_fu_name',null);
                session('life_name',null);
                session('life_sex',null);
                session('life_age',null);
                session('life_year',null);
                session('life_phone',null);
                session('life_id',null);
                session('life_money',null);
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }

        }else{
            $ims=M('Gta_life_set')->where(array('token'=>$this->token))->getField('content');
            $ims=explode(',',$ims);
            $this->assign('ims',$ims);
            $this->UDisplay();

        }
    }
    //我要贷款首页
    public function loan_new(){
        $info=M('Gta_loan_set')->where(array('token'=>$this->token))->getField('info');
        $this->assign('info',$info);
        $this->UDisplay();
    }
    //我要贷款1步
    public function loan1(){
        if(IS_POST){
           // p($_POST);die;
            session('loan_c_money',$_POST['money']);
            session('loan_qi_xian',$_POST['day']);
            session('loan_type',$_POST['type']);
            header("location:".U('loan2',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            $type_list=M('Gta_loan_set')->where(array('token'=>$this->token))->getField('type');
            $type_list=explode(',',$type_list);
            $this->assign('type_list',$type_list);
            $this->UDisplay();

        }
    }
  //我要贷款第2步

    public function loan2(){
        if(IS_POST){
           /* if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){
                M('Gta_users')->where(array('id'=>$id))->save(array('name'=>$_POST['name'],'phone'=>$_POST['phone']));
            }else{
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['add_time']=time();
                M('Gta_users')->add($data);
            }*/
            session('loan_name',$_POST['name']);
            session('loan_phone',$_POST['phone']);

            header("location:".U('loan3',array('token'=>$this->token,'openid'=>$this->openid)));

        }else{
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->field('name,phone')->find();
            $this->assign('info',$info);
            $this->UDisplay();

        }
    }
    //我要贷款第3步
    public function loan3(){
        if(IS_POST){
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['name']=session('loan_name');
            $data['c_money']=session('loan_c_money');
            $data['qi_xian']=session('loan_qi_xian');
            $data['type']=session('loan_type');
            $data['imgs']=$this->_post('imgs');
            $data['orderid']=getSn();
            $data['phone']=session('loan_phone');
            $data['add_time']=time();
            $data['uid']=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');

            if(M('Gta_loan')->add($data)){
                session('loan_name',null);
                session('loan_type',null);
                session('loan_c_money',null);
                session('loan_qi_xian',null);
                session('loan_phone',null);
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else{
            $ims=M('Gta_loantype_set')->where(array('token'=>$this->token,'name'=>session('loan_type')))->getField('content');
           /* $ims=M('Gta_loan_set')->where(array('token'=>$this->token))->getField('content');*/
            $ims=explode(',',$ims);
            $this->assign('ims',$ims);
          //  p($ims);die;
            $url=U('loan3',array('token'=>$this->token,'openid'=>$this->openid));
            $this->assign('url',$url);
            $this->UDisplay();

        }
    }

    //我要理财首页
    public function licai_index(){
     //   $list=M('Licai')->where(array('token'=>$this->token,'tui_jian'=>1))->limit(2)->select();
       // p($list);
        $list=M('Licai')
            ->field('tp_licai.title,tp_licai.id,tp_licai.rate,tp_licai.time_limit,tp_licai.safe,tp_licai.money,sum(a.money) as omoney')
            ->join("left join tp_licai_order as a on tp_licai.id=a.lid")
            ->where(array('tp_licai.token'=>$this->token,'tp_licai.tui_jian'=>1))
            ->group('a.lid')
            ->order('tp_licai.tui_jian desc,tp_licai.add_time desc')
            ->limit(8)
            ->select();
        foreach($list as $k=>$v){
            if(!$v['omoney']){
                $list[$k]['bili']="0%";
            }else{
                $list[$k]['bili']=($v['omoney']/$v['money']*100).'%';
            }
        }
      //  p($list);die;

        $flash=M('Gta_imgs')->where(array('token'=>$this->token,'type'=>2))->select();
        $this->assign('flash',$flash);

        $this->assign('list',$list);
        $this->UDisplay();
    }
   //理财列表页
    public function licai_list(){
        $list=M('Licai')
            ->field('tp_licai.title,tp_licai.id,tp_licai.rate,tp_licai.time_limit,tp_licai.safe,tp_licai.money,sum(a.money) as omoney')
            ->join("left join tp_licai_order as a on tp_licai.id=a.lid")
            ->where(array('tp_licai.token'=>$this->token))
            ->group('a.lid')
            ->order('tp_licai.tui_jian desc,tp_licai.add_time desc')
            ->select();
        $this->assign('list',$list);
       // p($list);
        $this->UDisplay();
    }
    //理财详情页
    public function licai_info(){
        $info=M('Licai')->find($_GET['id']);
      // p($info);
        $omoney=M('Licai_order')->where(array('token'=>$this->token,'lid'=>$_GET['id']))->sum('money');
        session('licai_ymoney',($info['money']-$omoney));
        $this->assign('omoney',$omoney);
        $this->assign('info',$info);
        $this->UDisplay();
    }

    public function licai_info1(){
        if(IS_POST){
            $info=M('Gta_users')->field('phone,id,name')->where(array('uid'=>$this->wxusers_id))->find();
            //插入订单
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['uid']=$info['id'];
            $data['phone']=$info['phone'];
            $data['lid']=$_GET['id'];

            $data['add_time']=time();
            $product=M('Licai')->field('title,rate,time_limit')->where(array('id'=>$_GET['id']))->find();
            $data['title']=$product['title'];
            $data['rate']=$product['rate'];
            $data['time_limit']=$product['time_limit'];
            $data['name']=$info['name'];
            $data['orderid']=getSn();
            $data['money']=$this->_post('money');
            $data['status']=2;
            if($lid=M('Licai_order')->add($data)){
                //扣掉钱
                M('Gta_users')->where(array('uid'=>$this->wxusers_id))->setDec('money',$_POST['money']);
                //送佣金
               // $this->common('Licai_order',$lid,'Gta_licai_set',$_POST['money'],2);
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }

        }else{
            $omoney=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('money');
            $m_money=M('Licai')->where(array('lid'=>$_GET['id']))->getField('start_price');
            $this->assign('omoney',$omoney);
            $this->assign('m_money',$m_money);
            $this->UDisplay();
        }

    }
    //理财提交资料
    public function licai_data(){
        if(IS_POST){

            /*if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){
                M('Gta_users')->where(array('id'=>$id))->save(array('name'=>$_POST['name'],'phone'=>$_POST['phone']));
            }else{
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['add_time']=time();
                $id=M('Gta_users')->add($data);
            }*/
            $id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');
            //插入订单
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['uid']=$id;
            $data['phone']=$this->_post('phone');
            $data['lid']=$this->_post('lid');

            $data['add_time']=time();
            $data['title']=M('Licai')->where(array('id'=>$_POST['lid']))->getField('title');
            $data['name']=$this->_post('name');
            $data['orderid']=getSn();
            //  echo $_GET['id'];die;
            if(M('Licai_order')->add($data)){
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }

        }else{
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->field('name,phone')->find();
            $this->assign('info',$info);
            $this->UDisplay();

        }
    }

     //财产险首页
    public function money_index(){
        $list=M('Property_insurance')->field('id,name,logo_pic')->where(array('token'=>$this->token))->select();
        $flash=M('Flash')->field('img,url')->where(array('token'=>$this->token))->select();
        $this->assign('flash',$flash);
        $this->assign('list',$list);
        $this->UDisplay();
    }
    //家庭财产险分类
    public function money_fl(){
        //家庭财产险分类
        $list=M('Gta_yiwa2')->where(array('token'=>$this->token,'type'=>4))->select();
        $this->assign('list',$list);
        $this->UDisplay();

    }
    //家庭财产险列表
    public function home_list(){
        if(IS_POST){
            $order='add_time desc';
            if(isset($_GET['order'])) {
                $order = $_GET['order'];
            }
            if(isset($_GET['order2'])) {
                $order = $_GET['order2'];
            }

            $n=$_POST['n']*3;
            $list=M('Gta_home')->where(array('token'=>$this->token,'fid'=>$_GET['fid']))->order($order)->limit($n,3)->select();
            foreach($list as $k=>$v){
                $list[$k]['kuozhang']=json_decode($v['kuozhang'],true);
            }
            $this->assign('list', $list);
            $x = $this->fetch('./tpl/Wap/default/gta/money_list.html',$list);//内容放进来
            exit($x);
        }else{
            $order='add_time desc';
            if(isset($_GET['order'])){
                $order=$_GET['order'];
                if(strstr($_GET['order'],'desc')){
                    $this->assign('px1','price');
                }else{
                    $this->assign('px1','price desc');
                }
                $this->assign('ys1','1');
            }else{
                $this->assign('px1','price desc');
            }
            if(isset($_GET['order2'])){
                $order=$_GET['order2'];
                $this->assign('ys2','1');
            }
            $list=M('Gta_home')->where(array('token'=>$this->token,'fid'=>$_GET['fid']))->order($order)->limit(3)->select();
            foreach($list as $k=>$v){
                $list[$k]['kuozhang']=json_decode($v['kuozhang'],true);
            }

            $this->assign('list',$list);
            $this->UDisplay("Gta_money_choice3");
        }

    }
    //财产险投保选择
    public function money_choice(){
        if(IS_POST){
          //  p($_POST);die;
                $this->isUser();
                session('money_money',$_POST['money']);
                session('money_pid',$_POST['pid']);
                session('money_pname',$_POST['name']);
                session('money_info',null);
                session('money_info',$_POST['info']);
                session('money_total_money',$_POST['total_money']);
                header("location:".U('money_data',array('token'=>$this->token,'openid'=>$this->openid,'type'=>2)));

        }else{
            if(M('Property_insurance')->where(array('id'=>$_GET['id'],'name'=>array('like','%家财险%'),'name'=>array('like','%家庭财产险%')))->find()){
               // echo 88;die;
                //跳家财险分类
                header("location:".U('money_fl',array('token'=>$this->token,'openid'=>$this->openid)));
                 die;

            }else{
                session('money_apid',$_GET['id']);
                session('money_pid_name',null);
                $list=M('Property_insurance_product')->where(array('pid'=>$_GET['id'],'token'=>$this->token))->select();
                $this->assign('list',$list);
                $this->UDisplay();
            }

        }

    }


    //财产险填写资料
    public function money_data(){
        if(IS_POST){
           // p($_POST);
           /* if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){
                M('Gta_users')->where(array('id'=>$id))->save(array('name'=>$_POST['name'],'phone'=>$_POST['phone']));
            }else{
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['add_time']=time();
                $id=M('Gta_users')->add($data);
            }*/
            $this->isUser();
            session('money_name',$_POST['name']);
            session('money_phone',$_POST['phone']);
            session('money_type',$_POST['type']);
            header("location:".U('money_imgs',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            if(isset($_GET['name'])){
                session('money_pid_name',$_GET['name']);
                session('money_apid',$_GET['id']);
                session('money_total_money',null);
            }
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->field('name,phone')->find();
            $this->assign('info',$info);
            $this->UDisplay();

        }

    }
    //财产险上传图片资料
    public function money_imgs(){
        if(IS_POST){
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['name']=session('money_name');
            $data['imgs']=$this->_post('imgs');
            $data['orderid']=getSn();
            $data['add_time']=time();
            //二维json数组,pid,name,price
            if(session('money_type')==2){
                $data['info']=session('money_info');
                $money_money=explode(',',session('money_money'));
                $money_pid=explode(',',session('money_pid'));
                $money_pname=explode(',',session('money_pname'));
                $arr=array();
                for($i=0;$i<count($money_money);$i++){
                    $arr[$i]['pid']=$money_pid[$i];
                    $arr[$i]['name']=$money_pname[$i];
                    $arr[$i]['money']=$money_money[$i];
                }
                $data['products']=json_encode($arr);
            }
            if(session('?money_pid_name')){//家财险
                $data['pid_name']=session('money_pid_name');
            }else{
                $data['pid_name']=M('Property_insurance')->where(array('id'=>session('money_apid')))->getField('name');
                //$data['info']=session('money_info');
            }
            if(session('?money_total_money')){//总金额
                $data['total_money']=session('money_total_money');
            }

            $data['pid']=session('money_apid');
            $data['uid']=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');
            $data['phone']=session('money_phone');
            if(M('Property_insurance_order')->add($data)){
                session('money_pid_name',null);
                session('money_total_money',null);
                session('money_name',null);
                session('money_money',null);
                session('money_pid',null);
                session('money_apid',null);
                session('money_pname',null);
                session('money_phone',null);
                session('money_info',null);
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else{
            if(session('?money_pid_name')){//家财险
                $ims=M('Gta_money_set')->where(array('token'=>$this->token))->getField('content2');
                $ims=explode(',',$ims);
            }else{//企业财产险
                $ims=M('Gta_money_set')->where(array('token'=>$this->token))->getField('content');
                $ims=explode(',',$ims);
            }
            $this->assign('ims',$ims);
            $url=U('money_imgs',array('token'=>$this->token,'openid'=>$this->openid));
            $this->assign('url',$url);
            $this->UDisplay("Gta_loan3");
        }

    }
    //车险首页
    public function cart_index(){
        $flash=M('Flash')->field('img')->where(array('token'=>$this->token))->select();
        $this->assign('flash',$flash);
        $this->UDisplay();

    }
    //车险基本险
    public function cart_one(){
        if(IS_POST){
            //p($_POST);
            $_POST['name']=explode(',',$_POST['name']);
            session('cart_name',$_POST['name']);
         //   p(session('cart_name'));die;
            header("location:".U('cart_two',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            $list=M('Gta_cart')->where(array('token'=>$this->token,'type'=>0))->select();
            $this->assign('list',$list);
            $this->UDisplay();
        }


    }
    //车险副本险
    public function cart_two(){
        if(IS_POST){//去住基本险
          //  p($_POST);
            $this->isUser();
            $_POST['name_two']=explode(',',$_POST['name_two']);
            session('cart_name_two',$_POST['name_two']);
            session('cart_other',$_POST['other']);
            header("location:".U('cart_one',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            $list=M('Gta_cart')->where(array('token'=>$this->token,'type'=>0))->select();
            foreach($list as $k=>$v){
                $list[$k]['content']=explode(',',$v['content']);
            }
            $this->assign('list',$list);
            $list1=M('Gta_cart')->where(array('token'=>$this->token,'type'=>1))->select();
            foreach($list1 as $k=>$v){
                $list1[$k]['content']=explode(',',$v['content']);
            }
            $this->assign('list1',$list1);
            $this->assign('name_one',session('cart_name'));
            $this->UDisplay();
        }
    }
    //车险选择公司
    public function cart_company(){
        if(IS_POST){

            session('cart_company',$_POST['company']);
            header("location:".U('cart_data',array('token'=>$this->token,'openid'=>$this->openid)));

        }else{
          //  p($_GET);die;
         //   $_GET['twoname']=explode(',',$_GET['twoname']);
            //session('cart_name_two',$_GET['twoname']);
            session('cart_money',$_GET['money']);
            session('cart_title',$_GET['title']);
            session('cart_abatement',$_GET['abatement']);
            session('cart_type',$_GET['type']);
            $list=M('Gta_cart_company')->where(array('token'=>$this->token))->select();
            $this->assign('list',$list);
            $this->UDisplay();

        }
    }
    //提交资料
    public function cart_data(){
        if(IS_POST){
           /* if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){
                M('Gta_users')->where(array('id'=>$id))->save(array('name'=>$_POST['name'],'phone'=>$_POST['phone']));
            }else{
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['add_time']=time();
                M('Gta_users')->add($data);
            }*/
            session('cart_true_name',$_POST['name']);
            session('cart_phone',$_POST['phone']);

            header("location:".U('cart_imgs',array('token'=>$this->token,'openid'=>$this->openid)));

        }else{
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->field('name,phone')->find();
            $this->assign('info',$info);
            $this->UDisplay();

        }
    }
        //车险上传图片资料
    public function cart_imgs(){
        if(IS_POST){
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['name']=session('cart_true_name');
            $data['phone']=session('cart_phone');
            $data['imgs']=$this->_post('imgs');
            $data['orderid']=getSn();
            $data['add_time']=time();
            if(session('cart_type')=='che'){
                $data['type']=0;
            }else{
                $data['type']=1;
            }
           // $data['one_name']=implode(',',session('cart_name'));
          //  $data['two_name']=implode(',',session('cart_name_two'));
            $cart_money=explode(',',session('cart_money'));
            $cart_title=explode(',',session('cart_title'));
            $cart_abatement=explode(',',session('cart_abatement'));
            $order_info=array();
            for($i=0;$i<count($cart_money);$i++){
                if($cart_money[$i]!='不投'&&$cart_money[$i]!='请选择投保'){
                    $order_info['money'][]=$cart_money[$i];
                    $order_info['title'][]=$cart_title[$i];
                    $order_info['abatement'][]=$cart_abatement[$i];

                }
            }
            $data['oder_info']=json_encode($order_info);
            $data['companys']=session('cart_company');
            $data['other']=session('cart_other');
            $data['uid']=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');
            if(M('Gta_cart_order')->add($data)){
                session('cart_type',null);
                session('cart_name',null);
                session('cart_phone',null);
                session('cart_true_name',null);
                session('cart_money',null);
                session('cart_title',null);
                session('cart_abatement',null);
                session('cart_company',null);
                session('cart_other',null);
              //  echo json_encode(array('status'=>1));
            }else{
             //   echo json_encode(array('status'=>0));
            }

        }else{
            $ims=M('Gta_cart_set')->where(array('token'=>$this->token))->getField('content');
            $ims=explode(',',$ims);
            $this->assign('ims',$ims);
            $url=U('cart_imgs',array('token'=>$this->token,'openid'=>$this->openid));
            $this->assign('url',$url);
            $this->UDisplay("Gta_loan3");
        }


    }
    //意外险首页
    public function yiwai_index(){
        $flash=M('Flash')->field('img,url')->where(array('token'=>$this->token))->select();
        $list=M('Gta_yiwai')->where(array('token'=>$this->token))->select();
        $this->assign('flash',$flash);
        $this->assign('list',$list);
        $this->UDisplay();

    }
    //意外险项目选择
    public function yiwai_item(){
        if(IS_POST){
          //  p($_POST);
            $this->isUser();
            session('yiwai_people_num',$_POST['people_num']);
            session('yiwai_zhiye',$_POST['zhiye']);
            session('yiwai_money',$_POST['money']);
            session('yiwai_pname',$_POST['pname']);
            header("location:".U('yiwai_data',array('token'=>$this->token,'openid'=>$this->openid)));

        }else{
            if($_GET['name']=='个人意外险'){
                //echo 1;die;
                session('yiwai_type',null);
                $list=M('Gta_yiwa2')->where(array('token'=>$this->token,'type'=>1))->select();
                $this->assign('list',$list);
                $this->UDisplay("Gta_yiwai2");
                die;
            }
            if($_GET['name']=='旅游意外险'){
                //echo 1;die;
                session('yiwai_type',null);
                $list=M('Gta_yiwa2')->where(array('token'=>$this->token,'type'=>2))->select();
                $this->assign('list',$list);
                $this->UDisplay("Gta_yiwai2");
                die;
            }

           /* if(M('Gta_yiwai')->where(array('id'=>$_GET['id']))->getField('content')){
                session('yiwai_yid',$_GET['id']);
                session('yiwai_type',1);
                $info=M('Gta_yiwai')->field('id,content,name')->find($_GET['id']);
                $this->assign('info',$info);
                $this->UDisplay("Gta_yiwai_item2");
            }else{*/
            session('yiwai_yid',$_GET['id']);
            session('yiwai_type',2);
            $list=M('Gta_yiwai_item')->where(array('yid'=>$_GET['id']))->select();
            $zhiyes=M('Gta_yiwai_set')->where(array('token'=>$this->token))->getField('zhiye');
            $zhiyes=explode(',',$zhiyes);
            $this->assign('zhiyes',$zhiyes);
            $this->assign('list',$list);
            $this->UDisplay();
          //  }

        }

    }

    //新增
    public function yiwai_list(){
        if(IS_POST){
            $order='add_time desc';
            if(isset($_GET['order'])) {
                $order = $_GET['order'];
            }
            if(isset($_GET['order2'])) {
                $order = $_GET['order2'];
            }
            $n=$_POST['n']*10;
            $list=M('Gta_ywproduct')->where(array('token'=>$this->token,'yid'=>$_GET['id']))->order($order)->limit($n,10)->select();
            foreach($list as $k=>$v){
                $list[$k]['kuozhang']=json_decode($v['kuozhang'],true);
            }
            $this->assign('list', $list);
            $x = $this->fetch('./tpl/Wap/default/gta/yiwai_list.html',$list);//内容放进来
            exit($x);

        }else{
            $order='add_time desc';
            if(isset($_GET['order'])){
                $order=$_GET['order'];
                if(strstr($_GET['order'],'desc')){
                    $this->assign('px1','price');
                }else{
                    $this->assign('px1','price desc');
                }
                $this->assign('ys1','1');
            }else{
                $this->assign('px1','price desc');
            }
            if(isset($_GET['order2'])){
                $order=$_GET['order2'];
                $this->assign('ys2','1');
            }
            $list=M('Gta_ywproduct')->where(array('token'=>$this->token,'yid'=>$_GET['id']))->order($order)->limit(10)->select();
            foreach($list as $k=>$v){
                $list[$k]['kuozhang']=json_decode($v['kuozhang'],true);
            }
            $this->assign('list',$list);
            $this->UDisplay();
        }

    }
    //意外险提交信息
    public function yiwai_data(){
        if(IS_POST){
            /*if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){
                M('Gta_users')->where(array('id'=>$id))->save(array('name'=>$_POST['name'],'phone'=>$_POST['phone']));
            }else{
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['add_time']=time();
                M('Gta_users')->add($data);
            }*/
            $this->isUser();
            session('yiwai_name',$_POST['name']);
            session('yiwai_phone',$_POST['phone']);
            header("location:".U('yiwai_img',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            if(isset($_GET['id'])){
                session('yiwai_yid',$_GET['id']);
            }
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->field('name,phone')->find();
            $this->assign('info',$info);
            $this->UDisplay();

        }
    }
    //意外险上传图片
    public function yiwai_img(){
        if(IS_POST){
            $data['token']=$this->token;
            $data['openid']=$this->openid;

            $data['name']=session('yiwai_name');
            $data['phone']=session('yiwai_phone');
            $data['imgs']=$this->_post('imgs');
            $data['orderid']=getSn();
            $data['add_time']=time();

            $data['uid']=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');


            if(session('yiwai_type')==2){//正常
                $yiwai_money=explode(',',session('yiwai_money'));
                $yiwai_pname=explode(',',session('yiwai_pname'));
                for($i=0;$i<count($yiwai_money);$i++){
                    $data['items'][$i]['name']=$yiwai_pname[$i];
                    $data['items'][$i]['money']=$yiwai_money[$i];
                }
                $data['items']=json_encode($data['items']);
                $data['title']=M('Gta_yiwai')->where(array('id'=>session('yiwai_yid')))->getField('name');
                $data['yid']=session('yiwai_yid');
                $data['zhiye']=session('yiwai_zhiye')?session('yiwai_zhiye'):'';
                $data['people_num']=session('yiwai_people_num')?session('yiwai_people_num'):'';
            }else{//这里是个人和旅游意外险走的
                $data['yid']=session('yiwai_yid');
                //$data['title']=
                $typek=M('Gta_ywproduct')->join("join tp_gta_yiwa2 as a on a.id=tp_gta_ywproduct.yid")->field('tp_gta_ywproduct.name,a.type')->where(array('tp_gta_ywproduct.id'=>session('yiwai_yid')))->find();
                if($typek['type']==1){
                    $data['title']="个人意外险";
                }
                if($typek['type']==2){
                    $data['title']="旅游意外险";
                }
                $data['title2']=$typek['name'];
            }
          // p($data);die;

            if(M('Gta_yiwai_order')->add($data)){
                session('yiwai_name',null);
                session('yiwai_phone',null);
                session('yiwai_yid',null);
                session('yiwai_zhiye',null);
                session('yiwai_people_num',null);
                session('yiwai_money',null);
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }

        }else{
            if(session('yiwai_type')==2){//这是正常的
                $ims=M('Gta_yiwai_set')->where(array('token'=>$this->token))->getField('content');
            }else{
                $typek=M('Gta_ywproduct')->join("join tp_gta_yiwa2 as a on a.id=tp_gta_ywproduct.yid")->field('tp_gta_ywproduct.name,a.type')->where(array('tp_gta_ywproduct.id'=>session('yiwai_yid')))->find();
                if($typek['type']==1){//个人意外
                    $ims=M('Gta_yiwai_set')->where(array('token'=>$this->token))->getField('content2');
                }
                if($typek['type']==2){//旅游意外险
                    $ims=M('Gta_yiwai_set')->where(array('token'=>$this->token))->getField('content3');
                }

            }
            $ims=explode(',',$ims);
            $this->assign('ims',$ims);
            $url=U('yiwai_img',array('token'=>$this->token,'openid'=>$this->openid));
            $this->assign('url',$url);
            $this->UDisplay("Gta_loan3");
        }


    }
    //物流险首页
   /* public function logistics_index(){
        $info=M('Gta_logistics_set')->where(array('token'=>$this->token))->getField('info');
        $this->assign('info',$info);
        $this->UDisplay();

    }*/
    //责任险分类
    public function zeren_list(){
        $list=M('Gta_yiwa2')->where(array('token'=>$this->token,'type'=>3))->select();
        $this->assign('list',$list);
        $this->UDisplay();
    }
    public function zeren_info(){
        if(IS_POST){
            $this->isUser();
            session('zeren_money',$_POST['money']);
            session('zeren_pid',$_POST['pid']);
            session('zeren_pname',$_POST['name']);
            session('zeren_info',null);
            session('zeren_info',$_POST['info']);
         //   p($_POST);die;
            header("location:".U('logistics_data',array('token'=>$this->token,'openid'=>$this->openid,'name'=>$_GET['name'])));
        }else{
            $list=M('Gta_zeren')->where(array('token'=>$this->token,'pid'=>$_GET['id']))->select();
            $this->assign('list',$list);
            $this->UDisplay();
        }

    }
    //责任险提交资料
    public function logistics_data(){
        if(IS_POST){
           /* if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){
                M('Gta_users')->where(array('id'=>$id))->save(array('name'=>$_POST['name'],'phone'=>$_POST['phone']));
            }else{
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['add_time']=time();
                M('Gta_users')->add($data);
            }*/
            session('logistics_name',$_POST['name']);
            session('logistics_phone',$_POST['phone']);

            header("location:".U('logistics_imgs',array('token'=>$this->token,'openid'=>$this->openid,'name'=>$_GET['name'])));

        }else{
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->field('name,phone')->find();
            $this->assign('info',$info);
            $this->UDisplay();
        }
    }
    //物流险上传图片
    public function logistics_imgs(){
        if(IS_POST){
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['name']=session('logistics_name');
            $data['imgs']=$this->_post('imgs');
            $data['orderid']=getSn();
            $data['phone']=session('logistics_phone');
            $data['add_time']=time();
            $data['uid']=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');
            $data['content']=session('zeren_info');
            $money_money=explode(',',session('zeren_money'));
            $money_pid=explode(',',session('zeren_pid'));
            $money_pname=explode(',',session('zeren_pname'));
            $arr=array();
            for($i=0;$i<count($money_money);$i++){
                $arr[$i]['pid']=$money_pid[$i];
                $arr[$i]['name']=$money_pname[$i];
                $arr[$i]['money']=$money_money[$i];
            }
            $data['info']=json_encode($arr);
            $data['title']=$_GET['name'];
            if(M('Gta_logistics_order')->add($data)){
                session('logistics_name',null);
                session('zeren_info',null);
                session('logistics_name',null);
                session('zeren_money',null);
                session('zeren_pid',null);
                session('zeren_pname',null);
                session('logistics_phone',null);
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else{
            $ims=M('Gta_logistics_set')->where(array('token'=>$this->token))->getField('content');
            $ims=explode(',',$ims);
            $this->assign('ims',$ims);
            $url=U('logistics_imgs',array('token'=>$this->token,'openid'=>$this->openid,'name'=>$_GET['name']));
            $this->assign('url',$url);
            $this->UDisplay("Gta_loan3");
        }

    }

    //P2P首页
    public function p2p_index(){
        $flash=M('Gta_imgs')->where(array('token'=>$this->token,'type'=>1))->select();
        $this->assign('flash',$flash);
        $list=M('P2p_touzi')
            ->field('tp_p2p_touzi.start_price,tp_p2p_touzi.title,tp_p2p_touzi.id,tp_p2p_touzi.rate,tp_p2p_touzi.time_limit,tp_p2p_touzi.safe,tp_p2p_touzi.money,sum(a.money) as omoney')
            ->join("left join tp_touzi_order as a on tp_p2p_touzi.id=a.pid")
            ->where(array('tp_p2p_touzi.token'=>$this->token,'tp_p2p_touzi.tui_jian'=>1))
            ->group('tp_p2p_touzi.id')
            ->limit(8)
            ->order('tp_p2p_touzi.add_time desc')
            ->select();
     //   $list=M('P2p_touzi')->where(array('token'=>$this->token,'tui_jian'=>1))->limit(8)->select();
    //    p($list);die;
        foreach($list as $k=>$v){
            if(!$v['omoney']){
                $list[$k]['bili']="0%";
            }else{
                $list[$k]['bili']=($v['omoney']/$v['money']*100).'%';
            }
        }
        $this->assign('list',$list);
        $this->UDisplay();
    }
    //p2p列表页
    public function p2p_list(){
        $list=M('P2p_touzi')
            ->field('tp_p2p_touzi.start_price,tp_p2p_touzi.title,tp_p2p_touzi.id,tp_p2p_touzi.rate,tp_p2p_touzi.time_limit,tp_p2p_touzi.safe,tp_p2p_touzi.money,sum(a.money) as omoney')
            ->join("left join tp_touzi_order as a on tp_p2p_touzi.id=a.pid")
            ->where(array('tp_p2p_touzi.token'=>$this->token))
            ->group('tp_p2p_touzi.id')
            ->order('tp_p2p_touzi.tui_jian desc,tp_p2p_touzi.add_time desc')
            ->select();
        $this->assign('list',$list);
        $this->UDisplay();
    }
    //p2p详情页
    public function p2p_info(){
        $info=M('P2p_touzi')->find($_GET['id']);
        $omoney=M('Touzi_order')->where(array('token'=>$this->token,'pid'=>$_GET['id']))->sum('money');
        $this->assign('info',$info);
        session('p2p_ymoney',($info['money']-$omoney));
        $this->assign('omoney',$omoney);
        $list=M('Touzi_order')->field('name,money,add_time')->where(array('pid'=>$_GET['id'],'token'=>$this->token,'status'=>2))->order('add_time desc')->select();
        $this->assign('list',$list);
        $this->UDisplay();
    }
    //立即投资
    public function p2p_touzi1(){
        $omoney=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('money');
        $m_money=M('P2p_touzi')->where(array('id'=>$_GET['id']))->getField('start_price');
        $this->assign('omoney',$omoney);
        $this->assign('m_money',$m_money);
        $this->UDisplay();
    }
    //p2p投资
    public function p2p_touzi(){
        if(IS_POST){
          // p($_REQUEST);die;
            /*if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){
                M('Gta_users')->where(array('id'=>$id))->save(array('name'=>$_POST['name'],'phone'=>$_POST['phone']));
            }else{
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['add_time']=time();
                $id=M('Gta_users')->add($data);
            }*/
            $info=M('Gta_users')->field('id,name,phone')->where(array('uid'=>$this->wxusers_id))->find();
            $info_product=M("P2p_touzi")->where(array('id'=>$_GET['id']))->field('time_limit,title,rate,huankuan')->find();
           // p($info_product);die;
            $data['token']=$this->token;
            $data['openid']=$this->openid;
           // $data['name']=$this->_post('name');
        //    $data['phone']=$this->_post('phone');
            $data['name']=$info['name'];
            $data['phone']=$info['phone'];

          //  $data['uid']=$id;
            $data['uid']=$info['id'];
            $data['pid']=$this->_get('id');
            $data['title']=$info_product['title'];
            $data['old_money']=$this->_post('money');
            $data['orderid']=getSn();
            $data['add_time']=time();
            $data['money']=$this->_post('money');
            $data['status']=2;
            $data['js_time']=strtotime(date('Y-m-d',time()));
            $data['rate']=$info_product['rate'];
            $data['time_limit']=$info_product['time_limit'];
            $data['js_type']=$info_product['huankuan'];
            if($tid=M('Touzi_order')->add($data)){
                //扣掉钱
                M('Gta_users')->where(array('uid'=>$this->wxusers_id))->setDec('money',$_POST['money']);
                //送佣金
               // $this->common('Touzi_order',$tid,'Gta_p2p_set',$_POST['money'],4);
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else{
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->field('name,phone')->find();
            $this->assign('info',$info);
            $this->UDisplay();

        }
    }
    //个人中心 首页
    public function user_index(){
        $userinfo = $this->userinfo();
        if($userinfo['phone']){
            $this->assign('userinfo',$userinfo);
        }
        $uid=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');
        $info1=(array)M('Gta_users')->field('id,jifeng,money,dengji')->where(array('uid'=>$this->wxusers_id))->find();
        $info=(array)M('Wxusers')->field('headimgurl,nickname')->find($this->wxusers_id);
        $info=array_merge($info,$info1);
        $total=M('Edia_user_commission') ->where(array('token'=>$this->token,'openid'=>$this->openid))->sum('yj');
     //   $this->assign('total',$total);
        $num1=M('Gta_loan')->where(array('uid'=>$uid))->count();
        $num2=M('Licai_order')->where(array('uid'=>$uid))->count();
        $num3=M('Gta_life_order')->where(array('uid'=>$uid))->count();
        $num4=M('Touzi_order')->where(array('uid'=>$uid))->count();
        $num5=M('Property_insurance_order')->where(array('uid'=>$uid))->count();
        $num6=M('Gta_cart_order')->where(array('uid'=>$uid))->count();
        $num7=M('Gta_yiwai_order')->where(array('uid'=>$uid))->count();
        $num8=M('Gta_logistics_order')->where(array('uid'=>$uid))->count();
        $num=$num1+$num2+$num3+$num4+$num5+$num6+$num7+$num8;
        //我的客户
        $kefu_num=$this->tojing($this->openid);
        $kefu_num=count($kefu_num[0])+count($kefu_num[1])+count($kefu_num[2]);
        $this->assign('kefu_num',$kefu_num);
        $this->assign('info',$info);
        //p($info);die;
        $this->assign('num',$num);
        //算投资金额
       // $touzi_list=M('Touzi_order')->field('money,rate,time_limit,add_time')->where(array('uid'=>$uid,'token'=>$this->token,'status'=>2))->select();
        $t_money=0;
        $s_money=0;
       /* foreach($touzi_list as $v){
            $t_money=$t_money+$v['money'];
            $s_money=$s_money+round($v['money']*$v['rate']/365*min((time()-$v['add_time'])/3600/24,30*$v['time_limit']),2);
        }*/
        $licai_list=M('Licai_order')->field('money,rate,time_limit,add_time')->where(array('uid'=>$uid,'token'=>$this->token,'status'=>2))->select();
        foreach($licai_list as $v){
            $t_money=$t_money+$v['money'];
            $s_money=$s_money+round($v['money']*$v['rate']/365*min((time()-$v['add_time'])/3600/24,30*$v['time_limit']),2);
        }
        //判断用户有没有注册过phone
        $phone=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('phone');
        $this->assign('phone',$phone);
        $this->assign('t_money',$t_money);
        $this->assign('s_money',$s_money);
        $this->UDisplay();
    }
    //我的订单
    public function user_orderlist(){
        $uid=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id');
        $my_order=array();

      //  p($uid);die;
        $my_order[1]=M('Gta_loan')->where(array('uid'=>$uid))->count();
        if(isset($_GET['type'])&&$_GET['type']==1){
            if(IS_AJAX){
                $n=$_POST['n']*5;
                $list=(array)M('Gta_loan')->where(array('uid'=>$uid))->field('id,orderid,status,add_time')->order('add_time desc')->limit($n,5)->select();//贷款
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='贷款';
                    $list[$v['add_time']]['type']=1;
                    unset($list[$k]);
                }
                $this->assign('list',$list);
                if($list){
                    $x = $this->fetch('./tpl/Wap/default/gta/Gta_user_orderlist1.html', $list);//内容放进来
                    exit($x);
                }else{
                    exit();
                }
            }else{
                $list=(array)M('Gta_loan')->where(array('uid'=>$uid))->field('id,orderid,status,add_time')->order('add_time desc')->limit(0,5)->select();//贷款
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='贷款';
                    $list[$v['add_time']]['type']=1;
                    unset($list[$k]);
                }
            }


        }
        $my_order[2]=M('Licai_order')->where(array('uid'=>$uid))->count();
        //p($my_order);die;
        if(isset($_GET['type'])&&$_GET['type']==2){
            if(IS_AJAX){
                $n=$_POST['n']*3;
                $list=(array)M('Licai_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time,title')->order('add_time desc')->limit($n,3)->select();//理财
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='理财';
                    $list[$v['add_time']]['type']=2;
                    unset($list[$k]);
                }
                $this->assign('list',$list);
                if($list){
                    $x = $this->fetch('./tpl/Wap/default/gta/Gta_user_orderlist1.html', $list);//内容放进来
                    exit($x);
                }else{
                    exit();
                }

            }else{
                $list=(array)M('Licai_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time,title')->order('add_time desc')->limit(0,3)->select();//理财
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='理财';
                    $list[$v['add_time']]['type']=2;
                    unset($list[$k]);
                }
            }

        }

       // p($list);die;
        $my_order[3]=M('Gta_life_order')->where(array('uid'=>$uid))->count();
        if(isset($_GET['type'])&&$_GET['type']==3){
            if(IS_AJAX){
                $n=$_POST['n']*5;
                $list=(array)M('Gta_life_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time,title')->order('add_time desc')->limit($n,5)->select();//
                foreach($list as $k=>$v) {
                    $list[$v['add_time']] = $v;
                    $list[$v['add_time']]['typename'] = '人寿保险';
                    $list[$v['add_time']]['type'] = 3;
                    unset($list[$k]);
                }
                $this->assign('list',$list);
                if($list){
                    $x = $this->fetch('./tpl/Wap/default/gta/Gta_user_orderlist1.html', $list);//内容放进来
                    exit($x);
                }else{
                    exit();
                }
            }else{
                $list=(array)M('Gta_life_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time,title')->order('add_time desc')->limit(0,5)->select();//
                foreach($list as $k=>$v) {
                    $list[$v['add_time']] = $v;
                    $list[$v['add_time']]['typename'] = '人寿保险';
                    $list[$v['add_time']]['type'] = 3;
                    unset($list[$k]);
                }
            }

        }

        $my_order[4]=M('Touzi_order')->where(array('uid'=>$uid))->count();
        if(isset($_GET['type'])&&$_GET['type']==4){
            if(IS_AJAX){
                $n=$_POST['n']*5;
                $list=(array)M('Touzi_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time')->order('add_time desc')->limit($n,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='p2p投资';
                    $list[$v['add_time']]['type']=4;
                    unset($list[$k]);
                }
                $this->assign('list',$list);
                if($list){
                    $x = $this->fetch('./tpl/Wap/default/gta/Gta_user_orderlist1.html', $list);//内容放进来
                    exit($x);
                }else{
                    exit();
                }
            }else{
                $list=(array)M('Touzi_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time')->order('add_time desc')->limit(0,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='p2p投资';
                    $list[$v['add_time']]['type']=4;
                    unset($list[$k]);
                }
            }

        }
        $my_order[5]=M('Property_insurance_order')->where(array('uid'=>$uid))->count();
        if(isset($_GET['type'])&&$_GET['type']==5){//财产险
            if(IS_AJAX){
                $n=$_POST['n']*5;
                $list=(array)M('Property_insurance_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time,pid_name as title')->order('add_time desc')->limit($n,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='财产险';
                    $list[$v['add_time']]['type']=5;
                    unset($list[$k]);
                }
                $this->assign('list',$list);
                if($list){
                    $x = $this->fetch('./tpl/Wap/default/gta/Gta_user_orderlist1.html', $list);//内容放进来
                    exit($x);
                }else{
                    exit();
                }
            }else{
                $list=(array)M('Property_insurance_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time,pid_name as title')->order('add_time desc')->limit(0,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='财产险';
                    $list[$v['add_time']]['type']=5;
                    unset($list[$k]);
                }

            }
        }
        $my_order[6]=M('Gta_cart_order')->where(array('uid'=>$uid))->count();
        if(isset($_GET['type'])&&$_GET['type']==6) {//车险
            if(IS_AJAX){
                $n=$_POST['n']*5;
                $list=(array)M('Gta_cart_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time')->order('add_time desc')->limit($n,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='车辆险';
                    $list[$v['add_time']]['type']=6;
                    unset($list[$k]);
                }
                $this->assign('list',$list);
                if($list){
                    $x = $this->fetch('./tpl/Wap/default/gta/Gta_user_orderlist1.html', $list);//内容放进来
                    exit($x);
                }else{
                    exit();
                }
            }else{
                $list=(array)M('Gta_cart_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time')->order('add_time desc')->limit(0,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='车辆险';
                    $list[$v['add_time']]['type']=6;
                    unset($list[$k]);
                }
            }

        }
        $my_order[7]=M('Gta_yiwai_order')->where(array('uid'=>$uid))->count();
        if(isset($_GET['type'])&&$_GET['type']==7) {//意外险
            if(IS_AJAX){
                $n=$_POST['n']*5;
                $list=(array)M('Gta_yiwai_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time,title')->order('add_time desc')->limit($n,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='意外险';
                    $list[$v['add_time']]['type']=7;
                    unset($list[$k]);
                }
                $this->assign('list',$list);
                if($list){
                    $x = $this->fetch('./tpl/Wap/default/gta/Gta_user_orderlist1.html', $list);//内容放进来
                    exit($x);
                }else{
                    exit();
                }
            }else{
                $list=(array)M('Gta_yiwai_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time,title')->order('add_time desc')->limit(0,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='意外险';
                    $list[$v['add_time']]['type']=7;
                    unset($list[$k]);
                }
            }
        }
        $my_order[8]=M('Gta_logistics_order')->where(array('uid'=>$uid))->count();
        if(isset($_GET['type'])&&$_GET['type']==8) {//物流险订单
            if(IS_AJAX){
                $n=$_POST['n']*5;
                $list=(array)M('Gta_logistics_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time')->order('add_time desc')->limit($n,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='物流险';
                    $list[$v['add_time']]['type']=8;
                    unset($list[$k]);
                }
                $this->assign('list',$list);
                if($list){
                    $x = $this->fetch('./tpl/Wap/default/gta/Gta_user_orderlist1.html', $list);//内容放进来
                    exit($x);
                }else{
                    exit();
                }
            }else{
                $list=(array)M('Gta_logistics_order')->where(array('uid'=>$uid))->field('id,orderid,status,add_time')->order('add_time desc')->limit(0,5)->select();//
                foreach($list as $k=>$v){
                    $list[$v['add_time']]=$v;
                    $list[$v['add_time']]['typename']='责任险';
                    $list[$v['add_time']]['type']=8;
                    unset($list[$k]);
                }
            }

        }
        $this->assign('my_order',$my_order);
        $this->assign('list',$list);
        $this->UDisplay();
    }
    //查看订单祥情
    public function user_order_info(){
        if(isset($_GET['type'])&&$_GET['type']==1){//贷款
            $info=M('Gta_loan')->find($_GET['id']);
            $info['imgs']=explode(',',$info['imgs']);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
           // p($info['imgs']);die;
            $info['title']='贷款';

        }
        if(isset($_GET['type'])&&$_GET['type']==2){//理财
            $info=M('Licai_order')->find($_GET['id']);
            if(!M('Licai')->find($info['lid'])){
                    unset($info['lid']);
            }
        }

        if(isset($_GET['type'])&&$_GET['type']==3){//人寿险
            $info=M('Gta_life_order')->find($_GET['id']);
            $info['imgs']=explode(',',$info['imgs']);
            $info['fu_name']=explode(',',$info['fu_name']);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            if(!M('Gta_life_product')->find($info['lid'])){
                unset($info['lid']);
            }
          //  p($info);die;
        }
        if(isset($_GET['type'])&&$_GET['type']==4){//  p2p投资
            $info=M('Touzi_order')->find($_GET['id']);
            if(!M('P2p_touzi')->find($info['pid'])){
                unset($info['pid']);
            }
            $syjl=M('Gta_syjl')->where(array('orderid'=>$_GET['id'],'type'=>1))->order('id')->select();
            $this->assign('syjl',$syjl);
        }
        if(isset($_GET['type'])&&$_GET['type']==5){//  财产险
            $info=M('Property_insurance_order')->find($_GET['id']);
            $info['imgs']=explode(',',$info['imgs']);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            $info['products']=json2arr(json_decode($info['products']));
            $info['title']=$info['pid_name'];
            if(!M('Gta_home')->find($info['Pid'])){
                unset($info['Pid']);
            }
        }
        if(isset($_GET['type'])&&$_GET['type']==6){//  车辆险
            $info=M('Gta_cart_order')->find($_GET['id']);
            $info['imgs']=explode(',',$info['imgs']);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            $info['one_name']=explode(',',$info['one_name']);
            $info['two_name']=explode(',',$info['two_name']);
            $info['companys']=explode(',',$info['companys']);
            $info['title']='车辆险';
            $info['oder_info']=json_decode($info['oder_info'],true);
            $num=count($info['oder_info']['money']);
            $money=$info['oder_info']['money'];
            $title=$info['oder_info']['title'];
            $abatement=$info['oder_info']['abatement'];
            for($i=0;$i<$num;$i++){
                $info['oder_info1'][]=array('title'=>$title[$i],'money'=>$money[$i],'abatement'=>$abatement[$i]);
            }
        }
      //  p($info);die;
        if(isset($_GET['type'])&&$_GET['type']==7){//  意外险
            $info=M('Gta_yiwai_order')->find($_GET['id']);
            $info['imgs']=explode(',',$info['imgs']);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
          //  p($info);
            $info['items']=json2arr(json_decode($info['items']));
            if(!M('Gta_yiwai')->find($info['yid'])){
                unset($info['yid']);
            }

        }
        if(isset($_GET['type'])&&$_GET['type']==8){//  物流险
            $info=M('Gta_logistics_order')->find($_GET['id']);
            $info['imgs']=explode(',',$info['imgs']);
            $info['imgs']=array_filter($info['imgs']);
            shuffle($info['imgs']);//重新排数组
            $info['info']=json2arr(json_decode($info['info']));
         //   $info['title']='物流险订单';
            foreach($info['info'] as $k=>$v){
                $info['info'][$k]['unit']=M('Gta_zeren')->where(array('name'=>$v['name']))->getField('unit');
            }

        }

        //p($info);die;
        $this->assign('info',$info);

        $this->UDisplay();
    }
    //个人资料
    public function user_my_info(){
        if(IS_POST){
          // p($_POST);die;
            //$a=M('')->add();
            //先搞验证码
            $yzmYz=validCode($this->token,$_POST['phone'],$_POST['code']);
            $yzmYz=json_decode($yzmYz,true);
            if($yzmYz['code']!=0){
                echo json_encode(array('status'=>2));
                die;
            }
            if($id=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('id')){//有就修改
                if(!isset($_POST['member_sn'])){
                   $_POST['member_sn']="Gta".date("YmdHis",time()).rand(100,999);
                }
                if(!M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('img')){//生成二维码

                    $code=new Code($this->token,'9'.$id);
                    $gta['img']=$code->getYJCode();

                    // echo $id;die;
                    M('Gta_users')->where(array('id'=>$id))->save(array('img'=>$gta['img']));
                }
                if(M('Gta_users')->where(array('uid'=>$this->wxusers_id))->save($_POST)!==false){

                   /* if($_POST['cart']){


                    }*/
                    echo json_encode(array('status'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }
            }else{//没有就新增
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->wxusers_id;
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['bank_name']=$this->_post('bank_name');
                $data['bank_num']=$this->_post('bank_num');
                $data['add_time']=time();
                $data['cart']=$this->_post('cart');
                $data['sheng']=$this->_post('sheng');
                $data['shi']=$this->_post('shi');
                $data['qu']=$this->_post('qu');
                if($id=M('Gta_users')->add($data)){
                    if($_POST['cart']){
                        if(!M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('img')){//生成二维码
                            $code=new Code($this->token,'9'.$id);
                            $gta['img']=$code->getYJCode();
                            M('Gta_users')->where(array('id'=>$id))->save(array('img'=>$gta['img']));
                        }
                    }
                    echo json_encode(array('status'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }
            }


        }else{
            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->find();
            $this->assign('info',$info);
            if($info['phone']){
                $this->UDisplay();
            }else{
                $this->UDisplay('Gta_user_my_info1');
            }
        }

    }
    //会员充值
    public function user_chongzhi(){
        if(IS_POST){
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['pay_type']=1;
            $data['money']=$_POST['money'];
            $data['add_time']=time();
            $data['status']=0;
            if($id=M('Usercenter_money_record')->add($data)){
                echo json_encode(array('status'=>1,'id'=>$id));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else{
            $list=M('Usercenter_money_record')->field('money,add_time')->where(array('token'=>$this->token,'openid'=>$this->openid,'status'=>1))->order('add_time desc')->select();
            $this->assign('list',$list);
            $this->UDisplay();
        }
    }
    //会员提现
    public function user_tixian(){
        if(IS_POST){
            if($_POST['type']==2){//佣金提现过来
                $time=M('gta_more')->where(array('token'=>$this->token))->getField('time');

            }
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            $data['true_name']=$this->_post('name');
            $data['phone']=$this->_post('phone');
            $data['bank_card']=$this->_post('bank_num');
            $data['bank_name']=$this->_post('bank_name');
            $data['add_time']=date('Y-m-d H:i:s',time());
            $data['number']=$this->_post('money');
            $data['type']=$this->_post('type');
           // p($data);die;
            if(M('Tixianjl')->add($data)){
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }

        }else{

            //算出佣金现在能不能提
            $time=M('gta_more')->where(array('token'=>$this->token))->getField('time');
            $this->assign('gc_time',$time);
            $time=strtotime("-$time day");
            $time_jl=M('Tixianjl')->where(array('type'=>2,'token'=>$this->token,'openid'=>$this->openid))->getField('add_time');
            if($time>=strtotime($time_jl)){//可以提
                $this->assign('time',1);
            }else{

                $this->assign('time',0);
            }

            $info=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->find();
            //佣金
            $olo_money=M('Tixianjl')->where(array('type'=>2,'token'=>$this->token,'openid'=>$this->openid,'status'=>array('in',array(1,2))))->sum('number');
            //余额
            $yu_money=M('Tixianjl')->where(array('type'=>1,'token'=>$this->token,'openid'=>$this->openid,'status'=>array('in',array(1,2))))->sum('number');



            $this->assign('info',$info);
            $this->assign('olo_money',$olo_money);
            $this->assign('yu_money',$yu_money);
            $this->UDisplay();
        }
    }
    //提现记录
    public function user_tixianjl(){
        $list=M('Tixianjl')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->select();
        $this->assign('list',$list);
        $this->UDisplay();
    }
    /**
     *  我的佣金
     */
    public function commission(){
        $homenice_commission=M('Edia_user_commission');
        $list=$homenice_commission
            ->where(array('token'=>$this->token,'openid'=>$this->openid))
            ->order('add_time desc')
            ->field('g_openid,g_name,add_time,yj')
            ->select();
        $total=$homenice_commission ->where(array('token'=>$this->token,'openid'=>$this->openid))->sum('yj');
    //    p($total);die;
        $this->assign('list',$list);
        $this->assign('total',$total);
        $this->UDisplay();
    }


    /*查询个人微信相关信息*/
    public function wxinfo($openid){
        $user = M('Wxuser')->where(array('token'=>$this->token))->find();
        $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$openid))->find();
        return $users;
    }

    /**
     * 我的客户  attribution 佣金
     */
    public function kefu(){
        $openid=$this->openid;
        $list=$this->tojing($openid);
        $this->assign('list',$list);
        $dopenid=M('Gta_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('dopenid');
        if($dopenid){
            $wxinfo = $this->wxinfo($dopenid);
            $d_nickname= $wxinfo['nickname'];
            $this->assign('d_nickname',$d_nickname);
        }else{
            $this->assign('d_nickname','');
        }
        $this->UDisplay();
    }
    //专属二唯码
    public function code(){
        $img=M('Gta_users')->where(array('uid'=>$this->wxusers_id))->getField('img');
        $this->assign('img',$img);
        $this->UDisplay();

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
    //验证手机号码
    public function is_phone(){
        //先去发验证码
        if(IS_POST){
            if(cookie('code_time')){//有cookie值不发码
                $res['status']=2;
            }else{
                $phone=$this->_post('phone');
                $openidYz=sendPhomeCode($this->token,$phone);
                $openidYz=json_decode($openidYz,true);
                cookie('code_time',time(),120);//设置两分钟
                $res='';
                if($openidYz['code']==0){
                    $res['status']=1;
                    $res['str']="验证码发送成功!";
                }else{
                    $res['status']=0;
                    $res['str']="验证码发送失败!";
                }
            }
            echo json_encode($res);
        }
    }
    //协议
    public function xy(){
        $info=M('Gta_more')->where(array('token'=>$this->token))->getField('xy');
        $this->assign('info',$info);
        $this->UDisplay();

    }
}
