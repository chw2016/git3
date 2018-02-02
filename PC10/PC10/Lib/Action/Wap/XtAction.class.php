<?php
class XtAction extends BaseAction{
    public function index(){
        $agent = $_SERVER['HTTP_USER_AGENT']; 
        if(!strpos($agent,"MicroMessenger")) {
          //  echo '此功能只能在微信浏览器中使用';exit;
        }
        $where['token']      = $this->_get('token'); 
		$where['id']      = $this->_get('id');

        $hostset =  M('Xt')->where($where)->find();
        $this->assign('info',$hostset);
        $this->display();
    }
    
   
    
    public function book(){ 
            $data['wecha_id']  =  $this->_post('wecha_id'); 
            $data['name']  =  $this->_post('name'); 
            $data['phone']   =  $this->_post('phone');  
            $data['gocount']       = $this->_post('gocount');  
            $data['hid'] = $this->_get('hid');
            $data['token'] = $this->_get('token');
            $order = M('Xtlist')->data($data)->add();    

            if($order){
                echo "恭喜,申请成功！";
            }else{
                echo "请从新申请！";
            }            
            exit;
         
            
        
    }
	
	
	public function book1(){ 
     
            $data['wecha_id']  =  $this->_post('wecha_id'); 
            $data['name']  =  $this->_post('name'); 
            $data['phone']   =  $this->_post('phone');  
            $data['content']       = $this->_post('content');  
            $data['hid'] = $this->_get('hid');
            $data['token'] = $this->_get('token');

            $order = M('Xtbless')->data($data)->add();    

            if($order){
                echo "恭喜,发送成功！";
            }else{
                echo "请从新发送！";
            }            
            exit;
         
            
        
    }
	
	
}
    
?>