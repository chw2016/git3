<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2015/8/20
 * Time: 14:06
 */
//include"/Lib/Action/Wap/Ynd.php";

class YndAction extends BaseAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/ynd';

    public function _initialize()
    {
        if(in_array(ACTION_NAME,array('usercontent','userregister'))){
            if(!IS_AJAX){
                $this->autoShare = true;
            }
        }

        parent::_initialize();
        Vendor('Ynd.Ynd');
    }

    /*微信个人相关信息*/
    public function wxinfo($openid){
        $wxGinfo = M('Wxuser')
            ->where(array('token'=>$this->token))
            ->find();
        $wxUinfo = M('Wxusers')
            ->where(array(
                'uid'=>$wxGinfo['id'],
                'openid'=>$openid))
            ->find();
        return $wxUinfo;
    }

    /*会员信息*/
    public function userinfo(){
        $userinfo = M('Ynd_user')
            ->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid))
            ->find();
        if($userinfo){
            switch($userinfo['status']){
                case -1:$userinfo['states'] = '未审核';break;
                case 1:$userinfo['states'] = '未激活';break;
                case 2:$userinfo['states'] = '正式会员';break;

            }
        }

        return $userinfo;
    }

    /*前端获取用户信息*/
    public function info(){
        // P($this->userinfo());
        $this->assign(array(
            'wxUinfo'     =>$this->wxinfo($this->openid),
            'userinfo'    =>$this->userinfo(),
        ));
    }

    /*状态判断*/
    public function is_user(){
        $info = $this->userinfo();
        if($info){
            if($info['status'] !=0){
                if($_GET['type'] == 'usercontent'){
                    return $this->ajaxReturn(array('status'=>1,'url'=>U('Ynd/usercontent',array('token'=>$this->token,'openid'=>$this->openid))));
                    //$this->success('',U('Ynd/usercontent',array('token'=>$this->token,'openid'=>$this->openid)));
                }elseif($_GET['type'] == 'addressselect'){
                    $this->success('',U('Ynd/addressselect',array('token'=>$this->token,'openid'=>$this->openid)));
                }

            }else{
                $this->error2('你注册的暂未通过审核，敬请等待...',U('Ynd/userregister',array('token'=>$this->token,'openid'=>$this->openid)));
            }
        }else{
            $this->error2('您还未成为会员，请注册...',U('Ynd/userregister',array('token'=>$this->token,'openid'=>$this->openid)));
        }
    }
    /*个人中心*/
    public function usercontent(){
      /*  $ynd = new Ynd();
        $info = $ynd->award('2','12',23,23,23,32,23,'9','34');
        /*$info = $ynd->getProductInf(454,543,array(
            array(12,32,21),
            array(13,43,48),
        ));
        P($info);exit;*/
        //if($this->userinfo()){
            $this->info();
            $this->UDisplay('usercontent');
       /* }else{
            $this->error2('您还未成为会员，请注册...',U('Ynd/userregister',array('token'=>$this->token,'openid'=>$this->openid)));
        }*/
    }

    /*添加日志*/
    public function log($info,$token,$openid){
        M('Ynd_recoads')->add(array(
            'token'=> $token,
            'openid' =>$openid,
            'info' => $info,
            'add_time' => date('Y-m-d H:i:s')
        ));
    }

    /*生成会员号*/
    public function vip_name(){
        $vip_name = abs(crc32(microtime(true).rand(100,999)));
        if (strlen($vip_name) < 10) {
            $vip_name = str_pad($vip_name, 10, '0', STR_PAD_RIGHT);
        }else{
            $vip_name = substr($vip_name, 0, 10);
        }
        return $vip_name;
    }

    /*个人资料*/
    public function userregister(){
        $info = $this->userinfo();
        if($info){
            $this->assign('op',1);
            $this->info();
        }else{
            $this->ajaxuser();
            $this->assign(array(
                'wxinfo'=>$this->wxinfo($this->openid),
                'code' =>$this->vip_name()
            ));
        }
        $this->UDisplay('userregister');

    }
//award($type, $order, $lq, $cp, $pay, $price, $pruduct_par1)
    public function ajaxuser(){
        $info = $this->userinfo();
        //P($info);
        if($info){
            if($info['status']==3){
                $_POST['status'] = -1;
            }
            if($info['password'] != $_POST['opassword'] ){
                $this->error('你输入的密码不正确，修改不成功！');
            }
            if(M('Ynd_user')
                ->where(array(
                    'token'=>$this->token,
                    'openid'=>$this->openid))
                ->save($_POST)){
                $this->log('修改个人资料',$this->token,$this->openid);
                $this->success('操作成功！');
            }else{
                $this->error('操作失败！');
            }
        }else{
            $uopenid = $_GET['uopenid'];
            if(!$uopenid){
                if(IS_AJAX){
                    $_POST['token'] = $this->token;
                    $_POST['openid'] = $this->openid;
                    $_POST['add_time'] = date('Y-m-d H:i:s');
                    $_POST['uname'] = $this->vip_name();
                    $_POST['status'] = -1;
                    $user_id = M('Ynd_user')->add($_POST);
                    if(M('Ynd_address')->add(array(
                        'token'     =>$this->token,
                        'openid'    =>$this->openid,
                        'user_id'   =>$user_id,
                        'address'   =>'现场提货',
                        'add_time' =>date('Y-m-d H:i:s')
                    ))){
                       
                        $this->log('成为会员',$this->token,$this->openid);
                        $this->success('提交成功，等待审核中');
                    }else{
                        $this->error('提交失败，请重新填写');
                    };
                }
            }else{
                $data['token'] = $this->token;
                $data['openid'] = $this->openid;
                $data['add_time'] = date('Y-m-d H:i:s');
                $data['uname'] = $this->vip_name();
                $data['uopenid'] = $uopenid;
                $_POST['status'] = 3;
                $user_id = M('Ynd_user')->add($data);
                if(M('Ynd_address')->add(array(
                    'token'     =>$this->token,
                    'openid'    =>$this->openid,
                    'user_id'   =>$user_id,
                    'address'   =>'现场提货',
                    'add_time' =>date('Y-m-d H:i:s')
                ))){
                    $this->log('成为会员',$this->token,$this->openid);
                    $this->success('提交成功，等待审核中');
                }else{
                    $this->error('提交失败，请重新填写');
                };
            }

        }
    }

    /*地址绑定*/
    public function addressbind(){
        $info = $this->userinfo();
        if(IS_AJAX){
            $_POST['token'] = $this->token;
            $_POST['openid'] = $this->openid;
            $_POST['add_time'] = date('Y-m-d H:i;s');
            $_POST['user_id'] = $info['id'];
            if($_POST['sign'] ==1){
                M('Ynd_address')->where(array('token'=>$this->token,'user_id'=>$info['id']))->save(array('sign'=>0));
            }
            if(M('Ynd_address')->add($_POST)){
                $this->success('添加成功！',U('Ynd/addressselect',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->UDisplay('addressbind');
        }
    }

    /*地址选择*/
    public function addressselect(){
        $info = $this->userinfo();
        if(IS_AJAX){
            $iTem = M('Ynd_address')->where(array('id'=>$_POST['id']))->find();
            if($iTem){
                if(M('Ynd_address')->where(array('token'=>$this->token,'user_id'=>$info['id']))->save(array('sign'=>0))!==false){
                    if( M('Ynd_address')->where(array('id'=>$_POST['id']))->save(array('sign'=>1))){
                        $this->success('操作成功',
                            U('Ynd/addressselect',array('token'=>$this->token,'oepnid'=>$this->openid)));
                    }else{
                        $this->error('选择失败');
                    }
                }else{
                    $this->error('系统有点忙');
                }
            }else{
                $this->error('非法操作');
            }
        }else{
            $list = M('Ynd_address')->where(array('token'=>$this->token,'user_id'=>$info['id']))->select();

            $this->assign(array(
                'info'=>$info,
                'list'  =>$list
            ));
            $this->UDisplay('addressselect');
        }
    }

    /*地址删除*/
    public function deladdress(){
        if(M('Ynd_address')->where(array('id'=>$_REQUEST['id']))->find()){
            if(M('Ynd_address')->where(array('id'=>$_REQUEST['id']))->delete()){
                $this->success('删除成功！');
            }else{
                $this->error('删除失败！');
            }
        }else{
            $this->error('非法操作！');
        }
    }

    /*银行卡的绑定*/
    public function band(){
        $info = $this->userinfo();
        $data = M('Ynd_bank')->where(array(
            'token'=>$this->token,
            'user_id'=>$info['id']
        ))->find();
        if(IS_AJAX){
            if($data){
                $_POST['mpassword']= $_POST['password'];
                $_POST['password'] = md5($_POST['password']);
                if(M('Ynd_bank')->where(array(
                    'token'=>$this->token,
                    'user_id'=>$info['id'],
                ))->save($_POST)){
                    $this->log('银行卡相关信息修改',$this->token,$this->openid);
                    $this->success('修改成功！');
                }else{
                    $this->error('修改失败！');
                }
            }else{
                $_POST['mpassword']= $_POST['password'];
                $_POST['password'] = md5($_POST['password']);
                $_POST['user_id'] = $info['id'];
                $_POST['token'] = $this->token;
                $_POST['openid'] = $this->openid;
                $_POST['add_time'] = date('Y-m-d H:i:s');
                if(M('Ynd_bank')->add($_POST)){
                    $this->log('银行卡相关信息绑定',$this->token,$this->openid);
                    $this->success('绑定成功');
                }else{
                    $this->error('绑定失败');
                }
            }
        }else{
            $this->assign(array(
                'data'=>$data
            ));
        }
        $this->UDisplay('band');
    }

    /*二维码页面*/
    public function QRCode(){
        $info = $this->userinfo();
        if(!$info['erimg']){
            $oUsercode = new Code($this->token,'275'.$info['id']);
            $img = $oUsercode->getYJCode();
            M('Ynd_user')->where(array('id'=>$info['id']))->save(array('erimg'=>$img));
        }
        $userinfo = $this->userinfo();
        $this->assign(array(
            'info'=>$userinfo,
            'wxuser' => $this->wxinfo($this->openid)
        ));
        $this->UDisplay('QRCode');
    }


    /*提现*/
    public function cue(){
        $info = $this->userinfo();
        $bank = M('Ynd_bank')->where(array('token'=>$this->token,'user_id'=>$info['id']))->find();
        if(IS_AJAX){
            if($info['password'] ==$_POST['password']){
                // echo $_POST['money']."||".$info['money'];exit;
                if($_POST['money'] > $info['money']){
                    $this->error('您钱包内的余额不足...');die;
                }
                if(M('Ynd_user')->where(array('id'=>$info['id']))->setDec('money',$_POST['money'])){
                    $data = array(
                        'token'=>$this->token,
                        'openid'=>$this->openid,
                        'user_id'=>$info['id'],
                        'money' =>$_POST['money'],
                        'type'=> 1,
                        'status' =>1,
                        'add_time' =>date('Y-m-d H:i:s')
                    );
                    if(M('Ynd_money')->add($data)){
                        $this->success('申请成功，等待审核...');
                    }else{
                        $this->error('系统繁忙，请稍后');
                    }
                }else{
                    $this->error('操作失败！');
                }

            }else{
                $this->error('您输入的密码不正确，请重新输入');
            }
        }
        $this->assign(array(
            'info'  => $info,
            'bank'  => $bank
        ));
        $this->UDisplay('cue');
    }

    /*寻回密码*/
    public function seachpassword(){
        $userinfo = $this->userinfo();
        if(IS_AJAX){
            if($userinfo){
                $code = rand(100000,999999);
                if(M('Ynd_user')
                    ->where(array('id'=>$userinfo['id']))
                    ->save(array('password'=>$code))){
                    //发送邮件
                    $body = "
                    您重新获取了一个新的密码，密码为：{$code}<br/>
                    提示：如若想重新修改密码，可以在‘个人资料’页，进行“修改密码”的操作！<br/>";
                    $emailfs=send_email("由你定安全密码找回",$body,$userinfo['email']);
                   if($emailfs){
                        $this->success('你的密码已发送至你的邮箱，请去你的邮箱查看！');
                    }else{
                        $this->error('系统繁忙，请稍后！');
                    }
                }    
            }
            
        }

    }



    /*充值*/
    public function recharge(){
        $info = $this->userinfo();
        //$infos = createOrderID();
        $bank = M('Ynd_bank')->where(array('token'=>$this->token,'user_id'=>$info['id']))->find();
        if(IS_AJAX){
            $orderID = createOrderID();
            if($info['password'] ==$_POST['password']){
                if($_POST['type'] == 1){
                    return $this->ajaxReturn(array('status'=>1,'info'=>'微信正在支付...','infoes'=>$orderID));exit;
                }
            }else{
                $this->error('您输入的密码不正确，请重新输入');
            }
        }
        $this->UDisplay('recharge');
    }



    /*商品分类*/
    public function productClassfiy(){
        $where = array(
            'token'=>$this->token,
            'parentid' =>array("in","'',0"),
            'cattype' =>3
        );
        $classfiy = M('Product_cat_new')
            ->where($where)
            ->select();
        foreach($classfiy as $key=>$val){
            $classfiy[$key]['dclassfiy'] = M('Product_cat_new')
                ->where(array('token'=>$this->token,'parentid'=>$val['id']))->select();
        }
        // P($classfiy);exit;
        return $classfiy;
    }

    /*商品分类对应的商品*/
    public function productList($cat_id){
        $list  = M('Ynd_product')
            ->where(array(
                'token'     =>$this->token,
                'cat_id'    =>$cat_id,
                'status'    =>1
            ))->order("sorts,id desc")
            ->select();
        return $list;
    }

    /*对应的商品*/
    public function product($pro_id){
        $info = M('Ynd_product')
            ->where(array(
                'id'=>$pro_id
            ))->find();
        return $info;
    }


    /*商城首页*/
    public function index(){
        $info = $this->userinfo();
        if(!$info){
            $this->error2('你现在还未注册我们的平台，请去注册！',U('userregister',array('token'=>$this->token,'openid'=>$this->openid)));exit;
        }
        $classfiy = $this->productClassfiy();
        $cat_id = $_REQUEST['cat_id'];
        if($cat_id){
            $list = $this->productList($cat_id);
        }else{
            $list = $this->productList($classfiy[0]['dclassfiy'][0]['id']);
        }
        $oImgModel = M('Imag');
        $phone = $oImgModel
            ->where(array(
                'token'=>$this->token,
                'app'=>'Ynd'
            ))->select();
       /* P($phone);exit;*/
        $this->assign(array(
            'classfiy' => $classfiy,
            'list'     =>  $list,
            'phone'=>$phone
        ));
        $this->UDisplay('index');
    }

    /*商城搜索结果页*/
    public function result(){
        if($_GET['seach']){
            $aWhere['name'] = array('like','%'.$_GET['seach'].'%');
        }
        $aWhere['token'] = $this->token;
        $list = M('Ynd_product')->where($aWhere)->select();
        $this->assign(array(
            'list'=>$list,
            'seach'=>$_GET['seach']
        ));
        $this->UDisplay('result');
    }


    /*商品详情页*/
    public function shop(){
        $info = $this->userinfo();
        $pro_id = $_REQUEST['pro_id'];
       // P($this->evaluate($pro_id));
        $this->assign(array(
            'info'      => $info,
            'product'   => $this->product($pro_id),
            'evaluate'  => $this->evaluate($pro_id)
        ));
        $this->UDisplay('shop');
    }


    /*评价记录*/
    public function evaluate($tid){
        $evaluate = M('Ynd_evaluate')
            ->where(array(
                'token'=>$this->token,
                'tid'=>$tid))
            ->order('add_time desc')
            ->select();
        foreach($evaluate as $key=>$val){
            $userinfo = $this->wxinfo($val['openid']);
            $evaluate[$key]['userinfo'] = $userinfo;
        }
        return $evaluate;
    }

    /*收货地址*/
    public function addressinfo(){
        $address = M('Ynd_address')
            ->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid,
                'sign' =>1
            ))->find();
        return $address;
    }


    /*
     * 购买页
     * 此处需要调用第三方接口
    */
    public function buy_shop(){
        $pro_id = $_GET['pro_id'];
        $type = $_GET['type'];
        $fand = M('Ynd_fangruler')->find();
        $this->assign('fand',$fand['num']);
        $this->assign(array(
            'info' =>$this->userinfo(),         //用户信息
            'product'=> $this->product($pro_id),     //商品信息
            'address' => $this->addressinfo(),
            'type' =>$type
        ));
        $this->UDisplay('buy_shop');
    }


    /*
     *购买操作
     * 此处调用第三方接口
     * */
    public function buy(){
        $pro_id = $_GET['pro_id'];
        $info = $this->userinfo();          //用户信息
        $product =  $this->product($pro_id);
        $aWhere['token'] = $this->token;
        $aWhere['tid'] = $pro_id;
        if($product['rule']==1){
            $shoplist = M('Ynd_fangdan')->where($aWhere)->order('exponent','add_time desc')->select();
        }elseif($product['rule']==2){
            $shoplist = M('Ynd_fangdan')->where($aWhere)->order('add_time desc')->select();
        }
        $data['yuid'] = $info['id'];
        //$data['pro_id'] = $pro_id;
        $data['token'] = $this->token;
        $data['openid'] = $this->openid;
        $data['money']  = floatval($_POST['money']);
        $data['num'] = intval($_POST['num']);
        $data['LQ'] = intval($_POST['LQ']);
        $data['CQ'] = intval($_POST['CQ']);
        $data['address_id'] = $_POST['address_id'];
        $data['way'] = $_POST['way'];
        $data['add_time'] = date('Y-m-d H:i:s');
        $num = $_POST['num'];

        if($num > $product['num']){
            $this->error('您此次购买的数量超出了库房的存量，请重新购选');
        }

        if($data['LQ'] > $info['LQ'] ||$data['CQ'] > $info['CQ']){
            $this->error('您的LQ币或CQ币暂时不够');
        }
        /*if($_POST['money'] > $info['money']){
            $this->error('您的LQ币或CQ币暂时不够');
        }*/
        if(!$info || $info['status']==0){
           $this->error('您没有权限购买');
        }
        if($oid = M('Ynd_order')->add($data)){

            if($info['status'] ==1){//查看用户是不是未激活的会员
                M('Ynd_user')->where(array('id'=>$info['id']))->save(array('status'=>2));
            }
            $Qsave = array(
                'LQ'=>$info['LQ'] - $_POST['LQ'],
                'CQ'=>$info['CQ'] - $_POST['CQ']
            );

            M('Ynd_user')->where(array('id'=>$info['id']))->save($Qsave);  //扣除Q币  $this->addrecord($this->token,$info['openid'],$info['user_id'],'放单',$data['money'],'money',0);
            $this->addrecord($this->token,$this->openid,$info['user_id'],'商品购买',$_POST['LQ'],'LQ',1);
            $this->addrecord($this->token,$this->openid,$info['user_id'],'商品购买',$_POST['CQ'],'CQ',1);
            if($ocid = M('Ynd_orderinfo')
                ->add(array(
                    'oid'=>$oid,
                    'pro_id'=>$pro_id,
                    'num'=> $_POST['num'],
                ))){
                $sdata = array();
                $fdnum = '';
                foreach($shoplist as $key=>$val){
                    $fdnum += $val['num'];
                }
                if($fdnum < $num){
                    $dunm = $num - $fdnum;
                    M('Ynd_product')
                        ->where(array('id'=>$pro_id))
                        ->setDec('sunnum',$dunm);
                    $sdata['a'] = $dunm;
                    $num = $fdnum;
                }
                /*按对应的规则进行放单人的清算*/
                for($i=0;$i<=count($shoplist);$i++){
                    if($num>0&&$shoplist[$i]['num']>0){
                        if($num>$shoplist[$i]['num']){
                            $wfnum = $shoplist[$i]['num'];
                        }else{
                            $wfnum = $num;
                        }
                        $sdata[$shoplist[$i]['id']] = $wfnum;
                        M('Ynd_fangdan')->where(array('id'=>$shoplist[$i]['id']))->setDec('num',$wfnum);
                        $num = $num - $shoplist[$i]['num'];
                    }
                }
                $var['user_id'] = json_encode($sdata);
                M('Ynd_orderinfo')->where(array('id'=>$ocid))->save($var);
                M('Ynd_product')
                    ->where(array('id'=>$pro_id))
                    ->save(array(
                        //'sunnum'=> $product['sunnum'] - $_POST['num'],
                        'num'=>$product['num'] - $_POST['num']
                    ));
               // M('Ynd_user')->where(array('id'=>$info['id']))->save();
                echo $this->ajaxReturn(array('status'=>2,'info'=>'购买成功，请关注！'));exit;
            }
        }
    }

    /*
     * 放单操作
     * */
    public function fangdan(){
        $info = $this->userinfo();
        $pro_id = $_GET['pro_id'];
        $product =  $this->product($pro_id);
        if(IS_AJAX){
            $data['yuid'] = $info['id'];
            $data['tid'] = $pro_id;
            $data['token'] = $this->token;
            $data['openid'] = $this->openid;
            $data['num'] = intval($_POST['num']);
            $data['money'] = floatval($_POST['money']);
            $data['fprice'] =floatval($_POST['wfprice']) ;
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['SLQ'] = intval($_POST['LQ']);
            $data['SCQ'] = intval($_POST['CQ']);
            $data['sumnum'] = intval($_POST['num']);
            $data['exponent'] = ((floatval($_POST['wfprice'])*intval($_POST['num']))/floatval($_POST['money']))*10000;
            if($_POST['password'] != $info['password']){
                $this->error('您输入的安全密码有误！');
            }
            if($data['num']>$product['num']){
                $this->error('您放单的数量已超出总部的库存量，放单失败！');
            }
            if(1 == $_POST['way']){
                if($_POST['money'] > $info['money']){
                    $this->error('您钱包的余额不足！');
                }
                if($data['SLQ'] > $info['LQ'] || $data['SCQ'] > $info['CQ']){
                    $this->error('您的LQ币或CQ币暂时不够');
                }

                $udata = array(
                    'num' =>$info['num'] - $data['num'],
                    'money' =>$info['money'] -$data['money'],
                    'LQ' => $info['LQ'] - $data['SLQ'],
                    'CQ' =>$info['CQ'] -$data['SLQ'],
                );

                if(M('Ynd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($udata)){
                    $data['status'] = 1;
                    if(M('Ynd_fangdan')->add($data)){
                        $this->addrecord($this->token,$info['openid'],$info['user_id'],'放单',$data['money'],'money',0);
                        M('Ynd_product')->where(
                            array('id'=>$pro_id))
                            ->save(array(
                               // 'num'=>$product['num']-$data['num'],
                                'sunnum'=>$product['sunnum']-$data['num'],
                                'fnum'=>$product['fnum']+$data['num']
                            ));
                        $this->success('放单成功！');
                    }else{
                        $this->error('放单失败');
                    }
                }
            }elseif(2 == $_POST['way']){//微信支付
                $orderID = createOrderID();
                $data['status'] = 0;
                $data['payorderID'] = $orderID;
                 if(M('Ynd_fangdan')->add($data)){
                     M('Ynd_product')->where(
                         array('id'=>$pro_id))
                         ->save(array(
                             //'num'=>$product['num']-$data['num'],
                             'sunnum'=>$product['sunnum']-$data['num'],
                             'fnum'=>$product['fnum']+$data['num']
                         ));
                    echo $this->ajaxReturn(array('status'=>1,'info'=>'购买成功，请关注！','wayes'=>2,'infoes'=>$orderID));exit;
                }else{
                    $this->error('放单失败');
                }


            }elseif(3 == $_POST['way']){//支付宝支付
                $aliPay = '';//支付宝支付的方法
                if($aliPay){
                    $udata = array(
                        'num' =>$info['num'] - $data['num'],
                        'LQ' => $info['LQ'] - $data['SLQ'],
                        'CQ' =>$info['CQ'] -$data['SCQ'],
                    );
                    if(M('Ynd_user')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($udata)){
                        if(M('Ynd_fangdan')->add($data)){
                             //调用接口，分配给放单人Q币；
                            $fanginfo = new Ynd();
                            // $yndcode->award(1, 1, 1, 1, 1, 1, 1);
                           // $fangqc = $fanginfo->award();
                            $this->success('放单成功！');
                        }else{
                            $this->error('放单失败');
                        }
                    }
                }
            }
        }
    }

    /*购买支付*/
    public function pay(){
        $order_id = $_GET['order_id'];
        $userinfo = $this->userinfo();
        $type = $_GET['type'];
        if( $type == 1){
            if($_POST['password'] != $userinfo['password']){
                $this->error('安全密码不正确，请重新确认！');exit;
            }
            if ($_POST['money'] < $userinfo['money']) {
                $data = array(
                    'money'=> $userinfo['money']-$_POST['money'],
                );
                if(M('Ynd_user')
                    ->where(array('id'=>$userinfo['id']))->save($data)!==false){
                    //订单状态修改
                    M('Ynd_order')->where(array(
                        'id'=>$order_id
                    ))->save(array(
                        'pay_time'=>date('Y-m-d H:i:s'),
                        'pay_status'=>1
                    ));
                    //生成日志记录  ($userid,$info,$money,$type,$rank){
                    $this->addrecord($this->token,$this->openid,$userinfo['id'],'商品购买',$_POST['money'],'money',1);

                    $orid = M('Ynd_orderinfo')->where(array(
                        'oid'=>$order_id
                    ))->select();
                    foreach($orid as $key=>$val){
                        $this->balance($val['id']);
                    }
                    M('Ynd_order')->where(array(
                        'id'=>$order_id
                    ))->save(array(
                        'balance_time'=>date('Y-m-d H:i:s'),
                        'is_balance'=>1
                    ));
                    $this->success('支付成功！');
                }else{
                    $this->error('支付失败');
                }
            }
        }elseif($type == 3) {
            if ($_POST['money'] ==0) {
                $data = array(
                    'money'=> $userinfo['money']-$_POST['money'],
                );
                if(M('Ynd_user')
                        ->where(array('id'=>$userinfo['id']))->save($data)!==false){
                    //订单状态修改
                    M('Ynd_order')->where(array(
                        'id'=>$order_id
                    ))->save(array(
                        'pay_time'=>date('Y-m-d H:i:s'),
                        'pay_status'=>1
                    ));
                    //生成日志记录  ($userid,$info,$money,$type,$rank){
                    $this->addrecord($this->token,$this->openid,$userinfo['id'],'商品购买',$_POST['money'],'money',1);

                    $orid = M('Ynd_orderinfo')->where(array(
                        'oid'=>$order_id
                    ))->select();
                    foreach($orid as $key=>$val){
                        $this->balance($val['id']);
                    }
                    M('Ynd_order')->where(array(
                        'id'=>$order_id
                    ))->save(array(
                        'balance_time'=>date('Y-m-d H:i:s'),
                        'is_balance'=>1
                    ));
                    $this->success('支付成功！');
                }else{
                    $this->error('支付失败');
                }
            }else{
                $orderID = createOrderID();
                $data['payorderID'] = $orderID;
                if( M('Ynd_order')->where(array(
                        'id' => $order_id
                    ))->save($data) !==false){
                    echo $this->ajaxReturn(array('status'=>1,'info'=>'购买中，请关注！','wayes'=>2,'infoes'=>$orderID));exit;
                }else{
                    $this->error('支付失败');
                }
            }

            //订单状态修改

            /* //生成日志记录  ($userid,$info,$money,$type,$rank){
             $this->addrecord($this->token,$this->openid,$userinfo['id'],'商品购买',$_POST['money'],'money',1);
             $this->addrecord($this->token,$this->openid,$userinfo['id'],'商品购买',$_POST['LQ'],'LQ',1);
             $this->addrecord($this->token,$this->openid,$userinfo['id'],'商品购买',$_POST['CQ'],'CQ',1);

             $orid = M('Ynd_orderinfo')->where(array(
                 'oid'=>$order_id
             ))->select();
             foreach($orid as $key=>$val){
                 $this->balance($val['id']);
             }
             M('Ynd_order')->where(array(
                 'id'=>$order_id
             ))->save(array(
                 'balance_time'=>date('Y-m-d H:i:s'),
                 'is_balance'=>1
             ));

             $this->success('支付成功！');
         }else{
             $this->error('支付失败');
         }*/
        }

    }

    /*购买结算*/
    public function balance($ocid){
        $orinfo = M('Ynd_orderinfo')->where(array('id'=>$ocid))->find();
        $userinfo = $orinfo['user_id'];
        $userinfo = json_decode($userinfo,ture);
        //array('pro_id'=>'num')
        $arr1 = array_keys($userinfo);
        $arr2 = array_values($userinfo);
       // P($arr1);exit;
        for ($i=0; $i < count($arr1) ; $i++) {
            if($arr1[$i] != "a"){
                $info = $this->fangdaninfo($arr1[$i]);
                /*结算加入*///在tp_ynd_orderinfo 中加一个status  默认为0；
                $money = $info['price']*$arr2[$i];
                // M('Ynd_user')->where(array('id'=>$info['user_id']))->setInc('money',$arr2[$i]);
                $userinfoes = M('Ynd_user')->where(array('id'=>$info['user_id']))->find();
                M('Ynd_user')->where(array('id'=>$info['user_id']))->setInc('money',$money);
                //生成日志记录
                $this->addrecord($this->token,$userinfoes['openid'],$info['user_id'],'放单结算',$money,'money',0);
            }

        }
       // M('Ynd_orderinfo')->where(array('id'=>$ocid))->save(array('status'=>1));
            //$info = $this->fangdaninfo();
    }

    /*查询放单的记录*/
    public function fangdaninfo($fid){
        $info = M('Ynd_fangdan')
            ->where(array('id'=>$fid))
            ->find();
        return $info;
    }
    public function fangdaninfo(){
        
    }

    /*
     * 购物车页
     * */
    public function shoping(){

        $list = M('Ynd_shop')
            ->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid,
                'status'=>0
            ))->order('id desc')->select();

        $iLQ = $iCQ = 0;
        foreach($list as $key=>$val){
            $list[$key]['shopinfo'] = $this->product($val['pro_id']);
            $iLQ += $val['num'] * $list[$key]['shopinfo']['LQ'];
            $iCQ += $val['num'] * $list[$key]['shopinfo']['CQ'];
            $list[$key]['sLQ'] = $val['num'] * $list[$key]['shopinfo']['LQ'];
            $list[$key]['sCQ'] = $val['num'] * $list[$key]['shopinfo']['CQ'];
        }

        $info = $this->userinfo();
        $address = M('Ynd_address')->where(array('token'=>$this->token,'user_id'=>$info['id'],'sign'=>1))->find();
        $this->assign(array(
            'list'=>$list,
            'address'=>$address
        ));
        $this->UDisplay('shoping');
    }


    /*删除购物车内的物品*/
    public function del_shop(){
        $iTem = M('Ynd_shop')->where(array('id'=>$_POST['ysid']))->find();
        if($iTem){
            if(M('Ynd_shop')->where(array('id'=>$_POST['ysid']))->delete()){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('非法操作');
        }
    }

    /*
     * 加入购物车
     * */
    public function inshoping(){
        $info = $this->userinfo();
        if(IS_AJAX){
            $_POST['token'] = $this->token;
            $_POST['openid'] = $this->openid;
            $_POST['yuid'] = $info['id'];

            $por_id =  $_POST['pro_id'];
             $lidji = M('Ynd_shop')->where(array(
             'token'=>$this->token,
                 'openid'=>$this->openid,
                     'pro_id'=>$por_id
                 )
             )->find();
            if($lidji){
                $num = $lidji['num'] + $_POST['num'];
                $_POSTP['token'] = $this->token;
                $_POSTP['openid'] = $this->openid;
                $_POSTP['yuid'] = $info['id'];
                $_POSTP['num'] = $num;
                if(M('Ynd_shop')->where(array(
                    'token'=>$this->token,
                    'openid'=>$this->openid,
                    'pro_id'=>$por_id
                ))->save($_POSTP)){
                    $this->success('加入购物车成功');
                }else{
                    $this->error('加入购物车失败');
                }
            }else{
                if ($info) {
                    if (M('Ynd_shop')->add($_POST)) {
                        $this->success('加入购物车成功');
                    } else {
                        $this->error('加入购物车失败');
                    }
                }
            }
        }
    }

    /*购物车订购*/
    public function shoporder(){
        $info = $this->userinfo();
        if($info){
            if(IS_AJAX){

                $data['yuid'] = $info['id'];
                //$data['pro_id'] = $pro_id;
                $data['token'] = $this->token;
                $data['openid'] = $this->openid;
                $data['money']  = floatval($_POST['menoy']);
                //$data['num'] = $_POST['num'];
                $data['LQ'] = intval($_POST['LQ']);
                $data['CQ'] = intval($_POST['CQ']);
                $data['address_id'] = M('Ynd_address')->where(array('token'=>$this->token,'user_id'=>$info['id'],'sign'=>1))->getField('id');
                $data['way'] = $_POST['way'];
                $data['add_time'] = date('Y-m-d H:i:s');

                $pro_id = $_POST['pro_id'];
                $pro_id = explode(',',$pro_id);
                $lastpro = end($pro_id);
                $num = $_POST['num'];
                $num = explode(',',$num);
                $shop_id = $_POST['shop_id'];
                $shop_id = explode(',',$shop_id);
                if(!$lastpro){
                    array_pop($pro_id);
                    array_pop($num);
                    array_pop($shop_id);
                }

                /*for($i=0;$i<count($pro_id);$i++){
                    $products = $this->product($pro_id[$i]);
                    if($products['sunnum'] < $num[$i]){

                        $this->error('');
                    }
                }*/

                if($data['LQ'] > $info['LQ'] || $data['CQ'] > $info['CQ']){
                    $this->error('您的LQ币或CQ币暂时不够');
                }
                if($oid = M('Ynd_order')->add($data)){
                    $Qsave = array(
                        'LQ'=>$info['LQ'] - $_POST['LQ'],
                        'CQ'=>$info['CQ'] - $_POST['CQ']
                    );
                    M('Ynd_user')->where(array('id'=>$info['id']))->save($Qsave);  //扣除Q币  $this->addrecord($this->token,$info['openid'],$info['user_id'],'放单',$data['money'],'money',0);
                    $this->addrecord($this->token,$this->openid,$info['user_id'],'商品购买',$_POST['LQ'],'LQ',1);
                    $this->addrecord($this->token,$this->openid,$info['user_id'],'商品购买',$_POST['CQ'],'CQ',1);
                    for($i=0;$i<count($pro_id);$i++){
                        if($ocid = M('Ynd_orderinfo')->add(array('oid'=>$oid,'pro_id'=>$pro_id[$i],'num'=>$num[$i]))){
                            $this->orderuser($ocid,$pro_id[$i],$num[$i]);
                            M('Ynd_shop')->where(array('id'=>$shop_id[$i]))->delete();
                        }else{
                            M('Ynd_order')->where(array('id'=>$oid))->delete();
                            $this->error('系统原因，购买失败！');
                        }
                    }
                    $this->success('订购成功');
                }else{
                    $this->error('操作失败！');
                }
            }

        }
    }

    /*单个产品的来源分配*/
    public function orderuser($ocid,$pro_id,$num){
        $product =  $this->product($pro_id);
        $aWhere['token'] = $this->token;
        $aWhere['tid'] = $pro_id;
        if($product['rule']==1){
            $shoplist = M('Ynd_fangdan')->where($aWhere)->order('exponent','add_time desc')->select();
        }elseif($product['rule']==2){
            $shoplist = M('Ynd_fangdan')->where($aWhere)->order('add_time desc')->select();
        }
        $sdata = array();
        $fdnum = '';
        foreach($shoplist as $key=>$val){
            $fdnum += $val['num'];
        }
        if($fdnum < $num){
            $dunm = $num - $fdnum;
            M('Ynd_product')
                ->where(array('id'=>$pro_id))
                ->setDec('sunnum',$dunm);
            $sdata['a'] = $dunm;
            $num = $fdnum;
        }
        for($i=0;$i<=count($shoplist);$i++){
            if($num>0&&$shoplist[$i]['num']>0){
                if($num>$shoplist[$i]['num']){
                    $wfnum = $shoplist[$i]['num'];
                }else{
                    $wfnum = $num;
                }
                $sdata[$shoplist[$i]['id']] = $wfnum;
                M('Ynd_fangdan')->where(array('id'=>$shoplist[$i]['id']))->setDec('num',$wfnum);
                $num = $num - $shoplist[$i]['num'];
            }
        }
        $var['user_id'] = json_encode($sdata);
        M('Ynd_orderinfo')->where(array('id'=>$ocid))->save($var);
        M('Ynd_product')
            ->where(array('id'=>$pro_id))
            ->save(array(
                'num'=> $product['num'] - $num,
            ));
       // echo $this->ajaxReturn(array('status'=>2,'info'=>'购买成功，请关注！'));exit;

    }

    /*订单详情*/
    public function orderinfo($order_id){
        $info = M('Ynd_order')
            ->where(array(
                'id'=>$order_id
            ))->find();
        $info['details'] = M('Ynd_orderinfo')
            ->where(array(
                'oid'=>$order_id
            ))->select();
        $info['log_name'] = M('Kuaizhao')->where(array('id'=>$info['log_id']))->getField('company');
        foreach($info['details'] as $key=>$val){
            $info['details'][$key]['produnct'] = M('Ynd_product')
                ->where(array(
                    'id'=>$val['pro_id']
                ))->find();
        }
        return $info;
    }




    /*订单记录详情页*/
    public function recordinfo(){
        $order_id = $_GET['order_id'];
        $userinfo = $this->userinfo();
        $address =$this->addressinfo();
     //P($this->orderinfo($order_id));
        $this->assign(array(
            'userinfo'=>$userinfo,
            'address' =>$address,
            'orderinfo'=>$this->orderinfo($order_id)
        ));
        $this->UDisplay('recordinfo');
    }




    /*
     * 物流系统 （对接快找100）
     * */
    public function logistics(){
        $this->UDisplay('logistics');
    }

    /*
     * 评价页面
     * */
    public function evaluateinfo(){
        $pro_id = $_GET['pro_id'];
        $product = $this->product($pro_id);
        $this->assign(array(
            'product'=>$product
        ));
        $this->UDisplay('evaluateinfo');
    }

    /*
     * 评价操作
     * */
    public function ajaxevaluate(){
        $info = $this->userinfo();
        if(IS_AJAX){
            $_POST['token'] = $this->token;
            $_POST['openid'] = $this->openid;
            $_POST['add_time'] = date('Y-m-d H:i:s');
            $_POST['tid'] = $_GET['pro_id'];
            if(M('Ynd_evaluate')->add($_POST)){
                $this->success('评价成功',U('Ynd/record',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error('评价失败！');
            }
        }
    }

    /*评价列表页*/
    public function evaluatelist($tid){
        $list = M('Ynd_evaluate')
            ->where(array(
                'token'=>$this->token,
                'tid' =>$tid
            ))->order('add_time desc')->select();
        foreach($list as $key=>$val){
            $list[$key]['wxinfo'] = $this->wxinfo($val['openid']);
        }
        return $list;
    }

    /*
     * 确认收货
     * */
    public function take_delivery(){
        if(IS_AJAX){
            $order_id = $_GET['order_id'];
            if($this->orderinfo($order_id)){
                if(M('Ynd_order')->where(array(
                    'id'=>$order_id
                ))->save(array(
                    'take_time'=>date('Y-m-d H:i:s'),
                    'is_take'=>1))){
                    $this->success('成功！');
                }else{
                    $this->error('失败！');
                }
            }else{
                $this->error('非法操作！');
            }
        }
        //$this->UDisplay('take_delivery');
    }


    /*订单商品查询*/
    public function productslist($oid){
        $list = M('Ynd_orderinfo')
            ->where(array('oid'=>$oid))
            ->select();
        return $list;
    }

    /*购买记录页*/
    public function record(){
        $type = $_GET['type'];
        if(1==$type){
            $aWhere['pay_status'] = 0;
        }elseif(2==$type){
            $aWhere['is_take'] = 0;
        }elseif(3==$type){
            $aWhere['is_evaluate'] = 0;
        }
        $aWhere['token'] = $this->token;
        $aWhere['openid'] = $this->openid;

        $orderlist = M('Ynd_order')->where($aWhere)->order('add_time desc')->select();
        foreach($orderlist as $key=>$val){
            $orderlist[$key]['info'] = $this->productslist($val['id']);
            $shop = $orderlist[$key]['info'];
            foreach($orderlist[$key]['info'] as $k=>$valuey){
                $orderlist[$key]['info'][$k]['shop'] = $this->product($valuey['pro_id']);
            }
        }
        $this->assign(array(
            'list'=>$orderlist
        ));
        //P($orderlist);
        $this->UDisplay('record');
    }

    /*放单记录页*/
    public function fRecord(){
        $type = $_GET['type'];
        $aWhere['token'] = $this->token;
        $aWhere['openid'] = $this->openid;
        if(1 == $type){
            $aWhere['num'] = array('neq',0);
        }elseif(2 == $type){
            $aWhere['num'] = array('eq',0);
        }
        $list = M('Ynd_fangdan')->where($aWhere)->order('id desc')->select();
        foreach($list as $key=>$val){
            $list[$key]['info'] = $this->product($val['tid']);
        }

        $this->assign(array(
            'list'=>$list
        ));
        $this->UDisplay('fRecord');
    }



    /*我的钱包记录*/
    public function mRecord(){
        $type = $_GET['type'];
        $list = M('Ynd_record')
            ->where(array(
                'token'=>$this->token,
                'openid'=>$this->openid,
                'type' =>$type
            ))->order('add_time desc')
            ->select();
        $this->assign(array(
           'list'=>$list
        ));
        $this->UDisplay('mRecord');
    }



    /*生成钱包记录*/
    function addrecord($token,$openid,$userid,$info,$money,$type,$rank){
        M('Ynd_record')->add(array(
            'token'=>$token,
            'openid'=>$openid,
            'user_id'=>$userid,
            'info'=>$info,
            'content'=>$money,
            'rank' =>$rank,
            'type' => $type,
            'add_time'=>date('Y-m-d H:i:s')
        ));
    }






}