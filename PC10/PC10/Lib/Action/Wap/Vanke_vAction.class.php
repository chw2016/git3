<?php
/*
 * 万科团购
 */
class Vanke_vAction extends BaseAction{

    /*
     * Tpl Dir
     */
    private $Vanke;
    protected $_sTplBaseDir = 'Wap/default/vanke';

    protected function _initialize(){
        parent::_initialize();
    }

    public function index()
    {
         if(session('?movieid')){

                if ($code=$_GET['code']) {
                    //订单信息
                    if($_GET['verify']){
                        WL('verifyData:' . print_r($_REQUEST, true) . print_r($_SERVER, true), 'vanke_verify.log');
                        $Order = M('Group_order')->where(array(
                            'status' => 1,
                            'movieid' => session(movieid),
                            'sn'   => array('like', '%,' . $code . ',%')
                        ))->find();

                        $aSN  = explode(',', $Order['sn']);
                        $aTBaseSN = explode(',', $Order['used_sn_time']);
                        $aTSN= array();
                        foreach ($aSN as $k => $sn) {
                            if ($sn) {
                                if (!$aTBaseSN[$k] && $sn == $code) {
                                    $aTSN[ ] = date('Y-m-d H:i:s');
                                }else{
                                    $aTSN[] = $aTBaseSN[$k];
                                }
                            }
                        }
                        
                        $Order = M('Group_order')->where(array(
                            'sn'   => array('like', '%,' . $code . ',%')
                        ))->data(array('used_sn_time' => ',' . implode(',', $aTSN) . ','))->save();

                         //添加验证记录
                        $usedsn = M('group_usedsn');

                        $Order = M('Group_order')->where(array(
                            'status' => 1,
                            'movieid' => session(movieid),
                            'sn'   => array('like', '%,' . $code . ',%')
                        ))->find();

                        $aSN  = explode(',', $Order['sn']);
                        $aTBaseSN = explode(',', $Order['used_sn_time']);

                        foreach ($aSN as $sk => $sv) {
                            if($aTBaseSN[$sk] && $sv == $code)
                                $used_time = $aTBaseSN[$sk];
                        }

                        if(!$usedsn->where(array(
                            'used_sn' => $code
                            ))->find())
                            $usedsn -> data(array(
                                'movieid' => session(movieid),
                                'used_sn' => $code ,
                                'used_time'  => $used_time
                                )) ->add();

                    }

                    
                    //查询
                    $msg = M('Group_order')->where(array(
                        'status' => 1,
                        'movieid' => session(movieid),
                        'sn'   => array('like', '%,' . $code . ',%')
                    ))->find();


                    $aSN2 = explode(',', $msg['sn']);
                    $aTSN2 = explode(',', $msg['used_sn_time']);
                    $time = null;

                    foreach ($aSN2 as $key => $sn) {
                        if ($sn == $code && $aTSN2[$key]) {
                            $time = $aTSN2[$key];
                        }
                    }

                    $this->assign('time', $time);

                    //商品
                    //根据openid获取用户信息
                    $this->Vanke = $Vanke = new Vanke();
                    $aVankeUser = $Vanke->getUserInfo($msg['openid']);
                    if (200 == $aVankeUser['code']) {
                        $aVankeUser = $aVankeUser['data'];
                    }
                    /*
                    $aVankeBindUser = $Vanke->getVankeBindUser($this->openid);
                    if (200 == $aVankeBindUser['code']) {
                        $aVankeBindUser = $aVankeBindUser['data'];
                    }
                    */

                    $aPro = M('Groupbuy_product')->where(array('id' => $msg['product_id']))->find();
                    $this->assign(array(
                        'msg'           => $msg,
                        'vankeuser'     => $aVankeUser,
                        'bindUser'      => $aVankeBindUser,
                        'moviename'     => M('Group_movie')->where(array('id' => $msg['movieid']))->getField('name'),
                        'product'       => $aPro,
                        'code'          => $code
                    ));
                }
                    //已验证查询
                    if(isset($_GET['allget'])){

                        $count=M('group_usedsn')->count();
            
                        import('ORG.Util.Page');
                        $page=new Page($count,8);//后台管理页面默认一页显示8条文章记录
                        
                        $page->setConfig('prev', "&laquo; Previous");//上一页
                        $page->setConfig('next', 'Next &raquo;');//下一页
                        $page->setConfig('first', '&laquo; First');//第一页
                        $page->setConfig('last', 'Last &raquo;');//最后一页
                        $page->setConfig('theme',' %first% %upPage%  %linkPage%  %downPage% %end%');

                        /*print_r(session(movieid));
                        die();*/

                        $sn_list=M('group_usedsn')
                            ->where(array('movieid'=>session(movieid)))
                            ->field(array('movieid','used_sn','used_time'))
                            ->limit($page->firstRow.','.$page->listRows)
                            ->select();

                            $movieid = M('Group_movie')->field(array('id','name'))->select();

                        foreach ($sn_list as $key => $value) {
                            foreach ($movieid as $mk => $mv) {
                                if($sn_list[$key]['movieid'] == $movieid[$mk]['id'])
                                    $sn_list[$key]['movieid'] = $movieid[$mk]['name'];
                            }
                        }

                        $this->assign('sn_list',$sn_list);
                    }

            $this->UDisplay('verify');
        }else
        {
            $this->error2('您好，请先登录',U('login'));
        }
    }

    function login(){
        $this->UDisplay('login');
    }

    function login_admin(){
        if( $_SESSION['verify'] !=  md5($_POST['verify'])   ){
                $this->error2('验证码不正确');
            }
        
            $user=M('group_movie');
            $name=$_POST['movie_admin'];
            $password=md5($_POST['movie_password']);
            
            if(!$this->checklen($name)){
                $this->error2('用户名长度必须在5~15个字符之间');
            }
            
            if($user->where("movie_admin ='$name' AND movie_password = '$password'")->find()){
                
                $movieid = $user->where(array('movie_admin'=>$name))->getField('id');

                session(movieid,$movieid);  

                redirect('?g=Wap&m=Vanke_v&a=',0, '跳转中...');
            }else{
                $this->error2('用户名或密码错误');
            }
    }

    function checklen($data){
            if(strlen($data)>15 || strlen($data)<5){
            return false;
        }
        return true;
    }

    Public function verify(){
        import('ORG.Util.Image');
        Image::buildImageVerify(4,1,'png',70,30);
    }

}
