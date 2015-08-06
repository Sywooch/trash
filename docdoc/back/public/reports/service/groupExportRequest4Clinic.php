<?php
use dfs\docdoc\helpers\DomHelper;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../../lib/php/dateTimeLib.php";
require_once dirname(__FILE__) . "/../php/reportLib.php";
require_once dirname(__FILE__) . "/../../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../../lib/php/style4Excel.php"; // стили для ячеек


$clinicList = (isset($_POST["line"])) ? $_POST["line"] : array();

$crDateFrom = (isset($_POST["dateFrom"])) ? checkField($_POST["dateFrom"], "t", date("d.m.Y")) : date("d.m.Y");
$crDateTill = (isset($_POST["dateTill"])) ? checkField($_POST["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
$clinicId = (isset($_POST["shClinicId"])) ? checkField($_POST["shClinicId"], "i", '') : '';
$shBranch = (isset($_POST["shBranch"])) ? checkField($_POST["shBranch"], "i", 0) : 0;
$status = (isset($_POST["shStatus"])) ? checkField($_POST["shStatus"], "i", 0) : 0;

$sortBy = (isset($_POST['sortBy'])) ? checkField($_POST['sortBy'], "t", "") : ''; // Сортировка
$sortType = (isset($_POST['sortType'])) ? checkField($_POST['sortType'], "t", "") : ''; // Сортировка

if (count($clinicList) > 0) {
	$report = "";
	$crDate = date("Ymd");
	$archiveName = $crDate . "_group_report_4_clinics_" . $crDateFrom . "_" . $crDateTill . ".zip";
	$archivePath = dirname(__FILE__) . "/../../_reports/" . $archiveName;

	$zip = new ZipArchive();
	if ($zip->open($archivePath, ZIPARCHIVE::CREATE) === true) {

		foreach ($clinicList as $clinic) {
			$clinicId = checkField($clinic, "i", '');

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
			$params['kind'] = DocRequest::KIND_DOCTOR;

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

			$fileName = getFileReport($params);
			$zip->addFile($fileName, "RequestReport_" . $clinicId . "_" . $crDateFrom . "_" . $crDateTill . ".xls");
		}

		$zip->close();

		$data = file_get_contents($archivePath);
		$size = filesize($archivePath);

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: max-age=0, must-revalidate, post-check=0, pre-check=0");
		header("Content-Description: PHP Generated ZIP Data");
		header('Content-Type: application/octetstream');
		header("Content-Type: application/octet-stream");
		header("Content-Length: " . $size);
		header("Content-Disposition: attachment;filename={$archiveName}");
		header("Content-Transfer-Encoding: binary");

		print $data;
		@unlink($archivePath);
		exit;
	}
}

function getFileReport($params = array())
{
	global $Head, $TH;

	$xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
	$xmlString .= "<dbInfo>";
	$xmlString .= getRequestListXML($params, getCityId());
	if ($params['clinic'] > 0) {
		$xmlString .= getClinicListByIdWithBranchesXML($params['clinic']);
	}
	$xmlString .= getStatus4RequestXML();
	$xmlString .= '</dbInfo>';

	$doc = new DOMDocument('1.0', 'UTF-8');
	if ($doc->loadXML($xmlString)) {
		$xml = new SimpleXMLElement($xmlString);

	} else {
		return "";
	}

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Docdoc.ru")->setTitle("Request report");
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', "Отчёт за период с " . $params['dateReciveFrom'] . " по " . $params['dateReciveTill']);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($Head);
	$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');

	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A5', "#")
		->setCellValue('B5', "Запрос")
		->setCellValue('C5', "Дата визита")
		->setCellValue('D5', "Врач")
		->setCellValue('E5', "Специальность")
		->setCellValue('F5', "Стоимость, руб")
		->setCellValue('G5', "Пациент")
		->setCellValue('H5', "Телефон")
		->setCellValue('I5', "Клиника")
		->setCellValue('J5', "Статус");

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

	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);

	$statusArray = getStatusArray();

	$TOTAL = 0;
	$lineCount = 0;
	$LineList = $xml->RequestList;
	if ($LineList) {
		$i = 1;
		$DELTA = 5;
		$TOTAL = 0;
		foreach ($LineList->Element as $item) {
			if (intval($item->Status) != 5) {
				$sector = $item->Sector->attributes();
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A' . ($i + $DELTA), ($i))
					->setCellValue('B' . ($i + $DELTA), strval($item->Id))
					->setCellValue(
						'C' . ($i + $DELTA),
						strval($item->AppointmentDate) . " " . strval($item->AppointmentTime)
					)
					->setCellValue('D' . ($i + $DELTA), strval($item->Doctor))
					->setCellValue('E' . ($i + $DELTA), strval($item->Sector))
					->setCellValue('F' . ($i + $DELTA), strval($item->Price))
					->setCellValue('G' . ($i + $DELTA), strval($item->Client))
					->setCellValue('H' . ($i + $DELTA), strval($item->ClientPhone))
					->setCellValue(
						'I' . ($i + $DELTA),
						DomHelper::searchElt($xml->ClinicList, "id", strval($item->ClinicId))->Name
					)
					->setCellValue('J' . ($i + $DELTA), $statusArray[strval($item->Status)]);
				$i++;
				$lineCount++;
				$TOTAL += floatval(strval($item->Price));
			}
		}
	}

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "Всего пациентов: " . $lineCount);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', "Общая стоимость, руб: " . $TOTAL);

	$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');

	$objPHPExcel->getActiveSheet()->setTitle('Report');
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(7);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

	$file =
		"RequestReport_" . $params['clinic'] . "_" . $params['dateReciveFrom'] . "_" .
		$params['dateReciveTill'] .
		".xls";
	$filename = dirname(__FILE__) . "/../../_reports/" . $file;

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($filename);
	chmod($filename, FILE_MODE);

	return $filename;
}

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
