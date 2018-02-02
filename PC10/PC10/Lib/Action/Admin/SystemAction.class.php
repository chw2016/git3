<?php
/**
 *网站后台
 *@package WeiKuCMS
 *@author WeiKuCMS
 **/
class SystemAction extends BackAction{
	
	public function index(){
		$where['display']=1;
		$where['status']=1;
		$order['sort']='asc';
		$nav=M('node')->where($where)->order($order)->select();	
		
		    import('@.ORG.RBAC');
                    $accessList =   RBAC::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
			
                foreach($nav as $key=>$module) {

                    if(isset($accessList[strtoupper('cms')][strtoupper($module['name'])]) || $_SESSION['administrator']) {
                    
                        $module['access'] =   1;
                        $menu[$key]  = $module;
						
                  }
                }
				
			
  $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=	$menu;
           
            if(!empty($_GET['tag'])){
                $this->assign('menuTag',$_GET['tag']);
            }

            $this->assign('nav',$menu);

		$this->display();
	}
	
	public function menu(){
		if(empty($_GET['pid'])){
			$where['display']=2;
			$where['status']=1;
			$where['pid']=2;
			$where['level']=2;
			$order['sort']='asc';
			$nav=M('node')->where($where)->order($order)->select();
			
			
			          import('@.ORG.RBAC');
                    $accessList =   RBAC::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
			
                foreach($nav as $key=>$module) {

                    if(isset($accessList[strtoupper('cms')][strtoupper($module['name'])]) || $_SESSION['administrator']) {
                    
                        $module['access'] =   1;
                        $menu[$key]  = $module;
						
                  }
                }
				
			
  $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=	$menu;
           
            if(!empty($_GET['tag'])){
                $this->assign('menuTag',$_GET['tag']);
            }

            $this->assign('nav',$menu);
			

		}
		$this->display();
	}
	
	public function main(){
		$this->display();
	}
}