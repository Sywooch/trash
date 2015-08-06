<?php
	require_once	dirname(__FILE__)."/../../include/common.php";
	require_once	dirname(__FILE__)."/../../lib/php/validate.php";
	require_once	dirname(__FILE__)."/../php/reportLib.php";
	require_once	dirname(__FILE__)."/../php/report4clinicLib.php";
	require_once	dirname(__FILE__)."/../../request/php/requestLib.php";
	require_once	dirname(__FILE__)."/../../lib/php/style4Excel.php";	// стили для ячеек

	
	$crDateFrom	= ( isset($_GET["dateFrom"]) ) ? checkField ($_GET["dateFrom"], "t", date("d.m.Y")) : date("d.m.Y"); 
	$crDateTill	= ( isset($_GET["dateTill"]) ) ? checkField ($_GET["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
	
	$clinicList 	= ( isset($_GET["line"]) ) ? $_GET["line"] : array();
	
	$params = array();
	$params['dateReciveFrom']	= $crDateFrom;
	$params['dateReciveTill']	= $crDateTill;
	
	if ( count($clinicList) > 0 ) {
		foreach ($clinicList as $clinic) {
			$clinic = checkField ($clinic, "i", 0);
			if ( $clinic > 0 ) {
				$params['clinic']	= $clinic;
				$file = reportGenerator ($params);
			}
		}
	}
	
	

	
	
	function reportGenerator ($params = array()) {
		
		$xmlString  = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlString .= "<dbInfo>";

		$clinic = new Clinic();
		$clinic -> getClinic($params['clinic']);
		
		$xmlString .= $clinic -> getClinicXML(); 
		$xmlString .= getSpecializationListXML();
		$xmlString .= getPatientListXML($params, $clinic -> Id);
		$xmlString .= getStatus4RequestXML();
		$xmlString .= '</dbInfo>';
	
		$doc = new DOMDocument('1.0','UTF-8');
		if ($doc -> loadXML($xmlString) ) {
			$xml = new SimpleXMLElement($xmlString);
		} else {
			echo "не XML";		
		}
		
	}
	
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Docdoc.ru") -> setTitle("Request common report");
$objPHPExcel->setActiveSheetIndex(0)
             ->setCellValue('A1', "Отчёт за период с ".$crDateFrom." по ".$crDateTill);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($Head);             
$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');

$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A3',"#")
					->setCellValue('B3', "Название")
					->setCellValue('C3', "Переведено")
					->setCellValue('D3', "Записано")
					->setCellValue('E3', "Приём состоялся")
					->setCellValue('F3', "Отказ");

$objPHPExcel->setActiveSheetIndex(0)->getStyle('A3')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('B3')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C3')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('D3')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('E3')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('F3')->applyFromArray($TH);

$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);


$LineList = $xml -> ClinicList;
if ( $LineList ) {
	$i = 1;
	$DELTA = 3;
	foreach($LineList ->  Element as $item) {
		$sector = $item -> Sector ->attributes();
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.($i+$DELTA), ($i))
					->setCellValue('B'.($i+$DELTA), strval($item -> Name))
					->setCellValue('C'.($i+$DELTA), strval($item -> Transfer))
					->setCellValue('D'.($i+$DELTA), strval($item -> Apointment))
					->setCellValue('E'.($i+$DELTA), strval($item -> Complete))
					->setCellValue('F'.($i+$DELTA), strval($item -> Reject));
		if ( strval($item -> ParentId) != '0' ) 
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.($i+$DELTA))->applyFromArray($moveRight3);
		$i++;
	}
}


$objPHPExcel->getActiveSheet()->setTitle('Report');
$objPHPExcel->setActiveSheetIndex(0);


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

$file="RequestCommonReport_".$crDateFrom."_".$crDateTill.".xls";
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