<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-9-9
 * Time: 下午9:59
 * To change this template use File | Settings | File Templates.
 */
class FlagAction extends UserAction{

    public function index(){
        $flagModel=M('Flag');
        $where['uid']=session('uid');
        if($_POST['reg_num']){
            $where['reg_num'] = $_POST['reg_num'];
            $this->assign('reg_num',$_POST['reg_num']);
        }
        $count=$flagModel->where($where)->count();
        $page=new Page($count,15);
        $info=$flagModel->where($where)->limit($page->firstRow.','.$page->listRows)->order('add_time desc')->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->display();
    }

    public function add(){
        $flagModel = M('Flag');
        if(IS_POST){
            $_POST['uid'] = session('uid');
            $_POST['add_time'] = time();
            if(!$flagModel->where(array('uid'=>session('uid'),'reg_num'=>$_POST['reg_num']))->find()){
                if($flagModel->add($_POST)){
                    $this->success('操作成功',U(MODULE_NAME.'/index'));
                }else{
                    $this->error('操作失败',U(MODULE_NAME.'/add'));
                }
            }else{
                $this->error('注册号已存在',U(MODULE_NAME.'/add'));
            }
        }else{
            $this->display();
        }
    }

    public function edit(){
        $flagModel = M('Flag');
        if(IS_POST){
            if($flagModel->where(array('uid'=>session('uid'),'id'=>$_POST['id']))->find()){
                $id = $_POST['id'];
                unset($_POST['id']);
                if($flagModel->where(array('uid'=>session('uid'),'id'=>$id))->save($_POST)){
                    $this->success('操作成功',U(MODULE_NAME.'/index'));
                }else{
                    $this->error('操作失败',U(MODULE_NAME.'/edit'));
                }
            }
        }else{
            $flagdata = $flagModel->where(array('uid'=>session('uid'),'id'=>$_GET['id']))->find();
            $this->assign('flagdata',$flagdata);
            $this->display();
        }
    }

    public function del(){
        $flagModel = M('Flag');
        if($flagModel->where(array('uid'=>session('uid'),'id'=>$_REQUEST['id']))){
            if($flagModel->delete(array('uid'=>session('uid'),'id'=>$_REQUEST['id']))){
                $this->success('操作成功',U(MODULE_NAME.'/index'));
            }else{
                $this->error('操作失败',U(MODULE_NAME.'/index'));
            }
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/index'));
        }
    }

    public function importcode(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 8145728 ;// 设置附件上传大小
        $upload->allowExts  = array('xlsx','xls');// 设置附件上传类型
        $upload->savePath =  './upload/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $err = $upload->getErrorMsg();
            $this->ajaxReturn(array('code'=>-1,'msg'=>$err));
        }else{// 上传成功
            $data = $upload->getUploadFileInfo();
            $filename = $data[0]['savepath'].$data[0]['savename'];
            $exceldata = $this->read($filename);
            $flagModel = M('flag');
            $c = 0;
            $d = 0;
            $e = 0;
            if($exceldata){
                for($i=2;$i<count($exceldata);$i++){
                    $where = array();
                    $where['uid'] = session('uid');
                    $where['reg_num'] = $exceldata[$i][1];
                    $temres = $flagModel->field('id')->where($where)->find();
                    if($temres == null){
                        $flagdata = array();
                        $flagdata['type'] =$exceldata[$i][0];
                        $flagdata['reg_num'] =$exceldata[$i][1];
                        $flagdata['flag_name'] = $exceldata[$i][2];
                        $flagdata['apply_date'] = $exceldata[$i][3];
                        $flagdata['first_date'] = $exceldata[$i][4];
                        $flagdata['first_under_date'] = $exceldata[$i][5];
                        $flagdata['reg_date'] = $exceldata[$i][6];
                        $flagdata['under_date'] = $exceldata[$i][7];
                        $flagdata['apply_person'] = $exceldata[$i][8];
                        $flagdata['agent_station'] = $exceldata[$i][9];
                        $flagdata['first_num'] = $exceldata[$i][10];
                        $flagdata['first_yema'] = $exceldata[$i][11];
                        $flagdata['used_goods'] = $exceldata[$i][12];
                        $flagdata['remark'] = $exceldata[$i][13];
                        $flagdata['goods_class'] = $exceldata[$i][14];
                        $flagdata['uid'] = session('uid');
                        $flagdata['add_time'] = time();
                        if($flagModel->add($flagdata)){
                            $c=$c+1;
                        }else{
                            $d=$d+1;
                        }
                        continue;
                    }else{
                        $e=$e+1;
                        continue;
                    }
                }
            }else{
                $this->ajaxReturn(array('code'=>0,'msg'=>'系统错误'));
            }
            // Create new PHPExcel object


            $this->ajaxReturn(array('code'=>0,'msg'=>'处理成功(成功插入'.$c.'条,失败'.$d.'条,有'.$e.'条重复)'));
        }
    }

    public function read($filename,$encode='utf-8'){
        vendor("PHPExcel.PHPExcel");
        $objReader = PHPExcel_IOFactory::createReader(Excel5);
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;

    }



}