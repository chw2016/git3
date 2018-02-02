<?php
/**
 *  银波米业
 **/
class CeshiAction extends TableAction {
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
    	ob_start();
    	
    	parent::_initialize();
        $this->pz	   = D('mru_ceshi');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    
    protected function setHeader(){
    	return array(
                  		                  			array(
    					'name' => '服务项目',
    					'url'  => U('Fuwu/index', array('token' => $this->_sToken))
    			),
    			array(
    					'name' => '门店导舰',
    					'url'  => U('Mdian/index', array('token' => $this->_sToken))
    			),
                array(
                    'name' => '免费体验',
                    'url'  => U('Tiyan/index', array('token' => $this->_sToken))
                ),
              array(
                    'name' => '限时抢购',
                    'url'  => U('Qianggou/index', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '加入我们',
                    'url'  => U('Zhaopin/index', array('token' => $this->_sToken))
                ),
/*                 array(
                    'name' => '我要加盟',
                    'url'  => U('Zhaopin/index2', array('token' => $this->_sToken))
                ),  */
    			array(
    					'name' => '最新活动',
    					'url'  => U('Huodong/index', array('token' => $this->_sToken))
    			),
    			array(
    					'name' => '在线测试',
    					'url'  => U('Ceshi/index', array('token' => $this->_sToken))
    			),
    			array(
    					'name' => '品牌介绍',
    					'url'  => U('Ppjs/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '专家介绍',
    					'url'  => U('Zjjs/index', array('token' => $this->_sToken))
    			),
    			
   			
    			array(
    					'name' => '美丽资讯',
    					'url'  => U('MruZx/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '导舰市区',
    					'url'  => U('Shi/index', array('token' => $this->_sToken))
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
    
    function down(){
    	exportExcel(array(
    			array('1','damon'),
    			array('1','damon'),
    			array('1','damon')
    	),array('id','name'),'表单');
    }


    /**
     *  显示表单
     **/
	public function index(){
		if($this->_get('expor')==1){
			exportExcel(array(
					array('1','damon'),
					array('1','damon'),
					array('1','damon')
			),array('id','name'),'表单');
			die;
		}
		
		
		

		
		//当前时间>开始时间 && 当前时间<结束时间  之间活动开启,否则活动关闭		
		$aWhere = array('token' => $this->_sToken);
/* 		$list=M('mru_ceshi')->where($aWhere)->field('id,k_time,j_time')->select();//查询所有k_time,j_time
		//print_r($list);
		foreach ($list as $v){//遍历判断
			$k_time=strtotime($v['k_time']);
			$j_time=strtotime($v['j_time']);
			$time=time();
			if($k_time<$time && $time<$j_time ){
				M('mru_ceshi')->where(array('id'=>$v['id']))->setField('statu',1);//开启
			}else{
				M('mru_ceshi')->where(array('id'=>$v['id']))->setField('statu',0);//关闭
			}
				
		}  */
		$this->table(
        	array(
                'abc'=>123,
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('Ceshi/index', array('token' => $this->_sToken)),
            	'Head_Opt' => array(
                	array(
                    	'name'   => '添加测试类型',
                    	'url'    => U('Ceshi/add_pinzhong')
                	)
            	),
                'tips' => array(
                    '你可以在这里管理添测试题库信息'
                ),
            	'Table_Header' => array(
                	'ID', '测试类型名', '测试图片','操作'
            	),
            	'List_Opt' => array(
                	array(
                    	'name' => '编辑',
                    	'url'  => U('Ceshi/PinzhongEdit')
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('Ceshi/PinzhongDel')
	                ),
            		array(
            			'name' => '题目管理',
            			'url'  => U('Ceshi/index2')
            		),
            	)
        	), 
        	$this->pz->where(array('token'=>$_SESSION['token']))->count(),
        	$this->pz->field('id,type,pic')->where(array('token'=>$_SESSION['token'])),
				array($this,'abc')
         );
	}

	/**
	 *  显示表单
	 **/
	public function index2(){
		$pid=$this->_get('id');
		$aWhere = array('token' => $this->_sToken);
		$aWhere = array('pid' => $this->_get('id'));
		
		$this->table(
				array(
						'abc'=>123,
						//'id' => 'id',//如果主键不是id，则需要设置
						'HeadHover' => U('Ceshi/index', array('token' => $this->_sToken)),
						'Head_Opt' => array(
								array(
										'name'   => '添加测试题',
										'url'    => U('Ceshi/add_pinzhong2',array('pid'=>''.$pid))
								),
								array(
										'name'   => '首页',
										'url'    => U('Ceshi/index')
								)
								
						),
						'tips' => array(
								'你可以在这里管理添加测试题信息'
						),
						'Table_Header' => array(
								'ID', '测试题名称', '操作'
						),
						'List_Opt' => array(
								array(
										'name' => '编辑',
										'url'  => U('Ceshi/PinzhongEdit2',array('pid'=>''.$pid))
								),
								array(
										'name' => '删除',
										'url'  => U('Ceshi/PinzhongDel2')
								),
						
						)
				),
				M('mru_ceshit')->where($aWhere)->count(),
				M('mru_ceshit')->field('id,name')->order("id desc")->where($aWhere),
				array($this,'abcd')
				
		);
	}
	
	public function abc($data){
		foreach($data as $k=>$v){
		//	$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
			/* 	$data[$k]['content']=substr(htmlspecialchars_decode($v['content']),0,78); */
/* 			$data[$k]['statu']="{$v['statu']}"?'活动已开启':'活动关闭';
			switch ($v['lname']){
				case 1:$data[$k]['lname']='减肥';break;
				case 2:$data[$k]['lname']='美容';break;
				case 3:$data[$k]['lname']='亚健康';break;
				case 4:$data[$k]['lname']='其它';break;
			} */
			
		}
		return $data;
	}
	
	public function abcd($data){
		foreach($data as $k=>$v){
			$data[$k]['name']=substr($v['name'],0,104)."...";
			//$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
			/* 	$data[$k]['content']=substr(htmlspecialchars_decode($v['content']),0,78); */
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
			->join('tp_mru_ceshi pz on pz.id = o.pinzhong_id')
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
			->join('tp_mru_ceshi pz on pz.id = o.pinzhong_id')
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
    	//特殊复选框
    	$list=M('mru_mdian')->field('name')->select();
    	$str='';
    	foreach ($list as $v){
    		$str.="<input type='checkbox' name='mdiao[]' value='{$v['name']}'  />{$v['name']}&nbsp&nbsp";
    	}
    	

        $this->add('mru_ceshi',array(
                      // array('title'=>"测试题库名",'type'=>"input",'name'=>"name",'msg'=>'请填写品牌咯','value'=>'name'),
            
        		array('type'=>'select','title'=>"测试类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
        				array('value'=>'减肥', 'content'=>'减肥'),
        				array('value'=>'美容','content'=>'美容'),
        				array('value'=>'亚健康','content'=>'亚健康'),
        				array('value'=>'其它','content'=>'其它'),
        		)),
       /*  	array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
            array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
 */
		   array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
            )),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),

        ),U('Ceshi/index'));

    }
    
    public function add_pinzhong2(){

    	$this->add('mru_ceshit',array(
    			array('title'=>"测试题名",'type'=>"longinput",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'),
    			array('title'=>"选择A的内容",'type'=>"input",'name'=>"aname",'value'=>'aname','msg'=>'请填写品牌咯'),
    	
    			array('title'=>"选择B的内容",'type'=>"input",'name'=>"bname",'value'=>'bname','msg'=>'请填写品牌咯'),
     			array('title'=>"选择C的内容",'type'=>"input",'name'=>"cname",'value'=>'cname','msg'=>'请填写品牌咯'),
     			array('title'=>"选择D的内容",'type'=>"input",'name'=>"dname",'value'=>'dname','msg'=>'请填写品牌咯'),
    		   
    			array('title'=>"选择A的测试结果",'type'=>"input",'name'=>"aaname",'value'=>'aaname','msg'=>'请填写品牌咯'),
    			array('title'=>"选择B的测试结果",'type'=>"input",'name'=>"bbname",'value'=>'bbname','msg'=>'请填写品牌咯'),
    			array('title'=>"选择C的测试结果",'type'=>"input",'name'=>"ccname",'value'=>'ccname','msg'=>'请填写品牌咯'),
    			array('title'=>"选择D的测试结果",'type'=>"input",'name'=>"ddname",'value'=>'ddname','msg'=>'请填写品牌咯'),
    			
    	),U('Ceshi/index2',array('id'=>$this->_get('pid'))));
    
    }

    public function bbc($data){
    
    	$data['pid']=$this->_get('id');
    	return $data;
    }

    /**
     * 编辑品牌    hidden
     */
    public function PinzhongEdit(){
    	//特殊复选框开始
    	$list=M('mru_mdian')->field('name')->select();
    	$values=M('mru_qianggou')->where("id=".$this->_get('id'))->field('mdiao')->select();
    	$values=explode(',', $values['0']['mdiao']);
    	$str='';
    	foreach ($list as $v){
    		 
    		if(in_array($v['name'],$values)){
    			$a=checked;
    		}else{
    			$a='';
    		}
    		$str.="<input class='checkbox'  type='checkbox' name='mdiao[]' value='{$v['name']}' {$a} />{$v['name']}&nbsp&nbsp";
    	}

        $this->Edit('mru_ceshi',array(
           // array('title'=>"测试题库名",'type'=>"input",'name'=>"name",'msg'=>'请填写品牌咯','value'=>'name'),
            
        		array('type'=>'select','title'=>"测试类型",'name'=>"type",'value'=>'type','msg'=>'请填写标题咯','many'=>array(
        				array('value'=>'减肥', 'content'=>'减肥'),
        				array('value'=>'美容','content'=>'美容'),
        				array('value'=>'亚健康','content'=>'亚健康'),
        				array('value'=>'其它','content'=>'其它'),
        		)),
       /*  	array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
            array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
 */
		   array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
            )),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),

        ),U('Ceshi/index', array('token' => $this->token)));

    }
    public function PinzhongEdit2(){
	    $this->Edit('mru_ceshit',array(
    			array('title'=>"测试题名",'type'=>"longinput",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'),
    			array('title'=>"选择A的内容",'type'=>"input",'name'=>"aname",'value'=>'aname','msg'=>'请填写品牌咯'),
    			array('title'=>"选择B的内容",'type'=>"input",'name'=>"bname",'value'=>'bname','msg'=>'请填写品牌咯'),
     			array('title'=>"选择C的内容",'type'=>"input",'name'=>"cname",'value'=>'cname','msg'=>'请填写品牌咯'),
     			array('title'=>"选择D的内容",'type'=>"input",'name'=>"dname",'value'=>'dname','msg'=>'请填写品牌咯'),
    		//	array('title'=>"分值",'type'=>"number",'name'=>"number",'msg'=>'','value'=>'number'),

	    	    array('title'=>"选择A的测试结果",'type'=>"input",'name'=>"aaname",'value'=>'aaname','msg'=>'请填写品牌咯'),
    			array('title'=>"选择B的测试结果",'type'=>"input",'name'=>"bbname",'value'=>'bbname','msg'=>'请填写品牌咯'),
    			array('title'=>"选择C的测试结果",'type'=>"input",'name'=>"ccname",'value'=>'ccname','msg'=>'请填写品牌咯'),
    			array('title'=>"选择D的测试结果",'type'=>"input",'name'=>"ddname",'value'=>'ddname','msg'=>'请填写品牌咯'),
    			
    			
    	),U('Ceshi/index2', array('token' => $this->token,'id'=>$this->_get('pid'))));
    
    }

    /**
     *  删除品种
     **/
    public function PinzhongDel(){
    	print_r();
    	M('mru_ceshit')->where(array('pid'=>$this->_get('id')))->delete();
        $this->del('mru_ceshi');
    }
    public function PinzhongDel2(){
    	$this->del('mru_ceshit');
    }
}
?>
