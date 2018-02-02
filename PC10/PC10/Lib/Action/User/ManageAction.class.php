<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南(anyi测试文件)
 * $this->_iTid   表示的是相关公众号的ID与$this->token对应；
 * $this->_iUID   表示的是相关公众号的管理员的ID;
 **/
class ManageAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';
    //public $tpl_dir = './tpl/User/default/wxdj/';

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
        $this->pz	   = D('common_cs');
		session('zm_id',1);
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '用户信息',
                'url'  => U('index', array('token' => $this->_sToken))
            ),
			array(
                'name' => '发货信息',
                'url'  => U('expiry', array('token' => $this->_sToken))
            ),
            array(
                'name' => '摇奖用户信息',
                'url'  => U('luckjoy', array('token' => $this->_sToken))
            ),

        );
    }
    //用户信息
    public function index(){
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
                  /*'search'=>array(
                      array('title'=>'用户名称','name'=>'li_name'),
					  array('title'=>'状态','name'=>'eq_tp_js_users|status','type'=>'select','many'=>array(
                        array('value'=>'2','name'=>'通过'),
                        array('value'=>'3','name'=>'不通过'),
                        array('value'=>'1','name'=>'审核中'),
                    )),

                    )*/
                ),
                M('js_users')->join('left join tp_wxusers on tp_wxusers.id = tp_js_users.uid')->where($aWhere)->count(),
                M('js_users')->join('left join tp_wxusers on tp_wxusers.id = tp_js_users.uid')->field('tp_js_users.id,tp_js_users.name,tp_wxusers.nickname,tp_js_users.phone,tp_js_users.address,tp_js_users.other,tp_js_users.add_time,tp_js_users.type,tp_js_users.status')->order('tp_js_users.add_time desc')->where($aWhere),
				array($this,'users_1')
            );
      // $this->assign('duoxuan',1);
        //$this->UDisplay('show1');
	}
	//核审用户
	public function Review_the_user()
	{

		/*$getid = M('js_users')->find($_GET['id']);
        $id = M('js_dj')->where(array('token'=>$this->token,'s_phone'=>$getid['phone']))->find();

        $sid = M('js_sn')->where(array('token'=>$this->token,'id'=>$id['sid']))->find();
        $this->assign('sid',$sid);
        $this->assign('id',$id);
		$this->assign('info',$getid);
		$this->display('./tpl/User/default/wxdj/dj.html');*/
		
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
			//设置为已兑奖
			//$sid = M('js_dj')->where(array('token'=>$this->token,'s_phone'=>$phone,'s_name'=>$name,'d_time'=>$add_time))->getfield('sid');
			//$SN['is_dj'] = 1;
			//M('js_sn')->where(array('token'=>$this->token,'id'=>$sid))->save($SN);
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

			//改变为有效
			$datat['status'] = 3;
			M('js_dj')->where(array('token'=>$this->token,'s_phone'=>$phone,'s_name'=>$name,'d_time'=>$add_time))->save($datat);

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
//其它信息结束
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
                        '发货列表',

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
                  /*'search'=>array(
                      array('title'=>'收货人电话','name'=>'li_s_phone'),
                      array('title'=>'收货人姓名','name'=>'li_s_name'),
                             array('type'=>'br'),
                      array('title'=>'状态','type'=>'select','name'=>'eq_status','many'=>array(
                              array('name'=>'发货','value'=>'1'),
                              array('name'=>'收货','value'=>'2'),
                              array('name'=>'有效','value'=>'3'),
                              array('name'=>'无效','value'=>'4'),
                          )),
                    )*/
                ),
                M('js_dj')->join('left join tp_js_sn on tp_js_sn.id=tp_js_dj.sid')->order('tp_js_dj.d_time desc')->where($aWhere)->count(),
                M('js_dj')->join('left join tp_js_sn on tp_js_sn.id=tp_js_dj.sid')->
				field('tp_js_dj.id,tp_js_sn.sn,tp_js_sn.secret,tp_js_dj.s_name,tp_js_dj.s_phone,tp_js_dj.logistics,tp_js_dj.logistics_num,tp_js_dj.s_address,tp_js_dj.d_time,tp_js_dj.status,tp_js_dj.type')
				->order('tp_js_dj.d_time desc')
				->where($aWhere),
				array($this,'dj_status')
            );
      //  $this->UDisplay('show1');

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
		{	//echo 2;die;
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
			array('title'=>'物流公司','type'=>'input','name'=>'logistics','placeholder'=>'请输入中文的字体','msg'=>'物流公司不能为空','value'=>'logistics'),
			array('title'=>'快递单号','type'=>'input','name'=>'logistics_num','placeholder'=>'请输入中文的字体','msg'=>'快递单号不能为空',
				'value'=>'logistics_num'),
			array('title'=>'电脑系列号','type'=>'input','name'=>'number','placeholder'=>'请输入中文的字体','msg'=>'电脑系列号不能为空',
				'value'=>'number'),
			array('type'=>'hidden','name'=>'s_phone'),
			array('type'=>"hidden",'name'=>"f_time"),
			array('type'=>"hidden",'name'=>"s_time"),
			array('title'=>'审核','type'=>'radio','name'=>'status','value'=>'status','many'=>array(
                array('content'=>"发货",'value'=>"1"),
                array('content'=>"收货",'value'=>"2")
            )),

		),

		U('expiry',array('token'=>$this->_sToken)),array($this,'cc1'));
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
	public function cc1($data){

		if($data['status']==1)
		{	$data['f_time'] = time();
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



	//摇奖
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
                       '编号' ,'摇奖名单','电话号码','地址','物流公司','物流订单','摇奖时间', '时间','备注','状态','是否通过','发货状态','是否中奖',''
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
                      //array('title'=>'收货人电话','name'=>'li_phone'),
                     array('title'=>'是否中奖','type'=>'select','name'=>'eq_status','many'=>array(
                          array('name'=>'不中奖','value'=>'1'),
                          array('name'=>'中奖','value'=>'2')
                      ))
                    )

                ),
                M('js_users')->where($aWhere)->count(),
                M('js_users')->field('id,name,phone,address,logistics,logistics_num,update_time,add_time,other,type,is_if,goods,status')->order('add_time desc')->where($aWhere),
				array($this,'lj')
            );
        //$this->UDisplay('show1');
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
            array('title'=>'物流公司','type'=>'input','value'=>'logistics','name'=>'logistics','placeholder'=>'请输入中文的字体','msg'=>'物流公司不能为空'),
            array('title'=>'快递单号','type'=>'input','value'=>'logistics_num','name'=>'logistics_num','placeholder'=>'请输入中文的字体','msg'=>'快递单号不能为空'),
            array('title'=>'电脑系列号','type'=>'input','name'=>'number','value'=>'number','placeholder'=>'请输入中文的字体','msg'=>'电脑系列号不能为空'),
            array('type'=>'hidden','name'=>'phone'),
            array('type'=>"hidden",'name'=>"f_time"),
            array('type'=>"hidden",'name'=>"s_time"),
            array('title'=>'审核','type'=>'radio','name'=>'goods','value'=>'goods','many'=>array(
                array('content'=>"发货",'value'=>"1"),
                array('content'=>"收货",'value'=>"2")
            )),

        ),

        U('luckjoy',array('token'=>$this->_sToken)),array($this,'cc2'));
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
					//$data['status'] = 2;

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

}

