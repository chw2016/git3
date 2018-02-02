<?php
/**
 *  理财
 **/
class JishiAction extends UserAction{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'User/default/jishi';

    /**
     *  Token
     **/
    private $_sToken = null;

    /**
     *  UID
     **/
    private $_iUID = null;

    /**
     *  顶部
     **/
    public function _initialize()
    {
        $this->_sToken = session('token');
        $this->_iUID   = session('uid');
        $this->assign(array(
            'Header' => array(
                array(
                    'name' => '理财产品',
                    'url'  => U('Jishi/index')
                ),
                array(
                    'name' => '产品分类',
                    'url'  => U('Jishi/type')
                ),
                array(
                    'name' => '地区管理',
                    'url'  => U('Jishi/area')
                ),
                array(
                    'name' => '理财师',
                    'url'  => U('Jishi/planner')
                ),
                array(
                    'name' => '订单管理',
                    'url'  => U('Jishi/order')
                ),
                array(
                    'name' => '配置管理',
                    'url'  => U('Jishi/ConfigShow')
                ),
            )
        ));
        parent::_initialize();
    }

    /**
     *  理财-产品
     **/
	public function index(){
        #页面表基本配置
        $this->assign(array(
            //'id' => 'id',//如果主键不是id，则需要设置
            'HeadHover' => U('Jishi/index'),
            'Head_Opt' => array(
                array(
                    'name'   => '添加产品',
                    'url'    => U('Jishi/ProductAdd')
                )
            ),
            'Table_Header' => array(
                'ID', '名称', '图片', '地区', '理财师', '类型', '实体价', '价格', '操作'
            ),
            'aListImg' => array(
                'container' => array('image'),
                'width'     => 70,
                'height'    => 70
            ),
            'List_Opt' => array(
                array(
                    'name' => '编辑',
                    'url'  => U('Jishi/ProductEdit')
                ),
                array(
                    'name' => '删除',
                    'url'  => U('Jishi/ProductDel')
                ),
            )
        ));
        #产品数据
		$DB              = D('finances_product');
		$aWhere['token'] = $this->_sToken;
		$iCount = $DB->where($aWhere)->count();
		$Page   = new Page($iCount,25);
		$aInfo  = $DB->table('tp_finances_product p')->field('
            p.id, p.title, p.image, a.title as aTitle, pl.name as plName,
            pt.title as ptTitle, p.entity_price, p.price')
            ->where(array('p.token' => $this->_sToken))
            ->join('tp_finances_area a on a.id = p.finances_area_id')
            ->join('tp_finances_planner pl on pl.id = p.finances_planner_id')
            ->join('tp_finances_product_type pt on pt.id = p.finances_product_type_id')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

		$this->assign(array(
            'page'     => $Page->show(),
            'aList'    => $aInfo
        ));
		$this->UDisplay('show');
	}

    /**
     *  添加-产品
     **/
	public function ProductAdd(){
		if(IS_POST){
			$Product       = D('Finances_product');
			$_POST['token']= $this->_sToken;
			if(($aData = $Product->create()) != false){
				if($Product->add()){
					$this->success2('产品添加成功',U('Jishi/index'));
				}else{
					$this->error2('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error2($Product->getError());
			}
		}else{
            $this->addEditPageData();
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Jishi/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
                'FormUrl'  => U('Jishi/ProductAdd',array('token'=>$_SESSION['token'])),
		        'planner' =>  D('finances_planner')->field('id, name')
			->where(array('token' => $this->_sToken))
			->select()
            ));
            $this->UDisplay('product_add');
		}
	}
    public function addEditPageData()
    {
        $this->AssignArea();
        $this->assign(array(
            /*'planner' => array_merge(array('-1' => '请选择理财师'), Arr::changeIndexToKVMap(
                D('finances_planner')->field('id, name')
                ->where(array('token' => $this->_sToken))
                ->select(), 'id', 'name')),*/

            'product_type' => array_merge(array('-1' => '请选择产品类型'), Arr::changeIndexToKVMap(
                D('finances_product_type')->field('id, title')
                ->where(array('token' => $this->_sToken))
                ->select(), 'id', 'title')),
        ));
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

	public function ProductEdit(){
		if(IS_POST){
			$_POST['id']    = FC::P('id');
			$_POST['token'] = $this->_sToken;
			$aWhere         = array(
                'id'    => FC::P('id'),
                'token' => FC::P('token')
            );
            $Product = D('finances_product');
			$Item    = $Product->where($aWhere)->find();
			if($Item == false) $this->error2('非法操作');
			if($Product->create()){
				if($iID = $Product->where($aWhere)->save($_POST)){
					$this->success2('修改成功');
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($Product->getError());
			}
		}else{
            $this->addEditPageData();
			$iID     = $this->_get('id');
			$Product = D('finances_product')->where(array(
                'id' => $iID,
                'token' => $this->_sToken
            ))->find();
			if($Product == false) $this->error2('非法操作');
			$this->assign(array(
                'product' => $Product,
                'id' => $iID,
                'FormUrl' => U('Jishi/ProductEdit',array('token'=>$_SESSION['token'])),
                'planner' =>  D('finances_planner')->field('id, name')
                    ->where(array('token' => $this->_sToken))
                    ->select(),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Jishi/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
            ));
            $this->UDisplay('product_add');
		}
	}

    /**
     *  删除产品
     **/
	public function ProductDel(){
		$iID     = $this->_get('id');
		$aWhere  = array('id'=>$iID,'token' => $this->_sToken);
		$Product = D('finances_product');
		$bCheck  = $Product->where($aWhere)->find();
		if($bCheck == false) $this->error2('非法操作');
		if($Product->where($aWhere)->delete()) {
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
	}

    //////////////////////////产品地区////////////////////////////////////
    /**
     *  理财-产品地区
     **/
	public function area(){
        #页面表基本配置
        $this->assign(array(
            //'id' => 'id',//如果主键不是id，则需要设置
            'HeadHover' => U('Jishi/area'),
            'Head_Opt' => array(
                array(
                    'name'   => '添加地区',
                    'url'    => U('Jishi/AreaAdd')
                )
            ),
            'Table_Header' => array(
                'ID', '名称', '操作'
            ),
            'List_Opt' => array(
                array(
                    'name' => '编辑',
                    'url'  => U('Jishi/AreaEdit')
                ),
                array(
                    'name' => '删除',
                    'url'  => U('Jishi/AreaDel')
                ),
            )
        ));
        #产品分类数据
		$DB              = D('finances_area');
		$aWhere['token'] = $this->_sToken;
		$iCount = $DB->where($aWhere)->count();
		$Page   = new Page($iCount,25);
		$aInfo  = $DB->table('tp_finances_area')->field(' id, title')
            ->where($aWhere)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

		$this->assign(array(
            'page'     => $Page->show(),
            'aList'    => $aInfo
        ));
		$this->UDisplay('show');
	}

    /**
     *  添加-分类
     **/
	public function AreaAdd(){
		if(IS_POST){
			$Area           = D('Finances_area');
			if($Area->create() != false){
				if($Area->add()){
					$this->success2('恭喜：添加地区成功',U('Jishi/area'));
				}else{
					$this->error2('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error2($Area->getError());
			}
		}else{
            $this->assign(array(
                'FormUrl' => U('Jishi/AreaAdd',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Jishi/area', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
            ));
            $this->UDisplay('area_add');
		}
	}

	public function AreaEdit(){
		if(IS_POST){
			$_POST['id']    = FC::P('id');
			$_POST['token'] = $this->_sToken;
			$aWhere         = array(
                'id'    => FC::P('id'),
                'token' => FC::P('token')
            );
            $Area = D('finances_area');
			$Item    = $Area->where($aWhere)->find();
			if($Item == false) $this->error2('非法操作');
			if($Area->create()){
				if($Area->where($aWhere)->save($_POST)){
					$this->success2('修改成功');
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($Area->getError());
			}
		}else{
			$iID     = $this->_get('id');
			$Area    = D('finances_area')->where(array(
                'id' => $iID,
                'token' => $this->_sToken
            ))->find();
			if($Area == false) $this->error2('非法操作');
			$this->assign(array(
                'product' => $Area,
                'id' => $iID,
                'FormUrl' => U('Jishi/AreaEdit',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Jishi/area', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
            ));
            $this->UDisplay('area_add');
		}
	}



    /**
     *  删除地区
     **/
	public function AreaDel(){
		$iID     = $this->_get('id');
		$aWhere  = array('id'=>$iID,'token' => $this->_sToken);
		$Area    = D('finances_area');
		$bCheck  = $Area->where($aWhere)->find();
		if($bCheck == false) $this->error2('非法操作');
		if($Area->where($aWhere)->delete()) {
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
	}
    //////////////////////////地区////////////////////////////////////

    //////////////////////////分类管理 ////////////////////////////////////
    /**
     *  理财-分类
     **/
	public function type(){
        #页面表基本配置
        $this->assign(array(
            //'id' => 'id',//如果主键不是id，则需要设置
            'HeadHover' => U('Jishi/type'),
            'Head_Opt' => array(
                array(
                    'name'   => '添加分类',
                    'url'    => U('Jishi/TypeAdd')
                )
            ),
            'Table_Header' => array(
                'ID', '名称', '操作'
            ),
            'List_Opt' => array(
                array(
                    'name' => '编辑',
                    'url'  => U('Jishi/TypeEdit')
                ),
                array(
                    'name' => '删除',
                    'url'  => U('Jishi/TypeDel')
                ),
            )
        ));
        #产品分类数据
		$DB              = D('finances_product_type');
		$aWhere['token'] = $this->_sToken;
		$iCount = $DB->where($aWhere)->count();
		$Page   = new Page($iCount,25);
		$aInfo  = $DB->table('tp_finances_product_type')->field('id, title')
            ->where($aWhere)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

		$this->assign(array(
            'page'     => $Page->show(),
            'aList'    => $aInfo,
            'ExtraBtn' => array(
                array(
                    'url'  => U('Jishi/index', array('token' => $this->_sToken)),
                    'name' => '返回'
            ))
        ));
		$this->UDisplay('show');
	}

    /**
     *  添加-分类
     **/
	public function TypeAdd(){
		if(IS_POST){
            $_POST['add_time'] = date('Y-m-d H:i:s');
			$ProductType = D('Finances_product_type');
            //$ProductType->add_time = date('Y-m-d H:i:s');
			if($ProductType->create() != false){
				if($ProductType->add()){
					$this->success2('恭喜：分类添加成功',U('Jishi/type'));
				}else{
					$this->error2('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error2($ProductType->getError());
			}
		}else{
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Jishi/type', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
                'FormUrl' => U('Jishi/TypeAdd',array('token'=>$_SESSION['token']))
            ));
            $this->UDisplay('type_add');
		}
	}

	public function TypeEdit(){
        $Type = D('finances_product_type');
		if(IS_POST){
			$_POST['id']    = FC::P('id');
			$_POST['token'] = $this->_sToken;
			$aWhere         = array(
                'id'    => FC::P('id'),
                'token' => FC::P('token')
            );
			$Item    = $Type->where($aWhere)->find();
			if($Item == false) $this->error2('非法操作');
			if($Type->create()){
				if($Type->where($aWhere)->save($_POST)){
					$this->success2('修改成功');
				}else{
					$this->error2('操作失败');
				}
			}else{
				$this->error2($Type->getError());
			}
		}else{
			$iID     = $this->_get('id');
			$Type    = $Type->where(array(
                'id' => $iID,
                'token' => $this->_sToken
            ))->find();
			if($Type == false) $this->error2('非法操作');
			$this->assign(array(
                'product' => $Type,
                'id' => $iID,
                'FormUrl' => U('Jishi/TypeEdit',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Jishi/type', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
            ));
            $this->UDisplay('type_add');
		}
	}


    /**
     *  删除分类
     **/
	public function TypeDel(){
		$iID     = $this->_get('id');
		$aWhere  = array('id'=>$iID,'token' => $this->_sToken);
		$ProductType = D('finances_product_type');
		$bCheck  = $ProductType->where($aWhere)->find();
		if($bCheck == false) $this->error2('非法操作');
		if($ProductType->where($aWhere)->delete()) {
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
	}
    //////////////////////////产品分类 END////////////////////////////////////



    //////////////////////////理财师管理 ////////////////////////////////////
    /**
     *  理财-理财师
     **/
	public function planner(){
        #页面表基本配置
        $this->assign(array(
            //'id' => 'id',//如果主键不是id，则需要设置
            'HeadHover' => U('Jishi/planner'),
            'Head_Opt' => array(
                array(
                    'name'   => '添加理财师',
                    'url'    => U('Jishi/PlannerAdd')
                )
            ),
            'Table_Header' => array(
                'ID', '地区', '姓名', '头像', '类型', '星级',
                '专业等级', '沟通等级', '服务等级', '接单数量', '操作'
            ),
            'aListImg' => array(
                'container' => array('image'),
                'width'     => 70,
                'height'    => 70
            ),
            'List_Opt' => array(
                array(
                    'name' => '编辑',
                    'url'  => U('Jishi/PlannerEdit')
                ),
                array(
                    'name' => '删除',
                    'url'  => U('Jishi/PlannerDel')
                ),
            )
        ));

        #产品分类数据
		$DB              = D('finances_planner');
		$aWhere['token'] = $this->_sToken;
		$iCount = $DB->where($aWhere)->count();
		$Page   = new Page($iCount,25);
		$aInfo  = $DB->table('tp_finances_planner p')->field('
            p.id, a.title as areaName, p.name, p.image, p.type, p.stars,
            p.specialty_level, p.communication_level, p.service_level, p.order_num')
            ->join('tp_finances_area a on a.id = p.finances_area_id')
            ->where(array('p.token' => $this->_sToken))
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        if ($aInfo) foreach($aInfo as $iK => $aItem) {
            foreach ($aItem as $sField => $Item) {
                if ('type' == $sField) {
                    $aInfo[$iK][$sField] = $Item == 1 ? '外部' : '内部';
                }
            }
        }

		$this->assign(array(
            'page'     => $Page->show(),
            'aList'    => $aInfo
        ));
		$this->UDisplay('show');
	}

    public function AssignPlan()
    {
        $this->AssignArea();
        $Model = D('Finances_planner');
        $this->assign(array(
            'stars' => $Model->getStars(),
            'specialty_level' => $Model->getSpecialtyLevel(),
            'communication_level' => $Model->getCommunicationLevel(),
            'service_level' => $Model->getServerLevel()
        ));
    }

    /**
     *  添加-理财师
     **/
	public function PlannerAdd(){
		if(IS_POST){
			$Model  = D('finances_planner');
			if($Model->create() != false){
				if($Model->add()){
					$this->success2('恭喜：添加理财师成功',U('Jishi/planner'));
				}else{
					$this->error2('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error2($Model->getError());
			}
		}else{
            $this->AssignPlan();
            $this->assign(array(
                'FormUrl' => U('Jishi/PlannerAdd',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Jishi/planner', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
            ));
            $this->UDisplay('planner_add');
		}
	}

	public function PlannerEdit(){
        $Model = D('finances_planner');
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
				$this->error2($Type->getError());
			}
		}else{
			$iID     = $this->_get('id');
			$Planner = $Model->where(array(
                'id' => $iID,
                'token' => $this->_sToken
            ))->find();
			if($Planner == false) $this->error2('非法操作');
			$this->assign(array(
                'info' => $Planner,
                'id' => $iID,
                'FormUrl' => U('Jishi/PlannerEdit',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U('Jishi/planner', array('token' => $this->_sToken)),
                        'name' => '返回'
                )),
            ));
            $this->AssignPlan();
            $this->UDisplay('planner_add');
		}
	}

    /**
     *  删除理财师
     **/
	public function PlannerDel(){
		$iID     = $this->_get('id');
		$aWhere  = array('id'=>$iID,'token' => $this->_sToken);
		$Model   = D('finances_planner');
		$bCheck  = $Model->where($aWhere)->find();
		if($bCheck == false) $this->error2('非法操作');
		if($Model->where($aWhere)->delete()) {
			$this->success2('删除成功');
		}else{
			$this->error2('操作失败');
		}
	}
    //////////////////////////理财师////////////////////////////////////


    //////////////////////////订单管理////////////////////////////////////
    /**
     *  理财-订单
     **/
	public function order(){
        #页面表基本配置
        #
        $this->assign(array(
            //'id' => 'id',//如果主键不是id，则需要设置
            'HeadHover' => U('Jishi/order'),
            'Head_Opt' => array(),
            'Table_Header' => array(
                'ID', '预约号', '理财师名称', '理财师图片', '名称',
                '预约时间', '支付金额', '是否支付', '订单状态', '下单时间', '操作'
            ),
            'aListImg' => array(
                'container' => array('finances_planner_image', 'finances_product_image'),
                'width'     => 70,
                'height'    => 70
            ),
            'List_Opt' => array(
                array(
                    'name' => '查看',
                    'url'  => U('Jishi/OrderShow')
                ),
            )
        ));

        #产品分类数据
		$Model           = D('Finances_order');
		$aWhere['token'] = $this->_sToken;
		$iCount = $Model->where($aWhere)->count();
		$Page   = new Page($iCount,25);

		$aInfo  = $Model->field('
            id, order_num, finances_planner_name, finances_planner_image,
            finances_product_title,
            time, price, is_paid, status, add_time
            ')
            ->where($aWhere)
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

	public function OrderShow(){
        $iID    = (int)$this->_get('id');
		$Model  = D('Finances_order');
        $aInfo  = $Model->where(array('id' => $iID, 'token' => $this->_sToken))->select();
        if (!$aInfo) {
            $this->error2('订单不存在', U('Jishi/order'));
            return false;
        }
        $this->assign(array(
            'aListImg' => array(
                'container' => array('理财师的图片', '产品的图片'),
                'width'     => 150,
                'height'    => 150
            ),
            'info' => array_combine(array(
                'id',
                '识别码',
                '用户识别码',
                '预约号',
                '理财师的名称',
                '理财师的图片',
                '产品的名称',
                '产品的图片',
                '产品的描述',
                '预约时间',
                '电话',
                '详细地址',
                '支付金额',
                '是否支付',
                '订单状态',
                '预约时间',
                '下单时间'
            ), array_values($aInfo[0]))
        ));
        $this->UDisplay('order_show');
	}


    //////////////////////////配置信息////////////////////////////////////
	public function ConfigShow()
    {
		$Model  = D('Finances_config');
        $aInfo  = $Model->where(array('token' => $this->_sToken))->select();
        $aInfo  = count($aInfo) > 0 ? $aInfo[0] : null;
        $this->assign(array(
            'info' => $aInfo,
            'ExtraBtn' => array(
                array(
                    'url'  => U('Jishi/index', array('token' => $this->_sToken)),
                    'name' => '返回'
                )
            ),
        ));
        $this->UDisplay('config_edit');
	}
    public function ConfigSave()
    {
        $Model          = D('Finances_config');
        $_POST['token'] = $this->_sToken;
        if($Model->create() != false){
            $Config = $Model->where(array('token' => $this->_sToken))->find();
            if (!$Config) {
                if($Model->add()){
                    $this->success2('恭喜：配置成功',U('Jishi/ConfigShow'));
                }else{
                    $this->error2('服务器繁忙,请稍候再试');
                }
            }else if($Model->where(array('token' => $this->_sToken))->save($_POST)){
                $this->success2('恭喜：配置成功',U('Jishi/ConfigShow'));
            }else{
                $this->error2('服务器繁忙,请稍候再试');
            }
        }else{
            $this->error2($Model->getError());
        }
    }
}
?>
