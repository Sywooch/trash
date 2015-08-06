<?php
use dfs\docdoc\helpers\DomHelper;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../php/reportLib.php";
require_once dirname(__FILE__) . "/../../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../../lib/php/translit.php";
require_once dirname(__FILE__) . "/../../lib/php/style4Excel.php"; // стили для ячеек

$clinicId = isset($_GET["shClinicId"]) ? checkField($_GET["shClinicId"], "i", '') : '';
$cityId = isset($_GET["shCityId"]) ? checkField($_GET["shCityId"], "i", 1) : 1;
$shBranch = isset($_GET["shBranch"]) ? checkField($_GET["shBranch"], "i", 0) : 0;
$dataType =
	isset($_GET["dataType"]) ? checkField($_GET["dataType"], "e", 'short', false, array('full', 'short')) : 'short';
$statusList = isset($_GET["status"]) ? $_GET["status"] : array();

$shDepart = (isset($_GET["shDepart"])) ? checkField($_GET["shDepart"], "i", 0) : 0;

$sortBy = isset($_GET['sortBy']) ? checkField($_GET['sortBy'], "t", "") : ''; // Сортировка
$sortType = isset($_GET['sortType']) ? checkField($_GET['sortType'], "t", "") : ''; // Сортировка

$params = array();
if ($clinicId == '') {
	$clinicId = 0;
}
$params['departure'] = $shDepart;
//$params['status']		= $status;
unset($statusList['all']);
$statusList = array_diff($statusList, array("all"));
$params['statusList'] = $statusList;
$params['clinic'] = $clinicId;
$params['branch'] = $shBranch;
$params['withPager'] = false;

//	$params['status']		= $status;

// Сортировка
if (!empty($sortBy)) {
	$params['sortBy'] = $sortBy;
}
if (!empty($sortBy)) {
	$params['sortType'] = $sortType;
}

$xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
$xmlString .= "<dbInfo>";

$xmlString .= getDoctorListReportXML($params, $cityId);
$xmlString .= getStatusDictXML();

$clinicName = "";
$clinicNameFullName = "";
if ($clinicId > 0) {
	$clinic = new Clinic($clinicId);
	$clinicName = (isset($clinic->data['short_name'])) ? translit($clinic->data['short_name']) : "";
	$clinicNameFullName = (isset($clinic->data['name'])) ? $clinic->data['name'] : "";
}

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
$objPHPExcel->getProperties()->setCreator("Docdoc.ru")->setTitle("Doctor list report");

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A5', "#")
	->setCellValue('B5', "Врач")
	->setCellValue('C5', "Специальность")
	->setCellValue('D5', "Стоимость, руб")
	->setCellValue('E5', "Спец.цена, руб")
	->setCellValue('F5', "Клиника (филиал)")
	->setCellValue('G5', "Статус")
	->setCellValue('H5', "Выезд на дом")
	->setCellValue('I5', "Фото")
	->setCellValue('J5', "Год начала практики")
	->setCellValue('K5', "Образование")
	->setCellValue('L5', "Детский врач");

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
$objPHPExcel->setActiveSheetIndex(0)->getStyle('L5')->applyFromArray($TH);

//$objPHPExcel->setActiveSheetIndex(0)->getStyle('A5:K5')->applyFromArray($TH);
$objPHPExcel->getActiveSheet()->getStyle("A5:L5")->getAlignment()->setWrapText(true);

//$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);

$LineList = $xml->DoctorList;
if ($LineList) {
	$i = 1;
	$DELTA = 5;
	if (count($LineList->Element) > 0) {
		foreach ($LineList->Element as $item) {
			$sector = $item->Sector->attributes();

			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + $DELTA) . ":K" . ($i + $DELTA))->applyFromArray(
				$general
			);
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . ($i + $DELTA), ($i))
				->setCellValue('B' . ($i + $DELTA), strval($item->Name))
				->setCellValue('C' . ($i + $DELTA), toList($item->SectorList, 'Sector', ','))
				->setCellValue('D' . ($i + $DELTA), strval($item->Price))
				->setCellValue('E' . ($i + $DELTA), strval($item->SpecialPrice))
				->setCellValue('F' . ($i + $DELTA), strval($item->Clinic))
				->setCellValue(
					'G' . ($i + $DELTA),
					($item->Status == 7)
						? "Нет анкеты"
						: DomHelper::searchElt(
						$xml->StatusDict,
						"id",
						strval($item->Status)
					)
				)
				->setCellValue('H' . ($i + $DELTA), ($item->IsDeparture == 1) ? "Да" : "")
				->setCellValue('I' . ($i + $DELTA), (!empty($item->Image)) ? "Да" : "Нет")
				->setCellValue('J' . ($i + $DELTA), ($item->ExperienceYear > 0) ? strval($item->ExperienceYear) : "Нет")
				->setCellValue('K' . ($i + $DELTA), ($item->IsEducation == 'yes') ? "Да" : "Нет")
				->setCellValue('L' . ($i + $DELTA), ($item->IsKidsReception == "1") ? "Да" : "");

			$objPHPExcel->getActiveSheet()->getCell('B' . ($i + $DELTA))->getHyperlink()->setUrl($item->Url);

			if ($item->Status == 7) {
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("G" . ($i + $DELTA))->applyFromArray($red);
			}

			if (empty($item->Image)) {
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("I" . ($i + $DELTA))->applyFromArray($red);
			}
			if (empty($item->ExperienceYear) || $item->ExperienceYear == 0) {
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("J" . ($i + $DELTA))->applyFromArray($red);
			}
			if ($item->IsEducation != 'yes') {
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("K" . ($i + $DELTA))->applyFromArray($red);
			}

			$objPHPExcel->setActiveSheetIndex(0)->getStyle("G" . ($i + $DELTA) . ":L" . ($i + $DELTA))->applyFromArray(
				$center
			);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle("D" . ($i + $DELTA))->applyFromArray($center);

			if ($i % 2 == 0) {
				$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + $DELTA) . ":L" . ($i + $DELTA))
					->applyFromArray($odd);
			}

			$objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . ($i + $DELTA) . ":L" . ($i + $DELTA))->applyFromArray(
				$wb
			);

			$i++;
		}
	}

	$objPHPExcel->setActiveSheetIndex(0)->getStyle("A" . ($DELTA + 1) . ":L" . ($i + $DELTA))->getAlignment()
		->setWrapText(true);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle("A" . ($DELTA + 1) . ":L" . ($i + $DELTA))->getAlignment()
		->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "Всего врачей: " . ($i - 1));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', "Дата формирования отчета: " . date("Y.m.d"));
}

$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');

$objPHPExcel->getActiveSheet()->setTitle('Report');
$objPHPExcel->setActiveSheetIndex(0)->setSelectedCell("A1");

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', $clinicNameFullName);
$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($Head);

switch ($dataType) {
	case 'full':
	{
		//$objPHPExcel->getActiveSheet()->removeColumn("E", 1);
		//$objPHPExcel->getActiveSheet()->removeColumn("E", 1); // спеццена
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
	}
		break;
	default :
		{
		$objPHPExcel->getActiveSheet()->removeColumn("K", 1);
		$objPHPExcel->getActiveSheet()->removeColumn("J", 1);
		$objPHPExcel->getActiveSheet()->removeColumn("I", 1);
		$objPHPExcel->getActiveSheet()->removeColumn("H", 1);
		$objPHPExcel->getActiveSheet()->removeColumn("E", 1); // спеццена
		$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
		//$objPHPExcel->getActiveSheet()->removeColumn("G", 1);
		}
}

$file = "Actualization_" . $clinicName . "_" . date("d.m.Y") . ".xls";
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

function toList($node, $Element = 'Name', $delimiter = ',')
{
	$str = "";
	if (count($node->$Element) > 0) {
		foreach ($node->$Element as $item) {
			$str .= strval($item) . $delimiter . " ";
		}
	}
	$str = rtrim($str, $delimiter . " ");

	return $str;
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

?>
