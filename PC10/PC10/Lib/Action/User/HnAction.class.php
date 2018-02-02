<?php
/**
 *  海南生活定制
 **/
class HnAction extends Table2Action {

    public $_sTplBaseDir = 'User/default/miye';

    public function _initialize()
    {
    	parent::_initialize();
        $this->pz	   = D('No_credit');
        $this->pz1   = D('Credit');
        $this->order  =M('No_credit_order');//订单表
        $this->tpl="tpl/User/default/helper/";
        $this->token=$this->_sToken;
		$this->tong = M('hn_yonjing_jl')->field('sum(yonjing) as a')->where(array('token'=>$this->_sToken,'type'=>0))->find();
		$this->tong1 = M('hn_yonjing_jl')->field('sum(yonjing) as b')->where(array('token'=>$this->_sToken,'type'=>1))->find();
		$this->tong2 = M('hn_yonjing_jl')->field('sum(yonjing) as c')->where(array('token'=>$this->_sToken,'type'=>2))->find();
    }

    protected function setHeader(){
    	return array(
            array(
                    'name' => '经纪人',
                    'url'  => U('index', array('token' => $this->_sToken))
            ),/*
            array(
                'name' => '申请经纪人',
                'url'  => U('zhuce', array('token' => $this->_sToken))
            ),*/
            array(
                'name' => '推荐客户列表',
                'url'  => U('tuijian', array('token' => $this->_sToken))
            ),
            array(
                'name' => '楼盘',
                'url'  => U('houses', array('token' => $this->_sToken))
            ),
            array(
                'name' => '设置',
                'url'  => U('set', array('token' => $this->_sToken))
            ),
			array(
                'name' => '反馈',
                'url'  => U('feedback', array('token' => $this->_sToken))
            ),
			array(
                'name' => '提现管理',
                'url'  => U('balance', array('token' => $this->_sToken))
            ),
			array(
                'name' => '联系我们',
                'url'  => U('contact', array('token' => $this->_sToken))
            ),
            array(
                'name' => '排行榜操作',
                'url'  => U('renwei', array('token' => $this->_sToken))
            ),
            array(
                'name' => '佣金商城',
                'url'  => U('shop', array('token' => $this->_sToken))
            ),
			array(
                'name' => '消费管理',
                'url'  => U('consume', array('token' => $this->_sToken))
            ),
			array(
                'name' => '佣金排行榜',
                'url'  => U('charts', array('token' => $this->_sToken))
            ),
			array(
                'name' => '照片墙评论',
                'url'  => U('photo', array('token' => $this->_sToken))
            ),
			array(
                'name' => '照片墙照片',
                'url'  => U('c_photo', array('token' => $this->_sToken))
            ),


            );
    }


    /**
     *  经纪人列表
     **/
	public function index(){
            $where['tp_hn_users.token'] =$this->_sToken;
        $where['tp_hn_users.renwei'] =1;

            //if(IS_POST){
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $where = $this->search($po);
            foreach($where as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $where[$b]=$v;
                        unset($where[$k]);
                    }
            }
                $where['token'] = $this->_sToken;
                $where['tp_hn_users.renwei'] =1;

                session('where_p',null);
                session('where_p',$where);
            //}else{
                //get 过来P分页时，带上条件查询数据
               // if(isset($_GET['p'])&&session('?where_p')){
                  //  $where=session('where_p');
               // }
            //}
            $this->table(
                array(
                    //'abc' => 123,
                  //  'id' => 'name', 
                    'HeadHover' => U('index', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                     array(
                          'name' => '导出Excel表',
                          'url' => U('excel',array('token'=>$this->_sToken))
                      ),


					),
                    'tips' => array(
                        '经纪人列表',' 推荐经纪人总佣金 '.$this->tong['a'].', 看房总佣金 '.$this->tong1['b'].', 签约总佣金 '.$this->tong2['c']
                    ),
                    'Table_Header' => array(
                        'ID', '姓名','手机号码','城市','微信名称','合计佣金','客户','经纪人','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '佣金统计',
                            'url' => U('yonjing')
                        ),
                        array(
                            'name' => '经纪人',
                            'url' => U('jjr')
                        ),
                        array(
                            'name' => '客户',
                            'url' => U('kefu')
                        ),
						array(
                            'name' => '详情',
                            'url' => U('details')
                        ),
						array(
                            'name' => '消费记录',
                            'url' => U('expense_calendar')
                        ),
                        array(
                            'name' => '删除经纪人',
                            'url' => U('del_ren')
                        ),

                    ),
                  'search'=>array(
							 array('title'=>'姓名','name'=>'li_name'),
							 array('title'=>'手机号码','name'=>'li_phone'),
                    )
                ),
                M('Hn_users')->join("left join tp_wxusers on tp_wxusers.id=tp_hn_users.uid")->where($where)->count(),
                M('Hn_users')->join("left join tp_wxusers on tp_wxusers.id=tp_hn_users.uid")->field('tp_hn_users.id,tp_hn_users.name,tp_hn_users.phone,tp_hn_users.fid1,tp_wxusers.nickname,tp_hn_users.yonjing')->where($where),
				array($this,'abc')
            );
        $this->UDisplay('show1');
	}
    public function del_ren(){
        $id = $_GET['id'];
        $info = M('hn_yonjing_jl')->where(array('uid'=>$id))->delete();
        $info1 = M('hn_bank')->where(array('uid'=>$id))->delete();
        $info2 = M('hn_tuijian')->where(array('uid'=>$id))->delete();
        $info3 = M('hn_xiaofeijl')->where(array('uid'=>$id))->delete();
        $info4 = M('hn_feedback')->where(array('uid'=>$id))->delete();
        if($info)
        {
            $this->del('hn_users');
        }
    }
	//导出excel表
	public function excel()
	{
		$data = M('Hn_users')->join("join tp_wxusers on tp_wxusers.id=tp_hn_users.uid")->field('tp_hn_users.id,tp_hn_users.name,tp_hn_users.phone,tp_hn_users.yonjing,tp_hn_users.fid1,tp_hn_users.fid2,tp_wxusers.nickname,tp_hn_users.from_phone')->where(array('token'=>$this->_sToken))->select();
	    foreach($data as $k=>$v){
			$aKufu = M('Hn_tuijian')->where(array('token'=>$this->_sToken,'uid'=>$v['id']))->select();
			$iKufu = count($aKufu);
			$data[$k]['kcount'] = $iKufu;
			$c_phone = M('hn_users')->where(array('token'=>$this->_sToken,'from_phone'=>$v['phone']))->select();
			$c_phone = count($c_phone);
			$data[$k]['c_phone'] = $c_phone;
            //推荐人姓名
            $name = M('Hn_users')
            ->where(array('phone'=>$v['from_phone'],
                'token'=>$this->_sToken))
            ->getField('name');
            $data[$k]['username'] = $name;
		}
		Excel::arr2ExcelDownload($data,array('编号', '姓名','手机号码','佣金','城市','单位','微信名称','推荐人电话','客户','经纪人','推荐人姓名'),'会员信息');
	}
	//消费记录
	public function expense_calendar()
	{
		$aWhere['tp_hn_xiaofeijl.token'] =$this->_sToken;
		$aWhere['tp_hn_xiaofeijl.uid'] =$_GET['id'];
		session('where_p',$aWhere);
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                        $_POST[$k]=$v;
                    }
            }
            $aWhere = $this->search($_POST);
			$aWhere['tp_hn_xiaofeijl.uid'] =$_GET['id'];
            $aWhere['tp_hn_xiaofeijl.token'] = $this->_sToken;
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
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '消费记录'
                ),
                'Table_Header' => array(
                    '编号','商品名称', '购买人姓名','购买人手机号码', '消费金额','时间','状态',''
                ),
                'List_Opt' => array(


                ),
              /*  'search'=>array(
                    array('title'=>'手机号码','name'=>'li_phone'),
                )*/
            ),

            M('hn_xiaofeijl')->join("join tp_hn_users on tp_hn_xiaofeijl.uid=tp_hn_users.id")->where($aWhere)->order('tp_hn_xiaofeijl.addtime desc')->count(),
            M('hn_xiaofeijl')
                ->join("join tp_hn_users on tp_hn_xiaofeijl.uid=tp_hn_users.id")
                ->field('tp_hn_xiaofeijl.id,tp_hn_xiaofeijl.s_name,tp_hn_users.name,
                tp_hn_users.phone,tp_hn_xiaofeijl.yonjing,tp_hn_xiaofeijl.addtime,tp_hn_xiaofeijl.status')
                ->order('tp_hn_xiaofeijl.addtime desc')
                ->where($aWhere),
            array($this,'shop_status')

        );

        $this->UDisplay('show1');
	}
	//会员信息详情
	public function details(){
        $info=M('Hn_users')
            ->join("join tp_wxusers on tp_wxusers.id=tp_hn_users.uid")
            ->field('tp_hn_users.from_phone,tp_hn_users.id,tp_hn_users.name,tp_hn_users.phone,tp_hn_users.yonjing,tp_hn_users.fid1,tp_hn_users.fid2,tp_wxusers.nickname,tp_hn_users.brand,tp_hn_users.alipay,tp_hn_users.bank,tp_hn_users.status,tp_hn_users.add_time,tp_hn_users.img,tp_hn_users.img1,tp_hn_users.img2')
            ->where(array('token'=>$this->_sToken,'tp_hn_users.id'=>$_GET['id']))->find();
		$id = M('hn_users')->field('id')->where(array('token'=>$this->_sToken,'id'=>$_GET['id']))->getField('id');
		$xiao = M('hn_xiaofeijl')->field('sum(yonjing) as c ')->where(array('token'=>$this->_sToken,'uid'=>$id))->find();
		$zong = M('hn_users')->where(array('token'=>$this->_sToken,'id'=>$_GET['id']))->getField('yonjing');
        $this->assign('zong',$zong);
        $this->assign('info',$info);
        $this->assign('xiao',$xiao);
        $this->display('./tpl/User/default/hn/details.html');
    }
	public function abc($data){
        foreach($data as $k=>$v){
			$aKufu = M('Hn_tuijian')->where(array('token'=>$this->_sToken,'uid'=>$v['id']))->select();
			$iKufu = count($aKufu);
			$data[$k]['kcount'] = $iKufu;
			$c_phone = M('hn_users')->where(array('token'=>$this->_sToken,'from_phone'=>$v['phone']))->select();
			$c_phone = count($c_phone);
			$data[$k]['c_phone'] = $c_phone;
			//消费金额
			//$id = M('hn_users')->field('id')->where(array('token'=>$this->_sToken))->getField('id');
			//$xiao = M('hn_xiaofeijl')->field('yonjing')->where(array('token'=>$this->_sToken,'uid'=>$id))->select();
			//p($data);
		}

        return $data;
    }

    public function abc1($data){
        foreach($data as $k=>$v){

           if($v['status']==0){
               $data[$k]['status']='新申请';
           }
            if($v['status']==1){
                $data[$k]['status']='审核通过';
            }
            if($v['status']==-1){
                $data[$k]['status']='审核未通过';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    //新的申请
	/*
    public function zhuce(){
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
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
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('zhuce', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    /*  array(
                          'name' => '添加非信贷产品',
                          'url' => U('Loan/add_pinzhong',array('token'=>$this->token))
                      ),
                    *//*

                ),
                'tips' => array(
                    '经纪人列表'
                ),
                'Table_Header' => array(
                    'ID', '姓名','手机号码', '身份','推荐人号码','状态','申请时间','操作'
                ),
                'List_Opt' => array(
                     array(
                         'name' => '审核',
                         'url' => U('hs')
                     ),

                ),
                'search'=>array(
                    array('title'=>'姓名','name'=>'li_name'),
                    array('title'=>'手机号码','name'=>'li_phone'),
                    array('title'=>'订单状态','name'=>'eq_status','type'=>'select','many'=>array(
                        array('value'=>'0','name'=>'新申请'),
                        array('value'=>'1','name'=>'通过'),
                        array('value'=>'-1','name'=>'未通过'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                )
            ),

            M('Hn_users')->where($aWhere)->count(),
            M('Hn_users')->field('id,name,phone,fid,from_phone,status,add_time')->where($aWhere),
            array($this,'abc1')

        );
        $this->UDisplay('show1');
    }*/
    //核审经纪人
    public function hs(){
        $info=M('hn_users')->field('status')->find($_GET['id']);
        if($info['status']!=0){
            $this->error2('已审核过了');
        }
        $this->Edit('hn_users',array(
            array('title'=>'审核','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"通过",'value'=>"1"),
                array('content'=>"不通过",'value'=>"-1")
            )),
            array('type'=>'hidden','name'=>'from_phone')
        ),U('zhuce',array('token'=>$this->token)),array($this,'hs1'));
    }
    public function hs1($data){
       // p($data);die;

        if(($data['status']==1)AND($data['from_phone']!='')){//通过关佣金

           if($id= M('Hn_users')->where(array('token'=>$this->_sToken,'phone'=>$data['from_phone'],'status'=>1))->getField('id')){//假

               $a=M('Hn_set')->where(array('token'=>$this->_sToken))->getField('yonjing1');
               if(M('Hn_users')->where(array('id'=>$id))->setInc('yonjing',$a)){
                   $info['token']=$this->_sToken;
                   $info['uid']=$id;
                   $info['add_time']=time();
                   $info['type_id']=$data['id'];
                   $info['content']='推荐'.$data['from_phone'].'成为经纪人所得';
                   $info['yonjing']=$a;
                   M('hn_yonjing_jl')->add($info);
               }
           }


        }
        return $data;
    }
    /**
     *  推荐客户列表
     **/
    public function tuijian(){
        $aWhere['tp_hn_tuijian.token'] =$this->_sToken;
        if(IS_POST){
          //  p($_POST);
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                $b=str_replace('|','.',$k);
                $aWhere[$b]=$v;
                unset($aWhere[$k]);
            }
            $aWhere['tp_hn_tuijian.token'] = $this->_sToken;
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
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('tuijian', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    /*  array(
                          'name' => '添加非信贷产品',
                          'url' => U('Loan/add_pinzhong',array('token'=>$this->token))
                      ),
                    */

                ),
                'tips' => array(
                    '推荐客户列表'
                ),
                'Table_Header' => array(
                    'ID','经纪人姓名','经纪人号码','客户姓名','客户号码','状态','推荐时间','备注','推荐楼盘','操作'
                ),
                'List_Opt' => array(
                    /*array(
                        'name' => '备注',
                        'url' => U('other')
                    ),*/
                     array(
                         'name' => '处理',
                         'url' => U('chuli')
                     ),
                     array(
                         'name' => '新增备注',
                         'url' => U('bei')
                     ),

                ),
                'search'=>array(
                    array('title'=>'客户姓名','name'=>'li_tp_hn_tuijian|name'),
                    array('title'=>'客户号码','name'=>'li_tp_hn_tuijian|phone'),
                    array('title'=>'订单状态','name'=>'eq_tp_hn_tuijian|status','type'=>'select','many'=>array(
                        array('value'=>'0','name'=>'新推荐'),
                        array('value'=>'1','name'=>'已处理'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_tp_hn_tuijian|add_time','type'=>'between')
                )
            ),

            M('hn_tuijian')->where($aWhere)->order('tp_hn_tuijian.add_time desc')->count(),
            M('hn_tuijian')
                   ->join(array('tp_hn_users as a on tp_hn_tuijian.uid=a.id','tp_hn_houses as b on tp_hn_tuijian.hid=b.id'))
                ->field('tp_hn_tuijian.id,a.name as n,a.phone as k,tp_hn_tuijian.name,tp_hn_tuijian.phone,tp_hn_tuijian.status,tp_hn_tuijian.add_time,tp_hn_tuijian.other,tp_hn_tuijian.hid')
                ->order('tp_hn_tuijian.add_time desc')->where($aWhere),
            array($this,'tuijian1')

        );
        $this->UDisplay('show1');
    }

    public function tuijian1($data){

        foreach($data as $k=>$v){
             $hid = explode(',',$v['hid']);
             $data[$k]['hid']='';
             for ($i=0; $i <count($hid) ; $i++) {
                 $data[$k]['hid'].=M('hn_houses')->where(array('id' =>$hid[$i]))->getField('title').' '
                 ;

             }


            if($v['status']==0){
                $data[$k]['status']="新推荐";
            }
            if($v['status']==1){
                $data[$k]['status']="待到访";
            }
            if($v['status']==2){
                $data[$k]['status']="已到访";
            }
            if($v['status']==3){
                $data[$k]['status']="待签约";
            }
			if($v['status']==4){
                $data[$k]['status']="已签约";
            }
			if($v['status']==5){
                $data[$k]['status']="已结佣";
            }
			if($v['status']==6){
                $data[$k]['status']="无意向";
            }
            if($v['status']==-1){
                $data[$k]['status']="客户未到访";
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);

        }

        return $data;
    }
    public function bei()
    {
        $info=M('hn_tuijian')->field('status')->find($_GET['id']);
        $this->assign('tishi','新增备注');
            $this->Edit('hn_tuijian',array(
                array('title'=>'新增备注','type'=>'input','name'=>'bei'),

            ),U('tuijian',array('token'=>$this->_sTtoken)));
    }
    //处理推荐客户状态
    public function chuli(){
        $info=M('hn_tuijian')->field('status')->find($_GET['id']);
        if($info['status']==0){
            $this->assign('tishi','这里管理员根据线下客户具体到访签约情况来改变此订单状态');
            $this->Edit('hn_tuijian',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"待到访",'value'=>"1"),
                    //array('content'=>"未到访",'value'=>"-1"),
                    //array('content'=>"已过期",'value'=>"5"),
                    array('content'=>"无意向",'value'=>"6")
                )),

            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
        }
		if($info['status']==1){
            $this->assign('tishi','这里管理员根据线下客户具体到访签约情况来改变此订单状态');
            $this->Edit('hn_tuijian',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"已到访",'value'=>"2"),
                    //array('content'=>"未到访",'value'=>"-1"),
                    //array('content'=>"已过期",'value'=>"5"),
                    array('content'=>"无意向",'value'=>"6")
                )),

            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
        }
		if($info['status']==2){
            $this->assign('tishi','这里管理员根据线下客户具体到访签约情况来改变此订单状态');
            $this->Edit('hn_tuijian',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"待签约",'value'=>"3"),
                    //array('content'=>"未到访",'value'=>"-1"),
                    //array('content'=>"已过期",'value'=>"5"),
                    array('content'=>"无意向",'value'=>"6")
                )),

            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
        }
        if($info['status']==3){
            $this->assign('tishi','这里管理员根据线下客户具体到访签约情况来改变此订单状态');
            $this->Edit('hn_tuijian',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"已签约",'value'=>"4"),
                    array('content'=>"无意向",'value'=>"6")
                )),

            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
        }

        if($info['status']==-1||$info['status']==5||$info['status']==6){
            $this->error2('此订单已处理完成');
        }
		if($info['status']==4){
            $this->assign('tishi','经纪人所得佣金');
            $this->Edit('hn_tuijian',array(
                array('title'=>'确认签约佣金','type'=>'input','name'=>'money'),
				array('title'=>'确认签约佣金','type'=>'hidden_true','name'=>'status','value'=>5),
            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
		}
    }
   //客户到访送佣金
    public function chuli1($data){
		if($data['money']){
			$info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',$data['money']);
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>$data['money'],
                'add_time'=>time(),
                'content'=>'已结佣',
                'type'=>2,
                'name'=>$info['name'],
                'type_id'=>$info['id']
            ));
			//$data['status']=4;
		}
        if($data['status']==2){
            $info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
            //$yonjing=M('hn_houses')->where(array('id'=>$info['hid']))->getField('yonjing1');
			//p($yonjing);die;
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',100);

            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>100,
                'add_time'=>time(),
                'content'=>'客户到访',
                'type'=>1,
                'name'=>$info['name'],
                'type_id'=>$info['id'],
                'update_time'=>time()
            ));
        }/*
		if($data['status']==1){
            $info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
            //$yonjing=M('hn_houses')->where(array('id'=>$info['hid']))->getField('yonjing1');
			//p($yonjing);die;
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',$yonjing);
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>$yonjing,
                'add_time'=>time(),
                'content'=>'客户到访',
                'type'=>1,
                'name'=>$info['name'],
                'type_id'=>$info['id'],
                'update_time'=>time()
            ));
        }
		if($data['status']==3){
            $info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
            $yonjing=M('hn_houses')->where(array('id'=>$info['hid']))->getField('yonjing2');
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',$yonjing);
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>$yonjing,
                'add_time'=>time(),
                'content'=>'签约成功',
                'type'=>2,
                'name'=>$info['name'],
                'type_id'=>$info['id']
            ));
        }*/
        return $data;
    }
	//签约佣金
	/*public function yon(){
            $info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
			$yon = $this->_post('yon');
            $yonjing=M('hn_houses')->where(array('id'=>$info['hid']))->getField('yonjing2');
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',$yonjing*$yon);
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>$yonjing*$yon,
                'add_time'=>time(),
                'content'=>'签约成功',
                'type'=>2,
                'name'=>$info['name'],
                'type_id'=>$info['id']
            ));

	}*/

    //备注
    public function other(){
        $info=M('hn_tuijian')->field('other,money')->find($_GET['id']);
        $this->assign('info',$info);
        $this->display('./tpl/User/default/hn/other.html');
    }



    /**
     *  楼盘列表
     **/
    public function houses(){
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
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
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('houses', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                          'name' => '添加楼盘',
                          'url' => U('add_houses',array('token'=>$this->_sTtoken))
                      ),


                ),
                'tips' => array(
                    '楼盘列表'
                ),
                'Table_Header' => array(
                    'ID', '楼盘名称','楼盘图片','看楼佣金', '签约佣金','排序','操作'
                ),
                'List_Opt' => array(
                     array(
                         'name' => '修改',
                         'url' => U('edit_houses')
                     ),
                    array(
                        'name' => '删除',
                        'url' => U('del_houses')
                    ),

                ),
                'search'=>array(
                    array('title'=>'楼盘名称','name'=>'li_title'),
                )
            ),

            M('hn_houses')->where($aWhere)->order('sort desc')->count(),
            M('hn_houses')->field('id,title,logopic,yonjing1,yonjing2,sort')->order('sort desc')->where($aWhere),
            array($this,'houses1')

        );
        $this->UDisplay('show1');
    }
    public function houses1($data){
        foreach($data as $k=>$v){
           $data[$k]['logopic']="<img src='".$v['logopic']."' width=70px />";
        }
        return $data;
    }
    /**
     * 添加楼盘
     */
    public function add_houses(){

        $this->add('hn_houses',array(
            array('title'=>"楼盘名称",'type'=>"input",'name'=>"title",'msg'=>'楼盘名称不能为空'),
            array('title'=>"一句话点评",'type'=>"input",'name'=>"jianjie",'msg'=>'简介不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"项目主图",'name'=>"logopic"),
                array('title'=>"详情页背景",'name'=>"back_pic")

            )),
            array('title'=>"简介",'type'=>"textarea_1",'name'=>"content"),
            array('title'=>"区位",'type'=>"textarea",'name'=>"info"),
            array('title'=>"相册",'type'=>"textarea_2",'name'=>"info2"),
			array('title'=>"微楼书",'type'=>"input",'name'=>"info3",'placeholder'=>'请加上http://'),
            array('title'=>"经纬度",'type'=>"map1",'name'=>"lng"),
			array('title'=>"备注",'type'=>"input",'name'=>"other"),
            array('title'=>"楼盘看房佣金(元)",'type'=>"input",'name'=>"yonjing1","placeholder"=>"100"),
            array('title'=>"楼盘签约佣金(元)",'type'=>"input",'name'=>"yonjing2",'width'=>'500px','placeholder'=>'比如输入0.01，经纪人所得佣金就是，签约金*0.01'),
            array('title'=>"排序",'type'=>"input",'name'=>"sort",'placeholder'=>'数值越大排在越前'),

        ),U('houses',array('token'=>$this->_sTtoken)));
    }

    public function edit_houses(){
        $this->Edit('hn_houses',array(
            array('title'=>"楼盘名称",'type'=>"input",'name'=>"title",'msg'=>'楼盘名称不能为空'),
			array('title'=>"一句话点评",'type'=>"input",'name'=>"jianjie",'msg'=>'简介不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"项目主图",'name'=>"logopic"),
                array('title'=>"详情页背景",'name'=>"back_pic")
            )),
            array('title'=>"简介",'type'=>"textarea_1",'name'=>"content"),
            array('title'=>"区位",'type'=>"textarea",'name'=>"info"),
            array('title'=>"相册",'type'=>"textarea_2",'name'=>"info2"),
            array('title'=>"微楼书",'type'=>"input",'name'=>"info3",'placeholder'=>'请加上http://'),
            array('title'=>"经纬度",'type'=>"map1",'lng'=>"lng",'lat'=>'lat'),
			array('title'=>"备注",'type'=>"input",'name'=>"other"),
            array('title'=>"楼盘看房佣金(元)",'type'=>"input",'name'=>"yonjing1",'placeholder'=>'100'),
            array('title'=>"楼盘签约佣金(元)",'type'=>"input",'name'=>"yonjing2",'width'=>'500px','placeholder'=>'比如输入0.01，经纪人所得佣金就是，签约金*0.01'),
            array('title'=>"排序",'type'=>"input",'name'=>"sort",'placeholder'=>'数值越大排在越前'),

        ),U('houses',array('token'=>$this->_sTtoken)));
    }
    public function del_houses(){
        $this->del('hn_houses');
    }
	//联系我们
	public function contact(){
        $this->Edit('hn_set',array(
            array('title'=>"联系我们",'type'=>"textarea",'name'=>"content"),
            array('title'=>"注册协议",'type'=>"textarea_1",'name'=>"xie"),
        ),U('index',array('token'=>$this->_sTtoken)));
    }
    //佣金记录
    public function yonjing(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['uid']=$_GET['id'];
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            $aWhere['uid']=$_GET['id'];
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
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                      array(
                          'name' => '返回经纪人列表',
                          'url' => U('index',array('token'=>$this->_sTtoken))
                      ),
					  array(
                          'name' => '导出佣金统计表',
                          'url' => U('excelyon',array('token'=>$this->_sToken))
                      ),

                ),
                'tips' => array(
                    '【'.M('hn_users')->where(array('id'=>$_GET['id']))->getField('name').'】佣金记录列表',


                ),
                'Table_Header' => array(
                    'ID','佣金','来源','名字', '时间',''
                ),
				'List_Opt' => array(
                    /* array(
                         'name' => '审核',
                         'url' => U('yon_shen')
                     ),*/

                ),
                'search'=>array(

                    array('title'=>'客户姓名','name'=>'li_name'),

                  //  array('type'=>'br'),
                    array('title'=>'时间','name'=>'be_add_time','type'=>'between')
                )
            ),

            M('hn_yonjing_jl')->where($aWhere)->order('add_time desc')->count(),
            M('hn_yonjing_jl')->field('id,yonjing,content,name,add_time,uid')->order('add_time desc')->where($aWhere),
            array($this,'yonjing3')

        );
        $this->UDisplay('show1');
    }
    public function yonjing3($data){

        foreach($data as $k=>$v){

            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
		}
		return $data;
    }
	//佣金统计
	public function excelyon()
	{
		$data = M('Hn_users')->join("join tp_hn_yonjing_jl on tp_hn_yonjing_jl.uid=tp_hn_users.id")->field('tp_hn_yonjing_jl.id,tp_hn_yonjing_jl.yonjing,tp_hn_yonjing_jl.content,tp_hn_yonjing_jl.name,tp_hn_users.phone,tp_hn_yonjing_jl.add_time')->where(array('tp_hn_yonjing_jl.token'=>$this->_sToken))->select();
	    foreach($data as $k=>$v){
			/*$aKufu = M('Hn_tuijian')->where(array('token'=>$this->_sToken,'uid'=>$v['id']))->select();
			$iKufu = count($aKufu);
			$data[$k]['kcount'] = $iKufu;
			$c_phone = M('hn_users')->where(array('token'=>$this->_sToken,'from_phone'=>$v['phone']))->select();
			$c_phone = count($c_phone);
			$data[$k]['c_phone'] = $c_phone;*/
			$data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
		}
		Excel::arr2ExcelDownload($data,array('编号','佣金','来源','姓名','手机号码','时间'),'佣金统计信息');
	}

    public function yon_shen(){
        $info=M('hn_yonjing_jl')->field('status')->find($_GET['id']);
        if($info['status']!=0){
            $this->error2('已审核过了');
        }
        $this->Edit('hn_yonjing_jl',array(
            array('title'=>'审核','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"结算",'value'=>"1"),
                array('content'=>"不结算",'value'=>"0")
            )),
        ),U('index',array('token'=>$this->_sTtoken)));
    }
    public function yonjing1($data){
        foreach($data as $k=>$v){
            if($v['status']==1){
                 $data[$k]['status']='新申请';
            }
            if($v['status']==2){
                $data[$k]['status']='审核通过';
            }
			if($v['status']==3)
			{
				$data[$k]['status']='审核未通过';
			}
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            $data[$k]['update_time']=date('Y-m-d H:i:s',$v['update_time']);
        }
        return $data;
    }

	public function yonjing2($data){
        foreach($data as $k=>$v){
            if($v['status']==0){
                 $data[$k]['status']='未结算';
            }
            if($v['status']==1){
                $data[$k]['status']='已结算';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    //设置
    public function set(){
        $this->Edit('hn_set',array(
            array('title'=>"推荐经纪人所得佣金",'type'=>"input",'name'=>"yonjing1"),
            array('title'=>"排行榜标题语",'type'=>"textarea1",'name'=>"content1"),
            array('type'=>'img','many'=>array(
                array('title'=>"主页生活家主图",'name'=>"info4"),
                array('title'=>"看房生活家主图",'name'=>"lookinfo"),
				array('title'=>"照片墙主图",'name'=>"p_img")
            )),
            array('title'=>"经纪人生活家详情",'type'=>"textarea_2",'name'=>"info5"),
            array('title'=>"客户生活家详情",'type'=>"textarea_3",'name'=>"jin"),
            array('title'=>"活动规则",'type'=>"textarea",'name'=>"info1"),
            array('title'=>"分享经纪人标题",'type'=>"input",'name'=>"title"),
            array('title'=>"分享经纪人内容",'type'=>"input",'name'=>"share"),
            array('title'=>"照片墙详情",'type'=>"textarea_4",'name'=>"photo"),
            array('title'=>"秀堂",'type'=>"input",'name'=>"titlep"),
            array('title'=>"秀堂内容",'type'=>"input",'name'=>"sharep")
        ),U('houses',array('token'=>$this->_sToken)));
    }
    //我的经纪人记录
    public function jjr(){
        $aWhere['token'] =$this->_sToken;
		$aWhere['from_phone']=M('hn_users')->where(array('id'=>$_GET['id']))->getField('phone');

        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            //$aWhere['uid']=$_GET['id'];
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
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '返回经纪人列表',
                        'url' => U('index',array('token'=>$this->_sTtoken))
                    ),


                ),
                'tips' => array(
                    '【'.M('hn_users')->where(array('id'=>$_GET['id']))->getField('name').'】推荐经纪人记录列表'
                ),
                'Table_Header' => array(
                    'ID','姓名','电话号码','状态', '时间',''
                ),
                'List_Opt' => array(

                ),
                'search'=>array(

                          array('title'=>'手机号码','name'=>'li_phone'),

                    )
            ),

            M('hn_users')->where($aWhere)->count(),

            M('hn_users')->field('id,name,phone,status,add_time')->where($aWhere),
            //array($this,'yonjing2'),
            array($this,'abc1')

        );
        $this->UDisplay('show1');
    }
	//推荐客户记录
    public function kefu(){
        $aWhere['tp_hn_tuijian.token'] =$this->_sToken;
		$aWhere['tp_hn_tuijian.uid']=$_GET['id'];
        if(IS_POST){
          //  p($_POST);
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                $b=str_replace('|','.',$k);
                $aWhere[$b]=$v;
                unset($aWhere[$k]);
            }
            $aWhere['tp_hn_tuijian.token'] = $this->_sToken;
			$aWhere['tp_hn_tuijian.uid']=$_GET['id'];
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
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'Head_Opt' => array(
                     array(
                          'name' => '返回经纪人列表',
                          'url' => U('index',array('token'=>$this->_sTtoken))
                      ),
                ),
                'tips' => array(
                    '【'.M('hn_users')->where(array('id'=>$_GET['id']))->getField('name').'】客户记录列表'
                ),
                'Table_Header' => array(
                    'ID','客户姓名','客户号码','状态','推荐时间','更新时间','推荐楼盘',''
                ),

                'search'=>array(
                    array('title'=>'客户姓名','name'=>'li_tp_hn_tuijian|name'),
                    array('title'=>'客户号码','name'=>'li_tp_hn_tuijian|phone'),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_tp_hn_tuijian|add_time','type'=>'between')
                )
            ),

            M('hn_tuijian')->where($aWhere)->order('tp_hn_tuijian.add_time desc')->count(),
            M('hn_tuijian')
                   ->join(array('tp_hn_users as a on tp_hn_tuijian.uid=a.id','tp_hn_houses as b on tp_hn_tuijian.hid=b.id'))
                ->field('tp_hn_tuijian.id,tp_hn_tuijian.name,tp_hn_tuijian.phone,tp_hn_tuijian.status,tp_hn_tuijian.add_time,tp_hn_tuijian.update_time,tp_hn_tuijian.hid')
                ->order('tp_hn_tuijian.add_time desc')->where($aWhere),
            array($this,'tuijian2')

        );
        $this->UDisplay('show1');
    }
	    public function tuijian2($data){

        foreach($data as $k=>$v){
            $hid = explode(',',$v['hid']);
             $data[$k]['hid']='';
             for ($i=0; $i <count($hid) ; $i++) {
                 $data[$k]['hid'].=M('hn_houses')->where(array('id' =>$hid[$i]))->getField('title').' '
                 ;

             }
            if($v['status']==0){
                $data[$k]['status']="新推荐";
            }
            if($v['status']==1){
                $data[$k]['status']="客户到访";
            }
            if($v['status']==2){
                $data[$k]['status']="客户未签约";
            }
            if($v['status']==3){
                $data[$k]['status']="客户成功签约";
            }
			if($v['status']==4){
                $data[$k]['status']="已结佣";
            }
			if($v['status']==5){
                $data[$k]['status']="已过期";
            }
			if($v['status']==6){
                $data[$k]['status']="无意向";
            }
            if($v['status']==-1){
                $data[$k]['status']="客户未到访";
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
			if($v['status']!=0){
				$data[$k]['update_time']=date('Y-m-d H:i:s',$v['update_time']);
			}
			}
        return $data;
    }
	//反馈信息
	public function feedback(){
		$aWhere['tp_hn_feedback.token'] = $this->_sToken;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['tp_hn_feedback.token'] = $this->_sToken;
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
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('feedback', array('token' => $this->_sToken)),

                'tips' => array(
                    '反馈信息列表'
                ),
                'Table_Header' => array(
                    'ID', '用户名','电话号码','反馈信息', '时间',''
                ),


            ),

            M('hn_feedback')->where($aWhere)->order('tp_hn_feedback.add_time desc')->count(),
            M('hn_feedback')
                ->join(array('tp_hn_users as a on tp_hn_feedback.uid=a.id'))
                ->field('tp_hn_feedback.id,a.name as j,a.phone as k,tp_hn_feedback.content,tp_hn_feedback.add_time')
                ->order('tp_hn_feedback.add_time desc')->where($aWhere),
			array($this,'feed_time')
        );
        $this->UDisplay('show1');
	}
	public function feed_time($data){
        foreach($data as $k=>$v){
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
	//提现管理
	public function balance()
	{
		$aWhere['tp_hn_bank.token'] = $this->_sToken;

        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['tp_hn_bank.token'] = $this->_sToken;
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
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('balance', array('token' => $this->_sToken)),

                'tips' => array(
                    '提现信息列表'
                ),
                'Table_Header' => array(
                    '序号','姓名','电话号码','提现金额','银行卡号','状态', '申请时间','审核时间','操作'
                ),
				'List_Opt' => array(
                        array(
                            'name' => '审核',
                            'url' => U('audio')
                        ),
                ),

            ),

            M('hn_bank')->where($aWhere)->order('tp_hn_bank.addtime desc')->count(),
            M('hn_bank')
                ->join(array('tp_hn_users as a on tp_hn_bank.uid=a.id'))
                ->field('tp_hn_bank.id,a.name as j,a.phone as k,tp_hn_bank.money,tp_hn_bank.brand,tp_hn_bank.status,tp_hn_bank.addtime,tp_hn_bank.updatetime')
                ->order('tp_hn_bank.addtime desc')->where($aWhere),
			array($this,'mon')
        );
        $this->UDisplay('show1');
	}
	public function mon($data)
	{
		foreach($data as $k=>$v)
		{
			if($v['status']==0){
                $data[$k]['status']="新申请";
            }
            if($v['status']==1){
                $data[$k]['status']="打款成功";
            }
            if($v['status']==-1){
                $data[$k]['status']="不通过";
            }
            $data[$k]['addtime']=date('Y-m-d H:i:s',$v['addtime']);
			if($v['status']==1 || $v['status'] == -1){
				$data[$k]['updatetime']=date('Y-m-d H:i:s',$v['updatetime']);
			}
		}
		return $data;
	}
	//提现审核
	public function audio()
	{
        $info=M('hn_bank')->field('status,money,uid')->find($_GET['id']);
        if($info['status']!=0){
           $this->error2('已审核过了');
        }
		$v = M('hn_users')->field('yonjing')->where(array('token'=>$this->_sToken))->find();
        $this->Edit('hn_bank',array(
            array('title'=>'审核','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"打款成功",'value'=>"1"),
                array('content'=>"不通过",'value'=>"-1")
            )),
			array('title'=>'确认签约佣金','type'=>'hidden_true','name'=>'updatetime','value'=>time()),
        ),
		U('balance',array('token'=>$this->_sTtoken)),array($this,'audio1'));
    }
	public function audio1($data){
		$info=M('hn_bank')->field('status,money,uid')->find($_GET['id']);
		if($data['status']==1){
			M('Hn_users')->where(array('id'=>$info['uid']))->setDec('yonjing',$info['money']);
		}
		return $data;
	}
    //排行榜操作
    public function renwei(){
        $aWhere='';
        $order='yonjing desc';
        $aWhere['token'] =$this->_sToken;
        $aWhere['renwei'] =2;
        if(IS_POST){
            $order=$_POST['paixu_form'];
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
            $aWhere['renwei'] =2;

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
                'HeadHover' => U('renwei', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                   array(
                        'name' => '添加',
                        'url' => U('add_renwei',array('token'=>$this->token))
                    ),
                    /*
               array(
                   'name' => '导出',
                   'type'=>'daochu',//type=daochu时，可以带上搜索条件
                   'url' => U('excel',array('token'=>$this->token))
               ),
               array(
                   'name' => '一键',
                   'type'=>'yijin',//开启多选用这个type=yijin
                   'url' => U('yijin')
               ),*/



                ),
                'tips' => array(
                    '排行榜前10名操作'
                ),
                'Table_Header' => array(
                    'ID', '姓名','佣金','排名','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '修改',
                        'url' => U('edit_renwei')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_renwei')
                    ),


                ),
               /* 'search'=>array(
                    array('title'=>'标题','name'=>'li_title'),
                    array('title'=>'SELECT框','type'=>'select','name'=>'eq_select','many'=>array(
                        array('name'=>'是','value'=>'1'),
                        array('name'=>'否','value'=>'0'),
                        array('name'=>'其他','value'=>'3'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                )*/
            ),
            M('hn_users')->where($aWhere)->order($order)->count(),
            M('hn_users')->field('id,name,yonjing,add_time')->order($order)->where($aWhere),
            array($this,'renwei1')

        );
        $this->UDisplay('show1');
    }
    public function renwei1($data){
        foreach($data as $k=>$v){
            $data[$k]['add_time']=$k+1;
        }
        return $data;
    }

    //添加人为数据
    public function add_renwei(){
        $this->add('hn_users',array(
            array('title'=>'姓名','type'=>'input','name'=>'name'),
            array('title'=>'佣金','type'=>'input','name'=>'yonjing'),
            array('title'=>'','type'=>'hidden_true','name'=>'renwei','value'=>2),

        ),U('renwei',array('token'=>$this->_sTtoken)));

    }
    public function edit_renwei(){
        $this->Edit('hn_users',array(
            array('title'=>'姓名','type'=>'input','name'=>'name'),
            array('title'=>'佣金','type'=>'input','name'=>'yonjing'),
            array('title'=>'','type'=>'hidden_true','name'=>'renwei','value'=>2),

        ),U('renwei',array('token'=>$this->_sTtoken)));

    }
    public function del_renwei(){
        $this->del('hn_users');
    }
    //佣金商城
    public function shop(){
        $aWhere='';
        $order='sort desc';
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            $order=$_POST['paixu_form'];
            foreach($_REQUEST as $k=>$v){
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
                'HeadHover' => U('shop', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加',
                        'url' => U('add_shop',array('token'=>$this->token))
                    ),
                    /*
               array(
                   'name' => '导出',
                   'type'=>'daochu',//type=daochu时，可以带上搜索条件
                   'url' => U('excel',array('token'=>$this->token))
               ),
               array(
                   'name' => '一键',
                   'type'=>'yijin',//开启多选用这个type=yijin
                   'url' => U('yijin')
               ),*/



                ),
                'tips' => array(
                    '商城'
                ),
                'Table_Header' => array(
                    'ID', '名称','是否显示','佣金','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '修改',
                        'url' => U('edit_shopping')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_shopping')
                    ),


                ),
                /* 'search'=>array(
                     array('title'=>'标题','name'=>'li_title'),
                     array('title'=>'SELECT框','type'=>'select','name'=>'eq_select','many'=>array(
                         array('name'=>'是','value'=>'1'),
                         array('name'=>'否','value'=>'0'),
                         array('name'=>'其他','value'=>'3'),
                     )),
                     array('type'=>'br'),
                     array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                 )*/
            ),
            M('hn_shop')->where($aWhere)->order($order)->count(),
            M('hn_shop')->field('id,name,status,price')->order($order)->where($aWhere),
            array($this,'shop1')

        );
        $this->UDisplay('show1');
    }
    public function shop1($data){
		foreach($data as $k=>$v)
		{
			if($v['status']==1)
			{
				$data[$k]['status']='不显示';
			}
			else{
				$data[$k]['status']='显示';
			}
		}
        return $data;
    }
    public function add_shop(){
        $this->add('hn_shop',array(
            array('title'=>'商品名称','type'=>'input','name'=>'name'),
            array('title'=>'佣金','type'=>'img','name'=>'pic','many'=>array(
                array('title'=>'商品图片','name'=>'pic')
            )),
            array('title'=>'佣金','type'=>'input','name'=>'price'),
            array('title'=>'排序','name'=>'sort','type'=>'input','placeholder'=>'数值越大排在越前'),
            array('title'=>'链接','name'=>'link','type'=>'input','placeholder'=>'商品跳转地址，必须加上http://'),
            array('title'=>'一句话点评','name'=>'sai','type'=>'input'),
			array('title'=>'是否显示','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"显示",'value'=>"0"),
                array('content'=>"不显示",'value'=>"1")
            )),

        ),U('shop',array('token'=>$this->_sTtoken)));

    }
	public function edit_shopping(){
        $this->Edit('hn_shop',array(
           array('title'=>'商品名称','type'=>'input','name'=>'name'),
            array('title'=>'佣金','type'=>'img','name'=>'pic','many'=>array(
                array('title'=>'商品图片','name'=>'pic')
            )),
            array('title'=>'佣金','type'=>'input','name'=>'price'),
            array('title'=>'排序','name'=>'sort','type'=>'input'),
			array('title'=>'链接','name'=>'link','type'=>'input','placeholder'=>'商品跳转地址，必须加上http://'),
            array('title'=>'一句话点评','name'=>'sai','type'=>'input'),
			array('title'=>'是否显示','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"显示",'value'=>"0"),
                array('content'=>"不显示",'value'=>"1")
            )),
        ),U('shop',array('token'=>$this->_sTtoken)));

    }
	public function del_shopping(){
        $this->del('hn_shop');
    }
	//消费管理
	public function consume()
	{
		 $aWhere['tp_hn_xiaofeijl.token'] =$this->_sToken;
		 session('where_p',$aWhere);
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['tp_hn_xiaofeijl.token'] = $this->_sToken;
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
                'HeadHover' => U('consume', array('token' => $this->_sToken)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '消费记录列表'
                ),
                'Table_Header' => array(
                    '编号','商品名称', '购买人姓名','购买人手机号码','收货地址', '消费金额','备注','时间','状态','操作'
                ),
                'List_Opt' => array(
                     array(
                         'name' => '处理订单',
                         'url' => U('shop_status_update')
                     ),
                    array(
                        'name' => '发货地址',
                        'url' => U('address')
                    ),

                ),
              /*  'search'=>array(
                    array('title'=>'手机号码','name'=>'li_phone'),
                )*/
            ),

            M('hn_xiaofeijl')->join("join tp_hn_users on tp_hn_xiaofeijl.uid=tp_hn_users.id")->where($aWhere)->order('tp_hn_xiaofeijl.addtime desc')->count(),
            M('hn_xiaofeijl')
                ->join("join tp_hn_users on tp_hn_xiaofeijl.uid=tp_hn_users.id")
                ->field('tp_hn_xiaofeijl.id,tp_hn_xiaofeijl.s_name,tp_hn_users.name,
                tp_hn_users.phone,tp_hn_users.address,tp_hn_xiaofeijl.yonjing,tp_hn_xiaofeijl.other,tp_hn_xiaofeijl.addtime,tp_hn_xiaofeijl.status')
                ->order('tp_hn_xiaofeijl.addtime desc')
                ->where($aWhere),
            array($this,'shop_status')

        );

        $this->UDisplay('show1');
	}
	public function shop_status($data)
	{
		foreach($data as $key=>$v)
		{
			if($v['status']==1)
			{
				$data[$key]['status']='已发货';
			}
			else if($v['status']==2)
			{
				$data[$key]['status']='已收货';
			}else
			{
				$data[$key]['status'] = '购买成功';
			}
			$data[$key]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
		}
		return $data;
	}
	public function shop_status_update(){
        $info=M('hn_xiaofeijl')->field('status')->find($_GET['id']);
        if($info['status']==2){
            $this->error2('已经处理了');
        }
        $this->Edit('hn_xiaofeijl',array(
            array('title'=>'处理','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"发货",'value'=>"1"),
                array('content'=>"已收货",'value'=>"2")
            )),
        ),U('consume',array('token'=>$this->token)));
    }
    //地址
    public function address(){
        $info=M('hn_xiaofeijl')->field('shop_name,phone,address')->find($_GET['id']);
        $this->assign('info',$info);
        $this->display('./tpl/User/default/hn/address.html');
    }
	public function charts()
	{
		 $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
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
                'HeadHover' => U('charts', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                          'name' => '导出Excel表',
                          'url' => U('exceldc',array('token'=>$this->_sToken))
                      ),


					),
                'tips' => array(
                    '佣金排行榜记录列表'
                ),
                'Table_Header' => array(
                    '编号','姓名','佣金','级别','名次',''
                ),
            ),

            M('hn_users')->field('id,name,yonjing')->where($aWhere)->order('yonjing desc')->count(),
            M('hn_users')
                ->field('id,name,yonjing')
                ->order('yonjing desc')
                ->where($aWhere),
				array($this,'listc')

        );

        $this->UDisplay('show1');
	}
	public function listc($data)
	{
		foreach($data as $k=>$v)
		{
			if(0<=$v['yonjing']&&$v['yonjing']<200)
				{
					 $data[$k]['grade']='菜鸟顾问';
				}
				else if(200<=$v['yonjing']&&$v['yonjing']<400)
				{
					$data[$k]['grade']='高级顾问';
				}
				else if(400<=$v['yonjing']&&$v['yonjing']<800)
				{
					$data[$k]['grade']='销售经理';
				}
				else if(800<=$v['yonjing']&&$v['yonjing']<1600)
				{
					$data[$k]['grade']='销售总监';
				}
				else if(1600<=$v['yonjing']&&$v['yonjing']<3200)
				{
					$data[$k]['grade']='区域总监';
				}
				else if(3200<=$v['yonjing']&&$v['yonjing']<6400)
				{
					$data[$k]['grade']='区域副总';
				}
				else if(6400<=$v['yonjing']&&$v['yonjing']<12800)
				{
					 $data[$k]['grade']='区域老总';
				}
				else if(12800<=$v['yonjing']&&$v['yonjing']<25600)
				{
					$data[$k]['grade']='大区副总';
				}
				else if(25600<=$v['yonjing']&&$v['yonjing']<51200)
				{
					$data[$k]['grade']='大区老总';
				}
				else if(51200<=$v['yonjing']&&$v['yonjing']<102400)
				{
					$data[$k]['grade']='集团副总';
				}
				else if(102400<=$v['yonjing']&&$v['yonjing']<204800)
				{
					 $data[$k]['grade']='集团总裁';
				}
				else if(204800<=$v['yonjing']&&$v['yonjing']<409600)
				{
					 $data[$k]['grade']='执行董事';
				}
				else if(409600<=$v['yonjing']&&$v['yonjing']<819200)
				{
					 $data[$k]['grade']='董事长';
				}
				else if(819200<=$v['yonjing']&&$v['yonjing']<1638400)
				{
					$data[$k]['grade']='马上离职';
				}
				else if($v['yonjing']>=1638400)
				{
					$data[$k]['grade']='加入我们';
				}
				$data[$k]['more'] = $k+1;
		}
		return $data;
	}
	public function exceldc()
	{
		$data = M('Hn_users')->field('id,name,yonjing')->where(array('token'=>$this->_sToken))->order('yonjing desc')->select();
		foreach($data as $k=>$v)
		{
			if(0<=$v['yonjing']&&$v['yonjing']<200)
                {
                     $data[$k]['grade']='菜鸟顾问';
                }
                else if(200<=$v['yonjing']&&$v['yonjing']<400)
                {
                    $data[$k]['grade']='高级顾问';
                }
                else if(400<=$v['yonjing']&&$v['yonjing']<800)
                {
                    $data[$k]['grade']='销售经理';
                }
                else if(800<=$v['yonjing']&&$v['yonjing']<1600)
                {
                    $data[$k]['grade']='销售总监';
                }
                else if(1600<=$v['yonjing']&&$v['yonjing']<3200)
                {
                    $data[$k]['grade']='区域总监';
                }
                else if(3200<=$v['yonjing']&&$v['yonjing']<6400)
                {
                    $data[$k]['grade']='区域副总';
                }
                else if(6400<=$v['yonjing']&&$v['yonjing']<12800)
                {
                     $data[$k]['grade']='区域老总';
                }
                else if(12800<=$v['yonjing']&&$v['yonjing']<25600)
                {
                    $data[$k]['grade']='大区副总';
                }
                else if(25600<=$v['yonjing']&&$v['yonjing']<51200)
                {
                    $data[$k]['grade']='大区老总';
                }
                else if(51200<=$v['yonjing']&&$v['yonjing']<102400)
                {
                    $data[$k]['grade']='集团副总';
                }
                else if(102400<=$v['yonjing']&&$v['yonjing']<204800)
                {
                     $data[$k]['grade']='集团总裁';
                }
                else if(204800<=$v['yonjing']&&$v['yonjing']<409600)
                {
                     $data[$k]['grade']='执行董事';
                }
                else if(409600<=$v['yonjing']&&$v['yonjing']<819200)
                {
                     $data[$k]['grade']='董事长';
                }
                else if(819200<=$v['yonjing']&&$v['yonjing']<1638400)
                {
                    $data[$k]['grade']='马上离职';
                }
                else if($v['yonjing']>=1638400)
                {
                    $data[$k]['grade']='加入我们';
                }
				$data[$k]['more'] = $k+1;
		}
        Excel::arr2ExcelDownload($data,array('编号', '姓名','佣金','级别','名次'),'排行榜信息');
	}
	public function photo()
	{
		 $aWhere['token'] =$this->_sToken;

        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
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
                'HeadHover' => U('photo', array('token' => $this->_sToken)),
                'tips' => array(
                    '照片墙'
                ),
                'Table_Header' => array(
                    '编号','评论内容','评论时间', ''
                ),
				'List_Opt' => array(
                        array(
                            'name' => '删除',
                            'url' => U('del_photo')
                        ),
                ),
            ),

            M('wx_photo_comment')
                ->field('id,content,add_time')
                ->where($aWhere)
                ->order('add_time desc')
                ->count(),
            M('wx_photo_comment')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere),
           array($this,'tim')
        );

        $this->UDisplay('show1');
	}
    public function tim($data)
    {
        foreach($data as $k=>$v)
        {
             $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
	public function del_photo()
	{
        $this->del('wx_photo_comment');
	}
	public function c_photo()
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

            $aWhere['token'] = $this->_sToken;
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
                'HeadHover' => U('c_photo', array('token' => $this->_sToken)),
                'tips' => array(
                    '照片'
                ),
                //显示表头里是图片的字段，并且设置图片大小
                'aListImg' => array(
                    'container' => array('headurl'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'Table_Header' => array(
                    '编号','图片', '点赞数','添加时间', '发帖人', '发帖昵称', ''
                ),
				'List_Opt' => array(
                    array(
                        'name' => '查看评论',
                        'url' => U('show_comment')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('delc_photo')
                    ),
                ),
            ),

            M('wx_photo')
                ->field('id,local_pic,add_time')
                ->where($aWhere)
                ->order('add_time desc')
                ->count(),
            M('wx_photo')
                ->field('id, local_pic,click_num,add_time, openid')
                ->order('add_time desc')
                ->where($aWhere),
				array($this,'img')

        );

        $this->UDisplay('show1');
	}

	public function show_comment()
	{
        $aWhere['pid'] = $_REQUEST['id'];
        $this->table(
            array(
                'HeadHover' => U('c_photo', array('token' => $this->_sToken)),
                'tips' => array(
                    '照片'
                ),
                //显示表头里是图片的字段，并且设置图片大小
                'aListImg' => array(
                    'container' => array('headurl'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'Table_Header' => array(
                    '编号','内容', '发帖时间', '用户','用户昵称'
                ),
				'List_Opt' => array(
                ),
            ),

            M('wx_photo_comment')
                ->field('id,uid,content, add_time')
                ->where($aWhere)
                ->order('add_time desc')
                ->count(),
            M('wx_photo_comment')
                ->field('id,uid,content, add_time')
                ->where($aWhere)
                ->order('add_time desc'),
				array($this,'wx_comment')

        );
        $this->UDisplay('show1');
	}

    public function wx_comment($data)
    {
        foreach ($data as $k => $v) {
            $data[$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $userinfo = M('Wxusers')->where(array(
                 'id' => $v['uid']
            ))->find();
            $data[$k]['headurl'] = Arr::get($userinfo, 'headimgurl');
            $data[$k]['nickname']   = Arr::get($userinfo, 'nickname');
            unset($data[$k]['uid']);
        }
        return $data;
    }

	 public function img($data){
        foreach($data as $k=>$v){
           $data[$k]['local_pic']="<img src='".$v['local_pic']."' width=70px />";
           //通过openid找到用户信息
           $userinfo = M('Wxusers')->where(array(
                'openid'    => $data[$k]['openid']
           ))->find();
            if(Arr::get($userinfo, 'headimgurl')){
                $data[$k]['headurl'] = Arr::get($userinfo, 'headimgurl');
            }else{
                $data[$k]['headurl'] = '';
            }
           $data[$k]['nickname'] = Arr::get($userinfo, 'nickname');
           unset($data[$k]['openid']);
        }
        return $data;
    }
	public function delc_photo()
	{
        $this->del('wx_photo');
	}

}
?>
