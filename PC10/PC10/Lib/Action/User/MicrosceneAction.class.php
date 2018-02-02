<?php 
	// 后台页面
	class MicrosceneAction extends UserAction {
		//定义token
		public $token;
		public $db;
		public $dbp;
		public $uid;
		public $Allid;
		// 初始化
		public function _initialize() {
			parent::_initialize();
			$token = session('token');
			$this->token = $token;
			$this->db = M('Scene');
			$this->dbp = M('scene_p');
			$this->uid = session('uid');
		}
		// 主页
		public function light(){
			$result = $this->dbp->where(array('token'=>$this->token,'uid'=>$this->uid))->select();
			$this->assign('info',$result);
			$this->display();
		}
		//主页内容的保存
		public function padd(){
			if(IS_POST){
            	$_POST['uid'] = $this->uid;
            	$_POST['token'] = $this->token;
            	$_POST['addtime'] = time();
            	if($this->dbp->add($_POST)){
                	$this->success('操作成功',U(MODULE_NAME.'/light',array('token'=>$this->token)));
            	}else{
                	$this->error('操作失败',U(MODULE_NAME.'/padd'));
            	}
        	}
		}
		//主页编辑
		public function editp(){
			$result = $this->dbp->where(array('id'=>$this->_get('pid','intval')))->find();
			$this->assign('pid',$this->_get('pid','intval'));
			$this->assign('res',$result);
			$this->display();
		}
		//主页保存
		public function psaves(){
			if(IS_POST){
            	$_POST['uid'] = $this->uid;
            	$_POST['token'] = $this->token;
            	$_POST['addtime'] = time();
            	if($this->dbp->where(array('id'=>$this->_get('pid','intval'),'token'=>$this->token))->save($_POST)){
                	$this->success('操作成功',U(MODULE_NAME.'/light',array('token'=>$this->token,'id'=>$this->_get('pid','intval'))));
            	}else{
                	$this->error('操作失败',U(MODULE_NAME.'/padd'));
            	}
        	}
		}
		//子页内容
		public function index(){
			// 获取该页面的id
			$this->Allid = $this->_get('pid');
			/*echo $pid;
			exit();*/
			$count=$this->db->where(array('token'=>$this->token,'pid'=>$this->Allid))->count();
			$page=new Page($count,15);
			$info=$this->db->where(array('token'=>$this->token,'pid'=>$this->Allid))->order('sorts,id')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->assign('pid',$this->Allid);
			$this->display();
		}
		// 添加子页面的微场景
		public function add(){
			$pid = $_GET['pid'];
			$this->assign('pid',$pid);
			$this->display();
		}
		
		// 插入数据
		public function inserts(){
			if ($this->_post('type','intval') == 1) {
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"video_url" => $this->_post('video_url'),
					"sorts" => $this->_post('sorts','intval'),
					// "music_url" => $this->_post('music_url'),
					"name" => $this->_post('name'),
					"pid" => $this->_get('pid','intval')
				);
				$result = $this->db->data($data)->add();
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
				}
			}else if($this->_post('type','intval') == 2){
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"scroll_url" => $this->_post('scroll_url'),
					"sorts" => $this->_post('sorts','intval'),
					/*"music_url" => $this->_post('music_url'),
					 "name" => $this->_post('name'),*/
					"pid" => $this->_get('pid','intval')
				);
				$result = $this->db->data($data)->add();
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
				}
			}else if($this->_post('type','intval') == 3){
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"img_url" => $this->_post('img_url'),
					"sorts" => $this->_post('sorts','intval'),
					/*"music_url" => $this->_post('music_url'),
					 "name" => $this->_post('name'),*/
					"pid" => $this->_get('pid','intval')
				);
				$result = $this->db->data($data)->add();
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
				}
			}else if($this->_post('type','intval') == 4){
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"txt_img" => $this->_post('txt_img'),
					"sorts" => $this->_post('sorts','intval'),
					/*"music_url" => $this->_post('music_url'),
					"name" => $this->_post('name'),*/
					"pid" => $this->_get('pid','intval')
				);
				$result = $this->db->data($data)->add();
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
				}
			}else if($this->_post('type','intval') == 5){
                $data = array(
                    "type" => $this->_post('type','intval'),
                    "token" => $this->_get('token'),
                    "longitude" => $this->_post('longitude'),//经度
                    "latitude" => $this->_post('latitude'),//经度
                    "sorts" => $this->_post('sorts','intval'),
                    /*"music_url" => $this->_post('music_url'),
                     "name" => $this->_post('name'),*/
                    "relation" => $this->_post('relation'),
                    "worktime" => $this->_post('worktime'),
                    "address" => $this->_post('address'),
					"pid" => $this->_get('pid','intval')
                );
                $result = $this->db->data($data)->add();
                if ($result) {
                    $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
                }else{
                    $this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
                }
            }else{
            	 $data = array(
                    "type" => $this->_post('type','intval'),
                    "token" => $this->_get('token'),
                    "appoint" => $this->_post('appoint'),
                    "appoint_nums" => $this->_post('appoint_nums'),
                    "sorts" => $this->_post('sorts','intval'),
                    /*"music_url" => $this->_post('music_url'),
                    "name" => $this->_post('name'),*/
					"pid" => $this->_get('pid','intval')
                   
                );
                $result = $this->db->data($data)->add();
                if ($result) {
                    $this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
                }else{
                    $this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$this->_get('pid','intval'))));
                }
            }
		}
		//编辑
		public function edit(){
			$id = $this->_get('id','intval');
			$pid = $this->_get('pid','intval');
			$result = $this->db->where(array('id'=>$id,'token'=>$this->token,'pid'=>$pid))->find();
			// json在数据库里面解码
			
			
			if (!empty($result['scroll_url'])) {
				$array = array();
				$total = 0;
				$decode = json_decode(str_replace("&amp;quot;", "\"", $result['scroll_url']),true);
				while (list($key,$value) = each($decode)) {
					foreach ($value as $key => $val) {
						array_push($array,$val);
                        $total++;
					}
				}
				$this->assign('total',$total);
				$this->assign('arr',$array);
				
				
			}
			
			$this->assign('pid',$pid);
			$this->assign('result',$result);
			$this->display();
		}


		//编辑保存
		public function saves(){
			$id = $this->_get('id','intval');
			$pid = $this->_get('pid','intval');
			if ($this->_post('type','intval') == 1) {
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"video_url" => $this->_post('video_url'),
					"sorts" => $this->_post('sorts','intval'),
					// "music_url" => $this->_post('music_url'),
					"name" => $this->_post('name'),
					"scroll_url" => "",
					"img_url" => "",
					"txt_img" => "",
					"longitude" => "",
					"latitude" => "",
					"relation" => "",
					"worktime" => "",
					"appoint" => "",
					"address" => ""
				);
				$result = $this->db->where(array('id'=>$id,'token'=>$this->token,'pid'=>$pid))->save($data);
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}
			}else if($this->_post('type','intval') == 2){
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"scroll_url" => $this->_post('scroll_url'),
					"sorts" => $this->_post('sorts','intval'),
					// "music_url" => $this->_post('music_url'),
					"name" => "",
					"video_url" => "",
					"img_url" => "",
					"txt_img" => "",
					"longitude" => "",
					"latitude" => "",
					"relation" => "",
					"worktime" => "",
					"appoint" => "",
					"address" => ""
				);
				$result = $this->db->where(array('id'=>$id,'token'=>$this->token,'pid'=>$pid))->save($data);
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}
			}else if($this->_post('type','intval') == 3){
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"img_url" => $this->_post('img_url'),
					"sorts" => $this->_post('sorts','intval'),
					// "music_url" => $this->_post('music_url'),
					"name" => "",
					"scroll_url" => "",
					"video_url" => "",
					"txt_img" => "",
					"longitude" => "",
					"latitude" => "",
					"relation" => "",
					"worktime" => "",
					"appoint" => "",
					"address" => ""
				);
				$result = $this->db->where(array('id'=>$id,'token'=>$this->token,'pid'=>$pid))->save($data);
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}
			}else if($this->_post('type','intval') == 4){
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"txt_img" => $this->_post('txt_img'),
					"sorts" => $this->_post('sorts','intval'),
					// "music_url" => $this->_post('music_url'),
					"name" => "",
					"img_url" => "",
					"scroll_url" => "",
					"video_url" => "",
					"longitude" => "",
					"latitude" => "",
					"relation" => "",
					"worktime" => "",
					"appoint" => "",
					"address" => ""
				);
				$result = $this->db->where(array('id'=>$id,'token'=>$this->token,'pid'=>$pid))->save($data);
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}
			}else if($this->_post('type','intval') == 5){
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"txt_img" => $this->_post('txt_img'),
					"sorts" => $this->_post('sorts','intval'),
					// "music_url" => $this->_post('music_url'),
					"name" => "",
					"img_url" => "",
					"scroll_url" => "",
					"video_url" => "",
					"longitude" => $this->_post('longitude'),
					"latitude" => $this->_post('latitude'),
					"relation" => $this->_post('relation'),
					"worktime" => $this->_post('worktime'),
					"appoint" => "",
					"address" => $this->_post('address')
				);
				$result = $this->db->where(array('id'=>$id,'token'=>$this->token,'pid'=>$pid))->save($data);
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}
			}else{
				$data = array(
					"type" => $this->_post('type','intval'),
					"token" => $this->_get('token'),
					"txt_img" => $this->_post('txt_img'),
					"sorts" => $this->_post('sorts','intval'),
                    "appoint_nums" => $this->_post('appoint_nums'),
					// "music_url" => $this->_post('music_url'),
					"name" => "",
					"img_url" => "",
					"scroll_url" => "",
					"video_url" => "",
					"longitude" => $this->_post('longitude'),
					"latitude" => $this->_post('latitude'),
					"relation" => $this->_post('relation'),
					"worktime" => $this->_post('worktime'),
					"appoint" => $this->_post('appoint'),
					"address" => ""
				);
				$result = $this->db->where(array('id'=>$id,'token'=>$this->token,'pid'=>$pid))->save($data);
				if ($result) {
					$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}else{
					$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
				}
			}		
		}
		//删除
		public function del(){
			$pid = $this->_get('pid','intval');
			$id = $this->_get('id','intval');
			$result = $this->db->where(array('id'=>$id,'token'=>$this->token))->delete();
			if ($result) {
				$this->success('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
			}else{
				$this->error('操作成功！',U(MODULE_NAME.'/index',array('token'=>$this->token,'pid'=>$pid)));
			}
		}
		//删除主页面
		public function delp(){
			$id = $this->_get('pid','intval');
			$result = $this->dbp->where(array('id'=>$id,'token'=>$this->token))->delete();
			$res = $this->db->where(array('token'=>$this->token,'pid'=>$id))->delete();
			if ($result && $res) {
				$this->success('操作成功！',U(MODULE_NAME.'/light',array('token'=>$this->token,'pid'=>$pid)));
			}else{
				$this->error('操作成功！',U(MODULE_NAME.'/light',array('token'=>$this->token,'pid'=>$pid)));
			}
		}
		// 查阅人数
		public function lookup(){
			$pid = $_GET['pid'];
			$count= M('appoint')->where(array('token'=>$this->token,'pid'=>$pid))->count();
			$page=new Page($count,20);
			// $result = M('appoint')->where(array('pid'=>$pid,'token'=>$this->token))->select();
			$info= M('appoint')->where(array('token'=>$this->token,'pid'=>$pid))->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('page',$page->show());
			$this->assign('info',$info);
			$this->assign('pid',$pid);
			$this->display();
		}



        public function exportexcel(){

            vendor("PHPExcel.PHPExcel");
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            // Set properties
            $objPHPExcel->getProperties()->setCreator("ctos")
                ->setLastModifiedBy("ctos")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(42);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);

            //设置行高度
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);

            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

            //set font size bold
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
            //$objPHPExcel->getActiveSheet()->getStyle('A1:I2')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            //设置水平居中
            //$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            //
            //$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '性别')
                ->setCellValue('C1', '电话号码')
                ->setCellValue('D1', '营员')
                ->setCellValue('E1', '人数');

            $info=M('appoint')->where(array('token'=>$this->token,'pid'=>$_GET['pid']))->select();

            // Miscellaneous glyphs, UTF-8
            for($i=0;$i<count($info);$i++){
                $objPHPExcel->getActiveSheet(0)->setCellValue('A'.($i+2), $info[$i]['name']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('B'.($i+2), $info[$i]['sex']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('C'.($i+2), $info[$i]['telphone']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('D'.($i+2), $info[$i]['campers']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('E'.($i+2), $info[$i]['num']);
                $objPHPExcel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(15);
            }
            // Rename sheet
            //$objPHPExcel->getActiveSheet()->setTitle('订单汇总表');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            //print_r($objPHPExcel);exit;
            //ob_end_clean();
            // Redirect output to a client’s web browser (Excel5)
            header("Pragma: public");

            header("Expires: 0");

            header("Cache-Control:must-revalidate,post-check=0,pre-check=0");

            header("Content-Type:application/force-download");

            header("Content-Type:application/vnd.ms-execl");

            header("Content-Type:application/octet-stream");

            header("Content-Type:application/download");

            header('Content-Disposition:attachment;filename="Appoint-('.date('Ymd-His').').xls"');

            header("Content-Transfer-Encoding:binary");


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

            $objWriter->save('php://output');
            exit;
        }


	}
?>