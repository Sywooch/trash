<?php
	require_once	dirname(__FILE__)."/../../include/common.php";
	require_once	dirname(__FILE__)."/../php/reportLib.php";
	require_once	dirname(__FILE__)."/../../lib/php/dateTimeLib.php";
	require_once	dirname(__FILE__)."/../../request/php/requestLib.php";
	require_once	dirname(__FILE__)."/../../lib/php/models/clinic.class.php";
	require_once	dirname(__FILE__)."/../../lib/php/style4Excel.php";	// стили для ячеек

	
	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", "01.".date("m.Y") ) : "01.".date("m.Y"); 
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");
	$clinicId	= ( isset($_GET["shClinicId"]) ) ? checkField ($_GET["shClinicId"], "i", '') : '';
	$clinic		= ( isset($_GET["shClinic"]) ) ? checkField ($_GET["shClinic"], "t", '') : '';
	$shBranch 	= ( isset($_GET["shBranch"]) ) ? checkField ($_GET["shBranch"], "i", 0) : 0;
	$withBranch = ($shBranch == 1 ) ? true : false;
	$status 	= ( isset($_GET["shStatus"]) ) ? checkField ($_GET["shStatus"], "i", 0) : 0;
	
	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;
	$sortBy		= (isset($_GET['sortBy'])) ? checkField ($_GET['sortBy'], "t", "") : '';	// Сортировка
	$sortType	= (isset($_GET['sortType'])) ? checkField($_GET['sortType'], "t", "") : '';	// Сортировка
	
	
	$crDateFrom	= ( isset($_GET["dateFrom"]) ) ? checkField ($_GET["dateFrom"], "t", date("d.m.Y")) : date("d.m.Y"); 
	$crDateTill	= ( isset($_GET["dateTill"]) ) ? checkField ($_GET["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
	$clinicId	= ( isset($_GET["shClinicId"]) ) ? checkField ($_GET["shClinicId"], "i", '') : '';
	$shBranch 	= ( isset($_GET["shBranch"]) ) ? checkField ($_GET["shBranch"], "i", 0) : 0;
	$status 	= ( isset($_GET["shStatus"]) ) ? checkField ($_GET["shStatus"], "i", 0) : 0;

	$sortBy		= (isset($_GET['sortBy'])) ? checkField ($_GET['sortBy'], "t", "") : '';	// Сортировка
	$sortType	= (isset($_GET['sortType'])) ? checkField($_GET['sortType'], "t", "") : '';	// Сортировка
	
	
	
	$params = array();
	$params['dateReciveFrom']	= $crDateFrom;
	$params['dateReciveTill']	= $crDateTill;
	$params['step'] 		= "100";
	$params['startPage']	= $startPage;
		if ( $clinicId == '' ) { $clinicId = 0; }
	$params['clinic']		= $clinicId;
	$params['branch']		= $shBranch;
	$params['withPrice']		= true;

	
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

	$xmlString  = '<?xml version="1.0" encoding="UTF-8"?>';
	$xmlString = '<dbInfo>';

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

	/*
	header("Content-type: text/xml; charset=UTF-8");
	$str = $doc->saveXML();	
	print $str; 
	exit;
	*/
	
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

$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A5',"Месяц")
					->setCellValue('B5', "Всего обращений")
					->setCellValue('C5', "Переведено")
					->setCellValue('E5', "Записано")
					->setCellValue('G5', "Приём состоялся")
					->setCellValue('I5', "Отказ");

$objPHPExcel->setActiveSheetIndex(0)->getStyle('A5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('B5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('E5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('G5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('I5')->applyFromArray($TH);

$objPHPExcel->getActiveSheet()->mergeCells('C5:D5');
$objPHPExcel->getActiveSheet()->mergeCells('E5:F5');
$objPHPExcel->getActiveSheet()->mergeCells('G5:H5');
$objPHPExcel->getActiveSheet()->mergeCells('I5:J5');


//$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);


$LineList = $xml -> Reports;
if ( $LineList ) {
	$i = 1;
	$DELTA = 5;
	$grTotal = $grTransfer = $grApointment = $grComplete = $grReject = 0;
	foreach($LineList ->  Report as $item) {
		
			$TOTAL = intval($item -> Total);
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.($i+$DELTA), strval($item -> Month))
						->setCellValue('B'.($i+$DELTA), strval($item -> Total))
						->setCellValue('C'.($i+$DELTA), strval($item -> Transfer))
						->setCellValue('D'.($i+$DELTA), ($TOTAL > 0) ? round (intval($item -> Transfer)/intval($item -> Total) *100)."%" : "" ) 
						->setCellValue('E'.($i+$DELTA), strval($item -> Apointment))
						->setCellValue('F'.($i+$DELTA), ($TOTAL > 0) ? round (intval($item -> Apointment)/intval($item -> Total) *100)."%" : "" )
						->setCellValue('G'.($i+$DELTA), strval($item -> Complete))
						->setCellValue('H'.($i+$DELTA), ($TOTAL > 0) ? round (intval($item -> Complete)/intval($item -> Total) *100)."%" : "" )
						->setCellValue('I'.($i+$DELTA), strval($item -> Reject))
						->setCellValue('J'.($i+$DELTA), ($TOTAL > 0) ? round (intval($item -> Reject)/intval($item -> Total) *100)."%" : "" );
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.($i+$DELTA))->applyFromArray($TH);
						
			$grTotal += intval($item -> Total);
			$grTransfer += intval($item -> Transfer);
			$grApointment += intval($item -> Apointment);
			$grComplete += intval($item -> Complete);
			$grReject += intval($item -> Reject);
			$i++;
	}
		$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.($i+$DELTA), "ИТОГО:")
						->setCellValue('B'.($i+$DELTA), $grTotal)
						->setCellValue('C'.($i+$DELTA), $grTransfer)
						->setCellValue('D'.($i+$DELTA), "" ) 
						->setCellValue('E'.($i+$DELTA), $grApointment)
						->setCellValue('F'.($i+$DELTA), "" )
						->setCellValue('G'.($i+$DELTA), $grComplete)
						->setCellValue('H'.($i+$DELTA), "" )
						->setCellValue('I'.($i+$DELTA), $grReject)
						->setCellValue('J'.($i+$DELTA), "" );
	
}

//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2',"Всего пациентов: ".count($xml -> RequestList ->  Element ));
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2',"Всего пациентов: ".$lineCount);
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3',"Общая стоимость, руб: ".$TOTAL);

//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2',count($xml -> RequestList ->  Element ));
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3',$TOTAL);
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('D2')->applyFromArray($strong);
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('D3')->applyFromArray($strong);
//$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
//$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');

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