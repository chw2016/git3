<?php
/**
 *  银波米业
 **/
class HuodongAction extends TableAction {
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
        $this->pz	   = D('mru_huodong');//招聘
        $this->dp	   = D('mru_huodong');//加盟
        $this->order   = D('Miye_order');
      
    }
    
    protected function setHeader(){
    	return include "./Lib/Action/User/Mru_top.php";
    	
     /* return array(
   			array(
    					'name' => '服务项目',
    					'url'  => U('Fuwu/index', array('token' => $this->_sToken))
    			),
    			array(
    					'name' => '门店导航',
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
    	); */
    	
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
		$aWhere['aid']=0;
		
		    	//搜索
		 if(IS_POST){
			$_POST=$_REQUEST;
			$aWhere=$this->search($_POST);
			$aWhere['token'] =$_SESSION['token'];
			$aWhere['aid']=0;
		//	P($aWhere);
		//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
		}//结束 
		//print_r(session(token));
		
		$this->table(
        	array(
                'abc'=>123,
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('Huodong/index', array('token' => $this->_sToken)),
            	'Head_Opt' => array(
                	array(
                    	'name'   => '添加公司活动',
                    	'url'    => U('Huodong/add_pinzhong')
                	),

            			
            		/* 	array(
            					'name'   => '店铺最新活动审核管理 ',
            					'url'    => U('Huodong/index2')
            			), */
            			array(
            					'name'   => '导出数据 ',
            					'url'    => U('ExportExcel/Huodong')
            			),
            			
            			
            			
            	),
                'tips' => array(
                    '链接:http://v.wapwei.com/index.php?g=Wap&m=Mruhd&a=index&token=e756d6be1ec4fab3c5920f3a3437160b'
                ),
            	'Table_Header' => array(
                    'ID', '标题','是否开启', '发布时间','图片','排序','操作'
            	),
            	'List_Opt' => array(
            			
/*             			array(
            					'name' => '报名信息',
            					'url'  => U('Huodong/mb')
            			), */
                	array(
                    	'name' => '编辑',
                    	'url'  => U('Huodong/PinzhongEdit')
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('Huodong/PinzhongDel')
	                ),

/*                     array(
                        'name' => '置顶',
                        'url'  => U('Huodong/zd')
                    ), */
            	),
        			
        			        		//搜索
        			 'search'=>array(
        			 		//array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
        			 		//array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
        			 		array('title'=>'发布时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
        			 )//结束
        	), 
        	$this->pz->where($aWhere)->count(),
        	$this->pz->field('id,title,status,add_time,pic,sort')->order("sort")->where($aWhere),
			array($this,'abc')

         );
	}


public function zd(){
    $id=$this->_get('id');
    M('mru_huodong')->where("id=".$id)->save(array('add_time'=>time()));

    $this->redirect(U('index'));
}




	public function abc($data){
		
		foreach($data as $k=>$v){
			$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			
			
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
			switch ($v['status']){
				case 1:$data[$k]['status']='开启';break;
				case 0:$data[$k]['status']='关闭';break;
			}
			
		}
		
		return $data;
	}
	
	
	public function abcd($data){
	
		foreach($data as $k=>$v){
            
			$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			switch ($v['state2']){
				case 1:$data[$k]['state2']='已审核';break;
				case 0:$data[$k]['state2']='未审核';break;
			}
			
			
			switch ($v['type']){
				case 1:$data[$k]['type']='发布活动';break;
				case 2:$data[$k]['type']='删除活动';break;
			}
				
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
			switch ($v['status']){
				case 1:$data[$k]['status']='开启';break;
				case 0:$data[$k]['status']='关闭';break;
			}
			
			$data[$k]['aid']=M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name');
						
	
			
				
		}
	
		return $data;
	}
	
	
	public function index2(){
		$aWhere = array('token' => $this->_sToken);
		$this->table(
				array(
						'abc'=>123,
						//'id' => 'id',//如果主键不是id，则需要设置
						'HeadHover' => U('Mdian/index', array('token' => $this->_sToken)),
						'Head_Opt' => array(
/* 								array(
										'name'   => '店铺预约审核管理',
										'url'    => U('Mdian/audit')
								),
								array(
										'name'   => '店铺最新活动审核管理 ',
										'url'    => U('Huodong/index2')
								) */
								
								array(
										'name'   => '返回',
										'url'    => U('Mdian/index')
								),
								
								
						),
						'tips' => array(
								'你可以在这里管理公司活动信息'
						),
						'Table_Header' => array(
								'ID','店铺操作', '店铺', '标题','是否开启', '发布时间','图片','审核状态','排序','操作'
						),
						'List_Opt' => array(
									array(
											'name' => '审核',
											'url'  => U('Huodong/sh')
									),
									array(
											'name' => '取消审核',
											'url'  => U('Huodong/sh2')
									),
						)
				),
				$this->pz->where(array('token' => $this->_sToken,'aid'=>array('neq',0)))->count(),
				$this->pz->field('id,type,aid,title,status,add_time,pic,state2,sort')->order("add_time desc")->where(array('token' => $this->_sToken,'aid'=>array('neq',0))),
				array($this,'abcd')
	
		);
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

    	//特殊点选框,复选框,下拉列表
     	$list=M('mru_mdian')->select();
    	foreach ($list as $k=>$v){
	    	$list[$k]['content']=$v['name'];
	    	$list[$k]['value']=$v['id'];
	    	unset($list[$k]['name']);
    	} 
    	$url=C('site_url')."index.php?g=Wap&m=Mruhd&a=index2&token=".$_SESSION['token'];
        $this->add('mru_huodong',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'请填写标题咯','value'=>'title'),
        	array('title'=>"门店活动",'type'=>"a",'url'=>$url,'name'=>"门店活动链接",'target'=>'1'),
        	array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'<a style="color:red;" >如果镇了链接,前台跳的是链接地址：注意:前面加http://</a>'),
        		array('type'=>'select','title'=>"是否开启",'name'=>"status",'value'=>'status','msg'=>'请填写标题咯','many'=>array(
        				array('value'=>'1', 'content'=>'开启'),
        				array('value'=>'0','content'=>'关闭'),
        		)),
        	array('type'=>'checkbox','title'=>'参与门店', '全选'=>1, 'name'=>'mdiaos', 'value'=>'mdiaos','msg'=>'请填写标题咯','many'=>$list),



            array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic')
            )),

            array('title'=>"活动详细",'type'=>"textarea",'name'=>"content",'value'=>'content')
       /*      array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y') */
        ),U('Huodong/index'),array($this,'bbc'));

    }
    public function bbc($data){
    
    	$data['add_time']=time();
    	$data['state2']=1;
    	return $data;
    }

    /**
     * 编辑品牌
     */
    public function PinzhongEdit(){
        	//特殊点选框,复选框,下拉列表
     	$list=M('mru_mdian')->select();
    	foreach ($list as $k=>$v){
	    	$list[$k]['content']=$v['name'];
	    	$list[$k]['value']=$v['id'];
	    	unset($list[$k]['name']);
    	} 


        $this->Edit('mru_huodong',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'请填写标题咯','value'=>'title'),
        		array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'<a style="color:red;" >如果镇了链接,前台跳的是链接地址：注意:前面加http://</a>'),

        		
        		array('type'=>'select','title'=>"是否开启",'name'=>"status",'value'=>'status','msg'=>'请填写标题咯','many'=>array(
        				array('value'=>'1', 'content'=>'开启'),
        				array('value'=>'0','content'=>'关闭'),
        		)),
        		
        	array('type'=>'checkbox','title'=>'参与门店','全选'=>1,  'name'=>'mdiaos', 'value'=>'mdiaos','msg'=>'请填写标题咯','many'=>$list),
        	array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic')
            )),

            array('title'=>"活动详细",'type'=>"textarea",'name'=>"content",'value'=>'content')

        ),U('Huodong/index', array('token' => $this->token)));

    }

    /**
     *  删除品种
     **/
    public function PinzhongDel(){
    	$b=M('mru_hubm')->where(array('uid'=>$_GET['id']))->delete();
    
    		$this->del('mru_huodong');
    	
        
    }
    
    public function sh(){
    	M('mru_huodong')->where(array('id'=>$_GET['id']))->save(array('state2'=>1));
    	$type=M('mru_huodong')->where(array('id'=>$_GET['id']))->getField('type');
    	if($type==2){
    		M('mru_hubm')->where(array('uid'=>$_GET['id']))->delete();
    		$b=M('mru_huodong')->where(array('id'=>$_GET['id']))->delete();
    		if($b){
    			$this->error2('删除成功');
    		}else{
    			$this->error2('删除失败');
    		}
    	}
    	$this->redirect('index2');
    }
    
    public function sh2(){
    	M('mru_huodong')->where(array('id'=>$_GET['id']))->save(array('state2'=>0));
    	$this->redirect('index2');
    }
    
    public function mbdel(){
    	$this->del('mru_hubm');
    } 
    
    
    /**
     *  显示表单
     **/
    public function mb(){
    	$id=$_GET['id'];
    	
    	$aWhere = array('token' => $this->_sToken);
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('Huodong/index', array('token' => $this->_sToken)),
    					'Head_Opt' => array(
    						    array(
    									'name'   => '返回 ',
    									'url'    => U('Huodong/index',array('aid'=>$_GET['aid']))
    							)
    					),
    					'tips' => array(
    							'你可以在这里管理公司活动信息'
    					),
    					'Table_Header' => array(
    							'ID', '活动名称', '报名时间','会员id','操作'
    					),
    					'List_Opt' => array(
    							 
    	
    							array(
    									'name' => '删除',
    									'url'  => U('Huodong/mbdel')
    							),
    							
    							array(
    									'name' => '该会员信息',
    									'url'  => U('Dianphd/ckGr',array('model'=>'mru_hubm','url'=>'Huodong/mb','uid'=>$_GET['id']))
    							),
    
    					
    					)
    			),
    			M('mru_hubm')->where(array('uid'=>$id))->count(),
    			M('mru_hubm')->field('id,uid,add_time,openid')->order("add_time desc")->where(array('uid'=>$id)),
    			array($this,'mbabc')
    
    	);
    }
    
    
    public function mbabc($data){
    	foreach($data as $k=>$v){
    		$data[$k]['uid']=M('mru_huodong')->where(array('id'=>$v['uid']))->getField('title');
    		$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
    		
    		//sql语句
    		
    		
    			
    	}
    	return $data;
    }
    
    //排序  只 能主健是id,子段是sort 才能排序  有意见自己去开发！
    public function sortajax(){
    	$this->sortajaxTable('mru_huodong');
    }
    
}
?>
