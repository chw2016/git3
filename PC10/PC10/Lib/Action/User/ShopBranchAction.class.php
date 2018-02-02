<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * Tel：15889394741
 * Notice:O2O分店登陆
 * Date: 2014/12/30
 * Time: 10:23
 */
class ShopBranchAction extends UserAction{
    public $branch_id;
    public $token;
    static public $treeList = array();
    static public $bigtreeList = array();

    public function _initialize() {
        parent::_initialize();
        $this->branch_id=$this->_get("branch_id","intval");
        $this->token=$this->_get("token");
        if($this->branch_id!=session("branch_id") || !isset($_GET['branch_id'])){
            $this->redirect(U('Branch/index',array('token'=>$this->token,'modulename'=>"Shop")));
            exit;
        }
        $shopdata = M('Shop')->where(array('id'=>$this->branch_id))->find();
        $this->assign("token",$this->token);
        $this->assign("shopdata",$shopdata);
        $this->assign("branch_id",$this->branch_id);
    }


    //分店商品分类列表
    public function classify(){
        $data=M("Shopclassfy")->where(array("branch_id"=>$this->branch_id))->order("sort asc")->select();
        $list=self::tree($data);
        $this->assign("list",$list);
        $this->display();
    }

    //添加分类
    public function AddClassify(){
        if(IS_POST){
            $_POST['branch_id']=$this->_get("branch_id","intval");
            if(M("Shopclassfy")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));

            }

        }else{
            $shop = M('Shop')->where(array('id'=>$this->branch_id))->find();
            /*
             * 平台分类
             */
            $data=M("Shopclassfy_all")->where(array("token"=>$this->token,'cid'=>$shop['tid']))->order("id desc")->select();

            $biglist=self::bigtree($data);



            $cats=M("Shopclassfy")->where(array("branch_id"=>$this->branch_id))->order("id desc")->select();
            $list=self::tree($cats);
            $this->assign("cats",$list);
            $this->assign("bigcats",$biglist);

            $this->display();
        }
    }

    //编辑分类
    public function EditClassify(){
        if(IS_POST){
            if($_POST['pid']==$_POST['id']){
                $this->ajaxReturn(array("status"=>0,"info"=>"操作错误"));
            }
            if(M("Shopclassfy")->where(array("id"=>$_POST['id']))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            $data=M("Shopclassfy")->where(array("id"=>$this->_get("id","intval")))->find();
            if($data['pid']){
                $cinfo=M("Shopclassfy")->field("id")->where(array("id"=>$data['pid']))->find();
            }
            $cats=M("Shopclassfy")->where(array("branch_id"=>$this->branch_id))->order("id desc")->select();
            $list=self::tree($cats);
            foreach($list as $k=>$v){
                $disable="";
                $selected="";
                //禁用本身
                if($v['id']==$data['id']){
                    $disable="disabled=''";
                }
                if($v['id']==$cinfo['id']){
                    $selected="selected=''";
                }
                //子栏目禁用
                if(self::check($data['id'],$v['id'])){
                    $disable="disabled=''";
                }
                $list[$k]['disabled']=$disable;
                $list[$k]['selected']=$selected;
            }

            $shop = M('Shop')->where(array('id'=>$this->branch_id))->find();

            /*
             * 平台分类
             */
            $bigdata=M("Shopclassfy_all")->where(array("token"=>$this->token,'cid'=>$shop['tid']))->order("id desc")->select();

            $biglist=self::bigtree($bigdata);


            $this->assign("bigcats",$biglist);

            $this->assign("cats",$list);
            $this->assign("data",$data);
            $this->display();
        }
    }

    //判断分类是否合法
    //par为当前栏目id  循环体栏目id
    static public function check($par,$son){
        $cat=M("Shopclassfy")->field("pid")->where(array("id"=>$son))->find();
        if($cat['pid']!=0){
            if($cat['pid']==$par){
                return true;
            }else{
                $pars=M("Shopclassfy")->field("id")->where(array("id"=>$cat['pid']))->find();
                return self::check($par,$pars['id']);
            }
        }
    }

    //删除分类
    public function DelClassify(){
        if(M("Shopware")->where(array("sid"=>$this->_get("id","intval")))->find()){
            $this->error2("当前分类下还有商品，请先删除商品再执行此操作!");
        }
        if(M("Shopclassfy")->where(array("id"=>$this->_get("id","intval")))->delete()){
            $this->success2("删除成功");
        }else{
            $this->error2("删除失败");
        }
    }

    //无限分类函数
    static public function tree(&$data,$pid = 0,$count = 1) {
        foreach ($data as $key => $value){
            if($value['pid']==$pid){
                $value['Count'] = $count;
                self::$treeList []=$value;
                unset($data[$key]);
                self::tree($data,$value['id'],$count+1);
            }
        }
        return self::$treeList ;
    }


    static public function bigtree(&$data,$pid = 0,$count = 1) {

        foreach ($data as $key => $value){

            if($value['pid']==$pid){

                $value['Count'] = $count;

                self::$bigtreeList []=$value;

                unset($data[$key]);

                self::bigtree($data,$value['id'],$count+1);

            }

        }

        return self::$bigtreeList ;

    }



    //商品列表
    public function ware(){
        $data=M("Shop")->field("mid")->where(array("token"=>$this->token,"id"=>$this->branch_id))->find();
        $condition['sid'] = $this->branch_id;
        $condition['token'] = $this->token;
        $mid=$data['mid'];
        if(isset($_REQUEST['tname'])){
            $condition['name'] = array('like','%'.$_REQUEST['tname'].'%');
        }

        if(isset($_REQUEST['tid']) && $_REQUEST['tid'] != ''){
            $condition['tid'] = $_REQUEST['tid'];
        }

        $count=M("Shopware")->field("id")->where(array("sid"=>$this->branch_id,'token'=>$this->token))->count();

        $page=new Page($count,20);

        $data=M("Shopware")->field("id,name,attr,des,pic,status,stock,sort")->where($condition)->order('sort,stock asc')->select();
        $cateclass = M("Shopclassfy")->where(array('branch_id'=>$this->branch_id))->select();
        $this->assign("list",$data);

        $this->assign("cateclass",$cateclass);
        $this->assign("mid",$mid);
        $this->assign("tid",$_REQUEST['tid']);
        $this->assign("tname",$_REQUEST['tname']);

        $this->display();
    }

    //添加商品
    public function AddWare(){
        if(IS_POST){
            $_POST['token']=$this->_get("token");
            $_POST['sid']=$this->_get("branch_id","intval");
            $_POST['images']=substr($_POST['images'],1,-1);
            $_POST['mid']=$this->_get("mid","intval");
            $flashes=explode(",",htmlspecialchars_decode($_POST['images']));
            if($id=M("Shopware")->add($_POST)){
                foreach($flashes as $v){
                    M("Shopflash")->add(array("pid"=>$id,"flash"=>$v));
                }
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));
            }
        }else{
            $mid=$this->_get("mid","intval");
            $member=M("Shopmember")->field("id,username")->where(array("token"=>$this->token,"id"=>$mid))->find();
            $classfy=M("Shopclassfy")->field("id,tname,pid")->where(array("branch_id"=>$this->_get("branch_id","intval")))->order("id desc")->select();
            $list=self::tree($classfy);
            $this->assign("list",$list);
            $this->assign("member",$member);
            $this->display();
        }
    }

    //修改商品
    public function EditWare(){
        if(IS_POST){
            $_POST['images']=substr($_POST['images'],1,-1);
            $flashes=explode(",",htmlspecialchars_decode($_POST['images']));
            $images=M("Shopflash")->where(array("pid"=>$_POST['id']))->select();
            if(M("Shopware")->where(array("id"=>$_POST['id']))->save($_POST)){
                foreach($images as $v){
                    unset($v['flash']);
                }
                if(M("Shopflash")->where(array("pid"=>$_POST['id']))->delete()){
                    foreach($flashes as $v){
                        M("Shopflash")->add(array("pid"=>$_POST['id'],"flash"=>$v));
                    }
                    $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
                }
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            $classfy=M("Shopclassfy")->field("id,tname,pid")->where(array("branch_id"=>$this->branch_id))->order("id desc")->select();
            $data=M("Shopware")->where(array("id"=>$this->_get("id","intval")))->find();
            $images=M("Shopflash")->where(array("pid"=>$this->_get("id","intval")))->select();
            $list=self::tree($classfy);
            foreach($list as $k=>$v){
                if($v['id']==$data['tid']){
                    $list[$k]['selected']="selected=''";
                }
            }
            $this->assign("images",$images);
            $this->assign("data",$data);
            $this->assign("list",$list);
            $this->display();
        }
    }

    //删除商品
    public function DelWare(){
        $data=M("Shopware")->where(array("id"=>$this->_get("id","intval")))->find();
        if(M("Shopware")->where(array("id"=>$this->_get("id","intval")))->save(array('status'=>0))){
            $this->success2("下架成功",U('ShopBranch/ware',array('token'=>$this->token,'branch_id'=>$this->branch_id)));
        }else{
            $this->error2("下架失败");
        }
    }

    //上架商品

    //删除商品
    public function UpWare(){
        $data=M("Shopware")->where(array("id"=>$this->_get("id","intval")))->find();
        if(M("Shopware")->where(array("id"=>$this->_get("id","intval")))->save(array('status'=>1))){
            $this->success2("上架成功",U('ShopBranch/ware',array('token'=>$this->token,'branch_id'=>$this->branch_id)));
        }else{
            $this->error2("上架失败");
        }
    }

    //我的账户
    public function account(){
        /*$condition['tp_sideorder.token']=$this->token;
        $condition['tp_sideorder.sid']=$this->branch_id;
	    $condition['tp_sideorder.paytype']=array('in','1,2');
        $condition['tp_sideorder.paystatus|tp_sideorder.sendstatus']=array('1','2','_multi'=>true);
        $totalGet=M("Sideorder")->join("right join tp_sidedetail on tp_sideorder.id=tp_sidedetail.sid")->where($condition)->sum("tp_sidedetail.total");
        $shopInfo=M("Shop")->field("rate")->where(array("id"=>$this->branch_id))->find();
        $shopRate=$shopInfo['rate'];
        $shopPayRate=$totalGet*$shopRate;
//        dump($shopPayRate);exit;
        $getRate=M("Shoprate")->where(array("sid"=>$this->branch_id,"status"=>1))->sum("account");
        $canRate=$totalGet-$shopPayRate-$getRate;
        */


        $mainorderModel = M("Mainorder");
        $shopInfo=M("Shop")->field("rate")->where(array("id"=>$this->branch_id))->find();
        /*
         * 总额
         */
        $condition['token']=$this->token;
        $condition['shopid']=array('like','|'.$this->branch_id.'|');
        $condition['paystatus']=1;
        $totalGet = $mainorderModel->where($condition)->sum('totalmoney');
        if(!$totalGet){
            $totalGet = 0;
        }

        $condition1['token']=$this->token;
        $condition1['shopid']=array('like','|'.$this->branch_id.'|');
        $condition1['paystatus']=1;
        $condition1['paytype']=3;

        //线上
        $offlinemoney = $mainorderModel->where($condition1)->sum('totalmoney');
        if(!$offlinemoney){
            $offlinemoney = 0;
        }

        $condition2['token']=$this->token;
        $condition2['shopid']=array('like','|'.$this->branch_id.'|');
        $condition2['paystatus']=1;
        $condition2['paytype']=array('in',array(1,2));
        $onlinemoney = $mainorderModel->where($condition2)->sum('totalmoney');
        if(!$onlinemoney){
            $onlinemoney = 0;
        }

        $this->assign('totalget',$totalGet);
        $this->assign('onlinemoney',$onlinemoney);
        $this->assign('offlinemoney',$offlinemoney);
        $this->assign('cangetmoney',$onlinemoney*(1-$shopInfo['rate']));
        $this->display();
    }

    //申请提现
    public function ApplyCount(){
        if(IS_AJAX){
            $condition['tp_sideorder.token']=$this->token;
            $condition['tp_sideorder.sid']=$this->branch_id;
	    $condition['tp_sideorder.paytype']=array('in','1,2');
            $condition['tp_sideorder.paystatus|tp_sideorder.sendstatus']=array('1','2','_multi'=>true);
            $totalGet=M("Sideorder")->join("right join tp_sidedetail on tp_sideorder.id=tp_sidedetail.sid")->where($condition)->sum("tp_sidedetail.total");
            $shopInfo=M("Shop")->field("rate")->where(array("id"=>$this->branch_id))->find();
            $shopRate=$shopInfo['rate'];
            $shopPayRate=$totalGet*$shopRate;
            $getRate=M("Shoprate")->where(array("sid"=>$this->branch_id,"status"=>1))->sum("account");
            $canRate=$totalGet-$shopPayRate-$getRate;
            if(floatval($canRate)>=$_POST['account']){
                $add['token']=$this->token;
                $add['sid']=$this->branch_id;
                $add['jtime']=date("Y-m-d H:i:s");
                $add['account']=$_POST['account'];
                if(M("Shoprate")->add($add)){
                    $this->ajaxReturn(array("status"=>0,"info"=>"操作成功!"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
                }
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"超过最大限额"));
            }
        }else{
            $this->display();
        }
    }

    //提现记录
    public function ApplyList(){
           $where['token']=$this->token;
           $where['sid']=$this->branch_id;
//           $where['status']=$this->_get("status","intval");
           $data=M("Shoprate")->field("id,jtime,account,status")->where($where)->select();
           $this->assign("data",$data);
           $this->display();
    }

    //轮播图列表
    public function Flash(){
        $list=M("Oflash")->field("id,pic,url,title,status")->where(array("token"=>$this->token,"type"=>4))->select();
        $this->assign("list",$list);
        $this->display();
    }

    //添加轮播图
    public function AddFlash(){
        if(IS_AJAX){
            $_POST['tid']=$this->branch_id;
            $_POST['token']=$this->token;
            $_POST['type']=4;
            $_POST['status']=0;
            if(M("Oflash")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>"失败"));
            }
        }else{
            $this->display();
        }
    }

    //修改轮播图
    public function EditFlash(){
        if(IS_AJAX){
            $where['token']=$this->token;
            $where['id']=$this->_get("id","intval");
            $where['tid']=$this->branch_id;
            if(M("Oflash")->where($where)->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            $id=$this->_get("id","intval");
            if(!$id){
                exit("非法操作!");
            }
            $data=M("Oflash")->field("id,pic,url,status,title")->where(array("token"=>$this->token,"tid"=>$this->branch_id,"id"=>$id))->find();
            if($data['status']==1){
                exit("已经审核通过，不能再修改!");
            }
            $this->assign("id",$id);
            $this->assign("data",$data);
            $this->display();
        }
    }

    //删除轮播
    public function DelFlash(){
        $id=$this->_get("id","intval");
        if(!$id){
            exit("非法操作!");
        }
        if(M("Oflash")->where(array("token"=>$this->token,"id"=>$id,"tid"=>$this->branch_id))->delete()){
            $this->success2("删除成功!");
        }else{
            $this->error2("删除失败");
        }
    }

    /*滚动文字设置*/
    public function writing(){

        if(IS_AJAX){
            $data = M('Shop')->where(array('token'=>$this->token,'id'=>$_GET['branch_id']))->find();
            if($data){
                if(M('Shop')->where(array('token'=>$this->token,'id'=>$_GET['branch_id']))->save($_POST)){
                    $this->ajaxReturn(array("status"=>1,"info"=>"操作成功"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
                }
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
            }
        }else{
            $list = M('Shop')->field('min_price,des,start_time,end_time,yingye_status,waimai_price,print_key,print_domain')->where(array('id'=>$_GET['branch_id'],'token'=>$this->token))->find();
            $this->assign('list',$list);
            $this->display();
        }

    }

    //员工管理
    public function cStaff(){
        $this->display();
    }

    //添加员工
    public function AddStaff(){
        echo 123;
    }

    public function changepwd(){
        if(IS_POST) {
            $oldpasswd = $_POST['oldpasswd'];
            $newpasswd = $_POST['newpasswd'];
            if ($res = M('Shop')->where(array('id' => $_GET['branch_id']))->find()) {
                if ($res['password'] == md5($oldpasswd)) {
                    if (M('Shop')->where(array('id' => $_GET['branch_id']))->save(array('password' => md5($newpasswd)))) {
                        $this->success2("操作成功!");
                    } else {
                        $this->error2("您没有修改密码!");
                    }
                } else {
                    $this->error2("老密码输入错误!");
                }
            } else {
                $this->error2("操作失败!");
            }
        }else{
            $this->display();
        }
    }
    public function activitise(){
        $sToken = $this->token;
        $iBranchid = $_GET['branch_id'];
        $oActivitiseModel = M('Shop_activities');
        $iCount = $oActivitiseModel->where(array(
            'token'=>$sToken,
            'branch_id'=>$iBranchid
        ))->count();
        $page = new Page($iCount,15);
        $show = $page->show();
        $aActivitise = $oActivitiseModel->where(array(
            'token'=>$sToken,
            'branch_id'=>$iBranchid
        ))->order('add_time desc')->limit(
            $page->firstRow.','.$page->listRows)->select();
        $this->assign(array(
            'activitise'=>$aActivitise,
            'page'=>$show
        ));
        $this->display();
    }
    public function cretaeactivi(){
        $iAid = isset($_GET['aid'])?$_GET['aid']:'';
        $sToken = $this->token;
        $oActivitiseModel = M('Shop_activities');
        $oShopModel = M('Shopware');  
        if(IS_AJAX){
            $iPid = isset($_POST['id'])?$_POST['id']:'';
            $_POST['branch_id'] = $_GET['branch_id'];
	    $_POST['token'] = $sToken;
            if(!$iPid){
                $_POST['add_time'] = date('Y-m-d H:i:s',time());
                $aCitise = $oActivitiseModel->where(array(
                    'token'=>$sToken,
                    'title'=>$_POST['title'],
                    'endtime'=>array('gt',$_POST['starttime'])
                ))->select();
                $aCitise = isset($aCitise)?$aCitise:array();
                if(!$aCitise){
                    if($oActivitiseModel->add($_POST)){
                        $this->ajaxReturn(array("status"=>1,"info"=>"操作成功"));
                    }else{
                        $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
                    }
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"活动已存在"));
                }
            }else{
	        $id = $_POST['id'];
	        unset($_POST['id']);
                if($oActivitiseModel->where(array(
                    'id'=>$id,
                    'token'=>$sToken
                ))->save($_POST)){
                    $this->ajaxReturn(array("status"=>1,"info"=>"编辑成功"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"编辑失败"));
                }
            }
        }else{
            if($iAid){
                $this->assign(array(
                    'info'=>$oActivitiseModel->where(array(
                        'id'=>$iAid,
                        'token'=>$sToken
                    ))->find(),
                    'product'=>$oShopModel->where(array(
                        'token'=>$sToken,
                        'sid'=>$_GET['branch_id']
                    ))->select()
                ));
                $this->display();
            }else{
                $this->assign(array(
                    'product'=>$oShopModel->where(array(
                        'token'=>$sToken,
                        'sid'=>$_GET['branch_id']
                    ))->select()
                ));
		$this->display();
            }
        }
    }

    public function delactivitise(){
        $iAid = isset($_GET['aid'])?$_GET['aid']:'';
        $sToken = $this->token;
        $oActivitiseModel = M('Shop_activities');
        if($oActivitiseModel->where(array(
            'id'=>$iAid,
            'token'=>$sToken
        ))->delete()){
            $this->success2("删除成功",U('ShopBranch/activitise',array('token'=>$this->token,'branch_id'=>$this->branch_id)));
        }else {
            $this->error2("删除失败");
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
        /*if($_REQUEST['branch_id']){
            $shop_id = $_REQUEST['branch_id'];
            $where = $where."AND (tp_integralshop_individual.shop_id ='$shop_id')";
            //$this->assign('shop_id',$_POST['shop_id']);
        }*//*if($_REQUEST['branch_id']){
            $shop_id = $_REQUEST['branch_id'];
            $where = $where."AND (tp_integralshop_individual.shop_id ='$shop_id')";
            //$this->assign('shop_id',$_POST['shop_id']);
        }*/

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
//P($list);exit;
        /*统计数据*/

        $count      = count($list);
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display('reveal');
    }
    /*领取礼品*/
    public function usegife(){
        $ointModel = M('Integralshop_individual');
        if(IS_AJAX){
            $iTem = $ointModel->where(array('id'=>$_POST['id']))->find();
            if($iTem){
                $_POST['shop_id'] = $this->branch_id;
                $_POST['use_time'] = date('Y-m-d H;i:s');
                $isUse = $ointModel->where(array('id'=>$_POST['id']))->save($_POST);
                if($isUse){
                    $this->success('操作成功！',U(MODULE_NAME.'/reveal',array('token'=>$this->token,'branch_id'=>$this->branch_id)));
                }else{
                    $this->error('系统繁忙，请稍后！',U(MODULE_NAME.'/reveal',array('token'=>$this->token,'branch_id'=>$this->branch_id)));
                }

            }else{
                $this->error('非法操作！',U(MODULE_NAME.'/reveal',array('token'=>$this->token,'branch_id'=>$this->branch_id)));
            }
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