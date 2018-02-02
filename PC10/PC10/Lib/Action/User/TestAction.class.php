<?php
class TestAction extends UserAction {	
	
	public $token;
	public $userModel;
	public $userInfoData;
	public $testModel;
	public $testClassModel;
	public $subjectModel;
	
	public function _initialize() {
		
		parent::_initialize();
		if (!session('?token')) {
			session('token', $_GET['token']);
		}
		
		$this->token = session('token');
		$this->assign('token', $this->token);
		$this->userModel = M('wxuser');
		$this->userInfoData = $this->userModel->where(array('token'=>$this->token))->find();		
		$this->testModel = M('test_item');
		$this->testClassModel = M('test_class');
		$this->subjectModel = M('test_subject');
	} 
	
	/*测试基础页面展示*/
	public function index() {
			/*进行分页*/		
		import('ORG.Util.Page');			
		$count = $this->testModel->where(array('wxuser_id'=>$this->userInfoData['id']))->count();			
		$page = new Page($count, 10);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$list = $this->testModel->where(array('wxuser_id'=>$this->userInfoData['id']))->order('id')->page($nowPage.','.$page->listRows)->select();
		foreach ($list as $key =>$value) {
			$classInfo = $this->testClassModel->where(array('id'=>$value['test_type_id']))->find();
			$list[$key]['test_type_id'] = $classInfo['class_name'];
		}
		$show = $page->show();
		$this->assign('list', $list);		
		$this->assign('page', $show);
		$this->assign('style', 1);
		$this->display();		
	}
	
	/*添加题目*/
	public function manager() {
		
		$type = $_GET['type']?$_GET['type']:0;
		if (0 == $type) {
			if (IS_POST) {
				/*添加test项目数据*/
				$test_type_id = $this->testClassModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$_POST['classify']))->find();
				
				/*判断是否存在这个关键字*/
				$isset_keyword = $this->testModel->where ( array (
						'keyword' => $_POST ['keyword'],
						'wxuser_id' => $this->userInfoData['id']
				) )->field ( 'keyword' )->find ();
				
				if ($isset_keyword != NULL) {
					$this->error2 ( '关键词已经存在！' );
					exit ();
				}
				
				$insertData = array(
					'wxuser_id'=>$this->userInfoData['id'],
					'keyword'=>trim($_POST['keyword']),
					'test_name'=>trim($_POST['name']),
					'test_type_id'=>$test_type_id['id'],
					'image'=>trim($_POST['image']),
					'test_start_time'=>strtotime(trim($_POST['start'])),
					'test_end_time'=>strtotime(trim($_POST['end'])),
					'status'=>trim($_POST['display']),
					'test_introduce'=>$_POST['test_info'],
					'is_open'=>0,
					'score_is'=>$test_type_id['class_status']
				);
				$insertBack = $this->testModel->where()->add($insertData);
				if ($insertBack) {
					
					/*保存关键字*/
					$data1 ['pid'] = $insertBack;
					$data1 ['module'] = 'Test';
					$data1 ['token'] = $this->token;
					$data1 ['keyword'] = $_POST ['keyword'];
					$insert = M ('keyword')->add ($data1);
					if ($insert) {
						$this->success('添加成功','index.php?g=User&m=Test&a=index&token='.$this->token);
					}					
					
				} else {
					$this->error('添加失败', 'index.php?g=User&m=Test&a=manager&type=0&token='.$this->token);
				}								
			} else {
				/*查找分类*/	
				$classInfo = $this->testClassModel->where(array('wxuser_id'=>$this->userInfoData['id']))->select();
				if (empty($classInfo)) {
					$this->error2('你还未添加测试活动分类', 'index.php?g=User&m=Test&a=classIndex&token='.$this->token);
// 					$this->redirect("Test/index", array('token'=>$this->token),5,'你还未添加测试活动分类');
				}
				/*分配类的数据*/
				$classInfoN = $this->testClassModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'class_status'=>0))->select();
				$classInfoY = $this->testClassModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'class_status'=>1))->select();
				$this->assign('classDataN', $classInfoN);
				$this->assign('classDataY', $classInfoY);
				$this->assign('type', $type);
				$this->display();
			}
		} elseif (1 == $type) {
			if (IS_POST) {
				$test_type_id = $this->testClassModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$_POST['classify']))->find();
				$updateData = array(
						'wxuser_id'=>$this->userInfoData['id'],
						'keyword'=>trim($_POST['keyword']),
						'test_name'=>trim($_POST['name']),
						'test_type_id'=>$test_type_id['id'],
						'image'=>trim($_POST['image']),
						'test_start_time'=>strtotime(trim($_POST['start'])),
						'test_end_time'=>strtotime(trim($_POST['end'])),
						'status'=>trim($_POST['display']),
						'test_introduce'=>$_POST['test_info'],
						'score_is'=>$test_type_id['class_status']
				);
				
				$find = $this->testModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$_GET['id']))->find();
				if (!empty($find)) {
					$insertBack = $this->testModel->where(array('id'=>$find['id']))->save($updateData);
					
					if ($insertBack !== false) {
						/*保存关键字*/						
						$data1 ['pid'] = $find['id'];
						$data1 ['module'] = 'Test';
						$data1 ['token'] = $this->token;
						$da ['keyword'] = trim($_POST ['keyword']);
						M ('keyword')->where ( $data1 )->save ( $da );
						
						$this->success('编辑成功','index.php?g=User&m=Test&a=index&token='.$this->token);
					} else {
						$this->error('编辑失败', 'index.php?g=User&m=Test&a=manager&type=1&token='.$this->token);
					}
				} else {
					$this->error('不存在编辑数据，编辑失败', 'index.php?g=User&m=Test&a=manager&type=1&token='.$this->token);
				}
				
			} else {
				
				$testInfo = $this->testModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>trim($_GET['id'])))->find();
				$classInfo = $this->testClassModel->where(array('wxuser_id'=>$this->userInfoData['id']))->select();
				if (!empty($testInfo)) {
					
					$classInfoN = $this->testClassModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'class_status'=>0))->select();
					$classInfoY = $this->testClassModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'class_status'=>1))->select();
					$this->assign('classDataN', $classInfoN);
					$this->assign('classDataY', $classInfoY);
					
					$this->assign('data', $testInfo);
					$this->assign('status', $testInfo['status']);
					$this->assign('type', $type);
					$this->assign('id', trim($_GET['id']));
				}
				$this->display();
			}
		}
		
	}
	
	/*查看建议*/
	public function advice() {
		if (isset($_GET['id'])) {
			$find = $this->testModel->where(array('id'=>trim($_GET['id']), 'wxuser_id'=>$this->userInfoData['id']))->find();
			if (!empty($find)) {
				$suggestModel = M('test_suggest');
				import('ORG.Util.Page');
				$count = $suggestModel->where(array('item_id'=>$find['id']))->count();
				$page = new Page($count, 10);
				$nowPage = isset($_GET['p'])?$_GET['p']:1;
				$list = $suggestModel->where(array('item_id'=>$find['id']))->order('id')->page($nowPage.','.$page->listRows)->select();
				$show = $page->show();
				$this->assign('list', $list);
				$this->assign('page', $show);
				$this->assign('style', 1);
				$this->assign('id',intval($_GET['id']));
				$this->display();
			}
		}
		
	}
	
	/*开启和结束测试活动*/
	public function start() {
		$type = trim($_GET['type']);
		if (1 == $type) {
			if (isset($_GET['id'])) {
				$find = $this->testModel->where(array('id'=>trim($_GET['id']), 'wxuser_id'=>$this->userInfoData['id'], 'is_open'=>0))->find();
				if (!empty($find)) {
					$updateBack = $this->testModel->where(array('id'=>$find['id']))->save(array('is_open'=>1));
					if ($updateBack !== false) {
						$this->success('开启成功','index.php?g=User&m=Test&a=index&token='.$this->token);
					} else {
						$this->error('开启失败', 'index.php?g=User&m=Test&a=index&token='.$this->token);
					}
				}
			}
		} elseif (2 == $type) {
			if (isset($_GET['id'])) {
				$find = $this->testModel->where(array('id'=>trim($_GET['id']), 'wxuser_id'=>$this->userInfoData['id'], 'is_open'=>1))->find();
				if (!empty($find)) {
					$updateBack = $this->testModel->where(array('id'=>$find['id']))->save(array('is_open'=>0));
					if ($updateBack !== false) {
						$this->success('关闭成功','index.php?g=User&m=Test&a=index&token='.$this->token);
					} else {
						$this->error('关闭失败', 'index.php?g=User&m=Test&a=index&token='.$this->token);
					}
				}
			}
		}
		
		
	}
	
	/*展示分类*/
	public function classIndex() {
		
		import('ORG.Util.Page');
		$classModel = M('test_class');
		$count = $classModel->where(array('wxuser_id'=>$this->userInfoData['id']))->count();
		$page = new Page($count, 10);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$list = $classModel->where(array('wxuser_id'=>$this->userInfoData['id']))->order('id')->page($nowPage.','.$page->listRows)->select();
		$show = $page->show();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->assign('style', 0);
		$this->display();
	}
	
	/*添加和编辑测试题库分类*/
	public function manClass() {
		$type = $_GET['type']?$_GET['type']:0;
		if (0 == $type) {
			if (IS_POST) {
				/*添加test项目分类数据*/
				$classname = trim($_POST['name']);
				$classify = trim($_POST['classify']);
				$classModel = M('test_class');
				$find = $classModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'class_name'=>$classname, 'class_status'=>$classify))->find();
				if (!empty($find)) {
					$this->error('你已经添加了这个分类', 'index.php?g=User&m=Test&a=manClass&type=0&token='.$this->token);
				} else {
					$insert = $classModel->where()->add(array('wxuser_id'=>$this->userInfoData['id'], 'class_name'=>$classname, 'class_status'=>$classify));
					if ($insert == true) {
						$this->success('添加分类成功','index.php?g=User&m=Test&a=classIndex&token='.$this->token);
					} else {
						$this->error('添加分类失败', 'index.php?g=User&m=Test&a=manClass&type=0&token='.$this->token);
					}
				}	
			} else {
				$this->assign('type', 0);
				$this->display();
			}
		} elseif (1 == $type) {
			if (IS_POST) {
				$classname = trim($_POST['name']);
				$classify = trim($_POST['classify']);
				$classModel = M('test_class');
				$find = $classModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>trim($_GET['id'])))->find();
				if (!empty($find)) {
					$update = $classModel->where(array('id'=>$find['id']))->save(array('class_name'=>$classname, 'class_status'=>$classify));
					if ($update !== false) {
						$this->success('编辑分类成功','index.php?g=User&m=Test&a=classIndex&token='.$this->token);
					} else {
						$this->error('编辑分类失败', 'index.php?g=User&m=Test&a=manClass&type=1&token='.$this->token);
					}
				} else {
					$this->error('不存在这个类，编辑分类失败', 'index.php?g=User&m=Test&a=manClass&type=1&token='.$this->token);
				}
				
			} else {
				if (isset($_GET['id'])) {
					$classModel = M('test_class');
					$find = $classModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$_GET['id']))->find();
					$this->assign('data', $find);					
				}
               // p($find);
				$this->assign('id', trim($_GET['id']));
				$this->assign('type', 1);
				$this->display();
			}
		}
	}
	
	/*删除*/
	public function delClass() {
		
		$style = trim($_GET['style']);
	
		/*删除题库名称*/
		if (1 == $style) {
			if (IS_POST) {
				$testModel = M('test_item');
				$find = $testModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$_GET['id']))->find();
				if (!empty($find)) {
					$delBack = $testModel->where(array('id'=>$find['id']))->delete();
					if ($delBack) {
						$this->success('删除成功','index.php?g=User&m=Test&a=index&token='.$this->token);
					} else {
						$this->error('删除失败', 'index.php?g=User&m=Test&a=index&token='.$this->token);
					}
				} else {
					$this->error('不存在删除数据，删除失败', 'index.php?g=User&m=Test&a=index&token='.$this->token);
				}
			}
		} elseif (0 == $style) {
			/*删除题库分类*/
			if (IS_POST) {
				$classModel = M('test_class');
				$find = $classModel->where(array('wxuser_id'=>$this->userInfoData['id'], 'id'=>$_GET['id']))->find();
				if (!empty($find)) {
					$delBack = $classModel->where(array('id'=>$find['id']))->delete();
					if ($delBack) {
						$this->success('删除成功','index.php?g=User&m=Test&a=classIndex&token='.$this->token);
					} else {
						$this->error('删除失败', 'index.php?g=User&m=Test&a=classIndex&token='.$this->token);
					}
				} else {
					$this->error('不存在删除数据，删除失败', 'index.php?g=User&m=Test&a=classIndex&token='.$this->token);
				}
			}
			
		}elseif (2 == $style){
			$findDatas = $this->subjectModel->where(array('id'=>trim($_GET['tid']), 'item_id'=>trim($_GET['id'])))->find();
			if (!empty($findDatas)) {
				$deleteBack = $this->subjectModel->where(array('id'=>trim($_GET['tid'])))->delete();
				if ($deleteBack) {
					$this->success('删除成功','index.php?g=User&m=Test&a=subject&token='.$this->token.'&id='.trim($_GET['id']));
				} else {
					$this->error('编辑失败', 'index.php?g=User&m=Test&a=subject&token='.$this->token.'&id='.trim($_GET['id']));
				}
			} else {
				$this->error('系统中不存在这个题目', 'index.php?g=User&m=Test&a=subject&token='.$this->token.'&id='.trim($_GET['id']));
			}
			
			
			
		} else {
			$this->error('不存在这个类，编辑分类失败', 'index.php?g=User&m=Test&a=index&token='.$this->token);
		}
		
	}
	
	
	/*展示对应题库中的题目*/
	public function subject() {
		
		if (isset($_GET['id'])) {
			import('ORG.Util.Page');
			$count = $this->subjectModel->where(array('item_id'=>trim($_GET['id'])))->count();
			$page = new Page($count, 10);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $this->subjectModel->where(array('item_id'=>trim($_GET['id'])))->select();
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			$this->assign('id', trim($_GET['id']));
			$this->display();
		}
		
	}
	
	/*添加和编辑题库中的题目*/
	public function addSubject() {

		$type = trim($_GET['type'])?trim($_GET['type']):0;
		$subject_id = trim($_GET['id']);
     //   p($_REQUEST);die;
		if (0 == $type) {
			//添加题目数据			
			if (IS_POST) {

				$insertData = array(
								'item_id'=>trim($_POST['id']),
								'subject_title'=>trim($_POST['name']),
								'answer_a_con'=>trim($_POST['answer_a']),
								'answer_b_con'=>trim($_POST['answer_b']),
								'answer_c_con'=>trim($_POST['answer_c']),
								'answer_d_con'=>trim($_POST['answer_d']),
								'answer_e_con'=>trim($_POST['answer_e']),
								'answer_num'=>trim($_POST['answer_num']),
								'subject_score'=>trim($_POST['title_score']),
								'subject_check'=>trim($_POST['title_answer']),
								'score_is'=>0
							);

				$insertBack = $this->subjectModel->add($insertData);
				if ($insertBack) {
					$this->success('添加成功','index.php?g=User&m=Test&a=subject&token='.$this->token.'&id='.trim($_POST['id']));
				} else {
					$this->error('添加失败', 'index.php?g=User&m=Test&a=addSubject&token='.$this->token);
				}
				
				
			} else {

				$this->assign('type', 0);
				$this->assign('id', $subject_id);
				$score_is = $this->testModel->where(array('id'=>$subject_id))->find();
				$this->display();
			}
			
		} elseif (1 == $type) {
			//修改题目数据
			if (IS_POST) {				
				$updateData = array(
						'item_id'=>trim($_POST['id']),
						'subject_title'=>trim($_POST['name']),
						'answer_a_con'=>trim($_POST['answer_a']),
						'answer_b_con'=>trim($_POST['answer_b']),
						'answer_c_con'=>trim($_POST['answer_c']),
						'answer_d_con'=>trim($_POST['answer_d']),
						'answer_e_con'=>trim($_POST['answer_e']),
						'answer_num'=>trim($_POST['answer_num']),
						'subject_score'=>trim($_POST['title_score']),
						'subject_check'=>trim($_POST['title_answer']),
						'score_is'=>0
				);
				/*先查找有没有*/
				$findDatas = $this->subjectModel->where(array('id'=>trim($_GET['tid']), 'item_id'=>trim($_POST['id'])))->find();
				if (!empty($findDatas)) {
					$insertBack = $this->subjectModel->where(array('id'=>trim($_GET['tid'])))->save($updateData);
					if ($insertBack !== false) {
						$this->success('编辑成功','index.php?g=User&m=Test&a=subject&token='.$this->token.'&id='.trim($_POST['id']));
					} else {
						$this->error('编辑失败', 'index.php?g=User&m=Test&a=addSubject&type=1&token='.$this->token);
					}
				} else {
					$this->error('系统中不存在这个题目', 'index.php?g=User&m=Test&a=addSubject&type=1&token='.$this->token);
				}
			} else {
				/*编辑查找*/
				if (isset($_GET['tid'])) {
					$findDatas = $this->subjectModel->where(array('id'=>trim($_GET['tid'])))->find();
					if (!empty($findDatas)) {
						$this->assign('data', $findDatas);
					} else {
						$this->error('系统中不存在这个题目', 'index.php?g=User&m=Test&a=addSubject&token='.$this->token);
					}
					$this->assign('type', 1);
					$this->assign('id', $subject_id);
				}
				
				$this->display();
			}
			
		} else {
			$this->display();
		}
		
	}
	
	/*删除题目*/
	public function delSubject() {
		
	}
	
	/*展示所有选项有分数的题目*/
	public function titleS() {
		
		if (isset($_GET['id'])) {
			import('ORG.Util.Page');
			$count = $this->subjectModel->where(array('item_id'=>trim($_GET['id'])))->count();
			$page = new Page($count, 10);
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$list = $this->subjectModel->where(array('item_id'=>trim($_GET['id'])))->select();
			$show = $page->show();
			$this->assign('list', $list);
			$this->assign('page', $show);
			$this->assign('id', trim($_GET['id']));
			$this->display();
		}
	}
	
	
	
	/*添加和编辑选项有分数的题目,调研题目，答案不唯一*/
	public function addTitleS() {
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		$subject_id = trim($_GET['id']);
		if (0 == $type) {						
			if (IS_POST) {
				$scoreModel = M('test_score');
				$insertArray = array(
						'item_id'=>trim($_POST['id']),
						'subject_title'=>trim($_POST['name']),
						'score_is'=>1
								);
				
				$insertDatas = array(
						'answer_content'=>trim($_POST['answer_a']),
						'answer_score'=>trim($_POST['answer_a_score'])
								);
				$insertArray['answer_a_con'] = $scoreModel->where()->add($insertDatas);
				
				$insertDatas = array(
						'answer_content'=>trim($_POST['answer_b']),
						'answer_score'=>trim($_POST['answer_b_score'])
								);
				$insertArray['answer_b_con'] = $scoreModel->where()->add($insertDatas);
				
				$insertDatas = array(
						'answer_content'=>trim($_POST['answer_c']),
						'answer_score'=>trim($_POST['answer_c_score'])
								);
				$insertArray['answer_c_con'] = $scoreModel->where()->add($insertDatas);
				
				$insertDatas = array(
						'answer_content'=>trim($_POST['answer_d']),
						'answer_score'=>trim($_POST['answer_d_score'])
								);
				$insertArray['answer_d_con'] = $scoreModel->where()->add($insertDatas);
				
				$insertDatas = array(
						'answer_content'=>trim($_POST['answer_e']),
						'answer_score'=>trim($_POST['answer_e_score'])
							);
				$insertArray['answer_e_con'] = $scoreModel->where()->add($insertDatas);
				
				if ($this->subjectModel->add($insertArray)) {
					$this->success('添加成功', 'index.php?g=User&m=Test&a=titleS&token='.$this->token.'&tyep=0&id='.trim($_POST['id']));
				} else {
					$this->error('添加失败', 'index.php?g=User&m=Test&a=addTitleS&token='.$this->token.'&tyep=0&id='.trim($_POST['id']));
				}				
				
			} else {
				
				/*查找对应的题目类型，做出相应页面的跳转*/

				$this->assign('type', 0);
				$this->assign('id', $subject_id);
				$this->display();
			}
			
		} elseif (1 == $type) {


			if (IS_POST) {				
				/*首先得查找是否有这个数据*/
				if (isset($_POST['id']) && isset($_GET['tid'])) {
					
					$findDatas = $this->subjectModel->where(array('id'=>trim($_GET['tid']), 'item_id'=>trim($_POST['id']), 'score_is'=>1))->find();
					if (!empty($findDatas)) {
						$scoreModel = M('test_score');
						$updateDatas = array(
										'answer_content'=>trim($_POST['answer_a']),
										'answer_score'=>trim($_POST['answer_a_score'])
									);
						$scoreModel->where(array('id'=>$findDatas['answer_a_con']))->save($updateDatas);
						
						$updateDatas = array(
								'answer_content'=>trim($_POST['answer_b']),
								'answer_score'=>trim($_POST['answer_b_score'])
						);
						$scoreModel->where(array('id'=>$findDatas['answer_b_con']))->save($updateDatas);
						
						$updateDatas = array(
								'answer_content'=>trim($_POST['answer_c']),
								'answer_score'=>trim($_POST['answer_c_score'])
						);
						$scoreModel->where(array('id'=>$findDatas['answer_c_con']))->save($updateDatas);
						
						$updateDatas = array(
								'answer_content'=>trim($_POST['answer_d']),
								'answer_score'=>trim($_POST['answer_d_score'])
						);
						$scoreModel->where(array('id'=>$findDatas['answer_d_con']))->save($updateDatas);
						
						$updateDatas = array(
								'answer_content'=>trim($_POST['answer_e']),
								'answer_score'=>trim($_POST['answer_e_score'])
						);

						$scoreModel->where(array('id'=>$findDatas['answer_e_con']))->save($updateDatas);
						
						$this->success('编辑成功', 'index.php?g=User&m=Test&a=titleS&token='.$this->token.'&tyep=0&id='.trim($_POST['id']));
					}
				}
				
			} else {
				/*编辑查找*/
				if (isset($_GET['tid'])) {
					$findDatas = $this->subjectModel->where(array('id'=>trim($_GET['tid']), 'score_is'=>1))->find();
					if (!empty($findDatas)) {
						
						/*现在是查找*/
						$scoreModel = M('test_score');
						$oneData = $scoreModel->where(array('id'=>intval($findDatas['answer_a_con'])))->find();
						$twoData = $scoreModel->where(array('id'=>intval($findDatas['answer_b_con'])))->find();
						$threeData = $scoreModel->where(array('id'=>intval($findDatas['answer_c_con'])))->find();
						$fourData = $scoreModel->where(array('id'=>intval($findDatas['answer_d_con'])))->find();
						$fiveData = $scoreModel->where(array('id'=>intval($findDatas['answer_e_con'])))->find();
						$this->assign('oneData', $oneData);
						$this->assign('twoData', $twoData);
						$this->assign('threeData', $threeData);
						$this->assign('fourData', $fourData);
						$this->assign('fiveData', $fiveData);
						
						$this->assign('data', $findDatas);
					} else {
						$this->error('系统中不存在这个题目', 'index.php?g=User&m=Test&a=addSubject&token='.$this->token);
					}
					$this->assign('type', 1);
					$this->assign('id', $subject_id);
				}
				
				$this->display();
			}
			
		} else {
			$this->display();
		}
	}	
	
	
	
	
	
	/*设置评分结果标准*/
	public function addResult() {
		
		$type = trim($_GET['type'])?trim($_GET['type']):0;
		if (0 == $type) {
			
			if (IS_POST) {
				$insertArray = array();
				$insertArray['item_id'] = trim($_POST['id']);
				$resultInfoModel = M('test_result_info');
				$insertDatas = array(
					'item_id'=>trim($_POST['id']),
					'score_min'=>trim($_POST['one_min']),
					'score_max'=>trim($_POST['one_max']),
					'result_info'=>trim($_POST['one_info'])
				);
				$insertArray['score_one_id'] = $resultInfoModel->where()->add($insertDatas);
				$insertDatas = array(
						'item_id'=>trim($_POST['id']),
						'score_min'=>trim($_POST['two_min']),
						'score_max'=>trim($_POST['two_max']),
						'result_info'=>trim($_POST['two_info'])
				);
				$insertArray['score_two_id'] = $resultInfoModel->where()->add($insertDatas);
				$insertDatas = array(
						'item_id'=>trim($_POST['id']),
						'score_min'=>trim($_POST['three_min']),
						'score_max'=>trim($_POST['three_max']),
						'result_info'=>trim($_POST['three_info'])
				);
				$insertArray['score_three_id'] = $resultInfoModel->where()->add($insertDatas);
				$insertDatas = array(
						'item_id'=>trim($_POST['id']),
						'score_min'=>trim($_POST['four_min']),
						'score_max'=>trim($_POST['four_max']),
						'result_info'=>trim($_POST['four_info'])
				);
				$insertArray['score_four_id'] = $resultInfoModel->where()->add($insertDatas);
				$insertDatas = array(
						'item_id'=>trim($_POST['id']),
						'score_min'=>trim($_POST['five_min']),
						'score_max'=>trim($_POST['five_max']),
						'result_info'=>trim($_POST['five_info'])
				);
				$insertArray['score_five_id'] = $resultInfoModel->where()->add($insertDatas);
				
				$resultModel = M('test_result');
				if ($resultModel->where()->add($insertArray)) {
					$this->success('设置成功', 'index.php?g=User&m=Test&a=index&token='.$this->token);
				} else {
					$this->error('设置失败', 'index.php?g=User&m=Test&a=index&token='.$this->token);
				}				
				
			} else {
				
				$resultModel = M('test_result');
				$find = $resultModel->where(array('item_id'=>trim($_GET['id'])))->find();
				if (!empty($find)) {
					$resultInfoModel = M('test_result_info');
					$findOne = $resultInfoModel->where(array('id'=>$find['score_one_id']))->find();
					$findTwo = $resultInfoModel->where(array('id'=>$find['score_two_id']))->find();
					$findThree = $resultInfoModel->where(array('id'=>$find['score_three_id']))->find();
					$findFour = $resultInfoModel->where(array('id'=>$find['score_four_id']))->find();
					$findFive = $resultInfoModel->where(array('id'=>$find['score_five_id']))->find();
					$this->assign('findOne', $findOne);
					$this->assign('findTwo', $findTwo);
					$this->assign('findThree', $findThree);
					$this->assign('findFour', $findFour);
					$this->assign('findFive', $findFive);					
					$this->assign('type', 1);	
				} else {
					$this->assign('type', 0);
				}
				
				$this->assign('id', trim($_GET['id']));				
				$this->display();
			}
						
		} elseif (1 == $type) {
			
			if (IS_POST) {
				
				$resultModel = M('test_result');
				$find = $resultModel->where(array('item_id'=>trim($_POST['id'])))->find();
				
				if (!empty($find)) {
					$resultInfoModel = M('test_result_info');
					$updateDatas = array(
							'item_id'=>trim($_POST['id']),
							'score_min'=>trim($_POST['one_min']),
							'score_max'=>trim($_POST['one_max']),
							'result_info'=>trim($_POST['one_info'])
					);
					$resultInfoModel->where(array('id'=>$find['score_one_id']))->save($updateDatas);
					
					$updateDatas = array(
							'item_id'=>trim($_POST['id']),
							'score_min'=>trim($_POST['two_min']),
							'score_max'=>trim($_POST['two_max']),
							'result_info'=>trim($_POST['two_info'])
					);
					$resultInfoModel->where(array('id'=>$find['score_two_id']))->save($updateDatas);
					
					$updateDatas = array(
							'item_id'=>trim($_POST['id']),
							'score_min'=>trim($_POST['three_min']),
							'score_max'=>trim($_POST['three_max']),
							'result_info'=>trim($_POST['three_info'])
					);
					$resultInfoModel->where(array('id'=>$find['score_three_id']))->save($updateDatas);
					
					$updateDatas = array(
							'item_id'=>trim($_POST['id']),
							'score_min'=>trim($_POST['four_min']),
							'score_max'=>trim($_POST['four_max']),
							'result_info'=>trim($_POST['four_info'])
					);
					$resultInfoModel->where(array('id'=>$find['score_four_id']))->save($updateDatas);
					
					$updateDatas = array(
							'item_id'=>trim($_POST['id']),
							'score_min'=>trim($_POST['five_min']),
							'score_max'=>trim($_POST['five_max']),
							'result_info'=>trim($_POST['five_info'])
					);
					$resultInfoModel->where(array('id'=>$find['score_five_id']))->save($updateDatas);
					$this->success('设置成功', 'index.php?g=User&m=Test&a=index&token='.$this->token);
				}				
			}
		} else {
			$this->display();
		}
	}
	
	// 选题答案统计
	public function ansTotal(){
		$id = intval($_GET['id']);
		$submitModel = M('test_submit');
		$subjectModel = M('test_subject');
		$scoreModel = M('test_score');
		// 获取到当前题目库的所有题目的名称
		$allTipic = $subjectModel->where(array('item_id'=>$id))->select();
        //p($allTipic);
		foreach ($allTipic as $key => $value) {
			$allTipic[$key]['answer_a'] = $scoreModel->where(array('id'=>$value['answer_a_con']))->find();
			$allTipic[$key]['answer_b'] = $scoreModel->where(array('id'=>$value['answer_b_con']))->find();
			$allTipic[$key]['answer_c'] = $scoreModel->where(array('id'=>$value['answer_c_con']))->find();
			$allTipic[$key]['answer_d'] = $scoreModel->where(array('id'=>$value['answer_d_con']))->find();
			$allTipic[$key]['answer_e'] = $scoreModel->where(array('id'=>$value['answer_e_con']))->find();

			// 重新加上一个字段
			$allTipic[$key]['answer_a'] = $allTipic[$key]['answer_a']['answer_content'];
			$allTipic[$key]['answer_b'] = $allTipic[$key]['answer_b']['answer_content'];
			$allTipic[$key]['answer_c'] = $allTipic[$key]['answer_c']['answer_content'];
			$allTipic[$key]['answer_d'] = $allTipic[$key]['answer_d']['answer_content'];
			$allTipic[$key]['answer_e'] = $allTipic[$key]['answer_e']['answer_content'];
		}
		// 获取到提交的结果
		$allMan = $submitModel->where(array('item_id'=>$id))->select();
      //  p($allMan);
		// 如果查询结果存在的话
        $tid=M('Test_item')->where(array('id'=>$id))->getField('test_type_id');
        $status=M('Test_class')->where(array('id'=>$tid))->getField('class_status');
        if($status==0){//走这里
            $tem=$subjectModel->where(array('item_id'=>$id))->select();
            $a1=0;
            $b1=0;
            $c1=0;
            $d1=0;
            $e1=0;
            $f1=0;
            foreach($allMan as $k1=>$v1){
                $allMan[$k1]['submit_result']=json_decode(htmlspecialchars_decode($v1['submit_result']),true);
                //if( $allMan[$k1]['submit_result'][0][$v['id]']){

            }
            foreach($tem as $k=>$v){

                foreach($allMan as $k2=>$v2){
                    if($v2['submit_result'][0][$v['id']]==A){
                        $a1=$a1+1;
                    }
                    if($v2['submit_result'][0][$v['id']]==B){
                        $b1=$b1+1;
                    }
                    if($v2['submit_result'][0][$v['id']]==C){
                        $c1=$c1+1;
                    }
                    if($v2['submit_result'][0][$v['id']]==D){
                        $d1=$d1+1;
                    }
                    if($v2['submit_result'][0][$v['id']]==E){
                        $e1=$e1+1;
                    }
                }
                $tem[$k]['answer_a']=$v['answer_a_con'].'-'.$a1;
                $tem[$k]['answer_b']=$v['answer_b_con'].'-'.$b1;
                $tem[$k]['answer_c']=$v['answer_c_con'].'-'.$c1;
                $tem[$k]['answer_d']=$v['answer_d_con'].'-'.$d1;
                $tem[$k]['answer_e']=$v['answer_e_con'].'-'.$e1;

            }
            //ho $a1;

           //  p($tem);
            //p($allMan);
            $this->assign('subjectModel',$tem);
        }else{
            if ($allMan) {
                $count0_1 = 0;$count0_2 = 0;$count0_3 = 0;$count0_4 = 0;$count0_5 = 0;
                $count1_1 = 0;$count1_2 = 0;$count1_3 = 0;$count1_4 = 0;$count1_5 = 0;
                $count2_1 = 0;$count2_2 = 0;$count2_3 = 0;$count2_4 = 0;$count2_5 = 0;
                $count3_1 = 0;$count3_2 = 0;$count3_3 = 0;$count3_4 = 0;$count3_5 = 0;
                $count4_1 = 0;$count4_2 = 0;$count4_3 = 0;$count4_4 = 0;$count4_5 = 0;
                // 首先统计有多少题目
                $countNumber = 0;
                $begin = 0;
                while(substr($allMan[0]['submit_result'],$begin,5)){
                    $countNumber++;
                    $begin += 5;
                }


                foreach ($allMan as $key => $value) {
                    if (substr($value['submit_result'],0,5)) {
                        // 首先是第一道题目
                        $allMan[$key]['a'] = substr($value['submit_result'],0,5);
                        if (substr($allMan[$key]['a'],4,1) == 'A'){
                            $count0_1++;
                        }elseif(substr($allMan[$key]['a'],4,1) == 'B'){
                            $count0_2++;
                        }elseif(substr($allMan[$key]['a'],4,1) == 'C'){
                            $count0_3++;
                        }elseif(substr($allMan[$key]['a'],4,1) == 'E'){
                            $count0_4++;
                        }elseif(substr($allMan[$key]['a'],4,1) == 'F'){
                            $count0_5++;
                        }
                    }
                    if (substr($value['submit_result'],5,5)) {
                        $allMan[$key]['b'] = substr($value['submit_result'],5,5);
                        if (substr($allMan[$key]['b'],4,1) == 'A') {
                            $count1_1++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'B'){
                            $count1_2++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'C'){
                            $count1_3++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'E'){
                            $count1_4++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'F'){
                            $count1_5++;
                        }
                    }
                    if (substr($value['submit_result'],10,5)) {
                        $allMan[$key]['b'] = substr($value['submit_result'],10,5);
                        if (substr($allMan[$key]['b'],4,1) == 'A') {
                            $count2_1++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'B'){
                            $count2_2++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'C'){
                            $count2_3++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'E'){
                            $count2_4++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'F'){
                            $count2_5++;
                        }
                    }
                    if (substr($value['submit_result'],15,5)) {
                        $allMan[$key]['b'] = substr($value['submit_result'],15,5);
                        if (substr($allMan[$key]['b'],4,1) == 'A') {
                            $count3_1++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'B'){
                            $count3_2++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'C'){
                            $count3_3++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'E'){
                            $count3_4++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'F'){
                            $count3_5++;
                        }
                    }
                    if (substr($value['submit_result'],20,5)) {
                        $allMan[$key]['b'] = substr($value['submit_result'],20,5);
                        if (substr($allMan[$key]['b'],4,1) == 'A') {
                            $count4_1++;
                        }elseif(substr($allMan[$key]['b'],4,1 ) == 'B'){
                            $count4_2++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'C'){
                            $count4_3++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'E'){
                            $count4_4++;
                        }elseif(substr($allMan[$key]['b'],4,1) == 'F'){
                            $count4_5++;
                        }
                    }

                }
                //
                //     p($allTipic);
                foreach ($allTipic as $key => $value) {
                    $countstr1 = 'count'.$key.'_1';
                    $countstr2 = 'count'.$key.'_2';
                    $countstr3 = 'count'.$key.'_3';
                    $countstr4 = 'count'.$key.'_4';
                    $countstr5 = 'count'.$key.'_5';
                    $allTipic[$key]['answer_a'] = $allTipic[$key]['answer_a']."-".$$countstr1;
                    $allTipic[$key]['answer_b'] = $allTipic[$key]['answer_b']."-".$$countstr2;
                    $allTipic[$key]['answer_c'] = $allTipic[$key]['answer_c']."-".$$countstr3;
                    $allTipic[$key]['answer_d'] = $allTipic[$key]['answer_d']."-".$$countstr4;
                    $allTipic[$key]['answer_e'] = $allTipic[$key]['answer_e']."-".$$countstr5;
                }

            }
            $this->assign('subjectModel',$allTipic);
        }

		// 遍历答案
		$this->display();
	}
	
	
	
	
	
	
	
	
}
