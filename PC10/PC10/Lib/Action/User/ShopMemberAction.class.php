<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * Tel:15889394741
 * Notice:分店管理
 * Date: 2015/1/4
 * Time: 14:10
 */
class ShopMemberAction extends UserAction{
    public $token;
    public $member_id;
    public function _initialize() {
        parent::_initialize();
        $this->member_id=$this->_get("member_id","intval");
        $this->token=$this->_get("token");
        if($this->member_id!=session("member_id") || !isset($_GET['member_id'])){
            $this->redirect(U('Branch/index',array('token'=>$this->token,'modulename'=>"Shopmember")));
            exit;
        }
        $shopmemberdata = M('Shopmember')->where(array('id'=>$this->member_id))->find();
        $this->assign("token",$this->token);
        $this->assign("shopmemberdata",$shopmemberdata);
        $this->assign("member_id",$this->member_id);
    }

    //管理员店铺列表
    public function index(){
        $where['token'] = $this->token;
        $where['mid'] = $this->member_id;
        if(isset($_GET['type'])) {
            if (isset($_GET['username'])) {
                $where['username'] = array('like', '%' . $this->_get("username") . '%');
            }
        }else{
            $where['token'] = $this->token;
            $where['mid'] = $this->member_id;
        }

        $count=M("Shop")->where($where)->count();
        $page=new Page($count,20);
        $list=M("Shop")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign("page",$page->show());
        $this->assign("list",$list);
        $this->display();
    }

    //管理员添加店铺
    public function AddShop(){
        if(IS_POST){
            if(M("Shop")->where(array("token"=>$this->token,'username'=>$_POST['username']))->find()){
                $this->ajaxReturn(array("status"=>0,"info"=>"该店铺已经存在!"));
            }
            $_POST['password']=md5($_POST['password']);
            $_POST['mid']=$this->member_id;
            $_POST['token']=$this->token;
            if(M("Shop")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>"添加失败"));
            }
        }else{
            $union=M("Shopunion")->field("id,cname")->where(array("token"=>$this->token,'mid'=>$this->member_id))->select();
            $ShopType=M("Shoptype")->field("id,name")->where(array("token"=>$this->token))->select();
            $area=M("Shopmember")->field("seng,si,xian")->where(array("token"=>$this->token,"id"=>$this->member_id))->find();
            $this->assign("area",$area);
            $this->assign("shoptype",$ShopType);
            $this->assign("union",$union);
            $this->display();
        }
    }

    //店铺修改
    public function EditShop(){
        if(IS_POST){
            if(!M("Shop")->where(array("token"=>$this->token,"mid"=>$this->member_id,"id"=>$_POST['id'],"password"=>md5($_POST['password'])))->find()){
                $this->ajaxReturn(array("status"=>0,"info"=>"修改成功"));
            }
            $_POST['token']=$this->token;
            $_POST['password']=md5($_POST['password']);
            if(M("Shop")->where(array("token"=>$this->token,"mid"=>$this->member_id,"id"=>$_POST['id']))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            $union=M("Shopunion")->field("id,cname")->where(array("token"=>$this->token,'mid'=>$this->member_id))->select();
            $ShopType=M("Shoptype")->field("id,name")->where(array("token"=>$this->token))->select();
            $data=M("Shop")->where(array("id"=>$this->_get("id","intval"),'token'=>$this->token,'mid'=>$this->member_id))->find();
            $area=M("Shopmember")->field("seng,si,xian")->where(array("token"=>$this->token,"id"=>$this->member_id))->find();
            $this->assign("area",$area);
            $this->assign("data",$data);
            $this->assign("union",$union);
            $this->assign("type",$ShopType);
            $this->display();
        }
    }

    //删除店铺


    //商品管理
    public function WareList(){
        $count=M("Shopware")->field("tp_shop.username,tp_shopware.id,tp_shopware.name,tp_shopware.price,tp_shopware.des,tp_shopware.pic,tp_shopware.status,tp_shopware.attr")->join("join tp_shop on tp_shopware.sid=tp_shop.id")->where(array("tp_shopware.token"=>$this->token,"tp_shopware.mid"=>$this->member_id))->count();
        $page=new Page($count,20);
        $data=M("Shopware")->field("tp_shop.username,tp_shopware.id,tp_shopware.name,tp_shopware.price,tp_shopware.des,tp_shopware.pic,tp_shopware.status,tp_shopware.attr")->join("join tp_shop on tp_shopware.sid=tp_shop.id")->where(array("tp_shopware.token"=>$this->token,"tp_shopware.mid"=>$this->member_id))->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign("list",$data);
        $this->assign("page",$page->show());
        $this->display();
    }

    //审核
    public function verify(){
        $condition=array();
        $save['status']=$this->_get("status","intval");
        $condition['id']    =$this->_get("id","intval");
        $condition['token'] =$this->token;
        $condition['mid']=$this->member_id;
        if(M("Shopware")->where($condition)->save($save)){
            $this->success2("操作成功");
        }else{
            $this->error2("操作失败");
        }
    }

    //区域管理
    public function Union(){
        $where['mid']=$this->member_id;
        $where['token']=$this->token;
        if(IS_POST){
            if(isset($_POST['cname'])){
                $where['cname']=array('like','%'.$_POST['cname'].'%');
            }
        }
        $count=M("Shopunion")->field("id,cname,des,pic,lat,long")->where($where)->count();
        $page=new Page($count,20);
        $data=M("Shopunion")->field("id,cname,des,pic,lat,long")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign("list",$data);
        $this->assign("page",$page->show());
        $this->display();
    }

    //添加社区
    public function AddUnion(){
        if(IS_AJAX){
            $_POST['mid']=$this->member_id;
            $_POST['token']=$this->token;
	    $where = array('mid'=>$this->member_id,'cname'=>$_POST['cname'],'token'=>$this->token);
            $data = M('Shopunion')->where($where)->find();
            if(!$data){
		    if(M("Shopunion")->add($_POST)){
	                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
	            }else{
	                $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));
	            }
	    }else{
	         $this->ajaxReturn(array("status"=>0,"info"=>"添加失败,同名社区已存在"));
	    }
        }else{
            $this->display();
        }
    }

    //修改社区
    public function EditUnion(){
        $id=$this->_get("id","intval");
        if(IS_AJAX){
            if(M("Shopunion")->where(array("token"=>$this->token,"id"=>$id,'mid'=>$this->member_id))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            if($id){
                $this->assign("id",$id);
            }else{
                exit("非法操作!");
            }
            $union=M("Shopunion")->where(array("token"=>$this->token,"id"=>$id,'mid'=>$this->member_id))->find();
            $this->assign("union",$union);
            $this->assign("id",$id);
            $this->display();
        }
    }

    //管理员账户
    public function account(){
        if(isset($_GET['type'])){
            $type=$this->_get("type","intval");
        }else{
            exit("访问错误");
        }
        $count=M("Shop")->field("id,mid,rate,username")->where(array("token"=>$this->token,'mid'=>$this->member_id,"stauts"=>1))->count();
        $page=new Page($count,20);
        $memberList=M("Shop")->field("id,mid,rate,username")->where(array("token"=>$this->token,'mid'=>$this->member_id,"status"=>1))->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach($memberList as $k=>$v){
            $condition['tp_sideorder.token']=$this->token;
            $condition['tp_sideorder.sid']=$v['id'];
            $condition['tp_sideorder.paystatus|tp_sideorder.sendstatus']=array('1','2','_multi'=>true);
            $memberList[$k]['totalget']=M("Sideorder")->join("join tp_sidedetail on tp_sidedetail.sid=tp_sideorder.id")->where($condition)->sum("tp_sidedetail.total");
            $rate=M("Shoprate")->where(array("token"=>$this->token,"sid"=>$v['id'],"status"=>1))->sum("account");
            if(!$rate){
                $rate=0;
            }
            $memberList[$k]['getrate']=$rate;
        }
        $totalget=0;
        $totalrate=0;
        $getrate=0;
        foreach($memberList as $k=>$v){
            if(!$v['totalget']){
                $v['totalget']=0;
            }
            $totalget+=floatval($v['totalget']);
            $totalrate+=floatval($v['totalget'])*floatval($v['rate']);
            $getrate+=floatval($v['getrate']);
        }

        $memberate=M("Shopmember")->field("rate")->where(array("token"=>$this->token,"id"=>$this->member_id))->find();
        $totalgetrates=M("Memberate")->where(array("token"=>$this->token,"mid"=>$this->member_id,"status"=>1))->sum("account");
        if(!$totalgetrates){
           $totalgetrates=0;
        }
        $payrate=floatval($memberate['rate'])*$totalget;
        $myCount=$totalget-$payrate-$totalgetrates;
        $shopCount=$totalget-$totalrate-$getrate;
        $this->assign("shopcount",$shopCount);
        $this->assign("totalgetrates",$totalgetrates);
        $this->assign("payrate",$payrate);
        $this->assign("rate",$memberate['rate']);
        $this->assign("totalget",$totalget);
        $this->assign("totalrate",$totalrate);
        $this->assign("getrate",$getrate);
        $this->assign("type",$type);
        $this->assign("mycount",$myCount);
        $this->assign("page",$page->show());
        $this->assign("memberList",$memberList);
        $this->display();
    }

    //渠道管理员申请提现
    public function ApplyCount(){
       if(IS_AJAX){
           $memberList=M("Shop")->field("id,mid,rate,username")->where(array("token"=>$this->token,'mid'=>$this->member_id,"status"=>1))->select();
           foreach($memberList as $k=>$v){
               $condition['tp_sideorder.token']=$this->token;
               $condition['tp_sideorder.sid']=$v['id'];
               $condition['tp_sideorder.paystatus|tp_sideorder.sendstatus']=array('1','2','_multi'=>true);
               $memberList[$k]['totalget']=M("Sideorder")->join("join tp_sidedetail on tp_sidedetail.sid=tp_sideorder.id")->where($condition)->sum("tp_sidedetail.total");
               $rate=M("Shoprate")->where(array("token"=>$this->token,"sid"=>$v['id'],"status"=>1))->sum("account");
               if(!$rate){
                   $rate=0;
               }
               $memberList[$k]['getrate']=$rate;
           }
           $totalget=0;
           $totalrate=0;
           $getrate=0;
           foreach($memberList as $k=>$v){
               $totalget+=floatval($v['totalget']);
               $totalrate+=floatval($v['totalget'])*floatval($v['rate']);
               $getrate+=floatval($v['getrate']);
           }
           $memberate=M("Shopmember")->field("rate")->where(array("token"=>$this->token,"id"=>$this->member_id))->find();
           $totalgetrates=M("Memberate")->where(array("token"=>$this->token,"mid"=>$this->member_id,"status"=>1))->sum("account");
           if(!$totalgetrates){
               $totalgetrates=0;
           }
           $payrate=floatval($memberate['rate'])*$totalget;
           //销售金额-佣金金额-已经申请提现金额
           $canrate=$totalget-$payrate-$totalgetrates;
           if(floatval($canrate)>floatval($_POST['account'])){
               $_POST['token']=$this->token;
               $_POST['jtime']=date("Y-m-d H:i:s");
               $_POST['account']=$_POST['account'];
               if(M("Memberate")->add($_POST)){
                   $this->ajaxReturn(array("status"=>1,"info"=>"操作成功"));
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

    //店铺提现记录
    public function CountList(){
        if(isset($_GET['status'])){
            if(isset($_GET['id'])){
                $id=$this->_get("id","intval");
            }else{
                exit("非法操作!");
            }
            $where['token']=$this->token;
            $where['status']=$this->_get("status","intval");
            $where['sid']=$id;
            $count=M("Shoprate")->field("id,account,jtime,status")->where($where)->count();
            $page=new Page($count,20);
            $data=M("Shoprate")->field("id,account,jtime,status")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
            $this->assign('id',$id);
            $this->assign("page",$page->show());
            $this->assign("data",$data);
            $this->display();
        }else{
            exit("非法操作!");
        }
    }

    //审核提现,是否超出提现范围,shoporders为订单总额
    public function CheckApply(){
        //查找该店铺可提现金额额度
        $sid=$this->_get("sid","intval");
        $id=$this->_get("id","intval");
        $condition['tp_sideorder.token']=$this->token;
        $condition['tp_sideorder.sid']=$sid;
        $condition['tp_sideorder.paystatus|tp_sideorder.sendstatus']=array('1','2','_multi'=>true);
        $shopOrders=M("Sideorder")->field("tp_sidedetail.total")->join("join tp_sidedetail on tp_sideorder.id=tp_sidedetail.sid")->where($condition)->sum("tp_sidedetail.total");
        $shopInfo=M("Shop")->field("rate")->where(array("id"=>$sid))->find();
        $shopRate=$shopInfo['rate'];
        $shopPayRate=$shopOrders*$shopRate;
        $getRate=M("Shoprate")->where(array("sid"=>$sid,"status"=>1))->sum("account");
        $canRate=$shopOrders-$shopPayRate-$getRate;
        $getRequestRate=M("Shoprate")->where(array("id"=>$id))->sum("account");
        if($getRequestRate>$canRate) {
            $this->error2("操作失败，金额不足!");
        }
        $where['token']=$this->token;
        $where['id']=$id;
        $save['status']=$this->_get("status","intval");
        if(M("Shoprate")->where($where)->save($save)){
            $this->success2("操作成功!");
        }else{
            $this->error2("操作失败!");
        }
    }

    //审核区域平台所有商品
    public function checkAll(){
        if(IS_AJAX){
            $id=substr($_POST['all'],0,-1);
            $where['token']=$this->token;
            $where['mid']=$this->member_id;
            $where['id']=array("exp","in({$id})");
            if(M("Shopware")->where($where)->save(array("status"=>1))){
                $this->ajaxReturn(array("status"=>1,"info"=>"批量审核成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
            }
        }else{
            exit("非法操作!");
        }
    }

    //轮播图列表
    public function Flash(){
        $list=M("Oflash")->field("id,pic,url,title,status")->where(array("token"=>$this->token,"type"=>3,"tid"=>$this->member_id))->select();
        $this->assign("list",$list);
        $this->display();
    }

    //添加轮播图
    public function AddFlash(){
        if(IS_AJAX){
            $_POST['tid']=$this->member_id;
            $_POST['token']=$this->token;
            $_POST['type']=3;
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
            $where['tid']=$this->member_id;
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
            $data=M("Oflash")->field("id,pic,url,status,title")->where(array("token"=>$this->token,"tid"=>$this->member_id,"id"=>$id))->find();
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
        if(M("Oflash")->where(array("token"=>$this->token,"id"=>$id,"tid"=>$this->member_id))->delete()){
            $this->success2("删除成功!");
        }else{
            $this->error2("删除失败");
        }
    }

    public function changepwd(){
        if(IS_POST) {
            $oldpasswd = $_POST['oldpasswd'];
            $newpasswd = $_POST['newpasswd'];
            if(!$_POST['phone']){
                $_POST['tel'] =$_POST['phone'];
            }
            if ($res = M('Shopmember')->where(array('id' => $this->member_id))->find()) {
                if ($res['password'] == md5($oldpasswd)) {
                    if (M('Shopmember')->where(array('id' => $this->member_id))->save(array('password' => md5($newpasswd)))) {
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

    //一键审核
    public function doallshop(){
        $db=M("Shopware");
        if($db->where(array('token'=>$this->token))->save(array('status'=>1))){
            $this->success2("操作成功");
        }else{
            $this->error2("操作失败");
        }

    }
    
}