<?php

set_time_limit(60);

use \dfs\docdoc\models\ClinicModel;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../php/reportLib.php";
require_once dirname(__FILE__) . "/../../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../../lib/php/translit.php";
require_once dirname(__FILE__) . "/../../lib/php/style4Excel.php";

$clinicIds = isset($_GET["clinicIds"]) ? checkField($_GET["clinicIds"], "t", '') : '';

if (empty($clinicIds)) {
	exit();
}

$dataProvider = new CActiveDataProvider(
	ClinicModel::class,
	[
		'criteria' => [
			'scopes' => [
				'inClinics' => [explode(',', $clinicIds)]
			],
			'with' => ['diagnosticClinics', 'diagnosticClinics.diagnostic']
		]
	]
);
$clinicIterator = new CDataProviderIterator($dataProvider, 100);

if (count($dataProvider->getData()) == 0) {
	exit();
}

$archiveDir = dirname(__FILE__) . "/../../_reports/";
$srcDir = dirname(__FILE__) . "/../../_reports/price/";
if (!file_exists($srcDir)) {
	mkdir($srcDir, DIR_MODE);
}

// Очищаем директорию с отчетами
if ($handle = opendir($srcDir)) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			unlink($srcDir . $file);
		}
	}
	closedir($handle);
}

$fileArr = [];
foreach ($clinicIterator as $item) {

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Docdoc.ru")->setTitle("Price - {$item->rewrite_name}");

	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', "Диагностика")
		->setCellValue('B1', "Стоимость, руб")
		->setCellValue('C1', "Спец.цена, руб");

	$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray($TH);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->applyFromArray($TH);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('C1')->applyFromArray($TH);
	$objPHPExcel->getActiveSheet()->getStyle("A1:C1")->getAlignment()->setWrapText(true);

	$i = 2;
	foreach ($item->diagnosticClinics as $service) {
		if (!is_null($service->diagnostic)) {
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $i, $service->diagnostic->getFullName())
				->setCellValue('B' . $i, $service->price)
				->setCellValue('C' . $i, $service->special_price);
			$i++;
		}
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);

	$clinicName = !empty($item->short_name) ? translit($item->short_name) : translit($item->name);
	$file = "Price_{$clinicName}_" . date('d.m.Y') . ".xls";
	$filename = dirname(__FILE__) . "/../../_reports/price/" . $file;

	array_push($fileArr, $file);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($filename);
	chmod($filename, FILE_MODE);
}

$zip = new ZipArchive();

$fileName = $archiveDir . "prices.zip";
if ($zip->open($fileName, ZIPARCHIVE::CREATE) !== true) {
	fwrite(STDERR, "Error while creating archive file");
	exit(1);
}

foreach ($fileArr as $file) {
	$zip->addFile($srcDir . $file, $file);
}

$zip->close();

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-Type: application/zip");
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=\"prices.zip\"");
header("Content-Transfer-Encoding: binary");
readfile($fileName);

unlink($fileName);
