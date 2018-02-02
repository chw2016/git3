<?php 
	class GuideAction extends UserAction{
		public function index(){
			$db=M('app');
			$result=$db->where(array('token'=>$this->token))->find();
			if($result){
				$this->assign('findD', $result);
				$this->assign('sendInfo', 1);
			}else{
				$this->assign('sendInfo',0);
			}

			$this->display();
		}


		// 获取数据，方法名getData
		public function getD(){

			/*首先获取token唯一标识，然后查找数据表wxuser*/
			
			if (!isset($_GET['token'])) {
				// $token = $_GET['token'];
				$img1=isset($_POST['img1'])?$_POST['img1']:"";
				$img2=isset($_POST['img2'])?$_POST['img2']:"";
				$img3=isset($_POST['img3'])?$_POST['img3']:"";
				$img4=isset($_POST['img4'])?$_POST['img4']:"";
				$state=isset($_POST['state'])?$_POST['state']:"";
				$name=session('name');
				$token=session('token');
				/*print_r($name);
				exit();*/
				$dataG=array('img_1'=>$img1,'img_2'=>$img2,'img_3'=>$img3,'$img_4'=>$img4,'stat'=>$state,'infom'=>'数据插入成功！');
				$data['url_1']=$img1;
				$data['url_2']=$img2;
				$data['url_3']=$img3;
				$data['url_4']=$img4;
				$data['state']=$state;
				
				$data['token']=$token;
				$data['name']=$name;
				

				$AppModel = M('app');
				$isApp = $AppModel->where(array('token' =>$token))->count();
				if ($isApp > 0) {
					$isSuccess = $AppModel->where(array('id' => $isApp['id']))->save($data);
					if ($isSuccess) {
						if ($isSuccess) {
							$this->success('操作成功',U(MODULE_NAME.'/index'),true);
						}else{
							$this->error('操作失败',U(MODULE_NAME.'/index'),true);
						}
					}
				} else {
					$isSuccess = $AppModel->add($data);
					if ($isSuccess) {
						if ($isSuccess) {
							$this->success('操作成功',U(MODULE_NAME.'/index'),true);
						}else{
							$this->error('操作失败',U(MODULE_NAME.'/index'),true);
						}
					}
				}

			}			
		}
		//作為判斷用戶是否開啟
		public function changestatus(){
			$status = $this->_post('decide');
			$status = empty($status) ? 0 : 1;
			$token = $this->_get('token');
			$data = M('App')->where(array('token'=>$token))->find();
		    $res = M('App')->where(array('id'=>$data['id']))->data(array('state'=>$status))->save();

		    echo $this->encode(array('code'=>0));

		}

	}          