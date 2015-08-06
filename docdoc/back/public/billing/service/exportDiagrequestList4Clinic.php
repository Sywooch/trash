<?php
use dfs\docdoc\helpers\DomHelper;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/commonDict.php";
require_once dirname(__FILE__) . "/../php/reportLib.php";
require_once dirname(__FILE__) . "/../../lib/php/models/diag_request.class.php";
require_once dirname(__FILE__) . "/../../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../../lib/php/serviceFunctions.php";
require_once dirname(__FILE__) . "/../../lib/php/style4Excel.php"; // стили для ячеек

$crDateFrom =
	(isset($_GET["dateFrom"])) ? checkField($_GET["dateFrom"], "t", "01." . date("m.Y")) : "01." . date("m.Y");
$crDateTill = (isset($_GET["dateTill"])) ? checkField($_GET["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
$clinic = (isset($_GET["shClinicId"])) ? checkField($_GET["shClinicId"], "i", '') : '';

//var_dump ($_GET); exit;

$params = array();

$xmlString = "<Root>";
$xmlString .= "<StartDate>" . $crDateFrom . "</StartDate>";
$xmlString .= "<EndDate>" . $crDateTill . "</EndDate>";

$currentClinic = new Clinic();
$currentClinic->getClinic($clinic);
$xmlString .= "<Title><![CDATA[" . $currentClinic->title . "]]></Title>";
$xmlString .= "<Settings>" . arrayToXML($currentClinic->getDiagnosticaSettings()) . "</Settings>";

$params['startPage'] = 0;
$params['withPager'] = false;
$params['clinicId'] = $clinic;
//$params['crDateFrom']	= $crDateFrom;
//$params['crDateTill']	= $crDateTill;

$contractId = (!empty($currentClinic->diagSettings['contractId'])) ? $currentClinic->diagSettings['contractId'] : 0;
$params['contractId'] = $contractId;

switch ($contractId) {
	case 3 :
	{
		$params['crDateFrom'] = $crDateFrom;
		$params['crDateTill'] = $crDateTill;
		$price = 200;
	}
		break;
	case 4 :
	{
		$params['crDateFrom'] = $crDateFrom;
		$params['crDateTill'] = $crDateTill;
		$price = 400;
	}
		break;
	case 5 :
	{
		$params['dateReciveFrom'] = $crDateFrom;
		$params['dateReciveTill'] = $crDateTill;
		$price = 800;
	}
		break;
	default :
		{
		$params['crDateFrom'] = $crDateFrom;
		$params['crDateTill'] = $crDateTill;
		$price = 0;
		}
}

$xmlString .= "<Price>" . $price . "</Price>";

$xmlString .= getDiagRequestListByContract4BillingXML($params);
$xmlString .= '<StatusDict>' . arrayToXML(getStatusDict()) . '</StatusDict>';

$xmlString .= "</Root>";

$doc = new DOMDocument('1.0', 'UTF-8');
if ($doc->loadXML($xmlString)) {
	$xml = new SimpleXMLElement($xmlString);
} else {
	echo "не XML";
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Docdoc.ru")->setTitle("Diagnostic Request for clinic");
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', $xml->Title);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($Head);
$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A2', "Дата отчёта с " . $crDateFrom . " по " . $crDateTill);
$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');

$DELTA = 5;
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A' . $DELTA, "№")
	->setCellValue('B' . $DELTA, "Дата создания заявки")
	->setCellValue('C' . $DELTA, "Пациент")
	->setCellValue('D' . $DELTA, "Телефон")
	->setCellValue('E' . $DELTA, "Записан на дату")
	->setCellValue('F' . $DELTA, "Диагностика");
$objPHPExcel->getActiveSheet()->getStyle('A' . $DELTA . ':G' . $DELTA)->applyFromArray($TH);
$objPHPExcel->getActiveSheet()->getStyle('A' . $DELTA . ':G' . $DELTA)->getAlignment()->setWrapText(true);

$i = 1;
$LineList = $xml->DiagRequestList;
if ($LineList) {
	foreach ($LineList->Element as $item) {
		$diagnosticaName =
			(!empty($item->DiagnosticaName)) ? strval($item->DiagnosticaName) : strval($item->DiagnosticaFullName);
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . ($i + $DELTA), strval($item->Id))
			->setCellValue('B' . ($i + $DELTA), strval($item->CrDate) . " " . strval($item->CrTime))
			->setCellValue('C' . ($i + $DELTA), strval($item->Client))
			->setCellValue('D' . ($i + $DELTA), strval($item->PhoneFrom) . " " . strval($item->PhoneFromAdd))
			->setCellValue('E' . ($i + $DELTA), strval($item->AdmDate) . " " . strval($item->AdmTime))
			->setCellValue('F' . ($i + $DELTA), $diagnosticaName)
			->setCellValue('G' . ($i + $DELTA), $price)
			->setCellValue('H' . ($i + $DELTA), DomHelper::searchElt($xml->StatusDict, 'id', $item->Status)->Title);
		if ($i % 2 == 0) {
			$objPHPExcel->getActiveSheet()->getStyle('A' . ($i + $DELTA) . ":G" . ($i + $DELTA))->applyFromArray($odd);
		}

		$i++;
	}
}

$objPHPExcel->getActiveSheet()->getStyle('A' . ($DELTA + 1) . ':H' . ($i + $DELTA - 1))->getAlignment()->setWrapText(
	true
);
$objPHPExcel->getActiveSheet()->getStyle('A' . ($DELTA + 1) . ':G' . ($i + $DELTA - 1))->applyFromArray($wb);
$objPHPExcel->getActiveSheet()->getStyle("A" . ($DELTA + 1) . ":H" . ($i + $DELTA - 1))->getAlignment()->setVertical(
	PHPExcel_Style_Alignment::VERTICAL_CENTER
);

/*	Итого	*/
$objPHPExcel->getActiveSheet()->mergeCells('A' . ($i + $DELTA) . ':F' . ($i + $DELTA));
$objPHPExcel->getActiveSheet()->setCellValue('G' . ($i + $DELTA), '=SUM(G6:G' . ($i + $DELTA - 1) . ')');
$objPHPExcel->getActiveSheet()->getStyle('G' . ($i + $DELTA))->applyFromArray($strong);
$objPHPExcel->getActiveSheet()->getStyle('G' . ($i + $DELTA))->getNumberFormat()->setFormatCode("# ##0");

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A3', "Количество записей: " . ($i - 1));
$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');

$objPHPExcel->getActiveSheet()->setTitle('Report');
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

$file = "diagnosticRequestReport_clinic_" . $clinic . "_" . $crDateFrom . "_" . $crDateTill . ".xls";
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
