<?php
/**
 *  银波米业1
 **/
class HyzxAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/together';

    /**
     *  Token
     **/
    //private $_sToken = null;

    /**jfadd_pinzhong
     *  UID
     **/
    //private $_iUID = null;

    /**
     *  顶部
     **/
    public function _initialize()
    {
    	parent::_initialize();
        $this->pz	   = D('Miye_pinzhong');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    
    protected function setHeader(){
    	return array(
                array(
                    'name' => '积分兑换',
                    'url'  => U('Hyzx/jf', array('token' => $this->_sToken))
                ),
    			
    			array(
    					'name' => '积分广告位',
    					'url'  => U('Mruggw/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '个人资料',
    					'url'  => U('Mrugr/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '我的优惠券',
    					'url'  => U('Wdyhj/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '评论管理',
    					'url'  => U('Mrupl/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '我要积分',
    					'url'  => U('MruJf/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '订单管理',
    					'url'  => U('Store_new/orders', array('token' => $this->_sToken,'mru'=>1))
    			),

    			
    			
    			
    			

            );
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
		$aWhere = array('token' => $this->_sToken);
		$this->table(
        	array(
                'abc'=>123,
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('Hyzx/index', array('token' => $this->_sToken)),//栏目样式 
            	'Head_Opt' => array(
                	array(
                    	'name'   => '会员中心',//2级
                    	'url'    => U('Hyzx/index')
                	),
            			array(
            					'name'   => '积分兑换',//2级
            					'url'    => U('Hyzx/jf')
            			)
            	),
                'tips' => array(//3级
                    '你可以在这里管理品种信息'
                ),
            	'Table_Header' => array(//4级
                	'ID', '品种名称', '添加时间','排序', '操作'
            	),
            	'List_Opt' => array(
            			
                	array(
                    	'name' => '编辑',
                    	'url'  => U('Hyzx/PinzhongEdit')
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('Hyzx/PinzhongDel')
	                ),
            	)
        	), 
        	M('mru_mhyzxsy')->where($aWhere)->count(),
        	M('mru_mhyzxsy')->field('id,sort')->order("sort")->where($aWhere),
            array($this,'abc')
         );
	}
	
	
	/**
	 *  显示表单
	 **/

	

	public function abc($data){
		foreach($data as $k=>$v){
			$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
			$data[$k]['content']=substr(htmlspecialchars_decode($v['content']),0,78); 
			$data[$k]['state']="{$v['state']}"?'活动已开启':'活动关闭';
			switch ($v['sex']){
				case 1:$data[$k]['sex']='男';break;
				case 2:$data[$k]['sex']='女';break;
				case 3:$data[$k]['sex']='不限';break;
			}
			//特殊复选框显示，先把保存在$v['mdiao']里的id偏历出来，然后用表的id=$v['mdiao']保存的id 把名字查询出来值给临时变量
			foreach (explode(',',$v['mdiao']) as $ke=>$s){//字符转数组遍历
				$str=M('mru_mdian')->where(array('id'=>$s))->field('name')->find();
				$str=implode(',', $str);//$str是个一维数组，转成字符串
				$data[$k]['aaa'].=$str.',';//用,拼结起来，值给临时变量
			}
			$data[$k]['mdiao']=$data[$k]['aaa'];//临时变量的值给正常子段赋值，改变正常子段
			$data[$k]['mdiao']=rtrim($data[$k]['mdiao'],',');//去右边,
			unset($data[$k]['aaa']);//删除临时变量   结束处!
			
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
			->join('tp_miye_pinzhong pz on pz.id = o.pinzhong_id')
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
			->join('tp_miye_pinzhong pz on pz.id = o.pinzhong_id')
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
		//特殊点选框,复选框,下拉列表
/* 		$list=M('Hyzx2')->select();//被添加内容的表
		foreach ($list as $k=>$v){
			$list[$k]['content']=$v['name'];//把内容子段改成content
			$list[$k]['value']=$v['id'];//把id子段改成value
			unset($list[$k]['name']);//删除原来的内容子段
    	} */
		$this->$aaa('Miye_pinzhong',array(
				array('title'=>"较长input框",'type'=>"longinput",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'),
				array('title'=>"input框",'type'=>"input",'name'=>"name",'value'=>'name'),
				array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd'),
				array('title'=>"隐藏",'type'=>"hidden",'name'=>"id",'msg'=>'请填写标题咯','value'=>'id'),
				array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
				array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
				array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
				array('type'=>'radio','title'=>"点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
						array('value'=>'1','content'=>'男'),
						array('value'=>'2','content'=>'女'),
				)),
				array('type'=>'radio','title'=>"特殊点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
				array('type'=>'select','title'=>"下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
						array('value'=>'1', 'content'=>'一年以上'),
						array('value'=>'2','content'=>'二年以上'),
						array('value'=>'3','content'=>'三年以上'),
						array('value'=>'4','content'=>'不限'),
				)),
				array('type'=>'select','title'=>"特殊下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
				array('type'=>'checkbox','title'=>'复选框','name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
						array('value'=>'1','content'=>'蓝'),
						array('value'=>'2','content'=>'红'),
				)),
				array('type'=>'checkbox','title'=>'特殊复选框', 'name'=>'jy', 'value'=>'jy','msg'=>'请填写标题咯','many'=>$list),
				array('type'=>'img','many'=>array(
						array('title'=>"图片1",'type'=>"img",'name'=>"pic",'value'=>'pic'),
						array('title'=>"图片2",'type'=>"img",'name'=>"pic2",'value'=>'pic'),
						array('title'=>"图片2",'type'=>"img",'name'=>"pic3",'value'=>'pic')
				)),
				array('title'=>"小的复文本框",'type'=>"textarea2",'name'=>"hd_time",'value'=>'hd_time'),
				array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
				array('title'=>"经纬度",'type'=>"map",'name'=>'name', 'lng'=>"position_x",'lat'=>'position_y'),
		),U('Hyzx/index'),array($this,'bbc'));
	}
	public function bbc($data){
		$data['password']=MD5($data['password']);
		$data['sex']=1;
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
        $this->del('Miye_pinzhong');
    }
    //积分
    
    public function jf(){
    	$aWhere = array('token' => $this->_sToken);
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('Hyzx/jf', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '添加积分兑换物品',//2级
    									'url'    => U('Hyzx/jfadd_pinzhong')
    							),
                            array(
                                'name'   => '查看兑换信息',//2级
                                'url'    => U('Hyzx/see_info')
                            )
    							
    					
    						
    					),
    					'tips' => array(//3级
    							'你可以在这里管理积分兑换信息'
    					),
    					'Table_Header' => array(//4级
    							'ID', '商品图片', '标题','所需积分', '数量','已领取数量', '是否开启','排序' ,'操作'
    					),
    					'List_Opt' => array(
    							
    							array(
    									'name' => '兑换信息',
    									'url'  => U('Hyzx/xx',array('token'=>$_SESSION['token']))
    							),
    							array(
    									'name' => '编辑',
    									'url'  => U('Hyzx/jfPinzhongEdit')
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('Hyzx/jfPinzhongDel')
    							),
    					)
    			),
    			M('mru_jf')->where($aWhere)->count(),
    			M('mru_jf')->field('id,pic,name,jf,num,vnum,state,sort')->order("sort")->where($aWhere),
    			array($this,'ifabc')
    	);
    }
    
    
    public function ifabc($data){
    	foreach($data as $k=>$v){
    	    
    		$data[$k]['name']=str_substr($v['name'],10,'...');
    		$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
    		
    		$data[$k]['state']="{$v['state']}"?'开启':'关闭';
    	
    		

    		 
    	}
    	return $data;
    }

    public function see_info(){
        $aWhere = array('token' => $this->_sToken);
        $this->table(
            array(
                'HeadHover' => U('Hyzx/jf', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Hyzx/jf')
                    ),
                ),
                'tips' => array(//3级
                    '你可以在这里查看会员积分兑换信息'//兑换时间、兑换产品名称、兑换人昵称、手机号、地址等
                ),
                'Table_Header' => array(//4级
                    'ID', '昵称', '兑换产品名称','兑换时间','手机号','会员号','地址'
                ),
            ),
            M('mru_jfxx')->where($aWhere)->count(),
            M('mru_jfxx')->field('id,openid,uid,add_time')->where($aWhere)->order('id desc'),
            array($this,'seeinfo')
        );
    }
public function seeinfo($data){
    foreach($data as $k=>$val){
        $user = M('Wxuser')->where(array('token'=>$this->_sToken))->find();
        $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$val['openid']))->find();
        $data[$k]['openid'] = $users['nickname'];
        $info = M('mru_jfb')->where(array(
            'token'=>$this->_sToken,
            'openid'=>$val['openid'],
        ))->find();
        $data[$k]['uid'] = M('mru_jf')->where(array('id'=>$val['uid']))->getField('name');
        $data[$k]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
        $data[$k]['tel'] = $info['tel'];
        $data[$k]['idcard'] = $info['idcard'];
        $data[$k]['address'] = $info['address'];
    }
    return $data;
}
    
    public function jfaddEdit($aaa){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('Hyzx2')->select();//被添加内容的表
    		foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('mru_jf',array(
    			
    			array('title'=>"标题1",'type'=>"input",'name'=>"name",'value'=>'name'),
    			array('type'=>'img','many'=>array(
    					array('title'=>"商品图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
    			
    			)),
    			array('title'=>"所需积分",'type'=>"number",'name'=>"jf",'msg'=>'','value'=>'jf'),
    			array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
    			
    			
    			array('type'=>'radio','title'=>"是否显示",'name'=>"state",'value'=>'state','msg'=>'请填写标题咯','many'=>array(
    					array('value'=>'0','content'=>'不显示'),
    					array('value'=>'1','content'=>'显示'),
    			)),
    	
    			array('title'=>"参数详情",'type'=>"textarea",'name'=>"content",'value'=>'content'),
    		
    	),U('Hyzx/jf'),array($this,'bbc'));
    }
    
    
    
    
    
    public function jfadd_pinzhong(){
    	$this->jfaddEdit(add);
    
    }
    /**
     * 编辑品牌    hidden
     */
    public function jfPinzhongEdit(){
    	$this->jfaddEdit(Edit);
    }
    /**
     *  删除品种
     **/
    public function jfPinzhongDel(){
    	M('mru_jfxx')->where(array('uid'=>$_GET['id']))->delete();
    	$this->del('mru_jf');
    }
    
    public function xx(){
    	
    	
    	$aWhere['token']=$_SESSION['token'];
    	$aWhere['uid']=$_GET['id'];
    	     	//搜索
    	 if(IS_POST){
	    	$_POST=$_REQUEST;
	    	$aWhere=$this->search($_POST);
	    	    	$aWhere['token']=$_SESSION['token'];
    				$aWhere['uid']=$_GET['id'];
    				//P($aWhere);die;
	    	//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    	}//结束
    	
    	
    	$this->table(
    			
    			
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('Hyzx/jf', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '返回',//2级
    									'url'    => U('Hyzx/jf')
    							)
    								
    								
    
    					),
    					'tips' => array(//3级
    							'你可以在这里管理积分兑换信息'
    					),
    					'Table_Header' => array(//4级
    							'ID', '积分物品名', '会员id', '兑换时间','操作'
    					),
    					'List_Opt' => array(
    								

     							array(
    									'name' => '该会员信息',
    									'url'  => U('Dianphd/ckGr',array('model'=>'mru_jfxx','url'=>'Hyzx/xx','uid'=>$_GET['id']))
    							), 
    							array(
    									'name' => '删除',
    									'url'  => U('Hyzx/xxDel')
    							),
    					),
    					
    					
    					 'search'=>array(
    					 		//array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
    					 		//array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
    					 		array('title'=>'况换时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
    					 )//结束 
    			),
    			M('mru_jfxx')->where($aWhere)->count(),
    			M('mru_jfxx')->field('id,uid,openid,add_time')->where($aWhere),
    			array($this,'ddabc')
    	);
    }
    
    
    public function ddabc($data){
    	foreach($data as $k=>$v){
    		$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
    		$data[$k]['uid']=M('mru_jf')->where(array('id'=>$v['uid']))->getField('name');
    			
    	}
    	return $data;
    }
   
    /**
     * 编辑品牌    hidden
     */
    public function xxEdit(){
    	$this->jfaddEdit2(Edit);
    }
    
    
    public function jfaddEdit2($aaa){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('Hyzx2')->select();//被添加内容的表
    	 foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('mru_jfxx',array(
    			 
    			array('title'=>"标题",'type'=>"input",'name'=>"dz",'value'=>'dz'),
    	
    
    	),U('Hyzx/xx',array('token'=>$_SESSION['token'],'id'=>$_GET['uid'])),array($this,'bbc'),1);
    }
    /**
     *  删除品种
     **/
    public function xxDel(){
    	$this->del('mru_jfxx');
    }
    
    //排序  只 能主健是id,子段是sort 才能排序  有意见自己去开发！
    public function sortajax(){
    	$this->sortajaxTable('mru_jf');
    }

}
?>
