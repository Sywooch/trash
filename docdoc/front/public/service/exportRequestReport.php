<?php
use dfs\docdoc\helpers\DomHelper;

require_once dirname(__FILE__) . "/../include/common.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/../php/lib/reportLib.php";
require_once dirname(__FILE__) . "/../php/lib/requestLib.php";

$TH = array(
	'font'      => array(
		'name' => 'Arial Cyr',
		'size' => '10',
		'bold' => true
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
	),
	'fill'      => array(
		'type'  => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => 'EAEAEA')
	)
);
$Head = array(
	'font'      => array(
		'name' => 'Arial Cyr',
		'size' => '16',
		'bold' => true
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);
$strong = array(
	'font' => array(
		'name' => 'Arial Cyr',
		'size' => '10',
		'bold' => true
	)
);


$crDateFrom = (isset($_GET["dateFrom"])) ? checkField($_GET["dateFrom"], "t", date("d.m.Y")) : date("d.m.Y");
$crDateTill = (isset($_GET["dateTill"])) ? checkField($_GET["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
$clinicId = (isset($_GET["shClinicId"])) ? checkField($_GET["shClinicId"], "i", '') : '';
$shBranch = (isset($_GET["shBranch"])) ? checkField($_GET["shBranch"], "i", 0) : 0;

$sortBy = (isset($_GET['sortBy'])) ? checkField($_GET['sortBy'], "t", "") : ''; // Сортировка
$sortType = (isset($_GET['sortType'])) ? checkField($_GET['sortType'], "t", "") : ''; // Сортировка


$params = array();
$params['dateReciveFrom'] = $crDateFrom;
$params['dateReciveTill'] = $crDateTill;
if ($clinicId == '') {
	$clinicId = 0;
}
$params['clinic'] = $clinicId;
$params['branch'] = $shBranch;
$params['status'] = "3";
$params['withPager'] = false;

// Сортировка
if (!empty($sortBy)) {
	$params['sortBy'] = $sortBy;
}
if (!empty($sortBy)) {
	$params['sortType'] = $sortType;
}


$xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
$xmlString .= "<dbInfo>";
$xmlString .= getRequestListXML($params, getCityId());
if ($clinicId > 0) {
	$xmlString .= getClinicListByIdWithBranchesXML($clinicId);
}
$xmlString .= getStatus4RequestXML();
$xmlString .= '</dbInfo>';

$doc = new DOMDocument('1.0', 'UTF-8');
if ($doc->loadXML($xmlString)) {
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
$objPHPExcel->getProperties()->setCreator("Docdoc.ru")->setTitle("Request report");
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', "Отчёт за период с " . $crDateFrom . " по " . $crDateTill);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($Head);
$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A5', "#")
	->setCellValue('B5', "Дата визита")
	->setCellValue('C5', "Врач")
	->setCellValue('D5', "Специальность")
	->setCellValue('E5', "Стоимость, руб")
	->setCellValue('F5', "Пациент")
	->setCellValue('G5', "Телефон")
	->setCellValue('H5', "Клиника");

$objPHPExcel->setActiveSheetIndex(0)->getStyle('A5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('B5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('D5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('E5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('F5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('G5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('H5')->applyFromArray($TH);

$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);


$LineList = $xml->RequestList;
if ($LineList) {
	$i = 1;
	$DELTA = 5;
	$TOTAL = 0;
	foreach ($LineList->Element as $item) {
		$sector = $item->Sector->attributes();
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . ($i + $DELTA), ($i))
			->setCellValue('B' . ($i + $DELTA), strval($item->AppointmentDate) . " " . strval($item->AppointmentTime))
			->setCellValue('C' . ($i + $DELTA), strval($item->Doctor))
			->setCellValue('D' . ($i + $DELTA), strval($item->Sector))
			->setCellValue('E' . ($i + $DELTA), priceVariant($sector['id']))
			->setCellValue('F' . ($i + $DELTA), strval($item->Client))
			->setCellValue('G' . ($i + $DELTA), strval($item->ClientPhone))
			->setCellValue(
				'H' . ($i + $DELTA),
				DomHelper::searchElt($xml->ClinicList, "id", strval($item->ClinicId))->Name
			);
		$i++;
		$TOTAL += priceVariant($sector['id']);
	}
}

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "Всего пациентов: " . count($xml->RequestList->Element));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', "Общая стоимость, руб: " . $TOTAL);

//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2',count($xml -> RequestList ->  Element ));
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3',$TOTAL);
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('D2')->applyFromArray($strong);
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('D3')->applyFromArray($strong);
$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');

$objPHPExcel->getActiveSheet()->setTitle('Report');
$objPHPExcel->setActiveSheetIndex(0);


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);


$file = "RequestReport_" . $crDateFrom . "_" . $crDateTill . ".xls";
$filename = dirname(__FILE__) . "/../../_reports/" . $file;

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($filename);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: max-age=0, must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/vnd.ms-excel');
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename={$file}");
header("Content-Transfer-Encoding: binary");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

function priceVariant($sector)
{
	$price = 800;

	$sector = intval($sector);
	switch ($sector) {
		case 86:
			$price = 1500;
			break; //Пластический хирург
		case 90:
			$price = 1200;
			break; // Стоматолог
		default :
			$price = 800;
	}

	return $price;
}

?>
