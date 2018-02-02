<?php
/**
 *  银波米业1
 **/
class ExportExcelAction  {
	public function PinzhongDel(){
        $this->del('Zp2');
    }
    
    
    /*
     * 导出为excel
    * $data 数据
    * $title 头部
    * $filename 文件名
    * 如果出现失败网络错误就注释清空缓存ob_end_clean();
    * 用法: $this->ExportExcel($data,array('微信昵称','姓名'),'会员资料库');
    * 注意:$data是个二维数组,第二个参数如果数据只有2条，里面的内容对应2条
    * 技术支持：李铭
    */
    
    
  public function ExportExcel($data=array(),$title=array(),$filename='report')
    {
    	$data = array_values($data);
    	$title = array_values($title);
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
    	$aSize = range('A', chr(65+count($title)-1));
    
    	foreach ($aSize as $size) {
    		//$objPHPExcel->getActiveSheet()->getColumnDimension($size)->setWidth(30);
    		$objPHPExcel->getActiveSheet()->getStyle($size.'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	}
    
    	//设置行高度
    	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
    
    	$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
    
    	//set font size bold
    	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
    
    
    	$objSheet = $objPHPExcel->setActiveSheetIndex(0);
    	for ($i = 0; $i < count($aSize); $i++) {
    		$objSheet->setCellValue($aSize[$i].'1', $title[$i]);
    	}
    
    	// Miscellaneous glyphs, UTF-8
    	for($i=0;$i<count($data);$i++){
    		$j = 0;
    		foreach ($data[$i] as $key => $item) {
    			$objPHPExcel->getActiveSheet(0)
    			->setCellValue($aSize[$j].($i+2), $item);
    			$j++;
    		}
    		$objPHPExcel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(15);
    	}
    	/*
    	 $objPHPExcel->getActiveSheet(0)->setCellValue('A'.($i+2), 1);
    	$objPHPExcel->getActiveSheet(0)->setCellValue('B'.($i+2), 'name');
    	*/
    	// Rename sheet
    	//$objPHPExcel->getActiveSheet()->setTitle($filename);
    
    
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
    
    	header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
    
    	header("Content-Transfer-Encoding:binary");
    
    
    	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    
    	$objWriter->save('php://output');
    	exit;
    }
    
    
    public function Mrugr(){
    	//echo 1;die;
    	$data=M('mru_jfb')->field("id,name,sex,tel,address,num,hongbao,idcard")->select();
    	//P($data);die;
    	$this->ExportExcel($data,array('编号','昵称','性别','手机','地址','积分','红名','会员卡'),'会员资料库');
    	die;
   }
   
   public function Zhaopin(){
   	//echo 1;die;
   	$data=M('mru_wyjm')->field('id,name,xl,dh,zy,age,sex,num,xtz,ytz,add_time')->select();
   	//P($data);die;
   	$this->ExportExcel($data,array('ID', '申请人','学历', '电话','从事职业','性别','年龄', '店面团队人数','现有投资规模','拟投资规模','加盟时间'),'会员资料库');
   	die;
   }
   
   public function jl(){
   	//echo 1;die;
   	$data=M('mru_jl')->field('id,name,age,sex,xl,jg,dh,yx,add_time')->select();
   	//P($data);die;
   	$this->ExportExcel($data,array('ID', '姓名','性别', '年龄','学历','籍贯', '电话','邮箱','应聘时间'),'会员资料库');
   	die;
   }
   public function Mrupl(){
   	//echo 1;die;
   	$data=M('mru_pl')->field('id,openid,mdain,name,xin,add_time')->where(array('token'=>$_GET['token']))->select();
    foreach($data as $k=>$val){
        $data[$k]['add_time'] = $val['add_time'];//date('Y-m-d',$val['add_time']);
        $user = M('mru_jfb')->where(array('token'=>$_GET['token'],'openid'=>$val['openid']))->find();
        $data[$k]['idcard'] = $user['idcard'];
        $data[$k]['tel'] = $user['tel'];
        $info = M('mru_pl')->where(array('id'=>$val['id']))->find();
        $data[$k]['content'] = (string)(base64_decode($info['content']));
    }
   	    Excel::arr2ExcelDownload($data,array('ID', '会员号', '门店', '商品名', '评价级别','评论时间','会员卡号','手机号','评价内容'),'会员资料库');
    //p($data);
    //die;
   	//$this->ExportExcel($data,array('ID', '会员号', '门店', '商品名', '评价级别','评论时间','会员卡号','手机号','评价内容'),'会员资料库');
   	die;
   }
   
   public function Huodong(){
   	//echo 1;die;
   	$data=M('mru_huodong')->where(array('aid'=>0))->field('id,title,status,add_time,pic,mdiaos,sort')->select();
   	//P($data);die;
   	$this->ExportExcel($data,array('ID', '标题','是否开启', '发布时间','图片','参加门店','排序'),'会员资料库');
   	die;
   }
    
   
   public function Xyxx(){
   	//echo 1;die;
   	$aWhere=$_SESSION['aWhere'];
   	$data=M('Context_shop')->where($aWhere)->field('id,cid,name,openid,sex,age,mc,cj,jjz,gj,address,type,hm,xx,email,tel,jname,jtel,gx,cm,type2,money')->select();

       foreach($data as $key=>$valure){
            //课程名称
           $info = M('Context_list')->where(array('id'=>$valure['cid']))->find();
           $data[$key]['cid'] = $info['title'];
           $user = M('Wxuser')->where(array('token'=>$_SESSION['token']))->find();
           $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$valure['openid']))->find();
           $data[$key]['openid'] = $users['nickname'];
       }

   	$this->ExportExcel($data,array('ID', '课程名称','姓名','微信号','性别','出生日期','曾参加过马拉松赛事名称及组别','以往马拉松成绩' ,'是否有赛事举办地户籍或居住证','国籍', '所居城市', '证件类型', '证件号码', '血型','电子邮箱地址','联系电话','紧急联系人','紧急联系人电话','紧急联系人关系','衣服尺码','类型','费用'),'会员资料库');
   	die;
   }
   
   
   public function hb(){
   	//echo 1;die;
   	if($_SESSION['aWhere']){
   		$aWhere=$_SESSION['aWhere'];
   	}else{
   		$aWhere = array(
   				'token' =>$_SESSION['token'],
   				'state' => 1
   		);
   	}

   	
   	$data=M('mru_hb')->field('id,aid,yzm,name,add_time,price,openid')->order("add_time desc")->where($aWhere)->select();
   	foreach ($data as $ke => $v){
   		$data[$ke]['add_time']=date("Y-m-d h:i:s",$v['add_time']);
   		$data[$ke]['openid']=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('tel');
   		$data[$ke]['aid']=M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name')?M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name'):'总部';
   	}
   	$this->ExportExcel($data,array('ID', '店铺','验证码','获取方式', '使用时间','价格','手机'),'红包记录');
   	die;
   }
   
   
   public function qgj(){
   	//echo 1;die;
    if($_SESSION['aWhere']){
   		$aWhere=$_SESSION['aWhere'];
   	}else{
   		$aWhere = array(
   				'token' =>$_SESSION['token'],
   				'state' => 1
   		);
   	}
  
   	$data=M('mru_qgj')->field('id,aid,yzm,name,add_time,price,openid')->order("add_time desc")->where($aWhere)->select();
   	foreach ($data as $ke => $v){
   		$data[$ke]['add_time']=date("Y-m-d h:i:s",$v['add_time']);
   		$data[$ke]['openid']=M('mru_jfb')->where(array('openid'=>$v['openid'],'token'=>$_SESSION['token']))->getField('tel');
   		$data[$ke]['aid']=M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name')?M('mru_mdian')->where(array('id'=>$v['aid']))->getField('name'):'总部';
   	}
   	$this->ExportExcel($data,array('ID', '店铺','验证码','获取方式', '使用时间','价格','手机'),'抢购券记录');
   	die;
   }


    /*劳动局后台聊天记录导出*/
    public function record(){
        $room = M('Labor_code')->where(array('id'=>$_GET['id']))->find();
        $info = M('Room_msg')->field('openid,add_time,msg,role,code')->where(array('code'=>$room['code'],'token'=>$_SESSION['token']))->order('add_time desc')->select();
        foreach($info as $key=>$val){
            $user = M('Wxuser')->where(array('token'=>$this->token))->find();
            $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$val['openid']))->find();
            $info[$key]['openid'] = $users['nickname'];
            switch($val['role']){
                case 0: $info[$key]['role'] = '企业';break;
                case 1: $info[$key]['role'] = '员工';break;
                case 2: $info[$key]['role'] = '新区劳动科';break;
                case 3: $info[$key]['role'] = '大鹏劳动办';break;
                case 4: $info[$key]['role'] = '南澳劳动办';break;
                case 5: $info[$key]['role'] = '葵涌劳动办';break;
            }
            $info[$key]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
        }

        exportExcel($info,array('聊天者','时间','内容','角色','聊天室验证码'),$room['title'].'聊天记录');
    }
   
   
}
?>
