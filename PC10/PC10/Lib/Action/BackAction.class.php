<?php
class BackAction extends BaseAction{ protected $pid;
 protected function _initialize(){
	  parent::_initialize();
	   if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            import('@.ORG.RBAC');
            if (!RBAC::AccessDecision('CMS')) {
                //检查认证识别号
                if (!$_SESSION [C('USER_AUTH_KEY')]) {
                    //跳转到认证网关
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }
                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
        }
	 
	 
	 
 $this->show_menu();
 }
 private function show_menu(){ $this->pid=$this->_get('pid','intval')?$this->_get('pid','intval'):1;
 $where['level']=$this->_get('level','intval');
 $where['pid']=$this->pid;
 $title=rawurldecode($this->_get('title'));
 $where['status']=1;
 $where['display']=array('gt',0);
 $order['sort']='asc';
 $nav=M('Node')->where($where)->order($order)->select();
 

 
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
 
 //exit();
 $this->assign('title',$title);

 } }
 ?>