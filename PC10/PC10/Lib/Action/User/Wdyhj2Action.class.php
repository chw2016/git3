<?php
/**
 *  银波米业1
 **/
class Wdyhj2Action extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/store';

    /**
     *  Token
     **/
    //private $_sToken = null;

    /**
     *  UID
     **/
    //private $_iUID = null;

    /**
     *  顶部
     **/
    public function _initialize()
    {
    	parent::_initialize();
    	
        $this->pz	   = D('mru_wdyhj');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    
    protected function setHeader(){
    	$aid=$_GET['aid'];
    	return include "./Lib/Action/User/Mru_top2.php";
    }

    public function uploadIcon(){
        //如果是POST上传图片
        $Model = M('Miye_logo');
        if(IS_POST){
            $aRet  = array();
            if($_POST['op'] == 1) {//update
                $bUp = $Model->data(array(
                    'icon' => $_POST['icon'],
                    'add_time' => date('Y-m-d H:i:s')
                ))->where(array(
                    'token' => $_POST['token'],
                    'type'  => 1
                ))->save();
            }else{
                $bUp = $Model->add(array(
                    'token' => $_POST['token'],
                    'add_time' => date('Y-m-d H:i:s'),
                    'type'  => 1,
                    'icon'  => $_POST['icon']
                ));
            }
            if($bUp){
                $aRet = array(
                    'status' => 0,
                    'info' =>'上传成功',
                    'url' => U('Miye/index',
                        array('token' => $_POST['token']))
                );
            }else{
                $aRet = array('status' => -1, 'info' =>'系统繁忙，请稍后再试');
            };
            $this->ajaxReturn($aRet);
        }else{
            $aIcon = $Model->where(array('token' => $this->token, 'type' => 1))->select();
            if(isset($aIcon[0])) { $aIcon = $aIcon[0]; }
            $this->assign('icon', $aIcon);
            $this->assign('token', $this->token);
            $this->UDisplay('uploadIcon');
        }
    }


    public function iconDelete(){
        $aRet  = array();
        $Model = M('Ieat_icon');
        if(
        $Model->delete(array(
            'token' => $_POST['token'],
            'type'  => 1,
            'icon'  => $_POST['icon']
        ))
        ){
            $aRet = array(
                'status' => 0,
                'info' =>'删除成功',
                'url' => U('Ieat/mallcontent',
                    array('token' => $_POST['token']))
            );
        }else{
            $aRet = array('status' => -1, 'info' =>'系统繁忙，请稍后再试');
        };
        $this->ajaxReturn($aRet);
    }


    /**
     *  显示表单
     **/
	public function index(){
		
		$aWhere['token']=$_SESSION['token'];
        $aWhere['aid'] = $_SESSION['aid'];
		
		//搜索
		if(IS_POST){
			$_POST=$_REQUEST;
			$aWhere=$this->search($_POST);
            $aWhere['sn_code'] = $aWhere['sn_code'][1];
			$aWhere['token'] =$_SESSION['token'];
			//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
            $this->table(
                array(
                    'abc'=>123,
                    //'id' => 'id',//如果主键不是id，则需要设置
                    'HeadHover' => U('Wdyhj2/index', array('token' =>$_SESSION['token'])),//栏目样式
                    /*             	'Head_Opt' => array(
                                        array(
                                            'name'   => '添加优惠卷',//2级
                                            'url'    => U('Wdyhj2/add_pinzhong')
                                        )
                                    ), */
                    'tips' => array(//3级
                        '注意:下面优惠券是本店消费，当会员来兑换优惠券时请在上面查询框里面输入会员要使用优惠券相应的验证码。'
                    ),
                    'Table_Header' => array(//4级
                        'ID','昵称', '电话','优惠券名称', '验证码','使用状态','使用时间','操作'
                    ),
                    'List_Opt' => array(

                        array(
                            'name' => '使用',
                            'url'  => U('Wdyhj2/Sy')
                        ),

                    ),

                    //搜索
                    'search'=>array(
                        array('title'=>'验证码','name'=>'eq_sn_code','placeholder'=>'请输入优惠券的验证码精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                    )//结束


                ),
                M('mru_yhj2')->where($aWhere)->count(),
                M('mru_yhj2')->field('id,openid,tel,uid,sn_code,type,use_time')->order("add_time")->where($aWhere),
                array($this,'abc')
            );
		}else{
            $this->table(
                array(
                    'abc'=>123,
                    //'id' => 'id',//如果主键不是id，则需要设置
                    'HeadHover' => U('Wdyhj2/index', array('token' =>$_SESSION['token'])),//栏目样式
                    'tips' => array(//3级
                        '注意:下面优惠券是本店消费，当会员来兑换优惠券时请在上面查询框里面输入会员要使用优惠券相应的验证码。'
                    ),
                    'Table_Header' => array(//4级
                        'ID','昵称', '电话','优惠券名称', '验证码','使用状态','使用时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '删除',
                            'url'  => U('Wdyhj2/xDel')
                        ),
                    ),

                    //搜索
                    'search'=>array(
                        array('title'=>'验证码','name'=>'eq_sn_code','placeholder'=>'请输入优惠券的验证码精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                    )//结束
                ),
                M('mru_yhj2')->where($aWhere)->count(),
                M('mru_yhj2')->field('id,openid,tel,uid,sn_code,type,use_time')->order("add_time")->where($aWhere),
                array($this,'abc')
            );
        }
       

	}

	public function abc($data){
		foreach($data as $k=>$v){
        //优惠券的名称
            $data[$k]['uid'] = M('Mru_wdyhj')->where(array('id'=>$v['uid']))->getField('name');
        //使用状态
            switch($v['type']){
                case 0:$data[$k]['type'] = '未使用';break;
                case 1:$data[$k]['type'] = '已使用';break;
                default:$data[$k]['type'] = '其他';break;
            }
            $user = M('Wxuser')->where(array('token'=>$_SESSION['token']))->find();
            $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$v['openid']))->find();
            $data[$k]['openid'] = $users['nickname'];
        //昵称
		}
		return $data;
	}


    public function AssignArea()
    {
        $this->assign(array(
            'area' => array_merge(array('-1' => '请选择地区'), Arr::changeIndexToKVMap(
                D('finances_area')->field('id, title')
                    ->where(array('token' => $this->_sToken))
                    ->select(), 'id', 'title'))
        ));
    }
    /**
     * 添加店铺
     */
    public function add_dianpu(){
        $this->add('Miye_dianpu',array(
            array('title'=>"店铺名",'type'=>"input",'name'=>"name"),
            array('title'=>"描述",'type'=>"input",'name'=>"desc")

        ),U('Miye/dianpu'));
    }

    /**
     *  店铺
     **/
	public function dianpu(){
		$Model           = $this->dp;
		$aWhere['token'] = $this->_sToken;
		$this->table(array(
            //'id' => 'id',//如果主键不是id，则需要设置
            'HeadHover' => U('Miye/dianpu', array('token' => $this->_sToken)),
            'Head_Opt' => array(
                array(
                    'name'   => '添加店铺',
                    'url'    => U('Miye/add_dianpu')
                )
            ),
            'Table_Header' => array(
                'ID', '店铺名', '描述', '操作'
            ),
            'tips' => array(
                '你可以在这里管理店铺信息'
            ),
            'List_Opt' => array(
                array(
                    'name' => '编辑',
                    'url'  => U('Miye/dianpuEdit')
                ),
                array(
                    'name' => '删除',
                    'url'  => U('Miye/dianpuDel')
                ),
            )
        	),
	        $Model->where($aWhere)->count(),
	        $Model->table('tp_miye_dianpu')
	        	->field('id, name, desc')
	            ->where($aWhere),
	        array(),
            array(
				'FormUrl' => U('Miye/dianpuAdd',array('token'=>$_SESSION['token'])),
	            'ExtraBtn' => array(
	                array(
	                    'url'  => U('Miye/dianpu', array('token' => $this->_sToken)),
	                    'name' => '返回'
	            ))
        	)
		);
	}

    /**
     *  添加-店铺
     **/
	public function dianpuAdd(){
		if(IS_POST){
			$Model = $this->dp;
			if($Model->create() != false){
				if($Model->add()){
					$this->success2('恭喜：添加成功',U('Miye/dianpu'));
				}else{
					$this->error2('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error2($Model->getError());
			}
		}else{
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Miye/dianpu', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
                'FormUrl' => U('Miye/dianpuAdd',array('token'=>$_SESSION['token']))
            ));
            $this->UDisplay('dianpu_add');
		}
	}

	public function dianpuEdit(){
        $Model = $this->dp;
		if(IS_POST){
			$_POST['id']    = FC::P('id');
			$_POST['token'] = $this->_sToken;
			$aWhere         = array(
                'id'    => FC::P('id'),
                'token' => FC::P('token')
            );
			$Item    = $Model->where($aWhere)->find();
			if($Item == false) $this->error2('非法操作');
			if($Model->create()){
				if($Model->where($aWhere)->save($_POST)){
					$this->success2('修改成功');
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($Model->getError());
			}
		}else{
			$iID     = $this->_get('id');
			$Item    = $Model->where(array(
                'id' => $iID,
                'token' => $this->_sToken
            ))->find();
			if($Item == false) $this->error2('非法操作');
			$this->assign(array(
                'info' => $Item,
                'id' => $iID,
                'FormUrl' => U('Miye/dianpuEdit',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Miye/dianpu', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
            ));
            $this->UDisplay('dianpu_add');
		}
	}


    /**
     *  删除店铺
     **/
	public function dianpuDel(){
		$iID     = $this->_get('id');
		$aWhere  = array('id'=>$iID,'token' => $this->_sToken);
		$Model	 = $this->dp;
		$bCheck  = $Model->where($aWhere)->find();
		if($bCheck == false) $this->error2('非法操作');
		if($Model->where($aWhere)->delete()) {
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
	}

    //////////////////////////订单管理////////////////////////////////////
    /**
     *  理财-订单
     **/
	public function order(){
        #页面表基本配置
        $this->assign(array(
            //'id' => 'id',//如果主键不是id，则需要设置
            'HeadHover' => U('Miye/order', array('token' => $this->_sToken)),
            'Head_Opt' => array(),
            'Table_Header' => array(
                'ID', '品种', '手机号', '店铺名',
        		'地址', '描述','状态', '下单时间',
        		 '操作'
            ),
            'tips' => array(
                '你可以在这里管理订单信息'
            ),
            'List_Opt' => array(
                array(
                    'name' => '查看详细',
                    'url'  => U('Miye/OrderShow', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '完成',
                    'url'  => U('Miye/orderHandle', array(
                    	'token' => $this->_sToken,
                		'status' => Miye_orderModel::STATUS_COMPLETE
                	))
                ),
                array(
                    'name' => '取消',
                    'url'  => U('Miye/orderHandle', array(
                    	'token' => $this->_sToken,
                		'status' => Miye_orderModel::STATUS_CANCEL
                	))
                ),
            )
        ));
		$Model             = $this->order;
		$aWhere['token'] = $this->_sToken;
		$iCount = $Model->where($aWhere)->count();
		$Page   = new Page($iCount,25);

		$aInfo  = $Model->table('tp_miye_order o')
			->field('
            o.id, pz.pinzhong as pzname, o.mobile, dp.name as dpname,
            o.address, o.desc, o.status, o.add_time
            ')
			->join('tp_miye_dianpu dp on dp.id = o.dianpu_id')
			->join('tp_mru_wdyhj pz on pz.id = o.pinzhong_id')
            ->where(array('o.token' => $this->_sToken))
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        if ($aInfo) foreach($aInfo as $iK => $aItem) {
            foreach ($aItem as $sField => $Item) {
                if ('status' == $sField) {
                    $aInfo[$iK][$sField] = $Model->getStatusName($Item);
                }
            }
        }

		$this->assign(array(
            'page'     => $Page->show(),
            'aList'    => $aInfo
        ));
		$this->UDisplay('show');
	}
	
	public function orderHandle(){
		$iID     = $this->_get('id');
		$iStatus = $this->_get('status');
		$aWhere  = array('id'=>$iID,'token' => $this->_sToken);
		$Model	 = $this->order;
		$bCheck  = $Model->where($aWhere)->find();
		if($bCheck == false || !in_array($iStatus, array(
			Miye_orderModel::STATUS_CANCEL,
			Miye_orderModel::STATUS_UNDEAL,
			Miye_orderModel::STATUS_COMPLETE
		))) $this->error2('操作非法');
		if($Model->where($aWhere)->save(array('status' => $iStatus))) {
			$this->success2('操作成功');
		}else{
			$this->error2('操作失败');
		}
	}

	public function OrderShow(){
        $iID    = (int)$this->_get('id');
		$Model  = $this->order;
        $aInfo  = $Model->table('tp_miye_order o')
        	->field('
            o.id, pz.pinzhong as pzname, o.mobile, dp.name as dpname,
            o.address, o.desc, o.status, o.add_time
            ')
			->join('tp_miye_dianpu dp on dp.id = o.dianpu_id')
			->join('tp_mru_wdyhj pz on pz.id = o.pinzhong_id')
			->where(array('o.id' => $iID, 'o.token' => $this->_sToken))
			->select();
			
        if (!$aInfo) {
            $this->error2('订单不存在', U('Miye/order'));
            return false;
        }
        
        foreach($aInfo as $key => $aData) {
        	foreach ($aData as $sKey => $aItem){
        		if('status' == $sKey){
        			$aInfo[$key][$sKey] = $Model->getStatusName($aItem);
        		}
        	}
        }
        
        $this->assign(array(
            'info' => array_combine(array(
                'ID', 
                '品种', 
                '手机号', 
                '店铺名',
        		'地址', 
        		'描述', 
        		'状态',
        		'下单时间'
            ), array_values($aInfo[0]))
        ));
        $this->UDisplay('order_show');
	}
	//定义增改函数
	public function addEdit($aaa){
		$aid=$_GET['aid'];
		//特殊点选框,复选框,下拉列表
/* 		$list=M('Wdyhj22')->select();//被添加内容的表
		foreach ($list as $k=>$v){
			$list[$k]['content']=$v['name'];//把内容子段改成content
			$list[$k]['value']=$v['id'];//把id子段改成value
			unset($list[$k]['name']);//删除原来的内容子段
    	} */
		$this->$aaa('mru_wdyhj',array(
				array('title'=>"优惠卷名称",'type'=>"input",'name'=>"name",'value'=>'name'),
				array('type'=>'img','many'=>array(
						array('title'=>"优惠卷图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
				
				)),
				array('title'=>"优惠卷有效时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
				//array('type'=>'radio','title'=>"优惠卷使用者",'name'=>"uid",'value'=>'uid','msg'=>'请填写标题咯','many'=>$list),
			
				array('title'=>"优惠卷价格",'type'=>"number",'name'=>"price",'msg'=>'','value'=>'price'),
			
				
			
			
				array('title'=>"优惠卷使用细则",'type'=>"textarea",'name'=>"content",'value'=>'content'),
		
		),U('Wdyhj2/index',array('aid'=>$aid)),array($this,'bbc'));
	}
	public function bbc($data){
	    //p($data);die;
		//$data['aid']=28;
		$data['yzm']=time();
		return $data;
	}
	
    /**
     *  添加-品种
     * 如果写了msg代表此字段为必填，值为错误提示内容
     * array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y')   这是添加地图
     **/
    public function add_pinzhong(){
	    $this->addEdit(add);

    }
   /**
     * 编辑品牌    hidden
     */
    public function PinzhongEdit(){
    	$this->addEdit(Edit);
    }
   /**
     *  删除品种
     **/
    public function PinzhongDel(){
    	M('mru_yhj2')->where(array('uid'=>$_GET['id']))->delete();
        $this->del('mru_wdyhj');
    }
    
    
    public function xx(){
        //$aWhere['aid'] = $_SESSION['aid'];
    	$aWhere['token']=$_SESSION['token'];
    	$aWhere['uid'] =$_GET['id'];
    	//存手机
    /*	foreach ($list as $ke=>$v){
    		$tel=M('mru_jfb')->where(array('token'=>$_SESSION['token'],'openid'=>$v['openid']))->getField('tel');
    		M('mru_yhj2')->where(array('token'=>$_SESSION['token'],'openid'=>$v['openid']))->save(array('tel'=>$tel));
    	}
    	
    	foreach ($list as $ke=>$v){
    		$list[$ke]['name']=M('mru_jfb')->where(array('token'=>$_SESSION['token'],'openid'=>$v['openid']))->getField('name');
    		$list[$ke]['value']=$v['openid'];
    	}
     	foreach ($list as $ke=>$v){
    		$list[$v['name']]['name']=$v['name'];
    		$list[$v['name']]['value']=$v['openid'];
    		unset($list[$ke]);
    	}
    */
    	
/*     	array('title'=>'姓名','name'=>'eq_sex','type'=>'select','many'=>array(
    			array('value'=>'1','name'=>'男'),
    			array('value'=>'2','name'=>'女'),
    			//array('value'=>'3','name'=>'旅游1'),
    	)), */
    	
    	//搜索
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
            $aWhere['tel'] = $aWhere['tel'][1];
    		$aWhere['token'] =$_SESSION['token'];
    		$aWhere['uid'] =$_GET['id'];
            $this->table(
                array(
                    'abc'=>123,
                    //'id' => 'id',//如果主键不是id，则需要设置
                    'HeadHover' => U('Wdyhj2/xx', array('token' =>$_SESSION['token'],'id'=>$_GET['id'])),//栏目样式
                    'Head_Opt' => array(
                        array(
                            'name'   => '返回',//2级
                            'url'    => U('Wdyhj2/index',array('uid'=>$_GET['id']))
                        )
                    ),
                    'tips' => array(//3级
                        '注意'
                    ),
                    'Table_Header' => array(//4级
                        'ID', '优惠卷名称','状态','使用时间', '会员姓名', '会员手机','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '使用',
                            'url'  => U('Wdyhj2/Sy',array('uid'=>$_GET['id']))
                        ),
                        array(
                            'name' => '删除',
                            'url'  => U('Wdyhj2/xDel',array('uid'=>$_GET['id']))
                        ),
                    ),

                    //搜索
                    'search'=>array(

                        //array('title'=>'用户昵称查询','name'=>'eq_openid','type'=>'select','many'=>$list),
                        array('title'=>'手机号码查询','name'=>'eq_tel','placeholder'=>'请输入手机号码精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                        //array('title'=>'使用时间查询','name'=>'be_add_time','type'=>'between','search'=>'查询')//be是Table里判断条件 add_time是子段
                        //array('title'=>'名称','name'=>'eq_name'),//eq是Table里判断条件 name是子段
                        //array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','search'=>'查询')//be是Table里判断条件 add_time是子段
                    ),//结束

                ),
                M('mru_yhj2')->where($aWhere)->count(),
                M('mru_yhj2')->field('id,uid,type,use_time,openid,tel')->where($aWhere),
                array($this,'xxabc')

            );




    	}else{
            $aWhere['aid'] = $_SESSION['aid'];
            $this->table(
                array(
                    'abc'=>123,
                    //'id' => 'id',//如果主键不是id，则需要设置
                    'HeadHover' => U('Wdyhj2/xx', array('token' =>$_SESSION['token'],'id'=>$_GET['id'])),//栏目样式
                    'Head_Opt' => array(
                        array(
                            'name'   => '返回',//2级
                            'url'    => U('Wdyhj2/index',array('uid'=>$_GET['id']))
                        )
                    ),
                    'tips' => array(//3级
                        '注意'
                    ),
                    'Table_Header' => array(//4级
                        'ID', '优惠卷名称','状态','使用时间', '会员姓名', '会员手机','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '删除',
                            'url'  => U('Wdyhj2/xDel',array('uid'=>$_GET['id']))
                        ),
                    ),

                    //搜索
                    'search'=>array(

                        //array('title'=>'用户昵称查询','name'=>'eq_openid','type'=>'select','many'=>$list),
                        array('title'=>'手机号码查询','name'=>'eq_tel','placeholder'=>'请输入手机号码精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                        //array('title'=>'使用时间查询','name'=>'be_add_time','type'=>'between','search'=>'查询')//be是Table里判断条件 add_time是子段
                        //array('title'=>'名称','name'=>'eq_name'),//eq是Table里判断条件 name是子段
                        //array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','search'=>'查询')//be是Table里判断条件 add_time是子段
                    ),//结束

                ),
                M('mru_yhj2')->where($aWhere)->count(),
                M('mru_yhj2')->field('id,uid,type,use_time,openid,tel')->where($aWhere),
                array($this,'xxabc')

            );
        }


    }
    
    public function xxabc($data){
    	foreach($data as $k=>$v){
    		//sql语句
    		$data[$k]['uid']=M('mru_wdyhj')->where(array('id'=>$v['uid']))->getField('name');
    		switch($v['type']){
                case 0:$data[$k]['type'] = '未使用';break;
                case 1:$data[$k]['type'] = '已使用';break;
                default:$data[$k]['type'] = '其他';break;
            }
           // $data[$k]['add_time'] = date('Y-m-d',$v['add_time']);
    		$list=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->find();
    		
    		$data[$k]['openid']=$list['name'];
    	}
    	return $data;
    }
    
    
    public function xxEdit(){
    	
    	$this->xxEdit2(Edit);
    }
    
    public function xxxDel(){
    	echo '<script>if(confirm("使用将会删除该会员的优惠卷!你确定要使用吗？")){location.href="'.U('xxDel',array('id'=>$_GET['id'],'uid'=>$_GET['uid'])).'"}else{history.back();};</script>';
    	
    	//$this->redirect('index');
    
    }
    
    public function xxDel(){
    	$j_time=M('mru_wdyhj')->where(array('id'=>$_GET['uid']))->getField(j_time);
    	if(strtotime($j_time)<time()){
    		echo '<script>alert("优惠卷已过期!");history.back();</script>';
    		die;
    	}
    
    	$b=M('mru_yhj2')->where(array('id'=>$_GET['id']))->delete();
    	if($b){
    		echo '<script>alert("使用成功!");location.href="'.U('xx',array('id'=>$_GET['uid'])).'"</script>';
    	}
    	
    	
    	//$this->del('mru_jfxx');
    }
    
    public function xDel(){
    	$this->del('mru_yhj2');
    }
    
    
    //排序  只 能主健是id,子段是sort 才能排序  有意见自己去开发！
    public function sortajax(){
    	$this->sortajaTable('mru_wdyhj');
    }
    
    
    
    
    
    public function Fj(){
    
    	$uid=$_GET['id'];
    	if(IS_POST){
    		
    		if($_REQUEST['name']){
    			foreach ($_REQUEST['name'] as $v){
    				M('mru_yhj2')->add(array('token'=>$_SESSION['token'],'uid'=>$uid,'openid'=>$v));
    			}
    			script("发卷成功","Wdyhj2/index");
    		}else{
    			script("发卷失败");
    		}
    	
    		
    	}else{
    		
    		
    		//特殊点选框,复选框,下拉列表
    		$list=M('mru_jfb')->where(array('token'=>$_SESSION['token']))->select();//被添加内容的表
    		//如果名称为空删除该条数据
    		foreach ($list as $ke=>$v){
    			if(!$v['name']) unset($list[$ke]);
    		}
    		
    		foreach ($list as $k=>$v){
    			$list[$k]['content']=$v['name'];//把内容子段改成content
    			$list[$k]['value']=$v['openid'];//把id子段改成value
    			unset($list[$k]['name']);//删除原来的内容子段
    		}
    		$this->add('mru_jfb',array(
    				array('type'=>'checkbox','title'=>'会员角色', 'name'=>'name','全选'=>1, 'value'=>'name','msg'=>'请填写标题咯','many'=>$list),
    		),U('Wdyhj2/index',array('aid'=>$aid)),array($this,'bbc'),'',array('token'=>$_SESSION['token']));
    		
    	}
    	
    }
    
    Public function Sy(){
    	
    	$info=M('mru_yhj2')->where(array('id'=>$_GET['id']))->find();
    	if($info['type']==1) echo "<script>history.back();</script>";
    	
    	$j_time=M('mru_wdyhj')->where(array('id'=>$info['uid']))->getField(j_time);
    	if(strtotime($j_time)<time()){
    		echo '<script>alert("优惠卷已过期!");history.back();</script>';
    		die;
    	}
    	

    	$b=M('mru_yhj2')->where(array('id'=>$_GET['id']))->save(array('type'=>1,'add_time'=>time(),'aid'=>$_SESSION['aid'],'use_time'=>date('Y-m-d H:i:s')));
    	if($b) script("使用成功",U('Wdyhj2/index',array('token'=>$_SESSION['token'])));
    	
    }
}
?>
