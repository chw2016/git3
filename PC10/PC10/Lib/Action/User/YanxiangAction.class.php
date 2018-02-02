<?php 
/**
 * @Author: zhang
 * @Date:   2015-01-07 15:17:46
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-01-21 11:41:35
 */

class YanxiangAction extends UserAction {
		public $token;
		public $openid;
		public $wxOpenNumber;
		public $userOpen;
		//public $mic;
		public function _initialize(){
			parent::_initialize();

			session('token',$_GET['token']);
			session('openid',$_GET['openid']);
			$this->token = session('token');
			// $this->openid = 'oqem0ju8x0YBHpDV_Bnvop8lk2is';
			$this->openid = session('openid');
			$this->assign('token',$this->token);
			$this->assign('openid',$this->openid);
			$this->wxOpenNumber = M('wxuser');
			//$this->mic = M('Yanxiang_Microimg');
			$this->userOpen = $this->wxOpenNumber->where(array('token'=>$this->token))->find();

		}
		// 首页显示
		public function index(){
			$db = M('Yanxiang_netposition');
			
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			foreach ($info as $key => $value) {
				$coculo = M('Yanxiang_province')->where(array('id'=>$value['uid']))->find();
				$info[$key]['uid'] = $coculo['province'];
                $info[$key]['fid'] = M('Yanxiang_fengqu')->where(array('id'=>$value['fid']))->getField('name');
			}
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			// echo $this->token;exit();
			$this->display();
		}
		// 研祥网店后台数据显示
		public function netSearchInner(){
			$db = M('Yanxiang_province');
			$getProvince = $db->select();
            $fengqu=M('Yanxiang_fengqu')->field('id,name')->where(array('id'=>$_GET['id']))->find();

            $this->assign('fengqu',$fengqu);
			$this->assign('province',$getProvince);
			// exit();
			$this->display();
		}
    //分区
    public function fengqu(){
        $list=M('Yanxiang_fengqu')->where(array('token'=>$this->token))->select();
        $this->assign('list',$list);
        $this->display();
    }
    //删除分区
    public function del_fengqu(){
        if(M('Yanxiang_fengqu')->where(array('id'=>$_GET['id']))->delete()){
            $this->success2('操作成功',U('fengqu',array('token'=>$this->token)));
        }else{
            $this->error2('操作失败',U('fengqu',array('token'=>$this->token)));
        }
    }
    //添加分区
    public function add_fengqu(){
        if(IS_POST){
          //  p($_POST);die;
            $_POST['token']=$this->token;
            if(isset($_POST['id'])&&$_POST['id']>0){//修改
                if(M('Yanxiang_fengqu')->where(array('id'=>$_POST['id']))->save(array('name'=>$_POST['name']))){
                    $this->success2('操作成功',U('fengqu',array('token'=>$this->token)));
                }else{
                    $this->error2('操作失败',U('fengqu',array('token'=>$this->token)));
                }
            }else{//新增
                if(M('Yanxiang_fengqu')->add($_POST)){
                    $this->success2('操作成功',U('fengqu',array('token'=>$this->token)));
                }else{
                    $this->error2('操作失败',U('fengqu',array('token'=>$this->token)));
                }
            }

        }else{
            $info=M('Yanxiang_fengqu')->where(array('id'=>$_GET['id']))->find();
            $this->assign('info',$info);
            $this->display();

        }
    }
		// 研祥网点添加后台数据接收
		public function netInnerAccept(){
			$db = M('Yanxiang_netposition');
			// 接收数据
			if (IS_POST) {
				$data = array(
                    'num'=>$_POST['num'],
                    'fid'=>$_POST['fid'],
					'uid' => $_POST['province'],
					'title' => $_POST['compname'],
					'address' => $_POST['compaddress'],
					'zipcode' => $_POST['zipcode'],
					'phone' => $_POST['telphone'],
					'fax' => $_POST['fax'],
					'img_path' => $_POST['imagepath'],
					'longitude' => $_POST['longitude'],
					'latitude' => $_POST['latitude'],
					'comType' => $_POST['compType']
				);
				
				if($db->data($data)->add()){
					$this->success('添加成功',U('Yanxiang/index',array('token'=>$this->token)));
				}else{
					$this->error('添加失败');
				}
			}
			
		}
		// 编辑网点	
		public function edits(){
			$id = $_GET['id'];
			$getResult = M('Yanxiang_netposition')->where(array('id'=>$id))->find();
           // p($getResult);
			$getProvince = M('Yanxiang_province')->where(array('id'=>$getResult['uid']))->find();
			$getAllProvince = M('Yanxiang_province')->select();
            //分区
            $fengqu=M('Yanxiang_fengqu')->field('id,name')->where(array('token'=>$this->token))->select();

            $this->assign('fengqu',$fengqu);
			$this->assign('result',$getResult);
			$this->assign('getAllProvince',$getAllProvince);
			$this->assign('res',$getProvince['province']);
			// exit();
			$this->display();
		}
		// 修改网点数据提交
		public function editData(){
			$db = M('Yanxiang_netposition');
			// 接收数据
			if (IS_POST) {
				$data = array(
                    'num'=>$_POST['num'],
                    'fid' => $_POST['fengqu'],
					'uid' => $_POST['province'],
					'title' => $_POST['compname'],
					'address' => $_POST['compaddress'],
					'zipcode' => $_POST['zipcode'],
					'phone' => $_POST['telphone'],
					'fax' => $_POST['fax'],
					'img_path' => $_POST['imagepath'],
					'longitude' => $_POST['longitude'],
					'latitude' => $_POST['latitude'],
					'comType' => $_POST['compType']
				);
				if($db->where(array('id'=>$_GET['id']))->save($data)){
					$this->success('修改成功',U('Yanxiang/index',array('token'=>$this->token)));
				}else{
					$this->error('修改失败');
				}
			}
		}
		// 删除数据
		public function del(){
			if (M('Yanxiang_netposition')->where(array('id'=>$_GET['id']))->delete()) {
				$this->success('删除成功',U('Yanxiang/index',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
		}
		// 新品上市
		public function newup(){
			$db = M('Yanxiang_newup');
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->assign('token',$_GET['token']);
			$this->display();
		}
		// 新品上市添加
		public function addnew(){
			$this->assign('token',$_GET['token']);
			$this->display();
		}
		// 新品上市数据接收
		public function Recaddnew(){
			$db = M('Yanxiang_newup');
			
			// 接收数据
			if (IS_POST) {
				$data = array(
					'title' => $_POST['pmodel'],
					'detail' => $_POST['pname'],
					'good_date' => $_POST['pdelivery'],
					'market_value' => $_POST['market'],
					'now_value' => $_POST['now'],
					'img_path' => $_POST['imagepath']
				);
				if($db->data($data)->add()){
					$this->success('添加成功',U('Yanxiang/newup',array('token'=>$_GET['token'])));
				}else{
					$this->error('添加失败');
				}
			}
		}
		// 新品编辑
		public function newedit(){
			$where = array();
			$where['id'] = $_GET['id'];
			$getResult = M('Yanxiang_newup')->where($where)->find();
			$this->assign('result',$getResult);
			$this->display();
		}
		// 修改数据的接收
		public function Recnewedit(){
			$db = M('Yanxiang_newup');
			
			// 接收数据
			if (IS_POST) {
				$data = array(
					'title' => $_POST['pmodel'],
					'detail' => $_POST['pname'],
					'good_date' => $_POST['pdelivery'],
					'market_value' => $_POST['market'],
					'now_value' => $_POST['now'],
					'img_path' => $_POST['imagepath']
				);
				$where['id'] = $_GET['id'];
				if($db->where($where)->save($data)){
					$this->success('修改成功',U('Yanxiang/newup',array('token'=>$_GET['token'])));
				}else{
					$this->error('修改失败');
				}
			}
		}
		// 新品上市删除数据
		public function newdel(){
			if (M('Yanxiang_newup')->where(array('id'=>$_GET['id']))->delete()) {
				$this->success('删除成功',U('Yanxiang/newup',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
		}
		// 产品通告页面
		public function cannoc(){
			$db = M('Yanxiang_annoc');
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->display();
		}
		// 添加产品通告页面
		public function addcannoc(){
			$this->display();
		}
		// 添加产品通告接收数据页面
		public function Reccannoc(){
			if(IS_POST){
				if(isset($_POST['type'])){
					$where['id'] = $_POST['id'];
					$data = array(
                        'pic' => $_POST['pic'],
						'title' => $_POST['title'],
						'explain' => $_POST['detail'],
						'content' => $_POST['content'],
						'tb_type' => $_POST['tbType'],
						'to' => $_POST['to'],
						'cc' => $_POST['cc']
					);
					if(M('Yanxiang_annoc')->where($where)->save($data)){
						$this->success('修改成功',U('Yanxiang/cannoc',array('token'=>$_GET['token'])));
					}else{
						$this->error('修改失败');
					}
				}else{
					$data = array(
                        'pic' => $_POST['pic'],
						'times' => date("Y-m-d"),
						'title' => $_POST['title'],
						'explain' => $_POST['detail'],
						'content' => $_POST['content'],
						'tb_type' => $_POST['tbType'],
						'to' => $_POST['to'],
						'cc' => $_POST['cc']
					);
					if(M('Yanxiang_annoc')->data($data)->add()){
						$this->success('添加成功',U('Yanxiang/cannoc',array('token'=>$_GET['token'])));
					}else{
						$this->error('添加失败');
					}
				}
				
			}
		}
		// 产品通告编辑页面
		public function editcannoc(){
			$id = $_GET['id'];
			$where['id'] = $id;
			$getResult = M('Yanxiang_annoc')->where($where)->find();
			$this->assign('result',$getResult);
			$this->display();
		}
		// 产品通告删除
		public function cannocdel(){
			if (M('Yanxiang_annoc')->where(array('id'=>$_GET['id']))->delete()) {
				$this->success('删除成功',U('Yanxiang/cannoc',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
		}
		// 研祥动态页面
		public function status(){
			$db = M('Yanxiang_status');
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->display();
		}
	    // 添加动态新闻
	    public function addstatus(){
	    	$this->display();
	    }
	    // 添加新闻动态页面数据接收的内容
	    public function Recstatus(){
	    	$db = M('Yanxiang_status');
	    	if (IS_POST) {
	    		if (isset($_POST['type'])) {
	    			$data = array(
						'title' => $_POST['newtitle'],
						'content' => $_POST['content']
					);
					$where['id'] = $_POST['id'];
					if($db->where($where)->save($data)){
						$this->success('修改成功',U('Yanxiang/status',array('token'=>$_GET['token'])));
					}else{
						$this->error('修改失败');
						// echo $where['id'];
					}
	    		}else{
			    	$data = array(
						'times' => date("Y-m-d"),
						'title' => $_POST['newtitle'],
						'content' => $_POST['content']
					);
					if($db->data($data)->add()){
						$this->success('添加成功',U('Yanxiang/status',array('token'=>$_GET['token'])));
					}else{
						$this->error('添加失败');
					}
	    		}
	    	}
	    	
	    }
	    // 动态新闻修改页面
	    public function editstatus(){
	    	$where['id'] = $_GET['id'];
	    	$getResult = M('Yanxiang_status')->where($where)->find();
	    	$this->assign('info',$getResult);
	    	$this->display();
	    }
	    // 删除新闻
	    public function delstatus(){
	    	$id = $_GET['id'];
	    	$where['id'] = $id;
	    	if(M('Yanxiang_status')->where($where)->delete()){
	    		$this->success('删除成功',U('Yanxiang/status',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
	    }
	    // 广告轮播图的添加
	    public function imgScroll(){
	    	$db = M('Yanxiang_imgscroll');
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
		
	    	$this->display();
	    }
	    // 新增以及编辑轮播图
	    public function aeimg(){
	    	if ($_GET['type'] == 'add') {
	    		$this->display();
	    	}else{
	    		// echo "1";
	    		$where['id'] = $_GET['id'];
	    		$getResult = M('Yanxiang_imgscroll')->where($where)->find();
	    		$this->assign('info',$getResult);
	    		$this->assign('hover',1);

	    		$this->display();
	    	}
	    	
	    }
	    // 新增加的轮播图的数据提交以及编辑
	    public function aeRec(){
	    	if (IS_POST) {
	    		if (isset($_POST['type'])) {
	    			$data = array(
	    					'img_path' => $_POST['img'],
	    					'link_url' => $_POST['url'],
	    					'type' => $_POST['diff'],
	    					'addtime' => date("Y-m-d")
	    				);
	    			if (M('Yanxiang_imgscroll')->data($data)->add()) {
	    				$this->success('添加成功',U('Yanxiang/imgScroll',array('token'=>$this->token)));
					}else{
						$this->error('添加失败');
					}
		        }else{
		        	$data = array(
	    					'img_path' => $_POST['img'],
	    					'link_url' => $_POST['url'],
	    					'type' => $_POST['diff']
	    				);
		        	$where['id'] = $_GET['id'];
	    			if (M('Yanxiang_imgscroll')->where($where)->save($data)) {
	    				$this->success('修改成功',U('Yanxiang/imgScroll',array('token'=>$this->token)));
					}else{
						$this->error('修改失败');
					}
		        }
	    	}
	    }
	    // 轮播图的删除
	    public function aedel(){
	    	if(M('Yanxiang_imgscroll')->where(array('id'=>$_GET['id']))->delete()){
	    		$this->success('删除成功',U('Yanxiang/imgScroll',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
	    } 
	    // 产品全库添加分类
	    public function procate(){
	    	$db = M('Yanxiang_catename');
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
	    	$this->display();
	    }
	    // 产品全库分类的增加与修改
	    public function aecate(){
	    	if ($_GET['type'] == 'add') {
	    		
	    	}else{
	    		$where['id'] = $_GET['uid'];
	    		$getResult = M('Yanxiang_catename')->where($where)->find();
	    		$this->assign('info',$getResult);
	    		$this->assign('hover',1);
	    	}
	    	$this->display();
	    }
	    // 产品全库分类数据的接收
	    public function aecateacc(){
	    	if (IS_POST) {

	    		if (isset($_POST['type'])) {
	    			$data = array(
	    					'catename' => $_POST['catename']
	    				);
	    			if (M('Yanxiang_catename')->data($data)->add()) {
	    				$this->success('添加成功',U('Yanxiang/procate',array('token'=>$this->token)));
					}else{
						$this->error('添加失败');
					}
		        }else{
		        	$data = array(
	    					'catename' => $_POST['catename']
	    				);
		        	$where['id'] = $_GET['id'];
	    			if (M('Yanxiang_catename')->where($where)->save($data)) {
	    				$this->success('修改成功',U('Yanxiang/procate',array('token'=>$this->token)));
					}else{
						$this->error('修改失败');
					}
		        }
	    	}
	    }
	    // 产品全库分类的删除
	    public function catedel(){
	    	if(M('Yanxiang_catename')->where(array('id'=>$_GET['uid']))->delete()){
	    		$this->success('删除成功',U('Yanxiang/procate',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
	    }
	    // 产品全库
	    public function allpro(){
	    	$this->display();
	    }
	    // 产品检索模块
	    public function cSearch(){
	    	$db = M('Yanxiang_csearch');
	    	$this->uid = $_GET['uid'];
			$count = $db->where(array('uid'=>$_GET['uid']))->count();
			$page = new Page($count,15);
			$info=$db->where(array('uid'=>$_GET['uid']))->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
	    	$this->display();
	    }
	    //产品添加以及修改
	   public function aepro(){
	   		if ($_GET['type'] == 'add') {
	    		$this->uid = $_GET['uid'];
	    	}else{
	    		$where['uid'] = $_GET['uid'];
	    		$where['id'] = $_GET['id'];
	    		$getResult = M('Yanxiang_csearch')->where($where)->find();
	    		$this->uid = $_GET['uid'];
	    		$this->assign('info',$getResult);
	    		$this->assign('hover',1);
	    	}
	    	$this->display();
	    }
	    // 产品数据的接收
	    public function aeproacc(){
	    	if (IS_POST) {
	    		if (isset($_POST['type'])) {
	    			$data = array(
	    					'img_path' => $_POST['img'],
	    					'type' => $_POST['proType'],
	    					'name' => $_POST['proName'],
	    					'addtime' => time(),
	    					'uid' => $_POST['uid'],
	    					'content' => $_POST['content']
	    				);
	    			if (M('Yanxiang_csearch')->data($data)->add()) {
	    				$this->success('添加成功',U('Yanxiang/cSearch',array('token'=>$this->token,'uid'=>$_POST['uid'])));
					}else{
						$this->error('添加失败');
					}
		        }else{
		        	$data = array(
	    					'img_path' => $_POST['img'],
	    					'name' => $_POST['proName'],
	    					'type' => $_POST['proType'],
	    					'content' => $_POST['content']
	    				);
		        	$where['id'] = $_GET['id'];
		        	$where['uid'] = $_POST['uid'];
	    			if (M('Yanxiang_csearch')->where($where)->save($data)) {
	    				$this->success('修改成功',U('Yanxiang/cSearch',array('token'=>$this->token,'uid'=>$_POST['uid'])));
					}else{
						$this->error('修改失败');
					}
		        }
	    	}
	    }
	    // 删除产品
	    public function aeprodel(){
	    	if (M('Yanxiang_csearch')->where(array('id'=>$_GET['id'],'uid'=>$_GET['uid']))->delete()) {
	    		$this->success('删除成功',U('Yanxiang/cSearch',array('token'=>$this->token,'uid'=>$_GET['uid'])));
			}else{
				$this->error('删除失败');
			}
	    }
    /*产品列表分类*/
    public function activeclass(){
        $db = M('Yanxiang_activeclass');
        $count = $db->count();
        $page = new Page($count,15);
        $info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->display();
    }

    /*添加、编辑产品分类*/
    public function setaecate(){
        if(IS_AJAX){
            $iTem = M('Yanxiang_activeclass')->where(array('id'=>$_POST['id']))->find();
            $_POST['add_time'] = date('Y-m-d H:i:s');
            $_POST['token'] = $this->token;
            if($iTem){
                $saveinfo = M('Yanxiang_activeclass')->where(array('id'=>$_POST['id']))->save($_POST);
                if($saveinfo){
                    $this->success('添加成功',U('Yanxiang/activeclass',array('token'=>$this->token)));
                }else{
                    $this->error('添加失败');
                }
            }else{
                if(M('Yanxiang_activeclass')->add($_POST)){
                    $this->success('添加成功',U('Yanxiang/activeclass',array('token'=>$this->token)));
                }else{
                    $this->error('添加失败');
                }
            }
        }else{
            $cid = $_GET['id'];
            if($cid){
                $info = M('Yanxiang_activeclass')->where(array('id'=>$cid))->find();
                $this->assign('info',$info);
            }
            $this->display();
        }

    }


	    // 产品列表
	    public function active(){
	    	$db = M('Yanxiang_active');
            $where = array('cid'=>$_GET['cid']);
			$count = $db->where($where)->count();
			$page = new Page($count,15);
			$info=$db->where($where)->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
	    	$this->display();
	    }
	    // 添加以及编辑活动
	    public function aeactive(){
	    	if ($_GET['type'] == 'add') {
	    		
	    	}else{
	    		$where['id'] = $_GET['id'];
	    		$getResult = M('Yanxiang_active')->where($where)->find();
	    		$this->assign('info',$getResult);
	    		$this->assign('hover',1);
	    	}
	    	$this->display();
	    }
	    // 添加以及编辑活动的数据接收
	    public function aeactacc(){
	    	if (IS_POST) {
	    		if (isset($_POST['type'])) {
	    			
	    			$data = array(
	    					'img_path' => $_POST['img'],
	    					'title' => $_POST['activetitle'],
	    					'content' => $_POST['content'],
	    					'ms' => $_POST['ms'],
	    					'addtime' => time(),
                            'cid' => $_GET['cid']
	    				);
	    			if (M('Yanxiang_active')->data($data)->add()) {
	    				$this->success('添加成功',U('Yanxiang/active',array('token'=>$this->token,'cid'=>$_GET['cid'])));
					}else{
						$this->error('添加失败');
					}
		        }else{
		        	$data = array(
	    					'img_path' => $_POST['img'],
	    					'title' => $_POST['activetitle'],
		        			'ms' => $_POST['ms'],
	    					'content' => $_POST['content']
	    				);
		        	$where['id'] = $_GET['id'];
	    			if (M('Yanxiang_active')->where($where)->save($data)) {
	    				$this->success('修改成功',U('Yanxiang/active',array('token'=>$this->token,'cid'=>$_GET['cid'])));
					}else{
						$this->error('修改失败');
					}
		        }
	    	}
	    }
	    // 删除活动数据
	    public function activedel(){
	    	if (M('Yanxiang_active')->where(array('id'=>$_GET['id']))->delete()) {
	    		$this->success('删除成功',U('Yanxiang/active',array('token'=>$this->token,'cid'=>$_GET['cid'])));
			}else{
				$this->error('删除失败');
			}
	    }
	    // 行业解决方案
	    public function industy(){
	    	$db = M('Yanxiang_industy');
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
	    	$this->display();
	    }
	    // 增加或者修改解决方案
	    public function aeindusty(){
	    	if ($_GET['type'] == 'add') {
	    		
	    	}else{
	    		$where['id'] = $_GET['id'];
	    		$getResult = M('Yanxiang_industy')->where($where)->find();
	    		$this->assign('info',$getResult);
	    		$this->assign('hover',1);
	    	}
	    	$this->display();
	    }
	    // 接收增加或者修改的数据
	    public function aeaccindusty(){
	    	if (IS_POST) {
	    		if (isset($_POST['type'])) {
	    			$data = array(
	    					'img_path' => $_POST['img'],
	    					'title' => $_POST['activetitle'],
	    					'content' => $_POST['content'],
	    					'addtime' => time(),
	    					'explain' => $_POST['explain']
	    				);
	    			if (M('Yanxiang_industy')->data($data)->add()) {
	    				$this->success('添加成功',U('Yanxiang/industy',array('token'=>$this->token)));
					}else{
						$this->error('添加失败');
					}
		        }else{
		        	$data = array(
	    					'img_path' => $_POST['img'],
	    					'title' => $_POST['activetitle'],
	    					'content' => $_POST['content'],
	    					'explain' => $_POST['explain']
	    				);
		        	$where['id'] = $_GET['id'];
	    			if (M('Yanxiang_industy')->where($where)->save($data)) {
	    				$this->success('修改成功',U('Yanxiang/industy',array('token'=>$this->token)));
					}else{
						$this->error('修改失败');
					}
		        }
	    	}
	    }
	    // 行业解决方案的删除
	    public function aeindustydel(){
	    	if (M('Yanxiang_industy')->where(array('id'=>$_GET['id']))->delete()) {
	    		$this->success('删除成功',U('Yanxiang/industy',array('token'=>$this->token)));
			}else{
				$this->error('删除失败');
			}
	    }
	    // 微活动列表
	    public function Microactive(){
	    	$db = M('Yanxiang_microimg');
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
	    	$this->display();
	    }

	    // 微活动列表添加以及修改
	    public function aeMicroactive(){
	    	if ($_GET['type'] == 'add') {
	    		
	    	}else{
	    		$where['id'] = $_GET['id'];
	    		$getResult = M('Yanxiang_microimg')->where($where)->find();
	    		$this->assign('info',$getResult);
	    		$this->assign('hover',1);
	    	}
	    	$this->display();
	    }
	    // 微活动大图片块接收数据
	   public function aeMicro(){
	    	if (IS_POST) {
	    		if (isset($_POST['type'])) {
	    			$data = array(
	    					'img_path' => $_POST['img'],
	    					'addtime' => date("Y-m-d H:i:s"),
	    					'token' => $_GET['token']
	    				);
	    			if (M('Yanxiang_microimg')->data($data)->add()) {
	    				$this->success('添加成功',U('Yanxiang/Microactive',array('token'=>$this->token)));
					}else{
						$this->error('添加失败');
					}
		        }else{
		        	$data = array(
	    					'img_path' => $_POST['img']
	    				);
		        	$where['id'] = $_GET['id'];
	    			if (M('Yanxiang_microimg')->where($where)->save($data)) {
	    				$this->success('修改成功',U('Yanxiang/Microactive',array('token'=>$this->token)));
					}else{
						$this->error('修改失败');
					}
		        }
	    	}
	    }
	     // 进入到子列表
	    public function ChildImg(){	
	    	$id = $this->_get('uid','intval');
	    	
	    	$db = M('Yanxiang_microinner');
			$count = $db->count();
			$page = new Page($count,15);
			$info=$db->order('id')->limit($page->firstRow.','.$page->listRows)->where(array('uid'=>$id))->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->assign('uid',$id);
	    	$this->display();
	    }
		// 子列表的添加以及编辑
	    public function aeChildImg(){
	    	if ($_GET['type'] == 'add') {
	    		
	    	}else{
	    		$where['id'] = $_GET['id'];
	    		$getResult = M('Yanxiang_microinner')->where($where)->find();
	    		$this->assign('info',$getResult);
	    		$this->assign('hover',1);
	    	}
	    	$uid = $this->_get('uid','intval');
		$token = $this->_get('token');
	    
	    	$this->assign('uid',$uid);
	    	$this->display();
	    }
	    // 接收数据
	    public function childacc(){
	    	if (IS_POST) {
	    		if (isset($_POST['type'])) {
	    			$data = array(
	    					'img_path' => $_POST['img'],
	    					'a_name' => $_POST['activename'],
	    					'link_url' => $_POST['activelink'],
	    					'uid' => $_POST['uid'],
	    					'token' => $_GET['token']
	    				);
	    			if (M('Yanxiang_microinner')->data($data)->add()) {
	    				$this->success('添加成功',U('Yanxiang/ChildImg',array('token'=>$this->token,'uid'=>$data['uid'])));
					
					}else{
						$this->error('添加失败');
					}
		        }else{
		        	$data = array(
	    					'img_path' => $_POST['img'],
	    					'a_name' => $_POST['activename'],
	    					'link_url' => $_POST['activelink']
	    				);
		        	$where['id'] = $_GET['id'];
		        	$uid = $_POST['uid'];
	    			if (M('Yanxiang_microinner')->where($where)->save($data)) {
	    				$this->success('修改成功',U('Yanxiang/ChildImg',array('token'=>$this->token,'uid'=>$uid)));
					
					}else{
						$this->error('修改失败');
					}
		        }
	    	}
	    }
	    // 子元素的删除
	    public function aeChildImgdel(){
	    	$id = $_GET['id'];
	    	$uid = $_GET['uid'];
	    	if (M('Yanxiang_microinner')->where(array('id'=>$id))->delete()) {
	    		$this->success('删除成功',U('Yanxiang/ChildImg',array('token'=>$this->token,'uid'=>$uid)));
	    	}else{
	    		$this->error('删除失败');
	    	}
	    }
	    // 外面大的删除掉
	    public function aeMicrodel(){
	    	$id = $this->_get('id');
	    	// 首先删除掉大表
	    	$resultDel1 = M('Yanxiang_microimg')->where(array('id'=>$id))->delete();
	    	// 然后删除掉小表，批量删除
	    	$resultDel2 = M('Yanxiang_microinner')->where(array('uid'=>$id))->delete();
	    	if ($resultDel1 && $resultDel2) {
	    		$this->success('删除成功',U('Yanxiang/Microactive',array('token'=>$this->token)));
	    	}else{
	    		$this->error('删除失败');
	    	}
	    }
	}
?>