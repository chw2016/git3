<?php
/**
 * Created by PhpStorm.
 * User: 訾超
 * Date: 2014/11/21
 * Time: 15:58
 */
class DistributionAction extends UserAction{

    public function _initialize(){
        parent::_initialize();
        if (session ( 'zm_id' ) == $_GET ['zm_id']) {
            $zm_id = session ( 'zm_id' );
            $this->assign ( 'zm_id', $zm_id );
        } elseif (session ( 'zm_id' ) !== $_GET ['zm_id']) {
            $this->redirect ( 'User/Branch/index',array('token'=>$this->token,'modulename'=>'Homenice_zm'));
        }
    }
    public function index(){
        $this->display();
    }
    public function zonemanager(){
        $zonemanagers = M('Homenice_zm')->where(array('token'=>$this->token))->select();
        $this->assign('zonemanagers',$zonemanagers);
        $this->display();
    }
    public function editzonemanager(){
        $this->assign('get',$_GET);
        $op = $_GET['op']?$_GET['op']:0;
        if($op == 1){
            $zonemanager = M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_GET['id']))->find();
            $this->assign('zonemanager',$zonemanager);
        }
        $this->assign('op',$op);
        if(IS_POST){
            if($_POST['loc_city'] == "地级市"){
                $_POST['loc_city'] = '';
                $_POST['loc_town'] = '';
                $_POST['loc_city_id'] = 0;
                $_POST['loc_town_id'] = 0;
            }
            if($_POST['loc_town'] == '市、县、区'){
                $_POST['loc_town'] = '';
                $_POST['loc_town_id'] = 0;
            }

            $op = $_POST['op']?$_POST['op']:0;
            $_POST['token'] = $this->token;
            $_POST['pwd'] = $_POST['password'];
            $_POST['password'] = md5($_POST['password']);
            //print_r($_POST);exit;
            if($op == 1){
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                //print_r($_POST);exit;
                if(M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_POST['id']))->save($_POST)){
                    $this->ajaxReturn(array('info'=>'编辑成功','status'=>1,'url'=>'index.php?&g=User&m=Distribution&a=zonemanager&token='.$this->token));
                }else{
                    $this->ajaxReturn(array('info'=>'编辑失败','status'=>0));
                }
            }
            elseif($op == 0){
                if(M('Homenice_zm')->where(array('token'=>$this->token,'loc_province'=>$_POST['loc_province'],'loc_city'=>$_POST['loc_city'],'loc_town'=>$_POST['loc_town']))->find()){
                    exit(json_encode(array('info'=>'此区域已有管理员','status'=>-1)));
                }
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if(M('Homenice_zm')->add($_POST)){
                    $this->ajaxReturn(array('info'=>'保存成功','status'=>1,'url'=>'index.php?&g=User&m=Distribution&a=zonemanager&token='.$this->token));
                }else{
                    $this->ajaxReturn(array('info'=>'保存失败','status'=>0));
                }
            }
        }
        $this->display();
    }
    public function del_zonemanager(){
        if(IS_POST){
            if(M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_GET['id']))->find()){
                if(M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_GET['id']))->delete()){
                    $this->ajaxReturn(array('info'=>'删除成功！','status'=>1,'url'=>'index.php?&g=User&m=Distribution&a=zonemanager&token='.$this->token));
                }else{
                    $this->ajaxReturn(array('info'=>'删除失败！','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'非法操作！','status'=>-1));
            }
        }
    }

    //微商列表
    public function userlist(){
        $data['phone'] = trim($_POST['phone']);
        $data['name'] = trim($_POST['name']);
        $data['loc_province_id'] = trim($_POST['loc_province']);
        $data['loc_city_id'] = trim($_POST['loc_city']);
        $data['loc_town_id'] = trim($_POST['loc_town']);
        foreach($data as $k => $v){
            if($v !== ''){
                $w[$k] = $v;
            }
        }
        $this->assign('w',$w);
        if($w['loc_province'] !==''){
            $w['loc_province'] = array('like','%'.$w['loc_province'].'%');
        }
        $zm_id = session('zm_id');
        $w['token'] = $this->token;
        if(isset($zm_id)){
            $zonemanager = M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_GET['zm_id']))->find();
            if($zonemanager['loc_city'] == ''){
                $w['loc_province'] = $zonemanager['loc_province'];
                $userList = M('Homenice_user')->where($w)->select();
            }elseif($zonemanager['loc_city'] !== '' && $zonemanager['loc_town'] == ''){
                $w['loc_province'] = $zonemanager['loc_province'];
                $w['loc_city'] = $zonemanager['loc_city'];
                $userList = M('Homenice_user')->where($w)->select();
            }elseif($zonemanager['loc_city'] !== '' && $zonemanager['loc_town'] !== ''){
                $w['loc_province'] = $zonemanager['loc_province'];
                $w['loc_city'] = $zonemanager['loc_city'];
                $w['loc_town'] = $zonemanager['loc_town'];
                $userList = M('Homenice_user')->where($w)->select();
            }
        }else{
            $userList = M('Homenice_user')->where($w)->select();
            foreach($userList as $k => $v){
                $zonemanager = M('Homenice_zm')->where(array('token'=>$this->token,'loc_province'=>$v['loc_province']))->find();
                if($zonemanager){
                    $userList[$k]['zmid'] = $zonemanager['id'];
                }else{
                    $userList[$k]['zmid'] = 0;
                }
            }
        }
        $this->assign('userList',$userList);
        $this->display();
    }
    //设置佣金比例
   /* public function setproportion(){
        $zmInfo = M('Homenice_zm')->where(array('token'=>$this->token,'id'=>session('zm_id')))->find();
        if($zmInfo){
            $this->assign('zmInfo',$zmInfo);
        }
        if(IS_POST){
            if(M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_POST['id']))->find()){
                if(M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_POST['id']))->save($_POST)){
                    exit(json_encode(array('info'=>'设置成功','status'=>1,'url'=>'index.php?g=User&m=Distribution&a=userlist&token='.$this->token.'&zm_id='.$_POST['id'])));
                }else{
                    exit(json_encode(array('info'=>'系统繁忙，设置失败','status'=>0)));
                }
            }else{
                exit(json_encode(array('info'=>'你个臭流氓','status'=>-1)));
            }
        }
        $this->display();
    }*/
    //佣金结算清单
    public function commission(){
        $this->assign('get',$_GET);
        if(session('zm_id')){
            $zonemanagerInfo = M('Homenice_zm')->where(array('token'=>$this->token,'zm_id'=>session('zm_id')))->find();
        }else{
            $zonemanagerInfo = M('Homenice_zm')->where(array('token'=>$this->token,'zm_id'=>$_GET['zmid']))->find();
        }
        $wsInfo = M('Homenice_user')->where(array('token'=>$this->token,'ws_id'=>$_GET['ws_id']))->find();
        if($wsInfo){
            $w['ws_openid'] = $wsInfo['openid'];
            $w['token'] = $this->token;
            $commissionRecorder = M('Homenice_commission')->order('id DESC')->where($w)->select();
            foreach($commissionRecorder as $k => $v){
                $orderInfo = M('Product_cart_new')->where(array('token'=>$this->token,'orderid'=>$v['order_id']))->find();
                $commissionRecorder[$k]['paid'] = $orderInfo['paid'];
                if(IS_POST){
                    if($_POST['is_commission'] !== "3"){
                        if($_POST['is_commission'] !== $commissionRecorder[$k]['is_commission']){
                            unset($commissionRecorder[$k]);
                        }
                    }
                    if($_POST['ispay'] !== "3"){
                        if($_POST['ispay'] !== $commissionRecorder[$k]['ispay']){
                            unset($commissionRecorder[$k]);
                        }
                    }
                }
            }
        }
        $this->assign('post',$_POST);
        $Page = new Page(count($commissionRecorder),25);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $this->assign('commissionRecorder',$commissionRecorder);
        $this->assign('json',json_encode($commissionRecorder));
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    public function docommission(){
        $userInfo = M('Homenice_user')->where(array('token'=>$this->token,'ws_id'=>$_GET['ws_id']))->find();
        if($userInfo['ws_id'] == $_GET['ws_id']){
            $commissionRecorder = M('Homenice_commission')->where(array('token'=>$this->token,'id'=>$_GET['cid']))->find();
            if($commissionRecorder['id'] == $_GET['cid']){
                if(!is_numeric($_GET['single_price'])){
                    exit(json_encode(array('info'=>'单价不合法','status'=>-1)));
                }
                if(!is_numeric($_GET['p'])){
                    exit(json_encode(array('info'=>'比例不合法','status'=>-1)));
                }
                if(!is_numeric($_GET['attribution'])){
                    exit(json_encode(array('info'=>'属性不合法','status'=>-1)));
                }
                $orderInfo = M('Product_cart_new')->where(array('token'=>$this->token,'orderid'=>$commissionRecorder['order_id']))->field('paid')->find();
                if($orderInfo['paid'] == 0){
                    exit(json_encode(array('info'=>'该订单未支付成功，不能结算','status'=>0)));
                }
                $is_c = M('Homenice_commission')->where(array('token'=>$this->token,'id'=>$_GET['cid']))->setField(array('is_commission'=>1,'commission_proportion'=>$_GET['p'],'attribution'=>$_GET['attribution'],'commission_time'=>date('Y-m-d H:i:s'),'single_price'=>$_GET['single_price']));
                if($is_c){
                    if(!empty($userInfo['dopenid'])){
                        $parent = M('Homenice_user')->where(array('token'=>$this->token,'openid'=>$userInfo['dopenid']))->find();
                        if($parent){
                            $recorder = M('Homenice_commission')->where(array('token'=>$this->token,'order_id'=>$commissionRecorder['order_id']))->count();
                            if($recorder == 1){
                                $data['token'] = $this->token;
                                $data['ws_openid'] = $parent['openid'];
                                $data['ws_name'] = $parent['name'];
                                $data['order_id'] = $commissionRecorder['order_id'];
                                $data['order_name'] = $commissionRecorder['order_name'];
                                $data['order_price'] = $commissionRecorder['order_price'];
                                $data['add_time'] = date('Y-m-d H:i:s');
                                $data['is_commission'] = 1;
                                $data['single_price'] = $_GET['single_price'];
                                $data['commission_proportion'] = $_GET['p']/2;
                                $data['attribution'] = $_GET['attribution'];
                                $data['commission_time'] = date('Y-m-d H:i:s');
                                M('Homenice_commission')->add($data);
                            }
                        }
                    }
                    exit(json_encode(array('info'=>'结算成功','status'=>1)));
                }else{
                    exit(json_encode(array('info'=>'结算失败','status'=>0)));
                }
            }else{
                exit(json_encode(array('info'=>'非法操作','status'=>-1)));
            }
        }else{
            exit(json_encode(array('info'=>'非法操作','status'=>-1)));
        }
    }
    //兑现
    public function payCash(){
        $zmInfo = M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_GET['zm_id']))->field('id,loc_province')->find();
        if($zmInfo['id'] == $_GET['zm_id']){
            $userInfo = M('Homenice_user')->where(array('token'=>$this->token,'ws_id'=>$_GET['ws_id']))->field('ws_id,loc_province')->find();
            if($userInfo['ws_id'] == $_GET['ws_id'] && $userInfo['loc_province'] == $zmInfo['loc_province']){
                $commissionInfo = M('Homenice_commission')->where(array('token'=>$this->token,'id'=>$_GET['cid']))->field('id')->find();
                if($commissionInfo){
                    $res = M('Homenice_commission')->where(array('token'=>$this->token,'id'=>$commissionInfo['id']))->setField(array('ispay'=>1,'pay_time'=>date('Y-m-d H:i:s')));
                    if($res){
                        exit(json_encode(array('info'=>'兑现成功','status'=>1)));
                    }else{
                        exit(json_encode(array('info'=>'兑现失败','status'=>0)));
                    }
                }else{
                    exit(json_encode(array('info'=>'非法操作','status'=>-1)));
                }
            }else{
                exit(json_encode(array('info'=>'非法操作','status'=>-1)));
            }
        }else{
            exit(json_encode(array('info'=>'非法操作','status'=>-1)));
        }
    }
    /*public function docommission(){
        if(IS_POST){
            $d['start_date'] = $_REQUEST['start_date'];
            $d['end_date'] = $_REQUEST['end_date'];
            $d['token'] = $_REQUEST['token'];
            foreach($_REQUEST['json'] as $k => $v){
                if($v['is_commission'] == 2){
                    $d['money'] = $v['commission'] +$d['money'];
                    $d['ws_name'] = $v['ws_name'];
                    $d['ws_openid'] = $v['ws_openid'];
                    $kk = 1;
                }
                $w['ws_openid'] = $v['ws_openid'];
            }
            $d['doc_time'] = date('Y-m-d H:i:s');
            $w['token'] = $this->token;
            $w['start_date'] = array('elt',$_REQUEST['start_date']);
            $w['end_date'] = array('egt',$_REQUEST['end_date']);
            if($kk !== 1){
                exit(json_encode(array('info'=>'没有找到可以结算的记录！','status'=>-1)));
            }else{
                if(M('Homenice_doc')->add($d)){
                    $data['is_commission'] = 1;
                    foreach($_REQUEST['json'] as $k => $v){
                        $where['id'][] = array('eq',$v['id']);
                    }
                    array_push($where['id'],'or');
                    M('Homenice_commission')->where($where)->save($data);
                    exit(json_encode(array('info'=>'结算成功','status'=>1)));
                }else{
                    exit(json_encode(array('info'=>'结算失败','status'=>0)));
                }
            }
        }
    }*/
    //数据统计
    public function dataCount(){
        //每个省份微商统计图
        $sql1 = "select loc_province,COUNT(loc_province) AS num from tp_homenice_user WHERE  token='".$this->token."' group by loc_province";
        $sql2 = "select loc_province from tp_homenice_user WHERE token='".$this->token."' group by loc_province";
        $loc_province = M('Homenice_user')->query($sql2);
        $userList = M('Homenice_user')->query($sql1);
        foreach($loc_province as $k => $v){
            $province[] = $v['loc_province'];
        }
        foreach($userList as $k => $v){
            $distribution[$k]['value'] = $v['num'];
            $distribution[$k]['name'] = $v['loc_province'];
            $num += $v['num'];
        }
        $this->assign('num',$num);
        $this->assign('distribution',json_encode($distribution));
        $this->assign('province',json_encode($province));

        //每月微商统计图
        $year = date ( 'Y' ) % 4;
        if ($year == 0) {
            $date = array ('01' => '31', '02' => '29', '03' => '31', '04' => '30', '05' => '31', '06' => '30', '07' => '31', '08' => '31', '09' => '30', '10' => '31', '11' => '30', '12' => '31' );
        } else {
            $date = array ('01' => '31', '02' => '28', '03' => '31', '04' => '30', '05' => '31', '06' => '30', '07' => '31', '08' => '31', '09' => '30', '10' => '31', '11' => '30', '12' => '31' );
        }
        foreach($province as $key => $value){
            $arr = array(
                'type'=>'line',
                'smooth'=>true,
                'itemStyle'=>array(
                    'normal'=>array(
                        'areaStyle'=>array(
                            'type'=>'default'
                        )
                    )
                )
            );
            $arr['name'] = $value;
            $oneMonth = array();
            foreach($date as $k => $v){
                $monthProvince = array();
                $start_month = date('Y').'-'.$k.' 00:00:00';
                $end_month = date('Y').'-'.$k.'-'.$v.' 23:59:59';
                $Month['reg_time'] = array('between',array($start_month,$end_month));
                $monthProvince = M('Homenice_user')->field('COUNT(loc_province) as monthprovince')->where(array('token'=>$this->token,'reg_time'=>$Month['reg_time'],'loc_province'=>$value))->select();
                $oneMonth[] = $monthProvince[0]['monthprovince'];
            }
            $arr['data'] = $oneMonth;
            $Max[] = $arr;
        }
        $this->assign('Max',json_encode($Max));
        $this->display();
    }
    public function historyLookup(){
        $sql = "select n.name,v.nickname,v.time,v.address,v.url from tp_product_new_visiter_data AS v LEFT JOIN tp_product_new AS n ON n.id=v.product_id WHERE v.token='".$this->token."' order by v.time DESC";
        $res = M('Product_new_visiter_data')->query($sql);
        $Page       = new Page(count($res),30);
        $sql1 = "select n.name,v.nickname,v.time,v.address,v.url from tp_product_new_visiter_data AS v LEFT JOIN tp_product_new AS n ON n.id=v.product_id WHERE v.token='".$this->token."' order by v.time DESC limit ".$Page->firstRow.",".$Page->listRows;
        $res = M('Product_new_visiter_data')->query($sql1);
        $show       = $Page->show();
        foreach($res as $k => $v){
            if(strpos($v['url'],'a=cats') == true){
                $res[$k]['name'] = '商城主页';
            }elseif(strpos($v['url'],'a=products') == true){
                $res[$k]['name'] = '商品分类页';
            }
        }
        $this->assign('page',$show);
        $this->assign('res',$res);
        $this->display();
    }

    //区域管理员修改密码
    public function modifyPwd(){
        $this->assign('get',$_GET);
        $zmInfo = M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_GET['zm_id']))->field('id,token,username,pwd')->find();
        if($zmInfo){
            if($zmInfo['id'] == session('zm_id')){
                $this->assign('zmInfo',$zmInfo);
            }
        }
        if(IS_POST){
            $zmInfo = M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$_POST['id']))->field('id,token,username,pwd,password')->find();
            if($zmInfo){
                if($zmInfo['password'] == md5($_POST['o'])){
                    if($zmInfo['password'] !== md5($_POST['opassword'])){
                        exit(json_encode(array('info'=>'旧密码输入不正确','status'=>-1)));
                    }
                    if($_POST['npassword'] !== $_POST['cnpassword']){
                        exit(json_encode(array('info'=>'两次密码输入不一致','status'=>-1)));
                    }else{
                        if(M('Homenice_zm')->where(array('token'=>$this->token,'id'=>$zmInfo['id']))->setField(array('password'=>md5($_POST['npassword']),'pwd'=>$_POST['npassword'],'last_edit_time'=>date('Y-m-d H:i:s')))){
                            session_destroy();
                            exit(json_encode(array('info'=>'修改密码成功，请重新登录','status'=>1,'url'=>'index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Homenice_zm')));
                        }else{
                            exit(json_encode(array('info'=>'修改密码失败','status'=>0)));
                        }
                    }
                }
            }else{
                exit(json_encode(array('info'=>'非法操作','status'=>-1)));
            }
        }
        $this->display();
    }
}