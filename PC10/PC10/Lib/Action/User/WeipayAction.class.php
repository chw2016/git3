<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-4-28
 * Time: 上午11:29
 * To change this template use File | Settings | File Templates.
 */
class WeipayAction extends UserAction{

    public $weipay;

    public function _initialize() {
        parent::_initialize();
        $this->weipay=M('Weipay_config');
        if (!$this->token){
            exit();
        }
    }

    public function index(){
        $config = $this->weipay->where(array('token'=>$this->token))->find();
        $user = M('Wxuser')->where(array('token'=>session('token')))->find();
        if(IS_POST){
            $row['appid']=$this->_post('appid');
            $row['appsecret']=$this->_post('appsecret');
            $row['partnerkey']=$this->_post('partnerkey');
            $row['appkey']=$this->_post('appkey');
            if($this->_post('is_open')){
                $row['is_open']=$this->_post('is_open');
            }else{
                $row['is_open'] = 0;
            }
            $row['token']=session('token');
            $row['uid']=$user['id'];
            if ($config){
                $where=array('token'=>$this->token);
                $this->weipay->where($where)->save($row);
            }else {
                $this->weipay->add($row);
            }
            $this->success('设置成功',U('Weipay/index'));
        }else{
            $this->assign('hover1',1);
            $this->assign('config',$config);
            $this->display();
        }
    }

    public function weipayorderlist(){
            $where['token']=session('token');
         /*   $tid=M('Wxuser')->where(array('token'=>session('token')))->getField('id');*/

             if(IS_POST){

                    if($_POST['orderid']){
                           $where['tp_weipay_order_list.orderid']=$this->_post('orderid');
                    }
                    if($_POST['from_orderid']){
                        $where['tp_weipay_order_list.from_orderid']=$this->_post('from_orderid');
                    }
                    if($_POST['status']!='全部'){
                        $where['tp_weipay_order_list.status']=$this->_post('status');
                    }
                     if($_POST['nickname']){
                         $where['a.nickname']=$this->_post('nickname');
                     }
                 $db=M('Weipay_order_list');
                 $count=$db->where($where)->join("join tp_wxusers as a on a.openid=tp_weipay_order_list.openid")->count();
                 $page=new Page($count,15);
                 $info=$db->where($where)
                     ->field('a.nickname,tp_weipay_order_list.id,tp_weipay_order_list.orderid,tp_weipay_order_list.from_orderid,tp_weipay_order_list.order_money,
                     tp_weipay_order_list.add_time,tp_weipay_order_list.order_type,tp_weipay_order_list.status')
                     ->join("join tp_wxusers as a on a.openid=tp_weipay_order_list.openid")->limit($page->firstRow.','.$page->listRows)->order('tp_weipay_order_list.add_time desc')->select();
                 $this->assign('page',$page->show());
                 $this->assign('info',$info);
                 $this->assign('hover2',1);
                 $this->display();
             }else{
                 if($_GET['orderid']){
                     $where['tp_weipay_order_list.orderid']=$this->_get('orderid');
                 }
                 if($_GET['from_orderid']){
                     $where['tp_weipay_order_list.from_orderid']=$this->_get('from_orderid');
                 }
                 if(isset($_GET['status'])&&$_GET['status']!='全部'){
                     $where['tp_weipay_order_list.status']=$this->_get('status');
                 }
                 if($_GET['nickname']){
                     $where['a.nickname']=$this->_get('nickname');
                 }
               //  p($where);

                 $db=M('Weipay_order_list');
                 $count=$db->where($where)->join("join tp_wxusers as a on a.openid=tp_weipay_order_list.openid")->count();
                 $page=new Page($count,15);
                 $info=$db->where($where)
                     ->field('a.nickname,tp_weipay_order_list.id,tp_weipay_order_list.orderid,tp_weipay_order_list.from_orderid,tp_weipay_order_list.order_money,
                     tp_weipay_order_list.add_time,tp_weipay_order_list.order_type,tp_weipay_order_list.status')
                     ->join("join tp_wxusers as a on a.openid=tp_weipay_order_list.openid")->limit($page->firstRow.','.$page->listRows)->order('tp_weipay_order_list.add_time desc')->select();
                 $this->assign('page',$page->show());
                 $this->assign('info',$info);
                 //p($info);
                 $this->assign('hover2',1);
                 $this->display();
             }



    }
    //导出
    public function excel(){
            $where['token']=session('token');
        if(IS_POST) {
            if ($_POST['orderid']) {
                $where['orderid'] = $this->_post('orderid');
            }
            if ($_POST['from_orderid']) {
                $where['from_orderid'] = $this->_post('from_orderid');
            }
            if ($_POST['status'] != '全部') {
                $where['status'] = $this->_post('status');
            }
        }
        $data=D('Weipay_order_list')->field('orderid,from_orderid,order_money,add_time,order_type,status')->where($where)->order('add_time desc')->select();
        foreach($data as $k=>$v){
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            if($v['status']==1){
                $data[$k]['status']='成功';
            }else{
                $data[$k]['status']='失败';
            }
            $data[$k]['from_orderid']=' '.$v['from_orderid'];
        }
        exportExcel($data,array('订单编号','产品订单号','金额','时间','订单类型','是否支付成功'),'非信贷订单');
    }



}