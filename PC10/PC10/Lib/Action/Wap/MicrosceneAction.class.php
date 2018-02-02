<?php 
	//前台页面
	class MicrosceneAction extends BaseAction {
		
		//显示首页
        protected function _initialize(){
        	parent::_initialize();
        }

		public function index(){
			$pid = $_GET['pid'];
			
			$db = M('scene');
			$dbs = M('scene_p');
			$result = $db->where(array('token'=>$this->token,"pid"=>$pid))->order('sorts')->select();
			$res = $dbs->where(array('token'=>$this->token,'id'=>$pid))->find();
			$this->assign("tpl",$this->tpl);
			$this->assign('info',$result);
			$this->assign('bgmu',$res);
			$this->assign('pid',$pid);
			$this->assign('token',$this->token);
			$this->display(); 
		}
		//保存数据
		public function updatedata(){
			$pid = $_GET['pid'];
			$token = $_GET['token'];
			$verificate = $this->_post('verificate');
			$data = array(
					"name" => $this->_post('name'),
					"sex" => $this->_post('sex'),
					"telphone" => $this->_post('telphone'),
					"company" => $this->_post('company'),
					"position" => $this->_post('position'),
					"email" => $this->_post('email'),
					"pid" => $pid,
					"token" => $token,
					"address" => $this->_post('address'),
					"verificate" => $verificate,
					"openid" => $this->openid,
					"campers"=>$this->_post('campers'),
					"num"=>$this->_post('num')
				);
			
			$result = M('Appoint')->add($data);
			
			if($result){
				$this->success("您的随机验证码是：".$verificate."，请妥善保管好该验证码",U(MODULE_NAME.'/index',array('token'=>$token,'pid'=>$pid)));
            }else{
                $this->error("添加失败",U(MODULE_NAME.'/index'));
            }
		}

        public function getusercount(){
            $id = $_REQUEST['pid'];
            $token = $_REQUEST['token'];
            $count1 = M('Appoint')->where(array('pid'=>$id,'token'=>$token))->count();
            $sdata = M('Scene')->where(array('token'=>$token,'pid'=>$id))->find();
            $count2 = $sdata['appoint_nums'];
            echo $this->encode(array('code'=>0,'counts'=>$count1+$count2));
        }


	}
