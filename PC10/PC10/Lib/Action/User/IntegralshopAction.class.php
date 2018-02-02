<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/10/24
 * Time: 10:40
 */
class IntegralshopAction extends UserAction{

    public function index(){
        //显示主页页面
        $model = M('Integralshop');
        //$where['tp_integralshop.token'] = session('token');
        $where = array('tp_integralshop.token'=>session('token'));
        $is_up = $_REQUEST['is_up'];
        if(!empty($is_up)){
            $where['tp_integralshop.is_up'] = $is_up;
        }
        if(IS_POST){
            $where['tp_integralshop.giftkey'] = $_REQUEST['giftkey'];
            if($_POST['id']){
                $iTem = $model->where(array('id'=>$_POST['id']))->find();
                if($iTem){
                    if($model->where(array('id'=>$_POST['id']))->save(array('is_up'=>$_POST['is_up']))){
                        $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
                    }else{
                        $this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
                    }
                }else{
                    $this->error('非法操作！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
                }
            }
        }

        $count      = $model->where($where)->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();

        $list = $model->where($where)->field('tp_integralshop.*,l.name')->join('left join tp_usercenter_level as l on tp_integralshop.extent = l.id ')->select();
        $this->assign('page',$show);
        $this->assign('data',$list);
        $this->display();

//        $a = new Model('a');
//        $list = $a->join('left join b on a.uid = b.uid' );
    }
    public function append(){
        //显示添加礼品页面
        $model = M('Usercenter_level');
        $where =array('token'=>session('token'));

        $data=$model->where($where)->order('id desc')->select();
        $this->assign('data',$data);
        $this->display();
    }
    //操作添加礼品页面
    public function add(){
        $_POST['token'] = $_REQUEST['token'];
        $_POST['giftkey'] = 'wp'.time();
        $model = M('Integralshop');
        $result=$model->add($_POST);
        if($result){
            $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
        }else{
            $this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
        }
    }
    public function revise(){
        //显示修改礼品页面
        $model = M('Integralshop');
        $where = array('token'=> session('token'),'id' => $_GET['id']);
        $data = $model->where($where)->find();
        $this->assign('data',$data);

        $db = M('Usercenter_level');
        $arr =array('token'=>session('token'));
        $result=$db->where($arr)->order('id desc')->select();
        $this->assign('result',$result);
        $this->display();
    }
    //兑换
    public function duihuan(){
        if(M('integralshop_individual')->where(array('id'=>$_GET['id']))->save(array('is_use'=>2))){
            $this->success2('成功');
        }else{
            $this->error2('失败');
        }
    }

    public function save(){
        $where = array('token'=> session('token'),'id' => $_GET['id']);
        $model=M('Integralshop');
        $_POST['token'] = $_REQUEST['token'];
        $_POST['giftkey'] = 'wp'.time();
        $result=$model->where($where)->save($_POST);
        if ($result) {
            $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
        }else{
            $this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
        }
    }

    public function del(){
        //删除操作
        $id=$this->_get('id','intval');
        $token=$this->_get('token');
        $where['id']=$id;
        $where['token']=$token;
        $db=M('Integralshop');
        $result=$db->where($where)->delete();
        if ($result) {
            $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$token)));
        }else{
            $this->error('操作失败！',U(MODULE_NAME.'/index',array('token'=>$token)));
        }
    }

    public function reveal(){

        $model =  M('Integralshop_individual');
        $token = session('token');
        $where= "( tp_integralshop_individual.token = '$token' )";
        $lid = $_GET['id'];
        if(!empty($lid)){
            $where = $where."AND (tp_integralshop_individual.lid = '$lid')";
        }
        if($_POST['truename']){
            $truename = $_POST['truename'];
            $where = $where."AND (u.id ='$truename')";
            $this->assign('truename',$_POST['truename']);
        }
        if($_POST['phone']){
            $phone = $_POST['phone'];
            $where = $where."AND (u.phone = '$phone')";
            $this->assign('phone',$_POST['phone']);
        }
        if($_POST['giftkey']){
            $giftkey = $_POST['giftkey'];
            $where = $where."AND (i.giftkey = '$giftkey')";
            $this->assign('giftkey',$_POST['giftkey']);
        }
        if($_POST['statdate']&&$_POST['enddate']){
            $statdate = strtotime($_POST['statdate']);
            $enddate = strtotime($_POST['enddate']);
            $where .= "AND (tp_integralshop_individual.time > '$statdate') AND (tp_integralshop_individual.time < '$enddate')";
            $this->assign(array(
                'statdate'=>$_POST['statdate'],
                'enddate'=>$_POST['enddate']
            ));
        }
        if($_POST['is_use']){
            $is_use = $_POST['is_use'];
            $where = $where."AND (tp_integralshop_individual.is_use ='$is_use')";
            $this->assign('is_use',$_POST['is_use']);
        }
        if($_POST['snnum']){
            $snnum = $_POST['snnum'];
            $where = $where."AND (tp_integralshop_individual.snnum ='$snnum')";
            $this->assign('snnum',$_POST['snnum']);
        }
        if($_POST['shop_id']){
            $shop_id = $_POST['shop_id'];
            $where = $where."AND (tp_integralshop_individual.shop_id ='$shop_id')";
            $this->assign('shop_id',$_POST['shop_id']);
        }
        if($_REQUEST['branch_id']){
            $shop_id = $_REQUEST['branch_id'];
            $where = $where."AND (tp_integralshop_individual.shop_id ='$shop_id')";
            //$this->assign('shop_id',$_POST['shop_id']);
        }

        $info = M('shop')->where(array('token'=>session('token')))->select();
        $this->assign('info',$info);
       // P($_POST['shop_id']);exit;

        /*if($this->token != '3db7fee419649f8be761dfc4f6b42ecc'){
            $list = $model->where($where)->field('tp_integralshop_individual.*,u.name,u.member_sn,u.phone')->join('left join tp_usercenter_memberlist as u on tp_integralshop_individual.openid = u.openid')->order('tp_integralshop_individual.time desc')->select();
        }else{*/
        /*易派单*/

        $list = $model->query("SELECT tp_integralshop_individual.*,u.truename as name,u.id as member_sn,u.phone,i.giftname,i.giftkey as gid,i.num,i.integral FROM
                          `tp_integralshop_individual` left join tp_shop_users as u on
                          tp_integralshop_individual.openid = u.openid left join
                          tp_integralshop as i on tp_integralshop_individual.lid = i.id
                           WHERE $where ORDER BY tp_integralshop_individual.time desc");
        //}
        //if($this->token == '5d8a87bab30de695954b17fc835b9d12'){
        if($this->token == '8a71b21a11dd5212bd74cee41dafab64'){
            $list = $model->query("SELECT tp_integralshop_individual.*,u.nickname as name,u.id as member_sn,u.phone,i.giftname,i.giftkey as gid,i.num,i.integral FROM
                          `tp_integralshop_individual` left join tp_media_users as u on
                          tp_integralshop_individual.openid = u.openid left join
                          tp_integralshop as i on tp_integralshop_individual.lid = i.id
                           WHERE $where ORDER BY tp_integralshop_individual.time desc");

        }
        foreach($list as $k=>$val){
            $usesum = intval($model->where(array('token'=>$this->token,'lid'=>$val['lid']))->count());
            $yusum = $val['num'] - $usesum;
            if($yusum==0){
                M('Integralshop')->where(array('id'=>$val['lid']))->save(array('is_up'=>2));
            }
            $list[$k]['usesum'] = $usesum;
            $list[$k]['yusum'] = $yusum;
            $shop = M('Shop')->where(array('id'=>$val['shop_id']))->find();
            $list[$k]['shop_id'] = $shop['username'];
        }
        /*统计数据*/

        $count      = count($list);
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign(array(
            'giftcount'=> intval($model->where(array('token'=>session('token')))->count()),
            'scorecount'=> intval($model->where(array('token'=>session('token')))->sum('store')),
            'mancount'=>intval($model->where(array('token'=>session('token')))->group('openid')->count())
        ));
       // return $list;
        if($_REQUEST['branch_id']){
            $this->display('reveals');
        }else{
            $this->display('reveal');
        }


    }

    public function duihuan_order(){
        $model =  M('Integralshop_individual');
        $token = session('token');
        $where= "( tp_integralshop_individual.token = '$token' )";
        $lid = $_GET['id'];
        if(!empty($lid)){
            $where = $where."AND (tp_integralshop_individual.lid = '$lid')";
        }
        if($_POST['truename']){
            $truename = $_POST['truename'];
            $where = $where."AND (u.id ='$truename')";
            $this->assign('truename',$_POST['truename']);
        }
        if($_POST['phone']){
            $phone = $_POST['phone'];
            $where = $where."AND (u.phone = '$phone')";
            $this->assign('phone',$_POST['phone']);
        }
        if($_POST['giftkey']){
            $giftkey = $_POST['giftkey'];
            $where = $where."AND (i.giftkey = '$giftkey')";
            $this->assign('giftkey',$_POST['giftkey']);
        }
        if($_POST['statdate']&&$_POST['enddate']){
            $statdate = $_POST['statdate'];
            $enddate = $_POST['enddate'];
            $where = $where."AND (tp_integralshop_individual.time > '$statdate')AND(tp_integralshop_individual.time < '$enddate')";
            $this->assign(array(
                'statdate'=>$_POST['statdate'],
                'enddate'=>$_POST['enddate']
            ));
        }
        if($_POST['is_use']){
            $is_use = $_POST['is_use'];
            $where = $where."AND (tp_integralshop_individual.is_use ='$is_use')";
            $this->assign('is_use',$_POST['is_use']);
        }
        if($_POST['snnum']){
            $snnum = $_POST['snnum'];
            $where = $where."AND (tp_integralshop_individual.snnum ='$snnum')";
            $this->assign('snnum',$_POST['snnum']);
        }
        if($_POST['shop_id']){
            $shop_id = $_POST['shop_id'];
            $where = $where."AND (tp_integralshop_individual.shop_id ='$shop_id')";
            $this->assign('shop_id',$_POST['shop_id']);
        }


        $info = M('shop')->where(array('token'))->select();
        $this->assign('info',$info);
        // P($_POST['shop_id']);exit;

        /*if($this->token != '3db7fee419649f8be761dfc4f6b42ecc'){
            $list = $model->where($where)->field('tp_integralshop_individual.*,u.name,u.member_sn,u.phone')->join('left join tp_usercenter_memberlist as u on tp_integralshop_individual.openid = u.openid')->order('tp_integralshop_individual.time desc')->select();
        }else{*/

        $list = $model->query("SELECT tp_integralshop_individual.*,u.truename as name,u.id as member_sn,u.phone,i.giftname,i.giftkey as gid,i.num,i.integral FROM
                          `tp_integralshop_individual` left join tp_shop_users as u on
                          tp_integralshop_individual.openid = u.openid left join
                          tp_integralshop as i on tp_integralshop_individual.lid = i.id
                           WHERE $where ORDER BY tp_integralshop_individual.time desc");
        //}
        foreach($list as $k=>$val){
            $usesum = intval($model->where(array('token','lid'=>$val['lid']))->count());
            $yusum = $val['num'] - $usesum;
            $list[$k]['usesum'] = $usesum;
            $list[$k]['yusum'] = $yusum;
            $shop = M('Shop')->where(array('id'=>$val['shop_id']))->find();
            $list[$k]['shop_id'] = $shop['username'];
        }
        $data = array();
        foreach($list as $key=>$value){
            $data[$key]['giftname'] = $value['giftname'];
            $data[$key]['giftkey'] = $value['giftkey'];
            $data[$key]['giftname'] = $value['giftname'];
            $data[$key]['num'] = $value['num'];
            $data[$key]['usesum'] = $value['usesum'];
            $data[$key]['yusum'] = $value['yusum'];
            $data[$key]['integral'] = $value['integral'];
            $data[$key]['truename'] = $value['truename'];
            $data[$key]['member_sn'] = $value['member_sn'];
            $data[$key]['phone'] = $value['phone'];
            $data[$key]['snnum'] = $value['snnum'];
            $data[$key]['shop_id'] = $value['shop_id'];
            $data[$key]['time'] = date('Y-m-d',$value['time']);
        }
       // p($data);exit;
        exportExcel($data,array('礼品名字','礼品编号','库存数量','送出数量',
            '剩余数量','所需积分','会员名字','会员编号','会员电话','兑换码',
            '所属店铺','领取时间','兑换状态'),$filename='兑换记录表');
    }
}