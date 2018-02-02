<?php
/**
 * @Author: zhang
 * @Date:   2015-01-30 14:09:48
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-02-28 09:54:44
 */
class AibangAction extends UserAction{
	public $token;
	public $openid;
	static public $treeList = array(); 
	// 初始化
	public function _initialize(){
		parent::_initialize();

	}
	// 主页显示，加上链接显示
	public function index(){
		$this->display();
	}
	// 添加城市
	public function cityAdd(){
		$db = M('Aibang_city');
		$count = $db->count();
		$page = new Page($count,15);
		$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	// 增加的页面以及修改的页面显示，增加的页面以及修改页面的数据的提交
	public function aeCity(){
		if (IS_POST) {
			// 通过ajax提交的数据，下面是展示页面
			if (isset($_POST['types'])) {
				$data = array(
	    					'city' => $_POST['cityname']
	    				);
	    			if (M('Aibang_city')->data($data)->add()) {
	    				$this->success('添加成功',U('Aibang/cityAdd',array('token'=>$this->token)));
					}else{
						$this->error('添加失败');
					}
			}else{
				$data = array(
	    					'city' => $_POST['cityname']
	    				);
		        	$where['id'] = $_GET['id'];
	    			if (M('Aibang_city')->where($where)->save($data)) {
	    					$this->success('修改成功',U('Aibang/cityAdd',array('token'=>$this->token)));
					}else{
						$this->error('修改失败');
					}
			}
		}else{
			if ($_GET['type'] == "add") {
				// 添加数据页面显示
				// 不做任何事情
			}else{
				// 修改数据页面显示，获取城市的ID
				$where['id'] = $_GET['id'];
		    		$getResult = M('Aibang_city')->where($where)->find();
		    		$this->assign('info',$getResult);
		    		$this->assign('hover',1);
			}
			$this->display();
		}
	}
	// 删除添加的城市
	public function delCity(){
		if (M('Aibang_city')->where(array('id'=>$_GET['id']))->delete() && M('Aibang_zone')->where(array('uid'=>$_GET['id']))->delete()) {
			$this->success('删除成功',U('Aibang/cityAdd',array('token'=>$this->token)));
		}else{
			$this->error('删除失败');
		}
	}
	// 区域的添加
	public function addZone(){
		$uid = $this->_get('uid','intval');
		$db = M('Aibang_zone');
		$count = $db->where(array('uid'=>$uid))->count();
		$page = new Page($count,15);
		$info=$db->order('id')->where(array('uid'=>$uid))->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->assign('uid',$uid);
		$this->display();
	}
	// 
	public function aeZone(){
		if (IS_POST) {
			// 通过ajax提交的数据，下面是展示页面
			if (isset($_POST['types'])) {
				$uid = $this->_post('uid','intval');
				$data = array(
	    					'zone' => $_POST['zonename'],
	    					'uid' => $uid
	    				);
	    			if (M('Aibang_zone')->data($data)->add()) {
	    				$this->success('添加成功',U('Aibang/addZone',array('token'=>$this->token,'uid'=>$uid)));
					}else{
						$this->error('添加失败');
					}
			}else{
				$data = array(
	    					'zone' => $_POST['zonename']
	    				);
					$where['uid'] = $this->_post('uid','intval');
		        	$where['id'] = $_GET['id'];
	    			if (M('Aibang_zone')->where($where)->save($data)) {
	    					$this->success('修改成功',U('Aibang/addZone',array('token'=>$this->token,'uid'=>$where['uid'])));
					}else{
						$this->error('修改失败');
					}
			}
		}else{
			if ($_GET['type'] == "add") {
				$uid = $this->_get('uid','intval');
				$this->assign('uid',$uid);
			}else{
				// 修改数据页面显示，获取城市的ID
					$uid = $this->_get('uid','intval');
					$this->assign('uid',$uid);
					$where['id'] = $_GET['id'];
		    		$getResult = M('Aibang_zone')->where($where)->find();
		    		$this->assign('info',$getResult);
		    		$this->assign('hover',1);
			}
			$this->display();
		}
	}
	// delete
	public function delZone(){
		if (M('Aibang_zone')->where(array('id'=>$_GET['id'],'uid'=>$_GET['uid']))->delete()) {
			$this->success('删除成功',U('Aibang/addZone',array('token'=>$this->token,'uid'=>$_GET['uid'])));
		}else{
			$this->error('删除失败');
		}
	}
	// 添加分类
	public function cateAdd(){
		$db = M('Aibang_cate');
		$count = $db->where(array('pid'=>0))->count();
		$getD=$db->order('pid')->select();
		$data = self::tree($getD);
		$this->assign('data',$data);
		$page = new Page($count,15);
		$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	// 增、改、提交、修改提交
	public function aeCate(){
		if (IS_POST) {
			// 通过ajax提交的数据，下面是展示页面
			if (isset($_POST['types'])) {
				$data = array(
	    					'cate' => $_POST['catename'],
	    					'cate_img' => $_POST['img'],
	    					'pid' => $_POST['pid']
	    				);
	    			if (M('Aibang_cate')->data($data)->add()) {
	    				$this->success('添加成功',U('Aibang/cateAdd',array('token'=>$this->token)));
					}else{
						$this->error('添加失败');
					}
			}else{
				$data = array(
	    					'cate' => $_POST['catename'],
	    					'cate_img' => $_POST['img'],
	    					'pid' => $_POST['pid']
	    				);
		        	$where['id'] = $_GET['id'];
	    			if (M('Aibang_cate')->where($where)->save($data)) {
	    					$this->success('修改成功',U('Aibang/cateAdd',array('token'=>$this->token)));
					}else{
						$this->error('修改失败');
					}
			}
		}else{
			if ($_GET['type'] == "add") {
				$getD=M('Aibang_cate')->order('pid')->select();
				$data = self::tree($getD);
				$this->assign('data',$data);
			}else{
				// 修改数据页面显示，获取城市的ID
				$where['id'] = $_GET['id'];
	    		$getResult = M('Aibang_cate')->where($where)->find();
	    		$this->assign('info',$getResult);
	    		$this->assign('res',$getResult['pid']);
	    		$this->assign('hover',1);
		    	$getD=M('Aibang_cate')->order('pid')->select();
				$data = self::tree($getD);
				$this->assign('data',$data);
			}
			$this->display();
		}
	}
	// 删除分类
	public function delCate(){
		// 加个判断是否是最后的删除或者不是最后的删除，如果有子分类的话
		if (M('Aibang_cate')->where(array('pid'=>$_GET['id']))->select()) {
			if (M('Aibang_cate')->where(array('id'=>$_GET['id']))->delete() && M('Aibang_cate')->where(array('pid'=>$_GET['id']))->delete()) {
				$this->success('删除成功',U('Aibang/cateAdd',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
		}else{
			// 没有子分类的情况下
			if (M('Aibang_cate')->where(array('id'=>$_GET['id']))->delete()) {
				$this->success('删除成功',U('Aibang/cateAdd',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
		}
		
	}
	// 设置一个无限级分类
	static public function tree(&$data,$pid = 0,$count = 1) {
        foreach ($data as $key => $value){
            if($value['pid']==$pid){
                $value['Count'] = $count;
                self::$treeList []=$value;
                unset($data[$key]);
                self::tree($data,$value['id'],$count+1);
            } 
        }
        return self::$treeList ;
    }
    // 审核流程
    public function examine(){
    	$db = M('Aibang_register');
    	if (isset($_GET['checked'])) {
    		// 查找已审核的商户
    		$count = $db->where(array('status'=>1))->count();
    		$this->assign('flag',1);
		
		$page = new Page($count,15);
		$info=$db->where(array('status'=>1))->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
    	}else{
    		// 查找未审核的商户
		$count = $db->where(array('status'=>0))->count();
		$page = new Page($count,15);
		$info=$db->where(array('status'=>0))->limit($page->firstRow.','.$page->listRows)->select();
    	}
    		$this->assign('page',$page->show());
		$this->assign('info',$info);	
    		$this->display();
    }
    // 审核操作
    public function checked(){
    	$id = $this->_get('id','intval');
    	$regModel = M('Aibang_register');
    	$data = array(
    			'status' => 1
    		);
    	$result = $regModel->where(array('id'=>$id))->save($data);
    	$notichcontent = '尊敬的用户，您的商铺已通过审核，可以查看您的商铺了';
    	$openid = $regModel->where(array('id'=>$id))->getField('openid');		
		$postdata = array('openid'=>$openid,'token'=>$this->token,'content'=>$notichcontent);
		$url =C('site_url')."index.php?g=Home&m=Auth&a=sendTextMsg";//通过这样执行的
		$data = $this->api_notice_increment($url,http_build_query($postdata));
	if ($result) {
    		$this->redirect('Aibang/examine', array('token' => $this->token));
    	}
    }

    //添加店铺
    public function addStore(){
        if(IS_POST) {
            $_POST['uid'] = 40;
            $_POST['token'] = '3818246739b8703ffc685690b2002a17';
            $_POST['openid'] = 'openid';
            $_POST['zone'] = '中心广场';
            $_POST['reg_time'] = date("Y-m-d",time());
            $regModel = M('Aibang_register');
            if($regModel->add($_POST)){
               $this->success2('操作成功');
            }else{
                $this->success2('操作失败');
            }
        }else{
            $this->display();
        }
    }

    /*删除*/

    public function delshop(){
        $regModel = M('Aibang_register');
        if($regModel->where(array('id'=>$_GET['id']))->delete()){
            $this->success2('操作成功');
        }else{
            $this->error2('操作失败');
        }
    }


    public function editStore(){
        $regModel = M('Aibang_register');
        if(IS_POST) {
            $id = $_POST['id'];
            unset($_POST['id']);
            if($regModel->where(array('id'=>$id))->save($_POST)){
                $this->success2('操作成功');
            }else{
                $this->success2('操作失败');
            }
        }else{
            $data = $regModel->where(array('id'=>$_GET['id']))->find();
            $this->assign('data',$data);
            $this->display();
        }
    }
}