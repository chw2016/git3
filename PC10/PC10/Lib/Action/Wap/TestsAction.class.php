<?php
/**
 * WAP端
 * @author NICK
 *
 */
class TestsAction extends BaseAction{

	public $token;
	public $openid;
	public $userModel;
	public $userDatas;
	public $wxUserModel;
	public $wxUserDatas;
	public $itemModel;
	public $titleModel;
	
	public function _initialize() {
		parent::_initialize();
		echo 11;die;
		if ((!session('?token')) || (!session('?openid'))) {
			session('token', $_REQUEST['token']);
			session('openid', $_REQUEST['openid']);
		}
	
		$this->token = session('token');
		$this->openid = session('openid');	
		
		$this->itemModel = M('test_item');
		$this->titleModel = M('test_subject');
				
		$this->userModel = M('wxuser');
		$this->userDatas = $this->userModel->where(array('token'=>$this->token))->find();
		
		$this->wxUserModel = M('wxusers');
		$this->wxUserDatas = $this->wxUserModel->where(array('uid'=>$this->userDatas['id'], 'openid'=>$this->openid))->find();
		$this->assign('token', $this->token);
		$this->assign('openid', $this->openid);
	}
	
	public function index() {
		if (isset($_GET['id'])) {
			$itemId = trim($_GET['id']);
			$itemInfo = $this->itemModel->where(array('wxuser_id'=>$this->userDatas['id'], 'id'=>$itemId, 'score_is'=>1))->find();						
			if (!empty($itemInfo)) {				
				$titleInfos = $this->titleModel->where(array('item_id'=>$itemInfo['id']))->select();
				if (!empty($titleInfos)) {
					$this->assign('data', $itemInfo);
					if (1 == $itemInfo['score_is']) {
						foreach ($titleInfos as $key => $value) {
							$scoreModel = M('test_score');
							$oneInfo = $scoreModel->where(array('id'=>intval($value['answer_a_con'])))->find();
							$twoInfo = $scoreModel->where(array('id'=>intval($value['answer_b_con'])))->find();
							$threeInfo = $scoreModel->where(array('id'=>intval($value['answer_c_con'])))->find();
							$fourInfo = $scoreModel->where(array('id'=>intval($value['answer_d_con'])))->find();
							$fiveInfo = $scoreModel->where(array('id'=>intval($value['answer_e_con'])))->find();
							
							$titleInfos[$key]['answer_a_con'] = $oneInfo['answer_content'];
							$titleInfos[$key]['answer_a_score'] = $oneInfo['answer_score'];
							
							$titleInfos[$key]['answer_b_con'] = $twoInfo['answer_content'];
							$titleInfos[$key]['answer_b_score'] = $twoInfo['answer_score'];
							
							$titleInfos[$key]['answer_c_con'] = $threeInfo['answer_content'];
							$titleInfos[$key]['answer_c_score'] = $threeInfo['answer_score'];
							
							$titleInfos[$key]['answer_d_con'] = $fourInfo['answer_content'];
							$titleInfos[$key]['answer_d_score'] = $fourInfo['answer_score'];
							
							$titleInfos[$key]['answer_e_con'] = $fiveInfo['answer_content'];
							$titleInfos[$key]['answer_e_score'] = $fiveInfo['answer_score'];							
						}
//                        echo $itemId;exit;
						$this->assign('id', $itemId);
						$this->assign('count', count($titleInfos));
						$this->assign('title', $titleInfos);
						$this->display();
					}					
				}
			} else {
				
				$resultModel = M('test_submit');
				$find = $resultModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'item_id'=>$itemId, 'is_test'=>1))->find();
				if (empty($find)) {
					$itemInfo = $this->itemModel->where(array('wxuser_id'=>$this->userDatas['id'], 'id'=>$itemId, 'is_open'=>1))->find();
					$titleInfos = $this->titleModel->where(array('item_id'=>$itemInfo['id']))->select();
					$this->assign('data', $itemInfo);
					$this->assign('id', $itemId);
					$this->assign('count', count($titleInfos));
					$this->assign('title', $titleInfos);
					$this->display('tpl/Wap/default/Test_index_01.html');
					exit();
				} else {
					$this->display('tpl/Wap/default/Test_sign.html');
				}							
			}			
		}
		
		
	}
	
	public function test_Result() {
		if (IS_POST) {
			/*先查找分数*/
			$resultInfoModel = M('test_result_info');

			$conditon['item_id'] = trim($_POST['id']);
			$conditon['score_min'] = array('lt', trim($_POST['score']));
			$conditon['score_max'] = array('egt', trim($_POST['score']));
			$resultInfo = $resultInfoModel->where($conditon)->find();
			if (!empty($resultInfo)) {
				
				$insertDatas = array(
					'wxuser_id'=>$this->userDatas['id'],
					'wxusers_id'=>$this->wxUserDatas['id'],
					'item_id'=>trim($_POST['id']),
					'submit_result'=>trim($_POST['select']),
					'submit_score'=>trim($_POST['score']),
					'is_test'=>0
				);
				
				$submitModel = M('test_submit');
				$insertBack = $submitModel->add($insertDatas);
				if ($insertBack) {
				//添加成功后返回的数据
					echo $this->encode(array('status'=>100, 'min'=>$resultInfo['score_min'], 'max'=>$resultInfo['score_max'], 'info'=>$resultInfo['result_info']));
				}				
			}			
		}
		
	}
	
	public function suggest() {
		
		if (IS_POST) {
			if (isset($_POST['id'])) {
				$find = $this->itemModel->where(array('id'=>trim($_POST['id']), 'wxuser_id'=>$this->userDatas['id']))->find();
				if (!empty($find)) {
					
					$suggestModel = M('test_suggest');
					$insertDatas = array(							
									'item_id'=>$find['id'],
									'wxuser_id'=>$this->userDatas['id'],
									'wxusers_id'=>$this->wxUserDatas['id'],
									'suggest_name'=>$this->wxUserDatas['nickname'],
									'suggest'=>trim($_POST['reason'])
									);
				
					if ($suggestModel->add($insertDatas)) {
						echo $this->encode(array('status'=>100, 'info'=>'谢谢你对我提出你的宝贵意见', 'url'=>'index.php?g=Wap&m=Test&a=index&token='.$this->token.'&openid='.$this->openid.'&id='.$find['id']));
					} else {
						echo $this->encode(array('status'=>1, 'info'=>'提交失败', 'url'=>'index.php?g=Wap&m=Test&a=suggest&token='.$this->token.'&openid='.$this->openid.'&id='.$find['id']));
					}					
				} else {
					$this->error2('系统中已不存在该测试');
				}
			}
			
		} else {
			if (isset($_GET['id'])) {
				$find = $this->itemModel->where(array('id'=>trim($_GET['id']), 'wxuser_id'=>$this->userDatas['id']))->find();
				if (!empty($find)) {
					$this->assign('id', $find['id']);
					$this->display();
				} else {
					$this->error2('系统中已不存在该测试');
				}
			}
			
		}
	}
	
	public function addTest() {
		
		if (IS_POST) {
		
			$jsonData = $_POST['jsonstr'];
			$jsonData = htmlspecialchars_decode($jsonData);
			$jsonData = json_decode($jsonData);
			$score = 0;
			
			$itemId = $jsonData[0]->id;//获取对象数组值
			$findResult = M('test_result')->where(array('item_id'=>$itemId))->find();
			
			if (empty($findResult)) {			
				echo $this->encode(array('status'=>1, 'info'=>'测试没有设置结果段', 'url'=>'index.php?g=Wap&m=Test&a=index&token='.$this->token.'&openid='.$this->openid.'&id='.$itemId));
				exit();
			}
			
			foreach ($jsonData[0] as $key=> $value){
				if (($key != 'id') || ($key != 'advice')) {
					/*根据id查找项目*/
					$find = $this->itemModel->where(array('id'=>$itemId, 'wxuser_id'=>$this->userDatas['id'],'is_open'=>1))->find();
					if (!empty($find)) {
						$subjectInfos = $this->titleModel->where(array('item_id'=>$find['id'],'id'=>$key, 'subject_check'=>$value))->find();
						if (!empty($subjectInfos)) {
							$score = $score + $subjectInfos['subject_score'];
						}
					}
					
				}
			}

			
			/*查找匹配结果*/
			$resultInfoModel = M('test_result_info');
			
			$conditon['item_id'] = $itemId;
			$conditon['score_min'] = array('elt', $score);
			$conditon['score_max'] = array('egt', $score);
				
			$resultInfo = $resultInfoModel->where($conditon)->find();

			if (!empty($resultInfo)) {							
				/*先查找是否存在*/
				$submitModel = M('test_submit');
				$findSubmit = $submitModel->where(array('wxuser_id'=>$this->userDatas['id'], 'wxusers_id'=>$this->wxUserDatas['id'], 'item_id'=>$itemId, 'is_test'=>1))->find();
				if (empty($findSubmit)) {
					/*添加测试结果数据*/
					$insertDatas = array(
							'wxuser_id'=>$this->userDatas['id'],
							'wxusers_id'=>$this->wxUserDatas['id'],
							'item_id'=>$itemId,
							'submit_result'=>$_POST['jsonstr'],
							'submit_score'=>$score,
							'is_test'=>1
					);
					
					$insertBack = $submitModel->add($insertDatas);
					
					
					$suggestDatas = array(
									'item_id'=>$itemId,
									'suggest'=>$itemId = $jsonData[0]->advice,
									'name'=>$itemId = $jsonData[0]->name,
									'unio'=>$itemId = $jsonData[0]->unio,
									'phone'=>$jsonData[0]->phone,
									'token'=>$this->token,
									'openid'=>$this->openid,
									'wxuser_id'=>$this->userDatas['id'],
									'wxusers_id'=>$this->wxUserDatas['id'],
								);
					/*添加建议数据*/

					$suggestBack = M('test_suggest')->add($suggestDatas);
					if ($insertBack) {
						echo $this->encode(array('status'=>100, 'min'=>$resultInfo['score_min'], 'max'=>$resultInfo['score_max'], 'info'=>$resultInfo['result_info'], 'score'=>$score));
						exit();
					}
				} else {
					echo $this->encode(array('status'=>1, 'info'=>'你已经测试过了，不能再测试了'));
					exit();
				}								
			} else {
				/*分数段有问题*/
				echo $this->encode(array('status'=>1, 'info'=>'分数段设置有问题'));
				exit();
			}
		}
		
	}
	
	
}
