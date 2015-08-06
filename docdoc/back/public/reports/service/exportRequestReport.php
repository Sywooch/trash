<?php
use dfs\docdoc\helpers\DomHelper;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../php/reportLib.php";
require_once dirname(__FILE__) . "/../../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../../lib/php/style4Excel.php"; // стили для ячеек


$crDateFrom = (isset($_GET["dateFrom"])) ? checkField($_GET["dateFrom"], "t", date("d.m.Y")) : date("d.m.Y");
$crDateTill = (isset($_GET["dateTill"])) ? checkField($_GET["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
$clinicId = (isset($_GET["shClinicId"])) ? checkField($_GET["shClinicId"], "i", '') : '';
$shBranch = (isset($_GET["shBranch"])) ? checkField($_GET["shBranch"], "i", 0) : 0;
$status = (isset($_GET["shStatus"])) ? checkField($_GET["shStatus"], "i", 0) : 0;

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
$params['withPager'] = false;
$params['withPrice'] = true;

switch ($status) {
	case 1 :
	{
		$params['isTransfer'] = "1";
		$params['crDateFrom'] = $crDateFrom;
		$params['crDateTill'] = $crDateTill;
		$params['dateReciveFrom'] = "";
		$params['dateReciveTill'] = "";
	}
		break;
	// Записано
	case 2 :
	{
		$params['crDateFrom'] = "";
		$params['crDateTill'] = $crDateTill;
		$params['dateReciveFrom'] = $crDateFrom;
		$params['dateReciveTill'] = date('d.m.Y', strtotime($crDateTill) + 3600 * 24 * 90);
	}
		break;
	case 3 :
		$params['status'] = "3";
		break;
	case 4 :
		$params['status'] = "8";
		break;
	case 5 :
		$params['status'] = "9";
		break;
}

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
$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A5', "#")
	->setCellValue('B5', "Запрос")
	->setCellValue('C5', "Дата создания")
	->setCellValue('D5', "Дата визита")
	->setCellValue('E5', "Врач")
	->setCellValue('F5', "Специальность")
	->setCellValue('G5', "Стоимость, руб")
	->setCellValue('H5', "Пациент")
	->setCellValue('I5', "Телефон")
	->setCellValue('J5', "Клиника")
	->setCellValue('K5', "Статус");

$objPHPExcel->setActiveSheetIndex(0)->getStyle('A5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('B5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('D5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('E5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('F5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('G5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('H5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('I5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('J5')->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('K5')->applyFromArray($TH);

$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);


$TOTAL = 0;
$lineCount = 0;
$statusArray = getStatusArray();

$LineList = $xml->RequestList;
if ($LineList) {
	$i = 1;
	$DELTA = 5;
	$TOTAL = 0;
	foreach ($LineList->Element as $item) {
		//if ( intval($item -> Status) != 5) {
		$sector = $item->Sector->attributes();
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . ($i + $DELTA), ($i))
			->setCellValue('B' . ($i + $DELTA), strval($item->Id))
			->setCellValue('C' . ($i + $DELTA), strval($item->CrDate) . " " . strval($item->CrTime))
			->setCellValue('D' . ($i + $DELTA), strval($item->AppointmentDate) . " " . strval($item->AppointmentTime))
			->setCellValue('E' . ($i + $DELTA), strval($item->Doctor))
			->setCellValue('F' . ($i + $DELTA), strval($item->Sector))
			->setCellValue('G' . ($i + $DELTA), strval($item->Price))
			->setCellValue('H' . ($i + $DELTA), strval($item->Client))
			->setCellValue('I' . ($i + $DELTA), strval($item->ClientPhone))
			->setCellValue(
				'J' . ($i + $DELTA),
				DomHelper::searchElt($xml->ClinicList, "id", strval($item->ClinicId))->Name
			)
			->setCellValue('K' . ($i + $DELTA), $statusArray[strval($item->Status)]);
		$i++;
		$lineCount++;
		$TOTAL += floatval(strval($item->Price));
		//}
	}
}

//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2',"Всего пациентов: ".count($xml -> RequestList ->  Element ));
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "Всего пациентов: " . $lineCount);
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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);


$file = "RequestReport_" . $crDateFrom . "_" . $crDateTill . ".xls";
$filename = dirname(__FILE__) . "/../../_reports/" . $file;

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
