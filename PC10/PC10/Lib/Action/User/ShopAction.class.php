<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * Tel:15889394741
 * Notice:O2O门店
 * Date: 2014/12/29
 * Time: 14:11
 */
class ShopAction extends UserAction{
    public $token;
    static public $treeList = array();

    public function _initialize() {
        parent::_initialize();
        $this->token=$this->_get("token");
        $this->assign("token",$this->token);
        if(!$this->token){
            exit("访问错误!");
        }
    }

    //门店首页
    public function index(){
        if(isset($_GET['type'])){
            $where=array();
            $where['tp_shop.token']=$this->token;
            if(isset($_GET['username'])){
                $where['tp_shop.username']=array('like','%'.$this->_get("username").'%');
            }
            if(isset($_GET['seng'])){
                $where['tp_shopmember.seng']=$this->_get("seng");
            }
            if(isset($_GET['si'])){
                $where['tp_shopmember.si']=$this->_get("si");
            }
            if(isset($_GET['xian'])){
                $where['tp_shopmember.xian']=$this->_get("xian");
            }
        }else{
            $where['tp_shop.token']=$this->token;
        }
        $count=M("Shop")->join("join tp_shopmember on tp_shop.mid=tp_shopmember.id")->where($where)->count();
        $page=new Page($count,20);
        $data=M("Shop")->field("tp_shopmember.username as uname,tp_shopmember.seng,tp_shopmember.si,tp_shopmember.xian,tp_shop.id,tp_shop.username,tp_shop.address,tp_shop.tel,tp_shop.pic,tp_shop.status,tp_shop.is_show")->join("join tp_shopmember on tp_shop.mid=tp_shopmember.id")->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('tp_shop.id desc,tp_shop.status asc')->order('status asc')->select();

        $list=array();
        foreach($data as $k=>$v){
            $list[$v['seng'].$v['si'].$v['xian']][$k]=$v;
        }

        $this->assign("page",$page->show());
        $this->assign("list",$list);
        $this->assign("shopname",$this->_get("username"));
        $this->display();
    }

    //导出店铺数据
    public function ShopExport(){
    	//echo 1;die;
        vendor("PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.IOFactory");
        $phpexcel=new PHPExcel();
        $phpexcel->getActiveSheet()->setCellValue('A1', '编号');
        $phpexcel->getActiveSheet()->setCellValue('B1', '分店名称');
        $phpexcel->getActiveSheet()->setCellValue('C1', '分店地址');
        $phpexcel->getActiveSheet()->setCellValue('D1', '联系电话');
        $phpexcel->getActiveSheet()->setCellValue('E1', '所属区域');
        $phpexcel->getActiveSheet()->setCellValue('F1', '区域管理员');
        $data=M("Shop")->field("tp_shopmember.username as uname,tp_shopmember.seng,tp_shopmember.si,tp_shopmember.xian,tp_shop.id,tp_shop.username,tp_shop.address,tp_shop.tel,tp_shop.id")->join("join tp_shopmember on tp_shop.mid=tp_shopmember.id")->where(array("tp_shop.token"=>$this->token))->select();
        $i=2;
        foreach($data as $k=>$v){
            $area=$v['seng'].$v['si'].$v['xian'];
            $phpexcel->getActiveSheet()->setCellValue('A'.$i,$i-1);
            $phpexcel->getActiveSheet()->setCellValue('B'.$i, $v['uname']);
            $phpexcel->getActiveSheet()->setCellValue('C'.$i, $v['address']);
            $phpexcel->getActiveSheet()->setCellValue('D'.$i, $v['tel']);
            $phpexcel->getActiveSheet()->setCellValue('E'.$i, $area);
            $phpexcel->getActiveSheet()->setCellValue('F'.$i, $v['username']);
            $i++;
        }
        $obj = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
        $filename = 'shop.xls';//文件名
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $obj->save('php://output');
    }

    //导出店铺数据
    public function ShopunionExport(){
        vendor("PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.IOFactory");
        $phpexcel=new PHPExcel();
        $phpexcel->getActiveSheet()->setCellValue('A1', '编号');
        $phpexcel->getActiveSheet()->setCellValue('B1', '社区名称');
        $phpexcel->getActiveSheet()->setCellValue('C1', '区域管理员');
        $phpexcel->getActiveSheet()->setCellValue('D1', '经度');
        $phpexcel->getActiveSheet()->setCellValue('E1', '纬度');
        $phpexcel->getActiveSheet()->setCellValue('F1', '所属区域');
        //$data=M("Shop")->field("tp_shopmember.username as uname,tp_shopmember.seng,tp_shopmember.si,tp_shopmember.xian,tp_shop.id,tp_shop.username,tp_shop.address,tp_shop.tel,tp_shop.id")->join("join tp_shopmember on tp_shop.mid=tp_shopmember.id")->where(array("tp_shop.token"=>$this->token))->select();
        $where = array();
        $where['tp_shopunion.token'] = $this->token;
        if($_GET['username']){
            $where['tp_shopunion.cname'] = array('like','%'.$_GET['username'].'%');
        }
        if($_GET['seng'] && $_GET['seng'] != '省份'){
            $where['tp_shopunion.seng'] = array('like','%'.$_GET['seng'].'%');
        }
        if($_GET['si'] && $_GET['si'] != '地级市'){
            $where['tp_shopunion.si'] = array('like','%'.$_GET['si'].'%');
        }
        if($_GET['xian'] && $_GET['xian'] != '市、县级市'){
            $where['tp_shopunion.xian'] = array('like','%'.$_GET['xian'].'%');
        }
        $data=M("Shopunion")->field("tp_shopunion.id,tp_shopmember.username,tp_shopmember.seng,tp_shopmember.si,tp_shopmember.xian,tp_shopunion.address,tp_shopunion.lat,tp_shopunion.long,tp_shopunion.cname,tp_shopunion.des,tp_shopunion.pic")->join("right join tp_shopmember on tp_shopmember.id=tp_shopunion.mid")->where($where)->select();
        $i=2;
        foreach($data as $k=>$v){
            $area=$v['seng'].$v['si'].$v['xian'];
            $phpexcel->getActiveSheet()->setCellValue('A'.$i,$i-1);
            $phpexcel->getActiveSheet()->setCellValue('B'.$i, $v['cname']);
            $phpexcel->getActiveSheet()->setCellValue('C'.$i, $v['username']);
            $phpexcel->getActiveSheet()->setCellValue('D'.$i, $v['lat']);
            $phpexcel->getActiveSheet()->setCellValue('E'.$i, $v['long']);
            $phpexcel->getActiveSheet()->setCellValue('F'.$i, $area);
            $i++;
        }
        $obj = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
        $filename = 'shopunion.xls';//文件名
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $obj->save('php://output');
    }

    //门店类型
    public function type(){
        $count=M("Shoptype")->where(array("token"=>$this->token))->count();
        $page=new Page($count,20);
        $list=M("Shoptype")->where(array("token"=>$this->token))->limit($page->firstRow . ',' . $page->listRows)->order('sort asc')->select();
        $this->assign("page",$page->show());
        $this->assign("list",$list);
        $this->display();
    }

    //基础配置显示
    public function BaseSet(){
        if(IS_AJAX){
            $_POST['token']=$this->token;
            if(M("Baseset")->where(array("token"=>$this->token))->find()){
                if(M("Baseset")->where(array("token"=>$this->token))->save($_POST)){
                    $this->ajaxReturn(array("status"=>1,"info"=>"设置成功"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"设置失败"));
                }
            }else{
                if(M("Baseset")->add($_POST)){
                    $this->ajaxReturn(array("status"=>1,"info"=>"设置成功"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"设置失败"));
                }
            }
        }else{
            $nav=M("Baseset")->where(array("token"=>$this->token))->find();
            $this->assign("nav",$nav);
            $this->display();
        }
    }
    //导航栏
    public function navigation(){
        $count=M("Navigation")->where(array("token"=>$this->token))->count();
        $page=new Page($count,20);
        $list=M("Navigation")->where(array("token"=>$this->token))->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign("list",$list);
        $this->assign("page",$page->show());
        $this->display();
    }

    //添加导航
    public function AddNag(){
        if(IS_AJAX){
            $_POST['token']=$this->token;
            if(M("Navigation")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));
            }
        }else{
            $this->display();
        }
    }

    //编辑导航
    public function EditNag(){
        if(IS_AJAX){
            if(M("Navigation")->where(array("id"=>$_POST['id']))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            $id=$this->_get("id","intval");
            $nag=M("Navigation")->where(array("id"=>$id))->find();
            $this->assign("nag",$nag);
            $this->assign("id",$id);
            $this->display();
        }
    }

    //删除导航
    public function DelNag(){
        if(IS_AJAX){
            if(M("Navigation")->where(array("id"=>$_POST['id']))->delete()){
                $this->ajaxReturn(array("status"=>1,"info"=>"删除成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"删除失败"));
            }
        }else{
            exit("非法操作!");
        }
    }

    //添加类型
    public function AddType(){
        if(IS_POST){
            $_POST['token']=$this->token;
            if(M("Shoptype")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>"添加失败"));
            }
        }else{
            $this->display();
        }
    }

    //编辑类型
    public function EditType(){
        if(IS_POST){
            if(M("Shoptype")->where(array("id"=>$_POST['tid']))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>"修改失败"));
            }
        }else{
            $data=M("Shoptype")->where(array("id"=>$this->_get("id","intval")))->find();
            $this->assign("data",$data);
            $this->display();
        }
    }

    //删除类型
    public function DelType(){
        if(M("Shop")->where(array("tid"=>$this->_get("id","intval")))->find()){
            $this->error2("不能执行此操作，请先删除该分类下的所有店铺");
        }
        if(M("Shoptype")->where(array("id"=>$this->_get("id","intval")))->delete()){
            $this->success2("删除成功",U("Shop/type",array("token"=>$this->token)));
        }else{
            $this->success2("删除失败");
        }
    }

    //添加店铺
    public function AddShop(){
        if(IS_POST){
            $_POST['password']=md5($_POST['password']);
            $_POST['token']=$this->token;
            if(M("Shop")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>M("Shp")->getError()));
            }
        }else{
            $union=M("Shopunion")->field("id,cname")->where(array("token"=>$this->token))->select();
            $ShopType=M("Shoptype")->field("id,name")->where(array("token"=>$this->token))->select();
            $member=M("Shopmember")->field("id,username")->where(array("token"=>$this->token))->select();
            $this->assign("member",$member);
            $this->assign("union",$union);
            $this->assign("shoptype",$ShopType);
            $this->display();
        }
    }

    //修改店铺
    public function EditShop(){
        if(IS_POST){
            /*if(!M("Shop")->where(array("token"=>$this->token,"id"=>$_POST['id'],"password"=>md5($_POST['password'])))->find()){
                $this->ajaxReturn(array("status"=>0,"info"=>"账号或者密码错误!"));
            }
            */
            $_POST['token']=$this->token;
            $_POST['password']=md5($_POST['password']);
            if(M("Shop")->where(array("id"=>$_POST['id']))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            $ShopType=M("Shoptype")->field("id,name")->where(array("token"=>$this->token))->select();
            $data=M("Shop")->where(array("id"=>$this->_get("id","intval")))->find();
            $member=M("Shopmember")->field("id,username")->where(array("token"=>$this->token))->select();
            $union=M("Shopunion")->field("id,cname")->where(array("token"=>$this->token))->select();
            $oCode = new Code($this->token,$data['id']);
            $img = $oCode->getYJCode();
            $this->assign("union",$union);
            $this->assign("member",$member);
            $this->assign("data",$data);
            $this->assign("type",$ShopType);
            $this->assign('img',$img);
            $this->display();
        }
    }

    //删除店铺
    public function DelShop(){
        /*if(M("Shopclassfy")->where(array("branch_id"=>$this->_get("id","intval")))->find()){
            $this->error2("不能执行此操作，请先删除该店铺下的所有分类");
        }
        */
        if(M("Shop")->where(array("id"=>$this->_get("id","intval")))->delete()){
            $this->success2("删除成功",U("Shop/index",array("token"=>$this->token)));
        }else{
            $this->error2("删除失败");
        }
    }

    //店铺审核
    public function checkShop(){
        $where['token']=$this->token;
        $where['id']   =$this->_get("id","intval");
        $save['status']=$this->_get("status","intval");
        if(M("Shop")->where($where)->save($save)){
            $this->success2("操作成功!");
        }else{
            $this->error2("操作失败!");
        }
    }

    //店铺审核
    public function changeshow(){
        $where['token']=$this->token;
        $where['id']   =$this->_get("id","intval");
        $save['is_show']=$this->_get("status","intval");
        if(M("Shop")->where($where)->save($save)){
            $this->success2("操作成功!");
        }else{
            $this->error2("操作失败!");
        }
    }

    //社区列表
    public function Union(){
        $condition=array();
        if(IS_POST){
            if($_REQUEST['cname']){
                $condition['tp_shopunion.cname'] = array('like','%'.$_REQUEST['cname'].'%');
            }
            if($_REQUEST['s_province'] && $_REQUEST['s_province'] != '省份'){
                $condition['tp_shopunion.seng'] = array('like','%'.$_POST['s_province'].'%');
            }
            if($_REQUEST['s_city'] && $_REQUEST['s_city'] != '地级市'){
                $condition['tp_shopunion.si'] = array('like','%'.$_REQUEST['s_city'].'%');
            }
            if($_REQUEST['s_county'] && $_REQUEST['s_county'] != '市、县级市'){
                $condition['tp_shopunion.xian'] = array('like','%'.$_REQUEST['s_county'].'%');
            }
        }


        /*
         *此处未分页  topher修复此bug
         */
        $condition['tp_shopunion.token']=$this->token;
        $count=M("Shopunion")->where($condition)->count();
        $page=new Page($count,15);
        $data=M("Shopunion")->field("tp_shopunion.id,tp_shopmember.username,tp_shopmember.seng,tp_shopmember.si,tp_shopmember.xian,tp_shopunion.address,tp_shopunion.lat,tp_shopunion.long,tp_shopunion.cname,tp_shopunion.des,tp_shopunion.pic")->join("right join tp_shopmember on tp_shopmember.id=tp_shopunion.mid")->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign("list",$data);
        $this->assign("cname",$_REQUEST['cname']);
        $this->display();
    }


    //添加社区
    public function AddUnion(){
        if(IS_POST){
            $where = array('mid'=>$_POST['mid'],'cname'=>$_POST['cname'],'token'=>$this->token);
            $data = M('Shopunion')->where($where)->find();
            if(!$data){
                $_POST['token']=$this->token;
                if(M("Shopunion")->add($_POST)){
                    $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));
                }
            }else{
                $this->ajaxReturn(array("status"=>2,"info"=>"该区域管理员下已存在此社区名称，请重新注册！"));
            }

        }else{
            $member=M("Shopmember")->field("id,username")->where(array("token"=>$this->token))->select();
            $this->assign("member",$member);
            $this->display();
        }
    }

    //修改社区
    public function EditUnion(){
        if(IS_POST){
            if(M("Shopunion")->where(array("id"=>$_POST['id']))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                //$this->ajaxReturn(array("status"=>1,"info"=>"修改失败"));
                //topher 修改此bug
                $this->ajaxReturn(array("status"=>-1,"info"=>"修改失败"));
            }
        }else{
            $data=M("Shopunion")->where(array("id"=>$this->_get("id","intval")))->find();
            $member=M("Shopmember")->field("id,username")->where(array("token"=>$this->token))->select();
            $this->assign("member",$member);
            $this->assign("data",$data);
            $this->display();
        }
    }

    //删除社区
    public function DelUnion(){
        if(M("Shopunion")->where(array("id"=>$this->_get("id","intval")))->delete()){
            $this->success2("删除成功",U("Shop/Union",array("token"=>$this->token)));
        }else{
            $this->success2("删除失败");
        }
    }


    //分店首页
    public function member(){
        $condition['token']=$this->token;
        if(IS_POST){
            if($_POST['username']!=''){
                $condition['username']=array('like','%'.$this->_post('username').'%');
            }
            if($_POST['seng']!='省份'){
                $condition['seng']=$this->_post('seng');
            }
            if($_POST['si']!='地级市'){
                $condition['si']=$this->_post('si');
            }
            if($_POST['xian']!='市、县级市'){
                $condition['xian']=$this->_post('xian');
            }
        }
        $list=M("Shopmember")->where($condition)->select();
        $this->assign("list",$list);
        $this->display();
    }

    //添加分店管理员
    public function AddMember(){
        if(IS_POST){
            if(M("Shopmember")->where(array("token"=>$this->token,'username'=>$_POST['username']))->find()){
                $this->ajaxReturn(array("status"=>0,"info"=>"该管理用户名已经存在"));
            }
            $_POST['password']=md5($_POST['password']);
            $_POST['token']=$this->token;
            if(M("Shopmember")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));
            }
        }else{
            $this->display();
        }
    }

    //修改分店管理员信息
    public function EditMember(){
        if(IS_POST){
            if(isset($_GET['id'])){
                $id=$this->_get("id","intval");
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"非法操作"));
            }

            if(!M("Shopmember")->where(array("token"=>$this->token,"id"=>$id,"password"=>md5($_POST['password'])))->find()){
                $this->ajaxReturn(array("status"=>0,"info"=>"账号或者密码错误!"));
            }
            $_POST['password']=md5($_POST['password']);
            if(M("Shopmember")->where(array("token"=>$this->token,"id"=>$id))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功!"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败!"));
            }
        }else{
            $id=$this->_get("id","intval");
            $data=M("Shopmember")->field("id,username,tel,seng,si,xian,rate")->where(array("token"=>$this->token,"id"=>$id))->find();
            $this->assign("data",$data);
            $this->assign("id",$id);
            $this->display();
        }
    }

    //修改渠道管理员密码
    public function EditPass(){
        if(IS_AJAX){
            if(M("Shopmember")->where(array("token"=>$this->token,"id"=>$this->_get("id","intval"),"password"=>md5($_POST['old'])))->find()){
                if(M("Shopmember")->where(array("token"=>$this->token,"id"=>$this->_get("id","intval")))->save(array("password"=>md5($_POST['news'])))){
                    $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
                }else{
                    $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
                }
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"账号或者密码错误!"));
            }
        }else{
            $id=$this->_get("id","intval");
            $data=M("Shopmember")->field("username")->where(array("token"=>$this->token,"id"=>$id))->find();
            $this->assign("data",$data);
            $this->assign("id",$id);
            $this->display();
        }
    }

    //删除分店管理员
    public function DelMember(){
        if(M("Shopmember")->where(array("id"=>$this->_get("id","intval")))->delete()){
            $this->success2("删除成功");
        }else{
            $this->error2("删除失败");
        }
    }

    //轮播图图片展示列表
    public function flash(){
        if(isset($_GET['type'])){
            $where['type']=$this->_get("type");
        }
        if(isset($_GET['status'])){
            $where['status']=$this->_get("status");
        }
        if(isset($_REQUEST['shopid']) && $_REQUEST['shopid'] != 0){
            $where['tid']=$_REQUEST["shopid"];
        }
        if(isset($_REQUEST['shopmemberid']) && $_REQUEST['shopmemberid'] != 0){
            $where['tid']=$_REQUEST["shopmemberid"];
        }
        /*
        * 运营商数据
        */
        $shopmember = M('Shopmember')->where(array('token'=>$this->token))->select();
        $shopdata =  M('Shop')->where(array('token'=>$this->token))->select();
        $where['token']=$this->token;
        $count=M("Oflash")->where($where)->count();
        $page=new Page($count,20);
        $imgs=M("Oflash")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

        /*
         * topher 修改此bug
         */
        foreach($imgs as $key=>$val){
            if($val['type'] == 3){
                $temp = array();
                $temp = M('Shopmember')->where(array('id'=>$val['tid']))->find();
                $imgs[$key]['cname'] = $temp['username'];
            }else if($val['type'] == 4){
                $temp = array();
                $temp = M('Shop')->where(array('id'=>$val['tid']))->find();
                $imgs[$key]['cname'] = $temp['username'];
            }else{
                $imgs[$key]['cname'] = '';
            }
        }
        $this->assign("shopmember", $shopmember);
        $this->assign("shopmemberid", $_REQUEST['shopmemberid']);
        $this->assign("shopdata", $shopdata);
        $this->assign("shopid", $_REQUEST['shopid']);
        $this->assign("list",$imgs);
        $this->assign("page",$page->show());
        $this->display();
    }

    //添加轮播图图片
    public function AddFlash(){
        if(IS_AJAX){
            $_POST['tid']=0;
            $_POST['token']=$this->token;
            if(M("Oflash")->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>"添加失败"));
            }
        }else{
            $this->display();
        }
    }

    //修改轮播图
    public function EditFlash(){
        if(IS_AJAX){
            $_POST['token']=$this->token;
            if(M("Oflash")->where(array("id"=>$_POST['id']))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>1,"info"=>"修改失败"));
            }
        }else{
            $img=M("Oflash")->where(array("id"=>$this->_get("id","intval")))->find();
            $this->assign("img",$img);
            $this->display();
        }
    }

    //删除轮播图
    public function DelFlash(){
        $id=$this->_get("id","intval");
        if(!$id){
            exit("非法操作!");
        }
        $data=M("Oflash")->where(array("id"=>$this->_get("id","intval"),'token'=>$this->token))->find();
        if(M("Oflash")->where(array("id"=>$this->_get("id","intval")))->delete()){
            unset($data['pic']);
            $this->success2("删除成功");
        }else{
            $this->error2("删除失败");
        }
    }

    //商品列表
    public function WareList(){
        $db=M("Shopware");
        $count=$db->where(array("token"=>$this->token))->count();
        $page=new Page($count,15);

        $data=$db->field("tp_shop.username,tp_shopware.stock,tp_shopware.id,tp_shopware.name,tp_shopware.price,tp_shopware.des,tp_shopware.pic,tp_shopware.status,tp_shopware.attr")->join("join tp_shop on tp_shopware.sid=tp_shop.id")->where(array("tp_shopware.token"=>$this->token))->order('tp_shopware.status asc,tp_shopware.id desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign("list",$data);
        $this->assign('page',$page->show());
        $this->display();
    }
    //设置库存
    public function stock(){
        if(IS_POST){
    //        p($_POST);
            if(M('Shopware')->where(array('id'=>$_POST['id']))->save(array('stock'=>$_POST['stock']))){
                $this->success2('设置成功',U('WareList',array('token'=>$this->token)));
            }else{
                $this->error2('设置失败',U('WareList',array('token'=>$this->token)));
            }
        }else{
           $stock= M('Shopware')->where(array('id'=>$_GET['id']))->getField('stock');
            $this->assign('stock',$stock);
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

    //审核
    public function verify(){
        $condition=array();
        $save['status']=$this->_get("status","intval");
        $condition['id']    =$this->_get("id","intval");
        $condition['token'] =$this->token;
        if(M("Shopware")->where($condition)->save($save)){
            $this->success2("操作成功");
        }else{
            $this->error2("操作失败");
        }
    }

    //几分等级
    public function GradeList(){
        $list=M("Shopgrade")->field("id,name,pic,status")->where(array("token"=>$this->token))->select();
        $this->assign("list",$list);
        $this->display();
    }

    //增加积分等级
    public function AddGrade(){
      if(IS_AJAX) {
          $_POST['token']=$this->token;
          if(M("Shopgrade")->add($_POST)){
              $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
          }else{
              $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));
          }
      }else{
          $this->display();
      }
    }
    //修改积分等级
    public function EditGrade(){
        if(IS_AJAX){
            if(M("Shopgrade")->where(array("id"=>$this->_get("id","intval"),"token"=>$this->token))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            if(isset($_GET['id'])){
                $id=$this->_get("id","intval");
                $data=M("Shopgrade")->where(array("token"=>$this->token,'id'=>$id))->find();
                $this->assign("data",$data);
                $this->assign("id",$id);
                $this->display();
            }else{
                exit("非法操作!");
            }
        }
    }

    public function DelGrade(){
                if(isset($_GET['id'])){
            if(M("Shopgrade")->where(array("token"=>$this->token,"id"=>$this->_get("id","intval")))->delete()){
                $this->success2("删除成功!");
            }else{
                $this->success2("删除失败!");
            }
        }else{
            exit("非法操作!");
        }
    }

    //积分设置
    public function ScoreSet(){
        if(IS_AJAX){
            $data = array();
            $data['id'] = $_POST['id'];
            $data['day_score'] = $_POST['day_score'];
            $data['days'] = $_POST['days'];
            $data['scores'] = $_POST['scores'];
            $data['status'] = $_POST['status'];
            $data['orderscore'] = $_POST['orderscore'];
            $data['moneyscore'] = $_POST['moneyscore'];
            $data['notget_money'] = $_POST['notget_money'];
            $data['add_money'] = $_POST['add_money'];
            $data['sub_score'] = $_POST['sub_score'];
            $data['tuijian_score'] = $_POST['tuijian_score'];
            $data['update_time'] = time();
            $data['token'] = $this->token;
            $moneylevel = array();
            if($_POST['one_money'] && $_POST['one_level']){
                $moneylevel[] = array($_POST['one_money']=>$_POST['one_level']);
            }else{
                $moneylevel[] = array();
            }
            if($_POST['two_money'] && $_POST['two_level']){
                $moneylevel[] = array($_POST['two_money']=>$_POST['two_level']);
            }else{
                $moneylevel[] = array();
            }
            if($_POST['three_money'] && $_POST['three_level']){
                $moneylevel[] = array($_POST['three_money']=>$_POST['three_level']);
            }else{
                $moneylevel[] = array();
            }

            $data['chong_level'] = json_encode($moneylevel);
            $data['chongzhi_text'] = $_POST['chongzhi_text'];

            $usercenter_signmodel=M('Shop_scoreset');
            if($_POST['id']){
                if($usercenter_signmodel->data(array('token'=>$this->token,'id'=>$data['id']))->save($data)){
                    $this->success('操作成功', U(MODULE_NAME . '/scoreman'),true);
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/scoreman'),true);
                }
            }else{

                if($usercenter_signmodel->data($data)->add()){
                    $this->success('操作成功', U(MODULE_NAME . '/scoreman'));
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/scoreman'));
                }
            }
        }else{
            $signdata=M('Shop_scoreset')->where(array("token"=>$this->token))->find();
            $signdata['moneylevel'] = json_decode($signdata['chong_level'],true);
            if(!empty($signdata)){
                $this->assign("signdata",$signdata);
            }
            $this->display();
        }
    }
    //积分列表
    public function ScoreList(){
        $count=M("Shop_users")->where(array("token"=>$this->token))->count();
        $page=new Page($count,20);
        $scoreInfo=M("Shop_users")->field("id,score,openid")->where(array("token"=>$this->token))->limit($page->firstRow.','.$page->listRows)->order('score desc')->select();
        foreach($scoreInfo as $k=>$v){
            $scoreInfo[$k]['level']=$this->getLevel($v['score'],$this->token,$v['openid']);

            $user=M("Wxusers")->field("nickname")->where(array("openid"=>$v['openid']))->find();
            $scoreInfo[$k]['user']=$user['nickname'];

        }
        $this->assign("page",$page->show());
        $this->assign("score",$scoreInfo);
        $this->display();
    }

    //获取用户的积分
    public function getLevel($score,$token,$openid){
        $condition['token']=$this->token;
        $condition['openid']=$this->openid;
        $condition['scope']=array("elt",$score);
        $level=M("Shopgrade")->field("name")->where($condition)->order("scope desc")->find();
        return $level['name'];
    }
    //我的账户 平台销售总额  收取区域管理佣金总额 管理提现总额 还可提现总额
    public function account(){
        if(isset($_GET['type'])){
            $type=$this->_get("type","intval");
        }else{
            exit("非法操作!");
        }
        $totalCommission=$this->commissionDetai($this->token);
        $totalget=0;
        $getrate=0;
        $totalrate=0;
        foreach($totalCommission as $k=>$v){
            $totalget+=$v['totalsale'];
            $getrate+=$v['getrate'];
            $totalrate+=$v['totalsale']*$v['rate'];
            $totalCommission[$k]['totalrate']=$v['totalsale']*$v['rate'];
            $totalCommission[$k]['unrate']=$v['totalsale']-$v['getrate']-$v['totalsale']*$v['rate'];
        }
        $mycount=$totalget-$totalrate-$getrate;

        $condition=array();
        $condition['token']=$this->token;
        $condition['paystatus']=1;

        //总收入
        $allMoney = M("Mainorder")->where($condition)->sum("totalmoney");

        if(!$allMoney){
            $allMoney=0;
        }

        $condition['paytype'] = array('in',array(1,2)) ;

        $online=M("Mainorder")->where($condition)->sum("totalmoney");



        if(!$online){
            $online=0;
        }
        $condition['paytype'] = 3;
        $downline=M("Mainorder")->where($condition)->sum("totalmoney");

        $this->assign("downline",$downline);
        $this->assign("online",$online);
        $this->assign("allMoney",$allMoney);
        $this->assign("mycount",$mycount);
        $this->assign("type",$type);
        $this->assign("totalget",$totalget);
        $this->assign("getrate",$getrate);
        $this->assign("totalrate",$totalrate);
        $this->assign("list",$totalCommission);
        $this->display();
    }

    //提现记录
    public function payList(){
        if(isset($_GET['status'])){
            $where['tp_memberate.token']=$this->token;
            $where['tp_memberate.status']=$this->_get("status","intval");
            $count=M("memberate")->where($where)->count();
            $page=new Page($count,10);
            $list=M("Memberate")->field("tp_shopmember.username,tp_memberate.id,tp_memberate.jtime,tp_memberate.account,tp_memberate.status")->join("join tp_shopmember on tp_shopmember.id=tp_memberate.mid")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
            //dump($list);exit;
            $this->assign("page",$page->show());
            $this->assign("list",$list);
            $this->display();
        }else{
            exit("非法操作!");
        }
    }

    //提现审核
    public function checkPay(){
        $where['token']=$this->token;
        $where['id']=$this->_get("id","intval");
        $save['status']=$this->_get("status","intval");
        if(M("Memberate")->where($where)->save($save)){
            $this->success2("操作成功!");
        }else{
            $this->error2("操作失败!");
        }
    }

    //收取平台佣金总额
//    public function getCommission($token=""){
//        if($token){
//            $commissionDetail=$this->commissionDetai($token);
//        }else{
//            return 0;
//        }
//    }
    //管理员佣金明细
    public function commissionDetai($token){
        $memberShop=M("Shopmember")->field("tp_shop.id as sid,tp_shopmember.id as mid,tp_shopmember.rate,tp_shopmember.username,tp_shopmember.seng,tp_shopmember.si,tp_shopmember.xian")->join("join tp_shop on tp_shop.mid=tp_shopmember.id")->where(array("tp_shopmember.token"=>$token))->select();
        $members=array();
        foreach($memberShop as $k=>$v){
            $members[$v['mid']][$k]=$v;
        }
        foreach($members as $k=>$v){
            foreach($v as $m=>$n){
                $where['tp_sideorder.sid']=$n['sid'];
                $where['tp_mainorder.paystatus|tp_mainorder.sendstatus']=array('1','2','_multi'=>true);
                $sideOrders=M("Sideorder")->field("tp_sideorder.id")->join("join tp_mainorder on tp_mainorder.id=tp_sideorder.mid")->where($where)->select();
                $total=0;
                foreach($sideOrders as $p=>$q){
                    $shoptotal=M("Sidedetail")->where(array("sid"=>$q['id']))->sum("total");
                    $total+=$shoptotal;
                }
                if($shoptotal){
                    $members[$k][$m]['totalsale']=$total;
                }else{
                    $members[$k][$m]['totalsale']=0;
                }
            }
        }
        foreach($members as $k=>$v){
            $totalsale=0;
            foreach($v as $m=>$n){
                $members[$k]['username']=$n['username'];
                $members[$k]['area']=$n['seng'].$n['si'].$n['xian'];
                $members[$k]['rate']=$n['rate'];
                $totalsale+=$n['totalsale'];
            }
            $members[$k]['totalsale']=$totalsale;
            $getrate=M("Memberate")->where(array("token"=>$token,'mid'=>$k,"status"=>1))->sum("account");
            if($getrate){
                $members[$k]['getrate']=$getrate;
            }else{
                $members[$k]['getrate']=0;
            }
        }
        return $members;
    }
    public function getRate(){
        $data=M("Sideorder")->field("tp_shopmember.username,tp_shopmember.rate,tp_sideorder.id,tp_shopmember.seng,tp_shopmember.si,tp_shopmember.xian")->join("join tp_shop on tp_shop.id=tp_sideorder.sid join tp_shopmember on tp_shopmember.id=tp_shop.mid")->where(array("tp_sideorder.token"=>$this->token,"tp_sideorder.paystatus"=>1))->select();
        $list=array();
        foreach($data as $k=>$v){
            $list[$v['username']][$k]=$v;
        }
        $new=array();
        foreach($list as $k=>$v){
            $new[$k]["username"]=$list[$k][0]['username'];
            $new[$k]["rate"]=$list[$k][0]['rate'];
            $new[$k]["area"]=$list[$k][0]['seng'].$list[$k][0]['si'].$list[$k][0]['xian'];
            foreach($v as $m=>$n){
                $total=M("Sidedetail")->where(array("sid"=>$n['id']))->sum("total");
                $new[$k]['totalsale']+=$total;
            }
            $new[$k]['totalrate']=floatval($new[$k]['totalsale'])*floatval($new[$k]["rate"]);
        }
        $this->assign("list",$new);
        $this->display();
    }

    //审核所有门店
    public function checkAll(){
        if(IS_AJAX){
            if(M("Shop")->where(array('token'=>$this->token))->save(array("status"=>1))){
                $this->ajaxReturn(array("status"=>1,"info"=>"批量审核成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"操作失败"));
            }
        }else{
            exit("非法操作!");
        }
    }


    //轮播图审核
    public function checkFlash(){
        $id=$this->_get("id","intval");
        $where['token']=$this->token;
        $where['id']=$id;
        $save['status']=$this->_get("status","intval");
        if(!$id){
            exit("非法操作!");
        }
        if(M("Oflash")->where($where)->save($save)){
            $this->success2("操作成功!");
        }else{
            $this->error2("操作失败!");
        }
    }


    /*
     * 分类模块
     */

    //分店商品分类列表

    public function classify(){

        $data=M("Shopclassfy_all")->where(array("token"=>$this->token))->order("id desc")->select();

        $list=self::tree($data);

        $this->assign("list",$list);

        $this->display();

    }

    //添加分类

    public function AddClassify(){

        if(IS_POST){
            $_POST['token'] = $this->token;
            if(M("Shopclassfy_all")->add($_POST)){

                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));

            }else{

                $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));

            }

        }else{

            $cats=M("Shopclassfy_all")->where(array("token"=>$this->token))->order("id desc")->select();

            $list=self::tree($cats);

            /*
             * 店铺分类
             */
            $shopcats=M("Shoptype")->where(array("token"=>$this->token))->select();
            $this->assign("shopcats",$shopcats);

            $this->assign("cats",$list);

            $this->display();

        }

    }



    //编辑分类

    public function EditClassify(){

        if(IS_POST){

            if($_POST['pid']==$_POST['id']){

                $this->ajaxReturn(array("status"=>0,"info"=>"操作错误"));

            }

            if(M("Shopclassfy_all")->where(array("id"=>$_POST['id']))->save($_POST)){

                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));

            }else{

                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));

            }

        }else{

            $data=M("Shopclassfy_all")->where(array("id"=>$this->_get("id","intval")))->find();

            if($data['pid']){

                $cinfo=M("Shopclassfy_all")->field("id")->where(array("id"=>$data['pid']))->find();

            }

            $cats=M("Shopclassfy_all")->where(array("token"=>$this->token))->order("id desc")->select();

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

            /*
             * 店铺分类
             */
            $shopcats=M("Shoptype")->where(array("token"=>$this->token))->select();
            $this->assign("shopcats",$shopcats);

            $this->assign("cats",$list);

            $this->assign("data",$data);

            $this->display();

        }

    }

    //删除分类

    public function DelClassify(){

        if(M("Shopclassfy_all")->where(array("id"=>$this->_get("id","intval")))->delete()){

            $this->success2("删除成功");

        }else{

            $this->error2("删除失败");

        }

    }


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

    //员工列表
    public function cStaff(){
        $Count=M('Shop_staff')->field('tp_shop.username,tp_shop_staff.id,tp_shop_staff.staff_user,tp_shop_staff.openid')->join('left join tp_shop on tp_shop.id=tp_shop_staff.sid')->where(array('tp_shop_staff.token'=>$this->token))->count();
        $Page=new Page($Count,20);
        $staffData=M('Shop_staff')->field('tp_shop.username,tp_shop_staff.id,tp_shop_staff.staff_user,tp_shop_staff.openid')->join('left join tp_shop on tp_shop.id=tp_shop_staff.sid')->where(array('tp_shop_staff.token'=>$this->token))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign(array(
            'staff'=>$staffData,
            'page' =>$Page->show()
        ));
        $this->display();
    }

    //添加员工
    public function AddStaff(){
        if(IS_AJAX){
            $_POST['staff_pwd']=md5($_POST['staff_pwd']);
            $_POST['token']   =$this->token;
            if(M('Shop_staff')->add($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"添加成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"添加失败"));
            }
        }else{
            $shopData=M('Shop')->field('id,username')->where(array('token'=>$this->token))->select();
            $this->assign('shop',$shopData);
            $this->display();
        }
    }

    //修改员工
    public function EditStaff(){
        $id=$this->_get("id","intval");
        if(IS_AJAX){
            $_POST['staff_pwd']=md5($_POST['staff_pwd']);
            if(M('Shop_staff')->where(array('id'=>$id))->save($_POST)){
                $this->ajaxReturn(array("status"=>1,"info"=>"修改成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"修改失败"));
            }
        }else{
            $shopData=M('Shop')->field('id,username')->where(array('token'=>$this->token))->select();
            $staffData=M('Shop_staff')->where(array('id'=>$id))->find();
            $this->assign(array(
                'shop'=>$shopData,
                'staff'=>$staffData,
                'id'   =>$id
            ));
            $this->display();
        }
    }

    //删除员工
    public function DelStaff(){
        if(M('Shop_staff')->where(array('id'=>$this->_get('id','intval')))->delete()){
            $this->success2('删除成功');
        }else{
            $this->error2('删除失败');
        }
    }
    /**
     * 会员管理
     */
    public function hygl(){
        if(IS_POST){

            if($_POST['id']>0){
                $where['id']=$_POST['id'];
            }
            if($_POST['name']){
                $where['name']=array('like','%'.$_POST['name'].'%');
            }
            if($_POST['stat_time']&&$_POST['end_time']){
                $_POST['stat_time']=strtotime($_POST['stat_time']);
                $_POST['end_time']=strtotime($_POST['end_time']);
                $where['add_time']=array('between',array($_POST['stat_time'],$_POST['end_time']));
            }
            $where['token']=$this->token;
            $count=M("Shop_users")->where($where)->count();
            $page=new Page($count,50);
            $list=M("Shop_users")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

            $this->assign("page",$page->show());
            foreach($list as $k=>$v){
                $sCount=M('Dy_score')->where(array('token'=>$this->token,'openid'=>$v['openid'],'score'=>array('gt',0)))->getField('sum(score) as sum');//得总积分
                $xiaofeijifen=M('Dy_score')->where(array('token'=>$this->token,'openid'=>$v['openid'],'score'=>array('lt',0)))->getField('sum(score) as sum');//得总积分
                $list[$k]['leijijifen'] = $sCount;   //累计积分
                $list[$k]['xiaofeijifen'] = $xiaofeijifen;   //消费积分
                $list[$k]['dengji'] = M("Shopgrade")->where(array('scope'=>array('elt',$sCount)))->order('scope desc')->limit(1)->getField('name'); //得等级

                $list[$k]['fatie']=M('Dy_community')->where(array('token'=>$this->token,'openid'=>$v['openid']))->count();//得发贴总数


            }
            $this->assign('id',isset($_POST['id'])?$_POST['id']:'');
            $this->assign('name',isset($_POST['name'])?$_POST['name']:'');
            $this->assign('stat_time',isset($_POST['stat_time'])?$_POST['stat_time']:'');
            $this->assign('end_time',isset($_POST['end_time'])?$_POST['end_time']:'');
            $this->assign('list',$list);
            $this->display();
        }else{
            $count=M("Shop_users")->where(array("token"=>$this->token))->count();
            $page=new Page($count,30);
            $list=M("Shop_users")->where(array("token"=>$this->token))->limit($page->firstRow . ',' . $page->listRows)->select();
            foreach($list as $k=>$v){
                $sCount=M('Dy_score')->where(array('token'=>$this->token,'openid'=>$v['openid'],'score'=>array('gt',0)))->getField('sum(score) as sum');//得总积分
                $xiaofeijifen=M('Dy_score')->where(array('token'=>$this->token,'openid'=>$v['openid'],'score'=>array('lt',0)))->getField('sum(score) as sum');//得总积分
                $list[$k]['leijijifen'] = $sCount;   //累计积分
                $list[$k]['xiaofeijifen'] = $xiaofeijifen;   //消费积分
                $list[$k]['dengji'] = $this->getUserLevel($this->token,$v['openid']); //得等级

                $list[$k]['fatie']=M('Dy_community')->where(array('token'=>$this->token,'openid'=>$v['openid']))->count();//得发贴总数


            }
            $this->assign('list',$list);
            $this->assign("page",$page->show());
            $this->display();
        }

    }

    public function getUserLevel($token,$openid){
        $scoredata = M('dy_score')->field('sum(score) as allscore')->where(array('token'=>$token,'openid'=>$openid,'score'=>array('gt',0)))->select();
        $users = M('shop_users')->where(array('token'=>$token,'openid'=>$openid))->find();
        $score = $scoredata[0]['allscore']+$users['other_score'];
        $where['token']=$token;
        $where['scope']=array('elt', $score);
        $userlevel_data = M("Shopgrade")->where($where)->order('scope desc')->limit(1)->find();
        return $userlevel_data['name'];
    }

    /**
     * 余额记录
     */
    public function ye(){
        $openid=$this->_get('openid');
        $money=$this->_get('money');
        $list=M('Ye_record')->where(array('token'=>$this->token,'openid'=>$openid,'status'=>array('gt',1)))->select();

        $this->assign('money',$money);
        $this->assign('list',$list);

        $this->display();
    }
    /**
     * 积分记录
     */
    public function jifen(){
        if(IS_POST){//手动增加积分
            $data['token']=$this->token;
            $data['openid']=$this->_post('openid');
            $data['type']=4;//代表手工增加
            $data['addtime']=date('Y-m-d h:i:s',time());
            $data['score']=$this->_post('score');
           if(M('Dy_score')->add($data)){
               M('Shop_users')->where(array('token'=>$this->token,'openid'=>$_POST['openid']))->setInc('score',$_POST['score']);
               $this->success2("操作成功");
           }else{
               $this->error2("操作失败");
           }
        }else {
            $openid = $this->_get('openid');
            $score = $this->_get('score');
            $list = M('Dy_score')->where(array('token' => $this->token, 'openid' => $openid))->select();
            $total_score=M('Shop_users')->where(array('token' => $this->token, 'openid' => $openid))->getField('score');
            $this->assign('score', $score);
            $this->assign('list', $list);
            $this->assign('total_score', $total_score);
            $this->display();
        }
    }
    /**
     * 德艺币记录
     */
    public function dyb(){
        $openid=$this->_get('openid');
        $dyb=$this->_get('dyb');
        $list=M('Dyb_score')->where(array('token'=>$this->token,'openid'=>$openid,'status'=>array('gt',0)))->select();
        $this->assign('dyb',$dyb);
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * 发帖记录

    public function fatie(){
        $openid=$this->_get('openid');
        $fatie=$this->_get('fatie');
        $list=M('Dy_community')->where(array('token'=>$this->token,'openid'=>$openid))->select();
        $this->assign('fatie',$fatie);
        $this->assign('list',$list);
        $this->display();
    }*/
    /**
     * 删除用户咯
     */
    public function del_user(){
       $id=$this->_get('id');
        $data['status']=0;
        if(M('Shop_users')->where(array('id'=>$id))->save($data)){
            $this->success2("成功");
        }else{
            $this->error2("失败");
        }
    }

    public function huifu_user(){
       $id=$this->_get('id');
        $data['status']=1;
        if(M('Shop_users')->where(array('id'=>$id))->save($data)){
            $this->success2("成功");
        }else{
            $this->error2("失败");
        }
    }
    /**
     * 冲值记录
     */
    public function czjl(){
        if(IS_POST){

            if($_POST['stat_time']&&$_POST['end_time']){
                $_POST['stat_time']=strtotime($_POST['stat_time']);
                $_POST['end_time']=strtotime($_POST['end_time']);
                $where['add_time']=array('between',array($_POST['stat_time'],$_POST['end_time']));
            }
            $where['token'] = $this->token;
            $where['pay_type'] = 1;//冲值为1
            $where['status'] = 3;//冲值成功
            $count = M("Ye_record")->where($where)->count();
            $page = new Page($count, 20);
            $list = M("Ye_record")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
            foreach ($list as $k => $v) {
                $list[$k]['name'] = M('Shop_users')->where(array('token' => $this->token, 'openid' => $v['openid']))->getField('name');
            }
            $this->assign('list', $list);
            $this->assign("page", $page->show());
            $this->assign('id',isset($_POST['id'])?$_POST['id']:'');
            $this->assign('name',isset($_POST['name'])?$_POST['name']:'');
            $this->assign('stat_time',isset($_POST['stat_time'])?$_POST['stat_time']:'');
            $this->assign('end_time',isset($_POST['end_time'])?$_POST['end_time']:'');
            $total_money=M("Ye_record")->where(array('token'=>$this->token))->getField('sum(money)');
            $this->assign('total_money', $total_money);
            $this->display();
        }else {
            $where['token'] = $this->token;
            $where['pay_type'] = 1;//冲值为1
            $where['status'] = 3;//冲值成功
            $total_money=M("Ye_record")->where(array('token'=>$this->token,'pay_type'=>1,'status'=>3))->getField('sum(money)');
            $this->assign('total_money', $total_money);
            $count = M("Ye_record")->where($where)->count();
            $page = new Page($count, 20);
            $list = M("Ye_record")->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
            foreach ($list as $k => $v) {
                $list[$k]['name'] = M('Shop_users')->where(array('token' => $this->token, 'openid' => $v['openid']))->getField('name');
            }
            $this->assign('list', $list);
            $this->assign("page", $page->show());
            $this->display();
        }
    }
    /**
     * 单品销量统计
     */
    public function tongji(){
        $dianpu=M('Shop')->field('id,username')->where(array('token'=>$this->token))->select();//店铺列表
        $where['token']=$this->token;
        if(IS_POST){
            //$condition1这个条件是为了查总抵扣额
            $condition1['token']=$this->token;
            $condition1['paystatus']=1;



            $_POST=array_filter($_POST);//以防where会带时间查询条件
            if($_POST['sid']>0){
                $where['sid']=$this->_post('sid');
                $this->assign('sid',$_POST['sid']);
                $condition1['shopid']='|'.$_POST['sid'].'|';
            }

            if (isset($_POST['start']) && isset($_POST['end'])) {
                $condition1['buytime']=array('between',array($this->_post("start"),$this->_post("end")));
                $this->assign('start',$this->_post("start"));
                $this->assign('end',$this->_post("end"));
            }
           // p($condition);die;
            $shop = M('Shopware')->field('id,name,vprice')->where($where)->select();
            $num = 0;//总数
            $pprice = 0;//平均价

            foreach ($shop as $k => $v) {//得销售总份数
                $pprice = $pprice + $v['vprice'];
                if (isset($_POST['start']) && isset($_POST['end'])) {
                    $shop[$k]['num'] = M('Sidedetail')->join("right join tp_sideorder on tp_sideorder.id=tp_sidedetail.sid")->where(array('tp_sidedetail.gid' => $v['id'], 'tp_sideorder.paystatus'=>1,'tp_sideorder.buytime'=>array('between',array($this->_post("start"),$this->_post("end")))))->getField('sum(tp_sidedetail.num)');
                    $shop[$k]['total_money']=M('Sidedetail')->join("right join tp_sideorder on tp_sideorder.id=tp_sidedetail.sid")->where(array('tp_sidedetail.gid' => $v['id'], 'tp_sideorder.paystatus'=>1,'tp_sideorder.buytime'=>array('between',array($this->_post("start"),$this->_post("end")))))->getField('sum(tp_sidedetail.total)');
                }else {

                    $shop[$k]['num'] = M('Sidedetail')->join("right join tp_sideorder on tp_sideorder.id=tp_sidedetail.sid")->where(array('tp_sidedetail.gid' => $v['id'], 'tp_sideorder.paystatus'=>1))->getField('sum(tp_sidedetail.num)');
                    $shop[$k]['total_money']=M('Sidedetail')->join("right join tp_sideorder on tp_sideorder.id=tp_sidedetail.sid")->where(array('tp_sidedetail.gid' => $v['id'], 'tp_sideorder.paystatus'=>1))->getField('sum(tp_sidedetail.total)');

                }
                $num = $num + $shop[$k]['num'];
            }
           // p($shop);die;
            $pprice = round($pprice / (count($shop)), 1);

            $dicuao_money=M('Mainorder')->where($condition1)->getField('sum(score_money)');
            $dicuao_money=empty($dicuao_money)?0:$dicuao_money;
            $this->assign('dicuao_money',$dicuao_money);

            /**
             * 商品按销量排序
             */
            foreach($shop as $k=>$v){
                $a=$v['num'].'_'.$v['id'];
                $shop[$a]=$v;
                unset($shop[$k]);
            }
            krsort($shop);

            session('Export_product',$shop);
            $this->assign('dianpu',$dianpu);
            $this->assign('pprice',$pprice);
            $this->assign('num',$num);
            $this->assign('shop',$shop);
            $this->display();
        }else{
            $shop = M('Shopware')->field('id,name,vprice')->where($where)->select();
            $num = 0;//总数
            $pprice = 0;//平均价

            foreach ($shop as $k => $v) {//得销售总份数
                $pprice = $pprice + $v['vprice'];
                $shop[$k]['num'] = M('Sidedetail')->join("right join tp_sideorder on tp_sideorder.id=tp_sidedetail.sid")->where(array('tp_sidedetail.gid' => $v['id'], 'tp_sideorder.paystatus'=>1))->getField('sum(tp_sidedetail.num)');
                $num = $num + $shop[$k]['num'];
                $shop[$k]['total_money']=M('Sidedetail')->join("right join tp_sideorder on tp_sideorder.id=tp_sidedetail.sid")->where(array('tp_sidedetail.gid' => $v['id'], 'tp_sideorder.paystatus'=>1))->getField('sum(tp_sidedetail.total)');
                //求总额
            }

            //求总抵扣金额
            $dicuao_money=M('Mainorder')->where(array('token'=>$this->token,'paystatus'=>1))->getField('sum(score_money)');
            $dicuao_money=empty($dicuao_money)?0:$dicuao_money;
            $this->assign('dicuao_money',$dicuao_money);

            /**
             * 商品按销量排序
             */
            foreach($shop as $k=>$v){
                $a=$v['num'].'_'.$v['id'];
                $shop[$a]=$v;
                unset($shop[$k]);
            }
            krsort($shop);
            $pprice = round($pprice / (count($shop)), 1);
            $this->assign('dianpu',$dianpu);
            $this->assign('pprice',$pprice);
            $this->assign('num',$num);
            $this->assign('shop',$shop);
            session('Export_product',$shop);

            $this->display();

        }

    }
    /**
     * 导出商品销售数据
     */
    public function Export_product(){
        $data=session('Export_product');
        exportExcel($data,array('id','商品名字','商品价格','销售份数','销售总额'),'商品销量统计');
    }

    /*
    查看用户
    */
    public function viewuser(){
        $user = M('Shop_users')->where(array('token'=>$this->token,'id'=>$this->_get('id')))->find();
        $user['gradename'] = $this->getUserLevel($this->token,$user['openid']);
        $gradelist = M('Shopgrade')->where(array('token'=>$this->token))->order('scope asc')->select();
        $this->assign('user',$user);
        $this->assign('gradelist',$gradelist);
        $this->display();
    }

    public function update_grade(){
        $grade = $this->_post('update_grade');
        $openid = $this->_post('openid');
        $Shopusers = new Shopusers($this->token,$openid,$grade,0);
        if($Shopusers->updateGrade()){
            $this->success2('操作成功');
        }else{
            $this->error2('操作成功');
        }
    }

}
