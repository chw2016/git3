<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 前端
 */
class YxAction extends BaseAction{
	
	public $_sTplBaseDir = 'Wap/default/yx';
	
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		$this->dt =  M('Yanxiang_status'); 
		
		
	}
	public function dt(){
		
		$count      = $this->dt->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $this->dt->limit($Page->firstRow.','.$Page->listRows)->order("sort, id desc")->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		
		$this->UDisplay();
	}
	public function dtshow(){
		$getResult=$this->dt->where(array('id'=>$_GET['id']))->find();
		$this->assign('info',$getResult);
		$this->UDisplay();
	}
	
	public function zp(){
		
		
		$count      = M('yanxiang_zp')->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = M('yanxiang_zp')->limit($Page->firstRow.','.$Page->listRows)->order("sort")->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		
		$lb=M('yanxiang_lb')->where(array('type'=>'微招聘'))->order("sort")->select();
		$this->assign('lb',$lb);
	
		$this->UDisplay();
	}
	public function zpshow(){
		
		$getResult=M('yanxiang_zp')->where(array('id'=>$_GET['id']))->find();
		
		$this->assign('info',$getResult);
		$this->UDisplay();
		
	}

	
	public function ck(){
		$list=M('yanxiang_ck')->limit(4)->order('sort desc')->select();
		$this->assign('list',$list);
		//p($list);

		
		$this->UDisplay();
	}
    public function ck_info(){

        $list=M('Yanxiang_ck2')->where(array('uid'=>$_GET['id']))->select();
        $this->assign('list',$list);

        $flash=M('Yanxiang_ck3')->where(array('uid'=>$_GET['id']))->select();
        $this->assign('flash',$flash);

        $this->UDisplay();
    }

    //看研祥加载更多
    public function ck_kan(){
        $count      = M('Yanxiang_ck2')->order("times desc")->count();
        $Page       = new Page($count,5);
        $show       = $Page->show();
        $list = M('Yanxiang_ck2')->limit($Page->firstRow.','.$Page->listRows)->order("times desc")->select();
      //  $list=M('Yanxiang_ck2')->order('times desc')->select();
       // p($list);
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->UDisplay();
    }
    public function content2(){
        $info=M('Yanxiang_ck2')->find($_GET['id']);
        $this->assign('res',$info);
        // p($info);
        $this->UDisplay();
    }
	
	public function ckshow(){
		/* $getResult=M('yanxiang_ck2')->where(array('uid'=>$_GET['id']))->select();
		$this->assign('info',$getResult); */
		
		$count      = M('yanxiang_ck2')->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = M('yanxiang_zp')->limit($Page->firstRow.','.$Page->listRows)->order("sort")->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		
		$lb=M('yanxiang_lb')->where(array('type'=>'看研详'))->order("sort")->select();
		$this->assign('lb',$lb);
		
		$this->UDisplay();
	}
	
	
	public function ckshow2(){
		$getResult=M('yanxiang_ck2')->where(array('id'=>$_GET['id']))->find();
		$this->assign('info',$getResult);
		$this->UDisplay();
	}
	
	
	public function zx(){
		/* $list=M('yanxiang_zx')->select();
		$this->assign('list',$list); */
		
		$count      = M('yanxiang_zx')->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = M('yanxiang_zx')->limit($Page->firstRow.','.$Page->listRows)->order("sort")->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		
		$lb=M('yanxiang_lb')->where(array('type'=>'市场资讯'))->order("sort")->select();
		$this->assign('lb',$lb);
		
		$this->UDisplay();
	}
	
	public function zxshow(){
		$getResult=M('yanxiang_zx')->where(array('id'=>$_GET['id']))->find();
		$this->assign('info',$getResult);
		$this->UDisplay();
	}
	
	
	public function xb(){
	/* 	$list=M('yanxiang_xb')->select();
		$this->assign('list',$list); */
		
		
		$count      = M('yanxiang_xb')->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = M('yanxiang_xb')->limit($Page->firstRow.','.$Page->listRows)->order("sort")->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		//p($list);
		
		$this->UDisplay();
	}
	public function content(){
        $info=M('yanxiang_xb')->find($_GET['id']);
        $this->assign('res',$info);
       // p($info);
        $this->UDisplay();
    }

	public function xbshow(){
		$getResult=M('yanxiang_xb')->where(array('id'=>$_GET['id']))->find();
		$this->assign('info',$getResult);
		$this->UDisplay();
	}
	
	public function rd(){
/* 		$list=M('yanxiang_rd')->select();
		$this->assign('list',$list);
		 */
		
		$count      = M('yanxiang_rd')->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = M('yanxiang_rd')->limit($Page->firstRow.','.$Page->listRows)->order("sort")->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
     //   p($list);
		
		$this->UDisplay();
	}
	
	public function rdshow(){
		$getResult=M('yanxiang_rd')->where(array('id'=>$_GET['id']))->find();
		//P($getResult);die;
		$this->assign('info',$getResult);
		$this->UDisplay();
	}
	
}
?>
