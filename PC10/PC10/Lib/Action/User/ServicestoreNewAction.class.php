<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/1/17
 * Time: 15:40
 */
class ServicestoreNewAction extends UserAction{

    public $token;
    public $userModel;
    public $serviceStoreModel;
    public $wxUserModel;
    public $userInfoData;
    public $serverOrderModel;
    public $storeId;

    public function _initialize() {

        parent::_initialize();
        if (!session('?token')) {
            session('token', $_GET['token']);
        }

        if (!session('?storeId')) {
            session('storeId', $_GET['storeId']);
        }
        $this->storeId = session('storeId');
        $this->assign('storeId', $this->storeId);
        $this->token = session('token');
        $this->assign('token', $this->token);
        $this->serverOrderModel = M('service_orders');
        $this->serviceStoreModel = M("service_store");
        $this->userModel = M('wxuser');
        $this->userInfoData = $this->userModel->where(array('token'=>$this->token))->find();
        $this->wxUserModel = M('wxusers');
    }

   /*
     * 售后服务部管理
    */
    public function index(){
        if (is_array($this->userInfoData) && !empty($this->userInfoData)) {
            $condition['wxuser_id'] = $this->userInfoData['id'];
            /*产看所有售后服务预约订单数*/
            $serverOrderCount = $this->serverOrderModel->where($condition)->count();
            $this->assign('serverOrderCount', $serverOrderCount);

            /*列举售后服务所有分部的基本信息*/
            import('ORG.Util.Page');
            $count = $this->serviceStoreModel->where($condition)->count();
            $page = new Page($count, 20);
            $nowPage = isset($_GET['p'])?$_GET['p']:1;
            $list = $this->serviceStoreModel->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
            foreach($list as $key=>$val){
                $info = M('Staff')->where(array('id'=>$val['zid']))->find();
                $list[$key]['zperson'] = $info['name'];
                $list[$key]['ztel'] = $info['telephone'];
            }


            $show = $page->show();
            $this->assign('list', $list);
            $this->assign('page', $show);

            /*列举售后服务未处理的预约订单数*/
            $condition['status'] = 0;
            $serverNoOrderCount = $this->serverOrderModel->where($condition)->count();
            $this->assign('serverNoOrderCount', $serverNoOrderCount);
            /*列举售后服务已经处理的预约订单数*/
            $condition['status'] = array('between','1 ,2');
            $serverYesOrderCount = $this->serverOrderModel->where($condition)->count();
            $this->assign('serverYesOrderCount', $serverYesOrderCount);
            /*列举售后客户总数*/
            $serverWxuserCount = $this->wxUserModel->where(array('uid'=>$this->userInfoData['id'], 'status'=>1))->count();
            $this->assign('serverWxuserCount', $serverWxuserCount);
        }
        $this->display();
    }
    //新增与编辑售后服务网点
    public function manager() {

        $operation = $_GET['op']?$_GET['op']:0; //操作默认情况下是添加分店的状况

        if (IS_POST) {
            $operation = $_POST['op']?$_POST['op']:0;
// 			$wxuser_id = $this->wxUser->where(array('token'=>$this->token))->find();
            $data['wxuser_id'] = $this->userInfoData['id'];
            $data['name'] = $_POST['name'];
            $data['adress'] = $_POST['address'];
            $data['phone'] = $_POST['tel'];
            $data['token'] = $_POST['token'];
            $data['image_url'] = $_POST['logourl'];
            $data['longitude'] = $_POST['longitude'];
            $data['latitude'] = $_POST['latitude'];
            /*$data['person'] = $_POST['person'];
            $data['tel'] = $_POST['phone'];*/
            $intro = isset($_POST['intro'])?$_POST['intro']:'';
            $actionIntro = isset($_POST['actionIntro'])?$_POST['actionIntro']:'';
            $data['info'] = $intro;
            $data['prompt_info'] = $actionIntro;
            $data['type'] = $_POST['type'];
            $data['zid'] = $_POST['zid'];
            $data['rank'] = $_POST['rank'];
            $Wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
           // P(M('staff')->where(array('wxuser_id'=>$this->userInfoData['id'],'id'=>$_POST['zid']))->find());exit;

                if (0 == $operation) {
                    if ($this->serviceStoreModel->add($data)) {
                        $this->success('添加成功','index.php?g=User&m=ServicestoreNew&a=index&token='.$this->_post('token'));
                    } else {
                        $this->error('添加失败', 'index.php?g=User&m=ServicestoreNew&a=manager&op=0&token='.$this->_post('token'));
                    }
                } elseif (1 == $operation) {
                    //if(M('staff')->where(array('wxuser_id'=>$this->userInfoData['id'],'id'=>$_POST['zid']))->find()){
                        $data['id'] = $_POST['id'];
                        if ($this->serviceStoreModel->where(array('id'=>$_POST['id']))->save($data)) {
                            $this->success('编辑成功','./index.php?g=User&m=ServicestoreNew&a=index&token='.$this->token);
                        } else {
                            $this->error('编辑失败', './index.php?g=User&m=ServicestoreNew&a=manager&op=1&token='.$this->token.'&id='.$data['id']);
                        }
                    /*}else{
                        $this->error('该站长不在公司的专员内');
                    }*/
                }


        }
        if (1 == $operation) {
            $orderId = $_GET['id'];
            $is_exist = $this->serviceStoreModel->where(array('id'=>$orderId))->find();
            if ($is_exist == true) {
                $this->assign('token', $this->token);
                $this->assign("data", $is_exist);
            }
        }
        $aWhere = array(
            'wxuser_id'=>$this->userInfoData['id'],
            'belong_id'=>$_GET['id'],
            'openid' =>array('neq','')
        );
        $staff = M('Staff')->where($aWhere)->select();

        $this->assign('staff',$staff);
        $this->assign('op',$operation);
        $this->display();
    }

    //删除服务网点
    public function del() {
        $orderId = $_GET['id'];
        $is_exist = $this->serviceStoreModel->where(array('id'=>$orderId))->find();
        if ($is_exist == true) {
            $back = $this->serviceStoreModel->where(array('id'=>$orderId))->delete();
            if ($back == true) {
                $this->success('删除成功','./index.php?g=User&m=ServicestoreNew&a=index&token='.$this->token);
            } else {
                $this->error('删除失败','./index.php?g=User&m=ServicestoreNew&a=index&token'.$this->token);
            }
        } else {
            $this->error('删除失败','./index.php?g=User&m=ServicestoreNew&a=index&token='.$this->token);
        }
    }


    /*
     * 维修订单管理
     * 默认情况下是所以的订单情况；在上面进行个数据统计，分未处理订单（type=1），已处理订单（type=2），所有订单和售后服务数量
     */

    public function orders(){
        $type = $_GET['type'];
        $token = $this->token;

        if($type == 1){
            $count = $this->serverOrderModel->where(array('token'=>$token,'status'=>0))->count();
            $Page = new Page($count,15);
            $show = $Page->show();
            $list = $this->serverOrderModel->where(array('token'=>$token,'status'=>0))->order('otime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($list as $k=>$value){
                //网店名称、员工名
                $store = $this->serviceStoreModel->where(array('token'=>$this->token,'id'=>$value['storeID']))->find();
                $list[$k]['storename'] = $store['name'];
                $staff = M('Staff')->where(array('id'=>$value['staffID']))->find();
                $list[$k]['staffname'] = $staff['name'];
                $list[$k]['tel'] = $staff['telephone'];
            }
            $this->assign('list',$list);
            $this->assign('page',$show);
        }elseif($type == 2){
            //$map['id']  = array('between','1,8');
            $status = array('between','1,2');
            $count = $this->serverOrderModel->where(array('token'=>$token,'status'=>$status))->count();
            $Page = new Page($count,15);
            $show = $Page->show();
            $list = $this->serverOrderModel->where(array('token'=>$token,'status'=>$status))->order('otime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($list as $k=>$value){
                //网店名称、员工名
                $store = $this->serviceStoreModel->where(array('token'=>$this->token,'id'=>$value['storeID']))->find();
                $list[$k]['storename'] = $store['name'];
                $staff = M('Staff')->where(array('id'=>$value['staffID']))->find();
                $list[$k]['staffname'] = $staff['name'];
                $list[$k]['tel'] = $staff['telephone'];
            }
            $this->assign('list',$list);
            $this->assign('page',$show);
        }else{

            /*列举售后服务未处理的预约订单数*/
            $condition['status'] = 0;
            $condition['token'] = $this->token;
            $serverNoOrderCount = $this->serverOrderModel->where($condition)->count();

            $this->assign('serverNoOrderCount', $serverNoOrderCount);
            /*列举售后服务已经处理的预约订单数*/
            $condition['status'] = array('between','1 ,2');
            $condition['token'] = $this->token;
            $serverYesOrderCount = $this->serverOrderModel->where($condition)->count();
            $this->assign('serverYesOrderCount', $serverYesOrderCount);
            /*列举售后客户总数*/
            $serverWxuserCount = $this->wxUserModel->where(array('uid'=>$this->userInfoData['id'], 'status'=>1))->count();
            $this->assign('serverWxuserCount', $serverWxuserCount);
            /*所有订单*/
            $id = $_GET['id'];
            if($id){
                $count = $this->serverOrderModel->where(array('token'=>$token,'storeID'=>$id))->count();
                $Page = new Page($count,15);
                $show = $Page->show();
                $list = $this->serverOrderModel->where(array('token'=>$token,'storeID'=>$id))->order('otime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
                foreach($list as $k=>$value){
                    //网店名称、员工名
                    $store = $this->serviceStoreModel->where(array('token'=>$this->token,'id'=>$value['storeID']))->find();
                    $list[$k]['storename'] = $store['name'];
                    $staff = M('Staff')->where(array('id'=>$value['staffID']))->find();
                    $list[$k]['staffname'] = $staff['name'];
                    $list[$k]['tel'] = $staff['telephone'];
                }
                $this->assign('list',$list);
                $this->assign('page',$show);

            }else{
                $count = $this->serverOrderModel->where(array('token'=>$token))->count();
                $Page = new Page($count,15);
                $show = $Page->show();
                $list = $this->serverOrderModel->where(array('token'=>$token))->order('otime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
                foreach($list as $k=>$value){
                    //网店名称、员工名
                    $store = $this->serviceStoreModel->where(array('token'=>$this->token,'id'=>$value['storeID']))->find();
                    $list[$k]['storename'] = $store['name'];
                    $staff = M('Staff')->where(array('id'=>$value['staffID']))->find();
                    $list[$k]['staffname'] = $staff['name'];
                    $list[$k]['tel'] = $staff['telephone'];
                }
                $this->assign('list',$list);
                $this->assign('page',$show);
            }
        }

        $this->display();
    }
    /*
     * 订单处理
     * 公司接到单后对其情况进行安排，包括安排技工去处理，对订单的受理情况*/
    public function orderInfo(){
        $token = $this->token;        
        $oid= $_GET['id'];
        $orders = $this->serverOrderModel->where(array('token'=>$token,'id'=>$oid))->find();
        $this->assign('date',$orders);
        //网点名、职员名
        $storeInfoData = $this->serviceStoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->select();
        $this->assign('storeInfoData', $storeInfoData);
        $fd = M('Staff')->where(array('token'=>$token,'belong_id'=>$orders['storeID']))->select();
        $this->assign('fd', $fd);
        if(IS_POST){
            $condition['storeID'] = $_POST['storeID'];
            $condition['staffID'] = $_POST['staffID'];
            $condition['status'] = $_POST['status'];
            $cid = $_POST['id'];
            $orderInfo = $this->serverOrderModel->where(array('token'=>$token,'id'=>$cid))->save($condition);
            if($orderInfo){
                $this->success('处理成功','./index.php?g=User&m=ServicestoreNew&a=orders&token='.$this->token);
            }else{
                $this->error('处理失败', './index.php?g=User&m=ServicestoreNew&a=orderInfo&token='.$this->token.'&id='.$cid);
            }
        }

        $this->display();
    }
    public function ajax(){
    	if(IS_POST){
            $id = $this->_post('id','intval');
            $token = $_GET['token'];
            $staffModel = M('Staff');
            $where['belong_id'] = $id;
            $getResult = $staffModel->where($where)->select();
           // $name = array();
            $str = "";
            foreach($getResult as $key => $value){
                //$name[$key] = $getResult[$key]['name'];
                $str.='<option value="'.$value["id"].'">'.$value["name"].'</option>';
            }
            $array = array(
                'option' => $str
            );
            $array = json_encode($array,true);
            echo $array;
        }
    }
    //订单删除；
    public function deloreder(){
        $where['token'] = $this->token;
        $where['id'] = $_GET['id'];
	    $deletes = $this->serverOrderModel->where($where)->delete();
        if($deletes){
            $this->success('删除成功','./index.php?g=User&m=ServicestoreNew&a=orders&token='.$this->token);
        }else{
            $this->error('删除失败','./index.php?g=User&m=ServicestoreNew&a=orders&token'.$this->token);
        }
    }
    /*
     * 公司职员管理
    */
    /*管理员工（删除和编辑公司职员）*/
    public function managerStaff() {
        $staffModel = M('staff');
        $operation = $_GET['op']?$_GET['op']:0;
        if (IS_POST) {
            $operation = $_POST['op']?$_POST['op']:0;
            $acceptData['name'] = $_POST['name']; //姓名
            $acceptData['staff_id'] = $_POST['staffid'];//工号
            $acceptData['staff_type'] = $_POST['display']; //工种
            $acceptData['age'] = $_POST['age'];
            //$acceptData['work_age'] = $_POST['work_age'];
            $acceptData['sex'] = $_POST['sex'];
            $acceptData['status'] = $_POST['status'];
            $storeResult = $this->serviceStoreModel->where(array('name'=>$_POST['store'], 'wxuser_id'=>$this->userInfoData['id']))->find();
            if (is_array($storeResult) && !empty($storeResult)) {
                $acceptData['belong_id'] = $storeResult['id'];
            }
            $acceptData['staff_logo'] = $_POST['logourl'];
            $acceptData['telephone'] = $_POST['telephone'];
            //$acceptData['staff_info'] = $_POST['staff_info'];
            $acceptData['wxuser_id'] = $this->userInfoData['id'];
            if (0 == $operation) {
                if($staffModel->where(array('wxuser_id'=>$this->userInfoData['id'],'staff_id'=>$acceptData['staff_id']))->find()){
                    $this->error('工号已存在，添加失败', 'index.php?g=User&m=ServicestoreNew&a=managerStaff&op=0&token='.$this->token);
                }else{
                    if ($staffModel->add($acceptData)) {
                        $this->success('添加成功','index.php?g=User&m=ServicestoreNew&a=staff&token='.$this->token);
                    } else {
                        $this->error('添加失败', 'index.php?g=User&m=ServicestoreNew&a=managerStaff&op=0&token='.$this->token);
                    }
                }

            } elseif (1 == $operation) {
                $data['id'] = $_POST['id'];
               $staff = $staffModel->where(array('id'=>$_POST['id']))->find();
                if ($staffModel->where(array('id'=>$_POST['id']))->save($acceptData)) {
                    if($_POST['status'] ==1){
                        msg($this->token,$staff['openid'],"您申请加入“云南人与自然车业连锁机构”员工审核通过！");
                    }elseif($_POST['status'] ==2){
                        msg($this->token,$staff['openid'],"由于您不是“云南人与自然车业连锁机构”的员工，审核未通过！");
                    }
                    $this->success('编辑成功','./index.php?g=User&m=ServicestoreNew&a=staff&token='.$this->token);
                } else {
                    $this->error('编辑失败', './index.php?g=User&m=ServicestoreNew&a=managerStaff&op=1&token='.$this->token.'&id='.$data['id']);
                }
            }
        }
        if (1 == $operation) {
            $staffId = $_GET['id'];
            $is_exist = $staffModel->where(array('id'=>$staffId))->find();
            if ($is_exist == true) {
                $this->assign('token', $this->token);
                $this->assign("data", $is_exist);
            }
        }
        $storeInfoData = $this->serviceStoreModel->where(array('wxuser_id'=>$this->userInfoData['id']))->select();
        $this->assign('storeInfoData', $storeInfoData);
        $this->assign('op',$operation);
        $this->display();
    }

    /*显示所有员工信息*/
    public function staff() {
        $staffModel = M('staff');
// 		$staffInfoDatas = $staffModel->join('tp_service_store on tp_staff.belong_id = tp_service_store.id')->select();//这样有比较多的字段相同
        if(IS_POST){
            if($_POST['name']){
                $aWhere = array(
                    'wxuser_id'=>$this->userInfoData['id'],
                    'name' =>array('like','%'.$_POST['name'].'%')
                );
            }else{
                $aWhere = array('wxuser_id'=>$this->userInfoData['id']);
            }
        }else{
            $aWhere = array('wxuser_id'=>$this->userInfoData['id']);
        }
        $count = $staffModel->where($aWhere)->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $staffInfoDatas = $staffModel->where($aWhere)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($staffInfoDatas as $key => $value) {
            $storeName = $this->serviceStoreModel->where(array('id'=>$value['belong_id']))->find();
            $staffInfoDatas[$key]['belong_id'] = $storeName['name'];
        }
        $this->assign('staffInfoDatas', $staffInfoDatas);
        $this->assign('page',$show);
        //print_r($staffInfoDatas);exit;
        $this->display();
    }
    /*员工信息生成二维码*/
    public function getstaff(){
        $staff = M('staff')->where(array('token'=>$this->token,'id'=>$_GET['id']))->find();
        $parament = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 160'.$staff['id'].'}}}';
	$Code = new Code($this->token, '160'.$staff['id']);
        /*获取access_token*/
        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        if($api){
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
            $json = json_decode(file_get_contents($url_get));
            $access_token = $json->access_token;
            $imgSource = $this->creatTicket($access_token, $parament);
        }
        $this->assign('staff',$staff);
        //$this->assign('imgUrl', $imgSource['header']['url']);
	$this->assign('imgUrl', $Code->getYJCode());
        $this->display();
    }
    public function creatTicket($token, $parament) {

        /*发送数据到微信服务器端并获取数据*/
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
        $result = $this->api_notice_increment($url, $parament);
        $jsonInfo = json_decode($result, true);
        $ticket = $jsonInfo['ticket'];

        /*根据ticket获取图片资源*/
        $url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpInfo = curl_getinfo($ch);
        return array_merge(array('body'=>$package), array('header'=>$httpInfo));
    }

    /*删除员工*/
    public function delStaff() {

        if (IS_POST) {
            $staffId = $_GET['id'];
            $staffModel = M('staff');
            $staffInfoDatas = $staffModel->where(array('id'=>$_GET['id']))->find();
            if (!empty($staffInfoDatas)) {

                if ($staffModel->where(array('id'=>$_GET['id']))->delete()) {
                    /*删除评价信息*/
                    $appraiseModel = M('appraise');
                    $appraiseModel->where(array('staff_id'=>$_GET['id']))->delete();
                    $this->success('删除成功','./index.php?g=User&m=ServicestoreNew&a=index&token='.$this->token);
                } else {
                    $this->error('删除失败','./index.php?g=User&m=ServicestoreNew&a=showStaff&token'.$this->token);
                }
            } else {
                $this->error('不存在该用户，删除失败','./index.php?g=User&m=ServicestoreNew&a=showStaff&token'.$this->token);
            }
        }
        $this->error('非法提交数据，删除失败','./index.php?g=User&m=ServicestoreNew&a=showStaff&token'.$this->token);
    }
    //评价查看；
    public function evaluation(){
        $token = $this->token;
        $staffID = $_GET['staffID'];
        $count = M('Service_appraise')->where(array('token'=>$token,'staffID'=>$staffID))->count();
        $Page = new Page($count,15);
        $show = $Page->show();
        $staffSee = M('Service_appraise')->where(array('token'=>$token,'staffID'=>$staffID))->order('etime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('see',$staffSee);
        $this->assign('page',$show);
        $this->display();
    }

    /*
     * 积分设置
     * */
    public function integralInstall(){
        $integral = M('Service_integral');
        $token = $this->token;
        $info = $integral->where(array('token'=>$token))->find();
//        print_r($info);exit;
        $this->assign('info',$info);
        if(IS_POST){
 //{id:id,store:store,stores:stores,stors:stors,integral:integral,token:token,op:op},
            $condition['token'] = $_POST['token'];
            $condition['store'] = $_POST['store'];
            $condition['stores'] = $_POST['stores'];
            $condition['stors'] = $_POST['stors'];
            $condition['integral'] = $_POST['integral'];
            $condition['id'] = isset($_POST['id'])?$_POST['id']:'';
           // print_r($condition);exit;
            if($_POST['id']){
                $integralsave = $integral->where(array('token'=>$token,'id'=>$condition['id']))->save($condition);
                if($integralsave){
                    $this->success('编辑成功','./index.php?g=User&m=ServicestoreNew&a=integralInstall&token='.$this->token.'&id='.$condition['id']);
                }else{
                    $this->error('编辑失败','./index.php?g=User&m=ServicestoreNew&a=integralInstall&token='.$this->token);
                }
            }else{
                $integralto = $integral->data($condition)->add();
                if($integralto){
                    $this->success('设置成功','./index.php?g=User&m=ServicestoreNew&a=integralInstall&token='.$this->token);
                }else{
                    $this->success('设置失败','./index.php?g=User&m=ServicestoreNew&a=integralInstall&token='.$this->token);
                }
            }

        }

        $this->display();
    }

/*
 * 积分商城
 * */

    public function store(){
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
                        $this->success('操作成功！',U(MODULE_NAME.'/store',array('token'=>session('token'))));
                    }else{
                        $this->error('操作失败！',U(MODULE_NAME.'/store',array('token'=>session('token'))));
                    }
                }else{
                    $this->error('非法操作！',U(MODULE_NAME.'/store',array('token'=>session('token'))));
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
        $_POST['giftkey'] = 'Tai'.time();
        $model = M('Integralshop');
        $result=$model->add($_POST);
//        print_r($result);exit;
        if($result){
            $this->success('操作成功！','./index.php?g=User&m=ServicestoreNew&a=store&token='.$this->token);
        }else{
            $this->error('操作失败！','./index.php?g=User&m=ServicestoreNew&a=append&token='.$this->token);
        }
    }
    public function revise(){
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

    public function save(){
        $where = array('token'=> session('token'),'id' => $_GET['id']);
        $model=M('Integralshop');
        $_POST['token'] = $_REQUEST['token'];
        $_POST['giftkey'] = 'wp'.time();

        $result=$model->where($where)->save($_POST);
        if ($result) {
            $this->success('编辑成功！','./index.php?g=User&m=ServicestoreNew&a=store&token='.$this->token.'&id='.$_GET['id']);
        }else{
            $this->error('编辑失败！','./index.php?g=User&m=ServicestoreNew&a=revise&token='.$this->token);
        }
    }

    public function duihuan(){
        if(M('integralshop_individual')->where(array('id'=>$_GET['id']))->save(array('is_use'=>2))){
            $this->success2('成功');
        }else{
            $this->error2('失败');
        }
    }
    public function on_uses(){
        if(M('integralshop_individual')->where(array('id'=>$_GET['id']))->save(array('is_use'=>3))){
            $this->success2('成功');
        }else{
            $this->error2('失败');
        }
    }
    public function duihuans(){
        $info = M('integralshop_individual')->where(array('id'=>$_GET['id']))->find();
        $tactice = M('Service_profile')->where(array('token'=>$this->token,'openid'=>$info['openid']))->save(array('is_locks'=>0));
        $oactive =  M('integralshop_individual')->where(array('id'=>$_GET['id']))->save(array('is_use'=>2));
        if($oactive && $tactice){
            $this->success2('成功');
        }else{
            $this->error2('失败');
        }
    }


    public function delstore(){

        $id=$this->_get('id','intval');
        $token=$this->_get('token');
        $where['id']=$id;
        $where['token']=$token;
        $db=M('Integralshop');
        $result=$db->where($where)->delete();
        if ($result) {
            $this->success('操作成功！','./index.php?g=User&m=ServicestoreNew&a=store&token='.$this->token);
        }else{
            $this->error('操作失败！','./index.php?g=User&m=ServicestoreNew&a=store&token='.$this->token);
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
            $where = $where."AND (u.user_phone = '$phone')";
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

        $list = $model->query("SELECT tp_integralshop_individual.*,u.car_username as name,u.id as member_sn,u.user_phone as phone,u.is_locks,i.giftname,i.giftkey as gid,i.num,i.integral,i.is_lock FROM
                          `tp_integralshop_individual` left join tp_service_profile as u on
                          tp_integralshop_individual.openid = u.openid left join
                          tp_integralshop as i on tp_integralshop_individual.lid = i.id
                           WHERE $where ORDER BY tp_integralshop_individual.time desc");

        foreach($list as $k=>$val){
            $usesum = intval($model->where(array('token'=>$this->token,'lid'=>$val['lid']))->count());
            $yusum = $val['num'] - $usesum;
            if($yusum==0){
                M('Integralshop')->where(array('id'=>$val['lid']))->save(array('is_up'=>2));
            }
            $staffModel = M('staff');
            $staffinfo = $staffModel->where(array('token'=>$this->token,'staff_id'=>$val['staffID']))->find();
            $list[$k]['staffIDs'] = $staffinfo['name'];
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

        $this->display('reveal');


    }

    /*会员管理*/
    public function menber(){
        $model = M('Service_profile');
        if(IS_POST){
            if($_POST['car_username']){
                $aWhere = array(
                    'token'=>$this->token,
                    'car_username'=>array('like','%'.$_POST['car_username'].'%')
                );
            }else{
                $aWhere = array('token'=>$this->token);
            }
        }else{
            $aWhere = array('token'=>$this->token);
        }
        $count = $model->where($aWhere)->count();
        $Page = new Page($count,10);
        $show = $Page->show();
        $list = $model->where($aWhere)->limit($Page->firstRow.','.$Page->listRows)->order('register_time desc')->select();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display();
    }
    /*会员信息详情*/
    public function menberinfo(){
        $oModel = M('Service_profile');
        if(IS_AJAX){
            $iTem = $oModel->where(array('id'=>$_POST['id']))->find();
            if(!$iTem) $this->error('非法操作！');
            if($oModel->where(array('id'=>$_POST['id']))->save($_POST)){
                if($_POST['type'] ==2){
                    $isFind = M('Staff')->where(array('wxuser_id'=>$this->userInfoData['id'],'openid'=>$iTem['openid']))->find();
                    if(!$isFind){
                        M('Staff')->add(array(
                            'wxuser_id'=>$this->userInfoData['id'],
                            'openid'=>$iTem['openid'],
                            'name' => $_POST['car_username'],
                            'telephone' =>$_POST['user_phone']
                        ));
                    }
                }

                $this->success('更改成功！','./index.php?g=User&m=ServicestoreNew&a=menber&token='.$this->token);
            }else{
                $this->error('更改失败！','./index.php?g=User&m=ServicestoreNew&a=menber&token='.$this->token);
            }
        }else{
            $iTems = $oModel->where(array('id'=>$_GET['id']))->find();
            $this->assign(array(
                'aInfo'=>$iTems,
                'ExtraBtn' => array(
                    array(
                        'url'  => U('ServicestoreNew/menber', array('token' => $this->token)),
                        'name' => '返回'
                    )
                )
            ));
            $this->display();
        }
    }
    /*删除会员*/
    public function delmen(){
        $model = M('Service_profile');
        $delmenber = $model->where(array('token'=>$this->token,'id'=>$_GET['pid']))->delete();
        if($delmenber){
            $this->success('操作成功！','./index.php?g=User&m=ServicestoreNew&a=menber&token='.$this->token);
        }else{
            $this->error('操作失败！','./index.php?g=User&m=ServicestoreNew&a=menber&token='.$this->token);
        }
    }

}
