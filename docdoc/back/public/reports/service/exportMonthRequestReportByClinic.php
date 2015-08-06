<?php
use dfs\docdoc\helpers\DomHelper;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../php/reportLib.php";
require_once dirname(__FILE__) . "/../../lib/php/dateTimeLib.php";
require_once dirname(__FILE__) . "/../../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../../lib/php/serviceFunctions.php";
require_once dirname(__FILE__) . "/../../lib/php/style4Excel.php"; // стили для ячеек


$crDateFrom =
	(isset($_GET["crDateShFrom"])) ? checkField($_GET["crDateShFrom"], "t", "01." . date("m.Y")) : "01." . date("m.Y");
$crDateTill = (isset($_GET["crDateShTill"])) ? checkField($_GET["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");
$clinicList = (isset($_GET["clinicList"])) ? $_GET["clinicList"] : array();

$params = array();
$params['dateReciveFrom'] = $crDateFrom;
$params['dateReciveTill'] = $crDateTill;
$city = getCityId();

$xmlString = "<Root>";

$xmlString .= getClinicLisFromArrayXML($clinicList);

$monthArray = monthBetweenTwoDate($crDateFrom, $crDateTill);
$dateStartArr = explode(".", $crDateFrom);
$dateEndArr = explode(".", $crDateTill);
$lastDay = date("t", strtotime($crDateTill));

$xmlString .= '<ClinicReports>';
foreach ($clinicList as $clinic) {
	$clinic = intval($clinic);

	$xmlString .= '<Clinic id="' . $clinic . '">';
	foreach ($monthArray as $line => $data) {
		if ($data[0] == '01' . "." . $dateStartArr[1] . "." . $dateStartArr[2]) {
			$startDate = $dateStartArr[0] . "." . $dateStartArr[1] . "." . $dateStartArr[2];
		} else {
			$startDate = $data[0];
		}

		if ($data[1] == $lastDay . "." . $dateEndArr[1] . "." . $dateEndArr[2]) {
			$endDate = $dateEndArr[0] . "." . $dateEndArr[1] . "." . $dateEndArr[2];
		} else {
			$endDate = $data[1];
		}

		$xmlString .= "<Report>";
		$xmlString .= "<StartDate>" . $startDate . "</StartDate>";
		$xmlString .= "<EndDate>" . $endDate . "</EndDate>";
		$month = date("m", strtotime($data[0]));
		$xmlString .= "<Month id=\"" . $month . "\">" . getRusMonth($month) . "</Month>";

		$xmlString .= "<Total>" . getRequestCount($clinic, 'total', $startDate, $endDate, true, $city) . "</Total>";
		$xmlString .=
			"<Transfer>" . getRequestCount($clinic, 'transfer', $startDate, $endDate, true, $city) . "</Transfer>";
		$xmlString .=
			"<Apointment>" .
			getRequestCount($clinic, 'apointment', $startDate, $endDate, true, $city) .
			"</Apointment>";
		$xmlString .=
			"<Complete>" . getRequestCount($clinic, 'complete', $startDate, $endDate, true, $city) . "</Complete>";
		$xmlString .= "</Report>";
	}

	$xmlString .= '</Clinic>';
}

$xmlString .= '</ClinicReports>';

$xmlString .= "</Root>";

$doc = new DOMDocument('1.0', 'UTF-8');
if ($doc->loadXML($xmlString)) {
	$xml = new SimpleXMLElement($xmlString);
} else {
	echo "не XML";
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Docdoc.ru")->setTitle("Request analize by month for clinic");
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', "Анализ обращений по месяцам для клиник");
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($Head);
$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A2', "Дата с " . $crDateFrom . " по " . $crDateTill);
$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');

$GR_DELTA = 3;
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A' . $GR_DELTA, "Месяц")
	->setCellValue('B' . $GR_DELTA, "Всего обращений")
	->setCellValue('C' . $GR_DELTA, "Переведенных")
	->setCellValue('E' . $GR_DELTA, "Записаных")
	->setCellValue('G' . $GR_DELTA, "Дошедших");

$objPHPExcel->getActiveSheet()->mergeCells('C' . $GR_DELTA . ':D' . $GR_DELTA);
$objPHPExcel->getActiveSheet()->mergeCells('E' . $GR_DELTA . ':F' . $GR_DELTA);
$objPHPExcel->getActiveSheet()->mergeCells('G' . $GR_DELTA . ':H' . $GR_DELTA);

$objPHPExcel->getActiveSheet()->getRowDimension($GR_DELTA)->setRowHeight(40);

$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('D' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('E' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('G' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('H' . $GR_DELTA)->applyFromArray($TH);

$ClinicLine = $xml->ClinicReports;
$cl = $xml->ClinicList;

if ($ClinicLine) {
	foreach ($ClinicLine->Clinic as $GrItem) {

		$attr = $GrItem->attributes();
		$id = $attr['id'];
		$name = DomHelper::searchElt($cl, "id", $id);
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . ($GR_DELTA + 1), $name);
		$objPHPExcel->getActiveSheet()->mergeCells('A' . ($GR_DELTA + 1) . ':J' . ($GR_DELTA + 1));
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($GR_DELTA + 1))->applyFromArray($strong);
		$objPHPExcel->getActiveSheet()->getRowDimension($GR_DELTA + 1)->setRowHeight(30);

		if ($GrItem->Report) {
			$i = 1;
			$DELTA = 1;
			$grTotal = $grTransfer = $grApointment = $grComplete = 0;
			foreach ($GrItem->Report as $item) {

				$TOTAL = intval($item->Total);
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A' . ($i + $GR_DELTA + $DELTA), strval($item->Month))
					->setCellValue('B' . ($i + $GR_DELTA + $DELTA), strval($item->Total))
					->setCellValue('C' . ($i + $GR_DELTA + $DELTA), strval($item->Transfer))
					->setCellValue(
						'D' . ($i + $GR_DELTA + $DELTA),
						($TOTAL > 0) ? intval($item->Transfer) / intval($item->Total) : ""
					)
					->setCellValue('E' . ($i + $GR_DELTA + $DELTA), strval($item->Apointment))
					->setCellValue(
						'F' . ($i + $GR_DELTA + $DELTA),
						($TOTAL > 0) ? intval($item->Apointment) / intval($item->Total) : ""
					)
					->setCellValue('G' . ($i + $GR_DELTA + $DELTA), strval($item->Complete))
					->setCellValue(
						'H' . ($i + $GR_DELTA + $DELTA),
						($TOTAL > 0) ? intval($item->Complete) / intval($item->Total) : ""
					);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + $GR_DELTA + $DELTA))->applyFromArray($TH);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle('D' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle('H' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

				$grTotal += intval($item->Total);
				$grTransfer += intval($item->Transfer);
				$grApointment += intval($item->Apointment);
				$grComplete += intval($item->Complete);
				$i++;
			}
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . ($i + $GR_DELTA + $DELTA), "ИТОГО:")
				->setCellValue('B' . ($i + $GR_DELTA + $DELTA), $grTotal)
				->setCellValue('C' . ($i + $GR_DELTA + $DELTA), $grTransfer)
				->setCellValue('D' . ($i + $GR_DELTA + $DELTA), "")
				->setCellValue('E' . ($i + $GR_DELTA + $DELTA), $grApointment)
				->setCellValue('F' . ($i + $GR_DELTA + $DELTA), "")
				->setCellValue('G' . ($i + $GR_DELTA + $DELTA), $grComplete)
				->setCellValue('H' . ($i + $GR_DELTA + $DELTA), "");
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + $GR_DELTA + $DELTA))->applyFromArray($strong);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle(
				'A' . ($i + $GR_DELTA + $DELTA) . ":H" . ($i + $GR_DELTA + $DELTA)
			)->applyFromArray($strong);
		}

		$GR_DELTA += $i + 2;
	}
}

$objPHPExcel->getActiveSheet()->setTitle('Report');
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);

$file = "monthRequestReport_" . $crDateFrom . "_" . $crDateTill . ".xls";
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
