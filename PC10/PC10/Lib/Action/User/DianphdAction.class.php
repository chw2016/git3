<?php
/**
 *  银波米业1
 **/
class DianphdAction extends TableAction {
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
 
        $this->pz	   = D('mru_dianphd');
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
		


		//P($_SESSION);die;
/* 		$calc=new Calendar();
		
		$time_info=$calc->showCalendar($list,$id); */
		
		
		//$this->assign('time_info',$time_info);
		// $this->assign('shoufu',$shoufu);
		
		
	  
		$aid=$_SESSION['aid'];
		//echo print_r($_SESSION);die;
	
/* 		if($_GET['addid'] || $_GET['editid'] ){
			$audit=M('mru_dianphd')->where(array('id'=>$_GET['asid']))->getField('audit');
			if($audit==0){
				echo "<script>alert('店铺预约未审核！请联系总公司审核,否则无效');</script>";
			}
			//$this->success2('111',U('Dianphd/index'));
		} */
		
		if($_GET['editid']){
			//日志
			$list=M('mru_dianphd')->where(array('id'=>$_GET['editid']))->find();
			if($list) M('mru_rz')->add(array(
					'name'=>$list['dname'],
					'pic'=>$list['pic'],
					'content'=>$list['content'],
					'state'=>$list['state'],
					'aid'=>$_SESSION['aid'],
					'hd_time'=>$list['hd_time'],
					'token'=>$_SESSION['token'],
					'add_time'=>time(),
					'sid'=>$list['id'],//物品编号
					'type'=>'修改预约'
			));
	   }
		
		if($_GET['addid']){
			
			//日志
			$list=M('mru_dianphd')->where(array('id'=>$_GET['addid']))->find();
			if($list) M('mru_rz')->add(array(
					'name'=>$list['dname'],
					'pic'=>$list['pic'],
					'content'=>$list['content'],
					'state'=>$list['state'],
					'aid'=>$_SESSION['aid'],
					'hd_time'=>$list['hd_time'],
					'token'=>$_SESSION['token'],
					'add_time'=>time(),
					'sid'=>$list['id'],//物品编号
					'type'=>'添加预约'
			));
			
			$aid=$_GET['aid'];
		    M('mru_yd')->add(array('pid'=>$_GET['addid']));
		    M('mru_yd2')->add(array('pid'=>$_GET['addid']));
		    $this->redirect('index',array('aid'=>$aid));
		}
		
	    $state=M('mru_dianphd')->where(array('aid'=>$aid,'token' => $this->_sToken))->field('id,state,audit')->select();
	   // print_r($state);die;
/* 	    echo "<pre>";
	    print_r($state);die; */
	    
	    foreach ($state  as  $v){
	     if($v['audit']==1){	
	    	if($v['state']==1){
	    		$hd_time=M('mru_dianphd')->where(array('id'=>$v['id']))->field('hd_time')->find();
	    		$hd_time=explode(',', $hd_time['hd_time']);
	    	  	foreach ($hd_time as $s){
	    			$s=explode('-', $s);
	    			$date=date("H:i");
	    			$date=strtotime($date);
	    			$s['0']=strtotime($s['0']);
	    			$s['1']=strtotime($s['1']);
	    			if($date>=$s['0']  && $date<=$s['1']){
	    				M('mru_dianphd')->where(array('id'=>$v['id']))->setField('state2',1);
	    				break;
	    			}else{
	    				M('mru_dianphd')->where(array('id'=>$v['id']))->setField('state2',0);
	    			}
	    		}
	    	}else{
	    		M('mru_dianphd')->where(array('id'=>$v['id']))->setField('state2',0);
	    	}
	      }else{
	      	//M('mru_dianphd')->where(array('id'=>$v['id']))->setField('state',0);
	      }	
	     }
	    
		$aWhere = array('token' => $this->_sToken);
		$this->table(
        	array(
                'abc'=>123,
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('Dianphd/index', array('token' => $this->_sToken)),//栏目样式 
            	'Head_Opt' => array(
                	array(
                    	'name'   => '添加预约',//2级
                    	'url'    => U('Dianphd/add_pinzhong',array('aid'=>$_GET['aid'],'modulename'=>$_GET['modulename'],'token'=>$_GET['token']))
                	)
            	),
                'tips' => array(//3级
                    '你可以在这里管理品种信息'
                ),
            	'Table_Header' => array(//4级
                	'ID', '店铺预约名称', '店铺预约图片'  ,'店铺预约类型' ,'是否开启','预约状态','审核状态','排序', '操作'
            	),
            	'List_Opt' => array(
            		array(
            					'name' => '设置预约人数',
            					'url'  => U('Mdian/index2',array('aid'=>$_GET['aid'],'modulename'=>$_GET['modulename'],'token'=>$_GET['token']))
            		),
/*             		array(
            					'name' => '查看预约信息',
            					'url'  => U('Dianphd/ck',array('aid'=>$_GET['aid'],'modulename'=>$_GET['modulename'],'token'=>$_GET['token']))
            		), */
            		
                	array(
                    	'name' => '编辑',
                    	'url'  => U('Dianphd/PinzhongEdit',array('aid'=>$_GET['aid'],'modulename'=>$_GET['modulename'],'token'=>$_GET['token']))
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('Dianphd/PinzhongDel',array('aid'=>$_GET['aid'],'modulename'=>$_GET['modulename'],'token'=>$_GET['token']))
	                ),
            	)
        	), 
        	$this->pz->where(array('aid'=>$aid))->count(),
        	$this->pz->field('id,dname,pic,type,state,state2,audit,sort')->where(array('aid'=>$aid,'token'=>$_SESSION['token']))->order("add_time desc"),
            array($this,'abc')
         );
	}
	
	//排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
	public function sortajax(){
		$this->sortajaxTable('mru_dianphd');
	}

	public function abc($data){
		foreach($data as $k=>$v){
			
			switch ($v['audit']){
				case 0:$data[$k]['audit']='未审核';break;
				case 1:$data[$k]['audit']='已审核';break;
			}

			
			switch ($v['state2']){
				case 0:$data[$k]['state2']='预约关闭中';break;
				case 1:$data[$k]['state2']='预约开启中';break;
			}
			

			
		
			
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
		
			$data[$k]['state']="{$v['state']}"?'开启预约':'关闭预约';
	

			
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
				'FormUrl' => U('Miye/dianpuAdd',array('token'=>$_SESSION['token'],'modulename'=>$_GET['modulename'])),
	            'ExtraBtn' => array(
	                array(
	                    'url'  => U('Miye/dianpu', array('token' => $this->_sToken,'modulename'=>$_GET['modulename'])),
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
                        'url'  => U('Miye/dianpu', array('token' => $this->_sToken,'modulename'=>$_GET['modulename'],'aid'=>$_GET['aid'])),
                        'name' => '返回'
                )),
                'FormUrl' => U('Miye/dianpuAdd',array('token'=>$_SESSION['token'],'modulename'=>$_GET['modulename'],'aid'=>$_GET['aid']))
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
			->join('tp_mru_dianphd pz on pz.id = o.pinzhong_id')
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
			->join('tp_mru_dianphd pz on pz.id = o.pinzhong_id')
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
        		'下单时间'
            ), array_values($aInfo[0]))
        ));
        $this->UDisplay('order_show');
	}
	//定义增改函数
	public function addEdit($aaa){
		//特殊点选框,复选框,下拉列表
/* 		$list=M('Dianphd2')->select();//被添加内容的表
		foreach ($list as $k=>$v){
			$list[$k]['content']=$v['name'];//把内容子段改成content
			$list[$k]['value']=$v['id'];//把id子段改成value
			unset($list[$k]['name']);//删除原来的内容子段
    	} */
		
	
		
		$this->$aaa('mru_dianphd',array(
				
				array('title'=>"店铺预约名称",'type'=>"input",'name'=>"dname",'value'=>'dname'),
		
				array('type'=>'select','title'=>"店铺预约类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
						array( 'content'=>'请选择预约类型'),
						array('value'=>'项目预约', '选中'=>1, 'content'=>'项目预约'),
						array('value'=>'免费体验预约', 'content'=>'体验预约'),
						array('value'=>'抢购预约','content'=>'抢购预约'),
						
						array('value'=>'团购网预约','content'=>'团购网预约'),
				)),
				array('type'=>'select','title'=>"是否开启",'name'=>"state",'value'=>'state','msg'=>'请填写标题咯','many'=>array(
						array('value'=>'0', 'content'=>'关闭'),
						array('value'=>'1','content'=>'开启'),
				)),
			
				array('type'=>'img','many'=>array(
						array('title'=>"店铺预约图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
						
				)),
			
				
				array('type'=>'checkbox',  '全选'=>1, 'title'=>'预约每天开启时间','name'=>"hd_time",'value'=>'hd_time','msg'=>'请填写标题咯','many'=>array(
						array('value'=>'9:00-9:30','content'=>'9:00-9:30','checked'=>1),
						array('value'=>'9:30-10:00','content'=>'9:30-10:00','checked'=>1),
						array('value'=>'10:00-10:30','content'=>'10:00-10:30','checked'=>1),
						array('value'=>'10:30-11:00','content'=>'10:30-11:00','checked'=>1),
						array('value'=>'11:00-11:30','content'=>'11:00-11:30','checked'=>1),
						array('value'=>'11:30-12:00','content'=>'11:30-12:00','checked'=>1),
						array('value'=>'12:00-12:30','content'=>'12:00-12:30','checked'=>1),
						array('value'=>'12:30-13:00','content'=>'12:30-13:00','checked'=>1),
						array('value'=>'13:00-13:30','content'=>'13:00-13:30','checked'=>1),
						array('value'=>'13:30-14:00','content'=>'13:30-14:00','checked'=>1),
						array('value'=>'14:00-14:30','content'=>'14:00-14:30','checked'=>1),
						array('value'=>'14:30-15:00','content'=>'14:30-15:00','checked'=>1),
						array('value'=>'15:00-15:30','content'=>'15:00-15:30','checked'=>1),
						array('value'=>'15:30-16:00','content'=>'15:30-16:00','checked'=>1),
						array('value'=>'16:00-16:30','content'=>'16:00-16:30','checked'=>1),
						array('value'=>'16:30-17:00','content'=>'16:30-17:00','checked'=>1),
						array('value'=>'17:00-17:30','content'=>'17:00-17:30','checked'=>1),
						array('value'=>'17:30-18:00','content'=>'17:30-18:00','checked'=>1),
						array('value'=>'18:00-18:30','content'=>'18:00-18:30','checked'=>1),
						array('value'=>'18:30-19:00','content'=>'18:30-19:00','checked'=>1),
						array('value'=>'19:30-20:00','content'=>'19:30-20:00','checked'=>1),
					
				)),
				
				
				array('title'=>"店铺预约详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
			
		),U('Dianphd/index',array('aid'=>$_GET['aid'],'modulename'=>$_GET['modulename'],'token'=>$_GET['token'])),array($this,'bbc'));
	}
	public function bbc($data){
		//$data['audit']=0;
		$data['aid']=$_SESSION['aid'];
		$data['add_time']=time();
		return $data;
	}
	
    /**
     *  添加-品种
     * 如果写了msg代表此字段为必填，值为错误提示内容
     * array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y')   这是添加地图
     **/
    public function add_pinzhong(){
/*     	if(IS_POST){
    		echo "<script>alert('添加需要公司总部审核才能生效');</script>";
    	}else{
    		
    	} */
    	
	    $this->addEdit(add);

    }
    

   /**
     * 编辑品牌    hidden
     */
    public function PinzhongEdit(){
/*         if(IS_POST){
        	echo "<script>alert('编辑需要公司总部审核才能生效');</script>";
    	}else{
    		
    	} */
    	
    	$this->addEdit(Edit);
    }
   /**
     *  删除品种
     **/
    public function PinzhongDel(){
    	//print_r($this->_get('id'));die;
    	
    	
    	if($_GET['id']){
    		//日志
    		$list=M('mru_dianphd')->where(array('id'=>$_GET['id']))->find();
    		if($list) M('mru_rz')->add(array(
    				'name'=>$list['dname'],
    				'pic'=>$list['pic'],
    				'content'=>$list['content'],
    				'state'=>$list['state'],
    				'aid'=>$_SESSION['aid'],
    				'hd_time'=>$list['hd_time'],
    				'token'=>$_SESSION['token'],
    				'add_time'=>time(),
    				'sid'=>$list['id'],//物品编号
    				'type'=>'删除预约'
    		));
    	}
    	
    	
    	$aid=$this->_get('aid');
    	//print_r($aid);die;
    	$pid=$this->_get('id');
    
    	$b=M('mru_yd')->where(array('pid'=>$pid))->delete();
    	//删除
    	M('mru_wyyy')->where(array('name'=>$_GET['id']))->delete();
    
    		
    		$a=M('mru_yd2')->where(array('pid'=>$pid))->delete();
    		if($a){
    		    $ab=M('mru_dianphd')->where(array('id'=>$pid))->delete();
    		 
    		    
    		    
    	
    		   $this->redirect('index',array('aid'=>$aid,'modulename'=>$_GET['modulename']));
    		    
    		    
    		}
   
    	
       
    }
    //查看预约信息
    public function ck(){
    	$aid=$_SESSION['aid'];
    	$aWhere['mdian']=$_SESSION['aid'];
        $aWhere['token']=$_SESSION['token'];
  
    	
    	$list=M('mru_wyyy')->field("id,name,openid")->where($aWhere)->select();
    	
    	foreach ($list as $ke => $v){
    		$name2=M('mru_dianphd')->where(array('id'=>$v['name']))->getField('dname')?
    		M('mru_dianphd')->where(array('id'=>$v['name']))->getField('dname')
    		:$v['name'];
    		$tel = M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('tel');
    		M('mru_wyyy')->where(array('id'=>$v['id']))->save(array('name2'=>$name2,'tel'=>$tel));
    	}
    	
    	
    	//搜索
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
    		$aWhere['token'] =$_SESSION['token'];
    		$aWhere['mdian'] =$_SESSION['aid'];//其它信息中，需要用到的父级id

    	}//结束

    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('Dianphd/ck', array('token' => $_SESSION['token'])),//栏目样式 
    					'Head_Opt' => array(
  /*   							array(
    									'name'   => '返回',//2级
    									'url'    => U('Dianphd/index',array('token'=>$token,'aid'=>$aid,'modulename'=>$_GET['modulename']))
    							) */
    					),
    					'tips' => array(//3级
    							'你可以在这里管理信息'
    					),
    					'Table_Header' => array(//4级
    							'ID', '预约名称', '预约类型', '预约时间', '真实姓名','会员名称','手机' ,'卡号','操作'
    					),
    					'List_Opt' => array(
    							array(
    									'name' => '查看留言',
    									'url'  => U('Dianphd/ckEdit',array('token'=>$token,'aid'=>$aid,'pid'=>$_GET['id'],'modulename'=>$_GET['modulename']))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('Dianphd/ckDel',array('token'=>$token,'aid'=>$aid,'modulename'=>$_GET['modulename']))
    							),
    							
    							array(
    									'name' => '该会员详细信息',
    									'url'  => U('Dianphd/ckGr',array('token'=>$token,'aid'=>$aid,'uid'=>$_GET['id'],'modulename'=>$_GET['modulename'],'model'=>'mru_wyyy','url'=>'Dianphd/ck'))
    							),
    					),
    					
    					'search'=>array(
    							 array('title'=>'预约名称查询','name'=>'li_name2','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
    							 array('title'=>'手机查询','name'=>'li_tel','placeholder'=>'请输入手机模糊查询','search'=>'查询'),//eq是Table里判断条件 name是子段
    							//array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
    							array('title'=>'预约类型查询','name'=>'eq_type','type'=>'select','many'=>array(
    									array('value'=>'抢购预约','name'=>'抢购预约'),
    									array('value'=>'团购网预约','name'=>'团购网预约'),
    									array('value'=>'免费体验预约','name'=>'免费体验预约'),
    									array('value'=>'项目预约','name'=>'项目预约'),
    							)),
    							
    							//array('title'=>'预约名称查询','name'=>'eq_name','type'=>'select','many'=>$list)
    					)
    					
    			),
    			M('mru_wyyy')->where($aWhere)->count(),
    			M('mru_wyyy')->field('id,name2,type,time_xs,dz,openid,tel')->where($aWhere)->order("id desc"),
    			array($this,'ckabc')
    	);
    }
    
    public function ckabc($data){
    	foreach ($data as $k=>$v){
    		
    	   $data[$k]['time_xs']=str_replace(',', '&nbsp', $v['time_xs'])."时";
    	   $data[$k]['openid']=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('name');
    	   //$data[$k]['tel']=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('tel');
    	   $data[$k]['tel2']=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('idcard');
    	}		
    	
    	return $data;
    }
    
    
    public function ckEdit(){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('Dianphd2')->select();//被添加内容的表
    		foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->Edit('mru_wyyy',array(
    
/*     			array('title'=>"预约名称",'type'=>"input",'name'=>"name",'value'=>'name'),
    			array('title'=>"市区",'type'=>"input",'name'=>"shi",'value'=>'shi'),
    			array('title'=>"预约时间",'type'=>"input",'name'=>"time_xs",'value'=>'time_xs'),
    			array('title'=>"会员名称",'type'=>"input",'name'=>"openid",'value'=>'openid'), */
    	
    			/* 				array('type'=>'select','title'=>"店铺预约类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
    			 array('value'=>'1', 'content'=>'免费体验预约'),
    					array('value'=>'2','content'=>'日常预约'),
    					array('value'=>'3','content'=>'抢购预约'),
    					array('value'=>'4','content'=>'其它预约'),
    			)), */

    				
				array('title'=>"留言",'type'=>"textarea",'name'=>"content",'value'=>'content'),
    				
    	),U('Dianphd/ck',array('aid'=>$_GET['aid'],'id'=>$_GET['pid'],'modulename'=>$_GET['modulename'])),array($this,'bbc'),1);
    }
    
    
    public function ckDel(){
    	$this->del('mru_wyyy');
    }
    
    //get要传四个值，该项目表model ,该项目该条id 如果该项目是有父级传父级uid 跳回地址
    //例如:
    public function ckGr(){
    	
    	$openid=M($_GET['model'])->where(array('id'=>$_GET['id']))->getField('openid');//查出该项目下的openid
    	
    	$this->Edit('mru_jfb',array(
               
    			
    			array('title'=>"昵称",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写input框'),
    			array('title'=>"性别",'type'=>"input",'name'=>"sex",'value'=>'sex','msg'=>'请填写input框'),
    			array('title'=>"手机",'type'=>"input",'name'=>"tel",'value'=>'tel','msg'=>'请填写input框'),
    			
    			array('title'=>"地址",'type'=>"input",'name'=>"address",'value'=>'address','msg'=>'请填写input框'),
    			array('title'=>"会员卡",'type'=>"input",'name'=>"idcard",'value'=>'idcard','msg'=>'请填写input框'),
    			
    	
    	),U($_GET['url'],array('token'=>$_SESSION['token'],'aid'=>$_SESSION['aid'],'id'=>$_GET['uid'])),array($this,'bbc'),1,array('openid'=>$openid,'token'=>$_SESSION['token']));
    
    }
    
    
}
?>
