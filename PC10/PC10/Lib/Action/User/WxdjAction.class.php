<?php
/**
 *  微信兑奖
 **/
class WxdjAction extends Table1Action {

    public $_sTplBaseDir = 'User/default/miye';
    public $tpl_dir = './tpl/User/default/wxdj/';


    public function _initialize()
    {
        parent::_initialize();
        $this->pz      = D('No_credit');
        $this->pz1   = D('Credit');
        $this->order  =M('No_credit_order');//订单表
        $this->tpl="tpl/User/default/helper/";
        $this->token=$this->_sToken;
    }

    protected function setHeader(){
        return array(
            array(
                    'name' => '产品列表',
                    'url'  => U('index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '用户信息',
                'url'  => U('users', array('token' => $this->_sToken))
            ),
            array(
                'name' => '兑奖信息管理',
                'url'  => U('expiry', array('token' => $this->_sToken))
            ),
            array(
                'name' => '日志信息',
                'url'  => U('log', array('token' => $this->_sToken))
            ),
            /*array(
                'name' => '中奖名单管理',
                'url'  => U('winners', array('token' => $this->_sToken))
            ),*/
            array(
                'name' => '摇奖用户信息',
                'url'  => U('luckjoy', array('token' => $this->_sToken))
            ),
            array(
                'name' => '短信通知',
                'url'  => U('val', array('token' => $this->_sToken))
            ),
            array(
                'name' => '后台操作日记',
                'url'  => U('admin', array('token' => $this->_sToken))
            ),
            array(
                'name' => '一键中奖',
                'url'  => U('yi', array('token' => $this->_sToken))
            ),
            array(
                'name' => '管理',
                'url'  => U('manage', array('token' => $this->_sToken))
            ),
            /*array(
                'name' => '摇奖信息',
                'url'  => U('yjwin', array('token' => $this->_sToken))
            ),*/
            array(
                'name' => '摇奖记录',
                'url'  => U('win', array('token' => $this->_sToken))
            ),

            );
    }
    public function manage()
    {
        $this->assign('tishi','后台销售人员入口的登录账户和密码');
        $this->Edit('powers',
        array(
            array('title'=>'用户名','type'=>'input','name'=>'username'),
            array('title'=>'密码','type'=>'password','name'=>'password'),
        ),
        U('index',array('token'=>$this->_sToken)),array($this,'manageinfo'));
    }

    public function manageinfo($data){
        $data['password'] = md5($data['password']);
        return $data;
    }
    public function yi(){
        $aWhere='';
        $order='';
        $aWhere['token'] =$this->_sToken;
        $aWhere['is_dj'] = 0;
        session('where_p',$aWhere);
        if(IS_POST){
            //$order=$_POST['paixu_form'];
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                  //  'id' => 'name',//如果主键不是id，则需要设置
                    'HeadHover' => U('yi', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                       /* array(
                            'name' => '添加',
                            'url' => U('add_houses',array('token'=>$this->token))
                        ),*/
                      /*  array(
                            'name' => '导出',
                            'type'=>'daochu',//type=daochu时，可以带上搜索条件
                            'url' => U('excel',array('token'=>$this->token))
                        ),*/
                         array(
                        'name' => '一键中奖',
                        'type'=>'yijin',
                        'url' => U('yijin')
                          ),



                    ),
                    'tips' => array(
                        '产品列表',
                        '中奖成功时需要刷新才能看到信息'
                    ),
                    'Table_Header' => array(
                        'ID', '卡密','卡明码','sn码','客户编码','发货时间','是否验证sn码','是否有奖','是否被兑换',''
                    ),
                    'List_Opt' => array(
                       /* array(
                            'name' => '修改',
                            'url' => U('edit_houses')
                        ),
                        array(
                            'name' => '修改',
                            'url' => U('del_houses')
                        ),*/


                    ),
                  'search'=>array(
                    array('title'=>'客户编码','name'=>'li_customer_code'),
                    array('title'=>'发货时间','name'=>'f_time','type'=>'between')
                    )
                ),
                M('js_sn')->where($aWhere)->order($order)->count(),
                M('js_sn')->field('id,secret,card,sn,customer_code,f_time,is_sn,is_yj,is_dj')->order($order)->where($aWhere),
                array($this,'abct')
            );
       $this->assign('duoxuan',1);
        $this->UDisplay('show1');
    }
    public function abct($data){
        foreach($data as $k=>$v){
            if($v['is_yj']==0){
                $data[$k]['is_yj']='没有奖';
            }

            if($v['is_yj']==1){
                $data[$k]['is_yj']='有奖';
            }

            if($v['is_dj']==0){
                $data[$k]['is_dj']='未兑换';
            }
            if($v['is_dj']==1){
                $data[$k]['is_dj']='已兑换';
            }
            if($v['is_dj']==2)
            {
                $data[$k]['is_dj']='兑换中';
            }
            if($v['is_sn']==1){
                $data[$k]['is_sn']='是';
            }
            if($v['is_sn']==0)
            {
                $data[$k]['is_sn']='否';
            }
            //$data[$k]['f_time'] = date('Y-m-d H:i:s',$v['f_time']);

        }
        return $data;
    }
    public function yijin()
    {
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        $da['content'] = '设置中奖';
        M('all_log')->add($da);

        $id = $_POST['id'];
        $p = explode('-',$id);

        $data['is_yj'] = 1;
            $c = M('js_sn')
            ->where(array(
                'token'=>$this->token,
                'id'=>array('in',$p)))
            ->save($data);
            if($c)
            {
                $this->success2('设置成功');
                header("Location:".U('index',array('token'=>$this->token)));;
            }else
            {
                $this->error2('设置失败');
            }


        //$c = M('js_sn')->where(array('token'=>$this->token,'id'=>$id))->select();
        //p($c);die;
    }

    /**
     *
     **/
    public function index(){
        $aWhere='';
        $order='';
        $aWhere['token'] =$this->_sToken;
        session('where_p',$aWhere);
        //if(IS_POST){
            //$order=$_POST['paixu_form'];
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        //}else{
            //get 过来P分页时，带上条件查询数据
            //if(isset($_GET['p'])&&session('?where_p')){
                //$aWhere=session('where_p');
            //}
        //}

            $this->table(
                array(
                  //  'id' => 'name',//如果主键不是id，则需要设置
                    'HeadHover' => U('index', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        array(
                            'name' => '导入EXCEL',
                            'url' => U('add_excel',array('token'=>$this->token))
                        ),
                      /*  array(
                            'name' => '导出',
                            'type'=>'daochu',//type=daochu时，可以带上搜索条件
                            'url' => U('excel',array('token'=>$this->token))
                        ),
                         array(
                        'name' => '一键中奖',
                        'type'=>'yijin',
                        'url' => U('yijin')
                          ),*/



                    ),
                    'tips' => array(
                        '产品列表'
                    ),
                    'Table_Header' => array(
                        'ID', '卡密','卡明码','sn码','客户编码','发货时间','是否有奖','是否被兑换',''
                    ),
                    'List_Opt' => array(
                       /* array(
                            'name' => '修改',
                            'url' => U('edit_houses')
                        ),
                        array(
                            'name' => '修改',
                            'url' => U('del_houses')
                        ),*/


                    ),
                  'search'=>array(
                      array('title'=>'卡密','name'=>'li_secret'),
                      array('title'=>'sn码','name'=>'li_sn'),
                      array('title'=>'是否有奖','type'=>'select','name'=>'eq_is_yj','many'=>array(
                              array('name'=>'有奖','value'=>'1'),
                              array('name'=>'没有奖','value'=>'0'),
                          )),
                      array('title'=>'是否已被兑换','type'=>'select','name'=>'eq_is_dj','many'=>array(
                          array('name'=>'已被兑换','value'=>'1'),
                          array('name'=>'未被兑换','value'=>'0'),
                          array('name'=>'兑换中','value'=>'2'),
                      )),
                             array('type'=>'br'),
                    array('title'=>'客户编码','name'=>'li_customer_code'),
                    array('title'=>'发货时间','name'=>'f_time','type'=>'between')
                    )
                ),
                M('js_sn')->where($aWhere)->order('f_time desc')->count(),
                M('js_sn')->field('id,secret,card,sn,customer_code,f_time,is_yj,is_dj')->order('f_time desc')->where($aWhere),
                array($this,'abc')
            );
       //$this->assign('duoxuan',1);
        $this->UDisplay('show1');
    }
    public function add_excel(){
        //$this->assign('jd',$_GET['type']);

        $this->display('add_excel');
    }

    //接口请求，拿产品数据
    public function get_sn(){
        file_put_contents('z.txt','few');
        if(IS_POST){
            //$data=json_decode($_POST['data'],true);
            $data=$_POST['data'];
            file_put_contents('b.php',"<?php\r\nreturn ".var_export($data,true)."?>");

            echo 1;
        }
    }

    public function abc($data){
        foreach($data as $k=>$v){
            if($v['is_yj']==0){
                $data[$k]['is_yj']='没有奖';
            }

            if($v['is_yj']==1){
                $data[$k]['is_yj']='有奖';
            }

            if($v['is_dj']==0){
                $data[$k]['is_dj']='未兑换';
            }
            if($v['is_dj']==1){
                $data[$k]['is_dj']='已兑换';
            }
            if($v['is_dj']==2)
            {
                $data[$k]['is_dj']='兑换中';
            }
            //$data[$k]['f_time'] = date('Y-m-d H:i:s',$v['f_time']);

        }
        return $data;
    }

    //用户信息
    public function users(){
        $aWhere='';
        $aWhere['tp_js_users.token'] =$this->_sToken;
        $aWhere['tp_js_users.type'] =1;
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['tp_js_users.token'] =$this->_sToken;
            $aWhere['tp_js_users.type'] =1;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('users', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                     array(
                          'name' => '导出Excel表',
                          'url' => U('excel',array('token'=>$this->_sToken))
                      ),
                    ),
                    'tips' => array(
                        '用户列表',
                        '后台销售人员入口 http://wx.jasic.com.cn/index.php?g=User&m=Branch&a=index&token=5d8a87bab30de695954b17fc835b9d12&modulename=Powers'
                    ),
                    'Table_Header' => array(
                        '用户编号','用户名称', '微信名称','电话号码','地址','备注','时间','是否中奖','状态','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name'=>'核审',
                            'url'=>U('Review_the_user')
                        )
                    ),
                  'search'=>array(
                      array('title'=>'用户名称','name'=>'li_name'),
                      array('title'=>'状态','name'=>'eq_tp_js_users|status','type'=>'select','many'=>array(
                        array('value'=>'2','name'=>'通过'),
                        array('value'=>'3','name'=>'不通过'),
                        array('value'=>'1','name'=>'审核中'),
                    )),

                    )
                ),
                M('js_users')->join('left join tp_wxusers on tp_wxusers.id = tp_js_users.uid')->where($aWhere)->count(),
                M('js_users')->join('left join tp_wxusers on tp_wxusers.id = tp_js_users.uid')->field('tp_js_users.id,tp_js_users.name,tp_wxusers.nickname,tp_js_users.phone,tp_js_users.address,tp_js_users.other,tp_js_users.add_time,tp_js_users.type,tp_js_users.status')->order('tp_js_users.add_time desc')->where($aWhere),
                array($this,'users_1')
            );
      // $this->assign('duoxuan',1);
        $this->UDisplay('show1');
    }
    //核审用户
    public function Review_the_user()
    {

        $getid = M('js_users')->find($_GET['id']);
        if($getid['userid']){
            $id = M('js_dj')->where(array('token'=>$this->token,'id'=>$getid['userid']))->find();
            $sid = M('js_sn')->where(array('token'=>$this->token,'id'=>$id['sid']))->find();
        }else{
            $id = M('js_dj')->where(array('token'=>$this->token,'s_name'=>$getid['name'],'s_phone'=>$getid['phone'],'d_time'=>$getid['add_time']))->find();
            $sid = M('js_sn')->where(array('token'=>$this->token,'id'=>$id['sid']))->find();
        }
        $this->assign('sid',$sid);
        $this->assign('id',$id);
        $this->assign('info',$getid);
        $this->display('./tpl/User/default/wxdj/dj.html');

    }
    public function Review_the_users()
    {

        $getid = M('js_users')->find($_GET['id']);
        if($getid['userid']){
            $id = M('js_dj')->where(array('token'=>$this->token,'id'=>$getid['userid']))->find();

            $sid = M('js_sn')->where(array('token'=>$this->token,'id'=>$id['sid']))->find();
        }else{
            $id = M('js_dj')->where(array('token'=>$this->token,'s_name'=>$getid['name'],'s_phone'=>$getid['phone'],'d_time'=>$getid['add_time']))->find();

            $sid = M('js_sn')->where(array('token'=>$this->token,'id'=>$id['sid']))->find();
        }
        $this->assign('sid',$sid);
        $this->assign('id',$id);
        $this->assign('info',$getid);
        $this->display('./tpl/User/default/wxdj/yj.html');

    }
    public function chuli()
    {
        $id = $_GET['id'];
        $phone = M('js_users')->where(array('token'=>$this->token,'id'=>$id))->getfield('phone');
        $name = M('js_users')->where(array('token'=>$this->token,'id'=>$id))->getfield('name');
        $userid = M('js_users')->where(array('token'=>$this->token,'id'=>$id))->getfield('userid');
        $add_time = M('js_users')->where(array('token'=>$this->token,'id'=>$id))->getfield('add_time');
        $val = M('js_set')->where(array('token'=>$this->token))->getfield('val');//短信通知
        $noval = M('js_set')->where(array('token'=>$this->token))->getfield('noval');
        $data['status'] = $_POST['kk'];
        $data['other'] = $_POST['other'];
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['content'] = '核审'.$name.'信息';
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        M('all_log')->add($da);
        if($_POST['kk']==2){

            senddj($phone,$val);
            //改变为有效
            if($userid){
                $datat['status'] = 3;
                M('js_dj')
                ->where(array('token'=>$this->token,'id'=>$userid))
                ->save($datat);
            }else{
                $datat['status'] = 3;
                M('js_dj')
                ->where(array('token'=>$this->token,'s_name'=>$name,'s_phone'=>$phone,'d_time'=>$add_time))
                ->save($datat);
            }


        }if($_POST['kk']==3){

            senddj($phone,$noval);
			
            if($userid){
                $dataf['status'] = 0;
                M('js_dj')
                ->where(array('token'=>$this->token,'id'=>$userid))
                ->save($dataf);
                //设置为已兑奖
                $sid = M('js_dj')
                ->where(array('token'=>$this->token,'id'=>$userid))
                ->getfield('sid');
                $SN['is_dj'] = 0;
                M('js_sn')
                ->where(array('token'=>$this->token,'id'=>$sid))
                ->save($SN);
            }else{
                $dataf['status'] = 0;
                M('js_dj')
                ->where(array('token'=>$this->token,'s_name'=>$name,'s_phone'=>$phone,'d_time'=>$add_time))
                ->save($dataf);
                //设置为已兑奖
                $sid = M('js_dj')
                ->where(array('token'=>$this->token,'s_name'=>$name,'s_phone'=>$phone,'d_time'=>$add_time))
                ->getfield('sid');
                $SN['is_dj'] = 0;
                M('js_sn')
                ->where(array('token'=>$this->token,'id'=>$sid))
                ->save($SN);
            }


        }
        if(M('js_users')->where(array('token'=>$this->token,'id'=>$id))->save($data))
        {
            echo json_encode(array('status'=>1));
        }else{
            echo json_encode(array('status'=>2));
        }

    }
    public function chuli1()
    {

        $id = $_GET['id'];
        $phone = M('js_users')->where(array('token'=>$this->token,'id'=>$id))->getfield('phone');
        $name = M('js_users')->where(array('token'=>$this->token,'id'=>$id))->getfield('name');
        $userid = M('js_users')->where(array('token'=>$this->token,'id'=>$id))->getfield('userid');
        $add_time = M('js_users')->where(array('token'=>$this->token,'id'=>$id))->getfield('add_time');

        $data['is_if'] = $_POST['kk'];
        $data['other'] = $_POST['other'];
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['content'] = '核审'.$name.'信息';
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        M('all_log')->add($da);
        if($userid){
            $datat['status'] = 3;
                M('js_dj')
                ->where(array('token'=>$this->token,'id'=>$userid))
                ->save($datat);
        }else{
            $datat['status'] = 3;
            M('js_dj')
            ->where(array('token'=>$this->token,'s_phone'=>$phone,'s_name'=>$name,'d_time'=>$add_time))
            ->save($datat);
        }
        if(M('js_users')->where(array('token'=>$this->token,'id'=>$id))->save($data))
        {
            echo json_encode(array('status'=>1));
        }else{
            echo json_encode(array('status'=>2));
        }

    }
    public function users_1($data){
        foreach($data as $k=>$v){
            if($v['status']==1)
            {
                $data[$k]['status']='审查中';
            }
            if($v['status']==2)
            {
                $data[$k]['status']='通过';
            }
            if($v['status']==3)
            {
                $data[$k]['status']='不通过';
            }
            $data[$k]['type']='中奖';
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    //兑奖信息
    public function expiry()
    {
        $aWhere='';
        $aWhere['tp_js_dj.token'] =$this->_sToken;
        $aWhere['tp_js_dj.type'] =1;
        $aWhere['tp_js_dj.status'] =array('in','1,2,3,4');
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['tp_js_dj.token'] =$this->_sToken;
            $aWhere['tp_js_dj.type'] =1;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('expiry', array('token' => $this->_sToken)),

                      'tips' => array(
                        '兑奖列表'

                    ),
                    'Table_Header' => array(
                        '用户ID','对应sn','对应卡密','收件人', '收件人号码','物流公司','快递单号','收件地址','兑奖时间','状态','是否兑奖的',''
                    ),

                    'List_Opt' => array(
                         array(
                             'name' => '处理订单',
                             'url' => U('shop_status_update')
                         ),
                         array(
                             'name' => '详情',
                             'url' => U('detail')
                         ),

                    ),
                  'search'=>array(
                      array('title'=>'收货人电话','name'=>'li_s_phone'),
                      array('title'=>'收货人姓名','name'=>'li_s_name'),
                             array('type'=>'br'),
                      array('title'=>'状态','type'=>'select','name'=>'eq_status','many'=>array(
                              array('name'=>'发货','value'=>'1'),
                              array('name'=>'收货','value'=>'2'),
                              array('name'=>'有效','value'=>'3'),
                              array('name'=>'无效','value'=>'4'),
                          )),
                    )
                ),
                M('js_dj')->join('left join tp_js_sn on tp_js_sn.id=tp_js_dj.sid')->order('tp_js_dj.d_time desc')->where($aWhere)->count(),
                M('js_dj')->join('left join tp_js_sn on tp_js_sn.id=tp_js_dj.sid')->
                field('tp_js_dj.id,tp_js_sn.sn,tp_js_sn.secret,tp_js_dj.s_name,tp_js_dj.s_phone,tp_js_dj.logistics,tp_js_dj.logistics_num,tp_js_dj.s_address,tp_js_dj.d_time,tp_js_dj.status,tp_js_dj.type')
                ->order('tp_js_dj.d_time desc')
                ->where($aWhere),
                array($this,'dj_status')
            );
        $this->UDisplay('show1');

    }
    public function dj_status($data)
    {
        foreach($data as $k=>$v)
        {
            if($v['status']==0)
            {
                $data[$k]['status']='刚兑奖';
            }
            if($v['status']==1){
                $data[$k]['status']='发货';
            }
            if($v['status']==2){
                $data[$k]['status']='收货';
            }
            if($v['status']==3){
                $data[$k]['status']='有效';
            }
            if($v['status']==4){
                $data[$k]['status']='无效';
            }
            if($v['type']==1){
                $data[$k]['type']='兑奖的';
            }
            if($v['type']==2){
                $data[$k]['type']='摇奖的';
            }
            $data[$k]['d_time']=date('Y-m-d H:i:s',$v['d_time']);
            //$data[$k]['f_time']=date('Y-m-d H:i:s',$v['f_time']);

        }
        return $data;
    }
    //导出用户数据
    //导出excel表
    public function excel()
    {
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        $da['content'] = '导出用户excel';
        M('all_log')->add($da);
        $data = M('js_users')->join('left join tp_wxusers on tp_wxusers.id = tp_js_users.uid')->field('tp_js_users.id,tp_js_users.name,tp_wxusers.nickname,tp_js_users.phone,tp_js_users.address,tp_js_users.add_time,tp_js_users.type,tp_js_users.status')->order('tp_js_users.add_time')->where(array('token'=>$this->_sToken))->select();
        foreach($data as $k=>$v)
        {
            if($v['type']==1)
            {
                $data[$k]['type']='中奖';
            }
            if($v['status']==1)
            {
                $data[$k]['status']='审查中';
            }
            if($v['status']==2)
            {
                $data[$k]['status']='通过';
            }
            if($v['status']==3)
            {
                $data[$k]['status']='不通过';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        Excel::arr2ExcelDownload($data,array('用户编号', '名称','微信名称','电话号码','地址','时间','是否中奖','状态'),'用户信息');
    }
    public function shop_status_update()
    {
        $info = M('js_dj')->find($_GET['id']);
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        $da['content'] = '处理用户订单';
        M('all_log')->add($da);
        /*if($info['status']==1)
        {   //echo 2;die;
            sendPhomeCode($this->token,$info['s_phone']);

        }*/
        if($info['status']==4)
        {
            $this->error2('该用户还没有核审');
        }
        if($info['status']==2)
        {
            $this->error2('已经处理了');
        }
        $this->Edit('js_dj',
        array(
            array('title'=>'物流公司','type'=>'input','name'=>'logistics','placeholder'=>'请输入中文的字体','msg'=>'物流公司不能为空'),
            array('title'=>'快递单号','type'=>'input','name'=>'logistics_num','placeholder'=>'请输入中文的字体','msg'=>'快递单号不能为空'),
            array('title'=>'电脑系列号','type'=>'input','name'=>'number','placeholder'=>'请输入中文的字体','msg'=>'电脑系列号不能为空'),
            array('type'=>'hidden','name'=>'s_phone'),
            array('type'=>"hidden",'name'=>"f_time"),
            array('type'=>"hidden",'name'=>"s_time"),
            array('title'=>'审核','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"发货",'value'=>"1"),
                array('content'=>"收货",'value'=>"2")
            )),

        ),

        U('expiry',array('token'=>$this->_sToken)),array($this,'cc1'));
    }
    public function cc1($data){
        if($data['status']==1)
        {   $data['f_time'] = time();
            $str1 = array('YYYY','XXXX','00');
            $str2 = array($data['number'],$data['logistics'],$data['logistics_num']);
            $str = str_replace($str1,$str2,'【佳士科技】：您的平板电脑已发货。电脑系列号：YYYY，快递公司：XXXX，快递单号：00');
            senddj($data['s_phone'],$str);
        }
        if($data['status']==2)
        {
            $data['s_time']=time();
        }
        return $data;
    }
    public function log()
    {
        $aWhere='';
        $aWhere['token'] =$this->_sToken;
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('log', array('token' => $this->_sToken)),

                      'tips' => array(
                        '日记列表'
                    ),
                    'Table_Header' => array(
                        '用户ID','姓名','内容','卡密', '时间',''
                    ),
                    'List_Opt' => array(

                    /*array(
                        'name' => '删除',
                        'url' => U('del_log')
                    ),*/

                    ),
                    'Head_Opt' => array(
                     array(
                          'name' => '导出Excel表',
                          'url' => U('excellog',array('token'=>$this->_sToken))
                      ),
                    ),
                    'search'=>array(
                      array('title'=>'卡密','name'=>'li_secret'),
                    )
                ),
                M('js_log')->field('id,name,info,secret,add_time')->where($aWhere)->count(),
                M('js_log')
                ->field('id,name,info,secret,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'logtime')

            );
        $this->UDisplay('show1');
    }
    /*public function del_log(){
        $this->del('js_log');
    }*/
    /*public function yjwin()
    {
        $aWhere='';
        $aWhere['token'] =$this->_sToken;
        $aWhere['status']= 2;
        $aWhere['is_if']= 1;
        $aWhere['type']= 2;
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            $aWhere['status']= 2;
            $aWhere['is_if']= 1;
            $aWhere['type']= 2;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('yjwin', array('token' => $this->_sToken)),

                      'tips' => array(
                        '要中奖用户列表'
                    ),
                    'Table_Header' => array(
                        '用户编号','用户名称','电话号码','地址','时间',''
                    ),
                    'List_Opt' => array(

                    /*array(
                        'name' => '删除',
                        'url' => U('del_log')
                    ),

                    ),
                    'Head_Opt' => array(
                     /*array(
                          'name' => '导出Excel表',
                          'url' => U('excellog',array('token'=>$this->_sToken))
                      ),
                    ),
                ),
                M('js_users')->field('id,name,phone,address,add_time')->where($aWhere)->count(),
                M('js_users')
                ->field('id,name,phone,address,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'logtime')

            );
        $this->UDisplay('show1');
    }*/
  public function yjwin()
    {
        $aWhere='';
        $aWhere['tp_js_dj.token'] =$this->_sToken;
        $aWhere['tp_js_dj.type'] =2;
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['tp_js_dj.token'] =$this->_sToken;
            $aWhere['tp_js_dj.type']= 2;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
            $this->table(
                array(
                    'HeadHover' => U('yjwin', array('token' => $this->_sToken)),

                      'tips' => array(
                        '发货信息列表',
                        '请注意：当摇奖用户信息通过和中奖时再发货'



                    ),
                    'Table_Header' => array(
                        '用户ID','对应sn','对应卡密','收件人', '收件人号码','物流公司','快递单号','收件地址','兑奖时间','是否通过','操作',''
                    ),

                    'List_Opt' => array(
                         array(
                             'name' => '处理订单',
                             'url' => U('shop_status_update')
                         ),
                         array(
                             'name' => '详情',
                             'url' => U('detail')
                         ),

                    ),
                  'search'=>array(
                      array('title'=>'收货人电话','name'=>'li_s_phone'),
                      array('title'=>'收货人姓名','name'=>'li_s_name'),
                             array('type'=>'br'),
                      array('title'=>'状态','type'=>'select','name'=>'eq_status','many'=>array(
                              array('name'=>'发货','value'=>'1'),
                              array('name'=>'收货','value'=>'2'),
                              array('name'=>'通过','value'=>'3'),
                              array('name'=>'不通过','value'=>'4'),
                          )),
                    )
                ),
                M('js_dj')
                ->join('left join tp_js_sn on tp_js_sn.id=tp_js_dj.sid')
                //->join('left join tp_js_users on tp_js_users.uid=tp_js_dj.uid')
                ->order('tp_js_dj.d_time desc')->where($aWhere)->count(),
                M('js_dj')->join('left join tp_js_sn on tp_js_sn.id=tp_js_dj.sid')
                //->join('left join tp_js_users on tp_js_users.uid=tp_js_dj.uid')
                ->field('tp_js_dj.id,tp_js_sn.sn,tp_js_sn.secret,tp_js_dj.s_name,tp_js_dj.s_phone,tp_js_dj.logistics,tp_js_dj.logistics_num,tp_js_dj.s_address,tp_js_dj.d_time,tp_js_dj.status')
                ->order('tp_js_dj.d_time desc')
                ->where($aWhere),
               array($this,'logt')
            );
        $this->UDisplay('show1');

    }
    public function logt($data)
    {

        foreach($data as $k=>$v)
        {

            if($v['status']==1){
                $data[$k]['status']='发货';
            }
            if($v['status']==2){
                $data[$k]['status']='收货';
            }
            if($v['status']==3){
                $data[$k]['status']='通过';
            }
            if($v['status']==4){
                $data[$k]['status']='不通过';
            }
            if($v['type']==1){
                $data[$k]['type']='兑奖的';
            }
            if($v['type']==2){
                $data[$k]['type']='摇奖的';
            }
            $data[$k]['d_time']=date('Y-m-d',$v['d_time']);
            //$data[$k]['f_time']=date('Y-m-d H:i:s',$v['f_time']);

        }
        return $data;
    }

    //摇奖记录
    public function win()
    {
        $aWhere='';
        $aWhere['token'] =$this->_sToken;
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('win', array('token' => $this->_sToken)),

                      'tips' => array(
                        '摇奖记录列表'
                    ),
                    'Table_Header' => array(
                        '编号','操作员','参与摇奖人数','预设中奖数量','中奖人数','操作时间',''
                    ),
                    'List_Opt' => array(

                    /*array(
                        'name' => '删除',
                        'url' => U('del_log')
                    ),*/

                    ),
                    'Head_Opt' => array(
                     /*array(
                          'name' => '导出Excel表',
                          'url' => U('excellog',array('token'=>$this->_sToken))
                      ),*/
                    ),
                ),
                M('js_win')->field('id,who,y_win,win,num,add_time')->where($aWhere)->count(),
                M('js_win')
                ->field('id,who,y_win,win,num,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'logtime')

            );
        $this->UDisplay('show1');
    }
    public function logtime($data)
    {
        foreach($data as $k=>$v)
        {
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    public function excellog()
    {
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['content'] = '导出日记信息';
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        M('all_log')->add($da);

        $data = M('js_log')->field('id,name,info,secret,add_time')->where(array('token'=>$this->_sToken))->select();
        foreach($data as $k=>$v)
        {
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        Excel::arr2ExcelDownload($data,array('用户ID', '姓名','内容','卡密','时间'),'日志信息');
    }
    //中奖名单管理
    /*public function winners(){
        $aWhere='';
        $aWhere['token'] =$this->_sToken;
        $aWhere['status'] =2;
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            $aWhere['status'] =2;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('winners', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                     /*array(
                          'name' => '导出Excel表',
                          'url' => U('excel',array('token'=>$this->_sToken))
                      ),
                    ),
                    'tips' => array(
                        '中奖列表'
                    ),
                    'Table_Header' => array(
                       '编号' ,'中奖名单','电话号码', '时间','状态',''
                    ),
                    'List_Opt' => array(
                        /*array(
                            'name'=>'核审',
                            'url'=>U('Review_the_user')
                        )
                    ),
                  'search'=>array(
                      array('title'=>'用户名称','name'=>'li_name'),

                    )
                ),
                M('js_users')->where($aWhere)->count(),
                M('js_users')->field('id,name,phone,add_time,status')->order('add_time desc')->where($aWhere),
                array($this,'users_2')
            );
        $this->UDisplay('show1');
    }*/
    public function users_2($data){
        foreach($data as $k=>$v){
            if($v['status']==2)
            {
                $data[$k]['statusstatus']='通过';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    public function luckjoy(){
        $aWhere='';
        $aWhere['token'] =$this->_sToken;
        $aWhere['type'] =2;
        $aWhere['old'] =0;
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            $aWhere['type'] =2;
            $aWhere['old'] =0;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('luckjoy', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                     array(
                          'name' => '摇奖',
                          'url' => U('random',array('token'=>$this->_sToken))
                      ),
                    ),
                    'tips' => array(
                        '审核摇奖的用户列表',
                        '通过和不中奖才能进行摇奖'
                    ),
                    'Table_Header' => array(
                       '编号' ,'摇奖名单','电话号码','地址','物流公司','物流订单', '摇奖时间','时间','备注','状态','是否通过','发货状态','是否中奖',''
                    ),
                    'List_Opt' => array(
                        array(
                            'name'=>'核审',
                            'url'=>U('Review_the_users')
                        ),
                        array(
                             'name' => '处理订单',
                             'url' => U('shop_status_updates')
                         ),
                         array(
                             'name' => '详情',
                             'url' => U('detail1')
                         ),
                    ),

                    'search'=>array(
                      array('title'=>'收货人电话','name'=>'li_phone'),
                      array('title'=>'收货人姓名','name'=>'li_name'),
                             array('type'=>'br'),
                    array('title'=>'是否通过','type'=>'select','name'=>'eq_is_if','many'=>array(
                          array('name'=>'通过','value'=>'1'),
                          array('name'=>'不通过','value'=>'0')
                      )),
                      array('title'=>'是否中奖','type'=>'select','name'=>'eq_status','many'=>array(
                          array('name'=>'不中奖','value'=>'1'),
                          array('name'=>'中奖','value'=>'2')
                      )),
                    )

                ),
                M('js_users')->where($aWhere)->count(),
                M('js_users')->field('id,name,phone,address,logistics,logistics_num,update_time,add_time,other,type,is_if,goods,status')->order('add_time desc')->where($aWhere),
                array($this,'lj')
            );
        $this->UDisplay('show1');
    }
    public function lj($data)
    {
        foreach($data as $k=>$v)
        {
            $data[$k]['type']='摇奖';
            if($v['is_if']==1)
            {
                $data[$k]['is_if']='通过';
            }
            if($v['is_if']==0)
            {
                $data[$k]['is_if']='不通过';
            }
            if($v['goods']==0)
            {
                $data[$k]['goods']='未发货';
            }
            if($v['goods']==1)
            {
                $data[$k]['goods']='发货';
            }
            if($v['goods']==2)
            {
                $data[$k]['goods']='收货';
            }
            if($v['status']==1)
            {
                $data[$k]['status']='未中奖';
            }
            if($v['status']==2)
            {
                $data[$k]['status']='中奖';
            }

            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            $data[$k]['update_time']=date('Y-m-d H:i:s',$v['update_time']);
        }
        return $data;
    }
    public function random()
    {
            /*$_POST['type'] = 1;
            if(M('js_users')->where(array('token'=>$this->token,'type'=>2))->order('rand()')->limit(5)->save($_POST))
            {
                $c
            }else{
                $this->error2('摇奖失败');
            }*/
            //后台操作日记
            $da['token']=$this->token;
            $da['add_time']=time();
            $da['user'] = $_SESSION['name'];
            $da['ip'] = get_client_ip();
            $da['content'] = '进行摇奖';
            M('all_log')->add($da);

            //$ernie = M('js_users')->field('id,name,phone,address,add_time,type,old,is_if')->order('add_time desc')->where(array('token'=>$this->token,'old'=>0,'is_if'=>1,'status'=>1,'type'=>2))->select();
            //$ern = M('js_users')->field('id,phone')->order('add_time desc')->where(array('token'=>$this->_sToken,'type'=>2))->select();
            $con = M('js_users')->field('type,old,is_if')->where(array('type'=>2,'old'=>0,'is_if'=>1,'status'=>1))->select();
            $con = count($con);
            $this->assign('ernie',$ernie);
            $this->assign('con',$con);
            $this->display('./tpl/User/default/wxdj/ernie.html');
    }
    public function detail()
    {
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['content'] = '查看用户信息详情';
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        M('all_log')->add($da);

        $id = $_GET['id'];
        $deta  = M('js_dj')->join('left join tp_js_sn on tp_js_sn.id=tp_js_dj.sid')->where(array('tp_js_dj.id'=>$id))->order('tp_js_dj.d_time desc')->find();

        $this->assign('deta',$deta);
        $this->display('./tpl/User/default/wxdj/detail.html');
    }
    public function detail1()
    {
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['content'] = '查看用户信息详情';
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        M('all_log')->add($da);

        $id = $_GET['id'];
        $deta  = M('js_users')->where(array('id'=>$id))->order('add_time desc')->find();
        $this->assign('deta',$deta);
        $this->display('./tpl/User/default/wxdj/detail1.html');
    }
    //短信通知
    public function val()
    {
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['content'] = '设置短信';
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        M('all_log')->add($da);

        $this->Edit('js_set',array(
            array('title'=>"通知有效内容",'type'=>"input",'name'=>"val",'placeholder'=>'请输入中文内容'),
            array('title'=>"通知无效内容",'type'=>"input",'name'=>"noval",'placeholder'=>'请输入中文内容'),
            array('title'=>"销售人电话",'type'=>"input",'name'=>"salesphone",'placeholder'=>'销售人电话'),
            array('title'=>"销售人内容",'type'=>"input",'name'=>"salesman",'placeholder'=>'销售人内容'),
        ),U('index',array('token'=>$this->token)));
    }
    //摇奖按钮
    public function djyj()
    {

                    $s = $this->_post('s');
                    //记录摇奖的人数
                    $data['add_time'] = time();
                    $data['token']    = $this->token;
                    $data['y_win']    = mt_rand(1,15);
                    $data['num']      = $s;
                    $data['win']      = $s;
                    $data['who']      = '管理员';
                    $p = M('js_win')->add($data);

                    $s = $this->_post('s');
                    $_POST['status'] = 2;
                    $data['update_time'] = time();

                    $uid = M('js_users')
                        ->where(array(
                            'token'=>$this->token,
                            'type'=>2,
                            'old'=>0,
                            'is_if'=>1))
                        ->order('rand()')
                        ->limit($s)
                        ->save($_POST);
                    //随机找出有效的摇奖用户

                  /*  foreach ($uid as $key => $value){
                        $h = M('js_dj')
                            ->where(
                                array(
                                    'id'=>$value['dj_id']
                                ))
                            ->save($_POST);
                            $ids[] = $value['id'];
                        //表示逐条修改兑奖用户状态为已中奖
                    }*/

                    /*$b = M('js_users')
                        ->where(array('id' => array('in', $ids)))
                        ->save($data);*/
                        //修所有改兑奖用户信息为审查通过
                    if($uid)
                        {
                            echo json_encode(array('status'=>1));
                            //$this->success2('摇奖成功');
                    }else{
                            //$this->error2('摇奖失败');
                            echo json_encode(array('status'=>2));
                    }
    }
    //设置中奖
    /*public function set_win()
    {
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['content'] = '设置中奖';
        M('all_log')->add($da);
        $aWhere='';
        $aWhere['token'] =$this->_sToken;
        $aWhere['type'] =2;
        session('where_p',$aWhere);
        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            $aWhere['type'] =2;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('set_win', array('token' => $this->_sToken)),
                    'Head_Opt' => array(

                    ),
                    'tips' => array(
                        '设置中奖列表'
                    ),
                    'Table_Header' => array(
                       '编号' ,'卡密','sn码','是否有奖','状态',''
                    ),
                    'List_Opt' => array(
                        /*array(
                            'name'=>'核审',
                            'url'=>U('Review_the_user')
                        )
                    ),

                ),
                M('js_sn')->where($aWhere)->count(),
                M('js_sn')->field('id,secret,sn,is,add_time,type')->order('add_time desc')->where($aWhere),
                array($this,'lj')
            );
        $this->UDisplay('show1');
    }*/
    public function admin()
    {

        $aWhere['token'] =$this->_sToken;
        session('where_p',$aWhere);

        if(IS_POST){

            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                    'HeadHover' => U('admin', array('token' => $this->_sToken)),

                    'tips' => array(
                        '后台操作日记列表'
                    ),
                    'Table_Header' => array(
                       '编号' ,'IP','用户名','内容','时间',''
                    ),
                    'List_Opt' => array(
                        array(
                        'name' => '删除',
                        'url' => U('del_log')
                        ),
                    ),

                ),
                M('all_log')->where($aWhere)->count(),
                M('all_log')->field('id,ip,user,content,add_time')->order('add_time desc')->where($aWhere),
                array($this,'ad')
            );
        $this->UDisplay('show1');
    }
    public function ad($data)
    {
        foreach($data as $k=>$v)
        {
            $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    public function del_log(){
        $this->del('js_log');

    }

    public function shop_status_updates()
    {
        $info = M('js_users')
        ->find($_GET['id']);
        //后台操作日记
        $da['token']=$this->token;
        $da['add_time']=time();
        $da['user'] = $_SESSION['name'];
        $da['ip'] = get_client_ip();
        $da['content'] = '处理用户订单';
        M('all_log')->add($da);

        //p($info);die;
        if($info['is_if']==0)
        {
            $this->error2('该用户还没有核审');
        }
        if($info['status']==1)
        {
            $this->error2('用户还没中奖');
        }
        if($info['goods']==2)
        {
            $this->error2('已经处理了');
        }
        $this->Edit('js_users',
        array(
            array('title'=>'物流公司','type'=>'input','name'=>'logistics','placeholder'=>'请输入中文的字体','msg'=>'物流公司不能为空'),
            array('title'=>'快递单号','type'=>'input','name'=>'logistics_num','placeholder'=>'请输入中文的字体','msg'=>'快递单号不能为空'),
            array('title'=>'电脑系列号','type'=>'input','name'=>'number','placeholder'=>'请输入中文的字体','msg'=>'电脑系列号不能为空'),
            array('type'=>'hidden','name'=>'phone'),
            array('type'=>"hidden",'name'=>"f_time"),
            array('type'=>"hidden",'name'=>"s_time"),
            array('title'=>'审核','type'=>'radio','name'=>'goods','many'=>array(
                array('content'=>"发货",'value'=>"1"),
                array('content'=>"收货",'value'=>"2")
            )),

        ),

        U('luckjoy',array('token'=>$this->_sToken)),array($this,'cc2'));
    }
    public function cc2($data){
        if($data['goods']==1)
        {   $data['f_time'] = time();
            $str1 = array('YYYY','XXXX','00');
            $str2 = array($data['number'],$data['logistics'],$data['logistics_num']);
            $str = str_replace($str1,$str2,'【佳士科技】：您的平板电脑已发货。电脑系列号：YYYY，快递公司：XXXX，快递单号：00');
            senddj($data['phone'],$str);
        }
        if($data['goods']==2)
        {
            $data['s_time']=time();
        }
        return $data;
    }

}
?>
