<?php
/**
 *  银波米业
 **/
class QianggouAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/together';

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
        $this->pz	   = D('Mru_qianggou');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    
    protected function setHeader(){
    	$aid=$_GET['aid'];
    	if($aid){
    		return array(
    				array(
    						'name' => '预约管理',
    						'url'  => U('Dianphd/index', array('token' => $this->_sToken,'aid'=>$aid))
    				),

    				array(
    						'name' => '限制抢购',
    						'url'  => U('Qianggou/index', array('token' => $this->_sToken,'aid'=>$aid))
    				),
    				
    				array(
    						'name' => '最新活动',
    						'url'  => U('Huodong/index', array('token' => $this->_sToken,'aid'=>$aid))
    				),
    				
    			
    		);
    	}else{
    		return include "./Lib/Action/User/Mru_top.php";
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
		
		$aid=$_GET['aid'];
		$token=session(token);
		
		if($aid){
			
		 	
			$aWhere = array('token' => $this->_sToken);
			$list=M('mru_qianggou')->where(array('token'=>$token,'aid'=>$aid))->field('id,k_time,j_time')->select();
			//print_r($list);
			foreach ($list as $v){
				$k_time=strtotime($v['k_time']);
				$j_time=strtotime($v['j_time']);
				$time=time();
				if($k_time<$time && $time<$j_time ){
					M('mru_qianggou')->where(array('id'=>$v['id']))->setField('state',1);
				}else{
					M('mru_qianggou')->where(array('id'=>$v['id']))->setField('state',0);
				}
					
			}
			
		  
			$this->table(
					array(
							'abc'=>123,
							//'id' => 'id',//如果主键不是id，则需要设置
							'HeadHover' => U('Qianggou/index', array('token' => $this->_sToken)),
							'Head_Opt' => array(
									array(
											'name'   => '添加限时抢购活动',
											'url'    => U('Qianggou/add_pinzhong',array('aid'=>$aid))
									)
										
							),
							'tips' => array(
									'你可以在这里管理限时抢购活动信息'
							),
							'Table_Header' => array(
									'ID', '活动标题', '活动图片','活动开始时间','活动结束时间','抢购人数','邀请积分','邀请红包' ,'活动状态','审核状态','排序','操作'
							),
							'List_Opt' => array(
									array(
											'name' => '编辑',
											'url'  => U('Qianggou/PinzhongEdit',array('aid'=>$aid))
									),
									array(
											'name' => '删除',
											'url'  => U('Qianggou/PinzhongDel')
									),
							)
					),
					$this->pz->where(array('token'=>$token,'aid'=>$aid))->count(),
					$this->pz->field('id,title,pic,k_time,j_time,num,jf,hongbao,state,state2,sort')->order('sort')->where(array('token'=>$token,'aid'=>$aid)),
					array($this,'abcd')
			
			);
			
		}else{
			
			$aWhere = array('token' => $this->_sToken);
			$list=M('mru_qianggou')->where($aWhere)->field('id,k_time,j_time')->select();
			//print_r($list);
			foreach ($list as $v){
				$k_time=strtotime($v['k_time']);
				$j_time=strtotime($v['j_time']);
				$time=time();
				if($k_time<$time && $time<$j_time ){
					M('mru_qianggou')->where(array('id'=>$v['id']))->setField('state',1);
				}else{
					M('mru_qianggou')->where(array('id'=>$v['id']))->setField('state',0);
				}
					
			}
			$this->table(
					array(
							'abc'=>123,
							//'id' => 'id',//如果主键不是id，则需要设置
							'HeadHover' => U('Qianggou/index', array('token' => $this->_sToken)),
							'Head_Opt' => array(
									array(
											'name'   => '添加限时抢购活动',
											'url'    => U('Qianggou/add_pinzhong')
									),
									array(
											'name' => '店铺限时抢购审核管理',
											'url'  => U('Qianggou/index2', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
									)
							),
							'tips' => array(
									'你可以在这里管理限时抢购活动信息'
							),
							'Table_Header' => array(
									'ID', '活动标题', '活动图片','活动开始时间','活动结束时间','抢购人数','邀请积分','邀请红包' ,'排序','操作'
							),
							'List_Opt' => array(
									array(
											'name' => '编辑',
											'url'  => U('Qianggou/PinzhongEdit')
									),
									array(
											'name' => '删除',
											'url'  => U('Qianggou/PinzhongDel')
									),
							)
					),
					$this->pz->where(array('token'=>$token,'aid'=>0))->count(),
					$this->pz->field('id,title,pic,k_time,j_time,num,jf,hongbao,sort')->order('sort')->where(array('token'=>$token,'aid'=>0)),
					array($this,'abc')
			
			);
			
		}
		
	}
	
	
	
	public function index2(){
		$aid=$_GET['aid'];
		$token=session(token);
		

				
		
				
			$aWhere = array('token' => $this->_sToken);
			$list=M('mru_qianggou')->where($aWhere)->field('id,k_time,j_time')->select();
			//print_r($list);
			foreach ($list as $v){
				$k_time=strtotime($v['k_time']);
				$j_time=strtotime($v['j_time']);
				$time=time();
				if($k_time<$time && $time<$j_time ){
					M('mru_qianggou')->where(array('id'=>$v['id']))->setField('state',1);
				}else{
					M('mru_qianggou')->where(array('id'=>$v['id']))->setField('state',0);
				}
					
			}
			$this->table(
					array(
							'abc'=>123,
							//'id' => 'id',//如果主键不是id，则需要设置
							'HeadHover' => U('Qianggou/index', array('token' => $this->_sToken)),
							'Head_Opt' => array(
									array(
											'name'   => '添加限时抢购活动',
											'url'    => U('Qianggou/add_pinzhong')
									),
									array(
											'name' => '店铺限时抢购审核管理',
											'url'  => U('Qianggou/index2', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
									)
							),
							'tips' => array(
									'你可以在这里管理限时抢购活动信息'
							),
							'Table_Header' => array(
									'ID','店铺', '活动标题', '活动图片','活动开始时间','活动结束时间','邀请积分','邀请红包' ,'审核状态','排序','操作'
							),
							'List_Opt' => array(
									array(
											'name' => '审核',
											'url'  => U('Qianggou/sh')
									),
									array(
											'name' => '取消审核',
											'url'  => U('Qianggou/sh2')
									),
							)
					),
					$this->pz->where(array('token'=>$token))->count(),
					$this->pz->field('id,aid,title,pic,k_time,j_time,jf,hongbao,state2,sort')->order("sort")->where(array('token'=>$token,'aid'=>array('neq',0))),
					array($this,'abcd')
						
			);
				
	
	
	}
	

	public function abc($data){
		foreach($data as $k=>$v){
	
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
		
			
		
			$data[$k]['hongbao']=$v['hongbao']?$v['hongbao']."元":'';
			/* 	$data[$k]['content']=substr(htmlspecialchars_decode($v['content']),0,78); */
		}
		return $data;
	}
	
	
	public function abcd($data){
		foreach($data as $k=>$v){
	        
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
			//$data[$k]['state']="{$v['state']}"?'活动已开启':'活动关闭';
			$data[$k]['state2']="{$v['state2']}"?'已审核':'未审核';
			
			$data[$k]['aid']=M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name');
		
		}
		return $data;
	}
	
	public function sh(){
		M('mru_qianggou')->where(array('id'=>$_GET['id']))->save(array('state2'=>1));
		$this->redirect('index2');
	}
	
	public function sh2(){
		M('mru_qianggou')->where(array('id'=>$_GET['id']))->save(array('state2'=>0));
		$this->redirect('index2');
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
    /**
     *  添加-品种
     * 如果写了msg代表此字段为必填，值为错误提示内容
     * array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y')   这是添加地图
     **/
    public function add_pinzhong(){
    	$aid=$_GET['aid'];
    	 
    	//P($_POST);die;
    	
    	if($aid){
    		 
    		 
    		//特殊点选框,复选框,下拉列表
    		/* 	$list=M('mru_mdian')->select();//被添加内容的表
    		 foreach ($list as $k=>$v){
    		$list[$k]['content']=$v['name'];//把内容子段改成content
    		$list[$k]['value']=$v['id'];//把id子段改成value
    		unset($list[$k]['name']);//删除原来的内容子段
    		} */
    		$this->add('Mru_qianggou',array(
    				 
    				array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'请填写标题咯','value'=>'title'),
    				array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
    				array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
    				array('title'=>"原价",'type'=>"number",'name'=>"price2",'msg'=>'','value'=>'price2'),
    				array('title'=>"特价",'type'=>"number",'name'=>"price3",'msg'=>'','value'=>'price3'),
    				array('title'=>"抢购人数",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
    				
    				
    				array('title'=>"设置分享积分",'type'=>"number",'name'=>"jf",'value'=>'jf'),
    				array('title'=>"设置激请好友奖励红包",'type'=>"number",'name'=>"hongbao",'value'=>'hongbao'),
    				
    				array('type'=>'select','title'=>"类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
    					array('value'=>'1', 'content'=>'减肥'),
    					array('value'=>'2','content'=>'美容'),
    					array('value'=>'3','content'=>'亚健康'),
    					array('value'=>'4','content'=>'其它'),
    			     )),
    				
    				//array('title'=>"限定次数",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
    				//array('title'=>"激请积分",'type'=>"number",'name'=>"jf",'msg'=>'','value'=>'jf'),
    				//array('title'=>"激请红包",'type'=>"number",'name'=>"price",'msg'=>'','value'=>'price'),
    				//array('type'=>'special','title'=>"参与门店",'name'=>'mdiao','many'=>$str),
    				//array('type'=>'checkbox','title'=>'特殊复选框', 'name'=>'mdiao', 'value'=>'mdiao','msg'=>'请填写标题咯','many'=>$list),
    				 
    				   
    	
    	  
    				/*      array('title'=>"密码",'type'=>"password",'name'=>"pwd"), */
    				array('type'=>'img','many'=>array(
    						array('title'=>"图片",'type'=>"img",'name'=>"pic"),
    						/*            array('title'=>"图片2",'type'=>"img",'name'=>"pic2"),
    						 array('title'=>"图片2",'type'=>"img",'name'=>"pic3") */
    		)),
    				array('title'=>"图文详细",'type'=>"textarea",'name'=>"content")
    				   //  array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y')
    		),U('Qianggou/index',array('aid'=>$aid)));
    		 
    	}else{
    		 
    		 
    		//特殊点选框,复选框,下拉列表
    		$list=M('mru_mdian')->where(array('token'=>$_SESSION['token']))->select();//被添加内容的表 'checked'=>1
    		
    		foreach ($list as $k=>$v){
    			$list[$k]['content']=$v['name'];//把内容子段改成content
    			$list[$k]['value']=$v['id'];//把id子段改成value
    			$list[$k]['checked']=1;
    			unset($list[$k]['name']);//删除原来的内容子段
    		}
    		
    		$this->add('Mru_qianggou',array(
    				 
    				array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'请填写标题咯','value'=>'title'),
    				array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
    				array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
    				array('title'=>"原价",'type'=>"number",'name'=>"price2",'msg'=>'','value'=>'price2'),
    				array('title'=>"特价",'type'=>"number",'name'=>"price3",'msg'=>'','value'=>'price3'),
    				
    				array('title'=>"抢购人数",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
    			/* 	array('title'=>"抢购卷价格",'type'=>"number",'name'=>"qgj",'value'=>'qgj'), */
    				array('title'=>"设置分享积分",'type'=>"number",'name'=>"jf",'msg'=>'','value'=>'jf'),
    				array('title'=>"设置激请好友奖励红包",'type'=>"number",'name'=>"hongbao",'msg'=>'','value'=>'hongbao'),
    				
    				array('type'=>'select','title'=>"类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
    						array('value'=>'1', 'content'=>'减肥'),
    						array('value'=>'2','content'=>'美容'),
    						array('value'=>'3','content'=>'亚健康'),
    						array('value'=>'4','content'=>'其它'),
    				)),
    				//array('title'=>"经纬度",'type'=>"map",'name'=>'name', 'lng'=>"position_x",'lat'=>'position_y'),
    		
    		         //array('title'=>"限定次数",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
    				//array('title'=>"激请积分",'type'=>"number",'name'=>"jf",'msg'=>'','value'=>'jf'),
    				//array('title'=>"激请红包",'type'=>"number",'name'=>"price",'msg'=>'','value'=>'price'),
    				//array('type'=>'special','title'=>"参与门店",'name'=>'mdiao','many'=>$str),
    				array('type'=>'checkbox','title'=>'参与门店','全选'=>1, 'name'=>'mdiao', 'value'=>'mdiao','msg'=>'请填写标题咯','many'=>$list),
    				 
    				 
    	
    				 
    				/*      array('title'=>"密码",'type'=>"password",'name'=>"pwd"), */
    				array('type'=>'img','many'=>array(
    						array('title'=>"图片",'type'=>"img",'name'=>"pic"),
    						/*            array('title'=>"图片2",'type'=>"img",'name'=>"pic2"),
    						 array('title'=>"图片2",'type'=>"img",'name'=>"pic3") */
    				)),
    				array('title'=>"图文详细",'type'=>"textarea",'name'=>"content")
    				/*      array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y') */
    		),U('Qianggou/index',array('aid'=>$aid)),array($this,'bbc'));
    		 
    	}
    	

    }
    
    
    public function bbc($data){
    
    	$data['state2']=1;
    	return $data;
    }


    /**
     * 编辑品牌
     */
    public function PinzhongEdit(){
    	
    $aid=$_GET['aid'];
    	
        if($aid){
        	
        	
        	//特殊点选框,复选框,下拉列表
        /* 	$list=M('mru_mdian')->select();//被添加内容的表
        	foreach ($list as $k=>$v){
        		$list[$k]['content']=$v['name'];//把内容子段改成content
        		$list[$k]['value']=$v['id'];//把id子段改成value
        		unset($list[$k]['name']);//删除原来的内容子段
        	} */
        	$this->Edit('Mru_qianggou',array(
        	
        			array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'请填写标题咯','value'=>'title'),
        			array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
        			array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
        			array('title'=>"原价",'type'=>"number",'name'=>"price2",'msg'=>'','value'=>'price2'),
        			array('title'=>"特价",'type'=>"number",'name'=>"price3",'msg'=>'','value'=>'price3'),
        			array('title'=>"抢购人数",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
        			array('title'=>"设置分享积分",'type'=>"number",'name'=>"jf",'msg'=>'','value'=>'jf'),
    				array('title'=>"设置激请好友奖励红包",'type'=>"number",'name'=>"hongbao",'msg'=>'','value'=>'hongbao'),
        			
        			array('type'=>'select','title'=>"类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
        					array('value'=>'1', 'content'=>'减肥'),
        					array('value'=>'2','content'=>'美容'),
        					array('value'=>'3','content'=>'亚健康'),
        					array('value'=>'4','content'=>'其它'),
        			)),
        	
        	 //array('title'=>"限定次数",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
        			//array('title'=>"激请积分",'type'=>"number",'name'=>"jf",'msg'=>'','value'=>'jf'),
        			//array('title'=>"激请红包",'type'=>"number",'name'=>"price",'msg'=>'','value'=>'price'),
        			//array('type'=>'special','title'=>"参与门店",'name'=>'mdiao','many'=>$str),
        			//array('type'=>'checkbox','title'=>'特殊复选框', 'name'=>'mdiao', 'value'=>'mdiao','msg'=>'请填写标题咯','many'=>$list),
        	
        	
        			 
        	
        			/*      array('title'=>"密码",'type'=>"password",'name'=>"pwd"), */
        			array('type'=>'img','many'=>array(
        					array('title'=>"图片",'type'=>"img",'name'=>"pic"),
        					/*            array('title'=>"图片2",'type'=>"img",'name'=>"pic2"),
        					 array('title'=>"图片2",'type'=>"img",'name'=>"pic3") */
        			)),
        			array('title'=>"图文详细",'type'=>"textarea",'name'=>"content")
        			/*      array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y') */
        	),U('Qianggou/index',array('aid'=>$aid)));
        	
        }else{
        	
        	
        	//特殊点选框,复选框,下拉列表
        	$list=M('mru_mdian')->select();//被添加内容的表
        	foreach ($list as $k=>$v){
        		$list[$k]['content']=$v['name'];//把内容子段改成content
        		$list[$k]['value']=$v['id'];//把id子段改成value
        		unset($list[$k]['name']);//删除原来的内容子段
        	}
        	$this->Edit('Mru_qianggou',array(
        	
        			array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'请填写标题咯','value'=>'title'),
        			array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
        			array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
        			array('title'=>"原价",'type'=>"number",'name'=>"price2",'msg'=>'','value'=>'price2'),
        			array('title'=>"特价",'type'=>"number",'name'=>"price3",'msg'=>'','value'=>'price3'),
        			
        			array('title'=>"抢购人数",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
      /*   			array('title'=>"抢购卷价格",'type'=>"number",'name'=>"qgj",'value'=>'qgj'), */
        			array('title'=>"设置分享积分",'type'=>"number",'name'=>"jf",'msg'=>'','value'=>'jf'),
    				array('title'=>"设置激请好友奖励红包",'type'=>"number",'name'=>"hongbao",'msg'=>'','value'=>'hongbao'),

        			array('type'=>'select','title'=>"类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
        					array('value'=>'1', 'content'=>'减肥'),
        					array('value'=>'2','content'=>'美容'),
        					array('value'=>'3','content'=>'亚健康'),
        					array('value'=>'4','content'=>'其它'),
        			)),
        	
        	//array('title'=>"限定次数",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
        			//array('title'=>"激请积分",'type'=>"number",'name'=>"jf",'msg'=>'','value'=>'jf'),
        			//array('title'=>"激请红包",'type'=>"number",'name'=>"price",'msg'=>'','value'=>'price'),
        			//array('type'=>'special','title'=>"参与门店",'name'=>'mdiao','many'=>$str),
        			array('type'=>'checkbox','title'=>'参与门店','全选'=>1, 'name'=>'mdiao', 'value'=>'mdiao','msg'=>'请填写标题咯','many'=>$list),
        	
        	
        			 
        	
        			/*      array('title'=>"密码",'type'=>"password",'name'=>"pwd"), */
        			array('type'=>'img','many'=>array(
        					array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>"pic"),
        					/*            array('title'=>"图片2",'type'=>"img",'name'=>"pic2"),
        					 array('title'=>"图片2",'type'=>"img",'name'=>"pic3") */
        			)),
        			array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>"content")
        			/*      array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y') */
        	),U('Qianggou/index'));
        	
        }

    }

    /**
     *  删除品种
     **/
    public function PinzhongDel(){
        $this->del('Mru_qianggou');
    }
    
    
    //排序  只 能主健是id,子段是sort 才能排序  有意见自己去开发！
    public function sortajax(){
    	$this->sortajaxTable('Mru_qianggou');
    }
}
?>
