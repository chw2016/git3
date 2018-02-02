<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-7-14
 * Time: 上午10:08
 * To change this template use File | Settings | File Templates.
 */
class BaozhiyuanAction extends UserAction{


    public function index(){
      //  echo 8;die;
        $where=array();
        if($_POST['status'] == 1){
            $where['status'] = 0;
            $this->assign('status',1);
        }else if($_POST['status'] == 2){
            $where['status'] = 1;
            $this->assign('status',2);
        }else{
            $this->assign('status','');
        }
        $scodeModel=M('Qcode_card');
        $where['uid']=session('uid');
       // $where['uid']=183;
        //echo session('uid');
        $count=$scodeModel->where($where)->count();
        $page=new Page($count,50);
        $info=$scodeModel->where($where)->order('status desc')->limit($page->firstRow.','.$page->listRows)->select();
        $orderLog =  new Model('Order_log','shopnc_','mysql://root:wapwei!@#$%09876@localhost/shopnc');
        foreach($info as $k=>$v){
            $lodata = array();
            $lodata = $orderLog->field(array('order_state,log_time'))->where(array('order_id'=>$v['order_id']))->find();
            if($lodata){
                $info[$k]['order_state'] = $lodata['order_state'];
                $info[$k]['log_time'] = date("Y-m-d H:i:s",$lodata['log_time']);
            }
        }
        if(isset($_GET['p'])){
            $p = $_GET['p'];
        }else{
            $p= 1;
        }
        $this->assign('page',$page->show());
        $this->assign('p',$p);
        $this->assign('info',$info);
        $this->display();
    }

    public function exportexcel(){
        //cho 88;die;

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

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(32);

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
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //
        //$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '券号')
            ->setCellValue('B1', '订单号')
            ->setCellValue('C1', '类型')
            ->setCellValue('D1', '状态')
            ->setCellValue('E1', '收货人')
            ->setCellValue('F1', '手机')
            ->setCellValue('G1', '地址')
            ->setCellValue('H1', '订单状态')
            ->setCellValue('I1', '发货时间');

        $where=array();
        if($_REQUEST['status'] == 1){
            $where['status'] = 0;
            $this->assign('status',1);
        }else if($_REQUEST['status'] == 2){
            $where['status'] = 1;
            $this->assign('status',2);
        }
        $scodeModel=M('Qcode_card');
        $where['uid']=session('uid');
       // $where['uid']=183;
        $count=$scodeModel->where($where)->count();
        $page=new Page($count,300);
        $info=$scodeModel->where($where)->field('q_code,order_sn,type,status,true_name,phone,address_info,order_id')->order('status desc')->limit($page->firstRow.','.$page->listRows)->select();


        $orderLog =  new Model('Order_log','shopnc_','mysql://root:wapwei!@#$%09876@localhost/shopnc');
        foreach($info as $k=>$v){
            if($v['type']==1){
                $info[$k]['type']='498型';
            }
            if($v['type']==2){
                $info[$k]['type']='698型';
            }
            if($v['type']==3){
                $info[$k]['type']='998型';
            }
            if($v['status']==0){
                $info[$k]['status']='未激活';
            }
            if($v['status']==1){
                $info[$k]['status']='已领取';
            }
            $lodata = array();
            $lodata = $orderLog->field(array('order_state,log_time'))->where(array('order_id'=>$v['order_id']))->find();
            if($lodata){
                $info[$k]['order_state'] = $lodata['order_state'];
                $info[$k]['log_time'] = date("Y-m-d H:i:s",$lodata['log_time']);
                unset($info[$k]['order_id']);
            }else{
                $info[$k]['order_state'] = '';
                $info[$k]['log_time'] = '';
                unset($info[$k]['order_id']);
            }
        }

               // Miscellaneous glyphs, UTF-8
        for($i=0;$i<count($info);$i++){
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.($i+2), $info[$i]['q_code']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B'.($i+2), $info[$i]['order_sn']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('C'.($i+2), $info[$i]['type']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('D'.($i+2), $info[$i]['status']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('E'.($i+2), $info[$i]['true_name']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('F'.($i+2), $info[$i]['phone']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('G'.($i+2), $info[$i]['address_info']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('H'.($i+2), $info[$i]['order_state']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('I'.($i+2), $info[$i]['log_time']);
            $objPHPExcel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(15);
        }
         // Rename sheet
        //$objPHPExcel->getActiveSheet()->setTitle('订单汇总表');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        //print_r($objPHPExcel);exit;
        ob_end_clean();
        // Redirect output to a client’s web browser (Excel5)
        header("Pragma: public");

        header("Expires: 0");

        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");

        header("Content-Type:application/force-download");

        header("Content-Type:application/vnd.ms-execl");

        header("Content-Type:application/octet-stream");

        header("Content-Type:application/download");

        header('Content-Disposition:attachment;filename="Order-('.date('Ymd-His').').xls"');

        header("Content-Transfer-Encoding:binary");


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
        exit;
    }

    public function importcode(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('txt');// 设置附件上传类型
        $upload->savePath =  './upload/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $err = $upload->getErrorMsg();
            $this->ajaxReturn(array('code'=>-1,'msg'=>$err));
        }else{// 上传成功
            $data = $upload->getUploadFileInfo();
            $filename = $data[0]['savepath'].$data[0]['savename'];
            $handle  = fopen ($filename, "r");
            $scodeModel = M('Qcode_card');
            $where = array();
            $i = 0;
            $c = 0;
            $d = 0;
            $uid = session('uid');
            $token = session('token');
            while (!feof ($handle)){
                $temparr = array();
                $temres = null;
                $buffer  = fgets($handle, 4096);
                $username = trim($buffer);
                $temparr = explode('|',$username);
                $where['q_code'] = $temparr[0];
                $where['q_secret'] = $temparr[1];
                $where['type'] = $temparr[2];
                $temres = $scodeModel->field('id')->where($where)->find();
                if($temres == null){
                    $codedata = array();
                    $codedata['q_code'] =$temparr[0];
                    $codedata['q_secret'] =$temparr[1];
                    $codedata['type'] =$temparr[2];
                    $codedata['add_time'] = time();
                    $codedata['status'] = 0;
                    $codedata['uid'] = $uid;
                    $codedata['token'] = $token;
                    if($scodeModel->add($codedata)){
                        $i=$i+1;
                    }else{
                        $c=$c+1;
                    }
                    continue;
                }else{
                    $d=$d+1;
                    continue;
                }
            }
            fclose ($handle);
            $this->ajaxReturn(array('code'=>0,'msg'=>'处理成功(成功插入'.$i.'条,失败'.$c.'条,有'.$d.'条重复)'));
        }
    }

    public function del_code(){
        $where['id']=$this->_get('id','intval');
        $where['uid']=session('uid');
        if(D('Qcode_card')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/index'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/index'));
        }
    }


}
