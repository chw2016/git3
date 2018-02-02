<?php
/**
 *  银波米业
 **/
class MruXfAction extends TableAction {
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
        $this->pz	   = D('mru_xf');//招聘
        $this->dp	   = D('mru_xf');//加盟
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
    	
    	$list=M('mru_mdian')->where(array('token'=>$_SESSION['token']))->select();//被添加内容的表
    	foreach ($list as $k=>$v){
    	  $list[$k]['value']=$v['id'];//把id子段改成value
    	} 
    	
        $aWhere = array('token' =>$_SESSION['token']);
        
         	//搜索
         if(IS_POST){
        $_POST=$_REQUEST;
        $aWhere=$this->search($_POST);
        $aWhere['token'] =$_SESSION['token'];
        //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
        }//结束 
        
        
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('MruXf/index',array('token' =>$_SESSION['token'])),
               'Head_Opt' => array(
/*                     array(
                        'name'   => '我要加盟',
                        'url'    => U('MruXf/index2')
                    ), */
               		


               		
     /*           		array(
               				'name'   => '导出数据',
               				'url'    => U('ExportExcel/MruXf')
               		), */
               		
                ),
                'tips' => array(
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(
                    'ID', '会员姓名','获得时间','获取方式', '红包' ,'积分'/*,'优惠卷' */,'操作'
                ),
                'List_Opt' => array(
/*                     array(
                        'name' => '查看详情',
                        'url'  => U('MruXf/ck')
                    ), */
                     array(
                        'name' => '删除',
                        'url'  => U('MruXf/ck2')
                    ),
                ),
            		       		//搜索
            		 'search'=>array(
            		 		//array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
            		 		//array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
            		 	  
            	  		    array('title'=>'积分红包分类查询','name'=>'eq_fs','type'=>'select','many'=>array(
            		 				array('value'=>'购买限时抢购商品','name'=>'红包记录'),
            		 				array('value'=>'首次分享朋友圈','name'=>'积分记录'),
            		 				//array('value'=>'3','name'=>'旅游1'),
            		 		)) 
            		 		//array('type'=>'select','title'=>"按店铺查询",'name'=>"eq_aid",'many'=>$list),
            		 		
            		 )//结束 
            		
            ),
            M('mru_xf')->where($aWhere)->count(),
            M('mru_xf')->field('id,openid,add_time,fs,hongbao ,num/*,yid */')->order("add_time desc")->where($aWhere),
        		array($this,'index2abc')
        		

        );
    }
    
    public function index2abc($data){
    	foreach($data as $k=>$v){
    		$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            $user = M('Wxuser')->where(array('token'=>$_SESSION['token']))->find();
            $info =M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('name');
            $data[$k]['openid'] = $info ? $info:M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$v['openid']))->getField('nickname');


    		//$data[$k]['yid']=M('mru_wdyhj')->where(array('id'=>$v['yid'],'token'=>$_SESSION['token']))->getField('name');
    		$data[$k]['hongbao']=$v['hongbao']?"红包+".$v['hongbao']:'';
    	 	$data[$k]['num']=$v['num']?"积分+".$v['num']:''; 
    	
    	}
    	return $data;
    }
    

    public function jl(){
    	$id=$_GET['id'];
   	    $aWhere = array('pid' => $id);
   	    
   	    if(IS_POST){
   	    	$_POST=$_REQUEST;
   	    	$aWhere=$this->search($_POST);
   	    	$aWhere['token'] =$_SESSION['token'];
   	    	$aWhere['pid']=$_GET['id'];
   	    	//P($aWhere);die;
   	    	//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
   	    }
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('MruXf/index', array('token' => $this->_sToken)),
               'Head_Opt' => array(
/*                     array(
                        'name'   => '我要加盟',
                        'url'    => U('MruXf/index2')
                    ), */
               		array(
               				'name'   => '返回',
               				'url'    => U('MruXf/index')
               		),
               		array(
               				'name'   => '导出所有招聘数据',
               				'url'    => U('ExportExcel/jl')
               		)
                ),
                'tips' => array(
                    '你可以在这里管理加盟信息'
                ),
                'Table_Header' => array(
                    'ID', '姓名','性别', '年龄','学历','籍贯', '电话','邮箱','应聘时间','查看'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看详情',
                        'url'  => U('MruXf/jlk',array('uid'=>$_GET['id']))
                    ),
                     array(
                        'name' => '删除',
                        'url'  => U('MruXf/jlk2')
                    ),
                ),
            		       		//搜索
            		 'search'=>array(
            		 		//array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
            		 		//array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
            		 		array('title'=>'应聘时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
            		 )//结束 
            ),
            M('mru_jl')->where($aWhere)->count(),
            M('mru_jl')->field('id,name,age,sex,xl,jg,dh,yx,add_time')->where($aWhere),
        		array($this,'jlabc')

        );
    }
    
    public function jlabc($data){
    	foreach($data as $k=>$v){
    	$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            $user = M("Wxuser")->where(array('token'=>$_SESSION['token']))->find();
            $data[$k]['name'] =$v['name'] ? $v['name']: M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$v['openid']))->getField('nickname');
    	
    	}
    	return $data;
    }

	public function abc($data){
		foreach($data as $k=>$v){
	
		//	$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
			/* 	$data[$k]['content']=substr(htmlspecialchars_decode($v['content']),0,78); */
		//$data[$k]['sex']="{$v['sex']}"?'活动已开启':'活动关闭';
			switch ($v['sex']){
				case 1:$data[$k]['sex']='男';break;
				case 2:$data[$k]['sex']='女';break;
				case 3:$data[$k]['sex']='不限';break;
			}
			
			switch ($v['jy']){
				case 1:$data[$k]['jy']='一年以上';break;
				case 2:$data[$k]['jy']='二年以上';break;
				case 3:$data[$k]['jy']='三年以上';break;
				case 4:$data[$k]['jy']='不限';break;
			}
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
                        'url'  => U('MruXf/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
                'FormUrl' => U('MruXf/dianpuAdd',array('token'=>$_SESSION['token']))
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
                'FormUrl' => U('MruXf/dianpuEdit',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('MruXf/dianpu', array('token' => $this->_sToken)),
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
            'HeadHover' => U('MruXf/order', array('token' => $this->_sToken)),
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
                    'url'  => U('MruXf/OrderShow', array('token' => $this->_sToken))
                ),
                array(
                    'name' => '完成',
                    'url'  => U('MruXf/orderHandle', array(
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

        $this->add('mru_xf',array(
            array('title'=>"职位",'type'=>"input",'name'=>"zw",'msg'=>'请填写标题咯','value'=>'zw'),
            array('title'=>"部门",'type'=>"input",'name'=>"bm",'msg'=>'请填写标题咯','value'=>'bm'),
        	array('title'=>"工资",'type'=>"input",'name'=>"gz",'msg'=>'请填写标题咯','value'=>'gz'),
        		array('type'=>'radio','title'=>"性别",'name'=>"sex",'value'=>'sex','msg'=>'请填写性别咯','many'=>array(
        				array('value'=>'1','content'=>'男'),
        				array('value'=>'2','content'=>'女'),
        				array('value'=>'3','content'=>'不限'),
        		)),

        		array('type'=>'select','title'=>"工作经验",'name'=>"jy",'value'=>'jy','msg'=>'请填写标题咯','many'=>array(
        				array('value'=>'1', 'content'=>'一年以上'),
        				array('value'=>'2','content'=>'二年以上'),
        				array('value'=>'3','content'=>'三年以上'),
        				array('value'=>'4','content'=>'不限'),
        		)),

            array('title'=>"学历",'type'=>"input",'name'=>"xl",'msg'=>'请填写标题咯','value'=>'xl'),
            array('title'=>"年龄",'type'=>"input",'name'=>"age",'msg'=>'请填写标题咯','value'=>'age'),
           // array('title'=>"学历",'type'=>"input",'name'=>"xl",'msg'=>'请填写标题咯','value'=>'xl'),

            array('title'=>"招聘人数",'type'=>"input",'name'=>"sunber",'msg'=>'请填写标题咯','value'=>'sunber'),
        	array('title'=>"截止日期",'type'=>"input",'name'=>"time",'msg'=>'请填写标题咯','value'=>'time'),
            array('title'=>"任职要求",'type'=>"textarea",'name'=>"content",'value'=>'content')
       /*      array('title'=>"经纬度",'type'=>"map",'lng'=>"position_x",'lat'=>'position_y') */
        ),U('MruXf/index'));

    }


    /**
     * 编辑品牌
     */
    public function PinzhongEdit(){

        $this->Edit('mru_xf',array(
            array('title'=>"职位",'type'=>"input",'name'=>"zw",'msg'=>'请填写标题咯','value'=>'zw'),
            array('title'=>"部门",'type'=>"input",'name'=>"bm",'msg'=>'请填写标题咯','value'=>'bm'),

        		array('title'=>"工资",'type'=>"input",'name'=>"gz",'msg'=>'请填写标题咯','value'=>'gz'),
        		array('type'=>'radio','title'=>"性别",'name'=>"sex",'value'=>'sex','msg'=>'请填写性别咯','many'=>array(
        				array('value'=>'1','content'=>'男'),
        				array('value'=>'2','content'=>'女'),
        				array('value'=>'3','content'=>'不限'),
        		)),

        		array('type'=>'select','title'=>"工作经验",'name'=>"jy",'value'=>'jy','msg'=>'请填写标题咯','many'=>array(
        				array('value'=>'1', 'content'=>'一年以上'),
        				array('value'=>'2','content'=>'二年以上'),
        				array('value'=>'3','content'=>'三年以上'),
        				array('value'=>'4','content'=>'不限'),
        		)),

            array('title'=>"年龄",'type'=>"input",'name'=>"age",'msg'=>'请填写标题咯','value'=>'age'),
            array('title'=>"学历",'type'=>"input",'name'=>"xl",'msg'=>'请填写标题咯','value'=>'xl'),

            array('title'=>"招聘人数",'type'=>"input",'name'=>"sunber",'msg'=>'请填写标题咯','value'=>'sunber'),
        		array('title'=>"截止日期",'type'=>"input",'name'=>"time",'msg'=>'请填写标题咯','value'=>'time'),
            array('title'=>"任职要求",'type'=>"textarea",'name'=>"content",'value'=>'content')

        ),U('MruXf/index', array('token' => $this->token)));

    }

    /**
     *  删除品种
     **/
    public function PinzhongDel(){
    	$id=$_GET['id'];
    	M(mru_jl)->where(array('pid'=>$id))->delete();
        $this->del('mru_xf');
    }
    
    public function ck(){
     	$id=$_GET['id'];
     	$list=M('mru_xf')->where(array('id'=>$id))->find();
     	$list['state']=$list['state']?'关闭':'开启';
     	$list['aid']=M('mru_mdian')->where(array('id'=>$list['aid']))->getField('name');
     	$this->assign('list',$list);
        
    	$this->display();
    }
    
    
    public function ck2(){
    	$id=$_GET['id'];
    	$list=M('mru_xf')->where(array('id'=>$id))->delete();
    	if($list){
    		$this->error2("删除成功");
    	}else{
    		$this->error2("删除失败");
    	}
    	//print_r($list);
    	//echo 1 ;die;
    	$this->display();
    }
    
    
    public function jlk(){
    	$id=$_GET['id'];
    	$list=M('mru_jl')->where(array('id'=>$id))->find();
    	$this->assign('list',$list);
    	//print_r($list);
    	//echo 1 ;die;
    	$this->display();
    }
    
    
    public function jlk2(){
    	$id=$_GET['id'];
    	$list=M('mru_jl')->where(array('id'=>$id))->delete();
    	if($list){
    		$this->error2("删除成功");
    	}else{
    		$this->error2("删除失败");
    	}
    	//print_r($list);
    	//echo 1 ;die;
    	$this->display();
    }
    
    
    public function addjm(){
    	$aWhere = array('token' => $this->_sToken);
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('MruXf/index', array('token' => $this->_sToken)),
    					'Head_Opt' => array(
    					/*                     array(
    					 'name'   => '我要加盟',
    							'url'    => U('MruXf/index2')
    					), */
    							array(
    									'name'   => '返回',
    									'url'    => U('MruXf/index')
    							),
    							array(
    									'name'   => '编辑我要加盟广告信息',
    									'url'    => U('MruXf/addjm')
    							),
    							array(
    									'name'   => '加盟成员信息',
    									'url'    => U('MruXf/index2')
    							),
    							 
    					),
    					'tips' => array(
    							'你可以在这里管理加盟信息'
    					),
    					'Table_Header' => array(
    							'ID','加盟信息','查看'
    					),
    					'List_Opt' => array(
    							array(
    									'name' => '编辑',
    									'url'  => U('MruXf/savejm')
    							),
/*     							array(
    									'name' => '删除',
    									'url'  => U('MruXf/ck2')
    							), */
    					)
    			),
    			M('mru_jm')->where(array('token'=>$_SESSION['token']))->count(),
    			M('mru_jm')->field('id,content')->where(array('token'=>$_SESSION['token']))
    
    
    	);
    }
    
    
    public function savejm(){
    
    	$this->Edit('mru_jm',array(
    			
    			array('title'=>"加盟信息",'type'=>"textarea",'name'=>"content",'value'=>'content')
    
    	),U('MruXf/addjm', array('token' => $this->token)));
    
    }
    
    //排序  只 能主健是id,子段是sort 才能排序  有意见自己去开发！
    public function sortajax(){
    	$this->sortajaxTable('mru_xf');
    }
    
    
    
    
    public function hb(){
    	 
    	$list=M('mru_hb')->where(array('token'=>$_SESSION['token'],'state'=>1))->select();//被添加内容的表
    	foreach ($list as $k=>$v){
    		$list[$k]['name']=M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name')?M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name'):'总部';//把id子段改成value
    		$list[$k]['value']=$v['aid']?$v['aid']:0;//把id子段改成value
    	}
    	foreach ($list as $ke=>$v){
    		$list[$v['name']]['name']=$v['name'];
    		$list[$v['name']]['value']=$v['value'];
    		unset($list[$ke]);
    	}
    
    	$aWhere = array(
    			    'token' =>$_SESSION['token'],
    				'state' => 1
    			);
    
    	//搜索
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
    		$aWhere['token'] =$_SESSION['token'];
    		$aWhere['state'] =1;
    		//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    		$_SESSION['aWhere']=$aWhere;
    	}//结束
    
    	 /* $b=M('mru_hb')->field('id,aid,yzm,name,add_time,price,openid')->order("add_time desc")->where($aWhere)->select();
    	$str='';
    	foreach ($b as $ke => $v){
    		$str+=$v['price'];
    	}
    	echo "<script>alert('总价格".$str."')</script>"; */
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('MruXf/hb',array('token' =>$_SESSION['token'])),
    					'Head_Opt' => array(
    				/* 	                    array(
    					 'name'   => '算总价',
    							'url'    => U('zj')
    					),  */
    							 
    
    
      
    							/*           		array(
    							 'name'   => '导出数据',
    									'url'    => U('ExportExcel/MruXf')
    							), */
    							
    							array(
    									'name'   => '导出数据 ',
    									'url'    => U('ExportExcel/hb')
    							),
    							 
    					),
    					'tips' => array(
    							'你可以在这里管理信息'
    					),
    					'Table_Header' => array(
    							'ID', '店铺','验证码','获取方式', '使用时间','价格','用户手机','会员昵称','操作'
    					),
    					'List_Opt' => array(
    					/*                     array(
    					 'name' => '查看详情',
    							'url'  => U('MruXf/ck')
    					), */
    							array(
    									'name' => '删除',
    									'url'  => U('MruXf/hdl')
    							),
    					),
    					//搜索
    					'search'=>array(
    							//array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
    							//array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
    
    						    array('title'=>'分类','name'=>'eq_aid','type'=>'select','many'=>$list) 
    							//array('type'=>'select','title'=>"按店铺查询",'name'=>"eq_aid",'many'=>$list),
    							 
    					)//结束
    
    			),
    			M('mru_hb')->where($aWhere)->count(),
    			M('mru_hb')->field('id,aid,yzm,name,add_time,price,openid')->order("add_time desc")->where($aWhere),
    			array($this,'iabc')
    
    
    	);
    }
    
    
    public function iabc($data){
    	foreach($data as $k=>$v){
    		$data[$k]['aid']=M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name')?M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name'):'总部';
    		$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
    		$data[$k]['openid']=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('tel');
            $info = M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('name');
            $user = M("Wxuser")->where(array('token'=>$_SESSION['token']))->find();
            $data[$k]['openid2']= $info ? $info : M("Wxusers")->where(array('uid'=>$user['id'],'openid'=>$v['openid']))->getField('nickname');
    	
           
    		 
    	}
    	return $data;
    }
    
    
    public function iabcc($data){
    	foreach($data as $k=>$v){
    		$data[$k]['aid']=M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name')?M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name'):'总部';
    		$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
    		$data[$k]['openid']=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('tel');
    		$data[$k]['openid2']=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('name');
    		 
    	}
    	return $data;
    }
    
    public function hdl(){
    	$id=$_GET['id'];
    	$list=M('mru_hb')->where(array('id'=>$id))->delete();
    	if($list){
    		$this->error2("删除成功");
    	}else{
    		$this->error2("删除失败");
    	}
    	//print_r($list);
    	//echo 1 ;die;
    	$this->display();
    }
    
    public function qgjl(){
    	$id=$_GET['id'];
    	$list=M('mru_qgj')->where(array('id'=>$id))->delete();
    	if($list){
    		$this->error2("删除成功");
    	}else{
    		$this->error2("删除失败");
    	}
    	//print_r($list);
    }
    
    public function zj(){
    	 $b=M('mru_hb')->field('id,aid,yzm,name,add_time,price,openid')->order("add_time desc")->where(arra)->select();
    	 $str='';
    	foreach ($b as $ke => $v){
    	$str+=$v['price'];
    	}
    	echo "<script>alert('总价格".$str."')</script>"; 
    }
    
    
    
    
    
    public function qgj(){
    
    	$list=M('mru_qgj')->where(array('token'=>$_SESSION['token'],'state'=>1))->select();//被添加内容的表
    	foreach ($list as $k=>$v){
    		$list[$k]['name']=M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name')?M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name'):'总部';//把id子段改成value
    		$list[$k]['value']=$v['aid']?$v['aid']:0;//把id子段改成value
    	}
    	foreach ($list as $ke=>$v){
    		$list[$v['name']]['name']=$v['name'];
    		$list[$v['name']]['value']=$v['value'];
    		unset($list[$ke]);
    	}
    
    	$aWhere = array('token' =>$_SESSION['token'],
    			        'state' => 1
    			);
    
    	//搜索
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
    		$aWhere['token'] =$_SESSION['token'];
    		//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    		$aWhere['state'] =1;
    		//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    		$_SESSION['aWhere']=$aWhere;
    		
    	}//结束
    
    	/* $b=M('mru_hb')->field('id,aid,yzm,name,add_time,price,openid')->order("add_time desc")->where($aWhere)->select();
    	 $str='';
    	foreach ($b as $ke => $v){
    	$str+=$v['price'];
    	}
    	echo "<script>alert('总价格".$str."')</script>"; */
    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('MruXf/qgj',array('token' =>$_SESSION['token'])),
    					'Head_Opt' => array(
    							/* 	                    array(
    							 'name'   => '算总价',
    									'url'    => U('zj')
    							),  */
    
    
    
    
    							/*           		array(
    							 'name'   => '导出数据',
    									'url'    => U('ExportExcel/MruXf')
    							), */
    							
    							
    							array(
    									'name'   => '导出数据 ',
    									'url'    => U('ExportExcel/qgj')
    							),
    
    					),
    					'tips' => array(
    							'你可以在这里管理信息'
    					),
    					'Table_Header' => array(
    							'ID', '店铺','验证码','获取方式', '使用时间','价格','用户手机','会员昵称','操作'
    					),
    					'List_Opt' => array(
    							/*                     array(
    							 'name' => '查看详情',
    									'url'  => U('MruXf/ck')
    							), */
    							array(
    									'name' => '删除',
    									'url'  => U('MruXf/qgjl')
    							),
    					),
    					//搜索
    					'search'=>array(
    							//array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
    							//array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
    
    							array('title'=>'分类','name'=>'eq_aid','type'=>'select','many'=>$list)
    							//array('type'=>'select','title'=>"按店铺查询",'name'=>"eq_aid",'many'=>$list),
    
    					)//结束
    
    			),
    			M('mru_qgj')->where($aWhere)->count(),
    			M('mru_qgj')->field('id,aid,yzm,name,add_time,price,openid')->order("add_time desc")->where($aWhere),
    			array($this,'iabcc')
    
    
    	);
    }

}
?>
