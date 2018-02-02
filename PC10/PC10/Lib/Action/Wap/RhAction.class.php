<?php

/**
 * 仁豪  李铭   2015.7.22
 *
 */
class RhAction extends BaseAction{
    public $token;
    public $wecha_id = 'gh_aab60b4c5a39';
    public $product_model;
    public $product_cat_model;
    public $session_cart_name;
    public $dopenid;
    public $_sTplBaseDir = 'Wap/default/rh';
    public $wxusers_id;//wxusers 表的id
    public $uid = null;

    public function _initialize() {
        if(in_array(ACTION_NAME,array('index','store','yuyue','user_index'))){
            if(!IS_POST){
                $this->autoShare = true;
            }

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
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();
        $this->uid = $wxuser['id'];

    }
    public function index(){

    }
    //预约
    public function yuyue(){
        if(IS_POST){
            $_POST['phone']=$this->_post('tel');
            $_POST['status']=0;
            $_POST['token']=$this->token;
            $_POST['openid']=$this->openid;
            $_POST['add_time']=time();
            if($_POST['store_name']){
                $_POST['type']=1;
            }
            if(M('Rh_yuyue')->add($_POST)){
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else{
            $this->UDisplay();
        }
    }
    // 门店
    public function store(){
        if(isset($_GET['lng'])&&isset($_GET['lat'])&& !isset($_GET['type'])){//有地址
            $list = M('Rh_store')->where(array('token'=>$this->token,'type'=>$_GET['store'],'location_c'=>$_GET['location_c'],'location_a'=>$_GET['location_a']))->select();

            foreach($list as $k=>$val){
                $list[$k]['jl']=floor(getdistance($_GET['lng'],$_GET['lat'],$val['lng'],$val['lat']))*2;
                $list[$list[$k]['jl']."_".$k]=$list[$k];
                unset($list[$k]);
            }
            $this->assign('lat',$_GET['lat']);
            $this->assign('location_c',$_GET['location_c']);
            $this->assign('location_a',$_GET['location_a']);
            $this->assign('location_p',$_GET['location_p']);
            ksort($list);
            // p($list);
        }elseif(isset($_GET['type'])){
            if(trim($_GET['type'])=='市辖区'){

                $list = M('Rh_store')->where(array('token'=>$this->token,'type'=>$_GET['store'],'location_c'=>$_GET['location_c']))->select();

            }else{

                $list = M('Rh_store')->where(array('token'=>$this->token,'type'=>$_GET['store'],'location_c'=>$_GET['location_c'],'location_a'=>$_GET['type']))->select();
            }
            foreach($list as $k=>$val){
                $list[$k]['jl']=floor(getdistance($_GET['lng'],$_GET['lat'],$val['lng'],$val['lat']))*2;
                $list[$list[$k]['jl']."_".$k]=$list[$k];
                unset($list[$k]);
            }
            $this->assign('lat',$_GET['lat']);
            $this->assign('location_c',$_GET['location_c']);
            $this->assign('location_a',$_GET['type']);
            $this->assign('location_p',$_GET['location_p']);
            ksort($list);
        }else{
            $list=M('Rh_store')->where(array('token'=>$this->token,'type'=>$_GET['store']))->select();
            foreach($list as $k=>$val){
                $list[$k]['jl']=floor(getdistance($_GET['lng'],$_GET['lat'],$val['lng'],$val['lat']))*2;
                $list[$list[$k]['jl']."_".$k]=$list[$k];
                unset($list[$k]);
            }
            $this->assign('lat',$_GET['lat']);
            $this->assign('location_c',$_GET['location_c']);
            $this->assign('location_a',$_GET['type']);
            $this->assign('location_p',$_GET['location_p']);
            ksort($list);
        }
        $this->assign('list',$list);
        $this->UDisplay();
    }
    //门店祥情页
    public function store_info(){

        $info=M('Rh_store')->find($_GET['id']);
        $ims=M('Gta_imgs')->field('img')->where(array('pid'=>$_GET['id'],'token'=>$this->token))->select();

        $this->assign('info',$info);
       // p($info);die;
        $this->assign('ims',$ims);
        $this->UDisplay();
    }
    //商城首页
    public function shop_list1(){
        if(IS_POST){

            $where['token']=$this->token;
            if(isset($_GET['catid'])){
                $kk=array();
                array_push($kk,$_GET['catid']);
                $cc=array($_GET['catid']);
                $c1=M('Product_cat_new')->field('id')->where(array('parentid'=>$_GET['catid']))->select();
                foreach($c1 as $k=>$v){
                    array_push($kk,$v['id']);
                }
                foreach($c1 as $k=>$v){
                    $c2=M('Product_cat_new')->field('id')->where(array('parentid'=>$v['id']))->select();
                    foreach($c2 as $v1){
                        array_push($kk,$v1['id']);
                    }
                }
                $where['catid']=array('in',$kk);
            }
            if(isset($_GET['type'])&&$_GET['type']=='名称'){
                $where['name']=array('like','%'.$_GET['key'].'%');
            }
            if(isset($_GET['type'])&&$_GET['type']=='长'){
                $where['long']=array('elt',$_GET['key']);
            }
            if(isset($_GET['type'])&&$_GET['type']=='宽'){
                $where['wide']=array('elt',$_GET['key']);
            }

            $n=$_POST['n']*10;

            $list=M('Product_new')->field('long,wide,high,id,name,vprice,price,logourl')->where($where)->order('paixu desc,time desc')->limit($n,10)->group('name')->select();
            //echo M('Product_new')->getLastSql();
            //p($list);
            $this->assign('list', $list);

                $x = $this->fetch('./tpl/Wap/default/rh/shop_list1.html',$list);//内容放进来
                exit($x);



        }else{
            $flash=M('Product_new_flash')->field('img')->where(array('token'=>$this->token))->select();
            $where['token']=$this->token;

            if(isset($_GET['catid'])){
                $kk=array();
                array_push($kk,$_GET['catid']);
                $cc=array($_GET['catid']);
                 $c1=M('Product_cat_new')->field('id')->where(array('parentid'=>$_GET['catid']))->select();
                foreach($c1 as $k=>$v){
                    array_push($kk,$v['id']);
                }
                foreach($c1 as $k=>$v){
                    $c2=M('Product_cat_new')->field('id')->where(array('parentid'=>$v['id']))->select();
                    foreach($c2 as $v1){
                        array_push($kk,$v1['id']);
                    }
                }
                $where['catid']=array('in',$kk);
                $this->assign('catid',$_GET['catid']);
            }
            if(isset($_GET['type'])&&$_GET['type']=='名称'){
                $where['name']=array('like','%'.$_GET['key'].'%');
            }
            if(isset($_GET['type'])&&$_GET['type']=='长'){
                $where['long']=array('elt',(float)$_GET['key']);
            }
            if(isset($_GET['type'])&&$_GET['type']=='宽'){
                $where['wide']=array('elt',(float)$_GET['key']);
            }
          //  p($where);

           // $list=M('Product_new')->field('long,wide,high,id,name,vprice,price,logourl')->where($where)->order('paixu desc,time desc')->limit(10)->select();
          // p($list);
            $cats=M('Product_cat_new')->field('id,name')->where(array('token'=>$this->token,'parentid'=>0))->limit(3)->order('sort')->select();

            //首页独立的
            $cats_list=M('Product_cat_new')->field('id,name')->where(array('token'=>$this->token,'parentid'=>$cats[0]['id']))->limit(3)->order('sort')->select();

            $list=array();
            foreach($cats_list as $k=>$v){
                $cats_list[$k]['list']=M('Product_new')->field('long,wide,high,id,name,vprice,price,logourl')->where(array('token'=>$this->token,'catid'=>$v['id']))->order('paixu desc,time desc')->limit(2)->select();
                if(!$cats_list[$k]['list']){
                    unset($cats_list[$k]);
                }
            }
            foreach($cats as $k=>$v){
                $cats[$k]['son']=M('Product_cat_new')->field('id,name')->where(array('token'=>$this->token,'parentid'=>$v['id']))->select();
            }
            $this->assign('cats',$cats);
            $this->assign('list',$cats_list);

            $this->assign('flash',$flash);
            $this->UDisplay();
        }

    }
        //搜索页
    public function shop_list(){
        if(IS_POST){
            $where['token']=$this->token;
            if(isset($_GET['catid'])){
                $kk=array();
                array_push($kk,$_GET['catid']);
                $cc=array($_GET['catid']);
                $c1=M('Product_cat_new')->field('id')->where(array('parentid'=>$_GET['catid']))->select();
                foreach($c1 as $k=>$v){
                    array_push($kk,$v['id']);
                }
                foreach($c1 as $k=>$v){
                    $c2=M('Product_cat_new')->field('id')->where(array('parentid'=>$v['id']))->select();
                    foreach($c2 as $v1){
                        array_push($kk,$v1['id']);
                    }
                }
                $where['catid']=array('in',$kk);
            }
            if(isset($_GET['type'])&&$_GET['type']=='名称'){
                $where['name']=array('like','%'.$_GET['key'].'%');
            }
            if(isset($_GET['type'])&&$_GET['type']=='长'){
                $where['long']=array('elt',$_GET['key']);
            }
            if(isset($_GET['type'])&&$_GET['type']=='宽'){
                $where['wide']=array('elt',$_GET['key']);
            }

            $n=$_POST['n']*10;

            $list=M('Product_new')->field('long,wide,high,id,name,vprice,price,logourl')->where($where)->order('paixu desc,time desc')->limit($n,10)->group('name')->select();

            $this->assign('list', $list);

            $x = $this->fetch('./tpl/Wap/default/rh/shop_list1.html',$list);//内容放进来
            exit($x);





        }else{
         //   echo 8;
            $flash=M('Product_new_flash')->field('img')->where(array('token'=>$this->token))->select();
            $where['token']=$this->token;
            if(isset($_GET['catid'])){
                $kk=array();
                array_push($kk,$_GET['catid']);
                $cc=array($_GET['catid']);
                $c1=M('Product_cat_new')->field('id')->where(array('parentid'=>$_GET['catid']))->select();
                foreach($c1 as $k=>$v){
                    array_push($kk,$v['id']);
                }
                foreach($c1 as $k=>$v){
                    $c2=M('Product_cat_new')->field('id')->where(array('parentid'=>$v['id']))->select();
                    foreach($c2 as $v1){
                        array_push($kk,$v1['id']);
                    }
                }
                $where['catid']=array('in',$kk);
                $this->assign('catid',$_GET['catid']);
            }
            if(isset($_GET['type'])&&$_GET['type']=='名称'){
                $where['name']=array('like','%'.$_GET['key'].'%');
            }
            if(isset($_GET['type'])&&$_GET['type']=='长'){
                $where['long']=array('elt',(float)$_GET['key']);
            }
            if(isset($_GET['type'])&&$_GET['type']=='宽'){
                $where['wide']=array('elt',(float)$_GET['key']);
            }
            //  p($where);
            $list=M('Product_new')->field('long,wide,high,id,name,vprice,price,logourl')->where($where)->order('paixu desc,time desc')->limit(10)->group('name')->select();
            // p($list);
            $cats=M('Product_cat_new')->field('id,name')->where(array('token'=>$this->token,'parentid'=>0))->limit(3)->order('sort')->select();
            $a=array($cats[0]['id'],$cats[1]['id'],$cats[2]['id']);
            if(isset($_GET['catid'])){//1级
                $c_id=M('Product_cat_new')->field('id,name,parentid')->where(array('token'=>$this->token,'id'=>$_GET['catid']))->find();
                if(in_array($c_id['parentid'],$a)){//下一级
                    foreach($cats as $k=>$v){
                        if($c_id['parentid']==$v['id']){
                            $cats[$k]['id']=$c_id['id'];
                            $cats[$k]['name']=$c_id['name'];
                        }
                    }
                }else{//下二级
                    $c_id2=M('Product_cat_new')->field('id,name,parentid')->where(array('token'=>$this->token,'id'=>$c_id['parentid']))->find();
                    $cats[0]['name']=$c_id['name'];
                    foreach($cats as $k=>$v){
                        if($c_id2['parentid']==$v['id']){
                            $cats[$k]['id']=$c_id['id'];
                            $cats[$k]['name']=$c_id['name'];

                        }
                    }
                }
            }


            foreach($cats as $k=>$v){
                $cats[$k]['son']=M('Product_cat_new')->field('id,name')->where(array('token'=>$this->token,'parentid'=>$v['id']))->select();
            }
            //p($list);
            //商城设置

            $this->assign('cats',$cats);
            $this->assign('list',$list);
            $this->assign('flash',$flash);
            $this->UDisplay();
        }

    }
    //个人中心
    public function user_index()
    {

        if(IS_POST){
          //  p($_POST);die;
            if($id=M('Usercenter_memberlist')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('id')){
                $data['name']=$this->_post('name');
                $data['phone']=$this->_post('phone');
                $data['address']= $_POST['addr_prov'].'|'. $_POST['addr_city'].'|'.$_POST['addr_area'].'|'.$_POST['address'];
                if(M('Usercenter_memberlist')->where(array('id'=>$id))->save($data)!==false){
                    echo $this->encode(array('code'=>0,'msg'=>'保存成功','url'=>'/index.php?g=Wap&m=Rh&a=user_index&token='.$this->token."&openid=".$this->openid));exit;
                }else{
                    echo $this->encode(array('code'=>-1,'msg'=>'保存失败请重试'));exit;
                }
            }else{
                $data['name']=$this->_post('name');
                $data['token']=$this->token;
                $data['openid']=$this->openid;
                $data['uid']=$this->uid;
                $data['phone']=$this->_post('phone');
                $data['address']= $_POST['addr_prov'].'|'. $_POST['addr_city'].'|'.$_POST['addr_area'].'|'.$_POST['address'];
                if(M('Usercenter_memberlist')->add($data)){
                    echo $this->encode(array('code'=>0,'msg'=>'保存成功','url'=>'/index.php?g=Wap&m=Rh&a=user_index&token='.$this->token."&openid=".$this->openid));exit;
                }else{
                    echo $this->encode(array('code'=>-1,'msg'=>'保存失败请重试'));exit;
                }
            }


        } else {//不是post过来的
            if (!M('Usercenter_memberlist')->where(array('token' => $this->token, 'openid' => $this->openid))->getField('id')) {
                $this->assign('status', 1);//设定一个状态
                $this->display("tpl/Wap/default/rh/user_zhuce.html");
                die;
            } else {
                $info = M('Wxusers')->field('nickname,headimgurl')->where(array('id' => $this->wxusers_id))->find();
                // p($info);die;
                $this->assign('info', $info);
                $jifeng=M('Usercenter_memberlist')->where(array('token'=>$this->token,'openid'=>$this->openid))->getField('score');
              //  echo $jifeng;die;
                $this->assign('jifeng',$jifeng?$jifeng:0);
                $this->UDisplay();
            }


        }
    }

    //个人资料修改
    public function user_info(){
        $memberdata = M('Usercenter_memberlist')->field('id,address,name,phone')->where(array('uid'=>$this->uid,'openid'=>$this->openid))->find();
        $this->assign('memberdata',$memberdata);
        $this->assign('address',explode('|',$memberdata['address']));
        $this->UDisplay("user_zhuce");
    }
}