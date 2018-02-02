<?php
/**
 * PC端
 * @author NICK
 *
 */
class 	RepairAction extends UserAction {
	
	public $token;
	public $openid;
	public $userModel;
	public $userInfoData;
	static public $treeList = array();

	public function _initialize() {
		
		parent::_initialize();
		if (!session('?token')) {
			session('token', $_GET['token']);
		}

		$this->token = session('token');
		$this->assign('token', $this->token);
		$this->userModel = M('wxuser');
		$this->userInfoData = $this->userModel->where(array('token'=>$this->token))->find();		
	} 
	
	/*基础页面展示*/
	public function index() {
		if (!empty($this->userInfoData)) {			
			$condition['wxuser_id'] = $this->userInfoData['id'];
			/*查看所有订单数*/
			$orderCount = M('repair_order')->where($condition)->count();
			$this->assign('orderCount', $orderCount);
			
			/*查看业务推广员量*/
			$salerCount = M('repair_saler')->where($condition)->count();
			$this->assign('salerCount', $salerCount);

			/*查看建议投诉量*/
			$compCount = M('repair_complain')->where($condition)->count();
			$this->assign('compCount', $compCount);
			
			/*查看未审核服务商用户量*/
			$condition['is_review'] = 1;
			$agentCount = M('repair_agent')->where($condition)->count();
			$this->assign('agentCount', $agentCount);
			
			/*查看所有审核服务商用户量*/
			$condition['is_review'] = array('neq', 0);			
			import('ORG.Util.Page');
			$count = M('repair_agent')->where($condition)->count();
			$page = new Page($count, 4);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_agent')->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);			
		}
		$this->display();
	}
	
	/*****************************************服务订单管理*************************************/
	
	/*订单展示*/
	public function order() {
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		/*
		if (0 == $type) {
			/*服务订单管理入口基本展示*/
		/*} elseif (1 == $type) {
			/*未处理的订单展示*/
		/*} elseif (2 == $type) {
			
		}*/
		/*查找省*/
		$province = M('area')->where(array('level'=>1))->select();
		$this->assign('province', $province);
				
		if (isset($_GET['status'])) {
			$status = trim($_GET['status']);
			$area = trim($_GET['area']);
			$condition['order_air'] = array(
					array('like', "%".$area."%"),
			);
			$this->assign('status', $status);
			$this->assign('area', $area);
		}
		if (0 == $type) {
			/*所有的订单*/
			$condition['wxuser_id'] = $this->userInfoData['id'];
			
			import('ORG.Util.Page');
			$count = M('repair_order')->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_order')->where($condition)->order('id desc')->page($nowPage.','.$page->listRows)->select();
			foreach ($list as $key => $value) {
				
			}
			$list = $this->getPipeiJishu($list);
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			
			$this->assign('type', $type);
			$this->display();
		} elseif (2 == $type){
			$condition['wxuser_id'] = $this->userInfoData['id'];
			$condition['status'] = array(array('eq',2),array('eq',3), 'or');
				
			import('ORG.Util.Page');
			$count = M('repair_order')->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_order')->where($condition)->order('id desc')->page($nowPage.','.$page->listRows)->select();
			$list = $this->getPipeiJishu($list);
			$show = $page->show();
			$this->assign('isShow', 1);
			$this->assign('list', $list);
			$this->assign('page', $show);
			
			$this->assign('type', $type);
			$this->display();
		} else {
			$condition['wxuser_id'] = $this->userInfoData['id'];
			$condition['status'] = $type;
			
			import('ORG.Util.Page');
			$count = M('repair_order')->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_order')->where($condition)->order('id desc')->page($nowPage.','.$page->listRows)->select();
			$list = $this->getPipeiJishu($list);
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);

			$this->assign('type', $type);
			$this->display();	
		}	
		
	}

	public function  getPipeiJishu($list){
		$repairAgent = M('repair_agent');
		foreach ($list as $key => $value) {
			# code...
			$nowStaff = M('repair_staff')->where(array('agent_id'=>$value['agent_id']))->find();
			$condition = array();
			$condition['area'] = array('like','%'.$value['order_air'].'%');
			$condition['bigclass'] = array('like','%'.$value['service_type'].'%');
			$condition['smallclass'] = array('like','%'.$value['repair_ele'].'%');
			$condition['is_review'] = 2;
			$condition['is_forbidden'] = 0;
			$repairUsers = $repairAgent->where($condition)->select();
			foreach ($repairUsers as $k => $v) {
				
				$staff = M('repair_staff')->where(array('agent_id'=>$v['id']))->find();
				if($nowStaff['id'] == $staff['id']){
					$repairUsers[$k]['staff_is_now'] = 1;
				}else{
					$repairUsers[$k]['staff_is_now'] = 0;
				}
				$repairUsers[$k]['staff_name'] = $staff['staff_name'];
				$repairUsers[$k]['staff_telphone'] = $staff['contact_tel'];
				$repairUsers[$k]['agent_id'] = $staff['agent_id'];
			}
			$list[$key]['staff_users'] = $repairUsers;
		}
		return $list;
	}

    /*订单删除*/
    public function del_order() {

        if (isset($_REQUEST['id'])) {
            $orderId = trim($_REQUEST['id']);
            $orderModel = M('repair_order');
            $res = $orderModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$orderId))->delete();
            if($res){
                $this->success('操作成功',U(MODULE_NAME.'/order',array('token'=>$this->token)));
            }else{
                $this->error('操作失败',U(MODULE_NAME.'/order',array('token'=>$this->token)));
            }
        }
    }

	
	/*订单详情*/
	public function detail() {
		
		if (isset($_GET['id'])) {
			$orderId = trim($_GET['id']);
			$orderModel = M('repair_order');
			$orderDatas = $orderModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$orderId))->find();
            $staff = M('repair_staff')->where(array('agent_id'=>$orderDatas['agent_id']))->find();
            $orderDatas['staff_name'] = $staff['staff_name'];
			if (!empty($orderDatas)) {
				$this->assign('orderDatas', $orderDatas);
			} 
		}
		$this->display();
	}
	
	/*导出数据*/
	public function export() {
		
		if (isset($_GET['op'])) {
			$op = trim($_GET['op']);
			if (0 == $op) {
				if (isset($_GET['status'])) {
					$status = trim($_GET['status']);
					$area = trim($_GET['area']);
					$condition['order_air'] = array(
							array('like', "%".$area."%"),
					);
				}
				$type = trim($_GET['type'])?trim($_GET['type']):0;
				$condition['wxuser_id'] = $this->userInfoData['id'];
				if (0 == $type) {
					$dataArray = M('repair_order')->where($condition)->order('id desc')->select();
				} elseif (2 == $type){
					$condition['status'] = array(array('eq',2),array('eq',3), 'or');
					$dataArray = M('repair_order')->where($condition)->order('id desc')->select();
				} else {
					$condition['status'] = $type;
					$dataArray = M('repair_order')->where($condition)->order('id desc')->select();
				}
					
				
				$array = array();
				$array[0] = array(
						'订单编号',
						'预约处理日期',
						'服务维修电器名',
						'故障说明',
						'下单时间',
						'下单人的姓名',
						'下单人的手机号码',
						'下单详细地址',
						'付款金额',
						'服务商门店名',
						'维修员工名称',
						'维修完成时间'
				);
				foreach ($dataArray as $key=>$value) {
					if (is_array($value) && !empty($value)) {
						$array[$key+1] = array(
								$value['order_nid'],
								$value['appoint_time'],
								$value['repair_ele'],
								$value['fault_info'],
								$value['order_time'],
								$value['order_name'],
								$value['order_tel'],
								$value['order_address'],
								$value['pay_money'],
								$value['agent_name'],
								$value['staff_name'],
								date('Y-m-d H:i:s', $value['finish_time'])
						);
					}
				}
			} elseif (1 == $op) {
				$type = trim($_GET['type'])?trim($_GET['type']):0;
				if (isset($_GET['status'])) {
					$status = trim($_GET['status']);
					$area = trim($_GET['area']);
					$condition['area'] = array(
							array('like', "%".$area."%"),
					);
				}
				$condition['wxuser_id'] = $this->userInfoData['id'];
				if (0 == $type) {
					$dataArray = M('repair_agent')->where($condition)->order('id desc')->select();
				} elseif (3 == $type) {
					$condition['is_forbidden'] = 1;
					$dataArray = M('repair_agent')->where($condition)->order('id desc')->select();
				} else {
					$condition['is_review'] = $type;
					$dataArray = M('repair_agent')->where($condition)->order('id desc')->select();
				}
				
				$array = array();
				$array[0] = array(
						'门店名称',
						'负责人姓名',
						'成立时间',
						'营业执照编号',
						'门店地址',
						'身份证号',
						'手机号码',
						'维修经验',
						'服务区域',
						'服务大类',
						'服务小类',
						'具体服务',
						'服务内容',
						'签到时间',
				);
				foreach ($dataArray as $key=>$value) {
					if (is_array($value) && !empty($value)) {
                        $staff = M('repair_staff')->where(array('agent_id'=>$value['id']))->find();
                        $value['head_name'] = $staff['staff_name'];
						$array[$key+1] = array(
								$value['store_name'],
								$value['head_name'],
								$value['found_time'],
								$value['license'],
								$value['agent_address'],
								$value['card_no'],
								$value['telephone'],
								$value['repair_time'],
								$value['area'],
								$value['bigclass'],
								$value['smallclass'],
								$value['service'],
								$value['content'],
								date('Y-m-d H:i:s', $value['sign_time'])
						);
					}
				}
			} elseif (2 == $op) {
				if (isset($_GET['status'])) {
					$status = trim($_GET['status']);
					$area = trim($_GET['area']);
					$condition['area'] = array(
							array('like', "%".$area."%"),
					);
				}
				$condition['wxuser_id'] = $this->userInfoData['id'];
				$dataArray = M('repair_user')->where($condition)->order('id desc')->select();
				
				$array = array();
				$array[0] = array(
						'用户姓名',
						'手机号码',
						'用户区域',
						'用户地址',
						'推荐用户的人',
						'用户的金额数',
						'用户的积分数',
				);
				foreach ($dataArray as $key=>$value) {
					if (is_array($value) && !empty($value)) {
						$array[$key+1] = array(
								$value['username'],
								$value['user_tel'],
								$value['area'],
								$value['address'],
								$value['referee'],
								$value['money'],
								$value['score'],
						);
					}
				}
			}
		}
		$this->array_to_excel($array);		
	}
	
	/**/
	public function array_to_excel($array) {
		if (is_array($array) && !empty($array)) {
			import('@.ORG.PHPExcel.PHPExcel');
			$objPHPExcel = new PHPExcel();
			
			/*设置标题*/
			$objPHPExcel->getProperties()->setTitle(time())->setDescription('none');
			
			/*读取数据，设置单元格的值*/			
			$i = 0;
			$j = 0;
			while (list($key, $value) = each($array)) {			
				if (is_array($value) && !empty($value)) {
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($j)->setWidth(16);
					$objPHPExcel->getActiveSheet()->getStyle('A1:AE'.($j+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('A1:AE'.($j+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
					while (list($k, $val) = each($value)) {
						if (strlen($val) <= 10) {
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(10);
						}elseif(( strlen($val) > 10 ) && ( strlen($val) < 20 )) {
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(20);
						}elseif(( strlen($val) >= 20) && ( strlen($val) <= 40 )) {
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(45);
						}else {
							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(60);
						}
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i,$j+1,$val);
						$i++;
					}
					$i = 0;
				}
				$j++;
			}
			$objPHPExcel->setActiveSheetIndex(0);
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
			header("Content-Type:application/force-download");
			header("Content-Type:application/vnd.ms-execl");
			header("Content-Type:application/octet-stream");
			header("Content-Type:application/download");
			header("Content-Disposition:attachment;filename=".date('Ymd').time().".xls");
			header("Content-Transfer-Encoding:binary");
			$objWriter->save('php://output');
		} elseif (is_string($array)) {
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->setCellValue('A1',$array);
		}
		
	}
	
	/****************************************服务商管理********************************/
	
	/*服务商的基本显示*/
	public function agent(){
		$type = trim($_REQUEST['type'])?trim($_REQUEST['type']):0;
		/*查找省*/
		$province = M('area')->where(array('level'=>1))->select();
		$this->assign('province', $province);
		if (isset($_REQUEST['status'])) {
			$status = trim($_REQUEST['status']);
			$area = trim($_REQUEST['area']);
			$condition['area'] = array(
				array('like', "%".$area."%"),
			);
			$this->assign('status', $status);
			$this->assign('area', $area);
		}
        if(isset($_REQUEST['start_date']) && isset($_REQUEST['end_date'])){
            $condition['add_time'] = array('between',array(strtotime($_REQUEST['start_date']),strtotime($_REQUEST['end_date'])));
            $this->assign('start_date',$_REQUEST['start_date']);
            $this->assign('end_date',$_REQUEST['end_date']);
        }
		/*所有的服务商*/
		if (0 == $type) {
			$condition['wxuser_id'] = $this->userInfoData['id'];
				
			import('ORG.Util.Page');
			$count = M('repair_agent')->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_agent')->where($condition)->order('is_review')->page($nowPage.','.$page->listRows)->order('add_time asc')->select();
			foreach($list as $k=>$v){
                $staff = M('repair_staff')->where(array('agent_id'=>$v['id']))->find();
                $wxusers = M('Wxusers')->where(array('id'=>$v['wxusers_id']))->find();
                $list[$k]['sname'] = $staff['staff_name'];
                $list[$k]['nickname'] = $wxusers['nickname'];
            }
            $show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);	
			$this->assign('type', $type);
			$this->display();
		}elseif (1 == $type) {
            $condition['wxuser_id'] = $this->userInfoData['id'];
            $condition['is_review'] = 1;
            import('ORG.Util.Page');
            $count = M('repair_agent')->where($condition)->count();
            $page = new Page($count, 20);
            $nowPage = isset($_GET['p'])?$_GET['p']:1;
            $list = M('repair_agent')->where($condition)->order('is_review')->page($nowPage.','.$page->listRows)->order('add_time asc')->select();
            foreach($list as $k=>$v){
                $staff = M('repair_staff')->where(array('agent_id'=>$v['id']))->find();
                $wxusers = M('Wxusers')->where(array('id'=>$v['wxusers_id']))->find();
                $list[$k]['sname'] = $staff['staff_name'];
                $list[$k]['nickname'] = $wxusers['nickname'];
            }
            $show = $page->show();
            $this->assign('list', $list);
            $this->assign('page', $show);

            $this->assign('type', $type);
            $this->display();
        }elseif (2 == $type) {
            $condition['wxuser_id'] = $this->userInfoData['id'];
            $condition['is_review'] = 2;
            import('ORG.Util.Page');
            $count = M('repair_agent')->where($condition)->count();
            $page = new Page($count, 20);
            $nowPage = isset($_GET['p'])?$_GET['p']:1;
            $list = M('repair_agent')->where($condition)->order('is_review')->page($nowPage.','.$page->listRows)->order('add_time asc')->select();
            foreach($list as $k=>$v){
                $staff = M('repair_staff')->where(array('agent_id'=>$v['id']))->find();
                $wxusers = M('Wxusers')->where(array('id'=>$v['wxusers_id']))->find();
                $list[$k]['sname'] = $staff['staff_name'];
                $list[$k]['nickname'] = $wxusers['nickname'];
            }
            $show = $page->show();
            $this->assign('list', $list);
            $this->assign('page', $show);

            $this->assign('type', $type);
            $this->display();
        }elseif (3 == $type) {
			$condition['wxuser_id'] = $this->userInfoData['id'];
			//$condition['is_forbidden'] = 1;
			$condition['is_review'] = 3;

			import('ORG.Util.Page');
			$count = M('repair_agent')->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_agent')->where($condition)->order('is_review')->page($nowPage.','.$page->listRows)->order('add_time asc')->select();
            foreach($list as $k=>$v){
                $staff = M('repair_staff')->where(array('agent_id'=>$v['id']))->find();
                $wxusers = M('Wxusers')->where(array('id'=>$v['wxusers_id']))->find();
                $list[$k]['sname'] = $staff['staff_name'];
                $list[$k]['nickname'] = $wxusers['nickname'];
            }
            $show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			$this->assign('type', $type);
			$this->display();
			
		}else {
			/*未审核和已经审核的服务商*/
			$condition['wxuser_id'] = $this->userInfoData['id'];
			$condition['is_review'] = $type;
				
			import('ORG.Util.Page');
			$count = M('repair_agent')->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_agent')->where($condition)->page($nowPage.','.$page->listRows)->order('add_time asc')->select();
            foreach($list as $k=>$v){
                $staff = M('repair_staff')->where(array('agent_id'=>$v['id']))->find();
                $wxusers = M('Wxusers')->where(array('id'=>$v['wxusers_id']))->find();
                $list[$k]['sname'] = $staff['staff_name'];
                $list[$k]['nickname'] = $wxusers['nickname'];
            }
            $show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
				
			$this->assign('type', $type);
			$this->display();
		}
	}
	
	/*服务商审核*/
	public function review() {
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		$this->assign('type', $type);
		if (0 == $type) {
			/*审核*/
			if (IS_POST) {
				if (isset($_GET['id'])) {
					$agentId = trim($_GET['id']);
					$check = trim($_POST['check']);
					if (1 == $check) {
						$find = M('repair_agent')->where(array('id'=>$agentId, 'is_review'=>1))->find();
                        $agentStaff = M('repair_staff')->where(array('agent_id'=>$find['id']))->find();
                        $openid = M('wxusers')->where(array('id'=>$find['wxusers_id']))->field('openid')->find();
						if (!empty($find)) {
                            $update = M('repair_agent')->where(array('id'=>$find['id']))->save(array('is_review'=>2,'add_time'=>time(),'op_reason'=>trim($_POST['op_reason'])));
                            $openid = $openid['openid'];
                            $templateData['touser'] = $openid;
                            $templateData['template_id'] = 'VlCxjIYJH5hxtt9FxSH36ZKPusxM20tHHprCStzGZXg';
                            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=index&token=".$this->token."&openid=".$openid;
                            $templateData['topcolor']="#FF0000";
                            $templateData['data']['first'] = array('value'=>$find['head_name']."您好!您的加盟申请通过了.",'color'=>"#173177");
                            $templateData['data']['keyword1'] = array('value'=>$find['agent_no'],'color'=>"#173177");
                            $templateData['data']['keyword2'] = array('value'=>$agentStaff['staff_name'],'color'=>"#173177");
                            $templateData['data']['keyword3'] = array('value'=>'通过','color'=>"#173177");
                            $templateData['data']['remark'] = array('value'=>"你好，你的加盟申请已通过审核，欢迎你的加盟！从现在起你就可以接收来自1号服务的家电安装、维修订单，请留意服务号的新订单消息，点击订单消息即可进行接单操作。系统将免费赠送100元，可接收50个订单。50单使用完毕后，需充值方可继续使用。收费标准不得高于1号服务的收费标准，收费标准请参考【用户专区】→【收费标准】。",'color'=>"#173177");
                            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
                            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
                            $data = $this->api_notice_increment($url,http_build_query($postdata));

                            if(!$data){
                                $this->api_notice_increment($url,http_build_query($postdata));
                            }

                            /*
                            $notichcontent = $find['head_name']."您好!你的技师加盟申请已通过.欢迎您的加盟!从现在起,您就可以收到来自1号服务的订单,请留意服务号的订单消息,点击订单消息即可接单.";
							$update = M('repair_agent')->where(array('id'=>$find['id']))->save(array('is_review'=>2));
                            $postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
                            $url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
                            $data = $this->api_notice_increment($url,http_build_query($postdata));
                            */
							if ($update) {
								echo $this->encode(array('status'=>100, 'info'=>'审核成功','url'=>'index.php?g=User&m=Repair&a=agent&type=1&token='.$this->token));
							} else {
								echo $this->encode(array('status'=>1, 'info'=>'系统繁忙', 'url'=>'index.php?g=User&m=Repair&a=review&type=1&token='.$this->token.'&id='.$find['id']));
							}
						} else {
							echo $this->encode(array('status'=>2, 'info'=>'未找到该审核用户'));
						}
					}
                    if($check == 2){
                        $find = M('repair_agent')->where(array('id'=>$agentId, 'is_review'=>1))->find();
                        $agentStaff = M('repair_staff')->where(array('agent_id'=>$find['id']))->find();
                        $openid = M('wxusers')->where(array('id'=>$find['wxusers_id']))->field('openid')->find();
                        if (!empty($find)) {
                            $update = M('repair_agent')->where(array('id'=>$find['id']))->save(array('is_review'=>3,'op_reason'=>trim($_POST['op_reason'])));
                            /*$notichcontent = $find['head_name']."您好!您的加盟申请未通过,原因:".trim($_POST['not_allow']).",请修订后重新提交.";
                            $postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
                            $url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
                            $data = $this->api_notice_increment($url,http_build_query($postdata));
                            */
                            $openid = $openid['openid'];
                            $templateData['touser'] = $openid;
                            $templateData['template_id'] = 'VlCxjIYJH5hxtt9FxSH36ZKPusxM20tHHprCStzGZXg';
                            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=index&token=".$this->token."&openid=".$openid;
                            $templateData['topcolor']="#FF0000";
                            $templateData['data']['first'] = array('value'=>$find['head_name']."您好!您的加盟申请未通过.",'color'=>"#173177");
                            $templateData['data']['keyword1'] = array('value'=>$find['agent_no'],'color'=>"#173177");
                            $templateData['data']['keyword2'] = array('value'=>$agentStaff['staff_name'],'color'=>"#173177");
                            $templateData['data']['keyword3'] = array('value'=>'未通过','color'=>"#173177");
                            $templateData['data']['remark'] = array('value'=>"你好，你的加盟申请未通过审核。原因如下:".trim($_POST['not_allow']),'color'=>"#173177");
                            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
                            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
                            $data = $this->api_notice_increment($url,http_build_query($postdata));

                            if(!$data){
                                $this->api_notice_increment($url,http_build_query($postdata));
                            }




                            if ($data) {
                                echo $this->encode(array('status'=>100, 'info'=>'信息发送成功','url'=>'index.php?g=User&m=Repair&a=agent&type=1&token='.$this->token));
                            } else {
                                echo $this->encode(array('status'=>1, 'info'=>'系统繁忙', 'url'=>'index.php?g=User&m=Repair&a=review&type=1&token='.$this->token.'&id='.$find['id']));
                            }
                        } else {
                            echo $this->encode(array('status'=>2, 'info'=>'未找到该审核用户'));
                        }
                    }
				}	
			} else {
					
				if (isset($_GET['id'])) {						
					$agentId = trim($_GET['id']);
					$agentModel = M('repair_agent');
					$agentDatas = $agentModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$agentId, 'is_review'=>1))->find();
					if (!empty($agentDatas)) {
						/*根据第一个技师的id找到相关的资料*/
						$staffModel = M('repair_staff');
						$staffInfo = $staffModel->where(array('id'=>$agentDatas['staff_id'], 'agent_id'=>$agentId))->find();
						if (!empty($staffInfo)) {
							/*超找员工的学历和工作经验*/
							$trainInfo = M('repair_train')->where(array('staff_id'=>$agentDatas['staff_id']))->select();
							$resumeInfo = M('repair_resume')->where(array('staff_id'=>$agentDatas['staff_id']))->select();
							$this->assign('trainInfo', $trainInfo);
							$this->assign('resumeInfo', $resumeInfo);
							$this->assign('staffInfo', $staffInfo);
						} else {
							/*没找到员工的信息*/
						}
						$this->assign('agentDatas', $agentDatas);
					}
				}
				$this->assign('id', $agentId);
				$this->display();
			}
		} elseif (1 == $type) {
			/*手动添加*/
			if (IS_POST) {

			} else {
				$this->display();
			}
				
		} elseif (2 == $type) {
			/*编辑*/
			if (IS_POST) {
				$jsonData = $_POST['jsonstr'];
				$jsonData = htmlspecialchars_decode($jsonData);
				$jsonData = json_decode($jsonData);		
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id']))->find();
				
				if ((!empty($find)) && ($find['is_review'] == 2)) {
					/*服务商资料*/
						$staffDatas = array(
								'wxuser_id'=>$this->userDatas['id'],
								'wxusers_id'=>$this->wxUserDatas['id'],
								'agent_id'=>$insert_agent,
								'staff_name'=>trim($jsonData[0]->yname),
								'staff_sex'=>trim($jsonData[0]->ysex),
								'birth_time'=>trim($jsonData[0]->birthday),
								'staff_heigh'=>trim($jsonData[0]->yheight),
								'staff_cardno'=>trim($jsonData[0]->idcard),
								'car_img'=>trim($jsonData[0]->imgurl),
								'hukou_address'=>trim($jsonData[0]->yplace),
								'staff_telphone'=>trim($jsonData[0]->yphone),
								'phone'=>trim($jsonData[0]->fixedtel),
								'qq'=>trim($jsonData[0]->qq),
								'email'=>trim($jsonData[0]->yemail),
								'contact'=>trim($jsonData[0]->contact),
								'contact_tel'=>trim($jsonData[0]->contacttel),
								'education'=>trim($jsonData[0]->education),
								'bank_name'=>trim($jsonData[0]->bankname),
								'bank'=>trim($jsonData[0]->openbank),
								'bank_card'=>trim($jsonData[0]->bankNo),
								'staff_address'=>trim($jsonData[0]->stagnation),
								'vehicle'=>trim($jsonData[0]->Traffic),
								'experience'=>trim($jsonData[0]->Experience),
								'work_type'=>trim($jsonData[0]->work),
								'have_apprentice'=>trim($jsonData[0]->Apprentice),
								'qualification'=>trim($jsonData[0]->Certificate)
						);				
						$update_staff = M('repair_staff')->where(array('id'=>$find['staff_id']))->save($staffDatas);
						if ($update_staff !== false) {
							/*添加学历和教育工作的*/
					
							/*教育*/
							if (($jsonData[0]->start_1 !='') || ($jsonData[0]->over_1 !='') || ($jsonData[0]->com_1 !='') || ($jsonData[0]->pos_1 !='')) {
								$eduction = array(
										'start_time'=>trim($jsonData[0]->begin_1),
										'end_time'=>trim($jsonData[0]->end_1),
										'school_name'=>trim($jsonData[0]->org_1),
										'major'=>trim($jsonData[0]->profess_1)
								);
								M('repair_train')->where(array('staff_id'=>$find['staff_id']))->save($eduction);
							}
					
							if (($jsonData[0]->begin_2 != '') || ($jsonData[0]->end_2 != '') || ($jsonData[0]->org_2 != '') || ($jsonData[0]->profess_2 != '')) {
								$eduction = array(
										'start_time'=>trim($jsonData[0]->begin_2),
										'end_time'=>trim($jsonData[0]->end_2),
										'school_name'=>trim($jsonData[0]->org_2),
										'major'=>trim($jsonData[0]->profess_2)
								);
								M('repair_train')->where(array('staff_id'=>$find['staff_id']))->save($eduction);
							}
					
							if (($jsonData[0]->begin_3 != '') || ($jsonData[0]->end_3 != '') || ($jsonData[0]->org_3 != '') || ($jsonData[0]->profess_3 != '')) {
								$eduction = array(
										'start_time'=>trim($jsonData[0]->begin_3),
										'end_time'=>trim($jsonData[0]->end_3),
										'school_name'=>trim($jsonData[0]->org_3),
										'major'=>trim($jsonData[0]->profess_3)
								);
								M('repair_train')->where(array('staff_id'=>$find['staff_id']))->save($eduction);
							}
					
							/*简历*/
							if (($jsonData[0]->start_1 !='') || ($jsonData[0]->over_1 !='') || ($jsonData[0]->com_1 !='') || ($jsonData[0]->pos_1 !='')) {
								$eduction = array(
										'start_time'=>trim($jsonData[0]->start_1),
										'end_time'=>trim($jsonData[0]->over_1),
										'company'=>trim($jsonData[0]->com_1),
										'staff_position'=>trim($jsonData[0]->pos_1)
								);
								M('repair_resume')->where(array('staff_id'=>$find['staff_id']))->save($eduction);
							}
					
							if (($jsonData[0]->start_2 !='') || ($jsonData[0]->over_2 !='') || ($jsonData[0]->com_2 !='') || ($jsonData[0]->pos_2 !='')) {
								$eduction = array(
										'start_time'=>trim($jsonData[0]->start_2),
										'end_time'=>trim($jsonData[0]->over_2),
										'company'=>trim($jsonData[0]->com_2),
										'staff_position'=>trim($jsonData[0]->pos_2)
								);
								M('repair_resume')->where(array('staff_id'=>$find['staff_id']))->save($eduction);
							}
					
							if (($jsonData[0]->start_3 !='') || ($jsonData[0]->over_3 !='') || ($jsonData[0]->com_3 !='') || ($jsonData[0]->pos_3 !='')) {
								$eduction = array(
										'start_time'=>trim($jsonData[0]->start_3),
										'end_time'=>trim($jsonData[0]->over_3),
										'company'=>trim($jsonData[0]->com_3),
										'staff_position'=>trim($jsonData[0]->pos_3)
								);
								M('repair_resume')->where(array('staff_id'=>$find['staff_id']))->save($eduction);
							}
						}
						echo $this->encode(array('status'=>100, 'info'=>'你已修改成功，请等审核', 'url'=>'index.php?g=Wap&m=Repair&a=index&token='.$this->token."&openid=".$this->openid));
					}
			} else {
				if (isset($_GET['id'])) {
					$agentId = trim($_GET['id']);
					$agentModel = M('repair_agent');
					$agentDatas = $agentModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$agentId))->find();
					if (!empty($agentDatas)) {
						/*根据第一个技师的id找到相关的资料*/
						$staffModel = M('repair_staff');
						$staffInfo = $staffModel->where(array('id'=>$agentDatas['staff_id']))->find();
						
						if (!empty($staffInfo)) {
							/*超找员工的学历和工作经验*/
							$trainInfo = M('repair_train')->where(array('staff_id'=>$agentDatas['staff_id']))->select();
							$resumeInfo = M('repair_resume')->where(array('staff_id'=>$agentDatas['staff_id']))->select();
							$this->assign('trainInfo', $trainInfo);
							$this->assign('resumeInfo', $resumeInfo);
							$this->assign('staffInfo', $staffInfo);
						} else {
							/*没找到员工的信息*/
						}
						$this->assign('agentDatas', $agentDatas);
					}
				}
				$this->assign('id', $agentId);
				$this->display();
			}
		}		
	}
	
	/*禁用*/
	public function forbidden() {
		if ($_POST['op_reason']) {
			$type = trim($_GET['type']);
			if (1 == $type) {
				/*禁止接单*/
				if (isset($_GET['id'])) {
					$agentId = trim($_GET['id']);
					$agentModel = M('repair_agent');
					$agentDatas = $agentModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$agentId))->find();
					if (!empty($agentDatas)) {
						if (1 == $agentDatas['is_forbidden']) {
							$this->error('你还没有启用服务商');
						} else {
							if (0 == $agentDatas['is_forbidden']) {
								$agentModel->where(array('id'=>$agentDatas['id']))->save(array('is_forbidden'=>1,'op_reason'=>$_POST['op_reason']));

                                /*
                                * 发送禁止接单消息
                            */

                                $openid = M('wxusers')->where(array('id'=>$agentDatas['wxusers_id']))->field('openid')->find();
                                if (!empty($agentDatas)) {
                                    $notichcontent = "账号编码：".$agentDatas['agent_no']."\r\n用户姓名:".$agentDatas['head_name']."\r\n禁用原因：".$_POST['op_reason']."\r\n您的账号被管理员禁接单，如有疑问可咨询025-58460223，或通过“修业网络客服中心”服务号咨询。";

                                   // $notichcontent = $agentDatas['head_name']."您好!您的账号已被管理员禁接单。\r\n原因：".$_POST['op_reason']."\r\n如有疑问可咨询025-58460223，或通过“修业网络客服中心”服务号咨询。";
                                    $postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
                                    $url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
                                    $data = $this->api_notice_increment($url,http_build_query($postdata));
                                    if(!$data){
                                        $this->api_notice_increment($url,http_build_query($postdata));
                                    }
                                }


                                $this->success('修改成功');
							} else {
								$agentModel->where(array('id'=>$agentDatas['id']))->save(array('is_forbidden'=>0,'op_reason'=>$_POST['op_reason']));
								$this->success('修改成功');
							}
						}
					}
				}					
			} elseif (2 == $type) {
				/*编辑禁用*/
				if (isset($_GET['id'])) {
					$agentId = trim($_GET['id']);
					$agentModel = M('repair_agent');
					$agentDatas = $agentModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$agentId))->find();
					if (!empty($agentDatas)) {
						if (0 == $agentDatas['is_forbidden']) {
                            $agentModel->where(array('id'=>$agentDatas['id']))->save(array('is_forbidden'=>1,'op_reason'=>$_POST['op_reason']));

                            /*
                            * 发送禁用消息
                        */

                            $openid = M('wxusers')->where(array('id'=>$agentDatas['wxusers_id']))->field('openid')->find();
                            if (!empty($agentDatas)) {

                                $notichcontent = "账号编码：".$agentDatas['agent_no']."\r\n用户姓名:".$agentDatas['head_name']."\r\n禁用原因：".$_POST['op_reason']."\r\n您的账号被管理员禁接单，如有疑问可咨询025-58460223，或通过“修业网络客服中心”服务号咨询。";

                                //$notichcontent = $agentDatas['head_name']."您好!您的账号已被管理员禁接单。\r\n原因：".$_POST['op_reason']."\r\n如有疑问可咨询025-58460223，或通过“修业网络客服中心”服务号咨询。";
                                $postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
                                $url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
                                $data = $this->api_notice_increment($url,http_build_query($postdata));
                                if(!$data){
                                    $this->api_notice_increment($url,http_build_query($postdata));
                                }
                            }


                            //$this->success('修改成功');

							$agentModel->where(array('id'=>$agentDatas['id']))->save(array('is_forbidden'=>1,'op_reason'=>$_POST['op_reason']));
                            $this->success('修改成功');
						} else {
							$agentModel->where(array('id'=>$agentDatas['id']))->save(array('is_forbidden'=>0,'op_reason'=>$_POST['op_reason']));
							$this->success('修改成功');
						}
					}
				}
			}
		}else{
            $agentId = trim($_GET['id']);
            $type = trim($_GET['type']);
            $this->assign('type',$type);
            $this->assign('agentId',$agentId);
            $this->display();
        }
	}
	
	/*服务商权限管理*/
	public function access() {
		if (IS_POST) {
			/*先查找是否存在这个服务商*/
			$isAgent = M('repair_agent')->where(array('id'=>trim($_POST['id']), 'wxuser_id'=>$this->userInfoData['id']))->find();
			if (empty($isAgent)) {
				$this->error2('系统中不存在该服务商！');
			} else {
                $order_max = trim($_POST['order_max']);
                $order_max_change = trim($_POST['order_max_change']);
                $order_max =  $order_max+$order_max_change;
                if($order_max_change != '' && is_numeric($order_max)){
                    if ($order_max < 0) {
                        $this->success2('操作失败！减少数额错误');
                        exit;
                    }
                }else{
                    $order_max_change = 0;
                }

                $backlog_max = trim($_POST['backlog_max']);
                $backlog_max_change = trim($_POST['backlog_max_change']);
                $backlog_max =  $backlog_max+ $backlog_max_change;
                if($backlog_max_change != '' && is_numeric($backlog_max)){
                    if ($backlog_max < 0) {
                        $this->success2('操作失败！减少数额错误');
                        exit;
                    }
                }else{
                    $backlog_max_change = 0;
                }


                $day_max = trim($_POST['day_max']);
                $day_max_change = trim($_POST['day_max_change']);
                $day_max = $day_max + $day_max_change;
                if($day_max_change != '' && is_numeric($day_max_change)){
                    if ($day_max < 0) {
                        $this->success2('操作失败！减少数额错误');
                        exit;
                    }
                }else{
                    $day_max_change = 0;
                }


                $deposit = trim($_POST['deposit']);
                $deposit_change = trim($_POST['deposit_change']);
                $deposit = $deposit+$deposit_change;
                if($deposit_change != '' && is_numeric($deposit)){
                    if ($deposit < 0) {
                        $this->success2('操作失败！减少数额错误');
                        exit;
                    }
                }else{
                    $deposit_change = 0;
                }


                $comp_day = trim($_POST['comp_day']);
                $comp_day_change = trim($_POST['comp_day_change']);
                $comp_day = $comp_day+$comp_day_change;
                if($comp_day_change != '' && is_numeric($comp_day)){
                    if ($comp_day < 0) {
                        $this->success2('操作失败！减少数额错误');
                        exit;
                    }
                }else{
                    $comp_day_change = 0;
                }

				$insertDatas = array(
						'wxuser_id'=>$this->userInfoData['id'],
						'agent_id'=>$isAgent['id'],
						'order_max'=>$order_max,
						'backlog_num'=>$backlog_max,
						'day_order_max'=>$day_max,
						'staff_mun'=>trim($_POST['staff_max']),
						'complaint'=>$comp_day,
						'deposit'=>$deposit
				);
				$accessModel = M('repair_agent_access');
				$isHave = $accessModel->where(array('agent_id'=>$isAgent['id'], 'wxuser_id'=>$this->userInfoData['id']))->find();
				if (!empty($isHave)) {
					$updateBack = $accessModel->where(array('id'=>$isHave['id']))->save($insertDatas);


                    $changedata['agent_no'] = $isAgent['agent_no'];
                    $changedata['agent_id'] = $isAgent['id'];
                    $changedata['wxuser_id'] = $isAgent['wxuser_id'];
                    $changedata['wxusers_id'] = $isAgent['wxusers_id'];
                    $changedata['name'] = $isAgent['head_name'];
                    $changedata['phone'] = $isAgent['telephone'];
                    $changedata['name'] = $isAgent['head_name'];
                    $changedata['order_max'] = $order_max_change;
                    $changedata['backlog_max'] = $backlog_max_change;
                    $changedata['day_max'] = $day_max_change;
                    $changedata['comp_day'] = $comp_day_change;
                    $changedata['deposit'] = $deposit_change;
                    $changedata['type'] = '管理员';
                    $changedata['update_time'] = time();
                    $changedata['token'] = $this->token;

                    M('Repair_access_change')->add($changedata);

                    //操作原因
                    M('repair_agent')->where(array('id'=>$isAgent['id']))->save(array('op_reason'=>$_POST['not_allow']));

					if ($updateBack && $opres) {
						$this->success2('编辑成功！',U('Repair/agent', array('token'=>$this->token)));
						
					} elseif ($updateBack === false &&$opres === false) {
						$this->error2('编辑失败！');
					}else{
                       $this->success2('编辑成功！',U('Repair/agent', array('token'=>$this->token)));
                    }
				} else {
					$insertBack = $accessModel->add($insertDatas);

                    $changedata['agent_no'] = $isAgent['agent_no'];
                    $changedata['agent_id'] = $isAgent['id'];
                    $changedata['wxuser_id'] = $isAgent['wxuser_id'];
                    $changedata['wxusers_id'] = $isAgent['wxusers_id'];
                    $changedata['name'] = $isAgent['head_name'];
                    $changedata['phone'] = $isAgent['telephone'];
                    $changedata['name'] = $isAgent['head_name'];
                    $changedata['order_max'] = $order_max_change;
                    $changedata['backlog_max'] = $backlog_max_change;
                    $changedata['day_max'] = $day_max_change;
                    $changedata['comp_day'] = $comp_day_change;
                    $changedata['deposit'] = $deposit_change;
                    $changedata['type'] = '管理员';
                    $changedata['update_time'] = time();
                    $changedata['token'] = $this->token;
                    M('Repair_access_change')->add($changedata);
					if ($insertBack) {
						$this->success2('添加成功！',U('Repair/agent', array('token'=>$this->token)));
					} else {
						$this->error2('添加失败！');
					}
				}
			}
			
		} else {
			if (isset($_GET['id'])) {
				$agentId = trim($_GET['id']);
				$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$agentId))->find();
				if (!empty($agentInfo)) {
					$this->assign('id', $agentInfo['id']);
					$this->assign('agentNo', $agentInfo['agent_no']);
				}

                $accessModel = M('repair_agent_access');
                $accessInfo = $accessModel->where(array('agent_id'=>$agentId, 'wxuser_id'=>$this->userInfoData['id']))->find();
                $this->assign('accessInfo', $accessInfo);
			}
			$this->display();
		}
	}


    /*
     * 权限变更明细表
     */
    public function accesschange(){
        $db=D('Repair_access_change');
        $where['token']=session('token');
        $count=$db->where($where)->count();
        $page=new Page($count,15);
       
        $info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('update_time desc')->select();
        foreach($info as $k=>$v){
	//echo $v['agent_id']
            $staff = M('Repair_staff')->where(array('wxuser_id'=>$this->userInfoData['id'],'agent_id'=>$v['agent_id']))->find();
            $info[$k]['staffname'] = $staff['staff_name'];
        }
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->display();
    }
	
	
	/*用户管理*/
	public function user() {
		/*查找省*/
		$province = M('area')->where(array('level'=>1))->select();
		$this->assign('province', $province);
		if (isset($_GET['status'])) {
			$status = trim($_GET['status']);
			$area = trim($_GET['area']);
			$condition['area'] = array(
					array('like', "%".$area."%"),
			);
			$this->assign('status', $status);
			$this->assign('area', $area);
		}
		
		$condition['wxuser_id'] = $this->userInfoData['id'];
		import('ORG.Util.Page');
		$count = M('repair_user')->where($condition)->count();
		$page = new Page($count, 20);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$list = M('repair_user')->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
		$show = $page->show();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	
	public function userInfo() {
		if (isset($_GET['id'])) {
			$userInfo = M('repair_user')->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>trim($_GET['id'])))->find();
			if (!empty($userInfo)) {
				$this->assign('user', $userInfo);
			}
			$this->display();
		}
	}
	
	/****************************************业务推广管理******************************************/
	
	public function saler() {
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			/*基本展示本平台的一些推广人员*/
			$condition['wxuser_id'] = $this->userInfoData['id'];
			
			import('ORG.Util.Page');
			$count = M('repair_saler')->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_saler')->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			$this->display();
		} elseif (1 == $type) {
			/*添加推广人员*/
			if (IS_POST) {				
				$insertDatas = array(
								'wxuser_id'=>$this->userInfoData['id'],
								'saler_name'=>trim($_POST['name']),
								'password'=>md5(trim($_POST['saler_pass'])),
								'saler_nid'=>trim($_POST['saler_no']),
								'saler_age'=>trim($_POST['age']),
								'saler_day'=>trim($_POST['experience']),
								'saler_tel'=>trim($_POST['phone'])
						);
				if (trim($_POST['place']) == '普通') {
					$insertDatas['saler_job'] = 0;
				} elseif (trim($_POST['place']) == '业务经理') {
					$insertDatas['saler_job'] = 1;
				} else {
					$insertDatas['saler_job'] = 0;
				}
				
				if (trim($_POST['sex']) == '女') {
					$insertDatas['saler_sex'] = 0;
				} elseif (trim($_POST['sex']) == '男') {
					$insertDatas['saler_sex'] = 1;
				} else {
					$insertDatas['saler_sex'] = 0;
				}
				$salerModel = M('repair_saler');
				$insertBack = $salerModel->add($insertDatas);
				if ($insertBack) {
					$this->success2('添加成功！',U('Repair/saler', array('token'=>$this->token)));
				} else {
					$this->error2('添加失败！');
				}
			} else {
				$this->assign('type', $type);
				$this->display('tpl/User/default/Repair_manSaler.html');
			}
			
		} elseif (2 == $type) {
			/*编辑推广人员*/
			if (IS_POST) {
				
				if (isset($_GET['id'])) {
					$salerId = trim($_GET['id']);/*先查找是否存在*/
					$salerModel = M('repair_saler');
					$find = $salerModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$salerId))->find();
					if (!empty($find)) {
						$updateDatas = array(
								'password'=>md5(trim($_POST['saler_pass'])),
								'saler_name'=>trim($_POST['name']),
								'saler_nid'=>trim($_POST['saler_no']),
								'saler_age'=>trim($_POST['age']),
								'saler_day'=>trim($_POST['experience']),
								'saler_tel'=>trim($_POST['phone'])
						);
						if (trim($_POST['place']) == '普通') {
							$updateDatas['saler_job'] = 0;
						} elseif (trim($_POST['place']) == '业务经理') {
							$updateDatas['saler_job'] = 1;
						} else {
							$updateDatas['saler_job'] = 0;
						}
						
						if (trim($_POST['sex']) == '女') {
							$updateDatas['saler_sex'] = 0;
						} elseif (trim($_POST['sex']) == '男') {
							$updateDatas['saler_sex'] = 1;
						} else {
							$updateDatas['saler_sex'] = 0;
						}
						
						$updateBack = $salerModel->where(array('id'=>$find['id']))->save($updateDatas);
						if ($updateBack) {
							$this->success2('编辑成功！',U('Repair/saler', array('token'=>$this->token)));
						} elseif ($updateBack === false) {
							$this->error2('编辑失败！');
						} else {
							$this->error2('你没有任何更新！');
						}
					}
				}				
			} else {
				if (isset($_GET['id'])) {
					$salerId = trim($_GET['id']);
					$salerDatas = M('repair_saler')->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$salerId))->find();
					if (!empty($salerDatas)) {
						$this->assign('salerDatas', $salerDatas);
					}
				}
				$this->assign('id', $salerId);
				$this->assign('type', $type);
				$this->display('tpl/User/default/Repair_manSaler.html');
			}
		}
	}
	
	
	/**********************************************服务类型管理*******************************************************/
	public function service() {
		$classModel = M('repair_class');
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			$condition['wxuser_id'] = $this->userInfoData['id'];
			import('ORG.Util.Page');
			$count = $classModel->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $classModel->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
			
			foreach ($list as $key=>$value) {
				if ($value['pid'] == 0) {
					$list[$key]['parents_name'] = '没有父类名称';
					$list[$key]['class_name'] = '服务一级级别';
				} else {
					$find = $classModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$value['pid']))->find();
					$list[$key]['parents_name'] = $find['name'];
				}
			}
			
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			$this->display();
			exit();
					
		} elseif (1 == $type) {
			
			if (IS_POST) {
				$insertDatas = array(
					'wxuser_id'=>$this->userInfoData['id'],
					'name'=>trim($_POST['name']),
					'pid'=>trim($_POST['pid']),
					'sort'=>trim($_POST['sort'])
				);
				
				$insertBack = $classModel->add($insertDatas);
				if ($insertBack) {
					$this->success2('添加成功！',U('Repair/service', array('token'=>$this->token)));
				} else {
					$this->error2('添加失败！');
				}
				
			} else {
				
				/*先查找所有父类为0*/
				$parentsDatas = $classModel->where(array('wxuser_id'=>$this->userInfoData['id']))->order('sort,pid')->select();
				
				$data = self::tree($parentsDatas);
				$this->assign('data',$data);
		
				$this->assign('type', 1);
				$this->display('tpl/User/default/Repair_manClass.html');
			}
			
		} elseif (2 == $type) {
			if (IS_POST) {				
				if (isset($_GET['id'])) {
					$classId = trim($_GET['id']);
					$is_exist = $classModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>trim($_GET['id'])))->find();
					if (!empty($is_exist)) {
						$updateDatas = array(
								'wxuser_id'=>$this->userInfoData['id'],
								'name'=>trim($_POST['name']),
								'pid'=>trim($_POST['pid']),
								'sort'=>trim($_POST['sort'])
						);
						
						$updateBack = $classModel->where(array('id'=>$is_exist['id']))->save($updateDatas);
						if ($updateBack) {
							$this->success2('编辑成功！',U('Repair/service', array('token'=>$this->token)));
						} elseif ($updateBack === false) {
							$this->error2('编辑失败！');
						} else {
							$this->error2('你未改任何数据！');
						}
					} else {
						$this->error2('不存在该分类！');
					}
				} else {
					$this->error2('未能获取参数数据');
				}
				
			} else {
				/*先查找所有父类为0*/
				$parentsDatas = $classModel->where(array('wxuser_id'=>$this->userInfoData['id']))->order('sort,pid')->select();
				
				$data = self::tree($parentsDatas);
				$this->assign('data',$data);

				$dataAarry = $classModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>trim($_GET['id'])))->find();
				$this->assign('dataArray', $dataAarry);
				
				$this->assign('type', 2);
				$this->display('tpl/User/default/Repair_manClass.html');
			}
		}
	}
	
	
	static public function tree(&$data,$pid = 0,$count = 1) {
		foreach ($data as $key => $value){
			if($value['pid']==$pid){
				$value['Count'] = $count;
				self::$treeList []=$value;
				unset($data[$key]);
				self::tree($data,$value['id'],$count+1);
			}
		}
		return self::$treeList ;
	}
	
	
	
	
	/***********************************************信息发布管理******************************************************/
	public function release() {
	
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			
			$condition['wxuser_id'] = $this->userInfoData['id'];
				
			import('ORG.Util.Page');
			$count = M('repair_release')->where($condition)->count();
			$page = new Page($count, 20);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = M('repair_release')->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			$this->display();
			
		} elseif (1 == $type) {
			/*添加发布信息*/
			if (IS_POST) {
				$data = array(
								'wxuser_id'=>$this->userInfoData['id'],
								'title'=>strip_tags($_POST['title']),
								'keyword'=>trim($_POST['keyword']),
								'time'=>strtotime(trim($_POST['date'])),
								'editer'=>trim($_POST['editer']),
								'message'=>strip_tags($_POST['info']),
								'sort'=>trim($_POST['sort'])
						);
				if (trim($_POST['obj']) == '服务商') {
					$data['aim_at'] = 1;
				} elseif (trim($_POST['obj']) == '用户') {
					$data['aim_at'] = 2;
				} else {
					$data['aim_at'] = 0;
				}
				
				$insert = M('repair_release')->add($data);
				if ($insert) {
					$this->success2('添加成功', U('Repair/release', array('token'=>$this->token)));
				} else {
					$this->error2('添加失败');
				}
				
			} else {
				$this->assign('type', $type);
				$this->display('tpl/User/default/Repair_releaseEdit.html');
			}
		} elseif (2 == $type) {
			/*编辑发布信息*/
			if (IS_POST) {
				
				if (isset($_GET['id'])) {
					$messageId = trim($_GET['id']);
					$messageFind = M('repair_release')->where(array('id'=>$messageId, 'wxuser_id'=>$this->userInfoData['id']))->find();
					if (!empty($messageFind)) {
						$data = array(
								'wxuser_id'=>$this->userInfoData['id'],
								'title'=>htmlspecialchars($_POST['title']),
								'keyword'=>trim($_POST['keyword']),
								'time'=>strtotime(trim($_POST['date'])),
								'editer'=>trim($_POST['editer']),
								'message'=>htmlspecialchars($_POST['info']),
								'sort'=>trim($_POST['sort'])
						);
						if (trim($_POST['obj']) == '服务商') {
							$data['aim_at'] = 1;
						} elseif (trim($_POST['obj']) == '用户') {
							$data['aim_at'] = 2;
						} else {
							$data['aim_at'] = 0;
						}
						
						$update = M('repair_release')->where(array('id'=>$messageFind['id']))->save($data);
						if ($update) {
							$this->success2('编辑成功', U('Repair/release', array('token'=>$this->token)));
						} elseif ($update === false) {
							$this->error2('编辑失败');
						} else {
							$this->error2('没有修改任何东西');
						}
					}
				}
				
				
				
			} else {
				if (isset($_GET['id'])) {
					$messageId = trim($_GET['id']);
					$messageFind = M('repair_release')->where(array('id'=>$messageId, 'wxuser_id'=>$this->userInfoData['id']))->find();
					if (!empty($messageFind)) {
						$this->assign('messageFind', $messageFind);
					}
				}
				$this->assign('type', $type);
				$this->display('tpl/User/default/Repair_releaseEdit.html');
			}
		} else {
			$this->display();
		}
		
	}
	
	
	
	
	
	
	
	/************************************************建议投诉管理****************************************************/
	
	public function suggest() {
		
		$condition['wxuser_id'] = $this->userInfoData['id'];
			
		import('ORG.Util.Page');
		$count = M('repair_complain')->where($condition)->count();
		$page = new Page($count, 20);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$list = M('repair_complain')->where($condition)->order('id')->page($nowPage.','.$page->listRows)->select();
		
		foreach ($list as $key=>$value) {
			$orderInfo = M('repair_order')->where(array('order_nid'=>$value['order_id']))->find();

			/*到时候添加一些员工的信息*/
// 			$staffInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$value['agent_id']))->find();
 			$list[$key]['order_address'] = $orderInfo['order_address'];
 			$list[$key]['order_tel'] = $orderInfo['order_tel'];
 			$list[$key]['staff_name'] = $orderInfo['staff_name'];
 			$list[$key]['staff_tel'] = $orderInfo['staff_tel'];
		}
		
		$show = $page->show();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
	
	public function sdetail() {
		if (isset($_GET['id'])) {
			$suggestId = trim($_GET['id']);
			$find = M('repair_complain')->where(array('id'=>$suggestId))->find();
			if (!empty($find)) {
				$this->assign('suggestInfo', $find);
			}
			$this->display();
		} else {
			$this->error2('不存在这个投诉建议');
		}
	}
	
	
	/*************************************************删除信息********************************************************/
	public function del() {
		/*删除服务商*/
		if (isset($_GET['type']) && isset($_GET['id']) ) {
			$type = trim($_GET['type']);
			$delId = trim($_GET['id']);
			if (1 == $type) {
				/*删除服务商*/
				$agentModel = M('repair_agent');
				$findAgent = $agentModel->where(array('id'=>$delId, 'wxuser_id'=>$this->userInfoData['id']))->find();
				if (!empty($findAgent)) {
					$delBack = $agentModel->where(array('id'=>$findAgent['id']))->delete();
					if ($delBack) {
						$this->success('删除成功！', U('Repair/index', array('token'=>$this->token)));
					} else {
						$this->error('系统繁忙，删除失败！');
					}
				} else {
					$this->error('该项已删除！');
				}
			} elseif (2 == $type) {
				/*删除业务推广员*/
				$salerModel = M('repair_saler');
				$findSaler = $salerModel->where(array('id'=>$delId, 'wxuser_id'=>$this->userInfoData['id']))->find();
				if (!empty($findSaler)) {
					$delBack = $salerModel->where(array('id'=>$findSaler['id']))->delete();
					if ($delBack) {
						$this->success('删除成功！', U('Repair/saler', array('token'=>$this->token)));
					} else {
						$this->error('系统繁忙，删除失败！');
					}
				} else {
					$this->error('该项已删除！');
				}
			} elseif (3 == $type) {
				/*删除服务类型*/
				$classModel = M('repair_class');
				$findClass = $classModel->where(array('id'=>$delId, 'wxuser_id'=>$this->userInfoData['id']))->find();
				if (!empty($findClass)) {
					$delBack = $classModel->where(array('id'=>$findClass['id']))->delete();
					if ($delBack) {
						$this->success('删除成功！', U('Repair/service', array('token'=>$this->token)));
					} else {
						$this->error('系统繁忙，删除失败！');
					}
				} else {
					$this->error('该项已删除！');
				}
			} elseif (4 == $type) {
				/*删除发布信息*/
				$releaseModel = M('repair_release');
				$find = $releaseModel->where(array('id'=>$delId, 'wxuser_id'=>$this->userInfoData['id']))->find();
				if (!empty($find)) {
					$delBack = $releaseModel->where(array('id'=>$find['id']))->delete();
					if ($delBack) {
						$this->success('删除成功！', U('Repair/release', array('token'=>$this->token)));
					} else {
						$this->error('系统繁忙，删除失败！');
					}
				} else {
					$this->error('该项已删除！');
				}
			} elseif (5 == $type) {
				/*删除投诉建议*/
				$releaseModel = M('repair_complain');
				$find = $releaseModel->where(array('id'=>$delId, 'wxuser_id'=>$this->userInfoData['id']))->find();
				if (!empty($find)) {
					$delBack = $releaseModel->where(array('id'=>$find['id']))->delete();
					if ($delBack) {
						$this->success('删除成功！', U('Repair/suggest', array('token'=>$this->token)));
					} else {
						$this->error('系统繁忙，删除失败！');
					}
				} else {
					$this->error('该项已删除！');
				}
			} else {
				$this->error('系统繁忙。。。');
			}
		}
		
	}	
	
	
	/*查找市*/
	public function findCity() {
		if (IS_POST) {
			$bigId = $_POST['id'];
			$condition['path'] =  array(
					array('like', $bigId."%")
			);
			$condition['level'] = 2;
			$city = M('area')->where($condition)->select();
				
			echo $this->encode(array('status'=>100, 'data'=>$city));
		}
	
	}
	
	public function findCounty() {
		if (IS_POST) {
			$bigId = $_POST['id'];
			$bigId = substr($bigId,0,4);
			$condition['code'] =  array(
					array('like', $bigId."%")
			);
			$condition['level'] = 3;
			
			$city = M('area')->where($condition)->select();
	
			echo $this->encode(array('status'=>100, 'data'=>$city));
		}
	
	}
	
	public function findTown() {
		if (IS_POST) {
			$bigId = $_POST['id'];
			$bigId = substr($bigId,0,6);
			$condition['code'] =  array(
					array('like', $bigId."%")
			);
			$condition['level'] = 4;
				
			$city = M('area')->where($condition)->select();
		
			echo $this->encode(array('status'=>100, 'data'=>$city));
		}
	}

    public function suggessok(){
        $complain_id = $_GET['id'];
        $complaindata = M('repair_complain')->where(array('id'=>$complain_id))->find();
        $find = M('repair_agent')->where(array('id'=>$complaindata['agent_id']))->find();
        $staff = M('repair_staff')->where(array('agent_id'=>$complaindata['agent_id']))->find();
        $openid = M('wxusers')->where(array('id'=>$find['wxusers_id']))->field('openid')->find();
        if (!empty($find)) {
            /*发送服务商*/
            $openid = $openid['openid'];
            $templateData['touser'] = $openid;
            $templateData['template_id'] = 'r8FdC7k3wtLOCPTyw17Kbs8XsHwjVXg0MoqbdfJs7gg';
            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=index&token=".$this->token."&openid=".$openid;
            $templateData['topcolor']="#FF0000";
            $templateData['data']['first'] = array('value'=>"",'color'=>"#173177");
            $templateData['data']['keyword1'] = array('value'=>$complaindata['complain_username'],'color'=>"#173177");
            $templateData['data']['keyword2'] = array('value'=>$staff['staff_name'],'color'=>"#173177");
            $templateData['data']['keyword3'] = array('value'=>$complaindata['order_id'],'color'=>"#173177");
            $templateData['data']['keyword4'] = array('value'=>$complaindata['complain_info'],'color'=>"#173177");
            $templateData['data']['keyword5'] = array('value'=>'投诉成立','color'=>"#173177");
            $templateData['data']['remark'] = array('value'=>"您未在7天内就用户投诉事宜达成一致，1号服务将对你的被投诉次数加1，以示处罚。被投诉次数累计到3次将永久禁用。希望您和1号服务一起不断提升服务质量，为用户提供优质的家电安装、维修服务。",'color'=>"#173177");
            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
            $data = $this->api_notice_increment($url,http_build_query($postdata));
            if(!$data){
                $this->api_notice_increment($url,http_build_query($postdata));
            }

            $templateData = array();

            /*发送用户*/
            $openid = M('wxusers')->where(array('id'=>$complaindata['wxusers_id']))->field('openid')->find();
            $templateData['touser'] = $openid['openid'];
            $templateData['template_id'] = 'OwnZiJIMdcops1LUBLAj-nTaeaHCPIoawG-UPH-04z8';
            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=index&token=".$this->token."&openid=".$openid['openid'];
            $templateData['topcolor']="#FF0000";
            $templateData['data']['first'] = array('value'=>"您好,您的投诉通过了!",'color'=>"#173177");
            $templateData['data']['Apply_id'] = array('value'=>$complaindata['order_id'],'color'=>"#173177");
            $templateData['data']['Apply_Type'] = array('value'=>'投诉返回单','color'=>"#173177");
            $templateData['data']['Apply_State'] = array('value'=>'投诉成立','color'=>"#173177");
            $templateData['data']['Apply_CreateTime'] = array('value'=>date("Y年m月d日 H:i:s"),'color'=>"#173177");
            $templateData['data']['remark'] = array('value'=>"您未在7天内就用户投诉事宜达成一致，1号服务将对你的被投诉次数加1，以示处罚。被投诉次数累计到3次将永久禁用！希望您和1号服务一起不断提升服务质量，为用户提供优质的家电安装、维修服务。",'color'=>"#173177");
            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
            $data = null;
            $data = $this->api_notice_increment($url,http_build_query($postdata));
            if(!$data){
                $this->api_notice_increment($url,http_build_query($postdata));
            }
            /*
             * 成立
             */
            M('repair_complain')->where(array('id'=>$complain_id))->save(array('status'=>1));

            /*
             * 投诉次数加1
             */
            M('repair_agent_access')->where(array('agent_id'=>$complaindata['agent_id']))->setInc('complaint',1);

            M('repair_complain')->where(array('id'=>$complain_id))->save(array('status'=>1));
            if(!M('repair_screen')->where(array('wxuser_id'=>$complaindata['wxuser_id'],'wxusers_id'=>$complaindata['wxusers_id'],'agent_id'=>$complaindata['agent_id']))->find()){
                M('repair_screen')->add(array('wxuser_id'=>$complaindata['wxuser_id'],'wxusers_id'=>$complaindata['wxusers_id'],'agent_id'=>$complaindata['agent_id']));
            }

            $this->success2("处理成功",'index.php?g=User&m=Repair&a=suggest&token='.$this->token);

        } else {
           $this->error2("处理失败",'index.php?g=User&m=Repair&a=suggest&token='.$this->token);
        }
    }



    public function suggessno(){
        $complain_id = $_GET['id'];
        $complaindata = M('repair_complain')->where(array('id'=>$complain_id))->find();
        $find = M('repair_agent')->where(array('id'=>$complaindata['agent_id']))->find();
        $openid = M('wxusers')->where(array('id'=>$find['wxusers_id']))->field('openid')->find();
        if (!empty($find)) {
            /*发送服务商*/
            $openid = $openid['openid'];
            $templateData['touser'] = $openid;
            $templateData['template_id'] = 'OwnZiJIMdcops1LUBLAj-nTaeaHCPIoawG-UPH-04z8';
            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=index&token=".$this->token."&openid=".$openid;
            $templateData['topcolor']="#FF0000";
            $templateData['data']['first'] = array('value'=>$find['head_name']."您好,有用户投诉您!",'color'=>"#173177");
            $templateData['data']['Apply_id'] = array('value'=>$complaindata['order_id'],'color'=>"#173177");
            $templateData['data']['Apply_Type'] = array('value'=>'投诉返回单','color'=>"#173177");
            $templateData['data']['Apply_State'] = array('value'=>'投诉不成立','color'=>"#173177");
            $templateData['data']['Apply_CreateTime'] = array('value'=>date("Y年m月d日 H:i:s"),'color'=>"#173177");
            $templateData['data']['remark'] = array('value'=>"经调查,用户对您的投诉不成立",'color'=>"#173177");
            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
            $data = $this->api_notice_increment($url,http_build_query($postdata));
            if(!$data){
                $this->api_notice_increment($url,http_build_query($postdata));
            }

            $templateData = array();

            /*发送用户*/
            $openid = M('wxusers')->where(array('id'=>$complaindata['wxusers_id'],'uid'=>$complaindata['wxuser_id']))->field('openid')->find();
            $templateData['touser'] = $openid['openid'];
            $templateData['template_id'] = 'OwnZiJIMdcops1LUBLAj-nTaeaHCPIoawG-UPH-04z8';
            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=index&token=".$this->token."&openid=".$openid['openid'];
            $templateData['topcolor']="#FF0000";
            $templateData['data']['first'] = array('value'=>"您好,您的投诉未通过!",'color'=>"#173177");
            $templateData['data']['Apply_id'] = array('value'=>$complaindata['order_id'],'color'=>"#173177");
            $templateData['data']['Apply_Type'] = array('value'=>'投诉返回单','color'=>"#173177");
            $templateData['data']['Apply_State'] = array('value'=>'投诉成立','color'=>"#173177");
            $templateData['data']['Apply_CreateTime'] = array('value'=>date("Y年m月d日 H:i:s"),'color'=>"#173177");
            $templateData['data']['remark'] = array('value'=>"经调查,用户对您的投诉不成立.感谢对1号服务的支持！",'color'=>"#173177");
            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
            $data = null;
            $data = $this->api_notice_increment($url,http_build_query($postdata));
            if(!$data){
                $this->api_notice_increment($url,http_build_query($postdata));
            }

            M('repair_complain')->where(array('id'=>$complain_id))->save(array('status'=>2));

            $this->success2("处理成功",'index.php?g=User&m=Repair&a=suggest&token='.$this->token);

        } else {
            $this->error2("处理失败",'index.php?g=User&m=Repair&a=suggest&token='.$this->token);
        }
    }

    /*
    转单
    */

    public function zhuandan(){
    	$oAgentModel = M('repair_agent');
    	$oOrderModel = M('repair_order');

    	$iAgentid = $_POST['agent_id'];
    	$iOrderId = $_POST['id'];
    	$aAgent = $oAgentModel->where(array('id'=>$iAgentid))->find();

    	$aUpdateData['agent_id'] = $iAgentid;
    	$aUpdateData['agent_name'] = $aAgent['head_name'];
    	$aUpdateData['status'] = 2;
    	$aUpdateData['is_tel'] = 0;
    	$aUpdateData['grab_time'] = time();
    	if($oOrderModel->where(array('id'=>$iOrderId))->data($aUpdateData)->save()){
    		$findOrder =  M('repair_order')->where(array('id'=>$iOrderId))->find();

    		
    		$aopenid = M('wxusers')->where(array('id'=>$aAgent['wxusers_id']))->field('openid')->find();

			/*发送给服务商的*/

            $templateData['touser'] = $aopenid['openid'];
            $templateData['template_id'] = 'XseMeBde00Fj-9Ox_xhBT2tOnYehS7YJUQ9ziCEwnss';
            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=myOrder&token=".$this->token."&openid=".$aopenid['openid']."&type=1";
            $templateData['topcolor']="#FF0000";
            $templateData['data']['first'] = array('value'=>'您有系统分配的新订单,请点击查看','color'=>"#173177");
            $templateData['data']['tradeDateTime'] = array('value'=>date('Y年m月d日H时i分s秒'),'color'=>"#173177");
            $templateData['data']['orderType'] = array('value'=>'保外单','color'=>"#173177");
            $templateData['data']['customerInfo'] = array('value'=>$findOrder['order_address']."-".$findOrder['order_name'],'color'=>"#173177");
            $templateData['data']['orderItemName'] = array('value'=>'家电报障','color'=>"#173177");
            $templateData['data']['orderItemData'] = array('value'=>$findOrder['repair_ele'].$findOrder['service_cont']." ".trim($findOrder['fault_info']),'color'=>"#173177");

            $templateData['data']['remark'] = array('value'=>"预约时间为".$findOrder['appoint_day']." ".$findOrder['appoint_day'].",请点击接单,在接单成功后在10分钟回电",'color'=>"#173177");
            $postdata = array('openid'=>$aopenid['openid'],'token'=>$this->token,'data'=>$this->encode($templateData));
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
            $data2 = $this->api_notice_increment($url,http_build_query($postdata));

            if(!$data2){
                $this->api_notice_increment($url,http_build_query($postdata));
            }

    		

    		/*
    		给用户发送通知
    		*/	


            $staff = M('repair_staff')->where(array('agent_id'=>$findOrder['agent_id']))->find();
            $findOrder['staff_name'] = $staff['staff_name'];
            $findOrder['staff_tel'] = $staff['staff_telphone'];

            $openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
            $openid = $openid['openid'];
            $templateData = array();
            $templateData['touser'] = $openid;
            $templateData['template_id'] = 'llSeKT9TXzoUy99S0FY0y-Q_GD9VNP9dtfeuhPL-68A';
            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=wxOrder&type=1&token=".$this->token."&openid=".$openid;
            $templateData['topcolor']="#FF0000";
            $templateData['data']['first'] = array('value'=>'尊敬的'.$findOrder['order_name'].'您好！您预约服务已经派单完成，请保持手机畅通以便技师与您取得联系。','color'=>"#173177");
            $templateData['data']['Good'] = array('value'=>$findOrder['repair_ele'],'color'=>"#173177");
            $templateData['data']['expDate'] = array('value'=>$findOrder['appoint_day'].",".$findOrder['appoint_time'],'color'=>"#173177");
            $templateData['data']['name'] = array('value'=>$findOrder['staff_name'],'color'=>"#173177");
            $templateData['data']['menu'] = array('value'=>"请参考【用户专区】→【收费标准】",'color'=>"#173177");
            $templateData['data']['remark'] = array('value'=>"技师电话:".$findOrder['staff_tel'],'color'=>"#173177");
            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
            $data = $this->api_notice_increment($url,http_build_query($postdata));

            if(!$data){
                $this->api_notice_increment($url,http_build_query($postdata));
            }


    		echo $this->encode(array('code'=>0,'msg'=>'转单成功'));
    	}else{
			echo $this->encode(array('code'=>-1,'msg'=>'转单失败'));
    	}
    }
	
	
}