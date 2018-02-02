<?php
class Export {
	
	public function __construct() {
		
		
	}
		
	public function array_to_excel($array) {
		
		if (is_array($array) && !empty($array)) {
			$excel_path_str = C('site_url').'lib'.C('COOKIE_PATH').'ORG'.C('COOKIE_PATH').'PHPExcel'.C('COOKIE_PATH').'PHPExcel.class.php';
			$Excel2007_path_str = C('site_url').'lib'.C('COOKIE_PATH').'ORG'.C('COOKIE_PATH').'PHPExcel'.C('COOKIE_PATH').'PHPExcel'.C('COOKIE_PATH').'Writer'.C('COOKIE_PATH').'Excel2007.php';
			print_r($Excel2007_path_str);
			print_r(is_file($Excel2007_path_str));
			exit();
			if(is_file($excel_path_str) && is_file($Excel2007_path_str)){
				require($excel_path_str);
				require($Excel2007_path_str);
				$objPHPExcel = new PHPExcel();
				/*设置标题*/
				$objPHPExcel->getProperties()->setTitle(time())->setDescription('none');
				
				/*读取数据，设置单元格的值*/
								
				$i = 0;
				$j = 0;
				while (list($key, $value) = each($array)) {
						
					if (is_array($value) && !empty($value)) {
						$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($j)->setWidth(16);
						$objPHPExcel->getActiveSheet()->getStyle('A1:AE'.($j+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('A1:AE'.($j+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				
						while (list($k, $val) = each($value)) {
							if (strlen($val) <= 10) {
								$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(10);
							}elseif(( strlen($val) > 10 ) && ( strlen($val) < 20 )) {
								$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(20);
							}elseif(( strlen($val) >= 20) && ( strlen($val) <= 40 )) {
								$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(45);
							}else {
								$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(60);
							}
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i,$j+1,$val);
							$i++;
						}
						$i = 0;
					}
					$j++;
				}
				
				
// 				foreach ($array as $key => $value) {					
// 					if (is_array($value) && !empty($value)) {						
// 						$valueNum = count($value);
// 						for ($i=0; $i < $valueNum; $i++) {
// 							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(16);
// 							$objPHPExcel->getActiveSheet()->getStyle('A1:AE'.($key+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// 							$objPHPExcel->getActiveSheet()->getStyle('A1:AE'.($key+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// 							if (strlen($value[$i]) <= 10) {
// // 							$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
// 								$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(10);
// 							}elseif(( strlen($value[$i]) > 10 ) && ( strlen($value[$i]) < 20 )) {
// 								$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(20);
// 							}elseif(( strlen($value[$i]) >= 20) && ( strlen($value[$i]) <= 40 )) {
// 								$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(45);
// 							}else {
// 								$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth(60);
// 							}
// 							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $key+1, $value[$i]);
// 						}
// 					}					
// 				}																
				$objPHPExcel->setActiveSheetIndex(0);
				$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
				
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
				header("Content-Type:application/force-download");
				header("Content-Type:application/vnd.ms-execl");
				header("Content-Type:application/octet-stream");
				header("Content-Type:application/download");
				header("Content-Disposition:attachment;filename=".date('Ymd').time().".xls");
				header("Content-Transfer-Encoding:binary");
				$objWriter->save('php://output');
			}
		} elseif (is_string($array)) {
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->setCellValue('A1',$array);
		}
	}
}