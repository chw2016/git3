<?php
class SmsAction extends UserAction{
	public function index(){
		$sms_config_model=M('config_sms');
		if(IS_POST){
            if(!$_POST['status']){
                $_POST['status'] = 0;
            }
			$where=array('token'=>session('token'));
			$check=$sms_config_model->where($where)->find();
			if (!$_POST['id'] && !$check){
                $_POST['token'] = session('token');
                $_POST['status'] = 1;
                if($sms_config_model->add($_POST)){
                    $this->success('操作成功', U(MODULE_NAME . '/index'));
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/index'));
                }
			}else{
				if($sms_config_model->create()){
                    if($sms_config_model->where($where)->save($_POST)){
                        $this->success('操作成功', U(MODULE_NAME . '/index'));
                    }else{
                        $this->error('操作失败', U(MODULE_NAME . '/index'));
                    }
				}else{
					$this->error($sms_config_model->getError());
				}
			}
		}else{

			$smsConfig=$sms_config_model->where(array('token'=>session('token')))->find();
			//
			$this->assign('smsConfig',$smsConfig);
            $this->assign('hover',1);
			$this->display();
		}
	}

    public function record(){
        $sms_config_model=M('Sms_send_list');
        $where['token']=session('token');
        $count=$sms_config_model->where($where)->count();
        $page=new Page($count,15);
        $info=$sms_config_model->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->assign('hover',2);
        $this->display();
    }


}


?>