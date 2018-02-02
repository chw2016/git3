<?php
/**
 *  银波米业
 **/
class YxCkAction extends TableAction {
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
        $this->pz	   = D('Yanxiang_ck');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    
    protected function setHeader(){
    	 return array(
          
    	 		
    	 		array(
    	 				'name' => '返回',
    	 				'url'  => U('Yanxiang/index', array('token' => $this->_sToken))
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
		
		$this->table(
        	array(
                'abc'=>123,
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('YxCk/index', array('token' => $this->_sToken)),
            	'Head_Opt' => array(
                	array(
                    	'name'   => '添加看研详',
                    	'url'    => U('YxCk/add_pinzhong')
                	)
            	),
                'tips' => array(
                    '看研祥,轮播图列表'
                ),
            	'Table_Header' => array(
                	'ID', '看研祥栏目图','操作'
            	),
            	'List_Opt' => array(

            			array(
            					'name' => '体验信息',
            					'url'  => U('YxCk/ty')
            			),
            			
                	array(
                    	'name' => '编辑',
                    	'url'  => U('YxCk/PinzhongEdit')
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('YxCk/PinzhongDel')
	                ),
            	),
 /*        			//搜索
        			'search'=>array(
        					array('title'=>'名称','name'=>'li_name'),//li是Table里判断条件 name是子段
        					array('title'=>'名称','name'=>'eq_name'),//eq是Table里判断条件 name是子段
        					array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')//be是Table里判断条件 add_time是子段
        			)//结束 */
        	), 
        	$this->pz->where()->count(),
        	$this->pz->field('id,pic')->order('sort desc')->where(),
			array($this,'abc')

         );
	}
    //看研详列表页轮播图显示
    public function index1(){
       // echo $_SESSION['ckid'];die;
        $where['uid']=$_SESSION['ckid'];
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('YxCk/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(

                    array(
                        'name'   => '添加文章',
                        'url'    => U('YxCk/add_content')
                    ),
                    array(
                        'name'   => '添加轮播图',
                        'url'    => U('YxCk/add_pinzhong1')
                    ),
                    array(
                        'name'   => '轮播图列表',
                        'url'    => U('YxCk/index1')
                    ),
                    array(
                        'name'=>'返回',
                        'url'=>U('YxCk/ty')
                    )
                ),
                'tips' => array(
                    '内页列表轮播图列表'
                ),
                'Table_Header' => array(
                    'ID', '图片','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '编辑',
                        'url'  => U('YxCk/PinzhongEdit1')
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('YxCk/PinzhongDel1')
                    ),
                ),
            ),
            M('Yanxiang_ck3')->where($where)->count(),
            M('Yanxiang_ck3')->field('id,pic')->order('add_time desc')->where($where),
            array($this,'abc')

        );
    }
	public function abc($data){
		foreach($data as $k=>$v){
		
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
		
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
    /**
     *  添加-品种
     * 如果写了msg代表此字段为必填，值为错误提示内容
     * array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y')   这是添加地图
     **/
    public function add_pinzhong(){

        $this->add('Yanxiang_ck',array(
   
        		array('type'=>'img','many'=>array(
                        array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic')
              
                 )),
            array('type'=>'input','name'=>'url','title'=>'跳转链接地址'),
               array('type'=>'input','name'=>'sort','title'=>'排序')
          
        ),U('YxCk/index'),array($this,'bbc'));

    }

    //看研详内页列表，轮播图
    public function add_pinzhong1(){

        $this->add('Yanxiang_ck3',array(

            array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic')

            )),
            array('type'=>'input','name'=>'url','title'=>'链接')

        ),U('YxCk/index1'),array($this,'bbc1'));

    }
    public function bbc1($data){
        $data['add_time']=time();
        $data['uid']=$_SESSION['ckid'];
        return $data;
    }
    //看研详内页列表，轮播图
    public function PinzhongEdit1(){

        $this->Edit('Yanxiang_ck3',array(
            array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic')

            )),
                array('type'=>'input','name'=>'url','title'=>'链接','value'=>'url')

        ),U('YxCk/index1', array('token' => $this->token),array($this,'bbc')));

    }


    /**
     * 编辑品牌
     */
    public function PinzhongEdit(){

        $this->Edit('Yanxiang_ck',array(
          array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic')
              
            )),
            array('type'=>'input','name'=>'url','title'=>'链接跳转地址','value'=>'url'),
            array('type'=>'input','name'=>'sort','title'=>'排序','value'=>'sort')

        ),U('YxCk/index', array('token' => $this->token),array($this,'bbc')));

    }
        
        public function bbc($data){
        	$data['add_time']=time();
        
        	return $data;
        }

    /**
     *  删除品种
     **/
    public function PinzhongDel(){
        $this->del('Yanxiang_ck');
    }

    public function PinzhongDel1(){
        $this->del('Yanxiang_ck3');
    }
    
    
    
    /**
     *  显示表单
     **/
    public function ty(){
        if($_GET['id']) $_SESSION['ckid']=$_GET['id'];
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('YxCk/index', array('token' => $this->_sToken)),
    					'Head_Opt' => array(
    							array(
    									'name'   => '添加文章',
    									'url'    => U('YxCk/add_content')
    							),
                            array(
                                'name'   => '添加轮播图',
                                'url'    => U('YxCk/add_pinzhong1')
                            ),
                            array(
                                'name'   => '轮播图列表',
                                'url'    => U('YxCk/index1')
                            ),
    							array(
    									'name'   => '返回',
    									'url'    => U('YxCk/index')
    							)
    					),
    					'tips' => array(
    							'看研究,内页文章列表'
    					),
    					'Table_Header' => array(
    							'ID', '标题', '图片','添加时间' ,'操作'
    					),
    					'List_Opt' => array(
    
    							array(
    									'name' => '编辑',
    									'url'  => U('YxCk/save_content',array('uid'=>$_GET['id']))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('YxCk/delete_content')
    							),
    					),
    					
    			),
    			M('Yanxiang_ck2')->where(array('uid'=>$_SESSION['ckid']))->count(),
    			M('Yanxiang_ck2')->field('id,title,pic,times')->where(array('uid'=>$_SESSION['ckid'])),
    			array($this,'tyabc')
    
    	);
    }
    
    
    public function tyabc($data){
    	foreach($data as $k=>$v){
    		$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
    	 
    	}
    	return $data;
    }
    
    public function tyEdit(){
      
    	$this->Edit('Yanxiang_ck2',array(
    			array('title'=>"留言",'type'=>"textarea",'name'=>"content",'value'=>'content')
    
    	),U('YxCk/ty', array('token' => $this->token,'id'=>$_GET['pid'])),array($this,'bbc'),3);
    
    }
    
    public function tyDel(){
    	$this->del('Yanxiang_ck2');
    }
    
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
    	$this->sortajaxTable('Yanxiang_ck');
    }

    
    
    
    //定义增改函数
    public function add_save($aaa){
    	 
    	$this->$aaa('Yanxiang_ck2',array(
    			array('title'=>"标题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写input框'),
    			array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'<a style="color:red;">注意:如果镇了链接前台跳的是链接地址,前面加http://</a>'),
    			array('type'=>'img','many'=>array(
    					array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic'),
    			)),
    			array('title'=>"描述",'type'=>"textarea2",'name'=>"ms",'value'=>'ms','msg'=>'请填写input框'),
    			array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
    			 
    	),U('YxCk/ty',array('token'=>$_SESSION['token'])),array($this,'xbbc'));
    
    }
    public function xbbc($data){
    
    	$data['times']=date("Y-m-d",time());
    	$data['uid']=$_SESSION['ckid'];
    	return $data;
    }
    //添加
    public function add_content(){
    	$this->add_save(add);
    }
    //编辑
    public function save_content(){
    	$this->add_save(Edit);
    }
    
    //删除
    public function delete_content(){
    	//M('Yanxiang_zx')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
    	$this->del('Yanxiang_ck2');
    }
    
    
}
?>
