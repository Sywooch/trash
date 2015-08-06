<?php
	require_once	dirname(__FILE__)."/../../include/common.php";
	require_once	dirname(__FILE__)."/../php/reportLib.php";
	require_once	dirname(__FILE__)."/../../lib/php/dateTimeLib.php";
	require_once	dirname(__FILE__)."/../../request/php/requestLib.php";
	require_once	dirname(__FILE__)."/../../lib/php/models/clinic.class.php";
	require_once	dirname(__FILE__)."/../../lib/php/style4Excel.php";	// стили для ячеек

	
	$clinicList = ( isset($_POST["line"]) ) ? $_POST["line"] : array();
	
	$crDateFrom	= ( isset($_POST["crDateShFrom"]) ) ? checkField ($_POST["crDateShFrom"], "t", "01.".date("m.Y") ) : "01.".date("m.Y"); 
	$crDateTill	= ( isset($_POST["crDateShTill"]) ) ? checkField ($_POST["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");
	$clinicId	= ( isset($_POST["shClinicId"]) ) ? checkField ($_POST["shClinicId"], "i", '') : '';
	$clinic		= ( isset($_POST["shClinic"]) ) ? checkField ($_POST["shClinic"], "t", '') : '';
	$shBranch 	= 1;
	$withBranch = ($shBranch == 1 ) ? true : false;
	$status 	= ( isset($_POST["shStatus"]) ) ? checkField ($_POST["shStatus"], "i", 0) : 0;
	
	$startPage	= ( isset($_POST["startPage"]) ) ? checkField ($_POST["startPage"], "i") : 0;
	$sortBy		= (isset($_POST['sortBy'])) ? checkField ($_POST['sortBy'], "t", "") : '';	// Сортировка
	$sortType	= (isset($_POST['sortType'])) ? checkField($_POST['sortType'], "t", "") : '';	// Сортировка
	
	
	$crDateFrom	= ( isset($_POST["dateFrom"]) ) ? checkField ($_POST["dateFrom"], "t", date("d.m.Y")) : date("d.m.Y"); 
	$crDateTill	= ( isset($_POST["dateTill"]) ) ? checkField ($_POST["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
	$clinicId	= ( isset($_POST["shClinicId"]) ) ? checkField ($_POST["shClinicId"], "i", '') : '';
	$shBranch 	= ( isset($_POST["shBranch"]) ) ? checkField ($_POST["shBranch"], "i", 0) : 0;
	$status 	= ( isset($_POST["shStatus"]) ) ? checkField ($_POST["shStatus"], "i", 0) : 0;

	$sortBy		= (isset($_POST['sortBy'])) ? checkField ($_POST['sortBy'], "t", "") : '';	// Сортировка
	$sortType	= (isset($_POST['sortType'])) ? checkField($_POST['sortType'], "t", "") : '';	// Сортировка
	
	
	
	$params = array();
	$params['dateReciveFrom']	= $crDateFrom;
	$params['dateReciveTill']	= $crDateTill;
	$params['step'] 		= "100";
	$params['startPage']	= $startPage;
		if ( $clinicId == '' ) { $clinicId = 0; }
	$params['branch']		= $shBranch;
	$params['withPrice']		= true;
	$params['kind'] = DocRequest::KIND_DOCTOR;
	
	switch ( $status ) {
		case 1 : 	{
						$params['isTransfer']	= "1";
						$params['crDateFrom']	= $crDateFrom;
						$params['crDateTill']	= $crDateTill;
						$params['dateReciveFrom']	= "";
						$params['dateReciveTill']	= "";
					} break;
		case 3 : $params['status']		= "3"; break;
		case 4 : $params['status']		= "8"; break;
		case 5 : $params['status']		= "9"; break;
	}
	
	
	// Сортировка
	if ( !empty($sortBy) ) {$params['sortBy'] = $sortBy;}
	if ( !empty($sortBy) ) {$params['sortType'] = $sortType;}

	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Docdoc.ru") -> setTitle("Request report");
	$objPHPExcel->setActiveSheetIndex(0)
	             ->setCellValue('A1', "Суммарный отчет по переведенным/записанным/дошедшим пациентам");
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($Head);             
	$objPHPExcel->setActiveSheetIndex(0)
	             ->setCellValue('A2', "за период с ".$crDateFrom." по ".$crDateTill);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A2')->applyFromArray($Head);
	$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
	
	$objPHPExcel->setActiveSheetIndex(0)
	             ->setCellValue('A3', $clinic );
	$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');

	
	if (count ($clinicList) > 0 ) {
		
		
		
		$GR_DELTA =  5;
		foreach ($clinicList as $clinic) {
			$clinicId = checkField ($clinic, "i", '');
			if ( $clinicId == '' ) { $clinicId = 0; }
			$params['clinic']		= $clinicId;
			
			$xmlString  = '<?xml version="1.0" encoding="UTF-8"?>';
			$xmlString = '<dbInfo>';

			$clinicObj = new Clinic();
			$clinicObj->getClinic($clinicId);
			$xmlString .= $clinicObj->getClinicXML();
			
			
			$xmlString .= "<Reports>";
			$monthArray = monthBetweenTwoDate($crDateFrom, $crDateTill);
			foreach ($monthArray as $line => $data) {
				$xmlString .= "<Report>";
					$xmlString .= "<StartDate>".$data[0]."</StartDate>";
					$xmlString .= "<EndDate>".$data[1]."</EndDate>";
					$month = date("m",strtotime($data[0]));
					$xmlString .= "<Month id=\"".$month."\">".getRusMonth($month)."</Month>";
					$xmlString .= "<Transfer>".getRequestCount($clinicId, 'transfer', $data[0], $data[1], $withBranch)."</Transfer>";
					$xmlString .= "<Apointment>".getRequestCount($clinicId, 'apointment', $data[0], $data[1], $withBranch)."</Apointment>";
					$xmlString .= "<Complete>".getRequestCount($clinicId, 'complete', $data[0], $data[1], $withBranch)."</Complete>";
					$xmlString .= "<Reject>".getRequestCount($clinicId, 'reject', $data[0], $data[1], $withBranch)."</Reject>";
					$xmlString .= "<Total>".getRequestCount($clinicId, 'total', $data[0], $data[1], $withBranch)."</Total>";
				$xmlString .= "</Report>"; 
			} 
			$xmlString .= "</Reports>"; 
			$xmlString .= '</dbInfo>';
		
			$doc = new DOMDocument('1.0','UTF-8');
			if ($doc -> loadXML($xmlString) ) {
				$xml = new SimpleXMLElement($xmlString);
			} else {
				echo "не XML";		
			}
 
			
			$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A'.$GR_DELTA,"Месяц")
								->setCellValue('B'.$GR_DELTA, "Всего обращений")
								->setCellValue('C'.$GR_DELTA, "Переведено")
								->setCellValue('E'.$GR_DELTA, "Записано")
								->setCellValue('G'.$GR_DELTA, "Приём состоялся")
								->setCellValue('I'.$GR_DELTA, "Отказ");
			
			$objPHPExcel->getActiveSheet()->mergeCells('C'.$GR_DELTA.':D'.$GR_DELTA);
			$objPHPExcel->getActiveSheet()->mergeCells('E'.$GR_DELTA.':F'.$GR_DELTA);
			$objPHPExcel->getActiveSheet()->mergeCells('G'.$GR_DELTA.':H'.$GR_DELTA);
			$objPHPExcel->getActiveSheet()->mergeCells('I'.$GR_DELTA.':J'.$GR_DELTA);
			
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('D'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('E'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('F'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('G'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('H'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('I'.$GR_DELTA)->applyFromArray($TH);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('J'.$GR_DELTA)->applyFromArray($TH);
			
			
			
			
			//$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
			$ClinicLine = $xml -> Clinic;
			if ( $ClinicLine ) {
					$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.($GR_DELTA - 1), strval($ClinicLine -> Title));
			}
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($GR_DELTA - 1).':J'.($GR_DELTA - 1));
			$objPHPExcel->setActiveSheetIndex(0)->getStyle( 'A'.($GR_DELTA - 1) )->applyFromArray($strong);
			
			
			
			$LineList = $xml -> Reports;
			if ( $LineList ) {
				$i = 1;
				$DELTA = 0;
				$grTotal = $grTransfer = $grApointment = $grComplete = $grReject = 0;
				foreach($LineList ->  Report as $item) {
					
						$TOTAL = intval($item -> Total);
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.($i+$DELTA+$GR_DELTA), strval($item -> Month))
									->setCellValue('B'.($i+$DELTA+$GR_DELTA), strval($item -> Total))
									->setCellValue('C'.($i+$DELTA+$GR_DELTA), strval($item -> Transfer))
									->setCellValue('D'.($i+$DELTA+$GR_DELTA), ($TOTAL > 0) ? intval($item -> Transfer)/intval($item -> Total) : "" ) 
									->setCellValue('E'.($i+$DELTA+$GR_DELTA), strval($item -> Apointment))
									->setCellValue('F'.($i+$DELTA+$GR_DELTA), ($TOTAL > 0) ? intval($item -> Apointment)/intval($item -> Total) : "" )
									->setCellValue('G'.($i+$DELTA+$GR_DELTA), strval($item -> Complete))
									->setCellValue('H'.($i+$DELTA+$GR_DELTA), ($TOTAL > 0) ? intval($item -> Complete)/intval($item -> Total) : "" )
									->setCellValue('I'.($i+$DELTA+$GR_DELTA), strval($item -> Reject))
									->setCellValue('J'.($i+$DELTA+$GR_DELTA), ($TOTAL > 0) ? intval($item -> Reject)/intval($item -> Total) : "" );
						$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.($i+$DELTA+$GR_DELTA))->applyFromArray($TH);
						if ( $i % 2 == 0 ) {
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('D'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('E'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('F'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('G'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('H'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('I'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);
							$objPHPExcel->setActiveSheetIndex(0)->getStyle('J'.($i+$DELTA+$GR_DELTA))->applyFromArray($odd);

						}
						$objPHPExcel->setActiveSheetIndex(0)->getStyle('D'.($i+$DELTA+$GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
						$objPHPExcel->setActiveSheetIndex(0)->getStyle('F'.($i+$DELTA+$GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE); 
						$objPHPExcel->setActiveSheetIndex(0)->getStyle('H'.($i+$DELTA+$GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
						$objPHPExcel->setActiveSheetIndex(0)->getStyle('J'.($i+$DELTA+$GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
									
						$grTotal += intval($item -> Total);
						$grTransfer += intval($item -> Transfer);
						$grApointment += intval($item -> Apointment);
						$grComplete += intval($item -> Complete);
						$grReject += intval($item -> Reject);
						$i++;
				}
				
				 
					$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.($i+$DELTA+$GR_DELTA), "ИТОГО:")
									->setCellValue('B'.($i+$DELTA+$GR_DELTA), $grTotal)
									->setCellValue('C'.($i+$DELTA+$GR_DELTA), $grTransfer)
									->setCellValue('D'.($i+$DELTA+$GR_DELTA), "" ) 
									->setCellValue('E'.($i+$DELTA+$GR_DELTA), $grApointment)
									->setCellValue('F'.($i+$DELTA+$GR_DELTA), "" )
									->setCellValue('G'.($i+$DELTA+$GR_DELTA), $grComplete)
									->setCellValue('H'.($i+$DELTA+$GR_DELTA), "" )
									->setCellValue('I'.($i+$DELTA+$GR_DELTA), $grReject)
									->setCellValue('J'.($i+$DELTA+$GR_DELTA), "" );
									
				$GR_DELTA = $GR_DELTA + $i + 4;
			}
			
			
			
			
			
		}
	}
	
	
	$objPHPExcel->getActiveSheet()->setTitle('Report');
	$objPHPExcel->setActiveSheetIndex(0);
	
	
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			

$file="RequestSummaryReport_".$crDateFrom."_".$crDateTill.".xls";
$filename = dirname(__FILE__)."/../../_reports/".$file;

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($filename);
chmod($filename, FILE_MODE);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: max-age=0, must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/vnd.ms-excel');
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=\"{$file}\"");
header("Content-Transfer-Encoding: binary");
            
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>