<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 前端
 */
class CmsZlmAction extends BaseAction{
	public $_sTplBaseDir = 'Wap/default';
	public $_html = '.html';
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		
		
		
	}

	// 首页显示，店铺发送
	public function index(){
		if(IS_AJAX){
			$lm=M('Cms_lm')->where(array('token'=>$_GET['token'],'id'=>$_GET['lmid']))->find();
		    $n=$_POST['n']*$lm['page']; 
			$list = M('Cms_zlm')->where(array('token'=>$_GET['token'],'uid'=>$_GET['lmid'],'is_show'=>1))->limit($n,$lm['page'])->order("sort")->select();
			$this->assign('list',$list);
			$lm['list']=$lm['list']."_ajax";
			$x = $this->fetch(sprintf('%s%s/%s/%s%s', TMPL_PATH, $this->_sTplBaseDir, $lm['xm'],$lm['list'], $this->_html),$list);
			exit($x); 
		}else{
			$lm=M('Cms_lm')->where(array('token'=>$_GET['token'],'id'=>$_GET['lmid']))->find();
			$list = M('Cms_zlm')->where(array('token'=>$_GET['token'],'uid'=>$_GET['lmid'],'is_show'=>1))->limit(0,$lm['page'])->order("sort")->select();
		    $this->assign('list',$list);
		    $this->display(sprintf('%s%s/%s/%s%s', TMPL_PATH, $this->_sTplBaseDir, $lm['xm'],$lm['list'], $this->_html));
		} 
	}
	
	
	public function show(){
		$lm=M('Cms_lm')->where(array('id'=>$_GET['lmid']))->find();
		$list=M('Cms_zlm')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list);
        $lm['show']=$lm['list'].'_'.$lm['show'];
		$this->display(sprintf('%s%s/%s/%s%s', TMPL_PATH, $this->_sTplBaseDir, $lm['xm'],$lm['show'], $this->_html));
	}

	
}
?>
