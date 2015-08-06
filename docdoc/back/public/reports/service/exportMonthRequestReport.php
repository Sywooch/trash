<?php
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../php/reportLib.php";
require_once dirname(__FILE__) . "/../../lib/php/dateTimeLib.php";
require_once dirname(__FILE__) . "/../../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../../lib/php/serviceFunctions.php";
require_once dirname(__FILE__) . "/../../lib/php/style4Excel.php"; // стили для ячеек


$crDateFrom = (isset($_GET["crDateShFrom"])) ? checkField($_GET["crDateShFrom"], "t", "01." . date("m.Y")) : "01." . date("m.Y");
$crDateTill = (isset($_GET["crDateShTill"])) ? checkField($_GET["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");


$params = array();
$params['dateReciveFrom'] = $crDateFrom;
$params['dateReciveTill'] = $crDateTill;
$city = getCityId();


$xmlString = "<Root>";
$xmlString .= "<Reports>";
$monthArray = monthBetweenTwoDate($crDateFrom, $crDateTill);
$dateStartArr = explode(".", $crDateFrom);
$dateEndArr = explode(".", $crDateTill);
$lastDay = date("t", strtotime($crDateTill));

foreach ($monthArray as $line => $data) {
	$xmlString .= "<Report>";
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

	$xmlString .= "<StartDate>" . $startDate . "</StartDate>";
	$xmlString .= "<EndDate>" . $endDate . "</EndDate>";
	$month = date("m", strtotime($data[0]));
	$xmlString .= "<Month id=\"" . $month . "\">" . getRusMonth($month) . "</Month>";

	$xmlString .= "<Total>" . getRequestCount(0, 'total', $startDate, $endDate, false, $city) . "</Total>";
	$xmlString .= "<Transfer>" . getRequestCount(0, 'transfer', $startDate, $endDate, false, $city) . "</Transfer>";
	$xmlString .= "<Apointment>" . getRequestCount(0, 'apointment', $startDate, $endDate, false, $city) . "</Apointment>";
	$xmlString .= "<Complete>" . getRequestCount(0, 'complete', $startDate, $endDate, false, $city) . "</Complete>";
	$xmlString .= "<ThisPeriodComplete>" . getRequestCount(0, 'this_period_complete', $startDate, $endDate, false, $city) . "</ThisPeriodComplete>";


	$contracts = getContractTypeDict();
	if (count($contracts) > 0) {
		$xmlString .= "<Contracts>";
		foreach ($contracts as $key => $val) {
			$xmlString .= "<Contract id=\"" . $val["id"] . "\">";
			$xmlString .= arrayToXML($val);
			$xmlString .= "<Total>" . getSummaryRequestCount('total', $startDate, $endDate, $city, $val['id']) . "</Total>";
			$xmlString .= "<Transfer>" . getSummaryRequestCount('transfer', $startDate, $endDate, $city, $val['id']) . "</Transfer>";
			$xmlString .= "<Apointment>" . getSummaryRequestCount('apointment', $startDate, $endDate, $city, $val['id']) . "</Apointment>";
			$xmlString .= "<Complete>" . getSummaryRequestCount('complete', $startDate, $endDate, $city, $val['id']) . "</Complete>";
			$xmlString .= "<ThisPeriodComplete>" . getSummaryRequestCount('this_period_complete', $startDate, $endDate, $city, $val['id']) . "</ThisPeriodComplete>";
			$xmlString .= "</Contract>";
		}
		$xmlString .= "</Contracts>";
	}

	$xmlString .= "</Report>";
}
$xmlString .= "</Reports>";
$xmlString .= "</Root>";


$doc = new DOMDocument('1.0', 'UTF-8');
if ($doc ->loadXML($xmlString)) {
	$xml = new SimpleXMLElement($xmlString);
} else {
	echo "не XML";
}


$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Docdoc.ru") ->setTitle("Request analize by month");
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', "Анализ обращений по месяцам");
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($Head);
$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A2', "Дата с " . $crDateFrom . " по " . $crDateTill);
$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');


$GR_DELTA = 3;
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A' . $GR_DELTA, "Месяц")
	->setCellValue('B' . $GR_DELTA, "Всего обращений")
	->setCellValue('C' . $GR_DELTA, "Переведенных")
	->setCellValue('E' . $GR_DELTA, "Записаных")
	->setCellValue('G' . $GR_DELTA, "Дошедших  (в этот месяц всего)")
	->setCellValue('I' . $GR_DELTA, "Дошедших (из обратившихся)");

$objPHPExcel->getActiveSheet()->mergeCells('C' . $GR_DELTA . ':D' . $GR_DELTA);
$objPHPExcel->getActiveSheet()->mergeCells('E' . $GR_DELTA . ':F' . $GR_DELTA);
$objPHPExcel->getActiveSheet()->mergeCells('G' . $GR_DELTA . ':H' . $GR_DELTA);
$objPHPExcel->getActiveSheet()->mergeCells('I' . $GR_DELTA . ':J' . $GR_DELTA);

$objPHPExcel->getActiveSheet()->getStyle('G' . $GR_DELTA . ':H' . $GR_DELTA)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('I' . $GR_DELTA . ':J' . $GR_DELTA)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getRowDimension($GR_DELTA)->setRowHeight(40);

$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('D' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('E' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('G' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('H' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('I' . $GR_DELTA)->applyFromArray($TH);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('J' . $GR_DELTA)->applyFromArray($TH);


$LineList = $xml ->Reports;
if ($LineList) {
	$i = 1;
	$DELTA = 0;
	$grTotal = $grTransfer = $grApointment = $grComplete = $grCompleteFull = 0;
	foreach ($LineList ->Report as $item) {

		$TOTAL = intval($item ->Total);
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . ($i + $DELTA + $GR_DELTA), strval($item ->Month))
			->setCellValue('B' . ($i + $DELTA + $GR_DELTA), strval($item ->Total))
			->setCellValue('C' . ($i + $DELTA + $GR_DELTA), strval($item ->Transfer))
			->setCellValue('D' . ($i + $DELTA + $GR_DELTA), ($TOTAL > 0) ? intval($item ->Transfer) / intval($item ->Total) : "")
			->setCellValue('E' . ($i + $DELTA + $GR_DELTA), strval($item ->Apointment))
			->setCellValue('F' . ($i + $DELTA + $GR_DELTA), ($TOTAL > 0) ? intval($item ->Apointment) / intval($item ->Total) : "")
			->setCellValue('G' . ($i + $DELTA + $GR_DELTA), strval($item ->Complete))
			->setCellValue('H' . ($i + $DELTA + $GR_DELTA), ($TOTAL > 0) ? intval($item ->Complete) / intval($item ->Total) : "")
			->setCellValue('I' . ($i + $DELTA + $GR_DELTA), strval($item ->ThisPeriodComplete))
			->setCellValue('J' . ($i + $DELTA + $GR_DELTA), ($TOTAL > 0) ? intval($item ->ThisPeriodComplete) / intval($item ->Total) : "");
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + $DELTA + $GR_DELTA))->applyFromArray($TH);

		$objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . ($i + $DELTA + $GR_DELTA) . ':J' . ($i + $DELTA + $GR_DELTA))->applyFromArray($odd);


		$objPHPExcel->setActiveSheetIndex(0)->getStyle('D' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('H' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
		$objPHPExcel->setActiveSheetIndex(0)->getStyle('J' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

		$grTotal += intval($item ->Total);
		$grTransfer += intval($item ->Transfer);
		$grApointment += intval($item ->Apointment);
		$grComplete += intval($item ->Complete);
		$grCompleteFull += intval($item ->ThisPeriodComplete);
		$i++;

		foreach ($item ->Contracts ->Contract as $line) {
			$TOTAL = intval($line ->Total);
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . ($i + $DELTA + $GR_DELTA), strval($line ->Title))
				->setCellValue('B' . ($i + $DELTA + $GR_DELTA), strval($line ->Total))
				->setCellValue('C' . ($i + $DELTA + $GR_DELTA), strval($line ->Transfer))
				->setCellValue('D' . ($i + $DELTA + $GR_DELTA), ($TOTAL > 0) ? intval($line ->Transfer) / intval($line ->Total) : "")
				->setCellValue('E' . ($i + $DELTA + $GR_DELTA), strval($line ->Apointment))
				->setCellValue('F' . ($i + $DELTA + $GR_DELTA), ($TOTAL > 0) ? intval($line ->Apointment) / intval($line ->Total) : "")
				->setCellValue('G' . ($i + $DELTA + $GR_DELTA), strval($line ->Complete))
				->setCellValue('H' . ($i + $DELTA + $GR_DELTA), ($TOTAL > 0) ? intval($line ->Complete) / intval($line ->Total) : "")
				->setCellValue('I' . ($i + $DELTA + $GR_DELTA), strval($line ->ThisPeriodComplete))
				->setCellValue('J' . ($i + $DELTA + $GR_DELTA), ($TOTAL > 0) ? intval($line ->ThisPeriodComplete) / intval($line ->Total) : "");

			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + $DELTA + $GR_DELTA))->applyFromArray($moveRight3);


			$objPHPExcel->setActiveSheetIndex(0)->getStyle('D' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('H' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle('J' . ($i + $DELTA + $GR_DELTA))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

			$i++;
		}

	}
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . ($i + $DELTA + $GR_DELTA), "ИТОГО:")
		->setCellValue('B' . ($i + $DELTA + $GR_DELTA), $grTotal)
		->setCellValue('C' . ($i + $DELTA + $GR_DELTA), $grTransfer)
		->setCellValue('D' . ($i + $DELTA + $GR_DELTA), "")
		->setCellValue('E' . ($i + $DELTA + $GR_DELTA), $grApointment)
		->setCellValue('F' . ($i + $DELTA + $GR_DELTA), "")
		->setCellValue('G' . ($i + $DELTA + $GR_DELTA), $grComplete)
		->setCellValue('H' . ($i + $DELTA + $GR_DELTA), "")
		->setCellValue('I' . ($i + $DELTA + $GR_DELTA), $grCompleteFull)
		->setCellValue('J' . ($i + $DELTA + $GR_DELTA), "");
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + $DELTA + $GR_DELTA))->applyFromArray($TH);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . ($i + $DELTA + $GR_DELTA) . ':J' . ($i + $DELTA + $GR_DELTA))->applyFromArray($odd);

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
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);


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
