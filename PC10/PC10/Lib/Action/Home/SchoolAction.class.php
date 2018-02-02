<?php
class SchoolAction extends Action{
	//关注回复
   public $uid= '';
   public $token='';
   public $kg_super_meb=null;

   public function addschool(){
        $this->uid = 464;
        $this->token = 'cf8025fc8d6071bf4f6a649b19bcf3bf';
        $this->kg_super_meb = M("Kg_super_meb_register");
        if(IS_POST){
            if(!empty($_POST['username']) && !empty($_POST['name']) && !empty($_POST['password']) && !empty($_POST['password'])){
                $data['username'] = $_POST['username'];
                $data['name'] = $_POST['name'];
                $data['tel'] = $_POST['tel'];
                $data['pwd'] = $_POST['password'];
                $data['password'] = $_POST['password'];
                $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
                $data['uid'] = $this->uid;
                $data['status'] = 0;
                $data['token'] = $this->token;
                $data['last_edit_time'] = $data['addtime'];
                if($this->kg_super_meb->where(array('username'=>$_POST['username'],'uid'=>$this->uid,'token'=>$this->token))->find()){
                    header("Content-type: text/html; charset=utf-8"); 
		    echo "<script type='text/javascript'>alert('注册失败用户名已存在');window.location.href='http://www.jiayuanbaobei.com/zhuce.html';</script>";
                }else{
                    if($this->kg_super_meb->add($data)){
                        header("Content-type: text/html; charset=utf-8"); 
			echo "<script type='text/javascript'>alert('感谢！我们将在24小时内联系您，协助办理开通事宜。');window.location.href='http://www.jiayuanbaobei.com/';</script>";
                    } else {
                        header("Content-type: text/html; charset=utf-8"); 
			echo "<script type='text/javascript'>alert('注册失败');window.location.href='http://www.jiayuanbaobei.com/zhuce.html';</script>";
                    }
                }    
            }else{
                header("Content-type: text/html; charset=utf-8"); 
		echo "<script type='text/javascript'>alert('信息不能为空');window.location.href='http://www.jiayuanbaobei.com/zhuce.html';</script>";
            }

        }else{
            header("Content-type: text/html; charset=utf-8"); 
	    echo "<script type='text/javascript'>alert('信息不能为空');window.location.href='http://www.jiayuanbaobei.com/zhuce.html';</script>";
            exit;
        }    



    }

}