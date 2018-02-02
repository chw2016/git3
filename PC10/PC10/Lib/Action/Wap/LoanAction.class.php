<?php

/**
 * 贷款  李铭   2015.5.12
 *
 */
class LoanAction extends BaseAction{
    public $token;
    public $wecha_id = 'gh_aab60b4c5a39';
    public $product_model;
    public $product_cat_model;
    public $session_cart_name;
    public $dopenid;
    public $_sTplBaseDir = 'Wap/default/loan';
    public $wxusers_id;//wxusers 表的id
    public $uid;//Credit_users表的id
    public $token_phone;

    public function _initialize() {

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

        /**
         * 得wxusers id
         */

        $this->wxusers_id=M('Wxuser')->where(array('token'=>$this->token))->getField('id');
        $this->wxusers_id=M('Wxusers')->where(array('uid'=>$this->wxusers_id,'openid'=>$this->openid))->getField('id');

        $this->uid=M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('id');
        $this->token_phone=M('speeddial')->where(array('token'=>$this->token))->getField('phone');

       // echo $id;
        $this->assign('signPackage',$signPackage);
    }
    //首页
    public function index(){
        if(isset($_GET['dopenid'])){//

            if(!M('Loan_score_jl')->where(array('token'=>$this->token,'openid'=>$_GET['dopenid'],'from_openid'=>$this->openid))->find()){
                $score=10;//加10积分
                M('Credit_users')->where(array('token'=>$this->token,'openid'=>$_GET['dopenid']))->setInc('jifeng_add',$score);
                //在记录表里插一条数据
                $data3['token']=$this->token;
                $data3['openid']=$_GET['dopenid'];
                $data3['from_openid']=$this->openid;
                $data3['add_time']=time();
                $data3['score']=$score;
                $data3['type']=1;
                M('Loan_score_jl')->add($data3);
            }

        }

        $imgs = M('Flash')->where(array('token'=>$this->token))->order('id desc')->select();

        $fl=M('Credit_fl')->where(array('token'=>$this->token))->select();
     //   p($fl);
        foreach($fl as $k=>$v){//如果分类下面没有产品，就不会显示分类
            if(!M('No_credit')->where(array('token'=>$this->token,'fid'=>$v['id']))->find()){
                unset($fl[$k]);
            }
        }
      //  p($fl);
        $cp=M('Credit')->where(array('token'=>$this->token))->getField('id');

        $this->assign('cp',$cp);


        $this->assign('fl',$fl);
        $this->assign('imgs',$imgs);
        $this->assign('foot',1);
        $this->UDisplay("Loan_index");
    }
    //爆款
    public function baoku(){
        $info=M('No_credit')->field('id,baoku_pic')->where(array('token'=>$this->token,'baoku_pic'=>array('neq','')))->find();
        $this->assign('info',$info);
        $this->assign('foot',2);
        $this->UDisplay("baoku");
    }
    //分亨
    public function fx(){
        /**
         * 没有受权，获取appid
         */
        $appidInfo=M('Diymen_set')->where(array('token'=>$this->token))->find();

        $this->assign('appidInfo',$appidInfo);
           $img=M('Wxuser')->where(array('token'=>$this->token))->getField('headerpic');
        $this->assign('img',$img);
        //echo $img;
        $this->UDisplay("fx");
    }
    //更多
    public function more(){
        $list=M('Product_new_article')->field('id,title,pic')->where(array('token'=>$this->token))->select();
        $this->assign('list',$list);
        $this->assign('foot',5);
        $this->UDisplay("more");
    }
    //更多文章
    public function article(){
        $info=M('Product_new_article')->field('title,info')->find($_GET['id']);
        $this->assign('info',$info);
        $this->assign('foot',5);
        $this->UDisplay("article");
    }
    //旅游产品列表页
    public function loan_list(){
        $product_model=M('No_credit');
        $list=$product_model->field("title,pic,id,shoufu")->where(array('token'=>$this->token,'fid'=>$_GET['id']))->order("add_time desc")->select();

        $this->assign('list',$list);
        $this->UDisplay("loan_list");
    }

    //贷款产品列表页
    public function credit_list(){
        $product_model=M('Credit');
        $list=$product_model->field("name,pic,id,max_price")->where(array('token'=>$this->token))->order("add_time desc")->select();

        $this->assign('list',$list);
        $this->UDisplay("credit_list");
    }

    /**
     * 非信贷产品页(旅游)
     */
    public function no_credit(){
        $info=M('No_credit')->where(array('id'=>$_GET['id']))->find();

        /**
         * 封装备图片好循环出来
         */
        for($i=1;$i<6;$i++){
           if($info['image'.$i]){
               $info['images'][]=$info['image'.$i];
           }
        }
        if($info['day_num']){
            $info['day_num']=explode(':',$info['day_num']);
        }
        if($info['content']){
            $info['content']=explode(':',$info['content']);
        }
        if($info['start_address']){
            $info['start_address']=explode(':',$info['start_address']);
        }
        if($info['end_address']){
            $info['end_address']=explode(':',$info['end_address']);
        }
        if($info['info1']){
            $info['info1']=explode(':',$info['info1']);
        }
        if($info['info2']){
            $info['info2']=explode(':',$info['info2']);
        }
        if($info['info3']){
            $info['info3']=explode(':',$info['info3']);
        }
        if($info['info4']){
            $info['info4']=explode(':',$info['info4']);
        }
        //p($info);die;

        $this->assign('info',$info);
        if(isset($_GET['foot'])){
            $this->assign('foot',2);
        }

        $this->UDisplay("no_credit");
    }

    public function no_credit_xiadan(){
        if(IS_POST){

            /**
             * 先注册下用户到这个表Credit_users
             */
           /* if(){

            }*/
        //p($_POST);die;
            if($_POST['people_num']==''){//没有日期的单
                $_POST['people_num']=1;

            }else{//有日期的单
                //判断重复下单过来的
                if($_POST['d']==''){//没有日期
                    if(M('No_credit_order')->where(array('cid'=>$_POST['id'],'type'=>1,'paystatus'=>0))->find()){
                        echo json_encode(array('status'=>4));die;//已经下单
                    }
                }else{//有日期
                    if(M('No_credit_order')->where(array('cid'=>$_POST['id'],'type'=>1,'paystatus'=>0,'date_time'=> $_POST['y_m'].'-'.$_POST['d']))->find()){
                        echo json_encode(array('status'=>4));die;//已经下单
                    }
                }

                $num_total=M('Nocredit_time')->where(array('cid'=>$_POST['id'],'d'=>$_POST['d'],'y_m'=>$_POST['y_m']))->getField('num');
             //   echo $num_total."<br />";
              //  $num_arr=M('No_credit_order')->field('people_num')->where(array('type'=>1,'cid'=>$_POST['id'],'paystatus'=>array('in',array(0,1,5,7))))->select();
             //   echo $num_total;

                $a=M('No_credit_order')->where(array('cid'=>$_POST['id'],'type'=>1,'paystatus'=>array('in',array(0,1,5,12)),'date_time'=> $_POST['y_m'].'-'.$_POST['d']))->getField('sum(people_num)');
               $b=M('No_credit_order')->where(array('cid'=>$_POST['id'],'type'=>1,'paystatus'=>array('in',array(0,1,5,12)),'date_time'=>  $_POST['y_m'].'-'.$_POST['d'],'people_num'=>3))->count();

            //   echo  $num_total-($a-$b);die;
                if($num_total-($a-$b)<$_POST['people_num']){
                    echo json_encode(array('status'=>3));//数量不足
                    die;
                }
            }



            $Credit_users_model=M('Credit_users');
            if(!$Credit_users_id=$Credit_users_model->where(array('wxusers_id'=>$this->wxusers_id))->getField('id')){

                $user['token']=$this->token;
                $user['openid']=$this->openid;
                $user['true_name']=$this->_post('name1');
                $user['phone']=$this->_post('phone1');
                $user['wxusers_id']=$this->wxusers_id;
                $Credit_users_id=$Credit_users_model->add($user);
            }else{//有的话就把姓名跟真实名字改了
                $data3['true_name ']=$_POST['name1'];
                $data3['phone']=$_POST['phone1'];
                $Credit_users_model->where(array('id'=>$Credit_users_id))->save($data3);
            }
            $No_credit_model=M('No_credit');
            $info=$No_credit_model->where(array('id'=>$_POST['id']))->find();
          //  p($info);
            if($_POST['many']>=1){
                $data['many']=$_POST['many'];
            }else{
                $data['many']=1;
            }
            $data['random']=$this->_post('random');
           // p($_POST);die;
            if($_POST['many']>1){//有购买数量的订单
                $data['shoufu']=$info['shoufu']*$_POST['many'];
                $data['fenqi']=$info['fenqi'];
                $data['monthly_repayments']=$info['monthly_repayments']*$_POST['many']+round(($_POST['random']/$info['fenqi']),2);
                $data['people_num']=$_POST['people_num'];
                $data['title']=$info['title'];

            }else{//没有购买数量的订单
                if($_POST['people_num']==3){//这里代表付2个人的全款
                    $data['shoufu']=$info['shoufu']*2;
                    $data['fenqi']=$info['fenqi'];
                    $data['monthly_repayments']=$info['monthly_repayments']*2+round(($_POST['random']/$info['fenqi']),2);
                    $data['people_num']=3;
                    $data['title']=$info['title'];
                    $data['many']=2;

                }else{//这里代表AA制付款
                    $data['shoufu']=$info['shoufu'];
                    $data['fenqi']=$info['fenqi'];
                    $data['monthly_repayments']=$info['monthly_repayments']+round($_POST['random']/$info['fenqi'],2);
                    $data['people_num']=$_POST['people_num'];
                    $data['title']=$info['title'];
                }

            }

            //$Credit_users_model->add();
            $No_credit_order_model=M('No_credit_order');
            $data['cid']=$this->_post('id');
            $data['type']=1;//非信贷产品
            $data['add_time']=time();
            $data['token']=$this->token;
            $data['openid']=$this->openid;
            if($_POST['d']){
                $data['date_time']=$this->_post('y_m').'-'.$this->_post('d');
            }else{
                $data['date_time']='';
            }

            $data['uid']=$Credit_users_id;
            $data['orderid']=$this->getSn();

           //
            if($id=$No_credit_order_model->add($data)){
                /**
                 * 把旅游人资料加上
                 */
                $No_credit_people_model=M('No_credit_people');
                $people1['name']=$this->_post('name1');
                $people1['phone']=$this->_post('phone1');
                $people1['other']=$this->_post('other1');
                $people1['cid']=$id;
                $No_credit_people_model->add($people1);
                if($_POST['people_num']>1&&!empty($_POST['name2'])){
                    $people2['name']=$this->_post('name2');
                    $people2['phone']=$this->_post('phone2');
                    $people2['cid']=$id;
                    $No_credit_people_model->add($people2);
                }

                echo json_encode(array('orderid'=>$data['orderid'],'status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
          //  $data['']
          //  p($_POST);
        }else{
            //没有验证过手机就行验证手机
           /* if(!M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('phone')){
                header("location:".U('phone',array('token'=>$this->token,'openid'=>$this->openid)));
                die;
            }*/

            /**
             * 日期
             */
            $id=$this->_get('id');
            $shoufu=M('No_credit')->where(array('id'=>$id))->getField('shoufu');
            $list=M('Nocredit_time')->where(array('cid'=>$id,'status'=>1))->select();
            $calc=new Calendar();
            $time_info=$calc->showCalendar($list,$id);
            $this->assign('time_info',$time_info);
           // echo $time_info;die;

            $info=M('No_credit')->where(array('id'=>$_GET['id']))->find();
            $last_y= date("Y", strtotime("+1 month"));
            $last_m= date("m", strtotime("+1 month"));
            $last_2y= date("Y", strtotime("+2 month"));
            $last_2m= date("m", strtotime("+2 month"));

            // 三种日期
             $last="?g=Wap&m=Loan&a=no_credit_xiadan&id=".$_GET['id']."&y=".$last_y."&m1=".$last_m."&token=".$this->token."&openid=".$this->openid.'&type=2';
             $now="?g=Wap&m=Loan&a=no_credit_xiadan&id=".$_GET['id']."&y=".date('Y')."&m1=".date('m')."&token=".$this->token."&openid=".$this->openid.'&type=1';
             $last_2="?g=Wap&m=Loan&a=no_credit_xiadan&id=".$_GET['id']."&y=".$last_2y."&m1=".$last_2m."&token=".$this->token."&openid=".$this->openid.'&type=3';
             $this->assign('last',$last);
            $this->assign('now',$now);
            $this->assign('last_2',$last_2);
            //得个人资料
            $user_info=M('Credit_users')->field('true_name,phone')->where(array('wxusers_id'=>$this->wxusers_id))->find();
            $this->assign('user_info',$user_info);
            $info['is_show']=explode(',',$info['is_show']);
           // p($info);die;
            $this->assign('info',$info);
            $this->UDisplay("no_credit_xiadan");
        }

    }
    //贷款下单
    public function credit_xiadian(){
        if(IS_POST){
           // p($_POST);die;
            //没有验证过手机就行验证手机
            /*if(!M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('phone')){
              //  header("location:".U('phone',array('token'=>$this->token,'openid'=>$this->openid)));
              //  die;
                echo json_encode(array('status'=>3));//跳到去验证手机号码
                die;

            }*/
           // die;
            session('cid',$_GET['cid']);
            session('loan_total_money',$_POST['loan_total_money']);
            session('fenqi',$_POST['fenqi']);
            session('monthly_repayments',$_POST['monthly_repayments']);
            $Credit_users_model=M('Credit_users');
            if($Credit_users_model->where(array('wxusers_id'=>$this->wxusers_id))->getField('pid')==''){//是否有图片来判断资料有没有填过
                echo json_encode(array('status'=>1));//跳到去填个人资料填写页面
            }else{
                echo json_encode(array('status'=>2));//直接跳过去
            }


        }else{
            $info=M('Credit')->find($_GET['id']);
            $this->assign('info',$info);
            //利率
            $lilu=M('Credit_lilu')->where(array('token'=>$this->token))->select();
            foreach($lilu as $k=>$v){
             //   $lilu[$v['qisu']]=$v['lilu'];
              //  unset($lilu[$k]);
            }
            $arr=array();
            for($i=0;$i<count($lilu);$i++){
                $arr[$lilu[$i]['qisu']]=$lilu[$i]['lilu'];
            }

            $arr=json_encode($arr);

           // p($arr);
            $this->assign('lilu',$arr);
            $this->UDisplay("credit_xiadian");

        }
    }

    //支付旅游订单页面
    public function zhifu(){
        if(isset($_GET['qingsu'])){//这里代表还款的支付
            $info=M('No_credit_order')->field('orderid,monthly_repayments')->where(array('id'=>$_GET['id']))->find();
            $orderid_qing=$info['orderid']."_".$_GET['qingsu'];


            //改装
            $info['shoufu']=$info['monthly_repayments'];

            $info['orderid']=$orderid_qing;
            $this->assign('info',$info);
            $this->assign('type_url',1);

        }else{//正常下单
            $order_model=M('No_credit_order');
            $info=$order_model->field('shoufu,orderid,uid,paystatus')->where(array('orderid'=>$_GET['orderid']))->find();
            //判断这单是否已经被支付
            //p($info);die;

            if(M('Credit_users')->where(array('id'=>$info['uid']))->getField('pid')){//以图片来判断是否
                $this->assign('type_url',1);
            }else{
                $this->assign('type_url',2);
            }
            $this->assign('info',$info);
        }

        $this->UDisplay("zhifu");
    }
    // 获取唯一订单号
    public function getSn(){

        return substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
    }
    //申请贷款，保存申请资料
    public function sq_loan(){
        if(IS_POST){
       // p($_POST);die;
       //     session('cid',1);
          //  session('loan_total_money',7500);
          //  session('fenqi',5);
          //  session('monthly_repayments',800);
            $Credit_users_model=M('Credit_users');

            if($id=$Credit_users_model->where(array('token'=>$this->token,'wxusers_id'=>$this->wxusers_id,'openid'=>$this->openid))->getField('id')){
                //已经存在资料就保存

                if($Credit_users_model->where(array('id'=>$id))->save($_POST)!==false){
                    //保存图片

                    /**
                     * 把图片插进去
                     */
                    $Credit_users_pic_model=M('Credit_users_pic');
                    $Credit_users_pic_model->where(array('uid'=>$id))->delete();//先删除
                    $pic=explode(',',$_POST['pids']);
                    $pic_id='';
                    foreach($pic as $v){
                        $pic_id.=$Credit_users_pic_model->add(array('uid'=>$id,'pic'=>$v,'token'=>$this->token,'add_time'=>time())).',';

                    }
                    //把主键放进去
                    $Credit_users_model->where(array('id'=>$id))->save(array('pid'=>$pic_id));

                    /**
                     * 把联系人插进去
                     */
                    $Credit_users_friend_model=M('Credit_users_friend');
                 //  $Credit_users_friend_model->where(array('uid'=>$id))->delete();//先删除
                    $name=explode('|',$_POST['name']);
                    $phone=explode('|',$_POST['phones']);
                    //  $zhiye1=explode(',',$_POST['zhiye1']);
                    $address=explode('|',$_POST['address']);
                    $friendly=explode('|',$_POST['fried']);
                    $lid_id='';
                    for($i=0;$i<count($name);$i++){
                        $lid_id.=$Credit_users_friend_model->add($a=array('uid'=>$id,'name'=>$name[$i],'phone'=>$phone[$i],
                                'name'=>$name[$i],'address'=>$address[$i],'friendly'=>$friendly[$i],'add_time'=>time())).',';

                    }
                    //把主键放进去
                    $Credit_users_model->where(array('id'=>$id))->save(array('lid'=>$lid_id));

                    echo json_encode(array('id'=>$id,'status'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }

            }else{//不存在就新增
                $_POST['wxusers_id']=$this->wxusers_id;
                $_POST['token']=$this->token;
                $_POST['openid']=$this->openid;

                if($id=$Credit_users_model->add($_POST)){
                    /**
                     * 把图片插进去
                     */
                    $Credit_users_pic_model=M('Credit_users_pic');
                    $pic=explode(',',$_POST['pids']);
                    $pic_id='';
                    foreach($pic as $v){
                        $pic_id.=$Credit_users_pic_model->add(array('uid'=>$id,'pic'=>$v,'token'=>$this->token,'add_time'=>time())).',';

                    }
                    //把主键放进去
                    $Credit_users_model->where(array('id'=>$id))->save(array('pid'=>$pic_id));
                    /**
                     * 把联系人插进去
                     */
                    $Credit_users_friend_model=M('Credit_users_friend');
                    $name=explode('|',$_POST['name']);
                    $phone=explode('|',$_POST['phone']);
                  //  $zhiye1=explode(',',$_POST['zhiye1']);
                    $address=explode('|',$_POST['address']);
                    $friendly=explode('|',$_POST['fried']);
                    $lid_id='';
                    for($i=0;$i<count($name);$i++){
                        $lid_id.=$Credit_users_friend_model->add(array('uid'=>$id,'name'=>$name[$i],'phone'=>$phone[$i],
                                'name'=>$name[$i],'address'=>$address[$i],'friendly'=>$friendly[$i],'add_time'=>time())).',';
                    }
                    //把主键放进去
                    $Credit_users_model->where(array('id'=>$id))->save(array('lid'=>$lid_id));

                    echo json_encode(array('id'=>$id,'status'=>1));
                }else{

                    echo json_encode(array('status'=>0));
                }

            }




        }else{
            //得个人资料
            $user_info=M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->find();
            $user_info['lid_info']=explode(',',trim($user_info['lid'],','));
            foreach($user_info['lid_info'] as $k=>$v){
                if($v){

                    $user_info['lid_info'][$k]=M('Credit_users_friend')->where(array('id'=>$v))->find();
                }else{

                }
            }
            //得图片
            $user_info['pid']=explode(',',trim($user_info['pid'],','));
            foreach($user_info['pid'] as $k=>$v){
                if($v){
                    $user_info['pid_info'][$k]=M('Credit_users_pic')->where(array('id'=>$v))->getField('pic');

                }
            }
            $user_info['school_time']=explode('-',$user_info['school_time']);
       //   p($user_info);
            $this->assign('user_info',$user_info);
            $this->assign('foot',3);
            $this->UDisplay("sq_loan");
        }
	

    }

    //申请货款展示页面
    public function sq_loan2(){
        $Credit_users_model=M('Credit_users');

        $info=$Credit_users_model->where(array('wxusers_id'=>$this->wxusers_id,'token'=>$this->token,'openid'=>$this->openid))->find();
        $pic_model=M('Credit_users_pic');
        //图片
        $info['pid']=explode(',',trim($info['pid'],','));
        foreach($info['pid'] as $k=>$v){
            $info['pid'][$k]=$pic_model->where(array('id'=>$v))->getField('pic');
        }
        //联系人
        $info['lid']=explode(',',trim($info['lid'],','));
        $friend_model=M('Credit_users_friend');
        foreach($info['lid'] as $k=>$v){
            $info['lid'][$k]=$friend_model->where(array('id'=>$v))->find();
        }
       // p($info);
        $this->assign('loan_price',session('loan_total_money'));
        $this->assign('loan_num',session('fenqi'));
        $this->assign('loan_mon_price',session('monthly_repayments'));
        $this->assign('info',$info);
        $this->UDisplay("sq_loan2");
    }

    //个人资料
    public function sq_loan3(){
        $Credit_users_model=M('Credit_users');
        $info=$Credit_users_model->where(array('wxusers_id'=>$this->wxusers_id,'token'=>$this->token,'openid'=>$this->openid))->find();
        $pic_model=M('Credit_users_pic');
        //图片
        $info['pid']=explode(',',trim($info['pid'],','));
        foreach($info['pid'] as $k=>$v){
            $info['pid'][$k]=$pic_model->where(array('id'=>$v))->getField('pic');
        }
        //联系人
        $info['lid']=explode(',',trim($info['lid'],','));
        $friend_model=M('Credit_users_friend');
        foreach($info['lid'] as $k=>$v){
            $info['lid'][$k]=$friend_model->where(array('id'=>$v))->find();
        }
        // p($info);
        $this->assign('loan_price',"4000");
        $this->assign('loan_num',7);
        $this->assign('loan_mon_price',350);
        $this->assign('info',$info);
        $this->assign('foot',3);
        $this->UDisplay("sq_loan3");
    }
    //提交贷款
    public function tijiao_loan(){
        $data['cid']=$_GET['id'];
        $title=M('Credit')->where(array('id'=>$_GET['id']))->getField('name');
        $data['title']=$title;//标题
                $uid=M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id,'token'=>$this->token,'openid'=>$this->openid))->getField('id');
        $data['uid']=$uid;//用户id
        $data['orderid']=$this->getSn();
        $data['fenqi']=$this->_post('fenqi');
        $data['monthly_repayments']=$this->_post('monthly_repayments');
        $data['loan_total_money']=$this->_post('loan_total_money');
        //$data['add_time']=$this->_post('add_time');
        $data['add_time']=time();
        $data['type']=2;
        $data['token']=$this->token;
        $data['openid']=$this->openid;
        $info_json=M('Credit_users')->find($uid);
        $data['user_info']=json_encode($info_json);
        $No_credit_order_model=M('No_credit_order');
        if($id=$No_credit_order_model->add($data)){
           /* if(M('Credit_users')->where(array('id'=>$uid))->getField('bank_card')==''){//用有没有卡号码来判断
                echo json_encode(array('id'=>$id,'status'=>2));//去填银行卡
            }else{*/
                echo json_encode(array('id'=>$id,'status'=>1));
           // }

        }else{
            echo json_encode(array('status'=>0));
        }
    }
    //提交贷款成功页面
    public function tijiao_loan_success(){
        if(!M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('pwd')){
            $this->UDisplay("set_pwd");
            die;
        }
        if(!M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('bank_card')){
            $this->UDisplay("bank_card2");
            die;
        }
        header("Location:".U('index',array('token'=>$this->token,'openid'=>$this->openid)));
      //  $this->UDisplay("user_index");
    }
    //个人中心首页
    public function user_index(){
        $Credit_users_model=M('Credit_users');
        $info=$Credit_users_model->where(array('wxusers_id'=>$this->wxusers_id))->find();
       // p($info);
        //取微信图像
        $headimg=M('Wxusers')->where(array('id'=>$this->wxusers_id))->getField('headimgurl');
        $this->assign('headimg',$headimg);
        $this->assign('info',$info);
        $this->assign('foot',3);
        $this->UDisplay("user_index");
    }
    //余额奖励
    public function balance_award(){
        //积分算法，
        $jifeng=M('Credit_users')->field('jifeng_add,jifeng_back')->where(array('wxusers_id'=>$this->wxusers_id))->find();
        $jifeng_dy=M('Shop_users')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('score');
        $jifeng=$jifeng['jifeng_add']+$jifeng_dy-$jifeng['jifeng_back'];
        $this->assign('jifeng',$jifeng);
        $this->UDisplay("balance_award");
    }

    //还款
    public function repayment(){
        if(IS_POST){
            if(M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->save(array('hk_d'=>$_POST['d']))!==false){
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else{
            /**
             * 日期
             */
            $id=$this->_get('id');
            $shoufu=M('No_credit')->where(array('id'=>$id))->getField('shoufu');
            $list=M('Nocredit_time')->where(array('cid'=>$id,'status'=>1))->select();
            $calc=new Calendar();
            $time_info=$calc->showCalendar($list,$id);
            //得还款设定的日期
            $uid=M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('id');
            $add_time_arr=(array)M('No_credit_order')->field('hs_time')->where(array('uid'=>$uid,'paystatus'=>1,'type'=>2))->select();
            $add_time_arr2=(array)M('No_credit_order')->field('hs_time')->where(array('uid'=>$uid,'paystatus'=>5,'type'=>1))->select();
            $add_time_arr=array_merge($add_time_arr,$add_time_arr2);

           foreach($add_time_arr as $k=>$v){
               $add_time_arr[$k]=(int)date('d',$v['hs_time']);
           }
          //  p($add_time_arr);
          //  $add_time_arr[4]=1;
            $this->assign('add_time_arr',$add_time_arr);
            $this->assign('foot',3);
            $this->assign('time_info',$time_info);
           // p($add_time_arr);
            $this->assign('foot_css',1);
            $this->UDisplay("repayment");
        }

    }

    //修改绑定的银行卡
    public function bank_card(){
        $Credit_users_model=M('Credit_users');
        $info=$Credit_users_model->where(array('wxusers_id'=>$this->wxusers_id))->find();
        if(IS_POST){//修改银行卡
            $data['bank_name']=$this->_post('bank_name');
            $data['bank_card']=$this->_post('bank_card');
            $data['bank_city']=$this->_post('bank_city');
           // p($_POST);die;
            if($info['pwd']==md5($_POST['pwd'])){
                if($Credit_users_model->where(array('id'=>$info['id']))->save($data)){
                    echo json_encode(array('status'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }

            }else{
                echo json_encode(array('status'=>0,'msg'=>'密码错误'));
            }
        }else{

           // p($info);
            $this->assign('foot',3);
            $this->assign('info',$info);

            $this->UDisplay("bank_card");
        }

    }

    //修改绑定的银行卡第二个页面
    public function bank_card2(){
        $info=M('Credit_users')->field('bank_city,bank_name,bank_card,true_name')->where(array('wxusers_id'=>$this->wxusers_id))->find();
        $this->UDisplay("bank_card2");
    }
    //我的联系人
    public function friend(){
        $Credit_users_friend_model = M('Credit_users_friend');
        if(IS_POST){
            if($_POST['type1']=='add'){//新增
                $Credit_users_friend_model->create();
                if($Credit_users_friend_model->add()){
                    echo json_encode(array('status'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }
            }else{//修改
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['zhiye']=$this->_post('zhiye');
                $data['address']=$this->_post('address');
                if($Credit_users_friend_model->where(array('uid'=>$_POST['uid']))->save($data)){
                    echo json_encode(array('status'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }
            }
        }else {
            $Credit_users_model = M('Credit_users');
            $uid = $Credit_users_model->where(array('wxusers_id' => $this->wxusers_id))->getField('id');
            //echo        $uid;

            $friend = $Credit_users_friend_model->where(array('uid' => $uid))->select();

        }
    }
    //密码修改
    public function edit_pwd(){
        if(IS_POST){
            $Credit_users_model = M('Credit_users');
            $info = $Credit_users_model->field('id,pwd')->where(array('wxusers_id' => $this->wxusers_id))->find();
            if(isset($_GET['type'])&&$_GET['type']==1){//首次设密码
                if($Credit_users_model->where(array('id'=>$info['id']))->save(array('pwd'=>md5($_POST['pwd1'])))){
                    echo json_encode(array('status'=>1));
                }else{
                    echo json_encode(array('status'=>0));
                }
            }else{
                if($info['pwd']==md5($_POST['old_pwd'])){
                    if($Credit_users_model->where(array('id'=>$info['id']))->save(array('pwd'=>md5($_POST['pwd1'])))){
                        echo json_encode(array('status'=>1));
                    }else{
                        echo json_encode(array('status'=>0));
                    }
                }else{
                    echo json_encode(array('status'=>-1));
                }
            }

        }else{
            $Credit_users_model = M('Credit_users');
            $info = $Credit_users_model->field('id,pwd')->where(array('wxusers_id' => $this->wxusers_id))->find();
           // p($info);
            $this->assign('foot',3);
            if($info['pwd']){//有密码
                $this->UDisplay("edit_pwd");
            }else{
                $this->UDisplay("set_pwd");
            }



        }
    }
    //订单列表
    public function order_list(){
        $No_credit_order_model=M('No_credit_order');
        $where['token']=$this->token;
        $where['openid']=$this->openid;
        if(isset($_GET['fl'])&&$_GET['fl']==1){//旅游单
            $where['type']=1;
        }
        if(isset($_GET['fl'])&&$_GET['fl']==2){//贷款单
            $where['type']=2;
        }
        if(isset($_GET['fl'])&&$_GET['fl']==3){//已支付或者，贷款已通过
            $where['paystatus']=1;
        }
        //还款计划跳过来的
        if(isset($_GET['d'])){//已支付或者，贷款已通过
            if(strlen($_GET['d'])==1){
                $_GET['d']='0'.$_GET['d'];
            }
            $list1=$No_credit_order_model->query($s="select * from tp_no_credit_order where token ='".$this->token."' and openid='".$this->openid."'
              and type='1' and paystatus='5' and date_format(from_unixtime(hs_time),'%d')='".$_GET['d']."';");
         //   p($list1);
         //  echo $No_credit_order_model->getLastSql();
            $list2=$No_credit_order_model->query($s="select * from tp_no_credit_order where token ='".$this->token."' and openid='".$this->openid."'
              and type='2' and paystatus='1' and date_format(from_unixtime(hs_time),'%d')='".$_GET['d']."';");
            $list=array_merge($list1,$list2);
          //  p($list2);
        }else{
            $list=$No_credit_order_model->where($where)->order("add_time desc")->select();
        }
       // p($list);

        //得旅游产品图片
        foreach($list as $k=>$v){
            if($v['type']==1){//旅游产品
                $list[$k]['pic']=M('No_credit')->where(array('id'=>$v['cid']))->getField('pic');
                $list[$k]['pid']=M('Credit_users')->where(array('id'=>$v['uid']))->getField('pid');
            }
            if($v['type']==2){//货款产品
                $list[$k]['pic']=M('Credit')->where(array('id'=>$v['cid']))->getField('pic');
            }


        }
      //  $fl=M('Credit_fl')->field('id,title')->where(array('token'=>$this->token))->select();

       // $this->assign('fl',$fl);
        $this->assign('list',$list);
        //p($list);
        $this->assign('foot',3);
        $this->UDisplay("order_list");
    }
    //订单详情
    public function order_info(){

        $id=$_GET['id'];
        $info=M('No_credit_order')->where(array('id'=>$id))->find();
        if($info['type']==1){
            $info['pic']=M('No_credit')->where(array('id'=>$info['cid']))->getField('pic');
        }
        if($info['type']==2){
            $info['pic']=M('Credit')->where(array('id'=>$info['cid']))->getField('pic');
        }
        //还款记录啊
        $time=M('No_credit_order')->where(array('id'=>$_GET['id']))->getField('hs_time');//订单时间
        $fengqi=M('No_credit_order')->where(array('id'=>$_GET['id']))->getField('fenqi');//分期数
        //  echo $time;
        $time_date=date('Y-m-d',$time);
        $arr=array();
        $c=1;
        for($i=1;$i<=$fengqi;$i++){
            $arr[$i]['start_time']=date("Y-m-d", strtotime('+'.($i-1).' months', $time));
            $a=strtotime($time_date);
            $arr[$i]['end_time']=date("Y-m-d", strtotime('+'.$i.' months', $time));
            $b=strtotime(date("Y-m-d", strtotime('+'.$i.' months', $time)));
            if($hk_jl=M('Hk_jl')->where(array('token'=>$this->token,'oid'=>$_GET['id'],'paystatus'=>1,
                'qisu'=>$i))->find()){
                $arr[$i]['hk_jl']=$hk_jl;
            }else{//这里设定为1时代表没有还款的
                $arr[$i]['c']=$c;
                $c++;
            }
        }
      // p($arr);



        $user_info=M('Credit_users')->field('true_name,phone,pid')->where(array('id'=>$info['uid']))->find();
        $this->assign('info',$info);
       // p($info);die;
        $this->assign('arr',$arr);
        $this->assign('user_info',$user_info);
         //   p($user_info);die;
        $this->assign('foot',3);
        //看这个商品有没有下架
        if(M('No_credit')->find($info['cid'])){
            $this->assign('xiajia',1);
        }else{
            $this->assign('xiajia',0);
        }
       // p($xiajia);die;

        $this->UDisplay("order_info");
    }
    //取消订单
    public function quxiao(){
        if(M('No_credit_order')->where(array('id'=>$_GET['id']))->save(array('paystatus'=>-1))){
            //发送短信消息
          /*  $phone=M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('phone');
            $order=M('No_credit_order')->where(array('id'=>$_GET['id']))->getField('orderid');
            $info="【如多分期】亲爱的用户，您的订单号:".$order."已经取消交易，如果有更多问题，请联系如多分期专属热线：".$this->token_phone;

            $openidYz=sendPhomeCode($this->token,$phone,$info);
            $openidYz=json_decode($openidYz,true);*/
            //发短信
            $phone=M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('phone');
            $info="【如多分期】注意到了你在如多分期上刚有个“取消订单”操作。很好！我们如多一贯支持用户用脚投票剔除不好的产品，你的“取消”是我们的继续改进的动力。谢谢你！";
            $openidYz=sendPhomeCode($this->token,$phone,$info);
            $openidYz=json_decode($openidYz,true);
            $notichcontent ="【如多分期】注意到了你在如多分期上刚有个“取消订单”操作。很好！我们如多一贯支持用户用脚投票剔除不好的产品，你的“取消”是我们的继续改进的动力。谢谢你！";
            $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
            $data = $this->api_notice_increment($url,http_build_query($postdata));
            if(!$data){
                $this->api_notice_increment($url,http_build_query($postdata));
            }
            if($openidYz['code']==0){//为真

        }
           header('location:'.U('order_list',array('token'=>$this->token,'openid'=>$this->openid)));
        //    $this->success2('',U('order_list',array('token'=>$this->token,'openid'=>$this->openid)));
        }else {
            header('location:'.U('order_list',array('token'=>$this->token,'openid'=>$this->openid)));
        }
    }
    //上传图片
    public function upload_img(){
        import('ORG.Net.UploadFile');//导入上传类
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg' ,'png' ,'gif');// 设置附件上传类型
        $upload->savePath =  './upload/loan/';// 设置附件上传目录
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }
        if($upload->upload()){
            // echo "<script language='JavaScrip'>alert('上传成功！);</script>";
            $info =  $upload->getUploadFileInfo();
            $arr='';
            foreach($info as $k=>$v){
                $imgpath=$v['savepath'].$v['savename'];
                $arr[$k] = array(
                    'name'=>$v['savename'],
                    'pic'=>$imgpath,
                );
            }

         //p($arr);die;
            echo json_encode($arr);
        }else{
            $error = $this->error($upload->getErrorMsg());
        }
    }
    //申请退款
    public function tuikuan(){
        M('No_credit_order')->where(array('id'=>$_GET['id']))->save(array('paystatus'=>7));
          //发送短信消息
                $phone=M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('phone');
                $title=M('No_credit_order')->where(array('id'=>$_GET['id']))->getField('title');
        $info="【如多分期】朋友，我看到了你的退款请求了，我也同意并做了处理了。有些遗憾，你是真的不喜欢“".$title."”还是有其他疑问？不要害羞，放心大胆打给我吧，我想和你谈谈。咱的电话4008622580，客服帅哥等你！";
        $openidYz=sendPhomeCode($this->token,$phone,$info);
                $openidYz=json_decode($openidYz,true);
        //发微信
        $notichcontent ="【如多分期】朋友，我看到了你的退款请求了，我也同意并做了处理了。有些遗憾，你是真的不喜欢“".$title."”还是有其他疑问？不要害羞，放心大胆打给我吧，我想和你谈谈。咱的电话4008622580，客服帅哥等你！";
        $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
        $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
        $data = $this->api_notice_increment($url,http_build_query($postdata));
        if(!$data){
            $this->api_notice_increment($url,http_build_query($postdata));
        }
              /*  if($openidYz['code']==0){
                    $res['status']=1;
                    $res['str']="验证码发送成功!";
                }else{
                    $res['status']=0;
                    $res['str']="验证码发送失败!";
                }*/




        header("location:".U('Loan/order_list',array('token'=>$this->token,'openid'=>$this->openid)));
    }
    //填写手机号码页面
    public function phone(){
        $this->UDisplay("phone");
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

        }else{
            $this->UDisplay("is_phone");

        }
    }
    //检查验证码是否通过
    public function is_code(){
        if(IS_POST){
            $yzmYz=validCode($this->token,$_POST['phone'],$_POST['code']);
            $yzmYz=json_decode($yzmYz,true);
            if($yzmYz['code']==0){
                //echo "<script>alert('手机短信验证成功,请保存资料');</script>";
                $data['status']=0;
                //把手机号码插进用户表
                if(M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->find()){//有记录
                    M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->save(array('phone'=>$_POST['phone']));
                }else{//插条新的
                    $data1['wxusers_id']=$this->wxusers_id;
                    $data1['token']=$this->token;
                    $data1['openid']=$this->openid;
                    $data1['phone']=$_POST['phone'];
                    M('Credit_users')->add();
                }

                echo json_encode($data);
            }elseif ($yzmYz['code']==-3){
                $data['status']=-3;
                echo json_encode($data);
              //  echo "<script>alert('系统繁忙,请重试!');history.back();</script>";die;
            }elseif ($yzmYz['code']==-2){
                $data['status']=-2;
                echo json_encode($data);
               // echo "<script>alert('验证超时,3分钟之内有效!');history.back();</script>";die;
            }elseif ($yzmYz['code']==-1){
               // echo "<script>alert('验证失败!');history.back();</script>";die;
                $data['status']=-1;
                echo json_encode($data);
            }

        }
    }
    //验证身份证号码
    public function is_card(){
        if(IS_POST){
            //检测身份证号码是码正确
            if($_POST['sex']==1){//男，默认成功
                $data['status']=1;
                echo json_encode($data);
            }else{//女
                if(is_card($_POST['name'],$_POST['card'],'gzmy_admin','USJAhpCC')){//验证成功
                    $data['status']=1;
                    echo json_encode($data);
                }else{
                    // echo $this->wxusers_id;die;
                    //先处理是新用户就加进去了
                    if(!M('Credit_users')->where(array('token'=>$this->token,'wxusers_id'=>$this->wxusers_id))->find()){//新增
                        $arr1['token']=$this->token;
                        $arr1['openid']=$this->openid;
                        $arr1['wxusers_id']=$this->wxusers_id;
                        M('Credit_users')->add($arr1);

                    }
                    M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->setInc('check_card',1);//错误了就加一次
                    if(M('Credit_users')->where(array('wxusers_id'=>$this->wxusers_id))->getField('check_card')==4){
                        $data['status']=1;//错了三次之后，默认他是错的
                    }else{
                        $data['status']=0;
                    }
                    echo json_encode($data);
                }
            }

        }
    }
    //PC端首页
    public function pc_index(){
    $this->token='55cad4ba46c41a8fde9c84274e36fa83';
   //   $this->token='5d8a87bab30de695954b17fc835b9d12';//本地
        $imgs = M('Flash')->where(array('token'=>$this->token))->order('id desc')->select();
        $fl=M('Credit_fl')->where(array('token'=>$this->token))->select();
        foreach($fl as $k=>$v){//如果分类下面没有产品，就不会显示分类
            if(!M('No_credit')->where(array('token'=>$this->token,'fid'=>$v['id']))->find()){
                unset($fl[$k]);
            }
        }

        $cp=M('Credit')->where(array('token'=>$this->token))->getField('id');
        $this->assign('cp',$cp);
        $this->assign('fl',$fl);
        $this->assign('imgs',$imgs);
        $this->assign('foot',1);
        $this->UDisplay("pc_index");
    }
    //关于我们
    public function pc_gywm(){
        $this->token='55cad4ba46c41a8fde9c84274e36fa83';
        $list=M('Loan_pics')->where(array('token'=>$this->token))->select();
        $this->assign('list',$list);
        $this->UDisplay("pc_gywm");
    }
    //PC常见问题
    public function pc_cjwt(){
        $this->token='55cad4ba46c41a8fde9c84274e36fa83';
        // $this->token='5d8a87bab30de695954b17fc835b9d12';//本地

        $info=M('Classify')->where(array('token'=>$this->token,'name'=>'常见问题'))->getField('id');

       $info=M('Classify')->where(array('pid'=>$info))->order('sorts')->select();

        foreach($info as $k=>$v){
            $info[$k]['extend']=M('Img')->field('title,text')->where(array('classid'=>$v['id']))->select();
        }
       //p($info);
        $this->assign('info',$info);
        $this->UDisplay("pc_cjwt");
    }
    //预定协议
    public function agreement(){
        $this->UDisplay("agreement");
    }


}
/**
 * Class 这个类是做设置旅游时间有关的表
 */
class Calendar{
    protected $_table;//table表格
    protected $_currentDate;//当前日期
    protected $_year; //年
    protected $_month; //月
    protected $_days; //给定的月份应有的天数
    protected $_dayofweek;//给定月份的 1号 是星期几
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->_table="";
        $this->_year = isset($_GET["y"])?$_GET["y"]:date("Y");
        $this->_month = isset($_GET["m1"])?$_GET["m1"]:date("m");
        if ($this->_month>12){//处理出现月份大于12的情况
            $this->_month=1;
            $this->_year++;
        }
        if ($this->_month<1){//处理出现月份小于1的情况
            $this->_month=12;
            $this->_year--;
        }
        $yue=intval($this->_month);//把月分比如05变成5
        $this->_currentDate = '<span class="y">'.$this->_year.'</span>年<span class="m">'.$yue.'</span>月份';//当前得到的日期信息
        $this->_days = date("t",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份应有的天数
        $this->_dayofweek = date("w",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份的 1号 是星期几
    }
    /**
     * 输出标题和表头信息
     */
    protected function _showTitle()
    {
     //   $this->_table="<table style='width: 1000px;'><thead><tr align='center' ><th colspan='7' class='tou'>".$this->_currentDate."</th></tr ></thead>";

        $this->_table="<table id='ducalendar' border='0' cellspacing='0' cellpadding='0' >";
      //  $this->_table.="<tbody><tr >";
        $this->_table.="<tbody><tr class='ducalendar-week'>";
       //$this->_table .="<td style='color:red'>星期日</td>";
        $this->_table .="<td style='color:red'>日</td>";
        $this->_table .="<td>一</td>";
        $this->_table .="<td>二</td>";
        $this->_table .="<td>三</td>";
        $this->_table .="<td>四</td>";
        $this->_table .="<td>五</td>";
        $this->_table .="<td style='color:red'>六</td>";
        $this->_table.="</tr>";
    }
    /**
     * 输出日期信息
     * 根据当前日期输出日期信息
     */
    protected function _showDate($c='',$id='')
    {

        $shoufu=M('No_credit')->where(array('id'=>$id))->getField('shoufu');//得着付钱

        $yue=intval($this->_month);//把月分比如05变成5

        $y_m=$this->_year.'-'.$yue;
        /**
         * 重组数组，key=d,且不是这个月的去掉
         */
        //$k=0;
       // p($c);
        $No_credit_order_model=M('No_credit_order');//订单表
        foreach($c as $k=>$v){
            if($v['y_m']==$y_m){
                //     $k++;
                $c['_'.$v[d]]=$v;
                $c['_'.$v[d]]['date_time']=$v['y_m'].'-'.$v['d'];
                $c['_'.$v[d]]['people_num']=$No_credit_order_model->where(array('cid'=>$v['cid'],'type'=>1,'paystatus'=>array('in',array(0,1,5,12)),'date_time'=> $c['_'.$v[d]]['date_time']))->getField('sum(people_num)');
                $c['_'.$v[d]]['people_num1']=$No_credit_order_model->where(array('cid'=>$v['cid'],'type'=>1,'paystatus'=>array('in',array(0,1,5,12)),'date_time'=> $c['_'.$v[d]]['date_time'],'people_num'=>3))->count();



                $c['_'.$v[d]]['yu_num']=$c['_'.$v[d]]['num']-($c['_'.$v[d]]['people_num']-$c['_'.$v[d]]['people_num1']);
                unset($c[$k]);
                unset($c['_'.$v[d]]['people_num1']);
            }else{
                unset($c[$k]);
            }
        }
       // p($c);
     //   echo $c['_'.$v[d]]['yu_num'];

        $nums=$this->_dayofweek+1;
        $this->_table.="<tr class='ducalendar-days'>";
        for ($i=1;$i<=$this->_dayofweek;$i++){//输出1号之前的空白日期
            $this->_table.="<td> </td>";
        }
        //foreach($c as $v) {
        for ($i = 1; $i <= $this->_days; $i++) {//输出天数信息
            if ($nums % 7 == 0) {//换行处理：7个一行

                $this->_table .= "<td  ";
                if(array_key_exists("_".$i,$c)){
                    $this->_table .=" class='red' ";
                }

                $this->_table .=">$i";

                if(array_key_exists("_".$i,$c)){
                 //   $this->_table .="<p>￥$shoufu</p><p>共{$c['_'.$i]['num']}位,余{$c['_'.$i]['yu_num']}位</p>";
                    $this->_table .="<div class='day-info a'y_m='".$y_m."'d='".$i."'>¥$shoufu</div><div class='day-info'>余{$c['_'.$i]['yu_num']}</div>";
                }
                $this->_table .="</td></tr><tr class='ducalendar-days'>";

            } else {

                $this->_table .= "<td  ";
                if(array_key_exists("_".$i,$c)){
                    $this->_table .=" class='red' ";
                }

                $this->_table .=">$i";

                if(array_key_exists("_".$i,$c)){
                   // $this->_table .="￥$shoufu余{$c['_'.$i]['yu_num']}";
                    $this->_table .="<div class='day-info a'y_m='".$y_m."'d='".$i."'>¥$shoufu</div><div class='day-info'>余{$c['_'.$i]['yu_num']}</div>";
                }


                $this->_table .="</td>";


            }
            $nums++;
        }

        $this->_table.="</tbody></table>";
        //获取当前id
        $id=$_GET['id'];
        $this->_table.="<input type='hidden' name='last' flag='?g=Wap&m=Loan&a=no_credit_xiadan&id=".$id."&y=".($this->_year)."&m1=".($this->_month-1)."'>";
        //这里拼接自己的url地址
       // $this->assign('aa',77);
     //   $this->_table.="<h3><a href='?g=User&m=Loan&a=set_time&id=".$id."&y=".($this->_year)."&m1=".($this->_month-1)."'>上一月</a>   ";
     //   $this->_table.="<a href='?g=User&m=Loan&a=set_time&id=".$id."&y=".($this->_year)."&m1=".($this->_month+1)."'>下一月</a></h3>";
    }
    /**
     * 输出日历
     */
    public function showCalendar($b='',$id='')
    {
        $this->_showTitle();
        $this->_showDate($b,$id);
        return $this->_table;
    }

}
