<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/15
 * Time: 8:52
 */
class CheckAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

    /**
     *  Token
     **/
    //private $_sToken = null;

    /**
     *  UID
     **/
    //private $_iUID = null;//
    public function _initialize()
    {
        parent::_initialize();
        // $this->pz	   = D('common_cs');
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '查看用户罚单记录',
                'url'  => U('Check/index', array('token' => $this->_sToken))
            ),
           array(
                'name' => '查看用户咨询记录',
                'url'  => U('Check/zhixun', array('token' => $this->_sToken))
            ),
            array(
                'name' => '查看用户消费动向',
                'url'  => U('Check/xuniye', array('token' => $this->_sToken))
            ),
            array(
                'name' => '查看会员个数',
                'url'  => U('Check/ckhygs', array('token' => $this->_sToken))
            ),
            array(
                'name' => '查看年审待办',
                'url'  => U('Check/nsdbh', array('token' => $this->_sToken))
            ),
            array(
                'name' => '导出全部用户',
                'url'  => U('Wap/Check/daochu', array('token' => $this->_sToken))
            ),

        );
    }

/*查看用户罚单记录*/
    public function index(){
        $this->table(
            array(
                'HeadHover' => U('Check/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                 /*   array(
                        'name'   => '添加提示',//2级
                        'url'    => U('Check/Check_add',array('token'=>$_SESSION['token']))
                    )*/
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','车牌号', '事发路段','记录时间','罚款金额','是否付款','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '查看用户记录',
                        'url'  => U('Check/huiyongji',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '查看消费记录',
                        'url'  => U('Check/xiaofei',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('Check_did')->where(array('token'=>$_SESSION['token']))->count(),

           M('Check_did')->field('id,openid,cph,site,site_time,dispose,payment')
               ->where(array('token'=>$_SESSION['token']))->order("id desc")->group('openid'),
            array($this,'indexinfo')

        // array($this,'abc')
        );

    }
 /*public function set_tip($aaa){
    $this->$aaa('Gjlog_tip',array(
        array('title'=>"提示语",'type'=>"longinput",'name'=>"tip_info",'value'=>'tip_info','msg'=>'请填提示语'),
    ),U('Gjlog/tip',array('token'=>$_SESSION['token'])),array($this,'tipinfo'));
}*/
    public function tipinfo($data){
        $data['token'] = $this->_sToken;
        if(!$_GET['id']){
            $data['add_time'] = date('Y-m-d H:i:s');
        }
        return $data;
    }
    public function add_tip(){
        $this->set_tip(add);
    }

    public function save_tip(){
        $this->set_tip(edit);
    }
    public function Check_sc(){
         $this->del('Check_ns');
    }


    /*这是查看用会旗下的分支，*/
     public function huiyongji(){
         $li = M('Check_did')->where(array('id'=>$_GET['id']))->find();     /*这是查他的openid*/
         $openid = $li['openid'];
        $this->table(
            array(
                'HeadHover' => U('Check/index', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(

                ),
                 'tips' => array(//3级
                    '你可以在这里管理信息   wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','车牌号', '事发路段','记录时间','罚款金额','是否付款','付款时间','操作'
                ),
                'List_Opt' => array(
                    array(

                        'name' => '删除',
                        'url'  => U('Check/delete_gnfo')
                    ),

                ),
            ),
            M('Check_did')->where(array('token'=>$_SESSION['token']))->count(),
            M('Check_did')->field('id,openid,cph,site,site_time,dispose,payment,fk_time')
                ->where(array('openid'=>$openid))->order("site_time desc"),
            array($this,'indexinfo')
        );

    }
    /*这是查询消费记录*/
    public function xiaofei(){
        $id = $_GET['id'];
        $li = M('Check_xuli')->where(array('id'=>$id))->find();     /*这是查他的openid*/
        $openid = $li['openid'];
        $this->table(
            array(
                'HeadHover' => U('Check/index', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(

                ),
                'tips' => array(//3级
                    '你可以在这里查询消费记录   wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','流出事件', '消费金额','进出','消费时间','操作'
                ),
                'List_Opt' => array(
                    array(

                        'name' => '删除',
                        'url'  => U('Check/delete_gnfo')
                    ),

                ),
            ),
            M('check_xuli')->where(array('token'=>$_SESSION['token']))->count(),
            M('check_xuli')->field('id,openid,path,money,turnover,qian_time')
                ->where(array('token'=>$_SESSION['token'],'openid'=>$openid))->order("qian_time desc"),
            array($this,'checkinfo')

        );
    }


    /*查看用户咨询记录*/
   public function zhixun(){
        $this->table(
            array(
                'HeadHover' => U('Check/zhixun', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(
                    /* array(
                         'name'   => '添加提示',//2级
                         'url'    => U('Gjlog/add_rzg',array('token'=>$_SESSION['token']))
                     )*/
                ),

                'tips' => array(//3级
                    '你可以在这里管理信息  wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','车牌号','车架号','发动机号','查询日期','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '查看更多',
                        'url'  => U('Check/zxxckgd',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('Check')->where(array('token'=>$_SESSION['token']))->count(),
            M('Check')->field('id,openid,cph,cjhLastNo6,fdjLastNo6,date_time')->
            where(array('token'=>$_SESSION['token']))->order("date_time desc")->group('openid'),
            array($this,'yonhjl')
        );
    }

/*查看用户咨询记录,下的查看更多*/
    public function zxxckgd(){
        $li = M('Check')->where(array('id'=>$_GET['id']))->find();     /*这是查他的openid*/
        $openid = $li['openid'];
        $this->table(
            array(
                'HeadHover' => U('Check/zhixun', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(

                ),
                'tips' => array(//3级
                    '你可以在这里查询消费记录   wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','车牌号','车架号','发动机号','查询日期','操作'
                ),
                'List_Opt' => array(
                    array(

                        'name' => '删除',
                        'url'  => U('Check/delete_gnfo')
                    ),

                ),
            ),
            M('Check')->where(array('token'=>$_SESSION['token']))->count(),
            M('Check')->field('id,openid,cph,cjhLastNo6,fdjLastNo6,date_time')
                ->where(array('token'=>$_SESSION['token'],'openid'=>$openid))->order("date_time desc"),
            array($this,'yonhjl')
         );
    }

    /*这是查看用户虚拟于额*/
    public function xuniye(){
        $this->table(
            array(
                'HeadHover' => U('Check/xuniye', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(
                    /* array(
                         'name'   => '添加提示',//2级
                         'url'    => U('Gjlog/add_rzg',array('token'=>$_SESSION['token']))
                     )*/
                ),

                'tips' => array(//3级
                    '你可以在这里查看用户信息  wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','流出动向','消费金额','进出','记录时间','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '查看更多',
                        'url'  => U('Check/zxxckgd',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('Check_xuli')->where(array('token'=>$_SESSION['token']))->count(),

            M('Check_xuli')->field('id,openid,path,money,turnover,qian_time')->
            where(array('token'=>$_SESSION['token']))->order("qian_time desc")->group('openid'),
            array($this,'checkinfo')
        );
    }
    /*查看会员个数,会员级别人数*/
    public function ckhygs(){
        $this->table(
            array(
                'HeadHover' => U('Check/ckhygs', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(
                    array(
                         'name'   => '查看注册会员',//2级
                         'url'    => U('Check/cha_zhu',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name'   => '查看高级会员',//2级
                        'url'    => U('Check/cha_gao',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name'   => '查看vip秘书会员',//2级
                        'url'    => U('Check/cha_vip',array('token'=>$_SESSION['token']))
                    ),
                ),
             )
         );
    }
/*注册会员查看*/
    public function cha_zhu(){
        $li = M('Check')->where(array('id'=>$_GET['id']))->find();     /*这是查他的openid*/
        $openid = $li['openid'];
        $this->table(
            array(
                'HeadHover' => U('Check/ckhygs', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(

                ),
                'tips' => array(//3级
                    '你可以在这里查询消费记录   wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','是否高级会员','是否vip秘书','虚拟帐户于额'
                ),
            ),


        M('check_hy')->where("expert is null and vipsec is null and token= '" .$_SESSION['token'] . "'")->count(),
            M('check_hy')->field('id,openid,expert,vipsec,account')
                ->where("expert is null and vipsec is null and token= '" .$_SESSION['token'] . "'"
                )->order("id desc"),
            array($this,'huiyaninfo')
        );
    }

/*查看高级会员*/
    public function cha_gao(){
        $li = M('Check')->where(array('id'=>$_GET['id']))->find();     /*这是查他的openid*/
        $openid = $li['openid'];
        $this->table(
            array(
                'HeadHover' => U('Check/ckhygs', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(

                ),
                'tips' => array(//3级
                    '你可以在这里查询消费记录   wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','是否高级会员','是否vip秘书','虚拟帐户于额'
                ),
            ),


            M('check_hy')->where(array('token'=>$_SESSION['token'],
                'vipsec'=>array('eq',2)
            ))->count(),
            M('check_hy')->field('id,openid,expert,vipsec,account')
                ->where(array('token'=>$_SESSION['token'],
                        'expert'=>array('eq',1))
                )->order("id desc"),
            array($this,'huiyaninfo')
        );
    }

    /*查看vip秘书会员*/
    public function cha_vip(){
        $li = M('Check')->where(array('id'=>$_GET['id']))->find();     /*这是查他的openid*/
        $openid = $li['openid'];
        $this->table(
            array(
                'HeadHover' => U('Check/ckhygs', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(

                ),
                'tips' => array(//3级
                    '你可以在这里查询消费记录   wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','是否高级会员','是否vip秘书','虚拟帐户于额'
                ),
            ),


            M('check_hy')->where(array('token'=>$_SESSION['token'],
                'vipsec'=>array('eq',1)
            ))->count(),
            M('check_hy')->field('id,openid,expert,vipsec,account')
                ->where(array('token'=>$_SESSION['token'],
                        'vipsec'=>array('eq',1))
                )->order("id desc"),
            array($this,'huiyaninfo')
        );
    }

    /*这是年审查询*/
    public function nsdbh(){
        $this->table(
            array(
                'HeadHover' => U('Check/nsdbh', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(

                ),
                'tips' => array(//3级
                    '你可以在这里查询消费记录   wcx'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','姓名','电话','车牌号','车辆识别号','发动机号','时间','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '查看更多',
                        'url'  => U('Check/nsxiaj',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),


            M('check_ns')->where(array('token'=>$_SESSION['token']))->count(),
            M('check_ns')->field('id,openid,username,dianh,cph,sbcph,fadong,nstime')
                ->where(array('token'=>$_SESSION['token']))->order("id desc")->group('openid'),
            array($this,'huiyaninfo')
        );
    }
/*年审下级*/
    public function nsxiaj(){
        $id=$_GET['id'];
        $li = M('Check_ns')->where(array('id'=> $id))->find();     /*这是查他的openid*/
        $openid = $li['openid'];
        $this->table(
            array(
                'HeadHover' => U('Check/nsdbh', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(
                    /* array(
                         'name'   => '添加提示',//2级
                         'url'    => U('Gjlog/add_rzg',array('token'=>$_SESSION['token']))
                     )*/
                ),

                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','姓名','电话','车牌号','车辆识别号','发动机号','时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Check/Check_sc',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('Check_ns')->where(array('token'=>$_SESSION['token']))->count(),
            M('Check_ns')->field('id,openid,username,dianh,cph,sbcph,fadong,nstime')->
            where(array('token'=>$_SESSION['token'],'openid'=>$openid))->order("nstime desc"),
            array($this,'huiyaninfo')
        );
    }


    /*获取微信相关信息*/
    function wxinfo($openid){
        $wxuser = M('Wxuser')
            ->where(array('token'=>$this->_sToken))
            ->find();
        $wxusers = M('Wxusers')
            ->where(array(
                'uid'=>$wxuser['id'],
                'openid'=>$openid
            ))->find();
        return $wxusers;
    }
    /*首页返回值*/
    function indexinfo($data){
        foreach($data as $key=>$val){
            if($val['payment']==1) {
                $data[$key]['payment'] = "是";
            }else{
                $data[$key]['payment'] = "否";
            }
            $wxinfo = $this->wxinfo($val['openid']);
            $data[$key]['openid'] = $wxinfo['nickname'];
         /*   $data[$key]['add_time'] = date('Y-m-d H;i',$val['add_time']);*/
        }
        return $data;
    }
/*消费也返回值*/
    function checkinfo($oepej){
        foreach($oepej as $k=>$v){
            if($v['turnover']==1){
                $oepej[$k]['turnover'] = "支出";
            }else{
                $oepej[$k]['turnover'] = "存入";
            }
            $wxinfo = $this->wxinfo($v['openid']);
            $oepej[$k]['openid'] = $wxinfo['nickname'];
        }
            return $oepej;
    }
/*用户记录*/
    function yonhjl($oepej){
        foreach($oepej as $k=>$v){
            $wxinfo = $this->wxinfo($v['openid']);
            $oepej[$k]['openid'] = $wxinfo['nickname'];
        }
        return $oepej;
    }

    /*是否会员页返回值*/
    function huiyaninfo($opej){
        foreach($opej as $k=>$v){
            if($v['expert']==1){
                $opej[$k]['expert'] = "是";
            }
            if($v['vipsec']==1){
                $opej[$k]['vipsec'] = "是";
            }
            $wxinfo = $this->wxinfo($v['openid']);
            $opej[$k]['openid'] = $wxinfo['nickname'];
        }
        return $opej;
    }






}