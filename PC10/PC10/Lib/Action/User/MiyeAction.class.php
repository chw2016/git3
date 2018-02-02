<?php
/**
 *  银波米业
 **/
class MiyeAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/miye';

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
        $this->pz	   = D('Miye_pinzhong');
        $this->xqu	   = D('Miye_xiaoqu');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    
    protected function setHeader(){
    	return array(
                array(
                    'name' => '品种',
                    'url'  => U('Miye/index', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '门店管理',
                    'url'  => U('Miye/dianpu', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '订单管理',
                    'url'  => U('Miye/order', array('token' => $this->_sToken))
                ),
                array(
                    'name' => 'logo管理',
                    'url'  => U('Miye/uploadIcon', array('token' => $this->_sToken))
                ),
                array(
    					'name' => '社区管理',
    					'url'  => U('Miye/sequ', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '区域管理',
    					'url'  => U('Miye/wm', array('token' => $this->_sToken))
    			),
            );
    }


    	/**
	 *  社区管理
	 **/
	public function sequ(){
	
		$aWhere = array('token' => $this->_sToken);
	
		$this->table(
				array(
						//'id' => 'id',//如果主键不是id，则需要设置
						'HeadHover' => U('Miye/sequ', array('token' => $this->_sToken)),
						'Head_Opt' => array(
								array(
										'name'   => '添加小区',
										'url'    => U('Miye/xiaoquAdd')
								)
						),
						'tips' => array(
								'你可以在这里管理品种信息'
						),
						'Table_Header' => array(
								'ID', '品种名称', '添加时间', '操作'
						),
						'List_Opt' => array(
								array(
										'name' => '编辑',
										'url'  => U('Miye/xiaoquEdit')
								),
								array(
										'name' => '删除',
										'url'  => U('Miye/xiaoquDel')
								),
						)
				),
				$this->xqu->where($aWhere)->count(),
				$this->xqu->field('id,xiaoqu,add_time')->where($aWhere)
			
		);
	
	}


    /**
	 *  添加-小区
	 **/
	public function xiaoquAdd(){
		if(IS_POST){
			$Model       = $this->xqu;
			$_POST['token']= $this->_sToken;
		
				
			if(($aData = $Model->create()) != false){
				if($Model->add()){
					$this->success2('小区添加成功',U('Miye/sequ'));
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
									'url'  => U('Miye/sequ', array('token' => $this->_sToken)),
									'name' => '返回'
							)
					),
					'FormUrl'  => U('Miye/xiaoquAdd',array('token'=>$_SESSION['token']))
			));
			$this->UDisplay('xiaoquAdd_add');
		}
	}
   



    	
	//修改小区
	public function xiaoquEdit(){
	 
		if(IS_POST){
			$_POST['id']    = FC::P('id');
			$_POST['token'] = $this->_sToken;
			$aWhere         = array(
					'id'    => FC::P('id'),
					'token' => FC::P('token')
			);
			$Model = $this->xqu;
			$Item    = $Model->where($aWhere)->find();
			if($Item == false) $this->error2('非法操作');
			if($Model->create()){
				if($iID = $Model->where($aWhere)->save($_POST)){
					$this->success2('修改成功',U('Miye/sequ', array('token' => $this->token)));
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($Model->getError());
			}
		}else{
			$iID     = $this->_get('id');
			$xqu		 = $this->xqu->where(array(
					'id' => $iID,
					'token' => $this->_sToken
			))->find();
	
			if($xqu == false) $this->error2('非法操作');
			$this->assign(array(
					'xiaoqu' => $xqu,
					'id' => $iID,
					'FormUrl' => U('Miye/xiaoquEdit',array('token'=>$_SESSION['token'])),
					'ExtraBtn' => array(
							array(
									'url'  => U('Miye/sequ', array('token' => $this->_sToken)),
									'name' => '返回'
							)
					),
			));
			$this->UDisplay('xiaoquAdd_add2');
		}
	}
	
    /**
	 *  删除小区
	 **/
	public function xiaoquDel(){
		$iID     = $this->_get('id');
		$aWhere  = array('id'=>$iID,'token' => $this->_sToken);
		$Product = D('Miye_xiaoqu');
		$bCheck  = $Product->where($aWhere)->find();
		if($bCheck == false) $this->error2('非法操作');
		if($Product->where($aWhere)->delete()) {
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
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
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('Miye/index', array('token' => $this->_sToken)),
            	'Head_Opt' => array(
                	array(
                    	'name'   => '添加品种',
                    	'url'    => U('Miye/PinzhongAdd')
                	)
            	),
                'tips' => array(
                    '你可以在这里管理品种信息'
                ),
            	'Table_Header' => array(
                	'ID', '品种名称', '添加时间', '操作'
            	),
            	'List_Opt' => array(
                	array(
                    	'name' => '编辑',
                    	'url'  => U('Miye/PinzhongEdit')
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('Miye/PinzhongDel')
	                ),
            	)
        	), 
        	$this->pz->where($aWhere)->count(),
        	$this->pz->field('id,pinzhong,add_time')->where($aWhere)
         );
	}

    /**
     *  添加-品种
     **/
	public function PinzhongAdd(){
		if(IS_POST){
			$Model       = $this->pz;
			$_POST['token']= $this->_sToken;
			if(($aData = $Model->create()) != false){
				if($Model->add()){
					$this->success2('产品添加成功',U('Miye/index'));
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
                        'url'  => U('Miye/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
                'FormUrl'  => U('Miye/PinzhongAdd',array('token'=>$_SESSION['token']))
            ));
            $this->UDisplay('pinzhong_add');
		}
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

	public function PinzhongEdit(){
		if(IS_POST){
			$_POST['id']    = FC::P('id');
			$_POST['token'] = $this->_sToken;
			$aWhere         = array(
                'id'    => FC::P('id'),
                'token' => FC::P('token')
            );
            $Model = $this->pz;
			$Item    = $Model->where($aWhere)->find();
			if($Item == false) $this->error2('非法操作');
			if($Model->create()){
				if($iID = $Model->where($aWhere)->save($_POST)){
					$this->success2('修改成功',U('Miye/index', array('token' => $this->token)));
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($Model->getError());
			}
		}else{
			$iID     = $this->_get('id');
			$PZ		 = $this->pz->where(array(
                'id' => $iID,
                'token' => $this->_sToken
            ))->find();
            
			if($PZ == false) $this->error2('非法操作');
			$this->assign(array(
                'pinzhong' => $PZ,
                'id' => $iID,
                'FormUrl' => U('Miye/PinzhongEdit',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Miye/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
            ));
            $this->UDisplay('pinzhong_add');
		}
	}

    /**
     *  删除品种
     **/
	public function PinzhongDel(){
		$iID     = $this->_get('id');
		$aWhere  = array('id'=>$iID,'token' => $this->_sToken);
		$Product = D('Miye_pinzhong');
		$bCheck  = $Product->where($aWhere)->find();
		if($bCheck == false) $this->error2('非法操作');
		if($Product->where($aWhere)->delete()) {
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
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
                    'url'    => U('Miye/dianpuAdd')
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
	
//wm开始	

	public function wm(){
	
		$token=$_SESSION['token'];
		/*     	//搜索
		 if(IS_POST){
		$_POST=$_REQUEST;
		$aWhere=$this->search($_POST);
		$aWhere['token'] =$_SESSION['token'];
		//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
		}//结束 */
		$this->table(
				array(
						'abc'=>123,
						//'id' => 'id',//如果主键不是id，则需要设置
						'HeadHover' => U('Miye/wm', array('token' => $this->_sToken)),//栏目样式
						'Head_Opt' => array(
								array(
										'name'   => '添加',//2级
										'url'    => U('Miye/add_wm',array('token'=>$token))
								)
						),
						'tips' => array(//3级
								'你可以在这里管理信息'
						),
						'Table_Header' => array(//4级
								'ID', '名称', '纬度','经度','操作'
						),
						'List_Opt' => array(
	
						/*              			array(
						 'name' => '其它信息',
								'url'  => U('Miye/xx',array('token'=>$_SESSION['token']))
						), */
								 
								array(
										'name' => '编辑',
										'url'  => U('Miye/wmEdit',array('token'=>$token))
								),
								array(
										'name' => '删除',
										'url'  => U('Miye/wmDel',array('token'=>$token))
								),
						),
						/*         		//搜索
						 'search'=>array(
						 		array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
						 		array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
						 		array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
						 )//结束 */
				),
				M('Miye_wm')->where(array('token'=>$token))->count(),
				M('Miye_wm')->field('id,name,position_y,position_x')->where(array('token'=>$token))->order("id desc")
				// array($this,'abc')
		);
	}
	
	
	public function wmaddEdit($aaa){
		//特殊点选框,复选框,下拉列表
		/* 		$list=M('Miye2')->select();//被添加内容的表
			foreach ($list as $k=>$v){
		$list[$k]['content']=$v['name'];//把内容子段改成content
		$list[$k]['value']=$v['id'];//把id子段改成value
		unset($list[$k]['name']);//删除原来的内容子段
		} */
		$this->$aaa('Miye_wm',array(
				
				array('title'=>"名称",'type'=>"input",'name'=>"name",'value'=>'name'),
			
				array('title'=>"经纬度",'type'=>"map",'name'=>'address', 'lng'=>"position_x",'lat'=>'position_y'),
		),U('Miye/wm',array('token'=>$_SESSION['token'])),array($this,'bbc'));
	
	}
	public function bbc($data){
		$data['password']=MD5($data['password']);
		$data['sex']=1;
		return $data;
	}
	//添加
	public function add_wm(){
		$this->wmaddEdit(add);
	}
	//编辑
	public function wmEdit(){
		$this->wmaddEdit(Edit);
	}
	//删除
	public function wmDel(){
		//M('Miye_pinzhong')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
		$this->del('Miye_wm');
	}
	
	
//wm结束	
}
?>
