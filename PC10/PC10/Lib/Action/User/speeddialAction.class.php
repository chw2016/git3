<?php
/**
 *语音回复
**/
class speeddialAction extends UserAction{
		public function index(){
		$where['token']=session('token');
		$info=M('speeddial')->where($where)->find();
		/*echo "<pre>";
		print_r($info);
		exit();*/
		// $this->assign('info',$info);
		
		if(IS_POST){
			if($info==false){				
		$this->all_insert();
			}else{
		$this->all_save();
			}
		}else{
			$this->assign('info',$info);
            $this->assign('hover5',1);
            $this->assign('sendMusicUrl',$info);
		$this->display();
		}
	}



	
}
?>