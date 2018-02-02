<?php
/**
 *  银波米业
 **/
class TiyanAction extends TableAction {
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
        $this->pz	   = D('mru_tiyan');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    
    protected function setHeader(){
    	return include "./Lib/Action/User/Mru_top.php";
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
		
		//搜索
/* 		if(IS_POST){
			$_POST=$_REQUEST;
			$aWhere=$this->search($_POST);
			
			$aWhere['token'] =$this->_sToken;
		}//结束 */
		
	
		
		$list=M('mru_tiyan')->where($aWhere)->field('id,k_time,j_time')->select();//查询所有k_time,j_time
	
		foreach ($list as $v){//遍历判断
			$k_time=strtotime($v['k_time']);
			$j_time=strtotime($v['j_time']);
			$time=time();
			if($k_time<$time && $time<$j_time ){
				M('mru_tiyan')->where(array('id'=>$v['id']))->setField('statu',1);//开启
			}else{
				M('mru_tiyan')->where(array('id'=>$v['id']))->setField('statu',0);//关闭
			}
		
		}
		
		$this->table(
        	array(
                'abc'=>123,
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('Tiyan/index', array('token' => $this->_sToken)),
            	'Head_Opt' => array(
                	array(
                    	'name'   => '添加体验',
                    	'url'    => U('Tiyan/add_pinzhong')
                	),
            			
            			array(
            					'name' => '查看体验信息',
            					'url'  => U('Dianphd2/ck')
            			)
            			
            	),
                'tips' => array(
                    '你可以在这里管理免费体验信息'
                ),
            	'Table_Header' => array(
                	'ID', '体验名称', '添加时间', '图片','报名人数','活动开始时间','活动结束时间','活动状态','排序' ,'操作'
            	),
            	'List_Opt' => array(

          
            			
                	array(
                    	'name' => '编辑',
                    	'url'  => U('Tiyan/PinzhongEdit')
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('Tiyan/PinzhongDel')
	                ),
            	),
 /*        			//搜索
        			'search'=>array(
        					array('title'=>'名称','name'=>'li_name'),//li是Table里判断条件 name是子段
        					array('title'=>'名称','name'=>'eq_name'),//eq是Table里判断条件 name是子段
        					array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')//be是Table里判断条件 add_time是子段
        			)//结束 */
        	), 
        	$this->pz->where($aWhere)->count(),
        	$this->pz->field('id,name,add_time,pic,num,k_time,j_time,statu,sort')->order("sort")->where($aWhere),
			array($this,'abc')

         );
	}
	public function abc($data){
		foreach($data as $k=>$v){
			$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$data[$k]['statu']=$v['statu']?'活动开启中':'活动关闭';
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
		$data[$k]['name']=substr(htmlspecialchars_decode($v['name']),0,30)."..."; 
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

        $this->add('mru_tiyan',array(
            array('title'=>"体验名称",'type'=>"input",'name'=>"name",'msg'=>'请填写体验名称咯'),
           /*  array('title'=>"密码",'type'=>"password",'name'=>"pwd"), */
        		array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
        		array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
        		array('title'=>"报名人数",'type'=>"number",'name'=>"num",'value'=>'num'),
        		array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic")
/*                 array('title'=>"图片2",'type'=>"img",'name'=>"pic2"),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic3") */
            )),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content")
        /*     array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y') */
        ),U('Tiyan/index'),array($this,'bbc'));

    }


    /**
     * 编辑品牌
     */
    public function PinzhongEdit(){

        $this->Edit('mru_tiyan',array(
            array('title'=>"体验名称",'type'=>"input",'name'=>"name",'value'=>'name'),
        		array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
        		array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
        		array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
            array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic')
            )),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content')

        ),U('Tiyan/index', array('token' => $this->token),array($this,'bbc')));

    }
        
        public function bbc($data){
        	$data['add_time']=time();
        
        	return $data;
        }

    /**
     *  删除品种
     **/
    public function PinzhongDel(){
        $this->del('mru_tiyan');
    }
    
    
    
    /**
     *  显示表单
     **/
    public function ty(){
    	$token=$_SESSION['token'];
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('Tiyan/index', array('token' => $this->_sToken)),
    					'Head_Opt' => array(
    							array(
    									'name'   => '返回',
    									'url'    => U('Tiyan/index')
    							)
    					),
    					'tips' => array(
    							'你可以在这里管理免费体验信息'
    					),
    					'Table_Header' => array(
    							'ID', '体验名称', '体验类型','电话','预约时间','小时','会员姓名' ,'操作'
    					),
    					'List_Opt' => array(
    
    							array(
    									'name' => '查看留言',
    									'url'  => U('Tiyan/tyEdit',array('pid'=>$_GET['id']))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('Tiyan/tyDel')
    							),
    					),
    					/*        			//搜索
    					 'search'=>array(
    					 		array('title'=>'名称','name'=>'li_name'),//li是Table里判断条件 name是子段
    					 		array('title'=>'名称','name'=>'eq_name'),//eq是Table里判断条件 name是子段
    					 		array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')//be是Table里判断条件 add_time是子段
    					 )//结束 */
    			),
    			M('mru_tiyan2')->where(array('token'=>$token,'pid'=>$_GET['id']))->count(),
    			M('mru_tiyan2')->field('id,name,type,openid,time,xs')->where(array('token'=>$token,'pid'=>$_GET['id'])),
    			array($this,'tyabc')
    
    	);
    }
    
    
    public function tyabc($data){
    	foreach($data as $k=>$v){
    		
    		$data[$k]['openid']=M('mru_jfb')->where(array('token'=>$_SESSION['token'],'openid'=>$v['openid']))->getField('tel');
    		$data[$k]['name1']=M('mru_jfb')->where(array('token'=>$_SESSION['token'],'openid'=>$v['openid']))->getField('name');
    	}
    	return $data;
    }
    
    public function tyEdit(){
      
    	$this->Edit('mru_tiyan2',array(
    			array('title'=>"留言",'type'=>"textarea",'name'=>"content",'value'=>'content')
    
    	),U('Tiyan/ty', array('token' => $this->token,'id'=>$_GET['pid'])),array($this,'bbc'),3);
    
    }
    
    public function tyDel(){
    	$this->del('mru_tiyan2');
    }
    
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
    	$this->sortajaxTable('mru_tiyan');
    }

}
?>
