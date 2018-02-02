<?php 
/*
	减肥达人后台
	PC端
	@author 老张
*/
	class ReduceAction extends UserAction {
		public $token;
		public $openid;
		public $wxOpenNumber;
		public $userOpen;
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
			$this->userOpen = $this->wxOpenNumber->where(array('token'=>$this->token))->find();
		}
		// 首先是显示所有的一个整体的界面
		public function index(){
			$this->display();
		}
		// 文章页面的添加
		public function article(){
			$this->display();
		}
		// 文章页面数据的提交
		public function articleInsert(){
			if (IS_POST) {
				$data = array(
						'title' => $this->_post('articleT'),
						'description' => $this->_post('articleJ'),
						'contentitle' => $this->_post('articleC'),
						'contentime' => date('Y-m-d'),
						'contentopen' => $this->userOpen['name'],
						'content' => $this->_post('newscontent'),
						'contentimg' => $this->_post('pic')
					);
				if (M('Reduce_losenews')->add($data)) {
					$this->success("提交成功",U(MODULE_NAME.'/index',array('token'=>$this->token)));
            	}else{
                	$this->error("提交失败",U(MODULE_NAME.'/index'));
            	}
			}
		}
		// 文章列表显示
		public function articlelist(){
			$info = M('Reduce_losenews')->select();
			$count = M('Reduce_losenews')->count();
			$page = new Page($count,15);
			$info = M('Reduce_losenews')->order('sorts,id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->display();
		}
		// 文章编辑
		public function articleedit(){

		}
		// 文章删除
		public function articledel(){

		}
		// 文章查看
		public function articlelookup(){
			
		}
		// 用户基本减肥数据查询
		public function baseDatalookUp(){
			$where = array();
			$bsale = isset($_POST['bsale']) ? $_POST['bsale'] : '';
			// 体重范围变化
			$weightMin = isset($_POST['weightMin']) ? $_POST['weightMin'] : '';
			$weightMax = isset($_POST['weightMax']) ? $_POST['weightMax'] : '';
			// BMI 值的变化
			$BMIMin = isset($_POST['BMIMin']) ? $_POST['BMIMin'] : '';
			$BMIMax = isset($_POST['BMIMax']) ? $_POST['BMIMax'] : '';
			// 根据四种状况来查询
			if($bsale){
				$where['health'] = $bsale;
		
			}
			if ($weightMin && $weightMax) {
				$where['weight'] = array('between',array($weightMin.'kg',$weightMax.'kg'));
			}
			if ($BMIMin && $BMIMax) {
				$where['BMI'] = array('between',array($BMIMin,$BMIMax));
			}
			// 首先获取微信号
			$wxuser = $this->userOpen;
			$wxid = $wxuser['id'];
			$db = M('wxusers');
			$result = $db->where(array('uid'=>$wxid))->select();
			$dbs = M('Reduce_custom');
			// 如果有的话按照条件查询，如果没有的话就直接查询
			$count = $dbs->where($where)->count();
			
			$page = new Page($count,15);
			$info = $dbs->where($where)->order('id')->limit($page->firstRow.','.$page->listRows)->select();
			foreach ($info as $key => $value) {
				foreach($result as $k => $v){
					if ($value['openid'] == $v['openid']) {
						$info[$key]['nikename'] = $v['nickname'];
					}
				}
			}
			
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->assign('bsale',$bsale);
			$this->assign('weightMin',$weightMin);
			$this->assign('weightMax',$weightMax);
			$this->assign('BMIMin',$BMIMin);
			$this->assign('BMIMax',$BMIMax);

			$this->display();
		}
		// 方案定制数据查询
		public function slookUp(){
			$where = array();
			$dateMin = isset($_POST['dateMin']) ? strtotime($_POST['dateMin']) : strtotime(date("Y-m-d",strtotime("-6 day"))." 00:00:00");
			$dateMax = isset($_POST['dateMax']) ? strtotime($_POST['dateMax']) : strtotime(date("Y-m-d")." 23:59:59");
			$count = M('Reduce_custom')->where(array('join_date'=>date("Y-m-d",strtotime("-1 day"))))->count();

			$datelist = self::timeList($dateMin,$dateMax);
			$coutsarr = array();
            $dur_counts = 0;
			foreach ($datelist['dayList'] as $value) {
				$temparr = array();
				$temparr['date'] = $value;
				$temparr['count'] =  M('Reduce_custom')->where(array('join_date'=>$value))->count();
                $dur_counts +=$temparr['count'];
				$coutsarr[] = $temparr;
			}
			

			$this->assign('count',$count);
			$this->assign('coutsarr',$coutsarr);
			$this->assign('dur_counts',$dur_counts);
			$this->assign('dateMin',$_POST['dateMin']);
			$this->assign('dateMax',$_POST['dateMax']);
			$this->display();
		}

		// 减重记录总的查询
		public function loseRecord(){
			$where = array();
            $dateMin = isset($_POST['dateMin']) ? strtotime($_POST['dateMin']) : strtotime(date("Y-m-d",strtotime("-6 day"))." 00:00:00");
            $dateMax = isset($_POST['dateMax']) ? strtotime($_POST['dateMax']) : strtotime(date("Y-m-d")." 23:59:59");
			$where['addtime'] = date("Y-m-d",strtotime("-1 day"));
			$where['foodname']=array('neq','');
			$count = M('Reduce_energy')->where($where)->count();

            $datelist = self::timeList($dateMin,$dateMax);
			$coutsarr = array();
            $dur_counts = 0;
			foreach ($datelist['dayList'] as $value) {
				$temparr = array();
				$temparr['date'] = $value;
				$temparr['count'] =  M('Reduce_energy')->where(array('addtime'=>$value,'foodname'=>array('neq','')))->count();
                $dur_counts +=$temparr['count'];
				$coutsarr[] = $temparr;
			}

			$this->assign('count',$count);
			$this->assign('coutsarr',$coutsarr);
            $this->assign('dur_counts',$dur_counts);
            $this->assign('dateMin',$_POST['dateMin']);
            $this->assign('dateMax',$_POST['dateMax']);
			$this->display();
		}

        /*
         * 原因分析
         */
        public function loseanalysis(){
            $where = array();
            $dateMin = isset($_POST['dateMin']) ? strtotime($_POST['dateMin']) : strtotime(date("Y-m-d",strtotime("-6 day"))." 00:00:00");
            $dateMax = isset($_POST['dateMax']) ? strtotime($_POST['dateMax']) : strtotime(date("Y-m-d")." 23:59:59");
            $where['date'] = date("Y-m-d",strtotime("-1 day"));
            $count = M('Reduce_fatanalysis')->where($where)->count();

            $datelist = self::timeList($dateMin,$dateMax);
            $coutsarr = array();
            $dur_counts = 0;
            foreach ($datelist['dayList'] as $value) {
                $temparr = array();
                $temparr['date'] = $value;
                $temparr['count'] =  M('Reduce_fatanalysis')->where(array('date'=>$value))->count();
                $dur_counts +=$temparr['count'];
                $coutsarr[] = $temparr;
            }

            $this->assign('count',$count);
            $this->assign('coutsarr',$coutsarr);
            $this->assign('dur_counts',$dur_counts);
            $this->assign('dateMin',$_POST['dateMin']);
            $this->assign('dateMax',$_POST['dateMax']);
            $this->display();
        }


        /*
         * 原因分析
         */
        public function loserecorddata(){
            $where = array();
            $dateMin = isset($_POST['dateMin']) ? strtotime($_POST['dateMin']) : strtotime(date("Y-m-d",strtotime("-6 day"))." 00:00:00");
            $dateMax = isset($_POST['dateMax']) ? strtotime($_POST['dateMax']) : strtotime(date("Y-m-d")." 23:59:59");
            $where['date'] = date("Y-m-d",strtotime("-1 day"));
            $count = M('Reduce_loserecord')->where($where)->count();

            $datelist = self::timeList($dateMin,$dateMax);
            $coutsarr = array();
            $dur_counts = 0;
            foreach ($datelist['dayList'] as $value) {
                $temparr = array();
                $temparr['date'] = $value;
                $temparr['count'] =  M('Reduce_loserecord')->where(array('date'=>$value))->count();
                $dur_counts +=$temparr['count'];
                $coutsarr[] = $temparr;
            }

            $this->assign('count',$count);
            $this->assign('coutsarr',$coutsarr);
            $this->assign('dur_counts',$dur_counts);
            $this->assign('dateMin',$_POST['dateMin']);
            $this->assign('dateMax',$_POST['dateMax']);
            $this->display();
        }

		// 
		static public function timeList($beginTimeStamp,$endTimeStamp){
	        if(!is_numeric($beginTimeStamp)||!is_numeric($endTimeStamp)||($endTimeStamp<=$beginTimeStamp)) return '';
	        $tmp=array();
	        for($i=$beginTimeStamp;$i<=$endTimeStamp;$i+=(24*3600)){
	            $tmp['timeStampList'][]=$i;
	            $tmp['dayList'][]=date('Y-m-d',$i);
	        }
	        return $tmp;
    	}

        public function loserecordview(){
            $openid = $_GET['openid'];
            $dateMin = isset($_POST['dateMin']) ? date("Y-m-d",strtotime($_POST['dateMin'])) : date("Y-m-d",strtotime("-15 day"));
            $dateMax = isset($_POST['dateMax']) ? date("Y-m-d",strtotime($_POST['dateMax'])) : date("Y-m-d");

            $where['openid'] = $openid;
            $where['token'] = $this->token;
            $where['addtime'] = array('between',array($dateMin,$dateMax));
            $db = M('Reduce_energy');
            $count=$db->where($where)->count();
            $page=new Page($count,50);
            $info=$db->where($where)->field('addtime')->limit($page->firstRow.','.$page->listRows)->group('addtime')->order('addtime asc')->select();
            if($info){
                foreach($info as $key=>$val){
                    $tempdata = array();
                    $tempdata =  $db->where(array('openid'=>$openid,'token'=>$this->token,'addtime'=>$val['addtime']))->select();
                    $str = '';
                    foreach($tempdata as $k=>$v){
                        $str .= '';
                        if($v['type'] == 'zao'){
                            $type = '早餐';
                        }else if($v['type'] == 'zhong'){
                            $type = '中餐';
                        }else if($v['type'] == 'wan'){
                            $type = '晚餐';
                        }else if($v['type'] == 'jia'){
                            $type = '加餐';
                        }else if($v['type'] == 'yun'){
                            $type = '运动';
                        }
                        $str .=$type."(";
                        if($v['foodname'] != ''){
                            $tdata = json_decode($v['foodname']);
                            foreach($tdata as $vs){
                                $t = explode(',',$vs);
                                $str .= $t[0]." 热量: ".$t[1].'卡路里 '.$t[2].'g';
                            }
                        }else{
                            $str.="未添加";
                        }
                        $str .=")<br>";
                    }

                    $info[$key]['foodname'] = $str;
                }
            }

            $this->assign('page',$page->show());
            $this->assign('info',$info);
            $this->assign('dateMin',$dateMin);
            $this->assign('dateMax',$dateMax);
            $this->assign('info',$info);
            $this->display();

        }

        public function weightview(){
            $openid = $_GET['openid'];
            $dateMin = isset($_POST['dateMin']) ? date("Y-m-d",strtotime($_POST['dateMin'])) : date("Y-m-d",strtotime("-30 day"));
            $dateMax = isset($_POST['dateMax']) ? date("Y-m-d",strtotime($_POST['dateMax'])) : date("Y-m-d");

            $where['openid'] = $openid;
            $where['token'] = $this->token;
            $where['date'] = array('between',array($dateMin,$dateMax));
            $db = M('Reduce_loserecord');
            $count=$db->where($where)->count();
            $page=new Page($count,15);
            $info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('date asc')->select();

            $this->assign('page',$page->show());
            $this->assign('info',$info);
            $this->assign('dateMin',$dateMin);
            $this->assign('dateMax',$dateMax);
            $this->assign('info',$info);
            $this->display();


        }


	}
?>