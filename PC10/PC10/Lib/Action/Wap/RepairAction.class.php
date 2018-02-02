<?php
/**
 * 
 * WAP端
 * @author NICK
 *
 */
class RepairAction extends BaseAction{

	public $token;
	public $openid;
	public $userModel;
	public $userDatas;
	public $wxUserModel;
	public $wxUserDatas;
    public $weiobj;
	
	static public $treeList = array();

	public function __construct() {
		parent::_initialize();

		if ((!session('?token')) || (!session('?openid'))) {
			session('token', $_REQUEST['token']);
			session('openid', $_REQUEST['openid']);
		}

		$this->token = $_REQUEST['token'];
		$this->openid =  $_REQUEST['openid'];

		$this->userModel = M('wxuser');
		$this->userDatas = $this->userModel->where(array('token'=>$this->token))->find();

		$this->wxUserModel = M('wxusers');
		$this->wxUserDatas = $this->wxUserModel->where(array('uid'=>$this->userDatas['id'], 'openid'=>$this->openid))->find();
		$this->assign('token', $this->token);
		$this->assign('openid', $this->openid);
	}
	
	/*商家进入*/
	public function index() {
		
		/*先去查找，然后进行判断*/
		$findAgent = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
        if (!empty($findAgent) && $findAgent['is_review'] != 3) {
            $this->assign('findAgent',$findAgent);
            if ($findAgent['is_forbidden'] == 1) {
                /*用户被禁用*/
                $this->assign('type', 2);
                $this->display('tpl/Wap/default/Repair_agent_exit.html');
                exit;
            }
		    if ($findAgent['is_exist'] == 1) {
				/*注销的情况*/
				$this->assign('type', 1);
				$this->display('tpl/Wap/default/Repair_agent_exit.html');
			}			
			if (($findAgent['is_review'] == 2) && ($findAgent['is_exist'] == 0)) {
				/*主页面显示*/
				$newsData = M('repair_release')->where(array('wxuser_id'=>$this->userDatas['id'], 'aim_at'=>array('neq',2)))->order('sort')->limit(3)->select();
				
				if (count($newsData) == 3) {
					$this->assign('isMore', 1);
				}
				
				$this->assign('newsData', $newsData);
				$this->display('tpl/Wap/default/Repair_home.html');
			}


			if ($findAgent['is_review'] == 1) {
				/*申请在进行中*/
				$this->assign('type', 3);
				$this->display('tpl/Wap/default/Repair_agent_exit.html');
			}
		} else {
			/*没有注册*/
			if (IS_POST) {
				$jsonData = $_POST['jsonstr'];
				$jsonData = htmlspecialchars_decode($jsonData);
				$jsonData = json_decode($jsonData);

                $idcard = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'card_no'=>trim($jsonData[0]->idcard)))->find();
				if($idcard){
                    echo $this->encode(array('status'=>1, 'info'=>'身份证已被注册，请联系客服'));
                    exit;
                }
				/*服务商资料*/
				$agentDatas = array(
						'wxuser_id'=>$this->userDatas['id'],
						'wxusers_id'=>$this->wxUserDatas['id'],
						'agent_no'=>trim($jsonData[0]->Code),
						'store_name'=>trim($jsonData[0]->store),
						'license'=>trim($jsonData[0]->license),
						'head_name'=>trim($jsonData[0]->head),
						'found_time'=>trim($jsonData[0]->ctime),
						'agent_address'=>trim($jsonData[0]->stagnation),
						'card_no'=>trim($jsonData[0]->idcard),
						'telephone'=>trim($jsonData[0]->yphone),
						'manager_name'=>trim($jsonData[0]->manager_name),
						'area'=>trim($jsonData[0]->area),
						'bigclass'=>trim($jsonData[0]->bigclass),
						'smallclass'=>trim($jsonData[0]->smallclass),
						'service'=>trim($jsonData[0]->specific),
						'content'=>trim($jsonData[0]->scontent),
						'is_review'=>1
				);
				$insert_agent = M('repair_agent')->add($agentDatas);
				
				/*服务商资料插入成功，插入技师数据*/
				if($insert_agent) {
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
					$insert_staff = M('repair_staff')->add($staffDatas);
					
					if ($insert_staff) {
						M('repair_agent')->where(array('id'=>$insert_agent))->save(array('staff_id'=>$insert_staff));
						/*添加学历和教育工作的*/
						if (($jsonData[0]->begin_1 != '') || ($jsonData[0]->end_1 != '') || ($jsonData[0]->org_1 != '') || ($jsonData[0]->profess_1 != '')) {
							$eduction = array(
									'staff_id'=>$insert_staff,
									'start_time'=>trim($jsonData[0]->begin_1),
									'end_time'=>trim($jsonData[0]->end_1),
									'school_name'=>trim($jsonData[0]->org_1),
									'major'=>trim($jsonData[0]->profess_1)
							);
							M('repair_train')->add($eduction);
						}
							
						if (($jsonData[0]->begin_2 != '') || ($jsonData[0]->end_2 != '') || ($jsonData[0]->org_2 != '') || ($jsonData[0]->profess_2 != '')) {
							$eduction = array(
									'staff_id'=>$insert_staff,
									'start_time'=>trim($jsonData[0]->begin_2),
									'end_time'=>trim($jsonData[0]->end_2),
									'school_name'=>trim($jsonData[0]->org_2),
									'major'=>trim($jsonData[0]->profess_2)
							);
							M('repair_train')->add($eduction);
						}
							
						if (($jsonData[0]->begin_3 != '') || ($jsonData[0]->end_3 != '') || ($jsonData[0]->org_3 != '') || ($jsonData[0]->profess_3 != '')) {
							$eduction = array(
									'staff_id'=>$insert_staff,
									'start_time'=>trim($jsonData[0]->begin_3),
									'end_time'=>trim($jsonData[0]->end_3),
									'school_name'=>trim($jsonData[0]->org_3),
									'major'=>trim($jsonData[0]->profess_3)
							);
							M('repair_train')->add($eduction);
						}							
						/*简历*/
						if (($jsonData[0]->start_1 !='') || ($jsonData[0]->over_1 !='') || ($jsonData[0]->com_1 !='') || ($jsonData[0]->pos_1 !='')) {
							$eduction = array(
									'staff_id'=>$insert_staff,
									'start_time'=>trim($jsonData[0]->start_1),
									'end_time'=>trim($jsonData[0]->over_1),
									'company'=>trim($jsonData[0]->com_1),
									'staff_position'=>trim($jsonData[0]->pos_1)
							);
							M('repair_resume')->add($eduction);
						}
							
						if (($jsonData[0]->start_2 !='') || ($jsonData[0]->over_2 !='') || ($jsonData[0]->com_2 !='') || ($jsonData[0]->pos_2 !='')) {
							$eduction = array(
									'staff_id'=>$insert_staff,
									'start_time'=>trim($jsonData[0]->start_2),
									'end_time'=>trim($jsonData[0]->over_2),
									'company'=>trim($jsonData[0]->com_2),
									'staff_position'=>trim($jsonData[0]->pos_2)
							);
							M('repair_resume')->add($eduction);
						}
							
						if (($jsonData[0]->start_3 !='') || ($jsonData[0]->over_3 !='') || ($jsonData[0]->com_3 !='') || ($jsonData[0]->pos_3 !='')) {
							$eduction = array(
									'staff_id'=>$insert_staff,
									'start_time'=>trim($jsonData[0]->start_3),
									'end_time'=>trim($jsonData[0]->over_3),
									'company'=>trim($jsonData[0]->com_3),
									'staff_position'=>trim($jsonData[0]->pos_3)
							);
							M('repair_resume')->add($eduction);
						}	
					}
					echo $this->encode(array('status'=>100, 'info'=>'加盟申请成功，请等待审核', 'url'=>'index.php?g=Wap&m=Repair&a=index&token='.$this->token."&openid=".$this->openid));
				}			
				
			} else {
				if (isset($_GET['Mid'])) {
					/*查找员工的信息*/
					$salerInfo = M('repair_saler')->where(array('wxuser_id'=>$this->userDatas['id'],'id'=>trim($_GET['id'])))->find();
					if (!empty($salerInfo)) {
						$this->assign('saler', $salerInfo['saler_name']);
					}					
				}				
				$encoded = rand(10000000, 100000000);//可能存在重复的，不可作为唯一标识
				$this->assign('encoded', $encoded);				
				
				/*查找省*/
				$province = M('area')->where(array('level'=>1))->select();
				$this->assign('province', $province);
				
				/*查找大类的数据*/
				$bigClass = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>0))->order('sort')->select();
				
				/*$data = array();
				foreach ($bigClass as $key=>$value) {
					$data[$key] = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>$value['id']))->order('sort')->select();
				}
				
				$secondClass = array();
				foreach ($data as $ke=>$val) {
					foreach ($val as $k=>$v) {
						$secondClass[] = $v;
					}
				} */
                $this->assign('bigClass', $bigClass);
                if($findAgent['is_review'] == 3){
                    /*查找第一个员工的资料*/
                    $fristStaff = M('repair_staff')->where(array('agent_id'=>$findAgent['id'], 'id'=>$findAgent['staff_id']))->find();
                    //	if (!empty($fristStaff)) {
                    $s_content = explode(',',$findAgent['content']);
                    $s_area = explode(',',$findAgent['area']);
                    $s_bigclass = explode(',',$findAgent['bigclass']);
                    $s_smallclass = explode(',',$findAgent['smallclass']);
                    array_pop($s_area);
                    array_pop($s_bigclass);
                    array_pop($s_smallclass);
                    $repairTrain = M('repair_train')->where(array('staff_id'=>$fristStaff['id']))->select();
                    $repairResume = M('repair_resume')->where(array('staff_id'=>$fristStaff['id']))->select();
                    $this->assign('s_content',$s_content);
                    $this->assign('s_area',$s_area);
                    $this->assign('s_bigclass',$s_bigclass);
                    $this->assign('s_smallclass',$s_smallclass);
                    $this->assign('repairTrain',$repairTrain);
                    $this->assign('repairResume',$repairResume);
                    $this->assign('fristStaff', $fristStaff);
                    $this->assign('agentInfo',$findAgent);
                    $this->display('tpl/Wap/default/Repair_index02.html');
                }else{
                    $this->display('tpl/Wap/default/Repair_index01.html');
                }
			}
			
		}
		
	}
	
	/*检验身份证*/
	public function checkCard() {
		if (IS_POST) {
			$cardNo = $_POST['card'];
			$findAgent = M('repair_agent')->where(array('card_no'=>$cardNo, 'is_review'=>2))->find();
			if (!empty($findAgent)) {
				if ($findAgent['is_forbidden'] == 1) {
					/*被禁用*/
					echo $this->encode(array('status'=>2, 'info'=>'你用的身份证被禁用'));exit;
				} else {
					echo $this->encode(array('status'=>100));
				}
			} else {
				echo $this->encode(array('status'=>1));
			}
		}
	}
	
	/*超找小类*/
	public function smallClass() {
		if (IS_POST) {
			$bigId = $_POST['id'];
			$bigClass = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>$bigId))->order('sort')->select();
			echo $this->encode(array('status'=>100, 'data'=>$bigClass));
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
	
	public function home() {
		$newsData = M('repair_release')->where(array('wxuser_id'=>$this->userDatas['id'], 'aim_at'=>array('neq',2)))->order('sort')->limit(3)->select();
		
		if (count($newsData) == 3) {
			$this->assign('isMore', 1);
		}
		
		$this->assign('newsData', $newsData);
		$this->display();
	}
	
	/*编辑*/
	public function edit() {	
		if (IS_POST) {
            $jsonData = $_POST['jsonstr'];
            $jsonData = htmlspecialchars_decode($jsonData);
            $jsonData = json_decode($jsonData);
            if(isset($_POST['is_review'])){
                $is_review = 1;
                $idcard = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>array('neq',$this->wxUserDatas['id']),'card_no'=>trim($jsonData[0]->idcard)))->find();
                if($idcard['card_no']){
                    echo $this->encode(array('status'=>1, 'info'=>'身份证已被注册，请联系客服'));  exit;
                }
            }else{
                $is_review = 2;
            }

			$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id']))->find();
            if ((!empty($find)) && ($find['is_review'] == 2 || $find['is_review'] == 3 )) {
				/*服务商资料*/
				$agentDatas = array(
						'store_name'=>trim($jsonData[0]->store),
						'license'=>trim($jsonData[0]->license),
						'head_name'=>trim($jsonData[0]->head),
						'found_time'=>trim($jsonData[0]->ctime),
						'agent_address'=>trim($jsonData[0]->stagnation),
						'card_no'=>trim($jsonData[0]->idcard),
						'telephone'=>trim($jsonData[0]->yphone),
						'manager_name'=>trim($jsonData[0]->manager_name),
						'area'=>trim($jsonData[0]->area),
						'bigclass'=>trim($jsonData[0]->bigclass),
						'smallclass'=>trim($jsonData[0]->smallclass),
						'service'=>trim($jsonData[0]->specific),
						'content'=>trim($jsonData[0]->scontent),
						'is_review'=>$is_review
				);

				$update_agent = M('repair_agent')->where(array('id'=>$find['id']))->save($agentDatas);
				
				if($update_agent !== false) {
					/*$staffDatas = array(
							'wxuser_id'=>$this->userDatas['id'],
							'wxusers_id'=>$this->wxUserDatas['id'],
							'agent_id'=>$find['id'],
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

						if (($jsonData[0]->begin_1 != '') || ($jsonData[0]->end_1 != '') || ($jsonData[0]->org_1 != '') || ($jsonData[0]->profess_1 != '')) {
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
					*/
					echo $this->encode(array('status'=>100, 'info'=>'你已修改成功，请等审核', 'url'=>'index.php?g=Wap&m=Repair&a=index&token='.$this->token."&openid=".$this->openid));
				}
			} else {
				echo $this->encode(array('status'=>'1','info'=>'你的帐号属于非审核账户，不能提交修改'));
			}		
		} else {
			$agentModel = M('repair_agent');
			$agentInfo = $agentModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			

			if (!empty($agentInfo)) {
				$this->assign('agentInfo', $agentInfo);
                $s_content = explode(',',$agentInfo['content']);
                $this->assign('s_content',$s_content);
				/*查找第一个员工的资料*/
				$fristStaff = M('repair_staff')->where(array('agent_id'=>$agentInfo['id'], 'id'=>$agentInfo['staff_id']))->find();
			//	if (!empty($fristStaff)) {
					$this->assign('fristStaff', $fristStaff);
					/*查找省*/
					//$province = M('repair_province')->select();
					$province = M('area')->where(array('level'=>1))->select();
					$this->assign('province', $province);
					
					/*查找大类的数据*/
					$bigClass = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>0))->order('sort')->select();
					
					$data = array();
					foreach ($bigClass as $key=>$value) {
						$data[$key] = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>$value['id']))->order('sort')->select();
					}
					
					$secondClass = array();
					foreach ($data as $ke=>$val) {
						foreach ($val as $k=>$v) {
							$secondClass[] = $v;
						}
					}
					$this->assign('bigClass', $bigClass);




                    $fristStaff = M('repair_staff')->where(array('agent_id'=>$agentInfo['id'], 'id'=>$agentInfo['staff_id']))->find();
                    //	if (!empty($fristStaff)) {
                    $s_content = explode(',',$agentInfo['content']);
                    $s_area = explode(',',$agentInfo['area']);
                    $s_bigclass = explode(',',$agentInfo['bigclass']);
                    $s_smallclass = explode(',',$agentInfo['smallclass']);
                    array_pop($s_bigclass);
                    array_pop($s_smallclass);
                    $repairTrain = M('repair_train')->where(array('staff_id'=>$fristStaff['id']))->select();
                    $repairResume = M('repair_resume')->where(array('staff_id'=>$fristStaff['id']))->select();
                    $this->assign('s_content',$s_content);
                    $this->assign('s_area',$s_area);
                    $this->assign('s_bigclass',$s_bigclass);
                    $this->assign('s_smallclass',$s_smallclass);
                    $this->assign('repairTrain',$repairTrain);
                    $this->assign('repairResume',$repairResume);
                    $this->assign('fristStaff', $fristStaff);
					
					/*查找相关的教育信息和工作经验信息*/
					$trainInfo = M('repair_train')->where(array('staff_id'=>$agentInfo['staff_id']))->select();
					if(!empty($trainInfo)){
						$this->assign('trainIs', 1);
						$this->assign('trainInfo', $trainInfo);
					}
					$resumeInfo = M('repair_resume')->where(array('staff_id'=>$agentInfo['staff_id']))->select();
					if(!empty($resumeInfo)){
						$this->assign('resumeIs', 1);
						$this->assign('resumeInfo', $resumeInfo);
					}
				//}
			}
			$this->display();
		}		
	}	
	/*新闻动态发布栏*/
	public function news() {
		
		if (isset($_GET['id'])) {
			/*查找图文*/
			$findNews = M('repair_release')->where(array('id'=>trim($_GET['id']), 'wxuser_id'=>$this->userDatas['id']))->find();
			if (!empty($findNews)) {
				$this->assign('news', $findNews);
			}			
		}
		$this->display();
	}
	
	/*展示更多消息*/
	public function moreNews() {
		$type = trim($_GET['type'])?trim($_GET['type']):1;
		if (1 == $type) {
			$newsData = M('repair_release')->where(array('wxuser_id'=>$this->userDatas['id'], 'aim_at'=>array('neq',2)))->order('sort')->select();
			$this->assign('newsData', $newsData);
			$this->assign('mark', 1);
		} elseif (2 == $type) {
			$newsData = M('repair_release')->where(array('wxuser_id'=>$this->userDatas['id'], 'aim_at'=>array('neq',1)))->order('sort')->select();
			$this->assign('newsData', $newsData);
			$this->assign('mark', 2);
		}
		
		$this->assign('news', $newsData);
		$this->display();
	}
	
	
	/*基本资料展示*/
	public function basic() {
		/*搜索相关的数据*/
		$agentModel = M('repair_agent');
		$agentInfo = $agentModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		if (!empty($agentInfo)) {
			$this->assign('agentInfo', $agentInfo);
			/*查找第一个员工的资料*/
			$fristStaff = M('repair_staff')->where(array('agent_id'=>$agentInfo['id'], 'id'=>$agentInfo['staff_id']))->find();
			if (!empty($fristStaff)) {
				$this->assign('fristStaff', $fristStaff);
				/*查找相关的教育信息和工作经验信息*/
				$trainInfo = M('repair_train')->where(array('staff_id'=>$agentInfo['staff_id']))->select();
				if(!empty($trainInfo)){
					$this->assign('trainIs', 1);
					$this->assign('trainInfo', $trainInfo);
				}
				$resumeInfo = M('repair_resume')->where(array('staff_id'=>$agentInfo['staff_id']))->select();
				if(!empty($resumeInfo)){
					$this->assign('resumeIs', 1);
					$this->assign('resumeInfo', $resumeInfo);
				}
			}
		}
		$this->display();
	}
	
	/*服务范围*/
	public function area() {
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		
		if (1 == $type) {
			if (IS_POST) {
				$jsonData = $_POST['jsonstr'];
				$jsonData = htmlspecialchars_decode($jsonData);
				$jsonData = json_decode($jsonData);
				
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id']))->find();
				
				if ((!empty($find)) && ($find['is_review']==2)) {
					$rangeData = array(
							'area'=>trim($jsonData[0]->area),
							'bigclass'=>trim($jsonData[0]->bigclass),
							'smallclass'=>trim($jsonData[0]->smallclass),
							'service'=>trim($jsonData[0]->specific),
							'content'=>trim($jsonData[0]->scontent),
							'is_review'=>1
					);
					/*区域修改也要处于审核状态*/
					$agentBack = M('repair_agent')->where(array('id'=>$find['id']))->save($rangeData);
					
					if ($agentBack) {
						echo $this->encode(array('status'=>100, 'info'=>'服务范围修改成功！', 'url'=>'index.php?g=Wap&m=Repair&a=home&token='.$this->token."&openid=".$this->openid));
					}
				} else {
					echo $this->encode(array('status'=>'1','info'=>'你的帐号属于非审核账户，不能提交修改'));
				}
					
				/*修改服务商的审核状态*/
			} else {
				/*查找省*/
				$province = M('area')->where(array('level'=>1))->select();
				$this->assign('province', $province);
				
				/*查找大类的数据*/
				$bigClass = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>0))->order('sort')->select();
				
				$data = array();
				foreach ($bigClass as $key=>$value) {
					$data[$key] = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>$value['id']))->order('sort')->select();
				}
				
				$secondClass = array();
				foreach ($data as $ke=>$val) {
					foreach ($val as $k=>$v) {
						$secondClass[] = $v;
					}
				}
				$this->assign('bigClass', $secondClass);
				$this->display("tpl/Wap/default/Repair_area_01.html");
			}	
		} else {
			$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($agentInfo)) {
				$this->assign('agentInfo', $agentInfo);
			}
			$this->display();
		}
					
	}
		
	/*查看优先权的客户*/
	public function firstUser() {		
		$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		if (!empty($agentInfo)) {
			$fristDatas = M('repair_user')->where(array('agent_id'=>$agentInfo['id']))->select();
			if (!empty($fristDatas)) {
				$this->assign('isNull', 0);
				$this->assign('data', $fristDatas);
			} else {
				$this->assign('isNull', 1);
			}
		}		
		$this->display();
	}
	
	/*查看员工的位置*/
	public function staff() {
		$type = trim($_GET['type']);
		if (1 == $type) {
			/*列出出发的员工*/
			$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($agentInfo)) {
				$staffData = M('repair_staff')->where(array('agent_id'=>$agentInfo['id'], 'staff_status'=>1))->order('staff_status desc')->select();
				
				if (!empty($staffData)) {
					$this->assign('data', $staffData);
				} else {
					$this->assign('staff', 1);
				}				
			} 
			$this->assign('type', $type);
			$this->display();
		} elseif (2 == $type) {
			/*列出所有员工*/
			$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($agentInfo)) {
				$staffData = M('repair_staff')->where(array('agent_id'=>$agentInfo['id']))->order('staff_status desc')->select();
				if (!empty($staffData)) {
					$this->assign('data', $staffData);
				} else {
					/*这个是没必要的*/
					$this->assign('staff', 1);
				}
			} 
			$this->assign('type', $type);
			$this->display();
		}
	}
	
	/*显示员工信息*/
	public function staffInfo() {
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			if (isset($_GET['id'])) {
				$staffId = trim($_GET['id']);
				$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				if (!empty($agentInfo)) {
					$find = M('repair_staff')->where(array('id'=>$staffId, 'agent_id'=>$agentInfo['id']))->find();
					if (!empty($find)) {
						$this->assign('fristStaff', $find);
						$this->display();					
					}
				}
			} else {
				$this->display();
			}
		} elseif (1 == $type) {
			if (isset($_GET['id'])) {
				$staffId = trim($_GET['id']);
				$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				if (!empty($agentInfo)) {
					$find = M('repair_staff')->where(array('id'=>$staffId, 'agent_id'=>$agentInfo['id']))->find();
					if (!empty($find)) {
						$this->assign('fristStaff', $find);
						if ($find['staff_status'] == 1) {
							$this->display("tpl/Wap/default/Repair_staffarea.html");
						} else {
							$this->display();
						}
					}
				}
			} else {
				$this->display();
			}
		}
		
	}
	
	/*员工管理*/
	public function manStaff() {
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			/*添加员工*/
			if (IS_POST) {
				$jsonData = $_POST['jsonstr'];
				$jsonData = htmlspecialchars_decode($jsonData);
				$jsonData = json_decode($jsonData);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id']))->find();
				
				if (!empty($find)) {
					$staffInfo = M('repair_staff')->where(array('agent_id'=>$find['id']))->select();
					$staffCount = count($staffInfo);
					
					$accessInfo = M('repair_agent_access')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$find['id']))->find();	
					$accessCount = $accessInfo['staff_mun'];
					if ($staffCount < $accessCount) {
						$staffDatas = array(
									'wxuser_id'=>$this->userDatas['id'],
									'agent_id'=>$find['id'],
									'staff_no'=>trim($jsonData[0]->staffno),
									'staff_pass'=>md5(trim($jsonData[0]->password)),
									'staff_name'=>trim($jsonData[0]->yname),
									'staff_sex'=>trim($jsonData[0]->ysex),
									'birth_time'=>trim($jsonData[0]->birthday),
									'staff_heigh'=>trim($jsonData[0]->yheight),
									'staff_cardno'=>trim($jsonData[0]->idcard),
									'staff_telphone'=>trim($jsonData[0]->yphone),
									'hukou_address'=>trim($jsonData[0]->yplace),
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
						$insert_staff = M('repair_staff')->add($staffDatas);
						if ($insert_staff) {
							/*添加学历和教育工作的*/
							
							/*教育*/
							if (($jsonData[0]->begin_1 != '') || ($jsonData[0]->end_1 != '') || ($jsonData[0]->org_1 != '') || ($jsonData[0]->profess_1 != '')) {
								$eduction = array(
									'staff_id'=>$insert_staff,
									'start_time'=>trim($jsonData[0]->begin_1),
									'end_time'=>trim($jsonData[0]->end_1),
									'school_name'=>trim($jsonData[0]->org_1),
									'major'=>trim($jsonData[0]->profess_1)
								);
								M('repair_train')->add($eduction);
							}
							
							if (($jsonData[0]->begin_2 != '') || ($jsonData[0]->end_2 != '') || ($jsonData[0]->org_2 != '') || ($jsonData[0]->profess_2 != '')) {
								$eduction = array(
									'staff_id'=>$insert_staff,
									'start_time'=>trim($jsonData[0]->begin_2),
									'end_time'=>trim($jsonData[0]->end_2),
									'school_name'=>trim($jsonData[0]->org_2),
									'major'=>trim($jsonData[0]->profess_2)
								);
								M('repair_train')->add($eduction);
							}
							
							if (($jsonData[0]->begin_3 != '') || ($jsonData[0]->end_3 != '') || ($jsonData[0]->org_3 != '') || ($jsonData[0]->profess_3 != '')) {
								$eduction = array(
										'staff_id'=>$insert_staff,
										'start_time'=>trim($jsonData[0]->begin_3),
										'end_time'=>trim($jsonData[0]->end_3),
										'school_name'=>trim($jsonData[0]->org_3),
										'major'=>trim($jsonData[0]->profess_3)
								);
								M('repair_train')->add($eduction);
							}
							
							/*简历*/
							if (($jsonData[0]->start_1 !='') || ($jsonData[0]->over_1 !='') || ($jsonData[0]->com_1 !='') || ($jsonData[0]->pos_1 !='')) {
								$eduction = array(
										'staff_id'=>$insert_staff,
										'start_time'=>trim($jsonData[0]->start_1),
										'end_time'=>trim($jsonData[0]->over_1),
										'company'=>trim($jsonData[0]->com_1),
										'staff_position'=>trim($jsonData[0]->pos_1)
								);
								M('repair_resume')->add($eduction);
							}
							
							if (($jsonData[0]->start_2 !='') || ($jsonData[0]->over_2 !='') || ($jsonData[0]->com_2 !='') || ($jsonData[0]->pos_2 !='')) {
								$eduction = array(
										'staff_id'=>$insert_staff,
										'start_time'=>trim($jsonData[0]->start_2),
										'end_time'=>trim($jsonData[0]->over_2),
										'company'=>trim($jsonData[0]->com_2),
										'staff_position'=>trim($jsonData[0]->pos_2)
								);
								M('repair_resume')->add($eduction);
							}
							
							if (($jsonData[0]->start_3 !='') || ($jsonData[0]->over_3 !='') || ($jsonData[0]->com_3 !='') || ($jsonData[0]->pos_3 !='')) {
								$eduction = array(
										'staff_id'=>$insert_staff,
										'start_time'=>trim($jsonData[0]->start_3),
										'end_time'=>trim($jsonData[0]->over_3),
										'company'=>trim($jsonData[0]->com_1),
										'staff_position'=>trim($jsonData[0]->pos_3)
								);
								M('repair_resume')->add($eduction);
							}
						}	
						echo $this->encode(array('status'=>100, 'info'=>'添加员工成功', 'url'=>'index.php?g=Wap&m=Repair&a=home&token='.$this->token."&openid=".$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'你添加的员工数达到上限了'));
					}
				}
				
			} else {
				$this->display('tpl/Wap/default/Repair_manStaff01.html');
			}
		} elseif (1 == $type) {
			/*编辑员工*/
			if (IS_POST) {
				$jsonData = $_POST['jsonstr'];
				$jsonData = htmlspecialchars_decode($jsonData);
				$jsonData = json_decode($jsonData);	
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id']))->find();
				
				if (!empty($find)) {
					$staffDatas = array(
							'wxuser_id'=>$this->userDatas['id'],
							'agent_id'=>$find['id'],
							'staff_no'=>trim($jsonData[0]->staffno),
							'staff_pass'=>md5(trim($jsonData[0]->password)),
							'staff_name'=>trim($jsonData[0]->yname),
							'staff_sex'=>trim($jsonData[0]->ysex),
							'birth_time'=>trim($jsonData[0]->birthday),
							'staff_heigh'=>trim($jsonData[0]->yheight),
							'staff_cardno'=>trim($jsonData[0]->idcard),
							'staff_telphone'=>trim($jsonData[0]->yphone),
							'hukou_address'=>trim($jsonData[0]->yplace),
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
						if (($jsonData[0]->begin_1 != '') || ($jsonData[0]->end_1 != '') || ($jsonData[0]->org_1 != '') || ($jsonData[0]->profess_1 != '')) {
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
					echo $this->encode(array('status'=>100, 'info'=>'编辑成功', 'url'=>'index.php?g=Wap&m=Repair&a=home&token='.$this->token."&openid=".$this->openid));
				}
			} else {
				if (isset($_GET['id'])) {
					$staffId = trim($_GET['id']);
					$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
					
					if (!empty($agentInfo)) {
						$find = M('repair_staff')->where(array('id'=>$staffId, 'agent_id'=>$agentInfo['id']))->find();
						
						if (!empty($find)) {
							$this->assign('fristStaff', $find);
							/*查找相关的教育信息和工作经验信息*/
							$trainInfo = M('repair_train')->where(array('staff_id'=>$find['id']))->select();
							if(!empty($trainInfo)){
								$this->assign('trainIs', 1);
								$this->assign('trainInfo', $trainInfo);
							}
							$resumeInfo = M('repair_resume')->where(array('staff_id'=>$find['id']))->select();
							if(!empty($resumeInfo)){
								$this->assign('resumeIs', 1);
								$this->assign('resumeInfo', $resumeInfo);
							}
						}
					}
				}
				$this->assign('type', $type);
				$this->display();
			}
		}
	}
	
	/*我的权限*/
	public function myPower() {
		$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id']))->find();
		if (!empty($find)) {	
			$accessInfo = M('repair_agent_access')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$find['id']))->find();
			$this->assign('accessInfo', $accessInfo);
		}
		$this->display();
	}
	
	/*我的二维码*/
	public function myCode() {
		
		$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		/*标记，用1000*/
		$parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 140'.$find['id'].'}}}';
		/*获取access_token*/
		$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
		if($api){
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
			$json = json_decode(file_get_contents($url_get));
			$access_token = $json->access_token;
			$imgSource = $this->creatTicket($access_token, $parament);
		}
		$this->assign('imgUrl', $imgSource['header']['url']);
		$this->display();
	}
	
	/*The two-dimensional code  BY NICK  */
	public function creatTicket($token, $parament) {
			
		/*发送数据到微信服务器端并获取数据*/
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
		$result = $this->api_notice_increment($url, $parament);
		$jsonInfo = json_decode($result, true);
		$ticket = $jsonInfo['ticket'];
	
		/*根据ticket获取图片资源*/
		$url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpInfo = curl_getinfo($ch);
		return array_merge(array('body'=>$package), array('header'=>$httpInfo));
	}
	
	/*我的签到*/
	public function mySign() {
		if (IS_POST) {
			$today = strtotime("today");
			$tomorrow = $today + 24*60*60;
			
			$signModel = M('member_card_sign');
			$conditions['token'] = $this->token;
			$conditions['wecha_id'] = $this->openid;
			$conditions['sign_time'] = array('between',array($today,$tomorrow));
			
			$isSign = $signModel->where($conditions)->find();
			if ((!empty($isSign)) && ($isSign['is_sign'] == '1')) {
				echo $this->encode(array('error'=>100, 'info'=>'你已经签到了'));
			} else {
				$signUpdate = $signModel->add(array('token'=>$this->token,'wecha_id'=>$this->openid,'sign_time'=>time(),'is_sign'=>1));
				if ($signUpdate == true) {
					
					$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
					$sige = M('repair_agent')->where(array('id'=>$find['id']))->save(array('sign_time'=>time()));
					/*发送消息*/
					$notichcontent = $find['head_name'].'您好，您在'.date('Y年m月d日 H时i分s秒').'签了到。可接收用户发送的服务订单';
					$postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
					$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
					$data = $this->api_notice_increment($url,http_build_query($postdata));				
					echo $this->encode(array('status'=>100, 'info'=>'签到成功', 'url'=>'index.php?g=Wap&m=Repair&a=mySign&token='.$this->token.'&openid='.$this->openid));
				}
			}
		} else {
			$today = strtotime("today");
			$tomorrow = $today + 24*60*60;
			$signModel = M('member_card_sign');
			$conditions['token'] = $this->token;
			$conditions['wecha_id'] = $this->openid;
			$conditions['sign_time'] = array('between',array($today,$tomorrow));
			$isSign = $signModel->where($conditions)->find();
				
			if (!empty($isSign)) {
				$this->assign('sign', 1);
			} else {
				$this->assign('sign', 0);
			}
			$this->display();
		}
	}
	
	/*充值recharge*/
	public function recharge() {
		
		if (IS_POST) {
			/*跳转到支付的页面*/
		} else {
			$this->display();
		}
	}
	
	/*我的订单*/
	public function myOrder() {
		/*查找所有的订单*/
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			/*确定接单*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId, 'status'=>1))->find();
					if (!empty($findOrder)) {
						$updateOrder = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>2));
						if ($updateOrder) {
							echo $this->encode(array('status'=>100, 'info'=>'成功确定订单', 'url'=>'index.php?g=Wap&m=Repair&a=home&token='.$this->token.'&openid='.$this->openid));
						} else {
							echo $this->encode(array('status'=>1, 'info'=>'系统繁忙'));
						}
					} else {
						echo $this->encode(array('status'=>2, 'info'=>'系统不存在该订单'));
					}
				}
			} else {
				/*待确定的订单*/
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				if (!empty($find)) {
					$orderInfo = M('repair_order')->where(array('agent_id'=>$find['id'], 'wxuser_id'=>$this->userDatas['id'], 'status'=>1))->order('order_time desc')->select();
					$this->assign('type', 0);
					if (!empty($orderInfo)) {
						$this->assign('isNull', 0);
						$this->assign('orderInfo', $orderInfo);
					} else {
						$this->assign('isNull', 1);
					}
				}
				$this->display();
			}		
		} elseif (1 == $type) {
			
			if (IS_POST) {
				/*查看员工的位置*/
				$orderId = trim($_POST['id']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId, 'status'=>3))->find();
					if (!empty($findOrder)) {
						echo $this->encode(array('status'=>100, 'info'=>'正在查找...', 'url'=>'index.php?g=Wap&m=Repair&a=staffInfo&token='.$this->token.'&openid='.$this->openid.'&id='.$findOrder['staff_id']));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'不能找到对应的订单...'));
					}
					
				}
			} else {
				/*服务中的订单*/
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				if (!empty($find)) {
					$condition['agent_id'] = $find['id'];
					$condition['wxuser_id'] = $this->userDatas['id'];
					$condition['status'] = array(array('eq',2),array('eq',3), 'or');
					$orderInfo = M('repair_order')->where($condition)->order('status desc,order_time desc')->select();
					$this->assign('type', $type);
					if (!empty($orderInfo)) {
						$this->assign('isNull', 0);
						$this->assign('orderInfo', $orderInfo);
					} else {
						$this->assign('isNull', 1);
					}	
				}	
				$this->display();
			}
			
		} elseif (2 == $type) {
			/*保修中的订单*/
			$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
            if (!empty($find)) {
				$condition['agent_id'] = $find['id'];
				$condition['wxuser_id'] = $this->userDatas['id'];
				$orderInfo = M('repair_order')->where($condition)->order('status desc,order_time desc')->select();
                $ordernewinfo = array();
                foreach($orderInfo as $k=>$v){
                    $endtime = intval($v['finish_time']) + ($v['period'])*24*3600;
                    if(time() < $endtime){
                        $ordernewinfo[]=$v;
                    }
                }
				$this->assign('type', $type);
				if (!empty($ordernewinfo)) {
					$this->assign('isNull', 0);
					$this->assign('orderInfo', $ordernewinfo);
				} else {
					$this->assign('isNull', 1);
				}	
			}
			$this->display();
		} elseif (3 == $type) {
			/*失去保修的订单*/
			$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($find)) {
				$condition['agent_id'] = $find['id'];
				$condition['wxuser_id'] = $this->userDatas['id'];
				$condition['finish_time'] = array('neq',0);
				$orderInfo = M('repair_order')->where($condition)->order('id desc')->select();
                $ordernewinfo = array();
                foreach($orderInfo as $k=>$v){
                    $endtime = intval($v['finish_time']) + ($v['period'])*24*3600;
                    if(time() >= $endtime || $v['status'] == 7){
                        $ordernewinfo[]=$v;
                    }
                }
				$this->assign('type', $type);
				if (!empty($ordernewinfo)) {
					$this->assign('isNull', 0);
					$this->assign('orderInfo', $ordernewinfo);
				} else {
					$this->assign('isNull', 1);
				}
			}
			$this->display();
		}
	}
	
	public function set() {
		$type = $type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			if (IS_POST) {
				/*技师出发*/
				$orderId = trim($_POST['id']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				
				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId, 'status'=>2))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>3));
						$updateStaff = M('repair_staff')->where(array('id'=>$findOrder['staff_id']))->save(array('staff_status'=>1));
						
						/*先查找用户*/
						$findUser = $this->wxUserModel->where(array('id'=>$findOrder['wxusers_id'], 'uid'=>$this->userDatas['id']))->find();
						if (!empty($findUser)) {
							
							/*工程师出发信息*/

                            /*文字消息
							$notichcontent = '尊敬的'.$findOrder['order_name'].'！您预约在'.$findOrder['appoint_day']." ".$findOrder['appoint_time'].$findOrder['repair_ele'].$findOrder['service_cont'].'维修技师（'.$findOrder['staff_name'].','.$findOrder['staff_tel'].'）已出发，请提前做好准备。';
							$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();							
							$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));
                            */

                            /*
                             * 模版消息
                             */

                            $staff = M('repair_staff')->where(array('agent_id'=>$findOrder['agent_id']))->find();

                            $openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
                            $openid = $openid['openid'];

                            $templateData['touser'] = $openid;
                            $templateData['template_id'] = 'Qaflf3xmvAebWbBRJigWu58nBBFnVsFYZq3rx_0tKgU';
                            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=wxOrder&type=1&token=".$this->token."&openid=".$openid;
                            $templateData['topcolor']="#FF0000";
                            $templateData['data']['first'] = array('value'=>'你的订单'.$findOrder['order_nid'].'，技师已出发。','color'=>"#173177");
                            $templateData['data']['keyword1'] = array('value'=>$staff['staff_name'],'color'=>"#173177");
                            $templateData['data']['keyword2'] = array('value'=>$findOrder['appoint_day'].' '.$findOrder['appoint_time'],'color'=>"#173177");
                            $templateData['data']['remark'] = array('value'=>"技师电话:".$findOrder['staff_tel'].",请做好准备。",'color'=>"#173177");
                            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
                            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
                            $data = $this->api_notice_increment($url,http_build_query($postdata));

                            if(!$data){
                                $this->api_notice_increment($url,http_build_query($postdata));
                            }




						}

						echo $this->encode(array('status'=>100, 'info'=>'员工已经出发了！','url'=>'index.php?g=Wap&m=Repair&a=myOrder&token='.$this->token.'&openid='.$this->openid."&type=1"));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'该订单已员工已经出发了'));
					}
				}
			}
		} elseif (1 == $type) {
			/*取消订单*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId, 'user_chang'=>0))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('user_change'=>1));
						echo $this->encode(array('status'=>100, 'info'=>'订单已取消！','url'=>'index.php?g=Wap&m=Repair&a=home&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'该订单已被你取消了....'));
					}
				}
			}
		} elseif (2 == $type) {
			/*确定用户取消订单*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId, 'user_change'=>1))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('is_change'=>1));

						/*确定预约取消发送消息*/  //您将时间从“预约时间”更改为“更改预约”，服务商已确认（电话：“手机”）

						$notichcontent = '您将时间从“'.$findOrder['appoint_time'].'”更改为“'.$findOrder['change_app_time'].'”，服务商已确认（电话：“'.$findOrder['staff_tel'].'”）';
						$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
						$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
						$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
						$data = $this->api_notice_increment($url,http_build_query($postdata));


						echo $this->encode(array('status'=>100, 'info'=>'确定预约更改成功！','url'=>'index.php?g=Wap&m=Repair&a=home&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统不存在该订单'));
					}
				}
			}
		} elseif (3 == $type) {
			/*服务完成*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$finish_time = strtotime($_POST['finish_time']);
                if(empty($finish_time)){
                    $finish_time = time();
                }else{
                	$finish_time = strtotime(($_POST['finish_time']).' '.date("H:i:s"));
                }
                $period = trim($_POST['period']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId, 'status'=>array('neq',4)))->find();
                    $staff = M('repair_staff')->where(array('agent_id'=>$find['id']))->find();
                    $findOrder['staff_name'] = $staff['staff_name'];
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>4, 'finish_time'=>$finish_time,'period'=>$period));
						/*先查找用户*/
						$findUser = $this->wxUserModel->where(array('id'=>$findOrder['wxusers_id'], 'uid'=>$this->userDatas['id']))->find();
						if (!empty($findUser)) {

							/*服务商维修完，发送信息*/
							//$notichcontent = '尊敬的'.$findOrder['order_name'].'用户，您好！您的'.$findOrder['repair_ele'].'维修已完成，请进行服务评价。';
							$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
                            $openid = $openid['openid'];

                            $templateData['touser'] = $openid;
                            $templateData['template_id'] = 'yhHn2KV6P7YFF0wioKTTlIUC1AIJ4kapdaPFhlkhRj4';
                            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=wxOrder&type=2&token=".$this->token."&openid=".$openid;
                            $templateData['topcolor']="#FF0000";
                            $templateData['data']['first'] = array('value'=>'尊敬的'.$findOrder['order_name'].'您好！您的1号服务家电安装、维修服务已经完成。','color'=>"#173177");
                            $templateData['data']['Content1'] = array('value'=>'','color'=>"#173177");
                            $templateData['data']['Good'] = array('value'=>$findOrder['repair_ele'],'color'=>"#173177");
                            $templateData['data']['contentType'] = array('value'=>$findOrder['service_cont'],'color'=>"#173177");
                            $templateData['data']['price'] = array('value'=>'以和技师协商为准，不得超过1号服务收费标准','color'=>"#173177");
                            $templateData['data']['menu'] = array('value'=>'请参考【技师专区】→【收费标准】','color'=>"#173177");
                            $templateData['data']['remark'] = array('value'=>"技师姓名:".$findOrder['staff_name'].",技师电话:".$findOrder['staff_tel'].",除以下情况均需保修：调试、空调清洗、拆装，客户要求不检漏情况加氟，电脑办公软件安装等。您可以通过【用户专区】→【我的订单】→【保修期】找到保修期内的订单，如需保修，点击“我要返修”即可。",'color'=>"#173177");
                            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
                            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
                            $data = $this->api_notice_increment($url,http_build_query($postdata));

                            if(!$data){
                                $this->api_notice_increment($url,http_build_query($postdata));
                            }


                            /*
                            $postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));
						    */
                        }
						echo $this->encode(array('status'=>100, 'info'=>'服务完成更改成功！','url'=>'index.php?g=Wap&m=Repair&a=myOrder&token='.$this->token.'&openid='.$this->openid."&type=2"));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统不存在该订单'));
					}
				}
			}else{
                $findOrder = M('repair_order')->where(array('id'=>$_GET['id']))->find();
                $beginTimeStamp = strtotime($findOrder['appoint_day']);
                $tmp = array();
                for($i=$beginTimeStamp;$i<=$beginTimeStamp+10*24*3600;$i+=(24*3600)){
                    $tmp[]=date('Y-m-d',$i);
                }
                $this->assign('daylist',$tmp);
                $this->assign('order_id',$_GET['id']);
                $this->assign('findOrder',$findOrder);
                $this->display('tpl/Wap/default/Repair_complete.html');
            }
		} elseif (4 == $type) {
			if (IS_POST) {
				/*来店维修完成*/
				$orderId = trim($_POST['id']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId, 'status'=>array('neq',4)))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>4, 'finish_time'=>time(), 'period'=>2));
						/*先查找用户*/
						$findUser = $this->wxUserModel->where(array('id'=>$findOrder['wxusers_id'], 'uid'=>$this->userDatas['id']))->find();
						if (!empty($findUser)) {

							/*服务商发送 来店维修完成信息*/
							$notichcontent = '尊敬的'.$findOrder['order_name'].'用户，您好！您送修的'.$findOrder['repair_ele'].'，维修工程师“'.$findOrder['staff_name'].'”（电话：“'.$findOrder['staff_tel'].'”）已维修完成。';

							$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
							$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);

							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));
						}
						echo $this->encode(array('status'=>100, 'info'=>'来店维修已完成！','url'=>'index.php?g=Wap&m=Repair&a=home&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统不存在该订单'));
					}
				}
			}
		} elseif (5 == $type) {
			if (IS_POST) {
				/*返修完成*/
				$orderId = trim($_POST['id']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId))->find();

                    $staff = M('repair_staff')->where(array('agent_id'=>$findOrder['agent_id']))->find();
                    $findOrder['staff_name'] = $staff['staff_name'];

					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>4));
						/*先查找用户*/
						$findUser = $this->wxUserModel->where(array('id'=>$findOrder['wxusers_id'], 'uid'=>$this->userDatas['id']))->find();
						if (!empty($findUser)) {

							/*服务商 返修处理完成信息*/
                            /*
							$notichcontent = '尊敬的'.$findOrder['order_name'].'用户，您好！您返修的'.$findOrder['repair_ele'].'维修已完成，请进行服务评价。';

							$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
							$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));
                            */
                            $staff = M('repair_staff')->where(array('agent_id'=>$findOrder['agent_id']))->find();
                            $findOrder['staff_name'] = $staff['staff_name'];

                            $openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
                            $openid = $openid['openid'];

                            $templateData['touser'] = $openid;
                            $templateData['template_id'] = 'yhHn2KV6P7YFF0wioKTTlIUC1AIJ4kapdaPFhlkhRj4';
                            $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=wxOrder&type=2&token=".$this->token."&openid=".$openid;
                            $templateData['topcolor']="#FF0000";
                            $templateData['data']['first'] = array('value'=>'尊敬的'.$findOrder['order_name'].'您好！','color'=>"#173177");
                            $templateData['data']['Content1'] = array('value'=>'','color'=>"#173177");
                            $templateData['data']['Good'] = array('value'=>$findOrder['repair_ele'],'color'=>"#173177");
                            $templateData['data']['contentType'] = array('value'=>$findOrder['service_cont'],'color'=>"#173177");
                            $templateData['data']['price'] = array('value'=>0,'color'=>"#173177");
                            $templateData['data']['menu'] = array('value'=>'保修期内，无收费','color'=>"#173177");
                            $templateData['data']['remark'] = array('value'=>"技师姓名:".$findOrder['staff_name'].",技师电话:".$findOrder['staff_tel'],'color'=>"#173177");
                            $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
                            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
                            $data = $this->api_notice_increment($url,http_build_query($postdata));

                            if(!$data){
                                $this->api_notice_increment($url,http_build_query($postdata));
                            }
						}
						echo $this->encode(array('status'=>100, 'info'=>'返修已完成！','url'=>'index.php?g=Wap&m=Repair&a=myOrder&token='.$this->token.'&openid='.$this->openid."&type=2"));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统不存在该订单'));
					}
				}
			}
		} elseif (6 == $type) {
			/*取消订单*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('agent_id'=>$find['id'], 'id'=>$orderId, 'is_tel'=>0))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('is_tel'=>1));
						echo $this->encode(array('status'=>100, 'info'=>'操作成功！','url'=>'index.php?g=Wap&m=Repair&a=myOrder&type=1&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'已经反馈过了'));
					}
				}
			}

		}
	}

	/*服务商分配订单*/
	public function allot() {

		if (IS_POST) {
			if (isset($_POST['staff'])) {
				$staffId = $_POST['staff'];
				$orderId = $_POST['id'];
				$staffInfo = M('repair_staff')->where(array('id'=>$staffId))->find();
				//表示员工分配好了
				if (!empty($staffInfo)) {
					$data = array(
							'staff_id'=>$staffInfo['id'],
							'staff_name'=>$staffInfo['staff_name'],
							'staff_tel'=>$staffInfo['staff_telphone'],
							'status'=>8
					);
					$findOrder = M('repair_order')->where(array('id'=>$orderId))->find();
					if (!empty($findOrder)) {

						$updateOrder = M('repair_order')->where(array('id'=>$findOrder['id']))->save($data);
						$str = M('repair_order')->getLastSql();
						if ($updateOrder !== false) {

							/*发送消息*/  //公司已将“地址”的用户的服务订单（“电气”+“服务内容”）分配给你，预约时间为“预约时间”，请在30分钟内联系用户（姓名+手机）。

							$notichcontent = '公司已将“'.$findOrder['order_address'].'”的用户的服务订单（“'.$findOrder['repair_ele'].$findOrder['service_cont'].'”）分配给你，预约时间为“'.$findOrder['appoint_time'].'”，请在30分钟内联系用户（'.$findOrder['order_name'].'+'.$findOrder['order_tel'].'）。';
							$openid = M('wxusers')->where(array('id'=>$staffInfo['wxusers_id']))->field('openid')->find();
							$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));

							echo $this->encode(array('status'=>100, 'info'=>'订单分配成功', 'url'=>'index.php?g=Wap&m=Repair&a=home&token='.$this->token.'&openid='.$this->openid));
						}
					}
				}
			} else {
				echo $this->encode(array('status'>1, 'info'=>'参数不正确', 'url'=>'index.php?g=Wap&m=Repair&a=allot&token='.$this->token.'&openid='.$this->openid));
			}
		} else {
			/*查找所有的技师*/
			if (isset($_GET['id'])) {
				$orderId = trim($_GET['id']);
				$this->assign('id', $orderId);
			}
			$find = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			$staffData = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$find['id']))->select();
			$this->assign('staffData', $staffData);
			$this->display();
		}
	}


	/********************************** staff *******************************************/

	/*员工登录*/
	public function staffIndex() {
		if (IS_POST) {
			if (isset($_POST['username']) && isset($_POST['password'])) {
				$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'],'staff_telphone'=>trim($_POST['username'])))->find();
				if (!empty($findStaff)) {
					if (md5(trim($_POST['password']) === $findStaff['staff_pass'])) {
						$saveBack = M('repair_staff')->where(array('id'=>$findStaff['id']))->save(array('is_bind'=>1, 'wxusers_id'=>$this->wxUserDatas['id']));
						echo $this->encode(array('status'=>100, 'info'=>'登录成功', 'url'=>'index.php?g=Wap&m=Repair&a=staffHome&token='.$this->token.'&openid='.$this->openid));
					} else{
						echo $this->encode(array('status'=>1, 'info'=>'帐号或密码错误！'));
					}
				} else {
					echo $this->encode(array('status'=>1, 'info'=>'系统中中不存在该用户！'));
				}
			} else {
				echo $this->encode(array('status'=>1, 'info'=>'数据不齐全！'));
			}

		} else {
			$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'is_bind'=>1))->find();
			if (!empty($findStaff)) {
				$newsData = M('repair_release')->where(array('wxuser_id'=>$this->userDatas['id'], 'aim_at'=>array('neq',1)))->order('sort')->limit(3)->select();

				if (count($newsData) == 3) {
					$this->assign('isMore', 1);
				}
				$this->assign('newsData', $newsData);
				$this->display('tpl/Wap/default/Repair_staffHome.html');
			} else {
				$this->display();
			}
		}
	}

	/*员工的基本资料*/
	public function baseStaff() {

		$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'is_bind'=>1))->find();
		if (!empty($findStaff)) {
			$this->assign('staffInfo', $findStaff);
		}
		$this->display();
	}

	/*员工签到*/
	public function staffSign() {
		if (IS_POST) {

			/*员工签到，提交经纬度*/
			$staffId = trim($_POST['id']);
			$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'id'=>$staffId))->find();
			if (!empty($findStaff)) {
				$updateData = array(
					'staff_place'=>trim($_POST['place']),
					'longitude'=>trim($_POST['lng']),
					'latitude'=>trim($_POST['lat']),
				);
				$updateBack = M('repair_staff')->where(array('id'=>$findStaff['id']))->save($updateData);
				echo $this->encode(array('status'=>100, 'info'=>'签到成功', 'url'=>'index.php?g=Wap&m=Repair&a=staffHome&token='.$this->token.'&openid='.$this->openid));
			}
		} else {
			$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'staff_status'=>1))->find();
			if (!empty($findStaff)) {
				$this->assign('isNull', 0);
				$this->assign('staffInfo', $findStaff);
			} else {
				$this->assign('isNull', 1);
			}
			$this->display();
		}
	}

	/*员工的二维码*/
	public function staffCode() {
		$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		$parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$findStaff['id'].'}}}';
		/*获取access_token*/
		$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
		if($api){
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
			$json = json_decode(file_get_contents($url_get));
			$access_token = $json->access_token;
			$imgSource = $this->creatTicket($access_token, $parament);
		}
		$this->assign('imgUrl', $imgSource['header']['url']);
		$this->display();
	}


	/*员工的订单*/
	public function staffOrder() {
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (1 == $type) {
			/*保修中的订单*/
			$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($findStaff)) {
				$condition['wxusers_id'] = $this->wxUserDatas['id'];
				$condition['staff_id'] = $findStaff['id'];
				$condition['period'] = array('eq',1);
				$orderInfo = M('repair_order')->where($condition)->select();
				$this->assign('type', $type);
				if (!empty($orderInfo)) {
					$this->assign('isNull', 0);
					$this->assign('orderInfo', $orderInfo);
				} else {
					$this->assign('isNull', 1);
				}
			}

			$this->display();
		} else {
		/*服务中的订单*/
			$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($findStaff)) {
					$condition['staff_id'] = $findStaff['id'];
					$condition['wxuser_id'] = $this->userDatas['id'];
					$condition['status'] = array(array('eq',2),array('eq',3),array('eq',4), 'or');

					$orderInfo = M('repair_order')->where($condition)->select();

					$this->assign('type', $type);
					if (!empty($orderInfo)) {
						$this->assign('isNull', 0);
						$this->assign('orderInfo', $orderInfo);
					} else {
						$this->assign('isNull', 1);
					}
					$this->display();
			}
		}

	}

	public function staffSet() {
	$type = $type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			if (IS_POST) {
				/*工程师出发*/
				$orderId = trim($_POST['id']);
				$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($findStaff)) {
					$findOrder = M('repair_order')->where(array('staff_id'=>$findStaff['id'], 'id'=>$orderId, 'status'=>2))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>3));
						$updateStaff = M('repair_staff')->where(array('id'=>$findOrder['staff_id']))->save(array('staff_status'=>1));

						/*先查找用户*/
						$findUser = $this->wxUserModel->where(array('id'=>$findOrder['wxusers_id'], 'uid'=>$this->userDatas['id']))->find();
						if (!empty($findUser)) {

							/*工程师出发信息*/
							$notichcontent = '尊敬的'.$findOrder['order_name'].'用户，您好！您预约在'.$findOrder['appoint_time'].$findOrder['repair_ele'].$findOrder['service_cont'].'工程师（'.$findOrder['staff_name'].'+'.$findOrder['staff_tel'].'）已出发，请提前做好准备。';
							$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
                            $postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));
						}

						echo $this->encode(array('status'=>100, 'info'=>'员工已经出发了！','url'=>'index.php?g=Wap&m=Repair&a=staffHome&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'该订单已员工已经出发了'));
					}
				}
			}
		} elseif (1 == $type) {
			/*取消订单*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($findStaff)) {
					$findOrder = M('repair_order')->where(array('staff_id'=>$findStaff['id'], 'id'=>$orderId, 'user_chang'=>0))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('user_change'=>1));
						echo $this->encode(array('status'=>100, 'info'=>'订单已取消！','url'=>'index.php?g=Wap&m=Repair&a=staffHome&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'该订单已被你取消了....'));
					}
				}
			}
		} elseif (2 == $type) {
			/*确定用户取消订单*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$findStaff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($findStaff)) {
					$findOrder = M('repair_order')->where(array('staff_id'=>$findStaff['id'], 'id'=>$orderId, 'user_change'=>1))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('is_change'=>1));

						/*确定预约取消发送消息*/
						$notichcontent = '您将时间从“'.$findOrder['appoint_time'].'”更改为“'.$findOrder['change_app_time'].'”，服务商已确认（电话：“'.$findOrder['staff_tel'].'”）';
						$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
						$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
						$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
						$data = $this->api_notice_increment($url,http_build_query($postdata));

						echo $this->encode(array('status'=>100, 'info'=>'确定预约更改成功！','url'=>'index.php?g=Wap&m=Repair&a=staffHome&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统不存在该订单'));
					}
				}
			}
		} elseif (3 == $type) {
			/*服务完成*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$find = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('staff_id'=>$find['id'], 'id'=>$orderId, 'status'=>array('neq',4)))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>4, 'finish_time'=>time(), 'period'=>2));
						/*先查找用户*/
						$findUser = $this->wxUserModel->where(array('id'=>$findOrder['wxusers_id'], 'uid'=>$this->userDatas['id']))->find();
						if (!empty($findUser)) {

							$notichcontent = '尊敬的'.$findOrder['order_name'].'用户，您好！您的'.$findOrder['repair_ele'].'维修已完成，请进行服务评价。';
							$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
							$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));

						}
						echo $this->encode(array('status'=>100, 'info'=>'服务完成更改成功！','url'=>'index.php?g=Wap&m=Repair&a=staffHome&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统不存在该订单'));
					}
				}
			}
		} elseif (4 == $type) {
			/*来店维修完成*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$find = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('staff_id'=>$find['id'], 'id'=>$orderId, 'status'=>array('neq',4)))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>4, 'finish_time'=>time(), 'period'=>2));
						/*先查找用户*/
						$findUser = $this->wxUserModel->where(array('id'=>$findOrder['wxusers_id'], 'uid'=>$this->userDatas['id']))->find();
						if (!empty($findUser)) {

							$notichcontent = '尊敬的'.$findOrder['order_name'].'用户，您好！您送修的'.$findOrder['repair_ele'].'，维修工程师“'.$findOrder['staff_name'].'”（电话：“'.$findOrder['staff_tel'].'”）已维修完成。';

							$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
							$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);

							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));
						}
						echo $this->encode(array('status'=>100, 'info'=>'来店维修已完成！','url'=>'index.php?g=Wap&m=Repair&a=staffHome&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统不存在该订单'));
					}
				}
			}
		} elseif (5 == $type) {
			/*返修完成*/
			if (IS_POST) {
				$orderId = trim($_POST['id']);
				$find = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

				if (!empty($find)) {
					$findOrder = M('repair_order')->where(array('staff_id'=>$find['id'], 'id'=>$orderId, 'period'=>1))->find();
					if (!empty($findOrder)) {
						$update = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('status'=>4, 'finish_time'=>time(), 'period'=>2));
						/*先查找用户*/
						$findUser = $this->wxUserModel->where(array('id'=>$findOrder['wxusers_id'], 'uid'=>$this->userDatas['id']))->find();
						if (!empty($findUser)) {

							$notichcontent = '尊敬的'.$findOrder['order_name'].'用户，您好！您返修的'.$findOrder['repair_ele'].'维修已完成，请进行服务评价。';

							$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
							$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));
						}
						echo $this->encode(array('status'=>100, 'info'=>'返修已完成！','url'=>'index.php?g=Wap&m=Repair&a=staffHome&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统不存在该订单'));
					}
				}
			}
		}
	}

	public function logout() {
		$find = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		if (!empty($find) && ($find['is_bind'] ==1)) {
			$updateBack = M('repair_staff')->where(array('id'=>$find['id']))->save(array('is_bind'=>0));
			$this->assign('type', 4);
			$this->display('tpl/Wap/default/Repair_agent_exit.html');
		}
	}

	/********************************** wxusesrs **************************************/

	/*wxhome*/
	public function wxhome() {
		$newsData = M('repair_release')->where(array('wxuser_id'=>$this->userDatas['id'], 'aim_at'=>array('neq',1)))->order('sort')->limit(3)->select();

		if (count($newsData) == 3) {
			$this->assign('isMore', 1);
		}
		$this->assign('newsData', $newsData);
		$this->display();
	}

	/*用户报障，先关注后报障*/
	public function wxWarn() {

		if (IS_POST) {
			$type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : 1;
			$jsonData = $_POST['jsonstr'];
			$jsonData = htmlspecialchars_decode($jsonData);
			$jsonData = json_decode($jsonData);

			/*查找用户*/
			$rapairUser = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

			$insertData = array(
					'wxuser_id'=>$this->userDatas['id'],
					'wxusers_id'=>$this->wxUserDatas['id'],
					'order_nid'=>$this->getSn(),
					'appoint_day'=>trim($jsonData[0]->AppointmentD),
					'appoint_time'=>$jsonData[0]->AppointmentT,
					'order_time'=>$_SERVER['REQUEST_TIME'],
					'service_type'=>trim($jsonData[0]->wxtype),
					'repair_ele'=>trim($jsonData[0]->electric),
					'service_cont'=>trim($jsonData[0]->scontent),
					'operate'=>trim($jsonData[0]->airjob),
					'fault_info'=>trim($jsonData[0]->Fault),
					'is_warranty'=>1,
					'warranty_time'=>date('Y-m-d',time()),
					'warranty_day'=>trim($jsonData[0]->bxTime),
					'pay_type'=>trim($jsonData[0]->Payment),
					'coupon_cn'=>trim($jsonData[0]->coup),
					'order_name'=>trim($jsonData[0]->yname),
					'order_tel'=>trim($jsonData[0]->phoneNo),
					'order_air'=>$rapairUser['area'],
					'order_address'=>trim($jsonData[0]->address),
					'status'=>1,
					'is_read'=>0,
                    'score'=>trim($jsonData[0]->score),
                    'type'=>$type
			);


			/*插入数据*/
			$insertBack = M('repair_order')->add($insertData);

            /*
             * 减少积分
             */
            if(trim($jsonData[0]->score)) {
                M('Repair_user')->where(array('wxuser_id' => $this->userDatas['id'], 'wxusers_id' => $this->wxUserDatas['id']))->setDec('score', trim($jsonData[0]->score));
            }
            if ($insertBack) {
				/*向所有的服务商发送图文消息*/
				/*优惠券的记录修改*/
				$couponId = trim($jsonData[0]->coup);
				$findCoupon = M('usercenter_user_salecard')->where(array('token'=>$this->token, 'openid'=>$this->openid,'id'=>$couponId,'status'=>0))->find();
				if (!empty($findCoupon)) {
					M('usercenter_user_salecard')->where(array('id'=>$findCoupon['id']))->save(array('status'=>1, 'used_time'=>time()));
					//如果表中有消费时间字段，则就添加一个消费的时间段
				}
				/*查找所有的服务商*/
				$str = rtrim( trim($jsonData[0]->scontent), ',' );
				$service_con = explode(',', $str);

				$condition['area'] = array(
						array('like', "%".$rapairUser['area']."%"),
				);
				$condition['bigclass'] = array(
						array('like', "%".$jsonData[0]->wxtype."%")
				);
				$condition['wxuser_id'] = $this->userDatas['id'];
				$condition['is_review'] = 2;

				$condition['smallclass'] = array(
						array('like', "%".$jsonData[0]->electric."%")
				);

				//$condition['sign_time'] = array(array('gt', time()-48*60*60), array('lt', time()));
				/*查找类*/
				$agentInfo = M('repair_agent')->where($condition)->order('id')->select();
 				//echo M('repair_agent')->getlastsql();
// 				exit();
				$agentDatas = array();
				if (!empty($agentInfo)) {
					foreach ($agentInfo as $key=>$value){
					   /*	$str1 = rtrim( trim($value['content']), ',' );
						$strcontent = explode(',', $str1);
						$result = array_diff($service_con, $strcontent);
						if (empty($result)) {
							/*还得去判断是否被屏蔽*/
                        $find = M('repair_screen')->where(array('wxusers_id'=>$this->wxUserDatas['id'], 'agent_id'=>$value['id']))->find();

                        $user_id = M('repair_agent')->field('wxuser_id')->where(array('id'=>$value['id']))->find();
                        /*
                         * 评分大于3分
                         */
                        /*
                        $appraise = M('Appraise')->where(array('wxuser_id'=>$user_id['wxuser_id'], 'wxusers_id'=>$this->wxUserDatas['id']))->order('id desc')->find();
                        if(!$appraise){
                            $score = 5;
                        }else{
                            $score = $appraise['score'];
                        }
                         */
                        /*
                         * 是否投诉
                         */
                        /*
                        $complaindata = M('repair_complain')->where(array('agent_id'=>$value['id'],'wxusers_id'=>$this->wxUserDatas['id'],'status'=>1))->order('id desc')->find();
                        */
                        if (empty($find)  && empty($complaindata)) {
                            $agentDatas[] = $value;
                        }


					}
				}

				$agentCount = count($agentDatas);
                /*
                 * 未找到服务商发送消息
                 */
                if($agentCount == 0){
                    $notichcontent = '非常感谢您的关注和使用！非常抱歉！您所在的区域暂无您需要的服务技师。';
                    $postdata = array('openid' => $this->openid, 'token' => $this->token, 'content' => $notichcontent);
                    $url = C('site_url') . "index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
                    $data = $this->api_notice_increment($url, http_build_query($postdata));
                }

				for ($i = 0; $i < $agentCount; $i++) {

					$agentWxId = $agentDatas[$i]['wxusers_id'];
					$openid = M('wxusers')->where(array('id'=>$agentWxId))->find();

					$openid = $openid['openid'];

// 					$notichcontent = "<a href=\"http://v.wapwei.com/index.php?g=Wap&m=Repair&a=grab&token=".$this->token.'&openid='.$openid."\">http://v.wapwei.com/index.php?g=Wap&m=Repair&a=wxhome&token=".$this->token.'&openid='.$openid."</a>";
// 					$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
// 					$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
// 					$data = $this->api_notice_increment($url,http_build_query($postdata));


                    /*发送给用户的 直发一次*/
                    if($i == 0) {
                        $notichcontent = '您的预约已发送成功，请保持通话畅通。';
                        $postdata = array('openid' => $this->openid, 'token' => $this->token, 'content' => $notichcontent);
                        $url = C('site_url') . "index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
                        $data = $this->api_notice_increment($url, http_build_query($postdata));
                    }

                    if($type == 1){
                    	$dingdanType = '保外单';
                    	$min = 10;
                    }else if($type == 2){
                    	$dingdanType = '保内单';
                    	$min = 10;
                    }
					/*发送给服务商的*/

                    $templateData['touser'] = $openid;
                    $templateData['template_id'] = 'XseMeBde00Fj-9Ox_xhBT2tOnYehS7YJUQ9ziCEwnss';
                    $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=grab&token=".$this->token."&openid=".$openid."&id=".$insertBack;
                    $templateData['topcolor']="#FF0000";
                    $templateData['data']['first'] = array('value'=>'有新订单，请点击抢单','color'=>"#173177");
                    $templateData['data']['tradeDateTime'] = array('value'=>date('Y年m月d日H时i分s秒'),'color'=>"#173177");
                    $templateData['data']['orderType'] = array('value'=>$dingdanType,'color'=>"#173177");
                    $templateData['data']['customerInfo'] = array('value'=>$jsonData[0]->address."-".$jsonData[0]->yname,'color'=>"#173177");
                    $templateData['data']['orderItemName'] = array('value'=>'家电报障','color'=>"#173177");
                    $templateData['data']['orderItemData'] = array('value'=>$jsonData[0]->electric.$insertData['service_cont']." ".trim($jsonData[0]->Fault),'color'=>"#173177");

                    $templateData['data']['remark'] = array('value'=>"预约时间为".$jsonData[0]->AppointmentD." ".$jsonData[0]->AppointmentT.",请点击接单,在接单成功后在".$min."分钟回电",'color'=>"#173177");
                    $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
                    $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
                    $data = $this->api_notice_increment($url,http_build_query($postdata));

                    if(!$data){
                        $this->api_notice_increment($url,http_build_query($postdata));
                    }
                    /*
					$param = array();
					$notichcontent = '“'.$jsonData[0]->address.'”'.$jsonData[0]->yname.'在'.date('Y年m月d日H时i分s秒').'发起一个“'.$jsonData[0]->electric.$insertData['service_cont'].'”预约时间为“'.$jsonData[0]->AppointmentD.'”，请接单成功后在30分钟回电。';
					$param['title'] = '订单通知';
					$param['description'] = $notichcontent;
					$param['url'] ='http://v.wapwei.com/index.php?g=Wap&m=Repair&a=grab&token='.$this->token.'&openid='.$openid.'&id='.$insertBack;
					$param['picurl'] = '';
					$postdata = array('openid'=>$openid,'token'=>$this->token,'content'=>json_encode($param));
					$url =C('site_url')."/index.php?g=Home&m=Auth&a=sendNewsMsg";
					$data = $this->api_notice_increment($url,http_build_query($postdata));
					if(!$data){
						$this->api_notice_increment($url,http_build_query($postdata));
					}
                    */



				}



				echo $this->encode(array('status'=>100, 'info'=>'下单成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxOrder&token='.$this->token.'&openid='.$this->openid));

			}

		} else {
			$repair_user = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

			if (!empty($repair_user)) {
				$this->assign('repair_user', $repair_user);
			}
		/*	else{
                $this->redirect('Wap/Repair/wxEdit',array('token'=>$this->token, 'openid'=>$this->openid,'type'=>1));
            }*/

			/*查找大类的数据*/
			$bigClass = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>0))->order('sort')->select();

			/*$data = array();
			foreach ($bigClass as $key=>$value) {
				$data[$key] = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>$value['id']))->order('sort')->select();
			}

			$secondClass = array();
			foreach ($data as $ke=>$val) {
				foreach ($val as $k=>$v) {
					$secondClass[] = $v;
				}
			} */
			$this->assign('bigClass', $bigClass);


			//$couponInfo = M('repair_coupon')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'status'=>0))->select();
			//$couponInfo = M('usercenter_user_salecard')->where(array('token'=>$this->token, 'openid'=>$this->openid, 'status'=>0))->select();
            $userscorearr = M('Repair_user')->field('score')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			$this->assign('userscorearr', $userscorearr);
			$this->assign('type',$_GET['type']);
			$this->display();
		}
	}
	
	public function getSn(){
	    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}


	/*屏蔽用户*/
	public function screen() {
        if(IS_POST){
            $exdata = explode('#',$_POST['checkeddata']);
            foreach($exdata as $k=>$v){
                $temp = explode('|',$v);
                $find = M('repair_screen')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id'],'agent_id'=>$temp[0]))->find();
                if($find){
                    if($temp[1] == 0){
                        if(!M('repair_screen')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id'],'agent_id'=>$temp[0]))->delete()){
                            echo $this->encode(array('status'=>1, 'info'=>'设置失败', 'url'=>'index.php?g=Wap&m=Repair&a=wxEdit&token='.$this->token.'&openid='.$this->openid));exit;
                        }
                    }
                }else{
                    if($temp[1] == 1){
                        if(!M('repair_screen')->data(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id'],'agent_id'=>$temp[0]))->add()){
                            echo $this->encode(array('status'=>1, 'info'=>'设置失败', 'url'=>'index.php?g=Wap&m=Repair&a=wxEdit&token='.$this->token.'&openid='.$this->openid));exit;
                        }
                    }
                }
            }
            echo $this->encode(array('status'=>100, 'info'=>'设置成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxEdit&token='.$this->token.'&openid='.$this->openid));

        }else{

            $rapairOrder = M('repair_order')->field('distinct(agent_id),agent_name')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'],'agent_id'=>array('neq',0)))->select();
            foreach($rapairOrder as $k=>$v){
                $staff = M('repair_staff')->where(array('wxuser_id'=>$this->userDatas['id'],'agent_id'=>$v['agent_id']))->find();
                $rapairOrder[$k]['staffname'] = $staff['staff_name'];
                $find = array();
                $find = M('repair_screen')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id'],'agent_id'=>$v['agent_id']))->find();
                if($find){
                    $rapairOrder[$k]['is_screen'] = 1;
                }else{
                    $rapairOrder[$k]['is_screen'] = 0;
                }
            }
            $this->assign('rapairOrder',$rapairOrder);
            $this->display();
        }
	}


	/*用户基本资料信息修改*/
	public function wxEdit() {
		$repair_user = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		if (empty($repair_user)) {
			$type = 0;
		} else {
			$type = 1;
		}
		$style = trim($_GET['type'])?trim($_GET['type']):0;
		if (isset($_GET['id'])) {
			$this->assign('id', trim($_GET['id']));
		}
		if (0 == $type) {
			/*添加*/
			if (IS_POST) {

				$addDatas = array(
						'wxuser_id'=>$this->userDatas['id'],
						'wxusers_id'=>$this->wxUserDatas['id'],
						'username'=>trim($_POST['yname']),
						'user_tel'=>trim($_POST['phoneNo']),
						'weixin_no'=>trim($_POST['MicroMsgNo']),
						'area'=>$_POST['area'],
						'address'=>trim($_POST['address'])
				);
				$addBack = M('repair_user')->add($addDatas);
				if ($addBack) {
					if (1 == $style) {
						echo $this->encode(array('status'=>100, 'info'=>'添加基本资料成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxWarn&token='.$this->token.'&openid='.$this->openid));
					} elseif (2 == $style){
						echo $this->encode(array('status'=>100, 'info'=>'添加基本资料成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxWarn&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>100, 'info'=>'添加基本资料成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxWarn&token='.$this->token.'&openid='.$this->openid));
					}
				} else {
					echo $this->encode(array('status'=>1, 'info'=>'系统繁忙', 'url'=>'index.php?g=Wap&m=Repair&a=wxEdit&token='.$this->token.'&openid='.$this->openid));
				}
			} else {
				/*查找省*/
				$province = M('area')->where(array('level'=>1))->select();
				$this->assign('province', $province);

				$this->assign('style', $style);
				$this->assign('type', $type);
				$this->display();
			}
		} elseif (1 == $type) {
			/*编辑*/

			if (IS_POST) {

				$updateData = array(
						'username'=>trim($_POST['yname']),
						'user_tel'=>trim($_POST['phoneNo']),
                        'weixin_no'=>trim($_POST['MicroMsgNo']),
						'area'=>$_POST['area'],
						'address'=>trim($_POST['address'])
				);
				$upBack = M('repair_user')->where(array('id'=>$repair_user['id']))->save($updateData);
				if (1 == $style) {
					echo $this->encode(array('status'=>100, 'info'=>'基本资料修改成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxWarn&token='.$this->token.'&openid='.$this->openid));
				} elseif (2 == $style){
						echo $this->encode(array('status'=>100, 'info'=>'添加基本资料成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxWarn&token='.$this->token.'&openid='.$this->openid.'&id='.$_GET['id']));
				} else {
					echo $this->encode(array('status'=>100, 'info'=>'基本资料修改成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxWarn&token='.$this->token.'&openid='.$this->openid));
				}

			} else {
				/*查找省*/
				$province = M('area')->where(array('level'=>1))->select();
				$this->assign('province', $province);

				$this->assign('repair_user', $repair_user);
				$this->assign('style', $style);
				$this->assign('type', $type);
				$this->display();
			}
		}
	}

	/*微信用户的二维码*/
	public function wxCode() {
		/*微信用户的二维码，以1001开头*/
		$find = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		$parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 130'.$find['id'].'}}}';
		/*获取access_token*/
		$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
		if($api){
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
			$json = json_decode(file_get_contents($url_get));
			$access_token = $json->access_token;
			$imgSource = $this->creatTicket($access_token, $parament);
		}
		$this->assign('imgUrl', $imgSource['header']['url']);
		$this->display();
	}

	/*微信用户的积分*/
	public function wxScore() {
		if (isset($_GET['score'])) {
			/*根据微信传递过来的分值*/
			$score = trim($_GET['score']);
			$userInfo = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($userInfo)) {
				$score = $score + $userInfo['score'];
				$updateBack = M('repair_user')->where(array('id'=>$userInfo['id']))->save(array('score'=>$score));
				if ($updateBack) {
					$insertScore = array(
						'wxuser_id'=>$this->userDatas['id'],
						'wxusers_id'=>$this->wxUserDatas['id'],
						'get_score'=>trim($_GET['score']),
						'gettime'=>time(),
						/*后面补全*/
					);
					$insertBack = M('repair_score')->add($insertScore);
					if ($insertBack) {
						/*添加积分成功*/
					}
				}
			}

		} else {
			$scoreInfo = M('repair_score')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->select();
			if (!empty($scoreInfo)) {
				$this->assign('isNull', 0);
				$this->assign('scoreInfo', $scoreInfo);
			} else {
				$this->assign('isNull', 1);
			}
			$repair_user = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
			if (!empty($repair_user)) {
				$this->assign('score', $repair_user['score']);
			}

			$this->display();
		}

	}



    /*
     * 服务商积分
     */

    public function agentScore() {
        if (isset($_GET['score'])) {
            /*根据微信传递过来的分值*/
            $score = trim($_GET['score']);
            $userInfo = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
            if (!empty($userInfo)) {
                $score = $score + $userInfo['score'];
                $updateBack = M('repair_user')->where(array('id'=>$userInfo['id']))->save(array('score'=>$score));
                if ($updateBack) {
                    $insertScore = array(
                        'wxuser_id'=>$this->userDatas['id'],
                        'wxusers_id'=>$this->wxUserDatas['id'],
                        'get_score'=>trim($_GET['score']),
                        'gettime'=>time(),
                        /*后面补全*/
                    );
                    $insertBack = M('repair_score')->add($insertScore);
                    if ($insertBack) {
                        /*添加积分成功*/
                    }
                }
            }

        } else {
            $score = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
            $this->assign('score',$score);


            if($_POST){
	    
                $scoref = $_POST['integral'];
                $updateBacks = M('repair_agent_access')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$score['id']))->find();
                $integraler = $updateBacks['order_max'];
                $integral = intval($scoref/100)+$integraler;
                $info = intval($scoref%100);
                //print_r($integral);exit;
                $updateBack = M('repair_agent_access')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$score['id']))->save(array('order_max'=>$integral));
                //print_r($updateBack);exit;
                $integrals = $score['score'] - $scoref;
                if($updateBack){
                    $scores = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->save(array('score'=>$integrals));
                    if($scores){
                        $changedata['agent_no'] = $score['agent_no'];
                        $changedata['agent_id'] = $score['id'];
                        $changedata['wxuser_id'] = $score['wxuser_id'];
                        $changedata['wxusers_id'] = $score['wxusers_id'];
                        $changedata['name'] = $score['head_name'];
                        $changedata['phone'] = $score['telephone'];
                        $changedata['name'] = $score['head_name'];
                        $changedata['order_max'] = $integral;
                        $changedata['backlog_max'] = 0;
                        $changedata['day_max'] = 0;
                        $changedata['comp_day'] = 0;
                        $changedata['deposit'] = 0;
                        $changedata['type'] = '管理员';
                        $changedata['update_time'] = time();
                        $changedata['token'] = $this->token;
                        M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->save(array('score'=>$info));
                        if(M('Repair_access_change')->data($changedata)->add()){
                            $this->ajaxReturn(array('status'=>1,'info'=>'积分兑换成功','url'=>'index.php?g=Wap&m=Repair&a=agentScore&token='.$this->token.'&openid='.$this->openid));
                        }else{
                            $this->ajaxReturn(array('status'=>5,'info'=>'积分兑换失败','url'=>'index.php?g=Wap&m=Repair&a=agentScore&token='.$this->token.'&openid='.$this->openid));
                        }
                    }else{
                        $this->ajaxReturn(array('status'=>6,'info'=>'积分兑换失败','url'=>'index.php?g=Wap&m=Repair&a=agentScore&token='.$this->token.'&openid='.$this->openid));
                    }
                }else{
                    $this->ajaxReturn(array('status'=>2,'info'=>'积分兑换失败','url'=>'index.php?g=Wap&m=Repair&a=agentScore&token='.$this->token.'&openid='.$this->openid));
                }
            }

            $this->display();
        }

    }

	/*微信用户的优惠券*/
	public function wxCoupon() {

		if (isset($_GET['coupon'])) {
			/*获取优惠券*/
			$couponId = trim($_GET['coupon']);
			/*查找优惠券信息*/

			$insertCoupon = array(
					'wxuser_id'=>$this->userDatas['id'],
					'wxusers_id'=>$this->wxUserDatas['id'],

					/*后面补全*/
			);
			$insertBack = M('repair_coupon')->add($insertScore);
			if ($insertBack) {
				/*添加积分成功*/
			}
		} else {
			$type = trim($_GET['type'])?trim($_GET['type']):0;
			$couponInfo = M('usercenter_user_salecard')->where(array('token'=>$this->token, 'openid'=>$this->openid, 'status'=>$type))->select();

			$this->assign('couponInfo', $couponInfo);


			//$couponInfo = M('repair_coupon')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'status'=>$type))->select();
			//$this->assign('type', $type);
			if (!empty($couponInfo)) {
				$this->assign('isNull', 0);
				$this->assign('couponInfo', $couponInfo);
			} else {
				$this->assign('isNull', 1);
			}
			$this->display();
		}

	}

	/*微信用户的订单*/
	public function wxOrder() {
	/*查找所有的订单*/
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			/**/
			if (IS_POST) {
				/*取消订单*/
				$orderId = trim($_POST['id']);
				$orderInfo = M('repair_order')->where(array('wxusers_id'=>$this->wxUserDatas['id'], 'wxuser_id'=>$this->userDatas['id'], 'id'=>$orderId))->find();
				if (!empty($orderInfo)) {
                    if($orderInfo['score']) {
                        M('Repair_user')->where(array('wxuser_id' => $this->userDatas['id'], 'wxusers_id' => $this->wxUserDatas['id']))->setInc('score', $orderInfo['score']);
                    }
                    $cancelBack = M('repair_order')->where(array('id'=>$orderInfo['id']))->save(array('user_change'=>1));
					if ($cancelBack) {
						echo $this->encode(array('status'=>100, 'info'=>'您的订单已取消', 'url'=>'index.php?g=Wap&m=Repair&a=wxOrder&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统繁忙'));
					}
				}

			} else {

				/*待确定订单*/
					$orderInfo = M('repair_order')->where(array('wxusers_id'=>$this->wxUserDatas['id'], 'wxuser_id'=>$this->userDatas['id'], 'status'=>1, 'user_change'=>0))->select();
					$this->assign('type', 0);
					if (!empty($orderInfo)) {
						$this->assign('isNull', 0);
						$this->assign('orderInfo', $orderInfo);
					} else {
						$this->assign('isNull', 1);
					}
					$this->display();
			}
		} elseif (1 == $type) {

			if (IS_POST) {
				/*完成订单*/
				$orderId = trim($_POST['id']);
				$orderInfo = M('repair_order')->where(array('wxusers_id'=>$this->wxUserDatas['id'], 'wxuser_id'=>$this->userDatas['id'], 'id'=>$orderId))->find();
				if (!empty($orderInfo)) {
					if ($orderInfo['status'] != 4) {
						$cancelBack = M('repair_order')->where(array('id'=>$orderInfo['id']))->save(array('status'=>4));
						if ($cancelBack) {
							/*用户点击 订单完成  发送信息*/
							$notichcontent = '我的'.$orderInfo['repair_ele'].'维修已完成，你将在未来“保修期”内负责保修。';
							$agentWxId = M('repair_agent')->where(array('id'=>$orderInfo['agent_id']))->field('wxusers_id')->find();
							$openid = M('wxusers')->where(array('id'=>$agentWxId))->field('openid')->find();
							$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);

							$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
							$data = $this->api_notice_increment($url,http_build_query($postdata));

							echo $this->encode(array('status'=>100, 'info'=>'订单完成', 'url'=>'index.php?g=Wap&m=Repair&a=wxCenter&token='.$this->token.'&openid='.$this->openid));
						} else {
							echo $this->encode(array('status'=>1, 'info'=>'系统繁忙'));
						}
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'该订单已完成'));
					}

				}
			} else {

				/*服务中订单*/
				$condition['wxusers_id'] = $this->wxUserDatas['id'];
				$condition['wxuser_id'] = $this->userDatas['id'];
				$condition['status'] = array(array('eq',2),array('eq',3), 'or');
				$orderInfo = M('repair_order')->where($condition)->select();
				$this->assign('type', $type);
				if (!empty($orderInfo)) {
					$this->assign('isNull', 0);
					$this->assign('orderInfo', $orderInfo);
				} else {
					$this->assign('isNull', 1);
				}
				$this->display();
			}

		} elseif (2 == $type) {
			if (IS_POST) {
				/*催单*/
				$orderId = trim($_POST['id']);
				$orderInfo = M('repair_order')->where(array('wxusers_id'=>$this->wxUserDatas['id'], 'wxuser_id'=>$this->userDatas['id'], 'id'=>$orderId))->find();
                $staff = M('repair_staff')->where(array('agent_id'=>$orderInfo['agent_id']))->find();
                $orderInfo['staff_name'] = $staff['staff_name'];
                if (!empty($orderInfo)) {
					if ($orderInfo['status'] != 4) {
							/*发送信息*/
                        $notichcontent ="尊敬的".$orderInfo['staff_name']."您好！\r\n您接收的".$orderInfo['repair_ele'].rtrim($orderInfo['service_cont'],',')."的服务订单,用户催您快点\r\n用户姓名:".$orderInfo['order_name']."\r\n联系电话:".$orderInfo['order_tel']."\r\n用户详细地址:".$orderInfo['order_address']."\r\n请尽快处理.";
						//$notichcontent = '您接收的“'.$orderInfo['order_address'].'”“'.$orderInfo['order_time'].'”（手机：“'.$orderInfo['order_tel'].'”）的服务订单（“'.$orderInfo['repair_ele'].'+'.$orderInfo['service_cont'].'”）催您快点！';
						$agentWxId = M('repair_agent')->where(array('id'=>$orderInfo['agent_id']))->field('wxusers_id')->find();
                        $openid = M('wxusers')->where(array('id'=>$agentWxId['wxusers_id']))->field('openid')->find();

						$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
						$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
						$data = $this->api_notice_increment($url,http_build_query($postdata));

						echo $this->encode(array('status'=>100, 'info'=>'订单已经催了一遍', 'url'=>'index.php?g=Wap&m=Repair&a=wxOrder&type=1&token='.$this->token.'&openid='.$this->openid));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'系统繁忙'));
					}
				}
			} else {

				/*保修期订单*/
				$condition['wxusers_id'] = $this->wxUserDatas['id'];
				$condition['wxuser_id'] = $this->userDatas['id'];
				$orderInfo = M('repair_order')->where($condition)->select();
                $ordernewinfo = array();
                foreach($orderInfo as $k=>$v){
                    $endtime = intval($v['finish_time']) + ($v['period'])*24*3600;
                    if(time() < $endtime){
                        $ordernewinfo[]=$v;
                    }
                }
				$this->assign('type', $type);
				if (!empty($ordernewinfo)) {
					$this->assign('isNull', 0);
					$this->assign('orderInfo', $ordernewinfo);
				} else {
					$this->assign('isNull', 1);
				}
				$this->display();
			}
		} elseif (3 == $type) {
            /*用户我要返修 */
			if (IS_POST) {

                $orderId = trim($_POST['id']);
                $orderInfo = M('repair_order')->where(array('wxusers_id'=>$this->wxUserDatas['id'], 'wxuser_id'=>$this->userDatas['id'], 'id'=>$orderId))->find();
                if (!empty($orderInfo)) {

                    M('repair_order')->where(array('wxusers_id'=>$this->wxUserDatas['id'], 'wxuser_id'=>$this->userDatas['id'], 'id'=>$orderId))->save(array('status'=>6));

                    $staff = M('repair_staff')->where(array('agent_id'=>$orderInfo['agent_id']))->find();
                    $findOrder['staff_name'] = $staff['staff_name'];

                    $agentUser = M('Repair_agent')->where(array('id'=>$orderInfo['agent_id']))->find();

                    /*技术返修发送消息*/
                    $openid = M('wxusers')->where(array('id'=>$agentUser['wxusers_id']))->field('openid')->find();
                    $openid = $openid['openid'];

                    $templateData['touser'] = $openid;
                    $templateData['template_id'] = '9nDtXd5Q-QQNqYvPvXg8KuNKW9VdOqj3K7St1JBaABU';
                    $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=myOrder&type=2&token=".$this->token."&openid=".$openid;
                    $templateData['topcolor']="#FF0000";
                    $templateData['data']['first'] = array('value'=>'','color'=>"#173177");
                    $templateData['data']['keyword1'] = array('value'=>$orderInfo['order_nid'],'color'=>"#173177");
                    $templateData['data']['keyword2'] = array('value'=>$orderInfo['order_name'],'color'=>"#173177");
                    $templateData['data']['keyword3'] = array('value'=>$orderInfo['staff_tel'],'color'=>"#173177");
                    $templateData['data']['keyword4'] = array('value'=>$orderInfo['order_address'],'color'=>"#173177");
                    $templateData['data']['remark'] = array('value'=>"尊敬的".$findOrder['staff_name']."您好！用户的订单需要返修，请收到信息后在30分钟内联系用户，尽快处理返修事宜。你可以通过【技师专区】-【我的主页】-【订单】-【保修期】查看需要保修的订单。",'color'=>"#173177");
                    $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
                    $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
                    $data = $this->api_notice_increment($url,http_build_query($postdata));

                    if(!$data){
                        $this->api_notice_increment($url,http_build_query($postdata));
                    }


                    /*发送信息用户*/
                    /*
                     * 获取技师名字
                     */
                    $staff = M('repair_staff')->where(array('agent_id'=>$orderInfo['agent_id']))->find();
                    $orderInfo['staff_name'] = $staff['staff_name'];

                    $useropenid = M('wxusers')->where(array('id'=>$orderInfo['wxusers_id']))->field('openid')->find();

                    $notichcontent ="尊敬的".$orderInfo['order_name']."您好！\r\n您的返修信息已发给技师，请保持通话畅通状态。\r\n技师电话:".$orderInfo['staff_name']."\r\n技师电话:".$orderInfo['staff_tel'];
                    //$notichcontent = '您接收的“'.$orderInfo['order_address'].'”“'.$orderInfo['order_time'].'”（手机：“'.$orderInfo['order_tel'].'”）的服务订单（“'.$orderInfo['repair_ele'].'+'.$orderInfo['service_cont'].'”）催您快点！';
                    $agentWxId = M('repair_agent')->where(array('id'=>$orderInfo['agent_id']))->field('wxusers_id')->find();
                    $openid = M('wxusers')->where(array('id'=>$agentWxId['wxusers_id']))->field('openid')->find();

                    $postdata = array('openid'=>$useropenid['openid'],'token'=>$this->token,'content'=>$notichcontent);
                    $url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
                    $data = $this->api_notice_increment($url,http_build_query($postdata));


                    echo $this->encode(array('status'=>100, 'info'=>'成功发送返修信息', 'url'=>'index.php?g=Wap&m=Repair&a=wxOrder&type=2&token='.$this->token.'&openid='.$this->openid));

                }


			} else {

				/*已完成订单*/
				$condition['wxusers_id'] = $this->wxUserDatas['id'];
				$condition['wxuser_id'] = $this->userDatas['id'];
				$condition['finish_time'] = array('neq',0);
				$orderInfo = M('repair_order')->where($condition)->select();
                $ordernewinfo = array();
                foreach($orderInfo as $k=>$v){
                    $endtime = intval($v['finish_time']) + ($v['period'])*24*3600;
                    if(time() >= $endtime){
                        $ordernewinfo[]=$v;
                    }
                }
				$this->assign('type', $type);
				if (!empty($orderInfo)) {
					$this->assign('isNull', 0);
					$this->assign('orderInfo', $ordernewinfo);
				} else {
					$this->assign('isNull', 1);
				}
				$this->display();
			}

		}
	}

	public function changeTime() {
		if (IS_POST) {
			if (isset($_POST['id'])) {
				$orderId = $_POST['id'];
				$findOrder = M('repair_order')->where(array('id'=>$orderId))->find();
				if (!empty($findOrder)) {
					$saveOrder = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('appoint_day'=>$_POST['cdaytime'],'appoint_time'=>$_POST['ctime']));
					if ($saveOrder) {
						echo $this->encode(array('status'=>100, 'info'=>'更改时间成功','url'=>'index.php?g=Wap&m=Repair&a=wxOrder&token='.$this->token.'&openid='.$this->openid));
					}
				}
			}
		} else {
			if (isset($_GET['id'])) {
				$orderId = trim($_GET['id']);

				$findOrder = M('repair_order')->where(array('id'=>$orderId))->find();
				if (!empty($findOrder)) {
					$this->assign('orderInfo', $findOrder);
				}
			}
			$this->display();
		}
	}

	/*我要返修*/
	public function goback() {
		if (IS_POST) {
			/*我要返修*/
			$jsonData = $_POST['jsonstr'];
			$jsonData = htmlspecialchars_decode($jsonData);
			$jsonData = json_decode($jsonData);

			$orderId = $jsonData[0]->id;
			$orderInfo = M('repair_order')->where(array('wxusers_id'=>$this->wxUserDatas['id'], 'wxuser_id'=>$this->userDatas['id'], 'id'=>$orderId, 'period'=>array('neq',0)))->find();
			if (!empty($orderInfo)) {

				/*$updateData = array(
					'appoint_time'=>trim($jsonData[0]->AppointmentD).','.$jsonData[0]->AppointmentT,
					'order_time'=>$_SERVER['REQUEST_TIME'],
					'service_type'=>trim($jsonData[0]->wxtype),
					'repair_ele'=>trim($jsonData[0]->electric),
					'service_cont'=>trim($jsonData[0]->scontent),
					'operate'=>trim($jsonData[0]->airjob),
					'fault_info'=>trim($jsonData[0]->Fault),
					'is_warranty'=>1,
					'pay_type'=>trim($jsonData[0]->Payment),
					'coupon_cn'=>trim($jsonData[0]->coup),
					'order_name'=>trim($jsonData[0]->yname),
					'order_tel'=>trim($jsonData[0]->phoneNo),
					'order_air'=>$rapairUser['area'],
					'order_address'=>trim($jsonData[0]->address),
					'status'=>1,
					'is_read'=>0
				);*/

				/*您为“详细地址”于“预约时间”进行的“电器+服务内容”，客户需要返修，请在30分钟内联系客户处理（姓名+电话）。*/
				$notichcontent = '您为“'.$orderInfo['order_address'].'”于“'.$orderInfo['repair_ele'].$orderInfo['service_cont'].'”，客户需要返修，请在30分钟内联系客户处理（'.$orderInfo['order_name'].'+'.$orderInfo['order_tel'].'）。';

				$agentWxId = M('repair_agent')->where(array('id'=>$orderInfo['agent_id']))->field('wxusers_id')->find();
				$openid = M('wxusers')->where(array('id'=>$agentWxId))->field('openid')->find();;
				$postdata = array('openid'=>$openid['openid'],'token'=>$this->token,'content'=>$notichcontent);
				echo $this->encode(array('status'=>100, 'info'=>'家电返修提交成功','url'=>'index.php?g=Wap&m=Repair&a=wxCenter&token='.$this->token.'&openid='.$this->openid));
			} else {
				echo $this->encode(array('status'=>1, 'info'=>'提交失败','url'=>'index.php?g=Wap&m=Repair&a=wxCenter&token='.$this->token.'&openid='.$this->openid));
			}
		} else {
			if (isset($_GET['id'])) {
				$orderId = trim($_GET['id']);
				$findOrder = M('repair_order')->where(array('id'=>$orderId, 'period'=>array('neq',0)))->find();
				if (!empty($findOrder)) {
					$this->assign('orderInfo', $findOrder);
					$repair_user = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();

					if (!empty($repair_user)) {
						$this->assign('repair_user', $repair_user);
					}

					/*查找大类的数据*/
					$bigClass = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>0))->order('sort')->select();
					/*
					$data = array();
					foreach ($bigClass as $key=>$value) {
						$data[$key] = M('repair_class')->where(array('wxuser_id'=>$this->userDatas['id'], 'pid'=>$value['id']))->order('sort')->select();
					}

					$secondClass = array();
					foreach ($data as $ke=>$val) {
						foreach ($val as $k=>$v) {
							$secondClass[] = $v;
						}
					}   */
					$this->assign('bigClass', $bigClass);

					$couponInfo = M('usercenter_user_salecard')->where(array('token'=>$this->token, 'openid'=>$this->openid, 'status'=>0))->select();
					$this->assign('couponInfo', $couponInfo);
					$this->assign('orderId', $orderId);
				}
			}

			$this->display();
		}
	}

	/*更改服务商*/
	public function changeAgent() {
		if (IS_POST) {
			if (isset($_POST['id'])) {
				$orderId = $_POST['id'];
				$findOrder = M('repair_order')->where(array('id'=>$orderId, 'status'=>8))->find();
				if (!empty($findOrder)) {
					$saveOrder = M('repair_order')->where(array('id'=>$findOrder['id']))->save(array('change_app_time'=>$_POST['ctime'], 'change_app_day'=>$_POST['time']));
					if ($saveOrder) {
						echo $this->encode(array('status'=>100, 'info'=>'更改时间成功','url'=>'index.php?g=Wap&m=Repair&a=wxCenter&token='.$this->token.'&openid='.$this->openid));
					}
				} else {
					echo $this->encode(array('status'=>1, 'info'=>'不能更改服务商','url'=>'index.php?g=Wap&m=Repair&a=wxCenter&token='.$this->token.'&openid='.$this->openid));
				}
			}
		} else {
			if (isset($_GET['id'])) {
				$orderId = trim($_GET['id']);

				$findOrder = M('repair_order')->where(array('id'=>$orderId))->find();

				if (!empty($findOrder)) {

					$str = rtrim( trim($findOrder['service_cont']), ',' );
					$service_con = explode(',', $str);

					$condition['area'] = array(
							array('like', "%".$findOrder['order_air']."%"),
					);
					$condition['bigclass'] = array(
							array('like', "%".$findOrder['service_type']."%")
					);
					$condition['wxuser_id'] = $this->userDatas['id'];
					$condition['is_review'] = 2;

					$condition['smallclass'] = array(
							array('like', "%".$findOrder['repair_ele']."%")
					);

					$condition['sign_time'] = array(array('gt', time()-48*60*60), array('lt', time()));

					/*查找类*/
					$agentInfo = M('repair_agent')->where($condition)->order('id')->select();
					$agentDatas = array();
					if (!empty($agentInfo)) {
						foreach ($agentInfo as $key=>$value){
							$str1 = rtrim( trim($value['content']), ',' );
							$strcontent = explode(',', $str1);
							$result = array_diff($service_con, $strcontent);
							if (empty($result)) {
								/*还得去判断是否被屏蔽*/
								$find = M('repair_screen')->where(array('wxusers_id'=>$this->userDatas['id'], $value['id']))->find();
								if (empty($find)) {
									$agentDatas[] = $value;
								}
							}
						}
					}
					$this->assign('agentInfo', $agentDatas);
				}
			}
			$this->assign('id', $orderId);
			$this->display();
		}
	}

	/*评价*/
	public function appraise() {
		if (IS_POST) {
			$orderId = $_POST['id'];
			$findOrder = M('repair_order')->where(array('id'=>$orderId, 'is_appriase'=>0))->find();
			if (!empty($findOrder)) {
				$update = M('repair_order')->where(array('id'=>$orderId))->save(array('is_appriase'=>1));
				if ($update) {
					$insertData = array(
							'wxuser_id'=>$this->userDatas['id'],
							'wxusers_id'=>$this->wxUserDatas['id'],
							'order_id'=>$findOrder['order_nid'],
							'score'=>$_POST['score'],
							'appraise_info'=>$_POST['info'],
							'appraise_name'=>$findOrder['order_name']
					);
					$insertBack = M('appraise')->add($insertData);
                    if(intval($_POST['score']) < 3){
                        if(!M('repair_screen')->where(array('wxuser_id'=>$findOrder['wxuser_id'],'wxusers_id'=>$findOrder['wxusers_id'],'agent_id'=>$findOrder['agent_id']))->find()){
                            M('repair_screen')->add(array('wxuser_id'=>$findOrder['wxuser_id'],'wxusers_id'=>$findOrder['wxusers_id'],'agent_id'=>$findOrder['agent_id']));
                        }
                    }
					if ($insertBack) {
						echo $this->encode(array('status'=>100, 'info'=>'评价成功', 'url'=>'index.php?g=Wap&m=Repair&a=wxCenter&token='.$this->token.'&openid='.$this->openid));
					}
				}
			} else {
				echo $this->encode(array('status'=>1, 'info'=>'不存在这个订单'));
			}
		} else {
			$orderId = trim($_GET['id']);
			$findOrder = M('repair_order')->where(array('id'=>$orderId))->find();
			if (!empty($findOrder)) {
				$this->assign('orderInfo', $findOrder);
			}
			$this->display();
		}
	}

	public function wxCenter() {
		$newsData = M('repair_release')->where(array('wxuser_id'=>$this->userDatas['id'], 'aim_at'=>array('neq',1)))->order('sort')->limit(3)->select();

		if (count($newsData) == 3) {
			$this->assign('isMore', 1);
		}
		$this->assign('newsData', $newsData);
		$this->display();
	}


	/***************************************USER***********************************************/

	public function condition($agentInfo, $referee, $agentAcess, $findOrder) {
		/*先判断投诉量，然后改变天数*/
		if ($agentAcess['complaint'] != 0) {

			$complainInfo = M('repair_complain')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$agentInfo['id']))->order("complain_time desc")->select();
			$startdate = $complainInfo['complain_time'];
			$enddate = time();
			$day = round(($enddate-$startdate)/3600/24);
			$day = ($agentAcess['complaint']*$agentAcess['not_get_day']) - $day;
			if ($day <= 0) {
				$updateAccess = M('repair_agent_access')->where(array('id'=>$agentAcess['id']))->save(array('complaint'=>0));
			} else {
				$complainAccount = round($day/$agentAcess['not_get_day']);
				$updateAccess = M('repair_agent_access')->where(array('id'=>$agentAcess['id']))->save(array('complaint'=>$complainAccount));
			}
		} else {
			$day = 0;
		}


		if ($day <= 0) {
			/*判断积压的订单数*/
			$backlogInfo = M('repair_order')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$agentInfo['id'], 'status'=>array('in',array(2,3,5,6))))->select();

            if ($agentAcess['order_max'] < 2) {
                /*积压的订单数太多了不能接单*/
                $notichcontent = "接单失败，原因为您的余额不足。请及时充值。如有不清楚，请致电：025-58460223或通过服务号修业网络客服中心咨询。";

                $postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
                $url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
                $data = $this->api_notice_increment($url,http_build_query($postdata));
                echo $this->encode(array('status'=>1, 'info'=>'接单失败,您的余额不足', 'url'=>'index.php?g=Wap&m=Repair&a=index&token='.$this->token.'&openid='.$this->openid));
                exit();
            }


            $backlogCount = count($backlogInfo);
			if ($backlogCount >= $agentAcess['backlog_num']) {
				/*积压的订单数太多了不能接单*/
                $notichcontent = "接单失败，原因为您的积压订单数过多，请及时处理。如有不清楚，请致电：025-58460223或通过服务号修业网络客服中心咨询。";

				$postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
				$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
				$data = $this->api_notice_increment($url,http_build_query($postdata));
				echo $this->encode(array('status'=>1, 'info'=>'接单失败，原因为您的积压订单数过多', 'url'=>'index.php?g=Wap&m=Repair&a=index&token='.$this->token.'&openid='.$this->openid));
				exit();
			} else {
				/**/
				$condition = array(
						'wxuser_id'=>$this->userDatas['id'],
						'agent_id'=>$agentInfo['id'],
						'status'=>2
				);
				$condition['grab_time'] = array(array('gt',strtotime("today")),array('lt',strtotime("today")+24*3600));
				$acceptCount = M('repair_order')->where($condition)->select();
				$acceptCount = count($acceptCount);
				if ($acceptCount >= $agentAcess['day_order_max']) {
					/*接单书太多，不能接单*/
					$notichcontent = "接单失败，原因为您今日的接单总数已超过一日可接单数。如有不清楚，请致电：025-58460223或通过服务号修业网络客服中心咨询。";

					$postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
					$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
					$data = $this->api_notice_increment($url,http_build_query($postdata));
					echo $this->encode(array('status'=>1, 'info'=>'接单失败，原因为您今日的接单总数已超过一日可接单数', 'url'=>'index.php?g=Wap&m=Repair&a=index&token='.$this->token.'&openid='.$this->openid));
					exit();
				} else {
					$updateData = array(
							'agent_id'=>$agentInfo['id'],
							'agent_name'=>$agentInfo['store_name'],
							'status'=>2,
                            'staff_name'=>$agentInfo['head_name'],
                            'staff_tel'=>$agentInfo['telephone'],
							'grab_time'=>time()
					);

					$clickBack = M('repair_order')->where(array('id'=>$findOrder['id']))->save($updateData);


                    $agentAcess = M('repair_agent_access')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$agentInfo['id']))->setDec('order_max',2);

                    $changedata['agent_no'] = $agentInfo['agent_no'];
                    $changedata['agent_id'] = $agentInfo['id'];
                    $changedata['wxuser_id'] = $agentInfo['wxuser_id'];
                    $changedata['wxusers_id'] = $agentInfo['wxusers_id'];
                    $changedata['name'] = $agentInfo['head_name'];
                    $changedata['phone'] = $agentInfo['telephone'];
                    $changedata['name'] = $agentInfo['head_name'];
                    $changedata['order_max'] = -2;
                    $changedata['backlog_max'] = 0;
                    $changedata['day_max'] = 0;
                    $changedata['comp_day'] = 0;
                    $changedata['deposit'] = 0;
                    $changedata['type'] = '接单';
                    $changedata['update_time'] = time();
                    $changedata['token'] = $this->token;

                    M('Repair_access_change')->add($changedata);

					/**接单成功，向服务商发消息**/
                    $notichcontent="您已成功接单，请在30分钟内电话联系用户确认相关细节。\r\n订单信息如下\r\n用户姓名:".$findOrder['order_name']."\r\n用户电话:".$findOrder['order_tel']."\r\n用户地址:".$findOrder['order_address']."\r\n电器:".$findOrder['repair_ele']."\r\n服务内容:".$findOrder['service_cont']."\r\n故障描述:".$findOrder['fault_info']."\r\n预约时间:".$findOrder['appoint_day'].",".$findOrder['appoint_time']."\r\n订单编码:".$findOrder['order_nid']."\r\n订单发起时间:".date('Y年m月d日H时i分s秒');
					//$notichcontent = '“'.$findOrder['order_address'].'”用户（电话：“'.$findOrder['order_tel'].'”，您也可以在服务中订单查看客户信息）在'.date('Y年m月d日H时i分s秒').'发起一个“'.$findOrder['repair_ele'].$findOrder['service_cont'].'”，预约时间为“'.$findOrder['appoint_time'].'”，您已接单成功，请在30分钟内回电确认。';

					$postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
					$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
					$data = $this->api_notice_increment($url,http_build_query($postdata));

					/*给用户发送消息-模版消息*/
                    /*
                    $notichcontent = "尊敬的姓名".$findOrder['order_name']."，您刚发起的”".$findOrder['repair_ele'].$findOrder['service_cont']."”的服务订单已安排维修技师，技师信息如下\r\n姓名:".$agentInfo['head_name']."\r\n联系电话:".$agentInfo['telephone']."\r\n驻店地址:".$agentInfo['agent_address']."\r\n订单编码:".$findOrder['order_nid']."\r\n技师将在30分钟内电话联系您，请保持通话畅通状态";
					//$notichcontent = '您刚刚发起的“'.$findOrder['repair_ele'].$findOrder['service_cont'].'”服务订单，“'.$agentInfo['store_name'].'”已接单，维修工程师将在30分钟内联系你，请保持通话畅通状态。';

					$openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->find();
					$openid = $openid['openid'];
					$postdata = array('openid'=>$openid,'token'=>$this->token,'content'=>$notichcontent);
					$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
					$data = $this->api_notice_increment($url,http_build_query($postdata));
                    */
                    $findOrder =  M('repair_order')->where(array('id'=>$findOrder['id']))->find();

                    $staff = M('repair_staff')->where(array('agent_id'=>$findOrder['agent_id']))->find();
                    $findOrder['staff_name'] = $staff['staff_name'];

                    $openid = M('wxusers')->where(array('id'=>$findOrder['wxusers_id']))->field('openid')->find();
                    $openid = $openid['openid'];

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
					/*
						保内单发送手机短信通知
						
				    */
					if($findOrder['type'] == 2){	

                      $this->sendSmSMsg($findOrder); 
                    }
						
					echo $this->encode(array('status'=>100, 'info'=>'抢单成功', 'url'=>'index.php?g=Wap&m=Repair&a=myOrder&token='.$this->token.'&openid='.$this->openid.'&type=1'));
		
				}
			}
		} else {
			/*投诉的太多了*/
			$notichcontent = '“'.$findOrder['order_address'].'”用户在'.date('Y年m月d日H时i分s秒').'发起一个“'.$findOrder['repair_ele'].$findOrder['service_cont'].'”，已被其他服务商抢单，请下次快点哦。';
				
			$postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
			$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
			$data = $this->api_notice_increment($url,http_build_query($postdata));
			echo $this->encode(array('status'=>1, 'info'=>'订单已被其他服务商抢掉', 'url'=>'index.php?g=Wap&m=Repair&a=index&token='.$this->token.'&openid='.$this->openid));
		}
		
		
	}

	public function  sendSmSMsg($findOrder){

		if($findOrder['type'] == 2){
			$sms_config_model=M('config_sms');
	        $check=$sms_config_model->where(array('token'=>$this->token))->find();

	        //发短信
	        if($check){
	            // http://api.sms.cn/mt/?uid=用户账号&pwd=MD5位32密码&mobile=号码&content=内容
	            //$contentdata = "尊敬的".$updateData['order_user']."，感谢您使用微信在线点餐。您的订单本店已收到，正在为您准备，稍后与您联系，谢谢！【".$this->tpl['name']."】";
	            //$contentdata = "您的验证码是".$updateData['order_user']."【".$this->tpl['name']."】";
	            //$contentdata = "【".$this->tpl['name']."】"."尊敬的".$updateData['order_user']."，感谢您使用微信在线点餐。您的订单本店已收到，正在为您准备，稍后与您联系，谢谢！";
	            $contentdata = "【".$this->tpl['name']."】"."尊敬的".$findOrder['order_name']."，您提交的家电维修订单已安排1号服务维修技师上门服务。\n技师姓名:".$findOrder['staff_name']."，电话为".$findOrder['staff_tel']."，请保持通话畅通。\n微信搜索并关注“1号服务”，可实时查看订单进度，并可进行催单、1键返修、服务评价等。如有疑问可咨询400-9696-011。";
	            $data['token'] = $this->token;
	            $data['content'] = $contentdata;
	            $data['type'] = 3;
	            $data['phone'] = $findOrder['order_tel'];
	            $data['add_time'] = time();
	            $data['is_ok'] = 0;


	            $url = 'http://yunpian.com/v1/sms/send.json';
	            $apidata['text'] = urlencode("$contentdata");
	            $apidata ="apikey=".$check['apikey']."&text=".$apidata['text']."&mobile=".$findOrder['order_tel'];
	            $returndata = $this->api_notice_increment($url,$apidata);
	            $returndata = json_decode($returndata);
	            if($returndata && $returndata->code == 0){
	                M('Sms_send_list')->add($data);
	            }
	        }
		}	




	}
	
	
	/*抢单*/
	public function grab() {
		
		if (IS_POST) {
			$orderId = trim($_POST['id']);
			$clickTime = $_SERVER['REQUEST_TIME'];		
			
			/*判断用户是为推荐人*/
			$findOrder = M('repair_order')->where(array('id'=>$orderId, 'wxuser_id'=>$this->userDatas['id']))->find();

			if ($findOrder['status'] == 1) {
				
				$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
				$referee = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$findOrder['wxusers_id']))->find();
				$agentAcess = M('repair_agent_access')->where(array('wxuser_id'=>$this->userDatas['id'], 'agent_id'=>$agentInfo['id']))->find();
				
//				/*if (($clickTime -$findOrder['order_time']) <= 30) {
//					if (($agentInfo['is_best'] == 1) && ($referee)) {
//						/*接订单，修改订单资料*/
//						$this->condition($agentInfo, $referee, $agentAcess, $findOrder);
//						exit();
//					} elseif (($agentInfo['is_best'] == 1) && (!$referee)) {
//						sleep(15);
//						$this->condition($agentInfo, $referee, $agentAcess, $findOrder);
//						exit();
//					} elseif (($agentInfo['is_best'] == 0) && (!$referee)) {
//						sleep(30);
//						$this->condition($agentInfo, $referee, $agentAcess, $findOrder);
//						exit();
//					}
//				} else {
					/*这里时间超过30s*/
					$this->condition($agentInfo, $referee, $agentAcess, $findOrder);
				//}
					
			} else {
				/*订单已经被抢*/
				$notichcontent = '“'.$findOrder['order_address'].'”用户在'.date('Y年m月d日H时i分s秒').'发起一个“'.$findOrder['repair_ele'].$findOrder['service_cont'].'”，已被其他服务商抢单，请下次快点哦。';
					
				$postdata = array('openid'=>$this->openid,'token'=>$this->token,'content'=>$notichcontent);
				$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
				$data = $this->api_notice_increment($url,http_build_query($postdata));
				echo $this->encode(array('status'=>1, 'info'=>'订单已被抢掉', 'url'=>'index.php?g=Wap&m=Repair&a=index&token='.$this->token.'&openid='.$this->openid));
			}			
		} else {
			
			/*查找优先权*/
			if (isset($_GET['id'])) {
				$orderId = trim($_GET['id']);
				$findOrder = M('repair_order')->where(array('id'=>$orderId, 'wxuser_id'=>$this->userDatas['id']))->find();
				if (!empty($findOrder)) {
					
					if ($findOrder['status'] == 1) {
						$this->assign('id', $orderId);
						$referee = M('repair_user')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$findOrder['wxusers_id']))->find();
						if ($referee['referee']) {
							$time = 0;
						} else {
							$time = 0;
						}
						$this->assign('time', $time);
						$this->display();
					} else {
						$this->assign('type', 5);
						$this->display('tpl/Wap/default/Repair_agent_exit.html');
					}
				}
			}
		}
		
	}
	
	
	/***************************************Business man*************************************/
	
	/*业务员入口基本展示*/
	public function sale() {
		
		if (IS_POST) {
			if (isset($_POST['username']) && isset($_POST['password'])) {
				$findStaff = M('repair_saler')->where(array('wxuser_id'=>$this->userDatas['id'],'saler_tel'=>trim($_POST['username'])))->find();
				if (!empty($findStaff)) {
					if (md5(trim($_POST['password']) === $findStaff['password'])) {
						$saveBack = M('repair_saler')->where(array('id'=>$findStaff['id']))->save(array('is_bind'=>1, 'wxusers_id'=>$this->wxUserDatas['id']));
						echo $this->encode(array('status'=>100, 'info'=>'登录成功', 'url'=>'index.php?g=Wap&m=Repair&a=sale&token='.$this->token.'&openid='.$this->openid));
					} else{
						echo $this->encode(array('status'=>1, 'info'=>'帐号或密码错误！'));
					}
				} else {
					echo $this->encode(array('status'=>1, 'info'=>'系统中中不存在该用户！'));
				}
			} else {
				echo $this->encode(array('status'=>1, 'info'=>'数据不齐全！'));
			}
			
		} else {
			$findStaff = M('repair_saler')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'is_bind'=>1))->find();
			
			if (!empty($findStaff)) {
				$newsData = M('repair_release')->where(array('wxuser_id'=>$this->userDatas['id'], 'aim_at'=>array('neq',1)))->order('sort')->select();
				$this->assign('newsData', $newsData);
				$this->display('tpl/Wap/default/Repair_saleCenter.html');
			} else {
				$this->display();
			}		
		}
		
	}
	
	/*业务员出推广的二维码生成*/
	public function saleCoupon() {
		if (IS_POST) {

		} else {
            $this->display();
		}
		
	}
	/*业务员注销*/
	public function saleLogout() {
		$find = M('repair_saler')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		if (!empty($find) && ($find['is_bind'] ==1)) {
			$updateBack = M('repair_saler')->where(array('id'=>$find['id']))->save(array('is_bind'=>0));
			if ($updateBack) {
				$this->assign('type', 4);
				$this->display('tpl/Wap/default/Repair_agent_exit.html');
			} else {
				$this->assign('type', 6);
				$this->display('tpl/Wap/default/Repair_agent_exit.html');
			}
			
		}
	}
	
	
	
	/*业务员推广的积分奖励二维码生成*/
	public function saleScore() {
		
		if (IS_POST) {

		} else {
			$this->display();
		}
		
	}
	
	/*业务经理二维码生成*/
	public function saleCode() {

		$findSaler = M('repair_saler')->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id']))->find();
		$parament = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$findSaler['id'].'}}}';		
		/*获取access_token*/
		$api=M('Diymen_set')->where(array('token'=>$this->token))->find();
		if($api){
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
			$json = json_decode(file_get_contents($url_get));
			$access_token = $json->access_token;
			$imgSource = $this->creatTicket($access_token, $parament);
		}
		$this->assign('imgUrl', $imgSource['header']['url']);
		$this->display();
		
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
	
	//上传图片uploads类
	public function uploadsT(){
		if(!empty($_FILES)){

			$size = $_FILES['mypic']['size'];
			if($size == 0){
			        $arr = array(
                                'code'=>'-1',
				'err'=>'你选择的图片太大，请重新选择，或对原来图片进行处理，以保证小于1M'
			        );

			        echo json_encode($arr);
				exit;
			}
			if($size > 1000000){
				$arr = array(
                                'code'=>'-1',
				'err'=>'你选择的图片太大，请重新选择，或对原来图片进行处理，以保证小于1M'
			        );

			        echo json_encode($arr);
				exit;
			}
		}
		import('ORG.Net.UploadFile');//导入上传类
		$upload = new UploadFile();// 实例化上传类
                $upload->maxSize  = 1000000 ;
		$upload->allowExts  = array('jpg' ,'png' ,'gif');// 设置附件上传类型
		$upload->savePath =  './upload/wapimg/';// 设置附件上传目录
		if(!file_exists($upload->savePath)){
			mkdir($upload->savePath);
		}
		if($upload->upload()){
		
			$info =  $upload->getUploadFileInfo();
			if($info['size'] > 1024000){
				$arr = array(
                                'code'=>-1,
				'err'=>'图片大小不能超过1M'
			        );
			        echo json_encode($arr);
				exit;
			}
			$imgpath=$info[0]['savepath'].$info[0]['savename'];
			$arr = array(
                    'code'=>0,
					'name'=>$info[0]['savename'],
					'pic'=>$imgpath
			);
			echo json_encode($arr);
		}else{
			$error = $this->error($upload->getErrorMsg());
                        echo json_encode(array('code'=>-1,'err'=>$error));

		}
	}
	
	/*订单详细信息展示*/
	public function wxorderInfo() {
		if (isset($_GET['id'])) {
			//$type = $_GET['type'];
			//$this->assign('type', $type);
			$findOrder = M('repair_order')->where(array('wxuser_id'=>$this->userDatas['id'], 'id'=>trim($_GET['id'])))->find();
            $staff = M('repair_staff')->where(array('agent_id'=>$findOrder['agent_id']))->find();
            $findOrder['staff_name'] = $staff['staff_name'];
            if (!empty($findOrder)) {
				/*$agentInfo = M('repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'],'id'=>$findOrder['agent_id']))->find();
				$this->assign('agentInfo', $agentInfo);   */
                $endtime = intval($findOrder['finish_time']) + ($findOrder['period'])*24*3600;
                if($endtime ==0 ){
                    $this->assign('orderInfo', $findOrder);
                }else{
                    if($findOrder['finish_time'] != null && time() < $endtime){
                        $findOrder['status'] = 10;
                        $this->assign('orderInfo', $findOrder);
                    }else if($findOrder['finish_time'] != null && time() >= $endtime){
                        $findOrder['status'] = 11;
                        $this->assign('orderInfo', $findOrder);
                    }

                }

			}
		}
		$this->display();
	}


    /*
     * 维权
     */
    public function wxWeiquan(){
        if(IS_POST){
            $weiquan_order = $_POST['weiquan_order'];
            $weiquan_content = $_POST['weiquan_content'];
            $findOrder = M('repair_order')->where(array('order_nid'=>$weiquan_order, 'wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id']))->find();
            if($findOrder){
                $data['order_id'] = $weiquan_order;
                $data['wxuser_id'] = $this->userDatas['id'];
                $data['wxusers_id'] = $this->wxUserDatas['id'];
                $data['saler_id'] = '';
                $data['agent_id'] = $findOrder['agent_id'];
                $data['staff_id'] = $findOrder['staff_id'];
                $data['complain_username'] = $findOrder['order_name'];
                $data['complain_info'] = $weiquan_content;
                $data['complain_phone'] = $findOrder['order_tel'];
                $data['complain_time'] = time();
                if(M('Repair_complain')->add($data)){

                    /*
                     * 发送投诉模版消息
                     */
                    $agent = M('Repair_agent')->where(array('id'=>$findOrder['agent_id']))->find();
                    $openid = M('wxusers')->where(array('id'=>$agent['wxusers_id']))->field('openid')->find();
                    $openid = $openid['openid'];
                    $templateData['touser'] = $openid;
                    $templateData['template_id'] = 'uNNdOhZPJDWnfSh6z1N6VTW7IzRHjCjSRVZepVA4SLs';
                    $templateData['url'] = C('site_url')."index.php?g=Wap&m=Repair&a=wxorderInfo&token=".$this->token."&openid=".$openid."&id=".$findOrder['id'];
                    $templateData['topcolor']="#FF0000";
                    $templateData['data']['first'] = array('value'=>"您被用户投诉，请尽快联系用户并在7日内处理完毕，处理完毕后，请务必让用户关闭投诉单。7日内未处理完毕的，您的被投诉次数将自动加1，累计到3次，您将被永久禁用。",'color'=>"#173177");
                    $templateData['data']['keyword1'] = array('value'=>$findOrder['order_name'],'color'=>"#173177");
                    $templateData['data']['keyword2'] = array('value'=>$findOrder['order_tel'],'color'=>"#173177");
                    $templateData['data']['keyword3'] = array('value'=>$weiquan_content,'color'=>"#173177");
                    $templateData['data']['keyword4'] = array('value'=>$weiquan_order,'color'=>"#173177");
                    $templateData['data']['remark'] = array('value'=>"",'color'=>"#173177");
                    $postdata = array('openid'=>$openid,'token'=>$this->token,'data'=>$this->encode($templateData));
                    $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTemplateMsg";
                    $data = $this->api_notice_increment($url,http_build_query($postdata));
                    if(!$data){
                        $this->api_notice_increment($url,http_build_query($postdata));
                    }


                    echo $this->encode(array('status'=>100, 'info'=>'投诉成功,等待处理','url'=>'index.php?g=Wap&m=Repair&a=wxWeiquanlist&token='.$this->token.'&openid='.$this->openid));
                }else{
                    echo $this->encode(array('status'=>1, 'info'=>'投诉失败'));
                }
            }else{
                echo $this->encode(array('status'=>1, 'info'=>'订单不存在哦'));
            }
        }else{
            $this->display();
        }
    }

    public function wxWeiquanlist(){
        if(IS_POST){
            $id = $_POST['id'];
            
            $complaintdata = M('Repair_complain')->where(array('id'=>$id))->find();
            if($complaintdata){
                if(M('Repair_complain')->where(array('id'=>$id))->save(array('status'=>3))){
                    echo $this->encode(array('status'=>100, 'info'=>'关闭成功'));
                }else{
                    echo $this->encode(array('status'=>1, 'info'=>'关闭失败'));
                }
            }else{
                echo $this->encode(array('status'=>1, 'info'=>'操作失败'));
            }
        }else{
        	$complaintdata = M('Repair_complain')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id'],'status'=>0))->select();
        	$this->assign('complaintdata',$complaintdata);
            $this->display();
        }
    }
    /*服务商的投诉记录*/
    public function complaints(){
        $agent = M('Repair_agent')->where(array('wxuser_id'=>$this->userDatas['id'],'wxusers_id'=>$this->wxUserDatas['id']))->find();
        if($agent) {
            $complaintdata = M('Repair_complain')->where(array('wxuser_id' => $this->userDatas['id'], 'agent_id' => $agent['id'],'status'=>array('in',array(0,1))))->select();
        }else{
            $complaintdata = null;
        }
        $this->assign('complaintdata',$complaintdata);
        $this->display();
    }
}